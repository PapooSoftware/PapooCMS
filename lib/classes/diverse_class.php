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
 * Diverse öffentliche Methoden
 */
class diverse_class
{
	/** @var cms */
	public $cms;
	/** @var replace_class|string */
	public $replace;
	/** @var ezSQL_mysqli */
	public $db;
	/** @var checked_class */
	public $checked;
	/** @var dumpnrestore_class */
	public $dumpnrestore;
	/** @var mail_it */
	public $mail_it;
	/** @var string */
	public $webverzeichnis = PAPOO_WEB_PFAD;
	/** @var string */
	public $no_output = "";
	/** @var string */
	public $lokal_db_praefix;

	/**
	 * diverse_class constructor.
	 */
	public function __construct()
	{
		global $cms, $replace, $db, $checked, $dumpnrestore, $mail_it;
		$this->cms = &$cms;
		$this->replace = &$replace;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->dumpnrestore = &$dumpnrestore;
		$this->mail_it = &$mail_it;
	}

	/**
	 *
	 */
	public function not_found()
	{
		if ($this->cms->system_config_data['config_404_benutzen_check'] == 1
			&& !stristr($_SERVER['REQUEST_URI'], "/trackback/")
		) {

			$this->check_log();
			$this->log_404();

			header("HTTP/1.1 404 Not Found");
			$_SERVER['REDIRECT_STATUS'] = 404;

			$url = trim($this->cms->system_config_data["config_adresse_der_404_seite"]);

			if (preg_match("~^https?://~", $url) == false) {
				$protocol =
					empty($_SERVER["HTTPS"]) == false && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443
						? "https://" : "http://";
				$url = $protocol . $_SERVER["HTTP_HOST"] . rtrim(PAPOO_WEB_PFAD, "/") . "/" . ltrim($url, "/");
			}

			die($this->do_404_request($url));
		}
	}

	/**
	 * @param string $url
	 *
	 * @return mixed
	 */
	static public function get_url_get($url = "")
	{
		if (curl::installed()) {
			try {
				$curl = new curl($url);
				$curl->setopt(CURLOPT_TIMEOUT, 5);
				$curl->setopt(CURLOPT_RETURNTRANSFER, true);
				$curl->setopt(CURLOPT_HEADER, 0);
				$curl->setopt(CURLOPT_USERAGENT, "Check Agent");
				$curl_ret = $curl->exec();
			}
			catch (curl_exception $e) {
				$curl_ret = false;
			}
			return $curl_ret;
		}
		else {
			return @file_get_contents($url);
		}
	}

