<?php
/**
#####################################
# CMS Papoo                	#
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
#                                   #
# Class: pluginscss_class           #
# (c) Stephan Bergmann              #
#                                   #
#####################################
# PHP Version >4.2                  #
#####################################
 */

/**
 * Class pluginscss_class
 */
class pluginscss_class
{
	/** @var string */
	var $the_pluginscss = "";
	/** @var string */
	var $pfad_top = "";
	/** @var string */
	var $plugins_directory = "";
	/** @var string */
	var $pluginscss_directory = "";

	/**
	 * pluginscss_class constructor.
	 */
	function __construct()
	{
		// cms Klasse einbinden
		global $cms;
		$this->cms = & $cms;
		// einbinden des Objekts der Datenbank Abstraktionsklasse ez_sql
		global $db;
		$this->db = & $db;
		// inhalt Klasse einbinden
		global $content;
		$this->content = & $content;
		// checked Klasse einbinden
		global $checked;
		$this->checked = & $checked;

		// Hier die Klassen als Referenzen

		$this->set_cms_pluginscss();
	}

	/**
	 *
	 */
	function set_cms_pluginscss()
	{
		$sql = sprintf("SELECT * FROM %s LIMIT 1",
			$this->cms->papoo_daten
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$_SESSION['dbp']['papoo_daten2']=$result;


		$the_pluginscss = $result[0]['daten_pluginscss'];

		//if (defined("admin")) $this->pfad_top = "../";
		//else $this->pfad_top = "";
		$this->pfad_top = $this->cms->pfadhier."/";

		$this->plugins_directory = "plugins/";
		$this->pluginscss_directory = "templates_c/";

		if (!$the_pluginscss OR !file_exists($this->pfad_top.$this->pluginscss_directory.$the_pluginscss)) {
			$this->make_pluginscss();
		}
		else {
			$this->content->template['pluginscss'] = $this->pluginscss_directory.$the_pluginscss;
		}
	}

	/**
	 *
	 */
	function make_pluginscss()
	{
		$sql = sprintf("SELECT * FROM %s ORDER BY plugin_id ASC",
			$this->cms->papoo_plugins
		);
		$result_array = $this->db->get_results($sql, ARRAY_A);

		if ($result_array) {
			foreach($result_array as $entry) {
				$plugin_name = $entry['plugin_name'];
				$plugin_css = $entry['plugin_css'];
				$plugincss_datei = $this->pfad_top.$this->plugins_directory.$plugin_css;
				$temp_css = "/* CSS-Datei des Plugins ".$plugin_name." */\n";
				if (!empty($plugin_css) && file_exists($plugincss_datei)) {
					$temp_css .= @file_get_contents($plugincss_datei);
				}
				$temp_css .= "\n/* ENDE des CSS-Datei des Plugins ".$plugin_name." */\n\n";
				$this->the_pluginscss .= $temp_css;
			}
		}
		$this->make_pluginscss_file();
	}

	/**
	 * schreibt die CSS-Daten in eine CSS-Datei und setzt die Variablen cms->pluginscss.
	 * Außerdem wird der Name der neuen CSS-Datei in der Tabelle papoo_daten gespeichert.
	 *
	 * TODO: Anpassungen für verschiedene Styles
	 *
	 * @return bool
	 */
	function make_pluginscss_file()
	{
		//1. CSS-Datei erstellen und in Verzeichnis templates_c schreiben
		$file_identifier = time();
		$filename = $this->pfad_top.$this->pluginscss_directory.$file_identifier."_plugins.css";

		$file = fopen($filename, "w+");
		if ($file) {
			fwrite($file, $this->the_pluginscss);
			fclose($file);
			chmod ($filename, 0644);
		}
		else {
			echo '<p>Fehler beim &ouml;ffnen/erstellen der Datei '.$filename.'</p>';
		}

		// 2. Name der neuen CSS-Datei registrieren (in cms und in Tabelle papoo_daten schreiben)
		$this->content->template['pluginscss'] = $this->pluginscss_directory.$file_identifier."_plugins.css";
		$sql = sprintf("UPDATE %s SET daten_pluginscss='%s'",
			$this->cms->papoo_daten,
			$this->db->escape($file_identifier."_plugins.css")
		);
		$this->db->query($sql);

		// 3. alte CSS-Dateien löschen
		$handle = @opendir($this->pfad_top.$this->pluginscss_directory);
		while (false !== ($file = @readdir($handle))) {
			if (stristr( $file,"_plugins.css")) {
				if ($file != $file_identifier."_plugins.css") unlink($this->pfad_top.$this->pluginscss_directory.$file);
			}
		}
		@closedir($handle);
		if (empty($this->error)){
			$this->error="";
		}
		if ($this->error > 0) {
			return false;
		} else {
			return true;
		}
	}
}

$pluginscss = new pluginscss_class();