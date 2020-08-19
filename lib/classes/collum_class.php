<?php

/**
#####################################
# PAPOO CMS                         #
# (c) Dr. Carsten Euwens 2008      	#
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.3                  #
#####################################
 */

class collum3 {

	/**
	 * collum3 Konstruktor.
	 */
	function __construct() {
		/**
		Klassen globalisieren
		 */
		// cms Klasse einbinden
		global $cms;
		$this->cms = & $cms;
		// Module-Klasse einbinden
		global $module;
		$this->module = & $module;
		// einbinden des Objekts der Datenbank Abstraktionsklasse ez_sql
		global $db;
		$this->db = & $db;
		// User Klasse einbinden
		global $user;
		$this->user = & $user;
		// inhalt Klasse einbinden
		global $content;
		$this->content = & $content;
		// checkedblen Klasse einbinde
		global $checked;
		$this->checked = & $checked;
		// Ersetzungsklasse einbinden
		global $replace;
		$this->replace = & $replace;
		// Download-Klasse einbinden
		global $download;
		$this->download = & $download;
		// Diverse-Klasse
		global $diverse;
		$this->diverse = & $diverse;
		global $artikel;
		$this->artikel = & $artikel;

		global $menu;
		$this->menu = & $menu;
		#print_r($this->module);
		// Rechte Spalte im Frontend
		if (empty($this->module->module_aktiv['mod_dritte_spalte']) ){
			$this->module->module_aktiv['mod_dritte_spalte']="";
		}
		if (empty($this->module->module_aktiv['mod_news']) ){
			$this->module->module_aktiv['mod_news']="";
		}
		if (!defined("admin") && $this->module->module_aktiv['mod_dritte_spalte']) {
			$this->make_collum3();
		}

		// News Spalte im Frontend
		if (!defined("admin") && $this->module->module_aktiv['mod_news']) {
			$this->make_news();
		}
	}

