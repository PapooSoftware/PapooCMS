<?php
/**
 * #####################################
 * # CMS Papoo                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.2                  #
 * #####################################
 */

/**
 * Class intern_upload_class
 */
class intern_upload_class
{
	/** @var cms */
	public $cms;
	/** @var ezSQL_mysqli */
	public $db;
	/** @var checked_class */
	public $checked;
	/** @var message_class */
	public $message;
	/** @var user_class */
	public $user;
	/** @var intern_user */
	public $intern_user;
	/** @var content_class */
	public $content;
	/** @var forum */
	public $forum;
	/** @var searcher */
	public $such;
	/** @var intern_menu_class */
	public $intern_menu;
	/** @var diverse_class */
	public $diverse;
	/** @var string */
	public $db_praefix;
	public $pfadhier = PAPOO_ABS_PFAD;

	/** @var array Nicht erlaubte Dateiendungen */
	private $illegalFileSuffixes = [
		'php', 'phtml', 'php3', 'php4', 'php5', 'php7',
		'html', 'cgi', 'pl', 'js',
	];

	/**
	 * intern_upload_class constructor.
	 */
	function __construct()
	{
		global $cms, $db, $checked, $message, $user, $intern_user, $content, $forum, $searcher, $intern_menu, $diverse,
			   $db_praefix;
		$this->cms = &$cms;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->message = &$message;
		$this->user = &$user;
		$this->intern_user = &$intern_user;
		$this->content = &$content;
		$this->forum = &$forum;
		$this->such = &$searcher;
		$this->intern_menu = &$intern_menu;
		$this->diverse = &$diverse;
		$this->db_praefix = &$db_praefix;

		IfNotSetNull($this->nur_mp3);
		IfNotSetNull($this->content->template['template_weiche_upload']);
		IfNotSetNull($this->content->template['erstellen']);
		IfNotSetNull($this->content->template['aendern']);
		IfNotSetNull($this->content->template['suchen_error_text']);
		IfNotSetNull($this->content->template['gefunden']);
		IfNotSetNull($this->content->template['bilder_active_cat_id']);
	}

	/**
	 * Dateinamen bereinigen
	 *
	 * @param string $val zu bereinigender Dateiname
	 *
	 * @return string
	 */
	function tidy_filename($val)
	{
		// whitespace durch Unterstrich ersetzen
		$new = preg_replace('=(\s+)=', '_', $val);

		// Umlaute konvertieren
		$map = ['ä' => 'ae', 'Ä' => 'AE', 'ß' => 'ss', 'ö' => 'oe', 'Ö' => 'OE', 'Ü' => 'UE', 'ü' => 'ue',];
		$new = str_replace(array_keys($map), array_values($map), $new);

		// alle anderen Zeichen verwerfen
		return preg_replace('#[^A-Za-z0-9_.-]#', '', $new);
	}

	/**
	 * @return void
	 */
	function make_inhalt()
	{
		IfNotSetNull($this->checked->messageget);
		IfNotSetNull($this->checked->action);
		// Überprüfen ob Zugriff auf die Inhalte besteht
		$this->user->check_access();

		switch ($this->checked->menuid) {
			// Startseite hochladen von Dateien
		case "31" :
			if (empty ($this->checked->messageget)) {
				$this->content->template['text'] = $this->content->template['message_19'];
			}
			if ($this->checked->messageget == 21) {
				$this->content->template['text'] = "<h3>" . $this->content->template['message_21'] . "</h3>";
			}
			break;

			// Hier wird eine neue Datei hochgeladen
		case "32" :
			$this->check_upload();
			$this->get_cat_list();
			if ($this->checked->action == "MULTI_DATEI") {
				$this->content->template['multiupload'] = "ok";
				$this->content->template['data_dir'] = '/dokumente/upload';
				$this->content->template['image_dir'] = $this->checked->image_dir;
			}
			else if ($this->checked->action == "MULTI_UPLOAD") {
				$this->make_multi_upload_now();
			}
			break;

			// Hier werden die Informationen zu einer Datei geändert
		case "33" :
			// Meldungen ausgeben:
			// Daten wurden gespeichert
			if ($this->checked->messageget == 21) {
				$this->content->template['text'] = "<h3>" . $this->content->template['message_21'] . "</h3>";
			}
			// Daten wurden gelöscht
			if ($this->checked->messageget == 22) {
				$this->content->template['text'] = "<h3>" . $this->content->template['message_22'] . "</h3>";
			}

			$temp_action = $this->checked->action;
			switch ($temp_action) {
			case 'delete_selected_files':
				$this->delete_selected_files();
				break;

			case "change_file":
				$this->upload_change_file();
				break;

			case "version_restore":
				$this->upload_version_restore($this->checked->version_id);
				break;

			case "version_delete":
				$this->upload_version_delete($this->checked->version_id);
				break;
			}

			$this->change_upload();
			$this->get_cat_list();

			break;
		}
	}

