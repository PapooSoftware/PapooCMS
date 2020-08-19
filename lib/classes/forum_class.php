<?php

/**
 * #####################################
 * # CMS Papoo                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.2                  #
 * #####################################
 */

/**
 * Mit dieser Klasse wird das Forum generiert.
 */
class forum
{
	/**
	 * Konstruktor, initialisieren der nötigen Variablen
	 */
	function __construct()
	{
		// Für Rückwärts-Kompatibilität
		if (!function_exists('is_countable')) {
			function is_countable($var) {
				return (is_array($var) || $var instanceof Countable);
			}
		}
		/**
		 * Klassen globalisieren
		 */
		// cms Klasse einbinden
		global $cms;
		$this->cms = &$cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = &$db;
		// Messages einbinden
		global $message;
		$this->message = &$message;
		// User Klasse einbinden
		global $user;
		$this->user = &$user;
		// weitere Seiten Klasse einbinden
		global $weiter;
		$this->weiter = &$weiter;
		// inhalt Klasse einbinden
		global $content;
		$this->content = &$content;
		// checkedblen Klasse einbinde
		global $checked;
		$this->checked = &$checked;
		// bbcode Klasse einbinden
		global $bbcode;
		$this->bbcode = &$bbcode;
		// Mail Klasse einbinden
		global $mail_it;
		$this->mail_it = &$mail_it;
		// html KLasse einbinden
		global $html;
		$this->html = &$html;
		// Diverse Funktionen
		global $diverse;
		$this->diverse = &$diverse;
		// Suchklasse
		global $searcher;
		$this->searcher = &$searcher;
		global $menu;
		$this->menu = &$menu;
		// Blacklist-Klasse
		global $blacklist;
		$this->blacklist = &$blacklist;
		/**
		 * Hier werden die Links zum Thread bzw. zurück zum Forum generiert
		 */
		$this->check_variablen();
		// Link zum Thread/ Forum zuweisen, je nach mod_rewrite Zustand
		if ($this->cms->mod_rewrite == 1) {
			// Link zum Thread
			$this->content->template['link_form_log_thread'] = $this->content->template['forumthread'] = "forumthread.php";
			// Link zum Forum
			$this->content->template['link_form_log'] = $this->content->template['forum'] = "forum.php";
		}
		if ($this->cms->mod_rewrite == 2) {
			// Link zum Thread
			$this->content->template['link_form_log_thread'] = $this->content->template['forumthread'] = "forumthread";
			// Link zum Forum
			$this->content->template['link_form_log'] = $this->content->template['forum'] = "forum";
		}

		if ($this->cms->mod_surls == 1) {
			$this->content->template['forum_surl'] = "";

			$menuid_forum = $this->checked->menuid;

			if (!empty($this->menu->menu_front)) {
				foreach ($this->menu->menu_front as $men) {
					if ($menuid_forum == $men['menuid']) {
						$this->content->template['forum_menu_surl'] = $men['menuname_url'];
						$this->content->template['forum_menu_free_surl'] = $men['menuname_free_url'];
					}
				}
			}
		}
		if ($this->cms->mod_free == 1) {
			$this->content->template['forum_surl'] = "";
			if (is_array($this->menu->menu_front)) {
				$menuid_forum = $this->checked->menuid;

				foreach ($this->menu->menu_front as $men) {
					if ($menuid_forum == $men['menuid']) {
						$this->content->template['forum_menu_surl'] = $men['menuname_free_url'];
					}

				}
			}
		}

		if (empty ($this->checked->menuid)) {
			$this->checked->menuid = "";
		}
		if (empty ($this->checked->forumid)) {
			$this->checked->forumid = "";
		}
		// menuid Übergeben
		$this->content->template['formmenuid'] = $this->checked->menuid;
		$this->content->template['forumid_tmpl2'] = $this->content->template['forumid_tmpl'] = $this->checked->forumid;
		// Parameter "messageboard" setzen, wegen <ul> und </ul> bei Nicht-Messageboard. Wird von do_board() auf "ja" gesetzt
		$this->content->template['messageboard'] = "nein";
	}

	/**
	 *
	 */
	function check_variablen()
	{
		if (empty($this->checked->msgid)) {
			$this->checked->msgid = "";
		}

		if (empty($this->checked->forumid)) {
			$this->checked->forumid = "";
		}

		if (empty($this->checked->formmenuid)) {
			$this->checked->formmenuid = "";
		}

		if (empty($this->checked->forumid_tmpl)) {
			$this->checked->forumid_tmpl = "";
		}

		if (empty($this->checked->formforumid)) {
			$this->checked->formforumid = "";
		}

		if (empty($this->checked->rootid)) {
			$this->checked->rootid = "";
		}

		if (empty($this->checked->formmsgid)) {
			$this->checked->formmsgid = "";
		}

		if (empty($this->checked->reporeid)) {
			$this->checked->reporeid = "";
		}

		if (empty($this->checked->menuid)) {
			$this->checked->menuid = "";
		}

		if (!is_numeric($this->checked->msgid)) {
			$this->checked->msgid = "0";
		}

		if (!is_numeric($this->checked->forumid)) {
			$this->checked->forumid = "0";
		}

		if (!is_numeric($this->checked->formmenuid)) {
			$this->checked->formmenuid = "0";
		}

		if (!is_numeric($this->checked->forumid_tmpl)) {
			$this->checked->forumid_tmpl = "0";
		}

		if (!is_numeric($this->checked->formforumid)) {
			$this->checked->formforumid = "0";
		}

		if (!is_numeric($this->checked->rootid)) {
			$this->checked->rootid = "0";
		}

		// formmsgid
		if (!is_numeric($this->checked->formmsgid)) {
			$this->checked->formmsgid = "0";
		}

		if (!is_numeric($this->checked->reporeid)) {
			$this->checked->reporeid = "0";
		}

		if (!is_numeric($this->checked->menuid)) {
			$this->checked->menuid = "0";
		}
	}


	/**
	 * Damit wird die Forum Seite erstellt
	 */
	function make_forum()
	{
		// Wenn die Suche aktiviert ist
		if (!empty ($this->checked->search)) {
			$this->checked->search = $this->searcher->clean($this->checked->search);
			$this->forum_search();
		} // Suche ist nicht aktivert, dann normal
		else {
			// Startseite Forum, kein Forum ausgewählt
			if (empty ($this->checked->forumid)) {
				// Liste der verfügbaren Foren erstellen
				$this->forum_list();
				// Die letzten 10 Messages anzeigen (Anzahl muß variabilisiert werden!)
				$this->last_messages();
			}
			else {
				// Forum mit allen Messages anzeigen, wenn ausgewählt
				$this->show_forum();
			}
		}
		$this->content->template['suchbox'] = "";
	}

