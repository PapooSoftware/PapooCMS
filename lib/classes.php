<?php
/**
#####################################
# CMS Papoo                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.2                  #
#####################################
 */

// Klasse und URL-Überprüfungsfunktion einbinden
if (strstr( $_SERVER['PHP_SELF'],'classes.php')) die('You are not allowed to see this page directly');

// Third-Party-Klassen einbinden
require_once(__DIR__ . "/classes/third-party/simple_html_dom.php");
require_once(__DIR__ . "/classes/third-party/Browser.php");
// Third-Party Libaries per Composer Autoload laden
require_once __DIR__ . '/classes/third-party/composer/autoload.php';

if (phpversion() >= 5.3) {
	//Klasse ImageWorkshop zu Bildbearbeitung
	require_once(__DIR__ . "/classes/third-party/PHPImageWorkshop/ImageWorkshop.php");
}

// Hier werden eine ganze Reihe von Klassen includiert die je nachdem benötigt werden.
require_once(PAPOO_ABS_PFAD."/lib/classes/class_debug.php");

// Session Klasse zur Einbindung der Sessionvariablen
require_once(PAPOO_ABS_PFAD."/lib/classes/session_class.php");

// Variablen einbinden von außen
require_once(PAPOO_ABS_PFAD."/lib/classes/variables_class.php");

// Inhalt Klasse
require_once(PAPOO_ABS_PFAD."/lib/classes/content_class.php");

// cURL-Klasse
require_once(PAPOO_ABS_PFAD."/lib/classes/curl_class.php");

// diverse Methoden Klasse,
require_once(PAPOO_ABS_PFAD."/lib/classes/diverse_class.php");

// Session Klasse zur Einbindung der Sessionvariablen
require_once(PAPOO_ABS_PFAD."/lib/classes/db_abstract_class.php");

// CMS Klasse, alle Eigenschaften des CMS
require_once(PAPOO_ABS_PFAD."/lib/classes/cms_class.php");

// XMLParser-Klasse einbinden
require_once(PAPOO_ABS_PFAD."/lib/classes/xmlparser_class.php");

if (defined('admin')) {
	//User Klasse, alle Eigenschaften der User
	require_once(PAPOO_ABS_PFAD."/lib/classes/user_class.php");
}

// Module-Klasse
require_once(PAPOO_ABS_PFAD."/lib/classes/module_class.php");

// User Klasse, alle Eigenschaften der User
require_once(PAPOO_ABS_PFAD."/lib/classes/user_class.php");

// menu-Klasse, alle Eigenschaften des Menüs
require_once(PAPOO_ABS_PFAD."/lib/classes/menu_class.php");

// HTML Validierungsklasse
require_once(PAPOO_ABS_PFAD."/lib/classes/html_valid_class.php");

// Replacement für Acronyme, Links und Sprachauszeichnungen
require_once(PAPOO_ABS_PFAD."/lib/classes/replace_class.php");

// weitere Seiten Klasse einbinden
require_once(PAPOO_ABS_PFAD."/lib/classes/weiter_class.php");

// Artikel Klasse
require_once(PAPOO_ABS_PFAD."/lib/classes/artikel_class.php");

// Sitemap Klasse
require_once(PAPOO_ABS_PFAD."/lib/classes/sitemap_class.php");

// Counter Klasse
require_once(PAPOO_ABS_PFAD."/lib/classes/counter_class.php");

// Messages Klasse
require_once(PAPOO_ABS_PFAD."/lib/classes/message_class.php");

// class_file_upload.php einbinden
require_once(PAPOO_ABS_PFAD."/lib/class_file_upload.php");

// Blacklist Klasse einbinden
require_once(PAPOO_ABS_PFAD."/lib/classes/blacklist_class.php");

// menutop-Klasse
require_once(PAPOO_ABS_PFAD."/lib/classes/menutop_class.php");

// Template
require_once(PAPOO_ABS_PFAD."/lib/classes/template_class.php");

// PluginsCSS-Klasse einbinden
require_once(PAPOO_ABS_PFAD."/lib/classes/pluginscss_class.php");
/* /--------------------------------------------------------------------------------------------\
*  |Klassen nur einbinden wenn man sich NICHT im Interna Bereich befindet                       |
*  \--------------------------------------------------------------------------------------------/
*/

if (!defined("admin")) {
	//Forum Klasse
	require_once(PAPOO_ABS_PFAD."/lib/classes/forum_class.php");

	// Umwandlung von bbcode in HTML
	require_once(PAPOO_ABS_PFAD."/lib/bbcode.inc.php");

	// Download-Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/download_class.php");

	// Die Klasse für die 3. Spalte
	require_once(PAPOO_ABS_PFAD."/lib/classes/collum_class.php");

	// Spamschutz-Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/spamschutz_class.php");

	// Kontaktformular Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/kontaktform_class.php");

	// Such Klasse
	require_once(PAPOO_ABS_PFAD."/lib/classes/search_class.php");
}

// Mail Klasse einbinden
require_once(PAPOO_ABS_PFAD."/lib/classes/class.phpmailer.php");
require_once(PAPOO_ABS_PFAD."/lib/classes/mail_it_class.php");

/* /--------------------------------------------------------------------------------------------\
*  |interne Klassen nur einbinden wenn man sich im Interna Bereich befindet                     |
*  \--------------------------------------------------------------------------------------------/
*/
if (defined("admin")) {
	// Blacklist Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/logfiles_class.php");

	// interne Startseite Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_home_class.php");

	// Bookmark Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_bookmark_class.php");

	// Dump'nRestore-Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/dumpnrestore_class.php");

	// interne Forum Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_forum_class.php");

	// interne content Verteil Klasse für Hauptmenü
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_content_data.php");

	// interne User Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_user_class.php");

	// interne Upload Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_upload_class.php");

	// interne span Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_span_class.php");

	// interne Link Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_link_class.php");

	// interne Styles Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_styles_class.php");

	// interne Stamm Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_stamm_class.php");

	// interne Menueerstellungs Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_menu_class.php");

	// interne Artikel Bearbeitungs Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_artikel_class.php");

	// interne 3. Spalte Bearbeitungs Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_spalte_class.php");

	// interne Plugin Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_plugin_class.php");

	// interne Plugin Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_hilfe_class.php");

	// interne menuintcss Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/intern_menuintcss_class.php");

	// interne Kategorien Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/cat_class.php");

	// interne Video Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/video_class.php");
}

//interne Plugin Klasse einbinden
require_once(PAPOO_ABS_PFAD."/lib/classes/intern_ordner_class.php");

require_once(PAPOO_ABS_PFAD."/lib/classes/body_class.php");

//interne Image Bearbeitungs Klasse(n) einbinden
require_once(PAPOO_ABS_PFAD."/lib/classes/image_core_class.php");
require_once(PAPOO_ABS_PFAD."/lib/classes/intern_image_class.php");

// Plugin-Integrator-Klasse einbinden
// muss ganz am Ende eingebunden werden, da sonst nicht alle Klassen von den Plugins genutzt werden können
require_once(PAPOO_ABS_PFAD."/lib/classes/pluginintegrator_class.php");