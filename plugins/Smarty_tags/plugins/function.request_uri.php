<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.request_uri.php
 * Type:     function
 * address:  request_uri
 * Purpose:  displays the PHP variable $_SERVER['REQUEST_URI']
 * -------------------------------------------------------------
 */

function smarty_function_request_uri($params, &$smarty)
{
	return $_SERVER['REQUEST_URI'];
}
?>
