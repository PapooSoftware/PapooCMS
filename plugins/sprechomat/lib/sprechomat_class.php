<?php
/*
#####################################
#                                   #
# Papoo CMS                         #
# (c) Carsten Euwens                #
# http://www.papoo.de               #
#                                   #
#####################################
#                                   #
# Plugin:  sprechomat               #
# Autor:   Stephan Bergmann         #
#          aka b.legt               #
# http://www.sprechomat.de          #
#                                   #
#####################################
*/

/**
 * Class sprechomat_class
 */
class sprechomat_class
{
	/** @var array */
	var $einstellungen = array();
	/** @var array */
	var $artikelausschluss = array();

	/**
	 * sprechomat_class constructor.
	 */
	function __construct()
	{
		//globale papoo-Klassen einbinden
		global $cms, $diverse, $db, $checked, $content, $menu, $sitemap;
		$this->cms = & $cms;
		$this->diverse= & $diverse;
		$this->db = & $db;
		$this->checked = & $checked;
		$this->content = & $content;
		$this->menu = & $menu;
		$this->sitemap = & $sitemap;

		// Sprechomat-eigene Attribute
		$this->text = "";
		$this->frontend_template = "sprechomat/templates/sprechomat_frontend.html";
		$this->text_template = "sprechomat/templates/sprechomat_text.html";

		$this->make_sprechomat();
	}

	// ALLGEMEINE FUNKTIONEN
	// *********************
	/**
	 * Weiche f�r weitere Aktionen
	 */
	function make_sprechomat()
	{
		global $template;

		if (!defined("admin")) {
			// Frontend
			$this->data_load(); // Sprechomat-Daten laden
			if (strpos("XXX".$template, "sprechomat_frontend.html")) {
				// Sprechomat-Ausgabefenster
				$this->diverse->no_output="no";
				if ($this->select_textart($this->checked)) {
					if ($this->einstellungen['sprechomat_devloginid']) {
						$this->make_text(); // Text f�r �bergabe erzeugen
						$this->content->template['sprechomat_text'] = utf8_encode($this->text);
					}
					else {
						$this->make_texturl(); // URL f�r �bergabe an Sprechomat.de erzeugen
					}
				}
			}
			if (strpos("XXX".$template, "sprechomat_text.html")) {
				// Sprechomat-Ausgabefenster
				$this->diverse->no_output="no";
				// Sprechomat-Textfenster
				if ($this->select_textart($this->checked)) {
					$this->make_text(); // Text f�r Ausgabefenster erzeugen, dann Text ausgeben.
					header('Content-Type: text/html; charset=iso-8859-1');
					echo $this->text;
					exit;
				}
			}
		}
		else {
			// Backend Daten
			if (strpos("XXX".$template, "sprechomat_backdata.html")) {
				$this->data_handle($this->checked);
			}
			// Backend Einstellungen
			if (strpos("XXX".$template, "sprechomat_backset.html")) {
				$this->data_handle($this->checked);
			}
			// Backend Artikel-Ausschluss
			if (strpos("XXX".$template, "sprechomat_backausschluss.html")) {
				$this->loesche_artikelnichtexistent();

				$this->data_handle($this->checked);
				$this->menu->data_front_complete = $this->menu->menu_data_read("FRONT", 1); // Sprach-ID "1", da es Sprechomat z.Z. nur auf deutsch gibt.
				$this->sitemap->make_sitemap(1); // Sprach-ID "1", da es Sprechomat z.Z. nur auf deutsch gibt.

				$this->get_artikelausschluss();
				$this->content->template['sprechomat_artikelausschluss'] = $this->artikelausschluss;
			}
			$this->data_load(); // Sprechomat-Daten laden
		}
	}

	/**
	 * L�dt die Sprechomat-Daten aus der Tabelle papoo_sprechomat
	 */
	function data_load()
	{
		global $db_praefix;

		$sql = sprintf("SELECT * FROM %s LIMIT 1", $db_praefix."sprechomat");
		$result = $this->db->get_results($sql, ARRAY_A);

		if (!empty($result)) {
			$this->einstellungen = $result[0];
			$this->content->template['sprechomat_einstellungen'] = $this->einstellungen;
		}
	}

