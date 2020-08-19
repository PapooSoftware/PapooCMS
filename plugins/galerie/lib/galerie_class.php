<?php

if (!function_exists("IfNotSetNull")) {
	/**
	 * @param $arg
	 */
	function IfNotSetNull(&$arg)
	{
		if (!isset($arg)) {
			$arg = NULL;
		}
	}
}

/**
 * Class galerie_class
 */
class galerie_class
{
	/** @var array Enth�lt diverse Einstellungen f�r die Galerien */
	var $einstellungen = array();
	/** @var array maximale Breite/H�he der Vorschau-Bilder einer Galerie. */
	var $max_thumb = array();
	/** @var int Anzahl der vorhandenen Galerien */
	var $anzahlGalerien = 0;

	/**
	 * galerie_class constructor.
	 */
	function __construct()
	{
		global $weiter, $cms, $db, $checked, $content, $diverse, $image_core, $db_praefix;
		$this->weiter =& $weiter;
		$this->cms = & $cms;
		$this->db = & $db;
		$this->checked = & $checked;
		$this->content = & $content;
		$this->diverse = & $diverse;
		$this->image_core = & $image_core;
		$this->db_praefix = $db_praefix;

		$this->einstellungen = array();
		$this->max_thumb = array("breite" => 0, "hoehe" => 0);

		$this->make_galerie();

		IfNotSetNull($this->content->template['zugrifsrechte_galerien_nicht_ok']);
		IfNotSetNull($this->content->template['fehler1']);
		IfNotSetNull($this->content->template['galerie_message']);
		IfNotSetNull($this->content->template['galerie_data']);
		IfNotSetNull($this->content->template['galerie_bilder_liste']);
	}

	/**
	 * Global Aktions-Weiche der Klasse
	 */
	function make_galerie()
	{
		IfNotSetNull($this->checked->old_parent_id);

		global $template;

		// Anzahl der aktiven Galerien bestimmen
		$sql = "SELECT COUNT(*) FROM " . $this->db_praefix . "galerie_galerien WHERE ";
		if (defined("admin") === false) {
			$sql .= "gal_aktiv_janein=1";
		}
		else {
			$sql .= "parent_id!=0";
		}
		$this->anzahlGalerien = $this->db->get_var($sql);

		if (!defined("admin")) {
			// Frontend
			if (strpos("XXX".$template, "galerie_front.html") || strpos("XXX".$template, "galerie_diashow.utf8.html") || strpos("XXX".$template, "galerie_rss.utf8.html")) {
				$this->galerie_init("FRONT");
				$this->switch_front(@$this->checked->galerie_switch);

				if ($this->cms->mod_rewrite>=2) {
					$this->content->template['template']="galerie/templates/galerie_front.html";
				}
				else {
					$this->content->template['template']="galerie/templates/galerie_front.html";
				}
			}
			elseif(isset($this->checked->var1) && $this->checked->var1 == "galerie") {
				$galerie_name = $this->checked->var2;

				// Galerie ID rausfinden von der angegebenen Galerie
				$sql = sprintf("SELECT gallang_gal_id FROM %s WHERE gallang_name='%s'", $this->db_praefix . "galerie_galerien_language", $this->db->escape($galerie_name));
				$result = $this->db->get_results($sql, ARRAY_A);

				if(!empty($result)) {
					// Zur passenden Galerie springen
					$galerie_id = $result[0]['gallang_gal_id'];

					header("Location:../plugin.php?galerie_switch=GALERIE_START&galerie_id=$galerie_id&template=galerie/templates/galerie_front.html");
					exit;
				}
				else {
					// 'Diese Galerie beinhaltet keine Bilder' anzeigen.
					header("Location:../plugin.php?galerie_switch=GALERIE_START&galerie_id=0&template=galerie/templates/galerie_front.html");
					exit;
				}
			}
			elseif (strpos("XXX".$template, "cat_front_main.html")) {
				if ($this->checkNumeric((array)$this->checked->cat_id)) {
					$result = $this->getCatData("");
					// Baumstruktur erstellen, dazu die Level, parents, childs und Anzahl der n�tigen ul/li-Endetags ans Template
					// Das gibt dem Template die M�glichkeit die ul-li-Struktur komplett zu erstellen (s. z. B. cat_back_main.html)
					if (count($result)) {
						// Galerie-Namen ausgeben, aber nur f�r die vorgegebene Cat-ID
						$this->content->template['galerie_cat_data'] = $this->categoriesTree($result, $this->checked->cat_id);
					}
				}
				else {
					// Hauptseite Kategorien, Kategorien anzeigen
					$this->content->template['galerie_cat_data'] = array();
					$this->fetchAllCategories(0, ""); // Alle Kategoriedaten holen
				}
				$this->content->template['template']="galerie/templates/galerie_front.html";
			}
		}
		else {
			// Backend
			if (strpos("XXX".$template, "galerie_back.html")) {
				$pfad = PAPOO_ABS_PFAD."/plugins/galerie/galerien/";
				$pfad=str_replace("//","/",$pfad);
				if ($this->oktale_zugriffsrechte($pfad) < 777) {
					if (!(@chmod($pfad, 0777))) {
						$this->content->template['zugrifsrechte_galerien_nicht_ok'] = $this->content->template['plugin']['galerie_back']['errmsg']['zugriffsrechte_galerie_nicht_ok'];
					}
				}

				$this->galerie_init("BACK");
				$this->switch_back(@$this->checked->galerie_switch);
				$this->fetchAllCategories(0, ""); // Alle Kategoriedaten holen
				// Submit ist erfolgt:
				if ($this->checked->old_parent_id AND $this->checked->old_parent_id != $this->checked->cat_new_sel_id) {
					$this->cat_alloc();
				}
			}
			elseif (strpos("XXX".$template, "galerie_back_install.html")) {
				$this->galerie_init("BACK");
			}
			elseif (strpos("XXX".$template, "galerie_back_tools.html")) {
				$this->galerie_init("BACK");
				$this->switch_back_tools(@$this->checked->galerie_switch);
			}
			elseif (strpos("XXX".$template, "galerie_back_set.html")) {
				$this->galerie_init("BACK");
				$this->switch_back_einstellungen(@$this->checked->galerie_switch);
			}
			elseif (strpos("XXX".$template, "cat_back_main.html")) {
				// Hauptseite Kategorien, Kategorien anzeigen
				$this->content->template['galerie_cat_data'] = array();
				$this->fetchAllCategories(0, ""); // Alle Kategoriedaten holen
				#$this->galerie_init("BACK");
				#$this->switch_back_einstellungen(@$this->checked->galerie_switch);
			}
			elseif (strpos("XXX".$template, "cat_back_new.html") && !strpos("XXX".$template, "faq_cat_back_new.html")) {
				$this->new_category();
			}
			elseif (strpos("XXX".$template, "cat_back_edit.html") && !strpos("XXX".$template, "faq_cat_back_edit.html")) {
				$this->edit_category();
			}
			elseif (strpos("XXX".$template, "cat_back_del.html") && !strpos("XXX".$template, "faq_cat_back_del.html")) {
				$this->del_category();
			}
		}
	}

	/**
	 * Liefert die dreistelligen, oktalen Zugriffsrechte einer Datei
	 *
	 * @param $value_datei
	 * @return bool|string
	 */
	function oktale_zugriffsrechte($value_datei)
	{
		return substr(sprintf('%o', fileperms($value_datei)), -3);
	}

	function cat_alloc()
	{
		if ($this->checked->cat_new_sel_id
			AND $this->checkNumeric((array)$this->checked->cat_new_sel_id)
			AND (array)$this->checkNumeric($this->checked->old_parent_id)) {
			$sql = sprintf("UPDATE %s SET parent_id = '%d'
							WHERE parent_id = '%d' AND gal_id = '%d'",
				$this->cms->tbname['galerie_galerien'],
				$this->db->escape($this->checked->cat_new_sel_id),
				$this->db->escape($this->checked->old_parent_id),
				$this->db->escape($this->checked->galerie_id)
			);
			$this->db->query($sql);
		}
		else {
			$this->content->template['fehler1'] = 1;
		}
	}

	/**
	 * Initialisieren der Klasse. Es werden alle "grundlegenden" Funktionen ausgef�hrt.
	 *
	 * @param string $modus
	 */
	function galerie_init($modus = "FRONT")
	{
		$this->einstellungen_laden();
	}

	/**
	 * Aktions-Weiche f�r Frontend
	 *
	 * @param bool $modus
	 */
	function switch_front($modus = false)
	{
		switch ($modus) {
		case "GALERIE_DIASHOW":
			$this->content->template['galerie_data'] = $this->galerien_data($this->checked->galerie_id);
			$this->content->template['galerie_bilder'] = $this->bilder_liste($this->checked->galerie_id, "NODECODE");
			$this->content->template['galerie_max_dimensions'] = array("breite" => ($this->max_thumb["breite"]), "hoehe" => ($this->max_thumb["hoehe"]), "hoehe_leiste" => ($this->max_thumb["hoehe"] * 1.3 + 5));
			$this->diverse->no_output="no";
			$this->content->template['galerie_weiche'] = "GALERIE_DIASHOW";
			break;

		case "GALERIE_START":
			$bild_id = $this->galerien_bild($this->checked->galerie_id);
			$this->content->template['galerie_bild'] = $this->bilder_data($bild_id);
			$this->content->template['galerie_bilder'] = $this->bilder_liste($this->checked->galerie_id, "NOBR");
			$this->show_details($this->content->template['galerie_bilder']);
			$this->content->template['galerie_max_dimensions'] = $this->max_thumb;
			$this->content->template['galerie_data'] = $this->einstellungen;
			$this->tools_make_lightbox();
			$this->content->template['galerie_weiche'] = "GALERIE";
			break;

		case "GALERIE":
			IfNotSetNull($this->checked->galerie_navigation_weiter);
			IfNotSetNull($this->checked->galerie_navigation_zurueck);
			IfNotSetNull($this->checked->galerie_bild_auswahl);
			IfNotSetNull($this->checked->galerie_id);
			IfNotSetNull($this->checked->galerie_bild_aktu);
			IfNotSetNull($richtung);

			if ($this->checked->galerie_navigation_weiter) $richtung = "weiter";
			if ($this->checked->galerie_navigation_zurueck) $richtung = "zurueck";
			if ($this->checked->galerie_bild_auswahl) $richtung = "auswahl";

			$bild_id = $this->galerien_bild($this->checked->galerie_id, $this->checked->galerie_bild_aktu, $richtung);
			$this->content->template['galerie_bild'] = $this->bilder_data ($bild_id);
			$this->content->template['galerie_bilder'] = $this->bilder_liste($this->checked->galerie_id, "NOBR");
			$this->content->template['galerie_max_dimensions'] = $this->max_thumb;
			$this->content->template['galerie_data'] = $this->einstellungen;

			$this->content->template['galerie_weiche'] = "GALERIE";
			break;

		default:
			$this->content->template['galerie_liste'] = $this->galerien_liste();
			$this->content->template['galerie_data'] = $this->einstellungen;
			$this->content->template['galerie_weiche'] = "LISTE";
			break;
		}
	}

	/**
	 * @param $results
	 */
	function show_details($results)
	{
		if(empty($results)) {
			return;
		}

		foreach ($results as $key => $value) {
			$filename = PAPOO_ABS_PFAD
				. "/plugins/galerie/galerien/"
				. $value['gal_verzeichnis']
				. "/"
				. $value['bild_datei'];
			if (file_exists($filename)) {
				$exif_daten = @exif_read_data($filename, 'EXIF');
				if ($exif_daten['GPSLatitudeRef']) {
					$this->getGps($exif_daten["GPSLongitude"]);
					$value['lon_degrees'] = $this->degrees;
					$value['lon_minutes'] = $this->minutes;
					$value['lon_seconds'] = $this->seconds;
					$value['GPSLongitudeRef'] = $exif_daten['GPSLongitudeRef'];
					$value['GPSLongitudeRef_dec'] = ($exif_daten['GPSLongitudeRef'] == 'S' || $exif_daten['GPSLongitudeRef']== 'W') ?  $this->decimal *= -1 : $this->decimal;
					$this->getGps($exif_daten["GPSLatitude"]);
					$value['lat_degrees'] = $this->degrees;
					$value['lat_minutes'] = $this->minutes;
					$value['lat_seconds'] = $this->seconds;
					$value['GPSLatitudeRef'] = $exif_daten['GPSLatitudeRef'];
					$value['GPSLatitudeRef_dec'] = ($exif_daten['GPSLatitudeRef'] == 'S' || $exif_daten['GPSLatitudeRef']== 'W') ?  $this->decimal *= -1 : $this->decimal;
				}
				$this->content->template['gal_verzeichnis'][$key] = $value; // Bild-, Galerie- und DL-Daten ans Template
			}
		}
		$this->content->template['galerie_bilder'] = $this->content->template['gal_verzeichnis'];
	}

	/**
	 * @param $exifCoord
	 */
	function getGps($exifCoord)
	{
		$this->degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
		$this->minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
		$this->seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;
		//normalize
		$this->minutes += 60 * ($this->degrees - floor($this->degrees));
		$this->degrees = floor($this->degrees);
		$this->seconds += 60 * ($this->minutes - floor($this->minutes));
		$this->minutes = floor($this->minutes);
		$this->decimal = $this->degrees + ((($this->minutes / 60) + ($this->seconds / 3600)));
		return;
	}

	/**
	 * @param $coordPart
	 * @return float|int
	 */
	function gps2Num($coordPart)
	{
		$parts = explode('/', $coordPart);
		if (count($parts) <= 0) return 0;
		if (count($parts) == 1) return $parts[0];
		return floatval($parts[0]) / floatval($parts[1]);
	}

