<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.user_check_active.php
 * Type:     function
 * Name:     user_check_active
 * Purpose:  displays the activation status of a given username
 * -------------------------------------------------------------
 */

function smarty_function_user_check_active($params, &$smarty)
{
	global $db;
	global $cms;

	if ($params['user_name'] != "")
	{
		$sql = sprintf("SELECT active
						FROM %s
						WHERE username = '%s'",
				$cms->papoo_user,
				$db->escape($params['user_name'])
				);
		$return = $db->get_var($sql);
		if ($return == "") $smarty->trigger_error("user_check_active: unknown user", E_USER_WARNING);
	}
	else
	{
		$smarty->trigger_error("user_check_active: missing user name", E_USER_WARNING);
		$return = "";
	}
	return $return;
}
?>
