<?php
/**
* Feld bearbeiten
*
* called by make_input_entry.php
*/
// damit erkannt wird, dass es change ist und somit f�r select etc. felder keine textarea mehr dargestellt wird
// sondern inputfelder
$this->content->template['firsttime'] = 1;
// Soll gel�scht werden?
if (!empty($this->checked->del_field))
{
	$this->content->template['mvcform_id'] = $this->checked->mvcform_id;
	$this->content->template['mv_id'] = $this->checked->mv_id;
	$this->content->template['fragedel'] = "ok";
	$this->content->template['mvcform_type'] = $this->checked->mvcform_type;
	return;
}
// Feld l�schen
if ((!empty($this->checked->del_field_echt))) $this->del_field(); // s. mv_php
// Anzahl der Sprachen, wird mehrfach gebraucht
$sql = sprintf("SELECT * FROM %s",
						$this->cms->tbname['papoo_mv_name_language']
				);
$sprachen1 = $this->db->get_results($sql);

//Die verfügbaren Verwaltungen...
$sql = sprintf("SELECT * FROM %s",DB_PRAEFIX."papoo_mv");
$mvs = $this->db->get_results($sql,ARRAY_A);
$this->content->template['mv_listing']=$mvs;

foreach ($mvs as $kmv=>$vmv)
{
	//Die verfügbaren Felder
	$sql = sprintf("SELECT * FROM %s WHERE mvcform_form_id = '%d' AND mvcform_type='text' ORDER BY mvcform_form_id ASC",DB_PRAEFIX."papoo_mvcform",$vmv['mv_id']);
	$mvs_felder = $this->db->get_results($sql,ARRAY_A);
	$this->content->template['mv_felder_listing'][$vmv['mv_id']]=$mvs_felder;
}

//print_r($this->content->template['mv_felder_listing']);

