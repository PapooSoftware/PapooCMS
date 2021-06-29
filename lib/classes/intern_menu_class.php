<?php

/**
#####################################
# Papoo CMS                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.2                  #
#####################################
 */

/**
 * Class intern_menu_class
 */
class intern_menu_class
{
	/**
	 * intern_menu_class constructor.
	 */
	function __construct()
	{
		// Globale Klassen als Referenzen einbinden
		// ****************************************
		// cms Klasse einbinden
		global $cms;
		$this->cms = &$cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = &$db;
		global $db_abs;
		$this->db_abs = &$db_abs;

		// Messages einbinden
		global $message;
		$this->message = &$message;
		// User Klasse einbinden
		global $user;
		$this->user = &$user;
		// inhalt Klasse einbinden
		global $content;
		$this->content = &$content;
		// checked Klasse einbinde
		global $checked;
		$this->checked = &$checked;

		// Ersetzungsklasse einbinden
		global $replace;
		$this->replace = &$replace;
		// Menü-Klasse einbinden
		global $menu;
		$this->menu = &$menu;
		// Diverse-Klasse einbinden
		global $diverse;
		$this->diverse = &$diverse;

		IfNotSetNull($this->content->template['is_inserted']);
		IfNotSetNull($this->content->template['error_menu_leer']);
		IfNotSetNull($this->content->template['is_start']);
		IfNotSetNull($this->content->template['menu_subteaser']);
		IfNotSetNull($this->content->template['extra_css_file']);
		IfNotSetNull($this->content->template['extra_css_sub_checked']);
		IfNotSetNull($this->content->template['artikel_sort']);
		IfNotSetNull($this->content->template['menu_spez_layout']);
		IfNotSetNull($this->content->template['formlink']);
		IfNotSetNull($this->content->template['nl']);
	}

	/**
	 * Aktions-Weiche für Menü-Punkte
	 */
	function make_inhalt()
	{
		// Überprüfen ob Zugriff auf die Inhalte besteht
		$this->user->check_access();

		//Checken ob Veröffentlichung gesetz
		$this->check_publish_all();

		switch ($this->checked->menuid) {
		case "14" :
			// Starttext Übergeben
			$this->menu_set_messages();
			break;

		case "43" :
		case "44" :
			// Menü-Punkt erstellen oder bearbeiten
			$this->menu_set_messages2();
			$this->switch_menu();
			break;

		case "46" :
			// Reihenfolge bearbeiten
			$_SESSION['dbp'] = array();
			$this->switch_order();
			break;
		}
	}

	/**
	 *
	 */
	private function check_publish_all()
	{
		$oldCsrfState = $this->db->csrfok;
		$this->db->csrfok = true;

		//Check ob umgestellt
		$xsql = array();
		$xsql['dbname'] = "papoo_menu_language";
		$xsql['select_felder'] = array("menuid_id");
		$xsql['limit'] = "";
		$xsql['where_data'] = array("publish_yn_lang_men" => 1);
		$result = $this->db_abs->select($xsql);
		if (count($result) < 4) {
			//Alle durchloopen und auf veröffentlichen setzen
			$xsql = array();
			$xsql['dbname'] = "papoo_me_nu";
			$xsql['select_felder'] = array("menuid");
			$xsql['limit'] = "";
			$xsql['where_data_like'] = "LIKE";
			$xsql['where_data'] = array("menuid" => "%%");
			$result = $this->db_abs->select($xsql);

			if (is_array($result)) {
				foreach ($result as $k => $v) {
					$sql = sprintf(
						'UPDATE %1$s SET publish_yn_lang_men=\'1\' WHERE menuid_id=\'%2$d\'',
						DB_PRAEFIX . "papoo_menu_language",
						$v['menuid']
					);
					$this->db->query($sql);
				}
			}
		}

		$this->db->csrfok = $oldCsrfState;
	}

	/**
	 *
	 */
	function menu_set_messages()
	{
		if (empty ($this->checked->messageget)) {
			$this->content->template['text'] = $this->content->template['message_30'];
		}
		elseif ($this->checked->messageget == 31) {
			$this->content->template['text'] =
				$this->content->template['message_30'].$this->content->template['message_31'];
		}
		elseif ($this->checked->messageget == 34) {
			$this->content->template['text'] =
				$this->content->template['message_30'].$this->content->template['message_34'];
		}
	}

	/**
	 *
	 */
	function menu_set_messages2()
	{
		IfNotSetNull($this->checked->messageget);
		if ($this->checked->messageget == 31) {
			$this->content->template['is_inserted'] = $this->content->template['system_mennu_eingetragen'];
		}
	}

	/**
	 * Verzeichnisse rauskriegen bzw. Menünamen für mod_rewrite mit sprechurls
	 * @param int $menid
	 * @return array|void
	 */
	function get_verzeichnis($menid = 0)
	{
		if (!empty($menid)) {
			$mendata=$this->menu->data_front_complete;
			foreach ($mendata as $menuitems) {
				//aktuelle menid finden
				if ($menid==$menuitems['menuid']){
					$this->m_url[]=($menuitems['url_menuname']);
					//Nicht oberste Ebene, neu aufrufen
					if ($menuitems['untermenuzu'] != 0) {
						$this->get_verzeichnis($menuitems['untermenuzu']);
					}
					else {
						return $this->m_url;
					}
				}
			}
		}
	}

	/**
	 * Unter-Weiche für Menü-Punkte neu und bearbeiten
	 * @return boolean|void
	 */
	function switch_menu()
	{
		IfNotSetNull($this->menu->ebenen_shift_ende);
		// Es wurde schon was Übertragen/ausgewählt
		if (
			!empty($this->checked->selmenuid)
			OR !empty($this->checked->formSubmit)
			OR !empty($this->checked->loeschen)
			OR !empty($this->checked->do_loeschen)
		) {
			// Menü-Punkt wurde aus Liste zum bearbeiten ausgewählt
			if (!empty($this->checked->selmenuid) AND empty($this->checked->formSubmit)) {
				$this->menu_preset_edit($this->checked->selmenuid);
				// Liste der Menü-Punkte zum Menü-Punkt bearbeiten erstellen
				$this->menu->data_front_publish =$this->mak_menu_liste_zugriff();

				$this->content->template['menulist_data'] = $this->menu->menu_navigation_build("FRONT_PUBLISH", "CLEAN");

				$this->content->template['menulist_ebenen_shift_ende'] = $this->menu->ebenen_shift_ende;
			}

			// Formular wurde Übertragen
			if (!empty($this->checked->formSubmit)) {
				// !!! Daten-löschen !!!
				if ($this->menu_check()) {
					$this->menu_save($this->checked->formmodus);
					$_SESSION['dbp']['menuok'] = 0;
				}
				else {
					$this->menu_preset_fehler();
				}
			}
			// Abfrage ob Daten gelöscht werden sollen
			if (!empty($this->checked->loeschen)) {
				$this->menu_preset_delete();
			}

			// Daten löschen
			if (!empty($this->checked->do_loeschen)) {
				$this->menu_delete($this->checked->delmenuid, $this->checked->deluntermenuzu_org);
				$_SESSION['dbp']['menuok'] = 0;
			}
			if ($this->cms->categories != 1) {
				$this->content->template['no_categories'] = 0;
			}
		}
		else {
			switch ($this->checked->menuid) {
			case 43:
				// Neuen Menü-Punkt erstellen
				$this->menu->data_front_publish =$this->mak_menu_liste_zugriff();
				$this->menu_preset_neu();
				break;

			case 44:
				$this->menu->data_front_publish =$this->mak_menu_liste_zugriff();
				$this->content->template['templateweiche'] = "LISTE";
				if ($this->cms->categories == 1) {
					// Kategorien rausholen
					$this->menu->preset_categories();
					$this->menu->get_menu_cats();

				}
				//Zusammenbauen und die ohne rechte entsprechend triggern
				$this->content->template['menulist_data'] = $this->menu->menu_navigation_build("FRONT_PUBLISH", "CLEAN");

				$this->content->template['menulist_ebenen_shift_ende'] = $this->menu->ebenen_shift_ende;
				if ($this->cms->categories == 1) {
					$this->menu->get_menu_cats();
					$this->menu->make_menucats();
					// Kategorien rausholen
					$this->menu->preset_categories();
					$this->content->template['catlist_data']=$this->menu->catlist_data;
					$this->menu->get_menu_cats();
					$this->content->template['categories'] = 1;
				}
				else {
					// else we have more than 1 category, effectively repeating menu lists etc.
					$this->content->template['catlist_data'] = array(0 => array("cat_id"=>-1));
					$this->content->template['no_categories'] = 1;
				}
				break;

			default:
				return false;
			}
		}
	}

