<?php

/**
#####################################
#                                   #
# Plugin:  	umfrage                 #
# Autor(c):	Stephan Bergmann        #
#          	aka b.legt              #
# http://www.sprechomat.de          #
#                                   #
#####################################
*/

/**
 * Class umfrage_class
 */
class umfrage_class
{
	var $db_praefix;
	var $sprach_id;
	var $antwort_jetzt;
	var $check;

	function __construct()
	{
		//globale papoo-Klassen einbinden
		global $cms, $module, $db, $checked, $content, $menu, $db_praefix;
		$this->cms = & $cms;
		$this->module = & $module;
		$this->db = & $db;
		$this->checked = & $checked;
		$this->content = & $content;
		$this->menu=& $menu;
		$this->db_praefix = $db_praefix;

		$this->sprach_id = $this->cms->lang_id;
		/** @var bool antwort_jetzt wird true, wenn die Antwort in diesem Aufruf eingetragen wurde. */
		$this->antwort_jetzt = false;

		$this->make_umfrage();
	}

	/**
	 * Weiche f�r weitere Aktionen
	 */
	function make_umfrage()
	{
		if ($_SESSION['sessionuserid']) {
			$this->check = $this->checkForVoting();
		}
		//(Ergebnis-Button statt Textlink
		if (isset($this->checked->ergebnis) && $this->checked->ergebnis) {
			$location_url = "plugin.php?menuid=".$this->checked->menuid."&template=umfrage/templates/umfrage_front.html&umfrage_id=".$this->checked->umfrage_id;
			header("Location: $location_url");
			exit();
		}
		if (!@$_SESSION['umfrage_ids']) {
			$_SESSION['umfrage_ids'] = array();
		}

		//$_SESSION['umfrage_ids'] = array(); // F�r Tests: R�cksetzen Marker ob schon an Umfrage teilgenommen

		global $template;

		if (!defined("admin")) {
			if ($this->module->module_aktiv['mod_umfrage_plugin']) {
				// Frontend - Antwort z�hlen
				if (@$this->checked->umfrage_id && @$this->checked->antwort_id && @$this->checked->umfrage_stimme_abgeben) {
					$this->antwort_eintragen($this->checked->umfrage_id, $this->checked->antwort_id);
				}

				// Umfrage-Formular anzeigen
				$this->umfrage_formular_anzeigen($this->umfragen_aktiv_id());

				// Frontend - Ergebnis anzeigen
				if (strpos("XXX".$template, "umfrage_front.html")) {
					$this->ergebnis_anzeigen();
				}
			}
		}
		else {
			$this->sprach_id = $this->cms->lang_back_id;

			// Backend Umfrage
			if (strpos("XXX".$template, "umfrage_backend.html")) {
				$this->switch_back();
			}
		}
	}

	/**
	 * @return array|null
	 */
	function checkForVoting()
	{
		$aktu_id = $this->umfragen_aktiv_id();
		$sql = sprintf("SELECT usr_id FROM %s WHERE usr_id = '%d' AND umf_id = '%d'",
			$this->db_praefix."umfrage_user",
			$_SESSION['sessionuserid'],
			$aktu_id
		);
		return $this->db->get_var($sql);
	}

	// ***********************
	// **   FRONTEND        **
	// ***********************

	/**
	 * Content-Daten des Umfrage-Formulars f�llen
	 *
	 * @param int $umfrage_id
	 */
	function umfrage_formular_anzeigen($umfrage_id = 0)
	{
		if (!$umfrage_id) {
			// Modul deaktivieren
			$this->module->module_aktiv['mod_umfrage_plugin'] = false;
		}
		else {
			$this->content->template['mod_umfrage_id'] = $umfrage_id;

			$this->content->template['mod_umfrage_name'] = $this->umfragen_get_name($umfrage_id);
			// Umfrage-Modul ausblenden, wenn kein Name der Umfrage (in entsprechender Sprache) besteht.
			if (empty($this->content->template['mod_umfrage_name'])) {
				$this->module->module_aktiv['mod_umfrage_plugin'] = false;
			}
			else {
				if(!$this->check) {
					// 1. Hat noch nicht an Umfrage teilgenommen
					if (!in_array($umfrage_id, $_SESSION['umfrage_ids'])) {
						$this->content->template['mod_umfrage_weiche'] = "UMFRAGE";
						$this->content->template['mod_umfrage_infos'] = $this->antworten_liste($umfrage_id);
					}
					// 2. hat jetzt gerade an der Umfrage teilgenommen
					elseif ($this->antwort_jetzt) {
						$this->content->template['mod_umfrage_weiche'] = "ANTWORT_JETZT";
					}
					// 3. hat schon an der Umfrage teilgenommen
					else {

					}
				}
				// 4. Test ob zur aktuellen Umfrage schon Ergebnisse vorliegen.
				// Falls nicht, dann Link zu Ergebnissen ausblenden
				if ($this->umfragen_anzahl_antworten($umfrage_id)) {
					$this->content->template['mod_umfrage_link_ergebnisse'] = true;
				}
				else {
					$this->content->template['mod_umfrage_link_ergebnisse'] = false;
				}
			}
		}
	}

