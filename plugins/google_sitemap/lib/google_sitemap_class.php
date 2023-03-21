<?php

/**
#####################################
# Papoo CMS                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
#                                   #
#####################################
# PHP Version >4.3                  #
#####################################
 */

/**
 * Class google_sitemap
 */
#[AllowDynamicProperties]
class google_sitemap
{
	/** @var string  */
	var $news = "";

	/**
	 * google_sitemap constructor.
	 */
	function __construct()
	{
		// Klassen globalisieren

		global $cms, $db, $message, $content, $checked, $sitemap, $intern_menu, $menu, $diverse, $user, $priority, $frequence, $data_sitemap;
		$this->cms = & $cms;
		$this->db = & $db;
		$this->message = & $message;
		$this->content = & $content;
		$this->checked = & $checked;
		$this->sitemap = & $sitemap;
		$this->intern_menu = & $intern_menu;
		$this->menu = & $menu;

		// Session einbinden
		//global $session;
		//$this->session = & $session;

		$this->diverse = & $diverse;
		$this->user = & $user;

		$this->pfadhier = PAPOO_ABS_PFAD;

		$this->priority = & $priority;
		$this->frequence = & $frequence;
		$this->date_sitemap = & $date_sitemap;

		$this->webverzeichnis=PAPOO_WEB_PFAD;

		//Wenn Admin dann durchf�hren
		$this->make_google_sitemap();
	}

	/**
	 * Google Sitemap erstellen
	 */
	function make_google_sitemap()
	{
		if (defined("admin")) {
			$this->user->check_intern();

			global $template;
			$templatedat=basename($template);
			if ($templatedat != "login.utf8.html") {
				IfNotSetNull($this->checked->template);
				switch ($this->checked->template) {
					// Die Standardeinstellungen werden bearbeitet
					case "google_sitemap/templates/google_sitemap_edit.html" :
						$this->google_edit();
						break;

					// Einen Eintrag erstellen
					case "google_sitemap/templates/google_sitemap_create.html" :
						$this->do_cache();
						break;

					default :
						break;
				}
			}
		}

	}

	/**
	 * Sitemap erstellen
	 */
	function do_cache()
	{
		//Name der Sitemap
		$dateiname = "sitemap.xml";

		$this->date_sitemap=date("Y-m-d",time());
		$sql = sprintf("SELECT * FROM %s WHERE google_sitemap_id='1'", $this->cms->tbname['papoo_google_sitemap']);
		$result=$this->db->get_results($sql);
		foreach($result as $treffer) {
			$this->frequence=$treffer->changefreq;
			$this->priority=$treffer->priority;
		}
		$slash = "/";
		$ordner_pfad = $this->pfadhier . $slash;
		$datei_pfad = $ordner_pfad . $dateiname;

		//Sprachen raussuchen die aktiv sind
		$sql = sprintf("SELECT * FROM %s WHERE more_lang='2'", $this->cms->tbname['papoo_name_language']);
		$result = $this->db->get_results($sql);

		$sprachen = array ();
		if (!empty($result)) {
			foreach ($result as $treffer) {
				array_push($sprachen, $treffer->lang_short);
			}
		}

		//Einstellungen auslesen
		if ($this->cms->mod_surls!=1 && $this->cms->mod_free!=1) {

			//links f�r die Men�punkte erstellen
			$this->do_cache_menu();
			//links f�r die Artikel erstellen
			$this->do_cache_artikel();
		}
		else {
			$this->do_sitemap_menu();
			$this->get_all_artikels();
		}

		$this->content->template['artikel']=str_replace("deen","de/en",$this->content->template['artikel']);
		$this->content->template['sitemap'] = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"https://www.google.com/schemas/sitemap/0.9\">" .
			$this->content->template['menu'] . $this->content->template['artikel'] . "\n</urlset>";

		// Herausfiltern ob https oder http verwendet werden soll
		if (isset($_SERVER['HTTPS'])) {
			$this->content->template['sitemap'] = str_replace("http://", "https://", $this->content->template['sitemap']);
			$this->content->template['link'] = "https://" . $this->cms->title_send.$this->webverzeichnis. "/" . $dateiname;
		}
		else {
			$this->content->template['link'] = "http://" . $this->cms->title_send.$this->webverzeichnis. "/" . $dateiname;
		}
		// Datei sitemap_test.php erstellen
		if (file_exists($datei_pfad)) {
			if (is_writable($datei_pfad)) {
				$this->diverse->write_to_file($slash.$dateiname, $this->content->template['sitemap']);
			}
			else {
				@chmod($this->pfadhier . $slash, 0777);

				if (is_writable($datei_pfad)) {
					$this->diverse->write_to_file($dateiname, $this->content->template['sitemap']);
				}
				else {
					$this->content->template['sitemap'] = "";
					$this->content->template['error'] = $this->content->template['plugin']['google_sitemap']['datei'] . $datei_pfad .
						$this->content->template['plugin']['google_sitemap']['datei2'];
				}
			}
		}
		else {
			@chmod($this->pfadhier . $slash, 0777);

			@$this->diverse->write_to_file("/".$dateiname, $this->content->template['sitemap']);
			if (file_exists($datei_pfad)) {
				$this->content->template['error'] = $this->content->template['plugin']['google_sitemap']['gespeichert'];
			}
			else {
				$this->content->template['sitemap'] = "";
				$this->content->template['error'] = $this->content->template['plugin']['google_sitemap']['ordner'] . $ordner_pfad .
					$this->content->template['plugin']['google_sitemap']['ordner2'];
			}
		}
	}

