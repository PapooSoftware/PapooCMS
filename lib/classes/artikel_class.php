<?php
/**
 * ######################################
 * # CMS Papoo							#
 * # (c) Dr. Carsten Euwens 2008		#
 * # Authors: Carsten Euwens			#
 * # http://www.papoo.de 				#
 * # Internet 							#
 * ######################################
 * # PHP Version 4.2 					#
 * ######################################
 */

/**
 * Class artikel_class
 */
class artikel_class
{
	/**
	 * artikel_class constructor.
	 */
	function __construct()
	{
		global $cms, $db, $user, $weiter, $content, $searcher, $checked, $counter,
				$replace, $download, $mail_it, $bbcode, $diverse, $menu, $spamschutz;

		// cms Klasse einbinden
		$this->cms = &$cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		$this->db =& $db;
		// User Klasse einbinden
		$this->user =& $user;
		// weitere Seiten Klasse einbinden
		$this->weiter =& $weiter;
		// inhalt Klasse einbinden
		$this->content =& $content;
		// Suchklasse einbinden
		$this->searcher =& $searcher;
		// checkedblen Klasse einbinden
		$this->checked =& $checked;
		// Zählen Klasse
		$this->counter =& $counter;
		// Ersetzungsklasse einbinden
		$this->replace =& $replace;
		// Download-Klasse einbinden
		$this->download =& $download;
		// Mail Klasse einbinden
		$this->mail_it =& $mail_it;
		// bbcode Klasse einbinden
		$this->bbcode =& $bbcode;
		// Diverse-Klasse einbinden
		$this->diverse =& $diverse;
		// Menü-Klasse einbinden
		$this->menu =& $menu;
		// Spamschutz-Klasse einbinden
		$this->spamschutz =& $spamschutz;

		if(!isset($this->checked->reporeid)) {
			$this->checked->reporeid = 0;
		}
		if(!isset($this->content->template['reporeid_print'])) {
			$this->content->template['reporeid_print'] = NULL;
		}
		if(!isset($this->content->template['articlesend'])) {
			$this->content->template['articlesend'] = NULL;
		}
		if(!isset($this->content->template['gesendet'])) {
			$this->content->template['gesendet'] = NULL;
		}
		if(!isset($this->content->template['comment_ok'])) {
			$this->content->template['comment_ok'] = NULL;
		}

		$this->content->template['articlesend'] = NULL;
		$this->content->template['reporeid_aktuell'] = $this->checked->reporeid ? $this->checked->reporeid : 0;
	}

	/**
	 * Hauptfunktion, Ergebnisse produzieren
	 */
	function make_inhalt()
	{
		$this->make_redirect();
		// Wenn der Artikel gesendet werden soll id übergeben
		if (!empty($this->checked->reporeid_send)) {
			$this->checked->reporeid = $this->checked->reporeid_print;
		}
		// wenn Suche dann suchen
		if (!empty($this->checked->search)) {
			IfNotSetNull($this->checked->searchfor);
			IfNotSetNull($this->checked->SortFeld);
			IfNotSetNull($this->checked->ascdesc);

			$_SESSION['pagesize'] = $this->cms->pagesize;
			if ($this->checked->searchfor != "") {
				$_SESSION['searchfor'] = $this->checked->searchfor;
			}
			if ($this->checked->SortFeld != "") {
				$_SESSION['SortFeld'] = $this->checked->SortFeld;
			}
			if ($this->checked->ascdesc != "") {
				$_SESSION['ascdesc'] = $this->checked->ascdesc;
			}
			$this->content->template['table_data'] = $this->find_artikel($this->checked->search);
		}
		// Wenn nicht dann Standard
		else {
			// wenn menuid leer ist dann Variable setzen
			if (empty($this->checked->menuid)) {
				$this->checked->menuid = 1;
			}
			if (empty($this->checked->reporeid)) {
				$this->checked->reporeid = "";
			}

			// Unterseite oder Artikel ausgewählt, dann anzeigen
			if ($this->checked->menuid != '1' or $this->checked->reporeid != "") {
				// Artikel aus der Datenbank fischen
				$this->content->template['table_data'] = $this->check_artikel();
			}
			// wenn Startseite, dann Startseite anzeigen
			else {
				// startseite erzeugen
				$this->content->template['table_data'] = $this->make_start();

				if ($this->cms->stamm_cat_startseite_ok == 1) {
					$this->content->template['cat_teaser_data'] = $this->make_cat_teaser();
					$this->content->template['mit_cat'] = "ok";
				}
				$this->content->template['startseite'] = "ok";
			}
			$this->content->template['menuid_aktuell'] = $this->checked->menuid;
		}
		if ($this->cms->stamm_cat_startseite_ok == 1) {
			$this->content->template['mit_cat'] = "ok";
		}
		
		if ($this->cms->stamm_cat_sub_ok == 1) {
			$this->content->template['mit_cat'] = "ok";
		}

		if(!isset($this->content->template['mit_cat'])) {
			$this->content->template['mit_cat'] = NULL;
		}

		if(!isset($this->content->template['such'])) {
			$this->content->template['such'] = NULL;
		}
		$this->makeOpenGraphMetaTags();
	}

	/**
	 * cms::make_redirect()
	 * Diese Methode Überprüft ob surls aktiviert sind
	 * und macht einen redirect wenn index.php mit
	 * menuid oder reporeid aufgerufen wird um DC zu vermeiden
	 * Ausnahmen sind print und search.
	 *
	 * @return void
	 */
	function make_redirect()
	{
		$startseite_indexphp = str_replace("//", "/", $this->cms->webverzeichnis . "/index.php");
		if ($this->cms->mod_surls == 1 || $this->cms->mod_free == 1) {
			if (stristr($_SERVER['REQUEST_URI'], "index.php") &&
				!stristr($_SERVER['REQUEST_URI'], "print=ok") &&
				!stristr($_SERVER['REQUEST_URI'], "reporeid_send") &&
				!stristr($_SERVER['REQUEST_URI'], "search") &&
				!stristr($_SERVER['REQUEST_URI'], "pdf") &&
				!stristr($_SERVER['REQUEST_URI'], "download_zip_galID") &&
				!stristr($_SERVER['REQUEST_URI'], "edit") &&
				!stristr($_SERVER['REQUEST_URI'], "downloadid") &&
				($_SERVER['REQUEST_URI'] != $startseite_indexphp) &&
				!stristr($_SERVER['REQUEST_URI'], "ps_act") &&
				!stristr($_SERVER['REQUEST_URI'], "logoff") &&
				!stristr($_SERVER['REQUEST_URI'], "gesendet=1") &&
				!stristr($_SERVER['REQUEST_URI'], "&easyedit") &&
				empty($this->checked->uebermittelformular) &&
				empty($this->checked->no_output)) {
				// Sprechende url des Artikels
				// Sprechende url des Menüpunktes
				$url_menu = "";
				if (is_numeric($this->checked->menuid)) {
					foreach ($this->menu->data_front_complete as $key => $value) {
						if ($this->checked->menuid == $value['menuid']) {
							$url_menu = PAPOO_WEB_PFAD . "/" . $value['menuname_url'];
							if ($this->cms->mod_free == 1) {
								$url_menu = PAPOO_WEB_PFAD . "/" . $value['menuname_free_url'];
							}
						}
					}
				}
				$url_menu=str_ireplace(".html.html",".html",$url_menu);

				if (is_numeric($this->checked->reporeid) &&
					!stristr($url_menu, ".html") &&
					$this->cms->mod_free != 1) {

					$sql = sprintf("SELECT url_header FROM %s WHERE lan_repore_id='%d' AND lang_id='%d'",
						$this->cms->tbname['papoo_language_article'],
						$this->db->escape($this->checked->reporeid),
						$this->cms->lang_id
					);
					$url_db = $this->db->get_var($sql);
					$url_art = $url_db . ".html";
				}
				if ($this->cms->mod_free == 1 &&
					is_numeric($this->checked->reporeid) &&
					!stristr($url_menu, ".html") &&
					$this->checked->reporeid > 0) {

					$sql = sprintf("SELECT url_header FROM %s WHERE lan_repore_id='%d' AND lang_id='%d'",
						$this->cms->tbname['papoo_language_article'],
						$this->db->escape($this->checked->reporeid),
						$this->cms->lang_id);
					$url_db = $this->db->get_var($sql);
					$url_art = $url_db;

					$sql = sprintf("SELECT COUNT(lcat_id) FROM %s WHERE lart_id='%d'",
						$this->cms->tbname['papoo_lookup_art_cat'],
						$this->db->escape($this->checked->reporeid)
					);
					$count = $this->db->get_var($sql);

					if ($count > 1) {
						$klammer_id = "[" . $this->checked->menuid . "]";

						$last = substr($url_art, -1, 1);
						$last2 = substr($url_art, -5, 5);

						//Artikel mit .html
						if ($last2 == ".html") {
							$url_art = str_replace(".html", "-" . $klammer_id . ".html", $url_art);
						}
						//Artikel mit /
						if ($last == "/") {
							$url_art = substr($url_art, 0, -1);
							$url_art = $url_art . "-" . $klammer_id . "/";
						}
					}
				}
				if ($this->cms->mod_free == 1) {
					if (is_numeric($this->checked->reporeid) && $this->checked->reporeid > 0 && !empty($url_art)) {
						$uri = "http://" . $this->cms->title . PAPOO_WEB_PFAD . $url_art;
					}
					else {
						$url_menu = str_replace(PAPOO_WEB_PFAD, "", $url_menu);
						$uri = "http://" . $this->cms->title . PAPOO_WEB_PFAD . $url_menu;
					}
					if (empty($url_art) && empty($url_menu)) {
						$uri = "http://" . $this->cms->title . PAPOO_WEB_PFAD . "/";
					}
				}
				else {
					IfNotSetNull($url_art);
					$uri = "http://" . $this->cms->title . "" . $url_menu . $url_art;
				}
				if ($_SESSION['sessionuserid'] == 1) {
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: " . $uri);
					header("Connection: close");
					exit;
				}
			}
		}
	}

	/**
	 * Datenbank durchsuchen und Ergebnisse auswerfen
	 * @param $search
	 * @return array|void
	 */
	public function find_artikel($search)
	{
		// Datenbank dursuchen und Ergebnisse abholen
		$ergebniss = $this->searcher->do_search(strip_tags($this->checked->search), $this->checked->searchfor);

		if ($this->checked->erw_suchbereich != 2) {
			// Anzahl der Ergebnisse aus der Suche Eigenschaft übergeben
			$this->weiter->result_anzahl = $this->searcher->result_anzahl;

			// Anzahl der Matches an Template übergeben zur Anzeige der Gesamtanzahl
			// und zur Berechnung der anzuzeigenden Seiten in eigenem Template _module_intern/mod_weiter.html
			$this->content->template['matches'] = $this->searcher->result_anzahl;
			// Suchwort(e) bergeben
			$this->suchwort = strip_tags($this->checked->search);
			// Übergabe suchwort(e) ans Template
			$this->content->template['suchworte'] = $this->suchwort;
			// wenn weitere Ergebnisse angezeigt werden können
			$this->weiter->weiter_link = "index.php?menuid=" . $this->checked->menuid;
			// wenn es sie gibt, weitere Seiten anzeigen
			$what = "search";
			$this->weiter->do_weiter($what);
		}
		if ($this->checked->erw_suchbereich == 2) {
			$this->weiter->weiter_link = "index.php?menuid=" . $this->checked->menuid;
		}
		// setzen, dass Suche ausgewählt ist
		$this->content->template['such'] = 1;
		// zuweisen der Inhalte aus der Suchfunktion als array
		return $ergebniss;
	}

	/**
	 * Diese Methode versendet einen Artikel
	 * @return void
	 */
	public function send_artikel()
	{
		// Daten aus der Datenbank fischen
		$select = sprintf("SELECT * FROM %s, %s WHERE publish_yn_lang='1'
							AND allow_publish='1'
							AND publish_yn_intra='0'
							AND dok_show_teaser_teaser='0' 
							AND allow_publish='1'
							AND reporeID LIKE '%d'
							AND reporeID=lan_repore_id
							AND lang_id='%d'
							ORDER BY reporeID DESC",
			$this->cms->papoo_repore,
			$this->cms->papoo_language_article,
			$this->db->escape($this->checked->reporeid),
			$this->db->escape($this->cms->lang_id)
		);
		// Daten in ein Array weisen
		$result = $this->db->get_results($select);
		// Array durchgehen
		if (!empty($result)) {
			foreach ($result as $roweins) {
				$inhalt = $roweins->header . "\n\n" . $roweins->lan_teaser . "\n\n" . $roweins->lan_article;
				$menuid_send = $roweins->cattextid;
			}
		}
		// Entscheidung nach $what wenn Link dann nur Link senden
		if ($this->checked->what == "link") {
			$inhalt = "";
		}

		IfNotSetNull($inhalt);
		$inhalt = $this->checked->comment_inhalt . "\n" . $inhalt;

		$inhalt = strip_tags($inhalt);

		IfNotSetNull($menuid_send);
		$send = $this->mail_it->send_mail(
			$menuid_send,
			$this->checked->reporeid,
			$this->checked->title_send,
			$this->checked->comment_mail,
			$this->checked->empfang_mail,
			$inhalt
		);
		// wenn senden geklappt hat
		if ($send == "ok") {
			$location_url =
				$_SERVER['PHP_SELF'] .
				"?menuid=" .$this->checked->menuid .
				"&reporeid=" . $this->checked->reporeid_print .
				"&gesendet=1";

			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}
		// wenn nicht dann Auswahl erneut anzeigen
		elseif ($send == "nok") {
			$location_url =
				"index.php?menuid=" . $this->checked->menuid .
				"&reporeid_send=1&reporeid_print=" . $this->checked->reporeid_print .
				"&error=1";

			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}
	}

	/**
	 * Diese Methode erstellt die Maske für das versenden eines Artikelsl für den Output ins Template
	 */
	public function send_artikel_mask()
	{
		IfNotSetNull($this->checked->empfang_mail);
		IfNotSetNull($this->checked->comment_mail);
		IfNotSetNull($this->checked->what);
		IfNotSetNull($this->checked->comment_inhalt);

		// Artikel soll gesendet werden -> Template
		$this->content->template['articlesend'] = 1;
		$this->content->template['noindex'] = 1;
		// Daten aus der Datenbank holen
		$select = sprintf("SELECT * FROM %s , %s WHERE publish_yn_lang='1' 
									AND allow_publish='1'
									AND publish_yn_intra='0'
									AND dok_show_teaser_teaser='0'
									AND allow_publish='1'
									AND reporeID = '%d'
									AND reporeID=lan_repore_id
									AND lang_id='%d'
									ORDER BY reporeID DESC",
			$this->cms->papoo_repore,
			$this->cms->papoo_language_article,
			$this->db->escape($this->checked->reporeid_print),
			$this->cms->lang_id
		);
		$result = $this->db->get_results($select);

		$this->content->template['empfang_mail'] = $this->checked->empfang_mail;
		$this->content->template['comment_mail'] = $this->checked->comment_mail;
		if ($this->checked->what == "link") {
			$this->content->template['checked_link'] = "nodecode:checked=\"checked\"";
		}
		if ($this->checked->what == "text") {
			$this->content->template['checked_text'] = "nodecode:checked=\"checked\"";
		}
		$this->content->template['comment_inhalt'] = $this->checked->comment_inhalt;
		// Daten zuweisen -> Template
		if (!empty($result)) {
			foreach ($result as $roweins) {
				$texteins = "<div>" . $roweins->lan_teaser . "</div>";
				$islinkmehr = $roweins->header;
				$this->content->template['reporeid_print'] = $this->checked->reporeid_print;
				$this->content->template['islink_send'] = $islinkmehr;
				$this->content->template['text_send'] = $texteins;
				// Error 1, Email nicht valide
			}
		}
		if(!isset($this->content->template['reporeid_print'])) {
			$this->content->template['reporeid_print'] = NULL;
		}
	}

