<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function._inhalt_mitte.php
 * Type:     function
 * Name:     _inhalt_mitte
 * Purpose:  displays all modules of the content column
 * -------------------------------------------------------------
 */

function smarty_function__inhalt_mitte($params, &$smarty)
{		
	$module = $smarty->get_template_vars(module);
	$left_column = ArraySortByField_mitte($module[3], "mod_order_id");
	foreach ($left_column AS $key)
	{
		$smarty->display($key['mod_template']);
	}
	$anzeig_versende = $smarty->get_template_vars(anzeig_versende);
	$anzeig_drucken = $smarty->get_template_vars(anzeig_drucken);
	$editlink = $smarty->get_template_vars(editlink);
	if ($anzeig_versende OR $anzeig_drucken OR $editlink)
	{
		$smarty->display('_module_intern/mod_artikel_optionen.html');
	}
	$smarty->display('_module_intern/mod_back_top.html');
}

function ArraySortByField_mitte($src_array, $field)
{
	$sortArr = array();
	// collect all data of the given field
	foreach ($src_array as $key => $value)
	{
		$sortArr[$key] = $value[$field];
	}
	asort($sortArr);  // sort the data asc
	$resultArr = array();
	// insert all other fields
	foreach ($sortArr as $key => $value)
	{
		$resultArr[$key] = $src_array[$key];
	}
	return $resultArr;
}
?>
