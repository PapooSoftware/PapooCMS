<?php

/**
 * Class aktivierung_plugin_class
 */
class aktivierung_plugin_class
{
	/**
	 * aktivierung_plugin_class constructor.
	 */
	function __construct()
	{
		global $content, $db, $checked, $diverse, $user, $cms, $menu;
		$this->content = & $content;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->diverse = &$diverse;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->menu = &$menu;

		$this->check_domain();

		IfNotSetNull($this->checked->is_saved_aktivierung);

		if (defined("admin")) {
			$this->user->check_intern();
			global $template;

			//$this->create_admin();
			//aktivieren_link

			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ($template != "login.utf8.html" && $template2=="aktivierung_back.html") {
				$this->check_domain();

				//$this->get_fb_data();
				if ($template2 == "aktivierung_back.html") {
					$this->save_settings();
				}

				if ($this->checked->is_saved_aktivierung == "ok") {
					$this->content->template['saved_aktivierung'] = "true";
				}
			}
			$this->get_aktivierungs_link();
		}

		IfNotSetNull($this->content->template['is_eingetragen']);
		IfNotSetNull($this->content->template['aktiv_error_1']);
		IfNotSetNull($this->content->template['aktiv_error_2']);
		IfNotSetNull($this->content->template['aktiv_error_3']);
		IfNotSetNull($this->content->template['aktiv_error_4']);
		IfNotSetNull($this->content->template['saved_aktivierung']);
		IfNotSetNull($this->content->template['plugin_error']);
	}

	function get_aktivierungs_link()
	{
		if (is_array($this->content->template['menuallback'])) {
			foreach ($this->content->template['menuallback'] as $key=>$value) {
				IfNotSetNull($value['template']);

				if (stristr($value['template'],"aktivierung_back")) {
					$this->content->template['aktivieren_link'] = "plugin.php?menuid=" . $value['menuid'] . $value['template'];
				}
			}
		}
	}

	/**
	 * aktivierung_plugin_class::http_request()
	 *
	 * @param mixed $get
	 * @return bool|string
	 */
	function http_request($get)
	{
		if (empty($_SESSION['curl_ok_aktivierung_link_not_ok'])) {
			if (function_exists("curl_init")) {
				$url = "http://verify.papoo.de/" . ($get);
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_TIMEOUT, 5);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_USERAGENT, "Check Agent");

				$curl_ret = curl_exec($ch);
				curl_close($ch);
			}
			if (empty($curl_ret)) {
				$_SESSION['curl_ok_aktivierung_link_not_ok'] = "NO";
			}
		}
		return $curl_ret;
	}

	/**
	 * aktivierung_plugin_class::check_domain()
	 *
	 * @return void
	 */
	function check_domain()
	{
		// Zuerst checken ob DM schonmal aktviert wurde
		if (!$this->check_old_aktivierung()) {
			//$server_array = explode("/", $_SERVER['HTTP_REFERER']);
			//$real_domain = $server_array['0'] . "/" . $server_array['1'] . "/" . $server_array['2'];

			$real_domain = $_SERVER['HTTP_HOST'];
			if (!strstr($real_domain,"http://.")) {
				$real_domain = "http://" . $real_domain;
			}
			if (!strstr($real_domain,"www.") && $real_domain != "http://localhost") {
				$real_domain = trim(str_replace("http://", "http://www.", $real_domain));
			}

			$url = $this->cms->title;
			$get = 'index.php?url=' . urlencode($url) . '&realurl=' . urlencode($real_domain) .
				'&version=' . urlencode(PAPOO_VERSION_STRING) . '&check=1';

			if ($real_domain == "http://localhost" || $real_domain == "http://127.0.0.1") {
				$this->content->template['not_verified'] = "";
				$this->content->template['localhost_installation'] = true;
			}
			else {
				$rdata=$this->http_request($get);
				if ($rdata == "domain_not_ok") {
					$this->content->template['not_verified'] = "ok";
				}
				else {
					$content = $real_domain . ";" . (time()+691200);
					$this->diverse->write_to_file("/interna/templates_c/aktivierung.txt", $content);
					$this->content->template['is_version_aktiviert'] = true;
				}
			}
		}
	}

	/**
	 * aktivierung_plugin_class::check_old_aktivierung()
	 *
	 * @return bool
	 */
	function check_old_aktivierung()
	{
		$aktivierungs_file = PAPOO_ABS_PFAD . "interna/templates_c/aktivierung.txt";
		if (file_exists($aktivierungs_file)) {
			$data = @file_get_contents($aktivierungs_file);
			$dat_array = explode(";", $data);
			//$server_array = explode("/", $_SERVER['HTTP_REFERER']);
			$real_domain = $_SERVER['HTTP_HOST'];
			if (!strstr($real_domain, "http://.")) {
				$real_domain = "http://" . $real_domain;
			}
			if (!strstr($real_domain,"www.") && $real_domain != "http://localhost") {
				$real_domain = trim(str_replace("http://", "http://www.", $real_domain));
			}

			if ($dat_array['0'] == $real_domain && $dat_array['1'] > time()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * aktivierung_plugin_class::save_settings()
	 *
	 * @return void
	 */
	function save_settings()
	{
		if (!function_exists('curl_init')) {
			$this->content->template['aktiv_error_4'] =
				"Online Aktivierung nicht m&ouml;glich - bitte schreiben Sie uns an info@papoo.de und teilen Sie uns Ihre Rechnungsnummer mit!";
		}

		if (!empty($this->checked->aktiv_plugin_ihre_rechnungsnummer)) {
			//$server_array = e xplode("/", $_SERVER['HTTP_REFERER']);
			$real_domain = $_SERVER['HTTP_HOST'];
			if (!strstr($real_domain, "http://.")) {
				$real_domain = "http://" . $real_domain;
			}
			if (!strstr($real_domain,"www.") && $real_domain!="http://localhost") {
				$real_domain = trim(str_replace("http://", "http://www.", $real_domain));
			}
			$url = $this->cms->title;
			$get = 'index.php?renr=' . $this->checked->aktiv_plugin_ihre_rechnungsnummer . '&url=' . urlencode($url) .
				'&realurl=' . urlencode($real_domain) . '&version=' . urlencode(PAPOO_VERSION_STRING) . '&reseller_id=1';

			$rdata = $this->http_request($get);
			//$content = $real_domain . ";" . (time()+1);
			// Daten wurden korrekt gespeichert
			if ($rdata == "insert_ok") {
				//$this->diverse->write_to_file("/interna/templates_c/aktivierung.txt", $content);
				$this->reload("ok");
			}

			if ($rdata == "error_1") {
				// Domain schon vorhanden, wurde schon aktiviert
				$this->content->template['aktiv_error_1'] = "true";
			}

			if ($rdata == "error_2") {
				// Domain schon vorhanden, aber mit anderer ReNr
				$this->content->template['aktiv_error_2'] = "true";
			}

			if ($rdata == "error_3") {
				// Diese Rechnungsnr gibt es nicht.
				$this->content->template['aktiv_error_3'] = "true";
			}
		}
	}

	/**
	 * aktivierung_plugin_class::reload()
	 *
	 * @param string $dat
	 * @return void
	 */
	function reload($dat="")
	{
		$location_url = "plugin.php?menuid=" . $this->checked->menuid .
			"&template=aktivierung/templates/aktivierung_back.html&is_saved_aktivierung="  .$dat;
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}
}

$aktivierung_plugin = new aktivierung_plugin_class();