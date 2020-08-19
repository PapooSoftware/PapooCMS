<?php
/**
#####################################
# PAPOO CMS 					    #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.3                  #
#####################################

 */

/**
 * require_once "./artikel_class.php";
 * require_once "./cms_class.php";
 */

/**
 * Weitere Seiten einbinden.
 *
 * Class weiter
 */
class  weiter  {

	//
	/** @var string der weiter Link */
	var $weiter_link;
	/** @var mixed weitere Seiten existieren */
	var $weitere_seiten;
	/** @var mixed Link zurück */
	var $hrefruck;
	/** @var array Array mit allen weiteren Seiten*/
	var $weiter_array;
	/** @var mixed Link weiter */
	var $hrefweiter;
	/** @var string Anzahl */
	var $result_anzahl="";

	/**
	 * weiter constructor.
	 */
	function __construct() {
		/**
		Klassen und Variablen globalisieren
		 */

		// cms Klasse einbinden
		global $cms;
		// checkedblen Klasse einbinden
		global $checked;
		//content Klasse
		global $content;
		global $diverse;
		/**
		und einbinden in die Klasse
		 */

		// Hier die Klassen als Referenzen
		$this->cms		= & $cms;
		$this->checked	= & $checked;
		$this->content	= & $content;
		$this->diverse	= & $diverse;
		//$this->content->template['weiter_array'] = array("aktivhref" => "");
	}

