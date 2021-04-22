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
 * Hier werden alle Daten die die Bearbeitung der
 * Foren bzw. Messages betreffen erstellt
 *
 * Class intern_forum
 */
class intern_forum
{

	/**
	 * Der Konstruktor der Klasse
	 * @return void
	 */
	function __construct()
	{
		// Klassen  globalisieren

		// cms Klasse einbinden
		global $cms;
		$this->cms = & $cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = & $db;
		// Messages einbinden
		global $message;
		$this->message = & $message;
		// User Klasse einbinden
		global $user;
		$this->user = & $user;
		// weitere Seiten Klasse einbinden
		global $weiter;
		$this->weiter = & $weiter;
		// inhalt Klasse einbinden
		global $content;
		$this->content = & $content;
		// Forum Klasse einbinden
		global $forum;
		$this->forum = & $forum;
		// Überprüft
		global $checked;
		$this->checked = & $checked;
		// html Bereinigunsklasse
		global $html;
		$this->html = & $html;
		// Ersetzungklasse
		global $replace;
		$this->replace = & $replace;
		// Diverse-Klasse
		global $diverse;
		$this->diverse = & $diverse;

		IfNotSetNull($this->message_data_such);
	}

	/**
	 *
	 */
	function make_inhalt()
	{
		// Überprüfen ob Zugriff auf die Inhalte besteht
		$this->user->check_access();
		//arrays erstellen
		$this->content->template['group_data_read'] = array ();
		$this->content->template['group_data_write'] = array ();

		// Bearbeitung
		switch ($this->checked->menuid) {
		case "3" :
			// Starttext Übergeben
			if (empty ($this->checked->messageget)) {
				$this->content->template['text'] = $this->content->template['message_13'];
			}
			// ARtikel wurde eingetragen
			elseif ($this->checked->messageget == 17) {
				$this->content->template['text'] = $this->content->template['message_13'] . $this->content->template['message_17'];
			}
			elseif ($this->checked->messageget == 398) {
				$this->content->template['text'] = $this->content->template['message_13'] . $this->content->template['message_398'];
			}
			break;

		case "40" :
			// Forum erstellen
			$this->make_forum();
			break;

		case "41" :
			//Foren bearbeiten oder löschen
			$this->change_forum();
			break;

			// Messages bearbeiten ###
		case "42" :
			// Nachrichten bearbeiten
			$this->change_message();
			break;

		}

	}

