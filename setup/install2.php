<?php

/**
 * #####################################
 * # CMS Papoo                								 #
 * # (c) Dr. Carsten Euwens 2008       							 #
 * # Authors: Carsten Euwens                                             #
 * # http://www.papoo.de                                                   #
 * # Internet                                                                         #
 * #####################################
 * # PHP Version >4.2                                                           #
 * #####################################
 *
 * Installation in Gedenken an Gerhard Sch�ning, gestorben im August 2004.
 */
session_start();
// $langsel="";
$langsel = $_SESSION['langsel'];
switch ( $langsel )
{
    case "de" :
        // Deutsch einbinden
        require_once "lang_file_de.php";
        $_SESSION['langsel'] = $langsel;
        break;

    case "en" :
        // Englisch einbinden
        require_once "lang_file_en.php";
        $_SESSION['langsel'] = $langsel;
        break;

    default :
        // default ist Deutsch
        require_once "lang_file_de.php";
        $_SESSION['langsel'] = "de";
        break;
}

if ( !isset ( $_SESSION['insert'] ) )
{
    $_SESSION['insert'] = 0; // ",";
}
// "session_register" beim weiteren Aufrufen
if ( isset ( $_SESSION['insert'] ) && !empty ( $_SESSION['insert'] ) && is_numeric( $_SESSION['insert'] ) )
{
    $insert = $_SESSION['insert'];
}
else
{
    $insert = 1;
}

if ( isset ( $_GET['submit'] ) && !empty ( $_GET['submit'] ) )
{
    $submit = $_GET['submit'];
}
else
{
    $submit = "";
}
if ( isset ( $_GET['schreib'] ) && !empty ( $_GET['schreib'] ) )
{
    $schreib = $_GET['schreib'];
}
else
{
    $schreib = "";
}
// z.Z. n�tig wegen function.php
$pfad = "";
$toptext = "";
$exist = 0;
$eofline_identifier = "; ##b_dump##"; // Trennzeichen

require_once "../lib/site_conf.php";
$dbh=mysqli_connect($db_host,$db_user,$db_pw);
if ( !@mysqli_select_db($dbh, $db_name))
{
    mysqli_query($dbh, "CREATE DATABASE IF NOT EXISTS ".$db_name);
    if ( !@mysqli_select_db($dbh, $db_name))
    {
        $fehler= "Datenbank konnte nicht erzeugt werden, bitte erstellen Sie diese manuell z.B. per phpmyAdmin";
    }
}

require_once "../lib/ez_sql.php";
$db->query("SET NAMES 'utf8'");
$db->query("ALTER DATABASE " . $db_name . " COLLATE utf8_general_ci");

require_once "../lib/classes/dumpnrestore_class.php";

function check_tables()
{
    $tablesd = array( 
        "papoo_abk",
        "papoo_blacklist",
        "papoo_cache",
        "papoo_category",
        "papoo_category_lang",
        "papoo_category_lookup",
        "papoo_category_lookup_read",
        "papoo_category_lookup_write",
        "papoo_cform",
        "papoo_cform_lang",
        "papoo_collum3",
        "papoo_counter",
        "papoo_daten",
        "papoo_download",
        "papoo_forums",
        "papoo_gruppe",
        "papoo_images",
        "papoo_kategorie_bilder",
        "papoo_kategorie_dateien",
        "papoo_kategorie_video",
        "papoo_lang_en",
        "papoo_language_article",
        "papoo_language_collum3",
        "papoo_language_download",
        "papoo_language_image",
        "papoo_language_stamm",
        "papoo_language_video",
        "papoo_links",
        "papoo_lookup_article",
        "papoo_lookup_cat_dateien",
        "papoo_lookup_cat_images",
        "papoo_lookup_cat_video",
        "papoo_lookup_download",
        "papoo_lookup_forum_read",
        "papoo_lookup_forum_write",
        "papoo_lookup_image",
        "papoo_lookup_me_all_ext",
        "papoo_lookup_men_collum3",
        "papoo_lookup_men_ext",
        "papoo_lookup_men_int",
        "papoo_lookup_mencat",
        "papoo_lookup_nomen_collum3",
        "papoo_lookup_ug",
        "papoo_lookup_write_article",
        "papoo_mail",
        "papoo_me_nu",
        "papoo_men_uint_language",
        "papoo_menu_language",
        "papoo_menuint",
        "papoo_message",
        "papoo_module",
        "papoo_module_language",
        "papoo_name_language",
        "papoo_plugin_language",
        "papoo_pluginclasses",
        "papoo_plugins",
        "papoo_pref_files",
        "papoo_pref_images",
        "papoo_repore",
        "papoo_styles",
        "papoo_teaser_lookup_art",
        "papoo_teaser_lookup_men",
        "papoo_updates",
        "papoo_user",
        "papoo_version_article",
        "papoo_version_language_article",
        "papoo_version_lookup_article",
        "papoo_version_lookup_write_article",
        "papoo_version_repore",
        "papoo_video", );
    global $db;
    global $db_praefix;
    $query = "SHOW TABLES";
    $result = $db->get_results( $query );
    if ( $result )
    {
        foreach ( $result as $eintrag )
        {
            if ( $eintrag )
            {
                foreach ( $eintrag as $titel => $tabellenname )
                {
                    if ( stristr(  $tabellenname,$db_praefix ) )
                    {
                        $name = str_replace( $db_praefix, "", $tabellenname );

                        $table_list[] = $name;
                        // echo $tabellenname."\n";
                    }
                }
            }
        }
    }
    $tbname = $table_list;
    // print_r($tbname);
    global $datx;
    $datx = "";
    foreach( $tablesd as $dat )
    {
        if ( ( !deep_in_array( $dat, $tbname ) ) )
        {
            $datx .= $dat . "<br />";
        }
    }

    if ( !empty( $datx ) )
    {
        return true;
    }
    return false;
}
/**
 * �berpr�fen ob ein Eintrag irgendwo im Array ist
 */

