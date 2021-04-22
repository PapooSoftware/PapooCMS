<?php

/**
 * #####################################
 * # Papoo CMS                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >=4.3                 #
 * #####################################
 */

/**
 * Class linklisteplugin
 */
class linklisteplugin
{
	/** @var string  */
	var $news = "";

	/**
	 * linklisteplugin constructor.
	 */
	function __construct()
	{
		// Klassen globalisieren
		// cms Klasse einbinden
		global $cms, $db, $message, $user, $weiter, $content, $checked, $mail_it, $replace,
			   $db_praefix, $intern_stamm, $diverse, $dumpnrestorem, $intern_image, $intern_artikel;

		// und einbinden in die Klasse
		// Hier die Klassen als Referenzen
		$this->cms = &$cms;
		$this->intern_artikel = &$intern_artikel;
		$this->intern_image = &$intern_image;
		$this->db = &$db;
		$this->content = &$content;
		$this->message = &$message;
		$this->weiter = &$weiter;
		$this->searcher = &$searcher;
		$this->checked = &$checked;
		$this->mail_it = &$mail_it;
		$this->intern_stamm = &$intern_stamm;
		$this->replace = &$replace;
		$this->diverse = &$diverse;
		$this->dumpnrestore = &$dumpnrestore;
		$this->make_linklisteplugin();
		$this->make_lang_var();
		$this->content->template['plugin_message'] = "";
	}

	/**
	 * linkliste erstellen
	 */
	function make_linklisteplugin()
	{
		if ( defined("admin") ) {
			global $template;

			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2=basename($template2);

			if ( $template != "login.utf8.html" ) {
				$template2=("linkliste/templates/".$template2);
				switch ( $template2 ) {
					// Die Standardeinstellungen werden bearbeitet
				case "linkliste/templates/linklisteplugin.html" :
					$this->check_pref();
					break;
					// Einen Eintrag erstellen
				case "linkliste/templates/linklisteplugin_create.html" :
					$this->make_entry();
					break;
					// Eine Liste eingeben
				case "linkliste/templates/linklisteplugin_createlist.html" :
					$this->make_entry( "list" );
					break;
					// Einen EIntrag bearbeiten
				case "linkliste/templates/linklisteplugin_edit.html" :
					$this->change_entry();
					break;
					// Eine e Kategorie erstellen
				case "linkliste/templates/linklistecat_create.html" :
					$this->make_cat_entry();
					break;
					// Einee Kategorie bearbeiten
				case "linkliste/templates/linklistecat_edit.html" :
					$this->change_cat_entry();
					break;
					// Einen Dump erstellen oder einspielen
				case "linkliste/templates/linklisteplugin_dump.html" :
					$this->linkliste_dump();
					break;
					// Linkeintr�ge sortieren
				case "linkliste/templates/linklistesort.html" :
					$this->linkliste_sort();
					break;
					// Kategorien sortieren
				case "linkliste/templates/linklistecat_sort.html" :
					$this->linkliste_sort_cat();
					break;

				default :

					break;
				}
			}
		}
	}

	function get_partner()
	{
		if (!empty($this->cms->tbname['papoo_partnerverwaltung_daten'])) {
			$sql = sprintf( "SELECT * FROM %s ", $this->cms->tbname['papoo_partnerverwaltung_daten'] );
			$result = $this->db->get_results( $sql, ARRAY_A );
			// print_r($result);
			$this->content->template['result_real'] = $result;
		}
	}

	/**
	 * Sprachdateien einbinden, nach Sprache switchen
	 */
	function make_lang_var()
	{

	}

	/**
	 * linklisteplugin::get_link_linkliste()
	 * Erstellt den Link im Template zum ersten Men�punkt der die Linkliste behinhaltet
	 *
	 * @return void
	 */
	function get_link_linkliste()
	{
		//linkliste_link
		global $menu;
		foreach ($menu->data_front_complete as $data) {
			if (stristr($data['menulink'],"plugin:linkliste/templates/linkliste_do_front.html")) {
				if (empty($link)) {
					if ($this->cms->mod_surls==1) {
						$link="/".$data['menuname_url'];
					}
					else {
						$template=str_ireplace("plugin:","",$data['menulink']);
						$link=PAPOO_WEB_PFAD."/plugin.php?menuid=".$data['menuid']."&amp;template=".$template;
					}
				}
			}
		}
		$this->content->template['linkliste_link']=$link;
	}

