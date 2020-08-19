<?php

/**
 * Class debug_class
 */
class debug_class
{
	/**
	 * debug_class constructor.
	 */
	function __construct()
	{
		// Einbindung globaler papoo-Klassen
		global $db, $cms, $checked, $content, $menuintcss, $pluginscss, $user;
		$this->db = & $db;
		$this->cms = & $cms;
		$this->checked = & $checked;
		$this->content = & $content;
		$this->menuintcss = & $menuintcss;
		$this->pluginscss = & $pluginscss;
		$this->user = & $user;


		// Aktions-Weiche
		// **************
		if (defined("admin")) {
			$this->user->check_intern();
			global $template;
			if (strpos("XXX".$template, "debug_backend.html")) {
				IfNotSetNull($this->checked->debug_action);
				switch ($this->checked->debug_action) {
				case "set_debug_install":
					$this->set_debug_install();
					break;

				case "delete_all_plugin_entrys":
					$this->delete_all_plugin_entrys();
					break;

				case "set_debug_stopallredirect":
					$this->set_debug_stopallredirect();
					break;

				case "set_artikel_admin_rechte":
					$this->set_artikel_admin_rechte();
					break;

				case "set_db_charset_collation":
					$this->set_db_charset_collation();
					break;

				case "set_article_searchfield":
					$this->set_article_searchfield();
					break;

				case "set_article_seitenbaum":
					$this->set_article_seitenbaum();
					break;

				case "gen_article_language_entries":
					$this->gen_article_language_entries();
					break;
				}
				$this->content->template['debug_install'] = isset($_SESSION['devtool_debug_install']) ? $_SESSION['devtool_debug_install'] : "";
				$this->content->template['debug_stopallredirect'] = isset($_SESSION['debug_stopallredirect']) ? $_SESSION['debug_stopallredirect'] : "";
			}
		}
	}

	/**
	 * setzt in $_SESSIONS den Parameter 'devtool_debug_install'. Anhand dieses Parameters entscheidet der Plugin-Manager
	 * Bei Installation oder De-Installations, ob eventuelle Fehlermeldungen ausgegeben werden, oder die Weiterleitug zu�ck
	 * zur Plugin-Manager-Seite direkt erfolgt.
	 */
	function set_debug_install()
	{
		if (isset($this->checked->debug_install) && $this->checked->debug_install == "ja") {
			$_SESSION['devtool_debug_install'] = true;
			$message = $this->content->template['plugin']['devtools']['automatik_de'];
		}
		else {
			$_SESSION['devtool_debug_install'] = false;
			$message = $this->content->template['plugin']['devtools']['automatik_re'];
		}
		$this->content->template['debug_message'] = $message;
	}

	/**
	 * setzt in $_SESSIONS den Parameter 'devtool_debug_stopallredirect'. Anhand dieses Parameters wird bei s�mtlichen Weiterleitungen
	 * per header(Locaton:..) entschieden, ob automatisch oder manuell weitergeleitet wird
	 */
	function set_debug_stopallredirect()
	{
		if (isset($this->checked->debug_stopallredirect) && $this->checked->debug_stopallredirect == "ja") {
			$_SESSION['debug_stopallredirect'] = true;
			$message = $this->content->template['plugin']['devtools']['alle_auto_de'];
		}
		else {
			$_SESSION['debug_stopallredirect'] = false;
			$message = $this->content->template['plugin']['devtools']['alle_auto_re'];
		}
		$this->content->template['debug_message'] = $message;
	}

