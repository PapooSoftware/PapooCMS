<?php
/**
#####################################
# CMS Papoo Plugin User Download    #
# (c) Philipp Schaefer 2012         #
# Authors: Philipp Schaefer         #
# http://www.papoo.de               #
# UserDownload Plugin               #
#####################################
# PHP Version >4.2                  #
#####################################
 **/

/**
 * Class user_download
 */
class user_download
{
	/**
	 * user_download::user_download()
	 *
	 * @return void
	 */
	function __construct()
	{
		$savepath = '../plugins/user_download/files/';
		$filephp = 'plugins/user_download/lib/file.php';

		global $content, $db, $checked, $diverse, $user, $cms, $weiter, $db_abs, $template;
		$this->content = & $content;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->diverse = &$diverse;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->weiter = &$weiter;
		$this->db_abs = &$db_abs;

		if (defined("admin")) {
			$this->user->check_intern();
			global $template;

			if ($template != "login.utf8.html") {
				if (basename($template)=="user_download_back_removefile.html") {
					if (!empty($this->checked->fileid)) {
						//Wenn eine Dateinummer gegeben ist, l�se als erstes alle Benutzerbeziehungen
						$sql = sprintf("DELETE FROM %s WHERE fileid = %d",$this->cms->tbname['plugin_user_download_lookup_uf'],$this->checked->fileid);
						$this->db->query($sql);

						//Hole dir den Path der zu l�schenden Datei
						$sql = sprintf("SELECT path FROM %s WHERE id = %d",$this->cms->tbname['plugin_user_download_files'],$this->checked->fileid);
						$path = $this->db->get_results($sql,"ARRAY_A");
						$path = $path[0]['path'];
						//und entferne diese
						unlink($path);

						//jetzt entferne den Eintrag in der plugin_user_download_lookup_uf Tabelle Tabelle
						$sql = sprintf("DELETE FROM %s WHERE id = %d",$this->cms->tbname['plugin_user_download_files'],$this->checked->fileid);
						$this->db->query($sql);
					}
					//Leite mich um auf die Startseite.
					$template = dirname($template)."/user_download_back.html" ;
				}
				if (basename($template)=="user_download_back_upload.html") {
					if(empty($this->checked->uid)) {
						$template = dirname($template)."/user_download_back.html"; //wenn keine UserId gegeben ist-> auf Liste weiterleiten
					}
					if (!empty($this->checked->formSubmit) && count($_FILES['up']['name'])!=0) { //Wenn das Form abgesendet wurde und die Anzahl der Dateien gr��er 0 ist
						$success=1;
						$error='';
						$files = array();
						try {
							$uid = $this->checked->uid; //Hole die �bergebene userid;
							foreach ($_FILES['up']['name'] as $key=>$file) { //alle Dateinamen durchlaufen
								if($_FILES['up']['size'][$key]==0) { //Wenn die Datei eine Gr��e von 0 Bytes besitzt, dann ist sie leer und muss nicht hochgeladen werden.
									continue;
								}

								if (!is_dir($savepath.'/'.$uid)) { //Erstelle den Ordner, wenn er noch nicht existiert.
									mkdir($savepath.'/'.$uid);
								}

								move_uploaded_file($_FILES['up']['tmp_name'][$key],$savepath.'/'.$uid.'/'.$file); //Verschiebe die aktuelle Datei an die richtige Stelle.

								$sql = sprintf("SELECT id FROM %s WHERE filename = '%s'",$this->cms->tbname['plugin_user_download_files'],$file);
								$fiid = $this->db->get_results($sql,"ARRAY_A");
								IfNotSetNull($fiid[0]['id']);
								$fiid = $fiid[0]['id'];
								$sql = sprintf("DELETE FROM %s WHERE fileid=%d",$this->cms->tbname['plugin_user_download_lookup_uf'],$fiid);
								$this->db->query($sql);
								//Entferne Dateien, mit dem gleich Pfad wie die hochgeladene
								$sql = sprintf("DELETE FROM %s WHERE path='%s'",$this->cms->tbname['plugin_user_download_files'],addslashes(realpath($savepath.'/'.$uid.'/'.$file)));
								$this->db->query($sql);

								//F�ge einen neuen Datensatz �ber die Datei ein
								$sql = sprintf("INSERT INTO %s (filename,path,size,uploaded_by) VALUES ('%s','%s',%d,%d)",
									$this->cms->tbname['plugin_user_download_files'],$file,addslashes(realpath($savepath.'/'.$uid.'/'.$file)),$_FILES['up']['size'][$key],$this->user->userid);
								$this->db->query($sql);

								//Verbinde Datei und Benutzer in der plugin_user_download_lookup_uf Tabelle.
								$fileid =  $this->db->insert_id; //Hole die letzte ID die eingef�gt wurde (Session sensitiv, damit voll Mehrfachbenutzerf�hig)
								$sql = sprintf("INSERT INTO %s (fileid,userid) VALUES (%d,%d)",$this->cms->tbname['plugin_user_download_lookup_uf'],$fileid,$this->checked->uid);
								$this->db->query($sql);

								$files[] = $file;
							}
						}
						catch (Exeption $e) {
							$success=0;
							$error = $e;
						}
						if ($success) {
							$this->content->template['plugin']['user_download']['text_01'] = $this->content->template['plugin_user_download__upload_sucess'].'<br />';
							foreach ($files as $file) {
								$this->content->template['plugin']['user_download']['text_01'] .= $file.'<br />';
							}

							global $mail_it;
							$sql = sprintf("SELECT email,user_vorname,user_nachname FROM %s WHERE userid = %d;",$this->cms->tbname['papoo_user'],$uid);
							$result = $this->db->get_results($sql,"ARRAY_A");
							$mail_it->from = $this->content->template['plugin_user_download__email_from'];
							$mail_it->to = $result[0]['email'];

							$mail_it->subject = $this->content->template['plugin_user_download__email_subject'];
							$tmp = str_replace('##VORNAME##',$result[0]['user_vorname'],$this->content->template['plugin_user_download__email_body']);
							$tmp = str_replace('##NACHNAME##',$result[0]['user_nachname'],$tmp);
							$mail_it->body = $tmp;
							$temp_ergebnis = $mail_it->do_mail();
						}
						else {
							$this->content->template['plugin']['user_download']['text_01'] = $error;
						}
					}
				}
				if (basename($template)=="user_download_back.html") {
					//Userliste generieren
					if (empty($this->checked->suchstring)) {
						$sql = sprintf("SELECT userid,username,email,user_vorname,user_nachname FROM %s WHERE userid <> 11;",$this->cms->tbname['papoo_user']);
					}
					else {
						$suchstring = $this->checked->suchstring;
						if (strpos($this->checked->suchstring,' ')) {
							//vor und nachname eingeben
							list($vorname,$nachname) = explode(' ',$suchstring,2);
							$sql = sprintf("SELECT userid,username,email,user_vorname,user_nachname FROM %s WHERE userid <> 11 AND user_vorname LIKE '%%%s%%' AND user_nachname LIKE '%%%s%%';",
								$this->cms->tbname['papoo_user'],$vorname,$nachname);
						}
						else {
							$sql = sprintf("SELECT userid,username,email,user_vorname,user_nachname FROM %s WHERE userid <> 11 AND ( user_vorname LIKE '%%%s%%' OR user_nachname LIKE '%%%s%%' OR username LIKE '%%%s%%');",
								$this->cms->tbname['papoo_user'],$suchstring,$suchstring,$suchstring);
						}
					}
					$result = $this->db->get_results($sql,"ARRAY_A");
					foreach ($result as $row) {
						//Hole alle Dateien, welche dem Benutzer in der plugin_user_download_lookup_uf Tabelle zugeordnet sind
						$sql2 = sprintf("SELECT id,filename FROM %s JOIN %s ON fileid = id WHERE userid=%d",
							$this->cms->tbname['plugin_user_download_lookup_uf'],$this->cms->tbname['plugin_user_download_files'],$row['userid']);
						$result2 = $this->db->get_results($sql2,"ARRAY_A");

						//Ausgabe
						IfNotSetNull($this->content->template['plugin']['user_download']['user_liste']);
						$this->content->template['plugin']['user_download']['user_liste'] .=
							sprintf('<tr><td><a href="plugin.php?menuid=%s&template=%s&uid=%s">%s</a></td><td>%s</td><td>%s</td><td>%s</td><td>',
								$this->checked->menuid+1,
								"user_download/templates/user_download_back_upload.html",
								$row['userid'],
								$row['username'],
								$row['user_vorname'],
								$row['user_nachname']
								,$row['email']
							);
						if (count($result2)) {
							foreach ($result2 as $row2) {
								$this->content->template['plugin']['user_download']['user_liste'] .=
									$row2['filename'].sprintf(' <a href="plugin.php?menuid=%s&template=%s&fileid=%s">[X]</a>',
										$this->checked->menuid,"user_download/templates/user_download_back_removefile.html",$row2['id']
									).'<br />';
							}
						}
						$this->content->template['plugin']['user_download']['user_liste'] .= '</td></tr>' ;
					}
				}
			}
		}

		if (basename($template)=="user_download_front.html" && $this->user->userid!=11) {
			$sql = sprintf("SELECT id,filename,size,time FROM %s JOIN %s ON fileid = id WHERE userid=%d",
				$this->cms->tbname['plugin_user_download_lookup_uf'],$this->cms->tbname['plugin_user_download_files'],$this->user->userid);
			$result = $this->db->get_results($sql,"ARRAY_A");
			if (count($result)>0) {
				IfNotSetNull($this->content->template['plugin']['user_download']['file_liste']);
				$this->content->template['plugin']['user_download']['file_liste'] .=
					sprintf('<table class="user_download_table_frontend"><tr><th>%s</th><th>%s</th><th>%s</th><th>%s</th></tr>',
						$this->content->template['plugin_user_download__filename'],
						$this->content->template['plugin_user_download__filesize'],
						$this->content->template['plugin_user_download__date'],
						$this->content->template['plugin_user_download__downloadlink']
					);
				foreach ($result as $row) {
					$link = sprintf('<a href="%s?fid=%d">%s</a>',$filephp,$row['id'],$this->content->template['plugin_user_download__link']);
					$size_formatted = $this->formatbytes($row['size'],"MB");
					if ($size_formatted<1) {
						$size_formatted = $this->formatbytes($row['size'],"KB");
					}
					$this->content->template['plugin']['user_download']['file_liste'] .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',$row['filename'],$size_formatted,$row['time'],$link);
				}
			}
			else {
				$this->content->template['plugin']['user_download']['file_liste'] =
					isset($this->content->template['plugin_user_download__no_files_available']) ? $this->content->template['plugin_user_download__no_files_available'] : "";
			}
		}
		else {
			$this->content->template['plugin']['user_download']['file_liste'] =
				isset($this->content->template['plugin_user_download__please_login']) ? $this->content->template['plugin_user_download__please_login'] : "";
		}
	}

	/**
	 * user_download::formatbytes()
	 *
	 * @param mixed $file
	 * @param mixed $type
	 * @return string
	 */
	function formatbytes($file, $type)
	{
		switch($type){
		case "KB":
			$filesize = $file * .0009765625; // bytes to KB
			break;
		case "MB":
			$filesize = ($file * .0009765625) * .0009765625; // bytes to MB
			break;
		case "GB":
			$filesize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625; // bytes to GB
			break;
		}
		if($filesize <= 0) {
			return $filesize = 'unknown file size';}
		else {
			return round($filesize, 2).' '.$type;
		}
	}
}

$user_download_class = new user_download();
