<?php
/**
#####################################
# CMS Papoo                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.2                  #
#####################################
 */

/**
 * Diese Klasse soll in einer Variablen mit HTML alle Stichwörter
 * die aus einer Datenbank ausgelesen werden ersetzen mit:
 * a) Stichwörter zu Links 			<a href=""></a>
 * b) Stichwörter zu Abkürzungen 	<acronym title=""></acronym>
 * c) language Attribute setzen 	<span lang="xx" xml:lang="xx"></span>
 *
 * Class replace_class
 */
class replace_class {
	/** @var array enthält die Wörter der "fremdländischen" Sprache */
	var $lexikon;
	/** @var array enthält Abkürzungen die ausgezeichnet werden */
	var $abkuerzungen;
	/** @var array enthält Links die ersetzt werden */
	var $links;
	/** @var array enthält Links die nicht mehr ausgezeichnet werden sollen. */
	var $links_ausschluss;

	/** @var string Enthält "Sonderzeichen" wie z.B. " oder ( oder . etc. die VOR dem reinen Wort stehen */
	var $wortumgebung_anfang;
	/** @var string Enthält "Sonderzeichen" wie z.B. " oder ) oder . etc. die NACH dem reinen Wort stehen */
	var $wortumgebung_ende;

	/**
	 * replace_class constructor.
	 */
	function __construct()
	{
		$this->lexikon = array ();
		$this->abkuerzungen = array ();
		$this->links = array ();
		$this->links_ausschluss = array ();

		$this->wortumgebung_anfang = "";
		$this->wortumgebung_ende = "";

		// cms Klasse einbinden
		global $cms;
		$this->cms = & $cms;

		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = & $db;

		// messages einbinden
		global $message;
		$this->message = & $message;

		//auswärtige Variablen einbindden
		global $checked;
		$this->checked = & $checked;

		global $content;
		$this->content =&$content;
	}

	/**
	 * Alle Replacement Funktionen durchführen für Webseiten
	 * Beispiel: $sample=$replace->do_replace($sample);
	 *
	 * @param $artikel
	 * @return string
	 */
	function do_replace($artikel)
	{
		//Fehlermeldungen abschalten
		if ($this->cms->do_replace_ok == 1) {
			// Alle Stichwörter zu Links umfunktionieren
			$artikel = $this->name_to_link($artikel);
			// "fremdländische" Wörter entsprechend auszeichnen
			//Da nur Englisch zur Zeit nur für Deutsche Artikel durchführen
			if ($this->cms->lang_id == 1 or empty($this->cms->lang_id)) {
				$artikel = $this->name_to_lang($artikel);
			}
			// Alle Stichwörter als acronym umformatieren
			$artikel = $this->name_to_abk($artikel);

			//Wenn das Glossar Plugin existiert dann ersetzen
			global $glossar;
			if (!empty($glossar)) {
				$artikel = $this->do_glossar($artikel);
			}
		}
		$this->links_ausschluss = array ();

		return $artikel;
	}

