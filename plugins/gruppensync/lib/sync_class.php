<?php

/**
 * sync_class
 * Mit dieser Klasse werden die Gruppenrechte
 * einer externen Installation mit
 * dieser Installation verkn�pft
 *
 * Gleichzeitig werden hier die Funktionen zur Verf�gung gestellt
 * die dieses Plugin als Kommunikationsstelle braucht
 *
 * Auf beiden Seiten mu� dieses Plugin also installiert sein
 *
 * Hier werden dann die Seiten auch freigegeben die hier anfragen d�rfen.
 *
 *
 * @package Papoo
 * @author Papoo Software
 * @copyright 2009
 * @version $Id$
 * @access public
 */
#[AllowDynamicProperties]
class sync_class
{
	/**
	 * sync_class constructor.
	 */
	function __construct()
	{
		global $content, $db_abs, $db, $checked, $cms, $user;
		$this->content = &$content;
		$this->db_abs = &$db_abs;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->user = &$user;

		//Admin ausf�hren
		$this->do_admin();
	}

	function do_admin()
	{
		if ( defined("admin") ) {
			$this->user->check_intern();
			global $template;
			global $pfadhier;
			$template2 = str_replace( $pfadhier . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ( $template != "login.utf8.html" ) {
				$this->cms->lang_back_content_id = $this->cms->lang_back_content_id;
				$template2 = ( "gruppensync/templates/" . $template2 );
				switch ( $template2 ) {
					// Einen Typ erstellen
				case "gruppensync/templates/sync.html":
					$this->sync_einstellungen_bearbeiten();
					break;
				case "gruppensync/templates/sync_grup.html":
					$this->sync_gruppen_bearbeiten();
					break;
				default:
					break;
				}
			}
		}
	}

	/**
	 * sync_class::sync_gruppen_bearbeiten()
	 *
	 * @return void
	 */
	function sync_gruppen_bearbeiten()
	{
		$this->setze_standard_werte();
		//Wenn speichern
		if (!empty($this->checked->submit_sync_grup)) {
			//Alte Daten l�schen
			$xsql['dbname'] = "papoo_gruppen_sync_lookup";
			$xsql['del_where_wert'] = " gruppen_sync_lookup_lokale_gruppe='".$this->db->escape($this->checked->gruppen_sync_lookup_lokale_gruppe)."' ";

			//Delete ausf�hren
			$this->db_abs->delete( $xsql);

			$externe=$this->checked->gruppen_sync_lookup_externe_gruppe;
			//Neue durchloopen und speichern
			if (!empty($externe)) {
				foreach ($externe as $key=>$value) {
					//Daten beladen
					$xsql['dbname'] = "papoo_gruppen_sync_lookup";
					$xsql['praefix'] = "gruppen_sync_lookup";

					$this->checked->gruppen_sync_lookup_externe_gruppe=$key;

					//Update ausf�hren
					$this->db_abs->insert( $xsql);

					$this->content->template['is_eingetragen'] = "ok";
				}
			}
		}

		$sql=sprintf("SELECT gruppenname,gruppeid FROM %s WHERE gruppeid='%d'",
			$this->cms->tbname['papoo_gruppe'],
			$this->db->escape($this->checked->sync_gruppe_id)
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		//Daten ans Template �bergeben
		$this->content->template['snyc_one_gruppe']=$result;

		//Daten aus der externen Seite holen
		$externe_liste=$this->sync_get_gruppen_von_extern();

		//Checken welche Gruppen angehakt sind
		$externe_liste = $this->sync_checke_welche_gruppen_sind_zugewisen($externe_liste);
		$externe_liste = empty($externe_liste) ? array() : $externe_liste;#var_dump($externe_liste);
		$this->content->template['externe_liste'] = $externe_liste;
	}

	/**
	 * sync_class::sync_checke_welche_gruppen_sind_zugewisen()
	 * HIer wird gechecked welche Gruppen aus den externen Gruppen
	 * lokal schon zugewiesein sind.
	 *
	 * @param mixed $liste
	 * @param string $gruppeid_lokal
	 * @return array|mixed
	 */
	function sync_checke_welche_gruppen_sind_zugewisen($liste=array(),$gruppeid_lokal="")
	{
		//INI
		$i=0;

		if (empty($gruppeid_lokal)) {
			$gruppeid_lokal=$this->checked->sync_gruppe_id;
		}

		//LIste der zugewiesenen rausholen
		$sql=sprintf("SELECT * FROM %s WHERE gruppen_sync_lookup_lokale_gruppe='%d'",
			$this->cms->tbname['papoo_gruppen_sync_lookup'],
			$this->db->escape($gruppeid_lokal)
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		//Eintr�ge durchgehen
		if (!empty($liste)) {
			foreach ($liste as $extern) {
				if (is_array($result)) {
					foreach ($result as $lokal) {
						//WEnn vorhanden
						if ($extern['gruppeid']==$lokal['gruppen_sync_lookup_externe_gruppe']) {
							$liste[$i]['checked']='checked="checked"';
						}
					}
				}
				$i++;
			}
		}
		return $liste;
	}

	/**
	 * @return mixed
	 */
	function sync_get_gruppen_von_extern()
	{
		//Url rausholen
		//Daten rausholen
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_gruppen_sync_daten']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//Die Url zuweisen
		$url=$result['0']['gruppen_sync_lang_ier_bitte_die_rl_eintragen_wo_die_aten'];

		$daten = serialize([]);
		if (function_exists('curl_init')) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_MAXREDIRS, 3);

			$daten = curl_exec($curl) ?: $daten;
			curl_close($curl);
		}

		$result_extern=unserialize($daten);
		return $result_extern;
	}

	/**
	 * sync_class::setze_standard_werte()
	 *
	 * @return void
	 */
	function setze_standard_werte()
	{
		//INI
		$url="";

		foreach ($_GET as $key => $value) {
			//sicherstellen dass es sich nur um korrekte Zeichen handelt
			$key = preg_replace("/[^a-zA-Z0-9_]_/", "", $key);
			if ($key=="page")continue;
			if ($key=="template")continue;
			$value = preg_replace("/[^a-zA-Z0-9_]_/", "", $value);
			$url.=$key."=".$value."&";
		}
		$location_url = $_SERVER['PHP_SELF'] . "?" . $url;
		//Nochmal DAten ausgeben
		$this->content->template['sync_self'] = $location_url;
	}

	/**
	 * sync_class::sync_einstellungen_bearbeiten()
	 * Hier werden die Einstellungen bearbeitet
	 * Eine Switch brauchts hier nicht da nur Updates erfolgen
	 *
	 * @return void
	 */
	function sync_einstellungen_bearbeiten()
	{
		$this->setze_standard_werte();

		//Wenn Update
		if (!empty($this->checked->submit_sync_back)) {
			//Daten beladen

			$xsql['dbname'] = "papoo_gruppen_sync_daten";
			$xsql['praefix'] = "gruppen_sync_lang";
			$xsql['must'] = array( "gruppen_sync_lang_hlen_ie_die_erwendung_aus");
			$xsql['where_name'] = "gruppen_sync_lang_id";
			$this->checked->gruppen_sync_lang_id=1;

			//Update ausf�hren
			$this->db_abs->update( $xsql);

			$this->content->template['is_eingetragen'] = "ok";
		}

		//Daten rausholen
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_gruppen_sync_daten']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//Daten ans Template �bergeben
		$this->content->template['gruppen_sync_lang']=$result;

		//Gruppenliste rausholen
		$this->sync_get_gruppenlist();

		//Url ausgeben
		//http://localhost/papoo_trunk/plugin.php?menuid=1&template=gruppensync/templates/sync_front.html
		$syncvar=sha1($this->cms->title_send);
		$this->content->template['gruppen_sync_server_url'] =
			"http://".$this->cms->title_send.PAPOO_WEB_PFAD."/plugin.php?menuid=1&template=gruppensync/templates/sync_front.html&syncvar=".$syncvar;
	}

	function sync_get_gruppenlist()
	{
		//INI
		$i=0;
		$j=0;

		//Liste der Gruppendaten
		$sql=sprintf("SELECT gruppenname,gruppeid FROM %s",
			$this->cms->tbname['papoo_gruppe']);
		$result=$this->db->get_results($sql,ARRAY_A);

		//Daten aus der externen Seite holen
		$externe_liste=$this->sync_get_gruppen_von_extern();

		//LIste der zugewiesenen rausholen
		$sql=sprintf("SELECT * FROM %s ",
			$this->cms->tbname['papoo_gruppen_sync_lookup']
		);
		$result2=$this->db->get_results($sql,ARRAY_A);
		//Liste der zugewiesenen durchgehen und die Gruppennamen �bergeben
		if (is_array($externe_liste)
			AND is_array($result2))
		{
			foreach($result2 as $dat) {
				foreach ($externe_liste as $extern) {
					if ($dat['gruppen_sync_lookup_externe_gruppe']==$extern['gruppeid']) {
						$result2[$i]['gruppen_sync_lookup_externe_gruppe']=$extern['gruppenname'];
					}
				}
				$i++;
			}
		}

		foreach($result as $dat) {
			if (is_array($result2)) {
				foreach($result2 as $dat2) {
					if ($dat2['gruppen_sync_lookup_lokale_gruppe']==$dat['gruppeid']) {
						$result[$j]['externe_gruppe'][]=$dat2['gruppen_sync_lookup_externe_gruppe'];
					}
				}
			}
			$j++;
		}

		//Ans Template zuweisen
		$this->content->template['gruppen_liste']=$result;
	}

	/**
	 * sync_class::post_papoo()
	 * Externer Aufruf
	 *
	 * @return void
	 */
	function post_papoo()
	{
		if (isset($this->checked->template) && $this->checked->template=="gruppensync/templates/sync_front.html") {
			$syncvar=sha1($this->cms->title_send);
			if ($this->checked->syncvar==$syncvar) {
				if (empty($this->checked->username) || empty($this->checked->password)) {
					//Liste der Gruppen rausholen
					$sql=sprintf("SELECT gruppenname,gruppeid FROM %s",
						$this->cms->tbname['papoo_gruppe']);
					$result=$this->db->get_results($sql,ARRAY_A);
					print_r(serialize($result));
				}
				else {
					$this->user->check_user();
					$this->user->userid;

					//Die USerdaten raus geben
					$sql=sprintf("SELECT userid,username,email,password 
												FROM %s WHERE userid='%d'",
						$this->cms->tbname['papoo_user'],
						$this->user->userid);
					$result=$this->db->get_results($sql,ARRAY_A);
					$seri_1=serialize($result);
					//Die Gruppendaten rausgeben
					$sql=sprintf("SELECT * 
												FROM %s WHERE userid='%d'",
						$this->cms->tbname['papoo_lookup_ug'],
						$this->user->userid);
					$result=$this->db->get_results($sql,ARRAY_A);
					$seri_2=serialize($result);
					//Verbinden
					$ausgabe=$seri_2."#####".$seri_1;
					echo $ausgabe;
				}
				exit();
			}
		}
	}
}

$sync = new sync_class();
