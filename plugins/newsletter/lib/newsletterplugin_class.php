<?php

/**
 * ########################################
 * # Papoo CMS                           #
 * # (c) Carsten Euwens 2007, khmweb 2010 #
 * # Authors: Dr. Carsten Euwens, khmweb  #
 * # http://www.papoo.de                  #
 * # Internet                             #
 * ########################################
 * # PHP Version >4.2                     #
 * ########################################
 */

/**
 * Class news
 */
class news
{
	/** @var string */
	var $news = "";
	/** @var bool Archiv direkt HTML Mails anzeigen... ohne frame true, false */
	var $show_html = false;

	/** @var string HTTP-Protokol zur Link- / Pfad-Ersetzung (damit auch https genutzt werden kann) */
	protected $protocol = 'http://';

	/**
	 * news constructor.
	 */
	function __construct()
	{
		// Für Rückwärts-Kompatibilität
		if (!function_exists('is_countable')) {
			function is_countable($var) {
				return (is_array($var) || $var instanceof Countable);
			}
		}

		global $cms, $db, $message, $user, $weiter, $content, $searcher, $checked, $diverse, $db_praefix, $intern_stamm, $mv;
		// Mail Klasse einbinden
		require_once(PAPOO_ABS_PFAD . "/lib/classes/class.phpmailer.php");
		require_once(PAPOO_ABS_PFAD . "/lib/classes/mail_it_class.php");
		$mail_it = new mail_it();

		ini_set("memory_limit", "256M");
		$this->mv = &$mv;
		$this->cms = &$cms;
		$this->db = &$db;
		$this->content = &$content;
		$this->message = &$message;
		$this->user = &$user;
		$this->weiter = &$weiter;
		$this->searcher = &$searcher;
		$this->checked = &$checked;
		$this->mail_it = &$mail_it;
		$this->intern_stamm = &$intern_stamm;
		$this->diverse = &$diverse;
		$this->db->hide_errors();
		//special Newsletter Data tables
		$this->papoo_newsletter = $db_praefix . "papoo_newsletter";
		$this->papoo_news_impressum = $db_praefix . "papoo_news_impressum";
		$this->papoo_news_user = $db_praefix . "papoo_news_user";
		$this->papoo_news_gruppen = $db_praefix . "papoo_news_gruppen";
		$this->papoo_news_protocol_user = $db_praefix . "papoo_news_protocol_user";
		if (file_exists(PAPOO_ABS_PFAD . '/plugins/newsletter/lib/newsletterplugin_addon_class.php')) {
			require_once PAPOO_ABS_PFAD . '/plugins/newsletter/lib/newsletterplugin_addon_class.php';
			$this->news_addon = new newsletter_addon();
		}
		$this->make_news();
	}

	/**
	 * Newsletter Start
	 */
	function make_news()
	{
		$this->content->template['menuid_aktuell'] = $this->checked->menuid;
		// �berpr�fen, ob Zugriff auf die Inhalte besteht

		#else $this->user->check_access();
		#if ($this->checked->template == "newsletter/templates/subscribe_newsletter.html")
		#{
		$_SESSION['nl']['admindaten'] = $this->get_admin_daten();
		$_SESSION['nl']['news_erw'] = $_SESSION['nl']['admindaten'][0]->news_erw;
		#}
		if (isset($this->checked->template) && $this->checked->template != "newsletter/templates/subscribe_newsletter_activated.html") {
			if ($this->checked->template == "newsletter/templates/subscribe_newsabo.html") {
				$this->content->template['is_iaks'] = "";
			}
			$this->content->template['newsletter_self'] = "./plugin.php?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template;
			$this->content->template['test_id'] = $_SESSION['nl']['test_id'] = $this->get_test_id(); // Id der Gruppe Test ermitteln
			if (defined('admin')) {
				$this->user->check_intern();
				$this->make_newsletter_link();
				$this->db->csrfok=true;
				// Konfigurations-Daten aus Papoo_news_impressum holen und an die Session �bergeben (0 = ausser POP3-Daten)
				$_SESSION['nl']['admindaten'] = $this->get_admin_daten();
				if (!empty($this->checked->template)) {
					#$this->user->check_intern();
					#$this->user->check_access();
					switch ($this->checked->template) {
						//Impressum bearbeiten
					case "newsletter/templates/newsimp.html":
						$this->make_imprint();
						break;

						// User bearbeiten / Adressen importieren
					case "newsletter/templates/newsabo.html":
						if (isset($this->checked->import) && $this->checked->import or
							isset($this->checked->is_uploaded) && $this->checked->is_uploaded
						) {
							$this->make_import();
						}
						else {
							$this->make_user();
						}
						break;

						// Import Fehlerprotokoll �bersicht anzeigen
					case "newsletter/templates/news_error_report.html":
						$this->news_error_report();
						break;

						// Import Fehlerprotokoll Details anzeigen
					case "newsletter/templates/news_error_report_details.html":
						$this->news_error_report_details();
						break;

						// Newsletter Gruppe einrichten/�ndern/l�schen
					case "newsletter/templates/news_group.html":
						$this->news_group_list();
						break;

						// Neue Gruppe anlegen
					case "newsletter/templates/news_group_new.html":
						$this->news_group_new();
						break;

						// Gruppe bearbeiten
					case "newsletter/templates/news_group_edit.html":
						$this->news_group_edit();
						break;

						// Neuen Newsletter erstellen
					case "newsletter/templates/news_nl_new.html":
						$this->news_nl_new();
						break;

						// Newsletter editieren
					case "newsletter/templates/news_nl_edit.html":
						$this->news_nl_edit();
						break;

						// Newsletter sichern
					case "newsletter/templates/new_sicherung.html":
						$this->news_dump();
						break;

						// Newsletter alt / neu / edit
					case "newsletter/templates/news_nl_list.html":
						$this->list_news();
						break;

						// Newsletter Protokoll
					case "newsletter/templates/news_protocol.html":
						$this->protocol();
						break;

						// Newsletter versenden
					case "newsletter/templates/news_nl_send.html":
						$this->nl_versand();
						break;

						// Newsletter versenden in steps
					case "newsletter/templates/news_nl_send_parts.html":
						$this->nl_versand_stepping();
						break;

						// Newsletter versenden in steps
					case "newsletter/templates/news_address_in_error.html":
						$this->show_invalid_mail_addresses();
						break;

					default:
						#$this->user->check_access();
						// Hauptmen�punkt Level 0 "Newsletter" newsletter_back.html
						$this->start_news();
						break;
					}
				}
				$this->db->csrfok=false;
			}
		}
	}

	function make_import()
	{
		$this->content->template['import'] = 1; // Import Template anzeigen
		$start = true;
		$this->max_counter_pro_runde = 50;
		$this->counter_start = isset($this->checked->counter_start) ? $this->checked->counter_start : NULL;
		if (empty($this->checked->counter_start)) {
			$this->checked->counter_start = 0;
			$this->counter_start = 0;
		}
		if (isset($this->checked->makeimport) && $this->checked->makeimport) {
			// check ob alle Feldnamen da sind
			$i = 0;
			if (count($_SESSION['nl_import']['first_line'])) {
				foreach ($_SESSION['nl_import']['first_line'] as $key => $value) {
					$value = strtoupper(trim($value));
					$k = $key + 1; // Pointer zur Position im Array, startet mit 1!
					if ($value == "NAME") {
						$i = $i | 1;
						$this->name = $k;
					}
					if ($value == "VORNAME") {
						$i = $i | 2;
						$this->vorname = $k;
					}
					if ($value == "STRASSE") {
						$i = $i | 4;
						$this->strasse = $k;
					}
					if ($value == "PLZ") {
						$i = $i | 8;
						$this->plz = $k;
					}
					if ($value == "ORT") {
						$i = $i | 16;
						$this->ort = $k;
					}
					if ($value == "MAIL") {
						$i = $i | 32;
						$this->mail = $k;
					}
				}
			}
			if (count($_SESSION['nl_import']['first_line']) != 6) $this->content->template['csv_error1'] = $fehler = 1; // Felderanzahl falsch
			elseif ($i != 63) $this->content->template['csv_error2'] = $fehler = 1; // irgendein Feld fehlt oder Name falsch
			if (!isset($fehler) || !$fehler) {
				$csv = $this->get_csv_content_rows();
				// CSV Datei in Array einlesen und dabei die G�ltigkeit der Daten checken
				$cvs_ar = $this->convert_to_array($csv); // File Array in fertiges Array konvertieren
				$this->insert_into_database($cvs_ar); // Daten eintragen
				$this->content->template['imported_records'] = $_SESSION['nl_import']['success_count'];
				$this->content->template['records_in_error'] = $_SESSION['nl_import']['records_in_error'];
				unlink(PAPOO_ABS_PFAD . "/dokumente/upload/" . basename($_SESSION['nl_import']['uploaded_file']));
				unset($_SESSION['nl_import']['csv_ar']);
				unset($_SESSION['nl_import']['error_count']);
				unset($_SESSION['nl_import']['first_line']);
				unset($_SESSION['nl_import']['records_in_error']);
				unset($_SESSION['nl_import']['report_id']);
				unset($_SESSION['nl_import']['success_count']);
				unset($_SESSION['nl_import']['import_daten']);
				unset($_SESSION['nl_import']['import_anz_zeilen']);
				unset($_SESSION['nl_import']['uploaded_file']);
			}
		}
		else {
			// Datei hochladen
			if (!empty($this->checked->startupload)) {
				if (!$this->do_upload()) {
					$this->content->template['is_uploaded'] = 1; // Upload hat stattgefunden
					$_SESSION['nl_import']['first_line'] = $this->lese_csv(); // 1. Zeile und alle Daten lesen
					// Import Fehlerprotokoll-Daten in die DB
					$sql = sprintf("INSERT INTO %s SET imported_by = '%s',
														used_file = '%s'",

						$this->cms->tbname['papoo_news_error_report'],

						$this->user->username,
						$this->db->escape($_FILES['myfile']['name'])
					);
					$this->db->query($sql);
					$_SESSION['nl_import']['report_id'] = $this->db->insert_id;

					//alle Verteilerlisten holen
					$sql = sprintf("SELECT news_gruppe_id, news_gruppe_name FROM %s",
						$this->cms->tbname['papoo_news_gruppen']
					);
					$result = $this->db->get_results($sql, ARRAY_A);
					$this->content->template['mailinglisten_list'] = $result;

				}
				else {
					$this->content->template['no_upload_msg'] = 1;
				} // Datei hochladen
				$this->content->template['is_start'] = 1;
			} // Ist noch Start, nix passiert, Upload Maske anbieten
			elseif ($start == true) {
				// CSV-Dateien zum l�schen
				if (isset($this->checked->file) && is_countable($this->checked->file) && count($this->checked->file)) {
					foreach ($this->checked->file AS $key => $value) {
						unlink(PAPOO_ABS_PFAD . "/dokumente/upload/" . $value);
					}
				}
				$this->show_csv_files();
				$this->content->template['is_start'] = "ok"; // Template auf Start setzen
				// Session zur�cksetzen
				unset($_SESSION['nl_import']['csv_ar']);
			}
		}
		return;
	}

	function show_csv_files()
	{
		$path = PAPOO_ABS_PFAD . "/dokumente/upload/";
		if (!is_dir("$path")) {
			die("<b>Error $path is not a directory?</b>");
		}
		$files = dir($path) or die("Error reading/opening $path");
		while ($a = $files->read()) {
			if (!empty($a)) $currentArray[] = $a;
		}
		sort($currentArray, SORT_REGULAR);
		for ($i = 0; $i < count($currentArray); $i++) {
			$current = $currentArray[$i];
			if (($current != "..") && ($current != ".")) {
				$f = strrev($current);
				$ext = substr($f, 0, strpos($f, "."));
				$ext = strrev($ext);
				if ($ext == "csv") {
					$filedata[$i]['name'] = $current;
					$filedata[$i]['size'] = filesize($path . $current);
					$filedata[$i]['date'] = date("d.m.Y H:i:s", filectime($path . $current));
				}
			}
		}
		IfNotSetNull($filedata);
		$this->content->template['filedata'] = $filedata;
	}

	/**
	 * @return int
	 */
	function do_upload()
	{
		$destination_dir = 'dokumente/upload';// Zielverzeichnis
		$extensions = array('php', 'php3', 'php4', 'phtml', 'cgi', 'pl'); // nicht erlaubte Dateiendungen
		$upload_do = new file_upload(PAPOO_ABS_PFAD); // Upload durchf�hren
		// Wenn Files hochgeladen wurden
		if (count($_FILES) > 0) {
			// Durchf�hren und falls etwas schief geht
			if (!$upload_do->upload($_FILES['myfile'], $destination_dir, 0, $extensions, 1)) {
				// Fehler aufgetreten
				if ($upload_do->error == "already exists") {
					// existiert schon, dann l�schen und nochmal hochladen
					unlink(PAPOO_ABS_PFAD . "/dokumente/upload/" . basename($upload_do->file['name']));
					if (!$upload_do->upload($_FILES['myfile'], $destination_dir, 0, $extensions, 1)) $falsch = 1; // Fehler
				} else $falsch = 1; // anderer Fehler
			}
		}
		// wenn der Upload geklappt hat, Daten eintragen.
		if (!isset($falsch) || $falsch != 1) {
			$filename = $upload_do->file['name'];
			#$filesize = $upload_do->file['size'];
			$download = "/dokumente/upload/" . $filename;
			$_SESSION['nl_import']['uploaded_file'] = $download;
		} // Fehler beim HochladenMeldung zeigen..
		else {
			$this->content->template['errortext'] = $this->content->template['message_20'] . "<p>$upload_do->error</p>";
		}
		$this->content->template['uploaded_filename'] = $_SESSION['nl_import']['uploaded_file'];
		IfNotSetNull($falsch);
		return $falsch;
	}

	/**
	 * Link zum Newsletterbereich f�r artikel erstellen
	 */
	function make_newsletter_link()
	{
		$sql = sprintf("SELECT menuid FROM %s WHERE menulink LIKE '%s' LIMIT 1 ",
			$this->cms->tbname['papoo_menuint'], "%news_nl_list%"
		);
		$menuid = $this->db->get_var($sql);
		$this->content->template['newsletter_link'] = "./plugin.php?menuid=" . $menuid . "&template=newsletter/templates/news_nl_new.html";
	}

	/**
	 * Spracheinstellungen ausgeben
	 *
	 * @param string $userlang
	 * @param bool $all
	 */
	function make_lang($userlang = "", $all = false)
	{
		$resultlang = $this->db->get_results("SELECT * FROM " . $this->cms->papoo_name_language . "  ");
		$resultlang2 = $_SESSION['nl']['admindaten'][0]->news_lang;
		$lang = explode(";", $resultlang2);
		$this->content->template['language_newsd'] = array();

		// Ausgew�hlte Sprachen zuweisen
		foreach ($resultlang as $rowlang) { // 7x, alle in Papoo vorhandenen Sprachen
			// checken, wenn Sprache gew�hlt ist
			// Lfd. Position (Array-Index) f�r ausgew�hlte Sprache (festgelegt in papoo_news_impressum) in vorhandenen gefunden?
			if (in_array($rowlang->lang_id, $lang)) {
				if ($all) { // f�r die newsimp.htm, diese zeigt immer alle Sprachen
					$lang_selected_by_user = 1;
				}
				if ($userlang) { // vergleich gegen vorgegebene Check-Daten ?
					$lang_selected_by_user = 0;
					foreach ($lang as $rowlang2) { // alle im NL verwendeten Sprachen aus den NL-Konfig-Daten
						if ($userlang == $rowlang2) { // Konfig-Daten = Vorgabe ?
							$lang_selected_by_user = 1; // ja
							break; // Abbruch foreach
						}
					}
					if (isset($rowlang2) && $rowlang->lang_id == $rowlang2) { // Vorgabe in den vorhandenen gefunden?
						$lang_selected_by_user = 1;
						$_SESSION['nl']['language_newsd'] = $rowlang2;
					}
					else {
						$lang_selected_by_user = 0;
					}
				}
				// Bei Versand eines Artikels als NL nur die im BE eingestellte Sprache anzeigen (keine Auswahl!)
				IfNotSetNull($lang_selected_by_user);
				if ($this->checked->reporeid AND $lang_selected_by_user == 0) ;
				else {
					array_push($this->content->template['language_newsd'],
						array(
							'language' => $rowlang->lang_long,
							'lang_id' => $rowlang->lang_id,
							'selected' => $lang_selected_by_user
						)
					);
				}
				// Select Boxen: Selectsize Parameter f�llen
				$this->content->template['selectitems'] = count($this->content->template['language_newsd']);
			}
			else {
				if ($all) { // f�r die newsimp.htm, diese zeigt immer alle Sprachen
					array_push($this->content->template['language_newsd'],
						array(
							'language' => $rowlang->lang_long,
							'lang_id' => $rowlang->lang_id,
							'selected' => 0
						)
					);
				}
			}
		}
	}

	/**
	 * Erstellt das Archiv f�r das Frontend
	 */
	function make_archiv()
	{
		$olddata = array();
		if (empty($this->checked->news_id)) { // dann alle versendeten NL anzeigen
			$old = $this->get_all_news(1, "");  // 1 = Frontend; Gruppe Test im Frontend nicht anzeigen
			if (!empty($old)) {
				foreach ($old as $data) {
					$inhalt = substr(strip_tags($data->news_inhalt), 0, 200);
					array_push($olddata,
						array(
							'news_id' => $data->news_id,
							'news_inhalt' => stripslashes($inhalt),
							'news_uberschrift' => stripslashes(strip_tags(($data->news_header))),
							'news_date' => $data->news_date,
							'news_abo' => $data->news_abbonennten,
							'news_gruppe' => $data->news_gruppe
						)
					);
				}
			}
			$this->content->template['old_news'] = $olddata;
		}
		else {
			// einen bestimmten NL anzeigen
			$oldnews = $this->get_old_news($this->checked->news_id);

			if ($this->show_html) {
				print_r(utf8_decode($oldnews['0']->news_inhalt_html));
				die();
			}
			if (!empty($oldnews)) {
				foreach ($oldnews as $data) {
					$data->news_inhalt = str_ireplace($data->news_header, "", $data->news_inhalt);
					$data->news_inhalt_html = str_ireplace($data->news_header, "", $data->news_inhalt_html);
					array_push($olddata,
						array(
							'news_id' => $data->news_id,
							'news_uberschrift' => stripslashes(strip_tags(($data->news_header))),
							'news_inhalt' => stripslashes($data->news_inhalt),
							'news_inhalt_html' => ($data->news_inhalt_html),
							'news_date' => $data->news_date,
							'news_abo' => $data->news_abbonennten
						)
					);
				}
			}
			$this->content->template['old_news_inhalt'] = $olddata;
		}
	}

	/**
	 * alles f�rs Frontend
	 */
	function post_papoo()
	{
		// Archiv Anzeige Frontend
		if (isset($this->checked->template) && $this->checked->template == "newsletter/templates/news_archiv.html") {
			$this->make_archiv();
		}
		else {
			if (isset($this->checked->template) && $this->checked->template == "newsletter/templates/subscribe_newsletter_activated.html") {
				$this->show_account_is_active();
			}
			else {
				if (isset($this->checked->template) && $this->checked->template == "newsletter/templates/subscribe_newsletter.html"
					or !empty($this->checked->loginnow)
				) {
					//if (!is_array($this->checked->nlgruppe))  // 1 = Frontend; dort die Gruppe "Test" nicht ausgeben
					//else $_SESSION['nl']['nlgruppe'] = $this->checked->nlgruppe;
					// Formular zum Abonnieren des NL wurde aufgerufen bei true
					if (empty($this->checked->news_message)) {
						$this->init_template();
					}
					else {
						switch ($this->checked->news_message) {
							// Es ist subscribiert worden, daher Mail schicken zur Best�tigung
							// Oder ein Eingabe-Fehler im Formular ist aufgetreten
						case "new_user":
							$this->subscribe_and_mail_user();
							break;

							// Es ist eine neue Subscription get�tigt worden
						case "is_send":
							// Neuen User eintragen
							$this->show_message_is_send();
							break;

							// Es ist eine Antwortmail angekommen, also checken und eintragen wenn ok
						case "activate":
							// User aktivieren
							$this->activate_user();
							break;

							// Es ist eine Antwortmail angekommen, wurde gecheckt also Best�tigung im Frontend ausgeben
							#case "is_active":
							#	$this->show_account_is_active();
							#	break;

							// Es soll ein Account gel�scht werden
						case "de_activate":
							if ($_SERVER['REQUEST_METHOD'] == 'POST') {
								$this->delete_account();
							}
							else {
								$this->content->template['requireUnsubscribeConfirmation'] = true;
							}
							break;

							// Es ist ein Account gel�scht worden
						case "is_deactivated":
							$this->show_message_deleted();
							break;
						}
					}
				}
			}
		}
		if ($this->news_addon != null) {
			$this->news_addon->post_papoo();
		}
	}

	/**
	 * Init Template
	 */
	function init_template()
	{
		$this->content->template['new_newsletter'] = "Neu";
		$this->make_lang();

		// Sollen erweiterte Userdaten angezeigt werden?
		if ($_SESSION['nl']['news_erw'] == 1) {
			$this->content->template['news_erw'] = 1;
			$table_datax = array();
			array_push($table_datax,
				array('checkgendermale' => 'nodecode:checked="checked"')
			);
			$this->content->template['table_datax'] = $table_datax;
		}
		$this->content->template['news_sprachwahl'] = $_SESSION['nl']['admindaten'][0]->news_sprachwahl;
		$this->content->template['news_anzeig_message'] = $_SESSION['nl']['admindaten'][0]->news_anzeig_message;
		$this->all_nlgroups_to_template("", "", 1); // 1 = Frontend, Gruppe Test nicht anzeigen
		return;
	}

	/**
	 * Einen NL l�schen
	 *
	 * @param string $del
	 */
	function newsletter_del_entry($del = "")
	{
		// Soll lt. Konfig wirklich gel�scht werden?
		#if ($_SESSION['nl']['admindaten'][0]->news_allow_delete)
		#{
		$sql = sprintf("DELETE FROM %s WHERE news_id='%d'",
			$this->cms->tbname['papoo_newsletter'],
			$this->db->escape($del)
		);
		$this->db->query($sql);
		$this->content->template['news_is_del'] = 1;
		// alle Attachments l�schen
		$sql = sprintf("SELECT name_stored FROM %s WHERE news_id='%s'",
			$this->cms->tbname['papoo_news_attachments'],
			$this->db->escape($del)
		);
		$name_stored = $this->db->get_results($sql, ARRAY_A);
		// Verzeichnis
		$destination_dir = PAPOO_ABS_PFAD . '/plugins/newsletter/attachments';
		// basedir und Klasse file_upload bekanntmachen
		$upload_do = new file_upload();
		if (count($name_stored)) {
			foreach ($name_stored AS $key => $value) {
				file_exists($destination_dir . $value['name_stored'])
					? $status = 1 : $status = 0;
				if ($status) { // Datei im Verzeichnis l�schen, wenn sie da ist
					// Upload ((array('filename','filetype','filesize','tmp_name','error')), dir, rename, disallowed ext, block)
					$upload_do->delete_file($value['name_stored'], $destination_dir);
				}
			}
		}
		$sql = sprintf("DELETE FROM %s
									WHERE news_id='%d'",
			$this->cms->tbname['papoo_news_attachments'],
			$this->db->escape($del));
		$this->db->query($sql);
	}

