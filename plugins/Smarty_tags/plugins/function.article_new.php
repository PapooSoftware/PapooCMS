<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.article_new.php
 * Type:     function
 * Name:     article_new
 * Purpose:  displays a link for "new article"
 * -------------------------------------------------------------
 */

function smarty_function_article_new($params, &$smarty)
{		
	$loggedin = $smarty->get_template_vars(loggedin);
	if ($loggedin == "user_ok")
	{
		$slash = $smarty->get_template_vars(slash);
		$return = '<a rel="nofollow" href="' . $slash . 'interna/artikel.php?menuid=10"';
		$return .= ' title="Einen neuen Artikel erstellen" target="_blank">';
		$return .= 'Einen neuen Artikel erstellen';
		$return .= '</a>';
		return $return;
	}
}
?>
