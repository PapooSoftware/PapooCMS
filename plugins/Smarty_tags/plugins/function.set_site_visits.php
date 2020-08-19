<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.set_site_visits.php
 * Type:     function
 * Name:     set_site_visits
 * Purpose:  sets/resets the counter of site visitors
 * -------------------------------------------------------------
 */

function smarty_function_set_site_visits($params, &$smarty)
{
	if (!count($params))
	{
		$smarty->trigger_error("set_site_visits: missing parameter 'counter'", E_USER_WARNING);
	}
	elseif (!ctype_digit((string)($params['counter'])))
	{
		$smarty->trigger_error("set_site_visits: parameter 'counter' is not numeric", E_USER_WARNING);
	}
	else
	{
		global $db;
		global $cms;
		
		$sql = sprintf("UPDATE %s
						SET gesamtcount = '%s'",
				$cms->papoo_daten,
				$db->escape($params['counter'])
				);
		$db->query($sql);
		return "Ok";
	}
}
?>
