<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.script_name.php
 * Type:     function
 * address:  script_name
 * Purpose:  displays the PHP variable $_SERVER['SCRIPT_NAME']
 * -------------------------------------------------------------
 */

function smarty_function_script_name($params, &$smarty)
{
	return $_SERVER['SCRIPT_NAME'];
}
?>
