<?php

/**
 * RSS - News-Grabber
 * Copyright 2006 by Netzwelt-Kali.de - Karl Fahrtmann
 */
#[AllowDynamicProperties]
class newsgrabber_class
{
	/**
	 * newsgrabber_class constructor.
	 */
	function __construct()
	{
		global $cms, $content, $checked, $db, $menu, $user, $template, $diverse;
		$this->cms = & $cms;
		$this->content = & $content;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->menu = & $menu;
		$this->user = & $user;
		$this->template = & $template;
		$this->diverse = & $diverse;

		// Aufruf der Aktionsweiche
		$this->make_test();
	}

	/**
	 * Aktionsweiche
	 */
	function make_test()
	{
		if (defined("admin")) {
			$this->user->check_intern();
			$this->backend_schreiben();
		}
		else {
			$this->news_schreiben();
		}
	}

	/**
	 * newsgrabber_class::news_schreiben()
	 *
	 * @return void
	 */
	function news_schreiben()
	{
		global $template;

		//NUr laufen wenn auch im Menüpunkt drin ist...
		if (!stristr($template,"newsgrabber")) {
			#return false;
		}
		$this->content->template['rss_lang_id']=$this->cms->lang_id;
		$result = $this->read_from_db();

		if (($result[0]->menuid > 0) and ($result[0]->menuid != $this->content->template['aktive_menuid'])) {
			return;
		}

		$newsfile = $result[0]->newsfile;
		$enable_caching = $result[0]->enable_caching;
		$cache_file = $result[0]->cache_file;
		$cache_file_time = $result[0]->cache_file_time;
		$cache_refresh_time = $result[0]->cache_refresh_time;
		$max_news = $result[0]->max_news;
		$kommentieren = $result[0]->kommentieren;
		$schlagzeile = $result[0]->schlagzeile;
		$datumanzeigen = $result[0]->datumanzeigen;
		$newsfilequelle = $result[0]->newsfilequelle;
		$fehleranzeige = $result[0]->fehleranzeige;
		$allowable_tags = $result[0]->allowable_tags;

		$update_cache=false;
		if ($enable_caching!="false") {
			$cache_diff =(time() - $cache_file_time)/60;
			if ($cache_file == "" or ($cache_diff > $cache_refresh_time)) {
				$update_cache=true;
				$fcontents=$this->get_rss_data($newsfile);
				$cache_file = $fcontents;
			}
			else {
				$fcontents = @unserialize($cache_file);
			}
		}
		else {
			$fcontents=$this->get_rss_data($newsfile);
			$cache_file = $fcontents;
		}

		$this->content->template['newsgrabber_array'] = $fcontents;

		$allowable_tags.="<p>";
		$news="";
		$i=0;
		if (is_array($fcontents)) {
			foreach ($fcontents as $key=>$value) {
				if ($i>=$max_news) {
					continue;
				}
				$news.= '<div class="rss_newsblock"><div class="rss_newsblockkopf">';
				if ($datumanzeigen != "true") {
					$news = $news."<a href=\"". trim($value['link'])."\" target=\"_blank\">".trim(strip_tags($value['title'], $allowable_tags))."</a>\n";
				}
				else {
					$news = $news."<a href=\"". trim($value['link'])."\" target=\"_blank\">".trim(strip_tags($value['title'], $allowable_tags))."</a><br /><small>".$value['pubDate'].":</small> ";
				}

				$news.= "</div>\n";
				if ( $schlagzeile=="true") {
					$description=((string)$value['description']);
					$description = str_replace ("style", "nostlye", $description);
					$news.="<div class=\"rss_newskoerper\">".trim(strip_tags($description, $allowable_tags))."</div>";
				}

				$news.= "</div>";

				$news.="";
				$i++;
			}
		}
		$out_buffer = '<div class="rss_news">'.$news.'</div>';

		$this->content->template['newsgrabber_text'] = "nobr:".$out_buffer;
		$this->content->template['max_news']=$max_news;

		# Ggf. gecachte Daten speichern
		if ($update_cache and $result and !empty($result[0]->newsgrabber_id)) {
			global $db_praefix;
			$table = $db_praefix . "newsgrabber";
			$sql = sprintf('UPDATE `%s` SET cache_file="%s", cache_file_time="%s"
								WHERE newsgrabber_id=%d AND newsgrabber_lang=%d',
				$table,
				$this->db->escape(serialize($cache_file)),
				$this->db->escape(time()+($cache_refresh_time*60)),
				$result[0]->newsgrabber_id,
				$result[0]->newsgrabber_lang
			);
			$this->db->query($sql);
		}
	}

