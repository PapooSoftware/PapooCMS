<?php
$template = "media_list.html";

/**
 *
 */
function do_list_medien()
{
	global $content;
	global $video_class;
	global $cms;
	global $checked;

	if (empty($checked->tinymce_lang_id)) {
		$sprach_id = $cms->lang_id;
	}
	else {
		$sprach_id = $checked->tinymce_lang_id;
	}
	// Daten rausholen
	$liste = $video_class->get_video_list("all");

	$catliste = $video_class->get_cat_list("all");
	$i = 0;
	foreach ($liste as $dat) {
		foreach ($catliste as $cat) {
			if ($dat['video_kat'] == $cat['video_cat_id']) {
				$liste[$i]['video_kat'] = $cat['video_cat_name'];
			}
		}
		$i++;
	}
	$count = count($liste);
	$i = 0;

	$export = 'var tinyMCEMediaList = new Array(';
	$dir = "";


	$verz = PAPOO_WEB_PFAD . "/video/";
	// Einträge durchgehen
	if (!empty($liste)) {
		foreach ($liste as $video) {
			// Wenn ein neues Verzeichnis
			if ($dir != $video['video_kat'] or $i == 0) {
				$export .= '["------ ' . str_replace('"', ' ', ' ' . $video['video_kat']) . ' ------",""]';
				$export .= ",";
			}
			$i++;
			$export .= '["' . str_replace('"', ' ', ' ' . $video['video_alt']) . ' ","' . $verz . '' . str_replace('"', ' ', '' . $video['video_file_name']) . '"]';
			$dir = $video['video_kat'];

			if ($i < $count) {
				$export .= ",";
			}
		}
	}
	$export .= ");";
	$content->template['export_media'] = "nodecode:" . $export;

	// Setze Header, damit der Code im Browser ausgeführt wird; wird z. B. bei MIME-Typen text/html u. U. geblockt
	header('Content-Type: application/javascript; charset=utf-8');
}

// benötigte Dateien einbauen
require_once "./all_inc.php";
