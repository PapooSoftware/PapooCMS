<?php
/*
 * Smarty plugin
 * ----------------------------------------------------------------------------
 * File:     function.papoo_news.php
 * Type:     function
 * Name:     papoo_news
 * Purpose:  displays the Papoo News published at http://www.papoo.de/cms-blog/
 * ----------------------------------------------------------------------------
 */

function smarty_function_papoo_news($params, &$smarty)
{
	require_once (PAPOO_ABS_PFAD."/lib/classes/extlib/Snoopy.class.inc.php");
	$url = "http://www.papoo.de/cms-blog/";
	$snoopy = new Snoopy();
	$snoopy->agent = "Smarty Tags";
	$snoopy->referer = $_SERVER["HTTP_REFERER"];
	$snoopy->fetch($url);
	$page = $snoopy->results;
	$page1 = explode('<a  id="artikel"></a>', $page);
	$page2 = explode('<!-- MODUL: weiter -->', $page1['1']);
	$output = $page2['0'];
	$output = str_ireplace("src=\"", "src=\"http://www.papoo.de", $output);
	$output = str_ireplace("href=\"", "href=\"http://www.papoo.de", $output);
	$output = str_ireplace("href=\"", "target=\"blank\" href=\"", $output);
	$output = strip_tags($output, "<h2><a><img><br /><p><div><span>");
	if (empty($output)) $output = "Keine Verbindung zum Server www.papoo.de";
	return $output;
}
?>