	/**
	 * @return void
	 * Mit dieser Methode wird das Forum nach bestimmten Stichwörtern durchsucht
	 */
	function forum_search()
	{
		// Liste der Foren anzeigen und Liste der verfügbaren Foren erzeugen (nach Rechten)
		$this->forum_list();
		// Array erstellen
		$message_data = array();
		// Es wurde gesucht -> Template

		$this->content->template['gesucht'] = 1;
		global $searcher;
		$this->checked->search = $searcher->clean($this->checked->search);
		$this->content->template['search'] = $this->checked->search;
		$search = explode(' ', $this->checked->search);
		$search_vars = $search_vars2 = $search_vars3 = '';
		foreach ($this->content->template['forumok'] as $forumid) {
			IfNotSetNull($this->checked->select_forum);

			//Wenn ein Forum zur Suche ausgewählt ist
			if (is_numeric($this->checked->select_forum) && $this->checked->select_forum > 1) {
				//Dann Überspringen wenn das nicht das ausgewählte Forum ist
				if ($this->checked->select_forum != $forumid) {
					continue;
				}
				$this->content->template['select_forum_id'] = $this->checked->select_forum;
			}
			$search_var_standard = sprintf(" %s.userid = %s.userid AND intranet_yn='0' AND forumid='%d'",
				$this->cms->papoo_message,
				$this->cms->papoo_user,
				$forumid);


			foreach ($search as $wort) {
				$search_vars .= sprintf(" %s AND thema LIKE '%s' OR %s AND messagetext LIKE '%s' OR ",
					$search_var_standard,
					"%" . $this->db->escape($wort) . "%",
					$search_var_standard,
					"%" . $this->db->escape($wort) . "%"
				);
			}
			IfNotSetNull($search['1']);
			IfNotSetNull($search['2']);
			$search_vars2 .= sprintf(" %s AND thema LIKE '%s' AND thema LIKE '%s' OR %s AND messagetext LIKE '%s' AND messagetext LIKE '%s' OR
			 %s AND messagetext LIKE '%s' AND username ='%s' OR",
				$search_var_standard,
				"%" . $this->db->escape($search['0']) . "%",
				"%" . $this->db->escape($search['1']) . "%",
				$search_var_standard,
				"%" . $this->db->escape($search['0']) . "%",
				"%" . $this->db->escape($search['1']) . "%",
				$search_var_standard,
				"%" . $this->db->escape($search['0']) . "%",
				"%" . $this->db->escape($search['1']) . "%"
			);

			$search_vars3 .= sprintf(" %s AND thema LIKE '%s' AND thema LIKE '%s' AND thema LIKE '%s' OR %s AND messagetext LIKE '%s' AND 
			messagetext LIKE '%s' AND messagetext LIKE '%s' OR %s AND messagetext LIKE '%s' AND messagetext LIKE '%s' AND username ='%s' OR",
				$search_var_standard,
				"%" . $this->db->escape($search['0']) . "%",
				"%" . $this->db->escape($search['1']) . "%",
				"%" . $this->db->escape($search['2']) . "%",
				$search_var_standard,
				"%" . $this->db->escape($search['0']) . "%",
				"%" . $this->db->escape($search['1']) . "%",
				"%" . $this->db->escape($search['2']) . "%",
				$search_var_standard,
				"%" . $this->db->escape($search['0']) . "%",
				"%" . $this->db->escape($search['1']) . "%",
				"%" . $this->db->escape($search['2']) . "%"
			);
		}

		$sql = sprintf("SELECT %s.msgid, thema, counten, forumid, level, rootid, ordnung,  %s.zeitstempel AS zeitstempel, username FROM %s, %s
WHERE %s %s.userid=%s.userid AND intranet_yn='0' AND forumid='xx' GROUP BY  msgid ORDER BY msgid DESC  LIMIT 100",
			$this->cms->papoo_message,
			$this->cms->papoo_message,
			$this->cms->papoo_message,
			$this->cms->papoo_user,
			$search_vars,
			$this->cms->papoo_message,
			$this->cms->papoo_user
		);

		$resultmessage1 = $this->db->get_results($sql, ARRAY_A);

		if (empty($resultmessage1))
			$resultmessage1 = array();

		$sql = sprintf("SELECT msgid,thema, counten, forumid, level, rootid, ordnung,  %s.zeitstempel, username FROM %s, %s
WHERE %s %s.userid=%s.userid AND intranet_yn='0' AND forumid='xx' GROUP BY  msgid ORDER BY msgid DESC LIMIT 200",
			$this->cms->papoo_message,
			$this->cms->papoo_message,
			$this->cms->papoo_user,
			$search_vars2,
			$this->cms->papoo_message,
			$this->cms->papoo_user
		);

		$resultmessage2 = $this->db->get_results($sql, ARRAY_A);

		if (empty($resultmessage2))
			$resultmessage2 = array();

		$sql = sprintf("SELECT msgid,thema, counten, forumid, level, rootid, ordnung,  %s.zeitstempel, username FROM %s, %s
WHERE %s %s.userid=%s.userid AND intranet_yn='0' AND forumid='xx' GROUP BY  msgid ORDER BY msgid DESC LIMIT 200",
			$this->cms->papoo_message,
			$this->cms->papoo_message,
			$this->cms->papoo_user,
			$search_vars3,
			$this->cms->papoo_message,
			$this->cms->papoo_user
		);

		$resultmessage3 = $this->db->get_results($sql, ARRAY_A);

		if (empty($resultmessage3))
			$resultmessage3 = array();

		// Datenbank Abfrage durchführen
		$resultmessage = array_merge($resultmessage3, $resultmessage2, $resultmessage1);

		// Gibt eine "NOTICE: Array to string conversion"-Nachricht, was ok so ist. => ignorieren.
		@array_unique($resultmessage);

		// Wenn nicht leer, dann in Array einlesen
		if (!empty ($resultmessage)) {
			// Für jedes Ergebnis ein Eintrag ins Array
			foreach ($resultmessage as $row) {

				$zeitpar = explode("\.", $row['zeitstempel']);

				IfNotSetNull($zeitpar['1']);

				if (is_numeric($zeitpar['1'])) {
					$datum1 = "-" . $zeitpar['1'] . "-" . $zeitpar['0'];
					$datum2 = str_ireplace("  ", "" . $datum1 . " ", $zeitpar['2']);
					$row['zeitstempel'] = $datum2;
				}
				// Daten zuweisen in das loop array fürs template... ###
				array_push($message_data, array('rootid' => $row['rootid'],
					'msgid' => $row['msgid'],
					'forumid' => $row['forumid'],
					'menuid' => $this->checked->menuid,
					'username' => $row['username'],
					'zeitstempel' => $row['zeitstempel'],
					'counten' => $row['counten'],
					'thema_surl' => $this->menu->urlencode($row['thema']) . ".html",
					'thema' => $row['thema']
				));
			}
			// zuweisen für das Template
			$this->message_data = $message_data;
			$this->content->template['message_data'] = $message_data;
		} // Keine Ergebnisse dann,
		else {
			// Text ausgeben!
			$this->content->template['content_text'] = $this->content->template['message_5'];
		}
	}

	/**
	 * Die Liste aller Foren wird hier erstellt.
	 * @return array|void $table_data Die Liste aller Foren
	 */
	function forum_list()
	{
		// Nur wenn kein Forum ausgewählt ist
		if (empty ($this->checked->forumid)) {
			// Übersicht aktivieren -> Template
			$this->content->template['uebersicht'] = 1;
		}
		// menuid Übergeben
		$this->content->template['formmenuid'] = $this->checked->menuid;
		// Array erzeugen
		$table_data = array();

		// Setze Variable auf NULL falls nicht existent, zwecks Behebung von Warnungen
		if (!isset($this->cms->intranet_yn)) {
			$this->cms->intranet_yn = NULL;
		}

		if ($this->user->userid == 10) {
			$selectforum = "SELECT * FROM " . $this->cms->papoo_forums . " WHERE  intranet_yn='" . $this->cms->intranet_yn . "' ORDER BY forumname ASC";
		}
		else {
			$selectforum = "SELECT " . $this->cms->papoo_forums . ".forumid, forumname, Beschreibung, forum_last, forum_threads, forum_beitr ";
			$selectforum .= " FROM " . $this->cms->papoo_forums . ", " . $this->cms->papoo_lookup_forum_read . ", " . $this->cms->papoo_lookup_ug . " ";
			$selectforum .= " WHERE ";
			$selectforum .= "  " . $this->cms->papoo_lookup_ug . ".userid='" . $this->user->userid . "' ";
			$selectforum .= " AND " . $this->cms->papoo_lookup_ug . ".gruppenid=" . $this->cms->papoo_lookup_forum_read . ".gruppenid";
			$selectforum .= " AND  " . $this->cms->papoo_forums . ".forumid=" . $this->cms->papoo_lookup_forum_read . ".forumid ";
			$selectforum .= " AND intranet_yn='" . $this->cms->intranet_yn . "' ORDER BY forumname ASC";
		}

		$resultforum = $this->db->get_results($selectforum);

		$j = 0;
		// Array erstellen für die Nummern der erlaubten Foren, damit auch andere Methoden hierauf zugreifen können
		$this->content->template['forumok'] = array();
		// DatenÜbersicht Über alle verfügbaren Foren anzeigen... ####
		if (!empty ($resultforum)) {
			$forumalt = "";
			foreach ($resultforum as $row) {
				// Nur Einmal pro Forum erlauben
				if ($forumalt == $row->forumid) {
					continue;
				}
				// erlaubte Foren id zuweisen
				$forumalt = $this->content->template['forumok'][$j] = $row->forumid;
				$j++;
				// Wenn das Forum ausgewählt ist, Daten rausholen für die Anzeige ...
				if ($this->checked->forumid == $row->forumid) {
					// Name für die ANzeige im ausgewählten Forum
					$this->content->template['forumnameh2'] = $row->forumname;
					// Id des ausgewählten Forums
					$this->content->template['forumid'] = $row->forumid;
				}
				// Wenn noch nicht die Anzahl der Beiträge und Threads gezählt wurde
				if ($row->forum_threads < 1 or $row->forum_beitr < 1) {
					$this->forumid = $row->forumid;
					// ANzahl der Threads und Beiträge updaten
					$this->update_forum($row->forumid);
				}

				if ($this->cms->mod_rewrite == 2) {
					$this->content->template['forum'] = "forum";
				}
				else {
					$this->content->template['forum'] = "forum.php";
				}
				$row->forumname_surl = $this->menu->urlencode($row->forumname);
				// Daten in ein Array weisen
				array_push($table_data, array('forumid' => $row->forumid,
					'menuid' => $this->checked->menuid,
					'forum' => $this->content->template['forum'],
					'forumname' => $row->forumname,
					'forumname_surl' => $row->forumname_surl,
					'beschreibung' => $row->Beschreibung,
					'forum_threads' => $row->forum_threads,
					'forum_beitr' => $row->forum_beitr
				));
			}
		}
		// Array in eine Eigenschaft der Klasse content einlesen.
		$this->content->template['table_data'] = $table_data;
	}