	/**
	 * @return array
	 */
	function mak_menu_liste_zugriff()
	{
		foreach($this->content->template['languageget'] as $language) {
			if($language['lang_short'] === $this->cms->lang_frontend) {
				$active_lang_id = $language['lang_id'];
			}
		}

		//Zuerst mal die Standardsprache
		$data_front_complete_default = $this->menu->menu_data_read("FRONT", $active_lang_id);

		//Dann in der gewählten Sprachen
		$this->menu->data_front_complete = $this->menu->menu_data_read("FRONT");

		$neu_men = array();

		//Dann Standardsprache durchlaufen
		if (is_array($data_front_complete_default)) {
			foreach ($data_front_complete_default as $v) {
				if (is_array($this->menu->data_front_complete)) {
					$is_in_sprache = "";
					$is_in_sprache_dat = array();
					foreach ($this->menu->data_front_complete as $key => $value) {
						if ($v['menuid'] == $value['menuid']) {
							$is_in_sprache = "k";
							$is_in_sprache_dat = $value;
						}
					}
				}

				IfNotSetNull($is_in_sprache);
				IfNotSetNull($is_in_sprache_dat);
				if ($is_in_sprache == "k") {
					$neu_men[] = $is_in_sprache_dat;
				}
				else {
					$v['menuname'] = $v['menuname'] . " (" . $this->cms->lang_frontend . ")";
					$neu_men[] = $v;
				}
			}
		}

		$this->menu->data_front_complete = $neu_men;

		$this->menu->data_front_publish = $this->menu->menu_data_read("FRONT_PUBLISH");

		if (is_array($this->menu->data_front_complete)) {
			$ii = 0;
			foreach ($this->menu->data_front_complete as $value) {
				if (is_array($this->menu->data_front_publish)) {
					foreach ($this->menu->data_front_publish as $value1) {
						//Wenn Rechte
						if ($value['menuid'] == $value1['menuid']) {
							$this->menu->data_front_complete[$ii]['has_rights'] = 1;
						}
					}
				}
				$ii++;
			}
		}
		return $this->menu->data_front_complete;
	}

	/**
	 * Kategorien rausholen
	 */
	function preset_categories()
	{
		if (!empty($_SESSION['langid_front'])) {
			$sprach_id = $_SESSION['langid_front'];
		}
		else {
			$sprach_id = $this->cms->lang_id;
		}
		$sql = sprintf(
			'SELECT * FROM %1$s, %2$s WHERE cat_id=cat_lang_id AND cat_lang_lang=\'%3$s\'',
			$this->cms->tbname['papoo_category_lang'],
			$this->cms->tbname['papoo_category'],
			$this->db->escape($sprach_id)
		);
		$result=$this->db->get_results($sql, ARRAY_A);
		$this->content->template['catlist_data'] = $result;
	}

	/**
	 * Setzt Werte für neuen Menü-Punkt
	 */
	function menu_preset_neu()
	{
		$this->content->template['templateweiche'] = "NEU_EDIT";
		$this->content->template['formmodus'] = "NEU";
		$this->content->template['formuntermenuzu_org'] = 0;
		$this->content->template['formuntermenuzu'] = 0;
		$this->content->template['formorder_id_org'] = -1;
		$this->content->template['formmenuid'] = 0;
		$this->content->template['zeigemenu'] = "ok";

		// Liste "Untermenü zu" aufbauen
		$this->content->template['menulist_data'] = $this->menu->menu_navigation_build("FRONT_PUBLISH", "CLEAN");

		$this->menu_preset_layouts();

		// Sprachen und Rechte setzen
		$this->menu_preset_sprachen();
		$this->menu_preset_rechte();
		$this->preset_categories();
	}

	/**
	 * Setzt Werte zum ändern eines Menü-Punktes (holt Daten des Menü-Punktes $menuid aus der Datenbank)
	 * @param $menuid
	 * @return bool|void
	 */
	function menu_preset_edit($menuid)
	{
		//Bilder einbinden
		global $intern_image;
		$intern_image->change_liste();

		$this->content->template['templateweiche'] = "NEU_EDIT";
		$this->content->template['formmodus'] = "EDIT";

		$this->preset_categories();
		if ($this->user->userid != 10) {
			$sql = sprintf(
				'SELECT %1$s.menuid
				FROM %1$s,%2$s,%3$s
				WHERE 
					%1$s.menuid = \'%4$d\' AND %1$s.menuid = %5$s.menuid AND %5$s.gruppenid = %3$s.gruppenid 
					AND userid = \'%6$d\'
				LIMIT 1;',
				$this->cms->papoo_menu,
				$this->cms->papoo_lookup_men,
				$this->cms->tbname['papoo_lookup_ug'],
				$this->db->escape($menuid),
				$this->cms->papoo_lookup_men,
				$this->user->userid
			);
			$menupunktvar = $this->db->get_var($sql);
			if (is_numeric($menupunktvar)) {
				$this->content->template['zeigemenu'] = "ok";
			}
		}
		else {
			$this->content->template['zeigemenu'] = "ok";
		}

		// Informationen dieses Menü-Punktes laden
		$sql = sprintf(
			'SELECT * FROM %1$s WHERE menuid=\'%2$d\' LIMIT 1;',
			$this->cms->papoo_menu,
			$this->db->escape($menuid)
		);
		$menupunkt = $this->db->get_results($sql, ARRAY_A);

		if (!empty($menupunkt)) {
			$menupunkt = $menupunkt[0];

			$untermenuzu = $menupunkt['untermenuzu'];
			//menukat
			$sql = sprintf(
				'SELECT cat_cid FROM %1$s WHERE menuid_cid=\'%2$s\';',
				$this->cms->tbname['papoo_lookup_mencat'],
				$this->db->escape($menupunkt['menuid'])
			);
			$cat_id=$this->db->get_var($sql);
			$this->content->template['menukat'] = $cat_id;
			$this->content->template['formuntermenuzu_org'] = $untermenuzu;
			$this->content->template['formuntermenuzu'] = $untermenuzu;

			$this->content->template['formorder_id_org'] = $menupunkt['order_id'];
			$this->content->template['formmenuid'] = $menupunkt['menuid'];
			$this->content->template['formlink'] = $menupunkt['menulink'];
			$this->content->template['menu_image'] = $menupunkt['menu_image'];
			$this->content->template['menu_subteaser'] = $menupunkt['menu_subteaser'];
			$this->content->template['menu_spez_layout'] = $menupunkt['menu_spez_layout'];
			$this->content->template['artikel_sort'] = $menupunkt['artikel_sort'];
			$this->content->template['extra_css_file'] = $menupunkt['extra_css_file'];
			if ($menupunkt['extra_css_sub'] == 1) {
				$this->content->template['extra_css_sub_checked'] = "nodecode:checked";
			}
		}
		else return false;

		// Liste "Ist Untermenü von.." nur anzeigen wenn nicht Startseite
		$this->content->template['is_start'] = false;
		if ($menuid != 1) {
			$this->content->template['menulist_data'] = $this->menu->menu_navigation_build("FRONT_PUBLISH", "CLEAN");
		}
		else {
			$this->content->template['is_start'] = true;
		}

		// Sprachen und Rechte setzen
		$this->menu_preset_sprachen($menuid);
		$this->menu_preset_rechte($menuid);
		$this->menu_preset_layouts($menuid);
	}

	/**
	 * @param int $menuid
	 */
	function menu_preset_layouts($menuid = 0)
	{
		//ZUerst die Layouts rausholen
		$sql = sprintf('SELECT * FROM %s', $this->cms->tbname['papoo_styles']);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['menu_list_layout'] = $result;
	}