	/**
	 * Antwort zu einer Umfrage eintragen
	 *
	 * @param int $umfrage_id
	 * @param int $antwort_id
	 */
	function antwort_eintragen($umfrage_id = 0, $antwort_id = 0)
	{
		if ($umfrage_id && $antwort_id && !in_array($umfrage_id, $_SESSION['umfrage_ids'])) {

			// 1. Antwort im Umfrage-Tabelle eintragen
			$sql = sprintf("UPDATE %s SET umf_count=umf_count+1, umf_datum_letzter=NOW() WHERE umf_id='%d'",
				$this->db_praefix."umfrage",
				$umfrage_id
			);
			$this->db->query($sql);

			// 2. Antwort im Antworten-Tabelle eintragen
			$sql = sprintf("UPDATE %s SET umfant_count=umfant_count+1 WHERE umfant_umf_id='%d' AND umfant_id='%d'",
				$this->db_praefix."umfrage_antworten",
				$umfrage_id,
				$antwort_id
			);
			$this->db->query($sql);

			//wenn angemeldet
			if($_SESSION['sessionuserid']){
				// 3. User in Datenbank eintragen, sodass dieser nur einmal die Umfrage durchführen kann
				$sql = sprintf("INSERT INTO %s SET usr_id = '%s', umf_id='%s'",
					$this->db_praefix."umfrage_user",
					$_SESSION['sessionuserid'],
					$umfrage_id
				);
				$this->db->query($sql);
			}

			// 3. Marker setzten f�r Erkennung ob schon an Umfrage teilgenommen hat.
			$this->antwort_jetzt = true;
			$_SESSION['umfrage_ids'][$umfrage_id] = $umfrage_id;
		}
	}

	/**
	 * Anzeigen des Ergebnis einer Umfrage
	 */
	function ergebnis_anzeigen()
	{
		// 1. Umfrage-ID ermitteln
		if ($this->checked->umfrage_id > 0) $umfrage_id = $this->checked->umfrage_id;
		else $umfrage_id = $this->umfragen_aktiv_id();

		// 2. Statistische Werte ermitteln
		$umfrage = $this->umfragen_data($umfrage_id);
		$teilnehmer = $umfrage[0]['umf_count'];

		$antworten = $this->antworten_liste($umfrage_id);
		$antworten_infos = array();
		$stimmen_max = 0;
		if (!empty($antworten)) {
			// maximale Stimmen-Anzahl einer Antwort bestimmen
			foreach($antworten as $antwort) {
				if ($antwort['umfant_count'] > $stimmen_max) {
					$stimmen_max = $antwort['umfant_count'];
				}
			}

			foreach($antworten as $antwort) {
				$temp_antwort = array();
				$temp_antwort['text'] = $antwort['umfantlan_text'];
				$temp_antwort['stimmen'] = $antwort['umfant_count'];
				//auf eine Kommastelle erweitert
				//$prozent = round(($antwort['umfant_count'] * 100/ $teilnehmer), 0);
				if ($teilnehmer) {
					$prozent = round(($antwort['umfant_count'] * 100/ $teilnehmer), 1);
				}
				else {
					$prozent = 0;
				}
				$temp_antwort['prozent'] = $prozent;

				if ($stimmen_max) {
					$breite = round(($antwort['umfant_count'] * 90/ $stimmen_max), 0) + 1;
				}
				else {
					$breite = 0;
				}
				$temp_antwort['breite'] = $breite;

				$antworten_infos[] = $temp_antwort;
			}
		}

		// 3. Ergebniss an die Content-Variablen �bergeben
		$this->content->template['title'] = $this->content->template['UMFMSG_TITEL']." ".$umfrage[0]['umflan_text'];
		$this->content->template['umfrage_name'] = $umfrage[0]['umflan_text'];
		$this->content->template['umfrage_teilnehmer'] = $teilnehmer;
		$this->content->template['antworten_infos'] = $antworten_infos;
		$this->content->template['umfragen_infos'] = $this->umfragen_liste();

		$this->content->template['umf_datum_start'] = $umfrage[0]['umf_datum_start'];
		$this->content->template['umf_datum_letzter'] = $umfrage[0]['umf_datum_letzter'];
	}


