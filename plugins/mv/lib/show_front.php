<?php
/**
*	Mitgliedsdaten im Frontend anzeigen
*/
if (!headers_sent($f, $l) && $this->checked->mv_id)
{
	if ($this->checked->download) require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/mv_dl_protected.php');
	$meta_id = $this->meta_gruppe;
	if (!defined("admin")
		AND is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
	// holt das Template f�rs Frontend aus der Datenbank
	$vw_one_item_key = array();
	$this->content->template['mv_template_all'] = "nobr:";
	$detail_id = $this->get_detail_id();
	$extern_meta = $this->checked->extern_meta == "x" ? 1 : $this->checked->extern_meta;
	$sql = sprintf("SELECT template_content_one FROM %s
												WHERE lang_id = '%d' 
												AND detail_id = '%d'
												AND meta_id = '%d'
												LIMIT 1",
												
												$this->cms->tbname['papoo_mv']
												. "_template_"
												. $this->db->escape($this->checked->mv_id),
												
												$this->db->escape($this->cms->lang_id),
												$this->db->escape($detail_id),
												$this->db->escape($extern_meta)
					);
	$this->content->template['mv_template_all'] .= $this->db->get_var($sql);
	$this->content->template['mv_content_id'] = $this->checked->mv_content_id;
	$where_clause1 = $this->get_sichtbar_feld_fuer_dzvhae();
	if ($where_clause1) $where_clause1 = " AND " . $where_clause1;

	// Daten f�r das Mitglied aus der Datenbank holen
	$sql = sprintf("SELECT * FROM %s, %s 
								WHERE mv_content_id = '%s' 
								%s
								AND mv_content_sperre <> '1'
								AND mv_meta_lp_user_id = mv_content_id
								AND mv_meta_lp_meta_id = '%d'
								AND mv_meta_lp_mv_id = '%d'
								LIMIT 1",
								
								$this->cms->tbname['papoo_mv']
								. "_content_"
								. $this->db->escape($this->checked->mv_id)
								. "_search_"
								. $this->db->escape($this->cms->lang_id),
								
								$this->cms->tbname['papoo_mv_meta_lp'],
								
								$this->db->escape($this->checked->mv_content_id),
								$where_clause1,
								$this->db->escape($meta_id),
								$this->db->escape($this->checked->mv_id)
					);
	$result = $this->db->get_results($sql);
	$this->finde_die_art_der_verwaltung_heraus();
	// 1 = Standard, 2 = MV, 3 = Kalender
	// mv_show_front.html:
	// Deaktiviert, denn es hat zur Folge, dass f�rs FE gesperrte via mv_content_id=x �ber die URL dennoch angezeigt werden k�nnen.
	// Wenn es derjenige ist, der eingetragen hat, kann er via mv_show_own_front.html seine anzeigen lassen
	// $this->userid ist, ohne eingeloggt zu sein, immer 11. Dies steht auch unter mv_content_owner... korrekt?
	#if ($this->mv_art > 1)
	#{
		//Wenn leer, dann schauen, ob es evtl. derjenige ist, der eingetragen hat
		if (empty($result))
		{
		
	//hinzugef�gt wegen: s. o.
			$this->content->template['mv_template_all'] = "";
		
			$sql = sprintf("SELECT * FROM %s, %s 
										WHERE mv_content_id = '%s' 
										AND mv_content_owner = '%d'
										AND mv_meta_lp_user_id = mv_content_id
										AND mv_meta_lp_meta_id = '%d'
										AND mv_meta_lp_mv_id = '%d'
										LIMIT 1",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($this->checked->mv_id)
										. "_search_"
										. $this->db->escape($this->cms->lang_id),
										
										$this->cms->tbname['papoo_mv_meta_lp'],
										
										$this->db->escape($this->checked->mv_content_id),
										$this->user->userid,
										$this->db->escape($meta_id),
										$this->db->escape($this->checked->mv_id)
							);
			#$result = $this->db->get_results($sql);
		}
	#}
	$i = 0;
	if (!empty($result))
	{
		$spalten_namen = array();
		$lang_id = $this->cms->lang_id;
		$spalten_namen = $this->get_spalten_namen();

		$fieldTypes = array_combine(
			array_map(function (array $field) { return "{$field['mvcform_name']}_{$field['mvcform_id']}"; }, $spalten_namen),
			array_map(function (array $field) { return "{$field['mvcform_type']}"; }, $spalten_namen)
		);

		$flexId = (int)$this->checked->mv_id;
		$contentId = (int)$this->checked->mv_content_id;

		$this->makeLanguageEntries($flexId, $contentId);

		// smarty templates include in ausgabe-formatierung ermoeglichen
		$GLOBALS["smarty"]->assign(array_merge($this->content->template, [
			"currentFlexEntry" => (array)$result[0]
		]));
		$this->content->template["mv_template_all"] = preg_replace_callback('/\{include file="([^"]+)"\}/',
			function ($match) {
				$template = PAPOO_ABS_PFAD."/styles/{$GLOBALS["cms"]->style_dir}/templates/".$match[1];
				if (file_exists($template)) {
					return $GLOBALS["smarty"]->fetch($template);
				}
				else {
					return "{Error: Template '$template' not found.}";
				}
			}, $this->content->template["mv_template_all"]
		);


		$currentFlexUserListMenuInternaId = (int)$this->db->get_var("SELECT menuid ".
			"FROM {$this->cms->tbname["papoo_menuint"]} ".
			"WHERE menulink = 'plugin:mv/templates/userlist.html&mv_id=$flexId'"
		);

		$template_type = 'replace';
		if (strpos($this->content->template['mv_template_all'], 'nobr:##SMARTY##') === 0) {
			$template_type = 'smarty';
			$smarty_file = PAPOO_ABS_PFAD."/templates_c/mv_template_all_".$this->checked->mv_id."_".$this->cms->lang_id.".html";
			file_put_contents($smarty_file.'.tmp', substr($this->content->template['mv_template_all'], strlen('nobr:##SMARTY##')), LOCK_EX);
			rename($smarty_file.'.tmp', $smarty_file);
		}

		// wenn es die eigennen Mitgliederdaten sind, dann Editfunktion(Link) anbieten
        if (($result[0]->mv_content_userid == $this->user->userid ||
             $result[0]->mv_content_owner == $this->user->userid) ||
            $this->user_has_write_rights_for_current_flex_and_backend_access()
        ) {
			if ($this->user->userid != 11)
			{
				///interna/plugin.php?menuid=1217&template=mv/templates/change_user.html&mv_id=2&mv_content_id=6&userid=17

				// $this->content->template['is_own_acc_link'] = ""
				// 												. "/interna/plugin.php?menuid=$currentFlexUserListMenuInternaId"
				// 												//. $this->checked->menuid
				// 												. "&template=mv/templates/change_user.html&mv_id="
				// 												. $this->checked->mv_id
				// 												. "&mv_content_id="
				// 												. $result[0]->mv_content_id;
				$this->content->template['is_own_acc_link'] = $_SERVER['PHP_SELF']
					. "?menuid="
					. $this->checked->menuid
					. "&template=mv/templates/mv_edit_front.html&mv_id="
					. $this->checked->mv_id
					. "&mv_content_id="
					. $result[0]->mv_content_id;
				$this->content->template['is_own_acc'] = "1";
			}
		}
		// weise dem Formfeld den Wert aus der Datenbank zu
		//Felder Rechte rausholen
		$this->get_field_rights();
		//Spezialrechte rausholen
		$this->get_field_rights_special();

		$replacements = ['id' => $row->mv_content_id, 'mv_id' => $this->checked->mv_id];

		foreach($spalten_namen as $form_feld_name)
		{
			$value_first = "";
			$value = "";
			// changed khmweb 13.8.10, check ob diese Felder gebraucht werden FIX!!
			$name = $form_feld_name['mvcform_name'];
			$id = $form_feld_name['mvcform_id'];
			#list($name, $id) = explode("_", $form_feld_name); // explode auf array geht nicht
			$die_aktuelle_id = $form_feld_name['mvcform_id'];
			if (is_numeric($die_aktuelle_id))
			{
				if (!in_array($die_aktuelle_id, $this->felder_rechte_aktuelle_gruppe))
				{
					$value = "";
					$this->content->template['mv_template_all'] .= "\n\t";
					$this->content->template['mv_template_all'] =
						preg_replace('/#' . $form_feld_name['mvcform_name'] . '_' . $form_feld_name['mvcform_id'] . '#/', "", $this->content->template['mv_template_all']);
					//Keine Leserechte - dann abbrechen
					continue;
				}
			}
			$dername = $form_feld_name['mvcform_name'] . "_" . $form_feld_name['mvcform_id'];
			//Testen, ob wg. der speziellen Gruppenrechte Schreibrechte bestehen
			foreach($this->felder_rechte_aktuelle_gruppe_special as $rechtspezial)
			{
				if ($rechtspezial['field_id'] == $die_aktuelle_id)
				{
					if ($rechtspezial['field_value'] == $result[0]->$dername)
					{
						$this->content->template['is_own_acc_link'] = $_SERVER['PHP_SELF']
																		. "?menuid="
																		. $this->checked->menuid
																		. "&template=mv/templates/mv_edit_front.html&mv_id="
																		. $this->checked->mv_id
																		. "&mv_content_id="
																		. $result[0]->mv_content_id;
						$this->content->template['is_own_acc'] = "1";
					}
				}
			}
			//Id des Feldes �bergeben, s. o., ist schon erledigt
			#$id = $form_feld_name['mvcform_id'];
			//Name des Feldes �bergeben
			$form_feld_name = $name = $form_feld_name['mvcform_name'];
			$feldname_feldid = $form_feld_name . "_" . $id;
	
			$sql = sprintf("SELECT mvcform_normaler_user
									FROM %s
									WHERE mvcform_id = '%d' 
									AND mvcform_meta_id = '%d'
									LIMIT 1",
									$this->cms->tbname['papoo_mvcform'],
									$this->db->escape($id),
									$this->db->escape($this->meta_gruppe)
							);
			$result_user = $this->db->get_var($sql);
			if ($result_user == "1")
			{
				$sql = sprintf("SELECT * FROM %s
											WHERE mvcform_name = '%s' 
											AND mvcform_id = '%s' 
											AND mvcform_meta_id = '%d'
											LIMIT 1",
											$this->cms->tbname['papoo_mvcform'],
											$this->db->escape($name),
											$this->db->escape($id),
											$this->db->escape($this->meta_gruppe)
								);
				$result2 = $this->db->get_results($sql);
				// Holt den Feldnamen f�r die jeweilige Sprache aus der Tabelle
				$sql = sprintf("SELECT mvcform_label
										FROM %s
										WHERE mvcform_lang_id = '%d' 
										AND mvcform_lang_lang = '%d' 
										AND mvcform_lang_meta_id = '%d'
										LIMIT 1",
										$this->cms->tbname['papoo_mvcform_lang'],
										$this->db->escape($id),
										$this->db->escape($lang_id),
										$this->db->escape($this->meta_gruppe)
								);
				$name_label = $this->db->get_var($sql);
				// Markierung f�r neue Feldtypen
				if ($result2[0]->mvcform_type == "timestamp")
				{
					list($tag, $monat, $jahr) = $this->get_day_month_year($result[0]->$feldname_feldid);
					if (!empty($tag) 
						&& !empty($monat) 
						&& !empty($jahr)) $value = $tag . "." . $monat . "." . $jahr;
					$dat_array[$name_label] = $value;
				}
				elseif($result2[0]->mvcform_type == "zeitintervall")
				{
					list($anfang_datum, $ende_datum) = explode(",", $result[0]->$feldname_feldid);
					list($tag, $monat, $jahr) = $this->get_day_month_year($anfang_datum);
					if (!empty($tag)
						&& !empty($monat)
						&& !empty($jahr)) $value = $tag . "." . $monat . "." . $jahr;
					list($tag, $monat, $jahr) = $this->get_day_month_year($ende_datum);
					if (!empty($tag)
						&& !empty($monat)
						&& !empty($jahr)) $value .= $this->content->template['plugin']['mv']['bis']
													. $tag
													. "."
													. $monat
													. "."
													. $jahr;
					$dat_array[$name_label] = $value;
				}
				elseif($result2[0]->mvcform_type == "password")
				{
					$value = "*******";
					$dat_array[$name_label] = "*******";
				}
				//do_pfadeanpassen
				elseif($result2[0]->mvcform_type == "textarea_tiny") {
					$value = $this->diverse->do_pfadeanpassen($result[0]->$feldname_feldid);
				}
				elseif($result2[0]->mvcform_type == "galerie")
				{
					$value = $this->make_galerie_images($result[0]->$feldname_feldid);
					$dat_array[$name_label] = $value;
				}
				elseif($result2[0]->mvcform_type == "picture"
						&& !empty($result[0]->$feldname_feldid))
				{
					$imagePathname = rtrim(PAPOO_ABS_PFAD, '/') . '/images/' . $result[0]->$feldname_feldid;
					if (is_file($imagePathname)) {
						// . $this->checked->mv_content_id . "_" entfernt
						$temp_infos = getimagesize($imagePathname);
						// Bild-Breite und -H�he setzen
						/*$faktor_breite = ($temp_infos[0] / 180);
						$faktor_hoehe = ($temp_infos[1] / 240);
						$faktor = max($faktor_breite, $faktor_hoehe);
						if($faktor != 0)	$this->image_infos['breite'] = round($temp_infos[0]/$faktor);
						if($faktor != 0)	$this->image_infos['hoehe'] = round($temp_infos[1]/$faktor);*/
						$this->image_infos['breite'] = $temp_infos[0];
						$this->image_infos['hoehe'] = $temp_infos[1];

						$imageDimensionsHtml = $temp_infos[3] ?? '';
						$srcset = [];

						// Look for a field of type "picture" that has the same name and the "2x" suffix
						$image2xResolutionFieldId = array_reduce(
							array_keys(get_object_vars($result[0])),
							function ($carry, $item) use ($name, $fieldTypes) {
								return $carry ?? ($fieldTypes[$item] == 'picture' && explode('_', $item, 2)[0] == $name.'2x' ? $item : $carry);
							},
							null
						);

						if ($image2xResolutionFieldId && $result[0]->$image2xResolutionFieldId
							&& is_file($image2xResolutionPathname = dirname($imagePathname) . '/' . $result[0]->$image2xResolutionFieldId)
						) {
							$srcset[] = $this->image_core->pfad_images_web . $result[0]->$image2xResolutionFieldId . ' 2x';
						}

						// Prepend original image if multiple versions exist
						if ($srcset) {
							array_unshift($srcset, $this->image_core->pfad_images_web . $result[0]->$feldname_feldid);
						}

						$rel_lightbox_img = '';
						$value = "";
						if ($this->mv_show_lightbox_single == 1)
						{
							$rel_lightbox_img = 'rel="lightbox"';
							//. $this->checked->mv_content_id. "_" entfernt
							$value = '<a href="'
										. $this->image_core->pfad_images_web
										. $result[0]->$feldname_feldid
										. '" '
										. $rel_lightbox_img
										. '>';
						}
						$value .= '<img '
							. ($srcset ? 'srcset="' . implode(', ', array_map('htmlspecialchars', $srcset)) . '" ' : '')
							."src=\"{$this->image_core->pfad_images_web}{$result[0]->$feldname_feldid}\" "
							. ($imageDimensionsHtml ? $imageDimensionsHtml . ' ' : '')
							. 'title="" alt="" class="imagesize"/>';
						if ($this->mv_show_lightbox_single == 1) $value .= '</a>';
						$value .= " ";
						$dat_array[$name_label] = '<img src="'
													. $this->image_core->pfad_images_web
													. $result[0]->$feldname_feldid
													. '" title="" alt=""  />';
					}
				}
				elseif($result2[0]->mvcform_type == "file")
				{
					if (!empty($result[0]->$feldname_feldid))
					{
						if ($this->download_protected)
							$value = "plugin.php"
									. "?menuid="
									. $this->checked->menuid
									. "&template=mv/templates/mv_show_front.html&download=1"
									. '&field_id='
									. $id
									. '&mv_id='
									. $this->checked->mv_id
									. '&mv_content_id='
									. $this->checked->mv_content_id;
						else $value = $result[0]->$feldname_feldid;
					}
				}
				elseif($result2[0]->mvcform_type == "multiselect")
				{
					if ($template_type == 'smarty') {
						$value = $this->get_multiselect_werte_array($id, $name, $this->db->escape($this->checked->mv_content_id));
					} else {
						$value = $this->get_multiselect_werte_front($id, $name, $this->db->escape($this->checked->mv_content_id));
						if ($this->show_multiselect_values_no_br_single_view) $value = preg_replace("/<br \/>/", "", $value);
						else $value = preg_replace("/,/", "", $value);
					}
					$dat_array[$name_label] = $value;
				}
				elseif($result2[0]->mvcform_type == "select"
					|| $result2[0]->mvcform_type == "radio")
				{
					$value = $this->get_lp_wert_front($id, $name, $this->db->escape($this->checked->mv_content_id));
					$dat_array[$name_label] = $value;
				}
				elseif($result2[0]->mvcform_type == "pre_select")
				{
					$value = $this->get_lp_wert_front($id, $name, $this->db->escape($this->checked->mv_content_id));
					$val_pre_array = explode("+++", $value);
					if (!empty($val_pre_array['0'])) $value = $val_pre_array['0'];
					$dat_array[$name_label] = $value;
				}
				elseif($result2[0]->mvcform_type == "check")
				{
					// added khmweb
					$value = $this->get_lp_wert($id, $name, $this->checked->mv_content_id);
					// FEHLER: value kann x-beliebig sein, nicht nur 1. khmweb
					$value = ($value == "0" or empty($value)) ? $this->content->template['plugin']['mv']['nein'] : $value;
					#if ($result[0]->$feldname_feldid == 1) $value = $this->content->template['plugin']['mv']['ja'];
					#else $value = $this->content->template['plugin']['mv']['nein'];
					$dat_array[$name_label] = $value;
				}
				/*elseif($result2[0]->mvcform_type == "email")
				{
				$value = $result[0]->$feldname_feldid;
				$dat_array[$name_label] = $value;
				}	*/
				elseif($result2[0]->mvcform_type == "preisintervall")
				{
					// holt die W�hrung aus der Datenbank
					$feld_waehrung = $this->get_feld_waehrung($id, $this->checked->mv_id);
					#$value = $result[0]->$feldname_feldid;
					$value = $result[0]->$feldname_feldid  . " " . $feld_waehrung; // chgd. khmweb 1.12.09 fehlende W�hrung mit Abstand hinzugef�gt
					$dat_array[$name_label] = $value . $feld_waehrung;
				}
				elseif ($result2[0]->mvcform_type == 'textarea') {
					$value = $result[0]->$feldname_feldid;
					
					$value = str_replace("\r\n", "\n", $value);
					// Ersetze Zeilenumbr�che durch brs wenn gew�nscht.
					if ($this->show_textarea_linebreaks_single_view)
						$value = str_replace("\n", '<br />', rtrim($value));
					
					$dat_array[$name_label] = $value;
				}
				elseif ($result2[0]->mvcform_type == 'flex_verbindung') {
					//print_r($result[0]);
					//Wir brauchen die FlexID und die FeldnameID um die Daten zu bekommen

					$idVAr = explode("_",$feldname_feldid);
					$feldID = end($idVAr);
					$sqlGetIds = sprintf("SELECT * FROM %s WHERE mvcform_id='%d'",DB_PRAEFIX."papoo_mvcform",$feldID);
					$resultFeldIDs = $this->db->get_results($sqlGetIds,ARRAY_A);

					$flexVerbindung['ID'] = $resultFeldIDs['0']['mvcform_flex_id'];
					$flexVerbindung['FeldID'] = $resultFeldIDs['0']['mvcform_flex_feld_id'];;
					$flexVerbindung['ZeileID'] = $result[0]->$feldname_feldid;

					//Jetzt noch den Namen des verlinkten Feldes...
					$sqlGetIds = sprintf("SELECT * FROM %s WHERE mvcform_id='%d'",DB_PRAEFIX."papoo_mvcform",$flexVerbindung['FeldID']);
					$resultFeldDats = $this->db->get_results($sqlGetIds,ARRAY_A);
					#print_r($sqlGetIds);
					#print_r($resultFeldDats);
					$flexVerbindung['ExternFeldID'] = $resultFeldDats['0']['mvcform_name']."_".$resultFeldIDs['0']['mvcform_flex_feld_id'];;

					//print_r($flexVerbindung);
					$zeilen = json_decode($flexVerbindung['ZeileID'],true);
					$flexVerbindungsLink = "<ul class='flexVerbLinks'>";
					//print_r($zeilen);
					if (is_array($zeilen))
					{
						foreach ($zeilen as $zk=>$zv)
						{
							//nur wenn es auch aktiv ist...
							if ($zv ==1)
							{
								//Jetzt den Wert des Feldes rausholen
								$sqlGetData = sprintf("SELECT %s FROM %s WHERE mv_content_id='%d'",
									$flexVerbindung['ExternFeldID'] ,
									DB_PRAEFIX."papoo_mv_content_".$flexVerbindung['ID']."_search_1",
									$zk)
								;
								$resultFlexVerbData = $this->db->get_results($sqlGetData,ARRAY_A);
								//print_r($sqlGetData);
								$valDat = ($resultFlexVerbData['0'][$flexVerbindung['ExternFeldID']]);
								//Mit Hilder der IDs den Wert rausholen
								// Aus flex x Feld y Zeile z

								//Und verlinken.
								$flexVerbindungsLink.= '<li><a href="/plugin.php?menuid='.$this->checked->menuid.'&template=mv/templates/mv_show_front.html&mv_id='.$flexVerbindung['ID'].'&extern_meta=x&mv_content_id='.$zk.'&getlang=de">'.$valDat.'</a></li>';

							}

						}
					}
					$value = $flexVerbindungsLink."</ul>";
					//print_r($value);
					//exit();
				}
				else
				{
					$value = $result[0]->$feldname_feldid;
					
					//$value = $this->get_lp_wert_front($id,$name,$this->db->escape($this->checked->mv_content_id));
					$dat_array[$name_label] = $value;
				}
				// dzvhae Sonderfall
				if ($this->dzvhae_system_id
					&& $name . '_' . $id == $this->dzvhae_feld_flex_systemid)
				{
					$sql = sprintf("SELECT mv_content_id,
											%s,
											%s,
											%s,
											%s,
											%s,
											%s,
											%s,
											%s,
											email_3
											FROM %s 
											WHERE %s = '%d'",
											$this->dzvhae_feld_flex_vorname,
											$this->dzvhae_feld_flex_nachname,
											$this->dzvhae_feld_flex_plz,
											$this->dzvhae_feld_flex_ort,
											$this->dzvhae_feld_flex_strasse,
											$this->dzvhae_feld_flex_telvorwahl,
											$this->dzvhae_feld_flex_telnummer,
											$this->dzvhae_feld_flex_fax,
											
											$this->cms->tbname['papoo_mv']
											. "_content_1"
											. "_search_"
											. $this->cms->lang_id,
											
											$this->dzvhae_feld_flex_systemid,
											$this->db->escape($value)
									);
					$mv_daten = $this->db->get_results($sql, ARRAY_A);
					$value = "<br /><strong>" . $mv_daten[0][$this->dzvhae_feld_flex_vorname] . ' ' . $mv_daten[0][$this->dzvhae_feld_flex_nachname] . "</strong><br />";
					$value .= $mv_daten[0][$this->dzvhae_feld_flex_strasse] . "<br />";
					$value .= $mv_daten[0][$this->dzvhae_feld_flex_plz] . $mv_daten[0][$this->dzvhae_feld_flex_ort] . "<br /><br />";
					$value .= "T: " . $mv_daten[0][$this->dzvhae_feld_flex_telvorwahl] . $mv_daten[0][$this->dzvhae_feld_flex_telnummer] . "<br />";
					$value .= "F: " . $mv_daten[0][$this->dzvhae_feld_flex_fax] . "<br />";
					$value .= $mv_daten[0]['email_3'] . "<br />";
				}
				$vw_one_item_key[$form_feld_name . "_" . $id] = $value;
				// F�rs Frontend
				if ($template_type != 'smarty') {
					if (empty($value_first))
					{
						if ($value == "") $this->content->template['mv_template_all'] =
									str_replace('#' . $name . '_' . $id . '#', "", $this->content->template['mv_template_all']);
						else $this->content->template['mv_template_all'] = preg_replace('/#' . $name . '_' . $id . '#/', $value, $this->content->template['mv_template_all']);
					}
					else
					{
						$this->content->template['mv_template_all'] = 
									preg_replace('/#first_' . $name . '_' . $id . '#/', $value_first, $this->content->template['mv_template_all']);
						$this->content->template['mv_template_all'] = preg_replace('/#' . $name . '_' . $id . '#/', $value, $this->content->template['mv_template_all']);
					}
					$this->content->template['mv_template_all'] = preg_replace('/#user_owner#/', $result[0]->mv_content_owner, $this->content->template['mv_template_all']);
					$this->content->template['mv_template_all'] = preg_replace('/#user_id#/', $result[0]->mv_content_userid, $this->content->template['mv_template_all']);
					if (is_numeric($this->checked->mv_content_id))
					{
						$this->content->template['mv_template_all'] =
									preg_replace('/#ID#/', $this->checked->mv_content_id, $this->content->template['mv_template_all']);
					}
					if (is_numeric($this->checked->mv_id))
					{
						$this->content->template['mv_template_all'] = preg_replace('/#MVID#/', $this->checked->mv_id, $this->content->template['mv_template_all']);
					}
				} else {
					$replacements[$name.'_'.$id] = $value;
				}
				$this->content->template['liste'][$i] = $value;
				$i++;
			}
		}

		if ($template_type == 'smarty') {
			$GLOBALS["smarty"]->assign('nl', "\n");
			$GLOBALS["smarty"]->assign('menuid', $this->checked->menuid);
			$GLOBALS["smarty"]->assign('mv_item', $replacements);
			$this->content->template['mv_template_all'] = 'nobr:'.$GLOBALS["smarty"]->fetch('file:'.$smarty_file);
			$GLOBALS["smarty"]->assign('mv_item', null);
		}

		$this->content->template['vw_one_item'] = $dat_array;
		$this->content->template['vw_one_item_key'] = $vw_one_item_key;
	}
	$this->make_lightbox();
}
