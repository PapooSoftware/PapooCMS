<?php

/**
 * Class keywords
 */
#[AllowDynamicProperties]
class keywords
{
	/**
	 * keywords constructor.
	 */
	function __construct()
	{
		global $user, $diverse, $cms, $content;
		$this->user = &$user;
		$this->diverse = &$diverse;
		$this->cms = &$cms;
		$this->content = &$content;

		// Aufruf der Aktionsweiche
		$this->make_admin();
		//Post Papoo
		$this->post_papoo();
	}

	/**
	 * Aktionsweiche
	 */
	function make_admin()
	{
		global $template;
		if(defined("admin")) {
			$this->user->check_intern();
			// Einbindung globaler papoo-Objekte
			global $content, $checked, $db, $cms;
			$this->content = &$content;
			$this->checked = &$checked;
			$this->db = &$db;
			$this->cms = &$cms;

			if(strpos("XXX" . $template, "keywords_back.html")) {
				//Einstellungen �berabeiten
				$this->make_einstellungen();
				//Wenn Aktualisiert werden soll
				if(!empty($this->checked->submit_aktu)) {
					$this->aktualisiere_liste();
					//Aktualisiert setzen
					$this->content->template['is_aktu'] = "ok";
					//Einstellungen �berabeiten
					$this->make_einstellungen();
				}
			}
		}
	}

	/**
	 * Liste der Eintr�ge aktualisieren
	 */
	function aktualisiere_liste()
	{
		//Aktive Sprachen rausbekommen
		$sql=sprintf("SELECT * FROM %s WHERE more_lang=2",
			$this->cms->tbname['papoo_name_language']
		);
		$reslang=$this->db->get_results($sql,ARRAY_A);
		//F�r alle Sprachen
		if (!empty($reslang)) {
			foreach ($reslang as $lang) {
				//vorhandene Einstellungen auslesen
				$sql=sprintf("SELECT keywords_stop_wrter, keywords_basis_der_tag_cloud
					FROM %s",
					$this->cms->tbname['papoo_keywords_tabelle']
				);
				$result=$this->db->get_results($sql,ARRAY_A);

				//Ausschli�daten erstellen
				$this->ausschluss=explode("\n",$result['0']['keywords_stop_wrter']);

				if ($result['0']['keywords_basis_der_tag_cloud'] == 1) {
					//Artikel des CMS
					$sql = sprintf("SELECT lan_metakey
						FROM %s WHERE lang_id='%d'",
						$this->cms->tbname['papoo_language_article'],
						$lang['lang_id']
					);
					$result = $this->db->get_results($sql, ARRAY_A);
				}
				else {
					//Shop
					if(!isset($this->cms->tbname['plugin_shop_produkte_lang'])) {
						// Nicht installiert
						$this->content->template['keywords_error'] = 1;
					}
					else {
						$sql = sprintf("SELECT DISTINCT produkte_lang_met_keywords AS lan_metakey
						FROM %s WHERE produkte_lang_lang_id='%d' AND produkte_lang_aktiv='1'",
							$this->cms->tbname['plugin_shop_produkte_lang'],
							$this->cms->lang_id
						);
						$result=$this->db->get_results($sql,ARRAY_A);
					}
					IfNotSetNull($result);
				}
				//Array erstellen mit allen Eintr�gen
				$liste=$this->make_liste($result);
				//Eintr�ge durchz�hlen
				$insert_daten=$this->count_liste($liste);
				//Einlesen in die Datenbank
				$this->insert_into_db($insert_daten,$lang['lang_id']);
			}
		}
		//Aktualisierungsdatum setzen
		$this->set_aktu_date();
	}

	/**
	 * Das letzte Aktualisierungsdatum setzen
	 */
	function set_aktu_date()
	{
		//Statement
		$sql=sprintf("UPDATE %s SET keywords_text='%d' ",
			$this->cms->tbname['papoo_keywords_tabelle'],
			time()
		);
		$this->db->query($sql);
	}

