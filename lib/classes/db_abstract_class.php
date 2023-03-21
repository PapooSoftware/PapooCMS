<?php
/**
#####################################
# CMS Papoo                         #
# (c) Carsten Euwens 2009           #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version 5                     #
#####################################
*/

/**
 * Class db_abs
 *
 * Diese Klasse bietet eine DB ABstraktion
 * Normale Statements brauchen nicht mehr gebaut zu werden hiermit
 * Voraussetzung ist das die Variablen im Template gleich heissen wie
 * die DB Felder,
 */
#[AllowDynamicProperties]
class db_abs
{
	/**
	 * db_abs constructor.
	 */
	function __construct()
	{
		// Klassen globalisieren
		global $cms, $db, $content, $checked;

		// cms Klasse einbinden
		$this->cms = &$cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		$this->db = &$db;
		// Content Klasse
		$this->content = &$content;
		// Checked Klasse
		$this->checked = &$checked;

	}

	public function show_version()
	{
		$this->version = 2;
	}

	/**
	 * @param string $db
	 * @return array
	 */
	function get_colums_from_db($db = "")
	{
		if (!empty($this->cms->tbname[$db])) {
			$sql = sprintf("SHOW COLUMNS FROM %s",
				$this->cms->tbname[$db]
			);
			$colums = $this->db->get_results($sql, ARRAY_A);
		}

		if (empty($colums)) {
			$colums = array();
		}

		foreach ($colums as $col) {
			$cols[] = $col['Field'];
		}

		IfNotSetNull($cols);
		return $cols;
	}

