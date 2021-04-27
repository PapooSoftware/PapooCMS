<?php
/**
#####################################
# CMS Papoo                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.3                  #
#####################################
 */

@error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set("Europe/Berlin");
$start= microtime();
if (function_exists("memory_get_usage")) {
	$mem_start = memory_get_usage();
}
$memlim = ini_get("memory_limit");
$memlim=str_replace("M","",$memlim);
if ($memlim<128) {
	@ini_set("memory_limit", "1280M");
}

@ini_set("memory_limit", "12800M");
@ini_set("arg_separator.output", "&amp;");
@ini_set("register_globals","0");
@ini_set("file_uploads","On");
//@ini_set("error_reporting",E_ALL);
@ini_set("display_errors", "On");
@ini_set("display_startup_errors", "On");
ini_set("max_input_vars","2000");
//date_default_timezone_set("Europe/Berlin");
// Und eine Konstante setzen
define('admin',"admin");
$aria="standard/";

// Alle benötigten Dateien einbinden
require_once "../lib/includes.inc.php";

if (defined("PAPOO_API_CALL")) {
	require_once "../lib/classes/PapooAPI.php";
	$api = new PapooAPI();
	$api->processRequest();
	$api->sendResponse();
	exit;
}

//redirect wenn einer eingetragen
require_once "redirect.php";

// Template Engine initialisieren
$smarty= new Smarty;
$smarty->template_dir = "./templates/".$aria;
$smarty->compile_dir = "./templates_c/".$aria;

// CSS setzen
//$seite=$cms->make_css();

// Userdaten Überprüfen
$user->check_user();

if (basename($template,".html").".html" != basename($template)) {
	$template = "index.html";
} // sicherer als bloser Test auf ".html"

/*
* Je nach ausgewählten Template entsprechend die Klasse einbinden
* und den Inhalt generieren
*/

//interne Startseite
if ($template=="indexint.html") {
	// Inhalt anzeigen
	$intern_home->make_inhalt();
}

// Artikel Verwaltung
if ($template=="artikel.html") {
	// Inhalt anzeigen
	$intern_artikel->make_inhalt();
}

// Menu Verwaltung
if ($template=="menu.html") {
	// Inhalt anzeigen
	$intern_menu->make_inhalt();
}

// Link Verwaltung
if ($template=="link.html") {
	// Inhalt anzeigen
	$intern_link->make_inhalt();
}

// Styles Verwaltung
if ($template=="styles.html") {
	// Inhalt anzeigen
	$intern_styles->make_inhalt();
}

// Forum Verwaltung
if ($template=="forum.html") {
	// Inhalt anzeigen
	$intern_forum->make_inhalt();
}

// Bilder  Verwaltung
if ($template=="image.html") {
	// Inhalt anzeigen
	$intern_image->make_inhalt();
}

// Stammdaten  Verwaltung
if ($template=="stamm.html" or $template=="update.html") {
	// Inhalt anzeigen
	$intern_stamm->make_inhalt();
}

// Upload Dateien  Verwaltung
if ($template=="upload.html") {
	// Inhalt anzeigen
	$intern_upload->make_inhalt();
}

// User  Verwaltung
if ($template=="user.html") {
	// Inhalt anzeigen
	$intern_user->make_inhalt();
}

// 3. Spalte  Verwaltung
if ($template=="spalte.html") {
	// Inhalt anzeigen
	$intern_spalte->make_inhalt();
}

// Auszeichnungen  Verwaltung
if ($template=="span.html") {
	// Inhalt anzeigen
	$intern_span->make_inhalt();
}

// Auszeichnungen  Verwaltung
if ($template=="ordner.html") {
	// Inhalt anzeigen
	$intern_ordner->make_inhalt();
}

// Auszeichnungen  Verwaltung
if ($template=="plugins.html") {
	// Inhalt anzeigen
	$plugin->make_plugin();
}

// Bilder Liste
if ($template=="image_list.html") {
	// Inhalt anzeigen
	// FIXME: Funktion existiert nicht
	do_list_image();
}
//Inhalt
if ($template=="content.html") {
	// Inhalt anzeigen
	$content_data->do_inhalt();
}

if ($template=="kategorie.html") {
	// Inhalt anzeigen
	$cat_class->do_cat();
}

if ($template=="kontaktform.html") {
	// Inhalt anzeigen
	// Kontaktformular Klasse einbinden
	require_once(PAPOO_ABS_PFAD."/lib/classes/kontaktform_class.php");
	$kontaktform = new kontaktform();
	$kontaktform->do_form();
}

if ($template=="video.html") {
	// Inhalt anzeigen
	$video_class->do_video();
}

// Linkliste
if ($template=="link_list.html") {
	// Inhalt anzeigen
	// FIXME: Funktion existiert nicht
	do_list_link();
}

// Medienliste
if ($template=="media_list.html") {
	// Inhalt anzeigen
	do_list_medien();
}

if ($template=="template_list.html") {
	// Inhalt anzeigen
	do_list_templates();
}

if ($template=="template_one.html") {
	// Inhalt anzeigen
	do_one_template();
}

// Blacklist
if ($template=="blacklist.html") {
	// Inhalt anzeigen
	$blacklist->do_admin();
}

// Blacklist
if ($template=="logfiles.html") {
	// Inhalt anzeigen
	$logfiles->do_admin();
}

if ($template=="template.html") {
	// Inhalt anzeigen
	$ctemplate->do_admin();
}

