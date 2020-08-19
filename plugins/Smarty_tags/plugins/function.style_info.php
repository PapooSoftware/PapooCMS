<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.style_info.php
 * Type:     function
 * Name:     style_info
 * Purpose:  displays information about the standard style
 * -------------------------------------------------------------
 */

function smarty_function_style_info($params, &$smarty)
{
	global $db;
	global $cms;

	$sql = sprintf("SELECT style_id,
							style_name,
							style_pfad,
							style_cc,
							html_datei
					FROM %s
					WHERE standard_style = '1'",
			$cms->papoo_styles
			);
	$return = $db->get_results($sql, ARRAY_A);
	$smarty->assign("style_info", $return);
}
?>