	// ***********************
	// **   BACKEND        **
	// ***********************

	/**
	 * Weiche f�r das Backend
	 */
	function switch_back()
	{
		switch ($this->checked->umfrage_switch) {
			// Alles zu Antworten
		case "ANTWORTEN NEU":
			$this->content->template['umfrage_id'] = $this->checked->umfrage_id;
			$this->content->template['umfrage_name'] = $this->umfragen_get_name($this->checked->umfrage_id);

			$this->content->template['umfrage_infos'] = $this->sprachen_laden();

			$this->content->template['umfrage_weiche'] = "ANTWORTEN NEU_EDIT";
			$this->content->template['umfrage_modus'] = "NEU";
			break;

		case "ANTWORTEN EDIT":
			$this->content->template['umfrage_id'] = $this->checked->umfrage_id;
			$this->content->template['umfrage_name'] = $this->umfragen_get_name($this->checked->umfrage_id);

			$this->content->template['umfrage_antwort_id'] = $this->checked->antwort_id;
			$antwort = $this->antworten_data($this->checked->antwort_id, "all_lang");
			$this->content->template['umfrage_infos'] = $antwort;
			$this->content->template['umfrage_weiche'] = "ANTWORTEN NEU_EDIT";
			$this->content->template['umfrage_modus'] = "EDIT";
			break;

		case "ANTWORTEN DELETE":
			$this->content->template['umfrage_id'] = $this->checked->umfrage_id;
			$this->content->template['umfrage_antwort_id'] = $this->checked->antwort_id;
			$antwort = $this->antworten_data($this->checked->antwort_id);
			$this->content->template['umfrage_antwort_name'] = $antwort[0]['umfantlan_text'];
			$this->content->template['umfrage_weiche'] = "ANTWORTEN DELETE";
			break;

		case "ANTWORTEN DO-DELETE":
			$this->antworten_delete($this->checked->antwort_id);
			$this->content->template['umfrage_backendmessage'] = $this->content->template['UMFMSG_B_ANTWORT_LOESCHEN_NACHRICHT'];

			// Rest ist wie "ANTWORTEN LISTE";
			$this->content->template['umfrage_id'] = $this->checked->umfrage_id;
			$this->content->template['umfrage_name'] = $this->umfragen_get_name($this->checked->umfrage_id);
			$antworten_liste = $this->antworten_liste($this->checked->umfrage_id);
			if (!empty($antworten_liste)) {
				$this->content->template['umfrage_infos'] = $antworten_liste;
			}
			else {
				$this->content->template['umfrage_infos'] = null;
			}
			$this->content->template['umfrage_weiche'] = "ANTWORTEN LISTE";
			break;

		case "ANTWORTEN SAVE":
			if ($this->checked->umfrage_modus_neu) {
				$this->antworten_save("NEU");
			}
			else {
				$this->antworten_save("EDIT");
			}

			$this->content->template['umfrage_backendmessage'] = $this->content->template['UMFMSG_B_ANTWORT_SICHERN_NACHRICHT'];
			// jetzt kein break, da Antworten-Liste ausgegeben wird.

		case "ANTWORTEN LISTE":
			$this->content->template['umfrage_id'] = $this->checked->umfrage_id;
			$this->content->template['umfrage_name'] = $this->umfragen_get_name($this->checked->umfrage_id);
			$antworten_liste = $this->antworten_liste($this->checked->umfrage_id);
			if (!empty($antworten_liste)) {
				$this->content->template['umfrage_infos'] = $antworten_liste;
			}
			else {
				$this->content->template['umfrage_infos'] = null;
			}
			$this->content->template['umfrage_weiche'] = "ANTWORTEN LISTE";
			break;

			// Alles zu Umfragen
		case "UMFRAGEN NEU":
			$this->content->template['umfrage_infos'] = $this->sprachen_laden();
			$this->menu->data_front_complete =$this->menu->menu_data_read("FRONT");
			$this->content->template['menulist_data'] = $this->menu->data_front_complete;

			$this->content->template['umfrage_weiche'] = "UMFRAGEN NEU_EDIT";
			$this->content->template['umfrage_modus'] = "NEU";
			break;

		case "UMFRAGEN EDIT":
			$umfrage = $this->umfragen_data($this->checked->umfrage_id, "all_lang");
			$this->menu->data_front_complete =$this->menu->menu_data_read("FRONT");
			$this->content->template['menulist_data'] = $this->menu->data_front_complete;
			$this->content->template['umfrage_id'] = $umfrage[0]['umf_id'];
			$this->content->template['umfrage_infos'] = $umfrage;
			$this->content->template['umfrage_menuid'] = $umfrage[0]['umf_menu'];
			$this->content->template['umfrage_weiche'] = "UMFRAGEN NEU_EDIT";
			$this->content->template['umfrage_modus'] = "EDIT";
			break;

		case "UMFRAGEN DELETE":
			$this->content->template['umfrage_id'] = $this->checked->umfrage_id;
			$this->content->template['umfrage_name'] = $this->umfragen_get_name($this->checked->umfrage_id);
			$this->content->template['umfrage_infos'] = $umfrage;
			$this->content->template['umfrage_weiche'] = "UMFRAGEN DELETE";
			break;

		case "UMFRAGEN DO-DELETE":
			$this->umfragen_delete($this->checked->umfrage_id);
			$this->content->template['umfrage_backendmessage'] = $this->content->template['UMFMSG_B_UMFRAGE_LOESCHEN_NACHRICHT'];

			// Rest ist wie "default";
			$this->content->template['umfrage_infos'] = $this->umfragen_liste();
			$this->content->template['umfrage_weiche'] = "UMFRAGEN LISTE";
			break;

		case "UMFRAGEN SAVE":
			if ($this->checked->umfrage_modus_neu) {
				$this->umfragen_save("NEU");
			}
			else {
				$this->umfragen_save("EDIT");
			}

			$this->content->template['umfrage_backendmessage'] = $this->content->template['UMFMSG_B_UMFRAGE_SICHERN_NACHRICHT'];
			// jetzt kein break; da Umfragen-List ausgegeben wird.

		default:
			$this->content->template['umfrage_infos'] = $this->umfragen_liste();
			$this->content->template['umfrage_weiche'] = "UMFRAGEN LISTE";
			break;
		}
	}