	/**
	 * @abstract Glossar Daten übergeben
	 *
	 * @param $artikel
	 * @param string $glossar_pfad
	 * @return string
	 */
	function do_glossar($artikel, $glossar_pfad = "")
	{
		if (!empty ($artikel)) {
			if (empty($glossar_pfad)) {
				global $glossar;
				$glossar_pfad = $glossar->praeferenzen['glosspref_menu_pfad'];
			}
			// Glossar -Daten aus Datenbank laden
			global $db_praefix;

			$sql = sprintf("SELECT * FROM %s WHERE glossar_lang_id='%d'",
				$db_praefix."glossar_daten",
				$this->db->escape($this->cms->lang_back_content_id)
			);

			$result = $this->db->get_results($sql, ARRAY_A);

			$test = array ();
			$drin = array ();
			$i=0;
			global $diverse;
			//Ergebniss durchgehen und die Links erzeugen
			if (is_array($result)) {
				foreach ($result as $glos) {
					$drin['link_name'] = $this->db->escape($glos['glossar_Wort']);
					$drin['link_name_alt'] = $diverse->explode_text_lines($glos['glossar_Wort_alt']);
					//print_r($drin);
					if ($this->cms->mod_free==1) {
						$drin['link_body'] = PAPOO_WEB_PFAD."".$glossar_pfad.$glos['glossar_id'].'-'.urlencode($drin['link_name']).'.html#'.$glos['glossar_id'];
					}
					else {
						if ($this->cms->mod_rewrite==2) {
							$drin['link_body'] = PAPOO_WEB_PFAD."/".$glossar_pfad.$glos['glossar_id'].'-'.urlencode($drin['link_name']).'.html#'.$glos['glossar_id'];
						}
						else {
							$drin['link_body'] = PAPOO_WEB_PFAD.$glossar_pfad.'&glossar='.$glos['glossar_id'].'&template=glossar/templates/wortdefinitionen.html'; //#'.$glos['glossar_id'];
						}
					}
					$drin['link_titel'] = $glos['glossar_Wort'];
					$test[$i] = $drin;
					$i++;
				}
			}
			$this->glossar = "ok";
			$this->links = $test;

			$artikel = $this->name_to_link($artikel, "glossar_link");
			$this->glossar = "";

			//Daten zurückgeben
			return $artikel;
		}
		else {
			//Daten zurückgeben
			return $artikel;
		}
	}

	/**
	 * @return void
	 * @param mixed $artikel
	 * @desc Ale Ersetungen aber keine Links, gut für Menü etc.
	 */
	function do_replace_nolink($artikel)
	{
		if ($this->cms->do_replace_ok==1) {
			// "fremdländische" Wörter entsprechend auszeichnen
			$artikel = $this->name_to_lang($artikel);
			// Abküzungen als acronym auszeichnen
			$artikel = $this->name_to_abk($artikel);
			// Inhalt zurückgeben
		}
		return $artikel;
	}

	/**
	 * Alle Replacementfunktionen durchführen für einen Ausdruck
	 * entsprechend sollen Links als Liste unten aufgeführt werden
	 * ebenso wie Abkürzungen und Acronyme
	 *
	 * @param $artikel
	 * @return string
	 */
	function do_print($artikel)
	{

		$artar=explode('<div class="print">',$artikel);

		if ($this->cms->do_replace_ok==1) {
			# Alle Links raussuchen und auflisten
			$artikel1 = $this->link_to_print($artar['1']);
			# Alle Abkürzungen und Acronyme raussuchen und auflisten
			$artikel1 = $this->abk_to_print($artikel1);
		}
		else {
			$artikel1 =($artar['1']);
		}
		$artikel=$artar['0'].$artikel1;
		# Inhalt zurückgeben
		return $artikel;
	}