	/**
	 * @return bool
	 */
	function check_write_rights()
	{
		$sql=sprintf("SELECT * FROM %s AS t1
									LEFT JOIN %s AS t2 ON t1.menuid=t2.menuid
									LEFT JOIN %s AS t3 ON t2.gruppenid=t3.gruppenid
									WHERE t1.menuid='17'
									AND userid='%d'
									GROUP BY t1.menuid",
			$this->cms->tbname['papoo_menuint'],
			$this->cms->tbname['papoo_lookup_men_int'],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->user->userid
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		if (!empty($result)) {
			return true;
		}
		return false;
	}

	/**
	 *
	 */
	function make_collum3()
	{
		$aktu_menuid = ($this->checked->menuid);
		if (!is_numeric($aktu_menuid)) {
			$aktu_menuid = 1;
		}
		if (!$aktu_menuid) $aktu_menuid = 1; // 3. Spalte wie in Startseite anzeigen

		// erlaubt also für das template freigeben
		$this->content->template['rechte_spalte'] = '1';

		if ($this->check_write_rights()) {
			$this->content->template['has_collum_write_rights']="ok";
		}

		$sql = sprintf("SELECT 
                            DISTINCT(inhalt_id), 
                            article,
                            name
                        FROM 
                            (
                                %s AS t1, 
                                %s AS t2
                            )
                            LEFT JOIN %s AS t3 ON t3.collum_men_id = '%d' AND t1.inhalt_id = t3.collum_col_id
						WHERE
						    t1.inhalt_id = t2.collum_id
						AND t2.lang_id   = '%d'
                        AND (
                                t3.collum_men_id IS NOT NULL
						    OR  t1.col_menuid    = '%s'
                            )
						ORDER BY 
                            t1.col_order_id ASC 
                        LIMIT 10 ",
			$this->cms->papoo_collum3,
			$this->cms->papoo_language_collum3,
			$this->cms->tbname['papoo_lookup_men_collum3'],
			$this->db->escape($aktu_menuid),
			$this->db->escape($this->cms->lang_id),
			"ok"
		);
		$selectrighti1 = $this->db->get_results($sql);

		if (empty($selectrighti1)) {
			$selectrighti1=array();
		}

		if (empty($selectrighti2)) {
			$selectrighti2=array();
		}

		$selectrighti = array_merge($selectrighti2,$selectrighti1);

		$this->content->template['right_data'] = array ();

		// Übergabe der Inhalte aus der Datenbank für die rechte Spalte
		if (count($selectrighti) >= 1) {
			foreach ($selectrighti as $row) {
				// Inhalte re-escapen
				//$inhalt = stripslashes($row->article);
				// HTML entkodieren
				$inhalt = $row->article;
				$column_id = $row->inhalt_id;
				// Pfade (wegen ModRewrite) korrigieren

				$inhalt = $this->download->replace_downloadlinks($inhalt);
				$inhalt = $this->diverse->do_pfadeanpassen($inhalt);

				// zuweisen des Inhalts an das Template
				$this->content->template['right_data'][] = array ('inhalt_right' => $inhalt,'name' => $row->name,
					'column_id' => $column_id);
			}
		}
		else {
			$this->module->module_aktiv['mod_dritte_spalte'] = false;
		}
	}

	/**
	 * Überprüfen ob nur ein Artikel unter der Menüid eingestellt ist
	 *
	 * @return string|null
	 */
	function check_one_artikel() {
		$sql = sprintf("SELECT DISTINCT(reporeID) FROM %s WHERE cattextid='%d'",
			$this->cms->tbname['papoo_repore'],
			$this->db->escape($this->checked->menuid)
		);
		$result=$this->db->get_var($sql);
		if (is_numeric($result) and $result>0) {
			return $result;
		}
		else {
			return "x";
		}
	}

	/**
	 * Artikel raussuchen
	 *
	 * @return array
	 */
	function get_artikel()
	{

		if (empty($this->artikel_newslist)) {

			$this->orderid="order_id";
		}
		$this->artikel_newslist = 0;

		if (stristr( $this->checked->reporeid,"%")) {
			$this->checked->reporeid= "";
		}

		if (empty($this->checked->reporeid)) {
			//checken ob nicht doch nur ein Artikel da ist
			$this->reporeid=$this->check_one_artikel();
		}
		else {
			$this->reporeid= $this->checked->reporeid;
		}

		if (empty($this->checked->menuid)) {
			$this->checked->menuid=1;
		}

		$select = sprintf(
			"SELECT DISTINCT(header), lan_teaser, url_header, lan_article, reporeID, order_id, cattextid, timestamp, comment_yn, 
					allow_comment, dokuser, count, count_download, lan_teaser_link, teaser_bild_html, teaser_bild
				FROM %s, %s, %s, %s, %s
				WHERE publish_yn_lang='1' AND allow_publish='1' AND cattextid
					LIKE '%s' AND reporeID LIKE '%s' AND reporeID=lan_repore_id AND lang_id='%d' AND reporeID=article_id AND gruppeid_id=gruppenid 
						AND userid='%d' AND t_a_reporeid=reporeID AND t_a_articleid= '%d'
				ORDER BY ta_order_id, header ASC",
			$this->cms->papoo_repore,
			$this->cms->papoo_language_article,
			$this->cms->papoo_lookup_article,
			$this->cms->papoo_lookup_ug,
			$this->cms->tbname['papoo_teaser_lookup_art'],
			"%",
			"%",
			$this->cms->lang_id,
			$this->user->userid,
			$this->db->escape($this->reporeid)
		);
		$this->result_artikel1= $this->db->get_results($select);


		$select = sprintf(
			"SELECT DISTINCT(header), lan_teaser, url_header, lan_article, reporeID, order_id, cattextid, timestamp, comment_yn, 
				allow_comment, dokuser, count,count_download, lan_teaser_link, teaser_bild_html, teaser_bild  
			FROM %s, %s, %s, %s, %s
			WHERE publish_yn_lang='1'  AND allow_publish='1' AND cattextid 
				LIKE '%s' AND reporeID LIKE '%s' AND reporeID=lan_repore_id AND lang_id='%d' AND reporeID=article_id AND gruppeid_id=gruppenid
					AND userid='%d' AND t_reporeid=reporeID AND t_menuid='%d' 
			ORDER BY tm_order_id,  header ASC",
			$this->cms->papoo_repore,
			$this->cms->papoo_language_article,
			$this->cms->papoo_lookup_article,
			$this->cms->papoo_lookup_ug,
			$this->cms->tbname['papoo_teaser_lookup_men'],
			"%",
			"%",
			$this->cms->lang_id,
			$this->user->userid,
			$this->db->escape($this->checked->menuid)
		);
		$this->result_artikel2= $this->db->get_results($select);

		if (!is_array($this->result_artikel1)){
			$this->result_artikel1=array();
		}

		if (!is_array($this->result_artikel2)){
			$this->result_artikel2=array();
		}
		$this->result_artikel=array_merge($this->result_artikel1,$this->result_artikel2);

		return $this->result_artikel;
	}

	/**
	 * @abstract Alle Artikel werden rausgesucht die im Newsteil erscheinen sollen
	 * @return array|void Inhalt für Template alle Newse
	 */
	function make_news()
	{
		//zuweisen das News gemarkte rausgesucht werden
		$this->artikel_newslist="ok";

		$this->orderid="reporeID";
		//Die Daten raussuchenen
		$news=$this->get_artikel();

		//Wenn welche vorhanden sinden

		if (!empty($news)) {
			$selectlink= "SELECT * FROM ".$this->cms->papoo_menu."";
			// Daten in ein assoziatives Array einlesen
			$resultlink= $this->db->get_results($selectlink, ARRAY_A);
			// Resultate Übergeben
			$news_data=array();
			$i=0;
			//an template zuweisen
			foreach ($news as $drin) {
				$var=0;
				if (empty($this->checked->reporeid)){
					$anzahl=$this->artikel->get_anzahl();
					if ($anzahl==1){
						$sql=sprintf("SELECT reporeID FROM %s WHERE cattextid='%s'",
							$this->cms->tbname['papoo_repore'],
							$this->db->escape($this->checked->menuid));
						$var = $this->db->get_var($sql);
					}
				}
				if ($drin->reporeID==$this->checked->reporeid or $var==$drin->reporeID){
					continue;
				}
				//Nurso oft wie eingestellt
				if ($i>=$this->cms->newsnr) {
					continue;
				}
				// Wenn ein spezieller Text für den teaser Link eingegeben wurde, diesen anzeigen
				if (trim($drin->lan_teaser_link) == "") {
					$islink= $drin->header;
				}
				else {
					$islink= $drin->lan_teaser_link;
				}

				// für die entsprechende id den Link heraussuchen
				$lt= $drin->cattextid;
				foreach ($resultlink as $guck) {
					if ($lt == $guck['menuid']) {
						// Link wird Übergeben (Bsp. index.php)
						$lt_text= $guck['menulink'];
					}
				}
				// wenn mod_rewrite dann bearbeiten
				if ($this->cms->mod_rewrite == 2) {
					if (!empty ($lt_text)) {
						// .php wird entfernt FIXME für andere Endungen
						$lt_text= str_ireplace(".php", "", $lt_text);
					}
					if (!empty($drin->teaser_bild_html)){

						$drin->teaser_bild_html=str_ireplace("\./images",PAPOO_WEB_PFAD."/images",$drin->teaser_bild_html);
						$drin->teaser_bild_html=str_ireplace("teaserbildleft","collumleft",$drin->teaser_bild_html);
						$drin->teaser_bild_html=str_ireplace("teaserbildright","collumright",$drin->teaser_bild_html);
						//style="float: left;"
						$drin->teaser_bild_html=str_ireplace("float:left","",$drin->teaser_bild_html);
					}
				}

				$this->artikel->m_url = array();
				$urldat_1 = "";

				$this->artikel->get_verzeichnis($drin->cattextid);
				$this->artikel->m_url = array_reverse($this->artikel->m_url);

				if (!empty($this->artikel->m_url)) {
					foreach ($this->artikel->m_url as $urldat) {
						$urldat_1 .= $this->menu->urlencode($urldat) . "/";
					}
				}
				if (!empty($urldat_1)) {
					$header_url = $this->cms->webverzeichnis."/".$this->cms->webvar.$urldat_1 . (($this->menu->urlencode($drin->url_header))) . ".html";
				}
				else {
					$header_url = $this->cms->webverzeichnis."/".$urldat_1 . (($this->menu->urlencode($drin->url_header))) . ".html";
				}
				if ($this->cms->mod_rewrite < 2) {
					$header_url="";
				}

				//Bild eine id zuweisen
				$bildid=explode(".",$drin->teaser_bild);
				$drin->teaser_bild_html=str_ireplace("src","id=\"".$bildid['0']."\" src",$drin->teaser_bild_html);
				#$drin->lan_teaser=str_ireplace("&","&amp;amp;amp;",$drin->lan_teaser);
				#$drin->header=substr($drin->header,0,100)."...";

				IfNotSetNull($this->content->template['message_2144']);

				array_push($news_data, array(
					'header'=>"nobr:".$drin->header,
					'lan_teaser'=>"nodecode:".$drin->lan_teaser,
					'img'=>"nobr:".$drin->teaser_bild_html,
					'linktext'=>$lt_text,
					'cattextid'=>$drin->cattextid,
					'reporeid'=>$drin->reporeID,
					'uberschrift'=>"nobr:".$drin->header,
					'url_header' => $header_url,
					'islinktext'=>"nobr:" . $this->content->template['message_2144'],
					'islink'=>"nobr:".$islink,
					'nummer'=>$i,
				));
				$i++;
			}
		}
		if (!empty($news_data)) {
			$this->content->template['news_data'] = $news_data;
		}
		else {
			$this->module->module_aktiv['mod_news'] = false;
		}
		$this->artikel->artikel_newslist="";
	}
}

$collum3 = new collum3();