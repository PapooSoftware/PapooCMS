<?php
/**
 * #####################################
 * # Papoo CMS                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.2                  #
 * #####################################
 */

/**
 * Class intern_user
 */
class intern_user
{
	/**
	 * intern_user constructor.
	 */
	function __construct()
	{
		/**
		 * Klassen globalisieren
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
		// Interne Menüklasse einbinden
		global $intern_menu;
		// global diverse
		global $diverse;

		/**
		 * und einbinden in die Klasse
		 */
		// Hier die Klassen als Referenzen
		$this->cms = &$cms;
		$this->db = &$db;
		$this->content = &$content;
		$this->message = &$message;
		$this->user = &$user;
		$this->weiter = &$weiter;
		$this->searcher = &$searcher;
		$this->checked = &$checked;
		$this->intern_menu = &$intern_menu;
		$this->diverse = &$diverse;

		IfNotSetNull($this->content->template['text_error']);
		IfNotSetNull($this->content->template['user_message_text']);
		IfNotSetNull($this->content->template['loeschgruppe']);
		IfNotSetNull($this->content->template['neugroup']);
		IfNotSetNull($this->content->template['userlist']);
		IfNotSetNull($this->content->template['loeschen']);
		IfNotSetNull($this->content->template['userbearb']);
		IfNotSetNull($this->content->template['neuuser']);
		IfNotSetNull($this->content->template['checked_internet']);
		IfNotSetNull($this->content->template['admin_checked']);
		IfNotSetNull($this->content->template['system_error']);
		IfNotSetNull($this->content->template['user_konfiguration_des_tinymce']);
		IfNotSetNull($this->content->template['beschreibung']);
		IfNotSetNull($this->content->template['gruppeid']);
		IfNotSetNull($this->content->template['altgruppe']);
		IfNotSetNull($this->content->template['gruppenname']);
		IfNotSetNull($this->content->template['module_aktiv']);
		IfNotSetNull($this->content->template['manageprofil']);
		IfNotSetNull($this->content->template['nouser']);
		IfNotSetNull($this->content->template['groupselect_form']);
		IfNotSetNull($this->content->template['selected']);
	}

