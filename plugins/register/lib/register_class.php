<?php

/**
 * Class register_class
 */
#[AllowDynamicProperties]
class register_class
{
	/** @var array diverse Einstellungen */
	var $einstellungen = array();

	/**
	 * register_class constructor.
	 */
	function __construct()
	{
		global $cms, $user, $db, $db_praefix, $checked, $content;
		$this->cms = & $cms;
		$this->user = & $user;
		$this->db = & $db;
		$this->db_praefix = $db_praefix;
		$this->checked = & $checked;
		$this->content = & $content;

		$this->einstellungen = array("umbruch_zeichen" => array("I", "Q") );

		$this->make_register();
	}

	/**
	 * Global Aktions-Weiche der Klasse
	 */
	function make_register()
	{
		global $template;

		if (!defined("admin")) {
			// Frontend
			if (strpos("XXX".$template, "/register_front.html")) {
				$this->switch_front();
			}
		}
		else {
			// Backend
			if (strpos("XXX".$template, "/register_back.html")) {
				$this->user->check_intern();
				$this->switch_back(@$this->checked->action);
			}
			if (strpos("XXX".$template, "/register_back_einbindung.html")) {
				$this->user->check_intern();
			}
		}
	}

	/**
	 * Aktions-Weiche f�r Frontend
	 */
	function switch_front()
	{
		$this->content->template['plugin']['register']['register_liste'] = $this->register_liste($this->checked->reg_reg_id);
	}

	/**
	 * Aktions-Weiche f�r Backend
	 *
	 * @param string $action
	 */
	function switch_back($action = "")
	{
		switch ($action) {
		case "register_eintrag_save":
			$this->eintraege_save($this->checked->save_mode, $this->checked->reg_id);
			$this->switch_back();
			break;

		case "register_eintrag_neu":
			$this->content->template['plugin']['register']['modus'] = "NEU";
			$this->content->template['plugin']['register']['template_weiche'] = "REGISTER_EINTRAG_NEU_EDIT";
			break;

		case "register_eintrag_edit":
			$temp_eintrag = $this->eintraege_data($this->checked->reg_id);
			$temp_eintrag['reg_text'] = "nodecode:".$temp_eintrag['reg_text'];
			$this->content->template['plugin']['register']['eintrag_data'] = $temp_eintrag;
			$this->content->template['plugin']['register']['modus'] = "EDIT";
			$this->content->template['plugin']['register']['template_weiche'] = "REGISTER_EINTRAG_NEU_EDIT";
			break;

		case "register_eintrag_delete":
			$this->content->template['plugin']['register']['eintrag_data'] = $this->eintraege_data($this->checked->reg_id);
			$this->content->template['plugin']['register']['template_weiche'] = "REGISTER_EINTRAG_DELETE";
			break;

		case "register_eintrag_delete_do":
			$this->eintraege_delete($this->checked->reg_id);
			$this->switch_back();
			break;

		case "":
		default:
			$this->content->template['plugin']['register']['register_liste'] = $this->register_liste();
			$this->content->template['plugin']['register']['template_weiche'] = "REGISTER_LISTE";
			break;
		}
	}


	// ===========================================================
	//
	// Register Funktionen
	//
	// ===========================================================

	/**
	 * Erstellt das Register
	 *
	 * @param int $reg_reg_id
	 * @return array
	 */
	function register_liste($reg_reg_id = 0)
	{
		$temp_return = array();
		$temp_range = range("A", "Z");
		$temp_buchstabe_break_last = max($this->einstellungen['umbruch_zeichen']);

		foreach ($temp_range as $buchstabe) {
			$temp_break = false;
			$temp_break_last = false;
			if (in_array($buchstabe, $this->einstellungen['umbruch_zeichen'])) {
				$temp_break = true;

				if ($buchstabe == $temp_buchstabe_break_last) {
					$temp_break_last = true;
				}
			}

			$temp_return[$buchstabe] = array("buchstabe" => $buchstabe, "break" => $temp_break, "break_last" => $temp_break_last, "eintraege" => array());
		}

		$temp_eintraege = $this->eintraege_liste($reg_reg_id);
		if (!empty($temp_eintraege)) {
			foreach($temp_eintraege as $eintrag) {
				$temp_return[$eintrag['reg_buchstabe']]['eintraege'][] = $eintrag;
			}
		}

		return $temp_return;
	}

