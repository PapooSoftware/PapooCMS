<?php
/**
 * ######################################
 * # Papoo CMS                          #
 * # (c) Dr. Carsten Euwens 2008        #
 * # Authors: Stephan Bergmann,         #
 * #					 Carsten Euwens #
 * # http://www.papoo.de                #
 * #                                    #
 * ######################################
 * # PHP Version  >= 4.3                #
 * ######################################

 */
if (stristr( $_SERVER['PHP_SELF'],'cache_class.php')) {
	die('You are not allowed to see this page directly');
}

/**
 * Class cache_class
 */
class cache_class
{
	/** @var bool Cache aktiv für diese Seite */
	var $aktiv;
	/** @var string Adresse der aktuellen Seite */
	var $url;
	/** @var string Sprach-ID */
	var $lang_short;
	/** @var int Zeit in Sekunden wie lange die Seiten im Cache gehalten werden sollen */
	var $cache_zeit;

	/**
	 * cache_class constructor.
	 */
	function __construct()
	{
		// Initialisierung Variablen
		$this->aktiv = false;
		$this->url = "";
		$this->lang_short = "de"; // Default-Sprache deutsch
		$this->cache_zeit = 1; // Seiten 1 Stunde im Cache halten

		// SESSION starten Über session-Klasse
		$pfad = PAPOO_ABS_PFAD;
		require_once $pfad . "/lib/classes/session_class.php";

		// Prüfung ob Wartung aktiv ist.
		if (!defined("admin")) {
			$this->get_wartung_dat();
		}
		//Checken ob redirect wg. Sprachen
		$this->check_redirect_geo();

		//Leadtracker
		#$this->check_leadtracker();

		// Prüfung ob Caching aktiv ist.
		$this->get_cache_dat();
		//session_start();
		if ($this->cache_is_aktiv == 'ok') {
			$this->cache_check_aktiv();
			// Initialisierungs-Funktion aufrufen (wenn aktiv)
			if ($this->aktiv) {
				$this->cache_init();
			}
		}
	}

	/**
	 * cache_class::check_redirect_geo()
	 *
	 * @return void
	 */
	private function check_redirect_geo()
	{
		if (file_exists(PAPOO_ABS_PFAD . "/templates_c/redirect_cache.txt")) {
			$daten=unserialize(file_get_contents(PAPOO_ABS_PFAD."/templates_c/redirect_cache.txt"));

			if (!empty($_SESSION['langdata_front']['lang_id'])) {
				//Aktuelle Sprache
				$aktu_lang = $_SESSION['langdata_front']['lang_id'];
			}
			else {
				$aktu_lang = 1;
			}

			//IP ADresse
			$ip = $_SERVER['REMOTE_ADDR'];

			if (function_exists('geoip_country_code_by_name')) {
				//Land der IP Adresse
				$country = geoip_country_code_by_name($ip);

				//Durchgehen ob enthalten
				if (is_array($daten['ip'])) {
					foreach ($daten['ip'] as $key => $value) {
						if (
							$country == $value['geo_redirect_ip_countrycodeip']
							&& $aktu_lang != $value['geo_redirect_ip_sprache_erzwingen']
						) {
							$lang_short = $this->get_lang_short($value['geo_redirect_ip_sprache_erzwingen'], $daten);

							//Neu Laden mit SprachID...
							$url = $_SERVER['REQUEST_URI'];

							if (!stristr($url,'?')) {
								$location_url = $url . "?getlang=".$lang_short;
							}
							else {
								$location_url = $url . "&getlang=" . $lang_short;
							}

							if ($_SESSION['debug_stopallredirect']) {
								echo '<a href="' . $location_url . '">' .
									$this->content->template['plugin']['mv']['weiter'] . '</a>';
							}
							else {
								header("Location: $location_url");
							}
							exit;

						}
					}
				}
			}
			//Wenn bis jetzt noch nix passiert ist, Domain checken
			$domain_pur = $_SERVER['HTTP_HOST'];
			$domain = str_replace('www.', "", $domain_pur);

			if (is_array($daten['domain'])) {
				foreach ($daten['domain'] as $key => $value) {
					if (
						$domain == $value['geo_redirect_domain__domainname']
						&& $aktu_lang!=$value['geo_redirect_domain__sprache_die_erzwungen_werden_soll']
					) {
						$lang_short = $this->get_lang_short(
							$value['geo_redirect_domain__sprache_die_erzwungen_werden_soll'],
							$daten
						);

						//Neu Laden mit SprachID...
						$url=$_SERVER['REQUEST_URI'];

						if (!stristr($url,'?')) {
							$location_url = $url . "?getlang=" . $lang_short;
						}
						else {
							$location_url = $url . "&getlang=" . $lang_short;
						}

						if ($_SESSION['debug_stopallredirect']) {
							echo '<a href="' . $location_url . '">'
								. $this->content->template['plugin']['mv']['weiter'] . '</a>';
						}
						else {
							header("Location: $location_url");
						}
						exit;

					}
				}
			}
		}
	}

