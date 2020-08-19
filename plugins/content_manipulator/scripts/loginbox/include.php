<?php

/**
 * Class loginbox
 */
class loginbox
{
	/** @var content_class */
	public $content;
	/** @var checked_class */
	public $checked;
	/** @var cms */
	public $cms;
	/** @var ezSQL_mysqli */
	public $db;

	/** @var string Hier liegen die Templates für das "Skript" */
	private $templatePath = PAPOO_ABS_PFAD . '/plugins/content_manipulator/scripts/loginbox/templates/';

	/**
	 * loginbox constructor.
	 */
	public function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		if (!defined("admin") || $_GET['is_lp'] == 1) {
			global $output;
			if (strstr($output, "#loginbox") !== false) {
				$output = $this->create_loginbox($output);
			}
		}
	}

	/**
	 * Adds an entry in the content manipulator dashboard
	 *
	 * @return void
	 */
	private function set_backend_message()
	{
		// Überschrift - de = Deutsch; en = Englisch
		$this->content->template['plugin_cm_head']['de'][] = "Skript Einbau Loginbox an beliebiger Stelle.";

		// Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][] =
			"Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten die Login Box einbauen, "
			. "die Syntax lautet:<br /><strong>#loginbox#</strong><br /> ";

		// Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * Replaces all occurrences of the shortcode with HTML code
	 *
	 * @param string $inhalt
	 *
	 * @return string
	 */
	private function create_loginbox($inhalt = "")
	{
		return str_replace('#loginbox#', $this->get_loginbox(), $inhalt);
	}

	/**
	 * Parses a template depending on the login status and returns the HTML
	 *
	 * @return string
	 */
	private function get_loginbox()
	{
		global $content, $user;

		// Wenn eingeloggt, dann das Benutzermenü darstellen
		$template = file_get_contents(
			$this->templatePath . ($user->userid > 11 || $user->userid == 10 ? 'is_logged_in.html' : 'login_box.html')
		);

		if (is_array($content->template)) {
			foreach ($content->template as $key => $value) {
				if (!is_array($value)) {
					$template = str_replace('{$' . $key . '}', $value, $template);
				}
			}
		}

		$replacements = [
			'#sperre#' => isset($content->template['sperre']) ? $content->template['message_2156'] : '',
			'#logfalse#' => isset($content->template['logfalse']) ? $content->template['message_2141'] : '',
			'#loggedin_false_pass#' => isset($content->template['loggedin_false_pass']) ? $content->template['message_2141a'] : '',
		];
		foreach ($replacements as $shortcode => $replacement) {
			$template = str_replace($shortcode, $replacement, $template);
		}

		return $template;
	}
}

$loginbox = new loginbox();
