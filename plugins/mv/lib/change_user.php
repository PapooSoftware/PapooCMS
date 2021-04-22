<?php
/**
* Mitgliedsdaten �ndern
*/
$this->check_special_right(); // Recht f�r K�ndigungsbutton ermitteln
if ($this->checked->mv_fb_kuendigung == $this->content->template['plugin']['mv']['fb_kuendigung'])
	$this->do_dzvhae_kuendigen_alle_gesuchten();
if (!empty($this->checked->mv_fb_kuendigung_really))
	$this->do_dzvhae_kuendigen_alle_gesuchten_wirklich();
$this->finde_die_art_der_verwaltung_heraus();
$this->content->template['mv_art_rechte'] = $this->mv_art;
global $template;
$this->upload_ok = true;
//Wenn eine Datei gel�scht werden soll
// hier gehts ums Editieren, deswegen...
$this->content->template['altereintrag'] = 1;
$this->content->template['edit'] = 1;
$this->content->template['mv_content_id'] = $this->checked->mv_content_id;
// dzvhae System Id Sonderfall
if ($this->dzvhae_system_id
	&& $this->dzvhae_mv_id == $this->checked->mv_id)
{
	$sql = sprintf("SELECT mv_dzvhae_system_id
							FROM %s
							WHERE mv_content_id = '%d'",
							$this->cms->tbname['papoo_mv_dzvhae'],
							$this->db->escape($this->checked->mv_content_id)
					);
	$this->content->template['mv_dzvhae_system_id'] = $this->db->get_var($sql);
}
// wenn dzvhae Sonderfall und Mitgliederverwaltung, dann Sperre nicht extra anzeigen
if ($this->dzvhae_system_id
	&& $this->is_mv_or_st()) $this->content->template['mv_sperre_zeigen'] = "nein";
$this->content->template['userid'] = $this->checked->userid;
$this->content->template['insert'] = $this->checked->insert;
$sql = sprintf("SELECT mv_content_userid
						FROM %s
						WHERE mv_content_id = '%d'",
						
						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_search_"
						. $this->cms->lang_back_content_id,
						
						$this->db->escape($this->checked->mv_content_id)
				);
$papoo_user_id = $this->db->get_var($sql);

// Mainmeta id etc.
$sql = sprintf("SELECT mv_meta_main_lp_meta_id,
						mv_meta_group_name
						FROM %s,%s
						WHERE mv_meta_main_lp_user_id = '%d'
						AND mv_meta_main_lp_mv_id = '%d'
						AND mv_meta_id = mv_meta_main_lp_meta_id",
						$this->cms->tbname['papoo_mv_meta_main_lp'],
						$this->cms->tbname['papoo_mv']
						. "_meta_"
						. $this->db->escape($this->checked->mv_id),
						$this->db->escape($this->checked->mv_content_id),
						$this->db->escape($this->checked->mv_id)
				);
$resultmm = $this->db->get_results($sql, ARRAY_A);
$this->content->template['mv_main_meta_id'] = $resultmm['0']['mv_meta_main_lp_meta_id'];
if (empty($this->content->template['mv_main_meta_id'])) $this->content->template['mv_main_meta_id'] = 1;
$this->content->template['mv_main_meta_id_name'] = $resultmm['0']['mv_meta_group_name'];
$sql = sprintf("SELECT DISTINCT(mv_meta_id),
						mv_meta_group_name
						FROM %s, %s, %s
						WHERE mv_mpg_group_id = gruppenid
						AND mv_mpg_id = mv_meta_id
						AND userid = '%d'
						AND mv_mpg_write = 1",
						
						$this->cms->tbname['papoo_mv']
						. "_meta_"
						. $this->db->escape($this->checked->mv_id),
						
						$this->cms->tbname['papoo_mv']
						. "_mpg_"
						. $this->db->escape($this->checked->mv_id),
						
						$this->cms->tbname['papoo_lookup_ug'],
						
						$this->user->userid
				);