	/**
	 * cache_class::get_lang_short()
	 *
	 * @param mixed $langid
	 * @param mixed $daten
	 * @return boolean|mixed
	 */
	private function get_lang_short($langid, $daten)
	{
		if (is_array($daten['sprachen'])) {
			foreach ($daten['sprachen'] as $key => $language) {
				if ($langid == $language['lang_id']) {
					return $language['lang_short'];
				}
			}
		}
		return false;
	}

	/**
	 * cache_class::get_wartung_dat()
	 *
	 * @return void
	 */
	function get_wartung_dat()
	{
		$file = "/interna/templates_c/wartung.txt";
		$file_html = "/interna/templates_c/wartung.html";

		if (empty($_SESSION['sessionuserid'])) {
			$_SESSION['sessionuserid'] = array();
		}

		$file = PAPOO_ABS_PFAD . $file;
		$file_html = PAPOO_ABS_PFAD . $file_html;
		if (file_exists($file)) {
			//Inhalt einlesen
			$daten = implode("", file($file));
			$datar = explode(";", $daten);
			$useridars[] = 10;
			$useridars[] = 12;
			$useridars[] = 13;
			if ($datar['0'] == "ok" && !in_array($_SESSION['sessionuserid'], $useridars)) {
				$daten_html = implode("", file($file_html));
				print_r($daten_html);
				exit();
			}
		}
	}

	/**
	 * Cache Einstellungen auslesen
	 */
	function get_cache_dat()
	{
		$file = "/interna/templates_c/cache.txt";
		$this->cache_is_aktiv = '';

		$file = PAPOO_ABS_PFAD . $file;
		//Wenn die Datei nicht existiert, diese mit 777 anlegen
		if (!file_exists($file)) {
			touch($file);
		}
		else {
			//Inhalt einlesen
			$daten = @implode("", file($file));
			$datar = explode(";", $daten);
			if ($datar['0'] == "ok") {
				$this->cache_is_aktiv = 'ok';
				$this->aktiv = true;
			}
			else {
				$this->remove_files(PAPOO_ABS_PFAD . "/cache/", ".html");
			}
			if (!empty($datar['1']) and $datar['1'] >= 1) {
				//Cache Zeit setzen
				$this->cache_zeit = $datar['1'];
				$this->cache_max_zeit = $datar['2'] + $datar['1'];
				$this->cache_xmax_zeit = $datar['3'] + (1209600);

				//Falls die Cache Zeit rum ist.
				if ($this->cache_max_zeit < time()) {
					$file = "/interna/templates_c/cache.txt";
					$zeile = "ok;";
					$zeile .= $this->cache_zeit . ";" . time();
					$zeile .= ";" . $datar['3'];
					$datei = @fopen(PAPOO_ABS_PFAD . $file, "w+b");
					@fwrite($datei, $zeile);
					@fclose($datei);
					if ($this->cache_zeit < 10) {
						$this->remove_files(PAPOO_ABS_PFAD . "/cache/", ".html");
					}
				}
				if ($this->cache_xmax_zeit < time()) {
					$file = "/interna/templates_c/cache.txt";
					$zeile = "ok;";
					$zeile .= $this->cache_zeit . ";" . time();
					$zeile .= ";" . time();
					$datei = @fopen(PAPOO_ABS_PFAD . $file, "w+b");
					@fwrite($datei, $zeile);
					@fclose($datei);
					$this->remove_files(PAPOO_ABS_PFAD . "/cache/", ".html");
				}
			}
		}
	}

