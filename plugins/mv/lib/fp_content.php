<?php
/**
 * Mitgliederliste Content befuellen (BE)
 */
global $template;

// wenn dzvhae Auftritt und mv_id=id der Mitgliederverwaltung
if ($dzvhae_system_id
	&& $dzvhae_mv_id == $this->checked->mv_id)
{
	// Holt die Maximale dzvhae user ID aus der Datenbank
	$sql = sprintf("SELECT MAX(mv_dzvhae_system_id)
		FROM %s",
		$this->cms->tbname['papoo_mv_dzvhae']);
	$max_dzvhae_system_id = $this->db->get_var($sql);
}

//mv_system_msg
if ($this->checked->fertig == 1) $this->content->template['mv_system_msg'] = $this->content->template['plugin']['mv']['datenfertig'];

// wenn dzvhae Sonderfall und Mitgliederverwaltung, dann Sperre nicht extra anzeigen
if ($this->dzvhae_system_id
	&& $this->is_mv_or_st()) $this->content->template['mv_sperre_zeigen'] = "nein";
// Holt die Daten f�r die Gruppen aus der Datenbank
$sql = sprintf("SELECT * FROM %s, %s
	WHERE gruppenid = mv_set_group_id  
	AND userid = '%d' 
	AND mv_id = '%d'",
	$this->cms->tbname['papoo_lookup_ug'],
	$this->cms->tbname['papoo_mv'],
	$this->db->escape($this->user->userid),
	$this->db->escape($this->checked->mv_id)
);
$gruppe_id = $this->db->get_results($sql);
//Wenn Insert ok, �bergeben um Template auf gespeichert zu sezten
$this->content->template['insert'] = $this->checked->insert;
$sql = sprintf("SELECT mv_art FROM %s
	WHERE mv_id = '%d'",
	$this->cms->tbname['papoo_mv'],
	$this->db->escape($this->checked->mv_id)
);
$mv_art = $this->db->get_var($sql);

