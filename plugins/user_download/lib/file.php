<?php
/**
#####################################
# CMS Papoo Plugin User Download    #
# (c) Philipp Schaefer 2012         #
# Authors: Philipp Schaefer         #
# http://www.papoo.de               #
# UserDownload Plugin -> Fileproxy  #
#####################################
# PHP Version >4.2                  #
#####################################
 **/

// Fehler bzw. warnings könnten den Download zerstören (HEADER), daher alles ausschalten
error_reporting(0);

// Datenbank Verbindung erstellen
foreach (['site_conf.php', "ez_sql.php"] as $libFile) {
	$file = '../../../lib/' . $libFile;
	if (is_file($file)) {
		include_once $file;
	}
}

// Verwende die gleiche Session wie Papoo -> möglich, da in einem Unterverzeichnis der gleichen Domain
session_start();

if (empty($_GET['fid'])) {
	die("No File ID given<br />Usage: file.php?fid=&lt;fileid&gt;");
}

//Anti SQL Injections
$fid = $db->escape($_GET['fid']);
$uid = $db->escape($_SESSION['sessionuserid']);

if (empty($uid)) {
	die("You are not logged in");
}

//Überprüfen ob der User auf die ID zugreifen darf.
$queryUserFile = sprintf("SELECT count(*) FROM %s WHERE userid=%d AND fileid=%d",
	$db_praefix . "plugin_user_download_lookup_uf",
	$uid,
	$fid
);
$resultUserFile = $db->get_results($queryUserFile, ARRAY_A);

//Wenn die Anzahl = 0 ist dann gibt es keinen Eintrag->kein Zugriff
if (!$resultUserFile || $resultUserFile[0]["count(*)"] == "0") {
	die("You have no permission to view this file");
}
else {
	$queryFileDownload = sprintf("SELECT filename, path, size FROM %s WHERE id=%d",
		$db_praefix . "plugin_user_download_files",
		$fid
	);
	$file = $db->get_results($queryFileDownload, ARRAY_A);

	try {
		//Header für einen binary Download setzen
		header('Content-type: application/octet-stream');
		//Wichtig! Dateiname übertragen, sonst heisst die Datei nachher file.php
		header('Content-Disposition: attachment; filename="' . $file[0]['filename'] . '"');
		//Größe übertragen -> vorallem sinnvoll für größere Dateien, so kann man den Downloadfortschritt besser beurteilen
		header('Content-Length: ' . $file[0]['size']);
		header("Content-Transfer-Encoding: binary\n");
		//Datei auslesen und direkt in den Output buffer schreiben
		readfile(addslashes(utf8_encode($file[0]['path'])));
	}
	catch(Exception $e) {
		echo "An unexpected error occured";
		var_dump($e);
	}
}