	/**
	 * @param $what
	 */
	function do_weiter($what)
	{
		// template variablen belegen um smartyfehler zu vermeiden
		//$this->content->template['weiter_array'] = array("aktivhref" => "");
		$this->content->template['weiter'] = false;
		$this->content->template['weitere_seiten']="";

		//sprechende url rausholen
		$this->get_surls_dat();

		if (empty($this->checked->search)){
			$this->checked->search="";
		}
		// Seiten Nr. einbinden kommt als $_GET aus der url oder ist leer
		$page = $this->checked->page;
		if (is_numeric($page)) {
			$this->content->template['page']=$page;
		}
		// $search einbinden
		$search = $this->checked->search;

		if(!isset($this->checked->erw_suchbereich)) {
			$this->checked->erw_suchbereich = NULL;
		}

		// cms Klasse einbinden
		if (is_numeric($this->checked->erw_suchbereich)) {
			$search.="&erw_suchbereich=".$this->checked->erw_suchbereich."&find=Finden";
		}

		// maximale Anzahl der Ergebnisse
		$pagesize = $this->cms->pagesize;

		if (empty($this->surl_dat)) {
			$this->surl_dat="startseite/";
		}

		$this->surl_dat=strip_tags($this->surl_dat);
		$this->surl_dat=$this->diverse->encode_quote($this->surl_dat);

		// tatsächliche Anzahl der Ergebnisse
		$result_anzahl = $this->result_anzahl;

		if (strstr($this->weiter_link,"shop.php?")) {
			$this->modlink="no";
		}

		// wenn $page belegt ist oder die Anzahl der Suchergebnisse größer ist als die maximale Ergebnis Anzahl
		if ((isset($page) and $page > 1) or $result_anzahl > $pagesize) {
			// für Template  Eigenschaften zuweisen
			//$this->content->template['weitere_seiten'] = "1";
			$this->content->template['weiter'] = 0;
			$this->content->template['weiter_page'] = $page;
			$weiter_data = array();

			// Wenn es weitere Seiten gibt und >1 ist
			if (isset($page) and $page > 1) {
				// Link zur vorherigen Seite erzeugen
				switch ($what) {
					// Die Suche läuft
				case "search":
					$lt_text_links = "" . $this->weiter_link . "&amp;page=" . ($page-1) . "&amp;search=" . $search;
					break;

					// Startseite
				case "teaser":
					$lt_text_links = "" . $this->weiter_link . "&amp;page=" . ($page-1);
					global $template;
					// Nicht im admin Bereich checken
					if (!defined("admin")) {
						if ($this->cms->mod_rewrite == "2" and $this->modlink!="no") {
							$lt_text_links = "" . $this->weiter_link. "/reporeid/0/page/" . ($page-1);
							if ($template=="forum.html") {
								$lt_text_links = "" . $this->weiter_link. "/page/" . ($page-1);
							}
						}
						if ($this->cms->mod_surls == "1" and $this->modlink!="no") {
							$lt_text_links = "" . $this->surl_dat . "page-" . ($page-1).".html";

							if ($template=="forum.html"){
								$lt_text_links = "" . $this->surl_dat . "forumid-".(int) $this->checked->forumid."-page-" . ($page-1).".html";
							}
						}

						if ($this->cms->mod_free == "1" and $this->modlink!="no") {
							$lt_text_links = "" . $this->surl_dat . "page-" . ($page-1).".html";

							if ($template=="forum.html"){
								$lt_text_links = "" . $this->surl_dat . "forumid-".(int) $this->checked->forumid."-page-" . ($page-1).".html";
							}
						}
					}
					break;
				}
				// Link dem Template zuweisen
				$this->content->template['hrefruck']= $lt_text_links;
			}

			// Anzahl der möglichen Seiten ermitteln
			if ($pagesize<1) {
				$pagesize=1;
			}

			if ($result_anzahl<1) {
				$result_anzahl=1;
			}
			//$page_result = round($result_anzahl / $pagesize, 0) + 1;
			$page_result = ceil($result_anzahl / $pagesize);
			$this->content->template['weiter_anzahl_pages']=$page_result;
			$this->weiter_data=array();
			if (empty($page)) {
				$page=1;
			}
			// Zahl der zur Verfügung stehenden Seiten
			//Vergleich auf nur kleiner ist inkorrekt. Hierdurch zeigt das paginating eine Seite zu wenig an.
			//for($i = 1; $i < $page_result; $i++) {
			$page2=0;
			if ($page<=6) {
				$page3=10;
			}
			else {
				if ($page<($page_result-4)) {
					$page3=$page+4;
				}
				else {
					$page3=$page+($page_result-$page);
				}
				$page2=$page-4;
				$this->make_one($what);
			}
			if ($page2<1) {
				$page2=1;
			}
			//WEnn $page_result>10 dann nur 1 ... 234356 ... letzter anzeigen sonst wird das zuviel
			if ($page_result>10) {
				//wenn page kleiner 6 dann normal ab 1 und nur letzter
				if ($page<$page3) {
					$maxzahl=$page3;
				}
				else {
					$maxzahl=$page3;
				}
				//ansonsten abzählen
				for ($i = $page2; $i <= $maxzahl; $i++) {
					// Wenn es weitere mögliche Seiten gibt
					if ($i > 0) {
						// Seite aktiv, dann kein Link, nur anzeigen
						if ($page == $i) {
							$aktivhref = 1;
						}
						// Seite nicht aktiv, dann Link anzeigen
						else {
							$aktivhref = "0";
						}
						// Link erstellen
						switch ($what){
							// Suche aktiv
						case "search":
							$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i . "&amp;search=" . $search;
							break;

							// Startseite
						case "teaser":
							$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i;
							global $template;
							if (!defined("admin")){
								if ($this->cms->mod_rewrite == "2" and $this->modlink!="no") {
									$href_zahl = "" . $this->weiter_link . "/reporeid/0/page/" . $i;
									if ($template=="forum.html") {
										$href_zahl = "" . $this->weiter_link . "/page/" . $i;
									}
								}
								if ($this->cms->mod_surls == "1" and $this->modlink!="no") {
									$href_zahl = "" . $this->surl_dat . "page-" . $i.".html";

									if ($template=="forum.html") {
										$href_zahl = "" . $this->surl_dat . "forumid-".(int) $this->checked->forumid."-page-" . $i.".html";
									}
								}

								if ($this->cms->mod_free == "1" and $this->modlink!="no") {
									$href_zahl = "" . $this->surl_dat . "page-" . $i.".html";

									if ($template=="forum.html"){
										$href_zahl = "" . $this->surl_dat . "forumid-".(int) $this->checked->forumid."-page-" . $i.".html";
									}
								}
							}
							break;
						}

						//dient zur Anzeige der Suchresultate "von/bis"
						if ($aktivhref) {
							if ($i != 1) {
								$olstart = $i*$pagesize-($pagesize-1);
							}
							else {
								$olstart = 1;
							}
						}
						else {
							$olstart = NULL;
						}
						if ($i*$pagesize < $result_anzahl) {
							$olend = $i*$pagesize;
						}
						else {
							$olend = $result_anzahl;
						}
						IfNotSetNull($href_zahl);
						// array füllen für template
						array_push($this->weiter_data, array(
							'aktivhref' => $aktivhref,
							'linkname' => $href_zahl,
							'linknummer' => $i,
							//dient zur Anzeige der Gesamtanzahl der Ergebnisseiten in _module_intern/mod_weiter.html und
							//der Anzeige v. "Resultate von bis"
							'page_result' => $page_result,
							'olstart' => $olstart,
							'olend' => $olend,
						));

						$this->content->template['weiter_array']=$this->weiter_data;
						$this->content->template['weiter'] = true;
					}
				}
				if ($page<$page_result-4) {
					$this->make_max($what);
					$this->content->template['weiter_array']=$this->weiter_data;
					$this->content->template['weiter'] = true;
				}
			}
			//pageresult < 6
			else {
				for($i = 1; $i <= $page_result; $i++) {
					// Wenn es weitere mögliche Seiten gibt
					if ($i > 0) {
						// Seite aktiv, dann kein Link, nur anzeigen
						if ($page == $i) {
							$aktivhref = 1;
						}
						// Seite nicht aktiv, dann Link anzeigen
						else {
							$aktivhref = "0";
						}
						// Link erstellen
						switch ($what){
							// Suche aktiv
						case "search":
							$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i . "&amp;search=" . $search;
							break;

							// Startseite
						case "teaser":
							$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i;
							global $template;
							if (!defined("admin")){
								if ($this->cms->mod_rewrite == "2" and $this->modlink!="no") {
									$href_zahl = "" . $this->weiter_link . "/reporeid/0/page/" . $i;

									if ($template=="forum.html"){
										$href_zahl = "" . $this->weiter_link . "/page/" . $i;
									}
								}
								if ($this->cms->mod_surls == "1" and $this->modlink!="no") {
									$href_zahl = "" . $this->surl_dat . "page-" . $i.".html";

									if ($template=="forum.html"){
										$href_zahl = "" . $this->surl_dat . "forumid-".(int) $this->checked->forumid."-page-" . $i.".html";
									}
								}

								if ($this->cms->mod_free == "1" and $this->modlink!="no") {
									$href_zahl = "" . $this->surl_dat . "page-" . $i.".html";

									if ($template=="forum.html"){
										$href_zahl = "" . $this->surl_dat . "forumid-".(int) $this->checked->forumid."-page-" . $i.".html";
									}
								}
							}
							break;
						}
						//dient zur Anzeige der Suchresultate "von/bis"
						if ($aktivhref) {
							if ($i != 1) $olstart = $i*$pagesize-($pagesize-1);
							else $olstart = 1;
						}
						IfNotSetNull($olstart);
						if ($i*$pagesize < $result_anzahl) {
							$olend = $i*$pagesize;
						}
						else {
							$olend = $result_anzahl;
						}
						IfNotSetNull($href_zahl);
						// array füllen für template
						array_push($weiter_data, array(
							'aktivhref' => $aktivhref,
							'linkname' => $href_zahl,
							'linknummer' => $i,
							//dient zur Anzeige der Gesamtanzahl der Ergebnisseiten in _module_intern/mod_weiter.html und
							//der Anzeige v. "Resultate von bis"
							'page_result' => $page_result,
							'olstart' => $olstart,
							'olend' => $olend,
						));
						$this->content->template['weiter_array']=$weiter_data;
						$this->content->template['weiter'] = true;
					}
				}
			}

			// Wenn es noch eine weitere Seite als die aktuelle gibt
			if ($result_anzahl > $pagesize * $page) {

				// Wenn Seite nicht gesetzt ist
				if (!isset($page)) {
					$page = 1;
				}
				// Link zur nächsten Seite erzeugen
				switch ($what){
					// Suche aktiv
				case "search":
					$href = "" . $this->weiter_link . "&amp;page=" . ($page + 1) . "&amp;search=" . $search;
					break;
					// Startseite
				case "teaser":
					$href = "" . $this->weiter_link . "&amp;page=" . ($page + 1);
					global $template;
					if (!defined("admin")) {
						if ($this->cms->mod_rewrite == "2" and $this->modlink!="no") {
							$href = "" . $this->weiter_link . "/reporeid/0/page/" . ($page + 1);
							if ($template=="forum.html") {
								$href = "" . $this->weiter_link . "/page/" . ($page + 1);
							}
						}
						if ($this->cms->mod_surls == "1" and $this->modlink!="no") {
							$href = "" . $this->surl_dat . "page-" . ($page+1).".html";
							if ($template=="forum.html"){
								$href = "" . $this->surl_dat . "forumid-".(int) $this->checked->forumid."-page-" . ($page+1).".html";
							}
						}
						if ($this->cms->mod_free == "1" and $this->modlink!="no") {
							$href = "" . $this->surl_dat . "page-" . ($page+1).".html";
							if ($template=="forum.html"){
								$href = "" . $this->surl_dat . "forumid-".(int) $this->checked->forumid."-page-" . ($page+1).".html";
							}
						}
					}
					break;
				}

				// Link dem Template zuweisen
				IfNotSetNull($href);
				$this->content->template['hrefweiter']= $href;
			}
		}
		if(!isset($this->content->template['hrefweiter'])) {
			$this->content->template['hrefweiter'] = NULL;
		}

		if(!isset($this->content->template['hrefruck'])) {
			$this->content->template['hrefruck'] = NULL;
		}
	}

