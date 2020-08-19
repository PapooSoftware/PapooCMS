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
 * Abstrakte Klasse um alle Variblen post/get anzusprechen
 * In dieser Klasse werden alle Variablen eingebunden die von außen kommen. D.h.
 * $_POST und $_GET werden als $this->checked->variablen_name eingebunden.
 *
 * Class checked_class
 */
class checked_class
{
	/**
	 * Alle Variablen aus $_POST und $_GET werden durchgeloopt und zugewiesen
	 *
	 * checked_class constructor.
	 */
	function __construct()
	{
		/*
		* Alle $_GET durchloopen die reinkommen
		* Die Variablen werden Überprüft ob sie numerisch, string oder Array sind
		*/
		foreach ($_GET as $key => $val) {
			/*
			 * Wenn der Inhalt numerisch ist einfach zuweisen
			 */
			if (is_numeric($val)) {
				$this->$key = $val;
				$_GET[$key] = $val;
			} /*
			* Wenn der Inhalt ein String ist String Überprüfung duchführen
			* d.h. Daten werden Datenbankishcer escaped.
			* striptags kann nicht ausgeführt werden, da mitunter auch HTML in der
			* Variable sein kann
			*/
			elseif (is_string($val)) {
				$this->$key = $this->check_xss($key, $val);
				$_GET[$key] = $this->check_xss($key, $val);
			} /*
			* Wenn der Inhalt ein Array ist zuweisen, Überprüfung muß dann
			* später stattfinden
			*/
			elseif (is_array($val)) {
				$this->$key = "0";
				$_GET[$key] = "0";
			} /*
			* Irgendwas unbekanntes
			*/
			else {
				$this->$key = "null";
				$_GET[$key] = "null";
			}
		}

		/*
		 * Alle $_POST durchloopen die reinkommen
		 * Die Variablen werden Überprüft ob sie numerisch, string oder Array sind
		 */
		foreach ($_POST as $key => $val) {
			/*
				  * Wenn der Inhalt numerisch ist einfach zuweisen
				  */
			if (is_numeric($val)) {
				$this->$key = $val;
				$_POST[$key] = $val;
			} /*
			* Wenn der Inhalt ein String ist String Überprüfung duchführen
			* d.h. Daten werden Datenbankishcer escaped.
			* striptags kann nicht ausgeführt werden, da mitunter auch HTML in der
			* Variable sein kann
			*/
			elseif (is_string($val)) {

				$this->$key = $this->check_xss_post($key, $val);
			} /*
			* Wenn der Inhaltein Array ist zuweisen, Überprüfung muß dann
			* später stattfinden
			*/
			elseif (is_array($val)) {
				$this->$key = $this->check_xss_array($key, $val);
				#$_POST[$key]=$this->$key;
			} /*
			* Irgendwas unbekanntes
			*/
			else {
				$this->$key = "null";
				#$_POST[$key]= "null";
			}
		}

		// TODO: Ist deprecated, sollte entfernt werden
		if (@get_magic_quotes_gpc()) {
			$this->remove_magicquotes();
		}
		// Nochmal checken
		$this->do_check();
	}

	/**
	 * Zwingende Überprüfungen auf int
	 */
	function do_check()
	{
		// vorgegebene auf Numerisch checken
		$check = array('menuid', 'reporeid', 'style', 'reporeid_print', 'forumid', 'rootid', 'msgid', 'selmenuid', 'page', 'reportage', 'image_id', 'id', 'video_id', 'cat_id', 'userid', 'gruppeid', 'mod_style_id', 'cform_id', 'style_id', 'downloadid', 'mv_id', 'mv_content_id', 'kal_id', 'monats_id');
		foreach ($check as $key => $var) {
			if (!empty($this->$var)) {
				if (!is_numeric($this->$var)) {
					$this->$var = "";
					$this->do_404 = true;
				}
			}
		}
		if (!empty($this->template)) {
			$this->template = str_ireplace("\.\.", "", $this->template);
		}
	}

	/**
	 * GET Strings auf xss checken
	 *
	 * @param string $key
	 * @param string $val
	 * @return mixed|string
	 */
	function check_xss($key, $val)
	{
		return $this->make_save_text($val);
	}

