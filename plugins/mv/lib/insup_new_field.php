<?php
// Vor Aufruf m�ssen folgende Variablen gesetzt werden:
// $modus nicht definiert oder auf "update" oder "insert" 
/**
* Felderdaten in die Datenbank eintragen
*
* called by change_field.php, make_entry.php, mv.php
*/
if (!isset($modus)) $modus = "insert"; //default 
if ($modus == "insert")
{
	// holt alle Meta Ebene aus der Datenbank
	// Eintrag direkt in alle Ebenen eintragen
	$sql = sprintf("SELECT * FROM %s",
							$this->cms->tbname['papoo_mv']
							. "_meta_"
							. $this->db->escape($this->checked->mv_id)
					);
	$meta_gruppen = $this->db->get_results($sql, ARRAY_A);
}
else
{
	$sql = sprintf("SELECT * FROM %s 
	  							WHERE mv_meta_id = '%d'",
								
								$this->cms->tbname['papoo_mv']
								. "_meta_"
								. $this->db->escape($this->checked->mv_id),
								
								$this->db->escape($this->meta_gruppe)
					);
	$meta_gruppen = $this->db->get_results($sql, ARRAY_A);
}
// max id + 1
$sql = sprintf("SELECT MAX(mvcform_id) FROM %s",
						$this->cms->tbname['papoo_mvcform']
				);
