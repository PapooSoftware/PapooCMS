<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.reset.php
 * Type:     function
 * Name:     reset
 * Purpose:  resets the time out value or sets the password
 * -------------------------------------------------------------
 */

function smarty_function_reset($params, &$smarty)
{
	if (empty($params['user_name']))
	{
		$smarty->trigger_error("reset: missing parameter 'user_name'", E_USER_WARNING);
		$fehler = 1;
	}
	if (empty($params['password']) AND empty($params['waittime']))
	{
		$smarty->trigger_error("reset: missing parameter 'password' or 'waittime'", E_USER_WARNING);
		$fehler = 1;
	}
	if (!empty($params['password']) AND !empty($params['waittime']))
	{
		$smarty->trigger_error("reset: parameter 'password' and 'waittime' defined", E_USER_WARNING);
		$fehler = 1;
	}
	if (!$fehler)
	{
		global $db;
		global $cms;
		if (!empty($params['password'])) $set = ' password=\'' . md5($params['password']) . '\'';
		else $set = ' zeitsperre=0';
		
		$sql = sprintf("UPDATE %s
						SET $set
						WHERE username = '%s'",
				$cms->papoo_user,
				$db->escape($params['user_name'])
				);
		$db->query($sql);
		return "Ok";
	}
}
?>