	// FRONTEND FUNKTIONEN
	// *******************
	/**
	 * Erzeugt die Links zum Ausgabfenster im Frontend. Wird als "post_papoo"-Funktion aufgerufen.
	 */
	function post_papoo()
	{
		// Sprechomat-Links nur erzeugen, wenn Sprechomat aktiv ist
		if ($this->einstellungen['sprechomat_aktiv_yn']) {
			// Sprechomat-Links nur im Frontend erzeugen
			if (!defined("admin")) {
				global $template;

				// Sprechomat-Links nur f�r deutsche Seiten erzeugen
				//if ($this->cms->lang_frontend == "de") // geht leider nicht :-((
				if ($this->cms->lang_id == 1) {
					//print_r($this->content->template);

					// Sprachlink f�r Artikel erstellen
					if (!empty($this->content->template['table_data']) && $template=="index.html") {
						$new_table_data = array();
						$this->get_artikelausschluss();

						for ($i = 0; $i < count(@ $this->content->template['table_data']); $i++ ) { // geht leider nicht mit foreach, da im ersten Element Bl�dsinn steht
							$temp_array = $this->content->template['table_data'][$i];

							// Start-Text 1. Seite hat keine �berschrift und keine ReporeID, also definieren
							if (!is_array($temp_array)) {
								$temp_array = array();
							}
							$keys = array_keys($temp_array);
							if (!in_array("uberschrift", $keys)) {
								$temp_array['uberschrift'] = "";
							}
							if (!in_array("reporeid", $keys)) {
								$temp_array['reporeid'] = false;
							}

							if ((strlen(trim($temp_array['uberschrift'].$temp_array['text'])) > 1)) {
								if ($temp_array['reporeid']) {
									$the_reporeid = $temp_array['reporeid'];
								}
								else {
									$the_reporeid = $this->checked->reporeid;
								}

								if (!$the_reporeid) { // Startseiten-Text
									if ($template !="inhalt.html" && $this->einstellungen['sprechomat_aktiv_starttext_yn']) {
										$sprachlink = $this->make_link("starttext", 1);
										// Sonderbehandlung f�r Startseite: Sprechomat-Link in Text einbauen.
										$temp_array['text'] = "<span id=\"sprechomat_start\">".$sprachlink."</span>".$temp_array['text'];
									}
								}
								// Normaler Artikel
								elseif ($this->einstellungen['sprechomat_aktiv_artikel_yn']
									&& !in_array($the_reporeid, $this->artikelausschluss)) {
									$temp_array['uberschrift'] .= " ";
									$sprachlink = $this->make_link("reporeid", $the_reporeid);
									$temp_array['sprechomatlink'] = $sprachlink;
								}
								$new_table_data[] = $temp_array;
							}
						}
						// Template-Variablen ersetzen
						if (!empty($new_table_data)) {
							$this->content->template['table_data'] = $new_table_data;
						}
					}
					// Sprachlink f�r Kommentare erstellen
					if (!empty($this->content->template['comment_data']) && $template=="index.html" && $this->einstellungen['sprechomat_aktiv_kommentare_yn']) {
						$temp_array = array();
						foreach($this->content->template['comment_data'] as $kommentar) {
							if (strlen(trim($kommentar['thema'].$kommentar['text']))) { // nur nichtleere Kommentare ber�cksichtigen
								$sprachlink = $this->make_link("messageid", $kommentar['msgid']);
								$kommentar['sprechomatlink'] = $sprachlink;
								$temp_array[] = $kommentar;
							}
						}
						// Template-Variablen ersetzen
						if (!empty($temp_array)) {
							$this->content->template['comment_data'] = $temp_array;
						}
					}

					// Sprachlink f�r G�stebuch erstellen
					if (!empty($this->content->template['comment_data']) && $template=="guestbook.html" && $this->einstellungen['sprechomat_aktiv_gaestebuch_yn']) {
						$temp_array = array();
						foreach($this->content->template['comment_data'] as $kommentar) {
							//print_r($kommentar);
							if (strlen(trim($kommentar['thema'].$kommentar['text']))) { // nur nichtleere Eintr�ge ber�cksichtigen
								$sprachlink = $this->make_link("messageid", $kommentar['msgid']);
								$kommentar['sprechomatlink'] = $sprachlink;
								$temp_array[] = $kommentar;
							}
						}
						// Template-Variablen ersetzen
						if (!empty($temp_array)) {
							$this->content->template['comment_data'] = $temp_array;
						}
					}

					// Sprachlink f�r Forum erstellen
					if (!empty($this->content->template['message_data']) && $this->einstellungen['sprechomat_aktiv_forum_yn']) {
						$temp_array = array();
						foreach($this->content->template['message_data'] as $message) {
							if (strlen(trim($message['thema'].$message['text']))) { // nur nichtleere Messages ber�cksichtigen
								$sprachlink = $this->make_link("messageid", $message['msgid']);
								$message['sprechomatlink'] = $sprachlink;
								$temp_array[] = $message;
							}
						}
						// Template-Variablen ersetzen
						if (!empty($temp_array)) {
							$this->content->template['message_data'] = $temp_array;
						}
					}

					// Sprachlink f�r Forum in Boardansicht erstellen
					if (!empty($this->content->template['messagefor_data']) && $this->einstellungen['sprechomat_aktiv_forum_yn']) {
						$temp_array = array();
						foreach($this->content->template['messagefor_data'] as $message) {
							if (strlen(trim($message['thema'].$message['text']))) { // nur nichtleere Messages ber�cksichtigen
								$sprachlink = $this->make_link("messageid", $message['msgid']);
								$message['sprechomatlink'] = $sprachlink;
								$temp_array[] = $message;
							}
						}
						// Template-Variablen ersetzen
						if (!empty($temp_array)) {
							$this->content->template['messagefor_data'] = $temp_array;
						}
					}
				}
			}
		}
	}

