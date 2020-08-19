<?php

/**
 * Hier handelt es sich um eine Beispiel Klasse
 * Die mu� man nicht so benutzen.
 * Im Prinzip kann man hier reinschreiben was man m�chte
 * */
class beispiel
{

	/**
	 * beispiel constructor.
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
			if (strstr( $output,"#beispiel")) {
				//Ausgabe erstellen
				$output=$this->create_beispielintegration($output);
			}
		}
	}

	/**
	 * Hiermit setzt man die Eintr�ge in der Administrations�bersicht
	 * @return void
	 */
	function set_backend_message()
	{
		//Zuerst die �berschrift - de = Deutsch; en = Englisch
		$this->content->template['plugin_cm_head']['de'][]="Skript Beispiel an beliebiger Stelle";

		//Dann den Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten ein bestimmtes Beispiel ausgeben lassen, die Syntax lautet.<br /><strong>#beispiel_1#</strong><br />Wobei Sie mit der Ziffer am Ende die ID des Beispiels bezeichnen. ";

		//Dann ein Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 * @return mixed|string|string[]|null
	 */
	function create_beispielintegration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#beispiel(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_beispiel($ndat['1']);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}

		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param int $id
	 * @return string
	 */
	function get_beispiel($id = 0)
	{
		//Beispiel zur�ckgeben.
		return "Dies ist der Beispieltext Nr: ".$id;
	}
}

$beispiel=new beispiel();
