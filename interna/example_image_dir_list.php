<?php
/**
 * #####################################
 * # CMS Papoo                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.2                  #
 * #####################################
 */

$template = "image_list.html";
/**
function do_list_image()
{
		global $content;
		global $intern_artikel;
		
		// Name, URL alt titele herausholen
		$intern_artikel->get_images();
		// ANzahl der Einträge
		$count = count($content->template['bild_data']);
		$i = 0;

		$export = 'var tinyMCEImageDirList = new Array(';
		// Einträge durchgehen
		if (!empty($content->template['bild_data'])) {
				foreach ($content->template['bild_data'] as $bild) {
						$i++;
						$export .= '[" ' . htmlspecialchars(str_replace('"', '', $bild['image_alt'])) . ' ", "../images/' . $bild['image_name'] . '", "' . htmlspecialchars(str_replace('"', '', $bild['image_alt'])) . '","' . htmlspecialchars(str_replace('"', '', $bild['image_title'])) . '"]';
						if ($i < $count) {
								$export .= ",";
						}
				} ;
		}

		$export .= ");";
		$content->template['export_images'] = "nodecode:" . $export;
}
// benötigte Dateien einbauen
require_once "./all_inc.php";
*/