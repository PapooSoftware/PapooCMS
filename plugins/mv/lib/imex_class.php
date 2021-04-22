<?php
/**
#####################################
# Papoo CMS                   		#
# (c) Carsten Euwens 2007   		#
# Authors: Dr. Carsten Euwens       #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.3                  #
#####################################
*/

require_once(__DIR__.'/Encoding.php');

class imex_mw_class
{
	function __construct()
	{
		global $user;
		$this->user = & $user;
		global $content;
		$this->content = & $content;
		global $cms;
		$this->cms = & $cms;
		global $db;
		$this->db = & $db;
		global $checked;
		$this->checked = & $checked;
		global $diverse;
		$this->diverse = & $diverse;
		global $mv;
		$this->mv = & $mv;
		global $weiter;
		$this->weiter = & $weiter;
		global $template;
		$this->do_admin();
	}

	/**
	 * Admin Weiche
	 */
	function do_admin()
	{
		global $template;
		// Wenn Admin
		if (defined("admin"))
		{
			$this->user->check_intern();

			require_once(PAPOO_ABS_PFAD . "/plugins/mv/lib/mv_conf.php");
			$mv_config = new mv_config;
			foreach ($mv_config->cfg AS $key => $value) {$this->$key = $value;}
			// Daten sollen exportiert werden BE
			if (strpos("XXX" . $template, "mv/templates/imex_exportit.html")) $this->do_export();
			// Daten sollen exportiert werden FE
			if (strpos("XXX" . $template, "mv/templates/mv_imex_exportit_fe.html")) $this->do_export();
			// Daten sollen importiert werden
			if (strpos("XXX" . $template, "mv/templates/imex_importit.html")) $this->do_import();
			// Importprotokoll anzeigen
			if (strpos("XXX" . $template, "mv/templates/mv_import_protokoll.html")) $this->print_protokoll();
			// Import Fehlerprotokoll �bersicht anzeigen
			if (strpos("XXX" . $template, "mv/templates/imex_error_report.html")) $this->imex_error_report();	
			// Import Fehlerprotokoll Details anzeigen
			if (strpos("XXX" . $template, "mv/templates/imex_error_report_details.html")) $this->imex_error_report_details();	
		}
	}
	/**
	 * Daten exportieren
	 */
	function do_export()
	{
		// Vorhandene Felder zur�ckgeben
		$this->content->template['tabelle'] = $this->checked->tabelle;
		$this->content->template['format'] = $this->checked->format;
		$this->content->template['feld'] = $this->checked->feld;
		$this->content->template['tabelle_lang'] = $this->checked->tabelle_lang;
		$this->content->template['trenner'] = $this->checked->trenner;
		$this->content->template['mv_sql'] = $this->checked->mv_sql;
		$this->content->template['mv_such_treffer'] = $this->checked->anzahl;
		$this->content->template['meta_fehlt'] = $this->checked->meta_fehlt;
		if (!empty($this->checked->mv_id)
			&& !empty($this->checked->mv_meta_id))
		{
			// Den Namen der Metaebene aus der Tabelle holen
			$sql = sprintf("SELECT mv_meta_group_name
									FROM %s 
									WHERE mv_meta_id = '%d'
									LIMIT 1",
									
									$this->cms->tbname['papoo_mv']
									. "_meta_"
									. $this->db->escape($this->checked->mv_id),
									
									$this->db->escape($this->checked->mv_meta_id)
							);
			$mv_meta_group_name = $this->db->get_var($sql);		
		}
		$this->content->template['mv_meta_group_name'] = $mv_meta_group_name;
		$this->content->template['mv_meta_id'] = $this->checked->mv_meta_id;		
		// nur die Tabellen von Flex-Verwaltungen anzeigen
		$this->get_mv_tabs();
		// welche Sprachen gibt es?
		$this->get_sprachtabellen();
		if (!empty($this->checked->delete)) $this->content->template['delete'] = "ok"; //Gel�scht ausgeben
		// Datei l�schen
		if (!empty($this->checked->delfile))
		{
			//Filtern
			$file = basename($this->checked->file);
			$file = str_replace("/", "", $file);
			$file = str_replace("\\", "", $file);
			//Pfad erzeugen
			$file = "/interna/templates_c/" . $file;
			//Datei l�schen
			$this->diverse->delete_file($file);
			$location_url = $_SERVER['PHP_SELF']
							. "?menuid= "
							. $this->checked->menuid
							. "&template= "
							. $this->checked->template
							. "&delete=ok";
			if ($_SESSION['debug_stopallredirect'])
				echo '<a href= "'
				. $location_url
				. '">Weiter</a>';
			else header( "Location: $location_url" );
			exit;
		}
		$fehler = 0;
		if (!empty($this->checked->startexport))
		{
			if (empty($this->checked->tabelle_lang) 
				and empty($this->checked->tabelle)) $fehler = 4;
			else
			{
				if (empty($this->checked->tabelle)) $fehler = 1;
				if (empty($this->checked->tabelle_lang)) $fehler = 2;
			}
		}
		if (!empty($this->checked->metachoose)
			AND empty($this->checked->metaebene))
		{
			$this->get_metagruppen();
			$fehler = 3;
		}
		if (!$fehler)
		{
			// Metaebene ausw�hlen (startexport = submit Button 1. Seite)
			if (!empty($this->checked->startexport)
				&& empty($this->checked->mv_sql))
			{
				// welche Metagruppen gibt es?
				$this->get_metagruppen();
				$this->content->template['meta_fehlt'] = "ja";			
			}
			// Wenn ausgeben gew�hlt, durchlaufen
			if (!empty($this->checked->metachoose)
				&& !empty($this->checked->metaebene))
			{
				// Daten holen
				#$time_start = microtime(true);
				$daten = $this->get_data($this->checked->tabelle
											. "_search_"
											. $this->checked->tabelle_lang,
											$this->checked->format
										);
				// Evtl. vorhandene HT in den Feldern raus, bringt Excel & Co. sonst durcheinander
				if (count($daten))
				{
					foreach ($daten AS $key => $value)
					{
						foreach ($value AS $key2 => $value2)
						{
							$daten[$key][$key2] = str_replace("\t", "", $value2);
						}
					}
				}
				#$time_end = microtime(true);
				#$totaltime = $time_end - $time_start;
				#echo "1. Die Abfrage dauerte " . $totaltime . " Sekunden<br />";
				// Daten erzeugen
				$datenfertig = $this->export_data($daten,
													$this->checked->tabelle
													. "_search_"
													. $this->checked->tabelle_lang,
													$this->checked->format,
													$this->checked->feld
												);
				// Ausgeben als Link
				if (!empty($datenfertig)
					and ($this->checked->format == "csv" or $this->checked->format == "xml"))
				{
					// Zeitstempel
					$time = time();
					// Dateinamen
					$file = "/interna/templates_c/export_"
							. basename($this->checked->tabelle)
							. $time
							. "."
							. $this->checked->format;
					// Datei erzeugen
					$this->diverse->write_to_file($file, $datenfertig);
					// Pfad ans Template
					$this->content->template['file'] = $file;
					// Link
					$this->content->template['self'] = $_SERVER['PHP_SELF']
														. "?menuid= "
														. $this->checked->menuid
														. "&template= "
														. $this->checked->template
														. "";
					//Pfad zur Datei
					$this->content->template['pfad'] = PAPOO_WEB_PFAD;
					header("content-type: application/csv; charset=utf-8");
					header("Content-Disposition: attachment; filename=\"export.csv\"");
					// Send Content-Transfer-Encoding HTTP header
					// (use binary to prevent files from being encoded/messed up during transfer)
					header('Content-Transfer-Encoding: binary');
					$datenfertig = \ForceUTF8\Encoding::toUTF8($datenfertig);
					echo $datenfertig;
					exit;
				}
				elseif (empty($datenfertig)) $this->content->template['kein_inhalt'] = 1;
			}
		}
		else $this->content->template['fehler'] = $fehler;
	}
	
	/**
	 * Holt die Metaebenen f�r die ausgew�hle Verwaltung aus der Datenbank
	 */	 	
	function get_metagruppen()
	{
		// holt die mv_id aus dem Tabellennamen
		$split = explode("_", $this->checked->tabelle);
		$mv_id = end($split);
		// holt die Metaebene aus der Datenbank
		if (is_numeric($mv_id))
		{
			$sql = sprintf("SELECT * FROM %s",
										$this->cms->tbname['papoo_mv']
										. "_meta_"
										. $this->db->escape($mv_id)
							);
			$metaebenen = $this->db->get_results($sql, ARRAY_A);
		}
		$this->content->template['metaebenen'] = $metaebenen;
	}

	/**
	 * L�scht den Inhalt bei Feldern, die in dieser Metaebene inaktiv sind
	 */	 	
	function delete_felder_meta($daten)
	{
		// holt die mv_id aus dem Tabellennamen
		$split = explode("_", $this->checked->tabelle);
		$mv_id = end($split);
		// holt alle nicht aktiven Felder f�r die ausgew�hlte Metaeben und mv_id aus der Datenbank
		$sql = sprintf("SELECT mvcform_name,
								mvcform_id
								FROM %s 
								WHERE mvcform_meta_id = '%d' 
								AND mvcform_aktiv = '1' 
								AND mvcform_form_id = '%d'",
								
								$this->cms->tbname['papoo_mvcform'],
								
								$this->db->escape($this->checked->metaebene),
								$this->db->escape($mv_id)
						);
		$meta_felder = $this->db->get_results($sql, ARRAY_A);
		if (!empty($daten))
		{
			$array_counter = 0;
			foreach($daten as $zeile)
			{
				if (!empty($meta_felder))
				{
					foreach($meta_felder as $feld)
					{
						$daten[$array_counter][$feld['mvcform_name'] . "_" . $feld['mvcform_id']] = "";
					}
				}
				$array_counter++;
			}
		}
		return($daten);
	}

	/**
	 * L�scht die Mitglieder wieder raus, die nicht zu den Rechtegruppen dieser Metagruppe geh�ren
	 */
	function delete_nicht_meta_eintrag($daten)
	{
		// holt die mv_id aus dem Tabellennamen
		$split = explode("_", $this->checked->tabelle);
		$mv_id = end($split);
		if (!empty($daten))
		{
			$array_counter = 0;
			foreach($daten as $zeile)
			{
				// gibt es in der Main Metaebene Lookup Tabelle einen Eintrag?
				$sql = sprintf("SELECT mv_meta_lp_user_id
										FROM %s 
										WHERE mv_meta_lp_user_id = '%d' 
										AND mv_meta_lp_meta_id = '%d'
										AND mv_meta_lp_mv_id = '%d'
										LIMIT 1",
										
										$this->cms->tbname['papoo_mv_meta_lp'],
										
										$this->db->escape($daten[$array_counter]['mv_content_id']),
										$this->db->escape($this->checked->metaebene),
                						$this->db->escape($mv_id)
								);
				$treffer = $this->db->get_var($sql);
				$array_counter++;
			}	
    	}
    	return($daten);	
	}	 	
	
	/**
	 * Daten aus einer gew�hlten Tabelle rausholen
	 */
	function get_data($tab, $format = "")
	{
		// wenn es ein Export aus der Suche heraus sein soll
		if (!empty($this->checked->mv_sql)) $result = $this->db->get_results($_SESSION['mv_import']['mv_sql_buffer'], ARRAY_A);
		// ansonsten relevante Daten holen unter Ber�cksichtigung der mv_id, meta_id und aller Felder hierzu
		else
		{
			global $db_praefix;
			// echten Tabellen Namen erstellen
			$tab = $db_praefix . $tab;
			$mv_id = @end(@explode("_", $this->checked->tabelle));
			// Reihenfolge der Felder bei der Ausgabe wie bei der Felderstellung nach Gruppen und OrderIds
			require_once(PAPOO_ABS_PFAD . "/plugins/mv/lib/mv.php");
			$mv = new mv;
			$this->checked->mv_id = $mv_id;
			$this->mv->meta_gruppe = $this->checked->metaebene;
			$this->mv->get_form_group_field_list();
			$select_fields = [
				'mv_content_id',
				'mv_content_owner',
				'mv_content_userid',
				'mv_content_search',
				'mv_content_sperre',
				'mv_content_teaser',
				'mv_content_create_date',
				'mv_content_edit_date',
				'mv_content_create_owner',
				'mv_content_edit_user',
			];
			if (count($this->content->template['gfliste']))
			{
				foreach ($this->content->template['gfliste'] AS $key => $value)
				{
					foreach ($value AS $key2 => $value2)
					{
						// Nur Gruppen, die auch Felder haben, sonst gibts hier �rger
						if ($key2 == "felder"
							AND count($value2))
						{
							foreach ($value2 AS $key3 => $value3)
							{
								// nur Daten f�r aktive Felder ausgeben
								if ($value3['mvcform_aktiv']) {
									$select_fields[] = "{$value3['mvcform_name']}_{$value3['mvcform_lang_id']}";
								}
							}
						}
					}
				}
			}
			$sql = sprintf("SELECT DISTINCT %s
										FROM %s, %s 
										WHERE mv_content_id = mv_meta_lp_user_id
										AND mv_meta_lp_meta_id = '%d' 
										AND mv_meta_lp_mv_id = '%d'
										ORDER BY mv_content_id",
										implode(', ', $select_fields),
										
										$tab,
										
										$this->cms->tbname['papoo_mv_meta_lp'],
										
										$this->db->escape($this->checked->metaebene),
										$mv_id
								);
			$result = $this->db->get_results($sql, ARRAY_A);
			if (count($result))
			{
				// die folgenden Systemfelder nicht exportieren. Diese Felder aus dem Array daher entfernen, w�rden sonst auch beim Import unn�tz angezeigt werden.
				foreach ($result AS $key => $value)
				{
					foreach ($value AS $key2 => $value2)
					{
						if ($key2 == "mv_content_search"
							OR $key2 == "mv_meta_lp_user_id"
							OR $key2 == "mv_meta_lp_meta_id"
							OR $key2 == "mv_meta_lp_mv_id") unset($result[$key][$key2]);
					}
				}
			}
		}
		// Zeilenumbr�che etc. entfernen, select/radio/check/multiselect in Klartext konvertieren
		if ($format != "xml")
		{
			if (!empty($result))
			{
				// array mit den zu konvertierenden Texten aufbauen
				// Im Array sind nur die relevanten Daten zu dieser mv_id, meta_id und f�r select/radio/check/multiselect/pre_select
				$array_convert_to_text = $this->make_array_convert_to_text();
				$i = 0;
				// Alle Eintr�ge durchgehen
				foreach ($result as $dat) // Satzebene
				{
					foreach ($dat as $key => $value) // Feldebene
					{
						$value3 = "";
						// $key enth�lt Feldname, $value Inhalt
						// Wenn das Feld im Array ist, dann den zu konvertierenden Text ausgeben
						if ($array_convert_to_text
							AND array_key_exists($key, $array_convert_to_text))
						{
							// Leere Feldinhalte nicht konvertieren (das g�be auch einen PHP-Error im foreach)
							if ($array_convert_to_text[$key] != "")
							{
								// Suche nach der passenden Lookup-ID, dann konvertieren
								foreach ($array_convert_to_text[$key] AS $key2)
								{
									foreach ($key2 AS $key3 =>$valuek)
									{
										if (substr($key3, 0, 13) == "mvcform_type=")
										{
											$mvcform_type = substr($key3, 13);
											break;
										}
									}
									// Bei multiselect mehrere Werte konvertieren und mit Komma trennen
									if ($mvcform_type == "multiselect"
										AND strpos($value, "\n")) // auf Pos 0 darf nix sein! Sonst fliegt der hier raus
									{
										// x0A = LF in Komma umwandeln und letztes Komma wieder raus
										$value2 = substr(str_replace("\n", ",", $value), 0, strlen($value) - 1);
										$val_array = explode(",", $value2);
										foreach ($val_array AS $element => $element_value)
										{
											if ($key2['content']
												AND $key2['lookup_id'] == $element_value) $value3 .= $key2['content'] . ",";
										}
									}
									elseif ($key2['lookup_id'] == $value) $value = $key2['content'];
								}
								if (!empty($value3)) $value = substr($value3, 0, strlen($value3) - 1); // latztes Komma wieder raus
							}
						}
						
						if (!empty($value))
						{
							// Entfernen von evtl. vorhandenen CR/LF, sonst w�rde im csv-file ein neuer Satz ausgel�st werden
							$value = str_replace("\r", "", str_replace("\n", "", $value));
							// bei presel nur den 2. Teil ausgeben
							if ($mvcform_type == "pre_select")
							{
								$my_array = explode("+++", $value);
								$value = $my_array[1];
							}
						}
						if (($mvcform_type == "multiselect"
							OR $mvcform_type == "select"
							OR $mvcform_type == "radio"
							OR $mvcform_type == "check"
							OR $mvcform_type == "pre_select")
								AND $value == "0") $result[$i][$key] = "";
						else $result[$i][$key] = utf8_decode($value);
					}
					$i++;
				}
			}
		}
		return $result;
	}

	/**
	 * Daten exportieren
	 */
	function export_data($results = "", $tabname = "", $mode = "csv", $feld = "ohne")
	{
		// mv_id aus dem Tabellennamen extrahieren
		$tabellen_array = explode("_", $this->checked->tabelle);
		$mv_id = end($tabellen_array);
		// holt die Feldernamen und Namen extra f�r den Export aus der Tabelle
		$sql = sprintf("SELECT mvcform_name,
								mvcform_id,
								mvcform_name_export
								FROM %s 
								WHERE mvcform_meta_id = '%d' 
								AND mvcform_form_id = '%d'",
								
								$this->cms->tbname['papoo_mvcform'],
								
								$this->db->escape($this->checked->metaebene),
								$this->db->escape($mv_id)
						);
		$feldernamen_mit_exportnamen = $this->db->get_results($sql, ARRAY_A);
		// Trennzeichen definieren
		$trenner = $this->checked->trenner == "tab" ? "\t" : ";";
		// Daten in eine Variable laden. Austausch des Feldnamens mit dem f�r den Export.
		$cvs = "";
		if (!empty ($results))
		{
			if ($mode == "csv") // Modus CSV
			{
				foreach ($results as $erg)
				{
					if ($feld == "mit")
					{
						if ($iskey != 1)
						{
							// Alle Feldnamen rausholen
							// $keys ist array mit Feldnamen, $dk enth�lt den jeweiligen Feldnamen
							$keys = array_keys($erg);
							// Feldnamen zuweisen
							foreach ($keys as $dk)
							{
								if (!empty($feldernamen_mit_exportnamen))
								{
									foreach($feldernamen_mit_exportnamen as $feldname)
									{
										// wenn es einen Export Namen f�r dieses Feld gibt, dann auch verwenden
										if ($feldname['mvcform_name'] . "_" . $feldname['mvcform_id'] == $dk
											&& $feldname['mvcform_name_export'] != "") $dk = $feldname['mvcform_name_export'];
									}
								}
								$cvs .= $dk . $trenner;
							}
							// Neue Zeile
							if ($trenner == "\t")
							{
								$cvs = substr($cvs, 0, strlen($cvs) - 1); // letztes \t raus
								$cvs .= "\r\n";
							}
							else $cvs .= "\n";
							$iskey = 1;
						}
					}
					foreach ($erg as $key => $item)
					{
						if ($key == "active_7"
							&& empty($item)) $item = 0; // Korrektur empty wird zu 0 bei active_7
						elseif ($trenner == "\t") $item = str_replace("\r", "", str_replace("\n", "", $item)); // evtl. vorhandene CR & LF raus
						if ($item == "Array") $item = ""; // wg. �bernahme von fehlerhaften Daten aus alter Flex
						// Eintrag erstellen
						$cvs .= $item . $trenner;
					}
					// Neue Zeile
					if ($trenner == "\t")
					{
						$cvs = substr($cvs, 0, strlen($cvs) - 1); // letztes \t raus
						$cvs .= "\r\n"; // und mit CR & LF abschliessen
					}
					else $cvs .= "\n";
				}
			}
			// Modus xml
			else
			{
				// xml Klasse einbinden
				include (PAPOO_ABS_PFAD . "/lib/classes/class_informations.php");
				//Klasse initialisieren
				$xml = new informations2;
				//Start erzeugen
				$cvs = '<?xml version= "1.0" encoding= "utf-8" ?>' . "\n<data>";
				// <? Alle Eintr�ge durchgehen
				foreach ($results as $erg) { $cvs .=  $xml->array2xml->convert($erg); } //Daten aus Array in ein xml konvertieren
				// Schlu� erzeugen
				$cvs .= "</data>";
			} 
		}
		return $cvs;
	}

