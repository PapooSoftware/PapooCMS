<?php
// Vor Aufruf m�ssen folgende Variablen gesetzt werden:
// $mv_content_id, $update_insert
//print_r($sql_spalten);
/**
* Sprachtabellen f�r die schnellere Suche updaten (??)
*
* called by change_user.php (update), change_user_front.php (update), edit_own_front.php (upodate),
// fp_content.php (insert), fp_content_front() in mv.php (insert)
*/
$sql = sprintf("SELECT mv_lang_id
						FROM %s",
						$this->cms->tbname['papoo_mv_name_language']
				);
$sprachen = $this->db->get_results($sql);
// Inhaltsdaten holen (wurden durch make_content_entry.php erstellt)
#$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
$sql = sprintf("SELECT * FROM %s 
							WHERE mv_content_id = '%d'",
							
							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id)
							. "_search_"
							. $lang,
							
							$this->db->escape($mv_content_id)
				);
#$result = $this->db->get_results($sql);
$message_mail = "";
$datum_eintrag = "";
// was f�r eine Art von Feld ist es?
$sql = sprintf("SELECT DISTINCT mvcform_id,
								mvcform_type,
								mvcform_label,
								mvcform_lang_id,
								mvcform_lang_lang,
								mvcform_lang_dependence
								FROM %s, %s 
								WHERE mvcform_form_id = '%s' 
								AND mvcform_meta_id = '%d'
								AND mvcform_lang_id = mvcform_id
								AND mvcform_lang_lang = '%d'",
								
								$this->cms->tbname['papoo_mvcform'],
								
								$this->cms->tbname['papoo_mvcform_lang'],
								
								$this->db->escape($this->checked->mv_id),
								$this->db->escape($this->meta_gruppe),
								$this->cms->lang_id
				);
$mvcform_types = $this->db->get_results($sql, ARRAY_A);
#foreach($mvcform_types as $dft)
#{
#	$formtypen[$dft['mvcform_id']]['mvcform_type'] = $dft['mvcform_type'];
#	$formtypen[$dft['mvcform_id']]['mvcform_label'] = $dft['mvcform_label'];
#	$formtypen[$dft['mvcform_id']]['mvcform_lang_dependence'] = $dft['mvcform_lang_dependence'];
#}
if (empty($this->checked->mv_content_sperre)) $this->checked->mv_content_sperre = 0;
// wenn admin, dann darf er auch sperren/entsperren
if ($this->have_admin_rights())
{
	$sperre_sql = "mv_content_sperre='" . $this->db->escape($this->checked->mv_content_sperre) . "'";
	// wenn dzvhae sonderfall und Mitgliederverwaltung
	if ($this->dzvhae_system_id 
		&& $this->is_mv_or_st()) $sperre_sql = $this->checked->active_7 == "1" ? "mv_content_sperre='0'" : "mv_content_sperre='1'";
}
// FE: Wenn nicht Admin und das H�kchen f�r direkte Freigabe nicht gesetzt ist
if (!defined("admin")) $sperre_sql = $this->mv_meta_allow_direct_unlock != 1 ? "mv_content_sperre='1'" : "mv_content_sperre='0'";
if (empty($this->checked->mv_content_sperre)) $this->checked->mv_content_sperre = 0;
if ($noaktive == "ok") $sperre_sql = "mv_content_sperre='" . $this->db->escape($this->checked->mv_content_sperre) . "'";
if ($sperre_sql)
{
	if ($this->content_sperre_sprachabhaengig) $sql_spalten_abh .= "," . $sperre_sql;
	else $sql_spalten .= "," . $sperre_sql;
}

// bei neuem Satz dem INSERT eine mv_content_id mitgeben. make_content_entry hat schon einen dummy-Satz angelegt, um eine insert_id zu erhalten.
if ($update_insert == "insert") $sql_spalten .= ",mv_content_id=" . $mv_content_id;
// Hole Mailadresse aus 1. Emailfeld, wenn FE und nicht Mitgliederverwaltung
if (!defined("admin"))
{
	$this->finde_die_art_der_verwaltung_heraus();
	if ($this->mv_art != 2) //2 = MV
	{
		// Erstes Feld vom Typ Email ermitteln
		foreach ($this->checked AS $key => $value)
		{
			$my_array = explode("_", $key);
			$feldid = end($my_array);
			if (is_numeric($feldid))
			{
				// Hole den Feldnamen, wenn es ein email feld ist
				$sql = sprintf("SELECT mvcform_name
										FROM %s
										WHERE mvcform_id = '%d'
										AND mvcform_type = 'email'
										LIMIT 1",
										$this->cms->tbname['papoo_mvcform'],
										$this->db->escape($feldid)
								);
				$result = $this->db->get_var($sql);
				if ($key == $result . "_" . $feldid)
				{
					$emails_eintrag = $value;
					break;
				}
			}
		}
	}
	else $emails_eintrag = $this->checked->email_3; // MV
	if (!$emails_eintrag) $emails_eintrag = $this->checked->email_3;
}
elseif (!$emails_eintrag
	AND $this->mail_to_user_if_admin_creates) $emails_eintrag = $this->checked->email_3;
