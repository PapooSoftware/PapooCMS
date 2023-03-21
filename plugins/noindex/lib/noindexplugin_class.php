<?php

/**
 * @autor: Andreas Gritzan <ag@papoo.de>
 */

/**
 * Die noindexplugin_class implementiert die hauptfunktionen des noindex-Plugins.
 *
 * */
#[AllowDynamicProperties]
class noindexplugin_class
{
	/**
	 * noindexplugin_class Konstruktor.
	 *
	 * Setzt wie üblich die globalen Referenzen, und ruft dann die GetContent Funktion auf, welche
	 * den Seiten Inhalt und gesetzen Einstellungen in das content->template Konstrukt lädt.
	 * Und ruft dann die CheckPost Funktion auf welche prüft, ob Form Daten abgeschickt wurden.
	 */
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content, $db, $cms, $image_core, $weiter, $db_praefix, $module, $checked, $menu, $intern_menu, $forum, $intern_forum, $user;
		$this->content = & $content;
		$this->db = & $db;
		$this->cms = & $cms;
		$this->image_core = & $image_core;
		$this->weiter = & $weiter;
		$this->db_praefix = & $db_praefix;
		$this->module = & $module;
		$this->checked = & $checked;
		$this->menu = & $menu;
		$this->intern_menu = & $intern_menu;
		$this->forum = & $forum;
		$this->intern_forum = & $intern_forum;
		$this->user = & $user;