	/**
	 * Diese Methode erstellt den Artikel für den Output ins Template
	 * @return array $table_data
	 */
	public function make_artikel()
	{
		// Daten aus der Datenbank holen für den jeweiligen Menupunkt
		$result = $this->get_artikel($this->checked->menuid);
		// Nur ein Eintrag vorhanden -> Template
		$this->content->template['eintrag'] = 1;
		// Artikel darstellen ermpglichen -> Template
		$this->content->template['article'] = 1;
		$table_data = array();
		$islink_ok = "ok";
		// Artikel einen hochzählen
		if (!empty($this->checked->reporeid)) {
			$sql = sprintf("UPDATE %s SET count=count+1 WHERE reporeID='%d' ",
				$this->cms->papoo_repore,
				$this->db->escape($this->checked->reporeid)
			);
			$this->db->query($sql);
			$islink_ok = "";
		}
		else {
			$select = sprintf("SELECT COUNT(reporeID) FROM %s WHERE cattextid='%d'",
				$this->cms->papoo_repore,
				$this->db->escape($this->checked->menuid)
			);
			$res = $this->db->get_var($select);
			if ($res == 1) {
				$sql = sprintf("UPDATE %s SET count=count+1 WHERE cattextid='%d' ",
					$this->cms->papoo_repore,
					$this->db->escape($this->checked->menuid)
				);
				$this->db->query($sql);
				$islink_ok = "";
			}
		}
		// Daten bearbeiten und zuweisen für das Template
		if (!empty($result)) {
			foreach ($result as $roweins) {
				// Usernamen raussuchen ###
				$queryuser = sprintf("SELECT * FROM %s WHERE userid='%d'",
					$this->cms->papoo_user,
					$roweins->dokuser
				);
				$resultusi = $this->db->get_results($queryuser);
				// Wenn nicht leer dann zuweisen
				if (!empty($resultusi)) {
					foreach ($resultusi as $rowuser) {
						// Variablen zuweisen für das Template
						$this->author = $rowuser->username;
						$this->content->template['usernameid'] = $this->user->username;
					}
				}
				$this->checked->real_reporeid = $this->repoex = $reporeid = $roweins->reporeID;
				// Es existiert ein Eintrag -> Template
				$this->eintrag = 1;
				// zählen und Variable erstellen für Template
				$count = $roweins->count;
				// Anzahl der Besucher insgesamt
				$this->counter->get_count();
				// Wenn kommentiert werden darf
				$this->content->template['allow_comment2'] = $roweins->allow_comment;
				// Text aus der Datenbank zuweisen
				$texteins = "<div class = \"inhalt\">" . $roweins->lan_article . "</div>";
				// Donwloads zählen, bzw. Infos zum Download anzeigen, setzen (oder nicht) je nach Angabe im Artikel
				$this->download->infos_anzeigen = $roweins->count_download;
				// Ersetzung der Download-Links

				// Wenn der Teaser im Artikel angezeigt werden soll
				if ($roweins->teaser_atyn == 1) {
					// Teaser markieren
					if (empty($roweins->teaser_bild_html)) {
						$teaser_text =
							"<div class=\"teaser\">" . $roweins->lan_teaser_img_fertig . "" . $roweins->lan_teaser . "</div>";
					}
					else {
						$teaser_text =
							"<<div class=\"teaser\">" . $roweins->teaser_bild_html . "" . $roweins->lan_teaser . "</div>";
					}
				}

				$texteins = $this->download->replace_downloadlinks($texteins);
				IfNotSetNull($this->download->refresh);
				$this->content->template['refresh'] = $this->download->refresh;
				$texteins = $this->diverse->do_pfadeanpassen($texteins);
				// Linktext für mehr über
				if (trim($roweins->lan_teaser_link) == "") {
					$islink = $roweins->header;
				}
				else {
					$islink = $roweins->lan_teaser_link;
				}

				$uberschrift = $roweins->uberschrift_hyn == 1 ? $roweins->header : "";

				$this->urlheader = $uberschrift;
				$this->content->template['thema'] = $roweins->header;
				$this->content->template['formthema'] = $this->diverse->encode_quote($roweins->header);
				// id für den Ausdruck übergeben
				$this->content->template['reporeid_print'] = $reporeid;
				// Editieren checken
				$this->check_edit($reporeid);
				// extra Meta-Titel zuweisen
				if (!empty($roweins->lan_metatitel)) {
					$this->content->template['site_title'] =
						"nobr:" . $this->diverse->encode_quote(strip_tags($roweins->lan_metatitel));
				}
				// sonst Artikel-überschrift
				else {
					$this->content->template['site_title'] =
						"nobr:" . $this->diverse->encode_quote(strip_tags($roweins->header));
				}

				// extra Meta-Beschreibung
				if (!empty($roweins->lan_metadescrip)) {
					$this->content->template['description'] =
						"nobr:" . $this->diverse->encode_quote(strip_tags($roweins->lan_metadescrip));
				}
				// sonst Artikel-Teaser
				else {
					$this->content->template['description'] =
						"nobr:" . $this->diverse->encode_quote(strip_tags($roweins->lan_teaser));
				}

				// extra Meta-Beschreibung
				if (!empty($roweins->lan_metakey)) {
					$this->content->template['keywords'] = $this->diverse->encode_quote($roweins->lan_metakey);
				}

				// Titel für Kommentar
				$this->content->template['comment_title'] = $uberschrift;

				if ($this->cms->comment_yn == 1) {
					// Zuweisung für das Template
					$commentok = 1;#
					$sql = sprintf("SELECT COUNT(*) FROM %s WHERE comment_article='%d' AND msg_frei='1'",
						$this->cms->papoo_message,
						$reporeid
					);
					$comment = $this->db->get_var($sql);
				}

				IfNotSetNull($comment);
				IfNotSetNull($commentok);
				IfNotSetNull($teaser_text);

				$table_data = array();
				array_push($table_data, array(
					'uberschrift' => $uberschrift,
					'teasertext' => $teaser_text,
					'islink' => $islink,
					'islink_ok' => $islink_ok,
					'text' => $texteins,
					//'zeitstempel' => $roweins->timestamp,
					'date_time' => $roweins->timestamp,
					'wie_oft' => $count,
					'erstellungsdatum' => $roweins->erstellungsdatum,
					'article_datum' => $roweins->stamptime,
					'author' => $this->author,
					'teaserbild' => $roweins->lan_teaser_img_fertig,
					'reporeid' => $reporeid,
					'comment' => $comment,
					'comment_ok' => $commentok
				));
			}
		}

		IfNotSetNull($uberschrift);
		IfNotSetNull($reporeid);

		if (!empty($this->checked->reporeid)) {
			$this->content->template['actu_headline'] = $uberschrift;
		}

		if(!isset($this->content->template['reporeid_print'])) {
			$this->content->template['reporeid_print'] = NULL;
		}
		$this->content->template['reporeid_print'] = $reporeid;

		return $table_data;
	}

	/**
	 * Kommentare anzeigen und in die Datenbank schreiben
	 */
	function do_comment()
	{
		if (empty($this->checked->uebermittelformular)) {
			$this->checked->uebermittelformular = "";
		}

		// Kommentare sind erlaubt
		$this->content->template['erlaubt'] = 1;
		// Username checken und wenn ok dann übergeben
		if (!empty($this->user->userid) and $this->user->userid != 11) {
			// $this->checked->neuvorname = $this->user->username;
			$this->checked->neuvorname = $_SESSION['sessionusername'];
			$this->content->template['author'] = $this->checked->neuvorname;
		}
		// Wenn das Formular für das eintragen eines Beitrages betätigt wurde
		if ($this->checked->uebermittelformular) {
			$fehlerliste = array();
			//mail valid?
			if(!filter_var($this->checked->email, FILTER_VALIDATE_EMAIL)){
				$this->content->template['email'] = "invalid";
			}
			else {
				$this->content->template['email'] = "valid";
			}

			// Wenn sowohl ein Thema eingegeben ist, als auch ein Inhalt
			if (empty($this->checked->neuvorname) ||
				$this->content->template['email'] == "invalid" ||
				empty($this->checked->email) ||
				empty($this->checked->formthema) ||
				empty($this->checked->inhalt) ||
				$this->spamschutz->is_spam) {
				// Daten fehlen oder Spamcode falsch
				// Fehler setzen
				if(empty($this->checked->neuvorname)) {
					array_push($fehlerliste,'Autor');
				}
				if(empty($this->checked->formthema)) {
					array_push($fehlerliste,'Thema');
				}
				if(empty($this->checked->inhalt)) {
					array_push($fehlerliste,'Beitrag');
				}
				if($this->spamschutz->is_spam) {
					array_push($fehlerliste,'Spamfilter');
				}
				if(empty($this->checked->email) || $this->content->template['email'] == "invalid"){
					array_push($fehlerliste,'E-Mail ungültig');
				}

				$this->content->template['fehlerliste'] = $fehlerliste;

				$this->content->template['fehler'] = "Bitte korrigieren Sie folgende Felder";
				// Daten an Template durchreichen.
				$this->content->template['author'] = $this->checked->neuvorname;
				$this->checked->formthema = str_ireplace("Kommentar zu", "", $this->checked->formthema);

				$this->content->template['thema'] = $this->checked->formthema;
				$this->content->template['formthema'] = $this->diverse->encode_quote($this->checked->formthema);
				$this->content->template['inhalt'] = "nobr:" . $this->checked->inhalt;
				$this->content->template['email'] = $this->checked->email;
			}
			else {
				// Datum erstellen
				$blacklist = new blacklist();
				$truethema = $blacklist->do_blacklist($this->checked->formthema);
				$trueinhalt = $blacklist->do_blacklist($this->checked->inhalt);
				// Aus Sicherheitsgründen < und > Zeichen ersetzen
				$this->checked->neuvorname = str_replace("<", "&lt;", $this->checked->neuvorname);
				$this->checked->neuvorname = str_replace(">", "&gt;", $this->checked->neuvorname);
				$this->checked->formthema = str_replace("<", "&lt;", $this->checked->formthema);
				$this->checked->formthema = str_replace(">", "&gt;", $this->checked->formthema);
				$this->checked->inhalt = str_replace("<", "&lt;", $this->checked->inhalt);
				$this->checked->inhalt = str_replace(">", "&gt;", $this->checked->inhalt);
				// Wenn userid leer, dann Gast oder jeder eingeben
				if ($this->user->userid == 0 or $this->user->userid == 11) {
					$userid_drin = 11;
					$usernameguest = $this->checked->neuvorname;
				}
				else {
					$userid_drin = $this->user->userid;
				}

				IfNotSetNull($usernameguest);
				// Eintrag für die Datenbank erstellen
				$query = sprintf(
					"INSERT INTO %s SET
					comment_article='%d',
					zeitstempel=NOW(),
					forumid='10',
					parentid='0',
					rootid='0',
					userid='%d',
					thema='%s',
					messagetext='%s',
					level='0',
					ordnung='0',
					username_guest='%s',
					msg_frei='%d',
					email='%s',
					artikelurl='%s' ",
					$this->cms->papoo_message,
					$this->db->escape($this->checked->reporeid),
					//$this->db->escape( $heute ),
					$this->db->escape($userid_drin),
					$this->db->escape($this->checked->formthema),
					$this->db->escape($this->checked->inhalt),
					$this->db->escape($usernameguest),
					$this->cms->gaestebuch_msg_frei,
					$this->checked->email,
					$this->checked->artikelurl
				);

				// nur wenn keine BlacklistEinträge drin sind, auch eintragen,
				// ansonsten nichts eintragen, läuft ins Leere
				if ($truethema == "ok" and $trueinhalt == "ok") {
					// in die Datenbank eintragen
					$this->db->query($query);
					if ($this->cms->benach_neu_kommentar == 1) {
						$this->content->template['message_2278'] =
							str_ireplace('#kommentar#', $this->checked->formthema, $this->content->template['message_2278']);
						$this->diverse->mach_nachricht_neu($this->content->template['message_2278'], $this->checked->inhalt);
					}
				}
				$_SESSION['kommentar_success'] = true;
				// neu laden und inhalt leeren
				$location_url =
					$_SERVER['PHP_SELF'] .
					"?menuid=" . $this->checked->menuid .
					"&reporeid=" . $this->checked->reporeid .
					"#formwrap_guest_anchor";

				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="' . $location_url . '">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit();
			}
		}

		if (isset($_SESSION['kommentar_success']) && $_SESSION['kommentar_success']) {
			$this->content->template['success'] = true;
			$_SESSION['kommentar_success'] = false;
		}

		if (empty($this->checked->reporeid)) {
			global $db_praefix;
			$sql = sprintf("SELECT lart_id FROM %s WHERE lcat_id='%d' LIMIT 1",
				$db_praefix . "papoo_lookup_art_cat",
				$this->checked->menuid
			);
			$res = $this->db->get_var($sql);

			// Kommentare auswählen
			if (!empty($res)) {
				$sqlcomment = sprintf("SELECT * FROM %s WHERE comment_article='%d' AND msg_frei='1' ORDER BY msgid ASC",
					$this->cms->papoo_message,
					$this->db->escape($res)
				);
			}
			else {
				$sqlcomment = "";
			}
		}
		else {
			// Kommentare auswählen
			if (!is_numeric($this->checked->reporeid)) {
				$this->checked->reporeid = 88;
			}
			if (empty($sqlcomment)) {
				$sqlcomment = sprintf("SELECT * FROM %s WHERE comment_article='%d' AND msg_frei='1' ORDER BY msgid ASC",
					$this->cms->papoo_message,
					$this->db->escape($this->checked->reporeid)
				);
			}
		}

		$this->content->template['username_neu_comment'] = $this->user->username;
		// Kommentare auslesen
		if (!empty($sqlcomment)) {
			$resultcomment = $this->db->get_results($sqlcomment);
		}
		else {
			$resultcomment = "";
		}
		// Wenn welche vorhanden sind, anzeigen ok setzen
		if (!empty($resultcomment)) {
			$this->content->template['comment_ok'] = "ok";
		}
		// Array erstellen
		$comment_data = array();
		// Wenn Resultate vorliegen
		if (!empty($resultcomment)) {
			// für jedes Ergebniss durchloopen
			foreach ($resultcomment as $rowcom) {
				// decodieren
				$messagetextcom = ($rowcom->messagetext);
				// Usernamen raussuchen
				$sqlgetuser = sprintf("SELECT username FROM %s WHERE userid='%d' LIMIT 1",
					$this->cms->papoo_user,
					$rowcom->userid
				);
				// Username zuweisen
				$resultusercom = $this->db->get_results($sqlgetuser);
				// Wenn es nicht leer ist Username zuweisen fürs Template
				if (!empty($resultusercom) and $rowcom->userid != "11") {
					foreach ($resultusercom as $rowusercom) {
						if (!empty($rowusercom->username)) {
							$username_comment = $rowusercom->username;
						}
						else {
							$username_comment = "Gast";
						}
					}
				}
				else {
					$username_comment = "_" . $rowcom->username_guest;
				}
				IfNotSetNull($username_comment);
				// Daten in ein Array weisen
				array_push($comment_data, array(
					'msgid' => $rowcom->msgid,
					'text' => $messagetextcom,
					//'zeitstempel' => $rowcom->zeitstempel,
					'date_time' => $rowcom->zeitstempel,
					'thema' => $rowcom->thema,
					'username_comment' => $username_comment
				));
			}
			// Array in Eigenschaft weisen
			$this->content->template['comment_data'] = $comment_data;
		}
		// Ende Kommentar #####
		if(!isset($this->content->template['comment_ok'])) {
			$this->content->template['comment_ok'] = NULL;
		}
	}
	/**
	 * Kommentare anzeigen und in die Datenbank schreiben
	 */
	function do_guestbook()
	{
		// Kommentare sind erlaubt
		$this->content->template['erlaubt'] = 1;
		// menuid zuweisen
		$this->content->template['formmenuid'] = $this->checked->menuid;
		// Wenn das Formular für das eintragen eines Beitrages betätigt wurde
		if (empty($this->checked->uebermittelformular)) {
			$this->checked->uebermittelformular = "";
		}

		if ($this->checked->uebermittelformular) {
			// !!! Einbau Spam-Kontrolle Teil 2
			// Test ob Daten fehlen; Test auf Spam;
			if (empty($this->checked->author) ||
				empty($this->checked->thema) ||
				empty($this->checked->inhalt) ||
				$this->spamschutz->is_spam) {
				// Daten fehlen oder Spamcode falsch
				// Fehler setzen
				$this->content->template['fehler'] = "Sie haben einen Fehler in Ihren Angaben";
				// Daten an Template durchreichen.
				$this->content->template['author'] = $this->checked->author;
				$this->content->template['thema'] = $this->checked->thema;
				$this->content->template['inhalt'] = "nobr:" . $this->checked->inhalt;
			}
			// Daten sind OK, in Datenbank eintragen
			else {
				$blacklist = new blacklist();
				$truethema = $blacklist->do_blacklist($this->checked->thema);
				$trueinhalt = $blacklist->do_blacklist($this->checked->inhalt);
				// Aus Sicherheitsgrnden < und > Zeichen ersetzen
				$this->checked->thema = str_replace("<", "&lt;", $this->checked->thema);
				$this->checked->thema = str_replace(">", "&gt;", $this->checked->thema);
				$this->checked->inhalt = str_replace("<", "&lt;", $this->checked->inhalt);
				$this->checked->inhalt = str_replace(">", "&gt;", $this->checked->inhalt);
				// Wenn userid leer, dann Gast oder jeder eingeben
				if ($this->user->userid == 0 or $this->user->userid == 11) {
					$userid_drin = 11;
				}
				else {
					$userid_drin = $this->user->userid;
				}
				// Eintrag für die Datenbank erstellen
				$query = sprintf(
					"INSERT INTO %s SET
					comment_article='%d',
					zeitstempel=NOW(),
					forumid='20',
					parentid='0',
					rootid='0',
					userid='%d',
					thema='%s',
					messagetext='%s',
					level='0',
					ordnung='0',
					username_guest='%s',
					msg_frei='%d' ",
					$this->cms->papoo_message,
					$this->db->escape($this->checked->reporeid),
					//$this->db->escape( $heute ),
					$this->db->escape($userid_drin),
					$this->db->escape($this->checked->thema),
					$this->db->escape($this->checked->inhalt),
					$this->db->escape($this->checked->author),
					$this->cms->gaestebuch_msg_frei
				);

				// nur wenn keine BlacklistEinträge drin sind, auch eintragen,
				// ansonsten nichts eintragen, läuft ins Leere
				if ($truethema == "ok" and $trueinhalt == "ok") {
					// in die Datenbank eintragen
					$this->db->query($query);
					if ($this->cms->benach_neu_gaestebuch == 1) {
						$this->content->template['message_2277'] =
							str_ireplace('#thema', $this->checked->thema, $this->content->template['message_2277']);

						$this->diverse->mach_nachricht_neu($this->content->template['message_2277'], $this->checked->inhalt);
					}
				}
				// neu laden und inhalt leeren
				// FIXME: Im Gästebuch gibt es keine ReporeID und auch kein inhaltrein, also was soll das ???
				$location_url = $this->cms->webverzeichnis . "/guestbook.php?menuid=" . $this->checked->menuid;
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="' . $location_url . '">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit();
			}
		}
		else {
			// Gästebucheintrge auswählen
			$sqlcomment = sprintf("SELECT COUNT(msgid) FROM %s WHERE forumid='20' AND msg_frei='1' ORDER BY msgid DESC ",
				$this->cms->papoo_message
			);
			// Kommentare auslesen
			$this->weiter->modlink = "no";
			$this->weiter->make_limit($this->cms->system_config_data['config_paginierung']);
			$resultanzahl = $this->db->get_var($sqlcomment);
			// zählen wieviele Artikel existieren
			$this->weiter->result_anzahl = $resultanzahl;
			// GästebuchEinträge auswählen
			$sqlcomment = sprintf("SELECT * FROM %s WHERE forumid='20' AND msg_frei='1' ORDER BY msgid DESC %s",
				$this->cms->papoo_message,
				$this->weiter->sqllimit
			);
			// Kommentare auslesen
			$resultcomment = $this->db->get_results($sqlcomment);
			// Wenn welche vorhanden sind, anzeigen ok setzen
			if (!empty($resultcomment)) {
				$this->content->template['comment_ok'] = "ok";
			}
			// Array erstellen
			$comment_data = array();
			// Wenn Resultate vorliegen
			if (!empty($resultcomment)) {
				// für jedes Ergebniss durchloopen
				foreach ($resultcomment as $rowcom) {
					// decodieren
					$messagetextcom = $rowcom->messagetext;
					// Usernamen raussuchen
					if ($rowcom->userid != 11) {
						$sqlgetuser = sprintf("SELECT username FROM %s WHERE userid='%d' LIMIT 1",
							$this->cms->papoo_user,
							$rowcom->userid
						);
						// Username zuweisen
						$resultusercom = $this->db->get_results($sqlgetuser);
						// Wenn es nicht leer ist Username zuweisen fürs Template
						if (!empty($resultusercom)) {
							foreach ($resultusercom as $rowusercom) {
								if (!empty($rowusercom->username)) {
									$username_comment = $rowusercom->username;
								}
								else {
									$username_comment = "Gast";
								}
							}
						}
						else {
							$username_comment = "Gast";
						}
					}
					else {
						$username_comment = "_" . $rowcom->username_guest;
					}
					IfNotSetNull($username_comment);
					// Daten in ein Array weisen
					array_push($comment_data, array(
						'msgid' => $rowcom->msgid,
						'text' => $messagetextcom,
						//'zeitstempel' => $rowcom->zeitstempel,
						'date_time' => $rowcom->zeitstempel,
						'thema' => $rowcom->thema,
						'username_comment' => $username_comment
					));
				}
				// Array in Eigenschaft weisen
				$this->content->template['comment_data'] = $comment_data;

				// Adresse des Links übergeben
				if ($this->cms->mod_rewrite == 2 && $this->cms->mod_surls != "1") {
					$this->weiter->weiter_link = "guestbook/menuid/" . $this->checked->menuid . "";
				}
				else {
					$this->weiter->weiter_link = "guestbook.php?menuid=" . $this->checked->menuid . "";
				}
				$what = "teaser";
				// Links zu weiteren Seiten anzeigen
				$this->weiter->do_weiter($what);
			}
		}
	}

