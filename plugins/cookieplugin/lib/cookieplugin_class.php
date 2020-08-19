<?php

/**
 * Hauptdatei für das Cookie Plugin.
 *
 * @author Andreas Gritzan <ag@papoo.de>
 */

/**
 * Class cookieplugin_class
 *
 * Hauptklasse des Plugins welches einen Cookie Datenschutzhinweis anzeigt.
 *
 * @author Andreas Gritzan <ag@papoo.de>
 */
class cookieplugin_class
{
	/**
	 * cookieplugin_class constructor.
	 */
	function __construct()
	{
		global $content, $db, $db_praefix, $checked, $user;
		$this->content = & $content;
		$this->db = & $db;
		$this->db_praefix = & $db_praefix;
		$this->checked = & $checked;
		$this->user = & $user;

		if(defined('admin')) {
			$this->user->check_intern();
			#$this->shop->echo_test();
			global $template;
			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ($template != "login.utf8.html") {
				if (stristr($template2,"cookie_backend")) {
					// CSS für die "Änderungen gespeichert"-Nachricht
					$this->content->template['css_path'] = $css_path = PAPOO_WEB_PFAD.'/plugins/cookieplugin/css';

					$this->GetContent();
					$this->CheckPost();
				}
			}
		}
	}

	/**
	 * Funktion die die vorhandenen Einstellungen in das content->template lädt, damit die Forms mit den
	 * vorhandenen Einstellungen gefüllt werden können.
	 */
	private function GetContent()
	{
		$this->content->template['plugin']['cookieplugin']['settings'] = $this->GetSettings();
	}

	/**
	 * Überprüft ob neue Einstellungen per POST abgeschickt wurden und speichert diese dann in die Datenbank.
	 */
	private function CheckPost()
	{
		if(isset($this->checked->cookieplugin_message) or isset($this->checked->cookieplugin_theme)) {
			// Alte Einstellungen löschen
			$sql = sprintf("DELETE FROM %s", $this->db_praefix . "cookieplugin_settings");
			$this->db->query($sql);

			// Neue hinzufügen
			$sql = sprintf("INSERT INTO %s VALUES ('message', '%s'), ('buttontext', '%s'), ('learnmore', '%s'), ('link', '%s'), ('theme', '%s'), ('button_text_color', '%s'), ('button_back_color', '%s'), ('button_hover_text_color_input', '%s'), ('button_hover_back_color_input', '%s'), ('more_info_link_color_input', '%s')",
				$this->db_praefix . "cookieplugin_settings",
				$this->db->escape($this->checked->cookieplugin_message),
				$this->db->escape($this->checked->cookieplugin_buttontext),
				$this->db->escape($this->checked->cookieplugin_learnmore),
				$this->db->escape($this->checked->cookieplugin_link),
				$this->db->escape($this->checked->cookieplugin_theme),
				$this->db->escape($this->checked->style_override['button_text_color']),
				$this->db->escape($this->checked->style_override['button_back_color']),
				$this->db->escape($this->checked->style_override['button_hover_text_color_input']),
				$this->db->escape($this->checked->style_override['button_hover_back_color_input']),
				$this->db->escape($this->checked->style_override['more_info_link_color_input'])
			);

			$this->db->query($sql);
		}
	}

	/**
	 * Holt die Einstellung mit Namen $name und gibt den Wert zurück.
	 *
	 * @param $name
	 * @param $settings_db
	 * @return mixed|null
	 */
	private function GetSetting($name, $settings_db)
	{
		if(!is_array($settings_db)) {
			return null;
		}

		foreach($settings_db as $setting) {
			if($name == $setting['name']) {
				return $setting['value'];
			}
		}

		return null;
	}

