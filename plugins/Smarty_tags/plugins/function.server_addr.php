<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.server_addr.php
 * Type:     function
 * address:  server_addr
 * Purpose:  displays the PHP variable $_SERVER['SERVER_ADDR']
 * -------------------------------------------------------------
 */

function smarty_function_server_addr($params, &$smarty)
{
	return $_SERVER['SERVER_ADDR'];
}
?>
