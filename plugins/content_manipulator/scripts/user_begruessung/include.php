<?php

/**
 * Class user_begruessung_class
 */
class user_begruessung_class
{
	var $text_begruessung = 'Sie sind angemeldet als: %s.';

	var $text_begruessung_en = 'You are logged in as: %s.';

	var $text_begruessung_fr = 'Vous êtes connecté en tant que: %s.';

	/**
	 * user_begruessung_class constructor.
	 */
	function __construct()
	{
		// Content Objekt, beinhaltet die Message Daten f�r das Backend
		global $content;
		$this->content = &$content;

		if (!defined("admin")) {
			global $output;
			if (strstr($output,"#user_begruessung#")) {
				$output = $this->content_manipulate();
			}
		}
		else {
			$this->set_backend_message();
		}
	}

	/**
	 * @return bool|false|mixed|resource|string|string[]|void|null
	 */
	function content_manipulate()
	{
		global $output;
		$temp_return = $output;

		global $cms;

		$temp_begruessung = "";

		if ($this->content->template['loggedin']) {
			$temp_username = $this->content->template['username'];

			if ($cms->lang_id=="1") {
				$temp_begruessung = sprintf($this->text_begruessung, $temp_username);
			}

			if ($cms->lang_id=="2") {
				$temp_begruessung = sprintf($this->text_begruessung_en, $temp_username);
			}

			if ($cms->lang_id=="5") {
				$temp_begruessung = sprintf($this->text_begruessung_fr, $temp_username);
			}
		}

		$temp_return = str_replace('#user_begruessung#', $temp_begruessung, $temp_return);

		return $temp_return;
	}

	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][] = 'User-Begrüßung';
		$this->content->template['plugin_cm_body']['de'][] =
			sprintf('<strong>#user_begruessung#</strong> wird erstzt durch: <em>"'.$this->text_begruessung.'"</em>', $this->content->template['username']);
		$this->content->template['plugin_cm_img']['de'][] = '';
	}
}

$user_begruessung = new user_begruessung_class();
