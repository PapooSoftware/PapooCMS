<?php

/**
 * Hier handelt es sich um eine Beispiel Klasse
 * Die mu� man nicht so benutzen.
 * Im Prinzip kann man hier reinschreiben was man m�chte
 *
 * Class cm_divers
 */
class cm_divers
{
	/**
	 * cm_divers constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		//Admin Ausgab erstellen
		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		//Frontend - dann Skript durchlaufen
		if (!defined("admin") || ($_GET['is_lp']==1)) {
			//Fertige Seite einbinden
			global $output;
			//Zuerst check ob es auch vorkommt
			if (strstr( $output,"#aktu_date") || strstr($output,"#aktu_menuname#")) {
				//Ausgabe erstellen
				$output=$this->create_dateintegration($output);
			}

			if (strstr( $output,"#rest") ) {
				//Ausgabe erstellen
				$output=$this->create_restintegration($output);
			}

			if (strstr( $output,"#menu") ) {
				//Ausgabe erstellen
				$output=$this->create_menuintegration($output);
			}
		}
	}

	/**
	 * Hiermit setzt man die Eintr�ge in der Administrations�bersicht
	 *
	 * @return void
	 */
	function set_backend_message()
	{
		//Zuerst die �berschrift - de = Deutsch; en = Englisch
		$this->content->template['plugin_cm_head']['de'][]="Skript diverse Daten";

		//Dann den Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten diverse Daten ausgeben lassen, die Syntax lautet.
<br /><strong>#aktu_date# => Aktuelles Datum: z.B. Januar 2012</strong>
<strong>#aktu_menuname# => Aktueller Titel des Menüs</strong>
<strong>#menuid# => Aktuelle ID des Menüs</strong>
<strong>#rest_x# => Anzahl der ausgefüllten Formulare eines bestimmten Formulars (x = Formular-ID)</strong><br /> ";

		//Dann ein Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_dateintegration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#aktu_date(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_date();

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}


		preg_match_all("|#aktu_menuname#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['0'] as $dat) {
			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_manuname();

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}
		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_restintegration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#rest(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			IfNotSetNull($ndat['1']);
			IfNotSetNull($ndat['2']);
			$banner_daten = $this->get_rest($ndat['1'],$ndat['2']);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}
		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_menuintegration($inhalt = "")
	{
		if (!is_numeric($this->checked->menuid)) {
			$this->checked->menuid=1;
		}
		$inhalt = str_ireplace('#menuid#', $this->checked->menuid, $inhalt);
		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param $id
	 * @param $anzahl
	 *
	 * @return int|null
	 */
	private function get_rest($id, $anzahl)
	{
		//papoo_form_manager_leads
		$sql=sprintf("SELECT SUM(form_manager_content_lead_feld_content) FROM %s LEFT JOIN %s 
                        ON  form_manager_content_lead_id_id=form_manager_lead_id
                        WHERE form_manager_form_id='%d'
                        AND form_manager_content_lead_feld_name='teilnehmer' 
                        AND form_manager_lead_id>203",
			DB_PRAEFIX."papoo_form_manager_leads",
			DB_PRAEFIX."papoo_form_manager_lead_content",
			$id
		);
		$count=$this->db->get_var($sql);

		$rest=$anzahl-$count;
		if ($rest<1) {
			$rest=0;
		}
		return $rest;
	}

	/**
	 * @return string|void
	 */
	private function get_manuname()
	{
		global $menu, $checked, $content;

		if (!empty($this->checked->reporeid) || $checked->menuid < 2 || $content->template['article'] == 1) {
			return "";
		}
		if (is_array($menu->data_front_complete)) {
			foreach ($menu->data_front_complete as $key=>$value) {
				if ($checked->menuid==$value['menuid']) {
					return '<h1 class="menuheadline">'.$value['lang_title'].'</h1>';
				}
			}
		}
	}

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	function get_date($id = 0)
	{
		$monat=$this->make_monat(date("F"));
		//Beispiel zur�ckgeben.
		return $monat." ".date("Y");
	}

	/**
	 * Monatsnamen korrekt darstellen
	 *
	 * @param $mon
	 * l
	 * @return string
	 */
	function make_monat($mon)
	{
		if ($this->cms->lang_id == 2) {
			return $mon;
		}
		else {
			switch($mon) {
			case "January":
				$mon = "Januar";
				break;
			case "February":
				$mon = "Februar";
				break;
			case "March":
				$mon = "M&auml;rz";
				break;
			case "April":
				$mon = "April";
				break;
			case "May":
				$mon = "Mai";
				break;
			case "June":
				$mon = "Juni";
				break;
			case "July":
				$mon = "Juli";
				break;
			case "August":
				$mon = "August";
				break;
			case "September":
				$mon = "September";
				break;
			case "October":
				$mon = "Oktober";
				break;
			case "November":
				$mon = "November";
				break;
			case "December":
				$mon = "Dezember";
				break;
			default:
				break;
			}
			return $mon;
		}
	}
}

$cm_divers=new cm_divers();
