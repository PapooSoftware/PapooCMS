<?php

/**
 * ###########################################
 * # CMS Papoo                               #
 * # (c) Dr. Carsten Euwens 2016             #
 * # Authors: Lennart Haas, Christoph Grenz  #
 * # http://www.papoo.de                     #
 * # Internet                                #
 * ###########################################
 * # PHP Version >5.2                        #
 * ###########################################
 */

/**
 * cURL-Exception-Klasse
 *
 * getCode() liefert den cURL-Fehlercode
 * getMessage() liefert den cURL-Fehlerstring
 *
 * Ein Code von -1 gibt einen Fehler mit dem
 * cURL-Handle oder dem Konstruktor an.
 */
class curl_exception extends Exception {

	/**
	 * @return string
	 */
	public function __toString()
	{
		return "Curl error [".$this->getCode()."] '".$this->getMessage()."'";
	}
}

/**
 * Wrapper für die einfachen cURL-Funktionen
 *
 * Bei new wird ein neues cURL-Handle erzeugt
 * und im Destruktor automatisch geschlossen.
 */
class curl
{
	/** @var false|resource|null  */
	private $handle = null;

	/**
	 * @param string $url Initiale URL
	 * @throws curl_exception
	 */
	public function __construct($url = null)
	{
		// Fehler vermeiden bei FALSE, etc.
		if (!$url) {
			$url = null;
		}

		$this->handle = curl_init($url);
		if (!$this->handle) {
			throw new curl_exception("couldn't initialize curl", -1);
		}
	}

	/**
	 *
	 */
	public function __clone()
	{
		if (is_resource($this->handle)) {
			$this->handle = curl_copy_handle($this->handle);
		}
	}

	/**
	 * Destruktor
	 * Schließt das cURL-Handle
	 */
	public function __destruct()
	{
		if ($this->handle)
			curl_close($this->handle);
	}

	/**
	 * Generelles Setzen von cURL-Optionen
	 *
	 * @return mixed Mit CURLOPT_RETURNTRANSFER der Seiteninhalt, sonst true
	 * @throws curl_exception
	 */
	public function exec()
	{
		if (!$this->handle) {
			throw new curl_exception("invalid curl handle", -1);
		}

		$result = curl_exec($this->handle);
		if ($result === false) {
			$errno = curl_errno($this->handle);
			if ($errno != 0) {
				throw new curl_exception(curl_error($this->handle), $errno);
			}
		}
		return $result;
	}

	/**
	 * Informationen zur Verbindung abrufen
	 *
	 * @param int $infotype CURLINFO_-Konstante
	 * @return mixed Ergebnis
	 * @throws curl_exception
	 */
	public function getinfo($infotype = 0)
	{
		if (!$this->handle) {
			throw new curl_exception("invalid curl handle", -1);
		}
		return curl_getinfo($this->handle, $infotype);
	}

	/**
	 * Setzen von cURL-Optionen
	 *
	 * @param int $option CURLOPT_-Konstante
	 * @param mixed $value Wert für die Option
	 * @return void
	 * @throws curl_exception
	 */
	public function setopt($option, $value)
	{
		if (!$this->handle) {
			throw new curl_exception("invalid curl handle", -1);
		}

		if (!curl_setopt($this->handle, $option, $value)) {
			throw new curl_exception(curl_error($this->handle), curl_errno($this->handle));
		}
	}

	/**
	 * Setzen von mehreren cURL-Optionen auf einmal
	 *
	 * @param array $options [CURLOPT_-Konstante => Wert]
	 * @return void
	 * @throws curl_exception
	 */
	public function setopt_array($options)
	{
		if (!$this->handle) {
			throw new curl_exception("invalid curl handle", -1);
		}

		$result = curl_setopt_array($this->handle, $options);
		if (!$result) {
			throw new curl_exception(curl_error($this->handle), curl_errno($this->handle));
		}
	}

	/**
	 * Setzt alle Optionen auf Default und die URL zurück.
	 *
	 * @return void
	 * @throws curl_exception
	 */
	public function reset()
	{
		if (!$this->handle or !is_resource($this->handle)) {
			throw new curl_exception("invalid curl handle", -1);
		}
		curl_reset($this->handle);
	}

	/**
	 * Maskiert einen String für eine URL
	 * Ähnlich zu urlencode().
	 *
	 * @param string $string
	 * @return string
	 * @throws curl_exception
	 */
	public function escape($string)
	{
		if (!$this->handle) {
			throw new curl_exception("invalid curl handle", -1);
		}

		return curl_escape($this->handle, $string);
	}


	/**
	 * Dekodiert einen URL-kodierten String
	 * Ähnlich zu urldecode().
	 *
	 * @param string $string
	 * @return string
	 * @throws curl_exception
	 */
	public function unescape($string)
	{
		if (!$this->handle) {
			throw new curl_exception("invalid curl handle", -1);
		}
		return curl_unescape($this->handle, $string);
	}

	/**
	 * cURL-Version
	 *
	 * @return array
	 * @throws curl_exception Wenn cURL-Extension fehlt
	 */
	static public function version()
	{
		if (!function_exists('curl_version')) {
			throw new curl_exception("curl extension missing");
		}
		return curl_version();
	}

	/**
	 * Prüft ob die cURL-Extension aktiv ist
	 *
	 * @return bool
	 */
	static public function installed()
	{
		return function_exists('curl_exec');
	}
}
