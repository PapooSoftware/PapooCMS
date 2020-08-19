<?php
/**
 *
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
 * Mit dieser Klasse werden Contents aus
 * externen oder internen Flexverwaltungen
 * geholt nd viele Andere Inhalte aus der DB
 * und in Artikel resp. 3. Spalten ersetzt
 *
 * Class content_manipulator
 */
class content_manipulator
{
	/**
	 * content_manipulator constructor.
	 *
	 * @throws Exception
	 * @throws Exception
	 */
	function __construct()
	{
		//In der Admin einbinden um die Messages dort auszugeben
		if (defined("admin")) {
			$this->filter_ausgabe_inhalt();
		}
	}

	function output_filter()
	{
		//Ausgabe filtern
		$this->filter_ausgabe_inhalt();
	}

	/**
	 * content_manipulator::filter_ausgabe_inhalt()
	 * Diese Funktion filter die Ausgabe im Inhaltsbereich
	 * und ersetzt die Vorgaben mit den Inhalten aus der Flexverwaltung
	 *
	 * @return void
	 */
	function filter_ausgabe_inhalt()
	{
		global $cms;
		global $diverse;

		//Verzeichnis scripts durchloopen
		$files=$diverse->lese_dir("/plugins/content_manipulator/scripts/");

		//Files durchgehen wenn Array
		if (is_array($files)) {
			foreach ($files as $key=>$value) {
				if (file_exists(PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/".$value['name']."/include.php")) {
					//Files inkludieren
					require_once(PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/".$value['name']."/include.php");
				}
			}
		}
	}
}

$content_manipulator = new content_manipulator();