	/**
	 * Eine NL Gruppe l�schen. Gruppe wird auch aus den Abonennten entfernt, die sich hierf�r angemeldet haben
	 *
	 * @param string $del
	 */
	function group_del_entry($del = "")
	{
		if ($del != 1) {
			$this->content->template['grp_is_del'] = 1;
			// Soll lt. Konfig wirklich gel�scht werden?
			#if ($_SESSION['nl']['admindaten'][0]->news_allow_delete)
			#{
			if(!isset($this->checked->makeimport_leeren) || isset($this->checked->makeimport_leeren) && !$this->checked->makeimport_leeren) {
				$sql = sprintf("DELETE FROM %s WHERE news_gruppe_id = '%d'",
					$this->papoo_news_gruppen,
					$del
				);
				$this->db->query($sql);
			}
			//lookup auch löschen
			$sql = sprintf("DELETE FROM %s WHERE news_gruppe_id_lu = '%d'",
				DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
				$del
			);
			$this->db->query($sql);

			// Die Gruppe aus den zugeordneten Usern entfernen
			$sql = sprintf("SELECT news_user_id,
										news_gruppen
										FROM %s
										WHERE news_gruppen LIKE %s
										OR news_gruppen LIKE %s",
				$this->papoo_news_user,
				'"' . $del . ' %"',
				'"% ' . $del . ' %"'
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			if (count($result)) {
				foreach ($result AS $key => $value) {
					if (strlen($value['news_gruppen']) == strlen($del) + 1) {
						$new_news_gruppen = ""; // steht allein am Anfang (keine weiteren)
					}
					elseif (substr($value['news_gruppen'], strlen($value['news_gruppen']) - strlen($del) - 2) == " " . $del . " ") {
						$new_news_gruppen = substr($value['news_gruppen'], 0, strlen($value['news_gruppen']) - strlen($del) - 1); // steht am Ende
					}
					elseif (strpos($value['news_gruppen'], $del . " ") > 0) {
						$new_news_gruppen = str_replace($del . " ", "", $value['news_gruppen']); // steht mittendrin
					}
					else {
						$new_news_gruppen = substr($value['news_gruppen'], strlen($del) + 1); // steht am Anfang plus weitere
					}
					$sql = sprintf("UPDATE %s SET news_gruppen = '%s' WHERE news_user_id = '%d'",
						$this->papoo_news_user,
						$new_news_gruppen,
						$value['news_user_id']
					);
					$this->db->query($sql);
				}
			}
		}
		else {
			$this->content->template['grp_is_del'] = 2;
		}
	}

	/**
	 * Alte NL Daten holen oder alten NL l�schen/als gel�scht kennzeichnen (Backend)
	 */
	function list_news()
	{
		// Meldung NL an X von X Abonnenten verschickt
		if (isset($this->checked->anz_g) AND (string)ctype_digit($this->checked->anz_g)) {
			if (isset($this->checked->anz_a) AND (string)ctype_digit($this->checked->anz_a)) {
				$this->content->template['text1'] =
					sprintf(NEWS_NL_SEND_TO_X_SUBSCRIBERS, $this->checked->anz_g, $this->checked->anz_a);
			}
		}
		// Meldung NL wurde gespeichert aul�sen, wenn sie �bergeben wurde
		$this->content->template['is_saved'] = isset($this->checked->news_message) ? $this->checked->news_message : NULL;

		// Link zur Liste fehlerhafter E-Mail-Adressen anzeigen, falls beim Versand welche festgestellt wurden
		$this->content->template['mie'] = isset($this->checked->mie) ? $this->checked->mie : NULL;
		$this->content->template['address_in_error'] = isset($_SESSION['nl']['address_in_error']) ? $_SESSION['nl']['address_in_error'] : NULL;
		$olddata = array();
		//  Eintrag l�schen?
		if (!empty($this->checked->news_del)) {
			$this->newsletter_del_entry($this->checked->news_del);
		}
		if (empty($this->checked->news_id)) {
			// Die Daten raussuchen
			$old = $this->get_all_news(0, "");
			if (!empty($old)) {
				foreach ($old as $data) {
					$grp = $this->get_group($data->news_nlgruppe);
					$grp = $grp[0];

					$inhalt = substr(strip_tags($data->news_inhalt), 0, 30);
					array_push($olddata,
						array(
							'news_id' => $data->news_id,
							'news_inhalt' => stripslashes($inhalt),
							'news_uberschrift' => stripslashes(strip_tags(($data->news_header))),
							'news_date' => $data->news_date,
							'news_date_send' => $data->news_date_send,
							'news_abo' => $data->news_abbonennten,
							'news_inhalt_lang' => $data->news_inhalt_lang,
							'news_nlgruppe' => $grp->news_gruppe_name,
							'news_gruppe' => $data->news_gruppe,
							'att_count' => $this->count_attachments($data->news_id)
						)
					);
				}
			}
		}
		else {
			$oldnews = $this->get_old_news($this->checked->news_id);
			foreach ($oldnews as $data) {
				$data->news_inhalt = str_ireplace($data->news_header, "", $data->news_inhalt);
				$data->news_inhalt_html = str_ireplace($data->news_header, "", $data->news_inhalt_html);
				array_push($olddata,
					array(
						'news_id' => $data->news_id,
						'news_uberschrift' => stripslashes(strip_tags(($data->news_header))),
						'news_inhalt' => stripslashes($data->news_inhalt),
						'news_inhalt_html' => ($data->news_inhalt_html),
						'news_date' => $data->news_date,
						'news_date_send' => $data->news_date_send,
						'news_abo' => $data->news_abbonennten
					)
				);
			}
		}
		// Sprach-IDs umsetzen
		if ($olddata) {
			$search = array("'1'", "'2'", "'3'", "'4'", "'5'", "'6'", "'7'");
			$replace = array("de", "en", "it", "es", "fr", "pt", "nl");
			foreach ($olddata as $key => $a1) {
				if (empty($olddata[$key]['news_inhalt_lang'])) {
					$olddata[$key]['news_inhalt_lang'] = "de";
				}
				else {
					$olddata[$key]['news_inhalt_lang'] =
						preg_replace($search, $replace, $olddata[$key]['news_inhalt_lang'], 1);
				}
			}
		}
		$this->content->template['old_news'] = $olddata;
	}

	/**
	 * Protokoll anzeigen (derzeit nur NL versendet an...)
	 */
	function protocol()
	{
		$olddata = array();
		if (empty($this->checked->news_id)) {
			$external_where = "news_date_send != ''";
			// Die Daten raussuchen
			$old = $this->get_all_news(0, $external_where);
			if (!empty($old)) {
				foreach ($old as $data) {
					array_push($olddata,
						array(
							'news_id' => $data->news_id,
							'news_uberschrift' => stripslashes(strip_tags(($data->news_header))),
							'news_date' => $data->news_date,
							'news_date_send' => $data->news_date_send,
							'news_abo' => $data->news_abbonennten,
							'news_inhalt_lang' => $data->news_inhalt_lang
						)
					);
				}
			}
			$this->content->template['anzahl_gesendet'] = is_array($old) ? count($old) : 0;
		}
		else {
			$this->weiter->weiter_link = "./plugin.php?menuid="
				. $this->checked->menuid
				. "&news_id="
				. $this->checked->news_id
				. "&template=newsletter/templates/news_protocol.html";
			$sql = sprintf("SELECT COUNT(news_user_email) FROM %s
									WHERE news_id = '%d'",
				$this->cms->tbname['papoo_news_protocol_user'],
				$this->db->escape($this->checked->news_id)
			);
			$this->content->template['anzahl_abonennten'] = $this->weiter->result_anzahl = $this->db->get_var($sql);
			$this->weiter->make_limit(20);
			$what = "teaser";
			$this->weiter->do_weiter($what);
			$sql = sprintf("SELECT * FROM %s
									WHERE news_id = '%d'
									ORDER BY news_user_email
									%s",
				$this->cms->tbname['papoo_news_protocol_user'],
				$this->db->escape($this->checked->news_id),
				$this->weiter->sqllimit
			);
			$this->content->template['recipients'] = $this->db->get_results($sql, ARRAY_A);
		}
		// Sprach-IDs umsetzen
		if ($olddata) {
			$search = array("'1'", "'2'", "'3'", "'4'", "'5'", "'6'", "'7'");
			$replace = array("de", "en", "it", "es", "fr", "pt", "nl");
			foreach ($olddata as $key => $a1) {
				if (empty($olddata[$key]['news_inhalt_lang'])) {
					$olddata[$key]['news_inhalt_lang'] = "de";
				}
				else {
					$olddata[$key]['news_inhalt_lang'] =
						preg_replace($search, $replace, $olddata[$key]['news_inhalt_lang'], 1);
				}
			}
		}
		$this->content->template['nl_send_to'] = $olddata;
	}

	/**
	 * Einen vorgegebenen NL holen
	 *
	 * @param $value
	 * @return array|void
	 */
	function get_old_news($value)
	{
		$sql = sprintf("SELECT * FROM %s WHERE news_id = '%d'",
			$this->cms->tbname['papoo_newsletter'],
			$this->db->escape($value)
		);
		$result = $this->db->get_results($sql);
		return $result;
	}

	/**
	 * Eine vorgegebene NL Gruppe holen
	 *
	 * @param $value
	 * @return array|void
	 */
	function get_group($value)
	{
		$sql = sprintf("SELECT * FROM %s WHERE news_gruppe_id = '%d'",
			$this->papoo_news_gruppen,
			$this->db->escape($value)
		);
		$result = $this->db->get_results($sql);
		return $result;
	}

	/**
	 * Alle alten NL holen
	 *
	 * @param int $front
	 * @param string $external_where
	 * @return array|void
	 */
	function get_all_news($front = 0, $external_where = "")
	{
		$table = $external_where ? $this->cms->tbname['papoo_news_protocol_newsletter'] : $this->papoo_newsletter;
		$this->weiter->weiter_link = "./plugin.php?menuid=" . $this->checked->menuid . "&template=";
		if (!$front) {
			#if (!$show_deleted) $where = "WHERE deleted != 1";
			if ($external_where) {
				$this->weiter->weiter_link .= "newsletter/templates/news_protocol.html";
			}
			else {
				$this->weiter->weiter_link .= "newsletter/templates/news_nl_list.html";
			}
		}
		else {
			// Gruppe Test von der Anzeige im Frontend ausschliessen und
			// auch nur die bereits gesendeten anzeigen.
			// Nicht gesendete sind NLs "in work"
			#if (!$show_deleted) $where = " WHERE news_nlgruppe NOT LIKE '" . $_SESSION['nl']['test_id'] . " %' AND news_send = 1 AND deleted != 1";
			#else  $where = " WHERE news_nlgruppe NOT LIKE '" . $_SESSION['nl']['test_id'] . " %' AND news_send = 1";
			$where = " WHERE news_nlgruppe NOT LIKE '" . $_SESSION['nl']['test_id'] . " %' AND news_send = 1";
			$this->weiter->weiter_link .= "newsletter/templates/news_archiv.html";
		}
		IfNotSetNull($where);
		if ($external_where) {
			$where = $where ? $where . " AND " . $external_where : "WHERE " . $external_where;
		}
		IfNotSetNull($where);
		$sql = sprintf("SELECT COUNT(news_id) FROM %s %s",
			$table,
			$where
		);
		$this->weiter->result_anzahl = $this->content->template['anzahl'] = $this->db->get_var($sql);

		$sql = sprintf("SELECT COUNT(news_id) FROM %s
								WHERE news_date_send != ''",
			$table
		);
		$this->content->template['anzahl_gesendet'] = $this->db->get_var($sql);
		$this->weiter->make_limit(10);
		// wenn es sie gibt, weitere Seiten anzeigen
		$what = "teaser";
		$this->weiter->do_weiter($what);
		$sql = sprintf("SELECT * FROM %s %s
								ORDER BY news_id DESC %s",
			$table,
			$where,
			$this->weiter->sqllimit
		);
		$result = $this->db->get_results($sql);
		return $result;
	}

	/**
	 * Alle NL Gruppen aus der Datenbank holen
	 *
	 * @return array|void
	 */
	function get_all_nl_groups()
	{
		$sql = sprintf("SELECT COUNT(news_gruppe_id)
									FROM %s",
			$this->papoo_news_gruppen
		);
		$anzahl = $this->db->get_var($sql);
		$this->content->template['anzahl'] = $anzahl;

		$sql = sprintf("SELECT * FROM %s
								ORDER BY news_gruppe_name",
			$this->papoo_news_gruppen
		);
		$result = $this->db->get_results($sql);
		return $result;
	}

	/**
	 * Alle NL Gruppen holen
	 *
	 * @return array|void
	 */
	function get_all_sys_groups()
	{
		$sql = sprintf("SELECT COUNT(gruppeid) FROM %s",
			$this->cms->papoo_gruppe
		);
		$anzahl = $this->db->get_var($sql);
		$this->content->template['anzahl'] = $anzahl;
		#$this->weiter->make_limit(10);

		// Anzahl der Ergebnisse aus der Suche Eigenschaft �bergeben
		#$this->weiter->result_anzahl = $anzahl;
		//$location_url = "./plugin.php?menuid=".$this->checked->menuid."&template=newsletter/templates/newsabo.html";
		#$this->weiter->weiter_link = "./plugin.php?menuid=" . $this->checked->menuid .
		#							"&template=newsletter/templates/news_group.html";
		// wenn es sie gibt, weitere Seiten anzeigen
		#$what = "teaser";
		#$this->weiter->do_weiter( $what );

		$sql = sprintf("SELECT gruppeid,
								gruppenname,
								gruppen_beschreibung
								FROM %s
								ORDER BY gruppenname",
			$this->cms->papoo_gruppe
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Die Newsletter Daten dumpen
	 */
	function news_dump()
	{
		if ($this->checked->makedump == 1) {
			global $db_praefix;
			$this->lokal_db_praefix = $db_praefix;
			$table_list = array();
			$query = "SHOW TABLES";
			$result = $this->db->get_results($query);

			if ($result) {
				foreach ($result as $eintrag) {
					if ($eintrag) {
						foreach ($eintrag as $titel => $tabellenname) {
							// "xxx" wird ben�tigt, da der praefix an Stelle 0 beginnt und strpos damit false w�re
							if (strpos("xxx" . $tabellenname, $this->lokal_db_praefix)) {
								if (stristr($tabellenname, "news")) {
									$temp_array = array("tablename" => $tabellenname);
									$table_list[] = $temp_array;
								}
							}
						}
					}
				}
			}
			$this->intern_stamm->make_dump($table_list);
		}
		if ($this->checked->backdump == 1) {
			$this->intern_stamm->make_restore();
		}
	}

	function show_message_is_send()
	{
		// Message aus der Datenbank holen und zuweisen
		$this->content->template['gesendet_newsletter'] = $this->get_message_news(1);
	}

	function show_account_is_active()
	{
		// Message aus der Datenbank holen und zuweisen
		$this->content->template['gesendet_newsletter'] = $this->get_message_news(2);
	}

	function show_message_deleted()
	{
		// Message aus der Datenbank holen und zuweisen
		$this->content->template['gesendet_newsletter'] = $message = $this->get_message_news(3);
	}

	/**
	 * Die entsprerchende message raussuchen die ausgegeben werden soll
	 *
	 * @param $value
	 * @return mixed
	 */
	function get_message_news($value)
	{
		if ($value == 1) {
			$result = $this->content->template['news_front1'];
		}
		if ($value == 2) {
			$result = $this->content->template['news_front2'];
		}
		if ($value == 3) {
			$result = $this->content->template['news_front3'];
		}
		IfNotSetNull($result);
		return $result;
	}

	// Start News bietet den Starttext an (newsletter_back.html)
	function start_news()
	{
		$this->content->template['start'] = 1;
	}

	/**
	 * Impressum anzeigen/erstellen
	 */
	function make_imprint()
	{
		if (!empty($this->checked->submit)) { // Update
			$exist = $_SESSION['nl']['admindaten'][0];
			if (empty($exist)) {
				$sql = sprintf("INSERT INTO '%s'
						SET news_impressum = '%s'",
					$this->cms->tbname['papoo_news_impressum'],
					"Inhalt"
				);
				$this->db->query($sql);
			}

			// Sprachen
			if (empty($this->checked->lang)) {
				$lang = empty($this->checked->lang) ? 1 : NULL;
			}
			else {
				$lang = "";
				$sql = sprintf("DELETE FROM %s
										WHERE news_imp_id = '1'",
					$this->cms->tbname['papoo_news_imp_lang']
				);
				$this->db->query($sql);
				foreach ($this->checked->lang as $rein) {
					$lang .= ";" . $rein;
					#if (empty($this->checked->inhalt_html[$rein])) $this->checked->inhalt_html[$rein] = "x";
					$sql = sprintf("INSERT INTO %s SET
							news_imp_imp='%s',
							news_imp_imp_html='%s',
							news_imp_lang_id='%s',
							news_imp_id='1'",
						$this->cms->tbname['papoo_news_imp_lang'],
						$this->db->escape(@$this->checked->inhalt[$rein]),
						$this->db->escape(@$this->checked->inhalt_html[$rein]),
						$this->db->escape($rein)
					);
					$this->db->query($sql);
				}
			}
			IfNotSetNull($this->checked->email);
			IfNotSetNull($this->checked->news_html);
			IfNotSetNull($this->checked->email_name);
			IfNotSetNull($this->checked->wyswig);
			IfNotSetNull($this->checked->erw);
			IfNotSetNull($this->checked->news_sprachwahl);
			IfNotSetNull($this->checked->news_anzeig_message);
			IfNotSetNull($this->checked->mails_per_step);
			IfNotSetNull($this->checked->allow_delete);
			// Daten eintragen
			$sql = sprintf("UPDATE %s SET
					news_email='%s',
					news_html='%s',
					news_name='%s',
					wyswig='%s',
					news_erw='%s',
					news_sprachwahl='%s',
					news_anzeig_message='%s',
					news_lang='%s',
					mails_per_step = '%d',
					news_allow_delete='%d'",
				$this->papoo_news_impressum,
				$this->db->escape($this->checked->email),
				$this->db->escape($this->checked->news_html),
				$this->db->escape($this->checked->email_name),
				$this->db->escape($this->checked->wyswig),
				$this->db->escape($this->checked->erw),
				$this->db->escape($this->checked->news_sprachwahl),
				$this->db->escape($this->checked->news_anzeig_message),
				$this->db->escape($lang),
				$this->db->escape($this->checked->mails_per_step),
				$this->db->escape($this->checked->allow_delete)
			);
			$this->db->query($sql);
			// Konfig-Daten in der Session erneuern
			$_SESSION['nl']['admindaten'] = $this->get_admin_daten();
		}
		$this->make_lang("", 1); // 1 = alle vorhandenen anzeigen

		// Admin-Formular anbieten
		$this->content->template['case2'] = "ok";
		if (!empty($_SESSION['nl']['admindaten'])) {
			foreach ($_SESSION['nl']['admindaten'] as $new) {
				$this->content->template['email'] = "nodecode:" . $new->news_email;
				$this->content->template['email_name'] = "nodecode:" . $new->news_name;
				$this->content->template['mails_per_step'] = $new->mails_per_step;

				$this->content->template['checkederw'] = $new->news_erw == 1 ? "nodecode:checked=\"checked\"" : NULL;
				$this->content->template['checkedwyswig'] = $new->wyswig == 1 ? "nodecode:checked=\"checked\"" : NULL;
				$this->content->template['checkednews_html'] = $new->news_html == 1 ? "nodecode:checked=\"checked\"" : NULL;
				$this->content->template['checkednews_anzeig_message'] = $new->news_anzeig_message == 1 ? "nodecode:checked=\"checked\"" : NULL;
				$this->content->template['checkednews_sprachwahl'] = $new->news_sprachwahl == 1 ? "nodecode:checked=\"checked\"" : NULL;
				$this->content->template['checked_allow_delete'] = $new->news_allow_delete == 1 ? "nodecode:checked=\"checked\"" : NULL;
			}
			$sql = sprintf("SELECT * FROM %s",
				$this->cms->tbname['papoo_news_imp_lang']
			);
			$result_l = $this->db->get_results($sql);
			if (!empty($result_l)) {
				foreach ($result_l as $la) {
					$id = $la->news_imp_lang_id;
					$array1[$id] = "nodecode:" . $la->news_imp_imp;
					$array2[$id] = "nodecode:" . $la->news_imp_imp_html;
				}
				IfNotSetNull($array1);
				IfNotSetNull($array2);
				$this->content->template['inhalt'] = $array1;
				$this->content->template['inhalt_html'] = $array2;
			}
		}
	}

	/**
	 * NL Konfigurationsdaten bereitsstellen
	 *
	 * @return array|void
	 */
	function get_admin_daten()
	{
		$sql = sprintf("SELECT * FROM %s LIMIT 1",
			$this->papoo_news_impressum
		);
		return $this->db->get_results($sql);
	}

	/**
	 * ID der Gruppe Test ermitteln
	 *
	 * @return array|null
	 */
	function get_test_id()
	{
		$sql = sprintf("SELECT news_gruppe_id FROM %s WHERE news_gruppe_name = '%s' ",
			$this->papoo_news_gruppen,
			"Test"
		);
		return $this->db->get_var($sql);
	}

	/**
	 * Content aus einem Artikel rausholen
	 */
	function get_article_content()
	{
		$text = "";
		if (!empty($this->checked->reporeid)) {
			$sql = sprintf("SELECT * FROM %s
										WHERE lan_repore_id = '%d'
										AND lang_id = '%d'
										LIMIT 1",
				$this->cms->tbname['papoo_language_article'],
				$this->db->escape($this->checked->reporeid),
				$this->db->escape($this->cms->lang_back_content_id)
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			if ($result) {
				if (!isset($result['0']['lan_teaser'])) {
					$result['0']['lan_teaser'] = NULL;
				}

				$text = $result['0']['lan_teaser'] . $result['0']['lan_article'];
				$site = $this->protocol . $this->content->template['site_name'] . PAPOO_WEB_PFAD;
				$text = str_ireplace("href=\"index", "href=\"" . $site . "/index", $text);
				$text = str_ireplace("href=\"plugin", "href=\"" . $site . "/plugin", $text);
				$text = str_ireplace("href=\"forum", "href=\"" . $site . "/forum", $text);
				$text = str_ireplace("href=\"guest", "href=\"" . $site . "/guest", $text);
				$text = str_ireplace("href=\"/", "href=\"" . $site . "/", $text);
				$text = str_ireplace("href=\"\./", "href=\"" . $site . "/", $text);
				//img src="./images
				$text = str_ireplace("src=\"\./images", "src=\"" . $site . "/images", $text);
				$text = str_ireplace("src=\"\.\./images", "src=\"" . $site . "/images", $text);

				$this->checked->linkname = $result['0']['header'];
			}
		}
		$this->checked->inhalt = "";
		$this->checked->inhalt_html = $text;
		return;
	}

	/**
	 * Newsletter erstellen, versenden und speichern
	 */
	function news_nl_new()
	{
		if (isset($this->checked->news_id) && $this->checked->news_id) {
			$this->content->template['news_id'] = $this->checked->news_id; // empty beim 1. Mal
		}
		// Bei Versand eines Artikels als NL (Indiz: teporeid) nur die im BE eingestellte Sprache anzeigen (keine Auswahl!)
		if (isset($this->checked->reporeid)) {
			$this->checked->news_lang = $this->cms->lang_back_content_id;
		}
		$this->make_lang($this->checked->news_lang); // Neuer NL, nicht aus Artikeldaten
		if (isset($this->checked->submit_file)) { // Upload Attachment
			// Uploads erst, wenn Betreff und Nachricht vorhanden sind
			if ($this->check_inputs_and_store_to_session_and_template() AND !$this->checked->news_id) {
				$this->content->template['no_upload_now'] = 1; // Fehler, Template: noch keine Uploads erlaubt
			}
			else {
				// Beim 1. Upload alle Daten speichern, sonst haben wir eine Leiche (Att. ohne NL-Inhalt),
				// wenn der User nach dem Upload aus dem Formular einfach rausgeht/rausfliegt
				if (!$this->checked->news_id) {
					$this->nl_save("INSERT INTO"); // 1. Aufruf noch keine id
					$this->content->template['news_id'] = $this->checked->news_id; // aber nun
				}
				$this->news_attachment_upload();
				$this->content->template['no_upload_now'] = 0; // jetzt d�rfen uploads kommen
				$this->getAttachmentsList();
			}
		}
		elseif (isset($this->checked->submit_del)) {
			$this->check_inputs_and_store_to_session_and_template();
			$this->news_attachment_delete();
			$this->getAttachmentsList();
		}
		if (isset($this->checked->submit_del) OR isset($this->checked->submit_file)) {
			$this->show_form(1);
		}
		else {
			IfNotSetNull($this->checked->submit);
			switch ($this->checked->submit) {
			case $this->content->template['plugin']['newsletter']['submit']['save']: // Speichern
				// Fehler im Form festgestellt?
				// check_inputs_and_store_to_session_and_template: Daten in die Session und zur�ck ins Template via if
				if ($this->check_inputs_and_store_to_session_and_template()) {
					$this->show_form(1);
				}
				else {
					$this->checked->news_id ? $this->nl_save("UPDATE") : $this->nl_save("INSERT INTO");
					$location_url = $_SERVER['PHP_SELF']
						. "?menuid="
						. $this->checked->menuid
						. "&template=newsletter/templates/news_nl_list.html&news_message=is_saved";
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					unset($_SESSION['nl']);
					exit;
				}
				break;
			case $this->content->template['plugin']['newsletter']['submit']['cancel']: // Abbruch
				$location_url = $_SERVER['PHP_SELF']
					. "?menuid="
					. $this->checked->menuid
					. "&template=newsletter/templates/news_nl_list.html";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="' . $location_url . '">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				unset($_SESSION['nl']);
				exit;
				break;
			default:
				if (isset($this->checked->reporeid)) {
					$this->show_form();
					return;
				}
				elseif ($this->checked->news_id) $this->getAttachmentsList();
				$this->show_form();
			}
		}
	}

	/**
	 * @param string $type
	 */
	function nl_save($type = "")
	{
		$inhalt_html = $_SESSION['nl']['news_inhalt_html'];
		$inhalt_html = str_replace('href="../', 'href="'. $this->protocol . $_SERVER['HTTP_HOST'] . PAPOO_WEB_PFAD . '/', $inhalt_html);
		$inhalt_html = str_replace('src="../', 'src="'. $this->protocol . $_SERVER['HTTP_HOST'] . PAPOO_WEB_PFAD . '/', $inhalt_html);

		#$where = $type == "UPDATE" ? " WHERE deleted = 0 AND news_id = '" . $this->checked->news_id . "'" : "";
		$where = $type == "UPDATE" ? " WHERE news_id = '" . $this->checked->news_id . "'" : "";

		$sql = sprintf($type . " %s SET news_date = '%s',
										news_inhalt_lang = '%d',
										news_inhalt = '%s',
										news_inhalt_html = '%s',
										news_header = '%s'
										%s",
			$this->cms->tbname['papoo_newsletter'],
			date('d.m.Y - H:i:s'),
			$this->db->escape($_SESSION['nl']['language_newsd']),
			$this->db->escape($_SESSION['nl']['news_inhalt']),
			$this->db->escape($inhalt_html),
			$this->db->escape($_SESSION['nl']['news_betreff']),
			$where
		);
		$this->db->query($sql);
		if ($type == "INSERT INTO") $this->checked->news_id = $this->db->insert_id;
	}

	/**
	 * Newsletter editieren
	 */
	function news_nl_edit()
	{
		if (isset($this->checked->submit_file)) {
			$this->check_inputs_and_store_to_session_and_template();
			//erlaubt b. Edit. NL-Daten sind bereits vorhanden, auch im Fehlerfall
			$this->news_attachment_upload();
			$this->getAttachmentsList();
			$this->show_form(1);
		}
		elseif (isset($this->checked->submit_del)) {
			$this->check_inputs_and_store_to_session_and_template();
			$this->news_attachment_delete();
			$this->getAttachmentsList();
			$this->show_form(1);
		}
		else {
			IfNotSetNull($this->checked->submit);
			switch ($this->checked->submit) {
			case $this->content->template['plugin']['newsletter']['submit']['save']: // Speichern
				// Fehler im Form festgestellt?
				// nl-vorschau bringt die Daten in die Session und stellt sie wieder ins Template, auch beim else...
				if ($this->check_inputs_and_store_to_session_and_template()) {
					$this->show_form(1);
				}
				else {
					$this->nl_save("UPDATE");
					$location_url = $_SERVER['PHP_SELF']
						. "?menuid="
						. $this->checked->menuid
						. "&template=newsletter/templates/news_nl_list.html&news_message=is_saved";
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					unset($_SESSION['nl']);
					exit;
				}
				break;
			case $this->content->template['plugin']['newsletter']['submit']['cancel']: // Abbruch
				$location_url = $_SERVER['PHP_SELF']
					. "?menuid="
					. $this->checked->menuid
					. "&template=newsletter/templates/news_nl_list.html";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="' . $location_url . '">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				unset($_SESSION['nl']);
				exit;
				break;
			default:
				if ($this->checked->news_id) $this->getAttachmentsList();
				else unset($_SESSION['nl']);
				$this->show_form();
			}
		}
	}

	/**
	 * �bergabe der Inhalte des Formulares an die Session
	 */
	function newsletter_data_to_session()
	{
		$_SESSION['nl']['news_inhalt'] = $this->checked->inhalt;
		$_SESSION['nl']['news_inhalt_html'] = $this->checked->inhalt_html;
		$_SESSION['nl']['news_betreff'] = $this->checked->linkname;
	}

	/**
	 * </a> durch die in <a href=... enthaltene URL ersetzen
	 * unber�cksichtigt ist noch der alt-Text
	 *
	 * @param $ersatz
	 * @return string
	 */
	function getReplaceString($ersatz)
	{
		if (!isset($_SESSION['nl']['index'])) {
			// array $a[1] mit den URLs aus href=" f�llen
			preg_match_all('/href="(.*?)"/i', $this->checked->inhalt_html, $a);
			$_SESSION['nl']['a'] = $a;    // merken
			$_SESSION['nl']['index'] = 0; // init
		}
		// URL aufbauen mit der </a> ersetzt wird
		// erst schauen, ob der PAPOO_WEB_PFAD bereits vom Admin im site_name eingegeben wurde
		$array = explode("/", $this->content->template['site_name']);
		if (substr($_SESSION['nl']['a'][1][$_SESSION['nl']['index']], 0, 7) != "http://") {
			$ersatz = "\nLink: http://"
				. $array[0]
				. PAPOO_WEB_PFAD
				. "/"
				. $_SESSION['nl']['a'][1][$_SESSION['nl']['index']] . "\n";
		}
		else $ersatz = "\nLink: " . $_SESSION['nl']['a'][1][$_SESSION['nl']['index']]; // bleibt unver�ndert
		$_SESSION['nl']['index']++; // zeige auf n�chste URL im Array
		return $ersatz;
	}

	/**
	 * Den Newsletter abonnieren
	 */
	function subscribe_and_mail_user()
	{
		// checken, ob die E-Mail-Adresse schon drin ist
		$sql = sprintf("SELECT * FROM %s
										WHERE news_user_email = '%s'
										AND deleted = 0",
			$this->papoo_news_user,
			$this->db->escape(trim($this->checked->newsletter_neuemail))
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		// schauen, ob eine aktive, also ungelöschte Emailadresse schon dabei ist
		if (count($result)) {
			$found = false;
			foreach ($result AS $key => $value) {
				if ($value['deleted']) {
					continue; // existiert, aber ist gelöscht
				}
				else {
					$found = true; // existiert schon
					break;
				}
			}
		}

		if($this->checked->datenschutz_newsl!='1'){
			// Meldung "NL wurde abonniert", obwohl bereits eingetragen
			// Eine andere Meldung käme einer Auskunft gleich...
			$location_url = PAPOO_WEB_PFAD
				. "/plugin.php?menuid="
				. $this->checked->menuid
				. "&template=newsletter/templates/subscribe_newsletter.html&error_nl=1";
			header ("Location: $location_url");
			exit;
		}

		if (isset($found) && $found) { // schon vorhanden, kommentarlos zusammenführen
			$sql = sprintf("SELECT * FROM %s
								  WHERE news_user_email = '%s'
								  AND deleted = 0",
				$this->cms->tbname['papoo_news_user'],
				$this->db->escape($this->checked->newsletter_neuemail)
			);
			$results = $this->db->get_results($sql, ARRAY_A);

			if (!empty($results)) {
				$gruppe = '';
				foreach ($results as $res) {
					$gruppe .= $res['news_gruppen'];
					$news_user_id = $res['news_user_id'];
				}
				#$date = date("j.n.Y - G:i:s");
				$nl_gruppen = "";
				foreach ($this->checked->nlgruppe as $key => $addgrpid) {
					$nl_gruppen .= $this->db->escape($addgrpid) . " ";
				}

				$gruppen = explode(' ', $gruppe . $nl_gruppen);
				$gruppen = implode(' ', array_unique($gruppen));


				$sql = sprintf("UPDATE %s SET news_name_nach = '%s',
												news_name_vor = '%s',
												news_name_str = '%s',
												news_name_plz = '%d',
												news_name_ort = '%s',
												news_name_gender = '%s',
												news_name_staat = '%s',
												news_gruppen = '%s'
												WHERE
												news_user_email = '%s'
												AND
												deleted = 0",

					$this->cms->tbname['papoo_news_user'],

					$this->checked->name,
					$this->checked->vorname,
					$this->checked->strasse,
					$this->checked->plz,
					$this->checked->ort,
					$this->checked->gender,
					'Deutschland',
					$gruppen,
					$this->db->escape($this->checked->newsletter_neuemail)
				);

				// Zeilenumbr�che wieder rein
				$sql = str_replace('###n###', "\n", $sql);
				$sql = str_replace('###r###', "\r", $sql);

				$this->db->query($sql);

				$gruppen = explode(' ', (string)$gruppen);

				//neues lookup setzen für results
				foreach ($results as $res) {

					foreach ($gruppen as $k2 => $v2) {
						if ($v2 > 0) {
							$sql = sprintf("INSERT INTO %s SET
										  news_user_id_lu='%d',
										  news_gruppe_id_lu='%d',
										  news_active = 0",
								DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
								$res['news_user_id'],
								$v2);

							$this->db->query($sql);
						}

					}
				}
				IfNotSetNull($news_user_id);
				$this->send_moderator_mail($gruppen, $news_user_id);
			}

			if ($_SESSION['debug_stopallredirect']) {
				$location_url = PAPOO_WEB_PFAD
					. "/plugin.php?menuid="
					. $this->checked->menuid
					. "&news_message=exists&template=newsletter/templates/subscribe_newsletter.html";
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				// Meldung "NL wurde abonniert", obwohl bereits eingetragen
				// Eine andere Meldung käme einer Auskunft gleich...
				$location_url = PAPOO_WEB_PFAD
					. "/plugin.php?menuid="
					. $this->checked->menuid
					. "&news_message=is_send&template=newsletter/templates/subscribe_newsletter.html";
				header("Location: $location_url");
			}
			exit();
		} // E-Mailadresse existiert noch nicht oder nur als gelöscht?
		else {
			// Test auf erw. Userdaten und Zuweisungen (Sprache etc.)
			$this->init_template();
			list($table_datax, $fehler) =
				$this->checknewsform($_SESSION['nl']['news_erw'],
					$this->checked->gender,
					$this->checked->neuvorname,
					$this->checked->neunachname,
					$this->checked->neustrnr,
					$this->checked->neuplz,
					$this->checked->neuort,
					$this->checked->neustaat,
					$this->checked->newsletter_neuemail
				);

			$sql = sprintf("SELECT count(news_gruppe_id)
										FROM %s WHERE news_grpfront = 1",
				$this->papoo_news_gruppen
			);
			$anzahl_veroeffentlichter_verteilerlisten = $this->db->get_var($sql);

			if  ($anzahl_veroeffentlichter_verteilerlisten == 0) {
				$this->checked->nlgruppe = [1];
			}
			// Ende bei Fehler. Werte und Fehler ans Template �bergeben
			else if (is_array($this->checked->nlgruppe) == false || count($this->checked->nlgruppe) == 0) {
				$this->content->template['fehltgrp'] = 1; // Angabe fehlt
				$fehler = 1;
			}

			if ($fehler) {
				$this->content->template['table_datax'] = $table_datax;
				$this->make_lang($this->checked->news_user_lang); // gew�hlte Sprache wieder vorselektieren
				return;
			}
			// Werte kommen in die DB, Best�tigungs-Mail senden, Antwort anzeigen
			if (empty($this->checked->news_user_lang)) {
				$this->checked->news_user_lang = $this->cms->lang_id;
			}

			#$date = date("j.n.Y - G:i:s");
			$nl_gruppen = "";
			foreach ($this->checked->nlgruppe as $key => $addgrpid) {
				$nl_gruppen .= $this->db->escape($addgrpid) . " ";
			}
			// Schl�ssel zur ident erstellen
			//FIXME: md5....
			$key = md5($this->checked->news_user_email . microtime());
			// Daten eintragen
			$sql = sprintf("INSERT INTO %s SET  news_user_email = '%s',
												news_name_gender = '%s',
												news_name_vor = '%s',
												news_name_nach = '%s',
												news_name_str = '%s',
												news_name_plz = '%s',
												news_name_ort = '%s',
												news_name_staat = '%s',
												news_phone = '%s',
												news_user_lang = '%s',
												news_name_firma = '%s',
												news_name_mitglied = '%d',
												news_name_abonnent = '%d',
												news_active = '0',
												news_key = '%s',
												news_gruppen = '%s',
												news_signup_date = '%s'",
				$this->papoo_news_user,
				$this->db->escape($this->checked->newsletter_neuemail),
				$this->db->escape($this->checked->gender),
				$this->db->escape($this->checked->neuvorname),
				$this->db->escape($this->checked->neunachname),
				$this->db->escape($this->checked->neustrnr),
				$this->db->escape($this->checked->neuplz),
				$this->db->escape($this->checked->neuort),
				$this->db->escape($this->checked->neustaat),
				$this->db->escape($this->checked->neuphone),
				$this->db->escape($this->checked->news_user_lang),
				$this->db->escape($this->checked->neufirma),
				$this->db->escape($this->checked->mitglied),
				$this->db->escape($this->checked->abonnent),
				$key,
				$nl_gruppen,
				date("d.m.Y - H:i:s")
			);
			$this->db->query($sql);
			//lookup setzen
			$sql = sprintf("SELECT MAX(news_user_id) FROM %s",
				$this->papoo_news_user);
			$result = $this->db->get_var($sql);

			if (is_array($this->checked->nlgruppe)) {
				foreach ($this->checked->nlgruppe as $k => $v) {
					$sql = sprintf("INSERT INTO %s SET
                                      news_user_id_lu='%d',
                                      news_gruppe_id_lu='%d',
                                      news_active=1",
						DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
						$result,
						$v
					);
					$this->db->query($sql);
				}
			}

			// Best�tigung schicken
			$this->content->template['news_mail1'] =
				str_replace('seitenurl', $this->cms->title_send, $this->content->template['news_mail1']);
			$newsletter = $this->content->template['news_mail1'];
			$this->content->template['news_mail2'] =
				str_replace('seitenurl', $this->cms->title_send, $this->content->template['news_mail2']);
			// Falls der User eine inkorrekte URL angegeben hat...
			$array = explode("/", $this->content->template['site_name']);
			// URL = http:// + www.mysite.de + PAPOO_WEB_PFAD (leer oder nicht - kommt aus site_conf)

			// Impressum rausholen
			$sql = sprintf("SELECT %s FROM %s",
				'news_imp_imp',
				$this->cms->tbname['papoo_news_imp_lang']
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			$impressumText = $result[0]['news_imp_imp'];

			// Mail mit Best�tigungslink zusammenbauen
			$newsletter_text = $this->content->template['news_mail2']
				. $this->protocol
				. $array[0]
				. PAPOO_WEB_PFAD
				. "/plugin.php?menuid="
				. $this->checked->menuid
				. "&activate=$key&news_message=activate&template=newsletter/templates/subscribe_newsletter.html";

			// Impressumstext dranh�ngen, falls vorhanden
			if ($impressumText) {
				$newsletter_text .= "  " . $impressumText;
			}

			$this->mail_it->to = $this->checked->newsletter_neuemail;
			$this->mail_it->from = $_SESSION['nl']['admindaten'][0]->news_email;
			$this->mail_it->from_textx = $_SESSION['nl']['admindaten'][0]->news_name;
			$this->mail_it->subject = $newsletter;
			$this->mail_it->body = $newsletter_text;
			$this->mail_it->priority = 5;
			$this->mail_it->do_mail();
			// Seite neu laden und Antwort anzeigen
			$location_url = PAPOO_WEB_PFAD
				. "/plugin.php?menuid="
				. $this->checked->menuid
				. "&news_message=is_send&template=newsletter/templates/subscribe_newsletter.html";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit();
		}
	}

	/**
	 * @param $gruppen
	 * @param $news_user_id
	 */
	function send_moderator_mail($gruppen, $news_user_id)
	{
		$mail_content = $this->content->template['news_mail3'];
		$moderated_groups = $this->get_moderated();
		$sendmail = false;

		foreach($moderated_groups as $gruppe){
			foreach($gruppen as $grup) {
				if($gruppe['news_gruppe_id'] == $grup){
					$sendmail = true;
				}
			}
		}
		$sql = sprintf("SELECT menuid FROM %s
								  WHERE menulink = '%s'",
			$this->cms->tbname['papoo_menuint'],
			'plugin:newsletter/templates/newsletter_back.html'
		);
		$menuid = $this->db->get_var($sql);

		if($sendmail) {
			$mail_content .= '<a href="'. PAPOO_WEB_PFAD
				. "/interna/plugin.php?menuid="
				. $menuid
				. '&template=newsletter/templates/newsabo.html&msgid='. $news_user_id . '">Hier gehts zum Eintrag</a>';

			//mail abschicken
			$this->mail_it->to = $_SESSION['nl']['admindaten'][0]->news_email;
			$this->mail_it->from = $_SESSION['nl']['admindaten'][0]->news_email;
			$this->mail_it->from_textx = "[Newsletterplugin]";
			$this->mail_it->subject = "Eintrag in moderierte Liste";
			$this->mail_it->body = $mail_content;
			$this->mail_it->priority = 5;
			$this->mail_it->do_mail();
		}
	}

	/**
	 * Abonnenten aktivieren
	 */
	function activate_user()
	{
		$key = $this->db->escape($this->checked->activate);
		$sql = sprintf("UPDATE %s SET news_active = '1',
										news_signup_date = '%s'
										WHERE news_key = '%s'",
			$this->papoo_news_user,
			date("d.m.Y - H:i:s"),
			$key
		);
		$this->db->query($sql);

		//Seite neu laden und Variablen leeren
		$location_url = PAPOO_WEB_PFAD
			. "/plugin.php?menuid="
			. $this->checked->menuid
			. "&template=newsletter/templates/subscribe_newsletter_activated.html";
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">Weiter</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit();
	}

	/**
	 * NL abbestellen
	 */
	function delete_account()
	{
		// NL abmelden per E-Mail   Kommt aus Mail -> l�schen/kennzeichnen
		$key = $this->db->escape($this->checked->activate);
		if (!empty($key)) {
			// Soll lt. Konfig wirklich gel�scht werden?
			if ($_SESSION['nl']['admindaten'][0]->news_allow_delete) {
				$sql = sprintf("DELETE FROM %s
										WHERE news_key = '%s'",
					$this->cms->tbname['papoo_news_user'],
					$key
				);
				$this->db->query($sql);
			}
			else {
				// als gel�scht kennzeichnen
				$sql = sprintf("UPDATE %s SET news_unsubscribe_date = '%s',
											deleted = '1'
											WHERE news_key = '%s'",
					$this->cms->tbname['papoo_news_user'],
					date("d.m.Y - H:i:s"),
					$key
				);
				$this->db->query($sql);
			}
			$sql = sprintf("UPDATE %s SET user_newsletter = '0'
										WHERE confirm_code = '%s'",
				$this->cms->tbname['papoo_user'],
				$key
			);
			$this->db->query($sql);
			// Wenn die Flex installiert ist, auch hier den Abonnenten abmelden
			if ($this->mv->is_mv_installed) {
				// Gibt es eine Mitgliederverwaltung?
				// Wenn ja, hole die dazu geh�rende mv_id
				$sql = sprintf("SELECT mv_id FROM %s
											WHERE mv_art = '2'
											LIMIT 1",
					$this->cms->tbname['papoo_mv']
				);
				$mv_id = $this->db->get_var($sql);
				if ($mv_id) {
					// Ein Flexuser ist auch immer in der papoo_user vorhanden
					// sollte das durch einen Fehler nicht der Fall sein, kann der Flexuser nicht ermittelt und die NL-Funktion daher dort nicht deakt. werden
					$sql = sprintf("SELECT userid FROM %s
												WHERE confirm_code = '%s'",
						$this->cms->tbname['papoo_user'],
						$key
					);
					$userid = $this->db->get_var($sql);
					if ($userid) {
						// Update f�r alle Sprachen der Flex
						$sql = sprintf("SELECT mv_lang_id
												FROM %s",
							$this->cms->tbname['papoo_mv_name_language']
						);
						$sprachen = $this->db->get_results($sql);
						if (!empty($sprachen)) {
							foreach ($sprachen as $sprache) {
								$sql = sprintf("UPDATE %s SET newsletter_5 = '0'
															WHERE mv_content_userid = '%d'",

									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($mv_id)
									. "_search_"
									. $this->db->escape($sprache->mv_lang_id),

									$this->db->escape($userid)
								);
								$this->db->query($sql);
							}
						}
					}
				}
			}
			$location_url = PAPOO_WEB_PFAD
				. "/plugin.php?menuid="
				. $this->checked->menuid
				. "&news_message=is_deactivated&template=newsletter/templates/subscribe_newsletter.html";
		}
		else {
			$location_url = PAPOO_WEB_PFAD
				. "/plugin.php?menuid="
				. $this->checked->menuid
				. "&news_message=&template=newsletter/templates/subscribe_newsletter.html";
		}
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">Weiter</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit();
	}

	/**
	 * @param int $id
	 * @return array|void
	 */
	function get_del_active($id = 0)
	{
		$sql = sprintf("SELECT news_active,
								deleted FROM %s
											WHERE news_user_id = '%s'",
			$this->papoo_news_user,
			$this->db->escape($id)
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	function check_lookup()
	{
		$sql = sprintf("SELECT COUNT(news_user_id) FROM %s",
			DB_PRAEFIX . "papoo_news_user");
		$result = $this->db->get_var($sql);

		$sql = sprintf("SELECT COUNT(news_user_id_lu) FROM %s ",
			DB_PRAEFIX . "papoo_news_user_lookup_gruppen");
		$result2 = $this->db->get_var($sql);

		//WEnn es mehr user Einträge als Gruppenlookups gibt
		//Dann ist es eine alte Version - Updaten!
		if ($result > $result2) {
			#$sql = sprintf("TRUNCATE TABLE %s", DB_PRAEFIX . "papoo_news_user_lookup_gruppen");
			$sql = sprintf("SELECT * FROM %s", DB_PRAEFIX . "papoo_news_user");
			$data = $this->db->get_results($sql, ARRAY_A);

			foreach ($data as $k => $v) {
				$gruppen = explode(" ", $v['news_gruppen']);

				foreach ($gruppen as $k2 => $v2) {
					if ($v2 > 0) {
						$sql = sprintf("INSERT INTO %s SET
                                  news_user_id_lu='%d',
                                  news_gruppe_id_lu='%d'",
							DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
							$v['news_user_id'],
							$v2);
						$this->db->query($sql);
					}
				}
			}
		}
	}

	/**
	 * Abonnenten bearbeiten, l�schen oder einf�gen
	 */
	function make_user()
	{
		//auf Lookup checken
		$this->check_lookup();

		if (!empty($this->checked->search)) {
			$this->checked->activate = ""; // sonst wird der vorige Vorgang wiederholt (active/inactive)
			$this->content->template['search'] = $this->checked->search;
		}
		if (isset($this->checked->activate) && $this->checked->activate) { // aktiv setzen �ber die Auswahliste
			$result_active = $this->get_del_active($this->checked->activate);
			if (!$result_active[0]['deleted']) { // nicht bei gel�schtem Abonnenten
				$result_active = (($result_active[0]['news_active']) xor 1) + 0; // invertieren (flip-flop)
				$sql = sprintf("UPDATE %s SET news_active = '%d'
											WHERE news_user_id = '%d'",
					$this->papoo_news_user,
					$result_active,
					$this->db->escape($this->checked->activate)
				);
				$this->db->query($sql);
			}
		}
		// Abonnenten aus der Datenbank l�schen
		if (!empty($this->checked->delid)) {
			$result_deleted = $this->get_del_active($this->checked->delid);
			if (!$result_deleted[0]['deleted']) { // schon gel�scht? Dann nix machen
				// Soll lt. Konfig wirklich gel�scht werden?
				if ($_SESSION['nl']['admindaten'][0]->news_allow_delete) {
					$sql = sprintf("DELETE FROM %s WHERE news_user_id = '%d'",
						$this->papoo_news_user,
						$this->checked->delid
					);
					$this->db->query($sql);
				}
				else {
					// als gel�scht kennzeichnen
					$sql = sprintf("UPDATE %s SET deleted = '1' WHERE news_user_id = '%s'",
						$this->cms->tbname['papoo_news_user'],
						$this->db->escape($this->checked->delid)
					);
					$this->db->query($sql);
				}
				$this->content->template['abo_is_del'] = 1;
			}
		}
		// msgid ist nur bei "Edit" eines Abonnenten gesetzt (= Abonnenten-ID). In dem Fall alle Sprachen anzeigen mit derjenigen, die selected ist.
		if (isset($this->checked->msgid) && $this->checked->msgid) {
			$sql = sprintf("SELECT news_user_lang FROM %s WHERE news_user_id = '%d'",
				$this->papoo_news_user,
				$this->db->escape($this->checked->msgid)
			);
			$resultlang = $this->db->get_results($sql, ARRAY_A);
			$this->make_lang($resultlang[0]['news_user_lang']);
		} else $this->make_lang(); // Alle Sprachen f�r neuen Abonnenten anzeigen, selected ist noch nichts.
		// Hier die Dubletten und inaktiven l�schen
		$this->make_extra_functions();
		// neuen Abonnenten eingeben
		// Rausfinden, ob erweiterte Userdaten angezeigt werden sollen
		$var = $_SESSION['nl']['admindaten'][0]->news_erw;
		// Neuer Abonnent: edit = 1 bei Aufruf �ber das Neu-Icon oben in der Auswahlliste
		// new_recipient ist auf 1 gesetzt, wenn Formulardaten mit "Eintragen" bei einem neuen Abonnenten submitted werden (hidden input in newsabo.html)
		if (!empty($this->checked->edit) or isset($this->checked->new_recipient) && $this->checked->new_recipient) {// neu, auch bei Fehler (edit=1 bei Aufruf)
			if (isset($this->checked->new_recipient) && $this->checked->new_recipient) { // neuer, input hidden in newsabo Wert aus ander_data[0]['new'] = 1
				// Bei der Admin-Eingabe nur E-Mail anfordern (bei �nderung alles?)
				list($this->content->template['ander_data'], $fehler) =
					$this->checknewsform("", "", "", "", "", "", "", "", $this->checked->news_user_email);
				// checken, ob die E-Mailadreese schon vorhanden ist
				$sql = sprintf("SELECT COUNT(news_user_id) FROM %s
															WHERE news_user_email = '%s'
															AND deleted = 0",
					$this->papoo_news_user,
					$this->db->escape($this->checked->news_user_email)
				);
				$cu = $this->db->get_var($sql);

				$nl_gruppen = "";
				if ($cu >= 1) {
					$this->content->template['ander_data'][0]['fehltemail'] = 3; // Fehler: schon vorhanden
					$fehler = 1;
					$sql = sprintf("SELECT * FROM %s
								  WHERE news_user_email = '%s'
								  AND deleted = 0",
						$this->cms->tbname['papoo_news_user'],
						$this->db->escape($this->checked->news_user_email)
					);
					$results = $this->db->get_results($sql, ARRAY_A);

					if (!empty($results)) {
						$gruppe = '';
						foreach ($results as $res) {
							$gruppe .= $res['news_gruppen'];
						}
						if(is_array($this->checked->grp_active)) {
							foreach ($this->checked->grp_active as $key => $addgrpid) {
								$nl_gruppen .= $this->db->escape($addgrpid) . " ";
							}
						}
						$gruppen = explode(' ', $gruppe . $nl_gruppen);
						$gruppen = implode(' ', array_unique($gruppen));

						$key = md5($this->db->escape($this->checked->news_user_email) . microtime());
						$date = "A " . date("d.m.Y - H:i:s"); // "A" = Kennzeichnung: Einf�gung ist durch Admin erfolgt

						$sql = sprintf("UPDATE %s SET news_user_email = '%s',
													news_active = '%d',
													news_name_vor = '%s',
													news_name_gender = '%s',
													news_name_nach = '%s',
													news_name_str = '%s',
													news_name_plz = '%s',
													news_name_ort = '%s',
													news_name_staat = '%s',
													news_phone = '%s',
													news_user_lang = '%s',
													news_name_firma = '%s',
													news_name_mitglied = '%d',
													news_name_abonnent = '%d',
													news_signup_date = '%s',
													news_key = '%s',
													news_gruppen = '%s',
													deleted = 0
													WHERE
													news_user_email = '%s'
													AND
													deleted = 0",
							$this->papoo_news_user,
							$this->db->escape($this->checked->news_user_email),
							$this->db->escape($this->checked->news_active),
							$this->db->escape($this->checked->news_name_vor),
							$this->db->escape($this->checked->news_name_gender),
							$this->db->escape($this->checked->news_name_nach),
							$this->db->escape($this->checked->news_name_str),
							$this->db->escape($this->checked->news_name_plz),
							$this->db->escape($this->checked->news_name_ort),
							$this->db->escape($this->checked->news_name_staat),
							$this->db->escape($this->checked->news_phone),
							$this->db->escape($this->checked->news_user_lang),
							$this->db->escape($this->checked->news_name_firma),
							$this->db->escape($this->checked->news_name_mitglied),
							$this->db->escape($this->checked->news_name_abonnent),
							$date,
							$key,
							$gruppen,
							$this->checked->news_user_email
						);

						// Zeilenumbr�che wieder rein
						$sql = str_replace('###n###', "\n", $sql);
						$sql = str_replace('###r###', "\r", $sql);

						$this->db->query($sql);

						$gruppen = explode(' ', (string)$gruppen);

						//neues lookup setzen für results
						foreach ($results as $res) {

							foreach ($gruppen as $k2 => $v2) {
								if ($v2 > 0) {
									$sql = sprintf("INSERT INTO %s SET
                                  news_user_id_lu='%d',
                                  news_gruppe_id_lu='%d'",
										DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
										$res['news_user_id'],
										$v2
									);
									$this->db->query($sql);
								}
							}
						}
					}
				}
				IfNotSetNull($this->checked->grp_active);
				if (!is_countable($this->checked->grp_active) || is_countable($this->checked->grp_active) && !count($this->checked->grp_active)) {
					$sql = sprintf("SELECT count(news_gruppe_id) FROM %s",
						$this->papoo_news_gruppen
					);
					if ($this->db->get_var($sql) == 1) {
						$this->checked->grp_active = array(1 => 1);
					}
					else {
						$this->content->template['news_fehltgrp'] = $fehler = "1";
						$this->content->template['case2a'] = "";
					}
				}
				if ($fehler) {
					$this->content->template['case2b'] = "ok"; // erneut senden
					$this->content->template['ander_data'][0]['new'] = 1;
					$this->content->template['erw_data'] = $var;
					$this->all_nlgroups_to_template($this->checked->grp_active);
					#$this->all_sysgroups_to_template();
					return;
				}

				// Std. language
				if (empty($this->checked->news_user_lang)) {
					$this->checked->news_user_lang = $this->cms->lang_id;
				}
				// NL Gruppen komprimieren
				foreach ($this->checked->grp_active as $key => $addgrpid) {
					$nl_gruppen .= $this->db->escape($addgrpid) . " ";
				}
				// Schl�ssel zur ident erstellen
				$key = md5($this->db->escape($this->checked->news_user_email) . microtime());
				$date = "A " . date("d.m.Y - H:i:s"); // "A" = Kennzeichnung: Einf�gung ist durch Admin erfolgt
				$sql = sprintf("INSERT INTO %s SET news_user_email = '%s',
													news_active = '%d',
													news_name_vor = '%s',
													news_name_gender = '%s',
													news_name_nach = '%s',
													news_name_str = '%s',
													news_name_plz = '%s',
													news_name_ort = '%s',
													news_name_staat = '%s',
													news_phone = '%s',
													news_user_lang = '%s',
													news_name_firma = '%s',
													news_name_mitglied = '%d',
													news_name_abonnent = '%d',
													news_signup_date = '%s',
													news_key = '%s',
													news_gruppen = '%s',
													deleted = 0",
					$this->papoo_news_user,
					$this->db->escape($this->checked->news_user_email),
					$this->db->escape($this->checked->news_active),
					$this->db->escape($this->checked->news_name_vor),
					$this->db->escape($this->checked->news_name_gender),
					$this->db->escape($this->checked->news_name_nach),
					$this->db->escape($this->checked->news_name_str),
					$this->db->escape($this->checked->news_name_plz),
					$this->db->escape($this->checked->news_name_ort),
					$this->db->escape($this->checked->news_name_staat),
					$this->db->escape($this->checked->news_phone),
					$this->db->escape($this->checked->news_user_lang),
					$this->db->escape($this->checked->news_name_firma),
					$this->db->escape($this->checked->news_name_mitglied),
					$this->db->escape($this->checked->news_name_abonnent),
					$date,
					$key,
					$nl_gruppen
				);
				$this->db->query($sql);

				//Id des eintrages $this->checked->grp_active
				$newuser_id = $this->db->insert_id;

				if (is_array($this->checked->grp_active)) {
					foreach ($this->checked->grp_active as $k => $v) {
						$sql = sprintf("INSERT INTO %s SET
                                      news_user_id_lu='%d',
                                      news_gruppe_id_lu='%d'",
							DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
							$newuser_id,
							$v
						);
						$this->db->query($sql);

					}
				}

				// Seite neu laden
				//exit();
				// header("Location: ./plugin.php?menuid=".$this->checked->menuid."&template=plugin.php?menuid={$formmenuid}&template=newsletter/templates/newsabo.html");
				//$location_url = "./plugin.php?menuid=".$this->checked->menuid."&template=plugin.php?menuid={$formmenuid}&template=newsletter/templates/newsabo.html";
				$location_url = "./plugin.php?menuid="
					. $this->checked->menuid
					. "&template=newsletter/templates/newsabo.html";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="' . $location_url . '">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit();
				//Ende Eingabe neuer User
			}
			else {
				// Template newsabo initialisieren f�r die Eingabe eines neuen Abonnenten
				$this->content->template['ander_data'] = array(); // leere Felder
				$this->content->template['case2b'] = "ok"; // Eingabeformular anzeigen
				array_push($this->content->template['ander_data'], array('new' => 1)); // Eingabe eines neuen ist aktiv
				$this->content->template['erw_data'] = $var; // Erw. Daten
				$this->all_nlgroups_to_template(); // NL Gruppen bereitstellen
			}
		}
		else {
			$this->content->template['case2a'] = "ok"; // Liste der Abonnenten anzeigen
			//Gruppen zuweisen
			$datanlg = $this->get_all_nl_groups();
			//print_r($datanlg);

			foreach ($datanlg as $xk => $xv) {
				$grnl[$xv->news_gruppe_id] = $xv->news_gruppe_name;
			}

			IfNotSetNull($grnl);
			$this->content->template['groupliste'] = $grnl;
			$this->content->template['selected_group'] = isset($this->checked->select_group) ? $this->checked->select_group : NULL;
			// Wenn kein Abonnent ausgew�hlt ist
			if (!isset($this->checked->msgid) || isset($this->checked->msgid) && empty($this->checked->msgid) or
				isset($this->checked->news_user_id) && is_numeric($this->checked->news_user_id)
			) {
				if (!empty($this->checked->search) || !empty($this->checked->select_group)) { // Wir haben eine Suche
					$selectedGroup = (int)$this->checked->select_group;
					$selectedGroupSqlConditional = $selectedGroup > 0
						? "AND news_gruppe_id_lu = {$selectedGroup} "
						: '';

					$sql = sprintf("SELECT B.news_gruppe_id_lu
											FROM %s AS A LEFT JOIN %s AS B
											ON news_user_id=news_user_id_lu
											WHERE news_user_email LIKE %s
											AND deleted = 0
											%s
											GROUP BY news_user_id
											",
						$this->papoo_news_user,
						DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
						"'%" . $this->db->escape($this->checked->search) . "%'",
						$selectedGroupSqlConditional
					);

					$anzahl = $this->db->get_results($sql);
					$anzahl = is_countable($anzahl) ? count($anzahl) : 0;

					$sql = sprintf("SELECT COUNT(news_user_id)
											FROM  %s AS A lEFT JOIN %s AS B
											ON news_user_id=news_user_id_lu
											WHERE news_user_email LIKE %s
											AND A.news_active = 1
											AND deleted = 0
											%s
											GROUP BY news_user_id ",
						$this->papoo_news_user,
						DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
						"'%" . $this->db->escape($this->checked->search) . "%'",
						$selectedGroupSqlConditional
					);
					$anzahl_aktiv = $this->db->get_results($sql);

					$anzahl_aktiv = is_countable($anzahl_aktiv) ? count($anzahl_aktiv) : 0;
					$this->content->template['anzahl'] = $anzahl;
					$this->content->template['anzahl_aktiv'] = $anzahl_aktiv;

					$this->weiter = new weiter();
					$this->weiter->make_limit(50);
					$sql = sprintf("SELECT COUNT(news_user_id)
											FROM  %s AS A lEFT JOIN %s AS B
											ON news_user_id=news_user_id_lu
											WHERE news_user_email LIKE %s
											%s
                                            AND deleted = 0
											",
						$this->papoo_news_user,
						DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
						"'%" . $this->db->escape($this->checked->search) . "%'",
						$selectedGroupSqlConditional
					);

					$this->weiter->result_anzahl = $this->db->get_var($sql);
					$this->weiter->weiter_link = "./plugin.php?menuid="
						. $this->checked->menuid
						. "&template=newsletter/templates/newsabo.html&select_group=".$selectedGroup;
					// wenn es sie gibt, weitere Seiten anzeigen
					$what = "search";
					$this->weiter->do_weiter($what);
					// Anzeigen der Abonnenten
					$sql = sprintf("SELECT * , A.news_active as news_active FROM %s AS A lEFT JOIN %s AS B
                                            ON news_user_id=news_user_id_lu
											WHERE news_user_email LIKE %s
											%s
                                            AND deleted = 0
                                            GROUP BY news_user_id
											ORDER BY news_user_email, A.news_active ASC
											%s",
						$this->papoo_news_user,
						DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
						"'%" . $this->db->escape($this->checked->search) . "%'",
						$selectedGroupSqlConditional,
						$this->weiter->sqllimit
					);


					$this->content->template['ander_data'] = $this->db->get_results($sql, ARRAY_A);
					//nun alle ergebnisse für csv
					$sql = sprintf("SELECT * , A.news_active as news_active FROM %s AS A lEFT JOIN %s AS B
                                            ON news_user_id=news_user_id_lu
											WHERE news_user_email LIKE %s
											%s
                                            AND deleted = 0
                                            GROUP BY news_user_id
											ORDER BY news_user_email, A.news_active ASC",
						$this->papoo_news_user,
						DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
						"'%" . $this->db->escape($this->checked->search) . "%'",
						$selectedGroupSqlConditional
					);
					$_SESSION['csv_export_newsletter_search'] = $this->db->get_results($sql, ARRAY_A);
				}
				// Wenn kein Abonnent �berarbeitet wurde und nicht Eintragen (Men�-Aufruf)
				elseif (!isset($this->checked->submit) || (isset($this->checked->submit) &&
					$this->checked->submit != $this->content->template['plugin']['newsletter']['speichern'] and empty($this->checked->search)
				)) {

					// Anzahl der Abonennten news_user_id
					$sql = sprintf("SELECT COUNT(news_user_id)
													FROM %s
													WHERE deleted = 0",
						$this->papoo_news_user
					);
					$anzahl = $this->db->get_var($sql);
					//print_r($sql);

					$this->content->template['anzahl'] = $anzahl;
					$sql = sprintf("SELECT COUNT(news_user_id)
													FROM %s
													 WHERE news_active = 1
													 AND deleted = 0",
						$this->papoo_news_user
					);
					$anzahl_aktiv = $this->db->get_var($sql);
					$this->content->template['anzahl_aktiv'] = $anzahl_aktiv;
					if (!isset($this->checked->sort_email)
						AND !isset($this->checked->sort_registration_date)
					) {
						// default-Werte zu Beginn
						$sort = "news_user_id DESC";
						$sort_dir = "DESC";
						$this->content->template['sort_registration_date'] = $this->content->template['sort_email'] = "";
					}
					else {
						if (isset($this->checked->sort_email) && $this->checked->sort_email == "DESC") {
							$sort = "news_user_email DESC";
							$sort_dir = $this->content->template['sort_email'] = "DESC";
							$this->content->template['sort_registration_date'] = "";
							$this->content->template['sort'] = "email";
						}
						elseif (isset($this->checked->sort_email) && $this->checked->sort_email == "ASC") {
							$sort = "news_user_email ASC";
							$sort_dir = $this->content->template['sort_email'] = "ASC";
							$this->content->template['sort_registration_date'] = "";
							$this->content->template['sort'] = "email";
						}

						if (isset($this->checked->sort_registration_date) && $this->checked->sort_registration_date == "DESC") {
							$sort = "news_user_id DESC";
							$sort_dir = $this->content->template['sort_registration_date'] = "DESC";
							$this->content->template['sort_email'] = "";
							$this->content->template['sort'] = "date";
						}
						elseif (isset($this->checked->sort_registration_date) && $this->checked->sort_registration_date == "ASC") {
							$sort = "news_user_id ASC";
							$sort_dir = $this->content->template['sort_registration_date'] = "ASC";
							$this->content->template['sort_email'] = "";
							$this->content->template['sort'] = "date";
						}
					}
					$this->weiter->make_limit(50);
					$sql = sprintf("SELECT COUNT(news_user_id)
													FROM %s
													 WHERE deleted = 0",
						$this->papoo_news_user
					);
					$this->weiter->result_anzahl = $this->db->get_var($sql);
					$this->weiter->weiter_link = "./plugin.php?menuid="
						. $this->checked->menuid
						. "&template=newsletter/templates/newsabo.html";
					IfNotSetNull($sort_dir);
					IfNotSetNull($sort);
					if (isset($this->content->template['sort']) && $this->content->template['sort'] == "date") {
						$this->weiter->weiter_link .= "&sort_registration_date=" . $sort_dir;
					}
					elseif (isset($this->content->template['sort']) && $this->content->template['sort'] == "email") {
						$this->weiter->weiter_link .= "&sort_email=" . $sort_dir;
					}
					// wenn es sie gibt, weitere Seiten anzeigen
					$what = "teaser";
					$this->weiter->do_weiter($what);
					// Anzeigen der Abonnenten
					$sql = sprintf("SELECT * FROM %s WHERE deleted = 0
												ORDER BY %s
												%s",
						$this->papoo_news_user,
						$sort,
						$this->weiter->sqllimit
					);

					$this->content->template['ander_data'] = $this->db->get_results($sql, ARRAY_A);
					//alle ergebnisse für csv
					$sql = sprintf("SELECT * FROM %s WHERE deleted = 0
												ORDER BY %s",
						$this->papoo_news_user,
						$sort
					);
					$_SESSION['csv_export_newsletter_search'] = $this->db->get_results($sql, ARRAY_A);
					// Sprach-IDs umsetzen
					if ($this->content->template['ander_data']) {
						$search = array("'1'i", "'2'i", "'3'i", "'4'i", "'5'i", "'6'i", "'7'i");
						$replace = array("de", "en", "it", "es", "fr", "pt", "nl");
						foreach ($this->content->template['ander_data'] as $key => $a1) {
							$this->content->template['ander_data'][$key]['news_user_lang'] =
								preg_replace($search, $replace, $this->content->template['ander_data'][$key]['news_user_lang']);
						}
					}
				} // Ein Abonnet wurde �berarbeitet. Edit/Eintragen
				else {
					$var = $_SESSION['nl']['admindaten'][0]->news_erw;
					list($this->content->template['ander_data'], $fehler) =
						$this->checknewsform(
							$var,
							$this->checked->news_name_gender,
							$this->checked->news_name_vor,
							$this->checked->news_name_nach,
							$this->checked->news_name_str,
							$this->checked->news_name_plz,
							$this->checked->news_name_ort,
							$this->checked->news_name_staat,
							$this->checked->news_user_email
						);
					// Pr�fen, ob die E-Mailadresse bei einer evtl. �nderung im Formular schon vorhanden ist
					// Suche nach S�tzen mit dieser E-Mailadresse aber mit anderer Userid
					// $fehler = null;
					$sql = sprintf("SELECT COUNT(news_user_id)
											FROM %s
											WHERE news_user_id != '%d'
											AND news_user_email = '%s'
											AND deleted = 0",
						$this->cms->tbname['papoo_news_user'],
						$this->db->escape($this->checked->news_user_id),
						$this->db->escape($this->checked->news_user_email)
					);
					$result = $this->db->get_var($sql);
					// E-Mailadresse wurde im Formular ge�ndert und ist aber schon vorhanden
					if ($result >= 1) {
						$this->content->template['ander_data'][0]['fehltemail'] = 3; // schon vorhanden
						$fehler = 1;
					}
					// Keine Gruppe zugeordnet? TODO: In die checknewsform einbauen
					if (!count($this->checked->grp_active)) {
						$sql = sprintf("SELECT count(news_gruppe_id)
												FROM %s",
							$this->papoo_news_gruppen
						);
						if ($this->db->get_var($sql) == 1) {
							$this->checked->grp_active = array(1 => 1);
						}
						else {
							$this->content->template['news_fehltgrp'] = $fehler = "1";
							$this->content->template['case2a'] = "";
						}
					}
					if ($fehler) {
						$this->content->template['case2a'] = "";
						$this->content->template['case2b'] = "ok";
						if ($var == 1) {
							$this->content->template['erw_data'] = "ok";
						}
						$this->all_nlgroups_to_template($this->checked->grp_active);
						$this->all_sysgroups_to_template(); // vorbereitet, testen, newsabo anpassen
						return;
					}

					$nl_gruppen = "";
					foreach ($this->checked->grp_active as $key => $addgrpid) {
						$nl_gruppen .= $this->db->escape($addgrpid) . " ";
					}

					//lookup neu setzen
					//Id des eintrages $this->checked->grp_active
					$newuser_id = $this->checked->news_user_id;
					//Alte löschen
					$sql = sprintf("DELETE FROM %s WHERE news_user_id_lu='%d' ",
						DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
						$newuser_id);
					$this->db->query($sql);
					if (is_array($this->checked->grp_active)) {
						foreach ($this->checked->grp_active as $k => $v) {
							$sql = sprintf("INSERT INTO %s SET
                                      news_user_id_lu='%d',
                                      news_gruppe_id_lu='%d'",
								DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
								$newuser_id,
								$v
							);
							$this->db->query($sql);
						}
					}

					//moderierte listen holen
					$moderated = $this->get_moderated();
					//erst alle deaktivieren
					foreach($moderated as $mod_id){
						$sql = sprintf("UPDATE %s SET news_active = 0
										WHERE news_user_id_lu = '%s'
										AND news_gruppe_id_lu = '%s'",
							DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
							$this->checked->news_user_id,
							$mod_id['news_gruppe_id']
						);
						$this->db->query($sql);
					}

					//dann aktivieren
					if($this->checked->grp_activefreigabe) {
						foreach($this->checked->grp_activefreigabe as $freigabe) {
							$sql = sprintf("UPDATE %s SET news_active = 1
										WHERE news_user_id_lu = '%s'
										AND news_gruppe_id_lu = '%s'",
								DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
								$this->checked->news_user_id,
								$freigabe
							);
							$this->db->query($sql);
						}
					}

					$sql = sprintf("UPDATE %s SET news_user_email = '%s',
													news_active = '%d',
													news_name_gender = '%s',
													news_name_vor = '%s',
													news_name_nach = '%s',
													news_name_str = '%s',
													news_name_plz = '%s',
													news_name_ort = '%s',
													news_name_staat = '%s',
													news_phone = '%s',
													news_user_lang = '%s',
													news_name_firma = '%s',
													news_name_mitglied = '%d',
													news_name_abonnent = '%d',
													news_gruppen = '%s'
													WHERE news_user_id = '%d'",
						$this->papoo_news_user,
						$this->db->escape($this->checked->news_user_email),
						$this->db->escape($this->checked->news_active),
						$this->db->escape($this->checked->news_name_gender),
						$this->db->escape($this->checked->news_name_vor),
						$this->db->escape($this->checked->news_name_nach),
						$this->db->escape($this->checked->news_name_str),
						$this->db->escape($this->checked->news_name_plz),
						$this->db->escape($this->checked->news_name_ort),
						$this->db->escape($this->checked->news_name_staat),
						$this->db->escape($this->checked->news_phone),
						$this->db->escape($this->checked->news_user_lang),
						$this->db->escape($this->checked->news_name_firma),
						$this->db->escape($this->checked->news_name_mitglied),
						$this->db->escape($this->checked->news_name_abonnent),
						$nl_gruppen,
						$this->db->escape($this->checked->news_user_id)
					);
					$this->db->query($sql);

					// Seite neu laden und Variablen leeren
					// header("Location: ./plugin.php?menuid=".$this->checked->menuid."&template=plugin.php?menuid={$formmenuid}&template=newsletter/templates/newsabo.html");
					//$location_url = "./plugin.php?menuid=".$this->checked->menuid."&template=plugin.php?menuid={$formmenuid}&template=newsletter/templates/newsabo.html";
					$location_url = "./plugin.php?menuid="
						. $this->checked->menuid
						. "&template=newsletter/templates/newsabo.html";
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit();
				}
			} // Es ist eine E-Mailadresse ausgew�hlt, also Formular anzeigen zur Bearbeitung
			else {
				IfNotSetNull($this->checked->activate);
				$result_active = $this->get_del_active($this->checked->activate);
				IfNotSetNull($result_active[0]['deleted']);
				if (!$result_active[0]['deleted']) { // nicht bei gel�schtem Abonnenten
					$this->content->template['case2a'] = "";
					$this->content->template['case2b'] = "ok";
					$sql = sprintf("SELECT * FROM %s
												WHERE news_user_id = '%d'
												AND deleted = 0",
						$this->papoo_news_user,
						$this->db->escape($this->checked->msgid)
					);
					$this->content->template['ander_data'] = $this->db->get_results($sql, ARRAY_A);
					// Rausfinden, ob erweiterte Userdaten angezeigt werden sollen
					$var = $_SESSION['nl']['admindaten'][0]->news_erw;
					if ($var == 1) {
						$this->content->template['erw_data'] = "ok";
					}
					$grp = $this->content->template['ander_data'][0];
					$grparray = explode(" ", $grp['news_gruppen']);
					$this->all_nlgroups_to_template($grparray);
					$this->all_sysgroups_to_template(); // vorbereitet, testen, newsabo anpassen
				}
			}
		}

		IfNotSetNull($grnl);
		foreach ($this->content->template['ander_data'] as $k => $v) {
			IfNotSetNull($v['news_gruppen']);
			$gruppen = explode(" ", $v['news_gruppen']);
			$nlgr = "";
			foreach ($gruppen as $k1 => $v1) {
				$v1 = trim($v1);
				if (!empty($v1)) {
					$nlgr .= "" . $grnl[$v1] . "<br />";
				}
			}
			$this->content->template['ander_data'][$k]['news_gruppen'] = $nlgr;
		}
	}

	function news_group_list()
	{
		// Wenn gel�scht werden soll
		if (!empty($this->checked->grp_del_id)) {
			$this->group_del_entry($this->checked->grp_del_id);
		}
		// Alle NL Gruppen in die Liste news_group.html
		//$this->all_nlgroups_to_template();<br />
		$this->nl_abonnenten_anzahl_to_template(1); // Sprachen bei der Ermittlung der Abonnentenanzahl nicht ber�cksichtigen
	}

	function news_group_new()
	{
		if (isset($_POST['submit']) && $_POST['submit'] == "Eintragen") {
			if ($this->checked->news_grpname) {
				$sqlin = sprintf("INSERT INTO %s SET news_gruppe_name = '%s',
													news_gruppe_description = '%s',
													news_grpfront = '%s',
													news_grpmoderated = '%s'",
					$this->papoo_news_gruppen,
					$this->db->escape($this->checked->news_grpname),
					$this->db->escape($this->checked->news_grpdescript),
					$this->db->escape($this->checked->news_grpfront),
					$this->db->escape($this->checked->news_grpmoderated)
				);

				$this->db->query($sqlin);
				$location_url = "./plugin.php?menuid="
					. $this->checked->menuid
					. "&template=newsletter/templates/news_group.html";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="' . $location_url . '">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit();
			}
			else {
				$this->content->template['grpname_error'] = 1;
				$this->content->template['grpdescrip'] = isset($this->checked->news_grpdescript) ? $this->checked->news_grpdescript : NULL;
				$this->content->template['grpfront'] = isset($this->checked->news_grpfront) ? $this->checked->news_grpfront : NULL;
				// restore user data
				$this->content->template['grpmoderated'] = isset($this->checked->news_grpmoderated) ? $this->checked->news_grpmoderated : NULL;
			}
		}
	}

	/**
	 * diese Routine bringt alle Gruppen zur Anzeige und ordnet die beiden Werte dem Template zu
	 *
	 * @param array $nlgrparray enthält diejenigen, die checked=checked sind
	 * @param array $anzarray die Anzahl der Abonnenten einer Gruppe
	 * @param int $front
	 */
	function all_nlgroups_to_template($nlgrparray = array(), $anzarray = array(), $front = 0)
	{
		if(is_array($anzarray) == false) {
			$anzarray = array();
		}
		$grpdata = array();
		$grp = $this->get_all_nl_groups(); // hole alle NL Gruppen

		if (!empty($grp)) { // gibt es NL Gruppen?
			foreach ($grp as $data) {
				$checked = "";
				if (!empty($nlgrparray)) { // gegen bestimmte NL Gruppen vergleichen?
					foreach ($nlgrparray as $group) {
						if ($data->news_gruppe_id == $group) {
							$checked = "checked"; // als ausgew�hlt kennzeichnen
						}
					}
				}
				// fast immer, ausser im FE bei der Gruppe Test
				if (is_array($anzarray) && (!$front) or ($data->news_gruppe_name != "Test")) {
					IfNotSetNull($anzarray[$data->news_gruppe_id]);
					array_push($grpdata, array(
							'grp_id' => $data->news_gruppe_id,
							'name' => $data->news_gruppe_name,
							'descript' => $data->news_gruppe_description,
							'checked' => $checked,
							'anzahl' => $anzarray[$data->news_gruppe_id],
							'show_front' => $data->news_grpfront// Anzahl der aktiven Abonnenten
						)
					);
				}
			}
		}
		$sql = sprintf("SELECT * FROM %s LEFT JOIN %s
										ON news_gruppe_id = news_gruppe_id_lu
										WHERE news_grpmoderated = 1
										AND news_user_id_lu = '%s'",
			$this->cms->tbname['papoo_news_gruppen'],
			$this->cms->tbname['papoo_news_user_lookup_gruppen'],
			@$this->checked->msgid
		);

		$result = $this->db->get_results($sql, ARRAY_A);

		$sql = sprintf("SELECT count(news_gruppe_id)
								FROM %s WHERE news_grpfront = 1",
			$this->papoo_news_gruppen
		);
		$this->content->template['oeffentliche_verteilerlisten'] = $this->db->get_var($sql);

		$this->content->template['grp_data'] = $grpdata;
		$this->content->template['grpmoderated_data'] = $result;
	}

	/**
	 * diese Routine bringt alle Gruppen zur Anzeige und ordnet die beiden Werte dem Template zu
	 *
	 * @param array $sysgrparray enth�lt diejenigen, die checked=checked sind
	 * @param array $anzarray die Anzahl der Abonnenten einer Gruppe
	 */
	function all_sysgroups_to_template($sysgrparray = array(), $anzarray = array())
	{
		$grpdata = array();
		$grp = $this->get_all_sys_groups(); // hole alle NL Gruppen
		// spezielle Gruppen aa = alle akt. Abonnenten (inkl. Systemgruppen), ag = alle NL Gruppen
		array_push($grp, array(
			'gruppeid' => "aa",
			'gruppenname' => "",
			'gruppen_beschreibung' => ""
		),
			array(
				'gruppeid' => "ag",
				'gruppenname' => "",
				'gruppen_beschreibung' => ""
			),
			array(
				'gruppeid' => "FS",
				'gruppenname' => "",
				'gruppen_beschreibung' => ""
			)
		);
		if (count($grp)) { // gibt es NL Gruppen?
			$checked = "";
			foreach ($grp as $data) { // alle Sysgruppen
				if (!empty($sysgrparray)) { // gegen bestimmte NL Gruppen vergleichen?
					foreach ($sysgrparray as $group) {
						if ($data['gruppeid'] == $group) {
							$checked = 'checked="checked"'; // als ausgew�hlt kennzeichnen
						}
					}
				}
				if (is_numeric($data['gruppeid'])) {
					IfNotSetNull($anzarray[$data['gruppeid']]);
					array_push($grpdata,
						array(
							'grp_id' => $data['gruppeid'],
							'name' => $data['gruppenname'],
							'descript' => $data['gruppen_beschreibung'],
							'checked' => $checked,
							'sys_anzahl' => $anzarray[$data['gruppeid']] // Anzahl der aktiven Abonnenten
						)
					);
				}
			}
		}
		// Suchergebnis der Flex ber�cksichtigen, falls vorhanden
		if (isset($_SESSION['nl']['mv_newsletter_sql']) && $_SESSION['nl']['mv_newsletter_sql']) {
			$mv_result = $this->db->get_results($_SESSION['nl']['mv_newsletter_sql'], ARRAY_A);

			if (!empty($mv_result)) {
				$maxct = count($grpdata);
				$grpdata[$maxct]['grp_id'] = "FS";
				$grpdata[$maxct]['name'] = "Flex Suchergebnis";
				$grpdata[$maxct]['descript'] = "";
				$grpdata[$maxct]['checked'] = "";
				foreach ($mv_result AS $key => $value) {
					if ($value['EMail_9']
						AND $value['mv_content_userid']
					) $grpdata[$maxct]['sys_anzahl']++;
				}
			}
		}
		$this->content->template['gruppe_data'] = $grpdata;
	}

	/**
	 * NL Gruppen mit Abonnenten-Anzahl ans Template �bergeben
	 *
	 * @param int $lang
	 */
	function nl_abonnenten_anzahl_to_template($lang = 1)
	{
		if ($lang) {
			// default 1 / de
			if (empty($_SESSION['nl']['language_newsd'])) {
				$_SESSION['nl']['language_newsd'] = 1;
			}
			// Numerische Werte umsetzen f�r die Abfrage
			$search = array("'1'", "'2'", "'3'", "'4'", "'5'", "'6'", "'7'");
			$replace = array("de", "en", "it", "es", "fr", "pt", "nl");
			$langval = preg_replace($search, $replace, $_SESSION['nl']['language_newsd'], 1);
			$sql_add = " AND (user_lang_front = '" . $langval . "'";
			// Eintr�ge ohne Angabe: default Annahme deutsch ( = 1)
			if ($_SESSION['nl']['language_newsd'] == 1) {
				$sql_add .= " OR user_lang_front = ''";
			}
			$sql_add .= ")";
		}
		$sql = sprintf("SELECT COUNT(news_user_id)
								FROM %s
								WHERE news_active = '1'
								AND news_user_lang = '%d'
								AND deleted = 0",
			$this->cms->tbname['papoo_news_user'],
			$this->db->escape($_SESSION['nl']['language_newsd'])
		);
		$nlg_abonnenten_gesamt_anzahl = $this->db->get_var($sql); // Anzahl aller aktiven Abonnenten in den NL-Gruppen
		// alle NL Gruppen holen
		$sql = sprintf("SELECT news_gruppe_id FROM %s",
			$this->papoo_news_gruppen
		);
		$nlgrp = $this->db->get_results($sql);
		// NL Gruppen durchloopen
		// ===========> Check: Mit $grp_id auf die DB als ID zugreifen...
		if (!empty($nlgrp)) {
			foreach ($nlgrp as $grp_id) {
				$nlg_anzahl[$grp_id->news_gruppe_id] = 0; // Eintrag mit 0 im Array f�r jede Gruppe anlegen
				// Die Abonnenten zu jeder Gruppe z�hlen
				// $sql = sprintf(" SELECT COUNT(news_gruppen)
				// 						FROM %s
				// 						WHERE news_active = '1'
				// 						AND news_gruppen LIKE '%s'
				// 						AND news_user_lang = '%d'
				// 						AND deleted = 0",
				// 	$this->papoo_news_user,
				// 	"%" . $grp_id->news_gruppe_id . " %",
				// 	$this->db->escape($_SESSION['nl']['language_newsd'])
				// );

				$sql = sprintf("SELECT B.news_gruppe_id_lu
											FROM %s AS A LEFT JOIN %s AS B
											ON news_user_id=news_user_id_lu
											WHERE deleted = 0
											AND B.news_gruppe_id_lu = %s
											GROUP BY news_user_id
											",
					$this->papoo_news_user,
					DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
					"" . $grp_id->news_gruppe_id . ""
				);

				$anzahl = $this->db->get_results($sql);
				$anzahl = is_array($anzahl) ? count($anzahl) : 0;

				$nlg_anzahl[$grp_id->news_gruppe_id] = $anzahl;
			}
		}
		IfNotSetNull($nlg_anzahl);
		if (isset($_SESSION['nl']['nlgruppe']) && is_array($_SESSION['nl']['nlgruppe'])) {
			$this->all_nlgroups_to_template($_SESSION['nl']['nlgruppe'], $nlg_anzahl);
		}
		else {
			$this->all_nlgroups_to_template("", $nlg_anzahl);
		} // Neuer NL
		// Anzahl aller NLG Abonnenten ans Template �bergeben
		$_SESSION['nl']['nlggesamtanz'] = $this->content->template['nlanz'] = $nlg_abonnenten_gesamt_anzahl;

		// Die aktiven Systemabonnenten z�hlen
		IfNotSetNull($sql_add);
		$sql = sprintf("SELECT COUNT(DISTINCT(email))
										FROM %s
										WHERE (user_newsletter = 'ok' OR user_newsletter = 1)
										AND active = 1
										%s",
			$this->cms->tbname['papoo_user'],
			$sql_add
		);

		$_SESSION['nl']['sysgesamtanz'] = $this->content->template['sysgesamtanz'] = $sys_abonnenten_gesamt_anzahl = $this->db->get_var($sql);
		// Die Gesamtanzahl aller akt. Abonnenten ermitteln und ans Template �bergeben
		$_SESSION['nl']['allgesamtanz'] = $this->content->template['gesamtanz'] = $nlg_abonnenten_gesamt_anzahl + $sys_abonnenten_gesamt_anzahl;
	}

	/**
	 * System Gruppen mit Abonnenten-Anzahl ans Template �bergeben
	 *
	 * @param int $lang
	 */
	function sys_abonnenten_anzahl_to_template($lang = 1)
	{
		if ($lang) {
			// Numerische Werte umsetzen f�r die Abfrage
			$search = array("'1'", "'2'", "'3'", "'4'", "'5'", "'6'", "'7'");
			$replace = array("de", "en", "it", "es", "fr", "pt", "nl");
			// default 1 / de
			if (empty($_SESSION['nl']['language_newsd'])) $_SESSION['nl']['language_newsd'] = 1;
			$langval = preg_replace($search, $replace, $_SESSION['nl']['language_newsd'], 1);
			$sql_add = " AND (user_lang_front = '" . $langval . "' ";
			// Eintr�ge ohne Angabe: default Annahme deutsch ( = 1)
			if ($_SESSION['nl']['language_newsd'] == 1) {
				$sql_add .= " OR user_lang_front = ''";
			}
			$sql_add .= ")";
		}
		IfNotSetNull($sql_add);
		// Nur aktive Sys Abonnenten z�hlen
		$sql = sprintf("SELECT COUNT(DISTINCT(email))
								FROM %s
								WHERE active = '1'
								AND (user_newsletter = 'ok' OR user_newsletter = 1)
								%s",
			$this->cms->tbname['papoo_user'],
			$sql_add
		);
		$sys_abonnenten_gesamt_anzahl = $this->db->get_var($sql);
		$sql = sprintf("SELECT gruppeid FROM %s",
			$this->cms->tbname['papoo_gruppe']
		);
		$sysgrp = $this->db->get_results($sql);
		foreach ($sysgrp as $grp_id) {
			$sys_anzahl[$grp_id->gruppeid] = 0;
			$sql = sprintf("SELECT COUNT(DISTINCT(email))
									FROM %s T1
									INNER JOIN %s T2 ON (T1.userid = T2.userid)
									WHERE (T1.user_newsletter = 1 OR T1.user_newsletter = 'ok')
									AND T1.active = 1
									AND T2.gruppenid = '%d'
									%s",
				$this->cms->tbname['papoo_user'],
				$this->cms->tbname['papoo_lookup_ug'],
				$this->db->escape($grp_id->gruppeid),
				$sql_add
			);
			// Die Abonnenten-Anzahl einer jeden Gruppe z�hlen und das Ergebnis dem Array zuweisen
			$sys_anzahl[$grp_id->gruppeid] = $this->db->get_var($sql);
		}
		IfNotSetNull($sys_anzahl);
		// Alle Gruppendaten inkl. ihrer Abonnentenanzahl ans Template �bergeben
		if (is_array($_SESSION['nl']['gruppe'])) {
			$this->all_sysgroups_to_template($_SESSION['nl']['gruppe'], $sys_anzahl);
		}
		else {
			$this->all_sysgroups_to_template("", $sys_anzahl);
		} // Neuer NL
		// Anzahl aller NLG Abonnenten ans Template und an die Session �bergeben
		$_SESSION['nl']['sysgesamtanz'] =
		$this->content->template['sysanz'] =
			$sys_abonnenten_gesamt_anzahl;
	}

	function news_group_edit()
	{
		if (!isset($_POST['submit']) || isset($_POST['submit']) && $_POST['submit'] != "Eintragen") {
			// Werte zur Anzeige ins Template news_group_edit.html
			$grp = $this->get_group($this->checked->grp_edit_id);
			$grp = $grp[0];
			$this->content->template['grpname'] = $grp->news_gruppe_name;
			$this->content->template['grpdescrip'] = $grp->news_gruppe_description;
			$this->content->template['grpfront'] = $grp->news_grpfront;
			$this->content->template['grpmoderated'] = $grp->news_grpmoderated;
			//$this->content->template['xlink'] =
			//"./plugin.php?menuid=".$this->checked->menuid."&template=newsletter/templates/news_group.html&grp_edit_id=".$this->checked->grp_edit_id;
		}
		else {
			if (isset($this->checked->news_grpname) && $this->checked->news_grpname) {
				if ($this->checked->grp_edit_id != 1) {
					// Daten �bernehemn in die DB
					$sql = sprintf(" UPDATE %s SET news_gruppe_name = '%s',
													news_gruppe_description = '%s',
													news_grpfront = '%s',
													news_grpmoderated = '%s'
													WHERE news_gruppe_id = '%d'",
						$this->papoo_news_gruppen,
						$this->db->escape($this->checked->news_grpname),
						$this->db->escape($this->checked->news_grpdescript),
						$this->db->escape($this->checked->news_grpfront),
						$this->db->escape($this->checked->news_grpmoderated),
						$this->db->escape($this->checked->grp_edit_id)
					);
					$this->db->query($sql);
					$location_url = "./plugin.php?menuid="
						. $this->checked->menuid
						. "&template=newsletter/templates/news_group.html";
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit();
				}
				else {
					$this->content->template['grp_is_edit'] = 2;
				}
			}
			else {
				$this->content->template['grpname_error'] = 1;
			}
		}
		// Weiterleitung nach Eingabe einer neuen Gruppe. L�uft �ber form action="{$xlink}" in news_group_new.html
		// Anzeige aller vorhandenen Gruppen
		//$this->content->template['xlink'] = "./plugin.php?menuid=".$this->checked->menuid."&template=newsletter/templates/news_group.html";
	}

	/**
	 * Dump Funktion zur Erstellung eines Datendumps des Newsletters
	 */
	function make_dump()
	{
		// Sicherung erstellen
		if (!empty($this->checked->save)) {
			global $db_praefix;
			$tablelist = array();
			$tablelist[]['tablename'] = $db_praefix . "papoo_news_impressum";
			$tablelist[]['tablename'] = $db_praefix . "papoo_news_imp_lang";
			$tablelist[]['tablename'] = $db_praefix . "papoo_newsletter";
			$tablelist[]['tablename'] = $db_praefix . "papoo_newsletter_messages";
			$tablelist[]['tablename'] = $db_praefix . "papoo_news_user";
			$tablelist[]['tablename'] = $db_praefix . "papoo_news_gruppen";
			$tablelist[]['tablename'] = $db_praefix . "papoo_news_gattachments";
			$this->checked->makedump = 1;
			$this->intern_stamm->fileext = "_news";
			$this->intern_stamm->make_dump($tablelist);
		}
	}

	/**
	 * HTML und Script aus einem Text entfernen
	 *
	 * @param $document
	 * @return mixed
	 */
	function html2txt($document)
	{
		$text = "";
		$search = array(
			'@<script[^>]*?>.*?</script>@si', // Strip out javascript
			'@<[\\/\\!]*?[^<>]*?>@si',        // Strip out HTML tags
			'@<style[^>]*?>.*?</style>@siU',  // Strip style tags properly
			'@<![\\s\\S]*?--[ \\t\\n\\r]*>@'  // Strip multi-line comments including CDATA
		);
		//$text = preg_replace($search, '', $document);
		return $text;
	}

	/**
	 * $var = 1 = erw. Daten des Abonnenten liegen vor
	 *
	 * @param $var
	 * @param $gender
	 * @param $vorname
	 * @param $nachname
	 * @param $strnr
	 * @param $plz
	 * @param $ort
	 * @param $staat
	 * @param $email
	 * @return array
	 */
	function checknewsform($var, $gender, $vorname, $nachname, $strnr, $plz, $ort, $staat, $email)
	{
		// Daten aus Formular merken. Werden bei Fehler �ber z.B. <input value="{$table.neu...}" wieder ausgegeben
		foreach ($this->checked as $varname => $value) {
			if ($varname != "nlgruppe") {
				$table_datax[0][$varname] = $value;
			}
		}
		IfNotSetNull($this->checked->nlgruppe);
		$this->all_nlgroups_to_template($this->checked->nlgruppe, "", 1);
		// Pr�fung nur bei erweiterten Userdaten
		if ($var) {
			// TODO: Pflichtfelder sp�ter noch �ber Konfig festlegen
			//if (empty($plz)) $table_datax[0]['fehltplz'] = $fehler = 1; // Angabe fehlt
			/**
			 * elseif (!preg_match("^([0-9]{4,5})$", $plz))
			 * {
			 * $table_datax[0]['fehltplz'] = 2; // ung�ltige Angabe
			 * $fehler = 1;
			 * }
			 */
			//if (empty($gender)) $table_datax[0]['fehltgender'] = $fehler = 1; // Angabe fehlt
			//if (empty($vorname)) $table_datax[0]['fehltvorname'] = $fehler = 1; // Angabe fehlt
			if (empty($nachname)) $table_datax[0]['fehltnachname'] = $fehler = 1; // Angabe fehlt
			//if (empty($ort)) $table_datax[0]['fehltort'] = $fehler = 1; // Angabe fehlt
			//if (empty($strnr)) $table_datax[0]['fehltstrnr'] = $fehler = 1; // Angabe fehlt
			//if (empty($staat)) $table_datax[0]['fehltstaat'] = $fehler = 1; // Angabe fehlt
		}
		if (!$this->validateEmailAddress($email)) {
			$table_datax[0]['fehltemail'] = 2; //ung�ltige Angabe
			$fehler = 1;
		}
		IfNotSetNull($table_datax);
		IfNotSetNull($fehler);
		$this->content->template['table_datax'] = $table_datax;
		return array(
			$table_datax,
			$fehler
		);
	}

	/**
	 * @param $email
	 * @return bool
	 */
	function validateEmailAddress($email)
	{
		if (preg_match('#^[^\\x00-\\x1f@]+@[^\\x00-\\x1f@]{2,}\.[a-z]{2,}$#i', $email)) return true;
		else return false;
	}

	/**
	 * Liste aller Attachments eines NL ans Template �bergeben & schauen, ob sie noch da sind
	 *
	 * @return void
	 */
	function getAttachmentsList()
	{
		// Hole Attachment-Daten f�rs Template
		$sql = sprintf("SELECT id,
								name,
								name_stored,
								size
								FROM %s
								WHERE news_id = '%d'
								ORDER BY name, size",
			$this->cms->tbname['papoo_news_attachments'],
			$this->db->escape($this->checked->news_id)
		);
		$result = $this->content->template['newsletter_file_list'] = $this->db->get_results($sql, ARRAY_A);
		#$this->content->template['faq_id'] = $faq_id;
		// Sind die Dateien auch (noch) vorhanden?
		if ($result) {
			foreach ($this->content->template['newsletter_file_list'] AS $key => $value) {
				file_exists(PAPOO_ABS_PFAD
					. '/plugins/newsletter/attachments/'
					. $this->content->template['newsletter_file_list'][$key]['name_stored'])
					? $status = 1 : $status = 0;
				if (!$status) {
					$this->content->template['fehler8'] = 1;
				} // Hilfetext zum Fehler ausgeben
				// Fehlermeldung bei dieser Datei anzeigen
				$this->content->template['newsletter_file_list'][$key]['file_status'] = $status;
			}
		}
	}

	/**
	 * @param int $news_id
	 * @return int|null
	 */
	function count_attachments($news_id = 0)
	{
		$sql = sprintf("SELECT COUNT(news_id)
								FROM %s
								WHERE news_id = '%d'",
			$this->cms->tbname['papoo_news_attachments'],
			$this->db->escape($news_id)
		);
		return $this->db->get_var($sql);
	}

	/**
	 * Attachment hochladen
	 *
	 * @return mixed
	 */
	function news_attachment_upload()
	{
		$fehler = 0;
		// Filename angegeben oder bloss "Hochladen" gedr�ckt?
		if (empty($_FILES['myfile']['name'])) {
			$this->content->template['fehler4'] = $fehler = 1;
		} // Keine Datei ausgew�hlt
		else {
			// Pr�fen, ob die Datei f�r diese FAQ-Version bereits hochgeladen wurde.
			// Die Dateien sind identisch, wenn filename und -size gleich sind.
			$sql = sprintf("SELECT *
								FROM %s
								WHERE news_id = '%d'
								AND name = '%s'
								AND size = '%d'",
				$this->cms->tbname['papoo_news_attachments'],
				$this->db->escape($this->checked->news_id),
				$this->db->escape($_FILES['myfile']['name']),
				$this->db->escape($_FILES['myfile']['size'])
			);
			$result = $this->db->get_var($sql);
			// Wurde schon f�r diesen NL hochgeladen, dann Fehler ausl�sen
			if ($result) {
				$this->content->template['fehler9'] = $fehler = 1;
			}
			else {
				// Datei hochladen. Falls der File-Name bei ungleicher Filesize bereits vorhanden ist, eine Kopie erstellen
				// Zielverzeichnis
				$destination_dir = PAPOO_ABS_PFAD
					. '/plugins/newsletter/attachments'; // evtl. �ber Konfig zug�nglich machen
				// nicht erlaubte Dateiendungen
				$extensions = array(
					'php',
					'php3',
					'php4',
					'php5',
					'phtml',
					'html',
					'htm',
					'exe',
					'cgi',
					'pl',
					'js'
				);
				// basedir und Klasse file_upload bekanntmachen
				$upload_do = new file_upload();
				// Upload-Parameter-Aufbau (filename als array!):
				// ((array('filename','filetype','filesize','tmp_name','error')), dir, rename, disallowed ext, block)
				if (!$upload_do->upload($_FILES['myfile'], $destination_dir, 1, $extensions, 1)) {
					// Fehlermeldung ausgeben; Fehler-Text erzeugt die Upload class selbst
					$this->content->template['fehler5'] = $fehler = $upload_do->error;

				} else {
					// Hole Filedaten
					$files = $upload_do->file;
					// Schon in der DB? (gleicher generierter Name, andere Size und Kopie...)
					// Ist, warum auch immer, die Datei im Verzeichnis gel�scht worden,
					// dann haben wir bereits diesen Eintrag in der DB und er darf nicht erneut eingetragen werden.
					$sql = sprintf("SELECT *
											FROM %s
											WHERE news_id = '%d'
											AND name_stored = '%s'",
						$this->cms->tbname['papoo_news_attachments'],
						$this->db->escape($this->checked->news_id),
						$this->db->escape($files['name'])
					);
					$result = $this->db->get_var($sql);
					// Nur eintragen, wenn noch nicht vorhanden
					if (empty($result)) {
						// neues Attachment in die DB eintragen
						$sql = sprintf("INSERT INTO %s
													SET news_id = '%d',
														name = '%s',
														name_stored = '%s',
														size = '%s'",
							$this->cms->tbname['papoo_news_attachments'],
							$this->db->escape($this->checked->news_id),
							$this->db->escape($_FILES['myfile']['name']),
							$this->db->escape($files['name']),
							$this->db->escape($files['size'])
						);
						$this->db->query($sql);
						$this->content->template['attachment_loaded'] = 1;
					}
				}
			}
		}
		return $fehler;
	}

	/**
	 * Ein Attachment l�schen
	 *
	 * @return mixed
	 */
	function news_attachment_delete()
	{
		$attach_id = $this->db->escape($this->checked->submit_del);
		// Hole den realen Dateinamen der Datei, wie er im Verzeichnis existiert
		$sql = sprintf("SELECT name_stored
									FROM %s
									WHERE id='%s'",
			$this->cms->tbname['papoo_news_attachments'],
			$this->db->escape($attach_id)
		);
		$name_stored = $this->db->get_var($sql);
		// Ist die Datei vorhanden? Sonst nur in der DB entfernen.
		file_exists(PAPOO_ABS_PFAD . '/plugins/newsletter/attachments/' . $name_stored) ? $status = 1 : $status = 0;
		// F�r das L�schen eines NL sollder NL auch gel�scht werden k�nnen, wenn durch einen Fehler die zugeh�rige Datei
		// im Verzeichnis nicht mehr existiert. Es w�rde sonst ein Fehler auftreten, der das L�schen des NL verhindert.
		// Beim L�schen eines NL werden ohnehin alle Attachments gel�scht und es ist daher uninteressant, ob er schon weg ist.
		// Andere Fehler beim L�schen verhindern jedoch weiterhin das L�schen des NL
		if ($status) { // Datei im Verzeichnis l�schen, wenn sie da ist
			// Verzeichnis
			$destination_dir = PAPOO_ABS_PFAD . 'plugins/newsletter/attachments';
			// basedir und Klasse file_upload bekanntmachen
			$upload_do = new file_upload();
			// Upload ((array('filename','filetype','filesize','tmp_name','error')), dir, rename, disallowed ext, block)
			$del_ok = $upload_do->delete_file($name_stored, $destination_dir);
		}
		$sql = sprintf("DELETE FROM %s WHERE id ='%d'",
			$this->cms->tbname['papoo_news_attachments'],
			$attach_id
		);
		$this->db->query($sql);
		// Meldung gel�scht ausgeben
		$this->content->template['attachment_is_del'] = 1;
		IfNotSetNull($del_ok);
		IfNotSetNull($fehler);
		// Wenn Datei nicht gel�scht werden konnte
		if (!$del_ok) {
			if(!isset($upload_do)) {
				$upload_do = new file_upload();
			}
			$this->content->template['fehler7'] = $fehler = $upload_do->error;
		}
		return $fehler;
	}

	function nl_versand_stepping()
	{
		if (isset($this->checked->cancel) && $this->checked->cancel) {
			$this->redirect(); // Ende & zur news_nl_list.html
		}
		else { // Button Versand oder default
			$_SESSION['nl']['anz_gesendete'] = isset($_SESSION['nl']['anz_gesendete']) ? $_SESSION['nl']['anz_gesendete'] : 0;

			if ($_SESSION['nl']['anz_gesendete'] == $_SESSION['nl']['anz_abonnenten']) {
				$this->redirect(); // Exit & zur news_nl_list.html
			}

			// Anzahl Mails aus Template, temp. User-Vorgabe oder aus Konfig.
			if (isset($this->checked->mails_per_step) AND (string)ctype_digit($this->checked->mails_per_step)) {
				$this->content->template['mails_per_step'] = $_SESSION['nl']['mails_per_step'] = $this->checked->mails_per_step;
			}
			else {
				$this->content->template['mails_per_step'] = $_SESSION['nl']['mails_per_step'] = $_SESSION['nl']['admindaten'][0]->mails_per_step;
			}; // default

			$this->make_and_send_nl();// Mail aufbereiten und versenden

			if ($_SESSION['nl']['anz_gesendete'] == $_SESSION['nl']['anz_abonnenten']) {
				$this->redirect(); // Ende & zur news_nl_list.html
			}

			$this->content->template['startfrom'] = $_SESSION['nl']['anz_gesendete'];
			$this->content->template['news_id'] = $this->checked->news_id;
			// Template Nachrichten
			IfNotSetNull($_SESSION['nl']['anz_error_no_confirm_code']);
			$this->content->template['text1'] =
				sprintf(NEWS_NL_SEND_TO_X_SUBSCRIBERS,
					$_SESSION['nl']['anz_gesendete'] - $_SESSION['nl']['anz_error_no_confirm_code'], $_SESSION['nl']['anz_abonnenten']);
			$mails_per_pageload = $_SESSION['nl']['mails_per_step'];
			$this->content->template['text2'] = sprintf(NEWS_NL_SEND_NEXT_X_MAILS, $mails_per_pageload);
		}
	}

	function redirect()
	{
		$sql = sprintf("UPDATE %s SET news_date_send = '%s',
										news_send = 1,
										news_abbonennten = '%d',
										news_gruppe = '%s',
										news_nlgruppe = '%s'
									WHERE news_id = '%d'",
			$this->papoo_newsletter,
			date("d.m.Y - H:i:s"),
			$this->db->escape($_SESSION['nl']['anz_gesendete'] - $_SESSION['nl']['anz_error_no_confirm_code']),
			$this->db->escape($_SESSION['nl']['gruppe_sys']),
			$this->db->escape($_SESSION['nl']['gruppe_nl']),
			$this->db->escape($_SESSION['nl']['insertid'])
		);
		$this->db->query($sql);
		$sql = sprintf("UPDATE %s SET news_date_send = '%s',
										news_send = 1,
										news_abbonennten = '%d',
										news_gruppe = '%s',
										news_nlgruppe = '%s'
									WHERE news_id = '%d'",
			$this->cms->tbname['papoo_news_protocol_newsletter'],
			date("d.m.Y - H:i:s"),
			$this->db->escape($_SESSION['nl']['anz_gesendete'] - $_SESSION['nl']['anz_error_no_confirm_code']),
			$this->db->escape($_SESSION['nl']['gruppe_sys']),
			$this->db->escape($_SESSION['nl']['gruppe_nl']),
			$this->db->escape($_SESSION['nl']['insertid'])
		);
		$this->db->query($sql);
		$location_url = $_SERVER['PHP_SELF']
			. "?menuid="
			. $this->checked->menuid
			. "&template=newsletter/templates/news_nl_list.html&anz_a="
			. $_SESSION['nl']['anz_abonnenten']
			. "&anz_g="
			. ($_SESSION['nl']['anz_gesendete'] - $_SESSION['nl']['anz_error_no_confirm_code']); // f�r die Message "NL an xx von yy Abonn. gesendet"
		if ($_SESSION['nl']['address_in_error'] OR $_SESSION['nl']['anz_error_no_confirm_code']) {
			$location_url .= "&mie=1";
		}
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">Weiter</a>';
		}
		else {
			header("Location: $location_url");
		}
		$this->content->template['address_in_error'] = $_SESSION['nl']['address_in_error'];
		unset($_SESSION['nl']);
		$_SESSION['nl']['address_in_error'] = $this->content->template['address_in_error'];
		exit;
	}

	function show_invalid_mail_addresses()
	{
		$this->content->template['address_in_error'] = $_SESSION['nl']['address_in_error'];
		unset($_SESSION['nl']['address_in_error']);
	}

	/**
	 * @param $array
	 * @param $key
	 * @param int $sort_flags
	 * @return array
	 */
	function array_unique_by_subitem($array, $key, $sort_flags = SORT_STRING){
		$items = array();
		// Die Subeitems auslesen
		foreach($array as $index => $item) {
			$items[$index] = $item[$key];
		}
		//Die Subitems mit array_unique bearbeiten
		$uniqueItems = array_unique($items, $sort_flags);
		//Der eigentliche Array über den Key mit den selektierten Subitems abgleichen
		$results = array_intersect_key($array, $uniqueItems);
		$items = array();
		foreach($results as $res) {
			array_push($items,$res);
		}
		return $items;
	}

	/**
	 * Stellt alle Daten bereit, die f�r den Versand mit nl_versand_stepping() n�tig sind
	 * Vermeidung von mehrfachen Zugriffen auf die DB und mehrfachem Datenaufbau bei jedem Abonnenten.
	 * Die Daten werden hier einmalig f�r alle Abonnenten bereit gestellt.
	 */
	function nl_versand()
	{
		$this->content->template['news_id'] = $this->checked->news_id;
		IfNotSetNull($this->checked->submit);
		switch ($this->checked->submit) {
			// Button Verschicken
		case $this->content->template['message_20021']:
			#$this->check_inputs_and_store_to_session_and_template();
			$this->show_form(); // nur f�r das Holen der NL-Sprache
			// Gew�hlte Gruppen in die Session speichern
			$_SESSION['nl']['nlgruppe'] = $this->checked->nlgruppe;
			$_SESSION['nl']['gruppe'] = $this->checked->gruppe;

			// Plausibilit�tspr�fungen f�r die Gruppen
			if (empty($this->checked->gruppe) and empty($this->checked->nlgruppe)) {
				$fehler = 1; // Keine Gruppe ausgew�hlt
			}
			if (!empty($this->checked->gruppe['aa']) and !empty($this->checked->nlgruppe)) {
				$fehler = 2; // Alle oder NL
			}
			if (!empty($this->checked->gruppe['ag']) and !empty($this->checked->nlgruppe)) {
				$fehler = 2; // Alle oder Sys
			}
			if (is_array($this->checked->gruppe)) {
				if ((in_array("aa", $this->checked->gruppe) or in_array("ag",
							$this->checked->gruppe)) and count($this->checked->gruppe) > 1
				) {
					$fehler = 2;
				} // Sys zus�tzlich
				if (in_array("aa", $this->checked->gruppe) and in_array("ag",
						$this->checked->gruppe) and count($this->checked->gruppe) >= 2
				) {
					$fehler = 2;
				} // Sys zus�tzlich
			}
			// Aktivieren, falls die Gruppe Test nur alleinige Gruppe beim Versand sein darf
			if ($this->checked->nlgruppe[$_SESSION['nl']['test_id'][0]->news_gruppe_id] and
				(count($this->checked->nlgruppe) > 1 or (count($this->checked->gruppe)))
			) {
				$fehler = 3;
			} // Test nur allein
			if (isset($fehler) && $fehler) {
				$this->content->template['news_fehltgrp'] = $fehler;
				// Alle eingegebenen Werte mit Abo-Anzahl zur�ck ins Template
				$this->sys_abonnenten_anzahl_to_template(1); // Sys Gruppen mit Abonnenten-Anzahl ans Template newssend.html �bergeben
				$this->nl_abonnenten_anzahl_to_template(1); // NL Gruppen mit Abonnenten-Anzahl ans Template newssend.html �bergeben
				$this->show_form(1);                        // NL Daten f�r diese NL-ID holen und ans Template geben
				return;
			}
			// Keine Fehler: Alle User raussuchen, die den NL abonniert haben

			// Zuerst die ausgew�hlten NL-Gruppen
			if (is_array($this->checked->nlgruppe)) {

				$grsize = count($this->checked->nlgruppe);
				$i = 0;
				$sql = sprintf("SELECT * FROM %s as A
												LEFT JOIN %s as B
												ON A.news_user_id = B.news_user_id_lu
												WHERE A.news_active = '1'
												AND B.news_active = '1'
												AND deleted = 0
												AND news_user_lang = '%s'
												AND (",
					$this->cms->tbname['papoo_news_user'],
					$this->cms->tbname['papoo_news_user_lookup_gruppen'],
					$this->db->escape($_SESSION['nl']['language_newsd'])
				);
				foreach ($this->checked->nlgruppe as $grupped) {
					$i++;
					$sql .= sprintf("B.news_gruppe_id_lu LIKE %s OR B.news_gruppe_id_lu LIKE '%s'",
						"'%" . $this->db->escape($grupped) . "%'",
						$this->db->escape($grupped)
					);
					if ($i < $grsize) $sql .= " OR ";
				}
				$sql .= ")";


				// Alle NL-Abonnenten
				$nlresult = $this->db->get_results($sql, ARRAY_A);
				//doppelte aus array rausschmeißen
				$nlresult = $this->array_unique_by_subitem($nlresult,'news_user_id');

			} // Oder alle NL-Gruppen
			else {
				if ($this->checked->gruppe['aa'] == "aa"
					or $this->checked->gruppe['ag'] == "ag"
				) {
					$sql = sprintf("SELECT * FROM %s
													WHERE news_active = 1
													AND deleted = 0
													AND news_user_lang = '%s'",
						$this->cms->tbname['papoo_news_user'],
						$this->db->escape($_SESSION['nl']['language_newsd'])
					);
					$nlresult = $this->db->get_results($sql, ARRAY_A);
				}
			}
			$result = array();
			// alle oder die ausgew�hlten System(Papoo)-Gruppen
			if (!empty($this->checked->gruppe)) { // Systemgruppen
				if ($this->checked->gruppe['aa'] == "aa") { // Alle Sysgruppen holen
					$sql = sprintf("SELECT userid AS news_user_id,
												username,
												user_firma AS news_name_firma,
												user_land AS news_name_staat,
												user_vorname AS news_name_vor,
												user_nachname AS news_name_nach,
												user_strasse AS news_name_str,
												user_hausnummer,
												user_ort AS news_name_ort,
												user_telefon AS news_phone,
												user_plz AS news_name_plz,
												active AS news_active,
												user_gender AS news_name_gender,
												user_timestamp AS news_signup_date,
												news_hardbounce,
												news_hardbounce_time,
												email as news_user_email,
												confirm_code as news_key
												FROM %s
												WHERE active = 1
												AND (user_newsletter = 'ok' OR user_newsletter = 1)
												GROUP BY email",
						$this->cms->tbname['papoo_user']
					);
					$result = $this->db->get_results($sql, ARRAY_A);
				}
				else {
					if (!$this->checked->gruppe['ag']) { // Bestimmte Sysgruppen holen
						$grsize = count($this->checked->gruppe);
						$i = 0;
						$sql = sprintf("SELECT T1.userid AS news_user_id,
													username,
													user_firma AS news_name_firma,
													user_land AS news_name_staat,
													user_vorname AS news_name_vor,
													user_nachname AS news_name_nach,
													user_strasse AS news_name_str,
													user_hausnummer,
													user_ort AS news_name_ort,
													user_telefon AS news_phone,
													user_plz AS news_name_plz,
													active AS news_active,
													user_gender AS news_name_gender,
													user_timestamp AS news_signup_date,
													news_hardbounce,
													news_hardbounce_time,
													email as news_user_email,
													confirm_code as news_key
													FROM %s T1, %s  ",
							$this->cms->tbname['papoo_user'],
							$this->cms->tbname['papoo_lookup_ug']
						);
						if (is_array($this->checked->gruppe)) {
							$sql .= ' WHERE ';
						}
						foreach ($this->checked->gruppe as $gruppe) {
							$i++;
							$sql .= " active = '1' AND ";
							$sql .= $this->cms->tbname['papoo_lookup_ug'] . ".gruppenid = '";
							$sql .= $this->db->escape($gruppe) . "' AND ";
							$sql .= $this->cms->tbname['papoo_lookup_ug'] . ".userid = ";
							$sql .= "T1.userid";
							$sql .= " AND (user_newsletter = 'ok'  OR user_newsletter = 1)";
							if ($i < $grsize) $sql .= " OR";
						}
						$sql .= " GROUP BY email";
						$result = $this->db->get_results($sql, ARRAY_A);
					}
				}
			}
			// Jetzt noch Flex-Suchergebnis, wenn ausgew�hlt
			if (($this->checked->gruppe['FS']
					OR $this->checked->gruppe['aa'] == "aa")
				AND $_SESSION['nl']['mv_newsletter_sql']
			) {
				$mv_result = $this->db->get_results($_SESSION['nl']['mv_newsletter_sql'], ARRAY_A);

				foreach ($mv_result AS $key => $value) {
					if ($value['EMail_9']
						AND $value['mv_content_userid']
					) {
						$sql = sprintf("SELECT userid AS news_user_id,
													username,
													user_firma AS news_name_firma,
													user_land AS news_name_staat,
													user_vorname AS news_name_vor,
													user_nachname AS news_name_nach,
													user_strasse AS news_name_str,
													user_hausnummer,
													user_ort AS news_name_ort,
													user_telefon AS news_phone,
													user_plz AS news_name_plz,
													active AS news_active,
													user_gender AS news_name_gender,
													user_timestamp AS news_signup_date,
													news_hardbounce,
													news_hardbounce_time,
													email as news_user_email,
													confirm_code as news_key
													FROM %s
													WHERE email = '%s'
													AND userid = '%d'",
							$this->cms->tbname['papoo_user'],
							$value['email_3'],
							$value['mv_content_userid']
						);
						$mvresult[$key] = $this->db->get_results($sql, ARRAY_A);
					}
					#else unset($mvresult[$key]);
				}
			}
			// Jetzt haben wir was in $nlresult/$result/$mv_result, in allen 3 oder in einer Kombi, je nach User-Auswahl
			// Dubletten entfernen, ebenso nicht valide Adressen
			// Dubletten treten auf, wenn in den Sysgruppen eine Mail mehreren Gruppen zugeordnet ist und evtl. auch durch die Flex
			$user_array = $mail_adresses = array();
			IfNotSetNull($nlresult);

			if(is_countable($nlresult)) {
				for ($i = 0; $i <= count($nlresult) - 1; $i++) {
					$nlresult[$i]['news_user_email'] = trim($nlresult[$i]['news_user_email']);
					if (!in_array($nlresult[$i]['news_user_email'], $mail_adresses)) { // Dubletten �berlesen
						if ($this->validateEmailAddress($nlresult[$i]['news_user_email'])) {
							$nlresult[$i]['nl_user'] = 1; // als NL-Abonnenten kennzeichnen
							$user_array[] = $nlresult[$i]; // alle
							$mail_adresses[] = $nlresult[$i]['news_user_email']; // email only
						} // Emailaddress not valid
						else {
							$address_in_error[] = "Invalid (found in NL group): " . $nlresult[$i]['news_user_email'] . "<br>";
						}
					}
					else {
						$address_in_error[] = "Multiple (found in NL group): " . $nlresult[$i]['news_user_email'] . "<br>";
					}
				}
			}

			// Sysgruppen werden jetzt auch auf Dubletten inkl. der NL-Gruppen gechecked ($mailaddresses enth�lt alle)
			if(is_countable($result)) {
				for ($i = 0; $i <= count($result) - 1; $i++) {
					$result[$i]['news_user_email'] = trim($result[$i]['news_user_email']);
					if (!in_array($result[$i]['news_user_email'], $mail_adresses)) { // Dubletten �berlesen
						if ($this->validateEmailAddress($result[$i]['news_user_email'])) {
							$result[$i]['papoo_user'] = 1; // als Abonnenten aus der papoo_user kennzeichnen
							$user_array[] = $result[$i]; // alle
							$mail_adresses[] = $result[$i]['news_user_email']; // email only
						}
						elseif ($result[$i]['news_user_email']) {
							$address_in_error[] = "Invalid (found in Sys group): " . $result[$i]['news_user_email'] . "<br>";
						}
					}
					elseif ($result[$i]['news_user_email']) {
						$address_in_error[] = "Multiple (found in Sys group): " . $result[$i]['news_user_email'] . "<br>";
					}
				}
			}

			if (!empty($mvresult)) {
				foreach ($mvresult as $key => $value) {
					$value[0]['news_user_email'] = trim($value[0]['news_user_email']);
					if (!in_array($value[0]['news_user_email'], $mail_adresses)) { // Dubletten �berlesen
						if ($this->validateEmailAddress($value[0]['news_user_email'])) {
							$mvresult[$key][0]['flex_user'] = 1; // als Abonnenten aus der papoo_user kennzeichnen
							$user_array[] = $mvresult[$key][0]; // alle
							$mail_adresses[] = $value[0]['news_user_email']; // email only
						}
						elseif ($value[0]['news_user_email']) {
							$address_in_error[] = "Invalid (found in Flex search result): " . $value[0]['news_user_email'] . "<br>";
						}
					}
					elseif ($value[0]['news_user_email']) {
						$address_in_error[] = "Multiple (found in Flex search result): " . $value[0]['news_user_email'] . "<br>";
					}
				}
			}
			$_SESSION['nl']['address_in_error'] = isset($address_in_error) ? $address_in_error : NULL;
			$_SESSION['nl']['user_array'] = $user_array;
			$_SESSION['nl']['anz_abonnenten'] = count($user_array);
			// Gruppen komprimieren f�r die Speicherung in der DB
			$sysgruppe = "";
			$nlgruppe = "";
			if ($this->checked->gruppe) {
				foreach ($this->checked->gruppe as $grupped) {
					$sysgruppe = $grupped . " " . $sysgruppe;
				}
			}
			if ($this->checked->nlgruppe) {
				foreach ($this->checked->nlgruppe as $nlgrupped) {
					$nlgruppe = $nlgrupped . " " . $nlgruppe;
				}
			}
			$_SESSION['nl']['gruppe_sys'] = $sysgruppe;
			$_SESSION['nl']['gruppe_nl'] = $nlgruppe;

			// Ist das ein erstmalig versendeter NL? (Datum gesendet != "")
			$sql = sprintf("SELECT news_send FROM %s
													WHERE news_id = '%d'",
				$this->cms->tbname['papoo_newsletter'],
				$this->checked->news_id
			);
			$_SESSION['nl']['already_sent'] = $already_sent = $this->db->get_var($sql);
			// Wenn schon mal gesendet, dann ist dieser NL als ein neuer NL anzusehen
			// (Annahme: ver�nderte Gruppen; neuer Timestamp f�r gesendet etc.)
			if ($already_sent) {
				$sql = sprintf("INSERT INTO %s SET news_date = '%s',
														news_date_send = '%s',
														news_inhalt_lang = '%s',
														news_inhalt = '%s',
														news_inhalt_html = '%s',
														news_header = '%s',
														news_send = '1',
														news_abbonennten = '%d',
														news_gruppe = '%s',
														news_nlgruppe = '%s'",
					$this->cms->tbname['papoo_newsletter'],
					date("d.m.Y - H:i:s"),
					date("d.m.Y - H:i:s"),
					$this->db->escape($_SESSION['nl']['language_newsd']),
					$this->db->escape($_SESSION['nl']['news_inhalt']),
					$this->db->escape($_SESSION['nl']['news_inhalt_html']),
					$this->db->escape($_SESSION['nl']['news_betreff']),
					$this->db->escape($_SESSION['nl']['anz_gesendete'] - $_SESSION['nl']['anz_error_no_confirm_code']),
					$this->db->escape($_SESSION['nl']['gruppe_sys']),
					$this->db->escape($_SESSION['nl']['gruppe_nl'])
				);
				$this->db->query($sql);
				$insertid = $this->db->insert_id;
			}
			else $insertid = $this->checked->news_id;
			$_SESSION['nl']['insertid'] = $insertid;
			// NL Speichern f�r den Nachweis/protokoll
			$sql = sprintf("INSERT INTO %s SET news_id = '%d',
													news_date = '%s',
													news_date_send = '%s',
													news_inhalt_lang = '%s',
													news_inhalt = '%s',
													news_inhalt_html = '%s',
													news_header = '%s',
													news_send = '1',
													news_abbonennten = '%d',
													news_gruppe = '%s',
													news_nlgruppe = '%s'",
				$this->cms->tbname['papoo_news_protocol_newsletter'],
				$insertid,
				date("d.m.Y - H:i:s"),
				date("d.m.Y - H:i:s"),
				$this->db->escape($_SESSION['nl']['language_newsd']),
				$this->db->escape($_SESSION['nl']['news_inhalt']),
				$this->db->escape($_SESSION['nl']['news_inhalt_html']),
				$this->db->escape($_SESSION['nl']['news_betreff']),
				$this->db->escape($_SESSION['nl']['anz_gesendete'] - $_SESSION['nl']['anz_error_no_confirm_code']),
				$this->db->escape($_SESSION['nl']['gruppe_sys']),
				$this->db->escape($_SESSION['nl']['gruppe_nl'])
			);
			$this->db->query($sql);
			// Attachment-Aufbereitung f�r den Versand
			// und kopieren, wenn es ein bereits versendeter NL ist
			$this->getAttachmentsList();
			if (is_array($this->content->template['newsletter_file_list'])) {
				foreach ($this->content->template['newsletter_file_list'] as $attach) {
					if (file_exists(PAPOO_ABS_PFAD
						. "/plugins/newsletter/attachments/"
						. $attach['name_stored'])) {
						$file_dest_name = time() . "_" . $attach['name_stored'];
						if ($already_sent) {
							if (!copy(
								PAPOO_ABS_PFAD
								. "/plugins/newsletter/attachments/"
								. $attach['name_stored'],

								PAPOO_ABS_PFAD
								. "/plugins/newsletter/attachments/"
								. $file_dest_name)
							) {
								$file_dest_name = "";
								echo "Fehler beim Kopieren der Datei " . $attach['name'] . "<br />";
							}
							else {
								$attach_array[] = PAPOO_ABS_PFAD
									. "/plugins/newsletter/attachments/"
									. $file_dest_name;
							}
							// Attachment in die DB eintragen
							$sql = sprintf("INSERT INTO %s
															SET news_id = '%d',
																name = '%s',
																name_stored = '%s',
																size = '%s'",
								$this->cms->tbname['papoo_news_attachments'],
								$this->db->escape($insertid),
								$this->db->escape($attach['name_stored']),
								$this->db->escape($file_dest_name),
								$this->db->escape($attach['size'])
							);
							$this->db->query($sql);
						}
						else {
							$attach_array[] = PAPOO_ABS_PFAD
								. "/plugins/newsletter/attachments/"
								. $attach['name_stored'];
						}
						// Speichern f�r den Nachweis/Protokoll
						$sql = sprintf("INSERT INTO %s
														SET news_id = '%d',
															name = '%s',
															name_stored = '%s',
															size = '%s'",
							$this->cms->tbname['papoo_news_protocol_attachments'],
							$this->db->escape($insertid),
							$this->db->escape($attach['name_stored']),
							$this->db->escape($file_dest_name),
							$this->db->escape($attach['size'])
						);
						$this->db->query($sql);
					}
				}
			}
			$_SESSION['nl']['attach_array'] = isset($attach_array) ? $attach_array : NULL;

			// Tag #Online_Link# übersetzen
			$online_link = $this->cms->title_send
				. "/plugin.php?menuid=1&template=newsletter/templates/news_archiv.html&news_id=" . $insertid;

			$_SESSION['nl']['news_inhalt_html'] =
				str_replace('#Online_Link#', $online_link, $_SESSION['nl']['news_inhalt_html']);
			$_SESSION['nl']['news_inhalt'] =
				str_replace('#Online_Link#', $online_link, $_SESSION['nl']['news_inhalt']);
			if ($this->news_addon !== null) {
				$this->news_addon->send_news_mail_replace('#Online_Link#', $online_link);
			}

			// Impressumdaten holen und einf�gen
			$sql = sprintf("SELECT * FROM %s
											WHERE news_imp_lang_id = '%d'",
				$this->cms->tbname['papoo_news_imp_lang'],
				$this->db->escape($_SESSION['nl']['language_newsd'])
			);
			$result = $this->db->get_results($sql);
			// Text NL: K�ndigungslink und Impressum
			$_SESSION['nl']['news_imptext1'] =
				str_replace('#url#', $this->cms->title_send . PAPOO_WEB_PFAD, $this->content->template['news_imptext1']); // Seiten-URL
			$_SESSION['nl']['news_imptext1'] =
				str_replace('#imp#', $result[0]->news_imp_imp, $_SESSION['nl']['news_imptext1']); // Impressum
			if (!empty($_SESSION['nl']['news_inhalt_html'])) {
				// HTML NL: K�ndigungslink allein
				$_SESSION['nl']['news_imptext2'] =
					str_replace('#url#', $this->cms->title_send . PAPOO_WEB_PFAD, $this->content->template['news_imptext2']); // Seiten-URL
				$_SESSION['nl']['news_inhalt_html'] = $_SESSION['nl']['news_inhalt_html'] . "\n\r";
				// Template addon_newsletter.template.utf8.html nutzen und Werte f�r $betreff (<title>), $url, $content (HTML NL), $impressum via Smarty ersetzen
				// und in $_SESSION['nl']['news_template'] speichern
				$_SESSION['nl']['news_template'] = $this->send_news_make_html_mail($result[0]->news_imp_imp, $result[0]->news_imp_imp_html);
			}

			$location_url = "./plugin.php?menuid="
				. $this->checked->menuid
				. "&template=newsletter/templates/news_nl_send_parts.html"
				. $this->checked->news_lang;
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit();
			break;

		case $this->content->template['plugin']['newsletter']['submit']['cancel']: // Abbruch
			$location_url = $_SERVER['PHP_SELF']
				. "?menuid="
				. $this->checked->menuid
				. "&template=newsletter/templates/news_nl_list.html";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			unset($_SESSION['nl']);
			exit;
			break;
		default :
			// Templatedaten bereitstellen
			$ng = $this->get_old_news($this->checked->news_id); // Falls schon mal gesendet, die Gruppen holen
			// FIXME: War vorher nicht gesetzt
			$old = $this->get_old_news($this->checked->news_id);
			$_SESSION['nl']['language_newsd'] = $old[0]->news_inhalt_lang ? $old[0]->news_inhalt_lang : 1; // default wenn 0 / "" etc.
			$_SESSION['nl']['nlgruppe'] = explode(" ", $ng[0]->news_nlgruppe);
			$this->nl_abonnenten_anzahl_to_template(1);
			$_SESSION['nl']['gruppe'] = explode(" ", $ng[0]->news_gruppe);
			$this->sys_abonnenten_anzahl_to_template(1);
			$_SESSION['nl']['news_betreff'] = $ng[0]->news_header;
			$_SESSION['nl']['news_inhalt_html'] = $html = $ng[0]->news_inhalt_html;
			$_SESSION['nl']['news_inhalt'] = $ng[0]->news_inhalt;
			#$this->check_inputs_and_store_to_session_and_template();
			$this->getAttachmentsList();
			if (isset($_SESSION['nl']['mv_newsletter_sql']) && $_SESSION['nl']['mv_newsletter_sql']) {
				$this->content->template['flex'] = 1; // f�r Textzusatz "und Flex-Suchergebnis"
			}
			$this->show_form();

			if (!empty($this->checked->preview)) {
				session_commit();
				header('Content-Type: text/html; charset=utf-8');
				header('Cache-Control: private, max-age=10');
				header("Content-Security-Policy: default-src 'self' 'unsafe-inline' data: https: http:; script-src 'unsafe-inline'; worker-src 'none'; frame-ancestors 'self'; form-action 'none'; connect-src 'none'; sandbox allow-popups");
				header('Content-Length: '.strlen($html));
				echo $html;
				exit();
			}
		}
	}

	function make_and_send_nl()
	{
		$result_all = $_SESSION['nl']['user_array']; // alle Empf�nger
		$i = $_SESSION['nl']['anz_gesendete']; // bisher gesendete
		$i2 = $_SESSION['nl']['mails_per_step']; // Anzahl Mails, die auf einmal zu senden sind (hier)
		if ($_SESSION['nl']['anz_gesendete'] < $_SESSION['nl']['anz_abonnenten']) {
			// aktuellen Ausschnitt aus allen Emails erstellen
			$result = array_slice($result_all, $i, $i2, true);
			foreach ($result as $new) {
				if ($i2) { // z�hlt runter, Anzahl der Mails, die auf einmal gesendet werden
					// das array besteht u. U. aus 2 verschiedenen Arrays (unterschiedliche Elemente) NL-Gruppe und Sys-Gruppe
					$email = $new['news_user_email']; // nur eine von beiden ist da
					$key = $new['news_key'] . $new['confirm_code']; // nur einer von beiden ist da
					// Nur versenden, wenn auch ein eindeutiger ConfirmCode zwecks K�ndigung vorhanden ist
					// Verhindert den Versand, wenn bei DB-Fehler (oder warum auch immer) der Confirm-Code fehlt
					// Hinweis: Ein Abonnent, der sich nicht abmelden kann, kann den Anwalt bem�hen !!!
					$this->mail_it->Encoding = "8bit";
					if (empty($key)) {
						$key = md5($new['username'] . time() . rand(1, 10000000));
						$sql = sprintf("UPDATE %s SET confirm_code = '%s'
													WHERE userid = '%d'",
							$this->cms->tbname['papoo_user'],
							$key,
							$new['news_user_id']
						);
						$this->db->query($sql);
						##$_SESSION['nl']['anz_error_no_confirm_code']++; // Fehler hochz�hlen
						#$_SESSION['nl']['address_in_error'][] =
						#"Kein Versand an " . $email . ". Abonnent hat keinen Confirm-Code, der f&uuml;r die K&uuml;ndigung erforderlich ist." . "<br>";
					}
					$_SESSION['nl']['news_imptext1'] = str_replace('#key#', $key, $_SESSION['nl']['news_imptext1']); // Key f�r Text NL

					if (!empty($_SESSION['nl']['news_inhalt_html']) && empty($_SESSION['newsletter_body_break_ok'])) {
						$html = $_SESSION['nl']['news_template'];
						$html = preg_replace('/<br\s*\/?>(?!\s?[\r\n])\s*/', "<br />\n", $html);
						$html = preg_replace('/<br \/>\n<br \/>/', "<br /><br />", $html);
						$html = preg_replace('/<br \/>\n<br \/>/', "<br /><br />", $html);
						$html = preg_replace('/<(table|thead|tbody)>(?!\s?[\r\n])\s?/',
							"<\$1>\n", $html);
						$html = preg_replace('/<\/(h[1-6]|p|table|thead|tbody|tr|td|th)>(?!\s?[\r\n])\s?/', "</\$1>\n", $html);
						$_SESSION['newsletter_body_break_ok'] = "ok";
						if ($html) {
							$_SESSION['nl']['news_inhalt_html'] = $html;
						}
					}

					if (!empty($_SESSION['nl']['news_inhalt_html'])) {
						$_SESSION['nl']['news_imptext2'] = str_replace('#key#', $key, $_SESSION['nl']['news_imptext2']); // Key f�r HTML NL
						$_SESSION['nl']['news_template'] =
							str_replace('#Newsletter_Kuendigen#', $_SESSION['nl']['news_imptext2'], $_SESSION['nl']['news_template']); // K�nd.link
						// Im HTML Umbrüche einsetzen um Probleme mit zu langen Zeilen
						// beim Mailversand zu verhindern.

						$this->mail_it->body_html = $this->replace_vars_namen($_SESSION['nl']['news_template'], $new);
						if ($this->news_addon !== null) {
							#$this->news_addon->send_news_make_imprint($this->cms->title_send . PAPOO_WEB_PFAD, $key);
							$this->mail_it->Encoding = "quoted-printable";
							#$addon_html_mail = $_SESSION['nl']['news_template'];
							#$this->news_addon->send_news_get_html_mail();
							#if (!empty($addon_html_mail)) $this->mail_it->body_html = $addon_html_mail;
						}
					}

					$body_text = $this->replace_vars_namen($_SESSION['nl']['news_inhalt'], $new);

					$this->mail_it->CharSet = "utf-8";
					$this->mail_it->to = $email;
					$this->mail_it->from = $_SESSION['nl']['admindaten'][0]->news_email;
					if (is_array($_SESSION['nl']['attach_array'])) {
						$this->mail_it->attach = $_SESSION['nl']['attach_array'];
					}
					$this->mail_it->from_textx = $_SESSION['nl']['admindaten'][0]->news_name;
					$this->mail_it->subject = $this->replace_vars_namen($_SESSION['nl']['news_betreff'], $new);
					$this->mail_it->body = $body_text . $_SESSION['nl']['news_imptext1'];
					if (!empty($email)) {
						$this->mail_it->do_mail();
					} // Versand
					// Zur�cksetzen f�r den n�chsten key
					$_SESSION['nl']['news_template'] =
						str_replace($_SESSION['nl']['news_imptext2'], '#Newsletter_Kuendigen#', $_SESSION['nl']['news_template']);
					$_SESSION['nl']['news_imptext1'] = str_replace($key, '#key#', $_SESSION['nl']['news_imptext1']);
					$_SESSION['nl']['news_imptext2'] = str_replace($key, '#key#', $_SESSION['nl']['news_imptext2']); // HTML
					$this->store_protocol($new); // Abonneten abspeichern f�r den Nachweis/Protokoll

					$i++;
					$_SESSION['nl']['anz_gesendete'] = $i;
					$i2--;
				}
				else {
					return; // max. erreicht
				}
			}
		}
	}

	/**
	 * @param string $content
	 * @param array $adress
	 * @return mixed|string|string[]|null
	 */
	function replace_vars_namen($content = "", $adress = array())
	{

		if (!empty($adress['news_name_nach'])) {
			$content = str_ireplace("#name#", $adress['news_name_nach'], $content);

			if ($adress['news_name_gender'] != 1) {
				$content = str_ireplace("#title#", "Frau", $content);
			}
			else {
				$content = str_ireplace("#title#", "Herr", $content);
			}
		} //Kein Nachname vorhanden, dann leer und Guten Tag
		else {
			$content = str_ireplace("#title#", "", $content);
			$content = str_ireplace("#name#", "", $content);
		}

		if (is_array($adress)) {
			foreach ($adress as $k => $v) {
				$content = str_ireplace("#" . $k . "#", $v, $content);

			}
		}
		return $content;
	}

	/**
	 * @param $new
	 */
	function store_protocol($new)
	{
		if ($new['nl_user']) {
			$gruppen = explode(' ', $_SESSION['nl']['gruppe_nl']);
			$verteiler_string = '-';
			foreach($gruppen as $gruppe){
				if($gruppe > 0){
					$verteiler_string .= ' ' . $this->get_list_name($gruppe) . ' -';
				}
			}
			// Daten der NL-Abonnenten ins Protokoll
			$sql = sprintf("INSERT INTO %s
										SELECT news_user_id,
											'%d' AS news_id,
											deleted,
											'$verteiler_string' AS news_type,
											news_active,
											news_key,
											news_user_email,
											news_name_vor,
											news_name_nach,
											news_name_gender,
											news_name_str,
											news_name_plz,
											news_user_lang,
											news_name_ort,
											news_name_staat,
											news_phone,
											news_name_firma,
											news_name_mitglied,
											news_name_abonnent,
											news_signup_date,
											news_unsubscribe_date,
											news_gruppen,
											news_hardbounce,
											news_hardbounce_time
											FROM %s
											WHERE news_user_id = '%d'",
				$this->papoo_news_protocol_user,
				$this->db->escape($_SESSION['nl']['insertid']),
				$this->papoo_news_user,
				$this->db->escape($new['news_user_id'])
			);
			$this->db->query($sql);
		}
		elseif ($new['flex_user']) {
			// Daten von Flex-Abonnenten ins Protokoll
			$sql = sprintf("INSERT INTO %s
										SELECT userid as news_user_id,
											'%d' AS news_id,
											'0' as deleted,
											'Flex' AS news_type,
											active as news_active,
											confirm_code as news_key,
											email as news_user_email,
											user_vorname as news_name_vor,
											user_nachname as news_name_nach,
											user_gender as news_name_gender,
											user_strasse as news_name_str,
											user_plz as news_name_plz,
											'1' as news_user_lang,
											user_ort as news_name_ort,
											user_land as news_name_staat,
											user_telefon as news_phone,
											user_firma as news_name_firma,
											'' as news_name_mitglied,
											'' as news_name_abonnent,
											zeitstempel as news_signup_date,
											'' as news_unsubscribe_date,
											'FS' as news_gruppen,
											news_hardbounce,
											news_hardbounce_time
											FROM %s
											WHERE email = '%s'",
				$this->papoo_news_protocol_user,
				$this->db->escape($_SESSION['nl']['insertid']),
				$this->cms->tbname['papoo_user'],
				$this->db->escape($new['news_user_email'])
			);
			$this->db->query($sql);
		}
		elseif ($new['papoo_user']) {
			$str = $new['news_name_str'] . " " . $new['user_hausnummer'];
			// Daten der Papoo-User-Abonnenten ins Protokoll
			$sql = sprintf("INSERT INTO %s SET news_id = '%d',
												news_user_id = '%d',
												news_type = 'Papoo',
												news_name_firma = '%s',
												news_name_staat = '%s',
												news_name_vor = '%s',
												news_name_nach = '%s',
												news_name_str = '%d',
												news_name_ort = '%s',
												news_phone = '%s',
												news_name_plz = '%s',
												news_active = '%s',
												news_name_gender = '%s',
												news_signup_date = '%s',
												news_hardbounce = '%s',
												news_hardbounce_time = '%s',
												news_user_email = '%s'",
				$this->papoo_news_protocol_user,
				$this->db->escape($_SESSION['nl']['insertid']),
				$this->db->escape($new['news_user_id']),
				$this->db->escape($new['news_name_firma']),
				$this->db->escape($new['news_name_staat']),
				$this->db->escape($new['news_name_vor']),
				$this->db->escape($new['news_name_nach']),
				$this->db->escape($str),
				$this->db->escape($new['news_name_ort']),
				$this->db->escape($new['news_phone']),
				$this->db->escape($new['news_name_plz']),
				$this->db->escape($new['news_active']),
				$this->db->escape($new['news_name_gender']),
				$this->db->escape($new['news_signup_date']),
				$this->db->escape($new['news_hardbounce']),
				$this->db->escape($new['news_hardbounce_time']),
				$this->db->escape($new['news_user_email'])
			);
			$this->db->query($sql);
		}
	}

	/**
	 * *** VORSCHAU eines Newsletters (Template: newssend.html) ***
	 * 1. Formular wurde gesendet. Jetzt die Vorschau mit Versandgruppen aufbereiten und anzeigen.
	 *
	 * @return int
	 */
	function check_inputs_and_store_to_session_and_template()
	{
		#$this->sys_abonnenten_anzahl_to_template(1); // Sys Gruppen mit Abonnenten-Anzahl ans Template newssend.html �bergeben
		#$this->nl_abonnenten_anzahl_to_template(1); // NL Gruppen mit Abonnenten-Anzahl ans Template newssend.html �bergeben
		// Plausibilit�tspr�fungen
		// Eingabefelder m�ssen den Betreff und mindestens eine Nachricht enthalten
		// Leerzeichen oder leere Tags sind keine Nachricht, wenn keine weiteren Zeichen da sind
		IfNotSetNull($this->checked->linkname);
		IfNotSetNull($this->checked->inhalt_html);
		IfNotSetNull($this->checked->inhalt);
		$betreff_empty = $this->check_string_empty($this->checked->linkname, 32, 255, true, true);
		$html_empty = $this->check_string_empty($this->checked->inhalt_html, 32, 255, true, true);
		$text_empty = $this->check_string_empty($this->checked->inhalt, 32, 255, true, true);
		if ($betreff_empty) {
			$_SESSION['nl']['news_betreff'] = ""; // SESSION-Feld leeren
			$this->content->template['nobetreff'] = $fehler = 1; // Fehlermeldung im Template ausl�sen
		}
		$_SESSION['nl']['own_content'] = 0;
		if ($html_empty and $text_empty) {
			$this->content->template['nomessage'] = $fehler = 1; // Fehlermeldung im Template ausl�sen
		}
		if ($html_empty) {
			$_SESSION['nl']['news_inhalt_html'] = "";
		}
		if ($text_empty) {
			$_SESSION['nl']['news_inhalt'] = "";
		}
		else {
			$_SESSION['nl']['own_content'] = 1;
		}
		if (empty($this->checked->news_lang)) {
			$this->content->template['nolang'] = $fehler = 1; // Fehlermeldung im Template ausl�sen
		}
		// Text aus HTML generieren, wenn kein eigener Text und kein Fehler vorliegt
		if (!isset($fehler) || isset($fehler) && !$fehler and !$_SESSION['nl']['own_content']) {
			$umw_inhalt = $this->checked->inhalt_html;
			// HTML-Code holen und von Zeilenumbr�chen befreien
			$umw_inhalt = str_replace("\r\n", "", $umw_inhalt);
			// Array-Index entfernen, u. U. l�uft der folgende preg_replace_callback sonst nicht einwandfrei
			unset ($_SESSION['nl']['index']);
			// </a> durch die in href="... enthaltene URL ersetzen. Der callback getReplaceString ermittelt die URLs
			$umw_inhalt = preg_replace_callback('/<\/a>/i',
				array(
					&$this,
					'getReplaceString'
				),
				$umw_inhalt
			);
			// f�r bestimmte Tags einen Zeilenumbruch ausl�sen
			$search = array(
				"'<\/div>'i",
				"'<\/td>'i",
				"'<\/li>'i",
				"'<\/dd>'i",
				"'<\/dt>'i",
				"'<hr>'i",
				"'<br\/>'i",
				"'<br \/>'i",
				"'<br>'i",
				"'<\/p>'i",
				"'<\/h1>'i",
				"'<\/h2>'i",
				"'<\/h3>'i",
				"'<\/h4>'i",
				"'<\/h5>'i",
				"'<\/h6>'i"
			);
			$umw_inhalt = preg_replace($search, "\r\n", $umw_inhalt); // 1x CR und LF
			$_SESSION['nl']['own_content'] = 0;                       // 0 = kein eigener Text, Autotext ist aktiv
			// restliche Tags entfernen (jetzt auch a-tags)
			$umw_inhalt = utf8_decode(strip_tags($umw_inhalt));
			// evtl. eingegebene Entities umwandeln. Umwandlung erfolgt ohne UTF8
			$umw_inhalt = $this->HTMLEntities_to_literals($umw_inhalt);
			$umw_inhalt = utf8_encode($umw_inhalt);
			$i = 0;               // Aktuelle Position in $umw_inhalt
			$umw_inhalt_neu = ""; // Hier wird die Ausgabe erstellt
			$len_umw_inhalt = strlen($umw_inhalt);
			// Steuerzeichen und Leerstellen vor Textbeginn entfernen
			while ((($umw_inhalt[$i] == " ")
					|| ($umw_inhalt[$i] == "\r")
					|| ($umw_inhalt[$i] == "\n"))
				and ($i <= $len_umw_inhalt)) {
				$i++;
			}
			// �berfl�ssige CR/LF entfernen (max. nur eine Leerzeile reicht)
			for (; $i <= ($len_umw_inhalt - 1);) { // $umw_inhalt bis zur vollen L�nge untersuchen
				if ($umw_inhalt[$i] == "\r") { // Suche nach CR
					$umw_inhalt_neu = $umw_inhalt_neu . "\r\n";
					$i++; // Zeige auf das n�chste Zeichen nach 1. CR
					if ($umw_inhalt[$i] == "\n") {
						$i++;
					}
					if ($i == $len_umw_inhalt){
						break;
					}
					$cr = 0;
					while ((($umw_inhalt[$i] == " ")
							|| ($umw_inhalt[$i] == "\r")
							|| ($umw_inhalt[$i] == "\n"))
						and ($i <= $len_umw_inhalt)) {
						$i++;
						$cr = 1;
					}
					if ($i == $len_umw_inhalt) {
						break;
					}
					if ($cr) {
						$umw_inhalt_neu = $umw_inhalt_neu . "\r\n";
					}
				}
				$umw_inhalt_neu = $umw_inhalt_neu . $umw_inhalt[$i];
				$i++;
				if ($i == $len_umw_inhalt) {
					break;
				}
			}
			$_SESSION['nl']['news_inhalt'] = $umw_inhalt_neu;
		}
		else $_SESSION['nl']['news_inhalt'] = $this->checked->inhalt;
		$_SESSION['nl']['news_inhalt_html'] = $this->checked->inhalt_html;
		$_SESSION['nl']['news_betreff'] = $this->checked->linkname;
		$_SESSION['nl']['language_newsd'] = $this->checked->news_lang;
		//nobr:, damit \n nicht in <br /> umgewandelt werden.
		//weiterhin k�nnen \n nur in einer Textarea in Templates dargestellt werden (nicht in div etc.)
		// s. => content_class.php nobr:
		$this->content->template['inhalt'] = "nobr:" . $_SESSION['nl']['news_inhalt'];
		$this->content->template['inhalt_html'] = "nodecode:" . $this->checked->inhalt_html;
		$this->content->template['betreff'] = "nodecode:" . $this->checked->linkname;
		$this->make_lang($_SESSION['nl']['language_newsd']);
		IfNotSetNull($fehler);
		return $fehler;
	}

	/**
	 * @param int $session_data_requested
	 */
	function show_form($session_data_requested = 0)
	{
		// Fehler im Form gefunden bei der Erstellung/�nderung eines NL. Dann ist $fehler=1.
		// Oder beim 1. Aufruf zur Erstellung eines neuen NL. Indiz: Keinerlei Parameter !
		// Oder beim Aufruf zur Anzeige/�nderung/Neuversand eines alten NL. Dann ist $this->checked->news_id gesetzt.
		if (isset($this->checked->news_id) && $this->checked->news_id AND
			$this->checked->template != "newsletter/templates/news_nl_new.html" AND !$session_data_requested
		) {
			$old = $this->get_old_news($this->checked->news_id);                               // Hole diesen NL
			$lang = $old[0]->news_inhalt_lang ? $old[0]->news_inhalt_lang : 1; //default wenn 0 / "" etc.
			$this->make_lang($lang); // Sprachen ins Template zur Auswahl inkl. selected lang
			$this->content->template['uberschrift'] = "nodecode:" . $old[0]->news_header;      // Betreff
			$this->content->template['inhalt'] = "nobr:" . $old[0]->news_inhalt;               // Text-NL
			$this->content->template['inhalt_html'] = "nodecode:" . $old[0]->news_inhalt_html; // HTML-NL
		}
		#else $this->make_lang($_SESSION['nl']['language_newsd']); // Sprachen ins Template zur Auswahl inkl. selected lang
		// Schalter setzen aus Konfig. f�r Templates
		$this->content->template['news_html'] = $_SESSION['nl']['admindaten'][0]->news_html;
		$this->content->template['wyswig'] = $_SESSION['nl']['admindaten'][0]->wyswig;
		if ($session_data_requested) { // Bei Fehler im Form
			#if ($this->checked->submit != $this->content->template['message_20021a']) $this->content->template['inhalt'] = "nobr:".$_SESSION['nl']['news_inhalt'];
			#elseif (!$_SESSION['nl']['own_content']) $this->content->template['inhalt'] = ""; // wurde automat. erzeugt
			#else $this->content->template['inhalt'] = "nobr:".$_SESSION['nl']['news_inhalt']; // ist eigener Inhalt
			$this->content->template['inhalt'] = "nobr:" . $_SESSION['nl']['news_inhalt']; // ist eigener Inhalt
			$this->content->template['inhalt_html'] = "nodecode:" . $_SESSION['nl']['news_inhalt_html'];
			$this->content->template['uberschrift'] = $_SESSION['nl']['news_betreff'];
		}
		else { // Kein Fehler. Artikeldaten ans Template
			if (isset($this->checked->reporeid) AND !isset($this->checked->news_id)) {
				$this->get_article_content();
				$this->check_inputs_and_store_to_session_and_template();
				$this->content->template['uberschrift'] = $_SESSION['nl']['news_betreff'];
			}
		}
	}

	/**
	 * @param string $subject
	 * @param int $startvalue
	 * @param int $endvalue
	 * @param bool $tags
	 * @param bool $entities
	 * @return bool|int
	 */
	function check_string_empty($subject = "", $startvalue = 0, $endvalue = 0, $tags = false, $entities = false)
	{
		// UTF8 to ASCII
		$subject_decoded = utf8_decode($subject);
		// Umwandlung der HTML-Entities
		if ($entities) {
			$subject_decoded = $this->HTMLEntities_to_literals($subject_decoded);
		}
		// HTML-Tags raus
		if ($tags) {
			$subject_decoded = strip_tags($subject_decoded);
		}
		// Sind nur noch Leerstellen (32/160/$20/$A0) im Text �brig?
		$leerzeichen_1 = str_replace(chr(32), "", $subject_decoded);
		// Test ohne Leerzeichen 32
		(strlen(str_replace(chr(160), "", $leerzeichen_1))) ? $leer = 0 : $leer = 1;
		if ($leer) {
			return true;
		}
		// Sind nur Steuerzeichen im Text vorhanden?
		// Hierzu alle druckbaren Zeichen entfernen, Rest nicht druckbar
		$len_before = strlen($subject_decoded);
		if ($startvalue and $endvalue) {
			$i = $startvalue;
			for (; $i <= $endvalue;) {
				$subject_decoded = str_replace(chr($i), "", $subject_decoded);
				$i++;
			}
			if ($len_before == strlen($subject_decoded)) {
				$leer = 1;
			}
			//(strlen($subject_decoded)) ? $leer=1 : $leer=0;
		}
		return $leer;
	}

	/**
	 * convert html entities to literals
	 *
	 * @param $umw_inhalt
	 * @return string|string[]|null
	 */
	function HTMLEntities_to_literals($umw_inhalt)
	{
		$search = array(
			"'&(quot|#34);'i",
			"'&(amp|#38);'i",
			"'&(lt|#60);'i",
			"'&(gt|#62);'i",
			"'&(nbsp|#160);'i",
			"'&(iexcl|#161);'i",
			"'&(cent|#162);'i",
			"'&(pound|#163);'i",
			"'&(curren|#164);'i",
			"'&(yen|#165);'i",
			"'&(brvbar|#166);'i",
			"'&(sect|#167);'i",
			"'&(uml|#168);'i",
			"'&(copy|#169);'i",
			"'&(ordf|#170);'i",
			"'&(laquo|#171);'i",
			"'&(not|#172);'i",
			"'&(shy|#173);'i",
			"'&(reg|#174);'i",
			"'&(macr|#175);'i",
			"'&(neg|#176);'i",
			"'&(plusmn|#177);'i",
			"'&(sup2|#178);'i",
			"'&(sup3|#179);'i",
			"'&(acute|#180);'i",
			"'&(micro|#181);'i",
			"'&(para|#182);'i",
			"'&(middot|#183);'i",
			"'&(cedil|#184);'i",
			"'&(supl|#185);'i",
			"'&(ordm|#186);'i",
			"'&(raquo|#187);'i",
			"'&(frac14|#188);'i",
			"'&(frac12|#189);'i",
			"'&(frac34|#190);'i",
			"'&(iquest|#191);'i",
			"'&(Agrave|#192);'",
			"'&(Aacute|#193);'",
			"'&(Acirc|#194);'",
			"'&(Atilde|#195);'",
			"'&(Auml|#196);'",
			"'&(Aring|#197);'",
			"'&(AElig|#198);'",
			"'&(Ccedil|#199);'",
			"'&(Egrave|#200);'",
			"'&(Eacute|#201);'",
			"'&(Ecirc|#202);'",
			"'&(Euml|#203);'",
			"'&(Igrave|#204);'",
			"'&(Iacute|#205);'",
			"'&(Icirc|#206);'",
			"'&(Iuml|#207);'",
			"'&(ETH|#208);'",
			"'&(Ntilde|#209);'",
			"'&(Ograve|#210);'",
			"'&(Oacute|#211);'",
			"'&(Ocirc|#212);'",
			"'&(Otilde|#213);'",
			"'&(Ouml|#214);'",
			"'&(times|#215);'i",
			"'&(Oslash|#216);'",
			"'&(Ugrave|#217);'",
			"'&(Uacute|#218);'",
			"'&(Ucirc|#219);'",
			"'&(Uuml|#220);'",
			"'&(Yacute|#221);'",
			"'&(THORN|#222);'",
			"'&(szlig|#223);'",
			"'&(agrave|#224);'",
			"'&(aacute|#225);'",
			"'&(acirc|#226);'",
			"'&(atilde|#227);'",
			"'&(auml|#228);'",
			"'&(aring|#229);'",
			"'&(aelig|#230);'",
			"'&(ccedil|#231);'",
			"'&(egrave|#232);'",
			"'&(eacute|#233);'",
			"'&(ecirc|#234);'",
			"'&(euml|#235);'",
			"'&(igrave|#236);'",
			"'&(iacute|#237);'",
			"'&(icirc|#238);'",
			"'&(iuml|#239);'",
			"'&(eth|#240);'",
			"'&(ntilde|#241);'",
			"'&(ograve|#242);'",
			"'&(oacute|#243);'",
			"'&(ocirc|#244);'",
			"'&(otilde|#245);'",
			"'&(ouml|#246);'",
			"'&(divide|#247);'i",
			"'&(oslash|#248);'",
			"'&(ugrave|#249);'",
			"'&(uacute|#250);'",
			"'&(ucirc|#251);'",
			"'&(uuml|#252);'",
			"'&(yacute|#253);'",
			"'&(thorn|#254);'",
			"'&(yuml|#255);'"
		);
		$replace = array(
			"\"",
			"&",
			"<",
			">",
			" ",
			chr(161),
			chr(162),
			chr(163),
			chr(164),
			chr(165),
			chr(166),
			chr(167),
			chr(168),
			chr(169),
			chr(170),
			chr(171),
			chr(172),
			chr(173),
			chr(174),
			chr(175),
			chr(176),
			chr(177),
			chr(178),
			chr(179),
			chr(180),
			chr(181),
			chr(182),
			chr(183),
			chr(184),
			chr(185),
			chr(186),
			chr(187),
			chr(188),
			chr(189),
			chr(190),
			chr(191),
			chr(192),
			chr(193),
			chr(194),
			chr(195),
			chr(196),
			chr(197),
			chr(198),
			chr(199),
			chr(200),
			chr(201),
			chr(202),
			chr(203),
			chr(204),
			chr(205),
			chr(206),
			chr(207),
			chr(208),
			chr(209),
			chr(210),
			chr(211),
			chr(212),
			chr(213),
			chr(214),
			chr(215),
			chr(216),
			chr(217),
			chr(218),
			chr(219),
			chr(220),
			chr(221),
			chr(222),
			chr(223),
			chr(224),
			chr(225),
			chr(226),
			chr(227),
			chr(228),
			chr(229),
			chr(230),
			chr(231),
			chr(232),
			chr(233),
			chr(234),
			chr(235),
			chr(236),
			chr(237),
			chr(238),
			chr(239),
			chr(240),
			chr(241),
			chr(242),
			chr(243),
			chr(244),
			chr(245),
			chr(246),
			chr(247),
			chr(248),
			chr(249),
			chr(250),
			chr(251),
			chr(252),
			chr(253),
			chr(254),
			chr(255)
		);
		return $umw_inhalt = preg_replace($search, $replace, $umw_inhalt);
	}

	/**
	 * news::make_extra_functions()
	 * Hier werden einige Extrag Funktionen gestartet
	 * wie z.B. Dubletten l�schen oder inaktive l�schen
	 * @return void
	 */
	function make_extra_functions()
	{
		if (isset($this->checked->ndel) && $this->checked->ndel == "dublett") {
			$this->news_user_del_dubletten();
		}
		//Inaktive User l�schen
		if (isset($this->checked->ndel) && $this->checked->ndel == "inaktiv") {
			$this->news_user_del_inaktive();
		}
		//csv export für suche
		if(isset($this->checked->nexp) && $this->checked->nexp == "do_it") {
			$this->make_csv_export_for_search();
		}
	}

	/**
	 * @param $csv
	 * @param $list
	 */
	function make_csv_head(&$csv, $list)
	{
		$trenner = isset($this->checked->trenner) && $this->checked->trenner == "tab" ? "\t" : ";";

		foreach ($list as $item) {
			foreach ($item as $key => $value) {
				$csv.= $key.$trenner;
			}
			break;
		}
		$csv.= "\n";
	}

	function make_csv_export_for_search()
	{
		$export_array = $_SESSION['csv_export_newsletter_search'];

		$csv = '';

		$this->make_csv_head($csv, $export_array);

		$trenner = isset($this->checked->trenner) && $this->checked->trenner == "tab" ? "\t" : ";";

		foreach ($export_array as $item) {
			foreach ($item as $key => $value) {
				$value = str_replace(array("\r\n", "\r", "\n"), '', $value);
				$value = str_replace(";", "", $value);
				$csv.= $value.$trenner;
			}
			$csv.= "\n";
		}

		if (!empty($csv)) {
			// Dateinamen
			$file = "/templates_c/newsletter_export.csv";
			// Datei erzeugen
			$this->diverse->write_to_file($file, $csv);

			$this->content->template['file'] = $file;
			// Link
			$this->content->template['self'] = $_SERVER['PHP_SELF']
				. "?menuid= "
				. $this->checked->menuid
				. "&template= "
				. $this->checked->template
				. "";
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="export.csv"');
			// Send Content-Transfer-Encoding HTTP header
			// (use binary to prevent files from being encoded/messed up during transfer)
			header('Content-Transfer-Encoding: binary');
			echo $csv;
			exit;
		}
	}

	/**
	 * news::news_user_del_dubletten()
	 *
	 * Hier werden die doppelten Eintr�ge aus der Newsuser Tabelle gel�scht
	 * Anmerkung: Jedwede Speicherung verhindert Dubletten, zus�tzlich ist die E-Mailadresse als unique key definiert. Dubletten sind also nicht m�glich.
	 *
	 * @return void
	 */
	function news_user_del_dubletten()
	{
		if ($_SESSION['nl']['admindaten'][0]->news_allow_delete) {
			$sql = sprintf("SELECT * FROM %s WHERE deleted = 0",
				$this->cms->tbname['papoo_news_user']
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			$duplicate_array = $mail_adresses = array();
			for ($i = 0; $i <= count($result) - 1; $i++) {
				$result[$i]['news_user_email'] = trim($result[$i]['news_user_email']);
				// keine Dublette, email hinnzuf�gen
				if (!in_array($result[$i]['news_user_email'], $mail_adresses)) $mail_adresses[] = $result[$i]['news_user_email'];
				else $duplicate_array[] = $result[$i]; // Dublette, alles speichern
			}
			// gefundene Dubletten l�schen
			if (count($duplicate_array)) {
				foreach ($duplicate_array as $key => $value) {
					$sql = sprintf("DELETE FROM %s WHERE news_user_id = '%d'",
						$this->cms->tbname['papoo_news_user'],
						$this->db->escape($value['news_user_id'])
					);
					$this->db->query($sql);
				}
			}
			//L�schen wurde durchgef�hrt
			$this->content->template['newsletter_dubletten_geloescht'] = "OK";
		}
	}

	/**
	 * news::news_user_del_inaktive()
	 * Hier werden die inaktiven User gel�scht
	 * @return void
	 */
	function news_user_del_inaktive()
	{
		// law and order: nur die l�schen, die nicht als gel�scht gekennzeichnet sind, wenns die Konfig. erlaubt
		if ($_SESSION['nl']['admindaten'][0]->news_allow_delete) {
			$sql = sprintf("DELETE FROM %s
									WHERE news_active = '0'",
				$this->cms->tbname['papoo_news_user']
			);
		}
		else {
			// als gel�scht kennzeichnen
			$sql = sprintf("UPDATE %s SET deleted = '1',
										news_unsubscribe_date = '%s'
										WHERE news_active = '0'",
				$this->cms->tbname['papoo_news_user'],
				date("d.m.Y - H:i:s")
			);
		}
		$this->db->query($sql);
		//L�schen wurde durchgef�hrt
		$this->content->template['newsletter_inaktive_geloescht'] = "OK";
	}

	/**
	 * @param $impressum
	 * @param $impressum_html
	 * @return bool|false|mixed|string|void
	 */
	function send_news_make_html_mail($impressum, $impressum_html)
	{
		$html_mail = '';
		$template_file = PAPOO_ABS_PFAD . '/plugins/newsletter/templates/addon_newsletter.template.utf8.html';

		if (file_exists($template_file)) {
			require_once(PAPOO_ABS_PFAD . "/lib/smarty/Smarty.class.php");
			$smarty_newsletter = new Smarty;
			$smarty_newsletter->assign('betreff', $_SESSION['nl']['news_betreff']);
			$smarty_newsletter->assign('content', $_SESSION['nl']['news_inhalt_html']);

			$url = '';

			if (!empty($this->cms->title_send)) {
				$url = $this->cms->title_send . PAPOO_WEB_PFAD;
				if (!stristr($url, 'http:') || !stristr($url, 'https:')) {
					$url = $this->protocol . $url;
				}
			}
			$smarty_newsletter->assign('url', $url);

			$tmp_impressum = nl2br($impressum);

			if (!empty($impressum_html)) {
				$tmp_impressum = $impressum_html;
			}

			$smarty_newsletter->assign('impressum', $tmp_impressum);
			$html_mail = $smarty_newsletter->fetch($template_file);
		}
		#$this->_html_mail = $html_mail;
		return $html_mail;
	}

	/**
	 * @return array
	 */
	function lese_csv()
	{
		// Datei einlesen
		$daten = $this->get_csv_content(); // alle CSV S�tze nach $_SESSION['nl_import']['import_daten']
		// Erste Zeile einlesen
		$felder = $daten['0']; // nur die 1. Zeile
		// Inhalte in ein array einlesen
		$feld_ar = explode("\t", $felder);
		return $feld_ar;
	}

	/**
	 * @return array
	 */
	function get_csv_content()
	{
		$handle = fopen(PAPOO_ABS_PFAD . "/dokumente/upload/" . basename($_SESSION['nl_import']['uploaded_file']), "r");
		$contents = fread($handle, filesize(PAPOO_ABS_PFAD . "/dokumente/upload/" . basename($_SESSION['nl_import']['uploaded_file'])));
		fclose($handle);
		// Zeilenumbr�che codieren
		$contents = preg_replace('/\r\n/', "###u###", $contents);
		$contents = preg_replace('/\n/', "###n###", $contents);
		$contents = preg_replace('/\r/', "###r###", $contents);
		if (stristr($contents, "###u###")) {
			$contents_a = explode("###u###", $contents);
		}
		else {
			$contents_a = explode("###n###", $contents);
		}
		$daten = array();
		if (!empty($contents_a))
			foreach ($contents_a as $zeile) {
				if (empty($zeile)) {
					continue; // leere Zeilen �berlesen
				}
				$daten[] = $zeile;
			};
		$_SESSION['nl_import']['import_daten'] = $daten;
		$_SESSION['nl_import']['import_anz_zeilen'] = count($daten);
		if (!$daten[$_SESSION['nl_import']['import_anz_zeilen']]) {
			$_SESSION['nl_import']['import_anz_zeilen'] = $_SESSION['nl_import']['import_anz_zeilen'] - 1;
		}
		if ($_SESSION['nl_import']['import_anz_zeilen'] < 0) {
			$_SESSION['nl_import']['import_anz_zeilen'] = 0;
		}
		return $daten;
	}

	/**
	 * @return array
	 */
	function get_csv_content_rows()
	{
		$counter = $this->checked->counter_start;
		$war_das_ende = false;
		$daten = array();
		$counter_start_plus_max = $this->checked->counter_start + $this->max_counter_pro_runde;
		// jetzt die n�chsten $this->max_counter_pro_runde Zeilen einlesen
		while ($counter < $counter_start_plus_max && !$war_das_ende) {
			if (!empty($_SESSION['nl_import']['import_daten'][0])) {
				$daten[] = $this->record = array_shift($_SESSION['nl_import']['import_daten']); // 1 Satz holen aus Session
				$rc = $this->check_record($counter); // Plausibilit�tspr�fung
				if (count($this->fehler_infos) OR $rc) {
					if ($rc != "skip" AND count($this->fehler_infos)) { // 1. Satz mit Feldernamen erhalten, aber leere S�tze (rc = "pop_only") entfernen
						array_pop($daten); // S�tze mit Fehler(n) auch entfernen
						$_SESSION['nl_import']['records_in_error']++;
					}
					if (count($this->fehler_infos) AND $rc != "pop_only") { // bei Fehler und nicht leerem Satz
						$this->error_report($counter); // Fehlerprotokoll
					}
				}
			}
			else $war_das_ende = true;
			$counter++;
		}

		$_SESSION['nl_import']['success_count'] = $_SESSION['nl_import']['import_anz_zeilen'] - $_SESSION['nl_import']['records_in_error'];
		$_SESSION['nl_import']['success_count'] = $_SESSION['nl_import']['success_count'] < 0 ? 0 : $_SESSION['nl_import']['success_count'];
		$this->error_report("sum"); // Anzahl der S�tze ans Fehlerprotokoll
		// wenn das Dateiende erreicht wurde, dann Flag setzen
		if ($counter < $this->max_counter_pro_runde || $war_das_ende) {
			$this->ende_der_datei = "ja";
		}
		if ($this->checked->counter_start) {
			unset($daten[0]);  //duplicate raus
		}
		// Daten zur�ckgeben
		return $daten;
	}

	/**
	 * @param string $dat
	 * @return array
	 */
	function convert_to_array($dat = "")
	{
		$retdaten = array();
		$k = 0;
		// Wenn Eintr�ge vorhanden
		if (!empty($dat)) {
			// Alle Eintr�ge durchgehen
			foreach ($dat as $key => $value) {
				// Wenn erste Zeile Datennamen sind, diese �berspringen
				if ($this->counter_start == "0") {
					$k++;
					if ($k < 2) continue;
				}
				// Inhalte in ein array einlesen
				$feld_ar = explode("\t", $value);
				$i = 1;
				$co = count($feld_ar);
				// Array durchgehen und Kodierung wieder umschalten
				foreach ($feld_ar as $feld) {
					// Zeilenumbr�che und sonstigen M�ll entfernen
					$feld = trim($feld);
					// Wenn sinnvoller Inhalt, dann �bergeben
					if ($i <= $co) {
						// Inhalt k�rzen
						if (strlen($feld) > 2500) $feld = substr($feld, 0, 2500) . "...";
						$feld_ar2[$i] = $feld;
						$i++;
					}
				}
				IfNotSetNull($feld_ar2);
				$retdaten[$key] = $feld_ar2;
			}
		}
		return $retdaten;
	}

	/**
	 * @param int $counter
	 * @return int|string
	 */
	function check_record($counter = 0)
	{
		$this->fehler_infos = array();
		if ($counter == 0) {
			$rc = "skip"; // Der 1. Satz mit Tabellennamen wird nicht gebraucht
		}
		else {
			$rc = "";
			if (!empty($this->record)) {
				$this->field_arr = explode("\t", $this->record); // S�tze sind mit HT separiert
				$mail_pos = $this->mail - 1;
				$erg = preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9_-]+(\.[_a-z0-9-]+)+$/i', $this->field_arr[$mail_pos]);
				if (!$erg) {
					$rc = 0x10;
					$_SESSION['nl_import']['error_count']++;
				}
				if (strlen($this->field_arr[$mail_pos]) > 255) {
					$rc = $rc | 0x200; // zu viele Zeichen
					$_SESSION['nl_import']['error_count']++;
				}
				if ($rc) { // aufgetretenen Fehler dem array zuweisen
					$this->fehler_infos[$counter][$mail_pos]['csv_pointer'] = $mail_pos + 1; // Korrektur + 1 f�r die Feld-Pos.-Z�hlweise ab 1 (kommt in die DB)
					$this->fehler_infos[$counter][$mail_pos]['fehler'] = $rc; // Fehlercode
					$rc = "";
				}
			}
			else {
				$rc = "pop_only";
			} // leerer Satz: ausklinken
		}
		return $rc;
	}

	/**
	 * Pr�fen, ob der angemeldete User zur Gruppe Admin geh�rt
	 *
	 * @return bool
	 */
	function check_admin()
	{
		$sql = sprintf("SELECT *
						FROM %s
						WHERE userid = '%d' AND gruppenid = 1",

			$this->cms->tbname['papoo_lookup_ug'],

			$this->user->userid
		);
		return count($this->db->get_results($sql, ARRAY_A)) ? true : false;
	}

	function news_error_report()
	{
		$this->content->template['is_admin'] = $this->check_admin();
		if ($this->checked->report_del_id AND $this->content->template['is_admin']) {
			$sql = sprintf("DELETE FROM %s WHERE report_id = '%d'",
				$this->cms->tbname['papoo_news_error_report'],
				$this->db->escape($this->checked->report_del_id)
			);
			$this->db->query($sql);
			$sql = sprintf("DELETE FROM %s WHERE report_id = '%d'",
				$this->cms->tbname['papoo_news_error_report_details'],
				$this->db->escape($this->checked->report_del_id)
			);
			$this->db->query($sql);
			$this->content->template['report_deleted'] = 1;
		}
		// Fehlerprotokoll holen
		$sql = sprintf("SELECT * FROM %s ORDER BY import_time DESC",
			$this->cms->tbname['papoo_news_error_report']
		);
		$results = $this->db->get_results($sql, ARRAY_A);
		if (count($results)) {
			// Datum import_time umformatieren
			foreach ($results AS $key => $field) {
				foreach ($field AS $fieldname => $value) {
					if ($fieldname == "import_time") {
						$date = new DateTime($value);
						$results[$key]['import_time'] = $date->format('d.m.Y H:i:s');
					}
				}
			}
			$this->content->template['error_report'] = $results; // Fehlerprotokoll-�bersicht ans Template
		}
	}

	function news_error_report_details()
	{
		if ($this->checked->report_id) { // Details anzeigen
			$sql = sprintf("SELECT count(report_id) FROM %s WHERE report_id = '%d'",
				$this->cms->tbname['papoo_news_error_report_details'],
				$this->db->escape($this->checked->report_id)
			);
			$this->weiter->result_anzahl = $this->db->get_var($sql);
			$this->weiter->make_limit(20);
			// Fehlerprotokoll holen
			$sql = sprintf("SELECT * FROM %s
										WHERE report_id = '%d'
										ORDER BY import_file_record_no, import_file_field_position
										%s",
				$this->cms->tbname['papoo_news_error_report_details'],
				$this->db->escape($this->checked->report_id),
				$this->weiter->sqllimit
			);
			$results = $this->db->get_results($sql, ARRAY_A);
			if (($this->checked->page - 1) > 0) {
				$i = (($this->checked->page - 1) * $this->weiter->pagesize) + 1;
			}
			else {
				$i = 1;
			}
			if (count($results)) {
				foreach ($results AS $field_key => $elements) {
					$msg = array();
					$fehler = $elements['completion_code'];
					if ($fehler & 0x400) $msg[] = $this->content->template['plugin']['newsletter']['email_schon_da']; // Uhrzeit
					if ($fehler & 0x200) $msg[] = $this->content->template['plugin']['newsletter']['max255_4']; // zu viele Zeichen
					if ($fehler & 0x10) $msg[] = $this->content->template['plugin']['newsletter']['email_error']; // mail format
					if ($fehler > $_SESSION['nl_import']['highest_cc']) $_SESSION['nl_import']['highest_cc'] = $fehler;
					foreach ($msg AS $key => $err_msg) {
						$new_results[$field_key][$key] = $elements;
						$new_results[$field_key][$key]['import_error_msg'] = $err_msg;
						$new_results[$field_key][$key]['completion_code'] = strtoupper(dechex($results[$field_key]['completion_code']));
						if ((floor(strlen($new_results[$field_key][$key]['completion_code']) / 2) * 2) !=
							strlen($new_results[$field_key][$key]['completion_code'])) {
							$new_results[$field_key][$key]['completion_code'] = "0" . $new_results[$field_key][$key]['completion_code'];
						}
						$new_results[$field_key][$key]['completion_code'] = "0x" . $new_results[$field_key][$key]['completion_code'];

						// zur Excel-Orientierung: Spaltenbezeichnungen, wenn mit Semikolon separiert. F�r TAB separierte Dateien unn�tig.
						$char = $results[$field_key]['import_file_field_position'] - 1; // 0 - x
						if (($char + 1) <= 26) {
							$new_results[$field_key][$key]['excel'] = chr(($char + 1) + 64);
						}
						else {
							$new_results[$field_key][$key]['excel'] =
								chr(floor((($char + 1) - 27) / 26) + 65) . chr(($char + 1) - (floor((($char + 1) - 27) / 26) + 1) * 26 + 64);
						}
						$new_results[$field_key][$key]['error_no'] = $i; // Lfd. Nummer
						$i++;
					}
				}
			}
			$sql = sprintf("SELECT error_count FROM %s WHERE report_id = '%d'",
				$this->cms->tbname['papoo_news_error_report'],
				$this->db->escape($this->checked->report_id)
			);
			$this->content->template['error_count'] = $this->db->get_var($sql);
			IfNotSetNull($new_results);
			$this->content->template['error_report_details'] = $new_results;
			$this->weiter->weiter_link = "./plugin.php?menuid="
				. $this->checked->menuid
				. "&template=newsletter/templates/news_error_report_details.html&report_id="
				. $this->checked->report_id;
			$this->weiter->do_weiter("teaser");

			$sql = sprintf("SELECT * FROM %s
										WHERE report_id = '%d'",

				$this->cms->tbname['papoo_news_error_report'],

				$this->db->escape($this->checked->report_id)
			);
			$results = $this->db->get_results($sql, ARRAY_A);
			$this->content->template['report_id'] = $results[0]['report_id'];
			$date = new DateTime($results[0]['import_time']);
			$this->content->template['import_time'] = $date->format('d.m.Y H:i:s');
		}
		if ($this->checked->report_export_id) {
			// Fehlerprotokoll holen
			$sql = sprintf("SELECT * FROM %s
										WHERE report_id = '%d'
										ORDER BY import_file_record_no, import_file_field_position",

				$this->cms->tbname['papoo_news_error_report_details'],

				$this->db->escape($this->checked->report_export_id)
			);
			$results = $this->db->get_results($sql, ARRAY_A);
			if (count($results)) {
				$nl_export_data = "NAME\tVORNAME\tSTRASSE\tPLZ\tORT\tMAIL\tSatz-Nr.\tFeld-Nr.\tFehlermeldung\r\n";
				foreach ($results AS $key => $value) {
					$fehler = $results[$key]['completion_code'];
					if ($fehler & 0x400) {
						$results[$key]['err_msg_text'] = $this->content->template['plugin']['newsletter']['email_schon_da']; // Uhrzeit
					}
					if ($fehler & 0x200) {
						$results[$key]['err_msg_text'] = $this->content->template['plugin']['newsletter']['max255_4']; // zu viele Zeichen
					}
					if ($fehler & 0x10) {
						$results[$key]['err_msg_text'] = $this->content->template['plugin']['newsletter']['email_error']; // mail format
					}

					$nl_export_data .= $results[$key]['NAME'] . "\t";
					$nl_export_data .= $results[$key]['VORNAME'] . "\t";
					$nl_export_data .= $results[$key]['STRASSE'] . "\t";
					$nl_export_data .= $results[$key]['PLZ'] . "\t";
					$nl_export_data .= $results[$key]['ORT'] . "\t";
					$nl_export_data .= $results[$key]['MAIL'] . "\t";
					$nl_export_data .= $results[$key]['import_file_record_no'] . "\t";
					$nl_export_data .= $results[$key]['import_file_field_position'] . "\t";
					$nl_export_data .= $results[$key]['err_msg_text'] . "\r\n";
				}
				// Pfad ans Template
				IfNotSetNull($file);
				$this->content->template['file'] = $file;
				header('Content-Disposition: attachment; filename="nl_error_report.csv"');
				// Send Content-Transfer-Encoding HTTP header
				// (use binary to prevent files from being encoded/messed up during transfer)
				header('Content-Transfer-Encoding: binary');
				echo $nl_export_data;
				exit;
			}
		}
	}

	/**
	 * @param int $counter
	 */
	function error_report($counter = 0)
	{
		// Am Ende des Imports die Z�hler speichern /Fehler/erfolgreich und gesamt)
		if ($counter == "sum") {
			$sql = sprintf("UPDATE %s SET error_count = '%d',
											success_count = '%d',
											highest_cc = '%d',
											records_to_import = '%d'
											WHERE report_id = '%d'",

				$this->cms->tbname['papoo_news_error_report'],

				$this->db->escape($_SESSION['nl_import']['error_count']),
				$this->db->escape($_SESSION['nl_import']['success_count']),
				$this->db->escape($_SESSION['nl_import']['highest_cc']),
				$this->db->escape($_SESSION['nl_import']['import_anz_zeilen']),
				$this->db->escape($_SESSION['nl_import']['report_id'])
			);
			$this->db->query($sql);
		}
		else {
			// Einer der Imports�tze wurde auf Plausibilit�t gepr�ft und es sind ein oder mehrere Fehler aufgetreten
			// die im laufenden Importsatz aufgetretenen Fehler in einem einzigen DB-Satz speichern
			foreach ($this->fehler_infos AS $record_no => $field_no) {
				$rec = $record_no + 1;
				// Alle EingabeDaten im Fehlerprotokoll speichern, damit daraus sp�ter eine CSV erstellt werden kann
				foreach ($_SESSION['nl_import']['first_line'] AS $element_key => $element_value) {
					$rec_arr[$element_value] = utf8_encode($this->field_arr[$element_key]); // Feldname als key erh�lt Daten
				}
				IfNotSetNull($rec_arr);
				foreach ($field_no AS $field_key => $elements) {
					$fieldname_pointer = $elements['csv_pointer'] - 1;
					$sql = sprintf("INSERT INTO %s SET report_id = '%d',
															import_file_record_no = '%d',
															import_file_field_position = '%d',
															import_file_field_name = '%s',
															completion_code = '%d',
															NAME = '%s',
															VORNAME = '%s',
															STRASSE ='%s',
															PLZ = '%s',
															ORT = '%s',
															MAIL = '%s'",
						$this->cms->tbname['papoo_news_error_report_details'],
						$this->db->escape($_SESSION['nl_import']['report_id']),
						$this->db->escape($rec),
						$this->db->escape($field_key + 1),
						$this->db->escape($_SESSION['nl_import']['first_line'][$fieldname_pointer]),
						$this->db->escape($elements['fehler']),
						$this->db->escape($rec_arr['NAME']),
						$this->db->escape($rec_arr['VORNAME']),
						$this->db->escape($rec_arr['STRASSE']),
						$this->db->escape($rec_arr['PLZ']),
						$this->db->escape($rec_arr['ORT']),
						$this->db->escape($rec_arr['MAIL'])
					);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * @return array|null
	 */
	function get_moderated()
	{
		$sql = sprintf("SELECT news_gruppe_id FROM %s WHERE news_grpmoderated = '%d'",
			$this->cms->tbname['papoo_news_gruppen'],
			1
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		return $result;
	}

	/**
	 * @param $id
	 * @return array|null
	 */
	function get_list_name($id)
	{
		$sql = sprintf("SELECT news_gruppe_name FROM %s WHERE news_gruppe_id = '%d'",
			$this->cms->tbname['papoo_news_gruppen'],
			$id
		);
		$result = $this->db->get_var($sql);
		return $result;
	}

	/**
	 * @param array $cvs_ar
	 */
	function insert_into_database($cvs_ar = array())
	{
		if ($this->checked->mailing_list) {
			$_SESSION['nl_import']['mailing_list'] = $this->checked->mailing_list;
		}
		if($this->checked->makeimport_leeren){
			foreach ($_SESSION['nl_import']['mailing_list'] as $id) {
				$this->group_del_entry($id);
			}
		}
		// Return initialisieren
		// Wenn Einträge vorhanden sind
		$nlgruppe = '';
		if (!empty($cvs_ar)) {
			if ($_SESSION['nl_import']['mailing_list']) {
				foreach ($_SESSION['nl_import']['mailing_list'] as $id) {
					$nlgruppe .= $id . " ";
				}
			}
			else {
				$sql = sprintf("SELECT news_gruppe_id FROM %s WHERE news_gruppe_name != 'Test'",
					$this->cms->tbname['papoo_news_gruppen']
				);
				$result = $this->db->get_results($sql, ARRAY_A);
				foreach ($result AS $key => $value) {
					$nlgruppe .= $value['news_gruppe_id'] . " ";
				}
			}
			foreach ($cvs_ar as $key => $value) {
				if ($value[$this->ort] == "Herr" || $value[$this->strasse] == "Herr" || $value[$this->plz] == "Herr") {
					$news_name_gender = 1;
				} else {
					$news_name_gender = 2;
				}
				#$news_name_gender="";
				if (!empty($this->checked->blacklist)) {
					$sql = sprintf("DELETE FROM %s WHERE news_user_email = '%s'",
						$this->cms->tbname['papoo_news_user'],
						$this->db->escape($value[$this->mail])
					);
					$this->db->query($sql);
				}
				else {
					//Individuellen KEy setzen
					//FIXME: md5....
					$key = md5($value[$this->mail] . microtime());

					$sql = sprintf("SELECT * FROM %s
								  WHERE news_user_email = '%s'
								  AND deleted = 0",
						$this->cms->tbname['papoo_news_user'],
						$this->db->escape($value[$this->mail])
					);
					$results = $this->db->get_results($sql, ARRAY_A);

					if (!empty($results)) {
						$gruppe = '';
						foreach ($results as $res) {
							$gruppe .= $res['news_gruppen'];
						}
						$gruppen = explode(' ', $gruppe . $nlgruppe);
						$gruppen = implode(' ', array_unique($gruppen));


						$sql = sprintf("UPDATE %s SET news_name_nach = '%s',
												news_name_vor = '%s',
												news_name_str = '%s',
												news_name_plz = '%d',
												news_name_ort = '%s',
												news_name_gender = '%s',
												news_name_staat = '%s',
												news_gruppen = '%s'
												WHERE
												news_user_email = '%s'
												AND deleted = 0",

							$this->cms->tbname['papoo_news_user'],

							$this->db->escape($value[$this->name]),
							$this->db->escape($value[$this->vorname]),
							$this->db->escape($value[$this->strasse]),
							$this->db->escape($value[$this->plz]),
							$this->db->escape($value[$this->ort]),
							$news_name_gender,
							'Deutschland',
							$gruppen,
							$this->db->escape($value[$this->mail])
						);

						// Zeilenumbr�che wieder rein
						$sql = str_replace('###n###', "\n", $sql);
						$sql = str_replace('###r###', "\r", $sql);

						$this->db->query($sql);

						$gruppen = explode(' ', (string)$gruppen);

						//neues lookup setzen für results
						foreach ($results as $res) {

							foreach ($gruppen as $k2 => $v2) {
								if ($v2 > 0) {
									$sql = sprintf("INSERT INTO %s SET
										  news_user_id_lu='%d',
										  news_gruppe_id_lu='%d'",
										DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
										$res['news_user_id'],
										$v2);
									$this->db->query($sql);
								}
							}
						}

						$duplicate_array = $mail_adresses = array();
						for ($i = 0; $i <= count($results) - 1; $i++) {
							$results[$i]['news_user_email'] = trim($results[$i]['news_user_email']);
							// keine Dublette, email hinnzuf�gen
							if (!in_array($results[$i]['news_user_email'], $mail_adresses)) {
								$mail_adresses[] = $results[$i]['news_user_email'];
							}
							else {
								$duplicate_array[] = $results[$i]; // Dublette, alles speichern
							}
						}
						// gefundene Dubletten l�schen
						if (count($duplicate_array)) {
							foreach ($duplicate_array as $key => $value) {
								$sql = sprintf("DELETE FROM %s WHERE news_user_id = '%d'",
									$this->cms->tbname['papoo_news_user'],
									$this->db->escape($value['news_user_id'])
								);
								$this->db->query($sql);
							}
						}
					}
					else {
						$sql = sprintf("INSERT INTO %s SET news_name_nach = '%s',
												news_name_vor = '%s',
												news_name_str = '%s',
												news_name_plz = '%s',
												news_name_ort = '%s',
												news_name_gender = '%s',
												news_name_staat = '%s',
												news_user_email = '%s',
												news_active = 1,
												news_key = '%s',
												news_signup_date = '%s',
												news_gruppen = '%s'",
							$this->cms->tbname['papoo_news_user'],
							$this->db->escape($value[$this->name]),
							$this->db->escape($value[$this->vorname]),
							$this->db->escape($value[$this->strasse]),
							$this->db->escape($value[$this->plz]),
							$this->db->escape($value[$this->ort]),
							$news_name_gender,
							'Deutschland',
							$this->db->escape($value[$this->mail]),
							$key,
							"I " . date("d.m.Y - H:i:s"),
							$nlgruppe
						);

						// Zeilenumbr�che wieder rein
						$sql = str_replace('###n###', "\n", $sql);
						$sql = str_replace('###r###', "\r", $sql);

						$this->db->query($sql);

						//lookup setzen
						$sql = sprintf("SELECT MAX(news_user_id) FROM %s",
							$this->cms->tbname['papoo_news_user']
						);
						$result = $this->db->get_var($sql);

						$gruppen = explode(" ", $nlgruppe);

						foreach ($gruppen as $k2 => $v2) {
							if ($v2 > 0) {
								$sql = sprintf("INSERT INTO %s SET
                                  news_user_id_lu='%d',
                                  news_gruppe_id_lu='%d'",
									DB_PRAEFIX . "papoo_news_user_lookup_gruppen",
									$result,
									$v2);
								$this->db->query($sql);
							}
						}
					}
				}
			}
		}
		// wenn noch kein Ende der Datei erreicht wurde, dann reload und n�chstes P�ckchen verarbeiten
		if ($this->ende_der_datei != "ja") {
			// die Seite erneut aufrufen und Nummer des letzten Elements �bergeben
			$self = $_SERVER['PHP_SELF']
				. "?menuid="
				. $this->checked->menuid
				. "&makeimport=Import starten&counter_start="
				. ($this->counter_start + $this->max_counter_pro_runde)
				. "&import=1&template="
				. $this->checked->template
				. "&blacklist="
				. $this->checked->blacklist;
			if (empty($_SESSION['debug_stopallredirect'])) {
				header('REFRESH: 0; URL=' . $self);
			}
			echo "Import l&auml;uft. Je nach Gr&ouml;sse der Importdatei kann der Import mehrere Minuten dauern...<br /><br />";
			echo ($this->counter_start + $this->max_counter_pro_runde)
				. " von "
				. $_SESSION['nl_import']['import_anz_zeilen']
				. " Datens&auml;tzen eingelesen.<br />\n<br />\n";
			echo '<a href= "' . $self . '">Weiter</a><br />';
			exit;
		}
		else {
			$this->content->template['import'] = 1; // Anzeige Template f�r Import Abonnenten aktiv
			$this->content->template['nl_import_ende'] = 1;
			if ($this->checked->blacklist == 1) {
				$this->content->template['is_import_blacklist'] = 1;
				debug::Print_d($this->content->template['is_import_blacklist']);
			}
		}
	}
}

$news = new news();