$max_mvcform_id = $this->db->get_var($sql);
$max_mvcform_id++;
if (!empty($meta_gruppen)) // gibt es Metaebenenen?
{
	// Loope die einzelnen Meta Ebenen durch
	foreach($meta_gruppen as $meta_gruppe)
	{
		if ($modus == "insert")
		{
			$ins1 = "INSERT INTO";
			$ins2 = "INSERT INTO";
			$ins3 = "INSERT INTO";
			$up1 = "";
			$up2 = "";
			$up3 = "";
			$insert_mvcform_id = "mvcform_id='" . $this->db->escape($max_mvcform_id) . "', ";
			$insertid_2 = $max_mvcform_id;
			$insup_feldname = "mvcform_name='" . $this->db->escape($this->checked->mvcform_name) . "', ";
		}
		if ($modus == "update")
		{
			$ins1 = "UPDATE";
			$up1 = "WHERE mvcform_id='"
					. $this->db->escape($this->checked->mvcform_id)
					. "' AND mvcform_meta_id='"
					. $this->db->escape($meta_gruppe['mv_meta_id'])
					. "'";
			$ins2 = "UPDATE";
			$ins3 = "UPDATE";
			$up3  = "WHERE mv_id='"
					. $this->db->escape($this->checked->mv_id)
					. "' AND feld_id='"
					. $this->db->escape($this->checked->mvcform_id)
					. "'";
			$insup_feldname = "";
			$insertid_2 = $this->db->escape($this->checked->mvcform_id);
			// wenn Update, dann alten Gruppen Id Wert f�r dieses Feld aus der Datenbank holen und mit neuen Gruppen id vergleichen
			// wenn sie sich unterscheiden sollten, dann die Feld Order Id aus min stellen, damit das verschobene Feld unten in der neuen Gruppe erscheint
			// und einmal sortieren dr�ber jagen
			$sql = sprintf("SELECT mvcform_group_id
									FROM %s 
									WHERE mvcform_id = '%d' 
									AND mvcform_meta_id = '%d'",
									
									$this->cms->tbname['papoo_mvcform'],
									
									$this->db->escape($this->checked->mvcform_id),
									$this->db->escape($meta_gruppe['mv_meta_id'])
							);
			$mvcform_group_id = $this->db->get_var($sql);
			$min_order_id = "";
			// neue Gruppe f�r dieses Feld? wenn ja dann...
			if ($mvcform_group_id != $this->checked->mvcform_group_id)
			{
				//dann hole die max Feld Order Id f�r diese Metagrupee und Verwaltung aus der Datenbank
				$sql = sprintf("SELECT MIN(mvcform_order_id) FROM %s 
															WHERE mvcform_meta_id = '%d' 
															AND mvcform_form_id = '%d'",
															
															$this->cms->tbname['papoo_mvcform'],
															
															$this->db->escape($this->meta_gruppe),
															$this->db->escape($meta_gruppe['mv_meta_id'])
								);
				$min_order_id = $this->db->get_var($sql);
				$min_order_id--;
				$min_order_id_sql = " mvcform_order_id='" . $this->db->escape($min_order_id) . "', ";
			}
		}
		// wenns ein Admin ist, dann darf er den Pflichtfeldeintrag f�r Admins auch editieren, ansonsten alten Wert wieder einsetzen
		if ($this->content->template['is_admin'] != "ok")
		{
			$sql = sprintf("SELECT mvcform_admin_must FROM %s 
														WHERE mvcform_id = '%d'",
														
														$this->cms->tbname['papoo_mvcform'],
														
														$this->db->escape($this->checked->mvcform_id)
							);
			$this->checked->mvcform_admin_must = $this->db->get_var($sql);
		}
	
		// wenn es seine eigene Meta Gruppe ist, dann darf das Feld auch gleich aktiv sein
		if ($this->meta_gruppe == $meta_gruppe['mv_meta_id']) $mvcform_aktiv = $this->db->escape($this->checked->mvcform_aktiv);
		else $mvcform_aktiv = "0";
		if (!empty($this->checked->mvcform_admin_must)) $this->checked->mvcform_must_back = $this->checked->mvcform_must = 1;
		// Beim Update kommt kein Wert f�r mvcform_lang_dependence. DB-Inhalt daf�r soll beibehalten werden.
		#$mvcform_lang_dependence = ($ins1 == "UPDATE") ? "" : ", mvcform_lang_dependence='" . $this->db->escape($this->checked->mvcform_lang_dependence) . "' ";
		// Normale Daten eintragen
		$sql = sprintf("%s %s SET %s %s 
									mvcform_meta_id = '%d',
									mvcform_group_id = '%d',
									%s
									mvcform_no_group = '0',
									mvcform_must = '%d',
									mvcform_must_back = '%d',
									mvcform_size = '%d',
									mvcform_form_id = '%d',
									mvcform_type = '%s',
									mvcform_content_type = '%s',
									mvcform_minlaeng = '%d',
									mvcform_maxlaeng = '%d',
									mvcform_required_feld = '%s',
									mvcform_default_wert = '%d',
									mvcform_list = '%d',
									mvcform_list_front = '%d',
									mvcform_normaler_user = '%d',
									mvcform_protokoll = '%d',
									mvcform_search = '%d',
									mvcform_search_back = '%d',							
									mvcform_kalender = '%d',
									mvcform_admin_must = '%d',
									mvcform_aktiv = '%d',
									mvcform_name_export = '%s',
									mvcform_lang_dependence = '%d',
									mvcform_flex_id = '%d',
									mvcform_flex_feld_id ='%d'
									%s",
									$ins1,
									
									$this->cms->tbname['papoo_mvcform'],
									
									$insert_mvcform_id,
									$min_order_id_sql,
									$this->db->escape($meta_gruppe['mv_meta_id']),
									$this->db->escape($this->checked->mvcform_group_id),
									$insup_feldname,
									$this->db->escape($this->checked->mvcform_must),
									$this->db->escape($this->checked->mvcform_must_back),
									$this->db->escape($this->checked->mvcform_size),
									$this->db->escape($this->checked->mv_id),
									$this->db->escape($this->checked->mvcform_type),
									$this->db->escape($this->checked->mvcform_content_type),
									$this->db->escape($this->checked->mvcform_minlaeng),
									$this->db->escape($this->checked->mvcform_maxlaeng),
									$this->db->escape($this->checked->mvcform_required_feld),
									$this->db->escape($this->checked->mvcform_default_wert),
									$this->db->escape($this->checked->mvcform_list),
									$this->db->escape($this->checked->mvcform_list_front),
									$this->db->escape($this->checked->mvcform_normaler_user),
									$this->db->escape($this->checked->mvcform_protokoll),
									$this->db->escape($this->checked->mvcform_search),
									$this->db->escape($this->checked->mvcform_search_back),
									$this->db->escape($this->checked->mvcform_kalender),
									$this->db->escape($this->checked->mvcform_admin_must),
									$mvcform_aktiv,
									$this->db->escape($this->checked->mvcform_name_export),
									$this->db->escape($this->checked->mvcform_lang_dependence),
									$this->db->escape($this->checked->mvcform_flex_id),
									$this->db->escape($this->checked->mvcform_flex_feld_id),

									$up1
					);
		#print_r($sql);
		#exit();
		$insert_is_ok = false;
		if (($modus == "update" && $this->meta_gruppe == $meta_gruppe['mv_meta_id'])
			|| ($modus == "update" && $this->meta_gruppe == "1" && $this->have_admin_rights()))
		{
			$this->db->query($sql);
			$insert_is_ok = true;
		}
		if ($modus == "insert")
		{
			$this->db->query($sql);
			$insert_is_ok = true;
		}
		if ($insert_is_ok)
		{
			// mehrere Pflichtfelder erben
			$felder_daten = $this->get_spalten_namen();
			if (!empty($felder_daten))
			{
				$sql = sprintf("DELETE FROM %s
			 							WHERE mv_id = '%d'
										AND meta_id = '%d'
										AND feld_id = '%d'",
										
										$this->cms->tbname['papoo_mvcform_pflicht_lp'],
										
										$this->db->escape($this->checked->mv_id),
										$this->db->escape($meta_gruppe['mv_meta_id']),
										$this->db->escape($insertid_2)
								);
				$this->db->query($sql);
				foreach($felder_daten as $feld_daten)
				{
					$checked_name = "hiddenmvcform_required_felder_" . $feld_daten['mvcform_id'];
					if ($this->checked->$checked_name == "1")
					{
						$sql = sprintf("INSERT INTO %s SET
														mv_id = '%d',
														meta_id = '%d',
														feld_id = '%d',
														pflicht_feld_id = '%d'",
														
														$this->cms->tbname['papoo_mvcform_pflicht_lp'],
														
														$this->db->escape($this->checked->mv_id),
														$this->db->escape($meta_gruppe['mv_meta_id']),
														$this->db->escape($insertid_2),
														$this->db->escape($feld_daten['mvcform_id']));
						$this->db->query($sql);
					}
				}
			}
		}
		// Neu sortieren
		$this->reorder_fields($this->db->escape($this->checked->mvcform_group_id), $meta_gruppe['mv_meta_id']);
		// Sprachdatei neu eintragen
		if (is_array($this->checked->mvcform_content_list))
		{
			$save_mvcform_content_list = $this->checked->mvcform_content_list;
			$this->checked->mvcform_content_list = "";
		}
		else
		{
			unset($save_mvcform_content_list);
			$this->checked->mvcform_content_list = trim($this->checked->mvcform_content_list);
		}
		if ($modus == "update")
			$up2 = " WHERE `mvcform_lang_id`="
					. $insertid_2
					. " AND `mvcform_lang_lang`="
					. $this->db->escape($this->cms->lang_back_content_id)
					. " AND `mvcform_lang_meta_id`="
					. $meta_gruppe['mv_meta_id'];
		else $up2 = "";
		$sql = sprintf("%s %s SET mvcform_lang_id = '%s',
									mvcform_lang_lang = '%s',
									mvcform_label = '%s',
									mvcform_content_list = '%s',
									mvcform_descrip = '%s',
									mvcform_lang_header = '%s',
									mvcform_lang_tooltip = '%s',
									mvcform_lang_meta_id = '%d'
									%s",
									$ins2,
									
									$this->cms->tbname['papoo_mvcform_lang'],
									
									$this->db->escape($insertid_2),
									$this->db->escape($this->cms->lang_back_content_id),
									$this->db->escape($this->checked->mvcform_label),
									$this->db->escape($this->checked->mvcform_content_list),
									$this->db->escape($this->checked->mvcform_descrip),
									"",
									$this->db->escape($this->checked->mvcform_lang_tooltip),
									$this->db->escape($meta_gruppe['mv_meta_id']),
									$up2
					);
	
		if (($modus == "update" && $this->meta_gruppe == $meta_gruppe['mv_meta_id'])
			|| ($modus == "update" && $this->meta_gruppe == "1" && $this->have_admin_rights())) $this->db->query($sql);
		if ($modus == "insert") $this->db->query($sql);
		// un�tige Whitespaces raus
		$this->checked->mvcform_preisintervall_list = trim($this->checked->mvcform_preisintervall_list);
		// Preisintervalle neu eintragen
		$sql = sprintf("%s %s SET mv_id = '%d',
									feld_id = '%d',
									intervalle = '%s',
				  					waehrung = '%s'
									%s",
									$ins3,
									
									$this->cms->tbname['papoo_mv_feld_preisintervall'],
									
									$this->db->escape($this->checked->mv_id),
									$this->db->escape($insertid_2),
									$this->db->escape($this->checked->mvcform_preisintervall_list),
									$this->db->escape($this->checked->mvcform_preisintervall_waehrung),
									$up3
						);	
		$this->db->query($sql);
		// Wenn neues Feld in der Standardsprache definiert wurde, dann in allen Sprachen das Feld anlegen
		if ($modus == "insert")
		{
			$sql = sprintf("SELECT * FROM %s
										WHERE mv_lang_id <> '%d'",
										
										$this->cms->tbname['papoo_mv_name_language'],
										
										$this->db->escape($this->cms->lang_back_content_id)
							);
			$sprachen = $this->db->get_results($sql);
			if (!empty($sprachen))
			{
				foreach($sprachen as $sprache)
				{
					// check Feld mvcform_content_list in dieser Tabelle: Ist vermutlich �berfl�ssig
					// Beim Edit ist das Feld danach leer
					$sql = sprintf("%s %s SET mvcform_lang_id = '%s',
												mvcform_lang_lang = '%s',
												mvcform_label = '%s',
												mvcform_content_list = '%s',
												mvcform_descrip = '%s',
												mvcform_lang_header = '%s',
												mvcform_lang_tooltip = '%s',
												mvcform_lang_meta_id = '%d'
												%s",
												$ins2,
												
												$this->cms->tbname['papoo_mvcform_lang'],
												
												$this->db->escape($insertid_2),
												$this->db->escape($sprache->mv_lang_id),
												$this->db->escape($this->checked->mvcform_label),
												$this->db->escape($this->checked->mvcform_content_list),
												$this->db->escape($this->checked->mvcform_descrip),
												"",
												$this->db->escape($this->checked->mvcform_lang_tooltip),
												$this->db->escape($meta_gruppe['mv_meta_id']),
												$up2
									);
					$this->db->query($sql);
				}
			}
		}
	}
	if (is_array($save_mvcform_content_list))
	{
		$this->checked->mvcform_content_list = $save_mvcform_content_list;
		unset($save_mvcform_content_list);
	}
}
?>