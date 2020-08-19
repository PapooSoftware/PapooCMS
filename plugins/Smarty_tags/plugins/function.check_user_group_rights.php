<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.user_groups.php
 * Type:     function
 * Name:     user_groups
 * Purpose:  checks if a user has group rights to a given group ID
 * -------------------------------------------------------------
 */

function smarty_function_check_user_group_rights($params, &$smarty)
{
	global $db;
	global $cms;
	global $user;
	if ($params['group_id'] == "")
	{
		$smarty->trigger_error("check_user_group_rights: missing group Id", E_USER_WARNING);
		$return = "";
	}
	elseif (!empty($user->username))
	{
		$sql = sprintf("SELECT count(T2.gruppenid) FROM %s T1
							INNER JOIN %s T2 ON (T1.userid = T2.userid)
							WHERE T1.username = '%s'
							AND T2.gruppenid = '%d'",
				$cms->papoo_user,
				$cms->papoo_lookup_ug,
				$db->escape($user->username),
				$db->escape($params['group_id'])
				);
		$return = $db->get_var($sql);
	}
	
	$smarty->assign("smartytags_forum_user", $return);
}
?>
