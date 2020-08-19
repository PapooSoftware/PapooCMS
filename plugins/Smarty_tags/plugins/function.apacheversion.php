<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.apacheversion.php
 * Type:     function
 * Name:     apacheversion
 * Purpose:  displays the version of apache
 * -------------------------------------------------------------
 */

function smarty_function_apacheversion($params, &$smarty)
{
	return apache_get_version();
}
?>
