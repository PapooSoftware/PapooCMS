<?php
/**
* mv::make_form_check()
*
* called by change_user.php, fp_content.php
* @descrip Formularfelder durchchecken bei der Eingabe eines Datensatzes (nicht f�r Feldeingabe/-definition)
* @return
*/
$fehler = array();
$this->insert_ok = true;

// wenn keine einzige Metaebene ausgew�hlt ist
if (empty($this->checked->mv_metaebenen)
	&& $this->checked->zweiterunde == "ja")
{
	$this->insert_ok = false;echo "NOOOO";debug_backtrace();
	$fehler[] = $this->content->template['plugin']['mv']['meta_waehlen'];
}
if ($this->checked->mv_submit == $this->content->template['plugin']['mv']['Eintragen']
		OR $this->checked->mv_submit == $this->content->template['plugin']['mv']['aendern']
		OR $this->checked->picdel
		OR $this->checked->upload)
{
	// Alle Felder des Formulars raussuchen
	$sql = sprintf("SELECT mvcform_name,
							mvcform_id,

							mvcform_must_back,
							mvcform_type,
							mvcform_minlaeng,
							mvcform_maxlaeng,
							mvcform_content_type,
							mvcform_label
							FROM %s, %s
							WHERE mvcform_form_id = '%d' 
							AND mvcform_aktiv = 1
							AND mvcform_lang_id = mvcform_id	
							AND mvcform_lang_lang = '%d' 
							AND mvcform_lang_meta_id = mvcform_meta_id 
							AND mvcform_meta_id = '%d'
							GROUP BY mvcform_id",
							
							$this->cms->tbname['papoo_mvcform'],
							
							$this->cms->tbname['papoo_mvcform_lang'],
							
							$this->db->escape($this->checked->mv_id),
							$this->db->escape($this->cms->lang_back_content_id),
							$this->db->escape($this->meta_gruppe)
					);
	$result = $this->db->get_results($sql);
	// Durchgehen
	// gibt es eine Liste? Und ist es nicht das erste Mal, dass die Seite aufgerufen wird?
	if (!empty($result))
	{
		foreach($result as $daten)
		{
			// Feldname setzen
			$name = $daten->mvcform_name . "_" . $daten->mvcform_id;
			// wenn leeres Feld
			$this->checked->$name = trim($this->checked->$name);
			if ((empty($this->checked->$name)&& $daten->mvcform_type != "password")
				OR (empty($this->checked->$name) && $daten->mvcform_type == "password" && empty($this->checked->mv_content_id)))
			{
				if ($daten->mvcform_type == "picture"
					OR $daten->mvcform_type == "file")
				{
					if ($this->checked->template == "mv/templates/change_user.html" ||
						$this->checked->template == "mv/templates/fp_content.html")
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
						if ($daten->mvcform_must_back == 1) {
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
				// Markierung f�r neue Feldtypen
				// Zeitstempel: extra Formatierung in TT.MM.JJJJ
				elseif ($daten->mvcform_type == "timestamp")
				{
					$name_tag = "mvcform_tag_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$name_monat = "mvcform_monat_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$name_jahr = "mvcform_jahr_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					// wenn eins der drei Felder JJ MM JJJJ leer ist dann error
					if ($daten->mvcform_must_back == 1
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
				elseif($daten->mvcform_type == "zeitintervall")
				{
					$anfang_tag = "anfang_tag_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$ende_tag = "ende_tag_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$anfang_monat = "anfang_monat_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$ende_monat = "ende_monat_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$anfang_jahr = "anfang_jahr_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					$ende_jahr = "ende_jahr_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
					// wenn eins der Felder JJ MM JJJJ leer ist dann error
					if ($daten->mvcform_must_back == 1
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
				elseif($daten->mvcform_type == "multiselect")
				{
					// Flag ob was ausgew�hlt wurde erstmal nicht setzen
					$treffer = false;
					// wieviel Lookupwerte gibt es f�r dieses Feld?
					$sql = sprintf("SELECT COUNT(lookup_id)
											FROM %s
											WHERE lang_id = '%d'",
											
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($this->checked->mv_id)
											. "_lang_"
											. $this->db->escape($daten->mvcform_id),
											
											$this->db->escape($this->cms->lang_back_content_id)
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
						AND $daten->mvcform_must_back == 1)
					{
						// dann Fehlermeldung raus
						$this->error[$name] = "error";
						$this->errortext[$name] = $this->content->template['plugin']['mv']['pflichtfeld'];
						$this->insert_ok = false;
					}
				}
				// alle anderen Felder, die nicht ausgef�llt sind ergeben auch einen error
				elseif ($daten->mvcform_must_back)
				{
					$this->error[$name] = "error";
					$this->errortext[$name] = $this->content->template['plugin']['mv']['pflichtfeld'];
					$this->insert_ok = false;
				}
			}
			// alpha / numerisch?
			if (!empty($this->checked->$name)
				AND $daten->mvcform_content_type == "num"
				AND $daten->mvcform_type == "text"
				AND !ctype_digit($this->checked->$name))
			{
				$this->error[$name] = "error";
				$this->errortext[$name] = $this->content->template['plugin']['mv']['num_error'];
				$this->insert_ok = false;
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
				if (0 === preg_match('~^(http://|https://|/)~', $this->checked->$name))
				{
					$this->checked->$name = "http://" . $this->checked->$name;
				}

			}
		#}
			// Mindestl�nge
			if ($daten->mvcform_minlaeng > strlen($this->checked->$name)
				&& strlen($this->checked->$name)
				&& ($daten->mvcform_type == "text"
					|| $daten->mvcform_type == "textarea"
					|| $daten->mvcform_type == "textarea_tiny"
					|| $daten->mvcform_type == "password"))
			{
				$this->error[$name] = "error";
				$this->errortext[$name] = $this->content->template['plugin']['mv']['min_laenge']
											. $daten->mvcform_minlaeng
											. $this->content->template['plugin']['mv']['zeichen'];
				$this->insert_ok = false;
			}
			// Maxl�nge
			if ($daten->mvcform_maxlaeng < strlen($this->checked->$name)
				&& ($daten->mvcform_type == "text" OR $daten->mvcform_type == "password")
				&& $daten->mvcform_maxlaeng != 0)
			{
				$this->error[$name] = "error";
				$this->errortext[$name] = $this->content->template['plugin']['mv']['max_laenge']
											. $daten->mvcform_maxlaeng
											. $this->content->template['plugin']['mv']['zeichen'];
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
			// Ist das Feld leer? Wenn es daf�r ein abh�ngiges Pflichtfeld gibt, muss dieses auch leer sein (??)
			if ($this->checked->$name == "")
			{
				// gibts es ein abh�ngiges Pflichtfeld?
				$required_feld = $this->get_required_feld($name);
				if (!empty($required_feld))
				{
					// ja, dann gehe die Pflicht-Felder durch
					foreach($required_feld as $treffer) 
					{
						$asv = $treffer->mvcform_name . "_" . $treffer->mvcform_id;
						// ist das Pflicht-Feld ein Datum? Dann m�ssen Tag/Monat/Jahr gechecked werden, ob sie auch alle drei leer sind
						if ($treffer->mvcform_type == "timestamp")
						{
							$name_tag = "mvcform_tag_" . $asv;
							$name_monat = "mvcform_monat_" . $asv;
							$name_jahr = "mvcform_jahr_" . $asv;
							// die "00"er kommen aus der Datenbank, wenn ein Mitglied bearbeitet wird und das Datumsfeld leer war
							if ((!empty($this->checked->$name_tag)
								&& !empty($this->checked->$name_monat)
								&& !empty($this->checked->$name_jahr))
								&& ($this->checked->$name_tag != "00"
									&& $this->checked->$name_monat != "00"
									&& $this->checked->$name_jahr != "0000"))
							{
								$this->error[$name] = "error";
								$this->insert_ok = false;
							}
						}
						// Zeitintervall
						elseif($treffer->mvcform_type == "zeitintervall")
						{
							$anfang_tag = "anfang_tag_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
							$ende_tag = "ende_tag_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
							$anfang_monat = "anfang_monat_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
							$ende_monat = "ende_monat_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
							$anfang_jahr = "anfang_jahr_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
							$ende_jahr = "ende_jahr_" . $daten->mvcform_name . "_" . $daten->mvcform_id;
							// wenn eins der Felder TT MM JJJJ leer ist dann error
							if (empty($this->checked->$anfang_tag)
								|| empty($this->checked->$ende_tag)
								|| empty($this->checked->$anfang_monat)
								|| empty($this->checked->$ende_monat)
								|| empty($this->checked->$anfang_jahr)
								|| empty($this->checked->$ende_jahr))
							{
								$this->error[$name] = "error";
								$this->errortext[$name] = $this->content->template['plugin']['mv']['datum_komplett'];
								$this->insert_ok = false;
							}
						}
						// ist das andere Feld ein Multiselect Feld?
						elseif($treffer->mvcform_type == "multiselect") {}
						// kein Datum, kein Multiselect dann checken ob das Feld leer ist
						else
						{
							if (!empty($this->checked->$asv))
							{
								$this->error[$name] = "error";
								$this->insert_ok = false;
							}
						}
						// ist das leere Mutliselect Feld auch wirklich leer? die $_SESSION muss gechecked werden
						if ($daten->mvcform_type == "multiselect")
						{
							if (!empty($this->checked->$asv))
							{
								$this->error[$name] = "error";
								$this->insert_ok = false;
								if (!empty($_SESSION["mvcform" . $name]))
								{
									foreach($_SESSION["mvcform" . $name] as $multiwert)
									{
										// gibt es irgendweinen Session wert daf�r der nicht leer ist?
										if ($multiwert != "")
										{
											// dann doch alles in Ordnung
											$this->error[$name] = "";
											$this->insert_ok = true;
										}
									}
								}
								else
								{
									$this->error[$name] = "";
									$this->insert_ok = true;
								}
							}
						}
					}
				}
			}
			// Fileupload, check extensions
			if ($daten->mvcform_type == "file" and $daten->mvcform_must_back)
			{
				if(!is_dir(PAPOO_ABS_PFAD . "/files/temp")) {
					mkdir(PAPOO_ABS_PFAD . "/files/temp");
				}

				$tempfilepath = PAPOO_ABS_PFAD . "/files/temp/{$this->user->userid}_form_temp.temp";
				$filetoload = $_FILES["mvcform" . $name]['name'];

				if(empty($filetoload) and is_file($tempfilepath))
				{
					$file_contents = file_get_contents($tempfilepath);

					$previously_uploaded = [];
					$i = 0;
					foreach(preg_split("/((\r?\n)|(\r\n?))/", $file_contents) as $line)
					{
						if(empty($line)) {
							continue;
						}

						if($i % 2 == 0) {
							$previously_uploaded[] = [$line, null];
						}
						else 
						{
							end($previously_uploaded);

							$unfinished_last_element = &$previously_uploaded[key($previously_uploaded)];

							$unfinished_last_element[1] = $line;
						}
						$i++;
					}

					$found_previously_uploaded_file = false;

					$previously_uploaded = array_reverse($previously_uploaded);
					foreach($previously_uploaded as $uploaded_file)
					{
						if($uploaded_file[0] === ("mvcform" . $name) and is_file(PAPOO_ABS_PFAD . "/files/" . $uploaded_file[1])) 
						{
							$found_previously_uploaded_file = $uploaded_file[1];
							break;
						}
					}

					if(is_string($found_previously_uploaded_file)) {
						$filetoload = $found_previously_uploaded_file;
					}
				}

				//debug::print_d($previously_uploaded);
				if(empty($filetoload)) 
				{
					$this->content->template['mv_upload_error'] = $this->content->template['plugin']['mv']['keine_datei_angegegeben'];
					$this->insert_ok = false;
				}
				else
				{
					$data = "mvcform" . $name . "\nTMP_" . $filetoload . "\n";
					file_put_contents($tempfilepath, $data, FILE_APPEND);
				}
			}
	
			// Bildupload
			if ($daten->mvcform_type == "picture" and $daten->mvcform_must_back)
			{
				if(!is_dir(PAPOO_ABS_PFAD . "/images/temp")) {
					mkdir(PAPOO_ABS_PFAD . "/images/temp");
				}

				$tempfilepath = PAPOO_ABS_PFAD . "/images/temp/{$this->user->userid}_form_temp.temp";
				$filetoload = $_FILES["mvcform" . $name]['name'];

				$check = 'mvcform' . $daten->mvcform_label . '_' . $daten->mvcform_id . '_already_uploaded';
				//ist bereits ein bild hinterlegt?
				if($this->checked->$check != '1') {
					if (empty($filetoload)) {
						if (is_file($tempfilepath)) {
							$file_contents = file_get_contents($tempfilepath);

							$previously_uploaded = [];
							$i = 0;
							foreach (preg_split("/((\r?\n)|(\r\n?))/", $file_contents) as $line) {
								if (empty($line)) {
									continue;
								}

								if ($i % 2 == 0) {
									$previously_uploaded[] = [$line, null];
								} else {
									end($previously_uploaded);

									$unfinished_last_element = &$previously_uploaded[key($previously_uploaded)];

									$unfinished_last_element[1] = $line;
								}
								$i++;
							}

							$found_previously_uploaded_file = false;

							$previously_uploaded = array_reverse($previously_uploaded);
							foreach ($previously_uploaded as $uploaded_file) {
								if ($uploaded_file[0] === ("mvcform" . $name) and is_file(PAPOO_ABS_PFAD . "/images/" . $uploaded_file[1])) {
									$found_previously_uploaded_file = $uploaded_file[1];
									break;
								}
							}

							if (is_string($found_previously_uploaded_file)) {
								$filetoload = $found_previously_uploaded_file;
							}
						}
						$this->image_core->image_load($filetoload);
						$image_infos = $this->image_core->image_infos;

						//echo "<pre>"; print_r($image_infos); echo "</pre>"; exit;

						// Test: Bild-Typ (JPG, GIF, PNG)
						if (!$image_infos['type']) {
							// Fehlermeldung ausgeben
							$this->content->template['mv_upload_error'] = $this->content->template['plugin']['mv']['falsche_bild_dateiendung'];
							// tempor�re Bild-Datei l�schen
							@unlink($image_infos['bild_temp']);
							// Formular zum Upload einer Bild-Datei anbieten
							$this->content->template['template_weiche'] = 'HOCHLADEN';
							$this->insert_ok = false;
						} // Test: Bild zu gro�
						elseif ($image_infos['breite'] > $this->pic_max_breite || $image_infos['hoehe'] > $this->pic_max_hoehe) {
							// Fehlermeldung ausgeben
							$this->content->template['mv_upload_error'] = $this->content->template['plugin']['mv']['zu_gross'];
							// tempor�re Bild-Datei l�schen
							@unlink($image_infos['bild_temp']);
							// Formular zum Upload einer Bild-Datei anbieten
							$this->content->template['template_weiche'] = 'HOCHLADEN';
							$this->insert_ok = false;
						}
					}
					else {
						$data = "mvcform" . $name . "\nTMP_10-" . $filetoload . "\n";
						file_put_contents($tempfilepath, $data, FILE_APPEND);
					}
				}
			}
			//Checken ob der Benutzername schon existiert
			if ($daten->mvcform_name == "Benutzername" AND !$this->checked->$name)
			{
				$this->error[$name] = "error";
				$this->errortext[$name] = $this->content->template['plugin']['mv']['user_empty'];
				$this->insert_ok = false;
			}
			elseif ($daten->mvcform_name == "Benutzername")
			{
				$this->finde_die_art_der_verwaltung_heraus();
				if ($this->mv_art == 2) // = Mitglieder
				{
					$checkuser = $this->mv_check_ob_user_existiert($this->checked->$name);
					if ($checkuser == false)
					{
						$this->error[$name] = "error";
						$this->errortext[$name] = $this->content->template['plugin']['mv']['user_exists'];
						$this->insert_ok = false;
					}
				}
			}
			if ($this->checked->$name AND $this->checken_ob_mail_existiert)
			{
				//Checken ob die Email schon existiert
				if ($daten->mvcform_name == "email")
				{
					$this->finde_die_art_der_verwaltung_heraus();
					if ($this->mv_art == 2)
					{
						$checkemail = $this->mv_check_ob_email_existiert($this->checked->$name);
						if ($checkemail == false)
						{
							$this->error[$name] = "error";
							$this->errortext[$name] = $this->content->template['plugin']['mv']['email_exists'];
							$this->insert_ok = false;
						}
					}
				}
			}
			// gibts einen error? dann das Feldlabel f�r die Fehlerausgabe zwischenspeichern
			if (!empty($this->error[$name])) $fehler[] = $daten->mvcform_label . ": " . $this->errortext[$name];
		}
	}
}
// wenn es keine Liste gibt, dann kann auch nichts eingetragen werden
else $this->insert_ok = false;// Fehlerliste ans Template weitergeben
$this->content->template['fehlerliste'] = $fehler;
?>
