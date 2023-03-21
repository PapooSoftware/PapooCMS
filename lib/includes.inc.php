<?php
/**
 * #####################################
 * # CMS Papoo                         #
 * # (c) Dr. Carsten Euwens 2015       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >5.5                  #
 * #####################################
 */

if (strstr($_SERVER['REQUEST_URI'],'ibrowser')) die('');
if (strstr( $_SERVER['PHP_SELF'],'includes.inc.php')) die('You are not allowed to see this page directly');

if (@!file_exists( __DIR__."/site_conf.php")) {
	header ("Location: ./setup/index.html");
	echo "<a href=\"./setup/index.html\">Bitte Setup starten / Please Start Setup</a>";
	exit();
}

require_once __DIR__ . "/version.php";
require_once __DIR__ . "/site_conf.php";

/**
 * Setze Variable auf NULL falls nicht existent, zwecks Behebung von Warnungen
 *
 * @param $variable
 */
function IfNotSetNull(&$variable)
{
	if(!isset($variable)) {
		$variable = NULL;
	}
}

// Abwärtskompatibilität für PHP 8.2 Klasse
if (!class_exists('AllowDynamicProperties')) {
	class AllowDynamicProperties { }
}

$version = PAPOO_VERSION . " Rev. " . PAPOO_REVISION . " - Papoo " . PAPOO_EDITION;

define("PAPOO_VERSION_STRING",$version);
define("DB_PRAEFIX",$db_praefix);

if (@!file_exists(PAPOO_ABS_PFAD."/lib/ez_sql.php")) {
	header ("Location: ./setup/index.html");
	echo "<a href=\"./setup/index.html\">Bitte Setup starten / Please Start Setup</a>";
	exit();
}

//Nur wenn beschreibbar ist
if (is_writeable(PAPOO_ABS_PFAD."/dokumente/logs/index.html")) {
	//Kann man, muß man nicht.
	//@session_save_path(PAPOO_ABS_PFAD."/dokumente/logs");
}

// Test-Einbindung der Cache-Klasse
require_once(PAPOO_ABS_PFAD."/lib/classes/cache_class.php");
require(PAPOO_ABS_PFAD."/lib/ez_sql.php");

//Namensrausm auf utf-8
$db->query("SET NAMES 'utf8'");
//SET sql_mode='';
$db->query("SET sql_mode=''");

//$db->query("SET CHARACTER SET 'utf8'");

require_once(PAPOO_ABS_PFAD."/lib/php-activerecord/ActiveRecord.php");

ActiveRecord\Config::initialize(function($cfg)
{
	global $db_host;
	global $db_name;
	global $db_user;
	global $db_pw;

	$cfg->set_model_directory(PAPOO_ABS_PFAD."/lib/models/");
	$cfg->set_connections(['development' => "mysql://$db_user:$db_pw@$db_host/$db_name?charset=utf8"]);
});

require_once(PAPOO_ABS_PFAD."/lib/function.php");
require_once(PAPOO_ABS_PFAD."/lib/url_syntax.php");
require_once(PAPOO_ABS_PFAD."/lib/classes.php");
require_once(PAPOO_ABS_PFAD."/lib/smarty/Smarty.class.php");