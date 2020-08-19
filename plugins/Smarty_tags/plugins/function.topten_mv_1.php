<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.topten_mv_1.php
 * Type:     function
 * Name:     topten
 * Purpose:  displays the most visited contents of mv_id 3
 * -------------------------------------------------------------
 */

function smarty_function_topten_mv_1($params, &$smarty)
{
	global $db;
	global $cms;
	
	$limit = empty($params['limit']) ? 10 : $params['limit'];
	
	$sql = sprintf("SELECT T1.mv_id,
							T1.mv_content_id,
							T1.counter,
							T2.Geschftsname_41 
							FROM %s T1
							INNER JOIN %s T2 ON (T1.mv_content_id = T2.mv_content_id)
							WHERE T1.mv_id = 3
							ORDER BY counter DESC
							LIMIT %d",
							$cms->tbname['papoo_mv_click_count'],
							$cms->tbname['papoo_mv_content_3'],
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
		$return = '<div id="topten_mv_1" class="modul">';
		$return .= '<div id="topten_mv_1_bg" class="modul">';
		$return .= '<span>Werkst&auml;tten Topten</span>';
		$return .= '<ol class="topten_ol">' . chr(13);
		for ($i = 0; $i < $limit; $i++)
		{
			if ($i == ($limit - 1)) // for special css classes
			{
				$return .= '<li class="topten_li last_topten_li"><span>' . $topten[$i]['counter'];
				$return .= ' Aufrufe:</span><br /><a class="topten_a last_topten_a" href="';
			}
			else
			{
				$return .= '<li class="topten_li"><span>' . $topten[$i]['counter'];
				$return .= ' Aufrufe:</span><br /><a class="topten_a" href="';
			}

			$return .= $slash . 'plugin.php?menuid=39&template=mv/templates/mv_show_front.html&mv_id=3&extern_meta=x&mv_content_id=' . $topten[$i]['mv_content_id'] . '" title="';
			$return .= $smarty->get_template_vars(message_2012) . '&quot;' . strip_tags($topten[$i]['Geschftsname_41']) . '&quot; ';
			$return .= $smarty->get_template_vars(message_2013) . '">' . strip_tags($topten[$i]['Geschftsname_41']) . '</a></li>' . chr(13);
		}
		$return .= "</ol></div></div>" . chr(13);
	}
	else
	{
		$smarty->trigger_error("topten: No articles found", E_USER_WARNING);
		$return = "";
	}
	return $return;
}
?>
