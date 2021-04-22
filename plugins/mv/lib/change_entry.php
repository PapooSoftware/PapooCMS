<?php
/**
* Verwaltung bearbeiten
* Ein Eintrag wird rausgeholt und bearbeitet und wieder eingetragen oder gelöscht
*
* called by mv.php only
*/
$this->make_lang();
$this->get_group_user();
$this->content->template['mv_art'] = $this->checked->mv_art;

// Es soll eingetragen werden
if ($this->checked->mv_submit
	&& $this->user_darf_mv_schreiben)
{
	// Änderungen des Verwaltungsnamen in den Menus updaten
	$sql = sprintf("SELECT mv_name_label FROM %s
												WHERE mv_id_id = '%d' 
												AND mv_lang_id = '%d'",
												$this->cms->tbname['papoo_mv_lang'],
												$this->db->escape($this->checked->mv_id),
												$this->db->escape($this->cms->lang_back_content_id)
					);
	$alter_name = $this->db->get_var($sql);
	// gab es überhaupt Änderungen?
	if ($alter_name != $this->checked->mv_name)
	{
		// menuid aus der Tabelle für die Verwaltung mit der ID mv_id holen
		$sql = sprintf("SELECT mv_menu_id FROM %s
												WHERE mv_id = '%d'",
												$this->cms->tbname['papoo_mv'],
												$this->db->escape($this->checked->mv_id)
						);
		$menu_id = $this->db->get_var($sql);
		// wenn ja, dann die Menus updaten
		$sql = sprintf("UPDATE %s SET menuname = '%s'
									WHERE menuid_id = '%d' 
									AND lang_id = '%d'",
									$this->cms->tbname['papoo_men_uint_language'],
									$this->db->escape($this->checked->mv_name),
									$this->db->escape($menu_id),
									$this->db->escape($this->cms->lang_back_content_id)
						);
		$this->db->query($sql);
	}
	// Änderungen Suchmaske und der Art (wobei nur die Nummer geändert wird, keine Mitgliederdaten in der user Tabelle oder so) speichern
	$sql = sprintf("UPDATE %s SET mv_set_suchmaske = '%d', 
									mv_art = '%d'
									WHERE mv_id = '%s' ",
									$this->cms->tbname['papoo_mv'],
									$this->db->escape($this->checked->mv_suchmaske),
									$this->db->escape($this->checked->mv_art),
									$this->db->escape($this->checked->mv_id)
					);
	$this->db->query($sql);
	$this->content->template['ausgabe'] = $this->content->template['plugin']['mv']['eintrag_geaendert'];
	$this->content->template['mv_art'] = $this->checked->mv_art;
	// Änderungen bei der Gruppenverknüpfung speichern
	$this->change_group_user();
	// Änderungen des Verwaltungsnamen speichern
	$sql = sprintf("UPDATE %s SET mv_name_label = '%s'
								WHERE mv_id_id = '%s' 
								AND mv_lang_id = '%d'",
								$this->cms->tbname['papoo_mv_lang'],
								$this->db->escape($this->checked->mv_name),
								$this->db->escape($this->checked->mv_id),
								$this->db->escape($this->cms->lang_back_content_id)
					);
	$this->db->query($sql);
}
$link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template;
$this->content->template['link'] = $link;
if (!empty($this->checked->mv_id))
{
	// Nach id und Inhaltssprache aus der Datenbank holen
	$sql = sprintf("SELECT * FROM %s, %s
									WHERE mv_id = mv_id_id 
									AND mv_id = '%s' 
									AND mv_lang_id = '%d' ",
									$this->cms->tbname['papoo_mv'],
									$this->cms->tbname['papoo_mv_lang'],
									$this->db->escape($this->checked->mv_id),
									$this->db->escape($this->cms->lang_back_content_id)
					);
	$result = $this->db->get_results($sql);
	if (!empty($result))
	{
// ist nur ein Treffer!
		foreach($result as $spalte)
		{
			$this->content->template['mv_id'] = $spalte->mv_id;
			$this->content->template['mv_name'] = $spalte->mv_name_label;
			$this->content->template['mv_suchmaske'] = $spalte->mv_set_suchmaske;
			$this->content->template['edit'] = "ok";
			$this->content->template['altereintrag'] = "ok";
			#$this->content->template['mv_link'] = "plugin.php?menuid=XXX&template=mv/templates/mv.html&mv_id=" . $spalte->mv_id;
			$this->content->template['mv_art'] = $spalte->mv_art;
		}
	}
}
else $this->get_form_list(); // Liste der Verwaltungen holen und ans Template an $list übergeben
// Soll  gelöscht werden?
#$this->mv_get_lese_schreibrechte_fuer_meta_ebene(); // ist bereits vor Aufruf erfolgt s. mv.php 270 und führt nur zum Fehler beim edit Verwaltung
if (!empty($this->checked->submitdelecht)
	&& $this->user_darf_mv_schreiben)
{
	$mv_id = $this->db->escape($this->checked->mv_id);
	#if ($mv_id != $this->checked->mv_id) return;
	global $db_praefix;
	foreach ($this->cms->tbname AS $key => $value)
	{
		$tb = substr($value, strlen($db_praefix)); // Präfix eleminieren
		$arr = explode("_", $tb);
		// Filter: Nur die zu dieser mv_id gehörenden Tabellen ermitteln
		if (($arr[0] == "papoo"
			 AND $arr[1] == "mv")
			 AND (
			 		(
						($arr[2] == "content"
						 OR $arr[2] == "mpg"
						 OR $arr[2] == "template"
						 OR $arr[2] == "meta"
				 		)
			 			AND $arr[3] == $mv_id
					)
					OR ($arr[2] == "meta" 
						AND $arr[3] == "lang" 
						AND $arr[4] == $mv_id
						)
				)
		   ) $tables[$key] = $key;
		
	}
	// $tables enthält nun die zu dieser mv_id zu löschenden Tabellennamen
	if (count($tables))
	{
		foreach ($tables as $key => $value)
		{
			$sql = sprintf("DROP TABLE IF EXISTS " . $this->cms->tbname[$tables[$key]]);
			$this->db->query($sql);
		}
	}
	
	// Die zu dieser mv_id relevanten Tabellen-Einträge entfernen
	// vor dem Löschen mv_menu_id sichern
	$sql = sprintf("SELECT mv_menu_id FROM %s
										WHERE mv_id = '%d'",
										$this->cms->tbname['papoo_mv'],
										$mv_id
					);
	$menu_id = $this->db->get_var($sql);
	
	$sql = sprintf("DELETE FROM %s
							WHERE mv_id = '%s'",
							$this->cms->tbname['papoo_mv'],
							$mv_id
				);
	$this->db->query($sql);
	// Vor Löschen der mvcform Daten sammeln, die nötig sind, um relevante Sätze aus der Tabelle mvcform_lang entfernen zu können
	$sql = sprintf("SELECT T2.* FROM %s T1
								INNER JOIN %s T2 ON (T1.mvcform_id = T2.mvcform_lang_id)
								WHERE T1.mvcform_form_id = '%d'
								GROUP BY T1.mvcform_id",
								$this->cms->tbname['papoo_mvcform'],
								$this->cms->tbname['papoo_mvcform_lang'],
								$mv_id
					);
	$for_mvcform_lang = $this->db->get_results($sql, ARRAY_A);
	// Löschen aus der Tabelle mvcform_lang. Sicherheitshalber die MetaID hinzunehmen.
	if (count($for_mvcform_lang))
	{
		foreach ($for_mvcform_lang as $key => $value)
		{
			$sql = sprintf("DELETE FROM %s
										WHERE mvcform_lang_id = '%d'
										AND mvcform_lang_meta_id = '%d'",
										$this->cms->tbname['papoo_mvcform_lang'],
										$value['mvcform_lang_id'],
										$value['mvcform_lang_meta_id']
							);
			$this->db->query($sql);
		}
	}
	$sql = sprintf("DELETE FROM %s
							WHERE mvcform_form_id = '%s'",
							$this->cms->tbname['papoo_mvcform'],
							$mv_id
				);
	$this->db->query($sql);
	$sql = sprintf("DELETE FROM %s
							WHERE mv_id = '%s'",
							$this->cms->tbname['papoo_mvcform_pflicht_lp'],
							$mv_id
				);
	$this->db->query($sql);
	$sql = sprintf("DELETE FROM %s
							WHERE mv_id = '%s'",
							$this->cms->tbname['papoo_mv_feld_preisintervall'],
							$mv_id
				);
	$this->db->query($sql);
	$sql = sprintf("DELETE FROM %s
							WHERE mv_id_id = '%s'",
							$this->cms->tbname['papoo_mv_lang'],
							$mv_id
				);
	$this->db->query($sql);
	$sql = sprintf("DELETE FROM %s
							WHERE mv_meta_lp_mv_id = '%s'",
							$this->cms->tbname['papoo_mv_meta_lp'],
							$mv_id
				);
	$this->db->query($sql);
	$sql = sprintf("DELETE FROM %s
							WHERE mv_meta_main_lp_mv_id = '%s'",
							$this->cms->tbname['papoo_mv_meta_main_lp'],
							$mv_id
				);
	$this->db->query($sql);
	$sql = sprintf("DELETE FROM %s
							WHERE mv_pro_mv_id = '%s'",
							$this->cms->tbname['papoo_mv_protokoll'],
							$mv_id
				);
	$this->db->query($sql);
	$sql = sprintf("SELECT mvcform_group_id FROM %s
								WHERE mvcform_group_form_id = '%d'
								ORDER BY mvcform_group_id",
								$this->cms->tbname['papoo_mvcform_group'],
								$mv_id
					);
	$result = $this->db->get_results($sql, ARRAY_A);
	$sql = sprintf("DELETE FROM %s
							WHERE mvcform_group_lang_id >= '%d'
							AND mvcform_group_lang_id <= '%d'",
							$this->cms->tbname['papoo_mvcform_group_lang'],
							$result[0]['mvcform_group_id'],
							$result[count($result) - 1]['mvcform_group_id']
				);
	$this->db->query($sql);
	$sql = sprintf("DELETE FROM %s
							WHERE mvcform_group_form_id = '%s'",
							$this->cms->tbname['papoo_mvcform_group'],
							$mv_id
				);
	$this->db->query($sql);
	
	// Menüeinträge zu dieser Verwaltung löschen
	$sql = sprintf("SELECT * FROM %s
									WHERE menulink LIKE '%s'",
									$this->cms->tbname['papoo_menuint'],
									"%mv_id="
									. $mv_id
									. "%"
					);
	$menus = $this->db->get_results($sql);
	if (count($menus))
	{
		foreach($menus as $menitem)
		{
			$sql = sprintf("DELETE FROM %s
										WHERE menuid = '%d'",
										$this->cms->tbname['papoo_menuint'],
										$this->db->escape($menitem->menuid)
							);
			$this->db->query($sql);
			$sql = sprintf("DELETE FROM %s
										WHERE menuid = '%d'",
										$this->cms->tbname['papoo_lookup_men_int'],
										$this->db->escape($menitem->menuid)
							);
			$this->db->query($sql);
			$sql = sprintf("DELETE FROM %s
										WHERE menuid_id = '%d'",
										$this->cms->tbname['papoo_men_uint_language'],
										$this->db->escape($menitem->menuid)
							);
			$this->db->query($sql);
		}
	}
	$sql = sprintf("SELECT * FROM %s
								WHERE plugin_papoo_id = '35'",
								$this->cms->tbname['papoo_plugins']
					);
	$result = $this->db->get_results($sql);
	$menu_main_id = substr($result[0]->plugin_menuids, 1, 5); // ACHTUNG klappt natürlich nur, wenn es dabei bleibt, dass plugin menuids zw. 1000 und 9999 sind
	$menuid_aktuell_save = $this->checked->menuid;
	// und die Menu Einträge löschen
	$this->intern_menu->menu_delete($menu_id, $menu_main_id); // kommt möglicherweise nicht zurück !!!
	
	$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&fertig=del";
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
	$this->content->template['mv_email'] = $this->checked->mv_email;
	$this->content->template['mv_id'] = $this->checked->mv_id;
	$this->content->template['fragedel'] = "ok";
	$this->content->template['edit'] = "";
}
?>