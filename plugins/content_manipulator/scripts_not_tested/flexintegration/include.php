<?php
/**
 * Skript ist in dieser Form noch nicht getestet...
 * */

/**
 * Class flexcmintegration
 */
class flexcmintegration
{
	/**
	 * flexcmintegration constructor.
	 */
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		//Admin Ausgab erstelle
		$this->set_backend_message();
		//Frontend - dann Skript durchlaufen
		if (!defined("admin")) {
			$i=0;
			//Fertige Seite einbinden
			global $output;
			$this->bind_mv();
			//Wenn nicht MV dann Inhalte checken
			if (!strstr( $this->checked->template,"mv")) {
				//Zuerst check ob es auch vorkommt
				if (strstr( $output,'class="insert_flex"')) {
					// Link rausholen
					$link = $this->get_link_data($inhalt);
					if (!empty($link)) {
						// Link aufexploden
						$check_ar = $this->get_link_content($link);
						// An checked Objekt �bergeben
						if (is_array($check_ar)) {
							foreach ($check_ar as $check) {
								// Wenn auf eigener Seite dann Klasse aufrufen
								if (stristr( $check['seite_url'],$cms->title_send)) {
									// Variablen �bergeben
									$this->give_checked($check);
									// Suche durchf�hren
									$insert = $this->show_search_mv();
								}
								// Externe Seite dann extern aufrufen
								else $insert = $this->get_extern($check); // Inhalte rausholen die eingebunden werden sollen
								// Ergebnis eintragen
								$this->make_text($insert);
							}
						}
						// An Template �bergeben
						$output = "" . $this->inhalt;
					}
					$i++;
				}
			}
		}
	}

	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][]="Skript Flexverwaltung an beliebiger Stelle";
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten ein bestimmtes Banner ausgeben lassen, die Syntax lautet.<br /><strong>#banner_1#</strong><br />Wobei Sie mit der Ziffer am Ende die ID des Bannereintrages bezeichnen. ";
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * content_manipulator::get_data_extern()
	 * Hier werden die Daten per Snoopy ausgelesen
	 *
	 * @param string $url
	 * @param string $seite_url
	 * @return string Daten der externen Seite
	 */
	function get_data_extern($url = "", $seite_url = "")
	{
		$url = html_entity_decode($seite_url);
		$url = str_replace('/', '%2F', $url);
		$url = str_replace('http:%2F%2F', 'http://', $url);
		$url = str_replace('%2Fplugin.php', '/plugin.php', $url);
		require_once (PAPOO_ABS_PFAD . "/lib/classes/extlib/Snoopy.class.inc.php");
		$html = new Snoopy();
		$html->agent = "Web Browser";
		$html->referer = $_SERVER["HTTP_REFERER"];
		$html->fetch($url);
		$daten = $html->results;
		return $daten;
	}

	/**
	 * content_manipulator::content_mani_recode_content()
	 * Inhalte mit Links wird umgewandelt
	 *
	 * @param string $daten
	 * @param string $seite_url
	 * @return string|string[]
	 */
	function content_mani_recode_content($daten = "", $seite_url = "")
	{
		$daten1 = explode('<!-- START-LISTE -->', $daten);
		//<div class="printfooter">
		$daten2 = explode('<!-- STOP-LISTE -->', $daten1['1']);
		$zwischen = $daten2['0'];
		// Externe Links gehen ins Blank
		$zwischen = str_ireplace("href=\"http://", "target=\"blank\" href=\"http://", $zwischen);
		// Bilder korriegieren /images/
		$zwischen = str_ireplace("/images/", $seite_url . "/images/", $zwischen);
		// template Variable verschleiern
		$zwischen = str_ireplace('name="template"', 'name="template_fremd"', $zwischen);
		// Action umbieren
		$zwischen = str_ireplace('action="/plugin.php"', 'action="' . PAPOO_WEB_PFAD . '/plugin.php"', $zwischen);
		$zwischen = str_ireplace('action="plugin.php"', 'action="' . PAPOO_WEB_PFAD . '/plugin.php"', $zwischen);
		$zwischen = str_ireplace('</form>',
			'<input value="flexint/templates/flexintfront.html" name="template" type="hidden"></form>', $zwischen);
		// template Varialen korrigieren
		$zwischen = str_ireplace('template=', 'template=flexint/templates/flexintfront.html&template_fremd=', $zwischen);
		// Links korrigieren
		$zwischen = str_ireplace('href="/plugin.php', 'href="' . PAPOO_WEB_PFAD . '/plugin.php', $zwischen);
		$zwischen = str_ireplace('href="plugin.php', 'href="' . PAPOO_WEB_PFAD . '/plugin.php', $zwischen);
		$zwischen = str_ireplace('menuid=', 'menuid=' . $this->checked->menuid . '&xyz=', $zwischen);
		$zwischen = preg_replace('/<input type="hidden" value="(.*?)" name="menuid" \/>/i',
			'<input value="' . $this->checked->menuid . '" name="menuid" type="hidden">', $zwischen);
		$ausgabe = $zwischen;
		return $ausgabe;
	}

	/**
	 * content_manipulator::get_extern()
	 * Hier wird aus einer externen Seite der Content
	 * ausgelesen und angepasst
	 *
	 * @param array $check
	 * @return string|string[]
	 */
	function get_extern($check = array())
	{
		// Startwert setzen
		$get = "?nix=1";
		// Daten aus der URL im Text durchgehen und �bergeben
		foreach ($check as $key => $value)
		{
			if ($key == "seite_url") continue;
			$get .= "&" . $key . "=" . $value . "";
		}
		// Link erstellen
		$url = trim($check['seite_url']) . $get;
		$url_echt = trim($check['seite_url']);
		$url_echt = str_replace("/plugin.php", "", $url_echt);
		// Daten holen per Snoopy
		#$daten = $this->get_data_extern($url, $url_echt); 
		$daten = $this->get_data_extern($url, $check['seite_url2']);
		// Daten umwandeln
		$daten = $this->content_mani_recode_content($daten, $url_echt);
		// R�ckgabe
		return $daten;
	}

	/**
	 * content_manipulator::make_text()
	 * Inhalte zusammenf�hren
	 *
	 * @param string $insert
	 * @return void
	 */
	function make_text($insert = "")
	{
		$insert = str_ireplace("nobr:", "", $insert);
		$text = $this->inhalt;
		$text_insert = '<div class="ausgabe_flex">' . $insert . '</div>';
		#$text= preg_replace('/<span class="insert_flex">(.*?)<\\/span>/i', $text_insert, $text);
		$text = preg_replace('/<(span|p) class="insert_flex">(.*?)<\\/(span|p)>/i', $text_insert, $text, 1);
		$this->inhalt = $text;
	}

	/**
	 * content_manipulator::give_checked()
	 * Die Inhalte an das checked Objekt �bergeben
	 *
	 * @param string $check
	 * @return void
	 */
	function give_checked($check = "")
	{
		// Eintr�ge �bergeben
		if (is_array($check)) {
			foreach ($check as $key => $value) {
				if ($key == "menuid") {
					$value = $this->checked->menuid;
				}
				$this->checked->$key = $value;
			}
		}
	}

	/**
	 * content_manipulator::get_link_content()
	 * Var Inhalte des Links rausholen
	 *
	 * @param array $link_a
	 * @return void
	 */
	function get_link_content($link_a = array())
	{
		if (is_array($link_a)) {
			$i = 0;
			foreach ($link_a as $link) {
				if (stristr( $link,"mv_search")) {
					#$link1 = explode("\?", $link);
					$link1 = explode("?", $link);
					$link2 = explode("&", $link);
					foreach ($link2 as $dat) {
						$ldat = explode("=", $dat);
						// amps rausholen
						$ldat["0"] = str_ireplace("amp;", "", $ldat["0"]);
						// Inhalt dekodieren
						$ldat["1"] = urldecode($ldat["1"]);
						// �bergeben
						$check[$i][$ldat["0"]] = $ldat["1"];
						$check[$i]['seite_url'] = $link1['0'];
						$check[$i]['seite_url2'] = $link;
					}
					$i++;
				}
			}
		}
		return $check;
	}

	/**
	 * content_manipulator::get_link_data()
	 * Filter den Link aus dem Content raus
	 * @param mixed $inhalt
	 * @return array mit Links
	 */
	function get_link_data($inhalt = array())
	{
		//Inhalte aufexploden
		preg_match_all("|<span [^>]+>(.*)</span>|U", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		preg_match_all("|<div [^>]+>(.*)</div>|U", $inhalt, $ausgabe3, PREG_PATTERN_ORDER);
		preg_match_all("|<p[^>]+>(.*)</p>|U", $inhalt, $ausgabe2, PREG_PATTERN_ORDER);
		$causgabe = array_merge($ausgabe['1'], $ausgabe2['1'], $ausgabe3['1']);
		// R�ckgabe ARRAY mit den Links
		return $causgabe;
	}

	/**
	 * content_manipulator::filter_ausgabe_spalte()
	 * Diese Funktion filter die Ausgabe im 3. Spalte
	 * und ersetzt die Vorgaben mit den Inhalten aus der Flexverwaltung
	 *
	 * @return void
	 */
	function filter_ausgabe_spalte()
	{
		// Ausgabe rausholen
		$ausgabe = $this->content->template['right_data'];
		if (is_array($ausgabe)) {
			foreach ($ausgabe as $data) {
				$inhalt = $data['inhalt_right'];
				//Link rausholen
				$link = $this->get_link_data($inhalt);
				if (!empty($link)) {
					//Link aufexploden
					$check = $this->get_link_content($link);
					// An checked Objekt �bergeben
					$this->give_checked($check);
					// Suche durchf�hren
					$insert = $this->show_search_mv();
					// Ergebnis eintragen
					$text = $this->make_text($insert);
					// An Template �bergeben
					$this->content->template['right_data'][$i]['inhalt_right'] = "nobr:" . $text;
					$i++;
				}
			}
		}
	}

	/**
	 * content_manipulator::bind_mv()
	 * Die mv Klasse einbinden
	 *
	 * @return void
	 */
	function bind_mv()
	{
		global $mv;
		$this->mv = &$mv;
	}

	/**
	 * content_manipulator::show_search_mv()
	 * Hier wird die Suchmaske aus der MV rausgeholt und ausgegeben
	 *
	 * @return string
	 */
	function show_search_mv()
	{
		if (is_object($this->mv)) {
			global $db;
			$this->db = &$db;
			$this->db->hide_errors();
			$this->mv->meta_gruppe = 1;
			$this->mv->get_users_groups();
			#$this->content->template['mv_template_all'] = "";
			$this->mv->nocontent_ok = "ok";
			$search_mv_id = 1;
			require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/search_user_front.php');
			#$this->mv->search_user_front(1);
			$temp = ($this->content->template['mv_template_all']);
			$daten = "";
			if (is_array($temp)) {
				foreach ($temp as $single) {
					$daten .= $single;
				}
			}
		}
		return $daten;
	}
}

$flexcmintegration=new flexcmintegration();
