<?php
// Vor Aufruf müssen folgende Variablen gesetzt werden:
// $change_or_make auf "change" oder "make"
/**
* Formulardaten für die Speicherung/Updaten in einem SQL Befehlsstring aufbereiten
**/
#$spalten_namen = array();
$spalten_namen = $this->get_spalten_namen();
//Felder Rechte rausholen
$this->get_field_rights_schreibrechte();
// weise den Spaltennamen die eingegebenen Werte zu
foreach($spalten_namen as $spalten_name)
{
	if (!defined("admin"))
	{
		//Checken obüberhaupt schreibrechte bestehen
		$die_aktuelle_id = $spalten_name['mvcform_id'];
		if (is_numeric($die_aktuelle_id))
		{
			if (!in_array($die_aktuelle_id, $this->felder_schreib_rechte_aktuelle_gruppe)) continue; //Keine Leserechte - dann abbrechen
		}
	}
	$find_name = $spalten_name['mvcform_name']; // Feldname
	$find_id = $spalten_name['mvcform_id'];     // Feldid
	$feld_name = $find_name . "_" . $find_id;   // Feldname_Feldid
	// Ein Mitglied im FE darf sich nicht selbst vom Login deaktivieren!
	if ($feld_name == "active_7"
		AND $this->checked->template == "mv/templates/mv_edit_front.html") continue;
	$find_type = $spalten_name['mvcform_type']; // Feldtype
	// Markierung für neue Feldtypen
	// checke ob es ein Passwortfeld ist
	if ($find_type == "password")
	{
		if ($change_or_make == "make") $this->checked->$feld_name = $this->diverse->hash_password($this->checked->$feld_name);
		else
		{
			// ist es auch ausgefüllt, dann Hash berechnen und Passwort ersetzen
			if ($this->checked->$feld_name != "") $this->checked->$feld_name = $this->diverse->hash_password($this->checked->$feld_name);
			// wenn es leer ist, dann altes Passwort aus der Tabelle holen
			else
			{
				$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
				$sql = sprintf("SELECT %s FROM %s
											WHERE mv_content_id = '%s'
											LIMIT 1",
											$this->db->escape($feld_name),
											
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($this->checked->mv_id)
											. "_search_"
											. $lang,
											
											$this->db->escape($this->checked->mv_content_id)
								);
				$this->checked->$feld_name = $this->db->get_var($sql);
			}
		}
	}
	// checke ob es ein Timestamp Feld ist, dann aus den 3 Formularfeldern einem Datumseintrag jahr-monat-tag machen
	if ($find_type == "timestamp")
	{
		$tag = "mvcform_tag_" . $find_name . "_" . $find_id;
		$monat = "mvcform_monat_" . $find_name . "_" . $find_id;
		$jahr = "mvcform_jahr_" . $find_name . "_" . $find_id;
		// Stunden, Minuten, Sekunden, Monat, Tag, Jahr
		$this->checked->$feld_name = "";
		if (!empty($this->checked->$monat) && !empty($this->checked->$tag) && !empty($this->checked->$jahr))
		{
			$this->checked->$feld_name = $this->get_longtime($this->checked->$jahr, $this->checked->$monat,
				$this->checked->$tag);
		}
	}
	//bei einem Zeitintervall-Feld das gleiche wie bei Timestamp nur mit zwei Datumswerten
	if ($find_type == "zeitintervall")
	{
		$anfang_tag = "anfang_tag_" . $find_name . "_" . $find_id;
		$ende_tag = "ende_tag_" . $find_name . "_" . $find_id;
		$anfang_monat = "anfang_monat_" . $find_name . "_" . $find_id;
		$ende_monat = "ende_monat_" . $find_name . "_" . $find_id;
		$anfang_jahr = "anfang_jahr_" . $find_name . "_" . $find_id;
		$ende_jahr = "ende_jahr_" . $find_name . "_" . $find_id;
		$this->checked->$feld_name = "";
		if (!empty($this->checked->$anfang_monat)
			&& !empty($this->checked->$anfang_tag)
			&& !empty($this->checked->$anfang_jahr))
		{
			$this->checked->$feld_name = $this->get_longtime($this->checked->$anfang_jahr,
				$this->checked->$anfang_monat, $this->checked->$anfang_tag);
		}
		// Trenner für die zwei Datums Timestamps
		$this->checked->$feld_name .= ",";
		if (!empty($this->checked->$ende_monat)
			&& !empty($this->checked->$ende_tag)
			&& !empty($this->checked->$ende_jahr))
		{
			$this->checked->$feld_name .= $this->get_longtime($this->checked->$ende_jahr,
				$this->checked->$ende_monat, $this->checked->$ende_tag);
		}
	}
	// ist es ein Multiselectfeld?
	if ($find_type == "multiselect")
	{
		// mögliche lookup Werte aus der Datenbank holen
		$sql = sprintf("SELECT lookup_id,
								content
								FROM `%s`
								WHERE lang_id = %d",
								
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_lang_"
								. $this->db->escape($find_id),
								
								(int)($this->cms->lang_back_content_id)
						);
		$select_optionen = $this->db->get_results($sql, ARRAY_A);
		// alte Lookup Werte löschen
		#$this->delete_lookup($this->db->escape($this->checked->mv_id), $find_id, $this->checked->mv_content_id);
		$found = 0;
		// neue Werte speichern
		if (!empty($select_optionen))
		{
			$lookup_ids = array();
			// Hole alle möglichen Lookup-IDs
			foreach($select_optionen as $select_option)
			{
				$lookup_ids[] = (int)$select_option['lookup_id'];
			}
			// Durchsuche die übergebenen Formulardaten nach Einträgen zum aktuellen Multiselect-Feld
			$subbi_prefix = 'hiddenmvcform' . $find_name . '_' . $find_id . '_';
			foreach($this->checked as $key => $value) {
				if (strpos($key, $subbi_prefix) === 0 && is_numeric($value) && $value > 0)
				{
					$lookup_id = substr($key, strlen($subbi_prefix));
					if (is_numeric($lookup_id)) {
						if (array_search((int)$lookup_id, $lookup_ids) !== FALSE)
						{
							$this->checked->$feld_name .= $lookup_id . "\n";
							$found = 1;
						}
					}
				}
			}
		}
		if (!$found) $this->checked->$feld_name .= "0\n"; // 0 falls leer. Wie beim Import!
	}
	// ist es ein select/radio... Feld
	if ($find_type == "select"
		|| $find_type == "pre_select"
		|| $find_type == "radio"
		|| $find_type == "check")
	{
		$this->checked->$feld_name = $this->checked->$feld_name ? $this->checked->$feld_name : 0;
		#$this->delete_lookup($this->db->escape($this->checked->mv_id),
		#					$find_id,
		#					$this->db->escape($this->checked->mv_content_id)
		#					);
		// $mv_id, $feld_id, $content_id, $lookup_id
		#$this->make_lookup($this->db->escape($this->checked->mv_id),
		#					$find_id,
		#					$this->db->escape($this->checked->mv_content_id),
		#					$this->db->escape($this->checked->$feld_name)
		#					);
	}
	// Picture Upload?
	if ($find_type == "picture"
		OR $find_type == "file")
	{
		// Upload Button allein oder mit Datei?
		#if (!empty($_SESSION['plugin_mv'][$feld_name])) $this->checked->$feld_name = $_SESSION['plugin_mv'][$feld_name];
		#else $this->checked->$feld_name = ""; // Datei wurde gelöscht. Eintrag in DB daher = ""
	}
	if ($find_type == "flex_verbindung")
	{
		#print_r($feld_name);
		$flver_name = "mvcform".$feld_name;

		$feld_name2 = explode("_",$feld_name);

		// hier felddaten rausholen -> mv_id, feld_id
		$sql = sprintf("SELECT * FROM %s WHERE mvcform_name='%s'",
			DB_PRAEFIX."papoo_mvcform",
			trim($feld_name2['0']));
		$verbresult = $this->db->get_results($sql,ARRAY_A);
		#print_r($sql);
		#print_r($verbresult);
		#
		$andereMVId = $verbresult['0']['mvcform_flex_id'];
		// jetzt aus der anderen mv das feld vom typ verbindung rausholen...
		$sql = sprintf("SELECT * FROM %s WHERE mvcform_type='%s' AND mvcform_form_id='%d'",
			DB_PRAEFIX."papoo_mvcform",
			"flex_verbindung",
			$andereMVId);
		$verbresult = $this->db->get_results($sql,ARRAY_A);
		#print_r($sql);
		#print_r($verbresult);
		//exit();
		$anderer_feld_name = $verbresult['0']['mvcform_name']."_".$verbresult['0']['mvcform_id'];

		//print_r($this->checked->$flver_name);exit();
		if (is_array($this->checked->$flver_name))
		{
			foreach ($this->checked->$flver_name as $zk=>$zv)
			{
				if ($zv==1)
				{
					// dann die daten dieses Eintrages holen
					$sql = sprintf("SELECT %s FROM %s WHERE mv_content_id='%d'",
						$anderer_feld_name,
						DB_PRAEFIX."papoo_mv_content_".$andereMVId."_search_1",
						$zk
					);
					$resultZK = $this->db->get_results($sql,ARRAY_A);
					//print_r($sql);
					//print_r($resultZK);

					//json to array
					$data_set = json_decode($resultZK['0'][$anderer_feld_name],true);
					//print_r($data_set);
					$data_set[$this->checked->mv_content_id]= 1;
					$data_insert = json_encode($data_set);
					$sql = sprintf("UPDATE %s SET %s = '%s' WHERE mv_content_id='%d'",

						DB_PRAEFIX."papoo_mv_content_".$andereMVId."_search_1",
						$anderer_feld_name,
						$this->db->escape($data_insert),
						$zk
					);
					$resultZK = $this->db->query($sql);
					//print_r($sql);
					// wieder speichen
					//print_r($data_set);
					#exit();

					//$zk = mv_content_id der anderen Verwaltung...
					$edat[$zk]=$zv;
				}
			}
			$this->checked->$feld_name = json_encode($edat);
		}
		//print_r($this->checked->$feld_name);
		//exit("GOGOGOGOGO");
		// Upload Button allein oder mit Datei?
		#if (!empty($_SESSION['plugin_mv'][$feld_name])) $this->checked->$feld_name = $_SESSION['plugin_mv'][$feld_name];
		#else $this->checked->$feld_name = ""; // Datei wurde gelöscht. Eintrag in DB daher = ""
	}
	unset($_SESSION['plugin_mv'][$feld_name]);
	// ist es ein Text, Textarea oder Check Feld
	if ($find_type == "text"
		|| $find_type == "textarea")
	{}
	if ($feld_name != "mv_content_userid"
		&& $feld_name != "mv_content_owner")
	{
		/*if ($find_type == "check"
		$value = $this->get_lp_wert_search_tabs($find_id,
													$find_name,
													$mv_content_id,
													$sprache->mv_lang_id
													);*/
		// Spaltenname dem jeweiligen Wert zuweisen
		$spalten = sprintf("`" . $this->db->escape($feld_name) . "`='%s', ", 
								$this->db->escape($this->ist_inhalt_auf_xss_pruefen_gesetzt($find_id) ? ($this->checked->{$feld_name}) : ($_POST[$feld_name]))
								);
		if ($feld_name == "Benutzername_1"
			OR $feld_name == "passwort_2"
			OR $feld_name == "email_3"
			OR $feld_name == "antwortmail_4"
			OR $feld_name == "newsletter_5"
			OR $feld_name == "board_6"
			OR $feld_name == "active_7"
			OR $feld_name == "signatur_8"
			OR $spalten_name['mvcform_lang_dependence']) $sql_spalten .= $spalten; // sprachunabhängig
		else $sql_spalten_abh .= $spalten; // sprachabhängig
	}
}
// Sonderfall dzvhae Miltgliederanmeldung im FE für alle Metaebenen per Default aktivieren. S. auch 2 x in make_content_entry.php, make_feld.php
if ($this->checked->template == "mv/templates/mv_create_front.html"
	AND $this->checked->mv_id == 1
	AND $this->dzvhae_system_id)
{
	#$this->delete_lookup($this->db->escape($this->checked->mv_id), 7, $this->db->escape($this->checked->mv_content_id));
	// $mv_id, $feld_id, $content_id, $lookup_id
	#$this->make_lookup($this->db->escape($this->checked->mv_id), 7, $this->db->escape($this->checked->mv_content_id), 1);
}

// und lösche das nicht benötigte ", " am Ende
if ($sql_spalten_abh) $sql_spalten_abh = substr($sql_spalten_abh, 0, -2);
if ($sql_spalten) $sql_spalten = substr($sql_spalten, 0, -2);

?>
