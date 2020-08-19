<?php

/**
 * Class RandomImages
 */
class RandomImages
{
	/**
	 * RandomImages constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		$this->set_backend_message();

		if (!defined("admin") || ($_GET['is_lp'] == 1)) {
			global $output;
			if (strstr($output, "#randomImages")) {
				$output = $this->create_image_list($output);
			}
		}
	}

	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][] = "Skript Random Images";
		$this->content->template['plugin_cm_body']['de'][] = "Mit diesem kleinen Skript kann man an beliebiger Stelle im Inhalte eine Liste von zufällig ausgewählten Bildern ausgeben lassen. Die Syntax lautet<br /><strong>#randomImages_X_Y#</strong><br />Wobei X die ID der Bilder-Kategorie und Y die Anzahl der Bilder bezeichnet.";
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * Ersetzt alle Platzhalter mit dem html der jeweiligen Bilderliste
	 * @param string $inhalt
	 *
	 * @return mixed|string
	 */
	function create_image_list($inhalt = "")
	{
		preg_match_all("|#randomImages(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;

		foreach ($ausgabe['1'] as $dat) {
			$ndat = explode("_", $dat);

			$cat_id = $ndat['1'];
			$no = $ndat['2'];

			$images = $this->get_images_from_db($ndat['1'], $ndat['2']);

			$html  = '<ul class="image-list img-cat-id-'.$ndat['1'].' no-'.$no.'">';
			foreach($images as $image) {
				$html .= '<li>'.$image['code'].'</li>';
			}
			$html .= '</ul>';

			$inhalt = str_ireplace($ausgabe['0'][$i], $html, $inhalt);
			$i++;
		}

		$inhalt = "" . $inhalt;
		return $inhalt;
	}

	/**
	 * Liefert eine Liste von zufällig ausgewählten Bildern aus einer Kategorie
	 * @param $cat_id mixed Die ID der Bilder-Kategorie
	 * @param $number_of_images
	 *
	 * @return array
	 */
	function get_images_from_db($cat_id, $number_of_images)
	{
		if (!empty($this->cms->tbname['papoo_images'])) {
			$sql = sprintf("SELECT image_id AS id, image_name AS filename, image_width as width, image_height AS height
						    FROM %s
						    WHERE image_dir = '%s'
                            ORDER BY rand()
                            LIMIT " . $number_of_images,
				$this->cms->tbname['papoo_images'],
				$cat_id
			);
			$images = $this->db->get_results($sql, ARRAY_A);
		}

		foreach ($images as $k => $v) {
			$sql = sprintf("SELECT alt, title
                            FROM %s
                            WHERE lan_image_id = '%s'
                            AND lang_id = '%d'
                            LIMIT 1",

				$this->cms->tbname['papoo_language_image'],
				$v['id'],
				$this->cms->lang_id
			);

			$image_metadata = $this->db->get_row($sql, ARRAY_A);

			$images[$k]['alt'] = $image_metadata['alt'];
			$images[$k]['title'] = $image_metadata['title'];

			$src   = ' src="' . PAPOO_WEB_PFAD . '/images/' . $images[$k]['filename'] . '"';
			$width = ' width="' . $images[$k]['width'] . '"';
			$height = ' height="' . $images[$k]['height'] . '"';
			$alt = ' alt="' . $images[$k]['alt'] . '"';
			$title = ' title="' . $images[$k]['title'] . '"';

			$images[$k]['code'] = '<img' . $src . $width . $height . $alt . $title . ' />';
		}
		return $images;
	}
}

$randomImages = new RandomImages();
