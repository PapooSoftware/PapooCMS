<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.maintenance.php
 * Type:     function
 * Name:     maintenance
 * Purpose:  displays the maintenance text
 * -------------------------------------------------------------
 */

function smarty_function_maintenance($params, &$smarty)
{
	global $db;
	global $cms;
	$sql = sprintf("SELECT wartungstext FROM %s",
			$cms->papoo_daten
			);
	$maintenance = $db->get_var($sql);
	return $maintenance;
}
?>
