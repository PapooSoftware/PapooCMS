<?php
/**
* Mitglieder-/Objektsuche Backend
*/
$nervi = array();
$this->content->template['meta_gruppen'] = $this->get_meta_gruppen();
// Suchbegriff zwischenspeichern
$searchfor = $this->db->escape($this->checked->search_mv);
// wenn keine Verwaltungs ID mitgeschickt wurde, dann 1 nehmen
if ($this->checked->mv_id == ""
	|| !is_numeric($this->checked->mv_id)) $this->checked->mv_id = 1;
$search_mv_id = $this->checked->mv_id;
#$this->content->template['mv_mv_id'] = $this->checked->mv_id;
#$this->content->template['mv_onemv'] = $this->checked->onemv;
$this->content->template['mv_id'] = $this->checked->mv_id;
// ist der erste Aufruf dieser Suchmaske
if (empty($this->checked->submit_mv)) $searchfor_start = "ok";
$order_by = " ORDER BY mv_content_id ";
// wenn Sortierung angegeben ist, dann auch danach sortieren, ansonsten nach mv_content_id
if (!empty($this->checked->sort_feld))
{
	$sql = sprintf("SELECT mvcform_id,
							mvcform_name
							FROM %s 
							WHERE mvcform_id = '%d' 
							AND mvcform_meta_id = '1'",
							$this->cms->tbname['papoo_mvcform'],
							$this->db->escape($this->checked->sort_feld)
					);
	$felddaten = $this->db->get_results($sql, ARRAY_A);
	$order_by = " ORDER BY " . $felddaten[0]['mvcform_name'] . "_" . $felddaten[0]['mvcform_id'] . " ";
}
// Volltextsuchberiff wieder ans Template geben
$this->content->template['search_mv'] = $searchfor;
// Sucht die Anzahl von Verwaltungen aus der Datenbank
/*$sql = sprintf("SELECT COUNT(mv_id)
							FROM %s 
							WHERE mv_set_suchmaske = '1'",
							$this->cms->tbname['papoo_mv']
				);
$anzahl_mvs = $this->db->get_var($sql);*/
#$this->content->template['anzahl_mvs'] = 101 + $anzahl_mvs;
// Sucht die Anzahl der Felder der Verwaltung
$sql = sprintf("SHOW COLUMNS FROM %s",
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_search_"
								. $this->cms->lang_back_content_id
						);
$result = $this->db->get_results($sql);
$spalten_anzahl = count($result);
// Fehlermeldungen: wenn noch kein Feld in der Verwaltung existiert
if ($spalten_anzahl < 9) $this->content->template['error'] .= $this->content->template['plugin']['mv']['noch_kein_feld'];
$sql = sprintf("SELECT COUNT(mv_content_id)
							FROM %s",
							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id)
							. "_search_"
							. $this->cms->lang_back_content_id
				);
$anzahl_eintraege = $this->db->get_var($sql);
if ($anzahl_eintraege == "0") $this->content->template['error'] .= $this->content->template['plugin']['mv']['noch_kein_eintrag'];
// wenn noch kein Template im Backend definiert wurde
$sql = sprintf("SELECT * FROM %s 
							WHERE lang_id = '%d' 
							AND meta_id = '%d'",
							
							$this->cms->tbname['papoo_mv']
							. "_template_"
							. $this->db->escape($this->checked->mv_id),
							
							$this->db->escape($this->cms->lang_back_content_id),
							$this->db->escape($this->meta_gruppe)
				);
$result = $this->db->get_results($sql);
if ($result[0]->template_content_all == "") $this->content->template['error'] .= $this->content->template['plugin']['mv']['noch_kein_template'];
// Das ist Code f�rs FE ????
// Verwaltungsnamen raussuchen
/*$sql = sprintf("SELECT mv_name,
						mv_id
						FROM %s 
						WHERE mv_set_suchmaske = '1'",
						$this->cms->tbname['papoo_mv']
				);
$mv_namen = $this->db->get_results($sql, ARRAY_A);*/
#$this->content->template['mv_namen'] = $mv_namen;
/*$this->content->template['link_show_all'] = $_SERVER['PHP_SELF']
											. "?menuid="
											. $this->checked->menuid
											. "&template=mv/templates/mv_show_all_front.html";*/
/*$this->content->template['link_show_own'] = $_SERVER['PHP_SELF']
											. "?menuid="
											. $this->checked->menuid
											. "&template=mv/templates/mv_show_own_front.html";*/

/*if ($search_mv_id == "") $this->content->template['link_new_search'] = $_SERVER['PHP_SELF']
																		. "?menuid="
																		. $this->checked->menuid
																		. "&template=mv/templates/mv_search_front.html";
else $this->content->template['link_new_search'] = $_SERVER['PHP_SELF']
													. "?menuid="
													. $this->checked->menuid
													. "&template=mv/templates/mv_search_front_onemv.html";*/