	/**
	 * @param int $langid
	 * @return array|void
	 */
	private function get_all_artikel_free($langid=1)
	{
		$sql=sprintf("SELECT lan_repore_id, url_header FROM %s WHERE lang_id='%d' ",
			DB_PRAEFIX."papoo_language_article",
			$langid);
		$data=$this->db->get_results($sql,ARRAY_A);
		return $data;
	}

	/**
	 * Alle Artikel cachen
	 */
	function do_cache_artikel()
	{
		$mod_rewrite = $this->cms->mod_rewrite;
		$sql = sprintf("SELECT * FROM %s,%s,%s,%s WHERE reporeID=lan_repore_id AND  reporeID=article_id AND gruppeid_id='10' AND publish_yn='1' AND " .
			$this->cms->tbname['papoo_language_article'] . ".lang_id=" . $this->cms->tbname['papoo_name_language'] . ".lang_id",
			$this->cms->tbname['papoo_repore'],
			$this->cms->tbname['papoo_language_article'],
			$this->cms->tbname['papoo_lookup_article'],
			$this->cms->tbname['papoo_name_language']
		);
		$result = $this->db->get_results($sql);

		foreach ($result as $treffer) {
			if ($mod_rewrite == 2) {
				$this->content->template['artikel'] .= "\n\t<url>\n\t\t<loc>http://" . $this->cms->title_send.$this->webverzeichnis. "/index/menuid/" .
					$treffer->cattextid . "/reporeid/" . $treffer->reporeID . "/getlang/" . $treffer->lang_short . "</loc>\n\t\t<lastmod>".$this->date_sitemap.
					"</lastmod>\n\t\t<changefreq>".$this->frequence."</changefreq>\n\t\t<priority>".$this->priority."</priority>\n\t</url>";
			}
			else {
				$this->content->template['artikel'] .= "\n\t<url>\n\t\t<loc>http://" . $this->cms->title_send.$this->webverzeichnis. "/index.php?getlang=" .
					$treffer->lang_short . "&amp;menuid=" . $treffer->cattextid . "&amp;reporeid=" . $treffer->reporeID . "</loc>\n\t\t<lastmod>".$this->date_sitemap.
					"</lastmod>\n\t\t<changefreq>".$this->frequence."</changefreq>\n\t\t<priority>".$this->priority."</priority>\n\t</url>";
			}
		}
	}

