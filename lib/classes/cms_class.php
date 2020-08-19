<?php
/**
 * File: lib/classes/cms_class.php
 *
 * @link      http://www.papoo.de/
 * @copyright 2004
 * @author    Carsten Euwens
 * @package   papoo
 * @since     version 2.0
 *
 */

/**
 * Diese Klasse initialisert alle Eigenschaften die das Papoo CMS
 * aus den Stammdaten hat.
 */
class cms
{
	/** @var string[] $extraFreeUrlPatterns A list of regular expressions to match plugin specific free urls. */
	private static $extraFreeUrlPatterns = [];

	/** @var mixed Soll die rechte Spalte angezeigt werden? */
	public $yesno;
	/** @var mixed soll der Styleswitcher angezeigt werden? */
	public $switcher;
	/** @var mixed soll ein einloggen im Frontend möglich sein? */
	public $einlogenyesno;
	/** @var string Der Starttext auf der Startseite */
	public $top_text;
	/** @var string Der Titel der Seite */
	public $title_send;
	/** @var mixed Sollen Artikel auf der Startseite lang(2) oder kurz(1) angezeigt werden */
	public $artikel_lang_yn;
	/** @var mixed sollen überhaupt Artikel angezeigt werden */
	public $artikel_yn;
	/** @var mixed sollen die Anzahl der Kommentare mit angezeigt werden */
	public $comment_yn;
	/** @var mixed Seitentitel */
	public $title;
	/** @var mixed Seitenbeschreibung für Metatag */
	public $description;
	/** @var mixed Seitenbeschreibung für Metatag */
	public $keywords;
	/** @var string Title ganz oben auf der Seite */
	public $top_title;
	/** @var string Papoo Versionsnummer */
	public $version;
	/** @var mixed Autor der Seite */
	public $seite_autor;
	/** @var mixed Mod_rewrite Status */
	public $mod_rewrite;
	/** @var mixed CSS Styleswitcherdaten */
	public $styleswitcher;
	/** @var mixed aktueller Style */
	public $standardstyle;
	/** @var mixed Maximale Anzahl der Einträge anzeigen */
	public $pagesize;
	/** @var mixed Anzahl der maximalen Ergebniss im Forum */
	public $pagesize_forum = "";
	/** @var mixed Liste der Sprachen */
	public $liste_lang = "";
	/** @var mixed richtiger Seitentitel */
	public $seitentitle = "";
	/** @var mixed Text für Kontakt-Formular */
	public $text_kontakt = "";
	/** @var mixed */
	public $showpapoo = 1;
	/** @var mixed Spamschutz */
	public $spamschutz_modus = 0;
	/** @var mixed Trenner surls */
	public $webvar = "web-";
	/** @var mixed Artikel short urls mit .html wenn leer am Ende erzwingen */
	public $artikel_url_mit_html = false;
	/** @var string */
	public $system_show_template_comment = "";
	/** @var ezSQL_mysqli */
	public $db;
	/** @var checked_class */
	public $checked;
	/** @var session_class */
	public $session;
	/** @var content_class */
	public $content;
	/** @var diverse_class */
	public $diverse;
	/** @var module_class */
	public $module;
	/** @var string */
	public $db_praefix;

	/**
	 * cms constructor.
	 */
	function __construct()
	{
		global $db, $checked, $session, $content, $diverse, $module, $db_praefix;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->session = &$session;
		$this->content = &$content;
		$this->diverse = &$diverse;
		$this->module = &$module;
		$this->db_praefix = $db_praefix;
		// globaler pfadhier einbinden

		$this->pfadhier = PAPOO_ABS_PFAD;

		$this->webverzeichnis = PAPOO_WEB_PFAD;

		$this->content->template['search'] = &$this->content->template['message_2281'];

		$this->content->template['PAPOO_VERSION'] = PAPOO_VERSION;
		$this->content->template['PAPOO_EDITION'] = PAPOO_EDITION;
		$this->content->template['PAPOO_REVISION'] = PAPOO_REVISION;

		// Aktuelle Url ans Template Übergeben
		if (stristr($_SERVER['SERVER_NAME'], "www")) {
			$referer = "http://www." . $_SERVER['SERVER_NAME'] . "" . $_SERVER['REQUEST_URI'];
		}
		else {
			$referer = "http://" . $_SERVER['SERVER_NAME'] . "" . $_SERVER['REQUEST_URI'];
		}
		$this->content->template['browserUrl'] = $referer;

		/*
		* Namen der Datenbanken erzeugen
		*
		* Zuerst wird ein Array gefüllt, das ist wichtig für die dynamische Zuweisung
		* innerhalb von Plugins, später werden auch alle Abfragen
		* innerhalb von papoo darÜber laufen
		*
		*/
		//Page
		$this->get_surls_dat_page();

		//Tabellen Liste erzeugen
		$this->make_tablelist();

		//papoo_links beinhaltet alle Links für die automatische Ersetzung

		$this->papoo_links = $db_praefix . "papoo_links";

		//Darin sind alle Daten der 3. Spalte enthalten
		$this->papoo_collum3 = $db_praefix . "papoo_collum3";
		//Alle Bilder mit Link zum Verzeichnis
		$this->papoo_images = $db_praefix . "papoo_images";
		//Alle internen Nachrichten zwischen den Usern
		$this->papoo_mail = $db_praefix . "papoo_mail";
		//Alle vorhandenen Styledateien mit Link zur jeweiligen Datei
		$this->papoo_styles = $db_praefix . "papoo_styles";
		//Hier ist der Großteil der Stammdaten drin verschlüsselt
		$this->papoo_daten = $db_praefix . "papoo_daten";
		$this->papoo_forums = $db_praefix . "papoo_forums";
		$this->papoo_gruppe = $db_praefix . "papoo_gruppe";
		$this->papoo_menu = $db_praefix . "papoo_me_nu";
		$this->papoo_menu_int = $db_praefix . "papoo_menuint";
		$this->papoo_message = $db_praefix . "papoo_message";
		$this->papoo_repore = $db_praefix . "papoo_repore";
		$this->papoo_user = $db_praefix . "papoo_user";
		$this->papoo_lang_en = $db_praefix . "papoo_lang_en";
		$this->papoo_abk = $db_praefix . "papoo_abk";
		$this->papoo_download = $db_praefix . "papoo_download";
		$this->papoo_lookup_ug = $db_praefix . "papoo_lookup_ug";
		$this->papoo_lookup_men = $db_praefix . "papoo_lookup_men_ext";
		$this->papoo_lookup_men_int = $db_praefix . "papoo_lookup_men_int";
		$this->papoo_lookup_forum_read = $db_praefix . "papoo_lookup_forum_read";
		$this->papoo_lookup_forum_write = $db_praefix . "papoo_lookup_forum_write";
		$this->papoo_name_language = $db_praefix . "papoo_name_language";
		$this->papoo_menu_language = $db_praefix . "papoo_menu_language";
		$this->papoo_menuint_language = $db_praefix . "papoo_men_uint_language";
		$this->papoo_language_download = $db_praefix . "papoo_language_download";
		$this->papoo_language_article = $db_praefix . "papoo_language_article";
		$this->papoo_language_image = $db_praefix . "papoo_language_image";
		$this->papoo_language_collum3 = $db_praefix . "papoo_language_collum3";
		$this->papoo_language_stamm = $db_praefix . "papoo_language_stamm";
		$this->papoo_plugins = $db_praefix . "papoo_plugins";
		$this->papoo_updates = $db_praefix . "papoo_updates";
		$this->papoo_blacklist = $db_praefix . "papoo_blacklist";
		$this->papoo_lookup_download = $db_praefix . "papoo_lookup_download";
		$this->papoo_lookup_me_all_ext = $db_praefix . "papoo_lookup_me_all_ext";
		$this->papoo_lookup_article = $db_praefix . "papoo_lookup_article";

		$this->papoo_plugin_language = $db_praefix . "papoo_plugin_language";
		$this->papoo_pluginclasses = $db_praefix . "papoo_pluginclasses";

		// die Eigenschaften der Klasse werden initialisiert durch den Aufruf der data() Funktion
		$this->data();

		//Standard Charsset
		if (defined("admin")) {
			$this->check_domain();
		}

		//Checken ob das Setup Verzeichnis noch exisistier und die site_conf die richtigen rechte hat
		$this->check_setup();

		$this->content->template['sulrstrenner'] = $this->webvar;

		//Menue und Artikeldaten ids raussuchen und an Klassik Übergeben
		$this->make_old_menu();

		if ($this->mod_free) {
			// die Eigenschaften der Klasse werden initialisiert durch den Aufruf der data() Funktion
			//NOchmal um die sprechenden urls auch in die Sprachwahl zu bringen
			$this->data();
		}

		//CMS Limit einbauen
		$this->makecmslimit();

		$searchengines = ["Validator", "Bobby", "phpwww", "Googlebot", "MSN"];
		// Für jeden Eintrag durchgehen und Useragenten abfragen der aufruft
		foreach ($searchengines as $key => $val) {
			if (stristr($_SERVER['HTTP_USER_AGENT'], $val)) {
				$this->content->template['noload'] = "ok";
			}
		}
		if (empty($_SESSION['user_dzvhae_id'])) {
			$_SESSION['user_dzvhae_id'] = "";
		}
		$this->content->template['user_dzvhae_id'] = $_SESSION['user_dzvhae_id'];

		//urldaten Übergeben
		$this->makeurldat();

		// Style initialisieren (nur im Frontend)
		$this->make_style();

		$this->url_rewrite_reset();

		if (!isset($this->content->template['noload'])) {
			$this->content->template['noload'] = null;
		}
	}

	/**
	 * @return string[] A list of regular expressions to match plugin specific free urls.
	 */
	public function getExtraFreeUrlPatterns(): array
	{
		return self::$extraFreeUrlPatterns;
	}

	/**
	 * URLdaten übergeben
	 */
	function makeurldat()
	{
		$drin = "";
		foreach ($_GET as $key => $value) {
			$key = htmlentities(strip_tags($key));
			$value = htmlentities(strip_tags($value));
			$drin .= $key . "=" . $value . "&amp;";
		}
		$this->content->template['urldatprint'] = $drin;

		if (empty($this->mod_surls)) {
			$this->mod_surls = "";
		}

		if ($this->mod_surls != 1) {
			$this->content->template['urldatself'] = $_SERVER['PHP_SELF'];
		}
	}

