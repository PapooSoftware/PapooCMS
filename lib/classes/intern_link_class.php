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
 * Class intern_link
 */
class  intern_link  {

	/**
	 * intern_link constructor.
	 */
	function __construct(){
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
		$this->cms		= & $cms;
		$this->db		= & $db;
		$this->content 	= & $content;
		$this->message	= & $message;
		$this->user		= & $user;
		$this->weiter	= & $weiter;
		$this->searcher	= & $searcher;
		$this->checked	= & $checked;

		IfNotSetNull($this->content->template['falsch']);
		IfNotSetNull($this->content->template['erstellt']);
		IfNotSetNull($this->content->template['aendern_now']);
	}

	/**
	 * @param $search
	 * @return mixed|string
	 */
	function clean($search) {
		//$search auf unerlaubte Zeichen Überprüfen und evtl. bereinigen
		$search = trim($search);
		$remove = "<>'\"_%*\\";
		for ($i = 0; $i < strlen($remove); $i ++) {
			$search = str_replace(substr($remove, $i, 1), "", $search);
		}
		return $search;
	}

	/**
	 *
	 */
	function make_inhalt()
	{
		// Überprüfen ob Zugriff auf die Inhalte besteht
		$this->user->check_access();

		switch ($this->checked->menuid) {

		case "23":
			// Starttext Übergeben
			$this->content->template['text']= $this->content->template['message_28'];
			break;

		case "24":
			// Hier wird ein neuer Link angelegt
			$this->neu_link();
			break;

		case "25":
			// Hier wird ein Link geändert
			$this->change_link();

			break;
		}
	}

	/**
	 *
	 */
	function neu_link()
	{
		// erstellen wird festegelegt für das erscheinen in der html template datei
		$linkname=$linktitel=$linkbody="";
		$link_data=array();
		$this->content->template['erstellen']= '1';
		// wenn eintragen ausgewählt ist und alle Variablen belegt sind weitermachen
		if (!empty($this->checked->formSubmit) and !empty($this->checked->linkname) and !empty($this->checked->linktitel) and !empty($this->checked->linkbody)) {
			// Überprüfen, ob die URL valide ist
			if (check_url ($this->checked->linkbody, array ('http')) === false) {
				$falsch = 1;
			}
			else {
				$falsch=2;
			}
			// wenn die url valide ist, Daten eintragen.
			if ($falsch != 1) {
				$this->content->template['erstellt']= '1';
				// Ersetzung von http wegen der Ersetzungsklassse
				//$this->checked->linkbody = str_ireplace("http://", "", $this->checked->linkbody);
				$insert = "INSERT INTO ".$this->cms->papoo_links." SET link_name='".$this->db->escape($this->checked->linkname)."', link_title='".$this->db->escape($this->checked->linktitel)."', link_body='".$this->db->escape($this->checked->linkbody)."' ";
				$this->db->query($insert);

				$this->content->template['text']=$this->content->template['message_21'];
			}
			// url ist nicht valide, daher Daten erneut Überprüfen lassen.
			else {
				$this->content->template['falsch']= '1';
				array_push($link_data, array(
					'linkname' => $linkname,
					'linktitel' => $linktitel,
					'linkbody' => $linkbody,

				));
				$this->content->template['table_data']=$link_data;
			}
		}
		// nichts ausgewählt, blankes Formular anbieten
		else {
			$this->content->template['neuerstellen']= '1';
			//leeren Link schon mal mit http://www. vorbelegen
			$link_data=array();
			array_push($link_data, array(
				'linkname' => "",
				'linktitel' => "",
				'linkbody' => "http://www.",
			));
			$this->content->template['table_data']=$link_data;
		}
	}