	/**
	 * Wenn wir drau�en sind
	 */
	function post_papoo()
	{
		if (isset($this->content->template['module_aktiv']['linklisteplugin_mod1']) && $this->content->template['module_aktiv']['linklisteplugin_mod1'] == 1 ) {
			//Link zur Linkliste erzeugen
			$this->get_link_linkliste();
			//Daten rausholen
			$sql = sprintf( "SELECT * FROM %s, %s WHERE linkliste_id=linkliste_lang_id AND linkliste_lang_lang='%s' AND cat_linkliste_id='%s' ORDER BY linkliste_order_id ASC LIMIT 12",
				$this->cms->tbname['papoo_linkliste_daten'],
				$this->cms->tbname['papoo_linkliste_daten_lang'],
				$this->cms->lang_id,
				0
			);

			$result = $this->db->get_results( $sql, ARRAY_A );
			$i = 0;
			if ( !empty ( $result ) ) {
				foreach ( $result as $xres ) {
					$result[$i]['extern'] = "";
					$result[$i]['linkliste_descrip'] = strip_tags( ( @html_entity_decode( $result[$i]['linkliste_descrip'],"ENT_NOQUOTES","UTF-8" ) ) );
					if ( !empty ( $xres['linkliste_link'] ) ) {
						// externe Links mit target=blank kodieren
						if ( stristr(  $xres['linkliste_link'],"http" ) ) {
							$result[$i]['extern'] = "ok";
						}
					}
					if ( !empty ( $xres['linkliste_link_lang'] ) ) {
						// externe Links mit target=blank kodieren
						if ( stristr(  $xres['linkliste_link_lang'] ,"http") ) {
							$result[$i]['extern'] = "ok";
						}
					}
					// interne Links erzeugen
					if ( ( $xres['linkliste_link_art!=0'] ) ) {
						$sql_art = sprintf( "SELECT cattextid FROM %s WHERE reporeID='%s'", $this->cms->tbname['papoo_repore'], $this->db->escape( $result[$i]['linkliste_link_art'] ) );
						$art_menuid = $this->db->get_var( $sql_art );
						$art_reporeid = $result[$i]['linkliste_link_art'];
						if ( $this->cms->mod_rewrite == 2 ) {
							$result[$i]['linkliste_link'] = PAPOO_WEB_PFAD . "/index/menuid/" . $art_menuid . "/reporeid/" . $art_reporeid;
						}
						else {
							$result[$i]['linkliste_link'] = PAPOO_WEB_PFAD . "/index.php?menuid=" . $art_menuid . "&reporeid=" . $art_reporeid;
						}
					}
					$i++;
				}
			}
			$this->content->template['bestdata'] = $result;
		}
		// Es ist kein Eintrag ausgew�hlt, daher entweder die Links oder komplett anzeigen
		if ( !defined("admin") ) {
			global $template;
			if ( stristr(  $template,"linkliste_do_front.html" ) ) {
				// Anzahl rausholen
				$sql = sprintf( "SELECT COUNT(linkliste_id) FROM %s ", $this->cms->tbname['papoo_linkliste_daten'] );
				$gesamt = $this->db->get_var( $sql );
				$this->content->template['gesamt'] = $gesamt;
				// Sprachen ermitteln die eingestellt
				$lang_id = $this->cms->lang_id;
				$sql = sprintf( "SELECT *  FROM %s ", $this->cms->tbname['papoo_linkliste_pref'] );
				$result = $this->db->get_results( $sql );

				$show = $result['0']->linkliste_show;
				$langstring = $result['0']->linkliste_lang;
				$xml_link = $result['0']->linkliste_xml_link;

				$langarray = explode( ";", $langstring );
				if ( !in_array( $this->cms->lang_id, $langarray ) and $this->cms->lang_id > 2 ) {
					// Diese id besagt welche Sprache Standaradm��ig ausgew�hlt ist
					$lang_id = 2;
				}
				// komplette Liste anzeigen
				if ( $show == 1 ) {
					$this->cat_result = array ();
					$this->get_cat_list_sort( '0', '0', $lang_id );
					$result2 = $this->cat_result;

					$this->cat_result = array ();
					$i = 0;

					if ( !empty ( $result2 ) ) {
						foreach ( $result2 as $res ) {
							// Alle Links aus der Datenbank holen
							$sql = sprintf( "SELECT * FROM %s, %s WHERE linkliste_id=linkliste_lang_id AND linkliste_lang_lang='%s' AND cat_linkliste_id='%s' ORDER BY linkliste_order_id ", $this->cms->tbname['papoo_linkliste_daten'], $this->cms->tbname['papoo_linkliste_daten_lang'], $lang_id, $this->db->escape( $res['cat_id'] ) );
							$result2[$i]['glist'] = $this->db->get_results( $sql, ARRAY_A );
							$i++;
						}
					}
					// Alle Links aus der Datenbank holen
					$sql = sprintf( "SELECT * FROM %s, %s WHERE
					 linkliste_id=linkliste_lang_id
					 AND linkliste_lang_lang='%s'
					 AND cat_linkliste_id='%s'
					 ORDER BY linkliste_order_id ",
						$this->cms->tbname['papoo_linkliste_daten'],
						$this->cms->tbname['papoo_linkliste_daten_lang'],
						$lang_id,
						""
					);
					$result2[$i + 1]['glist'] = $this->db->get_results( $sql, ARRAY_A );
					$i++;

					$this->content->template['result'] = $result;
					$this->content->template['show_komplett'] = "komplett";

					$this->content->template['categories'] = $result2;
				}
				// Liste mit unterkategorien anzeigen
				if ( $show == 2 ) {
					//Check Kategorien / Menuid wg. SEO
					$this->check_redirect();

					if ( empty ( $this->checked->cat_level ) ) {
						$this->checked->cat_level = 0;
					}

					if ( empty ( $this->checked->cat_sub ) ) {
						$this->checked->cat_sub = 0;
					}

					if ( !empty ( $this->checked->cat_id ) ) {
						$this->checked->cat_level++;
					}

					$this->cat_result = array ();

					/**
					 * //UNterkategorien raussuchen
					 * $sql_sub=sprintf("SELECT COUNT(cat_id) FROM %s WHERE cat_sub='%s'",
					 * $this->cms->tbname['papoo_linkliste_cat'],
					 * $this->db->escape($res['cat_id'])
					 * );
					 */
					// $this->get_cat_list_sort_front($this->checked->cat_id,$this->checked->cat_level,$this->cms->lang_id);
					$this->get_cat_list_sort_front( 0, 2, $lang_id );

					$result2 = $this->cat_result;

					// Anzahl der Ergebnisse aus der Suche Eigenschaft �bergeben
					$surls=$this->cms->mod_surls;
					$this->cms->mod_surls=0;
					$this->weiter->make_limit(12);
					$sql = sprintf( "SELECT COUNT(linkliste_id) FROM %s WHERE cat_linkliste_id='%s'", $this->cms->tbname['papoo_linkliste_daten'],
						$this->db->escape( $this->checked->cat_id )
					);
					$result = $this->db->get_var( $sql );
					$this->weiter->result_anzahl = $result;
					// wenn weitere Ergebnisse angezeigt werden k�nnen
					$this->cms->mod_rewrite = 1;
					$this->weiter->weiter_link = "plugin.php?menuid=" . $this->checked->menuid . "&amp;template=linkliste/templates/linkliste_do_front.html&amp;cat_id=" . $this->checked->cat_id . "&amp;cat_sub=" . $this->checked->cat_sub . "&amp;cat_level=" . $this->checked->cat_level . "";
					// wenn es sie gibt, weitere Seiten anzeigen
					$what = "teaser";
					$this->weiter->do_weiter( $what );
					$this->cms->mod_surls=$surls;
					// Alle Links aus der Datenbank holen
					$sql = sprintf( "SELECT * FROM %s, %s WHERE linkliste_id=linkliste_lang_id AND linkliste_lang_lang='%s' AND cat_linkliste_id='%s' ORDER BY linkliste_order_id ASC %s", $this->cms->tbname['papoo_linkliste_daten'], $this->cms->tbname['papoo_linkliste_daten_lang'], $lang_id, $this->db->escape( $this->checked->cat_id ), $this->weiter->sqllimit );
					$result = $this->db->get_results( $sql, ARRAY_A );
					// url_partner
					// http://localhost/papoo_trunk/plugin.php?menuid=7&amp;template=partnerverwaltung/templates/partnerverwaltung_front.html
					$this->content->template['url_partner'] = "/plugin.php?menuid=29&amp;template=partnerverwaltung/templates/partnerverwaltung_front.html&amp;partner_id=";
					$i = 0;

					if ( !empty ( $result ) ) {
						foreach ( $result as $xres ) {
							if (!empty($this->cms->tbname['papoo_partnerverwaltung_daten'])) {
								$sql = sprintf( "SELECT * FROM %s WHERE partner_id='%s' LIMIT 1",
									$this->cms->tbname['papoo_partnerverwaltung_daten'],
									$this->db->escape($xres['linkliste_real'])
								);
								$part = $this->db->get_results( $sql, ARRAY_A );

								$result[$i]['partner_id'] = $part['0']['partner_id'];
								$result[$i]['partner_name'] = $part['0']['partner_name'];
							}

							$result[$i]['extern'] = "";

							$result[$i]['linkliste_descrip'] = ( html_entity_decode( $result[$i]['linkliste_descrip'],ENT_NOQUOTES,'UTF-8' ) );
							if ( !empty ( $xres['linkliste_link'] ) ) {
								// externe Links mit target=blank kodieren
								if ( stristr(  $xres['linkliste_link'] ,"http") ) {
									$result[$i]['extern'] = "ok";
								}
							}
							if ( !empty ( $xres['linkliste_link_lang'] ) ) {
								// externe Links mit target=blank kodieren
								if ( stristr(  $xres['linkliste_link_lang'] ,"http") ) {
									$result[$i]['extern'] = "ok";
								}
							}
							// interne Links erzeugen
							if ( ( $xres['linkliste_link_art!=0'] ) ) {
								$sql_art = sprintf( "SELECT cattextid FROM %s WHERE reporeID='%s'", $this->cms->tbname['papoo_repore'], $this->db->escape( $result[$i]['linkliste_link_art'] ) );
								$art_menuid = $this->db->get_var( $sql_art );
								$art_reporeid = $result[$i]['linkliste_link_art'];
								if ( $this->cms->mod_rewrite == 2 ) {
									$result[$i]['linkliste_link'] = PAPOO_WEB_PFAD . "/index/menuid/" . $art_menuid . "/reporeid/" . $art_reporeid;
								}
								else {
									$result[$i]['linkliste_link'] = PAPOO_WEB_PFAD . "/index.php?menuid=" . $art_menuid . "&reporeid=" . $art_reporeid;
								}
							}
							$i++;
						}
					}
					$this->content->template['result_link'] = $result;
					$this->content->template['categories'] = $result2;
					$this->content->template['show_link'] = "link";
					// 2. Brotkrumen f�r die Linkliste
					$sqls = sprintf( "SELECT cat_sub FROM %s WHERE cat_id='%s' ", $this->cms->tbname['papoo_linkliste_cat'], $this->db->escape( $this->checked->cat_id ) );

					$catid = $this->db->get_var( $sqls );
					// Aktuelle Kategorie
					$sqln = sprintf( "SELECT * FROM %s,%s WHERE cat_id='%s' AND cat_id=cat_id_id AND cat_lang_id='%s' ", $this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat'], $this->db->escape( $this->checked->cat_id ), $lang_id );

					$cat_d = $this->db->get_results( $sqln );
					$this->content->template['cataktuellername'] = $cat_d['0']->cat_name;
					// Metas zuweisen
					if ( !empty ( $this->checked->cat_id ) ) {
						$this->content->template['site_title'] = $cat_d['0']->cat_title . " - " . $this->content->template['site_title'];
						$this->content->template['keywords'] = $cat_d['0']->cat_keywords . " " . $this->content->template['keywords'];
						$this->content->template['description'] = $cat_d['0']->cat_description . " - " . $this->content->template['description'];
					}
					else {
						// Aktuelle Kategorie
						$sqln = sprintf( "SELECT * FROM %s,%s WHERE cat_id='%s' AND cat_id=cat_id_id AND cat_lang_id='%s' ", $this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat'], $this->db->escape( 1 ), $lang_id );
						$cat_d = $this->db->get_results( $sqln );

						$this->content->template['site_title'] = $cat_d['0']->cat_title . " - " . $this->content->template['site_title'];
						$this->content->template['keywords'] = $cat_d['0']->cat_keywords . " " . $this->content->template['keywords'];
						$this->content->template['description'] = $cat_d['0']->cat_description . " - " . $this->content->template['description'];
					}

					if ( $catid != 0 ) {
						$this->do_brot( $catid );
						$catarray = array ();

						$sqlc = sprintf( "SELECT * FROM %s,%s WHERE cat_id=cat_id_id AND cat_id='%s' AND cat_lang_id='%s'", $this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat'], $this->db->escape( $this->checked->cat_id ), $lang_id );
						$catname = $this->db->get_results( $sqlc, ARRAY_A );
						$catarray = array_merge_recursive( $catname, $catarray );
						foreach ( $this->catid as $id ) {
							$sqlc = sprintf( "SELECT * FROM %s,%s WHERE cat_id=cat_id_id AND cat_id='%s' AND cat_lang_id='%s'", $this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat'], $this->db->escape( $id['cat_id'] ), $lang_id );
							$catname = $this->db->get_results( $sqlc, ARRAY_A );
							$catarray = array_merge_recursive( $catname, $catarray );
						}
						$catname = $catarray;
					}
					else {
						$sqlc = sprintf( "SELECT * FROM %s,%s WHERE cat_id=cat_id_id AND cat_id='%s' AND cat_lang_id='%s'", $this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat'], $this->db->escape( $this->checked->cat_id ), $lang_id );
						$catname = $this->db->get_results( $sqlc, ARRAY_A );
					}
				}
				IfNotSetNull($catname);
				$this->content->template['catname'] = $catname;

				IfNotSetNull($this->checked->cat_id);
				IfNotSetNull($this->checked->cat_sub);
				IfNotSetNull($this->checked->cat_level);

				$this->content->template['menuid_aktuell'] = $this->checked->menuid;
				$this->content->template['template'] = $this->checked->template . "&amp;cat_id=" . $this->checked->cat_id . "&amp;cat_sub=" . $this->checked->cat_sub . "&amp;cat_level=" . $this->checked->cat_level . "";
				$this->content->template['template2'] = $this->checked->template;
				$this->content->template['linktext'] = "plugin.php";
				if ( $this->cms->mod_rewrite == 2 ) {
					$this->content->template['linktext'] = "plugin";
				}
			}
		}
	}

	private function check_redirect()
	{
		/*
		 * MIt der cat id checken ob das auch die korrekte Menüid ist.
		 *
		 * Gibt es einen Eintrag mit dieser KAtid - dann redirect auf die richtige Menüid wenn nötig
		 */
		$sql=sprintf("SELECT menuid_id FROM %s WHERE menulinklang LIKE '%s' ",
			DB_PRAEFIX."papoo_menu_language",
			"%linkliste_do_front.html&cat_id=".$this->db->escape($this->checked->cat_id)."%"
		)
		;
		$result=$this->db->get_var($sql);

		if (!empty($result) && ($result!=$this->checked->menuid)) {
			$uri=PAPOO_WEB_PFAD."/plugin.php?menuid=".$result."&template=linkliste/templates/linkliste_do_front.html&cat_id=".$this->checked->cat_id."&page=".$this->checked->page."&cat_sub=".$this->checked->cat_sub."&cat_level=".$this->checked->cat_level;
			//redirect
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".$uri);
			header("Connection: close");
			exit();die();
		}
	}

	/**
	 * Combines two arrays by inserting one into the other at a given position then returns the result
	 *
	 * @param $src
	 * @param $dest
	 * @param $pos
	 * @return array|bool
	 */
	function array_insert( $src, $dest, $pos )
	{
		if ( !is_array( $src ) || !is_array( $dest ) || $pos <= 0 ) {
			return false;
		}
		return array_merge( array_slice( $dest, 0, $pos ), $src, array_slice( $dest, $pos ) );
	}

	/**
	 * Bilder Liste erstellen
	 */
	function get_image_list()
	{
		$this->intern_artikel->get_images( $this->cms->lang_id );
	}

	/**
	 * Linklisten Brotkrumen anzeigen
	 *
	 * @param $cat_id
	 */
	function do_brot( $cat_id )
	{
		$sql = sprintf( "SELECT cat_sub FROM %s WHERE cat_id='%s' ", $this->cms->tbname['papoo_linkliste_cat'], $this->db->escape( $cat_id ) );
		$this->catid[$cat_id]['cat_sub'] = $ergebnis = $this->db->get_var( $sql );
		$this->catid[$cat_id]['cat_id'] = $cat_id;
		if ( $ergebnis > 0 ) {
			$this->do_brot( $ergebnis );
		}
	}
	/**
	 * Alle Artikel raussuchen
	 */
	function do_all_art()
	{
		// Alle Artikel rausfischen
		$sql = " SELECT DISTINCT(reporeID), cattextid, header ";
		$sql .= " FROM " . $this->cms->papoo_repore . "," . $this->db->escape( $this->cms->papoo_language_article ) . "";
		$sql .= " WHERE reporeID=lan_repore_id ";
		$sql .= " AND lang_id=" . $this->cms->lang_id . "";
		$sql .= "";
		$sql .= "";
		$sql .= "";
		$result_art = $this->db->get_results( $sql, ARRAY_A );
		$this->content->template['result_art'] = $result_art;
	}
	/**
	 * Die Kategorienliste erstellen
	 */

	function get_cat_list()
	{
		$sql = sprintf( "SELECT * FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='1' ", $this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat'] );
		$sql2 = sprintf( "SELECT * FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='1' ORDER BY cat_level,cat_order_id ", $this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat'] );
		$result = $this->db->get_results( $sql, ARRAY_A );
		$result2 = $this->db->get_results( $sql2, ARRAY_A );
		$this->content->template['result_cat'] = $result;
		$this->content->template['result_cat_back'] = $result;
		return $result2;
	}

	/**
	 * Die Standardeinstellungen f�r das Glossar werden hier eingestellt
	 *
	 * @return void oder checked1
	 */
	function check_pref()
	{
		// $this->diverse->get_sql("linkliste","pref");
		$lang = 1;
		// Die Einstellungen sollen ver�ndert werden
		if ( !empty ( $this->checked->submitglossar ) ) {
			// Sprache
			if ( empty ( $this->checked->lang ) ) {
				$lang = 1;
			}
			else {
				foreach ( $this->checked->lang as $rein ) {
					$lang .= ";" . $rein;
				}
			}
			IfNotSetNull($this->checked->linkliste_xml_link);
			IfNotSetNull($this->checked->linkliste_xml_yn);
			IfNotSetNull($this->checked->linkliste_liste);
			// Datenbank updaten linkliste_show
			$sql = sprintf( "UPDATE %s SET linkliste_xml_yn='%s', linkliste_xml_link='%s', linkliste_lang='%s', linkliste_liste='%s',linkliste_show='%s' WHERE linkliste_id='1'", $this->cms->tbname['papoo_linkliste_pref'], $this->db->escape( $this->checked->linkliste_xml_yn ), $this->db->escape( $this->checked->linkliste_xml_link ), $lang, $this->db->escape( $this->checked->linkliste_liste ), $this->db->escape( $this->checked->linkliste_show ) );
			$this->db->query( $sql );
		}
		$this->make_lang();
		// Daten aus der Datenbank holen und zuweisen
		$sql = sprintf( "SELECT * FROM %s", $this->cms->tbname['papoo_linkliste_pref'] );
		$result = $this->db->get_results( $sql, ARRAY_A );
		$this->content->template['xml_result'] = $result;
	}

	/**
	 * Spracheinstellungen ausgeben
	 */

	function make_lang()
	{
		// daher hier auch eine weitere Abfrage
		$resultlang = $this->db->get_results( "SELECT * FROM " . $this->cms->papoo_name_language . "  " );
		$resultlang2 = $this->db->get_var( "SELECT linkliste_lang FROM " . $this->cms->tbname['papoo_linkliste_pref'] . "  " );
		$lang = explode( ";", $resultlang2 );
		$this->content->template['language_dat'] = array ();
		// zuweisen welche Sprache ausgew�hlt sind
		foreach ( $resultlang as $rowlang ) {
			// chcken wenn Sprache gew�hlt
			if ( in_array( $rowlang->lang_id, $lang ) ) {
				$selected_more = 'nodecode:checked="checked"';
			}
			else {
				$selected_more = "";
			}
			// �berspringen, wenn Sprache als Standard ausgew�hlt ist
			if (!isset($row) || isset($row) && $row->lang_frontend != $rowlang->lang_short ) {
				array_push( $this->content->template['language_dat'], array ( 'language' => $rowlang->lang_long,
					'lang_id' => $rowlang->lang_id,
					'selected' => $selected_more,
				));
			}
		}
	}

	/**
	 * Eine Kategorie erstellen
	 */
	function make_cat_entry()
	{
		// Sprachen raussuchen
		$this->make_lang();
		$this->get_cat_list();

		if (isset($this->checked->submitentry) && $this->checked->submitentry ) {
			// Level rasubekommen
			$sql2 = sprintf( "SELECT cat_level FROM %s WHERE cat_id='%s'", $this->cms->tbname['papoo_linkliste_cat'], $this->db->escape( $this->checked->cat_sub ) );
			$cat_level = $this->db->get_var( $sql2 );
			if ( !empty ( $this->checked->cat_sub ) ) {
				$cat_level++;
			}
			else {
				$cat_level = "0";
			}
			// Gr��ten Order_id Eintrag rausbekommen in dieser Ebene
			$sql2 = sprintf( "SELECT MAX(cat_order_id) FROM %s WHERE cat_sub='%s'", $this->cms->tbname['papoo_linkliste_cat'], $this->db->escape( $this->checked->cat_sub ) );
			$cat_orderid = $this->db->get_var( $sql2 );
			$cat_orderid++;
			// Daten eintragen in Datenbank
			$sql = sprintf( "INSERT INTO %s SET cat_sub='%s', cat_level='%s', cat_order_id='%s'", $this->cms->tbname['papoo_linkliste_cat'], $this->db->escape( $this->checked->cat_sub ), $this->db->escape( $cat_level ), $cat_orderid );
			$this->db->query( $sql );
			$insertid = $this->db->insert_id;
			// Sprachdaten eintragen cat_name
			foreach ( $this->checked->cat_name as $key => $value ) {
				$sql = sprintf( "INSERT INTO %s SET cat_id_id='%s', cat_lang_id='%s', cat_name='%s', cat_title='%s', cat_description='%s', cat_keywords='%s'", $this->cms->tbname['papoo_linkliste_lang_cat'], $insertid, $this->db->escape( $key ), $this->db->escape( $value ), $this->db->escape( $this->checked->cat_title[$key] ), $this->db->escape( $this->checked->cat_description[$key] ), $this->db->escape( $this->checked->cat_keywords[$key] ) );
				$this->db->query( $sql );
			}

			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&fertig=drin";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}
		else {
			$this->content->template['neuereintrag'] = "ok";
		}

		if (isset($this->checked->fertig) && $this->checked->fertig == "drin" ) {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}
	}

	/**
	 * Kategorie �ndern
	 */
	function change_cat_entry()
	{
		// Sprachen raussuchen
		$this->make_lang();
		$this->get_cat_list();
		// Es soll eingetragen werden
		if (isset($this->checked->submitentry) && $this->checked->submitentry ) {
			// Level rasubekommen
			$sql2 = sprintf( "SELECT cat_level FROM %s WHERE cat_id='%s'", $this->cms->tbname['papoo_linkliste_cat'], $this->db->escape( $this->checked->cat_sub ) );
			$cat_level = $this->db->get_var( $sql2 );
			if ( !empty ( $this->checked->cat_sub ) ) {
				$cat_level++;
			}
			else {
				$cat_level = "0";
			} ;

			$sql = sprintf( "UPDATE %s SET  cat_sub='%s', cat_level='%s' WHERE cat_id='%s'", $this->cms->tbname['papoo_linkliste_cat'], $this->db->escape( $this->checked->cat_sub ), $this->db->escape( $cat_level ), $this->db->escape( $this->checked->cat_id ) );
			$this->db->query( $sql );
			$insertid = $this->db->escape( $this->checked->cat_id );
			// Alle Sprachen linkliste_descrip
			$sql = sprintf( "DELETE FROM %s WHERE cat_id_id='%s'", $this->cms->tbname['papoo_linkliste_lang_cat'], $insertid );
			$this->db->query( $sql );
			// Sprachdaten eintragen cat_name
			foreach ( $this->checked->cat_name as $key => $value ) {
				$sql = sprintf( "INSERT INTO %s SET cat_name='%s', cat_id_id='%s', cat_lang_id='%s', cat_title='%s', cat_description='%s', cat_keywords='%s' ", $this->cms->tbname['papoo_linkliste_lang_cat'], $this->db->escape( $value ), $insertid, $this->db->escape( $key ), $this->db->escape( $this->checked->cat_title[$key] ), $this->db->escape( $this->checked->cat_description[$key] ), $this->db->escape( $this->checked->cat_keywords[$key] ) );
				$this->db->query( $sql );
			}

			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&fertig=drin";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}

		if ( !empty ( $this->checked->glossarid ) ) {
			// Nach id aus der Datenbank holen
			$sql = sprintf( "SELECT * FROM %s WHERE cat_id='%s'", $this->cms->tbname['papoo_linkliste_cat'], $this->db->escape( $this->checked->glossarid ) );

			$result = $this->db->get_results( $sql );
			if ( !empty ( $result ) ) {
				foreach ( $result as $glos ) {
					$this->content->template['cat_id'] = $glos->cat_id;
					$this->content->template['cat_sub'] = $glos->cat_sub;
					$this->content->template['edit'] = "ok";
					$this->content->template['altereintrag'] = "ok";
				}
				// Sprachdaten raussuchen
				$sql = sprintf( "SELECT * FROM %s WHERE cat_id_id='%s'", $this->cms->tbname['papoo_linkliste_lang_cat'], $this->db->escape( $this->checked->glossarid ) );
				$resultx = $this->db->get_results( $sql );
				if ( !empty ( $resultx ) ) {
					foreach ( $resultx as $lang ) {
						$wort[$lang->cat_lang_id] = $lang->cat_name;
						$title[$lang->cat_lang_id] = $lang->cat_title;
						$descrip[$lang->cat_lang_id] = $lang->cat_description;
						$mkey[$lang->cat_lang_id] = $lang->cat_keywords;
					}
					$this->content->template['cat_name'] = $wort;
					$this->content->template['cat_title'] = $title;
					$this->content->template['cat_description'] = $descrip;
					$this->content->template['cat_keywords'] = $mkey;
				}
			}
		}
		// alle anzeigen
		else {
			// Daten rausholen und als Liste anbieten
			$this->content->template['list'] = "ok";
			// Daten rausholen
			$sql = sprintf( "SELECT * FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='1' ORDER BY cat_name ASC", $this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat'] );
			$result = $this->db->get_results( $sql, ARRAY_A );
			// Daten f�r das Template zuweisen
			$this->content->template['list_dat'] = $result;
			$this->content->template['ll_link'] = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&amp;template=" . $this->checked->template . "&amp;glossarid="; ;
		}
		if (isset($this->checked->fertig) && $this->checked->fertig == "drin" ) {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}
		// Anzeigen das Eintrag gel�scht wurde
		if (isset($this->checked->fertig) && $this->checked->fertig == "del" ) {
			$this->content->template['deleted'] = "ok";
		}
		// Soll  gel�scht werden
		if ( !empty ( $this->checked->submitdelecht ) ) {
			// Eintrag nach id l�schen und neu laden
			$sql = sprintf( "DELETE FROM %s WHERE cat_id='%s'", $this->cms->tbname['papoo_linkliste_cat'], $this->db->escape( $this->checked->cat_id ) );
			$this->db->query( $sql );
			$insertid = $this->db->escape( $this->checked->cat_id );
			// Alle Sprachen linkliste_descrip
			$sql = sprintf( "DELETE FROM %s WHERE cat_id_id='%s'", $this->cms->tbname['papoo_linkliste_lang_cat'], $insertid );
			$this->db->query( $sql );
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&fertig=del";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}
		IfNotSetNull($this->checked->glossarname);
		IfNotSetNull($this->checked->glossarid);
		// Soll wirklich gel�scht werden?
		if ( !empty ( $this->checked->submitdel ) ) {
			$this->content->template['glossarname'] = $this->checked->glossarname;
			$this->content->template['glossarid'] = $this->checked->glossarid;
			$this->content->template['fragedel'] = "ok";
			$this->content->template['edit'] = "";
		}
	}

	/**
	 * Ein neuer Eintrag wird erstellt und die Option zum Datenbank durchloopen angeboten
	 *
	 * @param string $modus
	 * @return void
	 */

	function make_entry( $modus = "" )
	{
		if ( $modus == "list" ) {
			$this->content->template['neueliste'] = "1";
		}
		else {
			$this->content->template['neueliste'] = "";
		}
		// Sprachen raussuchen
		$this->make_lang();
		$this->get_cat_list();
		$this->do_all_art();
		$this->get_image_list();
		$this->get_partner();
		$this->content->template['neue'] = "1";

		if (isset($this->checked->submitliste) && $this->checked->submitliste ) {
			$voll = explode( "\n", $this->checked->voll_liste );
			foreach ( $voll as $link ) {
				$link = str_replace ( chr( 10 ), "", $link );
				$link = str_replace ( chr( 13 ), "", $link );
				$sql = sprintf( "SELECT linkliste_link FROM %s WHERE linkliste_link='%s'",
					$this->cms->tbname['papoo_linkliste_daten'],
					$this->db->escape( $link ) );
				$exist = $this->db->get_var( $sql );
				if ( empty( $exist ) and !empty( $link ) ) {
					// orderid rausbekommen
					$nu = "";
					$sql = sprintf( "SELECT MAX(linkliste_order_id) FROM %s WHERE cat_linkliste_id='%s'", $this->cms->tbname['papoo_linkliste_daten'], $this->db->escape( 1 ) );
					$max = $this->db->get_var( $sql ) + 1;
					$sql = sprintf( "INSERT INTO %s SET  linkliste_link='%s', cat_linkliste_id='%s', linkliste_order_id='%s', linkliste_link_art='%s'", $this->cms->tbname['papoo_linkliste_daten'], $this->db->escape( $link ), $this->db->escape( $nu ), $this->db->escape( $max ), $this->db->escape( $this->checked->linkliste_link_art ) );
					$this->db->query( $sql );
					$insertid = $this->db->insert_id;

					$sql = sprintf( "INSERT INTO %s SET linkliste_lang_id='%s', linkliste_lang_lang='%s', linkliste_Wort='%s', linkliste_descrip='%s', linkliste_link_lang='%s'",
						$this->cms->tbname['papoo_linkliste_daten_lang'],
						$this->db->escape( $insertid ),
						1,
						$this->db->escape( $link ),
						$this->db->escape( $this->checked->linkliste_descrip[$key] ),
						$this->db->escape( "" )
					);
					$this->db->query( $sql );
				}
			}
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&fertig=drin";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}

		if (isset($this->checked->submitentry) && $this->checked->submitentry) {
			// Bilddaten hochladen
			if ( !empty ( $_FILES['strFile'] ) ) {
				if ( empty ( $this->checked->paket_logo2 ) ) {
					$this->intern_image->upload_picture();
					$this->checked->image_name = $this->content->template['image_name'];
					$this->checked->image_breite = $this->content->template['image_breite'];
					$this->checked->image_hoehe = $this->content->template['image_hoehe'];
					$this->checked->gruppe = $this->content->template['image_gruppe'];
					$this->checked->texte[1]['lang_id'] = 1;
					$this->checked->texte[1]['alt'] = $this->checked->linkliste_lang_header['1'];
					$this->checked->texte[1]['title'] = '';
					$this->checked->texte[1]['long_desc'] = '';
					$this->checked->image_dir = "0";

					if ( !empty ( $this->checked->image_name ) ) {
						$this->intern_image->upload_save( 'no' );
					}
				}
			}
			else {
				$this->checked->image_name = $this->checked->paket_logo2;
			}

			if ( empty( $this->checked->copy ) ) {
				// checken ob schon drin
				foreach ( $this->checked->linkliste_Wort as $key => $value ) {
					$sql = sprintf( "SELECT linkliste_link_lang FROM %s WHERE linkliste_link_lang='%s'",
						$this->cms->tbname['papoo_linkliste_daten_lang'],
						$this->db->escape( $this->checked->linkliste_link_lang[$key] ) );
					$exist = $this->db->get_var( $sql );
				}
			}

			if ( empty( $exist ) ) {
				// orderid rausbekommen
				$sql = sprintf( "SELECT MAX(linkliste_order_id) FROM %s WHERE cat_linkliste_id='%s'", $this->cms->tbname['papoo_linkliste_daten'], $this->db->escape( $this->checked->cat_linkliste_id ) );
				$max = $this->db->get_var( $sql ) + 1;
				// Daten eintragen in Datenbank
				$sql = sprintf( "INSERT INTO %s SET
				linkliste_link='%s',
				paket_logo='%s',
				cat_linkliste_id='%s',
				linkliste_order_id='%s',
				linkliste_link_art='%s',
				 linkliste_real='%s'",
					$this->cms->tbname['papoo_linkliste_daten'],
					$this->db->escape( $this->checked->linkliste_link ),
					$this->db->escape( $this->checked->image_name ),
					$this->db->escape( $this->checked->cat_linkliste_id ),
					$this->db->escape( $max ),
					$this->db->escape( $this->checked->linkliste_link_art ),
					$this->db->escape( $this->checked->linkliste_real )
				);
				$this->db->query( $sql );
				$insertid = $this->db->insert_id;
				// Alle Sprachen linkliste_descrip
				foreach ( $this->checked->linkliste_Wort as $key => $value ) {
					$sql = sprintf( "INSERT INTO %s SET linkliste_lang_id='%s', linkliste_lang_lang='%s', linkliste_Wort='%s', linkliste_descrip='%s', linkliste_link_lang='%s', linkliste_lang_header='%s'",
						$this->cms->tbname['papoo_linkliste_daten_lang'],
						$this->db->escape( $insertid ),
						$this->db->escape( $key ),
						$this->db->escape( $value ),
						$this->db->escape( $this->checked->linkliste_descrip[$key] ),
						$this->db->escape( $this->checked->linkliste_link_lang[$key] ),
						$this->db->escape( $this->checked->linkliste_lang_header[$key] )
					);
					$this->db->query( $sql );
				}
				$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&fertig=drin";
				if ( $_SESSION['debug_stopallredirect'] ) {
					echo '<a href="' . $location_url . '">Weiter</a>';
				}
				else {
					header( "Location: $location_url" );
				}
				exit;
			}
			else {
				$this->content->template['exist'] = $exist;
			}
		}
		else {
			$this->content->template['neuereintrag'] = "ok";
		}

		if (isset($this->checked->fertig) && $this->checked->fertig == "drin" ) {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}
	}

	/**
	 * Ein Eintrag wird rausgeholt und bearbeitet und wieder eingetragen
	 */
	function change_entry()
	{
		// Sprachen raussuchen
		$this->make_lang();
		$this->get_cat_list();
		$this->get_partner();
		// print_r($this->content->template['result_cat']);
		$this->do_all_art();
		$this->get_image_list();
		// Es soll eingetragen werden
		if ( !empty( $this->checked->copy ) ) {
			// echo "WEWE";
			$this->checked->submitentry = 1;
			$this->make_entry();
		}
		if (isset($this->checked->submitentry) && $this->checked->submitentry ) {
			// ####
			// Bilddaten hochladen
			if ( !empty ( $_FILES['strFile'] ) ) {
				if ( empty ( $this->checked->paket_logo2 ) ) {
					$this->intern_image->upload_picture();
					$this->checked->image_name = $this->content->template['image_name'];
					$this->checked->image_breite = $this->content->template['image_breite'];
					$this->checked->image_hoehe = $this->content->template['image_hoehe'];
					$this->checked->gruppe = $this->content->template['image_gruppe'];
					$this->checked->texte[1]['lang_id'] = 1;
					$this->checked->texte[1]['alt'] = $this->checked->linkliste_lang_header['1'];
					$this->checked->texte[1]['title'] = '';
					$this->checked->texte[1]['long_desc'] = '';
					$this->checked->image_dir = "0";

					if ( !empty ( $this->checked->image_name ) ) {
						$this->intern_image->upload_save( 'no' );
					}
				}
			}
			if ( !empty ( $this->checked->paket_logo2 ) ) {
				$this->checked->image_name = $this->checked->paket_logo2;
			}

			if ( empty ( $this->checked->image_name ) ) {
				$sql = sprintf( "SELECT paket_logo FROM %s WHERE linkliste_id='%s'", $this->cms->tbname['papoo_linkliste_daten'], $this->db->escape( $this->checked->linkliste_id ) );
				$this->checked->image_name = $this->db->get_var( $sql );
			}
			$sql = sprintf( "UPDATE %s SET
			linkliste_real='%s',
			linkliste_link='%s',
			paket_logo='%s',
			linkliste_link_art='%s',
			cat_linkliste_id='%s'
			WHERE linkliste_id='%s'",
				$this->cms->tbname['papoo_linkliste_daten'],
				$this->db->escape( $this->checked->linkliste_real ),
				$this->db->escape( $this->checked->linkliste_link ),
				$this->db->escape( $this->checked->image_name ),
				$this->db->escape( $this->checked->linkliste_link_art ),
				$this->db->escape( $this->checked->cat_linkliste_id ),
				$this->db->escape( $this->checked->linkliste_id ) );
			$this->db->query( $sql );
			$insertid = $this->db->escape( $this->checked->linkliste_id );
			// Alle Sprachen linkliste_descrip
			$sql = sprintf( "DELETE FROM %s WHERE linkliste_lang_id='%s'", $this->cms->tbname['papoo_linkliste_daten_lang'], $insertid );
			$this->db->query( $sql );

			foreach ( $this->checked->linkliste_Wort as $key => $value ) {
				$sql = sprintf( "INSERT INTO %s SET linkliste_lang_id='%s', linkliste_lang_lang='%s', linkliste_Wort='%s', linkliste_descrip='%s', linkliste_link_lang='%s', linkliste_lang_header='%s'", $this->cms->tbname['papoo_linkliste_daten_lang'], $this->db->escape( $insertid ), $this->db->escape( $key ), $this->db->escape( $value ), $this->db->escape( $this->checked->linkliste_descrip[$key] ),

					$this->db->escape( $this->checked->linkliste_link_lang[$key] ),
					$this->db->escape( $this->checked->linkliste_lang_header[$key] )
				);
				$this->db->query( $sql );
			}

			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&fertig=drin";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['linkliste']['weiter'] . '</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}

		if ( !empty ( $this->checked->glossarid ) ) {
			// Nach id aus der Datenbank holen
			$sql = sprintf( "SELECT * FROM %s WHERE linkliste_id='%s'", $this->cms->tbname['papoo_linkliste_daten'], $this->db->escape( $this->checked->glossarid ) );

			$result = $this->db->get_results( $sql );
			if ( !empty ( $result ) ) {
				foreach ( $result as $glos ) {
					IfNotSetNull($glos->linkliste_Wort);
					IfNotSetNull($glos->linkliste_descrip);
					IfNotSetNull($glos->linkliste_id);
					IfNotSetNull($glos->paket_logo);
					IfNotSetNull($glos->linkliste_link);
					IfNotSetNull($glos->linkliste_link_art);
					IfNotSetNull($glos->linkliste_real);
					IfNotSetNull($glos->cat_linkliste_id);

					$this->content->template['linkliste_Wort'] = $glos->linkliste_Wort;
					$this->content->template['linkliste_descrip'] = "nobr:" . $glos->linkliste_descrip;
					$this->content->template['edit'] = "ok";
					$this->content->template['altereintrag'] = "ok";
					$this->content->template['linkliste_id'] = $glos->linkliste_id;
					$this->content->template['paket_logo'] = $glos->paket_logo;
					$this->content->template['linkliste_link'] = $glos->linkliste_link;
					$this->content->template['linkliste_link_art'] = $glos->linkliste_link_art;
					$this->content->template['linkliste_real'] = $glos->linkliste_real;
					$this->content->template['cat_linkliste_id'] = $glos->cat_linkliste_id;
				}
				// Sprachdaten raussuchen
				$sql = sprintf( "SELECT * FROM %s WHERE linkliste_lang_id='%s'", $this->cms->tbname['papoo_linkliste_daten_lang'], $this->db->escape( $this->checked->glossarid ) );
				$resultx = $this->db->get_results( $sql );

				foreach ( $resultx as $lang ) {
					$wort[$lang->linkliste_lang_lang] = $lang->linkliste_Wort;
					$wort2[$lang->linkliste_lang_lang] = $lang->linkliste_lang_header;
					$des[$lang->linkliste_lang_lang] = "nobr:" . $lang->linkliste_descrip;
					$link[$lang->linkliste_lang_lang] = "nobr:" . $lang->linkliste_link_lang;
					if ( empty( $lang->linkliste_link_lang ) ) {
						$link[$lang->linkliste_lang_lang] = $glos->linkliste_link;
					}
				}
				$this->content->template['linkliste_Wort'] = $wort;
				$this->content->template['linkliste_descrip'] = $des;
				$this->content->template['linkliste_link_lang'] = $link;
				$this->content->template['linkliste_lang_header'] = $wort2;
			}
		}
		else {
			// Daten rausholen und als Liste anbieten
			$this->content->template['list'] = "ok";
			// if (empty($this->checked->order)){
			// $this->checked->order=$_SESSION['ordercat'];
			// }
			// if (empty($this->checked->orcat_linkliste_idder)){
			// $this->checked->order=$_SESSION['ordercatid'];
			// }
			if ( empty( $this->checked->order ) or $this->checked->order == "men" ) {
				$order = '=\'0\'';
			}
			else {
				$order = '=\'' . $this->db->escape( $this->checked->cat_linkliste_id ) . '\'';
				$_SESSION['ordercatid'] = $this->checked->cat_linkliste_id;
			}
			if (isset($this->checked->order) && $this->checked->order == "all" ) {
				$order = ' LIKE \'%\'';
			}
			IfNotSetNull($this->checked->order);
			IfNotSetNull($this->checked->cat_linkliste_id);
			$_SESSION['ordercat'] = $this->checked->order;
			$this->content->template['catidx'] = $this->checked->cat_linkliste_id;
			// Daten rausholen
			$sql = sprintf( "SELECT * FROM %s, %s WHERE linkliste_id=linkliste_lang_id AND linkliste_lang_lang='1' AND cat_linkliste_id%s ORDER BY linkliste_Wort ASC", $this->cms->tbname['papoo_linkliste_daten'], $this->cms->tbname['papoo_linkliste_daten_lang'],

				( $order )
			);
			$result = $this->db->get_results( $sql, ARRAY_A );

			// Daten f�r das Template zuweisen
			$this->content->template['list_dat'] = $result;
			$this->content->template['ll_link'] = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&amp;template=" . $this->checked->template . "&amp;glossarid="; ;
		}
		if (isset($this->checked->fertig) && $this->checked->fertig == "drin" ) {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}
		// Anzeigen das Eintrag gel�scht wurde
		if (isset($this->checked->fertig) && $this->checked->fertig == "del" ) {
			$this->content->template['deleted'] = "ok";
		}
		// Soll  gel�scht werden
		if ( !empty ( $this->checked->submitdelecht ) ) {
			// Eintrag nach id l�schen und neu laden
			$sql = sprintf( "DELETE FROM %s WHERE linkliste_id='%s'", $this->cms->tbname['papoo_linkliste_daten'], $this->db->escape( $this->checked->linkliste_id ) );
			$this->db->query( $sql );
			$insertid = $this->db->escape( $this->checked->linkliste_id );
			// Alle Sprachen linkliste_descrip
			$sql = sprintf( "DELETE FROM %s WHERE linkliste_lang_id='%s'", $this->cms->tbname['papoo_linkliste_daten_lang'], $this->db->escape( $insertid ) );
			$this->db->query( $sql );
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&fertig=del";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['linkliste']['weiter'] . '</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}
		// Soll wirklich gel�scht werden?
		if ( !empty ( $this->checked->submitdel ) ) {
			$this->content->template['glossarname'] = $this->checked->glossarname;
			$this->content->template['glossarid'] = $this->checked->glossarid;
			$this->content->template['fragedel'] = "ok";
			$this->content->template['edit'] = "";
		}
	}

	/**
	 * Die Glossardaten dumpen und wieder zur�ckspielen
	 */
	function linkliste_dump()
	{
		$this->diverse->extern_dump( "linkliste" );
	}

	/**
	 * Glosarbeschreibung durchgehen um Erw�hnungen untereinander zu verlinken
	 *
	 * @param $daten
	 * @param $wort
	 * @return string
	 */
	function selfcheck( $daten, $wort )
	{
		// Daten rausholen
		$sql = sprintf( "SELECT * FROM %s ", $this->cms->tbname['papoo_glossar_daten'] );
		$result = $this->db->get_results( $sql );

		$test = array ();
		$drin = array ();
		$neutext = ( $daten );
		// Ersetzung durchf�hren
		foreach ( $result as $var ) {
			/**
			 * $link= "<a href=\"".PAPOO_WEB_PFAD.'/plugin.php?menuid=1&amp;glossar='.$var->glossar_id.'&amp;template=glossar/templates/glossarfront.html#'.$var->glossar_id."\">".$var->glossar_Wort."</a>";
			 * $neutext=str_ireplace($var->glossar_Wort,$link,$daten);
			 */

			if ( stristr(  $daten ,$var->glossar_Wort) ) {
				if ( $var->glossar_Wort != $wort ) {
					$drin['link_body'] = PAPOO_WEB_PFAD . '/plugin.php?menuid=1&amp;glossar=' . $var->glossar_id . '&amp;template=glossar/templates/glossarfront.html#' . $var->glossar_id;
					$drin['link_name'] = trim( $var->glossar_Wort );
					$test['0'] = $drin;
					$this->replace->glossar = "ok";
					$this->replace->links = $test;
					$neutext = $this->replace->name_to_link( $daten );
				}
			}
		}
		return $neutext;
	}

	/**
	 * Alle Linkeintr�ge raussuchen
	 *
	 * @param string $catid
	 * @param string $order
	 * @return array|void
	 */
	function get_link_list( $catid = "", $order="" )
	{
		$order=" ORDER BY linkliste_order_id ";
		if (empty($order)) {
			$order=" ORDER BY RAND() ";
		}
		// Alle Eintr�ge zu dieser Kategorie rausholen
		$sql = sprintf( "SELECT * FROM %s, %s WHERE linkliste_id=linkliste_lang_id AND linkliste_lang_lang='%s' AND cat_linkliste_id='%s' %s", $this->cms->tbname['papoo_linkliste_daten'], $this->cms->tbname['papoo_linkliste_daten_lang'], 1, $this->db->escape( $catid ),$order );
		$result2 = $this->db->get_results( $sql, ARRAY_A );
		$result3 = $this->db->get_results( $sql );

		$this->content->template['linklist_data'] = $result2;
		return $result3;
	}

	/**
	 * Linkliste sortieren
	 */
	function linkliste_sort()
	{
		// Liste der Kategorien raussuchen und �bergeben
		$this->get_cat_list();
		// catid aus Formular �bergeben cat_linkliste_id
		IfNotSetNull($this->checked->cat_linkliste_id);
		$this->content->template['cat_id'] = $this->checked->cat_linkliste_id;
		// Sortierung
		if ( !empty ( $this->checked->formartikelid ) && empty ( $this->checked->enterorder ) ) {
			$this->order_change( $this->checked->formartikelid, $this->checked->cat_linkliste_id );
		}
		if ( !empty ( $this->checked->enterorder ) ) {
			$sql = sprintf( "UPDATE %s SET linkliste_order_id='%d' WHERE linkliste_id='%d'", $this->cms->tbname['papoo_linkliste_daten'], $this->db->escape( $this->checked->new_order_id ), $this->db->escape( $this->checked->formartikelid ) );
			$this->db->query( $sql );
			$this->reorder_linklist();
		}
		// Linkliste holen
		$this->get_link_list( $this->checked->cat_linkliste_id );
	}

	/**
	 * Kategorien sortieren
	 */
	function linkliste_sort_cat()
	{
		$this->cat_result = array ();
		$this->level = 0;

		if ( !empty ( $this->checked->menuorder_menuid ) ) {
			$this->order_change_cat( $this->checked->formartikelid );
		}
		// Liste der Kategorien raussuchen und �bergeben
		$this->get_cat_list_sort();
		$this->content->template['result_cat'] = $this->cat_result;
	}

	/**
	 * Die Kategorienliste erstellen
	 *
	 * @param string $catid
	 * @param string $level
	 * @param string $langid
	 * @return void $this->cat_result wird gef�llt mit sortierten Kategorien
	 */
	function get_cat_list_sort( $catid = "0", $level = "", $langid = "1" )
	{
		$sql = sprintf( "SELECT * FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='%s' AND cat_sub='%s' ORDER BY cat_order_id", $this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat'], $this->db->escape( $langid ), $this->db->escape( $catid ) );

		$sqlcount = sprintf( "SELECT COUNT(cat_id) FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='%s' AND cat_sub='%s'", $this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat'], $this->db->escape( $langid ), $this->db->escape( $catid ) );
		$resultcount = $this->db->get_var( $sqlcount );

		$result2 = $this->db->get_results( $sql, ARRAY_A );
		$i = 1;
		if ( !empty ( $result2 ) ) {
			foreach ( $result2 as $sub ) {
				$letzter = "";

				if ( $resultcount == $i ) {
					$letzter = "letzter";
				}
				$i++;
				// Alter gr��er, dann runter
				if (isset($this->leve) && $this->level > $sub['cat_level'] ) {
					$mal = $this->level - $sub['cat_level'];
					$shift_anfang = "";
					for ( $i = 0; $i < $mal; $i++ ) {
						$shift_anfang .= "</ul><li>";
					}
					$shift_ende = "</li>";
				}
				// Alter kleiner, dann rauf
				if (isset($this->leve) &&  $this->level < $sub['cat_level'] ) {
					$shift_anfang = "<ul><li>";
					$shift_ende = "</li>";
				}
				// gleich dann nix
				if (isset($this->leve) &&  $this->level == $sub['cat_level'] ) {
					$shift_anfang = "<li>";
					$shift_ende = "</li>";
				}
				IfNotSetNull($shift_anfang);
				IfNotSetNull($shift_ende);

				array_push( $this->cat_result, array (
					'letzter' => $letzter,
					'shift_anfang' => $shift_anfang,
					'shift_ende' => $shift_ende,
					'cat_order_id' => $sub['cat_order_id'],
					'cat_sub' => $sub['cat_sub'],
					'cat_level' => $sub['cat_level'],
					'cat_id' => $sub['cat_id'],
					'cat_name' => $sub['cat_name']
				) );
				$this->level = $sub['cat_level'];
				$this->get_cat_list_sort( $sub['cat_id'], $sub['cat_level'] );
			}
		}
		// return $result2;
		// $this->content->template['result_cat']=$result;
	}

	/**
	 * Die Kategorienliste erstellen
	 *
	 * @param string $catid
	 * @param string $level
	 * @param string $langid
	 * @return void $this->cat_result wird gef�llt mit sortierten Kategorien
	 */
	function get_cat_list_sort_front( $catid = "0", $level = "", $langid = "1" )
	{
		$sql = sprintf( "SELECT * FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='%s' AND cat_sub='%s' ORDER BY cat_order_id", $this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat'], $this->db->escape( $langid ), $this->db->escape( $catid ) );

		$sqlcount = sprintf( "SELECT COUNT(cat_id) FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='%s' AND cat_sub='%s'", $this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat'], $this->db->escape( $langid ), $this->db->escape( $catid ) );
		$resultcount = $this->db->get_var( $sqlcount );
		$levelurl = $level;

		$result2 = $this->db->get_results( $sql, ARRAY_A );

		$i = 1;
		if ( !empty ( $result2 ) ) {
			foreach ( $result2 as $sub ) {
				// Anzahl der Links rausbekommen
				$sql_link = sprintf( "SELECT COUNT(linkliste_id) FROM %s WHERE cat_linkliste_id='%s'", $this->cms->tbname['papoo_linkliste_daten'], $this->db->escape( $sub['cat_id'] ) );
				$rescount = $this->db->get_var( $sql_link );
				// Wenn wir im Level zu tief sind �berspringen
				if ( $sub['cat_level'] > $level + 1 ) {
					continue;
				}
				$letzter = "";

				if ( $resultcount == $i ) {
					$letzter = "letzter";
				}
				$i++;
				// Alter gr��er, dann runter
				if ( $this->level > $sub['cat_level'] ) {
					$mal = $this->level - $sub['cat_level'];
					$shift_anfang = "";
					for ( $i = 0; $i < $mal; $i++ ) {
						$shift_anfang .= "</ul><li>";
					}
					$shift_ende = "</li>";
				}
				// Alter kleiner, dann rauf
				if ( $this->level < $sub['cat_level'] ) {
					$shift_anfang = "<ul><li>";
					$shift_ende = "</li>";
				}
				// gleich dann nix
				if ( $this->level == $sub['cat_level'] ) {
					$shift_anfang = "<li>";
					$shift_ende = "</li>";
				}
				array_push( $this->cat_result, array (
					'letzter' => $letzter,
					'shift_anfang' => $shift_anfang,
					'shift_ende' => $shift_ende,
					'cat_order_id' => $sub['cat_order_id'],
					'cat_sub' => $sub['cat_sub'],
					'cat_level' => $sub['cat_level'],
					'cat_id' => $sub['cat_id'],
					'cat_name' => $sub['cat_name'],
					'link_count' => $rescount
				) );
				$this->level = $sub['cat_level'];
				// Wenn aktiv
				if ( $sub['cat_id'] == $this->checked->cat_id ) {
					$this->get_cat_list_sort_front( $sub['cat_id'], $sub['cat_level'] );
				}
			}
		}
		// return $result2;
		// $this->content->template['result_cat']=$result;
	}

	/**
	 * Hiermit kann die Reihenfolge der Men�punkte ver�ndert werden.
	 *
	 * @param int $artikel_id
	 * @param int $menuid
	 * @return void
	 */
	function order_change( $artikel_id = 0, $menuid = 0 )
	{
		// Ordnung ermitteln
		if ($this->checked->artikelorder_action_plus ) {
			$ordnung = "hoch";
		}
		else {
			$ordnung = "runter";
		}
		// Wechsel-Partner emitteln
		$artikel_liste = $this->get_link_list( $menuid );

		$vorgaenger = array ();
		$nachfolger = array ();

		if ( !empty ( $artikel_liste ) ) {
			$artikel_vorgaenger = null;
			$artikel_vorgaenger->linkliste_id = 1;

			foreach ( $artikel_liste as $artikel ) {
				// Mit dem Vorg�nger tauschen
				if ( $ordnung == "hoch" && $artikel_id == $artikel->linkliste_id ) {
					// Fall beide sind gleich
					if ( $artikel_vorgaenger->linkliste_order_id == $artikel->linkliste_order_id ) {
						$artikel_vorgaenger->linkliste_order_id = $artikel_vorgaenger->linkliste_order_id + 1;
					}
					$vorgaenger['linkliste_id'] = $artikel_vorgaenger->linkliste_id;
					$nachfolger['linkliste_id'] = $artikel->linkliste_id;

					$vorgaenger['orderid'] = $artikel_vorgaenger->linkliste_order_id;
					$nachfolger['orderid'] = $artikel->linkliste_order_id;
				}
				// Mit dem Nachfolger tauschen
				if ( $ordnung == "runter" && $artikel_id == $artikel_vorgaenger->linkliste_id ) {
					// Fall beide sind gleich
					if ( $artikel_vorgaenger->linkliste_order_id == $artikel->linkliste_order_id ) {
						if ( $artikel->linkliste_order_id >= 1 ) {
							$artikel->linkliste_order_id = $artikel->linkliste_order_id + 1;
						}
						else {
							$artikel_vorgaenger->linkliste_order_id = $artikel_vorgaenger->linkliste_order_id + 1;
						}
					}
					$vorgaenger['linkliste_id'] = $artikel_vorgaenger->linkliste_id;
					$nachfolger['linkliste_id'] = $artikel->linkliste_id;

					$vorgaenger['orderid'] = $artikel_vorgaenger->linkliste_order_id;
					$nachfolger['orderid'] = $artikel->linkliste_order_id;
				}
				$artikel_vorgaenger = $artikel;
			}
		}

		$this->order_partner_wechsel( $vorgaenger, $nachfolger, $startseite );
		$this->reorder_linklist();
		// Seite neu laden (so kann erneutes Verschieben durch Browser-Reload verhindert werden)
		// $location_url = "./artikel.php?menuid=45&formmenuid=".$menuid;
		// if ($_SESSION['debug_stopallredirect']) echo '<a href="'.$location_url.'">Weiter</a>';
		// else header("Location: $location_url");
		// exit ();
	}

	/**
	 * Wechselt die Ordnungs-ID $order_id der beiden Wechsel-Partner $vorgaenger und $nachfolger
	 *
	 * @param $vorgaenger
	 * @param $nachfolger
	 * @param int $startseite
	 */
	function order_partner_wechsel($vorgaenger, $nachfolger, $startseite = 0 )
	{
		if ( !empty ( $vorgaenger ) && !empty ( $nachfolger ) ) {
			// Test ob Artikel auf der Startseite ist.
			if ( $startseite ) {
				$order_feld = "linkliste_order_id";
			}
			else {
				$order_feld = "linkliste_order_id";
			}

			$sql = sprintf( "UPDATE %s SET %s='%d' WHERE linkliste_id='%d'", $this->cms->tbname['papoo_linkliste_daten'], $this->db->escape( $order_feld ), $this->db->escape( $vorgaenger['orderid'] ), $this->db->escape( $nachfolger['linkliste_id'] ) );
			$this->db->query( $sql );

			$sql = sprintf( "UPDATE %s SET %s='%d' WHERE linkliste_id='%d'", $this->cms->tbname['papoo_linkliste_daten'], $this->db->escape( $order_feld ), $this->db->escape( $nachfolger['orderid'] ), $this->db->escape( $vorgaenger['linkliste_id'] ) );
			$this->db->query( $sql );
		}
	}

	/**
	 * Hiermit kann die Reihenfolge der Men�punkte ver�ndert werden.
	 *
	 * @param int $artikel_id
	 * @param int $menuid
	 * @return void
	 */
	function order_change_cat( $artikel_id = 0, $menuid = 0 )
	{
		// Ordnung ermitteln
		if ( $this->checked->menuorder_action_plus ) {
			$ordnung = "hoch";
		}
		else {
			$ordnung = "runter";
		}
		// Wechsel-Partner emitteln
		$menu = $this->get_cat_list();
		$vorgaenger = array ();
		$nachfolger = array ();
		$menupunkt_vorgaenger_cat_id = 1;

		if ( !empty ( $menu ) ) {
			foreach ( $menu as $menupunkt ) {
				// Mit dem Vorg�nger tauschen
				if ( $ordnung == "hoch" && $this->checked->menuorder_menuid == $menupunkt['cat_id'] ) {
					// Fall beide sind gleich
					if ( $menupunkt['vorgaenger_cat_order_id'] == $menupunkt['cat_order_id'] ) {
						$menupunkt['vorgaenger_cat_order_id'] = $menupunkt['vorgaenger_cat_order_id'] + 1;
					}
					$vorgaenger['cat_id'] = $menupunkt_vorgaenger_cat_id;
					$vorgaenger['cat_order_id'] = $menupunkt_vorgaenger_cat_order_id;
					$nachfolger['cat_id'] = $menupunkt['cat_id'];
					$nachfolger['cat_order_id'] = $menupunkt['cat_order_id'];
				}
				// Mit dem Nachfolger tauschen
				if ( $ordnung == "runter" && $this->checked->menuorder_menuid == $menupunkt_vorgaenger_cat_id ) {
					// Fall beide sind gleich
					if ( $menupunkt['vorgaenger_cat_order_id'] == $menupunkt['cat_order_id'] ) {
						if ( $menupunkt['cat_order_id'] >= 1 ) {
							$menupunkt['cat_order_id'] = $menupunkt['cat_order_id'] - 1;
						}
						else {
							$menupunkt['vorgaenger_cat_order_id'] = $menupunkt['vorgaenger_cat_order_id'] + 1;
						}
					}
					$vorgaenger['cat_id'] = $menupunkt_vorgaenger_cat_id;
					$vorgaenger['cat_order_id'] = $menupunkt_vorgaenger_cat_order_id;
					$nachfolger['cat_id'] = $menupunkt['cat_id'];
					$nachfolger['cat_order_id'] = $menupunkt['cat_order_id'];
				}
				$menupunkt_vorgaenger_cat_id = $menupunkt['cat_id'];
				$menupunkt_vorgaenger_cat_order_id = $menupunkt['cat_order_id'];
			}
		}
		$this->order_partner_wechsel_cat( $vorgaenger, $nachfolger );
	}

	/**
	 * Wechselt die Ordnungs-ID $order_id der beiden Wechsel-Partner $vorgaenger und $nachfolger
	 *
	 * @param $vorgaenger
	 * @param $nachfolger
	 * @param int $startseite
	 */
	function order_partner_wechsel_cat($vorgaenger, $nachfolger, $startseite = 0 )
	{
		if ( !empty ( $vorgaenger ) && !empty ( $nachfolger ) ) {
			// Test ob Artikel auf der Startseite ist.
			if ( $startseite ) {
				$order_feld = "cat_order_id";
			}
			else {
				$order_feld = "cat_order_id";
			}

			$sql = sprintf( "UPDATE %s SET %s='%d' WHERE cat_id='%d'", $this->cms->tbname['papoo_linkliste_cat'], $this->db->escape( $order_feld ), $this->db->escape( $vorgaenger['cat_order_id'] ), $this->db->escape( $nachfolger['cat_id'] ) );
			$this->db->query( $sql );

			$sql = sprintf( "UPDATE %s SET %s='%d' WHERE cat_id='%d'", $this->cms->tbname['papoo_linkliste_cat'], $this->db->escape( $order_feld ), $this->db->escape( $nachfolger['cat_order_id'] ), $this->db->escape( $vorgaenger['cat_id'] ) );
			$this->db->query( $sql );
		}
	}

	function reorder_linklist()
	{
		$links=$this->get_link_list($this->checked->cat_linkliste_id);
		$i=10;
		if (is_array($links)) {
			foreach ($links as $dat) {
				$sql = sprintf( "UPDATE %s SET linkliste_order_id='%d' WHERE linkliste_id='%d'",
					$this->cms->tbname['papoo_linkliste_daten'],
					$this->db->escape( $i ),
					$this->db->escape( $dat->linkliste_id )
				);
				$this->db->query( $sql );
				$i=$i+10;
			}
		}
	}
}

$linklisteplugin = new linklisteplugin();