	/**
	 * @return void
	 */
	function make_multi_upload_now()
	{
		//Bilder im Pfad auslesen
		$images = $this->diverse->directoryToArray(PAPOO_ABS_PFAD . "/interna/templates_c/upload");
		$images_path = $images;

		if (is_array($images)) {
			foreach ($images as $k => $v) {
				$images[$k] = str_ireplace(PAPOO_ABS_PFAD, "..", $v);
			}
		}

		//Ans Template geben für die Eingabe der alt / title Daten
		$this->content->template['image_liste'] = $images;

		//Template passend setzen
		$this->content->template['multiupload'] = $this->content->template['create_image_data'] = "ok";
		$this->content->template['image_dir'] = $this->checked->image_dir;

		if (!empty($this->checked->strSubmit_multiimages_data)) {
			//Die Bilder aus dem Upload Verzeichnis kopieren
			if (is_array($images_path)) {
				$i = 0;
				foreach ($images_path as $k => $v) {
					$v_temp = preg_replace("/ /", "-", $v);
					$v_dest = preg_replace("/[^a-zA-Z0-9-\.]/", "", strtolower(basename($v_temp)));

					if (file_exists(PAPOO_ABS_PFAD . "/dokumente/upload/" . $v_dest)) {
						$v_dest = str_ireplace(
							$this->user->userid . "-",
							$this->user->userid . "-" . rand(0, time()) . "-",
							$v_dest
						);
					}

					//Kopieren
					copy($v, PAPOO_ABS_PFAD . "/dokumente/upload/" . $v_dest);
					$filesize = @filesize(PAPOO_ABS_PFAD . "/dokumente/upload/" . $v_dest);

					//Löschen
					unlink($v);

					//Dann Dateien anlegen
					$sql = sprintf(
						'INSERT INTO %s
						SET downloadlink = "%s",
							downloadgroesse = "%s",
							downloaduserid = "%s",
							downloadkategorie = "%d",
							downloadname = "%s",
							zeitpunkt = NOW();',
						$this->cms->tbname['papoo_download'],
						"/dokumente/upload/" . $this->db->escape($v_dest),
						$filesize,
						$this->user->userid,
						$this->content->template['image_dir'],
						$this->checked->alt[$i]
					);
					$this->db->query($sql);
					$insertid_file = $this->db->insert_id;

					//Sprachdaten anlegen
					$sql = sprintf(
						'INSERT INTO %1$s SET download_id = "%2$d", lang_id = "%3$d", downloadname = "%4$s";',
						$this->cms->tbname['papoo_language_download'],
						$insertid_file,
						$this->cms->lang_back_content_id,
						$this->checked->alt[$i]
					);
					$this->db->query($sql);

					//Lookup Datei Rechte
					$sql = sprintf(
						'INSERT INTO %s SET download_id_id = "%d", gruppen_id_id = 10;',
						$this->cms->tbname['papoo_lookup_download'],
						$insertid_file
					);
					$this->db->query($sql);

					$i++;
				}
			}

			$this->content->template['multiupload_fin'] = "ok";
		}
	}

