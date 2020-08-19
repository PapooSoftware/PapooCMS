<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.http_referer.php
 * Type:     function
 * Name:     http_referer
 * Purpose:  displays the PHP variable $_SERVER['HTTP_REFERER']
 * -------------------------------------------------------------
 */

function smarty_function_http_referer($params, &$smarty)
{
	return $_SERVER['HTTP_REFERER'];
}
?>
