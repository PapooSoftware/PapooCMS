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
class convert {

	/** @var string  */
	var $beispiel = "";

	/**
	 * convert constructor.
	 */
	function __construct() {
		/**
		Klassen globalisieren
		 */

		// cms Klasse einbinden
		global $cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		// Messages einbinden
		global $message;
		// User Klasse einbinden
		global $user;
		// weitere Seiten Klasse einbinden
		global $weiter;
		// inhalt Klasse einbinden
		global $content;
		// Suchklasse einbinden
		global $searcher;
		// checkedblen Klasse einbinde
		global $checked;

		/**
		und einbinden in die Klasse
		 */

		// Hier die Klassen als Referenzen
		$this->cms = & $cms;
		$this->db = & $db;
		$this->content = & $content;
		$this->message = & $message;
		$this->user = & $user;
		$this->weiter = & $weiter;
		$this->searcher = & $searcher;
		$this->checked = & $checked;

	}

	/**
	 *
	 */
	function convert_article() {
		//langid bekommen
		//$this->cms->make_lang();

		$select = "SELECT * FROM ".$this->cms->papoo_repore." ";
		$result = $this->db->get_results($select);
		if (!empty ($result)) {
			foreach ($result as $row) {
				$sql="INSERT INTO ".$this->cms->papoo_language_article." ";
				$sql.=" SET header='".$this->db->escape($row->ueberschrift)."', ";
				$sql.=" lan_teaser='".$this->db->escape($row->teaser)."', ";
				$sql.=" lan_teaser_link='".$this->db->escape($row->teaser_link)."',";
				$sql.=" lan_article='".$this->db->escape($row->text)."', ";
				$sql.=" lang_id='".$this->db->escape($this->cms->lang_id)."', ";
				$sql.=" lan_repore_id='".$this->db->escape($row->reporeID)."'";
				$sql.="";
				$sql.="";
				$this->db->query($sql);
				echo $row->ueberschrift." eingegeben. <br />";
			}
		}
	}
}
$convert = new convert();