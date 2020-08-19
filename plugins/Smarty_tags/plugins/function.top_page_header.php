<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.top_page_header.php
 * Type:     function
 * Name:     top_page_header
 * Purpose:  displays the top page header defined at the first page
 * -------------------------------------------------------------
 */

function smarty_function_top_page_header($params, &$smarty)
{
	$sql = sprintf("SELECT head_title FROM %s
					WHERE lang_id = '%d'",
			$cms->papoo_language_stamm,
			$cms->lang_id
			);
	return = $db->get_var($sql);
}
?>
