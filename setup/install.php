<?php

/**
Installation in Gedenken an Gerhard Sch�ning, gestorben im August 2004.
*/
session_start();

//Sprachdatei inkludieren je nach Auswahl
$langsel = $_SESSION['langsel'];
if (empty($langsel)) $langsel = "de";

switch ($langsel)
{
    case "de":
    //Deutsch einbinden
    require_once "lang_file_de.php";
    $_SESSION['langsel']=$langsel;
    break;

    case "en":
    //Englisch einbinden
    require_once "lang_file_en.php";
    $_SESSION['langsel']=$langsel;
    break;

    default:
    //default ist Deutsch
    require_once "lang_file_de.php";
    $_SESSION['langsel']="de";
    break;
}

if (isset($_POST['submit']) && !empty($_POST['submit'])) $submit = $_POST['submit'];
else $submit = "";

if (isset($_POST['mysqlusername']) && !empty($_POST['mysqlusername'])) $mysqlusername = $_POST['mysqlusername'];
else $mysqlusername = "";

if (isset($_POST['mysqlpasswort']) && !empty($_POST['mysqlpasswort'])) $mysqlpasswort = $_POST['mysqlpasswort'];
else $mysqlpasswort = "";

if (isset($_POST['mysqlserver']) && !empty($_POST['mysqlserver'])) $mysqlserver = $_POST['mysqlserver'];
else $mysqlserver = "";

if (isset($_POST['mysqldatenbank']) && !empty($_POST['mysqldatenbank'])) $mysqldatenbank = $_POST['mysqldatenbank'];
else $mysqldatenbank = "";


if (isset($_POST['lizenz1']) && !empty($_POST['lizenz1'])) $lizenz1 = $_POST['lizenz1'];
else $lizenz1 = "";

if (isset($_POST['mysqlpraefix']) && !empty($_POST['mysqlpraefix']) && $_POST['mysqlpraefix']!="papoo") $mysqlpraefix = $_POST['mysqlpraefix'];
else $mysqlpraefix = "db_" . date("dmY");

if (isset($_POST['absolut']) && !empty($_POST['absolut'])) $absolut = $_POST['absolut'];
else $absolut = $_SERVER["DOCUMENT_ROOT"].preg_replace('/\/setup\/install.php/i', "",$_SERVER["SCRIPT_NAME"]);

if (isset($_POST['webverzeichnis']) && !empty($_POST['webverzeichnis'])) $webverzeichnis = $_POST['webverzeichnis'];
else $webverzeichnis = preg_replace('/\/setup\/install.php/i', "",$_SERVER["SCRIPT_NAME"]);

// Sicherheitsma�nahme
$mysqlusername = strip_tags($mysqlusername);
$mysqlpasswort = str_replace("\$", "\\\$", str_replace("\"", "\\\"", str_replace("\\", "\\\\", $mysqlpasswort)));
$mysqlserver = strip_tags($mysqlserver);
$mysqldatenbank = strip_tags($mysqldatenbank);
$mysqlpraefix = strip_tags($mysqlpraefix);
$absolut = strip_tags($absolut);
$webverzeichnis = strip_tags($webverzeichnis);
$fehler_mysqlusername="";
$fehler_mysqlserver="";
$fehler_mysqlpasswort="";
$fehler_mysqldatenbank="";
$fehler_mysqlpraefix="";
$fehler_absolut="";
$fehler_webverzeichnis="";