	/**
	 * Diese Methode gibt den Artikel oder die Startseite aus.
	 * @return array|string
	 */
	function check_artikel()
	{
		// Standard für Suchvariable
		$result = "";
		$sendneu = "no";

		if (empty($this->checked->send_artikel)) {
			$this->checked->send_artikel = "";
		}
		// Artikel wurde erfolgreich versendet
		if (!empty($this->checked->gesendet)) {
			$this->content->template['gesendet'] = "1";
		}
		// Artikel wird jetz gesendet
		if (!empty($this->checked->send_artikel) and $this->spamschutz->is_spam == false) {
			$this->send_artikel();
		}
		// Artikel wird nicht gesendet da Spam???
		if ($this->checked->send_artikel == "Senden" and $this->spamschutz->is_spam == true) {
			$sendneu = "yes";
			$this->content->template['error_comment'] = "Bitte überprüfen Sie Ihre Eingaben.";
		}

		if (empty($this->checked->reporeid_send)) {
			$this->checked->reporeid_send = "";
		}
		// Wenn Artikel senden ausgewählt ist , Eingabemaske anzeigen
		if ($this->checked->reporeid_send != "" or $sendneu == "yes") {
			header('X-Robots-Tag: noindex, nofollow');
			$this->send_artikel_mask();
		}
		else {
			// Wir sind nicht auf der Startseite
			if ($this->checked->menuid != '1' or ($this->checked->menuid == 1 and $this->checked->reporeid != "")) {
				$this->content->template['art_url'] = "";
				// Es ist kein einzelner Artikel ausgewählt
				if ($this->checked->reporeid == "") {
					// zählen wieviele Artikel existieren
					$this->weiter->result_anzahl = $this->get_anzahl();
					// Aber es existiert nur einer in der Datenbank fuer diesen Menueintrag
					if ($this->weiter->result_anzahl == 1) {
						// Daten aus der Datenbank holen für den jeweiligen Menupunkt
						$result = $this->make_artikel();
						// Url für das Menü etc.
						$this->content->template['art_url'] = $this->menu->urlencode($this->urlheader) . ".html";
						// Daten zurückgeben
						return $result;
					}
					// Es existieren mehr Eintrge fuer diesen Menupunkt; Einträge mit Links anzeigen
					else {
						// Alle Artikel diese Menüpunktes anzeigen mit teaser
						if ($this->cms->stamm_cat_sub_ok == 1) {
							$this->content->template['cat_teaser_data'] = $this->make_cat_teaser();
						}
						else {
							$teaser = $this->make_teaser();
						}
						// Seiten-Title auf Name des aktuellen Menü-Punktes setzen
						// $this->content->template['site_title'] = $this->menu->aktu_menuname;
						// Adresse des Links übergeben
						$this->weiter->weiter_link = "index.php?menuid=" . $this->checked->menuid . "";
						if ($this->cms->mod_rewrite == 2) {
							$this->weiter->weiter_link = "index/menuid/" . $this->checked->menuid;
						}
						$what = "teaser";
						// Links zu weiteren Seiten anzeigen
						$this->weiter->do_weiter($what);
						// Daten zurückgeben
						IfNotSetNull($teaser);
						return $teaser;
					}
				}
				// Es ist ein einzelner Eintrag ausgewählt
				else {
					// Daten aus der Datenbank holen für den jeweiligen Menupunkt
					$result = $this->make_artikel();
					// Edit check
					$this->check_edit($this->checked->reporeid);
					// Daten zurückgeben
					return $result;
				}
			}
		}
		return $result;
	}

	/**
	 * Wir sind auf der language_stammseite, alle Einträge mit Links anzeigen
	 * @return array
	 */
	function make_start()
	{
		// einbinden der user Daten
		$table_data = array();
		// 1. Startseiten-Text (wenn vorhanden) einfügen
		$top_text = trim($this->cms->top_text);
		if (!empty($top_text)) {
			$top_text = $this->diverse->do_pfadeanpassen($top_text);
			$top_text = $this->download->replace_downloadlinks($top_text);
			$table_data[ ] = array(
				'text' => $top_text
			);
		}
		// 2. Artikel der Startseite anzeigen
		if ($this->cms->stamm_cat_startseite_ok != 1) {
			$table_data = array_merge($table_data, $this->make_teaser());
			$this->weiter->result_anzahl = $this->get_anzahl();
		}

		// 3. Weiter-Link anpassen ???
		if ($this->cms->mod_rewrite == 2) {
			$this->weiter->weiter_link = "index/menuid/" . $this->checked->menuid;
		}
		else {
			$this->weiter->weiter_link = "index.php?menuid=" . $this->checked->menuid . "";
		}
		$what = "teaser";

		$this->weiter->do_weiter($what);

		$this->check_artikel();

		// Überprüfen, wie die Artikel unter dem Menüpunkt Startseite sortiert werden sollen
		$sql = sprintf("SELECT artikel_sort FROM %s WHERE menuid=1",
			$this->cms->papoo_menu
		);
		$artikel_sort_startseite = $this->db->get_results($sql, ARRAY_A)[0]['artikel_sort'];

		// Artikel sollen nach 'order_id_start' sortiert werden
		if ($artikel_sort_startseite==3) {
			usort($table_data, function($a, $b){
				if ($a['order_id_start'] == $b['order_id_start']) {
					return 0;
				}
				return ($a['order_id_start'] < $b['order_id_start'] ? -1 : 1);
			});
		}
		return $table_data;
	}

	/**
	 * Anzahl aller Einträge von Artikeln in der Datenbank raussuchen
	 *
	 * @return int|string
	 */
	function get_anzahl()
	{
		if (empty($this->checked->reporeid)) {
			// Keine Abfrage => like %
			$like_repo = "LIKE";
			// wenn Startseite
			if ($this->checked->menuid == 1) {
				// kein Menuid Bezug in der sql Abfrage => like %
				$selectmenuid = "%";
				$like = "LIKE";
				// Aber Abfrage ob geteasert werden soll auf der Startseite
				$like_teas = "=";
				$teas = "1";
			}
			// Nicht Startseite
			else {
				// Menuid Bezug in der sql Abfrage => =$menuid
				$selectmenuid = $this->db->escape($this->checked->menuid);
				$like = "=";
				// Keine Abfrage ob geteasert werden soll
				$like_teas = "LIKE";
				$teas = "%";
			}
		}
		else {
			$like_repo = "=";
			$like_teas = "LIKE";
			$teas = "%";
			$selectmenuid = "%";
			$like = "LIKE";
		}
		if (empty($this->checked->reporeid)) {
			$this->checked->reporeid = "%";
		}
		// Gesamte Anzahl der Artikel aus der Datenbank raussuchen
		// TODO: diesen Scheiß von zusammengewürfelten Strings zusammenfassen
		$select = "SELECT DISTINCT reporeID, pub_verfall, pub_start, 
				pub_verfall_page, pub_start_page, pub_wohin, pub_dauerhaft, teaser_list FROM " .
			$this->cms->papoo_repore .
			"," .
			$this->cms->papoo_lookup_article .
			", " .
			$this->cms->tbname['papoo_lookup_art_cat'] .
			", ";

		$select .=
			$this->cms->papoo_lookup_ug . ", " .
			$this->cms->papoo_language_article . " AS t4, " .
			$this->cms->tbname['papoo_me_nu'] . " WHERE";

		if(!isset($this->sub_menids)) {
			$this->sub_menids = NULL;
		}

		$this->check_sub();
		if (!is_array($this->sub_menids)) {
			$select .= " publish_yn_lang='1' AND publish_yn_intra='0' AND dok_show_teaser_teaser='0' AND allow_publish='1' AND";
			$select .= " teaser_list $like_teas '$teas' AND lcat_id $like '$selectmenuid' AND reporeID $like_repo '" .
				$this->db->escape($this->checked->reporeid) . "' AND lcat_id=menuid ";
			$select .= " AND reporeID=article_id AND lart_id = reporeID";
			$select .= " AND gruppeid_id=gruppenid";
			$select .= " AND userid='" . $this->user->userid . "'";
			$select .= " AND reporeID=t4.lan_repore_id AND t4.lang_id='" . $this->cms->lang_id . "'";
			$select .= " ORDER BY reporeID DESC ";
		}
		else {
			$this->sub_menids[] = $this->checked->menuid;
			$anzmenids = count($this->sub_menids);
			$ij = 0;
			foreach ($this->sub_menids as $menids) {
				$ij++;
				$select .= " publish_yn_lang='1' AND publish_yn_intra='0' AND dok_show_teaser_teaser='0' AND allow_publish='1' AND";
				$select .= " teaser_list $like_teas '$teas' AND lcat_id $like '$menids' AND reporeID $like_repo '" .
					$this->db->escape($this->checked->reporeid) . "' AND lcat_id=menuid ";
				$select .= " AND reporeID=article_id AND lart_id = reporeID";
				$select .= " AND gruppeid_id=gruppenid";
				$select .= " AND userid='" . $this->user->userid . "'";
				$select .= " AND reporeID=t4.lan_repore_id AND t4.lang_id='" . $this->cms->lang_id . "'";

				if ($ij < $anzmenids) {
					$select .= " OR ";
				}
			}
			$select .= " ORDER BY reporeID DESC ";
		}

		// reporeid wieder zurücksetzen
		if (stristr($this->checked->reporeid, "%")) {
			$this->checked->reporeid = "";
		}
		// Anzahl der Ergebnisse
		$this->artikel_anzahl = $this->db->get_results($select);

		IfNotSetNull($menuid);

		$neu = 0;
		$i = 0;
		if (!empty($this->artikel_anzahl)) {
			foreach ($this->artikel_anzahl as $artikel) {
				if (($this->checked->menuid == 1 &&
						empty($this->checked->reporeid)) or ($menuid == "x" && empty($this->checked->reporeid))) {
					// Timestammp heute
					$heute = time();
					$start = 0;
					$verfall = 0;
					// Verfallstimestamp;
					$dat = explode(".", $artikel->pub_verfall_page);

					if(!isset($dat['2'])) {
						$dat['2'] = NULL;
					}

					$dat_t1 = explode(" ", $dat['2']);

					if(!isset($dat_t1['1'])) {
						$dat_t1['1'] = NULL;
					}

					$dat_t2 = explode(":", $dat_t1['1']);
					if (empty($dat_t2['0'])) {
						$dat_t2['0'] = "0";
					}
					if (empty($dat_t2['1'])) {
						$dat_t2['1'] = "0";
					}
					if ($dat[2] > 1970) {
						$verfall = @mktime($dat_t2['0'], $dat_t2['1'], 0, $dat[1], $dat[0], $dat[2]);
					}
					// Starttimestamp
					$dat = explode(".", $artikel->pub_start_page);

					if(!isset($dat['2'])) {
						$dat['2'] = NULL;
					}

					$dat_t1 = explode(" ", $dat['2']);

					if(!isset($dat_t1['1'])) {
						$dat_t1['1'] = NULL;
					}

					$dat_t2 = explode(":", $dat_t1['1']);
					if (empty($dat_t2['0'])) {
						$dat_t2['0'] = "0";
					}
					if (empty($dat_t2['1'])) {
						$dat_t2['1'] = "0";
					}
					if ($dat[2] > 1970) {
						$start = @mktime($dat_t2['0'], $dat_t2['1'], 0, $dat[1], $dat[0], $dat[2]);
					}
					// Wenn Artikel auf der Startseite gelistet werden soll und das Datum passt bzw. keins gesetzt ist
					if ($artikel->teaser_list == "1" && (
							$start == 0 && $verfall == 0 ||
							$heute >= $start && $heute <= $verfall
						)) {
						$neu++;
					}
					else {
						// Verfall erreicht
						if ($artikel->pub_verfall_page != "0") {
							$i++;
							continue;
						}
					}
				}
				else {
					// Timestammp heute
					$heute = time();
					// Verfallstimestamp
					$dat = explode(".", $artikel->pub_verfall);

					IfNotSetNull($dat['2']);

					$dat_t1 = explode(" ", $dat['2']);

					IfNotSetNull($dat_t1['1']);

					$dat_t2 = explode(":", $dat_t1['1']);

					if (empty($dat_t2['0'])) {
						$dat_t2['0'] = "0";
					}
					if (empty($dat_t2['1'])) {
						$dat_t2['1'] = "0";
					}
					if ($dat[2] > 1970) {
						$verfall = @mktime($dat_t2['0'], $dat_t2['1'], 0, $dat[1], $dat[0], $dat[2]);
					}
					// Starttimestamp
					$dat = explode(".", $artikel->pub_start);

					IfNotSetNull($dat['2']);

					$dat_t1 = explode(" ", $dat['2']);

					IfNotSetNull($dat_t1['1']);

					$dat_t2 = explode(":", $dat_t1['1']);
					if (empty($dat_t2['0'])) {
						$dat_t2['0'] = "0";
					}
					if (empty($dat_t2['1'])) {
						$dat_t2['1'] = "0";
					}
					if ($dat[2] > 1970) {
						$start = @mktime($dat_t2['0'], $dat_t2['1'], 0, $dat[1], $dat[0], $dat[2]);
					}
					IfNotSetNull($start);
					IfNotSetNull($verfall);
					// Wenn nicht dauerhaft
					if ($artikel->pub_dauerhaft != 1) {
						// Wenn start nicht ok oder verfall schon erreicht
						if ($start < $heute || $verfall < $heute) {
							// Verfall schon erreicht
							if ($verfall < $heute) {
							}
							else {
								if ($start < $heute) {
									$neu++;
								}
							}
							// Dann überspringen
							continue;
						}
					}
					else {
						$neu++;
					}
				}
			}
		}
		if (empty($neu)) {
			$neu = "";
		}
		return $this->artikel_anzahl = $neu;
	}

