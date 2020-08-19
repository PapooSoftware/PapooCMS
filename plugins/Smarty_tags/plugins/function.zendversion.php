<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.zendversion.php
 * Type:     function
 * Name:     zendversion
 * Purpose:  displays the version of Zend
 * -------------------------------------------------------------
 */

function smarty_function_zendversion($params, &$smarty)
{
	return zend_version();
}
?>
