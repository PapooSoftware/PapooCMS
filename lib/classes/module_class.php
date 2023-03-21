<?php
/**
######################################
# papoo Version 3.0                  #
# (c) Carsten Euwens 2006            #
# Author: Stephan Bergmann           #
# http://www.papoo.de                #
######################################
# PHP Version 4.3                    #
######################################
 */

/**
 * Diese Klasse verwaltet die einzelnen Module des CMS Papoo
 *
 * Class module_class
 */
#[AllowDynamicProperties]
class module_class
{
	/** @var string Präfix */
	var $db_praefix;
	/** @var array nformationen aller Module */
	var $module_liste;
	/** @var array Liste installierter Module des Styles (Sortiert nach Bereiche und Order-ID) */
	var $style_module_liste;

	/** @var array Liste "Namen_des_Moduls = <true>" aller aktiven Module.
	 *  Name_des_Moduls entspricht dem Modul-Datei-Namen ohne ".html", also z.B. mod_breadcrump
	 */
	var $module_aktiv;
	/** @var array Liste mit Modul-IDs aller aktiven Module. */
	var $module_aktiv_id;
	/** @var int Anzahl der verschiedenen Bereiche (z.Z. 5 Stück) */
	var $bereiche_anzahl;

	/** @var array mehrdimensionales Array mit den Informationen der einzeln Module unterteilt in die einzelnen Bereiche.
	 *  Die Nummerierung der Bereiche beginnt mit 1 !!!
	 */
	var $bereiche_module;

	/** @var array Liste aller nicht-aktiven Module für die Zuweisung zu einem Bereich.
	 *  folgende Unterscheidung nach variablen oder fixen Modulen wurde 07/2009 gestrichen
	 *  variable Module sind in ALLEN Bereichen verwendbar, erhalten also auch einen Eintrag in ALLEN Bereichen.
	 *  fixe Module sind nur in EINEM bestimmten Bereich einsetzbar und erhalten deshalb auch nur EINEN Eintrag im entsprechenden Bereich
	 */
	var $module_inaktiv;

	/**
	 * module_class constructor.
	 */
	function __construct()
	{
		// Papoo-Klassen globalisieren
		global $db;
		$this->db = & $db;

		global $checked;
		$this->checked = & $checked;

		global $content;
		$this->content = & $content;

		global $user;
		$this->user = & $user;

		global $cms;
		$this->cms = & $cms;

		global $diverse;
		$this->diverse = & $diverse;

		// Klassen-interne Variablen initialisieren
		global $db_praefix;
		$this->db_praefix = $db_praefix;

		$this->module_liste = array();
		$this->style_module_liste = array();
		$this->module_aktiv = array();
		$this->module_aktiv_id = array();

		$this->bereiche_anzahl = 5;
		$this->bereiche_module = array();
		$this->module_inaktiv = array();


		// Klassen-interne Aktions-Weiche aufrufen
		$this->make_module();
	}

	/**
	 * Aktions-Weiche der Klasse module_class
	 */
	function make_module()
	{
		// FRONTEND
		if (!defined('admin'))
		{
			$this->init("FRONT", $this->cms->style_id);
		}
		// BACKEND (Aufruf erfolgt aus Styles-Klasse)
	}

	/**
	 * Initialisierung der nötigen Variablen für das Front- bzw. Back-End
	 *
	 * @param string $modus
	 * @param int $style_id
	 */
	function init($modus = "FRONT", $style_id = 0)
	{
		// Initialisierungen für Front- und Back-End

		$this->module_liste = $this->module_lesen();

		$this->style_module_liste = $this->style_module_lesen($style_id);

		$this->bereiche_module = $this->bereiche_setzen();

		$this->module_aktiv_set();

		$this->content->template['module_aktiv'] = & $this->module_aktiv;

		$this->content->template['module'] = & $this->bereiche_module;

		// Initialisierungen für das Back-End
		if ($modus == "BACK") {
			$this->content->template['mod_style_id'] = $style_id;
			$this->content->template['module_inaktiv'] = $this->module_inaktiv_lesen();
			$this->content->template['bereiche_liste'] = array("1" => "Kopf","2" => "linke Spalte","3" => "mittlere Spalte","4" => "rechte Spalte","5" => "Fuss");
		}
	}

