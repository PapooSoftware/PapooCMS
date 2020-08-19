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

session_set_cookie_params("432000");
session_start();
$title = "";
$ausgabe = "";
if (!empty ($_GET['hilfe'])) {
	require_once("../lib/classes/extlib/Snoopy.class.inc.php");

	if (empty($_GET['action'])) {
		($_GET['action'] = "");
	}
	if (($_GET['action'] == "edit")) {
		$url = "http://www.doku.papoo.de/index.php?title=" . ucwords(htmlentities($_GET['hilfe'])) . "&action=edit&section=" . htmlentities($_GET['section']);
	}
	else {
		$url = "http://www.doku.papoo.de/index.php/" . ucwords(htmlentities($_GET['hilfe']));
	}
	//http://www.doku.papoo.de/index.php?title=Spezial:Userlogin&amp;action=submitlogin&wpName=syspapoo&wpPassword=papoo2008&wpLoginattempt=Anmelden

	$titel = htmlentities($_GET['hilfe']);
	$html = new Snoopy();
	$html->agent = "Papoo Doku Browser";
	$html->referer = $_SERVER["HTTP_REFERER"];
	if (empty($_SESSION['kooies'])) {
		$_SESSION['kooies'] = "";
	}
	$html->cookies = $_SESSION['kooies'];

	if (empty($_SESSION['kooies'])) {
		$url1 = "http://www.doku.papoo.de/index.php";
		$formvar['title'] = "Spezial:Userlogin";
		$formvar['action'] = "submitlogin";
		$formvar['wpName'] = "syspapoo";
		$formvar['wpPassword'] = "syspapoo2008";
		$formvar['wpLoginattempt'] = "Anmelden";
		$html->submit($url1, $formvar);
	}
	$html->fetch($url);

	$_SESSION['kooies'] = $html->cookies;

	$daten = $html->results;

	$daten1 = explode(' content -->', $daten);
	// <div class="printfooter">
	$daten2 = explode('<div class="printfooter">', $daten1['1']);
	$zwischen = $daten2['0'];
	$zwischen = str_replace("href=\"http://", "target=\"blank\" href=\"http://", $zwischen);
	// Links zur Bearbeitung draus machen
	#$zwischen = eregi_replace("href=\"/index.php\?", "target=\"blank\" href=\"http://www.doku.papoo.de/index.php?", $zwischen);
	// im Fenster weitersurfen
	$zwischen = str_replace("href=\"/index.php/", "href=\"hilfe.php?hilfe=", $zwischen);
	$zwischen = str_replace("action=\"/index.php/", "action=\"hilfe.php?hilfe=", $zwischen);
	$zwischen = str_replace("href=\"/index.php\?title", "href=\"hilfe.php?hilfe", $zwischen);
	//action="/index.php
	$zwischen = str_replace('action="/index.php\?title=', "action=\"./hilfe.php?hilfe=", $zwischen);
	// Bilder korriegieren /images/
	$zwischen = str_replace("/images/", "http://www.doku.papoo.de/images/", $zwischen);
	//<!-- /jumpto -->
	$zwischen = str_replace("<!-- /jumpto -->", "<!-- /jumpto --><br />", $zwischen);
	$ausgabe = $zwischen;
}
$back = "zurück";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
	<title>Informationen zu <?php echo $titel;
		?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		@import url(./css/doku.css);
	</style>
</head>
<body>
<div class="topnavi">
	<a href="javascript:history.go(-1)"><?php echo $back;
		?></a> | <a href="./hilfe.php?hilfe=<?php echo $titel;
	?>&action=edit">Inhalt bearbeiten</a>|<a target="blank" href="http://www.doku.papoo.de?title=Spezial:Upload">Bilder
		hochladen</a> | <a href="hilfe.php?hilfe=Papoo_Dokumentation:Hilfe">Bearbeitungsregeln</a> | <a target="blank"
																										title="Es öffnet sich ein neues Fenster"
																										href="http://www.papoo.de/cms-dokumentation/">Doku-PDF </a>|
	<a href="javascript:self.print()">Drucken</a>
</div>
<div style="width:100%;overflow:auto;">
	<?php echo $ausgabe; ?>
</div>
<br/><br/>
<div align="center">
	<a href="#" class="popuplink" accesskey="9" onclick="window.close();">Fenster schliessen.</a>
</div>

</body>
</html>