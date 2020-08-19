<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.topten.php
 * Type:     function
 * Name:     topten
 * Purpose:  displays the most visited sites
 * -------------------------------------------------------------
 */

function smarty_function_topten($params, &$smarty)
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
						T1.`count`,
						T1.cattextid
					FROM %s T2
  						INNER JOIN %s T1 ON (T2.lan_repore_id = T1.reporeID)
					WHERE
  						T2.lang_id = '%d' AND 
  						T1.publish_yn = '1'
					ORDER BY T1.`count` DESC
					LIMIT %d",
			$cms->papoo_language_article,
			$cms->papoo_repore,
			$cms->lang_id,
			$db->escape($limit)
			);
	$topten = $db->get_results($sql, ARRAY_A);

	if (count($topten))
	{
		$limit = count($topten) < $limit ? count($topten) : $limit;

		$slash = $smarty->get_template_vars(slash);
		$sp_urls = $smarty->get_template_vars(sp_urls);
		$surlstrenner = $smarty->get_template_vars(surlstrenner);
		$frag_connect = $smarty->get_template_vars(frag_connect);
		$gleich_connect = $smarty->get_template_vars(gleich_connect);
		$plus_connect = $smarty->get_template_vars(plus_connect);
		
		$return = '<ol class="topten_ol">' . chr(13);
		for ($i = 0; $i < $limit; $i++)
		{
			if ($sp_urls =="ok")
			{
				// spURL aufbauen
				$artikel->m_url = array();
				$urldat_1 = "";
				// get surl name of each menu item by the given menuID
				$artikel->get_verzeichnis($topten[$i]['cattextid'], "search");
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
				$header_url = $urldat_1 . $topten[$i]['url_header'] . ".html";
				if ($cms->mod_rewrite == "2") $index = "index";
				else $index = "index.php";
				
				// build HTML
				if ($i == ($limit - 1)) // for special css classes
				{
					$return .= '<li class="topten_li last_topten_li"><span>' . $topten[$i]['count'];
					$return .= ' Aufrufe:</span><br /><a class="topten_a last_topten_a" href="';
				}
				else
				{
					$return .= '<li class="topten_li"><span>' . $topten[$i]['count'];
					$return .= ' Aufrufe:</span><br /><a class="topten_a" href="';
				}
				$return .= $slash . $surlstrenner . $header_url . '" title="' . $smarty->get_template_vars(message_2012);
				$return .= '&quot;' . strip_tags($topten[$i]['header']) . '&quot; ' . $smarty->get_template_vars(message_2013);
				$return .= '">' . strip_tags($topten[$i]['header']) . '</a></li>' . chr(13);
			}
			else
			{
				if ($i == ($limit - 1)) // for special css classes
				{
					$return .= '<li class="topten_li last_topten_li"><span>' . $topten[$i]['count'];
					$return .= ' Aufrufe:</span><br /><a class="topten_a last_topten_a" href="';
				}
				else
				{
					$return .= '<li class="topten_li"><span>' . $topten[$i]['count'];
					$return .= ' Aufrufe:</span><br /><a class="topten_a" href="';
				}
				$return .= $slash . $index . $frag_connect . 'menuid' . $gleich_connect . $topten[$i]['cattextid'];
				$return .= $plus_connect . 'reporeid' . $gleich_connect . $topten[$i]['lan_repore_id'] . '" title="';
				$return .= $smarty->get_template_vars(message_2012) . '&quot;' . strip_tags($topten[$i]['header']) . '&quot; ';
				$return .= $smarty->get_template_vars(message_2013) . '">' . strip_tags($topten[$i]['header']) . '</a></li>' . chr(13);
			}
		}
		$return .= "</ol>" . chr(13);
	}
	else
	{
		$smarty->trigger_error("topten: No articles found", E_USER_WARNING);
		$return = "";
	}
	return $return;
}
?>