	/**
	 * artikel_class::check_sub()
	 * checked ob zu diesem Menüpunkt sub artikel angezeigt werden sollen
	 *
	 * @return void
	 */
	function check_sub()
	{
		if (!defined("admin")) {
			//if (is_array($this->menu->menu_front))
			if (is_array($this->menu->data_front_complete)) {
				//foreach ($this->menu->menu_front as $men)
				foreach ($this->menu->data_front_complete as $men) {
					if ($men['menuid'] == $this->checked->menuid) {
						if ($men['menu_subteaser'] == 1) {
							$this->subteaser = 1;
							$submen = $this->checked->menuid;
							$this->level_plus = $men['level'] + 4;
						}
					}
				}
			}
			if(!isset($submen)) {
				$submen = NULL;
			}
			//Alle Untermenupunkte rekursiv rausholen
			$this->get_subid($submen);
		}
	}

	/**
	 * artikel_class::get_subid()
	 * Alle Unermenupunkte rausholen die dieser Menüpunkt hat
	 *
	 * @param int $submen
	 * @return void
	 */
	function get_subid($submen = 0)
	{
		if (!empty($this->menu->data_front_complete)) {
			foreach ($this->menu->data_front_complete as $men) {
				if ($men['untermenuzu'] == $submen && $men['level'] <= $this->level_plus) {
					$this->sub_menids[ ] = $men['menuid'];
					$this->get_subid($men['menuid']);
				}
			}
		}
	}

	/**
	 * Artikel aus der Datenbank fischen und übergeben
	 * modus [<nil>, "ALL"]: zeigt nur cms->sqllimit oder ALLE Artikel dieses Menü-Punktes.
	 * start [0, 1]: zeigt bei 0 die Artikel welches das Haekchen "Auf Startseite liste haben", bei 1,
	 * die Artikel welche der Startseite zugewiesen sind
	 *
	 * @param int $menuid
	 * @param string $modus
	 * @param int $start
	 * @return string|array Artikel
	 */
	function get_artikel($menuid = 1, $modus = "", $start = 0)
	{
		global $template;

		if (!defined("admin")) {
			if ($template == "guestbook.html") {
				// Kommentare anzeigen
				$this->do_guestbook();
			}
			else {
				// Kommentare anzeigen
				$this->do_comment();
			}
		}

		$docatlist = "";

		//Die Artikelsortierung rausholen
		if (is_array($this->menu->data_front_complete)) {
			foreach ($this->menu->data_front_complete as $key => $value) {
				if ($menuid == $value['menuid']) {
					$artikel_sort = $value['artikel_sort'];
				}
			}
		}

		if (empty($this->checked->reporeid)) {
			// Keine Abfrage => like %
			$like_repo = "LIKE";

			// wenn Startseite
			if ($menuid == 1) {
				// kein Menuid Bezug in der sql Abfrage => like %
				$selectmenuid = "%";
				$like = "LIKE";
				// Aber Abfrage ob geteasert werden soll auf der Startseite
				$like_teas = "=";
				$teas = "1";
				// Order-ID Start verwenden
				$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, order_id_start ASC ";
			}
			// Nicht Startseite
			else {
				// Menuid Bezug in der sql Abfrage => =$menuid
				// $selectmenuid = $this->checked->menuid;
				$selectmenuid = $this->db->escape($menuid);
				$like = "=";
				// Keine Abfrage ob geteasert werden soll
				$like_teas = "LIKE";
				$teas = "%";

				if ($this->cms->stamm_artikel_order == 1) {
					IfNotSetNull($artikel_sort);
					//alte Methode $orderfield = " ORDER BY dok_teaserfix DESC, reporeID DESC ";
					$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, ".
						((int)$artikel_sort == 6 ? "erstellungsdatum DESC, " : "").
						"lart_order_id ";
				}
			}

			if ($menuid == "x") {
				// kein Menuid Bezug in der sql Abfrage => like %
				$selectmenuid = "%";
				$like = "LIKE";
				// Aber Abfrage ob geteasert werden soll auf der Startseite
				$like_teas = "LIKE";
				$teas = "%";
				if (empty($this->checked->nocat))
					$docatlist = " cat_category_id > '0' AND ";
				// Order-ID Start verwenden

				if ($this->cms->stamm_artikel_order == 1) {
					$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, reporeID DESC ";
				}
				else {
					$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, lart_order_id DESC ";
				}
			}

			if ($menuid == "xy") {
				// kein Menuid Bezug in der sql Abfrage => like %
				$selectmenuid = $this->db->escape($this->checked->menuid);
				$like = "LIKE";
				// Aber Abfrage ob geteasert werden soll auf der Startseite
				$like_teas = "LIKE";
				$teas = "%";
				$docatlist = " cat_category_id > '0' AND ";
				// Order-ID Start verwenden
				if ($this->cms->stamm_artikel_order == 1) {
					$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, reporeID DESC ";
				}
				else {
					$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, lart_order_id ASC ";
				}
			}
		}
		else {
			$like_repo = "=";
			$like_teas = "LIKE";
			$teas = "%";
			$selectmenuid = "%";
			$like = "LIKE";
			$orderfield = "";
		}

		IfNotSetNull($artikel_sort);
		switch ($artikel_sort) {
			case "1":
				// Standard - Sortierung nach Konfiguration - nach manipulierbarem Erstellungsdatum

				// Das Häkchen "Neue Artikel oben einordnen" ist gesetzt
				if ($this->cms->stamm_artikel_order == 1) {
					$orderfield = "GROUP BY reporeID ORDER BY dok_teaserfix DESC, erstellungsdatum DESC, lart_order_id ASC";
				}
				// Das Häkchen "Neue Artikel oben einordnen" ist NICHT gesetzt
				else {
					$orderfield = "GROUP BY reporeID ORDER BY dok_teaserfix DESC, erstellungsdatum ASC, lart_order_id ASC";
				}
				break;

			case "2":
				// Neueste Artikel zuerst - ausgehend vom tatsäschlichen Erstellungsdatum
				$orderfield = "GROUP BY reporeID ORDER BY dok_teaserfix DESC, timestamp DESC, lart_order_id ASC";
				break;

			case "3":
				// Nach Orderid der Artikel
				$orderfield = "GROUP BY reporeID ORDER BY dok_teaserfix DESC, lart_order_id ASC";
				break;

			case "4":
				// Zufällig
				$orderfield = "GROUP BY reporeID ORDER BY dok_teaserfix DESC, RAND()";
				break;

			case "5":
				// Alphabetisch
				$orderfield = "GROUP BY reporeID ORDER BY dok_teaserfix DESC, header ASC";
				break;

			case "6":
				// Erstellungsdatum
				$orderfield = "GROUP BY reporeID ORDER BY dok_teaserfix DESC, erstellungsdatum DESC";
				break;

			default:
				break;
		}

		// Checken auf easy Anzeige
		if (!empty($this->cms->tbname['papoo_easyedit_daten']) &&
			$menuid != "x" &&
			$menuid != "1" &&
			$modus != "ALL" &&
			$this->cms->stamm_artikel_order == 1) {
			$sql = sprintf("SELECT easy_order_id FROM %s WHERE easy_men_id='%d'",
				$this->cms->tbname['papoo_easyedit_daten'],
				$this->db->escape($this->checked->menuid)
			);
			$orderid = $this->db->get_var($sql);
			if ($orderid == 1) {
				$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, reporeID DESC ";
			}
		}

		if (empty($this->checked->reporeid)) {
			$this->checked->reporeid = "%";
		}

		if (!is_numeric($this->checked->reporeid)) {
			switch ($modus) {
				case "ALL":
					$sqllimit = "";
					break;

				case "":
				default:
					$sqllimit = $this->cms->sqllimit;
			}

			if ($menuid == "x") {
				$sqllimit = "LIMIT 0,200";
			}
		}

		if (!empty($this->orderfield)) {
			$orderfield = $this->orderfield;
		}

		// Artikel aus der Datenbank saugen, blog Version, aktuelle werden zuerst angezeigt.
		// TODO: diesen Scheiß von zusammengewürfelten Strings zusammenfassen
		$select = "SELECT DISTINCT reporeID, header, teaser_list, erstellungsdatum, dok_show_teaser_link,
				 dok_teaserfix, pub_verfall, pub_start, pub_verfall_page, pub_start_page, pub_wohin, pub_dauerhaft, 
				 lan_teaser, lan_teaser_img_fertig, lan_article, " .
			$this->cms->papoo_repore .
			".order_id, order_id_start, cattextid, timestamp, comment_yn, allow_comment, dokuser, count,
				 count_download, lan_teaser_link, teaser_bild_html, url_header, lan_metatitel, lan_metadescrip,
				 lan_metakey, teaser_atyn, uberschrift_hyn, cat_category_id, stamptime ";
		$select .= ", lcat_id ,lart_order_id";
		$select .= " FROM " . $this->cms->papoo_repore . ", " .
			$this->cms->papoo_language_article . "," .
			$this->cms->papoo_lookup_article . "," .
			$this->cms->papoo_lookup_ug;
		$select .= ", " . $this->cms->tbname['papoo_lookup_art_cat'] . ", " . $this->cms->tbname['papoo_me_nu'];
		$select .= " WHERE ";

		// 1. Fall: Menüpunkt Startseite UND nur die Artikel,
		// die auf der Startseite gelistet werden sollen UND (reporeid leer ODER "%" (= alle))
		if ($menuid == 1 && $start == 0 && (!is_numeric($this->checked->reporeid))) {
			$select .= " publish_yn_lang='1' AND dok_show_teaser_teaser='0' AND allow_publish='1' AND $docatlist";
			$select .= " teaser_list $like_teas '$teas' AND reporeID $like_repo '" . $this->db->escape($this->checked->reporeid) . "' ";
			$select .= "AND reporeID=lan_repore_id AND lang_id='" . $this->cms->lang_id . "' ";
			$select .= " AND reporeID=article_id AND lart_id = reporeID ";
			$select .= " AND gruppeid_id=gruppenid";
			$select .= " AND userid='" . $this->user->userid . "'";
			if($this->content->template['anzeig_filter']==1) {
				//ARTIKEL-FILTER SELECT
				$select_all = $select;
			}
		}
		else {
			// 2. Fall: reporeid ist numerisch (= gültige Artikel-ID)
			if (is_numeric($this->checked->reporeid)) {
				$select .= " publish_yn_lang='1' AND allow_publish='1' AND $docatlist";
				$select .= " teaser_list $like_teas '$teas' AND lcat_id $like '$selectmenuid' AND reporeID $like_repo '" .
					$this->db->escape($this->checked->reporeid) . "' AND lcat_id=menuid ";
				$select .= "AND reporeID=lan_repore_id AND lang_id='" . $this->cms->lang_id . "' ";
				$select .= " AND reporeID=article_id AND lart_id = reporeID";
				$select .= " AND gruppeid_id=gruppenid";
				$select .= " AND userid='" . $this->user->userid . "'";
				if($this->content->template['anzeig_filter']==1) {
					//ARTIKEL-FILTER SELECT
					$select_all = $select;
				}
			}
			else {
				$this->check_sub();
				if(!isset($this->sub_menids)) {
					$this->sub_menids = NULL;
				}
				if (!is_array($this->sub_menids)) {
					$select .= " publish_yn_lang='1' AND dok_show_teaser_teaser='0' AND allow_publish='1' AND $docatlist";
					$select .= " teaser_list $like_teas '$teas' AND lcat_id $like '$selectmenuid' AND reporeID $like_repo '" .
						$this->db->escape($this->checked->reporeid) . "' AND lcat_id=menuid ";
					$select .= "AND reporeID=lan_repore_id AND lang_id='" . $this->cms->lang_id . "' ";
					$select .= " AND reporeID=article_id AND lart_id = reporeID";
					$select .= " AND gruppeid_id=gruppenid";
					$select .= " AND userid='" . $this->user->userid . "'";
				}
				else {
					$this->sub_menids[] = $this->checked->menuid;
					$anzmenids = count($this->sub_menids);
					$ij = 0;

					foreach ($this->sub_menids as $menids) {
						$ij++;
						$select .= " publish_yn_lang='1' AND dok_show_teaser_teaser='0' AND allow_publish='1' AND $docatlist";
						$select .= " teaser_list $like_teas '$teas' AND lcat_id $like '$menids' AND reporeID $like_repo '" .
							$this->db->escape($this->checked->reporeid) . "' AND lcat_id=menuid ";
						$select .= "AND reporeID=lan_repore_id AND lang_id='" . $this->cms->lang_id . "' ";
						$select .= " AND reporeID=article_id AND lart_id = reporeID";
						$select .= " AND gruppeid_id=gruppenid";
						$select .= " AND userid='" . $this->user->userid . "'";
						if ($ij < $anzmenids) {
							$select .= " OR ";
						}
					}
				}

				if ($this->content->template['anzeig_filter']==1) {
					//ARTIKEL-FILTER SELECT
					$select_all = $select;

					//ARTIKEL-FILTER WHERE
					if ($this->checked->von_article && $this->checked->bis_article) {
						$select .= " AND erstellungsdatum > '" .
							date('Y-m-d H:i:s', $this->checked->von_article - 60) . "'";
						$select .= " AND erstellungsdatum < '" .
							date('Y-m-d H:i:s', $this->checked->bis_article + 60) . "'";
					}
				}
			}
		}

		// Hier wird das finale SQL-Statement für die Artikel zusammengebastelt.
		IfNotSetNull($sqllimit);
		IfNotSetNull($orderfield);
		$select .= $orderfield . " " . $sqllimit . "";

		if (!is_numeric($this->checked->reporeid)) {
			$this->checked->reporeid = "";
		}
		if ($this->content->template['anzeig_filter'] == 1) {
			//ARTIKEL-FILTER liste der filter
			IfNotSetNull($select_all);
			$this->result_alle_artikel = $this->db->get_results($select_all);
		}

		$this->result_artikel = $this->db->get_results($select);
		$i = 0;
		$cache_reporeid = "";

		$filter_von = [];
		$filter_bis = [];

		if ($this->content->template['anzeig_filter'] == 1) {
			//ARTIKEL-FILTER filter bauen
			//wenn falsch gefiltert wurde fehlermeldung
			if ((isset($this->result_artikel) == false || count($this->result_artikel) == 0) && $this->checked->von_article) {
				$this->content->template['filter_falsch'] = "yes";
			}

			if (!empty($this->result_alle_artikel)) {
				foreach ($this->result_alle_artikel as $artikel)
				{
					$date_array = array();
					$date = date('M Y',strtotime($artikel->erstellungsdatum));
					$date_first = date('j M Y',strtotime($date));


					array_push($date_array, $date);
					array_push($date_array, strtotime($date_first));

					if (!in_array($date_array, $filter_von)) {
						array_push($filter_von, $date_array);
					}

					$now_array = array();

					$date_last = date('t M Y',strtotime($date));

					array_push($now_array, $date);
					array_push($now_array, strtotime($date_last));

					if (!in_array($now_array, $filter_bis)) {
						array_unshift($filter_bis, $now_array);
					}

				}
				//heute select einfügen
				$today = array();
				$now_date = date('t M Y',time());

				array_push($today, $this->content->template['heute_select']);
				array_push($today, strtotime($now_date)+60);

				array_unshift($filter_bis, $today);
			}
		}

		$this->content->template['article_von'] = $filter_von;
		$this->content->template['article_bis'] = $filter_bis;

		if (!empty($this->result_artikel)) {
			foreach ($this->result_artikel as $artikel) {
				if ($artikel->reporeID == $cache_reporeid) {
					$i++;
					continue;
				}

				$cache_reporeid = $artikel->reporeID;

				if (!empty($this->result_artikel[$i]->lcat_id)) {
					$this->result_artikel[$i]->cattextid = $this->result_artikel[$i]->lcat_id;
				}

				if (($menuid == 1 && empty($this->checked->reporeid)) or ($menuid == "x" && empty($this->checked->reporeid))) {

					$start = strtotime($artikel->pub_start_page);
					$verfall = strtotime($artikel->pub_verfall_page);
					$heute = time();

					// Wenn start nicht ok oder verfall schon erreicht
					if (($start > $heute || $verfall < $heute) && $verfall>0) {
						// Verfall schon erreicht, dann Menüpunkt ändern und auf dauerhaft setzen
						if ($verfall < $heute && $artikel->pub_verfall_page != "0" && !empty($artikel->pub_verfall_page)) {
							if ($artikel->pub_wohin != "del") {
								// soll inaktiviert werden

								$sql = sprintf("UPDATE %s SET teaser_list='0' WHERE reporeID='%d' LIMIT 1",
									$this->cms->tbname['papoo_repore'],
									$this->db->escape($artikel->reporeID)
								);
								$this->db->query($sql);
							}
							else {
								$sql = sprintf("DELETE FROM %s WHERE reporeID='%d' LIMIT 1",
									$this->cms->tbname['papoo_repore'],
									$this->db->escape($artikel->reporeID)
								);
								$this->db->query($sql);

								$sql = sprintf("DELETE FROM %s WHERE lan_repore_id='%d' LIMIT 1",
									$this->cms->tbname['papoo_language_article'],
									$this->db->escape($artikel->reporeID)
								);
								$this->db->query($sql);

								$sql = sprintf("DELETE FROM %s WHERE article_id='%d' LIMIT 1",
									$this->cms->tbname['papoo_lookup_article'],
									$this->db->escape($artikel->reporeID)
								);
								$this->db->query($sql);

								$sql = sprintf("DELETE FROM %s WHERE lart_id='%d' LIMIT 1",
									$this->cms->tbname['papoo_lookup_art_cat'],
									$this->db->escape($artikel->reporeID)
								);
								$this->db->query($sql);
							}
							// Dann überspringen
						}
						if ($artikel->pub_verfall_page != "0" && $menuid!=1) {
							$i++;
							continue;
						}
						elseif ($menuid==1) {
							$i++;
							continue;
						}
					}

					if(!isset($this->result_artikel[$i])) {
						$this->result_artikel[$i] = NULL;
					}

					$neu[$i] = $this->result_artikel[$i];
					$i++;
				}

				if ($menuid >= 1 or $menuid == "xy") {
					// Timestammp heute
					$heute = time();
					// Verfallstimestamp
					$dat = explode(".", $artikel->pub_verfall);

					if(!isset($dat['2'])) {
						$dat['2'] = NULL;
					}

					$dat_t1 = explode(" ", $dat['2']);

					if(!isset($dat_t1['1'])) {
						$dat_t1['1'] = NULL;
					}

					$dat_t2 = explode(":", $dat_t1['1']);
					if (empty($dat_t2['0'])) {
						$dat_t2['0'] = "0";
					}

					if (empty($dat_t2['1'])) {
						$dat_t2['1'] = "0";
					}

					if ($dat[2] > 1970) {
						$verfall = @mktime((int)$dat_t2['0'], (int)$dat_t2['1'], 0, (int)$dat[1], (int)$dat[0], (int)$dat[2]);
					}
					else {
						$verfall = 0;
					}
					// Starttimestamp
					$dat = explode(".", $artikel->pub_start);

					if(!isset($dat['2'])) {
						$dat['2'] = NULL;
					}

					$dat_t1 = explode(" ", $dat['2']);

					if(!isset($dat_t1['1'])) {
						$dat_t1['1'] = NULL;
					}

					$dat_t2 = explode(":", $dat_t1['1']);
					if ($dat[2] > 1970) {
						$start = @mktime($dat_t2['0'], $dat_t2['1'], 0, $dat[1], $dat[0], $dat[2]);
					}
					else {
						$start = 0;
					}
					// Wenn nicht dauerhaft
					if ($artikel->pub_dauerhaft != 1) {
						// Wenn start nicht ok oder verfall schon erreicht
						if ($start > $heute || $verfall < $heute) {
							// Verfall schon erreicht, dann Menüpunkt ändern und auf dauerhaft setzen
							if ($verfall < $heute) {
								// soll verschoben werden
								if ($artikel->pub_wohin != 0 || $artikel->pub_wohin == "del") {
									if ($artikel->pub_wohin != "del") {
										$sql = sprintf("UPDATE %s SET cattextid='%s', pub_dauerhaft='1' WHERE reporeID='%d' LIMIT 1",
											$this->cms->tbname['papoo_repore'],
											$this->db->escape($artikel->pub_wohin),
											$this->db->escape($artikel->reporeID)
										);
										$this->db->query($sql);

										$sql = sprintf("DELETE FROM %s WHERE lart_id='%d' ",
											$this->cms->tbname['papoo_lookup_art_cat'],
											$this->db->escape($artikel->reporeID)
										);
										$this->db->query($sql);

										$sql = sprintf("INSERT INTO %s SET lart_id='%d', lcat_id='%d' ",
											$this->cms->tbname['papoo_lookup_art_cat'],
											$this->db->escape($artikel->reporeID),
											$this->db->escape($artikel->pub_wohin)
										);
										#$this->db->query($sql);
									}
									else {
										$sql = sprintf("DELETE FROM %s WHERE reporeID='%d' LIMIT 1",
											$this->cms->tbname['papoo_repore'],
											$this->db->escape($artikel->reporeID)
										);
										$this->db->query($sql);

										$sql = sprintf("DELETE FROM %s WHERE lan_repore_id='%d' LIMIT 1",
											$this->cms->tbname['papoo_language_article'],
											$this->db->escape($artikel->reporeID)
										);
										$this->db->query($sql);

										$sql = sprintf("DELETE FROM %s WHERE article_id='%d' LIMIT 1",
											$this->cms->tbname['papoo_lookup_article'],
											$this->db->escape($artikel->reporeID)
										);
										$this->db->query($sql);
									}
								}
								// soll inaktiviert werden
								else {
									$sql = sprintf("UPDATE %s SET publish_yn_lang='0' WHERE lan_repore_id='%d' LIMIT 1",
										$this->cms->tbname['papoo_language_article'],
										$this->db->escape($artikel->reporeID)
									);
								}
								$this->db->query($sql);
							}
							$i++;
							// Dann überspringen
							continue;
						}
					}
					// erzeugt ohne Abfrage leeres Array und fährt später zu for-each-Fehler

					if (!empty($this->result_artikel[$i])) {
						$neu[$i] = $this->result_artikel[$i];
						$i++;
					}
				}
			}
		}
		else {
			if (!empty($this->checked->reporeid) && $this->checked->menuid == 1) {
				$this->no_cache = 1;
			}

		}
		if (empty($neu) && !empty($this->checked->reporeid)) {
			$neu = "";
			$this->diverse->not_found();
		}

		IfNotSetNull($neu);

		if ($menuid == 1) {
			return $this->result_artikel = $neu;
		}
		else {
			return $this->result_artikel = $neu;
		}
	}

