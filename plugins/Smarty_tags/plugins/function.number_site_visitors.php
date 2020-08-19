<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.number_site_visitors.php
 * Type:     function
 * Name:     number_site_visitors
 * Purpose:  displays the number of site visitors
 * -------------------------------------------------------------
 */

function smarty_function_number_site_visitors($params, &$smarty)
{
	global $db;
	global $cms;

	$sql = sprintf("SELECT anzeig_besucher,
							gesamtcount
					FROM %s",
			$cms->papoo_daten
			);
	$number_site_visitors = $db->get_results($sql, ARRAY_A);
	$return = $number_site_visitors[0]['anzeig_besucher'] ? $number_site_visitors[0]['gesamtcount'] : "";
	return $return;
}
?>