	/**
	 * Daten aus einer Datei einlesen, wenn CVS
	 * Erste Zeile ausgeben als Array f�r die Selectbox
	 * 
	 */
	function lese_erste_zeile_csv($ctrl = 0)
	{
		// Datei einlesen
		$daten = $this->get_csv_content();
		// Erste Zeile einlesen
		$felder = $daten['0'];
		// Semikolons, die kodiert sind, umkodieren
		$felder = str_replace('\;', "###sem###", $felder);
		// Inhalte in ein array einlesen
		$feld_ar = explode("\t", $felder);
		$i = 1;
		// Array durchgehen und Kodierung wieder umschalten
		// Selectbox-Inhalt aufbauen
		foreach ($feld_ar as $feld)
		{
			$feld = trim($feld);
			if (strlen($feld) > 2500) $feld = substr($feld, 0, 2500) . "..."; // Inhalt k�rzen
			// zur Excel-Orientierung: Spaltenbezeichnungen, wenn mit Semikolon separiert. F�r TAB separierte Dateien unn�tig.
			if ($i <= 26) $excel = chr($i + 64);
			else $excel = chr(floor(($i - 27) / 26) + 65) . chr($i - (floor(($i - 27) / 26) + 1) * 26 + 64);
			$excel = $i;
			// Lfd. Feldnr. voran zur besseren Identifikation, speziell bei Werten wie bloss 0, 1, leer, etc., wenn ctrl = 0
			if (!$ctrl
				AND $i != count($feld_ar) + 1) $feld_ar2[$i] = "(" . $excel . "): "; // letztes Array ist leer bei Satzende - �bergehen
			// Leerfelder-Kennzeichnung, auch zur besseren Identifizierung, aber nicht mehr am Satzende
			if ($feld == ""
				AND $i != count($feld_ar) + 1) $feld_ar2[$i] .= "Feld ist im 1. Satz leer";
			elseif ($i != count($feld_ar) + 1) $feld_ar2[$i] .=  str_ireplace("###sem###", ";", $feld); //Kodierte Semikolons wieder einlesen
			$i++;
		}
		return $feld_ar2;
	}

	/**
	 * Daten aus einer Datei einlesen wenn CVS
	 * Erste Zeile ausgeben als Array
	 * 
	 */
	function lese_erste_zeile_xml()
	{
		// Datei einlesen
		$daten = $this->get_xml_content();
		// Erste Zeile auslesen
		$i = 1;
		if (is_array($daten['data']['0']['data']['0']))
		{
			foreach ($daten['data']['0']['data']['0'] as $key => $value)
			{
				if ($key!= "attribute"
					and $key != "cdata")
				{
					$feld_ar2[$i] = $key;
					$i++;
				}
			}
		}
		return $feld_ar2;
	}

	/**
	 * Die Felder einer ausgew�hlten Tabelle zur�ckgeben
	 * 
	 */
	function lese_felder_tabelle($tabelle = "")
	{
		$tabelle = $this->db->escape($tabelle) . "_search_1"; // Aufbau f�r alle gleich, daher hier keine Unterscheidung bei den Sprachen
		// Eine Row rausholen
		$sql = sprintf("SHOW COLUMNS
						FROM %s",
						$this->cms->tbname[$tabelle]
				);
		$row = $this->db->get_results($sql, ARRAY_A);
		$output = array();
		$i = 1;
		foreach ($row as $key => $value)
		{
			// mv_dzvhae_system_id aus dem Array raus, wenn vorhanden
			if ($value['Field'] != "mv_dzvhae_system_id") $output[$i] = $value['Field'];
			$i++;
		}
		#if (array_pop(array_values($output)) ) unset($output[(array_pop(array_keys($output)))]);
		return $output;
	}

	/**
	 * Das Feld rausholen, das entfernt werden soll
	 */
	function get_entfernfeld($modus = "")
	{
		if (!empty($this->checked))
		{
			// Alle checked Felder durchgehen
			foreach ($this->checked as $key => $value)
			{
				// Modus Update
				if ($modus == "update")
				{
					// Wenn es sich um einen Entfernen-Button handelt
					if (stristr($key,"refstartentfernen")) $datar['0'] = "entf"; //Nummer rausholen ist immer vorne ala 1_start...
				}
				// Modus normal
				// Wenn es sich um einen Entfernen-Button handelt
				elseif (stristr($key,"startentfernen")) $datar = explode("_", $key); //Nummer rausholen ist immer vorne ala 1_start...
			}
		}
		// Wenn nicht leer, dann Inhalt zur�ckgeben
		if (!empty($datar['0'])) return $datar['0'];
		// Leer
		else return false;
	}

	/**
	 * Komplette CSV Daten einlesen
	 */
	function get_csv_content()
	{
		$handle = fopen(PAPOO_ABS_PFAD . "/dokumente/logs/" . basename($_SESSION['mv_import']['uploaded_file']), "r");
		$contents = fread($handle, filesize(PAPOO_ABS_PFAD . "/dokumente/logs/" . basename($_SESSION['mv_import']['uploaded_file'])));
		fclose ($handle);
		// Zeilenumbr�che codieren
		$contents = preg_replace('/\r\n/', "###u###", $contents);		
		$contents = preg_replace('/\n/', "###n###", $contents);
		$contents = preg_replace('/\r/', "###r###", $contents);
		if (stristr($contents, "###u###")) $contents_a = explode("###u###", $contents);
		else $contents_a = explode("###n###", $contents);
		$daten = array();
		if (!empty($contents_a))
			foreach ($contents_a as $zeile)
			{
				if (empty($zeile)) continue; // leere Zeilen �berlesen
				$daten[] = $zeile;
			};
		$_SESSION['mv_import']['import_daten'] = $daten;
		$_SESSION['mv_import']['import_anz_zeilen'] = count($daten);
		if ($this->checked->format == "csvmit") $_SESSION['mv_import']['import_anz_zeilen'] = $_SESSION['mv_import']['import_anz_zeilen'] - 1;
		if (!$daten[$_SESSION['mv_import']['import_anz_zeilen']]) $_SESSION['mv_import']['import_anz_zeilen'] = $_SESSION['mv_import']['import_anz_zeilen'] - 1;
		if ($_SESSION['mv_import']['import_anz_zeilen'] < 0) $_SESSION['mv_import']['import_anz_zeilen'] = 0;
		return $daten;
	}	

	/**
	 * CSV Daten einlesen
	 */
	function get_csv_content_rows()
	{
		$counter = $this->checked->counter_start;
		$war_das_ende = false;
		$daten = array();
		$counter_start_plus_max = $this->checked->counter_start + $this->max_counter_pro_runde;
		// jetzt die n�chsten $this->max_counter_pro_runde Zeilen einlesen
		while($counter < $counter_start_plus_max && !$war_das_ende) 
		{
			if (!empty($_SESSION['mv_import']['import_daten'][0])) 
			{
				$daten[] = $this->record = array_shift($_SESSION['mv_import']['import_daten']); // 1 Satz holen aus Session
				$rc = $this->check_record($counter); // Plausibilit�tspr�fung aller Felder
				if (count($this->fehler_infos)
					OR $rc)
				{
					if ($rc != "skip" // 1. Satz mit Feldernamen erhalten, aber leere S�tze (rc = "pop_only") entfernen
						AND count($this->fehler_infos))
					{
						array_pop($daten); // S�tze mit Fehler(n) auch entfernen
						$_SESSION['mv_import']['records_in_error']++;
					}
					if (count($this->fehler_infos) // bei Fehler und nicht leerem Satz
						AND $rc != "pop_only") $this->error_report($counter); // Fehlerprotokoll
				}
			}
			else $war_das_ende = true;
		  	$counter++;
		}
		$_SESSION['mv_import']['success_count'] = $_SESSION['mv_import']['import_anz_zeilen'] - $_SESSION['mv_import']['records_in_error'];
		$_SESSION['mv_import']['success_count'] = $_SESSION['mv_import']['success_count'] < 0 ? 0 : $_SESSION['mv_import']['success_count'];
		$this->error_report("sum"); // Anzahl der S�tze ans Fehlerprotokoll
		// wenn das Dateiende erreicht wurde, dann Flag setzen
		if ($counter < $this->max_counter_pro_runde
			|| $war_das_ende) $this->ende_der_datei = "ja";	
		// f�r dzvhae Sonderfall noch 1 Zeile dranh�ngen, damit das keine Probs bei csv Dateien mit Feldernamen macht
		if ($this->mv->dzvhae_system_id
			&& $this->ende_der_datei != "ja") $daten[] = $_SESSION['mv_import']['import_daten'][0];
		// Daten zur�ckgeben
		return $daten;
	}

	function check_record($counter = 0)
	{
		$this->fehler_infos = array();
		if ($counter == 0
			AND $this->checked->format == "csvmit") $rc = "skip"; // Der 1. Satz mit Tabellennamen wird nicht gebraucht
		else
		{
			if (!empty($this->record))
			{
				$this->field_arr = explode("\t", $this->record); // S�tze sind mit HT separiert
				// Check der abh�ngigen Pflichtfelder
				// table_fields nach Angaben zu abh. Pflichtfeldern durchsuchen
				foreach ($this->table_fields AS $key =>$value)
				{
					$feld_id_key = $pflicht_feld_id_key = NULL;
					if ($value['feld_id'] != ""
						AND $value['pflicht_feld_id'] != "") // sicherheitshalber beide
					{
						$pflicht_feld_id_key = $key + 1; // an diesem Punkt stehen wir, daher gleich �bernehmen
						// Suche nach dem key von feld_id in table_fields
						foreach ($this->table_fields AS $key2 => $value2)
						{
							if ($value2['mvcform_id'] == $value['feld_id']) $feld_id_key = $key2 + 1; // gefunden
							if ($feld_id_key)
							{
								// Suchen der keys, um damit im field_arr festzustellen, ob die Abh�ngigkeitsbedingung erf�llt ist
								foreach ($_SESSION['mv_import']['csv_ar'] AS $csv_key => $table_key)
								{
									if ($table_key == $feld_id_key) $field_arr_feld_id_key = $csv_key;
									if ($table_key == $pflicht_feld_id_key) $field_arr_pflicht_feld_key = $csv_key;
									// Beide keys gefunden
									if ($field_arr_feld_id_key != ""
										AND $field_arr_pflicht_feld_key != "")
									{
										// Pr�fen, ob im abh�ngigen Feld eine Eingabe vorhanden ist
										if ($this->field_arr[$field_arr_feld_id_key - 1] !=  ""
											AND $this->field_arr[$field_arr_pflicht_feld_key - 1] == "")
										{
											// Feldposition; csv-Zuordungs Z�hlung startet mit 1, field_arr & table_fields mit 0
											$this->fehler_infos[$counter][$field_arr_pflicht_feld_key - 1]['csv_pointer'] = $field_arr_pflicht_feld_key; // Feldpos.
											$this->fehler_infos[$counter][$field_arr_pflicht_feld_key - 1]['fehler'] = 0x100000; // Fehlercode
											$_SESSION['mv_import']['error_count']++;
											$this->error_report($counter); // Fehler in die DB
											$this->fehler_infos = array();
											$field_arr_feld_id_key = $field_arr_pflicht_feld_key = NULL;
											continue 3;
										}
									}
								}
							}
						}
					}
				}
				// Felder eines Satzes durchgehen und checken
				for ($i = 0; $i < $this->csv_max_key; $i++) // alle Felder bis zum letzten Feld (csv_max_key)
				{
					// diese 4 Systemfelder auch unbedingt pr�fen. Name statt Feldtyp verwenden f�r den dyn. Aufruf
					if ($this->table_fields[$i]['mvcform_name'] == "mv_content_id"
						OR $this->table_fields[$i]['mvcform_name'] == "mv_content_sperre"
						OR $this->table_fields[$i]['mvcform_name'] == "mv_content_teaser"
						OR $this->table_fields[$i]['mvcform_name'] == "mv_content_create_date"
						OR $this->table_fields[$i]['mvcform_name'] == "mv_content_edit_date") $mvcform_type = $this->table_fields[$i]['mvcform_name'];
					// diese Systemfelder nicht pr�fen
					elseif ($this->table_fields[$i]['mvcform_name'] == "mv_content_owner"
						OR $this->table_fields[$i]['mvcform_name'] == "mv_content_userid"
						OR $this->table_fields[$i]['mvcform_name'] == "mv_content_create_owner"
						OR $this->table_fields[$i]['mvcform_name'] == "mv_content_edit_user") $mvcform_type = ""; // wird ungepr�ft �berlesen
					// alles andere anhand des Feldtyps pr�fen
					else $mvcform_type = $this->table_fields[$i]['mvcform_type']; // Feldtyp f�r dyn. Aufruf
					// Nur diese Feldtypen sind erlaubt und werden gepr�ft
					if (empty($mvcform_type)
						OR !($mvcform_type == "check"
								OR $mvcform_type == "email"
								OR $mvcform_type == "file"
								OR $mvcform_type == "galerie"
								OR $mvcform_type == "link"
								OR $mvcform_type == "multiselect"
								OR $mvcform_type == "password"
								OR $mvcform_type == "picture"
								OR $mvcform_type == "pre_select"
								OR $mvcform_type == "preisintervall"
								OR $mvcform_type == "radio"
								OR $mvcform_type == "select"
								OR $mvcform_type == "text"
								OR $mvcform_type == "textarea"
								OR $mvcform_type == "textarea_tiny"
								OR $mvcform_type == "timestamp"
								OR $mvcform_type == "zeitintervall"
								OR $mvcform_type == "artikel"
								OR $mvcform_type == "flex_verbindung"
								OR $mvcform_type == "flex_tree"
								OR $mvcform_type == "mv_content_id"
								OR $mvcform_type == "mv_content_sperre"
								OR $mvcform_type == "mv_content_teaser"
								OR $mvcform_type == "mv_content_create_date"
								OR $mvcform_type == "mv_content_edit_date"
								OR $mvcform_type == "sprechende_url")) continue; // unbekannter Feldtyp oder Systemfeld: Daten ungepr�ft �bernehmen

					$this->field_arr[$i] = trim($this->field_arr[$i]);
					// Pr�fung auf Pflichtfeld oder abh�ngiges Pflichtfeld
					if (($this->table_fields[$i]['mvcform_must_back'] == 1 OR $this->table_fields[$i]['feld_id'])
						AND $this->field_arr[$i] == "")
					{
						$rc = 1; // Fehler Pflichtfeld
						$_SESSION['mv_import']['error_count']++;
					}
					elseif (!empty($this->field_arr[$i])) // Feld pr�fen
					{
						// Dyn. Aufruf der Pr�f-Routine aufgrund des Feldtyps oder Systemfeld-Namens
						$method = 'validate_' . $mvcform_type; // z. B. validate_multiselect
						$rc = call_user_func(array($this, $method), $i); // und aufrufen/ausf�hren. Pointer in die Tabellen csv/DB
					}
					if ($rc) // aufgetretenen Fehler dem array zuweisen
					{
						// Feldposition; csv-Zuordungs Z�hlung startet mit 1, field_arr & table_fields mit 0
						$this->fehler_infos[$counter][$i]['csv_pointer'] = $i + 1; // Korrektur + 1 f�r die Feld-Pos.-Z�hlweise ab 1 (kommt in die DB)
						$this->fehler_infos[$counter][$i]['fehler'] = $rc; // Fehlercode
						$rc = "";
					}
				}
			}
			else $rc = "pop_only"; // leerer Satz: ausklinken
		}
		return $rc;
	}

