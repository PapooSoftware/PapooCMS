<?php

/**
 * ########################################
 * # 2-Klick-Video-Plugin                 #
 * # (c) 2018 Papoo Software & Media GmbH #
 * #          Dr. Carsten Euwens          #
 * # Authors: Christoph Zimmer            #
 * # http://www.papoo.de                  #
 * ########################################
 * # PHP Version >= 5.3                   #
 * ########################################
 * @copyright 2018 Papoo Software & Media GmbH
 * @author Christoph Grenz <cg@papoo.de>
 * @date 2018-07-26
 */

namespace Papoo\Plugins\TwoClickVideo;
/**
 * Class TwoClickVideo
 *
 * @package Papoo\Plugins\TwoClickVideo
 */
#[AllowDynamicProperties]
class TwoClickVideo
{
	private $cms;
	private $db;
	private $content;
	private $template;
	private $settings;
	private static $handlers = [];
	private static $handler_domains = [];
	public $cachePath;

	/**
	 * @return void
	 */
	public function __construct()
	{
		global $db, $cms, $content;
		$this->db = &$db;
		$this->cms = &$cms;
		$this->content = &$content;
		$this->cachePath = rtrim(PAPOO_ABS_PFAD, '/') . '/images/thumbs/2_klick_video/';
		$this->cacheUrl = rtrim(PAPOO_WEB_PFAD, '/') . '/images/thumbs/2_klick_video/';

		$this->loadSettings();

		$this->template = preg_match('~'.basename(realpath(__DIR__."/..")).'/templates/(?<template>.*)\.html~', $GLOBALS["checked"]->template, $match)
			? $match["template"]
			: null;

		if (self::isBackend()) {

			if (isset($_GET["route"]) && preg_match('~^xhr/(?<request>.+)~', $_GET["route"], $match)) {
				$this->processXHR($match["request"]);
			}

			$protocol = empty($_SERVER["HTTPS"]) == false && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443
				? "https://" : "http://";

			$GLOBALS["content"]->template["plugin"]["2_klick_video"]["selfUrl"] = $protocol.$_SERVER["HTTP_HOST"].rtrim(PAPOO_WEB_PFAD, "/").
				"/interna/plugin.php?menuid={$GLOBALS["checked"]->menuid}";

			if ($_SERVER["REQUEST_METHOD"] === "POST") {
				$this->handlePOST();
				$this->redirectSeeOther($this->template);
			}

			//self::makeResponseMessageForTemplate();

		}
	}

	/**
	 * Register a video service handler
	 *
	 * @param string $className
	 * @param string[] $domains Supported video source domains (e.g. ["youtube.com", "youtube-nocookie.com"])
	 * @return void
	 */
	public static function registerHandler($className, $domains)
	{
		self::$handlers[] = $className;
		foreach ((array)$domains as $domain) {
			self::$handler_domains[$domain] = $className;
		}
	}

	/**
	 * @param string $domain
	 * @return HandlerBase|null
	 */
	private function getHandler($domain)
	{
		if (isset(self::$handler_domains[$domain])) {
			return new self::$handler_domains[$domain]($this);
		}
		else {
			return null;
		}
	}

	/**
	 * @return bool
	 */
	private static function isBackend() {
		return defined("admin") && in_array((int)$GLOBALS["checked"]->menuid, array_map(function ($menuid) {
				return (int)$menuid;
			}, explode(" ", trim((string)$GLOBALS["db"]->get_var(
				"SELECT plugin.plugin_menuids ".
				"FROM {$GLOBALS["cms"]->tbname["papoo_plugins"]} AS plugin ".
				"JOIN {$GLOBALS["cms"]->tbname["papoo_pluginclasses"]} AS classes ON classes.pluginclass_plugin_id = plugin.plugin_id ".
				"WHERE classes.pluginclass_name LIKE 'TwoClickVideo' ".
				"LIMIT 1"
			)))));
	}

