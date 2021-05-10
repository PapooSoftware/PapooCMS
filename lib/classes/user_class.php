<?php
/**
 * #####################################
 * # Papoo CMS                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version > 4.2                 #
 * #####################################
 */

if (stristr($_SERVER['PHP_SELF'], 'user_class.php')) {
	die('You are not allowed to see this page directly');
}

/**
 * Diese Klasse initialisert einen Benutzer, mit do_einlogg werden die Daten mit der Datenbank abgeglichen.
 *
 * Class user_class
 */
class user_class
{
	/** @var string Der Username */
	var $username = "";
	/** @var string Die Userid */
	var $userid = "";
	/** @var string Die Gruppenid */
	var $gruppenid = "";
	/** @var string Das Passwort */
	var $password = "";
	/** @var string Zustand des Einloggens */
	var $log_zustand = "";
	/** @var string Account bearbeiten */
	var $manageprofil = "";
	/** @var string noch nicht fertig */
	var $nichtfertig = "";
	/** @var string Falscher Login */
	var $logfalse = "";
	/** @var string Erster Einlogg */
	var $loginfirst = "";
	/** @var string Zugriff auf den Admin Bereich */
	var $internokay = "";
	/** @var string falsch eingeloggt */
	var $loggedin_false = "";
	/** @var int Sonderfall DZVHAE */
	var $dzvhae = 0;
	/** @var null Hat User Administrator-Rechte */
	private $hasAdminRights = null;
	// LDAP
	//var $ldap_server = 'ldap://127.0.0.1/';
	//var $ldap_basedn = 'dc=papoo,dc=de';
	//var $ldap_version = 3;
	//var $ldap_default_groups = array(10,12);

	/**
	 * Konstruktor
	 */
	function __construct()
	{
		// cms Klasse einbinden
		global $cms;
		$this->cms = &$cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = &$db;
		// Modul-Klasse
		global $module;
		$this->module = &$module;
		// inhalt Klasse einbinden
		global $content;
		$this->content = &$content;
		// checked Klasse einbinde
		global $checked;
		$this->checked = &$checked;
		// diverse
		global $diverse;
		$this->diverse = &$diverse;
		// Mailklasse
		global $mail_it;
		$this->mail_it = &$mail_it;
		// Spamschutz-Klasse einbinden
		global $spamschutz;
		$this->spamschutz = &$spamschutz;
		// BBCode-Editor einbinden
		global $bbcode;
		$this->bbcode = &$bbcode;
		// Blacklist-Klasse
		global $blacklist;
		$this->blacklist = &$blacklist;
		// Mitgliederverwaltungs-Klasse
		global $mv;
		$this->mv = &$mv;

		$this->check_user();

		IfNotSetNull($this->content->template['easyedit']);
		IfNotSetNull($this->content->template['logfalse']);
	}

	/**
	 * @param string $username
	 * @return bool
	 */
	public static function exists(string $username): bool
	{
		/** @var cms $cms */
		global $cms;
		/** @var ezSQL_mysqli $db */
		global $db;

		return (bool)$db->query(
			"SELECT * FROM {$cms->tbname['papoo_user']} WHERE username LIKE '{$db->escape($username)}'"
		);
	}

	/**
	 * @param string $username
	 * @return string
	 */
	public static function makeUniqueUsername(string $username): string
	{
		for ($i = 0; self::exists($username.($suffix = $i ?: '')); $i++);
		return $username.$suffix;
	}

	/**
	 * user_class::get_groups
	 * Holt alle dem User zugehoerigen Gruppen und gibt diese als Array zurueck.
	 * @param bool $cached If false, a new query is sent to the database server, thus receiving fresh data.
	 * @return array Gibt ein Array zurueck, das alle Gruppen enthaelt, denen der User zugeordnet ist.
	 */
	public function get_groups($cached = true)
	{
		if (is_array($this->gruppenid) === false || $cached === false) {
			$this->gruppenid = array();
			// Verifizierter Nutzer eingeloggt?
			if (is_numeric($this->userid) && $this->userid > 0) {
				// Hole alle GruppenIDs von Gruppen, denen der User zugeteilt ist
				$sql = sprintf("SELECT gruppenid FROM `%s` WHERE userid = %d",
					$this->cms->tbname["papoo_lookup_ug"],
					(int)$this->userid
				);
				$result = $this->db->get_results($sql, ARRAY_A);

				if (is_array($result)) {
					foreach ($result as $gruppe) {
						$this->gruppenid[(int)$gruppe["gruppenid"]] = $gruppe;
					}
				}
			}
			$this->content->template["user_groups"] = $this->gruppenid;
		}
		return $this->gruppenid;
	}