	/**
	 * @return void
	 */
	function check_upload()
	{
		// erstellen wird festegelegt für das erscheinen in der html template datei
		$this->content->template['erstellen'] = '1';

		@ini_set('max_execution_time', '3600');
		@ini_set('post_max_size', '200M');
		@ini_set('upload_max_filesize', '200M');

		if (!empty ($this->checked->formSubmit)) {
			$uploadSuccess = false;
			// Zielverzeichniss
			$destination_dir = '/dokumente/upload';

			// Upload durchführen
			$upload_do = new file_upload($this->pfadhier);
			$upload_do->set_addPraefix((bool)!$this->cms->system_config_data['config_uplaod_hide_praefix']);

			// Wenn Files hochgeladen wurden
			if (count($_FILES) > 0) {
				$_FILES['myfile']['name'] = $this->tidy_filename($_FILES['myfile']['name']);

				// Durchführen und falls etwas schief geht
				$uploadSuccess = $upload_do->upload($_FILES['myfile'], $destination_dir, 0, $this->illegalFileSuffixes, 1);
			}

			// wenn dder Upload geklappt hat, Daten eintragen.
			if ($uploadSuccess) {
				$filename = $upload_do->file['name'];
				$filesize = $upload_do->file['size'];
				$download = "/dokumente/upload/" . $filename;

				$sql = sprintf(
					'INSERT INTO %s 
					SET downloadlink = "%s", 
						downloadgroesse = "%d",
						downloadfeature = "x",
						downloadkategorie = "%d",
						downloaduserid = "%d",
						zeitpunkt = NOW()',
					$this->cms->papoo_download,
					$this->db->escape($download),
					$filesize,
					$this->db->escape($this->checked->downloadkategorie),
					$this->db->escape($this->user->userid)
				);
				$this->db->query($sql);
				$downloadid = $this->db->insert_id;

				// Einträge für extended search
				global $ext_search;
				if (!empty($ext_search) && $ext_search->pdf_extension == true) {
					$path = PAPOO_ABS_PFAD . $download;
					$finfo = new finfo(FILEINFO_MIME_TYPE);
					if ($finfo->file($path) == "application/pdf") {
						$search_create = new class_search_create;
						$search_create->create_page_pdf($path);
					}
				}

				// Download-Namen speichern
				if (!empty($this->checked->lang)) {
					// alte Einträge löschen
					$sql = sprintf(
						'DELETE FROM %s WHERE download_id = "%d";',
						$this->cms->papoo_language_download,
						$this->db->escape($downloadid)
					);
					$this->db->query($sql);

					foreach ($this->checked->lang as $sprach_id => $data) {
						// Wenn kein Name angegeben ist, Dateiname verwenden
						$downloadname = empty($data['name']) ? $filename : $data['name'];

						$sql = sprintf(
							'INSERT INTO %s SET download_id = "%d", lang_id = "%d", downloadname = "%s";',
							$this->cms->papoo_language_download,
							$this->db->escape($downloadid),
							$this->db->escape($sprach_id),
							$this->db->escape($downloadname)
						);
						$this->db->query($sql);
					}
				}
				// Downloadrechte setzen
				// Gruppe "Adminstrator" darf immer lesen
				$this->checked->gruppelesen[] = 1;

				// neue Einträge erstellen
				foreach ($this->checked->gruppelesen as $insert) {
					$sql = sprintf(
						'INSERT INTO %s SET download_id_id = "%d", gruppen_id_id = "%d";',
						$this->cms->papoo_lookup_download,
						$this->db->escape($downloadid),
						$this->db->escape($insert)
					);
					$this->db->query($sql);
				}

				$this->diverse->http_redirect("./upload.php?menuid=31&messageget=21", 303);
			}
			// Fehler beim Hochladen..
			else {
				// Meldung zeigen..
				$this->content->template['text'] = '<div class="error">' . $upload_do->error . "</div>";

				// Sprach-Einträge ausfüllen
				$this->make_div_lang();
				$sprachen = $this->content->template['menlang'];
				$this->content->template['menlang'] = [];
				if (!empty($sprachen)) {
					foreach ($sprachen as $sprache) {
						$sprache['linkname'] = "nodecode:" . $this->diverse->encode_quote(
								$this->checked->lang[$sprache['lang_id']]['name']
							);
						$this->content->template['menlang'][] = $sprache;
					}
				}

				// Kategorie übergeben
				$this->content->template['downloadkategorie'] = $this->checked->downloadkategorie;
				// Gruppen-Liste erstellen
				$temp_liste_gruppen = $this->get_gruppen_list();
				$temp_liste_rechte = $this->checked->gruppelesen;

				if (!empty($temp_liste_gruppen)) {
					foreach ($temp_liste_gruppen as $gruppe) {
						$gruppe['checked'] =
							(!empty($temp_liste_rechte) && in_array($gruppe['gruppeid'], $temp_liste_rechte));

						$this->content->template['gruppelesen_data'][] = $gruppe;
					}
				}
			}
		}
		// nichts ausgewählt, blankes Formular anbieten
		else {
			// verschiedene Sprachen ausfüllen
			$this->make_div_lang();
			// Gruppen-Liste erstellen
			$this->content->template['gruppelesen_data'] = $this->get_gruppen_list();
		}
	}

	/**
	 * @param $search
	 *
	 * @return mixed|string
	 */
	function clean($search)
	{
		//$search auf unerlaubte Zeichen überprüfen und evtl. bereinigen
		$search = trim($search);
		$remove = "<>'\"%*\\";
		for ($i = 0; $i < strlen($remove); $i++) {
			$search = str_replace(substr($remove, $i, 1), "", $search);
		}

		return $search;
	}

