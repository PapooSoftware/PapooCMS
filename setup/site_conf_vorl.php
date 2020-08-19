<?php
if (stristr( $_SERVER['PHP_SELF'],'site_conf.php')) die('You are not allowed to see this page directly');

$db_host="";
$db_name="";
$db_user="";
$db_pw="";
$db_praefix="";
$pfadhier="";
$webverzeichnis="";

//Konstanten belegen
define("PAPOO_ABS_PFAD",$pfadhier);
define("PAPOO_WEB_PFAD",$webverzeichnis);
define("PAPOO_DOKU_USER","");