	/**
	 * @param string $pfad
	 * @param string $extension
	 */
	function remove_files($pfad = "", $extension = "")
	{
		if ($pfad && $extension) {
			$handle = opendir($pfad);
			while (true) {
				// START Loop control; DO NOT PUT THAT IN THE LOOP HEAD AGAIN
				$file = readdir($handle);
				if ($file === false) {
					break;
				}
				// END Loop control

				switch ($extension) {
				case ".*":
					if ($file != "." && $file != "..") @unlink($pfad . $file);
					break;

				default:
					if (stristr( $file,$extension)) @unlink($pfad . $file);
					break;
				}
			}
			closedir($handle);
		}
	}

	/**
	 * Rudimentaere Pruefung, wann der Cache aktiv sein soll.
	 */
	function cache_check_aktiv()
	{
		$searchengines = array("Validator", "Bobby", "phpwww", "Googlebot", "MSN");
		$isSearchEngine = 0;

		// Fuer jeden Eintrag durchgehen und Useragenten abfragen, der aufruft
		foreach ($searchengines as $searchEngine) {
			if (stristr( $_SERVER['HTTP_USER_AGENT'], $searchEngine)) {
				$isSearchEngine++;
				$this->aktiv = false;
			}
		}

		// Es ist keine Suchmaschine, dann  Session starten
		if ($isSearchEngine == 0 && empty($_SESSION)) {
			session_start();
		}

		// 1. Frontend-Test: Backend nicht cachen.
		if (defined('admin')) {
			$this->aktiv = false;
		}

		// 2. Template-Test: bei Seiten die nicht "index.html" bzw. "print.utf8.html" verwenden nicht cachen.
		global $template;
		if (!empty($_GET['template'])) {
			$template = $_GET['template'];
		}

		if (
			$template != "index.html"
			&& $template != "print.utf8.html"
			&& $template !=	"inhalt.html"
		) {
			$this->aktiv = false;
		}

		if ( !empty($_GET['search'])) {
			$this->aktiv = false;
		}
		if (
			!empty($_GET['var1'])
			&& !empty($template)
			&& (
				stristr($_GET['var1'],"forumthread")
				|| stristr($_GET['var1'],"forum")
				|| stristr($template,"mv")
			)
		) {
			$this->aktiv = false;
		}

		// 3. POST-Test: bei POST-Uebergaben nicht cachen.
		// 4. User-Test: bei angemeldeten Benutzern nicht cachen
		// 5. Sprachwechsel: bei Sprach-Wechsel nicht cachen
		if (!empty($_POST) || !empty($_SESSION['sessionuserid']) || !empty($_GET['getlang'])) {
			$this->aktiv = false;
		}
	}

	/**
	 * Initialisierung der Cache-Klasse
	 */
	function cache_init()
	{
		// 1. atuelle URL ermitteln
		$this->url = $this->make_clean_url($_SERVER['REQUEST_URI']);

		$cache_datei = PAPOO_ABS_PFAD . "/cache/" . $this->url . ".html";
		if (file_exists($cache_datei)) {
			if (
				((filemtime($cache_datei) + $this->cache_zeit) < $this->cache_max_zeit)
				or (@filesize($cache_datei) < 5000)
			) {
				unlink($cache_datei);
			}

		}

		// Wenn Cache-Datei existiert, diese ausgeben und Schluss.
		if (file_exists($cache_datei)) {
			// Spezialfall Cache
			$manipulateCacheFile =
				PAPOO_ABS_PFAD . "/plugins/papoo_shop/lib/shop_class_cache_manipulation.php";
			if (file_exists($manipulateCacheFile)) {
				require_once($manipulateCacheFile);

				$daten = manipulate_cache($cache_datei);
			}
			header(
				'Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT',
				true,
				200
			);
			header('Content-Type: text/html; charset=utf-8');
			if (empty($daten)) {
				$daten = file_get_contents($cache_datei);
			}
			$daten = $this->hightlight($daten);
			print_r($daten);
			exit();
		}
	}

