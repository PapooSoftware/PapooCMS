<?php
/**
#####################################
# Papoo CMS                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.3                  #
#####################################
 */

/**
 * Administration der "3. Spalte"
 *
 * Class intern_spalte_class
 */
class intern_spalte_class
{
	/** @var int ID der aktuellen Sprache */
	var $lang_aktu_id;

	/**
	 * intern_spalte_class constructor.
	 */
	function __construct()
	{
		// cms Klasse einbinden
		global $cms;
		$this->cms = & $cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		global $db_praefix;
		$this->db = & $db;
		$this->db_praefix = $db_praefix;
		// User Klasse einbinden
		global $user;
		$this->user = & $user;
		// inhalt Klasse einbinden
		global $content;
		$this->content = & $content;
		// Suchklasse einbinden
		global $searcher;
		$this->searcher = & $searcher;
		// checkedblen Klasse einbinde
		global $checked;
		$this->checked = & $checked;
		// interne Artikel KLasse einbinden
		global $intern_artikel;
		$this->intern_artikel = & $intern_artikel;
		// Ersetzungklasse
		global $replace;
		$this->replace = & $replace;
		// Diverse-Klasse einbinden
		global $diverse;
		$this->diverse = & $diverse;
		// Menü-Klasse einbinden
		global $menu;
		$this->menu = & $menu;

		//print_r($this->checked);

		IfNotSetNull($this->content->template['TEMPLATE_WEICHE']);
		IfNotSetNull($this->content->template['name']);
		IfNotSetNull($this->content->template['col3_id']);
		IfNotSetNull($this->content->template['Beschreibung']);
		IfNotSetNull($this->content->template['menulist_drin_data']);
		IfNotSetNull($this->content->template['col_menuid']);
	}

	/**
	 * @return void
	 * @desc Inhalte erstellen je nach Menupunkt
	 */
	function make_inhalt()
	{
		// Überprüfen ob Zugriff auf die Inhalte besteht
		$this->user->check_access();

		//Array init
		$this->content->template['ander_data']=array();
		$this->content->template['menlang']=array();

		// HACK für Rückumwandlung von <br>s im TinyMCE
		if (empty($this->checked->editor_name)) {
			$this->checked->editor_name = "";
		}
		if (empty($this->checked->inhalt_ar['inhalt'])) {
			$this->checked->inhalt_ar['inhalt'] = "";
		}
		if ($this->checked->editor_name == "tinymce") {
			$this->checked->inhalt_ar['inhalt'] = $this->diverse->recode_entity_n_br($this->checked->inhalt_ar['inhalt'], "nobr");
		}

		switch ($this->checked->menuid) {
		case "15":
			// Starttext Übergeben
			if (empty($this->checked->messageget)) {
				$this->content->template['text']= $this->content->template['message_40'];
			}
			elseif ($this->checked->messageget==42) {
				$this->content->template['text']= $this->content->template['message_40'].$this->content->template['message_42'];
			}
			elseif ($this->checked->messageget==48) {
				$this->content->template['text']= $this->content->template['message_40'].$this->content->template['message_48'];
			}
			break;

		case "16":
			// Sprache definieren und Links für Sprachwahl ausgeben
			$this->make_div_lang();
			// neuen Artikel erstellen
			$this->make_artikel();
			break;

		case "17":
			// Sprache definieren und Links für Sprachwahl ausgeben
			$this->make_div_lang();
			// Artikel ändern
			$this->change_artikel();
			break;
		}
	}

	/**
	 * @return void
	 * @desc Artikel erstellen
	 */
	function make_artikel()
	{
		IfNotSetNull($this->checked->action_save);
		// Neuen Artikel speichern
		if ($this->checked->action_save) {
			// 1. Daten aufbereiten
			// Wenn der Artikel bereits hochgeladen wurde ...
			if (empty($this->checked->inhalt_ar['inhalt'])) {
				$this->checked->inhalt_ar['inhalt']="";
			}

			// 2. Daten speichern
			$this->insert_data();
			$this->col_reorder();

			// 3. Weiterleitung
			//header ("Location: ./spalte.php?menuid=15&amp;messageget=42");
			$location_url = "./spalte.php?menuid=15&amp;messageget=42";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}
		else {
			// Editor Überprüfen und zuweisen
			$this->intern_artikel->check_editor();
			// Bilder rausfiltern und zuweisen
			$this->intern_artikel->get_images($this->lang_aktu_id);
			// Downloadbare Dokumente zuweisen
			$this->intern_artikel->get_downloads($this->lang_aktu_id);
			// CSS-Klassen für QuickTag-Editor einlesen
			$this->intern_artikel->get_css_klassen();
			// Auswahl-Liste Menüpunkte
			$this->content->template['menulist_data'] = $this->menu->menu_data_read("FRONT_PUBLISH", $this->lang_aktu_id);
			// Setzen für das Template, damit die richtigen Sachen angezeigt werden.
			$this->content->template['TEMPLATE_WEICHE']= 'new_edit';
		}
	}

