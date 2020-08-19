<?php

/**
 * Class devtool_modlang_class
 */
class devtool_modlang_class
{
	/**
	 * devtool_modlang_class constructor.
	 */
	function __construct()
	{
		global $db, $cms, $db_praefix, $checked, $content, $user;
		$this->db = &$db;
		$this->cms = &$cms;
		$this->db_praefix = $db_praefix;
		$this->checked = &$checked;
		$this->content = &$content;
		$this->user = &$user;

		// Aktions-Weiche
		// **************
		global $template;
		if (defined("admin")) {
			$this->user->check_intern();
			global $template;
			if (defined("admin") && strpos("XXX" . $template, "modlang_backend.html")) {
				IfNotSetNull($this->checked->modlang_action);
				switch ($this->checked->modlang_action) {
				case "modlang_do":
					$this->modlang_do();
					$this->content->template['modlang_message'] = $this->content->template['plugin']['devtools']['erfolg'];
					break;

				case "mod_new":
					$this->mod_new();
					$this->content->template['modlang_message'] = $this->content->template['plugin']['devtools']['erfolg'];
					break;

				default:
					break;
				}
			}
		}
	}

	/**
	 * Neues Modul erstellen
	 */
	function mod_new()
	{
		if (!empty($this->checked->mod_new_name) && !empty($this->checked->mod_new_file) && !empty($this->checked->mod_new_descrip)) {
			// Daten in Tabelle papoo_module eintragen
			$sql = sprintf("INSERT INTO %s SET mod_bereich_id='%d', mod_modus='%s', mod_datei='%s'",
				$this->db_praefix . "papoo_module",
				$bereich,
				$modus,
				$this->db->escape($this->checked->mod_new_file)
			//,
			//$style->style_id,
			//$mod_id
			);
			$this->db->query($sql);
			$mod_id = $this->db->insert_id;

			$sql = sprintf("INSERT INTO %s SET modlang_mod_id='%d', modlang_lang_id='%d', modlang_name='%s', modlang_beschreibung='%s'",
				$this->db_praefix . "papoo_module_language",
				$mod_id,
				$this->cms->lang_id,
				$this->db->escape($this->checked->mod_new_name),
				$this->db->escape($this->checked->mod_new_descrip)
			);
			$this->db->query($sql);

			// Andere Sprachen setzen
			$this->modlang_do();
		}
	}

	function modlang_do()
	{
		// 1. Sprach-Tabelle auslesen
		$sql = sprintf("SELECT * FROM %s ORDER BY lang_id", $this->db_praefix . "papoo_name_language");
		$sprachen = $this->db->get_results($sql, ARRAY_A);

		// 2. Modul-Tabelle auslesen
		$sql = sprintf("SELECT DISTINCT mod_datei, mod_id FROM %s", $this->db_praefix . "papoo_module");
		$module = $this->db->get_results($sql, ARRAY_A);

		// 3. Schleife durch alle Module
		if (!empty($module)) {
			foreach($module as $modul) {
				// 3.A bestehende Language-Eintr�ge dieses Moduls auslesen
				$sql = sprintf("SELECT * FROM %s WHERE modlang_mod_id='%d' ORDER BY modlang_lang_id",
					$this->db_praefix . "papoo_module_language",
					$modul['mod_id']
				);
				$texte = $this->db->get_results($sql, ARRAY_A);

				// 3.B $texte-Array in die Form $lang_texte[lang_id] = array(..) bringen
				$lang_texte = array();
				if (!empty($texte)) {
					foreach ($texte as $text) {
						$lang_texte[$text['modlang_lang_id']] = $text;
					}
				}

				// 3.C bestehende Language-Eintr�ge dieses Moduls l�schen
				$sql = sprintf("DELETE FROM %s WHERE modlang_mod_id='%d'",
					$this->db_praefix . "papoo_module_language",
					$modul['mod_id']
				);
				$this->db->query($sql);

				// 3.D Eintr�ge f�r alle Sprachen vornehmen
				foreach ($sprachen as $sprache) {
					$sql = sprintf("INSERT INTO %s SET
									modlang_mod_id='%d', modlang_lang_id='%d', modlang_name='%s', modlang_beschreibung='%s'",
						$this->db_praefix . "papoo_module_language",
						$modul['mod_id'],
						$sprache['lang_id'],
						$this->db->escape($this->fallback_text($lang_texte, $feld = "modlang_name", $sprache['lang_id'])),
						$this->db->escape($this->fallback_text($lang_texte, $feld = "modlang_beschreibung", $sprache['lang_id']))
					);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * @param array $text_array
	 * @param string $feld
	 * @param $sprach_id
	 * @return mixed|string
	 */
	function fallback_text($text_array = array(), $feld = "", $sprach_id)
	{
		$text = @$text_array[$sprach_id][$feld];
		// 1. Fallback auf englisch
		if (empty($text)) $text = @$text_array[2][$feld];
		// 2. Fallback auf deutsch
		if (empty($text)) $text = @$text_array[1][$feld];

		return $text;
	}
}

$devtool_modlang = new devtool_modlang_class();