// Test bei �bertragung, ob alle Felder korrekt ausgef�llt sind
if ($submit)
{
	$daten_fehler = false;
	if (strip_tags($mysqlusername) == "")
	{
		$daten_fehler = true;
		$fehler_mysqlusername = $message11_fehler;
	}
	/*
	// Pr�fung auf Passwort entfernt, damit auch keine Passwort angegeben werden kann
	if (strip_tags($mysqlpasswort) == "")
	{
		$daten_fehler = true;
		$fehler_mysqlpasswort = $message12_fehler;
	}
	*/
	if (strip_tags($mysqlserver) == "")
	{
		$daten_fehler = true;
		$fehler_mysqlserver = $message13_fehler;
	}
	if (strip_tags($mysqldatenbank) == "")
	{
		$daten_fehler = true;
		$fehler_mysqldatenbank = $message14_fehler;
	}
	if (strip_tags($mysqlpraefix) == "")
	{
		$daten_fehler = true;
		$fehler_mysqlpraefix = $message15_fehler;
	}

	if (!file_exists($absolut."/lib/classes/artikel_class.php"))
	{
		$daten_fehler = true;
		$fehler_absolut = $message16_fehler;
	}

	if (empty($lizenz1))
	{
		$daten_fehler = true;
		$fehler_lizenz1 = $message16b_fehler;
	}

	// Pr�fung Webverzeichnis funzt leider nicht !!!
	/*
	$file = fopen ("http://localhost".$webverzeichnis."/lib/classes/artikel_class.php", "r");
	if (!file)
	{
		$daten_fehler = true;
		$fehler_webverzeichnis = $message16A_fehler;
	}
	fclose($file);
	*/
}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<link rel="stylesheet" type="text/css" href="metro-bootstrap.css" />
<link rel="stylesheet" type="text/css" href="font-awesome.css" />
<link rel="stylesheet" type="text/css" href="metro-bootstrap_papoo.css" />
<link rel="stylesheet" type="text/css" href="login.css" />
<?php
if ($submit && !$daten_fehler) echo "<title>".$message4."</title>";
else echo "<title>".$message5."</title>";
?>


</head>

<body>
<div class="container">
<div class="span3"></div>
<div class="span8">
<div id="logo" >
            <img src="../interna/css/images/logo_big.png" alt="" />
        </div>
		<h1>Setup Papoo CMS</h1>
		<div class="einlogg_out">
			
		<div class="einlogg" id="einlogg">
<?php
if ($submit && !$daten_fehler)
{
echo $message8;
}
else {
	echo $message9;
}
    ?>
	



<?php
$version = phpversion();
if ($version < "5.0.0")
{
 echo $message6; echo $version; echo $message7;
}

if ($submit && !$daten_fehler)
{
	$inhalt = implode("", file("site_conf_vorl.php"));

    $extra2="ppx07";
	$mysqlpraefix = $mysqlpraefix .$extra2. "_";
	$dokuuser=sha1(rand(1,99999).time());

	$inhalt = preg_replace('/db_user="(.*?)"/i', "db_user=\"$mysqlusername\"", $inhalt);
	$inhalt = preg_replace('/db_pw="(.*?)"/i', "db_pw=\"".str_replace("\\", "\\\\", $mysqlpasswort)."\"", $inhalt);
	$inhalt = preg_replace('/db_host="(.*?)"/i', "db_host=\"$mysqlserver\"", $inhalt);
	$inhalt = preg_replace('/db_name="(.*?)"/i', "db_name=\"$mysqldatenbank\"", $inhalt);
	$inhalt = preg_replace('/db_praefix="(.*?)"/i', "db_praefix=\"$mysqlpraefix\"", $inhalt);
	$inhalt = preg_replace('/pfadhier="(.*?)"/i', "pfadhier=\"$absolut\"", $inhalt);
	$inhalt = preg_replace('/webverzeichnis="(.*?)"/i', "webverzeichnis=\"$webverzeichnis\"", $inhalt);
	$inhalt = preg_replace('/PAPOO_DOKU_USER,"(.*?)"/i', "PAPOO_DOKU_USER,\"$dokuuser\"", $inhalt);


	$erstellen = fopen ("../lib/site_conf.php", "w+");
	$filename2 = "../lib/site_conf.php";
	$file = fopen($filename2, "r+");
	fwrite($file, $inhalt);
	fclose($file);

/** <h1 class="weiter">Schritt 2: Ihre Daten wurden eingetragen</h1>
        <p>Wir k�nnen nun versuchen, Verbindung zur Datenbank aufzunehmen und die notwendigen weiteren Schritte zu erledigen.</p>
        <p>F�r Fragen steht Ihnen jederzeit unser <a href="http://www.papoo.de/forum.php" title="Papoo-Forum in neuem Fenster �ffnen" target="_blank">Papoo-Forum</a> zur Verf�gung.</p>
        <p class="weiter2">Weiter mit <a href="install2.php">Schritt 3: Verbindung zur Datenbank pr�fen</a>.</p>
 */
	echo $message8_1;
}