	/**
	 * Checken ob das Setup Verzeichnis noch exisistiert und die site_conf die richtigen Rechte hat
	 */
	function check_setup()
	{
		$this->content->template['page_error'][] =
			'<div style="background:#fff;padding:20px;border:2px dotted red;margin:10px;">';
		$error = 0;

		if (($_SERVER['SERVER_NAME'] != 'localhost')
			&& ($_SERVER['SERVER_ADDR'] != '127.0.0.1')
			&& ($_SERVER['SERVER_ADDR'] != '192.168.2.108')
			&& !stristr($_SERVER['SERVER_ADDR'], "192")
		) {
			// ist der "/setup"-Ordner noch vorhanden?
			if (is_dir(PAPOO_ABS_PFAD . "/setup")) {
				$this->content->template['page_error'][] =
					"<strong>[setup-security]</strong> Das Setup Verzeichnis existiert noch bitte l&ouml;schen. "
					. "- The Setup Directory still exists, please delete.<br />";
				$error = 1;
			}
			if (is_dir(PAPOO_ABS_PFAD . "/update")) {
				$this->content->template['page_error'][] =
					"<strong>[setup-security]</strong> Das Update Verzeichnis existiert noch bitte l&ouml;schen. - "
					. "The Update Directory still exists, please delete.<br />";
				$error = 1;
			}
			// ist "lib/site_conf.php" beschreibbar?
			$filePermissionsOctal = substr(sprintf('%o', fileperms(PAPOO_ABS_PFAD . "/lib/site_conf.php")), -3);
			if ($filePermissionsOctal !== '444') {
				$this->content->template['page_error'][] =
					"<strong>[setup-security]</strong>Die Datei lib/site_conf.php ist noch beschreibbar, bitte "
					. "auf 444 setzen. - The file /lib/site_conf.php is still writable, please change the rights "
					. "to 444.<br />";
				$error = 1;
			}
		}

		$this->content->template['page_error'][] = '</div>';
		if ($error == 0) {
			$this->content->template['page_error'] = "";
		}
	}

	/**
	 * setzt die Pfadangabe zu Plugin-Templates bei aktiviertem mod_rewrite
	 */
	function url_rewrite_reset()
	{
		if (!empty ($this->checked->template)) {
			$the_template = $this->checked->template;

			if ($this->mod_rewrite == 2) {
				$the_template = $this->checked->template;
				$the_template = urldecode($the_template);
				$the_template = str_replace(":::", "/", $the_template);
			}

			global $template;

			$temp_style_template =
				PAPOO_ABS_PFAD . "/styles/" . $this->style_dir . "/templates/plugins/" . $the_template;
			if (file_exists($temp_style_template)) {
				$template = $temp_style_template;
			}
			else {
				$template = $this->pfadhier . "/plugins/" . $the_template;
			}
		}
	}

	/**
	 *
	 */
	function data()
	{
		//Wenn Sprache Über eine sprechende url kommt.
		$dolang = "";
		$i = 0;
		foreach ($_GET as $key => $value) {
			//Nur Übergeben wenn var drin vorkommt
			if (stristr($key, "var")) {
				if (!empty($value)) {
					if ($dolang == 1) {
						$this->checked->getlang = $value;
						$dolang = "";
					}
					if ($value == "getlang") {
						$dolang = 1;
					}
				}
			}
			$i++;
		}

		if (empty($_GET['var1'])) {
			$_GET['var1'] = "";
		}

		if (strlen($_GET['var1']) == 2) {
			$this->checked->getlang = $_GET['var1'];
			//Reihenfolge zurücksetzen
			$gatdaten = $_GET;
			$_GET = [];
			$i = 0;
			foreach ($gatdaten as $gdat) {
				if ($i >= 1) {
					$var = "var" . $i;
					$_GET[$var] = $gdat;
				}
				$i++;
			}
		}

		if (!isset($_SESSION['dbp']['papoo_daten'])) {
			$_SESSION['dbp']['papoo_daten'] = null;
		}

		// Standardeinstellungen werden ausgelesen und Variablen werden zugewiesen ####
		if (!is_array($_SESSION['dbp']['papoo_daten'])) {
			$select = $this->db->get_results(sprintf(
				"SELECT * FROM %s LIMIT 1", $this->db_praefix . "papoo_daten"
			));
			$_SESSION['dbp']['papoo_daten'] = $select;
		}
		else {
			$select = $_SESSION['dbp']['papoo_daten'];
		}

		if (empty($this->checked->false)) {
			$this->checked->false = "";
		}

		if ($this->checked->false == 1) {
			$this->content->template['loggedin_false_pass'] = "1";
		}

		//alle Eintrge durchgehen
		if (!empty($select)) {
			foreach ($select as $row) {
				$this->menuintcss = ""; # Wert wird von Klasse menuintcss gesetzt
				$this->pluginscss = ""; # Wert wird von Klasse pluginscss gesetzt

				// Soll die rechte Spalte angezeigt werden?
				$this->yesno = $row->rechte_spalte_internet;
				// soll ein einloggen im Frontend möglich sein?
				$this->einlogenyesno = $row->einloggen_internet;
				// Der Titel der Seite
				$this->title_send = $row->seitenname;

				$this->artikel_yn = $row->artikel_yn;
				$this->artikel_lang_yn = $row->artikel_lang_yn;

				$this->stamm_cat_sub_ok = $row->stamm_cat_sub_ok;
				$this->stamm_artikel_order = $row->stamm_artikel_order;

				// sollen die Anzahl der Kommentare mit angezeigt werden
				$this->comment_yn = $row->comment_yn;
				//Seitentitel
				$this->title = $row->seitenname;
				//Admin Email, wird zum versand benutzt
				$this->admin_email = $row->admin_email;
				//Papoo VersionsNummer
				$this->content->template['papoo_version'] = PAPOO_VERSION_STRING;
				//Autor der Seite
				$this->content->template['seite_autor'] = $row->autor_seite;
				//Mod_rewrite Status
				$this->mod_rewrite = $row->mod_rewrite;
				if ($this->mod_rewrite == 3) {
					//Template auf sprechende urls setzen
					$this->content->template['sp_urls'] = "ok";
					//Intern auf mod_rewrite setzen
					$this->mod_rewrite = 2;
					$this->mod_surls = 1;
				}
				// mod_mime Status
				$this->mod_mime = $row->mod_mime;
				$this->mod_free = $row->mod_free;
				$this->webvar = $this->webvar_org = $row->stamm_surls_trenner . "-";

				if ($this->mod_free == 1) {
					//Template auf sprechende urls setzen
					$this->content->template['sp_urls'] = "";
					$this->content->template['free_sp_urls'] = "ok";
					//Intern auf mod_rewrite setzen
					$this->mod_rewrite = $this->mod_surls = $this->mod_mime = 0;
					$this->webvar = $this->webvar_org = "";
				}

				// Welcher editor wird verwendet
				$this->editor = $row->editor;
				// Sprache im Backend
				$this->lang_backend = $row->lang_backend;
				$this->content->template['lang_back_default'] = $row->lang_backend;
				// Sprache im Frontend
				$this->frontend_lang = $this->lang_frontend = $row->lang_frontend;
				$this->content->template['lang_front_default'] = $row->lang_frontend;
				// Sprach-Auto-Detect
				$this->lang_autodetect = $row->lang_autodetect;
				// Datumsformatierung
				$this->lang_dateformat = $row->lang_dateformat;
				//automatische Ersetzung an oder nicht
				$this->do_replace_ok = $row->do_replace;
				// E-Mail bei Artikel-Freigabe
				$this->stamm_benach_artikelfreigabe_email = $row->stamm_benach_artikelfreigabe_email;
				//Papoo News anzeigen
				$this->showpapoo = $row->showpapoo;
				// CacheFunktion
				$this->cache_yn = $row->cache_yn;
				// Forum als Board oder Thread
				$this->forum_board = $row->forum_board;
				// Wie soll die liste der letzten Beiträge angezeigt werden? 0: einzelne Beiträge; 1: nur Themen anzeigen;
				$this->forum_letzte_modus = $row->forum_letzte_modus;
				//Suchbox ja/nein
				$this->content->template['suchbox'] = $row->suchbox;
				// Spamschutz-Modus ( 0: deaktiv; 1..n: Modus des Spamschutz; 99 = zufälliger Spamschutz-Modus)
				$this->spamschutz_modus = $row->spamschutz_modus;
				//SeitenName richtig
				$this->seitentitle = "";
				$this->seitentitle = $row->seitentitel;
				$this->stamm_cat_startseite_ok = $row->stamm_cat_startseite_ok;
				$this->categories = $row->categories;
				$this->content->template['category_set'] = $this->categories;

				//Anzahl der Seitenaufrufe
				$this->gesamtcount = $row->gesamtcount;
				//Rechnungsnummer
				$this->renr = $row->renr;
				//News Ja/nein
				$this->news = $row->news;
				//Anzahl der News
				$this->newsnr = $row->newsnr;

				$this->anzeig_besucher = $row->anzeig_besucher;
				$this->content->template['anzeig_besucher'] = $row->anzeig_besucher;
				if (empty($row->system_show_template_comment)) {
					$row->system_show_template_comment = "";
				}

				$this->content->template['system_show_template_comment'] = $row->system_show_template_comment;
				$this->anzeig_autor = $row->anzeig_autor;
				$this->content->template['anzeig_autor'] = $row->anzeig_autor;
				$this->anzeig_filter = $row->anzeig_filter;
				$this->content->template['anzeig_filter'] = $row->anzeig_filter;

				$this->captcha_sitekey = $row->captcha_sitekey;
				$this->content->template['captcha_sitekey'] = $row->captcha_sitekey;

				$this->captcha_secret = $row->captcha_secret;
				$this->content->template['captcha_secret'] = $row->captcha_secret;

				$this->anzeig_pageviews = $row->anzeig_pageviews;
				$this->content->template['anzeig_pageviews'] = $row->anzeig_pageviews;
				$this->content->template['anzeig_versende'] = $row->anzeig_versende;
				$this->content->template['anzeig_drucken'] = $row->anzeig_drucken;
				$this->anzeig_menue_komplett = $row->anzeig_menue_komplett;
				$this->benach_email = $row->benach_email;
				$this->show_menu_all = $row->show_menu_all;
				$this->content->template['benach_email'] = $row->benach_email;
				$this->benach_neueruser = $row->benach_neueruser;
				$this->content->template['benach_neueruser'] = $row->benach_neueruser;
				$this->benach_neu_gaestebuch = $row->benach_neu_gaestebuch;
				$this->gaestebuch_msg_frei = $row->gaestebuch_msg_frei;
				$this->content->template['benach_neu_gaestebuch'] = $row->benach_neu_gaestebuch;
				$this->benach_neu_kommentar = $row->benach_neu_kommentar;
				$this->content->template['benach_neu_kommentar'] = $row->benach_neu_kommentar;
				$this->benach_neu_forum = $row->benach_neu_forum;
				$this->content->template['benach_neu_forum'] = $row->benach_neu_forum;

				$this->content->template['usability'] = $row->usability;
				$this->stamm_artikel_rechte_jeder = $row->stamm_artikel_rechte_jeder;
				$this->stamm_artikel_rechte_chef = $row->stamm_artikel_rechte_chef;
				$this->stamm_veroffen_yn = $row->stamm_veroffen_yn;
				$this->stamm_listen_yn = $row->stamm_listen_yn;
				$this->stamm_menu_rechte_jeder = $row->stamm_menu_rechte_jeder;

				// Spamschutz für Konatkt-Formular
				$this->stamm_kontakt_spamschutz = $row->stamm_kontakt_spamschutz;
				$this->content->template['stamm_kontakt_spamschutz'] = $row->stamm_kontakt_spamschutz;

				$this->stamm_documents_change_backup = $row->stamm_documents_change_backup;

				/**
				 * Debugging Modus
				 */
				$this->content->template['error_ok'] = "show";

				$this->content->template['twitterHandle'] = isset($row->twitter_handle) ? $row->twitter_handle : null;
			}

			$sql = sprintf(
				"SELECT * FROM %s",
				$this->tbname['papoo_config']
			);
			$result = $this->db->get_results($sql, ARRAY_A);

			$this->system_config_data = $result['0'];
			@ini_set("session.gc_maxlifetime", $this->system_config_data['config_session_lifetime']);
			if ($this->system_config_data['config_memory_limit_feld'] > 36) {
				set_minimum_memory_limit($this->system_config_data['config_memory_limit_feld'] . "M");
			}
			ini_set("max_execution_time", $this->system_config_data['config_skriptlaufzeit_feld']);
			ini_set("upload_max_filesize", $this->system_config_data['config_upload_dateigre_feld']);

			$this->content->template['extra_meta_front'] = "nobr:" . $this->system_config_data['config_metatagszusatz'];
			if (is_array($result['0'])) {
				foreach ($result['0'] as $key => $value) {
					$this->content->template[$key] = $value;
					$this->$key = $value;
				}
			}
		}

		//Seitentitel
		$this->content->template['site_name'] = $this->title;
		$this->content->template['link'] = $this->title_send;
		$this->content->template['breadcrumb_link'] = $this->title_send;

		// alternative Sprachen bereitstellen
		$this->make_lang();
		//surls auf Sprache setzen
		if ($this->lang_short != $this->lang_frontend) {
			$this->webvar = $this->lang_short . "/" . $this->webvar;
			$this->url_lang_var = $this->lang_short;
		}
		//weitere Stammdaten nach Language bereitstellen
		$sql = sprintf(
			"SELECT * FROM %s WHERE lang_id='%d' AND stamm_id='2' LIMIT 1",
			$this->papoo_language_stamm,
			$this->db->escape($this->lang_id)
		);
		$select = $this->db->get_results($sql);

		//alle Einträge durchgehen
		if (!empty ($select)) {
			foreach ($select as $row) {
				// Der Starttext auf der Startseite
				$this->top_text = $row->start_text;
				//Seitenbeschreibung für Metatag
				$this->content->template['description'] = $this->diverse->encode_quote($row->beschreibung);
				//Seitenbeschreibung für Metatag
				$this->content->template['keywords'] = $this->diverse->encode_quote($row->stichwort);
				//Title ganz oben auf der Seite
				$this->content->template['top_title'] = $row->head_title;
				$this->content->template['site_title'] = $this->diverse->encode_quote($row->seitentitle);
				// Text für Kontakt-Formular
				$this->content->template['text_kontakt'] = $this->diverse->do_pfadeanpassen($row->kontakt_text);
			}
		}
		else {
			$this->top_text = $this->content->template['description'] =
			$this->content->template['keywords'] = $this->content->template['top_title'] =
			$this->content->template['site_title'] = $this->content->template['text_kontakt'] = "";
		}

		// Weitere Stammdaten initialisieren und zuweisen
		$defined = "no";
		if (defined("admin")) {
			$this->content->template['IS_ADMIN'] = "yes";
		}
		//wenn mod_rewrite aktiv ist Variablen zuweisen, ansonsten Standardvariablen zuweisen
		if ($this->mod_rewrite == "2" and $defined == "no") {
			// Variablen zuweisen
			$this->content->template['frag_connect'] = $this->content->template['plus_connect'] =
			$this->content->template['gleich_connect'] = $this->content->template['slashanf'] = "/";
			// Wäre besser in slashanf, das wird aber in den Template-Dateien nicht so verwendet
			$this->content->template['slash'] = $this->webverzeichnis . "/";
			$this->content->template['index'] = "index";
		}
		else {
			if ($this->mod_free == 1) {
				$this->content->template['frag_connect'] = $this->content->template['plus_connect'] =
				$this->content->template['gleich_connect'] = $this->content->template['index'] = "";
				// Wäre besser in slashanf, das wird aber in den Template-Dateien nicht so verwendet
				$this->content->template['slash'] = $this->webverzeichnis . "/";
				$this->content->template['slashanf'] = "/";
			}
			else {
				// Variablen zuweisen
				$this->content->template['frag_connect'] = "?";
				$this->content->template['plus_connect'] = "&amp;";
				$this->content->template['gleich_connect'] = "=";
				$this->content->template['slash'] = "";
				$this->content->template['slashanf'] = "./";
				$this->content->template['index'] = "index.php";
			}
		}
		$this->content->template['webverzeichnis'] = $this->webverzeichnis . "/";
		$this->content->template['pfadhier'] = $this->pfadhier . "/";

		$this->content->template['rss_lang_id'] = $this->lang_id;

		if (!isset($this->content->template['free_sp_urls'])) {
			$this->content->template['free_sp_urls'] = null;
		}

		if (!isset($this->content->template['sp_urls'])) {
			$this->content->template['sp_urls'] = null;
		}
	}

