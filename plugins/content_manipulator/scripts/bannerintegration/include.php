<?php

/**
 * Class bannerintegration
 */
class bannerintegration
{
	/**
	 * bannerintegration constructor.
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
			if (strstr( $output,"#banner")) {
				//Ausgabe erstellen
				$output=$this->create_bannerintegration($output);
			}
		}
	}

	/**
	 * @return void
	 */
	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][]="Skript Banner an beliebiger Stelle";
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten ein bestimmtes Banner ausgeben lassen, die Syntax lautet.<br /><strong>#banner_1#</strong><br />Wobei Sie mit der Ziffer am Ende die ID des Bannereintrages bezeichnen. ";
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_bannerintegration($inhalt = "")
	{
		// Ids rausholen
		preg_match_all("|#banner(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			$ndat = explode("_", $dat);
			$banner_daten = $this->get_banner_aus_plugin($ndat['1']);
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}
		$inhalt = "" . $inhalt;
		return $inhalt;
	}

	/**
	 * @param integer $banner_id
	 *
	 * @return string|null
	 */
	function get_banner_aus_plugin($banner_id = 0)
	{
		// Heutiges Datum
		$datum = date("Y-m-d", time());
		// INI
		$result="";
		// Wenn Tabelle existiert
		if (!empty($this->cms->tbname['papoo_bannerverwaltung_daten'])) {
			$sql = sprintf("SELECT banner_code
			 						FROM %s
									WHERE banner_start <= '%s'
											AND banner_stop >= '%s'
											AND banner_lang = '%s'
											AND banner_id = '%d'",
				$this->cms->tbname['papoo_bannerverwaltung_daten'],
				$datum,
				$datum,
				$this->cms->lang_short,
				$banner_id
			);
			$result = $this->db->get_var($sql);
		}
		return $result;
	}
}

$bannerintegration=new bannerintegration();
