<?php

/**
#####################################
# CMS Papoo                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.2                  #
#####################################
*/

#[AllowDynamicProperties]
class blacklist
{
	/** @var string  */
	var $black = "";

	/**
	 * blacklist Konstruktor.
	 */
	function __construct()
	{
		// Klassen globalisieren

		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		// checkedblen Klasse einbinde
		global $checked;
		// cms Klasse einbinden
		global $cms;
		// inhalt Klasse einbinden
		global $content;

		// und einbinden in die Klasse

		// Hier die Klassen als Referenzen
		$this->checked = & $checked;
		$this->db = & $db;
		$this->cms = & $cms;
		$this->content 	= & $content;
	}

	/**
	 * Administration
	 */
	function do_admin()
	{
		if (defined("admin")) {
			// User Klasse einbinden
			global $user;
			$this->user = & $user;

			//Checken ob erlaubt
			$this->user->check_access();

			//WEnn erlaubt dann Menüpunkt checken
			switch($this->checked->menuid) {

				case "82":
				// Starttext Übergeben
				$this->make_entries();
				break;
			}
		}
	}

	/**
	 * Einträge in die Datenbank durchführen
	 * @return void
	 */
	function make_entries()
	{
		//Einträge in die Datenbank machen
		if (!empty($this->checked->blacklist)) {
			$blacklist_ar=explode("\r\n",$this->checked->blacklist);
			//Alte Einträge löschen
			$sql = "DELETE FROM ".$this->cms->papoo_blacklist." ";
			$this->db->query($sql);
			//Neue eintragen

			foreach ($blacklist_ar as $bl) {
				$bl=trim($bl);
				if (!empty($bl)) {
					$sql = "INSERT INTO ".$this->cms->papoo_blacklist." SET black_name='".$this->db->escape($bl)."'";
					$this->db->query($sql);
				}
			}
		}
		//Einträge rausholen
		$sql = "SELECT * FROM ".$this->cms->papoo_blacklist." ORDER BY black_name ASC ";
		$result = $this->db->get_results($sql,ARRAY_A);
		//Übergeben
		$this->content->template['blblack']=$result;
	}

	/**
	 * @param $black
	 * @return bool|string
	 */
	function do_blacklist($black)
	{
		#$inhalt = implode("", file("./interna/blacklist.txt"));
		// Blacklist aus der Datenbank holen
		$sql = "SELECT * FROM ".$this->cms->papoo_blacklist." ";
		$result = $this->db->get_results($sql);
		// Rückgabewert initialisieren
		$true = "ok";
		// Daten durchgehen, ob Blacklistwort drin steckt
		if (!empty ($result)) {
			foreach ($result as $bword) {
				if ($true == "ok") {
					// checken für aktiven Eintrag
					#$bword->black_name;
					if (is_string($black)) {
						if (stristr( $black,$bword->black_name)) {
						$true="not_ok";
						}
					}
				}
			}
		}
		//Restliche Tests nur noch wenn noch kein Spam 
		if ($true == "ok") {
			//Test auf zuviele Links
			$true = $this->check_zuviele_links($black);
		}
		// Wert zurückgeben
		return $true;
	}

	/**
	 * blacklist::check_zuviele_links()
	 * Testet ob die Anzahl der Links im Text 5 nicht übersteigt
	 * @param mixed $black
	 * @return string ok|not_ok
	 */
	function check_zuviele_links($black)
	{
		$true="ok";
		if (is_string($black)) {
			if (substr_count($black, '<a')>5) {
				$true="not_ok";
			}
		}
		return $true;
	}
}

$blacklist = new blacklist();
