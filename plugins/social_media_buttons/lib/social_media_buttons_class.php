<?php

define("SHARIFF_CONFIG_FILE", PAPOO_ABS_PFAD . "/templates_c/shariff.json");

/**
 * Class social_media_buttons_class
 */
#[AllowDynamicProperties]
class social_media_buttons_class
{
	private $css_string;
	private $css_string_addon;
	private $js_string;
	private $js_string_addon;

	/**
	 * social_media_buttons_class constructor.
	 */
	public function __construct()
	{
		global $content, $checked, $db, $db_praefix, $user;
		$this->content =& $content;
		$this->checked =& $checked;
		$this->db =& $db;
		$this->db_praefix =& $db_praefix;
		$this->user =& $user;

		$this->main();
	}

	/**
	 * Die Hauptschleife
	 */
	private function main()
	{
		if (defined("admin")) {
			$this->user->check_intern();
			global $template;

			if (strpos("XXX" . $template, "social_media_buttons_back.html")) {
				$this->backend();
			}
		}
		else {
			$this->frontend();
		}
	}

	/**
	 * Backend
	 */
	private function backend()
	{
		if (isset($this->checked->submit) && $this->checked->submit) {
			$this->db_write();
			$this->db_write_config();
		}

		$this->content->template['plugin']['social_media_buttons']['buttons'] = $this->db_read_all_buttons();
		$this->content->template['plugin']['social_media_buttons']['config'] = $this->db_read_config();
	}

	/**
	 * Frontend
	 */
	private function frontend()
	{
		$this->generate_config_file();

		$active_buttons = $this->db_read_active_buttons();
		$this->content->template['plugin']['social_media_buttons']['config'] = $this->db_read_config();

		if ($this->content->template['plugin']['social_media_buttons']['config']['vertical'] == 1) {
			$this->content->template['plugin']['social_media_buttons']['config']['orientation'] = 'vertical';
		}
		else {
			$this->content->template['plugin']['social_media_buttons']['config']['orientation'] = 'horizontal';
		}

		// Den data-service-string erstellen
		$active_button_names = [];
		if (sizeof($active_buttons) > 0) {
			foreach ($active_buttons as $button) {
				$active_button_names[] = '&quot;' . $button['name'] . '&quot;';
			}
		}

		// Die html-Strings für die Einbindung erstellen
		$css_file = 'shariff.min.css';
		$js_file = 'shariff.min.js';

		if ($this->content->template['plugin']['social_media_buttons']['config']['fontawesome'] == 1) {
			$css_file = 'shariff.complete.css';
		}

		$this->css_string = '<link rel="stylesheet" type="text/css" href="' . PAPOO_WEB_PFAD . '/plugins/social_media_buttons/css/' . $css_file . '" />';
		$this->css_string_addon = '<link rel="stylesheet" type="text/css" href="' . PAPOO_WEB_PFAD . '/plugins/social_media_buttons/css/add.css" />';
		$this->js_string = '<script src="' . PAPOO_WEB_PFAD . '/plugins/social_media_buttons/js/' . $js_file . '"></script>';
		$this->content->template['plugin']['social_media_buttons']['active_buttons_template_string'] = implode(",", $active_button_names);
	}

	private function generate_config_file()
	{
		if (!is_file(SHARIFF_CONFIG_FILE)) {
			$config = file_get_contents(__DIR__ . "/../shariff-backend-php/shariff.json");
			file_put_contents(SHARIFF_CONFIG_FILE, $config, true);
		}

		$active_buttons = $this->db_read_active_buttons();

		$provided_backend_services = array(
			"Facebook",
			"LinkedIn",
			"Reddit",
			"StumbleUpon",
			"Flattr",
			"Pinterest",
			"AddThis"
		);

		$active_backend_services = array();
		foreach ($active_buttons as $active_button) {
			if (in_array($active_button['display'], $provided_backend_services)) {
				$active_backend_services[] = $active_button['display'];
			}
		}

		// Backend - Config einlesen
		$shariff_backend_config = json_decode(file_get_contents(SHARIFF_CONFIG_FILE), true);

		// Backend - Config manipulieren
		$shariff_backend_config['domains'] = array($this->content->template['site_name']);
		$shariff_backend_config['services'] = $active_backend_services;
		$shariff_backend_config['cache']['cacheDir'] = PAPOO_ABS_PFAD . "/templates_c/";

		// Backend - Config schreiben
		if (is_writable(SHARIFF_CONFIG_FILE)) {
			file_put_contents(SHARIFF_CONFIG_FILE, json_encode($shariff_backend_config), true);
		}
	}