	/**
	 * newsgrabber_class::atom_to_xml()
	 *
	 * @param string $fcontents
	 * @return string
	 */
	private function atom_to_xml($fcontents="")
	{
		// hier einiger ersetzungen um auch atom-feeds zu verarbeiten, quasi ein rss-feed daraus machen
		return $fcontents;
	}

	/**
	 * newsgrabber_class::get_rss_data()
	 *
	 * @param string $feed_urls
	 * @return array
	 */
	private function get_rss_data($feed_urls="")
	{
		$feeds=explode("\n",$feed_urls);

		if (is_array($feeds)) {
			foreach ($feeds as $key=>$value) {
				//Daten holen
				$data=$this->get_data($value);

				$data=$this->atom_to_xml($data);
				//In Array umwandeln
				$xml = simplexml_load_string($data,'SimpleXMLElement', LIBXML_NOCDATA);

				$json = json_encode($xml);
				$array = json_decode($json,TRUE);

				//Nur die Eintr�ge hinzuf�gen
				$items[]=$array['channel']['item'];
			}
		}

		//Eintr�ge durchgehen
		if (isset($items) && is_array($items)) {
			foreach ($items as $key=>$value) {
				if (is_array($value)) {
					foreach ($value as $key_item=>$value_item) {
						if (is_array($value_item)) {
							if (isset($value_item['comments']) && $value_item['comments']==1) {
								$time=time();
							}
							else {
								$time=strtotime($value_item['pubDate']);
							}
							preg_match_all( '/<img[^>]*>/Ui',$value_item['description'], $img_data );
							$value_item['description']=substr(strip_tags($value_item['description']),0,250)."...";
							IfNotSetNull($img_data['0']['0']);
							$value_item['image']=$img_data['0']['0'];
							$neu[$time]=$value_item;
						}
					}
				}
			}
		}
		if (isset($neu) && is_array($neu)) {
			krsort($neu);
		}
		IfNotSetNull($neu);
		return $neu;
	}

