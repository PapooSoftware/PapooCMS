<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.time.php
 * Type:     function
 * Name:     time
 * Purpose:  displays the current time, user formatted or default formatted
 * -------------------------------------------------------------
 */

function smarty_function_time($params, &$smarty)
{
	if(empty($params['format'])) $format = "%H:%M:%S";
	else $format = $params['format'];
	$return = strftime($format, time());
	return htmlentities($return);
}
?>
