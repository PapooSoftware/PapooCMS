<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.http_host.php
 * Type:     function
 * Name:     http_host
 * Purpose:  displays the PHP variable $_SERVER['HTTP_HOST']
 * -------------------------------------------------------------
 */

function smarty_function_http_host($params, &$smarty)
{
	return $_SERVER['HTTP_HOST'];
}
?>
