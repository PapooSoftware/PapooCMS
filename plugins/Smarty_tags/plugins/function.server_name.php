<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.server_name.php
 * Type:     function
 * Name:     server_name
 * Purpose:  displays the PHP variable $_SERVER['SERVER_NAME']
 * -------------------------------------------------------------
 */

function smarty_function_server_name($params, &$smarty)
{
	return $_SERVER['SERVER_NAME'];
}
?>