	/**
	 * Aktions-Weiche f�r Backend
	 *
	 * @param bool $modus
	 */
	function switch_back($modus = false)
	{
		IfNotSetNull($this->checked->galerie_standzeit);

		switch ($modus) {
			// =================
			// Bereich BILDER
			// =================
		case "GALERIE_BILD_REORDER":
			//$this->bilder_reorder($this->checked->galerie_bild_id, $this->checked->galerie_reorder);
			$this->bilder_reorder($this->checked->galerie_id, $this->checked->galerie_bild_order_ids);
			$this->bilder_nummer_aktualisieren($this->checked->galerie_id);

			$this->switch_back("GALERIE_DETAILS");
			break;

		case "GALERIE_BILD_DELETE":
			if ($this->bilder_loeschen($this->checked->galerie_bild_id)) {
				$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_BACK_BILD_DELETE_OK'];
			}
			else {
				$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_BACK_BILD_DELETE_FEHLER'];
			}
			$this->switch_back("GALERIE_DETAILS");
			break;

		case "GALERIE_BILD_SAVE_NEU":
			$this->content->template['galerie_liste'] = $this->galerien_liste("ALL");
			$galerien = array();
			if (is_array($this->content->template['galerie_liste'])) {
				foreach ($this->content->template['galerie_liste'] as $value) {
					$galerien[$value["gal_id"]] = $value;
				}
			}
			$galerie_id = (int)$this->checked->galerie_id;
			$ignoreFiles = $this->bilder_liste_komplett($galerie_id);
			$this->tools_store_multi_upload($galerien[$galerie_id]["gal_verzeichnis"]);
			if ($this->import_gallery_start($galerie_id, $ignoreFiles, $galerien[$galerie_id]["gal_verzeichnis"], true)) {
				$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_BACK_BILD_SAVE_NEU_OK'];
			}
			else {
				$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_BACK_BILD_SAVE_NEU_FEHLER'];
			}
			$this->switch_back("GALERIE_DETAILS");
			break;

		case "GALERIE_BILD_SAVE_EDIT":
			if ($this->bilder_texte_sichern($this->checked->galerie_bild_id, $this->checked->galerie_bild_texte)) {
				$this->einstellungen_standzeit_edit($this->checked->galerie_standzeit, "BILD", $this->checked->galerie_bild_id);
				$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_BACK_BILD_SAVE_EDIT_OK'];
				$this->switch_back("GALERIE_DETAILS");
			}
			else {
				$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_BACK_BILD_SAVE_EDIT_FEHLER'];
				$this->switch_back("GALERIE_BILD_EDIT");
			}
			break;

		case "GALERIE_BILD_NEU":
			$this->content->template['galerie_data'] = $this->galerien_data($this->checked->galerie_id);

			$this->content->template['galerie_weiche'] = "GALERIE_BILD_NEU";
			break;

		case "GALERIE_BILD_EDIT":
			$this->content->template['galerie_data'] = $this->galerien_data($this->checked->galerie_id);
			$this->content->template['galerie_bild_id'] = $this->checked->galerie_bild_id;
			if (isset($this->checked->galerie_modus) && $this->checked->galerie_modus == "NEU") {
				$this->content->template['galerie_bild_data'] = $this->bilder_data($this->checked->galerie_bild_id, "KEIN_TEXT");
			}
			else {
				$this->content->template['galerie_bild_data'] = $this->bilder_data($this->checked->galerie_bild_id);
			}
			$this->content->template['galerie_bild_texte'] = $this->bilder_texte_laden($this->checked->galerie_bild_id);

			$this->content->template['galerie_weiche'] = "GALERIE_BILD_EDIT";
			break;

			// =================
			// Bereich GALERIEN
			// =================
		case "GALERIE_DO_DELETE":
			if ($this->galerien_loeschen($this->checked->galerie_id)) {
				$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_BACK_GALERIE_DELETE_OK'];
			}
			else {
				$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_BACK_GALERIE_DELETE_FEHLER'];
			}
			$this->switch_back();
			break;

		case "GALERIE_DELETE":
			$this->content->template['galerie_data'] = $this->galerien_data($this->checked->galerie_id);

			$this->content->template['galerie_weiche'] = "GALERIE_DELETE";
			break;

		case "GALERIE_REORDER":
			//$this->content->template['galerie_data'] = $this->galerien_reorder($this->checked->galerie_id, $this->checked->galerie_reorder);
			$this->galerien_reorder($this->checked->galerie_order_ids);
			$this->galerien_order_aktualisieren();
			$this->switch_back();
			break;

		case "GALERIE_SAVE":
			$galerie_id = $this->galerien_sichern($this->checked->galerie_modus);
			if ($galerie_id) {
				$this->galerien_texte_sichern($galerie_id, $this->checked->galerie_texte);
				if ($this->checked->galerie_standzeit > 0) {
					$this->einstellungen_standzeit_edit($this->checked->galerie_standzeit, "GALERIE", $galerie_id);
				}
				$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_BACK_GALERIE_SAVE_OK'];
			}
			else {
				$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_BACK_GALERIE_SAVE_FEHLER'];
			}

			if ($this->checked->galerie_modus == "NEU") {
				$this->switch_back();
			}
			else {
				$this->switch_back("GALERIE_DETAILS");
			}
			break;

		case "GALERIE_EDIT":
			$this->content->template['galerie_data'] = $this->galerien_data($this->checked->galerie_id);
			$this->content->template['galerie_texte'] = $this->galerien_texte_laden($this->checked->galerie_id, "", "nobr:");
			$this->content->template['galerie_bilder_liste'] = $this->bilder_liste($this->checked->galerie_id);

			$this->content->template['galerie_modus'] = "EDIT";
			$this->content->template['galerie_weiche'] = "GALERIE_NEU_EDIT";
			break;

		case "GALERIE_DETAILS":
			$this->content->template['galerie_data'] = $this->galerien_data($this->checked->galerie_id);
			$this->content->template['galerie_bilder_liste'] = $this->bilder_liste($this->checked->galerie_id);
			$this->content->template['galerie_weiche'] = "GALERIE_DETAILS";
			break;

		case "GALERIE_NEU":
			$this->content->template['galerie_texte'] = $this->galerien_texte_laden(0);
			$this->content->template['galerie_modus'] = "NEU";
			$this->content->template['galerie_weiche'] = "GALERIE_NEU_EDIT";
			break;

		default:
			$this->content->template['galerie_liste'] = $this->galerien_liste("ALL");
			$this->content->template['galerie_safemode'] = ini_get("safe_mode");

			$this->content->template['galerie_weiche'] = "GALERIE_LISTE";
			break;
		}
	}

	/**
	 * @param bool $galerie_id
	 * @param array $ignore_list
	 * @param $gal_verzeichnis
	 * @param bool $namen_anpassen
	 * @return bool
	 */
	private function import_gallery_start($galerie_id = false, $ignore_list = array(), $gal_verzeichnis, $namen_anpassen = false)
	{
		// dreckiger Hack um Galerie-Texte automatisch zu erstellen
		$this->checked->galerie_texte = $this->galerien_texte_laden(0, $gal_verzeichnis);

		// Wichtige Änderungen, um den Import unter Werkzeuge per ajax durchführen zu können
		$ajax_import = isset($_POST["ajax_import"]) ? $_POST["ajax_import"] : false;
		$durchlauf = 0;
		$limit = 10;
		if ($galerie_id === false) {
			if ($ajax_import === false || $ajax_import === "start") {
				$galerie_id = $this->galerien_sichern("NEU", $gal_verzeichnis);
			}
			else if ($ajax_import === "continue" && isset($_POST["json"])) {
				$jsonObj = json_decode($_POST["json"]);
				$galerie_id = (int)$jsonObj->gid;
				$durchlauf = (int)$jsonObj->pass;
			}
		}

		if ($galerie_id) {
			$texte = $this->galerien_texte_laden($galerie_id, $gal_verzeichnis);
			$this->galerien_texte_sichern($galerie_id, $texte);

			$bilder = $this->tools_bilder_liste($gal_verzeichnis);
			if (is_array($bilder)) {
				$bilder = array_diff($bilder, $ignore_list);
			}
			if (is_array($bilder) && ($bilder_gesamt = count($bilder)) > 0) {
				if ($ajax_import !== false) {
					$bilder = array_slice($bilder, $durchlauf * $limit, $limit, true);
				}
				$counter = 0;
				$this->nextBildNummer = $ajax_import === false ? count($ignore_list) + 1 : $durchlauf * $limit + 1;
				foreach($bilder as $bild) {
					$bild_id = $this->bilder_sichern("NEU", $galerie_id, $bild);
					// Test auf automatische Bild-Namen-Anpassung
					if ($namen_anpassen) $bild = $this->tools_bilder_name_anpassen($bild);

					$texte = $this->bilder_texte_laden($bild_id, $bild);
					$this->bilder_texte_sichern($bild_id, $texte);
					$counter++;
					if($counter % 10 == 0) {
						set_time_limit(20);
					}
				}
				if ($ajax_import !== false) {
					$ajaxResponse = new stdClass();
					$ajaxResponse->gid = $galerie_id;
					$ajaxResponse->pass = $durchlauf + 1;
					$ajaxResponse->processedImages = $durchlauf * $limit + count($bilder);
					$ajaxResponse->imageCount = $bilder_gesamt;
					$ajaxResponse->done = $ajaxResponse->processedImages >= $ajaxResponse->imageCount;
					header("Content-type: application/json");
					echo(json_encode($ajaxResponse));
					exit;
				}
			}
			$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_TOOLS_GALERIE_OK'];
			$this->content->template['galerie_weiche'] = "GALERIE_OK";
			return true;
		}
		else {
			$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_TOOLS_GALERIE_FEHLER'];
			#$this->switch_back_tools();
			return false;
		}
	}

	/**
	 * Aktions-Weiche f�r Backend "Werkzeuge"
	 *
	 * @param bool $modus
	 */
	function switch_back_tools($modus = false)
	{
		switch ($modus) {
		case "GALERIE":
			$this->import_gallery_start(false, array(), $this->checked->galerie_verzeichnis, isset($this->checked->galerie_namen_anpassen));
			break;

		default:
			$this->content->template['galerie_liste'] = $this->galerien_liste("ALL");
			$this->content->template['galerie_verzeichnis_liste'] = $this->tools_galerien_liste();
			$this->content->template['galerie_weiche'] = "GALERIE_TOOLS";
			break;
		}
	}

	/**
	 * Aktions-Weiche f�r Backend "Einstellungen"
	 *
	 * @param bool $modus
	 */
	function switch_back_einstellungen($modus = false)
	{
		switch ($modus) {
		case "GALERIE_THUMBS_EDIT":
			if ($this->einstellungen_thumbs_edit()) {
				$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_SET_THUMBS_EDIT_OK'];
				$this->content->template['galerie_weiche'] = "GALERIE_THUMBS_EDIT";
			}
			else {
				$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_SET_THUMBS_EDIT_FEHLER'];
				$this->switch_back_tools();
			}
			break;

		case "GALERIE_THUMBS":
			$this->einstellungen_sichern("THUMBS");
			$this->content->template['galerie_daten'] = $this->einstellungen;

			$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_SET_THUMBS_OK'];
			$this->content->template['galerie_weiche'] = "GALERIE_THUMBS";
			break;

		case "GALERIE_DIASHOW":
			$this->einstellungen_sichern("DIASHOW");

			$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_SET_DIASHOW_OK'];
			$this->switch_back_einstellungen();
			break;

		case "GALERIE_STANDZEIT_EDIT":
			$this->einstellungen_standzeit_edit($this->checked->galerie_standzeit, "ALLE");

			$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_SET_STANDZEIT_EDIT_OK'];
			$this->content->template['galerie_weiche'] = "GALERIE_STANDZEIT_EDIT";
			break;

		case "GALERIE_STANDZEIT":
			$this->einstellungen_sichern("STANDZEIT");
			$this->content->template['galerie_daten'] = $this->einstellungen;

			$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_SET_STANDZEIT_OK'];
			$this->content->template['galerie_weiche'] = "GALERIE_STANDZEIT";
			break;

		case "GALERIE_LIGHTBOX":
			$this->einstellungen_sichern("LIGHTBOX");

			$this->content->template['galerie_message'] = $this->content->template['GALMSGB']['SWITCH_SET_STANDZEIT_OK'];
			$this->switch_back_einstellungen();
			break;

		default:
			$this->content->template['galerie_daten'] = $this->einstellungen;
			//$this->content->template['galerie_diashows'] = $this->diashows_liste();

			$this->content->template['galerie_weiche'] = "GALERIE_SET";
			break;
		}
	}


	// ===========================================================
	//
	// Galerien Funktionen
	//
	// ===========================================================

	/**
	 * Erstellt eine Liste aller aktiven Galerien.
	 * Pagination sowohl im Backend als auch im Frontend
	 *
	 * @param bool $modus
	 *  -NIL- (leer): Liste aktiver Galerien f�r Frontend
	 *  "ALL": Liste aller Galerien (aktive und nicht-aktive) f�r Backend
	 * @param bool $forceNoLimit
	 * @return array|bool|void
	 */
	function galerien_liste($modus = false, $forceNoLimit = false)
	{
		IfNotSetNull($this->checked->cat_new_sel_id);

		switch ($modus) {
		case "ALL":
			if ($this->checked->cat_new_sel_id) $sql_add = "parent_id='" . $this->db->escape($this->checked->cat_new_sel_id) . "' AND";
			else $sql_add = "parent_id !='0' AND";
			break;

		default:
			$sql_add = "t1.gal_aktiv_janein='1' AND";
		}

		// Paginating initialisieren
		if ($forceNoLimit == false) {
			if(defined("admin")) {
				$weiterTemplate = "galerie/templates/galerie_back.html";
			}
			else {
				$weiterTemplate = "galerie/templates/galerie_front.html";
			}
			$this->weiter->modlink = "no";
			$this->weiter->make_limit($this->cms->system_config_data['config_paginierung']);
			$this->weiter->result_anzahl = $this->anzahlGalerien;
			$this->weiter->weiter_link = "plugin.php?menuid=" . $this->checked->menuid . "&amp;template=" . $weiterTemplate;
			$this->weiter->do_weiter( "teaser" );
		}

		//$this->weiter->sqllimit = "LIMIT 0";
		// Galerieliste mit sqllimit von weiter-Klasse holen
		$sql = sprintf(	"SELECT * FROM %s AS t1, %s AS t2, %s AS t3 , %s AS t4
						WHERE %s
						t1.gal_id=t2.gallang_gal_id AND t2.gallang_lang_id='%d'
						AND t1.gal_bild_id=t3.bild_id AND t3.bild_id=t4.bildlang_bild_id AND t4.bildlang_lang_id='%d'
						ORDER BY t1.gal_order_id ASC, t1.gal_id DESC %s",
			$this->db_praefix."galerie_galerien",
			$this->db_praefix."galerie_galerien_language",
			$this->db_praefix."galerie_bilder",
			$this->db_praefix."galerie_bilder_language",
			$sql_add,
			$this->cms->lang_id,
			$this->cms->lang_id,
			($forceNoLimit ? "" : $this->weiter->sqllimit)
		);
		$galerien = $this->db->get_results($sql, ARRAY_A);

		if (!empty($galerien)) {
			return $galerien;
		}
		else {
			return false;
		}
	}