	/**
	 * Die Überschrift einer Message bekommen
	 *
	 * @param $msgid
	 */
	function get_message_header($msgid)
	{
		$papoo_message = $this->cms->papoo_message;
		$papoo_user = $this->cms->papoo_user;
		$papoo_forums = $this->cms->papoo_forums;

		$sqlmessage = "SELECT msgid, rootid, thema, messagetext ,  " .
			"       username, $papoo_message.forumid, " .
			"       $papoo_message.zeitstempel " .
			"FROM $papoo_message, $papoo_user, $papoo_forums  " .
			"WHERE ";
		foreach ($this->content->template['forumok'] as $forumidsql) {
			$sqlmessage .= " $papoo_message.msgid='$msgid' AND $papoo_message.userid = $papoo_user.userid AND " .
				"$papoo_message.forumid = $papoo_forums.forumid AND $papoo_message.intranet_yn='" .
				$this->cms->intranet_yn . "' AND $papoo_forums.forumid='$forumidsql' OR ";
		}
		$sqlmessage .= " $papoo_message.msgid='0'  AND $papoo_message.userid = '0' AND " .
			"$papoo_message.forumid = '0' AND $papoo_message.intranet_yn='" .
			$this->cms->intranet_yn .
			"' AND $papoo_forums.forumid='$forumidsql' LIMIT 1";
		// Message aus der Datenbank holen
		$result2 = $this->db->get_results($sqlmessage);

		// http://localhost/papoo/forumthread.php?forumid=1&menuid=138&rootid=1&msgid=15#15
		if (!empty ($result2)) {
			foreach ($result2 as $ret) {
				$this->thema = $ret->thema;
				$this->usernamex = $ret->username;
				$this->zeitstempel = $ret->zeitstempel;
				$this->msgid = $ret->msgid;
				$this->rootid = $ret->rootid;
			}
		}
	}

	/**
	 * Forum updaten mit der Anzahl der threads und messages
	 *
	 * @param string $value
	 */
	function update_forum($value = "")
	{
		$value = $this->db->escape($value);
		$sql = "UPDATE " . $this->cms->papoo_forums . "";
		$sql .= " SET forum_threads='" . $this->count_msg() . "', ";
		$sql .= " forum_beitr='" . $this->count_msg_all() . "'";
		$sql .= " WHERE forumid='$value'";
		$this->db->get_results($sql);
	}

	/**
	 * Hier werden die letzten 10? Messages herausgeholt und zurückgegeben
	 *
	 * @return void
	 */
	function last_messages()
	{
		// SQL-Statement abhängig von Modus der Liste letzte Beiträge
		switch ($this->cms->forum_letzte_modus) {
		case 1 :
			// In Liste nur Themen anzeigen
			$this->content->template['message_3'] = $this->content->template['message_3_2'];
			$sql_add = "t1.rootid = 0 AND ";
			$sortierfeld = "t1.letzter_beitrag_zeit";
			break;

		case 0 :
		default :
			// In Liste Beiträge anzeigen
			$this->content->template['message_3'] = $this->content->template['message_3_1'];
			$sql_add = "";
			$sortierfeld = "t1.msgid";
		}
		// NEUE ABFRAGE
		$message_data = array();
		$sql = sprintf(" SELECT DISTINCT t1.*, GREATEST(t1.msgid, t1.letzter_beitrag_id) AS lastid, t2.username
														FROM %s AS t1, %s AS t2, %s AS t3, %s AS t4, %s AS t5, %s AS t6

														WHERE %s
														t1.userid = t2.userid AND
														t1.forumid = t3.forumid AND t3.gruppenid = t6.gruppeid

														AND t4.userid = t5.userid AND t5.gruppenid = t6.gruppeid
														AND t4.userid='%d'

														ORDER BY %s DESC
														LIMIT 10",
			$this->cms->papoo_message,
			$this->cms->papoo_user,
			$this->cms->papoo_lookup_forum_read,
			$this->cms->papoo_user,
			$this->cms->papoo_lookup_ug,
			$this->cms->papoo_gruppe,
			$sql_add,
			$this->user->userid,
			$sortierfeld
		);
		$resultmessage = $this->db->get_results($sql);
		/*
			// ALTE ABFRAGE

			*/
		// Wenn Ergebnisse da sind,
		if (!empty ($resultmessage)) {
			// diese dann in ein Array einlesen
			foreach ($resultmessage as $row) {
				// "Nummern-Tausch", damit bei Liste nur Themen auf die letzte Nachricht geantwortet wird
				if ($this->cms->forum_letzte_modus == 1) {
					$row->rootid = $row->msgid;
					$row->msgid = $row->lastid;
				}
				// Daten zuweisen in das loop array fürs template... ###
				array_push($message_data, array('rootid' => $row->rootid,
					'lastid' => $row->lastid,
					'msgid' => $row->msgid,
					'forumid' => $row->forumid,
					'menuid' => $this->checked->menuid,
					'username' => $row->username,
					'zeitstempel' => $row->zeitstempel,
					'counten' => $row->counten,
					'thema_surl' => $this->menu->urlencode($row->thema),
					'thema' => $row->thema
				));
			}
		}
		// Link zum Thread zuweisen, je nach mod_rewrite Zustand
		if ($this->cms->mod_rewrite == 1) {
			$this->content->template['forumthread'] = "forumthread.php";
		}
		if ($this->cms->mod_rewrite == 2) {
			$this->content->template['forumthread'] = "forumthread";
		}
		// Daten zurückgeben
		$this->content->template['message_data'] = $message_data;
	}

	/**
	 * Hier wird die Anzahl der msg pro Forum ausgelsen, sollte aber nur einmal passieren nach einem Update
	 *
	 * @return array
	 */
	function count_msg_all()
	{
		// messages auslesen und zählen --- wird für später gebraucht ...
		$sql = sprintf(" SELECT COUNT(msgid) FROM %s
										WHERE forumid ='%d' AND intranet_yn='%d'  ",
			$this->cms->papoo_message,
			$this->forumid,
			$this->cms->intranet_yn
		);
		return $this->gesamt_anzahl = $this->db->get_var($sql);
	}

	/**
	 * Hier wird die Anzahl der threads pro Forum ausgelsen, sollte aber nur einmal passieren nach einem Update
	 *
	 * @return array
	 */
	function count_msg()
	{
		// messages auslesen und zählen --- wird für später gebraucht ...
		$sql = sprintf(" SELECT COUNT(msgid) FROM %s
										WHERE forumid ='%d' AND intranet_yn='%d' AND rootid='0' ",
			$this->cms->papoo_message,
			$this->forumid,
			$this->cms->intranet_yn);
		return $this->weiter->result_anzahl = $this->db->get_var($sql);
	}

