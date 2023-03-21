<?php

/**
#####################################
# CMS Papoo                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.2                  #
#####################################
 */

/**
 * Class searcher
 */
#[AllowDynamicProperties]
class searcher
{

	/** @var string Suchwert der zurückgegeben wird */
	var $result_search;
	/** @var int Anzahl der Suchergebnisse */
	var $result_anzahl;

	/**
	 * searcher constructor.
	 */
	function __construct()
	{
		/**
		Klassen globalisieren
		 */

		// cms Klasse einbinden
		global $cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		// Messages einbinden
		global $message;
		// User Klasse einbinden
		global $user;
		// weitere Seiten Klasse einbinden
		global $weiter;
		// inhalt Klasse einbinden
		global $content;
		// checkedblen Klasse einbinden
		global $checked;
		//Artikel Klasse
		global $artikel;
		$this->artikel=&$artikel;
		//Menüklasse
		global $menu;
		$this->menu=&$menu;

		// Hier die Klassen als Referenzen
		$this->cms = & $cms;
		$this->db = & $db;
		$this->content = & $content;
		$this->message = & $message;
		$this->user = & $user;
		$this->weiter = & $weiter;
		$this->checked = & $checked;

		$this->language_id = $this->cms->lang_id;
	}

	/**
	 * Suchbegriff, alle/jedes/exakt
	 *
	 * @param $searchfor
	 * @param string $what
	 * @return array|void
	 */
	function do_search( $searchfor, $what='' )
	{
		//Keine Eingabe?
		if ($searchfor == '') {
			return array();
		}
		$papoo_repore = $this->cms->papoo_repore;
		$papoo_repore_lang = $this->cms->papoo_language_article;
		$papoo_user = $this->cms->papoo_user;
		$papoo_lookup_ug = $this->cms->papoo_lookup_ug;
		$papoo_lookup_article = $this->cms->papoo_lookup_article;
		$papoo_language_stamm = $this->cms->papoo_language_stamm;
		$papoo_language_collum3 = $this->cms->papoo_language_collum3;
		$papoo_collum3 = $this->cms->papoo_collum3;
		// Werte für array_slice (Ausschnitt aus allen Ergebnissen)
		$sqllimit = $this->cms->sqllimit;
		$t1 = str_replace("LIMIT ", "", $sqllimit);
		$t2 = explode(",", $t1);
		$returnresult = array ();
		//Eingabe checken und bereinigen
		$searchfor = trim( $searchfor );
		$searchfor = $this->db->escape($this->clean($searchfor));
		$this->content->template['search']=$searchfor;
		IfNotSetNull($this->checked->zeitraum);
		$this->content->template['zeitraum']=$this->checked->zeitraum;
		$where1_artikel = array();
		$where1_startseite = array();
		$where1_collum3 = array();
		$suchbereich = (isset($this->checked->erw_suchbereich))?(int)$this->checked->erw_suchbereich:0;

		switch ($what) {
		case 'exakt':
			$lower_artikel = array();
			$lower_startseite = array();
			$lower_collum3 = array();
			$lower_artikel[] = "LOWER(header) LIKE '%$searchfor%'";
			$lower_artikel[] = "LOWER(lan_article) LIKE '%$searchfor%'";
			$lower_artikel[] = "LOWER(lan_metadescrip) LIKE '%$searchfor%'";
			$lower_artikel[] = "LOWER(lan_teaser) LIKE '%$searchfor%'";
			$lower_startseite[] = "LOWER(start_text_sans) LIKE '%$searchfor%'";
			$lower_startseite[] = "LOWER(beschreibung) LIKE '%$searchfor%'";
			$lower_startseite[] = "LOWER(seitentitle) LIKE '%$searchfor%'";
			$lower_collum3[] = "LOWER(article) LIKE '%$searchfor%'";
			$where_artikel = '(' . implode( ') OR (', $lower_artikel ) . ')';
			$where_startseite = '(' . implode( ') OR (', $lower_startseite ) . ')';
			$where_collum3 = '(' . implode( ') OR (', $lower_collum3 ) . ')';
			break;

		case 'alle':

		case 'jedes':

		default:
			$worte = explode( ' ', $searchfor );

			foreach ($worte as $wort) {
				$lower_artikel = array();
				$lower_startseite = array();
				$lower_collum3 = array();
				$lower_artikel[] = "LOWER(header) LIKE '%$wort%'";
				$lower_artikel[] = "LOWER(lan_article) LIKE '%$wort%'";
				$lower_artikel[] = "LOWER(lan_metadescrip) LIKE '%$wort%'";
				$lower_artikel[] = "LOWER(lan_teaser) LIKE '%$wort%'";
				$lower_startseite[] = "LOWER(start_text_sans) LIKE '%$wort%'";
				$lower_startseite[] = "LOWER(beschreibung) LIKE '%$wort%'";
				$lower_startseite[] = "LOWER(seitentitle) LIKE '%$wort%'";
				$lower_collum3[] = "LOWER(article) LIKE '%$wort%'";
				$where1_artikel[] = implode( ' OR ', $lower_artikel );
				$where1_startseite[] = implode( ' OR ', $lower_startseite );
				$where1_collum3[] = implode( ' OR ', $lower_collum3 );
			}
			$where_artikel = '(' . implode( ($what == 'alle' ? ') AND (' : ') OR ('), $where1_artikel ) . ')';
			$where_startseite = '(' . implode( ($what == 'alle' ? ') AND (' : ') OR ('), $where1_startseite ) . ')';
			$where_collum3 = '(' . implode( ($what == 'alle' ? ') AND (' : ') OR ('), $where1_collum3 ) . ')';
			break;
		}
		$order = 'header ';
		IfNotSetNull($this->checked->ascdesc);

		if ($this->checked->ascdesc=="asc" or $this->checked->ascdesc=="desc") {
			switch ($this->checked->SortFeld) {
			case 'titel':

			default:
				$order = 'header ' . $this->db->escape($this->checked->ascdesc) . ' ';
				break;

			case 'datum':
				//Anmerkung: STR_TO_DATE ist erst ab MySQL Version 4.1.1 verfügbar
				//$order = "STR_TO_DATE(timestamp, '%d.%m.%Y %H:%i:%s') " . $this->checked->ascdesc . ' ';
				//$order = "erstellungsdatum " . $this->checked->ascdesc . ' ';
				$order = "stamptime " . $this->db->escape($this->checked->ascdesc) . ' ';
				break;
			}
		}
		$jetzt = time();
		switch ($this->checked->zeitraum) {
		case 'none':

		default:
			$zeit = '';
			break;

			//1 Tag
		case '1':
			$zeit = " AND stamptime>'".($jetzt-86400)."' ";
			break;

			//3 Tage
		case '2':
			$zeit = " AND stamptime>'".($jetzt-259200)."' ";
			break;

			//1 Woche
		case '3':
			$zeit = " AND stamptime>'".($jetzt-604800)."' ";
			break;

			//4 Wochen
		case '4':
			$zeit = " AND stamptime>'".($jetzt-2419200)."' ";
			break;

			//3 Monate
		case '5':
			$zeit = " AND stamptime>'".($jetzt-7776000)."' ";
			break;
		}
		if ($suchbereich == 1 || !$suchbereich) {
			//Startseite durchsuchen
			$sql = "SELECT start_text_sans,
							beschreibung,
							seitentitle 
							FROM $papoo_language_stamm"
				. "\n WHERE ( $where_startseite )"
				. "\n AND lang_id = '" . $this->cms->lang_id . "'";
			$result_startseite = $this->db->get_results($sql);
			if ($this->cms->mod_rewrite == "2") {
				$index = "index";
			}
			else {
				$index = "index.php";
			}
			if (!empty($result_startseite)) {
				$text = "";
				array_push($returnresult, array (
					'linktext' => $index,
					'uberschrift' => "Startseite/Homepage",
					'text' => $text,
					'islink' => "Startseite/Homepage",
					'reporeid' => "0",
					'cattextid' => "1",
					'islinktext' => "Startseite/Homepage",
					'islink_ok'=>"ok"));
				//Anzahl aller matches zählen; Startwert 1 (0 wenn nichts in der Startseite gefunden wurde)
				$this->result_anzahl = 1;
			}
			//Zählen der Matches in collum3
			$sql = "SELECT COUNT(*) 
							FROM $papoo_language_collum3,
									$papoo_collum3"
				. "\n WHERE ( $where_collum3 )"
				. "\n AND lang_id = '" . $this->cms->lang_id . "'"
				. "\n AND collum_id = inhalt_id";
			$this->result_anzahl_collum3 = $this->db->get_var($sql);
			//Abfrage und Ausgabe nur, wenn etwas gefunden wurde
			if ($this->result_anzahl_collum3) {
				$sql = "SELECT * 
								FROM $papoo_language_collum3, 
										$papoo_collum3"
					. "\n WHERE ( $where_collum3 )"
					. "\n AND lang_id = '"
					. $this->cms->lang_id
					. "'"
					. "\n AND collum_id = inhalt_id";
				$result_collum3 = $this->db->get_results($sql);

				foreach ($result_collum3 as $col) {
					$sql=sprintf("SELECT * FROM %s
												WHERE collum_col_id = '%d' 
												LIMIT 1",
						$this->cms->tbname['papoo_lookup_men_collum3'],
						$col->collum_id
					);
					$result2 = $this->db->get_results($sql, ARRAY_A);

					IfNotSetNull($url_header);
					if (is_array($this->menu->data_front_complete)) {
						foreach ($this->menu->data_front_complete as $key=>$value) {
							if ($value['menuid']==$result2['0']['collum_men_id']) {
								$url_header=$value['menuname_url'];
							}
							if ($this->cms->mod_free == 1) {
								$url_header=$value['menuname_free_url'];
							}
						}
					}
					$text = strip_tags($col->article);
					$head = mb_substr($text, 0, 20, 'UTF-8') . "...";
					$text1 = substr($text, 0, 50);
					$text2 = mb_substr($text, 0, 300, 'UTF-8');
					array_push($returnresult, array (
						'linktext' => $index,
						'uberschrift' => "Rechte Spalte: " . $head,
						'url_header' => $url_header,
						'text' => $text2,
						'metadescrip' => $text2,
						'islink' =>"Rechte Spalte: " . $head,
						'reporeid' => "0",
						'is_collum' => "1",
						'cattextid' => $col->col_menuid,
						'islinktext' => $text1,
						'islink_ok'=>"ok"
					));
				}
				//Anzahl aller matches zählen
				$this->result_anzahl += $this->result_anzahl_collum3;
			}
			//Zählen der matches in Artikeln
			// FIXME: sprintf
			$sql = "SELECT COUNT(DISTINCT(reporeID))"
				. "\n FROM $papoo_repore_lang, $papoo_repore, $papoo_user, $papoo_lookup_ug, $papoo_lookup_article"
				. "\n WHERE ( $where_artikel )"
				. "\n AND publish_yn_lang = '1' AND allow_publish = '1'"
				. "\n AND reporeID = lan_repore_id"
				. $zeit
				. "\n AND lang_id = '" . $this->cms->lang_id . "'"
				. "\n AND $papoo_user.userid = " . $this->user->userid
				. "\n AND $papoo_user.userid = $papoo_lookup_ug.userid"
				. "\n AND $papoo_lookup_ug.gruppenid = gruppeid_id"
				. "\n AND article_id = reporeID";
			$this->result_anzahl += $this->db->get_var($sql);
			//Müsste eigentlich nicht, wenn nix gefunden wird
			$sql = "SELECT DISTINCT(reporeID), 
									lan_teaser, 
									header, 
									cattextid, 
									lan_metadescrip, 
									url_header, "
				. "\n DATE_FORMAT(erstellungsdatum, '%d.%m.%Y %H:%i:%s') AS erstellungsdatum2, "
				. "\n FROM_UNIXTIME(stamptime, '%d.%m.%Y %H:%i:%s') AS stamptime2 "
				. "\n FROM $papoo_repore_lang, $papoo_repore, $papoo_user, $papoo_lookup_ug, $papoo_lookup_article"
				. "\n WHERE ( $where_artikel )"
				. "\n AND publish_yn_lang = '1' AND allow_publish = '1'"
				. "\n AND reporeID = lan_repore_id"
				. $zeit
				. "\n AND lang_id = '" . $this->cms->lang_id . "'"
				. "\n AND $papoo_user.userid = " . $this->user->userid
				. "\n AND $papoo_user.userid=$papoo_lookup_ug.userid"
				. "\n AND $papoo_lookup_ug.gruppenid = gruppeid_id"
				. "\n AND article_id = reporeID"
				. "\n GROUP BY reporeID ORDER BY " . $order;
			$result_artikel = $this->db->get_results($sql);
			IfNotSetNull($result_collum3);

			if(!empty($result_artikel)) {
				foreach ($result_artikel as $row) {
					$returnresultmerge = $this->do_search_in_array($row);
					$returnresult = array_merge($returnresult, $returnresultmerge);
				}
			}
			$returnresult2 = array_slice($returnresult, $t2[0], $t2[1], true);

			// Bilder Galerie Plugin durchsuchen falls vorhanden
			global $db_praefix;
			$sql = sprintf("SELECT plugin_name FROM %s WHERE plugin_name='%s'", $db_praefix . "papoo_plugins", "Bilder-Galerie");
			$tmp_result = $this->db->get_results($sql, ARRAY_A);
			if(!empty($tmp_result)) {
				require_once PAPOO_ABS_PFAD . "/plugins/galerie/lib/galerie_class.php";
				global $galerie;
				$galerie->do_search($returnresult2, $searchfor, $sqllimit, $this->result_anzahl);
			}

			return ($returnresult2);
		}
		if ($suchbereich == 2) {
			if (!empty($this->cms->tbname['plugin_shop_daten']) && empty($this->cms->tbname['plugin_ext_search_page'])) {
				require_once PAPOO_ABS_PFAD."/plugins/papoo_shop/lib/shop_class_search.php";
				$shop_search=new shop_class_search();
				return $shop_search->do_search($returnresult,$searchfor,$sqllimit);
			}
		}
		return array();
	}

