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
 * /**
 * Hier werden die Banner weitergeleitet.
 */

session_set_cookie_params("432000");
session_start();
//n�tige Dateien einbinden
require_once ("../../lib/site_conf.php");
require_once ("../../lib/ez_sql.php");
require_once ("../../lib/classes/variables_class.php");
//Daten aus der url holen
$checked->url;

//Link aus der db holen und abgleichen
$sql = sprintf("SELECT * FROM %s WHERE banner_code LIKE '%s' AND banner_id='%d'",
	$db_praefix . "papoo_bannerverwaltung_daten",
	"%" . $db->escape($checked->url) .
	"%", $db->escape($checked->bannerid)
);
$var = $db->get_results($sql);

//Z�hlen
if (!empty($var) && $_SESSION['bannerclicked']!=$checked->bannerid)
{
	$sql = sprintf("UPDATE %s SET banner_clicks=banner_clicks+1 WHERE banner_id='%d'",
		$db_praefix . "papoo_bannerverwaltung_daten",
		$db->escape($checked->bannerid)
	);
	$db->query($sql);
	$_SESSION['bannerclicked']=$checked->bannerid;
}
//weiterleiten
header( "Location:".$checked->url);
exit ();
