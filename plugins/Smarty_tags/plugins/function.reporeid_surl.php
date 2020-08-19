<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.reporeid_surl.php
 * Type:     function
 * Name:     reporeid_surl
 * Purpose:  displays the surl for a given reporeid (article ID)
 * -------------------------------------------------------------
 */

function smarty_function_reporeid_surl($params, &$smarty)
{
	global $db;
	global $cms;
	global $artikel;
	global $menu;
	
	if (isset($params['reporeid']) AND ctype_digit((string)$params['reporeid']))
	{
		$sql = sprintf("SELECT 
							T2.header,
							T3.menuid_id,
							T3.lang_title,
							T2.url_header
						FROM %s T1
							INNER JOIN %s T3 ON (T1.cattextid = T3.menuid_id)
							INNER JOIN %s T2 ON (T3.lang_id = T2.lang_id)
											AND (T1.reporeID = T2.lan_repore_id)
						WHERE
							T3.lang_id = '%d' AND 
							T1.reporeid = '%d'",
				$cms->papoo_repore,
				$cms->papoo_menu_language,
				$cms->papoo_language_article,
				$cms->lang_id,
				$db->escape($params['reporeid'])
				);
		$reporeid_surl = $db->get_results($sql, ARRAY_A);

		if (count($reporeid_surl))
		{
			$sp_urls = $smarty->get_template_vars(sp_urls);
			$surlstrenner = $smarty->get_template_vars(surlstrenner);
			$slash = $smarty->get_template_vars(slash);
			if ($sp_urls)
			{
				// spURL aufbauen
				$artikel->m_url = array();
				$urldat_1 = "";
				// get surl name of each menu item by the given menuID
				$artikel->get_verzeichnis($reporeid_surl[0]['menuid_id'], "search");  //menu ID
				$artikel->m_url = array_reverse($artikel->m_url);
				// concat each menu item and sub item seperated by "/"
				if (!empty($artikel->m_url))
				{
					foreach ($artikel->m_url as $urldat)
					{
						$urldat_1 .= $menu->urlencode($urldat) . "/";
					}
					// add filename and suffix
					$header_url = $urldat_1 . $reporeid_surl[0]['url_header'] . ".html";
					$return = '<a href="' . $slash . $sulrstrenner . $header_url . '" title="';
					$return .= $smarty->get_template_vars(message_2012) . '&quot;' . strip_tags($reporeid_surl[0]['header']);
					$return .= '&quot; ' . $smarty->get_template_vars(message_2013) . '" class="reporeid_surl_a">';
					$return .= strip_tags($reporeid_surl[0]['header']) . '</a>';
				}
				else
				{
					$smarty->trigger_error("reporeid_surl: undefined surl", E_USER_WARNING);
					$return = "";
				}
			}
			else
			{
				$smarty->trigger_error("reporeid_surl: spurls not activated", E_USER_WARNING);
				$return = "";
			}
		}
		else
		{
			$smarty->trigger_error("reporeid_surl: unknown reporeid", E_USER_WARNING);
			$return = "";
		}
	}
	else
	{
		$smarty->trigger_error("reporeid_surl: missing reporeid", E_USER_WARNING);
		$return = "";
	}
	return $return;
}
?>
