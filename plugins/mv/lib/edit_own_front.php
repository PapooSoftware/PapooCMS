<?php
if (empty($this->checked->mv_id))$this->checked->mv_id = "1";
$sql = sprintf("SELECT mv_content_id 
						FROM %s 
						WHERE mv_content_userid = '%d'",
						
						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_search_"
						. $this->db->escape($this->cms->lang_id),
							
						$this->db->escape($this->user->userid)
				);
$this->checked->mv_content_id = $this->db->get_var($sql);
// checken ob auch der richtig User das editieren will
$sql = sprintf("SELECT mv_content_userid,
						mv_content_owner 
						FROM %s 
						WHERE mv_content_id = '%d'",
						
						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_search_"
						. $this->db->escape($this->cms->lang_id),
							
						$this->db->escape($this->checked->mv_content_id)
			);
$result = $this->db->get_results($sql, ARRAY_A);
$this->content->template['mv_link'] = "plugin.php"
										. "?menuid="
										. $this->checked->menuid
										. "&mv_id="
										. $this->checked->mv_id
										. "&template="
										. $this->checked->template;
$sql = sprintf("SELECT mv_meta_main_lp_meta_id 
						FROM %s 
						WHERE mv_meta_main_lp_user_id = '%d'
						AND mv_meta_main_lp_mv_id = '%d'",
						
						$this->cms->tbname['papoo_mv_meta_main_lp'],
						
						$this->db->escape($this->checked->mv_content_id),
						$this->db->escape($this->checked->mv_id)
				);
