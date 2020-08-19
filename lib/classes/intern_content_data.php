<?php
/**
#####################################
# papoo Version 3.0                 #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.3                  #
#####################################
 */

/**
 * Class content_data
 */
class content_data
{
	/**
	 * content_data constructor.
	 */
	function __construct() {
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
		// Suchklasse einbinden
		global $searcher;
		// checkedblen Klasse einbinde
		global $checked;
		//Diverse Klasse einbinden
		global $diverse;
		global $menu;

		global $sitemap;
		$this->sitemap=&$sitemap;

		/**
		und einbinden in die Klasse
		 */

		// Hier die Klassen als Referenzen
		$this->cms = & $cms;
		$this->db = & $db;
		$this->content = & $content;
		$this->message = & $message;
		$this->user = & $user;
		$this->menu = & $menu;
		$this->weiter = & $weiter;
		$this->searcher = & $searcher;
		$this->checked = & $checked;
		$this->diverse = & $diverse;

	}

	/**
	 *
	 */
	function do_inhalt()
	{
		$this->content->template['plugin']="";

		if ($this->checked->menuid==54) {
			$this->content->template['plugin']="ok";

			if ($this->menu->data_back['3']['menuid']==47) {
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: ./plugins.php?menuid=47");
				header("Connection: close");
				exit();
			}
		}

		if ($this->checked->menuid==55) {
			$this->content->template['inhalte']="ok";
			$this->make_baum();
		}
		if ($this->checked->menuid==56) {
			$this->content->template['system']="ok";
			$this->content->template['plugin']="";

			foreach ($this->menu->data_back_complete as $k=>$v) {
				if ($v['menuid']==18) {
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: ./stamm.php?menuid=18");
					header("Connection: close");
					exit();
				}
			}

		}
		if ($this->checked->menuid==58 ){
			$this->content->template['medien']="ok";

		}

		if ($this->checked->menuid==57) {
			$this->content->template['gruppen']="ok";
		}

		if ($this->checked->menuid==81) {
			$this->content->template['layout']="ok";

			foreach ($this->menu->data_back_complete as $k=>$v) {
				if ($v['menuid']==22) {
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: ./styles.php?menuid=22");
					header("Connection: close");
					exit();
				}
			}
		}
	}

	/**
	 * Direkt Navi Baum
	 */
	function make_baum()
	{
		$this->sitemap->make_sitemap($_SESSION['langid_front'], "intern_publish");

		if (!empty($this->cms->tbname['plugin_shop_kategorien_lookup'])) {
			//shop_class_sitemap
			if (file_exists(PAPOO_ABS_PFAD."/plugins/papoo_shop/lib/shop_class_sitemap.php")) {
				require_once PAPOO_ABS_PFAD."/plugins/papoo_shop/lib/shop_class_sitemap.php";
				$shop_map=new shop_class_sitemap();
				$shop_map->do_shop_sitemap();
			}
		}
		$sql=sprintf("SELECT menuid FROM %s
						WHERE menulink LIKE '%s'",
			$this->cms->tbname['papoo_menuint'],
			"%papoo_shop_produkte.html%"
		);
		$this->content->template['shop_produkt_menuid']=$this->db->get_var($sql);
		$this->content->template['table_data_site']=$this->content->template['table_data'];
		$this->content->template['table_data']="";
		$this->content->template['plugin']="";
	}
}

$content_data = new content_data();