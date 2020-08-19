<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.page_top.php
 * Type:     function
 * Name:     page_top
 * Purpose:  displays a link to the top of the current page
 * -------------------------------------------------------------
 */

function smarty_function_page_top($params, &$smarty)
{
	$msg = $smarty->get_template_vars(message_2024);
	return '<a href="#top" accesskey="1" title="' . $msg . '">' . $msg . '</a>';
}
?>