	/**
	 * Links zur Druckerausgabe am Ende ausgeben
	 *
	 * @param $block
	 * @return string|string[]|null
	 */
	function link_to_print($block)
	{
		# FIXME titel auch mit umschreiben!
		$matches = "";

		#global $db_link_members;

		if ($block == "") {
			return $block;
		}

		$links_build = "";
		$title=$this->cms->title_send;
		//target=blank rausfischen
		//$block = str_ireplace("target=\"blank\"", "", $block);
		//$block = str_ireplace("target=\"_blank\"", "", $block);
		$ersetz = '"http://'.$title.'/index.php';
		$ersetz3 = '"http://'.$title.'/index/';
		$ersetz2 = '"http://'.$title.'/plugin.php';
		$ersetz4 = '"http://'.$title.'/plugin/';
		$ersetz_v = '"http://'.$title.'/visilex.php';
		$ersetz5 = 'id="';
		$block = preg_replace('/ target="(.*?)"/', "", $block);
		$block = preg_replace('/ title="(.*?)"/', "", $block);

		$block = preg_replace('/ class="(.*?)"/', "", $block);
		$block = preg_replace('/rel="lightbox"/', "", $block);
		$block = preg_replace('/style="(.*?)"/', "", $block);
		$block = preg_replace('/target="_blank"/', "", $block);
		$block = preg_replace('/"..\/index.php/', $ersetz, $block);
		$block = preg_replace('/".\/index.php/', $ersetz, $block);
		$block = preg_replace('/"index.php/', $ersetz, $block);
		$block = preg_replace('/"\/visilex.php/', $ersetz_v, $block);
		$block = preg_replace('/"\/index\//', $ersetz3, $block);
		$block = preg_replace('/"\/plugin\//', $ersetz4, $block);
		$block = preg_replace('/"\/plugin.php/', $ersetz2, $block);
		$block = preg_replace('/href="..\/images/', $ersetz5, $block);
		$block = preg_replace('/"   >/', "\">", $block);
		$block = preg_replace('/"  >/', "\">", $block);
		$block = preg_replace('/" >/', "\">", $block);
		$block = preg_replace('/\/">/', "\">", $block);

		$block = preg_replace('/href="\//', "href=\"http://".$this->cms->title."/", $block);
		//src="www

		//Um Anker Links fischen zu können
		$block = preg_replace('/"#.*?"/', '\"#"', $block);
		#echo $block;

		#$block = preg_replace("/class=\".*?\"/",$ersetz,$block);
		$urls = '(http|telnet|gopher|file|wais|ftp)';
		$ltrs = '\w'; // any alphanumeric char, or underscore
		$gunk = '/#~:.?+=&%@!\-';

		// added comma!!! [e.g., http://www.spiegel.de/spiegel/0,1518,druck-289348,00.html]
		$punc = ',.:?\-';

		$any = "$ltrs$gunk$punc";

		preg_match_all("{
									\b          # start at word boundary
									$urls   :   # need resource and a colon
									[$any] +?   # followed by one or more of any valid
									#   characters--but be conservative
									#   and take only what you need
									(?=         # the match ends at
									[$punc] * # punctuation
									[^$any]   # followed by a non-URL character
									|           # or
									$         # the end of the string
									)
									}x", $block, $matches);
		//$block =preg_replace("/\r|\n/s", "",$block);
		$block = preg_replace('/ title=".*?"/', "", $block);
		//$block = preg_replace("/ class=\".*?\">/", ">", $block);
		foreach ($matches[0] as $url) {
			$url = str_replace("http://", "", $url);
			$rpos = strrpos($url, "/");
			$len = strlen($url);
			//cut trailing slashes if any
			if (($rpos > 6) && ($rpos +1 == $len)) {
				$url = substr($url, 0, $rpos);

				//update url in block
				$block = str_replace($url."/", $url, $block);
			}

			//temp url with separator
			$build_url = $url."�";

			if ((!strstr($links_build, $build_url))) {
				//add real url with separator
				$links_build .= $url."�";
			}
		}

		//fill links array
		$links_build = substr($links_build, 0, strlen($links_build) - 1);
		$links_list = explode("�", $links_build);
		//Links in diesem Text:
		$links_found = "<strong>".$this->content->template['message_2146']."</strong>\n";

		//build bottom legend
		$i = 0;
		$links_ok = "";
		foreach ($links_list as $lurl) {
			$i ++;
			if (!empty ($lurl) and $links_ok != 1) {
				$links_ok = 1;
			}
			$links_found .= "<br /><strong>[".$i."]</strong>http://$lurl\n";
		}
		$links_found = "<p style=\"font-size: 0.9em; margin-bottom: 3em;\">$links_found\n</p>";

		$block = str_replace("http://", "", $block);
		$block = preg_replace('/<a href="(.*?)">(.*?)<\/a>/i', "\\2 [\\1]", $block);

		// removal of $title "link"!
		$block = str_replace(" []", "", $block);
		//substitue links with indexes
		$i = 0;

		foreach ($links_list as $url) {
			$i ++;
			$block = str_replace("[".$url."]", "<strong>[".$i."]</strong>", $block);
		}

		if ($links_ok) {
			$block .= "$links_found\n";
		}
		//Links zu Ankern rausfischen
		$block = str_ireplace("\[#\]", "", $block);

		$block = preg_replace('/\[(.*?)www(.*?)\]/', "", $block);

		$block = str_ireplace("http:///", "", $block);
		$block = preg_replace('/src="www/', "src=\"http://www", $block);

		return $block;
	}