	/**
	 * gibt eine Liste aller Umfragen zur�ck
	 *
	 * @return array|bool|void
	 */
	function umfragen_liste()
	{
		$umfragen_liste = array();
		$sql = sprintf("SELECT * FROM %s AS t1, %s AS t2 WHERE t1.umf_id=t2.umflan_umf_id AND t2.umflan_lan_id='%d' ORDER BY t1.umf_id DESC",
			$this->db_praefix."umfrage",
			$this->db_praefix."umfrage_language",
			$this->sprach_id
		);
		$umfragen_liste = $this->db->get_results($sql, ARRAY_A);

		if (!empty($umfragen_liste)) {
			return $umfragen_liste;
		}
		else {
			return false;
		}
	}

	/**
	 * gibt die Daten der Umfrage $umfrage_id zur�ck
	 *
	 * @param int $umfrage_id
	 * @param string $modus
	 *  "all_lang" = f�r alle Sprachen;
	 *  0 oder leer = nur aktive Sprache
	 * @return array|bool|void
	 */
	function umfragen_data($umfrage_id = 0, $modus = "")
	{
		$umfragen_data = array();

		if ($umfrage_id) {
			switch ($modus) {
			case "all_lang":
				$sql_add1 = ", ".$this->db_praefix."papoo_name_language AS t3";
				$sql_add2 = " AND t2.umflan_lan_id=t3.lang_id";
				break;

			default:
				$sql_add1 = "";
				$sql_add2 = " AND t2.umflan_lan_id='".$this->sprach_id."'";
				break;
			}

			$sql = sprintf("SELECT * FROM %s AS t1, %s AS t2 %s WHERE t1.umf_id=t2.umflan_umf_id AND t1.umf_id='%d' %s ORDER BY t2.umflan_lan_id",
				$this->db_praefix."umfrage",
				$this->db_praefix."umfrage_language",
				$sql_add1,
				$umfrage_id,
				$sql_add2
			);
			$umfragen_data = $this->db->get_results($sql, ARRAY_A);

		}
		if (!empty($umfragen_data)) {
			return $umfragen_data;
		}
		else {
			return false;
		}
	}