	/**
	 * Sucht die Daten der Galerie $galerie_id
	 *
	 * @param int $galerie_id
	 * @return bool
	 */
	function galerien_data($galerie_id = 0)
	{
		$sql = sprintf(	"SELECT * FROM %s AS t1, %s AS t2, %s AS t3 , %s AS t4
						WHERE t1.gal_id='%d' AND
						t1.gal_id=t2.gallang_gal_id AND t2.gallang_lang_id='%d'
						AND t1.gal_bild_id=t3.bild_id AND t3.bild_id=t4.bildlang_bild_id AND t4.bildlang_lang_id='%d'",
			$this->db_praefix."galerie_galerien",
			$this->db_praefix."galerie_galerien_language",
			$this->db_praefix."galerie_bilder",
			$this->db_praefix."galerie_bilder_language",
			$galerie_id,
			$this->cms->lang_id,
			$this->cms->lang_id
		);
		$galerie = $this->db->get_results($sql, ARRAY_A);

		if (!empty($galerie)) {
			return $galerie[0];
		}
		else {
			return false;
		}
	}

	/**
	 * Sucht das anzuzeigenden Bild einer aktiven Galerie $galerie_id.
	 * $bild_id ist dabei die ID des aktuell angezeigten Bildes.
	 *
	 * @param int $galerie_id
	 * @param int $bild_aktu
	 * @param string $richtung gibt die Richtung des n�chsten Bildes an
	 *  "zurueck" f�r das vorherige,
	 *  "weiter" f�r das n�chste Bild,
	 *  "auswahl" besagt, dass bild_id als n�chtest angezeigt werden soll
	 * @return bool|int
	 */
	function galerien_bild($galerie_id = 0, $bild_aktu = 0, $richtung = "")
	{
		switch ($richtung) {
		case "auswahl":
			$bild_nummer = $bild_aktu;
			break;

		case "weiter":
			$bild_nummer = $bild_aktu + 1;
			break;

		case "zurueck":
			$bild_nummer = $bild_aktu - 1;
			break;

		case "":
		default:
			$bild_nummer = 1;
			break;
		}

		$sql = sprintf("SELECT `bild_id`
						FROM `%s`
						LEFT JOIN `%s` ON `bild_gal_id` = `gal_id`
						WHERE `bild_gal_id` = %d AND `bild_nummer` = %d AND `gal_aktiv_janein` = 1
						LIMIT 1",
			$this->cms->tbname["galerie_galerien"],
			$this->cms->tbname["galerie_bilder"],
			(int)$galerie_id,
			(int)$bild_nummer
		);
		$bild_id_next = $this->db->get_var($sql);

		return $bild_id_next !== null ? (int)$bild_id_next : false;
	}

	private function restoreNoPictureAvaliableDBRows()
	{
		$this->db->query("INSERT IGNORE INTO {$this->cms->tbname["galerie_bilder"]} SET ".
			"bild_id = 1, bild_datei = '../../../bilder/kein_bild.gif', bild_gal_id = 0, ".
			"bild_nummer = 0, bild_format = 'GIF', bild_breite = 100, bild_breite_thumb = 100, ".
			"bild_hoehe = 30, bild_hoehe_thumb = 30, bild_diashow_timeout = 5");
		if ((int)$this->db->get_var(
				"SELECT COUNT(*) FROM {$this->cms->tbname["galerie_bilder_language"]} WHERE bildlang_bild_id = 1"
			) == 0) {
			$this->db->query("INSERT INTO {$this->cms->tbname["galerie_bilder_language"]} ".
				"(bildlang_bild_id, bildlang_lang_id, bildlang_name, bildlang_beschreibung) VALUES ".
				"(1, 1, 'kein Bild', 'kein Bild'), ".
				"(1, 2, 'no picture', 'no picture')");
		}
	}

	/**
	 * Speichert die Daten einer Galerie
	 *
	 * @param string $modus
	 * @param string $verzeichnis
	 * @return bool|mixed
	 */
	function galerien_sichern($modus = "", $verzeichnis = "")
	{
		if ($modus == "NEU") {
			if (!$verzeichnis) {
				// Galerie-Verzeichnis(e) anlegen
				$pfad = $this->cms->pfadhier."/plugins/galerie/galerien/";
				$verzeichnis = $this->diverse->sicherer_dateiname(array_reduce($this->checked->galerie_texte, function ($c, $i) {
					return strlen($c) > 0 ? $c : $i["name"];
				}));

				if (is_dir($pfad.$verzeichnis)) {
					return false;
				} // Test: Verzeichnis existiert schon.
				if (!mkdir($pfad.$verzeichnis, 0777)) {
					return false;
				} // Test: Verzeichnis wurde angelegt.
				if (!mkdir($pfad.$verzeichnis."/thumbs", 0777)) {
					return false;
				}	// Test: Thumbs-Verzeichnis wurde angelegt.
			}
			$this->restoreNoPictureAvaliableDBRows();
			// 999999999 als Kennzeichnung, dass es keine Kategorie ist. Erst mal nach der �bernahme einer Galerie
			$sql = sprintf("INSERT INTO %s SET gal_verzeichnis='%s', gal_bild_id='1', gal_order_id='0', parent_id='999999999'",
				$this->db_praefix."galerie_galerien",
				$this->db->escape($verzeichnis));
			$this->db->query($sql);
			$galerie_id = $this->db->insert_id;

			$this->galerien_order_aktualisieren();
		}
		else {
			$galerie_id = $this->checked->galerie_id;
			if ($this->checked->galerie_bild_id > 1) {
				$galerie_bild_id = $this->checked->galerie_bild_id;
			}
			else {
				$galerie_bild_id = 1;
			}

			$sql = sprintf("UPDATE %s SET gal_aktiv_janein='%d', gal_bild_id='%d' WHERE gal_id='%d'",
				$this->db_praefix."galerie_galerien",
				$this->checked->galerie_aktiv_janein,
				$galerie_bild_id,
				$galerie_id
			);
			$this->db->query($sql);
		}
		return $galerie_id ;
	}

