<?php
// Vor Aufruf m�ssen folgende Variablen gesetzt werden:
// $felder_ok, $search_mv_id, $back_or_front
/**
* Felder f�r die Suchmaske aufbereiten (BE & FE)
* called by search_user.php, search_user_front.php
*/
//Metaeben durchgeben
$meta_id = $this->meta_gruppe;
if (!defined("admin")
	AND is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
if (empty($search_mv_id))
{
	// Alle Verwaltungen, die durchsuchbar sind
	$sql = sprintf("SELECT mv_id 
							FROM %s 
							WHERE mv_set_suchmaske = '1' 
							GROUP BY mv_id",
							
							$this->cms->tbname['papoo_mv']
					);
	$mv_ids = $this->db->get_results($sql, ARRAY_A);
}
else $mv_ids[] = array("mv_id" => $search_mv_id);
// Verwaltungsart
$sql = sprintf("SELECT mv_art 
						FROM %s 
						WHERE mv_id = '%d'",
						
						$this->cms->tbname['papoo_mv'],
						
						$this->db->escape($this->checked->mv_id)
				);
$verwaltungs_art = $this->db->get_var($sql);
if ($verwaltungs_art == "3") // Kalender
{
	$kalender_tag = date("j", time());
	$kalender_monat = date("n", time());
	$kalender_jahr = date("Y", time());
}
if (!empty($mv_ids))
{
	foreach($mv_ids as $mv_id)
	{
		// welche Felder gibts f�r die Suche in dieser MV?
		/*$sql = sprintf("SELECT mvcform_name,
								mvcform_id,
								mvcform_type,
								mvcform_size 
								FROM %s 
								WHERE mvcform_search%s = '1' 
								AND mvcform_form_id = '%d' 
								AND mvcform_meta_id = '%d' 
								GROUP BY mvcform_id 
								ORDER BY mvcform_order_id DESC ",
								
								$this->cms->tbname['papoo_mvcform'],
								
								$this->db->escape($back_or_front),
								$this->db->escape($mv_id['mv_id']),
								$this->db->escape($meta_id)
						);*/
		// chgd. by khmweb 08.09.10 Gruppen ber�cksichtigen bei der Ausgabe
		$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		$sql = sprintf("SELECT * FROM %s T2
		
								INNER JOIN %s T3 ON (T2.mvcform_group_id = T3.mvcform_group_lang_id) 
									AND (T2.mvcform_group_form_meta_id = T3.mvcform_group_lang_meta) 
									
								INNER JOIN %s T1 ON (T1.mvcform_meta_id = T2.mvcform_group_form_meta_id) 
									AND (T1.mvcform_group_id = T2.mvcform_group_id) 
									
								INNER JOIN %s T4 ON (T1.mvcform_id = T4.mvcform_lang_id) 
									AND (T1.mvcform_meta_id = T4.mvcform_lang_meta_id) 
									
								WHERE T1.mvcform_search%s = '1' 
									AND T1.mvcform_form_id = '%d' 
									AND T1.mvcform_meta_id = '%d' 
									AND T4.mvcform_lang_lang = '%d'
									AND T1.mvcform_normaler_user = '1'
									
								GROUP BY T1.mvcform_id, 
											T1.mvcform_group_id 
											
								ORDER BY T1.mvcform_group_id, 
											T1.mvcform_order_id DESC",
											
								$this->cms->tbname['papoo_mvcform_group'],
								
								$this->cms->tbname['papoo_mvcform_group_lang'],
								
								$this->cms->tbname['papoo_mvcform'],
								
								$this->cms->tbname['papoo_mvcform_lang'],
								
								$this->db->escape($back_or_front),
								$this->db->escape($mv_id['mv_id']),
								$this->db->escape($meta_id),
								$this->db->escape($lang)
						);
		$such_felder_temp[$mv_id['mv_id']] = $this->db->get_results($sql, ARRAY_A);
		$i = 0;
		if (!empty($such_felder_temp[$mv_id['mv_id']]))
		{
			foreach($such_felder_temp[$mv_id['mv_id']] as $feld)
			{
				// Darf das Feld auch von diesem Benutzer durchsucht werden? (Leserechte)
				if (array_search($feld['mvcform_id'], $felder_ok))
				{
					$such_felder[$mv_id['mv_id']][$i] = $such_felder_temp[$mv_id['mv_id']][$i];
					// Hole den Feldnamen f�r die Sprache aus der Datenbank
					// sind schon da... khmweb
					/*$sql = sprintf("SELECT mvcform_label 
											FROM %s 
											WHERE mvcform_lang_id = '%d' 
											AND mvcform_lang_lang = '%d' 
											AND mvcform_lang_meta_id = '%d'",
											
											$this->cms->tbname['papoo_mvcform_lang'],
											
											$this->db->escape($feld['mvcform_id']),
											$this->db->escape($this->cms->lang_id),
											$this->db->escape($meta_id)
									);*/
					#$result = $this->db->get_var($sql);
					#$such_felder[$mv_id['mv_id']][$i]['mvcform_name'] = $result;
					$such_felder[$mv_id['mv_id']][$i]['mvcform_name_id'] = $feld['mvcform_name'] . "_" . $feld['mvcform_id'];
					// Markierung f�r neue Feldtypen
					if ($feld['mvcform_type'] == "text" 
						|| $feld['mvcform_type'] == "textarea"
						|| $feld['mvcform_type'] == "textarea_tiny"
						|| $feld['mvcform_type'] == "email")
					{
						$platzi = $feld['mvcform_name'] . "_" . $feld['mvcform_id'];
						$such_felder[$mv_id['mv_id']][$i]['value'] = $this->checked->$platzi;
					}
					// wenn check Box dann H�kchen setzen, wenn es bei der vorhergehenden Suchabfrage gesetzt war
					#if ($feld['mvcform_type'] == "picture")
					#{
					#	$platzi = $feld['mvcform_name'] . "_" . $feld['mvcform_id'];
					#	if ($this->checked->$platzi == 1) $such_felder[$mv_id['mv_id']][$i]['check_set'] = 1;
					#}
					if ($feld['mvcform_type'] == "preisintervall")
							$such_felder[$mv_id['mv_id']][$i]['select_options'] = 
										'nobr:'
										. $this->get_feld_intervalle($feld['mvcform_id'],
																	$feld['mvcform_name'],
																	$this->checked->mv_id);
					// wenn es ein timestamp Feld ist
					if ($feld['mvcform_type'] == "timestamp")
					{
						// Select Feld f�r den Tag
						$select_tag = $this->content->template['plugin']['mv']['tag']
										. ' <select name="mvcform_tag_'
										. $feld['mvcform_name']
										. '_'
										. $feld['mvcform_id']
										. '" id="mvcform_tag_'
										. $feld['mvcform_name']
										. '_'
										. $feld['mvcform_id']
										. '" size="1" >';
						$select_tag .= '<option value="">TT</option>';
						$count = 1;
						while($count < 32)
						{
							$select_tag .= '<option value="' . sprintf("%02d", $count);
							// Wenn ausgew�hlt aus redo
							$tag = 'mvcform_tag_' . $feld['mvcform_name'] . '_' . $feld['mvcform_id'];

							if ($this->checked->$tag == "")
							{
								if ($kalender_tag == $count) $select_tag .= '" selected="selected">';
								else $select_tag .= '">';
							}
							elseif($this->checked->$tag == $count) $select_tag .= '" selected="selected">';
							else $select_tag .= '">';
							$select_tag .= sprintf("%02d", $count) . '</option>';
							$count++;
						}
						$select_tag .= '</select>';
						// Select Feld f�r den Monat
						$select_monat = $this->content->template['plugin']['mv']['monat']
										. ' <select name="mvcform_monat_'
										. $feld['mvcform_name']
										. '_'
										. $feld['mvcform_id']
										. '" id="mvcform_monat_'
										. $feld['mvcform_name']
										. '_'
										. $feld['mvcform_id']
										. '" size="1" >';
						$select_monat .= '<option value="">MM</option>';
						$count = 1;
						while($count < 13)
						{
							$select_monat .= '<option value="' . sprintf("%02d", $count);
							// Wenn ausgew�hlt aus redo
							$monat = 'mvcform_monat_' . $feld['mvcform_name'] . '_' . $feld['mvcform_id'];
							if ($this->checked->$monat == "")
							{
								if ($kalender_monat == $count) $select_monat .= '" selected="selected">';
								else $select_monat .= '">';
							}
							elseif($this->checked->$monat == $count) $select_monat .= '" selected="selected">';
							else $select_monat .= '">';
							$select_monat .= sprintf("%02d", $count) . '</option>';
							$count++;
						}
						$select_monat .= '</select>';
						// Select Feld f�r das Jahr
						$select_jahr = $this->content->template['plugin']['mv']['jahr']
										. ' <select name="mvcform_jahr_'
										. $feld['mvcform_name']
										. '_'
										. $feld['mvcform_id']
										. '" id="mvcform_jahr_'
										. $feld['mvcform_name']
										. '_'
										. $feld['mvcform_id']
										. '" size="1" >';
						$select_jahr .= '<option value="">JJJJ</option>';
						$count = 1910; // Startjahr
						while($count < 2037) // Endjahr
						{
							$select_jahr .= '<option value="' . sprintf("%04d", $count);
							// Wenn ausgew�hlt aus redo
							$jahr = 'mvcform_jahr_' . $feld['mvcform_name'] . '_' . $feld['mvcform_id'];

							if ($this->checked->$jahr == "")
							{
								if ($kalender_jahr == $count) $select_jahr .= '" selected="selected">';
								else $select_jahr .= '">';
							}
							elseif($this->checked->$jahr == $count) $select_jahr .= '" selected="selected">';
							else $select_jahr .= '">';
							$select_jahr .= sprintf("%04d", $count) . '</option>';
							$count++;
						}
						$select_jahr .= '</select>';
						$such_felder[$mv_id['mv_id']][$i]['select_options'] =
											'nobr:'
											. $select_tag
											. " "
											. $select_monat
											. " "
											. $select_jahr;
					}
					if ($feld['mvcform_type'] == "zeitintervall")
					{
						// Select Feld f�r den AnfangsTag
						$select_anfang_tag = $this->content->template['plugin']['mv']['tag']
											. ' <select name="anfang_tag_'
											. $feld['mvcform_name']
											. '_'
											. $feld['mvcform_id']
											. '" id="anfang_tag_'
											. $feld['mvcform_name']
											. '_'
											. $feld['mvcform_id']
											. '" size="1" >';
						$select_anfang_tag .= '<option value="">TT</option>';
						$count = 1;
						while($count < 32)
						{
							$select_anfang_tag .= '<option value="' . sprintf("%02d", $count);
							// Wenn ausgew�hlt aus redo
							$tag = 'anfang_tag_' . $feld['mvcform_name'] . '_' . $feld['mvcform_id'];
							if ($this->checked->$tag == "")
							{
								if ($kalender_tag == $count) $select_anfang_tag .= '" selected="selected">';
								else $select_anfang_tag .= '">';
							}
							elseif($this->checked->$tag == $count) $select_anfang_tag .= '" selected="selected">';
							else $select_anfang_tag .= '">';
							$select_anfang_tag .= sprintf("%02d", $count) . '</option>';
							$count++;
						}
						$select_anfang_tag .= '</select>';
						// Select Feld f�r den EndeTag
						$select_ende_tag = $this->content->template['plugin']['mv']['tag']
											. ' <select name="ende_tag_'
											. $feld['mvcform_name']
											. '_'
											. $feld['mvcform_id']
											. '" id="ende_tag_'
											. $feld['mvcform_name']
											. '_'
											. $feld['mvcform_id']
											. '" size="1" >';
						$select_ende_tag .= '<option value="">TT</option>';
						$count = 1;
						while($count < 32)
						{
							$select_ende_tag .= '<option value="' . sprintf("%02d", $count);
							// Wenn ausgew�hlt aus redo
							$tag = 'ende_tag_' . $feld['mvcform_name'] . '_' . $feld['mvcform_id'];
							if ($this->checked->$tag == "")
							{
								if ($kalender_tag == $count) $select_ende_tag .= '" selected="selected">';
								else $select_ende_tag .= '">';
							}
							elseif($this->checked->$tag == $count) $select_ende_tag .= '" selected="selected">';
							else $select_ende_tag .= '">';
							$select_ende_tag .= sprintf("%02d", $count) . '</option>';
							$count++;
						}
						$select_ende_tag .= '</select>';
						// Select Feld f�r den AnfangMonat
						$select_anfang_monat = $this->content->template['plugin']['mv']['monat']
												. ' <select name="anfang_monat_'
												. $feld['mvcform_name']
												. '_' . $feld['mvcform_id']
												. '" id="anfang_monat_'
												. $feld['mvcform_name']
												. '_'
												. $feld['mvcform_id']
												. '" size="1" >';
						$select_anfang_monat .= '<option value="">MM</option>';
						$count = 1;
						while($count < 13)
						{
							$select_anfang_monat .= '<option value="' . sprintf("%02d", $count);
							// Wenn ausgew�hlt aus redo
							$monat = 'anfang_monat_' . $feld['mvcform_name'] . '_' . $feld['mvcform_id'];

							if ($this->checked->$monat == "")
							{
								if ($kalender_monat == $count) $select_anfang_monat .= '" selected="selected">';
								else $select_anfang_monat .= '">';
							}
							elseif($this->checked->$monat == $count) $select_anfang_monat .= '" selected="selected">';
							else $select_anfang_monat .= '">';
							$select_anfang_monat .= sprintf("%02d", $count) . '</option>';
							$count++;
						}
						$select_anfang_monat .= '</select>';
						// Select Feld f�r den EndeMonat
						$select_ende_monat = $this->content->template['plugin']['mv']['monat']
												. ' <select name="ende_monat_'
												. $feld['mvcform_name']
												. '_'
												. $feld['mvcform_id']
												. '" id="ende_monat_'
												. $feld['mvcform_name']
												. '_'
												. $feld['mvcform_id']
												. '" size="1" >';
						$select_ende_monat .= '<option value="">MM</option>';
						$count = 1;
						while($count < 13)
						{
							$select_ende_monat .= '<option value="' . sprintf("%02d", $count);
							// Wenn ausgew�hlt aus redo
							$monat = 'ende_monat_' . $feld['mvcform_name'] . '_' . $feld['mvcform_id'];
							if ($this->checked->$monat == "")
							{
								if ($kalender_monat == $count) $select_ende_monat .= '" selected="selected">';
								else $select_ende_monat .= '">';
							}
							elseif($this->checked->$monat == $count) $select_ende_monat .= '" selected="selected">';
							else $select_ende_monat .= '">';
							$select_ende_monat .= sprintf("%02d", $count) . '</option>';
							$count++;
						}
						$select_ende_monat .= '</select>';
						// Select Feld f�r das AnfangJahr
						$select_anfang_jahr = $this->content->template['plugin']['mv']['jahr']
												. ' <select name="anfang_jahr_'
												. $feld['mvcform_name']
												. '_'
												. $feld['mvcform_id']
												. '" id="anfang_jahr_'
												. $feld['mvcform_name']
												. '_' . $feld['mvcform_id']
												. '" size="1" >';
						$select_anfang_jahr .= '<option value="">JJJJ</option>';
						$count = 1910;
						while($count < 2037)
						{
							$select_anfang_jahr .= '<option value="' . sprintf("%04d", $count);
							// Wenn ausgew�hlt aus redo
							$jahr = 'anfang_jahr_' . $feld['mvcform_name'] . '_' . $feld['mvcform_id'];
							if ($this->checked->$jahr == "")
							{
								if ($kalender_jahr == $count) $select_anfang_jahr .= '" selected="selected">';
								else $select_anfang_jahr .= '">';
							}
							elseif($this->checked->$jahr == $count) $select_anfang_jahr .= '" selected="selected">';
							else $select_anfang_jahr .= '">';
							$select_anfang_jahr .= sprintf("%04d", $count) . '</option>';
							$count++;
						}
						$select_anfang_jahr .= '</select>';
						// Select Feld f�r das EndeJahr
						$select_ende_jahr = $this->content->template['plugin']['mv']['jahr']
											. ' <select name="ende_jahr_'
											. $feld['mvcform_name']
											. '_'
											. $feld['mvcform_id']
											. '" id="ende_jahr_'
											. $feld['mvcform_name']
											. '_'
											. $feld['mvcform_id']
											. '" size="1" >';
						$select_ende_jahr .= '<option value="">JJJJ</option>';
						$count = 1910;
						while($count < 2037)
						{
							$select_ende_jahr .= '<option value="' . sprintf("%04d", $count);
							// Wenn ausgew�hlt aus redo
							$jahr = 'ende_jahr_' . $feld['mvcform_name'] . '_' . $feld['mvcform_id'];
							if ($this->checked->$jahr == "")
							{
								// displacement: damit das Ende hochgesetzt wird f�r die Suche
								if (($kalender_jahr + $this->jahr_ende_displacement) == $count) $select_ende_jahr .= '" selected="selected">';
								#if ($kalender_jahr == $count) $select_ende_jahr .= '" selected="selected">';
								else $select_ende_jahr .= '">';
							}
							elseif($this->checked->$jahr == $count) $select_ende_jahr .= '" selected="selected">';
							else $select_ende_jahr .= '">';
							$select_ende_jahr .= sprintf("%04d", $count) . '</option>';
							$count++;
						}
						$select_ende_jahr .= '</select>';
						$such_felder[$mv_id['mv_id']][$i]['select_options'] = 'nobr:<p> ' . $select_anfang_tag . " " . $select_anfang_monat . " " . $select_anfang_jahr
																				. $select_ende_tag . " " . $select_ende_monat . " " . $select_ende_jahr . '</p>';
					}
					// wenn select, radio oder multiselect, dann Selectfeld zusammenbauen
					if ($feld['mvcform_type'] == "radio"
						|| $feld['mvcform_type'] == "select"
						|| $feld['mvcform_type'] == "multiselect")
					{
						$sql = sprintf("SELECT * FROM %s 
													WHERE lang_id = '%d' 
													AND lookup_id <> 0 
													ORDER BY order_id",
													
													$this->cms->tbname['papoo_mv']
													. "_content_"
													. $this->db->escape($mv_id['mv_id'])
													. "_lang_"
													. $this->db->escape($feld['mvcform_id']),
													
													$this->db->escape($this->cms->lang_back_content_id)
										);
						$result = $this->db->get_results($sql);
						if ($this->meta_include_label == 1) 
							$such_felder[$mv_id['mv_id']][$i]['select_options'] = 
												'nobr:<option value="">'
												. $such_felder[$mv_id['mv_id']][$i]['mvcform_name']
												. '</option>';
						else $such_felder[$mv_id['mv_id']][$i]['select_options'] = 
												'nobr:<option value="">'
												. $this->content->template['plugin']['mv']['alle']
												. '</option>';
						if (!empty($result))
						{
							foreach($result as $row)
							{
								if ($row->lookup_id != "0" 
									&& $row->content != "")
								{
									$row->content = trim($row->content);
									$selecter = "";
									$platzi = $feld['mvcform_name'] . "_" . $feld['mvcform_id'];
									if ($this->checked->$platzi == $row->lookup_id) $selecter = ' selected="selected" ';
									$such_felder[$mv_id['mv_id']][$i]['select_options'] .= 
												"\n\t"
												. '<option value="'
												. $row->lookup_id
												. '" '
												. $selecter . '>'
												. $row->content
												. '</option>';
								}
							}
						}
					}
					if (($feld['mvcform_type'] == "check" AND $this->checkbox_as_selectbox)
						|| $feld['mvcform_type'] == "picture"
						|| $feld['mvcform_type'] == "galerie"
						|| $feld['mvcform_type'] == "file")
					{
						$platzi = $feld['mvcform_name'] . "_" . $feld['mvcform_id'];
						$ja = $this->content->template['plugin_mv_ja'];
						$nein = $this->content->template['plugin_mv_nein'];
						$auswahl = $this->content->template['plugin_mv_auswahl'];
						$such_felder[$mv_id['mv_id']][$i]['select_options'] =
												'nobr:<option value="">'
												. $auswahl
												. '</option><option value="1">'
												. $ja
												. '</option><option value="0">'
												. $nein . '</option>';
						if ($this->checked->$platzi == 1)
							$such_felder[$mv_id['mv_id']][$i]['select_options'] = 
												'nobr:<option value="">'
												. $auswahl
												. '</option><option selected="selected" value="1">'
												. $ja
												. '</option><option value="0">'
												. $nein
												. '</option>';
						if ($this->checked->$platzi == "0")
							$such_felder[$mv_id['mv_id']][$i]['select_options'] =
												'nobr:<option value="">'
												. $auswahl
												. '</option><option value="1">'
												. $ja
												. '</option><option selected="selected" value="0">'
												. $nein
												. '</option>';
					}
					elseif ($feld['mvcform_type'] == "check" AND !$this->checkbox_as_selectbox)
					{
						$platzi = $feld['mvcform_name'] . "_" . $feld['mvcform_id'];
						// Holt den Eintrag aus der Lang Tabelle
						$lang = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
						$sql = sprintf("SELECT content FROM %s
														WHERE lang_id = '%d' 
														LIMIT 1",
														
														$this->cms->tbname['papoo_mv']
														. "_content_"
														. $this->db->escape($feld['mvcform_form_id'])
														. "_lang_"
														. $this->db->escape($feld['mvcform_lang_id']),
														
														$this->db->escape($lang)
										);
						$ja = $this->db->get_var($sql);
						$checked = $this->checked->$platzi ? 'checked="checked"' : "";
						$such_felder[$mv_id['mv_id']][$i]['select_options'] =
												'nobr:<input type="checkbox" value="'
												. $ja
												. '" name="'
												. $platzi
												. '" id="'
												. $platzi
												. '" '
												. $checked
												. ' />';
						$such_felder[$mv_id['mv_id']][$i]['mvcform_type'] = "checkbox_type2";
					}
					/**
					* $such_felder[$mv_id['mv_id']][$i]['select_options'] = 'nobr:<option value="">'.$such_felder[$mv_id['mv_id']][$i]['mvcform_name'].'</option>';
					* */
					//Normale Input Felder dann Gr��e �bergeben
					else $such_felder[$mv_id['mv_id']][$i]['mvcform_size'] = $feld['mvcform_size']; //mvcform_size
				}
				$i++;
			}
		}
		// die mv_id noch mitgeben, damit die suchmaske auch weiss wo sie ist:)
		$such_felder[$mv_id['mv_id']]['mv_id'] = $mv_id['mv_id'];
	}
}
if (empty($search_mv_id)) $this->content->template['such_felder'] = $such_felder;
else $this->content->template['such_felder'] = $such_felder[$search_mv_id];
?>