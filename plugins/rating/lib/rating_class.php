<?php

/**
 * Class rating_class
 */
class rating_class
{
	/**
	 * rating_class constructor.
	 */
	function __construct()
	{
		global $cms, $db, $content, $checked, $template, $message, $module, $weiter;

		$this->cms = &$cms;
		$this->db = &$db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->message = &$message;
		$this->module = &$module;
		$this->weiter = &$weiter;

		if (!stristr( $template,"rating/")) {
			return;
		} // raus, wenn nicht dieses Plugin

		$this->content->template['menuid_aktuell'] = $this->checked->menuid;

		if (defined("admin")) {
			global $template;
			$template2 = str_ireplace(PAPOO_ABS_PFAD . "/plugins/rating/templates/", "", $template);
			switch ($template2) {
				// show first page 
			case "rating_back.html" :
				break;
				// manage configuration
			case "rating_back_config.html" :
				$this->config_ratings();
				break;
				// list/manage all ratings
			case "rating_back_list.html" :
				$this->list_ratings();
				break;
			}
		}
	}

	function config_ratings()
	{
		IfNotSetNull($this->checked->time_lock);
		IfNotSetNull($this->checked->submit_config);

		if (!is_numeric($this->checked->time_lock) AND $this->checked->submit_config) {
			$this->content->template['fehler1'] = $fehler = 1;
		} // time lock value not numeric
		if (!isset($fehler) AND $this->checked->submit_config || isset($fehler) && !$fehler AND $this->checked->submit_config) { // korrekte Daten wurden abgeschickt
			$rating_active = $this->checked->rating_active ? 1 : 0; // normalisieren
			// Werte speichern
			$sql = sprintf("UPDATE %s SET time_lock = '%d',
											rating_active = '%d'
											WHERE id = 1",

				$this->cms->tbname['papoo_rating_config'],

				$this->db->escape($this->checked->time_lock),
				$rating_active
			);
			$this->db->query($sql);
		}
		// Daten aus der DB holen &  ans Konfig.-Template übertragen
		$sql = sprintf("SELECT time_lock,
								rating_active
								FROM %s",

			$this->cms->tbname['papoo_rating_config']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['time_lock'] = $result[0]['time_lock'];
		$this->content->template['rating_active'] = $result[0]['rating_active'];
	}

	function list_ratings()
	{
		// Sollen ein/mehrere Sätze gelöscht oder die Anzeige unterdrückt werden?
		if (isset($this->checked->rating_save) && $this->checked->rating_save) {
			// Löschen
			if (isset($this->checked->rating_del_id) && count($this->checked->rating_del_id)) {
				// Sätze löschen
				foreach ($this->checked->rating_del_id as $key => $value) {
					if ($value AND is_numeric($key)) {
						$sql = sprintf("DELETE FROM %s WHERE rating_id = '%d'",

							$this->cms->tbname['papoo_rating_plugin'],

							$this->db->escape($key)
						);
						$this->db->query($sql);
					}
				}
			}
			// Anzeige-Unterdrückung
			if (count($this->checked->rating_suppress_id)) {
				foreach ($this->checked->rating_suppress_id as $key => $value) {
					if (is_numeric($value) AND is_numeric($key)) {
						$sql = sprintf("UPDATE %s SET dont_show = '%d'
														WHERE rating_id = '%d'",

							$this->cms->tbname['papoo_rating_plugin'],

							$this->db->escape($value),
							$this->db->escape($key)
						);
						$this->db->query($sql);
					}
				}
			}
		}
		// Bewertungen, Sterne, Durchschnitt etc. berechnen
		// Alle Daten holen
		$sql = sprintf("SELECT * FROM %s
								ORDER BY page_title",
			$this->cms->tbname['papoo_rating_plugin']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (count($result)) {
			IfNotSetNull($sum_stars_all);
			IfNotSetNull($sum_ratings_all);
			foreach ($result as $key => $value) {
				// Summe der Sterne dieser Seite berechnen
				$sum_ratings = $value['one_star']
					+ ($value['two_stars'] * 2)
					+ ($value['three_stars'] * 3)
					+ ($value['four_stars'] * 4)
					+ ($value['five_stars'] * 5);
				$sum_stars_all = $sum_stars_all + $sum_ratings; // add Anzahl Sterne gesamt
				$sum_ratings_all = $sum_ratings_all
					+ $value['one_star']
					+ $value['two_stars']
					+ $value['three_stars']
					+ $value['four_stars']
					+ $value['five_stars']; // add Anzahl Bewertungen gesamt
			}
			IfNotSetNull($sum_stars_all);
			IfNotSetNull($sum_ratings_all);

			$this->content->template['sum_stars_all'] = $sum_stars_all;//Gesamtanzahl Sterne aller Bewertungen
			$this->content->template['sum_ratings_all'] = $sum_ratings_all; // Gesamzanzahl abgegebener Bewertungen
			$this->content->template['rating_all'] = round($sum_stars_all / $sum_ratings_all, 1); // Bewertungs-Durchschnitt aller abgegebener Bewertungen
			$this->content->template['anzahl'] = $this->weiter->result_anzahl = count($result); // Gesamtanzahl bewerteter Seiten, auch an die weiter class
			// weitere Seiten? Dann Paginating anzeigen
			$this->weiter->weiter_link = "plugin.php"
				. "?menuid="
				. $this->checked->menuid
				. "&template="
				. $this->checked->template;
			$this->weiter->do_weiter("teaser");
			// Daten holen (Anzahl je Seite lt. sqllimit)
			$sql = sprintf("SELECT * FROM %s
									ORDER BY page_title
									%s",
				$this->cms->tbname['papoo_rating_plugin'],
				$this->db->escape($this->cms->sqllimit)
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			if (count($result)) { // sicherheitshalber nochmal
				foreach ($result as $key => $value)
				{
					// Summe der Sterne dieser Seite berechnen
					$sum_ratings = $value['one_star']
						+ ($value['two_stars'] * 2)
						+ ($value['three_stars'] * 3)
						+ ($value['four_stars'] * 4)
						+ ($value['five_stars'] * 5);
					$sum_stars_all = $sum_stars_all + $sum_ratings; // Sterne gesamt
					// Durchschnitt dieser Seite berechnen
					$result[$key]['rating_value'] = round($sum_ratings / $value['rating_count'], 1);
					$result[$key]['sum_ratings'] = $sum_ratings; // Anzahl Sterne dieser Seite
				}
				$this->content->template['rating_data'] = $result; // alles ans Template
			}
		}
	}

	function post_papoo()
	{
		if(!isset($this->content->template['reporeid_print'])) {
			$this->content->template['reporeid_print'] = NULL;
		}

		// Sind Module aktiv?
		if (!empty($this->module->module_aktiv)) {
			foreach($this->module->module_aktiv as $modul_name => $modul_aktiv) {
				if (strpos($modul_name, "rating") !== false) {
					$mod_active = true;
				} // Plugin ist da
			}
		}
		IfNotSetNull($mod_active);
		// Exec nur bei install. Plugin
		if ($mod_active) {
			// URI ist der eindeutige Key für alle Bewertungen
			// Zwischenspeichern, da via Ajax sonst die URI vom Modul kommt (.../mod_rating.html)
			// Irgendwas /variables_class?) zerstört die URI wegen der enthaltenen &amp;'s, daher hab ich die mit "ABCDE" in mod_rating.html ersetzt
			// Jetzt wieder zurück korrigieren
			IfNotSetNull($this->checked->uri);
			$this->checked->uri = str_replace("ABCDE", "&", $this->checked->uri);
			$this->content->template['uri'] = $_SERVER['REQUEST_URI'];
			$this->content->template['server_path'] = $_SERVER['SERVER_NAME'] . PAPOO_WEB_PFAD; // ans Javascript für Ajax-Aufruf nach einer Bewertung
			if ($this->checked->uri) {
				$uri = $this->checked->uri;
			}
			else {
				$uri = $this->content->template['uri'];
			}
			// Startseite? Dann als solche kennzeichnen (wird im Link genutzt)
			$this->content->assign();
			global $smarty;
			$startseite = $smarty->get_template_vars("startseite");
			// uri bei Startseite
			if (isset($this->checked->startseite) && $this->checked->startseite OR isset($startseite) && $startseite) {
				$this->checked->uri = $uri = "../index.php";
			}
			// Liegen bereits Daten hierfür vor?
			$sql = sprintf("SELECT * FROM %s
									WHERE uri = '%s'",
				$this->cms->tbname['papoo_rating_plugin'],
				$this->db->escape($uri)
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			// Keine Bewertung für Suchergebnisse
			if (strpos($uri, "index.php?search=")!==false) {
				$result[0]['dont_show'] = 1;
			}
			// Keine Bewertung für vom Admin unterdrückte Seiten
			if (!isset($result[0]['dont_show']) || isset($result[0]['dont_show']) && !$result[0]['dont_show']) {
				// Seitentitel zwischenspeichern
				$header = $smarty->get_template_vars("site_title");
				// Seitentitel zwischensdpeichern
				$this->content->template['header'] = $header;
				if (isset($this->checked->header) && $this->checked->header) {
					$header = $this->checked->header;
				}

				// Feststellen, ob eine mehrfache Bewertung innerhalb der Konfig-Angabe in Sekunden versucht wird
				$sql = sprintf("SELECT time_lock,
										rating_active
										FROM %s",
					$this->cms->tbname['papoo_rating_config']
				);
				$result2 = $this->db->get_results($sql, ARRAY_A);
				$this->content->template['rating_active'] = $result2[0]['rating_active'];
				$time_lock = $result2[0]['time_lock'];
				$aktuelle_zeit = time();
				$abgelaufen = $aktuelle_zeit - $time_lock;
				// alle abgelaufenen löschen
				$sql = sprintf("DELETE FROM %s
										WHERE ts <= '%d'",

					$this->cms->tbname['papoo_rating_time_locks'],

					$this->db->escape($abgelaufen)
				);
				$this->db->query($sql);
				// Schauen, ob zu dieser IP schon eine Bewertung für die aktuelle Seite vorliegt
				$ip = $_SERVER['REMOTE_ADDR'];
				$sql = sprintf("SELECT * FROM %s
										WHERE ip = '%s'
										AND uri = '%s'",

					$this->cms->tbname['papoo_rating_time_locks'],

					$this->db->escape($ip),
					$this->db->escape($uri)
				);
				$ip_check = $this->db->get_results($sql, ARRAY_A);
				// Mehrfach-Bewertungen innerhalb des Zeitraums, angegeben in der Konfig, verhindern
				if (count($ip_check)) {
					$this->content->template['ip_noch_gesperrt'] = $ip_noch_gesperrt = 1;
				} // Meldung ans Template und ans Javascript
				// Wird in der DB gespeichert. Wird das noch gebraucht?
				if ($this->content->template['reporeid_print']) {
					$reporeid = $this->content->template['reporeid_print'];
				}
				elseif ($this->checked->reporeid) {
					$reporeid = $this->checked->reporeid;
				}
				// Wenn eine Bewertung abgegeben wurde
				if (isset($this->checked->rating_value) && $this->checked->rating_value AND $this->checked->uri AND !$this->content->template['ip_noch_gesperrt']) {
					// Zeitpunkt der 1. Bewertung für die aktuelle Seite und diese IP festhalten
					$sql = sprintf("INSERT INTO %s SET ip = '%s',
													uri = '%s',
													ts = '%s'",
						$this->cms->tbname['papoo_rating_time_locks'],
						$this->db->escape($ip),
						$this->db->escape($uri),
						$this->db->escape($aktuelle_zeit)
					);
					$this->db->query($sql);
					$extra_sql = "";
					// Anzahl der Sterne + 1
					if (!is_numeric($this->checked->rating_value)) {
						$this->checked->rating_value = 0;
					} // normalize
					if ($this->checked->rating_value == 1) {
						$extra_sql = "one_star = one_star + 1,";
					}
					if ($this->checked->rating_value == 2) {
						$extra_sql = "two_stars = two_stars + 1,";
					}
					if ($this->checked->rating_value == 3) {
						$extra_sql = "three_stars = three_stars + 1,";
					}
					if ($this->checked->rating_value == 4) {
						$extra_sql = "four_stars = four_stars + 1,";
					}
					if ($this->checked->rating_value == 5) {
						$extra_sql = "five_stars = five_stars + 1,";
					}
					IfNotSetNull($reporeid);
					if (count($result)) {
						// Daten sind bereits vorhanden -> Update
						// die Artikel ID könnte sich inzwischen geändert haben, daher auch speichern
						$sql = sprintf("UPDATE %s SET %s
														page_title = '%s',
														article_id = '%d',
														rating_count = rating_count + 1
														WHERE uri = '%s'",
							$this->cms->tbname['papoo_rating_plugin'],
							$this->db->escape($extra_sql),
							$this->db->escape($header),
							$this->db->escape($reporeid),
							$this->db->escape($this->checked->uri)
						);
						$this->db->query($sql);
					}
					else {
						// Noch keine Daten bisher vorhanden -> Insert
						$sql = sprintf("INSERT INTO %s SET article_id = '%d',
														uri = '%s',
														page_title = '%s',
														%s
														rating_count = 1",
							$this->cms->tbname['papoo_rating_plugin'],
							$this->db->escape($reporeid),
							$this->db->escape($this->checked->uri),
							$this->db->escape($header),
							$this->db->escape($extra_sql)
						);
						$this->db->query($sql);
					}
					// Aktuelle Daten holen für die Anzeige
					$sql = sprintf("SELECT * FROM %s
											WHERE uri = '%s'",

						$this->cms->tbname['papoo_rating_plugin'],

						$this->db->escape($uri)
					);
					$result = $this->db->get_results($sql, ARRAY_A);
				}
				if (!count($result)) {
					$result[0]['rating_value'] = $result[0]['rating_count'] = 0;
				} // Keine Daten vorhanden
				else {
					// Summe der Sterne berechnen
					$sum_ratings = $result[0]['one_star']
						+ ($result[0]['two_stars'] * 2)
						+ ($result[0]['three_stars'] * 3)
						+ ($result[0]['four_stars'] * 4)
						+ ($result[0]['five_stars'] * 5);
					// auf eine Kommastelle gerundeten Durchschnittswert ausgeben
					$result[0]['rating_value'] = round($sum_ratings / $result[0]['rating_count'], 1);
					$result[0]['sum_ratings'] = $sum_ratings; // for later use
				}
				$result[0]['rating_value'] = $result[0]['rating_value'] ? $result[0]['rating_value'] : 0;
				$result[0]['rating_count'] = $result[0]['rating_count'] ? $result[0]['rating_count'] : 0;
				// für Ajax-Ausgabe via JS (nur bei der Abgabe einer Bewertung).
				// Format: nicht relevante Daten || Bewertungs-Durchschnitt || Anzahl abgegebener Bewertungen || nicht relevante Daten
				// wird via JS / Ajax wieder raus geflöht
				if (isset($this->checked->rating_value) && $this->checked->rating_value AND $this->checked->uri) {
					IfNotSetNull($ip_noch_gesperrt);
					echo "||" . $result[0]['rating_value'] . "||" . $result[0]['rating_count'] . "||" . $ip_noch_gesperrt . "||";
				}
			}
			$this->content->template['rating_data'] = $result[0]; // ans Template
		}

		if(!isset($this->content->template['reporeid_print'])) {
			$this->content->template['reporeid_print'] = NULL;
		}
	}
}

$rating = new rating_class();
