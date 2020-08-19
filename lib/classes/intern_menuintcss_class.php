<?php
/**
#####################################
# CMS Papoo                       	#
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
#                                   #
# Class: menuintcss_class           #
# (c) Stephan Bergmann              #
#                                   #
#####################################
# PHP Version >4.2                  #
#####################################
 */

/**
 * Class menuintcss_class
 */
class menuintcss_class
{
	var $the_menuintcss = "";
	var $menuintcss_directory = "";

	// Platzhalter: 1. menu_id; 2. menu_id; 3. menu_icon
	var $vorlage1 = "#menu_%s {
		background-image: url(%s);
		background-repeat: no-repeat;
		background-position: 0.2em  0.3em;
	}";

	/**
	 * menuintcss_class constructor.
	 */
	function __construct()
	{
		// globale papoo-Objekte einbinden
		global $cms;
		$this->cms = & $cms;
		global $db;
		$this->db = & $db;
		global $content;
		$this->content = & $content;

		$this->set_cms_menuintcss();
	}

	/**
	 *
	 */
	function set_cms_menuintcss()
	{
		if (!is_array($_SESSION['dbp']['papoo_daten2'])) {
			$sql = sprintf("SELECT * FROM %s LIMIT 1",
				$this->cms->papoo_daten
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			$_SESSION['dbp']['papoo_daten2']=$result;
		}
		else {
			$result=$_SESSION['dbp']['papoo_daten2'];
		}

		$menuintcss = $result[0]['daten_menuintcss'];

		$this->menuintcss_directory = "templates_c/";

		if (!$menuintcss OR !file_exists($this->menuintcss_directory.$menuintcss)) {
			$this->make_menuintcss();
		}
		else {
			$this->content->template['menuintcss'] = $this->menuintcss_directory.$menuintcss;
		}
	}

	/**
	 *
	 */
	function make_menuintcss()
	{
		$sql = sprintf("SELECT menuid, menu_icon FROM %s ORDER BY menuid ASC",
			$this->cms->papoo_menu_int
		);
		$result_array = $this->db->get_results($sql, ARRAY_A);

		if ($result_array) {
			foreach($result_array as $entry) {
				$menu_id = $entry['menuid'];
				$menu_icon = $entry['menu_icon'];
				$temp_css = "";

				$temp_css .= sprintf($this->vorlage1, $menu_id, $menu_icon);

				$this->the_menuintcss .= $temp_css;
			}
		}
		$this->make_menuintcss_file();
	}

	/**
	 * schreibt die CSS-Daten in eine CSS-Datei und setzt die Variablen cms->menuintcss.
	 * Außerdem wird der Name der neuen CSS-Datei in der Tabelle papoo_daten gespeichert.
	 * Zuletzt werden alte CSS-dateien gelöscht.
	 *
	 * @return bool
	 */
	function make_menuintcss_file()
	{
		//1. CSS-Datei erstellen und in Verzeichnis templates_c schreiben
		$error = 0;
		$file_identifier = time();
		$filename = $this->menuintcss_directory.$file_identifier."_menuint.css";

		$file = fopen($filename, "w+");
		if ($file) {
			fwrite($file, $this->the_menuintcss);
			fclose($file);
			chmod ($filename, 0644);
		}
		else {
			$error = 1;
			echo '<p>Fehler beim &ouml;ffnen/erstellen der Datei '.$filename.'</p>';
		}

		// 2. Name der neuen CSS-Datei registrieren (in cms und in Tabelle papoo_daten schreiben)
		$this->content->template['menuintcss'] = $filename;
		$sql = sprintf("UPDATE %s SET daten_menuintcss='%s'",
			$this->cms->papoo_daten,
			$file_identifier."_menuint.css"
		);
		$this->db->query($sql);

		// 3. alte CSS-Dateien löschen
		$handle = opendir('templates_c');
		while (false !== ($file = readdir($handle))) {
			if (stristr( $file,"_menuint.css")) {
				if ($file != $file_identifier."_menuint.css") {
					unlink($this->menuintcss_directory.$file);
				}
			}
		}
		closedir($handle);

		if ($error > 0) {
			return false;
		}
		else {
			return true;
		}
	}
}

$menuintcss = new menuintcss_class();