//XP erkennung
if (stristr($_SERVER['HTTP_USER_AGENT'],'NT 5.1') || stristr($_SERVER['HTTP_USER_AGENT'],"NT 5.10")) {
	$content->template['xpcss']='<link rel="stylesheet" type="text/css" href="css/metro-bootstrap_papoo_xp.css" />';
}

// post_papoo-Funktionen der Plugin-Klassen ausführen
$pluginintegrator->post_papoo();

$memlim = ini_get('memory_limit');
if (function_exists("memory_get_usage") && (int)$memlim > 0) {
	$akmem=round((memory_get_usage()) / 1048576, 3);

	// SI-Suffix umrechnen
	$memlim = trim($memlim);
	$suffix = strtolower($memlim[strlen($memlim)-1]);
	$memlim = (int)substr($memlim, 0, -1);
	switch($suffix) {
	case 'g':
		$memlim *= 1024;
	case 'm':
		$memlim *= 1024;
	case 'k':
		$memlim *= 1024;
	}
	// Wieder in MB umrechnen
	$memlim /= 1048576;

	$content->template['memlimit'] = ($memlim - $akmem < 1 ? $memlim - $akmem : NULL);
}

IfNotSetNull($diverse->error);
IfNotSetNull($content->template['xpcss']);
IfNotSetNull($content->template['inhalte']);
IfNotSetNull($content->template['system']);
IfNotSetNull($content->template['medien']);
IfNotSetNull($content->template['gruppen']);
IfNotSetNull($content->template['layout']);
IfNotSetNull($content->template['sprung_menu']); // oben.inc.html
IfNotSetNull($content->template['extra_key']);
IfNotSetNull($content->template['writemail']);
IfNotSetNull($content->template['mailen']);
IfNotSetNull($content->template['artikel_alle']);
IfNotSetNull($content->template['artikel_offen']);
IfNotSetNull($content->template['persdaten']);
IfNotSetNull($content->template['loggedin_false']);
IfNotSetNull($content->template['menuid_aktuell']);
IfNotSetNull($content->template['username']);
IfNotSetNull($content->template['password']);
IfNotSetNull($content->template['sperre']);

$content->template['IS_ADMIN']="OK";
$content->template['error'] = $diverse->error;
// Content an die Template-Engine Smarty zuweisen.
$content->assign();

// Error reporting für smarty senken, weil es pseudofehler sind
$error_reporting = error_reporting();
error_reporting($error_reporting & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// templates parsen
$output = $smarty->fetch($template);

error_reporting($error_reporting);

#$cp=utf8_encode("�");
#$output=str_replace("</body>",'<div class="footer">'.$cp.' � Papoo Software 2009 - <a href="http://www.papoo.de">www.papoo.de</a></div></body>',$output);

// Im Header Text-Codierung als UTF-8 versenden, da es sonst auf manchen Servern Probleme gibt.
//if (($template!="link_list.html" and $template!="image_list.html"))
#{
$contentTypeSent = array_reduce(headers_list(), function ($sent, $header) {
	return $sent || (bool)preg_match('~^Content-Type:~i', trim($header));
}, false);

if (!$contentTypeSent) {
	header('Content-Type: text/html; charset=utf-8');
}
#}
// Ausgabe

//Outputfilter die man noch Über die Ausgabe jagen kann initialisieren


//Bei Metro ein paar CSS Klassen setzen
if ($aria=="standard/") {
	$output=str_ireplace("submit_back_red_big_white"," btn btn-danger ",$output);
	$output=str_ireplace("submit_back_red_white"," btn btn-danger ",$output);
	$output=str_ireplace("submit_back_alert"," btn btn-danger ",$output);
	//submit_back_xxl_white

	$output=str_ireplace("submit_back_red"," btn btn-danger ",$output);

	$output=str_ireplace("submit_back_green_big_white"," btn btn-info ",$output);

	$output=str_ireplace("submit_back_green_big"," btn btn-info ",$output);
	$output=str_ireplace("submit_back_green_white"," btn btn-success ",$output);
	$output=str_ireplace("submit_back_green"," btn btn-info ",$output);
	$output=str_ireplace("submit_back_white"," btn btn-info ",$output);
	$output=str_ireplace("submit_back_xxl_white"," btn btn-info ",$output);
	$output=str_ireplace("submit_back_xxl"," btn btn-info ",$output);
	$output=str_ireplace("submit_back_big_white"," btn btn-info ",$output);
	$output=str_ireplace("submit_back_big"," btn btn-success ",$output);
	$output=str_ireplace("submit_back_xl_white"," btn btn-info ",$output);
	#submit_back
	$output=str_ireplace("submit_back_del"," btn btn-danger ",$output);
	$output=str_ireplace("submit_back"," btn btn-info ",$output);
	$output=str_ireplace('class="aktu_warenkorb','class="aktu_warenkorb btn btn-info ',$output);
	//submit_back_alert aktu_warenkorb
	// style="width:50%"
	$output=str_ireplace('style="width:50%"','style="width:80%"',$output);
	//class="outside  table table-striped table-hover"
	//<input type="submit" name="mv_
	//border="1"
	//<input type="submit" name="submitorder
	#$output=str_ireplace('border="1"','border="0"',$output);
	$output=str_ireplace('input type="submit" name="submitorder','input type="submit"  class="btn btn-info"  name="submitorder',$output);
	$output=str_ireplace('input type="submit" name="mv_','input type="submit" class="btn btn-info " name="mv_',$output);
	if (!stristr($output,"<textarea")) {
		$output=str_ireplace("<table",'<table class="outside  table table-striped table-hover"',$output);
	}
}
$pluginintegrator->output_filter_admin();

$output = $diverse->injectCsrfTokenIntoForms((string)$output);

print $output;