	/**
	 * Limit Funktion umsetzen
	 */
	function makecmslimit()
	{
		$this->pagesize = 1;
		if (empty ($this->checked->forumid)) {
			// Anzahl der maximalen Suchergebnisse FIXME -> Datenbank
			//ermgölicht die Anzeige von 100, 30, 20, 10 Treffern je Seite (s. Template index.html)
			$this->pagesize = $this->system_config_data['config_paginierung'];
		}
		else {
			// Anzahl der maximalen Ergebniss im Forum
			$this->pagesize = 200;
		}

		if (!is_numeric($this->pagesize)) {
			$this->pagesize = 1;
		}

		if (empty ($this->checked->page)) {
			$this->checked->page = "";
		}
		$page = $this->checked->page;

		if ($this->checked->page > 100000) {
			$page = 100000;
		}
		else if ($this->checked->page < 1) {
			$page = 1;
		}

		if (!is_numeric($this->checked->page)) {
			unset ($page);
		}

		// Limit für die Datenbank abfragen
		if (isset ($page) && is_numeric($page)) {
			//verhindert die erneute Anzeige des letzten Artikels unten auf der nächsten Seite als 1. oben
			// (realistischer; 20 Artikel je Seite)
			$this->sqllimit = "LIMIT " . (($page - 1) * $this->pagesize) . "," . ($this->pagesize);
		}
		else {
			$this->sqllimit = "LIMIT 0, " . ($this->pagesize);
		}
	}

	/**
	 * @param int $temp_param_style_id
	 */
	function make_style($temp_param_style_id = 0)
	{
		global $template;

		if (empty($this->checked->menuid) && $template == "index.html") {
			$this->checked->menuid = 1;
		}
		IfNotSetNull($this->checked->menuid);
		$sql = sprintf(
			"SELECT menu_spez_layout FROM %s WHERE menuid='%d'",
			$this->tbname['papoo_me_nu'],
			$this->checked->menuid
		);
		if (empty($this->checked->style)) {
			$this->checked->stylepost = $this->db->get_var($sql);
		}

		// Schauen ob ein Standardstyle in der Datenbank ist
		$selectstyle = sprintf("SELECT * FROM %s ORDER BY style_name", $this->db_praefix . "papoo_styles");
		$resultstyle = $this->db->get_results($selectstyle);

		$this->style_dir = "";

		if (!empty ($resultstyle)) {
			foreach ($resultstyle as $row) {
				if ($row->standard_style == 1) {
					if (!file_exists(PAPOO_ABS_PFAD . "/interna/templates_c/css.txt")) {
						$this->diverse->write_to_file("/interna/templates_c/css.txt", $row->style_id, "w+");
					}
				}
			}
		}

		if (empty($this->gesamtcount)) {
			$this->gesamtcount = "";
		}

		$temp_style = false;
		$temp_default_style = 0;

		// wenn ein anderer Style ausgewählt wurde, zuweisen ... ###
		//if ($this->session->style) $style = $this->session->style;
		if ($temp_param_style_id) {
			$style = $temp_param_style_id;
		}
		elseif (!empty ($this->checked->stylepost)) {
			$temp_style = $this->checked->stylepost;
		}
		elseif (!$temp_style && !empty ($this->checked->style)) {
			$temp_style = $this->checked->style;
		}
		elseif (!$temp_style && !empty($_SESSION['style'])) {
			$temp_style = $_SESSION['style'];
		}

		if ($temp_style or !isset($style)) {
			$style = $temp_style;
		}

		//aktuell kein Style ausgewählt. Daher nochmal nach Standard in der Datenbank schauen
		$style_data = [];

		IfNotSetNull($style);

		if (!empty($resultstyle)) {
			foreach ($resultstyle as $row) {
				$styleid = $row->style_id;
				$selected = "";

				if ($row->standard_style) {
					$temp_default_style = $row->style_id;
				}

				if ($row->style_id == $style) {
					$this->content->template['style_dir'] = $row->style_pfad;
					$this->content->template['style_cc'] = $row->style_cc;

					$selected = 'nodecode:selected="selected"';
					if (empty($this->checked->stylepost)) {
						$_SESSION['style'] = $style;
					}

					$this->style_id = $row->style_id;
					$this->style_dir = $row->style_pfad;
				}

				$style_data[] = [
					'stylevalue' => $styleid, 'style_name' => $row->style_name, 'selected' => $selected,
				];
			}
		}

		//Für die Admin
		$this->content->template['interna_editor_style_exists'] =
			file_exists(PAPOO_ABS_PFAD . "/styles/" . $this->style_dir . "/css/_interna_editor.css");
		$this->content->template['style_data'] = $style_data;

		// Test ob style definiert
		if (empty($this->content->template['style_dir'])) {
			if (empty($temp_param_style_id)) {
				$this->make_style($temp_default_style);
			}
			else {
				echo ".. Style nicht moeglich";
			}
		}
	}

	/**
	 * TODO: Siehe Kommentar in Funktion.
	 *
	 * Hier werden die lokalen Tabellen in ein Array zugewiesen
	 * Der Eintrag ist immer [globaler Name]=>lokaler name
	 * Daher kann man dann einfach in einer Abfrage einfach
	 * $this->cms->tbname['globaler_name'] benutzen
	 */
	function make_tablelist()
	{
		// eigentlich uncool, da diese Funktion auch in der Klasse DumpnRestore zur Verfügung steht.
		// Da die Klasse DumpnRestore aber erst nach der Klasse CMS eingebunden wird, steht sie hier
		// noch nicht zur Verfügung :-((
		// Mögliche Lösung:
		// Hier nur die Variable $this->tbname definieren und diese dann in der Klasse DumpnRestore
		// nachträglich mit Inhalt füllen.
		// Dazu müsste die  Einbindung der Klasse DumpnRestore in Classes.php aus dem Backend- in den
		// Allgemeinen-Bereich verschoben werden.
		$table_list = [];
		global $db_praefix;
		$query = "SHOW TABLES";
		$result = $this->db->get_results($query, ARRAY_A);

		if ($result) {
			foreach ($result as $eintrag) {
				if ($eintrag) {
					foreach ($eintrag as $titel => $tabellenname) {
						if (strpos("XXX" . $tabellenname, $db_praefix) == 3) {
							$name = substr_replace($tabellenname, "", 0, strlen($db_praefix));
							$table_list[$name] = $tabellenname;
						}
					}
				}
			}
		}
		$this->tbname = $table_list;
	}

