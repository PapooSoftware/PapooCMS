<?php
/**
 * #####################################
 * # Papoo CMS                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.3.0                #
 * #####################################
 */

/**
 * Das ist die Mega Klasse mit der man
 * Artikel erstellen und bearbeiten kann
 */
#[AllowDynamicProperties]
class intern_artikel
{
	/**
	 * intern_artikel constructor.
	 */
	function __construct()
	{
		// Enbindung globaler Klassen
		global $cms, $db, $db_abs, $message, $user, $weiter, $content, $searcher,
			   $checked, $menu, $intern_menu, $artikel, $html, $replace, $diverse;

		// cms Klasse einbinden
		$this->cms = &$cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		$this->db = &$db;
		// Messages einbinden
		$this->db_abs = &$db_abs;
		$this->message = &$message;
		// User Klasse einbinden
		$this->user = &$user;
		// weitere Seiten Klasse einbinden
		$this->weiter = &$weiter;
		// inhalt Klasse einbinden
		$this->content = &$content;
		// Suchklasse einbinden
		$this->searcher = &$searcher;
		// checkedblen Klasse einbinde
		$this->checked = &$checked;
		// Menü-Klasse einbinden
		$this->menu = &$menu;
		// Interne Menüklasse einbinden
		$this->intern_menu = &$intern_menu;
		// Artikel-Klasse
		$this->artikel = &$artikel;
		// html Bereinigunsklasse
		$this->html = &$html;
		// Ersetzungklasse
		$this->replace = &$replace;
		// Einbindung der Klasse Diverse
		$this->diverse = &$diverse;

		IfNotSetNull($this->content->template['templateweiche']);
		IfNotSetNull($this->content->template['message_text']);
		IfNotSetNull($this->content->template['artikel']);
		IfNotSetNull($this->content->template['artikelliste']);
		IfNotSetNull($this->content->template['artikel_start']);
		IfNotSetNull($this->content->template['keine_freigabe']);
		IfNotSetNull($this->content->template['urheber']);
		IfNotSetNull($this->content->template['eigene']);
		IfNotSetNull($this->content->template['newsletter_link']);
	}

	/**
	 *
	 */
	function make_inhalt()
	{
		$this->check_publish_lang();
		// Überprüfen ob Zugriff auf die Inhalte besteht
		$this->user->check_access();
		$this->content->template['formmenuid_sort'] = $this->checked->menuid;
		$this->content->template['selected_tab'] = 0;
		//$this->cms->make_lang();
		IfNotSetNull($_SESSION['sort']);
		if(!empty($this->checked->sort)) {
			$_SESSION['sort'] = $this->checked->sort;
		}
		else {
			$this->checked->sort = $_SESSION['sort'];
		}

		if(empty($_SESSION['sort'])) {
			$_SESSION['sort'] = "zeit";
			$this->checked->sort = "zeit";
		}
		$this->set_replangid();

		$sql = sprintf("SELECT user_list_artikel FROM %s WHERE username='%s'",
			$this->cms->papoo_user,
			$this->user->username
		);
		$user_list_artikel = $this->db->get_var($sql);

		$this->content->template['user_list_artikel'] = $user_list_artikel;

		// HACK für Rückumwandlung von <br>s im TinyMCE
		if(empty($this->checked->editor_name)) {
			$this->checked->editor_name = "";
		}
		if(empty($this->checked->inhalt_ar['inhalt'])) {
			$this->checked->inhalt_ar['inhalt'] = "";
		}
		if($this->checked->editor_name == "tinymce") {
			$this->checked->inhalt_ar['inhalt'] =
				$this->diverse->recode_entity_n_br($this->checked->inhalt_ar['inhalt'], "nobr");
		}

		// arrays init
		$this->content->template['link_data'] = array();
		$this->content->template['menlang'] = array();
		if(empty ($this->checked->messageget)) {
			$this->checked->messageget = "";
		}
		//Wenn aus Frontend kommt Session erstellen
		if(!empty($this->checked->from)) {
			$_SESSION['from'] = $this->checked->from;
			$_SESSION['f_langid'] = $this->checked->f_langid;
			$_SESSION['f_menuid'] = $this->checked->f_menuid;
		}
		$this->unique_id = $this->checked->reporeid;
		if(empty($this->unique_id)) {
			if(empty($this->checked->unique_id)) {
				//Session Einträge durchzählen
				$i = 0;
				if(!empty($_SESSION['metadaten'])) {
					foreach($_SESSION['metadaten'] as $un) {
						$i++;
					}
				}
				$this->content->template['unique_id'] = $this->unique_id = 10000 + $i + (int)$this->user->userid + rand();
				//Nie mehr als 50 Artikel gleichzeitig
				if($i > 50) {
					unset($_SESSION['metadaten']);
				}
			}
			else {
				$this->content->template['unique_id'] = $this->unique_id = $this->checked->unique_id;
			}
		}

		switch($this->checked->menuid) {
		case "5" :
			// SESSION-Sprach-ID zurücksetzen
			$_SESSION['back_aktu_article_edit_langid'] = "";
			$this->content->template['artikel_start'] = "ok";
			// Starttext Übergeben
			if(empty ($this->checked->messageget)) {
				$this->content->template['text'] = "ok";
			}
			// Artikel wurde eingetragen
			elseif($this->checked->messageget == 42) {
				$this->content->template['text'] =
					$this->content->template['message_41'] . $this->content->template['message_42'];
			}
			// Etwas fehlt, Überprüfen
			elseif($this->checked->messageget == 48) {
				$this->content->template['text'] =
					$this->content->template['message_41'] . $this->content->template['message_48'];
			}
			// Gelöscht
			elseif($this->checked->messageget == 2) {
				$this->content->template['message_text'] = $this->content->template['message_2'];
			}
			break;

		case "10" :
			// neuen Artikel erstellen
			$this->make_artikel();
			$this->content->template['artikel'] = "ok";
			break;

		case "11" :
			$_SESSION['news_betreff'] = "";
			// Artikel wurde eingetragen
			if($this->checked->messageget == 42) {
				$this->content->template['text'] = $this->content->template['message_42'];
			}
			// Etwas fehlt, Überprüfen
			elseif($this->checked->messageget == 48) {
				$this->content->template['text'] = $this->content->template['message_48'];
			}
			//Gelöscht
			elseif($this->checked->messageget == 2) {
				$this->content->template['text'] =
					$this->content->template['message_41'] . $this->content->template['message_2'];
			}
			// Artikel ändern
			$this->change_artikel();
			#$this->content->template['artikel'] ="ok";
			break;

		case "45" :
			// Artikel-Reihenfolge ändern
			$this->switch_order();
			//extra_key
			$this->content->template['extra_key'] = "Artikel";
			$this->content->template['switch'] = "ok";
			break;
		}

		$this->menu->data_front_complete = $this->menu->menu_data_read("FRONT");
		if($this->cms->categories == 1) {
			// Kategorien rausholen
			$this->menu->preset_categories();
			$this->menu->get_menu_cats();
			$this->content->template['categories'] = 1;
			if(is_array($this->content->template['menulist_data'])) {
				$i = 0;
				foreach($this->content->template['menulist_data'] as $key => $value) {
					if(is_array($this->menu->menu_cats)) {
						foreach($this->menu->menu_cats as $key2 => $value2) {
							if($value['menuid'] == $value2['menuid_cid']) {
								$this->content->template['menulist_data'][$i]['cat_cid'] = $value2['cat_cid'];
								if(empty($this->content->template['menulist_data'][$i]['cat_cid'])) {
									$this->content->template['menulist_data'][$i]['cat_cid'] = 1;
								}
							}
						}
					}
					$i++;
				}
			}
		}
		else {
			$this->content->template['catlist_data'][0] = array("catid" => -1);
			$this->content->template['catlist_data'][1] = array("catid" => -1);
			$this->content->template['catlist_data'][2] = array("catid" => -1);
			$this->content->template['catlist_data'][3] = array("catid" => -1);
			$this->content->template['catlist_data'][4] = array("catid" => -1);
			$this->content->template['catlist_data'][5] = array("catid" => -1);
			$this->content->template['no_categories'] = 1;
		}

		$this->menu->menu_navigation_build("FRONT_COMPLETE", "SITEMAP");
		$this->menu->make_menucats();
	}

	/**
	 * Mit dieser Methode werden die Veröffentlichungseinstellungen einmalig auf die Sprachen übertragen.
	 */
	private function check_publish_lang()
	{
		$oldCsrfState = $this->db->csrfok;
		$this->db->csrfok = true;

		//Check ob umgestellt
		$xsql = array();
		$xsql['dbname'] = "papoo_language_article";
		$xsql['select_felder'] = array("lan_repore_id");
		$xsql['limit'] = "";
		$xsql['where_data'] = array("publish_yn_lang" => 1);
		$result = $this->db_abs->select($xsql);
		if(count($result) < 2) {
			//Alle durchloopen und auf veröffentlichen setzen
			$xsql = array();
			$xsql['dbname'] = "papoo_repore";
			$xsql['select_felder'] = array("reporeID", "publish_yn");
			$xsql['limit'] = "";
			$xsql['where_data_like'] = "LIKE";
			$xsql['where_data'] = array("reporeID" => "%%");
			$result = $this->db_abs->select($xsql);
			if(is_array($result)) {
				foreach($result as $k => $v) {
					$sql = sprintf("UPDATE %s SET publish_yn_lang='%d'
									WHERE lan_repore_id='%d'",
						DB_PRAEFIX . "papoo_language_article",
						$v['publish_yn'],
						$v['reporeID']);
					$this->db->query($sql);
					$sql = sprintf("UPDATE %s SET publish_yn_lang='%d'
									WHERE lan_repore_id='%d'",
						DB_PRAEFIX . "papoo_version_language_article",
						$v['publish_yn'],
						$v['reporeID']);
					$this->db->query($sql);
				}
			}
		}

		$this->db->csrfok = $oldCsrfState;
	}

	/**
	 * @abstract Zwischenspeichern der unfertigen Version entweder per Klick
	 * oder automatisch
	 */
	function make_version_save()
	{
		if(!isset($this->sessionsave) || isset($this->sessionsave) && $this->sessionsave != "no") {
			//make_checked_session_complete
			$sql = sprintf("SELECT user_list_artikel FROM %s WHERE username='%s'",
				$this->cms->papoo_user,
				$this->user->username
			);
			$user_list_artikel = $this->db->get_var($sql);

			$this->make_checked_session_complete();
			$sess_dat = ($_SESSION['metadaten'][$this->unique_id][$this->replangid]);
			if(!empty($this->checked->ubernahme)) {
				$userid = $this->user->userid;
			}
			else {
				$userid = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['dokuser'];
			}
			$reporeidx = $this->db->escape($sess_dat['reporeid']);
			if(empty($sess_dat['reporeid'])) {
				//Max reporeid rausbekommen
				$reporeidx = $this->checked->unique_id;
			}
			$time = date("d.m.Y - H:i:s");
			$fix = "0";
			if(!empty ($this->checked->inhalt_ar['Submit1']) || !empty ($this->checked->inhalt_ar['Submit_zwischen'])) {
				$fix = "1";
				if(!empty ($this->checked->inhalt_ar['Submit_zwischen']) && empty ($this->checked->inhalt_ar['Submit1'])) {
					$fix = "0";
				}
				else {
					$fixleer = "0";
					//Alle Zwischen Einträge löschen
					// ******************************
					//VersioinsTabelle
					$sql = sprintf("DELETE FROM %s WHERE version_art_reporeid='%s' AND  version_art_fix ='%s'",
						$this->cms->tbname['papoo_version_article'],
						$this->db->escape($reporeidx),
						$this->db->escape($fixleer)
					);
					$this->db->query($sql);
					//Sprachen Tabelle
					$sql = sprintf("DELETE FROM %s WHERE lan_repore_id='%s' AND  version_art_fix ='%s'",
						$this->cms->tbname['papoo_version_language_article'],
						$this->db->escape($reporeidx),
						$this->db->escape($fixleer)
					);
					$this->db->query($sql);
					//Lookup Tabelle
					$sql = sprintf("DELETE FROM %s WHERE article_id='%s' AND  version_art_fix ='%s'",
						$this->cms->tbname['papoo_version_lookup_article'],
						$this->db->escape($reporeidx),
						$this->db->escape($fixleer)
					);
					$this->db->query($sql);
					//Lookup Tabelle
					$sql = sprintf("DELETE FROM %s WHERE article_wid_id='%s'",
						$this->cms->tbname['papoo_version_lookup_write_article'],
						$this->db->escape($reporeidx)
					);
					$this->db->query($sql);
					//Daten Tabelle
					$sql = sprintf("DELETE FROM %s WHERE reporeID ='%s' AND  version_art_fix ='%s'",
						$this->cms->tbname['papoo_version_repore'],
						$this->db->escape($reporeidx),
						$this->db->escape($fixleer)
					);
					$this->db->query($sql);
				}
			}
			//Versionsdaten in versionierungs Tabell speichern
			$sql = sprintf("INSERT INTO %s SET version_art_reporeid='%s',version_art_time ='%s',version_art_fix='%s' ",
				$this->cms->tbname['papoo_version_article'],
				$this->db->escape($reporeidx),
				$this->db->escape($time),
				$this->db->escape($fix)
			);
			$this->db->query($sql);

			$insertid_vers = $this->db->insert_id;
			$langar = (array_keys($_SESSION['metadaten'][$this->unique_id]));
			$sqlvar = "INSERT INTO";
			$sqlvar_update1 = " ";
			$menuidid = "10";
			// Eingabe formatieren
			$this->format_eingabe();

			IfNotSetNull($this->checked->cattext);
			$orderid = $this->artikel_get_orderid($this->checked->cattext);

			// Daten eintragen
			$sql = sprintf("$sqlvar %s SET
								count_download='%s',
								cattextid='%d',
								cat_category_id='%d',
								dokuser='%d',
								dokuser_last='%d',
								dokschreibengrid='%s',
								teaser_bild='%s',
								teaser_bild_lr='%s',
								timestamp=NOW(),
								publish_yn='%d',
								teaser='%s',
								teaser_list='%s',
								publish_yn_intra='%d',
								allow_publish='%d',
								teaser_bild_html='%s',
								order_id='%d',
								allow_comment='%d',
								artikel_news_list ='%s',
								teaser_atyn='%s',
								teaser_bild_groesse='%s',
								uberschrift_hyn='%s',
								teaser_menu='%s',
								teaser_sub_menu='%s',
								pub_verfall='%s',
								pub_start='%s',
								pub_verfall_page='%s',
								pub_start_page='%s',
								pub_wohin='%s',
								pub_dauerhaft='%s',
								dok_teaserfix='%d',
								dok_show_teaser_link='%d',
								dok_show_teaser_teaser='%d',
								version_repo_id='%s',
								reporeID='%s',
								version_art_fix ='%s' ",
					$this->cms->tbname['papoo_version_repore'],
					$this->db->escape($sess_dat['zahlen']),
					$this->db->escape($sess_dat['cattext']),
					$this->db->escape($sess_dat['formcatcat']),
					$userid,
					$this->user->userid,
					$this->db->escape($this->dokschreibengrid),
					$this->db->escape($sess_dat['teaserbild']),
					$this->db->escape($sess_dat['bildlr']),
					//$this->db->escape($this->timestamp),
					$this->db->escape($this->publish_yn),
					$this->db->escape($this->teaserfertig),
					$this->db->escape($sess_dat['freigabe_teaser_list']),
					$this->db->escape($this->publish_yn_intra),
					//$this->db->escape($this->intranet_yn),
					$this->db->escape($this->allow_publish),
					$this->db->escape($this->teaserfertig),
					$this->db->escape($orderid),
					$this->db->escape($this->allow_comment),
					$this->db->escape($sess_dat['newsaktiv']),
					$this->db->escape($sess_dat['atyn']),
					$this->db->escape($this->teaser_bild_groesse),
					$this->db->escape($sess_dat['hyn']),
					$this->db->escape($sess_dat['cattextnews']),
					$this->db->escape($sess_dat['unter']),
					$this->db->escape($sess_dat['pub_verfall']),
					$this->db->escape($sess_dat['pub_start']),
					$this->db->escape($sess_dat['pub_verfall_page']),
					$this->db->escape($sess_dat['pub_start_page']),
					$this->db->escape($sess_dat['pub_wohin']),
					$this->db->escape($sess_dat['pub_dauerhaft']),
					$this->db->escape($sess_dat['dok_teaserfix']),
					$this->db->escape($sess_dat['dok_show_teaser_link']),
					$this->db->escape($sess_dat['dok_show_teaser_teaser']),
					$this->db->escape($insertid_vers),
					$this->db->escape($reporeidx),
					$this->db->escape($fix)
				) . $sqlvar_update1;
			$this->db->query($sql);
			$i = 0;
			// Inhalte eintragen nach Sprache
			foreach($_SESSION['metadaten'][$this->unique_id] as $ses) {
				$inhalt = $ses['inhalt'];
				if($langar[$i] == 0) {
					//$langar[$i]=$this->cms->lang_id;
					$langar[$i] = $this->replangid;
				}
				// alle Addons durchführen für den Text
				$artikel = $this->replace->do_replace($inhalt); // OK !?!
				// HTML bereinigen
				$artikel = $this->html->do_tidy($artikel);
				//$artikel = html_entity_decode($artikel);
				// Im Donwloadlink die menuid korrekt setzen,
				//damit der richtige Artikel neu geladen wird lan_rss_yn
				$artikel = str_ireplace("ppmenuidpp", $ses['cattext'], $artikel);
				$this->make_teaser_bild();
				$sql = sprintf("INSERT INTO %s SET
						lan_article='%s',
						lan_article_sans='%s',
						lan_teaser='%s',
						url_header='%s',
						lan_teaser_link='%s',
						header='%s',
						lan_repore_id='%d',
						lang_id='%d',
						lan_metatitel='%s',
						lan_metadescrip='%s',
						lan_metakey='%s',
						lan_rss_yn='%s',
						version_langart_id='%s',
						version_art_fix ='%s' ,
						publish_yn_lang = '%d'",
					$this->cms->tbname['papoo_version_language_article'],
					$this->db->escape($artikel),
					//$this->db->escape($this->inhalt),
					$this->db->escape($inhalt),
					$this->db->escape($ses['teaser']),
					$this->db->escape($ses['url_header']),
					$this->db->escape($ses['teaser_link']),
					$this->db->escape($ses['uberschrift']),
					$this->db->escape($reporeidx),
					$this->db->escape($langar[$i]),
					$this->db->escape($ses['metatitel']),
					$this->db->escape($ses['metadescrip']),
					$this->db->escape($ses['metakey']),
					$this->db->escape($ses['lan_rss_yn']),
					$this->db->escape($insertid_vers),
					$this->db->escape($fix),
					$this->db->escape($ses['publish_yn_lang'])
				);
				$i++;
				$this->db->query($sql);
			}

			if(!is_array($sess_dat['gruppe_write'])) {
				$sess_dat['gruppe_write'] = array();
			}
			//Leserechte setzen
			$sess_dat['gruppe_write'][] = 1;
			foreach($sess_dat['gruppe_write'] as $insert) {
				$sqlin = "INSERT INTO " . $this->cms->tbname['papoo_version_lookup_article'] .
					" SET article_id='" . $this->db->escape($reporeidx) .
					"', gruppeid_id='" . $this->db->escape($insert) .
					"', version_lookup_article_id='" . $this->db->escape($insertid_vers) .
					"',version_art_fix ='" . $this->db->escape($fix) . "' ";
				$this->db->query($sqlin);
			}
			//Schreibrechte setzen
			$sess_dat["write_article"] = is_array($sess_dat["write_article"]) ? $sess_dat["write_article"] : [];
			$sess_dat['write_article'][] = 1;
			foreach($sess_dat['write_article'] as $insert) {
				$sqlin = "INSERT INTO " . $this->cms->tbname['papoo_version_lookup_write_article'] .
					" SET article_wid_id='" . $this->db->escape($reporeidx) .
					"', gruppeid_wid_id='" . $this->db->escape($insert) .
					"', version_lookup_article_id='" . $this->db->escape($insertid_vers) .
					"',version_art_fix ='" . $this->db->escape($fix) . "' ";
				$this->db->query($sqlin);
			}
			//Lookup Daten für die Menüpunkte löschen.
			$sql = "DELETE FROM " . $this->cms->tbname['papoo_version_lookup_art_cat'] . " WHERE lart_id='" . $this->db->escape($insertid_vers) . "'";
			$this->db->query($sql);
			//Lookup Daten für die Menüpunkte eintragen.
			if(is_array($sess_dat['cattext_ar'])) {
				$sess_dat['cattext_ar'] = array_unique($sess_dat['cattext_ar']);
				foreach($sess_dat['cattext_ar'] as $catss) {
					$sqlin = "INSERT INTO " . $this->cms->tbname['papoo_version_lookup_art_cat'] .
						" SET lart_id='$insertid_vers', lcat_id='$catss' ";
					$this->db->query($sqlin);
				}
			}
		}
		if(!isset($_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_link'])) {
			$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_link'] = NULL;
		}
	}

	/**
	 * Verzeichnisse rauskriegen bzw. Menünamen für mod_rewrite mit sprechurls
	 *
	 * @param string $menid
	 * @return boolean|void
	 */
	function get_verzeichnis($menid = "")
	{

		if(!empty($menid)) {
			$mendata = $this->menu->data_front_complete;
			foreach($mendata as $menuitems) {
				//aktuelle menid finden
				if($menid == $menuitems['menuid']) {
					$this->m_url[] = ($menuitems['url_menuname']);
					//Nicht oberste Ebene, neu aufrufen
					if($menuitems['untermenuzu'] != 0) {
						$this->get_verzeichnis($menuitems['untermenuzu']);
					}
					else {
						return true;
					}
				}
			}
		}
	}

	/**
	 * Endgültiges Abspeichern
	 *
	 * @param $modus
	 */
	function make_save($modus)
	{
		$this->make_checked_session_complete();
		if(empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['url_header'])) {
			$_SESSION['metadaten'][$this->unique_id][$this->replangid]['url_header'] =
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['uberschrift'];
		}
		if(empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['url_header'])) {
			$_SESSION['metadaten'][$this->unique_id][$this->replangid]['url_header'] = md5(time());
		}
		$sql = sprintf("SELECT user_list_artikel FROM %s WHERE username='%s'",
			$this->cms->papoo_user,
			$this->user->username
		);
		$user_list_artikel = $this->db->get_var($sql);