	/**
	 * POST Strings auf xss checken
	 *
	 * @param string $key
	 * @param string $val
	 * @return mixed|string
	 */
	function check_xss_post($key, $val)
	{
		if (!defined("admin")) {
			// Sonderregel fuer Frontend Passwort-Eingabe => Dort die Backend Methode verwenden.
			if ($key === "login" or $key === "password" or $key === "passwort") {
				$val = $this->make_save_text_post($val);
			} elseif ($key != "lan_article_sans" && $key != "inhalt" && $key != "lan_teaser" && $key != "header") {
				$val = $this->make_save_text($val);
			}
		}
		else {
			if ($key != "html" && $key != "myfile" && $key != "banner_code" && $key != "mv_template_one" && $key != "vhs_string" && $key != "wartungstext" && $key != "ctempl_content" && $key != "freiemodule_code" && $key != "einstellungen_lang_conversion_code_js" && $key != "usefulservices_analytics_key" && $key != "number_script" && $key != "ab_script") {
				$val = $this->make_save_text_post($val);
			}
		}

		// $this->$key=$val;
		return $val;
	}

	/**
	 * HTML etc. sicher entfernen oder escapen
	 *
	 * @param string $text
	 * @return mixed|string
	 */
	function make_save_text($text = "")
	{
		$text = strip_tags($text);
		$text = $this->html2txt($text);
		$text = $this->clean_db($text);
		return $text;
	}

	/**
	 * DB Hack Versuche ausfiltern
	 *
	 * @param $search
	 * @return mixed|string
	 */
	function clean_db($search)
	{
		//$search auf unerlaubte Zeichen überprüfen und evtl. bereinigen
		$search = trim(($search));
		$remove = "<>'\"%*\\";
		for ($i = 0; $i < strlen($remove); $i++) {
			$search = str_replace(substr($remove, $i, 1), "", $search);
		}
		return $search;
	}

	/**
	 * Scripts etc. sicher entfernen oder escapen
	 *
	 * @param string $text
	 * @return string|mixed
	 */
	function make_save_text_post($text = "")
	{
		$text = $this->html2txt_post($text);
		return $text;
	}

	/**
	 * HTML als Text machen
	 *
	 * @param $document
	 * @return string|string[]|null
	 */
	function html2txt($document)
	{
		$search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
			'@<[\\/\\!]*?[^<>]*?>@si', // Strip out HTML tags
			'@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
			'@<![\\s\\S]*?--[ \\t\\n\\r]*>@' // Strip multi-line comments including CDATA
		);
		return preg_replace($search, '', $document);
	}

	/**
	 * Scripts als Text machen
	 *
	 * @param $document
	 * @return string|string[]|null
	 */
	function html2txt_post($document)
	{
		$search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
		);
		return preg_replace($search, '', $document);
	}

	/**
	 * Überprüfen ob ein Eintrag irgendwo im Array ist
	 *
	 * @param string $okey
	 * @param $array
	 * @return array
	 */
	function check_xss_array($okey = "", $array)
	{
		$check = array('inhalt', 'teaser', '1', '2', '3', '4', '5', '6', '7');
		$neuar = array();

		if (!empty ($array)) {
			foreach ($array as $key => $item) {
				if (!is_array($item)) {
					if (!in_array($key, $check)) {
						$neuar[$key] = $this->check_xss_post($key, $item);
					}
					else {
						$neuar[$key] = $item;
					}
				}
				else {
					$neuar[$key] = $this->check_xss_array($key, $item);
				}
			}
		}
		return $neuar;
	}

	/**
	 * Bei aktivem MagicQuotes die Escape-Zeichen entfernen
	 */
	function remove_magicquotes()
	{
		// Elemente durchgehen
		if (!empty($this)) {
			foreach ($this as $name => $wert) {
				// Array-Element in die Rekursion schicken
				if (is_array($wert)) $this->$name = $this->remove_magicquotes_array($wert);
				// sonstige Werte mit Stripslasehs bearbeiten
				else $this->$name = stripslashes($wert);
			}
		}
	}

	/**
	 * Rekursive Funktion für die Abarbeitung von Arrays.
	 *
	 * @param $data
	 * @return array
	 */
	function remove_magicquotes_array($data)
	{
		$temp_array = array();
		// Elemente durchgehen
		if (!empty($data)) {
			foreach ($data as $name => $wert) {
				// Array-Element in die Rekursion schicken
				if (is_array($wert)) $temp_array[$name] = $this->remove_magicquotes_array($wert);
				// sonstige Werte mit Stripslasehs bearbeiten
				else $temp_array[$name] = stripslashes($wert);
			}
		}
		return $temp_array;
	}
}

$checked = new checked_class();