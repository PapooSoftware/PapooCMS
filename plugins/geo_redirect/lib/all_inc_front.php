<?php
/**
 * #####################################
 * # CMS PAPOO                         #
 * # (c) Dr. Carsten Euwens 2009       #
 * # Authors: Dr. Carsten Euwens       #
 * # http://www.papoo.de               #
 * #                                   #
 * #####################################
 * # PHP Version >=5.x                 #
 * #####################################
 */

if (strstr($_SERVER['REQUEST_URI'],'ibrowser')) {
	die('');
}
if (strstr( $_SERVER['PHP_SELF'],'all_inc_front.php')) {
	die('You are not allowed to see this page directly');
}
if (function_exists("memory_get_usage")) $mem_start = memory_get_usage();
$memlim = ini_get("memory_limit");
$memlim = str_replace("M", "", $memlim);
if ($memlim < 128) {
	@ini_set("memory_limit", "128M");
}

@ini_set('url_rewriter.tags', '');
@ini_set("register_globals", "0");
@ini_set("arg_separator.output", "&amp;");
@ini_set("url_rewriter.tags", "");
//date_default_timezone_set("Europe/Berlin");
$start = microtime();
// Alle ben�tigten Dateien einbinden
require_once "./lib/includes.inc.php";
// echo microtime()-$start."--";

if ($_GET['mv_content_id']==19) {
	do_redirect("http://www.topro.de");
}
/**
 * @param $uri
 */
function do_redirect($uri)
{
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ".$uri);
	header("Connection: close");
	exit();die();
}

// Template Engine initialisieren

$smarty = new Smarty;

global $cms;
$temp_style_dir = "";
if (!empty($cms->style_dir)) {
	$smarty->compile_id = $cms->style_dir;
	$temp_style_dir = "/styles/".$cms->style_dir."/templates/";
}

$smarty->template_dir = PAPOO_ABS_PFAD.$temp_style_dir;
$smarty->compile_dir = PAPOO_ABS_PFAD."/templates_c/";

if ($cms->tbname['papoo_smarty_user_plugins']) {
	require_once(PAPOO_ABS_PFAD."/plugins/Smarty_tags/includes/includes.php");
}

/**
 * @param $resource_type
 * @param $resource_name
 * @param $template_source
 * @param $template_timestamp
 * @param $smarty_obj
 * @return bool
 */
function make_template ($resource_type, $resource_name, &$template_source, &$template_timestamp, &$smarty_obj)
{
	if( $resource_type == 'file' ) {
		$temp_template = PAPOO_ABS_PFAD."/styles_default/templates/".$resource_name;

		if (file_exists($temp_template)) {
			$smarty_obj->display($temp_template);
			return true;
		}
		else {
			//$smarty_obj->trigger_error('unable to read resource: "'.$resource_name.'"');
			return false;
		}
	}
	else {
		// keine Datei
		return false;
	}
}

// Standard Handler definieren
$smarty->default_template_handler_func = 'make_template';

if (!is_dir(PAPOO_ABS_PFAD."/templates_c/")) {
	@mkdir(PAPOO_ABS_PFAD."/templates_c/", 0777);
	chmod(PAPOO_ABS_PFAD."/templates_c/", 0777);
}

// echo microtime()-$start."--";
if (basename($template, ".html") . ".html" != basename($template)) {
	$template =  "index.html";
} // sicherer als bloser pkalender auf ".html"

switch ($template) {
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

// post_papoo-Funktionen der Plugin-Klassen ausf�hren
$pluginintegrator->post_papoo();

if (strstr($_SERVER['REQUEST_URI'],'plugin.php')) {
	$content->template['canonical']= $_SERVER['REQUEST_URI'];
}
$content->template['aktiv_canonical']=false;
$content->template['referrer'] = $_SERVER['HTTP_REFERER'];
// Content an die Template-Engine Smarty zuweisen.
$content->assign();

// templates parsen
if ($diverse->no_output != "no") {
	$output = $smarty->fetch("__index.html");
}
else {
	$output = $smarty->fetch($template);
}

if ($checked->print == "ok") {

	$output = $smarty->fetch("print.utf8.html");
	$output = $replace->do_print($output);
}

// und tidy machen
if (empty($notidy)) {
	$output = $html->make_tidy_front($output);
}
// Besucher z�hlen,
$counter->count();
// $output=preg_replace('/\> (\.|\|\,|\;|\!|\?|:)/','>$1',$output);
// Im Header Text-Codierung als UTF-8 versenden, da es sonst auf manchen Servern Probleme gibt.
header('Content-Type: text/html; charset=utf-8');

// ENDE edited by b.legt

if (function_exists("memory_get_usage")) {
	$akmem=round((memory_get_usage()) / 1048576, 3);
	if ($memlim - $akmem < 1) {
		$content->template['memlimit'] = $memlim - $akmem;
	}
}
//Outputfilter die man noch �ber die Ausgabe jagen kann initialisieren
$pluginintegrator->output_filter();
$output=$diverse->do_pfadeanpassen($output);
// pkalender-Einbindung Cache-Klasse
$cache->cache_speichern();

print $output;
