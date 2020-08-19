<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.mod_kopftext.php
 * Type:     function
 * Name:     mod_kopftext
 * Purpose:  displays the top head text
 * -------------------------------------------------------------
 */

function smarty_function_mod_kopftext($params, &$smarty)
{
	$return = '<div class="modul" id="mod_kopftext">' . chr(13);
	$return .= '<h1 class="toph1">';
	$link = $smarty->get_template_vars(link);
	$message_2000 = $smarty->get_template_vars(message_2000);
	$return .= '<a href="http://' . $link . '" accesskey="0" title="' . $message_2000 . $link . '">';
	$top_title = $smarty->get_template_vars(top_title);
	$retrun .= $top_title;
	$return .= '</a>';
	$return .= '</h1>' . chr(13);
	$return .= '</div>' . chr(13);
	return $return;
}
?>