// OrderId �ndern
if (!empty($this->checked->change_order_id))
{
	// alte und neue Order ID zwischenspeichern
	$old_number = $this->checked->order_id_old_number;
	$new_number = $this->checked->order_id_new_number;
	// mv_id und mv_content_id=Feldid
	$mv_id = $this->checked->mv_id;
	$mv_content_id = $this->checked->feldid;
	// ist die neue Order ID nicht leer, eine Zahl und nicht gleich der alten OrderID?
	if ($new_number != ""
		&& is_numeric($new_number)
		&& $new_number != $old_number)
	{
		// lookup_id f�r die alte order_id aus der Lang Tabelle holen
		$sql = sprintf("SELECT lookup_id FROM %s
										WHERE order_id = '%s' 
										AND lang_id = '%d'",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($mv_id)
										. "_lang_"
										. $this->db->escape($mv_content_id),
										
										$this->db->escape($old_number),
										$this->db->escape($this->cms->lang_back_content_id)
						);
		$old_lookup_id = $this->db->get_var($sql);
		// lookup_id f�r die neue order_id aus der Lang Tabelle holen
		$sql = sprintf("SELECT lookup_id FROM %s
										WHERE order_id = '%s' 
										AND lang_id = '%d'",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($mv_id)
										. "_lang_"
										. $this->db->escape($mv_content_id),
										
										$this->db->escape($new_number),
										$this->db->escape($this->cms->lang_back_content_id)
						);
		$new_lookup_id = $this->db->get_var($sql);
		// gabs die alte order_id �berhaupt? dann gibts auch eine lookup_id daf�r
		if (!empty($old_lookup_id))
		{
			// Orderid des Vorg�ngers auf order des Nachfolgers setzen
			$sql = sprintf("UPDATE %s SET order_id = '%s' 
										WHERE lookup_id = '%s'
										AND lang_id = '%d'",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($mv_id)
										. "_lang_"
										. $this->db->escape($mv_content_id),
										
										$this->db->escape($new_number),
										$this->db->escape($old_lookup_id),
										$this->db->escape($this->cms->lang_back_content_id)
							);
			$this->db->query($sql);
		}
		//Orderid des alten auf neu setzen
		if (!empty($new_lookup_id))
		{
			$sql = sprintf("UPDATE %s SET order_id = '%s' 
										WHERE lookup_id = '%s'
										AND lang_id = '%d'",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($mv_id)
										. "_lang_"
										. $this->db->escape($mv_content_id),
										
										$this->db->escape($old_number),
										$this->db->escape($new_lookup_id),
										$this->db->escape($this->cms->lang_back_content_id)
							);
			$this->db->query($sql);
		}
		// so jetzt wieder die order_ids von 1 bis x durchgehen
		if (!empty($sprachen1))
		{
			foreach($sprachen1 as $sprache)
			{
				// Auswahlm�glichkeiten f�r die Sprache aus der Lang Tabelle holen
				$sql = sprintf("SELECT * FROM %s
											WHERE lookup_id <> 0 
											AND lang_id = '%d' 
											ORDER BY order_id ASC",
											
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($mv_id)
											. "_lang_"
											. $this->db->escape($mv_content_id),
											
											$this->db->escape($sprache->mv_lang_id)
								);
				$result = $this->db->get_results($sql);
				$i = 10;
				// Durchz�hlen und neu einsortieren
				if (!empty($result))
				{
					foreach($result as $dat)
					{
						$sql = sprintf("UPDATE %s SET order_id = '%d' 
													WHERE lookup_id = '%d'
													AND lang_id = '%d'",
													
													$this->cms->tbname['papoo_mv']
													. "_content_"
													. $this->db->escape($mv_id)
													. "_lang_"
													. $this->db->escape($mv_content_id),
													
													$this->db->escape($i),
													$this->db->escape($dat->lookup_id),
													$this->db->escape($sprache->mv_lang_id)
											);
						$this->db->query($sql);
						$i = $i + 10;
					}
				}
			}
		}
	}
}
// erster Aufruf zum Editieren
if (empty($this->checked->submit_field))
{
	// Feld Formular erstellen
	$this->make_feld_form();
	// Felddaten rausholen
	$this->get_field_data();
	// Feld-Typ ans Template zur Ausgabe des zum Feld-Typ passenden Templates
	// s. create_input.html: {elseif $mvcform_type} {include file="../../../plugins/mv/templates/formeingabe_$mvcform_type.html"}
	$this->content->template['mvcform_type'] = $this->content->template['fdat'][0]['mvcform_type'];
	// Markierung f�r neue Feldtypen
	// ist es ein select/multi/radio...
	if (!empty($this->content->template['language_form'])
		&& ($this->content->template['fdat'][0]['mvcform_type'] == "radio"
			|| $this->content->template['fdat'][0]['mvcform_type'] == "select"
			|| $this->content->template['fdat'][0]['mvcform_type'] == "multiselect"
			|| $this->content->template['fdat'][0]['mvcform_type'] == "check"
			|| $this->content->template['fdat'][0]['mvcform_type'] == "pre_select")
			)
	{
		// dann hole alle Eintr�ge aus der entsprechenden Lang Tabelle
		$sql = sprintf("SELECT * FROM %s
										WHERE lookup_id <> 0 
										AND lang_id = '%d' 
										ORDER BY order_id",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($this->checked->mv_id)
										. "_lang_"
										. $this->db->escape($this->checked->feldid),
										
										$this->db->escape($this->cms->lang_back_content_id)
						);
		$result = $this->db->get_results($sql);
		if (!empty($result))
		{
			foreach($result as $row)
			{
				// und speicher die Daten f�r Template
				$this->content->template['liste_array'][$row->lookup_id] = "nobr:" . htmlentities(trim($row->content), ENT_QUOTES, "UTF-8");
				$this->content->template['order_id'][$row->lookup_id] = $row->order_id;
			}
		}
		else // Falls kein Wert in der DB gefunden wurde.
		{
			$this->content->template['liste_array'][1] = "FEHLER: Kein Wert definiert!" ;
			$this->content->template['order_id'][1] = "10";
		}
	}
	// Preisintervall Feld
	if ($this->content->template['fdat'][0]['mvcform_type'] == "preisintervall")
	{
		$sql = sprintf("SELECT * FROM %s
									WHERE mv_id = '%d'
									AND feld_id = '%d'
									LIMIT 1",
									
									$this->cms->tbname['papoo_mv_feld_preisintervall'],
									
									$this->db->escape($this->checked->mv_id),
									$this->db->escape($this->checked->feldid)
						);
		$preisintervall = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['fdat'][0]['mvcform_preisintervall_list'] = "nobr:" . $preisintervall[0]['intervalle'];
		$this->content->template['fdat'][0]['mvcform_preisintervall_waehrung'] = $preisintervall[0]['waehrung'];
	}
}
// wenn auf Speichern gedr�ckt wurde, Button: In die Datenbank eintragen
else
{
	if ($this->checked->mvcform_type == "select"
		|| $this->checked->mvcform_type == "radio"
		|| $this->checked->mvcform_type == "multiselect"
		|| $this->checked->mvcform_type == "check"
		|| $this->checked->mvcform_type == "pre_select")
	{
		// muss vor Aufruf von check_data_fields.php gemacht werden!
		$sql = sprintf("SELECT * FROM %s
									WHERE lang_id = '%d' 
									AND lookup_id <> 0
									ORDER BY lookup_id",
									
									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($this->checked->mv_id)
									. "_lang_"
									. $this->db->escape($this->checked->feldid),
									
									$this->db->escape($this->cms->lang_back_content_id)
						);
		$old_lookup_data = $this->db->get_results($sql, ARRAY_A);
	}
	// Daten checken
	// Wenn ok, dann eintragen (del_field_value_active ist 1, wenn ein Eintrag f�r Radiobutton/selectbox gel�scht wurde.)
	require(PAPOO_ABS_PFAD . '/plugins/mv/lib/check_data_fields.php');
	if (!$fehler) // $fehler set by check_data_fields.php on return
	{
		$modus = "update";
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/insup_new_field.php');
		// lookup Tabellen Werte neu speichern.
		if (($this->checked->mvcform_type == "radio"
			|| $this->checked->mvcform_type == "select"
			|| $this->checked->mvcform_type == "multiselect"
			|| $this->checked->mvcform_type == "check"
			|| $this->checked->mvcform_type == "pre_select"))
		{
			// Neues Feld wird unter create_new_field() in der mv.php und via alter_new_field.php erstellt, nicht mehr hier.
			// S. auch make_input_entry.php Z. 624 (Aufruf $this->create_new_field();)
			if (count($this->checked->mvcform_content_list)) $content_alt_array = $this->checked->mvcform_content_list; // Vorgabe (alte Werte)
			else $content_alt_array = array();
			if (count($content_alt_array))
			{
				// die Daten f�r die aktuelle Sprache in der lang Tabelle l�schen
				$sql = sprintf("DELETE FROM %s
										WHERE lang_id = '%d'",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($this->checked->mv_id)
										. "_lang_"
										. $this->db->escape($this->checked->feldid),
										
										$this->db->escape($this->cms->lang_back_content_id)
								);
				$this->db->query($sql);
				// Die Daten f�r die aktuelle Sprache neu einf�gen
				if (count($old_lookup_data)) // bei Installationen mit Fehlern in der DB kann das Array leer sein
				{
					foreach ($content_alt_array AS $key => $value)
					{
						// die zugeh�rige orderID finden
						// die lookupID findet sich bei Gleichheit zu $key und $key kann daher als lookupID verwendet werden
						foreach ($old_lookup_data AS $key2 => $value2)
						{
							if ($old_lookup_data[$key2]['lookup_id'] == $key)
							{
								$sql = sprintf("INSERT INTO %s
														SET content = '%s', 
														lang_id = '%s', 
														lookup_id = '%s', 
														order_id = '%s'",
														
														$this->cms->tbname['papoo_mv']
														. "_content_"
														. $this->db->escape($this->checked->mv_id)
														. "_lang_"
														. $this->db->escape($this->checked->feldid),
														
														$this->db->escape($content_alt_array[$key]),
														$this->db->escape($this->cms->lang_back_content_id),
														$key,
														$old_lookup_data[$key2]['order_id']
												);
								$this->db->query($sql);
								break;
							}
						}
					}
				}
			}
			// neue Werte in die DB anf�gen, wenn welche vorhanden sind. Immer in allen Sprachen.
			if ($this->checked->mvcform_content_list_new != "")
			{
				$content_neu_array = explode("\r\n", trim($this->checked->mvcform_content_list_new));
				if (count($content_neu_array))
				{
					// alle neuen Werte in allen Sprachen speichern
					$sql = sprintf("SELECT MAX(lookup_id) lookup_id,
											MAX(order_id) order_id
											FROM %s",
										
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($this->checked->mv_id)
											. "_lang_"
											. $this->db->escape($this->checked->feldid)
							);
					$max_ids = $this->db->get_results($sql, ARRAY_A);
					$last_order_id = $max_ids[0]['order_id'] + 10;
					$last_lookup_id = $max_ids[0]['lookup_id'] + 1;
					foreach ($content_neu_array AS $key => $value)
					{
						if ($value)
						{
							$last_order_id = $last_order_id + 10;
							$last_lookup_id = $last_lookup_id + 1;
							$value = $this->db->escape(preg_replace("/\r/", "", $value));
							if (count($sprachen1))
							{
								foreach($sprachen1 as $sprache)
								{
									$sql = sprintf("INSERT INTO %s
															SET content = '%s', 
															lang_id = '%s', 
															lookup_id = '%s', 
															order_id = '%s'",
															
															$this->cms->tbname['papoo_mv']
															. "_content_"
															. $this->db->escape($this->checked->mv_id)
															. "_lang_"
															. $this->db->escape($this->checked->feldid),
															
															$value,
															$this->db->escape($sprache->mv_lang_id),
															$last_lookup_id,
															$last_order_id
													);
									$this->db->query($sql);
								}
							}
						}
					}
				}
			}
		}
		if ($this->checked->mvcform_type == "select"
			|| $this->checked->mvcform_type == "radio"
			|| $this->checked->mvcform_type == "multiselect"
			|| $this->checked->mvcform_type == "check"
			|| $this->checked->mvcform_type == "pre_select")
		{
			// Searchtabellen updaten
			$sql = sprintf("SELECT * FROM %s
										WHERE lang_id = '%d' 
										AND lookup_id <> 0 
										ORDER BY lookup_id",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($this->checked->mv_id)
										. "_lang_"
										. $this->db->escape($this->checked->feldid),
										
										$this->db->escape($this->cms->lang_back_content_id)
							);
			$neue_werte_lp = $this->db->get_results($sql, ARRAY_A);
			$sql = sprintf("SELECT mvcform_name FROM %s
													WHERE mvcform_id = '%d' 
													AND mvcform_meta_id = '%d' 
													AND mvcform_form_id = '%d'",
													
													$this->cms->tbname['papoo_mvcform'],
													
													$this->db->escape($this->checked->feldid),
													$this->db->escape($this->meta_gruppe),
													$this->db->escape($this->checked->mv_id)
							);
			$feld_name = $this->db->get_var($sql);
			$feld_id = $this->checked->feldid;
			// Vergleich alter Zustand mit der Eingabe
			if (!empty($old_lookup_data))
			{
				foreach ($old_lookup_data as $key => $value)
				{
					foreach ($neue_werte_lp as $key2 => $value2)
					{
						// wenn alt = neu keine �nderung, weitersuchen
						if ($value['lookup_id'] == $value2['lookup_id']) continue 2;
					}
					// �nderung gefunden
					// F�r multiselect besondere Behandlung, da das mehr als einen Pointer haben kann
					if ($this->checked->mvcform_type != "multiselect")
					{
						// Update f�r select, preselect, check, radio
						$sql = sprintf("UPDATE %s SET %s = ''
												WHERE %s = '%s'",
												
												$this->cms->tbname['papoo_mv']
												. "_content_"
												. $this->db->escape($this->checked->mv_id)
												. "_search_"
												. $this->db->escape($this->cms->lang_back_content_id),
												
												$this->db->escape($feld_name . "_" . $feld_id),
												$this->db->escape($feld_name . "_" . $feld_id),
												$value['lookup_id']
										);
						$this->db->query($sql);
					}
					else
					{
						// Behandlung von multiselect
						// Suche alle, die gleich oder �hnlich der �nderungs-ID sind (IDs der vom User gel�schten Werte)
						$sql = sprintf("SELECT mv_content_id, %s FROM %s  
												WHERE %s LIKE %s",
												
												$this->db->escape($feld_name . "_" . $feld_id),
												
												$this->cms->tbname['papoo_mv']
												. "_content_"
												. $this->db->escape($this->checked->mv_id)
												. "_search_"
												. $this->db->escape($this->cms->lang_back_content_id),
												
												$this->db->escape($feld_name . "_" . $feld_id),
												"'%" . $value['lookup_id'] . "%'"
										);
						$ms_result = $this->db->get_results($sql, ARRAY_A);
						if (count($ms_result))
						{
							foreach ($ms_result as $key3 => $value3)
							{
								// die Werte f�r Vergleiche extrahieren
								$expl_arr = explode("\r\n", $value3[$feld_name . "_" . $feld_id]);
								foreach ($expl_arr as $key4 => $value4)
								{
									// die vom User gel�schte ID osz gefunden (Wert wurde entfernt)
									if ($value4 == $value['lookup_id'])
									{
										unset ($expl_arr[$key4]); // aus dem Array raus
										$found = 1; // Schalter kennzeichnet einen Fund
										continue; // �berspringe restliche
									}
									// leere Werte auch eliminieren
									if (empty($value4)) unset ($expl_arr[$key4]);
								}
								// �nderung wurde entdeckt. jetzt verarbeiten
								if ($found)
								{
									$found = 0; // reset f�r weitere Teste
									$new_val = implode("\r\n", $expl_arr); // f�r die DB neu zusammenbauen (ohne die gel�schte(n) IDs)
									$sql = sprintf("UPDATE %s SET %s = '%s'
															WHERE mv_content_id = '%d'",
															
															$this->cms->tbname['papoo_mv']
															. "_content_"
															. $this->db->escape($this->checked->mv_id)
															. "_search_"
															. $this->db->escape($this->cms->lang_back_content_id),
															
															$this->db->escape($feld_name . "_" . $feld_id),
															$this->db->escape($new_val),
															$this->db->escape($value3['mv_content_id'])
													);
									$this->db->query($sql);

								}
							}
						}
					}
				}
			}
		}
		// Neu laden
		$location_url = $_SERVER['PHP_SELF']
						. "?menuid="
						. $this->checked->menuid
						. "&template="
						. $this->checked->template
						. "&mv_id="
						. $this->checked->mv_id
						. "&change_ok=1";
		$this->reload($location_url);
	}
	else // Ein Fehler ist aufgetreten ($fehler = 1)
	{
		// Feld Formular erstellen
		$this->make_feld_form();
		// Daten erneut ausgeben
		$this->do_it_again();
		// Template Error
		$this->content->template['fehler'] = "ok"; // to ??? wozu?
	}
}
?>