<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.last_mod_by.php
 * Type:     function
 * Name:     last_mod_by
 * Purpose:  displays the name of the editor, who edited the current article last
 * -------------------------------------------------------------
 */

function smarty_function_last_mod_by($params, &$smarty)
{
	global $db;
	global $cms;
	// Es gibt für die Starteite keine Infos über den Autor in der DB und die Startseite liefert keine reporeid!
	$table_data = $smarty->get_template_vars(table_data);
	$sql = sprintf("SELECT T2.username
					FROM %s T1
  					INNER JOIN %s T2 ON (T1.dokuser = T2.userid)
					WHERE T1.reporeID = '%d'",
					$cms->papoo_repore,
					$cms->papoo_user,
					$db->escape($table_data[0]['reporeid'])
					);
	$last_mod_by = $db->get_var($sql);
	return $last_mod_by;
}
?>