	/**
	 * @param string $modus
	 * @param int $style_id
	 */
	function switch_back($modus = "", $style_id = 0)
	{
		switch ($modus) {
		case "AKTIVIEREN":
			$this->admin_modul_aktivieren($style_id, $this->checked->mod_id, $this->checked->mod_bereich_id);
			$this->init("BACK", $style_id);
			break;

		case "DEAKTIVIEREN":
			$this->admin_modul_deaktivieren($style_id, $this->checked->mod_id);
			$this->admin_bereich_sortieren($style_id, $this->checked->mod_bereich_id);
			$this->init("BACK", $style_id);
			break;

		case "REORDER_HOCH":
			$this->admin_modul_verschieben($style_id, $this->checked->mod_id, $this->checked->mod_bereich_id, "HOCH");
			$this->admin_bereich_sortieren($style_id, $this->checked->mod_bereich_id);
			$this->init("BACK", $style_id);
			break;

		case "REORDER_RUNTER":
			$this->admin_modul_verschieben($style_id, $this->checked->mod_id, $this->checked->mod_bereich_id, "RUNTER");
			$this->admin_bereich_sortieren($style_id, $this->checked->mod_bereich_id);
			$this->init("BACK", $style_id);
			break;

		case "":
		default:
			$this->init("BACK", $style_id);
			break;
		}
	}

	/**
	 * Liest die Liste aller Module aus der Datenbank und gibt sie zurück.
	 * Bei einem Fehler, oder leerer Liste wird <false> zurück gegeben
	 *
	 * @return array|bool
	 */
	function module_lesen()
	{
		$temp_return = array();

		$sql = sprintf("SELECT * FROM %s as t1, %s as t2 WHERE t1.mod_id=t2.modlang_mod_id AND t2.modlang_lang_id='%d'",
			$this->db_praefix."papoo_module",
			$this->db_praefix."papoo_module_language",
			$this->cms->lang_id
		);
		$temp_result = $this->db->get_results($sql, ARRAY_A);

		// Modul-Array mit Indizes der Module versehen
		if (!empty($temp_result)) {
			foreach ($temp_result as $temp_modul) {
				$temp_return[$temp_modul['mod_id']] = $temp_modul;
			}
		}

		if (!empty($temp_return)) {
			return $temp_return;
		}
		else {
			return false;
		}
	}

	/**
	 * Liest die Liste aller installierten Module des Styles $style_id aus der Datenbank (Tabelle styles_module) aus
	 *
	 * @param int $style_id
	 * @return array|void
	 */
	function style_module_lesen($style_id = 0)
	{
		$temp_return = array();

		if (!empty($style_id)) {
			$sql = sprintf("SELECT * FROM %s WHERE stylemod_style_id='%d' ORDER BY stylemod_bereich_id, stylemod_order_id",
				$this->db_praefix."papoo_styles_module",
				$style_id
			);
			$temp_return = $this->db->get_results($sql, ARRAY_A);
		}

		return $temp_return;
	}

	/**
	 * Setze aus der Liste aller Module $this->module_liste und der Liste der Style-Module $this->style_module_liste
	 * die Module der verschiedenen Bereiche.
	 * Dabei wird das Array zuerst in ein Array mit der Anzahl der verschiedenen Bereiche $this->bereiche_anzahl aufgeteilt.
	 *
	 * @return array
	 */
	function bereiche_setzen()
	{
		$temp_return = array();

		// Array in die einzelnen Bereiche aufteilen
		for ($i = 1; $i <= $this->bereiche_anzahl; $i++) {
			$temp_return[$i] = array();
		}

		// die einzelnen AKTIVEN Module den jeweiligen Bereichen zuweisen.
		if (!empty($this->style_module_liste)) {
			foreach ($this->style_module_liste as $style_modul) {
				$temp_modul = $this->module_liste[$style_modul['stylemod_mod_id']];
				// Pfad-Korrekturen für verschiedene Module
				// a. Sonderbehandlung für $template
				if ($temp_modul['mod_datei'] == "[template]") {
					global $template;

					$temp_modul['mod_template'] = & $template; // !!! Muß Zeiger auf die Variable $template sein,
					// da diese Variable evtl. im weiteren Verlauf noch geändert wird (z.B. im Kategorie-Plugin von saschbo).
					// Das dies funktioniert zeigt die nächste Zeile. Hier wird das Template zum Test auf profil.html umgebogen:
					// $template = "profil.html";
				}
				// b. Sonderbehandlung für Plugin-Module
				elseif(strpos("XXX".$temp_modul['mod_datei'], "plugin:")) {
					// Prüfung ob Style eigene Plugin-Template-Datei hat
					$temp_style_template = str_replace("plugin:", PAPOO_ABS_PFAD."/styles/".$this->cms->style_dir."/templates/plugins/", $temp_modul['mod_datei']);
					if (file_exists($temp_style_template)) {
						$temp_modul['mod_template'] = $temp_style_template;
					}
					else {
						$temp_modul['mod_template'] = str_replace("plugin:", PAPOO_ABS_PFAD."/plugins/", $temp_modul['mod_datei']);
					}
				}
				// c. Standard
				else {
					$temp_modul['mod_template'] = $temp_modul['mod_datei'];
				}
				// Modul dem Bereich zuweisen
				$temp_return[$style_modul['stylemod_bereich_id']][] = $temp_modul;
			}
		}
		return $temp_return;
	}