	/**
	 * Erzeugt den Link zum �ffnen des Sprachausgabe-Fensters
	 *
	 * @param string $modus
	 * @param int $id
	 * @return string
	 */
	function make_link($modus = "", $id = 0)
	{
		$temp_link = sprintf("%splugin.php?%s=%d&amp;template=%s",
			$this->content->template['slash'],
			$modus,
			$id,
			$this->frontend_template
		);

		$sprachlink = sprintf(' <a href="%s" onclick="window.open(\'%s\', \'%s\', \'width=420, height=420, location=yes, menubar=yes\')" title="%s" target="%s" class="%s"><img src="%s" title="%s" alt="%s" width="25" height="22" /></a>',
			$temp_link,
			$temp_link,
			"sprechomat",
			$this->content->template['plugin']['sprechomat']['text_alt_title'],
			"sprechomat",
			"sprachlink",
			$this->content->template['slash']."plugins/sprechomat/bilder/sprechomat.gif",
			$this->content->template['plugin']['sprechomat']['text_alt_title'],
			$this->content->template['plugin']['sprechomat']['text_alt_title']
		);
		return $sprachlink;
	}

	/**
	 * Liest die Artikel-IDs der auszuschliessenden Artikel in "$this->artikelausschluss" ein
	 *
	 * @param int $sprach_id
	 */
	function get_artikelausschluss($sprach_id = 1)
	{
		global $db_praefix;

		// Daten aus der Datenbank fischen
		$query = sprintf("SELECT sprechomat_artikelausschluss_id FROM %s WHERE sprechomat_artikelausschluss_lang_id='%d'",
			$db_praefix."sprechomat_artikelausschluss",
			$sprach_id
		);
		// Daten in ein Array weisen
		$this->artikelausschluss = $this->db->get_col($query);

		// Falls kein Artikel ausgeschlossen ist, array "leer" setzen
		if (empty($this->artikelausschluss)) {
			$this->artikelausschluss = array();
		}
	}