	/**
	 * Hier wird das gesamte Forum ausgeben, das ausgewählt worden ist
	 * wahlweise als Board oder als Thread, ja nach Wahl in den Stammdaten
	 * $this->cms->forum_board  1= board 0=thread
	 *
	 * @return void
	 */
	function show_forum()
	{
		// für Template Forum ist ausgewählt
		$this->content->template['forumok'] = "ok";
		// Liste der verfügbaren Foren erstellen
		$this->forum_list();
		$this->forumid = $this->content->template['forumid'] = $this->checked->forumid;
		if (!in_array($this->forumid, $this->content->template['forumok'])) {
			$this->forumid = 0;
		}
		// Anzahl der messages rausholen
		$this->count_msg();
		// Datenbankabfrage formulieren
		$selectmessages = sprintf("SELECT t1.*, t2.username FROM %s AS t1, %s AS t2
									WHERE forumid ='%d' AND rootid='0' AND t1.userid=t2.userid
									ORDER BY t1.msgid DESC %s",
			$this->cms->papoo_message,
			$this->cms->papoo_user,
			$this->forumid,
			$this->cms->sqllimit
		);
		// Datenbank Abfrage durchführen, in ein assoziatives Array einlesen
		$resultmessages = $this->db->get_results($selectmessages, ARRAY_A);
		// Arrays initialisieren
		$messagefor_data = array();
		// Ergebnisse aus der Datenbankabfrage in ein Aray einlesen ...
		if (!empty ($resultmessages)) {
			foreach ($resultmessages as $tmp) {
				// Ergebnis im Array ablegen
				$this->forumarray[$tmp["msgid"]] = $tmp;
				// Vorwärtsbezüge konstruieren
				$this->kindarray[$tmp["parentid"]][] = $tmp["msgid"];
			}
		}
		// Variablen initialisieren...###
		$this->zahl_alt = "";
		// Wenn es Einträge gibt
		if (isset ($this->kindarray)) {
			// Und wenn die Einträge Array sind
			if (is_array($this->kindarray)) {
				// Dann für jeden Eintrag durchloopen
				foreach ($this->kindarray[0] as $thread) {
					// Für jedes Posting der obersten Ebene...
					// ... zeichneBaum() aufrufen
					// Daten aus der Methode holen
					$messagefor_data_merge = $this->zeichneBaum($thread);
					$this->zahl_alt = $this->zahl;
					$this->zahl = 0;
					// arrays mergen, um alle Informationen zu erhalten ... ###
					$messagefor_data = array_merge($messagefor_data, $messagefor_data_merge);
				}
			}
		}
		if ($this->cms->mod_rewrite == 1) {
			// Link zum Thread
			$this->content->template['link_form_log_thread'] = $this->content->template['forumthread'] = "forumthread.php";
			// Link zum Forum
			$this->content->template['link_form_log'] = $this->content->template['forum'] = "forum.php";
		}
		// schließende </ul>s generieren (nicht bei Board-Ansicht
		$close_all_ulvor = "";
		if (!($this->user->board == 1) AND ($messagefor_data[count($messagefor_data) - 1]['level'] > 0)) {
			$count = $messagefor_data[count($messagefor_data) - 1]['level'];
			for ($i = 0; $i < $count; $i++)
				$close_all_ulvor .= "</ul> ";
		}
		$this->content->template['close_all_ulvor'] = $close_all_ulvor;
		// Daten zuweisen für das Template
		$this->content->template['messagefor_data'] = $messagefor_data;
		// Link erstellen für die weiter Methode, um alle Links aus der Datenbank zu holen
		if ($this->cms->mod_rewrite == 2) {
			// für mod_rewrite / verwenden
			$this->weiter->weiter_link = "forum/forumid/" . $this->forumid . "/menuid/" . $this->checked->menuid;
		} // nicht mod_rewrite
		else {
			// dann normal mit ? & = arbeiten
			$this->weiter->weiter_link = "forum.php?forumid=" . $this->forumid . "&menuid=" . $this->checked->menuid;
		}
		// weitere Einträge zugänglich machen durch aufrufen der weiter Methode aus der Artikel Klasse
		$this->weiter->do_weiter("teaser");
	}

	/**
	 * Hier wird der Thread als Liste erstellt
	 *
	 * @param $eintrag
	 * @param int $aktuellerEintrag
	 * @return array
	 */
	function zeichneBaum($eintrag, $aktuellerEintrag = 0)
	{
		// Die hilfreichen Arrays importieren
		$messagefor_data = array();
		$ulvor = "";
		$weight = "400";
		// gleicher Level -- nix machen... ###
		if (empty ($this->zahl)) {
			$this->zahl = 0;
		}

		if ($this->zahl == $this->forumarray[$eintrag]["level"]) {
			$this->zahl = $this->forumarray[$eintrag]["level"];
			$ulvor = "";
		}
		// Eintrag hat einen höheren Level, also ein ul mehr... ###
		if ($this->zahl < $this->forumarray[$eintrag]["level"]) {
			$this->zahl = $this->forumarray[$eintrag]["level"];
			$ulvor = "<ul>";
		}
		// Eintrag hat einen niedrigeren Level, also ein ul oder mehrere uls runter... ###
		if ($this->zahl > $this->forumarray[$eintrag]["level"]) {
			$differenz = $this->zahl - $this->forumarray[$eintrag]["level"];
			for ($j = 0; $j < $differenz; $j++) {
				$ulvor .= "</ul>";
			}
			$this->zahl = $this->forumarray[$eintrag]["level"];
		}
		// neuer root level, also komplett runterulen ... ###
		if ($this->zahl_alt != "") {
			for ($j = 0; $j < $this->zahl_alt; $j++) {
				$ulvor .= "</ul>";
			}
			$weight = "600";
			// $ulvor .= "<br />"; // erzeugt nicht-valides XHTML !!!
			$this->zahl_alt = "";
		}
		$hier = "";
		if ($this->forumarray[$eintrag]["msgid"] == $this->checked->msgid) {
			$hier = "hier";
		}
		$zeitpar = explode("\.", $this->forumarray[$eintrag]["zeitstempel"]);

		IfNotSetNull($zeitpar['1']);

		if (is_numeric($zeitpar['1'])) {
			$datum1 = "-" . $zeitpar['1'] . "-" . $zeitpar['0'];
			$datum2 = str_ireplace("  ", "" . $datum1 . " ", $zeitpar['2']);
			$this->forumarray[$eintrag]["zeitstempel"] = $datum2;
		}

		array_push($messagefor_data, array('ulvor' => $ulvor,
			'level' => $this->forumarray[$eintrag]["level"],
			'weight' => $weight,
			'hier' => $hier,
			'rootid' => $this->forumarray[$eintrag]["rootid"],
			'msgid' => $this->forumarray[$eintrag]["msgid"],
			'forumid' => $this->forumarray[$eintrag]["forumid"],
			'menuid' => $this->checked->menuid,
			'username' => $this->forumarray[$eintrag]["username"],
			'zeitstempel' => $this->forumarray[$eintrag]["zeitstempel"],
			'counten' => $this->forumarray[$eintrag]["counten"],
			'thema_surl' => $this->menu->urlencode($this->forumarray[$eintrag]["thema"]),
			'thema' => $this->forumarray[$eintrag]["thema"]
		));
		// Eventuell sind noch Kinder mit auszugeben:
		if (isset ($this->kindarray[$eintrag])) {
			if (is_array($this->kindarray[$eintrag])) {
				foreach ($this->kindarray[$eintrag] as $kind) {
					// ... gehe alle Kinder durch ... ###
					// ... und rufe für jedes Kind zeichneBaum() auf, ... ###
					$messagefor_data_under = $this->zeichneBaum($kind, $aktuellerEintrag);
					// array mergen, damit alle ausgegeben werden können ... ###
					$messagefor_data = array_merge($messagefor_data, $messagefor_data_under);
				}
			}
		}
		return ($messagefor_data);
		// Fertig
	}