	/**
	 * Schriftgr��e einstellen zw. Min und Max Wert
	 *
	 * @param array $result
	 * @return array
	 */
	function make_fontsize($result=array())
	{
		$untersch=array();
		//Anzahl der unterschiedlichen Werte rausbekommen
		if (!empty($result)) {
			foreach ($result as $eintrag) {
				$wio=$eintrag['keywords_liste_wieoft'];
				if (empty($untersch[$wio])) {
					$untersch[$wio]=1;
				}
				else {
					$untersch[$wio]++;
				}
			}
		}
		ksort($untersch);

		//Anzahl
		$gesamt=count($untersch);
		if (empty($gesamt)) {
			$gesamt=1;
		}
		//Schrittweite
		$schritte=90;
		$schritt=round(100/$gesamt);
		if ($schritt>20) {
			$schritt=20;
			$schritte=120;
		}

		foreach ($untersch as $key=>$eintrag) {
			$untersch[$key]=$schritte;
			$schritte=$schritte+$schritt;
		}
		$i=0;
		if (!empty($result)) {
			foreach ($result as $eintrag) {
				$key=$eintrag['keywords_liste_wieoft'];
				$result[$i]['keywords_font_size']=$untersch[$key];
				$result[$i]['keywords_link']=$this->make_link($eintrag['keywords_liste_keyword']);
				#$result[$i]['keywords_line_height']=$untersch[$key];
				$i++;
			}
		}
		return $result;
	}

	/**
	 * Eintr�ge s�ubern
	 *
	 * @param string $eintrag
	 * @return mixed|string
	 */
	function clean_input($eintrag="")
	{
		$eintrag = trim($eintrag);
		$eintrag = str_replace("\n", "", $eintrag);
		$eintrag = str_replace("\r", "", $eintrag);
		$eintrag = str_replace("\t", "", $eintrag);
		$eintrag = str_replace(",", "", $eintrag);
		$eintrag = str_replace(";", "", $eintrag);
		$eintrag = str_replace(".", "", $eintrag);
		$eintrag = strip_tags($eintrag);
		$eintrag = stripslashes($eintrag);
		if(function_exists("mb_strtolower")) {
			$eintrag = mb_strtolower($eintrag, "UTF-8");
		}
		return $eintrag;
	}

	/**
	 * Eintr�ge durchz�hlen
	 *
	 * @param array $liste
	 * @return array
	 */
	function count_liste($liste=array())
	{
		//COunter erzeugen
		$count = array();
		//Eintr�ge durchgehen
		if(!empty($liste)) {
			foreach($liste as $eintrag) {
				//Nur wenn nicht leer und l�nger als 2 Buchstaben
				if(!empty($eintrag) && strlen($eintrag) > 2) {
					//Eintrag clearen
					$eintrag = $this->clean_input($eintrag);
					//Z�hlen
					if(empty($count[$eintrag])) {
						$count[$eintrag] = 1;
					}
					else {
						$count[$eintrag]++;
					}
				}
			}
			array_multisort($count, SORT_DESC);
		}
		return $count;
	}

	/**
	 * Liste der Keywords in die Datenbank einlesen
	 *
	 * @param array $insert
	 * @param int $lang_id
	 */
	function insert_into_db($insert=array(),$lang_id=1)
	{
		// wenn nicht leer
		if (!empty($insert)) {
			$sql=sprintf("DELETE FROM %s WHERE keywords_liste_lang_id='%s'",
				$this->cms->tbname['papoo_keywords_liste'],
				$this->db->escape($lang_id)
			);
			$this->db->query($sql);
			$i=0;
			foreach ($insert as $key=>$value) {
				//Wenn die max. Anzahl der Links erreicht, �berspringen
				if ($i>$this->maxwert) {
					continue;
				}
				$sql=sprintf("INSERT INTO %s SET keywords_liste_wieoft='%d', keywords_liste_lang_id='%s', keywords_liste_keyword='%s'",
					$this->cms->tbname['papoo_keywords_liste'],
					$this->db->escape($value),
					$this->db->escape($lang_id),
					$this->db->escape($key)
				);
				$this->db->query($sql);
				$i++;
			}
		}
	}