	/**
	 * @param array $order_ids
	 */
	function galerien_reorder($order_ids = array())
	{
		if (!empty($order_ids)) {
			foreach ($order_ids as $galerie_id => $order_id) {
				$sql = sprintf("UPDATE %s SET gal_order_id=%d WHERE gal_id='%d'",
					$this->db_praefix."galerie_galerien",
					$order_id,
					$galerie_id
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * Speichert die Daten einer Galerie
	 */
	function galerien_order_aktualisieren()
	{
		$sql = sprintf("SELECT * FROM %s ORDER BY gal_order_id ASC",
			$this->db_praefix."galerie_galerien"
		);
		$galerien = $this->db->get_results($sql);

		if (!empty($galerien)) {
			$counter = 10;
			foreach($galerien AS $galerie) {
				$sql = sprintf("UPDATE %s SET gal_order_id='%d' WHERE gal_id='%d'",
					$this->db_praefix."galerie_galerien",
					$counter,
					$galerie->gal_id
				);
				$this->db->query($sql);
				$counter += 10;
			}
		}
	}

	/**
	 * L�scht s�mtliche Daten der Galerie (DB-Eintr�ge und Dateien)
	 *
	 * @param int $galerie_id
	 * @return bool
	 */
	function galerien_loeschen($galerie_id = 0)
	{
		// Informationen sammeln
		$galerie = $this->galerien_data($galerie_id);
		$galerie_verzeichnis = $this->cms->pfadhier."/plugins/galerie/galerien/".$galerie['gal_verzeichnis']."/";
		$galerie_verzeichnis_thumbs = $galerie_verzeichnis."thumbs/";

		// Dateien und Verzeichnisse l�schen
		$this->diverse->remove_files($galerie_verzeichnis_thumbs, ".*");
		rmdir($galerie_verzeichnis_thumbs);
		$this->diverse->remove_files($galerie_verzeichnis, ".*");
		rmdir($galerie_verzeichnis);

		// DB-Eintr�ge aus Bilder-Tabellen l�schen
		$bilder = $this->bilder_liste($galerie_id);
		if(!empty($bilder)) {
			foreach ($bilder AS $bild) {
				// 1. Eintr�ge aus Tabelle galerie_bilder_language l�schen
				$sql = sprintf("DELETE FROM %s WHERE bildlang_bild_id='%d'", $this->db_praefix."galerie_bilder_language", $bild['bild_id']);
				$this->db->query($sql);
				// 2. Eintr�ge aus Tabelle galerie_bilder l�schen
				$sql = sprintf("DELETE FROM %s WHERE bild_id='%d'", $this->db_praefix."galerie_bilder", $bild['bild_id']);
				$this->db->query($sql);
			}
		}

		// DB-Eintr�ge aus Galerie-Tabellen l�schen
		// 1. Sprachen l�schen
		$sql = sprintf("DELETE FROM %s WHERE gallang_gal_id='%d'", $this->db_praefix."galerie_galerien_language", $galerie_id);
		$this->db->query($sql);
		// 2. Galerie l�schen
		$sql = sprintf("DELETE FROM %s WHERE gal_id='%d'", $this->db_praefix."galerie_galerien", $galerie_id);
		$this->db->query($sql);

		// Galerien neu durchnummerieren
		$this->galerien_order_aktualisieren();

		return true;
	}

	/**
	 * L�d die Texte der Galerie (Name, Beschreibung)
	 * entweder aus den �bertragenen Formular-Daten (bei Fehl-Eingaben im Formular)
	 * oder aus der Tabelle galerien_galerie_language (zum Editieren)
	 *
	 * @param int $galerie_id
	 * @param string $default_text
	 * @param string $modus "nobr:" oder leer
	 * @return array
	 */
	function galerien_texte_laden($galerie_id = 0, $default_text = "", $modus = "")
	{
		$texte = array();

		// Wenn Daten aus Formular vorliegen, dann diese durchreichen (nach Gr��en-�nderung oder Fehler)
		if (!empty ($this->checked->galerie_texte)) {
			$texte = $this->checked->galerie_texte;
		}
		// sonst Daten aus Datenbank laden
		else {
			// aktive Sprachen raussuchen
			$sql = sprintf("SELECT lang_id, lang_long FROM %s WHERE more_lang='2' OR lang_short='%s'",
				$this->cms->papoo_name_language,
				$this->cms->frontend_lang
			);
			$aktive_sprachen = $this->db->get_results($sql);

			if (!empty($aktive_sprachen)) {
				foreach($aktive_sprachen as $sprache) {
					$sql = sprintf("SELECT gallang_name, gallang_beschreibung FROM %s WHERE gallang_lang_id='%d' AND gallang_gal_id='%d' LIMIT 1",
						$this->db_praefix."galerie_galerien_language",
						$sprache->lang_id,
						$galerie_id
					);
					$galerie_texte =  $this->db->get_results($sql);

					if (!empty($galerie_texte)) {
						$temp_texte = array("lang_id" => $sprache->lang_id,
							"sprache" => $sprache->lang_long,
							"name" => $galerie_texte[0]->gallang_name,
							"beschreibung" => $modus.$galerie_texte[0]->gallang_beschreibung,
						);
					}
					else {
						$temp_texte = array("lang_id" => $sprache->lang_id,
							"sprache" => $sprache->lang_long,
							"name" => $default_text,
							"beschreibung" => $modus.$default_text
						);
					}
					$texte[$sprache->lang_id] = $temp_texte;
				}
			}
		}
		return $texte;
	}

	/**
	 * Sprach-Texte (Name, Beschreibung) in Tabelle galerie_galerien_language eintragen
	 *
	 * @param int $galerie_id
	 * @param array $texte
	 */
	function galerien_texte_sichern($galerie_id = 0, $texte = array())
	{
		if (!empty($texte) && $galerie_id != 0) {
			// 1. alte Eintr�ge aus Tabelle galerie_galerien_language l�schen
			$sql = sprintf("DELETE FROM %s WHERE gallang_gal_id='%d'",
				$this->db_praefix."galerie_galerien_language",
				$galerie_id
			);
			$this->db->query($sql);

			// 2. neue Text in Tabelle galerie_galerien_language eintragen
			foreach($texte as $text) {
				$sql = sprintf("INSERT INTO %s SET gallang_gal_id='%d', gallang_lang_id='%d', gallang_name='%s', gallang_beschreibung='%s' ",
					$this->db_praefix."galerie_galerien_language",
					$galerie_id,
					$text['lang_id'],
					$this->db->escape($text['name']),
					$this->db->escape($text['beschreibung'])
				);
				$this->db->query($sql);
			}
		}
	}

	// ===========================================================
	//
	// Bilder Funktionen
	//
	// ===========================================================
	/**
	 * @param $galerie_id
	 * @return array
	 */
	function bilder_liste_komplett($galerie_id)
	{
		$sql = sprintf("SELECT `bild_datei` FROM %s WHERE bild_gal_id = %d",
			$this->cms->tbname["galerie_bilder"],
			(int)$galerie_id
		);
		$result = $this->db->get_results($sql, ARRAY_N);
		$bilder = array();
		if (is_array($result)) {
			foreach ($result as $row) {
				$bilder[] = $row[0];
			}
		}
		return $bilder;
	}

	/**
	 * Gibt eine Liste aller Bilder der Galerie $galerie_id zur�ck
	 *
	 * @param $galerie_id
	 * @param string $modus
	 *  <NIL>: Daten werden mit Bild-Texten ausgegeben
	 *  "NODECODE": Texten wird "nodecode:" vorangesetzt wegen Ausgabe per Javascript
	 *  "NOBR": Texten wird "nobr:" vorangesetzt wegen Ausgabe per Javascript
	 *  "KEIN_TEXT": Daten weden OHNE Bild-Texte ausgegeben
	 * @return array|bool|void
	 */
	function bilder_liste ($galerie_id, $modus = "")
	{
		switch ($modus) {
		case "KEIN_TEXT":
			$sql_add_1 = "";
			$sql_add_2 = "";
			break;

		case "":
		default:
			$sql_add_1 = sprintf(", %s AS t2 , %s AS t4", $this->db_praefix."galerie_bilder_language", $this->db_praefix."galerie_galerien_language");
			$sql_add_2 = sprintf("	AND t3.gal_id=t4.gallang_gal_id AND t4.gallang_lang_id='%d'
										AND t1.bild_id=t2.bildlang_bild_id AND t2.bildlang_lang_id='%d'",
				$this->cms->lang_id,
				$this->cms->lang_id
			);
			break;
		}

		$sql = sprintf("SELECT COUNT(*)
						FROM %s AS t1
						LEFT JOIN %s AS t2 ON t1.gal_id = t2.bild_gal_id
						WHERE t1.gal_id=%d
						ORDER BY t2.bild_nummer",
			$this->cms->tbname["galerie_galerien"],
			$this->cms->tbname["galerie_bilder"],
			(int)$galerie_id
		);
		$this->anzahlBilder = (int)$this->db->get_var($sql);

		IfNotSetNull($this->checked->galerie_id);
		// Paginating initialisieren
		if(defined("admin")) {
			$weiterTemplate = "galerie/templates/galerie_back.html&galerie_switch=GALERIE_DETAILS&galerie_id=".$this->checked->galerie_id;
		}
		else {
			$weiterTemplate = "galerie/templates/galerie_front.html&galerie_switch=GALERIE_DETAILS&galerie_id=".$this->checked->galerie_id;
		}
		$this->weiter->modlink = "no";
		$this->weiter->make_limit($this->cms->system_config_data['config_paginierung']);
		$this->weiter->result_anzahl = $this->anzahlBilder;
		$this->weiter->weiter_link = "plugin.php?menuid=" . $this->checked->menuid . "&amp;template=" . $weiterTemplate;
		$this->weiter->do_weiter( "teaser" );

		$sql = sprintf(	"SELECT * FROM %s AS t1, %s AS t3 %s
						WHERE t1.bild_gal_id='%d'
						AND t1.bild_gal_id=t3.gal_id
						%s
						ORDER BY t1.bild_nummer ASC %s",
			$this->db_praefix."galerie_bilder",
			$this->db_praefix."galerie_galerien",
			$sql_add_1,
			$galerie_id,
			$sql_add_2,
			(defined("admin") ? $this->weiter->sqllimit : "")
		);
		$bilder = $this->db->get_results($sql, ARRAY_A);

		if ($modus == "NODECODE" || $modus == "NOBR" || $modus == "DIMENSIONS") {
			$nodecode_felder = array("bildlang_name", "bildlang_beschreibung", "gallang_name", "gallang_beschreibung");
			$temp_bilder = $bilder;
			$bilder = array();
			if (!empty($temp_bilder)) {
				foreach ($temp_bilder as $bild) {
					$temp_bild = array();
					foreach ($bild as $bild_feld => $bild_wert) {
						if (in_array($bild_feld, $nodecode_felder)) {
							if ($modus == "NODECODE") {
								$temp_bild[$bild_feld] = $bild_wert;
								$temp_bild[$bild_feld."_nodec"] = "nodecode:".addslashes(str_replace (array(chr(10), chr(13)), array("_NEWLINE_", ""), $bild_wert));
							}
							if ($modus == "NOBR") {
								$temp_bild[$bild_feld] = "nobr:".strip_tags($bild_wert);
							}
						}
						else {
							$temp_bild[$bild_feld] = $bild_wert;
						}
					}
					if ($temp_bild["bild_breite_thumb"] > $this->max_thumb["breite"]) {
						$this->max_thumb["breite"] = $temp_bild["bild_breite_thumb"];
					}
					if ($temp_bild["bild_hoehe_thumb"] > $this->max_thumb["hoehe"]) {
						$this->max_thumb["hoehe"] = $temp_bild["bild_hoehe_thumb"];
					}
					$bilder[] = $temp_bild;
				}
			}
		}
		if (!empty($bilder)) {
			return $bilder;
		}
		else {
			return false;
		}
	}

	/**
	 * Gibt die Daten des Bildes $bild_id zur�ck
	 *
	 * @param $bild_id
	 * @param string $modus
	 *  <NIL>: Daten werden mit Bild-Texten ausgegeben
	 *  "KEIN_TEXT": Daten weden OHNE Bild-Texte ausgegeben
	 * @return bool
	 */
	function bilder_data ($bild_id, $modus = "")
	{
		switch ($modus) {
		case "KEIN_TEXT":
			$sql_add_1 = "";
			$sql_add_2 = "";
			break;

		case "":
		default:
			$sql_add_1 = sprintf("%s AS t2 ,", $this->db_praefix."galerie_bilder_language");
			$sql_add_2 = sprintf("AND t1.bild_id=t2.bildlang_bild_id AND t2.bildlang_lang_id='%d' ", $this->cms->lang_id);
			break;
		}
		$sql = sprintf(	"SELECT * FROM %s AS t1, %s %s AS t3 , %s AS t4
						WHERE t1.bild_id='%d'
						%s
						AND t1.bild_gal_id=t3.gal_id AND t3.gal_id=t4.gallang_gal_id AND t4.gallang_lang_id='%d'",
			$this->db_praefix."galerie_bilder",
			$sql_add_1,
			$this->db_praefix."galerie_galerien",
			$this->db_praefix."galerie_galerien_language",
			$bild_id,
			$sql_add_2,
			$this->cms->lang_id
		);
		$bild = $this->db->get_results($sql, ARRAY_A);

		if (!empty($bild)) {
			return $bild[0];
		}
		else {
			return false;
		}
	}

	/**
	 * galerie_class::get_galerie()
	 * Diese Funktion gibt den Datensatz der Galerie mit der ID $galerie_id zurück
	 * @param int $galerie_id
	 * @return array|bool Gibt die Galerie zurück, falls vorhanden, ansonsten false
	 */
	function get_galerie($galerie_id = 0)
	{
		if(isset($this->last_fetched_galerie) === false || $this->last_fetched_galerie["id"] != $galerie_id) {
			$sql = sprintf(	"SELECT * FROM %s WHERE gal_id=%d",
				$this->db_praefix."galerie_galerien",
				(int)$galerie_id
			);
			$galerie = $this->db->get_row($sql, ARRAY_A);
			$this->last_fetched_galerie = array(
				"id" => $galerie_id,
				"galerie" => $galerie
			);
		}
		return is_array($this->last_fetched_galerie["galerie"]) ? $this->last_fetched_galerie["galerie"] : false;
	}

	/**
	 * Speichert die Daten eines Bildes
	 *
	 * @param string $modus
	 * @param int $galerie_id
	 * @param string $bild
	 * @param int $bild_id
	 * @return bool|int|mixed
	 */
	function bilder_sichern($modus = "", $galerie_id = 0, $bild = "", $bild_id = 0)
	{
		// Allgemeine Informationen sammeln und Initialisieren
		$galerie = $this->get_galerie($galerie_id);
		if ($galerie === false) return false;

		$pfad_images = $this->cms->pfadhier."/plugins/galerie/galerien/".$galerie['gal_verzeichnis']."/";
		$pfad_thumbs = $pfad_images."thumbs/";

		$this->image_core->pfad_images = $pfad_images;
		$this->image_core->pfad_thumbs = $pfad_thumbs;
		$this->image_core->tumbnail_max_groesse = array("breite" => $this->einstellungen['galset_thumb_breite'], "hoehe" => $this->einstellungen['galset_thumb_breite']);

		if (empty($bild)) {
			$this->image_core->image_load($_FILES['galerie_bild']);
		}
		else {
			$this->image_core->image_load($bild);
		}

		$image_infos = $this->image_core->image_infos;

		// Test: Bild-Typ (JPG, GIF, PNG)
		if (@!$image_infos['type']) {
			@unlink ($image_infos['bild_temp']);
			return false;
		}

		// Test: Datei existiert schon (nur wenn kein Bild �bergeben wurde)
		if (empty($bild) && file_exists($this->image_core->pfad_images.$image_infos['name'])) {
			unlink ($image_infos['bild_temp']);
			return false;
		}

		// Bild sichern
		$dateiname = $this->image_core->pfad_images.$image_infos['name'];
		$image = $this->image_core->image_create($image_infos['bild_temp']);
		if (empty($bild)) $this->image_core->image_save($image_infos['bild_temp'], $dateiname);

		// ThumbNail erzeugen und sichern
		$dateiname_thumbnail = $this->image_core->pfad_thumbs.$image_infos['name'];
		$dimension = $this->image_core->image_get_thumbnail_size($image_infos['breite'], $image_infos['hoehe']);
		$thumbnail = $this->image_core->image_create(array($dimension['breite'], $dimension['hoehe']));
		//imagecopyresized($thumbnail, $image, 0, 0, 0, 0, $dimension['breite'], $dimension['hoehe'], $image_infos['breite'], $image_infos['hoehe']);
		imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $dimension['breite'], $dimension['hoehe'], $image_infos['breite'], $image_infos['hoehe']);
		$this->image_core->image_save($thumbnail, $dateiname_thumbnail);

		// tempor�re Daten l�schen und image_core r�cksetzen
		ImageDestroy($image);
		ImageDestroy($thumbnail);
		if (empty($bild)) {
			unlink($image_infos['bild_temp']);
		}
		$this->image_core->init();

		if ($modus == "NEU") {
			// Bild in Datenbank eintragen
			$sql = sprintf("INSERT INTO %s
							SET bild_datei='%s', bild_gal_id='%d', bild_nummer='%d', bild_format='%s',
							bild_breite='%d', bild_breite_thumb='%d', bild_hoehe='%d', bild_hoehe_thumb='%d'",
				$this->db_praefix."galerie_bilder",
				$this->db->escape($image_infos['name']),
				$galerie_id,
				(isset($this->nextBildNummer) ? $this->nextBildNummer++ : $galerie['gal_bilderanzahl'] + 1),
				$image_infos['type'],
				$image_infos['breite'],
				$dimension['breite'],
				$image_infos['hoehe'],
				$dimension['hoehe']
			);
			$this->db->query($sql);
			$bild_id = $this->db->insert_id;

			// Anzahl Bilder in Galerie-Tabelle um eins erh�hen
			$sql = sprintf("UPDATE %s SET gal_bilderanzahl=gal_bilderanzahl+1 WHERE gal_id='%d'",
				$this->db_praefix."galerie_galerien",
				$galerie['gal_id']
			);
			$this->db->query($sql);
		}
		elseif ($bild_id) {
			// ge�nderte Bild-Gr��en in Datenbank eintragen
			$sql = sprintf("UPDATE %s
							SET bild_breite='%d', bild_breite_thumb='%d', bild_hoehe='%d', bild_hoehe_thumb='%d'
							WHERE bild_id='%d'",
				$this->db_praefix."galerie_bilder",
				$image_infos['breite'],
				$dimension['breite'],
				$image_infos['hoehe'],
				$dimension['hoehe'],
				$bild_id
			);
			$this->db->query($sql);
		}

		return $bild_id;
	}

	/**
	 * L�scht das Bild $bild_id
	 *
	 * @param int $bild_id
	 * @return bool
	 */
	function bilder_loeschen($bild_id = -1)
	{
		// Daten sammeln und initialisieren
		$galerie = $this->galerien_data($this->checked->galerie_id);
		$bild = $this->bilder_data($bild_id);

		$datei_bild = $this->cms->pfadhier."/plugins/galerie/galerien/".$galerie['gal_verzeichnis']."/".$bild['bild_datei'];
		$datei_thumb = $this->cms->pfadhier."/plugins/galerie/galerien/".$galerie['gal_verzeichnis']."/thumbs/".$bild['bild_datei'];

		// Dateien l�schen
		if (file_exists($datei_bild) && file_exists($datei_thumb)) {
			@unlink($datei_bild);
			@unlink($datei_thumb);
		}

		// Eintr�ge in DB l�schen
		$sql = sprintf("DELETE FROM %s WHERE bild_id='%d'",
			$this->db_praefix."galerie_bilder",
			$bild_id
		);
		$this->db->query($sql);

		$sql = sprintf("DELETE FROM %s WHERE bildlang_bild_id='%d'",
			$this->db_praefix."galerie_bilder_language",
			$bild_id
		);
		$this->db->query($sql);

		// Anzahl Bilder in Galerie-Tabelle um eins reduzieren
		$sql = sprintf("UPDATE %s SET gal_bilderanzahl=gal_bilderanzahl-1 WHERE gal_id='%d'",
			$this->db_praefix."galerie_galerien",
			$galerie['gal_id']
		);
		$this->db->query($sql);

		// Pr�fung ob Galerie_Bild gel�scht wurde.
		// Wenn ja, dann wird dass Galerie-Bild auf 1 gesetzt
		if ($galerie['gal_bild_id'] == $bild_id) {
			$sql = sprintf("UPDATE %s SET gal_bild_id=1 WHERE gal_id='%d'",
				$this->db_praefix."galerie_galerien",
				$galerie['gal_id']
			);
			$this->db->query($sql);
		}

		// Pr�fung ob letztes Bild aus Galerie gel�scht wurde.
		// Wenn ja, dann wird die Galerie deaktiviert
		if ($galerie['gal_bilderanzahl'] <= 1) {
			$sql = sprintf("UPDATE %s SET gal_aktiv_janein=0 WHERE gal_id='%d'",
				$this->db_praefix."galerie_galerien",
				$galerie['gal_id']
			);
			$this->db->query($sql);
		}

		// Bilder neu durchnummerieren
		$this->bilder_nummer_aktualisieren($galerie['gal_id']);
		return true;
	}

	/**
	 * Nummeriert die Bilder der Galerie $galerie_id neu durch
	 * - nach L�schen eines Bildes
	 *
	 * @param int $galerie_id
	 */
	function bilder_nummer_aktualisieren($galerie_id = 0)
	{
		$sql = sprintf("SELECT * FROM %s WHERE bild_gal_id='%d' ORDER BY bild_nummer ASC",
			$this->db_praefix."galerie_bilder",
			(int)$galerie_id
		);
		$bilder = $this->db->get_results($sql);

		if (!empty($bilder)) {
			$counter = 1;
			foreach($bilder AS $bild) {
				$sql = sprintf("UPDATE %s SET bild_nummer='%d' WHERE bild_id='%d'",
					$this->db_praefix."galerie_bilder",
					$counter,
					$bild->bild_id
				);
				$this->db->query($sql);

				$counter += 1;
			}
		}
	}

	/**
	 * @param int $galerie_id
	 * @param array $order_ids
	 */
	function bilder_reorder($galerie_id = 0, $order_ids = array())
	{
		if (empty($order_ids) || is_array($order_ids) == false) {
			return;
		}

		$galleryId = (int)$galerie_id;

		$orderMap = [];
		foreach ($order_ids as $imageId => $orderId) {
			$orderMap[(int)$imageId] = (int)$orderId;
		}

		$sql = "SELECT * FROM {$this->cms->tbname['galerie_bilder']} WHERE bild_gal_id = {$galleryId}";
		$images = array_map(function ($row) use ($orderMap) {
			$imageId = (int)$row['bild_id'];
			$orderId = $orderMap[$imageId] ?? (int)$row['bild_nummer'] * 10;

			return [
				'id' => $imageId,
				'orderId' => $orderId,
			];
		}, $this->db->get_results($sql, ARRAY_A));

		foreach ($images as $image) {
			$this->db->query(
				"UPDATE {$this->cms->tbname['galerie_bilder']} ".
				"SET bild_nummer = {$image['orderId']} ".
				"WHERE bild_id = {$image['id']}"
			);
		}
	}

	/**
	 * L�d die Texte eines Bildes (Name, Beschreibung)
	 * entweder aus den �bertragenen Formular-Daten (bei Fehl-Eingaben im Formular)
	 * oder aus der Tabelle galerien_bilder_language (zum Editieren)
	 *
	 * @param int $bild_id
	 * @param string $default_text
	 * @return array
	 */
	function bilder_texte_laden($bild_id = 0, $default_text = "")
	{
		$texte = array();

		// Wenn Daten aus Formular vorliegen, dann diese durchreichen (nach Gr��en-�nderung oder Fehler)
		if (!empty ($this->checked->galerie_bild_texte)) {
			$texte = $this->checked->galerie_bild_texte;
		}
		// sonst Daten aus Datenbank laden
		else {
			// aktive Sprachen raussuchen
			$sql = sprintf("SELECT lang_id, lang_long FROM %s WHERE more_lang='2' OR lang_short='%s'",
				$this->cms->papoo_name_language,
				$this->cms->frontend_lang
			);
			$aktive_sprachen = $this->db->get_results($sql);

			if (!empty($aktive_sprachen)) {
				foreach($aktive_sprachen as $sprache) {
					$sql = sprintf("SELECT bildlang_name, bildlang_beschreibung FROM %s WHERE bildlang_lang_id='%d' AND bildlang_bild_id='%d' LIMIT 1",
						$this->db_praefix."galerie_bilder_language",
						$sprache->lang_id,
						$bild_id
					);
					$bild_texte =  $this->db->get_results($sql);

					if (!empty($bild_texte)) {
						$temp_texte = array("lang_id" => $sprache->lang_id,
							"sprache" => $sprache->lang_long,
							"name" => $bild_texte[0]->bildlang_name,
							"beschreibung" => "nobr:".$bild_texte[0]->bildlang_beschreibung,
						);
					}
					else {
						$temp_texte = array("lang_id" => $sprache->lang_id,
							"sprache" => $sprache->lang_long,
							"name" => $default_text,
							"beschreibung" => $default_text
						);
					}
					$texte[$sprache->lang_id] = $temp_texte;
				}
			}
		}

		return $texte;
	}

	/**
	 * Sprach-Texte (Name, Beschreibung) in Tabelle galerie_bilder_language eintragen
	 *
	 * @param int $bild_id
	 * @param array $texte
	 * @return bool
	 */
	function bilder_texte_sichern($bild_id = 0, $texte = array())
	{
		if (!empty($texte) && $bild_id != 0) {
			// 1. alte Eintr�ge aus Tabelle galerie_bilder_language l�schen
			$sql = sprintf("DELETE FROM %s WHERE bildlang_bild_id='%d'",
				$this->db_praefix."galerie_bilder_language",
				$bild_id
			);
			$this->db->query($sql);

			// 2. neue Text in Tabelle galerie_galerien_language eintragen
			foreach($texte as $text) {
				if ($this->checked->galerie_switch == "GALERIE") {
					$beschreibung = "";
				}
				else {
					$beschreibung = $text['beschreibung'];
				}
				$sql = sprintf("INSERT INTO %s SET bildlang_bild_id='%d', bildlang_lang_id='%d', bildlang_name='%s', bildlang_beschreibung='%s' ",
					$this->db_praefix."galerie_bilder_language",
					$bild_id,
					$text['lang_id'],
					$this->db->escape($text['name']),
					$this->db->escape($beschreibung)
				);
				$this->db->query($sql);
			}
		}
		return true;
	}


	// ===========================================================
	//
	// Werkzeuge (Tools) Funktionen
	//
	// ===========================================================

	/**
	 * Diese Funktion scannt alle Verzeichnisse im Verzeichnis "galerien" und vergleicht sie mit den bestehenden Galerien.
	 * Gibt eine Array mit allen "neuen" Verzeichnissen zur�ck, also solchen, die noch keine Galerien sind.
	 * Die neuen Verzeichnisse m�ssen auch ein Unterverzeichnis "thumbs" beinhalten.
	 *
	 * @return array|bool
	 */
	function tools_galerien_liste()
	{
		$neue_galerien = array();

		// Verzeichnisse im Verzeichnis "galerien" ermitteln
		$verzeichnisse = array();
		$pfad = $this->cms->pfadhier."/plugins/galerie/galerien/";

		$handle = opendir($pfad);
		while (($file = readdir($handle)) !==false) {
			if(is_dir($pfad.$file)) {
				if(strpos("XXX".$file, ".") != 3) {
					$verzeichnisse[] = $file;
				}
			} // Nur nicht-unsichtbare Verzeichnisse aufnehmen
		}

		// bestehende Galerie-Verzeichnisse ermitteln
		$sql = sprintf(	"SELECT gal_verzeichnis FROM %s ",
			$this->db_praefix."galerie_galerien"
		);
		$galerien = $this->db->get_col($sql);
		if (empty($galerien)) {
			$galerien = array();
		}

		// Vergleich der beiden Arrays $verzeichnisse und $galerien
		$temp_galerien = array();
		if (!empty($verzeichnisse)) {
			foreach ($verzeichnisse as $verzeichnis) {
				if (!in_array($verzeichnis, $galerien)) {
					$temp_galerien[] = $verzeichnis;
				}
			}
		}

		// Pr�fung ob thumbs-Verzeichnis vorhanden ist und gen�gend Rechte bestehen
		if (!empty($temp_galerien)) {
			$i = 0;
			$j = 0;
			foreach ($temp_galerien as $temp_galerie) {
				$pfad = PAPOO_ABS_PFAD . '/plugins/galerie/galerien/' . $temp_galerie;
				$pfad=str_replace("//","/",$pfad);
				// Zugriffsrechte f�r Galerieverzeichnis nicht ausreichend
				if ($this->oktale_zugriffsrechte($pfad) < 777) {
					if (!(@chmod($pfad, 0777))) {
						$this->content->template['zugriffsrechte_einzelgalerie_nicht_ok'][$i] = '/plugins/galerie/galerien/' .$temp_galerie;
						$i++;
					}
				}
				// Zugriffsrechte f�r Galerieverzeichnis ausreichend
				else {
					// Thumbverzeichnis noch nicht vorhanden
					if (!is_dir($pfad . '/thumbs/')) {
						mkdir ($pfad . '/thumbs/');
						@chmod($pfad . '/thumbs/', 0777);
					}
					// Thumbverzeichnis bereits vorhanden
					else {
						@chmod($pfad . '/thumbs/', 0777);
					}

					// Thumbnailverzeichnis vorhanden, aber keine ausreichenden Zugriffsrechte
					if (is_dir($pfad . '/thumbs/') && $this->oktale_zugriffsrechte($pfad . '/thumbs/') < 777) {
						$this->content->template['zugriffsrechte_thumbnailverzeichnis_nicht_ok'][$j] = '/plugins/galerie/galerien/' .$temp_galerie . '/thumbs/';
						$j++;
					}
				}
				// Thumbnailverzeichnis vorhanden, Zugriffsrechte f�r Galerie- und Thumbnailverzeichnis ausreichend -> alles o.k.
				if (is_dir($pfad."/thumbs/") && ($this->oktale_zugriffsrechte($pfad) >= 777) && ($this->oktale_zugriffsrechte($pfad.'/thumbs/') >= 777)) {
					$neue_galerien[]['name'] = $temp_galerie;
				}
			}
		}

		// R�ckgabe
		if (!empty($neue_galerien)) {
			return $neue_galerien;
		}
		else {
			return false;
		}
	}

	/**
	 * Erzeugt ein Array mit allen Dateinamen des Verzeichnisses $verzeichnis
	 *
	 * @param string $verzeichnis
	 * @return array
	 */
	function tools_bilder_liste($verzeichnis = "")
	{
		$bilder = array();

		$pfad = $this->cms->pfadhier."/plugins/galerie/galerien/".$verzeichnis."/";
		$handle = opendir($pfad);
		while (($file = readdir($handle)) !==false) {
			if(!is_dir($pfad.$file)) {
				if(strpos("XXX".$file, ".") != 3) {
					$bilder[] = $file;
				}
			} // Nur nicht-unsichtbare Dateien aufnehmen
		}
		natcasesort($bilder);

		return $bilder;
	}

	/**
	 * Erzeugt aus einem Datei-Namen $name der Form "xxx <Name ..>.ext" einen "sinnvollen Namen",
	 * also z.B. aus "12 Hallo Welt.gif" wird "Hallo Welt"
	 *
	 * @param string $name
	 * @return mixed|string
	 */
	function tools_bilder_name_anpassen($name = "")
	{
		$temp_name = "";

		// dieses Array wird von preg_match_all gef�llt.
		$text_array = array();

		// Suchmuster: [0..9] (Bildname).ext
		$match_expression = "/(^[0-9]{1,}) (.*).([a-zA-Z]{3,4}$)/i";
		// f�llt $text_array in der Art:
		//		$text_array[0] = Originalname
		//		$text_array[1] = Bild-Nummer
		//		$text_array[2] = Neuer Name
		//		$text_array[3] = Datei-Extension
		preg_match_all ($match_expression, $name, $text_array, PREG_SET_ORDER);

		if (!empty($text_array)) {
			$temp_name = $text_array[0][2];
		}

		if (!empty($temp_name)) {
			return $temp_name;
		}
		else {
			return $name;
		}
	}

	/**
	 * Funktion um die Ligthbox Galerie einzubinden
	 *
	 * @param mixed $modus
	 */
	function tools_make_lightbox()
	{

		$headerdat=array();
		/*
		//n�tige JS Datein in den Kopf laden
		$headerdat[]='<link rel="alternate" href="'.PAPOO_WEB_PFAD.'/plugin.php?template=galerie/templates/galerie_rss.utf8.html&galerie_switch=GALERIE_DIASHOW&galerie_id='.$this->checked->galerie_id.'" type="application/rss+xml" title="" id="gallery" />';
		$headerdat[]="nobr:".'
			<script type="text/javascript" >
				var loadingImage = "'.PAPOO_WEB_PFAD.'/bilder/loading.gif";
				var closeButton = "'.PAPOO_WEB_PFAD.'/bilder/close.gif";
				var fileLoadingImage = "'.PAPOO_WEB_PFAD.'/plugins/galerie/images/loading.gif";
				var fileBottomNavCloseImage = "'.PAPOO_WEB_PFAD.'/plugins/galerie/images/close.gif";
			</script>
			';

		$headerdat[]='<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/plugins/galerie/js/prototype.js"></script>';
		$headerdat[]='<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/plugins/galerie/js/scriptaculous.js?load=effects"></script>';

		$headerdat[]='<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/plugins/galerie/js/lightbox.js"></script>';
		$headerdat[]='<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/js/lightbox_localisation.js"></script>';
		*/
		//n�tige CSS Dateien
		$headerdat[]='<link rel="stylesheet" href="'.PAPOO_WEB_PFAD.'/plugins/galerie/css/lightbox.css" type="text/css" media="screen" />';
		$headerdat[]="nobr:".
			'<!--[if lte IE 7]>
				<link rel="stylesheet" href="'.PAPOO_WEB_PFAD.'/plugins/galerie/css/lightbox_ie.css" type="text/css" media="screen" />
			<![endif]-->';
		$headerdat[]="nobr:".
			'<style type="text/css">
				#prevLink, #nextLink{background: transparent url('.PAPOO_WEB_PFAD.'/plugins/galerie/images/blank.gif) no-repeat; /* Trick IE into showing hover */}
				#prevLink:hover, #prevLink:visited:hover { background: url('.PAPOO_WEB_PFAD.'/plugins/galerie/images/prev.gif) left 15% no-repeat; }
				#nextLink:hover, #nextLink:visited:hover { background: url('.PAPOO_WEB_PFAD.'/plugins/galerie/images/next.gif) right 15% no-repeat; }
			</style>';

		$this->content->template['plugin_header'] = $headerdat;
	}

	/**
	 * @param $gal_dir
	 */
	private function tools_store_multi_upload($gal_dir)
	{
		$src = PAPOO_ABS_PFAD . "/interna/templates_c/upload/";
		$dest = PAPOO_ABS_PFAD . "/plugins/galerie/galerien/" . $gal_dir . "/";
		$dir = opendir($src);
		while(($el = readdir($dir)) !== false) {
			if (is_file($src.$el) && preg_match("/\\.(jpg|jpeg|png|gif)$/i", $el) == 1) {
				copy($src.$el, $dest.$el);
				unlink($src.$el);
			}
		}
	}

	// ===========================================================
	//
	// Einstellungen Funktionen
	//
	// ===========================================================
	/**
	 * L�d die Einstellungen des Plugins aus der Tabelle galerien_einstellungen und speichert diese in $this->einstellungen
	 */
	function einstellungen_laden()
	{
		$sql = sprintf(	"SELECT * FROM %s ",
			$this->db_praefix."galerie_einstellungen"
		);
		$einstellungen = $this->db->get_results($sql, ARRAY_A);

		if (!empty($einstellungen)) {
			$this->einstellungen = $einstellungen[0];
		}
	}

	/**
	 * Sichert die Einstellungen des Plugins in der Tabelle galerien_einstellungen und l�d die neuen Einstellungen.
	 *
	 * @param string $modus
	 * @return bool
	 */
	function einstellungen_sichern($modus = "")
	{
		switch ($modus) {
		case "THUMBS":
			$breite = $this->checked->galerie_thumb_breite;
			if ($breite < 50) {
				$breite = 50;
			}
			if ($breite > 800) {
				$breite =800;
			}

			$hoehe = $this->checked->galerie_thumb_hoehe;
			if ($hoehe < 50) {
				$hoehe = 50;
			}
			if ($hoehe > 800) {
				$hoehe = 800;
			}

			$sql = sprintf(	"UPDATE %s SET galset_thumb_breite='%d', galset_thumb_hoehe='%d'",
				$this->db_praefix."galerie_einstellungen",
				$breite,
				$hoehe
			);
			break;
			/*
			case "DIASHOW":
				$sql = sprintf(	"UPDATE %s SET galset_diashow_id='%d'",
								$this->db_praefix."galerie_einstellungen",
								$this->checked->galerie_diashow_thema
								);
				break;
			*/
		case "STANDZEIT":
			$sql = sprintf(	"UPDATE %s SET galset_diashow_timeout='%d'",
				$this->db_praefix."galerie_einstellungen",
				$this->checked->galerie_standzeit
			);
			break;
			//LIGHTBOX
		case "LIGHTBOX":
			IfNotSetNull($this->checked->galset_gps_view);
			IfNotSetNull($this->checked->galset_diashow_window);
			IfNotSetNull($this->checked->galset_diashow);
			IfNotSetNull($this->checked->galset_lightbox);
			$sql = sprintf("UPDATE %s SET galset_lightbox='%d', galset_diashow='%d', galset_diashow_window='%d', galset_gps_view='%d'",
				$this->db_praefix."galerie_einstellungen",
				$this->checked->galset_lightbox,
				$this->checked->galset_diashow,
				$this->checked->galset_diashow_window,
				$this->checked->galset_gps_view);
			break;
		default: return false;
		}
		$this->db->query($sql);

		$this->einstellungen_laden();

		return true;
	}

	/**
	 * Diese Funktion erstellt ALLE thumbnails neu mit den max. Gr��en aus den Galerie-Enstellungen.
	 *
	 * @return bool
	 */
	function einstellungen_thumbs_edit()
	{
		$fehler = false;

		// Liste aller Galerien ermitteln
		$galerien = $this->galerien_liste("ALL");
		if (!empty($galerien)) {
			foreach($galerien as $galerie) {
				// Liste aller Bilder dieser Galerie ermitteln
				$bilder = $this->bilder_liste($galerie['gal_id'], "KEIN_TEXT");
				if (!empty($bilder)) {
					foreach($bilder as $bild) {
						// altes Thumbnail l�schen
						$thumbnail = $this->cms->pfadhier."/plugins/galerie/galerien/".$galerie['gal_verzeichnis']."/thumbs/".$bild['bild_datei'];
						unlink($thumbnail);
						if (!$this->bilder_sichern("", $galerie['gal_id'], $bild['bild_datei'], $bild['bild_id'])) {
							echo "Fehler mit Bild ".$bild['bild_datei']." in Galerie ".$galerie['gal_verzeichnis']."<br />\n";
							$fehler = true;
						}
					}
				}
			}
		}
		if (!$fehler) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * �ndert die Standzeit (timeout) von Bildern f�r die Dia-Show
	 *
	 * @param int $standzeit
	 * @param string $modus
	 *  "ALLE": �ndert ALLE Bilder
	 *  "GALERIE": �ndert aller Bilder der Galerie $id
	 *  "BILD": �ndert das Bild mit der ID $id
	 * @param int $id
	 * @return bool|void
	 */
	function einstellungen_standzeit_edit($standzeit = 5, $modus = "", $id = 0)
	{
		switch ($modus) {
		case "ALLE":
			$sql = sprintf(	"UPDATE %s SET bild_diashow_timeout='%d'",
				$this->db_praefix."galerie_bilder",
				$standzeit
			);
			break;

		case "GALERIE":
			$sql = sprintf(	"UPDATE %s SET bild_diashow_timeout='%d' WHERE bild_gal_id='%d'",
				$this->db_praefix."galerie_bilder",
				$standzeit,
				$id
			);
			break;

		case "BILD":
			$sql = sprintf(	"UPDATE %s SET bild_diashow_timeout='%d' WHERE bild_id='%d'",
				$this->db_praefix."galerie_bilder",
				$standzeit,
				$id
			);
			break;

		case "":
		default: return false;
		}
		$this->db->query($sql);
	}

	function output_filter()
	{
		if (!defined("admin")) {
			//Inhalte durchgehen und ersetzen
			$this->filter_ausgabe_inhalt();
		}
	}

	function filter_ausgabe_inhalt()
	{
		global $output;

		if (strstr($output, "insert_gal")) {
			// Die Galerie rausholen und erzeugen
			$this->get_galerie_und_erzeuge_html($output);
		}
	}

	/**
	 * get_galerie_und_erzeuge_html()
	 * Diese Funktion holt die Daten aus dem Text raus und erzeugt
	 * anhand der ids die Barack Galerie mit lightbox links
	 *
	 * @param string $inhalt
	 * @return void
	 */
	function get_galerie_und_erzeuge_html(&$inhalt = "")
	{
		$galpfad = PAPOO_WEB_PFAD . "/plugins/galerie/";
		$galerie_html_top_einbindung_js_css = '<script type="text/javascript" charset="utf-8" src="' . $galpfad;
		$galerie_html_top_einbindung_js_css .= '/js/mootools-1.2-core.js"></script><script type="text/javascript" charset="utf-8" src="';
		$galerie_html_top_einbindung_js_css .= $galpfad . '/js/mootools-1.2-more.js"></script><script type="text/javascript" charset="utf-8" src="';
		$galerie_html_top_einbindung_js_css .= $galpfad . '/js/morphlist.js"></script><script type="text/javascript" charset="utf-8" src="' . $galpfad . '/js/barackslideshow.js"></script>';

		// Ersetze nur innerhalb des body-Tags
		$offset = strpos($inhalt, "<body");

		$css_lightbox = false;
		$css_slider = false;

		// Galerien rausholen #Content_Manipulator_Style#
		preg_match_all('/#insert_gal_(\d+)(?:_(\d+))?#/i', $inhalt, $matches, PREG_SET_ORDER, $offset);
		if(is_array($matches)) {
			for ($i = 0; $i < count($matches); $i++) {
				$replace = $matches[$i][0];
				$gal_id = $matches[$i][1];
				$gal_art_id = count($matches[$i]) == 2 ? 1 : (int)$matches[$i][2];

				$gal_width = "320px";
				$gal_width2 = str_replace("px","",$gal_width)+70;

				$gal_height = "320px";
				$gal_height2 = str_replace("px","",$gal_width)/2;

				$bilder_liste = $this->bilder_liste($gal_id);

				// Standard Lightbox Ansicht
				if ($gal_art_id == 1) {
					$bilder = $this->create_lightbox_gal($bilder_liste);
					if ($css_lightbox === false) {
						$inhalt = str_replace('</head>', '<link rel="stylesheet" href="'.PAPOO_WEB_PFAD.'/plugins/galerie/css/lightbox.css" type="text/css" media="screen" />
							</head>', $inhalt);
						$css_lightbox = true;
					}
				}
				// Filmstrip Ansicht mit Galleryview
				else if ($gal_art_id == 2) {
					$bilder = $this->create_lightbox_filmstrip($bilder_liste);
					if ($css_slider === false) {
						$inhalt = str_replace('</head>','
								<link rel="stylesheet" href="'.PAPOO_WEB_PFAD.'/plugins/galerie/css/screen.css" type="text/css" media="screen" />
								<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/plugins/galerie/js/easySlider.js"></script>
								<script type="text/javascript">
									$(document).ready(function(){
										$("#slider").easySlider({
											auto: true,
											continuous: true ,
											firstText: \'First\'
										});
									});
								</script>
								<style type="text/css">
									#slider li{
										width:'.$gal_width.';
										height:'.$gal_height.';
									}
									#nextBtn{
										left:'.$gal_width2.'px;
									}
									#prevBtn, #nextBtn{
										top:'.$gal_height2.'px;
									}
									#slider { }
								</style>
							</head>', $inhalt);
						$css_slider = true;
					}
				}
				$inhalt = substr_replace($inhalt, $bilder, strpos($inhalt, $replace, $offset), strlen($replace));
			}
		}

		// Galerien rausholen (Fallback für alte Methode)
		preg_match_all('/<div id="gal-(\d+?)"[^>]*rel="(\d+?)"[^>]*style="[^"]*width:\s*(\d+)[^"]*height:\s*(\d+).*?(?=<\/div>)<\/div>/s', $inhalt, $matches, PREG_SET_ORDER, $offset);
		if (is_array($matches)) {
			for ($i = 0; $i < count($matches); $i++) {

				$replace = $matches[$i][0];
				$gal_id = (int)$matches[$i][1];
				$gal_art_id = (int)$matches[$i][2];
				$gal_width = (int)$matches[$i][3];
				$gal_height = (int)$matches[$i][4];

				//Bilder holen
				$bilder_liste = $this->bilder_liste($gal_id);

				//Standard Lightbox Ansicht
				if ($gal_art_id == 1) {
					$bilder = $this->create_lightbox_gal($bilder_liste);
					if ($css_lightbox === false) {
						$inhalt = str_replace('</head>', '<link rel="stylesheet" href="'.PAPOO_WEB_PFAD.'/plugins/galerie/css/lightbox.css" type="text/css" media="screen" />
						</head>', $inhalt);
						$css_lightbox = true;
					}
				}
				//Filmstrip Ansicht mit Galleryview
				else if ($gal_art_id == 2) {
					$bilder=$this->create_lightbox_filmstrip($bilder_liste);
					if ($css_slider === false) {
						$inhalt = str_replace('</head>', '
								<link rel="stylesheet" href="'.PAPOO_WEB_PFAD.'/plugins/galerie/css/screen.css" type="text/css" media="screen" />
								<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/plugins/galerie/js/easySlider.js"></script>
								<script type="text/javascript">
									$(document).ready(function(){
										$("#slider").easySlider({
											auto: true,
											continuous: true ,
											firstText: \'First\'
										});
									});
								</script>
								<style type="text/css">
									#slider li{
										width:'.$gal_width.'px;
										height:'.$gal_height.'px;
									}
									#nextBtn{
										left:'.($gal_width+30).'px;
									}
									#prevBtn, #nextBtn{
										top:'.($gal_height/2).'px;
									}
									#slider { }
								</style>
							</head>', $inhalt);
						$css_slider = true;
					}
				}
				$inhalt = substr_replace($inhalt, $bilder, strpos($inhalt, $replace, $offset), strlen($replace));
			}
		}
	}


	/**
	 * @param string $name
	 * @return bool|string Das eingelesene Template, wenn es im Style- oder Pluginverzeichnis gefunden wird, ansonsten false
	 * @author Christoph Zimmer
	 */
	private function findTemplate($name) {
		$filename = "plugins/".pathinfo(realpath(__DIR__."/.."), PATHINFO_FILENAME)."/templates/$name";

		return array_reduce([
			PAPOO_ABS_PFAD."/styles/{$this->cms->style_dir}/templates/$filename",
			PAPOO_ABS_PFAD."/$filename"
		], function ($template, $filename) {
			return $template !== false ? $template : (is_file($filename) ? $filename : $template);
		}, false);
	}

	/**
	 * @param $name
	 * @param array $data Key-/Value-Paare, die in dem Spracheintrag ersetzt werden. ["name" => "Christoph"] wandelt "Hallo :name!" in "Hallo Christoph!" um.
	 * @return mixed Der fertig ausgewertete Spracheintrag mit allen Ersetzungen aus $data
	 * @author Christoph Zimmer
	 */
	private function loadMessage($name, $data) {
		$message = $this->content->template["plugin"]["gallery"][$name];
		foreach ((array)$data as $key => $value) {
			$message = preg_replace_callback('~(?<=^| ):(?<key>[a-z_]+)\b~', function ($match) use ($data) {
				return isset($data[$match["key"]]) ? $data[$match["key"]] : $data[0];
			}, $message);
		}
		return $message;
	}

	/**
	 * galerie_class::create_lightbox_gal()
	 *
	 * @param mixed $bilder_liste
	 * @return bool|false|mixed|string|void
	 */
	function create_lightbox_gal($bilder_liste)
	{
		global $smarty;
		$templateFile = "front/gallery.html";

		if (($template = $this->findTemplate($templateFile)) === false) {
			return "<strong>{$this->loadMessage("template_not_found", ["template" => $templateFile])}</strong>";
		}

		$smarty->assign("galleriesPath", PAPOO_WEB_PFAD."/plugins/".pathinfo(realpath(__DIR__."/.."), PATHINFO_FILENAME)."/galerien/");
		$smarty->assign("gallery", (array)$bilder_liste);

		return $smarty->fetch($template);
	}

	/**
	 * galerie_class::create_lightbox_filmstrip()
	 *
	 * @param mixed $bilder_liste
	 * @return string
	 */
	function create_lightbox_filmstrip($bilder_liste)
	{
		if (is_array($bilder_liste)) {
			foreach ($bilder_liste as $key=>$bild) {
				//<span>'.$this->diverse->encode_quote($bild['bildlang_beschreibung']).'</span>
				$pictures[] =
					'<li><div class="slider_img"><img src="' .
					PAPOO_WEB_PFAD . '/plugins/galerie/galerien/' .
					$bild['gal_verzeichnis'] . '/' .$bild['bild_datei'] .
					'" alt="' . $this->diverse->encode_quote($bild['bildlang_beschreibung']) .
					'" title="' . $this->diverse->encode_quote($bild['bildlang_beschreibung']) .
					'" width="' . $bild['bild_breite'] .
					'" height="' . $bild['bild_hoehe'] .
					'" /></div><div class="overlay_slider">' . $this->diverse->encode_quote($bild['bildlang_beschreibung']) . '</div></li>';
			}

		}
		$bilder='<div id="slider"><ul>'.implode(" ",$pictures).'</ul></div><br style="clear: both;">';

		return $bilder;
	}

	/**
	 * Neue Kategorie anlegen (Backend)
	 * template cat_back_new.html
	 */
	function new_category()
	{
		$this->content->template['anzahl'] = $this->read_count_all_categories("", 1); // Gesamtanzahl aller Kategorien
		// 1. Aufruf (Start).
		if (empty($this->checked->submit)) {
			$_SESSION['galerie']['template_data'] = "";
		} //  Alle Kategoriedaten holen
		// Alles ok - speichern in die DB
		else {
			// Falls kein Kategoriename angegeben wurde:
			if (empty($this->checked->cat_new_name_name) AND $this->checked->submit == $this->content->template['plugin']['galerie_back']['submit']['cat_new']) {
				// Fehler (Kategoriename nicht angegeben). Eingabe-Daten vorerst ungepr�ft wiederherstellen
				$this->content->template['fehler1'] = $fehler = 1;
				#$this->fetchAllCategories($this->checked->cat_new_sel_id, "");
			}
			if (!isset($fehler) || isset($fehler) && !$fehler) {
				// H�chste vorhandene order_id ermitteln und mit +10 addiert speichern
				$sql = sprintf("INSERT INTO %s
								SET parent_id = 0,
									gal_order_id = '%d'",
					$this->cms->tbname['galerie_galerien'],
					$this->db->escape($this->getNextOrderId())
				);
				$this->db->query($sql);
				$cat_id = $this->db->insert_id;
				$lang_id = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
				$sql = sprintf("INSERT INTO %s
								SET gallang_gal_id = '%d',
									gallang_lang_id = '%d',
									gallang_name = '%s',
									gallang_beschreibung = '%s'",
					$this->cms->tbname['galerie_galerien_language'],
					$cat_id,
					$lang_id,
					$this->db->escape($this->checked->cat_new_name_name),
					$this->db->escape($this->checked->cat_new_descript_name)
				);
				$this->db->query($sql);
				#$this->fetchAllCategories($this->checked->cat_new_sel_id, ""); // Kategorien anzeigen & letzte Auswahl markieren
				$this->content->template['cat_is_new'] = 1; // Meldung �ber das erfolgreiche Anlegen einer neuen Kategorie
			}
			$this->content->template['cat_new_name_name'] = $this->nobr($this->checked->cat_new_name_name);
			$this->content->template['cat_new_descript_name'] = $this->nobr($this->checked->cat_new_descript_name);
		}
	}

	/**
	 * Eine/mehrere ausgew�hlte Kategorie(n) wurde(n) durch Submit zur Bearbeitung angefordert. (Backend)
	 * template cat_back_edit.html
	 */
	function edit_category()
	{
		// Gesamtanzahl aller Kategorien bereitstellen (= "", 1)
		$this->content->template['anzahl'] = $this->read_count_all_categories("", 1);
		if (isset($this->checked->submit) && $this->checked->submit === $this->content->template['plugin']['galerie_back']['submit']['cat_edit_select']) { // Auswahl
			$this->edit_select_category();
			return;
		}

		if (!isset($this->checked->submit) || isset($this->checked->submit) && !$this->checked->submit AND !$this->checked->cat_edit_id) {
			$this->fetchAllCategories(0, ""); // Liste aller Kategorien anzeigen
		}
		else {
			$this->checked->cats_edit_id = isset($this->checked->cats_edit_id) && is_array($this->checked->cats_edit_id) ? $this->checked->cats_edit_id : [];
			$this->content->template['form_submitted'] = 1; // Msg-Ausgabesteuerung (MSGs nur nach Submit)
			// Entweder-oder, aber nicht beide (cat_edit_id: Kategorie-Id, wenn nur eine Kategorie bearbeitet wird,
			if ( $this->checked->cat_edit_id AND count($this->checked->cats_edit_id) ) {
				$this->content->template['fehler2'] = $fehler = 1;
			}
			else {
				// eine davon sollte zumindest da sein
				if (!count($this->checked->cats_edit_id) AND !$this->checked->cat_edit_id) {
					$this->content->template['fehler2'] = $fehler = 1;
				}
				else {
					// numerischer check f�r die Bearbeitung mehrerer Kategorien
					if (count($this->checked->cats_edit_id)) {
						if (!$this->checkNumeric($this->checked->cats_edit_id)) $this->content->template['fehler2'] = $fehler = 1;
					}
					else {
						// numerischer check der ID f�r die Bearbeitung einer Kategorie allein
						if (!ctype_digit($this->checked->cat_edit_id)) $this->content->template['fehler2'] = $fehler = 1;
					}
				}
			}
			if (!isset($fehler) || isset($fehler) && !$fehler) {
				//  Ist nur eine bestimmte Kategorie ausgew�hlt?
				if ($this->checked->cat_edit_id) {
					// Ja, Daten einer per Link ausgew�hlten Kategorie anzeigen (Auswahlliste von cat_back_main.html)
					if (empty($this->checked->submit)) {
						$result = $this->getCatData($this->checked->cat_edit_id);
						$this->content->template['cat_edit_name_name'] = $this->nobr($result[0]['gallang_name']);
						$this->content->template['cat_edit_descript_name'] = $this->nobr($result[0]['gallang_beschreibung']);
						$this->content->template['cat_move_from_id'] = $result[0]['gallang_gal_id'];
						$this->content->template['cat_edit_id'] = $this->checked->cat_edit_id;
						//  Alle Kategoriedaten holen und Option-Eintr�ge der Selectbox f�llen
						#$this->fetchAllCategories($this->checked->cat_edit_id, "");
					}
					// Die Daten einer bestimmten Kategorie �ndern (cat_back_main.html, Linkparameter: $this->checked->cat_edit_id)
					else {
						// Falls kein Kategoriename angegeben wurde: Fehler ausl�sen: Fehler (Kategoriename fehlt). Eingabe-Daten wiederherstellen
						if (empty($this->checked->cat_edit_name_name)) {
							$this->content->template['fehler1'] = 1;
						}
						// Es wurden komplette Daten abgeschickt - speichern in die DB
						else {
							// numerische Pr�fung der IDs vor Ausf�hrung aller �nderungen
							if (isset($this->checked->cat_move_from_id) && $this->checked->cat_move_from_id AND !ctype_digit($this->checked->cat_move_from_id)) {
								$this->content->template['fehler2'] = $fehler = 1;
							}
							if (isset($this->checked->cat_move_to_id) && $this->checked->cat_move_to_id AND !ctype_digit($this->checked->cat_move_to_id)) {
								$this->content->template['fehler2'] = $fehler = 1;
							}
							// Falls sich die Kategorie ge�ndert hat, diese neu zuordnen, wenn kein Fehler
							#if ($this->checked->cat_move_from_id != $this->checked->cat_move_to_id AND !$fehler) $this->move_category();
							if (!isset($fehler) || isset($fehler) && !$fehler) { // Update nur dann, wenn der obige Fehler nicht aufgetreten ist
								// Daten hinzuf�gen
								$sql = sprintf("UPDATE %s
												SET gallang_name = '%s',
													gallang_beschreibung = '%s'
												WHERE gallang_gal_id = '%d' AND gallang_lang_id ='%d'",
									$this->cms->tbname['galerie_galerien_language'],
									$this->db->escape($this->checked->cat_edit_name_name),
									$this->db->escape($this->checked->cat_edit_descript_name),
									$this->db->escape($this->checked->cat_edit_id),
									$this->db->escape($this->cms->lang_back_content_id)
								);
								$this->db->query($sql);
							}
							$this->content->template['cat_is_edit'] = 1; // Meldung Daten sind aktualisiert
						}
						IfNotSetNull($this->checked->cat_move_to_id);
						// Daten ans Template zur�ckgeben
						$this->content->template['cat_edit_id'] = $this->checked->cat_edit_id;
						$this->content->template['cat_move_to_id'] = $this->checked->cat_move_to_id;
						$this->content->template['cat_move_from_id'] = $this->checked->cat_move_from_id;
						$this->content->template['cat_edit_name_name'] = $this->nobr($this->checked->cat_edit_name_name);
						$this->content->template['cat_edit_descript_name'] = $this->nobr($this->checked->cat_edit_descript_name);
						$this->fetchAllCategories($this->checked->cat_move_to_id, ""); // restore Kategorien plus Auswahl
					}
				}
				else {
					// Nein, es ist keine Kategorie via Link ausgew�hlt. Mehrere Kategoriedaten in die DB schreiben
					if (count($this->checked->cats_edit_id)) { // Auswahl der zu bearbeitenden Kategorien
						// alle ausgew�hlten in die DB
						foreach ($this->checked->cats_edit_id as $key => $value) {
							// Fehler ausl�sen, wenn der Name fehlt
							if (!empty($this->checked->cat_edit_name_name[$key])) {
								$sql = sprintf("UPDATE %s
												SET gallang_name = '%s',
													gallang_beschreibung = '%s'
												WHERE gallang_gal_id = '%d' AND gallang_lang_id = '%d'",
									$this->cms->tbname['galerie_galerien_language'],
									$this->db->escape($this->checked->cat_edit_name_name[$key]),
									$this->db->escape($this->checked->cat_edit_descript_name[$key]),
									$this->db->escape($key),
									$this->db->escape($this->cms->lang_back_content_id)
								);
								$this->db->query($sql);
							}
							else {
								$this->content->template['cats_data'][$key]['fehler1'] = $this->content->template['fehler1'] = 1;
							}
							// Daten ans Template zur�ckgeben
							$this->content->template['cats_data'][$key]['cat_edit_name_name'] = $this->nobr($this->checked->cat_edit_name_name[$key]);
							$this->content->template['cats_data'][$key]['cat_edit_descript_name'] = $this->nobr($this->checked->cat_edit_descript_name[$key]);
							$this->content->template['cats_data'][$key]['gallang_gal_id'] = $key;
						}
					}
				}
			}
		}
		// Links (Edit, alles ausw�hlen, etc.)
		$this->content->template['self'] = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&amp;template=".$this->checked->template;
		// Toggle Selectboxen
		$this->content->template['checkeddel'] = !empty($this->checked->checkalldel) ? "checked='checked'" : "";
	}

	/**
	 * Anzeige der durch multiple Edit gew�hlten Kategoriedaten. (Backend)
	 * template cat_back_edit.html
	 */
	function edit_select_category()
	{
		if ($this->checkNumeric($this->checked->cat_edit_select)) {
			// Daten der ausgew�hlten Kategorien (checkboxen) bereitstellen
			foreach ($this->checked->cat_edit_select as $key => $edit) {
				$result = $this->getCatData($edit);
				$this->content->template['cats_data'][$key]['cat_edit_name_name'] = $this->nobr($result[0]['gallang_name']); //Kategoriename
				$this->content->template['cats_data'][$key]['cat_edit_descript_name'] = $this->nobr($result[0]['gallang_beschreibung']); // description
				$this->content->template['cats_data'][$key]['gallang_gal_id'] = $result[0]['gallang_gal_id']; // cat id
			}
			$this->content->template['anzahl'] = $this->read_count_all_categories("", 1); // Gesamtanzahl aller Kategorien
		}
		else {
			// (Noch) keine Auswahl oder nicht numerische Werte vorhanden, dann nur Formulardaten (erneut) bereitstellen
			$this->fetchAllCategories(0, "");
			$this->content->template['self'] = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&amp;template=".$this->checked->template;
		}
	}

	/**
	 * Kategorie(n) l�schen (Backend)
	 * template cat_back_del.html
	 */
	function del_category()
	{
		// Submit-Button aktiv? (Entfernen oder positive Antwort auf die L�schabfrage
		if (isset($this->checked->submit) && $this->checked->submit) {
			// Auswahl(en) in checkboxen?
			if ($this->checkNumeric($this->checked->cat_delete)) {
				// Schalter: bei catdelete = true ist die L�schabfrage noch nicht erfolgt
				if (isset($this->checked->catdelete) && $this->checked->catdelete) {
					$this->content->template['delete'] = 1;
					$this->content->template['cat_delete'] = $this->checked->cat_delete;
				}
				else {
					// L�schen? Ja / Nein
					if ($this->checked->submit == $this->content->template['plugin']['galerie_back']['submit']['delete_yes']) {
						// Verwaiste Eintr�ge kennzeichnen
						$this->content->template['galerie_cat_data'] = array();
						foreach ($this->checked->cat_delete as $id) {
							$this->content->template['galerie_cat_data'] = array_merge($this->content->template['galerie_cat_data'], $this->getCatData($id));
						}
						// Die in den checkboxen ausgew�hlten l�schen
						foreach ($this->checked->cat_delete as $key => $del) {
							$sql = sprintf("SELECT gal_id FROM %s
											WHERE parent_id = '%d'",
								$this->cms->tbname['galerie_galerien'],
								$this->db->escape($del)
							);
							$result = $this->db->get_var($sql);
							if (!$result) {
								// Kategoriedaten l�schen
								$sql = sprintf("DELETE FROM %s
												WHERE gallang_gal_id = '%d'",
									$this->cms->tbname['galerie_galerien_language'],
									$this->db->escape($del)
								);
								$this->db->query($sql);
								// Relation Kategorie/BG l�schen
								$sql = sprintf("DELETE FROM %s WHERE gal_id = '%d'",
									$this->cms->tbname['galerie_galerien'],
									$this->db->escape($del)
								);
								$this->db->query($sql);
								$this->content->template['cat_is_del'] = 1; // Fertig-Meldung
							}
							else {
								$this->content->template['cat_has_subcats'] = 1;
							} // Fertig-Meldung
						}
						#if ($this->content->template['cat_is_del']) $this->cat_renumber($del, 0);
					}
				}
			}
			elseif (isset($this->checked->cat_delete)) {
				$this->content->template['fehler1'] = 1;
			}
		}
		// Pfad etc. bereitstellen
		$this->content->template['self'] = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&amp;template=".$this->checked->template;
		// Toggle Selectboxen (Vorbelegung der checkboxen alle/nix)
		$this->content->template['checkeddel'] = !empty($this->checked->checkalldel) ? "checked='checked'" : "";
		$this->fetchAllCategories(0, ""); // Kategoriedaten erneut holen
	}

	/**
	 * Renumber Baumstruktur von Quelle und Ziel (Backend)
	 *
	 * @param int $cat_parent_id_from
	 * @param int $cat_id_to
	 */
	function cat_renumber($cat_parent_id_from = 0, $cat_id_to = 0)
	{
		// Quelle neu numerieren
		$cat_childs_from = $this->getChilds($cat_parent_id_from);
		for ($i = 0; $i < (count($cat_childs_from)); $i++) {
			$this->saveCatOrderId($cat_childs_from[$i]['gal_id'], ($i + 1) * 10);
		}
	}

	/**
	 * Die order_id einer Kategorie speichern
	 *
	 * @param int $id
	 * @param $order_id
	 */
	function saveCatOrderId($id = 0, $order_id)
	{
		$sql = sprintf("UPDATE %s
						SET gal_order_id = '%d'
						WHERE gal_id = '%d'",
			$this->cms->tbname['galerie_galerien'],
			$this->db->escape($order_id),
			$this->db->escape($id)
		);
		$this->db->query($sql);
	}

	/**
	 * Hole alle childs zu einer Kategorie-id (Backend)
	 *
	 * @param int $cat_id
	 * @return array|void
	 */
	function getChilds($cat_id = 0)
	{
		$sql = sprintf("SELECT gal_id, parent_id
						FROM %s
						WHERE parent_id = '%d'
						ORDER BY order_id",
			$this->cms->tbname['galerie_galerien'],
			$this->db->escape($cat_id)
		);
		return ($this->db->get_results($sql, ARRAY_A));
	}

	/**
	 * Anzeige von <br> unterdr�cken
	 *
	 * @param $nobr
	 * @return string
	 */
	function nobr($nobr)
	{
		#if (is_array($nobr)) {
		#	foreach ($nobr AS $key =>$value) {
		#		$nobr[$key]['question'] = "nobr:" . $nobr[$key]['question'];
		#	}
		#}
		#else
		$nobr = "nobr:" . $nobr;
		return $nobr;
	}

	/**
	 * Pr�fung auf numerisch (Backend)
	 *
	 * @param $test
	 * @param int $key_check
	 * @return bool
	 */
	function checkNumeric($test, $key_check = 0)
	{
		$numeric = true; // Vorbelegen mit "Wert ist numerisch"
		if (is_array($test)) { // Check array, das rein numerische Werte enthalten sollte
			if (!count($test)) { // Leeres array als nicht numerisch behandeln
				$numeric = false;
			}
			else {
				foreach ($test AS $key =>$value) {
					if (!ctype_digit((string)$key)) {
						$numeric = false;
						break;
					}
					if (!$key_check) { // Nur die Keys pr�fen?
						if (!ctype_digit((string)$value)) { // Auch Werte pr�fen
							$numeric = false;
							break;
						}
					}
				}
			}
		}
		else { // Kein Array als nicht numerisch behandeln
			$numeric = false;
		}
		return $numeric;
	}

	/**
	 * N�chste verf�gbare order_id holen (Backend)
	 *
	 * @return int|null
	 */
	function getNextOrderId()
	{
		return ($this->getMaxOrderId() + 10);
	}

	/**
	 * H�chste order_id ermitteln
	 *
	 * @return array|null
	 */
	function getMaxOrderId()
	{
		$lang_id = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		// Kategorie
		$sql = sprintf("SELECT MAX(gal_order_id)
						FROM %s
						WHERE parent_id = 0",
			$this->cms->tbname['galerie_galerien']
		);
		$result = $this->db->get_var($sql);
		return ($result);
	}

	/**
	 * Alle Kategorien ans Template mit Baumstruktur �bergeben und Daten zur Listensteuerung f�rs Template erzeugen
	 *
	 * @param string $selected
	 * @param string $selectedto
	 * @param int $active
	 */
	function fetchAllCategories($selected ="", $selectedto = "", $active = 0)
	{
		$result = $this->getCatData("");
		// Baumstruktur erstellen, dazu die Level, parents, childs und Anzahl der n�tigen ul/li-Endetags ans Template
		// Das gibt dem Template die M�glichkeit die ul-li-Struktur komplett zu erstellen (s. z. B. cat_back_main.html)
		if (count($result)) {
			// Kategorien mit Baumstruktur erstellen
			// Es kommen Eintr�ge durch categoriesTree nach $cat_tree_data
			// In $result haben wir dann alle Eintr�ge
			$cat_tree_data = $this->categoriesTree($result);
			// Baumstruktur ans Template
			$this->content->template['galerie_cat_data'] = $cat_tree_data;
			// Zus�tzlich die verwaisten Eintr�ge anh�ngen
			#if (count($temp)) $this->content->template['galerie_cat_data'] = array_merge($this->content->template['galerie_cat_data'], $temp);
			$this->cat_Tree = array(); // Clear
			$this->content->template['anzahl'] = $this->read_count_all_categories("", 1); // Gesamtanzahl aller Kategorien
			// Listenstruktur erstellen (Kennzeichnung parent mit childs, last child und Anzahl ul-/li-close-Tags ermitteln)
			for ($i = 0; $i < $this->content->template['anzahl']; $i++) {
				IfNotSetNull($this->content->template['galerie_cat_data'][$i+1]);

				// Parent ermitteln
				if ($this->content->template['galerie_cat_data'][$i]['level'] < $this->content->template['galerie_cat_data'][$i+1]['level']) {
					// Es geht einen Level tiefer im n�chsten Eintrag
					$this->content->template['galerie_cat_data'][$i]['parent'] = 1; // parent kennzeichnen
					$this->content->template['galerie_cat_data'][$i]['close_tags'] = 0; // 1 Level runter, also jetzt keine close-tags
				}
				else {
					// Es geht einen oder mehrere Level h�her. Anzahl der close-Tags berechnen und �bergeben.
					$this->content->template['galerie_cat_data'][$i]['close_tags'] =
						$this->content->template['galerie_cat_data'][$i]['level'] - $this->content->template['galerie_cat_data'][$i+1]['level'];
					// es geht rauf, also ist dies kein parent (hat keine Unterkategorien)
					$this->content->template['galerie_cat_data'][$i]['parent'] = 0;  // Kein parent
				}
				// Lastchild ermitteln. Ab Position $i den Rest durchsuchen
				// Dieser Wert l�st dann zusammen mit dem Wert in 'close_tags' die Ausgabe aller /ul-/li's im Template aus
				$found = 0;
				for ($i2 = $i + 1; $i2 < count($this->content->template['galerie_cat_data']); $i2++) {
					// TODO chck ist nicht Test auf > allein schon m�glich?
					if ($this->content->template['galerie_cat_data'][$i]['level'] <= $this->content->template['galerie_cat_data'][$i2]['level']) {
						if ($this->content->template['galerie_cat_data'][$i]['level'] == $this->content->template['galerie_cat_data'][$i2]['level']) {
							// Es wurde derselbe Level noch einmal gefunden, also ist das aktuelle Child kein Lastchild
							$this->content->template['galerie_cat_data'][$i]['close_tags'] = 0; // hier jetzt keine close-Tags
							// kein last child
							break;
						}
					}
					// Lastchild, da eine h�here Levelstufe gefunden wurde
					else {
						$found = 1; // last child
						break;
					}
				}
				// lastchild Wert ans Template �bergeben
				$this->content->template['galerie_cat_data'][$i]['lastchild'] = $found;
			}
		}
		else {
			$this->content->template['anzahl'] = 0; // es gibt keine Kategorien
			$this->content->template['galerie_cat_data'] = array();
		}
	}

	/**
	 * Lesen aller Kategoriedaten oder z�hlen aller Kategorien (Backend)
	 *
	 * @param string $id
	 * @param int $count
	 * @return array|void|null
	 */
	function read_count_all_categories($id = "", $count = 0)
	{
		$lang_id = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		$where = (empty($id)) ? " WHERE T1.gallang_lang_id = " . $lang_id . " " : " WHERE T1.gallang_gal_id = " . $id . " AND T1.gallang_lang_id = " . $lang_id . " ";
		// Noch nicht zu einer Kategorie zugeordnete Galerien �berlesen
		$where .= " AND parent_id != '999999999'";
		// Kategoriedaten komplett einlesen
		$sql = sprintf("SELECT SQL_CALC_FOUND_ROWS *
						FROM %s T1
						INNER JOIN %s T2 ON (T2.gal_id = T1.gallang_gal_id)
						$where
						ORDER BY T2.gal_order_id ",
			$this->cms->tbname['galerie_galerien_language'],
			$this->cms->tbname['galerie_galerien']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if ($count) {
			// Anzahl ermitteln
			$sql = sprintf("SELECT FOUND_ROWS()");
			$result = $this->db->get_var($sql);
		}
		return $result;
	}

	/**
	 * Baumstruktur f�r die Kategorien erzeugen (Level-Werte f�rs Template setzen) (Backend)
	 *
	 * @param array $categories enth�lt alle Daten
	 * @param int $id id des parents f�r das ein Baum oder eine Section des Baums auszugeben ist
	 * durch Vorgabe einer ID >0 wird nur die Section unterhalb des vorgegebenen parents �bergeben,
	 * also alle childs zum vorgegebenen parent. Weitere M�glichkeit zur Eingrenzung: Vorgabe des Levels.
	 *   0 inkludiert alle Hauptkategorien mit parent_id = 0
	 * @param int $level Level ab dem die Ausgabe beginnen soll
	 * @return void
	 */
	function categoriesTree($categories = array(), $id = 0, $level = 0)
	{
		$i1 = $i2 = 0;
		$temp_cat_array1 = array();
		// Schritt 1: Alle Unterkategorien finden (parent_id zur id finden/child zu parent) mit demselben parent
		// $temp_cat_array1 erh�lt hierzu die Schl�ssel (index von $categories).
		// $temp_cat_array bleibt leer, wenn kein parent (id) gefunden wird. Dann ist am Ende $i1 = 0.
		foreach ($categories as $data1) { // parent_id zur id finden (child zum parent)
			// Unterkategorien in einem Array zusammenfassen
			// $i2 enth�lt den Schl�ssel.
			if ($data1['parent_id'] == $id) {
				$temp_cat_array1[$i1++] = $i2;
			}
			$i2++;
		}
		// Daten zusammenstellen, wenn etwas gefunden wurde ($i1 != 0)
		if ($i1 != 0) {
			// Schritt 2: Zuweisung der Kategoriedaten aufgrund der zuvor gefundenen Schl�ssel. Level einf�gen.
			// Die Daten zu den parents/childs zuweisen
			foreach ($temp_cat_array1 as $data2) {
				$temp_cat_array2 = array();
				// Daten�bergabe aufgrund des Schl�ssels in $data2
				foreach ($categories[$data2] as $key => $value) {
					$temp_cat_array2[$key] = $value;
				}
				$temp_cat_array2['level'] = $level; // Nummer der Ebene zum "CSS-Einr�cken" in der Selectbox
				$this->cat_Tree[] = $temp_cat_array2; // Ergebnis komplett fortschreiben ins Array
				// Rekursiver Aufruf ist erforderlich f�r jede Kategorie. Anzahl der Aufrufe ist abh�ngig von der Leveltiefe
				// innerhalb einer Section.
				$this->categoriesTree($categories, $temp_cat_array2["gallang_gal_id"], $level + 1); // n�chsten Level untersuchen
			}
		}
		else {
			return;
		} // back to caller. Kein parent gefunden. Zur�ck zum Level vorher.
		return $this->cat_Tree; // fertige Baumstruktur zur�ckgeben
	}

	/**
	 * Alle Kategoriedaten sortiert nach der order_id holen
	 *
	 * @param string $id
	 * @return array|void|null
	 */
	function getCatData($id = "")
	{
		// Kategoriedaten komplett einlesen
		$result = $this->read_count_all_categories($id, 0);
		return $result;
	}

	/**
	 * @param $returnresult
	 * @param $searchfor
	 * @param $sqllimit
	 * @param $result_anzahl
	 */
	function do_search(&$returnresult, $searchfor, $sqllimit, &$result_anzahl)
	{
		$sql = sprintf("SELECT gal_verzeichnis, bildlang_beschreibung, bildlang_name, bild_datei, C.gal_id AS GALERIE_ID FROM %s A LEFT JOIN %s B ON A.bild_id=B.bildlang_bild_id LEFT JOIN %s C ON C.gal_id=A.bild_gal_id WHERE bild_datei LIKE '%s' AND bildlang_lang_id=%d", $this->db_praefix . "galerie_bilder", $this->db_praefix . "galerie_bilder_language", $this->db_praefix . "galerie_galerien", "%" . $this->db->escape($searchfor) . "%", $this->cms->lang_id);

		$results = $this->db->get_results($sql, ARRAY_A);

		foreach($results as $eintrag) {
			$bildabspfad = PAPOO_WEB_PFAD . "/plugins/galerie/galerien/" . $eintrag['gal_verzeichnis'] . "/" . $eintrag['bild_datei'];

			$bildeinbindung = '<br/><img src="'.$bildabspfad.'" alt="'.$eintrag['bild_datei'].'">';

			$returnresult[] = array(
				"linktext" => "plugin.php?menuid=1&template=galerie/templates/galerie_front.html&galerie_switch=GALERIE_START&galerie_id=" . $eintrag['GALERIE_ID'],
				"uberschrift" => $eintrag['bildlang_name'] . $bildeinbindung,
				"text" => $eintrag['bildlang_beschreibung'],
				"metadescrip" => $eintrag['bildlang_beschreibung']
			);
			$result_anzahl++;
		}
	}
}

$galerie = new galerie_class();