	// ===========================================================
	//
	// Eintr�ge Funktionen
	//
	// ===========================================================

	/**
	 * Gibt eine Liste aller Eintraege (alphabetisch sortiert) zurueck
	 *
	 * @param int $reg_reg_id
	 * @return array|void
	 */
	function eintraege_liste ($reg_reg_id = 0)
	{
		$temp_return = array();

		$sql_add = "";
		if ($reg_reg_id) {
			$sql_add = sprintf("WHERE reg_reg_id IN (0,%d)", $reg_reg_id);
		}

		$sql = sprintf("SELECT * FROM %s %s
						ORDER BY reg_name_order ASC",
			$this->db_praefix."register",
			$sql_add
		);
		$temp_return = $this->db->get_results($sql, ARRAY_A);

		return $temp_return;
	}

	/**
	 * Gibt die Daten des Eintrags $reg_id zur�ck
	 *
	 * @param $reg_id
	 * @return array|void
	 */
	function eintraege_data ($reg_id)
	{
		$temp_return = array();

		if ($reg_id) {
			$sql = sprintf("SELECT * FROM %s 
							WHERE reg_id='%d'",
				$this->db_praefix."register",
				$reg_id
			);
			$temp_return = $this->db->get_row($sql, ARRAY_A);
		}

		return $temp_return;
	}

	/**
	 * Speichert einen Eintrag
	 *
	 * @param string $modus [NEU, EDIT]
	 * @param int $reg_id ID es Eintrags (bei $modus = EDIT)
	 */
	function eintraege_save($modus = "EDIT", $reg_id = 0)
	{
		if ($modus == "NEU") {
			$sql = sprintf("INSERT INTO %s SET reg_reg_id='0'", $this->db_praefix."register");
			//echo "SQL: ".$sql;
			$this->db->query($sql);
			$reg_id = $this->db->insert_id;
		}

		if ($reg_id) {
			$temp_name = trim($this->checked->reg_name);
			if ($temp_name != NULL) {
				$temp_name_order = mb_strtolower($this->helper_umlaut_konverter($temp_name), "utf-8");
				$temp_buchstabe = mb_strtoupper($temp_name_order[0], "utf-8");
			}
			else {
				$temp_name_order = NULL;
				$temp_buchstabe = NULL;
			}

			$sql = sprintf("UPDATE %s
							SET reg_reg_id='%d', reg_buchstabe='%s', reg_name='%s', reg_name_order='%s', reg_text='%s'
							WHERE reg_id='%d'",
				$this->db_praefix."register",

				$this->checked->reg_reg_id,
				$this->db->escape($temp_buchstabe),
				$this->db->escape($temp_name),
				$this->db->escape($temp_name_order),
				$this->db->escape($this->checked->reg_text),

				$reg_id
			);
			$this->db->query($sql);
		}
	}

	/**
	 * Loescht die Daten des Eintrags $reg_id
	 *
	 * @param $reg_id
	 */
	function eintraege_delete ($reg_id)
	{
		if ($reg_id) {
			$sql = sprintf("DELETE FROM %s WHERE reg_id='%d'",
				$this->db_praefix."register",
				$reg_id
			);
			$this->db->query($sql);
		}
	}


	// ===========================================================
	//
	// Hilfs Funktionen
	//
	// ===========================================================
	/**
	 * @param string $text
	 * @return mixed|string
	 */
	function helper_umlaut_konverter($text = "")
	{
		$temp_return = trim($text);

		$temp_return = str_replace(utf8_encode ("Ä"), "Ae", $temp_return);
		$temp_return = str_replace(utf8_encode ("Ö"), "Oe", $temp_return);
		$temp_return = str_replace(utf8_encode ("Ü"), "Ue", $temp_return);

		$temp_return = str_replace(utf8_encode ("ä"), "ae", $temp_return);
		$temp_return = str_replace(utf8_encode ("ö"), "oe", $temp_return);
		$temp_return = str_replace(utf8_encode ("ü"), "ue", $temp_return);

		$temp_return = str_replace(utf8_encode ("ß"), "ss", $temp_return);

		return $temp_return;
	}
}

$register = new register_class();
