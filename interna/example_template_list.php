<?php
$template = "template_list.html";

/**
 *
 */
function do_list_templates()
{
	global $content;
	global $cms;
	global $db;
	global $checked;

	if (empty($checked->tinymce_lang_id)) {
		$sprach_id = $cms->lang_id;
	} else {
		$sprach_id = $checked->tinymce_lang_id;
	}
	// Daten rausholen
	$sql = "SELECT * FROM " . $cms->tbname['papoo_content_templates'] . " WHERE  ctempl_lang_id='" . $db->escape($sprach_id) . "' ORDER BY ctempl_name ASC ";
	$result = $db->get_results($sql, ARRAY_A);

	$liste = $result;

	$count = count($liste);
	$i = 0;

	$export = 'var tinyMCETemplateList = [';
	$dir = "";


	$verz = PAPOO_WEB_PFAD . "/video/";
	// Einträge durchgehen
	if (!empty($liste)) {
		foreach ($liste as $video) {
			// Wenn ein neues Verzeichnis

			$i++;

			$export .= '["' . str_replace('"', ' ', ' ' . $video['ctempl_name']) . ' ","' . str_replace('"', '&quot;', './one_template.php?ctempl_id=' . $video['ctempl_id']) . '"]';
			if ($i < $count) {
				$export .= ",";
			}
		}
	}
	$export .= "];";
	$content->template['export_media'] = "nodecode:" . $export;
}

// benötigte Dateien einbauen
require_once "./all_inc.php";