	/**
	 * gibt die ID der aktuell aktiven Umfrage zur�ck
	 *
	 * @return bool|int|null
	 */
	function umfragen_aktiv_id()
	{
		$umfrage_id = 0;

		$sql = sprintf("SELECT umf_id AS umfrage_id FROM %s WHERE umf_aktiv_janein='ja' AND umf_menu='%s' OR umf_aktiv_janein='ja' AND umf_menu='0' ORDER BY umf_menu DESC LIMIT 1",
			$this->db_praefix."umfrage",
			$this->db->escape($this->checked->menuid)
		);
		// Durch ORDER BY umf_menu DESC wird der Umfrage der Vorzug gegeben, welche einem bestimmten Men�-Punkt zugewiesen ist.
		// Sie "�berschreibt" damit eine Umfrage welche allen Men�-Punkte (=0) zugewiesen ist.
		//echo "SQL: ".$sql;
		$umfrage_id = $this->db->get_var($sql);

		if (!empty($umfrage_id)) {
			return $umfrage_id;
		}
		else {
			return false;
		}
	}

	/**
	 * gibt den Name der Umfrage $umfrage_id zur�ck
	 *
	 * @param int $umfrage_id
	 * @return bool|string|null
	 */
	function umfragen_get_name($umfrage_id = 0)
	{
		$name = "";
		if ($umfrage_id) {
			$sql = sprintf("SELECT umflan_text AS name FROM %s AS t1, %s AS t2 WHERE t1.umf_id=t2.umflan_umf_id AND t1.umf_id='%d' AND t2.umflan_lan_id='%d'",
				$this->db_praefix."umfrage",
				$this->db_praefix."umfrage_language",
				$umfrage_id,
				$this->sprach_id
			);
			$name = $this->db->get_var($sql);
		}

		if (!empty($name)) {
			return $name;
		}
		else {
			return false;
		}
	}

	/**
	 * Ermittelt die Anzahl der Antworten zu Umfrage $umfrage_id
	 *
	 * @param int $umfrage_id
	 * @return bool|int|null
	 */
	function umfragen_anzahl_antworten($umfrage_id = 0)
	{
		$anzahl = 0;
		if ($umfrage_id) {
			$sql = sprintf("SELECT umf_count AS anzahl FROM %s WHERE umf_id='%d'",
				$this->db_praefix."umfrage",
				$umfrage_id
			);
			$anzahl = $this->db->get_var($sql);
		}

		if (!empty($anzahl)) {
			return $anzahl;
		}
		else {
			return false;
		}
	}

	/**
	 * Speichern die Daten einer Umfrage je nach $modus
	 *
	 * @param int|string $modus
	 *  "NEU": eine neue Umfrage anlegen
	 *  "EDIT": Daten der Umfrage $this->checked->umfrage_id �ndern
	 * @return bool|void
	 */
	function umfragen_save($modus = 0)
	{
		if ($modus) {
			$sql_anfang = "";
			$sql_felder = "";
			$sql_ende = "";

			// 1. Wenn diese Umfrage aktiv ist, alle anderen Umfragen deaktivieren
			if ($this->checked->umfrage_aktiv_janein == "ja") $this->umfragen_aktiv_reset();

			// 2. allgemeine Umfrage-Daten speichern
			$sql_felder = sprintf(	"SET umf_aktiv_janein='%s', umf_menu='%s' ",
				$this->db->escape($this->checked->umfrage_aktiv_janein),
				$this->db->escape($this->checked->cattext)
			);

			if ($modus == "NEU") {
				$sql_anfang = sprintf("INSERT INTO %s ",
					$this->db_praefix."umfrage"
				);
				$sql_ende = ", umf_datum_start=NOW()";
			}
			else {
				$sql_anfang = sprintf("UPDATE %s ",
					$this->db_praefix."umfrage"
				);
				$sql_ende = sprintf(	"WHERE umf_id='%d'",
					$this->checked->umfrage_id
				);
			}

			$sql = $sql_anfang.$sql_felder.$sql_ende;

			$this->db->get_results($sql);

			// 3. Umfrage-ID ermitteln
			if ($modus == "NEU") {
				$umfrage_id = $this->db->insert_id;
			}
			else {
				$umfrage_id = $this->checked->umfrage_id;
			}

			// 4. Texte der verschiedenen Sprachen speichern
			// 4.a alte Texte l�schen
			$sql = sprintf("DELETE FROM %s WHERE umflan_umf_id='%d'",
				$this->db_praefix."umfrage_language",
				$umfrage_id
			);
			$this->db->query($sql);

			// 4.b neue Texte einf�gen
			foreach($this->checked->umfrage_text as $sprach_id => $text) {
				$sql = sprintf("INSERT INTO %s SET umflan_umf_id='%d', umflan_lan_id='%d', umflan_text='%s'",
					$this->db_praefix."umfrage_language",
					$umfrage_id,
					$sprach_id,
					$this->db->escape($text)
				);
				$this->db->query($sql);
			}
		}
		else {
			return false;
		}
	}