	/**
	 *
	 */
	function insert_data()
	{
		// 1. Daten aufbereiten
		$this->make_format();
		if (!empty($this->checked->action_savecopy)) {
			$this->checked->col_name="Copy ".$this->checked->col_name;
		}
		// 2. Grund-Daten speichern
		$sql = sprintf("INSERT INTO %s SET gruppe_id='%s', col_menuid='%s' ",
			$this->cms->papoo_collum3,
			$this->db->escape($this->doklesengrid),
			$this->db->escape($this->checked->col_menuid)
		);
		$this->db->query($sql);
		$insertid = $this->db->insert_id;

		// 3. Sprach-Einträge speichern
		$sql = sprintf("INSERT INTO %s SET collum_id='%d', lang_id='%d', name='%s', article='%s', article_sans='%s'",
			$this->cms->papoo_language_collum3,
			$insertid,
			$this->db->escape($this->lang_aktu_id),
			$this->db->escape($this->checked->col_name),
			$this->db->escape($this->artikel),
			$this->db->escape($this->inhalt)
		);
		$this->db->query($sql);

		// 4. Menü-Zugehörigkeit speichern
		$this->menulookup_save($insertid);
	}

	/**
	 * @param int $col3_id
	 */
	function menulookup_save($col3_id = 0)
	{
		if ($col3_id) {
			// 1. alte Einträge löschen
			$sql = sprintf("DELETE FROM %s WHERE collum_col_id='%d'", $this->db_praefix."papoo_lookup_men_collum3", $col3_id);
			$this->db->query($sql);

			// 2. Prüfung ob Eintrag in Auswahl-Feld in Liste ist. Wenn nicht, Eintrag hinzufügen.
			if ($this->checked->untermenuzu > 0) {
				if (empty($this->checked->inhalt_ar['cattext_ar'])) {
					$this->checked->inhalt_ar['cattext_ar'] = array();
				}
				if (!in_array($this->checked->untermenuzu, $this->checked->inhalt_ar['cattext_ar'])) {
					$this->checked->inhalt_ar['cattext_ar'][] = $this->checked->untermenuzu;
				}
			}

			// 3. Einträge speichern
			if (!empty($this->checked->inhalt_ar['cattext_ar'])) {
				foreach ($this->checked->inhalt_ar['cattext_ar'] as $menu_id) {
					$sql = sprintf("INSERT INTO %s SET collum_col_id='%d', collum_men_id='%d'",
						$this->db_praefix."papoo_lookup_men_collum3",
						$col3_id, $menu_id
					);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * Artikel Daten ändern
	 *
	 * @return void
	 */
	function change_artikel()
	{
		$temp_did_action = false;

		// Sortieren
		if (!empty($this->checked->submitorder)) {
			$temp_did_action = true;
			$this->switch_order();
		}
		IfNotSetNull($this->checked->extern);
		if (is_numeric($this->checked->extern)) {
			$this->content->template['extern_menuid_collum']=	$this->checked->extern;
		}

		// Eintrag editieren
		if (!empty($this->checked->action_edit)) {
			$temp_did_action = true;

			// Editor Überprüfen und zuweisen
			$this->intern_artikel->check_editor();
			// Bilder rausfiltern und zuweisen
			$this->intern_artikel->get_images($this->lang_aktu_id);
			// Downloadbare Dokumente zuweisen
			$this->intern_artikel->get_downloads($this->lang_aktu_id);
			// CSS-Klassen für QuickTag-Editor einlesen
			$this->intern_artikel->get_css_klassen();
			// Auswahl-Liste Menüpunkte
			$this->content->template['menulist_data'] = $this->menu->menu_data_read("FRONT_PUBLISH", $this->lang_aktu_id);

			// Setzen für das Template, damit die richtigen Sachen angezeigt werden.
			$this->content->template['TEMPLATE_WEICHE']= 'new_edit';

			// Artikel aus der Datenbank fischen
			$selectrepo = sprintf("SELECT * FROM %s AS t1, %s AS t2 WHERE t2.collum_id='%d' AND t2.lang_id='%d' AND t2.collum_id=t1.inhalt_id",
				$this->db_praefix."papoo_collum3",
				$this->db_praefix."papoo_language_collum3",
				$this->checked->col3_id,
				$this->lang_aktu_id
			);
			$resultrepo = $this->db->get_results($selectrepo);

			// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
			IfNotSetNull($reportage);

			$this->content->template['reportage'] = $reportage;

			//MenüEinträge rausholen
			$this->show_colmen();

			// Daten für das Formular zuweisen
			if (!empty($resultrepo)) {
				foreach ($resultrepo as $row) {
					$artikel = $row->article_sans;
					// Pfad für die Bilder angleichen
					$artikel = str_ireplace("\./images", "../images", $artikel);
					$this->content->template['Beschreibung'] = "nodecode:".$artikel;
					$this->content->template['col3_id']= $row->inhalt_id;
					$this->content->template['name']= $row->name;
					$this->content->template['col_menuid'] = $row->col_menuid;
				}
			}
			else {
				$this->content->template['Beschreibung']= "";
				$selectrepo2 = "SELECT * FROM ".$this->cms->papoo_collum3.",".$this->cms->papoo_language_collum3."  WHERE inhalt_id = '$reportage' AND lang_id='".$this->lang_aktu_id."' ";
				$selectrepo2.=" AND collum_id=inhalt_id";
				$resultrepo2 = $this->db->get_results($selectrepo2);
				if (!empty($resultrepo2)) {
					foreach ($resultrepo2 as $row2) {
						$this->content->template['col3_id']= $row2->inhalt_id;
					}
				}
			}
		}
		// Normales Speichern
		IfNotSetNull($this->checked->col3_id);
		IfNotSetNull($this->checked->col_menuid);
		IfNotSetNull($this->checked->action_save);
		if ($this->checked->col3_id && $this->checked->action_save) {
			// 1. Daten aufbereiten
			$this->make_format();

			// 2. Grund-Daten speichern
			$sql = sprintf("UPDATE %s SET col_menuid='%s' WHERE inhalt_id='%d'",
				$this->db_praefix."papoo_collum3",
				$this->db->escape($this->checked->col_menuid),
				$this->db->escape($this->checked->col3_id)
			);
			$this->db->query($sql);

			// 3. Sprach-Einträge speichern
			// 3.a alten Eintrag löschen
			$sql = sprintf("DELETE FROM %s WHERE collum_id='%d' AND lang_id='%d'",
				$this->db_praefix."papoo_language_collum3",
				$this->checked->col3_id,
				$this->lang_aktu_id
			);
			$this->db->query($sql);
			// 3.b neuen Eintrag sepichern
			$sql = sprintf("INSERT INTO %s SET collum_id='%d', lang_id='%d', name='%s', article='%s', article_sans='%s'",
				$this->db_praefix."papoo_language_collum3",
				$this->db->escape($this->checked->col3_id),
				$this->db->escape($this->lang_aktu_id),
				$this->db->escape($this->checked->col_name),
				$this->db->escape($this->artikel),
				$this->db->escape($this->inhalt)
			);
			$this->db->query($sql);

			// 4. Menü-Zugehörigkeit speichern
			$this->menulookup_save($this->checked->col3_id);

			// 5. neu ordnen und Weiterleitung
			$this->col_reorder();
			//header ("Location: ./spalte.php?menuid=15&amp;messageget=42");
			//$location_url = "./spalte.php?menuid=17&amp;col3_id=".$this->checked->col3_id;
			if (is_numeric($this->checked->extern_menuid_collum)) {
				$location_url = "../index.php?menuid=".$this->checked->extern_menuid_collum;
			}
			else {
				$location_url = "./spalte.php?menuid=17";
			}

			//$location_url = "./spalte.php?menuid=17&amp;col3_id=".$this->checked->col3_id."&amp;lang_idx=".$this->lang_aktu_id;
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}

		// Als Kopie speichern
		if (!empty($this->checked->action_savecopy)) {
			$temp_did_action = true;

			$this->insert_data();
			$this->col_reorder();
			//header ("Location: ./spalte.php?menuid=15&amp;messageget=42");
			$location_url = "./spalte.php?menuid=17&amp;messageget=42";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}

		// Löschen Abfrage
		if (!empty($this->checked->action_delete)) {
			$temp_did_action = true;
			$this->content->template['TEMPLATE_WEICHE']= 'delete';
			$this->content->template['uberschrift'] = sprintf("Artikel Nr.%d",$this->checked->col3_id);
			$this->content->template['col3_id']= $this->checked->col3_id;
		}

		// echt löschen
		if (!empty($this->checked->action_do_delete)) {
			$temp_did_action = true;

			// Eintrag in Collum3-Tabelle löschen
			$sql = sprintf("DELETE FROM %s WHERE inhalt_id='%d'", $this->db_praefix."papoo_collum3", $this->db->escape($this->checked->col3_id));
			$this->db->query($sql);
			// Einträge in Sprach-Tabelle löschen
			$sql = sprintf("DELETE FROM %s WHERE collum_id='%d'", $this->db_praefix."papoo_language_collum3", $this->db->escape($this->checked->col3_id));
			$this->db->query($sql);
			// Einträge in Lookup-Menü-Tabelle löschen
			$sql = sprintf("DELETE FROM %s WHERE collum_col_id='%d'", $this->db_praefix."papoo_lookup_men_collum3", $this->db->escape($this->checked->col3_id));
			$this->db->query($sql);

			$this->col_reorder();

			//header ("Location: ./spalte.php?menuid=15&amp;messageget=48");
			$location_url = "./spalte.php?menuid=17&amp;messageget=48";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}

		// keine der obigen Aktionen... dann Liste anzeigen
		if (!$temp_did_action) {
			$this->content->template['TEMPLATE_WEICHE']= 'list';

			// Wenn Suche leer.. normale Liste ausgeben
			if (empty($this->checked->search)) {
				unset($this->checked->search);
				// FIXME mehr Resultate erlauben!!
				$sql = sprintf("SELECT * FROM %s AS t1, %s AS t2 WHERE lang_id='%d' AND t2.collum_id=t1.inhalt_id ORDER BY t1.col_order_id LIMIT 500 ",
					$this->db_praefix."papoo_collum3",
					$this->db_praefix."papoo_language_collum3",
					$this->lang_aktu_id
				);
				$this->result = $this->db->get_results($sql);
				$this->make_result();
			}
			// .. sonst Liste mit Suchtreffern ausgeben
			else {
				// Suchbegriff bereinigen
				$search=$this->clean($this->checked->search);
				if ($this->checked->formSubmit) {
					$sql = sprintf("SELECT * FROM %s AS t1, %s AS t2 WHERE t2.article LIKE '%%%s%%' AND t2.lang_id='%d' AND t2.collum_id=t1.inhalt_id",
						$this->db_praefix."papoo_collum3",
						$this->db_praefix."papoo_language_collum3",
						$this->db->escape($search),
						$this->lang_aktu_id
					);
					$this->result = $this->db->get_results($sql);
					$this->make_result();
				}
			}
		}

	}

	/**
	 * @param $search
	 * @return mixed|string
	 */
	function clean($search) {
		//$search auf unerlaubte Zeichen Überprüfen und evtl. bereinigen
		$search = trim($search);
		$remove = "<>'\"_%*\\";
		for ($i = 0; $i < strlen($remove); $i ++) {
			$search = str_replace(substr($remove, $i, 1), "", $search);
		}
		return $search;
	}

	/**
	 * Liste der verwendeten Menüeinträge rausholen
	 */
	function show_colmen()
	{
		$menu=$this->menu->menu_data_read("FRONT_PUBLISH", $this->lang_aktu_id);
		#print_r($menu);
		$liste=$this->get_liste_men($this->checked->col3_id);
		//Menüliste durchgehen
		$i=0;
		foreach ($menu as $men) {
			//Einträge aus col3 durchgehen
			if (!empty($liste)) {
				foreach ($liste as $dat) {
					//Wenn vorhanden id und name Übergeben
					if ($dat['collum_men_id']==$men['menuid']) {
						$newdat[$i]['menuid']=$men['menuid'];
						$newdat[$i]['menu_name']=$men['menuname'];
					}
				}
			}
			$i++;
		}
		IfNotSetNull($newdat);
		$this->content->template['menulist_drin_data'] = $newdat;
	}

	/**
	 * Liste der Menüpunkte aus col3
	 *
	 * @param int $col3_id
	 * @return array|void
	 */
	function get_liste_men($col3_id = 0)
	{
		if (empty($col3_id)) {
			$sql=sprintf("SELECT * FROM %s ", $this->cms->tbname['papoo_lookup_men_collum3']);
		}
		else {
			$sql=sprintf("SELECT * FROM %s WHERE collum_col_id='%s'",
				$this->cms->tbname['papoo_lookup_men_collum3'],
				$this->db->escape($col3_id)
			);
		}
		return $this->db->get_results($sql,ARRAY_A);
	}

	/**
	 * Alle Felder neu durchsortieren
	 */
	function col_reorder()
	{
		$sql=sprintf("SELECT * FROM %s ORDER BY col_order_id",
			$this->cms->tbname['papoo_collum3']
		);
		$result=$this->db->get_results($sql);
		$i=10;
		if (is_array($result)) {
			foreach ($result as $dat) {
				$sql=sprintf("UPDATE %s SET col_order_id='%s' WHERE inhalt_id='%s' LIMIT 1",
					$this->cms->tbname['papoo_collum3'],
					$i,
					$this->db->escape($dat->inhalt_id)
				);
				$this->db->query($sql);
				$i=$i+10;
			}
		}
		if (is_numeric($this->checked->extern_menuid_collum)) {
			$location_url = "../index.php?menuid=".$this->checked->extern_menuid_collum;
		}
		else {
			$location_url = "./spalte.php?menuid=17";
		}

		if (isset($_SESSION['debug_stopallredirect']) && $_SESSION['debug_stopallredirect']) {
			echo '<a href="'.$location_url.'">Weiter</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}


	/**
	 * Felder sortieren
	 */
	function switch_order()
	{
		if (!empty($this->checked->submitorder)) {
			if (is_numeric($this->checked->inhalt_id) && is_numeric($this->checked->col_order_id)) {
				// Order-ID des Vorgängers ermitteln
				$sql=sprintf("SELECT col_order_id FROM %s WHERE inhalt_id='%s'",
					$this->cms->tbname['papoo_collum3'],
					$this->db->escape($this->checked->inhalt_id)
				);
				$alt_order_id=$this->db->get_var($sql);

				// Order-ID des Vorgängers auf Order-ID des Nachfolgers setzen
				$sql=sprintf("UPDATE %s SET col_order_id='%s' WHERE col_order_id='%s' LIMIT 1",
					$this->cms->tbname['papoo_collum3'],
					$this->db->escape($alt_order_id),
					$this->db->escape($this->checked->col_order_id)
				);
				$this->db->query($sql);

				// Order-ID des Nachfolgers auf alte Order-ID des Vorgängers setzen
				$sql=sprintf("UPDATE %s SET col_order_id='%s' WHERE inhalt_id='%s' LIMIT 1",
					$this->cms->tbname['papoo_collum3'],
					$this->db->escape($this->checked->col_order_id),
					$this->db->escape($this->checked->inhalt_id)
				);
				$this->db->query($sql);
				$this->col_reorder();
			}
		}
	}


	/**
	 * Liste mit Links erstellen
	 *
	 * @return void
	 */
	function make_result()
	{
		//Wenn Ergebnis vorhanden
		if (!empty($this->result)) {
			//Durchloopen
			foreach ($this->result as $row) {
				// verkürzter Text
				$tueberschrift = substr(strip_tags($row->article),0,50);
				// id des Artikels
				$reporeID = $row->inhalt_id;
				if ($row->col_menuid==0) {
					$menu="Start/Home";
				}
				else {
					$sql=sprintf("SELECT menuname FROM %s WHERE menuid_id='%s' AND lang_id='%s'",
						$this->cms->tbname['papoo_menu_language'],
						$this->db->escape($row->col_menuid),
						$this->db->escape($this->cms->lang_id)
					);
					$menu=$this->db->get_var($sql);
				}
				//$textart = strip_tags($tueberschrift) . "....  <a href=\"spalte.php?menuid=17&amp;reportage=$reporeID\">" . strip_tags($link) . " ".$this->content->template['message_47']." </a><br /><br />";
				//$textart = $tueberschrift . '....  <a href="spalte.php?menuid=17&amp;reportage='.$reporeID.'">'.$link.' '.$this->content->template['message_47'].'</a>';
				$textart = ' <a href="spalte.php?menuid=17&amp;col3_id='.$reporeID.'&amp;action_edit=1">  '.$this->content->template['message_168'].": <strong>".$menu." </strong>- ".$tueberschrift ." - ".$this->content->template['message_47'].'</a>';

				// Arraydaten erzeugen
				if ($row->col_name==1) {
					$row->col_name=$textart;
				}
				//$row->col_name=' <a href="spalte.php?menuid=17&amp;reportage='.$reporeID.'"> '.$row->col_name." - ".$this->content->template['message_47'].'</a>';
				$row->col_name=' <a href="spalte.php?menuid=17&amp;col3_id='.$reporeID.'&amp;action_edit=1"> '.$row->name." - ".$this->content->template['message_47'].'</a>';
				array_push($this->content->template['ander_data'], array('name'=>$row->col_name,
						'kurztext'=>$tueberschrift,
						'col_order_id'=>$row->col_order_id,
						'inhalt_id'=>$row->inhalt_id,
						'cform_name' => ""
					)
				);
			}
		}
	}


	/**
	 * Alle nötigen Formatierungen durchführen
	 *
	 * @return void
	 */
	function make_format()
	{
		$artikel = $this->checked->inhalt_ar['inhalt'];

		if ($this->cms->mod_rewrite==2) {
			$artikel = str_ireplace("\.\./images", "/images", $artikel);
		}
		else {
			$artikel = str_ireplace("\.\./images", "./images", $artikel);
		}


		if (is_numeric($this->checked->untermenuzu)) {
			$this->menu=$this->checked->untermenuzu;
		}
		elseif ($this->checked->untermenuzu=="none") {
			$this->menu="0";
		}
		else {
			$this->menu="0";
		}

		$this->inhalt = $artikel;
		$artikel = $this->replace->do_replace($artikel);
		//$artikel = $this->diverse->do_pfadeanpassen($artikel);
		$this->artikel = $artikel;
	}


	/**
	 * Ausgabe von alt und title und longdesc in verschiedenen Sprachen
	 *
	 * @return void
	 */
	function make_div_lang()
	{
		// lang_id rausfinden
		if(!empty($_SESSION['langid_front'])) {
			$this->lang_aktu_id=$_SESSION['langid_front'];
		}
		else {
			$this->lang_aktu_id = $this->cms->lang_id;
		}

		// aktive Sprachen raussuchen
		//$eskuel="SELECT lang_id, lang_short, lang_long FROM ".$this->cms->papoo_name_language." WHERE more_lang='2' OR lang_short='".$this->cms->frontend_lang."' ";
		$eskuel="SELECT lang_id, lang_short, lang_long FROM ".$this->cms->papoo_name_language." WHERE more_lang='2'";
		$reslang=$this->db->get_results($eskuel);

		// Daten dazu raussuchen
		foreach ($reslang as $lan) {
			if ($lan->lang_id == $this->lang_aktu_id) {
				$this->content->template['active_language']=$lan->lang_id;
				$this->content->template['article']=$lan->lang_long;
				$this->content->template['tinymce_lang_id'] = $lan->lang_id;
				$this->content->template['tinymce_lang_short'] = $this->cms->lang_back_short;
			}
			// Daten für die Links
			array_push($this->content->template['menlang'], array('language' => $lan->lang_long, 'lang_id' => $lan->lang_id));
		}
		if(!isset($this->content->template['article'])) {
			$this->content->template['article'] = NULL;
		}
	}
}

$intern_spalte = new intern_spalte_class();