	/**
	 * Automatische Link-Ersetzung
	 *
	 * @param string $text
	 * @param string $css_class
	 * @return string
	 */
	function name_to_link($text = "", $css_class="")
	{
		$sprache = "";
		// wenn leer, dann leer zurück und fertig.
		if ($text == "")
			return $text;

		// Das ist der Text der später die Auszeichnungen enthält und zurückgegeben wird
		$return_text = "";

		// Der Original-Text wird gesplitet in Tag-Teil(e) und Text-Teil(e), damit innerhalb von Tags nicht ausgezeichnet wird
		$tag_array = $this->split_text_tags($text);

		// die Abkürzungen laden
		if (empty ($this->glossar)) {
			$this->links_load();
		}

		// Jetzt kommt die eigentliche Auszeichnung, wobei nur Texte ausgezeichnet werden, die noch nicht ausgezeichnet sind.
		$temp_is_link = false;

		foreach ($tag_array as $eintrag) {
			// 1. "Nonsense-Tag" = erster Textteil
			if ($eintrag[1] == "<nonsensetag>") {
				$return_text .= $this->auszeichnung_links($eintrag[2], $sprache);
			}
			else {
				// 2. Einträge die schon ausgezeichnet sind komplett Übernehmen
				if (strpos("xxx".$eintrag[0], "</a>")) {
					$temp_is_link = false;
				}

				if ($temp_is_link) {
					$return_text .= $eintrag[0];
				}
				elseif (strpos("xxx".$eintrag[0], "<a ")) {
					$temp_is_link = true;
					$return_text .= $eintrag[0];
				}

				// außerdem Links nicht in Überschrift etc. auszeichnen
				elseif  (strpos("xxx".$eintrag[0], "<h1") OR strpos("xxx".$eintrag[0], "<h2") OR strpos("xxx".$eintrag[0], "<h3") OR strpos("xxx".$eintrag[0], "<h4") OR strpos("xxx".$eintrag[0], "<h5") OR strpos("xxx".$eintrag[0], "<h6") OR strpos("xxx".$eintrag[0], "<strong")) {
					$return_text .= $eintrag[0];
				}
				// 3. sonst Text auszeichnen
				else {
					$return_text .= $eintrag[1].$this->auszeichnung_links($eintrag[2], $css_class);
				}
			}
		}

		// Zu guter Letzt die Abkürzungen wieder löschen und den ausgezeichneten Text zurückgeben
		$this->links = array ();
		return trim($return_text);
	}

	/**
	 * Diese Funktion lädt alle Links welche ersetzt werden sollen
	 */
	function links_load() {
		$this->links = array (); // evtl. alte Einträge löschen

		$sql = "select link_name, link_body, link_title from ".$this->cms->papoo_links." order by link_name desc";

		$this->links = $this->db->get_results($sql, ARRAY_A);
		// Wenn die Datenbank keine Links enthält, ist $this->links NULL,
		// initialisiere mit leerem Array, damit foreach später nicht scheitert
		// Fix by Martin Wegner, http://mroot.net/
		if (is_null($this->links)) {
			$this->links = array ();
		}
	}

