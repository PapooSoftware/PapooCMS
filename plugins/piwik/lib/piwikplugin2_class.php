<?php

/**
 * Class piwikplugin2_class
 */
#[AllowDynamicProperties]
class piwikplugin2_class
{
	/**
	 * piwikplugin2_class constructor.
	 */
	function __construct()
	{
		global $content, $checked, $db, $user;
		$this->content = & $content;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->user = & $user;

		// Aufruf der Aktionsweiche
		$this->make_piwik();
	}

	function make_piwik()
	{
		if (defined("admin")) {
			$this->user->check_intern();

			global $template;

			// �berpr�ft, ob in der aktuellen URL der String "piwik_back.html" vorkommt
			if (strpos("XXX".$template, "piwik_back.html")) {
				// �berpr�ft, ob das Textareafeld "piwik_piwikwert" nicht leer istr
				if (isset($this->checked->piwik_piwikwert) && $this->checked->piwik_piwikwert) {
					$this->write_into_db();
				}
				$this->read_from_db();
			}
		}
	}

	/**
	 * Diese Funktion schreibt ein Text in das Feld "piwik_tracking_code"
	 * der Tabelle "XXX_plugin_piwik", wobei XXX_ das lokale Tabellen-Pr�fix ist.
	 */
	function write_into_db()
	{
		global $db_praefix;

		$temp_piwik_piwikwert = $_POST['piwik_piwikwert'];
		if (get_magic_quotes_gpc()) {
			$temp_piwik_piwikwert = stripslashes($temp_piwik_piwikwert);
		}

		// SQL-Anweisung zusammenstellen
		$query = sprintf("UPDATE %s SET piwik_tracking_code='%s' WHERE piwik_id=1",
			$db_praefix."plugin_piwik",
			$this->db->escape($temp_piwik_piwikwert)
		);
		// SQL-Anweisung ausf�hren
		$this->db->query($query);
	}

	/**
	 * Diese Funktion liest der Wert "piwik_tracking_code" aus der Tabelle "XXX_plugin_piwik"
	 * und �bergibt ihn an die Template-Variable "piwik_piwikwert"
	 *
	 * @return mixed
	 */
	function read_from_db()
	{
		global $db_praefix;

		// SQL-Anweisung zusammenstellen
		$query = sprintf("SELECT piwik_tracking_code FROM %s WHERE piwik_id=1",
			$db_praefix."plugin_piwik"
		);

		// Resultat der SQ-Anweisung in die Eigenschaft "$result" schreiben
		$result = $this->db->get_results($query);

		// Resultat in die Template Variable piwik.piwikwert schreiben
		$this->content->template['plugin']['piwik']['piwikwert'] = "nobr:".$result[0]->piwik_tracking_code;

		// Resultat ausgeben
		return $result[0]->piwik_tracking_code;
	}

	/**
	 * Diese Funktion gibt das Resultat der Funktion read_from_db() in den Quelltext der Seite aus
	 * Die Funktion output_filter()	wird von von Papoo grunds�tzlich aufgerufen.
	 */
	function output_filter()
	{
		global $output;
		$output = str_replace("</body>",$this->read_from_db()."</body>",$output);
	}
}

$piwikplugin2 = new piwikplugin2_class();
