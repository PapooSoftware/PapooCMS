<?php
/**
######################################
# CMS Papoo                          #
# (c) Dr. Carsten Euwens 2008        #
# Authors: Carsten Euwens            #
# http://www.papoo.de                #
# Internet                           #
######################################
# PHP Version 4.2                    #
######################################
 */

/**
 * Class content_class
 */
#[AllowDynamicProperties]
class content_class
{
	/** @var array beinhaltet Variablen für das Template */
	var $template;

	/** @var array $decodedNodes */
	private static $decodedNodes = [];

	/**
	 * content_class constructor.
	 */
	function __construct()
	{
		$this->template = array();
	}

	/**
	 * Template-Variablen der Template-Engine Smarty zuweisen
	 * @param bool $decode
	 */
	public function assign(bool $decode=true): void
	{
		global $smarty;
		global $template;

		// 1. Inhalte Codieren
		if ($decode) {
			$this->template = self::decode($this->template);
		}

		// 2. Template (HTML-Datei) Übergeben
		$this->template['_template_'] = $template;

		// 3. Zuweisung an Smarty
		$smarty->assign($this->template);
	}

	/**
	 * Inhalt eines Arrays umwandeln
	 *
	 * @param array $data
	 * @param array $nodeNames
	 * @return array
	 */
	private static function decode(array $data, array $nodeNames=[]): array
	{
		#return $data;
		$temp_array = array();

		if (is_array($data) && !empty($data)) {
			foreach($data as $name => $wert) {
				if (is_array($wert)) {
					// Die Rekursion
					$temp_array[$name] = self::decode($wert, array_merge($nodeNames, [$name]));
				}
				else {
					// Skip previously decoded values
					$nodeName = implode('.', array_merge($nodeNames, [$name]));
					if (self::$decodedNodes[$nodeName] ?? false and ($wert && !preg_match('~^[a-z]+:~', $wert))) {
						$temp_array[$name] = $wert;
					}

					// das eigentliche Decodieren, wobei nur Texte decodiert werden sollen
					elseif (is_string($wert)) {
						// nur Texte deocdieren, die nicht "nodecode:" sind
						if (strpos("xxx".$wert, "nodecode:") == 3) {
							$temp_array[$name] = substr_replace($wert, "", 0 , 9);
							// Umwandlung für Ausgabe "erwünschter Sonder-Zeichen"
							$temp_array[$name] = str_replace("&amp;", "&amp;amp;", $temp_array[$name]);
							$temp_array[$name] = str_replace("&lt;", "&amp;lt;", $temp_array[$name]);
							$temp_array[$name] = str_replace("&gt;", "&amp;gt;", $temp_array[$name]);
						}
						else {
							$nobr = false;
							if (strpos("xxx".$wert, "nobr:") == 3) {
								$nobr = true;
								$wert = substr_replace($wert, "", 0 , 5);
							}
							$temp_array[$name] = self::decode_text($wert, $nobr);
						}

						self::$decodedNodes[$name] = true;
					}
					else {
						$temp_array[$name] = $wert;
					}
				}
			}
		}
		return $temp_array;
	}

	/**
	 * @param string $text
	 * @param bool $nobr
	 * @return string
	 */
	private static function decode_text(string $text, bool $nobr): string
	{
		$neuer_text = preg_replace_callback('/((?:<[^>]+>?)?)((?(?<=>)[^<]+|))/', function ($match) use ($nobr) {
			// [alter code] Test für HTML-Auszeichnung "alleinstehender &s"
			$match[2] = str_replace("& ", "&amp; ", $match[2]);

			return self::decode_url($match[1]) . ($nobr ? $match[2] : nl2br($match[2]));
		}, $text);

		$neuer_text=preg_replace('/>[ ]{1,}([\.|\|\,|\;|\!|\?|:])/i','>$1',$neuer_text);

		// Sonderbehandlungen:
		// ===================
		//externe Links mit target=blank kodieren
		#$neuer_text = str_replace("href=\"http", " title=\"Linkziel liegt in einem neuen Fenster\" target=\"_blank\" href=\"http", $neuer_text);
		#global $cms;
		#$neuer_text = str_replace("target=\"_blank\" href=\"http://".$cms->title_send."", "target=\"_self\" href=\"http://".$cms->title_send, $neuer_text);

		// Kodierte Klammern zurückverwandeln
		$neuer_text = str_replace("classid=\"clsid:", "classid=\"clsid:clsid:", $neuer_text);
		//$neuer_text = str_replace("&amp;gt;", "&gt;", $neuer_text);

		//kodierte Entitäten zurückverwandeln &amp;#
		$neuer_text = str_replace("&amp;#", "&#", $neuer_text);

		//Gedankenstrich &amp;ndash;
		//$neuer_text = str_replace("&amp;ndash;", "&#150;", $neuer_text);

		// Euro-Zeichen &amp;euro;
		//$neuer_text = str_replace(chr(128), "&euro;", $neuer_text);
		//$neuer_text = str_replace("&amp;euro;", "&euro;", $neuer_text);

		// Text zurückgeben
		return $neuer_text;
	}

	/**
	 * ersetzt & durch &amp; in URLs, also in den Attributen href, src eines Tags
	 *
	 * @param string $tag
	 * @return string
	 */
	private static function decode_url(string $tag): string
	{
		// dieses Array wird von preg_match_all gefüllt.
		$tag_array = array();

		$match_expression = "/(.*?)(href=\"|src=\")(.*?)\"(.*)/i";
		// füllt $tag_array in der Art: 
		// $tag_array[0] = das gesmte Tag
		// $tag_array[1] = Tag-Teil vor href=" oder src="
		// $tag_array[2] = Das entsprechende Attribut (href bzw. src)
		// $tag_array[3] = Inhalt zwischen Anführungszeichen des href- bzw. src-Teils
		// $tag_array[4] = Tag-Teil nach schließenden Anführungszeichen von href=".." bzw. src=".."
		preg_match($match_expression, $tag, $tag_array);

		if(!empty($tag_array)) {
			$tag_array[3] = str_replace("&", "&amp;", $tag_array[3]);
			$tag_array[3] = str_replace("&amp;amp;", "&amp;", $tag_array[3]);
			$tag = $tag_array[1].$tag_array[2].$tag_array[3]."\"".$tag_array[4];
		}
		return $tag;
	}
}
$content= new content_class();