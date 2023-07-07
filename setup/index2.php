<?php 
// Sprachdatei inkludieren je nach Auswahl
session_start(); 

ob_start();
		phpinfo();
		$phpinfo = ob_get_contents();
		ob_end_clean();

$start = explode("<h2><a name=\"module_mysql\">mysql</a></h2>",$phpinfo,1000);
if(count($start) < 2){
$mysql= "MySQL Version konnte nicht gefunden werden.";
}else{
$again = explode("<tr><td class=\"e\">Client API version </td><td class=\"v\">",$start[1],1000);
$last_time = explode(" </td></tr>",$again[1],1000);
$mysql= "".$last_time[0]."";
}
$mysql_data=explode("-",$mysql);
$mysql =$mysql_data['0'];
$langsel = "";
if ( empty( $_POST['langsel'] ) ) $_POST['langsel'] = "de";
$langsel = $_POST['langsel'];
switch ( $langsel )
{
    case "de":
        // Deutsch einbinden
        require_once "lang_file_de.php";
        $_SESSION['langsel'] = $langsel;
        break;

    case "en":
        // Englisch einbinden
        require_once "lang_file_en.php";
        $_SESSION['langsel'] = $langsel;
        break;

    default:
        // default ist Deutsch
        require_once "lang_file_de.php";
        $_SESSION['langsel'] = "de";
        break;
}

// checken ob die Verzeichnisse beschreibbar sind
function checkpermission( $filename )
{
    $dirname=str_replace('index.html',"",$filename);
    if (is_dir($dirname))
    {
    @chmod ($dirname, 0777);
    }
    if ( !@fopen( "$filename", "w+" ) )
    {
        return false;
    }
    else
    {
        return true;
    }
}

function check_filesize()
{
    $size = filesize( "./papoo.sql" );
    $version = "light";
    $dbfehler = "";
    // WEnn Papoo Pro
    if ( $version == "pro" )
    {
        if ( $size < 383000 )
        {
            $dbfehler = "ok";
        }
    }
    if ( $version == "light" )
    {
        // WEnn Papoo Light
        if ( $size < 355000 )
        {
            $dbfehler = "ok";
        }
    }
    return $dbfehler;
}
// EInige Dateien zufällig prüfen
function file_is()
{
    $fehler = "";
    if ( !file_exists( "../lib/includes.inc.php" ) )
    {
        $fehler = "1";
    }
    if ( !file_exists( "../all_inc_front.php" ) )
    {
        $fehler = "1";
    }
    if ( !file_exists( "../lib/classes/cms_class.php" ) )
    {
        $fehler = "1";
    }
    return $fehler;
}

$verz_array = array( "../lib/site_conf.php",
					"../templates_c/index.html",
					"../images/index.html",
					"../images/thumbs/index.html",
					"../dokumente/index.html",
					"../dokumente/upload/index.html",
					"../dokumente/files/index.html",
					"../dokumente/backup/index.html",
					"../dokumente/logs/index.html",
					"../cache/index.html",
					"../interna/templates_c/standard/index.html",
					"../interna/templates_c/index.html" );

foreach ( $verz_array as $file )
{
    if ( !checkpermission( $file ) )
    {
        if ( $file == "../lib/site_conf.php" )
        {
            $perm[$file] = "Die Datei (file) $file ist nicht beschreibbar/not writable.";
        }
        else
        {
            $file = str_replace( "index.html", "", $file );
            $perm[$file] = "Das Verzeichnis (directory) $file ist nicht beschreibbar/not writable.";
        }
    }
}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<link rel="stylesheet" type="text/css" href="metro-bootstrap.css" />
<link rel="stylesheet" type="text/css" href="font-awesome.css" />
<link rel="stylesheet" type="text/css" href="metro-bootstrap_papoo.css" />
<link rel="stylesheet" type="text/css" href="login.css" />
	<title><?php echo $message2 ?></title>

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
echo $message3_a;



?>


	<?php
if ( file_is() == "1" )
{

    ?>
		<div style="border:1px solid black; padding:5px; color:red;"><h2>Achtung</h2>Eine oder mehrere Dateien sind nicht vorhanden, &uuml;berpr&uuml;fen Sie Ihren FTP Upload.<br />
		Falls nach der Installation Fehler auftreten laden Sie alle Dateien erneut hoch.</div>
		<?php
}
if ( check_filesize() == "ok" )
{

    ?><div style="border:1px solid black; padding:5px; color:red;"><h2>Achtung</h2>Die Installationsdatei ist m&ouml;glicherweise nicht in Ordnung, bitte überprüfen Sie die Größe der Datei papoo.sql im Verzeichnis setup mit der Größe Ihrer lokalen Datei. Die Datei sollte eine Gr&ouml;&szlig;e von ca. 350 kb bei der Papoo Light und 380 kb bei der Papoo Pro Version haben. <br />Laden Sie die Datei am besten erneut per FTP hoch!<br />
		Wenn Sie sicher sind das die Gr&ouml;&szlig;e der Datei in Ordnung ist k&ouml;nnen Sie trotzdem fortfahren.</div>

		<?php
}
echo $message3_b;