	/**
	 *
	 */
	function change_upload()
	{
		$this->content->template['aendern'] = '1';
		$link_data = [];
		// Wenn kein Link ausgewählt ist, Suchmaske anbieten
		if (empty ($this->checked->id)) {
			$this->content->template['suchen'] = '1';
			// wenn die Suche angefragt ist, suchen
			if (!empty ($this->checked->search)) {
				// Suchbegriff bereinigen $this->clean
				$this->search = ($this->checked->search);
				$this->content->template['search'] = $this->search;

				$mp3 = $this->nur_mp3 == 1 ? " AND t1.downloadlink LIKE '%mp3%'" : "";

				// Daten aus der Datenbank holen
				$suchergebniss = sprintf(
					'SELECT DISTINCT t1.*, t2.* 
					FROM %1$s AS t1
						INNER JOIN %2$s AS t2 ON t1.downloadid = t2.download_id
						INNER JOIN %3$s AS t3 ON t1.downloadkategorie = t3.dateien_cat_id_id
						INNER JOIN %5$s AS t5 ON t5.gruppeid = t3.gruppeid_id
						INNER JOIN %4$s AS t4 ON t4.gruppenid = t5.gruppeid
					WHERE (t2.downloadname LIKE "%6$s" AND t2.lang_id = "%7$d" AND t4.userid = "%8$d" %9$s)
						OR (t1.downloadlink LIKE "%6$s" AND t2.lang_id = "%7$d" AND t4.userid = "%8$d" %9$s)
					ORDER BY t2.downloadname DESC',
					$this->cms->papoo_download,
					$this->cms->papoo_language_download,
					$this->cms->tbname['papoo_lookup_cat_dateien'],
					$this->cms->tbname['papoo_lookup_ug'],
					$this->cms->tbname['papoo_gruppe'],
					"%" . $this->db->escape($this->search) . "%",
					$this->db->escape($this->cms->lang_id),
					$this->db->escape($this->user->userid),
					$mp3
				);
				$result = $this->db->get_results($suchergebniss);

				// wenn leer, dann nix machen
				if (!empty ($result)) {

					// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
					IfNotSetNull($typ);

					// Daten an Template übergeben
					foreach ($result as $row) {
						array_push($link_data,
							[
								'linkname' => $row->downloadname,
								'downloadlink' => $row->downloadlink,
								'linkid' => $row->downloadid,
								'typ' => $typ,
								'wieoft' => $row->wieoft,
								'zeitpunkt' => $row->zeitpunkt,
							]
						);
					}
					$this->content->template['table_data_files'] = $link_data;
					$this->content->template['suchen'] = '';
					$this->content->template['gefunden'] = '1';
				}
				// nichts gefunden anzeigen
				else {
					$this->content->template['suchen_error_text'] = $this->content->template['message_5'];
				}
			}
			else {
				if (empty($this->checked->downloadkategorie)) {
					$this->checked->downloadkategorie = 0;
				}

				$this->content->template['downloadkategorie'] = $this->checked->downloadkategorie;
				$mp3 = $this->nur_mp3 == 1 ? " AND t1.downloadlink LIKE '%mp3%'" : "";

				$suchergebniss = sprintf(
					'SELECT DISTINCT t1.*, t2.* 
					FROM %1$s AS t1
						INNER JOIN %2$s AS t2 ON t1.downloadid = t2.download_id
						INNER JOIN %3$s AS t3 ON t1.downloadkategorie = t3.dateien_cat_id_id
						INNER JOIN %5$s AS t5 ON t5.gruppeid = t3.gruppeid_id
						INNER JOIN %4$s AS t4 ON t4.gruppenid = t5.gruppeid
					WHERE t1.downloadkategorie = "%6$d"
						AND t2.lang_id = "%7$d"
						AND t4.userid = %8$d
						%9$s
					ORDER BY t1.downloadid DESC;',
					$this->cms->papoo_download,
					$this->cms->papoo_language_download,
					$this->cms->tbname['papoo_lookup_cat_dateien'],
					$this->cms->tbname['papoo_lookup_ug'],
					$this->cms->tbname['papoo_gruppe'],
					$this->db->escape($this->checked->downloadkategorie),
					$this->db->escape($this->cms->lang_id),
					$this->db->escape($this->user->userid),
					$mp3
				);
				if ($this->checked->downloadkategorie < 1) {
					$suchergebniss = sprintf(
						'SELECT DISTINCT t1.*, t2.* 
						FROM %1$s AS t1, %2$s AS t2, %3$s AS t3, %4$s AS t4, %5$s AS t5
						WHERE t1.downloadkategorie = "%6$d"
							AND t1.downloadid=t2.download_id AND t2.lang_id="%7$d"
							AND t4.userid="%8$d"
							AND t4.gruppenid=t5.gruppeid
							AND t5.gruppeid=t3.gruppeid_id
							%9$s
						ORDER BY t1.downloadid DESC;',
						$this->cms->papoo_download,
						$this->cms->papoo_language_download,
						$this->cms->tbname['papoo_lookup_cat_dateien'],
						$this->cms->tbname['papoo_lookup_ug'],
						$this->cms->tbname['papoo_gruppe'],
						$this->db->escape($this->checked->downloadkategorie),
						$this->db->escape($this->cms->lang_id),
						$this->db->escape($this->user->userid),
						$mp3
					);
				}
				$result = $this->db->get_results($suchergebniss);

				// wenn leer, dann nix machen
				if (!empty ($result)) {
					// Daten an Template übergeben
					foreach ($result as $row) {
						$typ = $this->diverse->get_file_icon($row->downloadlink);
						$row->downloadlink = substr($row->downloadlink, 1);
						$row->zeitpunkt = str_replace(" ", "<br />", $row->zeitpunkt);
						array_push(
							$link_data,
							[
								'linkname' => $row->downloadname,
								'downloadlink' => $row->downloadlink,
								'linkid' => $row->downloadid,
								'typ' => $typ,
								'wieoft' => $row->wieoft,
								'zeitpunkt' => $row->zeitpunkt,
							]
						);
					}

					$this->content->template['table_data_files'] = $link_data;
					$this->content->template['gefunden'] = '1';
				}
				// nichts gefunden anzeigen
				else {
					$this->content->template['kat_error_text'] = $this->content->template['message_25'];
				}
			}
		}
		// Datei ist ausgewählt, Daten aus der Datenbank holen
		else {
			if (empty ($this->checked->formSubmit) and empty ($this->checked->formSubmit_del)) {
				$this->content->template['versionen'] = $this->upload_versionen_liste($this->checked->id);

				$suchergebniss = sprintf(
					'SELECT * FROM %s WHERE downloadid = "%d";',
					$this->cms->papoo_download,
					$this->db->escape($this->checked->id)
				);
				$result = $this->db->get_results($suchergebniss);

				if (!empty ($result)) {
					// Daten zuweisen für template
					foreach ($result as $row) {
						$this->content->template['downloadid'] = $row->downloadid;
						$this->content->template['linkname'] = $row->downloadname;
						$this->content->template['downloadkategorie'] = $row->downloadkategorie;
						$this->content->template['linktitel'] = basename($row->downloadlink);
						$this->content->template['linkbody'] = $row->downloadlink;
					}
				}

				// Gruppenzugehörigkeit einstellen
				$temp_liste_gruppen = $this->get_gruppen_list();
				$sql = sprintf(
					'SELECT gruppen_id_id FROM %s WHERE download_id_id = "%d";',
					$this->cms->papoo_lookup_download,
					$this->db->escape($this->checked->id)
				);
				$temp_liste_rechte = $this->db->get_results($sql, ARRAY_A);

				if (!empty($temp_liste_gruppen)) {
					foreach ($temp_liste_gruppen as $gruppe) {
						$gruppe['checked'] = false;
						if (!empty($temp_liste_rechte) && in_array(['gruppen_id_id' => $gruppe['gruppeid']], $temp_liste_rechte)) {
							$gruppe['checked'] = true;
						}
						$this->content->template['gruppelesen_data'][] = $gruppe;
					}
				}

				// Sprache auswählen
				$this->downloadid = $this->checked->id;
				$this->make_div_lang();

				// sagen daten werden geändert
				$this->content->template['aendern_now'] = '1';
			}
			// Daten wurden geändert und sollen eingetragen werden, daher überprüfen
			else {
				if (!empty ($this->checked->formSubmit) and !empty ($this->checked->linkbody)) {
					// wenn okay, dann updaten
					$sql = sprintf(
						'UPDATE %s SET downloadlink = "%s", downloadkategorie = "%d" WHERE downloadid = "%d";',
						$this->cms->papoo_download,
						$this->db->escape($this->checked->linkbody),
						$this->db->escape($this->checked->downloadkategorie),
						$this->db->escape($this->checked->id)
					);
					$this->db->query($sql);

					// neue Spracheinträge für Dateinamen schreiben
					// alte Einträge löschen
					$sql = sprintf(
						'DELETE FROM %s WHERE download_id = "%d";',
						$this->cms->papoo_language_download,
						$this->db->escape($this->checked->id)
					);
					$this->db->query($sql);

					foreach ($this->checked->lang as $sprach_id => $data) {
						$sql = sprintf(
							'INSERT INTO %s SET download_id = "%d", lang_id = "%d", downloadname = "%s";',
							$this->cms->papoo_language_download,
							$this->db->escape($this->checked->id),
							$this->db->escape($sprach_id),
							$this->db->escape($data['name'])
						);
						$this->db->query($sql);
					}

					// Gruppen Downloadrechte setzen
					// alte Einträge löschen
					$sql = sprintf(
						'DELETE FROM %s WHERE download_id_id = "%d";',
						$this->cms->papoo_lookup_download,
						$this->db->escape($this->checked->id)
					);
					$this->db->query($sql);
					// Gruppe "Administrator" darf immer
					$this->checked->gruppelesen[] = 1;

					// neue Einträge erstellen
					foreach ($this->checked->gruppelesen as $insert) {
						$sql = sprintf(
							'INSERT INTO %s SET download_id_id = "%d", gruppen_id_id = "%d";',
							$this->cms->papoo_lookup_download,
							$this->db->escape($this->checked->id),
							$this->db->escape($insert)
						);
						$this->db->query($sql);
					}

					$this->diverse->http_redirect("./upload.php?menuid=33&messageget=21", 303);
				}
				else {
					if (!empty ($this->checked->formSubmit_del)) {
						if ($this->checked->action == "delete_do") {
							$this->upload_delete($this->checked->id);
						}
						else {
							$this->content->template['template_weiche_upload'] = "DELETE";
							$this->content->template['upload_data'] = $this->upload_data($this->checked->id);
						}
					}
				}
			}
		}
	}