	/**
	 * Holt die Einstellungen bzgl. dem was dem Plugin als Nachricht übergeben wird, etc., aus der Datenbank.
	 *
	 * @returns array Die Einstellungen [message, buttontext, learnmore, link, theme]
	 */
	private function GetSettings()
	{
		$sql = sprintf("SELECT * FROM %s", $this->db_praefix . "cookieplugin_settings");

		$result = $this->db->get_results($sql, ARRAY_A);

		$settings = array();

		$settings['message'] = $this->GetSetting("message", $result);
		$settings['buttontext'] = $this->GetSetting("buttontext", $result);
		$settings['learnmore'] = $this->GetSetting("learnmore", $result);
		$settings['link'] = $this->GetSetting("link", $result);
		$settings['theme'] = $this->GetSetting("theme", $result);
		$settings['button_text_color'] = $this->GetSetting("button_text_color", $result);
		$settings['button_back_color'] = $this->GetSetting("button_back_color", $result);
		$settings['button_hover_text_color_input'] = $this->GetSetting("button_hover_text_color_input", $result);
		$settings['button_hover_back_color_input'] = $this->GetSetting("button_hover_back_color_input", $result);
		$settings['more_info_link_color_input'] = $this->GetSetting("more_info_link_color_input", $result);

		return $settings;
	}

	/**
	 * @ignore
	 */
	function post_papoo()
	{
	}

	function output_filter()
	{
		$settings = $this->GetSettings();

		$message = isset($settings['message']) ? $settings['message'] : 'Diese Webseite benutzt Cookies um Ihnen das beste Erlebnis beim benutzen der Webseite bieten zu können.';
		$buttontext = isset($settings['buttontext']) ? $settings['buttontext'] : 'OK';
		$learnmore = isset($settings['learnmore']) ? $settings['learnmore'] : 'null';
		$link = isset($settings['link']) ? $settings['link'] : 'null';
		$theme = isset($settings['theme']) ? $settings['theme'] : 'dark-bottom';

		$consent_script = '<!-- Begin Cookie Consent plugin by Silktide - http://silktide.com/cookieconsent -->';
		$consent_script .= '<script type="text/javascript">';
		$consent_script .= 'window.cookieconsent_options = {"message":"' . $message . '","dismiss":"' . $buttontext . '","learnMore":"' . $learnmore . '","link":"' . $link . '","theme":"'. $theme . '"};';
		$consent_script .= '</script>';
		$consent_script .= '<script type="text/javascript" src="//cdn.static-fra.de/lib/vendor/silktide/cookieconsent2/1.0.9/build/cookieconsent.min.js"></script>';
		$consent_script .= '<!-- End Cookie Consent plugin -->';

		if(isset($settings['button_text_color']) or isset($settings['button_back_color']) or isset($settings['button_hover_text_color_input']) or isset($settings['button_hover_back_color_input']) or isset($settings['more_info_link_color_input'])) {
			$colormod = '<script type="text/javascript">';
			$colormod .= 'var styletext = "a.cc_btn.cc_btn_accept_all { background-color: ' . $settings['button_back_color'] . ' !important; color: ' . $settings['button_text_color'] . ' !important;} a.cc_btn.cc_btn_accept_all:hover { background-color: ' . $settings['button_hover_back_color_input'] . ' !important; color: ' . $settings['button_hover_text_color_input'] . ' !important;} a.cc_more_info { color: ' . $settings['more_info_link_color_input'] . ' !important;}";

      var link = document.createElement("style");
      link.type = \'text/css\';

      var styletextelement = document.createTextNode(styletext);

      link.appendChild(styletextelement);

        var head = document.getElementsByTagName("head")[0];

      document.getElementsByTagName("head")[0].appendChild(link);';
			//$colormod .= '.cc_container { .cc_btn, .cc_btn:visited { background-color: #ff0000;} }';
			$colormod .= '</script>';
		}

		//document.getElementsByTagName("head")[0].appendChild(link);';

		global $output;
		IfNotSetNull($colormod);
		$output = preg_replace("(<head>)", "$0".$consent_script . "\n" . $colormod . "\n", $output);
	}

}

$cookieplugin = new cookieplugin_class();
