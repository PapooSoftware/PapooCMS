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
class  content_class
{
	/** @var array template beinhaltet Variablen für das Template. */
	var $template; // template (Array) welches Variablen für das Template beinhaltet.

	/**
	 * content_class constructor.
	 */
	function __construct()
	{
		$this->template = array();
	}

	/**
	 * Template-Variablen der Template-Engine Smarty zuweisen
	 */
	function assign()
	{
		global $smarty;
		global $template;
		
		// 1. Inhalte Codieren
		$this->template = $this->decode($this->template);
		
		// 2. Template (HTML-Datei) übergeben
		$this->template['_template_'] = $template;
		
		/*
		// 3. Zuweisung an Smarty
		reset($this->template);
		//Array durchloopen
		while (list ($key, $val) = each($this->template))
		{
			$smarty->assign($key, $val);
		}
		*/
		$smarty->assign($this->template);
	}

	/**
	 * Inhalt eines Arrays umwandeln
	 *
	 * @param int $data
	 * @return array
	 */
	function decode($data = 0)
	{
		#return $data;
		$temp_array = array();
		
		if (is_array($data) && !empty($data)) {
			foreach($data as $name => $wert) {
				if (is_array($wert)) {
					// Die Rekursion
					$temp_array[$name] = $this->decode($wert);
				}
				else {
					// das eigentliche Decodieren, wobei nur Texte decodiert werden sollen
					if (is_string($wert)) {
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
							$temp_array[$name] = $this->decode_text($wert, $nobr);
						}
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
	 * @return mixed|string|string[]|null
	 */
	function decode_text($text = "", $nobr = false)
	{
		// hier kommt der decodierte Text rein
		$neuer_text = "";
		
		// muss gemacht werden damit Suchmuster korrekt arbeitet
		$text = "<nonsensetag>".$text;
		
		// dieses Array wird von preg_match_all gefüllt.
		$text_array = array();
		
		// Suchmuster: (<text_in_klammern>)(text_nach_klammern),
		// daher auch das nonsense-Tag um Texte am Anfang, also vor einem Tag zu erfassen
		$match_expression = "/(<[^>]{1,}>)([^<]{0,})/i";
		// füllt $text_array in der Art:
		//		$text_array[0] = Text des gesamten Suchmusters, also Tag + Text danach bis zum nächsten Tag
		//		$text_array[1] = das Tag (also der Text in Klammern incl. der Klammern)
		//		$text_array[2] = Text nach Tag bis zum nächsten Tag
		preg_match_all ($match_expression, $text, $text_array, PREG_SET_ORDER);
		
		// die Rückgabe zusammen stellen.
		foreach($text_array as $text_teil) {
			// Test für HTML-Auszeichnung "alleinstehender &s"
			$text_teil[2] = str_replace("& ", "&amp; ", $text_teil[2]);
			
			if ($text_teil[1] != "<nonsensetag>") {
				// & in href= bzw. src= durch &amp; ersetzen
				$text_teil[1] = $this->decode_url($text_teil[1]);
				
				// Tags nicht decodieren, den Text nach dem Tag aber schon
				if ($nobr) {
					$neuer_text .= $text_teil[1].$text_teil[2];
				}
				else {
					$neuer_text .= $text_teil[1].nl2br($text_teil[2]);
				}
			}
			// Text ganz am Anfang vor dem ersten Tag decodieren.
			else {
				if (!$nobr) {
					$neuer_text .= nl2br($text_teil[2]);
				}
				else {
					$neuer_text .= $text_teil[2];
				}
			}
			$neuer_text=preg_replace('/>[ ]{1,}([\.|\|\,|\;|\!|\?|:])/i','>$1',$neuer_text);
			//$neuer_text=preg_replace('/\> (\.|\|\,|\;|\!|\?|:)/','>$1',$neuer_text);
		}
		
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
		$neuer_text = str_replace("           ", " ", $neuer_text);
		
		// Euro-Zeichen &amp;euro;
		//$neuer_text = str_replace(chr(128), "&euro;", $neuer_text);
		//$neuer_text = str_replace("&amp;euro;", "&euro;", $neuer_text);
		
		// Text zurückgeben
		return $neuer_text;
	}

	/**
	 * ersetzt & durch &amp; in URLs, also in den Attributen href, src eines Tags
	 *
	 * @param $tag
	 * @return string
	 */
	function decode_url($tag)
	{
		// dieses Array wird von preg_match_all gefüllt.
		$tag_array = array();
		
		$match_expression = "/(.*?)(href=\"|src=\")(.*?)\"(.*)/i";
		// füllt $tag_array in der Art:
		//		$tag_array[0] = das gesmte Tag
		//		$tag_array[1] = Tag-Teil vor href=" oder src="
		//		$tag_array[2] = Das entsprechende Attribut (href bzw. src)
		//		$tag_array[3] = Inhalt zwischen Anführungszeichen des href- bzw. src-Teils
		//		$tag_array[4] = Tag-Teil nach schließenden Anführungszeichen von href=".." bzw. src=".."
		preg_match($match_expression, $tag, $tag_array);
		//print_r($tag_array);
		
		if(!empty($tag_array)) {
			$tag_array[3] = str_replace("&", "&amp;", $tag_array[3]);
			$tag_array[3] = str_replace("&amp;amp;", "&amp;", $tag_array[3]);
			$tag = $tag_array[1].$tag_array[2].$tag_array[3]."\"".$tag_array[4];
		}
		return $tag;
	}
	
}

$content= new content_class();