	/**
	 * Legt die Text-Art fest, sprich entscheidet was f�r ein Text geholt werden soll.
	 * M�glichen Text-Arten: normaler Artikel-Text, Forums-Beitrag (Kommentare, G�stebucheintr�ge), Starttext erste Seite.
	 *
	 * @param int $art_wert_array
	 * @return bool
	 */
	function select_textart($art_wert_array = 0)
	{
		$allesOK = false;
		if($art_wert_array) {
			// Text-Art und Text-ID setzen
			if ($this->checked->reporeid) {
				$this->text_art = "reporeid";
				$this->text_id = $this->checked->reporeid;
				$allesOK = true;
			}

			if ($this->checked->messageid) {
				$this->text_art = "messageid";
				$this->text_id = $this->checked->messageid;
				$allesOK = true;
			}

			if ($this->checked->starttext) {
				$this->text_art = "starttext";
				$this->text_id = 1;
				$allesOK = true;
			}
		}
		return $allesOK;
	}

	/**
	 * erzeugt die URL von der sich Sprechomat.de den Text herunter laden kann.
	 */
	function make_texturl()
	{
		$url = sprintf('plugin.php?%s=%d&template=%s',
			$this->text_art,
			$this->text_id,
			$this->text_template
		);

		// Check ob Webverzeichnis etc. schon ber�cksichtig ist
		if (strpos("XXX".$this->content->template['slash'], "/") == 3) {
			$slash = $this->content->template['slash'];
		}
		else {
			$slash = $this->cms->webverzeichnis."/";
		}

		$this->content->template['texturl'] = "nodecode:".$this->einstellungen['sprechomat_lokalurl'].$slash.$url;
	}

	/**
	 * Text f�r Sprachausgabe erzeugen, ausgeben und Ende.
	 */
	function make_text()
	{
		// Text laden und s�ubern
		$this->get_text();
		$this->clean_text();

		//header('Content-Type: text/html; charset=iso-8859-1');
		//$this->content->template['sprechomat_text'] = utf8_encode($this->text);
	}

	/**
	 * Hilfsfunktion f�r make_text(): L�d den Text aus der entsprechenden Tabelle
	 */
	function get_text()
	{
		switch ($this->text_art) {
		case "reporeid":
			// holt den Text von normalen Artikeln
		{
			// Daten aus der Datenbank fischen
			$query = "SELECT * FROM ".$this->cms->papoo_language_article." WHERE lan_repore_id='" . $this->text_id . "' AND lang_id='".$this->cms->lang_id."'";
			// Daten in ein Array weisen
			$result = $this->db->get_results($query);

			//checken ob Teaser vorgelesen werden soll
			$sql=sprintf("SELECT teaser_atyn FROM %s WHERE reporeID='%s'",
				$this->cms->tbname['papoo_repore'],
				$this->text_id
			);
			$teaseryes=$this->db->get_var($sql);

			if ($teaseryes==1) {
				// Array durchgehen
				foreach ($result as $roweins) {
					$this->text .= $roweins->header . ".\n" . $roweins->lan_teaser . ".\n" . $roweins->lan_article;
				}
			}
			else {
				// Array durchgehen
				foreach ($result as $roweins) {
					$this->text .= $roweins->header . ".\n" . $roweins->lan_article;
				}
			}

			break;
		}
		case "messageid":
			// holt den Text von Forums- und G�stebuch-Eintr�gen
		{
			// Daten aus der Datenbank fischen
			$query = "SELECT * FROM ".$this->cms->papoo_message." WHERE msgid='" . $this->text_id . "'";
			// Daten in ein Array weisen
			$result = $this->db->get_results($query);

			// Array durchgehen
			foreach ($result as $roweins) {
				$this->text .= $roweins->thema . ".\n" . $roweins->messagetext;
			}
			break;
		}
		case "starttext":
			// holt den Text der Startseite
		{
			// Daten aus der Datenbank fischen
			$query = "SELECT * FROM ".$this->cms->papoo_language_stamm." WHERE lang_id='".$this->cms->lang_id."'";
			// Daten in ein Array weisen
			$result = $this->db->get_results($query);

			// Array durchgehen
			foreach ($result as $roweins) {
				$this->text .= $roweins->start_text;
			}
			break;
		}
		}
	}

