<?php
$template = "template_one.html";

/**
 *
 */
function do_one_template()
{
	global $content;
	global $cms;
	global $db;
	global $checked;

	if (empty($checked->tinymce_lang_id)) $sprach_id = $cms->lang_id;
	else $sprach_id = $checked->tinymce_lang_id;
	// Daten rausholen
	$sql = "SELECT ctempl_content FROM ".$cms->tbname['papoo_content_templates']." WHERE ctempl_id='".$db->escape($checked->ctempl_id)."' AND  ctempl_lang_id='".$db->escape($sprach_id)."'";
	$result = $db->get_var($sql);
	
	$export = $result;
	$content->template['export_media'] = "nodecode:" . $export;
}

/**
 * var tinyMCEMediaList = new Array(
 * // Name, URL
 * ["Some Flash 1", "test1.swf"],
 * ["Some Flash 2", "test2.swf"]
 * );
 */
// benötigte Dateien einbauen
require_once "./all_inc.php";
