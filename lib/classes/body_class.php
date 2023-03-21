<?php

/**
 * Class body_class
 */
#[AllowDynamicProperties]
class body_class
{
	/** @var null  */
	private $content = null;

	/**
	 * body_class constructor.
	 */
	public function __construct()
	{
		global $content;
		$this->content = &$content;

		global $checked;
		$this->checked = &$checked;

		$this->content->template['browser_class_string'] = $this->browser();
		$this->content->template['url_class_name'] = $this->url();
		$this->content->template['php_file'] = $this->phpFile();
		$this->content->template['extra_css_file'] = $this->extraCss();
		$this->content->template['repore_id_string'] = $this->reporeID();
		$this->content->template['mv_content_id_string'] = $this->mvContentID();
		$this->content->template['is_search'] = $this->isSearch();
	}

	/**
	 * @param $text
	 * @return false|string|string[]|null
	 */
	private function toSlug($text)
	{
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
			return 'n-a';
		}
		return $text;
	}

	/**
	 * @return string
	 */
	private function browser()
	{
		$browser = new Browser();
		$browser_classes = array();
		$browser_classes[] = 'browser-name-' . $this->toSlug($browser->getBrowser());
		$browser_classes[] = 'browser-platform-' . $this->toSlug($browser->getPlatform());
		$browser_classes[] = 'browser-version-' . $this->toSlug($browser->getVersion());
		if ($browser->isMobile()) {
			$browser_classes[] = "browser-is-mobile";
		}
		if ($browser->isTablet()) {
			$browser_classes[] = "browser-is-tablet";
		}
		if ($browser->isRobot()) {
			$browser_classes[] = "browser-is-robot";
		}
		if ($browser->isFacebook()) {
			$browser_classes[] = "browser-is-facebook";
		}
		if ($browser->isChromeFrame()) {
			$browser_classes[] = "browser-is-chrome-frame";
		}
		return implode(" ", $browser_classes);
	}

	/**
	 * @return mixed|string|string[]|null
	 */
	private function url()
	{
		// URL umschreiben und ans template übergeben
		$url = $this->content->template['languageget'][0]['lang_link'];
		$url = str_replace($this->content->template['webverzeichnis'], "", $url);
		$url = preg_replace("/[^a-zA-Z0-9_]/", "-", $url);
		$url = rtrim($url, "-");
		return $url;
	}

	/**
	 * @return mixed|string|string[]|null
	 */
	private function phpFile()
	{
		// aktive PHP-Datei raussuchen und ans template übergeben
		$url = $this->content->template['urldatself'];
		$url = str_replace($this->content->template['webverzeichnis'], "", $url);
		$url = preg_replace("/[^a-zA-Z0-9_]/", "-", $url);
		$url = rtrim($url, "-");
		return $url;
	}

	/**
	 * @return string|string[]|null
	 */
	private function extraCss()
	{
		// extra CSS-Datei raussuchen und ans template übergeben
		if (!isset($this->content->template['extra_css'])) {
			$this->content->template['extra_css'] = null;
		}
		$css_file = $this->content->template['extra_css'];
		$css_class = strtolower(str_replace(".css", "-css", $css_file));
		$css_class = preg_replace("/[^a-z-]/", "-", $css_class);
		return $css_class;
	}

	/**
	 * @return string|null
	 */
	private function reporeID()
	{
		if (isset($this->checked->reporeid) && $this->checked->reporeid != "0") {
			return "is-article repore-id-" . $this->checked->reporeid;
		}
		else {
			return null;
		}
	}

	/**
	 * @return string|null
	 */
	private function mvContentID()
	{
		if (isset($this->checked->mv_content_id)) {
			return "is-mv-content mv-content-id-" . $this->checked->mv_content_id;
		}
		else {
			return null;
		}
	}

	/**
	 * @return string
	 */
	private function isSearch()
	{
		$return = "";
		if (isset ($this->checked->search)) {
			$return = "is-search";
		}
		return $return;
	}
}

$body_class = new body_class();
