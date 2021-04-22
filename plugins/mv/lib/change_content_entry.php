<?php
/**
* Einen Eintrag in der Mitgliederliste �ndern
* called by change_user.php, change_user_front.php, edit_own_front.php
*/
//Hier wird das SQL Statement f�r die MV Tabellen zusammengesetzt
$change_or_make = "change";

require(PAPOO_ABS_PFAD . '/plugins/mv/lib/prepare_content_entry.php');
if ($this->is_mv_or_st())
{
	$mv_felder = array(
		0 => array(
			name => "Benutzername",
			type => "text",
			userdb => "username",
			label1 => "Benutzername",
			label2 => "Username"
			),
		1 => array(
			name => "passwort",
			type => "password",
			userdb => "password",
			label1 => "Passwort",
			label2 => "Password"
			),
		2 => array(
			name => "email",
			type => "email",
			userdb => "email",
			label1 => "E-Mail",
			label2 => "E-Mail"
			),
		3 => array(
			name => "antwortmail",
			type => "check",
			userdb => "antwortmail",
			label1 => "Antwortmail",
			label2 => "Answermail"
			),
		4 => array(
			name => "newsletter",
			type => "check",
			userdb => "user_newsletter",
			label1 => "Newsletter",
			label2 => "Newsletter"
			),
		5 => array(
			name => "board",
			type => "select",
			userdb => "board",
			label1 => "Forum",
			label2 => "Board",
			select => array(
				de => "Board Ansicht\r\nThread Ansicht",
				en => "Board View\r\nThread View"
				)
			),
		6 => array(
			name => "active",
			type => "check",
			userdb => "active",
			label1 => "User aktiv?",
			label2 => "Active"
			),
		7 => array(
			name => "signatur",
			type => "textarea",
			userdb => "signatur",
			label1 => "Signatur",
			label2 => "Signatur"
			)
		);
	// holt die id Nummer f�r das Erste Feld in dieser MV
	$sql = sprintf("SELECT MIN(mvcform_id)
							FROM %s
							WHERE mvcform_form_id = '%s'",
							
							$this->cms->tbname['papoo_mvcform'],
							
							$this->db->escape($this->checked->mv_id)
					);
	$min_id = $this->db->get_var($sql);
	$this->get_field_rights_schreibrechte(); //Felder Rechte rausholen
	$spalten_namen = $this->get_spalten_namen();
	foreach($mv_felder as $feld)
	{
		if (!defined("admin"))
		{
			//Checken, ob �berhaupt Schreibrechte bestehen
			$die_aktuelle_id = $min_id;
			if (is_numeric($die_aktuelle_id))
			{
				if (!in_array($die_aktuelle_id, $this->felder_schreib_rechte_aktuelle_gruppe)) continue; //Keine Leserechte - dann abbrechen
			}
		}
		//Checken, ob die Felder �berhaupt aktiv sind...
		$okfeld = "";
		foreach($spalten_namen as $spl) { if ($spl['mvcform_name'] == $feld['name']) $okfeld = "ok"; }
		if ($okfeld != "ok"
			&& $feld['name'] == "active"
			&& $noaktive != "ok") $noaktive = "ok";
		if ($okfeld != "ok")
		{
			$min_id++;
			continue;
		}
		$feldname_id = $feld['name'] . "_" . $min_id;
		if ($feld['type'] != "password")
		{
			// User darf im Feld active keine Eingaben (Edit) im FE machen: Skip
			if ($feld['name'] == "active"
				AND $this->checked->template == "mv/templates/mv_edit_front.html") continue;
			// F�r die papoo_user nur 0 oder 1 erlaubt, egal, was einbgetragen wurde (erm�glicht auch "Ja" statt 1)
			if ($feld['name'] == "active"
				OR $feld['name'] == "antwortmail"
				OR $feld['name'] == "newsletter")
			{
				$feld_set .= empty($this->checked->$feldname_id) ? $this->db->escape($feld['userdb']) . "='0', " : $this->db->escape($feld['userdb']) . "='1', ";
			}
			else
			{
				// Korrekte Umsetzung Flex Forum Wert (beliebig) zu papoo_user Forum Wert (0 = Thread Ansicht, 1 = Board Ansicht)
				if ($feld['name'] == "board")
				{
					// Vorhandene lookup:ids holen nach $get_feld['mvcform_lang_lookup']
					$get_feld['mvcform_form_id'] = $this->db->escape($this->checked->mv_id); // mv_id
					$get_feld['mvcform_lang_id'] = 6; // feld_id Forum Board
					$get_feld = $this->get_lang_werte_lookup($get_feld, 1); // Deutsch f�r den folgenden Vergleich weiter unten
					if (count($get_feld['mvcform_lang_lookup']))
					{
						// f�r den gew�hlten Wert den content-Wert ermitteln (deutsch)
						foreach ($get_feld['mvcform_lang_lookup'] AS $key => $value)
						{
							if ($this->checked->board_6 == $get_feld['mvcform_lang_lookup'][$key]->lookup_id)
							{
								$content = $get_feld['mvcform_lang_lookup'][$key]->content; // gefunden
								break;
							}
						}
					}
					if ($content == "Board Ansicht") $forum = 1;
					else $forum = 0; // Thread Ansicht papoo_user
					$feld_set .= $this->db->escape($feld['userdb'])
							. "='"
							. $forum
							. "', ";
				}
				else $feld_set .= $this->db->escape($feld['userdb'])
							. "='"
							. $this->db->escape($this->checked->$feldname_id)
							. "', ";
			}
		}
		else
		{
			if (!empty($this->checked->$feldname_id))
			{
				$feld_set .= $this->db->escape($feld['userdb'])
							. "='"
							. $this->db->escape($this->checked->$feldname_id)
							. "', ";
			}
		}
		$min_id++;
	}
	$feld_set .= "confirm_code='" . md5(rand(0, time())) . "', ";
	// letztes ", " wieder rausnehmen
	$feld_set = substr($feld_set, 0, strlen($feld_set) - 2);
	// Frontend Sonderfall: Userid
	if ($this->checked->template == "mv/templates/mv_edit_front.html") $this->checked->userid = $this->user->userid;
	$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
	//Userid rausholen
	$sql = sprintf("SELECT mv_content_userid
							FROM %s
							WHERE mv_content_id = '%d'",
							
							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id)
							. "_search_"
							. $lang,
							
							$this->db->escape($this->checked->mv_content_id)
					);
	$id = $this->db->get_var($sql);
	// Aktualisiere die Daten in der user Tabelle
	$sql = sprintf("UPDATE %s SET %s
								WHERE userid = '%s'",
								
								$this->cms->tbname['papoo_user'],
								
								$feld_set,
								$this->db->escape($id)
					);
	$this->db->query($sql);
}
$sperre_sql = "";
// wenn admin, dann darf er auch sperren/entsperren
if($this->have_admin_rights())
{
	$sperre_sql = "mv_content_sperre='" . $this->db->escape($this->checked->mv_content_sperre) . "'";
	// wenn dzvhae sonderfall und Mitgliederverwaltung
	if ($this->dzvhae_system_id 
		&& $this->is_mv_or_st())
	{
		if ($this->checked->active_7 == "1") $sperre_wert = "0";
		elseif (defined("admin")) $sperre_wert = "1";
		$sperre_sql = "mv_content_sperre='" . $this->db->escape($sperre_wert) . "'";
	}
}
if ($noaktive == "ok") $sperre_wert = "0";
if (empty($this->checked->mv_content_sperre)) $this->checked->mv_content_sperre = 0;
if ($this->checked->template != "mv/templates/mv_edit_front.html"
	AND $sperre_wert != "1") $sperre_sql = "mv_content_sperre='" . $this->db->escape($this->checked->mv_content_sperre) . "'";