	/**
	 * Einloggvorgang duchziehen
	 * @param $username
	 * @param $password
	 */
	function do_einlogg($username, $password)
	{
		// Immer klein casten, damit z.B. "Root", oder "ROOT" bei der Eingabe nicht Probleme verursacht, obwohl das funktionieren sollte.
		$username = strtolower($username);

		global $webverzeichnis;
		$server = $_SERVER['SERVER_NAME'] . $webverzeichnis;

		// wenn der Sessionhash gegeben ist, diesen checken und keine Datenbankabfrage
		if (!empty ($_SESSION['sessionhash'])) {
			// unique für jede Seite
			// Testhash herstellen
			$testhash = hash('sha256', $server . trim($username) . $_SESSION['sessionuserid'] . session_id());

			// wenn der testhash dem alten Hash entspricht ist nichts geändert worden und Username und Passwort sind ok
			if ($_SESSION['sessionhash'] == $testhash) {
				// Eigenschaften zuweisen
				$this->userid = $_SESSION['sessionuserid'];
				$this->username = $username;
				$this->content->template['username'] = $username;

				$this->get_groups();

				if (empty ($_SESSION['sessioneditor'])) {
					$_SESSION['sessioneditor'] = "";
				}
				$this->editor = $_SESSION['sessioneditor'];
				//Screenreader Quicktags Extension
				if (empty($_SESSION['user_club_stufe'])) {
					$_SESSION['user_club_stufe'] = "";
				}
				$this->user_club_stufe = $_SESSION['user_club_stufe'];

				if (!isset($_SESSION['user_extern_id'])) {
					$_SESSION['user_extern_id'] = null;
				}

				$this->board = $_SESSION['board'];
				$this->content->template['user_extern_id'] = $_SESSION['user_extern_id'];
				//$this->user_style = $_SESSION['sessionuser_style'];

				$this->content->template['user_content_tree_show_all'] = $_SESSION['user_content_tree_show_all'];

				// Ausloggvariable löschen
				unset ($_SESSION['logoff']);
				unset ($_SESSION['logfalse']);

				// Einloggzustand übergeben an template
				$this->content->template['loggedin'] = "user_ok";
				//TINY MMCE Config einstellen
				$this->get_user_tiny();

			} // Etwas ist geändert worden, dann rausschmeisen
			else {
				$this->userid = 0;
				$this->username = 0;
				$this->board = $this->cms->forum_board;
				// Username und Passwort an Session übergeben
				$_SESSION['sessionusername'] = "";
				$_SESSION['user_club_stufe'] = "";
				$_SESSION['sessionuserid'] = "";
				$_SESSION['sessionusergruppenid'] = "";
				$_SESSION['sessionhash'] = "";
				$_SESSION['sessioneditor'] = "";
				$_SESSION['board'] = "";
				$_SESSION['user_content_tree_show_all'] = "";
				unset ($_SESSION);
				$_SESSION['dbp'] = array();
				//$_SESSION['sessionuser_style'] = "";
				// Kein User, für falsches Login Formular anzeigen
				$this->content->template['loggedin_false'] = "user_wrong";
				$this->content->template['loggedin'] = "";
				$_SESSION['logfalse'] = "aaaa";
			}
		}
		else {
			/**
			 * Echte normale Abfrage
			 */
			$username_exist = "0";
			$sperre = "";

			// Datenbankabfrage formulieren
			$selectuser = "SELECT userid, wie_oft_login, zeitsperre FROM " . $this->cms->papoo_user . " WHERE username COLLATE utf8_bin =  '" . ($this->db->escape($username)) . "'  AND active='1' ";

			$resultuser_ok2 = $resultuser_ok = $this->db->get_results($selectuser);
			#print_r($resultuser_ok2);
			#exit();
			if (is_array($resultuser_ok) and count($resultuser_ok) >= 1) {
				$username_exist = "1";
			}
			if (is_array($resultuser_ok2)) {
				foreach ($resultuser_ok2 as $row) {
					//Zeitsperre von 10 Minuten noch nicht abgelaufen
					if ($row->zeitsperre != 0) {
						if (time() - 600 < $row->zeitsperre) {
							$sperre = $this->content->template['sperre'] = "gesperrt";
						}
					}

				}
			}

			// Datenbankabfrage formulieren - User herausholen, ohne Passwort-Check
			$selectuser = sprintf("SELECT * FROM `%s` WHERE `username` = '%s' AND `active` = 1 LIMIT 1",
				$this->cms->papoo_user,
				$this->db->escape($username)
			);
			$resultuser = $this->db->get_row($selectuser, ARRAY_A);

			// Passwort verifizieren - ist nicht mehr durch MySQL machbar (Hashing-Algorithmus)
			$password_verified = false;
			if ($resultuser !== null && $username != "jeder" && empty($sperre)) {
				// Hash ist crypt-kompatibel
				if (substr($resultuser["password"], 0, 1) === '$' && $this->diverse->verify_password($password,
						$resultuser["password"])
				) {
					$password_verified = true;
				}
				else {
					// Passwort neu hashen, sofern es immer noch als MD5 Hash vorliegt
					if ($resultuser["password"] === md5($password)) {
						$this->db->query(sprintf("UPDATE `%s` SET `password` = '%s' WHERE `userid` = %d",
							$this->cms->tbname["papoo_user"],
							$this->db->escape($this->diverse->hash_password($password)),
							$resultuser["userid"]
						));
						$password_verified = true;
					}
				}
			}

			// es existiert ein User ###
			if ($password_verified) {
				//Zähler auf null setzen und Eintrag in Datenbank für Login legen
				$this->db->query(sprintf(
					"UPDATE `%s` 
								SET `wie_oft_login` = '0', `zeitsperre` = '0', `user_last_login` = '%d' 
							WHERE `userid` = '%d'",
					$this->cms->papoo_user,
					time(),
					$resultuser["userid"]
				));

				// Eigenschaften zuweisen
				$this->userid = $resultuser["userid"];
				$this->username = strtolower($resultuser["username"]);
				$this->user_club_stufe = $resultuser["user_club_stufe"];
				$this->editor = $resultuser["editor"];
				if ($this->editor == 0) {
					$this->editor = 3;
				}
				$this->board = $resultuser["board"];
				$this->user_style = $resultuser["user_style_id"];

				$this->get_groups();

				// Ausloggvariable löschen
				unset ($_SESSION['logoff']);
				unset ($_SESSION['logfalse']);
				unset ($_SESSION['meta_gruppe_id']);

				// Einloggzustand übergeben an template
				$userok = $this->content->template['loggedin'] = "user_ok";

				//TINY MMCE Config einstellen
				$this->get_user_tiny();

				// Hashwert erstellen
				$hash = hash('sha256', $server . trim($username) . $this->userid . session_id());

				// Username und Passwort an Session übergeben
				$_SESSION['sessionusername'] = strtolower($resultuser["username"]);
				$_SESSION['sessionuserid'] = $resultuser["userid"];
				$_SESSION['sessionusergruppenid'] = $this->gruppenid;
				$_SESSION['user_club_stufe'] = $resultuser["user_club_stufe"];
				$_SESSION['sessionhash'] = $hash;
				$_SESSION['sessioneditor'] = $this->editor;
				$_SESSION['board'] = $this->board;
				$_SESSION['user_content_tree_show_all'] = $resultuser["user_content_tree_show_all"];
				//$_SESSION['style'] = $this->user_style;

				$this->content->template['username'] = $username;

				// User-Style übernehmen
				if (!empty($this->user_style) AND ($this->user_style != $this->cms->style_id)) {
					//echo ".. User-Style initialisieren<br />\n";
					$this->cms->make_style($this->user_style);
					if (!defined("admin")) {
						global $module;
						$module->make_module();
					}
				}

				// Sprach-Daten (wenn vorhanden) an die CMS-Klasse übermitteln
				// Frontend
				if (!empty($resultuser["user_lang_front"])) {
					$sprache = $this->cms->lang_get($resultuser["user_lang_front"]);
					$this->cms->lang_save("FRONT", $sprache);
					// CMS-Daten neu einlesen, da sonst Seitentitel etc. falsch ist.
					$this->cms->data();
				}
				// Backend
				IfNotSetNull($this->checked->language);
				$sprache = $this->cms->lang_get($this->checked->language, "back");

				$this->cms->lang_save("BACK", $sprache);

				$this->log_user($username, $userok, $username_exist);

			} // etwas stimmt nicht Username oder Passwort falsch oder irgendetwas anderes stimmt nicht
			else {
				// LDAP-Synchronisierung
				if (!empty($this->ldap_server)) {
					$result = $this->check_ldap($username, $password);

					if ($result === false) {
						//Username war ok, aber Passwort nicht
						$this->content->template['loggedin_false_pass'] = "1";
						$this->password = 0;
						$this->user_club_stufe = 0;
					}
					elseif ($result === true) {
						//Username und Passwort ok
						$this->checked_extern = "ok";
						$this->log_user($username, 'user_ok', 1);
					}
				}
				else {
					// Externe Synchronisierung
					$this->check_extern($username, $password);
					IfNotSetNull($this->checked_extern);
					if ($this->checked_extern != "ok") {
						//Username war ok, aber Passwort nicht
						if ($username_exist == "1") {
							$zeit = "";
							foreach ($resultuser_ok as $row) {
								//Wenn es schon mehr 4 Einloggversuche gab, sperren
								if ($row->wie_oft_login > 4) {
									$zeit = time();
									$sqlx = "UPDATE " . $this->cms->papoo_user . " SET zeitsperre='$zeit', wie_oft_login='0' WHERE username = BINARY '" . ($this->db->escape($username)) . "'";
									$this->db->query($sqlx);
								}
							}
							//weniger als 4 Versuche, hochzählen
							if (empty ($zeit)) {
								$sqlx = "UPDATE " . $this->cms->papoo_user . " SET wie_oft_login=wie_oft_login+1 WHERE username = BINARY '" . ($this->db->escape($username)) . "'";
								$this->db->query($sqlx);
							}
							$this->content->template['loggedin_false_pass'] = "1";
						}
						$this->userid = 0;
						$this->username = 0;
						$this->password = 0;
						$this->user_club_stufe = 0;
						$_SESSION['dbp'] = array();
						$this->board = $this->cms->forum_board;
						$_SESSION['sessionusername'] = "";
						// Kein User, für falsches Login Formular anzeigen
						$userok = $this->content->template['loggedin_false'] = "user_wrong" . "--";
						$this->content->template['loggedin'] = "";
						$_SESSION['logfalse'] = "aaaa";

						$this->log_user($username, $userok, $username_exist);
						// Wenn im Frontend, dann Seite neu laden, da sonst Probs mit Menü
						if (!defined("admin")) {
							//header("Location:".$_SERVER['PHP_SELF'] );
							$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&reporeid=" . $this->checked->reporeid . "&template=" . $this->checked->template . "&false=" . $this->content->template['loggedin_false_pass'] . "&sperre=" . $sperre;
							if ($_SESSION['debug_stopallredirect']) {
								echo '<a href="' . $location_url . '">Weiter</a>';
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
		if (empty($_SESSION['dbp']['papoo_user_club_stufe'])) {
			$sql = sprintf("SELECT user_club_stufe FROM %s WHERE username='%s'",
				$this->cms->papoo_user,
				$this->db->escape($this->username)
			);
			$user_club_stufe = $this->db->get_var($sql);
			if (empty($user_club_stufe)) {
				$user_club_stufe = 0;
			}
			$_SESSION['dbp']['papoo_user_club_stufe'] = $user_club_stufe;
		}
		else {
			$user_club_stufe = $_SESSION['dbp']['papoo_user_club_stufe'];
		}

		if ($this->dzvhae == 1 AND $this->cms->tbname['papoo_mv']) {
			$sql = sprintf("SELECT mv_id FROM %s
										WHERE mv_art = 2",
				$this->cms->tbname['papoo_mv']
			);
			$mv_id = $this->db->get_var($sql);
			if ($mv_id) {
				$sql = sprintf("SELECT mv_content_id
											FROM %s 
											WHERE mv_content_userid = '%d' 
											LIMIT 1",
					$this->cms->tbname['papoo_mv_content_' . $mv_id . "_search_1"],
					$this->userid
				);
				$var = $this->db->get_var($sql);
				$this->content->template['user_dzvhae_id'] = $var;
			}
		}

		$this->content->template['user_club_stufe'] = $user_club_stufe;

	}

	/**
	 *
	 */
	function get_user_tiny()
	{
		#if (defined(admmin))
		{
			//TINy COnf raussuchen -> nach kleinster Gruppenid
			$sql = sprintf("SELECT MIN(gruppenid) AS mingrups,
    													user_konfiguration_des_tinymce FROM 
    													%s LEFT JOIN %s ON gruppeid=gruppenid
    													WHERE userid='%d' AND gruppeid<> 10
    													GROUP BY gruppeid
    													ORDER BY gruppenid ASC
    													LIMIT 0,1",
				$this->cms->tbname['papoo_gruppe'],
				$this->cms->tbname['papoo_lookup_ug'],
				$this->userid);
			$result_tiny = $this->db->get_results($sql, ARRAY_A);
			IfNotSetNull($result_tiny['0']);
			$this->content->template['user_tiny_conf'] = "nobr:" . $result_tiny['0']['user_konfiguration_des_tinymce'];
		}
	}

	/**
	 * @param $username
	 * @param $userok
	 * @param $username_exist
	 */
	function log_user($username, $userok, $username_exist)
	{
		//Einlogvorgänge loggen
		$logdetails = array();
		//($this->db->escape($username)), $userok
		array_push($logdetails, array(
			'typ' => "login",
			'username' => $this->db->escape($username),
			'userok' => $userok,
			'exist' => $username_exist,
		));
		$this->diverse->do_log("login", $logdetails);
	}

	/**
	 * Den Spezialfall LDAP-Auflösung durchführen
	 *
	 * @param $username
	 * @param $password
	 * @return bool|null
	 */
	function check_ldap($username, $password)
	{
		// Zur Sicherheit alles außer Buchstaben, Zahlen, Unter- und
		// Bindestrichen und Leerzeichen aus dem Benutzernamen entfernen.
		$username = preg_replace('[^A-Za-z0-9_ ÄÖÜäöüß-]', '', $username);

		$login_result = null;

		// Verbindung zum Server herstellen
		$connection = ldap_connect($this->ldap_server);
		if (!empty($this->ldap_version)) {
			ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_version);
		}

		// Anonym einloggen
		if (ldap_bind($connection)) {
			$user_search_array = array('*', 'ou', 'cn', 'givenName', 'sn', 'mail', 'passwordRetryCount');
			$ref = @ldap_search($connection, $this->ldap_basedn, '(uid=' . $username . ')', $user_search_array);
			if ($ref !== false) {
				$result = ldap_get_entries($connection, $ref);
				if (isset($result[0]) and isset($result[0]['dn'])) {
					// Anmelden
					$dn = $result[0]['dn'];
					$login_result = (bool)@ldap_bind($connection, $dn, $password);
				}
				else {
					// Benutzer existiert nicht (mehr). Deaktivieren falls existent.
					$this->ldap_disable_user($username);
				}
			}
		}

		// Wenn erfolgreich, Benutzerdaten erneut holen und in Datenbank speichern
		if ($login_result) {
			$ref = ldap_search($connection, $this->ldap_basedn, '(uid=' . $username . ')');
			$result = ldap_get_entries($connection, $ref);
			$this->ldap_update_user($username, $password, $result[0]);
		}

		// Und Verbindung wieder trennen
		ldap_unbind($connection);

		// Wenn Login OK
		if ($login_result) {
			// Benutzerdaten wieder aus Datenbank holen
			$sql = sprintf("SELECT * FROM %s WHERE username='%s' LIMIT 1",
				$this->cms->tbname['papoo_user'],
				$this->db->escape($username)
			);
			$row = $this->db->get_results($sql);
			$row = $row[0];

			// Benutzer in CMS einloggen
			$this->userid = $row->userid;
			$this->username = strtolower($row->username);
			$this->password = $password;
			$this->user_club_stufe = $row->user_club_stufe;
			$this->editor = $row->editor;
			if ($this->editor == 0) {
				$this->editor = 3;
			}
			$this->board = $row->board;
			$this->user_style = $row->user_style_id;
			$this->gruppenid = array(array('gruppenid' => 10), array('gruppenid' => 12));

			// Ausloggvariable löschen
			unset ($_SESSION['logoff']);
			unset ($_SESSION['logfalse']);

			// Einloggzustand übergeben an template
			$this->content->template['loggedin'] = "user_ok";
			$this->content->template['bbsuser'] = "user_ok";

			// Hashwert erstellen
			global $webverzeichnis;
			$hash = hash('sha256',
				$_SERVER['SERVER_NAME'] . $webverzeichnis . trim($this->username) . $this->userid . session_id());

			// Username, etc. an Session übergeben
			$_SESSION['user_extern_id'] = $row->user_bbsid;
			$_SESSION['sessionusername'] = strtolower($row->username);
			$_SESSION['sessionuserid'] = $row->userid;
			$_SESSION['sessionusergruppenid'] = $this->gruppenid;
			$_SESSION['sessionuserbbsid'] = $row->user_bbsid;
			$_SESSION['user_club_stufe'] = ($row->user_club_stufe) ? $row->user_club_stufe : "0";
			$_SESSION['sessionhash'] = $hash;
			$_SESSION['sessioneditor'] = $this->editor;
			$_SESSION['sessionboard'] = $this->board;
			$_SESSION['sessionuser_style'] = $this->user_style;
			$_SESSION['board'] = $this->board;
			$_SESSION['user_content_tree_show_all'] = $row->user_content_tree_show_all;

			// Sprach-Daten (wenn vorhanden) an die CMS-Klasse übermitteln
			// Frontend
			if (!empty($row->user_lang_front)) {
				$sprache = $this->cms->lang_get($row->user_lang_front);
				$this->cms->lang_save("FRONT", $sprache);
				// CMS-Daten neu einlesen, da sonst Seitentitel etc. falsch ist.
				$this->cms->data();
			}
			if (empty($this->checked->language)) {
				$this->checked->language = "de";
			}
			$sprache = $this->cms->lang_get($this->checked->language, "back");
			$this->cms->lang_save("BACK", $sprache);
		}
		return $login_result;
	}

	/**
	 * @param $username
	 */
	function ldap_disable_user($username)
	{
		// User-ID holen
		$sql = sprintf("SELECT userid FROM %s WHERE username='%s' AND user_bbsid LIKE '%%ldap:%%'",
			$this->cms->tbname['papoo_user'],
			$this->db->escape($username)
		);
		$userid_lokal = $this->db->get_var($sql);

		if ($userid_lokal) {
			$sql = sprintf("UPDATE %s SET active=0 WHERE userid=%d",
				$this->cms->tbname['papoo_user'],
				$this->db->escape($userid_lokal)
			);
			$this->db->query($sql);
		}
	}

	/**
	 * @param $username
	 * @param $password
	 * @param $userdata
	 */
	function ldap_update_user($username, $password, $userdata)
	{
		$bbsid = 'ldap:' . $userdata['dn'];
		// User-ID holen
		$sql = sprintf("SELECT userid FROM %s WHERE username='%s' LIMIT 1",
			$this->cms->tbname['papoo_user'],
			$this->db->escape($username)
		);
		$userid_lokal = $this->db->get_var($sql);

		// Wenn noch nicht existent neu eintragen
		if (!$userid_lokal) {
			// Gruppenzuordnung vorbereiten
			$gruppen = $this->ldap_default_groups;
			if ($gruppen) {
				$gruppen_str = 'g' . implode(',g', $gruppen) . ',';
			}
			else {
				$gruppen = array(10);
				$gruppen_str = 'g10,';
			}

			// Benutzer anlegen
			$sql = sprintf("INSERT INTO %s SET
							user_bbsid='%s', 
							username='%s', 
							email='%s', 
							password='%s', 
							active=1,
							board=1,
							user_plz='',
							gruppenid='%s',
							zeitstempel='%s'",
				$this->cms->tbname['papoo_user'],
				$this->db->escape($bbsid),
				$this->db->escape($username),
				$this->db->escape($userdata['mail'][0]),
				$this->db->escape('!'),
				$gruppen_str,
				date("Y.m.d G:i:s")
			);
			$this->db->query($sql);
			$userid_lokal = $this->db->insert_id;

			// Gruppenzuordnung anlegen
			foreach ($gruppen as $gruppenid) {
				$sql = sprintf("INSERT INTO %s SET userid='%d', gruppenid='%d'",
					$this->cms->tbname['papoo_lookup_ug'],
					$userid_lokal,
					$gruppenid
				);
				$this->db->query($sql);
			}
		}

		$givenname = (isset($userdata['givenName'])) ? $userdata['givenName'][0] : $userdata['givenname'][0];
		$retrycount = (isset($userdata['passwordRetryCount'])) ? $userdata['passwordRetryCount'][0] : $userdata['passwordretrycount'][0];
		// Daten aus LDAP einpflegen
		$sql = sprintf("UPDATE %s SET
						user_bbsid='%s',
						email='%s',
						user_vorname='%s',
						user_nachname='%s',
						password='%s', 
						wie_oft_login=%d,
						active=1, 
						zeitsperre=0,
						user_last_login=%d
					WHERE username='%s'",
			$this->cms->tbname['papoo_user'],
			$this->db->escape($bbsid),
			$this->db->escape($userdata['mail'][0]),
			$this->db->escape($givenname),
			$this->db->escape($userdata['sn'][0]),
			$this->db->escape('!'),
			(int)$retrycount,
			time(),
			$this->db->escape($userdata['uid'][0])
		);
		$this->db->query($sql);
	}

	/**
	 * Den Spezialfall Externe Auflösung durchführen
	 *
	 * @param $username
	 * @param $password
	 */
	function check_extern($username, $password)
	{
		if (!empty($this->cms->tbname['papoo_gruppen_sync_daten'])) {
			$sql = sprintf("SELECT * FROM %s",
				$this->cms->tbname['papoo_gruppen_sync_daten']
			);
			$result = $this->db->get_results($sql, ARRAY_A);
		}
		if (!empty($this->cms->tbname['papoo_gruppen_sync_daten']) &&
			empty($this->checked->syncvar) &&
			$result['0']['gruppen_sync_lang_hlen_ie_die_erwendung_aus'] == 2) {
			//Die Url zuweisen
			$url = $result['0']['gruppen_sync_lang_ier_bitte_die_rl_eintragen_wo_die_aten'];

			//USerdaten übergeben
			$url = $url . "&username=" . $username . "&password=" . $password;
			require_once(PAPOO_ABS_PFAD . "/lib/classes/extlib/Snoopy.class.inc.php");

			$html = new Snoopy();
			$html->agent = "Web Browser";
			$html->fetch($url);
			$xml = $html->results;

			if (substr($xml, 0, 2) != "a:") {
				$xml = mb_substr($xml, mb_strpos($xml, "a:"));
			}

			$userdat = explode("#####", $xml);

			$gruppendaten = unserialize($userdat[0]);
			$userdaten = unserialize($userdat['1']);
			//USerdaten durchgehen und eintragen

			//Gruppendaten durchgehen

			if (!empty($userdat[0])) {
				$this->check_user_datenbank($userdaten, $username, $password, $gruppendaten);
				$server = $_SERVER['SERVER_NAME'];
				$_SESSION['user_extern_id'] = $userdaten['0']['userid'];
				$this->content->template['user_extern_id'] = $_SESSION['user_extern_id'];

				// Datenbankabfrage formulieren
				$selectuser = "SELECT userid, user_bbsid, username, editor, user_style_id, board FROM " . $this->cms->papoo_user . " WHERE user_bbsid =  '" . ($this->db->escape($userdaten['0']['userid'])) . "' AND active='1' ";

				$resultuser = $this->db->get_results($selectuser);
				// es existiert ein User ###
				if (count($resultuser) == 1 and $username != "jeder" and empty ($sperre)) {

					// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
					IfNotSetNull($row);

					$row->board = "";
					foreach ($resultuser as $row) {
						/* Wird die gruppenid überhaupt gebraucht??*/
						// gruppenid raussuchen aus der lookup Tabelle
						$selectgruppeid = "SELECT gruppenid FROM " . $this->cms->papoo_lookup_ug . " WHERE userid='" . $this->db->escape($row->userid) . "' ";
						$resultgruppe = $this->db->get_results($selectgruppeid);

						// gruppenid in ein Array weisen
						$this->gruppenid = array();
						if (!empty ($resultgruppe)) {
							foreach ($resultgruppe as $gru) {
								array_push($this->gruppenid, array('gruppenid' => $gru->gruppenid,));
							}
						}
						//Zähler auf null setzen
						$sqlx = "UPDATE " . $this->cms->papoo_user . " SET wie_oft_login='0', zeitsperre='0' WHERE user_bbsid =  '" . ($this->db->escape($userdat['5'])) . "'";
						$this->db->query($sqlx);

						// Eigenschaften zuweisen
						$this->userid = $row->userid;
						$this->username = strtolower($row->username);
						$this->password = $password;
						$this->user_club_stufe = $row->user_club_stufe;
						$this->editor = $row->editor;
						if ($this->editor == 0) {
							$this->editor = 3;
						}
						$this->board = $row->board;
						$this->user_style = $row->user_style_id;

						// Ausloggvariable löschen
						unset ($_SESSION['logoff']);
						unset ($_SESSION['logfalse']);

						// Einloggzustand übergeben an template
						$userok = $this->content->template['loggedin'] = "user_ok";
						$this->content->template['bbsuser'] = "user_ok";

						// Hashwert erstellen
						$hash = hash('sha256', $server . rtrim($this->username) . $this->userid . session_id());

						// Username und Passwort an Session übergeben
						$_SESSION['sessionusername'] = strtolower($row->username);
						$_SESSION['sessionuserid'] = $row->userid;
						$_SESSION['sessionusergruppenid'] = $this->gruppenid;
						$_SESSION['sessionuserbbsid'] = $row->user_bbsid;
						$_SESSION['user_club_stufe'] = $row->user_club_stufe;
						$_SESSION['sessionhash'] = $hash;
						$_SESSION['sessioneditor'] = $this->editor;
						$_SESSION['sessionboard'] = $this->board;
						$this->session->sessionuser_style = $_SESSION['sessionuser_style'] = $this->user_style;
						// User-CSS übernehmen
						#$this->cms->make_css();
					}

					// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
					IfNotSetNull($username_exist);

					$this->log_user($username, $userok, $username_exist);

				}
				// User-CSS übernehmen
				#$this->cms->make_css();

				$this->checked_extern = "ok";
			} //Stimmt nicht, wiederholen
			else {
				#$this->sportnavi=false;
				$this->checked_extern = "ok";
			}
		}
	}

	/**
	 * checken ob der User in der Datenbank exisitiert wenn nicht, anlegen.
	 *
	 * @param $userdat
	 * @param $username
	 * @param $password
	 * @param $gruppendaten
	 */
	function check_user_datenbank($userdat, $username, $password, $gruppendaten)
	{
		if ($userdat[0]['userid'] > 11) {
			//Alte userid rausholen
			$sql = sprintf("SELECT userid FROM %s WHERE user_bbsid='%d'",
				$this->cms->tbname['papoo_user'],
				$this->db->escape($userdat[0]['userid'])
			);
			$userid_lokal = $this->db->get_var($sql);

			//Alte Daten löschen
			$sql = sprintf("DELETE FROM %s WHERE user_bbsid='%d'",
				$this->cms->tbname['papoo_user'],
				$this->db->escape($userdat[0]['userid'])
			);
			$this->db->query($sql);

			$sql = sprintf("DELETE FROM %s WHERE userid='%d'",
				$this->cms->tbname['papoo_lookup_ug'],
				$userid_lokal
			);
			$this->db->query($sql);

			$sql = sprintf("SELECT MAX(userid) FROM %s ",
				$this->cms->tbname['papoo_user']
			);
			$userid_max_local = $this->db->get_var($sql) + 1;

			//Neu eintragen
			$sql = sprintf("INSERT INTO %s SET
								user_bbsid='%d', 
								username='%s', 
								email='%s', 
								password='%s', 
								active='1', 
								board='1', 
								zeitstempel='%s'",
				$this->cms->tbname['papoo_user'],
				$this->db->escape($userdat[0]['userid']),
				$this->db->escape($username),
				$this->db->escape($userdat[0]['email']),
				$this->db->escape($this->diverse->hash_password("ghjfgj647jrtn7j67ju4b67ubv356wun647$%%$%67n76457\$§%")),
				date("Y.m.d  G:i:s")
			);
			$this->db->query($sql);
			$insertid = $this->db->insert_id;

			$gruppen_ars = array();
			//Die Gruppenids durchgehen
			if (!empty($gruppendaten)) {
				foreach ($gruppendaten as $gr) {
					//DIe Gruppenids rausholen
					$sql = sprintf("SELECT * FROM %s WHERE gruppen_sync_lookup_externe_gruppe='%d'",
						$this->cms->tbname['papoo_gruppen_sync_lookup'],
						$this->db->escape($gr['gruppenid'])
					);
					$gruppen = $this->db->get_results($sql, ARRAY_A);
					if (is_array($gruppen)) {
						$gruppen_ars = array_merge($gruppen_ars, $gruppen);
					}

				}
			}
			foreach ($gruppen_ars as $dat) {
				$gruppe_fertig[$dat['gruppen_sync_lookup_lokale_gruppe']] = $dat['gruppen_sync_lookup_lokale_gruppe'];
			}
			if (is_array($gruppe_fertig)) {
				foreach ($gruppe_fertig as $grup) {
					$sql = sprintf("INSERT INTO %s SET userid='%d', gruppenid='%d'",
						$this->cms->tbname['papoo_lookup_ug'],
						$insertid,
						$grup
					);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * Login als md5 verschlüsseln
	 *
	 * @param $login
	 * @return string
	 *
	 * @deprecated md5 ist unsicher
	 */
	function createCode($login)
	{
		srand((double)microtime() * 1000000);
		return $this->confirm_code = md5($login . time() . rand(1, 10000000));
	}

	/**
	 * Hier wird überprüft, ob der User auf den Admin Bereich Zugriff hat oder nicht
	 *
	 * @return bool|string|void
	 */
	function check_intern()
	{
		// Die Überprüfung findet heraus ob die Rechte bestehen mindestens einen Menüpunkt des Adminbereiches zu sehen
		//if (!empty ($this->userid) or $this->userid != 11) {
		if (!empty ($this->userid) AND $this->userid != 11) {
			if (empty($this->result_access_menu)) {
				$selectmenu = "SELECT " . $this->cms->papoo_menu_int . ".menuid as menuid, menuname, menulink, menutitel ";
				$selectmenu .= " FROM " . $this->cms->papoo_menu_int . "," . $this->cms->papoo_lookup_men_int . " ," . $this->cms->papoo_lookup_ug . "   WHERE";
				$selectmenu .= " " . $this->cms->papoo_lookup_ug . ".userid=" . $this->userid . " AND";
				$selectmenu .= " " . $this->cms->papoo_lookup_men_int . ".gruppenid=" . $this->cms->papoo_lookup_ug . ".gruppenid AND";
				$selectmenu .= " " . $this->cms->papoo_menu_int . ".menuid=" . $this->cms->papoo_lookup_men_int . ".menuid ";
				$selectmenu .= "";

				IfNotSetNull($_SESSION['dbp']['result_access_menu']);

				if (!is_array($_SESSION['dbp']['result_access_menu'])) {
					$this->result_access_menu = $this->db->get_results($selectmenu);
					$_SESSION['dbp']['result_access_menu'] = $this->result_access_menu;
				}
				else {
					$this->result_access_menu = $_SESSION['dbp']['result_access_menu'];
				}
			}
		} // Keine (gültige) Userid, dann auch keine Rechte
		else {
			$_SESSION['dbp'] = array();
			$this->result_access_menu = "";
		}

		if (empty ($this->result_access_menu)) {
			if (defined('admin')) {
				// Login-Seite anzeigen
				if (empty($this->extern)) {
					if (!strpos($_SERVER['REQUEST_URI'], basename($_SERVER['SCRIPT_NAME']))) {
						$location_url = "index.php";
						if (isset($_SESSION['debug_stopallredirect']) && $_SESSION['debug_stopallredirect']) {
							echo '<a href="' . htmlspecialchars($location_url) . '">Weiter</a>';
						}
						else {
							header("Location: $location_url");
						}
						exit;
					}

					$session_path = @session_save_path();
					if ($session_path) {
						//Sonderfall behandlug wenn zahl und ; vor dem path steht
						$regex = '/[\d]+;\//m';
						$session_path = preg_replace($regex, '/', $session_path);
						$free_space = disk_free_space($session_path);
						$this->content->template['diskspacelow'] =
							($free_space !== false and $free_space < 16 * 1024 * 1024);
					}
					// Login-Template anzeigen
					global $template;
					$template = "login.utf8.html";
				}

				// bei fehlerhaftem Einloggen auch noch entsprechende Meldung zeigen
				if (!empty($this->checked->username)) {
					$this->content->template['logfalse'] = true;
					@header('HTTP/1.1 403 Forbidden');
				}
			}
			else {
				return false;
			}
		}
		// Nicht leer, dann Zugriff erlauben
		else {
			//Plugin checken
			$this->content->template['internokay'] = "OK";
			return "ok";
		}
	}

	/**
	 * Hiermit wird kontrolliert ob auf eine gewählte Menüid Zugriff besteht -> Admin Bereich
	 *
	 * @param int $intern
	 * @return bool
	 */
	function check_access($intern = 0)
	{
		//ob das jetzt so sein muß??
		if (empty($this->result_access_menu)) {
			$this->check_intern();
		}
		$acces_ok = "";
		// Zugriff überprüfen, Array durchlaufen
		if (!empty($this->result_access_menu)) {
			foreach ($this->result_access_menu as $access) {
				// Wenn menuid erlaubt ist, dann Variable füllen
				if ($access->menuid == $this->checked->menuid) {
					$acces_ok = "OK";
				}
			}
		}
		if ($acces_ok != "OK" && $intern == 1) {
			return false;
		}

		if ($acces_ok != "OK") {
			// Wichtig, kein Zugriff Startseite neu laden !!
			//header("Location: index.php");
			$location_url = "index.php?menuid=1";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			} else {
				header("Location: $location_url");
			}
			exit;
		}
		else {
			return true;
		}
	}

	/**
	 * Hiermit wird kontrolliert ob auf eine gewählte Menüid Zugriff besteht -> Admin Bereich
	 *
	 * @return void
	 */
	function check_access_home()
	{

		// Zugriff überprüfen, Array durchlaufen
		foreach ($this->result_access_menu as $access) {
			// Wenn menuid erlaubt ist, dann Variable füllen
			if ($access->menuid == $this->checked->menuid) {
				$acces_ok = "OK";
			}
		}
		if ($acces_ok != "OK") {
			//header("Location: ./index.php?false=1");
			$location_url = "./index.php?false=1";
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
	 * Hiermit wird kontrolliert ob auf eine gewählte Menüid Zugriff besteht -> Admin Bereich
	 *
	 * @return void
	 */
	function check_access_home_return()
	{
		// Zugriff überprüfen, Array durchlaufen
		if (!empty($this->result_access_menu)) {
			foreach ($this->result_access_menu as $access) {
				// Wenn menuid erlaubt ist, dann Variable füllen
				if ($access->menuid == $this->checked->menuid) {
					$acces_ok = "OK";
				}
			}
		}
		if ($acces_ok != "OK") {
			//header("Location: ./index.php?false=1");
			$this->notok_plugin = "no";
		}
	}

	/**
	 * Funktion zur Authentifizierung
	 */
	function check_user()
	{
		if (empty($this->checked->sperre)) {
			$this->checked->sperre = "";
		}
		if ($this->checked->sperre == "gesperrt") {
			$this->content->template['sperre'] = "gesperrt";
		}

		//Fürs Backend die Sprachauswahl
		$this->content->template['language'] = array();
		// Setze Variable auf NULL falls nicht existent, zwecks Behebung von Warnungen
		if (!isset($_SESSION['dbp']['papoo_resultlang'])) {
			$_SESSION['dbp']['papoo_resultlang'] = null;
		}
		if (!is_array($_SESSION['dbp']['papoo_resultlang'])) {
			$resultlang = $this->db->get_results("SELECT * FROM " . $this->cms->papoo_name_language . "  ");
			$_SESSION['dbp']['papoo_resultlang'] = $resultlang;
		}
		else {
			$resultlang = $_SESSION['dbp']['papoo_resultlang'];
		}

		// zuweisen welche Sprache ausgewählt sind
		foreach ($resultlang as $rowlang) {

			array_push($this->content->template['language'],
				array(
					'language' => $rowlang->lang_long,
					'lang_id' => $rowlang->lang_id,
					'lang_short' => $rowlang->lang_short,
					'selected' => "",
				));

		}
		$password = "";
		if (!empty ($_SESSION['logfalse'])) {
			$this->content->template['logfalse'] = $_SESSION['logfalse'];
			unset ($_SESSION['logfalse']);
		}
		// Wenn $username leer ist
		if (empty ($this->checked->username)) {
			// Daten aus der Session holen, wenn diese gesetzt ist
			$username = empty($_SESSION['sessionusername']) ? "" : $_SESSION['sessionusername'];

			// Minimaldaten zuweisen
			$this->userid = "11";
			//Standardeinstellung für das Forum aus den Stammdaten
			$this->board = $this->cms->forum_board;
		}
		else {
			$username = trim($this->checked->username);
			$password = $this->checked->password;
		}

		// Einloggvorgang durchführen  Überprüfung der Daten, wenn username belegt ist
		if (!empty ($username)) {
			$this->do_einlogg($username, $password);
		}
		else {
			// Kein User
			$this->content->template['lognew'] = "nouser";
		}

		// für Template, anzeigen des einlogg Formulars
		$this->content->template['einloggen'] = "nouser";

		// Wenn die Anfrage aus dem Adminbereich kommt, weiter überprüfen
		if (defined("admin")) {
			$this->check_intern();
			//TINY MMCE Config einstellen
			$this->get_user_tiny();
		}
		$this->is_administrator();
	}

	/**
	 * Passwort erzeugen
	 *
	 * @return string
	 */
	function make_passwort()
	{
		// Warum fehlen hier: i, j, l, o?
		$Buchstaben = array(
			"a", "b", "c", "d", "e", "f", "g", "h", "k", "m", "n",
			"p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"
		);
		$Zahlen = range(2, 9, 1);
		$Sonderzeichen = array(".", "!", "%", "&", "=", "?");

		$Laenge = 8;

		for ($i = 0, $Passwort = ""; strlen($Passwort) < $Laenge; $i++) {
			if (rand(0, 2) == 0 && isset ($Buchstaben)) {
				$Passwort .= $Buchstaben[rand(0, count($Buchstaben))];
			}
			elseif (rand(0, 2) == 1 && isset ($Zahlen)) {
				$Passwort .= $Zahlen[rand(0, count($Zahlen))];
			}
			elseif (rand(0, 2) == 2 && isset ($Sonderzeichen)) {
				$Passwort .= $Sonderzeichen[rand(0, count($Sonderzeichen))];
			}
		}

		return $Passwort;
	}

	/**
	 * Passwort Erinnenrungsfunktion
	 */
	function remind_pass()
	{
		$this->content->template['vergessen'] = "ok";
		if (!empty ($this->checked->submituedat)) {
			//Formulart wurde übermittellt, also Daten raussuchen
			$sql = sprintf("SELECT email FROM %s
										WHERE username = '%s' 
										OR email='%s' 
										LIMIT 1",
				$this->cms->tbname['papoo_user'],
				$this->db->escape($this->checked->uedat),
				$this->db->escape($this->checked->uedat)
			);
			$result = $this->db->get_var($sql);
			if (!empty ($result)) {
				//neues Passwort erzeugen
				$newpass = $this->make_passwort();
				//neues Passwort eintragen
				$sql = sprintf("UPDATE %s SET password = '%s'
										WHERE email = '%s' 
										LIMIT 1",
					$this->cms->tbname['papoo_user'],
					$this->diverse->hash_password($newpass),
					$this->db->escape($result)
				);
				$this->db->query($sql);
				//neues Passwort mit Benutzername versenden
				$sql = sprintf("SELECT username FROM %s
										WHERE email = '%s' 
										LIMIT 1",
					$this->cms->tbname['papoo_user'],
					$this->db->escape($result)
				);
				$resultuser = $this->db->get_var($sql);
				if ($this->mv->is_mv_installed) {
					$sql = sprintf("SELECT mv_id FROM %s
												WHERE mv_art = 2",
						$this->cms->tbname['papoo_mv']
					);
					$mv_id = $this->db->get_var($sql);
					if ($mv_id
						AND $this->cms->tbname['papoo_mv_content_' . $mv_id . '_search_1']
					) {
						$sql = sprintf("UPDATE %s SET passwort_2 = '%s'
												WHERE Benutzername_1 = '%s' 
												LIMIT 1",
							$this->cms->tbname['papoo_mv_content_' . $mv_id . '_search_1'],
							$this->diverse->hash_password($newpass),
							$this->db->escape($resultuser)
						);
						$this->db->query($sql);
						$sql = sprintf("UPDATE %s SET passwort_2 = '%s'
												WHERE Benutzername_1 = '%s' 
												LIMIT 1",
							$this->cms->tbname['papoo_mv_content_' . $mv_id . '_search_1'],
							$this->diverse->hash_password($newpass),
							$this->db->escape($resultuser)
						);
						$this->db->query($sql);
					}
				}
				$link = "http://" . str_replace("//", "/", $this->cms->title_send . PAPOO_WEB_PFAD . "/account.php");
				$body = $this->content->template['message_2264'];
				$body = str_ireplace("#site", $this->cms->title_send, $body);
				$body = str_ireplace("#username#", $resultuser, $body);
				$body = str_ireplace("#password#", $newpass, $body);
				$body = str_ireplace("#link#", $link, $body);
				#$body =$body.$link;
				$this->mail_it->to = $result;
				$this->mail_it->from = $this->cms->admin_email;
				$this->mail_it->from_text = "";
				$this->mail_it->subject = $this->content->template['message_2263'] . $this->cms->title_send;
				$this->mail_it->body = $body;
				//$this->mail_it->priority = 5;
				$this->mail_it->do_mail();
				$this->content->template['valide'] = "ok";
			}
			else {
				$this->content->template['notvalide'] = "ok";
			}
		}
	}

	/**
	 * Hiermit kann ein neuer Account erstellt oder ein alter bearbeitet werden
	 *
	 * @return void
	 * @throws phpmailerException
	 */
	function do_account()
	{
		$this->nochmal = 0;
		$user_data = array();
		$checked = "";
		//visiuserid
		$this->content->template['easyedit'] = "edit";

		IfNotSetNull($this->checked->fertig);
		IfNotSetNull($this->checked->delaccount);
		IfNotSetNull($this->checked->forgot);

		if ($this->checked->fertig == "drin") {
			$this->content->template['userfertig'] = 1;
		}

		if ($this->checked->delaccount == "ok") {
			$this->content->template['userdel'] = 1;
		}

		if ($this->checked->forgot == "1") {
			$this->remind_pass();
		}
		else {
			if (!empty ($this->checked->loeschenecht)) {
				$sql = "UPDATE " . $this->cms->papoo_user . " SET active='0' WHERE userid='" . $this->userid . "' LIMIT 1 ";
				$this->db->query($sql);
				$body = "Userid: " . $this->user->userid . " hat Account deaktiviert.";

				$this->mail_it->to = $this->cms->admin_email;
				$this->mail_it->from = $this->cms->admin_email;
				$this->mail_it->from_text = "";
				$this->mail_it->body = $body;
				$this->mail_it->subject = "Benutzer " . $this->username . " hat Konto deaktiviert.";
				$this->mail_it->do_mail();
				// einloggen

				$this->do_einlogg($this->username, $this->password);
				$this->userid = 0;
				$this->username = 0;
				$this->password = 0;
				$_SESSION['sessionusername'] = "";
				// Kein User, für falsches Login Formular anzeigen
				//header("Location: ./login.php");
				$location_url = "./account.php?delaccount=ok";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="' . $location_url . '">Weiter</a>';
				} else {
					header("Location: $location_url");
				}
				exit;
			}
			else {
				//Wenn die Aktivierung eines Accounts erfolgen soll, aufgerufen via EMail, dann checken.
				if (!empty ($this->checked->activate)) {
					$sql = "SELECT userid,username,password_org FROM " . $this->cms->papoo_user . " WHERE";
					$sql .= " confirm_code='" . $this->db->escape($this->checked->activate) . "' LIMIT 1 ";

					$result = $this->db->get_results($sql);

					$count = count($result);

					// Wenn ein Eintrag existiert, dann bestätigen und Session starten

					if ($count == 1) {
						$this->db->csrfok = true;
						$sql = "UPDATE " . $this->cms->papoo_user . " SET active='1' WHERE confirm_code='" . $this->db->escape($this->checked->activate) . "' LIMIT 1 ";
						$this->db->query($sql);
						$this->db->csrfok = false;
						//print_r($sql);exit();

						// Für die Mitgliederverwaltung die Daten in der mv_content Tabelle speichern
						if ($this->mv->is_mv_installed) {
							$this->mv->activate_mv_user($result);
						}
						// einloggen
						#foreach ($result as $row) {
						#$this->do_einlogg($row->username, $row->password_org);
						#}
						//header("Location: ./login.php");
						$location_url = "./account.php?fertig=drin&menuid=".$this->checked->menuid;

						if ($_SESSION['debug_stopallredirect']) {
							echo '<a href="' . $location_url . '">Weiter</a>';
						} else {
							header("Location: $location_url");
						}
						exit;
					}
				}
				else {
					// Template Formular anzeigen
					if (!empty ($this->checked->loginnow)) {
						// Benutzereingaben überprüfen
						$this->check_data();

						if ($this->spamschutz->is_spam) {
							$this->namefalsch = "1";
						}
						// Wenn alle Eingaben korrekt sind, dann in die Datenbank eintragen|| $this->vorfalsch || $this->nachfalsch || $this->nrstrfalsch
						if (!($this->namefalsch || $this->passwortfalsch || $this->emailfalsch || $this->agbfalsch)) {
							// Wenn auf ja gesetzt, Antwortmails für das Forum zulassen
							if ($this->checked->antwortmail == "ok") {
								$antwortmail = 1;
							} else {
								$antwortmail = 0;
							}

							if ($this->checked->dauer_einlogg == "ok") {
								$this->checked->dauer_einlogg = 1;
							}
							else {
								$this->checked->dauer_einlogg = 0;
							}

							$title_send = $this->cms->title_send;
							//$heute = date("Y.m.d  G:i:s");
							// Confirm Code beim erstellen des Accounts via Email prüfen
							$confirm_code = $this->createCode($this->checked->neuusername);

							$sql = sprintf("INSERT INTO %s
											SET username='%s', email='%s', password='%s', antwortmail='%s', zeitstempel=NOW(),
											user_vorname='%s', user_nachname='%s', user_strasse='%s', user_ort='%s', user_plz='%s',
											user_style_id='%d', dauer_einlogg='%d', confirm_code='%s', board='%d',
											user_agb_ok='%d', user_newsletter='%s', signatur='%s', signatur_html='%s',
											active='0'",
								$this->cms->papoo_user,

								$this->db->escape($this->checked->neuusername),
								$this->db->escape($this->checked->neuemail),
								$md5password = $this->diverse->hash_password($this->checked->neupassword1),
								$antwortmail,

								$this->db->escape($this->checked->neuvorname),
								$this->db->escape($this->checked->neunachname),
								$this->db->escape($this->checked->neustrnr),
								$this->db->escape($this->checked->neuort),
								$this->db->escape($this->checked->neuplz),

								$this->db->escape($this->checked->style),
								$this->db->escape($this->checked->dauer_einlogg),
								$this->db->escape($confirm_code),
								$this->db->escape($this->checked->forum_board),

								$this->db->escape($this->checked->user_agb_ok),
								$this->db->escape($this->checked->newsletter),
								$this->db->escape($this->checked->signatur),
								$this->db->escape($this->bbcode->parse($this->checked->signatur))
							);

							$this->db->query($sql);
							// Neue Userid erfahren
							$userid = $this->db->insert_id;
							// die gruppenid auf jeder setzen
							$sqlin = "INSERT INTO " . $this->cms->papoo_lookup_ug . " SET userid='$userid', gruppenid='10' ";
							$this->db->query($sqlin);

							// sendmail hier

							$link = "http://" . str_replace("//", "/",
									$this->cms->title_send . PAPOO_WEB_PFAD . "/account.php");

							$body = $this->content->template['message_2270'];
							$body = str_ireplace("#seitentitel#", $title_send, $body);
							$body = str_ireplace("#username#", $this->checked->neuusername, $body);
							$body = str_ireplace("#passwort#", $this->checked->neupassword1, $body);
							$body = str_ireplace("#link_seite#", $link, $body);
							$body = str_ireplace("#bestaetigungslink#", $link . "?activate=" . $confirm_code."&menuid=".$this->checked->menuid, $body);

							$this->mail_it->to = $this->checked->neuemail;
							$this->mail_it->from = $this->cms->admin_email;
							$this->mail_it->from_text = "";
							$this->mail_it->subject = $this->content->template['message_2271'] . $this->cms->title_send;
							$this->mail_it->body = $body;
							//$this->mail_it->priority = 5;

							$this->mail_it->do_mail();
							if ($this->cms->benach_neueruser == 1) {
								$this->diverse->mach_nachricht_neu($this->content->template['message_2272'],
									$this->content->template['message_2273'] . $this->checked->neuusername . $this->content->template['message_2274']);
							}

							//header("Location: ./login.php?fertig=1");
							$location_url = $this->cms->webverzeichnis . "/account.php?fertig=1&menuid=".$this->checked->menuid;
							if ($_SESSION['debug_stopallredirect']) {
								echo '<a href="' . $location_url . '">Weiter</a>';
							}
							else {
								header("Location: $location_url");
							}
							exit;
						}
						else {
							$this->nochmal = 1;
						}
					}
					elseif (!empty ($this->checked->loginnow2)) {
						// Daten überprüfen
						$this->check_data(); //!!!! b.legt: hier liegt irgendein Fehler !!!
						//print_r("hier?");exit();
						/*
						* für genaue Überprüfug das hier verwenden
						* if (!($this->namefalsch || $this->passwortfalsch || $this-
						* >emailfalsch || $this->nrstrfalsch || $this->ortfalsch || $this-
						* >plzfalsch || $this->nachfalsch || $this->vorfalsch)) {
						*/
						// Wenn alle Eingaben korrekt sind, dann in die Datenbank updaten user_agb_ok
						if (!($this->namefalsch || $this->passwortfalsch || $this->emailfalsch || $this->agbfalsch)) {
							if ($this->checked->antwortmail == "ok") {
								$antwortmail = 1;
							}
							else {
								$antwortmail = 0;
							}
							//if (empty($this->checked->dauer_einlogg)){
							//	$this->checked->dauer_einlogg="";
							//}
							if ($this->checked->dauer_einlogg == "ok") {
								$this->checked->dauer_einlogg = 1;
							}
							else {
								$this->checked->dauer_einlogg = 0;
							}

							if (empty ($this->checked->style)) {
								$this->checked->style = "";
							}
							$_SESSION['board'] = $this->checked->forum_board;
							$passencryp = $this->diverse->hash_password($this->checked->neupassword1);
							// Daen eintragen
							$sql = "UPDATE " . $this->cms->papoo_user . " SET ";
							$sql .= " antwortmail='" . $this->db->escape($antwortmail) . "', ";
							$sql .= " email='" . $this->db->escape($this->checked->neuemail) . "',";

							// Wenn das Passwort geändert wurde
							if (!empty ($this->checked->neupassword1)) {
								$sql .= " password ='$passencryp',";
								$formPasswordanders = "";
							} else {
								$formPasswordanders = "Ihr Passwort wurde nicht geaendert";
							}
							$sql .= " user_vorname ='" . $this->db->escape($this->checked->neuvorname) . "', ";
							$sql .= " user_nachname='" . $this->db->escape($this->checked->neunachname) . "' , ";
							$sql .= " user_strasse='" . $this->db->escape($this->checked->neustrnr) . "', ";
							$sql .= " user_ort='" . $this->db->escape($this->checked->neuort) . "' ,";
							$sql .= " user_plz='" . $this->db->escape($this->checked->neuplz) . "', ";
							$sql .= " user_style_id = '" . $this->db->escape($this->checked->style) . "' ,";
							$sql .= " user_newsletter = '" . $this->db->escape($this->checked->newsletter) . "' ,";
							$sql .= " dauer_einlogg = '" . $this->db->escape($this->checked->dauer_einlogg) . "',  ";
							$sql .= " board = '" . $this->db->escape($this->checked->forum_board) . "',  ";
							$sql .= " signatur = '" . $this->db->escape($this->checked->signatur) . "',  ";
							$sql .= " user_titel='" . $this->db->escape($this->checked->user_titel) . "', ";
							$sql .= " user_gender='" . $this->db->escape($this->checked->user_gender) . "', ";
							$sql .= " user_country='" . $this->db->escape($this->checked->user_country) . "', ";
							$sql .= " user_tel_abends='" . $this->db->escape($this->checked->user_tel_abends) . "', ";
							$sql .= " user_tel_tags='" . $this->db->escape($this->checked->user_tel_tags) . "', ";
							$sql .= " user_fax='" . $this->db->escape($this->checked->user_fax) . "', ";
							$sql .= " user_tel_kunden_nr='" . $this->db->escape($this->checked->user_tel_kunden_nr) . "', ";
							//user_agb_ok='%d',
							$sql .= " user_agb_ok = '" . $this->db->escape($this->checked->user_agb_ok) . "',  ";
							$sql .= " signatur_html = '" . $this->db->escape($this->bbcode->parse($this->checked->signatur)) . "'  ";
							$sql .= " WHERE userid='" . $this->db->escape($this->userid) . "'";

							$this->db->query($sql);
							if (empty($this->checked->neuusername)) {
								$sql = sprintf("SELECT username FROM %s WHERE userid = '%d'",
									$this->cms->papoo_user,
									$this->db->escape($this->userid)
								);
								$this->checked->neuusername = $this->db->get_var($sql);
							}
							// sendmail hier
							$link = "http://" . str_replace("//", "/", $this->cms->title_send . "/account.php");

							$body = $this->content->template['message_2275'];
							$body = str_ireplace("#username#", $this->checked->neuusername, $body);
							$body = str_ireplace("#password#", $this->checked->neupassword1 . $formPasswordanders,
								$body);
							$body = str_ireplace("#link#", $link, $body);

							$this->mail_it->to = $this->checked->neuemail;
							$this->mail_it->from = $this->cms->admin_email;
							//$this->mail_it->from_text = "";
							$this->mail_it->subject = $this->content->template['message_2276'] . $this->cms->title_send;
							$this->mail_it->body = $body;
							$this->mail_it->priority = 5;

							$this->mail_it->do_mail();
						}
						else {
							$this->nochmal = 1;
						}
					}
					if (empty ($this->checked->fertig)) {
						$this->checked->fertig = "";
					}

					if ($this->checked->fertig == 1) {
						$this->content->template['neu_fertig'] = "ok";
					}
					// Userdaten überprüfen
					//$userdata = $this->check_user();

					// Wen keine Userid vorhanden ist, also keiner eingeloggt ist
					if ($this->userid == "11") {
						// Keine Userid, aber etwas stimmt nicht
						IfNotSetNull($this->content->template['logfalse']);
						if ($this->content->template['logfalse'] == "user_wrong") {
							$textlogin = "<h2>Bitte überprüfen Sie Ihre Angaben</h2><p>Sie habe anscheinend einen Usernamen benutzt der schon verwendet wird, oder Ihre Email Adresse ist nicht in Ordnung.</p>";
						}
						// Keine Userid, aber alles ok, Formular anzeigen -> Template
						else {
							$this->content->template['nichtfertig'] = '1';
							$this->content->template['loginfirst'] = '1';
							IfNotSetNull($this->checked->newsletter);
							if ($this->checked->newsletter == "ok") {
								$checked_newsletter = "nodecode:checked=\"checked\"";
							}
							else {
								$checked_newsletter = "";
							}

							$user_data = array();
							// etwas stimmt nicht nochmal aufrufen
							if ($this->nochmal == 1) {
								$this->content->template['error_nochmal_user'] = "ok";
								if ($this->checked->antwortmail == "ok") {
									$checked = 'nodecode:checked="checked"';
								}
								else {
									$checked = "";
								}

								if ($this->checked->dauer_einlogg == "ok") {
									$checked_logg = 'nodecode:checked="checked"';
								}
								else {
									$checked_logg = "";
								}

								if ($this->checked->user_agb_ok == 1) {
									$user_agb_ok = 'nodecode:checked="checked"';
								}
								else {
									$user_agb_ok = "";
								}
								array_push($user_data, array(
									'vorname' => $this->checked->neuvorname,
									'nachname' => $this->checked->neunachname,
									'strnrname' => $this->checked->neustrnr,
									'plzname' => $this->checked->neuplz,
									'ortname' => $this->checked->neuort,
									'checked' => $checked,
									'loeschen' => "",
									'user_titel' => $this->checked->user_titel,
									'user_gender' =>$this->checked->user_gender,
									'user_fax' => $this->checked->user_fax,
									'user_country' => $this->checked->user_country,
									'user_tel_abends' => $this->checked->user_tel_abends,
									'user_tel_tags' => $this->checked->user_tel_tags,
									'user_tel_kunden_nr' => $this->checked->user_tel_kunden_nr,
									'username' => $this->checked->neuusername,
									'mailname' => $this->checked->neuemail,
									'checked_logg' => $checked_logg,
									'password1' => $this->checked->neupassword1,
									'password2' => $this->checked->neupassword2,
									'fehltvorname' => $this->fehltvorname,
									'fehltnachname' => $this->fehltnachname,
									'fehltstrnr' => $this->fehltstrnr,
									'fehltplz' => $this->fehltplz,
									'fehltort' => $this->fehltort,
									'fehltemail' => $this->fehltemail,
									'fehltusername' => $this->fehltusername,
									'fehltpass1' => $this->fehltpass1,
									'fehltagb' => $this->fehltagb,
									'fehltpass2' => $this->fehltpass2,
									'nomatch' => $this->password_nomatch,
									'checked_newsletter' => $checked_newsletter,
									'checked_user_agb_ok' => $user_agb_ok,
									'signatur' => $this->checked->signatur
								));
							}
							else {
								$checkedboard1 = $checkedboard2 = $checkedboard3 = $checkedlogg = "";
								if ($this->cms->forum_board == 0) {
									$checkedboard1 = 'nodecode:checked="checked"';
								}

								$checked_newsletter = "nodecode:checked=\"checked\"";
								if ($this->cms->forum_board == 1) {
									$checkedboard2 = 'nodecode:checked="checked"';
								}

								if ($this->cms->forum_board == 2) {
									$checkedboard3 = 'nodecode:checked="checked"';
								}
								array_push($user_data, array(
									'vorname' => "",
									'nachname' => "",
									'strnrname' => "",
									'plzname' => "",
									'ortname' => "",
									'checked' => $checked,
									'loeschen' => "",
									'username' => "",
									'mailname' => "",
									'logalt' => "",
									'password1' => "",
									'password2' => "",
									'checked_board1' => $checkedboard1,
									'checked_board2' => $checkedboard2,
									'checked_board3' => $checkedboard3,
									'checked_newsletter' => $checked_newsletter,
								));
							}
							// Daten in abstrakte Datnklasse einlesen
							$this->content->template['table_data'] = $user_data;
						}
					} // Eine Userid ist vorhanden, also wurde sich korrekt eingeloggt
					else {
						// noch nicht fertig-> Template
						$this->content->template['nichtfertig'] = '1';
						// Account bearbeiten -> Template
						$this->content->template['manageprofil'] = '1';
						$this->content->template['loginfirst'] = '1';
						// Daten aus der Datenbank holen
						$resultuser = $this->db->get_results("SELECT * FROM " . $this->cms->papoo_user . " WHERE userid='" . $this->userid . "' AND active='1' LIMIT 1");
						// Wenn der User existiert, dann Daten ausgeben
						if (count($resultuser) == 1) {
							foreach ($resultuser as $row) {
								if ($row->antwortmail == "1") {
									$checked = 'nodecode:checked="checked"';
								} else {
									$checked = "";
								}
								if ($row->user_newsletter == "ok") {
									$checked_newsletter = "nodecode:checked=\"checked\"";
								} else {
									$checked_newsletter = "";
								}

								$checkedboard1 = $checkedboard2 = $checkedboard3 = $checkedlogg = "";
								if ($row->board == 0) {
									$checkedboard1 = 'nodecode:checked="checked"';
								}
								if ($row->board == 1) {
									$checkedboard2 = 'nodecode:checked="checked"';
								}
								if ($row->board == 2) {
									$checkedboard3 = 'nodecode:checked="checked"';
								}
								if ($row->dauer_einlogg == 1) {
									$checkedlogg = 'nodecode:checked="checked"';
								}
								if ($row->user_agb_ok == 1) {
									$user_agb_ok = 'nodecode:checked="checked"';
								}
								IfNotSetNull($user_agb_ok);
								IfNotSetNull($this->fehltvorname);
								IfNotSetNull($this->fehltnachname);
								IfNotSetNull($this->fehltstrnr);
								IfNotSetNull($this->fehltplz);
								IfNotSetNull($this->fehltort);
								IfNotSetNull($this->fehltemail);
								IfNotSetNull($this->fehltusername);
								IfNotSetNull($this->fehltemail);
								IfNotSetNull($this->fehltpass1);
								IfNotSetNull($this->fehltagb);
								IfNotSetNull($this->fehltpass2);
								IfNotSetNull($this->password_nomatch);

								if ($this->nochmal == 1) {
									$this->content->template['error_nochmal_user'] = "ok";
								}
								array_push($user_data, array(
									'vorname' => $row->user_vorname,
									'nachname' => $row->user_nachname,
									'strnrname' => $row->user_strasse,
									'plzname' => $row->user_plz,
									'ortname' => $row->user_ort,
									'checked' => $checked,
									'loeschen' => "Loeschen",
									'user_titel' =>$row->user_titel,
									'user_gender' =>$row->user_gender,
									'user_fax' => $row->user_fax,
									'user_country' => $row->user_country,
									'user_tel_abends' => $row->user_tel_abends,
									'user_tel_tags' => $row->user_tel_tags,
									'user_tel_kunden_nr' => $row->user_tel_kunden_nr,
									'username' => $row->username,
									'mailname' => $row->email,
									'logalt' => "",
									'checked_board1' => $checkedboard1,
									'checked_board2' => $checkedboard2,
									'checked_user_agb_ok' => $user_agb_ok,
									'checked_board3' => $checkedboard3,
									'style_selected' => $row->user_style_id,
									'signatur' => "nobr:" . $row->signatur,
									'checked_logg' => $checkedlogg,
									'checked_newsletter' => $checked_newsletter,
									'fehltvorname' => $this->fehltvorname,
									'fehltnachname' => $this->fehltnachname,
									'fehltstrnr' => $this->fehltstrnr,
									'fehltplz' => $this->fehltplz,
									'fehltort' => $this->fehltort,
									'fehltemail' => $this->fehltemail,
									'fehltusername' => $this->fehltusername,
									'fehltpass1' => $this->fehltpass1,
									'fehltagb' => $this->fehltagb,
									'fehltpass2' => $this->fehltpass2,
									'nomatch' => $this->password_nomatch
								));
							}
							// Daten in abstrakte Datenklasse einlesen
							$this->content->template['table_data'] = $user_data;
						}
					}
				}
			}
		}
	}

	/**
	 * Userdaten aus dem Formular überprüfen
	 *
	 * @return void
	 */
	function check_data()
	{
		$this->namefalsch = "";
		$this->emailfalsch = "";
		$this->passwortfalsch = "";
		$this->fehltvorname = "";
		$this->fehltnachname = "";
		$this->fehltstrnr = "";
		$this->fehltplz = "";
		$this->fehltort = "";
		$this->fehltusername = "";
		$this->fehltemail = "";
		$this->fehltpass1 = "";
		$this->fehltpass2 = "";
		$this->password_nomatch = "";
		$this->nrstrfalsch = "";
		$this->ortfalsch = "";
		$this->plzfalsch = "";
		$this->nachfalsch = "";
		$this->vorfalsch = "";
		$this->agbfalsch = "";

		if (!empty ($this->checked->loginnow) or !empty ($this->checked->loginnow2)) {
			// User überprüfen
			// existiert dieser User-Name schon ??
			if (!empty ($this->checked->neuusername)) {
				$selectuser = "SELECT COUNT(userID) FROM " . $this->cms->papoo_user . " " . "WHERE userName = '" . $this->db->escape($this->checked->neuusername) . "' ";
				$result = $this->db->get_var($selectuser);
				// Wenn ja...
				if ($result == 1) {
					// dann auf falsch setzen
					$this->namefalsch = 1;
					$this->content->template['usernamefalsch'] = $this->fehltusername = $this->content->template["ergaenzen"];
				}
				// existiert schon
			} // kein Username eingegeben
			else {
				if (empty ($_SESSION['sessionusername'])) {
					$this->namefalsch = 1;
					$this->content->template['usernamefalsch'] = $this->fehltusername = $this->content->template["ergaenzen"];
				}
				$this->checked->neuusername = "";
			}

			// Auch wenn der Username default ist
			if ($this->checked->neuusername == "Username") {
				$this->namefalsch = 1;
				$this->content->template['usernamefalsch'] = $this->fehltusername = $this->content->template["ergaenzen"];
			}

			//Blacklist filter
			if ($this->blacklist->do_blacklist($this->checked->neuemail) == "not_ok") {
				$this->namefalsch = 1;
			}
			if ($this->blacklist->do_blacklist($this->checked->neuusername) == "not_ok") {
				$this->namefalsch = 1;
			}

			// Passwort überprüfen
			if (empty ($this->checked->neupassword1)) {
				if (empty ($this->checked->neupassword1) or $this->checked->neupassword1 != $this->checked->neupassword2) {
					$this->passwortfalsch = 1;
					$this->content->template['pass1falsch'] = $this->fehltpass1 = $this->content->template["ergaenzen"];
					$this->content->template['pass2falsch'] = $this->fehltpass2 = $this->content->template["ergaenzen"];
				}
			}
			if ($this->checked->neupassword1 != $this->checked->neupassword2) {
				$this->content->template['nomatch'] = $this->password_nomatch = "<strong style=\"color:red;\">Passw&ouml;rter stimmen nicht &uuml;berein.</strong>";
			}

			if (empty ($this->checked->neupassword1) && empty(($this->checked->neupassword2)) && $this->userid > 11) {
				$this->passwortfalsch = "";
				$this->content->template['pass1falsch'] = $this->fehltpass1 = "";
				$this->content->template['pass2falsch'] = $this->fehltpass2 = "";
				$this->content->template['nomatch']="";
			}

			// Email überprüfen
			// keine Email angegeben
			if (empty ($this->checked->neuemail)) {
				$this->emailfalsch = 1;
				$this->content->template['emailfalsch'] = $this->fehltemail = $this->content->template["ergaenzen"];
			}
			else {
				// Validität der Mail checken
				if (!$this->mail_it->validateEmail($this->checked->neuemail)) {
					$this->emailfalsch = 1;
					$this->content->template['emailfalsch'] = $this->fehltemail = $this->content->template["ergaenzen"];
				}
				else {
					$selectuser = "SELECT COUNT(userid) FROM " . $this->cms->papoo_user . " " . "WHERE email = '" . $this->db->escape($this->checked->neuemail) . "' ";
					$result = $this->db->get_var($selectuser);
					// Wenn ja...
					if ($result > 0) {
						$selectuser = "SELECT userid FROM " . $this->cms->papoo_user . " " . "WHERE email = '" . $this->db->escape($this->checked->neuemail) . "' ";
						$result = $this->db->get_results($selectuser);
						$isok = "";
						foreach ($result as $id) {
							if ($id->userid == $this->userid) {
								$isok = "ok";
							}
						}
						if ($isok == "ok") {
							$this->emailfalsch = "";
							$this->fehltemail = "";
						}
						else {
							$this->emailfalsch = 1;
							$this->content->template['emailfalsch'] = $this->fehltemail = $this->content->template["ergaenzen"];
						}
					}
					// existiert schon
				}
			}

			// Vorname überprüfen
			if (empty ($this->checked->neuvorname) or $this->checked->neuvorname == "Vorname") {
				$this->vorfalsch = 1;
				$this->content->template['vornamefalsch'] = $this->fehltvorname = $this->content->template["ergaenzen"];
			}

			// Nachname überprüfen
			if (empty ($this->checked->neunachname) or $this->checked->neunachname == "Nachname") {
				$this->nachfalsch = 1;
				$this->content->template['nachnamefalsch'] = $this->fehltnachname = $this->content->template["ergaenzen"];
			}

			// Postleitzahl überprüfen
			if (empty ($this->checked->neuplz) or $this->checked->neuplz == "Postleitzahl") {
				$this->plzfalsch = 1;
				$this->content->template['plzfalsch'] = $this->fehltplz = $this->content->template["ergaenzen"];
			}

			// AGB überprüfen
			if (empty ($this->checked->user_agb_ok)) {
				$this->agbfalsch = 1;
				$this->content->template['agbfalsch'] = $this->fehltagb = $this->content->template["ergaenzen"];
			}

			// Ort überprüfen
			if (empty ($this->checked->neuort) or $this->checked->neuort == "Wohnort") {
				$this->ortfalsch = 1;
				$this->content->template['ortfalsch'] = $this->fehltort = $this->content->template["ergaenzen"];
			}

			// Strasse und Hausnummer überprüfen
			if (empty ($this->checked->neustrnr) or $this->checked->neustrnr == "Strasse und Hausnummer") {
				$this->nrstrfalsch = 1;
				$this->content->template['strfalsch'] = $this->fehltstrnr = $this->content->template["ergaenzen"];
			}
		}
	}

	/**
	 * Diese Funktion überprüft den aktuellen User auf nicht-/vorhandene Admin-Rechte und speichert
	 * das Ergebnis in $this->hasAdminRights.
	 *
	 * @return bool Gibt true zurück, wenn der User ein Administrator ist, false andererseits
	 */
	function is_administrator()
	{
		if ($this->hasAdminRights === null) {
			$sql = sprintf("SELECT * FROM %s WHERE userid=%d AND gruppenid=1 LIMIT 1;",
				$this->cms->tbname["papoo_lookup_ug"],
				$this->userid
			);
			$this->hasAdminRights = $this->content->template["userHasAdminRights"] = (bool)$this->db->query($sql);
		}
		return $this->hasAdminRights;
	}
}

$user = new user_class();