	/**
	 * Hilfsfunktion f�r make_text(): entfernt Tags etc. aus dem Text
	 */
	function clean_text()
	{
		// ISO-Latin-1 Konvertierung
		$this->text = utf8_decode($this->text);

		// "bl�d-Zeichen" entfernen
		$this->text = str_replace(chr(160)," ",$this->text); // &nbsp; in " " �berf�hren:
		$this->text = str_replace(" ? "," ",$this->text);
		$this->text = str_replace("\n"," ",$this->text);
		$this->text = str_replace("\r"," ",$this->text);

		// einige schlie�ende "Block-Tags" durch Punkte ersetzen.
		$temp_block_tags = array(	"</h1>", "</h2>", "</h3>", "</h4>", "</h5>", "</h6>",
			"</ul>", "</ol>", "</li>", "</td>");
		$this->text = str_replace($temp_block_tags ,". ",$this->text);

		// Tag schlie�en mit Leerstelle erweitern, da sonst "Tag entfernenen" nicht sauber arbeitet
		$this->text = str_replace(">","> ",$this->text);

		// alle Tags entfernen
		$this->text = strip_tags($this->text);

		// Anfuehrungszeichen entfernen
		$this->text = str_replace("\"","",$this->text);

		// Mehrfach-(Leer/Satz)-Zeichen entfernen
		$this->text = preg_replace("/[ ]{1,}/", " ", $this->text);

		// 123px in 123 Pixel �berf�hren, da sost Probleme austauschen k�nnen.
		$this->text = preg_replace("/([0-9]{1,})px/", "$1 Pixel", $this->text);

		// Doppelte Punkte aus "Block-Tag"-Ersetzung entfernen
		$this->text = preg_replace("/(\. ){1,}/", ". ", $this->text);
		$this->text = str_replace(" ,", ",", $this->text);
		$this->text = str_replace(" .", ".", $this->text);

		$this->text = str_replace(",.", ",", $this->text);
		$this->text = str_replace("(\.){1,}", ".", $this->text);
		//$this->text = str_replace("..", ".", $this->text);

		$this->text = trim($this->text);
	}