	/**
	 *
	 */
	function upload_change_file()
	{
		// Zielverzeichniss
		$destination_dir = '/dokumente/upload';
		// Upload durchführen
		$upload_do = new file_upload($this->pfadhier);
		//Zeitstempel
		$temp_date = date("Ymd\hHis");

		$upload_do->set_addPraefix((bool)!$this->cms->system_config_data['config_uplaod_hide_praefix']);

		// Wenn Files hochgeladen wurden
		if (empty($_FILES) || $_FILES['myfile']['size'] == 0) {
			return;
		}

		$temp_filename_backup = $this->checked->id . "_" . $temp_date . "_" . $this->checked->org_filename;
		$path_upload = $this->pfadhier . "/dokumente/upload/" . $this->checked->org_filename;
		$path_backup = $this->pfadhier . "/dokumente/backup/" . $temp_filename_backup;
		$path_tmp_org = $this->pfadhier . "/dokumente/backup/_tmp_" . $this->checked->org_filename;

		// Backup der alten Datei anlegen
		copy($path_upload, ($this->cms->stamm_documents_change_backup ? $path_backup : $path_tmp_org));
		//unlink($path_upload);

		if ($this->checked->renameFile === "1") {
			$upload_do->copy_mode = 2;
		}

		// Durchführen und falls etwas schief geht
		if (!$upload_do->upload($_FILES['myfile'], $destination_dir, 0, $this->illegalFileSuffixes, 1)) {
			// Backup-Datei wieder löschen
			if ($this->cms->stamm_documents_change_backup) {
				//unlink($this->pfadhier."/dokumente/backup/".$temp_filename_backup);
				copy($path_backup, $path_upload);
			}
			else {
				// Temp-Backup wieder herstellen
				copy($path_upload, $path_tmp_org);
			}
		}
		else {
			// Daten der alten Datei in Versionstabelle eintragen
			if ($this->cms->stamm_documents_change_backup) {
				$temp_upload = $this->upload_data($this->checked->id);
				$sql = sprintf(
					'INSERT INTO %s
					SET dv_downloadid = "%d",
						dv_downloadlink = "%s",
						dv_downloaduserid = "%d",
						dv_zeitpunkt = "%s",
						dv_org_filename = "%s";',
					$this->db_praefix . "papoo_download_versionen",
					$temp_upload['downloadid'],
					$this->db->escape($temp_filename_backup),
					$temp_upload['downloaduserid'],
					$temp_upload['zeitpunkt'],
					$this->db->escape($this->checked->org_filename)
				);
				$this->db->query($sql);
			}
			else {
				// Temp-Backup loeschen
				unlink($path_tmp_org);
			}

			// Daten der neuen Datei in DB eintragen
			$filename = $upload_do->file['name'];
			$filesize = $upload_do->file['size'];

			//Alten Dateinamen beibehalten
			$alter_name = PAPOO_ABS_PFAD . "/dokumente/upload/" . $this->checked->org_filename;
			$neuer_name = PAPOO_ABS_PFAD . "/dokumente/upload/" . $filename;

			if ($alter_name !== $neuer_name) {
				unlink($alter_name);

				if ($this->checked->renameFile === "1") {
					//Umkopieren, neue Datei löschen
					copy($neuer_name, $alter_name);
					unlink($neuer_name);

					//Name ändern
					$filename = $this->checked->org_filename;
				}
			}

			$download = "/dokumente/upload/" . $filename;

			$sql = sprintf(
				'UPDATE %s
				SET downloadlink = "%s",
					downloadgroesse = "%d",
					downloadfeature = "x",
					downloaduserid = "%d",
					zeitpunkt = NOW()
				WHERE downloadid = "%d";',
				$this->cms->papoo_download,
				$this->db->escape($download),
				$filesize,
				$this->user->userid,
				$this->checked->id
			);
			$this->db->query($sql);
		}

		// Einträge aus der erweiterten Suche löschen und wieder neu erstellen
		global $ext_search;
		if (!empty($ext_search) && $ext_search->pdf_extension == true) {
			$path_new = PAPOO_ABS_PFAD . "/dokumente/upload/" . $upload_do->file['name'];
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			if ($finfo->file($path_upload) == "application/pdf") {
				$search_create = new class_search_create;
				$search_create->delete_page_pdf($path_upload);
				$search_create->create_page_pdf($path_new);
			}
		}
	}

