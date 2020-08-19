<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.count_clicks_mv_show_front.php
 * Type:     function
 * Name:     content
 * Purpose:  counts clicks for each mv_content_id of mv_id 3
 * -------------------------------------------------------------
 */

function smarty_function_count_clicks_mv_show_front($params, &$smarty)
{
	$mv_id = $smarty->get_template_vars(mv_id);
	if ($mv_id == 3) // Nur für Werkstätten
	{
		$mv_content_id = $smarty->get_template_vars(mv_content_id);
		if(isset($_COOKIE['fahrradwerkstatt'][$mv_id][$mv_content_id])) return;
		else
		{
			#$strHost = @gethostbyaddr($db->escape($_SERVER["REMOTE_ADDR"]));
			#$strIP = "";
			#$strIP_regExp = "/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/";
			#if (isset($_SERVER["HTTP_X_FORWARDED_FOR"] )
			#	&& preg_match($strIP_regExp, $_SERVER["HTTP_X_FORWARDED_FOR"])) $strIP = $_SERVER["HTTP_X_FORWARDED_FOR"];
			#elseif (isset($_SERVER["HTTP_CLIENT_IP"])
			#&& preg_match($strIP_regExp, $_SERVER["HTTP_CLIENT_IP"])) $strIP = $_SERVER["HTTP_CLIENT_IP"];
			#else $strIP = $_SERVER["REMOTE_ADDR"];
			setcookie("fahrradwerkstatt[$mv_id][$mv_content_id]", "nothing", time() + (2 * 3600)); // 2 Stunden
			global $db;
			global $cms;
			$sql = sprintf("SELECT * 
							FROM %s
							WHERE mv_id = '%d' 
							AND mv_content_id = '%d'",
							$cms->tbname['papoo_mv_click_count'],
							$db->escape($mv_id),
							$db->escape($mv_content_id)
							);
			$result = $db->get_results($sql, ARRAY_A);
			if (count($result))
			{
				$sql = sprintf("UPDATE %s 
								SET counter = counter + 1 
								WHERE mv_id = '%d' 
								AND mv_content_id = '%d'",
								$cms->tbname['papoo_mv_click_count'],
								$db->escape($mv_id),
								$db->escape($mv_content_id)
								);
				$db->query($sql);
			}
			else
			{
				$sql = sprintf("INSERT INTO %s 
								SET counter = 1, 
								mv_id = '%d', 
								mv_content_id = '%d'",
								$cms->tbname['papoo_mv_click_count'],
								$db->escape($mv_id),
								$db->escape($mv_content_id)
								);
				$db->query($sql);
			}
		}
	}
	return;
}
?>