	/**
	 * Hiermit wird das Forum bearbeitet
	 *
	 * @return void
	 */
	function make_forum()
	{
		IfNotSetNull($this->checked->forumid);
		// menuid für das Template setzen
		$this->content->template['menuid_tmpl'] = $this->checked->menuid;
		if (empty ($this->checked->foruminsert)) {
			$this->checked->foruminsert = "";
		}
		if ($this->checked->foruminsert == "insertneuecht") {
			// Überprüfen ob es das Forum schon gibt
			$select = "SELECT forumid FROM " . $this->cms->papoo_forums . " WHERE forumname='" . $this->db->escape($this->checked->forname) . "'";
			$result = $this->db->get_results($select);
			// Wenn nicht, dann
			if (is_array($result) && count($result) > 0) {
				$this->content->template['text'] = $this->content->template['message_16'];
				$exist = 1;
			}
			else {
				//if ($this->checked->intranet_yn == 1) $intranet_yn= 1;
				//else $intranet_yn= 0;

				//$query= "INSERT INTO ".$this->cms->papoo_forums." SET forumname='".$this->db->escape($this->checked->forname)."', Beschreibung='".$this->db->escape($this->checked->metabeschreibung)."', intranet_yn='$intranet_yn'";
				$query = "INSERT INTO " . $this->cms->papoo_forums . " SET forumname='" . $this->db->escape($this->checked->forname) . "', Beschreibung='" . $this->db->escape($this->checked->metabeschreibung) . "'";
				$this->db->query($query);

				// aktuelle Forumid
				$this->checked->forumid = $this->db->insert_id;

				// Lese und Schreibrechte eintragen
				$this->insert_gruppenrechte();

				//header ("Location: ./forum.php?menuid=3");
				$location_url = "./forum.php?menuid=3";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="' . $location_url . '">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit;
			}
		}
		// wenn intranet existiert, auch eine Wahlmöglicheit anbieten ... ###
		//if ($this->cms->intranetnow == 1) $this->content->template['intranet_yn']= '1';

		$selected_read = "";
		$selected_write = "";
		// Arrays erstellen
		$table_data = array ();

		// Gruppen aus der Datenbank holen, um zuweisen zu können werd darin lesen darf ... ###
		$sqlroot = "SELECT gruppenname,gruppeid FROM " . $this->cms->papoo_gruppe . "";
		$resultroot = $this->db->get_results($sqlroot);

		foreach ($resultroot as $row) {
			$gruppenixkomma = str_ireplace(",", "", $row->gruppeid);
			$gruppenixkomma = "g" . $gruppenixkomma;

			// Daten aus dem Formular vergleichen, wenn sie belegt sind -> Leserechte
			if (!empty ($this->checked->gruppe_read)) {
				// Wenn ausgewählt ein checked für das Formular zuweisen
				if (in_array("$gruppenixkomma", $this->checked->gruppe_read)) {
					$selected_read = 'nodecode:checked="checked"';
				}
				// ansonsten leer lassen- > nicht ausgewählt
				else {
					$selected_read = "";
				}
			}
			// Daten aus dem Formular vergleichen, wenn sie belegt sind -> Schreibrechte
			if (!empty ($this->checked->gruppe_write)) {
				// Wenn ausgewählt ein checked für das Formular zuweisen
				if (in_array("$gruppenixkomma", $this->checked->gruppe_write)) {
					$selected_write = 'nodecode:checked="checked"';
				}
				// ansonsten leer lassen- > nicht ausgewählt
				else {
					$selected_write = "";
				}
			}
			// Datn für das Template zuweisen
			array_push($this->content->template['group_data_read'], array (
				'gruppeid' => $row->gruppeid,
				'gruppename' => $row->gruppenname,
				'checked' => $selected_read,
			));

			array_push($this->content->template['group_data_write'], array (
				'gruppeid' => $row->gruppeid,
				'gruppename' => $row->gruppenname,
				'checked' => $selected_write,
			));
		}

		// ENDE -------  Gruppen aus der Datenbank holen, um zuweisen zu können werd darin lesen und schreiben darf ... ###
		$this->content->template['case1b'] = '1';
		$this->content->template['forumneu'] = '1';
		$this->content->template['insert'] = 'insertneuecht';
		// hier nochmal die Eingaben Überprüfen

		// Wenn ein Intranet existiert // b.legt ?? wie hier Intranet-Option beseitigen??
		if (!empty ($this->cms->intranet_yn)) {
			// Wenn Forum für das Intranet sein soll
			if ($this->cms->intranet_yn == 1) {
				$this->checkedintra = 'nodecode:checked="checked"';
			}
			// oder für das Internet
			if ($this->cms->intranet_yn == 0) {
				$this->checkedinter = 'nodecode:checked="checked"';
			}
		}

		// Wenn das Forum schon existiert, nichts machen
		if (empty ($exist)) {
			if ($this->checked->foruminsert == 'insertneu') {
				// Stimmern die Daten Text
				$this->content->template['text'] = $this->content->template['message_14'];
			}
			else {
				// Forum bearbeiten Text
				$this->content->template['text'] = $this->content->template['message_15'];
			}
		}
		if (empty ($this->checked->forname)) {
			$this->checked->forname = "";
		}
		if (empty ($this->checked->metabeschreibung)) {
			$this->checked->metabeschreibung = "";
		}

		// Daten in ein Array einlesen
		array_push($table_data, array (
			'forumid' => $this->checked->forumid,
			'forumname' => "nodecode:".$this->diverse->encode_quote($this->checked->forname),
			'Beschreibung' => "nodecode:".$this->checked->metabeschreibung,
			'insert' => 'insertneuecht',

		));
		$this->content->template['table_data'] = $table_data;
		// Kontrolle der Eingaben, wenn Bedingungen erfllt, dann abschicken
		if (strlen($this->checked->forname) > 0) {
			$this->content->template['insert'] = 'insertneuecht';
		}
		// Bedingung nicht erfüllt, nochmal laden
		else {
			$this->content->template['insert'] = 'insertneuecht';
		}

	}

