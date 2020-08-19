<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.last_mod_date.php
 * Type:     function
 * Name:     last_mod_date
 * Purpose:  displays the creation date of the current article
 * -------------------------------------------------------------
 */

function smarty_function_last_mod_date($params, &$smarty)
{
	global $db;
	global $cms;
	$table_data = $smarty->get_template_vars(table_data);
	// Es gibt für die Starteite keine Infos über das Änderungsdatum in der DB und die Startseite liefert keine reporeid!
	$sql = sprintf("SELECT timestamp
					FROM %s
					WHERE reporeID = '%d'",
			$cms->papoo_repore,
			$db->escape($table_data[0]['reporeid'])
			);
	$last_mod_date = $db->get_var($sql);
	return $last_mod_date;
}
?>