	/**
	 * Diese Funktion ersetzt Worte in einem Tag-freien Text als Links, also z.B. so:
	 * Papoo => <a href="http://www.papoo.de" title="Titel des Links">Papoo</a>
	 *
	 * @param string $text
	 * @param string $css_class
	 * @return string
	 */
	function auszeichnung_links($text = "", $css_class = "")
	{
		IfNotSetNull($text_new);
		if ($text != "") {
			$text_array = explode(" ", $text);

			if (!empty($css_class)) {
				$temp_css_class = 'class="'.$css_class.'"';
			}
			else {
				$temp_css_class = '';
			}

			foreach ($text_array as $wort) {
				$ausgezeichnet = $wort;
				$wort = $this->wortumgebung_save($wort);
				if ($wort != "") {
					foreach ($this->links as $link) {
						if (empty($link['link_name_alt'])) {
							$link['link_name_alt'] = array();
						}

						if (strtolower($wort) == strtolower($link['link_name']) OR in_array(strtolower($wort), $link['link_name_alt']) ) {
							if (!in_array(strtolower($wort), $this->links_ausschluss)) {
								IfNotSetNull($link['link_title']);
								IfNotSetNull($link['link_body']);
								IfNotSetNull($link['link_name_alt']);

								$ausgezeichnet =
									'<a href="' . $link['link_body'] . '" title="' . $link['link_title'] . '" ' . $temp_css_class.' >' .
										$this->wortumgebung_restore($wort) .
									'</a>';
								$this->links_ausschluss[] = strtolower($link['link_name']);
								if(!empty($link['link_name_alt'])) {
									$this->links_ausschluss = array_merge($this->links_ausschluss, $link['link_name_alt']);
								}
							}
						}
					}
				}
				else if (strlen($ausgezeichnet) == 0) {
					$text_new .= " ";
				}
				if (empty($text_new)) {
					$text_new = $ausgezeichnet;
				}
				else {
					$text_new .= " ".$ausgezeichnet;
				}
			}
		}
		return $text_new;
	}

	/**
	 * Auszeichnung der Abkürzungen im Text
	 *
	 * @param string $text
	 * @return string
	 */
	function name_to_abk($text = "")
	{
		// wenn leer, dann leer zurück und fertig.
		if ($text == "") {
			return $text;
		}
		// Das ist der Text der später die Auszeichnungen enthält und zurückgegeben wird
		$return_text = "";
		// Der Original-Text wird gesplitet in Tag-Teil(e) und Text-Teil(e), damit innerhalb von Tags nicht ausgezeichnet wird
		$tag_array = $this->split_text_tags($text);
		// die Abkürzungen laden
		$this->abkuerzungen_load();
		// Jetzt kommt die eigentliche Auszeichnung, wobei nur Texte ausgezeichnet werden, die noch nicht ausgezeichnet sind.
		foreach ($tag_array as $eintrag) {
			// 1. "Nonsense-Tag" = erster Textteil
			if ($eintrag[1] == "<nonsensetag>") {
				$return_text .= $this->auszeichnung_abkuerzungen($eintrag[2]);
			}
			else {
				// 2. Einträge die schon ausgezeichnet sind komplett Übernehmen
				if (strpos("xxx".$eintrag[0], "<acronym")) {
					$return_text .= $eintrag[0];
				}
				// 3. sonst Text auszeichnen
				else {
					$return_text .= $eintrag[1].$this->auszeichnung_abkuerzungen($eintrag[2]);
				}
			}
		}
		// Zu guter Letzt die Abkürzungen wieder löschen und den ausgezeichneten Text zurückgeben
		$this->abkuerzungen = array ();
		return trim($return_text);
	}

