<?php
/**
* Mitgliedsdaten �ndern
*/
#error_reporting(E_ALL ^ E_NOTICE);
$this->get_field_rights_special();
if ($this->user->userid != 11
	OR count($this->felder_rechte_aktuelle_gruppe_special))
{
	// checken, ob auch der richtig User das editieren will
	$sql = sprintf("SELECT mv_content_userid, 
							mv_content_owner FROM %s
							WHERE mv_content_id = '%d'",
							
							$this->cms->tbname['papoo_mv']
							. "_content_"
							. $this->db->escape($this->checked->mv_id)
							. "_search_"
							. $this->db->escape($this->cms->lang_id),
							
							$this->db->escape($this->checked->mv_content_id)
					);
	$result = $this->db->get_results($sql, ARRAY_A);
	//Spezialrechte rausholen (in Abh�ngigkeit eines best. Feldwertes)
	#$this->get_field_rights_special();
	//Testen, ob wg. der speziellen Gruppenrechte Schreibrechte bestehen
	foreach ($this->felder_rechte_aktuelle_gruppe_special as $rechtspezial)
	{
		// Name und Feldtyp holen f�r das Feld
		$sql = sprintf("SELECT mvcform_name,
								mvcform_type
								FROM %s 
								WHERE mvcform_id = '%d'
								LIMIT 1",
								
								$this->cms->tbname['papoo_mvcform'],
								
								$rechtspezial['field_id']
							);
		$name = $this->db->get_results($sql, ARRAY_A);
		// editok setzen, wenn Spezialrecht besteht
		if (count($name))
		{
			$field_wert =
				$this->get_lp_wert_search_tabs($rechtspezial['field_id'], $name[0]['mvcform_name'], $this->checked->mv_content_id, $this->cms->lang_id);
			// bei multiselect alle Werte durchsuchen
			if ($name[0]['mvcform_type'] == "multiselect")
			{
				$arr_field_werte = explode("\n", $field_wert);
				$treffer = array_search($rechtspezial['field_value'], $arr_field_werte);
				if (is_numeric($treffer)) $field_wert = $arr_field_werte[$treffer];
				else $field_wert = "";
			}
			if ($rechtspezial['field_value'] == $field_wert) $editok = "ok";
		}
	}
	// ist es der Autor des Eintrags oder das Mitglied selbst
	if ($result[0]['mv_content_userid'] == $this->user->userid 
		|| $result[0]['mv_content_owner'] == $this->user->userid
		|| $editok == "ok"
		|| $this->user_has_write_rights_for_current_flex_and_backend_access())
	{
		global $template;
		// hier geht es ums Editieren, deswegen...
		$this->content->template['altereintrag'] = 1;
		$this->content->template['edit'] = 1;
		$this->content->template['mv_content_id'] = $this->checked->mv_content_id;
		// im Frontend mit der $this->user->userid die mv_content_id aus der user Datenbanktabelle holen
		if ($this->checked->zweiterunde != "ja"
			&& $this->checked->template == "mv/templates/mv_edit_front.html"
			&& $this->checked->owner != "ja"
			&& empty($this->checked->mv_content_id))
		{
			$sql = sprintf("SELECT mv_content_id FROM %s
													WHERE mv_content_userid = '%d'",
													
													$this->cms->tbname['papoo_mv']
													. "_content_"
													. $this->db->escape($this->checked->mv_id)
													. "_search_"
													. $this->db->escape($this->cms->lang_id),
													
													$this->db->escape($this->user->userid)
							);
			$this->checked->mv_content_id = $this->db->get_var($sql);
			$this->content->template['mv_content_id'] = $this->checked->mv_content_id;
		}
		$this->content->template['userid'] = $this->checked->userid;
		$this->content->template['insert'] = $this->checked->insert;
		// wenn keine �nderung abgeschickt wurde, dann die Werte aus der Datenbank holen und einsetzen
		if (empty($this->checked->mv_submit))
		{
			if ($this->have_admin_rights()) $extra_sql = " AND mv_content_sperre <> '1'";
			$sql = sprintf("SELECT * FROM %s
										WHERE mv_content_id = '%s'
										%s",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($this->checked->mv_id)
										. "_search_"
										. $this->db->escape($this->cms->lang_id),
										
										$this->db->escape($this->checked->mv_content_id),
										$extra_sql
							);
			$result = $this->db->get_results($sql);
			$this->finde_die_art_der_verwaltung_heraus();
			// wenn MV oder Kalender
			if ($this->mv_art > 1) // 1 Std., 2 MV, 3 Kalender
			{
				//Wenn leer, dann schauen, ob es evtl. derjenige ist, der eingetragen hat
				if (empty($result))
				{
					$sql = sprintf("SELECT * FROM %s
												WHERE mv_content_id = '%s' 
												AND mv_content_owner = '%d'",
												
												$this->cms->tbname['papoo_mv']
												. "_content_"
												. $this->db->escape($this->checked->mv_id)
												. "_search_"
												. $this->db->escape($this->cms->lang_id),
												
												$this->db->escape($this->checked->mv_content_id),
												$this->user->userid
									);
					$result = $this->db->get_results($sql);
				}
			}
			if (!empty($result))
			{
				$spalten_namen = array();
				$spalten_namen = $this->get_spalten_namen();
				// Daten f�r das Mitglied aus der Datenbank holen
				$sql = sprintf("SELECT * FROM %s
											WHERE mv_content_id = '%s' 
											AND mv_content_sperre <> '1'",
											
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($this->checked->mv_id)
											. "_search_"
											. $this->db->escape($this->cms->lang_id),
											
											$this->db->escape($this->checked->mv_content_id)
								);
				$result3 = $this->db->get_results($sql);
				if ($this->mv_art > 1)
				{
					//Wenn leer, dann schauen, ob es evtl. derjenige ist, der eingetragen hat
					if (empty($result3))
					{
						$sql = sprintf("SELECT * FROM %s
													WHERE mv_content_id = '%s' 
													AND mv_content_owner = '%d'",
													
													$this->cms->tbname['papoo_mv']
													. "_content_"
													. $this->db->escape($this->checked->mv_id)
													. "_search_"
													. $this->db->escape($this->cms->lang_id),
													
													$this->db->escape($this->checked->mv_content_id),
													$this->user->userid
										);
						$result3 = $this->db->get_results($sql);
					}
				}
				$this->finde_die_art_der_verwaltung_heraus();
				// weise dem Formularfeld den Wert aus der Datenbank zu
				foreach ($spalten_namen as $form_feld_name_array)
				{
					$form_feld_name = $form_feld_name_array['mvcform_name'] . "_" . $form_feld_name_array['mvcform_id'];
					$name = $form_feld_name_array['mvcform_name'];
					$id = $form_feld_name_array['mvcform_id'];
					$sql = sprintf("SELECT * FROM %s
												WHERE mvcform_name = '%s' 
												AND mvcform_id = '%s' 
												AND mvcform_meta_id = '%d'",
												$this->cms->tbname['papoo_mvcform'],
												$this->db->escape($name),
												$this->db->escape($id),
												$this->db->escape($this->meta_gruppe)
									);
					$result4 = $this->db->get_results($sql);
					if ($result4[0]->mvcform_type == "password") $this->checked->$form_feld_name = "";
					elseif ($result4[0]->mvcform_type == "timestamp")
					{
						$tag = "mvcform_tag_" . $form_feld_name;
						$monat = "mvcform_monat_" . $form_feld_name;
						$jahr = "mvcform_jahr_" . $form_feld_name;
						list($this->checked->$tag,
								$this->checked->$monat,
								$this->checked->$jahr) = $this->get_day_month_year($result3[0]->$form_feld_name);
					}
					// beim Zeitintervall gleich zweimal umformatieren
					elseif ($result4[0]->mvcform_type == "zeitintervall")
					{
						$anfang_tag = "anfang_tag_" . $form_feld_name;
						$ende_tag = "ende_tag_" . $form_feld_name;
						$anfang_monat = "anfang_monat_" . $form_feld_name;
						$ende_monat = "ende_monat_" . $form_feld_name;
						$anfang_jahr = "anfang_jahr_" . $form_feld_name;
						$ende_jahr = "ende_jahr_" . $form_feld_name;
						$datum = $result3[0]->$form_feld_name;
						list($anfang_datum, $ende_datum) = explode(",", $datum);
						list($this->checked->$anfang_tag,
								$this->checked->$anfang_monat,
								$this->checked->$anfang_jahr) = $this->get_day_month_year($anfang_datum);
						list($this->checked->$ende_tag,
								$this->checked->$ende_monat,
								$this->checked->$ende_jahr) = $this->get_day_month_year($ende_datum);
					}
					// holt die Werte aus der Look Up Tabelle f�r dieses Mutliselect Feld und diesem Benutzer
					elseif ($result4[0]->mvcform_type == "multiselect") $this->set_multiselect_session($id, $name, $result[0]->$form_feld_name);
					elseif ($result4[0]->mvcform_type == "check"
							|| $result4[0]->mvcform_type == "radio"
							|| $result4[0]->mvcform_type == "select")
					{
						$field_name = $name . "_" . $id;
						$sql = sprintf("SELECT %s FROM %s
													WHERE mv_content_id = '%d'",
													$field_name,
													
													$this->cms->tbname['papoo_mv']
													. "_content_"
													. $this->db->escape($this->checked->mv_id)
													. "_search_"
													. $this->db->escape($this->cms->lang_id),
													
													$this->db->escape($this->checked->mv_content_id)
										);
						$lookup_id = $this->db->get_var($sql);
						$this->checked->$form_feld_name = $lookup_id;
					}
					else $this->checked->$form_feld_name = $result3[0]->$form_feld_name;
					if ($form_feld_name == "mv_content_userid") $this->content->template['userid'] = $result3[0]->$form_feld_name;
				}
			}
			// gab keinen Treffer, wegen "hacken" oder weil der Eintrag gesperrt ist
			else $this->content->template['sperre_drin'] = "jep";
		}
		// holt die Daten f�r die gew�nschte Metaebene aus der Datenbank
		$sql = sprintf("SELECT mv_meta_allow_direct_unlock 
								FROM %s, %s 
								WHERE mv_meta_id = '%d'
								AND mv_meta_lang_id = mv_meta_id
								AND mv_meta_lang_lang_id = '%d'",
								
								$this->cms->tbname['papoo_mv']
								. "_meta_"
								. $this->db->escape($this->checked->mv_id),
								
								$this->cms->tbname['papoo_mv']
								. "_meta_lang_"
								. $this->db->escape($this->checked->mv_id),
								
								$this->db->escape($this->meta_gruppe),
								$this->db->escape($this->cms->lang_back_content_id)
						);
		$this->mv_meta_allow_direct_unlock = $this->db->get_var($sql);
		// mv_sperre_zeigen setzen
		$this->finde_die_art_der_verwaltung_heraus();
		// ans Template Werte weitergeben
		if ($this->mv_meta_allow_direct_unlock == 0 or $this->mv_art == 2) $this->content->template['mv_sperre_zeigen'] = "nein"; //mv_sperre_zeigen
		// Sonderfall f�r dzvhae: Punkt 13 (Veranstalter kann selbst eingetragene Veranstaltung eigenst�ndig l�schen)
		// Hierdurch wird der L�sch-Button angezeigt. Das Feld FE-Sperre ist in mv_edit_front.html auskommentiert.
		if ($this->dzvhae_system_id
			AND $this->checked->mv_id == 4) $this->content->template['mv_sperre_zeigen'] = "ja";
		$this->content->template['mv_content_sperre'] = $result3[0]->mv_content_sperre;
		$this->save_multiselect_session_front();
		// Formular anzeigen
		$this->content->template['message1'] = "ok";
		// Formular raussuchen und anzeigen
		$this->content->template['formok'] = "ok";
		$this->content->template['mv_id'] = $this->checked->mv_id;
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_form_check_front.php');
		$this->front_get_form($this->checked->mv_id);
		// Soll  gel�scht werden
		if (!empty($this->checked->submitdelecht))
		{
			// Eintrag nach id l�schen und neu laden
			$sql = sprintf("DELETE FROM %s
									WHERE mv_content_id = '%s'",
									
									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($this->checked->mv_id),
									
									$this->db->escape($this->checked->mv_content_id)
							);
			#$this->db->query($sql);
			// in den Sprachtabellen l�schen
			$sql = sprintf("SELECT mv_lang_id 
									FROM %s",
									$this->cms->tbname['papoo_mv_name_language']
							);
			$sprachen = $this->db->get_results($sql);
			if (!empty($sprachen))
			{
				foreach ($sprachen as $sprache)
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
			if (defined("admin"))
				$location_url = $_SERVER['PHP_SELF']
								. "?menuid="
								. $this->checked->menuid
								. "&mv_id="
								. $this->checked->mv_id
								. "&template=mv/templates/userlist.html&fertig=del";
			else $location_url = $_SERVER['PHP_SELF']
								. "?menuid="
								. $this->checked->menuid
								. "&mv_id="
								. $this->checked->mv_id
								. "&template=mv/templates/mv_edit_front.html&fertig=del";
			if ($_SESSION['debug_stopallredirect'])
				echo '<a href="'
						. $location_url
						. '">'
						. $this->content->template['plugin']['mv']['weiter']
						. '</a>';
			else header("Location: $location_url");
			exit;
		}
		// Soll wirklich gel�scht werden?
		if (!empty($this->checked->submitdel))
		{
			$this->content->template['mv_content_id'] = $this->checked->mv_content_id;
			$this->content->template['mv_id'] = $this->checked->mv_id;
			$this->content->template['fragedel'] = "ok";
			$this->content->template['edit'] = "";
			// Holt nochmal die Werte f�r das Mitglied aus der Tabelle
			$result = $this->get_mv_content($this->checked->mv_content_id);
			foreach ($result as $row)
			{
				foreach ($row as $key => $value) { $was_del .= $key . " :: " . $value . "\n"; }
			}
			$this->content->template['was_del'] = $was_del;
		}
		// Daten speichern, wenn alle Felder korrekt sind und die �nderung abgeschickt wurde
		if (!$this->is_locked($this->checked->mv_content_id)
			&& $this->insert_ok == true
			&& !empty($this->checked->mv_submit)
			// OR $this->checked->upload
			OR $this->checked->picdel
		) {
			$this->save_temp_old_entry(); // Daten ans Template �bergeben
			require(PAPOO_ABS_PFAD . '/plugins/mv/lib/change_content_entry.php');
			// Sprachtabelle updaten
			$mv_content_id = $this->checked->mv_content_id;
			$update_insert = "update";
			require(PAPOO_ABS_PFAD . '/plugins/mv/lib/update_lang_search_row.php');
			$this->compare_content();
			// $_SESSION Werte f�r den multiselect wieder l�schen
			$sql = sprintf("SELECT * FROM %s
										WHERE mvcform_type = 'multiselect'",
										$this->cms->tbname['papoo_mvcform']
							);
			$result = $this->db->get_results($sql);
			if (!empty($result))
			{
				foreach ($result as $row)
				{
					if (!empty($_SESSION["mvcform"
								. $row->mvcform_name
								. "_"
								. $row->mvcform_id])) unset($_SESSION["mvcform" . $row->mvcform_name . "_" . $row->mvcform_id]);
				}
			}
			if ($this->checked->fertig != 1
				AND empty($this->checked->upload)
				AND empty($this->checked->picdel))
			{
				// Front- und Backend Template nach erfolgreichen �ndern der Daten sind verschieden wie folgt:
				// alter $link_template = "template=".$this->checked->template doch ka(besser kz) ob sicherheitsl�cke;)
				if ($this->checked->template == "mv/templates/mv_edit_front.html") $link_template = "template=mv/templates/mv_edit_front.html";
				else $link_template = "template=mv/templates/userlist.html";
				$location_url = $_SERVER['PHP_SELF']
								. "?menuid="
								. $this->checked->menuid
								. "&fertig=1&"
								. $link_template
								. "&insert=ok&mv_id="
								. $this->checked->mv_id
								. "&mv_content_id="
								. $this->checked->mv_content_id;
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
		$this->content->template['zweiterunde'] = "ja";
	}
	// wenn einer urls selber zusammelbastelt bzw. versucht Sachen zu �ndern die Ihn nix angehen, dann weisse Seite
	else
	{
		if ($this->checked->fertig == "del") $this->content->template['isdel'] = "ok";
		else $this->content->template['please_login'] = "ok";
	}
}
?>
