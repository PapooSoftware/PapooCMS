<?php
/**
#####################################
# Papoo CMS                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.2                  #
#####################################
 */

/**
 * Class intern_span
 */
#[AllowDynamicProperties]
class  intern_span  {

	/**
	 * intern_span constructor.
	 */
	function __construct()
	{
		// Klassen globalisieren

		// cms Klasse einbinden
		global $cms;
		$this->cms		= & $cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db		= & $db;
		// Messages einbinden
		global $message;
		$this->message	= & $message;
		// User Klasse einbinden
		global $user;
		$this->user		= & $user;
		// weitere Seiten Klasse einbinden
		global $weiter;
		$this->weiter	= & $weiter;
		// inhalt Klasse einbinden
		global $content;
		$this->content 	= & $content;
		// Suchklasse einbinden
		global $searcher;
		$this->searcher	= & $searcher;
		// checkedblen Klasse einbinde
		global $checked;
		$this->checked	= & $checked;

		IfNotSetNull($this->content->template['linkname']);
		IfNotSetNull($this->content->template['linktitel']);
		IfNotSetNull($this->content->template['spanerstellen']);
		IfNotSetNull($this->content->template['suchen']);
		IfNotSetNull($this->content->template['neuerstellen']);
		IfNotSetNull($this->content->template['abkerstellen']);
	}

	/**
	 *
	 */
	function make_inhalt()
	{

		// Überprüfen ob Zugriff auf die Inhalte besteht
		$this->user->check_access();

		switch ($this->checked->menuid) {
		case "26":
			// Starttext Übergeben
			$this->content->template['text']= $this->content->template['message_24'];
			break;


		case "27":
			// Hier wird eine neue Akürzung angelegt
			// neuen Eintrag erstellen
			$this->neu_abk();
			break;

		case "28":
			// Hier wird eine Abkürzung geändert
			// Abkürzung ändern oder löschen
			$this->change_abk();
			break;


		case "29":
			// Hier wird eine neue Sprachauszeichnung angelegt
			// Neues span eingeben
			$this->neu_span();
			break;


		case "30":
			// Hier wird ein Link geändert
			$this->change_span();
			break;
		}
	}

