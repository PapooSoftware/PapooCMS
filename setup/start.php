<?php
session_start();
// Sprachdatei inkludieren je nach Auswahl
$langsel = $_SESSION['langsel'];
if ( empty( $langsel ) )
{
    $langsel = "de";
}
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
<title><?php echo $message23;
?></title>



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

	 <?php echo $message24;
?>


<?php
// POST-Eingang
/**
 * Installation in Gedenken an Gerhard Sch�ning, gestorben im August 2004.
 */
if ( isset ( $_POST['password'] ) && !empty ( $_POST['password'] ) )
{
    $password = $_POST['password'];
}
else
{
    $password = "";
}
if ( isset ( $_POST['submit'] ) && !empty ( $_POST['submit'] ) )
{
    $submit = $_POST['submit'];
}
else
{
    $submit = "";
}
if ( $submit and $password )
{
    // Conf Daten einbringen
    require_once "../lib/site_conf.php";
    // Datenbankklasse einbinden
    require_once "../lib/ez_sql.php";

	// Temporarily disable CSRF protection
	$oldCsrfState = $db->csrfok;
	$db->csrfok = true;

    $db->query("SET NAMES 'utf8'");
    //$db->query("SET CHARACTER SET 'utf8'");
    
    // Datenbanknamen erstellen
    $papoo_user = $db_praefix . "papoo_user";
    $papoo_user_lookup = $db_praefix . "papoo_lookup_ug";
    // Passwort verschl�seln
    $passwordmd = md5( $password );
	$sql3 = "DELETE FROM $papoo_user_lookup WHERE userid='10' ";
	$db->query( $sql3 );
	$sql3 = "INSERT INTO $papoo_user_lookup SET userid='10', gruppenid='1' ";
	$db->query( $sql3 );
	$sql3 = "INSERT INTO $papoo_user_lookup SET userid='10', gruppenid='10' ";
	$db->query( $sql3 );
	/*
	$sql3 = "INSERT INTO $papoo_user_lookup SET userid='1', gruppenid='11' ";
	$db->query( $sql3 );
	$sql3 = "INSERT INTO $papoo_user_lookup SET userid='1', gruppenid='12' ";
	$db->query( $sql3 );
	*/
	// Passwort eintragen
	//$sql2 = "UPDATE $papoo_user SET password='$passwordmd', active='1', zeitsperre='', wie_oft_login='0' WHERE userid='10'";
    $sql2 = "UPDATE $papoo_user SET password='$passwordmd', username='root', active='1', zeitsperre='', wie_oft_login='0' WHERE userid='10'";
	$db->query( $sql2 );

    // Wenn Englisch, dann folgendes
    if ( $langsel == "en" )
    {
        // Datenbanknamen erstellen
        $papoo_stamm = $db_praefix . "papoo_daten";
        $sql2 = "UPDATE $papoo_stamm SET lang_backend='en', lang_frontend='en' WHERE datenid='2'";
        $db->query( $sql2 );
    }
    $username = "root";
    $userid = 10;
    $hash = md5( $username . $password . $userid );

	// Revert CSRF protection to old state
	$db->csrfok = $oldCsrfState;
    
    // Username und Passwort an Session �bergeben
    // b.legt: nicht �bergeben, da sonst einige Angaben fehler (z.B. Editor)
    // $_SESSION['sessionusername'] = $username;
    // $_SESSION['sessionuserid'] = $userid;
    // $_SESSION['sessionhash'] = $hash;
    echo '<div class="start1" style="text-align:left;" >';
    echo $message27;
    echo '</div>';
}
else
{

    ?>

 <?php echo $message24_1;
    ?>


  <form name="artikel"  action="" method="post" id="formi">
   <fieldset>
    <legend><?php echo $message25;
    ?></legend>
    <label for="password"><?php echo $message12;
    ?>:</label>
    <input type="password" id="password" name="password" size="30" tabindex="1" title="<?php echo $message25;
    ?>" />
   </fieldset>
   <p class="weiter2"><input type="submit" class="submit_back_green_big btn btn-success " value="<?php echo $message26;
    ?>" name="submit" /></p>
  </form>



<?php

}

?>
	</div>	</div>	</div>
	<div style="clear: both;">&nbsp;</div>
</div>



</body>
</html>