	/**
	 * @param $tag
	 * @param int $menuid
	 * @param string $modus
	 * @param int $start
	 * @return string
	 */
	function get_artikel_related($tag, $menuid = 1, $modus = "", $start = 0)
	{
		global $template;

		if (!defined("admin")) {
			if ($template == "guestbook.html") {
				// Kommentare anzeigen
				$this->do_guestbook();
			}
			else {
				// Kommentare anzeigen
				$this->do_comment();
			}
		}

		$docatlist = "";

		if (empty($this->checked->reporeid)) {
			// Keine Abfrage => like %
			$like_repo = "LIKE";

			// wenn Startseite
			if($menuid == 1) {
				// kein Menuid Bezug in der sql Abfrage => like %
				$selectmenuid = "1";
				$like = "LIKE";
				// Aber Abfrage ob geteasert werden soll auf der Startseite
				$like_teas = "=";
				$teas = "1";
				// Order-ID Start verwenden
				$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, order_id_start ASC ";
			}
			// Nicht Startseite
			else {
				// Menuid Bezug in der sql Abfrage => =$menuid
				// $selectmenuid = $this->checked->menuid;
				$selectmenuid = $this->db->escape($menuid);
				$like = "=";
				// Keine Abfrage ob geteasert werden soll
				$like_teas = "LIKE";
				$teas = "%";
				// Normles Order-ID Feld verwenden
				if ($this->cms->stamm_artikel_order == 1) {
					//alte Methode $orderfield = " ORDER BY dok_teaserfix DESC, reporeID DESC ";
					$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, lart_order_id ASC ";
				}
				else {
					$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, lart_order_id ASC ";
				}
			}

			if ($menuid == "x") {
				// kein Menuid Bezug in der sql Abfrage => like %
				$selectmenuid = "%";
				$like = "LIKE";
				// Aber Abfrage ob geteasert werden soll auf der Startseite
				$like_teas = "LIKE";
				$teas = "%";
				if (empty($this->checked->nocat)) {
					$docatlist = " cat_category_id > '0' AND ";
				}

				// Order-ID Start verwenden
				if ($this->cms->stamm_artikel_order == 1) {
					$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, reporeID DESC ";
				}
				else {
					$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, lart_order_id DESC ";
				}
			}

			if ($menuid == "xy") {
				// kein Menuid Bezug in der sql Abfrage => like %
				$selectmenuid = $this->db->escape($this->checked->menuid);
				$like = "LIKE";
				// Aber Abfrage ob geteasert werden soll auf der Startseite
				$like_teas = "LIKE";
				$teas = "%";
				$docatlist = " cat_category_id > '0' AND ";
				// Order-ID Start verwenden
				if ($this->cms->stamm_artikel_order == 1) {
					$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, reporeID DESC ";
				}
				else {
					$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, lart_order_id ASC ";
				}
			}
		}
		else {
			$like_repo = "=";
			$like_teas = "LIKE";
			$teas = "%";
			$selectmenuid = "%";
			$like = "LIKE";
			$orderfield = "";
		}
		// Checken auf easy Anzeige
		if (!empty($this->cms->tbname['papoo_easyedit_daten']) &&
			$menuid != "x" &&
			$menuid != "1" &&
			$modus != "ALL" &&
			$this->cms->stamm_artikel_order == 1) {
			$sql = sprintf("SELECT easy_order_id FROM %s WHERE easy_men_id='%d'",
				$this->cms->tbname['papoo_easyedit_daten'],
				$this->db->escape($this->checked->menuid)
			);
			$orderid = $this->db->get_var($sql);
			if ($orderid == 1) {
				$orderfield = " GROUP BY reporeID ORDER BY dok_teaserfix DESC, reporeID DESC ";
			}
		}

		if (empty($this->checked->reporeid)) {
			$this->checked->reporeid = "%";
		}

		if (!is_numeric($this->checked->reporeid)) {
			switch ($modus) {
			case "ALL":
				$sqllimit = "";
				break;

			case "":
			default:
				$sqllimit = $this->cms->sqllimit;
			}

			if ($menuid == "x") {
				$sqllimit = "LIMIT 0,200";
			}
		}

		//Die Artikelsortierung rausholen
		if (is_array($this->menu->data_front_complete)) {
			foreach ($this->menu->data_front_complete as $key => $value) {
				if ($menuid == $value['menuid']) {
					$artikel_sort = $value['artikel_sort'];
				}
			}
		}

		IfNotSetNull($artikel_sort);
		switch ($artikel_sort) {
		case "2":
			// Aktuellste Zuerst
			$orderfield = "GROUP BY reporeID ORDER BY dok_teaserfix DESC, stamptime DESC, lart_order_id ASC";

			break;

			// Nach Orderid
		case "3":
			//
			$orderfield = "GROUP BY reporeID ORDER BY dok_teaserfix DESC, lart_order_id ASC";
			break;

		case "4":
			// Zufällig
			$orderfield = "GROUP BY reporeID ORDER BY dok_teaserfix DESC, RAND()";
			break;

		case "5":
			// Zufällig
			$orderfield = "GROUP BY reporeID ORDER BY dok_teaserfix DESC, header ASC";
			break;

		case "6":
			// Erstellungsdatum
			$orderfield = "GROUP BY reporeID ORDER BY dok_teaserfix DESC, erstellungsdatum DESC";
			break;

		default:
			break;
		}

		if (!empty($this->orderfield)) {
			$orderfield = $this->orderfield;

		}

		// Artikel aus der Datenbank saugen, blog Version, aktuelle werden zuerst angezeigt.
		// TODO: diesen Scheiß von zusammengewürfelten Strings zusammenfassen
		$select = "SELECT DISTINCT reporeID, header, erstellungsdatum, dok_show_teaser_link,dok_teaserfix,
		 pub_verfall, pub_start, pub_verfall_page, pub_start_page, pub_wohin, pub_dauerhaft, lan_teaser, 
		 lan_teaser_img_fertig, lan_article, " .
			$this->cms->papoo_repore .
			".order_id, order_id_start, cattextid, timestamp, comment_yn, allow_comment, dokuser, count, 
			count_download, lan_teaser_link, teaser_bild_html, url_header, lan_metatitel, lan_metadescrip, 
			lan_metakey, teaser_atyn, uberschrift_hyn, cat_category_id, stamptime ";
		$select .= ", lcat_id ,lart_order_id ";
		$select .= "FROM " . $this->cms->papoo_repore . ", " . $this->cms->papoo_language_article . "," .
			$this->cms->papoo_lookup_article . "," . $this->cms->papoo_lookup_ug;
		$select .= ", " . $this->cms->tbname['papoo_lookup_art_cat'] . ", " . $this->cms->tbname['papoo_me_nu'];


		$select .= " WHERE ";

		// 1. Fall: Menüpunkt Startseite UND nur die Artikel,
		// die auf der Startseite gelistet werden sollen UND (reporeid leer ODER "%" (= alle))
		//if ($menuid == 1 && $start == 0 && (empty($this->checked->reporeid) || $this->checked->reporeid == '%'))
		if ($start == 0 && (!is_numeric($this->checked->reporeid))) {
			$select .= " publish_yn_lang='1' AND dok_show_teaser_teaser='0' AND allow_publish='1' AND $docatlist";
			$select .= " teaser_list $like_teas '$teas' AND reporeID $like_repo '" . $this->db->escape($this->checked->reporeid) . "' ";
			$select .= "AND lan_metakey='" . $tag . "'";
			$select .= "AND reporeID=lan_repore_id AND lang_id='" . $this->cms->lang_id . "' ";
			$select .= " AND reporeID=article_id AND lart_id = reporeID ";
			$select .= " AND gruppeid_id=gruppenid";
			$select .= " AND userid='" . $this->user->userid . "'";
		}
		else {
			// 2. Fall: reporeid ist numerisch (= gültige Artikel-ID)
			if (is_numeric($this->checked->reporeid)) {
				$select .= " publish_yn_lang='1' AND allow_publish='1' AND $docatlist";
				$select .= " teaser_list $like_teas '$teas' AND lcat_id $like '$selectmenuid' AND reporeID $like_repo '" .
					$this->db->escape($this->checked->reporeid) . "' AND lcat_id=menuid ";
				$select .= "AND lan_metakey='" . $tag . "'";
				$select .= "AND reporeID=lan_repore_id AND lang_id='" . $this->cms->lang_id . "' ";
				$select .= " AND reporeID=article_id AND lart_id = reporeID";
				$select .= " AND gruppeid_id=gruppenid";
				$select .= " AND userid='" . $this->user->userid . "'";
			}
			else {
				$this->check_sub();
				if (!is_array($this->sub_menids)) {
					$select .= " publish_yn_lang='1' AND dok_show_teaser_teaser='0' AND allow_publish='1' AND $docatlist";
					$select .= " teaser_list $like_teas '$teas' AND lcat_id $like '$selectmenuid' AND reporeID $like_repo '" .
						$this->db->escape($this->checked->reporeid) . "' AND lcat_id=menuid ";
					$select .= "AND lan_metakey='" . $tag . "'";
					$select .= "AND reporeID=lan_repore_id AND lang_id='" . $this->cms->lang_id . "' ";
					$select .= " AND reporeID=article_id AND lart_id = reporeID";
					$select .= " AND gruppeid_id=gruppenid";
					$select .= " AND userid='" . $this->user->userid . "'";
				}
				else {
					$this->sub_menids[] = $this->checked->menuid;
					$anzmenids = count($this->sub_menids);
					$ij = 0;

					foreach ($this->sub_menids as $menids) {
						$ij++;
						$select .= " publish_yn_lang='1' AND dok_show_teaser_teaser='0' AND allow_publish='1' AND $docatlist";
						$select .= " teaser_list $like_teas '$teas' AND lcat_id $like '$menids' AND reporeID $like_repo '" .
							$this->db->escape($this->checked->reporeid) . "' AND lcat_id=menuid ";
						$select .= "AND lan_metakey='" . $tag . "'";
						$select .= "AND reporeID=lan_repore_id AND lang_id='" . $this->cms->lang_id . "' ";
						$select .= " AND reporeID=article_id AND lart_id = reporeID";
						$select .= " AND gruppeid_id=gruppenid";
						$select .= " AND userid='" . $this->user->userid . "'";
						if ($ij < $anzmenids) {
							$select .= " OR ";
						}
					}
				}
			}
		}

		IfNotSetNull($sqllimit);

		$select .= $orderfield . " " . $sqllimit . "";

		if (!is_numeric($this->checked->reporeid)) {
			$this->checked->reporeid = "";
		}

		$this->result_artikel = $this->db->get_results($select);

		// Dann durchgehen ob die Zeitpunkte stimmen
		$i = 0;
		$cache_reporeid = "";

		if (!empty($this->result_artikel)) {
			foreach ($this->result_artikel as $artikel) {
				if ($artikel->reporeID == $cache_reporeid) {
					$i++;
					continue;
				}

				$cache_reporeid = $artikel->reporeID;
				if (!empty($this->result_artikel[$i]->lcat_id)) {
					$this->result_artikel[$i]->cattextid = $this->result_artikel[$i]->lcat_id;
				}

				if (($menuid == 1 && empty($this->checked->reporeid)) or ($menuid == "x" && empty($this->checked->reporeid))) {
					// Timestammp heute
					$heute = time();
					$start = 0;
					$verfall = 0;
					// Verfallstimestamp
					$dat = explode(".", $artikel->pub_verfall_page);
					$dat_t1 = explode(" ", $dat['2']);
					$dat_t2 = explode(":", $dat_t1['1']);

					if (empty($dat_t2['0'])) {
						$dat_t2['0'] = "0";
					}
					if (empty($dat_t2['1'])) {
						$dat_t2['1'] = "0";
					}
					if ($dat[2] > 1970) {
						$verfall = @mktime($dat_t2['0'], $dat_t2['1'], 0, $dat[1], $dat[0], $dat[2]);
					}
					// Starttimestamp
					$dat = explode(".", $artikel->pub_start_page);
					$dat_t1 = explode(" ", $dat['2']);
					$dat_t2 = explode(":", $dat_t1['1']);
					if (empty($dat_t2['0'])) {
						$dat_t2['0'] = "0";
					}
					if (empty($dat_t2['1'])) {
						$dat_t2['1'] = "0";
					}
					if ($dat[2] > 1970) {
						$start = @mktime($dat_t2['0'], $dat_t2['1'], 0, $dat[1], $dat[0], $dat[2]);
					}
					// Wenn start nicht ok oder verfall schon erreicht
					if (($start > $heute || $verfall < $heute) && $verfall>0) {
						// Verfall schon erreicht, dann Menüpunkt ändern und auf dauerhaft setzen
						if ($verfall < $heute && $artikel->pub_verfall_page != "0" && !empty($artikel->pub_verfall_page)) {
							if ($artikel->pub_wohin != "del") {
								// soll inaktiviert werden
								$sql = sprintf("UPDATE %s SET teaser_list='0' WHERE reporeID='%d' LIMIT 1",
									$this->cms->tbname['papoo_repore'],
									$this->db->escape($artikel->reporeID)
								);
								$this->db->query($sql);
							}
							else {
								$sql = sprintf("DELETE FROM %s WHERE reporeID='%d' LIMIT 1",
									$this->cms->tbname['papoo_repore'],
									$this->db->escape($artikel->reporeID)
								);
								$this->db->query($sql);

								$sql = sprintf("DELETE FROM %s WHERE lan_repore_id='%d' LIMIT 1",
									$this->cms->tbname['papoo_language_article'],
									$this->db->escape($artikel->reporeID)
								);
								$this->db->query($sql);

								$sql = sprintf("DELETE FROM %s WHERE article_id='%d' LIMIT 1",
									$this->cms->tbname['papoo_lookup_article'],
									$this->db->escape($artikel->reporeID)
								);
								$this->db->query($sql);

								$sql = sprintf("DELETE FROM %s WHERE lart_id='%d' LIMIT 1",
									$this->cms->tbname['papoo_lookup_art_cat'],
									$this->db->escape($artikel->reporeID)
								);
								$this->db->query($sql);
							}
							// Dann überspringen
						}
						if ($artikel->pub_verfall_page != "0" && $menuid!=1) {
							$i++;
							continue;
						}
						elseif ($menuid==1) {
							$i++;
							continue;
						}
					}
					$neu[$i] = $this->result_artikel[$i];
					$i++;
				}

				if ($menuid >= 1 or $menuid == "xy") {
					// Timestammp heute
					$heute = time();
					// Verfallstimestamp
					$dat = explode(".", $artikel->pub_verfall);
					$dat_t1 = explode(" ", $dat['2']);
					$dat_t2 = explode(":", $dat_t1['1']);
					if (empty($dat_t2['0'])) {
						$dat_t2['0'] = "0";
					}

					if (empty($dat_t2['1'])) {
						$dat_t2['1'] = "0";
					}

					if ($dat[2] > 1970) {
						$verfall = @mktime($dat_t2['0'], $dat_t2['1'], 0, $dat[1], $dat[0], $dat[2]);
					}
					else {
						$verfall = 0;
					}
					// Starttimestamp
					$dat = explode(".", $artikel->pub_start);
					$dat_t1 = explode(" ", $dat['2']);
					$dat_t2 = explode(":", $dat_t1['1']);
					if ($dat[2] > 1970) {
						$start = @mktime($dat_t2['0'], $dat_t2['1'], 0, $dat[1], $dat[0], $dat[2]);
					}
					else {
						$start = 0;
					}
					// Wenn nicht dauerhaft
					if ($artikel->pub_dauerhaft != 1) {
						// Wenn start nicht ok oder verfall schon erreicht
						if ($start > $heute || $verfall < $heute) {
							// Verfall schon erreicht, dann Menüpunkt ändern und auf dauerhaft setzen
							if ($verfall < $heute) {
								// soll verschoben werden
								if ($artikel->pub_wohin != 0 || $artikel->pub_wohin == "del") {
									if ($artikel->pub_wohin != "del") {
										$sql = sprintf("UPDATE %s SET cattextid='%s', pub_dauerhaft='1' WHERE reporeID='%d' LIMIT 1",
											$this->cms->tbname['papoo_repore'],
											$this->db->escape($artikel->pub_wohin),
											$this->db->escape($artikel->reporeID)
										);
										$this->db->query($sql);

										$sql = sprintf("DELETE FROM %s WHERE lart_id='%d' ",
											$this->cms->tbname['papoo_lookup_art_cat'],
											$this->db->escape($artikel->reporeID)
										);
										$this->db->query($sql);

										$sql = sprintf("INSERT INTO %s SET lart_id='%d', lcat_id='%d' ",
											$this->cms->tbname['papoo_lookup_art_cat'],
											$this->db->escape($artikel->reporeID),
											$this->db->escape($artikel->pub_wohin)
										);
										#$this->db->query($sql);
									}
									else {
										$sql = sprintf("DELETE FROM %s WHERE reporeID='%d' LIMIT 1",
											$this->cms->tbname['papoo_repore'],
											$this->db->escape($artikel->reporeID)
										);
										$this->db->query($sql);

										$sql = sprintf("DELETE FROM %s WHERE lan_repore_id='%d' LIMIT 1",
											$this->cms->tbname['papoo_language_article'],
											$this->db->escape($artikel->reporeID)
										);
										$this->db->query($sql);

										$sql = sprintf("DELETE FROM %s WHERE article_id='%d' LIMIT 1",
											$this->cms->tbname['papoo_lookup_article'],
											$this->db->escape($artikel->reporeID)
										);
										$this->db->query($sql);
									}
								}
								// soll inaktiviert werden
								else {
									$sql = sprintf("UPDATE %s SET publish_yn_lang='0' WHERE lan_repore_id='%d' LIMIT 1",
										$this->cms->tbname['papoo_language_article'],
										$this->db->escape($artikel->reporeID)
									);
								}
								$this->db->query($sql);
							}
							$i++;
							// Dann überspringen
							continue;
						}
					}
					// erzeugt ohne Abfrage leeres Array und führt später zu for-each-Fehler
					if (!empty($this->result_artikel[$i])) {
						$neu[$i] = $this->result_artikel[$i];
						$i++;
					}
				}
			}
		}
		else {
			if (!empty($this->checked->reporeid) && $this->checked->menuid == 1) {
				$this->no_cache = 1;
			}
		}

		if (empty($neu) && !empty($this->checked->reporeid)) {
			$neu = "";
			$this->diverse->not_found();
		}

		IfNotSetNull($neu);

		if ($menuid == 1) {
			return $this->result_artikel = $neu;
		}
		else {
			return $this->result_artikel = $neu;
		}
	}