	function delete_all_plugin_entrys()
	{
		global $db_praefix;
		// Alle Men�-Eintr�ge l�schen
		// Eintr�ge aus Tabelle menuint l�schen
		$this->db->get_results(sprintf("DELETE FROM %s WHERE menuid>999 AND menuid<2000", $this->cms->papoo_menu_int));
		// Eintr�ge aus Tabelle menuint_language l�schen
		$this->db->get_results(sprintf("DELETE FROM %s WHERE menuid_id>999 AND menuid_id<2000", $this->cms->papoo_menuint_language));
		// Eintr�ge aus Tabelle lookup_menuint l�schen
		$this->db->get_results(sprintf("DELETE FROM %s WHERE menuid>999 AND menuid<2000", $this->cms->papoo_lookup_men_int));

		// Alle Eintr�ge aus Tabelle plugins l�schen
		$this->db->get_results(sprintf("DELETE FROM %s", $this->cms->papoo_plugins));
		// Alle Eintr�ge aus Tabelle pluginclasses l�schen
		$this->db->get_results(sprintf("DELETE FROM %s", $this->cms->papoo_pluginclasses));

		// Module l�schen
		// Plugin-Module ermitteln
		$sql = sprintf("SELECT mod_id FROM %s WHERE mod_datei LIKE 'plugin:%%'", $db_praefix."papoo_module");
		$module = $this->db->get_results($sql, ARRAY_A);
		// Plugin-Module l�schen
		if (!empty($module)) {
			foreach($module as $modul) {
				$sql = sprintf("DELETE FROM %s WHERE mod_id='%d'",  $db_praefix."papoo_module", $modul['mod_id']);
				$this->db->query($sql);
				$sql = sprintf("DELETE FROM %s WHERE modlang_mod_id='%d'",  $db_praefix."papoo_module_language", $modul['mod_id']);
				$this->db->query($sql);
			}
		}

		// CSS-Datei f�r Backend-Men� neu erstellen
		$this->menuintcss->make_menuintcss();
		// CSS-Datei f�r Plugins-CSS neu erstellen
		$this->pluginscss->make_pluginscss();

		// Auf die Plugin-Manager-Seite springen
		//header("Location: ./plugins.php?menuid=47");
		$location_url = "./plugins.php?menuid=47";
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="'.$location_url.'">'.$this->content->template['plugin']['devtools']['weiter'].'</a>';
		}
		else {
			header("Location: $location_url");
		}
	}

	function set_artikel_admin_rechte()
	{
		// 1. Alle Artikel-Nummern auslesen:
		$sql = sprintf("SELECT reporeID FROM %s", $this->cms->papoo_repore);
		$artikel_nummern = $this->db->get_results($sql, ARRAY_A);

		// 2. Alte Lese-Rechte f�r Gruppe "Administrator" l�schen
		$sql = sprintf("DELETE FROM %s WHERE gruppeid_id='1'", $this->cms->tbname['papoo_lookup_article']);
		$this->db->query($sql);

		// 3. Alte Schreib-Rechte f�r Gruppe "Administrator" l�schen
		$sql = sprintf("DELETE FROM %s WHERE gruppeid_wid_id='1'", $this->cms->tbname['papoo_lookup_write_article']);
		$this->db->query($sql);

		// 4. Lese- und Schreib-Rechte f�r Gruppe "Administrator" setzen
		if (!empty($artikel_nummern)) {
			foreach($artikel_nummern AS $artikel) {
				$sql = sprintf("INSERT INTO %s SET article_id='%d', gruppeid_id='1'",
					$this->cms->tbname['papoo_lookup_article'],
					$artikel['reporeID']
				);
				$this->db->query($sql);

				$sql = sprintf("INSERT INTO %s SET article_wid_id='%d', gruppeid_wid_id='1'",
					$this->cms->tbname['papoo_lookup_write_article'],
					$artikel['reporeID']
				);
				$this->db->query($sql);
			}
		}
		// 5. Meldung ausgeben
		$this->content->template['debug_message'] = $this->content->template['plugin']['devtools']['rechte_admin'];
	}

	function set_db_charset_collation()
	{
		if (!empty($this->cms->tbname)) {
			foreach ($this->cms->tbname as $table) {
				$sql = sprintf("ALTER TABLE %s CONVERT TO CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'", $this->db->escape($table));
				$this->db->query($sql);
			}
		}
		// Meldung ausgeben
		$this->content->template['debug_message'] = $this->content->template['plugin']['devtools']['fin_db_charset_setzen'];
	}

	function set_article_searchfield()
	{
		$sql = sprintf("SELECT * FROM %s", $this->cms->tbname['papoo_language_article']);
		$temp_articles = $this->db->get_results($sql, ARRAY_A);

		if (!empty($temp_articles)) {
			foreach($temp_articles as $temp_article) {
				if(!isset($temp_article['lan_teaser'])) {
					$temp_article['lan_teaser'] = NULL;
				}
				$temp_search_text = $temp_article['header']." ".$temp_article['lan_teaser']." ".$temp_article['lan_article_sans'];

				global $diverse;
				$temp_search_text = $diverse->searchfield_text($temp_search_text);

				$sql = sprintf("UPDATE %s SET lan_article_search='%s' WHERE lan_repore_id='%d' AND lang_id='%d' ",
					$this->cms->tbname['papoo_language_article'],
					$this->db->escape($temp_search_text),
					$temp_article['lan_repore_id'],
					$temp_article['lang_id']
				);
				$this->db->query($sql);
			}
		}
		// .. Meldung ausgeben
		$this->content->template['debug_message'] = $this->content->template['plugin']['devtools']['fin_article_searchfield'];
	}

	//Macht alle Artikel im internen Seitenbaum sichtbar.
	function set_article_seitenbaum()
	{
		$sql = sprintf("SELECT article_wid_id FROM %s WHERE NOT gruppeid_wid_id = 1",
			$this->cms->tbname['papoo_lookup_write_article']
		);

		$temp_articles = $this->db->get_results($sql, ARRAY_A);

		foreach ($temp_articles as $temp_article) {
			$artikel_id = ($temp_article["article_wid_id"]);
			$sql = sprintf("INSERT INTO %s VALUES ('%d', 1)",
				$this->cms->tbname['papoo_lookup_write_article'],
				$this->db->escape($artikel_id)
			);
		}
	}

	// Fügt allen Artikeln Spracheinträge für alle Sprachen hinzu, um diese unter Inhalt sichtbar zumachen.
	function gen_article_language_entries()
	{
		// Hole alle aktivierten Sprachen
		$sql = sprintf("SELECT * FROM `%s` WHERE `more_lang`=2 ORDER BY `lang_id`;",
			$this->cms->tbname['papoo_name_language']
		);
		$languages = $this->db->get_results($sql, ARRAY_A);

		// Hole alle Artikel
		$sql = sprintf("SELECT * FROM `%s` ORDER BY `reporeID`;",
			$this->cms->tbname['papoo_repore']
		);
		$articles = $this->db->get_results($sql, ARRAY_A);

		// Hole alle Artikel-Spracheinträge
		$sql = sprintf("SELECT * FROM `%s` ORDER BY `lan_repore_id`, `lang_id`;",
			$this->cms->tbname['papoo_language_article']
		);
		$artlang = $this->db->get_results($sql, ARRAY_A);

		// Sprachstruktur für späteren Algorithmus festlegen
		$langs = array();
		foreach($languages as $tuple) {
			$langs[(int)$tuple["lang_id"]] = false;
		}

		// Welche Sprachen haben welche Sprachen bereits zugewiesen
		$current = -1;
		$artlangs = array();
		$matched = array();
		foreach($artlang as $tuple) {
			// Bei neuem Artikel angekommen
			if($tuple["lan_repore_id"] != $current) {
				// Verfügbare Spracheinträge (IDs) für Artikel festhalten
				if($current != -1) {
					$artlangs[(int)$tuple["lan_repore_id"]] = $matched;
				}
				// ID-Register zurücksetzen und nächsten Artikel fokußieren
				$current = $tuple["lan_repore_id"];
				$matched = array();
			}
			foreach(array_keys($langs) as $key) {
				if($tuple["lang_id"] == $key) {
					$matched[] = $key;
					break;
				}
			}
		}

		// Artikel in papoo_repore, aber nicht in papoo_language_article
		$orphans = array();

		// Guck welcher Artikel gar keinen Spracheintrag hat
		foreach($articles as $article) {
			$orphan = true;
			foreach($artlang as $tuple) {
				if($tuple["lan_repore_id"] == $article["reporeID"]) {
					$orphan = false;
					break;
				}
			}
			if($orphan) {
				$orphans[] = $article["reporeID"];
			}
		}

		// Überschrift der Artikel rausholen
		$header = array();
		foreach($artlang as $tuple) {
			$header[$tuple["lan_repore_id"]] = $tuple["header"];
		}

		// Spracheinträge für Artikel, die bereits Einträge haben, anlegen
		foreach($artlangs as $key => $val) {
			foreach(array_keys($langs) as $langid) {
				if(array_search($langid, $val) === false) {
					$sql = sprintf("INSERT INTO `%s` (`lan_repore_id`, `lang_id`, `header`) VALUES (%d, %d, '%s');",
						$this->cms->tbname['papoo_language_article'],
						$key,
						$langid,
						$this->db->escape($header[$key])
					);
					$this->db->query($sql);
				}
			}
		}
		// Spracheinträge für verwaiste Artikel anlegen
		foreach($orphans as $orphan) {
			foreach(array_keys($langs) as $langid) {
				$title = ($langid == 1) ? "Verwaisten Artikel gefunden" : "orphaned article found";
				$sql = sprintf("INSERT INTO `%s` (`lan_repore_id`, `lang_id`, `header`) VALUES (%d, %d, '%s');",
					$this->cms->tbname['papoo_language_article'],
					$orphan,
					$langid,
					$title
				);
				$this->db->query($sql);
			}
		}
	}
}

$debug = new debug_class();