	/**
	 *
	 */
	function change_link()
	{

		$this->content->template['aendern']= '1';
		$link_data=array();
		$linkname=$linktitel=$linkbody="";

		// Wenn kein Link ausgewählt ist, Suchmaske anbieten
		if (empty($this->checked->id)) {
			$this->content->template['suchen']= '1';
			// wenn die Suche angefragt ist, suchen
			if (!empty($this->checked->search)) {
				// Suchbegriff bereinigen

				$this->checked->search=$this->clean($this->checked->search);
				// Daten aus der Datenbank holen
				$this->weiter->result_anzahl=$this->db->get_var("SELECT COUNT(*) FROM ".$this->cms->papoo_links." WHERE link_name LIKE '%" . $this->db->escape($this->checked->search) . "%' OR link_title LIKE '%" . $this->db->escape($this->checked->search) . "%' OR link_body LIKE '%" . $this->db->escape($this->checked->search) . "%'");
				$suchergebniss = "SELECT * FROM ".$this->cms->papoo_links." WHERE link_name LIKE '%" . $this->db->escape($this->checked->search) . "%' OR link_title LIKE '%" . $this->db->escape($this->checked->search) . "%' OR link_body LIKE '%" . $this->db->escape($this->checked->search) . "%' ORDER BY link_name ASC ". $this->cms->sqllimit ;

				$result = $this->db->get_results($suchergebniss);
				// wenn leer, dann nix machen
				if (!empty($result)) {
					// Daten durchnudeln
					foreach ($result as $row) {
						// Daten zuweisen für template Variablen
						array_push($link_data, array(
							'linkname' => $row->link_name,
							'linkid' => $row->id,
						));
					}
					$this->content->template['table_data']=$link_data;
					$this->content->template['gefunden']='1';
					$this->weiter->weiter_link= "./link.php?menuid=25";

					$this->weiter->do_weiter("search");
				}
				// nichts gefunden anzeigen
				else {
					$this->content->template['text']=$this->content->template['message_5'];
				}
			}
			else {
				$this->weiter->result_anzahl = $this->db->get_var("SELECT COUNT(*) FROM ".$this->cms->papoo_links);
				$suchergebniss = "SELECT * FROM ".$this->cms->papoo_links." ORDER BY link_name ASC ". $this->cms->sqllimit ;

				$result = $this->db->get_results($suchergebniss);
				// wenn leer, dann nix machen
				if (!empty($result)) {
					// Daten durchnudeln
					foreach ($result as $row) {
						// Daten zuweisen für template Variablen
						array_push($link_data, array(
							'linkname' => $row->link_name,
							'linkid' => $row->id,
						));
					}


					$this->content->template['table_data']=$link_data;
					$this->content->template['gefunden']='1';
					$this->weiter->weiter_link= "./link.php?menuid=25";

					$this->weiter->do_weiter("search");
				}
				// nichts gefunden anzeigen
				else {
					$this->content->template['text']=$this->content->template['message_5'];
				}
			}

		}
		// Links ist ausgewählt, Daten aus der Datenbank holen
		else {
			if (empty($this->checked->formSubmit)) {
				$suchergebniss = "SELECT * FROM ".$this->cms->papoo_links." WHERE id='".$this->db->escape($this->checked->id)."' ORDER BY id DESC LIMIT 1" ;
				$result = $this->db->get_results($suchergebniss);
				if (!empty($result)) {
					// Daten zuweisen für template
					foreach ($result as $row) {
						array_push($link_data, array(
							'linkname' => $row->link_name,
							'linktitel' => $row->link_title,
							//'linkbody' => "http://" . $row->link_body,
							'linkbody' => $row->link_body,
							'linkid' => $row->id,
						));
					}

				}
				$this->content->template['table_data']=$link_data;
				// sagen daten werden geändert
				$this->content->template['aendern_now']= '1';
			}
			// Daten wurden geändert und sollen eingetragen werden, daher Überprüfen
			elseif (!empty($this->checked->formSubmit) and !empty($this->checked->linkname) and !empty($this->checked->linktitel) and !empty($this->checked->linkbody)) {
				// url Überprüfen
				if (check_url ($this->checked->linkbody, array ('http')) === false) {
					$falsch = 1;
				}
				else {
					$falsch=2;
				}
				// wenn okay, dann updaten
				if ($falsch != 1) {
					$this->content->template['erstellt']= '1';
					// Ersetzung von http wegen der Ersetzungsklassse
					//$this->checked->linkbody = str_ireplace("http://", "", $this->checked->linkbody);
					$insert = "UPDATE ".$this->cms->papoo_links." SET link_name='".$this->db->escape($this->checked->linkname)."', link_title='".$this->db->escape($this->checked->linktitel)."', link_body='".$this->db->escape($this->checked->linkbody)."' WHERE id='".$this->db->escape($this->checked->id)."'";
					$this->db->query($insert);

					$this->content->template['text']=$this->content->template['message_21'];
				}
				// wenn nicht zur Überprüfung anbieten
				else {
					array_push($link_data, array(
						'linkname' => $linkname,
						'linktitel' => $linktitel,
						'linkbody' => $linkbody,
					));
					$this->content->template['table_data']=$link_data;
				}
			}
			if (!empty($this->checked->loeschen)) {
				$insert = "DELETE FROM ".$this->cms->papoo_links." WHERE id='".$this->db->escape($this->checked->id)."'";
				$this->db->query($insert);
				$this->content->template['text']=$this->content->template['message_29'];
				$this->content->template['aendern']= 0;
			}
		}
	}
}

$intern_link= new intern_link();