if (empty($this->checked->mv_content_sperre))
{
	$sql = sprintf("UPDATE %s SET verfall_stufe_id = '0'
								WHERE verfall_content_id = '%d'
								AND verfall_mv_id = '%d'
								LIMIT 1",
								
								$this->cms->tbname['papoo_mv_datum_verfallen'],
								
								$this->db->escape($this->checked->mv_content_id),
								$this->db->escape($this->checked->mv_id)
					);
	$this->db->query($sql);
}
// MV Daten Tabelle, aktualisiere die Daten der Mitgliederliste
// Doppelte Feldpflege in beiden Updates: folgende Felder werden ebenfalls durch update_lang_search_row.php bereits gepflegt:
// mv_content_edit_date, mv_content_edit_user, Benutzername_1 und alle anderen Systemfelder bis signatur_8, Sichtbar_## und Zusendungerwunescht_##
$sql = sprintf("UPDATE %s SET %s %s %s
							WHERE mv_content_id = '%s'",
							
							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id),
							
							$sperre_sql,
							$sql_spalten,
							$edit_date_user,
							$this->db->escape($this->checked->mv_content_id)
				);
#$this->db->query($sql);
#$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
// speichern in eingestellter Sprache
$sql = sprintf("UPDATE %s SET %s %s %s 
							WHERE mv_content_id = '%s'",
							
							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id)
							. "_search_"
							. $lang,
							
							$sperre_sql,
							$sql_spalten,
							$edit_date_user,
							$this->db->escape($this->checked->mv_content_id)
				);
#$this->db->query($sql);
// Berarbeitungsdatum und User der bearbeitet mitspeichern
if (!empty($sql_spalten)) $sql_spalten .= ",";
$sql_spalten .= " mv_content_edit_date='"
					. $this->unixtime_to_longtime(time())
					. "',mv_content_edit_user='"
					. $this->db->escape($this->user->userid)
					. "'";
?>