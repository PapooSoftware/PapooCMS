<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.user_groups.php
 * Type:     function
 * Name:     user_groups
 * Purpose:  displays the groups associated to a given username
 * -------------------------------------------------------------
 */

function smarty_function_user_groups($params, &$smarty)
{
	global $db;
	global $cms;

	if ($params['user_name'] != "")
	{
		$sql = sprintf("SELECT gruppenname AS group_name
						FROM %s T1
							INNER JOIN %s T2 ON (T1.userid = T2.userid)
							INNER JOIN %s T3 ON (T2.gruppenid = T3.gruppeid)
						WHERE T1.username = '%s'
						ORDER BY gruppenname",
				$cms->papoo_user,
				$cms->papoo_lookup_ug,
				$cms->papoo_gruppe,
				$db->escape($params['user_name'])
				);
		$return = $db->get_results($sql, ARRAY_A);
		if ($return == "") $smarty->trigger_error("user_groups: unknown user", E_USER_WARNING);
	}
	else
	{
		$smarty->trigger_error("user_groups: missing user name", E_USER_WARNING);
		$return = "";
	}
	if (is_array($return)) $smarty->assign("groups", $return);
	else return $return;
}
?>