$metagruppen = $this->db->get_results($sql, ARRAY_A);
// F�r Anzeige der sonstigen Metaebenen (als checked kennzeichnen)
if (!empty($metagruppen))
{
	$metaebenen = array();
	foreach($metagruppen as $metagruppe)
	{
		if ($this->is_not_dzvhae == false
			AND $this->user->username == "useradminflex_dzvhae"
			AND ($metagruppe['mv_meta_id'] < 2 OR $metagruppe['mv_meta_id'] > 3))
		{ 
			// tu nix, zeige in diesem Fall diese Metaebene nicht an
		}
		else
		{
			$sql = sprintf("SELECT mv_meta_lp_user_id
									FROM %s
									WHERE mv_meta_lp_user_id = '%d'
									AND mv_meta_lp_meta_id = '%d'
									AND mv_meta_lp_mv_id = '%d'",
									$this->cms->tbname['papoo_mv_meta_lp'],
									$this->db->escape($this->checked->mv_content_id),
									$this->db->escape($metagruppe['mv_meta_id']),
									$this->db->escape($this->checked->mv_id)
							);
			$treffer = $this->db->get_var($sql);
			if (!empty($treffer)) $metagruppe['checked'] = "1";
			if ($metagruppe['mv_meta_id'] == $this->content->template['mv_main_meta_id'])
				$this->content->template['mv_main_meta_id_is_schreibrecht'] = 1;
			$metaebenen[] = $metagruppe;
		}
	}
}
$this->content->template['mv_metaebenen'] = $metaebenen;
// Alle m�glichen Rechtegruppen
$sql = sprintf("SELECT gruppenname,
						gruppeid
						FROM %s, %s
						WHERE userid = '%d'
						AND gruppeid = gruppenid",
						$this->cms->tbname['papoo_gruppe'],
						$this->cms->tbname['papoo_lookup_ug'],
						$this->user->userid
				);
$rechtegruppen = $this->db->get_results($sql, ARRAY_A);
// Durchgehen, ob Admin Gruppe
foreach($rechtegruppen as $okgruppe)
{
	if ($okgruppe['gruppeid'] == 1)
	{
		$sql = sprintf("SELECT gruppenname,
								gruppeid
								FROM %s",
								$this->cms->tbname['papoo_gruppe']
						);
		$rechtegruppen_admin = $this->db->get_results($sql, ARRAY_A);
	}
}
// Wenn Admin Gruppe, dann alle Gruppen anzeigen.
if (!empty($rechtegruppen_admin)) $rechtegruppen = $rechtegruppen_admin;
if (!empty($rechtegruppen))
{
	$counter = 0;
	foreach($rechtegruppen as $rechtegruppe)
	{
		$sql = sprintf("SELECT userid
								FROM %s
								WHERE userid = '%d'
								AND gruppenid = '%d'",
								$this->cms->tbname['papoo_lookup_ug'],
								$this->db->escape($papoo_user_id),
								$this->db->escape($rechtegruppe['gruppeid'])
						);
		$treffer = $this->db->get_var($sql);
		if (!empty($treffer)) $rechtegruppen[$counter]['checked'] = "1";
		$counter++;
	}
}
$this->content->template['mv_rechtegruppen'] = $rechtegruppen;