	/**
	 * Mit dieser Methode wird ein Thread erstellt und zur Bearbeitung benutzt
	 *
	 * @return void
	 */
	function make_forum_thread()
	{
		$this->content->template['suchbox'] = "";
		// menuid Übergeben
		$this->content->template['formmenuid'] = $this->checked->menuid;
		$this->content->template['forumid_tmpl2'] = $this->content->template['forumid_tmpl'] = $this->checked->forumid;
		// Username für das Formular
		$this->content->template['usernameid'] = $this->user->username;
		// wenn eine Nachricht Übertragen wurde
		if (empty ($this->checked->uebermittelformular)) {
			$this->checked->uebermittelformular = "";
		}
		if (empty ($this->checked->inhalt)) {
			$this->checked->inhalt = "";
		}
		$this->checked->formulartext = $this->checked->inhalt;

		if (!empty ($this->checked->uebermittelformular) and $this->checked->formthema and $this->checked->formulartext and $this->checked->formforumid and $this->user->userid) {
			// Nachricht eintragen
			$this->insert_update();
		} // wenn nicht
		else {
			// Liste der vorhandenen Foren erzeugen
			$this->forum_list();
			// Schreiben oder nicht
			$this->thread_edit();
			// Wenn eine Messsageid ausgewählt wurde

			if (!empty ($this->checked->msgid) and empty ($this->no_right_write)) {
				// Übergabe der Variablen

				$this->content->template['rootid'] = $rootid = $this->db->escape($this->checked->rootid);
				$this->content->template['msgid'] = $msgid = $this->db->escape($this->checked->msgid);
				$this->content->template['forumid_tmpl'] = $this->db->escape($this->checked->forumid);
				$this->content->template['forummenuid'] = $this->db->escape($this->checked->menuid);
				$papoo_message = $this->cms->papoo_message;
				$papoo_user = $this->cms->papoo_user;
				$papoo_forums = $this->cms->papoo_forums;
				// Arrays erzeugen
				$message_data = array();
				$messagefor_data = array();

				/**
				 * Die folgenden Abfragen sind sehr komplex, aber nötig um die Rechte zu wahren.
				 */
				// Wenn der erste Eintrag ausgewählt ist
				if ($rootid == 0) {
					$rootid = $msgid;
					$sqlmessage2 = "SELECT msgid, parentid, rootid, counten, thema, messagetext, comment_article, level, rootid, $papoo_message.zeitstempel  AS zeitstempel, $papoo_user.userid, username, forumname, $papoo_message.forumid AS forumid ";
					$sqlmessage2 .= "FROM $papoo_message, $papoo_user, $papoo_forums " . "WHERE";
					foreach ($this->content->template['forumok'] as $forumidsql) {
						$sqlmessage2 .= " ($papoo_message.rootid = '$rootid' OR $papoo_message.msgid = '$rootid') AND $papoo_message.userid = $papoo_user.userid AND " . "$papoo_message.forumid = $papoo_forums.forumid AND $papoo_message.intranet_yn='" . $this->cms->intranet_yn . "' AND $papoo_forums.forumid='$forumidsql' OR ";
					}
					// echo $sqlmessage2;
					if ($this->user->board == 1) {
						$sqlmessage2 .= " ($papoo_message.rootid = '0' OR $papoo_message.msgid = '$rootid') AND $papoo_message.userid = 'nix' AND " . "$papoo_message.forumid = $papoo_forums.forumid AND $papoo_message.intranet_yn='" . $this->cms->intranet_yn . "' AND $papoo_forums.forumid='0' ORDER BY msgid ASC";
						$result = $this->db->get_results($sqlmessage2);
					} else {
						$sqlmessage2 .= " ($papoo_message.rootid = '0' OR $papoo_message.msgid = '$rootid') AND $papoo_message.userid = 'nix' AND " . "$papoo_message.forumid = $papoo_forums.forumid AND $papoo_message.intranet_yn='" . $this->cms->intranet_yn . "' AND $papoo_forums.forumid='0' ORDER BY msgid DESC";
						$result = $this->db->get_results($sqlmessage2, ARRAY_A);
					}
				} // ein weiterer Beitrag ist ausgewählt
				else {
					$sqlmessage2 = "SELECT msgid, parentid, rootid, counten, thema, messagetext, comment_article, level, rootid, ";
					$sqlmessage2 .= "$papoo_message.zeitstempel  AS zeitstempel, $papoo_user.userid,  username, forumname, $papoo_message.forumid AS forumid ";
					$sqlmessage2 .= " FROM $papoo_message, $papoo_user, $papoo_forums WHERE";
					foreach ($this->content->template['forumok'] as $forumidsql) {
						$sqlmessage2 .= " ($papoo_message.rootid = '$rootid' OR $papoo_message.msgid = '$rootid') AND $papoo_message.userid = $papoo_user.userid AND " . "$papoo_message.forumid = $papoo_forums.forumid AND $papoo_message.intranet_yn='" . $this->cms->intranet_yn . "' AND $papoo_forums.forumid='$forumidsql' OR ";
					}
					// echo $sqlmessage2;
					if ($this->user->board == 1) {
						$sqlmessage2 .= " ($papoo_message.rootid = '0' OR $papoo_message.msgid = '$rootid') AND $papoo_message.userid = 'nix' AND " . "$papoo_message.forumid = $papoo_forums.forumid AND $papoo_message.intranet_yn='" . $this->cms->intranet_yn . "' AND $papoo_forums.forumid='0' ORDER BY msgid ASC ";
						$result = $this->db->get_results($sqlmessage2);
					} else {
						$sqlmessage2 .= " ($papoo_message.rootid = '0' OR $papoo_message.msgid = '$rootid') AND $papoo_message.userid = 'nix' AND " . "$papoo_message.forumid = $papoo_forums.forumid AND $papoo_message.intranet_yn='" . $this->cms->intranet_yn . "' AND $papoo_forums.forumid='0' ORDER BY msgid DESC ";
						$result = $this->db->get_results($sqlmessage2, ARRAY_A);
					}
				}
				// Die angewählte message ... sollte nur angezeigt werden, wenn die Rechte vorhanden sind... ###
				$sqlmessage = "SELECT msgid, rootid, counten, thema, comment_article, messagetext ,  " . "  $papoo_user.userid, username, forumname, $papoo_message.forumid, " . "       $papoo_message.zeitstempel AS zeitstempel " . "FROM $papoo_message, $papoo_user, $papoo_forums  " . "WHERE ";
				foreach ($this->content->template['forumok'] as $forumidsql) {
					$sqlmessage .= " $papoo_message.msgid='$msgid' AND $papoo_message.userid = $papoo_user.userid AND " . "$papoo_message.forumid = $papoo_forums.forumid AND $papoo_message.intranet_yn='" . $this->cms->intranet_yn . "' AND $papoo_forums.forumid='$forumidsql' OR ";
				}
				IfNotSetNull($forumidsql);
				$sqlmessage .= " $papoo_message.msgid='0'  AND $papoo_message.userid = '0' AND " . "$papoo_message.forumid = '0' AND $papoo_message.intranet_yn='" . $this->cms->intranet_yn . "' AND $papoo_forums.forumid='$forumidsql' LIMIT 2";
				// Message aus der Datenbank holen
				$result2 = $this->db->get_results($sqlmessage);
				// Wenn was schiefgelaufen ist
				if (is_array($result2) && count($result2) < 1 || is_countable($result2) && count($result2) < 1) {
					// Kein Zugriff erlaubt
					$this->content->template['schreiben'] = $this->content->template['message_6'];
				}
				// Wenn eine Nachricht existiert (oder mehrere)
				if (!empty ($result2)) {
					if ($this->user->board == 1) {
						foreach ($result2 as $row) {
							$thema = $row->thema;
							if (!empty($this->checked->edit)) $this->content->template['themaneu'] = $this->diverse->encode_quote($thema);
							else $this->content->template['themaneu'] = $this->diverse->encode_quote("Re: " . $thema);
						}
					} else {
						// Username für das Formular
						$this->content->template['usernameid'] = $this->user->username;
						foreach ($result2 as $row) {
							$this->make_gruppe_name($row->userid);

							$mesid = $this->db->escape($row->msgid);
							// Nachricht einmal zählen
							$this->db->query("UPDATE " . $this->cms->papoo_message . " SET counten=counten+1 WHERE msgid='$mesid'");
							// Thema und Inhalt zuweisen
							$thema = $row->thema;
							$this->content->template['themaneu'] = $this->diverse->encode_quote("Re: " . $thema);
							$msg = $row->messagetext;
							// Pfade zu Smily-Bildern anpassen
							$msg = $this->diverse->do_pfadeanpassen($msg);
							// Wenn der angemeldete User auch der Schreiber der Nachricht ist, diese zur Bearbeitung freigeben
							if ($row->username == $this->user->username) {
								$this->content->template['edit'] = 1;
							}
							$sql = sprintf("SELECT signatur_html FROM %s WHERE username='%s'",
								$this->db->escape($this->cms->tbname['papoo_user']),
								$this->db->escape($row->username)
							);
							// echo "EE";
							$signatur = "nodecode:" . ($this->db->get_var($sql));

							$zeitpar = explode("\.", $row->zeitstempel);

							if (is_numeric($zeitpar['1'])) {
								$datum1 = "-" . $zeitpar['1'] . "-" . $zeitpar['0'];
								$datum2 = str_ireplace("  ", "" . $datum1 . " ", $zeitpar['2']);
								$row->zeitstempel = $datum2;
							}
							// Ergebniss in ein Arra zuweisen
							array_push($message_data, array('reporeid' => $row->comment_article,
									'msgid' => $row->msgid,
									'username' => $row->username,
									'signatur' => $signatur,
									'zeitstempel' => $row->zeitstempel,
									'rootid' => $row->rootid,
									'forumid' => $row->forumid,
									'menuid' => $this->checked->menuid,
									'text' => $msg,
									'thema' => $thema,
									'counten' => $row->counten)
							);

							// Array für das Template zugänglich machen
							$this->content->template['message_data'] = $message_data;
							$this->content->template['site_title'] = "nobr:" . strip_tags($thema);
							$msgx = strip_tags($msg);
							$this->content->template['description'] = "nobr:" . substr($msgx, 0, 200) . "...";
						}
					}
				}
				// bisherige Antworten, wenn vorhanden
				if (!empty ($result)) {
					if ($this->user->board == 1) {
						$this->board_result = $result;
						$this->do_board();
					}
					else {
						// Ergebnisse aus der Datenbankabfrage in ein Aray einlesen ... ###
						foreach ($result as $tmp) {
							// Ergebnis holen
							$this->forumarray[$tmp["msgid"]] = $tmp;
							// Ergebnis im Array ablegen
							$this->kindarray[$tmp["parentid"]][] = $tmp["msgid"];
							// Vorwärtsbezüge konstruieren
						}
						$this->zahl = 0;
						$this->zahl_alt = "";
						// wenn ein Array existiert
						if (is_array($this->kindarray[0])) {
							// für jeden Eintrag durchloopen
							foreach ($this->kindarray[0] as $thread) {
								// Für jedes Posting der obersten Ebene...
								// ... zeichneBaum() aufrufen
								$messagefor_data_merge = $this->zeichneBaum($thread);
								$this->zahl_alt = $this->zahl;
								$this->zahl = 0;
								// Arrays zusammenführen
								$this->content->template['messagefor_data'] = array_merge($messagefor_data, $messagefor_data_merge);
								// print_r($this->content->template['messagefor_data']);
							}
						}
						// schließende </ul>s generieren (nicht bei Board-Ansicht)
						$close_all_ulvor = "";
						if (!($this->user->board == 1) AND ($this->content->template['messagefor_data'][count($this->content->template['messagefor_data']) - 1]['level'] > 0)) {
							$count = $this->content->template['messagefor_data'][count($this->content->template['messagefor_data']) - 1]['level'];
							for ($i = 0; $i < $count; $i++)
								$close_all_ulvor .= "</ul> ";
						}
						$this->content->template['close_all_ulvor'] = $close_all_ulvor;
					}
				}
				$this->content->template['link_form_edit'] = "forumthread.php?rootid=" . $this->checked->rootid . "&msgid=" . $this->checked->msgid .
					"&forumid=" . $this->checked->forumid . "&menuid=" . $this->checked->menuid . "";
			}
		}
	}