	/**
	 * Alle Men�punkte cachen
	 */
	function do_cache_menu()
	{
		//Alle offenen Men�punkte raussuchen
		$sql = "SELECT distinct * FROM " . $this->cms->tbname['papoo_me_nu'] . "," . $this->cms->tbname['papoo_lookup_me_all_ext'] . "," .
			$this->cms->tbname['papoo_name_language'] . "," . $this->cms->tbname['papoo_daten'] .
			" WHERE menuid=menuid_id AND gruppeid_id='10' AND (more_lang='2' OR lang_short=lang_frontend) AND menulink NOT LIKE '%visilex.php%'";
		$result = $this->db->get_results($sql);
		$mod_rewrite = $this->cms->mod_rewrite;
		foreach ($result as $treffer) {
			$is_http = false;
			$is_plugin = false;
			$is_title = false;
			$the_template = "";
			$treffer->menulink = htmlentities($treffer->menulink);
			// c.1. Plugin-Links
			if (strpos("XXX_" . $treffer->menulink, "plugin:")) {
				$is_plugin = true;
				$the_template = str_replace("plugin:", "", $treffer->menulink);
				$treffer->menulink = "plugin.php?getlang=" . $treffer->lang_short . "&amp;menu_id=" . $treffer->menuid_id;
			}
			if (strpos("XXX_" . $treffer->menulink, "http://")) {
				$is_http = true;
				if (strpos($treffer->menulink, $this->cms->title_send))
				{
					$is_title = true;
				}
				//$treffer->menulink=str_replace("www.", "", $treffer->menulink);
			}

			if (!$is_http && !$is_plugin) {
				if ($mod_rewrite == 2) {
					$treffer->menulink = html_entity_decode($treffer->menulink);
					$treffer->menulink = str_ireplace(".php", "", $treffer->menulink);
					$this->content->template['menu'] .= "\n\t<url>\n\t\t<loc>http://" . $this->cms->title_send.$this->webverzeichnis. "/" .
						$treffer->menulink . "/menu_id/" . $treffer->menuid_id . "/getlang/" . $treffer->lang_short . "</loc>\n\t\t<lastmod>".
						$this->date_sitemap."</lastmod>\n\t\t<changefreq>".$this->frequence."</changefreq>\n\t\t<priority>".$this->priority."</priority>\n\t</url>";
				}
				else {
					$this->content->template['menu'] .= "\n\t<url>\n\t\t<loc>http://" . $this->cms->title_send.$this->webverzeichnis. "/" .
						$treffer->menulink . "?getlang=" . $treffer->lang_short . "&amp;menu_id=" . $treffer->menuid_id . "</loc>\n\t\t<lastmod>".
						$this->date_sitemap."</lastmod>\n\t\t<changefreq>".$this->frequence."</changefreq>\n\t\t<priority>".$this->priority."</priority>\n\t</url>";
				}
			}
			elseif (!$is_http && $is_plugin) {
				if ($mod_rewrite == 2) {
					$the_template = str_replace("/", ":::", $the_template);
					$treffer->menulink = $treffer->menulink . "/template/" . $the_template;
					$treffer->menulink = str_ireplace("&amp;", "&", $treffer->menulink);
					$treffer->menulink = str_ireplace(".php", "", $treffer->menulink);
					$treffer->menulink = str_ireplace("[?=&]", "/", $treffer->menulink);
				}
				else {
					if (empty ($defined))
						$defined = "";
					$the_template = PAPOO_ABS_PFAD . "plugins/" . $the_template;
					if ($defined == "yes")
						$the_template = "../" . $the_template;
					$menupunkt["template"] = "&amp;template=../" . $the_template;
					$treffer->menulink .= $menupunkt["template"];
				}
				$this->content->template['menu'] .= "\n\t<url>\n\t\t<loc>http://" . $this->cms->title_send.$this->webverzeichnis. "/" . $treffer->menulink .
					"</loc>\n\t\t<lastmod>".$this->date_sitemap."</lastmod>\n\t\t<changefreq>".$this->frequence."</changefreq>\n\t\t<priority>".
					$this->priority."</priority>\n\t</url>";
			}
			elseif ($is_http && !$is_title) {
				if ($mod_rewrite == 2) {
					$treffer->menulink = $this->mod_rewrite_replace($treffer->menulink);
				}
				$this->content->template['menu'] .= "\n\t<url>\n\t\t<loc>" . $treffer->menulink . "</loc>\n\t\t<lastmod>".$this->date_sitemap.
					"</lastmod>\n\t\t<changefreq>".$this->frequence."</changefreq>\n\t\t<priority>".$this->priority."</priority>\n\t</url>";
			}
			else {
				$treffer->menulink = str_replace("(.*)(" . $this->cms->title_send.$this->webverzeichnis. ")(.*)", "http://" .
					$this->cms->title_send.$this->webverzeichnis. "\\3", $treffer->menulink);
				if ($mod_rewrite == 2) {
					$treffer->menulink = $this->mod_rewrite_replace($treffer->menulink);
				}
				//$treffer->menulink=str_replace($this->cms->title_send, "", $treffer->menulink);
				$this->content->template['menu'] .= "\n\t<url>\n\t\t<loc>" . $treffer->menulink . "</loc>\n\t\t<lastmod>".$this->date_sitemap.
					"</lastmod>\n\t\t<changefreq>".$this->frequence."</changefreq>\n\t\t<priority>".$this->priority."</priority>\n\t</url>";
			}
			// $this->content->template['menu']=str_replace("&amp;","&",$this->content->template['menu']);
		}

	}
	function do_sitemap_menu()
	{
		$eskuel = "SELECT lang_id, lang_short, lang_long FROM " . $this->cms->papoo_name_language . " WHERE more_lang='2'";
		$reslang = $this->db->get_results($eskuel);
		foreach ($reslang as $langdat) {
			$userid=$this->user->userid;
			$this->user->userid=11;
			if (defined("admin")) {
				$this->menu->data_front_complete =$this->menu->menu_data_read("FRONT",$langdat->lang_id);
			}
			$menudat=$this->menu->menu_navigation_build("FRONT_COMPLETE");
			$this->menu->data_front_complete = $this->menu->menu_data_read("FRONT_NAME",$langdat->lang_id);
			$menudat = $this->menu->menu_navigation_build("FRONT_COMPLETE", "CLEAN");

			global $md;
			$md = $this->make_url_dir($menudat);

			/**
			 * Alle Men�punkte rausfischen
			 */
			$export = array();
			$i = 0;
			if($menudat) {
				foreach ($menudat as $link) {
					$i++;
					//sprechende urls
					if ($this->cms->mod_surls == 1 || $this->cms->mod_free==1) {
						$m_url = array();
						global $m_url;
						$m_url = array();

						global $m_url2;
						$m_url2 = array();
						$urldat_12="";
						$this->get_verzeichnis($link['menuid']);

						$m_url = array_reverse($m_url);
						$m_url2 = array_reverse($m_url2);
						$urldat_1=$urldat_11=$gl_menuname2=$gl_menuname="";

						if (!empty($m_url)) {
							foreach ($m_url as $urldat) {
								$urldat_1 .= $urldat . "/";
							}
						}
						$gl_menuname = $urldat_1;
						if (!empty($m_url2)) {
							foreach ($m_url2 as $urldat2) {
								$urldat_12 .= strip_tags($urldat2) . "/";
							}
						}
						$gl_menuname2 = $urldat_12;

						if ($langdat->lang_short!="de") {
							$short="/".$langdat->lang_short;
						}
						else {
							$short="";
						}

						$this->export_menu[$link['menuid']]="/".$this->cms->webvar."".$gl_menuname2;

						IfNotSetNull($this->content->template['menu']);

						if ($this->cms->mod_free == 1) {
							$export_menuname = 'http://'.$this->cms->title_send.PAPOO_WEB_PFAD . '' . $short. $link['url_menuname'];
							$this->content->template['menu'] .= "\n\t<url>\n\t\t<loc>" . $export_menuname. "</loc>\n\t\t<lastmod>".$this->date_sitemap.
								"</lastmod>\n\t\t<changefreq>".$this->frequence."</changefreq>\n\t\t<priority>".$this->priority."</priority>\n\t</url>";
						}
						else {
							$export_menuname = 'http://'.$this->cms->title_send.PAPOO_WEB_PFAD .  $short.'/'.$this->cms->webvar . '' . $gl_menuname;
							$this->content->template['menu'] .= "\n\t<url>\n\t\t<loc>" . $export_menuname. "</loc>\n\t\t<lastmod>".$this->date_sitemap.
								"</lastmod>\n\t\t<changefreq>".$this->frequence."</changefreq>\n\t\t<priority>".$this->priority."</priority>\n\t</url>";
						}
					}
				}
			}
		}
		IfNotSetNull($userid);
		IfNotSetNull($export);

		$this->user->userid=$userid;
		$this->content->template['quicklist']=$export;
		$this->cms->mod_rewrite=2;
	}

