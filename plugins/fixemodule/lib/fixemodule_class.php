<?php

namespace FixeModule;

// Funktionen wie Felder hinzufügen und löschen oder Module hinzufügen und löschen sind nur im Entwickler-Modus verfügbar
const FIXEMODULE_DEV_MODE = true;

require_once __DIR__ . "/system/View.php";
require_once __DIR__ . "/system/Controller.php";
require_once __DIR__ . "/controllers/Backend.php";
require_once __DIR__ . "/controllers/Frontend.php";
require_once PAPOO_ABS_PFAD . '/lib/php-activerecord/ActiveRecord.php';

/**
 * Hauptdatei für das fixemodule-Plugin.
 *
 * @author Martin Güthler <mg@papoo.de>
 */

/**
 * class fixemodule_class
 *
 * Hauptklasse des Plugins.
 *
 * @author Martin Güthler <mg@papoo.de>
 */
class fixemodule_class
{
	const PREFIX = 'fixemodule';

	/**
	 * fixemodule_class constructor.
	 */
	function __construct()
	{
		global $user, $db_abs, $db, $db_praefix;
		$this->user = &$user;
		$this->db_abs = &$db_abs;
		$this->db = &$db;
		$this->db_praefix = &$db_praefix;

		if (!defined("PAPOO_DB_PREFIX")) {
			define("PAPOO_DB_PREFIX", $this->db_praefix);
		}

		global $checked;
		$this->checked = &$checked;

		global $content;
		$this->content = &$content;

		$paths = \ActiveRecord\Config::instance()->get_model_directories();
		$paths[] = __DIR__ . '/models';
		\ActiveRecord\Config::instance()->set_model_directories($paths);

		if (defined('admin')) {
			$this->user->check_intern();
			global $template;
			$template2 = str_ireplace(PAPOO_ABS_PFAD . '/plugins/', '', $template);
			$template2 = basename($template2);

			if ($template != 'login.utf8.html') {
				if (stristr($template2, "fixemodule")) {
					// Pfad zum css Ordner zur Einbindung des backend css
					$this->content->template['css_path'] = $css_path = PAPOO_WEB_PFAD . '/plugins/fixemodule/css';

					// Backend
					$backend = new Backend;
					$backend->run();
				}
			}
		}
		else {
			// Frontend
			$frontend = new Frontend();
			$frontend->run();
		}
	}

	/**
	 * Funktion die vor dem Senden des Seiteninhalts für jedes Plugin aufgerufen wird,
	 * bei der dann per global $output und z.B. einem regulären Ausdruck der Inhalt
	 * verändert werden kann.
	 * (Backend)
	 */
	public function output_filter_admin()
	{
		#global $output;
	}

	/**
	 * Sucht nach Platzhaltern der Form #fixemdule.modulname.feldname# im content und ersetzt diese.
	 */
	public function output_filter()
	{
		global $output;
		$this->output = &$output;

		$this->frontend = new Frontend();
		$this->module = $this->frontend->run();

		if (strstr($this->output, "#fixemodule")) {
			$this->output = $this->replace_placeholders($this->output);
		}
	}

	/**
	 * @param $str
	 * @return mixed
	 */
	private function replace_placeholders($str)
	{
		preg_match_all("|#fixemodule(.*?)#|", $str, $gefundene_platzhalter, PREG_PATTERN_ORDER);
		$gefundene_platzhalter = $gefundene_platzhalter[0];

		$replacements = array();

		foreach ($gefundene_platzhalter as $platzhalter) {
			list($null, $modulname, $feldname, $attribut) =
				array_pad(explode(".", str_replace("#", "", $platzhalter)), 10, null);

			if (isset ($this->module[$modulname][$feldname])) {
				$module = &$this->module[$modulname];

				if ($feldname == 'html') {
					// Ersetze weitere Platzhalter fixer Module
					$this->module[$modulname][$feldname] = $this->replace_placeholders($this->module[$modulname][$feldname]);

					// Ersetze inhaltliche Platzhalter des aktuellen Moduls
					if (preg_match_all("|#(.*?)#|", $module['html'], $matches, PREG_PATTERN_ORDER)) {
						foreach ($matches[1] as $placeholder) {
							$module['html'] = str_replace("#{$placeholder}#", $module[$placeholder], $module['html']);
						}
					}
				}
				if (isset($attribut)) {
					$replacements[] = array($platzhalter, $this->module[$modulname][$feldname][$attribut]);
				}
				else {
					$replacements[] = array($platzhalter, $this->module[$modulname][$feldname]);
				}

				unset($module);
			}
		}
		foreach ($replacements as $replacement) {
			$str = str_replace($replacement[0], $replacement[1], $str);
		}

		return $str;
	}
}

$fixemodule = new fixemodule_class();