	function validate_mv_content_id($i = 0)
	{
		if (!ctype_digit($this->field_arr[$i])) $fehler = 0x8; // nicht numerisch
		elseif ($this->checked->ins == "ins_neu") // = neue hinzu. Pr�fung f�r ins_del_neu (del all & neu) nicht m�glich, da die S�tze noch nicht gel�scht sind
		{
			$sql = sprintf("SELECT count(mv_content_id) FROM %s
															WHERE mv_content_id = '%d'",
															
															$this->cms->tbname[$this->checked->tabelle],
															
															$this->db->escape($this->field_arr[$i])
							);
			if ($this->db->get_var($sql)) $fehler = 0x80000; // schon vorhanden
		}
		if ($fehler)
		{
			$this->table_fields[$i]['mvcform_name'] = "mv_content_id";
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}

	function validate_mv_content_sperre($i = 0)
	{
		if (!($this->field_arr[$i] == "0"
			OR $this->field_arr[$i] == "1"))
		{
			$fehler = 0x20000; // nicht 0 oder 1
			$_SESSION['mv_import']['error_count']++;
		}
		if ($fehler) $this->table_fields[$i]['mvcform_name'] = "mv_content_sperre";
		return $fehler;
	}

	function validate_mv_content_teaser($i = 0)
	{
		if (in_array($this->field_arr[$i], [0, 1]) == false) {
			$fehler = 0x20000; // nicht 0 oder 1
			$_SESSION['mv_import']['error_count']++;
		}
		if ($fehler) $this->table_fields[$i]['mvcform_name'] = "mv_content_teaser";
		return $fehler;
	}

	function validate_mv_content_create_date($i = 0)
	{
		$fehler = $this->check_date_and_time($this->field_arr[$i]);
		if ($fehler & 0x800)
		{
			$fehler = $fehler ^ 0x800; // 0x800 ausschalten
			$fehler = $fehler | 0x40000; // 0x40000 stattdessen einschalten
			$this->table_fields[$i]['mvcform_name'] = "mv_content_create_date";
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}
	
	function validate_mv_content_edit_date($i = 0)
	{
		$fehler = $this->check_date_and_time($this->field_arr[$i]);
		if ($fehler & 0x800)
		{
			$fehler = $fehler ^ 0x800; // 0x800 ausschalten
			$fehler = $fehler | 0x40000; // 0x40000 stattdessen einschalten
			$this->table_fields[$i]['mvcform_name'] = "mv_content_edit_date";
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}

	function validate_text($i = 0)
	{
		if ($this->table_fields[$i]['mvcform_name'] == "Benutzername")
		{
			$sql = sprintf("SELECT * FROM %s
											WHERE username = '%s'",
											
											$this->cms->tbname['papoo_user'],
											
											$this->db->escape($this->field_arr[$i])
							);
			#$result = $this->db->get_results($sql, ARRAY_A);
			#if (count($result)) // schon vorhanden
			#{
			#	$sql = sprintf("DELETE FROM %s
			#							WHERE username = '%s'",
			#							$this->cms->tbname['papoo_user'],
			#							$this->db->escape($this->field_arr[$i])
			#					);
			#	$this->db->query($sql);
			#	$sql = sprintf("DELETE FROM %s
			#							WHERE userid = '%d'",
			#							$this->cms->tbname['papoo_lookup_ug'],
			#							$this->db->escape($result[0]['userid'])
			#					);
			#	$this->db->query($sql);
				#$fehler = 0x80000; // schon vorhanden
				#$_SESSION['mv_import']['error_count']++;
			#}
		}
		if ($this->table_fields[$i]['mvcform_minlaeng']
			AND $this->table_fields[$i]['mvcform_minlaeng'] > strlen($this->field_arr[$i]))
		{
			$fehler = $fehler | 2; // Fehler Min. L�nge
			$_SESSION['mv_import']['error_count']++;
		}
		if ($this->table_fields[$i]['mvcform_maxlaeng']
			AND $this->table_fields[$i]['mvcform_maxlaeng'] < strlen($this->field_arr[$i]))
		{
			$fehler = $fehler | 4; // Fehler Max. L�nge
			$_SESSION['mv_import']['error_count']++;
		}
		if ($this->table_fields[$i]['mvcform_content_type'] == "num"
			AND !ctype_digit($this->field_arr[$i]))
		{
			$fehler = $fehler | 8; // Fehler nicht numerisch
			$_SESSION['mv_import']['error_count']++;
		}
		if (strlen($this->field_arr[$i]) > 255)
		{
			$fehler = $fehler | 0x200; // zu viele Zeichen
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}

	function validate_textarea($i = 0)
	{
		if ($this->table_fields[$i]['mvcform_minlaeng']
			AND $this->table_fields[$i]['mvcform_minlaeng'] > strlen($this->field_arr[$i]))
		{
			$fehler = 2; // Fehler Min. L�nge
			$_SESSION['mv_import']['error_count']++;
		}
		if (strlen($this->field_arr[$i]) > 65535)
		{
			$fehler = $fehler | 0x100; // zu viele Zeichen
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}
	
	function validate_textarea_tiny($i = 0)
	{
		if ($this->table_fields[$i]['mvcform_minlaeng']
			AND $this->table_fields[$i]['mvcform_minlaeng'] > strlen($this->field_arr[$i]))
		{
			$fehler = 2; // Fehler Min. L�nge
			$_SESSION['mv_import']['error_count']++;
		}
		if (strlen($this->field_arr[$i]) > 65535)
		{
			$fehler = $fehler | 0x100; // zu viele Zeichen
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}

	function validate_email($i = 0)
	{
		$erg = preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9_-]+(\.[_a-z0-9-]+)+$/i', $this->field_arr[$i]);
		if (!$erg)
		{
			$fehler = 0x10;
			$_SESSION['mv_import']['error_count']++;
		}
		if (strlen($this->field_arr[$i]) > 255)
		{
			$fehler = $fehler | 0x200; // zu viele Zeichen
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}

	function validate_password($i = 0)
	{
		if ($this->table_fields[$i]['mvcform_minlaeng']
			AND $this->table_fields[$i]['mvcform_minlaeng'] > strlen($this->field_arr[$i]))
		{
			$fehler = 2; // Fehler Min. L�nge
			$_SESSION['mv_import']['error_count']++;
		}
		if ($this->table_fields[$i]['mvcform_maxlaeng']
			AND $this->table_fields[$i]['mvcform_maxlaeng'] < strlen($this->field_arr[$i]))
		{
			$fehler = $fehler | 4; // Fehler Max. L�nge
			$_SESSION['mv_import']['error_count']++;
		}
		if (strlen($this->field_arr[$i]) > 255)
		{
			$fehler = $fehler | 0x200; // zu viele Zeichen
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}

	function validate_link($i = 0)
	{
		if (substr($this->field_arr[$i], 0, 7) != "http://")
		{
			$fehler = 0x20;
			$_SESSION['mv_import']['error_count']++;
		}
		if (strlen($this->field_arr[$i]) > 255)
		{
			$fehler = $fehler | 0x200; // zu viele Zeichen
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}

	function validate_select($i = 0)
	{
		if ($this->field_arr[$i] == "0") $fehler = 0x40;
		elseif (strlen($this->field_arr[$i]) > 255) $fehler = $fehler | 0x200; // zu viele Zeichen
		if ($fehler) $_SESSION['mv_import']['error_count']++;
		return $fehler;
	}

	function validate_multiselect($i = 0)
	{
		if ($this->field_arr[$i] == "0")
		{
			$fehler = 0x40;
			$_SESSION['mv_import']['error_count']++;
		}
		else
		{
			$val_array = explode(",", $this->field_arr[$i]);
			foreach ($val_array AS $key => $value)
			{
				if (empty($value))
				{
					$fehler = 0x40;
					$_SESSION['mv_import']['error_count']++;
				}
			}
			if (strlen($this->field_arr[$i]) > 65535)
			{
				$fehler = $fehler | 0x100; // zu viele Zeichen
				$_SESSION['mv_import']['error_count']++;
			}
		}
		return $fehler;
	}

	function validate_pre_select($i = 0)
	{
		$presel = explode("+++", $this->field_arr[$i]);
		if (count($presel) != 2) $fehler = 0x80; // Preselect falsches Format
		elseif (empty($presel[0])
				OR empty($presel[1])) $fehler = 0x80; // Preselect hat mind. einen empty value -> wrong format
		if ($fehler) $_SESSION['mv_import']['error_count']++;
		if (strlen($this->field_arr[$i]) > 65535)
		{
			$fehler = $fehler | 0x100; // zu viele Zeichen
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}

	function validate_radio($i = 0)
	{
		if ($this->field_arr[$i] == "0") $fehler = 0x40;
		elseif (strlen($this->field_arr[$i]) > 255) $fehler = 0x200; // zu viele Zeichen
		if ($fehler) $_SESSION['mv_import']['error_count']++;
		return $fehler;
	}

	function validate_check($i = 0)
	{
		if ($this->field_arr[$i] == "0") $fehler = 0x40;
		elseif (strlen($this->field_arr[$i]) > 255) $fehler = $fehler | 0x200; // zu viele Zeichen
		if ($fehler) $_SESSION['mv_import']['error_count']++;
		return $fehler;
	}

	function validate_timestamp($i = 0)
	{
		$fehler = $this->check_date_and_time($this->field_arr[$i]);
		if ($fehler & 0x800)
		{
			$fehler = $fehler ^ 0x800; // 0x800 ausschalten
			$fehler = $fehler | 0x8000; // 0x8000 stattdessen einschalten
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}

	function validate_file($i = 0)
	{
		$suffix = substr($this->field_arr[$i], strlen($this->field_arr[$i]) - 4);
		if ($suffix == ".php"
			OR $suffix == ".php3"
			OR $suffix == ".php4"
			OR $suffix == ".php5"
			OR $suffix == ".php6"
			OR $suffix == ".phtml"
			OR $suffix == ".cgi"
			OR $suffix == ".pl")
		{
			$fehler = 0x4000; // unerlaubte Datei
			$_SESSION['mv_import']['error_count']++;
		}
		if (strlen($this->field_arr[$i]) > 255)
		{
			$fehler = $fehler | 0x200; // zu viele Zeichen
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}

	function validate_picture($i = 0)
	{
		$dateiname = str_replace(".jpeg", ".jpg", $this->field_arr[$i]);
		$suffix = strtolower(substr($dateiname, strlen($dateiname) - 4));
		if (!($suffix == ".jpg"
			OR $suffix == ".png"
			OR $suffix == ".gif"))
		{
			$fehler = 0x2000; // unerlaubte Datei
			$_SESSION['mv_import']['error_count']++;
		}
		if (strlen($this->field_arr[$i]) > 255)
		{
			$fehler = $fehler | 0x200; // zu viele Zeichen
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}

	function validate_galerie($i = 0)
	{
		$dateien = explode(";", $this->field_arr[$i]);
		foreach ($dateien AS $key => $dateiname)
		{
			if (!empty($dateiname))
			{
				$dateiname = str_replace(".jpeg", ".jpg", $dateiname);
				$suffix = strtolower(substr($dateiname, strlen($dateiname) - 4));
				if (!($suffix == ".jpg"
					OR $suffix == ".png"
					OR $suffix == ".gif"))
				{
					$fehler = 0x2000; // unerlaubte Datei
					$_SESSION['mv_import']['error_count']++;
					break;
				}
			}
		}
		if (strlen($this->field_arr[$i]) > 65535)
		{
			$fehler = $fehler | 0x100; // zu viele Zeichen
			$_SESSION['mv_import']['error_count']++;
		}
		return $fehler;
	}

	function validate_artikel($i = 0)
	{
		return 0;
	}

	function validate_flex_verbindung($i = 0)
	{
		return 0;
	}

	function validate_flex_tree($i = 0)
	{
		return 0;
	}

	function validate_zeitintervall($i = 0)
	{
		$dates = explode(",", $this->field_arr[$i]);
		if (count($dates) != 2) $fehler = 0x10000;
		else
		{
			$fehler = $this->check_date_and_time($dates[0]); // von
			if ($fehler) $_SESSION['mv_import']['error_count']++;
			$fehler2 = $this->check_date_and_time($dates[1]); // bis
			if ($fehler2) $_SESSION['mv_import']['error_count']++;
			$fehler = $fehler | $fehler2;
			if ($fehler
				AND $fehler2
				AND $fehler == $fehler2) $_SESSION['mv_import']['error_count']--; // 2mal derslebe Fehler: nur einmal z�hlen
		}
		return $fehler;
	}

	function validate_preisintervall($i = 0)
	{
 		$this->field_arr[$i] = str_replace(",", ".", $this->field_arr[$i]);
		$num_arr = explode(".", $this->field_arr[$i]);
		foreach ($num_arr AS $key => $value)
		{
			if (!ctype_digit($value))
			{
				$fehler = 0x1000; // nicht numerisch
				$_SESSION['mv_import']['error_count']++;
			}
		}
		return $fehler;
	}

	function check_date_and_time($date_time)
	{
		// Tag, Monat, Datum und, wenn vorhanden, Uhrzeit ermitteln
		// hier keine Pr�fungen, wenn dabei was schief l�uft finden dies die anschliessenden Pr�fungen raus
		$datum = explode(".", $date_time);
		if (strlen($datum[0]) == 4) // beginnt mit dem Jahr, dann muss: "JJJJ.MM.DD" mit/ohne "HH:MM:SS"
		{
			$jahr = $datum[0];
			$monat = $datum[1];
			#if (strlen($datum[2]) == 2) $tag = $datum[2]; // Tag ohne Uhrzeit
			#elseif (strlen($datum[2]) == 11) // Tag mit Uhrzeit "DD HH:MM:SS"
			if (strlen($datum[2]) == 11) // Tag mit Uhrzeit "DD HH:MM:SS"
			{
				$datum2 = explode(" ", $datum[2]);
				$tag = $datum2[0];
				$zeit = $datum2[1];
			}
			#elseif (strlen($datum[2]) == 8) // Tag mit Uhrzeit "DD HH:MM"
			#{
			#	$datum2 = explode(" ", $datum[2]);
			#	$tag = $datum2[0];
			#	$zeit = $datum2[1] . ":00";
			#}
		}
		#elseif (strlen($datum[0]) == 2)
		#{
		#	$tag = $datum[0];
		#	$monat = $datum[1];
		#	if (strlen($datum[2]) == 2) $jahr = $datum[2]; // Jahr ohne Uhrzeit
		#	elseif (strlen($datum[2]) == 13) // Jahr mit Uhrzeit "DD HH:MM:SS"
		#	{
		#		$datum2 = explode(" ", $datum[2]);
		#		$jahr = $datum2[0];
		#		$zeit = $datum2[1];
		#	}
		#	elseif (strlen($datum[2]) == 10) // Jahr mit Uhrzeit "DD HH:MM"
		#	{
		#		$datum2 = explode(" ", $datum[2]);
		#		$jahr = $datum2[0];
		#		$zeit = $datum2[1] . ":00";
		#	}
		#}
		// Pr�fungen
		// wenn Pflichtfeld, Fehler bei leerem Feld Tag, Monat und/oder Jahr. Keine weiteren Checks sinnvoll.
		if ($daten->mvcform_must_back == 1
			AND (empty($tag) OR empty($monat) OR empty($jahr))) $fehler = 1; // Fehler Pflichtfeld
		// Pr�fung auf leeres Feld ist schon vor dem Aufruf erfolgt. Datum muss gept�ft werden
		else
		{
			$tag = $tag ? $tag : 0;
			$monat = $monat ? $monat : 0;
			$jahr = $jahr ? $jahr : 0;
			
			if (!checkdate($monat, $tag, $jahr)) $fehler = $fehler | 0x800; // Fehler Datum
		}
		// Check Uhrzeit
		if ($zeit
			AND !(bool)preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/', $zeit)) $fehler = $fehler | 0x400; // nur so: 00-23:00-59:00-59 2stellig!
		return $fehler;
	}

	// Pr�fen, ob der angemeldete User zur Gruppe Admin geh�rt
	function check_admin()
	{
		$sql = sprintf("SELECT *
						FROM %s
						WHERE userid = '%d' AND gruppenid = 1",
						
						$this->cms->tbname['papoo_lookup_ug'],
						
						$this->user->userid
						);
		return count($this->db->get_results($sql, ARRAY_A)) ? true : false;
	}

	function imex_error_report()
	{
		$this->content->template['is_admin'] = $this->check_admin();
		if ($this->checked->report_del_id
			AND $this->content->template['is_admin'])
		{
			$sql = sprintf("DELETE FROM %s 
									WHERE report_id = '%d'",
									
									$this->cms->tbname['papoo_mv_imex_error_report'],
									
									$this->db->escape($this->checked->report_del_id)
						);
			$this->db->query($sql);
			$sql = sprintf("DELETE FROM %s 
									WHERE report_id = '%d'",
									
									$this->cms->tbname['papoo_mv_imex_error_report_details'],
									
									$this->db->escape($this->checked->report_del_id)
						);
			$this->db->query($sql);
			$this->content->template['report_deleted'] = 1;
		}
		// Fehlerprotokoll holen
		$sql = sprintf("SELECT * FROM %s ORDER BY import_time DESC",
									$this->cms->tbname['papoo_mv_imex_error_report']
						);
		$results = $this->db->get_results($sql, ARRAY_A);
		if (count($results))
		{
			// Datum import_time umformatieren
			foreach ($results AS $key => $field)
			{
				foreach ($field AS $fieldname => $value)
				{
					if ($fieldname == "import_time")
					{
						$date = new DateTime($value);
						$results[$key]['import_time'] = $date->format('d.m.Y H:i:s');
					}
				}
			}
			$this->content->template['error_report'] = $results; // Fehlerprotokoll-�bersicht ans Template
		}
	}
	
	function imex_error_report_details()
	{
		if ($this->checked->report_id) // Details anzeigen
		{
			$sql = sprintf("SELECT count(report_id) FROM %s 
										WHERE report_id = '%d'",
										
										$this->cms->tbname['papoo_mv_imex_error_report_details'],
										
										$this->db->escape($this->checked->report_id)
							);
			$this->weiter->result_anzahl = $this->db->get_var($sql);
			$this->weiter->make_limit(20);
			// Fehlerprotokoll holen
			$sql = sprintf("SELECT * FROM %s 
										WHERE report_id = '%d'
										ORDER BY import_file_record_no, import_file_field_position
										%s",
										
										$this->cms->tbname['papoo_mv_imex_error_report_details'],
										
										$this->db->escape($this->checked->report_id),
										$this->weiter->sqllimit
							);
			$results = $this->db->get_results($sql, ARRAY_A);
			if (($this->checked->page - 1) > 0) $i = (($this->checked->page - 1) * $this->weiter->pagesize) + 1;
			else $i = 1;
			if (count($results))
			{
				foreach ($results AS $field_key => $elements)
				{
					$msg = array();
					$fehler = $elements['completion_code'];
					if ($fehler & 0x100000) $msg[] = $this->content->template['plugin']['mv']['fielddefinition']['error27']; // abh�ngiges Pflichtfeld
					if ($fehler &  0x80000) $msg[] = $this->content->template['plugin']['mv']['fielddefinition']['error26']; // schon vorhanden
					if ($fehler &  0x40000) $msg[] = $this->content->template['plugin']['mv']['no_valid_date3'];				// Datum ung�ltig
					if ($fehler &  0x20000) $msg[] = $this->content->template['plugin']['mv']['fielddefinition']['error25']; // nur 0/1
					if ($fehler &  0x10000) $msg[] = $this->content->template['plugin']['mv']['no_valid_dates'];				// Timestamp Datum
					if ($fehler &   0x8000) $msg[] = $this->content->template['plugin']['mv']['no_valid_date2'];				// Timestamp Datum
					if ($fehler &   0x4000) $msg[] = $this->content->template['plugin']['mv']['falsche_dateiendung'];		// Dateityp
					if ($fehler &   0x2000) $msg[] = $this->content->template['plugin']['mv']['falsche_dateiendung2'];		// Bilddateityp
					if ($fehler &   0x1000) $msg[] = $this->content->template['plugin']['mv']['pi_not_numeric'];				// Preisintervall nicht numerisch
					if ($fehler &    0x800) $msg[] = $this->content->template['plugin']['mv']['no_valid_date'];				// Zeitintervall Datum 
					if ($fehler &    0x400) $msg[] = $this->content->template['plugin']['mv']['no_valid_time'];				// Uhrzeit
					if ($fehler &    0x200) $msg[] = $this->content->template['plugin']['mv']['max255_4'];					// zu viele Zeichen
					if ($fehler &    0x100) $msg[] = $this->content->template['plugin']['mv']['max65535_3'];					// zu viele Zeichen
					if ($fehler &     0x80) $msg[] = $this->content->template['plugin']['mv']['fielddefinition']['error12']; // falsches Format pre_select
					if ($fehler &     0x40) $msg[] = $this->content->template['plugin']['mv']['fielddefinition']['error24']; // 0 bei select
					if ($fehler &     0x20) $msg[] = $this->content->template['plugin']['mv']['http_falsch'];				// link ohne http://
					if ($fehler &     0x10) $msg[] = $this->content->template['plugin']['mv']['email_error'];				// mail format
					if ($fehler &      0x8) $msg[] = $this->content->template['plugin']['mv']['num_error'];					// not numeric
					if ($fehler &      0x4) $msg[] = $this->content->template['plugin']['mv']['max65535_4'];					// max. L�nge
					if ($fehler &      0x2) $msg[] = $this->content->template['plugin']['mv']['max65535_4'];					// min. L�nge
					if ($fehler &      0x1) $msg[] = $this->content->template['plugin']['mv']['pflichtfeld'];				// Pflichtfeld
					if ($fehler > $_SESSION['mv_import']['highest_cc']) $_SESSION['mv_import']['highest_cc'] = $fehler;
					foreach ($msg AS $key => $err_msg)
					{
						$new_results[$field_key][$key] = $elements;
						$new_results[$field_key][$key]['import_error_msg'] = $err_msg;
						$new_results[$field_key][$key]['completion_code'] = strtoupper(dechex($results[$field_key]['completion_code']));
						if ((floor(strlen($new_results[$field_key][$key]['completion_code']) / 2) * 2) != strlen($new_results[$field_key][$key]['completion_code']))
							$new_results[$field_key][$key]['completion_code'] = "0" . $new_results[$field_key][$key]['completion_code'];
						$new_results[$field_key][$key]['completion_code'] = "0x" . $new_results[$field_key][$key]['completion_code'];
						
						// zur Excel-Orientierung: Spaltenbezeichnungen, wenn mit Semikolon separiert. F�r TAB separierte Dateien unn�tig.
						$char = $results[$field_key]['import_file_field_position'] - 1; // 0 - x
						if (($char + 1) <= 26) $new_results[$field_key][$key]['excel'] = chr(($char + 1) + 64);
						else $new_results[$field_key][$key]['excel'] =
								chr(floor((($char + 1) - 27) / 26) + 65) . chr(($char + 1) - (floor((($char + 1) - 27) / 26) + 1) * 26 + 64);
								
						$new_results[$field_key][$key]['error_no'] = $i; // Lfd. Nummer
						$i++;
					}
				}
			}
			$sql = sprintf("SELECT error_count FROM %s 
										WHERE report_id = '%d'",
										
										$this->cms->tbname['papoo_mv_imex_error_report'],
										
										$this->db->escape($this->checked->report_id)
							);
			$this->content->template['error_count'] = $this->db->get_var($sql);
			$this->content->template['error_report_details'] = $new_results;
			$this->weiter->weiter_link = "./plugin.php?menuid="
											. $this->checked->menuid
											. "&template=mv/templates/imex_error_report_details.html&report_id="
											. $this->checked->report_id;
			$this->weiter->do_weiter("teaser");
			
			$sql = sprintf("SELECT * FROM %s 
										WHERE report_id = '%d'",
										
										$this->cms->tbname['papoo_mv_imex_error_report'],
										
										$this->db->escape($this->checked->report_id)
							);
			$results = $this->db->get_results($sql, ARRAY_A);
			$this->content->template['report_id'] = $results[0]['report_id'];
			$date = new DateTime($results[0]['import_time']);
			$this->content->template['import_time'] = $date->format('d.m.Y H:i:s');
		}
	}

	function error_report($counter = 0)
	{
		// Am Ende des Imports die Z�hler speichern /Fehler/erfolgreich und gesamt)
		if ($counter == "sum")
		{
			$sql = sprintf("UPDATE %s SET error_count = '%d',
											success_count = '%d',
											highest_cc = '%d',
											records_to_import = '%d' 
											WHERE report_id = '%d'",
											
											$this->cms->tbname['papoo_mv_imex_error_report'],
											
											$this->db->escape($_SESSION['mv_import']['error_count']),
											$this->db->escape($_SESSION['mv_import']['success_count']),
											$this->db->escape($_SESSION['mv_import']['highest_cc']),
											$this->db->escape($_SESSION['mv_import']['import_anz_zeilen']),
											$this->db->escape($_SESSION['mv_import']['report_id'])
							);
			$this->db->query($sql);
		}
		else
		{
			// Einer der Imports�tze wurde auf Plausibilit�t gepr�ft und es sind ein oder mehrere Fehler aufgetreten
			// die im laufenden Importsatz aufgetretenen Fehler in einem einzigen DB-Satz speichern
			foreach ($this->fehler_infos AS $record_no => $field_no)
			{
				$counter_plus = $this->checked->format == "csvmit" ? 1 : 0;
				$rec = $record_no + $counter_plus;
				foreach ($field_no AS $field_key => $elements)
				{
					$msg = array();
					// zur Excel-Orientierung: Spaltenbezeichnungen, wenn mit Semikolon separiert. F�r TAB separierte Dateien unn�tig.
					$sql = sprintf("INSERT INTO %s SET report_id = '%d',
															import_file_record_no = '%d',
															import_file_field_position = '%d',
															import_file_field_name = '%s',
															completion_code = '%d'",
															
															$this->cms->tbname['papoo_mv_imex_error_report_details'],
															
															$this->db->escape($_SESSION['mv_import']['report_id']),
															$this->db->escape($rec),
															$this->db->escape($field_key + 1),
															$this->db->escape($_SESSION['mv_import']['first_line'][$elements['csv_pointer']]),
															$this->db->escape($elements['fehler'])
									);
					$this->db->query($sql);
				}
			}
		}
	}

	function get_validation_data()
	{
		$mv_id = end(explode("_", $this->checked->tabelle));
		// Lese die Attribute aller Felder, auch abh�ngige Pflichtfelder, wenn vorhanden
		$sql = sprintf("SELECT * FROM %s T1
								LEFT OUTER JOIN %s T2 ON (T1.mvcform_meta_id = T2.meta_id)
															AND (T1.mvcform_form_id = T2.mv_id)
															AND (T1.mvcform_id = T2.pflicht_feld_id)
								WHERE mvcform_meta_id = '%d' 
								AND mvcform_form_id = '%d'",
								
								$this->cms->tbname['papoo_mvcform'],
								
								$this->cms->tbname['papoo_mvcform_pflicht_lp'],
								
								$this->db->escape($this->checked->metaebene),
								$this->db->escape($mv_id)
						);
		$mvcfom_data = $this->db->get_results($sql, ARRAY_A);
		$tabelle_felder = $this->lese_felder_tabelle($this->checked->tabelle);
		// Feldzuordnung (csv - Tabelle) durchgehen (Z�hlweise beider Tabellen-Pointer in $_SESSION['mv_import']['csv_ar'] ab 1)
		if (count($_SESSION['mv_import']['csv_ar']))
		{
			foreach ($_SESSION['mv_import']['csv_ar'] AS $csv_index => $table_index)
			{
				// Systemfelder
				if ($tabelle_felder[$table_index] == "mv_content_id"
					OR $tabelle_felder[$table_index] == "mv_content_owner"
					OR $tabelle_felder[$table_index] == "mv_content_userid"
					OR $tabelle_felder[$table_index] == "mv_content_userid"
					OR $tabelle_felder[$table_index] == "mv_content_sperre"
					OR $tabelle_felder[$table_index] == "mv_content_teaser"
					OR $tabelle_felder[$table_index] == "mv_content_create_date"
					OR $tabelle_felder[$table_index] == "mv_content_edit_date"
					OR $tabelle_felder[$table_index] == "mv_content_create_owner"
					OR $tabelle_felder[$table_index] == "mv_content_edit_user") $this->table_fields[$csv_index - 1]['mvcform_name'] = $tabelle_felder[$table_index];
				// Feld-Attribute suchen
				foreach ($mvcfom_data AS $key => $value)
				{
					foreach ($value AS $feldname => $value2)
					{
						if ($feldname == "mvcform_id") $feldid = $value2;
						if ($feldname == "mvcform_name") $table_feldname = $value2;
						if ($feldid
							AND $table_feldname)
						{
							$realname = $table_feldname . "_" . $feldid; // konstuierter voller Feldname aus den Daten von mvcform
							// Feldattribut ist gefunden, wenn die Namen �bereinstimmen (mvcform_data und tabelle_felder)
							if ($realname == $tabelle_felder[$table_index])
							{
								// Gefunden und mit dem csv_index als key ins array
								$this->table_fields[$csv_index - 1] = $mvcfom_data[$key]; // csv array, table_fields starten mit 0, Key Umsetzung - 1
								$feldid = $table_feldname = "";
								continue 2;
							}
						}
					}
				}
			}
			// H�chsten index feststellen, wird sp�ter bei der Plausib der Felder gebraucht, um auch das letzte Feld zu checken.
			// $_SESSION['mv_import']['csv_ar'] ist nicht nach keys mit asc sortiert (ist in der Reihenfolge sortiert, wie bei der Zuordnung eingegeben).
			$my_arr = $_SESSION['mv_import']['csv_ar']; // zwischenspeichern
			ksort($my_arr); // keysort asc
			$value = end($my_arr); // set key pointer ans Ende
			$this->csv_max_key = key($my_arr); // h�chster key
			unset($my_arr);
		}
	}

	/**
	 * Komplette XML Daten einlesen
	 */
	function get_xml_content()
	{
		// Datei bestimmen
		$file = PAPOO_ABS_PFAD . "/dokumente/logs/" . basename($_SESSION['mv_import']['uploaded_file']);
		// XML Parser globalisieren
		global $xmlparser;
		// �bergeben
		$xml = $xmlparser;
		//Inhalt einlesen und in ein Array parsen
		$xml->parse($file);
		// Array �bergeben
		$xml_array = $xml->xml_data;
		if (is_array($xml_array)) return $xml_array;
		return false;
	}

	/**
	 * Eingelesene Daten konvertieren
	 */
	function convert_to_array($dat = "", $mit = "")
	{
		$retdaten = array();
		$k = 0;
		// Wenn Eintr�ge vorhanden
		if (!empty($dat))
		{
			// Alle Eintr�ge durchgehen
			foreach ($dat as $key => $value)
			{
				// Wenn erste Zeile Datennamen sind, diese �berspringen
				if ($mit == "1"
					&& $this->counter_start == "0")
				{
					$k++;
					if ($k < 2) continue;
				}
				// Semikolons, die kodiert sind, umkodieren
				$value = str_replace('\;', "###sem###", $value);
				// Inhalte in ein array einlesen
				$feld_ar = explode("\t", $value);
				$i = 1;
				$co = count($feld_ar);
				// Array durchgehen und Kodierung wieder umschalten
				foreach ($feld_ar as $feld)
				{
					// Zeilenumbr�che und sonstigen M�ll entfernen
					$feld = trim($feld);
					// Wenn sinnvoller Inhalt, dann �bergeben
					if ($i <= $co)
					{
						// Inhalt k�rzen
						if (strlen($feld) > 2500) $feld = substr($feld, 0, 2500) . "...";
						// Kodierte Semikolons wieder einlesen
						$feld_ar2[$i] = str_ireplace("###sem###", ";", $feld);
						$i++;
					}
				}
				$retdaten[$key] = $feld_ar2;
			}
		}
		return $retdaten;
	}
//UNUSED
	/**
	 * Eine Sicherung bei jedem Insert machen
	 * 
	 */
	function make_sicherung()
	{
		// Daten holen
		$daten = $this->get_data($this->checked->tabelle, $this->checked->format);
		// Daten erzeugen
		$datenfertig = $this->export_data($daten, $this->checked->tabelle, "xml", $this->checked->feld);
		// Ausgeben als Link
		if (!empty($datenfertig))
		{
			// Zeitstempel
			$time = time();
			// Dateinamen
			$file = "/dokumente/logs/sicherung_vor_import_" . basename($this->checked->tabelle) . $time . ".xml";
			// Datei erzeugen
			$this->diverse->write_to_file($file, $datenfertig);
		}
	}

	/**
	 * Tabelle aus Feldnamen erstellen
	 */
	function make_tab_name_from_feld($feld)
	{
		// Tabellennamen �bergeben
		$tabelle = $this->checked->tabelle;
		// Id raushiolen
		$feld_ar = explode("_", $feld);
		$endwert = end($feld_ar);
		$this->endwert = $endwert;
		// Feldtyp f�r die id aus der Datenbank holen
		$sql = sprintf("SELECT mvcform_type
								FROM %s
								WHERE mvcform_id = '%d'
								LIMIT 1",
								
								$this->cms->tbname['papoo_mvcform'],
								
								$this->db->escape($endwert)
						);
		$feldtyp = $this->db->get_var($sql);
		if ($feldtyp == "select"
			|| $feldtyp == "radio"
			|| $feldtyp == "multiselect"
			|| $feldtyp == "check"
			|| $feldtyp == "pre_select")
		{
			if (is_numeric($endwert)) $drin = $this->cms->tbname[$tabelle]
												. "_lang_"
												. $endwert; // Tabellenamen erzeugen
		}
		return $drin;
	}

	function make_array_convert_to_text()
	{
		$datax=$this->checked->tabelle;
		$mv_id = @end(@explode("_", $datax));
		// Die zu dieser mv_id, meta_id passenden Feldnamen holen (nur aktive Felder)
		$sql = sprintf("SELECT mvcform_name,
								mvcform_id,
								mvcform_type
								FROM %s 
								WHERE mvcform_form_id = '%d'
								AND mvcform_meta_id = '%d'
								AND mvcform_aktiv = 1
								AND (mvcform_type = 'select' 
									OR mvcform_type = 'multiselect' 
									OR mvcform_type = 'radio' 
									OR mvcform_type = 'check'
									OR mvcform_type = 'pre_select')",
									
								$this->cms->tbname['papoo_mvcform'],
								
								$this->db->escape($mv_id),
								$this->db->escape($this->checked->metaebene)
						);
		$felder = $this->db->get_results($sql, ARRAY_A);
		global $db_praefix;
		$tab = $db_praefix . $this->checked->tabelle;
		if (count($felder))
		{
			// Tabelle mit den ben�tigten Daten aufbauen (Feldname: lookup_id, content)
			foreach ($felder AS $key => $value)
			{
				$sql = sprintf("SELECT lookup_id,
										content,
										'mvcform_type=%s'
										FROM %s 
										WHERE lang_id = '%d'",
										$value['mvcform_type'],
										
										$tab
										. "_lang_"
										. $value['mvcform_id'],
										
										$this->db->escape($this->checked->tabelle_lang)
								);
				$feld_inhalte = $this->db->get_results($sql, ARRAY_A);
				$array_convert_to_text[$value['mvcform_name'] . "_" . $value['mvcform_id']] = $feld_inhalte;
			}
		}
		return $array_convert_to_text;
	}
	
	/**
	 * Lookupwert eines Eintrages rausholen und im Zweifel neu anlegen
	 */
	function get_wert_lookup($tab, $eintrag)
	{
		$eintrag = $this->db->escape(trim($eintrag));
		if ($eintrag == "") return("");
		$feld = array(); // Feld id
		$feld = explode("_", $tab);
		$feld_id = end($feld);
		// Tabellenname erstellen und �bergeben
		$tabelle = $this->checked->tabelle;
		// Hole die Max Id aus dieser Tabelle
		$sql = sprintf("SELECT MAX(mv_content_id)
								FROM %s", 
								$this->cms->tbname[$tabelle . "_search_1"]
						);
		$max_mv_content_id = $this->db->get_var($sql);
		// holt alle Sprachen aus der Tabelle
		$sql = sprintf("SELECT * 
								FROM %s", 
								$this->cms->tbname['papoo_mv_name_language']
						);
		$sprachen = $this->db->get_results($sql, ARRAY_A);
		// gibt es noch keinen Max Wert? (Tabelle ist leer)
		if (empty($max_mv_content_id))
		{
			foreach($sprachen as $sprache)
			{
				$max_mv_content_id = 0;
				$sql = sprintf("ALTER TABLE %s
										AUTO_INCREMENT = 1", 
										$this->cms->tbname[$tabelle
										. "_search_"
										. $this->db->escape($sprache['mv_lang_id'])]
								);
				$this->db->query($sql);
			}
		}
		// was f�r ein Feldtyp ist es
		$sql = sprintf("SELECT mvcform_type
								FROM %s 
								WHERE mvcform_id = '%d'
								AND mvcform_meta_id = '%d'
								LIMIT 1",
								
								$this->cms->tbname['papoo_mvcform'],
								
								$this->db->escape($feld_id),
								$this->db->escape($this->checked->metaebene)
						);
		$feld_type = $this->db->get_var($sql);
		// bei diesen Typen keine Lookupwert, ansonsten Lookupwerte...
		if ($feld_type != "password"
			&& $feld_type != "email"
			&& $feld_type != "timestamp"
			&& $feld_type != "zeitintervall") 
		{
			// hier also text, textarea, textarea_tiny, preisintervall, galerie, picture, file, link, multiselect, select, pre_select, check, radio
			// nach eventuell schon vorhandenen Eintr�gen und deren lookup_id suchen
			if ($feld_type == "multiselect"
				|| $feld_type == "select"
				|| $feld_type == "check"
				|| $feld_type == "radio"
				|| $feld_type == "pre_select")
			{
				$tab_lang = $this->cms->tbname[$tabelle . "_lang_" . $this->db->escape($feld_id)];
				// Wert rausbekommen aus mv_content_x_lang_y
				$sql = sprintf("SELECT lookup_id
										FROM %s 
										WHERE content = '%s' 
										AND lang_id = '%d'",
										
										$tab_lang,
										
										$eintrag,
										$this->db->escape($this->cms->lang_id)
							);
				$res_id = $this->db->get_var($sql); //Lookup Wert
			}
			if ($feld_type != "")
			{
				// mv_content_id Max + 1 holen
				$sql = sprintf("SELECT MAX(mv_content_id)
										FROM %s", 
										$this->cms->tbname[$tabelle
										. "_search_1"]
								);
				$max_mv_content_id = $this->db->get_var($sql);
				// ist wohl der erste Eintrag. Damit die laufenden ids auch stimmen,
				// muss nach dem mv_content_mv_id Eintrag die id mochmal in der lang und Lookup Tabelle korrigiert werden
				if ($max_mv_content_id == 0 )
				{
					$this->first_entry = "ja";
					#$this->tab_lookup = $tab_lookup;
					$this->tab_lang = $tab_lang;
				}
				$max_mv_content_id++;
				// Lookup Tabelle bef�llen
				$sql = sprintf("INSERT INTO %s SET content_id = '%d', 
													lookup_id = '%d'",
													$tab_lookup,
													$this->db->escape($max_mv_content_id),
													$this->db->escape($max_id)
							);
				#$this->db->query($sql);
			}
			if (empty($res_id)
				&& $feld_type != "")
			{
				// Wenn leer, dann neu eintragen
				$sql = sprintf("SELECT MAX(lookup_id)
										FROM %s", 
										$tab_lang
								);
				$max_id = $this->db->get_var($sql);
				$max_id++;
				$no = "yes";
				// 61 = Vorlage
				if ($feld_id == 61
					&& $max_id >= 4) $no = "no";
				// Nur eine bestimmte Sprache?
				#if ($this->checked->mv_import_sprachen != "all")
				#{
				#	$sprachen[0]['mv_lang_id'] = $this->checked->mv_import_sprachen;
				#	$max_id = ;
				#}
				#else
				#{			
					// holt alle Sprachen aus der Tabelle
					#$sql = sprintf("SELECT * 
					#						FROM %s", 
					#						$this->cms->tbname['papoo_mv_name_language']
					#				);
					#$sprachen = $this->db->get_results($sql, ARRAY_A);
				#}
				if (!empty($sprachen)
					&& $no == "yes")
				{
  					foreach($sprachen as $sprache)
  					{
              			// Lang Tabellen bef�llen	
						$sql = sprintf("INSERT INTO %s SET content = '%s', 
														lang_id = '%d', 
														lookup_id = '%d'",
														
														$tab_lang,
														
														$eintrag,
														$this->db->escape($sprache['mv_lang_id']),
														$this->db->escape($max_id)
									);		
						$this->db->query($sql);
          			}  
        		}		
				// Neuer Wert
				$res_id = $max_id;
			}		
		}
		// bei email, password, timestamp und zeitintervall den Wert direkt in die Content Tabelle ohne Lookup
		else $res_id = $eintrag;
		// Wert zur�ckgeben zum Eintragen in die content Tabelle
		return $res_id;
	}
	
	/**
	 * anhand des Namens der Tabelle rausfinden, ob es sich um eine MV Mitgliederverwaltung handelt
	 */
	function is_mv_mitglieder()
	{
		// Wenn eine MV Tabell da ist = MV ist installiert
		if ($this->cms->tbname['papoo_mv'])
		{
			$this->make_tab_name_from_feld($this->checked->tabelle);
			$id = $this->endwert;
			// mv_id �bergeben
			$this->mv_id = $id;
			$sql = sprintf("SELECT mv_art
									FROM %s 
									WHERE mv_id = '%d'
									LIMIT 1",
									
									$this->cms->tbname['papoo_mv'],
									
									$this->db->escape($id)
							);
			$res = $this->db->get_var($sql);
			$sql = sprintf("SELECT mv_set_group_id
									FROM %s 
									WHERE mv_id = '%d'
									LIMIT 1",
									
									$this->cms->tbname['papoo_mv'],
									
									$this->db->escape($id)
							);
			$this->mv_groud_id = $this->db->get_var($sql);
		}
		// Mitgliederverwaltung, dann ok
		if ($res == 2) return true; // Mitgleiderverwaltung, true zur�ckgeben
		// Keine MV
		return false;
	}
	
	/**
	 * Daten in die Datenbank eintragen
	 */
	function insert_into_database($cvs_ar = "")
	{
		global $db_praefix;
		// mv_id aus dem Tabellennamen extrahieren
		$tabellen_array = explode("_", $this->checked->tabelle);
		$mv_id = end($tabellen_array);
		$this->mv_id = $mv_id;
		$tabellen_alle = $this->cms->tbname;
		// Je nachdem, ob mit oder ohne Feldernamen, muss sp�ter der Z�hler um 1 erh�ht werden
		$counter_plus = $this->checked->format == "csvmit" ? 1 : 0;
		// In alle Sprachen oder nur f�r eine?
		if ($this->checked->mv_import_sprachen != "all") $sprachen = array(0 => (object)array('mv_lang_id' => $this->checked->mv_import_sprachen));
		else
		{
			// Sprachtabellen f�r die schnellere Suche
			$sql = sprintf("SELECT mv_lang_id
									FROM %s", 
									$this->cms->tbname['papoo_mv_name_language']
							);
			$sprachen = $this->db->get_results($sql);
		}
		// folgendes nur bei dzvhae Auftritt machen
		if ($this->mv->dzvhae_system_id)
		{
			// Holt die Maximale dzvhae user ID aus der Datenbank
			$sql = sprintf("SELECT MAX(mv_dzvhae_system_id)
									FROM %s",
									$this->cms->tbname['papoo_mv_dzvhae']
							);
			$max_dzvhae_system_id = $this->db->get_var($sql);		
			// wenn erste Runde, dann dzvhae System ID Spalte der mv_content Tabelle hinzuf�gen
			if ($this->checked->counter_start == "0")
			{
				// Neues Feld in die Mitglieder Content Tabelle einf�gen
				$sql = sprintf("ALTER TABLE %s
										ADD mv_dzvhae_system_id TEXT NOT NULL;",
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($mv_id)
										. "_search_1"
								);
				$this->db->query($sql);
			}
		}
		// Wenn alte Eintr�ge komplett durch neue ersetzen werden sollen, dann alte vorher l�schen
		if ($_SESSION['mv_import']['mv_imex_update_insert'] == "ins_del_neu"
				&& $this->checked->counter_start == "0")
		{
			$sql = sprintf("DELETE FROM %s 
									WHERE mv_meta_main_lp_mv_id = '%d'",
									
									$this->cms->tbname['papoo_mv_meta_main_lp'],
									
					  				$this->db->escape($mv_id)
							);
			$this->db->query($sql);
			$sql = sprintf("DELETE FROM %s 
									WHERE mv_meta_lp_mv_id = '%d'",
									
									$this->cms->tbname['papoo_mv_meta_lp'],
									
					  				$this->db->escape($mv_id)
							);
			$this->db->query($sql);
			if (!empty($sprachen))
			{
				foreach($sprachen AS $sprache)
				{
					// Alte Eintr�ge l�schen
					$sql = sprintf("DELETE FROM %s",
					
												$this->cms->tbname[$this->db->escape($this->checked->tabelle
												. "_search_"
												. $sprache->mv_lang_id)]
									);
					$this->db->query($sql);
				}
			}
			//  aber auch die Lang und Lookuptabellen Eintr�ge l�schen
			// Minimal- und Maximalwert f�r eine Feld ID in dieser Verwaltung 
			$sql = sprintf("SELECT MIN(mvcform_id) min,
									MAX(mvcform_id) max
										FROM %s 
										WHERE mvcform_form_id = '%d'
										AND mvcform_meta_id = '%d'",
										
										$this->cms->tbname['papoo_mvcform'],
										
										$this->db->escape($mv_id),
										$this->db->escape($this->checked->metaebene)
							);
			$result = $this->db->get_results($sql, ARRAY_A);
			$min_feld_id = $result[0]['min'];
			$max_feld_id = $result[0]['max'];
			// ++ weil in der while schleife < steht
			$max_feld_id++;
			// feld_id ist der Z�hler, und der f�ngt mit dem Minimalwert an
			$feld_id = $min_feld_id;
			// geh die Schleife durch, solange der Z�hler kleiner als der Maximalwert ist
			while($feld_id < $max_feld_id)
			{
				// existiert die Lang Tabelle f�r dieses Feld?
				if (in_array($this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($mv_id)
							. "_lang_"
							. $this->db->escape($feld_id),
							$tabellen_alle))
				{
// dann l�sch alle Eintr�ge AUWEIA..... ungeachtet der Vorlage und ob hinterher �berhaupt wieder oder weniger reinkommt...
					#if ($this->checked->mv_import_sprachen != "all") $where_del = " WHERE lang_id = " . $this->checked->mv_import_sprachen;
					$sql = sprintf("DELETE FROM %s %s",
					
												$this->cms->tbname['papoo_mv']
												. "_content_"
												. $this->db->escape($mv_id)
												. "_lang_"
												. $this->db->escape($feld_id),
												
												$where_del
									);
					$this->db->query($sql);				
				}
				// existiert die Lookup Tabelle f�r dieses Feld?
				if (in_array($this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($mv_id)
							. "_lookup_"
							. $this->db->escape($feld_id),
							$tabellen_alle))
				{
					// dann l�sch alle Eintr�ge					
					$sql = sprintf("DELETE FROM %s",								
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($mv_id)
											. "_lookup_"
											. $this->db->escape($feld_id)
								);
					#$this->db->query($sql);
				}
				// Z�hler eins hochz�hlen			
				$feld_id++;			
			}
			// Holt alle userids aus der papoo_user Tabelle, die zur Verwaltung geh�ren
			$sql = sprintf("SELECT userid
									FROM %s 
									WHERE user_mv_id = '%d'
									AND userid > 11",
									
									$this->cms->tbname['papoo_user'],
									
									$this->db->escape($mv_id)
							);
			$user_ids = $this->db->get_results($sql, ARRAY_A);
			if (!empty($user_ids))
			{
				// und l�scht die dazugeh�rigen Gruppenrechte Eintr�ge in papoo_lookup_ug
				foreach($user_ids as $key => $value)
				{
					$sql = sprintf("DELETE FROM %s 
											WHERE userid = '%d'
											AND userid > 11",
											
											$this->cms->tbname['papoo_lookup_ug'],
											
											$this->db->escape($value['userid'])
									);
					$this->db->query($sql);
				}
			}
			// Alte papoo_user Tabelleneintr�ge f�r diese Verwaltung l�schen
			$sql = sprintf("DELETE FROM %s 
									WHERE user_mv_id = '%d'
									AND userid > 11",
									
									$this->cms->tbname['papoo_user'],
									
									$this->db->escape($mv_id)
							);
			$this->db->query($sql);
			// alte �nderungsprotokoll Eintr�ge f�r diese Verwaltung l�schen
			$sql = sprintf("DELETE FROM %s 
									WHERE mv_pro_mv_id = '%d'",
									
									$this->cms->tbname['papoo_mv_protokoll'],
									
									$this->db->escape($mv_id)
							);
			$this->db->query($sql);			
			// f�r dzvhae Sonderfall Tabelleneintr�ge l�schen
			if ($this->mv->dzvhae_system_id)
			{
				$sql = sprintf("DELETE FROM %s", 
										$this->cms->tbname['papoo_mv_dzvhae']
								);
				$this->db->query($sql);
			}
			// Alte Sprachtabellen Eintr�ge l�schen
			if (!empty($sprachen))
			{
				foreach($sprachen as $sprache)
				{
					$sql = sprintf("DELETE FROM %s", 
												$this->cms->tbname['papoo_mv']
												. "_content_"
												. $this->db->escape($mv_id)
												. "_search_"
												. $this->db->escape($sprache->mv_lang_id)
									);
					$this->db->query($sql);	
				}
			}	
		}
		// Zuordnungsfelder einlesen
		$zuord = $_SESSION['mv_import']['csv_ar'];
		// Anzahl der Zuordnungsfelder
		$anz = count($zuord);
	  	// Holt die Maximale dzvhae user ID aus der Datenbank
		$sql = sprintf("SELECT MAX(mv_dzvhae_system_id)
								FROM %s",
								$this->cms->tbname['papoo_mv_dzvhae']
						);
		$max_dzvhae_system_id_temp = $this->db->get_var($sql);
		// Datenbankfelder einlesen
		$tabelle_felder = $this->lese_felder_tabelle($this->checked->tabelle);
		// wenn die Session noch leer ist
		if (empty($_SESSION['mv_import']['mv_lup']))
		{
			if (!empty($zuord))
			{
				// dann bau ein Array aus allen Feldnamen_ids zusammen
				foreach ($zuord as $key2 => $value2) { $lup[$tabelle_felder[$value2]] = $tabelle_felder[$value2]; }
			}
			$_SESSION['mv_import']['mv_lup'] = $lup;
		}
		// aus der SESSION rausholen
		$lup = $_SESSION['mv_import']['mv_lup'];
		// Rausfinden, ob es eine Mitgliederverwaltung ist
		$is_mv = $this->is_mv_mitglieder();
		// Lookupwerte f�r das Feld Zahlungen_xx f�r den Dzvh� Sonderfall
		if ($this->cms->tbname['papoo_mv_zahlungen_lookup']
			AND $this->mv->dzvhae_system_id)
		{
			// wurde schon mal etwas in einer Session zwischengespeichert?
			if (empty($_SESSION['mv_import']['mv_zahlungen_lookup']))
			{
				// nein, dann hole die Verkn�pfungen f�r dieses dzvhae Sonderfeld aus der Datenbank
				$sql = sprintf("SELECT * FROM %s",
										$this->cms->tbname['papoo_mv_zahlungen_lookup']
								);
				$zahlungen_lookup = $this->db->get_results($sql, ARRAY_A);	
				// Daten der Tabelle papoo_mv_zahlungen_lookup im array $old_school_array merken
				if (!empty($zahlungen_lookup)) foreach ($zahlungen_lookup as $zeile) { $old_school_array[$zeile['lookup_id']] = $zeile['text']; };
				// und speicher dies in der Session 
				$_SESSION['mv_import']['mv_zahlungen_lookup'] = $old_school_array; 			
			}
			else $old_school_array = $_SESSION['mv_import']['mv_zahlungen_lookup']; // wenn Session da ist, dann schreib den Sessionwert ins old_school_array :)
		}
		// Return initialisieren
		$datenfertig= "";
    	// Wenn Eintr�ge vorhanden sind
		if (!empty($cvs_ar))
		{
			$counter = 0;
			foreach ($cvs_ar as $key => $value)
			{
				#print_r($value);
				#exit("XX");
				// f�r dzvhae Sonderfall durchgehen und leere Benutzernamen Felder mit nachname_systemID bef�llen
				if ($this->mv->dzvhae_system_id)
				{
					$this->insert_username = "";
					$value[$this->mv->dzvhae_feld_benutzername_id] = trim($value[$this->mv->dzvhae_feld_benutzername_id]);					
					// wenn Benutzername leer
					if (empty($value[$this->mv->dzvhae_feld_benutzername_id]))
					{
						if (empty($value[$this->mv->dzvhae_feld_system_id_id]))
						{
              				$max_dzvhae_system_id_temp++;
              				$value[$this->mv->dzvhae_feld_system_id_id] = $max_dzvhae_system_id_temp;
            			}
            			// dann mach einen neuen aus Nachname und alter dzvhae SystemID
  						$value[$this->mv->dzvhae_feld_benutzername_id] = $value[$this->mv->dzvhae_feld_nachname_id] . "_" . $value[$this->mv->dzvhae_feld_system_id_id];
  						$this->zufall_name = $value[$this->mv->dzvhae_feld_benutzername_id];
        			}	
				}
				//print_r($counter);
				if ($counter < ($this->max_counter_pro_runde + $this->counter_minus_eins))
				{
					$insert = "";
					$insert_rows = "";
					$i = 1;
					// Wenn eine Zuordnung per Vorlage vorhanden ist...
					if (!empty($zuord))
					{
						// ...dann diese durchlaufen
						foreach ($zuord as $key2 => $value2)
						{
							//print_r($value2);
							// nur bei alter Flex einschalten !!! (oder wann immer das n�tig sein sollte...)
							#$value[$key2] = utf8_encode($value[$key2]);
							$insert_value_search_tab = $value[$key2]; // f�r Sprachtabelle Normalfall
							$insert_id = null; // Insertid auf 0 setzen
							// Zu dem Feld existiert eine Lookup Tabelle
							if (!empty($lup))
							{
								// Wert rausholen, wenn noch nicht existiert eintragen und dann Wert rausholen
								$feldname_lu = $tabelle_felder[$value2];
								if (!empty($lup[$feldname_lu]))
								{
									// Schreibt die Feld ID in $this->endwert
									$this->make_tab_name_from_feld($lup[$feldname_lu]);
									// wenns timestamp ist, dann Wert bearbeiten
									$sql = sprintf("SELECT mvcform_type
															FROM %s 
															WHERE mvcform_id = '%d'
															AND mvcform_meta_id = '%d'
															LIMIT 1",
															
															$this->cms->tbname['papoo_mvcform'],
															
															$this->db->escape($this->endwert),
															$this->db->escape($this->checked->metaebene)
													);
									$feld_type = $this->db->get_var($sql);
									// wenn es keine Textarea ist, dann auch alle \n \r raus, bzw. ###n### und ###r### (beim Import der Datei f�r Zeilenumbruch ersetzt))
									if ($feld_type != "textarea")
									{
										$value[$key2] = preg_replace("/###n###/", "", $value[$key2]);
										$value[$key2] = preg_replace("/###r###/", "", $value[$key2]);									
									}
									else
									{
										$value[$key2] = preg_replace("/###n###/", "\n", $value[$key2]);
										$value[$key2] = preg_replace("/###r###/", "\n", $value[$key2]);										
									}
									// Multiselect: Importselectwerte sind durch Komma getrennt 
									if ($feld_type == "multiselect")
									{
										$lookup_wert_buffer = "";
										$buffer = explode(",", $value[$key2]); // Zahlenwerte in $buffer
										if (!empty($buffer)
											&& !empty($buffer['0'])) // mindestens 1 Wert
										{
											// buffer contains each multiselect value
											foreach($buffer as $buff)
											{
												// Text to numeric lookup value, falls ein Wert vorgegeben ist, sonst 0
												if (empty($buff)) $lookup_wert_buffer = 0;
												else $lookup_wert_buffer = $this->get_wert_lookup($lup[$feldname_lu], $buff);
												$lookup_wert .= $lookup_wert_buffer . "\n##multiselect##"; // numWert\n##multiselect##
												$insert_value_search_tab = $lookup_wert;
											}
										}
										else $lookup_wert = $insert_value_search_tab = "0\n";
									}
									if ($feld_type == "select"
										|| $feld_type == "radio"
										|| $feld_type == "pre_select")
									{
										if ($lookup_wert == "")
										{
											// Wert des Eintrags aus der Lookup tabelle
											if (empty($value[$key2]))
											{
												$lookup_wert = $insert_value_search_tab = 0;
												$this->get_wert_lookup($lup[$feldname_lu], $value[$key2]);
											}
											else $insert_value_search_tab = $lookup_wert = $this->get_wert_lookup($lup[$feldname_lu], $value[$key2]);
										}
										else $insert_value_search_tab = $lookup_wert = $this->get_wert_lookup($lup[$feldname_lu], $value[$key2]);
									}
									if ($feld_type == "check")
									{
										//Wert des Eintrags aus der Lookup tabelle holen
										if ($lookup_wert == "") // ist immer "" !!!
										{
											if (empty($value[$key2])) $insert_value_search_tab = $lookup_wert = 0;
											else $insert_value_search_tab = $lookup_wert = $this->get_wert_lookup($lup[$feldname_lu], $value[$key2]);
										}
										else $insert_value_search_tab = $this->get_wert_lookup($lup[$feldname_lu], $value[$key2]);
									}
									$protokoll_buffer = $value[$key2];
									if ($lookup_wert 
										OR $lookup_wert == "0")
									{
										// Wert �bergeben
										$value[$key2] = $lookup_wert;
										// Lookup Werte f�r Lookup Tabellen
										#$look_up_array[$lup[$feldname_lu]] = $lookup_wert;
									}
									$lookup_wert = "";
								}
							}
							$value[$key2] = trim($value[$key2]);
							// Insert aus Feld und Inhalt zusammensetzen
							$insert_array[$tabelle_felder[$value2]] = $value[$key2];
							$insert .= $this->db->escape($tabelle_felder[$value2]) . "='" . $this->db->escape($value[$key2]) . "'";
							// wenn aktiv Feld, dann den Wert zwischenspeichern f�r Sperre
              				if ($tabelle_felder[$value2] == "active_7") $sperre_wert_import = $value[$key2];
							if ($tabelle_felder[$value2] == "mv_content_id") $own_mv_content_id = $value[$key2];
							$systemid_arr = explode("_", $tabelle_felder[$value2]);
							if ($systemid_arr[0] == "SystemID"
								AND $value[$key2]) $where_system_id = $tabelle_felder[$value2] . "='" . $value[$key2] . "'";
							// dzvhae Sonderfall: f�rs Protokoll bestimmte Werte zwischenspeichern
							if ($tabelle_felder[$value2] == $this->dzvhae_feld_flex_systemid
								|| $tabelle_felder[$value2] == $this->dzvhae_feld_flex_nachname 
								|| $tabelle_felder[$value2] == $this->dzvhae_feld_flex_vorname
								|| $tabelle_felder[$value2] == "Benutzername_1"
								|| $tabelle_felder[$value2] == "passwort_2")
									$_SESSION['mv_import']['mv_import_protokoll'][$this->counter_start + $counter][$tabelle_felder[$value2]] = $protokoll_buffer;
							// f�r die Sprachtabellen den Eintrag zwischenspeichern
							$insert_rows .= $this->db->escape($tabelle_felder[$value2]) 
											. "='" 
											. $this->db->escape($insert_value_search_tab) 
											. "', ";
							$insert_value_search_tab = "";							
							if ($i < $anz) $insert .= ", ";
							$i++;
						}
						// letztes ", " wieder l�schen
						$insert_rows = substr($insert_rows, 0 , -2);
						#print_r($insert);
						#print_r($results);
						// Nur Insert machen, wenn auch neue eingetragen werden sollen
						if ($_SESSION['mv_import']['mv_imex_update_insert'] == "ins_del_neu"
							|| $_SESSION['mv_import']['mv_imex_update_insert'] == "ins_neu")
						{
							// F�r Mitgliederverwaltung die Usertabelle updaten
							if ($is_mv == true)
							{
                				// In papoo_user eintragen
								$userinsertid = $this->insert_update_user("insert", $insert_array, $insert_id, $_SESSION['mv_import']['mv_imex_update_insert']);
								if (!stristr($insert_rows, ", mv_content_userid='")) // Import-Zuordnung hat Priorit�t, sonst SQL-error "specified twice"
								{
									$value[$this->mv->dzvhae_feld_benutzername_id] = $this->insert_username;
									$insert .= ", mv_content_userid='" . $this->db->escape($userinsertid) . "'";
									$insert_rows .= ", mv_content_userid='" . $this->db->escape($userinsertid) . "'";
								}
								if (stristr($insert_rows, "Benutzername_1=''"))
								{
									$insert = str_ireplace("Benutzername_1=''", "Benutzername_1='" . $this->insert_username . "'", $insert);
									$insert_rows = str_ireplace("Benutzername_1=''", "Benutzername_1='" . $this->insert_username . "'", $insert_rows);
								}
								// dzvhae Sonderfall
								if ($this->mv->dzvhae_system_id)
								{
									// wenn noch kein Feld Benutzername_1 vorhanden ist im SQL-String, sonst SQL-error "specified twice"
									if (!stristr($insert_rows, "Benutzername_1=")) 
									{
										$insert .= ", Benutzername_1='" . $this->insert_username . "'";
										$insert_rows .= ", Benutzername_1='" . $this->insert_username . "'";
									}
									$mv_dzvhae_sql = "";
									// Nur, wenn eine ausgew�hlt ist
									if ($this->checked->mv_dzvhae_system_id
										AND !stristr($insert_rows, ", mv_dzvhae_system_id='")) // Import-Zuordnung hat Priorit�t, sonst SQL-error "specified twice"
									{
										// $counter_plus ist gleich + 1, wenn die Importdatei Feldernamen hat ($counter + $counter_plus)
										$mv_dzvhae_sql = ", mv_dzvhae_system_id='"
															. $this->db->escape($cvs_ar[($counter + $counter_plus)][$_SESSION['mv_import']['mv_dzvhae_system_id']])
															. "'";
									}
									if ($is_mv == true
										AND !stristr($insert_rows, ", mv_content_sperre='")) // Import-Zuordnung hat Priorit�t, sonst SQL-error "specified twice"
									{
										if ($sperre_wert_import == "1") $sperre_wert = "0";
										else $sperre_wert= "1";
										$mv_dzvhae_sperre = ", mv_content_sperre='" . $this->db->escape($sperre_wert). "'";
										$mv_dzvhae_sql .= $mv_dzvhae_sperre;
									} 
									$sperre_wert_import = "0";   
								}
								elseif (!stristr($insert_rows, ", mv_content_sperre='")) // Import-Zuordnung hat Priorit�t, sonst SQL-error "specified twice"
								{
									$insert .= ", mv_content_sperre='0'";
									$insert_rows .= ", mv_content_sperre='0'";
								}
								if ($this->insert_username
									AND (!stristr($insert_rows, ", mv_content_create_owner='")
										OR !stristr($insert_rows, ", Benutzername_1='"))) // Import-Zuordnung hat Priorit�t, sonst SQL-error "specified twice"
								{
									$where = "mv_content_create_owner='"
												. $this->insert_username
												. "' OR Benutzername_1='"
												. $this->insert_username
												. "'";
								}
								if ($where
									AND $where_system_id) $where .= " OR ";
								if ($where_system_id) $where .= $where_system_id;
								if ($where)
								{
									$sql = sprintf("SELECT * FROM %s
																	WHERE %s",
																	$this->db->escape($db_praefix
																	. $this->checked->tabelle
																	. "_search_1"),
																	$where
													);
									$results = $this->db->get_results($sql);
									$where = "";
								}
							}

							if (empty($results))
							{
								$insert = str_replace('\n##multiselect##', '\n', $insert);
								#print_r($insert);
								#print_r("HIER");
								// Daten in die Datenbank (content_x) schreiben
								$sql = sprintf("INSERT IGNORE INTO %s SET %s %s",
																	$this->db->escape($db_praefix . $this->checked->tabelle),
																	$insert,
																	$mv_dzvhae_sql
												);
								// Zeilenumbr�che wieder rein
								$sql = str_replace('###n###', "\n", $sql);
								$sql = str_replace('###r###', "\r", $sql);
								#$this->db->query($sql);
								#print_r($sql);
								#exit("RAUS");
								#
								$counter++; // Z�hler f�r die dzvhae System ID hochz�hlen
								// Daten in die Sprachtabellen eintragen
								if (!empty($sprachen))
								{
									foreach($sprachen as $sprache)
									{
										// Importwert hat Vorrang, sonst falscher Wert oder SQL-Error f�r papoo_mv_meta_main_lp/papoo_mv_meta_lp
										if (!$own_mv_content_id)
										{
											// die aktuelle Max mv_content_id holen, damit in der Sprachtabelle diese dann mit eingesetzt werden kann
											$sql_test = sprintf("SELECT MAX(mv_content_id)
												FROM %s",

												$this->cms->tbname['papoo_mv']
												. "_content_"
												. $this->db->escape($mv_id)
												. "_search_"
												. $this->db->escape($sprache->mv_lang_id)
											);
											$this->mv_content_id = $mv_content_id = ($this->db->get_var($sql_test) + 1);
											// mv_content_id nur hier, wird bei content_x durch Mysql verwaltet (auto_increment)
											$insert_rows .= ", mv_content_id='" . $this->db->escape($this->mv_content_id) . "'";
										}
										else
										{
											$this->mv_content_id = $own_mv_content_id;
											$own_mv_content_id = 0;
										}
										// fuer alle Sprachen den Eintrag durchf�hren (content_x_search_y)
										$insert_rows = str_replace('\n##multiselect##', '\n', $insert_rows);
										#$first_iteration = 1;
										//Daten in die Datenbank (content_x_search_y) schreiben
										#$sql_search = sprintf("INSERT IGNORE INTO %s SET %s %s",
										$sql_search = sprintf("INSERT INTO %s SET %s %s",

											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($mv_id)
											. "_search_"
											. $this->db->escape($sprache->mv_lang_id),
																					
																					$insert_rows,
																					$mv_dzvhae_sperre
																);
										// Zeilenumbr�che wieder rein
										$sql_search = str_replace('###n###', "\n", $sql_search);
										$sql_search = str_replace('###r###', "\r", $sql_search);
										$this->db->query($sql_search);
										#if ($first_iteration)
										#{
										#	$first_iteration = 0;
										#	= $this->db->insert_id;
										#}
									}
								}
								// Mainmetaebene hinzuf�gen
								$sql = sprintf("INSERT IGNORE INTO %s SET mv_meta_main_lp_user_id = '%d',
																	mv_meta_main_lp_meta_id = '%d',
																	mv_meta_main_lp_mv_id = '%d'",
																	
																	$this->cms->tbname['papoo_mv_meta_main_lp'],
																	
																	$this->db->escape($this->mv_content_id),
																	$this->db->escape($this->checked->metaebene),
																	$this->db->escape($mv_id)
												);
								$this->db->query($sql);
								// Hauptmetaeben auch unter sonstige Metaebenen eintragen
								$sql = sprintf("INSERT IGNORE INTO %s SET mv_meta_lp_user_id = '%d',
																	mv_meta_lp_meta_id = '%d',
																	mv_meta_lp_mv_id = '%d'",
																	
																	$this->cms->tbname['papoo_mv_meta_lp'],
																	
																	$this->db->escape($this->mv_content_id),
																	$this->db->escape($this->checked->metaebene),
																	$this->db->escape($mv_id)
												);
								$this->db->query($sql);
								if (!empty($_SESSION['mv_import']['add_metaebenen']))
								{
									foreach($_SESSION['mv_import']['add_metaebenen'] as $add_metaebene)
									{
										if ($this->checked->metaebene != $add_metaebene) // falls doppelt angegeben (Haupt- und sonst. Metaebenen)
										{
											$sql = sprintf("INSERT IGNORE INTO %s SET mv_meta_lp_user_id = '%d',
																				mv_meta_lp_meta_id = '%d',
																				mv_meta_lp_mv_id = '%d'",
																				
																				$this->cms->tbname['papoo_mv_meta_lp'],
																				
																				$this->db->escape($this->mv_content_id),
																				$this->db->escape($add_metaebene),
																				$this->db->escape($mv_id)
															);
											$this->db->query($sql);
										}
									}
								}
							}
							else
							{
								$_SESSION['mv_import']['mv_import_protokoll'][$this->counter_start + $counter]['error'] = 
															'<span class="protokoll_error">nicht importiert - schon vorhanden</span>';
								$counter++;
							}
						}
						if ($this->first_entry == "ja")
						{
							$sql = sprintf("SELECT MAX(mv_content_id)
													FROM %s", 
													$this->cms->tbname['papoo_mv'] . "_content_" . $this->db->escape($mv_id)
											);
							#$max_mv_content_id = $this->db->get_var($sql);
							// Lookup Tabelle updaten
							$sql =sprintf("UPDATE %s SET content_id = '%d' 
														WHERE content_id = '1'",
														
														$this->tab_lookup,
														
														$this->db->escape($max_mv_content_id)
											);
							#$this->db->query($sql);		
							$this->first_entry = "nein";		
						}
						$datenfertig .= $sql . "; ##b_dump## \n";
						#$this->insert_look_up_werte($look_up_array, $this->mv_content_id); // Wert in Lookup eintragen
					}
				}
			}
		}
		// wenn noch kein Ende der Datei erreicht wurde, dann reload und n�chstes P�ckchen verarbeiten
		if ($this->ende_der_datei != "ja")
		{
			// die Seite erneut aufrufen und Nummer des letzten Elements �bergeben
			$self = $_SERVER['PHP_SELF']
					. "?menuid="
					. $this->checked->menuid
					. "&makeimport=Import starten&counter_start="
					. ($this->counter_start + $this->max_counter_pro_runde)
					. "&metaebene="
					. $this->checked->metaebene
					. "&format="
					. $this->checked->format
					. "&mv_id="
					. $this->checked->mv_id
					. "&ins="
					. $this->checked->ins
					. "&tabelle="
					. $this->checked->tabelle
					. "&template="
					. $this->checked->template
					. "&mv_import_sprachen="
					. $this->checked->mv_import_sprachen;
			if (empty($_SESSION['debug_stopallredirect'])) header('REFRESH: 0; URL=' . $self);
			echo "Import l&auml;uft. Je nach Gr&ouml;sse der Importdatei kann der Import mehrere Minuten dauern...<br /><br />";
			echo ($this->counter_start + $this->max_counter_pro_runde)
					. " von "
					. $_SESSION['mv_import']['import_anz_zeilen']
					. " Datens&auml;tzen eingelesen.<br />\n<br />\n";
			echo '<a href= "' . $self . '">Weiter</a><br />';
			exit;							
		}
		// Importprotokoll ausgeben
		else
		{
			// dzvhae System ID Sonderfall
			// system id aus der content Tabelle l�schen und in einer extra Tabelle zwischenspeichern, 
			// damit ess bei den ganzen Felder Loops keine Probs mit diesem Sonderfeld gibt
			if ($this->mv->dzvhae_system_id)
			{
				// Hole alle mv_content_id und system id aus der Datenbank
				$sql = sprintf("SELECT mv_content_id,
										mv_dzvhae_system_id
										FROM %s",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($mv_id)
										. "_search_1"
								);
				$dzvhae_system_id = $this->db->get_results($sql, ARRAY_A);
				// gehe die Treffer durch
				if (!empty($dzvhae_system_id))
				{
					// baue aus Ihnen ein Sql Statement zusammen
					$values_sys_id = "";
					foreach($dzvhae_system_id as $sys_id)
					{
						if ($sys_id['mv_dzvhae_system_id'] !=  "")
							$values_sys_id .= "("
												. $this->db->escape($sys_id['mv_dzvhae_system_id'])
												. ", "
												. $this->db->escape($sys_id['mv_content_id'])
												. "), ";
					}
					$values_sys_id = substr($values_sys_id, 0 , -2);
				}
				// die Trefferpaare in die Sonder Tabelle dzvhae einf�gen
				if (!empty($values_sys_id))
				{
					//$this->db->hide_errors();
					$sql = sprintf("INSERT INTO %s (mv_dzvhae_system_id,
													mv_content_id)
													VALUES %s",
													
													$this->cms->tbname['papoo_mv_dzvhae'],
													
													$values_sys_id
									);
					$this->db->query($sql);	
				}		
				// Spalte f�r dzvhae system id in der content Tabelle l�schen
				$sql = sprintf("ALTER TABLE %s DROP mv_dzvhae_system_id",
				
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($mv_id)
											. "_search_1"
								);
				$this->db->query($sql);			
			}
			// Importlink ans Template
			$this->content->template['mv_protokoll_link'] = $_SERVER['PHP_SELF']
															. "?menuid= "
															. $this->checked->menuid
															. "&template=mv/templates/mv_import_protokoll.html";
// TODO; raus damit, nur 4-stellig erlaubt
			// Jahreszahlen Information ans Template weitergeben, wenn es nur 2 Ziffern waren
			if ($this->mv->import_timestamp != "0")
				$this->content->template['mv_jahreszahl_ziffern'] = $this->content->template['plugin']['mv']['jahreszahl_ziffern_1']
																	. $this->mv->import_timestamp
																	. $this->content->template['plugin']['mv']['jahreszahl_ziffern_2'];
		}
		return $datenfertig;
	}

	/**
	 * Daten in der User Tabelle updaten
	 * Hier wird dann dynamisch das Statement zusammengesezt, um einen Eintrag in der User Tabelle und
	 * in der Lookup_ug und weiteren Tabellen zu machen
	 */
	function insert_update_user($modus = "", $insert_array = array(), $insert_id = "", $insup = "")
	{
		$i = 0;
		$bnok = "";
		foreach ($insert_array as $key => $value)
		{
			// dzvh� Sonderfall: wenn Benutzername leer ist, dann Nachname_xx_SystemID_xx
			if (stristr( $key, "Benutzername_")
				&&empty($value))
			{
				if (!empty($insert_array[$this->mv->dzvhae_feld_flex_nachname]))
				{
					$sql_text .= "username='"
									. $insert_array[$this->mv->dzvhae_feld_flex_nachname]
									. "_"
									. $insert_array[$this->mv->dzvhae_feld_flex_systemid]
									. "', ";
					$this->insert_username = $insert_array[$this->mv->dzvhae_feld_flex_nachname] 
									. "_" 
									. $insert_array[$this->mv->dzvhae_feld_flex_systemid];
				}
				else
				{
					$rand = md5(rand(0, time()));
					$sql_text .= "username='" 
								. $rand 
								. "', ";
					$this->insert_username = $rand;
				}
				$bnok = "ok";
				$i++;
			}
			if (!empty($value))
			{
				$ok = "";
				if (stristr( $key,"Benutzername_"))
				{
					$sql_text .= "username='" 
								. $this->db->escape($value) 
								. "', ";
					$this->insert_username = $this->db->escape($value);
					$i++;
					$bnok = "ok";
				}
				if (stristr( $key,"email_"))
				{
					$sql_text .= "email='" 
								. $this->db->escape($value) 
								. "'";
					$ok = "ok";
					$mail_value = $value;
				}
				if (stristr( $key,"passwort_"))
				{
					$sql_text .= "password='" . $this->db->escape($value) . "'";
					$ok = "ok";
					$pok = "ok";
					$i++;
				}
				if (stristr( $key,"board_"))
				{
					$sql_text .= "board='" . $this->db->escape($value) . "'";
					$ok = "ok";
				}
				if (stristr( $key,"signatur_"))
				{
					$sql_text .= "signatur_html='" . $this->db->escape($value) . "', ";
					$sql_text .= "signatur='" . strip_tags($this->db->escape($value)) . "'";
					$ok = "ok";
				}
				if (stristr( $key,"active_"))
				{
					$sql_text .= "active='1'";
					$ok = "ok";
				}
				if (stristr( $key,"newsletter_"))
				{
					$sql_text .= "user_newsletter='" . $this->db->escape($value) . "'";
					$ok = "ok";
				}
				if (stristr( $key,"mv_content_id"))
				{
					$sql_text .= "userid='" . $this->db->escape($value) . "'";
					$ok = "ok";
					$alt_userid;
				}
				if ($ok == "ok") $sql_text.= ", ";
			}
		}
		// Kein Passwort gesetzt
		if (empty($pok))
		{
			$sql_text .= "password='" . md5(rand(0, time())) . "', ";
			$i++;
		}
		if (empty($bnok))
		{
			if (!empty($this->zufall_name))
			{
					$sql_text .= "username='" 
								. $this->db->escape($this->zufall_name) 
								. "', ";
					$this->insert_username = $this->db->escape($this->zufall_name);
					$i++;
			}
			else
			{
				if (!empty($mail_value))
				{
					$sql_text .= "username='" 
								. $this->db->escape($mail_value) 
								. "', ";
					$this->insert_username = $mail_value;
					$i++;
				}
				else
				{
					$randvalue = md5(rand(0, time()));
					$sql_text .= "username='" . $randvalue . "', ";
					$this->insert_username = $randvalue;
					$i++;
				}
			}
		}
		$sql_text .= "zeitsperre='0', ";
		$sql_text .= "confirm_code='" . md5(rand(0, time())) . "', ";
		$sql_text .= "user_mv_id='" . $this->db->escape($this->mv_id) . "', ";
		$sql_text .= "user_fax='0'";
		// Alte User l�schen
		if ($insup ==  "ins_del_neu"
			&& $i >= 2)
		{
			if (is_numeric($insert_id))
			{
				// Eintrag in der Usertabelle l�schen
				$sql = sprintf("DELETE FROM %s 
										WHERE userid = '%d'
										AND userid > 11",
										
										$this->cms->tbname['papoo_user'],
										
										$this->db->escape($insert_id)
							);
				$this->db->query($sql);
				// Eintrag in der Rechte Tabelle l�schen
				$sql = sprintf("DELETE FROM %s 
										WHERE userid = '%d'
										AND userid > 11",
										
										$this->cms->tbname['papoo_lookup_ug'],
										
										$this->db->escape($insert_id)
							);
				$this->db->query($sql);
			}
		}
		// User eintragen
		if (($insup == "ins_del_neu"
			&& $i >= 2)
				|| ($insup == "ins_neu" 
				&& $i >= 2))
		{
			$sql = sprintf("SELECT username,
									userid
									FROM %s 
									WHERE username = '%s'
									AND userid > 11",
									
									$this->cms->tbname['papoo_user'],
									
									$this->db->escape($this->insert_username)
							);
			$username = $this->db->get_results($sql, ARRAY_A);
			if (!count($username))
			{
				$sql = sprintf("INSERT INTO %s SET %s ",
				
												$this->cms->tbname['papoo_user'],
												
												$sql_text
							);
				$this->db->query($sql);
				$insert_id = $this->db->insert_id;
				if (!empty($_SESSION['mv_import']['mv_rechtegruppen']))
				{
					foreach($_SESSION['mv_import']['mv_rechtegruppen'] as $papoo_rechtegruppe)
					{
						$sql = sprintf("INSERT IGNORE INTO %s SET userid = '%d', 
															gruppenid = '%d'",
															
															$this->cms->tbname['papoo_lookup_ug'],
															
															$this->db->escape($insert_id),
															$this->db->escape($papoo_rechtegruppe)
										);
						$this->db->query($sql);					
					}
				}
			}
			else $insert_id = $username[0]['userid'];
		}
		// Bestehenden User updaten
		if ($insup == "ins_upd")
		{
			$sql = sprintf("UPDATE %s SET %s 
										WHERE userid = '%d'",
										
										$this->cms->tbname['papoo_user'],
										
										$sql_text,
										$this->db->escape($insert_id)
						);
			$this->db->query($sql);
			// Eintrag in der lookup Tabelle l�schen
			$sql = sprintf("DELETE FROM %s 
									WHERE userid = '%d'
									AND userid > 11",
									
									$this->cms->tbname['papoo_lookup_ug'],
									
									$this->db->escape($insert_id)
							);
			$this->db->query($sql);
			$sql = sprintf("INSERT INTO %s SET userid = '%d', 
												gruppenid = '%d'",
												
												$this->cms->tbname['papoo_lookup_ug'],
												
												$this->db->escape($insert_id),
												$this->db->escape($this->mv_groud_id)
						);
			$this->db->query($sql);
		}
		return $insert_id;
	}
	
	/**
	 * Lookup Werte eintragen
	 * 
	 */
	function insert_look_up_werte($lua = array(), $insert_id = "")
	{
		if (!empty($lua))
		{
			foreach ($lua as $key => $value)
			{
				// Die Id am Ende aus dem Feld holen
				$this->make_tab_name_from_feld($key);
				// wenn es eine Nummer ist, dann auch ausf�hren. quick and dirty trick f�r die 4 dzvhae-Systemfelder
				if (is_numeric($this->endwert))
				{
					// Id aus der obigen Funktion �bergeben
					$key = "papoo_mv_content_" . $this->mv_id . "_lookup_" . $this->endwert;
					$sql =sprintf("DELETE FROM %s 
											WHERE content_id = '%d'",
											
											$this->cms->tbname[$key],
											
											$this->db->escape($insert_id)
									);
					#$this->db->query($sql);
					// Wenn multiselect, dann auch alle Daten eintragen
					// \n allein reicht nicht aus. Zu unsicher und andere als multiselect haben das auch...
					$lua_array = explode("\n##multiselect##", $value);
					if (count($lua_array) >= 1)
					{
						foreach ($lua_array as $value2)
						{
							if (!empty($value2))
							{
								$sql = sprintf("INSERT INTO %s SET content_id = '%d', 
																	lookup_id = '%d'",
																	$this->cms->tbname[$key],
																	$this->db->escape($insert_id),
																	$this->db->escape($value2)
												);
								$this->db->query($sql);
							}
						}
					}
					else
					{
						$sql = sprintf("INSERT INTO %s SET content_id = '%d', 
															lookup_id = '%d'",
															$this->cms->tbname[$key],
															$this->db->escape($insert_id),
															$this->db->escape($value)
									);
						$this->db->query($sql);
					}
				}
			}		
		}
	}
	
	/**
	 * XML Daten Datenbank verwertbar machen
	 */
	function convert_to_array_xml($daten = array())
	{
		$i = 1;
		if (is_array($daten['data']['0']['data']))
		{
			foreach ($daten['data']['0']['data'] as $key => $value)
			{
				$k = 0;
				foreach ($value as $key2 => $value2)
				{
					//Die ersten beiden Eintr�ge ignorieren, die sind xml speziell
					if ($key2 != "attribute"
						and $key2 != "cdata")
					{
						$k++; // Nur echte Eintr�gen z�hlen
						$feld_ar2[$i][$k] = $value2['0']['cdata']; // Nummer und Inhalt zuweisen
					}
				}
				$i++; //Key hochz�hlen
			}
		}
		return $feld_ar2;
	}
	
	/**
	 * Aus einer Vorlage eine Session machen
	 * Damit wird die Zuordnung vorbelegt
	 */
	function make_session_from_vorlage()
	{
		$imported_to_mv = @end(@explode("_", $this->checked->tabelle)); // MV ID
		// Selektierte sonstige Metaebenen sammeln
		foreach ($this->checked->add_metaebenen AS $key => $metaebene)
		{
			$imported_to_other_meta .= $metaebene;
			if ($key < count($this->checked->add_metaebenen) - 1) $imported_to_other_meta .= ","; // letzte ohne Komma
		}
		// Selektierte Rechtegruppen sammeln
		foreach ($this->checked->mv_rechtegruppen AS $key => $group)
		{
			$selected_group_rights .= $group;
			if ($key < count($this->checked->mv_rechtegruppen) - 1) $selected_group_rights .= ","; // letzte ohne Komma
		}
		// Templatenamen holen
		$templates = $this->get_vorlagen();
		foreach ($templates AS $key => $template_data)
		{
			if ($template_data['imex_id'] == $this->checked->myvorlage)
			{
				$used_template_name = $template_data['imex_name'];
				break;
			}
		}
		// Import Fehlerprotokoll-Daten in die DB
		$sql = sprintf("INSERT INTO %s SET imported_to_mv = '%d',
											imported_to_main_meta = '%d',
											imported_to_other_meta = '%s',
											selected_group_rights = '%s',
											imported_by = '%s',
											used_file = '%s',
											used_file_type = '%s',
											used_template_id = '%d',
											used_template_name = '%s',
											used_import_type = '%s'",
											
											$this->cms->tbname['papoo_mv_imex_error_report'],
											
											$this->db->escape($imported_to_mv),
											$this->db->escape($this->checked->metaebene),
											$this->db->escape($imported_to_other_meta),
											$this->db->escape($selected_group_rights),
											$this->user->username,
											$this->db->escape($_SESSION['mv_import']['file_to_import']),
											$this->db->escape($this->checked->format),
											$this->db->escape($this->checked->myvorlage),
											$this->db->escape($used_template_name),
											$this->db->escape($this->checked->ins)
						);
		$this->db->query($sql);
		$_SESSION['mv_import']['report_id'] = $this->db->insert_id;
		// Daten der ID auslesen
		$sql = sprintf("SELECT * FROM %s 
									WHERE imex_id = '%s'
									LIMIT 1",
									
									$this->cms->tbname['papoo_mv_imex_daten'],
									
									$this->db->escape($this->checked->myvorlage)
						);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->checked->ref_dat_tabelle = $result['0']['imex_felder_update'];
		$this->checked->ref_csv_tabelle = $result['0']['imex_csv_update'];
		$csv_vorlage = explode(";", $result['0']['imex_csv']); // Vorlage mit Feldnamen CSV (Quelle)
		if (!empty($csv_vorlage))
		{
			$found = 0;
			$_SESSION['mv_import']['first_line'] = $csv_ar = $this->lese_erste_zeile_csv(1); // Vorhandene Feldnamen in der CSV-Datei (Zeile 1)
			foreach ($csv_vorlage as $key => $value) // Vorlage imex_csv durchgehen
			{
				// und mit Feldernamen in Zeile 1 der CSV-Datei vergleichen
				foreach ($csv_ar as $key2 => $value2)
				{
					if ($value
						AND $value2 == $value)
					{
						$csv2[$key] = $key2; // Pointer zur CSV-Datei setzen (Feldname_X befindet sich an Pos. $key2)
						$found = 1;
						break;
					}
				}
				if (!$found) $csv2[$key] = $value; // Feldname zuweisen. Dient sp�ter zur Identifikation des Fehlers (!is_numeric)
			}
			
			$found = 0;
			$feld = explode(";", $result['0']['imex_felder']); // Vorlage mit Feldnnamen DB
			$tabelle_felder = $this->lese_felder_tabelle($this->checked->tabelle); // vorhandene Tabellen-Feldnamen in der DB
			// Vorlage imex_felder durchgehen (DB-Feldnamen)
			foreach ($feld as $key => $value)
			{
				// Feldernamen der Vorlage-Datei
				foreach ($tabelle_felder as $key2 => $value2)
				{
					if ($value
						AND $value == $value2)
					{
						$feld2[$key] = $key2; // Pointer zur Tabelle setzen (Feldname_X befindet sich an Pos. $key2)
						$found = 1;
						break;
					}
				}
				if (!$found) $feld2[$key] = $value;  // Feldname zuweisen. Dient sp�ter zur Identifikation des Fehlers (!is_numeric)
			}
			// Daten an das Session Array �bergeben
			// (Z�hlweise  beider Tabellen-Pointer in $_SESSION['mv_import']['csv_ar'] ab 1)
			foreach ($csv2 as $key => $value)
			{
				$_SESSION['mv_import']['csv_ar'][$value] = $feld2[$key]; // Jeder CSV Eintrag bekommt seinen Feld Eintrag wieder
			}
		}
	}

	/**
	 * Tabellen f�r die Verwaltungen aus der Datenbank holen und ans Template weitergeben
	 */
	function get_mv_tabs()
	{
		$sql = sprintf("SELECT mv_id,
								mv_name FROM %s",
								$this->cms->tbname['papoo_mv']
						);
		$verwaltungen = $this->db->get_results($sql, ARRAY_A);
		if (!empty($verwaltungen))
		{
			foreach($verwaltungen as $verwaltung) { $this->content->template['tabtar'][$verwaltung['mv_name']] = "papoo_mv_content_" . $verwaltung['mv_id']; }
		}
	}	 	

	/**
	 * Metaebenen 
	 */
	function get_meta_ebene()
	{
		$sql = sprintf("SELECT mv_id,
								mv_name
								FROM %s",
								$this->cms->tbname['papoo_mv']
						);
		$verwaltungen = $this->db->get_results($sql, ARRAY_A);
		if (!empty($verwaltungen))
		{
			foreach($verwaltungen as $verwaltung)
			{		
				$sql = sprintf("SELECT mv_meta_id,
										mv_meta_group_name
										FROM %s",
										
										$this->cms->tbname['papoo_mv']
										. "_meta_"
										. $this->db->escape($verwaltung['mv_id'])
								);
				$metagruppen = $this->db->get_results($sql, ARRAY_A);
				if (!empty($metagruppen))
				{
					foreach($metagruppen as $metagruppe)
					{
						$metagruppe['mv_meta_group_name'] = $verwaltung['mv_name'] . " - " . $metagruppe['mv_meta_group_name'];
						$metaebenen[] = $metagruppe;						
					}
				}
			}
		}
		$this->content->template['mv_metaebenen'] = $metaebenen;
	}	 
	
	/**
	 * Daten importieren
	 */
	function do_import()
	{

		$this->content->template['metaebene'] = $this->checked->metaebene;
		$this->content->template['tabelle'] = $this->checked->tabelle;
		if (!empty($this->checked->sprachen)) $this->content->template['mv_import_sprachen'] = $this->checked->sprachen;
		else $this->content->template['mv_import_sprachen'] = $this->checked->mv_import_sprachen;
		$this->content->template['format'] = $this->checked->format;
		$this->content->template['ins'] = $this->checked->ins;
		$this->content->template['myvorlage'] = $this->checked->myvorlage;
		$this->content->template['importvorlage_name'] = $this->checked->importvorlage_name;
		$start = true;
		// wenn es ein dzvhae Auftritt ist, dann Flag f�rs Template setzten
		if ($this->mv->dzvhae_system_id) $this->content->template['mv_is_dzvhae_mv'] = 1;		
		// wieviel Mitglieder/Objekte pro Runde (pro Reload) importiert werden
		$this->max_counter_pro_runde = 50;
		$this->counter_start = $this->checked->counter_start;
		// in der ersten Runde muss eine Zeile weniger verarbeitet werden, da diese die Spaltennamen enthalten kann
		// ansonsten Wert gleich 0
		$this->counter_minus_eins = 0;
		$this->get_mv_tabs(); // nur die Tabellen f�r Flex-Verwaltungen zur Auswahl anzeigen
		$this->get_meta_ebene(); // Alle vorhandenen Metaebenen ans Template
		$this->get_sprachtabellen();// welche Sprachen gibt es?
		// Alle vorhandenen Rechtegruppen
		$sql = sprintf("SELECT gruppenname,
								gruppeid
								FROM %s",
								$this->cms->tbname['papoo_gruppe']
						);
		$this->content->template['mv_rechtegruppen'] = $this->db->get_results($sql, ARRAY_A);
		if (!empty($this->checked->add_metaebenen)
			&& empty($_SESSION['mv_import']['add_metaebenen']))
		{
			// add_metaebenen in Session speichern
			$_SESSION['mv_import']['add_metaebenen'] = array();
			$_SESSION['mv_import']['add_metaebenen'] = $this->checked->add_metaebenen;
		}
		if (!empty($this->checked->mv_rechtegruppen)
			&& empty($_SESSION['mv_import']['mv_rechtegruppen']))
		{
			// mv_rechtegruppen in Session speichern
			$_SESSION['mv_import']['mv_rechtegruppen'] = array();
			$_SESSION['mv_import']['mv_rechtegruppen'] = $this->checked->mv_rechtegruppen;
		}    
		// Startwerte definieren, wenns die erste Runde ist
		if (empty($this->checked->counter_start))
		{
			$this->checked->counter_start = 0;
			$this->counter_start = 0;
			// Flag, ob die Jahreszahlen in Zeitfeldern 2- oder 4-stellig sind: true == 4-stellig, false == 2-stellig
			// wird erstmal auf true gesetzt, weil das Script von 4-stelligen Zahlen ausgeht
			$this->mv->import_timestamp = 0;
			$_SESSION['mv_import']['mv_import_protokoll'] = array(); // Session f�r dzvhae Sonderfall Protokoll l�schen
			$_SESSION['mv_import']['mv_lup'] = array(); // array mit allen Feldernamen_ids
			// wenn Datei mit Spaltennamen in der ersten Zeile, dann muss an anderer Stelle in der ersten Runde der Wert 1 abgezogen werden
			if ($this->checked->format == "csvmit") $this->counter_minus_eins = -1;
		}
		// Vorlage speichern
		if (!empty($this->checked->makevorlage)
			&& !empty($this->checked->importvorlage_name))
		{
			$sql = sprintf("SELECT imex_name FROM %s 
										WHERE imex_name = '%s'",
										
										$this->cms->tbname['papoo_mv_imex_daten'],
										
										$this->db->escape($this->checked->importvorlage_name)
							);
			$results = $this->db->get_results($sql, ARRAY_A);
			if (count($results)) $this->content->template['templ_exists'] = 1;
			else
			{
				$csv_felder = $this->lese_erste_zeile_csv(1); // Dateifelder einlesen, 1 = nur den Feldnamen �bergeben (ohne lfd. Nr.)
				$tabelle_felder = $this->lese_felder_tabelle($this->checked->tabelle);
				// Zuordnungen durchgehen und Feldnamen durch Semikolon getrennt konkatinieren
				// (Z�hlweise  beider Tabellen-Pointer in $_SESSION['mv_import']['csv_ar'] ab 1)
				foreach ($_SESSION['mv_import']['csv_ar'] as $key => $value)
				{
					$csvdtb .= $csv_felder[$key] . ";"; //CSV-Feldname
					$felddtb .= $tabelle_felder[$value] . ";"; //SQL-Tabellen-Name
				}
				// Vorlage in die Datenbank speichern
				$sql = sprintf("INSERT INTO %s SET imex_name = '%s', 
													imex_csv = '%s', 
													imex_felder = '%s', 
													imex_csv_update = '%s', 
													imex_felder_update = '%s' ",
													
													$this->cms->tbname['papoo_mv_imex_daten'],
													
													$this->db->escape($this->checked->importvorlage_name),
													$this->db->escape($csvdtb),
													$this->db->escape($felddtb),
													$this->db->escape($this->checked->ref_csv_tabelle),
													$this->db->escape($this->checked->ref_csv_tabelle)
								);
				$this->db->query($sql);
				$this->content->template['vorsaved']= "1";
			}
		}
		if (isset($_SESSION['mv_import']['tabelle_felder']))
		{
			$a = (array_search("Benutzername_1", $_SESSION['mv_import']['tabelle_felder']));
			if ($a !== false) $bn_error = true;
		}
		// Import durchf�hren
		if ($this->checked->makeimport
			AND !$bn_error)
		{
			// dzvhae System ID in Session speichern
			if (empty($_SESSION['mv_import']['mv_dzvhae_system_id'])) $_SESSION['mv_import']['mv_dzvhae_system_id'] = $this->checked->mv_dzvhae_system_id;
			$start = false; // Kein Start
			global $db_praefix;
			$this->content->template['is_uploaded_step3'] = "ok"; // Auf Schritt 3 setzen
			$this->get_validation_data();
			#if ($this->checked->format == "csvohne"
			#	OR $this->checked->format == "csvmit")
			#{
				$csv = $this->get_csv_content_rows(); // CSV Datei in Array einlesen und dabei die G�ltigkeit der Daten checken
				$cvs_ar = $this->convert_to_array($csv, $mit = "1"); // File Array in fertiges Array konvertieren
			#}
			// Wenn XML (csvmit/csvohne nicht relevant)
			if ($this->checked->format == "xml")
			{
				$xml = $this->get_xml_content(); // CSV Datei einlesen in Array
				$cvs_ar = $this->convert_to_array_xml($xml); // File Array in fertiges Array konvertieren
			}

			#if (count($cvs_ar)) // deaktiviert, denn dann erfolgt auch kein L�schen bei neu_ins_del und leerem Import 
			#{
				$datenfertig = $this->insert_into_database($cvs_ar); // Daten eintragen
				$this->content->template['imported_records'] = $_SESSION['mv_import']['success_count'];
			#}
			$this->content->template['records_in_error'] = $_SESSION['mv_import']['records_in_error'];
			$time = time(); // Zeitstempel
			$file = "/dokumente/logs/" . basename($this->checked->tabelle) . "_" . $time . "_csv_import.sql"; //Dateinamen
			// Datei zur Dokumentation des Imports erzeugen (nur Debugging)
			//$this->diverse->write_to_file($file ,$datenfertig);			
			unlink(PAPOO_ABS_PFAD . "/dokumente/logs/" . basename($_SESSION['mv_import']['uploaded_file']));
			// Sessioneintr�ge L�schen
			unset($_SESSION['mv_import']['metadaten']);
			unset($_SESSION['mv_import']['mv_lup']);
			//unset($_SESSION['mv_import']['mv_import_protokoll']);
			unset($_SESSION['mv_import']['mv_imex_update_insert']);
			unset($_SESSION['mv_import']['csv_ar']);
			unset($_SESSION['mv_import']['error_count']);
			unset($_SESSION['mv_import']['file_to_import']);
			unset($_SESSION['mv_import']['first_line']);
			unset($_SESSION['mv_import']['records_in_error']);
			unset($_SESSION['mv_import']['report_id']);
			unset($_SESSION['mv_import']['success_count']);
			unset($_SESSION['mv_import']['import_daten']);
			unset($_SESSION['mv_import']['import_anz_zeilen']);
			unset($_SESSION['mv_import']['mv_tabelle_felder_csv_all']);
			unset($_SESSION['mv_import']['tabelle_felder']);
			unset($_SESSION['mv_import']['mv_dzvhae_system_id']);
			unset($_SESSION['mv_import']['mv_zahlungen_lookup']);
			unset($_SESSION['mv_import']['uploaded_file']);
			unset($_SESSION['mv_import']['add_metaebenen']);
			unset($_SESSION['mv_import']['mv_rechtegruppen']);
			$this->reorder_select_felder($this->mv_id); // die Auswahlm�glichkeiten bei Select Feldern neu sortieren
		}
		// 2. Schritt des Imports: Formular Felder-Zuordnung
		else
		{
			// f�r dzvhae system ID auswahl zwischenspeichern und direkt ans Template
			$this->content->template['mv_dzvhae_system_id'] = $this->checked->mv_dzvhae_system_id;
			// Import Schritt 2
			if (!empty($this->checked->startimport)
				or !empty($this->checked->startzuordnen)
				or !empty($this->checked->is_auswahl)
				or $bn_error)
			{
				// Sessiondaten aktivieren
				$entfeld = $this->get_entfernfeld(); // Falls ein Entfernen-Button gedr�ckt wurde
				if (!empty($this->checked->startzuordnen)  // Falls der Felder-Zuordnen-Button gedr�ckt wurde
					OR !empty($this->checked->makevorlage) // Falls der Vorlage-Speichern-Button gedr�ckt wurde
					OR $entfeld
					or $bn_error)
				{
					if (!$this->checked->add_metaebenen) $this->checked->add_metaebenen = $_SESSION['mv_import']['add_metaebenen'];
					if (!$this->checked->mv_rechtegruppen) $this->checked->mv_rechtegruppen = $_SESSION['mv_import']['mv_rechtegruppen'];
				}
				if ($bn_error) $this->content->template['bn_in_error'] = 1;
				if (!$this->checked->tabelle) $fehler = $this->content->template['verwaltung_fehlt'] = 1;
				if (!$this->checked->metaebene) $fehler = $this->content->template['mv_fehlt'] = 1;
				if (!count($this->checked->add_metaebenen)) $fehler = $this->content->template['add_meta_fehlt'] = 1;
				if (!count($this->checked->mv_rechtegruppen)) $fehler = $this->content->template['rechtegruppe_fehlt'] = 1;
				if (!$this->checked->ins) $fehler = $this->content->template['neu_update_fehlt'] = 1;
				if (!$fehler)
				{
					if (!empty($this->checked->ins)) $_SESSION['mv_import']['mv_imex_update_insert'] = $this->checked->ins; // Update/Insert Art �bergeben f�r sp�ter
					if ($_SESSION['mv_import']['mv_imex_update_insert'] == "ins_upd") $this->content->template['is_update'] = "ok";
					// Wenn eine Vorlage gew�hlt wurde, Daten zuweisen
					if (!empty($this->checked->myvorlage)
						AND !count($_SESSION['mv_import']['csv_ar'])) $this->make_session_from_vorlage();
					$start = false; // Kein Start
					$entfeld = $this->get_entfernfeld(); // Entfernfeld rausholen
					if (!empty($entfeld)) unset($_SESSION['mv_import']['csv_ar'][$entfeld]); // Feld entfernen
					$this->content->template['is_uploaded_step2'] = "ok"; // Auf Schritt 2 setzen
					$this->content->template['tabelle'] = $this->checked->tabelle; // Name der ausgew�hlten Tabelle
					$this->content->template['format'] = $this->checked->format;
					$this->content->template['metaebene'] = $this->checked->metaebene;
					// Erste Zeile bei CSV Dateien
					if ($this->checked->format == "csvohne"
						or $this->checked->format == "csvmit") $csv_felder = $this->lese_erste_zeile_csv(0); // Dateifelder einlesen mit '(lfd. Nr.): ' am Beginn, dann Feldname
					// Feldnamen bei xml Dateien
					if ($this->checked->format == "xml") $csv_felder = $this->lese_erste_zeile_xml(); // Dateifelder einlesen
					$tabelle_felder = $this->lese_felder_tabelle($this->checked->tabelle); // Felder der ausgew�hlten Datenbank einlesen
					// wenn beide Einträge mit den Selectboxen ausgewählt sind
					// Felder ins Session Array zur Weitergabe
					if (!empty($this->checked->startzuordnen)
						and !empty($this->checked->dat_tabelle) 
						and !empty($this->checked->csv_tabelle)
					) {
						$_SESSION['mv_import']['csv_ar'][$this->checked->csv_tabelle] = $this->checked->dat_tabelle;
					}
					// Übergabe sprechend ans Template für die Liste unter den Selectboxen
					$templar = array();
					if (!empty($_SESSION['mv_import']['csv_ar']))
					{
						foreach ($_SESSION['mv_import']['csv_ar'] as $key => $value)
						{
							if (is_numeric($key)) $templar[$key]['csv'] = $csv_felder[$key]; // Feldname CSV
							else
							{
								// Fehler, Vorlagenfeld nicht in der CSV-Datei
								#$templar[$key]['csv'] = '<span class="template_error">' . $key . "</span>"; // Feldname CSV
								#$this->content->template['vorlagen_fehler1'] = 1; // Feld nicht vorhanden
							}
							if (is_numeric($value)) $templar[$key]['tab'] = $tabelle_felder[$value]; // Feldname DB
							else
							{
								// Fehler, Vorlagenfeld nicht in der DB
								#$templar[$key]['tab'] = '<span class="template_error">' . $value . "</span>"; // Feldname DB
								#$this->content->template['vorlagen_fehler1'] = $this->content->template['vorlagen_fehler1'] + 2; // Feld nicht vorhanden
							}
						}
					}
					$entfeld_upd = $this->get_entfernfeld("update"); // Entfernfeld rausholen
					// Wenn die Zuordnung nicht entfernt werden soll
					if (empty($entfeld_upd))
					{
						// Namen der Felder f�r die Ausgabe im Template
						$this->content->template['ref_dat_tabelle'] = $tabelle_felder[$this->checked->ref_dat_tabelle];
						$this->content->template['ref_csv_tabelle'] = $csv_felder[$this->checked->ref_csv_tabelle];
						// Ids der Felder um die als hidden Felder weiterzugeben und auch wieder l�schen zu k�nnen
						$this->content->template['ref_dat_tabelle_value'] = $this->checked->ref_dat_tabelle;
						$this->content->template['ref_csv_tabelle_value'] = $this->checked->ref_csv_tabelle;
					}
					else // Zuordnung aufheben und die IDs wieder freigeben
					{
						//die IDs wieder freigeben f�r die Felder Liste
						$this->checked->ref_dat_tabelle = "";
						$this->checked->ref_csv_tabelle = "";
					}
					$this->content->template['csv_tabelle_daten'] = $templar; // Daten ans Template �bergeben
					// f�r dzvhae system ID: Alle csv Feldernamen zwischenspeichern ohne zugeordnete rauszufischen
					if (empty($_SESSION['mv_import']['mv_tabellen_felder_csv_all'])) $_SESSION['mv_import']['mv_tabelle_felder_csv_all'] = $csv_felder;
					// Felder durchgehen ob schon drin, wenn ja dann rausflitschen
					if (!empty($csv_felder))
					{
						// Alle Felder aus dem CSV durchgehen
						foreach ($csv_felder as $key => $feld)
						{
							// Wenn noch nicht zugeordnet, �bergeben
							// vorhandene und  nicht ausgew�hlte Felder zuweisen
							if (empty($_SESSION['mv_import']['csv_ar'][$key])
								&& $key != $this->checked->ref_csv_tabelle) $felder[$key] = $feld;
						}
					}
					$this->content->template['csv_felder'] = $felder; // Daten ans Template
					$this->content->template['tabelle_felder_csv_all'] = $_SESSION['mv_import']['mv_tabelle_felder_csv_all'];
					$tabelle_felder2 = array();
					// Tabellenfelder durchgehen ob schon drin, wenn ja dann rausflitschen
					if (!empty($tabelle_felder))
					{
						// Alle Felder aus der Tabelle durchgehen
						foreach ($tabelle_felder as $key => $tab)
						{
							$isdrin = 0;
							// Alle Session Arraydaten durchgehen
							if (!empty($_SESSION['mv_import']['csv_ar']))
							{
								foreach ($_SESSION['mv_import']['csv_ar'] as $key2 => $value)
								{
									// Wenn schon drin, also  ausgew�hlt, dann auf drin setzen
									if ($key == $value
										or $key == $this->checked->ref_dat_tabelle) $isdrin = 1;
								}
							}
							// Wenn nocht nicht drin, dann ans Template �bergeben
							if ($isdrin != 1) $tabelle_felder2[$key] = $tab;
						}
					}
					// Datenbankfelder
					$this->content->template['tabelle_felder'] = $_SESSION['mv_import']['tabelle_felder'] = $tabelle_felder2;
					$a = (array_search("Benutzername_1", $_SESSION['mv_import']['tabelle_felder']));
					if ($a !== false) $this->content->template['bn_in_error'] = 1;
					else $this->content->template['bn_in_error'] = 0;
				}
				else
				{
					// Fehlerbehandlung
					$this->content->template['is_uploaded'] = $this->content->template['no_upload_msg'] = "ok";
					$this->content->template['vorlage_array'] = $this->get_vorlagen();
					if (count($this->content->template['mv_rechtegruppen']))
					{
						foreach ($this->content->template['mv_rechtegruppen'] AS $key => $value)
						{
							if (count($this->checked->mv_rechtegruppen))
							{
								foreach ($this->checked->mv_rechtegruppen AS $key2 => $value2)
								{
									if ($value['gruppeid'] == $value2) $this->content->template['mv_rechtegruppen'][$key]['checked'] = 1;
								}
							}
							if (!$this->content->template['mv_rechtegruppen'][$key]['checked']) $this->content->template['mv_rechtegruppen'][$key]['checked'] = 0;
						}
					}
				}
			}
			// Wenn eine Datei ausgew�hlt wurde
			if (!empty($this->checked->startupload))
			{
				$this->content->template['is_uploaded'] = "ok"; // Upload hat stattgefunden
				$this->do_upload(); // Datei hochladen
				$_SESSION['mv_import']['file_to_import'] = $_FILES['myfile']['name'];
				$vorlagen = $this->get_vorlagen(); // Vorlagen raussuchen
				$this->content->template['vorlage_array'] = $vorlagen; // Vorlagen ans Template �bergeben
				// Endung CSV, dann im Template auf CSV Voreinstellung setzen
				if (stristr($_SESSION['mv_import']['uploaded_file'], "csv")) $this->content->template['csvchecked'] = 'checked="checked"';
				// Endung xml, dann im Template auf xml Voreinstellung setzen
				if (stristr($_SESSION['mv_import']['uploaded_file'], "xml")) $this->content->template['xmlcheck'] = 'checked="checked"';
			}
			// Ist noch Start, nix passiert, Upload Maske anbieten
			elseif ($start == true AND !$fehler)
			{
				$this->content->template['is_start_mv'] = "ok"; // Template auf Start setzen
				// Session zur�cksetzen
				unset($_SESSION['mv_import']['csv_ar']);
				unset($_SESSION['mv_import']['mv_imex_update_insert']);
				// Wenn Vorlage, l�schen
				if (!empty($this->checked->delete_vorlage))
				{
					$sql = sprintf("DELETE FROM %s 
											WHERE imex_id = '%d'
											LIMIT 1",
											
											$this->cms->tbname['papoo_mv_imex_daten'],
											
											$this->db->escape($this->checked->myvorlage)
									);
					$this->db->query($sql);
					$this->content->template['vorlage_geloescht'] = "ok"; // Template ist gel�scht Aussage ausgeben
				}
				$vorlagen = $this->get_vorlagen(); // Vorlagen raussuchen
				$this->content->template['vorlage_array'] = $vorlagen; // Vorlagen ans Template �bergeben
			}
		}
	}

	/**
	 * Alle vorhandenen Vorlagen raussuchen
	 */
	function get_vorlagen()
	{
		$sql = sprintf("SELECT * FROM %s ORDER BY imex_name",
									$this->cms->tbname['papoo_mv_imex_daten']
						);
		$result=$this->db->get_results($sql, ARRAY_A);
		return $result;
	}

	/**
	 * Eine Datei in das interna/templates_c Verzeichnis hochladen
	 *
	 */
	function do_upload()
	{
		$destination_dir = 'dokumente/logs';// Zielverzeichnis
		$extensions = array ( 'php', 'php3', 'php4', 'phtml', 'cgi', 'pl'); // nicht erlaubte Dateiendungen
		$upload_do = new file_upload(PAPOO_ABS_PFAD); // Upload durchf�hren
		// Wenn Files hochgeladen wurden
		if (count($_FILES) > 0)
		{
			// Durchf�hren und falls etwas schief geht
			if (!$upload_do->upload($_FILES['myfile'], $destination_dir, 0, $extensions, 1))
			{
				$falsch = 1; // falsch setzen
				if (!empty ($upload_do->error)) $falsch_exists = 1; // wenn etwas passiert ist, und ein Error vorliegt
			}
			else $falsch = 0;
		}
		// wenn dder Upload geklappt hat, Daten eintragen.
		if ($falsch != 1)
		{
			$filename = $upload_do->file['name'];
			$filesize = $upload_do->file['size'];
			$download = "/dokumente/logs/" . $filename;
			$_SESSION['mv_import']['uploaded_file'] = $download;
		}
		else // Fehler beim Hochladen
		{
			// Meldung zeigen..
			$this->content->template['errortext'] = $this->content->template['message_20'] . "<p>$upload_do->error</p>";
			//Template auf Fehler setzen.
			$this->content->template['is_start_mv'] = "ok";
			$this->content->template['is_upload'] = "";
		}
	}
	
	/**
	* Gibt den Feldnamen und die ID zur�ck
	*
	* @param ing $key (Feldname und ID in einem ing)
	* @return $mvcform_name, $find_name (Gibt Feldname und ID einzeln zur�ck)
	* @access public
	*
	**/
	function get_feld_name($key)
	{
		$find_name = array_pop(explode("_", $key));
		// holt die Feldernamen aus der Datenbank
		$sql = sprintf("SELECT mvcform_name
								FROM %s 
								WHERE mvcform_id = '%s'
								AND mvcform_meta_id = '%d'",
								
								$this->cms->tbname['papoo_mvcform'],
								
								$this->db->escape($find_name),
								$this->db->escape($this->checked->metaebene)
						);
		$mvcform_name = $this->db->get_var($sql);
		return array($mvcform_name, $find_name);
	}
	
	/**
  	* SelectFelder neu sortieren
  	**/			 
	function reorder_select_felder($mv_id = "1")
	{
		$mv_id = $this->db->escape($mv_id);
		$sql = sprintf("SELECT mvcform_id
								FROM %s 
								WHERE (mvcform_type = 'select'
								OR mvcform_type = 'multiselect') 
								AND mvcform_form_id = '%d'
								AND mvcform_meta_id = '%d'",
								
								$this->cms->tbname['papoo_mvcform'],
								
								$this->db->escape($mv_id),
								$this->db->escape($this->checked->metaebene)
						);
		$select_felder = $this->db->get_results($sql, ARRAY_A);
		if (!empty($select_felder))
		{
			foreach($select_felder as $select_feld)
			{
				// leere Content Eintr�ge aus der Lang Tabelle des Feldes fischen
				$sql = sprintf("SELECT lookup_id
										FROM %s 
										WHERE content = ''",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($mv_id)
										. "_lang_"
										. $this->db->escape($select_feld['mvcform_id'])
								);
				$lookup_ids = $this->db->get_results($sql);
				if (!empty($lookup_up_ids))
				{
					// und die entsprechenden Eintr�ge in der Lookup Tabelle des Feldes l�schen
					foreach($lookup_ids as $lookup_id)
					{
						$sql = sprintf("DELETE FROM %s 
												WHERE lookup_id = '%d'",
												
												$this->cms->tbname['papoo_mv']
												. "_content_"
												. $this->db->escape($mv_id)
												. "_lookup_"
												. $this->db->escape($select_feld['mvcform_id'])
										);
						#$this->db->query($sql);						
					}
				}
				// und den leeren Content Eintrag in der Lang Tabelle des Feldes l�schen
				$sql = sprintf("DELETE FROM %s 
										WHERE content = ''",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($mv_id)
										. "_lang_"
										. $this->db->escape($select_feld['mvcform_id'])
								);
				$this->db->query($sql);
				// Auswahlm�glichkeiten des Feldes rausholen
				$sql = sprintf("SELECT * FROM %s 
											ORDER BY lang_id, content ASC",
											
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($mv_id)
											. "_lang_"
											. $this->db->escape($select_feld['mvcform_id'])
								);
				$auswahl = $this->db->get_results($sql, ARRAY_A);
				$i = 1;
				// Durchz�hlen und neu einsortieren
				if ((!empty($auswahl)))
				{
					foreach($auswahl as $dat)
					{
						if ($dat['lang_id'] != $lang_old) $i = 1;
						$sql = sprintf("UPDATE %s SET order_id = '%d' 
													WHERE lookup_id = '%d' 
													AND lang_id = '%d'",
													
													$this->cms->tbname['papoo_mv']
													. "_content_"
													. $this->db->escape($mv_id)
													. "_lang_"
													. $this->db->escape($select_feld['mvcform_id']),
													
													$this->db->escape($i),
													$this->db->escape($dat['lookup_id']),
													$this->db->escape($dat['lang_id'])
										);
						$this->db->query($sql);
						$i++;
						$lang_old = $dat['lang_id'];
					}
				}
			}
		}		
	}
	
	/**
	 * Holt f�r alle Sprachen Werte aus der Datenbank
	 */	 	
	function get_sprachtabellen()
	{
		// holt alle Sprachen aus der Tabelle
		$sql = sprintf("SELECT * FROM %s", 
										$this->cms->tbname['papoo_mv_name_language']
						);
		$sprachen = $this->db->get_results($sql, ARRAY_A);
		// und gibts ans Template weiter
		$this->content->template['sprachen'] = $sprachen;
	}
	
	/**
	 * Gibt das Protokoll des Imports aus, welche zuvor in einer Session gespeichert wurden
	 */	 	
	function print_protokoll()
	{
		$this->content->template['mv_import_protokoll'] = $_SESSION['mv_import']['mv_import_protokoll']; // Importprotokoll ans Template
		unset($_SESSION['mv_import']['mv_import_protokoll']); // Session l�schen
	}
}
$imex_mw = new imex_mw_class();
?>