	private static function makeResponseMessageForTemplate() {
		global $content;

		if (isset($_SESSION["dsgvo"]["success"])) {
			$content->template["plugin"]["dsgvo"]["isSuccess"] = true;
			$content->template["plugin"]["dsgvo"]["responseMessage"] = $content->template["plugin"]["dsgvo"][$_SESSION["dsgvo"]["success"]];
		}
		else if (isset($_SESSION["dsgvo"]["error"])) {
			$content->template["plugin"]["dsgvo"]["isError"] = true;
			$content->template["plugin"]["dsgvo"]["responseMessage"] = $content->template["plugin"]["dsgvo"][$_SESSION["dsgvo"]["error"]];
		}

		unset($_SESSION["dsgvo"]["success"], $_SESSION["dsgvo"]["error"]);
	}

	/**
	 * @see self::$settings
	 * @return void
	 */
	private function loadSettings()
	{
		$sql = sprintf(
			'SELECT *, HEX(installation_key) AS `installation_key`, TIME_TO_SEC(cache_lifetime) AS `cache_lifetime` FROM `%1$s` WHERE id=1',
			$this->cms->tbname['plugin_2_klick_video']
		);
		$settings = $this->db->get_results($sql, ARRAY_A)[0];

		// Installations-Schlüssel setzen falls er fehlt
		if ($settings['installation_key'] === null) {
			$key = '';
			for ($i = 0; $i < 16; ++$i) {
				$key .= chr(rand(0,255));
			}
			$sql = sprintf('UPDATE `%1$s` SET installation_key = x\'%2$s\' WHERE id=1',
				$this->cms->tbname['plugin_2_klick_video'],
				$this->db->escape(bin2hex($key))
			);
			$this->db->query($sql);
		}

		// Sprachdaten laden
		$sql = sprintf(
			'SELECT * FROM `%1$s` RIGHT JOIN `%2$s` USING (lang_id) ORDER BY lang_id',
			$this->cms->tbname['plugin_2_klick_video_lang'],
			$this->cms->tbname['papoo_name_language']
		);
		$settings['lang'] = [];
		foreach($this->db->get_results($sql, ARRAY_A) as $lang_row) {
			$settings['lang'][$lang_row['lang_id']] = $lang_row;
			if ($lang_row['lang_id'] == $this->cms->lang_id) {
				$settings = array_merge($settings, $settings['lang'][$lang_row['lang_id']]);
			}
		}

		$this->content->template['plugin']['2_klick_video'] = [];
		$this->settings = $this->content->template['plugin']['2_klick_video']['settings'] = $settings;

	}

	/**
	 * @param string $template
	 * @param int $status
	 * @return void
	 */
	private static function redirect($template, $status = 301) {
		$statusMap = array(
			301 => "Moved Permanently",
			302 => "Found",
			303 => "See Other",
			307 => "Temporary Redirect",
		);

		if (isset($statusMap[$status]) == false) {
			throw new Exception("Unsupported HTTP status code `$status`.");
		}

		$protocol = empty($_SERVER["HTTPS"]) == false && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443 ? "https://" : "http://";
		$baseUrl = $protocol.$_SERVER["HTTP_HOST"].rtrim(PAPOO_WEB_PFAD, "/")."/";

		header("HTTP/1.1 $status {$statusMap[$status]}");
		header("Location: {$baseUrl}interna/plugin.php?menuid={$GLOBALS["checked"]->menuid}&template=2_klick_video/templates/$template.html");
		exit;
	}

	/**
	 * @param string $template
	 * @see self::redirect
	 * @return void
	 */
	private static function redirectSeeOther($template) {
		self::redirect($template, 303);
	}

	/**
	 * @param string $string
	 * @return string
	 */
	private static function slugify($string) {
		return preg_replace('~-{2,}~', "-", preg_replace('~[^a-z0-9-]~i', "-",
				strtolower(str_replace(array("ä", "ö", "ü", "Ä", "Ö", "Ü", "ß"), array("ae", "oe", "ue", "ae", "oe", "ue", "ss"), trim($string)))
		));
	}