	/**
	 * Hiermit können existierende Foren bearbeitet oder gelöscht werden
	 *
	 * @return void
	 */
	function change_forum()
	{
		IfNotSetNull($this->checked->forumid);
		// setzen für das Template
		$this->content->template['loeschen'] = 'loeschen';
		// menuid für das Template setzen
		$this->content->template['menuid_tmpl'] = $this->checked->menuid;

		$table_data = array ();
		if (empty ($this->checked->loeschenecht)) {
			$this->checked->loeschenecht = "";
			$this->content->template['forumid'] = $this->checked->forumid;
		}
		// Daten wurden Überprüft, also löschen... ###
		if ($this->checked->loeschenecht != "") {
			//Überprüfung Numeric
			if (is_numeric($this->checked->forumid)) {
				if ($this->checked->forumid == 10 or $this->checked->forumid == 20) {
					//header ("Location: ./forum.php?menuid=3&messageget=398");
					$location_url = "./forum.php?menuid=3&messageget=398";
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit;
				}
				else {
					// löschen
					$sqlloesch = "DELETE FROM " . $this->cms->papoo_forums . " WHERE forumid='" . $this->db->escape($this->checked->forumid) . "'";
					$this->db->query($sqlloesch);
					// Alte LookupEinträge löschen -- Leserechte
					$delete_read = "DELETE FROM " . $this->cms->papoo_lookup_forum_read . " WHERE forumid='" . $this->db->escape($this->checked->forumid) . "'";
					$this->db->query($delete_read);
					// Alte LookupEinträge löschen -- Schreibrechte
					$delete_write = "DELETE FROM " . $this->cms->papoo_lookup_forum_write . " WHERE forumid='" . $this->db->escape($this->checked->forumid) . "'";
					$this->db->query($delete_write);

					$this->content->template['forumid_tmpl'] = "XX";
					//header ("Location: ./forum.php?menuid=3&messageget=17");
					$location_url = "./forum.php?menuid=3&messageget=17";
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit;
				}
			}
		}
		else {
			// NICHT löschen
			if (empty ($this->checked->loeschen)) {
				$this->checked->loeschen = "";
			}
			// Daten sollen gelöscht werden, also Überprüfen durch den User ###
			if ($this->checked->loeschen != "") {
				$this->content->template['loesch'] = '1';

				array_push($table_data, array (
					'forumid' => $this->checked->forumid,
					'forumname' => $this->checked->forname,

				));
				// Daten fürs Template
				$this->content->template['table_data'] = $table_data;
			}
			else {
				if (empty ($this->checked->foruminsert)) {
					$this->checked->foruminsert = "";
				}
				// Eingabe der Änderung des Forums in die Datenbank ###
				if ($this->checked->foruminsert == "insertaltecht" and is_numeric($this->checked->forumid)) {
					// Alte LookupEinträge löschen -- Leserechte
					$delete_read = "DELETE FROM " .
						$this->cms->papoo_lookup_forum_read . " WHERE forumid='" . $this->db->escape($this->checked->forumid) . "'";
					$this->db->query($delete_read);
					// Alte LookupEinträge löschen -- Schreibrechte
					$delete_write = "DELETE FROM " .
						$this->cms->papoo_lookup_forum_write . " WHERE forumid='" . $this->db->escape($this->checked->forumid) . "'";
					$this->db->query($delete_write);

					// Lese und Schreibrechte eintragen
					$this->insert_gruppenrechte();

					// Wenn für Intranet dann setzen
					//if ($this->checked->intranet_yn == 1) $intranet_yn= 1;
					//else $intranet_yn= 0;

					//$sqlup= "UPDATE ".$this->cms->papoo_forums." SET forumname='".$this->db->escape($this->checked->forname)."', Beschreibung='".$this->db->escape($this->checked->metabeschreibung)."', intranet_yn='$intranet_yn' WHERE forumid='".$this->checked->forumid."'";
					$sqlup = "UPDATE " . $this->cms->papoo_forums . " SET forumname='" . $this->db->escape($this->checked->forname) . "', Beschreibung='" . $this->db->escape($this->checked->metabeschreibung) . "' WHERE forumid='" . $this->db->escape($this->checked->forumid) . "'";
					$this->db->query($sqlup);
					// NEu laden -- Ende
					//header ("Location: ./forum.php?menuid=41");
					$location_url = "./forum.php?menuid=41";
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit;
				}
				if (empty ($this->checked->bearb)) {
					$this->checked->bearb = "";
				}
				// Wenn kein bestimmtes Forum ausgewählt worden ist  alle anzeigen
				if ($this->checked->bearb != 1) {
					$this->content->template['table_data'] =$this->make_forum_liste();
				}
				// Wenn ein Forum ausgewählt ist ... berarbeiten ...
				elseif ($this->checked->bearb == 1) {
					// Text Forum bearbeiten
					$this->content->template['text'] = $this->content->template['message_15'];

					// Ein Intranet ist vorhanden?? also Auswahl für Intranet  anzeigen
					//if ($this->cms->intranetnow == 1) $this->content->template['intranet_yn']= '1';

					// Freischalten für das Template
					$this->content->template['case1b'] = '1';
					// Daten des ausgewähltren FOrums heraussuchen

					//$result= $this->db->get_results(" SELECT forumid, forumname, Beschreibung, intranet_yn FROM ".$this->cms->papoo_forums." WHERE forumid='".$this->db->escape($this->checked->forumid)."'"." ORDER BY forumname ");
					$result = $this->db->get_results(" SELECT forumid, forumname, Beschreibung FROM " . $this->cms->papoo_forums . " WHERE forumid='" . $this->db->escape($this->checked->forumid) . "'" . " ORDER BY forumname ");
					// Für jeden Eintrag durchgehen ?? b.legt: Wie hier Intranet-Option entfernen??
					foreach ($result as $row) {
						if (isset ($row->intranet_yn)) {
							if ($row->intranet_yn == 1) {
								$this->content->template['checkedintra'] = 'nodecode:checked="checked"';
							}
							if ($row->intranet_yn == 0) {
								$this->content->template['checkedinter'] = 'nodecode:checked="checked"';
							}
						}
						// Daten zuweisen in Array
						array_push($table_data, array (
							'forumid' => $row->forumid,
							'forumname' => $this->diverse->encode_quote($row->forumname),
							'Beschreibung' => "nobr:" . $row->Beschreibung,
						));
					}
					// Array an Template Übermitteln
					$this->content->template['table_data'] = $table_data;
					// für die spätere Unterscheidung im Formular
					$this->content->template['insert'] = 'insertaltecht';
					// Forumid für das Formular
					$this->content->template['forumid_tmpl'] = $this->checked->forumid;

					// Lese auslesen
					$sqlread = "SELECT gruppenid from " . $this->cms->papoo_lookup_forum_read . " WHERE forumid = '" . $this->db->escape($this->checked->forumid) . "'";
					$resultread = $this->db->get_results($sqlread);
					// Schreibrechte auslesen
					$sqlwrite = "SELECT gruppenid from " . $this->cms->papoo_lookup_forum_write . " WHERE forumid = '" . $this->db->escape($this->checked->forumid) . "'";
					$resultwrite = $this->db->get_results($sqlwrite);

					// Alle Gruppen auslesen
					$sqlroot = "SELECT * FROM " . $this->cms->papoo_gruppe . "";
					$resultroot = $this->db->get_results($sqlroot);
					//Lese und Schreibrechte zuweisen
					foreach ($resultroot as $row) {
						// Variablen wieder auf leer setzen
						$selected_read = $selected_write = "";
						// checken ob Leserechte angehackt werden
						if (!empty ($resultread)) {
							foreach ($resultread as $read) {
								if ($row->gruppeid == $read->gruppenid) {
									$selected_read = 'nodecode:checked="checked"';
								}
							}
						}
						// checken ob Schreibrechte angehackt werden
						if (!empty ($resultwrite)) {
							foreach ($resultwrite as $write) {
								if ($row->gruppeid == $write->gruppenid) {
									$selected_write = 'nodecode:checked="checked"';
								}
							}
						}
						// Daten ins Array Leserechte
						array_push($this->content->template['group_data_read'], array (
							'gruppeid' => $row->gruppeid,
							'gruppename' => $row->gruppenname,
							'checked' => $selected_read,
						));

						// Daten ins Array Schreibrechte
						array_push($this->content->template['group_data_write'], array (
							'gruppeid' => $row->gruppeid,
							'gruppename' => $row->gruppenname,
							'checked' => $selected_write,
						));
					}
				}
			}
		}
	}