		$sess_dat = $_SESSION['metadaten'][$this->unique_id][$this->replangid];
		if(empty($sess_dat['reporeid'])) {
			$sess_dat['reporeid'] = $this->checked->reporeid;
		}
		if(!empty($this->checked->ubernahme)) {
			$userid = $this->user->userid;
		}
		else {
			$userid = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['dokuser'];
		}
		if($userid < 1) {
			$userid = $this->user->userid;
		}
		//je nach Modus verfahren
		if($modus == "INSERT") {
			$langar = (array_keys($_SESSION['metadaten'][$this->unique_id]));
			$sqlvar = "INSERT INTO";
			$sqlvar_update1 = " ";
			$menuidid = "10";
			$orderid = $this->db->escape($this->artikel_get_orderid($this->checked->cattext));
			$orderid_start = $this->artikel_get_orderid(1);
		}
		if($modus == "UPDATE") {
			$langar = (array_keys($_SESSION['metadaten'][$this->unique_id]));
			$sqlvar = "UPDATE";
			$sqlvar_update1 = " WHERE reporeID='" . $this->db->escape($sess_dat['reporeid']) . "' LIMIT 1";
			//Links ersetzen
			$i = 0;
			// Inhalte eintragen nach Sprache
			foreach($_SESSION['metadaten'][$this->unique_id] as $ses) {
				$inhalt = $ses['inhalt'];
				if($langar[$i] == 0) {
					$langar[$i] = $this->replangid;
				}
				$langid = $lang = $langar[$i];
				$link = "";
				$i++;
				$this->m_url = array();
				$this->get_verzeichnis($sess_dat['cattext']);
				$this->m_url = array_reverse($this->m_url);
				foreach($this->m_url as $dat) {
					$link .= $dat . "/";
				}
				$sql = sprintf("SELECT url_header FROM %s WHERE lan_repore_id='%s' AND lang_id='%s'",
					$this->cms->tbname['papoo_language_article'],
					$this->db->escape($sess_dat['reporeid']),
					$this->db->escape($lang)
				);
				$link2 = $link;
				$link .= $urlh_alt = $this->db->get_var($sql);
				$sql = sprintf("SELECT url_menuname FROM %s
								WHERE menuid_id='%s' AND lang_id='%d' ",
					$this->cms->papoo_menu_language,
					$this->db->escape($sess_dat['cattext']),
					$this->db->escape($lang)
				);
				$file_surls = $this->db->get_var($sql);
				//Nur beim updaten aktivieren
				if($modus == "UPDATE") {
					//Name Übergeben
					$name = $ses['url_header'];
					$varcount = "";
					//Checken ob die sprechende url schon existiert cattextid='%s' AND
					$sql = sprintf("SELECT COUNT(reporeID) FROM %s, %s WHERE  url_header='%s' AND reporeID!='%s' AND lan_repore_id=reporeID",
						$this->cms->tbname['papoo_repore'],
						$this->cms->tbname['papoo_language_article'],
						$this->db->escape($sess_dat['cattext']),
						$this->db->escape($urlh_alt)
					);
					$varcount = $this->db->get_var($sql);
					if($varcount > 1) {
						// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
						IfNotSetNull($insertid);

						$varcount = "-" . $langar[$i] . "-" . $insertid;
					}
					else {
						$varcount = "";
					}
					//Link in Artikel suchen
					$sql = sprintf("SELECT * FROM %s WHERE lan_article LIKE '%s' AND lang_id='%s'",
						$this->cms->papoo_language_article,
						"%href=\"" . PAPOO_WEB_PFAD . "/" . $this->cms->webvar . $this->db->escape($link) . ".html%",
						$langid
					);
					$result = $this->db->get_results($sql);
					//der alte Link
					$such = ("href=\"" . PAPOO_WEB_PFAD . "/" . $this->cms->webvar . ($link)) . ".html";
					//der neue Link
					$replace = ("href=\"" . PAPOO_WEB_PFAD . "/" . $this->cms->webvar . $link2 .
							$this->menu->replace_uml(strtolower($name . $varcount))) . ".html";
					//Link in Artikel ersetzen und eintragen
					if(!empty($result)) {
						foreach($result as $dat) {
							//Für jeden Eintrag ersetzen
							$dat->lan_article = str_ireplace($such, $replace, $dat->lan_article);
							$dat->lan_article_sans = str_ireplace($such, $replace, $dat->lan_article_sans);
							//Sql Statement zur Ersetzung
							$sql = sprintf("UPDATE %s
											SET lan_article='%s', lan_article_sans='%s'
											WHERE lan_repore_id='%d' AND lang_id='%d' LIMIT 1",
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
					$sql = sprintf("SELECT  * FROM %s WHERE article LIKE '%s' AND lang_id='%s'",
						$this->cms->tbname['papoo_language_collum3'],
						"%href=\"" . PAPOO_WEB_PFAD . "/" . $this->cms->webvar . $this->db->escape($link) . "%",
						$langid
					);
					$result = $this->db->get_results($sql);
					//Link in 3. Spalte ersetzen
					if(!empty($result)) {
						foreach($result as $dat) {
							//Für jeden Eintrag ersetzen
							$dat->article = str_ireplace($such, $replace, $dat->article);
							$dat->article_sans = str_ireplace($such, $replace, $dat->article_sans);
							//Sql Statement zur Ersetzung
							$sql = sprintf("UPDATE %s
											SET article='%s', article_sans='%s'
											WHERE collum_id='%d' AND lang_id='%d' LIMIT 1",
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
					$sql = sprintf("SELECT * FROM %s WHERE lang_id='%s'",
						$this->cms->tbname['papoo_language_stamm'],
						$lang
					);
					$result = $this->db->get_results($sql);
					//Link in Startseite ersetzen
					if(!empty($result)) {
						foreach($result as $dat) {
							//Für jeden Eintrag ersetzen
							$dat->start_text = str_ireplace($such, $replace, $dat->start_text);
							$dat->start_text_sans = str_ireplace($such, $replace, $dat->start_text_sans);
							//Sql Statement zur Ersetzung
							$sql = sprintf("UPDATE %s
											SET start_text='%s', start_text_sans='%s'
											WHERE stamm_id='%d' AND lang_id='%d' LIMIT 1",
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
			// alte Einträge löschen
			foreach($langar as $lang) {
				$sql = "DELETE FROM " . $this->cms->papoo_language_article .
					" WHERE lan_repore_id='" . $this->db->escape($sess_dat['reporeid']) .
					"' AND lang_id='" . $this->db->escape($lang) . "'";
				$this->db->query($sql);
			}
			// alte Einträge löschen (einfacher als updaten)
			$delsql = "DELETE FROM " . $this->cms->papoo_lookup_article .
				" WHERE article_id='" . $this->db->escape($sess_dat['reporeid']) . "' ";
			$this->db->query($delsql);
			// alte Einträge löschen (einfacher als updaten)
			$delsql = "DELETE FROM " . $this->cms->tbname['papoo_lookup_write_article'] .
				" WHERE article_wid_id='" . $this->db->escape($sess_dat['reporeid']) . "' ";
			$this->db->query($delsql);
			// Order-Informationen etc. von original-Artikel laden
			$sql_o = sprintf("SELECT order_id, order_id_start, cattextid, teaser_list FROM %s WHERE reporeid='%s'",
				$this->cms->tbname['papoo_repore'],
				$this->db->escape($sess_dat['reporeid'])
			);
			$result_o = $this->db->get_results($sql_o);
			// Wenn sich die Zugehörigkeit zu Menü-Punkt geändert hat:
			if($result_o[0]->cattextid != $sess_dat['cattext']) {
				// neue Order-IDs holen
				$orderid = $this->db->escape($this->artikel_get_orderid($sess_dat['cattext']));
				$orderid_start = $this->artikel_get_orderid(1);
			}
			else {
				// alte Order-IDs behalten
				$orderid = $this->db->escape($result_o[0]->order_id);
				$orderid_start = $this->db->escape($result_o[0]->order_id_start);
			}
			// Wenn Artikel nicht auf Startseite kommt (kein Teaser-Listing und nicht zu Startseite gehört)
			if(!$sess_dat['teaser_list'] && $sess_dat['cattext'] <= 1) {
				$orderid_start = 0;
			}
		}
		// Eingabe formatieren
		$this->format_eingabe();
		if(empty($sess_dat['pub_verfall'])) {
			$sess_dat['pub_verfall'] = 0;
		}
		if(empty($sess_dat['pub_start'])) {
			$sess_dat['pub_start'] = 0;
		}
		if(empty($sess_dat['pub_verfall_page'])) {
			$sess_dat['pub_verfall_page'] = 0;
		}
		if(empty($sess_dat['pub_start_page'])) {
			$sess_dat['pub_start_page'] = 0;
		}
		if(empty($sess_dat['pub_wohin'])) {
			$sess_dat['pub_wohin'] = 0;
		}
		if(empty($sess_dat['pub_dauerhaft'])) {
			$sess_dat['pub_dauerhaft'] = 0;
		}
		// Daten eintragen
		$sql = sprintf("$sqlvar %s
						SET
						count_download='%s',
						cattextid='%d',
						cat_category_id='%d',
						dokuser='%d',
						dokuser_last='%d',
						dokschreibengrid='%s',
						teaser_bild='%s',
						teaser_bild_lr='%s',
						timestamp=NOW(),
						publish_yn='%d',
						teaser='%s',
						teaser_list='%s',

						publish_yn_intra='%d',
						allow_publish='%d',
						teaser_bild_html='%s',
						order_id='%d',
						order_id_start='%s',
						allow_comment='%d',
						artikel_news_list ='%s',
						teaser_atyn='%s',
						teaser_bild_groesse='%s',
						stamptime='%s',

						uberschrift_hyn='%s',
						teaser_menu='%s',
						pub_verfall='%s',
						pub_start='%s',
						pub_verfall_page='%s',
						pub_start_page='%s',
						pub_wohin='%s',
						pub_dauerhaft='%s',
						dok_teaserfix='%d',
						dok_show_teaser_link='%d',
						dok_show_teaser_teaser='%d',
						teaser_sub_menu='%s' ",
				$this->cms->tbname['papoo_repore'],
				$this->db->escape($sess_dat['zahlen']),
				$this->db->escape($sess_dat['cattext']),
				$this->db->escape($sess_dat['formcatcat']),
				$userid,
				$this->user->userid,
				$this->db->escape($this->dokschreibengrid),
				$this->db->escape($sess_dat['teaserbild']),
				$this->db->escape($sess_dat['bildlr']),
				//$this->db->escape($this->timestamp),
				$this->db->escape($this->publish_yn),
				$this->db->escape($this->teaserfertig),
				$this->db->escape($sess_dat['freigabe_teaser_list']),
				$this->db->escape($this->publish_yn_intra),
				//$this->db->escape($this->intranet_yn),
				$this->db->escape($this->allow_publish),
				$this->db->escape($this->teaserfertig),
				$this->db->escape($orderid),
				$this->db->escape($orderid_start),
				$this->db->escape($this->allow_comment),
				$this->db->escape($sess_dat['newsaktiv']),
				$this->db->escape($sess_dat['atyn']),
				$this->db->escape($this->teaser_bild_groesse),
				time(),
				$this->db->escape($sess_dat['hyn']),
				$this->db->escape($sess_dat['cattextnews']),
				$this->db->escape($sess_dat['pub_verfall']),
				$this->db->escape($sess_dat['pub_start']),
				$this->db->escape($sess_dat['pub_verfall_page']),
				$this->db->escape($sess_dat['pub_start_page']),
				$this->db->escape($sess_dat['pub_wohin']),
				$this->db->escape($sess_dat['pub_dauerhaft']),
				$this->db->escape($sess_dat['dok_teaserfix']),
				$this->db->escape($sess_dat['dok_show_teaser_link']),
				$this->db->escape($sess_dat['dok_show_teaser_teaser']),
				$this->db->escape($sess_dat['unter'])
			) . $sqlvar_update1;
		$this->db->query($sql);
		$insertid = $this->db->insert_id;
		if($modus == "UPDATE") {
			$insertid = $sess_dat['reporeid'];
			// Order-IDs des alten Menü-Punkts, des aktuellen Menü-Punkts und der Startseite anpassen
			$this->artikel_reorder($result_o[0]->cattextid);
			$this->artikel_reorder($sess_dat['cattext']);
			$this->artikel_reorder(1);
		}
		if($modus == "INSERT" && !$this->checked->erstellungsdatum) {
			$sql = "UPDATE " . $this->cms->papoo_repore . " SET erstellungsdatum=NOW() WHERE reporeID='" . $insertid . "' ";
			$this->db->query($sql);
		}
		//Erstellungsdatum ändern, derzeit nur DE
		else {
			$split1 = explode(" ", $this->checked->erstellungsdatum);
			$split_datum = explode(".", $split1['0']);
			if(is_numeric($split_datum['0']) &&
				is_numeric($split_datum['1']) &&
				is_numeric($split_datum['2']) &&
				strlen($split_datum['0']) == 2 &&
				strlen($split_datum['1']) == 2 &&
				strlen($split_datum['2']) == 4) {
				$datum1 = $split_datum['2'] . "-" . $split_datum['1'] . "-" . $split_datum['0'];
			}
			$split_datum = explode(":", $split1['1']);
			if(is_numeric($split_datum['0']) &&
				is_numeric($split_datum['1']) &&
				is_numeric($split_datum['2']) &&
				strlen($split_datum['0']) == 2 &&
				strlen($split_datum['1']) == 2 &&
				strlen($split_datum['2']) == 2) {
				$datum2 = $split_datum['0'] . ":" . $split_datum['1'] . ":" . $split_datum['2'];
			}
			$erstellungsdatum = $datum1 . " " . $datum2;
			if(strlen($erstellungsdatum) == 19) {
				$sql = "UPDATE " . $this->cms->papoo_repore . " SET erstellungsdatum='" . $erstellungsdatum . "' WHERE reporeID='" . $insertid . "' ";
				$this->db->query($sql);
			}
		}
		global $mehrstufige_freigabe;
		if(is_object($mehrstufige_freigabe) && $mehrstufige_freigabe->aktiv) {
			$mehrstufige_freigabe->freigabe_eintragen($insertid);
		}
		//Wenn der Chef benachrichtigt werden muß
		if(isset($this->chef_message) && $this->chef_message == "yes") {
			$this->make_chef_message($insertid);
		}
		$i = 0;
		// Inhalte eintragen nach Sprache
		foreach($_SESSION['metadaten'][$this->unique_id] as $ses) {
			$inhalt = $ses['inhalt'];
			if($langar[$i] == 0) {
				//$langar[$i]=$this->cms->lang_id;
				$langar[$i] = $this->replangid;
			}
			$varcount = "";
			//Checken ob die Überschrift schon existiert
			$sql = sprintf("SELECT COUNT(reporeID) FROM %s,%s WHERE cattextid='%s' AND url_header='%s' AND reporeID!='%d' ",
				$this->cms->tbname['papoo_repore'],
				$this->cms->tbname['papoo_language_article'],
				$this->db->escape($sess_dat['cattext']),
				$this->db->escape($ses['url_header']),
				$insertid
			);
			$varcount = $this->db->get_var($sql);
			if($varcount >= 1) {
				#$varcount=$varcount+1;
				$varcount = "-" . $langar[$i] . "-" . $insertid;
			}
			else {
				$varcount = "";
			}
			// alle Addons durchführen für den Text
			$artikel = $this->replace->do_replace($inhalt); // OK !?!
			// HTML bereinigen
			$artikel = $this->html->do_tidy($artikel);
			//$artikel = html_entity_decode($artikel);
			// Im Donwloadlink die menuid korrekt setzen,
			//damit der richtige Artikel neu geladen wird
			$artikel = str_ireplace("ppmenuidpp", $ses['cattext'], $artikel);
			$this->make_teaser_bild($langar[$i]);
			if($this->cms->mod_free != 1) {
				$ses['url_header'] = $this->menu->replace_uml(strtolower($ses['url_header'])) . $varcount;
			}
			else {
				$ses['url_header'] = $this->menu->replace_uml(strtolower($ses['url_header']));
				if(stristr($ses['url_header'], ".html")) {
					$ses['url_header'] = str_replace(".html", $varcount . ".html", $ses['url_header']);
				}
				elseif($ses['url_header'][strlen($ses['url_header']) - 1] == "/") {
					$ses['url_header'] = substr($ses['url_header'], 0, strlen($ses['url_header']) - 1) . $varcount . "/";
				}
				$varcount = "";
			}
			/**
			 * Korrekturen Inhalte
			 */
			if(empty($ses['metatitel'])) {
				$ses['metatitel'] = $ses['uberschrift'];
			}
			if(empty($ses['metadescrip'])) {
				$ses['metadescrip'] = substr(strip_tags($ses['teaser']), 0, 150) . "...";
			}
			if(empty($ses['metadescrip']) || $ses['metadescrip'] == "...") {
				$ses['metadescrip'] = trim(strip_tags(substr($artikel, 0, 150))) . "...";
			}
			if($this->cms->mod_free == 1) {
				if(empty($ses['url_header'])) {
					$ses['url_header'] = "/" . substr($ses['uberschrift'], 0, 60) . ".html";
				}
				if(!stristr($ses['url_header'], ".html") &&
					!stristr($ses['url_header'], ".shtml") &&
					$ses['url_header'][strlen($ses['url_header']) - 1] != "/") {
					//auskommentieren wenn mit .html am Ende gewünscht bei Artikeln
					if($this->cms->artikel_url_mit_html) {
						$ses['url_header'] = $ses['url_header'] . ".html";
					}
					else {
						$ses['url_header'] = $ses['url_header'] . ".html";
					}
				}
				if($ses['url_header'][0] != "/") {
					$ses['url_header'] = "/" . $ses['url_header'];
				}
			}
			else {
				if(empty($ses['url_header'])) {
					$ses['url_header'] = substr($ses['uberschrift'], 0, 60);
				}
			}
			$ses['url_header'] = $this->check_url_header($ses['url_header'] . $varcount, $langar[$i]);
			$sql = sprintf("INSERT INTO %s
							SET
							lan_article='%s',
							lan_article_sans='%s',
							lan_teaser='%s',
							url_header='%s',
							lan_teaser_link='%s',
							header='%s',
							lan_repore_id='%d',
							lang_id='%d',
							lan_metatitel='%s',
							lan_metadescrip='%s',
							lan_rss_yn='%s',
							lan_teaser_img_fertig='%s',
							lan_metakey='%s',
							lan_article_search='%s',
							publish_yn_lang='%d'
							",
				$this->cms->tbname['papoo_language_article'],
				$this->db->escape($artikel),
				//$this->db->escape($this->inhalt),
				$this->db->escape($inhalt),
				$this->db->escape($ses['teaser']),
				$this->db->escape($ses['url_header']),
				$this->db->escape($ses['teaser_link']),
				$this->db->escape($ses['uberschrift']),
				$insertid,
				$langar[$i],
				$this->db->escape($ses['metatitel']),
				$this->db->escape($ses['metadescrip']),
				$this->db->escape($ses['lan_rss_yn']),
				$this->db->escape($this->teaserbild_in),
				$this->db->escape($ses['metakey']),
				$this->db->escape($this->diverse->searchfield_text($ses['url_header'] . " " . $ses['teaser'] . " " . $artikel)),
				$this->db->escape($ses['publish_yn_lang'])
			);
			$i++;
			$this->db->query($sql);
			//RSS Geschichte initiaisieren
			if($ses['lan_rss_yn'] == 1 && $this->allow_internet == 1) {
				global $rssfeed;
				if(is_object($rssfeed)) {
					$rssfeed->create_feed("now");
				}
			}
		}
		//Nach dem Eintrag die Bildergeschichte machen
		$this->image_new_thumb($sess_dat, $insertid);
		//Schreibrechte setzen
		if(!is_array($sess_dat['gruppe_write'])) {
			$sess_dat['gruppe_write'] = array();
		}
		// Adminstratoren dürfen IMMER lesend auf Artikel zugreifen
		$sess_dat['gruppe_write'][] = 1;
		$this->db->hide_errors();
		foreach($sess_dat['gruppe_write'] as $insert) {
			$sqlin = "INSERT INTO " . $this->cms->papoo_lookup_article . " SET article_id='" . $this->db->escape($insertid) .
				"', gruppeid_id='" . $this->db->escape($insert) . "' ";
			@$this->db->query($sqlin);
		}
		// Adminstratoren dürfen IMMER schreibend auf Artikel zugreifen
		$sess_dat["write_article"] = is_array($sess_dat["write_article"]) ? $sess_dat["write_article"] : [];
		$sess_dat['write_article'][] = 1;
		foreach($sess_dat['write_article'] as $insert) {
			$insertid = $this->db->escape($insertid);
			$insert = $this->db->escape($insert);
			$sqlin = "INSERT INTO " . $this->cms->tbname['papoo_lookup_write_article'] . " SET article_wid_id='" . $insertid .
				"', gruppeid_wid_id='" . $insert . "' ";
			@$this->db->query($sqlin);
		}

		$sql = sprintf("SELECT * FROM %s WHERE lart_id='%d'",
			$this->cms->tbname['papoo_lookup_art_cat'],
			$this->db->escape($insertid)
		);
		$men_ids = $this->db->get_results($sql);
		$men_ids_org = $this->db->get_results($sql, ARRAY_A);

		//Cache Dateien entfernen
		$this->remove_cache_file(PAPOO_ABS_PFAD, $file_surls, $men_ids);

		//Lookup Daten für die Menüpunkte löschen.
		$sql = sprintf("DELETE FROM %s WHERE lart_id='%d'",
			$this->cms->tbname['papoo_lookup_art_cat'],
			$this->db->escape($insertid)
		);
		$this->db->query($sql);

		//Lookup Daten für die Menüpunkte eintragen.
		if(is_array($sess_dat['cattext_ar'])) {
			$sess_dat['cattext_ar'] = array_unique($sess_dat['cattext_ar']);
			if(is_array($sess_dat['cattext_ar'])) {
				foreach($sess_dat['cattext_ar'] as $catss) {
					if(is_array($men_ids_org)) {
						foreach($men_ids_org as $org_m) {
							if($org_m['lcat_id'] == $catss) {
								$order_id = $org_m['lart_order_id'];
							}
						}
					}
					//Wenn keine orderid vorhanden ist
					if(empty($order_id)) {
						//Wenn nach Aktualität dann sollte die orderid =0 sein
						if($this->cms->stamm_artikel_order != 1) {
							$sql = sprintf("SELECT MAX(lart_order_id) FROM %s WHERE lcat_id='%d'",
								$this->cms->tbname['papoo_lookup_art_cat'],
								$this->db->escape($catss)
							);
							$order_id = $this->db->get_var($sql) + 10;
						}
						//Wenn nach Orderid dann Order nach hinten.
						else {
							$order_id = 1;
						}
					}
					$sqlin = "INSERT INTO " . $this->cms->tbname['papoo_lookup_art_cat'] .
						" SET lart_id='" . $insertid .
						"', lcat_id='" . $catss .
						"', lart_order_id='" . $this->db->escape($order_id) . "' ";
					@$this->db->query($sqlin);
					$this->artikel_reorder($catss);
					if($sess_dat['cattext'] == $catss) {
						$exitscat = 1;
					}
				}
			}
		}
		$this->artikel_reorder(1);
		if($sess_dat['cattext'] >= 1 && $exitscat != 1) {
			$sqlin = "INSERT INTO " . $this->cms->tbname['papoo_lookup_art_cat'] .
				" SET lart_id='" . $insertid .
				"', lcat_id='" . $sess_dat['cattext'] .
				"', lart_order_id='" . $orderid . "' ";
			@$this->db->query($sqlin);
			$this->artikel_reorder($sess_dat['cattext']);
		}
		if($sess_dat['cattext'] < 1 && $exitscat != 1 && !is_array($sess_dat['cattext_ar'])) {
			$sqlin = "INSERT INTO " . $this->cms->tbname['papoo_lookup_art_cat'] . " SET lart_id='" . $insertid . "', lcat_id='1' ";
			@$this->db->query($sqlin);
			$this->artikel_reorder(1);
		}
		unset($_SESSION['metadaten'][$this->unique_id]);
		unset($_SESSION['back_aktu_article_edit_langid']);
		$sql = sprintf("SELECT lang_short FROM %s WHERE lang_id='%d'",
			DB_PRAEFIX . "papoo_name_language",
			$this->db->escape($this->cms->lang_id)
		);
		$front_lang_short = $this->db->get_var($sql);
		//Wenn was aus dem Frontend kommt wieder dahin
		if(!empty($_SESSION['from']) && empty ($this->checked->inhalt_ar['Submit_zwischen'])) {
			$location_url =
				"../index.php?menuid=" . $_SESSION['f_menuid'] . "&reporeid=" . $this->checked->reporeid . "&getlang=" . $front_lang_short;
			unset($_SESSION['from']);
			unset($_SESSION['f_langid']);
			unset($_SESSION['f_menuid']);
		}
		else {
			IfNotSetNull($menuidid);
			$location_url = "./artikel.php?menuid=11&messageget=" . $menuidid;
		}
		if(!empty ($this->checked->inhalt_ar['Submit_zwischen'])) {
			$location_url = "./artikel.php?menuid=11&reporeid=" . $this->checked->reporeid;
		}
		if(isset($_SESSION['debug_stopallredirect']) && $_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">Weiter</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}

	/**
	 * @param $url
	 * @param $langid
	 * @return mixed|string
	 */
	function check_url_header($url, $langid)
	{
		if($this->cms->mod_free == 1) {
			$sql = sprintf("SELECT lang_short FROM %s WHERE lang_id='%s'",
				$this->cms->tbname['papoo_name_language'],
				$this->db->escape($langid)
			);
			$lang_short = $this->db->get_var($sql);
			//Nur Bei NICHT Standardsprache
			if($lang_short != $this->content->template['lang_front_default']) {
				if(!stristr($url, "/" . $lang_short . "/")) {
					//DIe neue url
					$url = "/" . $lang_short . "" . $url;
				}
			}
			$klammer_id = "" . time();
			$last = substr($url, -1, 1);
			$last2 = substr($url, -5, 5);
			$sql = sprintf("SELECT COUNT(lan_repore_id) FROM %s WHERE url_header='%s'",
				$this->cms->tbname['papoo_language_article'],
				$this->db->escape($url)
			);
			$result = $this->db->get_var($sql);
			if($result > 0) {
				//Artikel mit .html
				if($last2 == ".html") {
					if($this->cms->artikel_url_mit_html) {
						$url = str_replace(".html", "-" . $klammer_id . ".html", $url);
					}
				}
				//Artikel mit /
				if($last == "/") {
					$url = substr($url, 0, -1);
					$url = $url . "-" . $klammer_id . "/";
				}
				else {
					if(!$this->cms->artikel_url_mit_html) {
						$url = $url . "-" . $klammer_id;
					}
				}
				if($last != "/" && $last2 != ".html") {
					$url = $url . ".html";
				}
				$url = str_replace("?", "", $url);
				$url = str_replace("&", "", $url);
				return $url;
			}
			$sql = sprintf("SELECT COUNT(menuid_id) FROM %s WHERE url_menuname='%s'",
				$this->cms->tbname['papoo_menu_language'],
				$this->db->escape($url)
			);
			$result = $this->db->get_var($sql);
			if($result > 0) {
				//Artikel mit .html
				if($last2 == ".html") {
					if($this->cms->artikel_url_mit_html) {
						$url = str_replace(".html", "-" . $klammer_id . ".html", $url);
					}
				}
				//Artikel mit /
				if($last == "/") {
					$url = substr($url, 0, -1);
					$url = $url . "-" . $klammer_id . "/";
				}
				else {
					if(!$this->cms->artikel_url_mit_html) {
						$url = $url . "-" . $klammer_id;
					}
				}
				if($last != "/" && $last2 != ".html") {
					$url = $url . ".html";
				}
				$url = str_replace("?", "", $url);
				$url = str_replace("&", "", $url);
				return $url;
			}
			if($last != "/" && $last2 != ".html") {
				$url = $url . ".html";
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
	 * intern_artikel::remove_cache_file()
	 * Cache Dateien entfernen
	 *
	 * @param string $pfad
	 * @param string $url
	 * @param array  $men_ids
	 */
	function remove_cache_file($pfad = "", $url = "", $men_ids = array())
	{
		$pfad = $pfad . "/cache/";
		$extension = ".html";
		if($pfad && $men_ids) {
			$handle = @opendir($pfad);
			while(false !== ($file = @readdir($handle))) {
				switch($extension) {
				default:
					if(!empty($extension)) {
						if(stristr($file, $extension)) {
							@unlink($pfad . $file);
						}
					}
					foreach($men_ids as $ids) {
						if(stristr($file, "menuid_" . $ids->lcat_id)) {
							@unlink($pfad . $file);
						}
						if(@stristr($file, $url)) {
							@unlink($pfad . $file);
						}
					}
					break;
				}
			}
			@closedir($handle);
		}
	}

	/**
	 * Wenn man nicht veröffentlichen darf, den Chef benachrichtigen
	 *
	 * @param $insertid
	 *
	 * @throws phpmailerException
	 */
	function make_chef_message($insertid)
	{
		//An wen muss die Mail gehen
		$sql = sprintf("SELECT publish_user FROM %s, %s WHERE  userid='%s' AND gruppenid=gruppeid AND gruppenid!='10'",
			$this->cms->tbname['papoo_lookup_ug'],
			$this->cms->tbname['papoo_gruppe'],
			$this->user->userid
		);
		$userto = $this->db->get_var($sql);
		if($userto == 0) {
			$userto = 10;
		}
		$inhalt = "Der folgende Artikel soll veroeffentlicht werden, bitte ueberpruefen und freigeben:
    		<br />
    		<a href=\"./artikel.php?menuid=11&reporeid=" . $insertid . "\">Artikel oeffnen</a>";
		$sql = sprintf("INSERT INTO %s SET mail_head='%s', mail_text='%s', mail_from_user='%s', mail_to_user='%s', mail_date=NOW()",
			$this->cms->papoo_mail,
			"Artikel muss veroeffentlicht werden",
			$this->db->escape($inhalt),
			$this->user->userid,
			$this->db->escape($userto)
		);
		// Abfrage in die Datenbank
		$this->db->query($sql);
		if($this->cms->stamm_benach_artikelfreigabe_email) {
			global $mail_it;
			global $webverzeichnis;
			global $db_praefix;
			$temp_email_to = $this->cms->admin_email;
			$sql = sprintf("SELECT * FROM %s WHERE userid='%d'",
				$db_praefix . "papoo_user",
				$userto
			);
			$temp_user_data = $this->db->get_results($sql, ARRAY_A);
			if(!empty($temp_user_data)) {
				$temp_email_to = $temp_user_data[0]['email'];
			}
			$mail_it->to = $temp_email_to;
			$mail_it->from = $this->cms->admin_email;
			$mail_it->subject = "Artikel zu pruefen / veroeffentlichen auf " . $this->cms->title;
			$mail_it->body = "Artikel pruefen / veroeffentlichen:\n\n\n"
				. "http://" . $this->cms->title . $webverzeichnis . "/interna/artikel.php?menuid=11&reporeid=" . $insertid;
			$mail_it->do_mail();
		}
	}

	/**
	 * @abstract die notwendigen Session Variablen initialisieren
	 */
	function make_session_init()
	{
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['schreiben'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['reportage'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext_org'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['orderid_org'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaserbild'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['breite'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['hohe'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser_link'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['uberschrift'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['inhalt'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['atyn'] = "1";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metatitel'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metadescrip'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metakey'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext_ar'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_teaserfix'] = 0;
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_link'] = 0;
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_teaser'] = 0;
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['formcatcat'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattextnews'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['artnews'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_internet'] = $this->cms->stamm_veroffen_yn;
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['publish_yn_lang'] = $this->cms->stamm_veroffen_yn;
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['newsaktiv'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['unter'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['zahlen'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['zahlen'] = "";
		//$_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_teaser_list'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_teaser_list'] = $this->cms->stamm_listen_yn;
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe_write'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['comment_yn'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['bildlr'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['hyn'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['lan_rss_yn'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['url_header'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_verfall'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_start'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_verfall_page'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['erstellungsdatum'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_start_page'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_wohin'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_danach_aktiv'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_dauerhaft'] = "1";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['write_article'] = "";
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dokuser'] = "";
	}

	/**
	 * @return void
	 * @desc Einen Artikel erstellen mit allem drum und dran
	 */
	function make_artikel()
	{
		$this->content->template['linkmenuid'] = $this->checked->menuid;
		//$this->replangid=$this->cms->lang_id;
		//Seesion variablen initialisieren
		if(empty($_SESSION['metadaten'][$this->unique_id][$this->replangid])) {
			$this->make_session_init();
			$this->content->template['hynchecked'] = "nodecode:checked=\"checked\"";
			$this->content->template['checked_lan_rss_yn'] = "nodecode:checked=\"checked\"";
			if($this->cms->stamm_artikel_rechte_jeder == 1) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe_write']['0'] = "10";
			}
			if($this->cms->stamm_artikel_rechte_chef == 1) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['write_article']['0'] = "11";
			}
			// Freigabe Artikel-Bearbeitung für alle "eigenen" Gruppen (außer "admin", "jeder",
			if(!empty($_SESSION['sessionusergruppenid'])) {
				foreach($_SESSION['sessionusergruppenid'] as $temp_gruppen_array) {
					$temp_gruppen_id = $temp_gruppen_array['gruppenid'];
					if(!is_array($_SESSION['metadaten'][$this->unique_id][$this->replangid]['write_article'])) {
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['write_article'] = array();
					}
					if(($temp_gruppen_id != 1)
						AND ($temp_gruppen_id != 10)
						AND (!in_array($temp_gruppen_id, $_SESSION['metadaten'][$this->unique_id][$this->replangid]['write_article']))
					) {
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['write_article'][] = $temp_gruppen_id;
					}
				}
			}
		}
		$this->format_eingabe();
		//eine andere Version soll rausgeholt werden
		if(!empty($this->checked->version)) {
			$this->get_version_for_work();
		}
		//Funktionsweiche für die Darstellung
		//$this->make_function_switch();
		//Wenn auf einen Button gedrückt wurde, zwischenspeichern
		if(!empty($this->switch)) {
			$this->make_version_save();
		}
		if(!empty($this->checked->submit_v)) {
			$this->make_version_list();
		}
		// Rechte für die Erstellung Überprüfen und freigeben
		$this->check_rechte();
		if(empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['orderid_org'])) {
			$this->content->template['darf_schreiben'] = 'ok';
		}
		/**
		 * # Setzen für das Template, damit die richtigen Sachen angezeigt werden.
		 * #$this->content->template['case1'] = '1';
		 *
		 */
		if(!empty ($this->checked->inhalt_ar['Submit1'])) {
			$this->make_save("INSERT");
		}
		// Bilder für den Editor aus der Datenbank holen
		$this->get_images();
		// Download  Dateien für den Editor herausholen
		$this->get_downloads();
		// Kategorien
		$this->get_categories();
		// CSS-Klassen für den (QuickTag-)Editor herausholen
		$this->get_css_klassen();
		$this->show_form_artikel();
	}

	/**
	 * Kategorien auslesen
	 */
	function get_categories()
	{
		if(!empty($_SESSION['langid_front'])) {
			$sprach_id = $_SESSION['langid_front'];
		}
		else {
			$sprach_id = $this->cms->lang_id;
		}
		$sql = sprintf("SELECT * FROM %s,%s WHERE cat_id=cat_lang_id AND cat_lang_lang='%s'",
			$this->cms->tbname['papoo_category_lang'],
			$this->cms->tbname['papoo_category'],
			$sprach_id
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['kat_n'] = $result;
	}

	/**
	 * @abstract einen bestimmten Artikel rausuchen und Daten in Session zuweisen
	 */
	function get_artikel_for_work()
	{
		$selectrepo = sprintf("SELECT * FROM %s WHERE reporeID='%d' LIMIT 1",
			$this->cms->papoo_repore,
			$this->checked->reporeid
		);
		$this->rechte_reporeid = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'] = $this->checked->reporeid;
		$resultrepo = $this->db->get_results($selectrepo);
		// Daten für das Formular zuweisen, loopt nur einmal
		if(!empty ($resultrepo)) {
			foreach($resultrepo as $row) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['formcattext'] = $row->cattextid;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['formcatcat'] = $row->cat_category_id;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['orderid_org'] = $row->order_id;
				// Bildname zum checken in der Auswahlliste
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaserbild'] = $row->teaser_bild;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dokuser'] = $row->dokuser;
				// Wenn das Schreiben erlaubt ist für diese Gruppe....
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_internet'] = $row->publish_yn;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe'] = $row->dokschreibengrid;
				//freigabe_teaser_list
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_teaser_list'] = $row->teaser_list;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_teaserfix'] = $row->dok_teaserfix;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_link'] = $row->dok_show_teaser_link;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_teaser'] = $row->dok_show_teaser_teaser;
				//Pub Start
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_start'] = $row->pub_start;
				//Pub Ende
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_verfall'] = $row->pub_verfall;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_start_page'] = $row->pub_start_page;
				//Pub Ende
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_verfall_page'] = $row->pub_verfall_page;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['erstellungsdatum'] = $row->erstellungsdatum;
				//Nach Ende wohin?
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_wohin'] = $row->pub_wohin;
				//Pub Dauerhaft
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_dauerhaft'] = $row->pub_dauerhaft;
				// root-Gruppe darf immer schreiben ...
				if(!empty ($this->user->gruppenid)) {
					foreach($this->user->gruppenid as $grid) {
						if($grid == 1) {
							$schreiben = 1;
						}
					}
				}
				// Wenn das Schreiben erlaubt ist für den User weil es sein Dokument ist
				if($this->user->userid == $row->dokuser or $this->user->userid == 10) {
					$schreiben = 1;
				}
				//cattext
				#$this->content->template['menulist_data'] = $this->menu->data_front_publish;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext'] = $row->cattextid;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['bildlr'] = $row->teaser_bild_lr;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['zahlen'] = $row->count_download;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser_list'] = $row->teaser_list;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['artikel_news_list'] = $row->artikel_news_list;
				# $row->teaser_bild_groesse;
				if($row->teaser_bild_groesse != "x") {
					$splitsize = explode("x", $row->teaser_bild_groesse);
					IfNotSetNull($splitsize['1']);
					IfNotSetNull($splitsize['0']);
					$_SESSION['metadaten'][$this->unique_id][$this->replangid]['breite'] = $splitsize['1'];
					$_SESSION['metadaten'][$this->unique_id][$this->replangid]['hohe'] = $splitsize['0'];
				}
				#$this->content->template['xinput'] = 1;
				// Überprüfen welcher Editor ausgewählt ist
				$this->check_editor();
				//Darf kommentiert werden
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['comment_yn'] = $row->allow_comment;
				//artikel mit Teaser yn
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['atyn'] = $row->teaser_atyn;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['hyn'] = $row->uberschrift_hyn;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'] = $row->reporeID;
				if(isset($schreiben) && $schreiben == 1) {
					$this->content->template['schreiben'] = '1';
				}
				else {
					$this->content->template['no_schreiben'] = '1';
				}
				//Lookups zu den Menüpunkten zuweisen
				$select = sprintf("SELECT * FROM %s WHERE  lart_id='%d'",
					$this->cms->tbname['papoo_lookup_art_cat'],
					$this->db->escape($row->reporeID)
				);
				$result = $this->db->get_results($select, ARRAY_A);
				if(!empty ($result)) {
					foreach($result as $tex) {
						$cat_ar[] = $tex['lcat_id'];
					}
				}
				IfNotSetNull($cat_ar);
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext_ar'] = $cat_ar;
				// Inhalte zuweisen
				// $this->cms->lang_id ersetzt durch $this->replangid
				$select = sprintf("SELECT * FROM %s WHERE  lan_repore_id='%d' AND lang_id='%d'  LIMIT 1",
					$this->cms->papoo_language_article,
					$this->db->escape($row->reporeID),
					$this->db->escape($this->replangid)
				);
				$result = $this->db->get_results($select);
				if(!empty ($result)) {
					foreach($result as $tex) {
						$artikel = $tex->lan_article_sans;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['uberschrift'] = $tex->header;
						$teaser = $tex->lan_teaser;
						//$this->content->template_nodecode['teaser'] = $teaser;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser'] = $teaser;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['url_header'] = $tex->url_header;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser_link'] = $tex->lan_teaser_link;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metatitel'] = $tex->lan_metatitel;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metadescrip'] = $tex->lan_metadescrip;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metakey'] = $tex->lan_metakey;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['lan_rss_yn'] = $tex->lan_rss_yn;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['publish_yn_lang'] = $tex->publish_yn_lang;
						//$this->content->template_nodecode['Beschreibung'] = $artikel;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['inhalt'] = $artikel;
					}
				}
				else {
					//leere Daten Übergeben
					$_SESSION['metadaten'][$this->unique_id][$this->replangid]['uberschrift'] = "";
					$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser'] = "";
					$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser_link'] = "";
					$_SESSION['metadaten'][$this->unique_id][$this->replangid]['Beschreibung'] = "";
					$_SESSION['metadaten'][$this->unique_id][$this->replangid]['lan_rss_yn'] = "";
				}
				// Rechte aus der Datenbank holen, wer darf den Artikel im Frontend lesen?
				$select_gr = sprintf("SELECT * FROM %s WHERE article_id='%d'",
					$this->cms->papoo_lookup_article,
					$this->db->escape($this->checked->reporeid)
				);
				$result_gr = $this->db->get_results($select_gr);

				if(!empty($result_gr)) {
					foreach($result_gr as $x) {
						$table_data2[] = $x->gruppeid_id;
					}
				}
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe_write'] = $table_data2;
				// Rechte aus der Datenbank holen, wer darf den Artikel im Frontend lesen?
				$table_data3 = array();
				$select_grw = sprintf("SELECT * FROM %s WHERE article_wid_id='%s'",
					$this->cms->tbname['papoo_lookup_write_article'],
					$this->db->escape($this->checked->reporeid)
				);
				$result_gr2 = $this->db->get_results($select_grw);

				if(!empty($result_gr2)) {
					foreach($result_gr2 as $x) {
						$table_data3[] = $x->gruppeid_wid_id;
					}
				}
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['write_article'] = $table_data3;
			}
		}
	}

	/**
	 * @param $lang
	 */
	function make_session_lang($lang)
	{
		//Wenn eine andere Sprache dann
		//Session kopieren
		$_SESSION['metadaten'][$this->unique_id][$lang] = $_SESSION['metadaten'][$this->unique_id][$this->replangid];
		//Sprachdaten raussuchen Sprachdaten in Session zuweisen
		$select = sprintf("SELECT * FROM %s WHERE lan_repore_id='%d' AND lang_id='%d' LIMIT 1",
			$this->cms->papoo_language_article,
			$this->db->escape($_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid']),
			$this->db->escape($lang)
		);
		$result = $this->db->get_results($select);

		if(!empty ($result)) {
			foreach($result as $tex) {
				$artikel = $tex->lan_article_sans;
				$_SESSION['metadaten'][$this->unique_id][$lang]['uberschrift'] = $tex->header;
				$teaser = $tex->lan_teaser;
				//$this->content->template_nodecode['teaser'] = $teaser;
				$_SESSION['metadaten'][$this->unique_id][$lang]['teaser'] = $teaser;
				$_SESSION['metadaten'][$this->unique_id][$lang]['url_header'] = $tex->url_header;
				$_SESSION['metadaten'][$this->unique_id][$lang]['teaser_link'] = $tex->lan_teaser_link;
				$_SESSION['metadaten'][$this->unique_id][$lang]['metatitel'] = $tex->lan_metatitel;
				$_SESSION['metadaten'][$this->unique_id][$lang]['metadescrip'] = $tex->lan_metadescrip;
				$_SESSION['metadaten'][$this->unique_id][$lang]['metakey'] = $tex->lan_metakey;
				$_SESSION['metadaten'][$this->unique_id][$lang]['lan_rss_yn'] = $tex->lan_rss_yn;
				//$this->content->template_nodecode['Beschreibung'] = $artikel;
				$_SESSION['metadaten'][$this->unique_id][$lang]['inhalt'] = $artikel;
			}
		}
		else {
			$_SESSION['metadaten'][$this->unique_id][$lang]['uberschrift'] = "";
			$_SESSION['metadaten'][$this->unique_id][$lang]['url_header'] = "";
			$_SESSION['metadaten'][$this->unique_id][$lang]['teaser'] = "";
			$_SESSION['metadaten'][$this->unique_id][$lang]['teaser_link'] = "";
			$_SESSION['metadaten'][$this->unique_id][$lang]['metatitel'] = "";
			$_SESSION['metadaten'][$this->unique_id][$lang]['metadescrip'] = "";
			$_SESSION['metadaten'][$this->unique_id][$lang]['metakey'] = "";
			$_SESSION['metadaten'][$this->unique_id][$lang]['lan_rss_yn'] = "";
			//$this->content->template_nodecode['Beschreibung'] = $artikel;
			$_SESSION['metadaten'][$this->unique_id][$lang]['inhalt'] = "";
		}
	}

	/**
	 * @abstract Eine alte Version rausholen
	 */
	function get_version_for_work()
	{
		//$this->replangid=$this->cms->lang_id;
		$selectrepo = sprintf("SELECT * FROM %s WHERE reporeID='%d' AND version_repo_id='%d' LIMIT 1",
			$this->cms->tbname['papoo_version_repore'],
			$this->db->escape($this->checked->reporeid),
			$this->db->escape($this->checked->version)
		);
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'] = $this->checked->reporeid;
		$resultrepo = $this->db->get_results($selectrepo);
		// Daten für das Formular zuweisen, loopt nur einmal
		if(!empty ($resultrepo)) {
			unset($_SESSION['metadaten'][$this->unique_id][$this->replangid]);
			$this->make_session_init();
			foreach($resultrepo as $row) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['formcattext'] = $row->cattextid;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['formcatcat'] = $row->cat_category_id;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['orderid_org'] = $row->order_id;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dokuser'] = $row->dokuser;
				// Bildname zum checken in der Auswahlliste
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaserbild'] = $row->teaser_bild;
				// Wenn das Schreiben erlaubt ist für diese Gruppe....
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_internet'] = $row->publish_yn;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe'] = $row->dokschreibengrid;
				//freigabe_teaser_list
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_teaser_list'] = $row->teaser_list;
				// root-Gruppe darf immer schreiben ...
				if(!empty ($this->user->gruppenid)) {
					foreach($this->user->gruppenid as $grid) {
						if($grid == 1) {
							$schreiben = 1;
						}
					}
				}
				// Wenn das Schreiben erlaubt ist für den User weil es sein Dokument ist
				if($this->user->userid == $row->dokuser or $this->user->userid == 10) {
					$schreiben = 1;
				}
				//$schreiben = 1;
				//cattext
				#$this->content->template['menulist_data'] = $this->menu->data_front_publish;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext'] = $row->cattextid;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_teaserfix'] = $row->dok_teaserfix;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_link'] = $row->dok_show_teaser_link;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_teaser'] = $row->dok_show_teaser_teaser;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['bildlr'] = $row->teaser_bild_lr;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['zahlen'] = $row->count_download;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser_list'] = $row->teaser_list;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['artikel_news_list'] = $row->artikel_news_list;
				//Pub Start
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_start'] = $row->pub_start;
				//Pub Ende
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_verfall'] = $row->pub_verfall;
				//Pub Start
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_start_page'] = $row->pub_start_page;
				//Pub Ende
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_verfall_page'] = $row->pub_verfall_page;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['erstellungsdatum'] = $row->erstellungsdatum;
				//Nach Ende wohin?
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_wohin'] = $row->pub_wohin;
				//Pub Dauerhaft
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_dauerhaft'] = $row->pub_dauerhaft;
				# $row->teaser_bild_groesse;
				if($row->teaser_bild_groesse != "x") {
					$splitsize = explode("x", $row->teaser_bild_groesse);
					$_SESSION['metadaten'][$this->unique_id][$this->replangid]['breite'] = $splitsize['1'];
					$_SESSION['metadaten'][$this->unique_id][$this->replangid]['hohe'] = $splitsize['0'];
				}
				#$this->content->template['xinput'] = 1;
				// Überprüfen welcher Editor ausgewählt ist
				$this->check_editor();
				//Darf kommentiert werden
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['comment_yn'] = $row->allow_comment;
				//artikel mit Teaser yn
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['atyn'] = $row->teaser_atyn;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['hyn'] = $row->uberschrift_hyn;
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'] = $row->reporeID;
				if($schreiben == 1) {
					$this->content->template['schreiben'] = '1';
				}
				else {
					$this->content->template['no_schreiben'] = '1';
				}
				// Inhalte zuweisen
				// $this->cms->lang_id ersetzt durch $this->replangid;
				$select = sprintf("SELECT * FROM %s WHERE lan_repore_id='%d' AND lang_id='%d' AND version_langart_id='%d' LIMIT 1",
					$this->cms->tbname['papoo_version_language_article'],
					$this->db->escape($row->reporeID),
					$this->db->escape($this->replangid),
					$this->db->escape($this->checked->version)
				);
				$result = $this->db->get_results($select);
				if(!empty ($result)) {
					foreach($result as $tex) {
						$artikel = $tex->lan_article_sans;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['uberschrift'] = $tex->header;
						$teaser = $tex->lan_teaser;
						//$this->content->template_nodecode['teaser'] = $teaser;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser'] = $teaser;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['url_header'] = $tex->url_header;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser_link'] = $tex->lan_teaser_link;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metatitel'] = $tex->lan_metatitel;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metadescrip'] = $tex->lan_metadescrip;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metakey'] = $tex->lan_metakey;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['lan_rss_yn'] = $tex->lan_rss_yn;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['publish_yn_lang'] = $tex->publish_yn_lang;
						//$this->content->template_nodecode['Beschreibung'] = $artikel;
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['inhalt'] = $artikel;
					}
				}
				/**
				 *Rechte aus der Datenbank holen, wer darf den Artikel im Frontend lesen?*/
				$table_data2 = array();
				$select_gr = sprintf("SELECT * FROM %s WHERE article_id='%d' AND version_lookup_article_id='%d'",
					$this->cms->tbname['papoo_version_lookup_article'],
					$this->db->escape($this->checked->reporeid),
					$this->db->escape($this->checked->version)
				);
				$result_gr = $this->db->get_results($select_gr);

				foreach($result_gr as $x) {
					$table_data2[] = $x->gruppeid_id;
				}
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe_write'] = $table_data2;
			}
			/**
			 *Rechte aus der Datenbank holen, wer darf den Artikel im Frontend lesen?*/
			$table_data3 = array();
			$select_grw = sprintf("SELECT * FROM %s WHERE article_wid_id='%d'",
				$this->cms->tbname['papoo_version_lookup_write_article'],
				$this->db->escape($this->checked->reporeid)
			);
			$result_gr2 = $this->db->get_results($select_grw);

			if(!empty($result_gr2)) {
				foreach($result_gr2 as $x) {
					$table_data3[] = $x->gruppeid_wid_id;
				}
			}
			$_SESSION['metadaten'][$this->unique_id][$this->replangid]['write_article'] = $table_data3;
			$location_url = "./artikel.php?menuid=" . $this->checked->menuid . "&reporeid=" . $this->checked->reporeid . "&unique_id=" . $this->unique_id;
			if($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}
	}

	/**
	 * @return void
	 * @desc Artikel bearbeiten und wieder abspeichern
	 */
	function change_artikel()
	{
		$this->check_rechte();
		$this->content->template['linkmenuid'] = $this->checked->menuid;
		//eine andere Version soll rausgeholt werden
		if(!empty($this->checked->version)) {
			$this->get_version_for_work();
		}
		$this->make_div_lang();
		// Kategorien
		$this->get_categories();
		//$this->replangid=$this->cms->lang_id;
		IfNotSetNull($_SESSION['metadaten'][$this->unique_id]);
		if($this->user->userid != $_SESSION['metadaten'][$this->unique_id][$this->replangid]['dokuser']) {
			$this->content->template['urheber'] = "nicht";
			if(empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['dokuser'])) {
				$sql = sprintf("SELECT dokuser FROM %s WHERE reporeID='%s'",
					$this->cms->tbname['papoo_repore'],
					$this->db->escape($this->checked->reporeid)
				);
				$var = $this->db->get_var($sql);
				if($this->user->userid == $var) {
					$this->content->template['urheber'] = "";
				}
			}
		}
		$this->check_article_write();
		//Session variablen initialisieren
		if(empty($_SESSION['metadaten'][$this->unique_id][$this->replangid])) {
			#$this->replangid=$this->cms->lang_id;
			$this->make_session_init();
		}
		//Funktionsweiche für die Darstellung
		//$this->make_function_switch();
		//Wenn auf einen Button gedrückt wurde, zwischenspeichern
		IfNotSetNull($this->content->template['xinput']);
		IfNotSetNull($this->checked->loeschenecht);
		if(!empty($this->switch) or !empty ($this->checked->inhalt_ar['Submit1']) || !empty ($this->checked->inhalt_ar['Submit_zwischen'])) {
			$this->make_version_save();
		}
		// Wenn gespeichert werden soll, Speichern und Schluß
		if(!empty ($this->checked->inhalt_ar['Submit1']) || !empty ($this->checked->inhalt_ar['Submit_zwischen'])) {
			$this->make_save("UPDATE");
			return;
		}
		if(!empty ($this->checked->inhalt_ar['Submit2'])) {
			$_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_internet'] = "";
			$_SESSION['metadaten'][$this->unique_id][$this->replangid]['uberschrift'] = "1 Kopie - " .
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['uberschrift'];
			$this->make_save("INSERT");
			return;
		}
		$this->content->template['xinput'] = '1';
		$this->make_version_list();
		// Variablen initialisieren
		if(empty($this->checked->reportage)) {
			$this->checked->reportage = "";
		}
		// Löschen bestätigt, dann löschen
		if($this->checked->loeschenecht != "") {
			if(is_numeric($_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'])) {
				$temp_loesch_id = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'];
				// Daten zu Artikel löschen
				$sql = sprintf("DELETE FROM %s WHERE reporeID='%d'", $this->cms->tbname['papoo_repore'], $temp_loesch_id);
				$this->db->query($sql);
				//$sql = sprintf("DELETE FROM %s WHERE reporeID='%d'", $this->cms->tbname['papoo_version_repore'], $temp_loesch_id);
				//$this->db->query($sql);
				// Sprach-Inhalte des Artikels löschen
				$sql = sprintf("DELETE FROM %s WHERE lan_repore_id='%d'", $this->cms->tbname['papoo_language_article'], $temp_loesch_id);
				$this->db->query($sql);
				//$sql = sprintf("DELETE FROM %s WHERE lan_repore_id='%d'", $this->cms->tbname['papoo_version_language_article'], $temp_loesch_id);
				//$this->db->query($sql);
				// Lese-Rechte des Artikel löschen
				$sql = sprintf("DELETE FROM %s WHERE article_id='%d'", $this->cms->tbname['papoo_lookup_article'], $temp_loesch_id);
				$this->db->query($sql);
				//$sql = sprintf("DELETE FROM %s WHERE article_id='%d'", $this->cms->tbname['papoo_version_lookup_article'], $temp_loesch_id);
				//$this->db->query($sql);
				// Schreib-Rechte des Artikel löschen
				$sql = sprintf("DELETE FROM %s WHERE article_wid_id='%d'", $this->cms->tbname['papoo_lookup_write_article'], $temp_loesch_id);
				$this->db->query($sql);
				//$sql = sprintf("DELETE FROM %s WHERE article_wid_id='%d'", $this->cms->tbname['papoo_version_lookup_write_article'], $temp_loesch_id);
				//$this->db->query($sql);
				// Menü-Zuweisung(en) des Artikel löschen
				$sql = sprintf("DELETE FROM %s WHERE lart_id='%d'", $this->cms->tbname['papoo_lookup_art_cat'], $temp_loesch_id);
				$this->db->query($sql);
				//$sql = sprintf("DELETE FROM %s WHERE lart_id='%d'", $this->cms->tbname['papoo_version_lookup_art_cat'], $temp_loesch_id);
				//$this->db->query($sql);
				// ???
				//$sql = sprintf("DELETE FROM %s WHERE version_art_id='%d'", $this->cms->tbname['papoo_version_article'], $temp_loesch_id);
				//$this->db->query($sql);
			}
			// Artikel des original-Menü-Punktes neu durchnummerieren
			$this->artikel_reorder($this->checked->cattext_org);
			// Seite neu laden
			$location_url = "./artikel.php?menuid=5&messageget=2";
			if($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}
		// Zum Löschen bestätigen
		else {
			if(empty ($this->checked->inhalt_ar['loeschen'])) {
				$this->checked->inhalt_ar['loeschen'] = "";
			}
			if($this->checked->inhalt_ar['loeschen'] != "") {
				$this->switch = 5;
				$this->content->template['aktiv_i'] = "";
				$this->content->template['aktiv_en'] = "_aktiv";
				$this->content->template['from_aktiv'] = "7";
				$this->content->template['loeschen'] = '1';
				$this->content->template['selected_tab'] = 6;
				$this->content->template['uberschrift'] = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['uberschrift'];
			}
			else {
				$this->content->template['xsuch'] = '1';
				// Es ist ein Artikel ausgewählt der schon als Session-Version besteht
				if(!empty ($this->checked->reporeid) and !empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'])) {
					$this->format_eingabe();
				}
				// Es ist ein Artikel ausgewählt der noch nicht als Session-Version besteht
				elseif(!empty ($this->checked->reporeid) and empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'])) {
					$this->get_artikel_for_work();
					$this->format_eingabe();
				}
				else {
					// FIXME => Abfrage Über elterngruppe rein!!!
					$this->make_artikel_list();
				}
			}
		}
		// Wenn Suche leer abbrechen
		if(empty ($this->checked->search)) {
			unset ($this->checked->search);
		}
		//Artikel wird bearbeitet
		if(!empty($this->checked->reporeid)) {
			$this->content->template['artikel'] = '1';
			$this->content->template['artikelliste'] = '';
		}
		else {
			//unset($_SESSION['metadaten'][$this->unique_id]);
			//$_SESSION['back_aktu_article_edit_langid']=$this->cms->lang_id;
			$_SESSION['back_aktu_article_edit_langid'] = $this->replangid;
		}
		// Bilder für den Editor aus der Datenbank holen
		$this->get_images();
		// Download  Dateien für den Editor herausholen
		$this->get_downloads();
		// CSS-Klassen für den (QuickTag-)Editor herausholen
		$this->get_css_klassen();
	}

	/**
	 * @abstract Artikel Liste erstellen
	 *
	 * @param string $all_art
	 */
	function make_artikel_list($all_art = "")
	{
		IfNotSetNull($this->checked->sort);
		IfNotSetNull($this->checked->search);
		$this->content->template['xsuch_echt'] = '1';
		$this->content->template['sort'] = $this->checked->sort;
		$cattext = "";
		//$suche = $this->searcher->clean($this->checked->search);
		$suche = $this->checked->search;
		$suche_sql = "";
		switch($this->checked->sort) {
		case "menu":
			#$this->content->template['menulist_data'] = $this->menu->menu_navigation_build("FRONT_COMPLETE", "CLEAN");
			$this->content->template['menulist_data'] = $this->menu->menu_data_read("FRONT_PUBLISH", $this->replangid);
			$menuid = 1;
			if(!empty($this->checked->formmenuid)) {
				$menuid = $this->db->escape($this->checked->formmenuid);
			}
			$cattext = " AND lcat_id='" . $menuid . "'";
			$this->content->template['formmenuid_cat'] = $menuid;
			$order = " t2.header ASC";
			break;
		case "zeit":
			$order = " t1.stamptime DESC";
			break;
		case "alpha":
		default:
			$order = "t1.stamptime ASC";
		}
		if(!empty($suche)) {
			$suche_sql = "AND CONCAT(t2.header, ' ', t2.lan_teaser, ' ', t2.lan_article_sans) LIKE '%" . $this->db->escape($suche) . "%' ";
		}
		// Keine Suche aktiv, dann normal ausgeben
		//if (empty ($this->checked->search))
		{
			$this->content->template['artikel'] = '';
			$this->content->template['artikelliste'] = 'ok';
			if(empty($all_art)) {
				$this->weiter->make_limit(10);
			}
			else {
				$this->weiter->make_limit(1000000);
			}
			if($this->user->username == "root") {
				// aktive Sprache rausbekommen, id bestimmen
				$this->content->template['eigene'] = '1';
				// Anzahl für weitere Seiten rausbekommen
				$sqlsuch = sprintf("SELECT COUNT(reporeID) FROM %s, %s WHERE reporeID=lan_repore_id AND lang_id='%d'",
					$this->cms->papoo_repore,
					$this->cms->papoo_language_article,
					$this->db->escape($this->replangid)
				);
				$this->weiter->result_anzahl = $this->db->get_var($sqlsuch);

				// Daten raussuchen
				$this->get_suchergebniss = sprintf(
					"SELECT DISTINCT (t1.reporeID), t2.lan_teaser, t2.header, t1.teaser_bild, t4.username, t1.timestamp, t1.dokuser_last, t1.dokuser
							FROM
							%s AS t1, %s AS t2,
							%s AS t4, %s AS t5

							WHERE t1.reporeID = t2.lan_repore_id  AND t2.lang_id = '%d'
							AND t4.userid='%d' AND t5.lart_id=t1.reporeID

							%s %s ORDER BY %s ",
					$this->cms->papoo_repore, $this->cms->papoo_language_article,
					$this->cms->papoo_user, $this->cms->tbname['papoo_lookup_art_cat'],
					$this->db->escape($this->replangid),
					$this->user->userid,
					$cattext,
					$suche_sql,
					$order
				);
				$this->get_suchergebniss2 = $this->get_suchergebniss;
				$this->get_suchergebniss = $this->get_suchergebniss . " " . $this->weiter->sqllimit;
				// Alle Artikel anzeigen
			}
			else {
				global $db_praefix;
				$this->get_suchergebniss = sprintf(
					"SELECT DISTINCT (t1.reporeID), t2.lan_teaser, t2.header, t1.teaser_bild, t4.username, dokuser_last, dokuser,  t1.timestamp
							FROM
							%s AS t1, %s AS t2, %s AS t3,
							%s AS t4, %s AS t5, %s AS t6, %s AS t7

							WHERE t1.reporeID = t2.lan_repore_id  AND t2.lang_id = '%d'
							AND t1.reporeID = t3.article_wid_id
							AND t4.userid='%d'AND t4.userid = t5.userid AND t5.gruppenid = t6.gruppeid AND t3.gruppeid_wid_id=t6.gruppeid AND t7.lart_id=t1.reporeID

							%s %s ORDER BY %s ",
					$this->cms->papoo_repore, $this->cms->papoo_language_article, $db_praefix . "papoo_lookup_write_article",
					$this->cms->papoo_user, $this->cms->papoo_lookup_ug, $this->cms->papoo_gruppe,
					$this->cms->tbname['papoo_lookup_art_cat'],
					$this->db->escape($this->replangid),
					$this->user->userid,
					$cattext, $suche_sql, $order
				);
				$this->get_suchergebniss2 = $this->get_suchergebniss;
				$this->get_suchergebniss = $this->get_suchergebniss . " " . $this->weiter->sqllimit;
			}
			$this->get_artikel();
			$this->weiter->result_anzahl = count($this->result_liste_co);
			// Link für weitere Seiten
			IfNotSetNull($menuid);
			//$this->weiter->weiter_link = "./artikel.php?menuid=".$this->checked->menuid."&sort=".$this->checked->sort."&formmenuid=".$menuid;
			$temp_weiter_link = "./artikel.php?menuid=" . $this->checked->menuid;
			if($this->checked->sort) {
				$temp_weiter_link .= "&amp;sort=" . $this->checked->sort;
			}
			if($menuid) {
				$temp_weiter_link .= "&amp;formmenuid=" . $menuid;
			}
			if($this->checked->search) {
				$temp_weiter_link .= "&amp;search=" . $this->checked->search;
			}
			$this->weiter->weiter_link = $temp_weiter_link;
			// weitere Seiten anzeigen
			$this->weiter->do_weiter("teaser");
		}
	}

	/**
	 * @return void
	 * @desc Artikeldaten aus der Datenbankabfrage in Variablen einlesen
	 */
	function get_artikel()
	{
		$this->content->template['link_data'] = array();
		$result2 = $this->db->get_results($this->get_suchergebniss);
		$this->content->template['artikelliste'] = "ok";
		$this->result_liste = $this->db->get_results($this->get_suchergebniss, ARRAY_A);
		$this->result_liste_co = $this->db->get_results($this->get_suchergebniss2, ARRAY_A);
		//wenn Artikel vorhanden sind
		if(!empty ($result2)) {
			foreach($result2 as $row) {
				IfNotSetNull($row->dokuser);
				$temp_array = array();
				$temp_array['ueberschrift'] = $row->header;
				$temp_array['reporeID'] = $row->reporeID;
				$temp_array['teaser'] = $row->lan_teaser;
				//$temp_array['timestamp'] = $row->timestamp;
				$temp_array['date_time'] = $row->timestamp;
				$sql = sprintf("SELECT username FROM %s WHERE userid='%d'",
					$this->cms->tbname['papoo_user'],
					$row->dokuser_last
				);
				$temp_array['username'] = $this->db->get_var($sql);
				if(empty($temp_array['username'])) {
					$temp_array['username'] = "n.o.";
				}
				$sql = sprintf("SELECT username FROM %s WHERE userid='%d'",
					$this->cms->tbname['papoo_user'],
					$row->dokuser
				);
				$temp_array['autor'] = $this->db->get_var($sql);
				$temp_array['lang_id'] = $this->cms->lang_back_content_id;
				// Daten zuweisen
				$this->content->template['link_data'][] = $temp_array;
			}
		}
	}

	/**
	 * @deprecated Im Grund passiert hier nichts. Außer das 2 template Variablen gesetzt werden.
	 */
	function show_form_artikel()
	{
		// Menüpunte auswählen für die Schreibrechte bestehen
		$sqlmenu = sprintf("SELECT schreibrechte FROM %s WHERE menuid='%d'",
			$this->cms->papoo_menu_int,
			$this->db->escape($this->checked->menuid)
		);
		$resultmenu = $this->db->get_results($sqlmenu);

		$splitten = ",";
		foreach($resultmenu as $row) {
			if(stristr($row->schreibrechte, ",")) {
				$arraymenu_gruppe = explode($splitten, $row->schreibrechte);
			}
		}
		// Werte für die Inputbutton
		$this->content->template['input'] = 'Submit1';
		$this->content->template['yinput'] = 'Submit2';
	}

	/**
	 * @return void
	 * @desc Aus der Datenbank heraussuchen, welche gruppen Veröffentlichen düfen
	 */
	function check_allow_internet()
	{
		if(empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'])) {
			$reporeid = $this->checked->reporeid;
		}
		else {
			$reporeid = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'];
		}
		//$this->replangid=$this->cms->lang_id;
		//Raussuchen ob die aktuelle Gruppe darauf schreibend zugreifen darf
		$sql = sprintf(
			"SELECT gruppeid_wid_id FROM %s,%s,%s WHERE article_wid_id=reporeID AND gruppeid_wid_id=gruppenid AND userid='%s' AND reporeID='%s'",
			$this->cms->tbname['papoo_repore'],
			$this->cms->tbname['papoo_lookup_write_article'],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->user->userid,
			$this->db->escape($reporeid)
		);
		$this->result = $this->db->get_var($sql);
		// Rechte zuweisen
		// Die Gruppe darf diesen Artikel für das Internet bearbeiten
		if(!empty($this->result)) {
			$this->darf_schreiben = 1;
		}
		else {
			$sql = sprintf("SELECT dokuser FROM %s WHERE reporeID='%s'",
				$this->cms->tbname['papoo_repore'],
				$this->db->escape($this->checked->reporeid)
			);
			$var = $this->db->get_var($sql);
			if($this->user->userid == $var) {
				$this->darf_schreiben = 1;
			}
		}
		// FIXME
		//$arraysessiongruppenid = 1;
		$suchergebniss = "SELECT allow_internet, allow_intranet  ";
		$suchergebniss .= " FROM " . $this->cms->papoo_gruppe . ", " . $this->cms->papoo_lookup_ug . " WHERE ";
		$suchergebniss .= " " . $this->cms->papoo_lookup_ug . ".userid=" . $this->user->userid . "  ";
		$suchergebniss .= " AND ";
		$suchergebniss .= " " . $this->cms->papoo_gruppe . ".gruppeid=" . $this->cms->papoo_lookup_ug . ".gruppenid ";
		$resultgruppe = $this->db->get_results($suchergebniss);
		// Rechte zuweisen
		foreach($resultgruppe as $rowgruppe) {
			// Die Gruppe darf Artikel für das Internet veröffentlichen
			if($rowgruppe->allow_internet == 1) {
				$this->allow_internet = 1;
			}
			// Die Gruppe darf Artikel für das Intranet veröffentlichen
			if($rowgruppe->allow_intranet == 1) {
				$this->allow_intranet = 1;
			}
		}
	}

	/**
	 * Editor zuweisen ..
	 * 3 = default Editor (TinyMCE)
	 *
	 * @return void
	 * @desc Beschreibung eingeben...
	 */
	function check_editor()
	{

		if(isset ($this->user->editor)) {//Editor id ans Template Übergeben
			$this->content->template['editor_default'] = $this->user->editor;
		}
		else {
			$this->content->template['editor_default'] = 3;
		}
		$this->content->template['output'] = '1';
		// ENDE Editor zuweisen --
	}

	/**
	 * Die Kategorienliste erstellen
	 *
	 * @return array|void
	 */
	function get_image_cat_list()
	{
		$sql = sprintf("SELECT DISTINCT(bilder_cat_id), bilder_cat_name FROM %s,%s,%s 
			WHERE userid='%s' AND gruppenid=gruppeid_id AND bilder_cat_id_id=bilder_cat_id ORDER BY bilder_cat_name ASC",
			$this->cms->tbname['papoo_kategorie_bilder'],
			$this->cms->tbname['papoo_lookup_cat_images'],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->user->userid
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['result_cat'] = $result;
		return $result;
	}

	/**
	 * Die Kategorienliste erstellen
	 *
	 * @return array|void
	 */
	function get_datei_cat_list()
	{
		$sql = sprintf("SELECT DISTINCT t1.dateien_cat_id, t1.dateien_cat_name FROM %s AS t1,%s AS t2,%s AS t3
						WHERE t3.userid='%d' AND t3.gruppenid=t2.gruppeid_id AND t2.dateien_cat_id_id=t1.dateien_cat_id",
			$this->cms->tbname['papoo_kategorie_dateien'],
			$this->cms->tbname['papoo_lookup_cat_dateien'],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->user->userid
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['result_cat'] = $result;
		return $result;
	}

	/**
	 * Diese Funktion liefert die Bilder aus, an denen der Editor die Rechte hat an den Edito
	 *
	 * @param string $sprach_id
	 * @return void
	 */
	function get_images($sprach_id = "")
	{
		if(empty($sprach_id)) {
			$sprach_id = isset($this->replangid) ? $this->replangid : 1;
		}
		// Bilder aus der Datenbank holen ...
		//$arraysessiongruppenid = 1;
		// Sprache rausfinden
		$catok = $this->get_image_cat_list();
		$sqlbild = sprintf("SELECT DISTINCT(image_id), image_name, alt, title, image_width, image_dir, image_height
							FROM %s, %s, %s
							WHERE image_id=lan_image_id  AND lang_id='%d' AND image_dir=bilder_cat_id
							OR image_id=lan_image_id  AND lang_id='%d' AND image_dir=0
							ORDER BY image_dir, alt ASC",
			$this->cms->papoo_images,
			$this->cms->papoo_language_image,
			$this->cms->tbname['papoo_kategorie_bilder'],
			$this->db->escape($sprach_id),
			$this->db->escape($sprach_id)
		);
		$resultbild = $this->db->get_results($sqlbild);
		$bild_data = array();
		if(!empty ($resultbild)) {
			if(!isset($this->diverse->isdeep_item)) {
				$this->diverse->isdeep_item = array();
				$this->diverse->isdeep_item['bilder_cat_name'] = NULL;
			}
			else {
				IfNotSetNull($this->diverse->isdeep_item['bilder_cat_name']);
			}
			IfNotSetNull($this->unique_id);
			IfNotSetNull($this->replangid);
			IfNotSetNull($_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaserbild']);
			foreach($resultbild as $rowbild) {
				if($_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaserbild'] == $rowbild->image_name) {
					$checked = 'selected="selected"';
				}
				else {
					$checked = "";
				}
				/**if (!empty ($this->checked->tbild)) {
				 * if ($this->checked->tbild == $rowbild->image_name) {
				 * $checked = 'selected="selected"';
				 * } else {
				 * }
				 * }
				 */
				$thumbsize = array();
				$buh = "";
				//Größe der Thumbnails rausholen
				if(@file_exists(("../images/thumbs/" . $rowbild->image_name))) {
					$thumbsize = @GetImageSize("../images/thumbs/" . $rowbild->image_name);
					$buh = $thumbsize[0] . "x" . $thumbsize[1];
				}
				//Nur wenn Cat ok
				if($this->diverse->deep_in_array($rowbild->image_dir, $catok) or $rowbild->image_dir == "0") {
					IfNotSetNull($thumbsize[0]);
					IfNotSetNull($thumbsize[1]);
					array_push($bild_data, array(
						'image_name' => $rowbild->image_name,
						'image_alt' => str_replace('"', '', $rowbild->alt),
						'image_title' => str_replace('"', '', $rowbild->title),
						'image_dir' => $this->diverse->isdeep_item['bilder_cat_name'],
						'image_width' => $rowbild->image_width,
						'image_height' => $rowbild->image_height,
						'image_thumb_width' => $thumbsize[0],
						'image_thumb_height' => $thumbsize[1],
						'thumb_buh' => $buh,
						'image_check_teaser' => $checked
					));
				}
			}
			$this->content->template['bild_data'] = $bild_data;
		}
		// ENDE --- Bilder aus der Datenbank holen für bbcode Editor ... ###
	}

	/**
	 * Ausgabe der Downloadfähigen Dateien an den Editor
	 *
	 * @param string $sprach_id
	 * @return void
	 */
	function get_downloads($sprach_id = "")
	{
		if(empty($sprach_id)) {
			$sprach_id = $this->replangid;
		}
		$sql = sprintf("SELECT DISTINCT t1.*, t2.*
						FROM %s AS t1, %s AS t2, %s AS t3, %s AS t4, %s AS t5
						WHERE
						t1.downloadid=t2.download_id AND t2.lang_id='%d'
						AND t1.downloadkategorie=t3.dateien_cat_id_id AND t3.gruppeid_id=t5.gruppeid
						AND t4.userid='%d' AND t4.gruppenid=t5.gruppeid

						ORDER BY t1.downloadkategorie",
			$this->cms->papoo_download, $this->cms->papoo_language_download, $this->cms->tbname['papoo_lookup_cat_dateien'],
			$this->cms->papoo_lookup_ug, $this->cms->papoo_gruppe,
			$this->db->escape($sprach_id),
			$this->user->userid
		);
		$resultdown = $this->db->get_results($sql);
		$down_data = array();
		if(!empty ($resultdown)) {
			$kategorien = $this->get_datei_cat_list();
			$kat_namen = array(0 => "default");
			if(!empty($kategorien)) {
				foreach($kategorien as $kategorie) {
					$kat_namen[$kategorie['dateien_cat_id']] = $kategorie['dateien_cat_name'];
				}
			}
			foreach($resultdown as $rowdown) {
				$temp_array = array();
				$temp_array['downloadid'] = $rowdown->downloadid;
				//$temp_array['downloadname'] = "nodecode:".$this->diverse->encode_quote($rowdown->downloadname);
				$temp_array['downloadname'] = $this->diverse->encode_quote($rowdown->downloadname);
				$temp_array['downloadlink'] = $rowdown->downloadlink;
				//$temp_array['dateien_cat_name'] = "nodecode:".$this->diverse->encode_quote($kat_namen[$rowdown->downloadkategorie]);
				$temp_array['dateien_cat_name'] = $this->diverse->encode_quote($kat_namen[$rowdown->downloadkategorie]);
				/*
				//Nur wenn Cat ok
				if ($this->diverse->deep_in_array($rowdown->dateien_cat_name,$catok))
				{
				array_push($down_data, array ('downloadname' => $rowdown->downloadname, 'downloadid' => $rowdown->downloadid,
				'downloadlink' => $rowdown->downloadlink,'dateien_cat_name' => $rowdown->dateien_cat_name));
				}
				*/
				$down_data[] = $temp_array;
			}
			$this->content->template['down_data'] = $down_data;
		}
		// Artikel-Liste erstellen
		$sql = " SELECT DISTINCT(reporeID), cattextid, header ";
		$sql .= " FROM " . $this->cms->papoo_repore . "," . $this->cms->papoo_language_article . "";
		$sql .= " WHERE reporeID=lan_repore_id ";
		//$sql.=" AND lang_id=".$this->cms->lang_id."";
		$sql .= " AND lang_id=" . $this->db->escape($sprach_id) . "";
		$result = $this->db->get_results($sql);
		$down_art_data = array();
		if(!empty ($result)) {
			foreach($result as $rowdown) {
				array_push($down_art_data, array(
					'artikelid' => $rowdown->reporeID,
					'menuid' => $rowdown->cattextid,
					'artikel_header' => "nodecode:" . $this->diverse->encode_quote($rowdown->header)
				));
			}
			$this->content->template['down_art_data'] = $down_art_data;
		}
	}

	/**
	 * Ermittelt die CSS-Klassen welche im (QuickTag-)Editor bereit gestellt werden sollen
	 */
	function get_css_klassen()
	{
		$css_klassen = array();
		// Pfad zur CSS-Datei
		$css_datei = PAPOO_ABS_PFAD . "/styles/" . $this->content->template['style_dir'] . "/css/_index.css";
		if(file_exists($css_datei) && filesize($css_datei) > 0) {
			$datei = fopen($css_datei, "r");
			$css = fread($datei, filesize($css_datei));
			$css_array = array();
			// Suchmuster: (<text_in_klammern>)(text_nach_klammern),
			// daher auch das nonsense-Tag um Texte am Anfang, also vor einem Tag zu erfassen
			$match_expression = "/\.(.*)\{/i";
			// füllt $text_array in der Art:
			//		$text_array[0] = Text des gesamten Suchmusters
			//		$text_array[1] = CSS-Klassen-Name(n)
			preg_match_all($match_expression, $css, $css_array, PREG_SET_ORDER);
			// die Rückgabe zusammen stellen.
			foreach($css_array as $css_definition) {
				$css_klassen[] = trim($css_definition[1]);
			}
		}
		$this->content->template['css_data_klassen'] = $css_klassen;
		//return $css_klassen;
	}

	/**
	 * Vorschaudaten präsentieren
	 *
	 * @return void
	 */
	function make_vorschau()
	{
		$this->make_teaser_bild();
		$this->content->template['teaser_vorschau'] =
			$this->diverse->do_pfadeanpassen($this->teaserbild_in) . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser'];
		//  Beschreibung für Anzeige in Vorschau in normales Template
		$this->content->template['Beschreibung_vorschau'] =
			$this->replace->do_replace($this->diverse->do_pfadeanpassen($_SESSION['metadaten'][$this->unique_id][$this->replangid]['inhalt']));
	}

	/**
	 * Damit wird das ausgewählte Teaser Bild an den Teaser Text angehängt
	 *
	 * @param string $sprach_id
	 * @return void
	 */
	function make_teaser_bild($sprach_id = "")
	{
		$this->teaserbild_in = "";
		if(!empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaserbild'])) {
			$float = "float:" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['bildlr'] . "";
			// Sprache festlegen
			if(empty($sprach_id)) {
				$sprach_id = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['lang_id'];
			}
			#else $sprach_id = 1;
			$selectbild = sprintf("SELECT * FROM %s AS t1, %s AS t2 WHERE t1.image_name='%s' AND t1.image_id=t2.lan_image_id AND t2.lang_id='%d'",
				$this->cms->papoo_images,
				$this->cms->papoo_language_image,
				$this->db->escape($_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaserbild']),
				$this->db->escape($sprach_id)
			);
			$resultbild = $this->db->get_results($selectbild, ARRAY_A);
			if(!empty ($resultbild)) {
				foreach($resultbild as $bilddrin) {
					// störende Anführungszeichen entfernen
					$bilddrin = str_replace('"', '', $bilddrin);
					// Image-Tag zusammen setzen
					$this->teaserbild_in =
						'<img alt="' .
						$bilddrin['alt'] .
						'" title="' .
						$bilddrin['title'] .
						'" src="./images/thumbs/' .
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaserbild'] .
						'" class="teaserbild' .
						$_SESSION['metadaten'][$this->unique_id][$this->replangid]['bildlr'] .
						'" style="' .
						$float .
						'" />';
				}
			}
		}
	}

	/**
	 * @abstract Alle POST Variablen in eine Session eintragen
	 * @param $form_nummer
	 * @return bool|void
	 */
	function make_checked_session($form_nummer)
	{
		//Daten übergeben
		//from_aktiv
		//switch ($this->checked->from_aktiv)
		switch($form_nummer) {
			//Kommt von Inhalt und Überschrift
		case "1":
			//Überschrift
			if(!empty($this->checked->inhalt_ar['uberschrift'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['uberschrift'] =
					$this->checked->inhalt_ar['uberschrift'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['uberschrift'] = "";
			}
			//Überschrift ja/nein
			if(!empty($this->checked->inhalt_ar['hyn'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['hyn'] =
					$this->checked->inhalt_ar['hyn'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['hyn'] = "";
			}
			//Inhalt
			if(!empty($this->checked->inhalt_ar['inhalt'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['inhalt'] =
					$this->html->do_tidy($this->checked->inhalt_ar['inhalt']);
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['inhalt'] = "";
			}
			break;
			//kommt von Teaser
		case "2":
			//teaser
			if(!empty($this->checked->inhalt_ar['teaser'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser'] =
					$this->checked->inhalt_ar['teaser'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser'] = "";
			}
			//Teaser ja/nein atyn
			if(!empty($this->checked->inhalt_ar['atyn'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['atyn'] =
					$this->checked->inhalt_ar['atyn'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['atyn'] = "";
			}
			//Bild zum Teaser teaserbild
			if(!empty($this->checked->inhalt_ar['teaserbild'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaserbild'] =
					$this->checked->inhalt_ar['teaserbild'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaserbild'] = "";
			}
			//Teaser Bild links/rechts bildlr
			if(!empty($this->checked->inhalt_ar['bildlr'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['bildlr'] =
					$this->checked->inhalt_ar['bildlr'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['bildlr'] = "";
			}
			//Teaser Bildgröße
			if(!empty($this->checked->inhalt_ar['breite'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['breite'] =
					$this->checked->inhalt_ar['breite'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['breite'] = "";
			}
			if(!empty($this->checked->inhalt_ar['hohe'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['hohe'] =
					$this->checked->inhalt_ar['hohe'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['hohe'] = "";
			}
			//Teaser link
			if(!empty($this->checked->inhalt_ar['teaser_link'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser_link'] =
					$this->checked->inhalt_ar['teaser_link'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser_link'] = "";
			}
			break;
			//kommt von Rechte
		case "3":
			//Freigabe für andere gruppe
			if(!empty($this->checked->inhalt_ar['gruppe'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe'] =
					$this->checked->inhalt_ar['gruppe'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe'] = "";
			}
			//Wer darf im Frontend darauf zugreifen gruppe_write
			if(!empty($this->checked->inhalt_ar['gruppe_write'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe_write'] =
					$this->checked->inhalt_ar['gruppe_write'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe_write'] = "";
			}
			// Wer darf im backend schreibend rauf write_article
			if(!empty($this->checked->inhalt_ar['write_article'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['write_article'] =
					$this->checked->inhalt_ar['write_article'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['write_article'] = "";
			}
			//Darf der Artikel kommentiert werden
			if(!empty($this->checked->inhalt_ar['comment_yn'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['comment_yn'] =
					$this->checked->inhalt_ar['comment_yn'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['comment_yn'] = "";
			}
			break;
			//kommt von Einstellungen
		case "4":
			//MetaTitel
			if(!empty($this->checked->inhalt_ar['metatitel'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metatitel'] =
					$this->checked->inhalt_ar['metatitel'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metatitel'] = "";
			}
			if(!empty($this->checked->inhalt_ar['dok_teaserfix'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_teaserfix'] =
					$this->checked->inhalt_ar['dok_teaserfix'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_teaserfix'] = 0;
			}
			if(!empty($this->checked->inhalt_ar['dok_show_teaser_link'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_link'] =
					$this->checked->inhalt_ar['dok_show_teaser_link'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_link'] = 0;
			}
			if(!empty($this->checked->inhalt_ar['dok_show_teaser_teaser'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_teaser'] =
					$this->checked->inhalt_ar['dok_show_teaser_teaser'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_teaser'] = 0;
			}
			//Meta Description
			if(!empty($this->checked->inhalt_ar['metadescrip'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metadescrip'] =
					$this->checked->inhalt_ar['metadescrip'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metadescrip'] = "";
			}
			//Sprechende url
			if(!empty($this->checked->inhalt_ar['url_header'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['url_header'] =
					$this->checked->inhalt_ar['url_header'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['url_header'] = "";
			}
			//Meta Keywords
			if(!empty($this->checked->inhalt_ar['metakey'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metakey'] =
					$this->checked->inhalt_ar['metakey'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['metakey'] = "";
			}
			//Menüpunkt
			if(!empty($this->checked->inhalt_ar['cattext'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext'] =
					$this->checked->inhalt_ar['cattext'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext'] = "";
			}
			if(!empty($this->checked->inhalt_ar['cattext_ar'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext_ar'] =
					$this->checked->inhalt_ar['cattext_ar'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext_ar'] = "";
			}
			if(!empty($this->checked->inhalt_ar['formcatcat'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['formcatcat'] =
					$this->checked->inhalt_ar['formcatcat'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['formcatcat'] = "";
			}
			//veröffentlichen ja/nein
			if(!empty($this->checked->inhalt_ar['freigabe_internet'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_internet'] =
					$this->checked->inhalt_ar['freigabe_internet'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_internet'] = "";
			}
			//Veröffentlichen J/N sprachabhängig
			//$_SESSION['metadaten'][$this->unique_id][$this->replangid]['publish_yn_lang'] =
			//$tex->publish_yn_lang;
			if(!empty($this->checked->inhalt_ar['freigabe_internet'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['publish_yn_lang'] =
					$this->checked->inhalt_ar['freigabe_internet'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['publish_yn_lang'] = "";
			}
			//Downloads zählen
			if(!empty($this->checked->inhalt_ar['zahlen'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['zahlen'] =
					$this->checked->inhalt_ar['zahlen'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['zahlen'] = "";
			}
			//Startseite listen ja/nein
			if(!empty($this->checked->inhalt_ar['freigabe_teaser_list'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_teaser_list'] =
					$this->checked->inhalt_ar['freigabe_teaser_list'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_teaser_list'] = 0;
			}
			//RSS FEED Ja/Nein
			if(!empty($this->checked->inhalt_ar['lan_rss_yn'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['lan_rss_yn'] =
					$this->checked->inhalt_ar['lan_rss_yn'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['lan_rss_yn'] = 0;
			}
			//Pub Verfallsdatum
			if(!empty($this->checked->inhalt_ar['pub_verfall'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_verfall'] =
					$this->checked->inhalt_ar['pub_verfall'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_verfall'] = 0;
			}
			//Pub Startdatum
			if(!empty($this->checked->inhalt_ar['pub_start'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_start'] =
					$this->checked->inhalt_ar['pub_start'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_start'] = 0;
			}
			//Pub Verfallsdatum
			if(!empty($this->checked->inhalt_ar['pub_verfall_page'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_verfall_page'] =
					$this->checked->inhalt_ar['pub_verfall_page'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_verfall_page'] = 0;
			}
			if(!empty($this->checked->inhalt_ar['erstellungsdatum'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['erstellungsdatum'] =
					$this->checked->erstellungsdatum;
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['erstellungsdatum'] = 0;
			}
			//Pub Startdatum
			if(!empty($this->checked->inhalt_ar['pub_start_page'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_start_page'] =
					$this->checked->inhalt_ar['pub_start_page'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_start_page'] = 0;
			}
			//Pub Wohin nach ABlauf
			if(!empty($this->checked->inhalt_ar['pub_wohin'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_wohin'] =
					$this->checked->inhalt_ar['pub_wohin'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_wohin'] = 0;
			}
			//Pub Dauerhaft aktiv
			if(!empty($this->checked->inhalt_ar['pub_dauerhaft'])) {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_dauerhaft'] =
					$this->checked->inhalt_ar['pub_dauerhaft'];
			}
			else {
				$_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_dauerhaft'] = 0;
			}
			break;
			//kommt von Versionen
		case "5":
			break;
		default:
			return false;
			break;
		}
	}

	/**
	 * @abstract Alle POST Variablen in eine Session eintragen
	 */
	function make_checked_session_complete()
	{
		for($i = 1; $i <= 4; $i++) {
			$this->make_checked_session($i);
		}
	}

	/**
	 * @abstract Die Daten aus der Session wieder an die Seite übergeben
	 */
	function make_content()
	{
		/**
		 * Inhalt
		 */
		//Überschrift
		$this->content->template['uberschrift'] =
			"nodecode:" . $this->diverse->encode_quote($_SESSION['metadaten'][$this->unique_id][$this->replangid]['uberschrift']);
		//Hauptinhalt
		//$this->content->template['Beschreibung'] =
		//htmlentities("nodecode:".$_SESSION['metadaten'][$this->unique_id][$this->replangid]['inhalt']);
		$this->content->template['Beschreibung'] =
			"nodecode:" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['inhalt'];
		//Überschrift ja/nein
		if(!empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['hyn'])) {
			$this->content->template['hynchecked'] = "nodecode:checked=\"checked\"";
		}
		/**
		 * Teaser
		 */
		//teaser
		$this->content->template['teaser'] =
			"nodecode:" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser'];
		//Teaser ja/nein
		if(!empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['atyn'])) {
			$this->content->template['atynchecked'] = "nodecode:checked=\"checked\"";
		}
		else {
			$this->content->template['atynchecked'] = "";
		}
		//Bild zum Teaser wird automatisch gemacht
		//Teaser Bild links/rechts
		if($_SESSION['metadaten'][$this->unique_id][$this->replangid]['bildlr'] == "left") {
			$this->content->template['bildlr_links'] = "nodecode:checked=\"checked\"";
		}
		if($_SESSION['metadaten'][$this->unique_id][$this->replangid]['bildlr'] == "right") {
			$this->content->template['bildlr_rechts'] = "nodecode:checked=\"checked\"";
		}
		//Teaser Bildgröße
		$this->content->template['breiteteaser'] = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['breite'];
		$this->content->template['hoheteaser'] = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['hohe'];
		//Teaser link
		$this->content->template['teaser_link'] =
			"nodecode:" . $this->diverse->encode_quote($_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser_link']);
		/**
		 * Rechte
		 */
		//Freigabe für andere gruppe
		if(!empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe'])) {
			$this->content->template['checked'] = "nodecode:checked=\"checked\"";
		}
		//Wer darf im Frontend darauf zugreifen gruppe_write
		$result_gr = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe_write'];
		// Gruppendaten holen
		$result = $this->db->get_results("SELECT gruppenname, gruppeid FROM " . $this->cms->papoo_gruppe . " ORDER BY gruppenname ");
		$table_data2 = array();
		// alle Gruppen durchloopen und aktive anhaken
		foreach($result as $rowx) {
			// Administratoren nicht anzeigen. Diese bekommen immer alle Rechte.
			if($rowx->gruppeid != 1) {
				$checkedx = '';
				if(!empty ($result_gr) && is_array($result_gr)) {
					foreach($result_gr as $x) {
						if($rowx->gruppeid == $x) {
							$checkedx = 'nodecode:checked="checked"';
						}
					}
				}
				// FIXME Muss man nochmal drüberschauen und einen entgültigen Fix dafür schreiben
				else {
					if($this->checked->menuid == 10 && $this->cms->stamm_artikel_rechte_jeder == 1 && $rowx->gruppeid == 10) {
						$checkedx = 'nodecode:checked="checked"';
					}
				}
				array_push($table_data2, array(
					'gruppename' => $rowx->gruppenname,
					'gruppeid' => $rowx->gruppeid,
					'checkedx' => $checkedx
				));
			}
		}
		$this->content->template['table_data'] = $table_data2;
		$result_gr = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['write_article'];
		// Gruppendaten holen
		$result =
			$this->db->get_results("SELECT gruppenname, gruppeid FROM " . $this->cms->papoo_gruppe . " ORDER BY gruppenname ");
		$table_data2 = array();
		// alle Gruppen durchloopen und aktive anhaken
		foreach($result as $rowx) {
			// Administratoren nicht anzeigen. Diese bekommen immer alle Rechte.
			if($rowx->gruppeid != 1) {
				$checkedx = "";
				if(!empty ($result_gr)) {
					foreach($result_gr as $x) {
						if($rowx->gruppeid == $x) {
							$checkedx = 'nodecode:checked="checked"';
						}
					}
				}
				array_push($table_data2, array(
					'gruppename' => $rowx->gruppenname,
					'gruppeid' => $rowx->gruppeid,
					'checkedx' => $checkedx
				));
			}
		}
		$this->content->template['write_article'] = $table_data2;
		//Darf der Artikel kommentiert werden
		if(!empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['comment_yn'])) {
			$this->content->template['checked_comment'] = "nodecode:checked=\"checked\"";
		}
		else {
			$this->content->template['checked_comment'] = "";
		}
		//RSS FEED Js/nein
		if(!empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['lan_rss_yn'])) {
			$this->content->template['checked_lan_rss_yn'] = "nodecode:checked=\"checked\"";
		}
		else {
			$this->content->template['checked_lan_rss_yn'] = "";
		}
		//Dauerhaft veröffentlichen
		if(!empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_dauerhaft'])) {
			$this->content->template['checked_pub_dauerhaft'] = "nodecode:checked=\"checked\"";
		}
		else {
			$this->content->template['checked_pub_dauerhaft'] = "";
		}
		//Datum bis
		$this->content->template['pub_verfall'] =
			"" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_verfall'];
		//Datum von
		$this->content->template['pub_start'] =
			"" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_start'];
		//Datum bis
		$this->content->template['pub_verfall_page'] =
			"" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_verfall_page'];
		$this->content->template['erstellungsdatum'] =
			"" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['erstellungsdatum'];
		//Datum von
		$this->content->template['pub_start_page'] =
			"" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_start_page'];
		//nach Ablauf wohin
		$this->content->template['pub_wohin'] =
			"" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['pub_wohin'];
		/**
		 * Einstellungen
		 */
		//MetaTitel
		$this->content->template['metatitel'] =
			"nodecode:" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['metatitel'];
		//url_header
		$this->content->template['url_header'] =
			"nodecode:" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['url_header'];
		$this->content->template['dok_teaserfix'] =
			"nodecode:" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_teaserfix'];
		$this->content->template['dok_show_teaser_link'] =
			"nodecode:" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_link'];
		$this->content->template['dok_show_teaser_teaser'] =
			"nodecode:" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['dok_show_teaser_teaser'];
		//Meta Description
		$this->content->template['metadescrip'] =
			"nodecode:" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['metadescrip'];
		//Meta Keywords
		$this->content->template['metakey'] =
			"nodecode:" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['metakey'];
		//Menüpunkt
		//$this->content->template['menulist_data'] = $this->menu->data_front_publish;
		$this->menu->data_front_complete = $this->menu->menu_data_read("FRONT_PUBLISH", $this->replangid);
		if($this->cms->categories == 1) {
			// Kategorien rausholen
			$this->menu->preset_categories();
			$this->menu->get_menu_cats();
			$this->content->template['categories'] = 1;
		}
		else {
			$this->content->template['catlist_data'][0] = array("catid" => -1);
			$this->content->template['catlist_data'][1] = array("catid" => -1);
			$this->content->template['catlist_data'][2] = array("catid" => -1);
			$this->content->template['catlist_data'][3] = array("catid" => -1);
			$this->content->template['catlist_data'][4] = array("catid" => -1);
			$this->content->template['catlist_data'][5] = array("catid" => -1);
			$this->content->template['no_categories'] = 1;
		}
		$menu_data = $this->intern_menu->mak_menu_liste_zugriff();
		$this->content->template['menulist_data'] = $menu_data;
		$this->content->template['formcattext'] = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext'];
		$this->content->template['cattext_ar'] = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext_ar'];
		$this->content->template['formcatcat'] = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['formcatcat'];
		//veröffentlichen ja/nein nur hier!!
		if(!empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['publish_yn_lang'])) {
			$this->content->template['checked_internet'] = "nodecode:checked=\"checked\"";
		}
		else {
			$this->content->template['checked_internet'] = "";
		}
		//publish_yn_lang
		//Downloads zählen
		if(!empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['zahlen'])) {
			$this->content->template['down_checked'] = "nodecode:checked=\"checked\"";
		}
		else {
			$this->content->template['down_checked'] = "";
		}
		//Startseite listen ja/nein
		if(!empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_teaser_list'])) {
			$this->content->template['checked_startseite'] = 'nodecode:checked="checked"';
		}
		else {
			$this->content->template['checked_startseite'] = "";
		}
	}

	/**
	 * @abstract Liste der alten Versionen erstellen
	 */
	function make_version_list()
	{
		$reporeid = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'];
		if(empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'])) {
			$reporeid = $this->unique_id;
		}
		//Versionen anzeigen aus der aktuellen Bearbeitung
		$sql = sprintf("SELECT DISTINCT(header), version_art_time, version_art_id, lan_repore_id
			FROM %s, %s
			 WHERE version_art_reporeid='%d'
			 AND version_art_reporeid=lan_repore_id
			 AND %s.version_art_fix='0'
			 AND version_langart_id=version_art_id
			ORDER BY version_art_id DESC",
			$this->cms->tbname['papoo_version_article'],
			$this->cms->tbname['papoo_version_language_article'],
			$reporeid,
			$this->cms->tbname['papoo_version_article']
		);
		$this->content->template['versionlist'] = $result = $this->db->get_results($sql, ARRAY_A);
		//Versionen aus den alten Bearbeitungen
		//Versionen anzeigen
		//AND version_art_id=t3.versionid
		$sql = sprintf("SELECT DISTINCT(header), version_art_time, version_art_id, lan_repore_id, t3.dokuser_last, t4.username
						FROM %s,
						%s,
						%s as t3 ,
						%s AS t4

						WHERE version_art_reporeid='%d'
						AND version_art_reporeid=lan_repore_id
						AND reporeID=lan_repore_id
						AND %s.version_art_fix='1'
						AND version_langart_id=version_art_id
						AND version_art_id=t3.versionid
						AND t3.dokuser_last = t4.userid
						AND lang_id= '%d'
						ORDER BY version_art_id DESC",
			$this->cms->tbname['papoo_version_article'],
			$this->cms->tbname['papoo_version_language_article'],
			$this->cms->tbname['papoo_version_repore'],
			$this->cms->tbname['papoo_user'],
			$reporeid,
			$this->cms->tbname['papoo_version_article'],
			$this->cms->lang_back_content_id
		);
		$this->content->template['versionoldlist'] = $result = $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * @return void
	 * @desc Hier werden die eingegebenen Daten aus dem Formular überprüft
	 *            und formatiert für die Eingabe in die Datenbank
	 */
	function format_eingabe()
	{
		//alle checked an Session
		IfNotSetNull($this->checked->from_aktiv);
		$this->make_checked_session($this->checked->from_aktiv);
		//reporeid
		$this->content->template['reporeid'] = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'];
		//Daten an das Formular wieder zuweisen
		$this->make_content();
		if(!empty($this->checked->submit_vo)) {
			$this->make_vorschau();
		}
		$this->teaser_bild_groesse =
			$_SESSION['metadaten'][$this->unique_id][$this->replangid]['hohe'] . "x" . $_SESSION['metadaten'][$this->unique_id][$this->replangid]['breite'];
		// Ist die Veröffentlichung angeklickt
		if(!empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_internet'])) {
			// darf der User veröffentlichen, dann veröffentlicht setzen
			if(isset($this->allow_internet) && $this->allow_internet == 1) {
				$this->publish_yn = 1;
				$this->publish_yn_intra = 0;
				//$this->intranet_yn = 0;
				$this->allow_publish = 1;
			}
			// darf nicht veröffentlichen, dann auf stumm setzen
			else {
				$this->publish_yn = 1;
				$this->publish_yn_intra = 0;
				//$this->intranet_yn = 0;
				$this->allow_publish = 0;
				//Und Vorgesetzten benachrichtigen
				$this->chef_message = "yes";
			}
		}
		// noch nicht veröffentlichen, dann auf stumm setzen
		else {
			// darf der User veröffentlichen, dann veröffentlicht setzen
			if(isset($this->allow_internet) && $this->allow_internet == 1) {
				$this->publish_yn = 0;
				$this->publish_yn_intra = 0;
				//$this->intranet_yn = 0;
				$this->allow_publish = 1;
			}
			// darf nicht veröffentlichen, dann auf stumm setzen
			else {
				$this->publish_yn = 0;
				$this->publish_yn_intra = 0;
				//$this->intranet_yn = 0;
				$this->allow_publish = 0;
				//Und Vorgesetzten benachrichtigen
				//$this->chef_message="yes";
			}
		}
		//}
		// bis jetzt noch keine Funtion
		$this->doklesengrid = "";
		//url_header
		$_SESSION['metadaten'][$this->unique_id][$this->replangid]['url_header'] =
			$this->menu->urlencode($_SESSION['metadaten'][$this->unique_id][$this->replangid]['url_header']);
		// Schreibrechte festlegen, andere dürfen schreiben oder auch nicht, ja nach Anhakung
		$this->dokschreibengrid = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['gruppe'];
		// Reinen Text des teasers festlegen
		$this->teaser_bbcode = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaser'];
		// bbcode Parsen
		// Teaserbild anhängen an Teasertext
		$this->make_teaser_bild();
		// Teaser zusammenstellen
		$teaserfertig = $this->teaserbild_in;
		// Zeitstempel setzen, d.m.Y wegen zweistelligen Werten
		//$this->timestamp = date("d.m.Y  G:i:s");
		// soll kommentiert werden dürfen
		$this->allow_comment = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['comment_yn'];
		// Soll der Artikel auf der Startseite gelistet werden oder nicht
		//if ($_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_teaser_list'] == "ok"
		// or $_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_teaser_list'] == "2") {
		if($_SESSION['metadaten'][$this->unique_id][$this->replangid]['freigabe_teaser_list']) {
			// ja, soll gelistet werden
			$this->freigabe_teaser_list = "1";
		}
		else {
			// nein soll nicht
			$this->freigabe_teaser_list = "0";
		}
		// Pfad für das image berichtigen
		//$teaserfertig = (str_ireplace("\.\./images", "\./images", $teaserfertig));
		// Wenn sich Zugehörigkeit zu Menü-Punkt geändert hat, dann Order-ID raussuchen
		if($_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext_org'] !=
			$_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext']) {

			$this->order_id =
				$this->artikel_get_orderid($_SESSION['metadaten'][$this->unique_id][$this->replangid]['cattext']);
		}
		else {
			$this->order_id = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['orderid_org'];
		}
		// bearbeitete Daten escapen
		IfNotSetNull($artikel);
		IfNotSetNull($inhalt);
		$this->artikel = $artikel;
		$this->inhalt = $inhalt;
		$this->teaserfertig = $teaserfertig;
	}

	/**
	 * @return void
	 * @desc Hiermit werden die Rechte überprüft und zugewiesen, ob ein User
	 * für das Internet/Intranet veröffentlichen darf
	 * außerdem wird der Editor zugewiesen
	 */
	function check_rechte()
	{
		$this->darf_schreiben = "";
		// Wenn root, dann Rechte für alles geben
		if($this->user->username == "root") {
			$this->allow_internet = 1;
			$this->darf_schreiben = 1;
		}
		// ansonsten rechte zur Veröffentlichung Überprüfen
		else {
			$this->check_allow_internet();
			#$this->allow_internet = 1;
			$this->darf_schreiben = 1;
		}
		// Editor auswählen und zuweisen
		$this->check_editor();
		// die Rechte zum Veröffentlichen im Internet sind vorhanden
		if($this->allow_internet != 1) {
			// Freigabebutton laden
			$this->content->template['keine_freigabe'] = 'Submit1';
		}
		if(!empty($this->darf_schreiben)) {
			$this->content->template['darf_schreiben'] = 'ok';
		}
		// Wenn Internet, dann dafür Freigabe
		if($this->allow_internet == 1) {
			$this->content->template['internetok'] = 'Internet';
			$this->content->template['schreiben'] = '1';
		}
	}

	/**
	 * checken ob der User im Artikel schreiben darf
	 */
	function check_article_write()
	{
		//Überprüfen ob der User im aktuellen Artikel schreiben darf -> write_article
		//gruppenid des Users raussuchen
		$sql = sprintf("SELECT * FROM %s WHERE userid='%s'",
			$this->cms->tbname['papoo_lookup_ug'],
			$this->user->userid
		);
		$result = $this->db->get_results($sql);
		//checken ob die ok ist
		//Daten des Artikels
		if(empty($_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'])) {
			$reporeid = $this->checked->reporeid;
		}
		else {
			$reporeid = $_SESSION['metadaten'][$this->unique_id][$this->replangid]['reporeid'];
		}
		$sql = sprintf("SELECT gruppeid_wid_id FROM %s WHERE article_wid_id='%s'",
			$this->cms->tbname['papoo_lookup_write_article'],
			$this->db->escape($reporeid)
		);
		$result2 = $this->db->get_results($sql, ARRAY_A);
		foreach($result as $ok) {
			if(is_array($result2)) {
				if($this->diverse->deep_in_array($ok->gruppenid, $result2)) {
					$okx = "ok";
				}
			}
			if($ok->gruppenid == 1) {
				$okx = "ok";
			}
		}
		//wenn ja nix
		IfNotSetNull($okx);
		if($okx != "ok") {
			$this->sessionsave = "no";
			$this->content->template['darf_schreiben'] = '';
			$sql = sprintf("SELECT dokuser FROM %s WHERE reporeID='%s'",
				$this->cms->tbname['papoo_repore'],
				$this->db->escape($this->checked->reporeid)
			);
			$var = $this->db->get_var($sql);
			if($this->user->userid == $var) {
				$this->sessionsave = "";
				$this->content->template['darf_schreiben'] = 'ok';
			}
		}
		//wenn nein, sessionsave sperren und Save Formular sperren
	}

	/**
	 * Ausgabe von alt und title und longdesc in verschiedenen Sprachen
	 *
	 * @return void
	 */
	function make_div_lang()
	{
		// aktive Sprachen raussuchen
		$eskuel = "SELECT lang_id, lang_short, lang_long FROM " . $this->cms->papoo_name_language . " WHERE more_lang='2'";
		$reslang = $this->db->get_results($eskuel);
		// Daten dazu raussuchen
		foreach($reslang as $lan) {
			// wenn keine andere Langid gesetzt ist
			if(empty ($this->checked->lang_id)) {
				// aktive Standardsprache setzen
				//if ($this->cms->lang_backend == $lan->lang_short) {
				if($this->cms->lang_back_short == $lan->lang_short) {
					// aktuelle id
					$this->content->template['active_language'] = $lan->lang_id;
					// aktuelle lange Bezeichnung
					$this->content->template['article'] = $lan->lang_long;
				}
			}
			// ansonsten setzen wie aus lang_id
			else {
				if($this->checked->lang_id == $lan->lang_id) {
					$this->content->template['active_language'] = $lan->lang_id;
					$this->content->template['article'] = $lan->lang_long;
				}
			}
			$aktiv = "";
			//if (empty($this->langidsel)) $this->langidsel=$_SESSION['back_aktu_article_edit_langid'];
			if($lan->lang_id == $this->replangid) {
				$aktiv = "_aktiv";
			}
			// Daten für die Links
			array_push($this->content->template['menlang'], array(
					'language' => $lan->lang_long,
					'lang_id' => $lan->lang_id,
					'aktiv' => $aktiv,
				)
			);
		}
		if(!isset($this->content->template['article'])) {
			$this->content->template['article'] = NULL;
		}
	}

	/**
	 * gibt die Order-ID für den Artikel unter dem Menü-Punkt $menuid zurück
	 *
	 * @param $menuid
	 * @return int
	 */
	function artikel_get_orderid($menuid)
	{
		// Test ob Artikel der Start-Seite zugehört
		if($menuid == 1 || $menuid == 0) {
			//Nicht nach Aktualität, also nach hinten
			if($this->cms->stamm_artikel_order != 1) {
				$sql = sprintf("SELECT MAX(order_id_start) FROM %s WHERE teaser_list='1'",
					$this->cms->papoo_repore
				);
			}
			//Nach Aktualität also nach vorne
			else {
				$sql = "";
				$start_orderid = 1;
			}
		}
		else {
			$sql = sprintf("SELECT MAX(reporeID) FROM %s WHERE cattextid='%d'",
				$this->cms->papoo_repore,
				$this->db->escape($menuid)
			);
		}
		$orderid = (int)$this->db->get_var($sql);
		$orderid += 1;
		if(!empty($start_orderid)) {
			$orderid = 1;
		}
		return $orderid;
	}

	/**
	 * Sortiert die Artikel, die zum Menü-Punkt $menuid gehören, neu
	 * so werden evtl. entstandene Löcher entfernt
	 *
	 * @param int $menuid
	 */
	function artikel_reorder($menuid = 0)
	{
		// Test ob Artikel der Start-Seite zugehört
		if($menuid == 1 || $menuid == 0) {
			// Artikel-Liste der Startseite zusammenstellen, geordnet nach order_id_start
			$sql = sprintf("SELECT reporeID FROM %s WHERE teaser_list='1' ORDER BY order_id_start",
				$this->cms->papoo_repore
			);
			$order_feld = "order_id_start";
		}
		else {
			// Artikel-Liste des aktuellen Menü-Punktes zusammenstellen, geordnet nach order_id
			$sql = sprintf("SELECT lart_id FROM %s WHERE lcat_id='%d' ORDER BY lart_order_id",
				$this->cms->tbname['papoo_lookup_art_cat'],
				$this->db->escape($menuid)
			);
			$order_feld = "lart_order_id";
		}
		$artikel_liste = $this->db->get_results($sql);
		// Artikel neu durchnummerieren
		if(!empty($artikel_liste)) {
			$nummer_neu = 10;
			if($menuid == 1 || $menuid == 0) {
				foreach($artikel_liste as $artikel) {
					$sql = sprintf("UPDATE %s SET %s='%d' WHERE reporeID='%d'",
						$this->cms->papoo_repore,
						$order_feld,
						$nummer_neu,
						$this->db->escape($artikel->reporeID)
					);
					$this->db->query($sql);
					$nummer_neu += 10;
				}
			}
			else {
				$sql = sprintf("DELETE FROM %s WHERE lcat_id='%d'",
					$this->cms->tbname['papoo_lookup_art_cat'],
					$this->db->escape($menuid)
				);
				$this->db->query($sql);
				foreach($artikel_liste as $artikel) {
					$sql = sprintf("INSERT INTO %s SET lart_order_id='%d' , lart_id='%d' , lcat_id='%d'",
						$this->cms->tbname['papoo_lookup_art_cat'],
						$nummer_neu,
						$this->db->escape($artikel->lart_id),
						$menuid
					);
					$this->db->query($sql);
					$nummer_neu += 10;
				}
			}
		}
	}

	/**
	 * Unter-Aktions-Weiche für Reihenfolge ändern
	 */
	function switch_order()
	{
		// Liste mit Artikeln anzeigen
		if(empty($this->checked->formartikelid)) {
			// Template-Weiche setzen
			$this->content->template['templateweiche'] = "REORDER";
			$this->order_liste();
		}
		// Menü-Punkt wurde ausgewählt -> Reihenfolge ändern
		else {
			$this->order_change($this->checked->formartikelid, $this->checked->formmenuid);
			// Rest ist wie bei Liste anzeigen
			$this->content->template['templateweiche'] = "REORDER";
			$this->order_liste();
		}
	}

	/**
	 * Liste der Artikel anzeigen
	 */
	function order_liste()
	{
		// 1. Auswahl-Liste Menü-Punkte aufbauen
		$this->content->template['menulist_data'] =
			$this->menu->menu_data_read("FRONT_PUBLISH", $this->replangid);
		// 2. ID des ausgewählten Menü-Punktes bestimmen
		if(!empty($this->checked->formmenuid)) {
			$menuid = $this->checked->formmenuid;
		}
		else {
			$menuid = 1;
		}
		$this->content->template['formmenuid'] = $menuid;
		// 3. Liste der Artikel des Menü-Punktes aufbauen
		$start = 0;
		$artikel_liste = $this->artikel->get_artikel($menuid, "ALL", $start);
		// macht eigentlich nur Umwandlung von Object in benamtes Array :-((
		// besser wäre in artikel->get_artikel gleich ein ARRAY_A auszugeben.
		$artikel_data = array();
		if(!empty($artikel_liste)) {
			foreach($artikel_liste as $artikel) {
				$temp_array = array();
				if($artikel) {
					foreach($artikel as $name => $wert) {
						$temp_array[$name] = $wert;
					}
					$artikel_data[] = $temp_array;
				}
			}
		}
		$this->content->template['artikellist_data'] = $artikel_data;
	}

	/**
	 * Hiermit kann die Reihenfolge der Menüpunkte verändert werden.
	 *
	 * @param int $artikel_id
	 * @param int $menuid
	 * @return void
	 */
	function order_change($artikel_id = 0, $menuid = 0)
	{
		//Test ob Artikel der Start-Seite ($menuid = 1) getauscht werden
		if($menuid == 1 || $menuid == 0) {
			$startseite = 1;
		}
		else {
			$startseite = 0;
		}
		if(!empty($this->checked->saveorder_artikel)) {
			$this->order_partner_wechsel($startseite, $menuid);
		}
		$this->artikel_reorder($menuid);
		global $rssfeed;
		if(is_object($rssfeed)) {
			$rssfeed->create_feed("now");
		}
	}

	/**
	 * Wechselt die Ordnungs-ID $order_id der beiden Wechsel-Partner $vorgaenger und $nachfolger
	 *
	 * @param int $startseite
	 * @param int $menuid
	 */
	function order_partner_wechsel($startseite = 0, $menuid = 0)
	{
		// Test ob Artikel auf der Startseite ist.
		if($startseite) {
			foreach($this->checked->formartikelid as $key => $value) {
				$sql = sprintf("UPDATE %s SET order_id_start='%d' WHERE reporeID='%d'",
					$this->cms->papoo_repore,
					$this->db->escape($value),
					$this->db->escape($key)
				);
				$this->db->query($sql);
			}
		}
		// else $order_feld = "order_id";
		else {
			foreach($this->checked->formartikelid as $key => $value) {
				$sql = sprintf("UPDATE %s SET lart_order_id='%d' WHERE lart_id='%d' AND lcat_id='%d' ",
					$this->cms->tbname['papoo_lookup_art_cat'],
					$this->db->escape($value),
					$this->db->escape($key),
					$this->db->escape($menuid)
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * @abstract Wenn die Thumbnails in der Größe geändert wurden eintragen und
	 * neues Bild mit neuem thumb erzeugen und in die Datenbank eintragen
	 *
	 * @param $sessdat
	 * @param $reporeid
	 */
	function image_new_thumb($sessdat, $reporeid)
	{
		//Daten aus der Session holen
		if(!empty($sessdat['breite'])) {
			$this->checked->breite = $sessdat['breite'];
		}
		if(!empty($sessdat['hohe'])) {
			$this->checked->hohe = $sessdat['hohe'];
		}
		$this->checked->teaserbild = $sessdat['teaserbild'];
		//Wenn geändert wurde
		if(isset($this->checked->breite) && $this->checked->breite > 50 or isset($this->checked->hohe) && $this->checked->hohe > 50) {
			if(isset($this->checked->breite) && $this->checked->breite < 500 or isset($this->checked->hohe) && $this->checked->hohe < 500) {
				//neues thumb erzeugen das dann auch als Bild, aber immer nur eines pro Klasse
				//Klasse aktivieren
				$this->image_core = new image_core_class();
				//Daten zuweisen und erstellen
				$this->image_core->image_load($this->checked->teaserbild);
				$infos = $this->image_core->image_infos;
				$this->image_core->pfad_images = $this->image_core->pfad_thumbs;
				// Neue Bildgröße berechnen
				$neue_breite = $this->checked->breite;
				$neue_hoehe = $this->checked->hohe;
				//neues Bild erstellen
				$img_new = $this->image_core->image_create(array($neue_breite, $neue_hoehe), ($infos['type']));
				$img_src = $this->image_core->image_create($infos['bild_temp']);
				@imagecopyresized($img_new, $img_src, 0, 0, 0, 0, $neue_breite, $neue_hoehe, $infos['breite'], $infos['hoehe']);
				// Evtl. altes temporäres Bild löschen
				if(strpos("XXX" . $this->checked->image_name, "temp_" . $this->user->username)) {
					@unlink($this->image_core->pfad_images . $this->checked->image_name);
				}
				// Datei-Name für Bild festlegen
				$neuname = "reporeid" . $reporeid;
				$temp_name = $neuname . "." . strtolower($infos['type']);
				if(file_exists($this->image_core->pfad_images . $temp_name)) {
					@unlink($this->image_core->pfad_images . $temp_name);
				}
				$this->image_core->image_save($img_new, $this->image_core->pfad_images . $temp_name);
				@ImageDestroy($img_new);
				@ImageDestroy($img_src);
				//Artikeldatenbank updaten mit Image Name und dem richtigen Eintrag für den teaser
				$sql = sprintf("SELECT lan_teaser_img_fertig FROM %s WHERE lan_repore_id='%s'",
					$this->cms->tbname['papoo_language_article'],
					$this->db->escape($reporeid)
				);
				$result = $this->db->get_var($sql);
				$neutea = @str_ireplace($this->checked->teaserbild, $temp_name, $result);
				$sql = sprintf("UPDATE %s SET  lan_teaser_img_fertig='%s' WHERE lan_repore_id='%s'",
					$this->cms->tbname['papoo_language_article'],
					$this->db->escape($neutea),
					$this->db->escape($reporeid)
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 *
	 */
	function set_replangid()
	{
		$templang = 0;
		if(!empty($this->checked->submitlang)) {
			$sql = sprintf("SELECT lang_id FROM %s WHERE lang_long='%s' OR lang_id='%d' ",
				$this->cms->tbname['papoo_name_language'],
				$this->db->escape($this->checked->submitlang),
				$this->db->escape($this->checked->submitlang)
			);
			$templang = $this->db->get_var($sql);
		}
		if(!empty($_SESSION['langid_front'])) {
			$sprach_id = $_SESSION['langid_front'];
		}
		if(!$templang) {
			$templang = $sprach_id;
		}
		if(!$templang) {
			$templang = $_SESSION['back_aktu_article_edit_langid'];
		}
		if(!$templang) {
			$templang = $this->checked->f_langid;
		}
		if(!$templang) {
			$templang = $this->cms->lang_id;
		}
		$this->replangid = $templang;
		$sql = sprintf("SELECT lang_long FROM %s WHERE lang_id='%s'",
			$this->cms->tbname['papoo_name_language'],
			$this->db->escape($templang)
		);
		$this->content->template['aktulanglong'] = $this->db->get_var($sql);
		$this->content->template['tinymce_lang_id'] = $templang;
		// lang_short für TinyMCE bestimmen, da TinyMCE nicht alle Sprachen untertützt solange mit Notlösung cms->lang_back_short arbeiten
		//$sql = sprintf("SELECT lang_short FROM %s WHERE lang_id='%d'", $this->cms->tbname['papoo_name_language'], $templang);
		//$this->content->template['tinymce_lang_short'] = $this->db->get_var($sql);
		$this->content->template['tinymce_lang_short'] = $this->cms->lang_back_short;
		$_SESSION['back_aktu_article_edit_langid'] = $this->replangid;
	}
}

$intern_artikel = new intern_artikel();