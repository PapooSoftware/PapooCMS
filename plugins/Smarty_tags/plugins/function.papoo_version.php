<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.papoo_version.php
 * Type:     function
 * Name:     papoo_version
 * Purpose:  displays the content of the variable papoo_version
 * -------------------------------------------------------------
 */

function smarty_function_papoo_version($params, &$smarty)
{
	return $smarty->get_template_vars(papoo_version);
}
?>
