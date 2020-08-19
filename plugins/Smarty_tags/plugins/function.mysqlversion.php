<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.mysqlversion.php
 * Type:     function
 * Name:     mysqlversion
 * Purpose:  displays the version of MySQL
 * -------------------------------------------------------------
 */

function smarty_function_mysqlversion($params, &$smarty)
{
	global $db;
	$sql = sprintf("SELECT version()");
	$mysqlversion = $db->get_var($sql);
	return $mysqlversion;
}
?>