	/**
	 * Erstellen des Threads als Board Version, d.h. unterineander
	 *
	 * @return void
	 */
	function do_board()
	{
		$message_data = array();
		#print_r($this->board_result);
		$this->content->template['messageboard'] = "ja";
		// "globaler" Paramter für Editier-Rechte zurücksetzen
		$this->content->template['edit'] = "";

		$mesid = $this->db->escape($this->checked->msgid);
		// Nachricht einmal zählen
		$this->db->query("UPDATE " . $this->cms->papoo_message . " SET counten=counten+1 WHERE msgid='$mesid'");

		foreach ($this->board_result as $row) {
			if ($row->username == $this->user->username) {
				$the_edit = 1;
				$edit_link = "forumthread.php?rootid=" . $row->rootid . "&msgid=" . $row->msgid . "&forumid=" . $row->forumid . "&menuid=" . $this->checked->menuid . "";
			} else {
				$the_edit = 0;
				$edit_link = "";
			}

			$this->make_gruppe_name($row->userid);

			$msg = $row->messagetext;
			// Pfade der Smily-Bilder anpassen
			$msg = $this->diverse->do_pfadeanpassen($msg);
			$sql = sprintf("SELECT signatur_html FROM %s WHERE username='%s'",
				$this->db->escape($this->cms->tbname['papoo_user']),
				$this->db->escape($row->username)
			);
			$signatur = "nodecode:" . ($this->db->get_var($sql));
			$zeitpar = explode(".", $row->zeitstempel);

			IfNotSetNull($zeitpar['1']);

			if (is_numeric($zeitpar['1'])) {
				$datum1 = "-" . $zeitpar['1'] . "-" . $zeitpar['0'];
				$datum2 = str_ireplace("  ", "" . $datum1 . " ", $zeitpar['2']);
				$row->zeitstempel = $datum2;
			}

			// Ergebniss in ein Arra zuweisen
			array_push($message_data, array('signatur' => $signatur,
				'reporeid' => $row->comment_article,
				'msgid' => $row->msgid,
				'username' => $row->username,
				'zeitstempel' => $row->zeitstempel,
				'rootid' => $row->rootid,
				'forumid' => $row->forumid,
				'menuid' => $this->checked->menuid,//interna/spalte.php?menuid=17&col3_id=1&action_edit=1&extern=#tmp_sprun
				'text' => $msg,
				'thema' => $row->thema,
				'counten' => $row->counten,
				'edit' => $the_edit,
				'edit_link' => $edit_link,
				'board' => 'board',
				'gruppename' => $this->greturn,
				'gruppebild' => $this->breturn,

			));
			$this->content->template['site_title'] = "nobr:" . strip_tags($row->thema);
			$msgx = strip_tags($msg);
			$this->content->template['description'] = "nobr:" . substr($msgx, 0, 200) . "...";
		}
		$this->content->template['messagefor_data'] = $message_data;
	}

	/**
	 * Gibt im Forum den Gruppennamen aus.
	 *
	 * @param $userid
	 */
	function make_gruppe_name($userid)
	{
		// Gruppenname rausholen
		$sqlgr = "SELECT gruppenname FROM " . $this->cms->papoo_lookup_ug . "," . $this->cms->papoo_gruppe . "";
		$sqlgr .= " WHERE " . $this->cms->papoo_lookup_ug . ".userid=" . $this->db->escape($userid);
		$sqlgr .= " AND gruppeid=gruppenid";
		$sqlgr .= " ";
		$sqlgr .= "";
		$sqlgr .= "";
		$sqlgr .= "";

		$resultgr = $this->db->get_results($sqlgr);
		$this->breturn = $this->greturn = "";

		if (is_array($resultgr)) {
			foreach ($resultgr as $gr) {
				if ($gr->gruppenname != "jeder") {
					switch ($gr->gruppenname) {
						// bildgruppe
					case "Gold Club" :
						$this->breturn = $this->content->template['bildgruppe'] = "gold.jpg";
						break;


					case "Silber Club" :

						$this->breturn = $this->content->template['bildgruppe'] = "silber.jpg";
						break;

					default :
						$this->greturn = $this->content->template['forgruppenname'] = "<strong>" . $gr->gruppenname . "</strong>";
						break;
					}
				}
				else {
					$this->greturn = $this->content->template['forgruppenname'] = "";
				}
			}
		}
	}

