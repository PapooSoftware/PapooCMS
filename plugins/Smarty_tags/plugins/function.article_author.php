<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.article_author.php
 * Type:     function
 * Name:     article_author
 * Purpose:  displays the author of the current article
 * -------------------------------------------------------------
 */

function smarty_function_article_author($params, &$smarty)
{
	global $db;
	global $cms;
	global $checked;

	if ($checked->reporeid OR $checked->menuid)
	{
		$where = $checked->reporeid ? "T1.reporeid = " . $checked->reporeid : "T1.cattextid = " .  $checked->menuid;
		$sql = sprintf("SELECT 
						T3.username,
						T2.anzeig_autor
					FROM %s T1
						INNER JOIN %s T3 ON (T1.dokuser = T3.userid),
						%s T2
					WHERE " . $where,
					$cms->papoo_repore,
					$cms->papoo_user,
					$cms->papoo_daten,
					$db->escape($checked->reporeid)
					);
		$article_author = $db->get_results($sql, ARRAY_A);
		$return = $article_author[0]['anzeig_autor'] ? $article_author[0]['username'] : "";
	}
	else
	{
		$smarty->trigger_error("article_author: current menuid/reporeid not given", E_USER_WARNING);
		$return = "";
	}
	return $return;
}
?>