	/**
	 * @param $daten
	 * @return string
	 */
	function hightlight($daten)
	{
		$result_dat = @file_get_contents(PAPOO_ABS_PFAD . "/interna/templates_c/highlight.txt");
		$js_files = "";
		$result = unserialize($result_dat);

		//Nodus der Markierung
		if ($result['0']['ext_search_ighlighting_rt'] == 1) {
			$exact = "exact";
		}
		if ($result['0']['ext_search_ighlighting_rt'] == 2) {
			$exact = "whole";
		}
		if ($result['0']['ext_search_ighlighting_rt'] == 3) {
			$exact = "partial";
		}

		if (empty($_SESSION['search_var'])) {
			$_SESSION['search_var'] = "";
		}

		IfNotSetNull($exact);
		if (is_array($_SESSION['search_var']) && empty($this->checked->search)) {
			//Suchvar erstellen aus Array
			$search_var = utf8_encode(implode(" ", $_SESSION['search_var']));

			//Js einbinden
			$js_files = $this->include_hl_js();

			//HL umsetzen
			$js_files .= '
				<style type=\'text/css\'>
					span.hilite {'.$result['0']['ext_search__fr_igh'].'}
				</style>
				
				<script type=\'text/javascript\'>
				jQuery(function(){
					var options  = {
						exact:"'.$exact.'",
						style_name_suffix:false,
						keys:"'.htmlspecialchars ($search_var,ENT_QUOTES,"UTF-8").'"
					};
					jQuery(document).SearchHighlight(options);
				});
				</script>
			';

			//Session zurücksetzen damit nicht die ganze Zeit gehighlighted wird
			unset($_SESSION['search_var']);
		}
		//Suche aus Google, dann davon HL
		elseif (
			$result['0']['ext_search_ighlighting_auch_bei_oogle_reffern_aktivieren'] == 1
			&& empty($this->checked->search)
			&& !is_array($_SESSION['search_var'])
		) {
			//Js einbinden
			$js_files=$this->include_hl_js();

			$js_files.='
			<style type=\'text/css\'>
				span.hilite {'.$result['0']['ext_search__fr_igh'].'}
			</style>
			
			<script type=\'text/javascript\'>
				jQuery(function(){
					var options  = {
						exact:"'.$exact.'",
						style_name_suffix:false,
					};
					jQuery(document).SearchHighlight(options);
				});
			</script>
			';
		}
		return $this->binde_js_ein($js_files, $daten);
	}

	/**
	 * class_search_create::binde_js_ein()
	 *
	 * @param string $jsFiles
	 * @param string $daten
	 *
	 * @return string
	 */
	function binde_js_ein($jsFiles, $daten)
	{
		$daten = preg_replace('/<\/head>/', $jsFiles.'</head>', $daten);
		return $daten;
	}

	/**
	 * extended_search_class::include_hl_js()
	 * @return string
	 */
	function include_hl_js()
	{
		return '<script type="text/javascript" src="'
			. PAPOO_WEB_PFAD
			. '/plugins/extended_search/js/jquery.searchhighlight.js"></script>';
	}

	/**
	 * urls von ? etc bereinigen
	 * @param $url
	 * @return string
	 */
	function make_clean_url($url)
	{
		$url = str_ireplace('?', "_", $url);
		$url = str_ireplace('&', "_", $url);
		$url = str_ireplace('/', "_", $url);
		$url = str_ireplace('=', "_", $url);
		$url = str_ireplace('.', "_", $url);

		// 2. Sprach-ID ermitteln
		if (!empty($_SESSION['langdata_front']['lang_short'])) {
			$this->lang_short = $_SESSION['langdata_front']['lang_short'];
		}

		if (!empty($_SESSION['style'])) {
			$this->style = $_SESSION['style'];
		}
		else {
			$this->style = @file_get_contents(PAPOO_ABS_PFAD."/interna/templates_c/css.txt");
		}

		return $url . $this->lang_short . "_" . $this->style;
	}

	/**
	 *
	 */
	function cache_speichern()
	{
		//Sonderfall - was dynamisches mit Spamschutz... nicht cachen
		global $output;

		if (stristr($output,"Spam-Schutz")) {
			$this->aktiv = false;
		}

		if ($this->aktiv) {
			$this->url_save = $this->make_clean_url($_SERVER['REQUEST_URI']);
			$cache_datei = $this->url_save . ".html";

			global $html;
			$output = $html->make_tidy_front($output);

			$cache_daten = $output;
			global $menu;
			global $artikel;
			if ($menu->is_aktiv == 1 && $artikel->no_cache != 1 && !stristr( $this->url_save,"PHPS")) {
				$datei = @fopen(PAPOO_ABS_PFAD . "/cache/" . $cache_datei, "w+b");
				@fwrite($datei, $cache_daten);
				@fclose($datei);
			}
		}
	}

	/**
	 * @param $zeit
	 * @return false|string $zeit in dem von der Cache-Klasse verwendete Format YYYYMMDDhhmmss
	 */
	function cache_zeit_format($zeit)
	{
		return date("YmdHis", $zeit);
	}

}
$cache = new cache_class();
