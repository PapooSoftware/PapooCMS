<?php

namespace FixeModule;
/**
 * Class Controller
 *
 * @package FixeModule
 */
#[AllowDynamicProperties]
class Controller
{
	protected $checked;
	protected $view;
	protected $content;

	public function __construct()
	{
		global $checked;
		$this->checked = &$checked;
		global $content;
		$this->content = &$content;
		$this->view = new View();
		$this->base_url = "plugin.php?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template;
	}

	/**
	 * @param string $to
	 */
	public function redirect($to = "")
	{
		header('Location: ' . $this->base_url . $to);
	}

	/**
	 * trimmt einen string so zurecht, dass er als Smarty-Variable durchgeht. (Kleinbuchstaben, Zahlen und Unterstriche)
	 *
	 * @param $text
	 * @return mixed|string
	 */
	public function to_smarty($text)
	{
		$umlaute = array(
			"ä" => "ae",
			"Ä" => "ae",
			"ö" => "oe",
			"Ö" => "oe",
			"ü" => "ue",
			"Ü" => "ue",
			"ß" => "ss",
		);
		$text = str_replace(array_keys($umlaute), $umlaute, $text);

		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '_', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '_');

		// remove duplicate -
		$text = preg_replace('~-+~', '_', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
			return 'n-a';
		}

		return $text;
	}

	/**
	 * Nimmt ein <img>-tag und gibt ein Array mit den verschiedenen Attributen zurück.
	 * Das ursprüngliche tag wird auch mit dem key "tag" zurückgegeben.
	 *
	 * @param $img_tag
	 * @return array
	 */
	public function parse_img_tag($img_tag) {
		$img_tag = strip_tags($img_tag, "<img>");

		if (!$img_tag) {
			return null;
		}

		$doc = new \DOMDocument();
		@$doc->loadHTML($img_tag);
		$img = $doc->getElementsByTagName('img')[0];

		if ($img) {
			$return['tag'] = $img_tag;

			if ($img->getAttribute('src')) {
				$return['src'] = $img->getAttribute('src');
				$split_filename = explode("/", $return['src']);
				$return['filename'] = $split_filename[sizeof($split_filename)-1];
			}

			if ($img->getAttribute('width')) {
				$return['width'] = $img->getAttribute('width');
			}

			if ($img->getAttribute('height')) {
				$return['height'] = $img->getAttribute('height');
			}

			if ($img->getAttribute('alt')) {
				$return['alt'] = $img->getAttribute('alt');
			}

			if ($img->getAttribute('title')) {
				$return['title'] = $img->getAttribute('title');
			}
		}

		return $return;
	}
}