	/**
	 * Artikel aus der Datenbank zur Teaser Liste übergeben
	 * @return array teaser Liste
	 */
	function make_teaser()
	{
		// Daten in ein assoziatives Array einlesen
		$selectlink = sprintf("SELECT menuid, menulink FROM %s",
			$this->cms->papoo_menu
		);
		$resultlink = $this->db->get_results($selectlink, ARRAY_A);

		$table_data = array();
		$islink_ok = "ok";
		$start = 0;

		// Resultate übergeben
		$result = $this->get_artikel($this->checked->menuid, "", $start);

		// TODO normale Startseitenzuordnung wird hier nicht berücksichtigt
		if ($GLOBALS["checked"]->menuid == 1 && is_array($result)) {
			$result = array_filter($result, function ($article) {
				// FIXME: Filter auf Objekte angewendet da hier Fehler ausgespuckt wurden
				if(is_object($article)) {
					$start = strtotime($article->pub_start_page);
					$end = strtotime($article->pub_verfall_page);
					return
						$article->teaser_list == 1 &&
						(time() >= $start || $start === false) &&
					        (time() <= $end || $end === false);
				}
				else {return false;}
			});
		}

		// Wenn nicht leer, dann übergeben
		//$this->cms->tbname['papoo_lookup_art_cat']

		if(!isset($art_cat_loo)) {
			$art_cat_loo = NULL;
		}

		if (is_array($result)) {
			foreach ($result as $row) {
				if (!empty($row->reporeID)) {
					$art_cat_loo .= " OR lart_id='" . $row->reporeID . "' ";
				}
			}
		}
		$ascdesc = "DESC";
		if ($this->checked->menuid <= 1) {
			$ascdesc = "ASC";
		}
		$sql = sprintf("SELECT * FROM %s WHERE lart_id='xy' %s ORDER BY lcat_id %s",
			$this->cms->tbname['papoo_lookup_art_cat'],
			$art_cat_loo,
			$ascdesc
		);
		$res_lc = $this->db->get_results($sql, ARRAY_A);

		if (is_array($res_lc)) {
			foreach ($res_lc as $dat) {
				$lookup_cat[$dat['lart_id']] = $dat['lcat_id'];
			}
		}

		if (!empty($result)) {
			$lt_text = "";
			foreach ($result as $row) {
				if(!isset($row->dokuser)) {
					$row->dokuser = 0;
				}

				// Usernamen raussuchen ###
				$queryuser = sprintf("SELECT * FROM %s WHERE userid='%d'",
					$this->cms->papoo_user,
					$row->dokuser
				);
				$resultusi = $this->db->get_results($queryuser);

				// Wenn nicht leer dann zuweisen
				if (!empty($resultusi)) {
					foreach ($resultusi as $rowuser) {
						// Variablen zuweisen für das Template
						$this->author = $rowuser->username;
						$this->content->template['usernameid'] = $this->user->username;
					}
				}
				// überschrift bereinigen
				if(!isset($row->header)) {
					$row->header = NULL;
				}

				$uberschrift = $row->header;
				if (empty($uberschrift)) {
					continue;
				}
				// Wenn ein spezieller Text für den teaser Link eingegeben wurde, diesen anzeigen
				if (trim($row->lan_teaser_link) == "") {
					$islink = $row->header;
				}
				else {
					$islink = $row->lan_teaser_link;
				}
				// endcodieren des HTMLS des Teaser-Bildes
				// $row->teaser_bild_html = $row->teaser_bild_html;
				// FIXME: Korrektur für korrektes HTML
				if (empty($row->teaser_bild_html)) {
					$textunter = "<div class=\"teaserunder\">" . $row->lan_teaser_img_fertig . "" . $row->lan_teaser . "</div>";
				}
				else {
					$textunter = "<div class=\"teaserunder\">" . $row->teaser_bild_html . "" . $row->lan_teaser . "</div>";
				}

				$textunter = $this->diverse->do_pfadeanpassen($textunter);
				// $textunter = "<p>" . $row->lan_teaser . "</p>";
				// für die entsprechende id den Link heraussuchen

				if(!isset($lookup_cat[$row->reporeID])) {
					$lookup_cat[$row->reporeID] = NULL;
				}

				IfNotSetNull($lookup_cat);
				$row->cattextid = $lookup_cat[$row->reporeID];

				$lt = $row->cattextid;
				foreach ($resultlink as $guck) {
					if ($lt == $guck['menuid']) {
						// Link wird übergeben (Bsp. index.php)
						$lt_text = $guck['menulink'];
					}
					if ($lt == 0) {
						// Link wird übergeben (Bsp. index.php)
						$lt_text = "index.php";
					}
				}
				// wenn mod_rewrite dann bearbeiten
				if ($this->cms->mod_rewrite == 2) {
					if (!empty($lt_text)) {
						// .php wird entfernt FIXME für andere Endungen
						$lt_text = str_replace(".php", "", $lt_text);
					}
				}
				// wenn die Anzahl der Kommentare pro Artikel angezeigt werden soll
				if ($this->cms->comment_yn == 1) {
					// Zuweisung für das Template
					$commentok = 1;
					// Aus der Datenbank fischen
					$sql = sprintf("SELECT COUNT(*) FROM %s WHERE comment_article='%d' AND msg_frei='1'",
						$this->cms->papoo_message,
						$row->reporeID
					);
					$comment = $this->db->get_var($sql);
				}
				// nicht erlaubt die Anzahl der Kommentare zu zeigen
				else {
					$comment = "";
					$commentok = "";
				}
				// Die Artikel sollen nicht nur als teaser sondern komplett auf der Startseite angezeigt werden
				if ($this->cms->artikel_lang_yn == 1 ) {
					// Artikel-Text anfügen
					$textunter .= $this->diverse->do_pfadeanpassen($row->lan_article);
					$row->lan_teaser = $this->diverse->do_pfadeanpassen($row->lan_article);
					// Link zu Artikel entfernen
					//$islink = false;
				}

				// Wenn mod_rewrite, dann die Adress der images ändern!
				//Installation in Unterverzeichnis
				if ($this->cms->mod_rewrite == "2") {
					$textunter = $this->diverse->do_pfadeanpassen($textunter);
					$row->teaser_bild_html = $this->diverse->do_pfadeanpassen($row->teaser_bild_html);
				}

				// Donwloads zählen, bzw. Infos zum Download anzeigen, setzen (oder nicht) je nach Angabe im Artikel
				$this->download->infos_anzeigen = $row->count_download;

				if(!isset($_GET['is_lp'])) {
					$_GET['is_lp'] = NULL;
				}

				if (($_GET['is_lp'] != 1)) {
					$textunter = $this->download->replace_downloadlinks($textunter);
				}

				if(!isset($this->download->refresh)) {
					$this->download->refresh = NULL;
				}

				$this->content->template['refresh'] = $this->download->refresh;

				$this->m_url = array();
				$urldat_1 = "";

				if(!isset($this->subteaser)) {
					$this->subteaser = NULL;
				}

				if ($this->subteaser == 1) {
					$this->get_verzeichnis($row->cattextid);
				}
				else {
					if (is_numeric($this->checked->menuid) && $this->checked->menuid > 1) {
						$this->get_verzeichnis($this->checked->menuid);
					}
					if ($this->checked->menuid == "x" or $this->checked->menuid == 1) {
						$this->get_verzeichnis($row->cattextid);
					}
				}

				$row->lan_teaser = $this->diverse->do_pfadeanpassen($row->lan_teaser);
				if (($_GET['is_lp'] != 1)) {
					$row->lan_teaser = $this->download->replace_downloadlinks($row->lan_teaser);
				}

				$this->m_url = array_reverse($this->m_url);
				if (!empty($this->m_url)) {
					foreach ($this->m_url as $urldat) {
						$urldat_1 .= $this->menu->urlencode($urldat) . "/";
					}
				}
				$header_url = $urldat_1 . (($this->menu->urlencode($row->url_header))) . ".html";
				//Wenn freie URLS dann entsprechend nur die Artikel url nutzen
				if ($this->cms->mod_free == 1) {
					$header_url = (($this->menu->urlencode($row->url_header)));

					//Wie oft kommt der vor?
					$sql = sprintf("SELECT COUNT(lcat_id) FROM %s WHERE lart_id='%d'",
						$this->cms->tbname['papoo_lookup_art_cat'],
						$this->db->escape($row->reporeID)
					);
					$count_cat = $this->db->get_var($sql);

					//tiefsten menuepunkt holen und in url einbauen
					$sql2 = sprintf('SELECT MAX(T1.lcat_id)
									 FROM %1$s as T1,
									 %2$s as T2
									 WHERE T1.lcat_id = T2.menuid
									 AND T1.lart_id = %3$d
									 AND T2.level = (SELECT MAX(T2.level)
									 FROM %1$s as T1,
									 %2$s as T2
									 WHERE T1.lcat_id = T2.menuid
									 AND T1.lart_id = %3$d)',
						$this->cms->tbname['papoo_lookup_art_cat'],
						$this->cms->tbname['papoo_me_nu'],
						$this->db->escape($row->reporeID)
					);

					//Achtung Mehrfachzuordnung
					if ($count_cat > 1) {
						$klammer_id = "m-" . $this->db->get_var($sql2) . "";
						$last = substr($header_url, -1, 1);
						$last2 = substr($header_url, -5, 5);

						//Artikel mit .html
						if ($last2 == ".html") {
							$header_url = str_replace(".html", "-" . $klammer_id . ".html", $header_url);
						}
						//Artikel mit /
						if ($last == "/") {
							$header_url = substr($header_url, 0, -1);
							$header_url = $header_url . "-" . $klammer_id . "/";
						}
					}

					$first_url = $header_url[0];
					if ($first_url == "/") {
						$header_url = substr($header_url, 1, strlen($header_url));
					}
				}

				if ($this->cms->artikel_lang_yn == 1 && $this->checked->menuid >= 1) {
					$row->lan_teaser_img_fertig = "";
				}
				preg_match_all('/<img[^>]*>/Ui', $this->diverse->do_pfadeanpassen($row->lan_teaser), $img);

				if(!isset($img['0']['0'])) {
					$img['0']['0'] = NULL;
				}
				if(!isset($this->author)) {
					$this->author = NULL;
				}

				$img['0']['0'] = str_replace("/thumbs", "", $img['0']['0']);
				$img['0']['0'] = str_replace("style", "rel", $img['0']['0']);

				$teaserVideoTag = preg_match('~<video(?=\s|>).*?</video>~i', $row->lan_teaser, $teaserVideoTag)
					? $teaserVideoTag[0]
					: '';

				// Daten zuweisen
				$table_data[ ] = array(
					'text' => $textunter,
					'teaser_text' => $textunter,
					'islink' => $islink,
					'islink_ok' => $islink_ok,
					'uberschrift' => $uberschrift,
					'url_header' => $header_url,
					'article_datum' => $row->stamptime,
					'erstellungsdatum' => strtotime($row->erstellungsdatum),
					'lan_article' => $row->lan_article,
					'c' => $row->teaser_bild_html,
					'lan_teaser_img_fertig' => $this->diverse->do_pfadeanpassen($row->lan_teaser_img_fertig),
					'lan_teaser' => "<div class=\"teaser\">" . $row->lan_teaser . "</div>",
					'cat_category_id' => $row->cat_category_id,
					'reporeid' => $row->reporeID,
					'wie_oft' => $row->count,
					'comment' => $comment,
					'date_time' => $row->timestamp,
					'dok_show_teaser_link' => $row->dok_show_teaser_link,
					'dok_teaserfix' => $row->dok_teaserfix,
					'comment_ok' => $commentok,
					'cattextid' => $row->cattextid,
					// 'islinktext' => $this->content->template['message_2144'],
					'linktext' => $lt_text,
					'link_title' => str_replace("&quot;", "", $this->diverse->encode_quote($uberschrift)),
					'author' => $this->author,
					'teaserbild' => ($row->teaser_bild_html),
					'lan_teaser_img' => $img['0']['0'],
					'teaser_video' => $teaserVideoTag,
					'teaser_image_or_video' => $img[0][0] ?: $teaserVideoTag,
					'lan_teaser_sans' => substr(strip_tags($row->lan_teaser), 0, 150) . "...",
					'order_id_start' => ($row->order_id_start)
				);
			}
		}
		// Daten zurückgeben
		// return $this->list_start = $table_data;
		return $table_data;
	}