	/**
	 * Setzt aktiv_janein aller Umfragen auf "nein"
	 */
	function umfragen_aktiv_reset()
	{
		$sql = sprintf("UPDATE %s SET umf_aktiv_janein='nein' WHERE umf_menu='%s'",
			$this->db_praefix."umfrage",
			$this->db->escape($this->checked->cattext)
		);
		$this->db->query($sql);
	}

	/**
	 * Umfrage incl. aller Antworten etc. l�schen
	 *
	 * @param int $umfrage_id
	 */
	function umfragen_delete($umfrage_id = 0)
	{
		// 1. Antworten dieser Umfrage ermitteln und l�schen
		$antworten = $this->antworten_liste($umfrage_id);
		if (!empty($antworten)) {
			foreach($antworten as $antwort) {
				$this->antworten_delete($antwort['umfant_id']);
			}
		}

		// 2. Umfrage selbst l�schen
		$sql = sprintf("DELETE FROM %s 
						WHERE umf_id='%d'",
			$this->db_praefix."umfrage",
			$umfrage_id
		);
		$this->db->query($sql);

		$sql = sprintf("DELETE FROM %s 
						WHERE umflan_umf_id='%d'",
			$this->db_praefix."umfrage_language",
			$umfrage_id
		);
		$this->db->query($sql);
	}

	/**
	 * gibt eine Liste aller Antworten der Umfrage $umfrage_id zur�ck
	 *
	 * @param int $umfrage_id
	 * @return array|bool|void
	 */
	function antworten_liste($umfrage_id = 0)
	{
		$antworten_liste = array();
		$sql = sprintf("SELECT * FROM %s AS t1, %s AS t2 WHERE t1.umfant_umf_id='%d' AND t1.umfant_id=t2.umfantlan_umfant_id AND t2.umfantlan_lan_id='%d' ORDER BY t1.umfant_id ASC",
			$this->db_praefix."umfrage_antworten",
			$this->db_praefix."umfrage_antworten_language",
			$umfrage_id,
			$this->sprach_id
		);
		$antworten_liste = $this->db->get_results($sql, ARRAY_A);

		if (!empty($antworten_liste)) {
			return $antworten_liste;
		}
		else {
			return false;
		}
	}

	/**
	 * gibt die Daten der Antwort $antwort_id zur�ck
	 *
	 * @param int $antwort_id
	 * @param string|int $modus
	 *  "all_lang" = f�r alle Sprachen;
	 *  0 oder leer = nur aktive Sprache
	 * @return array|bool|void
	 */
	function antworten_data($antwort_id = 0, $modus = "")
	{
		$antworten_liste = array();

		if ($antwort_id) {
			switch ($modus)
			{
			case "all_lang":
				$sql_add1 = ", ".$this->db_praefix."papoo_name_language AS t3";
				$sql_add2 = " AND t2.umfantlan_lan_id=t3.lang_id";
				break;

			default:
				$sql_add1 = "";
				$sql_add2 = " AND t2.umfantlan_lan_id='".$this->sprach_id."'";
			}

			$sql = sprintf("SELECT * FROM %s AS t1, %s AS t2 %s WHERE t1.umfant_id=t2.umfantlan_umfant_id AND t1.umfant_id='%d' %s ORDER BY t2.umfantlan_lan_id ASC",
				$this->db_praefix."umfrage_antworten",
				$this->db_praefix."umfrage_antworten_language",
				$sql_add1,
				$antwort_id,
				$sql_add2
			);
			$antworten_liste = $this->db->get_results($sql, ARRAY_A);

		}
		if (!empty($antworten_liste)) {
			return $antworten_liste;
		}
		else {
			return false;
		}
	}