	/**
	 * Setzt Werte bei fehlerhafter Eingabe (bereits eingegebenen Werte werden durchgeschleift)
	 */
	function menu_preset_fehler()
	{
		$menuid = "";
		$this->content->template['text_error'] = $this->content->template['message_33a'];
		$this->content->template['templateweiche'] = "NEU_EDIT";
		$this->content->template['zeigemenu'] = "ok";
		$this->content->template['formmodus'] = $this->checked->formmodus;
		$this->content->template['formuntermenuzu_org'] = $this->checked->formuntermenuzu_org;
		$this->content->template['formuntermenuzu'] = $this->checked->formuntermenuzu;
		$this->content->template['formorder_id_org'] = $this->checked->formorder_id_org;
		$this->content->template['formmenuid'] = $this->checked->formmenuid;
		$this->content->template['formlink'] = $this->checked->formlink;

		// Liste "Untermenü zu" aufbauen
		$this->content->template['menulist_data'] = $this->menu->menu_navigation_build("FRONT_PUBLISH", "CLEAN");

		// Sprach-Zeugs setzen
		$temp_sprachen = array();
		$anzahl_sprachen = count($this->checked->language['lang_id']);
		for ($i = 0; $i < $anzahl_sprachen; $i ++) {
			$lang_id = $this->checked->language['lang_id'][$i];
			$sprache = $this->checked->language['language'][$i];
			$name = "nodecode:".$this->diverse->encode_quote($this->checked->language['formmenuname'][$i]);
			$titel = "nodecode:".$this->diverse->encode_quote($this->checked->language['formtitel'][$i]);
			$url_metadescription =
				"nodecode:".$this->diverse->encode_quote($this->checked->language['url_metadescrip'][$i]);
			$url_metakeywords =
				"nodecode:".$this->diverse->encode_quote($this->checked->language['url_metakeywords'][$i]);
			$url_menuname="nodecode:".$this->diverse->encode_quote($this->checked->language['url_menuname'][$i]);
			// !!! Hier evtl. genauere Fehler-Kennzeichnung einbauen
			$temp_array = array(
				"lang_id" => $lang_id,
				"language" => $sprache,
				"formmenuname" => $name,
				"url_menuname" => $url_menuname,
				"formtitel" => $titel,
				"url_metadescrip" => $url_metadescription,
				"url_metakeywords" => $url_metakeywords,
			);
			$temp_sprachen[] = $temp_array;
		}
		$this->content->template['menlang'] = $temp_sprachen; // !!!! ???

		// Rechte setzen
		$this->menu_preset_rechte($menuid, $this->checked->rechte_publish, $this->checked->rechte_lesen);
	}

	/**
	 * Setzt Werte zum Löschen des Menü-Punktes (wichtig: $menuid und $untermenuzu_org)
	 * !!! Sprachen nicht berücksichtigt
	 */
	function menu_preset_delete()
	{
		$this->content->template['templateweiche'] = "LOESCHEN";
		$this->content->template['delmenuid']= $this->checked->formmenuid;
		$this->content->template['deluntermenuzu_org']= $this->checked->formuntermenuzu_org;
		$this->content->template['delmenuname'] = $this->checked->language['formmenuname'][0];
		$this->content->template['deltitel'] = $this->checked->language['lang_id'][0];
		IfNotSetNull($this->checked->formlink);
		$this->content->template['dellink'] = $this->checked->formlink;
	}

	/**
	 * Baut die Liste für Ausgabe und Anzeige der verschiedenen Sprachen auf
	 *
	 * @param int $menuid
	 */
	function menu_preset_sprachen($menuid = 0)
	{
		// aktive Sprachen raussuchen
		$sql = sprintf(
			'SELECT lang_id, lang_long FROM %1$s WHERE more_lang=\'2\' OR lang_short=\'%2$s\';',
			$this->cms->papoo_name_language,
			$this->db->escape($this->cms->frontend_lang)
		);

		$sprachen = $this->db->get_results($sql);

		// Daten für den Menü-Punkt $menuid dazu raussuchen
		IfNotSetNull($this->checked->selmenuid);
		foreach ($sprachen as $sprache) {
			$sql = sprintf(
				'SELECT 
					menuname, url_menuname, lang_title, menulinklang, 
					url_metadescrip, url_metakeywords, publish_yn_lang_men
				FROM %1$s
				WHERE lang_id = \'%2$s\' AND menuid_id = \'%3$s\'
				LIMIT 1;',
				$this->cms->papoo_menu_language,
				$this->db->escape($sprache->lang_id),
				$this->db->escape($menuid)
			);
			$texte = $this->db->get_results($sql, ARRAY_A);

			//Noch keine Menuid - dann alles auf veröffentlichen stellen
			if (!is_numeric($this->checked->selmenuid)) {
				$this->content->template['publish_yn_lang_men_checked'][$sprache->lang_id] = ' checked="checked" ';
			}

			if (!empty($texte)) {
				$_SESSION['url_menuname'][$menuid][$sprache->lang_id]=$texte['0']['url_menuname'];
				$text = $texte[0];
				$this->content->template['menlang'][] = array (
					'lang_id' => $sprache->lang_id,
					'language' => $sprache->lang_long,
					'formmenuname' => "nodecode:".$this->diverse->encode_quote($text['menuname']),
					'url_menuname' => "nodecode:".$this->diverse->encode_quote($text['url_menuname']),
					'formlink' => "nodecode:".$this->diverse->encode_quote($text['menulinklang']),
					'formtitel' => "nodecode:".$this->diverse->encode_quote($text['lang_title']),
					'url_metadescrip' => "nodecode:".$this->diverse->encode_quote($text['url_metadescrip']),
					'url_metakeywords' => "nodecode:".$this->diverse->encode_quote($text['url_metakeywords'])
				);

				if ($text['publish_yn_lang_men'] == 1) {
					$this->content->template['publish_yn_lang_men_checked'][$sprache->lang_id] = ' checked="checked" ';
				}
			}
			else {
				$this->content->template['menlang'][] = array(
					'lang_id' => $sprache->lang_id,
					'language' => $sprache->lang_long,
					'formmenuname' => "",
					'url_menuname' => "",
					'url_metadescrip' => "",
					'url_metakeywords' => "",
					'formlink' => "",
					'formtitel' => ""
				);
			}
		}
	}

	/**
	 * Baut die Rechte-Listen für "Veröffentlichen" und "im Frontend lesen"
	 * Es wird der Marker "$checked" gesetzt, für diejenigen Menü-Punkte für die die
	 * entsprechenden Rechte bestehen (Veröffentlichen bzw. lesen im Frontend)
	 *
	 * @param int $menuid
	 * @param array $form_publish
	 * @param array $form_lesen
	 */
	function menu_preset_rechte($menuid = 0, $form_publish = array(), $form_lesen = array())
	{
		//Bilder einbinden
		$this->get_images();
		$liste_publish = array(); // Liste für "Veröffentlichen"
		$liste_lesen = array();	// Liste für "im Frontend lesen"

		$gruppen = $this->db->get_results(sprintf(
			'SELECT gruppenname, gruppeid FROM %1$s ORDER BY gruppenname;',
			$this->cms->papoo_gruppe
		));
		$rechte_publish = $this->db->get_results(sprintf(
			'SELECT gruppenid FROM %1$s WHERE menuid=\'%2$s\';',
			$this->cms->papoo_lookup_men,
			$this->db->escape($menuid)
		));
		$rechte_lesen = $this->db->get_results(sprintf(
			'SELECT gruppeid_id FROM %1$s WHERE menuid_id=\'%2$s\'',
			$this->cms->papoo_lookup_me_all_ext,
			$this->db->escape($menuid)
		));

		if (!empty($gruppen)) {
			// Hole alle Gruppen des angemeldeten Nutzers, ignoriere Gruppe "jeder"
			$user_hat_gruppen = $this->user->get_groups();
			unset($user_hat_gruppen[10]);

			foreach ($gruppen as $gruppe) {
				// Gruppe Admin wird nicht angezeigt, da diese Gruppe immer alle Rechte hat
				// (wird später beim Sichern der Rechte berücksichtigt)
				$is_admin = false;
				if ($gruppe->gruppeid == 1) {
					$is_admin = true;
				}

				// Rechte "publish" setzen
				$checked = "";
				// a. mit Daten aus Datenbank vergleichen
				if (!empty ($rechte_publish)) {
					foreach ($rechte_publish as $recht_publish) {
						if ($gruppe->gruppeid == $recht_publish->gruppenid) {
							$checked = 'nodecode:checked="checked"';
						}
					}
				}
				// b. mit Daten aus Formular vergleichen
				if (!empty($form_publish) && in_array($gruppe->gruppeid, $form_publish)) {
					$checked = 'nodecode:checked="checked"';
				}

				// c. Neuen Menuepunkt erstellen - Schreibrechte fuer alle Gruppen des angemeldeten Nutzers (exkl. Gruppe "jeder")
				if ($menuid == 0 && in_array($gruppe->gruppeid, array_keys($user_hat_gruppen))) {
					$checked = 'nodecode:checked="checked"';
				}

				$temp_publish = array(
					'gruppename' => $gruppe->gruppenname,
					'gruppeid' => $gruppe->gruppeid,
					'checked' => $checked
				);

				// Rechte "lesen" setzen
				$checked = "";
				// a. mit Daten aus Datenbank vergleichen
				if (!empty ($rechte_lesen)) {
					foreach ($rechte_lesen as $recht_lesen) {
						if ($gruppe->gruppeid == $recht_lesen->gruppeid_id) {
							$checked = 'nodecode:checked="checked"';
						}
					}
					// b. mit Daten aus Formular vergleichen
					if (!empty($form_lesen) && in_array($gruppe->gruppeid, $form_lesen))
						$checked = 'nodecode:checked="checked"';
				}
				//Standard setzen
				if ($gruppe->gruppeid == 10 && empty($_POST) && ($menuid == 0)) {
					if ($this->cms->stamm_menu_rechte_jeder == 1) {
						$checked = 'nodecode:checked="checked"';
					}
				}

				$temp_lesen = array(
					'gruppename' => $gruppe->gruppenname,
					'gruppeid' => $gruppe->gruppeid,
					'checked' => $checked
				);

				// Temp-Daten den Listen zuweisen (wenn nicht Gruppe Admin)
				if (!$is_admin) {
					$liste_publish[] = $temp_publish;
					$liste_lesen[] = $temp_lesen;
				}
			}
		}

		$this->content->template['liste_publish'] = $liste_publish;
		$this->content->template['liste_lesen'] = $liste_lesen;
	}

