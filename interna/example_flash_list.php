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
	if (!empty($liste)) {
		foreach ($liste as $dat) {
			if (!empty($catliste)) {
				foreach ($catliste as $cat) {
					if ($dat['video_kat'] == $cat['video_cat_id']) {
						$liste[$i]['video_kat'] = $cat['video_cat_name'];
					}
				}
			}
			$i++;
		}
	}

	$count = count($liste);
	$i = 0;

	$export = 'var tinyMCEflashList = new Array(';
	$dir = "";

	$verz = PAPOO_WEB_PFAD . "/video/";
	// Einträge durchgehen
	if (!empty($liste)) {
		foreach ($liste as $video) {
			// Wenn ein neues Verzeichnis
			if ($dir != $video['video_kat'] or ($i == 0 && $dir != $video['video_kat'])) {
				$export .= '["------ ' . str_replace('"', ' ', ' ' . $video['video_kat']) .
					' ------",""]';
				$export .= ",";
			}
			$i++;
			if (strstr($video['video_file_name'], '.flv')) {
				$export .= '["' . str_replace('"', ' ', ' ' . $video['video_alt']) . ' ","' . $verz .
					'' . str_replace('"', ' ', '' . $video['video_file_name']) . '"]';
				if ($i < $count) {
					$export .= ",";
				}
			}
			$dir = $video['video_kat'];
		}
	}
	$export .= ");";
	$export = str_replace(",);", ");", $export);
	$content->template['export_media'] = "nodecode:" . $export;
}

// benötigte Dateien einbauen
require_once "./all_inc.php";