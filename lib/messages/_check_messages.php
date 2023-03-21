<?php
/*
	Diese Datei prüft zwei Sprach-Dateien auf fehlende Sprach-Definitionen.
	
	Es werden dabei die Einträge der Datei $datei_referenz mit denen der Datei $datei_test verglichen und fehlende
	Einträge in der Datei $datei_test, welche in $datei_refrenz definiert sind, in ein Array $differenz geschrieben.
*/

/**
 * Class check_messages_class
 */
#[AllowDynamicProperties]
class check_messages_class
{
	/** @var string Datei mit den Referenz Sprach-Definitionen */
	var $datei_referenz;
	/** @var string Datei mit den zu testenden Sprach-Definitionen */
	var $datei_test;

	/** @var string Datei in die Sprachdefinitionen gespeichert (exportiert) werden */
	var $datei_export;

	/** @var array Array mit Sprach-Definitionen der Referenz */
	var $referenz; //
	/** @var array Array mit Namen der Sprach-Definitionen der Referenz */
	var $referenz_namen;
	/** @var array Array mit Referenz-Wert für fehlende Sprach-Definition */
	var $referenz_werte;

	/** @var array Array mit Sprach-Definitionen der Test-Datei */
	var $test;
	/** @var array Array mit Namen der Sprach-Definitionen der Test-Datei */
	var $test_namen;

	/** @var array Array mit Namen der fehlenden Sprach-Definitionen der Test-Datei */
	var $differenz_namen;

	/** @var mixed Dummie für Sprach-Definitionen aus den Dateien */
	var $content;

	/**
	 * check_messages_class constructor.
	 */
	function __construct()
	{
		// Variablen initialisieren.
		$this->datei_referenz = "./messages_frontend_de.inc.php";
		$this->datei_test = "./messages_frontend_it.inc.php";
		
		$this->datei_export = "./_check_messages_export.inc.php";
		
		$this->referenz = array();
		$this->referenz_namen = array();
		$this->referenz_werte = array();
		$this->test = array();
		$this->test_namen = array();
		$this->differenz_namen = array();
		
		$this->content;
		
		$this->init();
		
		$this->export_datei($this->referenz_werte);
		$this->ausgabe_schirm($this->differenz_namen);
	}

	/**
	 *
	 */
	function init()
	{
		// 1. Referenz-Datei einbinden und Definitionen an $referenz Übergeben.
		require_once($this->datei_referenz);
		$this->referenz = $this->content->template;
		$this->content = "";
		
		foreach ($this->referenz as $name => $wert) {
			if (is_array($wert)) {
				$this->referenz_namen = array_merge($this->referenz_namen, $this->get_array_name($name, $wert, "REFERENZ"));
			}
			else {
				$this->referenz_namen[] = "['".$name."']";
				$this->referenz_werte["['".$name."']"] = $wert;
			}
		}
		
		// 2. Test-Datei einbinden und Definitionen an $test Übergeben.
		require_once($this->datei_test);
		$this->test = $this->content->template;
		$this->content = "";
		//print_r($this->test);
		
		foreach ($this->test as $name => $wert) {
			if (is_array($wert)) {
				$this->test_namen = array_merge($this->test_namen, $this->get_array_name($name, $wert));
			}
			else  {
				$this->test_namen[] = "['".$name."']";
			}
		}
		
		// 3. Referenz mit Test vergleichen
		foreach ($this->referenz_namen as $name) {
			if (!in_array($name, $this->test_namen)) {
				//echo "<span style=\"color: 993333;\">".$name." = \"".$this->referenz_werte[$name]."\";</span><br />\n";
				$this->differenz_namen[] = $name;
			}
		}
	}

	/**
	 * wenn $modus = "REFERENZ", dann $wert in $referenz_werte speichern
	 *
	 * @param $array_name
	 * @param $array_wert
	 * @param string $modus
	 * @return array
	 */
	function get_array_name ($array_name, $array_wert, $modus = "")
	{
		$temp_return = array();
		
		foreach ($array_wert as $name => $wert) {
			if (is_array($wert)) {
				$temp_return = array_merge($temp_return, $this->get_array_name($array_name."']['".$name, $wert, $modus));
			}
			else {
				$temp_return[] = "['".$array_name."']['".$name."']";
				$this->referenz_werte["['".$array_name."']['".$name."']"] = $wert;
			}
			
		}
		return $temp_return;
	}

	/**
	 * @param array $sprachdefinitionen
	 * @return bool
	 */
	function export_datei($sprachdefinitionen = array())
	{
		$the_return = true;
		if (empty($sprachdefinitionen)) {
			$the_return = false;
		}
		else {
			$datei = fopen($this->datei_export, "wb");
			foreach ($sprachdefinitionen as $name => $wert) {
				$eintrag = "/* ".$this->referenz_werte[$name]." */\n";
				$eintrag .= '$this->content->template'.$name.' = \''.$wert.'\';'."\n\n";
				fwrite($datei, $eintrag);
			}
			fclose($datei);
		}
		return $the_return;
	}

	/**
	 * @param array $sprachdefinitionen
	 */
	function ausgabe_schirm($sprachdefinitionen = array())
	{
		echo "<h1>Vergleich von Sprach-Dateien.</h1>";
		echo "<p>Referenz-Datei: &quot;".$this->datei_referenz."&quot;<br />Test-Datei &quot;".$this->datei_test."&quot;</p>";
		if (!empty($sprachdefinitionen)) {
			echo '<p style="color: #993333;">';
			echo "<strong>Folgende Definitionen fehlen in der Datei &quot;".$this->datei_test."&quot;:</strong><br />\n<br />\n";
			foreach ($sprachdefinitionen as $name) {
				echo '$this->content->template'.$name." = \"".nl2br($this->referenz_werte[$name])."\";<br />\n";
			}
			echo "</p>";
		}
		else '<p style="color: #339933;">Test-Datei &quot;".$this->datei_test."&quot; vollst&auml;ndig!</p>';
	}
}

header('Content-Type: text/html; charset=utf-8');
$checker = new check_messages_class();