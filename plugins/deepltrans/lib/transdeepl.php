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
	 * Immer xml damit die tags erhalten bleiben
	 * @var
	 */
	private $ignore_tag = "nd";

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

	}

	public function setDeeplUrl()
	{
		//die Basis Translateurl setzen
		$this->deepl_url = "https://api.deepl.com/v2/translate?auth_key=".$this->auth_key."&source_lang=".$this->source_lang."&preserve_formatting=".$this->preserve_formatting."&split_sentences=".$this->split_sentences."&tag_handling=".$this->tag_handling."&ignore_tags=".$this->ignore_tag;
	}

	/**
	 *
	 */
	public function set_aut_key($key=""){
		$this->auth_key = $key;
	}

	/**
	 * Set To newlines - to preserve structure and linebreaks
	 * @param string $tf
	 */
	public function set_noNewlines($tf="nonewlines")
	{
		$this->split_sentences=$tf;
	}



	public function translateArray($target_lang="en",$text=array(),$delcomments=true)
	{

		//startzeit
		$start 					= microtime(true);

		//ini transtext
		$trans_text 			= array();

		$body 	 				= "";

		foreach($text as $sentence)
		{
			//kommentare entfernen
			if($delcomments)
			{
				$sentence				= 	preg_replace("~<!--(.*?)-->~s", "", $sentence);
			}
			$sentence 	 				= 	$this->escapePlaceholder($sentence);
			$sentence 	 				= 	$this->replaceAltUndCo($sentence,$target_lang);
			$body 						.= 	"&text=".urlencode($sentence);
		}

		//contents kodieren damit nix schief geht
		$target_lang 			= 	urlencode($target_lang);

		//deepl urls vervollständigen
		$deepl_url 		= $this->deepl_url."&target_lang=".$target_lang;

		//translate Aufruf durchführen
		$return_text 			= 	$this->curlFromDeepl($deepl_url,$body);

		//aus dem json rausholen
		$trans_text 			= 		json_decode($return_text,true);

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
	 * @param string $text
	 * @param $target_lang
	 * @return mixed|string|string[]|null
	 */
	public function replaceAltUndCo($text="",$target_lang)
	{
		$atrribute=array("alt","title","href");

		foreach($atrribute as $atr)
		{
			$matches 	= array();
			$trAlt 		= "";
			//alt across the system
			$pattern 	= '/'.$atr.'="(.*?)"+/';
			preg_match_all($pattern, $text,$matches);

			if(!empty($matches['1'])) {
				foreach ($matches['1'] as $mk=> $alt) {
					if(!stristr($alt,"#")) {
						$trans = $this->translate($target_lang, $alt, false);
						$transText = $trans['trans_text'];

						if ($atr == "href") {
							$transAlt = $atr . '="/' . strtolower($target_lang) . "" . trim($transText) . '"';
						} else {
							$transAlt = $atr . '="' . trim($transText) . '"';
						}

						if (!stristr($alt, "http")) {
							$text = str_ireplace($matches['0'][$mk], $transAlt, $text);
						}
					}
				}
				/**
				 * did not work really... deepl was to dump...
				 *
				$trans = $this->translate($target_lang, $trAlt, false);
				$transDats = explode("xxx", $trans['trans_text']);
				var_dump($transDats);

				if (!empty($matches['0'])) {
				foreach ($matches['0'] as $k => $alt) {
				if ($atr == "href") {
				$transAlt = $atr . '="/' . strtolower($target_lang) . "" . trim($transDats[$k]) . '"';
				} else {
				$transAlt = $atr . '="' . trim($transDats[$k]) . '"';
				}

				if (!stristr($alt, "http")) {
				$text = str_ireplace($alt, $transAlt, $text);
				}
				}
				}
				 * */
			}
		}
		//shop.php?menuid=247
		$text = str_ireplace("shop.php?menuid=247", "shop.php?menuid=247&getlang=".strtolower($target_lang), $text);
		$text = str_ireplace("getlang=de", "getlang=".strtolower($target_lang), $text);
		return $text;
	}

	/**
	 * @param string $text
	 * @return string|string[]|null
	 */
	public function escapePlaceholder($text="")
	{
		//Links in Flex
		$pattern = '/\$\$#(\S*?)#\$\$+/';
		$replacement ='<nd>$$#$1#$$</nd>';
		$text = preg_replace($pattern, $replacement,$text);

		//$text = 'als <a style="color: #fff !important; text-decoration: underline;" href="#mod_freiemodule_12">Download</a> oder #placeholder# und so gehts weiter...';
		//placeholder across the system
		$pattern = '/#(\S*?)#+/';
		$replacement ='<nd>#$1#</nd>';
		$text = preg_replace($pattern, $replacement,$text);
		//print_r($text);exit();

		//Some placeholder in flex....
		$pattern = '/\{(\S*?)\}+/';
		$replacement ='<nd>{$1}</nd>';
		$text = preg_replace($pattern, $replacement,$text);

		return $text;
	}

	public function unIgnore($text="")
	{
		$text = str_ireplace("<nd>","",$text);
		$text = str_ireplace("</nd>","",$text);
		return $text;
	}

	/**
	 * Text / HTML übersetzen
	 * @param string $target_lang
	 * @param string $text
	 * @return string übersetzter Text
	 */
	public function translate($target_lang="en",$text="",$noRekur=true)
	{
		//startzeit
		$start 					= microtime(true);

		//ini transtext
		$trans_text 			=	"";

		//contents kodieren damit nix schief geht und kommentare entfernen
		$text					= 	preg_replace("~<!--(.*?)-->~s", "", $text);
		$text 	 				= 	$this->escapePlaceholder($text);

		//only if not called from inside...
		if($noRekur)
		{
			$text 	 				= 	$this->replaceAltUndCo($text,$target_lang);
		}

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
	 * @param $deeplApiKey
	 * @param $body
	 * @param $targetLang
	 * @return mixed
	 */
	public static function curlFromDeepl($deepUrl,$body)
	{
		//Daten holen
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_URL, $deepUrl);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		$response = curl_exec($curl);

		return $response;
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
