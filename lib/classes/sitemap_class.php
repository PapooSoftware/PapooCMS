<?php
/**
 * #####################################
 * # CMS Papoo                         #
 * # (c) Carsten Euwens 2008           #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version 4.2                   #
 * #####################################
 *
 */

/**
 * Hier wird die Sitemap generiert
 *
 * Class sitemap
 */
#[AllowDynamicProperties]
class sitemap
{
	/**
	 * sitemap constructor.
	 */
	function __construct()
	{
		// cms Klasse einbinden
		global $cms;
		$this->cms = &$cms;

		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = &$db;

		// User Klasse einbinden
		global $user;
		$this->user = &$user;

		// inhalt Klasse einbinden
		global $content;
		$this->content = &$content;

		// checked Klasse einbinde
		global $checked;
		$this->checked = &$checked;

		// Menü-Klasse einbinden
		global $menu;
		$this->menu = &$menu;

		// eigene Variablen
		//??$this->table_data = array();

		IfNotSetNull($this->leserechte);
		IfNotSetNull($this->leserechte_menu);
	}

	/**
	 * @param string $sprach_id
	 * @param string $modus
	 * @param string $google
	 */
	function make_sitemap($sprach_id = "", $modus = "", $google="")
	{
		if (empty($sprach_id)) {
			$sprach_id = $this->cms->lang_id;
		}

		if (defined("admin") && empty($google)) {
			$this->menu->data_front_complete = $this->menu->menu_data_read("FRONT");
			$this->menu->data_front_publish = $this->menu->menu_data_read("FRONT_PUBLISH");

			if (is_array($this->menu->data_front_complete)) {
				$i=0;
				foreach ($this->menu->data_front_complete as $key=>$value) {
					if (is_array($this->menu->data_front_publish)) {
						foreach ($this->menu->data_front_publish as $key1=>$value1) {
							//Wenn Rechte
							if ($value['menuid']==$value1['menuid']) {
								$this->menu->data_front_complete[$i]['has_rights']=1;
							}
						}
					}
					$i++;
				}
			}
			$this->menu->data_front_publish =$this->menu->data_front_complete;
		}

		//Bei mod_free noch die Lookup Cat Art rausholen
		if ($this->cms->mod_free==1) {
			$sql=sprintf("SELECT * FROM %s",
				$this->cms->tbname['papoo_lookup_art_cat']
			);
			$look=$this->db->get_results($sql,ARRAY_A);
			if (is_array($look)) {
				foreach ($look as $key=>$value) {
					$this->lookup_art_cat[$value['lart_id']][$value['lcat_id']]="ok";
				}
			}
		}

		$this->content->template['table_data'] = $this->sitemap_table_build($sprach_id, $modus);

		IfNotSetNull($this->menu->ebenen_shift_ende);

		$this->content->template['sitemap_ebenen_shift_ende'] = $this->menu->ebenen_shift_ende;

		$this->menu->make_menucats();
	}