	/**
	 * @param $search
	 * @return mixed|string
	 */
	function clean($search)
	{
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
	function neu_abk()
	{
		$linktitel=$linkname="";
		// erstellen wird festegelegt für das erscheinen in der html template datei
		$this->content->template['erstellen']= '1';
		$this->content->template['abkerstellen']= '1';
		// wenn eintragen ausgewählt ist und alle checkedblen belegt sind weitermachen
		if (!empty($this->checked->formSubmit)and !empty($this->checked->linkname) and !empty($this->checked->linktitel)) {
			// Überprüfen, ob die >3
			if (strlen($this->checked->linkname) < 2) {
				$falsch = 1;
			}
			else {
				$falsch=2;
			}
			// wenn die >3 , Daten eintragen.
			if ($falsch != 1) {
				$this->content->template['erstellt']= '1';

				$insert = "INSERT INTO ".$this->cms->papoo_abk." SET abk_abk='".$this->db->escape($this->checked->linkname)."', abk_meaning='".$this->db->escape($this->checked->linktitel)."' " ;
				$this->db->query($insert);

				$this->content->template['text']=$this->content->template['message_21'];
			}
			// <3.
			else {
				$this->content->template['falsch']= '1';
				$link_data=array();
				array_push($link_data, array(
					'linkname' => $linkname,
					'linktitel' => $linktitel,

				));
				$this->content->template['table_data']=$link_data;
			}
		}
		// nichts ausgewählt, blankes Formular anbieten
		else {
			$this->content->template['neuerstellen']= '1';
		}
	}

	/**
	 *
	 */
	function change_abk()
	{
		$this->content->template['aendern']= '1';
		$this->content->template['abkerstellen'] ='1';
		$link_data=array();
		// Wenn keine Abkürzung ausgewählt ist, Suchmaske anbieten
		if (empty($this->checked->id)) {
			$this->content->template['suchen']= '1';
			// wenn die Suche angefragt ist, suchen
			if (!empty($this->checked->search)) {
				// Suchbegriff bereinigen
				$this->checked->search=$this->db->escape($this->clean($this->checked->search));
				// Daten aus der Datenbank holen
				$this->weiter->result_anzahl=$this->db->get_var("SELECT COUNT(*) FROM ".$this->cms->papoo_abk." WHERE abk_abk ='".$this->checked->search."' OR abk_abk LIKE '%" . $this->checked->search . "%' OR abk_meaning LIKE '%" . $this->checked->search . "%' " );
				$suchergebniss = "SELECT * FROM ". $this->cms->papoo_abk." WHERE abk_abk ='".$this->checked->search."' OR abk_abk LIKE '%" . $this->checked->search . "%' OR abk_meaning LIKE '%" . $this->checked->search . "%' ORDER BY abk_abk ASC ". $this->cms->sqllimit ;

				$result = $this->db->get_results($suchergebniss);
				// wenn leer, dann nix machen
				if (!empty($result)) {
					// Daten durchnudeln
					foreach ($result as $row) {
						// Daten zuweisen für template checkedblen
						array_push($link_data, array(
							'linkname' => $row->abk_abk,
							'linkbody' => $row->abk_meaning,
							'linkid' => $row->abk_id,
						));
					}
					$this->content->template['table_data']=$link_data;
					$this->content->template['suchen']= '';
					$this->content->template['gefunden']='1';

					$this->weiter->weiter_link= "./span.php?menuid=28";
					$this->weiter->do_weiter("search");
				}
				// nichts gefunden anzeigen
				else {
					$this->content->template['text']=$this->content->template['message_5'];
				}
			}
			else
			{
				// Daten aus der Datenbank holen
				$this->weiter->result_anzahl=$this->db->get_var("SELECT COUNT(*) FROM ".$this->cms->papoo_abk  );
				$suchergebniss = "SELECT * FROM ".$this->cms->papoo_abk."  ORDER BY abk_abk ASC ".$this->cms->sqllimit ;

				$result = $this->db->get_results($suchergebniss);
				// wenn leer, dann nix machen
				if (!empty($result)) {
					// Daten durchnudeln
					foreach ($result as $row) {
						// Daten zuweisen für template checkedblen
						array_push($link_data, array(
							'linkname' => $row->abk_abk,
							'linkbody' => $row->abk_meaning,
							'linkid' => $row->abk_id,
						));
					}

					$this->content->template['table_data']=$link_data;
					$this->content->template['gefunden']='1';

					$this->weiter->weiter_link= "./span.php?menuid=28";
					$this->weiter->do_weiter("search");

				}
				// nichts gefunden anzeigen
				else {
					$this->content->template['text']=$this->content->template['message_5'];
				}
			}
		}
		// Abkürzung ist ausgewählt, Daten aus der Datenbank holen
		else {
			if (empty($this->checked->formSubmit)) {
				$suchergebniss = "SELECT * FROM ".$this->cms->papoo_abk." WHERE abk_id='".$this->db->escape($this->checked->id)."' ORDER BY abk_id DESC LIMIT 1" ;
				$result = $this->db->get_results($suchergebniss);
				if (!empty($result)) {
					// Daten zuweisen für template
					foreach ($result as $row) {
						array_push($link_data, array(
							'linkname' => $row->abk_abk,
							'linktitel' => $row->abk_meaning,
							'linkid' => $row->abk_id,
						));
					}
				}
				$this->content->template['table_data']=$link_data;
				// sagen daten werden geändert
				$this->content->template['aendern_now']= '1';
			}
			// Daten wurden geändert und sollen eingetragen werden, daher Überprüfen
			elseif (!empty($this->checked->formSubmit) and !empty($this->checked->linkname) and !empty($this->checked->linktitel)) {
				// url Überprüfen
				if (strlen($this->checked->linkname) < 2) {
					$falsch = 1;
				}
				else {
					$falsch=2;
				}
				// wenn okay, dann updaten
				if ($falsch != 1) {
					$this->content->template['erstellt']= '1';

					$insert = "UPDATE ".$this->cms->papoo_abk." SET abk_abk='".$this->db->escape($this->checked->linkname)."', abk_meaning='".$this->db->escape($this->checked->linktitel)."' WHERE abk_id='".$this->db->escape($this->checked->id)."'";
					$this->db->query($insert);

					$this->content->template['text']=$this->content->template['message_21'];
				}
				// wenn nicht zur Überprüfung anbieten
				else {
					$this->content->template['falsch']= '1';
					array_push($link_data, array(
						'linkname' => $this->checked->linkname,
						'linktitel' => $this->checked->linktitel,

					));
					$this->content->template['table_data']=$link_data;
				}
			}

			if (!empty($this->checked->formSubmitentf)) {
				$insert = "DELETE FROM ".$this->cms->papoo_abk." WHERE abk_id='".$this->db->escape($this->checked->id)."'";
				$this->db->query($insert);
				$this->content->template['text']=$this->content->template['message_26'];
				//header ("Location: ./span.php?menuid=28&messageget=26");
				$location_url = "./span.php?menuid=28&messageget=26";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="'.$location_url.'">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit();
			}

		}
	}

	/**
	 *
	 */
	function neu_span()
	{
		$link_data=array();
		// erstellen wird festegelegt für das erscheinen in der html template datei
		$this->content->template['erstellen']= '1';
		$this->content->template['spanerstellen'] ='1';
		// wenn eintragen ausgewählt ist und alle checkedblen belegt sind weitermachen
		if (!empty($this->checked->formSubmit) and !empty($this->checked->linkname)) {
			// Überprüfen, ob die URL valide ist
			if (strlen($this->checked->linkname) < 3) {
				$falsch = 1;
			}
			else {
				$falsch=2;
			}

			if ($falsch != 1) {
				$this->content->template['erstellt']= '1';

				$insert = "INSERT INTO ".$this->cms->papoo_lang_en." SET lang_word='".$this->db->escape($this->checked->linkname)."'";
				$this->db->query($insert);

				$this->content->template['text']=$this->content->template['message_21'];
			}
			// url ist nicht valide, daher Daten erneut Überprüfen lassen.
			else {
				$this->content->template['falsch']= '1';
				array_push($link_data, array(
					'linkname' => $this->checked->linkname,
				));
				$this->content->template['table_data']=$link_data;
			}
		}
		// nichts ausgewählt, blankes Formular anbieten
		else {
			$this->content->template['neuerstellen']= '1';
		}
	}

	/**
	 *
	 */
	function change_span()
	{
		$this->content->template['spanerstellen']= '1';
		$this->content->template['aendern']= '1';
		$this->content->template['erstellen']= '1';
		$link_data=array();
		if (empty($this->checked->search)) {
			$this->checked->search="";
		}
		// Wenn kein Link ausgewählt ist, Suchmaske anbieten
		if (empty($this->checked->id)) {
			$this->content->template['suchen']= '1';
			// wenn die Suche angefragt ist, suchen
			if (!empty($this->checked->search)) {
				// Suchbegriff bereinigen
				$this->checked->search=$this->db->escape($this->clean($this->checked->search));
				// Daten aus der Datenbank holen
				$this->weiter->result_anzahl =
					$this->db->get_var("SELECT COUNT(*) FROM ".$this->cms->papoo_lang_en." WHERE lang_word LIKE '%" . $this->checked->search . "%'");

				$suchergebniss =
					"SELECT * FROM ".$this->cms->papoo_lang_en." WHERE lang_word LIKE '%" . $this->checked->search . "%'  ORDER BY lang_word ASC ".$this->cms->sqllimit ;

				$result = $this->db->get_results($suchergebniss);
				// wenn leer, dann nix machen
				if (!empty($result)) {
					// Daten durchnudeln
					foreach ($result as $row) {
						// Daten zuweisen für template checkedblen
						array_push($link_data, array(
							'linkname' => $row->lang_word,
							'linkid' => $row->lang_id,
						));
					}
					$this->content->template['table_data']=$link_data;
					$this->content->template['gefunden']='1';

					$this->weiter->weiter_link= "./span.php?menuid=30";
					$this->weiter->do_weiter("search");
				}
				// nichts gefunden anzeigen
				else {
					$this->content->template['text']=$this->content->template['message_5'];
				}
			}
			else {
				// Daten aus der Datenbank holen
				$this->weiter->result_anzahl =
					$this->db->get_var("SELECT COUNT(*) FROM ".$this->cms->papoo_lang_en);
				//$suchergebniss = "SELECT * FROM ".$this->cms->papoo_lang_en." WHERE lang_word LIKE '%" . $this->checked->search . "%'  ORDER BY lang_id DESC  ".$this->cms->sqllimit ;
				$suchergebniss =
					"SELECT * FROM ".$this->cms->papoo_lang_en."  ORDER BY lang_word ASC  ".$this->cms->sqllimit ;

				$result = $this->db->get_results($suchergebniss);
				// wenn leer, dann nix machen
				if (!empty($result)) {
					// Daten durchnudeln
					foreach ($result as $row) {
						// Daten zuweisen für template checkedblen
						array_push($link_data, array(
							'linkname' => $row->lang_word,
							'linkid' => $row->lang_id,
						));
					}

					$this->content->template['table_data'] = $link_data;
					$this->content->template['gefunden'] = '1';

					$this->weiter->weiter_link= "./span.php?menuid=30";
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
				$suchergebniss = "SELECT * FROM ".$this->cms->papoo_lang_en." WHERE lang_id='".$this->db->escape($this->checked->id)."' LIMIT 1" ;
				$result = $this->db->get_results($suchergebniss);
				if (!empty($result)) {
					// Daten zuweisen für template
					foreach ($result as $row) {
						array_push($link_data, array
						('linkname' => $row->lang_word,
							'linkid' => $row->lang_id,
						));
					}
					$this->content->template['table_data']=$link_data;
				}
				// sagen daten werden geändert
				$this->content->template['aendern_now']= '1';
			}
			// Daten wurden geändert und sollen eingetragen werden, daher Überprüfen
			elseif (!empty($this->checked->formSubmit) and !empty($this->checked->linkname)) {
				// url Überprüfen
				if (strlen($this->checked->linkname) < 3) {
					$falsch = 1;
				}
				else {
					$falsch=2;
				}
				// wenn okay, dann updaten
				if ($falsch != 1) {
					$this->content->template['erstellt']= '1';

					$insert = "UPDATE ".$this->cms->papoo_lang_en." SET lang_word='".$this->db->escape($this->checked->linkname)."' WHERE lang_id='".$this->db->escape($this->checked->id)."'";
					$this->db->query($insert);

					$this->content->template['text']=$this->content->template['message_21'];
				}
				// wenn nicht zur Überprüfung anbieten
				else {
					$this->content->template['falsch']= '1';

					// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
					IfNotSetNull($linkname);

					array_push($link_data, array(
						'linkname' => $linkname,

					));
					$this->content->template['table_data']=$link_data;
				}
			}
			if (!empty($this->checked->formSubmitentf)) {
				$insert = "DELETE FROM ".$this->cms->papoo_lang_en." WHERE lang_id='".$this->db->escape($this->checked->id)."'";
				$this->db->query($insert);
				$this->content->template['text']=$this->content->template['message_27'];
				//header ("Location: ./span.php?menuid=30&messageget=27");
				$location_url = "./span.php?menuid=30&messageget=27";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="'.$location_url.'">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit();
			}
		}
	}
}

$intern_span= new intern_span();