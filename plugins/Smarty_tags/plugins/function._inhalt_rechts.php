<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function._inhalt_rechts.php
 * Type:     function
 * Name:     _inhalt_rechts
 * Purpose:  displays all modules of the right column
 * -------------------------------------------------------------
 */

function smarty_function__inhalt_rechts($params, &$smarty)
{
	$module = $smarty->get_template_vars(module);
	$left_column = ArraySortByField_rechts($module[4], "mod_order_id");
	foreach ($left_column AS $key)
	{
		$smarty->display($key['mod_template']);
	}
}

function ArraySortByField_rechts($src_array, $field)
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