// kleiner Fix mittels Javascript, damit die Reiter alle gleich dicke R�nder haben
#$this->content->template['selected_reiter'] = 100 + $this->checked->mv_id;
//Wenn nur ein Eintrag vorhanden
#if ($anzahl_mvs < 2) $this->content->template['id_selected_reiter'] = 100 + $this->checked->mv_id - 1;
//Mehrere Eintr�ge, dann den letzten anzeigen
#else $this->content->template['id_selected_reiter'] = 100 + $this->checked->mv_id;
// erstmal immer 100 + aktuelle mv_id
#$this->content->template['id_selected_reiter'] = 100 + $this->checked->mv_id;
$felder_ok_fix[0] = "sonst klappt einfaches if() nicht";
if (!empty($this->gruppen_ids))
{
	foreach($this->gruppen_ids as $gruppen_id)
	{
		// holt die Felder raus, die der Benutzer mit seiner Usergruppe sehen darf
		$sql = sprintf("SELECT field_id
								FROM %s 
								WHERE group_read = '1' 
								AND group_id = '%d'",
								
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_field_rights",
								
								$this->db->escape($gruppen_id)
						);
		$felder_ok = $this->db->get_results($sql, ARRAY_A);
		// bastel einen einfachen array damit man array_search gleich benutzten kann, ohne sich den Hals zu verbiegen
		if (!empty($felder_ok)) foreach($felder_ok as $feld) { $felder_ok_fix[] = $feld['field_id']; }
	}
}
// �berpr�ft, ob es f�r die Feldertypen multiselect, select, radio und checked content Eintr�ge mit dem Suchbegriff gibt
// hole die Felder dieser Typen aus der Datenbank
$sql = sprintf("SELECT mvcform_name,
						mvcform_id,
						mvcform_type
						FROM %s 
						WHERE (mvcform_type = 'select'
								OR mvcform_type = 'multiselect'
								OR mvcform_type = 'radio'
								OR mvcform_type = 'check')
						AND mvcform_meta_id = '%d'
						AND mvcform_form_id = '%d'
						AND mvcform_normaler_user = '1'",
						$this->cms->tbname['papoo_mvcform'],
						$this->db->escape($this->meta_gruppe),
						$this->db->escape($this->checked->mv_id)
				);
$lookup_felder = $this->db->get_results($sql, ARRAY_A);
// Hole alle m�glichen Schreibweisen des Suchstrings / Sonderf�lle �|ss, �|Ae etc.
$sucharray = $this->sonderfall_loopen($searchfor);
// und bau aus dem Array eine sql satement zusammen
if (!empty($sucharray))
{
	foreach($sucharray as $suchstring) { $sql_sucharray .= "content LIKE '%" . $this->db->escape($suchstring) . "%' OR "; }
	$sql_sucharray = substr($sql_sucharray, 0, -4);
}
if (!empty($lookup_felder))
{
	$lookup_treffer_ids = array();
	// gehe die Felder durch
	foreach($lookup_felder as $lookup_feld)
	{
		// gibt es f�r dieses Feld einen content Eintrag, der den Suchstring beinhaltet?
		$sql = sprintf("SELECT lookup_id,
								content
								FROM %s 
								WHERE (%s)
								AND lang_id = '%d'",
								
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_lang_"
								. $this->db->escape($lookup_feld['mvcform_id']),
								
								$sql_sucharray,
								$this->cms->lang_back_content_id
						);
		$treffer_buffer = $this->db->get_results($sql, ARRAY_A);
		if (!empty($treffer_buffer)) $lookup_treffer_ids[$lookup_feld['mvcform_id']] = $treffer_buffer; // wenn ja, dann in Array zwischenspeichern
	}
}
// Suchmaske zusammenbauen
$felder_ok = $felder_ok_fix;
$back_or_front = "_back";
require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_suchmaske.php');
#$this->make_suchmaske($felder_ok_fix, $search_mv_id, "_back");
// wenn es eine Kalenderverwaltung (3) ist, dann nur aktuelle Beitr�ge anzeigen
$sql = sprintf("SELECT mv_art
						FROM %s 
						WHERE mv_id = '%d'",
						$this->cms->tbname['papoo_mv'],
						$this->db->escape($this->checked->mv_id)
				);