	/**
	 * Liste der Foren erstellen
	 *
	 * @return array
	 */
	function make_forum_liste()
	{
		$table_data=array();
		// Template auf Listing setzen
		$this->content->template['case1'] = '1';
		// Foren aus der Datenbank holen
		$result = $this->db->get_results("SELECT forumid, forumname, Beschreibung FROM " . $this->cms->papoo_forums . " ORDER BY forumname ");
		if (!empty ($result)) {
			// und in ein Array weisen
			foreach ($result as $row) {
				array_push($table_data, array (
					'forumid' => $row->forumid,
					'forumname' => $row->forumname,
					'Beschreibung' => $row->Beschreibung,
					'insert' => 'insertalt',
				));
			}
		}
		return $table_data;
	}

	/**
	 * @return void
	 * @desc Hiermit wird eine Message bzw. der gesamte thread gelöscht oder eine Nachricht verändert, editiert.
	 */
	function change_message()
	{
		$message_data = array ();
		if (empty ($this->checked->loeschenecht)) {
			$this->checked->loeschenecht = "";
		}

		// Nachricht löschen, Daten wurden Überprüft... ###
		if ($this->checked->loeschenecht != "") {
			/**
			 * Wenn rootid o ist, dann würden alle Nchrichten mit 0 gelöscht, das muß
			 * verhindert werden daher rootid auf msgid setzen. Dadurch wird die
			 * aktuelle Nachricht trotzdem gelöscht, weil sie Über msgid aufgerufen
			 * wird.
			 *
			 * Alle anderen abhängigen Messages werden auch gelöscht, da die rootid =
			 * der aktuellen msgid ist, wenn die rootid der zu löschenden Message =0
			 * ist.
			 *
			 * Aber die msgid darf nicht leer sein
			 */
			if (!empty ($this->checked->formmsgid)) {
				if ($this->checked->rootid == "0" or empty ($this->checked->rootid)) {
					$this->checked->rootid = $this->checked->formmsgid;
				}
				$sqlloesch = ("DELETE FROM " . $this->cms->papoo_message . " WHERE msgid='" . $this->db->escape($this->checked->rootid) . "' OR rootid='" . $this->db->escape($this->checked->rootid) . "' OR msgid='" . $this->db->escape($this->checked->rootid) . "'");
				// ABfrage durchführen -> löschen
				$this->db->query($sqlloesch);
			}
		}
		// ENDE ----  Nachricht löschen, Daten wurden Überprüft... ###
		// Nachricht soll gelöscht werden daher Daten zur Überprüfung eingeben ,,, ###
		else {
			if (empty ($this->checked->loesch)) {
				$this->checked->loesch = "";
			}
			if ($this->checked->loesch != "") {
				// Template auf nächste Stufe setzen.
				$this->content->template['loeschen'] = 'Löschen';
				$this->content->template['messageupdaten'] = 'messageupdateecht';
				$this->content->template['loeschecht'] = 'Löschen';
				// rootid auf 0 setzen, wenn leer
				if ($this->checked->rootid == "0" or empty ($this->checked->rootid)) {
					$this->checked->rootid = "0";
				}
				// Daten in ein Array laden
				array_push($message_data, array (
					'rootid' => $this->checked->rootid,
					'msgid' => $this->checked->msgid,
					'betreff' => $this->checked->betreff,
					'message' => $this->checked->message_text,
					'messageuser' => $this->checked->messageuser_form,

				));
				// Daten zuweisen für das Template
				$this->content->template['tmpl_last_messages'] = $message_data;
			}
			// ENDE ---  Nachricht soll gelöscht werden daher Daten zur Überprüfung eingeben ,,, ###
			// Nicht löschen sondern bearbeiten.... ####
			else {
				$message_data = array ();
				if (empty ($this->checked->messageupdateecht)) {
					$this->checked->messageupdateecht = "";
				}
				// Datenbank updaten, wenn so gewollt
				if ($this->checked->messageupdateecht != "") {
					//$this->checked->betreff=$this->db->escape($this->checked->betreff);
					//$this->checked->message_text = $this->db->escape($this->checked->message_text);
					// Abfrage formulieren
					$this->db->query("UPDATE " . $this->cms->papoo_message . " SET thema='" . $this->db->escape($this->checked->betreff) . "', messagetext='" . $this->db->escape($this->checked->message_text) . "' , msg_frei='" . $this->db->escape($this->checked->msg_frei) . "' , email='" . $this->db->escape($this->checked->email) . "' , username_guest='" . $this->db->escape($this->checked->name) . "' WHERE msgid='" . $this->db->escape($this->checked->msgid) . "'");
					//Checken ob forumid sich geändert hat
					//Forum id
					$sql=sprintf("SELECT forumid FROM %s WHERE msgid='%s'",
						$this->cms->tbname['papoo_message'],
						$this->db->escape($this->checked->msgid)
					);
					$forumid=$this->db->get_var($sql);
					if ($forumid!=$this->checked->forumid) {
						$this->change_message_forum($this->checked->msgid,$this->checked->forumid);
					}

					// Seite neu laden
					//header ("Location: ./forum.php?menuid=3");
					$location_url = "./forum.php?menuid=42";
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit;
				}
				if (empty ($this->checked->msgid)) {
					$this->checked->msgid = "";
				}
				// Wenn eine Message ausgewählt ist ###
				if ($this->checked->msgid != "") {
					if (empty ($this->checked->messageupdate)) {
						$this->checked->messageupdate = "";
					}
					//Foren anzeigen
					$this->content->template['forumliste'] =$this->make_forum_liste();
					$this->content->template['case1'] = '';

					// Wenn eine bearbeitet wurde ###
					if ($this->checked->messageupdate != "") {
						// Template setzen
						$this->content->template['case2b'] = '1';
						$this->content->template['messageupdaten'] = 'messageupdateecht';
						$this->content->template['button'] = 'Eintragen';
						$this->content->template['speichern_nachricht'] = 'Bitte klicken Sie erneut auf Eintragen';
						// Message text bereinigen
						//$this->checked->betreff=(stripslashes($this->checked->betreff));
						//$this->message_text = stripslashes(stripslashes($this->checked->message_text));
						array_push($message_data, array (
							'msgid' => $this->checked->msgid,
							'forumid' => $this->checked->forumid,
							'betreff' => $this->checked->betreff,
							'email' => $this->checked->email,
							'name' => $this->checked->name,
							'msg_frei' => $this->checked->msg_frei,
							'message' => "nobr:" . $this->checked->message_text,
							'messageuser' => $this->checked->messageuser_form,

						));
						$this->content->template['loeschen'] = 'Löschen';
						// Daten zuweisen in Template
						$this->content->template['message_data'] = $message_data;
					}
					// Message bearbeiten
					else {
						// Template setzen
						$this->content->template['case2b'] = '1';
						$this->content->template['speichern_nachricht'] = '';
						// Nachricht raussuchen mit der gewählten msgid
						$selectmessage = "SELECT rootid, msgid, email, username_guest, thema, messagetext, userid, msg_frei FROM " . $this->cms->papoo_message . " WHERE  msgid = '" . $this->db->escape($this->checked->msgid) . "' ";
						//Forum id
						$sql=sprintf("SELECT forumid FROM %s WHERE msgid='%s'",
							$this->cms->tbname['papoo_message'],
							$this->db->escape($this->checked->msgid)
						);
						$forumid=$this->db->get_var($sql);

						// Daten aus der Datenbank holen
						$resultmessage = $this->db->get_results($selectmessage);

						// in ein Array weisen
						foreach ($resultmessage as $row) {
							$messageuserid = $row->userid;
							array_push($message_data, array (
								'rootid' => $row->rootid,
								'name' => $row->username_guest,
								'forumid'=>$forumid,
								'msgid' => $row->msgid,
								'betreff' => $row->thema,
								'email' => $row->email,
								'msg_frei' => $row->msg_frei,
								'message' => "nobr:" . $row->messagetext,
							));
						}
						// Daten dem Template zuweisen
						$this->content->template['message_data'] = $message_data;
						// Usernamen raussuchen
						$messageuserid = $this->db->escape($messageuserid);
						$selectmessageuser = "SELECT username FROM " . $this->cms->papoo_user . " WHERE  userid = '$messageuserid'";
						$resultmessageuser = $this->db->get_results($selectmessageuser);
						if (!empty ($resultmessageuser)) {
							foreach ($resultmessageuser as $rowuser) {
								$messageuser = $rowuser->username;
							}
						}
						IfNotSetNull($messageuser);
						// weitere Daten dem Template zuweisen
						$this->content->template['messageuser'] = $messageuser;
						$this->content->template['messageupdaten'] = 'messageupdate';
						$this->content->template['button'] = 'Übertragen';
						$this->content->template['loeschen'] = 'Löschen';
					}
					//FIXME --- SPRACHE
				}
				else {
					//Forum Klasse
					require_once(PAPOO_ABS_PFAD."/lib/classes/forum_class.php");
					$forum = new forum();

					if (!empty($this->checked->formSubmit_messages)) {
						$this->forum_beitraege_loeschen_oder_freigeben();
					}
					/// LIste der verfügbaren Foren
					$forum->forum_list();
					// die letzten 10
					if (empty($this->checked->search)) {
						$this->last_messages();
						$this->content->template['tmpl_last_messages'] = $this->tmpl_last_messages;
					}
					// Template richtig setzen
					$this->content->template['case2'] = '1';
					if (empty ($this->checked->search)) {
						$this->checked->search = "";
					}

					if ($this->checked->search) {
						$this->checked->xsuch = '1';
						$this->forum_search();
						$this->content->template['tmpl_last_messages'] = $this->message_data_such;
					}
				}
			}
		}
	}

