<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.user_groups.php
 * Type:     function
 * Name:     user_groups
 * Purpose:  diesplays an article by a given reporeid
 * -------------------------------------------------------------
 */

function smarty_function_show_article($params, &$smarty)
{
	#ini_set('display_errors', 1);
	#error_reporting(E_ALL ^ E_NOTICE);

	global $db;
	global $cms;
	global $user;
	if ($params['reporeid'] == "")
	{
		$smarty->trigger_error("show_article: missing reporeid", E_USER_WARNING);
		$return = "";
	}
	else
	{
		$sql = sprintf("SELECT DISTINCT lan_article,
								header FROM %s T1
								INNER JOIN %s T2 ON (T1.lan_repore_id = T2.article_id)
								INNER JOIN %s T3 ON (T2.article_id = T3.reporeID)
								INNER JOIN %s T4 ON (T2.gruppeid_id = T4.gruppenid)
								INNER JOIN %s T5 ON (T4.userid = T5.userid)
								WHERE T3.publish_yn = 1 
								AND T3.allow_publish = 1 
								AND T3.publish_yn_intra = 0  
								AND T3.reporeID = '%d' 
								AND T1.lang_id = '%d' 
								AND T4.userid = '%d'",
								$cms->papoo_language_article,
								$cms->papoo_lookup_article,
								$cms->papoo_repore,
								$cms->papoo_lookup_ug,
								$cms->papoo_user,
								$db->escape($params['reporeid']),
								$cms->lang_id,
								$user->userid
				);
		$return = $db->get_results($sql, ARRAY_A);
	}
	$smarty->assign("header", $return[0]['header']);
	$smarty->assign("lan_article", $return[0]['lan_article']);	
	require_once $smarty->_get_plugin_filepath('function', 'content2');
	smarty_function_content2($params, &$smarty);
}
?>
