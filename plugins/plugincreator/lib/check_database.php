<?php
/**
 * Created by PhpStorm.
 * User: andreas
 * Date: 12.10.15
 * Time: 14:31
 */

include('../../../lib/site_conf.php');
include ('../../../lib/ez_sql.php');

if(isset($_REQUEST['table'])) {
	global $db;
	global $db_praefix;

	$tabelle = $db->escape($_GET['table']);
	$sql = "SHOW TABLES LIKE '%" . $tabelle . "'";

	$ar = $db->get_results($sql, ARRAY_A);

	echo empty($ar) ? 0 : 1;
	exit;
}

echo 0;