else
// 1. Aufruf oder ein oder mehr Felder nicht korrekt gef�llt?
{
	echo $message9_1;
    ?>


<form method="post" action="./install.php" id="formi">
	<fieldset>
		<legend><?php echo $message10?></legend>
		<span style="color: #990000;"><?php echo $fehler_mysqlusername?></span><br />
		<label for="name" class="left"><?php echo $message11?></label><br />
		<input type="text" id="name" name="mysqlusername" value="<?php echo $mysqlusername;?>" size="20" maxlength="40"></input><br />

		<span style="color: #990000;"><?php echo $fehler_mysqlpasswort?></span><br /><div class="clearfix"></div>
		<label for="pass" class="left"><?php echo $message12?>:</label><br />
		<input type="password" id="pass" name="mysqlpasswort" value="<?php echo $mysqlpasswort;?>" size="20" maxlength="100"></input><br />

		<span style="color: #990000;"><?php echo $fehler_mysqlserver?></span><br /><div class="clearfix"></div>
		<label for="host" class="left"><?php echo $message13?>:</label><br />
		<input type="text" id="host" name="mysqlserver" value="<?php echo $mysqlserver;?>" size="20" maxlength="40"></input><br />

		<span style="color: #990000;"><?php echo $fehler_mysqldatenbank?></span><br /><div class="clearfix"></div>
		<label for="db" class="left"><?php echo $message14?>:</label><br />
		<input type="text" id="db" name="mysqldatenbank" value="<?php echo $mysqldatenbank;?>" size="20" maxlength="40"></input><br />

		<span style="color: #990000;"><?php echo $fehler_mysqlpraefix?></span><br /><div class="clearfix"></div>
		<label for="praefix" class="left"><?php echo $message15?>:</label><br />
		<input type="text" id="praefix" name="mysqlpraefix" value="<?php echo $mysqlpraefix;?>" size="20" maxlength="40"></input><br />
			</fieldset>
			<fieldset>
		<legend><?php echo $message10a?></legend>
		<span style="color: #990000;"><?php echo $fehler_absolut?></span><br /><div class="clearfix"></div>
		<label for="absolut" class="left"><?php echo $message16?>:</label><br />
		<input type="text" id="absolut" name="absolut" value="<?php echo $absolut?>" size="50" maxlength="150"></input><br />

		<span style="color: #990000;"><?php echo $fehler_webverzeichnis?></span><br /><div class="clearfix"></div>
		<label for="webverzeichnis" class="left"><?php echo $message16A?>:</label><br />
		<input type="text" id="webverzeichnis" name="webverzeichnis" value="<?php echo $webverzeichnis;?>" size="50" maxlength="150"></input><br />
		<br />
	</fieldset>
				<fieldset>
		<legend><?php echo $message10b?></legend>
		<div style="width:90%;height:200px;overflow:auto;margin-left:20px;background-color:#fff;padding:10px;border:1px solid #999;">
		<?php echo $message16b?>
		</div>
		<br />
		<div class="clearfix"></div>
		<span style="color: #990000;"><?php if (!empty($fehler_lizenz1)){echo $fehler_lizenz1 ;} ?></span><br />
		<div class="clearfix"></div>
		<input type="checkbox" id="lizenz1" name="lizenz1" value="checked" /><label for="lizenz1" class="left"><?php echo $message16a?>:</label><br />

	</fieldset>
	<fieldset><legend><?php echo $message17?></legend>
	<p class="weiter2"> <input  type="submit" class="submit_back_green_big btn btn-info" name="submit" value="<?php echo $message17?>"></input></p>
</fieldset>
</form>

	<?php


}


?>
</div></div></div>
	<div style="clear: both;">&nbsp;</div>
</div>

</div>


</body>
</html>
