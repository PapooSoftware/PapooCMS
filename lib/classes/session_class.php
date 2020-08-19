<?php
// #####################################
// # CMS Papoo                         #
// # (c) Carsten Euwens 2008           #
// # Authors: Carsten Euwens           #
// # http://www.papoo.de               #
// # Internet                          #
// #####################################
// # PHP Version 4.2                   #
// #####################################

/**
 * Diese Klasse initialisert alle übergebenen Sessionvariablen und
 * wandelt diese in Eigenschaften der Klasse session um.
 *
 * Ruft sich selber hier unten auf!
 *
 * Class session_class
 */
class session_class
{

	/**
	 * session_class constructor.
	 */
	function __construct()
	{
		$this->starte_session();
	}

	/**
	 * Session starten
	 */
	function starte_session()
	{
		// Auslogvariable einbinden
		if (!empty($_GET['logoff']) || stristr($_SERVER['REQUEST_URI'],"logoff=1")) {
			$logoff = true;
		}
		else {
			$logoff = false;
		}

		// Suchmaschinen Array erstellen ( FIXME: erweitern
		$searchengines = array ("Validator", "Bobby", "phpwww", "Googlebot", "MSN");
		$is_search_engine = 0;
		// Für jeden Eintrag durchgehen und Useragenten abfragen der aufruft
		foreach ($searchengines as $key => $val) {
			if (stristr($_SERVER['HTTP_USER_AGENT'],$val)) {
				$is_search_engine ++;
				$this->noload="ok";
			}
		}

		$expire=time()+2592000;
		if (stristr($_SERVER['REQUEST_URI'],"gclid")) {
			$gclid=explode("gclid=",$_SERVER['REQUEST_URI']);
			setcookie("gclid",trim($gclid['1']),$expire,"/");
		}

		// Es ist keine Suchmaschine, dann  Session starten
		if ($is_search_engine == 0) {
			//Name der Session
			session_name('papoo_session_'.substr(md5(PAPOO_ABS_PFAD), 0, 4));
			// Session-Lebensdauer
			$cookie_lifetime = 432000;
			// Cookie-Pfad aus PAPOO_WEB_PFAD bestimmen
			$cookie_base_path = str_replace('//', '/', PAPOO_WEB_PFAD.'/');
			// Cookie-Domain setzen, Cookie mit und ohne www. gültig machen. Dabei ebenfalls lokale Installationen beachten.
			$cookie_domain = str_replace('.www.', '.', (strpos($_SERVER['SERVER_NAME'], '.') === false ? '' : '.'.$_SERVER['SERVER_NAME']));

			if (stristr($_SERVER['REQUEST_URI'],"https")) {
				// Nur über HTTPS senden. Wenn Website nur HTTPS nutzt anschalten!
				$cookie_secure = FALSE;
			}
			else {
				// Nur über HTTPS senden. Wenn Website nur HTTPS nutzt anschalten!
				$cookie_secure = FALSE;
			}


			// Zugriff via Javascript verweigern
			$cookie_httponly = TRUE;

			session_set_cookie_params($cookie_lifetime, $cookie_base_path, $cookie_domain, $cookie_secure, $cookie_httponly);
			@session_start();

			//Aktivieren wenn kein Efa Fontsize genutzt wird...
			//session_regenerate_id() ;
			#$_SESSION=array();
			// Check auf SESSION-Hijacking
			if (empty($_SERVER["HTTP_USER_AGENT"])) {
				$_SERVER["HTTP_USER_AGENT"]="";
			}
			// "eindeutige" ID setzten um SESSION-Übernahme zu testen
			if (empty($_SESSION['hijack_id'])) {
				$_SESSION['hijack_id'] = md5($_SERVER["HTTP_USER_AGENT"]);
				//echo '$_SESSION[hijack_id] initialisiert: '.$_SESSION['hijack_id']."<br />\n";
			}

			// Test ob eine SESSION-Übernahme versucht wird
			if ($_SESSION['hijack_id'] != md5($_SERVER["HTTP_USER_AGENT"])) {
				$_SESSION = array();
				$_SESSION['hijacked'] = true;

				require_once(PAPOO_ABS_PFAD."/lib/classes/diverse_class.php");
				$diverse = new diverse_class();
				$diverse->check_log();
				$diverse->log_hijack();

				$location_url = $_SERVER['PHP_SELF'];
				header("Location: $location_url");

				exit;
			}

			// SESSION die bereits einmal versucht wurde zu Übernehmen nicht mehr verwenden, sondern neue SESSION mit neuer ID starten
			if (!empty($_SESSION['hijacked'])) {
				//echo ".. SESSION wurde gestohlen: ".session_id()."; HiJack-ID: ".$_SESSION['hijack_id']."; HiJacked: ".$_SESSION['hijacked']."<br />\n"; 

				if (isset($_COOKIE[session_name()])) {
					setcookie(session_name(), '', time()-42000, '/');
				}
				//Session löschen, sonst wird ständig neu geladen
				session_destroy();

				$location_url = $_SERVER['PHP_SELF'];
				header("Location: $location_url");

				exit;
			}
			// Ende Check SESSION-Hijacking

			if (empty($_SESSION['dbp'])) {
				$_SESSION['dbp']=array();
			}

			// Überprüfen ob Cookies aktiviert sind
			if (!isset ($_SESSION['session_ok'])) {
				$_SESSION['session_ok'] = "ok";
				// Session ist nicht aktiv
				$this->sessionok = "no";
			}
			else {
				// Session ist aktiv!
				$this->sessionok = "ok";
			}

			// "session_unregister" Session beenden und Variablen löschen, danach Seite neu laden
			if ($logoff) {
				unset ($_SESSION['sessionusername']);
				//unset ($_SESSION['style']);
				//unset ($_SESSION['sessionuser_style']);
				unset ($_SESSION['sessionuserid']);
				unset ($_SESSION['sessionhash']);
				unset ($_SESSION['sessionboard']);
				unset($_SESSION['metadaten']);
				unset ($_SESSION);
				session_destroy();
				$_SESSION['dbp']=array();
				//header("Location: " . $_SERVER['PHP_SELF']);
				$location_url = $_SERVER['PHP_SELF'];
				if (!empty($_SESSION['debug_stopallredirect'])) {
					echo '<a href="'.$location_url.'">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit ();
			}
			if (isset ($_SESSION['geschickt']) && !empty ($_SESSION['geschickt'])) {
				$this->geschickt = $_SESSION['geschickt'];
				$_SESSION['geschickt']++;
				if ($_SESSION['geschickt'] > 3) {
					unset ($_SESSION['geschickt']);
				}
			}
		}
	}
}

$session = new session_class();