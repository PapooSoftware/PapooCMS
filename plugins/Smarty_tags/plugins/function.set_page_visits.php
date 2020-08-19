<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.set_page_visits.php
 * Type:     function
 * Name:     set_page_visits
 * Purpose:  sets/resets the counter of page visitors
 * -------------------------------------------------------------
 */

function smarty_function_set_page_visits($params, &$smarty)
{
	if (!count($params))
	{
		$smarty->trigger_error("set_page_visits: missing parameters 'counter', 'all' and/or 'reporeid'", E_USER_WARNING);
		$fehler = 1;
	}
	elseif (!isset($params['counter']))
	{
		$smarty->trigger_error("set_page_visits: empty parameter 'counter'", E_USER_WARNING);
		$fehler = 1;
	}
	elseif (!ctype_digit((string)($params['counter'])))
	{
		$smarty->trigger_error("set_page_visits: parameter 'counter' is not numeric", E_USER_WARNING);
		$fehler = 1;
	}
	if (!isset($params['all']) AND !isset($params['reporeid']))
	{
		$smarty->trigger_error("set_page_visits: missing parameter 'all' or 'reporeid'", E_USER_WARNING);
		$fehler = 1;
	}
	elseif (isset($params['all']) AND isset($params['reporeid']))
	{
		$smarty->trigger_error("set_page_visits: use one parameter of, 'all' or 'reporeid' only", E_USER_WARNING);
		$fehler = 1;
	}
	elseif (isset($params['reporeid']) AND !ctype_digit((string)($params['reporeid'])))
	{
		$smarty->trigger_error("set_page_visits: parameter 'reporeid' is not numeric", E_USER_WARNING);
		$fehler = 1;
	}
	if (!$fehler)
	{
		global $db;
		global $cms;
		
		$where = isset($params['reporeid']) ? "WHERE reporeID = '" . $params['reporeid'] . "'" : "";
		$sql = sprintf("UPDATE %s SET count = '%s' %s",
									$cms->papoo_repore,
									$db->escape($params['counter']),
									$where
						);
		$db->query($sql);
		return "Ok";
	}
}
?>