$mv_art = $this->db->get_var($sql);
//METAFIX Noch notwendig hier?
if ($mv_art == "3")
{
	// hole das erste Timestamp/Zeitintervall Feld aus der Datenbank
	$sql = sprintf("SELECT MIN(mvcform_id)
							FROM %s 
							WHERE mvcform_meta_id = '%d'
							AND	mvcform_form_id = '%d'
							AND	mvcform_kalender = '1'
							AND (mvcform_type = 'timestamp'
							OR mvcform_type = 'zeitintervall')",
							$this->cms->tbname['papoo_mvcform'],
							$this->db->escape($this->meta_gruppe),
							$this->db->escape($this->checked->mv_id)
					);
	$aktuell_feld_id = $this->db->get_var($sql);
	if (!empty($aktuell_feld_id))
	{
		// so jetzt aus dem Feldnamen und der Feldid eine zeitliche Beschr�nkung zusammenbauen
		$sql = sprintf("SELECT mvcform_name,
								mvcform_type
								FROM %s 
								WHERE mvcform_id = '%d' 
								AND mvcform_meta_id = '%d'
								AND mvcform_form_id = '%d'
								LIMIT 1",
								$this->cms->tbname['papoo_mvcform'],
								$this->db->escape($aktuell_feld_id),
								$this->db->escape($this->meta_gruppe),
								$this->db->escape($this->checked->mv_id)
						);
		$aktuell_feld = $this->db->get_results($sql, ARRAY_A);
		$zeitfeld_min_date = $aktuell_timestamp = $this->unixtime_to_longtime(time());
		$searchfeld_aktuell_timestamp = $this->get_longtime(date("Y"), date("m"), date("d"));

		// wenns ein Timestamp Feld, dann muss der Zeitstempel auch gr��er als der vom heutigen Tag sein
		if ($aktuell_feld[0]['mvcform_type'] == "timestamp") $kalender_extra = $aktuell_feld[0]['mvcform_name']
																				. "_" .
																				$aktuell_feld_id
																				. " > '"
																				. $searchfeld_aktuell_timestamp
																				. "' AND ";
		// wenns ein Zeitintervall Feld ist, dann muss der zweite Timestamp(nach dem ,) gr��er sein als der von heute
		else $kalender_extra = "SUBSTRING_INDEX("
								. $aktuell_feld[0]['mvcform_name']
								. "_"
								. $aktuell_feld_id
								. ", ',', -1) >= '"
								. $searchfeld_aktuell_timestamp
								. "' AND ";
		// und nach dem feld auch Sortieren lassen
		$order_by_kalenderfeld = " ORDER BY " . $aktuell_feld[0]['mvcform_name'] . "_" . $aktuell_feld_id;
	}
}
// wenns ein Kalenderfeld gibt, dann danach sortieren
if ($order_by_kalenderfeld != "") $order_by = $order_by_kalenderfeld . $this->kalender_order;
// wenn es keine Errors gibt, dann suchen
if (empty($this->content->template['error']))
{
	$felder_maske = "";
	// sucht die Felder, die in der Suchmaske f�r die Verwaltung mv_id extra erscheinen sollen 
	$sql = sprintf("SELECT mvcform_id,
							mvcform_name,
							mvcform_type
							FROM %s 
							WHERE mvcform_search_back = '1' 
							AND mvcform_form_id = '%s' 
							AND mvcform_meta_id = '%d'",
							$this->cms->tbname['papoo_mvcform'],
							$this->db->escape($this->checked->mv_id),
							$this->db->escape($this->meta_gruppe)
					);
	$result = $this->db->get_results($sql);
	if (!empty($result))
	{
		$zeitfeld_min_date = $this->unixtime_to_longtime(time());
		foreach($result as $row)
		{
			// Darf das Feld auch von diesem Benutzer durchsucht werden(Leserechte)
			if (array_search($row->mvcform_id, $felder_ok_fix))
			{
				$name_id = $row->mvcform_name . "_" . $row->mvcform_id;
				$searchfeld = $this->db->escape($this->checked->$name_id);
				#if ($row->mvcform_type == "picture"
				#	|| $row->mvcform_type == "file")
				#{
				#	if ($searchfeld == "1") $felder_maske .= $row->mvcform_name . "_" . $row->mvcform_id . " <> '' AND ";
				#}
				#elseif($row->mvcform_type == "preisintervall")
				if($row->mvcform_type == "preisintervall")
				{
					$name_min = "mvcform_min_" . $row->mvcform_name . "_" . $row->mvcform_id;
					$name_max = "mvcform_max_" . $row->mvcform_name . "_" . $row->mvcform_id;

					if ($this->checked->$name_min != "") $felder_maske .= $row->mvcform_name
																		. "_"
																		. $row->mvcform_id
																		. " > "
																		. $this->db->escape($this->checked->$name_min)
																		. " AND ";
					if ($this->checked->$name_max != "") $felder_maske .= $row->mvcform_name
																		. "_"
																		. $row->mvcform_id
																		. " < "
																		. $this->db->escape($this->checked->$name_max)
																		. " AND ";
				}
				elseif($row->mvcform_type == "radio"
					|| $row->mvcform_type == "check"
					|| $row->mvcform_type == "picture"
					|| $row->mvcform_type == "galerie"
					|| $row->mvcform_type == "file")
				{
					if ($row->mvcform_type != "radio")
					{
						if ($searchfeld != "")
						{
							//$label = $this->get_lp_wert_search_tab_ms($row->mvcform_id, $row->mvcform_name, $searchfeld, $this->cms->lang_back_content_id);
							if ($searchfeld == "0")
								$felder_maske .= "(" . $row->mvcform_name . "_" . $row->mvcform_id . " = '0' OR " . $row->mvcform_name . "_" . $row->mvcform_id . "='') AND ";
							//$label = $this->get_lp_wert_search_tab_ms($row->mvcform_id, $row->mvcform_name, $searchfeld, $this->cms->lang_back_content_id);
							#if ($searchfeld == "1") $felder_maske .= $row->mvcform_name . "_" . $row->mvcform_id . " != '' AND " . $row->mvcform_name . "_" . $row->mvcform_id . " != 0 AND ";
							else $felder_maske .= $row->mvcform_name . "_" . $row->mvcform_id . " != '' AND " . $row->mvcform_name . "_" . $row->mvcform_id . " != 0 AND ";
						}
					}
					else // Radio
					{
						//$label = $this->get_lp_wert_search_tab_ms($row->mvcform_id, $row->mvcform_name, $searchfeld, $this->cms->lang_back_content_id);
						if ($searchfeld != "") $felder_maske .= $row->mvcform_name . "_" . $row->mvcform_id . " = '" . $this->db->escape($searchfeld) . "' AND ";	
					}
				}
				elseif($row->mvcform_type == "multiselect")
				{
					if ($searchfeld != "") $felder_maske .= $row->mvcform_name . "_" . $row->mvcform_id . " REGEXP '[[:<:]]" . $this->db->escape($searchfeld) . "[[:>:]]' AND ";
				}
				elseif($row->mvcform_type == "select")
				{
					if ($searchfeld != "") $felder_maske .= $row->mvcform_name . "_" . $row->mvcform_id . " LIKE '" . $this->db->escape($searchfeld) . "' AND ";
				}
				elseif($row->mvcform_type == "timestamp")
				{
					$name_tag = "mvcform_tag_" . $row->mvcform_name . "_" . $row->mvcform_id;
					$name_monat = "mvcform_monat_" . $row->mvcform_name . "_" . $row->mvcform_id;
					$name_jahr = "mvcform_jahr_" . $row->mvcform_name . "_" . $row->mvcform_id;
					// wenn alle drei Felder JJ MM JJJJ nicht leer sind dann auch Suchen
					if (!empty($this->checked->$name_tag)
						&& !empty($this->checked->$name_monat)
						&& !empty($this->checked->$name_jahr))
					{
						$searchfeld = $this->get_longtime($this->checked->$name_jahr, $this->checked->$name_monat, $this->checked->$name_tag);
						if ($mv_art == 3)
						{
							$felder_maske .= $row->mvcform_name . "_" . $row->mvcform_id . " >= '" . $this->db->escape($searchfeld) . "' AND ";
							if ($zeitfeld_min_date > $searchfeld
								|| $zeitfeld_min_date == "") $zeitfeld_min_date = $searchfeld;
						}
						else $felder_maske .= $row->mvcform_name . "_" . $row->mvcform_id . " >= '" . $this->db->escape($searchfeld) . "' AND ";
					}
				}
				elseif($row->mvcform_type == "zeitintervall")
				{
					$searchfeld_anfang = $searchfeld_ende = "";
					if (is_numeric($this->checked->datum)) $searchfeld_anfang = $this->checked->datum;
					$anfang_tag = "anfang_tag_" . $row->mvcform_name . "_" . $row->mvcform_id;
					$ende_tag = "ende_tag_" . $row->mvcform_name . "_" . $row->mvcform_id;
					$anfang_monat = "anfang_monat_" . $row->mvcform_name . "_" . $row->mvcform_id;
					$ende_monat = "ende_monat_" . $row->mvcform_name . "_" . $row->mvcform_id;
					$anfang_jahr = "anfang_jahr_" . $row->mvcform_name . "_" . $row->mvcform_id;
					$ende_jahr = "ende_jahr_" . $row->mvcform_name . "_" . $row->mvcform_id;
					if (!empty($this->checked->$anfang_monat)
						&& !empty($this->checked->$anfang_tag)
						&& !empty($this->checked->$anfang_jahr)) $searchfeld_anfang = $this->get_longtime($this->checked->$anfang_jahr,
																											$this->checked->$anfang_monat,
																											$this->checked->$anfang_tag
																										);
					else $searchfeld_anfang = $this->get_longtime(date("Y"), date("m"), date("d"));
					if (!empty($this->checked->$ende_monat)
						&& !empty($this->checked->$ende_tag)
						&& !empty($this->checked->$ende_jahr)) $searchfeld_ende = $this->get_longtime($this->checked->$ende_jahr,
																											$this->checked->$ende_monat,
																											$this->checked->$ende_tag
																										);
					else $searchfeld_ende = $this->get_longtime(date("Y"), date("m"), date("d"));
					if ($mv_art == 3)
					{
						if ($searchfeld_anfang == "") $searchfeld_anfang = $zeitfeld_min_date; // aktuelles Datum
						if ($searchfeld_ende == "") $searchfeld_ende = $zeitfeld_min_date; // aktuelles Datum
						$felder_maske .= "SUBSTRING_INDEX("
										. $row->mvcform_name
										. "_"
										. $row->mvcform_id
										. ", ',', -1) >= '"
										. $this->db->escape($searchfeld_anfang)
										. "' AND "
										. "SUBSTRING_INDEX("
										. $row->mvcform_name
										. "_"
										. $row->mvcform_id
										. ", ',', 1) <= '"
										. $this->db->escape($searchfeld_ende)
										. "' AND ";
						$kalender_extra2 = "SUBSTRING_INDEX(" . $row->mvcform_name . "_" . $row->mvcform_id . ", ',', -1) >= 'xxxx' AND ";
						if ($zeitfeld_min_date > $searchfeld_anfang
							|| $zeitfeld_min_date == "") $zeitfeld_min_date = $searchfeld_anfang;
					}
					else
					{
						$felder_maske .= "SUBSTRING_INDEX(" . $row->mvcform_name . "_" . $row->mvcform_id . ", ',', -1) >= '" . $this->db->escape($searchfeld_anfang) . "' AND "; 
						$kalender_extra2 = "SUBSTRING_INDEX(" . $row->mvcform_name . "_" . $row->mvcform_id . ", ',', -1) >= 'xxxx' AND ";
					}
				}
				else
				{
					if ($searchfeld != "")
					{
						// holt die m�glichen Schreibweisen f�r den Suchstring
						$searchfeld_array = $this->sonderfall_loopen($searchfeld);
						// wenn es ein Feld ist, das nur von Anfang an durchsucht werden soll, dann %s weglassen
						$prozentzeichen = "%";
						if (!empty($this->search_from_beginn))
						{
							foreach($this->search_from_beginn as $search_from_beginn_id)
							{
								if ($search_from_beginn_id == $row->mvcform_id) $prozentzeichen = "";
							}
						}
						$felder_maske .= "(";
						foreach($searchfeld_array as $searchfeld_schreibweise)
						{
							$felder_maske .= $row->mvcform_name . "_" . $row->mvcform_id . " LIKE '" . $prozentzeichen . $this->db->escape($searchfeld_schreibweise) . "%' OR ";
						}
						$felder_maske = substr($felder_maske, 0, -4); // letztes OR wieder raus
						$felder_maske .= ") AND ";
					}
				}
			}
		}
		$felder_maske = substr($felder_maske, 0, -5); // letztes ' AND ' wieder rausnehmen
		if ($felder_maske != "") $felder_maske = "(" . $felder_maske . ")";
	}
	// baut eien WHERE Klausel aus den Gruppen zusammen
	if (!empty($this->gruppen_ids))
	{
		foreach($this->gruppen_ids as $gruppe_id) { $felder_gruppen .= "group_id='" . $this->db->escape($gruppe_id) . "' OR "; }
		$felder_gruppen = substr($felder_gruppen, 0, -4); // letztes ' AND ' wieder rausnehmen
		if ($felder_gruppen != "") $felder_gruppen = "(" . $felder_gruppen . ") AND ";
	}
	// holt die Felder und Werte f�r die F�lle, dass die Gruppe den Beitrag sehen darf
	$sql = sprintf("SELECT * FROM %s, %s 
								WHERE %s field_id = mvcform_id 
								AND mvcform_meta_id = '%d'",
								
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_group_rights",
								
								$this->cms->tbname['papoo_mvcform'],
								
								$felder_gruppen,
								$this->db->escape($this->meta_gruppe)
					);
	$felder_gruppen_werte = $this->db->get_results($sql, ARRAY_A);
	$felder_gruppen = "";
	if ($felder_gruppen != "")
	{
		$felder_gruppen = substr($felder_gruppen, 0, -4);      // letztes ' OR ' wieder rausnehmen
		$felder_gruppen_ow = " AND (" . $felder_gruppen . ")"; // f�r Volltextsuche und Feldtextsuche
		$felder_gruppen = " WHERE " . $felder_gruppen;
	}
	// Welche Felder sollen �berhaupt durchsucht werden?
	$sql = sprintf("SELECT mvcform_id,
							mvcform_name,
							mvcform_type
							FROM %s 
							WHERE mvcform_form_id = '%d' 
							AND mvcform_meta_id = '%d' 
							AND mvcform_aktiv = '1' 
							AND mvcform_normaler_user = '1'",
							$this->cms->tbname['papoo_mvcform'],
							$this->db->escape($this->checked->mv_id),
							$this->db->escape($this->meta_gruppe)
					);
	$such_felder = $this->db->get_results($sql, ARRAY_A);
	$such_felder_sql = "";
	// baut aus den sql Treffern die Feldernamen f�r sql Statement zusammen
	if (!empty($such_felder))
	{
		$such_felder_sql = "mv_content_id, mv_content_owner, mv_content_userid, mv_content_edit_date, mv_content_create_owner, mv_content_edit_user, ";
		foreach($such_felder as $such_feld)
		{
			$such_felder_sql .= $such_feld['mvcform_name'] . "_" . $such_feld['mvcform_id'] . ", ";
			$this->felder_typen[$such_feld['mvcform_name'] . "_" . $such_feld['mvcform_id']] = $such_feld['mvcform_type'];
		}
		$such_felder_sql = substr($such_felder_sql, 0, -2); // letztes Komma wieder weg
	}
	if ($zeitfeld_min_date < $aktuell_timestamp
		&& $zeitfeld_min_date != "") $kalender_extra = "";
	// wurde ein �nderungsdatumsbereich angegeben? dann in diesem Zeitintervall suchen
	$search_changedate = "";
	$ende_tag_changedate = $anfang_tag_changedate = date("d", time());
	$ende_monat_changedate = $anfang_monat_changedate = date("m", time());
	$ende_jahr_changedate = $anfang_jahr_changedate = date("Y", time());
	if (!empty($this->checked->anfang_tag_changedate)
		&& !empty($this->checked->anfang_monat_changedate)
		&& !empty($this->checked->anfang_jahr_changedate)
		&& !empty($this->checked->ende_tag_changedate)
		&& !empty($this->checked->ende_monat_changedate)
		&& !empty($this->checked->ende_jahr_changedate)
		&& ($this->checked->anfang_tag_changedate != $anfang_tag_changedate
			|| $this->checked->anfang_monat_changedate != $anfang_monat_changedate
			|| $this->checked->anfang_jahr_changedate != $anfang_jahr_changedate
			|| $this->checked->ende_tag_changedate != $ende_tag_changedate
			|| $this->checked->ende_monat_changedate != $ende_monat_changedate
			|| $this->checked->ende_jahr_changedate != $ende_jahr_changedate))
	{
		$anfang_timestamp = $this->get_longtime($this->checked->anfang_jahr_changedate, $this->checked->anfang_monat_changedate, $this->checked->anfang_tag_changedate);
		$ende_timestamp = $this->get_longtime($this->checked->ende_jahr_changedate, $this->checked->ende_monat_changedate, $this->checked->ende_tag_changedate, "23", "59", "59");
		$search_changedate = " AND mv_content_edit_date >= '"
								. $this->db->escape($anfang_timestamp)
								. "' AND mv_content_edit_date <= '"
								. $this->db->escape($ende_timestamp)
								. "' ";
	}
	$mv_lp_meta = "mv_meta_main_lp_meta_id";
	$mv_lp_mv_id = "mv_meta_main_lp_mv_id";
	$mv_lp_tabelle = "papoo_mv_meta_main_lp";
	$mv_lp_user_id = "mv_meta_main_lp_user_id";
	// Konfig-Variable mv_meta_show_all: Alle Metaebenen bei der Suche ber�cksichtigen
	if ($this->mv_meta_show_all
		|| ($this->checked->mv_id == $this->dzvhae_mv_id
			&& $this->dzvhae_system_id
			&& $this->meta_gruppe == "1"))
	{
		$mv_lp_meta = "mv_meta_lp_meta_id";
		$mv_lp_mv_id = "mv_meta_lp_mv_id";
		$mv_lp_tabelle = "papoo_mv_meta_lp";
		$mv_lp_user_id = "mv_meta_lp_user_id";
	}

	//****************************************************************************************************
	// wenn Volltextsuche und Suchfelder leer dann Gesamtliste ausgeben
	//****************************************************************************************************
	if (empty($searchfor)
		&& empty($felder_maske))
	{
		$sql = sprintf("SELECT COUNT(DISTINCT(mv_content_id))
								FROM %s, %s %s 
								WHERE mv_content_id = %s 
								AND %s = '%d'
								AND %s = '%d' %s",
								
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_search_"
								. $this->db->escape($this->cms->lang_back_content_id),
								
								$this->cms->tbname[$mv_lp_tabelle],
								
								$felder_gruppen,
								$mv_lp_user_id,
								$mv_lp_meta,
								$this->db->escape($this->meta_gruppe),
								$mv_lp_mv_id,
								$this->db->escape($this->checked->mv_id),
								$search_changedate
						);
		$anzahl = $this->db->get_var($sql);
		// ??? s. 887 such_treffer_anzahl
		$this->weiter->make_limit($this->intervall_back);
		// Anzahl der Ergebnisse aus der Suche Eigenschaft �bergeben

        if(!isset($_SESSION['nl'])) {
            $_SESSION['nl'] = NULL;
        }

		$_SESSION['nl']['mv_newsletter_anzahl'] = $this->content->template['anzahl'] = $this->weiter->result_anzahl = $anzahl;
		$gets = $this->get_get_vars();
		$this->weiter->weiter_link = "plugin.php?" . $gets;
		$this->weiter->modlink = "no";
		$this->weiter->do_weiter("teaser");
		// ka ob es denn Fall �berhaupt gibt, aber sicher ist sicher
		if ($felder_gruppen == "") $felder_gruppen = " WHERE ";
		// z. B. mv_content_id, board_6, signatur_8, email_3, Benutzername_1 in $such_felder_sql
		if (!empty($such_felder_sql))
		{
			$sql = sprintf("SELECT DISTINCT(mv_content_id),
									mv_content_edit_date,
									%s
									FROM %s, %s %s 
		  							mv_content_id = %s 
		  							AND %s = '%d' 
		  							AND %s = '%d' %s %s %s",
									$such_felder_sql,
									
									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($this->checked->mv_id)
									. "_search_"
									. $this->db->escape($this->cms->lang_back_content_id),
									
									$this->cms->tbname[$mv_lp_tabelle],
									
									$felder_gruppen,
									$mv_lp_user_id,
									$mv_lp_meta,
									$this->db->escape($this->meta_gruppe),
									$mv_lp_mv_id,
									$this->db->escape($this->checked->mv_id),
									$search_changedate,
									$order_by,
									$this->db->escape($this->weiter->sqllimit)
							);
			$result_treffer = $this->db->get_results($sql);
			if ($felder_gruppen != " WHERE ") $felder_gruppen .= " AND ";
			$sql_buffer = sprintf("SELECT DISTINCT *
										FROM %s, %s 
										%s 
										mv_content_id = %s 
										AND %s = '%d' 
										AND %s = '%d' %s %s",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($this->checked->mv_id)
										. "_search_"
										. $this->db->escape($this->cms->lang_back_content_id),
										
										$this->cms->tbname[$mv_lp_tabelle],
										
										$felder_gruppen,
										$mv_lp_user_id,
										$mv_lp_meta,
										$this->db->escape($this->meta_gruppe),
										$mv_lp_mv_id,
										$this->db->escape($this->checked->mv_id),
										$search_changedate,
										$order_by
								);
		}
	}
	//****************************************************************************************************
	// ansonsten richtig suchen
	//****************************************************************************************************
	else
	{
		if (!empty($searchfor))
		{
			$sucharray = $this->sonderfall_loopen($searchfor);
			// holt die Formularfelder IDs aus der Datenbank die f�r normale User erlaubt sind
			$sql = sprintf("SELECT mvcform_name,
									mvcform_id,
									mvcform_type
									FROM %s 
									WHERE mvcform_type <> 'password' 
									AND mvcform_normaler_user = '1' 
									AND mvcform_form_id = '%d' 
									AND mvcform_meta_id = '%d'",
									$this->cms->tbname['papoo_mvcform'],
									$this->db->escape($this->checked->mv_id),
									$this->db->escape($this->meta_gruppe)
							);
			$result = $this->db->get_results($sql);
			// Tabellenname content_X
			$tab_content = $this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id)
							. "_search_"
							. $this->cms->lang_back_content_id;
			if (!empty($result))
			{
				// und baut daraus den Suchstring
				foreach($result as $row)
				{
					// Darf das Feld auch von diesem Benutzer durchsucht werden(Leserechte)
					if (array_search($row->mvcform_id, $felder_ok_fix))
					{
						switch($row->mvcform_type)
						{
							// Feldertypen die ignoriert werden k�nnen
							case "timestamp":
							case "zeitintervall":
							case "hidden":
							case "password": break;
							#case "galerie": break;
							// Der Rest wird durchsucht
							default:
								// Suchabfrage f�r dieses Feld
								foreach($sucharray as $searchfor)
								{
									// wenn es ein multiselect, select, radio, check Feld ist und der Suchbegriff in den Optionswerten vorkommt
									if (!empty($lookup_treffer_ids[$row->mvcform_id]))
									{
										foreach($lookup_treffer_ids[$row->mvcform_id] as $lookup_treffer_id)
										{
											$felder .= $row->mvcform_name . "_" . $row->mvcform_id . " LIKE '" . $this->db->escape($lookup_treffer_id['lookup_id']) . "' OR ";
										}
									}
									// sonst hier entlang
									else $felder .= $row->mvcform_name . "_" . $row->mvcform_id . " LIKE '%" . $this->db->escape($searchfor) . "%' OR ";
								}
								break;
						}
					}
				}
				if ($felder != "")
				{
					$felder = substr($felder, 0, -4); // letztes ' OR ' wieder rausnehmen
					$felder = "(" . $felder . ")";
				}
			}
		}
		if ($felder != ""
			&& $felder_maske != "") $felder = " AND " . $felder;

		if ($felder_maske != ""
			|| $felder != ""
			|| $felder_gruppen_ow != "") $and = " AND ";
		#$sichtbar = $this->get_sichtbar_feld_fuer_dzvhae(); // Nur im FE ?
		$sql = sprintf("SELECT COUNT(DISTINCT(mv_content_id))
									FROM %s, %s 
									WHERE %s %s %s %s
									mv_content_id = %s 
									AND %s = '%d'
									AND %s = '%d'
									%s",
									
									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($this->checked->mv_id)
									. "_search_"
									. $this->db->escape($this->cms->lang_back_content_id),
									
									$this->cms->tbname[$mv_lp_tabelle],
									
									$felder_maske,
									$felder,
									$felder_gruppen_ow,
									$and,
									$mv_lp_user_id,
									$mv_lp_meta,
									$this->db->escape($this->meta_gruppe),
									$mv_lp_mv_id,
									$this->db->escape($this->checked->mv_id),
									$search_changedate
						); // die normalen Felder
		$anzahl = $this->db->get_var($sql);
		$this->weiter->make_limit($this->intervall_back);

        if(!isset($_SESSION['nl'])) {
            $_SESSION['nl'] = NULL;
        }

		// Anzahl der Ergebnisse aus der Suche Eigenschaft �bergeben
		$this->weiter->result_anzahl = $_SESSION['nl']['mv_newsletter_anzahl'] = $this->content->template['anzahl'] = $anzahl;
		$gets = $this->get_get_vars();
		$this->weiter->weiter_link = "plugin.php?" . $gets;
		$this->weiter->modlink = "no";
		$this->weiter->do_weiter("teaser");
		// und sucht damit in der Content Tabelle nach dem Suchbegriff
		if (!empty($such_felder_sql))
		{
			$sql_buffer = $sql = sprintf("SELECT DISTINCT *
											FROM %s, %s 
											WHERE %s %s %s %s
											mv_content_id = %s 
											AND %s = '%d'
											AND %s = '%d'
											%s
											%s
											%s",
											#$such_felder_sql,
											
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($this->checked->mv_id)
											. "_search_"
											. $this->db->escape($this->cms->lang_back_content_id),
											
											$this->cms->tbname[$mv_lp_tabelle],
											
											$felder_maske,
											$felder,
											$felder_gruppen_ow,
											$and,
											$mv_lp_user_id,
											$mv_lp_meta,
											$this->db->escape($this->meta_gruppe),
											$mv_lp_mv_id,
											$this->db->escape($this->checked->mv_id),
											$search_changedate,
											$order_by,
											$this->db->escape($this->weiter->sqllimit)
						);
			$result_treffer = $this->db->get_results($sql);
		}
	}
	// Trefferanzahl ans Template
	$this->content->template['such_treffer_anzahl'] = $anzahl;
	$this->content->template['mv_uebersicht_liste'] = $this->object_to_array($result_treffer);
	if (!empty($result_treffer))
	{
		$result = $result_treffer;
		$hit = "";
		$back_or_front = "mvcform_list";
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/print_treffer_liste.php');
		#$this->print_treffer_liste($result_treffer, "", "mvcform_list");
		$this->content->template['buffer_liste'] .= $this->content->template['mv_liste'];
	}
	else $this->content->template['message'] = "no_content"; // Ausgeben, dass es keine Treffer gab
}
// Holt alle Sortierfelder aus der Datenbank
$sql = sprintf("SELECT mvcform_id,
						mvcform_name,
						mvcform_label
						FROM %s, %s 
						WHERE mvcform_id = mvcform_lang_id 
						AND mvcform_meta_id = '1' 
						AND mvcform_meta_id = mvcform_lang_meta_id 
						AND mvcform_form_id = '%d' 
						AND mvcform_sort <> '0' 
						AND mvcform_lang_lang = '%d'
						GROUP BY mvcform_id",
						$this->cms->tbname['papoo_mvcform'],
						$this->cms->tbname['papoo_mvcform_lang'],
						$this->db->escape($this->checked->mv_id),
						$this->db->escape($this->cms->lang_back_content_id)
				);