	/**
	 * erstellt Listen aller aktiven (installierten) Module
	 * $this->module_aktiv: eine Liste mit den Modul-Namen und
	 * $this->module_aktiv_id: eine Liste mit den Modul-IDs
	 */
	function module_aktiv_set()
	{
		if (!empty($this->style_module_liste)) {
			foreach ($this->style_module_liste as $style_modul) {
				// Name des Moduls ermitteln
				$name = basename ($this->module_liste[$style_modul['stylemod_mod_id']]['mod_datei'], ".html");
				// Modul der Liste zuweisen und zwar so: $liste['name_des_moduls'] = true;
				$this->module_aktiv[$name] = true;
				$this->module_aktiv_id[] = $style_modul['stylemod_mod_id'];
			}
		}
	}

	/**
	 * Liest aus der Liste aller Module $this->module_liste die inaktiven Module
	 *
	 * @return array
	 */
	function module_inaktiv_lesen()
	{
		$temp = array();

		// Array in die einzelnen Bereiche aufteilen
		$module_liste = $this->module_liste;
		/**
		 * @param $a
		 * @param $b
		 * @return int|mixed?
		 */
		function cmp($a, $b)
		{
			return strcmp($a["modlang_name"], $b["modlang_name"]);
		}
		usort($module_liste, "cmp");

		// die einzelnen IN-AKTIVEN Module zuweisen.
		if (!empty($module_liste)) {
			foreach ($module_liste as $modul) {
				// Prüfung ob Modul iaktiv ist
				if (!in_array($modul['mod_id'], $this->module_aktiv_id)) {
					$temp[] = $modul;
				}
			}
		}
		return $temp;
	}


	/**
	 * aktiviert für den Style $style_id ein Modul $mod_id im Bereich $bereich_id als letztes Element
	 *
	 * @param int $style_id
	 * @param int $mod_id
	 * @param int $bereich_id
	 */
	function admin_modul_aktivieren($style_id = 0, $mod_id = 0, $bereich_id = 0)
	{
		if ($mod_id && $bereich_id) {
			// 1. order_id des letzten Moduls dieses Bereichs ermitteln
			$sql = sprintf("SELECT max(stylemod_order_id) as order_id FROM %s
							WHERE stylemod_style_id='%d' AND stylemod_bereich_id='%d'",
				$this->db_praefix."papoo_styles_module",
				$style_id,
				$bereich_id
			);
			$order_id = $this->db->get_var($sql);

			// 2. Modul für diesen Bereich als letztes Element aktivieren.
			$sql = sprintf("INSERT INTO %s SET stylemod_style_id='%d', stylemod_mod_id='%d', stylemod_bereich_id='%d', stylemod_order_id='%d'",
				$this->db_praefix."papoo_styles_module",
				$style_id,
				$mod_id,
				$bereich_id,
				$order_id + 10
			);
			$this->db->query($sql);
		}
	}

	/**
	 * deaktiviert im Style $style_id das Modul $mod_id
	 *
	 * @param int $style_id
	 * @param int $mod_id
	 */
	function admin_modul_deaktivieren($style_id = 0, $mod_id = 0)
	{
		if ($style_id && $mod_id) {
			$sql = sprintf("DELETE FROM %s WHERE stylemod_style_id='%d' AND stylemod_mod_id='%d'",
				$this->db_praefix."papoo_styles_module",
				$style_id,
				$mod_id
			);
			$this->db->query($sql);
		}
	}