	/**
	 * @param $tag
	 * @return array
	 */
	function make_teaser_related($tag)
	{
		// Daten in ein assoziatives Array einlesen
		$selectlink = sprintf("SELECT menuid, menulink FROM %s",
			$this->cms->papoo_menu
		);
		$resultlink = $this->db->get_results($selectlink, ARRAY_A);

		$table_data = array();
		$islink_ok = "ok";
		$start = 0;

		// Resultate übergeben
		$result = $this->get_artikel_related($tag, $this->checked->menuid, "", $start);

		// Wenn nicht leer, dann übergeben
		//$this->cms->tbname['papoo_lookup_art_cat']
		if (is_array($result)) {
			foreach ($result as $row) {
				if (!empty($row->reporeID)) {
					$art_cat_loo = " OR lart_id='" . $row->reporeID . "' ";
				}
			}
		}

		IfNotSetNull($art_cat_loo);

		$ascdesc = "DESC";
		if ($this->checked->menuid <= 1) {
			$ascdesc = "ASC";
		}
		$sql = sprintf("SELECT * FROM %s WHERE lart_id='xy' %s ORDER BY lcat_id %s",
			$this->cms->tbname['papoo_lookup_art_cat'],
			$art_cat_loo,
			$ascdesc
		);
		$res_lc = $this->db->get_results($sql, ARRAY_A);
		if (is_array($res_lc)) {
			foreach ($res_lc as $dat) {
				$lookup_cat[$dat['lart_id']] = $dat['lcat_id'];
			}
		}

		if (!empty($result)) {
			$lt_text = "";
			foreach ($result as $row) {
				// Usernamen raussuchen ###
				$queryuser = sprintf("SELECT * FROM %s WHERE userid='%s'",
					$this->cms->papoo_user,
					$row->dokuser
				);
				$resultusi = $this->db->get_results($queryuser);
				// Wenn nicht leer dann zuweisen
				if (!empty($resultusi)) {
					foreach ($resultusi as $rowuser)
					{
						// Variablen zuweisen für das Template
						$this->author = $rowuser->username;
						$this->content->template['usernameid'] = $this->user->username;
					}
				}
				// überschrift bereinigen
				$uberschrift = $row->header;
				if (empty($uberschrift)) {
					continue;
				}
				// Wenn ein spezieller Text für den teaser Link eingegeben wurde, diesen anzeigen
				if (trim($row->lan_teaser_link) == "") {
					$islink = $row->header;
				}
				else {
					$islink = $row->lan_teaser_link;
				}
				// endcodieren des HTMLS des Teaser-Bildes
				// $row->teaser_bild_html = $row->teaser_bild_html;
				// FIXME Korrektur für korrektes HTML
				if (empty($row->teaser_bild_html)) {
					$textunter = "<div class=\"teaserunder\">" . $row->lan_teaser_img_fertig . "" . $row->lan_teaser . "</div>";
				}
				else {
					$textunter = "<div class=\"teaserunder\">" . $row->teaser_bild_html . "" . $row->lan_teaser . "</div>";
				}

				$textunter = $this->diverse->do_pfadeanpassen($textunter);
				// $textunter = "<p>" . $row->lan_teaser . "</p>";
				// für die entsprechende id den Link heraussuchen
				IfNotSetNull($lookup_cat);
				$row->cattextid = $lookup_cat[$row->reporeID];

				$lt = $row->cattextid;
				foreach ($resultlink as $guck) {
					if ($lt == $guck['menuid']) {
						// Link wird übergeben (Bsp. index.php)
						$lt_text = $guck['menulink'];
					}
					if ($lt == 0) {
						// Link wird übergeben (Bsp. index.php)
						$lt_text = "index.php";
					}
				}
				// wenn mod_rewrite dann bearbeiten
				if ($this->cms->mod_rewrite == 2) {
					if (!empty($lt_text)) {
						// .php wird entfernt FIXME für andere Endungen
						$lt_text = str_replace(".php", "", $lt_text);
					}
				}
				// wenn die Anzahl der Kommentare pro Artikel angezeigt werden soll
				if ($this->cms->comment_yn == 1) {
					// Zuweisung für das Template
					$commentok = 1;
					// Aus der Datenbank fischen
					$sql = sprintf("SELECT COUNT(*) FROM %s WHERE comment_article='%d' AND msg_frei='1'",
						$this->cms->papoo_message,
						$row->reporeID
					);
					$comment = $this->db->get_var($sql);
				}
				// nicht erlaubt die Anzahl der Kommentare zu zeigen
				else {
					$comment = "";
					$commentok = "";
				}
				// Die Artikel sollen nicht nur als teaser sondern komplett auf der Startseite angezeigt werden
				if ($this->cms->artikel_lang_yn == 1 ) {
					// Artikel-Text anfügen
					$textunter .= $this->diverse->do_pfadeanpassen($row->lan_article);
					$row->lan_teaser = $this->diverse->do_pfadeanpassen($row->lan_article);
					// Link zu Artikel entfernen
					//$islink = false;
				}

				// Wenn mod_rewrite, dann die Adress der images ändern!
				// Installation in Unterverzeichnis
				if ($this->cms->mod_rewrite == "2") {
					// $textunter = str_ireplace("\./images", $this->cms->webverzeichnis."/images", $textunter);
					$textunter = $this->diverse->do_pfadeanpassen($textunter);
					$row->teaser_bild_html = $this->diverse->do_pfadeanpassen($row->teaser_bild_html);
				}

				// Donwloads zählen, bzw. Infos zum Download anzeigen, setzen (oder nicht) je nach Angabe im Artikel
				$this->download->infos_anzeigen = $row->count_download;
				if (($_GET['is_lp'] != 1))
				{
					$textunter = $this->download->replace_downloadlinks($textunter);
				}

				$this->content->template['refresh'] = $this->download->refresh;

				$this->m_url = array();
				$urldat_1 = "";

				if ($this->subteaser == 1) {
					$this->get_verzeichnis($row->cattextid);
				}
				else {
					if (is_numeric($this->checked->menuid) && $this->checked->menuid > 1) {
						$this->get_verzeichnis($this->checked->menuid);
					}
					if ($this->checked->menuid == "x" or $this->checked->menuid == 1) {
						$this->get_verzeichnis($row->cattextid);
					}
				}

				$row->lan_teaser = $this->diverse->do_pfadeanpassen($row->lan_teaser);
				if (($_GET['is_lp'] != 1)) {
					$row->lan_teaser = $this->download->replace_downloadlinks($row->lan_teaser);
				}

				$this->m_url = array_reverse($this->m_url);

				if (!empty($this->m_url)) {
					foreach ($this->m_url as $urldat) {
						$urldat_1 .= $this->menu->urlencode($urldat) . "/";
					}
				}
				$header_url = $urldat_1 . (($this->menu->urlencode($row->url_header))) . ".html";
				//Wenn freie URLS dann entsprechend nur die Artikel url nutzen
				if ($this->cms->mod_free == 1) {
					$header_url = (($this->menu->urlencode($row->url_header)));

					//Wie oft kommt der vor?
					$sql = sprintf("SELECT COUNT(lcat_id) FROM %s WHERE lart_id='%d'",
						$this->cms->tbname['papoo_lookup_art_cat'],
						$this->db->escape($row->reporeID)
					);
					$count_cat = $this->db->get_var($sql);

					//Achtung Mehrfachzuordnung
					if ($count_cat > 1) {
						$klammer_id = "m-" . $this->checked->menuid . "";
						$last = substr($header_url, -1, 1);
						$last2 = substr($header_url, -5, 5);

						//Artikel mit .html
						if ($last2 == ".html") {
							$header_url = str_replace(".html", "-" . $klammer_id . ".html", $header_url);
						}
						//Artikel mit /
						if ($last == "/") {
							$header_url = substr($header_url, 0, -1);
							$header_url = $header_url . "-" . $klammer_id . "/";
						}
					}

					$first_url = $header_url[0];
					if ($first_url == "/") {
						$header_url = substr($header_url, 1, strlen($header_url));
					}
				}

				if ($this->cms->artikel_lang_yn == 1 && $this->checked->menuid >= 1) {
					$row->lan_teaser_img_fertig = "";
				}

				preg_match_all('/<img[^>]*>/Ui', $this->diverse->do_pfadeanpassen($row->lan_teaser), $img);

				$img['0']['0'] = str_replace("/thumbs", "", $img['0']['0']);
				$img['0']['0'] = str_replace("style", "rel", $img['0']['0']);

				$teaserVideoTag = preg_match('~<video(?=\s|>).*?</video>~i', $row->lan_teaser, $teaserVideoTag)
					? $teaserVideoTag[0]
					: '';

				// Daten zuweisen
				$table_data[ ] = array(
					'text' => $textunter,
					'teaser_text' => $textunter,
					'islink' => $islink,
					'islink_ok' => $islink_ok,
					'uberschrift' => $uberschrift,
					'url_header' => $header_url,
					'article_datum' => $row->stamptime,
					'erstellungsdatum' => strtotime($row->erstellungsdatum),
					'lan_article' => $row->lan_article,
					'teaser_bild_html' => $row->teaser_bild_html,
					'lan_teaser_img_fertig' => $this->diverse->do_pfadeanpassen($row->lan_teaser_img_fertig),
					'lan_teaser' => "<div class=\"teaser\">" . $row->lan_teaser . "</div>",
					'cat_category_id' => $row->cat_category_id,
					'reporeid' => $row->reporeID,
					'wie_oft' => $row->count,
					'comment' => $comment,
					'date_time' => $row->timestamp,
					'dok_show_teaser_link' => $row->dok_show_teaser_link,
					'dok_teaserfix' => $row->dok_teaserfix,
					'comment_ok' => $commentok,
					'cattextid' => $row->cattextid,
					// 'islinktext' => $this->content->template['message_2144'],
					'linktext' => $lt_text,
					'link_title' => str_replace("&quot;", "", $this->diverse->encode_quote($uberschrift)),
					'author' => $this->author,
					'teaserbild' => ($row->teaser_bild_html),
					'lan_teaser_img' => $img['0']['0'],
					'teaser_video' => $teaserVideoTag,
					'teaser_image_or_video' => $img[0][0] ?: $teaserVideoTag,
					'lan_teaser_sans' => substr(strip_tags($row->lan_teaser), 0, 150) . "..."
				);
			}
		}
		// Daten zurückgeben
		// return $this->list_start = $table_data;
		return $table_data;
	}

