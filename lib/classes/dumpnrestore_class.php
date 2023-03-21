<?php
// #####################################
// # CMS Papoo                         #
// # (c) Carsten Euwens 2003           #
// # Authors: Stephan Bergmann         #
// #          aka b.legt               #
// # http://www.papoo.de               #
// # Internet                          #
// #####################################
// # PHP Version 4.2                   #
// #####################################

/**
 * Erstellung und Rückspielung von SQL-Daten
 *
 * Class dumpnrestore_class
 */
#[AllowDynamicProperties]
class dumpnrestore_class
{
	/**
	 * dumpnrestore_class constructor.
	 */
	function __construct()
	{
		// Klassen-Variablen
		/** @var string eofline_identifier Trennzeichen zur SQL-Zeilenende-Erkennung für split */
		$this->eofline_identifier = "; ##b_dump##";

		/** @var string dump_filename Default-Verzeichnis der Sicherung */
		$this->dump_filename = "templates_c/dumpnrestore.sql";

		/** @var array querys enthält die einzelnen SQL-Anweisungen */
		$this->querys = array();

		/** @var string global_db_praefix globales Präfix für Tabellen-Namen als einheitliche Grundlage für Ersetzung durch lokales Tabellen-Präfix */
		$this->global_db_praefix = "XXX_";

		global $db_praefix;
		/** @var string lokal_db_praefix lokales Präfix der Tabellen-Namen */
		$this->lokal_db_praefix = $db_praefix;

		global $db;
		/** @var object db Einbindung globaler papoo-Datenbank-Klasse (ezSQL) */
		$this->db = & $db;
	}

