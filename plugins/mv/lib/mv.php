<?php

$cfg = ActiveRecord\Config::instance();
$cfg->set_model_directories([
	// Plugin Models haben Prioritaet
	__DIR__ . "/models",
	// ... Dann erst die normalen Models
	PAPOO_ABS_PFAD . "/lib/models/"
]);

require __DIR__ . '/tree_view.php';
/**
 * Flexverwaltung GoFlex
 *
 * @package
 * @author  Dr. Carsten Euwens, Frank Stratmann
 * @copyright Copyright (c) 2009 - 2010 Papoo Software
 * @version $ID$
 * @access public
 */
// Inhaltssprache
// $this->cms->lang_back_content_id
#[AllowDynamicProperties]
class mv
{
	const FLEX_TYPE_DEFAULT = 1;
	const FLEX_TYPE_USER = 2;
	const FLEX_TYPE_CALENDAR = 3;

	private static $mvContentFields = [
		'mv_content_id',
		'mv_content_owner',
		'mv_content_userid',
		'mv_content_search',
		'mv_content_sperre',
		'mv_content_teaser',
		'mv_content_create_date',
		'mv_content_edit_date',
		'mv_content_create_owner',
		'mv_content_edit_user',
	];

	protected $save_checked;
	protected $save_from;
	protected $save_mode;
	protected $save_to_type;
	protected $save_new_data;

	public function __construct()
	{
		if (@file_exists(PAPOO_ABS_PFAD . "/plugins/mv/lib/mv_conf.php")) {
			require_once(PAPOO_ABS_PFAD . "/plugins/mv/lib/mv_conf.php");
			$mv_config = new mv_config;
			foreach ($mv_config->cfg as $key => $value) {
				$this->$key = $value;
			}

			// Pagination aus Settings oder Checked übernehmen
			if (isset($this->intervall_front) == false || is_numeric($this->intervall_front) == false || $this->intervall_front < 1) {
				$this->intervall_front = (int)$GLOBALS["cms"]->system_config_data["config_paginierung"];

				if (isset($this->checked->pagination) and ctype_digit($this->checked->pagination)) {
					$this->intervall_front = (int)$this->checked->pagination;
				}

				if ($this->intervall_front === 0) {
					$this->intervall_front = 99999;
				}
			}
		} else {
			/**
			 * Formulare mit brs zwischen label und Feld anzeigen
			 * 1 = anzeigen
			 * 0 = nicht anzeigen (dann muss ein Float Mechanismus genutzt werden
			 */
			$this->showbrs = "0";
			// Wieviel Treffer sollen bei der Suche auf einer Seite angezeigt werden
			// Pagination Count aus der Systemeinstellung uebernehmen
			$this->intervall_front = (int)$GLOBALS["cms"]->system_config_data["config_paginierung"]; // für mv_search_front_onemv.html, mv_show_all_front.html (Frontend)
			$this->intervall_back = 20; // Mitglieder-/Objektsuche im Backend
			// maximale Bilderbreite und -h�he bei Bilduploads
			$this->pic_max_breite = 850;
			$this->pic_max_hoehe = 500;
			$this->is_mv_installed = true;
			// wenn Flag gesetzt dann ist es ein dzvhae Auftritt
			$this->dzvhae_system_id = true;
			// Z�hler f�r den csv Import: 0 bedeutet das die Jahreszahlen hatten alle 4 Ziffern, gr��erer Wert gibt die Anzahl der Importwerte an die nur 2 Ziffern hatten
			$this->import_timestamp = 0;
			// wenn die Jahreszahl (2 Ziffern) kleiner als dieser Wert ist, dann bekommt sie eine 20 vorangesetzt, ansonsten eine 19
			$this->import_timestamp_grenzwert = 11;
			// Detailansicht im Frontend, von welchem Feld abh�ngig?
			$this->detail_feld_id = 64;
			// Sonderfall dzvhae: welche Import Feld IDs haben diese Felder
			$this->dzvhae_feld_benutzername_id = 1;
			$this->dzvhae_feld_nachname_id = 16;
			$this->dzvhae_feld_system_id_id = 93;
			// Sonderfall dzvhae: aus Nachname_16 und SystemID_93 wird der Benutzername generiert, wenn keiner beim Import angegeben wurde
			$this->dzvhae_feld_flex_nachname = "Nachname_16";
			$this->dzvhae_feld_flex_systemid = "SystemID_93";
			//Das Feld bestimmt ob Eintr�ge sichtbar sind bei dzvhae, funktioniert nur wenn MV auf
			//dzvhae steht
			$this->dzvhae_feld_sichtbar = "Sichtbar_65";
			// Anzahl und Ids der Detailansichten
			$this->detail_anzahl = array(
				"1",
				"2",
				"3"
			);
			// Meta ID f�r nicht eingeloggte Besucher
			$this->meta_id_no_login = "1";
			// Meta ID f�r neu angemeldete Benutzer (ist jetzt dynamisch steurbar �ber url extern_meta)
			$this->meta_id_new_user = "5";
			//ANzeigen der Labels in der Auswahlliste
			$this->meta_include_label = false;
			// Meta ID zur Auswahl f�r neu angemeldete Benutzer
			$this->meta_id_choose_new_user = array(
				"1",
				"5"
			); // hier 1, 5 f�r admin, neuer Benutzer
			// Meta ID f�r die Systememails, wenn jemand sich neu anmeldet
			$this->mv_system_email_meta_id = "1";
			// Soll die Lightbox genutzt werden bei den Einzelbildern 1=OK, 0=Nicht benutzen
			$this->mv_show_lightbox_single = "1";
			// Soll die Lightbox genutzt werden bei den Galerien 1=OK, 0=Nicht benutzen
			$this->mv_show_lightbox_galerie = "1";
			//Sortierungsrichtung bei Kalendereintr�gen ASC oder DESC mit Leerzeichen vorne dran!
			$this->kalender_order = " ASC";
			//Wenn ein Eintrag abgelaufen ist eine Mail rausschicken an den Eintr�ger (true/false)
			$this->mv_schick_mail_wenn_abgelaufen = false;
			//Tage vor und nach dem Ablauf eine Mail schicken array(14,8,0,-1);
			$this->mv_abstand_mail_schicken = array(
				8,
				0
			);
			//cc Felder
			#$this->cc1 = "";
			#$this->cc2 = "";
			//Ist nicht dzvhae zum verschicken von Mails
			$this->is_not_dzvhae = false;
			//Bei der Suche nach Objekten alle Metaebenen ber�cksichtigen
			$this->mv_meta_show_all = 1;
			//Soll �berpr�ft werden ob die E-Mail Adresse schon existiert?
			$this->checken_ob_mail_existiert = false;
			$this->zweite_default_gruppe = false;
			$this->output_html_link_for_file_upload_field = true;
			$this->download_protected = false;
			$this->content_sperre_sprachabhaengig = false;
			$this->checkbox_as_selectbox = true;
			$this->mail_to_user_if_admin_creates = false;
			$this->and_not_or_checkbox_search_fe = true;
		}
		global $cms;
		$this->cms = &$cms;
		global $db;
		$this->db = &$db;
		global $message;
		$this->message = &$message;
		global $content;
		$this->content = &$content;
		global $checked;
		$this->checked = &$checked;
		global $intern_menu;
		$this->intern_menu = &$intern_menu;
		global $diverse;
		$this->diverse = &$diverse;
		global $mail_it;
		$this->mail_it = &$mail_it;
		global $user;
		$this->user = &$user;
		global $image_core;
		$this->image_core = &$image_core;
		global $plugin;
		$this->plugin = &$plugin;
		global $menuintcss;
		$this->menuintcss = &$menuintcss;
		global $weiter;
		$this->weiter = &$weiter;
		global $menu;
		$this->menu = &$menu;
		global $module;
		$this->module = &$module;
		global $dumpnrestore;
		$this->dumpnrestore = &$dumpnrestore;

		// Respect the maximum image size of the system configuration
		$maxImageSize = explode('x', preg_replace('~[^\dx]~', '', (string)$cms->config_maximale_bildgrel), 2);
		$imageMaxWidth = (int)$maxImageSize[0] ?: 4000;
		$imageMaxHeight = (int)($maxImageSize[1] ?? 0) ?: 4000;

		$this->pic_max_breite = $imageMaxWidth;
		$this->pic_max_hoehe = $imageMaxHeight;

		$this->make_mv_plugin();
	}

	function output_filter()
	{
		global $output;
		$output = TreeView::replace_variables($output);
	}

	/**
	 * @param string $tablesLikePattern WHERE table_name LIKE '?'; table prefix is added automatically
	 * @param string $column
	 * @return string[]
	 */
	private static function findTablesMissingColumn(string $tablesLikePattern, string $column): array
	{
		/** @var ezSQL_mysqli $db */
		global $db;
		global $db_name, $db_praefix;

		return $db->get_col(
			"SELECT _table.TABLE_NAME ".
			"FROM information_schema.TABLES _table ".
			"LEFT JOIN information_schema.COLUMNS _column ON _column.TABLE_SCHEMA = _table.TABLE_SCHEMA AND _column.TABLE_NAME = _table.TABLE_NAME AND _column.COLUMN_NAME LIKE '{$db->escape($column)}' ".
			"WHERE _table.TABLE_SCHEMA LIKE '{$db->escape($db_name)}' AND _table.TABLE_NAME LIKE '{$db->escape($db_praefix)}{$tablesLikePattern}' AND _column.COLUMN_NAME IS NULL"
		);
	}

	public static function update(): void
	{
		/** @var cms $cms */
		global $cms;
		/** @var ezSQL_mysqli $db */
		global $db;

		// Tabellen aktualisieren
		foreach (self::findTablesMissingColumn('papoo_mv_content_%_search_%', 'mv_content_teaser') as $table) {
			$db->query("ALTER TABLE `{$table}` ADD COLUMN mv_content_teaser tinyint(1) NOT NULL DEFAULT 1 AFTER mv_content_sperre");
		}
	}

	/*
	 * Admin switch
	 */
	function make_mv_plugin()
	{
		self::update();

		#error_reporting(E_ALL);
		//Alle externen IDS auf numerisch pr�fen!!!
		$this->global_checks();
		//global $backend;
		//$this->backend = "hallo"; // ?? s. update_lang_search_row.php
		// Flag setzten, dass das mv Plugin installiert ist, f�r Kernel Funktionen
		#$this->mv->is_mv_installed = 1;
		if ($this->dzvhae_system_id) $this->content->template['is_dzvhae_system'] = "ok";
		if (defined("admin")) {
			if (empty($this->checked->mv_content_sperre)) $this->checked->mv_content_sperre = 0;
			// baut aus dem $_GET einen link zusammen
			$own_link = "";
			if (!empty($_GET)) {
				foreach ($_GET as $argument => $wert) {
					$argument = strip_tags($argument);
					$wert = strip_tags($wert);
					$own_link .= $argument . "=" . $wert . "&amp;";
				}
				$own_link = substr($own_link, 0, -5);
				$this->content->template['mv_own_link'] = $own_link;
			}
			// Rechtegruppen des Users rausholen
			$this->get_users_groups();
			// admin_id globalisieren
			$this->get_admin_id();
			// hat er auch Adminrechte? wenn ja , dann in $this->admin_is_ok speichern
			$this->have_admin_rights();
			// f�r die Metaebene Anzeige im Template das Flag setzen
			if (strstr($this->checked->template, "mv")
				and $this->mv_exist()) $this->content->template['plugin_metaebene'] = "ok";
			// wenn die Metagruppe neu ausgew�hlt werden soll, dann alles auf "Null" setzen
			if ($this->checked->selected_meta_gruppe == "neu") {
				$this->meta_gruppe = "";
				$this->checked->selected_meta_gruppe = "";
				// Hierdurch wird es beim blossem Aufruf der Ebenen-Neuauswahl erforderlich, eine neue Ebene zu w�hlen (alte gew�hlte Ebene geht verloren)
				// Ist zur Zeit aber erforderlich, da �nderungen oder das L�schen an der eingeloggten Ebene zu verhindern sind.
				// TODO: Rechte-Vergabe f�r �nderungen/L�schen unter Beachtung, dass nach der Install nur die Ebene Std. vorhanden ist etc.
				$_SESSION['meta_gruppe_id'] = "";
				$new_is_meta_sel_active = 1;
			} // wenn eine neue Meta ID mitgeschickt wurde, dann wird diese benutzt
			elseif (!empty($this->checked->selected_meta_gruppe)) $_SESSION['meta_gruppe_id'] = $this->meta_gruppe = $this->checked->selected_meta_gruppe;
			// wenn eine SESSION Meta ID existiert, dann diese nehmen
			elseif (!empty($_SESSION['meta_gruppe_id'])) $this->meta_gruppe = $_SESSION['meta_gruppe_id'];
			// Wenn zu Beginn noch keine Metaebene gew�hlt ist, Standard als Default setzen
			// jedoch nicht f�r create_meta.html, damit das L�schen einer Meta- und einer Rechtegruppe m�glich ist
			if ($this->meta_gruppe == ""
				and !$new_is_meta_sel_active
				and $this->checked->template != "mv/templates/create_meta.html"
			)
				$_SESSION['meta_gruppe_id'] = $this->meta_gruppe = 1;
			$meta_id = $this->meta_gruppe;
			// ??? s. 184, auskommentiert
			/*if (!defined("admin")
			AND is_numeric($this->checked->extern_meta)) $this->meta_gruppe = $this->checked->extern_meta;*/
			// die mv_id und den Template Link ans Template f�r Metaebene rechts unter Hilfe
			if (empty($this->checked->mv_id)
				or !is_numeric($this->checked->mv_id)) $war_nix = $this->checked->mv_id = 1;
			$this->content->template['mv_id'] = $this->checked->mv_id;
			$sql = sprintf("SELECT mv_menu_id
							FROM %s
							WHERE mv_id = '%d'
							LIMIT 1",

				$this->cms->tbname['papoo_mv'],

				$this->db->escape($this->checked->mv_id)
			);
			$menu_id = $this->db->get_var($sql);
			$this->content->template['link'] = $_SERVER['PHP_SELF']
				. "?menuid="
				. $menu_id
				. "&template=mv/templates/create_input.html";
			$this->content->template['link_meta'] = $_SERVER['PHP_SELF']
				. "?menuid="
				. $menu_id
				. "&template=mv/templates/create_meta.html";
			// Kalender Felder
			$this->get_kalender_felder();
			//Menuid
			$this->content->template['menuid_aktuell'] = $this->checked->menuid;
			// Name der Metaebene aus der DB holen und ans Template �bergeben
			if (!empty($this->meta_gruppe)) {
				// checken. ob es die Meta Tabelle gibt
				// nicht da, wenn die Flexverwaltung frisch installiert und noch keine neue Verwaltung definiert wurde
				if (in_array($this->cms->tbname['papoo_mv'] . "_meta_" . $this->db->escape($this->checked->mv_id), $this->cms->tbname)) {
					$sql = sprintf("SELECT mv_meta_group_name
									FROM %s
									WHERE mv_meta_id = '%d'
									LIMIT 1",

						$this->cms->tbname['papoo_mv']
						. "_meta_"
						. $this->db->escape($this->checked->mv_id),

						$this->db->escape($this->meta_gruppe)
					);
					$meta_gruppe_name = $this->db->get_var($sql);
					$this->content->template['metaebene_name'] = $meta_gruppe_name;
					$this->content->template['metaebene_name_id'] = $this->meta_gruppe;
				}
			}
			//Lese und Schreibrechte bestimmen
			$this->mv_get_lese_schreibrechte_fuer_meta_ebene();
			if ($war_nix == 1) $this->checked->mv_id = "";
			global $template;
			// checkt, ob eine Backup Datei existiert, die �lter als 15 Minuten ist, wenn ja, dann l�schen
			$this->check_dump();
			// checken, ob die Sprachen schon aus der papoo_name_language Tabelle kopiert wurden
			$this->check_language();
			// Standard Sprache holen
			$this->get_standard_lang();
			$this->user->check_intern();
			$templatedat = basename($template);
			if ($templatedat != "login.html") {
				// Wenn das Template "Mitglieder eintragen" ist, und zum ersten Mal aufgerufen wird,
				// dann $_SESSION Eintr�ge f�r Mutliselectfelder l�schen
				if ($this->checked->template == "mv/templates/fp_content.html"
					|| $this->checked->template == "mv/templates/change_user.html") {
					$this->make_upload_or_picdel();
					if ($this->checked->zweiterunde != "ja") $this->delete_session();
				}
				switch ($this->checked->template) {
					// Die Standardeinstellungen werden bearbeitet
					case "mv/templates/mv_start.html":
						$this->mv_start();
						break;

					// Kalenderfunktion
					case "mv/templates/mv_kalender.html":
						$this->make_calender();
						//$this->show_datum();
						break;

					// Einen Dump erstellen oder einspielen
					case "mv/templates/backup.html":
						if ($this->admin_is_ok) $this->mv_dump("mv,mvcform");
						else $this->content->template['rights_error'] = "admin";
						break;

					// Einen Eintrag erstellen
					case "mv/templates/create_email.html":
						if ($this->admin_is_ok) require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_entry.php');
						#if ($this->admin_is_ok)$this->make_entry();
						else $this->content->template['rights_error'] = "admin";
						break;

					// Einen Eintrag bearbeiten
					case "mv/templates/change_email.html":
						if ($this->admin_is_ok) require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/change_entry.php');
						#if ($this->admin_is_ok)$this->change_entry();
						else $this->content->template['rights_error'] = "admin";
						break;

					// Einen Eintrag erstellen
					case "mv/templates/create_input.html":
						if ($this->checked->pffeldid != ""
							&& $this->checked->pfgruppeid != "") {
							if ($this->is_group_write_ok()) require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_input_entry.php');
							else $this->content->template['rights_error'] = "write";
						} else {
							if ($this->checked->reorder_select == "ja"
								&& $this->admin_is_ok) $this->reorder_select_felder($this->checked->mv_id);
							else require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_input_entry.php');
							#else $this->make_input_entry();
						}
						break;

					// Einen Eintrag erstellen
					case "mv/templates/create_meta.html":
						if ($this->checked->pffeldid != ""
							&& $this->checked->pfgruppeid != "") {
							if ($this->is_group_write_ok()) require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_input_entry.php');
							else $this->content->template['rights_error'] = "write";
						} else {
							if ($this->checked->reorder_select == "ja"
								&& $this->admin_is_ok) $this->reorder_select_felder($this->checked->mv_id);
							elseif ($this->is_group_write_ok()) require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_input_entry.php');
							else require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_input_entry.php');
						}
						break;

					// Mitglieder eintragen
					case "mv/templates/fp_content.html":
						if ($this->is_group_write_ok()) {
							$this->get_field_rights_schreibrechte();
							if (!count($this->felder_schreib_rechte_aktuelle_gruppe)) $this->content->template['no_field_rights'] = 1;
							else require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/fp_content.php');
						} // noch ne Fallunterscheidung f�r Metaebene error Meldung
						else $this->content->template['rights_error'] = "write";
						break;

					// Mitgliederliste anzeigen
					case "mv/templates/userlist.html":
						// added by khmweb 12.11.09 f�r die Aktivierung mehrerer S�tze via Checkboxen
						// mv_activate = submit button in userlist.html
						if ($this->checked->mv_activate) $this->multiple_activation_of_records();
						if ($this->is_group_write_ok()) $this->show_user_list();
						// noch ne Fallunterscheidung f�r Metaebene error Meldung
						else $this->content->template['rights_error'] = "write";
						break;

					// Mitglied bearbeiten
					case "mv/templates/change_user.html":
						if ($this->is_group_write_ok()) require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/change_user.php');
						else $this->content->template['rights_error'] = "write";
						break;

					// Suchanfrage starten
					case "mv/templates/search_user.html":
						require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/search_user.php');
						#$this->search_user($this->checked->mv_id);
						break;

					// Newsletter verschicken
					case "mv/templates/newsletter.html":
						$this->newsletter();
						break;

					// Templates bearbeiten
					case "mv/templates/edit_templates.html":
						if ($this->user_darf_mv_schreiben) $this->edit_templates();
						else $this->content->template['rights_error'] = "admin";
						break;

					// Spracheinstellungen
					case "mv/templates/edit_languages.html":
						if ($this->admin_is_ok) $this->edit_languages();
						else $this->content->template['rights_error'] = "admin";
						break;

					// Rechteeinstellungen
					case "mv/templates/edit_rights.html":
						if ($this->user_darf_mv_schreiben) $this->edit_rights();
						else $this->content->template['rights_error'] = "admin";
						break;

					// Welche Gruppen d�rfen Beitr�ge lesen/ erstellen/ bearbeiten
					case "mv/templates/edit_content_rights.html":
						if ($this->user_darf_mv_schreiben) $this->edit_content_rights();
						else $this->content->template['rights_error'] = "admin";
						break;

					// Welche Gruppen d�rfen Felder erstellen/ bearbeiten
					case "mv/templates/edit_field_rights.html":
						if ($this->user_darf_mv_schreiben) $this->edit_field_rights();
						else $this->content->template['rights_error'] = "admin";
						break;

					// Welche Gruppen d�rfen bestimmte Teile der MV lesen/schreiben (z. B. Verbandschefs von den deutschen Landesverb�nden)
					case "mv/templates/edit_group_rights.html":
						if ($this->user_darf_mv_schreiben) $this->edit_group_rights();
						else $this->content->template['rights_error'] = "admin";
						break;

					// Welche Gruppen d�rfen Mitglieder k�ndigen? Button beim Edit
					case "mv/templates/edit_special_rights1.html":
						if ($this->user_darf_mv_schreiben) $this->edit_special_rights(1);
						else $this->content->template['rights_error'] = "admin";
						break;

					// Welche Gruppen d�rfen Mitglieder k�ndigen? Button bei der Suche
					case "mv/templates/edit_special_rights2.html":
						if ($this->user_darf_mv_schreiben) $this->edit_special_rights(2);
						else $this->content->template['rights_error'] = "admin";
						break;
					// Welche Sortierungs Felder sollen f�r die Suche angeboten werden
					case "mv/templates/sortierung.html":
						if ($this->user_darf_mv_schreiben) $this->edit_sortierung();
						else $this->content->template['rights_error'] = "admin";
						break;

					// m�gliche Links f�rs Frontend anzeigen
					case "mv/templates/frontend_links.html":
						$this->frontend_links();
						break;

					// Zeigt einen mit den Verwaltungen verkn�pften Kalender an
					case "mv/templates/mv_kalender.html":
						$this->make_calender();
						break;

					// Zeigt das Protokoll f�r einen Eintrag an
					case "mv/templates/mv_show_protokoll.html":
						$this->show_protokoll();
						break;

					// Einstellungen f�r die Metaebene editieren
					case "mv/templates/mv_meta_edit.html":
						if ($this->admin_is_ok) $this->edit_meta();
						else $this->content->template['rights_error'] = "admin";
						break;

					// Entwicklerfunktionen, am Besten im laufenden Betrieb auskommentieren!

					// Updaten der Searchtabellen, vosicht kann unter Umst�nden zu Timeouts f�hren
					case "mv/templates/mv_update_search.html":
						$this->update_lang_search_tabs();
						break;

					// gibt alle Optionswerte aller Select, Mutliselect, Radio und Check Felder aus
					case "mv/templates/mv_get_options.html":
						$this->druck_option_werte();
						break;

					// Gibt die Dzvh� System Ids aus
					case "mv/templates/mv_get_dz_system_ids.html":
						$this->get_dz_system_ids();
						break;

					// Z�hlt die Anzahl der Dateien, Zeilen und Zeichen
					case "mv/templates/mv_count_files_rows.html":
						$this->count_file_rows();
						break;

					// kl- importskript f�r die Lookup Tabelle, Feld Zahlungen DzvhAe
					case "mv/templates/lp_zahlungen.html":
						$this->lp_zahlungen();
						break;

					// bis hier
					default:
						break;
				}
			}
		} // FRONTEND
		else {
			$this->content->template["mv_id"] = (int)$this->checked->mv_id;
			$this->content->template["selected_flex_100_latest_entries"] = is_numeric($this->checked->mv_id)
				? $this->db->get_results("SELECT *
					FROM `{$this->cms->tbname["papoo_mv"]}_content_{$this->db->escape($this->checked->mv_id)}_search_{$this->db->escape($this->cms->lang_id)}`
					WHERE `mv_content_sperre` != 1 AND `mv_content_teaser` = 1
					ORDER BY `mv_content_id` DESC LIMIT 100", ARRAY_A)
				: [];

			// Dynamisch die zugehoerige Klasse finden
			$template_filename = explode('.', basename($this->checked->template))[0];
			$classname = implode('', array_map(function ($word) {
				return strtoupper($word);
			}, explode('_', $template_filename)));

			$lib_file_path = __DIR__ . "/{$template_filename}.php";

			if (file_exists($lib_file_path)) {
				require_once($lib_file_path);

				if (class_exists($classname)) {
					$this->loaded_classes[] = new $classname($this->checked, $this->cms, $this->content->template);
				}
			}
		}

		if ($exit) {
			if ($_SESSION['debug_stopallredirect'])
				echo '<a href="'
					. $location_url
					. '">'
					. $this->content->template['plugin']['mv']['weiter']
					. '</a>';
			else header("Location: $location_url");
			exit;
		}
	}

	private function makeLanguageEntries(int $flexId, int $contentId)
	{
		/**
		 * @var checked_class $checked
		 * @var cms $cms
		 * @var content_class $content
		 * @var ezSQL_mysqli $db
		 */
		global $checked, $cms, $content, $db;

		$defaultLanguage = $cms->lang_frontend;
		$slugFieldName = $this->get_sprechende_url_col_name($flexId);

		// Wenn freie URLs inaktiv sind oder kein Feld vom Typ "sprechende_url" definiert ist, Vorgang abbrechen
		if ($cms->mod_free == 0 || empty($slugFieldName)) {
			return;
		}

		foreach ($content->template['languageget'] as &$language) {
			// Hole Slug für die aktuelle Sprache
			$langId = (int)$language['lang_id'];
			$slug = @$db->get_var(
				"SELECT `{$slugFieldName}` " .
				"FROM {$cms->tbname["papoo_mv_content_{$flexId}_search_{$langId}"]} " .
				"WHERE mv_content_id = $contentId " .
				"LIMIT 1"
			);

			// Überspringe, wenn kein Slug für die aktuelle Sprache definiert ist
			if (empty($slug)) {
				continue;
			}

			// Baue URL für die aktuelle Sprache
			$languagePrefix = $language['lang_short'] == $defaultLanguage ? '' : '/' . $language['lang_short'];
			$url = "{$languagePrefix}/f-{$flexId}-{$contentId}-{$checked->menuid}-{$slug}.html";

			// Finalen Link zur jeweiligen Übersetzung des Flexeintrags speichern
			$language['lang_link'] = $url;
		};
		unset ($language);
	}

	function make_upload_or_picdel()
	{
		// Feststellen, ob Upload oder L�schen erfolgen soll
		$upload_or_picdel = "";
		$this->checked->upload = array();
		foreach ($this->checked as $key => $value) {
			$wrk_arr = explode("_", $key);
			// Format ist: mvcformFeldname_ID_XYZ, XYZ = upload oder picdel.
			if (substr($wrk_arr[0], 0, 7) == "mvcform") // am String-Anfang
			{
				$upload_or_picdel = $wrk_arr[count($wrk_arr) - 1]; // letzter Eintrag
				// String-Ende
				if ($upload_or_picdel == "upload"
					&& isset($_FILES[substr($key, 0, -7)])
					&& $_FILES[substr($key, 0, -7)]["error"] == UPLOAD_ERR_OK
					&& $_FILES[substr($key, 0, -7)]["size"] > 0
				) {
					// Feldname zur Aktion extrahieren
					$this->checked->upload[] = substr($key, 7, -7);
				} elseif ($upload_or_picdel == "picdel") {
					// Feldname zur Aktion extrahieren
					$this->checked->picdel['fieldname'] = substr($key, 7, -7); // "mvcform" und "_picdel"/"_upload" nicht extrahieren
					$keys = array_keys($value);
					$this->checked->picdel['filename'] = array_shift($keys); // oder array_pop statt array_shift, egal, da nur ein Element
					break; // kann nur einmal vorkommen, also raus
				}
			}
		}
		if (count($this->checked->upload) == 0) {
			unset($this->checked->upload);
		}
	}

	/**
	 * mv::global_checks()
	 * Hier werden einige globale checks durchgef�hrt
	 *
	 * @return void
	 */
	function global_checks()
	{
		//DIe onemv id mu� immer numerisch sein
		if (!is_numeric($this->checked->onemv)) $this->checked->onemv = 1;
		//DIe mv id mu� immer numerisch sein
		if (!is_numeric($this->checked->mv_id)) $this->checked->mv_id = "x"; // chgd. khmweb von 1 auf x sonst klappts nicht mit der Liste der Verwaltungen
		//Die externe Meta ID mu� numerisch sein
		if (!is_numeric($this->checked->extern_meta)) $this->checked->extern_meta = "x";
		//Die externe feldid mu� numerisch sein
		if (!is_numeric($this->checked->feldid)) $this->checked->feldid = "x";
		//Die externe mvcform_id mu� numerisch sein
		if (!is_numeric($this->checked->mvcform_id)) $this->checked->mvcform_id = "x";
		//Die externe mod_mv_id mu� numerisch sein
		if (!is_numeric($this->checked->mod_mv_id)) $this->checked->mod_mv_id = "x";
		//Die externe field_name mu� numerisch sein
		if (!is_numeric($this->checked->field_name)) $this->checked->field_name = "x";
		return $false;
	}

	/**
	 *
	 * @abstract Wenn wir drau�en sind
	 */
	function post_papoo()
	{
		require_once(PAPOO_ABS_PFAD . "/plugins/mv/lib/post_papoo.php");
	}

	/**
	 * Setzt die Metaebene ID als Flag in $this->meta_gruppe
	 * oder wenn es keine Metaebene gibt, dann ist die standard Id = 1
	 */
	function get_meta_group($mv_id = "")
	{
		// hat der User irgendwelche Rechtegruppen?
		if (!empty($this->gruppen_ids)) {
			// gibt es auch schon eine installierte Verwaltung innerhalb der Flexverwaltung
			if ($this->mv_exist()) {
				//Nat�rlich nur ausf�hren wenn die nicht woanders z.B. in der Admin schon gesetzt wurde
				if (empty($_SESSION['meta_gruppe_id'])) {
					// wenn mv_id nicht leer ist dann nehm die mod_mv_id
					if ($mv_id != "") $mv_id = $this->checked->mod_mv_id;
					// ansonsten nehm die mv_id
					else $mv_id = $this->checked->mv_id;
					$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
					// holt die Main Metaebene aus der Tabelle
					$sql = sprintf("SELECT mv_meta_main_lp_meta_id
			FROM %s,%s
			WHERE mv_content_userid = '%d'
			AND mv_meta_main_lp_user_id = mv_content_id
			AND mv_meta_main_lp_mv_id = '%d'",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_search_"
						. $lang,

						$this->cms->tbname['papoo_mv_meta_main_lp'],

						$this->db->escape($this->user->userid),
						$this->db->escape($this->checked->mv_id)
					);
					$meta_gruppe_id = $this->db->get_var($sql);
					// gibt keine Metagruppe?
					if (empty($meta_gruppe_id)) {
						$this->meta_gruppe = 1;
						$_SESSION['meta_gruppe_id'] = 1;
					} else {
						$this->meta_gruppe = $meta_gruppe_id;
						$_SESSION['meta_gruppe_id'] = $meta_gruppe_id;
					}
				}
			}
		}
	}

	/**
	 * Startseite der Verwaltung, Admingruppen f�r die Flexverwaltung aussuchen
	 */
	function mv_start()
	{
		$this->change_varchar_to_text();
		$this->delete_old_temp_data();
		// holt die admin_id aus der Admin Tabelle
		$sql = sprintf("SELECT admin_id
	    FROM %s
	    WHERE id = '1'
	    LIMIT 1",
			$this->cms->tbname['papoo_mv_admin']
		);
		$admin_id = $this->db->get_var($sql);
		$this->content->template['admin_id'] = $admin_id;
		// holt die Gruppen aus der Papoo Gruppen Rechte Tabelle
		$sql = sprintf("SELECT gruppeid,
	    gruppenname,
	    gruppen_beschreibung
	    FROM %s
	    ORDER BY gruppeid",
			$this->cms->tbname['papoo_gruppe']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		// wenn der Wert 0 ist, dann frische Flexverwaltung oder wenn er Adminrechte hat
		if ($admin_id == 0
			|| $this->have_admin_rights()) {
			$this->content->template['gruppen'] = $this->content->template['gruppen_mv'] = $result;
			$this->content->template['admin_ok'] = '1';
		}
		// wurde auf Eintragen gedr�ckt
		if ($this->checked->submit_admin_id != "") {
			// holt den admin_name der Gruppe aus der Tabelle
			$sql = sprintf("SELECT gruppenname
		FROM %s
		WHERE gruppeid = '%d'
		LIMIT 1",

				$this->cms->tbname['papoo_gruppe'],

				$this->db->escape($this->checked->group_name)
			);
			$admin_name = $this->db->get_var($sql);
			// speichert die neue Admingruppe f�r die Verwaltung
			$sql = sprintf("UPDATE %s SET admin_id = '%d',
		admin_name = '%s'
		WHERE id = '1'",
				$this->cms->tbname['papoo_mv_admin'],
				$this->db->escape($this->checked->group_name),
				$this->db->escape($admin_name)
			);
			$this->db->query($sql);
			$this->content->template['admin_id'] = $this->checked->group_name;
		}
	}

	// varchar-Felder in TEXT umwandeln, nur f�r die Content-Tabellen
	function change_varchar_to_text()
	{
		if (file_exists(PAPOO_ABS_PFAD . "/plugins/mv/sql/system.php")) // Test, ob schon ausgef�hrt, dann fehlt der File
		{
			global $db_praefix;
			// Alle Tabellennamen holen
			foreach ($this->cms->tbname as $key => $value) {
				// Flex-Tabellen extrahieren nach $tables
				$tb = substr($value, strlen($db_praefix)); // Pr�fix eleminieren
				$arr = explode("_", $tb);
				// Filter: Nur die zu dieser mv_id geh�renden Tabellen ermitteln
				if ($arr[0] == "papoo"
					and $arr[1] == "mv"
					and $arr[2] == "content"
					and ($arr[4] == ""
						or $arr[4] == "search")
				) $tables[$key] = $key;

			}
			// $tables enth�lt nun die zur Flex geh�renden Content-Tabellennamen
			if (count($tables)) {
				// Felddaten zu jeder Tabelle holen und verabeiten
				foreach ($tables as $key => $value) {
					$sql = sprintf("SHOW COLUMNS FROM " . $this->cms->tbname[$tables[$key]]);
					$result = $this->db->get_results($sql, ARRAY_A); // alle Felder dieser Tabelle
					if (count($result)) {
						$sql = sprintf("ALTER TABLE %s",
							$db_praefix . $key
						);
						$found = 0; // Falls kein varchar hier gefunden wird, dann SQL unten nicht ausf�hren
						for ($i = 0; $i <= count($result); $i++) {
							// Type = varchar? Und sicherheitshalber nicht f�r Keys etc.
							if (substr($result[$i]['Type'], 0, 8) == "varchar("
								and empty($result[$i]['Key'])) {
								$found = 1; // mind. ein Feld wurde gefunden
								$sql .= sprintf(" CHANGE `%s` `%s` TEXT,",
									$result[$i]['Field'],
									$result[$i]['Field']
								);
							} else continue; // Falscher Type oder Key, mach nix
						}
					}
					if ($found) {
						$sql = substr($sql, 0, -1); // letztes Komma entfernen
						$this->db->query($sql); // Change to TEXT
					}
				}
			}
			@unlink(PAPOO_ABS_PFAD . "/plugins/mv/sql/system.php"); // Indikator entfernen, dass die Tabellen bereits umgewandelt wurden
		}
	}

	function delete_old_temp_data()
	{
		$old = time() - 86400; // 24 Std. alt
		$verzeichnis = openDir(PAPOO_ABS_PFAD . "/files");
		while ($file = readDir($verzeichnis)) {
			if (substr($file, 0, 4) == "TMP_"
				and filemtime(PAPOO_ABS_PFAD . "/files/" . $file) <= $old) @unlink(PAPOO_ABS_PFAD . "/files/$file");
		}
		$verzeichnis = openDir(PAPOO_ABS_PFAD . "/images");
		while ($file = readDir($verzeichnis)) {
			if (substr($file, 0, 4) == "TMP_"
				and filemtime(PAPOO_ABS_PFAD . "/images/" . $file) <= $old) @unlink(PAPOO_ABS_PFAD . "/images/$file");
		}
		$verzeichnis = openDir(PAPOO_ABS_PFAD . "/images/thumbs");
		while ($file = readDir($verzeichnis)) {
			if (substr($file, 0, 4) == "TMP_"
				and filemtime(PAPOO_ABS_PFAD . "/images/thumbs/" . $file) <= $old) @unlink(PAPOO_ABS_PFAD . "/images/thumbs/$file");
		}
	}

	#nicht entfernen

	/**
	 * zu welchen Gruppen geh�rt der user?
	 */
	function get_users_groups()
	{
		// zu welchen Gruppen geh�rt der User
		$sql = sprintf("SELECT gruppenid FROM %s
	    WHERE userid='%d'", $this->cms->tbname['papoo_lookup_ug'], $this->db->escape($this->user->userid));
		$gruppen_id = $this->db->get_results($sql, ARRAY_A);

		// Rechtegruppen als array zwischenspeichern
		if (!empty($gruppen_id)) {
			foreach ($gruppen_id as $gruppe_id) {
				$gruppen[] = $gruppe_id['gruppenid'];
			}
		}
		$this->gruppen_ids = $gruppen;
	}

	/**
	 * Darf der User mit seinen Gruppenrechten aus dieser MV lesen?
	 * returns $group_is_ok=1, wenn ers darf, else 0
	 */
	function is_group_ok()
	{
		$group_is_ok = false;
		// gibt es eine Verwaltung mit der mv_id
		$sql = sprintf("SELECT mv_name
	    FROM %s
	    WHERE mv_id = '%d'
	    LIMIT 1",

			$this->cms->tbname['papoo_mv'],

			$this->db->escape($this->checked->mv_id)
		);
		$mv_id_name = $this->db->get_var($sql);
		if (!empty($mv_id_name)) {
			// zu welchen Gruppen geh�rt der User
			if (!empty($this->gruppen_ids)) {
				foreach ($this->gruppen_ids as $gruppe_id) {
					// darf diese Gruppe aus dieser MV lesen?
					$sql = sprintf("SELECT group_read
			FROM %s
			WHERE group_id = '%d'
			AND content_right_meta_id = '%d'
			LIMIT 1",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_content_rights",

						$this->db->escape($gruppe_id),
						$this->db->escape($this->meta_gruppe)
					);
					$leserecht = $this->db->get_var($sql);
					if ($leserecht == "1") {
						$group_is_ok = true; // darf lesen
						break;
					}
				}
			}
			// falls er zwar nicht alle Eintr�ge anschauen darf, aber Eintr�ge mit bestimtmen Feldwerten
			if (!$group_is_ok) {
				if (!empty($this->gruppen_id)) {
					foreach ($this->gruppen_id as $gruppe_id) {
						// gibt es f�r diese Gruppe bestimmte Feldwerte, die ein Leserecht f�r diesen Beitrag zur Folge haben
						$sql = sprintf("SELECT field_id
			    FROM %s
			    WHERE group_id = '%d'
			    AND field_right_meta_id = '%d'
			    AND group_read = '1'",

							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id)
							. "_group_rights",

							$this->db->escape($gruppe_id),
							$this->db->escape($this->meta_gruppe)
						);
						$leserecht = $this->db->get_results($sql);
						if (!empty($leserecht)) $group_is_ok = true; // darf lesen
					}
				}
			}
		}
		return ($group_is_ok);
	}

	/**
	 * Darf der User mit seinen Gruppenrechten in diese MV schreiben?
	 */
	function is_group_write_ok()
	{
		if (!$this->meta_gruppe) return false;
		$group_is_ok = false;
		// zu welchen Gruppen geh�rt der User
		if (!empty($this->gruppen_ids)) {
			foreach ($this->gruppen_ids as $gruppe_id) {
				// darf diese Gruppe in diese MV schreiben?
				$sql = sprintf("SELECT mv_mpg_id
		    FROM %s
		    WHERE mv_mpg_id = '%d'
		    AND mv_mpg_group_id = '%d'
		    AND mv_mpg_write = 1",

					$this->cms->tbname['papoo_mv']
					. "_mpg_"
					. $this->db->escape($this->checked->mv_id),

					$this->db->escape($this->meta_gruppe),
					$this->db->escape($gruppe_id)
				);
				$schreibrecht = $this->db->get_var($sql);
				if ($schreibrecht >= "1") {
					$group_is_ok = true; // darf schreiben
					break;
				}
			}
		}
		// falls er zwar nicht alle Eintr�ge anschauen darf, aber Eintr�ge mit bestimtmen Feldwerten
		if (!$group_is_ok) {
			if (!empty($this->gruppen_ids)) {
				foreach ($this->gruppen_ids as $gruppe_id) {
					// gibt es f�r diese Gruppe bestimmte Feldwerte, die ein Schreibrecht f�r diesen Beitrag zur Folge haben
					$sql = sprintf("SELECT field_id
			FROM %s
			WHERE group_id = '%d'
			AND group_write = '1'",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_group_rights",

						$this->db->escape($gruppe_id)
					);
					$schreibrecht = $this->db->get_results($sql);
					if (!empty($schreibrecht)) $group_is_ok = true; // darf schreiben
				}
			}
		}
		return ($group_is_ok);
	}

	/**
	 * globalisiert den admin Wert
	 **/
	function get_admin_id()
	{
		// holt den Wert aus der Admin Tabelle
		$sql = sprintf("SELECT admin_id FROM %s
	    WHERE id = '1'",
			$this->cms->tbname['papoo_mv_admin']);
		$this->admin_id = $this->db->get_var($sql);
	}

	/**
	 * Ist hier ein Admin am werkeln?
	 **/
	function have_admin_rights()
	{
		$admin_is_ok = false;
		if (!empty($this->gruppen_ids)) // Papoo Rechtegruppen des Users
		{
			foreach ($this->gruppen_ids as $gruppe_id) {
				if ($gruppe_id == $this->admin_id) $admin_is_ok = true;
			} // so jetzt darf er admin sein
		}
		// Template Flag setzen, wenn er ein Amdin ist und in der Meta Ebene admin werkelt
		if ($admin_is_ok
			&& $this->meta_gruppe == "1") $this->content->template['is_admin_meta'] = "ok";
		if ($admin_is_ok) $this->content->template['is_admin'] = "ok";
		// Flag setzten, damit nicht immer diese Funktion durchgenudelt wird
		$this->admin_is_ok = $admin_is_ok;
		// wers braucht, auch noch einen R�ckgabewert
		return ($admin_is_ok);
	}

	/**
	 * Standard Sprache aus der Tabelle mv_name_language holen
	 */
	function get_standard_lang()
	{
		$sql = sprintf("SELECT mv_lang_id FROM %s
	    WHERE mv_lang_standard='1'", $this->cms->tbname['papoo_mv_name_language']);
		$this->cms->lang_standard_id = $this->db->get_var($sql);

		// falls noch keine Standard Sprache definiert wurde, dann einfach Sprache mit id=1 als Standard nehmen
		if (empty($this->cms->lang_standard_id)) {
			$this->cms->lang_standard_id = 1;
		}
	}

	/**
	 *    Sprachen editieren, bzw. aktiviren/deaktivieren
	 */
	function edit_languages()
	{
		// wurde auf Eintragen gedr�ckt?
		if ($this->checked->mv_submit) {
			// �nderungen speichern, wenn zumindest eine Sprache aktiviert ist, ansonsten ignorieren
			if (!empty($this->checked->mv_lang_id)) {
				// wenn keine Standard Sprache definiert ist, dann wird einfach die Sprache mit id=1 zum Standard
				if ($this->checked->mv_lang_standard == "") {
					$this->checked->mv_lang_standard = "1";
				}
				// wurde eine neue Sprache aktiviert, dann f�r diese Sprache die Optionswerte etc. aus der Standardsprache kopieren und ein (L�nderk�rzel) hintendran
				$sql = sprintf("SELECT mv_lang_id,mv_lang_short, mv_aktive FROM %s
		    WHERE mv_aktive='1'
		    ORDER BY mv_lang_id", $this->cms->tbname['papoo_mv_name_language']);
				$aktive_old_langs = $this->db->get_results($sql, ARRAY_A);
				$neue_sprachen = array();

				if (!empty($this->checked->mv_lang_id)) {
					// gehe alle markierten Sprachen durch
					foreach ($this->checked->mv_lang_id as $aktive_new_lang) {
						$treffer = false;

						if (!empty($aktive_old_langs)) {
							// gehe alle Sprachen aus der Datenbank durch
							foreach ($aktive_old_langs as $aktive_old_lang) {
								// wenn es diese Sprachen schon in der Datenbank gab, dann Treffer
								if ($aktive_lang_old['mv_lang_id'] == $aktive_new_lang) {
									$treffer = true;
								}
							}
						}
						// gab es keinen Treffer, dann die Sprach ID ins Array zwischenspeichern
						if (!$treffer) {
							$neue_sprachen[] = $aktive_new_lang;
						}
					}
				}
				// hole alle Sprachen aus der Datenbank
				$sql = sprintf("SELECT mv_lang_id FROM %s", $this->cms->tbname['papoo_mv_name_language']);
				$result = $this->db->get_results($sql);
				$i = 1;
				// und gehe die einzelnen Sprachen durch
				foreach ($result as $row) {
					// entweder 1 f�r aktiv , alles andere auf 0 setzen
					if ($this->checked->mv_lang_id[$i] != "1") {
						$this->checked->mv_lang_id[$i] = "0";
					}
					// ausgew�hlte Standardsprache bekommt eine 1 alle anderen eine 0
					$mv_lang_standard = "0";

					if ($this->checked->mv_lang_standard == $i) {
						$mv_lang_standard = "1";
						$this->cms->lang_standard_id = $i;
					}
					// neuen Wert in Datenbank eintragen
					$sql = sprintf("UPDATE %s
			SET mv_aktive='%d',
			mv_lang_standard='%d'
			WHERE mv_lang_id='%d'",
						$this->cms->tbname['papoo_mv_name_language'],
						$this->db->escape($this->checked->mv_lang_id[$i]), $this->db->escape($mv_lang_standard),
						$this->db->escape($i));
					$this->db->query($sql);
					$i++;
				}
			}
		}
		// holt die Werte aus der Datenbank und gibt diese an das Template weiter
		$sql = sprintf("SELECT * FROM %s
	    ORDER BY mv_lang_id", $this->cms->tbname['papoo_mv_name_language']);
		$this->content->template['language_liste'] = $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * �berpr�ft ob es der erste Aufruf dieser Flexerwaltung ist und wenn ja,
	 * dann kopiere die Sprachen aus papoo_name_language
	 */
	function check_language()
	{
		// wieviel m�gliche Sprachen gibt es in dieser Felxverwaltung
		$sql = sprintf("SELECT COUNT(mv_lang_id) FROM %s", $this->cms->tbname['papoo_mv_name_language']);
		$anzahl = $this->db->get_var($sql);

		// wenn es noch keien Eintr�ge gibt ->erster Aufruf der Verwaltung
		if ($anzahl == 0) {
			// holt die Sprachen aud der Tabelle papoo_name_language
			$sql = sprintf("SELECT * FROM %s
		ORDER BY lang_id", $this->cms->tbname['papoo_name_language']);
			$result = $this->db->get_results($sql);
			if (!empty($result)) {
				foreach ($result as $row) {
					// und f�gt sie der Flexverwaltungs Tabelle papoo_mv_name_language hinzu
					$sql = sprintf("INSERT INTO %s
			SET mv_lang_short='%s',
			mv_lang_long='%s',
			mv_aktive='%d'",
						$this->cms->tbname['papoo_mv_name_language'], $this->db->escape($row->lang_short),
						$this->db->escape($row->lang_long), $this->db->escape($row->more_lang
							- 1)); // -1 weil hier 0=inaktiv und 1=aktiv, bei papoo_name_language sind es 2 und 1
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * mv::html2txt()
	 * HTML und Script aus einem Text herausziehen
	 *
	 * @param mixed $document
	 * @return
	 */
	function html2txt($document)
	{
		$search = array(
			'@<script[^>]*?>.*?</script>@si', // Strip out javascript
			'@<[\\/\\!]*?[^<>]*?>@si',        // Strip out HTML tags
			'@<style[^>]*?>.*?</style>@siU',  // Strip style tags properly
			'@<![\\s\\S]*?--[ \\t\\n\\r]*>@'  // Strip multi-line comments including CDATA
		);

		// <? <----Damit der PSPad Editor nicht rumzickt, sonst ist der Rest in grau

		$text = preg_replace($search, '', $document);
		return $text;
	}

	/**
	 * Ein Formular raussuchen und ausgeben im Frontend
	 */
	function front_get_form($formid = "")
	{
		$this->get_form_group_list_front($formid); // Daten raussuchen
		$this->make_form_front($this->result_groups); // Aus den Daten Formular machen
		// Sprachdaten rausholen
		/*$sql = sprintf("SELECT *
								FROM %s
								WHERE mv_id_id = '%d'
								AND mv_lang_id = '%d'",
								$this->cms->tbname['papoo_mv_lang'],
								$this->db->escape($this->checked->mv_id),
								$this->db->escape($this->cms->lang_id)
						);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!empty($result))
		{
			foreach($result as $daten)
			{
				$result[0]['mv_toptext_html'] = str_ireplace("\.\./images", "./images", $daten['mv_toptext_html']);
				$result[0]['mv_bottomtext_html'] = str_ireplace("\.\./images", "./images", $daten['mv_bottomtext_html']);
			}
		}
		$this->content->template['pcfronttext'] = $result;*/
	}

	/**
	 * echtes HTML Form erstellen
	 */
	function make_form_front($formdata)
	{
		// Flag setzen, damit die make_feld Funktionen auch wissen, dass sie das Pflichtfeld-H�kchen vom Frontend checken sollen
		$this->mv_back_or_front = "front";
		$groupar = $this->result_groups; // Gruppen (Fieldsets)
		$i = $index = 0;
		//Meta ID festlegen
		$meta_id = $this->meta_gruppe;
		if (!defined("admin") and is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
		// F�r diese Gruppen durchgehen
		if ((!empty($this->result_groups))) {
			foreach ($this->result_groups as $group) {
				$this->feldarray = array();
				// Alle Felder der jeweiligen Gruppe rausholen
				$sql = sprintf("SELECT * FROM %s, %s
		    WHERE mvcform_group_id = '%d'
		    AND mvcform_id = mvcform_lang_id
		    AND mvcform_lang_lang = '%d'
		    AND mvcform_meta_id = mvcform_lang_meta_id
		    AND mvcform_meta_id = '%d'
		    AND mvcform_aktiv = '1'
		    GROUP BY mvcform_id
		    ORDER BY mvcform_order_id DESC",
					$this->cms->tbname['papoo_mvcform_lang'],
					$this->cms->tbname['papoo_mvcform'],
					$this->db->escape($group['mvcform_group_id']),
					$this->db->escape($this->cms->lang_id),
					$this->db->escape($meta_id)
				);
				$result = $this->db->get_results($sql, ARRAY_A);
				if (!empty($result)) {
					$this->content->template['gfliste'][$i][$index]['mvcform_group_text'] = $group['mvcform_group_text']; // Gruppenname
					//Felder Rechte rausholen
					$this->get_field_rights_schreibrechte();
					foreach ($result as $fld) {
						$die_aktuelle_id = $fld['mvcform_id'];
						if (is_numeric($die_aktuelle_id)) {
							if (!in_array($die_aktuelle_id, $this->felder_schreib_rechte_aktuelle_gruppe)) {
								$value = "";
								continue; //Keine Leserechte - dann abbrechen
							}
						}
						// B�se HTML rausflitschen
						$this->checked->$fld = $this->html2txt($this->checked->$fld);
						// Anf�hrungszeichen kodieren
						$this->checked->$fld = $this->diverse->encode_quote($this->checked->$fld);
						// weil Felder gleiche Namen haben k�nnen, den Name mit der ID versehen
						$fld['mvcform_name'] .= "_" . $fld['mvcform_id'];
						$feld = $fld;
						$cfeld = ""; // sicherheitshalber
						require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_feld.php');
						#$this->make_feld_front($fld);
						$this->content->template['gfliste'][$i][$index]['mvcform_name_id'] = $fld['mvcform_name']; // Feldname CSS
						$this->content->template['gfliste'][$i][$index]['html'] = $cfeld; // Feld HTML
						$index++;
					}
				}
				$i++;
			}
		}
		$this->tiny_elements = substr($this->tiny_elements, 0, -1);
		$this->content->template['show_tiny'] = $this->show_tiny;
		$this->content->template['tiny_elements'] = $this->tiny_elements;
	}

	/**
	 * mv::make_feld_tooltip()
	 * Erstellt das Tooltip Bild mit Text
	 * @param string $tooltip
	 * @return
	 */
	function make_feld_tooltip($tooltip = "")
	{
		$tooltip_text = "";

		if (!empty($tooltip)) {
			$tooltip = nl2br($tooltip);
			$tooltip = str_ireplace("\n", "", $tooltip);
			$tooltip = str_ireplace("\r", "", $tooltip);
			$tooltip_text = '<img class="descrip_img" title="';
			$tooltip_text .= $this->diverse->encode_quote($tooltip);
			$tooltip_text .= '" alt="info" src="' . PAPOO_WEB_PFAD
				. '/plugins/mv/bilder/info.gif" height="16" width="16" />';
		}
		return $tooltip_text;
	}

	// UNUSED !!!

	/**
	 * Holt den ausgw�hlten Wert f�r das Feld($feld) aus der entsprechenden
	 * Lang Tabelle
	 */
	function get_lang_wert($feld)
	{
		// Holt den Eintrag aus der Lang Tabelle
		$sql = sprintf("SELECT content FROM %s
	    WHERE lookup_id = '%d'
	    AND lang_id = '%d'
	    LIMIT 1",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($feld['mvcform_form_id'])
			. "_lang_"
			. $this->db->escape($feld['mvcform_lang_id']),

			$this->db->escape($this->checked->{$feld['mvcform_name']}),
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$lang_wert = $this->db->get_var($sql);
		// zwischenspeichern und als return Wert zur�ck
		$feld['mvcform_lang_wert'] = $lang_wert;
		return ($feld);
	}

	// UNUSED !!!

	/**
	 * Holt die ausgw�hlten Werte f�r das Feld($feld) aus der entsprechenden
	 * Lang Tabelle
	 */
	function get_lang_werte_multi($feld)
	{
		$mv_id = $feld['mvcform_form_id'];
		$feld_id = $feld['mvcform_lang_id'];
		$feld_name = $feld['mvcform_name'];
		// Holt den/die Lookupwert aus der Tabelle, ist aber immer nur ein Treffer, da die Werte mit \n getrennt sind
		$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		$sql = sprintf("SELECT %s FROM %s
	    WHERE mv_content_id = '%d'
	    LIMIT 1",
			$this->db->escape($feld_name),

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($mv_id)
			. "_search_"
			. $lang,

			$this->db->escape($this->checked->mv_content_id)
		);
		$feld_werte = $this->db->get_var($sql);
		// whitespaces am Anfang udn ende raus, und dann Werte in array spliten
		$feld_werte = explode("\n", trim($feld_werte));
		// Inhaltsprache Backend
		$lang = $this->cms->lang_back_content_id;

		// gibt es Werte
		if (!empty($feld_werte)) {
			// dann geh sie durch
			foreach ($feld_werte as $feld_wert) {
				// und hol f�r den jeweiligen Lookup Wert den Content aus der Tabelle
				$sql = sprintf("SELECT content FROM %s
		    WHERE lookup_id = '%d'
		    AND lang_id = '%d'
		    LIMIT 1",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($mv_id)
					. "_lang_"
					. $this->db->escape($feld_id),

					$this->db->escape($feld_wert),
					$this->db->escape($lang)
				);
				$lang_werte[] = $this->db->get_var($sql);
			}
		}
		// speicher die Treffer im array $feld und als return Wert zur�ckgeben
		$feld['mvcform_lang_werte'] = $lang_werte;
		return ($feld);
	}

	/**
	 * Holt die m�glichen Werte f�r das Feld ($feld) aus der entsprechenden
	 * lang Tabelle
	 */
	function get_lang_werte_lookup($feld, $lang = 0)
	{
		$mv_id = $feld['mvcform_form_id'];
		$feld_id = $feld['mvcform_lang_id'];
		if (!$lang) $lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		$sql = sprintf("SELECT * FROM %s
	    WHERE lang_id = '%d'
	    AND lookup_id <> 0
	    ORDER BY order_id",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($mv_id)
			. "_lang_"
			. $this->db->escape($feld_id),

			$this->db->escape($lang)
		);
		$feld['mvcform_lang_lookup'] = $this->db->get_results($sql);
		if (($feld_id >= 74 and $feld_id <= 80)
			and $this->dzvhae_system_id) {
			if (!empty($feld['mvcform_lang_lookup'])) {
				foreach ($feld['mvcform_lang_lookup'] as $key => $value) {
					$sql = sprintf("SELECT text
			FROM %s
			WHERE lookup_id = '%d'",
						$this->cms->tbname['papoo_mv']
						. "_zahlungen_lookup",
						$value->content
					);
					$result = $this->db->get_var($sql);
					$feld['mvcform_lang_lookup'][$key]->text = $result;
				}
			}
		}
		return ($feld);
	}

	/**
	 * FUNCTION: validateEmail
	 *
	 * Validates an email address and return true or false.
	 *
	 * @param string $address email address
	 * @return bool true/false
	 * @access public
	 */
	function validateEmail($address)
	{
		// alt return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9_-]+(\.[_a-z0-9-]+)+$/i', $address);
		return preg_match('/^([a-z0-9]+([-_.]?[a-z0-9])+)@[a-z0-9äöü]+([-_.]?[a-z0-9])+.[a-z]{2,4}$/i', $address);
	}

	/**
	 * Daten einer Fieldsetgruppe rausholen
	 */
	function get_groupdata()
	{
		// Normale Daten
		$sql = sprintf("SELECT * FROM %s
	    WHERE mvcform_group_id = '%d'
	    AND mvcform_group_form_meta_id = '%d'",
			$this->cms->tbname['papoo_mvcform_group'],
			$this->db->escape($this->checked->grupid),
			$this->db->escape($this->meta_gruppe)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['dat'] = $result;
	}

	/**
	 * Eine Gruppe l�schen
	 */
	function del_group()
	{
		// Standardaten l�schen
		$sql = sprintf("DELETE FROM %s
	    WHERE mvcform_group_id = '%d'",
			$this->cms->tbname['papoo_mvcform_group'],
			$this->db->escape($this->checked->mvcform_group_id)
		);
		$this->db->query($sql);
		// SPrachdateien l�schen
		$sql = sprintf("DELETE FROM %s
	    WHERE mvcform_group_lang_id = '%d'",
			$this->cms->tbname['papoo_mvcform_group_lang'],
			$this->db->escape($this->checked->mvcform_group_id)
		);
		$this->db->query($sql);
		// Felder der Gruppe auf nogroup setzen
		$sql = sprintf("UPDATE %s SET mvcform_no_group = '1'
	    WHERE mvcform_group_id = '%d'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->mvcform_group_id)
		);
		$this->db->query($sql);
		$location_url = $_SERVER['PHP_SELF']
			. "?menuid="
			. $this->checked->menuid
			. "&template=" . $this->checked->template
			. "&mv_id="
			. $this->checked->mv_id
			. "&delok=1";
		$this->reload($location_url);
	}

	/**
	 * Gruppen bearbeiten
	 */
	function change_group()
	{
		// L�schbutton anbieten, wenn der Gruppe keine Felder zugeordnet sind
		$sql = sprintf("SELECT  mvcform_group_id
	    FROM %s
	    WHERE mvcform_group_id = '%d'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->grupid)
		);
		if ($this->db->get_results($sql, ARRAY_A)) $this->content->template['cannot_delete_group'] = 1; // Kann nicht gel�scht werden
		// Soll gel�scht werden?
		if (!empty($this->checked->del_group)) {
			$this->content->template['mvcform_group_id'] = $this->checked->mvcform_group_id;
			$this->content->template['mv_id'] = $this->checked->mv_id;
			$this->content->template['groupedit'] = $this->content->template['fragedel'] = "ok";
			return;
		}
		if (!empty($this->checked->del_group_echt)) $this->del_group();
		if (empty($this->checked->submit_group)) {
			$this->content->template['groupedit'] = "ok";
			// Sprachen zuweisen
			$this->make_lang_group();
			// Daten rausholen und zuweisen
			$this->get_groupdata();
		} else {
			if (empty($this->checked->mvcform_group_text)
				or empty($this->checked->mvcform_group_name)) $this->content->template['fehler14'] = $this->content->template['groupedit'] = "ok";
			else {
				// Wenn ok, eintragen
				$this->insup_new_group("update");
				// Neu laden
				$location_url = $_SERVER['PHP_SELF']
					. "?menuid="
					. $this->checked->menuid
					. "&template="
					. $this->checked->template
					. "&mv_id="
					. $this->checked->mv_id
					. "&grp_changed=1";;
				$this->reload($location_url);
			}
			$this->content->template['language_form'][0]['mvcform_group_text'] = $this->checked->mvcform_group_text;
			$this->content->template['dat'][0]['mvcform_group_name'] = $this->checked->mvcform_group_name;
			$this->content->template['mv_id'] = $this->checked->mv_id;
			$this->content->template['dat'][0]['mvcform_group_id'] = $this->checked->mvcform_group_id;
		}
	}

	/**
	 * Neues gruppen erstellen
	 */
	function create_new_group()
	{
		if (empty($this->checked->submit_group)) {
			$this->content->template['groupedit'] = "ok";
			// Sprachen zuweisen
			$this->make_lang_group();
		} else {
			if (empty($this->checked->mvcform_group_text)
				or empty($this->checked->mvcform_group_name)) $this->content->template['fehler14'] = $this->content->template['groupedit'] = "ok";
			else {
				$this->insup_new_group("insert");// Wenn ok, eintragen
				// Neu laden
				$location_url = $_SERVER['PHP_SELF']
					. "?menuid="
					. $this->checked->menuid
					. "&template="
					. $this->checked->template
					. "&mv_id="
					. $this->checked->mv_id
					. "&grp_saved=1";;
				$this->reload($location_url);
			}
			$this->content->template['language_form'][0]['mvcform_group_text'] = $this->checked->mvcform_group_text;
			$this->content->template['dat'][0]['mvcform_group_name'] = $this->checked->mvcform_group_name;
			$this->content->template['mv_id'] = $this->checked->mv_id;
			#$this->content->template['dat'][0]['mvcform_group_id'] = $this->checked->mvcform_group_id;
		}
	}

	/**
	 * Daten aus Post zur�ckgeben
	 */
	function do_it_again()
	{
		// Alle Post daten erneut ins Template
		foreach ($_POST as $key => $value) {
			$key = $this->db->escape($key);
			// B�sen HTML rausflitschen
			$value2 = $this->html2txt($this->db->escape($value));
			// Anf�hreungszeichen kodieren
			$value2 = $this->diverse->encode_quote($value2);
			#if ($key == "mvcform_content_list_new"
			#	|| $key == "mvcform_preisintervall_list") $value2 = "nobr:" . $value2;
			if ($key == "mvcform_content_list") $this->content->template['liste_array'] = $value2;
			else $this->content->template[$key] = $value2;
			// Das Order-ID-Array retour
			// ist es ein select/multi/radio... bei der Neueingabe uninteressant, nur im EditMode
			if (!empty($this->content->template['language_form'])
				&& ($this->content->template['fdat'][0]['mvcform_type'] == "radio"
					|| $this->content->template['fdat'][0]['mvcform_type'] == "select"
					|| $this->content->template['fdat'][0]['mvcform_type'] == "multiselect"
					|| $this->content->template['fdat'][0]['mvcform_type'] == "check"
					|| $this->content->template['fdat'][0]['mvcform_type'] == "pre_select")
				&& $this->checked->feldid != "x"
			) {
				// dann hole alle Eintr�ge aus der entsprechenden Lang Tabelle
				$sql = sprintf("SELECT * FROM %s
		    WHERE lookup_id <> 0
		    AND lang_id = '%d'
		    ORDER BY order_id",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_lang_"
					. $this->db->escape($this->checked->feldid),

					$this->db->escape($this->cms->lang_back_content_id)
				);
				$result = $this->db->get_results($sql);
				if (!empty($result)) {
					foreach ($result as $row) {
						// und speicher die Daten f�r Template
						#$this->content->template['liste_array'][$row->lookup_id] = "nobr:" . trim($row->content);
						$this->content->template['order_id'][$row->lookup_id] = $row->order_id;
					}
				}
			}
			if (($this->checked->feldid == "x" and $key == "mvcform_content_list")
				or ($this->checked->feldid != "x" and $key == "mvcform_content_list_new")
				or $key == "mvcform_lang_tooltip") $value = "nobr:" . $value;
			$this->content->template['fdat'][0][$key] = $value;
		}
	}

	/**
	 * Gruppendaten aktualisieren
	 *
	 * called by make_entry.php, mv.php: change_group(), create_new_group()
	 */
	function insup_new_group($modus = "insert")
	{
		// Gruppierung updaten
		if ($modus == "update") {
			$maxid = $this->db->escape($this->checked->mvcform_group_id);
			$sql = sprintf("UPDATE %s SET mvcform_group_text = '%s'
		WHERE mvcform_group_lang_lang = '%d'
		AND mvcform_group_lang_meta = '%d'
		AND mvcform_group_lang_id = '%d'",
				$this->cms->tbname['papoo_mvcform_group_lang'],
				$this->db->escape($this->checked->mvcform_group_text),
				$this->db->escape($this->cms->lang_back_content_id),
				$this->db->escape($this->meta_gruppe),
				$this->db->escape($this->checked->mvcform_group_id)
			);
			$this->db->query($sql);
		}
		if ($modus == "insert") {
			// die Gruppe in allen Sprachen anlegen
			$sql = sprintf("SELECT * FROM %s",
				$this->cms->tbname['papoo_mv_name_language']
			);
			$sprachen = $this->db->get_results($sql, ARRAY_A);
			if (!empty($sprachen)) {
				$sql = sprintf("SELECT MAX(mvcform_group_id)
		    FROM %s",
					$this->cms->tbname['papoo_mvcform_group']
				);
				$maxid = $this->db->get_var($sql) + 1;
				//Metaebenen rausholen
				$meta_gruppen = $this->get_meta_gruppen();
				foreach ($meta_gruppen as $meta) {
					// generelle Daten eintragen
					$sql = sprintf("INSERT INTO %s
			SET	mvcform_group_form_id = '%d',
			mvcform_group_name = '%s',
			mvcform_group_form_meta_id = '%d',
			mvcform_group_id = '%d'",
						$this->cms->tbname['papoo_mvcform_group'],
						$this->db->escape($this->checked->mv_id),
						$this->db->escape($this->checked->mvcform_group_name),
						$this->db->escape($meta['mv_meta_id']),
						$maxid
					);
					$this->db->query($sql);
					foreach ($sprachen as $key => $value) {
						$sql = sprintf("INSERT INTO %s
			    SET mvcform_group_lang_id = '%d',
			    mvcform_group_text = '%s',
			    mvcform_group_lang_lang = '%s',
			    mvcform_group_lang_meta = '%d'",
							$this->cms->tbname['papoo_mvcform_group_lang'],
							$maxid,
							$this->db->escape($this->checked->mvcform_group_text),
							$this->db->escape($value['mv_lang_id']),
							$this->db->escape($meta['mv_meta_id'])
						);
						$this->db->query($sql);
					}
				}
			}
		}
		// Neu sortieren
		$this->reorder_groups($this->checked->mv_id);
		// wenn der Aufruf ohne $modus erfolgte
		$maxid = empty($maxid) ? $this->checked->mvcform_group_id : $maxid;
		return ($maxid);
	}

	/**
	 * Liste der Gruppen
	 * called by mv.php (2x), back_get_form.php
	 */
	function get_form_group_list($formid = "")
	{
		if (empty($formid)) $formid = $this->checked->mv_id;
		if (!empty($this->cms->tbname['papoo_mvcform_group'])
			&& !empty($this->cms->tbname['papoo_mvcform_group_lang'])) {
			// Gruppen auslesen
			$sql = sprintf("SELECT *
		FROM %s, %s
		WHERE mvcform_group_form_id = '%d'
		AND	mvcform_group_lang_id = mvcform_group_id
		AND mvcform_group_lang_lang = '%d'
		AND mvcform_group_lang_meta = mvcform_group_form_meta_id
		AND mvcform_group_form_meta_id = '%d'
		GROUP BY mvcform_group_id
		ORDER BY mvcform_group_order_id",
				$this->cms->tbname['papoo_mvcform_group'],
				$this->cms->tbname['papoo_mvcform_group_lang'],
				$this->db->escape($formid),
				$this->db->escape($this->cms->lang_back_content_id),
				$this->db->escape($this->meta_gruppe)
			);
			$result_groups = $this->db->get_results($sql, ARRAY_A);
		}
		// Wenn eine exitiert, dann k�nnen auch Felder angelegt werden
		if (count($result_groups) > 0) $this->content->template['gruppeok'] = "ok";
		// Ans Template
		$this->content->template['glist'] = $result_groups;
		// F�r interne Weiterverarbeitung
		$this->result_groups = $result_groups;
	}

	/**
	 * Liste der Gruppen
	 */
	function get_form_group_list_front($formid = "")
	{
		if (empty($formid)) $formid = $this->checked->mv_id;
		$meta_id = $this->meta_gruppe;
		if (!defined("admin") and is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
		// Gruppen auslesen
		$sql = sprintf("SELECT *
	    FROM %s, %s
	    WHERE mvcform_group_form_id = '%d'
	    AND	mvcform_group_lang_id = mvcform_group_id
	    AND mvcform_group_lang_lang = '%d'
	    AND mvcform_group_lang_meta = mvcform_group_form_meta_id
	    AND mvcform_group_form_meta_id = '%d'
	    GROUP BY mvcform_group_id
	    ORDER BY mvcform_group_order_id ",
			$this->cms->tbname['papoo_mvcform_group'],
			$this->cms->tbname['papoo_mvcform_group_lang'],
			$this->db->escape($formid),
			$this->db->escape($this->cms->lang_id),
			$this->db->escape($meta_id)
		);
		$result_groups = $this->db->get_results($sql, ARRAY_A);
		// Wenn eine exitiert dann k�nnen auch Felder angelegt werden
		if (count($result_groups) > 0) $this->content->template['gruppeok'] = "ok";
		// Ans Template
		$this->content->template['glist'] = $result_groups;
		// F�r interne Weiterverarbeitung
		$this->result_groups = $result_groups;
	}

	/**
	 * Typen zuweisen in ein Array
	 * called by make_feld_form()
	 */
	function cform_make_typ()
	{
		$typ_array = array();
		// Markierung f�r neue Feldtypen
		// result_cat
		$typ_array[] = array(
			"type" => "skip",
			"name" => ""
		);
		$typ_array[] = array(
			"type" => "sprechende_url",
			"name" => "Sprechende URL"
		);
		$typ_array[] = array(
			"type" => "skip_end",
			"name" => ""
		);
		$typ_array[] = array(
			"type" => "skip",
			"name" => ""
		);

		$typ_array[] = array(
			"type" => "text",
			"name" => "Text"
		);

		$typ_array[] = array(
			"type" => "textarea",
			"name" => "Textarea"
		);

		$typ_array[] = array(
			"type" => "textarea_tiny",
			"name" => "Textarea mit Editor"
		);
		$typ_array[] = array(
			"type" => "skip_end",
			"name" => ""
		);
		$typ_array[] = array(
			"type" => "skip",
			"name" => ""
		);

		$typ_array[] = array(
			"type" => "email",
			"name" => "E-Mail"
		);

		$typ_array[] = array(
			"type" => "password",
			"name" => "Password"
		);

		$typ_array[] = array(
			"type" => "link",
			"name" => "Link"
		);

		$typ_array[] = array(
			"type" => "hidden",
			"name" => "Hidden"
		);

		$typ_array[] = array(
			"type" => "skip_end",
			"name" => ""
		);

		$typ_array[] = array(
			"type" => "skip",
			"name" => ""
		);

		$typ_array[] = array(
			"type" => "select",
			"name" => "Select Box"
		);

		$typ_array[] = array(
			"type" => "multiselect",
			"name" => "Multiselect Box"
		);

		$typ_array[] = array(
			"type" => "pre_select",
			"name" => "Bilder/Text Auswahl"
		);

		$typ_array[] = array(
			"type" => "radio",
			"name" => "Radio Button"
		);

		$typ_array[] = array(
			"type" => "check",
			"name" => "Checkbox"
		);

		$typ_array[] = array(
			"type" => "skip_end",
			"name" => ""
		);

		$typ_array[] = array(
			"type" => "skip",
			"name" => ""
		);

		$typ_array[] = array(
			"type" => "timestamp",
			"name" => "Timestamp"
		);

		$typ_array[] = array(
			"type" => "skip_end",
			"name" => ""
		);

		$typ_array[] = array(
			"type" => "skip",
			"name" => ""
		);

		$typ_array[] = array(
			"type" => "file",
			"name" => "File Upload"
		);

		$typ_array[] = array(
			"type" => "picture",
			"name" => "Bild Upload"
		);

		$typ_array[] = array(
			"type" => "skip_end",
			"name" => ""
		);

		$typ_array[] = array(
			"type" => "skip",
			"name" => ""
		);

		$typ_array[] = array(
			"type" => "galerie",
			"name" => "Bilder Galerie"
		);

		$typ_array[] = array(
			"type" => "artikel",
			"name" => "Artikel"
		);

		$typ_array[] = array(
			"type" => "flex_verbindung",
			"name" => "Flex Verknüpfung"
		);

		$typ_array[] = array(
			"type" => "flex_tree",
			"name" => "Flexbaum"
		);

		$typ_array[] = array(
			"type" => "skip_end",
			"name" => ""
		);

		$typ_array[] = array(
			"type" => "skip",
			"name" => ""
		);

		$typ_array[] = array(
			"type" => "zeitintervall",
			"name" => "Zeitintervall"
		);

		$typ_array[] = array(
			"type" => "preisintervall",
			"name" => "Preisintervall"
		);

		$this->content->template['result_cat'] = $typ_array;
	}

	/**
	 * Typen zuweisen in ein Array
	 */
	function cform_make_content_typ()
	{
		$typ_array = array();
		// result_cat
		$typ_array[] = array(
			"type" => "alpha",
			"name" => "alphabetisch"
		);

		$typ_array[] = array(
			"type" => "num",
			"name" => "numerisch"
		);
		$this->content->template['result_cat_ctyp'] = $typ_array;
	}

	function get_sprechende_url_col_name($mv_id)
	{
		if (!is_numeric($mv_id)) {
			return false;
		}
		// Hole den Namen der Spalte vom Typ sprechende URL
		$sql = sprintf("SELECT `mvcform_id`, `mvcform_name` FROM `%s` WHERE `mvcform_form_id`=%d AND `mvcform_type`='sprechende_url' LIMIT 1;",
			$this->cms->tbname['papoo_mvcform'],
			intval($mv_id)
		);
		$result = $this->db->get_row($sql, ARRAY_A);
		if (is_array($result)) {
			return $result['mvcform_name'] . "_" . $result['mvcform_id'];
		}
		return false;
	}

	function delete_radio_value($feldid)
	{
		$sql = sprintf("DELETE FROM %s
	    WHERE lookup_id = '%d'",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_lang_"
			. $this->db->escape($this->checked->feldid),

			$this->db->escape($feldid)
		);
		$this->db->query($sql);
		$sql = sprintf("DELETE FROM %s
	    WHERE lookup_id = '%d'",
			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_lookup_"
			. $this->db->escape($this->checked->feldid),
			$this->db->escape($feldid)
		);
		#$this->db->query($sql);
		$this->del_field_value_active = 1;
		return $this->del_field_value_active;
	}

	/**
	 * Feld Formular erstellen
	 */
	function make_feld_form()
	{
		// holt die Felder aus der Tabelle
		$felder = $this->get_spalten_namen();
		if (!empty($this->checked->feldid)
			or $this->checked->pffeldid == "new") $felder = $this->check_pflicht($felder, $this->checked->feldid); // checken ob checked Werte dabei sind
		// Ans Template
		$this->content->template['felderauswahl'] = $felder;
		$this->make_lang_field(); // Sprachen zuweisen
		$this->cform_make_typ(); // Feld-Typen zuweisen
		$this->cform_make_content_typ(); // Content Typen zuweisen (alpha, num)
		$this->get_form_group_list(); // Gruppierungsliste aussuchen
	}

	/**
	 * Neues Feld erstellen
	 * called by make_input_entry.php only
	 */
	function create_new_field()
	{
		if (is_array($this->checked->mvcform_type) // array von submit-buttons enth�lt einen gew�hlten mvcform_type (text, check etc.)
			and $this->checked->pffeldid == "new") // oder "quick", wenn einer aus der Schnellauswahl-Select-Box gew�hlt wurde
		{
			foreach ($this->checked->mvcform_type as $key => $value) // ist nur ein Element, key auswerten und merken
			{
				if ($key == "quick") $mvcform_type = $this->checked->mvcform_type_select; // Schnellauswahl genutzt? Dann den gew�hlten Wert aus der Selectbox nehmen
				else $mvcform_type = $key;
			}
			// Nur diese Feldtypen sind erlaubt
			if ($mvcform_type == "check"
				or $mvcform_type == "email"
				or $mvcform_type == "file"
				or $mvcform_type == "galerie"
				or $mvcform_type == "link"
				or $mvcform_type == "multiselect"
				or $mvcform_type == "password"
				or $mvcform_type == "picture"
				or $mvcform_type == "pre_select"
				or $mvcform_type == "preisintervall"
				or $mvcform_type == "radio"
				or $mvcform_type == "select"
				or $mvcform_type == "text"
				or $mvcform_type == "textarea"
				or $mvcform_type == "textarea_tiny"
				or $mvcform_type == "timestamp"
				or $mvcform_type == "artikel"
				or $mvcform_type == "flex_verbindung"
				or $mvcform_type == "flex_tree"
				or $mvcform_type == "zeitintervall"
				or $mvcform_type == "hidden"
				or $mvcform_type == "sprechende_url") {
				$this->content->template['mvcform_type'] = $this->db->escape($mvcform_type);
				$this->content->template['altereintrag'] = "new_next"; // Template-Anwahl
			} else $this->content->template['fehler15'] = $fehler = 1; // ung�ltiger mvcform_type
		} else $mvcform_type = $this->db->escape($this->checked->mvcform_type);

		if (empty($this->checked->submit_field)) {
			// Per Default Normaler User und Feld auf aktiv setzen
			$this->content->template['fdat'][0]['mvcform_normaler_user']
				= $this->content->template['fdat'][0]['mvcform_aktiv']
				= $this->content->template['fdat'][0]['mvcform_list_front']
				= 1;
			$this->make_feld_form(); // Formular zur Felderstellung erstellen und anzeigen
		} elseif (!$fehler) // Dateneingabe zur Felddefinition ist erfolgt via Button submit_field
		{
			// Daten checken
			require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/check_field_definition.php');
			if (!$fehler) // $fehler set by check_field_definition.php on return
			{
				// Wenn ok, dann eintragen into papoo_mvcform, papoo_mvcform_lang, papoo_mv_feld_preisintervall
				require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/insup_new_field.php');
				if (!$fehler) {
					// Default Feldrechte setzen
					if ($insertid_2) {
						$this->change_field_rights($insertid_2, 0, 1, 1, 1); // Default Admin lesen & schreiben
						$this->change_field_rights($insertid_2, 0, 10, 1, 0); // Default jeder lesen
					}
					#$this->insup_new_field();
					// Neues Feld der Tabelle Mitglieder Content und der lang- Tabelle hinzuf�gen
					require(PAPOO_ABS_PFAD . '/plugins/mv/lib/alter_new_field.php');
					// Neu laden
					$location_url = $_SERVER['PHP_SELF']
						. "?menuid="
						. $this->checked->menuid
						. "&template="
						. $this->checked->template
						. "&mv_id="
						. $this->checked->mv_id
						. "&new_ok=1";
					$this->reload($location_url);
				}
			} else {
				$this->content->template['formeingabe_fehler'] = 1; //Meldung ans Template, dass ein Fehler vorliegt
				$this->content->template['altereintrag'] = "new_next"; // Template-Anwahl
				// Feld Formular erstellen
				$this->make_feld_form();
				// Daten erneut ausgeben
				$this->do_it_again();
			}
		}
	}

	/**
	 * Felder neu ordnen
	 */
	function reorder_fields($groupid = "0", $meta_gruppe = "-1")
	{
		if ($meta_gruppe == "-1") $meta_gruppe = $this->meta_gruppe;
		// Felder der Gruppe rausholen
		$sql = sprintf("SELECT * FROM %s
	    WHERE mvcform_group_id = '%d'
	    AND mvcform_meta_id = '%d'
	    ORDER BY mvcform_order_id ASC",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($groupid), $this->db->escape($meta_gruppe)
		);
		$result = $this->db->get_results($sql);
		$i = 10;

		// Durchz�hlen und neu einsortieren
		if ((!empty($result))) {
			foreach ($result as $dat) {
				$sql = sprintf("UPDATE %s SET mvcform_order_id = '%d'
		    WHERE mvcform_id = '%d'
		    AND mvcform_meta_id = '%d'",
					$this->cms->tbname['papoo_mvcform'],
					$this->db->escape($i),
					$this->db->escape($dat->mvcform_id),
					$this->db->escape($meta_gruppe)
				);
				$this->db->query($sql);
				$i = $i + 10;
			}
		}
	}

	/**
	 * Felder neu ordnen
	 */
	function reorder_groups($mv_id = "0")
	{
		// Felder der Gruppe rausholen
		$sql = sprintf("SELECT * FROM %s
	    WHERE mvcform_group_form_id = '%d'
	    AND mvcform_group_form_meta_id = '%d'
	    ORDER BY mvcform_group_order_id ASC",
			$this->cms->tbname['papoo_mvcform_group'],
			$this->db->escape($mv_id),
			$this->db->escape($this->meta_gruppe)
		);
		$result = $this->db->get_results($sql);
		$i = 10;
		// Durchz�hlen und neu einsortieren
		if ((!empty($result))) {
			foreach ($result as $dat) {
				$sql = sprintf("UPDATE %s SET mvcform_group_order_id = '%d'
		    WHERE mvcform_group_id = '%d'
		    AND mvcform_group_form_meta_id = '%d'",
					$this->cms->tbname['papoo_mvcform_group'],
					$this->db->escape($i),
					$this->db->escape($dat->mvcform_group_id),
					$this->db->escape($this->meta_gruppe)
				);
				$this->db->query($sql);
				$i = $i + 10;
			}
		}
	}

	/**
	 * Spracheintr�ge f�r Felder erstellen
	 */
	function make_lang_field()
	{
		// array f�rs Template definieren
		$this->content->template['language_form'] = array();
		// Ausgeschriebene Version der Inhaltsprache aus der Tabelle holen
		$sql = sprintf("SELECT mv_lang_long
	    FROM %s
	    WHERE mv_lang_id = '%d'",

			$this->cms->tbname['papoo_mv_name_language'],

			$this->db->escape($this->cms->lang_back_content_id)
		);
		$mv_lang_long = $this->db->get_var($sql);
		// wenn es ein neues Feld ist, sind content_list und feld_label nat�rlich leer
		$content_list = "";
		if (is_numeric($this->checked->feldid)) {
			// Label in der Inhaltsprache f�r das Feld aus der Tabelle holen
			$sql = sprintf("SELECT mvcform_label
		FROM %s
		WHERE mvcform_lang_id = '%d'
		AND mvcform_lang_lang = '%d'
		AND mvcform_lang_meta_id = '%d'",

				$this->cms->tbname['papoo_mvcform_lang'],

				$this->db->escape($this->checked->feldid),
				$this->db->escape($this->cms->lang_back_content_id),
				$this->db->escape($this->meta_gruppe)
			);
			$feld_label = $this->db->get_var($sql);
			// Content in der Inhaltsprache f�r das Feld aus der Tabelle holen
			$sql = sprintf("SELECT mvcform_type
		FROM %s
		WHERE mvcform_id = '%d'
		LIMIT 1",

				$this->cms->tbname['papoo_mvcform'],

				$this->db->escape($this->checked->feldid)
			);
			$feldtyp = $this->db->get_var($sql);
			if ($feldtyp == "select"
				|| $feldtyp == "radio"
				|| $feldtyp == "multiselect"
				|| $feldtyp == "check"
				|| $feldtyp == "pre_select") {
				$sql = sprintf("SELECT lookup_id,
		    content
		    FROM %s
		    WHERE lang_id = '%d'
		    AND content <> ''
		    ORDER BY order_id",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_lang_"
					. $this->db->escape($this->checked->feldid),

					$this->db->escape($this->cms->lang_back_content_id)
				);
				$content_list = $this->db->get_results($sql);
			}
			// Preisintervalle
			if ($feldtyp == "preisintervall") {
				$sql = sprintf("SELECT intervalle
		    FROM %s
		    WHERE feld_id = '%d'
		    AND mv_id = '%d'
		    LIMIT 1",

					$this->cms->tbname['papoo_mv_feld_preisintervall'],

					$this->db->escape($this->checked->feldid),
					$this->db->escape($this->checked->mv_id)
				);
				$preisintervall_list = $this->db->get_var($sql);
			}
		}
		// und Ergebnisse ins array f�rs Template schreiben
		$selected_more = 'nodecode:checked="checked"';
		array_push($this->content->template['language_form'], array(
			'language' => $mv_lang_long,
			'lang_id' => $this->cms->lang_back_content_id,
			'selected' => $selected_more,
			'mvcform_label' => $feld_label,
			'mvcform_descrip' => "",
			'mvcform_content_list' => "nobr:" . $content_list,
			'mvcform_preisintervall_list' => "nobr:" . $preisintervall_list
		));
	}

	/**
	 * Daten eines Feldes rausholen
	 */
	function get_field_data()
	{
		// anstatt mit zb. mvcform_id=mvcform_lang_id zu suchen, lieber so und mit LIMIT 1, es ist dadurch eine fast doppelt so schnelle sql Abfrage
		$sql = sprintf("SELECT * FROM %s, %s
	    WHERE mvcform_id = '%d'
	    AND	mvcform_meta_id = '%d'
	    AND mvcform_lang_id ='%d'
	    AND mvcform_lang_meta_id = '%d'
	    AND	mvcform_lang_lang = '%d'
	    LIMIT 1",

			$this->cms->tbname['papoo_mvcform'],

			$this->cms->tbname['papoo_mvcform_lang'],

			$this->db->escape($this->checked->feldid),
			$this->db->escape($this->meta_gruppe),
			$this->db->escape($this->checked->feldid),
			$this->db->escape($this->meta_gruppe),
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$feld_daten = $this->db->get_results($sql, ARRAY_A);
		$no_loesch = array("Benutzername",
			"passwort",
			"email",
			"antwortmail",
			"newsletter",
			"board",
			"active",
			"signatur"
		);

		if (in_array($feld_daten['0']['mvcform_name'], $no_loesch)) $this->content->template['no_loesch'] = "NO";
		// und gibt die Werte ans Template weiter
		$this->content->template['fdat'] = $feld_daten;
		if (isset($this->content->template['fdat'][0]['mvcform_lang_tooltip']))
			$this->content->template['fdat'][0]['mvcform_lang_tooltip'] = "nobr:" . $this->content->template['fdat'][0]['mvcform_lang_tooltip'];
	}

	/**
	 * Feld l�schen
	 */
	function del_field()
	{
		// Holt den Feldname f�r die entsprechende ID aus der Datenbank
		$sql = sprintf("SELECT mvcform_name
	    FROM %s
	    WHERE mvcform_id = '%d'
	    LIMIT 1",

			$this->cms->tbname['papoo_mvcform'],

			$this->db->escape($this->checked->feldid)
		);
		$feld_name = $this->db->get_var($sql);
		// Normale Daten l�schen
		$sql = sprintf("DELETE FROM %s
	    WHERE mvcform_id = '%d'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->feldid)
		);
		$this->db->query($sql);
		// Sprachdaten l�schen
		$sql = sprintf("DELETE FROM %s
	    WHERE mvcform_lang_id = '%d'",
			$this->cms->tbname['papoo_mvcform_lang'],
			$this->db->escape($this->checked->feldid)
		);
		$this->db->query($sql);
		// Preisintervall Eintrag f�r dieses Feld l�schen
		$sql = sprintf("DELETE FROM %s
	    WHERE mv_id = '%d'
	    AND feld_id = '%d'",
			$this->cms->tbname['papoo_mv_feld_preisintervall'],
			$this->db->escape($this->checked->mv_id),
			$this->db->escape($this->checked->feldid)
		);
		$this->db->query($sql);
		// Spalte f�r dieses Feld in der content Tabelle l�schen
		$sql = sprintf("ALTER TABLE %s
	    DROP %s",
			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id),

			$this->db->escape($feld_name)
			. "_" .
			$this->db->escape($this->checked->feldid)
		);
		#$this->db->query($sql);
		// Spalte f�r dieses Feld in den search Tabellen l�schen
		$sql = sprintf("SELECT mv_lang_id
	    FROM %s",
			$this->cms->tbname['papoo_mv_name_language']
		);
		$sprachen = $this->db->get_results($sql);
		if (!empty($sprachen)) {
			foreach ($sprachen as $sprache) {
				$sql = sprintf("ALTER TABLE %s
		    DROP %s",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_search_"
					. $this->db->escape($sprache->mv_lang_id),

					$this->db->escape($feld_name)
					. "_" .
					$this->db->escape($this->checked->feldid)
				);
				$this->db->query($sql);
			}
		}
		// Lookup Tabelle und zugeh�rige Lang Tabelle l�schen
		$sql = sprintf("DROP TABLE IF EXISTS %s",
			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_lookup_"
			. $this->db->escape($this->checked->feldid)
		);
		#$this->db->query($sql);
		$sql = sprintf("DROP TABLE IF EXISTS %s",
			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_lang_"
			. $this->db->escape($this->checked->feldid)
		);
		$this->db->query($sql);
		// Seite erneut laden
		$location_url = $_SERVER['PHP_SELF']
			. "?menuid="
			. $this->checked->menuid
			. "&template="
			. $this->checked->template
			. "&mv_id="
			. $this->checked->mv_id
			. "&delok=1";
		$this->reload($location_url);
	}

	function finde_die_art_der_verwaltung_heraus()
	{
		$sql = sprintf("SELECT mv_art FROM %s
	    WHERE mv_id='%d'", $this->cms->tbname['papoo_mv'], $this->db->escape($this->checked->mv_id));
		$this->mv_art = $this->db->get_var($sql);
	}

	function get_meta_gruppen()
	{
		// Meta Gruppen aus der Datenbank holen, die dieser User sehen darf
		if ($this->have_admin_rights()) {
			$sql = sprintf("SELECT DISTINCT(mv_meta_id),
	    mv_meta_group_name
	    FROM %s
	    ORDER BY mv_meta_group_name",
				$this->cms->tbname['papoo_mv'] . "_meta_" . $this->db->escape($this->checked->mv_id));
			$meta_gruppen = $this->db->get_results($sql, ARRAY_A);
		} else {
			$sql =
				sprintf(
					"SELECT DISTINCT (mv_meta_id), mv_meta_group_name FROM %s,%s,%s
		    WHERE  mv_mpg_id=mv_meta_id
		    AND mv_mpg_group_id=gruppenid
		    AND userid='%d'
		    ORDER BY mv_meta_group_name",
					$this->cms->tbname['papoo_mv'] . "_meta_" . $this->db->escape($this->checked->mv_id),
					$this->cms->tbname['papoo_mv'] . "_mpg_" . $this->db->escape($this->checked->mv_id),
					$this->cms->tbname['papoo_lookup_ug'], $this->db->escape($this->user->userid));
			$meta_gruppen = $this->db->get_results($sql, ARRAY_A);
		}
		return $meta_gruppen;
	}

	/**
	 * Alle Gruppen mit den jeweiligen Feldern
	 */
	function get_form_group_field_list($formid = "")
	{
		// Liste der Gruppen rausholen
		$this->get_form_group_list($formid);
		$groupar = $this->result_groups;
		$this->content->template['gruppen_anzahl'] = count($groupar);
		$i = 0;
		$sql = sprintf("SELECT max(mvcform_id)
	    FROM %s",
			$this->cms->tbname['papoo_mvcform']
		);
		$max_buffer = $this->db->get_var($sql);
		// F�r diese Gruppen durchgehen
		if ((!empty($this->result_groups))) {
			$sql = sprintf("SELECT mv_art FROM %s
		WHERE mv_id = '%d'",
				$this->cms->tbname['papoo_mv'],
				$this->db->escape($this->checked->mv_id)
			);
			$this->content->template['mv_art'] = $this->db->get_var($sql);
			foreach ($this->result_groups as $group) {
				// Alle Felder der jeweiligen Gruppe rausholen
				$sql = sprintf("SELECT DISTINCT *
		    FROM %s, %s
		    WHERE mvcform_group_id = '%d'
		    AND mvcform_lang_meta_id = '%d'
		    AND mvcform_lang_meta_id = mvcform_meta_id
		    AND mvcform_id = mvcform_lang_id
		    AND mvcform_lang_lang = '%d'
		    GROUP BY mvcform_id
		    ORDER BY mvcform_order_id DESC",
					$this->cms->tbname['papoo_mvcform_lang'],
					$this->cms->tbname['papoo_mvcform'],
					$this->db->escape($group['mvcform_group_id']),
					$this->db->escape($this->meta_gruppe),
					$this->db->escape($this->cms->lang_back_content_id)
				);
				$result = $this->db->get_results($sql, ARRAY_A);
				$groupar[$i]['felder'] = $result;
				// damit das Javascript f�r die H�kchen auch weiss welche Formularfelder IDs es pro Gruppe ansprechen soll
				$max_id = 1;
				$min_id = $max_buffer;
				if (!empty($result)) {
					foreach ($result as $row) {
						if ($row['mvcform_id'] < $min_id) $min_id = $row['mvcform_id'];
						if ($row['mvcform_id'] > $max_id) $max_id = $row['mvcform_id'];
					}
				}
				$groupar[$i]['ids_anfang'] = $min_id;
				$groupar[$i]['ids_ende'] = $max_id;
				$i++;
			}
		}
		$sql = sprintf("SELECT *
	    FROM %s, %s
	    WHERE mvcform_form_id = '%d'
	    AND mvcform_meta_id = '%d'
	    AND mvcform_no_group = '1'
	    AND mvcform_id = mvcform_lang_id
	    AND mvcform_lang_lang = '%d'
	    AND mvcform_meta_id = mvcform_lang_meta_id
	    GROUP BY mvcform_id",
			$this->cms->tbname['papoo_mvcform_lang'],
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->mv_id),
			$this->db->escape($this->meta_gruppe),
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if ((!empty($result))) {
			$groupar[$i + 1]['felder'] = $result;
			$groupar[$i + 1]['mvcform_group_text'] = "Keine Gruppe / No group";
			$groupar[$i + 1]['mvcform_group_id'] = "xx";
		}
		$this->content->template['gfliste'] = $groupar;
		// niedrigste Gruppenid f�r diese Verwaltung ans Template weitergeben, f�rs javascript
		$sql = sprintf("SELECT MIN(mvcform_group_id)
	    FROM %s
	    WHERE mvcform_form_id = '%d'
	    AND mvcform_meta_id = '%d'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->mv_id),
			$this->db->escape($this->meta_gruppe)
		);
		$min_group = $this->db->get_var($sql);
		if (empty($min_group)) $min_group = 1;
		// niedrigste Feld ID in der Verwaltung
		$sql = sprintf("SELECT MIN(mvcform_id)
	    FROM %s
	    WHERE mvcform_form_id = '%d'
	    AND mvcform_meta_id = '%d'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->mv_id),
			$this->db->escape($this->meta_gruppe)
		);
		$min_feld = $this->db->get_var($sql);
		// alle Felder IDs ans Template, da Javascript zu bl�d ist, falls eine L�cke in den Ids existiert, weil schonmal ein Feld gel�scht wurde, gleiches Porb mit gruppen
		$sql = sprintf("SELECT mvcform_id
	    FROM %s
	    WHERE mvcform_form_id = '%d'
	    AND mvcform_meta_id = '%d'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->mv_id),
			$this->db->escape($this->meta_gruppe)
		);
		$felder_ids = $this->db->get_results($sql, ARRAY_A);
		$sql = sprintf("SELECT DISTINCT(mvcform_group_id)
	    FROM %s
	    WHERE mvcform_form_id = '%d'
	    AND mvcform_meta_id = '%d'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->mv_id),
			$this->db->escape($this->meta_gruppe)
		);
		$group_ids = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['feld_anfang_alle'] = $min_feld;
		$this->content->template['gruppen_anfang'] = $min_group;
		$this->content->template['felder_ids'] = $felder_ids;
		$this->content->template['group_ids'] = $group_ids;
	}

	/**
	 * Felder sortieren
	 */
	function switch_order()
	{
		// doch oldschool foreach....
		if (!empty($this->checked)) {
			foreach ($this->checked as $key => $value) {
				if (strpos($key, "submitorder_fld") !== false) $feld_id = end(explode("_", $key));
			}
		}

		if (!empty($feld_id)) {
			$feld_order_id = "mvcform_order_id_" . $feld_id;
			if (is_numeric($feld_id)
				&& is_numeric($this->checked->$feld_order_id)) {
				$sql = sprintf("SELECT mvcform_group_id
		    FROM %s
		    WHERE mvcform_id = '%s'
		    AND mvcform_meta_id = '%d'
		    LIMIT 1",

					$this->cms->tbname['papoo_mvcform'],

					$this->db->escape($feld_id),
					$this->db->escape($this->meta_gruppe)
				);
				$group_id = $this->db->get_var($sql);
				// Orderid des aktuellen
				$sql = sprintf("SELECT mvcform_order_id
		    FROM %s
		    WHERE mvcform_id = '%s'
		    AND mvcform_meta_id = '%d'
		    LIMIT 1",

					$this->cms->tbname['papoo_mvcform'],

					$this->db->escape($feld_id),
					$this->db->escape($this->meta_gruppe)
				);
				$alt_order_id = $this->db->get_var($sql);
				// Orderid des Vorg�ngers auf order des Nachfolgers setzen
				$sql = sprintf("UPDATE %s SET mvcform_order_id = '%s'
		    WHERE mvcform_order_id = '%s'
		    AND mvcform_group_id = '%d'
		    AND mvcform_meta_id = '%d'
		    LIMIT 1",

					$this->cms->tbname['papoo_mvcform'],

					$this->db->escape($alt_order_id),
					$this->db->escape($this->checked->$feld_order_id),
					$this->db->escape($group_id),
					$this->db->escape($this->meta_gruppe)
				);
				$this->db->query($sql);
				//Orderid des alten auf neu setzen
				$sql = sprintf("UPDATE %s SET mvcform_order_id = '%s'
		    WHERE mvcform_id = '%s'
		    AND mvcform_meta_id = '%d'
		    LIMIT 1",

					$this->cms->tbname['papoo_mvcform'],

					$this->db->escape($this->checked->$feld_order_id),
					$this->db->escape($feld_id),
					$this->db->escape($this->meta_gruppe)
				);
				$this->db->query($sql);
				$this->reorder_fields($group_id);
			}
		}
	}

	/**
	 * Gruppen sortieren
	 */
	function switch_order_groups()
	{
		if (!empty($this->checked)) {
			foreach ($this->checked as $key => $value) {
				if (strpos($key, "submitorder_gr") !== false) $group_id = end(explode("_", $key));
			}
		}

		if (!empty($group_id)) {
			$group_order_id = "mvcform_group_order_id_" . $group_id;
			if (is_numeric($group_id)
				&& is_numeric($this->checked->$group_order_id)) {
				// Orderid des Vorg�ngers
				$sql = sprintf("SELECT mvcform_group_order_id
		    FROM %s
		    WHERE mvcform_group_id = '%s'
		    AND mvcform_group_form_meta_id = '%d'
		    LIMIT 1",

					$this->cms->tbname['papoo_mvcform_group'],

					$this->db->escape($group_id),
					$this->db->escape($this->meta_gruppe)
				);
				$alt_order_id = $this->db->get_var($sql);
				// Orderid des Vorg�ngers auf order des Nachfolgers setzen
				$sql = sprintf("UPDATE %s SET mvcform_group_order_id = '%s'
		    WHERE mvcform_group_order_id = '%s'
		    AND mvcform_group_form_meta_id = '%d'
		    LIMIT 1",

					$this->cms->tbname['papoo_mvcform_group'],

					$this->db->escape($alt_order_id),
					$this->db->escape($this->checked->$group_order_id),
					$this->db->escape($this->meta_gruppe)
				);
				$this->db->query($sql);
				$sql = sprintf("UPDATE %s SET mvcform_group_order_id = '%s'
		    WHERE mvcform_group_id = '%s'
		    AND mvcform_group_form_meta_id = '%d'
		    LIMIT 1",
					$this->cms->tbname['papoo_mvcform_group'],
					$this->db->escape($this->checked->$group_order_id),
					$this->db->escape($group_id),
					$this->db->escape($this->meta_gruppe)
				);
				$this->db->query($sql);
				$this->reorder_groups($this->checked->mv_id);
			}
		}
	}

	/**
	 * Sicherung der Datenbank erstellen / einspielen
	 */
	function mv_dump($plugin, $pfad = "", $no = "xyz")
	{
		// da ja alles dynamisch ist, hier eine Dump Funktion zusammengeschustert aus der diverse Klasse und aus den Dev Tools
		global $intern_stamm;
		$this->intern_stamm = &$intern_stamm;

		// Dump herstellen
		if ($this->checked->makedump == 1) {
			global $db_praefix;
			$this->lokal_db_praefix = $db_praefix;
			// baue Liste aus allen Tabellennamen zusammen, die mit den in $plugin enthaltenen K�rzeln anfangen
			$table_list = array();
			// alle Tabellen die es in der Datenbank gibt
			$query = "SHOW TABLES";
			$result = $this->db->get_results($query);

			if ($result) {
				foreach ($result as $eintrag) {
					if ($eintrag) {
						foreach ($eintrag as $titel => $tabellenname) {
							if (strpos("xxx" . $tabellenname,
								$this->lokal_db_praefix)) // "xxx" wird ben�tigt, da praefix an Stelle 0 beginnt und strpos damit false w�re
							{
								// wenn $plugin nichts enth�llt, dann alle Tabellennamen zwischenspeichern
								if (empty($plugin)) {
									$temp_array = array("tablename" => $tabellenname);
									$table_list[] = $temp_array;
								} // wenn nicht, dann nur die Tabellennamen zwischenspeichern, die auch mit den in $plugin enthaltenen K�rzeln anfangen
								else {
									$plugin_list = explode(",", $plugin);
									foreach ($plugin_list as $tbn) {
										if (stristr($tabellenname, $tbn)) {
											$temp_array = array("tablename" => $tabellenname);
											if (!in_array($temp_array, $table_list)) {
												$table_list[] = $temp_array;
											}
										}
									}
								}
							}
						}
					}
				}
			}
			// so jede in $table_list gespeicherte Tabelle als Teil-Dump in templates_c/dumpnrestore.sql zwischenspeichern
			$this->dumpnrestore->doupdateok = "ok";
			$this->dumpnrestore->donewdump = "ok";
			$poese_hacker = md5(time()) . md5(rand()); // Zeitstempel.Zufallszahl jeweils mit md5 verschl�sseln
			$this->dumpnrestore->dump_filename = "templates_c/mv_dump_" . $poese_hacker . ".sql";
			$this->dumpnrestore->dump_save();          // L�scht alte Sicherungs-Datei f�r append.

			foreach ($table_list as $tabelle) {
				$this->dumpnrestore->querys = $this->dumpnrestore->dump_load($tabelle['tablename'], 0, 10000);
				$this->dumpnrestore->dump_save("append"); // jeweils ans Ende der Datei "dranh�ngen"
			}
			// gibt einen Downlaod Link der Datei templates_c/dumpnrestore.sql aus
			$this->content->template['newdump_message'] = '<a href="' . $this->dumpnrestore->dump_filename . '">'
				. $this->content->template['plugin']['mv']['sicherung_download'] . '</a>';
			/* Datei wird direkt gedownloaded -> probs mit Time Out bei zu gro�en Dumps
			// l�dt den Inhalt der Datei templates_c/dumpnrestore.sql in einen String
			$handler = fopen("templates_c/dumpnrestore.sql", "r");
			$content = fread ($handler, filesize ("templates_c/dumpnrestore.sql"));
			fclose($handler);
			// Oder bietet die Datei als Download direkt im Browser an
			$this->intern_stamm->make_download($content);*/
		}

		// Dump einspielen
		if ($this->checked->backdump == 1
			|| $this->checked->restore == "lala") {
			// damit die Dump Funktion auch die Datei findet, ka warum das sonst nicht funzt^^
			$tmp_file = $this->cms->pfadhier . "/interna/templates_c/restore.sql";
			move_uploaded_file($_FILES['myfile']['tmp_name'], $tmp_file);
			$this->intern_stamm->make_restore();
		}
	}

	/**
	 * holt die Daten aus der Gruppe Datenbank und bereited sie f�rs Template
	 * vor
	 */
	function get_group_user()
	{
		$sql = sprintf("SELECT * FROM %s", $this->cms->tbname['papoo_gruppe']);
		$result = $this->db->get_results($sql);
		$groupies = array();
		$temp_group = array();

		if (!empty($result)) {
			// geht die einzelnen Gruppen durch
			foreach ($result as $row) {
				array_push($groupies, $row->gruppenname);
				$temp_group[$row->gruppenname] = $row->gruppeid;
			}
		}
		$this->content->template['groupies'] = $groupies;
		// holt die Daten aus der settings Datenbank und ab ins Template
		$sql = sprintf("SELECT * FROM %s
	    WHERE mv_id='%d'", $this->cms->tbname['papoo_mv'], $this->db->escape($this->checked->mv_id));
		$result = $this->db->get_results($sql);
		$this->content->template['group_id'] = $result[0]->mv_set_group_id;
		$this->content->template['group_name'] = $result[0]->mv_set_group_name;
	}

	/**
	 * Die Gruppenzugeh�rigkeit aller Mitglieder �ndern
	 */
	function change_group_user()
	{
		$sql = sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_gruppe']
		);
		$result = $this->db->get_results($sql);
		$groupies = array();
		$temp_group = array();
		if (!empty($result)) {
			// geht die einzelnen Gruppen durch
			foreach ($result as $row) {
				array_push($groupies, $row->gruppenname);
				$temp_group[$row->gruppenname] = $row->gruppeid;
			}
		}
		$result = "";
		$this->content->template['groupies'] = $groupies;
		$tb_check = "papoo_mv_content_" . $this->db->escape($this->checked->mv_id);
		if (!empty($this->cms->tbname[$tb_check])
			&& $this->checked->mv_art == "2") {
			// hole alle userids von allen Mitglieder aus der mv_content Tabelle
			$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
			$sql = sprintf("SELECT mv_content_userid FROM %s",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_search_"
				. $lang
			);
			$result = $this->db->get_results($sql);
		}
		if (!empty($result)) {
			// und �ndere bei jedem Mitglied die Gruppen ID in der lookup_ug Tabelle
			foreach ($result as $row) {
				$sql = sprintf("UPDATE %s SET gruppenid = '%d'
		    WHERE userid = '%d'
		    AND gruppenid = '%d'",
					$this->cms->tbname['papoo_lookup_ug'],
					$this->db->escape($temp_group[$this->checked->group_name]),
					$this->db->escape($row->mv_content_userid),
					$this->db->escape($this->content->template['group_id'])
				);
				$this->db->query($sql);
			}
		}
		// �ndere Gruppen ID auch in der settings Tabelle
		$sql = sprintf("UPDATE %s SET mv_set_group_id = '%s',
	    mv_set_group_name = '%s'
	    WHERE mv_id = '%d'",
			$this->cms->tbname['papoo_mv'],
			$this->db->escape($temp_group[$this->checked->group_name]),
			$this->db->escape($this->checked->group_name),
			$this->db->escape($this->checked->mv_id)
		);
		$this->db->query($sql);
		// damit im Frontend auch die neuen Werte angezeigt werden
		$this->content->template['group_name'] = $this->checked->group_name;
		$this->content->template['group_id'] = $temp_group[$this->checked->group_name];
	}

	/**
	 * Die ausgew�hlte Inhaltsprache in ein array f�rs Template schreiben
	 */
	function make_lang()
	{
		// array f�rs Template definieren
		$this->content->template['language_form'] = array();
		// Ausgeschriebene Version der Inhaltsprache aus der Tabelle holen
		$sql = sprintf("SELECT mv_lang_long FROM %s
	    WHERE mv_lang_id='%d'", $this->cms->tbname['papoo_mv_name_language'],
			$this->db->escape($this->cms->lang_back_content_id));
		$mv_lang_long = $this->db->get_var($sql);
		// und Ergebnisse ins array f�rs Template schreiben
		$selected_more = 'nodecode:checked="checked"';
		array_push($this->content->template['language_form'], array(
			'language' => $mv_lang_long,
			'lang_id' => $this->cms->lang_back_content_id,
			'selected' => $selected_more
		));
	}

	/**
	 * Spracheinstellungen der Gruppen ausgeben
	 */
	function make_lang_group()
	{
		// array f�rs Template definieren
		$this->content->template['language_form'] = array();
		// Ausgeschriebene Version der Inhaltsprache aus der Tabelle holen
		$sql = sprintf("SELECT mv_lang_long
	    FROM %s
	    WHERE mv_lang_id = '%d'",
			$this->cms->tbname['papoo_mv_name_language'],
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$mv_lang_long = $this->db->get_var($sql);
		// und Ergebnisse ins array f�rs Template schreiben
		$selected_more = 'nodecode:checked="checked"';
		$text = "";
		$meta_id = $this->meta_gruppe;
		if (!defined("admin")
			and is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
		// Wenn Editieren
		if ((is_numeric($this->checked->grupid))) {
			$sql = sprintf("SELECT mvcform_group_text
		FROM %s
		WHERE mvcform_group_lang_id = '%d'
		AND mvcform_group_lang_lang = '%d'
		AND mvcform_group_lang_meta = '%d'",
				$this->cms->tbname['papoo_mvcform_group_lang'],
				$this->db->escape($this->checked->grupid),
				$this->db->escape($this->cms->lang_back_content_id),
				$this->db->escape($meta_id)
			);
			$text = $this->db->get_var($sql);
		}
		array_push($this->content->template['language_form'], array(
			'language' => $mv_lang_long,
			'lang_id' => $this->cms->lang_back_content_id,
			'selected' => $selected_more,
			'mvcform_group_text' => $text
		));
	}

	/**
	 * Daten f�r Standardsprache in Template ausgeben
	 */
	function make_lang_old()
	{
		// welche Sprache ist die Standardsprache f�r diese Flexverwaltung
		$sql = sprintf("SELECT * FROM %s
	    WHERE mv_lang_standard='1'", $this->cms->tbname['papoo_mv_name_language']);
		$resultlang = $this->db->get_results($sql);
		$this->content->template['language_form'] = array();

		if (!empty($resultlang)) {
			// zuweisen welche Sprache ausgew�hlt sind
			foreach ($resultlang as $rowlang) {
				// checken wenn Sprache gew�hlt
				$selected_more = 'nodecode:checked="checked"';
				array_push($this->content->template['language_form'], array(
					'language' => $rowlang->mv_lang_long,
					'lang_id' => $rowlang->mv_lang_id,
					'selected' => $selected_more,
				));
			}
		} else {
			$sql = sprintf("SELECT * FROM %s
		WHERE mv_lang_id='1'", $this->cms->tbname['papoo_mv_name_language']);
			$resultlang = $this->db->get_results($sql);
			if (!empty($resultlang)) {
				// zuweisen welche Sprache ausgew�hlt sind
				foreach ($resultlang as $rowlang) {
					// checken wenn Sprache gew�hlt
					$selected_more = 'nodecode:checked="checked"';
					array_push($this->content->template['language_form'], array(
						'language' => $rowlang->mv_lang_long,
						'lang_id' => $rowlang->mv_lang_id,
						'selected' => $selected_more,
					));
				}
			}
		}
	}

	/**
	 * Liste der Verwaltungen
	 */
	function get_form_list()
	{
		$sql = sprintf("SELECT mv_id,
	    mv_name,
	    mv_art,
	    mv_set_group_name,
	    mv_set_suchmaske,
	    gruppenname
	    FROM %s T1
	    LEFT JOIN %s T2 ON (T1.mv_set_group_id = T2.gruppeid)
	    ORDER BY mv_name",
			$this->cms->tbname['papoo_mv'],
			$this->cms->tbname['papoo_gruppe']

		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['list'] = $result;
	}

	/**
	 * Seite neu laden
	 */
	function reload($location_url = "")
	{
		if ($_SESSION['debug_stopallredirect'])
			echo '<a href="'
				. $location_url
				. '">'
				. $this->content->template['plugin']['mv']['weiter']
				. '</a>';
		else header("Location: $location_url");
		exit;
	}

	/**
	 * mv::make_special_rights_meta()
	 * Diese Funktion bestimmt ob generell Eintr�ge erstellt werden d�rfen,
	 * D�rfen neue Eintr�ge direkt eingetragen werden? Dann ist keine vorherige
	 * Erstellung eines Accounts mit E-Mail Verifikation notwendig. Nachteil -
	 * jeder kann sich eintragen ohne Best�tigung. und Sollen neue Eintr�ge aus
	 * dem Frontend direkt f�r alle sichtbar sein? Wenn kein H�kchen gesetzt
	 * ist dann sind die Eintr�ge standardm��ig gesperrt und m��en manuell
	 * freigeschaltet werden.
	 * @return void
	 */
	function make_special_rights_meta()
	{
		$meta_id = $this->meta_gruppe;
		if (!defined("admin")
			and is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
		$sql = sprintf("SELECT * FROM %s
	    WHERE mv_meta_id = '%d'
	    LIMIT 1",
			$this->cms->tbname['papoo_mv']
			. "_meta_"
			. $this->db->escape($this->checked->mv_id),

			$this->db->escape($meta_id)
		);
		$meta_gruppe_name = $this->db->get_results($sql, ARRAY_A);
		if (count($meta_gruppe_name)) {
			foreach ($meta_gruppe_name['0'] as $key => $value) {
				$this->content->template[$key] = $value;
				$this->$key = $value;
			}
		}
	}

	/**
	 * Mitgliederliste Content bef�llen
	 */
	function fp_content_front()
	{
		global $template;
		//Rechte Spezial raussuchen und erstellen
		$this->make_special_rights_meta();
		// added khmweb 20.11.09 Lese-Schreibrechte des Users ans Template �bergeben
		$this->mv_get_lese_schreibrechte_fuer_meta_ebene();
		//Wenn generell �berhaupt ein Eintrag gemacht werden darf
		if ($this->mv_meta_allow_frontend == 1) {
			// wenn dzvhae Auftritt und mv_id=id der Mitgliederverwaltung
			if ($dzvhae_system_id
				&& $dzvhae_mv_id == $this->checked->mv_id) {
				// Holt die Maximale dzvhae user ID aus der Datenbank
				$sql = sprintf("SELECT MAX(mv_dzvhae_system_id)
					FROM %s",
					$this->cms->tbname['papoo_mv_dzvhae']
				);
				$max_dzvhae_system_id = $this->db->get_var($sql);
			}
			// Holt die Gruppe aus der Datenbank, die editieren darf, wenn der User dieser Gruppe angeh�rt. Sonst kommt da nix.
			$sql = sprintf("SELECT * FROM %s, %s
				WHERE gruppenid = mv_set_group_id
				AND userid = '%d'
				AND mv_id = '%d'",

				$this->cms->tbname['papoo_lookup_ug'],

				$this->cms->tbname['papoo_mv'],

				$this->db->escape($this->user->userid),
				$this->db->escape($this->checked->mv_id)
			);
			$gruppe_id = $this->db->get_results($sql);
			// Neues Verfahren: Erlaubt der Standardverwaltung auch dann Eintr�ge im FE, wenn dies so in der Rechteverwaltung angegeben ist
			// s. Mail von Carsten 8.2.11 13_49
			//Wenn Insert ok, �bergeben, um das Template auf gespeichert zu sezten
			$this->content->template['insert'] = $this->checked->insert;
			$sql = sprintf("SELECT mv_art FROM %s
				WHERE mv_id = '%d'",

				$this->cms->tbname['papoo_mv'],

				$this->db->escape($this->checked->mv_id)
			);
			$this->content->template['mv_art'] = $mv_art = $this->db->get_var($sql);
			$sql = sprintf("SELECT mv_id FROM %s
				WHERE mv_art = '2'
				LIMIT 1",
				$this->cms->tbname['papoo_mv']
			);
			$this->content->template['mv_id_of_mv'] = $mv_id_of_mv = $this->db->get_var($sql);
			// Keine Anzeige des Formulars im FE, wenn es eine Standardverwaltung ist und der User nicht der Gruppe angeh�rt, die editieren darf
			// s. Einstellungen unter "Verwaltung bearbeiten". Hier unbedingt Grp. angeben, wenn im FE Create/Edit erfolgen soll.
			if (!($mv_art == 1 // 1 = Standard, 2 = MV, 3 = Kalender
				&& empty($gruppe_id)
				&& !defined("admin"))) {
				// wenn mehr als 3 bzw. 3 Spalten in der Tabelle sind, dann gibts auch schon Felder zum eintippseln
				$sql = sprintf("SHOW COLUMNS FROM %s",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_search_"
					. $this->db->escape($this->cms->lang_id)
				);
				$result = $this->db->get_results($sql);
				$spalten_anzahl = count($result);
				// Fallunterscheidung bleibt drin, falls doch noch unterschiedliche Anzahlen am Ende rauskommen
				if (($mv_art == 1 && $spalten_anzahl > 3)
					|| ($mv_art == 2 && $spalten_anzahl > 3)
					|| ($mv_art == 3 && $spalten_anzahl > 3)) {
					// Beim Ausgeben der Formularfelder wird die Variable abgefragt
					$this->password_new = "ja";
					$this->save_multiselect_session_front();
					$this->content->template['zweiterunde'] = $this->checked->zweiterunde;
					// Formular anzeigen
					$meta_id = $this->meta_gruppe;
					if (!defined("admin")
						and is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
					// Einleitungs-, Ausleitungstext und Mailtexte ans Template geben
					$sql = sprintf("SELECT * FROM %s
						WHERE mv_meta_lang_id = '%d'
						AND mv_meta_lang_lang_id = '%d'
						LIMIT 1",

						$this->cms->tbname['papoo_mv']
						. "_meta_lang_"
						. $this->db->escape($this->checked->mv_id),

						$this->db->escape($meta_id),
						$this->db->escape($this->cms->lang_id)
					);
					$result = $this->db->get_results($sql, ARRAY_A);
					if (!empty($result)) {
						$this->content->template['mv_meta_top_text'] = $result[0]['mv_meta_top_text'];
						$this->content->template['mv_meta_bottom_text'] = $result[0]['mv_meta_bottom_text'];
						$this->content->template['mv_meta_antwort_text'] = "nodecode:" . $result[0]['mv_meta_antwort_text'];

						$this->content->template['mv_mail_text_create_admin'] = "nodecode:" . $result[0]['mv_mail_text_create_admin'];
						$this->content->template['mv_mail_text_create_user'] = "nodecode:" . $result[0]['mv_mail_text_create_user'];
						$this->content->template['mv_mail_text_change_user'] = "nodecode:" . $result[0]['mv_mail_text_change_user'];
						$this->content->template['mv_mail_text_change_admin'] = "nodecode:" . $result[0]['mv_mail_text_change_admin'];
					}
					$this->content->template['message1'] = "ok";
					// Formular raussuchen und anzeigen
					$this->content->template['formok'] = "ok";
					$this->content->template['mv_id'] = $this->checked->mv_id;
					$this->content->template['mv_dzvhae_system_id'] = $max_dzvhae_system_id;
					require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_form_check_front.php');
					if ($this->cms->stamm_kontakt_spamschutz and $this->user->userid == 11) // nur, wenn nicht eingeloggt
					{
						global $spamschutz;
						if ($spamschutz->is_spam
							and $this->checked->mv_submit == $this->content->template['plugin']['mv']['Eintragen']) {
							$this->content->template['fehlerliste'][] = "Spamschutz: Fehler";
							$this->content->template['mv_spam_schutz_error'] = 1;
							$this->insert_ok = false;
						}
					}
					$this->front_get_form($this->checked->mv_id);
					// Daten speichern
					if ($this->checked->mv_submit == $this->content->template['plugin']['mv']['Eintragen']) {
						if ($this->insert_ok == true) // kein Fehler
						{
							require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_content_entry.php');
							#$this->make_content_entry();
							// Sprachtabellen f�r die schnellere Suche
							$mv_content_id = $this->checked->mv_content_id;
							$update_insert = "insert";
							require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/update_lang_search_row.php');
							#$this->update_lang_search_row($this->checked->mv_content_id, "insert");
							// $_SESSION Werte f�r den multiselect wieder l�schen
							$sql = sprintf("SELECT * FROM %s
								WHERE mvcform_type = 'multiselect'
								AND mvcform_form_id = '%d'",

								$this->cms->tbname['papoo_mvcform'],

								$this->db->escape($this->checked->mv_id)
							);
							$result = $this->db->get_results($sql);
							if (!empty($result)) {
								foreach ($result as $row) {
									if (!empty($_SESSION["mvcform" . $row->mvcform_name . "_" . $row->mvcform_id]))
										unset($_SESSION["mvcform" . $row->mvcform_name . "_" . $row->mvcform_id]);
								}
							}
							if ($this->checked->fertig != 1) {
								$location_url = $_SERVER['PHP_SELF']
									. "?menuid="
									. $this->checked->menuid
									. "&fertig=1&template="
									. $this->checked->template
									. "&mv_id="
									. $this->checked->mv_id
									. "&insert=ok";
								if ($_SESSION['debug_stopallredirect'])
									echo '<a href="'
										. $location_url
										. '">'
										. $this->content->template['plugin']['mv']['weiter']
										. '</a>';
								else header("Location: $location_url");
								exit;
							} else {
								$location_url = $_SERVER['PHP_SELF']
									. "?menuid="
									. $this->checked->menuid
									. "&fertig=1&template="
									. $this->checked->template
									. "&mv_id="
									. $this->checked->mv_id . "&insert=ok";
								if ($_SESSION['debug_stopallredirect'])
									echo '<a href="'
										. $location_url
										. '">'
										. $this->content->template['plugin']['mv']['weiter']
										. '</a>';
								else header("Location: $location_url");
								exit;
							}
						}
					}
				} else $this->content->template['noch_kein_feld'] = $this->content->template['plugin']['mv']['noch_kein_feld'];
			}
		}
	}

	/**
	 * Holt die Werte des Mitglieds $mv_content_id aus der Tabelle
	 **/
	function get_mv_content($mv_content_id)
	{
		$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		$sql = sprintf("SELECT * FROM %s
	    WHERE mv_content_id = '%s'",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_search_"
			. $lang,

			$this->db->escape($mv_content_id)
		);
		return $this->db->get_results($sql);
	}

	/**
	 * Multiselect Feld Eintrag in $_SESSION zwischenspeichern oder L�schen
	 **/
	function save_multiselect_session()
	{
		// holt alle Felder die Multiselectfelder sind aus der Datenbank
		$sql = sprintf("SELECT * FROM %s
	    WHERE mvcform_type = 'multiselect'
	    AND mvcform_form_id = '%d'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->mv_id)
		);
		$result = $this->db->get_results($sql);
		if (!empty($result)) {
			// geht die Felder einzeln durch
			foreach ($result as $row) {
				$form_name = "mvcform" . $row->mvcform_name . "_" . $row->mvcform_id;
				$multiselect_name = "mvcform" . $row->mvcform_name . "_" . $row->mvcform_id;
				if ($this->checked->$multiselect_name) {
					if (empty($_SESSION[$multiselect_name])) $_SESSION[$multiselect_name] = array();
					if (!in_array($this->checked->$form_name, $_SESSION[$multiselect_name])) {
						$multi_name = $this->get_multi_name($this->checked->$form_name, $row->mvcform_id);
						$_SESSION[$multiselect_name][$this->checked->$form_name] = $multi_name;
						$this->content->template['multiselect_anker'] = "anker_" . $multiselect_name;
					}
				}
				// Multiselect Eintrag wieder aus der Session l�schen
				$multiselect_name .= "mendel";
				if ($this->checked->$multiselect_name) {
					// + 1 weil ein Leerzeichen vor dem "entfernen" ist
					$multi_option = substr($this->checked->$multiselect_name, 0, -(strlen($this->content->template['plugin']['mv']['entfernen']) + 1));
					$multiselect_name = substr($multiselect_name, 0, -6); // -6 weil "mendel" 6 Buchstaben hat und raus muss:)
					unset($_SESSION[$multiselect_name][$multi_option]);
					$this->content->template['multiselect_anker'] = "anker_" . $multiselect_name;
				}
			}
		}
	}

	/**
	 * Multiselect Feld Eintrag in $_SESSION zwischenspeichern oder L�schen
	 **/
	function save_multiselect_session_front()
	{
		// holt alle Felder, die Multiselectfelder sind, aus der Datenbank
		$sql = sprintf("SELECT *
	    FROM %s
	    WHERE mvcform_type = 'multiselect'
	    AND mvcform_form_id = '%d'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->mv_id)
		);
		$result = $this->db->get_results($sql);
		if (!empty($result)) {
			// geht die Felder einzeln durch
			foreach ($result as $row) {
				$form_name = "mvcform" . $row->mvcform_name . "_" . $row->mvcform_id;
				$multiselect_name = "mvcform" . $row->mvcform_name . "_" . $row->mvcform_id;
				if ($this->checked->$multiselect_name) {
					if (empty($_SESSION[$multiselect_name])) $_SESSION[$multiselect_name] = array();
					if (!in_array($this->checked->$form_name, $_SESSION[$multiselect_name])) {
						$multi_name = $this->get_multi_name_front($this->checked->$form_name, $row->mvcform_id);
						$_SESSION[$multiselect_name][$this->checked->$form_name] = $multi_name;
						$this->content->template['multiselect_anker'] = "anker_" . $multiselect_name;
					}
				}
				// Multiselect Eintrag wieder aus der Session l�schen
				$multiselect_name .= "mendel";
				if ($this->checked->$multiselect_name) {
					// +1 weil ein Leerzeichen vor dem "entfernen" ist
					$multi_option = substr($this->checked->$multiselect_name, 0, -(strlen($this->content->template['plugin']['mv']['entfernen']) + 1));
					$multiselect_name = substr($multiselect_name, 0, -6); // -6 weil "mendel" 6 Buchstaben hat und raus muss
					unset($_SESSION[$multiselect_name][$multi_option]);
					$this->content->template['multiselect_anker'] = "anker_" . $multiselect_name;
				}
			}
		}
	}

	/**
	 * Liefert den Content f�r die lookup_id aus der lang Tabelle f�r das Feld
	 * feld_id
	 **/
	function get_multi_name($lookup_id, $feld_id)
	{
		$sql = sprintf("SELECT content
	    FROM %s
	    WHERE lookup_id <> 0
	    AND lookup_id = '%d'
	    AND lang_id = '%d'
	    ORDER BY order_id",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_lang_"
			. $this->db->escape($feld_id),

			$this->db->escape($lookup_id),
			$this->db->escape($this->cms->lang_back_content_id)
		);
		return $this->db->get_var($sql);
	}

	/**
	 * Liefert den Content f�r die lookup_id aus der lang Tabelle f�r das Feld
	 * feld_id
	 **/
	function get_multi_name_front($lookup_id, $feld_id)
	{
		$sql = sprintf("SELECT content
	    FROM %s
	    WHERE lookup_id<>0
	    AND lookup_id='%d'
	    AND lang_id='%d'
	    ORDER BY order_id",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_lang_"
			. $this->db->escape($feld_id),

			$this->db->escape($lookup_id),
			$this->db->escape($this->cms->lang_id)
		);
		return $this->db->get_var($sql);
	}

	/**
	 * mv::mv_check_ob_user_existiert()
	 * Testen ob der Username schon existiert in der papoo_user
	 * Gibt true (existiert noch nicht) oder false (existiert) zur�ck
	 *
	 * @param mixed $user
	 * @param mixed $user
	 * @return void
	 */
	function mv_check_ob_user_existiert($user)
	{
		$id = $this->user->userid;
		if (defined("admin")) {
			$id = $this->db->escape($this->checked->userid);
			//Wenn leer, dann kommt von Suche
			if (empty($id)) {
				//Userid rausholen
				$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
				$sql = sprintf("SELECT mv_content_userid
		    FROM %s
		    WHERE mv_content_id = '%d'",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_search_"
					. $lang,

					$this->db->escape($this->checked->mv_content_id)
				);
				$id = $this->db->get_var($sql);
			}
		} else {
			//Userid rausholen
			$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
			$sql = sprintf("SELECT mv_content_userid
		FROM %s
		WHERE mv_content_id = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_search_"
				. $lang,

				$this->db->escape($this->checked->mv_content_id)
			);
			$id = $this->db->get_var($sql);
		}
		$sql = sprintf("SELECT * FROM %s
	    WHERE username = '%s'
	    AND userid != '%d'",
			$this->cms->tbname['papoo_user'],
			$this->db->escape($user),
			$id
		);
		$result = $this->db->get_results($sql);
		return empty($result) ? true : false; // user exists not / exists
	}

	/**
	 * mv::mv_check_ob_email_existiert()
	 * Testen ob  die E-Mail schon existiert
	 * Gibt true (existiert noch nicht) oder false (existiert) zur�ck
	 *
	 * @param mixed $user
	 * @param mixed $email
	 * @return void
	 */
	function mv_check_ob_email_existiert($email)
	{
		$id = $this->user->userid;
		if (defined("admin")) {
			$id = $this->db->escape($this->checked->userid);
			//Wenn leer, dann kommt von Such
			if (empty($id)) {
				//Userid rausholen
				$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
				$sql = sprintf("SELECT mv_content_userid
		    FROM %s
		    WHERE mv_content_id = '%d'",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_search_"
					. $lang,

					$this->db->escape($this->checked->mv_content_id)
				);
				$id = $this->db->get_var($sql);
			}
		}
		if ($id) {
			$sql = sprintf("SELECT * FROM %s
		WHERE email = '%s'
		AND userid != '%d'",
				$this->cms->tbname['papoo_user'],
				$this->db->escape($email),
				$this->db->escape($id)
			);
			$result = $this->db->get_results($sql);
		}
		if (!empty($result)
			&& !empty($email)) return false;
		return true;
	}

	/**
	 * L�scht den Eintrag mit der content_id aus der lookup Tabelle mv_id /
	 * feld_id
	 **/
	function delete_lookup($mv_id, $feld_id, $content_id)
	{
		return;
		$sql = sprintf("DELETE FROM %s
	    WHERE content_id = '%d'",
			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($mv_id)
			. "_lookup_"
			. $this->db->escape($feld_id),
			$this->db->escape($content_id)
		);
		#$this->db->query($sql);
	}

	/**
	 * F�gt einen neuen Eintrag in der Lookup Tabelle mv_id / feld_id hinzu
	 **/
	function make_lookup($mv_id, $feld_id, $content_id, $lookup_id)
	{
		return;
		$sql = sprintf("INSERT INTO %s
	    SET content_id = '%d',
	    lookup_id = '%d'",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($mv_id)
			. "_lookup_"
			. $this->db->escape($feld_id),

			$this->db->escape($content_id),
			$this->db->escape($lookup_id)
		);
		#$this->db->query($sql);
	}

	/**
	 * Einbindung der Ligthbox Galerie
	 *
	 * @param unknown_type $modus
	 */
	function make_lightbox()
	{
		// Nur einbinden, wenn Lightbox gewollt ist
		if ($this->mv_show_lightbox_galerie == 1
			or $this->mv_show_lightbox_single == 1) {
			/*$headerdat = array();
			$headerdat[] = "nobr:"
							. '<script type="text/javascript">
									var loadingImage = "' . PAPOO_WEB_PFAD . '/bilder/loading.gif";
									var closeButton = "' . PAPOO_WEB_PFAD . '/bilder/close.gif";
									var fileLoadingImage = "' . PAPOO_WEB_PFAD . '/plugins/galerie/images/loading.gif";
									var fileBottomNavCloseImage = "' . PAPOO_WEB_PFAD . '/plugins/galerie/images/close.gif";
								</script>';
			//n�tige JS Datein in den Kopf laden
			$headerdat[] = '<script type="text/javascript" src="' . PAPOO_WEB_PFAD . '/js/lightbox.js"></script>';
			$headerdat[] = '<script type="text/javascript" src="' . PAPOO_WEB_PFAD . '/js/lightbox_localisation.js"></script>';
			$this->content->template['plugin_header'] = $headerdat;*/
		}
	}

	/**
	 *    Detail ID rausfischen oder 1 als Standard zur�ckgeben
	 */
	function get_detail_id()
	{
		// Welche Detailansicht soll verwendet werden?
		if ($this->dzvhae_system_id == true) {
			// Name f�r das Feld rausholen
			$sql = sprintf("SELECT mvcform_name FROM %s
		WHERE mvcform_id = '%d'
		AND mvcform_meta_id = '%d'
		AND mvcform_form_id = '%d'
		LIMIT 1",
				$this->cms->tbname['papoo_mvcform'],
				$this->db->escape($this->detail_feld_id),
				$this->db->escape($this->meta_gruppe),
				$this->db->escape($this->checked->mv_id)
			);
			$detail_feld_name = $this->db->get_var($sql);
			// Detail ID f�r diesen Beitrag rausholen
			if (!empty($detail_feld_name)) {
				$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
				$sql = sprintf("SELECT %s FROM %s
		    WHERE mv_content_id = '%d'
		    LIMIT 1",
					$this->db->escape($detail_feld_name
						. "_"
						. $this->detail_feld_id),

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_search_"
					. $lang,

					$this->db->escape($this->checked->mv_content_id)
				);
				$detail_id = $this->db->get_var($sql);
			}
			// wenn da was schiefgelaufen ist, dann Detail Id = 1
			if (empty($detail_id)
				|| !is_numeric($detail_id)
				|| $detail_id < 1
				|| $detail_id > 3) $detail_id = 1;
		} else $detail_id = 1;
		return $detail_id;
	}

	/**
	 *    Mitgliedsdaten im Frontend anzeigen
	 */
	function show_front()
	{
		if ($this->checked->download) require(PAPOO_ABS_PFAD . '/plugins/mv/lib/mv_dl_protected.php');
		else require(PAPOO_ABS_PFAD . '/plugins/mv/lib/show_front.php');
	}

	/**
	 * F�r Standard Verwaltungen alle Daten ausgeben
	 */
	function show_all_front()
	{
		// Welche Felder sollen �berhaupt durchsucht werden?
		$sql = sprintf("SELECT mvcform_id, mvcform_name, mvcform_type FROM %s
	    WHERE mvcform_form_id = '%d'
	    AND mvcform_meta_id = '%d'
	    AND mvcform_aktiv = '1'
	    AND mvcform_normaler_user = '1'
	    AND mvcform_list_front = '1'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->mv_id),
			$this->db->escape($this->meta_gruppe)
		);
		$such_felder = $this->db->get_results($sql, ARRAY_A);
		$such_felder_sql = "";
		// baut aus den sql Treffern die Feldernamen f�r sql Statement zusammen
		if (!empty($such_felder)) {
			$such_felder_sql = "mv_content_id, ";
			foreach ($such_felder as $such_feld) {
				$such_felder_sql .= $this->db->escape($such_feld['mvcform_name']
						. "_"
						. $such_feld['mvcform_id'])
					. ", ";
				$this->felder_typen[$such_feld['mvcform_name'] . "_" . $such_feld['mvcform_id']] = $such_feld['mvcform_type'];
			}
			$such_felder_sql = substr($such_felder_sql, 0, -2); // letztes Komma wieder weg
		}
		$sql = sprintf("SELECT COUNT(mv_content_id)
	    FROM %s
	    WHERE mv_content_sperre <> '1' AND mv_content_teaser = 1",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_search_"
			. $this->db->escape($this->cms->lang_id)
		);
		$anzahl = $this->db->get_var($sql);
		$this->content->template['anzahl'] = $anzahl;
		$this->weiter->make_limit($this->intervall_front);
		// Anzahl der Ergebnisse aus der Suche Eigenschaft �bergeben
		$this->weiter->result_anzahl = $anzahl;
		$gets = $this->get_get_vars();
		$mod_rewrite_org = $this->cms->mod_rewrite;
		$this->cms->mod_rewrite = 0;
		$this->weiter->weiter_link = "plugin.php?" . $gets;
		// wenn es sie gibt, weitere Seiten anzeigen
		$what = "teaser";
		$this->weiter->modlink = "no";
		$this->weiter->do_weiter($what);
		$sql = sprintf("SELECT * FROM %s
	    WHERE mv_content_sperre <> '1' AND mv_content_teaser = 1 %s",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_search_"
			. $this->db->escape($this->cms->lang_id),

			$this->db->escape($this->weiter->sqllimit)
		);
		$result = $this->db->get_results($sql);
		// Trefferanzahl ans Template
		$this->content->template['such_treffer_anzahl'] = $anzahl;
		$this->content->template['mv_uebersicht_liste'] = $this->object_to_array($result);
		if (!empty($result)) {
			$hit = "";
			$back_or_front = "mvcform_list_front";
			require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/print_treffer_liste_front.php');
			#$this->print_treffer_liste_front($result, "", "mvcform_list_front");
			$this->content->template['buffer_liste'] .= $this->content->template['mv_liste'];
		} else $this->content->template['message'] = "no_content"; // Ausgeben das keine Treffer gab
	}

	/**
	 * F�r Standard Verwaltungen alle Daten ausgeben
	 */
	function show_own_front()
	{
		// Welche Felder sollen �berhaupt durchsucht werden?
		$sql = sprintf("SELECT mvcform_id,
	    mvcform_name,
	    mvcform_type
	    FROM %s
	    WHERE mvcform_form_id = '%d'
	    AND mvcform_meta_id = '%d'
	    AND mvcform_aktiv = '1'
	    AND mvcform_normaler_user = '1'
	    AND mvcform_list_front = '1'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->mv_id),
			$this->db->escape($this->meta_gruppe)
		);
		$such_felder = $this->db->get_results($sql, ARRAY_A);
		#$such_felder_sql = "";
		// baut aus den Treffern die Feldernamen f�r sql Statement zusammen
		if (!empty($such_felder)) {
			$such_felder_sql = "mv_content_id, ";
			foreach ($such_felder as $such_feld) {
				$this->felder_typen[$such_feld['mvcform_name']
				. "_" .
				$such_feld['mvcform_id']] = $such_feld['mvcform_type'];
			}
		}
		$anzahl_von = 0;
		$anzahl_bis = $anzahl_von + $intervall;
		if (empty($this->checked->mv_id)) $this->checked->mv_id = "1";

		if (empty($this->checked->mv_limit)) $this->checked->mv_limit = $anzahl_von . ', ' . $anzahl_bis;
		$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		$sql = sprintf("SELECT COUNT(mv_content_id)
	    FROM %s
	    WHERE mv_content_userid = '%d'
	    AND mv_content_teaser = 1
	    AND mv_content_sperre <> '1'",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_search_"
			. $lang,

			$this->db->escape($this->user->userid)
		);
		//Sicherheit damit nicht als jeder das aufgerufen werden kann
		if ($this->user->userid != 11) $anzahl = $this->db->get_var($sql);
		if ($anzahl > 0) {
			// holt das Template f�rs Frontend aus der Datenbank
			$this->content->template['mv_template_all'] = "nobr:";
			$sql = sprintf("SELECT template_content_all
		FROM %s
		WHERE lang_id = '%d'
		AND meta_id = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_template_"
				. $this->db->escape($this->checked->mv_id),

				$this->db->escape($this->cms->lang_id),
				$this->db->escape($this->meta_gruppe)
			);
			$this->content->template['mv_template_all'] .= $this->db->get_var($sql);
			$this->content->template['anzahl'] = $anzahl;
			$this->weiter->make_limit($this->intervall_front);
			// Anzahl der Ergebnisse aus der Suche Eigenschaft �bergeben
			$this->weiter->result_anzahl = $anzahl;
			$this->weiter->weiter_link = "plugin.php"
				. "?menuid="
				. $this->checked->menuid
				. "&mv_id="
				. $this->checked->mv_id
				. "&template="
				. $this->checked->template;
			// wenn es sie gibt, weitere Seiten anzeigen
			$what = "teaser";
			$this->weiter->modlink = "no";
			$this->weiter->do_weiter($what);
			$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
			$sql = sprintf("SELECT * FROM %s
		WHERE mv_content_userid = '%d'
		AND mv_content_teaser = 1
		AND mv_content_sperre <> '1' %s",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_search_"
				. $lang,

				$this->db->escape($this->user->userid),
				$this->db->escape($this->weiter->sqllimit)
			);
			$result = $this->db->get_results($sql);
			$this->content->template['mv_mv_id'] = $this->checked->mv_id;
			$this->content->template['mv_uebersicht_liste'] = $this->db->get_results($sql, ARRAY_A);
			if (!empty($result)) {
				$hit = "";
				$back_or_front = "mvcform_list_front";
				require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/print_treffer_liste_front.php');
				#$this->print_treffer_liste_front($result, "", "mvcform_list_front");
			}
			$this->content->template['mv_liste'] = $result;
		}
	}

	/**
	 * Die Mitgliederliste ausgeben (BE)
	 */
	function show_user_list()
	{
		$this->checked->page = $this->checked->page ? $this->checked->page : 1;
		$_SESSION['plugin_mv']['page'] = $this->checked->page;
		$this->content->template['fertig'] = $this->checked->fertig; // MSG Satz wurde gel�scht/gespeichert
		// Check, ob es S�tze gibt
		$sql = sprintf("SELECT COUNT(mv_content_id) FROM %s",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_search_"
			. $this->cms->lang_back_content_id
		);
		$result = $this->db->get_var($sql);
		if (!empty($result)) {
			$hit = "";
			$back_or_front = "mvcform_list";
			require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/print_treffer_table.php');
			#$this->print_treffer_table($result, "", "mvcform_list");
		} else $this->content->template['message'] = "no_content";
	}

	/**
	 * @param int $flexId
	 * @return int
	 */
	public static function getFlexType(int $flexId): int
	{
		/** @var cms $cms */
		global $cms;
		/** @var ezSQL_mysqli $db */
		global $db;

		return (int)$db->get_var("SELECT mv_art FROM {$cms->tbname['papoo_mv']} WHERE mv_id = {$flexId} LIMIT 1");
	}

	// added by khmweb 12.11.09/09.02.11 mehrere S�tze aktivieren

	/**
	 * Datensätze aktivieren, anteasern etc. via Checkboxen in Übersichtsliste
	 */
	function multiple_activation_of_records(): void
	{
		$flexId = (int)$this->checked->mv_id;
		$languageId = (int)$this->cms->lang_back_content_id;

		$mvContentUsers = is_array($this->checked->mv_content_userid) ? $this->checked->mv_content_userid : [];
		$mvContentIds = array_keys($mvContentUsers);

		/** @var cms $cms */
		global $cms;
		/** @var ezSQL_mysqli $db */
		global $db;

		// Ermittle aktive Einträge
		$enableItems = is_array($this->checked->mv_content_active)
			? array_map(function ($id) { return (int)$id; }, $this->checked->mv_content_active)
			: [];

		// Einträge aktivieren
		if (count($enableItems) > 0) {
			$db->query(
				"UPDATE {$cms->tbname["papoo_mv_content_{$flexId}_search_{$languageId}"]} ".
				"SET mv_content_sperre = 0 ".
				"WHERE mv_content_id IN (".implode(', ', $enableItems).")"
			);
		}
		// Einträge deaktivieren
		if (count($enableItems) < count($mvContentIds)) {
			$db->query(
				"UPDATE {$cms->tbname["papoo_mv_content_{$flexId}_search_{$languageId}"]} ".
				"SET mv_content_sperre = 1 ".
				"WHERE mv_content_id IN (".implode(', ', array_diff($mvContentIds, $enableItems)).")"
			);
		}

		// Benutzer sperren oder freischalten, wenn es sich um eine Mitgliedsverwaltung handelt
		if (self::getFlexType($flexId) == self::FLEX_TYPE_USER) {
			// Ermittle aktive Benutzer
			$enableUsers = array_values(array_unique(array_map(function ($id) { return (int)$id; },
				array_filter($mvContentUsers, function ($mvContentId) use ($enableItems) {
					return in_array($mvContentId, $enableItems);
				}, ARRAY_FILTER_USE_KEY)
			)));
			$allUserIds = array_values(array_unique(array_map(function ($id) { return (int)$id; }, $mvContentUsers)));

			// Benutzer aktivieren
			if (count($enableUsers) > 0) {
				$db->query(
					"UPDATE {$cms->tbname['papoo_user']} ".
					"SET `active` = 1 ".
					"WHERE userid IN (".implode(', ', $enableUsers).")"
				);
			}
			// Benutzer deaktivieren
			if (count($enableUsers) < count($allUserIds)) {
				$db->query(
					"UPDATE {$cms->tbname['papoo_user']} ".
					"SET `active` = 0 ".
					"WHERE userid IN (".implode(', ', array_diff($allUserIds, $enableUsers)).")"
				);
			}
		}

		// Ermittle, welche Einträge angeteasert werden
		$teaserItems = is_array($this->checked->mv_content_teaser)
			? array_map(function ($id) { return (int)$id; }, $this->checked->mv_content_teaser)
			: [];


		// Einträge anteasern
		if (count($teaserItems) > 0) {
			$db->query(
				"UPDATE {$cms->tbname["papoo_mv_content_{$flexId}_search_{$languageId}"]} ".
				"SET mv_content_teaser = 1 ".
				"WHERE mv_content_id IN (".implode(', ', $teaserItems).")"
			);
		}
		// Einträge nicht anteasern
		if (count($teaserItems) < count($mvContentIds)) {
			$db->query(
				"UPDATE {$cms->tbname["papoo_mv_content_{$flexId}_search_{$languageId}"]} ".
				"SET mv_content_teaser = 0 ".
				"WHERE mv_content_id IN (".implode(', ', array_diff($mvContentIds, $teaserItems)).")"
			);
		}
	}

	/**
	 * Aktivierungsmail an User bei MV und �nderung des Aktivierungsstatus
	 */
	function send_activation_mail($userid)
	{
		$sql = sprintf("SELECT active,
	    email FROM %s
	    WHERE userid = '%s'",
			$this->cms->tbname['papoo_user'],
			$this->db->escape($userid)
		);
		$user_status = $this->db->get_results($sql, ARRAY_A);
		if (is_array($user_status)
			and $user_status[0]['active'] == 0) {
			$this->mail_it->to = $user_status[0]['email'];
			$this->mail_it->from = $this->cms->admin_email;
			$this->mail_it->subject = $this->content->template['plugin']['mv']['user_active2'] . " " . $this->cms->title;
			$this->mail_it->body = $this->content->template['plugin']['mv']['activation_message'];
			$this->mail_it->priority = 5;
			$this->mail_it->do_mail();
		}
	}

	/**
	 * mv::do_main_meta_wechseln_alle_gesuchten()
	 * F�hrt die �nderungen der Main MetaEbene durch
	 * @return void
	 */

	function do_weitere_meta_wechseln_alle_gesuchten($id_array = array())
	{
		if (!empty($id_array)
			&& is_numeric($this->checked->main_weitere_new)) {
			//Z�hler der IDS
			$i = 0;
			//Z�hler des Ergebnis Arrays
			$j = 0;
			//Maximale Anzahl
			$max = 100;
			$sql_felder_mv_content = "";
			$sql_felder_mv_content_del = "";
			foreach ($id_array as $id) {
				$id = $id['mv_content_id'];
				//Felder f�r die mv_content und mv_content_seach
				if ($this->checked->submitdelecht) {
					$sql_felder_mv_content .= " OR (mv_meta_lp_user_id='"
						. $id
						. "' AND mv_meta_lp_mv_id="
						. $this->db->escape($this->checked->mv_id)
						. " AND mv_meta_lp_meta_id='"
						. $this->checked->main_weitere_new
						. "') ";
				}
				if ($this->checked->action_main_weitere_new == "update") {
					$sql_felder_mv_content .= "  ('"
						. $id
						. "', '"
						. $this->db->escape($this->checked->mv_id)
						. "' , '"
						. $this->checked->main_weitere_new
						. "'),  ";
					$sql_felder_mv_content_del .= " OR (mv_meta_lp_user_id='"
						. $id . "' AND mv_meta_lp_mv_id="
						. $this->db->escape($this->checked->mv_id)
						. " AND mv_meta_lp_meta_id='"
						. $this->checked->main_weitere_new
						. "') ";
				}
				$i++;
				if ($i >= $max) {
					$i = 0;
					$sql_felder_content_array[$j] = $sql_felder_mv_content;
					$sql_felder_content_array_del[$j] = $sql_felder_mv_content_del;
					$sql_felder_mv_content_del = $sql_felder_mv_content = "";
					$j++;
				}
			}
			$sql_felder_content_array[$j] = $sql_felder_mv_content;
			$sql_felder_content_array_del[$j] = $sql_felder_mv_content_del;
			//Durchgehen und umstellen
			if ($this->checked->submitdelecht) {
				foreach ($sql_felder_content_array as $feldsql) {
					$sql = sprintf("DELETE FROM %s
			WHERE mv_meta_lp_user_id='xyz' %s",
						$this->cms->tbname['papoo_mv_meta_lp'],
						$feldsql
					);
					$this->db->query($sql);
				}
			}
			if ($this->checked->action_main_weitere_new == "update") {
				//Alte Eintr�ge dazu l�schen
				foreach ($sql_felder_content_array_del as $feldsql) {
					$sql = sprintf("DELETE FROM %s
			WHERE mv_meta_lp_user_id='xyz' %s",
						$this->cms->tbname['papoo_mv_meta_lp'],
						$feldsql
					);
					$this->db->query($sql);
				}
				//Alle weiteren Ebenen hinzuf�gen
				foreach ($sql_felder_content_array as $feldsql) {
					$feldsql = substr($feldsql, 0, -3);
					$sql = sprintf("INSERT INTO %s (
			mv_meta_lp_user_id,
			mv_meta_lp_mv_id,
			mv_meta_lp_meta_id )
			VALUES  %s",
						$this->cms->tbname['papoo_mv_meta_lp'],
						$feldsql
					);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * mv::do_main_meta_wechseln_alle_gesuchten()
	 * F�hrt die �nderungen der Main MetaEbene durch
	 * @return void
	 */

	function do_main_meta_wechseln_alle_gesuchten($id_array = array())
	{
		if (!empty($id_array) && is_numeric($this->checked->main_meta_new)) {
			//Z�hler der IDS
			$i = 0;
			//Z�hler des Ergebnis Arrays
			$j = 0;
			//Maximale Anzahl
			$max = 100;
			$sql_felder_mv_content = "";

			//Schleife zur Erzeugung der UPDATE Statements f�r inactive in 200er SChritten
			foreach ($id_array as $id) {
				$id = $id['mv_content_id'];
				//Felder f�r die mv_content und mv_content_seach
				$sql_felder_mv_content .= " OR mv_meta_main_lp_user_id='" . $id . "' AND mv_meta_main_lp_mv_id="
					. $this->db->escape($this->checked->mv_id);

				$i++;
				if ($i >= $max) {
					$i = 0;
					$sql_felder_content_array[$j] = $sql_felder_mv_content;
					$sql_felder_mv_content = "";
					$j++;
				}
			}
			$sql_felder_content_array[$j] = $sql_felder_mv_content;
			//Durchgehen und umstellen
			foreach ($sql_felder_content_array as $feldsql) {
				$sql = sprintf("UPDATE %s SET mv_meta_main_lp_meta_id = '%d'
		    WHERE mv_meta_main_lp_user_id = 'xy'
		    %s",
					$this->cms->tbname['papoo_mv_meta_main_lp'],
					$this->db->escape($this->checked->main_meta_new),
					$feldsql
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * mv::do_dzvhae_kuendigen_alle_gesuchten()
	 * F�hrt die K�ndigung speziell f�r dzvhae durch
	 * @return void
	 */
	function do_dzvhae_kuendigen_alle_gesuchten()
	{
		if ($this->check_special_right()) {
			$this->content->template['echt_kuendigen'] = "frage";
			$this->content->template['mv_content_id'] = $this->checked->mv_content_id;
		}
	}

	function check_special_right()
	{
		if ($this->checked->template == "mv/templates/search_user.html") $mode = 2; // Suche
		elseif ($this->checked->template == "mv/templates/change_user.html") $mode = 1; // Edit
		$found = false;
		if (!($mode == 1 or $mode == 2)) return $found;
		$sql = sprintf("SELECT `group`
	    FROM %s",

			$this->cms->tbname['papoo_mv']
			. "_special_rights"
			. $mode
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		//Wenn Admin Rechte bestehen dann alle rausholen
		if (!empty($result)) {
			if ($this->have_admin_rights()) {
				$sql = sprintf("SELECT gruppeid FROM %s",
					$this->cms->tbname['papoo_gruppe']);
				$rechtegruppen_ok = $this->db->get_results($sql, ARRAY_A);
			} else {
				//Die Gruppen rausholen an denen der User der gerade bedient die Rechte hat
				$sql = sprintf("SELECT gruppeid
		    FROM %s, %s
		    WHERE userid = '%d'
		    AND gruppeid=gruppenid",
					$this->cms->tbname['papoo_gruppe'],
					$this->cms->tbname['papoo_lookup_ug'],
					$this->user->userid
				);
				$rechtegruppen_ok = $this->db->get_results($sql, ARRAY_A);
			}
			if (!empty($rechtegruppen_ok)) {
				foreach ($result as $key => $value) {
					if ($this->in_arrayi($value['group'], $rechtegruppen_ok)) {
						$found = true; // Recht ist vorhanden
						$this->content->template['special_right'] = true;
						break;
					}
				}
			}
		}
		return $found;
	}

	function in_arrayi($needle, $haystack)
	{
		$found = false;
		foreach ($haystack as $value) {
			if (strtolower($value['gruppeid']) == strtolower($needle)) $found = true;
		}
		return $found;
	}

	/**
	 * mv::do_dzvhae_kuendigen_alle_gesuchten()
	 * F�hrt die K�ndigung speziell f�r dzvhae durch
	 * @return void
	 */
	function do_dzvhae_kuendigen_alle_gesuchten_wirklich()
	{
		//Wenn auch welche vorhanden sind
		if (!empty($this->checked->mv_content_id)
			and $this->check_special_right()) {
			$aktiv = 0;  //0 = Sperre, 1= Aktiv
			$sperre = 1; //1=Sperre, = = Aktiv
			//Z�hler der IDS
			$i = 0;
			//Z�hler des Ergebnis Arrays
			$j = 0;
			//Maximale Anzahl
			$max = 200;
			$sql_felder_mv_content = "";
			$sql_felder_mv_lang = "";
			//Schleife zur Erzeugung der UPDATE Statements f�r inactive in 200er Schritten
			foreach ($this->checked->mv_content_id as $id) {
				$id = $id['mv_content_id'];
				//Felder f�r die mv_content und mv_content_seach
				$sql_felder_mv_content .= " mv_content_id='" . $id . "' OR ";
				$sql_felder_mv_lang .= " OR lookup_id='" . $id . "'";
				$i++;
				if ($i >= $max) {
					$i = 0;
					$sql_felder_content_array[$j] = $sql_felder_mv_content;
					#$sql_felder_content_array[$j] = $id;
					$sql_felder_lang_array[$j] = $sql_felder_mv_lang;
					$sql_felder_mv_lang = "";
					$sql_felder_mv_content = "";
					$j++;
				}
				//Userid rausholen
				$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
				$sql = sprintf("SELECT mv_content_userid
		    FROM %s
		    WHERE mv_content_id = '%d'",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_search_"
					. $lang,

					$id
				);
				$userid = $this->db->get_var($sql);
				//Rechte l�schen
				$sql = sprintf("DELETE FROM %s
		    WHERE userid = '%d'",
					$this->cms->tbname['papoo_lookup_ug'],
					$userid
				);
				$this->db->query($sql);
				//Rechte neu setzen
				$sql = sprintf("INSERT INTO %s SET userid = '%d',
		    gruppenid = '10'",
					$this->cms->tbname['papoo_lookup_ug'],
					$userid
				);
				$this->db->query($sql);
				$sql = sprintf("INSERT INTO %s SET userid = '%d',
		    gruppenid = '19'",
					$this->cms->tbname['papoo_lookup_ug'],
					$userid
				);
				$this->db->query($sql);
				// Metaebenen neu setzen
				// 1. Hauptmetaebene auf �rzte
				$sql = sprintf("UPDATE %s
		    SET mv_meta_main_lp_meta_id = 2
		    WHERE mv_meta_main_lp_user_id = '%d'
		    AND mv_meta_main_lp_mv_id = '%d'",
					$this->cms->tbname['papoo_mv_meta_main_lp'],
					$this->db->escape($id),
					$this->db->escape($this->checked->mv_id)
				);
				$this->db->query($sql);
				// L�schen aller sonstigen Metaebenen f�r diese mv_content_id
				$sql = sprintf("DELETE FROM %s
		    WHERE mv_meta_lp_user_id = '%d'
		    AND mv_meta_lp_mv_id = '%d'",
					$this->cms->tbname['papoo_mv_meta_lp'],
					$this->db->escape($id),
					$this->db->escape($this->checked->mv_id)
				);
				$this->db->query($sql);
				// sonstige Metaebenen auf �rzte und standard setzen f�r diese mv_content_id
				$sql = sprintf("INSERT INTO %s
		    SET mv_meta_lp_user_id = '%d',
		    mv_meta_lp_meta_id = 1,
		    mv_meta_lp_mv_id = '%d'",
					$this->cms->tbname['papoo_mv_meta_lp'],
					$this->db->escape($id),
					$this->db->escape($this->checked->mv_id)
				);
				$this->db->query($sql);
				$sql = sprintf("INSERT INTO %s
		    SET mv_meta_lp_user_id = '%d',
		    mv_meta_lp_meta_id = 2,
		    mv_meta_lp_mv_id = '%d'",
					$this->cms->tbname['papoo_mv_meta_lp'],
					$this->db->escape($id),
					$this->db->escape($this->checked->mv_id)
				);
				$this->db->query($sql);
				// Protokoll
				$protokoll = array();
				$sql = sprintf("INSERT INTO %s SET mv_pro_date = NOW(),
		    mv_pro_mv_content_id = '%s',
		    mv_pro_group_id = '%s',
		    mv_pro_feld_id = '%s',
		    mv_pro_old_content = '%s',
		    mv_pro_login_id = '%s',
		    mv_pro_ip = '%s',
		    mv_pro_mv_id = '%d'",
					$this->cms->tbname['papoo_mv_protokoll'],
					$this->db->escape($id),
					'',
					$this->db->escape(62),
					$this->db->escape("K&uuml;ndigung"),
					$this->db->escape($this->user->userid),
					$this->db->escape($_SERVER['REMOTE_ADDR']),
					$this->db->escape($this->checked->mv_id)
				);
				$this->db->query($sql);
			}
			$sql_felder_content_array[$j] = $sql_felder_mv_content;
			$sql_felder_lang_array[$j] = $sql_felder_mv_lang;
			//Das Aktiv H�kchen rausholen
			$sql = sprintf("SELECT mvcform_id FROM %s
		WHERE mvcform_form_id = '%d'
		AND mvcform_name = 'active'",
				$this->cms->tbname['papoo_mvcform'],
				$this->db->escape($this->checked->mv_id)
			);
			$active_mv_cform_id = $this->db->get_var($sql);
			//Sprachids alle rausholen
			$sql = sprintf("SELECT mv_lang_id FROM %s",
				$this->cms->tbname['papoo_mv_name_language']
			);
			$lang_ids = $this->db->get_results($sql, ARRAY_A);
			$sql = sprintf("SELECT mvcform_id FROM %s
		WHERE mvcform_name LIKE '%s'
		LIMIT 1",
				$this->cms->tbname['papoo_mvcform'],
				"istmitglied"
			);
			$istmitglied_id = $this->db->get_var($sql);
			//Dann jetzt durchgehen und die Statements ausf�hren
			//Aktiv H�kchen entfernen
			foreach ($sql_felder_content_array as $feldsql) {
				$feldsql = substr($feldsql, 0, -4);
				//Die Searchtabellen
				foreach ($lang_ids as $lids) {
					$sql = sprintf("UPDATE %s SET %s = '2',
			%s = '0',
			%s = '0',
			%s = '0',
			%s = '0',
			%s = '0',
			%s = '0',
			%s = '0',
			%s = '0',
			%s = '2',
			%s = '2'
			WHERE %s",
						$this->dzvhae_feld_flex_istmitglied,
						$this->dzvhae_feld_flex_landesverband,
						$this->dzvhae_feld_flex_zahlungen,
						$this->dzvhae_feld_flex_mitgliedfeld1,
						$this->dzvhae_feld_flex_mitgliedfeld2,
						$this->dzvhae_feld_flex_mitgliedfeld3,
						$this->dzvhae_feld_flex_mitgliedfeld4,
						$this->dzvhae_feld_flex_mitgliedfeld5,
						$this->dzvhae_feld_flex_mitgliedfeld6,
						$this->dzvhae_feld_flex_zahlverfahren,
						$this->dzvhae_feld_flex_sichtbar,

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_search_"
						. $lids['mv_lang_id'],

						$istmitglied_id,
						$feldsql
					);
					$this->db->query($sql);
				}
			}
			$this->content->template['echt_kuendigen_passiert'] = "ok";
		}
	}

	function update_all_content_tables($field = "", $fldvalue = "", $id, $sprachen)
	{
		foreach ($sprachen as $key => $value) {
			$sql = sprintf("UPDATE %s SET %s = '%s'
		WHERE mv_content_id = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_search_"
				. $value['mv_lang_id'],

				$field,
				$fldvalue,
				$id
			);
			$this->db->query($sql);
		}
	}

	/**
	 * Alle Get Variablen zuweisne
	 */
	function get_get_vars()
	{
		if ($this->cms->mod_free == 1 || $this->cms->mod_surls == 1) {
			/*
	    // Hinweis: Dürfte bei der Suche nicht klappen, was ist mit den Suchfeldern?
	    // Alternativen: Alle mv-Parameter zwecks Identifizierung vereinheitlichen, z. B. beginen mit "mv_". Nachteil: Hoher Änderungsaufwand.
	    // Oder ein array erstellen, das die Parameter enthält, die nict zur Flex gehören und diese dann in der unten deaktivierten Routine
	    // überspringen. Nachteil: Neue Papoo-Parameter müssen eingepflegt werden.
	    // Bsp. eines solchen arrays:
	    #$skip_keys = array("var1","var2","var3","template2","stylepost","msgid","formmenuid","forumid_tmpl","formforumid","rootid","formmsgid","reporeid","mv/templates/mv_search_front_onemv.html","forumid","spamcode");
	    $url="menuid=".$this->checked->menuid;
	    $url.="&template=".$this->checked->template;
	    $url.="&mv_id=".$this->checked->mv_id;
	    $url.="&onemv=".$this->checked->onemv;
	    $url.="&search_mv=".$this->checked->search_mv;

	    $url.="&sort_feld=".$this->checked->sort_feld;
	    #  $url.="menuid=".$this->checked->menuid;
	    return $url;
		*/


			$parameters = array_filter([
				"menuid" => (int)$this->checked->menuid,
				"template" => (string)$this->checked->template,
				"mv_id" => (int)$this->checked->mv_id,
				'mv_submit' => (string)$this->checked->mv_submit,
				"onemv" => (string)$this->checked->onemv,
				"search_mv" => (string)$this->checked->search_mv,
				"sort_feld" => (string)$this->checked->sort_feld
			], function ($value, $name) {
				return strlen($value) > 0 || !in_array($name, [
					'mv_submit',
				]);
			}, ARRAY_FILTER_USE_BOTH);

			// $this->cms->template_lang wird nach einem Seitenwechsel ueber die Pagination ploetzlich nicht mehr definiert...
			$menuId = (int)$this->checked->menuid;
			$langId = (int)$this->cms->lang_id;
			$menuLink = $this->db->get_var("SELECT menulinklang FROM {$this->cms->tbname["papoo_menu_language"]} WHERE menuid_id = $menuId AND lang_id = $langId");

			if (strpos($menuLink, "plugin:") === 0) {
				$menuLinkParameters = explode("&", trim(str_replace("plugin:", "template=", $menuLink)));
				$parameters = array_reduce($menuLinkParameters, function ($parameters, $param) {
					$param = explode("=", $param, 2);
					$parameters[$param[0]] = count($param) == 2 ? $param[1] : "";
					return $parameters;
				}, $parameters);
			}

			$flexId = $parameters['mv_id'] = (int)$parameters['mv_id'];

			// Append the user input of a flex search to the parameters
			if ($flexId) {
				$searchColumn = defined('admin') ? 'mvcform_search_back' : 'mvcform_search';
				$searchableFlexFields = array_map(function (array $row) {
					return "{$row['mvcform_name']}_{$row['mvcform_id']}";
				}, $this->db->get_results(
					"SELECT mvcform_id, mvcform_name ".
					"FROM {$this->cms->tbname['papoo_mvcform']} ".
					"WHERE mvcform_form_id = {$flexId} AND mvcform_aktiv = 1 ".
					"AND {$searchColumn} = 1", ARRAY_A)
				);

				foreach ($searchableFlexFields as $fieldName) {
					if (strlen($value = $this->checked->$fieldName ?? '') > 0) {
						$parameters[$fieldName] = $value;
					}
				}
			}

			return urldecode(http_build_query($parameters, null, "&"));
		}

		$drinurl = "";
		foreach ($_GET as $key => $value) {
			foreach ($this->checked as $key2 => $value2) {
				if ($key == $key2) {
					if ($key2 == "page") continue;
					if ($key2 == "submitlang") continue;
					if ($key2 == "search_mv") $value2 = urlencode($value2);
					$inurl[$key2] = $value2;
				}
			}
		}
		$counturl = count($inurl);
		if (is_array($inurl)) {
			foreach ($inurl as $key => $value) {
				$drinurl .= $key . "=" . $value;
				$k++;
				if ($k < $counturl) $drinurl .= "&";
			}
		}
		return $drinurl;
	}

	/**
	 * Welcher Felder m�ssen denn beachtet werden, sprich haben ein H�kchen bei
	 * Kalender
	 **/
	function get_kalender_felder()
	{
		// holt alle Felder IDs f�r Felder die in der gew�nschten Metaebene ein H�kchen bei Kalender haben
		$sql = sprintf("SELECT mvcform_id FROM %s
	    WHERE mvcform_meta_id='%d'
	    AND mvcform_kalender='1'
	    AND mvcform_form_id='%d'",
			$this->cms->tbname['papoo_mvcform'], $this->db->escape($this->meta_gruppe),
			$this->db->escape($this->checked->mv_id));
		$this->kalender_felder = $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Welcher Felder m�ssen denn beachtet werden, sprich haben ein H�kchen bei
	 * Kalender Hier nicht die mv_id sondern die mv_id der jeweiligen Kalender
	 * Module mod_mv_id !!!!
	 **/
	function get_kalender_felder_mod()
	{
		// holt alle Felder IDs f�r Felder die in der gew�nschten Metaebene ein H�kchen bei Kalender haben
		$sql = sprintf("SELECT mvcform_id FROM %s
	    WHERE mvcform_meta_id = '%d'
	    AND mvcform_kalender = '1'
	    AND mvcform_form_id = '%d'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->meta_gruppe),
			$this->db->escape($this->checked->mod_mv_id)
		);
		$this->kalender_felder = $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Suchfunktion im Frontend f�r den Kalender
	 */
	function search_kalender_front($dertag)
	{
		$dertag_org = $dertag;
		$dertag = $this->unixtime_to_longtime($dertag);
		$treffer_all = array();
		//Mta ID festlegen
		$meta_id = $this->meta_gruppe;
		if (!defined("admin")
			and is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
		// schaut ob es f�r diesen Tag in den ausgegew�hlten Feldern einen Treffer im Content Bereich der Verwaltung gibt
		if (!empty($this->kalender_felder)) {
			foreach ($this->kalender_felder as $kalender_feld) {
				// holt den Feldnamen und Feldtyp aus der Datenbank
				$sql = sprintf("SELECT mvcform_name,
		    mvcform_type
		    FROM %s
		    WHERE mvcform_id = '%s'
		    AND mvcform_meta_id = '%d'",
					$this->cms->tbname['papoo_mvcform'],
					$this->db->escape($kalender_feld['mvcform_id']),
					$this->db->escape($meta_id)
				);
				$feld_daten = $this->db->get_results($sql, ARRAY_A);
				// manchmal....
				$subbi = $feld_daten[0]['mvcform_name'] . "_" . $kalender_feld['mvcform_id'];
				// Markierung f�r neue Feldtypen
				// wenns ein Datumsfeld ist
				if ($feld_daten[0]['mvcform_type'] == "timestamp") {
					// gibt es f�r dieses Feld und dem Datum einen Treffer
					$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
					$sql = sprintf("SELECT DISTINCT *
			FROM %s, %s, %s
			WHERE %s = '%s'
			AND mv_content_id = mv_meta_lp_user_id
			AND mv_meta_main_lp_user_id = mv_meta_lp_user_id
			AND mv_meta_lp_meta_id = '%d'
			AND mv_meta_lp_mv_id = '%d'
			OR %s = '%s'
			AND mv_content_id = mv_meta_main_lp_user_id
			AND mv_meta_main_lp_user_id = mv_meta_lp_user_id
			AND mv_meta_lp_meta_id = '%d'
			AND mv_meta_lp_mv_id = '%d'",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mod_mv_id)
						. "_search_"
						. $lang,

						$this->cms->tbname['papoo_mv_meta_lp'],

						$this->cms->tbname['papoo_mv_meta_main_lp'],

						$this->db->escape($subbi),
						$this->db->escape($dertag),
						$this->db->escape($this->meta_gruppe),
						$this->db->escape($this->checked->mod_mv_id),
						$this->db->escape($subbi),
						$this->db->escape($dertag),
						$this->db->escape($this->meta_gruppe),
						$this->db->escape($this->checked->mod_mv_id)
					);
					$treffer = $this->db->get_results($sql, ARRAY_A);
				} // beim Zeitintervall
				elseif ($feld_daten[0]['mvcform_type'] == "zeitintervall") {
					// erstmal alle Zeitintervalle f�r dieses Feld aus der Datenbank holen
					$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
					$sql = sprintf("SELECT %s,
			mv_content_id
			FROM %s, %s, %s
			WHERE %s LIKE '%s'
			AND mv_content_id = mv_meta_lp_user_id
			AND mv_meta_main_lp_user_id = mv_meta_lp_user_id
			AND mv_meta_lp_meta_id = '%d'
			AND mv_meta_lp_mv_id = '%d'
			OR %s LIKE '%s'
			AND mv_content_id = mv_meta_main_lp_user_id
			AND mv_meta_main_lp_user_id = mv_meta_lp_user_id
			AND mv_meta_lp_meta_id = '%d'
			AND mv_meta_lp_mv_id = '%d'
			",
						$this->db->escape($feld_daten[0]['mvcform_name']
							. "_" .
							$kalender_feld['mvcform_id']),

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mod_mv_id)
						. "_search_"
						. $lang,

						$this->cms->tbname['papoo_mv_meta_lp'],

						$this->cms->tbname['papoo_mv_meta_main_lp'],

						$this->db->escape($feld_daten[0]['mvcform_name']
							. "_" .
							$kalender_feld['mvcform_id']),

						"%,%", $this->db->escape($this->meta_gruppe),
						$this->db->escape($this->checked->mod_mv_id),

						$this->db->escape($feld_daten[0]['mvcform_name']
							. "_" .
							$kalender_feld['mvcform_id']),

						"%,%",
						$this->db->escape($this->meta_gruppe),
						$this->db->escape($this->checked->mod_mv_id)
					);
					$alle_feld_werte = $this->db->get_results($sql, ARRAY_A);
					if (!empty($alle_feld_werte)) {
						foreach ($alle_feld_werte as $feld_wert) {
							if (!empty($feld_wert[$subbi])) {
								//list($datum_anfang, $datum_ende) = explode(",", $feld_wert[$subbi]);
								//							if($datum_anfang <= $dertag && $datum_ende >= $dertag)
								//							{
								//								$treffer[] = $feld_wert;
								//							}
								$splitwert = $feld_wert[$subbi];
								list($datum_anfang, $datum_ende) = explode(",", $splitwert);
								$dasplit = explode(".", $datum_anfang);
								$dendsplit = explode(".", $datum_ende);
								if (!empty($dasplit['0'])) $datum_anfang_time = mktime(0, 0, 0, $dasplit['1'], $dasplit['2'], $dasplit['0']);
								if (!empty($dendsplit['0'])) $datum_ende_time = mktime(0, 0, 0, $dendsplit['1'], $dendsplit['2'], $dendsplit['0']);
								if ($datum_anfang_time <= $dertag_org
									&& $datum_ende_time >= $dertag_org) $treffer_ids[] = array($feld_wert['mv_content_id']);
							}
						}
					}
					$id_search = "";
					if (is_array($treffer_ids)) {
						foreach ($treffer_ids as $tref) {
							foreach ($tref as $ids) {
								$id_search .= " OR mv_content_id='" . $ids . "' ";
							}
						}
					}
					$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
					$sql = sprintf("SELECT * FROM %s
			WHERE mv_content_id = 'xyx' %s",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mod_mv_id)
						. "_search_"
						. $lang,

						$id_search
					);
					$treffer = $this->db->get_results($sql, ARRAY_A);
				}
				if (!empty($treffer)) $treffer_all = array_merge($treffer_all, $treffer);
			}
		}
		$result = $treffer_all;
		$hit = "";
		$back_or_front = "mvcform_list_front";
		require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/print_treffer_liste_front_kalender.php');
		#$this->print_treffer_liste_front_kalender($treffer_all, "", "mvcform_list_front");
	}

	//UNUSED!!!
	function insert_alle_verfall_daten()
	{
		$sql = sprintf("DELETE FROM %s ",
			$this->cms->tbname['papoo_mv_datum_verfallen']
		);
		$this->db->query($sql);
		$sql = sprintf("SELECT DISTINCT(mv_content_id)
	    FROM %s",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_search_"
			. $this->db->escape($this->cms->lang_id)
		); // die normalen Felder
		$anzahl = $this->db->get_results($sql, ARRAY_A);
		foreach ($anzahl as $an) {
			$sql = sprintf("INSERT INTO %s SET verfall_content_id = '%d',
		verfall_mv_id = '%d',
		verfall_stufe_id = '0'",
				$this->cms->tbname['papoo_mv_datum_verfallen'],
				$an['mv_content_id'],
				$this->checked->mv_id
			);
			$this->db->query($sql);
		}
	}

	/**
	 * mv::insert_einmal_verfall_daten()
	 * Das Verfallsdatum f�r einen Eintrag auf Anfang setzen
	 * @param integer $mv_content_id
	 * @return void
	 */
	function insert_einmal_verfall_daten($mv_content_id = 1, $stufe = 1)
	{
		$sql = sprintf("DELETE FROM %s
	    WHERE verfall_content_id = '%d'
	    AND verfall_mv_id = '%d'",
			$this->cms->tbname['papoo_mv_datum_verfallen'],
			$mv_content_id,
			$this->checked->mv_id
		);
		$this->db->query($sql);
		$sql = sprintf("INSERT INTO %s SET verfall_content_id = '%d',
	    verfall_mv_id ='%d',
	    verfall_stufe_id = '%d'",
			$this->cms->tbname['papoo_mv_datum_verfallen'],
			$mv_content_id,
			$this->checked->mv_id,
			$stufe
		);
		$this->db->query($sql);
	}

	/**
	 * mv::checke_alle_abgelaufenen_eintraege()
	 * Hier werden die abgelaufenen Eintr�ge durchgegangen
	 * Und alle die noch keine Aktion haben werden beschickt
	 * Die Abfolge der Tage entspricht immer auch der Stufe
	 * 1. Eintrag => Stufe 1
	 * 2. Eintrag => Stufe 2
	 *
	 * Stufe wird nur dann eingetragen wenn verschickt.
	 *
	 * @return void
	 */
	function checke_alle_abgelaufenen_eintraege($kalender_extra = "", $kalender_extra2 = "")
	{
		//Eintr�ge in die DB veranlassen
		#$this->insert_alle_verfall_daten();
		//HIER GEHTS WEITER
		//Nur durchf�hren, wenn auch freigeschaltet ist
		if ($this->mv_schick_mail_wenn_abgelaufen == true) {
			//so umstellen das immer das kalenderextra kommt
			if (!empty($kalender_extra)
				or !empty($kalender_extra2)) {
				if (empty($kalender_extra)
					&& !empty($kalender_extra2)) $kalender_extra = $kalender_extra2;
			}
			//AND erg�nzen
			if (!stristr($kalender_extra, "AND")) $kalender_extra .= " AND ";
			$stufe = 0;
			//F�r alle Tages Abst�nde durchgehen
			foreach ($this->mv_abstand_mail_schicken as $tage) {
				//Das Datum mit dem heutigen ersetzen
				if (empty($kalender_extra_org)) $kalender_extra_org = $kalender_extra;
				$morgen = mktime(0, 0, 0, date("m"), date("d") + $tage, date("Y"));
				$tag = date("d", $morgen);
				$searchfeld_anfang = $this->get_longtime(date("Y", $morgen), date("m", $morgen), $tag);
				$kalender_extra = str_ireplace("xxxx", $searchfeld_anfang, $kalender_extra_org);
				$kalender_extra = str_replace('>', '<', $kalender_extra);
				$kalender_extra_drueber = str_replace('<', '>', $kalender_extra);
				//Wenn wirklich nicht leer && eregi("\>",$kalender_extra)
				if (!empty($kalender_extra)) {
					$sql = sprintf("SELECT DISTINCT * FROM %s, %s
			WHERE %s mv_content_sperre <> '1'
			AND verfall_content_id = mv_content_id
			AND verfall_mv_id = '%d'
			AND verfall_stufe_id < '%d'",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_search_"
						. $this->db->escape($this->cms->lang_id),

						$this->cms->tbname['papoo_mv_datum_verfallen'],
						$kalender_extra,
						$this->db->escape($this->checked->mv_id),
						$stufe
					); // die normalen Felder
					$anzahl = $this->db->get_results($sql, ARRAY_A);
					if ($tage <= 0) {
						$kalender_extraxy = substr($kalender_extra, 0, -4);
						$sql = sprintf("UPDATE %s SET mv_content_sperre = '1'
			    WHERE %s",

							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id)
							. "_search_"
							. $this->db->escape($this->cms->lang_id),
							$kalender_extraxy
						);
						$this->db->query($sql);
						$sql = sprintf("UPDATE %s SET mv_content_sperre = '1'
			    WHERE %s",

							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id),

							$kalender_extraxy
						);
						#$this->db->query($sql);
					}
				}
				//Eintr�ge durchgehen
				if (is_array($anzahl)) {
					foreach ($anzahl as $dat) {
						//E-Mails raussuchen
						foreach ($dat as $key => $value) {
							if ($this->validateEmail($value)) $email = $value;
						}
						//Nachricht schicken
						if ($tage < 0) $this->verschick_nachricht_an_eintraeger_das_abgelaufen(1, $dat['mv_content_id'], $tage, $email, $dat);
						if ($tage == 0) $this->verschick_nachricht_an_eintraeger_das_abgelaufen(2, $dat['mv_content_id'], 0, $email, $dat);
						if ($tage > 0) $this->verschick_nachricht_an_eintraeger_das_abgelaufen(3, $dat['mv_content_id'], $tage, $email, $dat);
						//Stufe raufsetzen
						$this->insert_einmal_verfall_daten($dat['mv_content_id'], $stufe);
					}
				}
				//Die n�chste Stufe checken
				$stufe++;
			}
		}
	}

	/**
	 * Kunden-Mod f�r Hiltmann
	 * mv::verschick_nachricht_an_eintraeger_das_abgelaufen()
	 * Hier wird eine Nachricht verschickt an den, der eingetragen hat
	 * mit Link.
	 * $mod
	 * 1=Vergangenheit, ist l�nger abgelaufen
	 * 2=Gerade abgelaufen
	 * 3=L�uft demn�chst ab
	 * @return void
	 */
	function verschick_nachricht_an_eintraeger_das_abgelaufen($mod = 0, $mvc_id = 0, $tage = 0, $email = "",
															  $daten = array())
	{
		//Metaebene
		$mv_meta_id = $this->meta_gruppe;
		//Mail Inhalt,
		$nachricht_mod = "mail_text_nach" . $mod;
		$message_mail = $this->content->template['plugin']['mv'][$nachricht_mod];
		//Link zum Eintrag
		/**
		 * //Hier der Link zum Frontend Eintrag
		 *
		 * echo $link="\n Link zum Eintrag: \n http://".$this->cms->title_send.PAPOO_WEB_PFAD."/plugin.php?menuid=".$this->checked->menuid."&template=mv/templates/mv_edit_front.html&mv_id=".$this->checked->mv_id."&extern_meta=".$this->checked->extern_meta."&mv_content_id=".$mvc_id."";
		 */
		//Menuid f�r den internen Link rausholen
		$sql = sprintf("SELECT menuid
	    FROM %s
	    WHERE menulink LIKE '%s'",
			$this->cms->tbname['papoo_menuint'],
			"%mv_id="
			. $this->db->escape($this->checked->mv_id)
			. "%"
		);
		$menuid = $this->db->get_var($sql);
		$link = "\n Link zum Eintrag: \n http://"
			. $this->cms->title_send
			. PAPOO_WEB_PFAD
			. "/plugin.php?menuid=6&template=mv/templates/mv_edit_front.html&mv_id="
			. $this->checked->mv_id
			. "&extern_meta=1&mv_content_id="
			. $mvc_id
			. "&selected_meta_gruppe="
			. $mv_meta_id;
		$nachricht_mod = "plugin_anmeldung_email_betreff_ablauf" . $mod;
		if (!defined("admin")) // FE
		{
			$message_mail .= "\n\n";
			$message_mail = $this->ersetze_inhalte($message_mail, $daten, $link);
			//debugging
			if (!empty($email) && $email == "uhi@hiltmann.net") {
				$this->mail_it->to = $email;
				#$this->mail_it->cc[] = $this->cc1;
				#$this->mail_it->cc[] = $this->cc2;
				$this->mail_it->from = $this->cms->admin_email;
				#$this->mail_it->from_textx = $this->content->template['plugin']['mv']['anmeldung_email_emailadresse'];
				$this->mail_it->subject = $this->ersetze_inhalte($this->content->template[$nachricht_mod], $daten, $link);
				$this->mail_it->body = $message_mail;
				$this->mail_it->do_mail();
			}
		}
	}

	/**
	 * mv::ersetze_inhalte()
	 * Ersetze dynamisch Inhalte aus Eintr�gen im Text
	 * @return void
	 *
	 * called by update_lang_search_row.php,
	 *     verschick_nachricht_an_eintraeger_das_abgelaufen() in mv.php
	 */
	function ersetze_inhalte($message, $daten = array(), $link)
	{
		//Link einbinden
		$message = str_ireplace("#link#", $link, $message);
		//Reste ersetzen
		foreach ($daten as $key => $value) {
			$replace = "#" . $key . "#";
			$message = str_ireplace($replace, $value, $message);
		}
		return $message;
	}

	function get_sichtbar_feld_fuer_dzvhae()
	{
		$abfrage = "";
		if (!empty($this->dzvhae_feld_sichtbar)
			&& $this->dzvhae_system_id == true
			&& $this->checked->mv_id == 1) $abfrage = $this->dzvhae_feld_sichtbar . "='1'";
		return $abfrage;
	}

	/**
	 * Aus Galerieeintr�gen eine Bildergalerie basteln
	 */
	function make_galerie_first_images($daten = "")
	{
		$daten = trim($daten);
		$bildarray = explode(";", $daten);
		return $bildarray[0];
	}

	/**
	 * Aus Galerieeintr�gen eine Bildergalerie basteln
	 */
	function make_galerie_images($daten = "")
	{
		$daten = trim($daten);
		$bildarray = explode(";", $daten);
		$return = "";
		if (!empty($bildarray)) {
			foreach ($bildarray as $value) {
				if (!empty($value)) {
					$temp_infos = @getimagesize(PAPOO_ABS_PFAD . "/images/thumbs/" . $value);
					// Bild-Breite und -H�he setzen
					/*if (is_array($temp_infos))
					{
						$faktor_breite = ($temp_infos[0] / 180);
						$faktor_hoehe = ($temp_infos[1] / 240);
						$faktor = max($faktor_breite, $faktor_hoehe);
						$this->image_infos['breite'] = round($temp_infos[0]/$faktor);
						$this->image_infos['hoehe'] = round($temp_infos[1]/$faktor);
					}*/
					$this->image_infos['breite'] = $temp_infos[0];
					$this->image_infos['hoehe'] = $temp_infos[1];
					$rel_lightbox = '';
					if ($this->mv_show_lightbox_galerie == 1) $rel_lightbox = 'rel="lightbox"';
					$value = '<a href="'
						. $this->image_core->pfad_images_web
						. $value
						. '" '
						. $rel_lightbox
						. '><img src="'
						. $this->image_core->pfad_thumbs_web
						. $value
						. '" width="'
						. $this->image_infos['breite']
						. '" height="'
						. $this->image_infos['hoehe']
						. '" title="'
						. $find_name
						. '" alt="'
						. $find_name
						. '" /></a>';
					$return .= $value;
				}
			}
		}
		$return = "<div class=\"mv_galerie\">" . $return . "</div>";
		return $return;
	}

	/**
	 * mv::get_field_rights()
	 * Spezialrechte rausholen (in Abh�ngigkeit eines best. Ferldwertes)
	 * @return void
	 */
	function get_field_rights_special()
	{
		$meta_id = $this->meta_gruppe;
		if (!defined("admin")) if (is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
		$sql = sprintf("SELECT * FROM %s,%s
	    WHERE gruppenid = group_id
	    AND userid = '%d'
	    AND group_write = 1
	    AND gruppenid != 11
	    AND group_right_meta_id = '%d'
	    ORDER BY field_id ASC",

			$this->cms->tbname['papoo_lookup_ug'],

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_group_rights",

			$this->db->escape($this->user->userid),
			$this->db->escape($meta_id)
		);
		$group_felderrechte = $this->db->get_results($sql, ARRAY_A);
		if (!is_array($group_felderrechte)) $group_felderrechte = array();
		$this->felder_rechte_aktuelle_gruppe_special = $group_felderrechte;
	}

	private function user_has_write_rights_for_current_flex_and_backend_access()
	{
		$user_id = (int)$this->user->userid;
		$flex_id = (int)$this->checked->mv_id;

		return (int)$this->db->get_var("SELECT COUNT(*) " .
				"FROM {$this->cms->tbname["papoo_gruppe"]} AS g " .
				"JOIN {$this->cms->tbname["papoo_lookup_ug"]} AS ug ON g.gruppeid = ug.gruppenid " .
				"JOIN {$this->cms->tbname["papoo_mv_content_{$flex_id}_content_rights"]} AS mv ON ug.gruppenid = mv.group_id " .
				"WHERE ug.userid = $user_id AND g.admin_zugriff = 1 AND mv.group_write = 1"
			) > 0;
	}

	/**
	 * mv::get_field_rights()
	 * Holt alle Felder ids raus an denen man gerade akut die Leserechte hat
	 * @return void
	 */
	function get_field_rights()
	{
		$meta_id = $this->meta_gruppe;
		if (!defined("admin")) if (is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
		$rechte = array();
		$sql = sprintf("SELECT DISTINCT(field_id)
	    FROM %s, %s
	    WHERE gruppenid = group_id
	    AND userid = '%d'
	    AND group_read = 1
	    AND field_right_meta_id = '%d'
	    ORDER BY field_id ASC",
			$this->cms->tbname['papoo_lookup_ug'],

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_field_rights",

			$this->db->escape($this->user->userid),
			$this->db->escape($meta_id)
		);
		$felderrechte = $this->db->get_results($sql, ARRAY_A);
		if (is_array($felderrechte)) foreach ($felderrechte as $feld) {
			$rechte[] = $feld['field_id'];
		}
		$this->felder_rechte_aktuelle_gruppe = $rechte;
	}

	/**
	 * mv::get_field_rights()
	 * Holt alle Felder ids raus an denen man gerade akut die Schrteibrechte hat
	 * @return void
	 */
	function get_field_rights_schreibrechte()
	{
		$rechte = array();
		//Meta Ebene einstellen, wenn extern dann die externe nehmen.
		$meta_id = $this->meta_gruppe;
		if (!defined("admin")
			and is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
		$sql = sprintf("SELECT DISTINCT(field_id)
	    FROM %s, %s
	    WHERE gruppenid = group_id
	    AND userid = '%d'
	    AND group_write = 1
	    AND field_right_meta_id = '%d'
	    ORDER BY field_id ASC",
			$this->cms->tbname['papoo_lookup_ug'],

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_field_rights",

			$this->db->escape($this->user->userid),
			$this->db->escape($meta_id)
		);
		$felderrechte = $this->db->get_results($sql, ARRAY_A);
		if (is_array($felderrechte)) {
			foreach ($felderrechte as $feld) {
				$rechte[] = $feld['field_id'];
			}
		}
		if (count($rechte) < 1) $this->content->template['no_schreib_rechte_feld'] = "ok";
		$this->felder_schreib_rechte_aktuelle_gruppe = $rechte;
	}

	/**
	 * Holt die Werte aus der Look Up Tabelle f�r dieses Mutliselect Feld
	 */
	function set_multiselect_session($id, $name, $liste)
	{
		$tbname = "papoo_mv_content_" . $this->db->escape($this->checked->mv_id) . "_lang_" . $this->db->escape($id);
		if ($this->cms->tbname[$tbname]) {
			$liste = trim($liste);
			$liste_array = explode("\n", $liste);
			if (!empty($liste_array)) {
				$treffer = " AND (";
				foreach ($liste_array as $wert) {
					$wert = trim($wert);
					$treffer .= "lookup_id='" . $this->db->escape($wert) . "' OR ";
				}
			}
			// letztes " OR " wieder rausnehmen
			$treffer = substr($treffer, 0, strlen($treffer) - 4);
			$treffer = $treffer . ")";
			$sql = sprintf("SELECT * FROM %s
		WHERE lang_id = '%d' %s",
				$this->cms->tbname[$tbname],
				$this->db->escape($this->cms->lang_back_content_id),
				$treffer
			);
			$result = $this->db->get_results($sql);
			// schreibt die Werte in die $_SESSION
			if (!empty($result)) foreach ($result as $row) {
				$_SESSION["mvcform" . $name . "_" . $id][$row->lookup_id] = $row->content;
			}
		}
	}

	/**
	 * Holt die Werte aus der Look Up Tabelle f�r dieses Mutliselect Feld
	 */
	function set_multiselect_session_front($id, $name, $liste)
	{
		$tbname = "papoo_mv_content_" . $this->db->escape($this->checked->mv_id) . "_lang_" . $this->db->escape($id);

		if ($this->cms->tbname[$tbname]) {
			$liste = trim($liste);
			$liste_array = explode("\n", $liste);

			if (!empty($liste_array)) {
				$treffer = " AND (";
				foreach ($liste_array as $wert) {
					$wert = trim($wert);
					$treffer .= "lookup_id='" . $this->db->escape($wert) . "' OR ";
				}
			}
			// letztes " OR " wieder rausnehmen
			$treffer = substr($treffer, 0, strlen($treffer) - 4);
			$treffer = $treffer . ")";
			$sql = sprintf("SELECT * FROM %s
		WHERE lang_id='%d' %s", $this->cms->tbname[$tbname], $this->db->escape($this->cms->lang_id), $treffer);
			$result = $this->db->get_results($sql);
			// schreibt die Werte in die $_SESSION
			if (!empty($result)) {
				foreach ($result as $row) {
					$_SESSION["mvcform" . $name . "_" . $id][$row->lookup_id] = $row->content;
				}
			}
		}
	}

	/**
	 * Holt die Spaltennamen(Formularfelder) aus der Datenbank
	 **/
	function get_spalten_namen()
	{
		$meta_id = $this->meta_gruppe;

		if (!defined("admin")) {
			if (is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
		}
		// Feldname, Feldid und Feldtyp aus der Datenbank holen
		$sql = sprintf("SELECT *
	    FROM %s
	    WHERE mvcform_meta_id = '%d'
	    AND mvcform_form_id = '%d'
	    AND mvcform_aktiv = 1",

			$this->cms->tbname['papoo_mvcform'],

			$this->db->escape($meta_id),
			$this->db->escape($this->checked->mv_id)
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * checked m�gliche Pflichteintr�ge f�r dieses Feld, wenn es ausgef�llt ist
	 **/
	function check_pflicht($felder_daten, $feld_id)
	{
		$meta_id = $this->meta_gruppe;
		if (!defined("admin")) if (is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
		$pflicht_felder = array();
		if (!empty($felder_daten)) {
			foreach ($felder_daten as $feld_daten) {
				$checked_name = "hiddenmvcform_required_felder_" . $feld_daten['mvcform_id'];
				if ($this->checked->$checked_name == "1") $pflicht_felder[] ['pflicht_feld_id'] = $feld_daten['mvcform_id'];
			}
		}
		// Wenn dieses Feld ausgew�hlt ist, dann werden folgende Felder zur Pflicht...
		// Hole die Daten hierzu
		if (empty($pflicht_felder)) {
			// Feldname, Feldid und Feldtyp aus der Datenbank holen
			$sql = sprintf("SELECT pflicht_feld_id FROM %s
		WHERE meta_id = '%d'
		AND mv_id = '%d'
		AND feld_id = '%d'",
				$this->cms->tbname['papoo_mvcform_pflicht_lp'],
				$this->db->escape($meta_id),
				$this->db->escape($this->checked->mv_id),
				$this->db->escape($feld_id)
			);
			$pflicht_felder = $this->db->get_results($sql, ARRAY_A);
		}

		if (!empty($felder_daten)) {
			$counter = 0;
			foreach ($felder_daten as $feld) {
				if (!empty($pflicht_felder)) {
					foreach ($pflicht_felder as $pflicht_feld) {
						if ($pflicht_feld['pflicht_feld_id'] == $feld['mvcform_id']) $felder_daten[$counter]['is_checked'] = "1";
					}
					$counter++;
				}
			}
		}
		return ($felder_daten);
	}

	/**
	 * L�scht alle Multiselect $_SESSION Eintr�ge
	 **/
	function delete_session()
	{
		// Feld-ID und Feldname aus der Datenbank holen
		$sql = sprintf("SELECT DISTINCT(mvcform_id),
	    mvcform_name
	    FROM %s
	    WHERE mvcform_type = 'multiselect'",
			$this->cms->tbname['papoo_mvcform']);
		$result = $this->db->get_results($sql);
		if (!empty($result)) foreach ($result as $row) {
			unset($_SESSION["mvcform" . $row->mvcform_name . "_" . $row->mvcform_id]);
		}
	}

	/**
	 * Gibt den Feldnamen und die ID zur�ck
	 **/
	function get_feld_name($key)
	{
		$arr = explode("_", $key);
		$find_id = end($arr);
		$mvcform_name = substr($key, 0, -(strlen($find_id) + 1));
		return array(
			$mvcform_name,
			$find_id
		);
	}

	/**
	 * Gibt die Werte aus der Look Up Tabelle für das Multiselect Feld
	 * $name.'_'.$id für den Benuzter $mv_content_id zurück
	 *
	 * @param int $id Feld ID
	 * @param string $name MultiselectFeldname
	 * @param int $mv_content_id Benuzter ID
	 * @param string $separator Trennzeichen für Multi-WErte
	 * @return $value (Werte aus der Look Up Tabelle für das Feld $name_$id für
	 *     den Benuzter ID)
	 * @access public
	 *
	 **/
	function get_multiselect_werte($id, $name, $mv_content_id, $separator = '<br />')
	{
		$werte = $this->get_multiselect_werte_array($id, $name, $mv_content_id);
		if ($werte) {
			return implode($separator, $werte);
		} else {
			return $lang_wert = $this->content->template['plugin']['mv']['new_content'];
		}
	}

	/**
	 * Gibt die Werte aus der Look Up Tabelle für das Multiselect Feld
	 * $name.'_'.$id für den Benuzter $mv_content_id zurück
	 *
	 * @param int $id Feld ID
	 * @param string $name MultiselectFeldname
	 * @param int $mv_content_id Benuzter ID
	 * @return string[] (Werte aus der Look Up Tabelle für das Feld $name_$id
	 *     für den Benuzter ID)
	 * @access public
	 *
	 **/
	function get_multiselect_werte_array($id, $name, $mv_content_id)
	{
		$field_name = $name . "_" . $id;
		$sql = sprintf("SELECT %s FROM %s
		WHERE mv_content_id = '%d'",
			$field_name,

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_search_"
			. $this->db->escape($this->cms->lang_back_content_id),

			$this->db->escape($mv_content_id)
		);
		$field_werte = $this->db->get_var($sql);
		$arr_field_werte = explode("\n", $field_werte);
		$lang_wert = [];
		if (count($arr_field_werte)) {
			foreach ($arr_field_werte as $row => $value) {
				if ($value != "") {
					$sql = sprintf("SELECT content FROM %s T1
					WHERE T1.lookup_id = '%d'
					AND lang_id = '%d'",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_lang_"
						. $this->db->escape($id),

						$value,
						$this->db->escape($this->cms->lang_back_content_id)
					);
					$lang_wert[] = $this->db->get_var($sql);
				}
			}
		}
		return $lang_wert;
	}

	/**
	 * Holt die Multiselectwerte aus der Tabelle und gibt sie ans Template
	 * weiter
	 */
	function get_multiselect_werte_front($id, $name, $mv_content_id)
	{
		$field_name = $name . "_" . $id;
		$sql = sprintf("SELECT %s FROM %s
			WHERE mv_content_id = '%d'",
			$field_name,

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_search_"
			. $this->db->escape($this->cms->lang_id),

			$this->db->escape($mv_content_id)
		);
		$field_werte = $this->db->get_var($sql);
		$arr_field_werte = explode("\n", $field_werte);
		if ($arr_field_werte) {
			foreach ($arr_field_werte as $row => $value) {
				if ($value != "") {
					$sql = sprintf("SELECT content FROM %s T1
					WHERE T1.lookup_id = '%d'
					AND lang_id = '%d'",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_lang_"
						. $this->db->escape($id),

						$value,
						$this->db->escape($this->cms->lang_id)
					);
					$lang_wert .= $this->db->get_var($sql) . ", <br />";
				}

			}
			$lang_wert = substr($lang_wert, 0, -8); // letztes <br /> wieder raus
		} else {
			$lang_wert = $this->content->template['plugin']['mv']['new_content'];
		}
		return ($lang_wert);
	}

	/**
	 * Liefert den Wert f�r mv_content_id aus der Lang Tabelle BE
	 **/
	function get_lp_wert($id, $name, $mv_content_id, $ctrl = 0)
	{
		if (!$ctrl) {
			$field_name = $name . "_" . $id;
			$sql = sprintf("SELECT %s FROM %s
		WHERE mv_content_id = '%d'",
				$field_name,

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_search_"
				. $this->db->escape($this->cms->lang_back_content_id),

				$this->db->escape($mv_content_id)
			);
			$field_wert = $this->db->get_var($sql);
		} else $field_wert = $mv_content_id; // in dem Fall keine mv_content_id, sondern eine lookup_id !!
		$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		$sql = sprintf("SELECT content FROM %s T1
	    WHERE T1.lookup_id = '%d'
	    AND lang_id = '%d'",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_lang_"
			. $this->db->escape($id),

			$field_wert,
			$lang
		);
		$lang_wert = $this->db->get_var($sql);
		return ($lang_wert);
	}

	/**
	 * Liefert den Wert f�r content aus der Lang Tabelle FE
	 **/
	function get_lp_wert_front($id, $name, $mv_content_id)
	{
		$sql = sprintf("SELECT content FROM %s T1, %s T2
	    WHERE mv_content_id = '%d'
	    AND T1.lookup_id = T2.%s
	    AND lang_id = '%d'",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_lang_"
			. $this->db->escape($id),

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_search_"
			. $this->cms->lang_id,

			$this->db->escape($mv_content_id),
			$name . "_" . $id,
			$this->db->escape($this->cms->lang_id)
		);
		$lang_wert = $this->db->get_var($sql);
		return ($lang_wert);
	}

	/**
	 * Gibt die Werte aus der Look Up Tabelle f�r das Bilder Galerie Feld
	 * $name.'_'.$id f�r den Benuzter $mv_content_id zur�ck
	 *
	 * @param ing $id ,$name,$mv_content_id (Feld ID, MultiselectFeldname,
	 *     Benuzter ID)
	 * @return $value (Werte aus der Look Up Tabelle f�r das Feld $name_$id f�r
	 *     den Benuzter ID)
	 * @access public
	 *
	 **/
	function get_galerie_werte($id, $name, $mv_content_id)
	{
		$tbname = "papoo_mv_content_" . $this->checked->mv_id . "_lang_" . $id;
		if ($this->cms->tbname[$tbname]) {
			if (!empty($result)) {
				foreach ($result as $row) {
					$value .= $row->content . "<br />";
				}
			}
		} else $value = $this->content->template['plugin']['mv']['new_content'];
		return ($value);
	}

	/**
	 * Hochladen der Bilder durchf�hren.
	 * Tempor�res Bild �bernehmen und Thumbnail erstellen
	 */
	function upload_picture_mv($name, $mode = "")
	{
		$this->image_core->image_load($_FILES[$name]); // Bild von Server-temp nach /images/TMP_10_... speichern
		$image_infos = $this->image_core->image_infos; // Bild Attribute zwischenspeichern
		if ($image_infos['name']) $this->dateiname_upload = $dateiname_temp = "TMP_" . $image_infos['name']; // aus $_FILES[$name] extrahiert
		else $this->dateiname_upload = $dateiname_temp = "";
		// 1. Schritt Upload und Thumbnail
		if ($mode == "") {
			// Nur die Bild-Typen JPG, GIF, PNG erlauben
			if (!$image_infos['type']) {
				// Fehlermeldung ausgeben
				$this->content->template['mv_upload_error'] = $this->content->template['plugin']['mv']['falsche_dateiendung2'];
				// tempor�r hochgeladenee Bild-Datei l�schen
				@unlink($image_infos['bild_temp']);
				// Formular zum Upload einer Bild-Datei anbieten
				$this->content->template['template_weiche'] = 'HOCHLADEN';
				$this->upload_ok = false;
				return false;
			}
			// Resize, wenn Bild zu gro�
			if ($image_infos['breite'] > $this->pic_max_breite
				|| $image_infos['hoehe'] > $this->pic_max_hoehe) {
				// Die gew�nschte l�ngste Seite
				if ($this->pic_max_breite >= $this->pic_max_hoehe) $maxl = $this->pic_max_breite;
				else $maxl = $this->pic_max_hoehe;
				$size[0] = $image_infos['breite'];
				$size[1] = $image_infos['hoehe'];
				// Breite < H�he, dann Breite umrechnen
				if ($image_infos['breite'] < $image_infos['hoehe']) {
					$breite = Round(($image_infos['breite'] / $image_infos['hoehe']) * $maxl);
					$hoehe = $maxl;
				} // H�he < Breite, dann H�he umrechnen
				elseif ($image_infos['breite'] > $image_infos['hoehe']) {
					$breite = $maxl;
					$hoehe = Round(($image_infos['hoehe'] / $image_infos['breite']) * $maxl);
				} // Breite = H�he
				else $breite = $hoehe = $maxl;
				$image = $this->image_core->image_create($image_infos['bild_temp']); // get resource ID
				$new_image = imagecreatetruecolor($breite, $hoehe); // create black image of the specified size
				// resize/resample $image to $new_image overwrites black image
				imagecopyresampled($new_image, $image, 0, 0, 0, 0, $breite, $hoehe, $image_infos['breite'], $image_infos['hoehe']);
				// Temp Bild mit resized ersetzen
				switch ($image_infos['type']) {
					case "JPG":
						imagejpeg($new_image, $image_infos['bild_temp'], $this->cms->system_config_data['config_jpg_komprimierungsfaktor_feld_']);
						break;
					case "GIF":
						imagegif($new_image, $image_infos['bild_temp']);
						break;
					case "PNG":
						imagepng($new_image, $image_infos['bild_temp']);
						break;
				}
				$image_infos['bild_temp'] = $new_image; // resource Id
				$image_infos['breite'] = $breite; // new size, used to create thumbnail
				$image_infos['hoehe'] = $hoehe;
				$resized = 1;
			}
			// Thumbnail erzeugen und sichern
			if ($resized) $image = $image_infos['bild_temp']; // resource ID ist schon da
			else $image = $this->image_core->image_create($image_infos['bild_temp']); // get resource ID
			$dateiname_thumbnail = $this->image_core->pfad_thumbs . $dateiname_temp;
			$dimension = $this->image_core->image_get_thumbnail_size($image_infos['breite'], $image_infos['hoehe']);
			$thumbnail = $this->image_core->image_create(array($dimension['breite'], $dimension['hoehe']));
			imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $dimension['breite'], $dimension['hoehe'], $image_infos['breite'], $image_infos['hoehe']);
			$this->image_core->image_save($thumbnail, $dateiname_thumbnail);
		}
		// 2. Schritt erfolgt nur beim Speichern von neuen Daten, nicht beim Speichern von vorhandenen Daten (edit)
		// oder beim Upload f�r vorhandene Daten (edit), dann rename temp pictures
		// $mode=new kann nur bei fp_content.html (BE) und mv_create_front.html beim Speichern auftreten
		// die zus. Abfrage der Templates erm�glicht diesen Templates den rename nach dem Upload und vor dem Speichern
		if ($mode == "new" xor
			($this->checked->template == "mv/templates/change_user.html"
				or $this->checked->template == "mv/templates/mv_edit_front.html"
				or $this->checked->template == "mv/templates/mv_edit_own_front.html")) {
			// Pruefen ob Bilder schon hochgeladen wurden, aber das Form nicht vollstaendig abgeschickt wurde
			// => In der Datei $tempfilepath werden die Feldnamen und zugehoerigen hochgeladenen Dateien drin stehen
			// Dieser Block wurde als Fix dafuer eingefuegt, dass Bilder Felder nicht required sein muessen -- ag
			{
				$tempfilepath = PAPOO_ABS_PFAD . "/images/temp/{$this->user->userid}_form_temp.temp";

				if (is_file($tempfilepath)) {
					$file_contents = file_get_contents($tempfilepath);

					unlink($tempfilepath);

					$previously_uploaded = [];
					$i = 0;
					foreach (preg_split("/((\r?\n)|(\r\n?))/", $file_contents) as $line) {
						if (empty($line)) {
							continue;
						}

						if ($i % 2 == 0) {
							$previously_uploaded[] = [$line, null];
						} else {
							end($previously_uploaded);

							$unfinished_last_element = &$previously_uploaded[key($previously_uploaded)];

							$unfinished_last_element[1] = $line;
						}
						$i++;
					}

					$found_previously_uploaded_file = false;

					$previously_uploaded = array_reverse($previously_uploaded);
					foreach ($previously_uploaded as $uploaded_file) {
						if ($uploaded_file[0] === $name and is_file(PAPOO_ABS_PFAD . "/images/" . $uploaded_file[1])) {
							$found_previously_uploaded_file = $uploaded_file[1];
							break;
						}
					}

					$orgname = $name;
					if (is_string($found_previously_uploaded_file)) {
						$filetoload = $found_previously_uploaded_file;
					}
				}
			}

			if ($mode == "") $name = substr($dateiname_temp, 3); // new Filename
			else // Neueintrag speichern aktiv
			{
				// Filenamen extrahieren, setzen
				$feld_name = substr($name, 7); // Feldname, skip preceding "mvcform"
				$dateiname_temp = $name = $_SESSION['plugin_mv'][$feld_name];
				$name = substr($dateiname_temp, 3); // new Filename
			}

			// Falls kein Bild vorhanden (gel�scht || nichts hochgeladen), sonst renames machen
			if (substr($dateiname_temp, 0, 4) == "TMP_") {
				$name_sicher = $this->diverse->sicherer_dateiname($name); // new Filename
				$time_id = time();
				$this->dateiname_upload = $time_id . $name_sicher; // timestamp plus new filename
				$dateiname = str_replace("//", "/", $this->image_core->pfad_images . $this->dateiname_upload); // plus Pfad
				$dateiname_thumb = str_replace("//", "/", $this->image_core->pfad_thumbs . $this->dateiname_upload); // plus Pfad thumbs
				@rename(str_replace("//", "/", $this->image_core->pfad_images . $dateiname_temp), $dateiname); // rename pic
				@rename(str_replace("//", "/", $this->image_core->pfad_thumbs . $dateiname_temp), $dateiname_thumb); // rename thumb
			}
		}
	}

	/**
	 * Die Datei $name im images und images/thumbs Ordner l�schen
	 **/
	function delete_picture($name, $update = 0)
	{
		// Upload Bild
		@unlink($this->image_core->pfad_images . $name);
		// Upload Tumbnail
		@unlink($this->image_core->pfad_thumbs . $name);
	}

	/**
	 * Das tempor�res hochladen der Files durchf�hren.
	 */
	function upload_file_mv($name, $mode = "")
	{
		if ($_FILES[$name]['name']) $this->dateiname_upload = $dateiname_temp = "TMP_" . $_FILES[$name]['name'];
		else $this->dateiname_upload = $dateiname_temp = "";
		// 1. Schritt Upload und Thumbnail
		if ($mode == "") {
			// Nur diese File-Typen erlauben
			$datei_ext = strtolower(substr(strrchr($dateiname_temp, '.'), 1));
			$extensions = array('php',
				'php3',
				'php4',
				'php5',
				'php6',
				'phtml',
				'cgi',
				'pl'
			);
			if (in_array($datei_ext, $extensions)
				or $datei_ext == "") {
				$this->upload_ok = false;
				$this->content->template['mv_upload_error'] = $this->content->template['plugin']['mv']['falsche_dateiendung'];
				@unlink($_FILES[$name]['name']);
				$this->dateiname_upload = "";
				return false;
			}
			if ($this->checked->template == "mv/templates/mv_create_front.html"
				or $this->checked->template == "mv/templates/fp_content.html") {
				$dateiname = $this->cms->pfadhier . '/files/' . $dateiname_temp;
				$mode = "intern";
			} else {
				$name_1 = substr($dateiname_temp, 3); // new Filename
				$time_id = time();
				$datname_sicher = $this->diverse->sicherer_dateiname($name_1); // new Filename
				$this->dateiname_upload = $time_id . $datname_sicher;
				$dateiname = $this->cms->pfadhier . '/files/' . $this->dateiname_upload; // plus Pfad
			}
		}
		// 2. Schritt nur beim Speichern von neuen Daten, nicht beim Speichern von vorhandenen Daten (edit)
		// oder beim Upload f�r vorhandene Daten (edit), dann rename temp pictures
		// new kann nur bei fp_content.html und mv_create_front.html beim Speichern auftreten
		// die zus. Abfrage der Templates erm�glicht diesen Templates den rename nach dem Upload und vor dem Speichern
		if (($mode == "new"
				or $mode == "intern") xor
			($this->checked->template == "mv/templates/change_user.html"
				or $this->checked->template == "mv/templates/mv_edit_front.html"
				or $this->checked->template == "mv/templates/mv_edit_own_front.html")) {
			if ($mode == ""
				or $mode == "intern") $what = move_uploaded_file($_FILES[$name]['tmp_name'], $dateiname); // Upload aktiv
			// Neueintrag speichern aktiv (mode = new)
			else {
				// Filenamen extrahieren, setzen
				$feld_name = substr($name, 7); // Feldname, skip preceding "mvcform"
				$dateiname_temp = $_SESSION['plugin_mv'][$feld_name];
				if (substr($dateiname_temp, 0, 4) == "TMP_") {
					$name = substr($dateiname_temp, 3); // new Filename
					$time_id = time();
					$this->dateiname_upload = $time_id . $this->diverse->sicherer_dateiname($name); // new Filename
					$destination = $this->cms->pfadhier . '/files/' . $this->dateiname_upload; // plus Pfad
					rename($this->cms->pfadhier . '/files/' . $dateiname_temp, $destination); // rename file
				} else $this->dateiname_upload = "";
				#$_SESSION['plugin_mv'][$feld_name] = $this->dateiname_upload;

				$tempfilepath = PAPOO_ABS_PFAD . "/files/temp/{$this->user->userid}_form_temp.temp";
				unlink($tempfilepath);
			}
		}
	}

	/**
	 * Die tempor�ren Dateien so wirklich richtig abspeichern;)
	 * Die Mitglieder ID wird noch hinzugef�gt
	 **/
	function save_file($name, $id)
	{
		$name_neu = $id . "_" . $name;
		rename($this->cms->pfadhier . "/files/" . $name, $this->cms->pfadhier . "/files/" . $name_neu);
	}

	/**
	 * Die Datei $name im images und images/thumbs Ordner l�schen
	 **/
	function delete_file($name)
	{
		@unlink($this->cms->pfadhier . "/files/" . $name);
	}

	function update_file_pic_content_tables($filename, $fieldname)
	{
		if ($this->checked->template == "mv/templates/change_user.html"
			or $this->checked->template == "mv/templates/mv_edit_front.html"
			or $this->checked->template == "mv/templates/mv_edit_own_front.html") {
			$sql = sprintf("UPDATE %s SET %s = '%s'
		WHERE mv_content_id = '%s'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id),

				$fieldname,
				$filename,
				$this->db->escape($this->checked->mv_content_id)
			);
			#$this->db->query($sql);
			$sql = sprintf("SELECT mv_lang_id
		FROM %s",
				$this->cms->tbname['papoo_mv_name_language']
			);
			$sprachen = $this->db->get_results($sql);
			// speichern in eingestellter Sprache
			$sql = sprintf("UPDATE %s SET %s = '%s'
		WHERE mv_content_id = '%s'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_search_"
				. $this->db->escape($this->cms->lang_back_content_id),

				$fieldname,
				$filename,
				$this->db->escape($this->checked->mv_content_id)
			);
			$this->db->query($sql);
		}
	}

	/**
	 * Hole f�r das Feld $name eventuelle Eintr�ge anderer Felder die zum
	 * Pflichtfeld werden, wenn dieses Feld ausgef�llt ist
	 **/
	function get_required_feld($name)
	{
		$meta_id = $this->meta_gruppe;
		if (!defined("admin")
			and is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
		$arr = explode("_", $name);
		$id = end($arr);
		#$id = end(explode("_", $name));
		$sql = sprintf("SELECT * FROM %s, %s
	    WHERE meta_id = '%d'
	    AND mv_id = '%d'
	    AND pflicht_feld_id = '%d'
	    AND feld_id = mvcform_id
	    AND mv_id = mvcform_form_id
	    AND meta_id = mvcform_meta_id",
			$this->cms->tbname['papoo_mvcform_pflicht_lp'],
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($meta_id),
			$this->db->escape($this->checked->mv_id),
			$this->db->escape($id)
		);
		$result = $this->db->get_results($sql);
		return ($result);
	}

	/**
	 * Speichert f�r alle Felder die Pflichtfelder- und Auflistungseintr�ge
	 * (und Normaler User) neu ab
	 **/
	function save_pflicht_und_list()
	{
		$meta_id = $this->meta_gruppe;
		if (!defined("admin")
			and is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
		// holt alle FormFelder die es f�r diese Verwaltung und diese Metaebene gibt aus der Datenbank
		$sql = sprintf("SELECT * FROM %s
			WHERE mvcform_form_id = '%d'
			AND mvcform_meta_id = '%d'
			AND mvcform_form_id = '%d'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->mv_id),
			$this->db->escape($meta_id),
			$this->db->escape($this->checked->mv_id)
		);
		$result = $this->db->get_results($sql);
		if (!empty($result)) {
			// und geht die FormFelder durch
			foreach ($result as $row) {

				// dabei die �bermittelten Werte($this->checked) f�r Pflicht, Ausflisten Back, Auflisten Front, normale User Felder und Protokoll in der Datenbank updaten
				$admin_mustc = "mvcform_admin_must_" . $row->mvcform_id;
				$this->checked->$admin_mustc = $this->checked->$admin_mustc ? $this->checked->$admin_mustc : 0;
				if ($this->content->template['is_admin'] == "ok") {
					$admin_must = "mvcform_admin_must_" . $row->mvcform_id;
					$this->checked->$admin_mustc = $this->checked->$admin_mustc ? $this->checked->$admin_must : 0;
					$admin_must = "mvcform_admin_must='" . $this->db->escape($this->checked->$admin_must) . "', ";
				} else $admin_must = "";
				$must = "mvcform_must_" . $row->mvcform_id;
				$must_back = "mvcform_must_back_" . $row->mvcform_id;
				$list = "mvcform_list_" . $row->mvcform_id;
				$list_front = "mvcform_list_front_" . $row->mvcform_id;
				$normaler_user = "mvcform_normaler_user_" . $row->mvcform_id;
				$protokoll = "mvcform_protokoll_" . $row->mvcform_id;
				$search = "mvcform_search_" . $row->mvcform_id;
				$search_back = "mvcform_search_back_" . $row->mvcform_id;
				$kalender = "mvcform_kalender_" . $row->mvcform_id;
				$filter_front = "mvcform_filter_front_" . $row->mvcform_id;
				$defaulttreeviewname = "mvcform_defaulttreeviewname";
				$aktiv = "mvcform_aktiv_" . $row->mvcform_id;
				if (($row->mvcform_admin_must) == 1
					|| $this->checked->$admin_mustc == 1) {
					$this->checked->$must = 1;
					$this->checked->$must_back = 1;
				}
				$this->checked->$must = $this->checked->$must ? $this->checked->$must : 0;
				$this->checked->$must_back = $this->checked->$must_back ? $this->checked->$must_back : 0;
				$this->checked->$list = $this->checked->$list ? $this->checked->$list : 0;
				$this->checked->$list_front = $this->checked->$list_front ? $this->checked->$list_front : 0;
				$this->checked->$normaler_user = $this->checked->$normaler_user ? $this->checked->$normaler_user : 0;
				$this->checked->$protokoll = $this->checked->$protokoll ? $this->checked->$protokoll : 0;
				$this->checked->$search = $this->checked->$search ? $this->checked->$search : 0;
				$this->checked->$search_back = $this->checked->$search_back ? $this->checked->$search_back : 0;
				$this->checked->$kalender = $this->checked->$kalender ? $this->checked->$kalender : 0;
				$this->checked->$filter_front = $this->checked->$filter_front ? $this->checked->$filter_front : 0;
				$this->checked->$defaulttreeviewname = $this->checked->$defaulttreeviewname ? $this->checked->$defaulttreeviewname : 0;
				$this->checked->$aktiv = $this->checked->$aktiv ? $this->checked->$aktiv : 0;
				$sql = sprintf("UPDATE %s SET %s mvcform_must = '%s',
					mvcform_must_back = '%s',
					mvcform_list = '%s',
					mvcform_list_front = '%s',
					mvcform_normaler_user = '%d',
					mvcform_protokoll = '%d',
					mvcform_search = '%d',
					mvcform_search_back = '%d',
					mvcform_kalender = '%d',
					mvcform_filter_front = '%d',
					mvcform_defaulttreeviewname = '%d',
					mvcform_aktiv = '%d'
					WHERE mvcform_id = '%d'
					AND mvcform_meta_id = '%d'",
					$this->cms->tbname['papoo_mvcform'],
					$admin_must,
					$this->db->escape($this->checked->$must),
					$this->db->escape($this->checked->$must_back),
					$this->db->escape($this->checked->$list),
					$this->db->escape($this->checked->$list_front),
					$this->db->escape($this->checked->$normaler_user),
					$this->db->escape($this->checked->$protokoll),
					$this->db->escape($this->checked->$search),
					$this->db->escape($this->checked->$search_back),
					$this->db->escape($this->checked->$kalender),
					$this->db->escape($this->checked->$filter_front),
					$this->db->escape($row->mvcform_id == $this->checked->$defaulttreeviewname ? 1 : 0),
					$this->db->escape($this->checked->$aktiv),
					$this->db->escape($row->mvcform_id),
					$this->db->escape($meta_id)
				);
				$this->db->query($sql);
			}
		}
		// wenn es ein admin in der admin Meta Ebene war, dann die Adminpflicht Felderwerte auf die anderen Meta Ebenen �bertragen
		if ($this->meta_gruppe == "1") {
			// hol die Adminpflicht Felderwerte aus der Datenbank
			$sql = sprintf("SELECT mvcform_id,
				mvcform_admin_must
				FROM %s
				WHERE mvcform_meta_id = '1'",
				$this->cms->tbname['papoo_mvcform']
			);
			$admin_must_werte = $this->db->get_results($sql, ARRAY_A);
			if (!empty($admin_must_werte)) {
				foreach ($admin_must_werte as $admin_must_wert) {
					// und trag diese Werte bei den anderen Meta Ebenen ein
					$sql = sprintf("UPDATE %s SET mvcform_admin_must = '%s'
						WHERE mvcform_id = '%s'",
						$this->cms->tbname['papoo_mvcform'],
						$this->db->escape($admin_must_wert['mvcform_admin_must']),
						$this->db->escape($admin_must_wert['mvcform_id'])
					);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * Checkt ob das Feld auch in der Mitgliederliste erscheinen soll
	 **/
	function feld_is_listed($find_id, $list)
	{
		if (empty($this->is_listed_array)) {
			$sql = sprintf("SELECT * FROM %s
		WHERE mvcform_meta_id='%d'", $this->cms->tbname['papoo_mvcform'], $this->db->escape($this->meta_gruppe));

			$this->is_listed_array = $this->db->get_results($sql, ARRAY_A);

			foreach ($this->is_listed_array as $ar) {
				$neu[$ar['mvcform_id']][$ar['mvcform_meta_id']] = $ar['mvcform_list'];
			}
			$this->is_listed_array = $neu;
		}
		$sql = sprintf("SELECT %s FROM %s
	    WHERE mvcform_id='%d'
	    AND mvcform_meta_id='%d'", $this->db->escape($list),
			$this->cms->tbname['papoo_mvcform'], $this->db->escape($find_id),
			$this->db->escape($this->meta_gruppe));
		#$result = $this->db->get_var($sql);
		$result = $this->is_listed_array[$find_id][$this->meta_gruppe];

		// Ausnahme ist das Feld=userid, die Userid wird immer angezeigt
		if ($find_id == "userid") {
			$result = "1";
		}

		if (!empty($result)) {
			if ($result == "1") return (true);
		}
		return (false);
	}

	/**
	 * Checkt ob die Mitgliederverwaltung (plugin_papoo_id=35) auch installiert
	 * ist
	 **/
	function is_mv_installed()
	{
		$sql = sprintf("SELECT * FROM %s
	    WHERE plugin_papoo_id='35'", $this->cms->tbname['papoo_plugins']);

		if ($this->db->get_var($sql)) {
			return (true);
		} else {
			return (false);
		}
	}

	/**
	 * Checkt ob es eine Mitgliederverwaltung oder Standard Verwaltung ist
	 **/
	function is_mv_or_st()
	{
		$sql = sprintf("SELECT mv_art FROM %s
	    WHERE mv_id = '%d'",
			$this->cms->tbname['papoo_mv'],
			$this->db->escape($this->checked->mv_id)
		);
		if ($this->db->get_var($sql) == "2") return (true);
		else return (false);
	}

	/**
	 * speichert die "alten" Daten aus der Datenbank ins Template
	 **/
	function save_temp_old_entry()
	{
		$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		$sql = sprintf("SELECT * FROM %s
	    WHERE mv_content_id = '%d'
	    LIMIT 1",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_search_"
			. $lang,

			$this->db->escape($this->checked->mv_content_id)
		);
		$this->content->template['temp_old_entry'] = $this->db->get_results($sql);
	}

	/**
	 * Vergleicht die alten Daten aus der Datenbank mit den neuen Daten aus dem
	 * Form
	 **/
	function compare_content()
	{
		$result_diff = array();
		// hole die neuen Werte aus der Datenbank
		$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		$sql = sprintf("SELECT * FROM %s
	    WHERE mv_content_id = '%d'
	    LIMIT 1",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_search_"
			. $lang,

			$this->db->escape($this->checked->mv_content_id)
		);
		$result = $this->db->get_results($sql);// new data
		$result_alt = $this->content->template['temp_old_entry']; // old data
		// die alten Werte aus dem Zwischenspeicher holen
		// die ersten 8 Systemspalten nat�rlich nicht vergleichen, sprich leer machen
		$result_alt[0]->mv_content_id = $result[0]->mv_content_id = "";
		$result_alt[0]->mv_content_owner = $result[0]->mv_content_owner = "";
		$result_alt[0]->mv_content_userid = $result[0]->mv_content_userid = "";
		$result_alt[0]->mv_content_sperre = $result[0]->mv_content_sperre = "";
		$result_alt[0]->mv_content_teaser = $result[0]->mv_content_teaser = '';
		$result_alt[0]->mv_content_create_date = $result[0]->mv_content_create_date = "";
		$result_alt[0]->mv_content_edit_date = $result[0]->mv_content_edit_date = "";
		$result_alt[0]->mv_content_create_owner = $result[0]->mv_content_create_owner = "";
		$result_alt[0]->mv_content_edit_user = $result[0]->mv_content_edit_user = "";
		// gehe die einzelnen Werte durch
		foreach ($result[0] as $key => $value) {
			// wenn sich die Werte unterscheiden, dann speichere den Unterschied
			if ($result[0]->$key != $result_alt[0]->$key) {
				if (empty($result_alt[0]->$key)) $result_alt[0]->$key = "0";
				$result_diff[$key] = $result_alt[0]->$key;
			}
		}
		// wenn es Unterschiede gibt, dann die �nderung in der Datenbank speichern
		if (!empty($result_diff)) {
			foreach ($result_diff as $key => $value) {
				list($name, $id) = explode("_", $key);
				$sql = sprintf("INSERT INTO %s SET mv_pro_date = NOW(),
		    mv_pro_mv_content_id = '%s',
		    mv_pro_group_id = '%s',
		    mv_pro_feld_id = '%s',
		    mv_pro_old_content = '%s',
		    mv_pro_login_id = '%s',
		    mv_pro_ip = '%s',
		    mv_pro_mv_id = '%d'",
					$this->cms->tbname['papoo_mv_protokoll'],
					$this->db->escape($this->checked->mv_content_id),
					'',
					$this->db->escape($id),
					$this->db->escape($value),
					$this->db->escape($this->user->userid),
					$this->db->escape($_SERVER['REMOTE_ADDR']),
					$this->db->escape($this->checked->mv_id)
				);
				$this->db->query($sql);
			}
		}
		// und wieder alten Eintrag l�schen
		$this->content->template['temp_old_entry'] = array();
	}

	#nicht entfernen

	/**
	 * mv::activate_mv_user()
	 * Einen User aktivieren, der auf den E-Mail Link geklickt hat.
	 * called by lib/classes/user_class.php do_account()
	 * @return void
	 */
	function activate_mv_user($result = array())
	{
		$sql = sprintf("SELECT MIN(mv_id) FROM %s
	    WHERE mv_art = '2'",
			$this->cms->tbname['papoo_mv']
		);
		$mv_id = $this->db->get_var($sql);
		if (!empty($mv_id)) {
			$userid = $result['0']->userid;
			$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
			$sql = sprintf("SELECT mv_content_id FROM %s
		WHERE mv_content_userid = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($mv_id)
				. "_search_"
				. $lang,

				$this->db->escape($userid)
			);
			$mv_content_id = $this->db->get_var($sql);
			// Trage die Daten in die Mitgliederliste ein
			$sql = sprintf("UPDATE %s SET active_7 = '1'
		WHERE mv_content_id = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($mv_id),

				$this->db->escape($mv_content_id)
			);
			#$this->db->query($sql);
			$sql = sprintf("SELECT mv_lang_id FROM %s",
				$this->cms->tbname['papoo_mv_name_language']
			);
			$sprachen = $this->db->get_results($sql, ARRAY_A);
			if (!empty($sprachen)) {
				foreach ($sprachen as $sprache) {
					$sql = sprintf("UPDATE %s SET active_7 = '1'
			WHERE mv_content_id = '%s'",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($mv_id)
						. "_search_"
						. $this->db->escape($sprache['mv_lang_id']),

						$this->db->escape($mv_content_id)
					);
					$this->db->query($sql);
					// Sprachtabellen Feld active_7
					$sql = sprintf("INSERT INTO %s SET lookup_id = '%d',
			content = '%d',
			lang_id = '%d'",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($mv_id)
						. "_lang_7",

						$this->db->escape($mv_content_id),
						$this->db->escape(1),
						$this->db->escape($sprache['mv_lang_id'])
					);
					$this->db->query($sql);
				}
				#$this->update_all_content_tables("active_7", $value = "1");
			}
		}
	}

	#nicht entfernen

	/**
	 * F�gt einen User aus der normalen Frontend Registrierung der ersten MV zu
	 */
	function insert_mv_user($userid, $md5password, $neuemail, $neuerusername)
	{
		// hole die mv_id f�r die zuerst Installierte MV aus der Datenbank
		$sql = sprintf("SELECT MIN(mv_id)
	    FROM %s
	    WHERE mv_art = '2'",
			$this->cms->tbname['papoo_mv']
		);
		$mv_id = $this->db->get_var($sql);
		if ($this->checked->antwortmail == "ok") $antwortmail = 1;
		if ($this->checked->newsletter == "ok") $newsletterok = 1;
		// gibts schon eine MV?
		if (!empty($mv_id)) {
			// Trage die Daten in die Mitgliederliste ein
			$sql = sprintf("INSERT INTO %s SET	mv_content_userid = '%s',
		Benutzername_1 = '%s',
		passwort_2 = '%s',
		email_3 = '%s',
		antwortmail_4 = '%s',
		newsletter_5 = '%s',
		board_6 = '%s',
		active_7 = '%s',
		signatur_8 = '%s',
		mv_content_create_date = '%s',
		mv_content_edit_date = '%s',
		mv_content_create_owner = '%s'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($mv_id),

				$this->db->escape($userid),
				$this->db->escape($neuerusername),
				$this->db->escape($md5password),
				$this->db->escape($this->checked->neuemail),
				$this->db->escape($antwortmail),
				$this->db->escape($newsletterok),
				$this->db->escape($this->checked->forum_board),
				"0",
				$this->db->escape($this->checked->signatur),
				$this->unixtime_to_longtime(time()),
				$this->unixtime_to_longtime(time()),
				$this->db->escape($this->checked->neuemail)
			);
			#$this->db->query($sql);
			$mv_content_insert_id = $this->db->insert_id;
			// Sprachtabellen f�r die schnellere Suche
			$sql = sprintf("SELECT mv_lang_id
		FROM %s",
				$this->cms->tbname['papoo_mv_name_language']
			);
			$sprachen = $this->db->get_results($sql, ARRAY_A);
			if (!empty($sprachen)) {
				foreach ($sprachen as $sprache) {
					$sql = sprintf("INSERT INTO %s SET mv_content_id = '%d',
			mv_content_userid = '%s',
			Benutzername_1 = '%s',
			email_3 = '%s',
			passwort_2 = '%s',
			antwortmail_4 = '%s',
			newsletter_5 = '%s',
			board_6 = '%s',
			active_7 = '%s',
			signatur_8 = '%s',
			mv_content_create_date = '%s',
			mv_content_edit_date = '%s',
			mv_content_create_owner = '%s'",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($mv_id)
						. "_search_"
						. $this->db->escape($sprache['mv_lang_id']),

						$this->db->escape($mv_content_insert_id),
						$this->db->escape($userid),
						$this->db->escape($neuerusername),
						$this->db->escape($this->checked->neuemail),
						$this->db->escape($md5password),
						$this->db->escape($antwortmail),
						$this->db->escape($newsletterok),
						$this->db->escape($this->checked->forum_board),
						"0",
						$this->db->escape($this->checked->signatur),
						$this->unixtime_to_longtime(time()),
						$this->unixtime_to_longtime(time()),
						$this->db->escape($this->checked->neuemail)
					);
					$this->db->query($sql);
					//Srprachtabellen Antwortmail
					$sql = sprintf("INSERT INTO %s SET lookup_id = '%d',
			content = '%d',
			lang_id = '%d'",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($mv_id)
						. "_lang_4",

						$this->db->escape($mv_content_insert_id),
						$this->db->escape($antwortmail),
						$this->db->escape($sprache['mv_lang_id'])
					);
					$this->db->query($sql);
					//Srprachtabellen Newsletter
					$sql = sprintf("INSERT INTO %s SET lookup_id = '%d',
			content = '%d',
			lang_id = '%d'",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($mv_id)
						. "_lang_5",

						$this->db->escape($mv_content_insert_id),
						$this->db->escape($newsletterok),
						$this->db->escape($sprache['mv_lang_id'])
					);
					$this->db->query($sql);
					//Sprachtabellen Forum
					$sql = sprintf("INSERT INTO %s SET lookup_id = '%d',
			content = '%d',
			lang_id = '%d'",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($mv_id)
						. "_lang_6",

						$this->db->escape($mv_content_insert_id),
						$this->db->escape($this->checked->forum_board),
						$this->db->escape($sprache['mv_lang_id'])
					);
					$this->db->query($sql);
				}
			}
			// Metalookup Werte eintragen
			$sql = sprintf("INSERT INTO %s SET mv_meta_main_lp_meta_id = '%d',
		mv_meta_main_lp_user_id = '%d',
		mv_meta_main_lp_mv_id = '%d'",
				$this->cms->tbname['papoo_mv_meta_main_lp'],
				$this->db->escape($this->meta_id_new_user),
				$this->db->escape($mv_content_insert_id),
				$this->db->escape($mv_id)
			);
			$this->db->query($sql);
			if (!empty($this->meta_id_choose_new_user)) {
				foreach ($this->meta_id_choose_new_user as $meta_id) {
					$sql = sprintf("INSERT INTO %s SET mv_meta_lp_user_id = '%d',
			mv_meta_lp_meta_id = '%d',
			mv_meta_lp_mv_id = '%d'",
						$this->cms->tbname['papoo_mv_meta_lp'],
						$this->db->escape($mv_content_insert_id),
						$this->db->escape($meta_id),
						$this->db->escape($mv_id)
					);
					$this->db->query($sql);
				}
			}
			// Emails an Systemadmins
			// wenn keine meta id mitgeschickt wurde, dann die standard nehmen
			if (empty($this->checked->mv_meta_id)) $this->checked->mv_meta_id = $this->mv_system_email_meta_id;
			$sql = sprintf("SELECT mv_meta_emails
		FROM %s
		WHERE mv_meta_id = '%d'",
				$this->cms->tbname['papoo_mv']
				. "_meta_"
				. $this->db->escape($mv_id),
				$this->db->escape($this->checked->mv_meta_id)
			);
			$emails = $this->db->get_var($sql);
			if (!empty($emails)) {
				$emails_ary = explode("\n", $emails);
				foreach ($emails_ary as $email) {
					$this->mail_it->to = $email;
					$this->mail_it->from = "";
					$this->mail_it->from_textx = $this->content->template['plugin']['mv']['anmeldung_email_emailadresse'];
					$this->mail_it->subject = $this->content->template['plugin']['mv']['anmeldung_email_betreff'];
					$this->mail_it->body = $this->content->template['plugin']['mv']['anmeldung_email_text']
						. "\n\r"
						. $neuerusername
						. "\n\r"
						. $neuemail;
					$this->mail_it->do_mail();
				}
			}
		}
	}

	/**
	 * Die Templates f�r die Gesamt�bersicht und Einzelansicht f�rs Frontend
	 * editieren
	 */
	function edit_templates()
	{
		// Falls schon was mitgeschickt wurde, dann wieder ausgeben, aber ohne <br />
		$this->content->template['mv_template_all'] = "nobr:" . $this->checked->mv_template_all;
		$this->content->template['mv_template_one'] = "nobr:" . $this->checked->mv_template_one;
		$this->content->template['mv_edit_template_flex_link_selection'] = "nobr:" . $this->checked->mv_edit_template_flex_link_selection;
		$this->content->template['mv_edit_template_flex_link_tree'] = "nobr:" . $this->checked->mv_edit_template_flex_link_tree;
		$this->content->template['mv_id'] = $this->checked->mv_id;
		$this->content->template['mv_template_plain'] = "nobr:";
		$this->content->template['mv_link'] = $_SERVER['PHP_SELF']
			. "?menuid="
			. $this->checked->menuid
			. "&template="
			. $this->checked->template
			. "&amp;mv_id="
			. $this->checked->mv_id;
		// �nderungen speichern wenn auf "eintragen" gedr�ckt wurde
		if ($this->checked->mv_submit == $this->content->template['plugin']['mv']['Eintragen']) {
			$sql = sprintf("UPDATE %s SET template_content_all = '%s',
                template_content_one = '%s',
                template_content_flex_link_selection = '%s',
                template_content_flex_link_tree = '%s'
                WHERE lang_id = '%d'
                AND meta_id = '%d'",
				$this->cms->tbname['papoo_mv']
				. "_template_"
				. $this->db->escape($this->checked->mv_id),
				$this->db->escape($this->checked->mv_template_all),
				$this->db->escape($this->checked->mv_template_one),
				$this->db->escape($this->checked->mv_edit_template_flex_link_selection),
				$this->db->escape($this->checked->mv_edit_template_flex_link_tree),
				$this->db->escape($this->cms->lang_back_content_id),
				$this->db->escape($this->meta_gruppe)
			);
			$this->db->query($sql);
		}
		// hole die Templates aus der Datenbank
		$sql = sprintf("SELECT * FROM %s
            WHERE lang_id = '%d'
            AND meta_id = '%d'
            AND detail_id = '%d'",
			$this->cms->tbname['papoo_mv']
			. "_template_"
			. $this->db->escape($this->checked->mv_id),
			$this->db->escape($this->cms->lang_back_content_id),
			$this->db->escape($this->meta_gruppe),
			1
		);
		$result = $this->db->get_results($sql);
		$sql = sprintf("SELECT * FROM %s
            WHERE lang_id = '%d'
            AND meta_id = '%d'
            AND detail_id = '1'",
			$this->cms->tbname['papoo_mv']
			. "_template_"
			. $this->db->escape($this->checked->mv_id),
			$this->db->escape($this->cms->lang_back_content_id),
			$this->db->escape($this->meta_gruppe),
			1
		);
		$temp_all_detail_1 = $this->db->get_results($sql);
		$this->content->template['mv_template_all'] = "nobr:" . $temp_all_detail_1[0]->template_content_all;
		$this->content->template['mv_edit_template_flex_link_selection'] = "nobr:" . $temp_all_detail_1[0]->template_content_flex_link_selection;
		$this->content->template['mv_edit_template_flex_link_tree'] = "nobr:" . $temp_all_detail_1[0]->template_content_flex_link_tree;
		$this->content->template['mv_template_one'] = "nobr:" . $result[0]->template_content_one;
		// hole die Feldernamen und IDs f�r diese Verwaltung aus der Datenbank und nur die ein H�kchen bei Front list haben
		$sql = sprintf("SELECT * FROM %s, %s
            WHERE mvcform_form_id ='%d'
            AND mvcform_aktiv = 1
            AND mvcform_lang_id = mvcform_id
            AND mvcform_lang_lang = '%d'
            AND mvcform_lang_meta_id = '%d'
            AND mvcform_meta_id = '%d'
            GROUP BY mvcform_id",
			$this->cms->tbname['papoo_mvcform'],
			$this->cms->tbname['papoo_mvcform_lang'],
			$this->db->escape($this->checked->mv_id),
			$this->db->escape($this->cms->lang_id),
			$this->db->escape($this->meta_gruppe),
			$this->db->escape($this->meta_gruppe)
		);
		$result = $this->db->get_results($sql);

		// Feature: Alle mv_content_* Felder eines Datensatzes sollen mit in den Platzhaltern aufgenommen werden
		// @see TODO#4739
		$this->content->template['mv_template_plain'] .= "#id#\n";
		$this->content->template['mv_template_plain'] .= "#owner#\n";
		$this->content->template['mv_template_plain'] .= "#userid#\n";
		$this->content->template['mv_template_plain'] .= "#edit_date#\n";
		$this->content->template['mv_template_plain'] .= "#create_owner#\n";
		$this->content->template['mv_template_plain'] .= "#edit_user#\n";

		// wenn es Felder gibt, dann gib pro Feld "Feldnamen in Backendsprache: #Feldname_Feldid#" aus
		//$this->user_darf_mv_schreiben
		if (!empty($result)) {
			foreach ($result as $row) {
				$this->content->template['mv_template_plain'] .= $row->mvcform_label . ": #" . $row->mvcform_name . "_" . $row->mvcform_id . "#\n";
			}
		}
	}

	/**
	 * Sprachtabellen f�r die schneller Suche updaten
	 */
	function update_lang_search_tabs()
	{
		return;
		// Sprachtabellen f�r die schnellere Suche
		$sql = sprintf("SELECT mv_lang_id FROM %s", $this->cms->tbname['papoo_mv_name_language']);
		$sprachen = $this->db->get_results($sql);

		if (!empty($sprachen)) {
			foreach ($sprachen as $sprache) {
				// alte Werte l�schen
				$sql = sprintf("DELETE FROM %s",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_search_"
					. $this->db->escape($sprache->mv_lang_id)
				);
				$this->db->query($sql);
				// neue Werte einf�gen
				$sql = sprintf("SELECT * FROM %s",
					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_search_"
					. $this->db->escape($sprache->mv_lang_id)
				);
				$result = $this->db->get_results($sql);
				// die Eintr�ge durchloopen
				foreach ($result as $row) {
					$insert_rows = "";
					foreach ($row as $key => $value) {
						// die id zwischenspeichern f�r multiselect look up tabellen
						if ($key == "mv_content_id") $mv_content_id = $value;
						list($find_name, $find_id) = $this->get_feld_name($key);
						// was f�r eine Art von Feld ist es?
						$sql = sprintf("SELECT mvcform_type FROM %s
			    WHERE mvcform_id='%s'
			    AND mvcform_form_id='%s'", $this->cms->tbname['papoo_mvcform'],
							$this->db->escape($find_id), $this->db->escape($this->checked->mv_id));
						$mvcform_type = $this->db->get_var($sql);

						if ($key != "userid" && $key != "mv_content_owner" && $key != "mv_content_id"
							&& $key != "mv_content_userid" && $key != "mv_content_sperre"
							&& $key != 'mv_content_search'
							&& $key != "mv_content_create_date" && $key != "mv_content_create_owner"
							&& $key != "mv_content_edit_date" && $key != "mv_content_edit_user") {
							switch ($mvcform_type) {
								// ??? pre_select ???
								//f�r das Multiselect Feld die Werte aus der entsprechenden Look Up Tabelle holen
								case "multiselect":
									$value = $this->get_lp_wert_search_tabs($find_id, $find_name, $mv_content_id, $sprache->mv_lang_id);
									break;
								// wenn Select oder radio Feld dann Werte aus der Lookup Tabelle holen
								case "select":
									$value = $this->get_lp_wert_search_tabs($find_id, $find_name, $mv_content_id, $sprache->mv_lang_id);
									break;
								case "radio":
									$value = $this->get_lp_wert_search_tabs($find_id, $find_name, $mv_content_id, $sprache->mv_lang_id);
									break;
								case "check":
									$value = $this->get_lp_wert_search_tabs($find_id, $find_name, $mv_content_id, $sprache->mv_lang_id);
									break;
								default:
									$value = $this->get_lp_wert_search_tabs($find_id, $find_name, $mv_content_id, $sprache->mv_lang_id);
									break;
							}
						}
						// die Eintr�ge notieren
						$insert_rows .= $key . "='" . $this->db->escape($value) . "', ";
					}

					$insert_rows = substr($insert_rows, 0, -2);
					// Komplette row in Sprachtabelle einf�gen
					$sql = sprintf("INSERT INTO %s
			SET %s", $this->cms->tbname['papoo_mv'] . "_content_" . $this->db->escape($this->checked->mv_id)
						. "_search_" . $this->db->escape($sprache->mv_lang_id), $insert_rows);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * Liefert den Wert f�r mv_content_id aus der Lang Tabelle
	 **/
	function get_label_ms($id, $lookup_id, $lang_id)
	{
		$sql = sprintf("SELECT content FROM %s
	    WHERE lookup_id='%d'
	    AND lang_id='%d'",
			$this->cms->tbname['papoo_mv'] . "_content_" . $this->db->escape($this->checked->mv_id) . "_lang_"
			. $this->db->escape($id), $this->db->escape($lookup_id), $this->db->escape($lang_id));
		$lang_wert = $this->db->get_var($sql);
		return ($lang_wert);
	}

	/**
	 * Liefert den Wert f�r content aus der search Tabelle
	 **/
	function get_lp_wert_search_tabs($id, $name, $mv_content_id, $lang_id)
	{
		$field_name = $name . "_" . $id;
		$sql = sprintf("SELECT %s FROM %s
	    WHERE mv_content_id = '%d'",
			$field_name,

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_search_"
			. $this->db->escape($lang_id),

			$this->db->escape($mv_content_id)
		);
		return $this->db->get_var($sql);
	}

	/**
	 * Macht eine Felder Rechtetabelle f�r eine Verwaltung
	 * + called by make_entry.php
	 **/
	function make_field_rights()
	{
		$sql = sprintf("CREATE TABLE %s (
	    `field_id` int(11) default NULL,
	    `user_id` int(11) default NULL,
	    `group_id` int(11) default NULL,
	    `group_read` int(11) DEFAULT '0',
	    `group_write` int(11) DEFAULT '0',
	    `field_right_meta_id` int(11) DEFAULT '0',
	    KEY `field_id` (`field_id`)
	)
	ENGINE = MyISAM
	DEFAULT CHARSET = utf8;",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_field_rights"
		);
		$this->db->query($sql);
	}

	/**
	 * Macht eine Gruppen (Feld hat einen bestimmten Wert, dann darf die Gruppe
	 * lesen/schreiben) Rechtetabelle f�r eine Verwaltung called by
	 * make_entry.php
	 **/
	function make_group_rights()
	{
		$sql = sprintf("CREATE TABLE %s (
	    `id` int(11) NOT NULL auto_increment,
	    `field_id` int(11) default NULL,
	    `group_id` int(11) default NULL,
	    `field_value` text,
	    `group_read` int(11) DEFAULT '0',
	    `group_write` int(11) DEFAULT '0',
	    `group_right_meta_id` int(11) DEFAULT '0',
	    PRIMARY KEY (`id`)
	)
	ENGINE = MyISAM
	DEFAULT CHARSET = utf8
	AUTO_INCREMENT = 1;",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_group_rights"
		);
		$this->db->query($sql);
	}

	/**
	 * Macht eine Content Rechtetabelle f�r eine Verwaltung
	 * called by make_entry.php
	 **/
	function make_content_rights()
	{
		$sql = sprintf("CREATE TABLE %s (
	    `mv_content_id` int(11) default NULL,
	    `user_id` int(11) default NULL,
	    `group_id` int(11) default NULL,
	    `group_read` int(11) DEFAULT '0',
	    `group_write` int(11) DEFAULT '0',
	    `is_listed` int(11) DEFAULT '0',
	    `content_right_meta_id` int(11) DEFAULT '0',
	    KEY `mv_content_id` (`mv_content_id`)
	)
	ENGINE = MyISAM
	DEFAULT CHARSET = utf8;",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_content_rights"
		);
		$this->db->query($sql);
	}

	/**
	 * �ndert die Rechte f�r ein Feld in einer Verwaltung
	 **/
	function change_field_rights($field_id, $user_id, $group_id, $group_read, $group_write)
	{
		// alte Rechte des Users $user_id f�r das Feld $field_id erstmal l�schen
		if ($userid != "") {
			$sql = sprintf("DELETE FROM %s
		WHERE user_id = '%d'
		AND field_id = '%d'",
				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_field_rights",
				$this->db->escape($user_id),
				$this->db->escape($field_id)
			);
			$this->db->query($sql);
		} else // oder alte Rechte der Gruppe $group_id f�r das Feld $field_id erstmal l�schen
		{
			$sql = sprintf("DELETE FROM %s
		WHERE group_id = '%d'
		AND field_right_meta_id = '%d'
		AND field_id = '%d'",
				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_field_rights",
				$this->db->escape($group_id),
				$this->db->escape($this->meta_gruppe),
				$this->db->escape($field_id)
			);
			$this->db->query($sql);
		}
		// neue Werte einsetzen
		$sql = sprintf("INSERT INTO %s
	    SET field_id = '%d',
	    user_id = '%d',
	    group_id = '%d',
	    group_read = '%d',
	    group_write = '%d',
	    field_right_meta_id = '%d' ",
			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_field_rights",
			$this->db->escape($field_id),
			$this->db->escape($user_id),
			$this->db->escape($group_id),
			$this->db->escape($group_read),
			$this->db->escape($group_write),
			$this->db->escape($this->meta_gruppe)
		);
		$this->db->query($sql);
	}

	/**
	 * �ndert die Rechte f�r ein Feld Owner in einer Verwaltung
	 **/
	function change_group_rights($id, $field_id, $wert, $group_id, $group_read, $group_write)
	{
		if ($id != "") {
			// Werte updaten
			$sql = sprintf("UPDATE %s SET field_id = '%d',
		field_value = '%d',
		group_id = '%d',
		group_read = '%d',
		group_write = '%d',
		group_right_meta_id = '%d'
		WHERE id = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_group_rights",

				$this->db->escape($field_id),
				$this->db->escape($wert),
				$this->db->escape($group_id),
				$this->db->escape($group_read),
				$this->db->escape($group_write),
				$this->db->escape($this->meta_gruppe),
				$this->db->escape($id)
			);
			$this->db->query($sql);
		} else {
			// neue Werte einsetzen
			$sql = sprintf("INSERT INTO %s SET field_id = '%d',
		field_value = '%d',
		group_id = '%d',
		group_read = '%d',
		group_write = '%d',
		group_right_meta_id = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_group_rights",

				$this->db->escape($field_id),
				$this->db->escape($wert),
				$this->db->escape($group_id),
				$this->db->escape($group_read),
				$this->db->escape($group_write),
				$this->db->escape($this->meta_gruppe)
			);
			$this->db->query($sql);
		}
	}

	/**
	 * �ndert die Rechte f�r einen Content Eintrag in einer Verwaltung
	 **/
	function change_content_rights($user_id, $group_id, $group_read, $group_write)
	{
		// alte Rechte des Users $user_id f�r den Eintrag $mv_content_id erstmal l�schen
		if ($userid != "") {
			$sql = sprintf("DELETE FROM %s
		WHERE user_id = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_content_rights",

				$this->db->escape($user_id)
			);
			$this->db->query($sql);
		} else // oder alte Rechte der Gruppe $group_id f�r den Eintrag $mv_content_id erstmal l�schen
		{
			$sql = sprintf("DELETE FROM %s
		WHERE group_id = '%d'
		AND content_right_meta_id = '%d' ",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_content_rights",

				$this->db->escape($group_id),
				$this->db->escape($this->meta_gruppe)
			);
			$this->db->query($sql);
		}
		// neue Werte einsetzen
		$sql = sprintf("INSERT INTO %s
	    SET user_id = '%d',
	    group_id = '%d',
	    group_read = '%d',
	    group_write = '%d',
	    content_right_meta_id = '%d'",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_content_rights",

			$this->db->escape($user_id),
			$this->db->escape($group_id),
			$this->db->escape($group_read),
			$this->db->escape($group_write),
			$this->db->escape($this->meta_gruppe)
		);
		$this->db->query($sql);
	}

	/**
	 * Die Rechte in einer Verwaltung editieren
	 **/
	function edit_rights()
	{
		// F�rs Template den Link vorbereiten
		$link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=";
		$this->content->template['link'] = $link;
		$this->content->template['mv_id'] = $this->db->escape($this->checked->mv_id);
		$this->finde_die_art_der_verwaltung_heraus();
		$this->content->template['mv_art_rechte'] = $this->mv_art;
		$this->content->template['right_id'] = $this->db->escape($this->checked->right_id);
	}

	/**
	 * Die Rechte f�r Content Eintrag in einer Verwaltung editieren
	 **/
	function edit_content_rights()
	{
		// �nderungen speichern, wenn auf �ndern gedr�ckt wurde
		if ($this->checked->submit_rights != "") {
			// holt die Gruppen komplett aus der MV Rechte Tabelle
			$sql = sprintf("SELECT * FROM %s
		WHERE group_id <> '0'
		AND content_right_meta_id = '%d'
		OR group_id <> ''
		AND content_right_meta_id = '%d'
		ORDER BY group_id",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_content_rights",

				$this->db->escape($this->meta_gruppe),
				$this->db->escape($this->meta_gruppe)
			);
			$gruppen = $this->db->get_results($sql, ARRAY_A);
			if (!empty($gruppen)) {
				foreach ($gruppen as $gruppe) {
					$group_read = 'group_read_' . $gruppe['group_id'];
					$group_write = 'group_write_' . $gruppe['group_id'];
					$this->change_content_rights('', $gruppe['group_id'], $this->checked->$group_read, $this->checked->$group_write);
				}
			}
		}
		// holt die Papoo-Gruppen IDs aus der Papoo Gruppen Rechte Tabelle
		$sql = sprintf("SELECT COUNT(gruppeid) FROM %s
	    ORDER BY gruppeid",
			$this->cms->tbname['papoo_gruppe']
		);
		$gruppen_ids_neu = $this->db->get_var($sql);
		// holt die Gruppen IDs aus der MV Rechte Tabelle
		$sql = sprintf("SELECT COUNT(group_id) FROM %s
	    WHERE group_id <> '0'
	    AND content_right_meta_id = '%d'
	    OR group_id <> ''
	    AND content_right_meta_id = '%d'
	    ORDER BY group_id",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_content_rights",

			$this->db->escape($this->meta_gruppe),
			$this->db->escape($this->meta_gruppe)
		);
		$gruppen_ids_alt = $this->db->get_var($sql);
		// holt die Gruppen komplett aus der MV Rechte Tabelle
		if ($gruppen_ids_alt) {
			$sql = sprintf("SELECT * FROM %s
		WHERE group_id <> '0'
		AND content_right_meta_id = '%d'
		OR group_id <> ''
		AND content_right_meta_id = '%d'
		ORDER BY group_id",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_content_rights",

				$this->db->escape($this->meta_gruppe),
				$this->db->escape($this->meta_gruppe)
			);
			$gruppen_alt = $this->db->get_results($sql, ARRAY_A); // brauchen wir noch, falls sich was ge�ndert haben sollte
		}
		$gruppen_output = $gruppen_alt;
		// wenn sich was ver�ndert hat, dann bei der Ausgabe anpassen
		if ($gruppen_ids_neu != $gruppen_ids_alt) {
			$gruppen_save = array();
			// holt die Papoo-Gruppen aus der Papoo Gruppen Rechte Tabelle
			$sql = sprintf("SELECT gruppeid,
		gruppenname,
		gruppen_beschreibung
		FROM %s
		ORDER BY gruppeid",
				$this->cms->tbname['papoo_gruppe']
			);
			$gruppen_neu = $this->db->get_results($sql, ARRAY_A);
			foreach ($gruppen_neu as $gruppe_neu) {
				$treffer = "";
				// loope alte Groupenliste durch
				if (!empty($gruppen_alt)) {
					foreach ($gruppen_alt as $gruppe_alt) {
						// wenn es die Gruppe schon gab, dann alte Werte �bernehmen
						if ($gruppe_neu['gruppeid'] == $gruppe_alt['group_id']) {
							$gruppen_save[] = array(
								'user_id' => '',
								'group_id' => $gruppe_alt['group_id'],
								'group_read' => $gruppe_alt['group_read'],
								'group_write' => $gruppe_alt['group_write']
							);
							$treffer = "bingo";
						}
					}
				}
				// ist es eine neue Gruppe
				if ($treffer != "bingo") {
					// dann neue Werte hinzuf�gen
					$gruppen_save[] = array(
						'user_id' => '',
						'group_id' => $gruppe_neu['gruppeid'],
						'group_read' => '0',
						'group_write' => '0'
					);
				}
			}
			// die Liste mit allen �nderungen durchgehen
			foreach ($gruppen_save as $gruppe) {
				$this->change_content_rights($gruppe['user_id'], $gruppe['group_id'], $gruppe['group_read'], $gruppe['group_write']);
			}
		}
		$sql = sprintf("SELECT T1.*,
	    T2.gruppenname
	    FROM %s T1
	    INNER JOIN %s T2 ON (T1.group_id = T2.gruppeid)
	    WHERE group_id <> '0'
	    AND group_id <> ''
	    AND content_right_meta_id = '%d'
	    ORDER BY group_id",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_content_rights",

			$this->cms->tbname['papoo_gruppe'],
			$this->db->escape($this->meta_gruppe)
		);
		$this->content->template['liste_rights'] = $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Die Rechte zum K�ndigen von Mitgliedern einer MV bearbeiten
	 **/
	function edit_special_rights($mode = 1)
	{
		if (!($mode == 1 or $mode == 2)) $mode = 1;
		// �nderungen speichern, wenn auf �ndern gedr�ckt wurde
		if ($this->checked->submit_rights != "") {
			// alle Eintr�ge l�schen
			$sql = sprintf("DELETE FROM %s",
				$this->cms->tbname['papoo_mv']
				. "_special_rights"
				. $mode
			);
			$this->db->query($sql);
			if (count($this->checked->group_right)) // es wurden Gruppen ausgew�hlt -> Speichern
			{
				foreach ($this->checked->group_right as $key => $value) {
					$sql = sprintf("INSERT INTO %s
			SET `group` = '%d'",
						$this->cms->tbname['papoo_mv']
						. "_special_rights"
						. $mode,
						$this->db->escape($value)
					);
					$this->db->query($sql);
				}
			}
		}
		$sql = sprintf("SELECT T1.*,
	    T2.gruppenname,
	    T2.gruppeid
	    FROM %s T1
	    RIGHT JOIN %s T2 ON (T1.group = T2.gruppeid)
	    ORDER BY gruppenname",

			$this->cms->tbname['papoo_mv']
			. "_special_rights"
			. $mode,

			$this->cms->tbname['papoo_gruppe']
		);
		$this->content->template['liste_rights'] = $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Die Rechte f�r Gruppe editieren
	 **/
	function edit_group_rights()
	{
		// Eintrag l�schen, wenn auf l�schen gedr�ckt wurde
		if ($this->checked->submit_del != "") {
			// Eintrag nach id l�schen und neu laden
			$sql = sprintf("DELETE FROM %s
		WHERE id = '%s'
		AND group_right_meta_id = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_group_rights",

				$this->db->escape($this->checked->id),
				$this->db->escape($this->meta_gruppe)
			);
			$this->db->query($sql);
		}
		// �nderungen speichern, wenn auf �ndern gedr�ckt wurde
		if ($this->checked->submit_rights != "") {
			// holt die Werte f�r die Verwaltung aus der Tabelle
			$sql = sprintf("SELECT * FROM %s, %s, %s
		WHERE field_id = mvcform_lang_id
		AND mvcform_lang_lang = '%d'
		AND group_id = gruppeid
		AND mvcform_lang_meta_id = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_group_rights",

				$this->cms->tbname['papoo_gruppe'],

				$this->cms->tbname['papoo_mvcform_lang'],

				$this->db->escape($this->cms->lang_id),
				$this->db->escape($this->meta_gruppe)
			);
			$rechte_gruppen = $this->db->get_results($sql, ARRAY_A);
			if (!empty($rechte_gruppen)) {
				foreach ($rechte_gruppen as $gruppe) {
					$group_read = "group_read_" . $gruppe['id'];
					$group_write = "group_write_" . $gruppe['id'];
					$this->change_group_rights($gruppe['id'],
						$gruppe['field_id'],
						$gruppe['field_value'],
						$gruppe['group_id'],
						$this->checked->$group_read,
						$this->checked->$group_write
					);
				}
			}
		}
		// �nderungen speichern, wenn auf �ndern gedr�ckt wurde
		if ($this->checked->submit_new_group != "") {
			if ($this->checked->field_name != ""
				&& $this->checked->field_value != "") {
				$sql = sprintf("SELECT id FROM %s
		    WHERE field_id = '%d'
		    AND group_id = '%d'
		    AND field_value = '%d'
		    AND group_right_meta_id = '%d'",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_group_rights",

					$this->db->escape($this->checked->field_name),
					$this->db->escape($this->checked->group_name),
					$this->db->escape($this->checked->field_value),
					$this->db->escape($this->meta_gruppe)
				);
				$id = $this->db->get_results($sql, ARRAY_A);
				$id = $id[0]['id'] ? $id[0]['id'] : "";
				$this->change_group_rights($id,
					$this->checked->field_name,
					$this->checked->field_value,
					$this->checked->group_name,
					$this->checked->group_read,
					$this->checked->group_write
				);
			}
		}
		// holt die Gruppen aus der Papoo Gruppen Rechte Tabelle
		$sql = sprintf("SELECT gruppeid,
	    gruppenname,
	    gruppen_beschreibung
	    FROM %s
	    ORDER BY gruppeid",
			$this->cms->tbname['papoo_gruppe']);
		$gruppen = $this->db->get_results($sql, ARRAY_A);
		// holt Feldernamen und IDs aus der Tabelle
		$sql = sprintf("SELECT mvcform_id,
	    mvcform_label
	    FROM %s, %s
	    WHERE mvcform_form_id = '%d'
	    AND mvcform_aktiv = 1
	    AND mvcform_lang_id = mvcform_id
	    AND mvcform_lang_lang = '%d'
	    AND mvcform_lang_meta_id = '%d'
	    AND mvcform_meta_id = mvcform_lang_meta_id
	    AND (mvcform_type = 'select'
	    OR mvcform_type = 'multiselect'
	    OR mvcform_type = 'radio'
	    OR mvcform_type = 'check')",
			$this->cms->tbname['papoo_mvcform'],
			$this->cms->tbname['papoo_mvcform_lang'],
			$this->db->escape($this->checked->mv_id),
			$this->db->escape($this->cms->lang_id),
			$this->db->escape($this->meta_gruppe)
		);
		$felder = $this->db->get_results($sql, ARRAY_A);
		$felder_werte = array();
		// holt die Werte f�r die Felder
		if (!empty($felder)) {
			foreach ($felder as $feld) {
				$sql = sprintf("SELECT content,
		    lookup_id,
		    order_id
		    FROM %s
		    WHERE lang_id = '%d'
		    AND lookup_id <> '0'
		    ORDER BY order_id",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_lang_"
					. $this->db->escape($feld['mvcform_id']),

					$this->db->escape($this->cms->lang_id)
				);
				$feld_wert = $this->db->get_results($sql, ARRAY_A);
				$felder_werte[$feld['mvcform_id']] = $feld_wert;
			}
		}
		// speichert den ganzen Kram in Hidden Feldern ab, damit das Javascript diese dann auswerten kann
		$hidden_field = "nobr:";
		if (!empty($felder_werte)) {
			foreach ($felder_werte as $feld_id => $werte) {
				if (!empty($werte)) {
					$js_array_name = $js_array_lookup_id = $comma = "";
					// wenn es mehrere m�gliche Werte f�r das Feld gibt
					foreach ($werte as $options) {
						// bereits zugeornete Werte nicht erneut in die Selectbox
						#if (count($result))
						#{
						#	foreach ($result AS $key => $value) { if ($options['lookup_id'] == $value['field_value']) continue 2; }
						#}
						// Daten f�r Javascript aufbereiten
						if ($options['content'] != "") {
							if ($comma) {
								$js_array_name .= $comma;
								$js_array_lookup_id .= $comma;
							} else $comma = ",";
							$js_array_name .= $options['content'];
							$js_array_lookup_id .= $options['lookup_id'];
						}
					}
					$hidden_field .= "\n"
						. '<input type="hidden" name="'
						. $feld_id
						. '_name"'
						. ' id="'
						. $feld_id
						. '_name"'
						. ' value="'
						. $js_array_name
						. '">';
					$hidden_field .= "\n"
						. '<input type="hidden" name="'
						. $feld_id
						. '_lookup_id"'
						. ' id="'
						. $feld_id
						. '_lookup_id"'
						. ' value="'
						. $js_array_lookup_id
						. '">';
				}
			}
		}
		// holt die Werte f�r die Verwaltung aus der Tabelle
		$sql = sprintf("SELECT *
	    FROM %s, %s, %s
	    WHERE field_id = mvcform_lang_id
	    AND mvcform_lang_lang = '%d'
	    AND group_id = gruppeid
	    AND mvcform_lang_meta_id = '%d'
	    AND group_right_meta_id = '%d'",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($this->checked->mv_id)
			. "_group_rights",

			$this->cms->tbname['papoo_gruppe'],
			$this->cms->tbname['papoo_mvcform_lang'],
			$this->db->escape($this->cms->lang_id),
			$this->db->escape($this->meta_gruppe),
			$this->db->escape($this->meta_gruppe)
		);
		$rechte_gruppen = $this->db->get_results($sql, ARRAY_A);
		$counter = 0;
		if (!empty($rechte_gruppen)) {
			// loope die Gruppen durch und hole daf�r Feldwert Labels aus der entsprechenden Lang Tabelle
			foreach ($rechte_gruppen as $rechte_gruppe) {
				$sql = sprintf("SELECT content
		    FROM %s
		    WHERE lookup_id = '%d'
		    AND lang_id = '%d'",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_lang_"
					. $this->db->escape($rechte_gruppe['field_id']),

					$this->db->escape($rechte_gruppe['field_value']),
					$this->db->escape($this->cms->lang_id)
				);
				$feld_label = $this->db->get_var($sql);
				$rechte_gruppen[$counter]['field_label'] = $feld_label;
				// wenn es l�nger als 20 Zeichen ist, dann abschneiden und ... dahinter
				if (strlen($feld_label) > 20) $rechte_gruppen[$counter]['field_label'] = substr($feld_label, 0, 20) . "...";
				$counter++;
			}
		}
		// ans Template weitergeben
		$link = $_SERVER['PHP_SELF']
			. "?menuid="
			. $this->checked->menuid
			. "&template="
			. $this->checked->template
			. "&mv_id="
			. $this->checked->mv_id;
		$this->content->template['link'] = $link;
		$this->content->template['liste_rights'] = $rechte_gruppen;
		$this->content->template['gruppen'] = $gruppen;
		$this->content->template['felder'] = $felder;
		$this->content->template['felder_werte'] = $felder_werte;
		$this->content->template['hidden_field'] = $hidden_field;
		$this->content->template['mv_id'] = $this->db->escape($this->checked->mv_id);
	}

	/**
	 * Die Rechte f�r das Feld in einer Verwaltung editieren
	 * called by mv.php via edit_field_rights.html
	 **/
	function edit_field_rights()
	{
		$this->content->template['mv_id'] = $this->checked->mv_id;
		$this->content->template['link'] = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template;
		// �nderungen speichern, wenn auf �ndern gedr�ckt wurde
		if ($this->checked->submit_rights != "") {
			// holt die Gruppen komplett aus der MV Rechte Tabelle
			$sql = sprintf("SELECT * FROM %s
		WHERE field_id = '%d'
		AND field_right_meta_id = '%d'
		ORDER BY group_id",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_field_rights",

				$this->db->escape($this->checked->feld_id),
				$this->db->escape($this->meta_gruppe)
			);
			$gruppen = $this->db->get_results($sql, ARRAY_A);
			if (!empty($gruppen)) {
				foreach ($gruppen as $gruppe) {
					$group_read = 'group_read_' . $gruppe['group_id'];
					$group_write = 'group_write_' . $gruppe['group_id'];
					$this->change_field_rights($gruppe['field_id'], '', $gruppe['group_id'], $this->checked->$group_read, $this->checked->$group_write);
				}
			}
		}
		// die Felder
		$sql = sprintf("SELECT mvcform_id,
	    mvcform_label
	    FROM %s, %s
	    WHERE mvcform_form_id = '%d'
	    AND mvcform_aktiv = 1
	    AND mvcform_lang_id=mvcform_id
	    AND mvcform_lang_lang = '%d'
	    AND mvcform_lang_meta_id = '%d'
	    AND mvcform_meta_id = '%d'
	    GROUP BY mvcform_id",
			$this->cms->tbname['papoo_mvcform'],
			$this->cms->tbname['papoo_mvcform_lang'],
			$this->db->escape($this->checked->mv_id),
			$this->db->escape($this->cms->lang_id),
			$this->db->escape($this->meta_gruppe),
			$this->db->escape($this->meta_gruppe)
		);
		$felder = $this->db->get_results($sql, ARRAY_A);
		if (count($felder)) {
			// added by khmweb 29.11.09 Anzeige der Felder, f�r die noch keine Rechte vorgegeben wurden
			foreach ($felder as $key => $value) {
				$sql = sprintf("SELECT * FROM %s
		    WHERE field_id = '%d'
		    AND field_right_meta_id = '%d'
		    AND (group_read = 1 OR group_write = 1)",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_field_rights",

					$felder[$key]['mvcform_id'],
					$this->db->escape($this->meta_gruppe)
				);
				$rechte = $this->db->get_results($sql, ARRAY_A);
				$felder[$key]['keine_rechte'] = count($rechte) ? 0 : 1;
			}
			// ans Template geben
			$this->content->template['liste_felder'] = $felder;
		}
		// holt die Felder f�r die Verwaltung aus der Datenbank
		if ($this->checked->feld_anzeigen == "") {

			// die Admingruppen, die aufgelisted werden
			$sql = sprintf("SELECT * FROM %s
		WHERE is_listed = '1'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_content_rights"
			);
			$admin_gruppen = $this->db->get_results($sql, ARRAY_A);
			// khmweb: Wozu dient das? Ergebnis der Abfrage wird nicht referenziert
			/*if (!empty($admin_gruppen))
			{
				foreach($admin_gruppen as $admin_gruppe)
				{
					$sql = sprintf("SELECT field_id,
											group_read,
											group_write
											FROM %s
											WHERE group_id = '%d'
											AND field_right_meta_id = '%d'
											ORDER BY field_id",

											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($this->checked->mv_id)
											. "_field_rights",
											$this->db->escape($admin_gruppe['group_id']),
											$this->db->escape($this->meta_gruppe)
									);
					$admin_felder = $this->db->get_results($sql, ARRAY_A);
				}
			}*/
			$this->content->template['admin_gruppen'] = $admin_gruppen;
			$link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template;
			$this->content->template['link'] = $link;
			$this->content->template['mv_id'] = $this->db->escape($this->checked->mv_id);
		} // wenn aus Feld ausgew�hlt wurde dann die Rechtetabelle f�r das Feld ausgeben
		else {
			// holt die Gruppen IDs aus der Papoo Gruppen Rechte Tabelle
			$sql = sprintf("SELECT COUNT(gruppeid) FROM %s
		ORDER BY gruppeid",
				$this->cms->tbname['papoo_gruppe']
			);
			$gruppen_ids_neu = $this->db->get_var($sql);
			// holt die Gruppen IDs aus der MV Rechte Tabelle
			$sql = sprintf("SELECT COUNT(group_id) FROM %s
		WHERE field_id = '%d'
		AND field_right_meta_id = '%d'
		ORDER BY group_id",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_field_rights",

				$this->db->escape($this->checked->feld_id),
				$this->db->escape($this->meta_gruppe)
			);
			$gruppen_ids_alt = $this->db->get_var($sql);
			// holt die Gruppen komplett aus der MV Rechte Tabelle
			$sql = sprintf("SELECT * FROM %s
		WHERE field_id = '%d'
		AND field_right_meta_id = '%d'
		ORDER BY group_id",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_field_rights",

				$this->db->escape($this->checked->feld_id),
				$this->db->escape($this->meta_gruppe)
			);
			$gruppen_alt = $this->db->get_results($sql, ARRAY_A);
			$gruppen_output = $gruppen_alt;
			// wenn sich was ver�ndert hat, dann bei der Ausgabe anpassen
			if ($gruppen_ids_neu != $gruppen_ids_alt) {
				$gruppen_save = array();
				// holt die Gruppen aus der Papoo Gruppen Rechte Tabelle
				$sql = sprintf("SELECT gruppeid,
		    gruppenname,
		    gruppen_beschreibung
		    FROM %s
		    ORDER BY gruppeid",
					$this->cms->tbname['papoo_gruppe']
				);
				$gruppen_neu = $this->db->get_results($sql, ARRAY_A);
				foreach ($gruppen_neu as $gruppe_neu) {
					$treffer = "";
					// loope alte Gruppenliste durch
					if (!empty($gruppen_alt)) {
						foreach ($gruppen_alt as $gruppe_alt) {
							// wenn es die Gruppe schon gab, dann alte Werte �bernehmen
							if ($gruppe_neu['gruppeid'] == $gruppe_alt['group_id']) {
								$gruppen_save[] = array(field_id => $gruppe_alt['field_id'],
									'user_id' => '',
									'group_id' => $gruppe_alt['group_id'],
									'group_read' => $gruppe_alt['group_read'],
									'group_write' => $gruppe_alt['group_write']
								);
								$treffer = "bingo";
							}
						}
					}
					// ist es eine neue Gruppe
					if ($treffer != "bingo") {
						// dann neue Werte hinzuf�gen
						$gruppen_save[] = array(
							'field_id' => $this->db->escape($this->checked->feld_id),
							'user_id' => '',
							'group_id' => $gruppe_neu['gruppeid'],
							'group_read' => '0',
							'group_write' => '0'
						);
					}
				}
				// so die Liste mit allen �nderungen durchgehen
				foreach ($gruppen_save as $gruppe) {
					$this->change_field_rights($gruppe['field_id'], $gruppe['user_id'], $gruppe['group_id'], $gruppe['group_read'], $gruppe['group_write']);
				}
			}
			$sql = sprintf("SELECT T1.*,
		T2.gruppenname
		FROM %s T1
		INNER JOIN %s T2 ON (T1.group_id = T2.gruppeid)
		WHERE field_id = '%d'
		AND field_right_meta_id = '%d'
		ORDER BY group_id",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($this->checked->mv_id)
				. "_field_rights",

				$this->cms->tbname['papoo_gruppe'],
				$this->db->escape($this->checked->feld_id),
				$this->db->escape($this->meta_gruppe)
			);
			$this->content->template['liste_rights'] = $this->db->get_results($sql, ARRAY_A);
			// Holt den Feldnamen(Label) aus der Tabelle
			$sql = sprintf("SELECT mvcform_label
		FROM %s, %s
		WHERE mvcform_id = '%d'
		AND mvcform_form_id = '%d'
		AND mvcform_lang_id = mvcform_id
		AND mvcform_lang_lang = '%d'
		AND mvcform_lang_meta_id = '%d'",
				$this->cms->tbname['papoo_mvcform'],
				$this->cms->tbname['papoo_mvcform_lang'],
				$this->db->escape($this->checked->feld_id),
				$this->db->escape($this->checked->mv_id),
				$this->db->escape($this->cms->lang_id),
				$this->db->escape($this->meta_gruppe)
			);
			$feld_name = $this->db->get_var($sql);
			$this->content->template['feld_anzeigen'] = '1';
			$this->content->template['feld_id'] = $this->db->escape($this->checked->feld_id);
			$this->content->template['feld_name'] = $feld_name;
		}
	}

	/**
	 * Holt die Gruppen aus der Tabelle
	 **/
	function get_groups()
	{
		$sql =
			sprintf("SELECT gruppeid, gruppenname, gruppen_beschreibung FROM %s", $this->cms->tbname['papoo_gruppe']);
		$result = $this->db->get_results($sql);
		return ($result);
	}

	/**
	 * Pr�ft ob das Sql Dump f�r die Verwaltung �lter als 15 Minuten ist, wenn
	 * ja dann l�schen
	 **/
	function check_dump()
	{
		// tempor�re Dateien im Ordner /interna/templates_c/
		$handle = opendir('templates_c');

		while (false !== ($file = readdir($handle))) {
			if (preg_match("/mv_dump_/", $file)) {
				$file_date = $file_date_15 = 0;
				$file_date = filemtime('templates_c/' . $file);
				$now_date_15 = time() - (15 * 60);
				if ($file_date < $now_date_15) {
					@unlink('templates_c/' . $file);
				}
			}
		}
		closedir($handle);
	}

	/**
	 * SelectFelder neu sortieren, leere Eintr�ge l�schen (entstehen bei
	 * fehlerhaften Importdateien, oder wenn jemand leere Selectoptionen
	 * "eingibt")
	 */
	function reorder_select_felder($mv_id = "1")
	{
		// holt alle select felder f�r die ausgew�hlte Metaebene und Verwaltung aus der Datenbank
		$sql = sprintf("SELECT mvcform_id
	    FROM %s
	    WHERE mvcform_type = 'select'
	    AND mvcform_form_id = '%d'
	    AND mvcform_meta_id = '%d'",

			$this->cms->tbname['papoo_mvcform'],

			$this->db->escape($mv_id),
			$this->db->escape($this->meta_gruppe)
		);
		$select_felder = $this->db->get_results($sql, ARRAY_A);
		if (!empty($select_felder)) {
			// geht die einzelnen select Felder durch
			foreach ($select_felder as $select_feld) {
				// leere Content Eintr�ge aus der Lang Tabelle des Feldes fischen
				$sql = sprintf("SELECT lookup_id
		    FROM %s
		    WHERE content = ''",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($mv_id)
					. "_lang_"
					. $this->db->escape($select_feld['mvcform_id'])
				);
				$lookup_ids = $this->db->get_results($sql);
				// und den leeren Content Eintrag in der Lang Tabelle des Feldes l�schen
				$sql = sprintf("DELETE FROM %s
		    WHERE content = ''",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($mv_id)
					. "_lang_"
					. $this->db->escape($select_feld['mvcform_id'])
				);
				$this->db->query($sql);
				// Auswahlm�glichkeiten des Feldes rausholen
				$sql = sprintf("SELECT * FROM %s
		    ORDER BY lang_id,content ASC",

					$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($mv_id)
					. "_lang_"
					. $this->db->escape($select_feld['mvcform_id']),

					$this->db->escape($select_feld['mvcform_id'])
				);
				$auswahl = $this->db->get_results($sql, ARRAY_A);
				$i = 10;
				if ((!empty($auswahl))) {
					// Durchz�hlen und neu einsortieren
					foreach ($auswahl as $dat) {
						if ($dat['lang_id'] != $lang_old) $i = 1;
						$sql = sprintf("UPDATE %s SET order_id = '%d'
			    WHERE lookup_id = '%d'
			    AND lang_id = '%d'",

							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($mv_id)
							. "_lang_"
							. $this->db->escape($select_feld['mvcform_id']),

							$this->db->escape($i),
							$this->db->escape($dat['lookup_id']),
							$this->db->escape($dat['lang_id'])
						);
						$this->db->query($sql);
						$i = $i + 10;
						$lang_old = $dat['lang_id'];
					}
				}
			}
		}
	}

	/**
	 * mv::mv_get_lese_schreibrechte_fuer_meta_ebene()
	 * Diese Funktion bestimmt, ob Leserechte oder/und Schreibrechte
	 * bestehen in dieser Verwaltung
	 *
	 * @return void
	 */
	function mv_get_lese_schreibrechte_fuer_meta_ebene()
	{
		$this->user_darf_mv_lesen = false;
		$this->user_darf_mv_schreiben = false;
		// solange keine Verwaltung installiert ist, gibts die Tabelle noch nicht. Verhindert Fehlermeldungen nach Install.
		if ($this->meta_gruppe
			and $this->cms->tbname['papoo_mv_mpg_' . $this->db->escape($this->checked->mv_id)]) {
			$sql = sprintf("SELECT * FROM %s, %s
		WHERE mv_mpg_group_id = gruppenid
		AND userid = '%d'
		AND mv_mpg_id = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_mpg_"
				. $this->db->escape($this->checked->mv_id),

				$this->cms->tbname['papoo_lookup_ug'],
				$this->user->userid,
				$this->db->escape($this->meta_gruppe)
			);
			$result = $this->db->get_results($sql, ARRAY_A);
		}
		// Was, wenn der User zu mehreren Gruppen geh�rt?
		// Hier wird sich nur auf eine, die 1. in der DB gefundenen Rechte bezogen.
		// Leserechte bestimmen
		if (is_array($result)) {
			if (count($result)) {
				foreach ($result as $key => $value) {
					if ($result[$key]['mv_mpg_read'] == 1
						and !$this->user_darf_mv_lesen) {
						$this->user_darf_mv_lesen = true;
						$this->content->template['user_darf_lesen'] = "ok";
					}
					// Schreibrechte bestimmen
					if ($result[$key]['mv_mpg_write'] == 1
						and !$this->user_darf_mv_schreiben) {
						$this->user_darf_mv_schreiben = true;
						$this->content->template['user_darf_schreiben'] = "ok";
					}
					if ($this->user_darf_mv_schreiben
						and $this->user_darf_mv_lesen) break;
				}
			}
		}
	}

	/**
	 * Ist der User auch berechtigt unter dieser Metaeben was zu
	 * sehen/�ndern/beides?
	 **/
	// Kann das durch is_group_write_ok() ersetzt werden? Check erfolgt auf andre Art. Hier wird gecheckt,
	// ob irgendein Recht besteht (lesen und/oder schreiben). In is_group_write_ok() wird auf schreiben gepr�ft.
	function is_user_right_here()
	{
		$meta_gruppe_id = $this->meta_gruppe;
		// ist auch eine id mitgeschickt worden und wenn ja ist es auch eine Zahl
		if (!empty($meta_gruppe_id)
			&& is_numeric($meta_gruppe_id)) {
			// baut aus dem User seine Papoo Rechtegruppen IDs eine WHERE Clause zusammen
			if (!empty($this->gruppen_ids)) // Papoo Rechtegruppen IDs des Users
			{
				$mpg_group_ids = "(";
				foreach ($this->gruppen_ids as $gruppen_id) {
					$mpg_group_ids .= "mv_mpg_group_id='" . $this->db->escape($gruppen_id) . "' OR ";
				}
				$mpg_group_ids = substr($mpg_group_ids, 0, -4); // letztes ' OR ' raus
				$mpg_group_ids .= ")";
			} // wenn der User �berhaupt keine Gruppenrechte hat, dann geht nat�rlich gar nix
			else {
				$this->content->template['meta_error_kein_zutritt'] = "error";
				return false;
			}
			// hat der User mit seinen Rechtegruppen bei dieser Metaebene mit deren Rechtegruppen �bereinstimmungen?
			$sql = sprintf("SELECT COUNT(mv_mpg_group_id)
		FROM %s, %s, %s
		WHERE %s
		AND mv_mpg_id = '%d'
		AND mv_mpg_id = mv_meta_id
		AND mv_mpg_group_id = gruppeid
		ORDER BY mv_meta_group_name,
		gruppenname",

				$this->cms->tbname['papoo_mv']
				. "_mpg_"
				. $this->db->escape($this->checked->mv_id),

				$this->cms->tbname['papoo_gruppe'],

				$this->cms->tbname['papoo_mv']
				. "_meta_"
				. $this->db->escape($this->checked->mv_id),

				$mpg_group_ids,
				$this->db->escape($meta_gruppe_id)
			);
			$meta_papoo_gruppen = $this->db->get_var($sql);
			// gibts einen oder mehr Treffer?
			if ($meta_papoo_gruppen) return true; // dann darf er auch alles sehen
			else {
				// wenn keine treffer, dann darf er auch nix sehen
				$this->content->template['meta_error_kein_zutritt'] = "error";
				return false;
			}
		} // keine Zahl bzw. keine ID, dann darf er auch nix sehen
		else {
			$_SESSION['meta_gruppe_id'] = "";
			$this->checked->selected_meta_gruppe = "";
			//$this->content->template['meta_error_kein_zutritt'] = "error";
			return false;
		}
		return true; // erstmal jeder darf alles ?? Wann?
	}

	/**
	 * Gibt die Links f�r Frontend aus, die man unter einem Men�punkt einbauen
	 * kann
	 */
	function frontend_links()
	{
		// Suchmaske alle Verwaltungen
		$this->content->template['front_link_men_search'] =
			"plugin:mv/templates/mv_search_front.html&mv_id=" . $this->checked->mv_id;
		// Suchmaske f�r die asugew�hlte Verwaltung
		$this->content->template['front_link_search_one_mv'] =
			"plugin:mv/templates/mv_search_front_onemv.html&mv_id=" . $this->checked->mv_id . "&onemv="
			. $this->checked->mv_id;
		// Mitglieds-/Objektdaten anzeigen
		$this->content->template['front_link_men_show'] =
			"plugin:mv/templates/mv_show_front.html&mv_id=" . $this->checked->mv_id . "&mv_content_id="
			. $this->checked->mv_content_id;
		// Mitglieds-/Objektdaten editieren
		$this->content->template['front_link_men_edit'] =
			"plugin:mv/templates/mv_edit_front.html&mv_id=" . $this->checked->mv_id . "&mv_content_id="
			. $this->checked->mv_content_id;
		// Standard Verwaltung alles anzeigen
		$this->content->template['front_link_men_show_all'] =
			"plugin:mv/templates/mv_show_all_front.html&mv_id=" . $this->checked->mv_id;
		// Standard Verwaltung eigene Beit�ge auflisten
		$this->content->template['front_link_men_show_own'] =
			"plugin:mv/templates/mv_show_own_front.html&mv_id=" . $this->checked->mv_id;
		// Standard Verwaltung neuer Eintrag
		$this->content->template['front_link_men_create_own'] =
			"plugin:mv/templates/mv_create_front.html&mv_id=" . $this->checked->mv_id;
		// Seine eigene Mitgliederdaten verarbeiten
		// added if khmweb 05.12.09
		if ($this->checked->mv_id == 1) $this->content->template['front_link_men_edit_own'] =
			"plugin:mv/templates/mv_edit_own_front.html&mv_id=" . $this->checked->mv_id;
		// Verwaltungs ID
		$this->content->template['mv_id'] = $this->checked->mv_id;
		// Link auf das eigene Template
		$this->content->template['link'] =
			$_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template;
		$this->content->template['mv_link'] =
			$_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&amp;mv_id=" . $this->checked->mv_id;
		if ($this->checked->update_all_fields == "ok") $this->update_alle_felder_in_alle_metaebenen();
	}

	/**
	 * mv::update_alle_felder_in_alle_metaebenen()
	 * ALle Felder raussuchen und in alle Metaebenen l�schen und neu eintragen
	 * @return void
	 */
	function update_alle_felder_in_alle_metaebenen()
	{
		return;
		if (is_numeric($this->checked->mv_id)) {
			//Felder raussuchen
			$sql = sprintf("SELECT DISTINCT * FROM %s
		WHERE mvcform_meta_id = 1
		AND mvcform_form_id = '%d'",
				$this->cms->tbname['papoo_mvcform'],
				$this->db->escape($this->checked->mv_id)
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			//Meta Ebenen raussuchen
			$sql = sprintf("SELECT mv_meta_id FROM %s ",
				$this->cms->tbname['papoo_mv']
				. "_meta_"
				. $this->checked->mv_id
			);
			$metas = $this->db->get_results($sql, ARRAY_A);
			if (count($metas)) {
				//Durchloopen f�r jede Metaebene
				foreach ($metas as $mtid) {
					if ($mtid['mv_meta_id'] > 0) {
						//Eintr�ge dieser Ebene l�schen
						$sql_del = sprintf("DELETE FROM %s
			    WHERE mvcform_meta_id = '%d'",
							$this->cms->tbname['papoo_mvcform'],
							$mtid['mv_meta_id']
						);
						#$this->db->query($sql_del);
						if (count($result)) {
							//Alle Eintr�ge durchloopen und Statement erzeugen
							foreach ($result as $dat) {
								$statement = "";
								foreach ($dat as $key => $value) {
									$statement .= $key . "='" . $value . "',";
								}
								$statement = substr($statement, 0, -1); // letztes Komma wieder raus
								$statement = str_ireplace("mvcform_meta_id='1", "mvcform_meta_id='" . $mtid['mv_meta_id'], $statement);
								$sql = sprintf("INSERT INTO %s SET %s",
									$this->cms->tbname['papoo_mvcform'],
									$statement);
								#$this->db->query($sql);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Kalender erstellen
	 */
	function make_calender($front = "", $no = "")
	{
		$monats_id = "monats_id" . $no;
		$jahr_id = "jahr_id" . $no;
		$monat = date("m");
		$jahr = date("Y");

		// Liste der Monate
		for ($i = 1; $i < 13; $i++) {
			$monat_array[$i]['name'] = $this->make_monat(date("F", mktime(0, 0, 0, $i, 1, $jahr)));
			$monat_array[$i]['mon_id'] = $i;
		}

		// Liste der Jahre
		for ($i = $jahr - 5; $i < $jahr + 5; $i++) {
			$jahr_array[$i]['name'] = $i;
			$jahr_array[$i]['jahr_id'] = $i;
		}
		// ans Template weitergeben
		$this->content->template['monat_array'][$no] = $monat_array;
		$this->content->template['jahr_array'][$no] = $jahr_array;
		$this->content->template['jahr_id'][$no] = $this->checked->$jahr_id;
		$this->content->template['monats_id'][$no] = $this->checked->$monats_id;

		// wenn Monats Id mitgeschickt wurde, dann auch benutzen
		if (!empty($this->checked->$monats_id)) {
			$monat = $this->checked->$monats_id;
		} // ansonsten ans Template die Monatszahl des aktuellen Monats �bergeben
		else {
			$this->content->template['monats_id'][$no] = date("n", time());
			#$this->content->template['monats_id'][$no] = date("n", mktime());
		}

		// wenn Jahres Id mitgeschickt wurde, dann auch benutzen
		if (!empty($this->checked->$jahr_id)) {
			$jahr = $this->checked->$jahr_id;
		} // ansonsten ans Template die Jahresszahl des aktuellen Jahres �bergeben
		else {
			$this->content->template['jahr_id'][$no] = date("Y", time());
			#$this->content->template['jahr_id'][$no] = date("Y", mktime());
		}
		// Leere Eintr�ge bis zum ersten Tag in der Tabelle erzeugen
		$tagderwoche = date("w", mktime(0, 0, 0, $monat, 1, $jahr)) - 1;
		$heute = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

		for ($j = 1; $j <= $tagderwoche; $j++) {
			$tagderwoche_a[] = $j;
		}
		// und ans Template weitergeben
		$this->content->template['tagderwoche_a'][$no] = $tagderwoche_a;
		// Anzahl der Tage des aktuellen Monats raussuchen
		$anzahl = date("t", mktime(0, 0, 0, $monat, 1, $jahr));

		// Alle Tage durchloopen und Treffer in der Verwaltung zuweisen
		for ($j = 1; $j <= $anzahl; $j++) {
			$kal_tage[$j][] = $j;
			// Checken ob Montag
			$montag = date("D", mktime(0, 0, 0, $monat, $j, $jahr));

			// Wenn Montag dann zuweisen und neuen tr erzwingen
			if ($montag == "Mon") {
				$kal_tage[$j][] = $montag;
			}
			// checken ob an dem Tag buchen m�glich ist
			$dertag = mktime(0, 0, 0, $monat, $j, $jahr);

			// wenns keine Metagruppe f�r den User gibt, dann auch keine Termine anzeigen
			if (!empty($this->meta_gruppe)) {
				$ok = $this->check_calender_mv($dertag);
			} else {
				$ok = false;
			}
			$kal_tage[$j]['belegt'] = "";

			if ($ok) {
				$kal_tage[$j]['link'] = 'ok';
				$kal_tage[$j]['treffer'] =
					count($this->treffer_all) . $this->content->template['plugin']['mv']['mv_eintrag'];
				$kal_tage[$j]['mv_id'] = $this->checked->mod_mv_id;
			} else {
				$kal_tage[$j]['link'] = "";
				$kal_tage[$j]['mv_id'] = $this->checked->mod_mv_id;
			}

			$kal_tage[$j]['datum'] = mktime(0, 0, 0, $monat, $j, $jahr);
			$kal_tage[$j]['tag'] = $j;
			$kal_tage[$j]['monat'] = $monat;
			$kal_tage[$j]['jahr'] = $jahr;
		}
		// Link f�r Suchmaske ans Template
		$this->content->template['url_suchmaske'][$no] =
			PAPOO_WEB_PFAD . "/plugin.php?template=mv/templates/mv_kalender_search_front.html&menuid="
			. $this->checked->menuid;
		$this->content->template['mv_id_kal'][$no] = $no;

		// Daten ins Template
		if ($no != "no") {
			$this->content->template['paket_tage'][$no] = $kal_tage;
		} else {
			return $kal_tage;
		}
	}

	/**
	 * Gibts es f�r den Tag Eintr�ge in Datums- oder Zeitintervallfeldern der
	 * ausgew�hlten Verwaltung
	 */
	function check_calender_mv($dertag)
	{
		$dertag_org = $dertag;
		// wegen Unix Timestamp Begrenzung 1.1.1970 Umwandlung in eigenen Longtime Zeitstempel
		$dertag = $this->unixtime_to_longtime($dertag);
		$treffer_all = array();
		// Kalender Felder
		if (empty($this->kalender_felder)) $this->get_kalender_felder();
		// schaut, ob es f�r diesen Tag in den ausgegew�hlten Feldern einen Treffer im Content Bereich der Verwaltung gibt
		if (!empty($this->kalender_felder)) {
			foreach ($this->kalender_felder as $kalender_feld) {
				$treffer = "";
				// K�rzel damit man gucken kann, ob der Feldname schon in dem Array ist oder nicht
				$code = "fid_" . $kalender_feld['mvcform_id'] . "_mid_" . $this->db->escape($this->meta_gruppe);
				$feld_daten = $this->feldername->$code;
				if (empty($this->feldername->$code)) {
					$this->feldername = new stdClass();
					// holt den Feldnamen und Feldtyp aus der Datenbank
					$sql = sprintf("SELECT mvcform_name,
			mvcform_type
			FROM %s
			WHERE mvcform_id = '%s'
			AND mvcform_meta_id = '%d'",
						$this->cms->tbname['papoo_mvcform'],
						$this->db->escape($kalender_feld['mvcform_id']),
						$this->db->escape($this->meta_gruppe)
					);
					$feld_daten = $this->feldername->$code = $this->db->get_results($sql, ARRAY_A);
				}
				// manchmal....
				$subbi = $feld_daten[0]['mvcform_name'] . "_" . $kalender_feld['mvcform_id'];
				// wenns ein Datumsfeld ist
				if ($feld_daten[0]['mvcform_type'] == "timestamp") {
					// gibt es f�r dieses Feld und dem Datum einen Treffer
					$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
					$sql = sprintf("SELECT %s FROM %s, %s, %s
			WHERE %s = '%s'
			AND mv_content_id = mv_meta_lp_user_id
			AND mv_meta_main_lp_user_id = mv_meta_lp_user_id
			AND mv_meta_lp_meta_id = '%d'
			AND mv_meta_lp_mv_id = '%d'
			OR %s = '%s'
			AND mv_content_id = mv_meta_main_lp_user_id
			AND mv_meta_main_lp_user_id = mv_meta_lp_user_id
			AND mv_meta_lp_meta_id = '%d'
			AND mv_meta_lp_mv_id = '%d'",
						$this->db->escape($subbi),

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_search_"
						. $lang,

						$this->cms->tbname['papoo_mv_meta_lp'],

						$this->cms->tbname['papoo_mv_meta_main_lp'],

						$this->db->escape($subbi),
						$this->db->escape($dertag),
						$this->db->escape($this->meta_gruppe),
						$this->db->escape($this->checked->mv_id),
						$this->db->escape($subbi),
						$this->db->escape($dertag),
						$this->db->escape($this->meta_gruppe),
						$this->db->escape($this->checked->mv_id)
					);
					$treffer = $this->db->get_results($sql);
				} // beim Zeitintervall
				elseif ($feld_daten[0]['mvcform_type'] == "zeitintervall") {
					if (empty($this->zeitintervalle[$subbi])) {
						// erstmal alle Zeitintervalle f�r dieses Feld aus der Datenbank holen
						$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
						$sql = sprintf("SELECT %s FROM %s, %s, %s
			    WHERE %s LIKE '%s'
			    AND mv_content_id = mv_meta_lp_user_id
			    AND mv_meta_main_lp_user_id = mv_meta_lp_user_id
			    AND mv_meta_lp_meta_id = '%d'
			    AND mv_meta_lp_mv_id = '%d'
			    OR %s LIKE '%s'
			    AND mv_content_id = mv_meta_main_lp_user_id
			    AND mv_meta_main_lp_user_id = mv_meta_lp_user_id
			    AND mv_meta_lp_meta_id = '%d'
			    AND mv_meta_lp_mv_id = '%d'",

							$this->db->escape($feld_daten[0]['mvcform_name']
								. "_"
								. $kalender_feld['mvcform_id']),

							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id)
							. "_search_"
							. $lang,

							$this->cms->tbname['papoo_mv_meta_lp'],
							$this->cms->tbname['papoo_mv_meta_main_lp'],

							$this->db->escape($feld_daten[0]['mvcform_name']
								. "_"
								. $kalender_feld['mvcform_id']),

							"%,%",
							$this->db->escape($this->meta_gruppe),
							$this->db->escape($this->checked->mv_id),

							$this->db->escape($feld_daten[0]['mvcform_name']
								. "_"
								. $kalender_feld['mvcform_id']),
							"%,%",
							$this->db->escape($this->meta_gruppe),
							$this->db->escape($this->checked->mv_id)
						);
						//Da sind jetzt ALLE Eintr�ge drin, die ein , drin haben ... Braucht unbedingt mal einen FIX (nicht nur den...)
						$this->zeitintervalle[$subbi] = $this->db->get_results($sql, ARRAY_A);
					}
					if (!empty($this->zeitintervalle[$subbi])) {
						foreach ($this->zeitintervalle[$subbi] as $feld_wert) {
							if (!empty($feld_wert[$subbi])) {
								$splitwert = $feld_wert[$subbi];
								list($datum_anfang, $datum_ende) = explode(",", $splitwert);
								$dasplit = explode(".", $datum_anfang);
								$dendsplit = explode(".", $datum_ende);
								if (!empty($dasplit['0'])) $datum_anfang_time = mktime(0, 0, 0, $dasplit['1'], $dasplit['2'], $dasplit['0']);
								if (!empty($dendsplit['0'])) $datum_ende_time = mktime(0, 0, 0, $dendsplit['1'], $dendsplit['2'], $dendsplit['0']);
								if ($datum_anfang_time <= $dertag_org
									&& $datum_ende_time >= $dertag_org) $treffer[] = array($feld_wert);
								$neuwerte[] [$subbi] = $feld_wert[$subbi];
							}
						}
						$this->zeitintervalle[$subbi] = $neuwerte;
					}
				}
				if (!empty($treffer)) $treffer_all = array_merge($treffer_all, $treffer);
			}
		}

		// keinen Treffer? dann auch nichts melden
		if (empty($treffer_all)) {
			return false;
		} // ansonsten Trefferdaten und true zur�ckgeben
		else {
			// Trefferdaten �ber Zwischenvariable ans Template weitergeben
			$this->treffer_all = $treffer_all;
			return true;
		}
	}

	/**
	 * Monatsnamen korrekt darstellen
	 */
	function make_monat($mon)
	{
		if ($this->cms->lang_id == 2) {
			return $mon;
		} else {
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
			return $mon;
		}
	}

	/**
	 * Ist der Eintrag gesperrt? wenn ja dann true zur�ck
	 */
	function is_locked($mv_content_id, $mv_id = "")
	{
		if (empty($mv_id)) $mv_id = $this->checked->mv_id;
		$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		// holt den Flag Wert f�r die Sperre aus der Datenbank
		$sql = sprintf("SELECT mv_content_sperre
	    FROM %s
	    WHERE mv_content_id = '%d'",

			$this->cms->tbname['papoo_mv']
			. "_content_"
			. $this->db->escape($mv_id)
			. "_search_"
			. $lang,

			$this->db->escape($mv_content_id)
		);
		$is_locked = $this->db->get_var($sql);
		$this->finde_die_art_der_verwaltung_heraus();
		//Wenn MV nicht Mitgliederverwaltung
		if ($this->mv_art > 1
			and $is_locked == "1") //Wenn leer dann schauen ob es evtl. derjenige ist der eingetragen hat
		{
			$sql = sprintf("SELECT mv_content_id
		FROM %s
		WHERE mv_content_id = '%d'
		AND mv_content_owner = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_content_"
				. $this->db->escape($mv_id)
				. "_search_"
				. $lang,

				$this->db->escape($mv_content_id),
				$this->user->userid
			);
			$is_locked = $this->db->get_var($sql);
			if (is_numeric($is_locked)) $is_locked = "0";
		}
		// ist der 1 dann ist der Eintrag gesperrt
		if ($is_locked == "1") return true;
		// ansonsten ist der Eintrag nicht gesperrt
		return false;
	}

	/**
	 * Sortierungsfelder f�r die Suche editieren
	 */
	function edit_sortierung()
	{
		// neues Feld hinzuf�gen
		if (!empty($this->checked->submit_new_field)) {
			$sql = sprintf("UPDATE %s
		SET mvcform_sort = '1'
		WHERE mvcform_id = '%d'
		AND mvcform_form_id = '%d'",
				$this->cms->tbname['papoo_mvcform'],
				$this->db->escape($this->checked->felder),
				$this->db->escape($this->checked->mv_id));
			$this->db->query($sql);
		}

		// Feld wieder rausl�schen
		if (!empty($this->checked->submit_del_field)) {
			$feld_del = array_keys($this->checked->submit_del_field);
			$sql = sprintf("UPDATE %s
		SET mvcform_sort = '0'
		WHERE mvcform_id = '%d'
		AND mvcform_form_id = '%d'",
				$this->cms->tbname['papoo_mvcform'],
				$this->db->escape($feld_del[0]),
				$this->db->escape($this->checked->mv_id));
			$this->db->query($sql);
		}
		// Holt alle Felder aus der Datenbank, die es f�r diese Verwaltung gibt und noch nicht als Sortierfeld markiert wurden,
		$sql = sprintf("SELECT mvcform_id,
	    mvcform_name
	    FROM %s
	    WHERE mvcform_meta_id = '1'
	    AND mvcform_form_id = '%d'
	    AND mvcform_sort = '0'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->mv_id));
		$felder = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['felder'] = $felder;
		// Holt alle Sortierfelder aus der Datenbank
		$sql = sprintf("SELECT mvcform_id,
	    mvcform_name
	    FROM %s
	    WHERE mvcform_meta_id = '1'
	    AND mvcform_form_id = '%d'
	    AND mvcform_sort <> '0'",
			$this->cms->tbname['papoo_mvcform'],
			$this->db->escape($this->checked->mv_id));
		$felder = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['felder_sort'] = $felder;
		// link ans Template
		$this->content->template['link'] = $_SERVER['PHP_SELF']
			. "?menuid="
			. $this->checked->menuid
			. "&template="
			. $this->checked->template;
	}

	/**
	 * Testet, ob es schon mind. eine Verwaltung in der Flexverwaltung gibt
	 */
	function mv_exist()
	{
		$sql = sprintf("SELECT count(mv_id) FROM %s",
			$this->cms->tbname['papoo_mv']
		);
		$mv_id_1 = $this->db->get_var($sql);
		return $mv_id_1;
	}

	/**
	 * Formatiert das Datum f�rs jeweilige Land(im Moment nur Deutschland)
	 */
	function format_date($timestamp)
	{
		$datum = "";

		// hier m�sste dann eine L�nderabfrage rein...
		if (!empty($timestamp)) {
			$datum = date("d.m.Y H:i", $timestamp);
		}
		return $datum;
	}

	/**
	 * F�r dzvhae import von einer Verkn�pfungs csv Datei
	 */
	function lp_zahlungen()
	{
		// Mv Id ans Template
		$this->content->template['mv_id'] = $this->checked->mv_id;
		// Link auf das eigene Template
		$this->content->template['mv_link'] = $_SERVER['PHP_SELF']
			. "?menuid="
			. $this->checked->menuid
			. "&mv_id="
			. $this->checked->mv_id;
		// l�dt die Datei Zeilenweise in ein Array ein
		if (file_exists(PAPOO_ABS_PFAD . "/dokumente/logs/feld_zahlungen_lookup.csv"))
			$daten = file(PAPOO_ABS_PFAD
				. "/dokumente/logs/feld_zahlungen_lookup.csv");
		if (!empty($daten)) {
			$values = "";
			$values_array = array();
			foreach ($daten as $zeile) {
				// Zahlenwert und den Lookuptext extrahieren und un�tige Whitespaces l�schen
				list($zahlenwert, $text) = explode("\t", $zeile);
				$zahlenwert = trim($zahlenwert);
				$text = trim($text);
				// und f�r sql Statement zwischenspeichern
				$values .= "('" . $this->db->escape($zahlenwert) . "','" . $this->db->escape($text) . "'),";
				$values_array[$zahlenwert] = $text;
			}
			$values = substr($values, 0, -1); // letztes Komma wieder raus
		} else {
			$this->content->template['mv_keine_datei'] = $this->content->template['plugin']['mv']['lp_zahlungen_datei_fehlt'];
			return;
		}
		// wenn die Tabelle schon existiert dann leeren
		if ($this->cms->tbname['papoo_mv_zahlungen_lookup']) {
			$sql = sprintf("TRUNCATE TABLE %s",
				$this->cms->tbname['papoo_mv']
				. "_zahlungen_lookup"
			);
			$this->db->query($sql);
		} else {
			// neue Tabelle generieren
			$sql = sprintf("CREATE TABLE IF NOT EXISTS %s (
		`lookup_id` int(11) NOT NULL,
		`text` text NOT NULL,
		KEY `lookup_id` (`lookup_id`)
	    )
	    ENGINE = MyISAM
	    DEFAULT CHARSET = utf8;",

				$this->cms->tbname['papoo_mv']
				. "_zahlungen_lookup"
			);
			$this->db->query($sql);
		}
		// Werte in die Tabelle einf�gen
		$sql = sprintf("INSERT INTO %s (lookup_id, text) VALUES %s",
			$this->cms->tbname['papoo_mv']
			. "_zahlungen_lookup",
			$values);
		$this->db->query($sql);
		// Lookuppaare ans Template
		$this->content->template['mv_lp_zahlungen_values'] = $values_array;
	}

	/**
	 * Liefert die maximale meta_id (Metaebene ID) f�r alle Verwaltungen
	 */
	function get_max_meta_id()
	{
		// holt alle mv_ids aus der Datenbank
		$sql = sprintf("SELECT mv_id FROM %s", $this->cms->tbname['papoo_mv']);
		$mv_ids = $this->db->get_results($sql, ARRAY_A);

		if (!empty($mv_ids)) {
			// Tabellen- und Spaltennamen f�r das sql Statement zusammensetzen
			foreach ($mv_ids as $mv_id) {
				$tabellen_name .= $this->cms->tbname['papoo_mv'] . "_meta_" . $this->db->escape($mv_id['mv_id']) . ", ";
				$spalten_name .= $this->cms->tbname['papoo_mv'] . "_meta_" . $this->db->escape($mv_id['mv_id'])
					. ".mv_meta_id" . ", ";
			}
			$tabellen_name = substr($tabellen_name, 0, -2);
			$spalten_name = substr($spalten_name, 0, -2);
		}
		// wieviel Verwaltungen sind installiert
		$anzahl_verwaltungen = count($mv_ids);

		// wenns mehr als eine ist dann die einzelnen Spalten der jeweiligen Verwaltung mit GREATEST vergleichen
		if ($anzahl_verwaltungen > 1) {
			// Metaebenen ID der neuen Ebene holen, indem in allen Meta Tabbelen von allen Verwaltungen die Maximalwerte verglichen werden
			$sql = sprintf("SELECT MAX(GREATEST(%s)) FROM %s", $spalten_name, $tabellen_name);
			$max_meta_id = $this->db->get_var($sql);
		} // bei nur einer Verwaltung braucht es das GREATEST nicht
		else {
			$sql = sprintf("SELECT MAX(mv_meta_id) FROM %s",
				$this->cms->tbname['papoo_mv'] . "_meta_" . $this->db->escape($mv_id['mv_id']));
			$max_meta_id = $this->db->get_var($sql);
		}
		// den Wert zur�ckgeben
		return ($max_meta_id);
	}

	/**
	 * Wandelt eine Array von Objekten in ein "normales" Array um
	 **/
	function object_to_array($objekt)
	{
		$array = array();

		if (!empty($object)) {
			$i = 0;
			foreach ($objekt as $row) {
				$array[$i] = get_object_vars($row);
				$i++;
			}
		}
		return $array;
	}

	/**
	 * Newsletter nach der internen Suche verschicken
	 */
	function newsletter()
	{
		// Da das Plugin ja nach der Flexverwaltung installiert werden k�nnte und auch kann, hier wird es nochmal schnell eingebunden falls noch nicht geschehen
		require_once(PAPOO_ABS_PFAD . "/plugins/newsletter/lib/newsletterplugin_class.php");
		global $news;
		$news = new news();
		$this->news = &$news;
		$_SESSION['mv_newsletter_anzahl'] = $this->checked->anzahl;
		$this->news->send_news();
		// Anzahl der Emailadressen und die Empf�nger anzeigen
		//$this->content->template['mv_anzahl'] = $this->checked->anzahl;
		//$this->list_emailadressen($_SESSION['mv_import']['mv_sql_buffer']);
		//$this->news->start_news();
	}

	/**
	 * Listet die Vornamen, Namen und Emailadressen auf
	 */
	function list_emailadressen($sql_buffer)
	{
		// ertsmal das SELECT * raus und daf�r SELECT mv_content_owner rein
		// dann noch die usertabelle dazu und die lookup_ug mit rein
		$sql_buffer = preg_replace("/SELECT \* FROM/",
			"SELECT DISTINCT (mv_content_userid), username, email FROM " . $this->cms->tbname['papoo_user'] . ","
			. $this->cms->tbname['papoo_lookup_ug'] . ",", $sql_buffer);
		$sql_buffer = preg_replace("/(papoo_mv_content_.{1}_search_.{1} WHERE )/",
			"\\1mv_content_userid=" . $this->cms->tbname['papoo_user'] . ".userid AND active='1' AND ",
			$sql_buffer);
		// $sql_buffer = preg_replace ("/papoo_mv_content_(.{1})_search_.{1}/i", "papoo_mv_content_\\1", $sql_buffer);
		print "<br />" . $sql_buffer . "<br />";
		$result = $this->db->get_results($sql_buffer);
		print_r($result);
	}

	/**
	 * Gibt alle Feldername_Felderids mit Optionswerten aus
	 */
	function druck_option_werte()
	{
		// holt alle multi, select,radio und check Felder f�r diese Verwaltung und Metaebene aus der Datenbank
		$sql = sprintf("SELECT * FROM %s
	    WHERE mvcform_meta_id = '1'
	    AND mvcform_form_id = '1'
	    AND (mvcform_type = 'select'
	    OR mvcform_type ='multiselect'
	    OR mvcform_type = 'check'
	    OR mvcform_type = 'radio')
	    ORDER by mvcform_id",
			$this->cms->tbname['papoo_mvcform']
		);
		$felder = $this->db->get_results($sql, ARRAY_A);
		if (!empty($felder)) {
			$out = "<h2>Ausgabe der Feldwerte f&uuml;r die Feldtypen select/multiselect/check/radio</h2>";
			// loope alle Felder durch
			foreach ($felder as $feld) {
				// holt alle m�glichen Auswahlwerte f�r dieses Feld aus der Datenbank
				$sql = sprintf("SELECT content FROM %s
		    WHERE lang_id = 1",
					$this->cms->tbname['papoo_mv']
					. "_content_1_lang_"
					. $this->db->escape($feld['mvcform_id'])
				);
				$content = $this->db->get_results($sql, ARRAY_A);
				// Feldname in Bold
				$out .= "<strong>Feldname: "
					. $feld['mvcform_name']
					. "_"
					. $feld['mvcform_id']
					. '</strong><ol style="margin-top:0">';
				if (!empty($content)) {
					// Feldoptionen auflisten
					$i = 1;
					foreach ($content as $wert) {
						$out .= "<li> "
							. $wert['content']
							. "</li>";
						#$i++;
					}
					$out .= "</ol>";
				}
			}
		}
		// Liste ans Template weitergeben
		$this->content->template['mv_out'] = $out;
	}

	/**
	 * Zeigt die �nderungen f�r diesen Eintrag
	 */
	function show_protokoll()
	{
		// holt die Protokoll Daten f�r diesen Eintrag aus der Datenbank
		$sql = sprintf("SELECT mv_pro_date,
	    username,
	    mv_pro_old_content,
	    mv_pro_feld_id
	    FROM %s,%s
	    WHERE mv_pro_login_id = userid
	    AND mv_pro_mv_content_id = '%d'
	    ORDER BY mv_pro_date DESC
	    LIMIT 50",
			$this->cms->tbname['papoo_mv_protokoll'],
			$this->cms->tbname['papoo_user'],
			$this->db->escape($this->checked->mv_content_id)
		);
		$protokoll_records = $this->db->get_results($sql, ARRAY_A);
		if (!empty($protokoll_records)) {
			// alle Eintr�ge durchloopen
			$i = 0;
			foreach ($protokoll_records as $key => $value) {
				// Pr�fen, ob die dazugeh�rige lang-Tabelle noch existiert
				#$table_name = 'papoo_mv'
				#			. "_content_"
				#			. $this->db->escape($this->checked->mv_id)
				#			. "_lang_"
				#			. $this->db->escape($protokoll_records[$key]['mv_pro_feld_id']);
				#if (!$this->cms->tbname[$table_name]) continue; // Tabelle / Feld existiert nicht mehr. Kein Zugriff mehr auf die Daten.
				// Feldtyp f�r die id aus der Datenbank holen
				$sql = sprintf("SELECT mvcform_type FROM %s
		    WHERE mvcform_id = '%d'
		    AND mvcform_meta_id = '%d'",
					$this->cms->tbname['papoo_mvcform'],
					$this->db->escape($protokoll_records[$key]['mv_pro_feld_id']),
					$this->db->escape($this->meta_gruppe)
				);
				$feld_type = $this->db->get_var($sql);
				// Label f�r Feldid aus der Datenbank holen
				$sql = sprintf("SELECT mvcform_label
		    FROM %s
		    WHERE mvcform_lang_id = '%d'
		    AND mvcform_lang_lang = '%d'
		    AND mvcform_lang_meta_id = '%d'",
					$this->cms->tbname['papoo_mvcform_lang'],
					$this->db->escape($protokoll_records[$key]['mv_pro_feld_id']),
					$this->db->escape($this->cms->lang_back_content_id),
					$this->db->escape($this->meta_gruppe)
				);
				$feld_label = $this->db->get_var($sql);
				$protokoll[$i]['mv_pro_feld_label'] = $key . " " . $feld_label; // Feldlabel
				$protokoll[$i]['mv_pro_date'] = $protokoll_records[$key]['mv_pro_date']; // �nderungsdatum
				$protokoll[$i]['username'] = $protokoll_records[$key]['username']; // Edit User
				// Feldtypen-Sonderf�lle durchgehen
				if ($feld_type == "timestamp") {
					if (!empty($protokoll_records[$key]['mv_pro_old_content'])) {
						list($tag, $monat, $jahr) = $this->get_day_month_year($protokoll_records[$key]['mv_pro_old_content']);
						$protokoll[$i]['mv_pro_old_content'] = $tag . "." . $monat . "." . $jahr;
					}
				} elseif ($feld_type == "zeitintervall") {
					list($anfang_datum, $ende_datum) = explode(",", $protokoll_records[$key]['mv_pro_old_content']);
					if (!empty($anfang_datum)) list($anfang_tag, $anfang_monat, $anfang_jahr) = $this->get_day_month_year($anfang_datum);
					if (!empty($ende_datum)) list($ende_tag, $ende_monat, $ende_jahr) = $this->get_day_month_year($ende_datum);
					$protokoll[$i]['mv_pro_old_content'] = $anfang_tag . "." . $anfang_monat . "." . $anfang_jahr . " "
						. $this->content->template['plugin']['mv']['bis'] . " " . $ende_tag . "." . $ende_monat . "."
						. $ende_jahr;
				} elseif ($feld_type == "select"
					|| $feld_type == "radio"
					|| $feld_type == "multiselect"
					|| $feld_type == "check"
					|| $feld_type == "pre_select") {
					if ($protokoll_records[$key]['mv_pro_old_content']) {
						if ($feld_type = "multiselect") $items_arr = explode("\n", $protokoll_records[$key]['mv_pro_old_content']);
						else $items_ar[] = ""; // damit keinen foreach-Fehler gibt
						// alle Multiselect-Werte durchgehen
						foreach ($items_arr as $key2 => $value2) {
							if ($value2) // m�sste eigentlich immer was da sein
							{
								// Klartext abholen zur Id
								$sql = sprintf("SELECT content FROM %s
				    WHERE lookup_id = '%d'
				    AND lang_id = '%d'",

									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($this->checked->mv_id)
									. "_lang_"
									. $this->db->escape($protokoll_records[$key]['mv_pro_feld_id']),

									$value2,
									$this->db->escape($this->cms->lang_back_content_id)
								);
								$result = $this->db->get_var($sql);
								if ($result) $protokoll[$i]['mv_pro_old_content'] = $result;
								else $protokoll[$i]['mv_pro_old_content'] = ""; // war vorher ein leeres Feld
								$i++;
							}
						}
					} // war vorher ein leeres Feld
					else $protokoll[$i]['mv_pro_old_content'] = "";
				} else $protokoll[$i]['mv_pro_old_content'] = $protokoll_records[$key]['mv_pro_old_content'];
				$i++;
			}// end foreach
		}
		// Liste ans Template weitergeben
		$this->content->template['mv_protokoll_liste'] = $protokoll;
	}

	/**
	 * baut aus dem Suchstring ein Array mit allen m�glichen Kombinationen f�r
	 * die Sonderzeichen zusammen wie z.B. � oder Umlaute
	 */
	function sonderfall_loopen($searchfor)
	{
		// hier sind die Sonderf�lle in einem Array gespeichert mit einem | getrennt, das bei regul�ren Ausdr�cken ODER bedeutet
		$sonderfaelle = array(
			"ß|ss",
			"ä|ae",
			"Ä|Ae",
			"ö|oe",
			"Ö|Oe",
			"ü|ue",
			"Ü|Ue"
		);

		// und hier die Kombinationen nochmal als Array gespeichert
		$kombinationen = array(
			array(
				"ß",
				"ss"
			),
			array(
				"a",
				"ae"
			),
			array(
				"Ä",
				"Ae"
			),
			array(
				"ö",
				"oe"
			),
			array(
				"Ö",
				"Oe"
			),
			array(
				"ü",
				"ue"
			),
			array(
				"Ü",
				"Ue"
			)
		);

		$sucharray_buffer = array();
		// erste Runde ist der Suchbegriff alleine im Array gespeichert
		$sucharray_buffer[] = $searchfor;

		// geh alle Sonderf�lle durch
		foreach ($sonderfaelle as $key => $sonderfall) {
			// gehe alle bisherigen Suchbegriffm�glichkeiten durch
			foreach ($sucharray_buffer as $moeglichkeit) {
				// Teile den Suchbegriff durch den Sonderfall in ein Array auf
				$teile = preg_split("/" . ($sonderfall) . "/", $moeglichkeit);
				// �berpr�fe ob es im Suchbegriff den Sonderfall gibt
				if (count($teile) > 1) {
					// dann hole dir alle m�glichen Kombinationen, wie z.B.
					// Suchbegriff = Spass
					// Kombinationen: Spass und Spa�
					list($sucharray, $i, $sonderfall_alt) =
						$this->get_sucharray($teile, array(), 0, $kombinationen[$key]);
					// erg�nze die bisherigen Suchbegriffm�glichkeiten durch die neuen Kombinationen
					$sucharray_buffer = array_merge($sucharray_buffer, $sucharray);
					// und l�sche die erste Suchm�glichkeit, sonst gibt es doppelte Treffer
					array_shift($sucharray_buffer);
				}
			}
		}
		// so nochmal sch�n sortiern
		sort($sucharray_buffer);
		// und alle m�glichen Kombinationen zur�ckgeben
		if (count($sucharray_buffer) > 128) $sucharray_buffer = array();
		return ($sucharray_buffer);
	}

	/**
	 * Baut alle m�glichen Kombinationen aus einem Suchbegriff und einem
	 * Sonderzeichen zusammen rekursive Funktion
	 */
	function get_sucharray($teile, $searchfor_buffer, $i, $sonderfall)
	{
		// erste Runde hier entlang
		if ($i == 0) {
			// gehe beide Sonderf�lle durch, wie z.B. "ss" und "�"
			foreach ($sonderfall as $buchstabe) {
				// bau aus ersten($i=0) Teilstring und Sonderzeichen eine Kombination zusammen
				$searchfor_buffer_neu[] = $teile[$i] . $buchstabe;
			}
			// gibt es mehr als ein Teilstring
			if (count($teile) > 1) {
				// dann geh den n�chsten Teilstring durch ($i=1), rekursive Funktion
				list($searchfor_buffer, $i,
					$sonderfall) = $this->get_sucharray($teile, $searchfor_buffer_neu, 1, $sonderfall);
			} else {
				// wenn nur ein Teilstring dabei war, dann gib die zwei Kombinationen zur�ck
				return array(
					$searchfor_buffer_neu,
					$i,
					$sonderfall
				);
			}
		} // alle weiteren Runden hier entlang
		else {
			// mach das nur bis zum vorletzten Teilstring, sonst klebt am Ende jeder Suchkombination nochmal das Sonderzeichen dran
			// z.B. F�hn -> F�hn�, Foehn�,F�hnoe,Foehnoe anstatt F�hn,Foehn
			if ($i < count($teile) - 1) {
				// erste Runde
				$y = 0;

				// gehe als bisherigen m�glichen Schreibweisen durch
				foreach ($searchfor_buffer as $moeglichkeit) {
					// gehe die beiden Sonderfallbuchtaben durch, wie z.B. "ss" und "�"
					foreach ($sonderfall as $buchstabe) {
						// bau aus Teilstring Nummer $i und Sonderzeichen eine Kombination zusammen
						$searchfor_buffer_neu[$y] = $moeglichkeit . $teile[$i] . $buchstabe;
						$y++;
					}
				}
				// n�chster Teilstring
				$i++;
				// und hol f�r diesen Teilstring die Kombinationen
				list($searchfor_buffer, $i, $sonderfall) =
					$this->get_sucharray($teile, $searchfor_buffer_neu, $i, $sonderfall);
			}
			// gib die bisher gefunden Kombinationen, Nummer des Teilstrings und Sonderf�lle als Wert zur�ck
			return array(
				$searchfor_buffer,
				$i,
				$sonderfall
			);
		}

		// so letzter Teilstring, dann nur den Teilstring dranbauen
		if (!empty($searchfor_buffer)) {
			foreach ($searchfor_buffer as $key => $moeglichkeit) {
				$searchfor_buffer[$key] = $moeglichkeit . $teile[$i];
			}
		}
		// und gib alle Kombinationen zur�ck
		return array(
			$searchfor_buffer,
			$i,
			$sonderfall
		);
	}

	/**
	 * Einstellungen f�r die Metaeben editieren
	 * Einleitungstext und Systememails
	 */
	function edit_meta()
	{
		// �nderungen speichern
		if (!empty($this->checked->submit_mv_meta)) {
			// Metaebene Name und Emails updaten
			$sql = sprintf("UPDATE %s SET mv_meta_group_name = '%s',
		mv_meta_emails = '%s',
		mv_meta_allow_frontend = '%s',
		mv_meta_allow_direct_entry = '%s',
		mv_meta_allow_direct_unlock = '%s'
		WHERE mv_meta_id = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_meta_"
				. $this->db->escape($this->checked->mv_id),

				$this->db->escape($this->checked->mv_meta_name),
				$this->db->escape($this->checked->mv_meta_emails),
				$this->db->escape($this->checked->mv_meta_allow_frontend),
				$this->db->escape($this->checked->mv_meta_allow_direct_entry),
				$this->db->escape($this->checked->mv_meta_allow_direct_unlock),
				$this->db->escape($this->checked->mv_meta_id)
			);
			$this->db->query($sql);
			// Top-, Bottom-, Antworttext und Mailtexte updaten
			$sql = sprintf("UPDATE %s SET mv_meta_top_text = '%s',
		mv_meta_bottom_text = '%s',
		mv_meta_antwort_text = '%s',
		mv_mail_text_create_user = '%s',
		mv_mail_text_create_admin = '%s',
		mv_mail_text_change_user = '%s',
		mv_mail_text_change_admin = '%s'
		WHERE mv_meta_lang_id = '%d'
		AND mv_meta_lang_lang_id = '%d'",

				$this->cms->tbname['papoo_mv']
				. "_meta_lang_"
				. $this->db->escape($this->checked->mv_id),

				$this->db->escape($this->checked->mv_meta_top_text),
				$this->db->escape($this->checked->mv_meta_bottom_text),
				$this->db->escape($this->checked->mv_meta_antwort_text),
				$this->db->escape($this->checked->mv_mail_text_create_user),
				$this->db->escape($this->checked->mv_mail_text_create_admin),
				$this->db->escape($this->checked->mv_mail_text_change_user),
				$this->db->escape($this->checked->mv_mail_text_change_admin),
				$this->db->escape($this->checked->mv_meta_id),
				$this->db->escape($this->cms->lang_back_content_id)
			);
			$this->db->query($sql);
			// Nachricht das erfolgreich gespeichert wurde
			$this->content->template['mv_system_msg'] = $this->content->template['plugin']['mv']['meta_geandert'];
		}
		// holt die Daten f�r die gew�nschte Metaebene aus der Datenbank
		$sql = sprintf("SELECT * FROM %s, %s
	    WHERE mv_meta_id = '%d'
	    AND mv_meta_lang_id = mv_meta_id
	    AND mv_meta_lang_lang_id = '%d'",

			$this->cms->tbname['papoo_mv']
			. "_meta_"
			. $this->db->escape($this->checked->mv_id),

			$this->cms->tbname['papoo_mv']
			. "_meta_lang_"
			. $this->db->escape($this->checked->mv_id),

			$this->db->escape($this->checked->mv_meta_id),
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$meta_daten = $this->db->get_results($sql, ARRAY_A);
		// ans Template Werte weitergeben
		$this->content->template['mv_mail_text_create_user'] = "nobr:" . $meta_daten[0]['mv_mail_text_create_user'];
		$this->content->template['mv_mail_text_create_admin'] = "nobr:" . $meta_daten[0]['mv_mail_text_create_admin'];
		$this->content->template['mv_mail_text_change_user'] = "nobr:" . $meta_daten[0]['mv_mail_text_change_user'];
		$this->content->template['mv_mail_text_change_admin'] = "nobr:" . $meta_daten[0]['mv_mail_text_change_admin'];

		$this->content->template['mv_meta_top_text'] = "nobr:" . $meta_daten[0]['mv_meta_top_text'];
		$this->content->template['mv_meta_bottom_text'] = "nobr:" . $meta_daten[0]['mv_meta_bottom_text'];
		$this->content->template['mv_meta_antwort_text'] = "nobr:" . $meta_daten[0]['mv_meta_antwort_text'];
		$this->content->template['mv_meta_allow_frontend'] = "nobr:" . $meta_daten[0]['mv_meta_allow_frontend'];
		$this->content->template['mv_meta_allow_direct_entry'] = "nobr:" . $meta_daten[0]['mv_meta_allow_direct_entry'];
		$this->content->template['mv_meta_allow_direct_unlock'] = "nobr:" . $meta_daten[0]['mv_meta_allow_direct_unlock'];
		$this->content->template['mv_meta_emails'] = "nobr:" . $meta_daten[0]['mv_meta_emails'];
		$this->content->template['mv_meta_name'] = $meta_daten[0]['mv_meta_group_name'];
		$this->content->template['mv_id'] = $this->checked->mv_id;
		$this->content->template['mv_meta_id'] = $this->checked->mv_meta_id;
	}

	/**
	 * F�r die Suche nach einem �nderungs-Datumsbereich
	 */
	function make_edit_changedate()
	{
		// wenn kein �nderungsdatum ausgef�llt ist, dann das Datum von heute nehmen
		if (empty($this->checked->anfang_tag_changedate) && empty($this->checked->anfang_monat_changedate)
			&& empty($this->checked->anfang_jahr_changedate) && empty($this->checked->ende_tag_changedate)
			&& empty($this->checked->ende_monat_changedate) && empty($this->checked->ende_jahr_changedate)) {
			$this->checked->anfang_tag_changedate = date("d", time());
			$this->checked->anfang_monat_changedate = date("m", time());
			$this->checked->anfang_jahr_changedate = date("Y", time());
			$this->checked->ende_tag_changedate = date("d", time());
			$this->checked->ende_monat_changedate = date("m", time());
			$this->checked->ende_jahr_changedate = date("Y", time());
		}
		$cfeld = "";
		$cfeld .= '<label for="mv_changedate">';
		$cfeld .= $this->content->template['plugin']['mv']['changedate'];
		$cfeld .= '</label>';
		// Select Feld f�r den AnfangsTag
		$select_anfang_tag = $this->content->template['plugin']['mv']['tag']
			. ' <select name="anfang_tag_changedate" id="anfang_tag_changedate" size="1" >';
		$select_anfang_tag .= '<option value="">TT</option>';
		$count = 1;

		while ($count < 32) {
			$select_anfang_tag .= '<option value="' . sprintf("%02d", $count);
			// Wenn ausgew�hlt aus redo
			$tag = 'anfang_tag_changedate';

			if ($this->checked->$tag == $count) {
				$select_anfang_tag .= '" selected="selected">';
			} else {
				$select_anfang_tag .= '">';
			}
			$select_anfang_tag .= sprintf("%02d", $count) . '</option>';
			$count++;
		}
		$select_anfang_tag .= '</select>';
		// Select Feld f�r den EndeTag
		$select_ende_tag = $this->content->template['plugin']['mv']['tag']
			. ' <select name="ende_tag_changedate" id="ende_tag_changedate" size="1" >';
		$select_ende_tag .= '<option value="">TT</option>';
		$count = 1;

		while ($count < 32) {
			$select_ende_tag .= '<option value="' . sprintf("%02d", $count);
			// Wenn ausgew�hlt aus redo
			$tag = 'ende_tag_changedate';

			if ($this->checked->$tag == $count) {
				$select_ende_tag .= '" selected="selected">';
			} else {
				$select_ende_tag .= '">';
			}
			$select_ende_tag .= sprintf("%02d", $count) . '</option>';
			$count++;
		}
		$select_ende_tag .= '</select>';
		// Select Feld f�r den AnfangMonat
		$select_anfang_monat = $this->content->template['plugin']['mv']['monat']
			. ' <select name="anfang_monat_changedate" id="anfang_monat_changedate" size="1" >';
		$select_anfang_monat .= '<option value="">MM</option>';
		$count = 1;

		while ($count < 13) {
			$select_anfang_monat .= '<option value="' . sprintf("%02d", $count);
			// Wenn ausgew�hlt aus redo
			$monat = 'anfang_monat_changedate';

			if ($this->checked->$monat == $count) {
				$select_anfang_monat .= '" selected="selected">';
			} else {
				$select_anfang_monat .= '">';
			}
			$select_anfang_monat .= sprintf("%02d", $count) . '</option>';
			$count++;
		}
		$select_anfang_monat .= '</select>';
		// Select Feld f�r den EndeMonat
		$select_ende_monat = $this->content->template['plugin']['mv']['monat']
			. ' <select name="ende_monat_changedate" id="ende_monat_changedate" size="1" >';
		$select_ende_monat .= '<option value="">MM</option>';
		$count = 1;

		while ($count < 13) {
			$select_ende_monat .= '<option value="' . sprintf("%02d", $count);
			// Wenn ausgew�hlt aus redo
			$monat = 'ende_monat_changedate';

			if ($this->checked->$monat == $count) {
				$select_ende_monat .= '" selected="selected">';
			} else {
				$select_ende_monat .= '">';
			}
			$select_ende_monat .= sprintf("%02d", $count) . '</option>';
			$count++;
		}
		$select_ende_monat .= '</select>';
		// Select Feld f�r das AnfangJahr
		$select_anfang_jahr = $this->content->template['plugin']['mv']['jahr']
			. ' <select name="anfang_jahr_changedate" id="anfang_jahr_changedate" size="1" >';
		$select_anfang_jahr .= '<option value="">JJJJ</option>';
		$count = 2000;

		while ($count < 2037) {
			$select_anfang_jahr .= '<option value="' . sprintf("%04d", $count);
			// Wenn ausgew�hlt aus redo
			$jahr = 'anfang_jahr_changedate';

			if ($this->checked->$jahr == $count) {
				$select_anfang_jahr .= '" selected="selected">';
			} else {
				$select_anfang_jahr .= '">';
			}
			$select_anfang_jahr .= sprintf("%04d", $count) . '</option>';
			$count++;
		}
		$select_anfang_jahr .= '</select>';
		// Select Feld f�r das EndeJahr
		$select_ende_jahr = $this->content->template['plugin']['mv']['jahr']
			. ' <select name="ende_jahr_changedate" id="ende_jahr_changedate" size="1" >';
		$select_ende_jahr .= '<option value="">JJJJ</option>';
		$count = 2000;

		while ($count < 2037) {
			$select_ende_jahr .= '<option value="' . sprintf("%04d", $count);
			// Wenn ausgew�hlt aus redo
			$jahr = 'ende_jahr_changedate';

			if ($this->checked->$jahr == $count) {
				$select_ende_jahr .= '" selected="selected">';
			} else {
				$select_ende_jahr .= '">';
			}
			$select_ende_jahr .= sprintf("%04d", $count) . '</option>';
			$count++;
		}
		$select_ende_jahr .= '</select>';
		$cfeld .= '<div class="mv_zeitintervall">';
		$cfeld .= $select_anfang_tag . " " . $select_anfang_monat . " " . $select_anfang_jahr;
		$cfeld .= " " . $this->content->template['plugin']['mv']['bis'] . "<br />";
		$cfeld .= $select_ende_tag . " " . $select_ende_monat . " " . $select_ende_jahr;
		$cfeld .= '</div>';
		$this->content->template['mv_changedate'] = $cfeld;
	}

	/**
	 * Gibt eine Liste der Vergebenen dzvhae System Id aus
	 */
	function get_dz_system_ids()
	{
		$liste = array();

		// Holt alle SystemIds aus der Datenbank
		$sql = sprintf("SELECT %s
	    FROM %s
	    ORDER BY %s",
			$this->dzvhae_feld_flex_systemid,
			$this->cms->tbname['papoo_mv_content_1_search_1'],
			$this->dzvhae_feld_flex_systemid
		);
		$systemids = $this->db->get_results($sql, ARRAY_A);

		// geht die ids durch
		foreach ($systemids as $id) {
			// wenns schon einen Eintrag mit dieser SystemID gibt, dann mit doppelt dem array hinzuf�gen
			if (in_array($id[$this->dzvhae_feld_flex_systemid], $liste)) {
				$liste[$id[$this->dzvhae_feld_flex_systemid]] = $id[$this->dzvhae_feld_flex_systemid] . "<strong>Doppelt</strong>";
			} // wenn kein leerer String ist, dann SystemId dem array hinzuf�gen
			elseif ($id[$this->dzvhae_feld_flex_systemid] != "") {
				$liste[$id[$this->dzvhae_feld_flex_systemid]] = $id[$this->dzvhae_feld_flex_systemid];
			}
		}
		// sch�n Sortieren
		sort($liste);
		// und ans Template weitergeben
		$this->content->template['mv_liste'] = $liste;
	}

	/**
	 * Z�hlt die Zeilen aller Dateien in den angegebenen Ordnern
	 */
	function count_file_rows()
	{
		// Ordner die durchsucht werden
		$ordners = array(
			"css",
			"messages",
			"sql",
			"lib",
			"templates"
		);

		//$ordners = array("lib/classes", "lib", "", "interna", "interna/templates/standard", "templates/standard", "templates/standard/_module_fix", "templates/standard/_module_var", "templates/standard/_module_intern", "lib/messages");
		// Z�hler f�r die Anzahl der Dateien, Zeilen und Zeichen
		$counter_dateien = 0;
		$counter_zeilen = 0;
		$counter_zeichen = 0;
		// Ausgabearray f�rs Template und Bufferarray f�rs Zeichenz�hlen
		$files = array();
		$file_array = array();

		// Loope alle ordner durch
		foreach ($ordners as $ordner) {
			$counter_array = 0;
			$counter_zeilen_ordner[$ordner] = 0;
			// Verzeichnis handle �ffnen
			$handle = opendir("../plugins/mv/" . $ordner);

			//$handle = opendir("../".$ordner);
			// lese Verzeichnis Dateiweise durch
			while ($file = readdir($handle)) {
				// wenns kein . .. .svn ist dann
				if ($file != "." && $file != ".." && $file != ".svn") {
					// speicher den Dateinamen zwischen
					$files[$ordner][$counter_array]['filename'] = $file;

					// ist es eine Datei
					if (is_file("../plugins/mv/" . $ordner . "/" . $file)) //if(is_file("../".$ordner."/".$file))
					{
						// lese Datei ein
						$file_array = file("../plugins/mv/" . $ordner . "/" . $file);
						//$file_array = file("../".$ordner."/".$file);
						if (!empty($file_array)) {
							// und Z�hle die Zeichen in der Datei
							foreach ($file_array as $file_string) {
								$counter_zeichen += strlen($file_string);
							}
						}
					} else {
						$file_array = array();
					}
					// speicher die Zeilen der Datei zwischen
					$files[$ordner][$counter_array]['zeilen'] = count($file_array);
					// Z�hler f�r die gesamt Zeilenanzahl hochz�hlen
					$counter_zeilen += $files[$ordner][$counter_array]['zeilen'];
					// Z�hler f�r die Anzahl der zeilen in diesem Ordner hochz�hlen
					$counter_zeilen_ordner[$ordner] += $files[$ordner][$counter_array]['zeilen'];
					// Z�hler f�r Anzahl der Dateien hochz�hlen
					$counter_dateien++;
					// Z�hler f�r den Array Index hochz�hlen
					$counter_array++;
				}
			}
			// handle f�r das Verzeichnis wieder schliessen
			closedir($handle);
		}
		// Ergebnis ans Template weitergeben
		$this->content->template['mv_out'] = "<strong>Anzahl der Dateien:</strong> " . $counter_dateien .
			"<br /><strong>Anzahl der Zeilen:</strong> " . $counter_zeilen .
			"<br /><strong>Anzahl der Zeichen:</strong> " . $counter_zeichen;
		$this->content->template['mv_out_liste'] = $files;
		$this->content->template['mv_out_ordner_zeilen'] = $counter_zeilen_ordner;
	}

	/**
	 * Macht aus einem jjjj.mm.tt hh:mm:ss Zeitstempel einen Unix Timestamp
	 */
	function longtime_to_unixtime($longtime)
	{
		if (!empty($longtime)) {
			// Hole aus dem Zeitstempel Jahr, Monat, Tag, Stunde, Minute und Sekunde raus
			list($datum, $uhrzeit) = explode(" ", $longtime);
			list($jahr, $monat, $tag) = explode(".", $datum);
			list($stunde, $minute, $sekunde) = explode(":", $uhrzeit);
			if (!empty($jahr) && !empty($monat) && !empty($tag) && !empty($stunde) && !empty($minute)
				&& !empty($sekunde)) {
				// mache aus den Einzelnen Datumswerten einen Unix Timestamp
				$unixtime = mktime($stunde, $minute, $sekunde, $monat, $tag, $jahr);
			} // Fehler: String ist nicht komplett, bzw. kein Zeitstempel im Format jjjj.mm.tt hh:mm:ss
			else {
				return (false);
			}
		} // Fehler: Ein leerer String wurde �bergeben
		else {
			return (false);
		}
		// gibt den neuen Zeitstempel zur�ck
		return ($unixtime);
	}

	/**
	 * Mache aus einem Unix Timestamp einen jjjj.mm.tt hh:mm:ss Zeitstempel
	 */
	function unixtime_to_longtime($unixtime)
	{
		// Fehler: Wenn leerer String �bergeben wurde
		$longtime = false;

		if (!empty($unixtime)) {
			// wenn der String nicht leer ist, dann wandel den Unix Timestamp in jjjj.mm.tt hh:mm:ss Zeitstempel um
			$longtime = date("Y.m.d H:i:s", $unixtime);
		}
		// gibt den neuen Zeitstempel zur�ck
		return ($longtime);
	}

	/**
	 * Mache aus einem jjjj.mm.tt hh:mm:ss Zeitstempel einen tt.mm.jjjj
	 * hh:mm:ss    Zeitstempel
	 */
	function longtime_to_germantime($longtime)
	{
		if (!empty($longtime)) {
			// Hole aus dem Zeitstempel Jahr, Monat, Tag, Stunde, Minute und Sekunde raus
			list($datum, $uhrzeit) = explode(" ", $longtime);
			list($jahr, $monat, $tag) = explode(".", $datum);
			list($stunde, $minute, $sekunde) = explode(":", $uhrzeit);
			if (!empty($jahr) && !empty($monat) && !empty($tag) && !empty($stunde) && !empty($minute)
				&& !empty($sekunde)) {
				$germantime = $tag . "." . $monat . "." . $jahr . " " . $stunde . ":" . $minute . ":" . $sekunde;
			} // Fehler: String ist nicht komplett, bzw. kein Zeitstempel im Fromat jjjj.mm.tt hh:mm:ss
			else {
				return (false);
			}
		} // Fehler: Ein leerer String wurde �bergeben
		else {
			return (false);
		}
		// gibt den neuen Zeitstempel zur�ck
		return ($germantime);
	}

	/**
	 *    Gibt aus einem jjjj.mm.tt hh:mm:ss Zeitstempel Tag, Monat, Jahr zur�ck
	 */
	function get_day_month_year($longtime)
	{
		// Hole aus dem Zeitstempel Jahr, Monat, Tag
		list($datum, $uhrzeit) = explode(" ", $longtime);
		list($jahr, $monat, $tag) = explode(".", $datum);
		// und gib die Werte zur�ck
		return (array(
			$tag,
			$monat,
			$jahr
		));
	}

	/**
	 * Gibt aus einem jjjj.mm.tt hh:mm:ss Zeitstempel Stunde, Minute, Sekunde
	 * zur�ck
	 */
	function get_sec_min_hour($longtime)
	{
		// Hole aus dem Zeitstempel Jahr, Monat, Tag, Stunde, Minute und Sekunde raus
		list($datum, $uhrzeit) = explode(" ", $longtime);
		list($jahr, $monat, $tag) = explode(".", $datum);
		list($stunde, $minute, $sekunde) = explode(":", $uhrzeit);
		// und gib die 3 Werte zur�ck
		return (array(
			$sekunde,
			$minute,
			$stunde
		));
	}

	#nicht entfernen

	/**
	 * Gibt aus den Sekunden, Minuten, Stunden, Tage, Monate, Jahre Werten ein
	 * "longtime" String zur�ck
	 */
	function get_longtime($jahr, $monat, $tag, $stunde = "00", $minute = "00", $sekunde = "00")
	{
		// ist nur ein Kompromiss, falls csv Dateien f�r Jahreszahlen nur 2 Ziffern haben
		// 0 bis 10 wird zu 2000 bis 2010 und 11 biss 99 wird zu 1911 bis 19999
		if (strlen($jahr) == 2) {
			if ($jahr < $this->import_timestamp_grenzwert) {
				$jahr = "20" . $jahr;
			} else {
				$jahr = "19" . $jahr;
			}
			// Flag hochz�hlen damit eine Nachricht kommt, das die Jahreszahlen nicht ganz "korrekt" waren
			$this->import_timestamp++;
		}
		$longtime = $jahr . "." . $monat . "." . $tag . " " . $stunde . ":" . $minute . ":" . $sekunde;
		return ($longtime);
	}

	/**
	 * Mache aus einem tt.mm.jj hh:mm:ss Zeitstempel einen 20jj.mm.tt hh:mm:ss
	 * Zeitstempel
	 */
	function dzvhaetime_to_longtime($dzvhaetime)
	{
		if (!empty($dzvhaetime)) {
			// Hole aus dem Zeitstempel Jahr, Monat, Tag, Stunde, Minute und Sekunde raus
			list($datum, $uhrzeit) = explode(" ", $dzvhaetime);
			list($tag, $monat, $jahr) = explode(".", $datum);
			if (!empty($jahr) && !empty($monat) && !empty($tag)) {
				// die 20 vor dem $jahr Wert kommt daher, das es sich um Editier- und Erstelldaten handelt und diese alle nach dem Jahr 2000 erstellt wurden
				$longtime = "20" . $jahr . "." . $monat . "." . $tag . " " . $uhrzeit;
			} // Fehler: String ist nicht komplett, bzw. kein Zeitstempel im Fromat tt.mm.jj hh:mm:ss
			else {
				return (false);
			}
		} // Fehler: Ein leerer String wurde �bergeben
		else {
			return (false);
		}
		// gibt den neuen Zeitstempel zur�ck
		return ($longtime);
	}

	/**
	 * Holt die W�hrung f�r ein Preisintervall Feld aus der Datenbank
	 */
	function get_feld_waehrung($feld_id, $mv_id)
	{
		$sql = sprintf("SELECT waehrung FROM %s
			WHERE mv_id='%d'
			AND feld_id='%d' LIMIT 1",
			$this->cms->tbname['papoo_mv_feld_preisintervall'], $this->db->escape($mv_id),
			$this->db->escape($feld_id));
		$feld_waehrung = $this->db->get_var($sql);
		return ($feld_waehrung);
	}

	/**
	 * Holt die Select-Optionen f�r ein Preisintervall-Feld aus der Datenbank
	 */
	function get_feld_intervalle($feld_id, $feld_name, $mv_id)
	{
		$sql = sprintf("SELECT intervalle,
			waehrung
			FROM %s
			WHERE mv_id = '%d'
			AND feld_id = '%d'
			LIMIT 1",
			$this->cms->tbname['papoo_mv_feld_preisintervall'],
			$this->db->escape($mv_id),
			$this->db->escape($feld_id)
		);
		$feld_werte = $this->db->get_results($sql, ARRAY_A);
		$feld_intervalle = $feld_werte[0]['intervalle'];
		$feld_waehrung = $feld_werte[0]['waehrung'];
		if (!empty($feld_intervalle)) {
			$feld_intervalle_array = explode("\r\n", $feld_intervalle);
			$select_min = $this->content->template['plugin']['mv']['preisintervall_min']
				. '<select name="mvcform_min_'
				. $feld_name
				. '_'
				. $feld_id
				. '" id="mvcform_min_'
				. $feld_name
				. '_'
				. $feld_id
				. '" size="1" >';
			$select_max = $this->content->template['plugin']['mv']['preisintervall_max']
				. '<select name="mvcform_max_'
				. $feld_name
				. '_'
				. $feld_id
				. '" id="mvcform_max_'
				. $feld_name
				. '_'
				. $feld_id
				. '" size="1" >';
			$select_min .= '<option value="">'
				. $this->content->template['plugin']['mv']['preisintervall_kein']
				. '</option>';
			$select_max .= '<option value="">'
				. $this->content->template['plugin']['mv']['preisintervall_kein']
				. '</option>';
			$wert_min = "mvcform_min_" . $feld_name . "_" . $feld_id;
			$wert_max = "mvcform_max_" . $feld_name . "_" . $feld_id;
			foreach ($feld_intervalle_array as $intervall_wert) {
				$intervallwert = trim($intervallwert);
				$select_min .= '<option value="' . $intervall_wert . '"';
				$select_max .= '<option value="' . $intervall_wert . '"';
				if ($this->checked->$wert_min == $intervall_wert) $select_min .= ' selected="selected"';
				if ($this->checked->$wert_max == $intervall_wert) $select_max .= ' selected="selected"';
				$select_min .= '>' . $intervall_wert . $feld_waehrung . '</option>';
				$select_max .= '>' . $intervall_wert . $feld_waehrung . '</option>';
			}
			$select_min .= "\n</select>\n";
			$select_max .= "\n</select>\n";
		}
		return ($select_min . $select_max);
	}

	// f�r usort() notwendig (called by search_user_front.php)
	static function cmp($a, $b)
	{
		global $sortby;
		return strcmp($a->$sortby, $b->$sortby);
		#return strcmp(iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $a->$sortby), iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $b->$sortby));
	}

	// Ermittelt die CSS-Klassen welche im (QuickTag-)Editor bereit gestellt werden sollen
	function get_css_klassen()
	{
		$css_klassen = array();

		// Pfad zur CSS-Datei

		$css_datei = PAPOO_ABS_PFAD . "/styles/" . $this->content->template['style_dir'] . "/css/_index.css";
		//echo "Pfad: ".$css_datei."<br />\n";

		if (file_exists($css_datei)) {
			$datei = fopen($css_datei, "r");
			$css = fread($datei, filesize($css_datei));
			//echo "Datei: ".$css."<br />\n";

			$css_array = array();

			// Suchmuster: (<text_in_klammern>)(text_nach_klammern),
			// daher auch das nonsense-Tag um Texte am Anfang, also vor einem Tag zu erfassen
			$match_expression = "/\.(.*)\{/i";
			// f�llt $text_array in der Art:
			//		$text_array[0] = Text des gesamten Suchmusters
			//		$text_array[1] = CSS-Klassen-Name(n)
			preg_match_all($match_expression, $css, $css_array, PREG_SET_ORDER);

			// die R�ckgabe zusammen stellen.
			foreach ($css_array as $css_definition) {
				$css_klassen[] = trim($css_definition[1]);
			}
			//print_r($css_klassen);
		}
		$this->content->template['css_data_klassen'] = $css_klassen;

		#return $css_klassen;
	}

	/**
	 * Prueft ob die Option 'Filter Front Text' in den Feld Optionen fuer
	 * dieses Feld gesetzt ist.
	 *
	 * Diese Option soll angeben ob der Text des Feldes auf XSS geprueft werden
	 * soll, oder einfach so durchgegeben werden soll.
	 *
	 * @param int $feld_id Die ID des Feldes, welches geprueft werden soll.
	 * @return bool Ob die Option fuer dieses Feld gesetzt ist.
	 */
	public function ist_inhalt_auf_xss_pruefen_gesetzt($feld_id)
	{
		$feld_id = intval($feld_id);
		$sql = <<<EOF
SELECT mvcform_filter_front
FROM {$this->cms->tbname['papoo_mvcform']}
WHERE mvcform_id = {$feld_id};
EOF;
		return (bool)$this->db->get_results($sql, ARRAY_A)[0]['mvcform_filter_front'];
	}
}

$mv = new mv();
