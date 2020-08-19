<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.number_page_views.php
 * Type:     function
 * Name:     number_page_views
 * Purpose:  displays the number of page views for the current page
 * -------------------------------------------------------------
 */

function smarty_function_number_page_views($params, &$smarty)
{
	global $db;
	global $cms;
	global $checked;

	$where = $checked->reporeid ? "T2.reporeid = " . $checked->reporeid : "T2.cattextid = " .  $checked->menuid;
	if ($checked->reporeid OR $checked->menuid)
	{
		$sql = sprintf("SELECT 
						T2.count,
						T1.anzeig_pageviews
					FROM %s T1, %s T2
					WHERE " . $where,
					$cms->papoo_daten,
					$cms->papoo_repore
					);
		$number_page_views = $db->get_results($sql, ARRAY_A);
		$return = $number_page_views[0]['anzeig_pageviews'] ? $number_page_views[0]['count'] : "";
	}
	else
	{
		$smarty->trigger_error("number_page_views: no current menuid and reporeid", E_USER_WARNING);
		$return = "";
	}
	return $return;
}
?>
