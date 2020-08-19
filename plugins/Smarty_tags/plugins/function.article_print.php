<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.article_print.php
 * Type:     function
 * Name:     article_print
 * Purpose:  displays the link for printing the current article
 * -------------------------------------------------------------
 */

function smarty_function_article_print($params, &$smarty)
{
	$slash = $smarty->get_template_vars(slash);
	$anzeig_drucken = $smarty->get_template_vars(anzeig_drucken);
	$urldatself = $smarty->get_template_vars(urldatself);
	if ($anzeig_drucken AND $urldatself)
	{
		// Druckversion dieses Artikels
		$urldatprint = $smarty->get_template_vars(urldatprint);
		$message_2016 = $smarty->get_template_vars(message_2016);
		$return = '<a rel="nofollow" href="' . $slash . $urldatself . '?' . $urldatprint . 'print=ok" title="' . $message_2016;
		$return .= '">';
		$return .= $message_2016;
		$return .= '</a>';
		return $return;
	}
}
?>
