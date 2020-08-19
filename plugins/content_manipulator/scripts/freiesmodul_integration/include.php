<?php

/**
 * Class frmodul
 */
class frmodul
{
	/**
	 * frmodul constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		//Admin Ausgab erstelle
		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		//Frontend - dann Skript durchlaufen
		if (!defined("admin") || ($_GET['is_lp']==1)) {
			//Fertige Seite einbinden
			global $output;
			//Zuerst check ob es auch vorkommt
			if (strstr( $output,"#freies_modul")) {
				//Ausgabe erstellen
				$output=$this->create_frmodul($output);

				// Formular Platzhalter parsen
				if (class_exists("formularintegration")) {
					new formularintegration();
				}
			}
		}
	}

	/**
	 * frmodul::set_backend_message()
	 *
	 * @return void
	 */
	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][]="Skript Freies Modul an beliebiger Stelle";
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten ein bestimmtes freies Modul ausgeben lassen, die Syntax lautet.<br /><strong>#freies_modul_1#</strong><br />Wobei Sie mit der Ziffer am Ende die ID des Freien Moduleintrages bezeichnen. ";
		$this->content->template['plugin_cm_img']['de'][] = '' ;
	}

	/**
	 * frmodul::create_frmodul()
	 *
	 * @param string $inhalt
	 * @return mixed|string|string[]|null
	 */
	function create_frmodul($inhalt = "")
	{
		// Ids rausholen
		preg_match_all("|#freies_modul(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			$ndat = explode("_", $dat);
			$banner_daten = $this->get_banner_aus_plugin($ndat['1']);
			$replace = '<!-- fm -->' . $banner_daten . '<!-- fm -->'; // Um die einzelnen freien Module später noch zu finden und evtl. zu manipulieren.
			$inhalt = str_ireplace($ausgabe['0'][$i], $replace, $inhalt);
			$i++;
		}
		$inhalt = "" . $inhalt;
		return $inhalt;
	}

	/**
	 * frmodul::get_banner_aus_plugin()
	 *
	 * @param integer $banner_id
	 * @return string|null
	 */
	function get_banner_aus_plugin($banner_id = 0)
	{
		// Heutiges Datum
		$datum = date("Y-m-d", time());
		// INI
		$result="";
		// Wenn Tabelle existiert
		if (!empty($this->cms->tbname['papoo_freiemodule_daten'])) {
			/* Überprüfung hier nur auf Sprache */
			$sql = sprintf("SELECT freiemodule_code
						FROM %s
						WHERE freiemodule_lang = '%s'
						AND freiemodule_id = '%d'",
				$this->cms->tbname['papoo_freiemodule_daten'],
				$this->cms->lang_short,
				$banner_id
			);
			$result = $this->db->get_var($sql);
		}
		return $result;
	}
}

$frmodul=new frmodul();