	/**
	 * @param $get
	 *
	 * @return bool|mixed|null
	 */
	function http_request($get)
	{
		$curl_ret = null;
		if (empty($_SESSION['curl_ok_aktivierung_link_not_ok'])) {
			$url = "http://verify.papoo.de/" . ($get);
			if (curl::installed()) {
				try {
					$curl = new curl($url);
					$curl->setopt(CURLOPT_TIMEOUT, 5);
					$curl->setopt(CURLOPT_RETURNTRANSFER, true);
					$curl->setopt(CURLOPT_HEADER, 0);
					$curl->setopt(CURLOPT_USERAGENT, "Check Agent");
					$curl_ret = $curl->exec();
				}
				catch (curl_exception $e) {
					$curl_ret = false;
				}
			}
			if (empty($curl_ret)) {
				$_SESSION['curl_ok_aktivierung_link_not_ok'] = "NO";
			}
		}
		return $curl_ret;
	}

	/**
	 *
	 */
	function check_domain()
	{
		if (defined("admin")) {
			//Zuerst checken ob DM schonmal aktviert wurde
			if (!empty($_SERVER['HTTP_HOST'])) {
				if (!$this->check_old_aktivierung()) {
					$real_domain = $_SERVER['HTTP_HOST'];
					if (!strstr($real_domain, "http://.")) {
						$real_domain = "http://" . $real_domain;
					}
					if (!strstr($real_domain, "www.") && $real_domain != "http://localhost") {
						$real_domain = trim(str_replace("http://", "http://www.", $real_domain));
					}
					$url = $this->title;
					$get = 'index.php?url=' . urlencode($url) . '&realurl=' . urlencode($real_domain) . '&version='
						. urlencode(PAPOO_VERSION_STRING) . '&check=1';

					if ($real_domain == "http://localhost" || $real_domain == "http://127.0.0.1") {
						$this->content->template['not_verified'] = "";
					}
					else {
						$rdata = $this->http_request($get);
						if ($rdata == "domain_not_ok") {
							$this->content->template['not_verified'] = "ok";
						}
						else {
							$content = $real_domain . ";" . (time() + 691200);
							$this->diverse->write_to_file("/interna/templates_c/aktivierung.txt", $content);
						}
					}
				}
			}
		}
	}