	/**
	 * forum::ext_search()
	 *
	 * @param int $id
	 * @return void
	 */
	function ext_search($id=0)
	{
		if (!empty($this->cms->tbname['plugin_ext_search_page'])) {
			//Diese Klasse setzt die Suche um!!
			require_once PAPOO_ABS_PFAD."/plugins/extended_search/lib/class_search_create.php";

			//Klasse initialisieren und zur Verfügung stellen
			$search_create = new class_search_create();

			$search_create->create_page_front($id);
		}
	}

	/**
	 * intern_forum::forum_beitraege_loeschen_oder_freigeben()
	 * Hier werden alle ausgewählten Beiträge
	 * auf einen Rutsch gelöscht oder freigegeben.
	 * @return void
	 */
	function forum_beitraege_loeschen_oder_freigeben()
	{
		//erstmal alle deaktivieren
		if (is_array($this->checked->_message_setfree)) {
			foreach ($this->checked->_message_setfree as $key => $value) {
				$sql=sprintf("UPDATE %s SET msg_frei='0' WHERE msgid='%d'",
					$this->cms->tbname['papoo_message'],
					$this->db->escape($key)
				);
				$this->db->query($sql);
			}
		}
		//dann alle aktivieren
		if (isset($this->checked->message_setfree) && is_array($this->checked->message_setfree)) {
			foreach ($this->checked->message_setfree as $key=>$value) {
				if ($value==1 && is_numeric($key)) {
					if ($this->checked->message_setfree[$key]==1) {
						$sql = sprintf("UPDATE %s SET msg_frei='1' WHERE msgid='%d'",
							$this->cms->tbname['papoo_message'],
							$this->db->escape($key)
						);
						$this->db->query($sql);
					}
					//das greift nie
					else {
						$sql = sprintf("UPDATE %s SET msg_frei='0' WHERE msgid='%d'",
							$this->cms->tbname['papoo_message'],
							$this->db->escape($key)
						);
						$this->db->query($sql);
					}
				}
			}
		}

		if (isset($this->checked->message_del) and is_array($this->checked->message_del)) {
			foreach ($this->checked->message_del as $key=>$value) {
				if ($value==1 && is_numeric($key)) {
					$sql = sprintf("DELETE FROM %s WHERE msgid='%d' LIMIT 1",
						$this->cms->tbname['papoo_message'],
						$this->db->escape($key)
					);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * Foren Zugehörigkeit eines Beitrages ändern
	 *
	 * @param $msgid
	 * @param $forumid
	 */
	function change_message_forum($msgid,$forumid)
	{
		//rootid rausholen
		$sql=sprintf("SELECT rootid FROM %s WHERE msgid='%s'",
			$this->cms->tbname['papoo_message'],
			$this->db->escape($msgid)
		);
		$rootid=$this->db->get_var($sql);

		if ($rootid == "0" or empty ($rootid)) {
			$rootid = $msgid;
		}
		$sql=sprintf("UPDATE %s SET forumid='%s' WHERE msgid='%s' OR rootid='%s'",
			$this->cms->tbname['papoo_message'],
			$this->db->escape($forumid),
			$this->db->escape($rootid),
			$this->db->escape($rootid)
		);
		$this->db->query($sql);
		/**
		$sql = ("DELETE FROM " . $this->cms->papoo_message . " WHERE msgid='" . $this->db->escape($this->checked->rootid) . "' OR rootid='" . $this->db->escape($this->checked->rootid) . "' OR msgid='" . $this->db->escape($this->checked->rootid) . "'");
		 */
		$this->ext_search($msgid);
	}

	/**
	 * Mit dieser Methode wird das Forum nach bestimmten Stichwörtern durchsucht
	 *
	 * @return void
	 */
	function forum_search()
	{
		// Array erstellen
		$message_data = array ();
		// Es wurde gesucht -> Template
		$this->content->template['gesucht'] = 1;
		$selectmessage = "SELECT " . $this->cms->papoo_message . ".msgid, thema, counten, forumid, level, rootid, ordnung,";
		$selectmessage .= "  " . $this->cms->papoo_message . ".zeitstempel AS zeitstempel, ";
		$selectmessage .= "  username FROM " . $this->cms->papoo_message . ", " . $this->cms->papoo_user . " WHERE  ";

		$selectmessage .= " thema LIKE '%" . $this->db->escape($this->checked->search) . "%' AND ";
		$selectmessage .= " " . $this->cms->papoo_message . ".userid = " . $this->cms->papoo_user . ".userid OR ";
		$selectmessage .= " messagetext LIKE '%" . $this->db->escape($this->checked->search) . "%' AND " . $this->cms->papoo_message . ".userid = " . $this->cms->papoo_user . ".userid  ";

		$selectmessage .= " ORDER BY msgid DESC ";
		#echo $selectmessage;
		$resultmessage = $this->db->get_results($selectmessage);
		$this->weiter->result_anzahl=count($resultmessage);
		$this->weiter->weiter_link="forum.php?menuid=42";
		$this->weiter->do_weiter("search");

		// Die Ergebnisse  aus der Datenbank holen und anzeigen...
		$selectmessage = "SELECT " . $this->cms->papoo_message . ".msgid, thema, counten, forumid, level, rootid, ordnung,";
		$selectmessage .= "  " . $this->cms->papoo_message . ".zeitstempel AS zeitstempel, ";
		$selectmessage .= "  username FROM " . $this->cms->papoo_message . ", " . $this->cms->papoo_user . " WHERE  ";

		$selectmessage .= " thema LIKE '%" . $this->db->escape($this->checked->search) . "%' AND ";
		$selectmessage .= " " . $this->cms->papoo_message . ".userid = " . $this->cms->papoo_user . ".userid OR ";
		$selectmessage .= " messagetext LIKE '%" . $this->db->escape($this->checked->search) . "%' AND " . $this->cms->papoo_message . ".userid = " . $this->cms->papoo_user . ".userid  ";

		$selectmessage .= " ORDER BY msgid DESC ".$this->cms->sqllimit;
		// Datenbank Abfrage durchführen
		$resultmessage = $this->db->get_results($selectmessage);
		// Wenn nicht leer, dann in Array einlesen
		if (!empty ($resultmessage)) {
			// Für jedes Ergebnis ein Eintrag ins Array
			foreach ($resultmessage as $row) {
				// Daten zuweisen in das loop array fürs template... ###
				array_push($message_data, array (
					'rootid' => $row->rootid,
					'msgid' => $row->msgid,
					'forumid' => $row->forumid,
					'menuid' => $this->checked->menuid,
					'username' => $row->username,
					'zeitstempel' => $row->zeitstempel,
					'counten' => $row->counten,
					'thema' => $row->thema
				));
			}
			// zuweisen für das Template
			$this->message_data_such = $message_data;
			#$this->content->template['message_data'] = $message_data;
		}
		// Keine Ergebnisse dann,
		else {
			// Text ausgeben!
			$this->content->template['content_text'] = $this->content->template['message_5'];
		}
	}

	/**
	 * @return void
	 * @desc Hier werden die Gruppen Lese und Schreibrechte eingetragen in die Lookup Tabellen
	 */
	function insert_gruppenrechte()
	{
		// Leserechte zählen
		if (!empty ($this->checked->gruppe_read)) {
			$countread = count($this->checked->gruppe_read);
		}
		else {
			$countread = 0;
		}
		// Schreibrechte zählen
		if (!empty ($this->checked->gruppe_write)) {
			$countwrite = count($this->checked->gruppe_write);
		}
		else {
			$countwrite = 0;
		}
		// Leserechte eintragen
		for ($i = 0; $i < $countread; $i++) {
			$insert_read = "INSERT INTO " . $this->cms->papoo_lookup_forum_read . " SET forumid='" . $this->db->escape($this->checked->forumid) . "', gruppenid='" . $this->db->escape($this->checked->gruppe_read[$i]) . "'";
			$this->db->query($insert_read);
		}
		// Schreibrechte eintragen
		for ($i = 0; $i < $countwrite; $i++) {
			$insert_write = "INSERT INTO " . $this->cms->papoo_lookup_forum_write . " SET forumid='" . $this->db->escape($this->checked->forumid) . "', gruppenid='" . $this->db->escape($this->checked->gruppe_write[$i]) . "'";
			$this->db->query($insert_write);
		}
	}

	/**
	 * @return void
	 * @desc Hier werden die letzten 10? Messages herausgeholt und zurückgegeben
	 */
	function last_messages()
	{
		if ($this->user->userid != 10) {
			$sql = sprintf("SELECT COUNT(msgid) FROM %s T1, %s T2 WHERE T1.userid = T2.userid",
				$this->cms->papoo_message,
				$this->cms->papoo_user
			);
			$selectmessage = sprintf("SELECT msgid,
												msg_frei,
												messagetext,
												thema,
												counten,
												forumid,
												level,
												rootid,
												COALESCE(T1.email, T2.email) email,
												artikelurl,
												ordnung,
												T1.zeitstempel zeitstempel,
												username
												FROM %s T1, %s T2
												WHERE T1.userid = T2.userid
												ORDER BY msgid DESC %s",
				$this->cms->papoo_message,
				$this->cms->papoo_user,
				$this->cms->sqllimit
			);
		}
		else {
			$sql = sprintf("SELECT COUNT(msgid)
									FROM %s T1
									LEFT JOIN %s T2 ON (T1.userid = T2.userid)",
				$this->cms->papoo_message,
				$this->cms->papoo_user
			);
			$selectmessage = sprintf("SELECT msgid,
												msg_frei,
												messagetext,
												thema,
												counten,
												forumid,
												username_guest,
												COALESCE(T1.email, T2.email) email,
												artikelurl,
												level,
												rootid,
												ordnung,
												T1.zeitstempel zeitstempel,
												username
												FROM %s T1
												LEFT JOIN %s T2 ON (T1.userid = T2.userid)
												ORDER BY msgid DESC %s",
				$this->cms->papoo_message,
				$this->cms->papoo_user,
				$this->cms->sqllimit
			);
		}
		$this->weiter->result_anzahl=$this->db->get_var($sql);
		$this->weiter->weiter_link="forum.php?menuid=42";
		$this->weiter->do_weiter("teaser");
		// Datenbank Abfrage starten
		$message_data = $this->db->get_results($selectmessage, ARRAY_A);
		// Wenn Ergebnisse da sind,
		// Link zum Thread zuweisen, je nach mod_rewrite Zustand
		if ($this->cms->mod_rewrite == 1) {
			$this->forumthread_link = "forumthread.php";
		}
		if ($this->cms->mod_rewrite == 2) {
			$this->forumthread_link = "forumthread";
		}
		// Daten zurückgeben
		$this->tmpl_last_messages = $message_data;
	}
}

$intern_forum = new intern_forum();