<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.server_software.php
 * Type:     function
 * Name:     server_software
 * Purpose:  displays the PHP variable $_SERVER['SERVER_SOFTWARE']
 * -------------------------------------------------------------
 */

function smarty_function_server_software($params, &$smarty)
{
	return $_SERVER['SERVER_SOFTWARE'];
}
?>