	/**
	 * Inhalt erstellen für die User bzw. Gruppenbearbeitung
	 *
	 * @return void
	 */
	function make_inhalt()
	{
		// Überprüfen ob Zugriff auf die Inhalte besteht
		$this->user->check_access();
		// Arrays init
		$this->content->template['gruppelesen_data'] = array();
		$this->content->template['userlesen_data'] = array();
		$this->content->template['userschreiben_data'] = array();

		switch ($this->checked->menuid) {
		case "4" :
			// Starttext Übergeben
			if (empty ($this->checked->messageget)) {
				$this->content->template['text'] = $this->content->template['message_18'];
			} // Gruppe wurde eingetragen
			elseif ($this->checked->messageget == 50) {
				$this->content->template['text'] = $this->content->template['message_18'] . $this->content->template['message_50'];
			} // User eingetragen
			elseif ($this->checked->messageget == 52) {
				$this->content->template['text'] = $this->content->template['message_18'];
				$this->content->template['user_message_text'] = $this->content->template['message_52'];
			} // User gelöscht
			elseif ($this->checked->messageget == 53) {
				$this->content->template['text'] = $this->content->template['message_18'] . $this->content->template['message_53'];
			} // root kann nicht gelöscht werden
			elseif ($this->checked->messageget == 54) {
				$this->content->template['text'] = $this->content->template['message_18'] . $this->content->template['message_54'];
			} // jeder kann nicht gelöscht werden
			elseif ($this->checked->messageget == 55) {
				$this->content->template['text'] = $this->content->template['message_18'] . $this->content->template['message_55'];
			} // Gruppe wurde gelöscht
			elseif ($this->checked->messageget == 57) {
				$this->content->template['text'] = $this->content->template['message_18'] . $this->content->template['message_57'];
			} // Gruppe root kann nicht gelöscht werden
			elseif ($this->checked->messageget == 58) {
				$this->content->template['text'] = $this->content->template['message_18'] . $this->content->template['message_58'];
			} // Gruppe jeder kann nicht gelöscht werden
			elseif ($this->checked->messageget == 59) {
				$this->content->template['text'] = $this->content->template['message_18'] . $this->content->template['message_59'];
			}
			break;
			// Hier wird eine neue Gruppe angelegt
		case "6" :
			$this->content->template['menuid_form'] = 6;
			// Formular anzeigen für Template
			$this->gesetzt = "NOK";
			if (empty ($this->checked->formSubmit)) {
				$this->checked->formSubmit = "";
			}
			// Es wurde auf Übermitteln gedrückt
			if ($this->checked->formSubmit != "") {
				// Wenn alle Sachen korrekt ausgefüllt sind
				if ($this->checked->groupname != "" and $this->checked->gruppenleiter != "none" and $this->checked->kategorie != "" and $this->checked->beschreibung != "") {
					// Abfrage formulieren
					// $sql= " INSERT INTO ".$this->cms->papoo_gruppe." SET gruppenname='".$this->db->escape($this->checked->groupname)."',"." gruppenleiter='".$this->db->escape($this->checked->gruppenleiter)."',admin_zugriff='".$this->db->escape($this->checked->admin_zugriff)."', elterngruppe='".$this->db->escape($this->checked->kategorie)."', "." gruppen_beschreibung='".$this->db->escape($this->checked->beschreibung)."', allow_internet='".$this->db->escape($this->checked->allow_internet)."', allow_intranet='".$this->db->escape($this->checked->allow_intranet)."', intranet='".$this->db->escape($this->checked->intranet_zu)."', publish_user='".$this->db->escape($this->checked->userleiter)."' ";
					$sql = " INSERT INTO " . $this->cms->papoo_gruppe . "
					SET gruppenname='" . $this->db->escape($this->checked->groupname) . "'," . " gruppenleiter='" . $this->db->escape($this->checked->gruppenleiter) . "',admin_zugriff='" . $this->db->escape($this->checked->admin_zugriff) . "', elterngruppe='" . $this->db->escape($this->checked->kategorie) . "', " . " gruppen_beschreibung='" . $this->db->escape($this->checked->beschreibung) . "', allow_internet='" . $this->db->escape($this->checked->allow_internet) . "', publish_user='" . $this->db->escape($this->checked->userleiter) . "', user_konfiguration_des_tinymce='" . $this->db->escape($this->checked->user_konfiguration_des_tinymce) . "' ";
					// in die Datenbank eintragen
					$this->db->query($sql);
					// Userstartseite neu laden
					// header("Location: ./user.php?menuid=4&messageget=50");
					$location_url = "./user.php?menuid=9&messageget=50";
					if ($_SESSION['debug_stopallredirect'])
						echo '<a href="' . $location_url . '">Weiter</a>';
					else
						header("Location: $location_url");
					exit;
				} // es ist nicht alles korrekt ausgefüllt
				else {
					// Message ausgeben
					$this->content->template['text_error'] = $this->content->template['message_48a'];
				}
			}
			else {
				// Daten für neues Formular anzeigen ###############
				if ($this->gesetzt == "NOK") {
					// Variable setzen für die if Abfrage im Template ###
					$this->content->template['neugroup'] = '1';
					// Gruppe zuweisen für die Auswahl der Elterngruppe
					$result = $this->db->get_results("SELECT gruppenname, gruppeid FROM " . $this->cms->papoo_gruppe . " ORDER BY gruppenname ASC ");

					foreach ($result as $row) {
						array_push($this->content->template['gruppelesen_data'], array('gruppelesen_name' => $row->gruppenname, 'gruppelesen_value' => $row->gruppeid,));
					}
					// User zuweisen für die Auswahl des Gruppenleiters
					$resultuser = $this->db->get_results("SELECT username, userid FROM " . $this->cms->papoo_user . "  ORDER BY username ASC ");
					foreach ($resultuser as $row) {
						array_push($this->content->template['userlesen_data'], array('userlesen_name' => $row->username,
							'userlesen_value' => $row->userid,
						));
					}
					// User zuweisen für die Auswahl des Gruppenleiters
					$sql = "SELECT DISTINCT(username), " . $this->cms->papoo_user . ".userid FROM " . $this->cms->papoo_user . ", " . $this->cms->papoo_gruppe . ", " . $this->cms->papoo_lookup_ug . "  WHERE
						" . $this->cms->papoo_lookup_ug . ".userid=" . $this->cms->papoo_user . ".userid
						AND " . $this->cms->papoo_lookup_ug . ".gruppenid=gruppeid AND  allow_internet='1'
						ORDER BY username ASC ";

					$resultuser = $this->db->get_results($sql);
					// if (!empty($resultuser))
					foreach ($resultuser as $row) {
						array_push($this->content->template['userschreiben_data'], array('userlesen_name' => $row->username,
							'userlesen_value' => $row->userid,
						));
					}
				}
			}

			break;
			// Ende neue Gruppe
			// Neuer User
		case "7" :
			$gesetzt = "NOK";
			if (empty ($this->checked->formSubmit)) {
				$this->checked->formSubmit = "";
			}
			if ($this->checked->formSubmit != "") {
				if ($this->checked->neuusername != "" and $this->checked->formpassword != "" and $this->checked->email != "") {
					// checken ob der User schon existiert
					$sql = "SELECT username FROM " . $this->cms->papoo_user . " WHERE username='" . $this->db->escape($this->checked->neuusername) . "' LIMIT 1 ";
					$resultname = $this->db->get_results($sql);
					// wenn ja, dann Fehler auswerfen
					if (count($resultname) > 0) {
						$this->content->template['text'] = $this->content->template['message_51'];
					} // wenn nicht dann eintragen
					else {
						if ($this->checked->antwortmail == "ok") {
							$antwortmail = 1;
						}
						else {
							$antwortmail = 0;
						}
						if ($this->checked->dauer_einlogg == "ok") {
							$this->checked->dauer_einlogg = 1;
						}
						else {
							$this->checked->dauer_einlogg = 0;
						}
						//$heute = date("Y.m.d  G:i:s");
						// Passwort verschlüsseln
						$hashed_password = $this->diverse->hash_password($this->checked->formpassword);
						$confirm_code = $this->createCode($this->checked->neuusername);
						// Eingabe formulieren
						$sql = "INSERT INTO " . $this->cms->papoo_user;
						$sql .= " (username, email, password, password_org, antwortmail, zeitstempel, user_vorname, user_nachname, user_strasse, user_ort, user_plz,user_style_id,dauer_einlogg, active, confirm_code, board, beschreibung, user_lang_front, user_lang_back,user_newsletter) ";
						$sql .= "VALUES ('" . $this->db->escape($this->checked->neuusername) . "', '" . $this->db->escape($this->checked->email) . "', ";
						$sql .= " '$hashed_password','" . $this->db->escape('xxx') . "', '$antwortmail', NOW(),";
						$sql .= " '" . $this->db->escape($this->checked->neuvorname) . "','" . $this->db->escape($this->checked->neunachname) . "','" . $this->db->escape($this->checked->neustrnr) . "', ";
						$sql .= " '" . $this->db->escape($this->checked->neuort) . "','" . $this->db->escape($this->checked->neuplz) . "','" . $this->db->escape($this->checked->stylepost) . "', ";
						$sql .= " '" . $this->db->escape($this->checked->dauer_einlogg) . "', '" . $this->db->escape($this->checked->active) . "', '" . $confirm_code . "','" . $this->db->escape($this->checked->forum_board) . "','" . $this->db->escape($this->checked->beschreibung) . "', ";
						$sql .= " '" . $this->db->escape($this->checked->lang_front) . "','" . $this->db->escape($this->checked->lang_back) . "','" . $this->db->escape($this->checked->user_newsletter) . "') ";

						$this->db->query($sql);
						// Neue Userid erfahr
						$userid = $this->db->insert_id;
						// wenn die gruppenid nicht leer ist
						if (!empty ($this->checked->gruppelesen)) {
							// neue Einträge erstellen
							foreach ($this->checked->gruppelesen as $insert) {
								$sqlin = "INSERT INTO " . $this->cms->papoo_lookup_ug . " SET userid='$userid', gruppenid='$insert' ";
								$this->db->query($sqlin);
							}
						} // ansonsten wird die gruppenid auf jeder gesetzt
						else {
							// neue Einträge erstellen
							$sqlin = "INSERT INTO " . $this->cms->papoo_lookup_ug . " SET userid='$userid', gruppenid='10' ";
							$this->db->query($sqlin);
						}
						// zur Startseite
						// header("Location: ./user.php?menuid=4&messageget=52");
						$location_url = "./user.php?menuid=4&messageget=52";
						if ($_SESSION['debug_stopallredirect']) {
							echo '<a href="' . $location_url . '">Weiter</a>';
						}
						else {
							header("Location: $location_url");
						}
						exit;
					}
				}
				else {
					$gesetzt = "NOK";
					$this->content->template['text_error'] = $this->content->template['message_48'];
				}
			}
			if ($gesetzt == "NOK") {
				$this->show_form();
			}

			break;
			// User bearbeiten
		case "8" :
			$this->change_user();
			break;
			// Gruppe bearbeiten
		case "9" :
			$this->content->template['menuid_form'] = 9;
			if (empty ($this->checked->loeschenecht)) {
				$this->checked->loeschenecht = "";
			}
			// Es soll gelöscht werden, Überprüft also löschen ###
			if ($this->checked->loeschenecht != "") {
				$gruppeid = $this->db->escape($this->checked->gruppeid);
				// checken ob root oder jeder ausgewählt ist
				if ($gruppeid != "1" and $gruppeid != "10") {
					$sqlloesch = "DELETE FROM " . $this->cms->papoo_gruppe . " WHERE gruppeid='$gruppeid' LIMIT 1";
					// echo $sqlloesch;
					$this->db->query($sqlloesch);
					// header("Location: ./user.php?menuid=4&messageget=57");
					$location_url = "./user.php?menuid=9&messageget=57";
					if ($_SESSION['debug_stopallredirect'])
						echo '<a href="' . $location_url . '">Weiter</a>';
					else
						header("Location: $location_url");
					exit;
				}
				else {
					// root, dann kann nicht gelöscht werden
					if ($gruppeid == "1") {
						// header("Location: ./user.php?menuid=4&messageget=58");
						$location_url = "./user.php?menuid=9&messageget=58";
						if ($_SESSION['debug_stopallredirect'])
							echo '<a href="' . $location_url . '">Weiter</a>';
						else
							header("Location: $location_url");
						exit;
					}
					// jeder, dann kann nicht gelöscht werden
					if ($gruppeid == "10") {
						// header("Location: ./user.php?menuid=4&messageget=59");
						$location_url = "./user.php?menuid=9&messageget=59";
						if ($_SESSION['debug_stopallredirect'])
							echo '<a href="' . $location_url . '">Weiter</a>';
						else
							header("Location: $location_url");
						exit;
					}
				}
			}
			// Nicht löschen, daher ...
			else {
				if (empty ($this->checked->loeschen)) {
					$this->checked->loeschen = "";
				}
				// Löschmöglichkeit anzeigen, Daten Überprüfen --- Fragen  ob wirklich gelöscht werden soll###
				if ($this->checked->loeschen != "") {
					$this->content->template['loeschgruppe'] = '1';
					array_push($this->content->template['gruppelesen_data'], array('gruppenname' => $this->checked->groupname, 'gruppeid' => $this->checked->gruppeid,));
				} // Kein löschen ausgewählt, do the rest ###
				else {
					if (empty ($this->checked->formSubmit)) {
						$this->checked->formSubmit = "";
					}
					// Daten wurden verändert und Übermittelt , also zur Überprüfung anzeigen ###
					if ($this->checked->formSubmit != "") {
						if (empty ($this->checked->gruppeid)) {
							$this->checked->gruppeid = "";
						}
						// alles ok, dann ändern
						if ($this->checked->groupname != "" and $this->checked->gruppenleiter != "none" and $this->checked->kategorie != "" and $this->checked->beschreibung != "") {
							// $sql= "UPDATE ".$this->cms->papoo_gruppe." SET gruppenname='".$this->db->escape($this->checked->groupname)."',"." gruppenleiter='".$this->db->escape($this->checked->gruppenleiter)."', admin_zugriff='".$this->db->escape($this->checked->admin_zugriff)."', elterngruppe='".$this->db->escape($this->checked->kategorie)."', "."gruppen_beschreibung='".$this->db->escape($this->checked->beschreibung)."', allow_internet='".$this->db->escape($this->checked->allow_internet)."', allow_intranet='".$this->db->escape($this->checked->allow_intranet)."', intranet='".$this->db->escape($this->checked->intranet_zu)."', publish_user='".$this->db->escape($this->checked->userleiter)."' WHERE gruppeid=".$this->db->escape($this->checked->gruppeid)." ";
							$sql = "UPDATE " . $this->cms->papoo_gruppe . " SET gruppenname='" . $this->db->escape($this->checked->groupname) . "'," . " gruppenleiter='" . $this->db->escape($this->checked->gruppenleiter) . "', admin_zugriff='" . $this->db->escape($this->checked->admin_zugriff) . "', elterngruppe='" . $this->db->escape($this->checked->kategorie) . "', " . "gruppen_beschreibung='" . $this->db->escape($this->checked->beschreibung) . "', allow_internet='" . $this->db->escape($this->checked->allow_internet) . "', publish_user='" . $this->db->escape($this->checked->userleiter) . "', user_konfiguration_des_tinymce='" .
								$this->db->escape($this->checked->user_konfiguration_des_tinymce) . "'  WHERE gruppeid=" . $this->db->escape($this->checked->gruppeid) . "";
							$this->db->query($sql);
							// header("Location: ./user.php?menuid=9&messageget=50");
							$location_url = "./user.php?menuid=9&messageget=50";
							if ($_SESSION['debug_stopallredirect'])
								echo '<a href="' . $location_url . '">Weiter</a>';
							else
								header("Location: $location_url");
							exit;
						} // nicht ok, dann message raus.
						else {
							$this->content->template['text_error'] = $this->content->template['message_48a'];
						}
					} // Es werden die Daten aus der Datenbank geholt und angezeigt
					else {
						if (empty ($this->checked->gruppeid)) {
							$this->checked->gruppeid = "";
						}
						$gruppeid = $this->checked->gruppeid;
						// Gruppe anzeigen zur Auswahl
						if ($gruppeid == "") {
							// Template-> Gruppebearbeitung
							$this->content->template['altgruppe'] = '1';
							// ANzahl der Gruppen ermitteln
							$this->weiter->result_anzahl = $this->db->get_var("SELECT COUNT(*) FROM " . $this->cms->papoo_gruppe . " ");

							$sql = sprintf("SELECT COUNT(%s.userid) AS count,
                 													gruppenname,
                 													gruppen_beschreibung,
                 													allow_internet,
                 													admin_zugriff,
                 													gruppeid
                 													FROM %s LEFT JOIN %s  ON gruppeid=%s.gruppenid
                 													LEFT JOIN %s ON  %s.userid=%s.userid
                 													GROUP BY gruppeid
                 													ORDER BY gruppenname ASC
                 													%s",
								$this->cms->tbname['papoo_user'],
								$this->cms->tbname['papoo_gruppe'],
								$this->cms->tbname['papoo_lookup_ug'],
								$this->cms->tbname['papoo_lookup_ug'],
								$this->cms->tbname['papoo_user'],
								$this->cms->tbname['papoo_user'],
								$this->cms->tbname['papoo_lookup_ug'],
								$this->cms->sqllimit
							);
							$result = $this->db->get_results($sql, ARRAY_A);


							// Name der Gruppen und id zur Auswahl angeben
							$this->content->template['gruppelesen_data'] = $result;

							//ANzahl der User in der Gruppe
							$sql = sprintf("SELECT COUNT(userid), gruppenname
                 													FROM %s LEFT JOIN %s ON gruppeid=gruppenid
                 													GROUP BY gruppeid",
								$this->cms->tbname['papoo_gruppe'],
								$this->cms->tbname['papoo_lookup_ug']);
							$result = $this->db->get_results($sql);

							// Link für weitere Seiten
							$this->weiter->weiter_link = "./user.php?menuid=9";
							// weitere Seiten anzeigen
							$this->weiter->do_weiter("teaser");
						}
						// Es ist eine Gruppe ausgewählt, also die Daten anzeigen zur Bearbeitung ###
						else {
							if (empty ($this->checked->gruppeid)) {
								$this->checked->gruppeid = "";
							}
							// Daten der Gruppe raussuchen
							$result = $this->db->get_results("SELECT * FROM " . $this->cms->papoo_gruppe . "  WHERE gruppeid='" . $this->db->escape($this->checked->gruppeid) . "' ORDER BY gruppeid ");
							// Gruppe existiert
							if (count($result)) {
								foreach ($result as $row) {
									$gruppenleiter = $row->gruppenleiter;
									$gruppeid = $row->gruppeid;
									$groupname = $row->gruppenname;
									$admin_zugriff = $row->admin_zugriff;
									$kategorie = $row->elterngruppe;
									$beschreibung = $row->gruppen_beschreibung;
									$allow_internet = $row->allow_internet;
									$tiny = $row->user_konfiguration_des_tinymce;
									// $allow_intranet= $row->allow_intranet;
									// $intranet_zu= $row->intranet;
									$publish_user = $row->publish_user;
								}
								IfNotSetNull($allow_internet);
								IfNotSetNull($admin_zugriff);
								IfNotSetNull($groupname);
								IfNotSetNull($beschreibung);
								IfNotSetNull($tiny);
								// Gruppe darf Artikel für das Internet veröffentlichen
								if ($allow_internet == 1) {
									$this->content->template['checked_internet'] = 'nodecode:checked="checked"';
								}
								// Gruppe darf Artikel für das Intranet veröffentlichen
								// if ($allow_intranet == 1) $this->content->template['checked_intranet']= 'nodecode:checked="checked"';
								// hat die Gruppe Zugriff auf das Intranet
								// if ($intranet_zu == 1) $this->content->template['checked_intranet_zu']= 'nodecode:checked="checked"';
								// Hat die Gruppe Zugriff auf den Adminbereich
								if ($admin_zugriff == 1) {
									$this->content->template['admin_checked'] = 'nodecode:checked="checked"';
								}

								$this->content->template['gruppeid'] = $gruppeid;
								// Formular setzen
								$this->content->template['neugroup'] = '1';
								$this->content->template['gruppenname'] = $groupname;
								$this->content->template['update'] = 'update';
								$this->content->template['beschreibung'] = "nobr:" . $beschreibung;
								$this->content->template['user_konfiguration_des_tinymce'] = "nobr:" . $tiny;
								// Alle Gruppen ausser der eigenen
								$result = $this->db->get_results("SELECT gruppenname, gruppeid FROM " . $this->cms->papoo_gruppe . " WHERE gruppeid!='" . $this->db->escape($this->checked->gruppeid) . "' ORDER BY gruppenname ASC ");
								// anzeigen der Gruppen für die ANgabe der Untergruppen von:
								IfNotSetNull($kategorie);
								foreach ($result as $row) {
									if ($row->gruppeid == $kategorie) {
										$selected2 = "selected";
									}
									else {
										$selected2 = "";
									}
									array_push($this->content->template['gruppelesen_data'], array('gruppelesen_name' => $row->gruppenname,
										'gruppelesen_value' => $row->gruppeid,
										'selected2' => $selected2,
									));
								}
								// Gruppenleiter festlegen
								$resultuser = $this->db->get_results("SELECT username, userid FROM " . $this->cms->papoo_user . " ORDER BY username ASC ");
								IfNotSetNull($gruppenleiter);
								foreach ($resultuser as $row) {
									if ($row->userid == $gruppenleiter) {
										$selected = "selected";
									}
									else {
										$selected = "";
									}

									array_push($this->content->template['userlesen_data'], array('userlesen_name' => $row->username,
										'userlesen_value' => $row->userid,
										'selected' => $selected,
									));
								}
								// USerleiter festlegen
								// User zuweisen für die Auswahl des Gruppenleiters
								$sql = "SELECT username, " .
									$this->cms->papoo_user .
									".userid FROM " . $this->cms->papoo_user . ", " .
									$this->cms->papoo_gruppe . ", " .
									$this->cms->papoo_lookup_ug . "  WHERE " . $this->cms->papoo_lookup_ug .
									".userid=" . $this->cms->papoo_user . ".userid
									AND " . $this->cms->papoo_lookup_ug . ".gruppenid=gruppeid AND  allow_internet='1'
									ORDER BY username ASC ";
								$resultuser = $this->db->get_results($sql);
								IfNotSetNull($publish_user);
								if ((!empty($resultuser))) {
									foreach ($resultuser as $row) {
										if ($row->userid == $publish_user) {
											$selected = "selected";
										}
										else {
											$selected = "";
										}

										array_push($this->content->template['userschreiben_data'], array('userlesen_name' => $row->username,
											'userlesen_value' => $row->userid,
											'selected' => $selected,
										));
									}
								}
							}
							else {
								// Gruppe existiert nicht
								$this->content->template['text'] = $this->content->template['message_56'];
							}
						}
					}
				}
			}
			break;
		}
	}