	/**
	 * weiter::get_surls_dat()
	 *
	 * @return void
	 */
	function get_surls_dat()
	{
		if(!isset($this->surl_urlvar)) {
			$this->surl_urlvar = NULL;
		}

		foreach ($_GET as $key=>$value) {
			if (!empty($value))
			{
				#$this->surl_urlvar.=$value;
				if ( !stristr($value,'.html')) {
					$this->surl_urlvar.=$value."/";
				}
				else {
					$pg1=explode("-",$value);

					if ($pg1['0']=="page") {
						$pg2=explode('.html',$pg1['1']);
						if (is_numeric($pg2['0'])) {
							$this->checked->page=$pg2['0'];
						}
					}
				}
			}
		}

		if(!isset($this->surl_urlvar)) {
			$this->surl_urlvar = NULL;
		}

		$this->surl_dat=$this->surl_urlvar;

		if ($this->cms->mod_free) {
			$sprechende_url=urldecode($_SERVER['REQUEST_URI']);
			//Dann das UNterverzeichnis falls vorhanden
			$sprechende_url=trim(str_replace(PAPOO_WEB_PFAD,"",$sprechende_url));
			//Dann das trailing Slash
			$sprechende_url=str_replace(PAPOO_WEB_PFAD,"",$sprechende_url);
			$sprechende_url=substr($sprechende_url,1,(strlen($sprechende_url)-1));

			if (stristr($sprechende_url,".html")) {
				$data=explode("/",$sprechende_url);

				$last=array_pop($data);
				$sprechende_url=str_replace($last,"",$sprechende_url);
			}

			$this->surl_dat="".$sprechende_url;
		}
	}