	/**
	 * Erstellt Dump-SQL-Anweisungen der Tabelle "$tablename" und gibt diese Anweisungen in einem Array per return zurück
	 *
	 * Funktion: Es wird immer eine komplette SQL-Anweisung erstellt und diese dann an in das Array $the_return gepusht.
	 * Anweisungen die sich über mehrere Zeilen erstrecken werden zuerst in die Variable $temp geschrieben.
	 *
	 * TODO check ob Feld- bzw. Index-Parameter bei versch. DB-Systemen den gleichen Namen haben (für if(Feldtyp) in 2. und switch(Key_name) in 3.)
	 *
	 * @param $tablename
	 * @param int $lim
	 * @param int $max
	 * @param int $counttab
	 * @return array|string
	 */
	function dump_load($tablename, $lim = 0, $max = 10, $counttab = 0)
	{
		$dump_tablename = str_replace("xxx" . $this->lokal_db_praefix, $this->global_db_praefix, "xxx" . $tablename);

		$the_return = array();
		$temp = "";
		if (!ini_get("safe_mode")) {
			set_time_limit(300);
		}
		// ini_set(memory_limit, "40M"); // Bei Speicher-Problemen evtl. aktivieren
		// Tabellen Struktur erstellen
		// **************************
		if (empty($this->export)) {
			// 1. Kopf schreiben
			$the_return[] = "DROP TABLE IF EXISTS `" . $dump_tablename . "`" . $this->eofline_identifier . "\n";
			$query = "SHOW CREATE TABLE " . $tablename;
			$indizes = $this->db->get_results($query, ARRAY_A);
			$temp .= $indizes[0]['Create Table'];
			$temp = str_replace("CREATE TABLE `" . $this->lokal_db_praefix, "CREATE TABLE `" . $this->global_db_praefix, $temp);
			$the_return[] = $temp . " ; ##b_dump##\n";
		} // End of if (empty($this->export))

		// Tabellen-Inhalt erstellen
		// *************************
		if ((empty($this->donewdump))) {
			$dump_file = fopen($this->dump_filename, "a");
		}
		IfNotSetNull($dump_file);

		// Tabellen vorher leeren
		if (!empty($this->export) and empty($this->multi) and ($lim == 0)) {
			$queryx = $the_return[] = "TRUNCATE TABLE `" . $dump_tablename . "`" . $this->eofline_identifier . "\n";
			// $queryx = $the_return[] ="DELETE FROM `".$dump_tablename."`".$this->eofline_identifier."\n";
		}
		if ((empty($this->donewdump))) {
			fwrite($dump_file, $queryx);
		}

		if (!empty($max)) {
			$query = sprintf("SELECT * FROM %s LIMIT %s,%s", $tablename, $lim, $max);
			$query2 = sprintf("SELECT COUNT(*) FROM %s ", $tablename);
		}
		else {
			$query = sprintf("SELECT * FROM %s", $tablename);
			$query2 = "";
		}
		$result = $this->db->get_results($query);
		$result_count = $this->db->get_var($query2);

		if ($result) {
			foreach ($result as $eintraege) {
				if (!empty($this->multi)) {
					$temp = "DELETE FROM `" . $dump_tablename . "` WHERE ";
					$xi = 0;
					foreach($eintraege as $feldname => $feldwert) {
						if ($xi < 2) {
							$temp .= $feldname . "='" . $this->db->escape(($feldwert)) . "' AND ";
						}
						$xi++;
					}
					$temp[strlen($temp) - 2] = " ";
					$temp[strlen($temp) - 3] = " ";
					$temp[strlen($temp) - 4] = " ";

					$queryx = $temp . $this->eofline_identifier . "\n";
					if ($this->doupdateok == "ok") {
						$the_return[] = $queryx;
					}
					if ((empty($this->donewdump))) {
						fwrite($dump_file, $queryx);
					}
				}

				$temp = "INSERT INTO `" . $dump_tablename . "` SET ";
				foreach($eintraege as $feldname => $feldwert) {
					$temp .= '`' . $this->db->escape($feldname) . "`='" . $this->db->escape(($feldwert)) . "', ";
				}
				$temp[strlen($temp) - 2] = " ";

				$queryx = $temp . $this->eofline_identifier . "\n";
				if ($this->doupdateok == "ok") {
					$the_return[] = $queryx;
				}
				if ((empty($this->donewdump))) {
					fwrite($dump_file, $queryx);
				}
			}
		}

		// $the_return[] = "\n\n";
		if ((empty($this->donewdump)))
		{
			fclose($dump_file);
		}
		$limmax = $lim + $max;
		if ($result_count > $limmax && $this->doupdateok != "ok") {
			// Neu laden
			// interna/stamm.php?menuid=53&makedump=1
			$limmax = $lim + $max;
			echo "Erstellung l&auml;uft";
			// $location_url = "./stamm.php?menuid=53&makedump=1&lim=".$limmax."&tab=".$counttab;
			echo $this->refresh = '<meta http-equiv="refresh" content="0; url=./stamm.php?menuid=53&makedump=1&lim=' . $limmax . '&counttab=' . $counttab . '" />';
			exit();
		}

		if ($this->doupdateok == "ok") {
			return $the_return;
		}
		else {
			return $this->dump_filename;
		}
	}

	/**
	 * Schreibt Dump-SQL-Anweisungen aus dem Array "$this->querys" in Datei "$this->dump_filename"
	 *
	 * @param string $modus  ["", "append"] ist $modus="append" werden die Daten am Ende angefügt, sonst wird die Datei gelöscht und neu gefüllt
	 */
	function dump_save($modus = "")
	{
		if ($modus == "append") {
			$dump_file = fopen($this->dump_filename, "a");
		}
		else {
			$dump_file = fopen($this->dump_filename, "w");
		}

		if ($dump_file) {
			if (!empty($this->querys)) {
				foreach ($this->querys as $query) fwrite($dump_file, $query);
			}
			fclose($dump_file);
		}
		else {
			echo 'Fehler beim &ouml;ffnen/erstellen der Datei ' . $this->dump_filename;
		}
	}

	/**
	 * Schreibt Dump-SQL-Anweisungen aus dem Array "$this->querys" in einen String und gibt diesen String zurück
	 *
	 * @return string
	 */
	function dump_text()
	{
		$text = "";
		if (!empty($this->querys)) {
			foreach ($this->querys as $query) {
				$text .= $query . "\r";
			}
		}
		return $text;
	}