if ( !empty( $perm ) )
{
    echo $message3b;

    ?>
        <div style="border:1px solid black; padding:5px; color:red;"><ul>
        <?php
    foreach ( $perm as $error )
    {
        echo "<li>" . $error . "</li>";
    }
    echo "</ul></div>";
}
//Überschrift CHECK
echo $message3a;
//Tabelle Systemvorrausetzungen
echo $msgn1;
 
?>
<table  class="outside  table table-striped table-hover"><tr><th>Systemeinstellung</th><th>Ihr System</th><th>Mindest.</th><th>Ok</th><th style="width:40%;">Erläuterung</th></tr>
<tr><td>PHP-Version</td><td><?php echo phpversion(); ?></td><td>7.4.x</td><td style="width:20px;"><?php if (phpversion()>=7.4){
?>  <img src="../interna/bilder/check.png" width="20" height="20" alt="OK"/><?php
} else {
 ?>
<img src="../interna/bilder/filleclose.gif" width="20" height="20" alt="NOT OK"/>
<?php
}
?></td><td>Papoo verwendet Funktionen, die die PHP-Version 7.4 benötigen. Die Verwendung älterer Versionen ist nicht möglich. Sie können bei jedem Provider Ihren Account auf PHP&nbsp;7.4 oder höher umstellen (lassen).</td></tr>

<tr><td>MySQL/MariaDB-Version</td><td><?php echo $mysql;?></td><td>5</td><td><?php if ($mysql>"4.2"){ ?>
<img src="../interna/bilder/check.png" width="20" height="20" alt="OK"/><?php
} else {
 ?>
<img src="../interna/bilder/filleclose.gif" width="20" height="20" alt="NOT OK"/>
<?php
}
?></td><td>Papoo verwendet einige Funktionen die mind. Version 4.3 brauchen. Die Verwendung älterer Versionen ist möglich aber mit Problemen verbunden für die wir keinen Support übernehmen.</td></tr>
<tr><td>Register Globals</td><td><?php 
    echo $glob= ini_get("register_globals");
    if ($glob==1) echo "ON";
    else echo "OFF";
    ?></td><td>OFF</td><td><?php if ($glob!="1"){ ?>
<img src="../interna/bilder/check.png" width="20" height="20" alt="OK"/><?php
} else {
 ?>
<img src="../interna/bilder/stamm.png" width="20" height="20" alt="NOT OK"/>
<?php
}
?></td><td>Papoo funktioniert natürlich auch mit Register Globals ON, aber OFF ist die empfohlene Sicherheitseinstellung! Wenn Ihr Server es erlaubt stellen Sie das per <code>.htaccess</code>-Datei oder <code>php.ini</code>-Datei um!</td></tr>
<tr><td>Memory Limit</td><td><?php 
    echo ini_get("memory_limit");
    ini_set("memory_limit", "32M");
    ?><br />Memory-Limit nach setzen auf 32:
    <?php 
    echo $memlimit=ini_get("memory_limit");
    ?></td><td>16M</td><td><?php if ($memlimit>"16M"){ ?>
<img src="../interna/bilder/check.png" width="20" height="20" alt="OK"/><?php
} else {
 ?>
<img src="../interna/bilder/filleclose.gif" width="20" height="20" alt="OK"/>
<?php
}
?></td><td>8M (= 8 Megabyte) als Arbeitsspeicher reicht nur für die Lightversion aus. Höhere Versionen wie die Papoo Pro brauchen mindestens 16 MB, der Shop sogar mind. 32 MB. Wenn das Memory Limit nach dem setzen größer ist, können Sie das flexibel in der Adminoberfläche ändern.</td></tr>
<tr><td>Skriptlaufzeit</td><td><?php 
    echo ini_get("max_execution_time"). " Sekunden";
    ini_set("max_execution_time", "60");
    ?><br />Memory-Limit nach setzen auf 60 Sekunden:<br />
    <?php 
    echo $memlimit=ini_get("max_execution_time")." Sekunden";
    ?></td><td>30</td><td><?php if ($memlimit>"29"){ ?>
<img src="../interna/bilder/check.png" width="20" height="20" alt="OK"/><?php
} else {
 ?>
<img src="../interna/bilder/filleclose.gif" width="20" height="20" alt="OK"/>
<?php
}
?></td><td>Die Skriptlaufzeit ist gerade bei kleineren Accounts sehr wichtig. Weniger als 30 Sekunden kann zu Problemen führen, 60 sind optimal. In der Regel sind Papooseiten innerhalb von weniger als 1 Sekunden fertig, aber wenn Sie z.B. eine Datenbanksicherung einspielen brauchen Sie deutlich mehr Zeit.</td></tr>

</table>
<?php
echo $message3an1;

//$message3a2
if ( empty( $perm ) )
{
echo $message3a2;
}
else
{
echo $message3a2b;
}
?>
	</div>
	</div></div>
	<div style="clear: both;">&nbsp;</div>
</div>

</div>

</body>
</html>