	/**
	 * @param int $file_id
	 * @param bool $redirectAfterFileIsRemoved
	 */
	function upload_delete($file_id = 0, $redirectAfterFileIsRemoved = true)
	{
		$temp_file = $this->upload_data($file_id);

		if (empty($temp_file)) {
			return;
		}

		$path = PAPOO_ABS_PFAD . $temp_file['downloadlink'];
		// Versionen loeschen
		$this->upload_versionen_delete($file_id);

		// Eintrag aus Tabelle "papoo_download" löschen
		$sql = sprintf(
			'DELETE FROM %s WHERE downloadid = "%d";',
			$this->cms->papoo_download,
			$this->db->escape($file_id)
		);
		$this->db->query($sql);

		// Einträge aus Sprachen-Tabelle "papoo_language_download" löschen
		$sql = sprintf(
			'DELETE FROM %s WHERE download_id = "%d";',
			$this->cms->papoo_language_download,
			$this->db->escape($file_id)
		);
		$this->db->query($sql);

		// Einträge aus Rechte-Tabelle "papoo_lookup_download" löschen
		$sql = sprintf(
			'DELETE FROM %s WHERE download_id_id = "%d";',
			$this->cms->papoo_lookup_download,
			$this->db->escape($file_id)
		);
		$this->db->query($sql);

		// Einträge aus der erweiterten Suche löschen
		global $ext_search;
		if (!empty($ext_search) && $ext_search->pdf_extension == true) {
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			if ($finfo->file($path) == "application/pdf") {
				$search_create = new class_search_create;
				$search_create->delete_page_pdf($path);
			}
		}

		// Datei löschen
		$deleteFile = $this->pfadhier . $temp_file['downloadlink'];
		// prüfe vorher den Zugriff auf die Datei ....
		if (is_file($deleteFile) && is_writeable($deleteFile) && unlink($deleteFile)) {
			if ($redirectAfterFileIsRemoved) {
				// Text gelöscht ausgeben
				$this->diverse->http_redirect('./upload.php?menuid=33&messageget=22', 303);
			}
		}
		// Wenn Datei nicht gelöscht werden konnte
		else {
			// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
			IfNotSetNull($linkbody);

			// "Datei konnte nicht gelöscht werden"-Text ausgeben
			$this->content->template['text'] = $this->content->template['message_23'] . "<p>$linkbody</p>";
		}
	}

