<?php

/**
 * Class cachecleaner_class
 */
class cachecleaner_class {
	/**
	 * cachecleaner_class constructor.
	 */
	function __construct()
	{
		// Einbindung globaler papoo-Klassen
		global $checked, $content, $diverse, $db;
		$this->checked = &$checked;
		$this->content = &$content;
		$this->diverse = &$diverse;
		$this->db = &$db;

		$this->pfadhier = PAPOO_ABS_PFAD;
		// Aktions-Weiche
		// **************
		// User Klasse einbinden
		global $user;
		$this->user = &$user;
		// Aktions-Weiche
		// **************
		global $template;
		if (defined("admin")) {
			$this->user->check_intern();
			global $template;
			if (strpos("XXX" . $template, "cachecleaner_backend.html")) {
				IfNotSetNull($this->checked->cachecleaner_action);
				switch ($this->checked->cachecleaner_action) {
				case "clean":
					$this->clean_cache_templates();
					$this->clean_cache_css();
					$this->clean_cache_sites();
					unset($_SESSION['metadaten']);
					$this->content->template['cachecleaner_message'] = $this->content->template['plugin']['devtools']['cache_deleted'];
					break;
				}
			}
		}
	}

	function clean_cache_templates()
	{
		// 1. Template-Cache-Dateien im interna-Bereich l�schen
		$this->diverse->remove_files($this->pfadhier . "/interna/templates_c/", ".html.php");
		$this->diverse->remove_files($this->pfadhier . "/interna/templates_c/standard/", ".html.php");
		$this->diverse->remove_files($this->pfadhier . "/interna/templates_c/upload/", ".*");

		// 2. Template-Cache-Dateien im Frontend-Bereich l�schen
		$this->diverse->remove_files($this->pfadhier . "/templates_c/", ".html.php");

		//ical Datein löschen
		$this->diverse->remove_files($this->pfadhier . "/templates_c/", "ical");
		$this->diverse->remove_files($this->pfadhier . "/templates_c/", ".ics");
	}

	function clean_cache_css()
	{
		// 1. Menuint-CSS-Datei des interna-Bereichs l�schen
		$this->diverse->remove_files($this->pfadhier . "/interna/templates_c/", "_menuint.css");
		// 2. Plugins-CSS-Datei l�schen
		$this->diverse->remove_files($this->pfadhier . "/templates_c/", "_plugins.css");
	}

	function clean_cache_sites ()
	{
		// 1. Eintr�ge in Cache-Tabelle l�schen
		global $db_praefix;
		$sql = sprintf("DELETE FROM %s ", $db_praefix . "papoo_cache");
		$this->db->query($sql);
		// 2. Cache-Dateien l�schen
		$this->diverse->remove_files($this->pfadhier . "/cache/", ".html");
	}
}

$cachecleaner = new cachecleaner_class();
