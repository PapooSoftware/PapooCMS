<?php
// Vor Aufruf m�ssen folgende Variablen gesetzt werden:
// $search_mv_id oder nicht gesetzt
/**
* Mitglieder-/Objektsuche Frontend
*/
if (!isset($search_mv_id)) $search_mv_id = "";
$nervi = array();
// Suchbegriff zwischenspeichern
// leerzeichen zur wildcard in der suche machen
$searchfor = str_replace(" ", "_", preg_replace('/\s{2,}/', " ", $this->db->escape(trim($this->checked->search_mv))));
// wenn keine Verwaltungs ID mitgeschickt wurde, dann 1 nehmen
$this->checked->mv_id = is_numeric($this->checked->mv_id) ? (int)$this->checked->mv_id : 1;
//Meta ID festlegen
$meta_id = $this->meta_gruppe;
// ??? Frage nach FE ???
if (!defined("admin") && is_numeric($this->checked->extern_meta)) {
	$meta_id = $this->checked->extern_meta;
}
// Volltextsuchberiff wieder ans Template geben
$this->content->template['search_mv'] = $this->checked->search_mv;
$this->content->template['mv_mv_id'] = $this->checked->mv_id;
$this->content->template['mv_onemv'] = $this->checked->onemv;
$this->content->template['mv_id'] = $this->checked->mv_id;
$this->content->template['extern_meta'] = $this->checked->extern_meta;

// ist der erste Aufruf dieser Suchmaske
if (empty($this->checked->submit_mv)) $searchfor_start = "ok";
$order_by = " ORDER BY mv_content_id ";

$sort_feld = (!empty($this->checked->sort_feld))?((int)$this->checked->sort_feld):NULL;

if ($sort_feld === NULL and !empty($this->default_sort_field)) {
	if (is_array($this->default_sort_field)) {
		// Bei Array mv_id als Key nehmen.
		if (isset($this->default_sort_field[$this->checked->mv_id]))
			$this->default_sort_field[$this->checked->mv_id];
	}
	else
		$sort_feld = $this->default_sort_field;
}

// wenn Sortierung angegeben ist, dann auch danach sortieren, ansonsten nach mv_content_id
if ($sort_feld)
{
	$sql = sprintf("SELECT mvcform_id,
							mvcform_name
							FROM %s
							WHERE mvcform_id = %d
							AND mvcform_meta_id = %d
							AND mvcform_form_id = %d",
							$this->cms->tbname['papoo_mvcform'],
							(int)($sort_feld),
							(int)$meta_id,
							(int)$this->checked->mv_id
					);
	$felddaten = $this->db->get_results($sql, ARRAY_A);
	if ($felddaten)
		$order_by = " ORDER BY " . $this->db->escape($felddaten[0]['mvcform_name'] . "_" . $felddaten[0]['mvcform_id']) . " ";
}
$order_by = $this->db->escape($order_by);
// Sucht die Anzahl von Verwaltungen aus der Datenbank
$sql = sprintf("SELECT COUNT(mv_id)
						FROM %s
						WHERE mv_set_suchmaske = '1'",
						$this->cms->tbname['papoo_mv']
				);
$anzahl_mvs = $this->db->get_var($sql);
$this->content->template['anzahl_mvs'] = 101 + $anzahl_mvs;
// Such die Anzahl der Felder der Verwaltung
$sql = sprintf("SHOW COLUMNS FROM %s",

							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id)
							. "_search_"
							. $this->cms->lang_id
				);
$result = $this->db->get_results($sql);
// Fehlermeldungen: wenn noch kein Feld in der Verwaltung existiert
if (count($result) <= count(self::$mvContentFields)) {
	$this->content->template['error'] .= $this->content->template['plugin']['mv']['noch_kein_feld'];
}
$sql = sprintf("SELECT COUNT(mv_content_id)
						FROM %s
						WHERE mv_content_sperre != '1' AND mv_content_teaser = 1",

						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_search_"
						. $this->cms->lang_id
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
							$this->db->escape($this->cms->lang_id),
							$this->db->escape($meta_id)
				);
$result = $this->db->get_results($sql);
if ($result[0]->template_content_all == ""
	&& empty($this->nocontent_ok)) $this->content->template['error'] .= $this->content->template['plugin']['mv']['noch_kein_template'];
// Verwaltungsnamen raussuchen
$sql = sprintf("SELECT mv_name,
						mv_id
						FROM %s
						WHERE mv_set_suchmaske = '1'",
						$this->cms->tbname['papoo_mv']
				);