	/**
	 * Hiermit kann ein neuer Beitrag geschrieben werden bzw. ein alter editiert werden
	 *
	 * @return void
	 */
	function thread_edit()
	{
		// Wenn kein User eingeloggt ist oder nur jeder
		if (empty ($this->user->userid) or $this->user->userid == "11") {
			// zuweisen fürs Template -- keine Rechte
			$this->content->template['schreiben2'] = $this->content->template['message_7'];
		} // Ein User ist eingeloggt,
		else {
			// herausfinden ob der User schreiben darf in dem ausgewählten Forum, ABfrage formulieren
			$selectschreib = "SELECT " . $this->cms->papoo_forums . ".forumid, forumname, schreib_rechte ";
			$selectschreib .= " FROM " . $this->cms->papoo_forums . ", " . $this->cms->papoo_lookup_forum_write . ", " . $this->cms->papoo_lookup_ug . " ";
			$selectschreib .= " WHERE " . $this->cms->papoo_forums . ".forumid = '" . $this->db->escape($this->checked->forumid) . "' ";
			$selectschreib .= " AND " . $this->cms->papoo_lookup_ug . ".userid= " . $this->db->escape($this->user->userid) . " ";
			$selectschreib .= " AND " . $this->cms->papoo_lookup_ug . ".gruppenid=" . $this->cms->papoo_lookup_forum_write . ".gruppenid";
			$selectschreib .= " AND  " . $this->cms->papoo_forums . ".forumid=" . $this->cms->papoo_lookup_forum_write . ".forumid ";
			$selectschreib .= " LIMIT 1";

			$resultschreib = $this->db->get_results($selectschreib);

			$schreibrechte = "";
			if (count($resultschreib) == 1) {
				$schreibrechte = count($resultschreib);
			}
			// Für jeden Eintrag mit den Userrechten vergleichen
			// Wenn der User Schreibrechte hat
			if ($schreibrechte == 1) {
				// Wenn editieren eines existierenden Beitrages ausgewählt ist
				if (empty ($this->checked->edit)) {
					$this->checked->edit = "";
				}
				if ($this->checked->edit == 1) {
					// Eintrag kann editiert werden
					$this->content->template['editeintrag'] = 'editeintrag';
					// Wenn eine Messageid ausgewählt worden ist
					if ($this->checked->msgid) {
						// INhalt der Message rausholen
						$sql = "SELECT msgid, messagetext, messagetext_bbcode, thema, level, rootid, comment_article, " . "  " .
							$this->cms->papoo_message . ".zeitstempel AS zeitstempel, " . "  forumid, username " . "FROM " .
							$this->cms->papoo_message . ", " . $this->cms->papoo_user . " " . "WHERE msgid = '" .
							$this->db->escape($this->checked->msgid) . "' " . "  AND " . $this->cms->papoo_message . ".userid = " .
							$this->cms->papoo_user . ".userid ";
						$sql .= " AND " . $this->cms->papoo_user . ".userid = '" . $this->user->userid . "'";
						// $sql .= "LIMIT 2";
						$sql .= "LIMIT 1";
						$result = $this->db->get_results($sql);

						$this->content->template['schreiben2'] = "x";
						// Wenn keine Nachricht existiert
						if (count($result) != 1) {
							// echo "WW";
							$this->no_right_write = "no";
							$this->content->template['schreiben2'] = $this->content->template['message_8'];
							// exit;
						}
						// Für den Eintrag die Daten zuweisen (alte Daten aus der Datenbank)
						if (!empty ($result)) {
							foreach ($result as $row) {
								$this->content->template['forumid_tmpl'] = $this->forumid = $row->forumid;
								$oldMsg = $row->messagetext;
								$oldMsg_bb = $row->messagetext_bbcode;
								$oldthema = $row->thema;
								$oldLevel = $row->level;
								$oldRoot = $row->rootid;
								$reporeid = $row->comment_article;
								$oldAuthor = $row->username;
								$oldDate = $row->zeitstempel;
							}
						}
					}
					IfNotSetNull($oldthema);
					IfNotSetNull($oldMsg);
					// Überprüfen ob die forumid valide ist, das Forum existiert
					$result = $this->db->get_results("SELECT forumid, forumname, schreib_rechte FROM " . $this->cms->papoo_forums . " " .
						"WHERE forumid = '$this->forumid' " . "LIMIT 2");
					if (count($result) != 1) {
						// Forum existiert nicht
						$this->content->template['schreiben2'] = $this->content->template['message9'];
						$this->diverse->not_found();
					}
					// Wenn eine Messageid ausgewählt ist
					if ($this->checked->msgid) {
						// Thema zuweisen
						$themaalt = ($oldthema);
						// fürs Template
						$this->content->template['themaalt'] = $themaalt;
					}
					// Wenn Übermittelt worden ist, aber kein Text drin war
					if ($this->checked->uebermittelformular and empty ($this->checked->formulartext)) {
						// Fehlermeldung ausgeben, kein text!
						$this->schreiben2 = $this->content->template['message_10'];
					}

					$newthema = "";
					// Wenn Übermittelt worden ist
					if ($this->checked->uebermittelformular) {
						// Thema bereinigen
						$newthema = $this->checked->formthema;
					} // ansonsten
					elseif ($this->checked->msgid) {
						$newthema = $oldthema;
						// Thema auf 78 Zeichen einkürzen
						if (strlen($newthema) > 78)
							$newthema = substr($newthema, 0, 78);
					}
					$this->content->template['themaneu'] = $this->diverse->encode_quote($newthema);
					// Wenn kein bbcode eingetragen wurde
					if (empty ($oldMsg_bb)) {
						// nur Text anzeigen
						// $newtext = strip_tags($oldMsg);
						$newtext = $oldMsg;
					} // ansonsten
					else {
						// bb_Code Text anzeigen
						$newtext = $oldMsg_bb;
					}
					// $newtext = htmlentities($newtext);
					// Text für Template Übergeben
					// $this->content->template['textneu'] = $this->diverse->encode_quote($newtext);
					$this->content->template['textneu'] = "nobr:" . $newtext;
					$this->content->template['einloggok'] = 1;
				}
				else {
					$this->content->template['einloggok'] = 1;
					// Wenn eine Message ausgewählt ist
					if (empty ($this->checked->msgid)) {
						$this->checked->msgid = "";
					}
					if ($this->checked->msgid) {
						$selectmessage = "SELECT msgid, messagetext, thema, level, rootid, comment_article, ";
						$selectmessage .= "  " . $this->cms->papoo_message . ".zeitstempel AS zeitstempel, ";
						$selectmessage .= "  forumid, username ";
						$selectmessage .= " FROM " . $this->cms->papoo_message . ", " . $this->cms->papoo_user . " ";
						$selectmessage .= " WHERE msgid = '" . $this->db->escape($this->checked->msgid) . "' ";
						$selectmessage .= " AND " . $this->cms->papoo_message . ".userid = " . $this->cms->papoo_user . ".userid ";
						$selectmessage .= " LIMIT 2";

						$result = $this->db->get_results($selectmessage);
						if (count($result) != 1) {
							// Nachricht existiert nicht
							$this->content->template['schreiben2'] = $this->content->template['message_8'];
							$this->diverse->not_found();
						}
					}
					$result = $this->db->get_results("SELECT forumid, forumname FROM " . $this->cms->papoo_forums . " " . "WHERE forumid = '" .
						$this->db->escape($this->checked->forumid) . "' " . "LIMIT 2");
					if (count($result) != 1) {
						// Forum existiert nicht
						$this->content->template['schreiben2'] = $this->content->template['message_9'];
						$this->diverse->not_found();
					}
				}
			}
			else {
				// Keine Rechte zum schreiben vorhanden
				$this->content->template['schreiben2'] = $this->content->template['message_11'];
				$this->content->template['schreiben'] = $this->content->template['message_11'];
			}
		}
	}

	/**
	 * Hier wird eine Nachricht in die Datenbank eingetragen
	 *
	 * @return void
	 */
	function insert_update()
	{
		// Varibale auf invalide setzen
		$invalidmsgid = 0;
		// Testen ob die Messageid in Ordnung ist
		if ($this->checked->formmsgid) {
			$select = "SELECT msgid FROM " . $this->cms->papoo_message . "  " . "WHERE msgid = '" . $this->db->escape($this->checked->formmsgid) . "' " . "LIMIT 2";
			$result = $this->db->get_results($select);
			// Wenn mehr als eine msgid vorhanden ist
			if (count($result) != 1) {
				// invalide
				$invalidmsgid = 1;
			}
		}
		// Blacklist Filter
		if ($this->blacklist->do_blacklist($this->checked->formthema) == "not_ok" or
			$this->blacklist->do_blacklist($this->checked->formulartext) == "not_ok" or
			$this->blacklist->do_blacklist($this->formulartext_bb) == "not_ok"
		) {
			$invalidmsgid = 1;
		}
		// Kein Intranet
		$intranet_yn = $this->cms->intranet_yn;

		$this->formulartext_bb = $this->checked->formulartext;
		// aus Sicherheits-Gründen < und > Zeichen in Platzhalter umwandeln
		$platzhalter = rand(0, time());
		$this->checked->formulartext = str_replace("<", $platzhalter . "_lt", $this->checked->formulartext);
		$this->checked->formulartext = str_replace(">", $platzhalter . "_gt", $this->checked->formulartext);

		$this->checked->formulartext = $this->bbcode->parse($this->checked->formulartext);
		// Hier <br />s die BB-Code erzeugt wieder zurück-wandeln
		$this->checked->formulartext = $this->diverse->recode_entity_n_br($this->checked->formulartext, "nobr");
		// aus Sicherheits-Gründen < und > Zeichen zurückwandeln
		$this->checked->formulartext = str_replace($platzhalter . "_lt", "&lt;", $this->checked->formulartext);
		$this->checked->formulartext = str_replace($platzhalter . "_gt", "&gt;", $this->checked->formulartext);
		// außerdem Thema umwandeln
		$this->checked->formthema = str_replace("<", "&lt;", $this->checked->formthema);
		$this->checked->formthema = str_replace(">", "&gt;", $this->checked->formthema);
		// Überflüssige Res rausflitschen
		$this->checked->formthema = $this->remake_res($this->checked->formthema);

		$this->checked->formulartext = $this->diverse->dolink($this->checked->formulartext);
		// Wenn kein alter Beitrag editiert wurde
		if ($this->checked->editeintrag != "editeintrag") {
			// Und alles vailde ist
			if (!$invalidmsgid) {
				// neue Nachricht eintragen

				$this->insert_new_message($this->checked->reporeid, $this->cms->title, $this->checked->formmsgid, $this->checked->formforumid, $this->user->userid,
					trim($this->checked->formthema), trim($this->checked->formulartext), trim($this->formulartext_bb), $this->checked->menuid, $intranet_yn);

				if ($this->cms->benach_neu_forum == 1) {
					$this->diverse->mach_nachricht_neu("Neuer Forum Eintrag", "Der Eintrag " . $this->checked->thema . "wurde angelegt.");
				}
			}
			$this->ext_search($this->checked->formmsgid);
			// Seite neu laden
			$location_url = $this->cms->webverzeichnis . "/forum.php?forumid=" . $this->checked->formforumid . "&menuid=" . $this->checked->menuid;
			// header("Location: ".$this->cms->webverzeichnis."/forum.php?forumid=".$this->formforumid."&menuid=".$this->checked->menuid);
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		} // alter Beitrag wurde editiert
		elseif ($this->checked->editeintrag == "editeintrag") {
			// alles ok
			if (!$invalidmsgid) {
				// alte Nachricht updaten
				$this->update_message(trim($this->checked->formthema), trim($this->checked->formulartext), trim($this->formulartext_bb), $this->checked->msgid);
			}
			$this->ext_search($this->checked->msgid);
			// Seite neu laden
			$location_url = $this->cms->webverzeichnis . "/forum.php?forumid=" . $this->checked->formforumid . "&menuid=" . $this->checked->menuid;
			// header("Location: ".$this->cms->webverzeichnis."/forum.php?forumid=".$this->checked->formforumid."&menuid=".$this->checked->menuid."");
			if ($_SESSION['debug_stopallredirect'])
				echo '<a href="' . $location_url . '">Weiter</a>';
			else
				header("Location: $location_url");
			exit;
		}
		// Nachricht anzeigen in forum.php
	}

	/**
	 * forum::ext_search()
	 *
	 * @param int $id
	 * @return void
	 */
	function ext_search($id = 0)
	{
		if (!empty($this->cms->tbname['plugin_ext_search_page'])) {
			//Diese Klasse setzt die Suche um!!
			require_once PAPOO_ABS_PFAD . "/plugins/extended_search/lib/class_search_create.php";

			//Klasse initialisieren und zur Verfügung stellen
			$search_create = new class_search_create();

			$search_create->create_page_front($id);
		}
	}