$felder_sortn = $this->db->get_results($sql, ARRAY_A);
$this->content->template['felder_sort'] = $felder_sortn;
// f�gt noch ein extra Suchfeld f�r den �nderungsdatumsbereich hinzu
$this->make_edit_changedate();
// Sonderfall dzvh�: Schnellfunktionen
if ($this->dzvhae_system_id)
{
	if (!$this->checked->mv_fb_kuendigung
		AND !$this->checked->mv_fb_kuendigung_really)
	{
		$sql = sprintf("SELECT DISTINCT(mv_content_id)
								FROM %s, %s 
								WHERE %s %s %s %s 
								mv_content_id = %s 
								AND %s = '%d'
								AND %s = '%d' %s",
								
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_search_"
								. $this->db->escape($this->cms->lang_back_content_id),
								
								$this->cms->tbname[$mv_lp_tabelle],
								
								$felder_maske,
								$felder,
								$felder_gruppen_ow,
								$and,
								$mv_lp_user_id,
								$mv_lp_meta,
								$this->db->escape($this->meta_gruppe),
								$mv_lp_mv_id,
								$this->db->escape($this->checked->mv_id),
								$search_changedate
						); // die normalen Felder
		$this->content->template['mv_content_id'] = $special_action_ids = $this->db->get_results($sql, ARRAY_A);
	}
	$this->check_special_right(); // Recht f�r K�ndigungsbutton ermitteln
	// ausgwaehlte Trefferliste die Mitgliedschaft k�ndigen
	if (!empty($this->checked->mv_fb_kuendigung)) $this->do_dzvhae_kuendigen_alle_gesuchten();
	if (!empty($this->checked->mv_fb_kuendigung_really)) $this->do_dzvhae_kuendigen_alle_gesuchten_wirklich();
	if (!empty($this->checked->mv_weitere_ebene_wechseln)) // Button "Jetzt �ndern"
	{
		if ($this->checked->action_main_weitere_new == "delete"
			AND !$this->checked->submitdelecht)
		{
			$this->content->template['mv_id'] = $this->checked->mv_id;
			$this->content->template['main_weitere_new'] = $this->checked->main_weitere_new;
			$this->content->template['fragedel'] = "ok";
		}
		else
		{
			$sql = sprintf("SELECT DISTINCT(mv_content_id)
									FROM %s, %s 
									WHERE %s %s %s %s 
									mv_content_id = %s 
									AND %s = '%d'
									AND %s = '%d' %s",
									
									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($this->checked->mv_id)
									. "_search_"
									. $this->db->escape($this->cms->lang_back_content_id),
									
									$this->cms->tbname[$mv_lp_tabelle],
									
									$felder_maske,
									$felder,
									$felder_gruppen_ow,
									$and,
									$mv_lp_user_id,
									$mv_lp_meta,
									$this->db->escape($this->meta_gruppe),
									$mv_lp_mv_id,
									$this->db->escape($this->checked->mv_id),
									$search_changedate
							); // die normalen Felder
			$special_action_ids = $this->db->get_results($sql, ARRAY_A);
			$this->do_weitere_meta_wechseln_alle_gesuchten($special_action_ids);
		}
	}
	// deaktiviert 27.3.10 khmweb (s. Punkt 327)
	// ausgwaehlte Trefferliste die Mitgliedschaft k�ndigen
	/*if (!empty($this->checked->mv_main_ebene_wechseln))
	{
		$mv_lp_meta = "mv_meta_main_lp_meta_id";
		$mv_lp_mv_id = "mv_meta_main_lp_mv_id";
		$mv_lp_tabelle = "papoo_mv_meta_main_lp";
		$mv_lp_user_id = "mv_meta_main_lp_user_id";
		$sql = sprintf("SELECT DISTINCT(mv_content_id)
								FROM %s, %s 
								WHERE %s %s %s %s 
								mv_content_id = %s 
								AND %s = '%d'
								AND %s = '%d' %s",
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_search_"
								. $this->db->escape($this->cms->lang_back_content_id),
								$this->cms->tbname[$mv_lp_tabelle],
								$felder_maske,
								$felder,
								$felder_gruppen_ow,
								$and,
								$mv_lp_user_id,
								$mv_lp_meta,
								$this->db->escape($this->meta_gruppe),
								$mv_lp_mv_id,
								$this->db->escape($this->checked->mv_id),
								$search_changedate
						); // die normalen Felder
		$special_action_ids = $this->db->get_results($sql, ARRAY_A);
		$this->do_main_meta_wechseln_alle_gesuchten($special_action_ids);
	}*/
}
// link ans Template
if ($search_mv_id != "") $onemv = "&onemv=" . $search_mv_id;
$this->content->template['link_mv'] = $_SERVER['PHP_SELF']
										. "?menuid="
										. $this->checked->menuid
										. "&template="
										. $this->checked->template
										. $onemv;