	/**
	 * verschiebt im Style $style_id das Modul $mod_id im Bereich $bereich_id um einen Platz nach oben $modus="HOCH"
	 * oder einen Platz nach unten $modus="RUNTER"
	 *
	 * @param int $style_id
	 * @param int $mod_id
	 * @param int $bereich_id
	 * @param string $modus
	 */
	function admin_modul_verschieben($style_id = 0, $mod_id = 0, $bereich_id = 0, $modus = "")
	{
		if ($style_id && $mod_id && $bereich_id && $modus) {
			// Zu verschiebendes Modul in die "passende" Lücke verschieben
			if ($modus == "HOCH") {
				$sql = sprintf("UPDATE %s SET stylemod_order_id=(stylemod_order_id - 15) WHERE stylemod_style_id='%d' AND stylemod_mod_id='%d'",
					$this->db_praefix."papoo_styles_module",
					$style_id,
					$mod_id
				);
			}
			if ($modus == "RUNTER") {
				$sql = sprintf("UPDATE %s SET stylemod_order_id=(stylemod_order_id + 15) WHERE stylemod_style_id='%d' AND stylemod_mod_id='%d'",
					$this->db_praefix."papoo_styles_module",
					$style_id,
					$mod_id
				);
			}
			$this->db->query($sql);
		}
	}

	/**
	 * Setzt die Order-IDs aller Module eines Styles $style_id im Bereich $bereich_id neu.
	 * So werden z.B. Lücken nach der Deaktivierung eines Moduls geschlossen.
	 *
	 * @param int $style_id
	 * @param int $bereich_id
	 */
	function admin_bereich_sortieren ($style_id = 0, $bereich_id = 0)
	{
		if ($bereich_id) {
			// Module dieses Bereichs auslesen
			$sql = sprintf("SELECT * FROM %s WHERE stylemod_style_id='%d' AND stylemod_bereich_id='%d' ORDER BY stylemod_order_id ASC",
				$this->db_praefix."papoo_styles_module",
				$style_id,
				$bereich_id
			);
			$module = $this->db->get_results($sql, ARRAY_A);

			// Wenn es Module gibt, diese neu durchnummerieren
			if (!empty($module)) {
				$nummer_neu = 10;
				foreach ($module as $modul) {
					$sql = sprintf("UPDATE %s SET stylemod_order_id='%d' WHERE stylemod_style_id='%d' AND stylemod_mod_id='%d'",
						$this->db_praefix."papoo_styles_module",
						$nummer_neu,
						$style_id,
						$modul['stylemod_mod_id']
					);
					$this->db->query($sql);
					$nummer_neu += 10;
				}
			}
		}
	}

	/**
	 * diverse HILFS-FUNKTIONEN
	 *
	 * @param int $style_id
	 * @return bool
	 */
	function helper_make_xml($style_id = 0)
	{
		$temp_return = false;

		if ($style_id) {
			$this->init("FRONT", $style_id);

			if (!empty($this->bereiche_module)) {
				// XML-Daten  erstellen..
				$temp_xml_text = "";
				$temp_xml_text .= "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
				$temp_xml_text .= "<!-- Die Daten fuer den Modulmanager -->\n";
				$temp_xml_text .= "<modul_liste>\n\n";

				foreach($this->bereiche_module as $bereich_id =>$bereich) {
					if (!empty($bereich)) {
						foreach($bereich as $modul) {
							$temp_xml_text .= "\t<modul>\n";
							$temp_xml_text .= "\t\t<bereich_id>".$bereich_id."</bereich_id>\n";
							$temp_xml_text .= "\t\t<mod_datei>".$modul['mod_datei']."</mod_datei>\n";
							$temp_xml_text .= "\t</modul>\n\n";
						}
					}
				}
				$temp_xml_text .="</modul_liste>";

				// XML-Daten ausgeben
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Description: File Transfer");
				header("Accept-Ranges: bytes");
				header("Content-Type: text/xml");
				header('Content-Disposition: attachment; filename="module.xml"');
				header('Content-Transfer-Encoding: binary');
				echo $temp_xml_text;


				$temp_return = true;
			}
		}

		return $temp_return;
	}


	/**
	 * XML Datei erstellen für die CSS Einbindung
	 *
	 * @param array $module
	 * @return void
	 *
	 * @deprecated Warum ist das noch hier?
	 */
	function helper_make_xml_BACKUP($module = array())
	{
		$xml_text = "";
		$xml_text .= "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
		$xml_text .= "<!-- Die Daten fuer den Modulmanager -->\n";
		$xml_text .= "<modul_liste>\n\n";

		if (!empty($module)) {
			foreach ($module as $modul) {
				// Nur aktive Module speichern
				if ($modul['mod_aktiv']) {
					$xml_text.="\t<modul>\n";
					foreach ($modul as $key => $value) {
						$xml_text.="\t\t<".$key.">".$value."</".$key.">\n";
					}
					$xml_text.="\t</modul>\n\n";
				}
			}
			$xml_text.="</modul_liste>";

			$file="/interna/templates_c/module.xml";
			$this->diverse->write_to_file($file,$xml_text);
		}
	}
}

$module = new module_class();