	/*public function post_papoo() {
		if ($this->settings["logfile_autoremove"] == 1) {
			// Durchlaufen bei jedem Seitenaufruf verhindern, nur einmal am Tag
			if (
				isset($_SESSION["dsgvo"]["remove_logfiles_cronjob"]) == false ||
				$_SESSION["dsgvo"]["remove_logfiles_cronjob"] !== date("Ymd")
			) {
				$_SESSION["dsgvo"]["remove_logfiles_cronjob"] = date("Ymd");

				$this->splitLogfiles();
				$this->removeLogfiles();
			}
		}
		if ($this->settings["formdata_autoremove"] == 1 && isset($GLOBALS["cms"]->tbname["papoo_form_manager"])) {
			$this->removeFormData();
		}
		$this->refreshCookiesLifetime();
	}
	public function output_filter() {
		global $output;

		if ($this->settings["youtube_nocookie"] == 1) {
			$output = self::convertYoutubeLinksToNoCookieLinks($output);
		}

		self::recordExternalResources($output);
	}*/

	/**
	 * @see $output
	 * @return void
	 */
	public function output_filter() {
		global $output;

		if ($this->settings["active"] == 1) {
			$output = $this->convertVideoLinks($output);
		}
		return $output;
	}

	private function processXHR($request) {
		/** @var \Papoo\Plugins\DSGVO\XHRController $xhrController */
		$xhrController = require_once "XHRController.php";
		$xhrController->process($request);
	}

	/**
	 * @return void
	 */
	private function handlePOST() {
		if (isset($_POST["action"]) && method_exists($this, ($method = "{$_POST["action"]}Action"))) {
			call_user_func(array($this, $method));
		}
	}

	/**
	 * @param string $name
	 * @return bool|string Das eingelesene Template, wenn es im Style- oder Pluginverzeichnis gefunden wird, ansonsten false
	 * @author Christoph Zimmer
	 */
	private static function findTemplate($name) {
		$filename = "plugins/".pathinfo(realpath(__DIR__."/.."), PATHINFO_FILENAME)."/templates/$name";

		return array_reduce(array(
			PAPOO_ABS_PFAD."/styles/{$GLOBALS["cms"]->style_dir}/templates/$filename",
			PAPOO_ABS_PFAD."/$filename"
		), function ($template, $filename) {
			return $template !== false ? $template : (is_file($filename) ? $filename : $template);
		}, false);
	}