$this->content->template['mv_link_self'] = preg_replace("/\\/(?!plugin\\.php)[^\\/]+\$/", "/plugin.php", $_SERVER['PHP_SELF'], 1) . "?";
$this->content->template['mv_link_export'] = $_SERVER['PHP_SELF']
												. "?menuid="
												. $this->checked->menuid
												. "&template=mv/templates/imex_exportit.html&anzahl="
												. $this->content->template['such_treffer_anzahl']
												. "&tabelle_lang="
												. $this->cms->lang_back_content_id; // f�r Export Suchergebnis CSV
$this->content->template['mv_link_newsletter'] = $_SERVER['PHP_SELF']
													. "?menuid="
													. $this->checked->menuid
													. "&template=newsletter/templates/news_nl_list.html";
$this->content->template['mv_link_newsletter_lang'] = $this->cms->lang_back_content_id;
$this->content->template['mv_id'] = $this->checked->mv_id;
$this->content->template['mv_meta_id'] = $this->meta_gruppe;

if(!isset($_SESSION['nl'])) {
    $_SESSION['nl'] = NULL;
}

// in Session speichern f�r Newsletter verschicken/, CSV-Export (s. BE Suchen; 2 Links) oder Schnellfunktionskn�pfen
$pos_limit = strpos($sql_buffer, "LIMIT");
if ($pos_limit) $sql_buffer = substr($sql_buffer, 0, ($pos_limit - 1)); // " LIMIT 0,20" am Ende entfernen
$_SESSION['mv_import']['mv_sql_buffer'] = $_SESSION['nl']['mv_newsletter_sql'] = $sql_buffer;
$this->finde_die_art_der_verwaltung_heraus();
$this->content->template['mv_art_rechte'] = $this->mv_art;
?>
