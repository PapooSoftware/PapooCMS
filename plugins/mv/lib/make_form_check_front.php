<?php
/**
* mv::make_form_check_front()
*
* @descrip Formularfelder durch checken
* @return
*/
$fehler = array();
$this->insert_ok = true;
// Alle Felder des Formulars raussuchen
$sql = sprintf("SELECT mvcform_name,
						mvcform_id,
						mvcform_must,
						mvcform_must_back,
						mvcform_type,
						mvcform_minlaeng,
						mvcform_maxlaeng,
						mvcform_content_type,
						mvcform_label 
						FROM %s, %s
						WHERE mvcform_form_id = '%d'
						AND mvcform_lang_id = mvcform_id
						AND mvcform_lang_lang = '%d'
						AND mvcform_lang_meta_id = '%d'
						AND mvcform_lang_meta_id = mvcform_meta_id GROUP BY mvcform_id",
						
						$this->cms->tbname['papoo_mvcform'],
						
						$this->cms->tbname['papoo_mvcform_lang'],
						
						$this->db->escape($this->checked->mv_id),
						$this->db->escape($this->cms->lang_id),
						$this->db->escape($this->meta_gruppe)
				);
$result = $this->db->get_results($sql);
// Durchgehen
// gibt es �berhaupt schon eine Liste? und ist es nicht das erste Mal, dass die Seite aufgerufen wird?
if (!empty($result))
{
	//Felder Rechte rausholen
	$this->get_field_rights_schreibrechte();
	if (count($this->felder_schreib_rechte_aktuelle_gruppe) < 1) $this->insert_ok = false;
	foreach($result as $daten)
	{
		//Checken ob�berhaupt schreibrechte bestehen
		$die_aktuelle_id = $daten->mvcform_id;
		if (is_numeric($die_aktuelle_id))
		{
			if (!in_array($die_aktuelle_id, $this->felder_schreib_rechte_aktuelle_gruppe)) continue; //Keine Leserechte - dann abbrechen
		}
		// Name �bergeben
		$name = $daten->mvcform_name . "_" . $daten->mvcform_id;
		// Must? und wurde auch auf "Eintragen" oder "�ndern" gedr�ckt (oder Hochladen/Entfernen!!!)
		#if ($daten->mvcform_must == 1
		#	&& ($this->checked->mv_submit == $this->content->template['plugin']['mv']['Eintragen']
		#		|| $this->checked->mv_submit == $this->content->template['plugin']['mv']['aendern']
		#		OR $this->checked->picdel
		#		OR $this->checked->upload))
		if ($this->checked->mv_submit == $this->content->template['plugin']['mv']['Eintragen']
				OR $this->checked->mv_submit == $this->content->template['plugin']['mv']['aendern']
				OR $this->checked->picdel
				OR $this->checked->upload)
		{
			$this->checked->$name = trim($this->checked->$name);
			// wenn leeres Feld, Passwort, Bilder und Dateien sind Ausnahmen, d�rfen nur nicht beim ersten Mal leer sein (Dateien noch etwas komplizierter))
			if ((empty($this->checked->$name) && $daten->mvcform_type != "password")
				|| (empty($this->checked->$name) && $daten->mvcform_type == "password" && empty($this->checked->mv_content_id)))
			{
				if ($daten->mvcform_type == "galerie")
				{
					if ($this->checked->template == "mv/templates/mv_edit_front.html"
						OR $this->checked->template == "mv/templates/mv_edit_own_front.html")
					{
						// Beim Upload keine Pr�fung, da etwas hochgeladen wird (hoffentlich erfolgreich). 
						// Der Eintrag in die DB erfolgt erst sp�ter 
						// Erst dann ist was in der DB: Nach Upload k�me ohne diesen Check eine Fehlermeldung.
						if ($this->checked->upload != $name)
						{
							$sql = sprintf("SELECT %s
													FROM %s
													WHERE mv_content_id = '%s'",
													$name,
													
													$this->cms->tbname['papoo_mv']
													. "_content_"
													. $this->db->escape($this->checked->mv_id)
													. "_search_"
													. $this->db->escape($this->cms->lang_back_content_id),
													
													$this->db->escape($this->checked->mv_content_id)
											);
							$result = $this->db->get_results($sql);
							if ($daten->mvcform_must == 1
								AND (empty($result[0]->$name) OR $this->checked->picdel == $name)) // es wird gel�scht, also Hinweis/Fehlermeldung
							{
								// Problem: Pflichtpfeld kann nach dem L�schen ohne Speichern verlassen werden...
								$this->error[$name] = "error";
								$this->errortext[$name] = $this->content->template['plugin']['mv']['pflichtfeld'];
								$this->insert_ok = false;
							}
						}
					}
				}
				else if (in_array($daten->mvcform_type, array("file", "picture"), true))
				{
					if (in_array($this->checked->template, array(
						"mv/templates/mv_edit_front.html",
						"mv/templates/mv_edit_own_front.html",
						"mv/templates/mv_create_front.html"
					), true))
					{
						$sql = sprintf("SELECT `%s` FROM `%s` WHERE `mv_content_id` = '%d'",
							$name,
							$this->db->escape(sprintf("%s_content_%d_search_%d",
								$this->cms->tbname['papoo_mv'],
								$this->checked->mv_id,
								$this->cms->lang_back_content_id
							)),
							$this->db->escape($this->checked->mv_content_id)
						);
						$result = $this->db->get_results($sql);
						if ($daten->mvcform_must == 1) {
							$upload_missing = false;
							if (is_array($this->checked->upload)) {
								// Beim Upload keine Pr�fung, da etwas hochgeladen wird (hoffentlich erfolgreich).
								// Der Eintrag in die DB erfolgt erst sp�ter
								// (s. change_user.php Aufruf zu back_get_form -> make_form.php -> make_feld.php - make_picture_feld.php/make_file_feld.php).
								// Erst dann ist was in der DB: Nach Upload k�me ohne diesen Check eine Fehlermeldung.
								// es wird gel�scht, also Hinweis/Fehlermeldung
								// Problem: Pflichtpfeld kann nach dem L�schen ohne Speichern verlassen werden...
								if (in_array($name, $this->checked->upload) === false && (
										isset($this->checked->{"mvcform".$name."_already_uploaded"}) === false && (
											empty($result[0]->$name) || $this->checked->picdel["fieldname"] == $name
										)
									)
								) {
									$upload_missing = true;
								}
								else {}
							}
							else if (isset($this->checked->{"mvcform".$name."_already_uploaded"}) === false && (isset($this->checked->picdel) === false ||  $this->checked->picdel["fieldname"] !== $name)) {
								$upload_missing = true;
							}
							else if (isset($this->checked->{"mvcform".$name."_already_uploaded"}) && isset($this->checked->picdel) && $this->checked->picdel["fieldname"] === $name) {
								$upload_missing = true;
							}

							if ($upload_missing) {
								$this->error[$name] = "error";
								$this->errortext[$name] = $this->content->template['plugin']['mv']['pflichtfeld'];
								$this->insert_ok = false;
							}
						}
					}
				}
				// Zeitstempel: extra Formatierung in TT.MM.JJJJ
				elseif ($daten->mvcform_type == "timestamp")
				{
					$name_tag = "mvcform_tag_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$name_monat = "mvcform_monat_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$name_jahr = "mvcform_jahr_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					// wenn eins der drei Felder JJ MM JJJJ leer ist dann error
					if ($daten->mvcform_must == 1
						AND (empty($this->checked->$name_tag) OR empty($this->checked->$name_monat) OR empty($this->checked->$name_jahr)))
					{
						$this->error[$name] = "error";
						$this->errortext[$name] = $this->content->template['plugin']['mv']['pflichtfeld'];
						$this->insert_ok = false;
					}
					if (!(empty($this->checked->$name_tag)
						OR empty($this->checked->$name_monat)
						OR empty($this->checked->$name_jahr)))
					{
						if (!checkdate($this->checked->$name_monat, $this->checked->$name_tag, $this->checked->$name_jahr))
						{
							$this->error[$name] = "error";
							$this->errortext[$name] = $this->content->template['plugin']['mv']['no_valid_date2'];
							$this->insert_ok = false;
						}
					}
				}
				// Zeitintervall
				elseif ($daten->mvcform_type == "zeitintervall")
				{
					$anfang_tag = "anfang_tag_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$ende_tag = "ende_tag_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$anfang_monat = "anfang_monat_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$ende_monat = "ende_monat_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$anfang_jahr = "anfang_jahr_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$ende_jahr = "ende_jahr_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					// wenn eins der Felder JJ MM JJJJ leer ist dann error
					if ($daten->mvcform_must == 1
						AND (empty($this->checked->$anfang_tag)
							|| empty($this->checked->$ende_tag)
							|| empty($this->checked->$anfang_monat)
							|| empty($this->checked->$ende_monat)
							|| empty($this->checked->$anfang_jahr)
							|| empty($this->checked->$ende_jahr)))
					{
						$this->error[$name] = "error";
						$this->errortext[$name] = $this->content->template['plugin']['mv']['pflichtfeld'];
						$this->insert_ok = false;
					}
					if (!(empty($this->checked->$anfang_tag)
							|| empty($this->checked->$ende_tag)
							|| empty($this->checked->$anfang_monat)
							|| empty($this->checked->$ende_monat)
							|| empty($this->checked->$anfang_jahr)
							|| empty($this->checked->$ende_jahr)))
					{
						if (!checkdate($this->checked->$anfang_monat, $this->checked->$anfang_tag, $this->checked->$anfang_jahr)
							OR !checkdate($this->checked->$ende_monat, $this->checked->$ende_tag, $this->checked->$ende_jahr))
						{
							$this->error[$name] = "error";
							$this->errortext[$name] = $this->content->template['plugin']['mv']['no_valid_date'];
							$this->insert_ok = false;
						}
						if ($this->checked->$ende_jahr . $this->checked->$ende_monat . $this->checked->$ende_tag
								< $this->checked->$anfang_jahr . $this->checked->$anfang_monat . $this->checked->$anfang_tag)
						{
							$this->error[$name] = "error";
							$this->errortext[$name] = $this->content->template['plugin']['mv']['no_valid_date_from_to'];
							$this->insert_ok = false;
						}
					}
				}
				// Multiselect Sonderfall wegen Javascript
				elseif ($daten->mvcform_type == "multiselect")
				{
					// Flag ob was ausgew�hlt wurde erstmal nicht setzen
					$treffer = false;
					// wieviel Lookupwerte gibt es f�r dieses Feld?
					$sql = sprintf("SELECT COUNT(lookup_id) 
											FROM %s
											WHERE lang_id = '1'",
											
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($this->checked->mv_id)
											. "_lang_"
											. $this->db->escape($daten->mvcform_id)
									);
					$anzahl_lookup_werte = $this->db->get_var($sql);
					// Z�hler f�r die while Schleife
					$counter = 1;
					// Substitutionsvariable damit das $this->checked in der while Schleife auch den gew�nschten Wert liefert:)
					$subbi = "hiddenmvcform" . $name . "_" . $counter;
					// gehe alle Lookupwerte durch
					while($counter <= $anzahl_lookup_werte)
					{
						// wurde ein Multiselectwert vom Benutzer ausgew�hlt?
						if ($this->checked->$subbi == 1) $treffer = true; // wenn Ja dann Flag setzten
						$counter++;
						$subbi = "hiddenmvcform" . $name . "_" . $counter;
					}
					// gabs keinen Treffer bei den Auswahlm�glichkeiten?
					if (!$treffer
						AND $daten->mvcform_must == 1)
					{
						// dann Fehlermeldung raus
						$this->error[$name] = "error";
						$this->errortext[$name] = $this->content->template['plugin']['mv']['pflichtfeld'];
						$this->insert_ok = false;
					}
				}
				// alle anderen Felder die nicht ausgef�llt sind ergeben auch einen error
				elseif ($daten->mvcform_must)
				{
					$this->error[$name] = "error";
					$this->errortext[$name] = $this->content->template['plugin']['mv']['pflichtfeld'];
					$this->insert_ok = false;
				}
			}
			// alpha / numerisch?
			if ($daten->mvcform_content_type == "num"
				AND $daten->mvcform_type == "text")
			{
				if (!is_numeric($this->checked->$name))
				{
					$this->error[$name] = "error";
					$this->errortext[$name] = $this->content->template['plugin']['mv']['num_error'];
					$this->insert_ok = false;
				}
			}
			// preisintervall
			if ($this->checked->$name
				AND $daten->mvcform_type == "preisintervall")
			{
				// check Anzahl Kommata
				if (substr_count($this->checked->$name, ",") > 1
					OR substr_count($this->checked->$name, ".") > 1)
				{
					$this->error[$name] = "error";
					$this->errortext[$name] = $this->content->template['plugin']['mv']['pi_comma'];
					$this->insert_ok = false;
				}
				else // check auf numeric
				{
					$val = str_replace(",", "", $this->checked->$name);
					if (!is_numeric($val))
					{
						$this->error[$name] = "error";
						$this->errortext[$name] = $this->content->template['plugin']['mv']['pi_not_numeric'];
						$this->insert_ok = false;
					}
				}
			}
			// email
			if ($this->checked->$name
				AND $daten->mvcform_type == "email")
			{
				if ((!$this->validateEmail($this->checked->$name)))
				{
					$this->error[$name] = "error";
					$this->errortext[$name] = $this->content->template['plugin']['mv']['email_error'];
					$this->insert_ok = false;
				}
			}
			// Passwort Best�tigung
			if ($daten->mvcform_type == "password"
				&& $this->checked->$name)
			{
				$name_2 = "2_" . $name;
				if ($this->checked->$name != $this->checked->$name_2)
				{
					$this->error[$name] = "error";
					$this->errortext[$name] = $this->content->template['plugin']['mv']['pw_falsch'];
					$this->insert_ok = false;
				}
			}
			// Link checken
			if ($daten->mvcform_type == "link"
				&& $this->checked->$name)
			{
				if (substr($this->checked->$name, 0, 7) != "http://")
				{
					$this->checked->$name = "http://" . $this->checked->$name;
					#$this->error[$name] = "error";
					#$this->errortext[$name] = $this->content->template['plugin']['mv']['http_falsch'];
					#$this->insert_ok = false;
				}
			}
		}
		// Mindestl�nge
		if ($daten->mvcform_minlaeng > strlen($this->checked->$name)
			&& strlen($this->checked->$name)
			&& ($daten->mvcform_type == "text"
				|| $daten->mvcform_type == "textarea"
				|| $daten->mvcform_type == "textarea_tiny"
				|| $daten->mvcform_type == "password"))
		{
			$this->error[$name] = "error";
			$this->errortext[$name] = $this->content->template['plugin']['mv']['min_laenge'] . $daten->mvcform_minlaeng . $this->content->template['plugin']['mv']['zeichen'];
			$this->insert_ok = false;
		}
		// Maxl�nge
		if ($daten->mvcform_maxlaeng < strlen($this->checked->$name)
			&& ($daten->mvcform_type == "text" OR $daten->mvcform_type == "password")
			&& $daten->mvcform_maxlaeng != 0)
		{
			$this->error[$name] = "error";
			$this->errortext[$name] = $this->content->template['plugin']['mv']['max_laenge'] . $daten->mvcform_maxlaeng . $this->content->template['plugin']['mv']['zeichen'];
			$this->insert_ok = false;
		}
		// Maximale L�ngen
		if (strlen($this->checked->$name) > 65535
			&& ($daten->mvcform_type == "password"
				|| $daten->mvcform_type == "textarea"
				|| $daten->mvcform_type == "textarea_tiny"
				|| $daten->mvcform_type == "galerie"))
		{
			$this->error[$name] = "error";
			$this->errortext[$name] = $this->content->template['plugin']['mv']['max65535_3'];
			$this->insert_ok = false;
		}
		elseif (strlen($this->checked->$name) > 255
			&& ($daten->mvcform_type == "text"
				|| $daten->mvcform_type == "email"
				|| $daten->mvcform_type == "preisintervall"
				|| $daten->mvcform_type == "file"
				|| $daten->mvcform_type == "picture"
				|| $daten->mvcform_type == "link"))
		{
			$this->error[$name] = "error";
			$this->errortext[$name] = $this->content->template['plugin']['mv']['max255_4'];
			$this->insert_ok = false;
		}
		// Ist das Feld leer?
		if (empty($this->checked->$name))
		{
			// gibts es ausgew�hlte/beschriebene Felder die zur Folge haben das dieses Feld zum Pflichtfeld wird
			$required_feld = $this->get_required_feld($name);
			if (!empty($required_feld))
			{
				// ja, dann gehe die Felder durch
				foreach($required_feld as $treffer)
				{
					$asv = $treffer->mvcform_name . "_" . $treffer->mvcform_id;
// ist es ein Datum? dann m�ssen die 3 Popups(Tag/Monat/Jahr) gechecked werden, ob sie auch alle drei leer sind
					if ($treffer->mvcform_type == "timestamp")
					{
						$name_tag = "mvcform_tag_" . $asv;
						$name_monat = "mvcform_monat_" . $asv;
						$name_jahr = "mvcform_jahr_" . $asv;
// die "00"er kommen aus der Datenbank, wenn ein Mitglied bearbeitet wird und das Datumsfeld leer war
						if ((!empty($this->checked->$name_tag) && !empty($this->checked->$name_monat) && !empty($this->checked->$name_jahr))
							&&
							($this->checked->$name_tag != "00" && $this->checked->$name_monat != "00" && $this->checked->$name_jahr != "0000"))
						{
							$this->error[$name] = "error";
							$this->insert_ok = false;
						}
					}
					// beim Zeitintervall das Gleiche wie beim Datum
					elseif($treffer->mvcform_type == "zeitintervall")
					{
						$anfang_tag = "anfang_tag_" . $asv;
						$ende_tag = "ende_tag_" . $asv;
						$anfang_monat = "anfang_monat_" . $asv;
						$ende_monat = "ende_monat_" . $asv;
						$anfang_jahr = "anfang_jahr_" . $asv;
						$ende_jahr = "ende_jahr_" . $asv;
						// wenn das zeitintervall keinen Wert hat, dann wird das leer Feld zur Pflicht -> error
						if (!empty($this->checked->$anfang_tag)
							&& !empty($this->checked->$ende_tag)
							&& !empty($this->checked->$anfang_monat)
							&& !empty($this->checked->$ende_monat)
							&& !empty($this->checked->$anfang_jahr)
							&& !empty($this->checked->$ende_jahr)
							&& $this->checked->$anfang_tag != "00"
							&& $this->checked->$ende_tag != "00"
							&& $this->checked->$anfang_monat != "00"
							&& $this->checked->$ende_monat != "00"
							&& $this->checked->$anfang_jahr != "0000"
							&& $this->checked->$ende_jahr != "0000")
						{
							$this->error[$name] = "error";
							$this->insert_ok = false;
						}
					}
					// kein Datum, dann checken ob das Feld leer ist
					else
					{
						if (!empty($this->checked->$asv))
						{
							$this->error[$name] = "error";
							$this->insert_ok = false;
						}
					}
				}
			}
		}
		//Checken ob der Benutzername schon existiert
		if ($daten->mvcform_name == "Benutzername")
		{
			$this->finde_die_art_der_verwaltung_heraus();
			if ($this->mv_art == 2)
			{
				$checkuser = $this->mv_check_ob_user_existiert($this->checked->$name);
				if ($checkuser == false)
				{
					$this->error[$name] = "error";
					$this->insert_ok = false;
				}
			}
		}
		//Checken ob die Email schon existiert nach Submit
		if ($this->checked->$name
			AND $this->checken_ob_mail_existiert // Schalter Konfig.
			AND $daten->mvcform_name == "email"
			AND $this->checked->mv_submit)
		{
			if ($this->checked->template == "mv/templates/mv_create_front.html") $check_it = 1;
			else
			{
				$sql = sprintf("SELECT email_3 FROM %s
										WHERE mv_content_id = '%d'",
										
										$this->cms->tbname['papoo_mv']
													. "_content_"
													. $this->db->escape($this->checked->mv_id)
													. "_search_"
													. $this->db->escape($this->cms->lang_back_content_id),
													
										$this->db->escape($this->checked->mv_content_id)
						);
				$old_email = $this->db->get_var($sql);
				if (trim($old_email) != trim($this->checked->email_3)) $check_it = 1;
			}
			if ($check_it)
			{
				$this->finde_die_art_der_verwaltung_heraus();
				if ($this->mv_art == 2)
				{
					$checkemail = $this->mv_check_ob_email_existiert($this->checked->$name);
					if ($checkemail == false)
					{
						$this->error[$name] = "error";
						$this->insert_ok = false;
					}
				}
			}
		}
		// gibts einen error? dann das Feldlabel f�r die Fehlerausgabe zwischenspeichern
		if (!empty($this->error[$name])) $fehler[] = $daten->mvcform_label . ": " . $this->errortext[$name];
	}
}
// wenn es keine Liste gibt, dann kann auch nichts eingetragen werden
else $this->insert_ok = false;
//Testen ob es einen User oder E-Mail Adresse schon gibt wenn Typ 1 = MItgliederverwaltung
// Fehlerliste ans Template weitergeben
$this->content->template['fehlerliste'] = $fehler;
?>