<?php

/**
 * Class freeplugin_class
 */
#[AllowDynamicProperties]
class freeplugin_class
{
	//Speichert mit der ID der Sprache als Index das Sprachkürzel
	private $languages;
	//Speichert mit der URL als Index die Sprach-ID
	private $article_url_has_lang_id;
	//Speichert mit der Artikel-ID als Index die URL
	private $article_id_has_url;
	//Speichert mit der URL als Index die Sprach-ID
	private $menu_url_has_lang_id;
	//Speichert mit der Artikel-ID als Index die URL
	private $menu_id_has_url;

	private $sprechende_urls;
	private $artMultiAssoc;

	private function showErrors()
	{
		ini_set("display_errors", 1);
		error_reporting(-1);
	}

	/**
	 * freeplugin_class constructor.
	 */
	function __construct()
	{
		global $content, $user, $checked, $db, $cms, $menu;
		$this->content = & $content;
		$this->user = & $user;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->cms = & $cms;
		$this->menu = & $menu;

		/*
		// Einbindung der Modul-Klasse
		global $module;
		$this->module = & $module;
		//print_r($this->module->module_aktiv);
		*/

		if (defined("admin")) {
			$this->user->check_intern();
			global $template;

			if (strpos("XXX" . $template, "free_back.html")) {
				//Hole mehrfach-zugeordnete ArtikelIDs
				$this->get_multiple_assoc();
				//Hole Papoo-Sprachen und Artikelspracheinträge-IDs
				$this->get_papoo_languages();
				//Einstellungen �berabeiten
				$this->start_free_urls();
			}
		}
	}