	/**
	 * Baut die Tabelle des Inhalts-Verzeichnis auf
	 *
	 * @param string $sprach_id
	 * @param string $modus
	 * @return array|bool
	 */
	function sitemap_table_build($sprach_id = "", $modus = "")
	{
		IfNotSetNull($leserechte);
		IfNotSetNull($leserechte_menu);

		if (empty($sprach_id)) {
			$sprach_id = $this->cms->lang_id;
		}
		$sitemap_data = array();
		$this->artikel_liste_data=array();
		// Komplette Menü-Daten einlesen (schon als Navigation vorbereitet, d.h. incl. <ul>s und Pfadanpassungen etc.
		/**
		 * if (is_array($_SESSION['dbp']['sitemap_menu_data']))
		 * {
		 * $menu_data=$_SESSION['dbp']['sitemap_menu_data'];
		 * }
		 * else {
		 * $menu_data = $this->menu->menu_navigation_build("FRONT_COMPLETE", "SITEMAP");
		 * $_SESSION['dbp']['sitemap_menu_data']=$menu_data;
		 * }
		 */
		if ($modus == "intern_publish") {
			$sql2=sprintf("SELECT * FROM %s WHERE gruppeid_id='10'",
				$this->cms->tbname['papoo_lookup_me_all_ext']
			);
			$this->leserechte_menu=$this->db->get_results($sql2,ARRAY_A);
		}

		if ($this->cms->categories == 1) {
			// Kategorien rausholen
			$catlist_data = $this->menu->preset_categories(true);
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
		$menu_data = $this->menu->menu_navigation_build("FRONT_COMPLETE", "SITEMAP");
		$this->menu_data=$menu_data;
		$artikel_liste = $this->get_artikel($sprach_id, $modus);

		if (is_array($this->leserechte)) {
			foreach ($this->leserechte as $key=>$value) {
				$leserechte[$value['article_id']]=$value['gruppeid_id'];
			}
		}

		if (is_array($this->leserechte_menu)) {
			foreach ($this->leserechte_menu as $key=>$value) {
				$leserechte_menu[$value['menuid_id']]=$value['gruppeid_id'];
			}
		}

		// Menü-Daten
		if (!empty($menu_data)) {
			foreach ($menu_data as $menupunkt)
			{
				$menupunkt['artikel_entalten'] = false;
				$menupunkt['artikel_anzahl'] = 0;
				$menupunkt['artikel'] = array();

				// Anpassungen der Namen
				$menupunkt['text'] = $menupunkt['menuname'];
				$menupunkt['title'] = $menupunkt['lang_title'];
				$menupunkt['link'] = $menupunkt['menulink'];

				//menu.menuname_url
				//$menupunkt['menuname_url'] = $menupunkt['menuname_url'];
				IfNotSetNull($menupunkt['menuname_url']);

				$is_url="";
				if (!empty($artikel_liste)) {
					// Artikel zuweisen
					foreach ($artikel_liste as $artikel) {
						if ($menupunkt['menuid'] == $artikel->lcat_id) {
							$menupunkt['artikel_enthalten'] = true;
							$menupunkt['artikel_anzahl'] += 1;

							$artikelurl = ($artikel->url_header) . ".html";
							if ($this->cms->mod_free==1) {
								$artikelurl = ($artikel->url_header);
								$first_url=$artikelurl[0];

								if ($first_url=="/") {
									$artikelurl=substr($artikelurl,1,strlen($artikelurl));
								}
							}
							if (empty($artikelurl)) {
								$artikelurl="jgiusdhflghldjgsfdhgjsdhlkfjghlksjdgklhsdklghsdjklg";
							}

							if (stristr( $menupunkt['menuname_url'] ?? '',$artikelurl)  ) {
								$menupunkt['menuname_art_url']=str_replace($artikelurl,"",$menupunkt['menuname_url']);
								$is_url="ok";
							}

							if (empty($is_url)) {
								$menupunkt['menuname_art_url']=$menupunkt['menuname_url'];
							}

							if ($this->cms->mod_free==1) {
								// FIXME: index menuname_free_url gibt es nicht. Workaround for now.
								IfNotSetNull($menupunkt['menuname_free_url']);
								$menupunkt['menuname_art_url'] = $menupunkt['menuname_free_url'];
								// FIXME: lookup_art_cat existiert nicht. Workaround for now.
								IfNotSetNull($this->lookup_art_cat);
								if (is_array($this->lookup_art_cat[$artikel->reporeID]) && count($this->lookup_art_cat[$artikel->reporeID]) > 1) {
									$klammer_id="m-".$artikel->lcat_id."";
									$last  = substr($artikelurl,-1,1);
									$last2 = substr($artikelurl,-5,5);

									//Artikel mit .html
									if ($last2 ==".html" ) {
										$artikelurl=str_replace(".html","-".$klammer_id.".html",$artikelurl);
									}
									//Artikel mit /
									if ($last =="/") {
										$artikelurl  = substr($artikelurl,0,-1);
										$artikelurl=$artikelurl."-".$klammer_id."/";
									}
								}
							}
							IfNotSetNull($artikel->publish_yn_lang);
							IfNotSetNull($leserechte[$artikel->reporeID]);
							$menupunkt['artikel'][] = array(
								'reporeid' => $artikel->reporeID,
								'level' => $menupunkt['level'] + 1,
								'cattextid' => $artikel->lcat_id,
								'text' => $artikel->header,
								'lang_id' => $artikel->lang_id,
								'arturl' => $artikelurl,
								'link' => $menupunkt['link'],
								'leserechte'=> $leserechte[$artikel->reporeID],
								'publish_yn' => $artikel->publish_yn_lang
							);
							$this->artikel_liste_data[]=$menupunkt['artikel'];
						}
					}
				}
				$menupunkt['leserechte']=$leserechte_menu[$menupunkt['menuid']];

				// FIXME: $this->menu->catlist_data existiert eig. nicht
				IfNotSetNull($catlist_data);

				$menupunkt["categoryFound"] = array_reduce((array)$catlist_data,
					function ($categoryFound, $category) use ($menupunkt) {
						return $categoryFound || $category["cat_lang_id"] == $menupunkt["cat_cid"];
					}, false
				);
				// Daten zuweisen
				$sitemap_data[] = $menupunkt;
			}
		}
		if ($this->cms->categories == 1) {
			$this->content->template["orphanedMenuItemsExist"] = array_reduce($sitemap_data,
				function ($orphanFound, $menu) {
					return $orphanFound || $menu["categoryFound"] == false;
				}, false
			);
		}

		if (!empty($sitemap_data)) {
			return $sitemap_data;
		}
		else {
			return false;
		}
	}

	/**
	 * TODO: Würde wohl in der Artikel-Klasse besser aufgehohen sein.
	 *
	 * @param string $sprach_id
	 * @param string $modus
	 * @return array|void
	 */
	function get_artikel($sprach_id = "", $modus = "")
	{
		if (empty($sprach_id)) {
			$sprach_id = $this->cms->lang_id;
		}

		if ($modus == "intern_publish") {
			$sql = sprintf("SELECT DISTINCT reporeID, publish_yn_lang, lcat_id, lang_id,  header, url_header, cattextid
				FROM %s AS t1, %s AS t2, %s AS t3, %s AS t4, %s AS t5
				WHERE t1.reporeID=t2.lan_repore_id AND t2.lang_id='%d'
				AND t1.reporeID=t3.article_wid_id AND t1.reporeID=t5.lart_id
				AND t3.gruppeid_wid_id=t4.gruppenid
				AND t4.userid='%s'
				ORDER BY t5.lart_order_id ASC",

				$this->cms->papoo_repore,
				$this->cms->papoo_language_article,
				$this->cms->tbname['papoo_lookup_write_article'],
				$this->cms->papoo_lookup_ug,
				$this->cms->tbname['papoo_lookup_art_cat'],

				$sprach_id,

				$this->user->userid
			);
		}
		else {
			$sql = sprintf("SELECT DISTINCT reporeID, lcat_id, header,lang_id,  url_header, cattextid
				FROM %s AS t1, %s AS t2, %s AS t3, %s AS t4, %s AS t5
				WHERE t2.publish_yn_lang='1' AND t1.publish_yn_intra='0' AND t1.allow_publish='1'
				AND t1.reporeID=t2.lan_repore_id AND t2.lang_id='%d'
				AND t1.reporeID=t3.article_id AND t1.reporeID=t5.lart_id
				AND t3.gruppeid_id=t4.gruppenid
				AND t4.userid='%s'
				ORDER BY t5.lart_order_id ASC",

				$this->cms->papoo_repore,
				$this->cms->papoo_language_article,
				$this->cms->papoo_lookup_article,
				$this->cms->papoo_lookup_ug,
				$this->cms->tbname['papoo_lookup_art_cat'],

				$sprach_id,

				$this->user->userid
			);
		}

		if (empty($this->checked->reporeid)) {
			$this->checked->reporeid = "";
		}

		if (stristr( $this->checked->reporeid,"%")) {
			$this->checked->reporeid = "";
		}

		$temp_return = $this->db->get_results($sql);

		if ($modus == "intern_publish") {
			$sql2=sprintf("SELECT * FROM %s
												WHERE gruppeid_id='10'",
				$this->cms->papoo_lookup_article);
			$this->leserechte=$this->db->get_results($sql2,ARRAY_A);
		}
		return $this->result_artikel = $temp_return;
	}
}

$sitemap = new sitemap();