	/**
	 * @param $name
	 * @param array $data Key-/Value-Paare, die in dem Spracheintrag ersetzt werden. ["name" => "Christoph"] wandelt "Hallo :name!" in "Hallo Christoph!" um.
	 * @return mixed Der fertig ausgewertete Spracheintrag mit allen Ersetzungen aus $data
	 * @author Christoph Zimmer
	 */
	private static function loadMessage($name, $data) {
		$message = $GLOBALS["content"]->template["plugin"]["dsgvo"][$name];
		foreach ((array)$data as $key => $value) {
			$message = preg_replace_callback('~(?<=^| ):(?<key>[a-z_]+)\b~', function ($match) use ($data) {
				return isset($data[$match["key"]]) ? $data[$match["key"]] : $data[0];
			}, $message);
		}
		return $message;
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	private function escape($value)
	{
		if (is_null($value)) return 'NULL';
		if (is_bool($value)) return $value ? 'TRUE' : 'FALSE';
		if (is_int($value)) return (string)$value;
		if (is_float($value)) return (string)$value;
		return '\''.$this->db->escape((string)$value).'\'';
	}

	/**
	 * @see self::handlePOST
	 * @return void
	 */
	private function clearCacheAction()
	{
		$sql = sprintf('SELECT * FROM `%1$s` JOIN `%2$s` USING (`cache_id`) FOR UPDATE',
			$this->cms->tbname['plugin_2_klick_video_cache'],
			$this->cms->tbname['plugin_2_klick_video_cache_images']
		);
		$rows = $this->db->get_results($sql, ARRAY_A);
		foreach ($rows as $row) {
			$row['filename'] = str_replace('../', '', $row['filename']);
			@unlink($this->cachePath . $filename);
		}
		$this->db->query(sprintf('TRUNCATE %1$s', $this->cms->tbname['plugin_2_klick_video_cache_images']));
		$this->db->query(sprintf('TRUNCATE %1$s', $this->cms->tbname['plugin_2_klick_video_cache']));
	}

	/**
	 * @param string $provider
	 * @param string $video_id
	 * @return array|null
	 */
	public function getCacheItem($provider, $video_id)
	{
		$sql = sprintf('SELECT *, TIMESTAMPDIFF(SECOND, `cache_date`, NOW()) AS `cache_age` FROM `%1$s` WHERE `video_provider` = %2$s AND `video_id` = %3$s',
			$this->cms->tbname['plugin_2_klick_video_cache'],
			$this->escape($provider),
			$this->escape($video_id)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if ($result) {
			return $result[0];
		} else {
			return null;
		}
	}

	/**
	 * @param string $provider
	 * @param string $video_id
	 * @return array[]
	 */
	public function getCacheImages($provider, $video_id)
	{
		$sql = sprintf('SELECT * FROM `%1$s` JOIN `%2$s` USING (`cache_id`) WHERE `video_provider` = %3$s AND `video_id` = %4$s',
			$this->cms->tbname['plugin_2_klick_video_cache'],
			$this->cms->tbname['plugin_2_klick_video_cache_images'],
			$this->escape($provider),
			$this->escape($video_id)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if ($result) {
			return $result;
		} else {
			return [];
		}
	}

	/**
	 * @param string $provider
	 * @param string $video_id
	 * @param string $title
	 * @param array[] $images
	 * @return void
	 */
	public function setCacheContent($provider, $video_id, $title, $images)
	{
		$sql = sprintf('INSERT INTO `%1$s` (video_provider, video_id, video_title, cache_date)
				VALUES (%2$s, %3$s, %4$s, NOW())
				ON DUPLICATE KEY UPDATE
					cache_id=LAST_INSERT_ID(cache_id), video_title=VALUES(video_title), cache_date=VALUES(cache_date)',
			$this->cms->tbname['plugin_2_klick_video_cache'],
			$this->escape($provider),
			$this->escape($video_id),
			$this->escape(mb_substr($title, 0, 128))
		);
		$this->db->query($sql);
		$insert_id = $this->db->insert_id;

		$inserts = [];
		$used_widths = [];
		$used_filenames = [];
		foreach ($images AS $image) {
			$inserts[] = sprintf('(%s, %s, %s, %s, %s)',
				$this->escape($insert_id),
				$this->escape($image['image_width']),
				$this->escape($image['image_height']),
				$this->escape($image['file_name']),
				$this->escape($image['etag'])
			);
			$used_widths[] = $this->escape($image['image_width']);
			$used_filenames[$image['file_name']] = $image['file_name'];
		}
		$sql = sprintf('INSERT INTO `%1$s` (`cache_id`, `image_width`, `image_height`, `file_name`, `etag`)
			VALUES %2$s ON DUPLICATE KEY UPDATE
				image_height = VALUES(image_height), file_name = VALUES(file_name), etag = VALUES(etag)',
			$this->cms->tbname['plugin_2_klick_video_cache_images'],
			implode(', ', $inserts)
		);
		$this->db->query($sql);

		$sql = sprintf('SELECT `file_name` FROM `%1$s` WHERE `cache_id`=%2$s AND `image_width` NOT IN (%3$s) FOR UPDATE',
			$this->cms->tbname['plugin_2_klick_video_cache_images'],
			$this->escape($insert_id),
			implode(', ', $used_widths)
		);
		$r = $this->db->get_results($sql, ARRAY_A);
		if ($r) {
			foreach ($r as $row) {
				if (!isset($used_filenames[$row['file_name']])) {
					@unlink($this->cachePath . $row['file_name']);
				}
			}
		}
		$sql = sprintf('DELETE FROM `%1$s` WHERE `cache_id`=%2$s AND `image_width` NOT IN (%3$s)',
			$this->cms->tbname['plugin_2_klick_video_cache_images'],
			$this->escape($insert_id),
			implode(', ', $used_widths)
		);
		$this->db->query($sql);
	}

	/**
	 * @return void
	 */
	private function saveAction()
	{
		// Allgemeines speichern
		$settings = [
			'active' => !empty($_POST['active']) ? (bool)$_POST['active'] : false,
			'two_click' => !empty($_POST['two_click']) ? (bool)$_POST['two_click'] : false,
			'use_thumbnails' => !empty($_POST['use_thumbnails']) ? (bool)$_POST['use_thumbnails'] : false,
			'cache_lifetime' => (int)$_POST['cache_lifetime'],
			'use_flex_video' => !empty($_POST['use_flex_video']) ? (bool)$_POST['use_flex_video'] : false,
			'thumbnail_sizes' => !empty($_POST['thumbnail_sizes']) ? $_POST['thumbnail_sizes'] : '',
		];
		$sql = sprintf('UPDATE `%1$s` SET %2$s WHERE id=1',
			$this->cms->tbname['plugin_2_klick_video'],
			implode(', ', array_map(function ($k, $v) {
				if ($k == 'cache_lifetime') {
					return "`$k` = SEC_TO_TIME(".$this->escape($v).')';
				} else {
					return "`$k` = ".$this->escape($v);
				}
			}, array_keys($settings), $settings))
		);
		$this->db->query($sql);

		// Sprachdaten speichern
		foreach ($this->settings['lang'] as $lang_id => $lang_data) {
			$settings = [];
			foreach (['title_text', 'dismiss_text', 'info_text', 'link_text',
				'text_color', 'background_color', 'confirm_button_color',
				'dismiss_button_color', 'confirm_text_color',
				'dismiss_text_color'] as $key)
			{
				if (isset($_POST[$key][$lang_id])) {
					$settings[$key] = $_POST[$key][$lang_id];
				}
			}

			if ($settings) {
				$settings['lang_id'] = (int)$lang_id;
				$sql = sprintf('INSERT INTO `%1$s` (%2$s) VALUES (%3$s) ON DUPLICATE KEY UPDATE %4$s',
					$this->cms->tbname['plugin_2_klick_video_lang'],
					implode(', ', array_map(function ($k) {
						return "`$k`";
					}, array_keys($settings))),
					implode(', ', array_map(function ($v) {
						return $this->escape($v);
					}, $settings)),
					implode(', ', array_map(function ($k) {
						return "`$k`=VALUES(`$k`)";
					}, array_keys($settings)))
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * @param string $input
	 * @return string $string
	 */
	public function convertVideoLinks($input)
	{
		$domains = implode('|', array_map(function($k) { return preg_quote($k, '~'); }, array_keys(self::$handler_domains)));
		$regex = '~<(?<type>a|iframe|object)\s(?<options1>[^>]*)\b(?:href|data|src)\s*=\s*"(?<url>(?<schema>https?)://(?:www\.)?(?<domain>'.$domains.')(?<path>/[^"]+))"(?<options2>[^>]*)>(?<content>.*?)</\1>~s';

		$converted = 0;

		$matches = null;
		if (preg_match_all($regex, $input, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE)) {
			$matches = array_reverse($matches);
			foreach ($matches as $match) {
				$offset = $match[0][1];
				$length = strlen($match[0][0]);

				// Daten vorbereiten
				$data = array_map(function ($x) {
					return $x[0];
				}, $match);
				$data['options'] = $data['options1'].$data['options2'];

				// Neues Video-Objekt erstellen
				$result = $this->convertVideoLink($data);

				// Link/Embed ersetzen
				if (is_string($result)) {
					$suffix = '';
					// Wenn von <p> eingeschlossen, Absatz mit entfernen.
					if (substr($input, $offset-3, 3) === '<p>') {
						$offset -= 3;
						$length += 3;
						if (substr($input, $offset+$length, 4) === '</p>') {
							$length += 4;
						} else {
							$suffix = '<p>';
						}
					}
					$input = substr_replace($input, $result.$suffix, $offset, $length);
					$converted++;
				}
			}
		}

		if ($converted) {
			$einbindung = "\n\t".'<script defer="defer" type="text/javascript" src="'.PAPOO_WEB_PFAD.'/plugins/2_klick_video/js/2_klick_video.js"></script>'."\n\t";
			$einbindung .= sprintf('<style type="text/css">'."\n\t\t"
				.'.two-click-video .video--info-box { color: %s; background-color: %s; } '."\n\t\t"
				.'.two-click-video .video--info-box .activate-button { color: %s; background-color: %s; } '."\n\t\t"
				.'.two-click-video .video--info-box .activate-button:hover, .two-click-video .video--info-box .activate-button:focus { background-color: %s; } '."\n\t\t"
				.'.two-click-video .video--info-box .dismiss-button { color: %s; background-color: %s; } '."\n\t\t"
				.'.two-click-video .video--info-box .dismiss-button:hover, .two-click-video .video--info-box .dismiss-button:focus { background-color: %s; } '."\n\t"
				.'</style>'."\n",
				htmlspecialchars($this->settings['text_color']),
				htmlspecialchars($this->makeAlpha($this->settings['background_color'], 0.98)),
				htmlspecialchars($this->settings['confirm_text_color']),
				htmlspecialchars($this->settings['confirm_button_color']),
				htmlspecialchars($this->makeRGBHoverTint($this->settings['confirm_button_color'])),
				htmlspecialchars($this->settings['dismiss_text_color']),
				htmlspecialchars($this->settings['dismiss_button_color']),
				htmlspecialchars($this->makeRGBHoverTint($this->settings['dismiss_button_color']))
			);
			$head_end = strpos($input, '</head>');
			$input = substr_replace($input, $einbindung, $head_end, 0);
		}
		return $input;
	}

	private function makeAlpha($hex_color, $alpha=1.0)
	{
		if (strlen($hex_color) == 4) {
			$item_len = 1;
			$factor = 0x11;
		} else {
			$item_len = 2;
			$factor = 1;
		}
		$red = hexdec(substr($hex_color, 1, $item_len))*$factor;
		$green = hexdec(substr($hex_color, 1+$item_len, $item_len))*$factor;
		$blue = hexdec(substr($hex_color, 1+2*$item_len, $item_len))*$factor;

		return "rgba($red, $green, $blue, $alpha)";
	}
	
	private function makeRGBHoverTint($hex_color, $tint_factor=0.16)
	{
		if (strlen($hex_color) == 4) {
			$item_len = 1;
			$factor = 0x11;
		} else {
			$item_len = 2;
			$factor = 1;
		}
		$red = hexdec(substr($hex_color, 1, $item_len))*$factor;
		$green = hexdec(substr($hex_color, 1+$item_len, $item_len))*$factor;
		$blue = hexdec(substr($hex_color, 1+2*$item_len, $item_len))*$factor;

		$do_shade = ($red+$green+$blue > 650);
		if ($do_shade) {
			$red = (int)($red * (1-($tint_factor*0.6)));
			$green = (int)($green * (1-($tint_factor*0.6)));
			$blue = (int)($blue * (1-($tint_factor*0.6)));
		} else {
			$red = (int)min($red + (255 - $red)*0.4*$tint_factor, 255);
			$green = (int)min($green + (255 - $green)*0.57*$tint_factor, 255);
			$blue = (int)min($blue + (255 - $blue)*0.2*$tint_factor, 255);
		}
		
		return "rgb($red, $green, $blue)";
	}

	/**
	 * @param array $data
	 * @return string $string
	 */
	public function convertVideoLink($data)
	{
		$handler = $this->getHandler($data['domain']);
		$provider = $handler->getProviderId();
		$video_id = $handler->getVideoId($data);
		if (!$video_id) {
			return null;
		}
		$data['video_id'] = $video_id;
		$data['embed_url'] = $handler->getEmbedUrl($data);

		// Cache-Inhalt holen und ggf. auffrischen
		$cached = $this->getCacheItem($provider, $video_id);
		if (!$cached or $cached['cache_age'] > $this->settings['cache_lifetime']) {
			@mkdir($this->cachePath);
			$handler->fillCache($data, (bool)(int)$this->settings['use_thumbnails']);
			$cached = null;
		}
		if (!$cached) {
			$cached = $this->getCacheItem($provider, $video_id);
		}
		$images = $this->getCacheImages($provider, $video_id);

		// Vorschaubilder bestimmen
		$srcset = [];
		$defaultsrc = null;
		$defaultsrc_diff = 4294967295;
		foreach ($images as $image) {
			if (abs($image['image_width'] - 480) < $defaultsrc_diff) {
				$defaultsrc = $this->cacheUrl.urlencode($image['file_name']);
				$defaultsrc_diff = abs($image['image_width'] - 480);
			}
			$srcset[] = $this->cacheUrl.urlencode($image['file_name']).' '.$image['image_width'].'w';
		}

		// Klassen von altem Objekt kopieren
		$classes = '';
		if (preg_match('/\bclass\s*=\s*"([^"]*)"/i', $data['options'], $match)) {
			$classes = trim($match[1]).' ';
			$data['options'] = preg_replace('/\bclass\s*=\s*"([^"]*)"\s*/i', '', $data['options']);
		}
		$classes .= 'two-click-video video--'.$provider;
		if (!$this->settings['two_click']) {
			$classes .= ' single-confirm';
		}

		$result = [];
		if ($this->settings['use_flex_video']) {
			$result[] = '<div class="flex-video">';
		}
		$result[] = sprintf('<div class="%s" data-url="%s" %s>',
			$classes,
			htmlspecialchars($data['embed_url']),
			$data['options']
		);
		$result[] = sprintf('<div class="ytp-gradient-top" data-layer="1"></div><span class="video--title">%s</span>',
			htmlspecialchars($cached['video_title'])
		);
		if ($defaultsrc !== null) {
			$result[] = sprintf('<img class="video--preview" alt="" src="%s" srcset="%s" sizes="%s" />',
				htmlspecialchars($defaultsrc),
				htmlspecialchars(implode(', ', $srcset)),
				htmlspecialchars($this->settings['thumbnail_sizes'])
			);
		}
		$result[] = '<button type="button" class="video--play"><span class="video-play-icon" title="Play"></span></button>';
		$result[] = sprintf('
		<div class="video--info-box-container">
		<fieldset class="video--info-box">
			<legend>%s</legend>
				<p>%s</p><p>
					<button type="button" class="activate-button">%s</button>
					<button type="button" class="dismiss-button">%s</button>
		</p>
		</fieldset>
		</div>',
			htmlspecialchars($this->settings['title_text']),
			($this->settings['info_text']),
			htmlspecialchars($this->settings['link_text']),
			htmlspecialchars($this->settings['dismiss_text'])
		);
		$result[] = '</div>';
		if ($this->settings['use_flex_video']) {
			$result[] = '</div>';
		}
		return implode('', $result);
	}
}

/**
 * Class HandlerBase
 *
 * @package Papoo\Plugins\TwoClickVideo
 */
#[AllowDynamicProperties]
abstract class HandlerBase
{
	/** @var string */
	const PROVIDER_ID = '???';
	/** @var string */
	const PRETTY_NAME = __CLASS__;
	/** @var TwoClickVideo */
	protected $plugin;

	/**
	 * @param TwoClickVideo $mainPlugin
	 * @return void
	 */
	public function __construct(TwoClickVideo $mainPlugin)
	{
		$this->plugin = $mainPlugin;
	}

	/**
	 * @return string
	 */
	public function getProviderId()
	{
		return static::PROVIDER_ID;
	}

	/**
	 * @param array $data;
	 * @return string
	 */
	public abstract function getVideoId($data);

	/**
	 * @param array $data;
	 * @return string
	 */
	public abstract function getEmbedUrl($data);

	/**
	 * @param array $data;
	 * @param bool $get_thumbnails
	 * @return void
	 */
	public abstract function fillCache($data, $get_thumbnails);
}

require_once(__DIR__.'/handlers/Youtube.php');

$TwoClickVideo = new TwoClickVideo();
