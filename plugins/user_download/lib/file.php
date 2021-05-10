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
error_reporting(E_ALL);

// Datenbank Verbindung erstellen
foreach (['site_conf.php', "ez_sql.php"] as $libFile) {
	$file = '../../../lib/' . $libFile;
	if (is_file($file)) {
		include_once $file;
	}
}
// Verwende die gleiche Session wie Papoo -> möglich, da in einem Unterverzeichnis der gleichen Domain
session_name('papoo_session_'.substr(md5(PAPOO_ABS_PFAD), 0, 4));
// Session-Lebensdauer
$cookie_lifetime = 432000;
// Cookie-Pfad aus PAPOO_WEB_PFAD bestimmen
$cookie_base_path = str_replace('//', '/', PAPOO_WEB_PFAD.'/');
// Cookie-Domain setzen, Cookie mit und ohne www. gültig machen. Dabei ebenfalls lokale Installationen beachten.
$cookie_domain = str_replace('.www.', '.', (strpos($_SERVER['SERVER_NAME'], '.') === false ? '' : '.'.$_SERVER['SERVER_NAME']));

if (stristr($_SERVER['REQUEST_URI'],"https")) {
	// Nur über HTTPS senden. Wenn Website nur HTTPS nutzt anschalten!
	$cookie_secure = FALSE;
}
else {
	// Nur über HTTPS senden. Wenn Website nur HTTPS nutzt anschalten!
	$cookie_secure = FALSE;
}

// Zugriff via Javascript verweigern
$cookie_httponly = TRUE;
session_set_cookie_params($cookie_lifetime, $cookie_base_path, $cookie_domain, $cookie_secure, $cookie_httponly);
session_start();
//print_r($_SESSION);
if (empty($_GET['fid'])) {
	die("No File ID given<br />Usage: file.php?fid=&lt;fileid&gt;");
}

//Anti SQL Injections
$fid = $db->escape($_GET['fid']);
$uid = $db->escape($_SESSION['sessionuserid']);
$db->scrfok=true;
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
$db->scrfok=false;
?>