	/**
	 * Array aus einer ABfrage erstellen
	 *
	 * @param array $result
	 * @return array|null
	 */
	function make_liste($result=array())
	{
		//Vars erstellen
		$komplett = array();

		//Es existiert überhaupt eines
		if(!empty($result)) {
			//Einträge durchgehen
			foreach($result as $value) {
				IfNotSetNull($value['lan_metakey']);
				$datenarray = explode(";", $value['lan_metakey']);
				if(is_array($datenarray)) {
					$datenarray = array_unique($datenarray);
				}
				if(!strstr($value['lan_metakey'], ";")) {
					$value['lan_metakey'] = str_replace(",", ";", $value['lan_metakey']);
					$datenarray = explode(";", $value['lan_metakey']);
					if(is_array($datenarray)) {
						$datenarray = array_unique($datenarray);
					}
				}
				$komplett = array_merge($komplett, $datenarray);
			}
		}

		foreach ($this->ausschluss as $dat) {
			if(is_array($komplett)) {
				$i = 0;
				$dat = trim($dat);
				foreach($komplett as $key2 => $value2) {
					$value2 = trim(strtolower($value2));
					if($value2 == $dat) {
						$komplett[$i] = "";
					}
					$i++;
				}
			}
		}
		if(is_array($komplett)) {
			foreach($komplett as $key => $value) {
				$value = trim($value);
				if(!empty($value)) {
					$neu[] = $value;
				}
			}
		}
		IfNotSetNull($neu);
		return $neu;
	}
	/**
	 * Einstellungen des Plugins durchgehen
	 *
	 */
	function make_einstellungen()
	{
		$max = 50;
		if(!isset($this->checked->keywords_wieviel) || isset($this->checked->keywords_wieviel) && $this->checked->keywords_wieviel > $max) {
			$this->checked->keywords_wieviel = $max;
		}

		//Wenn ein neuer Wert eingetragen wird
		if(!empty($this->checked->submit_keyword)) {
			$sql = sprintf("UPDATE %s SET
										keywords_wieoft='%d',
										keywords_wieviel='%d',
										keywords_stop_wrter='%s',
										keywords_basis_der_tag_cloud ='%s'",
				$this->cms->tbname['papoo_keywords_tabelle'],
				$this->db->escape($this->checked->keywords_wieoft),
				$this->db->escape($this->checked->keywords_wieviel),
				$this->db->escape($this->checked->keywords_stop_wrter),
				$this->db->escape($this->checked->keywords_basis_der_tag_cloud)
			);
			$this->db->query($sql);
			$this->maxwert = $this->checked->keywords_wieviel;
			//Liste aktualisieren
			$this->aktualisiere_liste();
		}

		//vorhandene Einstellungen auslesen
		$sql = sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_keywords_tabelle']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$result['0']['keywords_stop_wrter'] = "nobr:" . $result['0']['keywords_stop_wrter'];

		//Der maximale WErt an Links f�r die Aktualisierung
		$this->maxwert = $result['0']['keywords_wieviel'];
		//An Tempalte zuweisen
		$this->content->template['keyword'] = $result;
		//Liste der KEywords $this->cms->tbname['papoo_keywords_liste']
		$sql = sprintf("SELECT * FROM %s
									WHERE keywords_liste_lang_id='%d'
									ORDER BY keywords_liste_wieoft DESC",
			$this->cms->tbname['papoo_keywords_liste'],
			$this->cms->lang_back_content_id
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		$this->content->template['keywords_liste']=$result;
	}

	/**
	 * Aktualisierung checken
	 * wenn nicht aktuelle dann setzen
	 */
	function check_aktu()
	{
		$sql=sprintf("SELECT keywords_text, keywords_wieoft,keywords_wieviel FROM %s ",
			$this->cms->tbname['papoo_keywords_tabelle']
		);
		$daten=$this->db->get_results($sql,ARRAY_A);
		$this->maxwert=$daten['0']['keywords_wieviel'];
		//Check
		if ($daten['0']['keywords_text']+$daten['0']['keywords_wieoft']<time()) {
			//Liste aktualisieren
			$this->aktualisiere_liste();
			//Datum neu setzen
			$this->set_aktu_date();
		}
	}

	/**
	 * keywords::post_papoo()
	 *
	 * @return void
	 */
	function post_papoo()
	{
		//Wenn nicht Admin
		if (!defined("admin")) {
			// Einbindung globaler papoo-Objekte
			global $content, $checked, $db, $weiter, $cms, $user;
			$this->content = & $content;
			$this->checked = & $checked;
			$this->db = & $db;
			$this->weiter = & $weiter;
			$this->cms = & $cms;
			$this->user = &$user;
			/*
			if (!empty($this->cms->tbname['plugin_shop_daten'])) {
				$shop = new shop_class();
				$this->shop = &$shop;
			}
			*/

			//Aktu checken
			$this->check_aktu();

			//Liste der KEywords $this->cms->tbname['papoo_keywords_liste']
			$sql=sprintf("SELECT * FROM %s
										WHERE keywords_liste_lang_id='%d'
										ORDER BY keywords_liste_keyword ASC",
				$this->cms->tbname['papoo_keywords_liste'],
				$this->cms->lang_id
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			if ($this->cms->mod_surls==1 && empty($this->checked->keyword)) {
				$this->checked->keyword=str_replace(".html","",$this->checked->var3);
			}
			if (empty($this->checked->page)) {
				$this->checked->page=1;
			}
			if (!empty($this->checked->keyword) && stristr($_SERVER['REQUEST_URI'],"keywordplugin")) {
				$this->content->template['site_title']="Suchergebnisse: ".$this->checked->keyword." - Seite ".$this->checked->page." - ".$this->content->template['site_title'];
			}

			//Fontsizen
			$result=$this->make_fontsize($result);

			$this->content->template['keywords_liste']=$result;
			$this->keywordliste=$result;
			//Wenn Anzeige
			global $template;
			if ($this->cms->mod_surls==1) {
				if ($_GET['var2']=="keywords-front") {
					$template=PAPOO_ABS_PFAD."/plugins/keywords/templates/keywords_front.html";
				}
				else {
					if ($_GET['var1']=="keywordsplugin") {
						$this->diverse->not_found();
					}
				}
			}
			if ($this->cms->mod_free==1 && !empty($this->checked->keyword)) {
				$template=PAPOO_ABS_PFAD."/plugins/keywords/templates/keywords_front.html";
			}

			//vorhandene Einstellungen auslesen
			$sql=sprintf("SELECT
											keywords_basis_der_tag_cloud
											FROM %s",
				$this->cms->tbname['papoo_keywords_tabelle']
			);
			$basis=$this->db->get_var($sql);

			if (stristr($template,"keywords_front.html")) {
				//Normal Artikel
				if ($basis==1) {
					$this->show_front();
				}
				//Jetzt nur Shop
				else {
					$this->shop_front = new shop_class_frontend();
					$this->do_newest();
				}
			}
		}
	}

	/**
	 * Alle Eintr�ge zu dem Keyword anzeigen
	 *
	 * @param int $anzahl
	 * @return string|array|null
	 */
	function show_front($anzahl=0)
	{
		//$this->checked->keyword="";

		//Alle Eintr�ge in der Sprache mit dem Keyword...
		if ($anzahl==0) {
			$anzahl=$this->get_anzahl();
		}
		//LImit pro Seite
		$this->weiter->make_limit(10);
		$this->weiter->modlink="no";
		$this->weiter->result_anzahl =$anzahl;

		$what = "teaser";
		$this->weiter->do_weiter( $what );

		//Wenn Eintr�ge vorhanden
		if ($anzahl >0) {
			//Daten aus der Datenbank rausholen
			$daten=$this->get_data();
			//Daten verarbeiten
			$this->make_teaserlist($daten);
		}
		$this->content->template['plugin_keyword_keyword']=$this->checked->keyword;
		$this->content->template['plugin_keyword_count']=$anzahl;

		return $this->content->template['plugin_keyword_keyword'];
	}

	/**
	 * Teaserdaten erstellen
	 *
	 * @param $daten
	 */
	function make_teaserlist($daten)
	{
		//$this->content->template['key_daten']=$daten;
		$temp_daten = array();

		if (!empty($daten)) {
			global $diverse;
			foreach ($daten as $eintrag) {
				$first_url=$eintrag['url_header'][0];
				if ($first_url=="/") {
					$eintrag['url_header']=substr($eintrag['url_header'],1,strlen($eintrag['url_header']));
				}
				$eintrag['lan_teaser'] = $diverse->do_pfadeanpassen($eintrag['lan_teaser']);
				$temp_daten[] = $eintrag;
			}
		}
		$this->content->template['key_daten']=$temp_daten;
	}

	/**
	 * Einen Link erstellen
	 *
	 * @param $keyword
	 * @return string|null
	 */
	function make_link($keyword)
	{
		IfNotSetNull($this->checked->keyword);
		//Normal
		if ($this->cms->mod_rewrite==1) {
			$link="plugin.php?menuid=7&amp;template=keywords/templates/keywords_front.html&amp;keyword=".$keyword;
			$this->content->template['linktext']="index.php";
			$this->weiter->weiter_link ="plugin.php?menuid=1&amp;template=keywords/templates/keywords_front.html&amp;keyword=".$this->checked->keyword;
		}
		//Mod_rewrite
		if ($this->cms->mod_rewrite==2 && $this->cms->mod_surls!=1) {
			$link="plugin/menuid/7/template/keywords:::templates:::keywords_front.html/keyword/".$keyword;
			$this->content->template['linktext']="index";
			$this->weiter->weiter_link ="plugin.php?menuid=1&amp;template=keywords/templates/keywords_front.html&amp;keyword=".$this->checked->keyword;
		}
		//S-urls
		if ($this->cms->mod_surls==1) {
			//Spreachende url des ersten Men�punktes erstellen
			$webvar=$this->cms->webvar;
			$link=$webvar."keywordplugin/keywords-front/".$keyword."/";
			$this->content->template['linktext']="";
			$this->weiter->weiter_link ="plugin.php?menuid=7&amp;template=keywords/templates/keywords_front.html&amp;keyword=".$this->checked->keyword;
		}

		if ($this->cms->mod_free==1) {
			//Spreachende url des ersten Men�punktes erstellen
			//$webvar=$this->cms->webvar;
			IfNotSetNull($webvar);
			$link=$webvar."keywordplugin/keywords-front/".$keyword."/";
			$this->content->template['linktext']="";
			$this->weiter->weiter_link ="plugin.php?menuid=7&amp;template=keywords/templates/keywords_front.html&amp;keyword=".$this->checked->keyword;
			#$sql=sprint_f("SELECT ");
		}
		IfNotSetNull($link);
		return $link;
	}

	/**
	 * Anzahl aller Eintr�ge von Artikeln in der Datenbank raussuchen
	 */
	function get_anzahl()
	{
		// Gesamte Anzahl der Artikel aus der Datenbank raussuchen
		$select = sprintf("SELECT COUNT(DISTINCT reporeID) FROM %s, %s, %s, %s AS t4
				WHERE publish_yn = '1' AND allow_publish = '1' AND reporeID = article_id AND lan_metakey LIKE
				%s AND gruppeid_id = gruppenid AND userid = %s AND reporeID = t4.lan_repore_id AND t4.lang_id = %s ORDER BY reporeID DESC",
			$this->cms->papoo_repore,
			$this->cms->papoo_lookup_article,
			$this->cms->papoo_lookup_ug,
			$this->cms->papoo_language_article,
			"%" . $this->db->escape($this->checked->keyword) . "%",
			$this->user->userid,
			$this->cms->lang_id
		);

		// Anzahl der Ergebnisse
		return $this->db->get_var($select);
	}

	/**
	 * aller Eintr�ge von Artikeln in der Datenbank raussuchen
	 */
	function get_data()
	{
		// Gesamte Anzahl der Artikel aus der Datenbank raussuchen
		// Korrektur Teaserbild-Anzeige
		$select = sprintf("SELECT DISTINCT reporeID as reporeid, cattextid, header, lan_teaser_img_fertig, lan_teaser, cattextid, url_header, teaser_bild_lr
				FROM %s, %s, %s, %s AS t4
				WHERE publish_yn = '1' AND allow_publish = '1' AND reporeID = article_id AND lan_metakey LIKE
				%s AND gruppeid_id = gruppenid AND userid = %s AND reporeID = lan_repore_id AND t4.lang_id = %s ORDER BY header ASC %s",
			$this->cms->papoo_repore,
			$this->cms->papoo_lookup_article,
			$this->cms->papoo_lookup_ug,
			$this->cms->papoo_language_article,
			"%" . $this->db->escape($this->checked->keyword) . "%",
			$this->user->userid,
			$this->cms->lang_id,
			$this->weiter->sqllimit
		);

		$result=$this->db->get_results($select, ARRAY_A);

		//Bild urls korrigieren bei mod_rewrite
		if ($this->cms->mod_rewrite==2 || $this->cms->mod_surls==1) {
			//Artikel Klasse einbinden um die Pfade so�ter rauszubekommen
			global $artikel;
			$this->artikel = & $artikel;
			$i=0;
			//Eintr�ge durchgehen die vorliegen
			foreach ($result as $daten) {
				$this->artikel->m_url = array();

				$urldat_1 = "";
				//Verzeichnispfad rausholen
				$this->artikel->get_verzeichnis( $daten['cattextid'] );

				$this->m_url = array_reverse( $this->artikel->m_url );

				if ( !empty( $this->m_url ) ) {
					foreach ( $this->m_url as $urldat ) {
						$urldat_1 .= ( $urldat ) . "/";
					}
				}

				$header_url = $urldat_1 . ( ( ($daten['url_header'] )) ) . ".html";
				//Sprechende URL mit korrektem PFad angeben
				$result[$i]['url_header']=$header_url;
				//Bilde korrigieren
				$result[$i]['lan_teaser']=$this->diverse->do_pfadeanpassen($daten['lan_teaser']);
				$result[$i]['lan_teaser_img_fertig']=$this->diverse->do_pfadeanpassen($daten['lan_teaser_img_fertig']);
				$i++;
			}
		}
		// Anzahl der Ergebnisse
		return $result;
	}

	function do_newest()
	{
		//Settings rausholen
		$shop_settings = new shop_class_settings();
		$shop_settings->shop_get_system_settings_fuer_form();

		$this->content->template['shop_system_settings']=$shop_settings->shop_settings_daten;

		//Ans Template zur Ausgabe
		$this->content->template['newest_produkte']=$produkt_liste;

		$sql=sprintf("SELECT DISTINCT * FROM %s
								LEFT JOIN %s ON  produkte_lang_id=produkte_produkt_id
								LEFT JOIN %s ON kategorien_id=produkte_kategorie_id
				WHERE produkte_lang_lang_id='%d'
				AND produkte_lang_meta_beschreibung LIKE '%s'
				AND produkte_lang_aktiv='1'
				GROUP BY produkte_lang_id
									ORDER BY produkte_lang_id DESC LIMIT %d",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->cms->tbname['plugin_shop_lookup_kategorie_prod'],
			$this->cms->tbname['plugin_shop_kategorien'],
			$this->cms->lang_id,
			"%".$this->db->escape(($this->checked->keyword))."%",
			$this->content->template['shop_system_settings']['0']['einstellungen_lang_nzahl_neueste_en']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$i=0;
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				$result[$i]['produkte_lang_layout_id']=1;
				$i++;
			}
		}
		$shop_front=new shop_class_frontend();
		$shop_front->special_layout="OK";

		//Die Ausgabe Layouts erstellen
		if (isset($produkt_liste) && is_array($produkt_liste)) {
			foreach ($produkt_liste as $key=>$value) {
				$produkt_liste[$key]['daten']='<div class="single_produkt_liste">'.$value['daten']."</div>";
			}
		}
		//Ans Template zur Ausgabe
		$this->content->template['new_produkte_site']=$produkt_liste;
		$this->content->template['plugin_keyword_keyword']=$this->checked->keyword;
		$this->content->template['plugin_keyword_count']=count($produkt_liste);
	}
}

$keyword = new keywords();
