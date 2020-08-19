<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.date.php
 * Type:     function
 * Name:     date
 * Purpose:  displays the current date/time, user or default formatted
 * -------------------------------------------------------------
 */

function smarty_function_date($params, &$smarty)
{
	if (empty($params['format'])) $format = "%b %d, %Y";
	else
	{
		if ($params['format'] == "papoo") $format = $smarty->get_template_vars(lang_dateformat);
		elseif ($params['format'] == "long") $format = $smarty->get_template_vars(lang_dateformat_long);
		elseif ($params['format'] == "short") $format = $smarty->get_template_vars(lang_dateformat_short);
		else $format = $params['format'];
	}
	$return = strftime($format, time());
	return htmlentities($return);
}
?>