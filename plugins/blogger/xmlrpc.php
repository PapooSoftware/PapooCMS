<?php
include ("lib/trackback_cls.php");

// $trackback = new Trackback('name des blogs bzw der applikation','autor','UTF-8');
//
// echo $trackback->ping('http://www.lojatiff.de/steuern/spezielle-informationen-zu-amtsgerichten-in-deutschlandrechtslupe-de-bietet-zusammenfassende-neuigkeiten-aus-der-welt-der-deutschen-judikatur-auf/trackback/',
// 'http://localhost/papoo_trunk/index.php?menuid=1', 'titel des beitrages','textauschnitt des beitrages');
//
// exit();

require_once('../../lib/site_conf.php');
require_once(PAPOO_ABS_PFAD.'/lib/ez_sql.php');
require_once(PAPOO_ABS_PFAD.'/lib/classes/variables_class.php');
$db->query("SET NAMES 'utf8'");
$db->query("SET sql_mode=''");

//die antwort f�r den Trackback sender wird als XML ausgegeben
header('Content-Type: text/xml');

//Einbinden der php trackback Klasse
include('lib/trackback_cls.php');

// //Neues trackback Object instanzieren
// $trackback = new Trackback('openKE.org','openKE.org','UTF-8');
//
//die Trackback Parameter aus den GET bzw POST Variablen des Requests auslesen
if (!empty($checked->id)) {
	$tb_id = $checked->id;
}

if (!empty($checked->url)) {
	$tb_url = $checked->url;
}

if (!empty($checked->title)) {
	$tb_title = $checked->title;
}

if (!empty($checked->exerpt)) {
	$tb_expert = $checked->exerpt;
}

//
// /**
//  * $SqlInsert  = 'INSERT INTO restrackbacks (trid, timestamp, bid, title, text, fromurl) '.
// /              "VALUES (' ', now(), '".$tb_id."', '".$tb_title."', '"
// 			   .$tb_expert."', '"
// 			   .$tb_url."')";
//
// $result = mysql_query($SqlInsert);
// */
// #if (!$result) {    die('Invalid query: ' . mysql_error()); };
//
// //XML Antwort f�r den Trackback Sender generierten und zur�ck liefern
// echo $trackback->recieve(true);
