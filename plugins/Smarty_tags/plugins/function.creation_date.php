<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.creation_date.php
 * Type:     function
 * Name:     creation_date
 * Purpose:  displays the creation date of the current article
 * -------------------------------------------------------------
 */

function smarty_function_creation_date($params, &$smarty)
{
	global $db;
	global $cms;
	$table_data = $smarty->get_template_vars(table_data);
	// Es gibt für die Starteite kein Erstellungsdatum in der DB und die Startseite liefert keine reporeid!
	$sql = sprintf("SELECT erstellungsdatum
					FROM %s
					WHERE reporeID = '%d'",
			$cms->papoo_repore,
			$db->escape($table_data[0]['reporeid'])
			);
	$creation_date = $db->get_var($sql);
	return $creation_date;
}
?>
