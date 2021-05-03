<?php
/**
 * Diese Klasse übersetzt den übergebenden Inhalt in die gewünschte Sprache
 * Class transdeepl
 */

class transdeepl
{
	/**
	 * @var auth_key = Enthält den Api KEy für deepl
	 */
	private $auth_key = "xxx";

	/**
	 * Enthält den zu übersetzenden Text
	 * @var
	 */
	private $text;

	/**
	 * DIe Zielsprache
	 * derzeit möglich en, fr, es, pt, it, nl, pl, ru
	 * @var
	 */
	private $target_lang;

	/**
	 * Ursprungssprache hier immer de
	 * @var
	 */
	private $source_lang = "de";

	/**
	 * Formatierung erhalten - immer 1 = true
	 * @var
	 */
	private $preserve_formatting = 1;

	/**
	 * Sätze splitten = nonewlines
	 * @var
	 */
	private $split_sentences = "nonewlines";

	/**
	 * Immer xml damit die tags erhalten bleiben
	 * @var
	 */
	private $tag_handling = "xml";

	/**
	 * Die deeplurl die genutzt wird.
	 * BEispiel
	 * https://api.deepl.com/v2/translate?auth_key=".$auth_key."&text=beispiel&target_lang=en&source_lang=de&preserve_formatting=1&split_sentences=nonewlines&tag_handling=xml
	 * @var
	 */
	private $deepl_url;


	/**
	 * transdeepl constructor.
	 */
	public function __construct()
	{
		//die Basis Translateurl setzen
		$this->deepl_url = "https://api.deepl.com/v2/translate?auth_key=".$this->auth_key."&source_lang=".$this->source_lang."&preserve_formatting=".$this->preserve_formatting."&split_sentences=".$this->split_sentences."&tag_handling=".$this->tag_handling;

	}

	/**
	 *
	 */
	public function set_aut_key(){
		$this->auth_key = "xxx";

	}

	/**
	 * Text / HTML übersetzen
	 * @param string $target_lang
	 * @param string $text
	 * @return string übersetzter Text
	 */
	public function translate($target_lang="en",$text="")
	{
		$return['used_time'] 	= 	1;
		$return['trans_text'] 	= 	"TRANS - ".$text;

		//übersetzten Text zurückgeben
		return $return;
		//startzeit
		$start 					= microtime(true);

		//ini transtext
		$trans_text 			=	"";

		//contents kodieren damit nix schief geht
		$text 					= 	urlencode($text);
		$target_lang 			= 	urlencode($target_lang);

		//deepl urls vervollständigen

		$deepl_url 		= $this->deepl_url."&text=".$text."&target_lang=".$target_lang;

		//translate Aufruf durchführen
		$return_text 			= 	$this->curl($deepl_url);

		//aus dem json rausholen
		$ttext 					= 	json_decode($return_text,true);

		//den Text aus dem Array übergeben und escapes entfernen
		$trans_text 			= 	stripslashes($ttext['translations']['0']['text']);

		//ende
		$stop 					= 	microtime(true);

		//zeit die es gebraucht hat
		$difftime 				= 	$stop - $start;

		//Daten für Rückgabe aufbereiten
		$return['used_time'] 	= 	$difftime;
		$return['trans_text'] 	= 	$trans_text;

		//übersetzten Text zurückgeben
		return $return;
	}

	/**
	 * Gibt einen übersetzten Text zurück
	 * @return bool|string
	 */
	public function curl($deepl_url="")
	{
		$returndata ="";
		//print_r($this->deepl_url);
		//print_r("\n");

		$curl = curl_init($deepl_url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		$returndata = curl_exec($curl);
		$code = @curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
		$curlerror = @curl_error($curl);
		$curlerrno = @curl_errno($curl);

		return $returndata;
	}

	/**
	 * @param str $strlen
	 * @return float|int
	 */
	public function estimate_time($str="")
	{
		$time = strlen($str) / 100;
		return $time;
	}
}
