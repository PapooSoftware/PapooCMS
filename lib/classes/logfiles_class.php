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

/**
 * Class logfiles
 */
#[AllowDynamicProperties]
class logfiles {

	/** @var string */
	var $black = "";

	/**
	 * logfiles constructor.
	 */
	function __construct() {
		/**
		Klassen globalisieren
		 */

		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		// checkedblen Klasse einbinde
		global $checked;
		// cms Klasse einbinden
		global $cms;
		// inhalt Klasse einbinden
		global $content;

		/**
		und einbinden in die Klasse
		 */

		// Hier die Klassen als Referenzen
		$this->checked = & $checked;
		$this->db = & $db;
		$this->cms = & $cms;
		$this->content 	= & $content;
	}

	/**
	 * Administration
	 * @return void
	 */
	function do_admin()
	{
		if (defined("admin")) {
			// User Klasse einbinden
			global $user;
			$this->user		= & $user;

			//Checken ob erlaubt
			$this->user->check_access();

			//WEnn erlaubt dann Menüpunkt checken
			switch ($this->checked->menuid) {

			case "87":
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
		$dateiarray = @file(PAPOO_ABS_PFAD."/dokumente/logs/login_log.log");
		$this->content->template['daten_log_login'] =htmlentities(@implode($dateiarray));
		$this->content->template['daten_log_login']=str_replace(" ","&nbsp;",$this->content->template['daten_log_login']);

		$dateiarray = @file(PAPOO_ABS_PFAD."//dokumente/logs/email_log.log");
		$this->content->template['daten_log_email'] =htmlentities(@implode($dateiarray));
		$this->content->template['daten_log_email']=str_replace(" ","&nbsp;",$this->content->template['daten_log_email']);
	}
}

$logfiles = new logfiles();