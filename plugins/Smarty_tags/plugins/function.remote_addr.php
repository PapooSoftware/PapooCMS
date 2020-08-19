<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.remote_addr.php
 * Type:     function
 * address:  remote_addr
 * Purpose:  displays the PHP variable $_SERVER['REMOTE_ADDR']
 * -------------------------------------------------------------
 */

function smarty_function_remote_addr($params, &$smarty)
{
	return $_SERVER['REMOTE_ADDR'];
}
?>