	// BACKEND FUNKTIONEN
	// *******************
	/**
	 * @param string $daten
	 * @param int $sprach_id
	 */
	function data_handle($daten = "", $sprach_id = 1)
	{
		if ($daten) {
			global $db_praefix;

			IfNotSetNull($daten->sprechomat_aktiv_yn);

			// Daten wurden �bertragen -> Speichern
			if (@$daten->sprechomat_submit_data) {
				$sql = sprintf("UPDATE %s SET
								sprechomat_aktiv_yn='%d',
								sprechomat_serverurl='%s',
								sprechomat_devloginid='%s',
								sprechomat_domainid='%s',
								sprechomat_lokalurl='%s'",

					$db_praefix."sprechomat",
					$daten->sprechomat_aktiv_yn,
					$this->db->escape($daten->sprechomat_serverurl),
					$this->db->escape($daten->sprechomat_devloginid),
					$this->db->escape($daten->sprechomat_domainid),
					$this->db->escape($daten->sprechomat_lokalurl)
				);
				$this->db->get_results($sql);

				$this->content->template['sprechomat_backendmessage'] = $this->content->template['plugin']['sprechomat']['text4'];
			}

			// Einstellungen wurden �bertragen -> Speichern
			if (@$daten->sprechomat_submit_set) {
				$sql = sprintf("UPDATE %s SET
								sprechomat_aktiv_starttext_yn='%d',
								sprechomat_aktiv_artikel_yn='%d',
								sprechomat_aktiv_kommentare_yn='%d',
								sprechomat_aktiv_gaestebuch_yn='%d',
								sprechomat_aktiv_forum_yn='%d'",

					$db_praefix."sprechomat",
					$daten->sprechomat_aktiv_starttext_yn,
					$daten->sprechomat_aktiv_artikel_yn,
					$daten->sprechomat_aktiv_kommentare_yn,
					$daten->sprechomat_aktiv_gaestebuch_yn,
					$daten->sprechomat_aktiv_forum_yn
				);
				$this->db->get_results($sql);

				$this->content->template['sprechomat_backendmessage'] = $this->content->template['plugin']['sprechomat']['text5'];
			}

			// Artikel-Ausschluss wurden �bertragen -> Speichern
			if (@$daten->sprechomat_submit_ausschluss) {
				// Alle Ausschl�sse dieser Sprache l�schen
				$sql = sprintf("DELETE FROM %s WHERE sprechomat_artikelausschluss_lang_id='%d'",
					$db_praefix."sprechomat_artikelausschluss",
					$sprach_id
				);
				$this->db->query($sql);

				// Ausschl�sse dieser Sprache eintragen
				if (!empty($daten->sprechomat_artikelausschluss)) {
					foreach ($daten->sprechomat_artikelausschluss AS $artikel_id) {
						$sql = sprintf("INSERT INTO %s SET sprechomat_artikelausschluss_id='%d', sprechomat_artikelausschluss_lang_id='%d'",
							$db_praefix."sprechomat_artikelausschluss",
							$artikel_id,
							$sprach_id
						);
						$this->db->query($sql);
					}
				}

				$this->content->template['sprechomat_backendmessage'] = $this->content->template['plugin']['sprechomat']['text6'];
			}
		}
	}

	/**
	 * L�scht alle Eintr�ge in der Artikel-Asschluss-Tabelle zu denen kein Artikel mehr in der Sprache "$sprach_id" besteht
	 *
	 * @param int $sprach_id
	 */
	function loesche_artikelnichtexistent($sprach_id = 1)
	{
		global $db_praefix;
		$temp_artikel_array = $this->helper_array2text($this->get_artikelexistent($sprach_id));

		if (!empty($temp_artikel_array)) {
			$query = sprintf("DELETE FROM %s WHERE sprechomat_artikelausschluss_id NOT IN (%s) AND sprechomat_artikelausschluss_lang_id='%d'",
				$db_praefix."sprechomat_artikelausschluss",
				$temp_artikel_array,
				$sprach_id
			);
			$this->db->query($query);
		}
	}

	/**
	 * Liest die IDs existierender Artikel der Sprache "$sprach_id" aus und gibt sie als Array zur�ck
	 *
	 * @param int $sprach_id
	 * @return array
	 */
	function get_artikelexistent($sprach_id = 1)
	{
		global $db_praefix;

		// Daten aus der Datenbank fischen
		$query = sprintf("SELECT T1.reporeID FROM %s AS T1, %s AS T2 WHERE T1.reporeID=T2.lan_repore_id AND T2.lang_id='%d'",
			$db_praefix."papoo_repore",
			$db_praefix."papoo_language_article",
			$sprach_id);
		// Daten in ein Array weisen
		$result = $this->db->get_col($query);

		if (!empty($result)) {
			return $result;
		}
		else {
			return array();
		}
	}

	/**
	 * Wandelt ein Array in eine Zeichen-Kette um, wobei die einzelnen Werte in einfachen Anf�hrungszeichen, durch Komma getrennt gelistet werden.
	 * Geht nur mit 1-dimensionalen Arrays !
	 *
	 * @param array $daten
	 * @return string
	 */
	function helper_array2text($daten = array())
	{
		$text = "";

		if (!empty($daten)) {
			foreach ($daten AS $wert) {
				$text .= "'".$wert."', ";
			}
			$text[strlen($text) - 2] = " ";
		}
		return $text;
	}
}

$sprechomat = new sprechomat_class();
