<?php
/**
 * http://localhost/papoo_trunk/interna/plugin.php?menuid=1084&template=commentplus/templates/commentplus_back.html&mv_commentplus_flex_id=1&mv_content_commentplus_id=1
 * */

/**
 * Class commentplus
 */
#[AllowDynamicProperties]
class commentplus
{
	/**
	 * commentplus constructor.
	 */
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content, $db, $user, $checked, $cms, $diverse, $spamschutz, $db_abs;
		$this->content = $content;
		$this->db = $db;
		$this->user = $user;
		$this->checked = $checked;
		$this->cms = $cms;
		$this->diverse = $diverse;
		$this->spamschutz = $spamschutz;
		$this->db_abs = &$db_abs;

		$this->irz=false;

		if (defined("admin")) {
			$this->user->check_intern();
			global $template;

			if (strpos("XXX" . $template, "commentplus_back.html")) {
				//Einstellungen �berabeiten
				$this->start_daten_verwalten();
			}
		}
	}

	/**
	 * commentplus::output_filter()
	 *
	 * @return void
	 */
	public function output_filter()
	{
		global $output;

		if (strstr( $output,"#forum")) {
			//Ausgabe erstellen
			$output=$this->create_comment_integration($output);
		}

		if (strstr( $output,"#comment")) {
			//Ausgabe erstellen
			$output=$this->get_comment_flex($output);
		}
	}

	/**
	 * commentplus::get_comment_flex()
	 *
	 * @param $inhalt
	 * @return mixed|string|string[]|null
	 */
	private function get_comment_flex($inhalt)
	{

		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#comment(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;

		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_comment_count($ndat['1'],$ndat['2'],$ndat['3']);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}

		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * commentplus::get_comment_count()
	 *
	 * @param integer $forumid
	 * @param integer $flex_id
	 * @param integer $id
	 * @return array|null
	 */
	private function get_comment_count($forumid=0,$flex_id=0,$id=0)
	{
		if (is_numeric($this->checked->glossar)) {
			$id="gl".$this->checked->glossar;
		}

		$sql=sprintf("SELECT COUNT(msgid) FROM %s
									WHERE forumid='%d'
									AND comment_article='%s' AND msg_frei='1' ",
			$this->cms->tbname['papoo_message'],
			$this->db->escape($forumid),
			$this->db->escape($id)
		);
		$count=$this->db->get_var($sql);
		return $count;
	}

	/**
	 * commentplus::create_comment_integration()
	 *
	 * @param string $inhalt
	 * @return mixed|string|string[]|null
	 */
	private function create_comment_integration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#forum(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_comment_entry($ndat['1'],$ndat['2'],$ndat['3']);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}

		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * commentplus::get_flex_entry()
	 *
	 * @param int $forumid
	 * @param integer $flex_id
	 * @param integer $id
	 * @return void|string
	 */
	private function get_comment_entry($forumid=0,$flex_id=0,$id=0)
	{
		//Wenn neu Eintrag erfolgt ist
		if (!empty($this->checked->cneuvorname) && !empty($this->checked->cinhalt) && !empty($this->checked->uebermittelformular)&& !$this->spamschutz->is_spam) {
			//Daten eintragen
			$blacklist = new blacklist();
			$truethema = $blacklist->do_blacklist($this->checked->formthema);
			$trueinhalt = $blacklist->do_blacklist($this->checked->inhalt);

			/**
			 * nur wenn keine Blacklisteintr�ge drin sind, auch eintragen,
			 * ansonsten nichts eintragen, l�uft ins Leere
			 */

			if ($truethema == "ok" and $trueinhalt == "ok") {
				// Wenn userid leer, dann Gast oder jeder eingeben
				if ($this->user->userid == 0 or $this->user->userid == 11) {
					$userid_drin = 11;
					$usernameguest = $this->checked->cneuvorname;
				}
				else {
					$userid_drin = $this->user->userid;
					$usernameguest = $this->user->username;
				}

				$thema=substr($this->checked->cinhalt,0,50);

				//IRZ Sonderfall
				if($this->irz===true) {
					$this->checked->cinhalt="Projekt: ".$this->checked->message_cplus_projekt."\n".
						"Datum: ".$this->checked->message_cplus_datum."\n".
						"Land: ".$this->checked->message_cplus_land."\n".
						"Massnahme: ".$this->checked->message_cplus_ort_massnahme."\n".
						$this->checked->cinhalt;

					// Eintrag f�r die Datenbank erstellen
					$query = sprintf("INSERT INTO %s SET
													comment_article='%d', 
													zeitstempel=NOW(), 
													forumid='%d', 
													parentid='0', 
													rootid='0',
													userid='%d', 
													thema='%s', 
													messagetext='%s', 
													level='0', 
													ordnung='0', 
													username_guest='%s', 
													msg_frei='%d' ,
													message_cplus_projekt='%s',
													message_cplus_datum='%s',
													message_cplus_land='%s',
													message_cplus_ort_massnahme='%s'
													
													",
						$this->cms->papoo_message,
						$this->db->escape($id),
						$this->db->escape($forumid),
						$this->db->escape($userid_drin),
						$this->db->escape($thema),
						$this->db->escape($this->checked->cinhalt),
						$this->db->escape($usernameguest),
						$this->cms->gaestebuch_msg_frei,
						$this->db->escape($this->checked->message_cplus_projekt),
						$this->db->escape($this->checked->message_cplus_datum),
						$this->db->escape($this->checked->message_cplus_land),
						$this->db->escape($this->checked->message_cplus_ort_massnahme)
					);
				}
				else {
					if (is_numeric($this->checked->glossar)) {
						$id="gl".$this->checked->glossar;
					}

					// Eintrag f�r die Datenbank erstellen
					$query = sprintf("INSERT INTO %s SET
													comment_article='%s', 
													zeitstempel=NOW(), 
													forumid='%d', 
													parentid='0', 
													rootid='0',
													userid='%d', 
													thema='%s', 
													messagetext='%s', 
													level='0', 
													ordnung='0', 
													username_guest='%s', 
													msg_frei='%d' 
													",
						$this->cms->papoo_message,
						$this->db->escape($id),
						$this->db->escape($forumid),
						$this->db->escape($userid_drin),
						$this->db->escape($thema),
						$this->db->escape($this->checked->cinhalt),
						$this->db->escape($usernameguest),
						$this->cms->gaestebuch_msg_frei
					);
				}

				// in die Datenbank eintragen
				$this->db->query($query);

				//Anzahl der Nachrichten Updaten
				$sql=sprintf("UPDATE %s SET
											forum_beitr=forum_beitr+1
											WHERE forumid='%d'",
					$this->cms->tbname['papoo_forums'],
					$this->db->escape($forumid)
				);
				$result=$this->db->get_results($sql,ARRAY_A);

				//Wenn Benachrichtigung dann verschicken
				if ($this->cms->benach_neu_kommentar == 1) {
					//Thema bezeichnen
					$this->content->template['message_2278']=str_ireplace('#kommentar#',$thema,$this->content->template['message_2278']);

					$this->diverse->mach_nachricht_neu($this->content->template['message_2278'],$this->checked->cinhalt);
				}
			}
			if (!empty($this->checked->mv_id)) {
				$this->reload_front("&mv_id=".$flex_id."&extern_meta=x&mv_content_id=".$id);
			}
			if (!empty($this->checked->glossar)) {
				$this->reload_front("glossar=".$this->checked->glossar."","glossar");
			}
		}
		else {
			if (!empty($this->checked->uebermittelformular)) {
				// Daten fehlen oder Spamcode falsch
				// Fehler setzen
				$fehler = '<div class="error">Sie haben einen Fehler in Ihren Angaben</div>';
			}
		}

		if (is_numeric($this->checked->glossar)) {
			$id="gl".$this->checked->glossar;
		}
		//Zuerst mal die hier passenden Beitr�ge rausholen
		$sql=sprintf("SELECT * FROM %s
									WHERE forumid='%d'
									AND comment_article='%s'
									AND msg_frei='1' 
									ORDER BY msgid ASC",
			$this->cms->tbname['papoo_message'],
			$this->db->escape($forumid),
			$this->db->escape($id)
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$template = file_get_contents(PAPOO_ABS_PFAD."/plugins/commentplus/templates/commentplus_front_liste.html");

		$neu=$inhalt="";
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				if (is_array($value)) {
					$inhalt=$template;
					foreach ($value as $key2=>$value2) {
						$inhalt=str_replace("#".$key2."#",nl2br(htmlentities(strip_tags($value2))),$inhalt);
					}
					$neu.=$inhalt;
				}
			}
		}

		$spamschutz=$this->get_spamschutz();

		if ($this->irz) {
			$template_form = file_get_contents(PAPOO_ABS_PFAD."/plugins/commentplus/templates/commentplus_front_form_irz.html");
		}
		else {
			$template_form = file_get_contents(PAPOO_ABS_PFAD."/plugins/commentplus/templates/commentplus_front_form.html");
		}

		$template_form=str_replace("#spamschutz#",$spamschutz,$template_form);
		$template_form=str_replace("#fehler#",$fehler,$template_form);

		$template_form=str_replace("#cinhalt#",htmlentities($this->checked->cinhalt),$template_form);
		$template_form=str_replace("#cneuvorname#",htmlentities($this->checked->cneuvorname),$template_form);

		$template_form=str_replace("#message_cplus_projekt#",htmlentities($this->checked->message_cplus_projekt),$template_form);

		$template_form=str_replace("#message_cplus_datum#",htmlentities($this->checked->message_cplus_datum),$template_form);

		$template_form=str_replace("#message_cplus_land#",htmlentities($this->checked->message_cplus_land),$template_form);


		$template_form=str_replace("#message_cplus_ort_massnahme#",htmlentities($this->checked->message_cplus_ort_massnahme),$template_form);

		if (is_array($this->content->template)) {
			foreach ($this->content->template as $key=>$value) {
				$template_form=str_replace("#".$key."#",$value,$template_form);
			}
		}

		$neu=$neu.$template_form;

		return $neu;
	}

	/**
	 * commentplus::get_spamschutz()
	 *
	 * @return void|string
	 */
	private function get_spamschutz()
	{
		if ($this->content->template['spamschutz_modus']==1) {
			$data=$this->content->template['message_2178'];

			$data.='<img src="'.PAPOO_WEB_PFAD.'/images/_spamcode_image.php" width="200" height="50" alt="'.$this->content->template['message_2179'].'" title="'.$this->content->template['message_2179'].'" /><br />
			<label for="spamcode">'.$this->content->template['message_2180'].':</label>
			<input type="text" name="spamcode" id="spamcode" size="10" maxlength="10" value="" /><br />';
		}
		if ($this->content->template['spamschutz_modus']==2) {
			$data='<p>'.$this->content->template['message_2181'].'</p>
			<label for="spamcode">'.$this->content->template['spamschutz_aufgabe'].'</label>
			<input type="text" name="spamcode" id="spamcode" size="4" maxlength="10" value="" /><br />';
		}
		if ($this->content->template['spamschutz_modus']==3) {
			$data="Nicht vorgesehen...";
		}

		return $data;
	}

	/**
	 * commentplus::start_daten_verwalten()
	 *
	 * @return void
	 */
	private function start_daten_verwalten()
	{
		//Foren rausholen

		$sql=sprintf("SELECT * FROM %s
									WHERE forumid > '21'",
			$this->cms->tbname['papoo_forums']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				$result[$key]['platzhalter']='#forum_'.$value['forumid'].'_#MVID#_#ID##';
				$result[$key]['count_platzhalter']='#comment_'.$value['forumid'].'_#MVID#_#ID##';
			}
		}
		$this->content->template['complus_forum']=$result;
	}

	/**
	 * commentplus_plugin_felder_class::reload()
	 *
	 * @param string $dat
	 * @param string $glossar
	 * @return void
	 */
	private function reload_front( $dat = "", $glossar="" )
	{
		if (!empty($glossar)) {
			$url = "menuid=" . $this->checked->menuid."&template=glossar/templates/wortdefinitionen.html&".$dat;
		}
		else {
			$url = "menuid=" . $this->checked->menuid;
			$url .= "&template=mv/templates/mv_show_front.html";
			if (!empty($dat)) {
				$url .=  $dat;
			}
		}
		$self = $_SERVER['PHP_SELF'];

		$location_url = $self . "?" . $url;
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header( "Location: $location_url" );
		}
		exit;
	}

	/**
	 * commentplus_plugin_felder_class::reload()
	 *
	 * @param string $dat
	 * @return void
	 */
	private function reload($dat = "")
	{
		$url = "menuid=" . $this->checked->menuid;


		$url .= "&template=commentplus/templates/commentplus_back.html";

		if ( !empty( $dat ) )
		{
			$url .= "&message_commentplus=" . $dat;
		}

		$self = $_SERVER['PHP_SELF'];


		$location_url = $self . "?" . $url;
		if ( $_SESSION['debug_stopallredirect'] ) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header( "Location: $location_url" );
		}
		exit;
	}
}

$commentplus = new commentplus();
