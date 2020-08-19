<?php
/**
 * #####################################
 * # CMS PAPOO                         #
 * # (c) Dr. Carsten Euwens 2018       #
 * # Authors: Dr. Carsten Euwens       #
 * # http://www.papoo.de               #
 * #                                   #
 * #####################################
 * # PHP Version >=7.x                 #
 * #####################################
 */

//Laufzeitberechnung
$start = microtime(true);

//Bei Direktaufruf abbrechen
if (strstr( $_SERVER['PHP_SELF'],'all_inc_front.php')) die('You are not allowed to see this page directly');

//Notwendige Dateien einbinden
require_once "./lib/redirect.php";
require_once "./lib/settings/ini_set.php";
require_once "./lib/includes.inc.php";

// Template Engine initialisieren
$smarty = new Smarty;

//CMS initialisieren
global $cms;

//Stylehooks einbinden
require_once "./lib/settings/style_hook.php";
run_style_hook('libs_included', array(&$content));

//Smarty Variablen setzen
$smarty->template_dir = PAPOO_ABS_PFAD.$temp_style_dir;
$smarty->compile_dir = PAPOO_ABS_PFAD."/templates_c/";

//Jetzt Content Variablen ersetzen
require_once "./lib/settings/content_vars.php";

//Plugin Smartytags - Sonderfall
if ($cms->tbname['papoo_smarty_user_plugins']) {
	require_once(PAPOO_ABS_PFAD."/plugins/Smarty_tags/includes/includes.php");
}

// Standard Handler definieren - zu finden in function.php
$smarty->default_template_handler_func = 'make_template';

if (!is_dir(PAPOO_ABS_PFAD."/templates_c/")) {
	@mkdir(PAPOO_ABS_PFAD."/templates_c/", 0777);
	chmod(PAPOO_ABS_PFAD."/templates_c/", 0777);
}

// sicherer als bloser check auf ".html"
if (basename($template, ".html") . ".html" != basename($template)) $template = "index.html";

//Je nach Template aufrufen
switch ($template)
{
	// Startseite
case "index.html":
	// Inhalt anzeigen
	$artikel->make_inhalt();
	break;

	// Forum
case "forum.html":
	// Inhalt anzeigen
	$forum->make_forum();
	break;

case "forumthread.html":
	// Inhalt anzeigen
	$forum->make_forum_thread();
	break;

case "guestbook.html":
	// Inhalt anzeigen
	$artikel->make_inhalt();
	break;

case "inhalt.html":
	// Inhalt anzeigen
	$content->template['inhalt_t'] = "ok";
	$sitemap->make_sitemap();
	break;

case "kontakt.html":
	// Inhalt anzeigen
	$content->template['kontakt_t'] = "ok";
	$kontaktform->do_form();
	break;

case "profil.html":
	// Inhalt anzeigen
	$user->do_account();
	break;

case "login.html":
	// Inhalt anzeigen
	$user->do_account();
	break;

	// Drucken
case "print.utf8.html":
	// Inhalt anzeigen
	$artikel->make_print();
	break;
}

// Im Header Text-Codierung als UTF-8 versenden, da es sonst auf manchen Servern Probleme gibt.
header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT', true, 200);
header('Content-Type: text/html; charset=utf-8');

// post_papoo-Funktionen der Plugin-Klassen ausführen
$pluginintegrator->post_papoo();

// Content an die Template-Engine Smarty zuweisen.
run_style_hook('pre_smarty', array(&$content));

//Fix für die Sprachlinks durchlaufen lassen - hat historische Gründe...
$diverse->fix_language_links();

//Daten an Template übergeben
$content->assign();

if(!isset($checked->no_output)) {
	$checked->no_output = NULL;
}

// Error reporting für smarty senken, weil es pseudofehler sind
$error_reporting = error_reporting();
error_reporting($error_reporting & ~E_NOTICE & ~E_STRICT);

// templates parsen
if ($checked->no_output != "no") {
	if ($diverse->no_output != "no") {
		$output = $smarty->fetch("__index.html");
	}
	else {
		$output = $smarty->fetch($template);
	}
}
else {
	$output = $smarty->fetch($template);
}
error_reporting($error_reporting);

// Besucher zählen,
$counter->count();

if ($cms->system_config_data['config_adresse_der_404_seite']==$_SERVER['REQUEST_URI']) {
	//header( "HTTP/1.1 404 Not Found" );
}

if(($mem_exhausted = memory_exhausted()) !== false) {
	$content->template['memlimit'] = $mem_exhausted;
}

//Runtime...
$content->template['script_run_time']=microtime(true)-$start;

//Alles was das output nochmal parsed...
require_once "./lib/settings/output_pars.php";

//Ausgabe
print $output;

//und raus...
exit();