function deep_in_array( $value, $array )
{
    if ( !empty ( $array ) )
    {
        foreach ( $array as $item )
        {
            if ( !is_array( $item ) )
            {
                if ( $item == $value )
                    return true;
                else
                    continue;
            }

            if ( in_array( $value, $item ) )
            {
                $isdeep_item = $item;
                return true;
            }
            else
            if ( deep_in_array( $value, $item ) )
                return true;
        }
    }
    return false;
}
// if-Abfrage nicht n�tig
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

if ( $submit == 1 )
{

    ?>
<title>Schritt 4 Ihrer Papoo Installation</title>
<?php

}
else
{

    ?>
<title>Schritt 3 Ihrer Papoo Installation</title>
<?php

}

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
if ( $submit == 1 )
{
    if ( !$exist )
    {
        echo $message20;
    }
}
else
{
    echo $message22;
}
if ( !empty($fehler))
{
echo $fehler;
}
?>


<?php


if ( $submit == 1 )
{
    // setzen der Ersetzungsvariablen
    $praefix = $db_praefix;
	
	
    // $db->hide_errors();
    // Query kann beliebig (unabsichtlich) wiederholt werden,
    // weil papoo.sql jetzt zuerst die Tabellen l�scht
    // error_reporting(E_ALL);
    if ( $insert == 1 )
    {
		// Temporarily disable CSRF protection
		$oldCsrfState = $db->csrfok;
		$db->csrfok = true;

        // $selecttest = "SELECT * FROM ".$praefix."papoo_user";
        // $resulttest = $db->query($selecttest);
        // if (!empty ($resulttest)) {
        // $exist = 1;
        // }
        $sql = "SHOW TABLES LIKE '" . $praefix . "papoo_user'";
        $exist = $db->query( $sql ); // $exist ist 0 wenn Tabelle nicht vorhanden,. Wenn die Tabelle schon existiert, ist $exist = 1
        // echo "Anzahl: ".$exist;
        $memlimit = "";
        $memlim = ini_get( 'memory_limit' );
        if ( $memlim < 16 )
        {
            $memlimit = "Limit";
            // echo "Memory-Limit < 16MB!";
        }
        // Zu wenig Speicher, daher die minimale INstallation ohne Plugins
        if ( $memlimit )
        {
            $sqldatei = "papoo.sql";
        }
        else
        {
            $sqldatei = "papoo.sql";
        }
        // echo $sqldatei;
        if ( !$exist )
        {
            // Daten eintragen
            $setup = new dumpnrestore_class();
            $setup->restore( $sqldatei );
            echo $message20_1;

            if ( ( check_tables() ) )
            {
                global $datx;

                ?>
				    <div style="border:1px solid black; padding:5px; color:red;"><h2>Achtung</h2>Die folgenden Tabellen wurden nicht korrekt installiert, bitte &uuml;berpr&uuml;fen Sie die /setup/papoo.sql Datei.<br />Laden Sie im Zweifelsfall das Verzeichnis /setup erneut hoch!<br />
				    <?php
                echo $datx;
                echo "</div>";
            } ;
        }
        /*
		if ($exist != 1 or $schreib == 1) {
		// Query zugelassen
		$_SESSION['insert'] = 1;
		echo $message20;
		// echo $fehler;
		}
		*/
        else
        {
            if ( $schreib == 1 )
            {
                // Daten eintragen
                $setup = new dumpnrestore_class();
                $setup->restore( $sqldatei );
                echo $message20_1;

                if ( ( check_tables() ) )
                {
                    global $datx;

                    ?>
				    <div style="border:1px solid black; padding:5px; color:red;"><h2>Achtung</h2>Die folgenden Tabellen wurden nicht korrekt installiert, bitte &uuml;berpr&uuml;fen Sie die /setup/papoo.sql Datei.<br />Laden Sie im Zweifelsfall das Verzeichnis /setup erneut hoch!<br />
				    <?php
                    echo $datx;
                    echo "</div>";
                } ;
            }
            else echo $message21;
        }

		// Revert CSRF protection to old state
		$this->db->csrfok = $oldCsrfState;
    }
}
else
{
    echo $message22_1;
}

?>
	</div>	</div>	</div>
	<div style="clear: both;">&nbsp;</div>
</div>

</div>

</body>
</html>