//if (!($mv_art == 1 and empty($gruppe_id) and !defined('admin')))
if ($mv_art != 1 or !empty($gruppe_id) or defined('admin'))
{
	// wenn mehr als 8 bzw. 8 Spalten in der Tabelle sind, dann gibts auch schon Felder zum eintippseln
	$sql = sprintf("SHOW COLUMNS FROM %s",

		$this->cms->tbname['papoo_mv']
		. "_content_"
		. $this->db->escape($this->checked->mv_id)
		. "_search_"
		. $this->db->escape($this->cms->lang_back_content_id)
	);
	$result = $this->db->get_results($sql);
	$spalten_anzahl = count($result);
	// Fallunterscheidung bleibt drin, falls doch noch unterschiedlichen Anzahlen am Ende rauskommen^^
	if (($mv_art == 1 && $spalten_anzahl > 8) || ($mv_art == 2 && $spalten_anzahl > 8) || ($mv_art == 3 && $spalten_anzahl > 8))
	{
		// Mainmeta id etc.
		if (empty($this->checked->main_metaebene)) $this->content->template['mv_main_meta_id'] = $this->meta_gruppe;
		else $this->content->template['mv_main_meta_id'] = $this->checked->main_metaebene;

		$sql = sprintf("SELECT 	count(mvcform_id) 
			FROM %s",
			$this->cms->tbname['papoo_mvcform']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (count($result) > 8) // 8 Standardfelder f�r MV: Benutzername, Passwort... etc.
		{
			// Mind. je ein Pflichtfeld f�r BE und FE muss definiert werden zur Verhinderung der Speicherung von Leers�tzen in die DB
			$sql = sprintf("(SELECT 	count(T1.mvcform_must) mvcform_must, 
				count(T1.mvcform_must_back) mvcform_must_back 
				FROM %s T1 
				WHERE T1.mvcform_aktiv = '1' 
				AND T1.mvcform_must = '1' 
				AND T1.mvcform_form_id = '%d' 
				AND T1.mvcform_meta_id = '%d')
				UNION ALL
				(SELECT count(T2.mvcform_must) mvcform_must,
					count(T2.mvcform_must_back) mvcform_must_back 
					FROM %s T2 
					WHERE T2.mvcform_aktiv = '1' 
					AND T2.mvcform_must_back = '1' 
					AND T2.mvcform_form_id = '%d' 
					AND T2.mvcform_meta_id = '%d')",
					$this->cms->tbname['papoo_mvcform'],
					$this->db->escape($this->checked->mv_id),
					$this->db->escape($this->meta_gruppe),
					$this->cms->tbname['papoo_mvcform'],
					$this->db->escape($this->checked->mv_id),
					$this->db->escape($this->meta_gruppe)
				);
			$result = $this->db->get_results($sql, ARRAY_A);
			if (!$result[0]['mvcform_must']) $this->content->template['kein_pflichtfeld_fe'] = $fehler = 1;
			if (!$result[1]['mvcform_must_back']) $this->content->template['kein_pflichtfeld_be'] = $fehler = 1;
		}
		if (!$fehler)
		{
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
			if (!empty($metagruppen))
			{
				$counter = 0;
				foreach($metagruppen as $metagruppe)
				{
					if ($this->is_not_dzvhae == false
						AND $this->user->username == "useradminflex_dzvhae"
						AND ($metagruppe['mv_meta_id'] < 2 OR $metagruppe['mv_meta_id'] > 3))
					{ 
						unset($metagruppen[$counter]);
					}
					else
					{
						if (!empty($this->checked->mv_metaebenen))
						{
							foreach($this->checked->mv_metaebenen as $metaebene)
							{
								if ($metaebene == $metagruppe['mv_meta_id']) $metagruppen[$counter]['checked'] = "1";
							}
						}
						elseif ($metagruppe['mv_meta_id'] == $this->meta_gruppe
							AND $metagruppe['mv_meta_id'] == 1) $metagruppen[$counter]['checked'] = "1"; // sonst die aktuelle Metaebene als Standard vorselektieren
					}
					$counter++;
				}
				if ($metagruppen[0]['mv_meta_id'] == 1) $metagruppen[0]['checked'] = "1"; // Metaebene standard immer vorbelegen
			}
			$this->content->template['mv_metaebenen'] = $metagruppen;
			// Alle m�glichen Rechtegruppen
			$sql = sprintf("SELECT gruppenname,
				gruppeid FROM %s, %s
				WHERE userid = '%d'
				AND gruppeid = gruppenid",
				$this->cms->tbname['papoo_gruppe'],
				$this->cms->tbname['papoo_lookup_ug'],
				$this->user->userid
			);
			$rechtegruppen = $this->db->get_results($sql, ARRAY_A);
			//Durchgehen ob Admin Gruppe
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
			//Wenn Admin Gruppe, dann alle Gruppen anzeigen.
			if (!empty($rechtegruppen_admin)) $rechtegruppen = $rechtegruppen_admin;
			$rechtegruppen = $this->db->get_results($sql, ARRAY_A); // ????????? khmweb
			if (!empty($rechtegruppen))
			{
				$counter = 0;
				foreach($rechtegruppen as $rechtegruppe)
				{
					if (!empty($this->checked->mv_rechtegruppen))
					{
						foreach($this->checked->mv_rechtegruppen as $rechtegruppe_checked)
						{
							if ($rechtegruppe['gruppeid'] == $rechtegruppe_checked) $rechtegruppen[$counter]['checked'] = "1";
						}
					}
					$counter++;
				}
			}
			$this->content->template['mv_rechtegruppen'] = $rechtegruppen;
			// Beim Ausgeben der Formularfelder wird die Variable abgefragt
			$this->password_new = "ja";
			$this->content->template['zweiterunde'] = $this->checked->zweiterunde;
			$this->content->template['message1'] = "ok";
			// Formular raussuchen und anzeigen
			$this->content->template['formok'] = "ok";
			$this->content->template['mv_id'] = $this->checked->mv_id;
			$this->content->template['mv_dzvhae_system_id'] = $max_dzvhae_system_id;
			// muss das nicht nur nach dem Submit? check!
			require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_form_check.php'); 
			$formid = $this->checked->mv_id;
			require(PAPOO_ABS_PFAD . '/plugins/mv/lib/back_get_form.php');
			// Daten speichern
			if ($this->checked->mv_submit == $this->content->template['plugin']['mv']['Eintragen'])
			{
				// wird im Fehlerfall durch make_form_check auf false gesetzt. Dann noch nichts in die DB bringen
				if ($this->insert_ok == true)
				{
					require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_content_entry.php'); // schreiben in die content-Tabellen
					// Sprachtabellen f�r die schnellere Suche
					$mv_content_id = $this->checked->mv_content_id;
					$update_insert = "insert";
					require(PAPOO_ABS_PFAD . '/plugins/mv/lib/update_lang_search_row.php'); // schteiben in die search-Tabellen
					// $_SESSION Werte f�r den multiselect wieder l�schen
					$sql = sprintf("SELECT mvcform_name,
						mvcform_id
						FROM %s
						WHERE mvcform_type = 'multiselect' 
						AND mvcform_form_id = '%d' 
						AND mvcform_meta_id = '%d'",
						$this->cms->tbname['papoo_mvcform'],
						$this->db->escape($this->checked->mv_id),
						$this->db->escape($this->meta_gruppe)
					);
					$result = $this->db->get_results($sql);
					if (!empty($result))
					{
						foreach($result as $row)
						{
							if (!empty($_SESSION["mvcform"
								. $row->mvcform_name
								. "_"
								. $row->mvcform_id])) unset($_SESSION["mvcform"
								. $row->mvcform_name
								. "_"
								. $row->mvcform_id]);
						}
					}
					$location_url = $_SERVER['PHP_SELF']
						. "?menuid="
						. $this->checked->menuid
						. "&fertig=saved&template=&template=mv/templates/userlist.html"
						. "&mv_id="
						. $this->checked->mv_id
						. "&insert=ok";
					if ($_SESSION['debug_stopallredirect'])
						echo '<a href="'
						. $location_url
						. '">'
						. $this->content->template['plugin']['mv']['weiter']
						. '</a>';
					else header("Location: $location_url");

					exit;
				}
			}
		}
	}
	else $this->content->template['noch_kein_feld'] = $this->content->template['plugin']['mv']['noch_kein_feld'];
}
?>