// wenn keine �nderung abgeschickt wurde, dann die Werte aus der Datenbank holen und einsetzen
if (empty($this->checked->mv_submit)
	AND empty($this->checked->upload)
	AND empty($this->checked->picdel))
{
	$sql = sprintf("SELECT * FROM %s
								WHERE mv_content_id = '%s'
								LIMIT 1",
								
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_search_"
								. $this->db->escape($this->cms->lang_back_content_id),
								
								$this->db->escape($this->checked->mv_content_id)
					);
	$result = $this->db->get_results($sql);
	if (!empty($result))
	{
		$spalten_namen = array();
		$spalten_namen = $this->get_spalten_namen();
		$this->content->template['mv_content_sperre'] = $result[0]->mv_content_sperre;
		$this->content->template['mv_content_create_date'] = $this->longtime_to_germantime($result[0]->mv_content_create_date);
		$this->content->template['mv_content_edit_date'] = $this->longtime_to_germantime($result[0]->mv_content_edit_date);
		$this->content->template['mv_content_create_owner'] = $result[0]->mv_content_create_owner;
		$this->content->template['Benutzername_1'] = $result[0]->Benutzername_1;
		$this->content->template['mv_content_edit_user'] = $result[0]->mv_content_edit_user;
		// weise dem Formularfeld den Wert aus der Datenbank zu
		if (count($spalten_namen))
		{
			foreach($spalten_namen as $form_feld_name)
			{
				$name = $form_feld_name['mvcform_name'];
				$id = $form_feld_name['mvcform_id'];
				$feld_type = $form_feld_name['mvcform_type'];
				$feld_name_id = $form_feld_name['mvcform_name'] . "_" . $form_feld_name['mvcform_id'];
				switch($feld_type)
				{
					// standard
					default:
						$this->checked->$feld_name_id = $result[0]->$feld_name_id;
						break;
					case "password":
						$this->checked->$feld_name_id = "";
						break;
					case "check":
					case "radio":
					case "select":
						// die Lookuptabellen sind nich sprachabh�ngig, daher die Daten aus den search-Tabellen holen
						$this->checked->$feld_name_id = $lookup_id = $result[0]->$feld_name_id;
						break;
					// Multiselect
					case "multiselect":
						// holt die Werte aus der Look Up Tabelle f�r dieses Mutliselect Feld und diesem Benutzer
						$this->set_multiselect_session($id, $name, $result[0]->$feld_name_id);
						break;
					// beim Timestamp muss ein anderes Format her
					case "timestamp":
						$tag = "mvcform_tag_" . $feld_name_id;
						$monat = "mvcform_monat_" . $feld_name_id;
						$jahr = "mvcform_jahr_" . $feld_name_id;
						list($this->checked->$tag,
							$this->checked->$monat,
							$this->checked->$jahr) = $this->get_day_month_year($result[0]->$feld_name_id);
						break;
					// beim Zeitintervall gleich zweimal umformatieren
					case "zeitintervall":
						$anfang_tag = "anfang_tag_" . $feld_name_id;
						$ende_tag = "ende_tag_" . $feld_name_id;
						$anfang_monat = "anfang_monat_" . $feld_name_id;
						$ende_monat = "ende_monat_" . $feld_name_id;
						$anfang_jahr = "anfang_jahr_" . $feld_name_id;
						$ende_jahr = "ende_jahr_" . $feld_name_id;
						$datum = $result[0]->$feld_name_id;
						list($anfang_datum, $ende_datum) = explode(",", $datum);
						list($this->checked->$anfang_tag, $this->checked->$anfang_monat,
							$this->checked->$anfang_jahr) = $this->get_day_month_year($anfang_datum);
						list($this->checked->$ende_tag, $this->checked->$ende_monat, $this->checked->$ende_jahr) =
							$this->get_day_month_year($ende_datum);
						break;
				}
				if ($feld_name_id == "mv_content_userid") $this->content->template['userid'] = $result[0]->$feld_name_id;
				elseif ($feld_name_id == "mv_content_sperre") $this->content->template['mv_content_sperre'] = $result[0]->$feld_name_id;
				elseif ($feld_name_id == "mv_content_teaser") {
					$this->content->template['mv_content_teaser'] = $result[0]->$feld_name_id;
				}
			}
		}
	}
}
//Formular wurde abgeschickt_ Schritt 1_ Rechte pr�fen
else
{
	// Mainmeta id etc.
	if (empty($this->checked->main_metaebene)) $this->content->template['mv_main_meta_id'] = $this->meta_gruppe;
	else $this->content->template['mv_main_meta_id'] = $this->checked->main_metaebene;
	#$sql = sprintf("SELECT mv_meta_id,
	#						mv_meta_group_name
	#						FROM %s",
	#						$this->cms->tbname['papoo_mv']
	#						. "_meta_"
	#						. $this->db->escape($this->checked->mv_id)
	#				);
	#$metagruppen = $this->db->get_results($sql, ARRAY_A);
	#if (!empty($metagruppen))
	#{
	#	$counter = 0;
	#	foreach($metagruppen as $metagruppe)
	#	{
	#		if (!empty($this->checked->mv_metaebenen))
	#		{
	#			foreach($this->checked->mv_metaebenen as $metaebene)
	#			{
	#				if ($metaebene == $metagruppe['mv_meta_id']) $metagruppen[$counter]['checked'] = "1";
	#			}
	#		}
	#		$counter++;
	#	}
	#}
	// deaktiviert khmweb, sonst kommt bei Fehlern im Form alles!
	#$this->content->template['mv_metaebenen'] = $metagruppen;
	// Alle m�glichen Rechtegruppen
	#$sql = sprintf("SELECT gruppenname,
	#						gruppeid
	#						FROM %s",
	#						$this->cms->tbname['papoo_gruppe']
	#				);
	#$rechtegruppen = $this->db->get_results($sql, ARRAY_A);
	#if (!empty($rechtegruppen))
	#{
	#	$counter = 0;
	#	foreach($rechtegruppen as $rechtegruppe)
	#	{
	#		if (!empty($this->checked->mv_rechtegruppen))
	#		{
	#			foreach($this->checked->mv_rechtegruppen as $rechtegruppe_checked)
	#			{
	#				if ($rechtegruppe['gruppeid'] == $rechtegruppe_checked) $rechtegruppen[$counter]['checked'] = "1";
	#			}
	#		}
	#		$counter++;
	#	}
	#}
	// deaktiviert khmweb, sonst kommt bei Fehlern im Form alles!
	#$this->content->template['mv_rechtegruppen'] = $rechtegruppen;
}
// Formular anzeigen
$this->content->template['message1'] = "ok";
// Formular raussuchen und anzeigen
$this->content->template['formok'] = "ok";
$this->content->template['mv_id'] = $this->checked->mv_id;
//Daten �berpr�fen. make_form_check.php ruft nix weiter auf.
if (empty($this->checked->submitdel)) require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_form_check.php');
// Formular mit allen Feldern zur Anzeige erstellen. picture, galerie und file Felder machen einen Upload.
// Weg zum Feld: back_get_form.php -> make_form.php -> make_feld.php. Von hier aus zu den versch. Feldtypen, z. B. make_picture_feld.php.
$formid = $this->checked->mv_id;
require(PAPOO_ABS_PFAD . '/plugins/mv/lib/back_get_form.php');
// Mainmeta id etc.
if (!empty($this->checked->main_metaebene)) $this->content->template['mv_main_meta_id'] = $this->checked->main_metaebene;
#if (!empty($this->checked->mv_metaebenen))
#{
#	$sql = sprintf("SELECT mv_meta_id,
#							mv_meta_group_name
#							FROM %s",
#							$this->cms->tbname['papoo_mv']
#							. "_meta_"
#							. $this->db->escape($this->checked->mv_id)
#					);
#	$metagruppen = $this->db->get_results($sql, ARRAY_A);
#	if (!empty($metagruppen))
#	{
#		$counter = 0;
#		foreach($metagruppen as $metagruppe)
#		{
#			if (!empty($this->checked->mv_metaebenen))
#			{
#				foreach($this->checked->mv_metaebenen as $metaebene)
#				{
#					if ($metaebene == $metagruppe['mv_meta_id']) $metagruppen[$counter]['checked'] = "1";
#				}
#			}
#			$counter++;
#		}
#	}
	// deaktiviert khmweb, sonst kommt bei Fehlern im Form alles!
	#$this->content->template['mv_metaebenen'] = $metagruppen;
