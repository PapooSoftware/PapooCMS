<?php
require_once(PAPOO_ABS_PFAD . "/plugins/pkalender/lib/class_datums.php");

if (stristr($_SERVER['PHP_SELF'], 'class_cal_front.php')) die('You are not allowed to see this page directly');
/**
 * class_cal_front
 * Die Shop Klasse realisiert den Kalender
 * Dazu geh�ren noch einige weitere Klassen
 * die entsprechend eingebunden werden
 * @package Papoo
 * @author Papoo Software
 * @copyright 2009
 * @version $Id$
 * @access public
 */
class class_cal_front
{
	/**
	 * class_cal_front::class_cal_front()
	 * Initialisierung und Einbindung von Klassen
	 * @return void
	 */
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content, $user, $checked, $cms, $db_abs, $db, $diverse, $module;
		$this->content = &$content;
		$this->user = &$user;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db_abs = &$db_abs;
		$this->db = &$db;
		$this->diverse = &$diverse;
		$this->module = &$module;

		if (defined('admin')) {
			$this->user->check_intern();
		}
	}

	/**
	 * class_cal_front::get_alle_datums_eines_kalenders()
	 *
	 * @return void
	 */
	function get_kalender_front()
	{
		if (!isset($_POST['monats_id'])) {
			$_POST['monats_id'] = NULL;
		}

		if (is_numeric($_POST['monats_id'])) {
			$this->checked->monats_id = $_POST['monats_id'];
		}

		if (!isset($this->checked->cal_insert)) {
			$this->checked->cal_insert = NULL;
		}

		//Jahreszahlen erzeugen f�r Jahres�bersicht
		$this->create_jahreszahlen();

		//
		$this->get_surls_dat_from_url();

		//Die Kalender im FE
		$all_cals = $this->get_alle_calender();

		$this->all_cals = $all_cals;
		//Kalender Vorlage holen
		$vorlage = $this->create_cal_vorlage($all_cals);

		//Daten der Kalender
		$this->content->template['kalender_result'] = $vorlage;

		//Url f�r die Verlinkung im Kalender
		$this->get_url_of_kalender($all_cals);

		//Kalender und url zum Kalender rausholen
		$this->get_aktu_kalender_url();

		//Kalenderdaten im Content anzeigen
		$this->get_kalender_daten_eines_kalender($all_cals);

		if ($this->checked->cal_insert == "true") {
			$this->content->template['is_eingetragen'] = "ok";
		}

		if (!isset($jahr)) {
			$jahr = NULL;
		}

		//Aktuelles Datum �bergeben
		if (isset($this->checked->date_time) && is_numeric($this->checked->date_time)) {
			$this->content->template['plugin_kalender_aktu_datum'] = $this->checked->date_time;
		}
		else {
			IfNotSetNull($this->monat);
			IfNotSetNull($this->jahr);
			$this->content->template['plugin_kalender_aktu_datum_monat'] = mktime(0, 0, 0, $this->monat, 1, $this->jahr);
			//$this->monats_name
			$this->content->template['plugin_kalender_aktu_datum_monat_monat'] = $this->make_monat(date("F", mktime(0, 0, 0,
				$this->monat, 1, $jahr)));;
		}

		if (is_array($vorlage)) {
			foreach ($vorlage as $key => $value) {
				if ($value['kalender_id'] == $this->checked->kal_id) {
					$neu_vorlage[] = $value;
				}
			}
		}

		if (!isset($this->checked->cal_view)) {
			$this->checked->cal_view = NULL;
		}

		if ($this->checked->cal_view == "cal" && empty($this->checked->date_id)) {
			$this->content->template['plugin_calender_view'] = "cal";
			$this->content->template['plugin_calender_view_data'] = $neu_vorlage;
		}

		if ($this->checked->cal_view == "new" && $this->content->template['kalender_eintrge_von_aussen']) {
			$this->content->template['plugin_calender_view'] = "new";
			if ($this->kalender_direkt_freischalten == 1) {
				$this->checked->pkal_date_eintrag_im_frontend_freischalten = 1;
			}

			$this->checked->psel_cal_id = $this->checked->kal_id;
			$this->class_datums->get_kategorien();
			$this->class_datums->create_new_termin();
		}

		if (!isset($this->show_module_ok)) {
			$this->show_module_ok = NULL;
		}

		$aktuell = array();
		if (!empty($this->module->module_aktiv['mod_pkalender_front']) || !empty($this->module->module_aktiv['mod_pkalender_front_aktuell']) || $this->show_module_ok) {
			IfNotSetNull($vorlage[0]['kalender_anzahl_der_eintrge_im_modul_aktuellste_eintrge']);
			$terminliste = $this->get_alle_termine_ab_jetzt($all_cals, $vorlage[0]['kalender_anzahl_der_eintrge_im_modul_aktuellste_eintrge']);

			$now = time();
			$dacal_id = 0;

			if (is_array($this->content->template['alle_termin_des_monats'])) {
				foreach ($this->content->template['alle_termin_des_monats'] as $kalender) {
					if (is_array($kalender)) {
						$i = 1;
						if ($terminliste) {
							foreach ($terminliste as $key => $termin) {
								if ($dacal_id < $termin['pkal_date_kalender_id']) {
									$i = 1;
								}

								if (isset($this->aktuelle[$termin['pkal_date_kalender_id']]) && $i > $this->aktuelle[$termin['pkal_date_kalender_id']]) {
									continue;
								}

								// Endzeitpunkt bestimmen
								$end = (int)$termin['pkal_date_end_datum'];
								if ($termin['pkal_date_uhrzeit_ende'] == '') {
									$end = strtotime('23:59:59', $end);
								}
								else {
									$end = strtotime($termin['pkal_date_uhrzeit_ende'], $end);
								}
								// Wenn Termin vergangen, überspringen
								if ($end < $now) {
									continue;
								}
								$aktuell[$termin['pkal_date_kalender_id']][$key] = $termin;
								$i++;
								$dacal_id = $termin['pkal_date_kalender_id'];
							}
						}
					}
				}
			}
			$this->content->template['alle_termin_ab_jetzt'] = $aktuell;
			if (isset($aktuell[$this->checked->kal_id])) {
				$this->content->template["termine_des_aktuellen_kalenders"] = $aktuell[$this->checked->kal_id];
			}
			$this->alle_termin_ab_jetzt = $aktuell;
		}
	}

	/**
	 * class_cal_front::create_jahreszahlen()
	 *
	 * @return void
	 */
	function create_jahreszahlen()
	{
		if(isset($this->checked->kal_id)) {
			$uebersicht_jahr_liste = array();
			$sql = sprintf('SELECT DISTINCT year(FROM_UNIXTIME(pkal_date_start_datum)) AS \'jahr\' FROM %1$s WHERE pkal_date_kalender_id=\'%2$s\'
							AND year(FROM_UNIXTIME(pkal_date_start_datum)) >= YEAR(DATE(NOW()))
						union
						SELECT DISTINCT year(FROM_UNIXTIME(pkal_date_end_datum)) AS \'jahr\' FROM %1$s WHERE pkal_date_kalender_id=\'%2$s\'
							AND year(FROM_UNIXTIME(pkal_date_end_datum)) >= YEAR(DATE(NOW()))
						',
				$this->cms->tbname['plugin_kalender_date'],
				$this->db->escape($this->checked->kal_id)
			);
			$results = $this->db->get_results($sql, ARRAY_A);

			foreach ($results as $result) {
				$uebersicht_jahr_liste[] = $result['jahr'];
			}
			sort($uebersicht_jahr_liste);
			$this->content->template['uebersicht_jahr_liste'] = $uebersicht_jahr_liste;
			IfNotSetNull($this->checked->wy);
			$this->content->template['aktives_jahr'] = $this->checked->wy;
			// $this->content->template['uebersicht_jahr']= date("Y");
			// $this->content->template['uebersicht_jahr1']= date("Y")+1;
		}
	}

	function get_anzahl_liste_eintraege()
	{

	}

	/**
	 * class_cal_front::get_surls_dat_from_url()
	 *
	 * @return void
	 */
	function get_surls_dat_from_url()
	{
		if ($this->cms->mod_rewrite == 2) {
			if (is_object($this->checked)) {
				foreach ($this->checked as $key => $value) {
					//&& strlen($value)<5
					if (stristr($key, "var")) {
						$expl = $value;
					}
				}
			}
			if (!is_array($expl)) {
				$var_ex = explode("-", $expl);
			}
			#$this->checked->date_id=$var_ex['1'];
			if (is_array($var_ex)) {
				$this->checked->date_time = trim(str_replace(".html", "", array_pop($var_ex)));
			}
			if (is_numeric($this->checked->date_time) && empty($this->checked->monats_id)) {
				$this->checked->monats_id = @date("m", $this->checked->date_time);
				$this->checked->cal_view = "";
			}
			//1283292000
			//1283292000
			//&& empty($this->checked->var2)
			if ($this->cms->mod_rewrite == 2 && empty($this->checked->var2) && !empty($this->checked->var1) && !is_numeric($this->checked->kal_id)) {
				$this->checked->kal_id = $var_ex['0'];
				if (!empty($this->checked->kal_id)) {
					$sql = sprintf("SELECT * FROM %s
										WHERE menuid_id LIKE '%s'
										AND lang_id='%d'",
						$this->cms->tbname['papoo_menu_language'],
						$this->db->escape($this->checked->menuid),
						$this->cms->lang_id
					);
					$result = $this->db->get_results($sql, ARRAY_A);
					global $template;
					$template = PAPOO_ABS_PFAD . "/plugins/pkalender/templates/pkalender_front.html";
				}
			}
		}
		if (is_numeric($_POST['monats_id'])) {
			$this->checked->monats_id = $_POST['monats_id'];
		}
	}

	/**
	 * class_cal_front::get_kalender_daten_eines_kalender()
	 *
	 * @param mixed $all_cals
	 * @return void
	 */
	function get_kalender_daten_eines_kalender($all_cals)
	{
		if (is_numeric($this->checked->kal_id)) {
			//Daten dieses Kalenders
			if (is_array($all_cals)) {
				foreach ($all_cals as $key => $value) {
					if ($value['kalender_id'] == $this->checked->kal_id) {
						$kalender = $value;
					}
				}
			}
			IfNotSetNull($kalender);
			$this->content->template['kalender_daten'] = $kalender;
			//Eintr�ge d�rfen von au�en erfolgen
			$this->content->template['kalender_eintrge_von_aussen'] = $kalender['kalender_eintrge_von_aussen'];
			$this->kalender_direkt_freischalten = $kalender['kalender_direkt_freischalten'];
			if ($this->content->template['kalender_eintrge_von_aussen'] == 1) {
				$this->check_rights_von_aussen();
			}

			$this->kalender_email_versenden_bei_neuen_eintrag_von_auen = $kalender['kalender_email_versenden_bei_neuen_eintrag_von_auen'];
			$this->kalender_email_adresse_fr_den_versand_dieser_mail = $kalender['kalender_email_adresse_fr_den_versand_dieser_mail'];

			//Daten rausholen diesen Monats get_alle_termine_des_monats
			IfNotSetNull($this->monat);
			IfNotSetNull($this->jahr);
			$daten_des_monats = $this->get_alle_termine_des_monats($this->monat, $kalender, $this->jahr);
			//Ein Datum ausgew�hlt, dann diese Daten rausholen
			if (is_numeric($this->checked->date_time)) {
				$datums = $this->check_date($this->checked->date_time, $this->monat, $kalender, $this->jahr, True);

				if ($datums) {
					$this->content->template['site_title'] = $datums[0]['pkal_date_titel_des_termins'];
					$this->content->template['description'] = strip_tags($datums[0]['pkal_date_terminbeschreibung']);
				}
			} //Kein Datum, dann alle Eintr�ge des Monats
			else {
				$datums = $daten_des_monats;
				$this->content->template['site_title'] = $kalender['kalender_bezeichnung_des_kalenders'];
				$this->content->template['description'] = strip_tags($kalender['kalender_text_oberhalb']);
			}
			//Dann die vorhandenen termine �bergeben
			$this->content->template['termine_des_datums'] = $datums;
		}

		if (isset($this->checked->pkal_cat_id) && $this->checked->pkal_cat_id) {
			if ($this->checked->wy) {
				$sql = sprintf("SELECT * FROM %s 
								LEFT JOIN %s ON pkal_date_id=date_id_id
								LEFT JOIN %s ON date_gruppe_lese_id=gruppenid
								WHERE pkal_date_kalender_id='%d'
								AND pkal_date_kategorie_im_kalender='%d'
								AND year(FROM_UNIXTIME(pkal_date_start_datum))='%d'
								AND userid='%d' AND pkal_date_kalender_id='%d'
						",
					$this->cms->tbname['plugin_kalender_date'],
					$this->cms->tbname['plugin_kalender_lookup_read_date'],
					$this->cms->tbname['papoo_lookup_ug'],
					$this->db->escape($this->checked->kal_id),
					$this->db->escape($this->checked->pkal_cat_id),
					$this->db->escape($this->checked->wy),
					$this->user->userid,
					$this->checked->kal_id
				);
			}
			else {
				$sql = sprintf("	SELECT * FROM %s 
									LEFT JOIN %s ON pkal_date_id=date_id_id
									LEFT JOIN %s ON date_gruppe_lese_id=gruppenid
									WHERE pkal_date_kalender_id='%d'
						AND pkal_date_kategorie_im_kalender='%d'
						AND FROM_UNIXTIME(pkal_date_start_datum) >= date(NOW())
						AND userid='%d' AND pkal_date_kalender_id='%d'
						",
					$this->cms->tbname['plugin_kalender_date'],
					$this->cms->tbname['plugin_kalender_lookup_read_date'],
					$this->cms->tbname['papoo_lookup_ug'],
					$this->db->escape($this->checked->kal_id),
					$this->db->escape($this->checked->pkal_cat_id),
					$this->user->userid,
					$this->checked->kal_id
				);
			}
		}
		else {
			if (isset($this->checked->wy) && $this->checked->wy) {
				$sql = sprintf("	SELECT * FROM %s 
									LEFT JOIN %s ON pkal_date_id=date_id_id
									LEFT JOIN %s ON date_gruppe_lese_id=gruppenid
									WHERE pkal_date_kalender_id='%d'
								AND year(FROM_UNIXTIME(pkal_date_start_datum))='%d'
								AND userid='%d' AND pkal_date_kalender_id='%d'
							",
					$this->cms->tbname['plugin_kalender_date'],
					$this->cms->tbname['plugin_kalender_lookup_read_date'],
					$this->cms->tbname['papoo_lookup_ug'],
					$this->db->escape($this->checked->kal_id),
					$this->db->escape($this->checked->wy),
					$this->user->userid,
					$this->checked->kal_id
				);
			}
			else {
				$sql = sprintf("	SELECT * FROM %s 
									LEFT JOIN %s ON pkal_date_id=date_id_id
									LEFT JOIN %s ON date_gruppe_lese_id=gruppenid
									WHERE pkal_date_kalender_id='%d'
							AND FROM_UNIXTIME(pkal_date_start_datum) >= date(NOW())
							AND userid='%d' AND pkal_date_kalender_id='%d'
							",
					$this->cms->tbname['plugin_kalender_date'],
					$this->cms->tbname['plugin_kalender_lookup_read_date'],
					$this->cms->tbname['papoo_lookup_ug'],
					$this->db->escape($this->checked->kal_id),
					$this->user->userid,
					$this->checked->kal_id
				);
			}
		}


		$temp_das_resultat_unsortiert = $this->db->get_results($sql, ARRAY_A);
		//print_r($temp_das_resultat_unsortiert);exit;
		usort($temp_das_resultat_unsortiert, "class_cal_front::sortier_kalender_eintraege");

		$this->content->template['termine_des_jahres'] = $temp_das_resultat_unsortiert;
	}

	/**
	 * @param $a
	 * @param $b
	 * @return bool
	 */
	static function sortier_kalender_eintraege($a, $b)
	{
		return $a['pkal_date_start_datum'] > $b['pkal_date_start_datum'];
	}

	/**
	 * class_cal_front::check_rights_von_aussen()
	 * Nach den Schreibrechten checken die der User hat
	 * @return void
	 */
	function check_rights_von_aussen()
	{
		$sql = sprintf("SELECT * FROM %s
									LEFT JOIN %s ON kalender_gruppe_write_id=gruppenid
									WHERE  kalender_wid_id='%d'
									AND userid='%d'",
			$this->cms->tbname['plugin_kalender_lookup_write'],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->db->escape($this->checked->kal_id),
			$this->user->userid
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		if (empty($result)) {
			$this->content->template['kalender_eintrge_von_aussen'] = "";
		}
	}

	/**
	 * class_cal_front::get_aktu_kalender_url()
	 *
	 * @return void
	 */
	function get_aktu_kalender_url()
	{
		if (!isset($this->checked->var1)) {
			$this->checked->var1 = NULL;
		}
		//Zuerst mal die ID
		$kal_split = explode("pkalender", $this->checked->var1);
	}

	/**
	 * class_cal_front::get_url_of_kalender()
	 *
	 * @param $all_cals
	 * @return void
	 */
	function get_url_of_kalender($all_cals)
	{
		//FIX ME 
		//HIer noch die urls pro Men�punkt rausholen damit die menuid stimmt. plugin:pkalender/templates/pkalender_front.html&kal_id=21
		//Dann hat man auch die sprechenden urls!
		if (is_array($all_cals)) {
			foreach ($all_cals as $key => $value) {
				$this->aktuelle[$value['kalender_id']] = $value['kalender_anzahl_der_eintrge_im_modul_aktuellste_eintrge'];
				$surl = "";
				$sql = sprintf("SELECT * FROM %s
									WHERE menulinklang LIKE '%s'
									AND lang_id='%d'",
					$this->cms->tbname['papoo_menu_language'],
					"%plugin:pkalender/templates/pkalender_front.html&kal_id=" . $this->db->escape($value['kalender_id']) . "%",
					$this->cms->lang_id
				);
				$result = $this->db->get_results($sql, ARRAY_A);
				if (!empty($result)) {
					$menuid = $result['0']['menuid_id'];
					if ($this->cms->mod_rewrite == 2) {
						global $menu;
						if (is_object($menu)) {
							foreach ($menu->data_front_complete as $key2 => $value2) {
								if ($menuid == $value2['menuid']) {
									$surl = $value2['menuname_url'];
								}
							}
						}
					}
				}
				else {
					$menuid = 1;
				}
				$this->content->template['url_cal'][$value['kalender_id']] =
					PAPOO_WEB_PFAD . "/plugin.php?menuid=" . $menuid . "&amp;template=pkalender/templates/pkalender_front.html&amp;kal_id=";
				#$this->content->template['url_cal_modrewrite'][$value['kalender_id']]=PAPOO_WEB_PFAD."/".$surl."";
				$this->content->template['url_cal2'][$value['kalender_id']] =
					PAPOO_WEB_PFAD . "/plugin.php?menuid=" . $menuid . "&amp;template=pkalender/templates/pkalender_front.html&amp;kal_id=";
			}
		}
		if (is_object($this->checked)) {
			foreach ($this->checked as $key => $value) {
				if (is_numeric($value)) {
					$this->content->template[$key] = $value;
				}
			}
		}
	}

	/**
	 * class_cal_front::get_alle_calender()
	 *
	 * @return array|void
	 */
	function get_alle_calender()
	{
		if (!isset($this->kalender_id_extern)) {
			$this->kalender_id_extern = NULL;
		}
		if (!isset($this->checked->kal_id)) {
			$this->checked->kal_id = NULL;
		}

		if (is_numeric($this->kalender_id_extern)) {
			$this->checked->kal_id = $this->kalender_id_extern;
		}
		$kalid = "";
		if (is_numeric($this->checked->kal_id)) {
			$kalid = "AND kalender_id='" . $this->checked->kal_id . "' ";
		}
		//Daten nach Sprache und Rechten
		$sql = sprintf("SELECT * FROM %s
									LEFT JOIN %s ON kalender_id_id=kalender_id
									LEFT JOIN %s ON kalender_gruppe_lese_id =	gruppenid
									WHERE kalender_lang_id='%d'
									 %s
									AND userid='%d'
									GROUP BY kalender_id",
			$this->cms->tbname['plugin_kalender'],
			$this->cms->tbname['plugin_kalender_lookup_read'],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->cms->lang_id,
			$kalid,
			$this->user->userid
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		return $result;
	}

	/**
	 * class_cal_front::create_cal_vorlage()
	 *
	 * @param $all_cals
	 * @return array
	 */
	function create_cal_vorlage($all_cals)
	{
		if (is_array($all_cals)) {
			foreach ($all_cals as $key => $value) {
				$all_cals[$key]['calender_dat'] = $this->make_calender($value);
			}
		}
		return $all_cals;
	}

	/**
	 * Kalender erstellen
	 *
	 * @param string $cal
	 * @return mixed
	 */
	function make_calender($cal = "")
	{
		$monat = date("m");
		$jahr = date("Y");

		if (!isset($this->checked->date_time)) {
			$this->checked->date_time = NULL;
		}
		if (!isset($this->checked->monats_id)) {
			$this->checked->monats_id = NULL;
		}

		if (is_numeric($this->checked->date_time) && $this->checked->date_time > date("Y")) {
			$jahr = date("Y", $this->checked->date_time);
		}
		else {
			$jahr = date("Y");
		}

		// Liste der Monate
		$n = 1;
		$m = $monat + 12;
		for ($ij = $n; $ij <= $m; $ij++) {
			$monat_array[$ij]['name'] = $this->make_monat(date("F", mktime(0, 0, 0, $ij,
				1, $jahr)));
			$monat_array[$ij]['jahr'] = (date("Y", mktime(0, 0, 0, $ij, 1, $jahr)));
			$monat_array[$ij]['mon_id'] = $ij;
		}
		$this->content->template['monat_array_kal'] = $monat_array;
		$this->content->template['monats_id'] = $this->checked->monats_id;
		if (!empty($this->checked->monats_id)) {
			$monat = $this->checked->monats_id;
		}
		else {
			$this->content->template['monats_id'] = date("n", time());
		}

		IfNotSetNull($this->checked->template);
		IfNotSetNull($this->checked->cal_view);

		$preparePermalinkForMonth = function ($month) {
			return array_merge((array)$month, [
				"permalink" => rtrim(PAPOO_WEB_PFAD, "/")."/plugin.php?".http_build_query([
						"menuid" => (int)$this->checked->menuid,
						"template" => $this->checked->template,
						"kal_id" => (int)$this->checked->kal_id,
						"monats_id" => (int)$month["mon_id"],
						"cal_view" => $this->checked->cal_view,
						"getlang" => $this->checked->getlang,
					])
			]);
		};

		$selectedMonth = (int)$this->checked->monats_id;
		$previousMonth = $selectedMonth > $n && $selectedMonth <= $m ? $selectedMonth - 1 : null;
		$nextMonth = $selectedMonth >= $n && $selectedMonth < $m ? $selectedMonth + 1 : null;

		$this->content->template["calendarPagination"] = [
			"previousMonth" => isset($monat_array[$previousMonth]) ? $preparePermalinkForMonth($monat_array[$previousMonth]) : null,
			"nextMonth" => isset($monat_array[$nextMonth]) ? $preparePermalinkForMonth($monat_array[$nextMonth]) : null,
		];

		// echo $monat;
		// Leere Eintr�ge bis zum ersten Tag in der Tabelle erzeugen
		$firstday = mktime(0, 0, 0, $monat, 1, $jahr);
		$tagderwoche = date("w", $firstday);
		if ($tagderwoche <= 0) {
			$tagderwoche = 7;
		}
		$heute = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

		for ($j = $tagderwoche - 1; $j >= 1; $j--) {
			$tagderwoche_a[] = date("d", $firstday - ($j * 24 * 3600));
		}

		$this->content->template['tagderwoche_a'] = $tagderwoche_a;
		$this->monat = $monat;
		$this->jahr = $jahr;
		// Tage des aktuellen Monats raussuchen
		$anzahl = date("t", mktime(0, 0, 0, $monat, 1, $jahr));

		# Leere Eintr�ge zum Auff�llen der Tabelle am Ende einf�gen
		$tagderwoche_last = date("w", mktime(0, 0, 0, $monat, $anzahl, $jahr));
		if ($tagderwoche_last > 0) {
			for ($j = 7; $j > $tagderwoche_last; $j--) {
				$tagderwoche_last_array[] = $j;
			}
		}
		$this->content->template['tagderwoche_last'] = $tagderwoche_last_array;

		// durchloopen und zuweisen
		for ($tag = 1; $tag <= $anzahl; $tag++) {
			$kal_tage[$tag][] = $tag;
			// Checken ob Montag
			$montag = date("w", mktime(0, 0, 0, $monat, $tag, $jahr)) == 1;
			// Wenn Montag dann zuweisen und neuen tr erzwingen
			if ($montag) {
				$kal_tage[$tag][] = $montag;
			}

			// Anzahl der freien Pl�tze auf 0 setzen
			$this->frei = 0;
			$this->belegt = '';

			// checken ob an dem Tag buchen m�glich ist
			$dertag = mktime(0, 0, 0, $monat, $tag, $jahr);

			$ok = $this->check_date($dertag, $monat, $cal, $jahr);
			$ok2 = $this->check_date($dertag, $monat, $cal, $jahr, "all");
			$kal_tage[$tag]['belegt'] = "";
			if ($ok) {
				$kal_tage[$tag]['link'] = 'ok';
				$kal_tage[$tag]['pkal_date_id'] = $ok['pkal_date_id'];
				$kal_tage[$tag]['termin_name'] = $ok['pkal_date_titel_des_termins'];
				$kal_tage[$tag]['termin_time'] = $dertag;
				$kal_tage[$tag]['alle_termine'] = $ok2;
				$ok['pkal_date_titel_des_termins'] = str_replace(" ", "-", $ok['pkal_date_titel_des_termins']);
				$kal_tage[$tag]['termin_name_url'] = preg_replace("/[^a-z0-9-]/", "", strtolower($ok['pkal_date_titel_des_termins']));
			}
			else {
				$kal_tage[$tag]['link'] = "";
			}

			$kal_tage[$tag]['datum'] = mktime(0, 0, 0, $monat, $tag, $jahr);
			$kal_tage[$tag]["is_today"] = mktime(0, 0, 0, date("m"), date("d"), date("Y")) == $kal_tage[$tag]["datum"];
		}

		// Daten ins Template
		return $kal_tage;
	}

	/**
	 * class_cal_front::check_date()
	 *
	 * @param int $date Timestamp
	 * @param int $monat
	 * @param mixed $cal
	 * @param mixed $jahr
	 * @param bool $all Wenn true: Alle Termine des Tages zur�ckgeben, sonst nur den ersten
	 * @return array|bool|null
	 */
	function check_date($date, $monat, $cal, $jahr = '', $all = false)
	{
		$date = (int)$date;
		if (!$monat) {
			$date_array = getdate($date);
			$monat = $date_array['mon'];
		}
		if (!isset($this->monats_termin[$cal['kalender_id']])) {
			$this->monats_termin[$cal['kalender_id']] = $this->get_alle_termine_des_monats($monat, $cal, $jahr);
		}

		if (is_array($this->monats_termin[$cal['kalender_id']])) {
			foreach ($this->monats_termin[$cal['kalender_id']] as $key => $value) {
				$date = (int)$date;
				# Startdatum berechnen
				$start_timearray = getdate((int)$value['pkal_date_start_datum']);
				$start_date = mktime(0, 0, 0, $start_timearray['mon'], $start_timearray['mday'], $start_timearray['year']);
				$value['pkal_date_start_datum'] = $start_date;
				# Enddatum berechnen
				$end_timearray = getdate((int)$value['pkal_date_end_datum']);
				$end_date = mktime(23, 59, 58, $end_timearray['mon'], $end_timearray['mday'], $end_timearray['year']);
				$value['pkal_date_end_datum'] = $end_date;

				if (($date >= $value['pkal_date_start_datum']) && ($date <= $value['pkal_date_end_datum'])) {
					if (!$all) {
						return $value;
					}
					else {
						$data[] = $value;
					}
				}
			}
		}
		if (!empty($all)) {
			if (!isset($data)) {
				$data = NULL;
			}
			return $data;
		}
		else {
			return false;
		}
	}

	/**
	 * class_cal_front::http_request_open()
	 *
	 * @param mixed $url
	 * @param integer $timeout
	 * @return string
	 */
	private function http_request_open($url, $timeout = 10000)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt($ch, CURLOPT_USERAGENT,
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.8.1.4) Gecko/20070515 Firefox/2.0.0.4");

		$curl_ret = curl_exec($ch);
		//die("$curl_ret");
		curl_close($ch);
		return trim($curl_ret);
	}

	/**
	 * class_cal_front::get_rrule_array()
	 *
	 * @param $data
	 * @param $start
	 * @param $end
	 * @param $ical
	 * @param $value
	 * @param $cal_id
	 * @return void
	 */
	private function get_rrule_array($data, $start, $end, $ical, $value, $cal_id)
	{
		$data1 = explode(';', $data);

		if (is_array($data1)) {
			foreach ($data1 as $key => $value1) {
				//$data2 = explode('=', $value1);
				$return_int[$data2[0]] = $data2[1];
			}
		} else return;

		// Maximalanzahl: COUNT-Variable
		$count = isset($return_int['COUNT']) ? $return_int['COUNT'] : -1;

		//W�chentlich
		if ($return_int['FREQ'] == "WEEKLY") {
			//StopDatum
			if (isset($return_int['UNTIL']))
				$end = $ical->iCalDateToUnixTimestamp($return_int['UNTIL']);
			elseif (is_int($end))
				$end = array('time_stamp' => $end);
			//Frequenz
			$frequenz = isset($return_int['INTERVAL']) ? $return_int['INTERVAL'] : 1;

			//Startwochentag
			$start_day = $return_int['BYDAY'];

			//Erstes Datum ab heute
			if ($end['time_stamp'] > time()) {
				for ($i = $start['time_stamp']; $i < $end['time_stamp']; $i++) {
					$i = $i + (86400 * 7 * $frequenz);

					$start = $ical->iCalDateToUnixTimestamp($value['DTSTART']);
					$stop = $ical->iCalDateToUnixTimestamp($value['DTEND']);

					$return[$i]['pkal_date_id'] = $value['UID'];
					$return[$i]['pkal_date_lang_id'] = 1;
					$return[$i]['pkal_date_kalender_id'] = $cal_id;
					$return[$i]['pkal_date_titel_des_termins'] = $value['SUMMARY'];
					$return[$i]['pkal_date_terminbeschreibung'] = $value['DESCRIPTION'] . " " . $value['LOCATION'];
					$return[$i]['pkal_date_terminbeschreibung'] = stripslashes($return[$i]['pkal_date_terminbeschreibung']);
					$return[$i]['pkal_date_kategorie_im_kalender'] = "0";
					$return[$i]['pkal_date_start_datum'] = $i;
					$return[$i]['pkal_date_start_datum_Date'] = date("d.m.Y", $i);
					$return[$i]['pkal_date_end_datum'] = $i;
					$return[$i]['pkal_date_uhrzeit_beginn'] = $start['time'];
					$return[$i]['pkal_date_uhrzeit_ende'] = $stop['time'];
					$return[$i]['pkal_date_eintrag_im_frontend_freischalten'] = "1";
					$return[$i]['date_gruppe_lese_id'] = "10";
					$return[$i]['userid'] = "10";
					$return[$i]['gruppenid'] = "10";
					$return[$i]['pkal_date_link_zu_terminfeld'] = "";
					if ($count > 0) $count--;
					if ($count == 0) break;
				}
			}
		}
		if (is_array($return)) {
			$this->return_rr = $return;
		}
		else {
			$this->return_rr = array();
		}

	}

	/**
	 * class_cal_front::get_google_dat()
	 *
	 * @param mixed $url
	 * @param $cal_id
	 * @return void
	 */
	private function get_google_dat($url, $cal_id)
	{
		//Einmal / Stunde abrufen
		if (@filemtime(PAPOO_ABS_PFAD . "/templates_c/ical" . $cal_id . ".ics") + 3600 < time()) {
			$data = $this->http_request_open($url);
			$this->diverse->write_to_file("/templates_c/ical" . $cal_id . ".ics", $data);
		}

		require_once(PAPOO_ABS_PFAD . '/plugins/pkalender/lib/icalreader.php');

		$file = PAPOO_ABS_PFAD . '/templates_c/ical' . $cal_id . '.ics';
		$ical = new ical($file);
		$array = $ical->events();
		$i = 0;
		if (is_array($array)) {
			foreach ($array as $key => $value) {
				$tz = NULL;
				// Suche nach Zeitzonenangabe in RECURRENCE-ID
				foreach ($value as $subkey => $subvalue) {
					if (strpos($subkey, 'RECURRENCE-ID;TZID=') === 0) {
						if ($subvalue == $value['DTSTART']) {
							$tz = substr($subkey, 19);
							break;
						}
					}
				}
				// Timestamps extrahieren
				$start = $ical->iCalDateToUnixTimestamp($value['DTSTART'], $tz);
				$stop = $ical->iCalDateToUnixTimestamp($value['DTEND'], $tz);

				$i = $start['time_stamp'];
				$return[$i]['pkal_date_id'] = $value['UID'];
				$return[$i]['pkal_date_lang_id'] = 1;
				$return[$i]['pkal_date_kalender_id'] = $cal_id;
				$return[$i]['pkal_date_titel_des_termins'] = $value['SUMMARY'];
				$return[$i]['pkal_date_terminbeschreibung'] = $value['DESCRIPTION'] . " " . $value['LOCATION'];
				$return[$i]['pkal_date_terminbeschreibung'] = stripslashes($return[$i]['pkal_date_terminbeschreibung']);
				$return[$i]['pkal_date_kategorie_im_kalender'] = "0";
				$return[$i]['pkal_date_start_datum'] = $start['time_stamp'];
				$return[$i]['pkal_date_start_datum_Date'] = date("d.m.Y", $start['time_stamp']);
				$return[$i]['pkal_date_end_datum'] = $stop['time_stamp'];
				$return[$i]['pkal_date_uhrzeit_beginn'] = $start['time'];
				$return[$i]['pkal_date_uhrzeit_ende'] = $stop['time'];
				$return[$i]['pkal_date_eintrag_im_frontend_freischalten'] = "1";
				$return[$i]['date_gruppe_lese_id'] = "10";
				$return[$i]['userid'] = "10";
				$return[$i]['gruppenid'] = "10";
				$return[$i]['pkal_date_link_zu_terminfeld'] = "";

				// Ganzt�gige Termine verarbeiten
				// Enddatum korrigieren, Zeiten auf NULL lassen
				if ($return[$i]['pkal_date_uhrzeit_ende'] === NULL) {
					$return[$i]['pkal_date_end_datum'] = $stop['time_stamp'] + $plus_time - 60;
				}

				#$i++;

				//Hier wiederkehrende Termine verarbeiten
				if (!empty($value['RRULE'])) {
					$this->get_rrule_array($value['RRULE'], $start, time() + (100 * 24 * 3600), $ical, $value, $cal_id);
					foreach ($this->return_rr as $keyc => $valuec) {
						$return[$keyc] = $valuec;
					}
				}
			}
			ksort($return);
		}
		/**
		 * [pkal_date_id] => 37
		 * [pkal_date_lang_id] => 1
		 * [pkal_date_kalender_id] => 25
		 * [pkal_date_titel_des_termins] => asdasd
		 * [pkal_date_terminbeschreibung] => <p>asdasd</p>
		 * [pkal_date_kategorie_im_kalender] => 0
		 * [pkal_date_start_datum] => 1332284400
		 * [pkal_date_end_datum] => 1332885600
		 * [pkal_date_uhrzeit_beginn] =>
		 * [pkal_date_uhrzeit_ende] =>
		 * [pkal_date_veranstaltung_wiederholt_sich_so_oft] =>
		 * [pkal_date_an_jedem] =>
		 * [pkal_date_link_zu_terminfeld] =>
		 * [pkal_date_eintrag_im_frontend_freischalten] => 1
		 * [date_id_id] => 37
		 * [date_gruppe_lese_id] => 10
		 * [userid] => 10
		 * [gruppenid] => 10
		 */
		return $return;
	}

	/**
	 * class_cal_front::get_alle_termine_des_monats()
	 *
	 * @param string $monat
	 * @param mixed $cal
	 * @param string $jahr
	 * @return array|void|null
	 */
	function get_alle_termine_des_monats($monat = "", $cal, $jahr = "")
	{
		if (!empty($cal['kalender_xml_google'])) {
			$result = $this->get_google_dat($cal['kalender_xml_google'], $cal['kalender_id']);
		}

		$monat_start = $monat - 1;
		$start = mktime(0, 0, 0, $monat_start, 1, $jahr);
		$start1 = mktime(0, 0, 0, $monat, 1, $jahr);
		$end1 = mktime(0, 0, 0, $monat, 31, $jahr);
		$end = mktime(0, 0, 0, $monat, 100, $jahr);

		if (!isset($this->checked->wy)) {
			$this->checked->wy = NULL;
		}

		if (is_numeric($this->checked->wy) && $this->checked->wy == date("Y")) {
			$end1 = mktime(0, 0, 0, 12, 31, $jahr);
			$end = mktime(0, 0, 0, 12, 31, $jahr);
			$this->content->template['jahresuebersicht'] = "ok";
		}

		if (is_numeric($this->checked->wy) && $this->checked->wy == (date("Y") + 1)) {
			$start1 = mktime(0, 0, 0, 1, 1, $jahr + 1);

			$end1 = mktime(0, 0, 0, 12, 31, $jahr + 1);
			$end = mktime(0, 0, 0, 12, 31, $jahr + 1);
			$this->content->template['jahresuebersicht_plus1'] = "ok";
		}
		//$end += (3600*24-1);
		$end1 += (3600*24-1);

		//WEnn es schon einen Result gibt...
		if (!empty($result) && is_array($result)) {
			foreach ($result as $key => $value) {

				//Nur Termine, die (teilweise) im Monat liegen, �bernehmen
				if ($value['pkal_date_start_datum'] >= $start1 && $value['pkal_date_start_datum'] <= $end1 ||
					$value['pkal_date_end_datum'] >= $start1 && $value['pkal_date_end_datum'] <= $end1)
				{
					$neu[$key] = $value;
				}
			}
		}
		if (!isset($neu)) {
			$neu = NULL;
		}
		$result = $neu;

		/**
		 * Zuerst alle Termine die
		 * vor dem ersten angefangen haben und diesen Monat enden
		 *
		 * Dann alle die diesen Monat anfange und diesen Monat endene
		 *
		 * Dann alle die diesen Monat anfangen aber sp�ter enden
		 *    OR
		 * pkal_date_start_datum>='%d'
		 * AND pkal_date_end_datum<='%d'
		 * AND userid='%d'
		 * AND pkal_date_kalender_id='%d'
		 * AND pkal_date_eintrag_im_frontend_freischalten='1'
		 *
		 * OR
		 * pkal_date_start_datum>='%d'
		 * AND userid='%d'
		 * AND pkal_date_kalender_id='%d'
		 * AND pkal_date_eintrag_im_frontend_freischalten='1'
		 **/

		$sql = sprintf("SELECT DISTINCT * FROM %s
									LEFT JOIN %s ON pkal_date_id=date_id_id
									LEFT JOIN %s ON date_gruppe_lese_id=gruppenid
									
									WHERE pkal_date_start_datum<='%d'
									AND pkal_date_end_datum<='%d'
									AND pkal_date_end_datum>='%d'
									AND userid='%d'
									AND pkal_date_kalender_id='%d'
									AND pkal_date_eintrag_im_frontend_freischalten='1'
									
								OR 
									pkal_date_start_datum>='%d'
									AND pkal_date_end_datum<='%d'
									AND userid='%d'
									AND pkal_date_kalender_id='%d'
									AND pkal_date_eintrag_im_frontend_freischalten='1'
									
									OR 
									pkal_date_start_datum>='%d'
									AND pkal_date_start_datum<='%d'									
									AND userid='%d'
									AND pkal_date_kalender_id='%d'
									AND pkal_date_eintrag_im_frontend_freischalten='1'
									
									
									GROUP BY pkal_date_id
									ORDER BY pkal_date_start_datum ASC
									",
			$this->cms->tbname['plugin_kalender_date'],
			$this->cms->tbname['plugin_kalender_lookup_read_date'],
			$this->cms->tbname['papoo_lookup_ug'],
			$start1,
			$end,
			$start1,
			$this->user->userid,
			$cal['kalender_id'],

			$start1,
			$end1,
			$this->user->userid,
			$cal['kalender_id'],

			$start1,
			$end1,
			$this->user->userid,
			$cal['kalender_id']
		);
		if (empty($result)) {
			$result = $this->db->get_results($sql, ARRAY_A);
		}

		# Sortiert alle Termine nach Datum *und* Uhrzeit
		if ($result !== NULL)
			usort($result, array($this, 'cmp_termine'));

		$this->content->template['alle_termin_des_monats'][$cal['kalender_id']] = $result;
		return $result;
	}

	/**
	 * class_cal_front::cmp_termine()
	 * Sortierfunktion f�r die Termine. Z.B. f�r usort()
	 * Sortiert alle Eintr�ge nach Startdatum und -uhrzeit und bei
	 * gleichzeitigen Terminen auch nach Enddatum und -uhrzeit.
	 *
	 * @param $a
	 * @param $b
	 * @return int
	 */
	protected function cmp_termine(&$a, &$b)
	{
		$keys = array('pkal_date_start_datum', 'pkal_date_uhrzeit_beginn', 'pkal_date_end_datum', 'pkal_date_uhrzeit_ende', 'pkal_date_titel_des_termins');
		$result = 0;

		foreach ($keys as $key) {
			if ($a[$key] == $b[$key])
				continue;
			return ($a[$key] <= $b[$key]) ? -1 : 1;
		}
		return 0;
	}

	/**
	 * class_cal_front::get_alle_termine_ab_jetzt()
	 *
	 * @param mixed $cal
	 * @param int $count
	 * @param int $days_limit
	 * @return array|void
	 */
	function get_alle_termine_ab_jetzt($cal = array(), $count = 15, $days_limit = 365)
	{
		$count = is_numeric($count) ? (int)abs($count) : 15;
		if (!empty($cal[0]['kalender_xml_google'])) {
			$result = $this->get_google_dat($cal['0']['kalender_xml_google'], $cal['0']['kalender_id']);
		}

		$start = time() - 86400 * 30;
		$end = time() + $days_limit * 24 * 3600;

		//Wenn es schon einen Result gibt...
		if (!empty($result)) {
			$i = 0;
			if (is_array($result)) {
				foreach ($result as $key => $value) {
					if ($i >= $count) break;
					// Nur Termine, die (teils)) in der Zukunft liegen, �bernehmen
					if (
						($value['pkal_date_start_datum'] > $start && $value['pkal_date_start_datum'] < $end) ||
						($value['pkal_date_end_datum'] > $start)
					) {
						$neu[$key] = $value;
						$i++;


					}
					//$neu[$key]=$value;


				}
			}
		}
		IfNotSetNull($neu);
		$result = $neu;
		$sql = sprintf("SELECT DISTINCT * FROM %s
									LEFT JOIN %s ON pkal_date_id=date_id_id
									LEFT JOIN %s ON date_gruppe_lese_id=gruppenid
									WHERE pkal_date_start_datum>='%d'
									AND pkal_date_end_datum<='%d'
									AND userid='%d'
									AND pkal_date_eintrag_im_frontend_freischalten='1'
									ORDER BY pkal_date_start_datum ASC
									LIMIT %d									
									",
			$this->cms->tbname['plugin_kalender_date'],
			$this->cms->tbname['plugin_kalender_lookup_read_date'],
			$this->cms->tbname['papoo_lookup_ug'],
			$start,
			$end,
			$this->user->userid,
			$count
		);
		if (empty($result)) {
			$result = $this->db->get_results($sql, ARRAY_A);
		}
		/* L�sche heutige, bereits vergangene Termine */
		$todelete = array();
		$now = time() - (86400 * 30);
		$nowhour = strftime('%H');
		$nowminute = strftime('%M');
		$nowtime = $nowhour * 60 + $nowminute;

		if (is_array($result)) {
			foreach ($result as $key => $value) {
				if ($value['pkal_date_start_datum'] < $now) {
					list($hour, $minute) = explode(':', $value['pkal_date_uhrzeit_beginn']);
					$time = $hour * 60 + $minute;
					if ($time < $nowtime)
						$todelete[] = $key;
				}
			}
		}

		$todelete = array_reverse($todelete);
		foreach ($todelete as $index)
			unset($result[$index]);

		if (is_array($result)) {
			//FIXME: index kalender_id ist eigentlich nicht gesetzt.
			IfNotSetNull($cal['kalender_id']);
			$this->content->template['alle_termin_des_monats'][$cal['kalender_id']] = array_values($result);
		}
		return $result;
	}

	/**
	 * Monatsnamen korrekt darstellen
	 *
	 * @param $mon
	 * @return string
	 */
	function make_monat($mon)
	{
		if ($this->cms->lang_id == 2) {
			return $mon;
		}
		else {
			switch ($mon) {
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
			$this->monats_name = $mon;
			return $mon;
		}
	}
}

$class_cal_front = new class_cal_front();
$class_cal_front->class_datums = $class_datums;