if (!empty($sprachen))
{
	foreach($sprachen as $sprache)
	{
		$message_mail = "";
		// BE/FE Inhaltssprache
		$lang_id = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		// sprachenabh�ngige �nderung
		if ($update_insert == "update")
		{
			if ($sql_spalten) // wenn es sprachenunabh�ngige Felder gibt
			{
				$sql = sprintf("UPDATE %s SET %s 
											WHERE mv_content_id = '%d'",
											
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($this->checked->mv_id)
											. "_search_"
											. $this->db->escape($sprache->mv_lang_id),
											
											$sql_spalten,
											$this->db->escape($mv_content_id)
								);
				$this->db->query($sql);
				//print_r($sql);exit();
			}
			if ($sql_spalten_abh
				AND $lang_id == $sprache->mv_lang_id) // wenn es sprachenabh�ngige Felder gibt und die sprachenabh�ngigen Felder noch nicht gespeichert sind
			{
				$sql = sprintf("UPDATE %s SET %s 
											WHERE mv_content_id = '%d'",
											
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($this->checked->mv_id)
											. "_search_"
											. $this->db->escape($sprache->mv_lang_id),
											
											$sql_spalten_abh,
											$this->db->escape($mv_content_id)
								);
				$this->db->query($sql);
				//print_r($sql);exit();
			}
		}
		if ($update_insert == "insert")
		{
			// sprachenunabh�ngige Speicherung
			if ($sql_spalten) // wenn es sprachenunabh�ngige Felder gibt
			{
				// Satz f�r aktuelle Sprache wurde schon angelegt, s. make_content_entry.php
				if ($lang_id == $sprache->mv_lang_id)
				{
					$sql_cmd = "UPDATE ";
					$where = " WHERE mv_content_id=" . $mv_content_id;
				}
				else
				{
					$sql_cmd = "INSERT INTO ";
					$where = "";
				}
				$sql = sprintf("%s %s SET %s %s",
				
											$sql_cmd,
											
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($this->checked->mv_id)
											. "_search_"
											. $this->db->escape($sprache->mv_lang_id),
											
											$sql_spalten,
											$where
								);
				$this->db->query($sql);
			}
			// sprachenabh�ngige Speicherung (nur einmal f�r die eingestellte Sprache speichern)
			if ($sql_spalten_abh) // wenn es sprachenabh�ngige Felder gibt und die sprachenabh�ngigen Felder noch nicht gespeichert sind
			{
				if ($sql_spalten
					AND $lang_id == $sprache->mv_lang_id) // wurden schon sprachenunabh�ngige Felder eingef�gt? (Frage ob UPDATE oder INSERT)
				{
					$sql = sprintf("UPDATE %s SET %s
												WHERE mv_content_id = '%d'",
				
												$this->cms->tbname['papoo_mv']
												. "_content_"
												. $this->db->escape($this->checked->mv_id)
												. "_search_"
												. $this->db->escape($lang_id),
												
												$sql_spalten_abh,
												$this->db->escape($mv_content_id)
									);
					$this->db->query($sql);
				}
				elseif ($lang_id == $sprache->mv_lang_id) // es gab keine sprachenunabh�ngigen Felder, daher die sprachenabh�ngigen einf�gen statt UPDATE
				{
	//TEST ???
					$sql = sprintf("INSERT IGNORE INTO %s SET %s ",
				
												$this->cms->tbname['papoo_mv']
												. "_content_"
												. $this->db->escape($this->checked->mv_id)
												. "_search_"
												. $this->db->escape($lang_id),
												
												$sql_spalten_abh
									);
					$this->db->query($sql);
				}
			}
		}
	}
}
// Mailversand nur wenn FE und Mailadresse angegeben oder BE und Schalter an und Mailadresse angegeben
if ((defined("admin") AND $this->mail_to_user_if_admin_creates)
	OR !defined("admin"))
{
	if ($emails_eintrag) // Wenn keine Usermailadresse, dann auch keine Benachrichtigung. Kann im FE nur auftreten, wenn ohne Login
	{
		$mode = "";
		if ($this->checked->template == "mv/templates/mv_create_front.html") $mode = "create";
		elseif ($this->checked->template == "mv/templates/mv_edit_front.html") $mode = "change";
		elseif ($this->checked->template == "mv/templates/mv_edit_own_front.html") $mode = "change";
		elseif ($this->checked->template == "mv/templates/fp_content.html"
			AND $this->mail_to_user_if_admin_creates) $mode = "create";
		elseif ($this->checked->template == "mv/templates/change_user.html"
			AND $this->mail_to_user_if_admin_creates) $mode = "change";
		if ($mode) // kein einziger Versand, wenn der Aufruf nicht durch eins der drei Templates erfolgt
		{
			// Link zum Eintrag aufbauen, kommt ins message body
			$save_link = $link = "\n http://"
								. $this->cms->title_send
								. PAPOO_WEB_PFAD
								. "/plugin.php?menuid="
								. $this->checked->menuid
								. "&template=mv/templates/mv_show_front.html&mv_id="
								. $this->checked->mv_id
								. "&extern_meta="
								. $this->checked->extern_meta
								. "&mv_content_id="
								. $this->checked->mv_content_id
								. "";
			// switch, viell. kommt ja sp�ter mal mehr hinzu...
			switch ($mode)
			{
				case "create":
					$subject = $this->content->template['plugin']['mv']['email_betreff_anmeldung'];
					break;
				case "change":
					$subject =  $this->content->template['plugin']['mv']['email_betreff_aenderung'];
					break;
			}
			$save_subject = $subject = trim($subject);
			// Datensatz-Inhalt ohne Klartexte f�r select etc.
			$sql = sprintf("SELECT * FROM %s
									WHERE mv_content_id = '%d'",
								
									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($this->checked->mv_id)
									. "_search_"
									. $this->db->escape($this->cms->lang_id),
									
									$this->db->escape($this->checked->mv_content_id)
						);
			$new_data = $this->db->get_results($sql, ARRAY_A);
			// Mail-Texte holen
			$sql = sprintf("SELECT *
									FROM %s
									WHERE mv_meta_lang_id = '%d'
									AND mv_meta_lang_lang_id = '%d'",
									
									$this->cms->tbname['papoo_mv']
									. "_meta_lang_"
									. $this->db->escape($this->checked->mv_id),
									
									$this->db->escape($this->meta_gruppe),
									$this->db->escape($this->cms->lang_id)
							);
			$mail_texts = $this->db->get_results($sql, ARRAY_A);
			// Admin-Mailadresse(n) holen
			$sql = sprintf("SELECT mv_meta_emails
									FROM %s
									WHERE mv_meta_id = '%d'",
									
									$this->cms->tbname['papoo_mv']
									. "_meta_"
									. $this->db->escape($this->checked->mv_id),
									
									$this->db->escape($this->meta_gruppe)
							);
			$send_to = $this->db->get_var($sql);
			// Mailadresse aus dem Formular immer an den Anfang, wenn vorhanden. Landet im array hier: $send_to_ary['0']
			$emails_eintrag = trim($emails_eintrag);
			if ($emails_eintrag) $send_to = trim($emails_eintrag . "\n" . $send_to);
			// 0 to User, 1 from & to, 2 - x weitere Recipients (to)
			$send_to_ary = explode("\n", $send_to);
			if (count($send_to_ary)) // kein Versand, wenn nicht mind. 1 Adresse f�r from (1. Adresse ist from und to)
			{
				$from = trim($send_to_ary['0']); // Absender, entweder aus FE oder 1. Eintrag im BE
				$save_from = $from;
				$save_mode = $mode;
				include(PAPOO_ABS_PFAD . "/plugins/mv/lib/user_send_mail.php"); // Usermod
				$user_send_mail = new user_send_mail();
				$save_checked = new stdClass();
				$first_iteration_foreach = true;
				foreach($send_to_ary as $to) // an alle Empf�nger senden
				{
					$save_to_type = $to_type = ($first_iteration_foreach) ? "user" : "admin";
					$first_iteration_foreach = false;
					if (trim($to)) // skip, falls leer
					{
						// Mail an Admin, wenn nicht MV, dann kommt die Absenderadresse aus dem FE oder dem 1. Eintrag im BE
						#if ($to_type == "admin"
						#	AND $this->mv_art != 2) $from = $save_from = $send_to_ary['0'];
						$body = trim($mail_texts[0]['mv_mail_text_' . $mode . "_" . $to_type]);
						// $this->checked vor �nderungen sch�tzen, dazu erst einmal sichern
						foreach ($this->checked as $key => $value) { $save_checked->$key = $value; }
						// Passwort vor User-Mod verstecken
						if ($new_data[0]['passwort_2'])
						{
							$new_data[0]['passwort_2'] = $this->checked->passwort_2 = "";
							if (count($old_data)) $old_data[0]['passwort_2'] = "";
						}
						$save_new_data = $new_data;
						// kundenspezifisches Mod aufrufen, falls eines ben�tigt wird
						// Usermod ausf�hren
						$rc = $user_send_mail->send_mail($subject, $body, $to, $to_type, $from, $link, $mode, $old_data, $new_data, $this->checked);
						// $this->checked wiederherstellen, falls ge�ndert
						unset($this->checked); // wegwerfen
                        $this->checked =  new checked_class();
						foreach ($save_checked as $key => $value) { $this->checked->$key = $value; } // restore $this->checked
						$from = $save_from; // gesch�tzt vor �nderung, restore
						$mode = $save_mode;
						$to_type = $save_to_type;
						$new_data = $save_new_data;
						$body = preg_replace('/#link#/', $link, $body);
						$body = preg_replace('/#MVID#/', $this->checked->mv_id, $body);
						$body = preg_replace('/#ID#/', $this->checked->mv_content_id, $body); // create ??
						$body = preg_replace('/#from#/', $from, $body);
						$body = preg_replace('/#to#/', $to, $body);
						$body = preg_replace('/#date#/', date("d.m.Y H:i:s"), $body);
						if (count($new_data))
						{
							foreach ($new_data AS $key_nd => $value_nd)
							{
								foreach ($value_nd AS $key => $value)
								{
									if (!($key == "mv_content_id"
										|| $key == "mv_content_owner"
										|| $key == "mv_content_userid"
										|| $key == "mv_content_sperre"
										|| $key == "mv_content_teaser"
										|| $key == "mv_content_create_date"
										|| $key == "mv_content_create_owner"
										|| $key == "mv_content_edit_date"
										|| $key == "mv_content_edit_user"
										|| $key == "mv_content_search"))
									{
										$suffix = substr($key, (strrpos($key, "_")) + 1);
										$fieldname = substr($key, 0, strrpos($key, "_"));
										foreach ($mvcform_types as $keyx => $valuex)
										{
											if ($valuex['mvcform_id'] == $suffix)
											{
												$mvcform_type = $valuex['mvcform_type'];
												break;
											}
										}
										switch ($mvcform_type)
										{
											default: break;
											case "select":
											case "radio":
											case "check":
												$value = $this->get_lp_wert_front($suffix, $fieldname, $this->db->escape($this->checked->mv_content_id));
												break;
											case "multiselect":
												$value = $this->get_multiselect_werte($suffix, $fieldname, $this->db->escape($this->checked->mv_content_id));
												break;
											case "pre_select":
												$value = $this->get_lp_wert_front($suffix, $fieldname, $this->db->escape($this->checked->mv_content_id));
												$val_pre_array = explode("+++", $value);
												if (!empty($val_pre_array['0'])) $value = $val_pre_array['0'];
												break;
											case "timestamp":
												if ($value != "")
												{
													list($tag, $monat, $jahr) = $this->get_day_month_year($value);
													$value = $tag . "." . $monat . "." . $jahr;
												}
												break;
											// Wenn Zeitintervall Feld dann entsprechend Formatieren
											case "zeitintervall":
												list($anfang_datum, $ende_datum) = explode(",", $value);
												if (!empty($anfang_datum))
												{
													list($tag, $monat, $jahr) = $this->get_day_month_year($anfang_datum);
													$value = $tag . "." . $monat . "." . $jahr;
												}
												if ($anfang_datum != $ende_datum)
												{
													if (!empty($ende_datum))
													{
														list($tag, $monat, $jahr) = $this->get_day_month_year($ende_datum);
														$value .= $this->content->template['plugin']['mv']['bis'] . $tag . "." . $monat . "." . $jahr;
													}
												}
												break;
											case "password":
												$value = "******";
												break;
											case "preisintervall":
												$value = $value . " " . $this->get_feld_waehrung($suffix, $this->checked->mv_id);
												break;
										}
										$body = preg_replace('/#' . $key . '#/', $value, $body);
									}
								}
							}
						}#error_reporting(E_ALL);echo "tt=$to_type to=$to from=$from<br>";
						if (trim($subject) // ohne Inhalt kein Versand (k�nnen durch Usermod f�lschlich inhaltlich gel�scht worden sein)
							AND trim($body)
							AND trim($to)
							AND trim($from) // sicherheitshalber
							AND !$rc) // kein Versand, wenn Usermod rc = 1/true
						{#error_reporting(E_ALL);echo "tt=$to_type to=$to from=$from<br>";#exit;
							$this->mail_it->to = trim($to);
							#if ($to_type == "admin"
							#	AND $emails_eintrag != "") $this->mail_it->replyto = trim($emails_eintrag);
							$this->mail_it->from = trim($from);
							$this->mail_it->subject = trim($subject);
							$this->mail_it->body = $body;
							$this->mail_it->do_mail();
						}
					}
				}
			}
		}
	}
}
?>