	/**
	 * Für alle verfügbaren Sprachen Text anbieten zum Ausfüllen
	 *
	 * @return void
	 */
	function make_div_lang()
	{
		$sprachen = [];

		// aktive Sprachen raussuchen
		$eskuel = sprintf('SELECT lang_id, lang_long FROM %s WHERE more_lang = 2;', $this->cms->papoo_name_language);
		$reslang = $this->db->get_results($eskuel);

		// Daten dazu raussuchen
		if (!empty($reslang)) {
			IfNotSetNull($this->downloadid);
			foreach ($reslang as $lan) {
				$temp_array = [];

				// Downlad-Name dieser Sprache laden
				$sel_dat = sprintf(
					'SELECT downloadname FROM %s WHERE lang_id = %d AND download_id = %d LIMIT 1;',
					$this->cms->papoo_language_download,
					$this->db->escape($lan->lang_id),
					$this->db->escape($this->downloadid)
				);
				$downloadname = $this->db->get_var($sel_dat);

				$temp_array['lang_id'] = $lan->lang_id;
				$temp_array['language'] = $lan->lang_long;
				$temp_array['linkname'] = "nodecode:" . $this->diverse->encode_quote($downloadname);

				$sprachen[$lan->lang_id] = $temp_array;
			}
		}
		$this->content->template['menlang'] = $sprachen;
	}

