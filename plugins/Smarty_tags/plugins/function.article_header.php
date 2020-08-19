<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.article_header.php
 * Type:     function
 * Name:     article_header
 * Purpose:  displays the article_header of the article
 * -------------------------------------------------------------
 */

function smarty_function_article_header($params, &$smarty)
{
	$startseite = $smarty->get_template_vars(startseite);
	// Es gibt für die Starteite keine Überschrift in der DB. Diese steht im Artikel, wenn dort vorhanden.
	if ($startseite == "ok") $return = "";
	else
	{
		$table_data = $smarty->get_template_vars(table_data);
		$return = !empty($table_data[0]['uberschrift']) ? $table_data[0]['uberschrift'] : "";
	}
	return $return;
}
?>