	/**
	 * Hier werden die Userdaten bearbeitet
	 * ...sollte mal weiter aufgetrennt werden...
	 */
	function change_user()
	{
		if (!empty ($this->checked->exportlist)) {
			#echo "EXPORT";
			$this->do_export();
		}

		if (empty ($this->checked->loeschenecht)) {
			$this->checked->loeschenecht = "";
		}
		// User soll gelöscht werden, Daten wurden Überprüft ##########
		if ($this->checked->loeschenecht != "") {
			// Überprüfen ob root oder jeder
			if ($this->checked->userid != "10" and $this->checked->userid != "11") {
				$sqlloesch = "DELETE FROM " . $this->cms->papoo_user . " WHERE userid='" . $this->db->escape($this->checked->userid) . "' LIMIT 1";
				$this->db->query($sqlloesch);
				// header("Location: ./user.php?menuid=4&messageget=53");
				$location_url = "./user.php?menuid=4&messageget=53";
				if ($_SESSION['debug_stopallredirect'])
					echo '<a href="' . $location_url . '">Weiter</a>';
				else
					header("Location: $location_url");
				exit;
			}
			else {
				// root, dann kann nicht gelöscht werden
				if ($this->checked->userid == "10") {
					// header("Location: ./user.php?menuid=4&messageget=54");
					$location_url = "./user.php?menuid=4&messageget=54";
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit;
				}
				// jeder, dann kann nicht gelöscht werden
				if ($this->checked->userid == "11") {
					// header("Location: ./user.php?menuid=4&messageget=55");
					$location_url = "./user.php?menuid=4&messageget=55";
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit;
				}
			}
		} // Nicht löschen, sondern bearbeiten
		else {
			if (empty ($this->checked->loeschen)) {
				$this->checked->loeschen = "";
			}
			// User soll gelöscht werden, Daten Überprüfen
			if ($this->checked->loeschen != "") {
				// Template auf wirklich löschen setzen
				$this->content->template['loeschen'] = '1';
				// Daten zuweisen
				array_push($this->content->template['gruppelesen_data'], array('formusername' => $this->checked->neuusername, 'userid' => $this->checked->userid,));
			} // Nicht löschen, Daten heraussuchen und anzeigen
			else {
				// Daten Überprüft und Datenbank updaten
				$gesetzt = "NOK";
				if (empty ($this->checked->formSubmit)) {
					$this->checked->formSubmit = "";
				}
				// Formular wurde Übermittelt und Daten zur Übreprüfung anzeigen ###
				if ($this->checked->formSubmit != "" and $this->checked->formSubmit != "Finden") {
					// Es ist alles ausgefüllt und wird als Liste angezeigt ... ####
					if ($this->checked->neuusername != "" and $this->checked->email != "") {
						if ($this->checked->antwortmail == "ok") {
							$antwortmail = 1;
						}
						else {
							$antwortmail = 0;
						}

						if ($this->checked->dauer_einlogg == "ok") {
							$this->checked->dauer_einlogg = 1;
						}
						else {
							$this->checked->dauer_einlogg = 0;
						}

						if (empty ($this->checked->stylepost)) {
							$this->checked->stylepost = "";
						}
						// Neues Password
						if ($this->checked->formpassword != "") {
							// Passwort verschlüsseln
							if ($this->checked->userid == 10 or $this->checked->userid == 11) {
								$this->checked->active = 1;
							}

							if ($this->checked->userid == 11) {
								$this->checked->neuusername = "jeder";
							}

							if ($this->checked->userid == 10) {
								$this->checked->neuusername = "root";
							}

							$hashed_password = $this->diverse->hash_password($this->checked->formpassword);
							// Datenbank updaten
							$sql = " UPDATE " . $this->cms->papoo_user . " SET username='" . $this->db->escape($this->checked->neuusername) . "', email='" . $this->db->escape($this->checked->email) . "', password='$hashed_password', password_org='" . $this->db->escape("xxx") . "', " . " beschreibung='" . $this->db->escape($this->checked->beschreibung) . "',";
							$sql .= " user_vorname ='" . $this->db->escape($this->checked->neuvorname) . "', ";
							$sql .= " user_nachname='" . $this->db->escape($this->checked->neunachname) . "' , ";
							$sql .= " user_strasse='" . $this->db->escape($this->checked->neustrnr) . "', ";
							$sql .= " user_ort='" . $this->db->escape($this->checked->neuort) . "' ,";
							$sql .= " user_plz='" . $this->db->escape($this->checked->neuplz) . "', ";
							$sql .= " user_style_id = '" . $this->db->escape($this->checked->stylepost) . "' ,";
							$sql .= " user_lang_front = '" . $this->db->escape($this->checked->lang_front) . "' ,";
							$sql .= " user_lang_back = '" . $this->db->escape($this->checked->lang_back) . "' ,";
							$sql .= " dauer_einlogg = '" . $this->db->escape($this->checked->dauer_einlogg) . "',  ";
							$sql .= " board = '" . $this->db->escape($this->checked->forum_board) . "',  ";
							$sql .= " active = '" . $this->db->escape($this->checked->active) . "',  ";
							$sql .= " user_newsletter = '" . $this->db->escape($this->checked->user_newsletter) . "',  ";
							$sql .= " antwortmail='$antwortmail', ";
							$sql .= " dauer_einlogg='" . $this->db->escape($this->checked->dauer_einlogg) . "' ";
							$sql .= "";
							$sql .= " WHERE userid='" . $this->db->escape($this->checked->userid) . "'";
						} // Kein neues Passwort
						else {
							if ($this->checked->userid == 10 or $this->checked->userid == 11) {
								$this->checked->active = 1;
							}

							if ($this->checked->userid == 11) {
								$this->checked->neuusername = "jeder";
							}

							if ($this->checked->userid == 10) {
								$this->checked->neuusername = "root";
							}
							$sql = " UPDATE " . $this->cms->papoo_user . " SET username='" . $this->db->escape($this->checked->neuusername) . "', " . " email='" . $this->db->escape($this->checked->email) . "', " . " beschreibung='" . $this->db->escape($this->checked->beschreibung) . "',";
							$sql .= " user_vorname ='" . $this->db->escape($this->checked->neuvorname) . "', ";
							$sql .= " user_nachname='" . $this->db->escape($this->checked->neunachname) . "' , ";
							$sql .= " user_strasse='" . $this->db->escape($this->checked->neustrnr) . "', ";
							$sql .= " user_ort='" . $this->db->escape($this->checked->neuort) . "' ,";
							$sql .= " user_plz='" . $this->db->escape($this->checked->neuplz) . "', ";
							$sql .= " user_style_id = '" . $this->db->escape($this->checked->stylepost) . "' ,";
							$sql .= " user_lang_front = '" . $this->db->escape($this->checked->lang_front) . "' ,";
							$sql .= " user_lang_back = '" . $this->db->escape($this->checked->lang_back) . "' ,";
							$sql .= " dauer_einlogg = '" . $this->db->escape($this->checked->dauer_einlogg) . "',  ";
							$sql .= " active = '" . $this->db->escape($this->checked->active) . "',  ";
							$sql .= " board = '" . $this->db->escape($this->checked->forum_board) . "',  ";
							$sql .= " user_newsletter = '" . $this->db->escape($this->checked->user_newsletter) . "',  ";
							$sql .= " antwortmail='$antwortmail', ";
							$sql .= " dauer_einlogg='" . $this->db->escape($this->checked->dauer_einlogg) . "'";
							$sql .= "";
							$sql .= " WHERE userid='" . $this->db->escape($this->checked->userid) . "'";
						}
						$this->db->query($sql);
						// wenn die gruppenid nicht leer ist
						if (!empty ($this->checked->gruppelesen)) {
							// alte Einträge löschen (einfacher als updaten)
							$delsql = "DELETE FROM " . $this->cms->papoo_lookup_ug . " WHERE userid='" . $this->db->escape($this->checked->userid) . "' ";
							$this->db->query($delsql);
							// neue Einträge erstellen

							foreach ($this->checked->gruppelesen as $insert) {
								$insert = $this->db->escape($insert);
								$sqlin = "INSERT INTO " . $this->cms->papoo_lookup_ug . " SET userid='" . $this->db->escape($this->checked->userid) . "', gruppenid='$insert' ";
								$this->db->query($sqlin);
							}

						} // ansonsten wird die gruppenid auf jeder gesetzt
						else {
							// alte Einträge löschen (einfacher als updaten)
							$delsql = "DELETE FROM " . $this->cms->papoo_lookup_ug . " WHERE userid='" . $this->db->escape($this->checked->userid) . "' ";
							$this->db->query($delsql);
							// neue Einträge erstellen
							$sqlin = "INSERT INTO " . $this->cms->papoo_lookup_ug . " SET userid='" . $this->db->escape($this->checked->userid) . "', gruppenid='10' ";
							$this->db->query($sqlin);
						}
						if ($this->checked->userid == 10) {
							$delsql = "DELETE FROM " . $this->cms->papoo_lookup_ug . " WHERE userid='" . $this->db->escape($this->checked->userid) . "' AND gruppenid='1' ";
							$this->db->query($delsql);
							$sqlin = "INSERT INTO " . $this->cms->papoo_lookup_ug . " SET userid='10', gruppenid='1' ";
							$this->db->query($sqlin);
							$delsql = "DELETE FROM " . $this->cms->papoo_lookup_ug . " WHERE userid='" . $this->db->escape($this->checked->userid) . "' AND gruppenid='10' ";
							$this->db->query($delsql);
							$sqlin = "INSERT INTO " . $this->cms->papoo_lookup_ug . " SET userid='10', gruppenid='10' ";
							$this->db->query($sqlin);
						}
						// header ("Location: ./user.php?menuid=8&messageget=52");
						$location_url = "./user.php?menuid=8&messageget=52";
						if ($_SESSION['debug_stopallredirect']) {
							echo '<a href="' . $location_url . '">Weiter</a>';
						}
						else {
							header("Location: $location_url");
						}
						exit;
					} // Es ist nicht alles ausgefüllt, Formular erneut anzeigen
					else {
						$this->show_form();
						// Template auf Userbearbeitung setzen
						$this->content->template['userbearb'] = '1';
						// Nicht alles ausgefüllt message
						$this->content->template['text_error'] = $this->content->template['message_48'];
					}
				} // Daten für das Formular ermitteln ####
				else {
					if ($gesetzt == "NOK") {
						if (empty ($this->checked->userid)) {
							$this->checked->userid = "";
						}
						$userid = $this->db->escape($this->checked->userid);
						// Es ist ein User ausgewählt, also anzeigen ###
						if ($userid != "") {
							// Template auf Userbearbeitung setzen
							$this->content->template['userbearb'] = '1';
							// Userdaten raussuchen
							$result = $this->db->get_results("SELECT * FROM " . $this->cms->papoo_user . " WHERE userid='$userid' ORDER BY userid ");
							// Daten zuweisen
							$user_data = array();
							$checked = "";

							foreach ($result as $row) {
								if ($row->antwortmail == "1") {
									$checked = 'nodecode:checked="checked"';
								}

								$checkedboard1 = $checkedboard2 = $checkedboard3 = $checkedlogg = "";
								if ($row->board == 0) {
									$checkedboard1 = 'nodecode:checked="checked"';
								}

								if ($row->board == 1) {
									$checkedboard2 = 'nodecode:checked="checked"';
								}

								if ($row->board == 2) {
									$checkedboard3 = 'nodecode:checked="checked"';
								}

								if ($row->dauer_einlogg == 1) {
									$checkedlogg = 'nodecode:checked="checked"';
								}

								array_push($user_data, array('vorname' => $row->user_vorname,
									'nachname' => $row->user_nachname,
									'strnrname' => $row->user_strasse,
									'plzname' => $row->user_plz,
									'ortname' => $row->user_ort,
									'checked' => "$checked",
									'loeschen' => "",
									'username' => $row->username,
									'active' => $row->active,
									'mailname' => $row->email,
									'user_newsletter' => $row->user_newsletter,
									'signatur' => $row->signatur,
									'logalt' => "",
									'checked_board1' => $checkedboard1,
									'checked_board2' => $checkedboard2,
									'checked_board3' => $checkedboard3,
									'style_selected' => $row->user_style_id,
									'lang_front' => $row->user_lang_front,
									'lang_back' => $row->user_lang_back,
									'beschreibung' => "nodecode:" . $row->beschreibung,
									'checked_logg' => $checkedlogg,
									'userid' => $row->userid,
									'fehltvorname' => "",
									'fehltnachname' => "",
									'fehltstrnr' => "",
									'fehltplz' => "",
									'fehltort' => "",
									'fehltemail' => "",
									'fehltusername' => "",
									'fehltpass1' => "",
									'fehltagb' => "",
									'fehltpass2' => "",
								));
								// $this->content->template['beschreibung']= "nobr:".$row->beschreibung;
							}
							// Daten in abstrakte Datnklasse einlesen
							$this->content->template['table_data'] = $user_data;
							// Gruppenzugehörigkeit einstellen
							// userid<->gruppenid aus lookup Tabelle holen
							$select_gr = "SELECT gruppenid FROM " . $this->cms->papoo_lookup_ug . " WHERE userid='$userid'";
							$result_gr = $this->db->get_results($select_gr);
							// Gruppendaten holen
							$result = $this->db->get_results("SELECT gruppenname, gruppeid FROM " . $this->cms->papoo_gruppe . " ORDER BY gruppenname ");
							// alle Gruppen durchloopen und aktive anhaken
							foreach ($result as $row) {
								$checked = "";
								if (!empty ($result_gr)) {
									foreach ($result_gr as $x) {
										if ($row->gruppeid == $x->gruppenid) {
											$checked = 'nodecode:checked="checked"';
										}
									}
								}
								array_push($this->content->template['gruppelesen_data'], array('gruppelesen_name' => $row->gruppenname, 'gruppelesen_value' => $row->gruppeid, 'checked' => $checked,));
							}
						} // Alle User anzeigen ###
						else {
							if (empty ($this->checked->search)) {
								$this->checked->search = "";
							}
							// Gruppenliste erstellen für Auswahl
							$this->do_gruppenliste();
							// Suchbegriff bereinigen
							$search = $this->db->escape($this->clean($this->checked->search));
							// Template-> Userliste anzeigen
							$this->content->template['userlist'] = '1';
							// Seite
							$this->content->template['pagex'] = $this->checked->page;
							// es wird gesucht
							if (!empty ($search)) {
								$this->weiter->result_anzahl = $this->db->get_var("SELECT COUNT(*) FROM " . $this->cms->papoo_user . "
                                                WHERE username LIKE '%" . $search . "%'
                                                OR email LIKE '%" . $search . "%'
                                                GROUP BY userid  ORDER BY userid ");
							} // ansonsten ANzahl für alle
							else {
								if (empty ($this->checked->groupselect) or $this->checked->groupselect == "none") {
									$sql = "SELECT COUNT(userid) FROM " . $this->cms->papoo_user . "  ORDER BY userid ";
								} else {
									$sql = " SELECT COUNT(" . $this->cms->papoo_user . ".userid)";
									$sql .= " FROM " . $this->cms->papoo_user . "," . $this->cms->papoo_lookup_ug . "";
									$sql .= "  ";
									$sql .= " WHERE ";
									$sql .= " " . $this->cms->papoo_lookup_ug . ".gruppenid=";
									$sql .= " '" . $this->db->escape($this->checked->groupselect) . "'";
									$sql .= " AND";
									$sql .= " " . $this->cms->papoo_lookup_ug . ".userid=";
									$sql .= " " . $this->cms->papoo_user . ".userid";
									#$sql .= " GROUP BY " . $this->cms->papoo_user . ".userid ";
									$sql .= "";
									$sql .= "";
									$sql .= "";
								}

								$this->weiter->result_anzahl = $this->db->get_var($sql);
							}
							// Kein User anzeigen (sollte eigentlich nicht vorkommen, außer bei der Suche)
							if ($this->weiter->result_anzahl < 1) {
								$this->content->template['nouser'] = '1';
							} // User gefunden, dann los
							else {
								// Anzahl der User anzeigen
								$this->content->template['anzahl_user'] = $this->weiter->result_anzahl;
								// Link für weitere Seiten Übergeben
								$this->weiter->weiter_link = "./user.php?menuid=8&groupselect=" . $this->checked->groupselect;
								// weitere Seiten anzeigen
								$this->weiter->do_weiter("search");
								// in welche Richtung werden die Usernamen dargestellt
								$this->check_order();
								// Username etc aus der Datenbank auslesen
								if (!empty ($search)) {
									$this->content->template['search'] = $search;
									$result = $this->db->get_results("SELECT DISTINCT(username), userid, gruppenid, zeitstempel, beitraege FROM " . $this->cms->papoo_user . " WHERE username LIKE '%" . $search . "%' OR email LIKE '%" . $search . "%' ORDER BY $this->order $this->richtung " . $this->cms->sqllimit . "");
								} // Keine Suche, also alle anzeigen
								else {
									if (empty ($this->checked->groupselect) or $this->checked->groupselect == "none") {
										$sql = "SELECT username, userid, gruppenid, zeitstempel, beitraege, user_last_login FROM " . $this->cms->papoo_user . "  GROUP BY userid ORDER BY $this->order $this->richtung " . $this->cms->sqllimit . "";
									} else {
										$sql = " SELECT username, " . $this->cms->papoo_user . ".userid, " . $this->cms->papoo_lookup_ug . ".gruppenid, zeitstempel, beitraege, user_last_login ";
										$sql .= " FROM " . $this->cms->papoo_user . "," . $this->cms->papoo_lookup_ug . "";
										$sql .= "  ";
										$sql .= " WHERE ";
										$sql .= " " . $this->cms->papoo_lookup_ug . ".gruppenid=";
										$sql .= " " . $this->db->escape($this->checked->groupselect) . "";
										$sql .= " AND";
										$sql .= " " . $this->cms->papoo_lookup_ug . ".userid=";
										$sql .= " " . $this->cms->papoo_user . ".userid";
										$sql .= "";
										$sql .= "";
										$sql .= "";
										$sql .= " GROUP BY userid ORDER BY " . $this->cms->papoo_user . ".$this->order $this->richtung " . $this->cms->sqllimit . "";
									}
									$result = $this->db->get_results($sql);
									// FIXME!!!
									$this->weiter->result_anzahl = count($result);
								}
								// Für jeden User durchgehen
								foreach ($result as $row) {
									IfNotSetNull($row->user_last_login);

									// HTML start Liste für Gruppe zuweisen
									$gruppe = "<ul>";
									// Gruppenzugehörigkeit abfragen
									$selectgruppe = "SELECT " . $this->cms->papoo_gruppe . ".gruppenname FROM " . $this->cms->papoo_lookup_ug . ", " . $this->cms->papoo_gruppe . " WHERE " . $this->cms->papoo_lookup_ug . ".userid='" . $row->userid . "' AND " . $this->cms->papoo_gruppe . ".gruppeid=" . $this->cms->papoo_lookup_ug . ".gruppenid ";
									$arraygruppe = $this->db->get_results($selectgruppe);
									// Gruppennamen zuweisen fürs Template, alle Gruppen durchloopen
									if (!empty ($arraygruppe)) {
										foreach ($arraygruppe as $gruppesuch) {
											if (!empty ($gruppesuch)) {
												$gruppe .= "<li>" . $gruppesuch->gruppenname . "</li>";
											}
										}
									}
									$gruppe .= "</ul>";

									if (!is_numeric($row->zeitstempel)) {
										$ex_zst = explode(" ", $row->zeitstempel);
										IfNotSetNull($ex_zst['0']);
										IfNotSetNull($ex_zst['1']);
										if (strstr($ex_zst['0'], ".")) {
											$ex_zst1 = explode(".", $ex_zst['0']);
										}
										else {
											$ex_zst1 = explode("-", $ex_zst['0']);
										}

										$ex_zst2 = explode(":", $ex_zst['1']);
										$time = @mktime($ex_zst2['0'], $ex_zst2['1'], $ex_zst2['2'], $ex_zst1['1'], $ex_zst1['2'], $ex_zst1['0']);
									}
									else {
										$time = $row->zeitstempel;
									}
									// Daten fürs Template zuweisen
									array_push($this->content->template['gruppelesen_data'], array(
											'formusername' => $row->username,
											'gruppe_zu' => $gruppe,
											'eintritt' => $time,
											'beitraege' => $row->beitraege,
											'userid' => $row->userid,
											'user_last_login' => $row->user_last_login)
									);
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Liste der User als CSV exportieren in einem .txt File
	 */
	function do_export()
	{
		$mode = "";
		$this->diverse->groupselect = $this->checked->groupselect;
		$this->diverse->do_export($mode);
	}

	/**
	 * Gruppenliste erstellen
	 */
	function do_gruppenliste()
	{
		IfNotSetNull($this->checked->groupselect);

		// Gruppe rausuchen
		$selectgruppe = "SELECT * FROM " . $this->cms->papoo_gruppe . " ";
		$arraygruppe = $this->db->get_results($selectgruppe);
		// Daten zuweisen
		$this->content->template['gruppenliste'] = array();
		if (!empty ($arraygruppe)) {
			foreach ($arraygruppe as $gruppe) {
				$selected = "";
				if ($this->checked->groupselect == $gruppe->gruppeid) {
					$selected = "selected";
					$this->content->template['groupselect_form'] = $gruppe->gruppeid;
				}
				array_push($this->content->template['gruppenliste'], array('gruppenname' => $gruppe->gruppenname, 'gruppenid' => $gruppe->gruppeid, 'selected' => $selected,));
			}
		}
	}

	/**
	 * Hiermit wird entschieden in welche Richtung (bestimmt durch den Link)
	 * die Userdaten angezeigt werden
	 *
	 * @return void
	 */
	function check_order()
	{
		if (empty ($this->checked->order)) {
			$this->checked->order = "";
		}
		switch ($this->checked->order) {
			// ordnen nach Username
		case "user" :
			$this->order = "username";
			break;
			// ordnen nach Beitritt
		case "beitritt" :
			$this->order = "zeitstempel";
			break;
			// ordnen nach userid
		default :
			$this->order = "userid";
		}
		if (empty ($this->checked->richtung)) {
			$this->checked->richtung = "";
		}
		// Richtung der ORdnung
		switch ($this->checked->richtung) {
		case "rauf" :
			$this->richtung = "ASC";
			$this->content->template['richtung'] = 'runter';
			$this->content->template['richtungweiter'] = 'rauf';
			break;

		case "runter" :
			$this->richtung = "DESC";
			$this->content->template['richtung'] = 'rauf';
			$this->content->template['richtungweiter'] = 'runter';
			break;

		default :
			$this->richtung = "DESC";
			$this->content->template['richtung'] = 'runter';
		}
	}

	/**
	 * Diese Methode erzeugt das Formular für die EIngabe eines neuen Users
	 *
	 * @return void
	 */
	function show_form()
	{
		if (empty ($this->checked->antwortmail)) {
			$this->checked->antwortmail = "";
		}

		if ($this->checked->antwortmail == "ok") {
			$checked = 'nodecode:checked="checked"';
		}
		else {
			$checked = "";
		}

		if (empty ($this->checked->dauer_einlogg)) {
			$this->checked->dauer_einlogg = "";
		}

		if ($this->checked->dauer_einlogg == "ok") {
			$checked_logg = 'nodecode:checked="checked"';
		}
		else {
			$checked_logg = "";
		}

		if ($this->checked->antwortmail == "1") {
			$checked = 'nodecode:checked="checked"';
		}

		$checkedboard1 = $checkedboard2 = $checkedboard3 = $checkedlogg = "";
		if (empty ($this->checked->forum_board)) {
			$this->checked->forum_board = "";
		}

		if ($this->checked->forum_board == 0) {
			$checkedboard1 = 'nodecode:checked="checked"';
		}

		if ($this->checked->forum_board == 1) {
			$checkedboard2 = 'nodecode:checked="checked"';
		}

		if ($this->checked->forum_board == 2) {
			$checkedboard3 = 'nodecode:checked="checked"';
		}

		if ($this->checked->dauer_einlogg == "ok") {
			$checkedlogg = 'nodecode:checked="checked"';
		}

		if (empty ($this->checked->neuvorname)) {
			$this->checked->neuvorname = "";
		}

		if (empty ($this->checked->neunachname)) {
			$this->checked->neunachname = "";
		}

		if (empty ($this->checked->neuplz)) {
			$this->checked->neuplz = "";
		}

		if (empty ($this->checked->neustrnr)) {
			$this->checked->neustrnr = "";
		}

		if (empty ($this->checked->neuort)) {
			$this->checked->neuort = "";
		}

		if (empty ($this->checked->neuusername)) {
			$this->checked->neuusername = "";
		}

		if (empty ($this->checked->email)) {
			$this->checked->email = "";
		}

		if (empty ($this->checked->stylepost)) {
			$this->checked->stylepost = "";
		}

		if (empty ($this->checked->userid)) {
			$this->checked->userid = "";
		}

		IfNotSetNull($this->checked->lang_back);
		IfNotSetNull($this->checked->lang_front);
		IfNotSetNull($this->checked->beschreibung);

		$user_data = array();
		array_push($user_data, array(
			'vorname' => $this->checked->neuvorname,
			'nachname' => $this->checked->neunachname,
			'strnrname' => $this->checked->neustrnr,
			'plzname' => $this->checked->neuplz,
			'ortname' => $this->checked->neuort,
			'checked' => $checked,
			'loeschen' => "",
			'username' => $this->checked->neuusername,
			'mailname' => $this->checked->email,
			'checked_logg' => $checked_logg,
			'logalt' => "",
			'checked_board1' => $checkedboard1,
			'checked_board2' => $checkedboard2,
			'checked_board3' => $checkedboard3,
			'style_selected' => $this->checked->stylepost,
			'lang_front' => $this->checked->lang_front,
			'lang_back' => $this->checked->lang_back,
			'beschreibung' => "nodecode:" . $this->checked->beschreibung,
			'userid' => $this->checked->userid,
			'signatur' => "",
			'fehltvorname' => "",
			'fehltnachname' => "",
			'fehltstrnr' => "",
			'fehltplz' => "",
			'fehltort' => "",
			'fehltemail' => "",
			'fehltusername' => "",
			'fehltpass1' => "",
			'fehltagb' => "",
			'fehltpass2' => "",
		));
		$this->content->template['neuuser'] = '1';
		// Daten in abstrakte Datnklasse einlesen
		$this->content->template['table_data'] = $user_data;
		// wenn das gruppenarray leer ist als array initialisieren
		if (empty ($this->checked->gruppelesen)) {
			$this->checked->gruppelesen = array();
		}
		// Gruppen anzeigen
		$result = $this->db->get_results("SELECT gruppenname, gruppeid FROM " . $this->cms->papoo_gruppe . " ORDER BY gruppenname ASC ");
		$this->content->template['gruppelesen_data'] = array();
		foreach ($result as $row) {
			$checked = "";
			if (in_array($row->gruppeid, $this->checked->gruppelesen)) {
				$checked = 'nodecode:checked="checked"';
			}
			array_push($this->content->template['gruppelesen_data'], array('gruppelesen_name' => $row->gruppenname, 'gruppelesen_value' => $row->gruppeid, 'checked' => $checked,));
		}
	}

	/**
	 * Login als md5 verschlüsseln
	 * @param $login
	 * @return string
	 * @deprecated md5 is unsicher
	 */
	function createCode($login)
	{
		srand((double)microtime() * 1000000);
		return $this->confirm_code = md5($login . time() . rand(1, 10000000));
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
		for ($i = 0; $i < strlen($remove); $i++) {
			$search = str_replace(substr($remove, $i, 1), "", $search);
		}
		return $search;
	}
}

$intern_user = new intern_user();