	// FUNKTIONEN ZUM ZURÜCKSPIELEN

	/**
	 * Läd die SQL-Datei "$filename" und führt sie in der papoo-DB aus
	 *
	 * @param string $filename
	 */
	function restore($filename = "")
	{
		if ($filename) {
			$this->restore_load($filename);
			$this->restore_save();
		}
	}

	/**
	 * SQL-Anweisungen aus Datei "$filename" in Array "$this->querys" laden
	 *
	 * @param string $filename
	 */
	function restore_load($filename = "")
	{
		if (is_file($filename)) {
			if (!ini_get("safe_mode")) @set_time_limit(300);
			$file_handle = fopen($filename, "r");
			if ($file_handle) {
				if (filesize($filename)) {
					$inhalt = fread($file_handle, filesize ($filename));
					if (!$inhalt) {
						echo "dumpnrestore.restoreload: Datei &quot;" . $filename . "&quot; ist leer";
					}
					else {
						$this->querys = explode($this->eofline_identifier, $inhalt);
					}
				}
			}
			else {
				echo "dumpnrestore.restoreload: Fehler beim öffnen der Datei &quot;" . $filename . "&quot;.";
			}
		}
	}

	/**
	 * SQL-Anweisungen aus Array "$this->querys" in papoo-DB ausführen
	 *
	 * @return int
	 */
	function restore_save()
	{
		$error_count = 0;

		$this->db_nicht_tabellen = array("papoo_menuint",
			"papoo_men_uint_language",
			"papoo_lookup_men_int", // Nur für Tests ausgeschlossen. Bedarf Sonderbehandlung !!!!
			"papoo_plugins",
			"papoo_pluginclasses",
			"papoo_plugin_language",
			"papoo_plugin_dump_sicherung",
			"papoo_name_language",
			"papoo_module",
			"papoo_module_language"
		);
		//Fehler verstecken
		$this->db->hide_errors();

		if ($this->querys) {
			foreach ($this->querys as $row) {
				$row = trim($row);
				if (!empty($row)) {
					$query = str_ireplace("`" . $this->global_db_praefix, "`" . $this->lokal_db_praefix, $row);
					if (empty($_SESSION['updateit'])) {
						$_SESSION['updateit']="";
					}
					if ($_SESSION['updateit']==1) {
						foreach ($this->db_nicht_tabellen as $tab) {
							if (stristr($row,$tab)) {
								continue;
							}
						}
					}
					$this->db->query(($query));
				}
			}
		}

		//Fehler wieder anzeigen
		$this->db->show_errors();
		return $error_count;
	}

	// DIVERSE HILFS-FUNKTIONEN

	/**
	 * @return array|bool
	 */
	function make_tablelist()
	{
		$table_list = array();
		global $db_name;
		//$query = "SHOW TABLES";
		$query = 'SELECT table_name "Table Name", table_rows "Rows Count",
							round(((data_length + index_length)/1024/1024),2)
							"Table Size (MB)" FROM information_schema.TABLES WHERE table_schema = "'.$db_name.'";';
		$result = $this->db->get_results($query,ARRAY_A);
		//debug::print_d($result);
		$x = 0;

		if ($result) {
			foreach($result as $eintrag) {
				if ($eintrag) {
					foreach($eintrag as $titel => $tabellenname) {
						// "xxx" wird benötigt, da praefix an Stelle 0 beginnt und strpos damit false wäre
						if (strpos("xxx" . $tabellenname, $this->lokal_db_praefix) == 3) {
							$table_list[$x]['tablename'] = $tabellenname;
							$table_list[$x]['rows'] = $eintrag['Rows Count'];
							$table_list[$x]['size'] = $eintrag['Table Size (MB)'];
							$x++;
						}
					}
				}
			}
		}
		if (!empty($table_list)) {
			return $table_list;
		}
		else {
			return false;
		}
	}
}

$dumpnrestore = new dumpnrestore_class();