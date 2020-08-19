<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.newest.php
 * Type:     function
 * Name:     newest
 * Purpose:  displays the most visited sites
 * -------------------------------------------------------------
 */

function smarty_function_newest($params, &$smarty)
{
	global $db;
	global $cms;
	global $artikel;
	global $menu;
	
	$limit = empty($params['limit']) ? 10 : $params['limit'];
	
	$sql = sprintf("SELECT 
  						T2.header,
						T2.lan_repore_id,
						T2.url_header,
						T1.cattextid,
						T1.timestamp
					FROM %s T2
  						INNER JOIN %s T1 ON (T2.lan_repore_id = T1.reporeID)
					WHERE
  						T2.lang_id = '%d' AND 
  						T1.publish_yn = '1'
					ORDER BY T1.timestamp DESC
					LIMIT %d",
			$cms->papoo_language_article,
			$cms->papoo_repore,
			$cms->lang_id,
			$db->escape($limit)
			);
	$newest = $db->get_results($sql, ARRAY_A);

	if (count($newest))
	{
		$limit = count($newest) < $limit ? count($newest) : $limit;

		$sp_urls = $smarty->get_template_vars(sp_urls);
		$surlstrenner = $smarty->get_template_vars(surlstrenner);
		$slash = $smarty->get_template_vars(slash);
		$frag_connect = $smarty->get_template_vars(frag_connect);
		$gleich_connect = $smarty->get_template_vars(gleich_connect);
		$plus_connect = $smarty->get_template_vars(plus_connect);

		$return = '<ol class="newest_ol">' . chr(13);
		for ($i = 0; $i < $limit; $i++)
		{
			// spURL aufbauen
			$artikel->m_url = array();
			$urldat_1 = "";
			// get surl name of each menu item by the given menuID
			$artikel->get_verzeichnis($newest[$i]['cattextid'], "search");  //menu ID
			$artikel->m_url = array_reverse($artikel->m_url);
			// concat each menu item and sub item seperated by "/"
			if (!empty($artikel->m_url))
			{
				foreach ($artikel->m_url as $urldat)
				{
					$urldat_1 .= $menu->urlencode($urldat) . "/";
				}
			}
			// add filename and suffix
			$header_url = $urldat_1 . $newest[$i]['url_header'] . ".html";
			if ($cms->mod_rewrite == "2") $index = "index";
			else $index = "index.php";
			if (substr($newest[$i]['timestamp'], 4, 1) == "-")
			{
				$date_time = explode(" ", $newest[$i]['timestamp']);
				$date = explode("-", $date_time[0]);
				$ttmmjjjhhmmss = $date[2] . "." . $date[1] . "." . $date[0] . " " . $date_time[1];#echo $ttmmjjjhhmmss;
			}
			else $ttmmjjjhhmmss = $newest[$i]['timestamp'];
			if ($sp_urls =="ok")
			{
				$return .= '<li class="newest_li">' . $ttmmjjjhhmmss . '<br /><a href="' . $slash . $sulrstrenner . $header_url . '" title="';
				$return .= $smarty->get_template_vars(message_2012) . '&quot;' . strip_tags($newest[$i]['header']);
				$return .= '&quot; ' . $smarty->get_template_vars(message_2013) . '" class="newest_a">';
				$return .= strip_tags($newest[$i]['header']) . '</a></li>' . chr(13);
			}
			else
			{
				$return .= '<li class="newest_li">' . $ttmmjjjhhmmss . '<br /><a title="' . $smarty->get_template_vars(message_2012) . '&quot;';
				$return .= strip_tags($newest[$i]['header']) . '&quot; ' . $smarty->get_template_vars(message_2013) . '" href="';
				$return .= $slash . $index . $frag_connect . 'menuid' . $gleich_connect . $newest[$i]['cattextid'];
				$return .= $plus_connect . 'reporeid' . $gleich_connect . $newest[$i]['lan_repore_id'] . '" class="newest_a">';
				$return .= strip_tags($newest[$i]['header']) . '</a></li>' . chr(13);
			}
		}
		$return .= "</ol>" . chr(13);
	}
	else
	{
		$smarty->trigger_error("newest: No articles found", E_USER_WARNING);
		$return = "";
	}
	return $return;
}
?>