	/**
	 * @param $url
	 * @return bool|string|void
	 */
	private function get_data($url)
	{
		if (function_exists("curl_init")) {
			$ch = curl_init( trim($url) );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_USERAGENT,
				"Check Agent" );

			$curl_ret = curl_exec( $ch );
			curl_close( $ch );
			return $curl_ret;
		}
	}

	/**
	 * newsgrabber_class::backend_schreiben()
	 *
	 * @return void
	 */
	function backend_schreiben()
	{
		if (isset($_POST['senden'])) {
			if (isset($_POST['newsfile'])) {
				$newsfile = $_POST['newsfile'];
			}
			else {
				$newsfile = $_POST['newsfile1'];
			}
			$enable_caching = $_POST['enable_caching'];
			if ($enable_caching[0] == "true") {
				$enable_caching = "true";
			}
			else {
				$enable_caching = "false";
			}
			$cache_file = isset($_POST['cache_file']) ? $_POST['cache_file'] : NULL;
			$cache_file_time = time();
			if(!is_numeric($_POST['cache_refresh_time'])) {
				$_POST['cache_refresh_time'] = 0;
			}
			$cache_refresh_time = $_POST['cache_refresh_time'] + 0;
			if (is_numeric($cache_refresh_time) && $cache_refresh_time <= 0) {
				$cache_refresh_time = 20;
			}
			if(!is_numeric($_POST['max_news'])) {
				$_POST['max_news'] = 0;
			}
			$max_news = $_POST['max_news'] + 0;
			if ($max_news <= 0) {
				$max_news = 10;
			}
			$kommentieren = $_POST['kommentieren'];
			if ($kommentieren[0] == "true") {
				$kommentieren = "true";
			}
			else {
				$kommentieren = "false";
			}
			$schlagzeile = $_POST['schlagzeile'];
			if ($schlagzeile[0] == "true") {
				$schlagzeile = "true";
			}
			else {
				$schlagzeile = "false";
			}
			$datumanzeigen = $_POST['datumanzeigen'];
			if ($datumanzeigen[0] == "true") {
				$datumanzeigen = "true";
			}
			else {
				$datumanzeigen = "false";
			}
			$newsfilequelle = $_POST['newsfilequelle'];
			if ($newsfilequelle[0] == "true") {
				$newsfilequelle = "true";
			}
			else {
				$newsfilequelle = "false";
			}
			$fehleranzeige = $_POST['fehleranzeige'];
			if ($fehleranzeige[0] == "true") {
				$fehleranzeige = "true";
			}
			else {
				$fehleranzeige = "false";
			}
			$allowable_tags = "<a><abbr><acronym><b><big><br><bdo><center><cite><code><del><dfn><div><em><h3><h4><h5><h6><i><ins><kbd><li><ol><samp><small><span><strike><strong><sub><sup><table><td><th><tr><tt><u><ul><var>"; // Erlaubte HTML-Tags"
			$result = array((object)NULL);
			$result[0]->newsfile = $newsfile;
			$result[0]->enable_caching = $enable_caching;
			$result[0]->cache_file = $cache_file;
			$result[0]->cache_file_time = $cache_file_time;
			$result[0]->cache_refresh_time = $cache_refresh_time;
			$result[0]->max_news = $max_news;
			$result[0]->kommentieren = $kommentieren;
			$result[0]->schlagzeile = $schlagzeile;
			$result[0]->datumanzeigen = $datumanzeigen;
			$result[0]->newsfilequelle = $newsfilequelle;
			$result[0]->fehleranzeige = $fehleranzeige;
			$result[0]->allowable_tags = $allowable_tags;
			$result[0]->menuid = $_POST['newsgrabber_menuid'];
			$this->write_into_db($result);
		}

		$result = $this->read_from_db();
		$this->get_liste_menu();
		$this->content->template['newsfile'] = "nobr:".$result[0]->newsfile;
		$this->content->template['enable_caching'] = $result[0]->enable_caching;
		$this->content->template['cache_file'] = $result[0]->cache_file;
		$this->content->template['cache_file_time'] = $result[0]->cache_file_time;
		$this->content->template['cache_refresh_time'] = $result[0]->cache_refresh_time;
		$this->content->template['max_news'] = $result[0]->max_news;
		$this->content->template['kommentieren'] = $result[0]->kommentieren;
		$this->content->template['schlagzeile'] = $result[0]->schlagzeile;
		$this->content->template['datumanzeigen'] = $result[0]->datumanzeigen;
		$this->content->template['fehleranzeige'] = $result[0]->fehleranzeige;
		$this->content->template['newsfilequelle'] = $result[0]->newsfilequelle;
		$this->content->template['menu_id'] = $result[0]->menuid;
	}

	/**
	 * newsgrabber_class::write_into_db()
	 *
	 * @param mixed $result
	 * @return void
	 */
	function write_into_db($result)
	{
		$newsfile = $result[0]->newsfile;
		$enable_caching = $result[0]->enable_caching;
		$cache_file = $result[0]->cache_file;
		$cache_file_time = $result[0]->cache_file_time;
		$cache_refresh_time = $result[0]->cache_refresh_time;
		$max_news = $result[0]->max_news;
		$kommentieren = $result[0]->kommentieren;
		$schlagzeile = $result[0]->schlagzeile;
		$datumanzeigen = $result[0]->datumanzeigen;
		$newsfilequelle = $result[0]->newsfilequelle;
		$fehleranzeige = $result[0]->fehleranzeige;
		$allowable_tags = $result[0]->allowable_tags;

		if (defined("admin")) {
			$lang_id=$this->cms->lang_back_content_id;
		}
		else {
			$lang_id=$this->cms->lang_id;
		}
		if (empty($lang_id))
			$lang_id=1;
		$menuid = $result[0]->menuid;

		if (!empty($newsfile)) {
			// das lokale Tabellen-Pr�fix der Installation
			global $db_praefix;
			$table = $db_praefix . "newsgrabber";
			$sql=sprintf("DELETE FROM %s WHERE newsgrabber_id=1 AND newsgrabber_lang=%d", $table, (int)($lang_id) );
			$this->db->query($sql);
			if (defined("admin")) {
				// SQL-Anweisung zusammenstellen
				$query = sprintf("INSERT INTO `%s` SET newsfile='%s', enable_caching='%s',
						cache_file='%s', cache_file_time='%s', cache_refresh_time='%s',
						max_news='%s', kommentieren='%s', schlagzeile='%s', datumanzeigen='%s',
						newsfilequelle='%s', fehleranzeige='%s', allowable_tags='%s',
						newsgrabber_lang=%d, newsgrabber_id=1, menuid=%d",
					$table,
					$this->db->escape($newsfile),
					$this->db->escape($enable_caching),
					$this->db->escape($cache_file),
					$this->db->escape($cache_file_time),
					$this->db->escape($cache_refresh_time),
					$this->db->escape($max_news),
					$this->db->escape($kommentieren),
					$this->db->escape($schlagzeile),
					$this->db->escape($datumanzeigen),
					$this->db->escape($newsfilequelle),
					$this->db->escape($fehleranzeige),
					$this->db->escape($allowable_tags),
					(int)($lang_id),
					(int)($menuid)
				);
				// Pr�fe ob Menuid existiert, sonst Upgrade der Tabelle
				$query2 = sprintf('SHOW COLUMNS FROM `%s` LIKE \'menuid\'', $table);
				if (!$this->db->get_results($query2)) {
					$query2 = sprintf('ALTER TABLE `%s` ADD `menuid` INT(11)', $table);
					$this->db->query($query2);
				}
			}
			else {
				// SQL-Anweisung zusammenstellen
				$query = sprintf("INSERT INTO `%s` SET newsfile='%s', newsgrabber_lang='%d' ,newsgrabber_id=1",
					$table,
					$this->db->escape($newsfile),
					$this->db->escape($lang_id)
				);
			}

		}
		$this->diverse->write_to_file("/templates_c/rssfeeds.txt",$newsfile);
		// SQL-Anweisung ausf�hren
		$this->db->get_results($query);
	}

	/**
	 * newsgrabber_class::read_from_db()
	 *
	 * @return array|void
	 */
	function read_from_db()
	{
		if (defined("admin")) {
			$lang_id=$this->cms->lang_back_content_id;
		}
		else {
			$lang_id=$this->cms->lang_id;
		}
		if (empty($lang_id)) {
			$lang_id=1;
		}
		global $db_praefix;

		$table = $db_praefix . "newsgrabber";
		$query = sprintf("SELECT * FROM `%s` WHERE newsgrabber_id=1 AND newsgrabber_lang='%d' LIMIT 1",
			$table,
			$this->db->escape($lang_id)
		);

		$result = $this->db->get_results($query);
		return $result;
	}

	/**
	 * Liste der Men�punkte besorgen
	 */
	function get_liste_menu()
	{
		global $menu;
		$menu->data_front_complete =$menu->menu_data_read("FRONT");
		$this->content->template['menulist_data'] = $menu->data_front_complete;
	}
}

$newsgrabber = new newsgrabber_class();
