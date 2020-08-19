<?php

/**
 * Einträge aus der 3. Spalte integrieren
 *
 * Class spalte3
 */
class spalte3
{
	/**
	 * spalte3 constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db, $db_abs;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;
		$this->db_abs = &$db_abs;

		//Admin Ausgab erstellen
		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		//Frontend - dann Skript durchlaufen
		if (!defined("admin") || ($_GET['is_lp']==1)) {
			//Fertige Seite einbinden
			global $output;
			//Zuerst check ob es auch vorkommt
			if (strstr( $output,"#spalte3")) {
				//Ausgabe erstellen
				$output=$this->create_spalte3_integration($output);
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
		$this->content->template['plugin_cm_head']['de'][]="Skript Einträge aus der 3. Spalte an beliebiger Stelle";

		//Dann den Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten Einträge aus der 3. Spalte ausgeben lassen, die Syntax lautet.<br /><strong>#spalte3_1#</strong><br />Wobei Sie mit der Ziffer am Ende die ID des Eintrages bezeichnen. ";

		//Dann ein Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_spalte3_integration($inhalt = "")
	{

		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#spalte3(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;

		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_spalte3($ndat['1']);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}
		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	function get_spalte3($id = 0)
	{
		$lang_id=$this->cms->lang_id;

		//Eintrag rausholen
		$xsql = array();
		$xsql['dbname'] = "papoo_language_collum3";
		$xsql['select_felder'] = array("*");
		$xsql['where_data'] = array(
			"collum_id" => $id,
			"lang_id" => $lang_id
		);

		$result = $this->db_abs->select($xsql);

		return $result['0']['article'];
	}
}

$spalte3=new spalte3();