	/**
	 * intern_artikel::remove_cache_file()
	 * Cache Dateien entfernen
	 * @param string $pfad
	 * @param string $rootid
	 * @return void
	 */
	function remove_cache_file($pfad = "", $rootid = "")
	{
		$pfad = $pfad . "/cache/";
		$extension = ".html";
		$filerootid = "";
		$sql = sprintf("SELECT url_menuname FROM %s WHERE menuid_id='%d' AND lang_id='%d'",
			$this->cms->tbname['papoo_menu_language'],
			$this->db->escape($this->checked->menuid),
			$this->cms->lang_id
		);
		$urlmenu = $this->db->get_var($sql);
		if (is_numeric($rootid)) {
			$filerootid = $rootid;
		}
		if ($pfad && $filerootid) {
			if (is_dir($pfad)) {
				$handle = @opendir($pfad);

				while (false !== ($file = @readdir($handle))) {
					switch ($extension) {
					default:

						if (stristr($file, "rootid-" . $filerootid)) {
							@unlink($pfad . $file);
						}
						//$urlmenu
						if (stristr($file, $urlmenu . "_de")) {
							@unlink($pfad . $file);
						}
						break;
					}
				}
				@closedir($handle);
			}
		}
	}

	/**
	 * Überflüssige Re: s rausflitschen
	 *
	 * @param $dore
	 * @return mixed|string|string[]|null
	 */
	function remake_res($dore)
	{
		if (stristr($dore, "Re: ")) {
			$dore = str_ireplace("Re: ", "", $dore);
			$dore = "Re: " . $dore;
		}
		return $dore;
	}

	/**
	 * Neue Nachricht in die Datenbank eintragen
	 *
	 * @param $reporeid
	 * @param $title
	 * @param $elternid
	 * @param $forumid
	 * @param $userid
	 * @param $messagethema
	 * @param $messagetext
	 * @param $messagetext_bbcode
	 * @param $menuid
	 * @param $intranet_yn
	 * @return void
	 */
	function insert_new_message($reporeid, $title, $elternid, $forumid, $userid, $messagethema, $messagetext, $messagetext_bbcode, $menuid, $intranet_yn)
	{
		// ordnen durch die Zeit
		$tmp = explode(" ", microtime());
		$orderstr = $tmp[0] + $tmp[1];

		// wenn schon ein Eltern da ist

		if ($elternid) {
			$level = "";
			$elternid = $this->db->escape($elternid);
			// Nachricht raussuchen, mit der msgid=elternid
			$result = $this->db->get_results("SELECT * FROM " . $this->cms->papoo_message . " WHERE msgid=$elternid LIMIT 1");
			// Für den Eintrag durchgehen
			foreach ($result as $row) {
				// EIn level tiefer gehen
				$level = $row->level + 1;
				// ordnen durch die Zeit
				// $tmp = explode(" ", microtime());
				// $orderstr = $tmp[0] + $tmp[1];
				// Wurzel des neuen Eintrages
				$rootid = $row->rootid;
				// wenn rootid leer ist (neuer Eintrag), rootid = msgid
				if (!$rootid)
					$rootid = $row->msgid;
			}
			// Es ist keine Elternid vorhanden, erster neuer Eintrag
		}
		else {
			// Im Forum einen Thread hochzählen
			$hocht = sprintf("UPDATE %s SET forum_threads=forum_threads+1 WHERE forumid='%d'",
				$this->cms->papoo_forums,
				$this->db->escape($forumid)
			);
			$this->db->query($hocht);
			// alles auf null setzen
			$level = 0;
			$rootid = 0;
			$elternid = 0;
			$letzter_beitrag_zeit = $orderstr;
			$orderstr = '';
		}
		IfNotSetNull($rootid);
		IfNotSetNull($letzter_beitrag_zeit);
		// Keine tieferen Level als 31 erlauben
		if ($level <= 31) {
			// ordnen durch die Zeit
			// $tmp = explode(" ", microtime());
			// $orderstr = $tmp[0] + $tmp[1];
		}
		else {
			$level = 31;
			// ordnen durch die Zeit
			// $tmp = explode(" ", microtime());
			// $orderstr = $tmp[0] + $tmp[1];
		}


		if ($this->user->userid != 11) {
			// User einen hochzählen ( Beiträge werden gezählt)
			$hochzahl = sprintf("UPDATE %s SET beitraege=beitraege+1 WHERE userid='%d'",
				$this->cms->papoo_user, $userid
			);
			$this->db->query($hochzahl);
			// Datum erstellen
			//$heute = date("j.n.Y  G:i:s");
			// Smilies einbauen
			$messagetext = $this->html->do_smilies($messagetext);
			// Im Forum einen Beitrag hochzählen
			$hochb = sprintf("UPDATE %s SET forum_beitr=forum_beitr+1 WHERE forumid='%d'",
				$this->cms->papoo_forums, $this->db->escape($forumid)
			);
			$this->db->query($hochb);
			// Abfrage erstellen
			$intranet_yn = $this->cms->intranet_yn;
			$query = sprintf("INSERT INTO %s
						SET comment_article='%d',
						zeitstempel=NOW(),
						forumid='%d',
						parentid='%d',
						rootid='%d',
						userid='%d',
						thema='%s',
						messagetext='%s',
						messagetext_bbcode='%s',
						level='%d',
						ordnung='%s',
						intranet_yn='%s',
						letzter_beitrag_zeit='%s' ",
				$this->cms->papoo_message,
				$this->db->escape($reporeid),
				//$this->db->escape($heute),
				$this->db->escape($forumid),
				$this->db->escape($elternid),
				$this->db->escape($rootid),
				$this->db->escape($userid),
				$this->db->escape($messagethema),
				$this->db->escape($messagetext),
				$this->db->escape($messagetext_bbcode),
				$this->db->escape($level),
				$this->db->escape($orderstr),
				$this->db->escape($intranet_yn),
				$this->db->escape($letzter_beitrag_zeit)
			);
			// Abfrage durchführen
			$this->db->query($query);
			// aktuelle Messageid
			$mesaid = $this->db->insert_id;
			// Im Forum den aktuellen Beitrag einstellen
			$hochb = sprintf("UPDATE %s SET forum_last='$mesaid' WHERE forumid='%d'",
				$this->cms->papoo_forums,
				$this->db->escape($forumid)
			);
			$this->db->query($hochb);
			// Anzahl der Antworten updaten
			$query = "UPDATE  " . $this->cms->papoo_message . " SET answers=answers+1 WHERE msgid='$rootid' ";
			$this->db->query($query);
			// Letzter_Beitrag_Zeit und Letzter_Beitrag_ID der "Root-Nachricht" aktualisieren, falls kein neues Thema.
			if ($rootid) {
				$query = sprintf("UPDATE %s SET letzter_beitrag_zeit='%s', letzter_beitrag_id='%d'
												WHERE msgid='%d' ",
					$this->cms->papoo_message,
					$this->db->escape($orderstr),
					$this->db->escape($mesaid),
					$this->db->escape($rootid)
				);
				$this->db->query($query);
			}
		}
		$this->remove_cache_file(PAPOO_ABS_PFAD, $rootid);
		// Wenn der Eintrag eine Antwort ist, Nachricht an Ersteintrag senden
		if ($elternid != 0) {
			// Nachricht senden
			$this->mail_it->send_answer_forum($elternid, $rootid, $mesaid, $forumid, $menuid, $this->cms->title);
		}
	}

	/**
	 * Alte Nachricht bearbeitet eintragen
	 *
	 * @param $messagethema
	 * @param $messagetext
	 * @param $messagetext_bbcode
	 * @param $msgid
	 * @return void
	 */
	function update_message($messagethema, $messagetext, $messagetext_bbcode, $msgid)
	{
		// Datum feststellen
		//$heute = date("j.n.Y  G:i:s");
		$msgid = $this->db->escape($msgid);
		// Smilies einbauen
		$messagetext = $this->html->do_smilies($messagetext);
		//$query = " UPDATE " . $this->cms->papoo_message . " SET zeitstempel='$heute', thema='" . $this->db->escape($messagethema) . "',";
		$query = " UPDATE " . $this->cms->papoo_message . " SET zeitstempel=NOW(), thema='" . $this->db->escape($messagethema) . "',";
		$query .= " messagetext ='" . $this->db->escape($messagetext) . "', messagetext_bbcode='" . $this->db->escape($messagetext_bbcode) . "' ";
		$query .= " WHERE msgid='$msgid'";
		// eintragen
		$this->db->query($query);
		$this->remove_cache_file(PAPOO_ABS_PFAD, $this->checked->rootid);
	}
}

$forum = new forum();