	/**
	 * FUNCTION: validateEmail
	 *
	 * Validates an email address and return true or false.
	 *
	 * @param string $address email address
	 * @return bool true/false
	 * @access public
	 */
	function validateEmail($address)
	{
		return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9_-]+(\.[_a-z0-9-]+)+$/i', $address);
	}

	/**
	 * Holt die Vars aus dem checked Objekt die passend zur Tabelle sind.
	 *
	 * @param mixed $xpr
	 * @param array $must
	 * @param string $not
	 * @param string $db
	 * @param string $lang_name
	 * @return array
	 */
	function hole_passende_eintraege_aus_checked_raus($xpr, $must, $not = "xyzxyz", $db = "", $lang_name = "")
	{
		//INI
		$ret = array();
		$count = 0;
		$return_to_template = array();

		//Alle felder Rausholen die passen koennten
		$cols = $this->get_colums_from_db($db);

		//Anzahl der MUST Felder
		$must_count = is_array($must) || $must instanceof Countable ? count($must) : 0;

		if (is_array($cols)) {
			//Die checked Daten durchgehen
			foreach ($this->checked as $key => $value) {
				//sicherstellen dass es sich nur um korrekte Zeichen handelt
				$key = preg_replace("/[^a-zA-Z0-9_]_/", "", $key);
				//Wenn vorkommt in Returm Array uebergeben
				if (in_array($key, $cols)) {
					$ret[$key] = $value;

					//WEnn Eintrag enthalten ist, eintragen
					if (!empty($value)) {
						//WEnn der Eintrag auch im Must Array ist den Zaehler hochsetzen
						if (is_array($must)) {
							if (in_array($key, $must)) {
								$count++;
							}
						}
					}
					//Nochmal Daten ausgeben
					$return_to_template['0'][$key] = "nobr:" . $value;
				}
				else {
					// Wenn in der checked ein array ist. (Ist eigentlich nur für den Kalender da hier sonst fehler gespuckt werden)
					if(is_array($value)) {
						$this->content->template[$key] = "";
						foreach($value as $ar_key => $ar_value) {
							$this->content->template[$key] .= "nobr:" . $ar_value;
						}
					}
					//Nochmal Daten ausgeben
					else {
						$this->content->template[$key] = "nobr:" . $value;
					}
				}
			}
		}
		//DAten
		$this->content->template[$xpr] = $return_to_template;

		//Wenn alle drin
		if ($count == $must_count) {
			$this->alle_must_enthalten = 1;
		} //Nicht alle drin
		else {
			//Eintraege durchgehen
			if (is_array($must)) {
				foreach ($must as $var) {
					if (empty($this->checked->$var)) {
						if ($var != "extended_user_email") {
							$this->content->template['plugin_error'][$var] = $this->content->template['plugin_shop_fehlt_eintrag'];
						}
						else {
							if (!empty($this->checked->extended_user_email_false)) {
								$this->content->template['plugin_error'][$var] = $this->content->template['plugin_shop_fehlt_eintrag'];
								$this->content->template['plugin_error_email_false'][$var] = $this->content->template['plugin_shop_fehlt_eintrag'];
							}
							else {
								$this->content->template['plugin_error'][$var] = $this->content->template['plugin_shop_fehlt_eintrag_email_exist'];
							}
						}
					}
					else {
						if ($var == "extended_user_email") {
							if (!$this->validateEmail($this->checked->$var)) {
								$this->content->template['plugin_error'][$var] = $this->content->template['plugin_shop_fehlt_eintrag_email_falsch'];
							}
						}
					}
				}
			}
		}
		if (is_array($must)) {
			foreach ($must as $var) {
				if ($var == "extended_user_email") {
					if (!$this->validateEmail($this->checked->$var)) {
						$this->content->template['plugin_error'][$var] = $this->content->template['plugin_shop_fehlt_eintrag_email_falsch'];
						$this->alle_must_enthalten = "";
					}
				}
				//extended_user_benutzername_false
				if ($var == "extended_user_benutzername") {
					if (($this->checked->extended_user_benutzername_false)) {
						$this->content->template['plugin_error'][$var] = $this->content->template['plugin_shop_fehlt_eintrag_username_falsch'];
						$this->alle_must_enthalten = "";
					}
				}
			}
		}
		if (!is_array($must)) {
			$this->alle_must_enthalten = 1;
		}
		return $ret;
	}

	/**
	 * shop_class::insert_new_eintrag_in_shop_db()
	 * Neuen Eintrag in eine uebergeben Tabelle erstellen
	 *
	 * @param mixed $xsql
	 * @param int $show
	 * @return mixed|void
	 */
	function insert($xsql, $show = 0)
	{
		//Initialisierung
		$insert = "";

		//Neu setzen
		$this->alle_must_enthalten = 0;

		IfNotSetNull($xsql['praefix']);
		IfNotSetNull($xsql['must']);
		IfNotSetNull($xsql['not_praefix']);
		IfNotSetNull($xsql['dbname']);
		IfNotSetNull($xsql['lang_id']);

		//Die passenden Eintraege rausholen
		$post_vars =
			$this->hole_passende_eintraege_aus_checked_raus($xsql['praefix'], $xsql['must'], $xsql['not_praefix'], $xsql['dbname'], $xsql['lang_id']);

		//Die durchgehen und Statement erstellen
		foreach ($post_vars as $key => $value) {
			$insert .= $key . "='" . $this->db->escape($value) . "', ";
		}

		//Sprachid setzen
		if (!empty($xsql['lang_id'])) {
			$insert .= $xsql['praefix'] . "_lang_id='" . $xsql['lang_id'] . "', ";
		}

		$insert = substr($insert, 0, -2);

		//Statement erzeugen
		$sql = sprintf("INSERT INTO %s SET %s",
			$this->cms->tbname[$xsql['dbname']],
			$insert
		);
		$this->alle_must_enthalten;
		//Wenn was drin ist
		if ($this->alle_must_enthalten == 1) {
			//Insert durchfuehren
			$this->db->query($sql);

			//Wenn anzeigen
			if ($show == 1) {
				echo $sql;
			}

			//Id uebergeben
			$return['insert_id'] = $this->db->insert_id;


		}
		else {
			//Wenn anzeigen
			if ($show == 1) {
				echo "NICHT ALLE ENTHALTEN";
			}

			//Werte wieder zurueckgeben
			$this->werte_nochmal_zurueckgeben();
		}
		IfNotSetNull($return);
		return $return;
	}

	/**
	 * shop_class::insert_new_eintrag_in_shop_db()
	 * Neuen Eintrag in eine uebergeben Tabell erstellen
	 * @param mixed $xsql
	 * @param string $show
	 * @return mixed
	 */
	function delete($xsql, $show = "")
	{
		//Statement erzeugen
		$sql = sprintf("DELETE FROM %s WHERE %s %s",
			$this->cms->tbname[$xsql['dbname']],
			$xsql['del_where_wert'],
			$xsql['limit']
		);
		if ($show == 1) {
			echo $sql;
		}
		//Loeschen durchfuehren
		return $this->db->query($sql);
	}

	/**
	 * shop_class::insert_new_eintrag_in_shop_db()
	 * Neuen Eintrag in eine uebergeben Tabell erstellen
	 * @param mixed $xsql
	 * @param int $show
	 * @return mixed
	 */
	function update($xsql, $show = 0)
	{
		//Initialisierung
		$insert = "";

		if (defined("admin")) {
			unset($_SESSION['shop_settings']);
		}

		//Neu setzen
		$this->alle_must_enthalten = 0;
		//Die passenden Eintraege rausholen

		// Unterdrückt da hier schlimme Sachen sonst passieren. (IfNotSetNull() lässt komische Sachen passieren)
		$post_vars = $this->hole_passende_eintraege_aus_checked_raus(
			@$xsql['praefix'],
			@$xsql['must'],
			@$xsql['not_praefix'],
			@$xsql['dbname'],
			@$xsql['where_lang_name']
		);

		//Die durchgehen und Statement erstellen
		foreach ($post_vars as $key => $value) {
			$insert .= $key . "='" . $this->db->escape($value) . "', ";
		}

		if (!empty($xsql['where_lang_name'])) {
			//Sprachid setzen
			$insert .= $xsql['praefix'] . "_lang_id='" . $this->cms->lang_back_content_id . "'";

			//Statement erzeugen
			$sql = sprintf("UPDATE %s SET %s WHERE %s='%d' AND %s='%s' LIMIT 1",
				$this->cms->tbname[$xsql['dbname']],
				$insert,
				$xsql['where_name'],
				$this->db->escape($this->checked->{$xsql['where_name']}),
				$xsql['where_lang_name'],
				$this->db->escape($this->cms->lang_back_content_id)
			);
		}
		else {
			$insert = substr($insert, 0, -2);
			//Statement erzeugen
			$sql = sprintf("UPDATE %s SET %s WHERE %s='%s' LIMIT 1",
				$this->cms->tbname[$xsql['dbname']],
				$insert,
				$xsql['where_name'],
				$this->db->escape($this->checked->{$xsql['where_name']})
			);
		}

		//Wenn was drin ist
		if ($this->alle_must_enthalten == 1) {
			//Wenn anzeigen
			if ($show == 1) {
				echo $sql;
			}

			//Insert durchfuehren
			$this->db->query($sql);
			//Id uebergeben
			$return['insert_id'] = $this->checked->{$xsql['where_name']};


		}
		else {
			//Werte wieder zurueckgeben
			$this->werte_nochmal_zurueckgeben();
		}
		IfNotSetNull($return);
		return $return;
	}

	/**
	 * @param $xsql
	 * @param int $show
	 * @return array|null|string
	 *
	 * Select auf eine DB Tabelle ausführen
	 */
	function select($xsql, $show = 0)
	{
		//Initialisierung
		$select = "";
		$select_felder = "";

		if (defined("admin")) {
			unset($_SESSION['shop_settings']);
		}

		//Neu setzen
		#$this->alle_must_enthalten = 0;
		//Die passenden Eintraege rausholen
		#$post_vars = $this->hole_passende_eintraege_aus_checked_raus($xsql['praefix'], $xsql['must'],$xsql['not_praefix'],$xsql['dbname']);

		if (!is_array($xsql['where_data'])) {
			return "where_data ist KEIN ARRAY";
		}

		if (!is_array($xsql['select_felder'])) {
			return "select_felder ist KEIN ARRAY";
		}

		if (empty($xsql['where_data_like'])) {
			$compare = " = ";
		}
		else {
			$compare = " LIKE ";
		}

		//Die durchgehen und Statement erstellen
		foreach ($xsql['where_data'] as $key => $value) {
			$select .= $key . $compare . "'" . $this->db->escape($value) . "' AND ";
		}

		if (!isset($xsql['where_not_data'])) {
			$xsql['where_not_data'] = NULL;
		}

		if (!isset($xsql['group_by'])) {
			$xsql['group_by'] = NULL;
		}

		if (!isset($xsql['order_by'])) {
			$xsql['order_by'] = NULL;
		}

		if (!isset($xsql['order_by_asc_desc'])) {
			$xsql['order_by_asc_desc'] = null;
		}

		if (is_array($xsql['where_not_data'])) {
			foreach ($xsql['where_not_data'] as $key => $value) {
				$select .= $key . $compare . " '" . $this->db->escape($value) . "' AND ";
			}
		}

		//Letztes AND raus
		$select = substr($select, 0, -4);

		//Die Felder durchgehen und Statement erstellen
		foreach ($xsql['select_felder'] as $key => $value) {
			$select_felder .= $value . ", ";
		}

		//Letztes Komma raus...
		$select_felder = substr($select_felder, 0, -2);

		if (!empty($xsql['group_by'])) {
			$xsql['group_by'] = " GROUP BY " . $xsql['group_by'];
		}

		if (!empty($xsql['order_by'])) {
			$xsql['order_by'] = " ORDER BY " . $xsql['order_by'];
		}

		if (!empty($xsql['order_by_asc_desc'])) {
			$xsql['order_by_asc_desc'] = " " . $xsql['order_by_asc_desc'];
		}

		IfNotSetNull($xsql['limit']);

		$sql = sprintf("SELECT %s FROM %s WHERE %s %s %s %s %s",
			$this->db->escape($select_felder),
			$this->cms->tbname[$xsql['dbname']],
			$select,
			$this->db->escape($xsql['group_by']),
			$this->db->escape($xsql['order_by']),
			$this->db->escape($xsql['order_by_asc_desc']),
			$this->db->escape($xsql['limit'])
		);

		if ($show == 1) {
			debug::print_d($sql);
		}

		return $this->db->get_results($sql, ARRAY_A);
	}


	/**
	 * shop_class::werte_nochmal_zurueckgeben()
	 * Gibt alle Werte nochmal ans Template zurueck
	 * @return void
	 */
	function werte_nochmal_zurueckgeben()
	{
		//Nachricht uebergeben
		$this->content->template['is_eingetragen'] = "no";
	}

}

// Hiermit wird die Klasse initialisiert und kann damit sofort ueberall benutzt werden.
$db_abs = new db_abs();
