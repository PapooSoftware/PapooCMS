<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.user_name.php
 * Type:     function
 * Name:     user_name
 * Purpose:  displays the user name if the user is logged in
 * -------------------------------------------------------------
 */

function smarty_function_user_name($params, &$smarty)
{
	$loggedin = $smarty->get_template_vars(loggedin);
	if ($loggedin)
	{
		global $user;
		if ($user->username)
		{
			global $db;
			global $cms;
			$sql = sprintf("SELECT user_vorname,
									user_nachname
									FROM %s
									WHERE username = '%s'",
									$cms->papoo_user,
									$db->escape($user->username)
							);
			$result = $db->get_results($sql, ARRAY_A);
			$vorname = $result[0]['user_vorname'];
			$nachname = $result[0]['user_nachname'];
			if ($params['name'] == 1) $return = $vorname;
			elseif ($params['name'] == 2) $return = $nachname;
			elseif ($params['name'] == 3) $return = $vorname . " " . $nachname;
			elseif (!isset($params['name'])) $return = $vorname . " " . $nachname;
		}
	}
	return $return;
}
?>