	/**
	 * den ersten Eintrag erstellen
	 *
	 * @param $what
	 */
	function make_one($what)
	{
		if (empty($this->checked->search)) {
			$this->checked->search="";
		}
		// $search einbinden
		$search = $this->checked->search;

		//dient zur Anzeige der Gesamtanzahl der Ergebnisseiten
		$page_result = 1;
		$i=1;
		// Link erstellen
		switch ($what) {
			// Suche aktiv
		case "search":
			$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i . "&amp;search=" . $search;
			break;
			// Startseite
		case "teaser":
			$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i;
			global $template;
			if (!defined("admin")){
				if ($this->cms->mod_rewrite == "2" and $this->modlink!="no") {
					$href_zahl = "" . $this->weiter_link . "/reporeid/0/page/" . $i;
					if ($template=="forum.html") {
						$href_zahl = "" . $this->weiter_link . "/page/" . $i;
					}
				}
				if ($this->cms->mod_surls == "1" and $this->modlink!="no") {
					$href_zahl = "" . $this->surl_dat . "page-" . ($i).".html";

					if ($template=="forum.html") {
						$href_zahl = "" . $this->surl_dat . "forumid-".(int) $this->checked->forumid."-page-" . ($i).".html";
					}
				}
				if ($this->cms->mod_free == "1" and $this->modlink!="no") {
					$href_zahl = "" . $this->surl_dat . "page-" . $i.".html";
					if ($template=="forum.html"){
						$href_zahl = "" . $this->surl_dat . "forumid-".(int) $this->checked->forumid."-page-" . $i.".html";
					}
				}
			}
			break;
		}

		IfNotSetNull($aktivhref);
		IfNotSetNull($olstart);
		IfNotSetNull($olend);
		IfNotSetNull($href_zahl);

		//dient zur Anzeige der Suchresultate "von/bis"

		// array füllen für template
		array_push($this->weiter_data, array(
			'aktivhref' => $aktivhref,
			'linkname' => $href_zahl,
			'linknummer' => $i,
			'first' => "ok",
			//dient zur Anzeige der Gesamtanzahl der Ergebnisseiten in _module_intern/mod_weiter.html und
			//der Anzeige v. "Resultate von bis"
			'page_result' => $page_result,
			'olstart' => $olstart,
			'olend' => $olend,
		));
	}

