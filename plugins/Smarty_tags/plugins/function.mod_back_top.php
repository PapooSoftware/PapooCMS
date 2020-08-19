<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.mod_back_top.php
 * Type:     function
 * Name:     mod_back_top
 * Purpose:  displays links back and to top
 * -------------------------------------------------------------
 */

function smarty_function_mod_back_top($params, &$smarty)
{
	$return = '<div class="modul" id="mod_back_top" style="clear: both;">' . chr(13);
	// back
	$return .= '<div style="text-align:left; float: left; width: 48%;">' . chr(13);
	$return .= '<a href="javascript:history.go(-1)" id="linkbacktop_back">';
	$message_2196 = $smarty->get_template_vars(message_2196);
	$return .= $message_2196;
	$return .= '</a>' . chr(13);
	$return .= '<span class="ignore">.</span>' . chr(13);
	$return .= '</div>' . chr(13);
	// to top
	$return .= '<div style="text-align:right; float: right; width: 48%;">' . chr(13);
	$return .= '<a href="#top" accesskey="1" id="linkbacktop_top">';
	$message_2024 = $smarty->get_template_vars(message_2024);
	$return .= $message_2024;
	$return .= '</a>' . chr(13);
	$return .= '</div>' . chr(13);
	$return .= '</div>' . chr(13);
	return $return;
}
?>