	/**
	 * Die Kategorien-Liste erstellen
	 *
	 * @return array
	 */
	function get_cat_list()
	{
		$intern_ordner = new intern_ordner_class();
		$intern_ordner->show_ordner_bilder("papoo_kategorie_dateien");

		//Aktueller Level
		IfNotSetNull($this->checked->downloadkategorie);
		IfNotSetNull($aktu_sub_id);
		IfNotSetNull($level);
		if (is_array($this->content->template['dirlist'])) {
			foreach ($this->content->template['dirlist'] as $key => $value) {
				if ($this->checked->downloadkategorie == $value['dateien_cat_id']) {
					$level = $value['dateien_sub_cat_level'] + 1;
					$aktu_sub_id = $value['dateien_sub_cat_von'];
				}
			}
		}
		$this->content->template['dateien_cat_id'] = $aktu_sub_id;
		$this->content->template['dateien_active_cat_id'] = $this->checked->downloadkategorie;

		$mp3 = $this->nur_mp3 == 1 ? " WHERE downloadlink LIKE '%mp3%'" : "";

		$sql = sprintf(
			'SELECT dateien_cat_id, COUNT(downloadid) AS count
			FROM %1$s LEFT JOIN %2$s ON downloadkategorie = dateien_cat_id
			%3$s
			GROUP BY dateien_cat_id;',
			$this->cms->tbname['papoo_download'],
			$this->cms->tbname['papoo_kategorie_dateien'],
			$mp3
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		if (is_array($result)) {
			foreach ($result as $key => $value) {
				if (empty($value['dateien_cat_id'])) {
					$value['dateien_cat_id'] = 0;
				}
				$neu[$value['dateien_cat_id']] = $value['count'];
			}
		}
		IfNotSetNull($neu);
		$this->content->template['dateien_cat_id_count'] = $neu;
		IfNotSetNull($this->content->template['dateien_cat_id_count']['0']);

		$sql = sprintf(
			'SELECT DISTINCT(dateien_cat_id),dateien_cat_name
			FROM %1$s,%2$s,%3$s
			WHERE userid = %4$d
				AND gruppenid = gruppeid_id
				AND dateien_cat_id_id = dateien_cat_id
				AND dateien_sub_cat_level = "%5$d"
				AND dateien_sub_cat_von = "%6$d";',
			$this->cms->tbname['papoo_kategorie_dateien'],
			$this->cms->tbname['papoo_lookup_cat_dateien'],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->user->userid,
			$level,
			$this->db->escape($this->checked->downloadkategorie)
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		$this->content->template['result_cat'] = $result;

		return $result;
	}

	/**
	 * Die Gruppen-Liste erstellen
	 *
	 * @param string $modus '' erstellt Liste aller Gruppen ohne Administrator<br />
	 *                      'all' erstellt Liste aller Gruppen
	 *
	 * @return array
	 */
	function get_gruppen_list($modus = "")
	{
		$sql = sprintf(
			'SELECT gruppenname, gruppeid FROM %s %s ORDER BY gruppenname;',
			$this->cms->papoo_gruppe,
			strtolower($modus) === 'all' ? '' : 'WHERE gruppeid <> 1'
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * @param int $download_id
	 *
	 * @return array|null
	 */
	function upload_data($download_id = 0)
	{
		if (!$download_id) {
			return [];
		}

		$sql = sprintf(
			'SELECT * FROM %s WHERE downloadid = "%d";', $this->db_praefix . "papoo_download", $download_id
		);
		return $this->db->get_row($sql, ARRAY_A);
	}

	/**
	 * @param int $version_id
	 *
	 * @return void
	 */
	function upload_version_restore($version_id = 0)
	{
		$temp_file_version = $this->upload_version_data($this->checked->version_id);
		$temp_file_aktu = $this->upload_data($temp_file_version['dv_downloadid']);

		// Backup der aktuellen Datei anlegen
		$temp_date = date("Ymd\hHis");
		$temp_filename_backup =
			$temp_file_version['dv_downloadid'] . "_" . $temp_date . "_" . basename($temp_file_aktu['downloadlink']);

		if (file_exists($this->pfadhier . $temp_file_aktu['downloadlink'])) {
			rename(
				$this->pfadhier . $temp_file_aktu['downloadlink'],
				$this->pfadhier . "/dokumente/backup/" . $temp_filename_backup
			);

			// Daten der aktuellen Datei in Versionstabelle eintragen
			if ($this->cms->stamm_documents_change_backup) {
				$sql = sprintf(
					'INSERT INTO %s
					SET dv_downloadid = "%d", 
						dv_downloadlink = "%s", 
						dv_downloaduserid = "%d", 
						dv_zeitpunkt  ="%s", 
						dv_org_filename = "%s";',
					$this->db_praefix . "papoo_download_versionen",
					$temp_file_aktu['downloadid'],
					$this->db->escape($temp_filename_backup),
					$temp_file_aktu['downloaduserid'],
					$temp_file_aktu['zeitpunkt'],
					$this->db->escape(basename($temp_file_aktu['downloadlink']))
				);
				$this->db->query($sql);
			}
		}

		// Versions-Datei verschieben
		if (file_exists($this->pfadhier . "/dokumente/backup/" . $temp_file_version['dv_downloadlink'])) {
			rename(
				$this->pfadhier . "/dokumente/backup/" . $temp_file_version['dv_downloadlink'],
				$this->pfadhier . "/dokumente/upload/" . $temp_file_version['dv_org_filename']
			);

			// Daten der Versions-Datei in DB eintragen
			$filesize = filesize($this->pfadhier . "/dokumente/upload/" . $temp_file_version['dv_org_filename']);
			$download = "/dokumente/upload/" . $temp_file_version['dv_org_filename'];

			$sql = sprintf(
				'UPDATE %s
				SET downloadlink = "%s", 
					downloadgroesse = "%d", 
					downloadfeature = "x", 
					downloaduserid = "%d", 
					zeitpunkt = NOW()
				WHERE downloadid = "%d";',
				$this->cms->papoo_download,
				$this->db->escape($download),
				$filesize,
				$this->user->userid,

				$temp_file_version['dv_downloadid']
			);
			$this->db->query($sql);
		}

		// alten Eintrag in Versionstabelle loeschen
		$sql = sprintf("DELETE FROM %s WHERE dv_id='%d'", $this->db_praefix . "papoo_download_versionen", $version_id);
		$this->db->query($sql);

		$this->diverse->http_redirect("./upload.php?menuid=33&id=" . $temp_file_version['dv_downloadid'], 303);
	}

	/**
	 * @param int $version_id
	 */
	function upload_version_delete($version_id = 0)
	{
		$temp_version = $this->upload_version_data($version_id);
		@unlink($this->pfadhier . "/dokumente/backup/" . $temp_version['dv_downloadlink']);

		$sql = sprintf(
			'DELETE FROM %s WHERE dv_id = "%d";',
			$this->db_praefix . "papoo_download_versionen",
			$temp_version['dv_id']
		);
		$this->db->query($sql);
	}

	/**
	 * @param int $version_id
	 *
	 * @return array
	 */
	function upload_version_data($version_id = 0)
	{
		if (!$version_id) {
			return [];
		}

		$sql = sprintf(
			'SELECT * FROM %s WHERE dv_id = "%d";', $this->db_praefix . "papoo_download_versionen", $version_id
		);
		return $this->db->get_row($sql, ARRAY_A);
	}

	/**
	 * @param int $download_id
	 */
	function upload_versionen_delete($download_id = 0)
	{
		if ($download_id) {
			$temp_versionen = $this->upload_versionen_liste($download_id);
			if (!empty($temp_versionen)) {
				foreach ($temp_versionen as $version) {
					$this->upload_version_delete($version['dv_id']);
				}
			}
		}
	}

	/**
	 * @param int $download_id
	 *
	 * @return array
	 */
	function upload_versionen_liste($download_id = 0)
	{
		if (!$download_id) {
			return [];
		}

		$sql = sprintf(
			'SELECT t1.*, t2.username 
			FROM %s AS t1
				LEFT JOIN %s AS t2 ON t1.dv_downloaduserid = t2.userid
			WHERE dv_downloadid = "%d"
			ORDER BY dv_zeitpunkt DESC;',
			$this->db_praefix . "papoo_download_versionen",
			$this->db_praefix . "papoo_user",
			$download_id
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 *
	 */
	private function delete_selected_files()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST' || is_array($_POST['delete_selected_files']) == false) {
			$this->diverse->http_redirect('/interna/upload.php?menuid=33', 303);
		}

		$fileIds = array_map(function ($id) {
			return (int)$id;
		}, $_POST['delete_selected_files']);

		// Ausgewählte Dateien aus dem System entfernen
		foreach ($fileIds as $id) {
			$this->upload_delete($id, false);
		}

		$queryParams = [
			'menuid' => 33,
			'messageget' => 21,
		];

		if (isset($this->checked->downloadkategorie)) {
			$queryParams['downloadkategorie'] = $this->checked->downloadkategorie;
		}

		$this->diverse->http_redirect('/interna/upload.php?'.http_build_query($queryParams, '', '&'), 303);
	}
}

$intern_upload = new intern_upload_class();