	/**
	 * den ersten Eintrag erstellen
	 *
	 * @param $what
	 */
	function make_max($what)
	{
		if (empty($this->checked->search)) {
			$this->checked->search="";
		}
		// $search einbinden
		$search = $this->checked->search;
		// maximale Anzahl der Ergebnisse
		$pagesize = $this->cms->pagesize;
		// tatsächliche Anzahl der Ergebnisse
		$result_anzahl = $this->result_anzahl;
		//dient zur Anzeige der Gesamtanzahl der Ergebnisseiten

		$i=$page_result = ceil($result_anzahl / $pagesize);
		// Link erstellen
		switch ($what){
			// Suche aktiv
		case "search":
			$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i . "&amp;search=" . $search;
			break;
			// Startseite
		case "teaser":
			$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i;
			global $template;
			if (!defined("admin")) {
				if ($this->cms->mod_rewrite == "2" and $this->modlink!="no") {
					$href_zahl = "" . $this->weiter_link . "/reporeid/0/page/" . $i;
					if ($template=="forum.html") {
						$href_zahl = "" . $this->weiter_link . "/page/" . $i;
					}
				}
				if ($this->cms->mod_surls == "1" and $this->modlink!="no") {
					$href_zahl = "" . $this->surl_dat . "page-" . ($i).".html";
					if ($template=="forum.html") {
						$href_zahl = "" . $this->surl_dat . "forumid-".(int) $this->checked->forumid."-page-" . ($i).".html";
					}
				}
				if ($this->cms->mod_free == "1" and $this->modlink!="no") {
					$href_zahl = "" . $this->surl_dat . "page-" . $i.".html";
					if ($template=="forum.html"){
						$href_zahl = "" . $this->surl_dat . "forumid-".(int) $this->checked->forumid."-page-" . $i.".html";
					}
				}
			}
			break;
		}

		IfNotSetNull($aktivhref);
		IfNotSetNull($olstart);
		IfNotSetNull($olend);
		IfNotSetNull($href_zahl);

		//dient zur Anzeige der Suchresultate "von/bis"

		// array füllen für template
		array_push($this->weiter_data, array(
			'aktivhref' => $aktivhref,
			'linkname' => $href_zahl,
			'linknummer' => $i,
			'last' => "ok",
			//dient zur Anzeige der Gesamtanzahl der Ergebnisseiten in _module_intern/mod_weiter.html und
			//der Anzeige v. "Resultate von bis"
			'page_result' => $page_result,
			'olstart' => $olstart,
			'olend' => $olend,
		));
	}

	/**
	 * @param $limit
	 */
	function make_limit($limit)
	{
		$this->cms->pagesize=$this->pagesize=$limit;
		$page = $this->checked->page;

		if ($this->checked->page > 100000){
			$page = 100000;
		}
		elseif ($this->checked->page < 1) {
			$page = 1;
		}
		elseif (!is_numeric($this->checked->page)) {
			unset($page);
		}
		// Limit für die Datenbank abfragen
		if (isset($page)) {
			$this->sqllimit = "LIMIT " . (($page-1) * $this->pagesize) . "," . ($this->pagesize);
		}
		else {
			$this->sqllimit = "LIMIT " . ($this->pagesize);
		}
	}
}

$weiter= new weiter();