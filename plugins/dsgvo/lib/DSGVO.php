<?php

/**
 * ########################################
 * # Datenschutz-Grundverordnung (DSGVO)  #
 * # (c) 2018 Papoo Software & Media GmbH #
 * #          Dr. Carsten Euwens          #
 * # Authors: Christoph Zimmer            #
 * # http://www.papoo.de                  #
 * ########################################
 * # PHP Version >= 5.3                   #
 * ########################################
 * @copyright 2018 Papoo Software & Media GmbH
 * @author Christoph Zimmer <cz@papoo.de>
 * @date 2018-02-28
 */

#[AllowDynamicProperties]
class DSGVO
{
	private $template;
	private $settings;

	public function __construct() {
		if (isset($_SESSION["dsgvo"]) == false || is_array($_SESSION["dsgvo"]) == false) {
			$_SESSION["dsgvo"] = array();
		}

		$this->loadSettings();

		$this->template =
			preg_match('~'.basename(realpath(__DIR__."/..")).'/templates/(?<template>.*)\.html~', $GLOBALS["checked"]->template ?? '', $match)
			? $match["template"]
			: null;

		if (self::isBackend()) {
			if (isset($_GET["route"]) && preg_match('~^xhr/(?<request>.+)~', $_GET["route"], $match)) {
				$this->processXHR($match["request"]);
			}

			$protocol = empty($_SERVER["HTTPS"]) == false && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443
				? "https://" : "http://";

			$GLOBALS["content"]->template["plugin"]["dsgvo"]["selfUrl"] = $protocol.$_SERVER["HTTP_HOST"].rtrim(PAPOO_WEB_PFAD, "/").
				"/interna/plugin.php?menuid={$GLOBALS["checked"]->menuid}";


			if ($_SERVER["REQUEST_METHOD"] === "POST") {
				$this->handlePOST();
				$this->redirectSeeOther($this->template);
			}

			self::makeResponseMessageForTemplate();

			$this->prepareBackendDataForView();
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
				"WHERE classes.pluginclass_name LIKE '".__CLASS__."' ".
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
	 * @param $input
	 * @return bool|array|void
	 */
	private static function recordExternalResources($input) {
		$domain = preg_quote($_SERVER["HTTP_HOST"], "~");
		if (preg_match_all(
				'~<(?:script|iframe|link)\s[^>]*(?<=\s)(?:href|src)=("|\')(?<url>(?:https?:)?//(?!'.$domain.')(?:(?!\1).)+)[^>]+>~',
				$input, $matches, PREG_SET_ORDER
			) === false || count($matches) == 0
		) {
			return false;
		}

		$urls = array_map(function ($match) {
			return $match["url"];
		}, array_filter($matches, function ($match) {
			return preg_match('~\srel=("|\')(?:alternate|canonical|pingback)\1~', $match[0]) != 1;
		}));

		if (count($urls) == 0) {
			return false;
		}

		// last_encounter explizit befuellen, da MySQL 5.5 DATETIME DEFAULT NOW() nicht unterstuetzt
		$GLOBALS["db"]->query("INSERT INTO {$GLOBALS["cms"]->tbname["plugin_dsgvo_external_resources"]} ".
			"(url, url_hash, last_encounter) VALUES ".
			"(".implode("), (", array_map(function ($url) {
				return "'{$GLOBALS["db"]->escape($url)}', '{$GLOBALS["db"]->escape(hash("md5", $url))}', '".date("Y-m-d H:i:s")."'";
			}, $urls)).") ".
			"ON DUPLICATE KEY UPDATE last_encounter = NOW()"
		);
	}

	private function prepareBackendDataForView() {
		global $content;

		//===================================================================
		// Liste aller Cookies

		$cookieList = array_map(function ($name, $value) {
			return array(
				"name" => $name,
				"value" => $value,
			);
		}, array_keys($_COOKIE), $_COOKIE);

		usort($cookieList, function ($a, $b) {
			if (strtolower($a["name"]) === strtolower($b["name"])) { return 0; }
			$test = array($a["name"], $b["name"]);
			natcasesort($test);
			return reset($test) === $a["name"] ? -1 : 1;
		});

		$content->template["plugin"]["dsgvo"]["cookieList"] = $cookieList;

		//===================================================================
		// Externe Skripte & Schriftarten

		$content->template["plugin"]["dsgvo"]["externalResources"] = $GLOBALS["db"]->get_results(
			"SELECT * ".
			"FROM {$GLOBALS["cms"]->tbname["plugin_dsgvo_external_resources"]} ".
			"ORDER BY url", ARRAY_A
		);

		//===================================================================
		// Formular-Manager

		$content->template["plugin"]["dsgvo"]["formManagerInstalled"] = isset($GLOBALS["cms"]->tbname["papoo_form_manager"]);

		//===================================================================
		// HTTPS aktiviert?

		$content->template["plugin"]["dsgvo"]["usingHTTPS"] = empty($_SERVER["HTTPS"]) == false && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443;

	}

	private function loadSettings() {
		$this->settings = $GLOBALS["content"]->template["plugin"]["dsgvo"]["storedSettings"] = $GLOBALS["db"]->get_row(
			"SELECT * FROM {$GLOBALS["cms"]->tbname["plugin_dsgvo_settings"]} WHERE id = 1", ARRAY_A
		);
	}

	/**
	 * @param $template
	 * @param int $status
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
		header("Location: {$baseUrl}interna/plugin.php?menuid={$GLOBALS["checked"]->menuid}&template=dsgvo/templates/$template.html");
		exit;
	}

	/**
	 * @param $template
	 */
	private static function redirectSeeOther($template) {
		self::redirect($template, 303);
	}

	private static function slugify($string) {
		return preg_replace('~-{2,}~', "-", preg_replace('~[^a-z0-9-]~i', "-",
				strtolower(str_replace(array("ä", "ö", "ü", "Ä", "Ö", "Ü", "ß"), array("ae", "oe", "ue", "ae", "oe", "ue", "ss"), trim($string)))
		));
	}

	public function post_papoo() {
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
	}

	/**
	 * @param $request
	 */
	private function processXHR($request) {
		/** @var \Papoo\Plugins\DSGVO\XHRController $xhrController */
		$xhrController = require_once "XHRController.php";
		$xhrController->process($request);
	}

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
	 * @param $columns
	 * @return bool|void
	 */
	private static function updateSettings($columns) {
		if (is_array($columns) == false) {
			return false;
		}

		$GLOBALS["db"]->query("UPDATE {$GLOBALS["cms"]->tbname["plugin_dsgvo_settings"]} SET ".implode(", ", $columns));
	}

	private function splitLogfiles() {
		$logfiles = array_filter(array_map(function ($basename) {
			return realpath(rtrim(PAPOO_ABS_PFAD, "/")."/dokumente/logs")."/$basename";
		}, array(
			"http_log.log",
			"email_log.log",
			"login_log.log",
			"hijack_log.log",
			"debug_log.log",
			"404.log",
		)), function ($filename) { return is_file($filename); });

		foreach ($logfiles as $logfile) {
			$date = file_get_contents($logfile, false, null, 0, 10);

			if ($date === date("d.m.Y") || strlen($date) < 10) {
				continue;
			}

			$suffix = preg_match('~(?<day>\d{2})\.(?<month>\d{2})\.(?<year>\d{4})~', $date, $match)
				? $match["year"].$match["month"].$match["day"]
				: time();

			$suffixedFilename = $logfile."~$suffix";

			$zip = class_exists("ZipArchive") ? new ZipArchive() : null;
			if (is_object($zip) && $zip->open("$suffixedFilename.zip", ZipArchive::CREATE) === true) {
				// Logdatei archivieren
				$zip->addFile($logfile, "/".basename($suffixedFilename));
				$zip->close();
				// Logdatei leeren
				file_put_contents($logfile, "");
				@chmod("$suffixedFilename.zip", 0666);
			}
			else {
				// Logdatei umbenennen und neu generieren
				if (rename($logfile, $suffixedFilename)) {
					file_put_contents($logfile, "");
					@chmod($logfile, 0666);
				}
			}
		}
	}

	private function removeLogfiles() {
		$today = strtotime(date("Y-m-d 00:00:00"));
		$path = realpath(rtrim(PAPOO_ABS_PFAD, "/")."/dokumente/logs");

		foreach (new DirectoryIterator($path) as $file) {
			// Im Dateinamen pruefen, ob es eine Logdatei ist und ob diese geloescht werden soll
			if ($file->isFile() && preg_match('/\.log[~_](?<time>\d{8,10})/', $file->getBasename(), $match)) {
				$time = $match["time"];
				if (strlen($time) == 8) {
					$time = strtotime(substr($time, 0, 4)."-".substr($time, 4, 2)."-".substr($time, 6, 2)." 00:00:00");
				}
				else {
					$time = (int)$time;
				}

				if (($today - $time) / 86400 >= (int)$this->settings["logfile_autoremove_interval"]) {
					@unlink("$path/{$file->getBasename()}");
				}
			}
		}
	}

	private function removeFormData() {
		// Durchlaufen bei jedem Seitenaufruf verhindern, nur einmal am Tag
		if (isset($_SESSION["dsgvo"]["remove_formdata_cronjob"]) && $_SESSION["dsgvo"]["remove_formdata_cronjob"] === date("Ymd")) {
			return;
		}
		else {
			$_SESSION["dsgvo"]["remove_formdata_cronjob"] = date("Ymd");
		}

		$expire = date("Y-m-d", time() - 86400 * (int)$this->settings["formdata_autoremove_interval"]);

		$GLOBALS["db"]->query(
			"DELETE _lead, _content ".
			"FROM {$GLOBALS["cms"]->tbname["papoo_form_manager_leads"]} _lead ".
			"LEFT JOIN {$GLOBALS["cms"]->tbname["papoo_form_manager_lead_content"]} _content ON _content.form_manager_content_lead_id_id = _lead.form_manager_lead_id ".
			"WHERE DATE(FROM_UNIXTIME(_lead.form_manager_form_datum)) <= '$expire'"
		);
	}

	private function refreshCookiesLifetime() {
		// Aufgrund von Dopplungen und Obsoleszens durch CCM19 deaktiviert
		// @see https://www.ccm19.de/
		return;

		if (is_array($_COOKIE) == false) {
			return;
		}

		$expire = time() + (int)$this->settings["cookie_lifetime"] * 60 * 60 * 24;
		$path = rtrim(PAPOO_WEB_PFAD, "/")."/";
		// Cookie-Domain setzen, Cookie mit und ohne www. gültig machen. Dabei ebenfalls lokale Installationen beachten. (aus session_class uebernommen)
		$domain = str_replace('.www.', '.', (strpos($_SERVER['SERVER_NAME'], '.') === false ? '' : '.'.$_SERVER['SERVER_NAME']));

		// RegEx-Patterns zum Filtern der Cookies nach deren Namen
		$blacklist = array(
			"~^_{1,2}g~",
		);

		foreach ($_COOKIE as $key => $value) {
			$cookieIsBlacklisted = array_reduce($blacklist, function ($blacklisted, $pattern) use ($key) {
				return $blacklisted || preg_match($pattern, $key);
			}, false);

			if ($cookieIsBlacklisted) {
				continue;
			}

			@setcookie($key, $value, $expire, $path, $domain, false, true);
		}
	}

	private function logfileAutoremoveAction() {
		$logfileAutoremoveEnabled = isset($_POST["logfile_autoremove"]) && $_POST["logfile_autoremove"] === "1" ? 1 : 0;
		$logfileAutoremoveInterval = isset($_POST["logfile_autoremove_interval"]) ? max((int)$_POST["logfile_autoremove_interval"], 1) : 1;

		$columns = array(
			"logfile_autoremove = $logfileAutoremoveEnabled",
		);

		if ($logfileAutoremoveEnabled) {
			$columns[] = "logfile_autoremove_interval = $logfileAutoremoveInterval";
		}

		self::updateSettings($columns);

		unset($_SESSION["dsgvo"]["remove_logfiles_cronjob"]);
	}

	private function cookieLifetimeAction() {
		$cookieLifetime = isset($_POST["cookie_lifetime"]) ? max((int)$_POST["cookie_lifetime"], 1) : 1;

		self::updateSettings(array("cookie_lifetime = $cookieLifetime"));
	}

	private function youtubeNoCookieAction() {
		$youtubeNoCookieEnabled = isset($_POST["youtube_nocookie"]) && $_POST["youtube_nocookie"] === "1" ? 1 : 0;

		self::updateSettings(array("youtube_nocookie = $youtubeNoCookieEnabled"));
	}

	/**
	 * @param $input
	 * @return string|string[]|null
	 */
	public static function convertYoutubeLinksToNoCookieLinks($input) {
		return preg_replace_callback('~<(a|iframe)\s[^>]*(?<=\s)(?:href|src)="[^"]*youtube.com[^"]*(?:/v/|v=|embed/)(?<video_id>[\w-]{10,12})[^"]*?(?<query_string>(?:\?[^"]+)?).*?</\1>~s', function ($match) {
			// Erzwinge Parameter rel=0 im QueryString
			parse_str(html_entity_decode(substr($match["query_string"], 1)), $params);
			$params["rel"] = 0;

			$queryString = http_build_query($params, '', "&amp;");

			return '<iframe width="420" height="315" src="https://www.youtube-nocookie.com/embed/'.$match["video_id"].'?'.$queryString.'" frameborder="0" allowfullscreen></iframe>';
		}, $input);
	}

	private function formdataAutoremoveAction() {
		$formdataAutoremoveEnabled = isset($_POST["formdata_autoremove"]) && $_POST["formdata_autoremove"] === "1" ? 1 : 0;
		$formdataAutoremoveInterval = isset($_POST["formdata_autoremove_interval"]) ? max((int)$_POST["formdata_autoremove_interval"], 1) : 1;

		$columns = array(
			"formdata_autoremove = $formdataAutoremoveEnabled",
		);

		if ($formdataAutoremoveEnabled) {
			$columns[] = "formdata_autoremove_interval = $formdataAutoremoveInterval";
		}

		self::updateSettings($columns);

		unset($_SESSION["dsgvo"]["remove_formdata_cronjob"]);
	}
}

$DSGVO = new DSGVO();