	/**
	 * Liefert alle in der Datenbank verfügbaren Button-Namen
	 *
	 * @return array
	 */
	private function get_button_names()
	{
		$buttons = $this->db_read('all');

		foreach ($buttons as $button) {
			$button_names[] = $button['name'];
		}
		return $button_names;
	}

	public function output_filter()
	{
		global $output;
		$output = str_replace("</head>", $this->css_string . "</head>", $output);
		$output = str_replace("</head>", $this->css_string_addon . "</head>", $output);
		$output = str_replace("</body>", $this->js_string . "</body>", $output);
		$output = str_replace("</body>", $this->js_string_addon . "</body>", $output);

		if (strstr($output, "#social_media_buttons#")) {
			$modul = '<div class="shariff"
                        data-services="['.$this->content->template['plugin']['social_media_buttons']['active_buttons_template_string'].']"
                        data-theme="'.$this->content->template['plugin']['social_media_buttons']['config']['theme'].'"
                        data-lang="'.$this->content->template['lang_short'].'"
                        data-backend-url="'.PAPOO_WEB_PFAD.'/plugins/social_media_buttons/shariff-backend-php"
                        data-orientation="'.$this->content->template['plugin']['social_media_buttons']['config']['orientation'].'"
                        data-mail-url="mailto:"></div>';

			$output = str_replace('#social_media_buttons#', $modul, $output);
		}
	}

	/**
	 * @return array|void
	 */
	private function db_read_active_buttons()
	{
		$query = sprintf("SELECT * FROM %s WHERE aktiv = 1", $this->db_praefix . "plugin_social_media_buttons");
		return $this->db->get_results($query, ARRAY_A);
	}

	/**
	 * @return array|void
	 */
	private function db_read_all_buttons()
	{
		$query = sprintf("SELECT * FROM %s", $this->db_praefix . "plugin_social_media_buttons");
		return $this->db->get_results($query, ARRAY_A);
	}

	/**
	 * @return mixed
	 */
	private function db_read_config()
	{
		$query = sprintf("SELECT * FROM %s", $this->db_praefix . "plugin_social_media_buttons_config");
		$result = $this->db->get_results($query, ARRAY_A);

		foreach ($result as $k => $v) {
			foreach ($v as $k2 => $v2) {
				$return[$k2] = $v2;
			}
		}

		return $return;
	}

	private function db_write_config()
	{
		$fontawesome = 0;
		$vertical = 0;

		$config = $this->checked->config;

		if (isset($config['fontawesome']) && $config['fontawesome'] == 1) {
			$fontawesome = 1;
		}

		if (isset($config['vertical']) && $config['vertical'] == 1) {
			$vertical = 1;
		}

		$query = sprintf
		(
			"UPDATE %s
                SET fontawesome='%s', theme='%s', vertical='%s';",

			$this->db_praefix . "plugin_social_media_buttons_config",
			$fontawesome,
			$config['theme'],
			$vertical
		);
		$this->db->query($query);
	}

	/**
	 * Schreibt die Daten in die Datenbank
	 */
	private function db_write()
	{
		// alle angehakten Checkboxen
		$checked_buttons = $_POST['buttons'];

		// Keine Checkbox angehakt
		if (sizeof($checked_buttons) == 0) {
			$db_checked_buttons = (array)'""';
		} // mindestens eine Checkbox angehakt
		else {
			foreach ($checked_buttons as $button) {
				$db_checked_buttons[] = '"' . $this->db->escape($button) . '"';
			}
		}

		// string für die sql-query zusammenfummeln
		$db_checked_buttons = implode(',', $db_checked_buttons);
		$query = sprintf
		(
			"UPDATE %s
                SET aktiv = CASE
                WHEN `name` IN (%s) THEN 1
                ELSE 0
                END;",

			$this->db_praefix . "plugin_social_media_buttons",
			$db_checked_buttons
		);

		$this->db->query($query);
	}
}

$social_media_buttons = new social_media_buttons_class();
