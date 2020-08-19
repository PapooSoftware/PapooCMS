<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.title.php
 * Type:     function
 * Name:     title
 * Purpose:  displays the META title tag
 * -------------------------------------------------------------
 */

function smarty_function_title($params, &$smarty)
{
	global $db;
	global $cms;
	$startseite = $smarty->get_template_vars(startseite);
	if ($startseite == "ok")
	{
		$sql = sprintf("SELECT seitentitle FROM %s
						WHERE lang_id = '%d'",
				$cms->papoo_language_stamm,
				$cms->lang_id
				);
		$return = $db->get_var($sql);
	}
	else
	{
		$table_data = $smarty->get_template_vars(table_data);
		$sql = sprintf("SELECT lan_metatitel FROM %s
						WHERE lang_id = '%d'
						AND lan_repore_id = '%d'",
				$cms->papoo_language_article,
				$cms->lang_id,
				$db->escape($table_data[0]['reporeid'])
				);
		$return = $db->get_var($sql);
	}
	return $return;
}
?>
