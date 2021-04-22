<?php

// Config für Datenbank Verbindung rausholen
foreach (['site_conf.php', 'classes/curl_class.php'] as $libFile) {
	$file = '../../../lib/' . $libFile;
	if (is_file($file)) {
		include_once $file;
	}
}
// Datenbank einbauen
$db = new MySQLi($db_host, $db_user, $db_pw, $db_name);

// Nehmen der CSS Liste
$query = "SELECT *, `cssText` as `csstext` FROM " . $db_praefix . "plugin_kontrastwechsler_css";
$cssListe = mysqli_fetch_assoc($db->query($query));

// Nehmen der Kontrast ID über checked Klasse.
$kontrastID = $_GET["kontrast_id"];
// Wenn der Kontrast resettet werden soll wird ein leerer Cookie erstellt
if ($kontrastID === "reset" || $kontrastID == 0) {
	setcookie('contrast', "reset", 0, PAPOO_WEB_PFAD);
	setcookie('contrast', "reset", 0, PAPOO_WEB_PFAD . "/");
	header("Content-Type: text/css");
	die();
}
else {
	$kontrastID = (int)$kontrastID;
	setcookie('contrast', $kontrastID, 0, PAPOO_WEB_PFAD);
	setcookie('contrast', $kontrastID, 0, PAPOO_WEB_PFAD . "/");
}
// Nehmen der Text Farbe für die zugewiesene ID
$query = sprintf(
	"SELECT `textColor`, `backgroundColor` FROM %s WHERE kontrastID = %d",
	$db_praefix . "plugin_kontrastwechsler",
	$kontrastID
);
$result = mysqli_fetch_assoc($db->query($query));

// Was in dem CSS als Platzhalter geändert wird
$search = array('#text_color#', '#background_color#');

// Womit die Platzhalter replaced werden
$replace = array($result["textColor"], $result["backgroundColor"]);
// str_replace für die oben genannten Dinge
$cssDone = str_replace($search , $replace , $cssListe["cssText"]);
// CSS das dann ausgegeben wird.
header("Content-Type: text/css");
echo $cssDone;
die();