	/**
	 *  Speichert die Antworten einer Umfrage je nach $modus
	 *
	 * @param int|string $modus
	 *  "NEU": eine neue Antwort anlegen
	 *  "EDIT": Daten der Antwort $this->checked->antwort_id �ndern
	 * @return bool|void
	 */
	function antworten_save($modus = 0)
	{
		if ($modus) {
			$sql = "";

			// 1. allgemeine Antwort-Daten speichern (nur f�r neue Antworten)
			if ($modus == "NEU") {
				$sql = sprintf(	"INSERT INTO %s SET umfant_umf_id='%d' ",
					$this->db_praefix."umfrage_antworten",
					$this->checked->umfrage_id
				);
				$this->db->get_results($sql);
			}

			// 3. Antwort-ID ermitteln
			if ($modus == "NEU") {
				$antwort_id = $this->db->insert_id;
			}
			else {
				$antwort_id = $this->checked->antwort_id;
			}

			// 4. Texte der verschiedenen Sprachen speichern
			// 4.a alte Texte l�schen
			$sql = sprintf("DELETE FROM %s WHERE umfantlan_umfant_id='%d'",
				$this->db_praefix."umfrage_antworten_language",
				$antwort_id
			);
			$this->db->query($sql);

			// 4.b neue Texte einf�gen
			foreach($this->checked->antwort_text as $sprach_id => $text) {
				$sql = sprintf("INSERT INTO %s SET umfantlan_umfant_id='%d', umfantlan_lan_id='%d', umfantlan_text='%s'",
					$this->db_praefix."umfrage_antworten_language",
					$antwort_id,
					$sprach_id,
					$this->db->escape($text)
				);
				$this->db->query($sql);
			}
		}
		else {
			return false;
		}
	}

	/**
	 * Antwort incl. aller Sprachen l�schen.
	 * Die Zahl der Stimmen dieser Antwort werden von der Teilnehmer-Zahl der Umfrage abgezogen
	 *
	 * @param int $antwort_id
	 */
	function antworten_delete($antwort_id = 0)
	{
		$antwort = $this->antworten_data($antwort_id);

		// 1. Antwort etc. l�schen
		$sql = sprintf("DELETE FROM %s WHERE umfant_id='%d'",
			$this->db_praefix."umfrage_antworten",
			$antwort_id
		);
		$this->db->query($sql);

		$sql = sprintf("DELETE FROM %s WHERE umfantlan_umfant_id='%d'",
			$this->db_praefix."umfrage_antworten_language",
			$antwort_id
		);
		$this->db->query($sql);

		// 2. Anzahl der Teilnehmer korrigieren
		if ($antwort[0]['umfant_count'] > 0) {
			$sql = sprintf("UPDATE %s SET umf_count=umf_count-%d WHERE umf_id='%d'",
				$this->db_praefix."umfrage",
				$antwort[0]['umfant_count'],
				$antwort[0]['umfant_umf_id']
			);
			$this->db->query($sql);
		}
	}

	/**
	 *  Gibt die Sprach-Informationen in einem Array zur�ck
	 * "benamt" ist das Array so, dass es den Namen der Umfrage-Tabellen entspricht
	 *
	 * @return array|bool|void
	 */
	function sprachen_laden()
	{
		$sprachen = array();
		$sql = sprintf("SELECT lang_id AS umflan_lan_id, lang_id AS umfantlan_lan_id, lang_long FROM %s WHERE more_lang='2' ORDER BY lang_id ASC",
			$this->db_praefix."papoo_name_language"
		);
		$sprachen = $this->db->get_results($sql, ARRAY_A);

		if (!empty($sprachen)) {
			return $sprachen;
		}
		else {
			return false;
		}
	}
}

$umfrage = new umfrage_class();
