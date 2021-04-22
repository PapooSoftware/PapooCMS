<?php
/**
 * Einen Eintrag in die Mitgliederliste machen (used by BE & FE)
 * called by fp_content_entry.php, mv.php fp_content_front()
 */
// Meta Ebene einstellen, wenn extern dann die externe nehmen.
if (!defined("admin"))
{
	$meta_id = $this->meta_gruppe;
	if (!defined("admin")
		AND is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
	$this->checked->mv_metaebenen['0'] = $this->checked->main_metaebene = $meta_id;
}
// Neuen Satz erst einmal anlegen und insert_id/mv_content_id erzeugen
$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
$sql = sprintf("INSERT INTO %s
	SET mv_content_owner = '%d'",

	$this->cms->tbname['papoo_mv']
	. "_content_"
	. $this->db->escape($this->checked->mv_id)
	. "_search_"
	. $lang,

	$this->db->escape($this->user->userid)
);
$this->db->query($sql);
$this->checked->mv_content_id = $this->db->insert_id;
// Sonderfall dzvhae Miltgliederanmeldung im FE f�r alle Metaebenen per Default aktivieren. S. auch 2 x hier, in make_content_entry.php, make_feld.php, prepare_content_entry.php
if ($this->checked->template == "mv/templates/mv_create_front.html"
	AND $this->checked->mv_id == 1
	AND $this->dzvhae_system_id) $this->checked->active_7 = 1;
$change_or_make = "make";
require(PAPOO_ABS_PFAD . '/plugins/mv/lib/prepare_content_entry.php');
if ($this->is_mv_or_st())
{
	$mv_felder = array(
		0 => array(
			"name" => "Benutzername",
			"type" => "text",
			"userdb" => "username",
			"label1" => "Benutzername",
			"label2" => "Username"
		),
		1 => array(
			"name" => "passwort",
			"type" => "password",
			"userdb" => "password",
			"label1" => "Passwort",
			"label2" => "Password"
		),
		2 => array(
			"name" => "email",
			"type" => "email",
			"userdb" => "email",
			"label1" => "E-Mail",
			"label2" => "E-Mail"
		),
		3 => array(
			"name" => "antwortmail",
			"type" => "check",
			"userdb" => "antwortmail",
			"label1" => "Antwortmail",
			"label2" => "Answermail"
		),
		4 => array(
			"name" => "newsletter",
			"type" => "check",
			"userdb" => "user_newsletter",
			"label1" => "Newsletter",
			"label2" => "Newsletter"
		),
		5 => array(
			"name" => "board",
			"type" => "select",
			"userdb" => "board",
			"label1" => "Forum",
			"label2" => "Board",
			"select" => array(
				"de" => "Board Ansicht\r\nThread Ansicht",
				"en" => "Board View\r\nThread View"
			)
		),
		6 => array(
			"name" => "active",
			"type" => "check",
			"userdb" => "active",
			"label1" => "User aktiv?",
			"label2" => "Active"
		),
		7 => array(
			"name" => "signatur",
			"type" => "textarea",
			"userdb" => "signatur",
			"label1" => "Signatur",
			"label2" => "Signatur"
		)
	);
	// holt die niedrigste id Nummer in dieser MV
	$sql = sprintf("SELECT MIN(mvcform_id)
		FROM %s
		WHERE mvcform_form_id = '%s'",

		$this->cms->tbname['papoo_mvcform'],

		$this->db->escape($this->checked->mv_id)
	);
	$min_id = $this->db->get_var($sql);
	//Felder Rechte rausholen
	$this->get_field_rights_schreibrechte();
	foreach($mv_felder as $feld)
	{
		//Checken ob �berhaupt schreibrechte bestehen
		$die_aktuelle_id = $min_id;
		if (is_numeric($die_aktuelle_id)) // ???
		{
			//Keine Schreibrechte - dann abbrechen
			if (!in_array($die_aktuelle_id, $this->felder_schreib_rechte_aktuelle_gruppe)) 
			{
				$min_id++;
				continue;
			}
		}
		$feldname_id = $feld['name'] . "_" . $min_id;
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
		else $feld_set .= $this->db->escape($feld['userdb']) . "='" . $this->db->escape($this->checked->$feldname_id) . "', ";
		$min_id++;
	}
	$feld_set .= "confirm_code='" . md5(rand(0, time())) . "'";
	// aktualisiere die Daten in der user Tabelle
	// zeitstempel added khmweb
	$sql = sprintf("INSERT INTO %s
		SET zeitstempel = '%s',
		%s",

		$this->cms->tbname['papoo_user'],

		date("Y.m.d H:i:s"),
		$feld_set
	);
	$this->db->query($sql);
	$userid = $insert_id_neu = $this->db->insert_id;
	#$userid = ", mv_content_userid='".$this->db->escape($this->db->insert_id)."'";
	if (empty($this->checked->mv_rechtegruppen)) $this->checked->mv_rechtegruppen['0'] = 10;
	//Wenn ADmin Rechte bestehen dann alle rausholen
	if ($this->have_admin_rights())
	{
		$sql = sprintf("SELECT gruppenname,
			gruppeid
			FROM %s",
			$this->cms->tbname['papoo_gruppe']
		);
		$rechtegruppen_ok = $this->db->get_results($sql, ARRAY_A);
	}
	else
	{
		$sql = sprintf("SELECT gruppenname,
			gruppeid
			FROM %s, %s
			WHERE userid = '%d'
			AND gruppeid = gruppenid",

			$this->cms->tbname['papoo_gruppe'],

			$this->cms->tbname['papoo_lookup_ug'],

			$this->user->userid
		);
		$rechtegruppen_ok = $this->db->get_results($sql, ARRAY_A);
	}
	if (!empty($this->checked->mv_rechtegruppen))
	{
		foreach($this->checked->mv_rechtegruppen as $rechtegruppe)
		{
			foreach($rechtegruppen_ok as $okgruppe)
			{
				if (in_array($rechtegruppe, $okgruppe))
				{
					$sql = sprintf("INSERT INTO %s 	SET userid = '%d', 
						gruppenid = '%d'",

						$this->cms->tbname['papoo_lookup_ug'],

						$this->db->escape($insert_id_neu),
						$this->db->escape($rechtegruppe)
					);
					$this->db->query($sql);
					// Falls via Konfig. eine weitere Default-Gruppe angegeben ist, dann diese auch eintragen
					if ($this->zweite_default_gruppe
						AND is_numeric($this->zweite_default_gruppe))
					{
						// Feststellen, ob die angegebene Gruppe existiert
						$sql = sprintf("SELECT gruppeid
							FROM %s",
							$this->cms->tbname['papoo_gruppe']
						);
						$all_groups = $this->db->get_results($sql, ARRAY_A);
						foreach ($all_groups AS $key => $value)
						{
							if ($value['gruppeid'] == $this->zweite_default_gruppe)
							{
								// Gruppe existiert: Dann in die lookup:ug eintragen
								$sql = sprintf("INSERT INTO %s 	SET userid = '%d', 
									gruppenid = '%d'",

									$this->cms->tbname['papoo_lookup_ug'],

									$this->db->escape($insert_id_neu),
									$this->db->escape($this->zweite_default_gruppe)
								);
								$this->db->query($sql);
								break;
							}
						}
					}
				}
			}
		}
	}
}
// Ist bei der Erstellung unn�tig?
$sql = sprintf("DELETE FROM %s
	WHERE mv_meta_lp_user_id = '%d'
	AND mv_meta_lp_mv_id = '%d'",

	$this->cms->tbname['papoo_mv_meta_lp'],

	$this->db->escape($this->checked->mv_content_id),
	$this->db->escape($this->checked->mv_id)
);
$this->db->query($sql);
if (!empty($this->checked->mv_metaebenen))
{
	if (!in_array("1", $this->checked->mv_metaebenen)) $this->checked->mv_metaebenen[count($this->checked->mv_metaebenen)] = 1; 
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
else
{
	$sql = sprintf("INSERT INTO %s
		SET mv_meta_lp_user_id = '%d', 
		mv_meta_lp_meta_id = '%d', 
		mv_meta_lp_mv_id = '%d'",

		$this->cms->tbname['papoo_mv_meta_lp'],

		$this->db->escape($this->checked->mv_content_id),
		$this->db->escape($this->meta_id_no_login),
		$this->db->escape($this->checked->mv_id)
	);
	$this->db->query($sql);
}
$sql = sprintf("DELETE FROM %s
	WHERE mv_meta_main_lp_user_id = '%d'
	AND mv_meta_main_lp_mv_id = '%d'",

	$this->cms->tbname['papoo_mv_meta_main_lp'],

	$this->db->escape($this->checked->mv_content_id),
	$this->db->escape($this->checked->mv_id)
);
$this->db->query($sql);
//$meta_id_no_login
if (!empty($this->checked->main_metaebene))
{
	$sql = sprintf("INSERT INTO %s
		SET mv_meta_main_lp_meta_id = '%d',
		mv_meta_main_lp_user_id = '%d',
		mv_meta_main_lp_mv_id = '%d'",

		$this->cms->tbname['papoo_mv_meta_main_lp'],

		$this->db->escape($this->checked->main_metaebene),
		$this->db->escape($this->checked->mv_content_id),
		$this->db->escape($this->checked->mv_id)
	);
	$this->db->query($sql);
}
else
{
	$sql = sprintf("INSERT INTO %s
		SET mv_meta_main_lp_meta_id = '%d',
		mv_meta_main_lp_user_id = '%d',
		mv_meta_main_lp_mv_id = '%d'",

		$this->cms->tbname['papoo_mv_meta_main_lp'],

		$this->db->escape($this->meta_id_no_login),
		$this->db->escape($this->checked->mv_content_id),
		$this->db->escape($this->checked->mv_id)
	);
	$this->db->query($sql);
}
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
#$sperre_sql = "";

// wenn admin, dann darf er auch sperren/entsperren
#if ($this->have_admin_rights())
#{
#	$sperre_sql = "mv_content_sperre='" . $this->db->escape($this->checked->mv_content_sperre) . "',";
// wenn dzvhae sonderfall und Mitgliederverwaltung
#	if ($this->dzvhae_system_id 
#		&& $this->is_mv_or_st()) $sperre_sql = $this->checked->active_7 == "1" ? "mv_content_sperre='0'," : "mv_content_sperre='1',";
#}
// FE: Wenn nicht Admin und das H�kchen f�r direkte Freigabe nicht gesetzt ist
#if (!defined("admin")) $sperre_sql = $this->mv_meta_allow_direct_unlock != 1 ? "mv_content_sperre='1'," : "mv_content_sperre='0',";
$sql = sprintf("SELECT username
	FROM %s
	WHERE userid = '%d'",

	$this->cms->tbname['papoo_user'],

	$this->db->escape($this->user->userid)
);
$username = $this->db->get_var($sql);
$userid = $userid ? $userid : $this->db->escape($this->user->userid);
// Trage die Daten in die Mitgliederliste ein
// �bertr�gt Multiselectwerte im Klartext... (in $sql_spalten enthalten)
// $sql_spalten aufgebaut in prepare_content_entry.php.
// In update_lang_search_row.php wird kein Klartext f�r die Searchtabellen verwendet.

// Sonderfall dzvhae Miltgliederanmeldung im FE f�r alle Metaebenen per default aktivieren. s. auch prepare_content_entry.php, make_feld.php
if ($this->checked->template == "mv/templates/mv_create_front.html"
	AND $this->checked->mv_id == 1
	AND $this->dzvhae_system_id)
{
	$sql_spalten = str_replace("`active_7`='0',", "", $sql_spalten);
	$sql_spalten = str_replace("`active_7`='',", "", $sql_spalten);
	$sql_spalten = str_replace("`active_7`='1',", "", $sql_spalten);
	$sql_spalten .= ",active_7='1'";
}
$sql = sprintf("UPDATE %s SET %s
	mv_content_create_date = '%s',
	mv_content_edit_date = '%s',
	mv_content_edit_user = '',
	mv_content_create_owner = '%s',
	mv_content_userid = '%d',
	%s
	WHERE mv_content_id = '%d'",

	$this->cms->tbname['papoo_mv']
	. "_content_"
	. $this->db->escape($this->checked->mv_id)
	. "_search_"
	. $lang,

	$sperre_sql,
	$this->unixtime_to_longtime(time()),
	$this->unixtime_to_longtime(time()),
	$this->db->escape($username),
	$userid,
	$sql_spalten,
	$this->db->escape($this->checked->mv_content_id)
);
#$this->db->query($sql);
if (!empty($sql_spalten)) $sql_spalten .= ",";
$sql_spalten .= " mv_content_create_date='"
	. $this->unixtime_to_longtime(time())
	. "',mv_content_edit_date='"
	#				. $this->unixtime_to_longtime(time())
	. "',mv_content_edit_user=''"
	. ",mv_content_create_owner='"
	. $this->db->escape($username)
	. "',mv_content_userid='"
	. $userid
	. "'";
// Sonderfall dzvh�
if ($this->dzvhae_system_id)
{
	// f�gt f�r diesen neuen Eintrag eine System id in die Extratabelle ein
	$sql = sprintf("INSERT INTO %s
		SET mv_content_id = '%d'",

		$this->cms->tbname['papoo_mv_dzvhae'],

		$this->db->escape($this->checked->mv_content_id)
	);
	$this->db->query($sql);
}
?>