	/**
	 * Prüft ob die eingegebenen Daten korrekt sind
	 *
	 * @return bool
	 */
	function menu_check()
	{
		$alles_OK = true;

		// Texte Überprüfen
		$anzahl_sprachen = count($this->checked->language['lang_id']);
		for ($i = 0; $i < $anzahl_sprachen; $i ++) {
			// Test ob Einträge leer
			if (empty($this->checked->language['formmenuname']['0'])) {
				$alles_OK = false;
				// !!! Hier evtl. genauere Fehler-Kennzeichnung einbauen
				$this->content->template['error_menu_leer']="OK";
			}
		}
		return $alles_OK;
	}

	/**
	 * Speichert die Daten
	 *
	 * @param int $modus
	 * @param string $extern
	 * @return bool|void
	 */
	function menu_save($modus = 0, $extern = "no")
	{
		// 1. Diverse Anpassungen und Einstellungen
		// ****************************************
		//EDIT CHRISTOPH_Z -> trim whitespaces vom Anfang und Ende der Menüpunkte-Links
		for ($i = 0; $i < sizeof($this->checked->language['formlink']); $i++) {
			$this->checked->language['formlink'][$i] = trim($this->checked->language['formlink'][$i]);
		}

		// Menü-Link anpassen
		$formlink = (empty($this->checked->formlink) ? 'index.php' : $this->checked->formlink);

		// damit werden die Untermenüs zu Startseite auch tatsächlich untermenüs
		// Untermenuzu anpassen
		$untermenuzu = $this->checked->formuntermenuzu;
		//Alte Kategorie auslesen
		$sql = sprintf(
			'SELECT cat_cid FROM %1$s WHERE menuid_cid = \'%2$s\'',
			$this->cms->tbname['papoo_lookup_mencat'],
			$this->db->escape($this->checked->formmenuid)
		);
		$result_cat_cid = $this->db->get_var($sql);
		// Level-feststellen
		$level = $this->menu_get_level_id($untermenuzu);

		// Modus-Abhängige Voreinstellungen
		switch ($modus) {
		case "NEU":
			// Order-ID festlegen
			$order_id = $this->menu_get_order_id($untermenuzu);
			// SQL-Einstellungen
			$sql_modus = "INSERT INTO ";
			$sql_bedingung = "";
			break;

		case "EDIT":
			// Order-ID festlegen
			if ($this->checked->formuntermenuzu_org != $untermenuzu) {
				$order_id = $this->menu_get_order_id($untermenuzu);
			}
			else {
				$order_id = $this->checked->formorder_id_org;
			}
			// SQL-Einstellungen
			$sql_modus = "UPDATE ";
			$sql_bedingung = "WHERE menuid='" . $this->db->escape($this->checked->formmenuid) . "'";
			break;

		default:
			return false;
		}

		IfNotSetNull($this->checked->menu_subteaser);
		IfNotSetNull($this->checked->extra_css_sub);

		// 2. Menü-Daten speichern
		// ***********************
		$sql = sprintf(
			'%1$s %2$s 
			SET 
				menulink = \'%3$s\', menu_image = \'%4$s\', menu_subteaser = \'%5$s\', untermenuzu = \'%6$d\',
				level = \'%7$d\', order_id = \'%8$d\', extra_css_file = \'%9$s\', extra_css_sub = \'%10$s\',
				menu_spez_layout = \'%11$d\', artikel_sort = \'%12$d\'
			%13$s',
			$sql_modus,
			$this->cms->papoo_menu,
			$this->db->escape($formlink),
			$this->db->escape($this->checked->menu_image),
			$this->db->escape($this->checked->menu_subteaser),
			$this->db->escape($untermenuzu),
			$this->db->escape($level),
			$this->db->escape($order_id),
			$this->db->escape($this->checked->extra_css_file),
			$this->db->escape($this->checked->extra_css_sub),
			$this->db->escape($this->checked->menu_spez_layout),
			$this->db->escape($this->checked->artikel_sort),
			$sql_bedingung
		);
		$this->db->query($sql);
		if ($modus == "EDIT") {
			IfNotSetNull($this->checked->untermenuzu_org);
			$this->menu_reorder($this->checked->untermenuzu_org);
			$this->helper_level_set();
			$menuid = $this->checked->formmenuid;
		}
		else {
			$menuid = $this->db->insert_id;
		}

		IfNotSetNull($this->checked->category);

		$this->extern_insert_menuid = $menuid;
		//Wenn numerich und die neue Kategorie ist anders als die alte dann neu sezten
		if (is_numeric($menuid) && $result_cat_cid != $this->checked->category) {
			//Von extern gibts keine Kategorie... daher überspringen
			if ($extern == "no") {
				//Kategorien löschen
				$sql = sprintf(
					'DELETE FROM %1$s WHERE menuid_cid=\'%2$s\';',
					$this->cms->tbname['papoo_lookup_mencat'],
					$this->db->escape($menuid)
				);
				$this->db->query($sql);

				//Kategorien eintragen
				$sql = sprintf(
					'INSERT INTO %1$s SET menuid_cid=\'%2$s\', cat_cid=\'%3$s\';',
					$this->cms->tbname['papoo_lookup_mencat'],
					$this->db->escape($menuid),
					$this->db->escape($this->checked->category)
				);
				$this->db->query($sql);
			}
			else {
				$this->checked->category = $result_cat_cid;
			}
			if ($modus == "EDIT") {
				//Untermenupunkte $this->checked->formuntermenuzu
				$sql_m = sprintf(
					'SELECT menuid FROM %1$s WHERE untermenuzu=\'%2$d\';',
					$this->cms->papoo_menu,
					$this->db->escape($this->checked->formmenuid)
				);
				$result_m = $this->db->get_results($sql_m);
				//Durchgehen
				if (!empty($result_m)) {
					foreach ($result_m as $dat) {
						$sql = sprintf(
							'DELETE FROM %1$s WHERE menuid_cid=\'%2$s\'',
							$this->cms->tbname['papoo_lookup_mencat'],
							$this->db->escape($dat->menuid)
						);
						$this->db->query($sql);

						//Kategorien eintragen
						$sql = sprintf(
							'INSERT INTO %1$s SET menuid_cid=\'%2$s\', cat_cid=\'%3$s\';',
							$this->cms->tbname['papoo_lookup_mencat'],
							$this->db->escape($dat->menuid),
							$this->db->escape($this->checked->category)
						);
						$this->db->query($sql);
					}
				}
			}
		}

		// 3. Sprach-Daten speichern
		// *************************
		//Links ersetzen in Artikeln etc.
		$anzahl_sprachen = count($this->checked->language['lang_id']);

		for ($i = 0; $i < $anzahl_sprachen; $i++) {
			$langid = $this->checked->language['lang_id'][$i];
			$link = "";
			$this->m_url = array();
			$this->get_verzeichnis($menuid);
			$this->m_url = array_reverse($this->m_url);
			foreach ($this->m_url as $dat) {
				$link .= $dat . "/";
			}

			//Nur beim updaten aktivieren
			if ($modus == "EDIT") {
				//Name Übergeben
				$name = $this->checked->language['url_menuname'][$i];
				$sql = sprintf(
					'SELECT url_menuname FROM %1$s WHERE menuid_id=\'%2$s\' AND lang_id=\'%3$d\';',
					$this->cms->papoo_menu_language,
					$this->db->escape($menuid),
					$this->db->escape($langid)
				);
				$link = $this->db->get_var($sql) . "/";
				$this->remove_cache_file($link, $menuid);
				//Anzahl der gleichen Menüpunkte raussuchen //untermenuzu='%d' AND
				$sql = sprintf(
					'SELECT COUNT(menuid) FROM %1$s,%2$s WHERE menuid=menuid_id AND %2$s.url_menuname=\'%3$s\';',
					$this->cms->papoo_menu,
					$this->cms->papoo_menu_language,
					$this->db->escape($name)
				);
				$varcount = $this->db->get_var($sql);
				if ($varcount > 1) {

					$varcount = "-" . $langid . "-" . $menuid;
				}
				else {
					$varcount = "";
				}

				//Link in Artikel suchen
				$sql = sprintf(
					'SELECT * FROM %1$s WHERE lan_article LIKE \'%2$s\' AND lang_id=\'%3$s\'',
					$this->cms->papoo_language_article,
					"%href=\"" . PAPOO_WEB_PFAD . "/" . $this->cms->webvar . $this->db->escape($link) . "%",
					$this->db->escape($langid)
				);
				$result = $this->db->get_results($sql);
				//der alte Link
				$such = ("href=\"" . PAPOO_WEB_PFAD . "/" . $this->cms->webvar . $link);
				//der neue Link
				$replace = "href=\""
					. PAPOO_WEB_PFAD . "/" . $this->cms->webvar
					. $this->menu->replace_uml(
						strtolower($name . $varcount)
					) . "/";

				//Link in Artikel ersetzen und eintragen
				if (!empty($result)) {
					foreach ($result as $dat) {
						//Für jeden Eintrag ersetzen
						$dat->lan_article = str_ireplace($such, $replace, $dat->lan_article);
						$dat->lan_article_sans = str_ireplace($such, $replace, $dat->lan_article_sans);
						//Sql Statement zur Ersetzung
						$sql=sprintf(
							'UPDATE %1$s 
							SET lan_article = \'%2$s\', lan_article_sans=\'%3$s\'
							WHERE lan_repore_id=\'%4$d\' AND lang_id=\'%5$d\'
							LIMIT 1;',
							$this->cms->tbname['papoo_language_article'],
							$this->db->escape($dat->lan_article),
							$this->db->escape($dat->lan_article_sans),
							$this->db->escape($dat->lan_repore_id),
							$this->db->escape($dat->lang_id)
						);
						$this->db->query($sql);
					}
				}
				//Link in 3. Spalte suchen
				$sql = sprintf(
					'SELECT * FROM %1$s WHERE article LIKE \'%2$s\' AND lang_id=\'%3$s\';',
					$this->cms->tbname['papoo_language_collum3'],
					"%href=\"" . PAPOO_WEB_PFAD . "/" . $this->cms->webvar . $this->db->escape($link) . "%",
					$langid
				);
				$result=$this->db->get_results($sql);

				//Link in 3. Spalte ersetzen
				if (!empty($result)) {
					foreach ($result as $dat) {
						//Für jeden Eintrag ersetzen
						$dat->article=str_ireplace($such,$replace,$dat->article);
						$dat->article_sans=str_ireplace($such,$replace,$dat->article_sans);
						//Sql Statement zur Ersetzung
						$sql = sprintf(
							'UPDATE %1$s 
							SET article = \'%2$s\', article_sans = \'%3$s\'
							WHERE collum_id = \'%4$d\' AND lang_id=\'%5$d\'
							LIMIT 1;',
							$this->cms->tbname['papoo_language_collum3'],
							$this->db->escape($dat->article),
							$this->db->escape($dat->article_sans),
							$this->db->escape($dat->collum_id),
							$this->db->escape($dat->lang_id)
						);
						$this->db->query($sql);
					}
				}

				//Link in Startseite suchen
				$sql = sprintf(
					'SELECT * FROM %1$s WHERE lang_id = \'%2$s\'',
					$this->cms->tbname['papoo_language_stamm'],
					$langid
				);
				$result = $this->db->get_results($sql);
				//Link in Startseite ersetzen
				if (!empty($result)) {
					foreach ($result as $dat) {
						//Für jeden Eintrag ersetzen
						$dat->start_text = str_ireplace($such, $replace, $dat->start_text);
						$dat->start_text_sans = str_ireplace($such, $replace, $dat->start_text_sans);
						//Sql Statement zur Ersetzung
						$sql = sprintf(
							'UPDATE %1$s 
							SET start_text = \'%2$s\', start_text_sans = \'%3$s\'
							WHERE stamm_id = \'%4$d\' AND lang_id = \'%5$d\'
							LIMIT 1;',
							$this->cms->tbname['papoo_language_stamm'],
							$this->db->escape($dat->start_text),
							$this->db->escape($dat->start_text_sans),
							$this->db->escape($dat->stamm_id),
							$this->db->escape($dat->lang_id)
						);
						$this->db->query($sql);
					}
				}
			}

		}
		$sprachextern = "";
		if ($extern != "no" && $modus == "EDIT") {
			$sprachextern = ' AND lang_id=\'' . $this->cms->lang_back_content_id . '\'';
		}
		// 3.1 Alle alten Einträge löschen
		$sql = sprintf(
			'DELETE FROM %1$s WHERE menuid_id=\'%2$d\' %3$s;',
			$this->cms->papoo_menu_language,
			$menuid,
			$sprachextern
		);
		$this->db->query($sql);

		// 3.2 Neue Einträge einfügen
		for ($i = 0; $i < $anzahl_sprachen; $i++) {
			$langid = $this->checked->language['lang_id'][$i];
			$name = $this->checked->language['formmenuname'][$i];
			$titel = $this->checked->language['formtitel'][$i];
			$menulinklang = $this->checked->language['formlink'][$i];
			$url_menuname = $this->checked->language['url_menuname'][$i];
			$url_metadescrip = $this->checked->language['url_metadescrip'][$i];
			$url_metakeywords = $this->checked->language['url_metakeywords'][$i];
			if (strlen($url_menuname) < 2) {
				$url_menuname = $name;
			}
			if (strlen($url_menuname) < 2) {
				$url_menuname = $url_menuname . $menuid;
			}

			//Checken ob es schon einen dieser Ebene gibt // nicht gleiche Ebene
			$varcount="";
			if ($this->cms->mod_free != 1) {
				$sql = sprintf(
					'SELECT COUNT(menuid) FROM %1$s, %2$s WHERE menuid = menuid_id AND %2$s.url_menuname=\'%3$s\';',
					$this->cms->papoo_menu,
					$this->cms->papoo_menu_language,
					$this->db->escape($url_menuname)
				);
				$varcount = $this->db->get_var($sql);
				if ($varcount >= 1) {
					$varcount = "-" . $langid . "-" . $menuid;
				}
				else {
					$varcount = "";
				}
			}
			if ($this->cms->mod_free == 1) {
				if (!stristr($url_menuname, ".html") && $url_menuname[strlen($url_menuname)-1] != "/") {
					$url_menuname = $url_menuname . "/";
				}
				if ($url_menuname[0] != "/") {
					$url_menuname = "/".$url_menuname;
				}
			}
			if (empty($titel)) {
				$titel = $name;
			}
			//check_url_header
			$url_menuname_insert =
				$this->check_url_header(
					$this->menu->replace_uml(strtolower($url_menuname)) . $varcount, $langid
				);

			if ($extern != "no" && $modus == "EDIT") {
				if ($this->cms->lang_back_content_id == $langid) {
					$sql = sprintf(
						'INSERT INTO %1$s
						SET
							menuid_id = \'%2$d\', lang_id = \'%3$d\', menuname = \'%4$s\', url_menuname = \'%5$s\',
							url_metadescrip = \'%6$s\', url_metakeywords = \'%7$s\', lang_title = \'%8$s\', 
							menulinklang = \'%9$s\', publish_yn_lang_men = \'%10$d\';',
						$this->cms->papoo_menu_language,
						$this->db->escape($menuid),
						$this->db->escape($langid),
						$this->db->escape($name),
						$this->db->escape($url_menuname_insert),
						$this->db->escape($url_metadescrip),
						$this->db->escape($url_metakeywords),
						$this->db->escape($titel),
						$this->db->escape($menulinklang),
						$this->db->escape($this->checked->publish_yn_lang_men[$langid])
					);
					$this->db->query($sql);
				}
			}
			else {
				$sql = sprintf(
					'INSERT INTO %1$s
					SET
					    menuid_id = \'%2$d\', lang_id = \'%3$d\', menuname = \'%4$s\', url_menuname = \'%5$s\',
						url_metadescrip = \'%6$s\', url_metakeywords = \'%7$s\', lang_title = \'%8$s\', 
						menulinklang = \'%9$s\', publish_yn_lang_men = \'%10$d\';',
					$this->cms->papoo_menu_language,
					$this->db->escape($menuid),
					$this->db->escape($langid),
					$this->db->escape($name),
					$this->db->escape($url_menuname_insert),
					$this->db->escape($url_metadescrip),
					$this->db->escape($url_metakeywords),
					$this->db->escape($titel),
					$this->db->escape($menulinklang),
					$this->db->escape($this->checked->publish_yn_lang_men[$langid])
				);
				$this->db->query($sql);
			}
		}

		//Hier dann direkt einen Artikel erstellen wenn index.php gewählt
		if ($formlink == "index.php" && $modus != "EDIT") {
			$url_menuname_insert_article =
				$this->check_url_header(
					$this->menu->replace_uml(strtolower($this->checked->language['formmenuname']['0'])) . $varcount, $langid
				);
			$this->create_article_blank($menuid, $url_menuname_insert, $this->checked->language['formmenuname']['0']);
		}

		// 4. Rechte speichern
		// *******************
		// 4.1 Lookup-Tabelle füllen: welche Gruppen unter diesem Menü-Punkt veröffentlichen dürfen
		// 4.1.1 Alle alten Einträge löschen
		$sql = sprintf(
			'DELETE FROM %1$s WHERE menuid = \'%2$d\'',
			$this->cms->papoo_lookup_men,
			$menuid
		);
		$this->db->query($sql);

		// 4.1.2 Rechte schreiben
		$this->checked->rechte_publish[] = 1; // root darf immer
		foreach ($this->checked->rechte_publish as $recht_publish) {
			$sql = sprintf(
				'INSERT INTO %1$s SET menuid = \'%2$d\', gruppenid = \'%3$d\'',
				$this->cms->papoo_lookup_men,
				$this->db->escape($menuid),
				$this->db->escape($recht_publish)
			);
			$this->db->query($sql);
		}

		// Lookup-Tabelle füllen: wer darf diesen Menü-Punkt im Frontend sehen
		// 4.2.1 Alle alten Einträge löschen
		$sql = sprintf(
			'DELETE FROM %1$s WHERE menuid_id = \'%2$d\';',
			$this->cms->papoo_lookup_me_all_ext,
			$this->db->escape($menuid)
		);
		$this->db->query($sql);

		// 4.2.2 Rechte schreiben
		$this->checked->rechte_lesen[] = 1; // root darf immer
		foreach ($this->checked->rechte_lesen as $recht_lesen)
		{
			$sql = sprintf(
				'INSERT INTO %1$s SET menuid_id = \'%2$d\', gruppeid_id = \'%3$d\';',
				$this->cms->papoo_lookup_me_all_ext,
				$this->db->escape($menuid),
				$this->db->escape($recht_lesen)
			);
			$this->db->query($sql);
		}

		if ($extern == "no") {
			// 5. Weiterleitung für neuen Seitenaufbau
			$location_url = "./menu.php?menuid=44&selmenuid=".$menuid."&messageget=31";
			if (isset($_SESSION['debug_stopallredirect']) && $_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit ();
		}
	}

	/**
	 * @param $url
	 * @param $langid
	 * @return bool|mixed|string
	 */
	function check_url_header($url, $langid)
	{
		if ($this->cms->mod_free == 1) {
			$sql = sprintf(
				'SELECT lang_short FROM %1$s WHERE lang_id = \'%2$s\';',
				$this->cms->tbname['papoo_name_language'],
				$this->db->escape($langid)
			);
			$lang_short = $this->db->get_var($sql);

			//Nur Bei NICHT Standardsprache
			if ($lang_short != $this->content->template['lang_front_default']) {
				if (!stristr($url, "/" . $lang_short . "/")) {
					//DIe neue url
					$url = "/" . $lang_short . $url;
				}
			}

			$sql = sprintf(
				'SELECT COUNT(lan_repore_id) FROM %1$s WHERE url_header = \'%2$s\';',
				$this->cms->tbname['papoo_language_article'],
				$this->db->escape($url)
			);
			$result = $this->db->get_var($sql);
			if ($result > 0) {
				$klammer_id = "" . time();
				$last = substr($url, -1, 1);
				$last2 = substr($url, -5, 5);

				//Artikel mit .html
				if ($last2 == ".html" ) {
					$url = str_replace(".html", "-" . $klammer_id . ".html", $url);
				}
				//Artikel mit /
				if ($last == "/") {
					$url = substr($url, 0, -1);
					$url = $url . "-" . $klammer_id . "/";
				}
				$url = str_replace("?", "", $url);
				$url = str_replace("&", "", $url);
				return $url;
			}

			$sql = sprintf(
				"SELECT COUNT(menuid_id) FROM %s WHERE url_menuname = '%s'",
				$this->cms->tbname['papoo_menu_language'],
				$this->db->escape($url)
			);
			$result = $this->db->get_var($sql);
			if ($result > 0) {
				$klammer_id = "" . time();
				$last = substr($url, -1, 1);
				$last2 = substr($url, -5, 5);

				//Artikel mit .html
				if ($last2 == ".html" ) {
					$url = str_replace(".html", "-" . $klammer_id . ".html", $url);
				}
				//Artikel mit /
				if ($last == "/") {
					$url = substr($url,0,-1);
					$url = $url."-".$klammer_id."/";
				}
				$url = str_replace("?", "", $url);
				$url = str_replace("&", "", $url);
				return $url;
			}

			$url = str_replace("?", "", $url);
			$url = str_replace("&", "", $url);
			return $url;
		}
		else {
			$url = str_replace("?", "", $url);
			$url = str_replace("&", "", $url);
			return $url;
		}
	}

	/**
	 * @param int $menuid
	 * @param string $url_menuname_insert
	 * @param string $name
	 */
	private function create_article_blank($menuid = 0,$url_menuname_insert = "",$name = "")
	{

		//menuid
		$temp_menu['menuid'] = $menuid;
		$temp_menu['url_menuname'] = $url_menuname_insert;
		$temp_menu['menuname'] = $name;

		$sql = sprintf(
			'INSERT INTO %1$s
			SET
				cattextid = \'%2$d\', cat_category_id = 0, dokuser = \'%3$d\', dokuser_last = \'%3$d\',
				timestamp = NOW(), erstellungsdatum = NOW(), stamptime = \'%4$s\', order_id = 0,
				order_id_start = 0, allow_comment = 0, artikel_news_list = 0, publish_yn = \'1\',
				pub_dauerhaft = 1, allow_publish = 1, dok_teaserfix = 0, uberschrift_hyn = 1,
				teaser_atyn = 1, teaser_list = 0, teaser_bild_groesse = \'x\';',
			DB_PRAEFIX . "papoo_repore",
			$temp_menu['menuid'],
			$this->user->userid,
			time()
		);
		$this->db->query($sql);
		$temp_repore_id = $this->db->insert_id;

		$temp_artikel_url = $temp_menu['url_menuname'];
		if (mb_substr($temp_artikel_url, -1) == "/") {
			$temp_artikel_url = mb_substr($temp_artikel_url, 0, mb_strlen($temp_artikel_url) - 1);
		}
		$temp_artikel_url .= ".html";

		// Sprach-Daten loopen
		// *****************************
		$sqlLang = sprintf("SELECT * FROM %s WHERE  more_lang=2 ",
						DB_PRAEFIX."papoo_name_language");
		$availableLangs = $this->db->get_results($sqlLang,ARRAY_A);

		foreach ($availableLangs as $k =>$language)
		{
			$sql = sprintf(
				'INSERT INTO %1$s 
			SET
				lan_repore_id=\'%2$d\', lang_id=\'%3$d\', header=\'%4$s\', url_header=\'%5$s\',
				lan_teaser=\'%6$s\', lan_article_sans=\'%7$s\', lan_article=\'%8$s\', publish_yn_lang=\'1\';',
				DB_PRAEFIX."papoo_language_article",
				$temp_repore_id,
				$language['lang_id'],
				$temp_menu['menuname'],
				$temp_artikel_url,
				'',
				'<p>.. INHALT ..</p>',
				'<p>.. INHALT ..</p>'
			);
			$this->db->query($sql);
		}


		// Menü-Zuweisung
		// *****************************
		$sql = sprintf(
			"INSERT INTO %s SET lart_id = '%d', lcat_id = '%d', lart_order_id = 10",
			DB_PRAEFIX."papoo_lookup_art_cat",
			$temp_repore_id,
			$temp_menu['menuid']
		);
		$this->db->query($sql);

		// Lese-Rechte
		// *****************************
		$sql = sprintf(
			"INSERT INTO %s SET article_id = '%d', gruppeid_id = 1",
			DB_PRAEFIX."papoo_lookup_article",
			$temp_repore_id
		);
		$this->db->query($sql);

		if ($this->cms->stamm_artikel_rechte_jeder == 1) {
			$sql = sprintf(
				"INSERT INTO %s SET article_id = '%d', gruppeid_id = 10",
				DB_PRAEFIX."papoo_lookup_article",
				$temp_repore_id
			);
			$this->db->query($sql);
		}

		// Schreib-Rechte
		// *****************************
		$sql = sprintf(
			"INSERT INTO %s SET article_wid_id = '%d', gruppeid_wid_id = 1",
			DB_PRAEFIX."papoo_lookup_write_article",
			$temp_repore_id
		);
		$this->db->query($sql);

		if ($this->cms->stamm_artikel_rechte_chef == 1) {
			$sql = sprintf(
				"INSERT INTO %s SET article_wid_id = '%d', gruppeid_wid_id = 11",
				DB_PRAEFIX."papoo_lookup_write_article",
				$temp_repore_id
			);
			$this->db->query($sql);
		}
	}

	/**
	 * intern_artikel::remove_cache_file()
	 * Cache Dateien entfernen
	 *
	 * @param string $file
	 * @param int $men_id
	 * @return void
	 */
	function remove_cache_file($file = "",$men_id = 0)
	{
		$pfad = PAPOO_ABS_PFAD . "/cache/";
		$url = str_replace("/", "", $file);
		$extension = ".html";
		if ($pfad && $men_id) {
			$handle = @opendir($pfad);
			while (false !== ($file = @readdir($handle))) {
				if (!empty($extension) && @stristr($file, $url)){
					@unlink($pfad . $file);
				}
				if (@stristr($file, "menuid_" . $men_id)){
					@unlink($pfad . $file);
				}
			}
			closedir($handle);
		}
	}

	/**
	 * @param int $menuid
	 * @param int $untermenuzu
	 * @param int $extern
	 * @return bool|void
	 */
	function menu_delete($menuid = 0, $untermenuzu = -1,$extern = 0)
	{
		if ($menuid != 0 AND $untermenuzu != -1) {
			// 1. Einträge aus den verschiedenen TAbellen löschen
			// 1.1 Menüeintrag löschen
			$sql = sprintf(
				"DELETE FROM %s WHERE menuid='%d' AND menuid<>1",
				$this->cms->papoo_menu,
				$this->db->escape($menuid)
			);
			$this->db->query($sql);

			// 1.2 Lookup Einträge "publish" löschen
			$sql = sprintf("DELETE FROM %s WHERE menuid='%d' AND menuid<>1 ",
				$this->cms->papoo_lookup_men,
				$this->db->escape($menuid)
			);
			$this->db->query($sql);

			// 1.3 Lookup Einträge "lesen" löschen
			$sql = sprintf("DELETE FROM %s WHERE menuid_id='%d' AND menuid_id<>1 ",
				$this->cms->papoo_lookup_me_all_ext,
				$this->db->escape($menuid)
			);
			$this->db->query($sql);
			// 1.4 SprachEinträge löschen
			$sql = sprintf(
				"DELETE FROM %s WHERE  menuid_id='%d' AND menuid_id<>1 ",
				$this->cms->papoo_menu_language,
				$this->db->escape($menuid)
			);
			$this->db->query($sql);

			$sql = sprintf(
				"UPDATE %s SET untermenuzu = '1' , level ='1' WHERE untermenuzu ='%d'",
				$this->cms->papoo_menu,
				$this->db->escape($menuid)
			);
			$this->db->query($sql);

			// 2. Order-IDs der Menü-Punkte mit gleichem "$untermenuzu" neu durchnummerieren (da ja evtl. ein "Loch" entstanden ist)
			$this->menu_reorder($untermenuzu);

			// 3. Weiterleitung für neuen Seitenaufbau
			if ($extern == 0) {
				$location_url = "./menu.php?menuid=14&messageget=34";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="'.$location_url.'">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit ();
			}
		}
		else {
			return false;
		}
	}

	/**
	 * Gibt den Ebenen-Level des Menü-Punktes mit dem Wert für 'untermenuzu' = $eltern_id zurück.
	 *
	 * @param int $eltern_id
	 * @return int|null
	 */
	function menu_get_level_id ($eltern_id = -1)
	{
		$level = 0;
		if ($eltern_id != -1) {
			if ($eltern_id == 0) {
				$level = 0;
			}
			else {
				// Level des Eltern-Elements feststellen
				$sql = sprintf(
					"SELECT level FROM %s WHERE menuid='%d'",
					$this->cms->papoo_menu,
					$this->db->escape($eltern_id)
				);
				$level = $this->db->get_var($sql);
				// Level ist um eins höher als der des Eltern-Elements
				$level = $level + 1;
			}
		}
		return $level;
	}

	/**
	 * Gibt die Order-ID des Menü-Punktes mit dem Wert für 'untermenuzu' = $eltern_id zurück.
	 * So werden neue oder verschobene Menü-Punkte mit der richtigen Order-ID ausgestattet
	 *
	 * @param int $eltern_id
	 * @return bool|float|int
	 */
	function menu_get_order_id ($eltern_id = -1)
	{
		$order_id = 0;

		if ($eltern_id != -1) {
			// Anzahl bisheriger Menü-Punkte raussuchen
			$sql = sprintf(
				"SELECT COUNT(menuid) FROM %s WHERE untermenuzu='%d'",
				$this->cms->papoo_menu,
				$this->db->escape($eltern_id)
			);
			$anzahl = $this->db->get_var($sql);
			$order_id = $anzahl*10 + 10;
		}

		return ($order_id ? $order_id : false);
	}

	/**
	 * Nummeriert alle Menü-Punkte mit dem Wert für 'untermenuzu' = $eltern_id neu durch.
	 * Die Sortierung bleibt dabei erhalten, es werden lediglich evtl. entstandene "Nummern-Löcher" entfernt
	 *
	 * @param int $eltern_id
	 * @return bool
	 */
	function menu_reorder ($eltern_id = -1)
	{
		$alles_OK = true;
		if ($elternid = "reorder") {
			// Menü-Struktur auslesen
			$sql=sprintf("SELECT menuid,untermenuzu,level,order_id FROM %s ORDER BY untermenuzu,order_id",
				$this->cms->tbname['papoo_me_nu'] );
			$menu = $this->db->get_results($sql,ARRAY_A);
			$i=1;

			// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
			IfNotSetNull($altid);

			// Menü-Punkte neu durchnummerieren
			if (!empty($menu)) {
				foreach ($menu as $menupunkt) {
					if ($menupunkt['untermenuzu'] > $altid) {
						$i=1;
					}

					$order_id_neu = $i*10;
					$altid = $menupunkt['untermenuzu'];

					$sql = sprintf(
						"UPDATE %s SET order_id='%d' WHERE menuid='%d'",
						$this->cms->papoo_menu,
						$order_id_neu,
						$menupunkt['menuid']
					);
					$i++;
					$this->db->query($sql);
				}
			}
			$_SESSION['dbp']['menuok']=0;
		}
		else {
			$_SESSION['dbp']['menuok']=0;

			if ($eltern_id == -1) {
				$alles_OK = false;
			}
			else {
				// Menü-Punkte raussuchen
				$sql = sprintf("SELECT * FROM %s WHERE untermenuzu='%d' ORDER BY order_id ASC",
					$this->cms->papoo_menu,
					$this->db->escape($eltern_id)
				);
				$menupunkte = $this->db->get_results($sql);

				// Wenn es Menü-Punkte gibt, diese neu durchnummerieren
				if (!empty($menupunkte)) {
					$nummer_neu = 1;
					foreach ($menupunkte as $menupunkt) {
						$sql = sprintf("UPDATE %s SET order_id='%d' WHERE menuid='%d'",
							$this->cms->papoo_menu,
							$this->db->escape($nummer_neu),
							$this->db->escape($menupunkt->menuid)
						);
						$this->db->query($sql);
						$nummer_neu += 1;
					}
				}
			}
		}
		return $alles_OK;
	}


	// **********************
	// *  Reihefolge ändern *
	// **********************

	/**
	 * Unter-Aktions-Weiche für Reihenfolge ändern
	 */
	function switch_order()
	{
		// Daten aller Frontend-Menü-Punkte laden (für Baum der Menü-Punkte)
		$this->menu->data_front_complete = $this->menu->menu_data_read("FRONT_PUBLISH");
		$this->content->template['zeigemenu'] = "ok";
		//print_r($this->data_front_publish);
		if ($this->cms->categories == 1) {

			// Kategorien rausholen
			$this->menu->preset_categories();
			$this->menu->get_menu_cats();
			$this->content->template['categories'] = 1;
		}
		else {
			$this->content->template['catlist_data'][0] = array("catid"=>-1);
			$this->content->template['catlist_data'][1] = array("catid"=>-1);
			$this->content->template['catlist_data'][2] = array("catid"=>-1);
			$this->content->template['catlist_data'][3] = array("catid"=>-1);
			$this->content->template['catlist_data'][4] = array("catid"=>-1);
			$this->content->template['catlist_data'][5] = array("catid"=>-1);
			$this->content->template['no_categories'] = 1;
		}

		// Menü-Baum anzeigen
		IfNotSetNull($this->menu->ebenen_shift_ende);
		if (empty($this->checked->saveorder)) {
			// Template-Weiche setzen
			$this->content->template['templateweiche'] = "REORDER";

			// Daten raussuchen für Anzeige Menü-Baum
			$this->content->template['menuorder_data'] = $this->menu->menu_navigation_build("FRONT_COMPLETE", "CLEAN");
			$this->content->template['menuorder_ebenen_shift_ende'] = $this->menu->ebenen_shift_ende;
		}
		// Menü-Punkt wurde ausgewählt -> Reihenfolge ändern
		else {
			$this->order_change();
		}
		$this->menu->make_menucats();
	}

	/**
	 * Hiermit kann die Reihenfolge der Menüpunkte verändert werden.
	 *
	 * @return void
	 */
	function order_change()
	{
		//Alle Menüpunkte durchgeben und neue Orderids eintragen
		$menu = $this->menu->menu_data_read("FRONT_ORDERMENUID");

		// Menü-Punkte neu durchnummerieren
		if (!empty($menu)) {
			foreach ($menu as $menupunkt) {
				$order_id_neu = $this->checked->menuorder_menuid[$menupunkt['menuid']];
				if (is_numeric($order_id_neu)) {
					$sql = sprintf(
						"UPDATE %s SET order_id='%d' WHERE menuid='%d' ",
						$this->cms->papoo_menu,
						$this->db->escape($order_id_neu),
						$menupunkt['menuid']
					);
					$this->db->query($sql);
				}
			}
		}
		$this->menu_reorder("reorder");

		// Seite neu laden (so kann erneutes Verschieben durch Browser-Reload verhindert werden)
		if (empty($this->checked->noload_shop)) {
			$location_url = "./menu.php?menuid=46";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit();
		}
	}

	/**
	 * Wechselt die Ordnungs-ID $order_id der beiden Wechsel-Partner $vorgaenger und $nachfolger
	 *
	 * @param $vorgaenger
	 * @param $nachfolger
	 */
	function order_partner_wechsel($vorgaenger, $nachfolger)
	{
		if (!empty($vorgaenger) && !empty($nachfolger)) {
			$sql = sprintf(
				"UPDATE %s SET order_id='%d' WHERE menuid='%d'",
				$this->cms->papoo_menu,
				$this->db->escape($vorgaenger['orderid']),
				$this->db->escape($nachfolger['menuid'])
			);
			$this->db->query($sql);

			$sql = sprintf(
				"UPDATE %s SET order_id='%d' WHERE menuid='%d'",
				$this->cms->papoo_menu,
				$this->db->escape($nachfolger['orderid']),
				$this->db->escape($vorgaenger['menuid'])
			);
			$this->db->query($sql);
		}
	}

	/**
	 * Diese Funktion liefert die Bilder aus, an denen der Editor die Rechte hat an den Editor
	 *
	 * @param int $sprach_id
	 * @return void
	 */
	function get_images($sprach_id = 0)
	{
		if (empty($sprach_id)) {
			$sprach_id = $this->cms->lang_id;
		}
		global $intern_artikel;
		$this->intern_artikel = &$intern_artikel;
		$catok = $this->intern_artikel->get_image_cat_list();

		$sqlbild = sprintf(
			"SELECT DISTINCT(image_id), image_name, alt, title, image_width, image_dir, image_height
			FROM %s, %s, %s
			WHERE image_id = lan_image_id  AND lang_id = '%d' AND image_dir = bilder_cat_id
			OR image_id = lan_image_id  AND lang_id = '%d' AND image_dir = 0
			ORDER BY image_dir, alt ASC",
			$this->cms->papoo_images,
			$this->cms->papoo_language_image,
			$this->cms->tbname['papoo_kategorie_bilder'],
			$this->db->escape($sprach_id),
			$this->db->escape($sprach_id)
		);
		$resultbild = $this->db->get_results($sqlbild);
		$bild_data = array ();

		if (!empty($resultbild)) {
			IfNotSetNull($this->unique_id);
			IfNotSetNull($this->replangid);
			IfNotSetNull($this->diverse->isdeep_item);
			foreach ($resultbild as $rowbild) {
				if (isset($_SESSION['metadaten']) and $_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaserbild'] == $rowbild->image_name) {
					$checked = 'selected="selected"';
				}
				else {
					$checked = "";
				}
				$thumbsize = array();
				$buh= "";
				//Größe der Thumbnails rausholen
				if (@file_exists(("../images/thumbs/".$rowbild->image_name))){
					$thumbsize = @GetImageSize("../images/thumbs/".$rowbild->image_name);
					$buh = $thumbsize[0]."x".$thumbsize[1];
				}
				//Nur wenn Cat ok
				IfNotSetNull($thumbsize[0]);
				IfNotSetNull($thumbsize[1]);
				if ($this->diverse->deep_in_array($rowbild->image_dir,$catok) or $rowbild->image_dir=="0"){
					array_push($bild_data, array (
						'image_name' => $rowbild->image_name,
						'image_alt' => str_replace('"', '', $rowbild->alt),
						'image_title' => str_replace('"', '', $rowbild->title),
						'image_dir' => $this->diverse->isdeep_item['bilder_cat_name'],
						'image_width' => $rowbild->image_width,
						'image_height' => $rowbild->image_height,
						'image_thumb_width' => $thumbsize[0],
						'image_thumb_height' => $thumbsize[1],
						'thumb_buh' => $buh,
						'image_check_teaser' => $checked));
				}
			}
			$this->content->template['bild_data'] = $bild_data;
		}
		// ENDE --- Bilder aus der Datenbank holen für bbcode Editor ... ###
	}

	/**
	 * @param int $level
	 * @param array $menuids
	 */
	function helper_level_set($level = 0, $menuids = array())
	{
		if ($level == 0) {
			$sql = sprintf("UPDATE %s SET level='0' WHERE untermenuzu='0'", $this->cms->papoo_menu);
			$this->db->query($sql);

			$sql = sprintf("SELECT menuid FROM %s WHERE untermenuzu='0'", $this->cms->papoo_menu);
			$temp_eltern_menu_ids = $this->db->get_col($sql);
		}
		else {
			$temp_menuids_string = implode(",", $menuids);
			$sql = sprintf(
				"UPDATE %s SET level='%d' WHERE untermenuzu IN (%s)",
				$this->cms->papoo_menu,
				$level,
				$temp_menuids_string
			);
			$this->db->query($sql);

			$sql = sprintf("SELECT menuid FROM %s WHERE untermenuzu IN (%s)", $this->cms->papoo_menu, $temp_menuids_string);
			$temp_eltern_menu_ids = $this->db->get_col($sql);
		}

		if (!empty($temp_eltern_menu_ids)) {
			$level++;
			$this->helper_level_set($level, $temp_eltern_menu_ids);
		}
	}
}

$intern_menu = new intern_menu_class();
