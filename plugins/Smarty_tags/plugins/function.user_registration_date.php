<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.user_registration_date.php
 * Type:     function
 * Name:     user_registration_date
 * Purpose:  displays the user registration date
 * -------------------------------------------------------------
 */

function smarty_function_user_registration_date($params, &$smarty)
{
	global $db;
	global $cms;

	if ($params['user_name'] != "")
	{
		$sql = sprintf("SELECT zeitstempel
						FROM %s
						WHERE username = '%s'",
				$cms->papoo_user,
				$db->escape($params['user_name'])
				);
		$return = $db->get_var($sql);
		if (empty($return)) $smarty->trigger_error("user_registration_date: unknown user", E_USER_WARNING);
	}
	else
	{
		$smarty->trigger_error("user_registration_date: missing user name", E_USER_WARNING);
		$return = "";
	}
	return $return;
}
?>