		if(defined('admin')) {
			$this->user->check_intern();
			global $template;
			$template2 = str_ireplace(PAPOO_ABS_PFAD . "/plugins/", "", $template);
			$template2 = basename($template2);

			if($template != "login.utf8.html") {
				if(stristr($template2, "noindex_")) {
					// CSS Pfad zur Einbindung der backend.css Datei in das template angeben (Erfolgt in noindex_backend.html)
					$this->content->template['css_path'] = $css_path = PAPOO_WEB_PFAD.'/plugins/noindex/css';

					// Inhalt für die templates in $this->content->template überführen
					$this->GetContent();

					// Prüfen ob Forms abgeschickt wurden
					$this->CheckPost();
				}
			}
		}
	}

	/**
	 * Funktion zur Vereinfachung/Sanitierung der Datenbank Aufrufe innerhalb des Plugins.
	 *
	 * @param array $daten Ein Array in dem nur die Werten stehen, die eingetragen werden sollen.
	 * @param string $database Ein String der die Datenbank angibt, z.B. "papoo_forum"
	 * @param bool $delete Wert der angibt ob die Datenbank vorher geleert werden sollte.
	 */
	private function SaveToDatabase($daten, $database, $delete = false)
	{
		$column = "";

		if("noindex_article" == $database) {
			$column = "article_id";
		}
		else if("noindex_forum" == $database) {
			$column = "msgid";
		}
		else if("noindex_menu" == $database) {
			$column = "menuid_id";
		}
		else if("noindex_dateinamen" == $database) {
			$column = "filename";
		}
		else if("noindex_templates" == $database) {
			$column = "template";
		}
		else if("noindex_urls" == $database) {
			$column = "url";
		}

		// Tabelle vorher leeren falls es sowas wie Dateinamen sind, die von ner textarea kommen
		if($delete) {
			$sql = sprintf("DELETE FROM %s", $this->db_praefix . $database);
			$this->db->query($sql);
		}

		foreach($daten as $eintrag) {
			if(is_string($eintrag)) {
				$eintrag = preg_replace('/\s+/', '', $eintrag);
			}

			if(!empty($eintrag))
			{
				$sql = sprintf("INSERT INTO %s (%s) VALUES ('%s') ON DUPLICATE KEY UPDATE %s=%s", $this->db_praefix . $database, $column, $this->db->escape($eintrag), $column, $column);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * Funktion die den Inhalt für den Spezielles Tab in content->template lädt.
	 */
	private function GetSpecialData()
	{
		$dateinamen = "nobr:";

		$sql = sprintf("SELECT * FROM %s", $this->db_praefix . "noindex_dateinamen");
		$results = $this->db->get_results($sql, ARRAY_A);

		foreach($results as $entry)
			$dateinamen .= $entry['filename'] . "\n";

		$this->content->template['plugin']['noindex']['special']['dateinamen'] = $dateinamen;

		$templates = "nobr:";

		$sql = sprintf("SELECT * FROM %s", $this->db_praefix . "noindex_templates");
		$results = $this->db->get_results($sql, ARRAY_A);

		foreach($results as $entry)
			$templates .= $entry['template'] . "\n";

		$this->content->template['plugin']['noindex']['special']['templates'] = $templates;

		$urls = "nobr:";

		$sql = sprintf("SELECT * FROM %s", $this->db_praefix . "noindex_urls");
		$results = $this->db->get_results($sql, ARRAY_A);

		foreach($results as $entry)
			$urls .= $entry['url'] . "\n";
		#array_pop($urls);
		$this->content->template['plugin']['noindex']['special']['urls'] = $urls;
	}

	/**
	 * Funktion die den Inhalt von dem Forum Tab in content->template lädt.
	 *
	 * Die Funktion benutzt dazu eigene Plugin Datenbank abfragen sowie Abfragen die aus der forum_class
	 * kopiert wurden.
	 */
	private function GetForumData()
	{
		/** noindex Seiten Information holen */

		$sql = sprintf("SELECT forumname, forumid FROM %s", $this->cms->papoo_forums);

		$foren = $this->db->get_results($sql, ARRAY_A);

		$this->content->template['plugin']['noindex']['forum'] = array();

		foreach($foren as $forum) {
			if(empty($forum['forumname'])) {
				continue;
			}

			$temp_forum = array();

			$temp_forum['forumid'] = $forum['forumid'];
			$temp_forum['forumname'] = $forum['forumname'];

			$sql = sprintf("SELECT page FROM %s WHERE forumid=%d", $this->db_praefix . "noindex_forum_pages", $forum['forumid']);

			$pages = $this->db->get_results($sql, ARRAY_A);

			$pages_array = array();

			foreach($pages as $page) {
				$pages_array[] = $page['page'];
			}

			$pages = join(",", $pages_array);

			$temp_forum['pages'] = $pages;

			$this->content->template['plugin']['noindex']['forum'][] = $temp_forum;
		}

		$sql = sprintf("SELECT * FROM %s WHERE forumid=0 AND page=0", $this->db_praefix . "noindex_forum_pages");

		$this->content->template['plugin']['noindex']['forum_listing'] = count($this->db->get_results($sql, ARRAY_A)) > 0;

		/** Forum Beiträge holen */

		$searchterm = "";

		$message_data = array ();

		// Die Ergebnisse  aus der Datenbank holen und anzeigen...
		$selectmessage = "SELECT " . $this->cms->papoo_message . ".msgid, thema, counten, forumid, level, rootid, ordnung, messagetext,";
		$selectmessage .= "  " . $this->cms->papoo_message . ".zeitstempel AS zeitstempel, ";
		$selectmessage .= "  username FROM " . $this->cms->papoo_message . ", " . $this->cms->papoo_user . " WHERE  ";

		$selectmessage .= " thema LIKE '%" . $this->db->escape($searchterm) . "%' AND ";
		$selectmessage .= " " . $this->cms->papoo_message . ".userid = " . $this->cms->papoo_user . ".userid OR ";
		$selectmessage .= " messagetext LIKE '%" . $this->db->escape($searchterm) . "%' AND " . $this->cms->papoo_message . ".userid = " . $this->cms->papoo_user . ".userid LIMIT 10";

		// Datenbank Abfrage durchf�hren
		$resultmessage = $this->db->get_results($selectmessage);

		// Wenn nicht leer, dann in Array einlesen
		if (!empty ($resultmessage)) {
			// F�r jedes Ergebnis ein Eintrag ins Array
			foreach ($resultmessage as $row) {
				$sql = sprintf("SELECT * FROM %s WHERE msgid=%d", $this->db_praefix . "noindex_forum_msgs", $row->msgid);

				$noindex_res = count($this->db->get_results($sql, ARRAY_A)) > 0;

				// Daten zuweisen in das loop array f�rs template... ###
				array_push($message_data, array (
					'rootid' => $row->rootid,
					'msgid' => $row->msgid,
					'forumid' => $row->forumid,
					'menuid' => $this->checked->menuid,
					'username' => $row->username,
					'zeitstempel' => $row->zeitstempel,
					'counten' => $row->counten,
					'thema' => $row->thema,
					'messagetext' => $row->messagetext,
					'noindex' => $noindex_res
				));
			}
			// zuweisen f�r das Template
			$this->content->template['plugin']['noindex']['message_data_such'] = $message_data;
			#$this->content->template['message_data'] = $message_data;
		}
		// Keine Ergebnisse dann,
		else {
			// Text ausgeben!
			$this->content->template['content_text'] = $this->content->template['message_5'];
		}
	}

	/**
	 * Funktion die den Inhalt des Artikel Tabs in content->template lädt.
	 *
	 * Die Funktion benutzt dazu eigene Plugin Datenbank abfragen sowie Abfragen die aus der artikel_class
	 * kopiert wurden.
	 */
	private function GetArticleData()
	{
		/** Seitendaten */
		$sql = sprintf("SELECT * FROM %s WHERE page=0;", $this->db_praefix . "noindex_article_pages");
		$listing = count($this->db->get_results($sql, ARRAY_A)) > 0;

		$sql = sprintf("SELECT * FROM %s WHERE page!=0;", $this->db_praefix . "noindex_article_pages");
		$result = $this->db->get_results($sql, ARRAY_A);

		$pages = array();

		foreach($result as $entry) {
			$pages[] = $entry['page'];
		}

		$pages = join(",", $pages);

		$this->content->template['plugin']['noindex']['article']['listing'] = $listing;
		$this->content->template['plugin']['noindex']['article']['pages'] = $pages;

		/** Suchdaten */
		if (!empty($this->checked->noindex_artikel_suche)) {
			$suche_sql = "AND CONCAT(t2.header, ' ', t2.lan_teaser, ' ', t2.lan_article_sans) LIKE '%" . $this->db->escape($this->checked->noindex_artikel_suche) . "%' ";
		}
		IfNotSetNull($this->replangid);
		IfNotSetNull($suche_sql);

		$order = " t1.stamptime DESC";
		$this->content->template['eigene'] = '1';
		// Anzahl f�r weitere Seiten rausbekommen
		$sqlsuch = "SELECT COUNT(reporeID) FROM " . $this->cms->papoo_repore . ", " . $this->cms->papoo_language_article . " ";
		//$sqlsuch .= " WHERE reporeID=lan_repore_id AND lang_id='".$this->cms->lang_id."'";
		$sqlsuch .= " WHERE reporeID=lan_repore_id AND lang_id='" . $this->db->escape($this->replangid) . "'";
		$sqlsuch .= " ";

		$this->weiter->result_anzahl = $this->db->get_var($sqlsuch);
		// Daten raussuchen

		$get_suchergebniss = sprintf("SELECT DISTINCT * FROM (
                                                    SELECT DISTINCT (t1.reporeID), t2.lan_teaser, t2.header, t1.teaser_bild, t4.username, t1.timestamp, t1.dokuser_last, t1.dokuser
													FROM
													%s AS t1, %s AS t2,
													%s AS t4, %s AS t5

													WHERE t1.reporeID = t2.lan_repore_id AND t5.lart_id=t1.reporeID
													%s
													) AS articles
                                                    LEFT JOIN
                                                      (SELECT article_id AS noindex FROM %s) AS noindex_articles
                                                    ON noindex_articles.noindex=articles.reporeID
									",

			$this->cms->papoo_repore, $this->cms->papoo_language_article,
			$this->cms->papoo_user, $this->cms->tbname['papoo_lookup_art_cat'],
			$suche_sql,
			$this->db_praefix . "noindex_article_ids"
		);

		$get_suchergebniss2 = $get_suchergebniss;
		$get_suchergebniss = $get_suchergebniss . " ";

		$this->content->template['plugin']['noindex']['artikel_liste'] = array();
		$result2 = $this->db->get_results($get_suchergebniss);
		$this->content->template['artikelliste'] = "ok";
		#$this->result_liste = $this->db->get_results($get_suchergebniss, ARRAY_A);
		#$this->result_liste_co = $this->db->get_results($get_suchergebniss2, ARRAY_A);
		//wenn Artikel vorhanden sind
		if (!empty ($result2)) {
			foreach ($result2 as $row) {
				$temp_array = array();
				$temp_array['ueberschrift'] = $row->header;
				$temp_array['reporeID'] = $row->reporeID;
				$temp_array['teaser'] = $row->lan_teaser;
				//$temp_array['timestamp'] = $row->timestamp;
				$temp_array['date_time'] = $row->timestamp;
				$sql = sprintf("SELECT username FROM %s WHERE userid='%d'",
					$this->cms->tbname['papoo_user'],
					$row->dokuser_last
				);
				$temp_array['username'] = $this->db->get_var($sql);
				if (empty($temp_array['username']))
				{
					$temp_array['username'] = "n.o.";
				}
				$sql = sprintf("SELECT username FROM %s WHERE userid='%d'",
					$this->cms->tbname['papoo_user'],
					$row->dokuser
				);
				$temp_array['autor'] = $this->db->get_var($sql);
				$temp_array['lang_id'] = $this->cms->lang_back_content_id;
				$temp_array['noindex'] = $row->noindex;

				// Daten zuweisen
				$this->content->template['plugin']['noindex']['artikel_liste'][] = $temp_array;
			}
		}

		// Doppelte funde filtern
		$this->content->template['plugin']['noindex']['artikel_liste'] = array_unique($this->content->template['plugin']['noindex']['artikel_liste'], SORT_REGULAR);
	}

	/**
	 * Funktion die alle Inhalt-füllende Aufrufe zusammenfasst.
	 */
	private function GetContent()
	{
		$this->GetArticleData();
		$this->GetForumData();
		$this->GetMenuData();
		$this->GetSpecialData();
	}

	/**
	 * Funktion die alle Post-prüfenden Aufrufe zusammenfasst.
	 */
	private function CheckPost()
	{
		$this->CheckPostArtikelSuchen();
		$this->CheckPostArtikelSeiten();
		$this->CheckPostForumSuchen();
		$this->CheckPostForumSeiten();
		$this->CheckPostMenu();
		$this->CheckPostSpezielles();
	}

	/**
	 * Funktion die prüft ob ein Artikel noindex haken gesetzt wurde und dieses in die Datenbank einträgt.
	 */
	private function CheckPostArtikelSuchen()
	{
		// reporeID => [0,1]
		if(isset($this->checked->noindex_article)) {
			foreach($this->checked->noindex_article as $key=>$val) {
				if($val)
					$sql = sprintf("INSERT INTO %s VALUES (%d) ON DUPLICATE KEY UPDATE article_id=%d;", $this->db_praefix . "noindex_article_ids", $key, $key);
				else
					$sql = sprintf("DELETE FROM %s WHERE article_id=%d", $this->db_praefix . "noindex_article_ids", $key);

				$this->db->query($sql);
			}
		}
	}

	/**
	 * Funktion die die Artikel Seiten Einstellungen speichert, falls die Form abgeschickt wurde.
	 */
	private function CheckPostArtikelSeiten()
	{
		if(isset($this->checked->noindex_article_pages)) {
			$noindex_listing = isset($this->checked->noindex_article_listing);
			$noindex_pages = preg_split("/,/", $this->checked->noindex_article_pages);
			$forum_db = $this->db_praefix . "noindex_article_pages";

			$sql = sprintf("DELETE FROM %s", $this->db_praefix . "noindex_article_pages");
			$this->db->query($sql);

			// Das komplette Listing zu ignorieren ist als Beitrag #0 kodiert.
			if($noindex_listing) {
				$sql = sprintf("INSERT INTO %s (page) VALUES(0);", $forum_db);
				$this->db->query($sql);
			}

			// TODO: das hier (mehrere Daten in einer Anfrage übergeben) in savetodatabase integrieren und savetodatabase hier benutzen
			$sql = "INSERT INTO " . $forum_db . " (page) VALUES ";
			foreach($noindex_pages as $page) {
				$page = $this->db->escape($page);
				$sql .= "(" . $page . "),";
			}

			// Letze Komma zu Semicolon
			if(count($noindex_pages > 0) and !empty($noindex_pages[0])) {
				$sql = substr($sql, 0, -1);
				$sql .= ";";

				// Alle Seiten einfügen
				$this->db->query($sql);
			}
		}
	}

	/**
	 * Funktion die bei Foren Beiträgen gesetzte Haken in der Datenbank speichert.
	 */
	private function CheckPostForumSuchen()
	{
		if(isset($this->checked->noindex_forum_message)) {
			// msgid => [0, 1]

			$message_ids = $this->checked->noindex_forum_message;

			foreach($message_ids as $msgid=>$noindex) {
				if($noindex) {
					$sql = sprintf("INSERT INTO %s VALUES (%d) ON DUPLICATE KEY UPDATE msgid=%d;", $this->db_praefix . "noindex_forum_msgs", $msgid, $msgid);
				}
				else {
					$sql = sprintf("DELETE FROM %s WHERE msgid=%d", $this->db_praefix . "noindex_forum_msgs", $msgid);
				}
				$this->db->query($sql);
			}
		}
	}

	/**
	 * Funktion die Forum Seiten Einstellungen in der Datenbank speichert, falls das Form abgeschickt wurde.
	 */
	private function CheckPostForumSeiten()
	{
		if(isset($this->checked->noindex_forum_pages)) {
			$noindex_listing = isset($this->checked->noindex_forum_listing) and $this->checked->noindex_forum_listing;

			$unterforen = $this->checked->noindex_forum_pages;

			foreach($unterforen as $forumid=>$pages) {
				$noindex_pages = preg_split("/,/", $pages);
				$forum_db = $this->db_praefix . "noindex_forum_pages";

				$sql = sprintf("DELETE FROM %s WHERE forumid=%d", $forum_db, $forumid);
				$this->db->query($sql);

				$sql = "INSERT INTO " . $forum_db . " (page, forumid) VALUES ";
				foreach($noindex_pages as $page) {
					$page = $this->db->escape($page);
					$sql .= "(" . $page . ", " . $forumid . "),";
				}

				// Letze Komma zu Semicolon
				if(count($noindex_pages > 0) and !empty($noindex_pages[0])) {
					$sql = substr($sql, 0, -1);
					$sql .= ";";

					// Alle Seiten einfügen
					$this->db->query($sql);
				}
			}

			if(!$noindex_listing) {
				$sql = sprintf("DELETE FROM %s WHERE forumid=0 AND page=0", $this->db_praefix . "noindex_forum_pages");
				$this->db->query($sql);
			}
			else {
				$sql = sprintf("INSERT INTO %s VALUES(%d, %d);", $this->db_praefix . "noindex_forum_pages", 0, 0);
				$this->db->query($sql);
			}
		}
	}

	/**
	 *  Funktion die Einstellungen die in dem Menu Tab vorgenommen wurde in der Plugin-Datenbank abspeichert.
	 */
	private function CheckPostMenu()
	{
		if(isset($this->checked->noindex_menu)) {
			// Die keys des $this->checked->noindex_menu arrays sind jetzt die menuids die noindexed' werden sollen.

			// Neue Häkchen setzen
			$noindex_menuids = array();

			// Den key in value schieben und an SaveToDatabase geben
			foreach($this->checked->noindex_menu as $menuid=>$val) {
				if($val) {
					array_push($noindex_menuids, $menuid);
				}
			}

			$this->SaveToDatabase($noindex_menuids, "noindex_menu", true);
		}
	}

	/**
	 * Funktion die Einstellungen die in dem Spezielles Tab gemacht wurden in der Datenbank abspeichert.
	 *
	 * @uses noindexplugin_class::SaveToDatabase()
	 */
	private function CheckPostSpezielles()
	{
		if(isset($this->checked->noindex_spezielles_area1)) {
			$noindex_dateinamen = preg_split('/[\n]/', $this->checked->noindex_spezielles_area1);

			$this->SaveToDatabase($noindex_dateinamen, "noindex_dateinamen", true);
		}
		if(isset($this->checked->noindex_spezielles_area2)) {
			$noindex_templates = preg_split('/[\n]/', $this->checked->noindex_spezielles_area2);

			$this->SaveToDatabase($noindex_templates, "noindex_templates", true);
		}
		if(isset($this->checked->noindex_spezielles_area3)) {
			$noindex_urls = preg_split('/[\n]/', $this->checked->noindex_spezielles_area3);

			$this->SaveToDatabase($noindex_urls, "noindex_urls", true);
		}
	}

	/**
	 * Funktion die zurückgibt ob das aktuelle Dokument wegen Einstellungen des Spezielles Tabs auf noindex gesetzt werden soll.
	 *
	 * @return bool|void Ob das aktuelle Dokument auf noindex gesetzt werden soll.
	 */
	private function IsnoindexSpecial()
	{
		$uri = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$matches = array();

		$dateiname = NULL;
		$template = NULL;

		// Dateiname, template, url aus URI parsen
		if(preg_match('/([^\/\?]+\.(?:php|html))/', $uri, $matches)) {
			$dateiname = $matches[1];
		}
		if(preg_match('/[^\&]+&template=(?:%20)?(.+\.html)/', $uri, $matches)) {
			$template = $matches[1];
		}
		$url = $uri;

		// Datenbanken abfragen, ob einer von denen nicht indiziert werden sollte
		$sql = sprintf("SELECT * FROM %s WHERE filename='%s'", $this->db_praefix . "noindex_dateinamen", $this->db->escape($dateiname));
		if(count($this->db->get_results($sql, ARRAY_A)) > 0) return true;

		$sql = sprintf("SELECT * FROM %s WHERE template='%s'", $this->db_praefix . "noindex_templates", $this->db->escape($template));
		if(count($this->db->get_results($sql, ARRAY_A)) > 0) return true;

		$sql = sprintf("SELECT * FROM %s WHERE url='%s'", $this->db_praefix . "noindex_urls", $this->db->escape($url));
		if(count($this->db->get_results($sql, ARRAY_A)) > 0) return true;
	}

	/**
	 * Funktion die zurückgibt ob das aktuelle Dokument wegen Einstellungen des Menü Tabs auf noindex gesetzt werden soll.
	 *
	 * @return bool Ob das aktuelle Dokument auf noindex gesetzt werden soll.
	 */
	private function IsnoindexMenu()
	{
		$sql = sprintf("SELECT * FROM %s", $this->db_praefix . "noindex_menu");
		$results = $this->db->get_results($sql, ARRAY_A);

		$noindex_menuids = array();

		// In besseres Format für die Abfrage umwandeln
		foreach($results as $entry) {
			$noindex_menuids[$entry['menuid_id']] = 'on';
		}

		return isset($noindex_menuids[$this->checked->menuid]);
	}

	/**
	 * Funktion die zurückgibt ob das aktuelle Dokument wegen Einstellungen des Artikel Tabs auf noindex gesetzt werden soll.
	 *
	 * @return bool|void Ob das aktuelle Dokument auf noindex gesetzt werden soll.
	 * @uses artikel_class
	 */
	private function IsnoindexArticle()
	{
		// Artikel Klasse einbinden die wir brauchen um zu gucken
		// ob wir gerade eine Artikel-Liste angucken ($artikel->artikel_anzahl)
		global $artikel;

		/*** Artikel wird angeschaut prüfen ***/
		$noindex = false;

		if($this->checked->reporeid) {
			$id = $this->checked->reporeid;

			$sql = sprintf("SELECT * FROM %s WHERE article_id=%d", $this->db_praefix . "noindex_article_ids", $id);

			$noindex = count($this->db->get_results($sql, ARRAY_A)) > 0;
		}

		// Wenn der Artikel irgendwo eingebunden wird, wird diese property benutzt statt der anderen. Oder so.
		if($this->checked->real_reporeid) {
			$id = $this->checked->real_reporeid;

			$sql = sprintf("SELECT * FROM %s WHERE article_id=%d", $this->db_praefix . "noindex_article_ids", $id);

			$noindex |= count($this->db->get_results($sql, ARRAY_A)) > 0;
		}

		if($noindex) {
			return $noindex;
		}

		/*** Artikel Seiten indexierung überprüfen ***/
		if(isset($artikel->artikel_anzahl)) {
			$sql = sprintf("SELECT * FROM %s WHERE page=0", $this->db_praefix . "noindex_article_pages");
			$listing_nicht_indizieren = count($this->db->get_results($sql, ARRAY_A));

			if($listing_nicht_indizieren) {
				return true;
			}

			$current_page = isset($this->checked->page) ? $this->checked->page : 1;

			$sql = sprintf("SELECT * FROM %s WHERE page=%d", $this->db_praefix . "noindex_article_pages", $current_page);

			return count($this->db->get_results($sql, ARRAY_A)) > 0;
		}
	}

	/**
	 * Funktion die zurückgibt ob das aktuelle Dokument wegen Einstellungen des Forum Tabs auf noindex gesetzt werden soll.
	 *
	 * @return bool Ob das aktuelle Dokument auf noindex gesetzt werden soll.
	 */
	private function IsnoindexForum()
	{
		// Prüfen ob wir überhaupt in der Forum.php sind => false wenn nicht!
		$uri = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$matches = array();
		$dateiname = "";
		if(preg_match('/([^\/\?]+\.(?:php|html))/', $uri, $matches)) {
			$dateiname = $matches[1];
		}

		if($dateiname != "forum.php") {
			return false;
		}

		// Listing ist-geindext prüfen (msgid ist null wenn wir in der forum.php sind, aber kein beitrag angucken)
		$sql = sprintf("SELECT * FROM %s WHERE page=0 AND forumid=0", $this->db_praefix . "noindex_forum_pages");
		if(!isset($this->checked->msgid) || $this->checked->msgid == 0 and count($this->db->get_results($sql, ARRAY_A)) > 0) {
			return true;
		}

		// Prüfen ob wir die Paginierung auf noindex setzen sollen
		if($this->checked->forumid > 0 and $this->checked->msgid == 0) {
			$pagenum = isset($this->checked->page) ? $this->checked->page : 1;

			$sql = sprintf("SELECT * FROM %s WHERE forumid=%d AND page=%d", $this->db_praefix . "noindex_forum_pages", $this->checked->forumid, $pagenum);

			if(count($this->db->get_results($sql, ARRAY_A)) > 0) {
				return true;
			}
		}

		// Prüfen ob wir einen Beitrag angucken, der auf noindex steht
		$sql = sprintf("SELECT * FROM %s WHERE msgid=%d", $this->db_praefix . "noindex_forum_msgs", $this->checked->msgid);

		// Gucken ob ein Beitrag im Thread auf noindex steht
		if(isset($this->forum->board_result)) {
			foreach($this->forum->board_result as &$beitrag) {
				$sql .= (" OR msgid=" . $beitrag->msgid);
			}
		}
		return count($this->db->get_results($sql, ARRAY_A)) > 0;
	}

	/**
	 * Sammelfunktion für alle Isnoindex- Aufrufe.
	 *
	 * @return bool Ob das aktuelle Dokument auf noindex gesetzt werden soll, d.h. nicht von Suchmaschinen gecrawled werden soll.
	 */
	private function ShouldModifyOutput()
	{
		return $this->IsnoindexSpecial() or $this->IsnoindexMenu() or $this->IsnoindexArticle() or $this->IsnoindexForum();
	}

	/**
	 * Holt sich die Menu Daten für das Menü Tab aus den relevanten Klassen und schreibt die ins Template.
	 *
	 * Der Code ist aus der intern_menu_class weitestgehend kopiert um die
	 * Menu-Struktur anzuzeigen.
	 * @uses menu_class
	 */
	private function GetMenuData()
	{
		// Menu Struktur, etc. (Kopiert aus intern_menu_class)
		$this->menu->data_front_publish = $this->intern_menu->mak_menu_liste_zugriff();
		if ($this->cms->categories == 1) {
			$this->menu->preset_categories();
			$this->menu->get_menu_cats();
		}

		$this->content->template['menulist_data'] = $this->menu->menu_navigation_build("FRONT_PUBLISH", "CLEAN");

		IfNotSetNull($this->menu->ebenen_shift_ende);

		$this->content->template['menulist_ebenen_shift_ende'] = $this->menu->ebenen_shift_ende;

		if ($this->cms->categories == 1) {
			$this->menu->get_menu_cats();
			$this->menu->make_menucats();
			$catlist_data = $this->menu->preset_categories(true);
			$this->content->template['catlist_data'] = $catlist_data;
			$this->menu->get_menu_cats();
			$this->content->template['categories'] = 1;
		}
		else {
			$this->content->template['catlist_data'][0] = array("cat_id"=>-1);
			$this->content->template['no_categories'] = 1;
		}

		// Häkchen Konfiguration holen und übergeben
		$haken = array();

		$sql = sprintf("SELECT * FROM %s", $this->db_praefix . "noindex_menu");
		$results = $this->db->get_results($sql, ARRAY_A);

		// Zum einfacheren Abfragen im template in den key schieben
		foreach($results as $val) {
			$haken[$val['menuid_id']] = 'on';
		}
		$this->content->template['plugin']['noindex']['menu_checks'] = $haken;
	}

	/**
	 * Funktion die dem Dokument $output den robots metatag ersetzt.
	 *
	 * @param $output
	 * @param $success
	 * @uses preg_replace()
	 * @return string|null
	 */
	private function ModifyMeta($output, &$success)
	{
		return preg_replace("/<meta name=\"robots\".+>/", '<meta name="robots" content="noindex">', $output, -1, $success);
	}

	/**
	 * Funktion die dem Dokument $output den robots metatag hinzufügt.
	 *
	 * @param $output
	 * @param $success
	 * @return string|null
	 * @uses preg_replace()
	 */
	private function AddMeta($output, &$success)
	{
		return preg_replace("/(<meta .+>)/", '$1\n<meta name="robots" content="noindex">', $output, -1, $success);
	}

	/**
	 * Funktion die versucht das Dokument $output auf noindex zu stellen.
	 *
	 * @param $output
	 * @return null|string
	 * @uses ModifyMeta()
	 * @uses AddMeta()
	 */
	private function Setnoindex($output)
	{
		$success = 0;
		$neu_output = $this->ModifyMeta($output, $success);

		// Robots tag nicht gefunden => hinzufügen
		if(!$success) {
			$neu_output = $this->AddMeta($output, $success);
		}

		// Hinzufügen fehlgeschlagen => Original output verwenden
		if(!$success) {
			$neu_output = $output;
		}
		return $neu_output;
	}

	/**
	 * Callback funktion die das Dokument filtert, falls eine entsprechende Einstellung vorgenommen wurde.
	 * @uses $output Die globale Inhalts-Variable, die in der all_inc_front definiert wird.
	 * @uses noindexplugin_class::ShouldModifyOutput() Sammel Methode die zurückgibt ob eine Einstellung eine Veränderung des outputs zur Folge haben sollte.
	 * @uses noindexplugin_class::Setnoindex() Methode die die eigentliche Modifikation des Dokuments durchführt.
	 */
	function output_filter()
	{
		global $output;

		if($this->ShouldModifyOutput()) {
			$output = $this->Setnoindex($output);
		}
	}

	/**
	 * @ignore
	 */
	function post_papoo()
	{

	}
}

$noindex_plugin = new noindexplugin_class();