	/**
	 * @param $str
	 * @param string $prefix
	 * @param string $suffix
	 * @return string
	 */
	private function sprechende_url($str, $prefix = "", $suffix = "")
	{
		$search  = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "´");
		$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "");

		$url = str_replace($search, $replace, $str);

		$url = strtolower($url);

		#$url = preg_replace("/^\/*/i", "", $url);
		#$url = preg_replace("/\/*$/i", "", $url);
		$url = preg_replace("/<[^>]*?>[^<]*?<\/[^>]*?>/", "", $url);
		$url = preg_replace("/^[^a-z0-9]*/i", "", $url);
		$url = preg_replace("/\.html*$/i", "", $url);
		$url = preg_replace("/[^a-z0-9-]/i", "-", $url);
		$url = preg_replace("/-{2,}/", "-", $url);
		$url = preg_replace("/^-*/", "", $url);
		$url = preg_replace("/-*$/", "", $url);

		return $prefix . $url . $suffix;
	}

	/**
	 * @param $str
	 * @param bool $isMenu
	 * @return bool
	 */
	private function sprechende_url_doppelt($str, $isMenu = false)
	{
		#$url = $this->sprechende_url($str);
		$url = $str;

		$count = 0;
		if($isMenu) {
			foreach($this->sprechende_urls['menu'] as $val) {
				if(strcmp($url, $val) == 0) {
					$count++;
				}
			}
		}
		else {
			foreach($this->sprechende_urls['article'] as $val) {
				if(strcmp($url, $val) == 0) {
					$count++;
				}
			}
		}
		return ($count > 1) ? true : false;
	}

	private function get_multiple_assoc()
	{
		$this->artMultiAssoc = array();
		//Hole Papoo Systemsprachen.
		$sql=sprintf("SELECT `lart_id`, COUNT(`lart_id`) AS amount FROM `%s` GROUP BY `lart_id` HAVING amount > 1;",
			$this->cms->tbname['papoo_lookup_art_cat']
		);
		$result = $this->db->get_results($sql,ARRAY_A);

		foreach ($result as $row) {
			$this->artMultiAssoc[$row['lart_id']] = true;
		}
	}

	private function get_papoo_languages()
	{
		$this->sprechende_urls = array();
		$this->sprechende_urls['menu'] = array();
		$this->sprechende_urls['article'] = array();

		//Hole Standardsprache
		$sql=sprintf("SELECT `lang_frontend` FROM `%s`;",
			$this->cms->tbname['papoo_daten']
		);
		$this->languages['default'] = $this->db->get_var($sql);

		//Hole Papoo Systemsprachen.
		$sql=sprintf("SELECT lang_id, lang_short FROM %s",
			$this->cms->tbname['papoo_name_language']
		);
		$result = $this->db->get_results($sql,ARRAY_A);

		foreach ($result as $row) {
			$this->languages[$row['lang_id']] = $row['lang_short'];
		}

		//Sprechende URLs der Artikel generieren und zwischenspeichern
		$sql=sprintf("SELECT `lan_repore_id`, `lang_id`, `header` FROM `%s` ORDER BY `lan_repore_id`, `lang_id`;",
			$this->cms->tbname['papoo_language_article']
		);
		$result = $this->db->get_results($sql,ARRAY_A);

		$currentid=-1;
		$currenturls=array();
		foreach ($result as $row) {
			$url = $this->sprechende_url($row['header']);
			if($currentid == $row['lan_repore_id']) {
				//url vorhanden
				$exists = false;
				foreach($currenturls as $val) {
					if(strcmp($url, $val) == 0) {
						$exists = true;
					}
				}
				if(!$exists) {
					$this->sprechende_urls['article'][] = $url;
					$currenturls[] = $url;
				}
			}
			else {
				$currenturls=array();
				$currenturls[] = $url;
				$currentid = $row['lan_repore_id'];
				$this->sprechende_urls['article'][] = $url;
				#$this->article_url_has_lang_id[$url] = $row['lang_id'];
			}
			//Speichert URL (auch doppelte) und bekommt dann später ggf. ein Kürzel angehängt.
			$this->article_id_has_url[$row['lan_repore_id'].'-'.$row['lang_id']] = $url;
		}

		//Hole Menü Spracheinträge-IDs und URLs
		$sql=sprintf("SELECT `menuid_id`, `lang_id`, `menuname` FROM `%s` ORDER BY `menuid_id`, `lang_id`;",
			$this->cms->tbname['papoo_menu_language']
		);
		$result = $this->db->get_results($sql,ARRAY_A);

		$currentid=-1;
		$currenturls=array();
		foreach ($result as $row) {
			$url = $this->sprechende_url($row['menuname']);
			if($currentid == $row['menuid_id']) {
				//url vorhanden
				$exists = false;
				foreach($currenturls as $val) {
					if(strcmp($url, $val) == 0)
					{
						$exists = true;
					}
				}
				if(!$exists) {
					$this->sprechende_urls['menu'][] = $url;
					$currenturls[] = $url;
				}
			}
			else {
				$currenturls=array();
				$currenturls[] = $url;
				$currentid = $row['menuid_id'];
				$this->sprechende_urls['menu'][] = $url;
				#$this->menu_url_has_lang_id[$url] = $row['lang_id'];
			}
			$this->menu_id_has_url[$row['menuid_id'].'-'.$row['lang_id']] = $url;
		}
	}

	private function edit_links_in_articles()
	{
		$menus    = array();
		$articles = array();

		//Hole Menüeinträge
		$sql=sprintf("SELECT * FROM %s;",
			$this->cms->tbname['papoo_menu_language']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (is_array($result)) {
			foreach ($result as $value) {
				$menus[$value['menuid_id'].'-'.$value['lang_id']] = $value;
			}
		}

		//Alle Artikel rausholen
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_language_article']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (is_array($result)) {
			foreach ($result as $value) {
				$articles[$value['lan_repore_id'].'-'.$value['lang_id']] = $value;
			}
			foreach ($result as $value) {
				//Links im Artikel bearbeiten
				$text = $value['lan_article'];
				$langid = $value['lang_id'];
				preg_match_all("/<a[^>]*?href=\"([^\"]*?)\"/", $text, $matches);
				foreach($matches[1] as $match) {
					if(strpos($match, "http") !== false) { continue; }
					//Kein mod_rewrite-Link.
					if(strpos($match, "index.php") !== false) {
						$url = "";
						if(preg_match("/menuid=([\d]+)/", $match, $idmatch)) {
							$menuid = $idmatch[1];
							$mIndex = $menuid.'-'.$langid;
							if(isset($menus[$mIndex])) {
								$url = $menus[$mIndex]['url_menuname'];
							}
						}
						if(preg_match("/reporeid=([\d]+)/", $match, $idmatch)) {
							$reporeid = $idmatch[1];
							$aIndex = $reporeid.'-'.$langid;
							if(isset($articles[$aIndex])) {
								$url = $articles[$aIndex]['url_header'];
								if($this->artMultiAssoc[$reporeid]) {
									$url = substr_replace($url, "-m-" . $menuid . ".html", -5);
								}
							}
						}
						if($url) {
							$text = str_replace($match, PAPOO_WEB_PFAD . $url, $text);
						}
					}
				}

				if(strcmp($text, $value['lan_article']) != 0) {
					$sql = sprintf("UPDATE %s SET lan_article='%s', lan_article_sans='%s' WHERE lan_repore_id=%d AND lang_id=%d",
						$this->cms->tbname['papoo_language_article'],
						$this->db->escape($text),
						$this->db->escape($text),
						$value['lan_repore_id'],
						$langid
					);
					$this->db->query($sql);
				}
			}
		}

		//STARTSEITE
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_language_stamm']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//Durchgehen und updaten
		if (is_array($result)) {
			foreach ($result as $value) {
				//Links im Artikel bearbeiten
				$text = $value['start_text'];
				$langid = $value['lang_id'];
				preg_match_all("/<a[^>]*?href=\"([^\"]*?)\"/", $text, $matches);
				foreach($matches[1] as $match)
				{
					if(strpos($match, "http") !== false) { continue; }
					//Kein mod_rewrite-Link.
					if(strpos($match, "index.php") !== false) {
						$url = "";
						if(preg_match("/menuid=([\d]+)/", $match, $idmatch)) {
							$menuid = $idmatch[1];
							$mIndex = $menuid.'-'.$langid;
							if(isset($menus[$mIndex])) {
								$url = $menus[$mIndex]['url_menuname'];
							}
						}
						if(preg_match("/reporeid=([\d]+)/", $match, $idmatch)) {
							$reporeid = $idmatch[1];
							$aIndex = $reporeid.'-'.$langid;
							if(isset($articles[$aIndex])) {
								$url = $articles[$aIndex]['url_header'];
								if($this->artMultiAssoc[$reporeid]) {
									$url = substr_replace($url, "-m-" . $menuid . ".html", -5);
								}
							}
						}
						if($url) {
							$text = str_replace($match, PAPOO_WEB_PFAD . $url, $text);
						}
					}
				}

				if(strcmp($text, $value['start_text']) != 0) {
					$sql = sprintf("UPDATE %s SET start_text='%s', start_text_sans='%s' WHERE stamm_id=%d AND lang_id=%d",
						$this->cms->tbname['papoo_language_stamm'],
						$this->db->escape($text),
						$this->db->escape($text),
						$value['stamm_id'],
						$langid
					);
					$this->db->query($sql);
				}
			}
		}
	}

	function do_sitemap_menu()
	{
		#$this->cms->mod_rewrite=1;
		$eskuel = "SELECT lang_id, lang_short, lang_long FROM " . $this->cms->papoo_name_language . " WHERE more_lang='2'";
		$reslang = $this->db->get_results($eskuel);
		foreach ($reslang as $langdat) {
			if (defined("admin")) {
				$this->menu->data_front_complete =$this->menu->menu_data_read("FRONT",$langdat->lang_id);
			}
			$menudat=$this->menu->menu_navigation_build("FRONT_COMPLETE");
			$this->menu->data_front_complete = $this->menu->menu_data_read("FRONT_NAME",$langdat->lang_id);
			$menudat = $this->menu->menu_navigation_build("FRONT_COMPLETE", "CLEAN");

			global $md;
			$md = $this->make_url_dir($menudat);
			$i = 0;
			// Alle Men�punkte rausfischen

			$count = count($menudat);
			$export = array();
			$i = 0;
			foreach ($menudat as $link) {
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

					$this->export_menu[$link['menuid']]="/".$this->cms->webvar."".$gl_menuname;
				}

				if ($i < $count) {
					#$export .= ",
					#";
				}
			}
		}
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
	 * @return mixed
	 */
	function make_url_dir($menudat)
	{
		global $suurls;
		foreach ($menudat as $menuitems) {
			$daten[$menuitems['menuid']]=$menuitems;
		}
		return $daten;
	}

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
			// ANzahl der Eintr�ge
			$count = count($result);
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
						//�
						$gl_menuname2=$this->menu->urlencode($gl_menuname2);
						$link->header=$this->menu->urlencode($link->header);

						//$export.= '[" '.htmlspecialchars($link->header).' ", "/index/menuid/'.$link->lcat_id.'/reporeid/'.$link->reporeID.'"]';

						if ($langdat->lang_short!="de") {
							$short=$langdat->lang_short;
						}
						else {
							$short="";
						}
						$this->export_article[$link->lcat_id]="/".$this->cms->webvar."".$header_url;
					}
				}
			}
		}
	}

	/**
	 * @param string $alt
	 * @param string $neu
	 */
	function replace_links_article($alt="", $neu="")
	{
		if (strlen($alt)>2) {
			$sql=sprintf("SELECT * FROM %s WHERE lan_article LIKE '%s'",
				DB_PRAEFIX."papoo_language_article",
				"%".$alt."%");

			$result=$this->db->get_results($sql,ARRAY_A);

			//Replace
			foreach ($result as $k=>$v) {
				$neu_artikel=str_ireplace($alt,$neu,$v['lan_article']) ;

				//TODO: Hier noch das Update einbauen
			}
		}
	}

	private function start_free_urls()
	{
		$this->do_sitemap_menu();

		//Aktiviere freie URLs wandle sie entsprechend um
		if (!empty($this->checked->submit_free_urls)) {
			//alle Men�punkte rausholen
			$sql=sprintf("SELECT * FROM %s WHERE `menulinklang` NOT LIKE '%%http%%'",
				$this->cms->tbname['papoo_menu_language']
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			//Menü URLs generieren
			if (is_array($result)) {
				foreach ($result as $key=>$value) {
					$langid = $value['lang_id'];
					$lang = "/" . $this->languages[$langid];
					$mIndex = $value['menuid_id'].'-'.$langid;
					//Standardsprachkürzel entfernen
					if(strcmp($lang, "/" . $this->languages['default']) == 0) {
						$lang = "";
					}

					$suffix = "";
					#if($this->sprechende_url_doppelt($this->sprechende_url($value['menuname']), true))
					if(!isset($this->menu_id_has_url[$mIndex])) {
						continue;
					}

					if($this->sprechende_url_doppelt($this->menu_id_has_url[$mIndex], true)) {
						$suffix = "-m-" . $value['menuid_id'];
					}

					#$url = $this->sprechende_url($this->menu_id_has_url[$mIndex], PAPOO_WEB_PFAD . $lang . "/", $suffix . "/");
					$url = $this->sprechende_url($this->menu_id_has_url[$mIndex], $lang . "/", $suffix . "/");

					$neu=$url;
					$alt=$this->export_menu[$value['menuid_id']];

					#$this->replace_links_article($alt,$neu);

					$sql = sprintf("UPDATE %s SET url_menuname='%s' WHERE menuid_id=%d AND lang_id=%d",
						$this->cms->tbname['papoo_menu_language'],
						$this->db->escape($url),
						$value['menuid_id'],
						$value['lang_id']
					);
					$this->db->query($sql);
				}
			}

			$this->get_all_artikels();

			//Alle Artikel rausholen
			$sql=sprintf("SELECT * FROM %s",
				$this->cms->tbname['papoo_language_article']
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			//Durchgehenu und updaten
			if (is_array($result)) {
				foreach ($result as $value) {
					$langid = $value['lang_id'];
					if(!isset($this->languages[$langid])) {
						continue;
					}
					$lang = "/" . $this->languages[$langid];
					$aIndex = $value['lan_repore_id'].'-'.$langid;
					//Standardsprachkürzel entfernen
					if(strcmp($lang, "/" . $this->languages['default']) == 0) {
						$lang = "";
					}

					$suffix = "";
					#if($this->sprechende_url_doppelt($this->sprechende_url($value['header'])))
					if(isset($this->article_id_has_url[$aIndex])) {
						if($this->sprechende_url_doppelt($this->article_id_has_url[$value['lan_repore_id'].'-'.$langid])) {
							$suffix .= "-" . $value['lan_repore_id'];
						}
					}

					#$url = $this->sprechende_url($value['header'], PAPOO_WEB_PFAD . $lang . "/", ".html");
					$url = $this->sprechende_url($value['header'], $lang . "/", $suffix . ".html");

					$neu=$url;
					$alt = isset($this->export_article[$value['lan_repore_id']]) ? $this->export_article[$value['lan_repore_id']] : "";

					$this->replace_links_article($alt,$neu);

					$sql = sprintf("UPDATE %s SET url_header='%s' WHERE lan_repore_id=%d AND lang_id=%d",
						$this->cms->tbname['papoo_language_article'],
						$this->db->escape($url),
						$value['lan_repore_id'],
						$value['lang_id']
					);
					$this->db->query($sql);
				}
			}

			$this->edit_links_in_articles();

			//Freie URLs aktivieren
			$sql = sprintf("UPDATE %s SET mod_rewrite=1, mod_mime=1, mod_free=1",
				$this->cms->papoo_daten
			);
			$this->db->query($sql);

			//Allle Produkte rausholen
			$this->content->template['free_message']="OK";
			//Durchgehen und updaten
		}

		//Aktiviere mod_rewrite und wandle URLs entsprechend um
		else if (!empty($this->checked->submit_free_urls_reset)) {
			//alle Men�punkte rausholen
			$sql=sprintf("SELECT * FROM %s WHERE `menulinklang` NOT LIKE '%http%'",
				$this->cms->tbname['papoo_menu_language']
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			//durchgehen und updaten
			if (is_array($result)) {
				foreach ($result as $key=>$value) {
					$url = $this->sprechende_url($value['menuname']);

					$sql = sprintf("UPDATE %s SET url_menuname='%s' WHERE menuid_id=%d AND lang_id=%d",
						$this->cms->tbname['papoo_menu_language'],
						$this->db->escape($url),
						$value['menuid_id'],
						$value['lang_id']
					);
					$this->db->query($sql);
				}
			}

			//Alle Artikel rausholen
			$sql=sprintf("SELECT * FROM %s",
				$this->cms->tbname['papoo_language_article']
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			//Durchgehenu unnd updaten
			if (is_array($result)) {
				foreach ($result as $key=>$value) {
					//Links im Artikel bearbeiten
					$text = $value['lan_article'];

					$url = $this->sprechende_url($value['header']);

					$sql = sprintf("UPDATE %s SET lan_article='%s', lan_article_sans='%s', url_header='%s' WHERE lan_repore_id=%d AND lang_id=%d",
						$this->cms->tbname['papoo_language_article'],
						$this->db->escape($text),
						$this->db->escape($text),
						$this->db->escape($url),
						$value['lan_repore_id'],
						$value['lang_id']
					);
					$this->db->query($sql);
				}
			}


			//mod_rewrite URLs aktivieren
			$sql = sprintf("UPDATE %s SET mod_rewrite=3, mod_mime=1, mod_free=0",
				$this->cms->papoo_daten
			);
			$this->db->query($sql);

			//Allle Produkte rausholen
			$this->content->template['free_message']="OK";
			//Durchgehen und updaten
		}
	}
}

$free_urls = new freeplugin_class();
