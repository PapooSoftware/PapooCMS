<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.google.php
 * Type:     function
 * Name:     google
 * Purpose:  Google search (this site or any domain)
 * -------------------------------------------------------------
 */

function smarty_function_google($params, &$smarty)
{
	$button_text = empty($params['button_text']) ? 'Site search' : $params['button_text'];
	$domain = empty($params['domain']) ? $_SERVER['SERVER_NAME'] : $params['domain'];
	$return = '<form method="get" action="http://www.google.com/search">' . chr(13);
	$return .= '<input type="hidden" name="sitesearch" value="' . $domain . '" />' . chr(13);
	$return .= '<input type="text" id="google_search" name="q" value="" />' . chr(13);
	$return .= '<input type="submit" id="google_submit" value="' . $button_text . '" /></form>' . chr(13);
	return $return;
}
?>
