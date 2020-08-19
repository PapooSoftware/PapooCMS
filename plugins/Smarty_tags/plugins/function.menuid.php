<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.menuid.php
 * Type:     function
 * Name:     menuid
 * Purpose:  displays the current menuid
 * -------------------------------------------------------------
 */

function smarty_function_menuid($params, &$smarty)
{
	global $checked;
	return $menuid = $checked->menuid;
}
?>
