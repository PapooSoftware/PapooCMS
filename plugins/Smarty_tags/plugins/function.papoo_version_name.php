<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.papoo_version_name.php
 * Type:     function
 * Name:     papoo_version_name
 * Purpose:  displays the name of this papoo_version (last word)
 * -------------------------------------------------------------
 */

function smarty_function_papoo_version_name($params, &$smarty)
{
	$arry = explode(" ", $smarty->get_template_vars(papoo_version));
	return $arry[count($arry)-1];
}
?>