	/**
	 * Verzeichnisse rauskriegen bzw. Men�namen f�r mod_rewrite mit sprechurls
	 *
	 * @param string $menid
	 */
	function get_verzeichnis($menid = "")
	{
		global $md;

		$this->get_urls($md,$menid);
	}

	/**
	 * @param $md
	 * @param $menid
	 */
	function get_urls($md, $menid)
	{
		global $m_url;
		global $m_url2;
		if ($md[$menid]['untermenuzu']!=0) {
			$m_url[]= $md[$menid]['url_menuname'];
			$m_url2[]= $md[$menid]['menuname'];
			$this->get_urls($md,$md[$menid]['untermenuzu']);

		}
		else {
			$m_url[]= $md[$menid]['url_menuname'];
			$m_url2[]= $md[$menid]['menuname'];
		}
	}

	/**
	 * @param $menudat
	 * @return array
	 */
	function make_url_dir($menudat)
	{
		$daten = [];
		if($menudat) {
			foreach ($menudat as $menuitems) {
				$daten[$menuitems['menuid']]=$menuitems;
			}
		}
		return $daten;
	}

	/***
	 * Die Daten editieren
	 */
	function google_edit()
	{
		if (isset($this->checked->submitentry) && $this->checked->submitentry) {
			$sql = sprintf("SELECT * FROM %s WHERE google_sitemap_id='1'", $this->cms->tbname['papoo_google_sitemap']);
			if($this->db->query($sql)) {
				$sql = sprintf("UPDATE %s SET google_sitemap_id='1', datum='%s', changefreq='%s', priority='%s' WHERE google_sitemap_id='1'",
					$this->cms->tbname['papoo_google_sitemap'],
					$this->db->escape(time()),
					$this->db->escape($this->checked->changefreq),
					$this->db->escape($this->checked->priority)
				);
				$this->db->query($sql);
			}
			else {
				$sql = sprintf("INSERT INTO %s SET google_sitemap_id='1', datum='%s', changefreq='%s', priority='%s'",
					$this->cms->tbname['papoo_google_sitemap'],
					$this->db->escape(time()),
					$this->db->escape($this->checked->changefreq),
					$this->db->escape($this->checked->priority)
				);
				$this->db->query($sql);
			}
			$this->content->template['ausgabe'] = $this->content->template['plugin']['google_sitemap']['geaendert'];
			$this->content->template['changefreq'] = $this->checked->changefreq;
			$this->content->template['priority'] = $this->checked->priority;
		}
	}

