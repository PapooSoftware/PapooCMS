<?php
// ***************************************************************************
// * 
// * INFORMATIONS
// * ===============
// * 		Bindet die folgenden Klassen zur Ausgabe von Server-Informationen ein
// * 
// * class array2xml_class:
// *       wandelt ein Mehrdimensionales Array in einen XML-Baum um.
// * 
// * 
// * Stand: 22.06.2004
// * (c):   Stephan Bergmann
// *        S[dot]Bergmann[at]XiMiX[dot]de
// ***************************************************************************

/**
 * Class informations2
 */
class informations2
{
	/**
	 * informations2 constructor.
	 */
	function __construct()
	{
		$this->array2xml = new array2xml_class;
	}
}

/**
 * Class array2xml_class
 */
class array2xml_class
{
	/** @var string  */
	var $xml_name;
	/** @var string  */
	var $zeilenumbruch;
	/** @var  */
	var $xml_kopf;

	/**
	 * array2xml_class constructor. setzt Default-Werte.
	 */
	function __construct()
	{
		$this->xml_name = "data";
		$this->zeilenumbruch = "\r\n";
		#$fragezeichen = "?";
		#$this->xml_kopf = "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ".$fragezeichen.">".$this->zeilenumbruch;
	}

	/**
	 * Konvertiert ein mehrdimensionales Array in ein XML-Baum
	 * @param int $the_array
	 * @param int $level
	 * @param string $upper_name
	 * @return bool|string
	 */
	function convert($the_array = 0, $level = 0, $upper_name = "")
	{
		if (is_array($the_array)) {
			if ($level == 0 && !$upper_name) {
				$temp_xml = $this->xml_kopf."<".$this->xml_name.">".$this->zeilenumbruch;
			}
			else {
				$temp_xml = "";
			}
			$tabs = $this->level_tabs($level);

			if (!empty($the_array)) {
				foreach ($the_array as $name => $wert) {
					if ($upper_name) {
						$name = $upper_name;
					}
					// Wenn $wert ein Array ist, in Rekursion schicken
					if (is_array($wert)) {
						if (is_string($name) && !is_array($wert[0])) {
							$temp_xml .= $tabs."<".$name.">".$this->zeilenumbruch.
								$this->convert($wert, ($level+1)).$tabs.
								"</".$name.">".$this->zeilenumbruch;
						}
						else {
							$temp_xml .= $this->convert($wert, $level, $name);
						}
					}
					else {
						// Strings in CDATA-Kapseln
						if (is_string($wert)) {
							$wert = "<![CDATA[".$wert."]]>";
						}
						$temp_xml .= $tabs."<".$name.">".$wert."</".$name.">".$this->zeilenumbruch;
					}
				}
			}

			if ($level == 0 && !$upper_name) {
				$temp_xml .= "</".$this->xml_name.">".$this->zeilenumbruch;
			}
			return $temp_xml;
		}
		else return false;
	}

	/**
	 * Fügt X = $level Tabulator-Zeichen aneinander um ein saubere Einrückung zu erhalten (reine Kosmetik)
	 * @param int $level
	 * @return string
	 */
	function level_tabs($level = 0)
	{
		$tabs = "";
		for ($i = -1; $i < $level; $i++) {
			$tabs .= "\t";
		}
		return $tabs;
	}
}