	/**
	 * Diese Funktion lädt alle Abkürzungen welche ausgezeichnet werden sollen
	 */
	function abkuerzungen_load() {
		$this->abkuerzungen = array (); // evtl. alte Einträge löschen

		$sql = "select abk_abk, abk_meaning  from ".$this->cms->papoo_abk." order by abk_abk asc ";

		$this->abkuerzungen = $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * mehrfaches explode  um auch \n etc zu trennen
	 *
	 * @param $text
	 * @return array
	 */
	function do_explode($text)
	{
		return explode(" ", $text);
	}

	/**
	 * Diese Funktion zeichnet Worte in einem Tag-freien Text als Abkürzungen (Acronyme) aus, also z.B. so:
	 * <acronym class="acronym" title="zur Zeit">z.Z.</acronym>
	 *
	 * @param string $text
	 * @return mixed|string
	 */
	function auszeichnung_abkuerzungen($text = "")
	{
		IfNotSetNull($text_new);
		if ($text != "") {
			$text_array = $this->do_explode($text);

			foreach ($text_array as $wort) {
				$ausgezeichnet = $wort;
				$wort = $this->wortumgebung_save($wort, "kein_Punkt");
				if ($wort != "") {
					if (!empty($this->abkuerzungen)) {
						foreach ($this->abkuerzungen as $abkuerzung) {
							if ($wort == trim(preg_quote($abkuerzung['abk_abk']))) $ausgezeichnet =
								'<acronym class="acronym" title="'.$abkuerzung['abk_meaning'].'">'.$this->wortumgebung_restore($wort).'</acronym>';

							if (stristr( $wort,preg_quote($abkuerzung['abk_abk']))) {
								if ($wort == ($abkuerzung['abk_abk'])) {
									$ausgezeichnet = '<acronym class="acronym" title="'.$abkuerzung['abk_meaning'].'">'.$this->wortumgebung_restore($wort).'</acronym>';
								}
								else {
									$ausgezeichnet = $this->wortumgebung_restore(str_replace(preg_quote($abkuerzung['abk_abk']), '<acronym class="acronym" title="'.$abkuerzung['abk_meaning'].'">'.($abkuerzung['abk_abk']).'</acronym>', $wort));
								}
								#$ausgezeichnet.="xx".preg_quote($abkuerzung['abk_abk']);
							}
						}
					}
				}
				else if (strlen($ausgezeichnet) == 0) {
					$text_new .= " ";
				}
				if (empty($text_new)) {
					$text_new = $ausgezeichnet;
				}
				else {
					$text_new .= " ".$ausgezeichnet;
				}
			}
		}
		return $text_new;
	}

	/**
	 * Abkürzungen aus dem Text herausnehmen und als Glossar an Ausdruck anfügen
	 * Der Artikel wird dabei in ein Array eingelesen und
	 * die Einträge darin mit einem Array aus Einträgen aus der Datenbank verglichen
	 * die Ersetzung erfolgt nur für gefundene Einträge
	 *
	 * @param $block
	 * @return mixed|string|string[]|null
	 */
	function abk_to_print($block)
	{
		# wenn leer, dann leer zurück
		if ($block == "") {
			return $block;
		}

		# Daten aus der Datenbank auslesen
		$sql = "SELECT abk_abk, abk_meaning FROM ".$this->cms->papoo_abk." ORDER BY abk_abk ASC  ";
		$lqy = $this->db->get_results($sql);

		# Variablen zuweisen
		$linklist = array ();
		$key = array ();
		if (!empty ($lqy)) {
			#Such array mit Daten füllen
			foreach ($lqy as $row) {
				$test[] = $link_name = trim($row->abk_abk);
			}

			#Text aufarbeiten nach jedem end tag ein Leerzeichen
			$neublock = preg_replace("[>]", "> ", $block);
			# Html tags entfernen
			//$neublock = strip_tags($neublock);

			#aus Text Array machen
			$neublock = preg_split("[ ]", $neublock);

			foreach ($neublock as $arrayblock) {
				#Eintrag aufarbeiten
				$arrayblock = trim($arrayblock);
				$arrayblock = str_ireplace("[\n|\r|\t| |]", "", $arrayblock);
				# Wenn leer Überspringen
				if (empty ($arrayblock))
					continue;
				# KOntrolle ob Eintrag in der Datenbank ist
				if (!empty ($test)) {
					if (in_array($arrayblock, $test)) {
						# wenn ja, dann Schlüssel speichern
						$key[] = array_search($arrayblock, $test);
					}
				}
			}
			# für jeden gefundenen Schlüssel Daten für Ersetzungsarray finden
			foreach ($key as $key_arr) {
				$link_name = trim($lqy[$key_arr]->abk_abk);
				$link_meaning = trim($lqy[$key_arr]->abk_meaning);
				$link_meaning = str_ireplace(" ", "___", $link_meaning);
				$link_meaning = str_ireplace("\(", "___", $link_meaning);
				$link_meaning = str_ireplace("\)", "___", $link_meaning);
				$linklist[] = array ('text' => $link_name, 'meaning' => $link_meaning);
			}
			//fill links array
			IfNotSetNull($links_found);
			if (!empty($linklist)) {
				//Abkürzungen in diesem Text
				$links_found = "<br /><strong>".$this->content->template['message_2147'].":</strong>\n";
			}
			//build bottom legend
			$i = 0;
			foreach ($linklist as $lurl) {
				$i ++;
				$links_found .= "<br /><strong>(".$i.")</strong> ".$lurl['text']." => ".$lurl['meaning']."\n";
			}
			$links_found = "<p style=\"font-size: 0.9em; margin-bottom: 3em;\">$links_found\r\n</p>";

			//substitue links with indexes
			$i = 0;
			foreach ($linklist as $url) {
				$i ++;
				$block = str_replace($url['text'], $url['text']."<strong>(".$i.")</strong>", $block);
			}

			if ($links_found) {
				$block .= "$links_found\n";
			}

			return str_ireplace("___", " ", $block);
		}
		//Kein Eintrag in der Datenbank
		else {
			# Rückgabe der unveränderten Werte
			return $block;
		}
	}

	/**
	 * Sprachauszeichnung für "fremdländische" Wörter
	 *
	 * @param string $text
	 * @param string $sprache
	 * @return string
	 */
	function name_to_lang($text = "", $sprache = "en")
	{
		// wenn leer, dann leer zurück und fertig.
		if ($text == "") {
			return $text;
		}

		// Das ist der Text der später die Auszeichnungen enthält und zurückgegeben wird
		$return_text = "";

		// das entsprechende Lexikon laden
		$this->lexikon_load($sprache);

		if (empty($this->lexikon)) {
			$return_text = $text;
		}
		else {
			// Der Original-Text wird gesplitet in Tag-Teil(e) und Text-Teil(e), damit innerhalb von Tags nicht ausgezeichnet wird
			$tag_array = $this->split_text_tags($text);

			// Jetzt kommt die eigentliche Auszeichnung, wobei nur Texte ausgezeichnet werden, die noch nicht ausgezeichnet sind.
			foreach ($tag_array as $eintrag) {
				// 1. "Nonsense-Tag" = erster Textteil
				if ($eintrag[1] == "<nonsensetag>") {
					$return_text .= $this->auszeichnung_englisch($eintrag[2], $sprache);
				}
				else {
					// 2. Einträge die schon ausgezeichnet sind komplett Übernehmen
					if (strpos($eintrag[0], "lang=")) {
						$return_text .= $eintrag[0];
					}

					// 3. sonst Text auszeichnen
					else {
						$return_text .= $eintrag[1].$this->auszeichnung_englisch($eintrag[2], $sprache);
					}
				}
			}
			// Zu guter Letzt das Lexikon wieder löschen und den ausgezeichneten Text zurückgeben
			$this->lexikon = array ();
		}
		return trim($return_text);
	}

	/**
	 * Diese Funktion lädt die "fremdländischen" Worte ins Lexikon
	 *
	 * @param string $sprache
	 */
	function lexikon_load($sprache = "en")
	{
		$this->lexikon = array (); // evtl. alte Einträge löschen

		switch ($sprache) {
		case "en" :
			$sql = "select lang_word  from ".$this->cms->papoo_lang_en." order by lang_word asc ";
			break;
		}
		$this->lexikon = $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Diese Funktion zeichnet Worte in einem Tag-freien Text als "fremsprachiges" Wort aus, also z.B. so:
	 * <span class="lang_eng" lang="en" xml:lang="en">englisches Wort</span>
	 *
	 * @param string $text
	 * @param string $sprache
	 * @return string
	 */
	function auszeichnung_englisch($text = "", $sprache = "en")
	{
		IfNotSetNull($text_new);
		if ($text != "") {
			$text_array = explode(" ", $text);

			foreach ($text_array as $wort) {
				$ausgezeichnet = $wort;
				$wort = $this->wortumgebung_save($wort);
				if ($wort != "") {
					foreach ($this->lexikon as $wort_lang) {
						if (trim(strtolower($wort)) == trim(strtolower($wort_lang['lang_word'])))
							$ausgezeichnet = '<span class="lang_'.$sprache.'" lang="'.$sprache.'" xml:lang="'.$sprache.'">'.$this->wortumgebung_restore($wort).'</span>';
					}
				}
				else if (strlen($ausgezeichnet) == 0) {
					$text_new .= " ";
				}
				if (empty($text_new)) {
					$text_new = $ausgezeichnet;
				}
				else {
					$text_new .= " ".$ausgezeichnet;
				}
			}
		}
		return $text_new;
	}

	/**
	 * Speichert die "Wort-Umgebung" und gibt das Wort OHNE Umgebung zurück.
	 *
	 * @param $wort
	 * @param string $modus
	 * @return mixed
	 */
	function wortumgebung_save($wort, $modus = "")
	{
		$result_array = array ();

		switch ($modus) {
		case "kein_Punkt" :
			$match_expression = '/([("\'\[]{0,})(.*[^)"\'\],;!])(.*)/i';
			break;
		default :
			$match_expression = '/([("\'\[]{0,})(.*[^)"\'\]\.,;!])(.*)/i';
		}

		preg_match($match_expression, $wort, $result_array);

		IfNotSetNull($result_array[1]);
		IfNotSetNull($result_array[2]);
		IfNotSetNull($result_array[3]);

		$this->wortumgebung_anfang = $result_array[1];
		$wort_clean = $result_array[2];
		$this->wortumgebung_ende = $result_array[3];

		return $wort_clean;
	}

	/**
	 * Baut die ursprüngliche "Wort-Umgebung" wieder um das Wort und gibt dieses dann zurück
	 *
	 * @param $wort
	 * @return string
	 */
	function wortumgebung_restore($wort) {
		$wort = trim($this->wortumgebung_anfang.$wort.$this->wortumgebung_ende);
		return $wort;
	}

	/**
	 * Diese Funktion splittet einen Text in Tag-Elemente und den Text dazwischen
	 *
	 * @param string $text
	 * @return array
	 */
	function split_text_tags($text = "") {
		// muß gemacht werden damit Suchmuster korrekt arbeitet
		$text = "<nonsensetag>".$text;

		// dieses Array wird von preg_match_all gefüllt.
		$text_array = array ();

		// Suchmuster: (<text_in_klammern>)(text_nach_klammern),
		// daher auch das nonsense-Tag um Texte am Anfang, also vor einem Tag zu erfassen
		$match_expression = "/(<[^>]{1,}>)([^<]{0,})/i";
		// füllt $text_array in der Art:
		// $text_array[0] = Text des gesamten Suchmusters, also Tag + Text danach bis zum nächsten Tag
		// $text_array[1] = das Tag (also der Text in Klammern incl. der Klammern)
		// $text_array[2] = Text nach Tag bis zum nächsten Tag
		preg_match_all($match_expression, $text, $text_array, PREG_SET_ORDER);

		return $text_array;
	}
}

$replace = new replace_class();