	/**
	 * @param $url
	 * @return mixed|string|string[]|null
	 */
	function mod_rewrite_replace($url)
	{
		//$url = str_ireplace("&amp;", "&", $url);
		$url = html_entity_decode($url);
		$url = str_ireplace(".php", "", $url);
		$url = str_ireplace("[?=&]", "/", $url);
		return $url;
	}

	/**
	 * Linkliste 2
	 * Alle Artikel als Links
	 */
	function get_all_artikels()
	{

		$eskuel = "SELECT lang_id, lang_short, lang_long FROM " . $this->cms->papoo_name_language . " WHERE more_lang='2'";
		$reslang = $this->db->get_results($eskuel);
		foreach ($reslang as $langdat) {
			$this->menu->data_front_complete = $this->menu->menu_data_read("FRONT_NAME");
			$menudat = $this->menu->menu_navigation_build("FRONT_COMPLETE", "CLEAN");
			#$menudat2 = $menu->menu_navigation_build("FRONT_COMPLETE");

			$sprach_id=$langdat->lang_id;
			global $md;
			$md = $this->make_url_dir($menudat);
			//Alle Artikel rausfischen
			$sql = " SELECT DISTINCT(reporeID), lcat_id, header, url_header ";
			$sql .= " FROM " . $this->cms->papoo_repore . "," . $this->cms->papoo_language_article .
				", " . $this->cms->tbname['papoo_lookup_art_cat'] . ", " . $this->cms->tbname['papoo_lookup_article'] . "";
			$sql .= " WHERE reporeID=lan_repore_id AND lart_id = reporeID AND reporeID=article_id";
			$sql .= " AND lang_id='" . $sprach_id . "'";
			$sql .= " AND publish_yn='1'";
			$sql .= " AND gruppeid_id='10'";
			$sql .= " GROUP BY reporeID ORDER BY header ASC";
			$result = $this->db->get_results($sql);
			$i = 0;
			#global $sitemap;
			#$sitemap->make_sitemap(1);

			if (is_array($result)) {
				foreach ($result as $link) {
					$i++;
					//sprechende urls
					if ($this->cms->mod_surls == 1 || $this->cms->mod_free==1) {
						$m_url = array();
						global $m_url;
						$m_url = array();

						global $m_url2;
						$m_url2 = array();
						$urldat_1 = "";
						$urldat_12="";
						global $gl_menuname;
						$gl_menuname = "";
						$this->get_verzeichnis($link->lcat_id);


						$m_url = array_reverse($m_url);
						$m_url2 = array_reverse($m_url2);

						if (!empty($m_url)) {
							foreach ($m_url as $urldat) {
								$urldat_1 .= $urldat . "/";
							}
						}
						$header_url = $urldat_1 . (($link->url_header)) . ".html";
						if (!empty($m_url2)) {
							foreach ($m_url2 as $urldat2) {
								$urldat_12 .= trim(strip_tags($urldat2)) . "/";
							}
						}
						$gl_menuname2 = (trim($urldat_12));

						$gl_menuname2=$this->menu->urlencode($gl_menuname2);
						$link->header=$this->menu->urlencode($link->header);

						//$export.= '[" '.htmlspecialchars($link->header).' ", "/index/menuid/'.$link->lcat_id.'/reporeid/'.$link->reporeID.'"]';

						if ($langdat->lang_short!="de") {
							$short=$langdat->lang_short;
						}
						else {
							$short="";
						}

						if ($this->cms->mod_free==1) {
							$export = $this->cms->title_send.PAPOO_WEB_PFAD . '' .$short. $link->url_header ;
							$this->content->template['artikel'] .= "\n\t<url>\n\t\t<loc>http://" .$export . "</loc>\n\t\t<lastmod>".$this->date_sitemap.
								"</lastmod>\n\t\t<changefreq>".$this->frequence."</changefreq>\n\t\t<priority>".$this->priority."</priority>\n\t</url>";
						}
						else {
							$export = $this->cms->title_send.PAPOO_WEB_PFAD . '/' .$short. $this->cms->webvar . $header_url ;
							$this->content->template['artikel'] .= "\n\t<url>\n\t\t<loc>http://" .$export . "</loc>\n\t\t<lastmod>".$this->date_sitemap.
								"</lastmod>\n\t\t<changefreq>".$this->frequence."</changefreq>\n\t\t<priority>".$this->priority."</priority>\n\t</url>";
						}
					}
				}
			}
		}
	}
}

$google_sitemap = new google_sitemap();
