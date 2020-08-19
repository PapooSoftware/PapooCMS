<?php

/**
 * Class autocontent_class
 */
class autocontent_class
{
	/**
	 * autocontent_class constructor.
	 */
	function __construct()
	{
		global $db, $db_praefix, $cms, $user, $checked, $content;
		$this->db = & $db;
		$this->db_praefix = & $db_praefix;
		$this->cms = & $cms;
		$this->user = & $user;
		$this->checked = & $checked;
		$this->content = & $content;

		// Aktions-Weiche
		// **************
		if (defined("admin")) {

			$user->check_intern();

			global $template;
			if (strpos("XXX".$template, "autocontent_backend.html")) {
			IfNotSetNull($this->checked->autocontent_action);
				switch ($this->checked->autocontent_action) {
					case "autocontent_article":
						$this->autocontent_article();
						$this->content->template['plugin']['devtools']['template_weiche'] = "autocontent_article";
						break;

					case "":
					default;
						$this->content->template['plugin']['devtools']['template_weiche'] = "START";
						break;
				}
			}
		}
	}

	function autocontent_article()
	{
		// Liste der Men�punkte
		global $menu;
		$temp_menu_liste = $menu->menu_data_read("FRONT");

		// Liste der Men�punkte f�r die schon ein Artikel zugewiesen ist
		$sql = sprintf("SELECT DISTINCT lcat_id FROM %s", $this->db_praefix."papoo_lookup_art_cat");
		$temp_menu_article_liste = $this->db->get_col($sql);
		if (empty($temp_menu_article_liste)) {
			$temp_menu_article_liste = array();
		}

		if (!empty($temp_menu_liste)) {
			foreach($temp_menu_liste as $temp_menu) {
				// .. Pr�fung ob:
				// 			- nicht Startseite
				// 			- normaler Men�punkt (z.B. nicht Kontakt.php oder Plugin)
				// 			- nicht schon ein Artikel besteht
				if (($temp_menu['menuid'] > 1) AND ($temp_menu['menulinklang'] == "index.php") AND !in_array($temp_menu['menuid'], $temp_menu_article_liste)) {
					// Artikel-Daten
					// *****************************
					$sql = sprintf("INSERT INTO %s
									SET
									cattextid='%d',
									cat_category_id=0,
									dokuser='%d',
									dokuser_last='%d',
									timestamp=NOW(),
									erstellungsdatum=NOW(),
									stamptime='%s',

									order_id=0,
									order_id_start=0,
									allow_comment=0,
									artikel_news_list =0,

									publish_yn='1',
									pub_dauerhaft=1,
									allow_publish=1,
									dok_teaserfix=0,

									uberschrift_hyn=1,
									teaser_atyn=1,
									teaser_list=0,
									teaser_bild_groesse='x'
									",

									$this->db_praefix."papoo_repore",

									$temp_menu['menuid'],
									$this->user->userid,
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

					// Sprach-Daten
					// *****************************
					$sql = sprintf("INSERT INTO %s SET
									lan_repore_id='%d', lang_id='%d',
									header='%s', url_header='%s',
									lan_teaser='%s',
									lan_article_sans='%s', lan_article='%s', publish_yn_lang='1'
									",

									$this->db_praefix."papoo_language_article",

									$temp_repore_id,
									$this->cms->lang_id,

									$temp_menu['menuname'],
									$temp_artikel_url,

									"<p>.. TEASER ..</p>",

									"<p>.. INHALT ..</p>",
									"<p>.. INHALT ..</p>"
					);
					$this->db->query($sql);

					// Men�-Zuweisung
					// *****************************
					$sql = sprintf("INSERT INTO %s SET lart_id='%d', lcat_id='%d', lart_order_id=10",
									$this->db_praefix."papoo_lookup_art_cat",
									$temp_repore_id,
									$temp_menu['menuid']
					);
					$this->db->query($sql);

					// Lese-Rechte
					// *****************************
					$sql = sprintf("INSERT INTO %s SET article_id='%d', gruppeid_id=1",
									$this->db_praefix."papoo_lookup_article",
									$temp_repore_id
					);
					$this->db->query($sql);

					if ($this->cms->stamm_artikel_rechte_jeder==1) {
						$sql = sprintf("INSERT INTO %s SET article_id='%d', gruppeid_id=10",
										$this->db_praefix."papoo_lookup_article",
										$temp_repore_id
						);
						$this->db->query($sql);
					}

					// Schreib-Rechte
					// *****************************
					$sql = sprintf("INSERT INTO %s SET article_wid_id='%d', gruppeid_wid_id=1",
									$this->db_praefix."papoo_lookup_write_article",
									$temp_repore_id
					);
					$this->db->query($sql);

					if($this->cms->stamm_artikel_rechte_chef==1)
					{
						$sql = sprintf("INSERT INTO %s SET article_wid_id='%d', gruppeid_wid_id=11",
										$this->db_praefix."papoo_lookup_write_article",
										$temp_repore_id
						);
						$this->db->query($sql);
					}
				}
			}
		}
	}

	/**
	 * !!! l�scht ALLE zu Artikeln geh�rende Inhalte !!!! SEHR gef�hrlich !!! ist nur zum "R�cksetzen" der autocontent_article-Funktion gedacht !!!
	 */
	function helper_content_delete_article()
	{
		$sql = sprintf("TRUNCATE TABLE %s", $this->db_praefix."papoo_repore");
		$this->db->query($sql);

		$sql = sprintf("TRUNCATE TABLE %s", $this->db_praefix."papoo_language_article");
		$this->db->query($sql);

		$sql = sprintf("TRUNCATE TABLE %s", $this->db_praefix."papoo_lookup_art_cat");
		$this->db->query($sql);

		$sql = sprintf("TRUNCATE TABLE %s", $this->db_praefix."papoo_lookup_article");
		$this->db->query($sql);

		$sql = sprintf("TRUNCATE TABLE %s", $this->db_praefix."papoo_lookup_write_article");
		$this->db->query($sql);
	}
}

$autocontent = new autocontent_class();
