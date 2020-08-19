<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.menuid_surl.php
 * Type:     function
 * Name:     menuid_surl
 * Purpose:  displays the surl for a given menu id
 * -------------------------------------------------------------
 */

function smarty_function_menuid_surl($params, &$smarty)
{
	global $db;
	global $cms;
	global $artikel;
	global $menu;
	
	if (isset($params['menuid']) AND ctype_digit((string)$params['menuid']))
	{
		$sql = sprintf("SELECT 
							menuname,
							lang_title
						FROM %s
						WHERE
							lang_id = '%d' AND 
							menuid_id = '%d'",
				$cms->papoo_menu_language,
				$cms->lang_id,
				$db->escape($params['menuid'])
				);
		$menuid_surl = $db->get_results($sql, ARRAY_A);
	
		if (count($menuid_surl))
		{
			$sp_urls = $smarty->get_template_vars(sp_urls);
			$surlstrenner = $smarty->get_template_vars(surlstrenner);
			$slash = $smarty->get_template_vars(slash);
			if ($sp_urls)
			{
				// build spURL
				$artikel->m_url = array();
				$urldat_1 = "";
				// get surl name of each menu item by the given menuID
				$artikel->get_verzeichnis($params['menuid'], "search");  //menu ID
				$artikel->m_url = array_reverse($artikel->m_url);
				// concat each menu item and sub item seperated by "/"
				if (!empty($artikel->m_url))
				{
					foreach ($artikel->m_url as $urldat)
					{
						$urldat_1 .= $menu->urlencode($urldat) . "/";
					}
					$return = '<a href="' . $slash . $sulrstrenner . $urldat_1 . '" title="';
					$return .= strip_tags($menuid_surl[0]['lang_title']);
					$return .= '" class="menuid_surl_a">';
					$return .= strip_tags($menuid_surl[0]['menuname']) . '</a>';
				}
				else
				{
					$smarty->trigger_error("menuid_surl: undefined surl", E_USER_WARNING);
					$return = "";
				}
			}
			else
			{
				$smarty->trigger_error("menuid_surl: spurls not activated", E_USER_WARNING);
				$return = "";
			}
		}
		else
		{
			$smarty->trigger_error("menuid_surl: unknown menuid", E_USER_WARNING);
			$return = "";
		}
	}
	else
	{
		$smarty->trigger_error("menuid_surl: missing parameter menuid", E_USER_WARNING);
		$return = "";
	}
	return $return;
}
?>
