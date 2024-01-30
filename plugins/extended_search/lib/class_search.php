<?php
/**
 * @package   Papoo
 * @author    Papoo Software
 * @copyright 2009
 * @version   $Id$
 * @access    public
 */

if(stristr($_SERVER['PHP_SELF'], 'class_search.php')) {
	die('You are not allowed to see this page directly');
}

/**
 * Class class_search_front
 */
#[AllowDynamicProperties]
class class_search_front
{
	/** @var bool Kundenanpassung */
	private $is_hoe = false;

	/**
	 * class_search_front::class_search_front()
	 * Klasse zum durchf�hren der Suche
	 *
	 * @return void
	 */

	function __construct()
	{
		// Einbindung globaler papoo-Klassen
		global $diverse, $db, $checked, $content, $menu, $weiter, $user, $cms;
		$this->diverse = &$diverse;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->content = &$content;
		$this->menu = &$menu;
		$this->weiter = &$weiter;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->content->template['menuid_aktuell'] = $this->checked->menuid;
	}

	/**
	 * class_search_front::make_statistik()
	 *
	 * @param $woerter
	 * @return void
	 */
	function make_statistik($woerter)
	{

		if(is_array($woerter)) {
			foreach($woerter as $key => $value) {
				if($key >= 1) {
					$mehr = "OK";
				}

				//prüfen, wurde danach schon gesucht?
				//wenn ja, den vorhanden Eintrag hochsetzen
				$sql = sprintf("SELECT ext_search_stat_id, ext_search_count FROM %s
		                      	WHERE ext_search_stat_search LIKE '%s'",
					$this->cms->tbname['plugin_ext_search_statistik'], $this->db->escape(utf8_encode($value)));
				$sucheintrag = $this->db->get_results($sql);

				if($sucheintrag){
					//Zuerst pr�fen
					$sql = sprintf("UPDATE %s
		                      SET ext_search_stat_time='%d',
		                      ext_search_count='%d'
		                      WHERE ext_search_stat_search LIKE '%s'", $this->cms->tbname['plugin_ext_search_statistik'], time(), $sucheintrag[0]->ext_search_count+1, $this->db->escape(utf8_encode($value)));

					$this->db->query($sql);
				}
				else {
					//Zuerst pr�fen
					$sql = sprintf("INSERT INTO %s
		                      SET ext_search_stat_search='%s',
		                      ext_search_stat_time='%d',
		                      ext_search_count='1'", $this->cms->tbname['plugin_ext_search_statistik'], $this->db->escape(utf8_encode($value)), time());
					$this->db->query($sql);
				}
			}
		}
		if(isset($mehr) && $mehr == "OK") {

			//prüfen, wurde danach schon gesucht?
			//wenn ja, den vorhanden Eintrag hochsetzen
			$sql = sprintf("SELECT ext_search_stat_id, ext_search_count FROM %s
		                    WHERE ext_search_stat_search LIKE '%s'",
				$this->cms->tbname['plugin_ext_search_statistik'], $this->db->escape($this->checked->search));
			$sucheintrag = $this->db->get_results($sql);

			if($sucheintrag){
				//Und einmal den gesamten Begriff
				$sql = sprintf("UPDATE %s
                                SET ext_search_stat_time='%d',
                                ext_search_count='%d'
                                WHERE ext_search_stat_search LIKE '%s'", $this->cms->tbname['plugin_ext_search_statistik'], time(), $sucheintrag[0]->ext_search_count+1, $this->db->escape($this->checked->search));
				$this->db->query($sql);

			}
			else {
				//Und einmal den gesamten Begriff
				$sql = sprintf("INSERT INTO %s
                                SET ext_search_stat_search='%s',
                                ext_search_stat_time='%d',
                                ext_search_count='1'", $this->cms->tbname['plugin_ext_search_statistik'], $this->db->escape($this->checked->search), time());
				$this->db->query($sql);
			}
		}
	}

	/**
	 * class_search_front::make_thesaurus()
	 *
	 * @param mixed $search
	 *
	 * @return void
	 */
	function make_thesaurus($search)
	{
		$sql = sprintf("SELECT ext_search_thesaurus_einten_ie_vielleicht_egriff FROM %s
                    WHERE ext_search_thesaurus_uchbegriffe='%s'
                    AND ext_search_thesaurus_lang_id='%d'", $this->cms->tbname['plugin_ext_search_thesaurus_search'], $this->db->escape($search), $this->cms->lang_id);
		$result = $this->db->get_var($sql);
		$this->content->template['thesaurus_frontend'] = $result;
	}

	/**
	 * class_search_front::make_einschraenkungen()
	 *
	 * @return void
	 */
	function make_einschraenkungen()
	{
		$sql = sprintf("SELECT * FROM %s", $this->cms->tbname['plugin_ext_search_config']);
		$daten = $this->db->get_results($sql, ARRAY_A);

		if(is_array($daten)) {
			foreach($daten as $key => $value) {
				$einsch['artikel'] = $value['ext_search_ur_rtikel'];
				$einsch['produkte'] = $value['ext_search_ur_rodukte'];
				$einsch['forum'] = $value['ext_search_ur_orum'];
				$einsch['mv'] = $value['ext_search_ur_lex'];
				$einsch['doc'] = $value['ext_search_ur_doc'];
				$einsch['faq'] = $value['ext_search_inschrnkung_auf_nur_'];
			}
		}
		if(isset($einsch) && is_array($einsch)) {
			$sum = array_sum($einsch);
		}

		if(isset($einsch) && isset($sum) && $sum > 0) {
			$this->content->template['einschraenkungen_search'] = $einsch;
		}

		//Eisntellungen �bergeben
		if(is_numeric($this->checked->erw_suchbereich)) {
			$this->content->template['erw_suchbereich'] = $this->checked->erw_suchbereich;
		}
		else {
			$this->checked->erw_suchbereich = 0;
		}
	}

	/**
	 * class_search_front::make_js_header()
	 *
	 * @return void
	 */
	function make_js_header()
	{
		//$this->content->template['plugin_header'][]='<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/js/jquery.js"></script>';
		$this->content->template['plugin_header'][] = '<script type="text/javascript" ' .
			'src="' . PAPOO_WEB_PFAD . '/plugins/extended_search/js/jquery.autocomplete.js">' .
			'</script>';

		$this->content->template['plugin_header'][] = '<link rel="stylesheet" ' .
			'href="' . PAPOO_WEB_PFAD . '/plugins/extended_search/css/autocomplete.css" ' .
			'type="text/css" media="screen"  />';

		$this->is_datepicker = 1;

		$this->content->template['plugin_header'][] = 'nobr:<script type="text/javascript" >
          $(document).ready(function(){
            $(\'#query\').autocomplete({
              minChars: 2, // minimale Anzahl an Zeichen, die eingegeben werden müssen
              noCache: true, // Cache deaktivieren?
              appendTo: $("body"), // Resultate anhängen an
              zIndex: 1001,
              serviceUrl:\'' . PAPOO_WEB_PFAD . '/plugins/extended_search/lib/ajax_search.php\',
              onSelect: function (suggestion) {
                //alert(\'You selected: \' + suggestion.value + \', \' + suggestion.data);
              }
            });
          });
          </script>';
	}

	function make_js_header_modul()
	{
		$sql = sprintf(
			"SELECT ext_search_vorschlge_im_modul_suchmaske 
            FROM %s;",
			$this->cms->tbname['plugin_ext_search_config']
		);
		$daten = $this->db->get_var($sql);

		if($daten != 1) {
			return;
		}
		//$this->content->template['plugin_header'][]='<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/js/jquery.js"></script>';
		$this->content->template['plugin_header'][] = '<script type="text/javascript" src="' . PAPOO_WEB_PFAD
			. '/plugins/extended_search/js/jquery.autocomplete.js"></script>';

		$this->content->template['plugin_header'][] = '<link rel="stylesheet" href="' . PAPOO_WEB_PFAD
			. '/plugins/extended_search/css/autocomplete.css" type="text/css" media="screen"  />';

		$this->is_datepicker = 1;

		$this->content->template['plugin_header'][] =
			'nobr:<script type="text/javascript" >//<![CDATA[
              jQuery(document).ready(function(){
                var a1;
                jQuery(function(){            
                  var options = {
                    serviceUrl: \'' . PAPOO_WEB_PFAD . '/plugins/extended_search/lib/ajax_search.php\',
                    width: 300,
                    delimiter: /(,|;)\s*/,
                    deferRequestBy: 0, //miliseconds
                    params: { country: \'Yes\' }
                  };
                  a1 = $(\'#search\').autocomplete(options);
                });
              });
            //]]>
		</script>';
	}

	/**
	 * extended_search_class::jetzt_suchen()
	 *
	 * @return array|void
	 */
	public function jetzt_suchen()
	{
		$this->make_js_header();

		//Suche bereinigen
		$suchwort = $this->clean_suchwort();

		$this->content->template['suchworte'] = utf8_encode($suchwort);

		//Wenn zu lang wieder zur�ck
		if(strlen($suchwort) > 200) {
			return;
		}

		//Einzelw�rter erstellen
		$such_woerter = preg_split('/\s+/', $suchwort);

		$sub_sql_text = "";
		//Durchgehen und trimmen
		if(is_array($such_woerter) && count($such_woerter)>1) {
			foreach($such_woerter as $key => $value) {
				//$key=$key+1;
				$such_woerter[$key + 1] = trim($value);
				$sub_sql_text .= " AND ext_search_completet_text LIKE '%" . $this->db->escape(utf8_encode($value))
					. "%' ";
			}
			unset($such_woerter['0']);
		}

		$this->such_woerter = $such_woerter;
		$_SESSION['search_var'] = $such_woerter;

		//Statistik
		$this->make_statistik($this->such_woerter);

		//Thesaurus
		$this->make_thesaurus($this->checked->search);

		//Einschr�nkungen
		$this->make_einschraenkungen();

		//Menu Einschränkungen
		$this->make_menu_einschraekungen();

		//Rechte an dem Eintrag rausholen
		$sql = sprintf("SELECT * FROM %s
                WHERE userid='%d' ORDER BY gruppenid DESC", $this->cms->tbname['papoo_lookup_ug'], $this->user->userid);
		$result_gruppen = $this->db->get_results($sql, ARRAY_A);

		//Nicht zu viele, max 4
		if(count($such_woerter) > 4) {
			array_splice($such_woerter, 4);
		}

		$einschr = "";
		//Einschr�nkungen
		if($this->checked->erw_suchbereich == 1) {
			$einschr .= " AND ext_search_seite_reporeid > '1' ";
		}

		if($this->checked->erw_suchbereich == 2) {
			$einschr .= " AND ext_search_seite_produkt_id > '1' ";
		}

		if($this->checked->erw_suchbereich == 3) {
			$einschr .= " AND ext_search_seite_message_id > '1' ";
		}

		if($this->checked->erw_suchbereich == 4) {
			$einschr .= " AND ext_search_seite_mv_id >= '1' ";
		}

		if($this->checked->erw_suchbereich == 5) {
			$einschr .= " AND ext_search_seite_faq_id > '1' ";
		}

		if($this->checked->erw_suchbereich == 6) {
			$einschr .= " AND ext_search_document_path IS NOT NULL ";
		}

		if(is_numeric($this->checked->menuid_search)) {
			//Alle Unterpunkte rausholen...
			$data = $this->get_sub_menus($this->checked->menuid_search);
			if(isset($this->menu_id) && is_array($this->menu_id)) {
				$einschr .= " AND (";
				$einschr2 = "";
				foreach($this->menu_id as $km => $vm) {
					$einschr2 .= " ext_search_seite_menu_id = '" . $this->db->escape($vm['menuid']) . "' OR";
				}
				$einschr2 = substr($einschr2, 0, -2);
				$einschr .= " " . $einschr2 . ") ";
			}
		}

		// Zuerst das LIMIT rausholen
		//ARRAY durchgehen
		$sub = "";
		if(is_array($such_woerter)) {
			$sub = array();
			foreach($such_woerter as $key => $value) {
				if(strlen($value) < 2) {
					if($key == 0) {
						$no = "NO";
					}
					continue;
				}

				//Gruppen durchgehen und eintragen
				if(!is_array($result_gruppen)) {
					continue;
				}

				//$gruppen_gesamt="";
				foreach($result_gruppen as $key2 => $value2) {
					//$gruppen_gesamt.=$value['gruppenid'].";";
					$sub[] = " ext_search_gruppen_id='" . $value2['gruppenid'] . "'
                        AND ext_search_wort_id LIKE '" . utf8_encode($value) . "%' " . $sub_sql_text . " " . $einschr;
				}
			}
            $sub = implode(" OR ", $sub);
		}
		$sql = sprintf("SELECT ext_search_id
                FROM %s
                LEFT JOIN %s ON ext_search_id=ext_search_seite_id
                LEFT JOIN %s ON ext_search_seite_id=ext_search_id_rid
                WHERE (%s)
                AND ext_search_lang_id='%d'
                GROUP BY ext_search_id ", $this->cms->tbname['plugin_ext_search_page'], $this->cms->tbname['plugin_ext_search_vorkommen'], $this->cms->tbname['plugin_ext_search_gruppen'], $sub, $this->cms->lang_id);

		$count = 0;
		if(!isset($no) || isset($no) && $no != "NO") {
			$count = count($this->db->get_results($sql, ARRAY_A));
		}

		$sql = sprintf("SELECT ext_search_nzahl_der_uchergebnisse
                    FROM %s", $this->cms->tbname['plugin_ext_search_config']);
		$anzahl = (int)$this->db->get_var($sql);

		$this->content->template['weiter_array'] = "";

		$this->weiter->result_anzahl = $count;

		$this->weiter->make_limit($anzahl);
		$this->content->template['matches'] = $this->weiter->result_anzahl;

		//Hier die Like Ergebnisse rausholen für besseres Matching von Mehrwörter Suchen
		#$like_search=$this->get_like_search_data($suchwort);

		$sql = sprintf("SELECT
                ext_search_id,
                SUM(ext_search_id)/ext_search_id AS summe,
                ext_search_url,
                ext_search_title,
                ext_search_description,
                ext_search_completet_text,
                ext_search_seite_menu_id,
                ext_search_seite_reporeid,
                ext_search_seite_produkt_id,
                ext_search_seite_forum_id,
                ext_search_seite_message_id,
                ext_search_seite_faq_id,
                ext_search_seite_mv_id,
                ext_search_seite_mv_content_id,
                ext_search_document_path,
                SUM(ext_search_score_id) AS ext_search_score_id
                FROM %s
                LEFT JOIN %s ON ext_search_id=ext_search_seite_id
                LEFT JOIN %s ON ext_search_seite_id=ext_search_id_rid
                WHERE (%s)
                AND ext_search_lang_id='%d'
                GROUP BY ext_search_id
                ORDER BY ext_search_score_id DESC, summe DESC
                %s",
			$this->cms->tbname['plugin_ext_search_page'],
			$this->cms->tbname['plugin_ext_search_vorkommen'],
			$this->cms->tbname['plugin_ext_search_gruppen'],
			$sub, $this->cms->lang_id,
			$this->weiter->sqllimit
		);

		$what = "search";
		// Links zu weiteren Seiten anzeigen
		$this->weiter->do_weiter($what);

		if(!isset($no) || isset($no) && $no != "NO") {
			$result = $this->db->get_results($sql, ARRAY_A);
		}
		//Jetzt KA suchen
		if($this->is_hoe) {
			$data = $this->check_extern_searcht($suchwort);
			if(!is_array($result)) {
				$result = array();
			}
			if(is_array($data)) {
				$result = array_merge($data, $result);
			}
		}

		// FIXME: like_search war nie gesetzt?
		IfNotSetNull($like_search);
		IfNotSetNull($result);

		//Like und normale Suche mixen
		$result = $this->mix_search($like_search, $result);

		//Suche aufbereiten f�r Ausgabe
		$result = $this->create_front_array($result, $suchwort);

		return $result;
	}

	/**
	 * @param int $id
	 * @return bool|void
	 */
	private function get_sub_menus($id = 0)
	{
		//Checken ob der Eintrag überhaupt so gesucht werden darf... ext_search_menus
		$sql = sprintf("SELECT ext_search_menus FROM %s ", DB_PRAEFIX . "plugin_ext_search_config");
		$array_dat = unserialize($this->db->get_var($sql));

		$diectad = $array_dat['cattext_ar'];
		if(!is_array($diectad)) {
			return false;
		}

		$datax = array_search($id, $diectad);

		if(empty($datax)) {
			unset($this->menu_id);

			return false;
		}

		if($id >= 1) {
			$this->menu_id[]['menuid'] = $id;
			//Unterpunkte rausholen
			$sql = sprintf("SELECT menuid FROM %s WHERE untermenuzu='%d'", DB_PRAEFIX . "papoo_me_nu", $id);
			$menuids = $this->db->get_results($sql, ARRAY_A);
			//debug::print_d($menuids);
			if(is_array($menuids)) {
				foreach($menuids as $k => $v) {
					$this->menu_id[] = $v;
				}
			}
		}

		//Spezialfall swro
		if($this->checked->menuid_search == 45) {
			$this->menu_id[]['menuid'] = 46;
		}
	}

	private function make_menu_einschraekungen()
	{
		//Daten rausholen
		$sql = sprintf("SELECT ext_search_menus FROM %s ", DB_PRAEFIX . "plugin_ext_search_config");
		$data = $this->db->get_var($sql);

		$menus = unserialize($data);
		$menu_dats = $menus['cattext_ar'];
		if(is_array($menu_dats)) {
			if(count($menu_dats) > 0) {
				$this->content->template['menu_dat_show_search'] = "ok";
			}
			if(in_array($this->checked->menuid_search, $menu_dats)) {
				$this->checked->menuid_search_now = $this->checked->menuid_search;
				$this->content->template['menuid_search_now'] = $this->checked->menuid_search_now;
			}
		}
		if(!empty($this->checked->search)) {
			$this->content->template['menuid_search_now'] = $this->checked->menuid_search;
		}

		//Menupunkte durchlaufen
		$this->content->template['data_front_menu'] = $this->menu->data_front_complete;
	}

	/**
	 * @param mixed $like_search
	 * @param mixed $result
	 * Mixed die Ergebnisse so dass die mit der Like Suche oben stehen... wenn es eine Phrase gibt...
	 * @return array
	 */
	private function mix_search($like_search = array(), $result = array())
	{
		$standard = $like = $neu = array();

		if(is_array($result)) {
			foreach($result as $k => $v) {
				$standard[$v['ext_search_id']] = $v;
			}
		}

		if(is_array($like_search)) {
			foreach($like_search as $k => $v) {
				$like[$v['ext_search_id']] = $v;
			}
		}

		foreach($like as $k => $v) {
			if(!empty($standard[$k])) {
				$neu[] = $standard[$k];
			} else {
				$neu[] = $v;
			}

			unset($standard[$k]);
		}
		$return = array_merge($neu, $standard);
		return $return;
	}

	/**
	 * @param string $suchwort
	 * @return array|void <NULL, multitype:, unknown>
	 */
	private function get_like_search_data($suchwort = "")
	{
		$suchwort = utf8_encode($suchwort);
		//Suchen mit LIKE
		//SUM(ext_search_id)/ext_search_id AS summe,
		$sql = sprintf("SELECT
    			SUM(ext_search_score_id) AS ext_search_score_id,
                ext_search_id,
                
                ext_search_url,
                ext_search_title,
                ext_search_description,
                ext_search_completet_text,
                ext_search_seite_reporeid,
                ext_search_seite_produkt_id,
                ext_search_seite_forum_id,
                ext_search_seite_message_id,
                ext_search_seite_faq_id,
                ext_search_seite_mv_id,
                ext_search_seite_mv_content_id,
                ext_search_document_path
                
                FROM %s
                LEFT JOIN %s ON ext_search_id=ext_search_seite_id
                LEFT JOIN %s ON ext_search_seite_id=ext_search_id_rid
                WHERE
    			(ext_search_title LIKE '%s' OR
                ext_search_description LIKE '%s' OR 
                ext_search_completet_text LIKE '%s' )
                AND ext_search_lang_id='%d'
                GROUP BY ext_search_id
                ORDER BY ext_search_score_id DESC
                %s", $this->cms->tbname['plugin_ext_search_page'], $this->cms->tbname['plugin_ext_search_vorkommen'], $this->cms->tbname['plugin_ext_search_gruppen'], '%'
			. $this->db->escape($suchwort) . '%', '%' . $this->db->escape($suchwort) . '%', '%'
			. $this->db->escape($suchwort) . '%', $this->cms->lang_id, $this->weiter->sqllimit);

		$result = $this->db->get_results($sql, ARRAY_A);
		return $result;
	}

	/**
	 * class_search_front::check_extern_searcht()
	 *
	 * @param $suchwort
	 * @return array|bool
	 */
	protected function check_extern_searcht($suchwort)
	{
		return true;

		//Url
		$url = 'http://gemeinde-hoerstel.server7.citywerk.org/suchen.php?lang=de&x=0&y=0&keyword=' . $suchwort;

		$daten = $this->http_request_open($url);
		$dat_ar1 = explode('<!-- BEGIN: searchresults -->', $daten);
		$dat_ar2 = explode('<!-- END: searchresults -->', $dat_ar1['1']);

		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		preg_match_all("/$regexp/siU", $dat_ar2['0'], $matches);
		if(is_array($matches['2'])) {
			$i = 0;
			foreach($matches['2'] as $key => $value) {
				$dt['ext_search_id'] = 0;
				$dt['ext_search_url'] = $value;
				$dt['ext_search_title'] = utf8_encode($matches['3'][$i]);
				$dt['ext_search_description'] = utf8_encode($matches['3'][$i]);
				$dt['ext_search_id'] = 0;
				$dt['ext_search_id'] = 0;
				$dt['ext_search_id'] = 0;
				$export[] = $dt;
				$i++;
			}
		}
		return $export;
	}

	/**
	 * @param $url
	 * @param int $timeout
	 * @return bool|string
	 */
	protected function http_request_open($url, $timeout = 10)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		##curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.8.1.4) Gecko/20070515 Firefox/2.0.0.4");

		$curl_ret = curl_exec($ch);
		curl_close($ch);

		return $curl_ret;
	}

	/**
	 * extended_search_class::get_teaser_text()
	 *
	 * @param $text
	 * @param string $suchwort
	 * @return mixed $text
	 */
	function get_teaser_text($text, $suchwort = "")
	{
		//Array erstellen
		$text = str_replace(html_entity_decode("&nbsp;", ENT_COMPAT, "UTF-8"), " ", $text);
		$text_array = explode(" ", $text);

		if(is_array($this->such_woerter)) {
			foreach($this->such_woerter as $key => $value) {
				$this->such_woerter_neu[$key] = $value;
			}
		}

		/** @var cms $cms */
		global $cms;
		/** @var ezSQL_mysqli $db */
		global $db;

		$teaserMaxLength = (int)($db->get_var(
				"SELECT ext_search_teaser_max_length FROM {$cms->tbname['plugin_ext_search_config']} LIMIT 1"
			) ?? 400);

		$this->such_woerter = $this->such_woerter_neu;
		$neu_array = array();
		//durchgehen
		if(is_array($text_array)) {
			foreach($text_array as $key => $value) {
				$value = strtolower($value);
				//Erstes
				$needle = utf8_encode($this->such_woerter['0']);
				if(empty($needle) == false && stristr($value, $needle) && empty($neu_array)) {
					// $value;
					$xk = $key - 3;
					if($i == 3) {
						$neu_array = array_slice($text_array, $xk);
					}
					$i++;
				}
			}
			$key = 0;
			if(empty($neu_array) && !empty($this->such_woerter['1'])) {
				foreach($text_array as $key => $value) {
					$value = strtolower($value);
					//Zweites
					if(stristr($value, $this->such_woerter['1'])) {
						$xk = $key - 3;
						$neu_array = array_slice($text_array, $xk);
					}
				}
			}
			$key = 0;
			if(empty($neu_array) && !empty($this->such_woerter['2'])) {
				foreach($text_array as $key => $value) {
					$value = strtolower($value);
					//Drittes
					if(stristr($value, $this->such_woerter['2'])) {
						$xk = $key - 3;
						$neu_array = array_slice($text_array, $xk);
					}
				}
			}
			$key = 0;
			if(empty($neu_array) && !empty($this->such_woerter['3'])) {
				foreach($text_array as $key => $value) {
					$value = strtolower($value);
					//Viertes
					if(stristr($value, $this->such_woerter['3'])) {
						$xk = $key - 3;
						$neu_array = array_slice($text_array, $xk);
					}
				}
			}
		}
		$text3 = implode(" ", $text_array);
		$text2 = implode(" ", $neu_array);
		$text3 = str_ireplace("  ", " ", $text3);
		$text3 = str_ireplace("  ", " ", $text3);
		$text3 = str_ireplace("  ", " ", $text3);

		//if (stristr($text3,$suchwort))
		$suchwort = utf8_encode($suchwort);

		if(stristr($text3, $suchwort)) {
			$position = stripos($text3, $suchwort);
			$actualstr = substr($text3, $position, strlen($suchwort));

			$start = 0;
			$nextWhiteSpace = strpos($text3, ' ', $start + $teaserMaxLength);

			$substr = substr($text3, $start, $nextWhiteSpace !== false ? $nextWhiteSpace : $teaserMaxLength) . ' [&hellip;]';

			$text2 = str_ireplace($suchwort, '<strong class="search_highlight">' . (($actualstr)) . '</strong>', $substr);

			return $text2;
		}

		if(strlen($text2) < 50) {
			$text2 = $text;
		}

		$nextWhiteSpace = strlen($text2) > $teaserMaxLength ? mb_strpos($text2, ' ', $teaserMaxLength) : false;

		$text2 = mb_substr($text2, 0, $nextWhiteSpace !== false ? $nextWhiteSpace : $teaserMaxLength, 'UTF-8') . ' [&hellip;]';

		//Highlighting
		if(is_array($this->such_woerter)) {
			foreach($this->such_woerter as $key => $value) {
				$value = strtolower($value);
				$text2 = str_ireplace('' . utf8_encode(ucfirst($value)) . '', '<strong class="search_highlight">'
					. utf8_encode(ucfirst($value)) . '</strong>', $text2);
				if(!mb_ereg(utf8_encode(ucfirst($value)), $text2)) {
					$text2 = str_ireplace('' . utf8_encode($value) . '', '<strong class="search_highlight">'
						. utf8_encode($value) . '</strong>', $text2);
				}
			}
		}

		return $text2;
	}

	/**
	 * extended_search_class::create_front_array()
	 *
	 * @param $front
	 * @param string $suchwort
	 * @return array $front_array
	 */
	function create_front_array($front, $suchwort = "")
	{
		if(!is_array($front)) {
			return array();
		}

		/** @var menu_class $menu */
		global $menu;

		// Use function to collect menu ancestors for search results
		$makeBreadcrumbs = function ($id, array $collection, array $tracker=[]) use (&$makeBreadcrumbs) {
			$menu = array_reduce($collection, function ($carry, $item) use ($id) {
				return $item['menuid'] == $id ? $item : $carry;
			});

			return $menu && in_array($id, $tracker) == false
				? array_merge($makeBreadcrumbs($menu['untermenuzu'], $collection, array_merge($tracker, [$menu['untermenuzu']])), [$menu])
				: [];
		};

		$front_array = array();
		foreach($front as $key => $value) {
			$this->image_produkt = "";
			$array = $value;

			//Die url
			$array['url_header'] = $this->get_url_des_eintrages($value);

			//Die �berschrift
			$array['uberschrift'] = substr($value['ext_search_title'], 0, 80);

			if(strlen($array['uberschrift']) < 5) {
				$array['uberschrift'] = substr($value['ext_search_completet_text'], 0, 80);
			}

			$array['breadcrumbs'] = $makeBreadcrumbs($value['ext_search_seite_menu_id'], $menu->data_front_complete);

			//Der Teaser Text
			$array['text'] = $this->get_teaser_text($value['ext_search_completet_text'], $suchwort);

			//Bild
			$array['img'] = $this->image_produkt;

			// Dokumente
			$path = str_replace(PAPOO_ABS_PFAD, "", $value['ext_search_document_path']);
			$array['ext_search_document_path'] = str_replace(DIRECTORY_SEPARATOR, "/", $path);

			$front_array[] = $array;
		}
		return $front_array;
	}

	/**
	 * class_search_front::get_url_des_eintrages()
	 *
	 * @param array $value
	 *
	 * @return mixed $url
	 */
	function get_url_des_eintrages($value = array())
	{
		$this->m_url = array();

		//Fall 1 Artikel
		if($value['ext_search_seite_reporeid'] >= 1) {
			$url = $this->get_artikel_url($value['ext_search_seite_reporeid']);
		}

		//Fall 2 Produkt Shop
		if($value['ext_search_seite_produkt_id'] >= 1) {
			$url = $this->get_produkt_url($value['ext_search_seite_produkt_id']);
		}

		//Fall 3 Forum
		if($value['ext_search_seite_message_id'] >= 1) {
			$url = $this->get_forum_url($value['ext_search_seite_message_id'], $value['ext_search_seite_forum_id']);
		}

		//Fall 4 FAQ
		if($value['ext_search_seite_faq_id'] >= 1) {
			$url = $this->get_faq_url($value['ext_search_seite_faq_id']);
		}

		if($value['ext_search_seite_mv_id'] >= 1) {
			$url = $this->get_flex_url($value['ext_search_seite_mv_id'], $value['ext_search_seite_mv_content_id']);
		}
		return $url;
	}

	/**
	 * class_search_front::get_flex_url()
	 *
	 * @param integer $id
	 * @param int $content_id
	 * @return mixed $url
	 */
	private function get_flex_url($id = 0, $content_id = 0)
	{
		//Men�punkt Forum
		$sql = sprintf("SELECT menuid_id
                FROM %s
                WHERE menulinklang LIKE '%s'
                OR menulinklang LIKE '%s'", $this->cms->tbname['papoo_menu_language'], "%mv_id=" . $id . "%", "%mv_id="
			. $id . "");
		$result = $this->db->get_results($sql, ARRAY_A);
		$url = PAPOO_WEB_PFAD . "/plugin.php?menuid=" . $result['0']['menuid_id']
			. "&template=mv/templates/mv_show_front.html&mv_id=" . $id . "&mv_content_id=" . $content_id;

		return $url;
	}

	/**
	 * class_search_front::get_faq_url()
	 *
	 * @param int $id
	 *
	 * @return mixed $url
	 */
	function get_faq_url($id = 0)
	{
		//Men�punkt Forum
		$sql =
			sprintf("SELECT menuid_id FROM %s WHERE menulinklang LIKE '%s'", $this->cms->tbname['papoo_menu_language'], "%faq_front.html%");
		$result = $this->db->get_results($sql, ARRAY_A);

		$sql = sprintf("SELECT  cat_id FROM %s WHERE faq_id='%d'", $this->cms->tbname['papoo_faq_cat_link'], $id);
		$result2 = $this->db->get_results($sql, ARRAY_A);

		//"#c{$cat_data[$rowctx1].id}f{$faq_data[$rowctx1][$rowctx2].id}"

		$url = PAPOO_WEB_PFAD . "/plugin.php?menuid=" . $result['0']['menuid_id']
			. "&template=faq/templates/faq_front.html&faq_id=" . $id . "&faq_main_id=" . $result2['0']['cat_id']."#c".$result2['0']['cat_id']."f".$id;

		return $url;
	}

	/**
	 * class_search_front::get_forum_url()
	 *
	 * @param int $id
	 * @param int $forid
	 * @return string
	 */
	function get_forum_url($id = 0, $forid = 0)
	{
		//Men�punkt Forum
		$sql =
			sprintf("SELECT menuid_id FROM %s WHERE menulinklang='forum.php'", $this->cms->tbname['papoo_menu_language']);
		$result = $this->db->get_results($sql, ARRAY_A);

		$sql = sprintf("SELECT thema,rootid FROM %s WHERE msgid='%d'", $this->cms->tbname['papoo_message'], $id);
		$result2 = $this->db->get_results($sql, ARRAY_A);

		if($this->cms->mod_rewrite == "2") {
			$this->get_verzeichnis($result['0']['menuid_id']);

			$this->m_url = array_reverse($this->m_url);
			if(!empty($this->m_url)) {
				foreach($this->m_url as $urldat) {
					$urldat_1 .= $this->menu->urlencode($urldat) . "/";
				}
			}
			$header_url = $this->cms->webvar . $urldat_1 . "forumid-" . $forid . "-thread-" . $id . "-"
				. $this->menu->urlencode($result2['0']['thema']) . ".html";

			return $header_url;
		}
		else {
			$url =
				PAPOO_WEB_PFAD . "/forumthread.php?menuid=" . $result['0']['menuid_id'] . "&msgid=" . $id . "&rootid="
				. $result2['0']['rootid'];

			return $url;
		}
	}

	/**
	 * class_search_front::get_produkt_url()
	 *
	 * @param integer $id
	 * @return string
	 */
	function get_produkt_url($id = 0)
	{
		$sql = sprintf("SELECT
                kategorien_menu_id,
                produkte_lang_image_galerie,
                produkte_lang_produkt_surl
                FROM %s
                LEFT JOIN %s ON kategorien_id=produkte_kategorie_id
                LEFT JOIN %s ON produkte_produkt_id=produkte_lang_id
                WHERE produkte_lang_id='%d'
                AND produkte_lang_lang_id='%d'", $this->cms->tbname['plugin_shop_kategorien'], $this->cms->tbname['plugin_shop_lookup_kategorie_prod'], $this->cms->tbname['plugin_shop_produkte_lang'], $id, $this->cms->lang_id);
		$result = $this->db->get_results($sql, ARRAY_A);
		$images = explode(",", $result['0']['produkte_lang_image_galerie']);
		$this->image_produkt = $images['1'];

		if($this->cms->mod_rewrite == "2") {
			$this->get_verzeichnis($result['0']['kategorien_menu_id']);

			$this->m_url = array_reverse($this->m_url);
			if(!empty($this->m_url)) {
				foreach($this->m_url as $urldat) {
					$urldat_1 .= $this->menu->urlencode($urldat) . "/";
				}
			}
			$header_url = $urldat_1 . $this->menu->urlencode($result['0']['produkte_lang_produkt_surl']) . ".html";

			return $header_url;
		}
		else {
			$url = PAPOO_WEB_PFAD . "/shop.php?menuid=" . $result['0']['kategorien_menu_id'] . "&produkt_id=" . $id;

			return $url;
		}
	}

	/**
	 * class_search_front::get_artikel_url()
	 *
	 * @param integer $id
	 * @return string
	 */
	function get_artikel_url($id = 0)
	{
		//Daten des Artikels rausholen

		$sql = sprintf("SELECT
                url_header,
                lcat_id
                FROM %s
                LEFT JOIN %s ON lan_repore_id=lart_id
                WHERE lan_repore_id='%d'
                AND lang_id='%d'", $this->cms->tbname['papoo_language_article'], $this->cms->tbname['papoo_lookup_art_cat'], $id, $this->cms->lang_id);
		$result = $this->db->get_results($sql, ARRAY_A);
		if($this->cms->mod_free == "1") {
			return $result['0']['url_header'];
		} else {

			if($this->cms->mod_rewrite == "2") {
				$this->get_verzeichnis($result['0']['lcat_id']);

				$this->m_url = array_reverse($this->m_url);
				if(!empty($this->m_url)) {
					foreach($this->m_url as $urldat) {
						$urldat_1 .= $this->menu->urlencode($urldat) . "/";
					}
				}

				return $urldat_1 . $this->menu->urlencode($result['0']['url_header']) . ".html";
			} else {
				return PAPOO_WEB_PFAD . "/index.php?menuid=" . $result['0']['lcat_id'] . "&reporeid=" . $id;
			}
		}
	}

	/**
	 * Verzeichnisse rauskriegen bzw. Men�namen f�r mod_rewrite mit sprechurls
	 *
	 * @param string $menid
	 * @param string $search
	 * @return bool|void
	 */
	function get_verzeichnis($menid = "", $search = "")
	{
		#echo $this->checked->menuid;
		if(!empty($_GET['var1']) || !empty($search) || $this->cms->mod_surls == 1) {
			//             echo "HIER";
			//             print_r($this->menu->data_front_complete);
			$mendata = $this->menu->data_front_complete;
			foreach($mendata as $menuitems) {
				//             echo $menid;
				// aktuelle men�id finden
				if($menid != $menuitems['menuid']) {
					continue;
				}

				//                 echo $menid;
				$this->m_url[] = ($menuitems['url_menuname']);
				// Nicht oberste Ebene, neu aufrufen
				if($menuitems['untermenuzu'] != 0) {
					$this->get_verzeichnis($menuitems['untermenuzu']);
				}
				else {
					return true;
				}
			}
		}
	}

	/**
	 * extended_search_class::clean_suchwort()
	 *
	 * @return mixed $search
	 */
	function clean_suchwort()
	{
		$search = trim($this->checked->search);
		$remove = "<>'\"_%*\\";
		for($i = 0; $i < strlen($remove); $i++) {
			$search = str_replace(substr($remove, $i, 1), "", $search);
		}
		return utf8_decode($search);
	}
}
