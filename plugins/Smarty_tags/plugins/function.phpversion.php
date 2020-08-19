<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.phpversion.php
 * Type:     function
 * Name:     phpversion
 * Purpose:  displays the version of php
 * -------------------------------------------------------------
 */

function smarty_function_phpversion($params, &$smarty)
{
	return phpversion();
}
?>