	/**
	 * @param string $url
	 *
	 * @return bool|string|void
	 */
	public function do_404_request($url = "")
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "Check Agent");

		$curl_ret = curl_exec($ch);
		curl_close($ch);
		if (empty($curl_ret)) {
			return "404 Fehler - Seite wurde nicht gefunden - NOT FOUND";
		}

		return $curl_ret;
	}

	/**
	 * Funktion um Fehlermeldungen in ein Error Array zuzuweisen
	 *
	 * @param mixed $num   = Fehlernummer
	 * @param mixed $dat   = Inhalt der Fehlermeldung
	 * @param mixed $class = Klasse in der die Fehlermeldung passiert
	 * @param mixed $func  = Funktion in der die Fehlermeldung passiert
	 *
	 * @return void
	 */
	public function error($num = "", $dat = "", $class = "", $func = "")
	{
		if (!is_array($this->error)) {
			$this->error = [];
		}
		$this->error[] = ['number' => $num, 'data' => $dat, 'class' => $class, 'function' => $func,];
	}

	/**
	 * @deprecated kein Mehrwert
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function dround($value)
	{
		return $value;
	}

	/**
	 * @deprecated kein Mehrwert
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function dround_no($value)
	{
		return $value;
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function dround2($value)
	{
		return str_replace(",", ".", $value);
	}

	/**
	 * @param string $titel
	 * @param string $inhalt
	 *
	 * @throws phpmailerException
	 */
	public function mach_nachricht_neu($titel, $inhalt = "")
	{
		$this->mail_it->to = $this->cms->benach_email;
		$this->mail_it->from = $this->cms->admin_email;
		$this->mail_it->from_text = "System - " . $this->cms->seitentitle;
		$this->mail_it->subject = $titel;
		$this->mail_it->body = $inhalt;
		// niedrigste Priorität
		$this->mail_it->priority = 5;
		$this->mail_it->do_mail();
	}

	/**
	 * Bildet einen MD5-Hash aus $string
	 *
	 * @deprecated MD5 ist nicht mehr sicher ....
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public function encrypt($string)
	{
		return md5($string);
	}

	/**
	 * Erstellt einen neuen Passwort-Hash und benutzt dabei die bcrypt-Hashfunktion.
	 *
	 * @param string $password
	 *
	 * @return bool|string Returns the hashed password, or FALSE on failure.
	 */
	public function hash_password($password)
	{
		return password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);
	}

	/**
	 * Verifiziert ein Passwort, das durch diverse_class::hash_password generiert wurde.
	 *
	 * @param string $password
	 * @param string $hash
	 *
	 * @return bool Returns TRUE if the password and hash match, or FALSE otherwise.
	 */
	public function verify_password($password, $hash)
	{
		return password_verify($password, $hash);
	}

	/**
	 * Eine Datei öffnen und den Inhalt einlesen und zurückgeben
	 * Pfad der Datei muss korrekt sein relativ zum Wurzelverzeichnis
	 * Wenn die Datei nicht existiert wird diese angelegt mit 777
	 *
	 * VORSICHT KEINE ÜBERPRÜFUNG
	 *
	 * @param string $file Dateiname
	 *
	 * @return string Inhalt der Datei
	 */
	public function open_file($file = "")
	{
		if (empty($file) or !is_string($file)) {
			return false;
		}

		$filepath = PAPOO_ABS_PFAD . $file;
		// Wenn die Datei nicht existiert, diese mit 777 anlegen
		$chmod = (!file_exists($filepath) ? 0777 : 0755);
		$file = @fopen($filepath, "a+");
		if ($chmod) {
			@chmod($filepath, $chmod);
		}
		// Inhalt einlesen
		$inhalt = implode("", file($filepath));
		@fclose($file);
		return $inhalt;
	}

	/**
	 * Inhalt eines Verzeichnisses auslesen
	 *
	 * @param string $dir
	 * @param string $ext
	 *
	 * @return array|void|bool Inhalt des Verzeichnisses
	 */
	public function lese_dir($dir = "", $ext = "")
	{
		if (empty($dir) or !is_string($dir)) {
			return false;
		}
		$dirAbsolutePath = PAPOO_ABS_PFAD . $dir;
		if (!is_dir($dirAbsolutePath)) {
			return;
		}
		$handle = opendir($dirAbsolutePath);
		while (true) {
			$file = readdir($handle);
			if ($file === false) {
				break;
			}

			if (
				in_array($file, ['.', '..', '.DS_Store', '.svn', '.git'])
				or (!empty($ext) and !stristr($file, $ext))
			) {
				continue;
			}
			$result[] = [
				'name' => $file,
				'schreib' => is_writeable($dirAbsolutePath . "/" . $file),
				'is_dir' => is_dir($dirAbsolutePath . "/" . $file)
			];
		}

		usort($result, ["diverse_class", "cmp"]);
		return $result;
	}

	/**
	 * diverse_class::getDirectoryTree()
	 *
	 * @param mixed $outerDir
	 * @param mixed $filters
	 *
	 * @return array
	 */
	public function getDirectoryTree($outerDir, $filters = [])
	{
		$dirs = array_diff(scandir($outerDir), array_merge([".", ".."], $filters));
		$dir_array = [];
		foreach ($dirs as $d) {
			$dir_array[$d] = is_dir($outerDir . "/" . $d)
				? $this->getDirectoryTree($outerDir . "/" . $d, $filters) : $dir_array[$d] = $d;
		}
		return $dir_array;
	}

	/**
	 * Get an array that represents directory tree
	 *
	 * @param string $directory Directory path
	 * @param bool   $recursive Include sub directories
	 * @param bool   $listDirs  Include directories on listing
	 * @param bool   $listFiles Include files on listing
	 * @param string $exclude   Exclude paths that matches this regex
	 *
	 * @return array
	 */
	public function directoryToArray($directory, $recursive = true, $listDirs = false, $listFiles = true, $exclude = '')
	{
		$arrayItems = [];
		$skipByExclude = false;
		$handle = opendir($directory);
		if ($handle) {
			while (false !== ($file = readdir($handle))) {
				preg_match("/(^(([\.]){1,2})$|(\.(svn|git(?:keep)?|md))|(Thumbs\.db|\.DS_STORE))$/iu", $file, $skip);
				if ($exclude) {
					preg_match($exclude, $file, $skipByExclude);
				}
				if (!$skip && !$skipByExclude) {
					if (is_dir($directory . DIRECTORY_SEPARATOR . $file)) {
						if ($recursive) {
							$arrayItems = array_merge(
								$arrayItems,
								$this->directoryToArray(
									$directory . DIRECTORY_SEPARATOR . $file,
									$recursive,
									$listDirs,
									$listFiles,
									$exclude
								)
							);
						}
						if ($listDirs) {
							$file = $directory . DIRECTORY_SEPARATOR . $file;
							$arrayItems[] = $file;
						}
					}
					else if ($listFiles) {
						$file = $directory . DIRECTORY_SEPARATOR . $file;
						$arrayItems[] = $file;
					}
				}
			}
			closedir($handle);
		}
		return $arrayItems;
	}

	/**
	 * Vergleicht die lowercase-Varianten der Einträge 'name' 2er arrays miteinander.
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return int
	 */
	public function cmp($a, $b)
	{
		return strcmp(strtolower($a["name"]), strtolower($b["name"]));
	}

	/**
	 * Inhalt in eine Datei schreiben
	 *
	 * VORSICHT KEINE ÜBERPRÜFUNG
	 *
	 * @param string $file Dateiname relativ zu PAPOO_ABS_PFAD
	 * @param string $inhalt Das, was in die Datei soll
	 * @param string $open Datei-Modus
	 *
	 * @return bool
	 */
	public function write_to_file($file = "", $inhalt = "", $open = "w+")
	{
		if (empty($inhalt) or empty($file) or !is_string($file)) {
			return false;
		}
		else {
			// Datei in der geschrieben werden soll. (entnehmen von doppelten backslashes)
			$filex = PAPOO_ABS_PFAD . $file;
			$filex = str_replace("//", "/", $filex);
			// Datei öffnen
			$file = fopen($filex, $open);
			// Encoding der momentanen Inhalts prüfen
			$enc = mb_detect_encoding($inhalt);
			// Den momentanen Inhalt zu UTF-8 konvertieren (Falls nicht bereits passiert)
			$inhalt = mb_convert_encoding($inhalt, "UTF-8", $enc);
			// In Datei schreiben
			@fwrite($file, $inhalt);
			// Datei schließen
			@fclose($file);
			return true;
		}
	}

	/**
	 * Datei löschen
	 *
	 * VORSICHT KEINE ÜBERPRÜFUNG
	 *
	 * @param string $file Dateiname
	 *
	 * @return void|bool
	 */
	public function delete_file($file = "")
	{
		if (empty($file) or !is_string($file)) {
			return false;
		}
		else {
			$filex = PAPOO_ABS_PFAD . $file;
			$this->remove_files($filex, ".*");
			$this->rec_rmdir($filex);
			unlink($filex);
		}
	}

	/**
	 * automatisch Links erstellen
	 * Original aus markdown.php
	 *
	 * @param string $text
	 *
	 * @return mixed|string|string[]|null
	 */
	public function dolink($text)
	{
		$pattern = '#(^|[^\"=]{1})(http://|ftp://|news:)([^\s<>]+)([\s\n<>]|$)#sm';
		$text = preg_replace($pattern, "\\1<a href=\"\\2\\3\">\\2\\3</a>\\4", $text);
		$text = str_ireplace(
			"(([a-z0-9_]|\-|\.)+@([^[:space:]]*)([[:alnum:]-]))",
			"<a href=\"mailto:\\1\">\\1</a>",
			$text
		);
		return $text;
	}

	/**
	 * erkennt ob $text ein externer Link ist
	 *
	 * @param string $text
	 *
	 * @return bool
	 */
	public function is_externlink($text = "")
	{
		$the_return = false;

		if(preg_match('/(?:https?|s?ftp?s):\/\//i', $text)) {
			$the_return = true;
		}

		if($text['0']=="/") {
			$the_return = true;
		}

		return $the_return;
	}

	/**
	 * erkennt ob $text ein externer Link ist
	 *
	 * @param string $text
	 *
	 * @return bool
	 */
	public function is_externlink_bread($text = "")
	{
		$the_return = $this->is_externlink($text);

		if (!empty($this->cms->title) and stristr($text, $this->cms->title)) {
			$the_return = false;
		}

		return $the_return;
	}

	/**
	 * Löscht alle Dateien im Cache Verzeichnis in denen $url vorkommt
	 *
	 * @param string $url
	 *
	 * @return void
	 */
	public function remove_cache_file($url)
	{
		$pfad = PAPOO_ABS_PFAD . "/cache/";
		if ($pfad && $url) {
			$handle = @opendir($pfad);
			while (false !== ($file = @readdir($handle))) {
				if (!empty($url)) {
					if (stristr($file, $url)) {
						@unlink($pfad . $file);
					}
				}
			}
			@closedir($handle);
		}
	}

	/**
	 * Löscht alle Dateien im Verzeichnis "$pfad" mit der Endung "$extension".
	 * Ist $extension ".*", werden ALLE Dateien des Verzeichnisses gelöscht.
	 *
	 * @param string $pfad
	 * @param string $extension Dateiendung
	 *
	 * @return void
	 */
	public function remove_files($pfad = "", $extension = "")
	{
		if ($pfad && $extension && is_dir($pfad)) {
			$handle = opendir($pfad);
			while (false !== ($file = readdir($handle))) {
				switch ($extension) {
				case ".*" :
					if ($file != "." && $file != ".." && $file != '.gitkeep') {
						unlink($pfad . $file);
					}
					break;

				default :
					if (stristr($file, $extension)) {
						unlink($pfad . $file);
					}
					break;
				}
			}
			closedir($handle);
		}
	}

	/**
	 * Loesche ein Verzeichnis rekursiv
	 *
	 * Quelle:http://aktuell.de.selfhtml.org/tippstricks/php/verzeichnisse/
	 *
	 * @param string $path
	 *
	 * @return int 0 bei Erfolg
	 */
	public function rec_rmdir($path)
	{
		if (!is_dir($path)) {
			return -1;
		}

		$dir = @opendir($path);
		if (!$dir) {
			return -2;
		}

		while (true) {
			$entry = @readdir($dir);
			if ($entry === false) {
				break;
			}
			// wenn der Eintrag das aktuelle Verzeichnis oder das Elternverzeichnis ist, ignoriere es
			if (in_array($entry, ['.', '..'])) {
				continue;
			}
			// wenn der Eintrag ein Verzeichnis ist, dann
			if (is_dir($path . '/' . $entry)) {
				// rufe dich rekursiv auf
				if ($this->rec_rmdir($path . '/' . $entry) !== 0) {
					@closedir($dir);
				}
			}
			else if (is_file($path . '/' . $entry) || is_link($path . '/' . $entry)) {
				// ansonsten loesche diese Datei / diesen Link
				if (!@unlink($path . '/' . $entry)) {
					@closedir($dir);
					return -2;
				}
			}
			else {
				// ein nicht unterstuetzer Dateityp
				@closedir($dir);
				return -3;
			}
		}
		@closedir($dir);
		if (!@rmdir($path)) {
			return -2;
		}

		return 0;
	}

	/**
	 * Erzeugt aus $name einen "sicheren" Dateinamen (ohne Umlaute etc.). $name sollte OHNE Pfad Übergeben werden.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function sicherer_dateiname($name)
	{
		$name_org = $name_neu = $endung = "";
		$ersetzungen = [
			"ä" => "ae",
			"Ä" => "Ae",
			"ö" => "oe",
			"Ö" => "Oe",
			"ü" => "ue",
			"Ü" => "Ue",
			"ß" => "ss",
			"_" => "-",
			" " => "-",
		];
		// Trennung in Name $name_org und Endung $endung
		if (strpos($name, ".")) {
			for ($i = (strlen($name) - 1); $i >= 0; $i--) {
				if ($name[$i] != ".") {
					$endung = $name[$i] . $endung;
				}
				else {
					$endung = "." . $endung;
					$name_org = substr($name, 0, $i);
					$i = -1;
				}
			}
		}
		else {
			$name_org = $name;
		}
		// Ersetzungen im Namen $name_org durchführen
		foreach ($ersetzungen as $search => $replace) {
			$name_org = preg_replace("/" . preg_quote($search) . "/", $replace, $name_org);
		}
		// Restliche "seltsame" Zeichen aus dem Namen $name_org entfernen und in neuem Namen $name_neu speichern
		for ($i = 0; $i < strlen($name_org); $i++) {
			if (preg_match('/[a-zA-Z0-9-]/', $name_org[$i])) {
				$name_neu .= $name_org[$i];
			}
		}
		// Neuer Name mit Endung zusammensetzen und zurückgeben
		$name_neu .= $endung;
		return $name_neu;
	}

	/**
	 * Ersetzt <br />-Tags
	 *
	 * @param string $text
	 * @param string $modus
	 *
	 * @return string
	 */
	public function recode_entity_n_br($text = "", $modus = "")
	{
		$return_text = "";

		if (!empty ($text)) {
			if ($modus == "nobr") {
				// erst die Zeilenumbrüche etc. entfernen
				$text = str_replace(["\n", "\r", "\t",], "", $text);
			}
			// Dann <br>s in Zeilenumbrüche umwandeln
			$text = str_ireplace("<br [^>]{1,}>", "\n", $text);
			// Der Original-Text wird gesplittet in Tag-Teil(e) und Text-Teil(e),
			// damit Auszeichnungen innerhalb von Tags erhalten bleiben
			$tag_array = $this->replace->split_text_tags($text);

			// Jetzt kommt die eigentliche Rück-Umwandlung
			foreach ($tag_array as $eintrag) {
				// 1. "Nonsense-Tag" = erster Textteil
				if ($eintrag[1] == "<nonsensetag>") {
					$return_text .= $eintrag[2];
				}
				// 2. Einträge innerhalb des Tags nicht umwandeln (nur bereinigen), "normaler" Text aber schon
				else {
					$return_text .= $this->removeEmptyStyleAttributes($eintrag[1]) . $eintrag[2];
				}
			}
		}

		return $return_text;
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public function encode_quote($text = "")
	{
		return str_replace(['"', "'"], ['&quot;', '&lsquo;'], $text);
	}

	/**
	 * Entfernt leere style-Attribute
	 *
	 * @deprecated verschwendete Rechenzeit, weil funktionell nichts am HTML geändert wird
	 *
	 * @param $text
	 *
	 * @return string
	 */
	public function removeEmptyStyleAttributes($text)
	{
		return str_replace(' style=""', "", $text);
	}

	/**
	 * @param string $output
	 *
	 * @return string
	 *
	 * TODO: System-Konfiguration um Einstellung "Youtube-Links durch Video ersetzen" erweitern, um dieses Feature zu de-/aktivieren.
	 */
	public function do_videos($output)
	{
		// Ersetze Youtube-Links, in denen kein weiteres HTML-Tag vorkommt
		return preg_replace_callback(
			'~<a\s[^>]*href="[^"]*youtube.com[^"]*(?:/v/|v=)(?<video_id>[\w-]{10,12})[^<]+</a>~',
			function ($match) {
				return '<div class="flex-video">'
					. '<iframe width="420" height="315" src="https://www.youtube.com/embed/' . $match["video_id"]
					. 'rel=0" frameborder="0" allowfullscreen></iframe>' . '</div>';
			},
			$output
		);
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public function do_pfadeanpassen($text = "")
	{
		if (!empty ($text)) {
			$needles = [
				'bilder/',
				'dokumente/',
				'forum/',
				'forum.php',
				'images/',
				'index/',
				'index.php',
				'js/',
				'plugins/',
				'plugin.php',
			];

			// Attributen, deren Wert mit einem der Strings aus $needles beginnt, wird der Papoo-Webpfad vorangestellt.
			// Es wird automatisch auf die Varianten `/{needle}`, `./{needle}` und `../{needle}` geprüft.
			foreach ($needles as $needle) {
				// Sowohl Apostrophe als auch Anführungszeichen bei der Suche berücksichtigen
				$pattern = '~(?<quote>\'|")\.{0,2}(?(?<=\.)/|/?)/?'.preg_quote($needle, '~').'~';

				// Ersetzung unter Berücksichtigung des String-Trennzeichens durchführen und den Webpfad voranstellen
				$text = preg_replace_callback($pattern, function ($match) use ($needle) {
					return $match['quote'].rtrim(PAPOO_WEB_PFAD, '/').'/'.$needle;
				}, $text);
			}

			// "Verzeichnis /video"-Suchmuster '"video/', erstmal raus wg. <video>  tag in HTML 5
			$suchmuster = ['src="/video/', 'src="./video/', 'src="../video/',];
			$text = str_replace($suchmuster, 'src="' . $this->webverzeichnis . '/video/', $text);

			$suchmuster = ['"startimage=./', 'startimage=../',];
			$text = str_replace($suchmuster, 'startimage=' . $this->webverzeichnis . '/', $text);

			$text = str_replace("../../../../js/", '' . $this->webverzeichnis . '/js/', $text);

			// korrekten Webpfad für interne Links gewährleisten
			$text = preg_replace(
				'#(<(?:form|area|img|a)\s[^>]*(?<=\s)(?:action|href|src)=")(?!' . PAPOO_WEB_PFAD . '/)(/[^"]*)#',
				'\1' . PAPOO_WEB_PFAD . '\2',
				$text
			);
		}

		return $text;
	}

	/**
	 * @deprecated kein Mehrwert
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public function do_lightbox($text)
	{
		return $text;
	}

	/**
	 * Log Funktion für verschiedene Fälle
	 * Email, Login etc.
	 *
	 * @param string $logart     Was soll geloggt werden Email|Login|etc
	 * @param string|array $logdetails Inhalt des logs
	 *
	 * @return void
	 */
	public function do_log($logart, $logdetails)
	{
		// check ob Dateien ok
		$logok = $this->check_log();
		// Dateien sind ok, dann loggen
		if ($logok) {
			switch ($logart) {
				// Email
			case "email" :
				// Log erzeugen
				$this->log_email($logdetails);
				break;
				// Login
			case "login" :
				// Log erzeugen
				$this->log_login($logdetails);
				break;
			case "debug" :
				// Log erzeugen
				$this->log_debugging($logdetails);
				break;
			}
		}
	}

	/**
	 * Überprüft, ob die Datei größer als 6MiB ist
	 * Wenn ja dann umbenennen mit Zeitstempel
	 *
	 * @param string $filename Die Datei, die geprüft werden soll
	 *
	 * @return void
	 */
	function check_filesize($filename)
	{
		// Größe holen
		$size = @filesize($filename);
		// Wenn Datei nicht existiert/nicht zugreifbar ist, abbrechen
		if ($size === false) {
			return;
		}
		// Ansonsten Größe in MB checken und ggf. umbenennen
		$sizemb = round($size / (1024 * 1024), 2);
		if ($sizemb > 6 && empty($_SESSION['isrenamed'])) {
			rename($filename, $filename . "_" . time());
			$_SESSION['isrenamed'] = 1;
		}
	}

	/**
	 *  Welche Logdatei gesplitted werden soll. (email|debug|http|login)
	 *
	 * @param string $log
	 */
	public function splitLogfileDaily($log)
	{
		$path = realpath(rtrim(PAPOO_ABS_PFAD, "/") . "/dokumente/logs");
		$filename = "$path/$log";

		if ($filename === false || !is_file($filename)) {
			return;
		}

		$date = file_get_contents($filename, false, null, 0, 10);

		if ($date === date("d.m.Y")) {
			return;
		}

		$suffix = time();
		if (preg_match('~(?<day>\d{2})\.(?<month>\d{2})\.(?<year>\d{4})~', $date, $match)) {
			$suffix = $match["year"] . $match["month"] . $match["day"];
		}

		$suffixedName = "$log~$suffix";

		// PHP 7.0 Code durch abwärtskompatiblen ersetzt
		$zip = class_exists("ZipArchive") ? new ZipArchive() : null;
		if (is_object($zip) && $zip->open($path . '/' . $suffixedName . ".zip", ZipArchive::CREATE) === true) {
			// Logdatei archivieren
			$zip->addFile($filename, "/" . $suffixedName);
			$zip->close();
			// Logdatei leeren
			file_put_contents($filename, "");
			@chmod("$path/$suffixedName.zip", 0666);
		}
		else {
			// Logdatei umbenennen und neu generieren
			if (rename($filename, $path . '/' . $suffixedName)) {
				file_put_contents($filename, "");
				@chmod($filename, 0666);
			}
		}
	}

	/**
	 * Alle versendeten Email loggen
	 *
	 * @param array $logdetails
	 */
	public function log_email($logdetails)
	{
		$this->splitLogfileDaily("email_log.log");

		// Logfile erstellen
		$this->check_filesize($this->filename . "email_log.log");
		$filename2 = $this->filename . "email_log.log";
		// Zuweisen der HTTP Header Daten IP etc.
		$inhalt = date("d.m.Y - H:i:s; ");
		$inhalt .= $_SERVER['REMOTE_ADDR'];

		$inhalt .= "; ";
		foreach ($logdetails as $detail) {
			$inhalt .= $detail['emailerror'] . "; ";
			$inhalt .= "to: " . $detail['mailto'] . "; ";
			$inhalt .= "cc: " . $detail['mailcc'] . "; ";
			$inhalt .= "from: " . $detail['mailfrom'] . "; ";
			$inhalt .= "from_text: " . $detail['mailfromtext'] . "; ";
			$inhalt .= "from_name: " . $detail['emailfromname'] . "; ";
			$inhalt .= "subject: " . $detail['emailsubject'] . "; ";
			$inhalt .= "sender: " . $detail['emailsend'];
		}
		$inhalt .= "; ";
		$inhalt .= $_SERVER['HTTP_USER_AGENT'];
		$inhalt .= "\r";
		// Inhalte aus den Logdetails zuweisen
		$file = fopen($filename2, "a");
		@fwrite($file, $inhalt);
		@fclose($file);
	}

	/**
	 * Debug logging
	 *
	 * @param array $logdetails
	 */
	public function log_debugging($logdetails)
	{
		$this->splitLogfileDaily("debug_log.log");

		// Logfile erstellen
		$this->check_filesize($this->filename . "debug_log.log");
		$filename2 = $this->filename . "debug_log.log";
		// Zuweisen der HTTP Header Daten IP etc.
		$inhalt = date("d.m.Y - H:i:s; ");
		$inhalt .= $_SERVER['REMOTE_ADDR'];

		$inhalt .= "; ";
		$inhalt .= $_SERVER['REQUEST_URI'];

		$inhalt .= "; ";
		foreach ($logdetails as $key => $value) {
			$inhalt .= $key . ':' . $value . '; ';
		}
		$inhalt .= $_SERVER['HTTP_USER_AGENT'];
		$inhalt .= "\r\n";
		// Inhalte aus den Logdetails zuweisen
		$file = fopen($filename2, "a");
		@fwrite($file, $inhalt);
		@fclose($file);
	}

	/**
	 *
	 */
	public function log_zugriff()
	{
		$this->splitLogfileDaily("http_log.log");

		// Logfile erstellen
		$this->check_filesize(PAPOO_ABS_PFAD . "/dokumente/logs/" . "http_log.log");
		$filename2 = PAPOO_ABS_PFAD . "/dokumente/logs/" . "http_log.log";
		// Zuweisen der HTTP Header Daten IP etc.
		$inhalt = date("d.m.Y - H:i:s; ");
		$inhalt .= $_SERVER['REMOTE_ADDR'] . "; ";
		$inhalt .= $_SERVER['REQUEST_URI'] . "; " . "; ";
		$inhalt .= $_SERVER['HTTP_USER_AGENT'];
		$inhalt .= "\r";
		// Inhalte aus den Logdetails zuweisen
		$file = fopen($filename2, "a");
		@fwrite($file, $inhalt);
		@fclose($file);
	}

	/**
	 * Alle Login Versuche loggen
	 *
	 * @param array $logdetails
	 */
	public function log_login($logdetails)
	{
		$this->splitLogfileDaily("login_log.log");

		// Logfile erstellen
		$this->check_filesize($this->filename . "login_log.log");
		$filename2 = $this->filename . "login_log.log";
		// Zuweisen der HTTP Header Daten IP etc.
		$inhalt = date("d.m.Y - H:i:s; ");
		$inhalt .= $_SERVER['REMOTE_ADDR'];
		$inhalt .= "; ";
		foreach ($logdetails as $detail) {
			$inhalt .= "" . $detail['username'];
			$inhalt .= "; ";
			$inhalt .= "" . $detail['userok'];
			$inhalt .= "; ";
			$inhalt .= "" . $detail['exist'];
			$inhalt .= "; ";
		}
		$inhalt .= "; ";
		$inhalt .= $_SERVER['HTTP_USER_AGENT'];
		$inhalt .= "\r";
		// Inhalte aus den Logdetails zuweisen
		$file = fopen($filename2, "a");
		@fwrite($file, $inhalt);
		@fclose($file);
	}

	/**
	 * diverse_class::log_404()
	 */
	public function log_404()
	{
		$this->splitLogfileDaily("404.log");

		// Logfile erstellen
		$filename = $this->filename . "404.log";

		// Zuweisen der HTTP Header Daten IP etc.
		$inhalt = sprintf(
				'%1$s; %2$s; %3$s; %4$s',
				date("d.m.Y - H:i:s"),
				"404 Fehler durch Aufruf von; " . $_SERVER['REQUEST_URI'],
				"Verlinkt von; " . $_SERVER['HTTP_REFERER'],
				$_SERVER['HTTP_USER_AGENT']
			) . "\r\n";

		// Inhalte aus den Logdetails zuweisen
		$file = fopen($filename, "a");
		fwrite($file, $inhalt);
		fclose($file);

		$this->check_filesize($this->filename . "404.log");
	}

	/**
	 * Alle Login Versuche loggen
	 */
	public function log_hijack()
	{
		$filename = $this->filename . "hijack_log.log";

		$inhalt = sprintf(
				'%1$s; %2%s; %3$s;',
				date("d.m.Y - H:i:s"),
				"Remote-IP: " . $_SERVER['REMOTE_ADDR'],
				"URI: " . $_SERVER["REQUEST_URI"]
			) . "\r\n";

		// Inhalte aus den Logdetails zuweisen
		$file = fopen($filename, 'a');
		fwrite($file, $inhalt);
		fclose($file);

		$this->check_filesize($filename);
	}

	/**
	 * Prüft, ob das logs-Verzeichnis existiert. Wenn nicht, wird es angelegt.
	 * Das logs-Verzeichnis befindet sich in /dokumente
	 * Dabei wird noch eine .htaccess erstellt, um jeglichen Zugriff von außen zu sperren.
	 *
	 * @return bool
	 */
	public function check_log()
	{
		$okDir = $okHt = false;
		$this->filename = $logDir = PAPOO_ABS_PFAD . '/dokumente/logs/';
		$htaccess = PAPOO_ABS_PFAD . '/dokumente/logs/.htaccess';
		// Überprüfen, ob das logs-Verzeichnis besteht
		if (!is_dir($logDir)) {
			// Logs Verzeichnis exitiert nicht, also anlegen.
			mkdir($logDir);
		}
		else {
			$okDir = true;
		}
		// Überprüfen, ob die .htaccess Datei existiert
		if (!file_exists($htaccess)) {
			// .htaccess Datei anlegen, damit der Inhalt von außen nicht gelesen werden kann
			file_put_contents($logDir . ".htaccess", "DENY FROM ALL", LOCK_EX);
		}
		else {
			$okHt = true;
		}
		// LogVerzeichniss existiert und htaccess auch
		return $okDir and $okHt;
	}

	/**
	 * Liste der User als CVS exportieren in einem .txt File
	 *
	 * @param string $mode
	 *
	 * @return string|void
	 */
	public function do_export($mode)
	{
		$sql = "";
		// Abfrage mit der gesuchten Gruppenid
		if (!empty($this->groupselect)) {
			$sql = sprintf(
				'SELECT * 
				FROM %1$s AS u INNER JOIN %2$s AS ug ON u.userid = ug.userid
				WHERE ug.gruppenid = %3$s',
				$this->cms->papoo_user,
				$this->cms->papoo_lookup_ug,
				$this->db->escape($this->groupselect)
			);
		}

		if (!empty($this->checked->search)) {
			$sql = sprintf(
				'SELECT * FROM %1$s WHERE username LIKE "%2$s"',
				$this->cms->papoo_user,
				'%' . $this->db->escape($this->checked->search) . '%'
			);
		}
		// einen bestimmten User raussuchen anhand der id
		if (!empty($this->userquery)) {
			$sql = sprintf(
				'SELECT * FROM %1$s WHERE username = "%2$s" LIMIT 1;',
				$this->cms->papoo_user,
				$this->db->escape($this->userquery)
			);
		}
		if (!empty($sql)) {
			$results = $this->db->get_results($sql);
			if ($mode == "xml") {
				$results = $this->db->get_results($sql, ARRAY_A);
			}
		}
		else {
			return;
		}

		$cvs = "";
		if (!empty($results)) {
			if ($mode != "xml") {
				foreach ($results as $erg) {
					foreach ($erg as $item) {
						$cvs .= $item . ",";
					}
					$cvs .= "\n\r";
				}
			}
			else {
				$cvs = $results;
			}
		}

		if (empty($mode)) {
			if (!empty ($cvs)) {
				$filenamecvs = "CVS_Papoo_benutzer" . time();
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Description: File Transfer");
				header("Accept-Ranges: bytes");
				header("Content-Type: text/plain");
				header('Content-Transfer-Encoding: binary');
				header('Content-Disposition: attachment; filename=' . $filenamecvs . '');
				echo $cvs;
				exit ();
			}
		}

		if (!empty($mode)) {
			return $cvs;
		}
	}

	/**
	 * Die Daten dumpen
	 *
	 * @param string $plugin
	 * @param string $pfad
	 * @param string $no
	 * @param string $structure
	 */

	public function extern_dump($plugin, $pfad = "", $no = "xyz", $structure = "")
	{
		global $intern_stamm;
		$this->intern_stamm = &$intern_stamm;
		if (isset($this->checked->makedump) && $this->checked->makedump == 1) {
			global $db_praefix;
			$this->lokal_db_praefix = $db_praefix;
			$table_list = [];
			$query = "SHOW TABLES";
			$result = $this->db->get_results($query);
			if ($result) {
				foreach ($result as $eintrag) {
					if ($eintrag) {
						foreach ($eintrag as $titel => $tabellenname) {
							if (strpos($tabellenname, $this->lokal_db_praefix) !== false) {
								if (empty ($plugin)) {
									$temp_array = [
										"tablename" => $tabellenname,
									];
									$table_list[] = $temp_array;
								}
								else {
									$plugin_list = explode(",", $plugin);
									foreach ($plugin_list as $tbn) {
										if (stristr($tabellenname, $tbn)) {
											$temp_array = [
												"tablename" => $tabellenname,
											];
											$table_list[] = $temp_array;
										}
									}
								}
							}
						}
					}
				}
			}
			if (!empty($structure)) {
				$this->intern_stamm->structure_dump = "ok";
			}

			$this->intern_stamm->make_dump($table_list, $pfad);
		}
		if (isset($this->checked->backdump) && $this->checked->backdump == 1) {
			$this->intern_stamm->make_restore();
		}
	}

	/**
	 * SQL Dateien erstellen
	 *
	 * @param string $plugin
	 * @param string $pfad
	 * @param string $no
	 */
	public function make_sql($plugin = "test", $pfad = "no", $no = "xyz")
	{
		$this->checked->makedump = 1;
		// ->update
		$this->dumpnrestore->update = 1;
		$this->dumpnrestore->multi = 1;

		foreach ($this->cms->tbname as $tbname) {
			if (stristr($tbname, $plugin)) {
				$this->extern_dump($tbname, $pfad, $no);
			}
		}
	}

	/**
	 * xml Daten aus url mit Datenbank abstimmen
	 *
	 * @param string $tablenames Die Tabellen die importiert werden sollen, kann leer sein
	 * @param string $not        welche nicht importiert werden sollen, kann leer sein
	 * @param string $urlx       - nicht verwendet -
	 * @param string $what       - nicht verwendet -
	 *
	 * @return void
	 */
	public function get_sql($tablenames = "", $not = "", $urlx = "", $what = "")
	{
		// Url rausholem
		// Daten ausgeben xml_result
		$sql = sprintf("SELECT * FROM %s", $this->cms->tbname['papoo_export_pref']);
		$url_result = $this->db->get_results($sql);
		$url = $url_result['0']->export_link;
		$url = str_ireplace('export.html', "export_sql.html", $url);
		$url = $url . "&submit=einloggen&username=" . $url_result['0']->export_username . "&password="
			. $url_result['0']->export_passwort . "&export=" . $tablenames;

		// globales Präfix für Tabellen-Namen als einheitliche Grundlage für Ersetzung durch lokales Tabellen-Präfix
		$this->global_db_praefix = "XXX_";
		global $db_praefix;
		$this->lokal_db_praefix = $db_praefix;

		// xml Klasse einbinden
		require_once(PAPOO_ABS_PFAD . "/lib/classes/extlib/Snoopy.class.inc.php");
		$html = new Snoopy();
		$html->fetch($url);
		$xml = $html->results;

		$this->do_update($xml, $tablenames, $not);
	}

	/**
	 * Überprüfen ob ein Eintrag irgendwo im Array ist
	 *
	 * @param mixed $value
	 * @param array $array
	 *
	 * @return bool
	 */
	public function deep_in_array($value, $array)
	{
		if (!empty ($array)) {
			foreach ($array as $item) {
				if (!is_array($item)) {
					if ($item == $value) {
						return true;
					}
					else {
						continue;
					}
				}

				if (in_array($value, $item)) {
					$this->isdeep_item = $item;
					return true;
				}
				else if ($this->deep_in_array($value, $item)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Update durchführen
	 *
	 * @param string $xml
	 * @param string $tbname
	 * @param string $not
	 */
	public function do_update($xml, $tbname, $not)
	{
		$sql_array = explode("##b_dump##", $xml);

		foreach ($sql_array as $sql) {
			$sql = str_ireplace("`" . $this->global_db_praefix, "`" . $this->lokal_db_praefix, $sql);
			$sql_check = explode("WHERE", $sql);
			$sql_check2 = explode("SET", $sql);

			if (
				!stristr($sql_check[0], $not)
				and !stristr($sql_check2[0], $not)
				and (stristr($sql_check[0], $tbname) or stristr($sql_check2[0], $tbname))
			) {
				$this->db->query(($sql));
			}
		}
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public function searchfield_text($text)
	{
		if (!empty($text)) {
			return '';
		}

		$text = str_replace(">", "> ", $text);
		$text = str_replace("\n", " ", $text);
		return strip_tags($text);
	}

	/**
	 * @param string $text
	 *
	 * @return array
	 */
	public function explode_text_lines($text = "")
	{
		if (empty($text)) {
			return [];
		}

		$text = strtolower($text);
		$text = str_replace(" ", "", $text);
		$text = str_replace(["\n\r", "\r\n", "\r"], "\n", $text);
		return explode("\n", $text);
	}

	/**
	 * @param $file
	 *
	 * @return string
	 */
	public function get_file_icon($file)
	{
		$types = [
			"pdf" => "pdf.png",
			"mp3" => "mp3.png",
			"doc" => "word.png",
			"docx" => "word.png",
			"odt" => "openofficeorg-20-writer.png",
			"ods" => "openofficeorg-20-calc.png",
			"odp" => "openofficeorg-20-impress.png",
			"zip" => "zip.png",
		];

		$extension = @array_pop(explode(".", $file));
		return $types[$extension] ?? "unknown_big.png";
	}

	/**
	 * diverse_class::http_redirect()
	 *
	 * Leitet auf eine andere URL weiter.
	 * Bei unvollständigen URLs gilt:
	 * Absolute Pfade sind relativ zum PAPOO_WEB_PFAD,
	 * Relative Pfade relativ zum aktuellen Pfad in der URL.
	 *
	 * Wenn $_SESSION['debug_stopallredirect'] wahr ist, wird die Weiterleitung nicht automatisch
	 * durchgeführt.
	 *
	 * @param string $url  URL
	 * @param int    $code HTTP-Statuscode (Standard: 307 (Temporary Redirect))
	 *
	 * @return void
	 */
	public function http_redirect($url = "", $code = 307)
	{
		$codetexts = [
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			307 => 'Temporary Redirect',
			308 => 'Permanent Redirect',
		];
		$codetext = isset($codetexts[$code]) ? $codetexts[$code] : 'Redirect';

		$https = !empty($_SERVER['HTTPS']);

		$i = strpos($url, '://');
		if ($i !== false and $i < 8) {
			$fullurl = $url;
		}
		else if ($url[0] == '/') {
			$fullurl = ($https ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . PAPOO_WEB_PFAD . $url;
		}
		else {
			$fullurl =
				($https ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/')
				. '/' . $url;
		}

		if (empty($_SESSION['debug_stopallredirect'])) {
			header('HTTP/1.1 ' . $code . ' ' . $codetext);
			header("Location: " . $fullurl);
		}
		else {
			echo '<!-- ' . $code . ' ' . $codetext . ' -->';
		}
		echo '<html><body><a href="' . htmlspecialchars($fullurl) . '">'
			. $this->content->template['plugin']['mv']['weiter'] . '</a></body></html>';
		exit();
	}

	/**
	 * Allen links, die auf den Menüpunkt 1 (Startseite) zeigen, die URL zurechtstutzen,
	 * um duplicate content zu vermeiden
	 *
	 * @return bool|void
	 */
	public function fix_menu_one_links()
	{
		global $output, $content;
		$template = &$content->template;

		// dom-Objekt erzeugen
		/** @see http://simplehtmldom.sourceforge.net/manual.htm */
		$dom = str_get_html(
			$output,
			$lowercase = true,
			$forceTagsClosed = true,
			$target_charset = DEFAULT_TARGET_CHARSET,
			$stripRN = false,
			$defaultBRText = DEFAULT_BR_TEXT,
			$defaultSpanText = DEFAULT_SPAN_TEXT
		);

		// output zu lang oder startseite inaktiv
		if ($dom === false || $template["menu_data"][0]["menuid"] != 1) {
			return false;
		}

		foreach ($dom->find('a') as $link) {
			if ($template['free_sp_urls']) {
				if ($link->href == PAPOO_WEB_PFAD . $template['menu_data'][0]['url_menuname']) {
					$link->href = PAPOO_WEB_PFAD . '/';
					if ($template['lang_short'] != $template['lang_front_default']) {
						$link->href .= PAPOO_WEB_PFAD . '/' . $template['lang_short'] . "/";
						$link->href = preg_replace('~/{2,}~', '/', $link->href);
					}
				}
			}
			else if ($link->href == PAPOO_WEB_PFAD . "/index.php?menuid=1") {
				$link->href = PAPOO_WEB_PFAD . '/';
			}
		}

		// dom-Objekt zurückspielen
		$output = $dom->save();
	}

	/**
	 * Nasty Workaround um die Sprachlinks auf der Startseite zu korrigieren
	 *
	 * @return void
	 */
	public function fix_language_links()
	{
		global $content;
		$template = &$content->template;

		if ($template['free_sp_urls'] && $template['aktive_menuid'] == 1) {
			foreach ($template["languageget"] as $k => $v) {
				$template["languageget"][$k]["lang_link"] = rtrim(PAPOO_WEB_PFAD, "/") . "/"
					. ($template["lang_front_default"] === $template["languageget"][$k]["lang_short"]
						? "" : $template["languageget"][$k]["lang_short"] . "/");
			}
		}
	}

	/**
	 * @param $output
	 * @return mixed
	 */
	public function placeholders($output)
	{
		return str_replace("#webverzeichnis#", PAPOO_WEB_PFAD, $output);
	}

	/**
	 * @param string $vatId
	 * @return bool
	 */
	public function validateVatId(string $vatId): bool
	{
		$pattern = '~^(
			ATU\d{8} |                                  # Austria
			BE\d{10} |                                  # Belgium
			BG\d{9,10} |                                # Bulgaria
			CY\d{8}[a-z] |                              # Cyprus
			CZ\d{8,10} |                                # Czech Republic
			DE\d{9} |                                   # Germany
			DK\d{8} |                                   # Denmark
			EE\d{9} |                                   # Estonia
			GR\d{9} |                                   # Greece
			ES(?(?=\d).\d{7}[a-z]|[a-z]\d{7}[a-z\d]) |  # Spain
			FI\d{8} |                                   # Finland
			FR[a-z\d]{2}\d{9} |                         # France
			GB(\d{9}|\d{12}|[a-z]{2}\d{3}) |            # United Kingdom
			HU\d{8} |                                   # Hungary
			IE(\d[a-z\d]\d{5}[a-z]|\d{7}[a-w][a-i]) |   # Ireland
			IT\d{11} |                                  # Italy
			LT(\d{9}|\d{12}) |                          # Lithuania
			LU\d{8} |                                   # Luxembourg
			LV\d{11} |                                  # Latvia
			MT\d{8} |                                   # Malta
			NL\d{9}B\d{2} |                             # Netherlands
			PL\d{10} |                                  # Poland
			PT\d{9} |                                   # Portugal
			RO\d{2,10} |                                # Romania
			SE\d{12} |                                  # Sweden
			SI\d{8} |                                   # Slovenia
			SK\d{10}                                    # Slovakia
			)$~xi';

		$subject = preg_replace('~[^a-z0-9]~', '', strtolower($vatId));

		return (bool)preg_match($pattern, $subject);
	}

	/**
	 * @param string $html
	 * @return string
	 */
	public function injectCsrfTokenIntoForms(string $html): string
	{
		// Textareas vorübergehend verschleiern, damit das CSRF-Token nicht in TinyMCE-Instanzen inkludiert wird
		$textareas = [];
		$html = preg_replace_callback(
			'~<(?<tag>(?(R)[^\s/>]+|textarea(?=[\s/>])))(?>[^>]*)(?<sc>(?<=/))?>(?<fot>(?>[^<]*)(?(?=<!--)(?>.+?-->)(?>(?&fot)*)))(?(sc)|(?R)*</\g{tag}>(?(R)(?>(?&fot)*)|))~i',
			function ($match) use (&$textareas) {
				$id = uniqid('textarea__');
				$textareas[] = [
					'id' => $id,
					'html' => $match[0],
				];
				return $id;
			},
			$html
		) ?? $html;

		// CSRF-Token integrieren
		$html = str_ireplace('</form>', '<input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'"/></form>', $html);

		// Textareas wiederherstellen
		$html = array_reduce($textareas, function ($html, $textarea) {
			return str_replace($textarea['id'], $textarea['html'], $html);
		}, $html);

		return $html;
	}
}

$diverse = new diverse_class();