	/**
	 * Liste der Kategorien rausholen
	 * @return array|void
	 */
	function get_cat_liste()
	{
		if (!empty($_SESSION['langid_front'])) {
			$sprach_id = $_SESSION['langid_front'];
		}
		else {
			$sprach_id = $this->cms->lang_id;
		}

		$sql = sprintf("SELECT * FROM %s,%s WHERE cat_id=cat_lang_id AND cat_lang_lang='%s' ORDER BY cat_order_id",
			$this->cms->tbname['papoo_category_lang'],
			$this->cms->tbname['papoo_category'],
			$sprach_id
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Artikel aus der Datenbank zur Teaser Liste übergeben
	 * @return array|void teaser Liste
	 */
	function make_cat_teaser()
	{
		$_SESSION['pdb'] = array();
		//Liste der Kategorien rausholen die auf der Startseite angezeigt werden sollen
		$catliste = $this->get_cat_liste();

		// Daten in ein assoziatives Array einlesen
		$selectlink = sprintf("SELECT menuid, menulink FROM %s",
			$this->cms->papoo_menu
		);
		$resultlink = $this->db->get_results($selectlink, ARRAY_A);

		$islink_ok = "ok";
		// Resultate übergeben
		if ($this->checked->menuid <= 1) {
			$result = $this->get_artikel("x");
		}
		else {
			$result = $this->get_artikel("xy");
		}

		//Die Kategorien durchgehen und die ersten x rausholen
		if (is_array($catliste)) {
			foreach ($catliste as $catone) {
				if ($catone['cat_startseite_ok'] == 1) {
					// Wenn nicht leer, dann übergeben
					if (!empty($result)) {
						$lt_text = "";
						$ixx = 1;
						foreach ($result as $row) {
							//wenn tatsächlich gelistet werden soll
							if($row->teaser_list == "1") {
								$sql = sprintf("SELECT * FROM %s, %s WHERE cat_id=cat_lang_id 
														AND cat_lang_lang='%s' AND cat_id='%s' ORDER BY cat_order_id",
									$this->cms->tbname['papoo_category_lang'],
									$this->cms->tbname['papoo_category'],
									$this->content->template['languageget'][0]['lang_id'],
									$row->cat_category_id
								);
								$cat_text = $this->db->get_results($sql, ARRAY_A)[0]["cat_text"];

								if ($ixx <= $catone['cat_startseite_anzahl']) {
									if ($catone['cat_id'] == $row->cat_category_id) {
										$ixx++;
										// Überschrift bereinigen
										$uberschrift = $row->header;
										if (empty($uberschrift)) {
											continue;
										}

										// Wenn ein spezieller Text für den teaser Link eingegeben wurde, diesen anzeigen
										if (trim($row->lan_teaser_link) == "") {
											$islink = $row->header;
										}
										else {
											$islink = $row->lan_teaser_link;
										}

										$row->teaser_bild_html = $this->diverse->do_pfadeanpassen($row->teaser_bild_html);
										$row->lan_teaser_img_fertig = $this->diverse->do_pfadeanpassen($row->lan_teaser_img_fertig);
										$row->lan_teaser = $this->diverse->do_pfadeanpassen($row->lan_teaser);
										// für die entsprechende id den Link heraussuchen
										$lt = $row->cattextid;
										foreach ($resultlink as $guck) {
											if ($lt == $guck['menuid']) {
												// Link wird übergeben (Bsp. index.php)
												$lt_text = $guck['menulink'];
											}
											if ($lt == 0) {
												// Link wird übergeben (Bsp. index.php)
												$lt_text = "index.php";
											}
										}
										// wenn mod_rewrite dann bearbeiten
										if ($this->cms->mod_rewrite == 2) {
											if (!empty($lt_text)) {
												// .php wird entfernt TODO für andere Endungen
												$lt_text = str_replace(".php", "", $lt_text);
											}
										}
										// wenn die Anzahl der Kommentare pro Artikel angezeigt werden soll
										if ($this->cms->comment_yn == 1) {
											// Zuweisung für das Template
											$commentok = 1;
											// Aus der Datenbank fischen
											$sql = sprintf("SELECT COUNT(*) FROM %s WHERE comment_article='%d'",
												$this->cms->papoo_message,
												$row->reporeID
											);
											$comment = $this->db->get_var($sql);
										} // nicht erlaubt die Anzahl der Kommentare zu zeigen
										else {
											$comment = "";
											$commentok = "";
										}
										// Die Artikel sollen nicht nur als teaser sondern komplett auf der Startseite angezeigt werden
										if ($this->cms->artikel_lang_yn == 1) {
											// Artikel-Text anfügen
											$row->lan_article = $this->diverse->do_pfadeanpassen($row->lan_article);
											// Link zu Artikel entfernen
											$islink = false;
										}
										// Wenn mod_rewrite, dann die Adress der images ändern!
										// Installation in Unterverzeichnis
										if ($this->cms->mod_rewrite == "2") {
											// $textunter = str_ireplace("\./images", $this->cms->webverzeichnis."/images", $textunter);
											$row->teaser_bild_html = $this->diverse->do_pfadeanpassen($row->teaser_bild_html);
										}

										$this->m_url = array();
										$urldat_1 = "";
										$this->get_verzeichnis($row->cattextid);
										$this->m_url = array_reverse($this->m_url);

										if (!empty($this->m_url)) {
											foreach ($this->m_url as $urldat) {
												$urldat_1 .= $this->menu->urlencode($urldat) . "/";
											}
										}

										$header_url = $urldat_1 . (($this->menu->urlencode($row->url_header))) . ".html";
										if ($this->cms->mod_free == 1) {
											$header_url = (($this->menu->urlencode($row->url_header)));

											$last = substr($header_url, -5, 5);
											if ($last != ".html") {
												$header_url .= ".html";
											}

											$first_url = $header_url[0];
											if ($first_url == "/") {
												$header_url = substr($header_url, 1, strlen($header_url));
											}
										}
										// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
										IfNotSetNull($textunter);

										// Daten zuweisen
										$table_data = array(
											'text' => $textunter,
											'islink' => $islink,
											'lan_article' => $row->lan_article,
											'teaser_bild_html' => $row->teaser_bild_html,
											'lan_teaser_img_fertig' => $row->lan_teaser_img_fertig,
											'lan_teaser' => $row->lan_teaser,
											'article_datum' => $row->erstellungsdatum,
											'islink_ok' => $islink_ok,
											'uberschrift' => $uberschrift,
											'url_header' => $header_url,
											'reporeid' => $row->reporeID,
											'comment' => $comment,
											'comment_ok' => $commentok,
											'cattextid' => $row->cattextid,
											'cat_id' => $row->cat_category_id,
											'cat_text' => $cat_text,
											// 'islinktext' => $this->content->template['message_2144'],
											'linktext' => $lt_text,
											'link_title' => str_replace("&quot;", "",
												$this->diverse->encode_quote($uberschrift)),
											'teaserbild' => $row->teaser_bild_html
										);
										$catarray[$catone['cat_id']][0]['catname'] = $catone['cat_text'];
										$catarray[$catone['cat_id']][1][] = $table_data;
									}
								}
							}
						}
					}
				}
			}
		}
		// Daten zurückgeben
		// return $this->list_start = $table_data;
		IfNotSetNull($catarray);
		return $catarray;
	}

	/**
	 * Verzeichnisse rauskriegen bzw. Menünamen für mod_rewrite mit sprechurls
	 * @param string $menid
	 * @param string $search
	 * @return bool|void
	 */
	function get_verzeichnis($menid = "", $search = "")
	{
		if (!empty($_GET['var1']) or !empty($search) or $this->cms->mod_surls == 1) {

			$mendata = $this->menu->data_front_complete;
			foreach ($mendata as $menuitems) {
				// aktuelle menüid finden
				if ($menid == $menuitems['menuid']) {
					$this->m_url[ ] = ($menuitems['url_menuname']);
					// Nicht oberste Ebene, neu aufrufen
					if ($menuitems['untermenuzu'] != 0) {
						$this->get_verzeichnis($menuitems['untermenuzu']);
					}
					else {
						return true;
					}
				}
			}
		}
	}

	/**
	 * Hiermit wird die Druckausgabe eines Artikels erzeugt.
	 * @return void
	 */
	function make_print()
	{
		if (is_numeric($this->checked->reporeid_print))
		{
			// Verarbeitung
			$this->content->template['comment_data'] = array();
			$this->content->template['url'] = $_SERVER["HTTP_REFERER"];
			$table_data = array();
			// Gast Name
			$username_comment = "Gast";
			$this->diverse->no_output = "no";
			// Artikel raussuchen mit Kontrolle

			$select = sprintf("SELECT DISTINCT T1.*, T2.* FROM %s AS T1, %s AS T2,
								%s AS T3, %s AS T4
								WHERE
								T1.publish_yn_lang='1' AND T1.publish_yn_intra='0' AND T1.allow_publish='1'
								AND T1.reporeID='%d' AND T1.reporeID=T2.lan_repore_id AND T2.lang_id='%d'

								AND T1.reporeID=T3.article_id AND T3.gruppeid_id=T4.gruppenid
								AND T4.userid='%d'

								ORDER BY reporeID DESC",
				$this->cms->papoo_repore,
				$this->cms->papoo_language_article,
				$this->cms->papoo_lookup_article,
				$this->cms->papoo_lookup_ug,
				$this->db->escape($this->checked->reporeid_print),
				$this->db->escape($this->cms->lang_id),
				$this->db->escape($this->user->userid)
			);
			$result = $this->db->get_results($select);
			// Wenn ein Eintrag vorhanden ist, setzen
			if (!empty($result)) {
				$this->content->template['eintrag'] = '1';
			}
			$this->content->template['article'] = '1';
			// Einträge zuweisen
			if (!empty($result)) {
				// Kommentare zu diesem Artikel raussuchen
				if ($this->checked->forumid == 20) {
					$sql = sprintf("SELECT * FROM %s WHERE forumid='20' ORDER BY zeitstempel ASC",
						$this->cms->papoo_message
					);
				}
				else {
					if (!empty($this->checked->reporeid_print))
					{
						$sql = sprintf("SELECT * FROM %s WHERE comment_article='%d' ORDER BY zeitstempel ASC",
							$this->cms->papoo_message,
							$this->db->escape($this->checked->reporeid_print)
						);
					}
				}
				IfNotSetNull($sql);
				$sqlcomment = $this->db->get_results($sql);
				// wenn nicht leer,
				if (!empty($sqlcomment)) {
					// Setzen fürs Template -> Kommentare vorhanden
					$this->content->template['comment_ok'] = "ok";
					// für jeden Eintrag
					foreach ($sqlcomment as $rowcom) {
						// übergeben und parsen$this->bbcode->parse(
						$messagetextcom = $rowcom->messagetext;
						// Username des Eintrags raussuchen
						$sql = sprintf("SELECT username FROM %s WHERE userid='%d' LIMIT 1",
							$this->cms->papoo_user,
							$rowcom->userid
						);
						$sqlgetuser = $this->db->get_results($sql);
						// Username zuweisen , wenn nicht leer
						if (!empty($this->checked->sqlgetuser)) {
							foreach ($this->checked->sqlgetuser as $rowusercom) {
								$username_comment = $rowusercom->username;
							}
						}
						// Daten in ein Array suweisen
						array_push($this->content->template['comment_data'], array(
							'text' => $messagetextcom,
							//'zeitstempel' => $rowcom->zeitstempel,
							'date_time' => $rowcom->zeitstempel,
							'thema' => $rowcom->thema,
							'username_comment' => $username_comment
						));
					}
				}

				foreach ($result as $roweins) {
					$dokuser = $roweins->dokuser;
					// Autornamen raussuchen
					$queryuser = sprintf("SELECT * FROM %s WHERE userid='%d'",
						$this->cms->papoo_user,
						$dokuser
					);
					$resultusi = $this->db->get_results($queryuser);
					if (!empty($resultusi)) {
						foreach ($resultusi as $rowuser) {
							$this->content->template['author'] = $rowuser->username;
							$this->content->template['usernameid'] = $rowuser->userid;
						}
					}
					// HTML erzeugen
					$texteins = $roweins->lan_article;
					// Links etc. aus dem Text fischen und unten dranhängen
					$texteins = $this->replace->do_print($texteins);
					// mit dem Teaser zusammenlegen, aber nur wenn auch was im Teaser drin ist.
					if (!empty($roweins->lan_teaser)) {
						$texteins = "<div>" . $roweins->lan_teaser . "</div>" . $texteins;
					}

					$islinkmehr = $roweins->header;

					$this->content->template['comment_title'] = $islinkmehr;
					array_push($table_data, array(
						'islink' => $islinkmehr,
						'text' => $this->diverse->do_pfadeanpassen($texteins), //'zeitstempel' => $roweins->timestamp,
						'date_time' => $roweins->timestamp
					));
					$this->content->template['table_data'] = $table_data;
				}
			}
		}
	}

	/**
	 * @abstract Hier wird festgestellt ob ein Artikel im Frontend editiert werden darf.
	 * Dabei wird mit der reporeid gesucht und mit den Rechten gegengecheckt.
	 * Wenn alles positiv ist kommt ein edit Button im Frontend beim Artikel
	 * @param $editnr
	 * @return bool
	 */
	function check_edit($editnr)
	{
		$this->content->template['editlink'] = "";
		// checken ob der User überhaupt schreiben bzw. zugreifen darf
		$this->user->extern = "1";
		if ($this->user->check_intern() != "ok")
			return false;

		$sql = sprintf("SELECT t1.reporeID FROM %s AS t1, %s AS t2, %s AS t3
						WHERE t1.reporeID='%d' AND t1.reporeID=t2.article_wid_id
						AND t3.userid='%d' AND t2.gruppeid_wid_id=t3.gruppenid LIMIT 1",
			$this->cms->papoo_repore,
			$this->cms->tbname['papoo_lookup_write_article'],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->db->escape($editnr),
			$this->user->userid
		);
		$repo = $this->db->get_var($sql);

		if (!empty($repo)) {
			$this->content->template['editlink'] =
				PAPOO_WEB_PFAD .
				"/interna/artikel.php?menuid=11&amp;reporeid=" . $repo .
				"&amp;from=front&amp;f_langid=" . $this->cms->lang_id .
				"&amp;f_menuid=" . $this->checked->menuid .
				"&submitlang=" . $this->content->template['aktulanglong'];
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Diese Methode erstellt Open Graph Meta-Tags für alle geladenen Artikel und gibt diese ans Template weiter.
	 * <strong>table_data["open_graph"])</strong><br>
	 * Zusätzlich werden die Tags in der "globalen" (nicht Artikel-spezifischen) Templatevariable
	 * <strong>$openGraphMetaTags</strong> gespeichert, sofern nur ein Artikel vorhanden ist.
	 *
	 * @date 2018-02-14
	 * @return boolean|array|void
	 */
	private function makeOpenGraphMetaTags()
	{
		$articles = &$this->content->template["table_data"];
		$languageID = (int)$this->cms->lang_id;

		if (is_array($articles) == false || count($articles) == 0) {
			return false;
		}

		/* Alter Code
		$data = $this->db->get_results(
			"SELECT lan_repore_id, header, lan_teaser, lan_article, lan_metatitel, lan_metadescrip, url_header ".
			"FROM {$this->cms->tbname["papoo_language_article"]} ".
			"WHERE lan_repore_id IN (".implode(", ", array_map(function ($article) {
				return (int)$article["reporeid"];
			}, $articles)).") AND lang_id = $languageID", ARRAY_A
		);
		*/

		// FIXME: Produziert Fehler (index reporeid existiert nicht)
		$sql = sprintf("SELECT lan_repore_id, header, lan_teaser, lan_article, 
								lan_metatitel, lan_metadescrip, url_header 
								FROM %s WHERE lan_repore_id IN (%s) AND lang_id='%d'",
			$this->cms->tbname["papoo_language_article"],
			implode(", ", array_map(function($article) { return @(int)$article["reporeid"]; }, $articles)),
			$languageID
		);
		$data = $this->db->get_results($sql, ARRAY_A);

		// Aus der Datenbank geholte Daten vorbereiten
		$data = array_reduce($data, function ($data, $row) {
			$data[(int)$row["lan_repore_id"]] = [
				"id" => (int)$row["lan_repore_id"],
				"title" => $row["header"],
				"teaser" => $GLOBALS["diverse"]->do_pfadeanpassen($row["lan_teaser"]),
				"content" => $GLOBALS["diverse"]->do_pfadeanpassen($row["lan_article"]),
				"meta_title" => $row["lan_metatitel"],
				"meta_description" => $row["lan_metadescrip"],
				"slug" => $row["url_header"],
			];
			return $data;
		}, []);

		// FIXME: Produziert Fehler (index reporeid existiert nicht)
		$articles = array_map(function ($article) use ($data) {
			@$articleData = &$data[(int)$article["reporeid"]];
			$protocol = empty($_SERVER['HTTPS']) == false &&
				$_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
			$baseUrl = $protocol.$_SERVER["HTTP_HOST"].rtrim(PAPOO_WEB_PFAD, "/")."/";

			// Hole das erste Bild aus dem Teaser bzw Content raus, das gefunden wird
			$image = null;
			foreach (["teaser", "content"] as $column) {
				if (preg_match('~<img[^>]+(?<= )src="(?<image>(?<protocol>(?:https?://)?)[^"]+)~',
					$articleData[$column], $match)) {
					$image = strlen($match["protocol"]) > 0 ? $match["image"] : $protocol.$_SERVER["HTTP_HOST"].$match["image"];
					break;
				}
			}

			// TODO backwards compatibility mod_rewrite
			$url = $this->cms->mod_free == 1
				? $baseUrl.ltrim($articleData["slug"], "/")
				: $baseUrl."index.php?menuid=".(int)$this->checked->menuid."&reporeid=".(int)$this->checked->reporeid;

			// Werte fuer Open Graph Meta Tags erstellen
			return array_merge($article, ["open_graph" => [
				"title" => trim(strip_tags($articleData["title"])),
				"description" => trim(strip_tags($articleData["meta_description"])),
				"type" => "Website",
				"image" => trim($image),
				"url" => trim($url),
			]]);
		}, $articles);

		if (count($articles) == 1) {
			$this->content->template["openGraphMetaTags"] = reset($articles)["open_graph"];
		}
	}
}

$artikel = new artikel_class();
