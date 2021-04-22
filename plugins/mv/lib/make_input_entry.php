<?php
/**
* Neue Gruppen und Felder hinzuf�gen
*
* called by mv.php: create_input.html (Felder bearbeiten), create_meta.html (Meta Einstellungen)
*/
#$this->db->trace = true;

$this->checked->mv_id = is_numeric($this->checked->mv_id) ? $this->checked->mv_id : 1;
$this->content->template['metaebene_auswahl'] = "ok";
$this->content->template['delok'] = $this->checked->delok; // MSG Feld wurde gel�scht
$this->content->template['change_ok'] = $this->checked->change_ok; // MSG Feld wurde ge�ndert
$this->content->template['new_ok'] = $this->checked->new_ok; // MSG Feld wurde gespeichert
$this->content->template['grp_saved'] = $this->checked->grp_saved; // MSG Gruppe wurde gespeichert
$this->content->template['grp_changed'] = $this->checked->grp_changed; // MSG Gruppe wurde ge�ndert
$meta_gruppen = $this->get_meta_gruppen(); // erforderlich?? Wird sp�ter nochmals gemacht, oder?
// ans Template weitergeben
$this->content->template['meta_gruppen'] = $meta_gruppen;

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


// wenn keine Rechte und keine Metaebene gew�hlt ist
if ($this->meta_gruppe == ""
	|| !$this->is_user_right_here())
{
	if ($this->have_admin_rights())
	{
		$this->content->template['mv_is_admin'] = "ok";
		// wurde eine neue Metagruppe hinzugef�gt? dann auch in Datenbank speichern
		if (!empty($this->checked->submit_group_new_name))
		{
			// �berpr�fen ob der Metagruppenname schon existiert
			$sql = sprintf("SELECT mv_meta_id
									FROM %s
									WHERE mv_meta_group_name = '%s'",
									
									$this->cms->tbname['papoo_mv']
									. "_meta_"
									. $this->db->escape($this->checked->mv_id),
									
									$this->db->escape($this->checked->group_new_name)
							);
			$gibts_schon = $this->db->get_var($sql);
			// wurde auch ein Text f�r den neuen Gruppennamen mitgeschickt
			if ($this->checked->group_new_name == "") $this->content->template['meta_error_empty'] = "error";
			// ist der Metagruppenname noch frei, also auch unter diesem Gruppennamen speichern
			elseif(empty($gibts_schon))
			{
				// holt die MAX meta_id aus allen Verwaltungen
				//$max_meta_id = $this->get_max_meta_id();
				$sql = sprintf("SELECT MAX(mv_meta_id)
										FROM %s",
										$this->cms->tbname['papoo_mv']
										. "_meta_"
										. $this->db->escape($this->checked->mv_id)
								);
				$max_meta_id = $this->db->get_var($sql);
				$max_meta_id++;
				$sql = sprintf("INSERT INTO %s SET	mv_meta_group_name = '%s', 
													mv_meta_id = '%d'",
													
													$this->cms->tbname['papoo_mv']
													. "_meta_"
													. $this->db->escape($this->checked->mv_id),
													
													$this->db->escape($this->checked->group_new_name),
													$this->db->escape($max_meta_id)
								);
				$was = $this->db->query($sql);
				$sql = sprintf("SELECT mv_lang_id
										FROM %s",
										$this->cms->tbname['papoo_mv_name_language']
								);
				$sprachen = $this->db->get_results($sql, ARRAY_A);

				if (!empty($sprachen))
				{
					foreach($sprachen as $sprache)
					{
						$sql = sprintf("INSERT INTO %s SET	mv_meta_lang_id = '%d',
															mv_meta_lang_lang_id = '%d'",
															
															$this->cms->tbname['papoo_mv']
															. "_meta_lang_"
															. $this->db->escape($this->checked->mv_id),
															
															$this->db->escape($max_meta_id),
															$this->db->escape($sprache['mv_lang_id'])
										);
						$this->db->query($sql);
					}
				}
				// Felder und Defaultwerte aus der admin Metaebene kopieren
				$sql = sprintf("SELECT * FROM %s
											WHERE mvcform_meta_id = '1'
											AND mvcform_form_id = '%d'",
											
											$this->cms->tbname['papoo_mvcform'],
											
											$this->db->escape($this->checked->mv_id)
								);
				$mvcform = $this->db->get_results($sql, ARRAY_A);
				$sql = sprintf("SELECT T2.* FROM %s T1
											INNER JOIN %s T2 ON (T1.mvcform_meta_id = T2.mvcform_lang_meta_id)
												AND (T1.mvcform_id = T2.mvcform_lang_id)
											WHERE T1.mvcform_form_id = '%d'
											AND T1.mvcform_meta_id = 1",
											$this->cms->tbname['papoo_mvcform'],
											$this->cms->tbname['papoo_mvcform_lang'],
											$this->db->escape($this->checked->mv_id)
								);
				$mvcform_lang = $this->db->get_results($sql, ARRAY_A);
				$firstround = 1;
				$keys = "";
				$values = "";
				if (!empty($mvcform))
				{
					// Zeilen durchloopen
					foreach($mvcform as $zeile)
					{
						if ($firstround == 1) $keys .= "\n(";
						$values .= "\n(";
						// Spalten durchloopen
						foreach($zeile as $key => $value)
						{
							if ($firstround == 1) $keys .= "`" . $this->db->escape($key) . "`, ";
							// anstatt den kopierten MetaebenenID Wert zu nehmen, doch lieber den Wert f�r die neue Metaebene nehmen
							if ($key == "mvcform_meta_id") $values .= "'" . $this->db->escape($max_meta_id) . "', ";
							else $values .= "'" . $this->db->escape($value) . "', ";
						}
						if ($firstround == 1) $keys = substr($keys, 0, -2) . ")";
						$values = substr($values, 0, -2) . "), ";
						$firstround++;
					}
				}
				// 	tr�gt die von der admin Metaebene kopierten Werte in die Tabelle ein
				if (!empty($values))
				{
					$values = substr($values, 0, -2); // letztes ", " wieder rausnehmen
					$sql = sprintf("INSERT INTO %s %s
												VALUES %s",
												
												$this->cms->tbname['papoo_mvcform'],
												
												$keys,
												$values
									);
					$this->db->query($sql);
				}
			// so jetzt das gleiche f�r die mvcform_lang Tabelle machen
				$firstround = 1;
				$keys = "";
				$values = "";
				if (!empty($mvcform_lang))
				{
					foreach($mvcform_lang as $zeile)
					{
						if ($firstround == 1) $keys .= "\n(";
						$values .= "\n(";
						foreach($zeile as $key => $value)
						{
							if ($firstround == 1) $keys .= "`" . $this->db->escape($key) . "`, ";
							if ($key == "mvcform_lang_meta_id") $values .= "'" . $this->db->escape($max_meta_id) . "', ";
							else $values .= "'" . $this->db->escape($value) . "', ";
						}
						if ($firstround == 1) $keys = substr($keys, 0, -2) . ")";
						$values = substr($values, 0, -2) . "), ";
						$firstround++;
					}
				}
				$values = substr($values, 0, -2);
				if (!empty($values))
				{
					$sql = sprintf("INSERT INTO %s %s
											VALUES %s",
											
											$this->cms->tbname['papoo_mvcform_lang'],
											
											$keys,
											$values
									);
					$this->db->query($sql);
				}
				if (!empty($this->detail_anzahl))
				{
					foreach($this->detail_anzahl as $detail_id)
					{
						// das Ausgabe Template erm�glichen
						$sql = sprintf("INSERT INTO %s
													(`id`,
													`template_content_all`,
													`template_content_one`,
													`lang_id`,
													`meta_id`,
													`detail_id`
													) 
													VALUES
													('1', '', '', '1', '%d', '%d'),
													('2', '', '', '2', '%d', '%d'),
													('3', '', '', '3', '%d', '%d'),
													('4', '', '', '4', '%d', '%d'),
													('5', '', '', '5', '%d', '%d'),
													('6', '', '', '6', '%d', '%d'),
													('7', '', '', '7', '%d', '%d')",
													
													$this->cms->tbname['papoo_mv']
													. "_template_"
													. $this->db->escape($this->checked->mv_id),
													
													$this->db->escape($max_meta_id),
													$this->db->escape($detail_id),
													$this->db->escape($max_meta_id),
													$this->db->escape($detail_id),
													$this->db->escape($max_meta_id),
													$this->db->escape($detail_id),
													$this->db->escape($max_meta_id),
													$this->db->escape($detail_id),
													$this->db->escape($max_meta_id),
													$this->db->escape($detail_id),
													$this->db->escape($max_meta_id),
													$this->db->escape($detail_id),
													$this->db->escape($max_meta_id),
													$this->db->escape($detail_id)
									);
						$this->db->query($sql);
					}
				}
				// Gruppen und Defaultwerte aus der standard Metaebene kopieren
				$sql = sprintf("SELECT * FROM %s
											WHERE mvcform_group_form_meta_id = '1'
											AND mvcform_group_form_id = '%d'",
											$this->cms->tbname['papoo_mvcform_group'],
											$this->db->escape($this->checked->mv_id)
								);
				$mvcgroup = $this->db->get_results($sql, ARRAY_A);
				foreach ($mvcgroup AS $key => $value)
				{
					$sql = sprintf("SELECT * FROM %s
												WHERE mvcform_group_lang_meta = '1'
												AND mvcform_group_lang_id = '%d'",
												$this->cms->tbname['papoo_mvcform_group_lang'],
												$value['mvcform_group_id']
									);
					$mvcgroup_lang[] = $this->db->get_results($sql, ARRAY_A);
				}
				//Neue Meta ID $max_meta_id
				$set = "";
				// Die alten Eintr�ge durchgehen und SQL Statement mit neuer MEta ID erzeugen
				foreach ($mvcgroup_lang AS $key)
				{
					foreach($key as $dat)
					{
						//Reset set Array
						$set = "";
						$set .= " mvcform_group_lang_id='" . $dat['mvcform_group_lang_id'] . "', ";
						$set .= " mvcform_group_lang_lang='" . $dat['mvcform_group_lang_lang'] . "', ";
						$set .= " mvcform_group_text='" . $dat['mvcform_group_text'] . "', ";
						$set .= " mvcform_group_text_intern='" . $dat['mvcform_group_text_intern'] . "', ";
						$set .= " mvcform_group_lang_meta='" . $max_meta_id . "' ";
						$insert[] = $set;
					}
				}
				//Statements abarbeiten und eingeben
				foreach($insert as $sq)
				{
					$sql = sprintf("INSERT INTO %s SET %s",
											$this->cms->tbname['papoo_mvcform_group_lang'],
											$sq
									);
					$this->db->query($sql);
				}
				//Reset set Array
				$set = "";
				//Reset Insert Array
				$insert = array();
				// Die alten Eintr�ge durchgehen und SQL Statement mit neuer MEta ID erzeugen
				foreach($mvcgroup as $dat)
				{
					//Reset set Array
					$set = "";
					$set .= " mvcform_group_id='" . $dat['mvcform_group_id'] . "', ";
					$set .= " mvcform_group_form_id='" . $dat['mvcform_group_form_id'] . "', ";
					$set .= " mvcform_group_order_id='" . $dat['mvcform_group_order_id'] . "', ";
					$set .= " mvcform_group_name='" . $dat['mvcform_group_name'] . "', ";
					$set .= " mvcform_group_form_meta_id='" . $max_meta_id . "' ";
					$insert[] = $set;
				}
				//Statements abarbeiten und eingeben
				foreach($insert as $sq)
				{
					$sql = sprintf("INSERT INTO %s SET %s",
											$this->cms->tbname['papoo_mvcform_group'],
											$sq
									);
					$this->db->query($sql);
				}
			}
			// den gibt es schon, Meldung ans Template, das ein neuer Name her muss;)
			else $this->content->template['meta_error_double'] = "error";
		}
		// wurde eine neue Rechtegruppe einer Metagruppe hinzugewiesen? dann ab in die Datenbank
		if (!empty($this->checked->submit_new_right_group))
		{
			// �berpr�fen ob die Rechtegruppe schon bei dieser Metagruppe dabei ist
			$sql = sprintf("SELECT mv_mpg_id
									FROM %s
									WHERE mv_mpg_id = '%d' 
									AND mv_mpg_group_id = '%d'",
									
									$this->cms->tbname['papoo_mv']
									. "_mpg_"
									. $this->db->escape($this->checked->mv_id),
									
									$this->db->escape($this->checked->meta_gruppe),
									$this->db->escape($this->checked->rechte_gruppe)
							);
			$gibts_schon = $this->db->get_var($sql);
			// wurde auch was ausgew�hlt?
			if ($this->checked->meta_gruppe == ""
				|| $this->checked->rechte_gruppe == ""
				||
					(empty($this->checked->group_read)
					&& empty($this->checked->group_write))) $this->content->template['meta_error_fehlt_was'] = "error";
			// mitgeschickte Rechtegruppe der Metagruppe hinzuf�gen, wenn sie noch nicht dabei ist und alles korrekt ausgef�llt wurde
			if (empty($gibts_schon)
				&& $this->content->template['meta_error_fehlt_was'] != "error")
			{
				$sql = sprintf("INSERT INTO %s SET mv_mpg_id = '%d', 
													mv_mpg_group_id = '%d', 
													mv_mpg_write = '%d', 
													mv_mpg_read = '%d'",
													
													$this->cms->tbname['papoo_mv']
													. "_mpg_"
													. $this->db->escape($this->checked->mv_id),
													
													$this->db->escape($this->checked->meta_gruppe),
													$this->db->escape($this->checked->rechte_gruppe),
													$this->db->escape($this->checked->group_write),
													$this->db->escape($this->checked->group_read)
								);
				$this->db->query($sql);
				// damit die checked Werte nicht weider eingesetzt werden Flag setzen
				$werte_reset = "ja";
			}
			// Meldung ans Template, das die Rechtegruppe schon dabei ist
			if (!empty($gibts_schon)) $this->content->template['meta_error_rg_double'] = "error";
		}
		// wenn eine Meta-Rechte-Gruppe verkn�pfung gel�scht werden soll
		if ($this->checked->del_mpg == "del")
		{
			$sql = sprintf("DELETE FROM %s
									WHERE mv_mpg_id = '%d' 
									AND mv_mpg_group_id = '%d'",
									
									$this->cms->tbname['papoo_mv']
									. "_mpg_"
									. $this->db->escape($this->checked->mv_id),
									
									$this->db->escape($this->checked->mv_mpg_id),
									$this->db->escape($this->checked->mv_mpg_group_id)
							);
			$this->db->query($sql);
		}
		if ($this->checked->del_meta == "del"
			AND !$this->checked->submitdelecht
			AND !$this->checked->submit_group_new_name
			AND !$this->checked->submit_new_right_group)
		{
			$this->content->template['meta_gruppe'] = $this->checked->meta_gruppe;
			$this->content->template['mv_id'] = $this->checked->mv_id;
			$this->content->template['fragedel'] = "ok";
		}
		// Metagruppe l�schen
		if ($this->checked->submitdelecht)
		{
			if ($this->checked->meta_gruppe != "1")
			{
				$sql = sprintf("DELETE FROM %s
										WHERE mv_meta_id = '%d'",
										
										$this->cms->tbname['papoo_mv']
										. "_meta_"
										. $this->db->escape($this->checked->mv_id),
										
										$this->db->escape($this->checked->meta_gruppe)
								);
				$this->db->query($sql);
				// in der Sprachtabelle l�schen
				$sql = sprintf("DELETE FROM %s
										WHERE mv_meta_lang_id = '%d'",
										
										$this->cms->tbname['papoo_mv']
										. "_meta_lang_"
										. $this->db->escape($this->checked->mv_id),
										
										$this->db->escape($this->checked->meta_gruppe)
								);
				$this->db->query($sql);
				// auch die Verkn�pfungen f�r diese Metaebene l�schen
				$sql = sprintf("DELETE FROM %s
										WHERE mv_mpg_id = '%d'",
										
										$this->cms->tbname['papoo_mv']
										. "_mpg_"
										. $this->db->escape($this->checked->mv_id),
										
										$this->db->escape($this->checked->meta_gruppe)
								);
				$this->db->query($sql);
				$sql = sprintf("SELECT DISTINCT(mvcform_id) FROM %s
											WHERE mvcform_meta_id = '%d'
											AND mvcform_form_id = '%d'",
											
											$this->cms->tbname['papoo_mvcform'],
											
											$this->db->escape($this->checked->meta_gruppe),
											$this->db->escape($this->checked->mv_id)
								);
				$mvcform_id = $this->db->get_results($sql, ARRAY_A);
				// und die Feldeintr�ge f�r diese Metagruppe
				$sql = sprintf("DELETE FROM %s
										WHERE mvcform_meta_id = '%d' 
										AND mvcform_form_id = '%d'",
										
										$this->cms->tbname['papoo_mvcform'],
										
										$this->db->escape($this->checked->meta_gruppe),
										$this->db->escape($this->checked->mv_id)
								);
				$this->db->query($sql);
				foreach ($mvcform_id AS $key => $value)
				{
					// und die Spracheintr�ge f�r die jeweiligen Feldeintr�ge l�schen
					$sql = sprintf("DELETE FROM %s
											WHERE mvcform_lang_meta_id = '%d'
											AND mvcform_lang_id = '%d'",
											
											$this->cms->tbname['papoo_mvcform_lang'],
											
											$this->db->escape($this->checked->meta_gruppe),
											$value['mvcform_id']
									);
					$this->db->query($sql);
				}
				// Templates f�r diese Metaebene l�schen
				$sql = sprintf("DELETE FROM %s
										WHERE meta_id = '%d'",
										
										$this->cms->tbname['papoo_mv']
										. "_template_"
										. $this->db->escape($this->checked->mv_id),
										
										$this->db->escape($this->checked->meta_gruppe)
								);
				$this->db->query($sql);
				// Nachricht ans Template, das gel�scht wurde
				$this->content->template['mv_system_msg'] = $this->content->template['plugin']['mv']['meta_deleted'];
				$sql = sprintf("SELECT DISTINCT(T1.mvcform_group_id)
										FROM %s T1
										INNER JOIN %s T2 ON (T1.mvcform_group_id = T2.mvcform_group_lang_id)
										AND (T1.mvcform_group_form_meta_id = T2.mvcform_group_lang_meta)
										WHERE T1.mvcform_group_form_id = '%d'",
										$this->cms->tbname['papoo_mvcform_group'],
										$this->cms->tbname['papoo_mvcform_group_lang'],
										$this->db->escape($this->checked->mv_id)
								);
				$mvcform_group_id = $this->db->get_results($sql, ARRAY_A);
				$sql = sprintf("DELETE FROM %s
										WHERE mvcform_group_form_meta_id = '%d'
										AND mvcform_group_form_id = '%d'",
										
										$this->cms->tbname['papoo_mvcform_group'],
										
										$this->db->escape($this->checked->meta_gruppe),
										$this->db->escape($this->checked->mv_id)
								);
				$this->db->query($sql);
				foreach ($mvcform_group_id AS $key => $value)
				{
					$sql = sprintf("DELETE FROM %s
											WHERE mvcform_group_lang_meta = '%d'
											AND mvcform_group_lang_id = '%d'",
											$this->cms->tbname['papoo_mvcform_group_lang'],
											$this->db->escape($this->checked->meta_gruppe),
											$value['mvcform_group_id']
									);
					$this->db->query($sql);
				}
			}
			elseif($this->checked->meta_gruppe == "1") $this->content->template['mv_system_error'] = $this->content->template['plugin']['mv']['meta_standard'];
			else $this->content->template['mv_system_error'] = $this->content->template['plugin']['mv']['meta_id_fehlt'];
		}
	}
	// nochmal die Meta Gruppen aus der Datenbank holen, die dieser User sehen darf, falls es �nderungen gab
	if ($this->have_admin_rights())
	{
		$sql = sprintf("SELECT DISTINCT(mv_meta_id),
										mv_meta_group_name
										FROM %s
										ORDER BY mv_meta_group_name",
										
										$this->cms->tbname['papoo_mv']
										. "_meta_"
										. $this->db->escape($this->checked->mv_id)
						);
		$meta_gruppen = $this->db->get_results($sql, ARRAY_A);
	}
	else
	{
		$sql = sprintf("SELECT DISTINCT (mv_meta_id),
										mv_meta_group_name
										FROM %s, %s, %s
									  	WHERE  mv_mpg_id = mv_meta_id
									  	AND mv_mpg_group_id = gruppenid
									  	AND userid = '%d'
										ORDER BY mv_meta_group_name",
										
										$this->cms->tbname['papoo_mv']
										. "_meta_"
										. $this->db->escape($this->checked->mv_id),
										
										$this->cms->tbname['papoo_mv']
										. "_mpg_"
										. $this->db->escape($this->checked->mv_id),
										
										$this->cms->tbname['papoo_lookup_ug'],
										
										$this->db->escape($this->user->userid)
						);
		$meta_gruppen = $this->db->get_results($sql, ARRAY_A);
	}
	// Papoo Rechte Gruppen aus der Datenbank holen
	$sql = sprintf("SELECT gruppeid,
							gruppenname
							FROM %s
							ORDER BY gruppenname",
							$this->cms->tbname['papoo_gruppe']
					);
	$papoo_gruppen = $this->db->get_results($sql, ARRAY_A);
	// den Meta Gruppen zugewiesene Papoo Gruppen aus der Datenbank holen
	$sql = sprintf("SELECT mv_mpg_id,
							mv_mpg_group_id,
							gruppenname,
							mv_meta_group_name,
							mv_mpg_write,
							mv_mpg_read
							FROM %s, %s, %s
							WHERE mv_mpg_id = mv_meta_id 
							AND mv_mpg_group_id = gruppeid 
							ORDER BY mv_meta_group_name,
										gruppenname",
										
							$this->cms->tbname['papoo_mv']
							. "_mpg_" .
							$this->db->escape($this->checked->mv_id),
							
							$this->cms->tbname['papoo_gruppe'],
							
							$this->cms->tbname['papoo_mv']
							. "_meta_"
							. $this->db->escape($this->checked->mv_id)
					);
	$meta_papoo_gruppen = $this->db->get_results($sql, ARRAY_A);
	// ans Template weitergeben
	$this->content->template['meta_gruppen'] = $meta_gruppen;
	$this->content->template['papoo_gruppen'] = $papoo_gruppen;
	$this->content->template['meta_papoo_gruppen'] = $meta_papoo_gruppen;
	$this->content->template['mv_id'] = $this->checked->mv_id;
	$this->content->template['metaebene'] = "ok";
	$this->content->template['mv_meta_editieren'] = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=mv/templates/mv_meta_edit.html";
	$this->content->template['link'] = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template;
	// die alten Werte wieder bereit stellen
	if ($werte_reset != "ja")
	{
		$this->content->template['meta_gruppe'] = $this->checked->meta_gruppe;
		$this->content->template['rechte_gruppe'] = $this->checked->rechte_gruppe;
		$this->content->template['group_read'] = $this->checked->group_read;
		$this->content->template['group_write'] = $this->checked->group_write;
	}
}
// es wurde schon eine Meta-Verbands-Ebene ausgew�hlt
// ja, dann �berpr�fe, ob der User auch die Rechte dazu hat
else
{
	$sql = sprintf("SELECT mv_art
								FROM %s
								WHERE mv_id = '%d'",
								
								$this->cms->tbname['papoo_mv'],
								
								$this->db->escape($this->checked->mv_id)
					);
	$mv_art = $this->db->get_var($sql);
	// Anzahl vorhandener Felder f�r diese Verwaltung
	$sql = sprintf("SELECT 	count(mvcform_id) 
								FROM %s
								WHERE mvcform_form_id = '%d'",
								
								$this->cms->tbname['papoo_mvcform'],
								
								$this->db->escape($this->checked->mv_id)
					);
	$result = $this->db->get_var($sql);
	if (($result > 8 AND $mv_art == 2) // 8 Standardfelder f�r jede Verwaltung (Systemfelder)
		OR ($result AND $mv_art != 2))
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
		if (!$result[0]['mvcform_must']) $this->content->template['kein_pflichtfeld_fe'] = 1;
		if (!$result[1]['mvcform_must_back']) $this->content->template['kein_pflichtfeld_be'] = 1;
	}
	if ($this->is_user_right_here())
	{
		// Suchmaske
		//$this->content->template['mv_front_link_dir_search'] = "plugin.php?menuid=XXX&template=mv/templates/mv_search_front.html&mv_id=".$this->checked->mv_id
		$this->content->template['mv_front_link_men_search'] = "plugin:mv/templates/mv_search_front.html&mv_id=" . $this->checked->mv_id;
		if ($mv_art == "2")
		{
			// Mitgliedsdaten anzeigen
			//$this->content->template['mv_front_link_dir_show'] = "plugin.php?menuid=XXX&template=mv/templates/mv_show_front.html&mv_id=".$this->checked->				
			//mv_id."&mv_content_id=".$this->checked->mv_content_id."&userid=XXX" ;
			$this->content->template['mv_front_link_men_show'] = "plugin:mv/templates/mv_show_front.html&mv_id="
																	. $this->checked->mv_id
																	. "&mv_content_id="
																	. $this->checked->mv_content_id;
			// Mitgliedsdaten editieren
			//$this->content->template['mv_front_link_dir_edit'] = "plugin.php?menuid=XXX&template=mv/templates/mv_edit_front.html&mv_id=".$this->checked->
			//mv_id."&mv_content_id=".$this->checked->mv_content_id."&userid=XXX" ;
			$this->content->template['mv_front_link_men_edit'] = "plugin:mv/templates/mv_edit_front.html&mv_id="
																	. $this->checked->mv_id
																	. "&mv_content_id="
																	. $this->checked->mv_content_id;
		}
		if ($mv_art == "1")
		{
			// Standard Verwaltung alles anzeigen FE
			//$this->content->template['sv_front_link_dir_show_all'] = "plugin.php?menuid=XXX&template=mv/templates/mv_show_all_front.html&mv_id=".$this->checked->mv_id;
			$this->content->template['sv_front_link_men_show_all'] = "plugin:mv/templates/mv_show_all_front.html&mv_id=" . $this->checked->mv_id;
			// Standart Verwaltung eigene Beitr�ge auflisten
			//$this->content->template['sv_front_link_dir_show_own'] = "plugin.php?menuid=XXX&template=mv/templates/mv_show_all_front.html&mv_id=".$this->checked->mv_id;
			$this->content->template['sv_front_link_men_show_own'] = "plugin:mv/templates/mv_show_all_front.html&mv_id=" . $this->checked->mv_id;
			// Standart Verwaltung neuer Eintrag
			//$this->content->template['sv_front_link_dir_create_own'] = "plugin.php?menuid=XXX&template=mv/templates/mv_create_front.html&mv_id=".$this->checked->mv_id;
			$this->content->template['sv_front_link_men_create_own'] = "plugin:mv/templates/mv_create_front.html&mv_id=" . $this->checked->mv_id;
		}
		$this->content->template['mv_id'] = $this->checked->mv_id;
		$link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template;
		$this->content->template['link'] = $link;
		// Wenn mvid gesetzt
		if (is_numeric($this->checked->mv_id))
		{
			$this->content->template['form'] = "ok";
			// Sortierung
			$this->switch_order();
			$this->switch_order_groups();

			// Gruppierung erstellen
			if ($this->checked->pfgruppeid == "new")
			{
				$this->create_new_group();
				$this->content->template['form'] = "nein";
				$this->content->template['new_group_in_work'] = 1;
			}

			// Gruppierung bearbeiten
			if (is_numeric($this->checked->grupid))
			{
				$this->change_group();
				$this->content->template['form'] = "nein";
			}
			// Feld erstellen oder Formular zur Erstellung anzeigen
			if ($this->checked->pffeldid == "new")
			{
				$this->content->template['altereintrag'] = "new"; // Meldung an die Feld-Typen-Templates zur Steuerung von Headline und welche Template-Datei
				$this->create_new_field();
				$this->content->template['form'] = "nein";
			}
			// Feld bearbeiten
			if (is_numeric($this->checked->feldid))
			{
				// F�r die 8 Systemfelder einer MV darf die Sprachen(un)abh�ngigkeit nicht ver�ndert werden
				/*if ($mv_art == "2")
				{
					$sql = sprintf("SELECT mvcform_name
											FROM %s
											WHERE mvcform_id = '%d'
											LIMIT 1",
											
											$this->cms->tbname['papoo_mvcform'],
											
											$this->db->escape($this->checked->feldid)
					);
					$mvcform_name = $this->db->get_var($sql);
					if ($mvcform_name == "Benutzername"
						OR $mvcform_name == "passwort"
						OR $mvcform_name == "email"
						OR $mvcform_name == "antwortmail"
						OR $mvcform_name == "newsletter"
						OR $mvcform_name == "board"
						OR $mvcform_name == "active"
						OR $mvcform_name == "signatur") $this->content->template['altereintrag'] = 1;
					else $this->content->template['altereintrag'] = "new_next"; // darf ge�ndert werden
				}
				else*/ $this->content->template['altereintrag'] = "new_next"; // Meldung an die Feld-Typen-Templates zur Steuerung von Headline und welche Template-Datei
				require(PAPOO_ABS_PFAD . '/plugins/mv/lib/change_field.php');
				$this->content->template['form'] = "nein";
			}
			// Pflichtfelder, Listenfelder oder normaler User Felder �ndern
			if ($this->checked->submitmust_or_list) $this->save_pflicht_und_list();
			// Liste der Gruppen und Felder des Formulares
			$this->get_form_group_field_list();
			$this->content->template['metaebene_auswahl'] = "nein";
		}

	}
	// Der Benutzer mit dieser userid darf nichts �ndern
	else
	{
		// ans Template weitergeben, das dieser User nix sehen darf
		$this->content->template['wrong_user_id'] = "ja";
		// Meta Gruppen aus der Datenbank holen
		$sql = sprintf("SELECT * FROM %s
									ORDER BY mv_meta_group_name",
									
									$this->cms->tbname['papoo_mv']
									. "_meta_"
									. $this->db->escape($this->checked->mv_id)
						);
		$meta_gruppen = $this->db->get_results($sql, ARRAY_A);
		// ans Template weitergeben
		$this->content->template['meta_gruppen'] = 			$meta_gruppen;
		$this->content->template['papoo_gruppen'] = 		$papoo_gruppen;
		$this->content->template['meta_papoo_gruppen'] = 	$meta_papoo_gruppen;
		$this->content->template['mv_id'] = 				$this->checked->mv_id;
		$this->content->template['metaebene'] = 			"nein";
		$this->content->template['link'] = 					$_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template;
	}
}

// Anzahl S�tze je Metagruppe z�hlen und ans Template �bergeben
// bei fehlerhaften Installationen kann der Wert bei Sprachwechsel falsch sein
foreach($meta_gruppen AS $key => $value)
{
	$sql = sprintf("SELECT count(*)
								FROM %s
								WHERE mv_meta_lp_mv_id = '%d'
								AND mv_meta_lp_meta_id = '%d'",
								
								$this->cms->tbname['papoo_mv_meta_lp'],
								
								$this->db->escape($this->checked->mv_id),
								$value['mv_meta_id']
					);
	$result = $this->db->get_var($sql);
	$this->content->template['meta_gruppen'][$key]['rec_count'] = $result;
}
?>