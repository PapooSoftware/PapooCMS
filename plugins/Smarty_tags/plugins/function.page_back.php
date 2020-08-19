<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.page_back.php
 * Type:     function
 * Name:     page_back
 * Purpose:  displays a link back to the previous page
 * -------------------------------------------------------------
 */

function smarty_function_page_back($params, &$smarty)
{
	$message_2196 = $smarty->get_template_vars(message_2196);
	return '<a id="linkbacktop_back" href="javascript:history.go(-1)" title="' . $message_2196 . '">' . $message_2196 . '</a>';
}
?>