$mv_namen = $this->db->get_results($sql, ARRAY_A);
$this->content->template['mv_namen'] = $mv_namen;
$this->content->template['link_show_all'] = $_SERVER['PHP_SELF']
											. "?menuid="
											. $this->checked->menuid
											. "&template=mv/templates/mv_show_all_front.html";
$this->content->template['link_show_own'] = $_SERVER['PHP_SELF']
											. "?menuid="
											. $this->checked->menuid
											. "&template=mv/templates/mv_show_own_front.html";

if ($search_mv_id == "") $this->content->template['link_new_search'] = $_SERVER['PHP_SELF']
																		. "?menuid="
																		. $this->checked->menuid
																		. "&template=mv/templates/mv_search_front.html";
else $this->content->template['link_new_search'] = $_SERVER['PHP_SELF']
													. "?menuid="
													. $this->checked->menuid
													. "&template=mv/templates/mv_search_front_onemv.html";
$this->content->template['selected_reiter'] = 100 + $this->checked->mv_id;
//Wenn nur ein Eintrag vorhanden
if ($anzahl_mvs < 2) $this->content->template['id_selected_reiter'] = 100 + $this->checked->mv_id - 1;
//Mehrer Eintr�ge dann den letzten anzeigen
else $this->content->template['id_selected_reiter'] = 100 + $this->checked->mv_id;
// erstmal immer 100 + aktuelle mv_id
$this->content->template['id_selected_reiter'] = 100 + $this->checked->mv_id;
$felder_ok_fix[0] = "sonst klappt einfaches if() nicht";
if (!empty($this->gruppen_ids))
{
	foreach($this->gruppen_ids as $gruppen_id)
	{
		// holt die Felder raus, die der Benutzer mit seiner Usergruppe sehen darf
		$sql = sprintf("SELECT field_id
								FROM %s
								WHERE group_read = '1'
								AND group_id = '%d'
								AND field_right_meta_id = '%d'
								GROUP BY field_id",

								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_field_rights",
								$this->db->escape($gruppen_id),

								$meta_id);
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
						AND mvcform_normaler_user = '1'
						AND mvcform_aktiv = 1",
						$this->cms->tbname['papoo_mvcform'],
						$this->db->escape($meta_id),
						$this->db->escape($this->checked->mv_id)
				);
$lookup_felder = $this->db->get_results($sql, ARRAY_A);
// Hole alle m�glichen Schreibweisen des Suchstrings / Sonderf�lle �|ss, Umlaute etc.
//$anfang_time=microtime();
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
// wenn es eine Kalenderverwaltung (3) ist, dann nur aktuelle Beitr�ge anzeigen
$sql = sprintf("SELECT mv_art
						FROM %s
						WHERE mv_id = '%d'",
						$this->cms->tbname['papoo_mv'],
						$this->db->escape($this->checked->mv_id)
				);
$mv_art = $this->db->get_var($sql);
//METAFIX Noch notwendig hier?
if ($mv_art == "3") // Kalender
{
	// hole das erste Timestamp/Zeitintervall Feld aus der Datenbank
	$sql = sprintf("SELECT MIN(mvcform_id)
							FROM %s
							WHERE mvcform_meta_id = '%d'
							AND	mvcform_form_id = '%d'
							AND	mvcform_kalender = '1'
							AND	(mvcform_type = 'timestamp'
							OR mvcform_type = 'zeitintervall')",
							$this->cms->tbname['papoo_mvcform'],
							$this->db->escape($meta_id),
							$this->db->escape($this->checked->mv_id)
					);
	$aktuell_feld_id = $this->db->get_var($sql);
	if (!empty($aktuell_feld_id))
	{
		// aus dem Feldnamen und der Feldid eine zeitliche Beschr�nkung zusammenbauen
		$sql = sprintf("SELECT mvcform_name,
								mvcform_type
								FROM %s
								WHERE mvcform_id = '%d'
								AND mvcform_meta_id = '%d'
								AND mvcform_form_id = '%d'
								LIMIT 1",
								$this->cms->tbname['papoo_mvcform'],
								$this->db->escape($aktuell_feld_id),
								$this->db->escape($meta_id),
								$this->db->escape($this->checked->mv_id)
						);
		$aktuell_feld = $this->db->get_results($sql, ARRAY_A);
		$aktuell_timestamp = time();
		$zeitfeld_min_date = $aktuell_timestamp;
		$searchfeld_aktuell_timestamp = $this->get_longtime(date("Y"), date("m"), date("d"));
		// wenns ein Timestamp Feld, dann muss der Zeitstempel auch gr��er als der vom heutigen Tag sein
		if ($aktuell_feld[0]['mvcform_type'] == "timestamp") $kalender_extra = $aktuell_feld[0]['mvcform_name']
																				. "_" .
																				$aktuell_feld_id
																				. ">'" .
																				$searchfeld_aktuell_timestamp
																				. " AND ";
		// wenns ein Zeitintervall Feld ist, dann muss der zweite Timestamp(nach dem ,) gr��er sein als der von heute
		else $kalender_extra = "SUBSTRING_INDEX("
								. $aktuell_feld[0]['mvcform_name']
								. "_"
								. $aktuell_feld_id
								. ", ',', -1)>='"
								. $searchfeld_aktuell_timestamp
								. "' AND ";
		// und nach dem feld auch Sortieren lassen
		$order_by_kalenderfeld = " ORDER BY " . $aktuell_feld[0]['mvcform_name'] . "_" . $aktuell_feld_id;
	}
}
// wenns ein Kalenderfeld gibt, dann danach sortieren, aber nur, wenn kein andres Sortfeld vorgegeben wurde
if ($order_by_kalenderfeld != ""
	AND $order_by == "mv_content_id") $order_by = $order_by_kalenderfeld . $this->kalender_order;
	else
	$order_by = $order_by . $this->kalender_order;


// wenn es keine Fehler gibt, dann suchen
if (empty($this->content->template['error']))
{
	$felder_maske = array();
	// sucht die Felder, die f�r die Verwaltung mv_id in der Suchmaske extra erscheinen sollen
	$sql = sprintf("SELECT mvcform_id,
							mvcform_name,
							mvcform_type
							FROM %s
							WHERE mvcform_search = '1'
							AND mvcform_form_id = '%s'
							AND mvcform_meta_id = '%d'",
							$this->cms->tbname['papoo_mvcform'],
							$this->db->escape($this->checked->mv_id),
							$this->db->escape($meta_id)
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
				// Markierung f�r neue Feldtypen
				#if ($row->mvcform_type == "picture"
				#	|| $row->mvcform_type == "file")
				#{
				#	if ($searchfeld == "1") $felder_maske[] = $row->mvcform_name . "_" . $row->mvcform_id . " <> ''";
				#}
				#elseif($row->mvcform_type == "preisintervall")
				if($row->mvcform_type == "preisintervall")
				{
					$name_min = "mvcform_min_" . $row->mvcform_name . "_" . $row->mvcform_id;
					$name_max = "mvcform_max_" . $row->mvcform_name . "_" . $row->mvcform_id;

					if ($this->checked->$name_min != "") $felder_maske[] = $row->mvcform_name
																		. "_"
																		. $row->mvcform_id
																		. " >= "
																		. $this->db->escape($this->checked->$name_min);
					if ($this->checked->$name_max != "") $felder_maske[] = $row->mvcform_name
																		. "_"
																		. $row->mvcform_id
																		. " <= "
																		. $this->db->escape($this->checked->$name_max);
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
							if ($searchfeld == "0")
								$felder_m = "(" . $row->mvcform_name . "_" . $row->mvcform_id . " = '0' OR " . $row->mvcform_name . "_" . $row->mvcform_id . " = '')";
							if ($searchfeld) $felder_m = "(" . $row->mvcform_name . "_" . $row->mvcform_id . " != '0' AND " . $row->mvcform_name . "_" . $row->mvcform_id . " != '')";
							// damit checkboxen statt mit AND mit OR bei Bedarf verkn�pft werden k�nnen
							if ($row->mvcform_type == "check") $felder_maske_checkbox[] .= $felder_m;
							else $felder_maske[] .= felder_m;
							$felder_m = "";
						}
					}
					elseif ($searchfeld != "") $felder_maske[] = $row->mvcform_name . "_" . $row->mvcform_id . " = '" . $this->db->escape($searchfeld) . "'";
				}
				/**elseif($row->mvcform_type=="radio" || $row->mvcform_type=="check")
				{
				if($searchfeld!="")
				{
					$felder_maske[] = $row->mvcform_name."_".$row->mvcform_id." = '".$this->db->escape($searchfeld)."'";
				}
				}*/
				elseif($row->mvcform_type == "multiselect")
				{
					if ($searchfeld != "") $felder_maske[] = $row->mvcform_name . "_" . $row->mvcform_id . " REGEXP '[[:<:]]" . $this->db->escape($searchfeld) . "[[:>:]]'";
				}
				elseif($row->mvcform_type == "select")
				{
					// Ermoegliche Filtern nach mehreren Optionen (Whitelist (ohne !) oder Blacklist (mit !))
					// [!]option_id[,option_id]...
					// @edited 2018-01-17 <cz>
					if (preg_match('~^(?<blacklist>!|)(?<options>\d.*)~', trim($searchfeld), $match)) {
						$isBlacklist = $match["blacklist"] === "!";
						$selectedOptions = array_unique(array_map(function ($option) {
							return (int)$option;
						}, explode(",", $match["options"])));

						if (count($selectedOptions) > 0) {
							$felder_maske[] = "{$row->mvcform_name}_{$row->mvcform_id} ".($isBlacklist ? "NOT " : "")."IN (".implode(", ", $selectedOptions).")";
						}
					}
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
							$felder_maske[] = $row->mvcform_name . "_" . $row->mvcform_id . " >= '" . $this->db->escape($searchfeld) . "'";
							if ($zeitfeld_min_date > $searchfeld
								|| $zeitfeld_min_date == "") $zeitfeld_min_date = $searchfeld;
						}
						else $felder_maske[] = $row->mvcform_name . "_" . $row->mvcform_id . " >= '" . $this->db->escape($searchfeld) . "'";
					}
					else if (is_numeric($this->checked->$name_jahr)) {
						// Ermoegliche Suche im Zeitraum eines ganzen Jahres
						// @edited 2018-01-12 <cz>
						$year = (int)$this->checked->$name_jahr;
						$column = "`{$row->mvcform_name}_{$row->mvcform_id}`";
						$felder_maske[] = "$column >= '$year.01.01 00:00:00' AND $column <= '$year.12.31 23:59:59'";
					}
					else // zu Beginn alle ab heute und zuk�nftige
					{
						if ($mv_art == 3) // Kalender
						{
							if (!$this->checked->mv_submit)
							{
								if (empty($this->checked->sort_feld))
								{
									$order_by = "ORDER BY "
												. $row->mvcform_name
												. "_"
												. $row->mvcform_id;
								}
								$felder_maske[] = "SUBSTRING_INDEX("
												. $row->mvcform_name
												. "_"
												. $row->mvcform_id
												. ", ',', -1) >= '"
												. $this->db->escape($zeitfeld_min_date)
												. "'";
							}
						}
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
						// zu Beginn alle ab heute und zuk�nftige
						if (!$this->checked->mv_submit)
						{
							// Nur, wenn keine Vorgabe (beim 1. Aufruf ohne Vorgabe)
							if (empty($this->checked->sort_feld))
							{
								$order_by = "ORDER BY "
											. $row->mvcform_name
											. "_"
											. $row->mvcform_id;
							}
							$felder_maske[] = "SUBSTRING_INDEX("
											. $row->mvcform_name
											. "_"
											. $row->mvcform_id
											. ", ',', -1) >= '"
											. $this->db->escape($searchfeld_anfang)
											. "'";
						}
						else $felder_maske[] = "SUBSTRING_INDEX("
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
										. "'";
						$kalender_extra2 = "SUBSTRING_INDEX(" . $row->mvcform_name . "_" . $row->mvcform_id . ", ',', -1) >= 'xxxx' AND ";
						if ($zeitfeld_min_date > $searchfeld_anfang
							|| $zeitfeld_min_date == "") $zeitfeld_min_date = $searchfeld_anfang;
					}
					else
					{
						$felder_maske[] = "SUBSTRING_INDEX(" . $row->mvcform_name . "_" . $row->mvcform_id . ", ',', -1) >= '" . $this->db->escape($searchfeld_anfang) . "'";
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
						$felder_maske2 = "(";
						foreach($searchfeld_array as $searchfeld_schreibweise)
						{
							$felder_maske2 .= $row->mvcform_name . "_" . $row->mvcform_id . " LIKE '" . $prozentzeichen . $this->db->escape($searchfeld_schreibweise) . "%' OR ";
						}
						$felder_maske2 = substr($felder_maske2, 0, -4); // letztes OR wieder raus
						$felder_maske2 .= ")";
						$felder_maske[] = $felder_maske2;
					}
				}
			}
		}
	}
	// baut eine WHERE Klausel aus den Gruppen zusammen
	if (!empty($this->gruppen_ids))
	{
		foreach($this->gruppen_ids as $gruppe_id) { $felder_gruppen .= "group_id='" . $this->db->escape(26) . "' OR "; }
	//foreach($this->gruppen_ids as $gruppe_id) { $felder_gruppen .= "group_id='" . $this->db->escape($gruppe_id) . "' OR "; }
		$felder_gruppen = substr($felder_gruppen, 0, -4); // letztes ' AND ' wieder rausnehmen
		if ($felder_gruppen != "") $felder_gruppen = "(" . $felder_gruppen . ") AND ";
	}
	// holt die Felder und Werte f�r die F�lle, das die Gruppe den Beitrag sehen darf
	$sql = sprintf("SELECT * FROM %s, %s
								WHERE %s field_id = mvcform_id
								AND mvcform_meta_id = '%d'
								AND mvcform_aktiv = 1",

								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_group_rights",

								$this->cms->tbname['papoo_mvcform'],

								$felder_gruppen,
								$this->db->escape($meta_id)
					);
	$felder_gruppen_werte = $this->db->get_results($sql, ARRAY_A);
	$felder_gruppen = "";
	// baut aus den Felder Gruppen Werte eine WHERE Clause zusammen
	if (!empty($felder_gruppen_werte))
	{
		foreach($felder_gruppen_werte as $eintrag)
		{
			switch($eintrag['mvcform_type'])
			{
				// Markierung f�r neue Feldtypen
				// standard
				default: break;

				//f�r das Multiselect Feld die Werte aus der entsprechenden Look Up Tabelle holen
				case "multiselect":
					$eintrag['field_value'] = $this->get_lp_wert($eintrag['field_id'], $eintrag['mvcform_name'], $eintrag['field_value'], 1);
					break;
				// wenn Select oder radio Feld dann Werte aus der Lookup Tabelle holen
				case "select":
					$eintrag['field_value'] = $this->get_lp_wert($eintrag['field_id'], $eintrag['mvcform_name'], $eintrag['field_value'], 1);
					break;
				case "check":
					$eintrag['field_value'] = $this->get_lp_wert($eintrag['field_id'], $eintrag['mvcform_name'], $eintrag['field_value'], 1);
					break;
				case "radio":
					$eintrag['field_value'] = $this->get_lp_wert($eintrag['field_id'], $eintrag['mvcform_name'], $eintrag['field_value'], 1);
					break;
				//f�r das Bilder Galerie Feld die Werte aus der entsprechenden Look Up Tabelle holen
				//case "galerie":
				//	$eintrag['field_value'] = $this->get_lp_wert_search_tabs($eintrag['field_id'], $eintrag['mvcform_name'], $eintrag['field_value'], $this->cms->lang_id);
					break;
			}
			// wenn es ein multiselect, select, radio, check Feld ist und der Suchbegriff in den Optionswerten vorkommt
			if (!empty($lookup_treffer_ids[$eintrag['field_id']]))
			{
				foreach($lookup_treffer_ids[$eintrag['field_id']] as $lookup_treffer_id)
				{
					$felder_gruppen .= $eintrag['mvcform_name'] . "_" . $eintrag['field_id'] . "='" . $this->db->escape($lookup_treffer_id['lookup_id']) . "' OR ";
				}
			}
			// sonst hier entlang
			else $felder_gruppen .= $eintrag['mvcform_name'] . "_" . $eintrag['field_id'] . "='" . $this->db->escape($eintrag['field_value']) . "' OR ";
		}
	}
	if ($felder_gruppen != "") $felder_gruppen = substr($felder_gruppen, 0, -4); // letztes ' OR ' wieder rausnehmen
	// Steuerung der Ausgabe-SQL
	// Welche Felder d�rfen �berhaupt durchsucht werden?
	// aktive Felder, normaler User darf das und bei aktivem Suchfeld
	$sql = sprintf("SELECT mvcform_id,
							mvcform_name,
							mvcform_type
							FROM %s
							WHERE mvcform_form_id = '%d'
							AND mvcform_meta_id = '%d'
							AND mvcform_aktiv = '1'
							AND mvcform_normaler_user = '1'
							AND mvcform_list_front = '1'",

							$this->cms->tbname['papoo_mvcform'],

							$this->db->escape($this->checked->mv_id),
							$this->db->escape($meta_id)
					);
	$such_felder = $this->db->get_results($sql, ARRAY_A);
	$such_felder_sql = "";
	// baut aus den sql Treffern die Feldernamen f�r sql Statement zusammen
	if (!empty($such_felder))
	{
		$such_felder_sql = "mv_content_id, ";
		foreach($such_felder as $such_feld)
		{
			$such_felder_sql .= $this->db->escape($such_feld['mvcform_name'] . "_" . $such_feld['mvcform_id']) . ", ";
			$this->felder_typen[$such_feld['mvcform_name'] . "_" . $such_feld['mvcform_id']] = $such_feld['mvcform_type'];
		}
		$such_felder_sql = substr($such_felder_sql, 0, -2); // letztes Komma wieder weg
	#}
		if ($zeitfeld_min_date < $aktuell_timestamp
			&& $zeitfeld_min_date != "") $kalender_extra = "";
		// holt die Formularfelder IDs aus der Datenbank, die f�r normale User erlaubt sind
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
								$this->db->escape($meta_id)
						);
		$formular_felder = $this->db->get_results($sql);
		if (!empty($searchfor))
		{
			$sucharray = $this->sonderfall_loopen($searchfor);
			// Tabellenname content_X
			$tab_content = $this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id)
							. "_search_"
							. $this->cms->lang_id;
			if (!empty($formular_felder))
			{
				// und baut daraus den Suchstring
				foreach($formular_felder as $row)
				{
					// Darf das Feld auch von diesem Benutzer durchsucht werden (Leserechte)?
					if (array_search($row->mvcform_id, $felder_ok_fix))
					{
						switch($row->mvcform_type)
						{
							// Markierung f�r neue Feldtypen
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
											$felder[] = $row->mvcform_name . "_" . $row->mvcform_id . " LIKE '" . $this->db->escape($lookup_treffer_id['lookup_id']) . "'";
										}
									}
									// sonst hier entlang
									else $felder[] = $row->mvcform_name . "_" . $row->mvcform_id . " LIKE '%" . $this->db->escape($searchfor) . "%'";
								}
								break;
						}
					}
				}
			}
		}
		$get_sichtbar_feld_fuer_dzvhae = $this->get_sichtbar_feld_fuer_dzvhae();
		if ($get_sichtbar_feld_fuer_dzvhae) $felder_maske[] = $this->get_sichtbar_feld_fuer_dzvhae();
		if (is_numeric($this->checked->mv_content_userid)) $felder_maske[] = "mv_content_userid='" . $this->db->escape($this->checked->mv_content_userid) . "'";
		if (is_array($felder_maske) && count($felder_maske) > 0) $where_clause1 = '((' . implode( ') AND (', $felder_maske) . '))' . " AND ";
		if (is_array($felder) && count($felder) > 0) $where_clause2 = '((' . implode( ') OR (', $felder) . '))' . " AND ";
		if (is_array($felder_maske_checkbox) && count($felder_maske_checkbox) > 0)
		{
			if ($this->and_not_or_checkbox_search_fe) $where_clause3 = '((' . implode( ') AND (', $felder_maske_checkbox) . '))' . " AND ";
			else $where_clause3 = '((' . implode( ') OR (', $felder_maske_checkbox) . '))' . " AND ";
		}
		 $sql = sprintf("SELECT COUNT(DISTINCT(mv_content_id))
								FROM %s, %s
								WHERE %s %s %s %s
								(mv_content_sperre IS NULL OR mv_content_sperre <> '1')
								AND mv_content_teaser = 1
								AND mv_content_id = mv_meta_lp_user_id
								AND mv_meta_lp_meta_id = '%d'
								AND mv_meta_lp_mv_id = '%d'",

								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_search_"
								. $this->db->escape($this->cms->lang_id),

								$this->cms->tbname['papoo_mv_meta_lp'],

								$where_clause1,
			 				    $where_clause2,
								$where_clause3,
								$kalender_extra,
								$this->db->escape($meta_id),
								$this->db->escape($this->checked->mv_id)
						); // die normalen Felder
		$anzahl = $this->db->get_var($sql);

		//abgelaufene benachrichtigen
		$this->checke_alle_abgelaufenen_eintraege($kalender_extra, $kalender_extra2);
		$this->weiter->make_limit($this->intervall_front);
		$this->weiter->modlink = "no";
		// Anzahl der Ergebnisse aus der Suche Eigenschaft �bergeben
		$this->weiter->result_anzahl = $anzahl;
		$gets = $this->get_get_vars();
		$this->weiter->weiter_link = "plugin.php?" . $gets . "&getlang=" . $this->content->template['lang_short'];
		// wenn es sie gibt, weitere Seiten anzeigen
		$what = "teaser";
		$this->weiter->do_weiter($what);
		#$this->content->template['sort_dir'] = $this->checked->sort_dir = (($this->checked->sort_dir) xor 1) + 0; // invertieren (flip-flop)
		#$sort_dir = $this->checked->sort_dir ? " ASC" : " DESC";
		if (trim($order_by) == "ORDER BY Stufe_24") $order_by = " ORDER BY Stufe_24 DESC, Name_10";
		// Test auf select-Feld
		$sql = sprintf("SELECT mvcform_type
								FROM %s
								WHERE mvcform_meta_id = '%d'
								AND mvcform_form_id = '%d'
								AND mvcform_name = '%s'",
								$this->cms->tbname['papoo_mvcform'],
								$this->db->escape($meta_id),
								$this->db->escape($this->checked->mv_id),
								$this->db->escape($felddaten[0]['mvcform_name'])
						);
		$select_field = $this->db->get_var($sql);
		if ($select_field != "select") $sqllimit = $this->weiter->sqllimit;
		else $sqllimit = ""; // um alle Daten sp�ter sortieren zu k�nnen

        // Anpassung TODO#4739
        $such_felder_sql .= ", ".implode(',', ['mv_content_owner ', 'mv_content_userid ', 'mv_content_edit_date ', 'mv_content_create_owner ', 'mv_content_edit_user ']);

		// wenn Volltextsuche und Suchfelder leer, dann Gesamtliste ausgeben
		// aber nur aufgrund der Bedingungen, die zur Erstellung von $such_felder_sql f�hrten
		// das sind diese Bedingungern: mvcform_aktiv = '1' AND mvcform_normaler_user = '1' AND mvcform_list_front = '1'"
		$sql = $sql_buffer = sprintf("SELECT DISTINCT %s
								FROM %s, %s
								WHERE %s %s %s %s (mv_content_sperre IS NULL OR mv_content_sperre <> '1')
								AND mv_content_teaser = 1
								AND mv_content_id = mv_meta_lp_user_id
								AND mv_meta_lp_meta_id = '%d'
								AND mv_meta_lp_mv_id = '%d'
								%s
								%s",
								$such_felder_sql,

								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_search_"
								. $this->db->escape($this->cms->lang_id),

								$this->cms->tbname['papoo_mv_meta_lp'],

								$where_clause1,
								$where_clause2,
								$where_clause3,
								$kalender_extra,
								$this->db->escape($meta_id),
								$this->db->escape($this->checked->mv_id),
								$order_by,
								$sqllimit
						);
		$result = $this->db->get_results($sql);

		if (!empty($this->checked->sort_feld) && $result !== null)
		{
			if ($select_field == 'select')
			{
				// der zu sortierende Feldname mit seiner ID
				global $sortby;
				$sortby = $this->db->escape($felddaten[0]['mvcform_name'] . "_" . $felddaten[0]['mvcform_id']);
				// �berschreiben der lookup_id im Array mit dem Klartext
				foreach ($result AS $key => $value)
				{
					$result[$key]->$sortby = $this->get_lp_wert_front($felddaten[0]['mvcform_id'], $felddaten[0]['mvcform_name'], $value->mv_content_id);
				}
				#$oldLocale = setlocale(LC_COLLATE, "0");
				//setlocale(LC_COLLATE, 'de_DE.utf8');
				#setlocale(LC_COLLATE, 'German_Germany.1252');
				// Jetzt nach global $sortby sortieren, siehe mv.php am Ende
				usort($result, array ("mv", "cmp"));
				#setlocale(LC_COLLATE, $oldLocale);
				// Start und Ende der anzuzeigenden S�tze aus sqllimit ermitteln
				$st_e_arr = explode(",", str_replace("LIMIT ", "", $this->weiter->sqllimit));
				// und Ausschnitt dazu erstellen
				$result = array_slice($result, $st_e_arr[0], $st_e_arr[1], true);
			}
		}
		// Einleitungs- und Ausleitungstext f�r diese Metaebene ans Template geben
		$sql = sprintf("SELECT mv_meta_top_text,
								mv_meta_bottom_text
								FROM %s
								WHERE mv_meta_lang_id = '%d'
								AND mv_meta_lang_lang_id = '%d'
								LIMIT 1",

								$this->cms->tbname['papoo_mv']
								. "_meta_lang_"
								. $this->db->escape($this->checked->mv_id),

								$this->db->escape($meta_id),
								$this->db->escape($this->cms->lang_id)
						);
		$meta_texte = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['mv_meta_top_text'] = $meta_texte[0]['mv_meta_top_text'];
		$this->content->template['mv_meta_bottom_text'] = $meta_texte[0]['mv_meta_bottom_text'];
		// Trefferanzahl ans Template
		$this->content->template['such_treffer_anzahl'] = $anzahl;
		$this->content->template['mv_uebersicht_liste'] = $this->object_to_array($result);
		if (!$this->show_search_result_before_search && $anzahl != 0)
		{
			$this->content->template['message'] = "";
			if ($this->checked->mv_submit || $sort_feld != null)
			{
				$hit = "";
				$back_or_front = "mvcform_list_front";
				require(PAPOO_ABS_PFAD . '/plugins/mv/lib/print_treffer_liste_front.php');
			}
		}
		elseif($anzahl == 0)
		{
			$this->content->template['message'] = "no_content";
		}
		else
		{
			$hit = "";
			$back_or_front = "mvcform_list_front";
			require(PAPOO_ABS_PFAD . '/plugins/mv/lib/print_treffer_liste_front.php');
		}
		// Suchmaske zusammenbauen
		$felder_ok = $felder_ok_fix;
		$back_or_front = "";
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_suchmaske.php');
		#$this->make_suchmaske($felder_ok_fix, $search_mv_id, "");
		#$this->print_treffer_liste_front($result, "", "mvcform_list_front");
		$this->content->template['buffer_liste'] .= $this->content->template['mv_liste'];
		#}
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
								$this->db->escape($this->cms->lang_id)
						);
		$felder = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['felder_sort'] = $felder;
		// link ans Template
		if ($search_mv_id != "") $onemv = "&onemv=" . $search_mv_id;
		// gibt es eigene Beitr�ge hier?
		$sql = sprintf("SELECT COUNT(mv_content_id)
								FROM %s
								WHERE mv_content_userid = '%d'
								AND mv_content_teaser = 1
								AND mv_content_sperre <> '1'",

								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_search_"
								. $this->cms->lang_id,

								$this->db->escape($this->user->userid)
						);
		$anzahl_eigene = $this->db->get_var($sql);
		$this->content->template['mv_anzahl_eigene'] = $anzahl_eigene;
		$this->content->template['link_mv'] = $_SERVER['PHP_SELF']
												. "?menuid="
												. $this->checked->menuid
												. "&template="
												. $this->checked->template
												. $onemv;
        // auch bei freien URLs plugin.php aufrufen - alles außer plugin.php wird durch selbiges ersetzt
        $this->content->template['mv_link_self'] = preg_replace("/\\/(?!plugin\\.php)[^\\/]+\$/", "/plugin.php", $_SERVER['PHP_SELF'], 1) . "?";
		$this->content->template['mv_link_export'] = $_SERVER['PHP_SELF']
												. "?menuid="
												. $this->checked->menuid
												. "&template=mv/templates/mv_imex_exportit_fe.html&anzahl="
												. $this->content->template['such_treffer_anzahl']
												. "&tabelle_lang="
												. $this->cms->lang_back_content_id; // f�r Export Suchergebnis CSV
		$this->content->template['mv_meta_id'] = $this->meta_gruppe;
		// in Session speichern f�r Newsletter verschicken/, CSV-Export (s. BE Suchen; 2 Links) oder Schnellfunktionskn�pfen
		$pos_limit = strpos($sql_buffer, "LIMIT");
		if ($pos_limit) $sql_buffer = substr($sql_buffer, 0, ($pos_limit - 1)); // " LIMIT 0,20" am Ende entfernen
		$_SESSION['mv_import']['mv_sql_buffer'] = $_SESSION['nl']['mv_newsletter_sql'] = $sql_buffer;
	}
	else
	{
		$this->content->template['such_treffer_anzahl'] = 0;
		$this->content->template['no_search_allowed'] = 1;
	}
}
$this->content->template['mv_id'] = $this->checked->mv_id;

?>