	/**
	 * Daten eines Artikels übergeben ans array
	 *
	 * @param $dsia
	 * @return array
	 */
	function do_search_in_array($dsia)
	{
		$returnresult = array ();
		$islink = $dsia->header;
		$textunter = $dsia->lan_metadescrip;
		$this->artikel->m_url=array();
		$urldat_1="";
		$this->artikel->get_verzeichnis($dsia->cattextid,"search");
		$this->artikel->m_url=array_reverse($this->artikel->m_url);
		if (!empty($this->artikel->m_url)) {
			foreach ($this->artikel->m_url as $urldat) {
				$urldat_1.=$this->menu->urlencode($urldat)."/";
			}
		}
		$header_url=$urldat_1.$dsia->url_header.".html";
		if ($this->cms->mod_rewrite == "2") {
			$index = "index";
		}
		else {
			$index = "index.php";
		}

		if ($this->cms->mod_free==1) {
			if (!empty($dsia->url_header)) {
				$header_url=($dsia->url_header);
				$first_url=$header_url[0];
				if ($first_url=="/") {
					$header_url=substr($header_url,1,strlen($header_url));
				}
			}

		}
		if ($dsia->erstellungsdatum2 == "00.00.0000 00:00:00") $dsia->erstellungsdatum2 = "";
		if ($dsia->stamptime2 == "01.01.1970 01:00:00") $dsia->stamptime2 = "";

		IfNotSetNull($this->content->template['message_2144']);

		# Daten zuweisen linksearchfor
		array_push($returnresult, array (
			'linktext' => $index,
			'uberschrift' => $islink,
			'url_header' => $header_url,
			'text' => $textunter,
			'islink' => $islink,
			'reporeid' => $dsia->reporeID,
			'cattextid' => $dsia->cattextid,
			'islinktext' => $this->content->template['message_2144'],
			'islink_ok'=>"ok",
			'metadescrip' => $dsia->lan_metadescrip,
			'stamptime' => $dsia->stamptime2,
			'creationdate' => $dsia->erstellungsdatum2));
		return $returnresult;
	}

	/**
	 * @param $search
	 * @return mixed|string
	 */
	function clean($search) {
		//$search auf unerlaubte Zeichen überprüfen und evtl. bereinigen
		$search = trim($search);
		$remove = "<>'\"_%*\\";
		for ($i = 0; $i < strlen($remove); $i ++)
			$search = str_replace(substr($remove, $i, 1), "", $search);
		return $search;
	}
}

$searcher = new searcher();
