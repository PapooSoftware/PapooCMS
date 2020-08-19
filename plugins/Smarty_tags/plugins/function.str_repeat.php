<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.str_repeat.php
 * Type:     function
 * address:  str_repeat
 * Purpose:  displays a given string and repeated a given number of times
 * -------------------------------------------------------------
 */

function smarty_function_str_repeat($params, &$smarty)
{
	if (!count($params))
	{
		$smarty->trigger_error("str_repeat: missing parameters count and what", E_USER_WARNING);
		$return = "";
	}
	elseif (count($params) > 2)
	{
		$smarty->trigger_error("str_repeat: too few parameters", E_USER_WARNING);
		$return = "";
	}
	elseif (!isset($params['count']))
	{
		$smarty->trigger_error("str_repeat: parameter count missing", E_USER_WARNING);
		$return = "";
	}
	elseif (!isset($params['what']))
	{
		$smarty->trigger_error("str_repeat: parameter what missing", E_USER_WARNING);
		$return = "";
	}
	elseif (!ctype_digit((string)($params['count'])))
	{
		$smarty->trigger_error("str_repeat: parameter count is not numeric", E_USER_WARNING);
		$return = "";
	}
	elseif ($params['count'] == 0)
	{
		$smarty->trigger_error("str_repeat: parameter count is zero", E_USER_WARNING);
		$return = "";
	}
	elseif ($params['what'] == "")
	{
		$smarty->trigger_error("str_repeat: parameter what is empty", E_USER_WARNING);
		$return = "";
	}
	else $return = str_repeat($params['what'], $params['count']);
	return $return;
}
?>