	/**
	 * @return bool
	 */
	function check_old_aktivierung()
	{
		$aktivierungs_file = PAPOO_ABS_PFAD . "/interna/templates_c/aktivierung.txt";
		$aktivierungs_file = str_replace("//", "/", $aktivierungs_file);
		if (file_exists($aktivierungs_file)) {
			$data = file_get_contents($aktivierungs_file);
			$dat_array = explode(";", $data);
			$real_domain = $_SERVER['HTTP_HOST'];
			if (!strstr($real_domain, "http://.")) {
				$real_domain = "http://" . $real_domain;
			}
			if (!strstr($real_domain, "www.") && $real_domain != "http://localhost") {
				$real_domain = trim(str_replace("http://", "http://www.", $real_domain));
			}

			if ($dat_array['0'] == $real_domain && $dat_array['1'] > time()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @return mixed|null
	 */
	function get_lang_frontend()
	{
		return $this->db->get_var("SELECT lang_frontend FROM " . $this->db_praefix . "papoo_daten");
	}

	/**
	 * @return array|null
	 */
	function get_lang_kuerzel()
	{
		$sql = sprintf("SELECT lang_short FROM %s ",
			$this->db_praefix . "papoo_name_language"
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 *
	 */
	function make_lang()
	{
		if (empty($this->checked->getlang)) {
			$this->checked->getlang = "";
		}
		$org_getlang = $this->checked->getlang;
		if (!empty($this->checked->search)) {
			$this->mod_free_search = $this->mod_free;
			$this->mod_free = "";
		}

		if ($this->mod_free == 1 && !defined("admin")) {
			$kuerzel = $this->get_lang_kuerzel();

			$this->checked->getlang = $this->get_lang_frontend();

			//Checken ob es überhaupt eine valide Sprache ist...
			foreach ($kuerzel as $k => $v) {
				$kuerzel[] = $v['lang_short'];

				if (stristr($_SERVER['REQUEST_URI'], "/" . $v['lang_short'] . "/")) {
					$this->checked->getlang = $v['lang_short'];
				}
			}
			if (strlen($org_getlang) == 2) {
				$this->checked->getlang = $org_getlang;
			}
		}
		IfNotSetNull($_SESSION['langdata_front']);
		$aktu_lang_short = $_SESSION['langdata_front']['lang_short'];
		// Sprache für Frontend festlegen:
		// *******************************
		// wenn noch keine Sprache festgelegt wurde
		if (empty($_SESSION['langdata_front'])) {
			$sprache = $this->lang_get($this->lang_frontend);

			// Frontend-Sprache speichern
			$this->lang_save("FRONT", $sprache);
		}
		// Sprache wurde per Modul Sprachwahl geändert
		if (!empty($this->checked->getlang)) {
			$sprache = $this->lang_get($this->checked->getlang);
			// Wenn Sprache aktiv ist, dann diese setzen
			if ($sprache) {
				$this->lang_save("FRONT", $sprache);
			} // .. sonst alte Spracheinstellungen behalten
			else {
				$this->lang_save("FRONT", $_SESSION['langdata_front']);
			}
		} // Sprache wurde schon definiert -> Sprach-Daten aus SESSION nehmen
		else {
			$this->lang_save("FRONT", $_SESSION['langdata_front']);
		}

		// Sprache für Backend festlegen:
		// ******************************
		if (defined("admin")) {
			$_SESSION['dbp'] = [];

			if (!empty($this->checked->submitlang)) {
				if (is_numeric($this->checked->submitlang)) {
					$sql = sprintf(
						"SELECT lang_short FROM %s WHERE lang_id='%d' ",
						$this->tbname['papoo_name_language'],
						$this->db->escape($this->checked->submitlang));
				}
				else {
					$sql = sprintf(
						"SELECT lang_short FROM %s WHERE lang_long='%s' ",
						$this->tbname['papoo_name_language'],
						$this->db->escape($this->checked->submitlang));
				}

				if (empty($_SESSION['dbp']['templang'])) {
					$templang = $this->db->get_var($sql);

					$_SESSION['dbp']['templang'] = $templang;
				}
				else {
					IfNotSetNull($_SESSION['dbp']['templang']);
					$templang = $_SESSION['dbp']['templang'];
				}

				$sprache = $this->lang_get($templang);

				$this->lang_save("FRONT", $sprache);
			}
			// wenn noch keine Sprache festgelegt wurde
			if (empty($_SESSION['langdata_back'])) {
				// Default-Backend-Sprache auslesen und speichern
				$sprache = $this->lang_get($this->lang_backend);
				$this->lang_save("BACK", $sprache);
			} // Sprache wurde schon definiert -> Sprach-Daten aus SESSION nehmen
			else {
				$this->lang_save("BACK", $_SESSION['langdata_back']);
			}
		}

		// Liste für Modul-Sprachwahl erstellen
		// muss leider immer gemacht werden, da die Entscheidung ob das Modul aktiv ist erst später füllt
		$sprachwahl = [];

		// Setze Variable auf NULL falls nicht existent, zwecks Behebung von Warnungen
		if (!isset($_SESSION['dbp']['temp_sprachen'])) {
			$_SESSION['dbp']['temp_sprachen'] = null;
		}
		if (!is_array($_SESSION['dbp']['temp_sprachen'])) {
			$sql = sprintf(
				"SELECT lang_id, lang_long, lang_img, lang_short FROM %s WHERE more_lang='2'",
				$this->papoo_name_language
			);
			$temp_sprachen = $this->db->get_results($sql, ARRAY_A);
			$_SESSION['dbp']['temp_sprachen'] = $temp_sprachen;
		}
		else {
			$temp_sprachen = $_SESSION['dbp']['temp_sprachen'];
		}
		global $template;

		if ($this->mod_free == 1 && !empty($this->checked->reporeid) && $template == "index.html") {
			$sql = sprintf(
				"SELECT url_header, lang_id FROM %s WHERE lan_repore_id=%d",
				DB_PRAEFIX . "papoo_language_article",
				$this->db->escape($this->checked->reporeid)
			);
			$result_lang_art = $this->db->get_results($sql, ARRAY_A);
		}

		if ($this->mod_free == 1 && !empty($this->checked->menuid)) {
			$sql = sprintf(
				"SELECT url_menuname, lang_id FROM %s WHERE menuid_id=%d",
				DB_PRAEFIX . "papoo_menu_language",
				$this->db->escape($this->checked->menuid)
			);
			$result_lang_men = $this->db->get_results($sql, ARRAY_A);
		}

		IfNotSetNull($result_lang_art);
		IfNotSetNull($result_lang_men);

		$this->webverzeichnis_org = $this->webverzeichnis;
		if (!empty($temp_sprachen)) {
			$link_org = $_SERVER['REQUEST_URI'];

			foreach ($temp_sprachen as $temp_sprache) {
				$sprache = [];
				if ($this->mod_free == 1 && !defined("admin")) {
					//Speziall wenn Artikel dann direkt sprechende urls ausgeben wenn free urls
					if ($template == "index.html" && !empty($this->checked->reporeid)
						|| $template == "index.html"
						&& !empty($this->checked->menuid)
						&& empty($this->checked->mv_id)
						|| $template != "index.html" && !empty($this->checked->menuid) && empty($_GET['template'])) {
						//Wir haben einen Artikel
						if (!empty($this->checked->reporeid)) {
							if (is_array($result_lang_art)) {
								foreach ($result_lang_art as $keya => $valuea) {
									if ($temp_sprache['lang_id'] == $valuea['lang_id']) {
										$link = $this->webverzeichnis_org . $valuea['url_header'];
									}
								}
							}
						} //Wir haben mehrere Artikel zu einem Menüpunkt
						else {
							foreach ($result_lang_men as $keym => $valuem) {
								if ($temp_sprache['lang_id'] == $valuem['lang_id']) {
									$link = $this->webverzeichnis_org . $valuem['url_menuname'];
								}
							}
						}
					}
					else {
						//Sonderfall Startseite
						if (empty($_GET['template']) && empty($this->checked->menuid)) {
							// Hole aufgerufene sprechende URL
							$surl = substr($_SERVER['REQUEST_URI'], strlen(PAPOO_WEB_PFAD));
							if ($surl[0] !== "/") {
								$surl = "/" . $surl;
							}
							// Hole zur URL dazugehörigen Menü-Link
							$sql = sprintf(
								"SELECT `menuid_id`, `menulinklang` 
								FROM `%s` 
								WHERE `lang_id`=%d AND `url_menuname`='%s' 
								LIMIT 1;",
								$this->tbname['papoo_menu_language'],
								$temp_sprache['lang_id'],
								$this->db->escape($surl)
							);
							$result = $this->db->get_row($sql, ARRAY_A);
							// Überprüfe auf Aufruf einer anderen Seite, die keine menuid weitergibt
							if (is_array($result) && $result['menuid_id'] != 1) {
								// Generiere Link anhand der sURL
								$link = $this->webverzeichnis_org . $result['menulinklang'];
							} // Wurde kontakt.php, forum.php, etc. aufgerufen -> sURL in DB finden
							else {
								if ($result === null) {
									$phpfile = basename($surl);
									if ($phpfile !== "index.php") {
										$sql = sprintf(
											"SELECT `url_menuname` 
											FROM `%s` 
											WHERE `lang_id`=%d AND `menulinklang`='%s' 
											LIMIT 1;",
											$this->tbname['papoo_menu_language'],
											$temp_sprache['lang_id'],
											$this->db->escape(basename($surl))
										);
										$menuname = $this->db->get_var($sql);
										if ($menuname) {
											// Generiere Link anhand der aufgerufenen Datei (suche sURL in DB)
											$link = $this->webverzeichnis_org . $menuname;
										}
										else {
											// php-Datei als Link verwenden (keine sURL)
											$link = $this->webverzeichnis_org . "/" . $phpfile . "?getlang="
												. $temp_sprache['lang_short'];
										}
									}
									else {
										// Startseite Fallback
										$link = $this->webverzeichnis_org . "/" . $temp_sprache['lang_short'] . "/";
									}
								}
								else {
									// Hier ist die Startseite
									$link = $this->webverzeichnis_org . "/" . $temp_sprache['lang_short'] . "/";
								}
							}
						}
						else {
							if (stristr($link_org, "?") && !stristr($link_org, "/?getlang")
								&& !stristr(
									$link_org,
									"p?getlang"
								)) {
								$link_temp = preg_replace("/(.*?)([\?|&|&amp;])getlang=(.*){2}/i", "$1", $link_org);
								$link = $link_temp . "&amp;getlang=" . $temp_sprache['lang_short'];
								if (stristr($link, "php&getlang")) {
									$link = str_replace("php&getlang", "php?getlang", $link);
								}
							}
							else {
								if (!empty($this->webverzeichnis) && stristr($link_org, $this->webverzeichnis)) {
									$link = str_replace(
										$this->webverzeichnis,
										$this->webverzeichnis . "/" . $temp_sprache['lang_short'],
										$link_org
									);
									$link = str_replace($aktu_lang_short . "/", "", $link);
								}
								else {
									$link_temp = preg_replace("/(.*?)([\?|&|&amp;])getlang=(.*){2}/i", "$1", $link_org);
									$link = $link_temp . "?menuid=" . $this->checked->menuid . "getlang="
										. $temp_sprache['lang_short'];

									if (stristr($link, "/" . $aktu_lang_short . "/?getlang")) {
										$link = str_replace($aktu_lang_short . "/", "", $link);
									}
								}
							}
						}
					}
				}
				else {
					// Link zum Wechseln der Sprache mit aktiviertem ModRewrite
					if ($this->mod_rewrite == 2) {
						// Test ob aktueller Link evtl. "ohne Mod-Rewrite" ist, also z.B. nach Forums-Beitrag editieren
						if (stristr($link_org, "?") && !stristr($link_org, "/?get")) {
							$link_temp = preg_replace("/(.*?)([\?|&|&amp;])getlang=(.*){2}/i", "$1", $link_org);
							$link = $link_temp . "&amp;getlang=" . $temp_sprache['lang_short'];
						}
						else {
							if (empty($this->checked->menuid)) {
								$this->checked->menuid = 1;
							}
							$link_temp = preg_replace("/(.*?)([\?|&|&amp;])getlang=(.*){2}/i", "$1", $link_org);
							if ($link_temp == "/") {
								$link_temp = "/index.php";
							}
							$link = $link_temp . "?menuid=" . $this->checked->menuid . "&getlang="
								. $temp_sprache['lang_short'];
						}
					} // Link zum Wechseln der Sprache OHNE ModRewrite
					else {
						$link_temp = preg_replace("/(.*?)(\?|&|&amp;)getlang=(.*){2}/i", "$1", $link_org);
						if (stristr($link_temp, "?")) {
							$link = $link_temp . "&getlang=" . $temp_sprache['lang_short'];
						}
						else {
							if (empty($this->checked->menuid)) {
								$this->checked->menuid = 1;
							}
							if (empty($link_temp)) {
								$link_temp = "index.php";
							}

							$link = $link_temp . "?menuid=" . $this->checked->menuid . "&getlang="
								. $temp_sprache['lang_short'];
						}

						if (!empty($this->checked->search)) {
							// Setze Variable auf NULL falls nicht existent, zwecks Behebung von Warnungen
							IfNotSetNull($this->checked->erw_suchbereich);
							IfNotSetNull($this->checked->menuid_search);
							IfNotSetNull($this->checked->menuid);
							$link .= "&menuid=" . $this->checked->menuid . "menuid_search="
								. $this->checked->menuid_search . "&erw_suchbereich="
								. $this->checked->erw_suchbereich . "&find=finden";
						}
					}
				}

				IfNotSetNull($link);
				$link = str_ireplace("&reporeid", "&amp;reporeid", $link);
				$link = str_ireplace("&amp;getlang", "&getlang", $link);
				$sprache['lang_id'] = $temp_sprache['lang_id'];
				$sprache['lang_short'] = $temp_sprache['lang_short'];
				$sprache['language'] = $temp_sprache['lang_long'];
				$sprache['lang_bild'] = $temp_sprache['lang_img'];

				if (empty($first_link)) {
					$first_link = "";
				}
				if ($first_link == htmlspecialchars($link)) {
					$link = "/" . $temp_sprache['lang_short'] . "/";
				}
				$sprache['lang_link'] = htmlspecialchars($link);
				if (empty($first_link)) {
					$first_link = $link;
				}

				$sprachwahl[] = $sprache;
			}
		}

		if (!empty($this->checked->search)) {
			$this->mod_free = $this->mod_free_search;
		}

		$this->content->template['languageget'] = $sprachwahl;
	}

	/**
	 * @param string $sprache_short
	 * @param mixed  $frontback
	 *
	 * @return mixed
	 */
	function lang_get($sprache_short = "de", $frontback = "")
	{
		if (strlen($sprache_short) > 3 || strlen($sprache_short) < 2) {
			$sprache_short = "de";
		}
		if (empty($frontback)) {
			$sql = sprintf(
				"SELECT * FROM %s WHERE lang_short='%s' AND more_lang='2' LIMIT 1",
				$this->db_praefix . "papoo_name_language",
				$this->db->escape($sprache_short)
			);
		}
		else {
			$sql = sprintf(
				"SELECT * FROM %s WHERE lang_short='%s' LIMIT 1",
				$this->db_praefix . "papoo_name_language",
				$this->db->escape($sprache_short)
			);
		}

		$result = $this->db->get_results($sql, ARRAY_A);
		$sprache = $result[0];

		if (!empty($sprache)) {
			// Falls Sprach-Richtung (lang_dir) leer ist, auf "ltr" setzen
			if (empty($sprache['lang_dir'])) {
				$sprache['lang_dir'] = "ltr";
			}

			// Datumsformatierung festlegen
			if ($this->lang_dateformat == 1) {
				$sprache['lang_dateformat'] = $sprache['lang_dateformat_long'];
			}
			else {
				$sprache['lang_dateformat'] = $sprache['lang_dateformat_short'];
			}
			// Falls Datumsformatierung (lang_dateformat) leer ist, auf "%d.%m.%G; %H:%M:%S Uhr" setzen
			// (entspricht: deutsch kurz)
			if (empty($sprache['lang_dateformat'])) {
				$sprache['lang_dateformat'] = "%d.%m.%Y; %H:%M:%S";
			}

			return $sprache;
		}
		else {
			return false;
		}
	}

	/**
	 * @param string $frontback
	 * @param array  $sprache
	 */
	function lang_save($frontback = "FRONT", $sprache = [])
	{
		if (!empty($sprache)) {
			// Landestypische Datums-fromatierung festlegen
			setlocale(
				LC_TIME,
				strtolower($sprache['lang_short']) . "_" . strtoupper($sprache['lang_short']) . ".UTF-8",
				strtolower($sprache['lang_short']) . '.UTF-8',
				strtolower($sprache['lang_short']),
				'C.UTF-8',
				'C'
			);

			switch ($frontback) {
			case "FRONT":
				// Daten in CMS-Klassen-Variablen speichern
				$this->lang_id = $sprache['lang_id'];
				$this->lang_back_content_id = $sprache['lang_id'];
				$this->lang_short = $sprache['lang_short'];
				$this->lang_long = $sprache['lang_long'];
				$_SESSION['langid_front'] = $this->lang_id;

				$this->content->template['lang_short'] = $sprache['lang_short'];
				$this->content->template['lang_dir'] = $sprache['lang_dir'];
				$this->content->template['lang_dateformat'] = $sprache['lang_dateformat'];
				$this->content->template['lang_dateformat_short'] = $sprache['lang_dateformat_short'];
				$date_short = explode(";", $this->content->template['lang_dateformat_short']);
				$this->content->template['lang_dateformat_date'] = $date_short['0'];
				$this->content->template['lang_dateformat_long'] = $sprache['lang_dateformat_long'];
				$this->content->template['aktulanglong'] = $sprache['lang_long'];

				// Daten in SESSION-Variablen speichern
				$_SESSION['langdata_front'] = $sprache;
				break;

			case "BACK":
				// Daten in CMS-Klassen-Variablen speichern
				$this->lang_back_id = $sprache['lang_id'];

				$this->lang_back_short = $sprache['lang_short'];
				$this->lang_back_long = $sprache['lang_long'];

				$this->content->template['lang_back_short'] = $sprache['lang_short'];
				$this->content->template['lang_back_dir'] = $sprache['lang_dir'];
				$this->content->template['lang_back_dateformat'] = $sprache['lang_dateformat'];
				$this->content->template['lang_back_dateformat_short'] = $sprache['lang_dateformat_short'];
				$this->content->template['lang_back_dateformat_long'] = $sprache['lang_dateformat_long'];

				$inurl = [];
				foreach ($this->checked as $key => $value) {
					if (isset($_GET[$key])) {
						if ($key === "submitlang") {
							continue;
						}
						$inurl[$key] = $value;
					}
				}
				$this->content->template['self_lang'] = $_SERVER['PHP_SELF'] . "?" . http_build_query($inurl);

				// Daten in SESSION-Variablen speichern
				$_SESSION['langdata_back'] = $sprache;
				break;
			}
		}
	}

	/**
	 * cms::get_surls_dat_page()
	 *
	 * @return void
	 */
	function get_surls_dat_page()
	{
		foreach ($_GET as $key => $value) {
			if (!empty($value)) {
				if (!stristr($value, '.html')) {
					if (empty($this->surl_urlvar)) {
						$this->surl_urlvar = "";
					}
					$this->surl_urlvar .= $value . "/";
				}
				else {
					$pg1 = explode("-", $value);

					if (!isset($pg1['2'])) {
						$pg1['2'] = null;
					}
					if ($pg1['0'] == "page" || $pg1['2'] == "page") {
						$pg2 = explode('.html', $pg1['1']);

						if (is_numeric($pg2['0'])) {
							$this->checked->page = $pg2['0'];
						}
						$pg2 = explode('.html', $pg1['3']);

						if (is_numeric($pg2['0']) && $pg2['0'] >= 1) {
							$this->checked->page = $pg2['0'];
						}
					}
				}
			}
		}
		if (empty($this->surl_urlvar)) {
			$this->surl_urlvar = "";
		}

		$this->surl_dat = $this->surl_urlvar;

		global $db_praefix;
		$sql = "SELECT mod_free FROM " . $db_praefix . "papoo_daten";
		$mod_free = $this->db->get_var($sql);

		if ($mod_free) {
			$sprechende_url = urldecode($_SERVER['REQUEST_URI']);

			//Dann das UNterverzeichnis falls vorhanden
			$sprechende_url = trim(str_replace($this->webverzeichnis, "", $sprechende_url));

			//Dann das trailing Slash
			$sprechende_url = str_replace(PAPOO_WEB_PFAD, "", $sprechende_url);
			$sprechende_url = substr($sprechende_url, 1, (strlen($sprechende_url) - 1));
			$this->surl_dat = "" . $sprechende_url;
			$data = explode("/", $this->surl_dat);

			$last = array_pop($data);
			$pg1 = explode("-", $last);

			IfNotSetNull($pg1['2']);
			if ($pg1['0'] == "page" || $pg1['2'] == "page") {
				$pg2 = explode('.html', $pg1['1']);

				if (is_numeric($pg2['0'])) {
					$this->checked->page = $pg2['0'];
				}
				$pg2 = explode('.html', $pg1['3']);

				if (is_numeric($pg2['0']) && $pg2['0'] >= 1) {
					$this->checked->page = $pg2['0'];
				}
			}
		}
	}

	/**
	 * Die alten Menü und Artikel ids werden erzeugt, damit intern alles weiterlaufen kann
	 * Von extern kommen bei aktiviertem mod_rewrite die glatten urls
	 */
	function make_old_menu()
	{
		if (($this->mod_free == 1 AND !defined("admin"))) {
			global $template;

			$this->content->template['urldatself'] = "index.php";
			switch ($template) {
			case "forum.html":
				$this->content->template['urldatself'] = "forum.php";
				break;
			case "forumthread.html":
				$this->content->template['urldatself'] = "forumthread.php";
				break;
			case "guestbook.html":
				$this->content->template['urldatself'] = "guestbook.php";
				break;
			case "inhalt.html":
				$this->content->template['urldatself'] = "inhalt.php";
				break;
			case "kontakt.html":
				$this->content->template['urldatself'] = "kontakt.php";
				break;
			case "profil.html":
				$this->content->template['urldatself'] = "profil.php";
				break;
			case "login.html":
				$this->content->template['urldatself'] = "login.php";
				break;
			}
			$this->get_menuid_free();
		}

		if (($this->mod_rewrite == 2 AND !defined("admin"))) {
			if ($this->mod_surls == 1) {
				global $template;

				$this->content->template['urldatself'] = "index.php";
				switch ($template) {
				case "forum.html":
					$this->content->template['urldatself'] = "forum.php";
					break;
				case "forumthread.html":
					$this->content->template['urldatself'] = "forumthread.php";
					break;
				case "guestbook.html":
					$this->content->template['urldatself'] = "guestbook.php";
					break;
				case "inhalt.html":
					$this->content->template['urldatself'] = "inhalt.php";
					break;
				case "kontakt.html":
					$this->content->template['urldatself'] = "kontakt.php";
					break;
				case "profil.html":
					$this->content->template['urldatself'] = "profil.php";
					break;
				case "login.html":
					$this->content->template['urldatself'] = "login.php";
					break;
				}
				$this->get_menuid();
			}
		}
	}

	/**
	 * Umlaute und Spezialfälle
	 * ersetzen
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	function revert_uml($url = "")
	{
		$url = urlencode($url);
		$ae = utf8_encode("�");
		$ue = utf8_encode("�");
		$oe = utf8_encode("�");
		$amp = utf8_encode("&");
		$frag = utf8_encode("?");

		$url = str_ireplace("ae", $ae, $url);
		$url = str_ireplace("oe", $oe, $url);
		$url = str_ireplace("ue", $ue, $url);
		$url = str_ireplace("u-e", "ue", $url);
		$url = str_ireplace("a-e", "ae", $url);
		$url = str_ireplace("o-e", "oe", $url);
		$url = str_ireplace("-", " ", $url);
		$url = str_ireplace("xampcod", $amp, $url);
		$url = str_ireplace(" digitff", $frag, $url);
		return $url;
	}

	/**
	 * @param $url
	 *
	 * @return bool
	 */
	function get_glossar_url($url)
	{
		$data = explode("/", $url);
		$last = end($data);
		$data2 = explode("-", $last);
		$first = $data2['0'];

		//erste Bedingung - Nummer am Anfang und .html am Ende
		if (is_numeric($first) && stristr($last, ".html")) {
			$rest = str_replace($first . "-", "", $last);
			$rest = str_replace(".html", "", $rest);
			$rest = str_replace("-inlinetrue", "", $rest);

			if (!empty($this->tbname['glossar_daten'])) {
				//Dann checken ob Glossar Eintrag
				$sql = sprintf(
					"SELECT glossar_id FROM %s WHERE glossar_Wort='%s'",
					$this->tbname['glossar_daten'],
					$this->db->escape($rest)
				);
				$result = $this->db->get_results($sql, ARRAY_A);
			}

			if (!empty($result)) {
				$this->checked->glossar = $result['0']['glossar_id'];

				//Dann den Glossar Menüpunkt rausholen
				$sql = sprintf(
					"SELECT menuid_id FROM %s WHERE menulinklang LIKE '%s'",
					$this->tbname['papoo_menu_language'],
					"%glossar/templates/glossar_front.html%"
				);
				$this->checked->menuid = $this->db->get_var($sql);

				if (is_numeric($this->checked->menuid)) {
					global $template;
					$template = PAPOO_ABS_PFAD . "/plugins/" . "glossar/templates/wortdefinitionen.html";
					return $this->checked->menuid;
				}
			}
		}
		return false;
	}

	/**
	 * Erstes Vorkommen eines strings ersetzen
	 *
	 * @param string $search  Zu suchender Wert
	 * @param string $replace Zu ersetzender Wert
	 * @param string $subject Gegenstand der Ersetzung
	 *
	 * @return bool|string ersetzter string
	 */
	function str_replace_once($search, $replace, $subject)
	{
		$pos = strpos($subject, $search);
		if ($pos === false) {
			return false;
		}
		else {
			$part1 = substr($subject, 0, $pos);
			$part2 = substr($subject, $pos + strlen($search));
			return $part1 . $replace . $part2;
		}
	}

	/**
	 * Menuid erzeugen
	 */
	function get_menuid_free()
	{
		$sprechende_url = urldecode($_SERVER['REQUEST_URI']);
		//ergänzt damit auch bei free urls aufrufe wie plugin.php?menuid=xy&template=xy klappen
		$sprechende_url_org = urldecode($_SERVER['REQUEST_URI']);

		if (strpos($sprechende_url, "?")) {
			$sprechende_url = mb_substr($sprechende_url, 0, strpos($sprechende_url, "?"));
		}
		if (strpos($sprechende_url, "&")) {
			$sprechende_url = mb_substr($sprechende_url, 0, strpos($sprechende_url, "&"));
		}

		$sprechende_url = str_replace("?html2pdf_sumbit=1", "", $sprechende_url);
		$sprechende_url = str_replace("&html2pdf_sumbit=1", "", $sprechende_url);

		//Dann das UNterverzeichnis falls vorhanden
		$sprechende_url = trim(str_replace($this->webverzeichnis, "", $sprechende_url));
		// Das war hier eigentlich vorher nicht eingetragen. Bug?
		$sprechende_url_org = trim(str_replace($this->webverzeichnis, "", $sprechende_url_org));

		$this->no_404 = "";
		if (strlen($sprechende_url) > 1) {
			$this->no_404 = "OK";
		}
		//alle aktiven Sprachen holen
		$sql = "SELECT lang_short FROM {$this->tbname['papoo_name_language']} WHERE more_lang=2";
		$result = $this->db->get_results($sql, ARRAY_A);
		foreach ($result as $res) {
			if ($sprechende_url == '/' . $res['lang_short'] . '/') {
				$this->no_404 = "";
			}
		}
		if (stristr($sprechende_url, ".php")) {
			//ergänzt damit auch bei free urls aufrufe wie plugin.php?menuid=xy&template=xy klappen
			$norm1 = explode("?", $sprechende_url_org);
			if(isset($norm1['1'])) {
				$norm2 = explode("&", $norm1['1']);
			}

			IfNotSetNull($norm2);
			if (is_array($norm2)) {
				foreach ($norm2 as $key => $value) {
					$norm3 = explode("=", $value);
					if (is_array($norm3)) {
						if (!empty($norm3['0'])) {
							if (!isset($_POST[$norm3[0]])) {
								IfNotSetNull($norm3[1]);
								$this->checked->{$norm3[0]} = $this->checked->make_save_text($norm3[1]);
							}
						}
					}
				}
			}
			$norm1['0'] = str_replace("/", "", $norm1['0']);
			$this->make_template($norm1['0']);
		}

		//Dann das trailing Slash
		$sprechende_url = substr($sprechende_url, 1, (strlen($sprechende_url) - 1));

		//Das ist jetzt die sprechende url...
		$sprechende_url_org = $sprechende_url;

		$lastx = substr($sprechende_url, -1, 1);
		$last2x = substr($sprechende_url, -5, 5);
		$last3x = substr($sprechende_url, -4, 4);
		if ($lastx != "/" && $last2x != ".html" && $last3x != ".php") {
			$sprechende_url .= "/";
		}

		//Falls Forum
		if (stristr($sprechende_url, 'forumid')) {
			$sprechende_url_org = $sprechende_url;
			$dat = explode("forumid", $sprechende_url);
			$sprechende_url = trim($dat['0']);
		}

		//spezial Immo
		if (stristr($sprechende_url, '/oid-')) {
			$sprechende_url_org = $sprechende_url;
			$dat = explode("/oid-", $sprechende_url);
			$sprechende_url = trim($dat['0'] . "/");
		}

		//Falls Glossar
		$glossar_menu_id = $this->get_glossar_url($sprechende_url);

		if (
			!empty($sprechende_url)
			&& !stristr( $sprechende_url, "?" )
			&& $sprechende_url != "index.php"
			&& !stristr($sprechende_url, "keywordplugin")
			&& !is_numeric($glossar_menu_id)
			&& $last3x != ".php"
		) {
			$sprechende_url = call_user_func(
				function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower',
				$sprechende_url
			);

			$first_url = $sprechende_url[0];

			if ($first_url != "/") {
				$sprechende_url = "/" . $sprechende_url;
			}

			if (!empty($this->checked->page)) {
				$sprechende_url = trim(str_replace("page-" . $this->checked->page . ".html", "", $sprechende_url));
			}

			//Daten des Menüpunktes mit der surl raussuchen
			$sql = sprintf(
				"SELECT menuid, untermenuzu, menulink,menulinklang 
				FROM %s INNER JOIN %s ON menuid_id=menuid
				WHERE lang_id='%s' AND %s.url_menuname='%s'",
				$this->tbname['papoo_me_nu'],
				$this->tbname['papoo_menu_language'],
				$this->lang_id,
				$this->tbname['papoo_menu_language'],
				$this->db->escape($sprechende_url)
			);
			$result = $this->db->get_results($sql, ARRAY_A);

			//Nur eine, kein Prob. Übergeben
			if (count($result) == 1) {
				$this->checked->menuid = $result[0]['menuid'];
				global $template;
				if (!empty($result[0]['menulinklang'])) {
					$result[0]['menulink'] = $result[0]['menulinklang'];
				}

				//Menülink rausholen
				$this->make_template($result[0]['menulink']);
			}
			//Kein Eintrag dann 1
			// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
			IfNotSetNull($abfrage_url);

			if (count($result) == 0 && !stristr($abfrage_url, "keyword")) {
				$lastx = substr($sprechende_url_org, -1, 1);
				$last2x = substr($sprechende_url_org, -5, 5);
				if ($lastx != "/" && $last2x != ".html") {
					$sprechende_url = substr($sprechende_url, 0, -1);
					$sprechende_url_try2 = $sprechende_url . "/";
					$sprechende_url .= ".html";
				}

				//Dann schauen ob es einen passenden Artikel dazu gibt
				$this->get_reporeid($sprechende_url);

				if (empty($this->checked->reporeid)) {
					IfNotSetNull($sprechende_url_try2);
					$this->get_reporeid($sprechende_url_try2);
				}

				//Oder ein passendes Produkt
				$this->get_produktid($sprechende_url);

				//Produkt?
				if (!empty($this->checked->produkt_id)) {
					$mud = explode("/", $sprechende_url);
					$mu = end($mud);
					$sprechende_url = str_replace($mu, "", $sprechende_url);
					$this->checked->do_404 = "";

					$sql = sprintf(
						'SELECT menuid, untermenuzu, menulink,menulinklang 
						FROM %1$s INNER JOIN %2$s ON menuid_id=menuid
						WHERE (lang_id="%3$s" AND %2$s.url_menuname="%4$s")
							OR (menuid=%5$d AND lang_id=%3$d) 
						LIMIT 1',
						$this->tbname['papoo_me_nu'],
						$this->tbname['papoo_menu_language'],
						$this->lang_id,
						$this->db->escape($sprechende_url),
						$this->db->escape($this->checked->menuid_produkt)
					);
					$result = $this->db->get_results($sql, ARRAY_A);

					if (count($result) == 1 || is_numeric($this->checked->menuid_produkt)) {
						$this->checked->menuid = $result[0]['menuid'];
						global $template;
						if (!empty($result[0]['menulinklang'])) {
							$result[0]['menulink'] = $result[0]['menulinklang'];
						}

						//Menülink rausholen
						$this->make_template($result[0]['menulink']);
					}
				}

				if (
					empty($this->checked->reporeid)
					&& empty($this->checked->page)
					&& empty($this->checked->produkt_id)
					&& !empty($this->no_404)
					&& !stristr($sprechende_url_org,  "/oid-")
				) {
					$this->checked->do_404 = "not_found";
				}
			}

			//Mehr als ein Eintrag dann wirds kompliziert
			if (count($result) > 1) {
				// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
				IfNotSetNull($max);
				IfNotSetNull($var);

				$this->get_uni_menuid($result, $max, $var);

				if (!empty($result[0]['menulinklang'])) {
					$result[0]['menulink'] = $result[0]['menulinklang'];
				}
				$this->make_template($result[0]['menulink']);
			}

			if (empty($this->datei)) {
				$this->datei = "index.php";
			}

			$link = PAPOO_WEB_PFAD;
			if (!stristr($this->datei, "/")) {
				$link .= "/" . $this->datei . "?menuid=" . $this->checked->menuid;
			}
			else {
				$link = "" . $this->datei . "?menuid=" . $this->checked->menuid;
			}

			if (!empty($this->checked->reporeid)) {
				$link .= "&amp;reporeid=" . $this->checked->reporeid;
			}

			$i = 0;
			// FIXME: $this->template_lang ist eigentlich nicht gesetzt
			IfNotSetNull($this->template_lang);
			foreach ($this->content->template['languageget'] as $ld) {
				$this->content->template['languageget'][$i]['lang_link'] =
					$link . $this->template_lang . "&amp;getlang=" . $ld['lang_short'];
				$i++;
			}

			IfNotSetNull($var);
			$this->var_dat = $var;
			$max = is_array($var) ? count($var) : 0;
			$max1 = $max;

			//Menuid raussuchen
			if (stristr($sprechende_url_org, 'forumid-')) {
				//Forumid rausfischen
				$forar = explode('forumid-', $sprechende_url_org);

				$forar2 = explode('-', $forar['1']);

				//Die ID des aktuellen FOrums
				$this->checked->forumid = $forar2['0'];
				if (is_numeric($this->checked->forumid)) {
					$this->make_template("forum.php");
				}
				//Thread ID rausfischen
				if (stristr($sprechende_url_org, 'thread-')) {
					$forar = explode('thread-', $sprechende_url_org);
					$forar2 = explode('-', $forar['1']);

					//Die ID des aktuellen FOrums
					$this->checked->msgid = $forar2['0'];

					$template = "forumthread.html";
					$sql = sprintf(
						"SELECT rootid FROM %s WHERE msgid='%d'",
						$this->tbname['papoo_message'],
						$this->db->escape($this->checked->msgid)
					);
					$rootid = $this->db->get_var($sql);
					$this->checked->rootid = $rootid;
				}
			}

			if (stristr($var[$max1], '.html') or $max == 1) {
				$this->get_reporeid($var[$max1]);
			}
		}

		if (stristr($sprechende_url, "keywordplugin")) {
			$this->checked->menuid = 1;
			$sprechende_url = str_replace("keywordplugin/keywords-front/", "", $sprechende_url);
			$sprechende_url = str_replace("/", "", $sprechende_url);
			$this->checked->keyword = $sprechende_url;
		}

		// Verhindere 404-Status beim Aufruf der Startseite
		if (in_array($sprechende_url, ["/", ".html"], true)) {
			$this->checked->menuid = 1;
			unset($this->checked->do_404);
		}

		/**
		 * Hier die Einbindung für die Plugins
		 * Die können hier dann nochmal wirbeln und
		 * die urls und Daten so verbiegen wie sie wollen
		 * Gilt für alle ab Version Papoo 6.0.0
		 */
		$sql = sprintf("SELECT * FROM %s ORDER BY pluginclass_id", DB_PRAEFIX . "papoo_pluginclasses");
		if (!isset($_SESSION['dbp']['papoo_pluginclass']) || !is_array($_SESSION['dbp']['papoo_pluginclass'])) {
			$result = $this->db->get_results($sql);
			$_SESSION['dbp']['papoo_pluginclass'] = $result;
		}
		else {
			$result = $_SESSION['dbp']['papoo_pluginclass'];
		}

		if ($result) {
			foreach ($result as $klasse) {
				$pfad_plugin_file = basename($klasse->pluginclass_datei);
				$pfad_plugin = str_ireplace($pfad_plugin_file, "", $klasse->pluginclass_datei);

				// 2. die Klassen-Datei einbinden
				if (file_exists(PAPOO_ABS_PFAD . "/plugins/" . $pfad_plugin . "freeurls.php")) {
					// !!! der Pfad ist alles andere als sauber !!!
					require_once(PAPOO_ABS_PFAD . "/plugins/" . $pfad_plugin. "freeurls.php");
				}
			}
		}
	}

	/**
	 * @param $url
	 */
	function get_produktid($url)
	{
		$url_dat = explode("/", $url);
		$url = end($url_dat);
		$url = str_replace(".html", "", $url);
		if (isset($url[0]) && $url[0] == "/") {
			$url = substr($url, 1, strlen($url));
		}

		if (!empty($this->tbname['plugin_shop_produkte_lang'])) {
			$sql = sprintf(
				"SELECT produkte_lang_id FROM %s WHERE produkte_lang_produkt_surl='%s'",
				$this->tbname['plugin_shop_produkte_lang'],
				$this->db->escape($url)
			);
			$result = $this->db->get_var($sql);
			$this->checked->produkt_id = $result;

			$sql = sprintf(
				"SELECT kategorien_menu_id 
				FROM %s LEFT JOIN %s ON kategorien_id=produkte_kategorie_id
				WHERE produkte_produkt_id='%d'",
				$this->tbname['plugin_shop_lookup_kategorie_prod'],
				$this->tbname['plugin_shop_kategorien'],
				$this->db->escape($url)
			);
			$result = $this->db->get_var($sql);

			if (!$result) {
				// Fallback auf ersten Shop-Menüpunkt
				$lang_id = $this->lang_id;
				if (!$lang_id) {
					$lang_id = 1;
				}
				$sql = sprintf(
					"SELECT menuid_id 
					FROM %s
					WHERE menulinklang='shop.php' AND lang_id=%d
					ORDER BY menuid_id
					LIMIT 1",
					$this->tbname['papoo_menu_language'],
					$lang_id
				);
				$result = $this->db->get_var($sql);
			}

			$this->checked->menuid_produkt = $result;
		}
	}

	/**
	 * Menuid erzeugen
	 */
	function get_menuid()
	{
		$i = 1;

		$dolang = "";
		$this->surl_urlvar = "/" . $this->webvar_org;
		foreach ($_GET as $key => $value) {
			//Nur Übergeben wenn var drin vorkommt
			if (stristr($key, "var") && !empty($value)) {
				if (stristr($value, $this->webvar_org) and $i < 2) {
					$value = $this->str_replace_once($this->webvar_org, "", $value);
				}

				if (stristr($value, "redirect") || strlen($value) <= 2) {
					continue;
				}

				$value = call_user_func(function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower', $value);
				$value = urlencode($value);
				$value = stripslashes($value);
				$var[$i] = $value;
				if ($dolang == 1) {
					$this->checked->getlang = $value;
					$dolang = "";
				}

				if ($value == "getlang") {
					$dolang = 1;
				}
			}
			if (strlen($value) <= 2) {
				continue;
			}
			$i++;
		}

		IfNotSetNull($var);
		$this->var_dat = $var;
		if (empty($var)) {
			$var = [];
		}
		$max = count($var);
		$max1 = $max;

		if ($max >= 1 && $var['1'] != "keywordplugin") {
			//Rausfinden ob der letzte Eintrag ein Artikel ist
			if (stristr($var[$max], '.html')) {
				$max = $max - 1;
			}

			//Noch herausfinden ob es sich ums Forum handelt
			foreach ($var as $forid_dat) {
				if (stristr($forid_dat, 'forumid-') && stristr($forid_dat, 'thread-')) {
					$aktuell_forum = true;
				}
			}

			//Menuid raussuchen
			$var[$max] = $this->make_abfrageok($var[$max]);
			$sub_max = $max;
			//Kein Forum, normal...
			if ($aktuell_forum != true) {
				$abfrage_url = $var[$max];
			} //Forum, also eine / weiter vorne die Menüurl suchen
			else {
				//Aber nur wenn der darunter nicht leer ist, sonst ist es nur diie Forumsansicht
				if (!empty($var[$max - 1])) {
					$abfrage_url = $var[$max - 1];
					$sub_max = $max - 1;
				}
				else {
					$abfrage_url = $var[$max];
				}
			}
			$xy = 0;
			//Hier noch die unter dem max Punkte Überprüfen ob die ok sind
			foreach ($var as $forid_dat) {
				if ($sub_max > $xy) {
					//Daten des Menüpunktes mit der surl raussuchen
					$sql = sprintf(
						"SELECT menuid, untermenuzu, menulink,menulinklang FROM %s,%s
							  WHERE lang_id='%s' AND %s.url_menuname='%s' AND menuid_id=menuid",
						$this->tbname['papoo_me_nu'],
						$this->tbname['papoo_menu_language'],
						$this->lang_id,
						$this->tbname['papoo_menu_language'],
						$this->db->escape($forid_dat)
					);
					$result = $this->db->get_results($sql, ARRAY_A);

					if (count($result) == 0 && !stristr($forid_dat, "keywordplugin")) {
						$this->checked->do_404 = "not_found";
					}
				}
				$xy++;
			}

			//Daten des Menüpunktes mit der surl raussuchen
			$sql = sprintf(
				"SELECT menuid, untermenuzu, menulink, menulinklang 
				FROM %s INNER JOIN %s ON menuid_id=menuid
				WHERE lang_id='%s' AND %s.url_menuname='%s'",
				$this->tbname['papoo_me_nu'],
				$this->tbname['papoo_menu_language'],
				$this->lang_id,
				$this->tbname['papoo_menu_language'],
				$this->db->escape($abfrage_url)
			);
			$result = $this->db->get_results($sql, ARRAY_A);

			//Nur eine, kein Prob. Übergeben
			if (count($result) == 1) {
				$this->checked->menuid = $result[0]['menuid'];
				global $template;
				if (!empty($result[0]['menulinklang'])) {
					$result[0]['menulink'] = $result[0]['menulinklang'];
				}

				//Menülink rausholen
				$this->make_template($result[0]['menulink']);
			}
			//Kein Eintrag dann 1
			if (count($result) == 0 && !stristr($abfrage_url, "keyword")) {
				$this->checked->do_404 = "not_found";
			}
			//Mehr als ein Eintrag dann wirds kompliziert
			if (count($result) > 1) {
				$this->get_uni_menuid($result, $max, $var);

				if (!empty($result[0]['menulinklang'])) {
					$result[0]['menulink'] = $result[0]['menulinklang'];
				}
				$this->make_template($result[0]['menulink']);
			}

			//SpezialFall alte Plugins mod_rewrite
			if ($var['2'] == "menuid" or $var['2'] == "forumid") {
				$num = count($var);
				for ($i = 2; $i <= $num; $i++) {
					$this->checked->{$var[$i]} = $var[$i + 1];
					$i++;
				}
				global $template;
				$template = $this->checked->template;
				if ($var['1'] == "forum.php" || $var['1'] == "forum") {
					$template = "forum.html";
				}
				if ($var['1'] == "forumthread.php" || $var['1'] == "forumthread") {
					$template = "forumthread.html";
				}
			}
			else {
				if (empty($this->datei)) {
					$this->datei = "index.php";
				}

				$link = PAPOO_WEB_PFAD .  "/" . $this->datei . "?menuid=" . $this->checked->menuid;
			}

			foreach ($var as $forid_dat) {
				if (stristr($forid_dat, 'forumid-')) {
					//Forumid rausfischen
					$forar = explode('forumid-', $forid_dat);
					$forar2 = explode('-', $forar['1']);

					//Die ID des aktuellen FOrums
					$this->checked->forumid = $forar2['0'];
					if (is_numeric($this->checked->forumid)) {
						$this->make_template("forum.php");
					}
					//Thread ID rausfischen
					if (stristr($var[$max1], 'thread-')) {
						$forar = explode('thread-', $var[$max1]);
						$forar2 = explode('-', $forar['1']);

						//Die ID des aktuellen FOrums
						$this->checked->msgid = $forar2['0'];

						$template = "forumthread.html";
						$sql = sprintf(
							"SELECT rootid FROM %s WHERE msgid='%d'",
							$this->tbname['papoo_message'],
							$this->db->escape($this->checked->msgid)
						);
						$rootid = $this->db->get_var($sql);
						$this->checked->rootid = $rootid;
					}
				}
			}

			if (stristr($var[$max1], '.html') or $max == 1) {
				$this->get_reporeid($var[$max1]);
			}
		}
		if ($_GET['var2'] == "keywords-front") {
			$this->checked->menuid = 7;
		}
		if (!empty($this->checked->reporeid)) {
			$link .= "&reporeid=" . $this->checked->reporeid;
		}

		$i = 0;
		if (!empty($link)) {
			foreach ($this->content->template['languageget'] as $ld) {
				$this->content->template['languageget'][$i]['lang_link'] =
					$link . $this->template_lang . "&amp;getlang=" . $ld['lang_short'];
				$i++;
			}
		}
	}

	/**
	 * Template zuweisen nach Auswahl
	 *
	 * @param $tem
	 */
	function make_template($tem)
	{
		global $template;

		$this->template_lang = "";

		// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
		IfNotSetNull($plugin_pfad);

		if (stristr($tem, "plugin:")) {
			$te_p = str_replace("plugin:", "", $tem);
			$template = PAPOO_ABS_PFAD . "/plugins/" . $te_p;
			$this->datei = "plugin.php";
			$the_template = str_replace("plugin:", "", $tem);
			$the_template = $plugin_pfad . "" . $the_template;
			$the_template = "" . $the_template;
			$template_ar = explode("&", $the_template);
			$xi = 0;
			foreach ($template_ar as &$tap) {
				$tap = str_ireplace("amp;", "", $tap);
				$xi++;
			}
			unset($tap);

			if (empty($template_ar['0'])) {
				$this->checked->template = $the_template;
			}
			else {
				$this->checked->template = $template_ar['0'];
			}
			if (is_array($template_ar)) {
				foreach ($template_ar as $tap) {
					$tap_c = explode("=", $tap);
					if (!empty($tap_c)) {
						$this->checked->{$tap_c['0']} = isset($tap_c['1']) ? trim(strip_tags($tap_c['1'])): NULL ;
					}
				}
			}
			$this->template_lang = "&amp;template=" . $the_template;
			if ($this->mod_rewrite == 2) {
				$the_template = str_replace("/", ":::", $the_template);
				$the_template = urlencode($the_template);
				$this->checked->template2 = "/template/" . $the_template;
			}
		} //Normale Templates
		else {
			$templateToFile = [
				// $template => $file
				'index.php' => 'index.html', 'forum.php' => 'forum.html',
				'forumthread.php' => 'forumthread.html', 'guestbook.php' => 'guestbook.html',
				'visilex.php' => 'visilex.html', 'inhalt.php' => 'inhalt.html',
				'kontakt.php' => 'kontakt.html', 'profil.php' => 'profil.html',
				'login.php' => 'login.html', 'kurse.php' => 'kurse.html',
				'partner.php' => 'partner.html', 'mitglied.php' => 'mitglied.html',
				'mysportnavi.php' => 'mysportnavi.html', 'account.php' => 'profil.html',
				'danke.php' => 'danke.html', 'blog.php' => 'blog.html',
				'print.utf8.php' => 'print.utf8.html',
				'shop.php' => '../../plugins/papoo_shop/templates/papoo_shop_front.html',
			];
			$this->datei = $tem;
			$template = $templateToFile[$tem] ?? null;
			$this->content->template['urldatself'] = $this->datei;
		}
	}

	/**
	 * Rausfinden wohin der Menüpunkt gehört
	 *
	 * @param array  $dat
	 * @param string $max
	 * @param array  $var
	 */
	function get_uni_menuid($dat = [], $max = "", $var = [])
	{
		//einfachster Fall -> oberste Ebene
		if ($max == 1) {
			foreach ($dat as $daten) {
				//Alle menü ja unterschiedlich sein der ersten Ebene
				if ($daten['untermenuzu'] == 0) {
					$this->checked->menuid = $daten['menuid'];
				}
			}
		}
		//komplizierter, ist irgendwo...
		if ($max > 1) {
			$menid = 0;
			//Bei var1 starten und dann runterloopen
			foreach ($var as $vardat) {
				if (!stristr($vardat, '.html')) {
					$sql = sprintf(
						"SELECT menuid FROM %s,%s
					  WHERE lang_id='%s' AND %s.url_menuname='%s' AND menuid_id=menuid
					  AND untermenuzu='%s'",
						$this->tbname['papoo_me_nu'],
						$this->tbname['papoo_menu_language'],
						$this->lang_id,
						$this->tbname['papoo_menu_language'],
						$this->db->escape($vardat),
						$menid
					);
					$result = $this->db->get_var($sql);
					$menid = $result;
				}
			}
			//Der letzte gefunden Eintrag isses
			$this->checked->menuid = $menid;
		}
	}

	/**
	 * Daten wieder Datenbanktauglich machen
	 *
	 * @param string $dat
	 *
	 * @return mixed|string
	 */
	function make_abfrageok($dat = "")
	{
		return str_replace("_", " ", $dat);
	}

	/**
	 * reporeid eines Artikel raussuchen
	 *
	 * @param $header
	 */
	function get_reporeid($header)
	{
		$header = urldecode($header);
		if (preg_match("/-m-([\d]+)/", $header, $match) !== false && $this->mod_free == 1) {
			IfNotSetNull($match[0]);
			IfNotSetNull($match[1]);
			$this->checked->menuid = (int)$match[1];
			$header = str_replace($match[0], "", $header);
		}

		$header_org = $header;
		$header = urldecode($header);
		if ($this->mod_free != 1) {
			$header = str_replace('.html', '', $header);
		}

		$sql = sprintf(
			"SELECT lan_repore_id, cattextid 
			FROM %s INNER JOIN %s ON lan_repore_id=reporeID
			WHERE lang_id='%s' AND url_header='%s'",
			$this->tbname['papoo_language_article'],
			$this->tbname['papoo_repore'],
			$this->lang_id,
			$this->db->escape($header)
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		//Bei Mod free meuid rausholen
		if ($this->mod_free == 1 && empty($this->checked->menuid)) {
			//menuid aus url bei Mehrfachzuweisun

			//menuid aus DB bei Einem Ergebnis
			if (count($result) == 1) {
				//ID aus DB
				$sql = sprintf(
					"SELECT lcat_id FROM %s WHERE lart_id='%d'",
					$this->tbname['papoo_lookup_art_cat'],
					$result[0]['lan_repore_id']
				);
				$this->checked->menuid = $this->db->get_var($sql);
			}
		}

		//Nur eine, kein Prob. Übergeben
		global $template;

		if (count($result) == 1) {
			$this->checked->reporeid = $result[0]['lan_repore_id'];
		}

		//Kein Eintrag dann 1
		if (count($result) == 0 && $template == "index.html" && stristr($header_org, ".html")
			&& !stristr(
				$header_org,
				"page-"
			)
			&& empty($this->no_404)) {
			$this->checked->reporeid = "";
			$this->checked->do_404 = "not_found";
		}
		//Mehr als ein Eintrag dann wirds kompliziert
		if (count($result) > 1) {
			$this->get_uni_reporeid($result);
		}
	}

	/**
	 * Liste der Artikel anzeigen
	 *
	 * @param array  $result
	 */
	function get_uni_reporeid($result = [])
	{
		foreach ($result as $art) {
			if ($art['cattextid'] == $this->checked->menuid) {
				if (!empty($this->checked->reporeid)) {
					continue;
				}
				$this->checked->reporeid = $art['lan_repore_id'];
			}
		}
	}
}

$cms = new cms();
