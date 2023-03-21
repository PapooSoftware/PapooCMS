<?php
/**
#####################################
# papoo Version 3                   #
# (c) Carsten Euwens 2006           #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.2                  #
#####################################
 */

/**
 * Class intern_hilfe
 */
#[AllowDynamicProperties]
class intern_hilfe
{
	/**
	 * intern_hilfe constructor.
	 */
	function __construct()
	{
		/**
		Klassen globalisieren
		 */

		// Messages einbinden
		global $message;
		// User Klasse einbinden
		global $user;
		// inhalt Klasse einbinden
		global $content;
		global $db;
		global $cms;
		global $menu;
		// checkedblen Klasse einbinde
		global $checked;

		/**
		und einbinden in die Klasse
		 */

		// Hier die Klassen als Referenzen

		$this->content = & $content;
		$this->db = & $db;
		$this->cms = & $cms;
		$this->message = & $message;
		$this->user = & $user;
		$this->menu = & $menu;
		$this->checked = & $checked;
		$this->make_inhalt();
	}

	/**
	 *
	 */
	function make_inhalt()
	{
		// Überprüfen ob Zugriff auf die Inhalte besteht
		#$this->user->check_access();

		//Name des Menüpunktes raussuchen
		$sql = sprintf("SELECT menuname FROM %s WHERE menuid_id='%s' AND lang_id='%s'",
			$this->cms->tbname['papoo_men_uint_language'],
			$this->db->escape($this->checked->menuid),
			1
		);
		$menuname = $this->db->get_var($sql);

		$menuname = str_ireplace(" ","_",$menuname);
		$this->content->template['hilfe_key'] = urlencode($menuname);
		$hilfe=$menuname;
		$this->content->template['hilfe_key'] = $hilfe;
		$this->content->template['hilfe_key2'] = urlencode($hilfe);
	}
}

$intern_hilfe = new intern_hilfe();