#}
#if (!empty($this->checked->mv_rechtegruppen))
#{
	// Alle m�glichen Rechtegruppen
#	$sql = sprintf("SELECT gruppenname,
#							gruppeid
#							FROM %s",
#							$this->cms->tbname['papoo_gruppe']
#					);
#	$rechtegruppen = $this->db->get_results($sql, ARRAY_A);
#
#	if (!empty($rechtegruppen))
#	{
#		$counter = 0;
#		foreach($rechtegruppen as $rechtegruppe)
#		{
#			if (!empty($this->checked->mv_rechtegruppen))
#			{
#				foreach($this->checked->mv_rechtegruppen as $rechtegruppe_checked)
#				{
#					if ($rechtegruppe['gruppeid'] == $rechtegruppe_checked) $rechtegruppen[$counter]['checked'] = "1";
#				}
#			}
#			$counter++;
#		}
#	}
	// deaktiviert khmweb, sonst kommt bei Fehlern im Form alles!
	#$this->content->template['mv_rechtegruppen'] = $rechtegruppen;
#}
// letzte �nderungsdaten aus der content Tabelle holen
$sql = sprintf("SELECT mv_content_edit_date,
						mv_content_edit_user
						FROM %s
						WHERE mv_content_id = '%d'
						LIMIT 1",
						
						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_search_"
						. $this->db->escape($this->cms->lang_back_content_id),
						
						$this->db->escape($this->checked->mv_content_id)
				);
$mv_content_edit_daten = $this->db->get_results($sql, ARRAY_A);
// letzte �nderung ausgeben, falls vorhanden
$sql = sprintf("SELECT MAX(mv_pro_id)
						FROM %s
						WHERE mv_pro_mv_content_id = '%d' 
						AND mv_pro_mv_id = '%d'",
						
						$this->cms->tbname['papoo_mv_protokoll'],
						
						$this->db->escape($this->checked->mv_content_id),
						$this->db->escape($this->checked->mv_id)
				);
$max_pro_id = $this->db->get_var($sql);
$sql = sprintf("SELECT mv_pro_date,
						username,
						mv_pro_old_content,
						mv_pro_feld_id
						FROM %s, %s
						WHERE mv_pro_login_id = userid 
						AND mv_pro_id = '%d'
						LIMIT 1",
						
						$this->cms->tbname['papoo_mv_protokoll'],
						
						$this->cms->tbname['papoo_user'],
						
						$this->db->escape($max_pro_id)
				);