$meta_main = $this->db->get_var($sql);
if ($meta_main != "5") $this->content->template['mv_zweiter_besuch'] = "ja";
// Arzt =3 Journalist = 4
if ($this->checked->mv_arzt_journalist == "3"
	|| $this->checked->mv_arzt_journalist == "4")
{
	// und neu eintragen
	$sql = sprintf("INSERT INTO %s SET mv_meta_lp_user_id = '%d',
										mv_meta_lp_meta_id = '%d',
										mv_meta_lp_mv_id = '%d'",
										
										$this->cms->tbname['papoo_mv_meta_lp'],
										
										$this->db->escape($this->checked->mv_content_id),
										$this->db->escape($this->checked->mv_arzt_journalist),
										$this->db->escape($this->checked->mv_id)
					);
	$this->db->query($sql);
	$sql = sprintf("UPDATE %s SET mv_meta_main_lp_meta_id = '%d'
								WHERE mv_meta_main_lp_user_id = '%d'
								AND mv_meta_main_lp_mv_id = '%d'",
								
								$this->cms->tbname['papoo_mv_meta_main_lp'],
								
								$this->db->escape($this->checked->mv_arzt_journalist),
								$this->db->escape($this->checked->mv_content_id),
								$this->db->escape($this->checked->mv_id)
					);
	$this->db->query($sql);
	$this->content->template['mv_zweiter_besuch'] = "ja";
}
// ist es der Autor des Eintrags oder das Mitglied selbst
if ($result[0]['mv_content_userid'] == $this->user->userid
	|| $result[0]['mv_content_owner'] == $this->user->userid)
{
	#global $template;
	// hier gehst ums Editieren, deswegen...
	$this->content->template['altereintrag'] = 1;
	$this->content->template['edit'] = 1;
	$this->content->template['mv_content_id'] = $this->checked->mv_content_id;
	// im Frontend mit der $this->user->userid die mv_content_id aus der user Datenbanktabelle holen
	if ($this->checked->zweiterunde != "ja"
		&& $this->checked->template == "mv/templates/mv_edit_own_front.html"
		&& $this->checked->owner != "ja"
		&& empty($this->checked->mv_content_id))
	{
		$sql = sprintf("SELECT mv_content_id 
								FROM %s 
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
	// wenn keine Änderung abgeschickt wurde, dann die Werte aus der Datenbank holen und einsetzen
	if (empty($this->checked->mv_submit))
	{
		$sql = sprintf("SELECT * 
								FROM %s 
								WHERE mv_content_id = '%s' ",
								
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_search_"
								. $this->db->escape($this->cms->lang_id),
							
								$this->db->escape($this->checked->mv_content_id)
						);
		$result = $this->db->get_results($sql);
		if (!empty($result))
		{
			$spalten_namen = array();
			$spalten_namen = $this->get_spalten_namen();
			// Daten für das Mitglied aus der Datenbank holen
			$sql = sprintf("SELECT * 
									FROM %s 
									WHERE mv_content_id = '%s' ",
									
									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($this->checked->mv_id)
									. "_search_"
									. $this->db->escape($this->cms->lang_id),
							
									$this->db->escape($this->checked->mv_content_id)
							);
			$result3 = $this->db->get_results($sql);
			//Felder Rechte rausholen
			$this->get_field_rights_schreibrechte();
			$i = 0;
			// weise dem Formularfeld den Wert aus der Datenbank zu
			foreach($spalten_namen as $form_feld_name_array)
			{
				$form_feld_name = $form_feld_name_array['mvcform_name'] . "_" . $form_feld_name_array['mvcform_id'];
				$die_aktuelle_id = $form_feld_name_array['mvcform_id'];
				if (is_numeric($die_aktuelle_id))
				{
					if (!in_array($die_aktuelle_id, $this->felder_schreib_rechte_aktuelle_gruppe))
					{
						$this->checked->$form_feld_name = "";
						$spalten_namen[$i] = "";
						continue; //Keine Leserechte - dann abbrechen
					}
				}
				$name = $form_feld_name_array['mvcform_name'];
				$id = $form_feld_name_array['mvcform_id'];
				$sql = sprintf("SELECT * 
										FROM %s 
										WHERE mvcform_name = '%s'
										AND mvcform_id = '%s'
										AND mvcform_meta_id = '%d'",
										$this->cms->tbname['papoo_mvcform'],
										$this->db->escape($name),
										$this->db->escape($id),
										$this->db->escape($this->meta_gruppe)
								);
				$result4 = $this->db->get_results($sql);
				// Markierung für neue Feldtypen
				if ($result4[0]->mvcform_type == "timestamp")
				{
					$tag = "mvcform_tag_" . $form_feld_name;
					$monat = "mvcform_monat_" . $form_feld_name;
					$jahr = "mvcform_jahr_" . $form_feld_name;
					list($this->checked->$tag,
							$this->checked->$monat,
							$this->checked->$jahr) = $this->get_day_month_year($result3[0]->$form_feld_name);
				}
				// beim Zeitintervall gleich zweimal umformatieren
				elseif($result4[0]->mvcform_type == "zeitintervall")
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
				// holt die Werte aus der Look Up Tabelle für dieses Mutliselect Feld und diesem Benutzer
				elseif($result4[0]->mvcform_type == "multiselect") $this->set_multiselect_session($id, $name, $result[0]->$form_feld_name);
				elseif($result4[0]->mvcform_type == "check"
					|| $result4[0]->mvcform_type == "radio"
					|| $result4[0]->mvcform_type == "select")
				{
					$this->checked->$form_feld_name = $lookup_id =
						$this->get_lp_wert_search_tabs($id, $form_feld_name_array['mvcform_name'], $this->checked->mv_content_id, $this->cms->lang_id);
				}
				else $this->checked->$form_feld_name = $result3[0]->$form_feld_name;
				if ($form_feld_name == "mv_content_userid") $this->content->template['userid'] = $result3[0]->$form_feld_name;
				$i++;
			}
		}
		// gab keinen Treffer, wegen "hacken" oder weil der Eintrag gesperrt ist
		else $this->content->template['sperre_drin'] = "jep";
	}
	$this->save_multiselect_session_front();
	// Formular anzeigen
	$this->content->template['message1'] = "ok";
	// Formular raussuchen und anzeigen
	$this->content->template['formok'] = "ok";
	$this->content->template['mv_id'] = $this->checked->mv_id;
	require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_form_check_front.php');
	#$this->make_form_check_front();
	$this->front_get_form($this->checked->mv_id);
	// Soll  gelöscht werden
	if (!empty($this->checked->submitdelecht))
	{
		// Eintrag nach id löschen und neu laden
		$sql = sprintf("DELETE FROM %s 
								WHERE mv_content_id = '%s'",
								
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id),
							
								$this->db->escape($this->checked->mv_content_id)
						);
		#$this->db->query($sql);
		// in den Sprachtabellen löschen
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
		$location_url = $_SERVER['PHP_SELF']
						. "?menuid="
						. $this->checked->menuid
						. "&mv_id=" . $this->checked->mv_id
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
	// Soll wirklich gelöscht werden?
	if (!empty($this->checked->submitdel))
	{
		$this->content->template['mv_content_id'] = $this->checked->mv_content_id;
		$this->content->template['mv_id'] = $this->checked->mv_id;
		$this->content->template['fragedel'] = "ok";
		$this->content->template['edit'] = "";
		// Holt nochmal die Werte für das Mitglied aus der Tabelle
		$result = $this->get_mv_content($this->checked->mv_content_id);
		foreach($result as $row)
		{
			foreach($row as $key => $value) { $was_del .= $key . " :: " . $value . "\n"; }
		}
		$this->content->template['was_del'] = $was_del;
	}

	// Daten speichern wenn alle Felder korrekt sind und die Änderung abgeschickt wurde
	if (!$this->is_locked($this->checked->mv_content_id)
		&& $this->insert_ok == true
		&& !empty($this->checked->mv_submit)
			&& $this->checked->mv_submit == $this->content->template['plugin']['mv']['aendern'])
	{
		// vergleichen alten Mitgliederdaten mit den neuen und speicher die Änderungen ab
		$this->save_temp_old_entry();
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/change_content_entry.php');
		// Sprachtabelle updaten
		$mv_content_id = $this->checked->mv_content_id;
		$update_insert = "update";
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/update_lang_search_row.php');
		$this->compare_content();
		// $_SESSION Werte für den multiselect wieder löschen
		$sql = sprintf("SELECT * FROM %s 
									WHERE mvcform_type = 'multiselect'",
									$this->cms->tbname['papoo_mvcform']);
		$result = $this->db->get_results($sql);
		if (!empty($result))
		{
			foreach($result as $row)
			{
				if (!empty($_SESSION["mvcform" . $row->mvcform_name . "_" . $row->mvcform_id]))
					unset($_SESSION["mvcform" . $row->mvcform_name . "_" . $row->mvcform_id]);
			}
		}
		if ($this->checked->fertig != 1)
		{
			// Front- und Backend Template nach erfolgreichen Ändern der Daten sind verschieden wie folgt:
			// alter $link_template = "template=".$this->checked->template doch ka(besser kz) ob sicherheitslücke;)
			if ($this->checked->template == "mv/templates/mv_edit_own_front.html") $link_template = "template=mv/templates/mv_edit_own_front.html";
			else $link_template = "template=mv/templates/userlist.html";
			$location_url = $_SERVER['PHP_SELF']
							. "?menuid="
							. $this->checked->menuid
							. "&fertig=1&"
							. $link_template
							. "&insert=ok&mv_id="
							. $this->checked->mv_id . "&mv_content_id="
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
// wenn einer urls selber zusammelbastelt bzw. versucht Sachen zu ändern die Ihn nix angehen, dann weisse Seite
else
{
	$location_url = $_SERVER['PHP_SELF']
					. "?menuid="
					. $this->checked->menuid
					. "&mv_id="
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