$protokoll = $this->db->get_results($sql, ARRAY_A);
// wurde �berhaupt schon mal an diesem Datensatz was ge�ndert, wenn ja dann weiter
if (!empty($mv_content_edit_daten))
{
	// Wenn die Benutzer ID gespeichert wurde, dann den Benutzernamen daf�r suchen
	if (is_numeric($mv_content_edit_daten[0]['mv_content_edit_user']))
	{
		$sql = sprintf("SELECT username
								FROM %s
								WHERE userid = '%d'
								LIMIT 1",
								
								$this->cms->tbname['papoo_user'],
								
								$this->db->escape($mv_content_edit_daten[0]['mv_content_edit_user'])
						);
		$last_edit_benutzername = $this->db->get_var($sql);
		// wenn es f�r diese Zahl keinen Benutzer in der user Tabelle gibt, dann gib die Zahl aus, f�r die Fehlersuche
		if (empty($last_edit_benutzername)) $last_edit_benutzername = $mv_content_edit_daten[0]['mv_content_edit_user'];
	}
	// wenn es ein ausgeschriebener Benutzername ist, dann diesen ausgeben(kommt vor, wenn beim Import keine Ids benutzt wurden, sondern Plain Text Benutzer)
	else $last_edit_benutzername = $mv_content_edit_daten[0]['mv_content_edit_user'];
	//Feldname
	$sql = sprintf("SELECT mvcform_name
							FROM %s
							WHERE mvcform_id = '%d'",
							$this->cms->tbname['papoo_mvcform'],
							$protokoll['0']['mv_pro_feld_id']
					);
	$this->content->template['mv_protokol_feld'] = $this->db->get_var($sql);
	$this->content->template['mv_letzte_aenderung_wer'] = $last_edit_benutzername;
	$this->content->template['mv_letzte_aenderung_datum'] =
		$this->longtime_to_germantime($mv_content_edit_daten[0]['mv_content_edit_date']);
	// gibt es Protokolleintr�ge f�r diesen Beitrag?
	if (!empty($protokoll))
	{
		// Markierung f�r neue Feldtypen
		// Feldtyp f�r die id aus der Datenbank holen
		$sql = sprintf("SELECT mvcform_type
								FROM %s
							WHERE mvcform_id = '%d' 
							AND mvcform_meta_id = '%d'",
							$this->cms->tbname['papoo_mvcform'],
							$this->db->escape($protokoll[0]['mv_pro_feld_id']),
							$this->db->escape($this->meta_gruppe)
						);
		$feld_type = $this->db->get_var($sql);
		if ($feld_type == "timestamp")
		{
			if (!empty($protokoll[0]['mv_pro_old_content']))
			{
				list($tag, $monat, $jahr) = $this->get_day_month_year($protokoll[0]['mv_pro_old_content']);
				$protokoll[0]['mv_pro_old_content'] = $tag . "." . $monat . "." . $jahr;
			}
		}
		elseif($feld_type == "zeitintervall")
		{
			list($anfang_datum, $ende_datum) = explode(",", $protokoll[0]['mv_pro_old_content']);
			list($anfang_tag, $anfang_monat, $anfang_jahr) = $this->get_day_month_year($anfang_datum);
			list($ende_tag, $ende_monat, $ende_jahr) = $this->get_day_month_year($ende_datum);
			$protokoll[0]['mv_pro_old_content'] = $anfang_tag . "." . $anfang_monat . "." . $anfang_jahr . " "
				. $this->content->template['plugin']['mv']['bis'] . " " . $ende_tag . "." . $ende_monat . "."
				. $ende_jahr;
		}
		elseif($feld_type == "select" 
			|| $feld_type == "radio" 
			|| $feld_type == "check")
		{
			$sql = sprintf("SELECT content
									FROM %s
									WHERE lookup_id = '%d' 
									AND lang_id = '%d'
									LIMIT 1",
									
									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($this->checked->mv_id)
									. "_lang_"
									. $this->db->escape($protokoll[0]['mv_pro_feld_id']),
									
									$this->db->escape($protokoll[0]['mv_pro_old_content']),
									$this->db->escape($this->cms->lang_back_content_id)
							);
			$protokoll[0]['mv_pro_old_content'] = $this->db->get_var($sql);
		}
		if (empty($protokoll[0]['mv_pro_old_content'])) $protokoll[0]['mv_pro_old_content'] = "0";
		$this->content->template['mv_alter_wert'] = $protokoll[0]['mv_pro_old_content'];
		// Label f�r Feldid aus der Datenbank holen
		$sql = sprintf("SELECT mvcform_label
								FROM %s
								WHERE mvcform_lang_id = '%d' 
								AND mvcform_lang_lang = '%d' 
								AND mvcform_lang_meta_id = '%d'
								LIMIT 1",
								
								$this->cms->tbname['papoo_mvcform_lang'],
								
								$this->db->escape($protokoll[0]['mv_pro_feld_id']),
								$this->db->escape($this->cms->lang_back_content_id),
								$this->db->escape($this->meta_gruppe)
						);
		$feld_label = $this->db->get_var($sql);
	}
	$this->content->template['mv_protokoll_feld'] = $feld_label;
	$this->content->template['mv_protokoll_link'] = $_SERVER['PHP_SELF']
													. "?menuid="
													. $this->checked->menuid
													. "&mv_id="
													. $this->checked->mv_id
													. "&template=mv/templates/mv_show_protokoll.html&mv_content_id="
													. $this->checked->mv_content_id;
}
// Soll der Satz gel�scht werden?
if (!empty($this->checked->submitdelecht))
{
	$sql = sprintf("SELECT mv_content_userid
							FROM %s
							WHERE mv_content_id = '%s'",
							
							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id)
							. "_search_"
							. $this->db->escape($this->cms->lang_back_content_id),
								
							$this->db->escape($this->checked->mv_content_id)
					);
	$mv_content_userid = $this->db->get_var($sql);
	// Eintrag nach id l�schen und neu laden
	$sql = sprintf("DELETE FROM %s
							WHERE mv_content_id = '%s'",
							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id),
							$this->db->escape($this->checked->mv_content_id)
					);
	#$this->db->query($sql);
	$sql = sprintf("SELECT mv_lang_id
							FROM %s",
							$this->cms->tbname['papoo_mv_name_language']
					);
	$sprachen = $this->db->get_results($sql);

	if (!empty($sprachen))
	{
		foreach($sprachen as $sprache)
		{
			$sql = sprintf("DELETE FROM %s
									WHERE mv_content_id = '%s'",
									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($this->checked->mv_id)
									. "_search_"
									. $this->db->escape($sprache->mv_lang_id),
									$this->db->escape($this->checked->mv_content_id)
							);
			$this->db->query($sql);
		}
	}
	// In der papoo_user/papoo_lookup_ug nur dann l�schen,
	// wenn es sich um eine Mitgliederverwaltung handelt und der User nicht root ist
	if ($this->content->template['mv_art_rechte'] == 2
		AND $mv_content_userid > 11) // Mitgliederverwaltung und nicht root etc.?
	{
		// wenn Mitgliederverwaltung dann Eintrag in der Usertabelle l�schen
		$sql = sprintf("DELETE FROM %s
								WHERE userid = '%d'",
								$this->cms->tbname['papoo_user'],
								$this->db->escape($mv_content_userid)
						);
		$this->db->query($sql);
	
		$sql = sprintf("DELETE FROM %s
								WHERE userid = '%d'",
								$this->cms->tbname['papoo_lookup_ug'],
								$this->db->escape($mv_content_userid)
						);
		$this->db->query($sql);
	}
	// Metaeintr�ge l�schen
	$sql = sprintf("DELETE FROM %s
							WHERE mv_meta_lp_user_id = '%d'
	  						AND mv_meta_lp_mv_id = '%d'",
							$this->cms->tbname['papoo_mv_meta_lp'],
							$this->db->escape($this->checked->mv_content_id),
							$this->db->escape($this->checked->mv_id)
					);
	$this->db->query($sql);
	$sql = sprintf("DELETE FROM %s
							WHERE mv_meta_main_lp_user_id = '%d'
	  						AND mv_meta_main_lp_mv_id = '%d'",
							$this->cms->tbname['papoo_mv_meta_main_lp'],
							$this->db->escape($this->checked->mv_content_id),
							$this->db->escape($this->checked->mv_id)
					);
	$this->db->query($sql);
	$location_url = $_SERVER['PHP_SELF']
					. "?menuid="
					. $this->checked->menuid
					. "&mv_id="
					. $this->checked->mv_id
					. "&template=mv/templates/userlist.html&fertig=del";

	if ($_SESSION['debug_stopallredirect'])
		echo '<a href="'
		. $location_url
		. '">'
		. $this->content->template['plugin']['mv']['weiter']
		. '</a>';
	else header("Location: $location_url");
	exit;
}

// Soll gel�scht werden?
if (!empty($this->checked->submitdel))
{
	$this->content->template['mv_content_id'] = $this->checked->mv_content_id;
	$this->content->template['mv_id'] = $this->checked->mv_id;
	$this->content->template['fragedel'] = "ok";
	$this->content->template['edit'] = "";
	// Holt nochmal die Werte f�r das Mitglied aus der Tabelle
	$result = $this->get_mv_content($this->checked->mv_content_id);
	if (count($result))
	{
		foreach($result as $row)
		{
			foreach($row as $key => $value) { $was_del .= $key . " :: " . $value . "\n"; }
		}
	}
	$this->content->template['was_del'] = $was_del;
}

/** 
 * HIER SPEICHERN 
 */

// Daten speichern, wenn alle Felder korrekt sind und die �nderung abgeschickt wurde
// upload_ok ist false bei Upload-Fehler (picture und file)
// insert_ok ist false bei Fehlern im Formular
#if ($this->insert_ok == true
#	&& $this->upload_ok == true && !empty($this->checked->mv_submit)
#	&& $this->checked->mv_submit == $this->content->template['plugin']['mv']['aendern']
#	OR $this->checked->upload
#	OR $this->checked->picdel)
if ($this->insert_ok == true 
	&& !empty($this->checked->mv_submit)
	&& $this->checked->mv_submit == $this->content->template['plugin']['mv']['aendern'])
{
	// added if khmweb 28.11.09, sonst werden die User Gruppen gel�scht und ein unn�tiger Update auf papoo_user gemacht, wenn nicht MV
	if ($this->content->template['mv_art_rechte'] == 2)
	{
		//Zuerst die Rechte retten die drin stehen
		$sql = sprintf("SELECT * FROM %s
									WHERE userid = '%d'",
									$this->cms->tbname['papoo_lookup_ug'],
									$this->db->escape($papoo_user_id)
						);
		$alt_rechte = $this->db->get_results($sql, ARRAY_A);

		//Die Gruppen rausholen an denen der User der gerade bedient die Rechte hat
		$sql = sprintf("SELECT gruppenname,
								gruppeid
								FROM %s, %s
								WHERE userid = '%d'
								AND gruppeid=gruppenid",
								$this->cms->tbname['papoo_gruppe'],
								$this->cms->tbname['papoo_lookup_ug'],
								$this->user->userid
						);
		$rechtegruppen_ok = $this->db->get_results($sql, ARRAY_A);
		//WEnn Admin Rechte bestehen dann alle rausholen
		if ($this->have_admin_rights())
		{
			$sql = sprintf("SELECT gruppenname,
									gruppeid FROM %s", 
									$this->cms->tbname['papoo_gruppe']);
			$rechtegruppen_ok = $this->db->get_results($sql, ARRAY_A);
		}
		//Rechte durchgehen die eingetragen werden sollen
		if (!empty($this->checked->mv_rechtegruppen)
			AND !empty($rechtegruppen_ok))
		{
			//Die Rechte alle l�schen
			$sql = sprintf("DELETE FROM %s
									WHERE userid = '%d'",
									$this->cms->tbname['papoo_lookup_ug'],
									$this->db->escape($papoo_user_id)
							);
			$this->db->query($sql);
			foreach($this->checked->mv_rechtegruppen as $rechtegruppe)
			{
				//Checken ob Rechte an den Gruppen bestehen - darf nur eingetragen wenn
				//der aktuelle User auch dieser Gruppe angeh�rt
				foreach($rechtegruppen_ok as $okgruppe)
				{
					if (in_array($rechtegruppe, $okgruppe))
					{
						$sql = sprintf("INSERT INTO %s
												SET userid = '%d',
													gruppenid = '%d'",
													$this->cms->tbname['papoo_lookup_ug'],
													$this->db->escape($papoo_user_id),
													$this->db->escape($rechtegruppe)
										);
						$this->db->query($sql);
					}
				}
			}
			$i = 0;
			//Dann die alten rechte wieder eintragen wenn sie nicht schon drin stehen
			$okgr = array();

			if (is_array($rechtegruppen_ok))
			{
				foreach($rechtegruppen_ok as $okgruppe) { $okgr[] = $okgruppe['gruppeid']; }
			}
			//Hier wird gecheckt ob die alten den aktuellen berechtigten Gruppen entsprechen
			if (is_array($alt_rechte))
			{
				foreach($alt_rechte as $ar)
				{
					if (!in_array($ar['gruppenid'], $okgr)) $altgr[$i] = $ar['gruppenid'];
					$i++;
				}
			}
		}
		$i = 0;
		// Hier wird gecheckt ob von den alten
		// NICHT berechtigten Gruppen welche nicht �bermittelt wurden
		if (is_array($altgr))
		{
			foreach($altgr as $ar)
			{
				if (!in_array($ar, $this->checked->mv_rechtegruppen)) $altgr2[$i] = $ar;
				$i++;
			}
		}
		if (is_array($altgr2))
		{
			foreach($altgr2 as $ar)
			{
				$sql = sprintf("INSERT INTO %s SET userid = '%d',
													gruppenid = '%d'",
													$this->cms->tbname['papoo_lookup_ug'],
													$this->db->escape($papoo_user_id),
													$this->db->escape($ar)
								);
				$this->db->query($sql);
			}
		}
	}
	// Bei diesem User werden nur die Metaebenen 2 und 3 angezeigt. Alle anderen vorhandenen kann er nicht l�schen, daher hier eingrenzen
	if ($this->user->username == "useradminflex_dzvhae") $extra_sql = " AND (mv_meta_lp_meta_id = 2 OR mv_meta_lp_meta_id = 3)";
	$sql = sprintf("DELETE FROM %s
							WHERE mv_meta_lp_user_id = '%d'
							AND mv_meta_lp_mv_id = '%d'
							%s",
							$this->cms->tbname['papoo_mv_meta_lp'],
							$this->db->escape($this->checked->mv_content_id),
							$this->db->escape($this->checked->mv_id),
							$extra_sql
					);
	$this->db->query($sql);#echo $sql;exit;

	// wenn die Mainmetaebene nicht dabei ist, dann noch hinzuf�gen
	/*
	if(!in_array($this->checked->main_metaebene,$this->checked->mv_metaebenen)) $this->checked->mv_metaebenen[] = $this->checked->main_metaebene;
	*/
	if($this->checked->mv_id == 1
		&& !in_array("1", $this->checked->mv_metaebenen)) $this->checked->mv_metaebenen[] = 1;
	if (!empty($this->checked->mv_metaebenen))
	{
		foreach($this->checked->mv_metaebenen as $metaebene)
		{
			// added if khmweb die Hauptmetabene wird weiter unten ebenfalls eingef�gt... ohne if doppelt
			if ($this->checked->main_metaebene != $metaebene)
			{
				$sql = sprintf("INSERT INTO %s
										SET mv_meta_lp_user_id = '%d',
										mv_meta_lp_meta_id = '%d',
										mv_meta_lp_mv_id = '%d'",
										$this->cms->tbname['papoo_mv_meta_lp'],
										$this->db->escape($this->checked->mv_content_id),
										$this->db->escape($metaebene),
										$this->db->escape($this->checked->mv_id)
								);
				$this->db->query($sql);
			}
		}
	}
	if (is_numeric($this->checked->main_metaebene))
	{
		$sql = sprintf("UPDATE %s
							SET mv_meta_main_lp_meta_id = '%d'
		  					WHERE mv_meta_main_lp_user_id = '%d'
							AND mv_meta_main_lp_mv_id = '%d'",
							$this->cms->tbname['papoo_mv_meta_main_lp'],
							$this->db->escape($this->checked->main_metaebene),
							$this->db->escape($this->checked->mv_content_id),
							$this->db->escape($this->checked->mv_id)
						);
		$this->db->query($sql);
		//Hauptebene noch als weitere Ebene eintragen wg. der Suche
		$sql = sprintf("INSERT INTO %s
								SET mv_meta_lp_user_id = '%d',
								mv_meta_lp_meta_id = '%d',
								mv_meta_lp_mv_id = '%d'",
								$this->cms->tbname['papoo_mv_meta_lp'],
								$this->db->escape($this->checked->mv_content_id),
								$this->db->escape($this->checked->main_metaebene),
								$this->db->escape($this->checked->mv_id)
						);
		$this->db->query($sql);
	}
	//print_r($sql_spalten);
	$this->save_temp_old_entry(); // Sichern der alten Daten f�rs Protokoll
	// Alle Daten speichern. Hierzu prepare_content_entry.php aufrufen zum Aufbau der SQL.
	require(PAPOO_ABS_PFAD . '/plugins/mv/lib/change_content_entry.php');

	// Daten auch in die Searchtabellen speichern
	$mv_content_id = $this->checked->mv_content_id;
	$update_insert = "update";
	require(PAPOO_ABS_PFAD . '/plugins/mv/lib/update_lang_search_row.php');
	// Vergleich der alten Mitgliederdaten in $this->content->template['temp_old_entry'] mit den neuen in mv_content_x_search_y und speichern der �nderungen ins Protokoll
	$this->compare_content();
// Kundenmod khmweb 20.10.2010
// Wird ausgef�hrt, wenn die Adminfreigabe erfolgt.
// Bei der Anmeldung muss dazu die mv_content_sperre als Standard gesetzt sein
// N�heres siehe in kundenmod01.php
if ($this->cms->tbname['papoo_mv_articles_papoo']
	AND empty($this->checked->mv_content_sperre)) require(PAPOO_ABS_PFAD . '/plugins/mv/lib/kunden_mod01.php');
// Ende Kundenmod

	if ($this->checked->fertig != 1
		AND empty($this->checked->upload)
		AND empty($this->checked->picdel))
	{
		// Front- und Backend Template nach erfolgreichen �ndern der Daten sind verschieden wie folgt:
		// alter $link_template = "template=" . $this->checked->template doch ka(besser kz) ob sicherheitsl�cke;)
		if ($this->checked->template == "mv/templates/mv_edit_front.html") $link_template = "template=mv/templates/mv_edit_front.html";
		else $link_template = "template=mv/templates/userlist.html";
		$location_url = $_SERVER['PHP_SELF']
						. "?menuid="
						. $this->checked->menuid
						. "&fertig=1&"
						. $link_template
						. "&insert=ok&mv_id="
						. $this->checked->mv_id
						. "&page="
						. $_SESSION['plugin_mv']['page'];
		$exit = 1;
	}
}
$this->content->template['zweiterunde'] = "ja";
?>