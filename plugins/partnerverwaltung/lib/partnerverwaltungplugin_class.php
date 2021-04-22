<?php

/**
 * #####################################
 * # CMS Papoo                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.3                  #
 * #####################################
 */

/**
 * Class partnerverwaltung
 */
class partnerverwaltung
{

	/** @var string */
	var $news = "";

	/**
	 * partnerverwaltung constructor.
	 */
	function __construct()
	{
		// Klassen globalisieren

		global $cms, $db, $message, $user, $menu, $content, $searcher, $checked, $mail_it, $replace, $db_praefix,
			   $intern_stamm, $intern_artikel, $diverse, $blacklist, $intern_image, $weiter;

		// und einbinden in die Klasse

		// Hier die Klassen als Referenzen
		$this->intern_image = &$intern_image;
		$this->blacklist = &$blacklist;
		$this->weiter = &$weiter;
		$this->cms = &$cms;
		$this->db = &$db;
		$this->content = &$content;
		$this->message = &$message;
		$this->user = &$user;
		$this->menu = &$menu;
		$this->searcher = &$searcher;
		$this->checked = &$checked;
		$this->mail_it = &$mail_it;
		$this->intern_stamm = &$intern_stamm;
		$this->replace = &$replace;
		$this->diverse = &$diverse;
		$this->intern_artikel = &$intern_artikel;
		$this->make_bannerverwaltung();
		$this->content->template['plugin_message'] = "";
		$this->post_papoo();
	}

	/**
	 * Interne Verwaltung
	 */
	function make_bannerverwaltung()
	{
		global $template;
		$this->klasse = "make_easyedit()";
		if (defined("admin")) {
			$this->user->check_intern();
			$this->content->template['tinymce_lang_short'] = "de";
			$templatedat = basename($template);
			switch ($templatedat) {

				//Ein Banner wird eingebunden
			case "partnerverwaltungplugin_create.html":
				$this->new_partner();
				break;
				//Ein Banner wird bearbeitet
			case "partnerverwaltungplugin_edit.html":
				$this->edit_partner();
				break;
				// Hauptübersicht
			case "partnerverwaltungplugin.html":
				$this->edit_partner_pref();
				break;
				// Backup wird erstellt
			case "partnerverwaltungplugin_dump.html":
				$this->make_dump();
				break;

			default:
				break;
			}
		}
	}

	/**
	 * partnerverwaltung::edit_partner_pref()
	 * Einstellungen �ndern
	 * @return void
	 */
	function edit_partner_pref()
	{
		if (!empty($this->checked->submitentry)) {
			$sql=sprintf("UPDATE %s SET partnerverwaltung_text_top='%s'",
				$this->cms->tbname['papoo_partnerverwaltung_pref'],
				$this->db->escape($this->checked->partnerverwaltung_text_top)
			);
			$this->db->query($sql);
		}
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_partnerverwaltung_pref']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$this->content->template['partner_pref_daten']=$result;
	}

	/**
	 * Was im Frontend passiert
	 */
	function post_papoo()
	{
		if (!defined("admin")) {
			global $template;
			if (stristr($template,"partnerverwaltung")) {
				if (!empty($this->checked->partner_id)) {
					$this->content->template['partner'] = "w";
					$sql = sprintf("SELECT * FROM %s,%s WHERE userid=partner_user_id AND partner_id='%s'",
						$this->cms->tbname['papoo_partnerverwaltung_daten'],
						$this->cms->tbname['papoo_user'],
						$this->db->escape($this->checked->partner_id)
					);
					$result = $this->db->get_results($sql, ARRAY_A);

					$this->content->template['user'] = $result;
					//Alle Links aus der Datenbank holen
					$sql = sprintf("SELECT paket_logo, linkliste_link_lang,linkliste_link,linkliste_Wort,linkliste_lang_header, %s.linkliste_descrip, linkliste_id
							FROM %s, %s WHERE linkliste_id=linkliste_lang_id AND linkliste_lang_lang='%s' AND linkliste_real='%s' ORDER BY linkliste_order_id ASC ",
						$this->cms->tbname['papoo_linkliste_daten_lang'], $this->cms->tbname['papoo_linkliste_daten'],
						$this->cms->tbname['papoo_linkliste_daten_lang'], 1, $this->db->escape($this->
						checked->partner_id)
					);
					$result = $this->db->get_results($sql, ARRAY_A);
					if (empty($result)) {
						$result = array();
					}
					$result = ($result);
					$dupar = array();
					if (!empty($result)) {
						$i = 0;
						foreach ($result as $dup) {
							if ($this->diverse->deep_in_array($dup['linkliste_link_lang'], $dupar)) {
								$result[$i] = "";
							}
							;
							$dupar[] = $dup['linkliste_link_lang'];
							$i++;
						}
						$i = 0;
						foreach ($result as $xres) {
							$result[$i]['linkliste_descrip'] = (html_entity_decode($result[$i]['linkliste_descrip']));
							$i++;
						}
					}
					$this->content->template['result_link'] = $result;
				}
				else {
					//Top Text reaushiolen
					$sql=sprintf("SELECT * FROM %s",
						$this->cms->tbname['papoo_partnerverwaltung_pref']
					);
					$result=$this->db->get_results($sql,ARRAY_A);

					$this->content->template['partnerverwaltung_text_top']="nodecode:".$result['0']['partnerverwaltung_text_top'];

					$this->weiter->make_limit(40);
					$sql = sprintf("SELECT COUNT(partner_id) FROM %s ",
						$this->cms->tbname['papoo_partnerverwaltung_daten']
					);
					$result = $this->db->get_var($sql);
					$this->weiter->result_anzahl = $result;
					// wenn weitere Ergebnisse angezeigt werden k�nnen
					$this->cms->mod_rewrite = 1;
					$this->weiter->weiter_link = PAPOO_WEB_PFAD."/plugin.php?menuid=" . $this->checked->menuid .
						"&template=partnerverwaltung/templates/partnerverwaltung_front.html";

					$this->content->template['url'] = PAPOO_WEB_PFAD."/plugin.php?menuid=" . $this->checked->
						menuid . "&template=partnerverwaltung/templates/partnerverwaltung_front.html&partner_id=";

					// wenn es sie gibt, weitere Seiten anzeigen
					$what = "teaser";
					$this->weiter->do_weiter($what);
					//Partner raussuchen
					$sql = sprintf("SELECT * FROM %s,%s WHERE userid=partner_user_id ORDER BY user_plz %s",
						$this->cms->tbname['papoo_partnerverwaltung_daten'],
						$this->cms->tbname['papoo_user'],
						$this->weiter->sqllimit
					);
					$result = $this->db->get_results($sql, ARRAY_A);

					if (is_array($result)) {
						$i=0;
						foreach ($result as $p) {
							$sql = sprintf("SELECT COUNT(linkliste_id) FROM %s WHERE linkliste_real='%d'",
								$this->cms->tbname['papoo_linkliste_daten'],
								$p['partner_id']
							);
							$count = $this->db->get_var($sql);
							$result[$i]['count_ref']=$count;
							$i++;
						}
					}
					$this->content->template['list_dat'] = $result;
					$this->content->template['ubersicht'] = "w";
				}
			}
		}
	}

	/**
	 * Einen User l�schen
	 *
	 * @param $userid
	 */
	function del_user($userid)
	{
		//Userdaten papoo_user
		$sql = sprintf("DELETE FROM %s WHERE userid='%s' LIMIT 1",
			$this->cms->tbname['papoo_user'],
			$this->db->escape($userid)
		);
		$this->db->query($sql);

		//papoo_visilex_user
		$sql = sprintf("DELETE FROM %s WHERE partner_user_id='%s'",
			$this->cms->tbname['papoo_partnerverwaltung_daten'],
			$this->db->escape($userid)
		);
		$this->db->query($sql);
	}
	/**
	 * Seite neu ladden
	 */
	function reload()
	{
		$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
			"&template=" . $this->checked->template . "&fertig=drin";
		if ($_SESSION['debug_stopallredirect']) echo '<a href="' . $location_url .
			'">Weiter</a>';
		else  header("Location: $location_url");
		exit;
	}

	/**
	 * Bilder Liste erstellen
	 */
	function get_image_list()
	{
		$this->intern_artikel->get_images();
	}

	function edit_partner()
	{
		$this->get_image_list();
		if (!empty($this->checked->visilex_user_id) and is_numeric($this->checked->
			visilex_user_id))
		{
			//Wenn l�schen
			if (!empty($this->checked->loeschen)) {
				$this->del_user($this->checked->visilex_user_id);
				$this->reload();
			}
			if ($this->checked->formSubmit) {
				//Bilddaten hochladen
				if (!empty($_FILES['strFile'])) {
					if (empty($this->checked->paket_logo2)) {
						$this->intern_image->upload_picture();
						$this->checked->image_name = $this->content->template['image_name'];
						$this->checked->image_breite = $this->content->template['image_breite'];
						$this->checked->image_hoehe = $this->content->template['image_hoehe'];
						$this->checked->gruppe = $this->content->template['image_gruppe'];
						$this->checked->texte[1]['lang_id'] = 1;
						$this->checked->texte[1]['alt'] = "Logo" . $this->checked->user_firma;
						$this->checked->texte[1]['title'] = '';
						$this->checked->texte[1]['long_desc'] = '';
						$this->checked->image_dir = "0";

						if (!empty($this->checked->image_name)) {
							$this->intern_image->upload_save('no');
						}

					}
				}
				if (!empty($this->checked->paket_logo2)) {
					$this->checked->image_name = $this->checked->paket_logo2;
				}

				if (empty($this->checked->image_name)) {
					$sql = sprintf("SELECT partner_logo FROM %s WHERE partner_user_id='%s'",
						$this->cms->tbname['papoo_partnerverwaltung_daten'],
						$this->db->escape($this->checked->visilex_user_id)
					);
					$this->checked->image_name = $this->db->get_var($sql);
				}
			}
			//Wenn ausgew�hlt
			$this->content->template['edit'] = '1';
			//Wenn eintragen
			if (!empty($this->checked->formSubmit)) {
				//checken
				$this->checkdata();
				//Wenn ok, dann eintragen
				if (empty($this->fehler_eingabe)) {
					$this->insert_update('update');
					$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
						"&template=" . $this->checked->template . "&fertig=drin";
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit;
				}
				else {
					//Fehlermeldung ausgeben
					$this->content->template['fehler'] =
						"<h2>Überprüfen Sie die markierten Felder!</h2>";
					$this->doitagain();
				}
			}
		}

		if (!empty($this->checked->partnerid)) {
			$sql = sprintf("SELECT * FROM %s,%s WHERE userid=partner_user_id AND partner_id='%s'",
				$this->cms->tbname['papoo_partnerverwaltung_daten'],
				$this->cms->tbname['papoo_user'],
				$this->db->escape($this->checked->partnerid)
			);
			$result = $this->db->get_results($sql, ARRAY_A);

			$this->content->template['user'] = $result;
			$this->content->template['edit'] = "w";
		}
		else {
			$sql = sprintf("SELECT * FROM %s,%s WHERE userid=partner_user_id",
				$this->cms->tbname['papoo_partnerverwaltung_daten'],
				$this->cms->tbname['papoo_user']
			);
			$result = $this->db->get_results($sql, ARRAY_A);

			$this->content->template['list_dat'] = $result;
			$this->content->template['list'] = "w";
			$this->content->template['link_partner'] = $_SERVER['PHP_SELF'] . "?menuid=" . $this->
				checked->menuid . "&template=" . $this->checked->template . "&partnerid=";
		}
	}

	/**
	 * Daten eintragen oder updaten
	 *
	 * @param string $modus
	 */

	function insert_update($modus = "")
	{
		$confirm_code = 0;

		IfNotSetNull($this->checked->formpassword1);

		if ($modus == 'insert') {
			$insup = "INSERT INTO ";
			$where = "";
			$active = 1;
			$this->confirm_code = $this->user->createCode($this->checked->neuusername);
			$this->checked->formpassword = $this->checked->formpassword1;
			$password = $this->db->escape(md5($this->checked->formpassword));
			$time = time();
		}
		if ($modus == 'insert_neu') {
			$insup = "INSERT INTO ";
			$where = "";
			$active = 0;
			$this->confirm_code = $this->user->createCode($this->checked->neuusername);
			$this->checked->formpassword = $this->checked->formpassword1;
			$password = $this->db->escape(md5($this->checked->formpassword));
			$time = time();
		}

		if ($modus == 'update') {
			//Domainliste updaten
			#$this->update_domain_user_list();

			$insup = "UPDATE ";
			if (is_numeric($this->checked->visilex_user_id)) {

				$userid = $this->db->escape($this->checked->visilex_user_id);
				$where = " WHERE userid='" . $userid . "' ";
				$where2 = " WHERE partner_user_id=" . $userid;
				$active = 1;
				if (empty($this->checked->formpassword)) {
					//altes Passwort
					$sql = sprintf("SELECT password FROM %s WHERE userid='%s' LIMIT 1",
						$this->cms->tbname['papoo_user'],
						$userid
					);
					$password = $this->db->get_var($sql);
				}
				else {
					$password = $this->db->escape(md5($this->checked->formpassword));
				}
			}
			else {
				$where = " WHERE userid='xx'";
				$where2 = " WHERE partner_user_id='xx'";
			}
		}

		IfNotSetNull($insup);
		IfNotSetNull($password);
		IfNotSetNull($active);
		IfNotSetNull($where);
		IfNotSetNull($this->checked->neuusername);
		IfNotSetNull($this->checked->user_gender);
		IfNotSetNull($this->checked->email);
		IfNotSetNull($this->checked->antwortmail);
		IfNotSetNull($this->checked->user_firma);
		IfNotSetNull($this->checked->user_vorname);
		IfNotSetNull($this->checked->user_nachname);
		IfNotSetNull($this->checked->user_strasse);
		IfNotSetNull($this->checked->user_ort);
		IfNotSetNull($this->checked->user_plz);
		IfNotSetNull($this->checked->user_telefon);
		IfNotSetNull($this->checked->user_fax);
		IfNotSetNull($this->checked->user_telefon);
		IfNotSetNull($this->checked->user_land);
		IfNotSetNull($this->checked->user_kontonr);
		IfNotSetNull($this->checked->user_blz);
		IfNotSetNull($this->checked->user_bankname);
		IfNotSetNull($this->checked->user_confirm_abbuchung);
		IfNotSetNull($this->checked->user_agb_ok);
		IfNotSetNull($this->checked->user_newsletter);
		IfNotSetNull($this->checked->image_name);

		//eintragen normale USerdaten
		$sql = sprintf("%s %s SET " . "username='%s', " . "user_gender='%s', " .
			"email='%s', " . "password='%s', " . "antwortmail='%s', " . "user_firma='%s', " .
			"user_vorname='%s', " . "user_nachname='%s', " . "user_strasse='%s', " .
			"user_ort='%s', " . "user_plz='%s', " . "user_telefon='%s', " .
			"user_fax='%s', " . "user_land='%s', " . "active='%s'," . "confirm_code='%s'," .
			"user_kontonr='%s', " . "user_blz='%s', " . "user_bankname='%s', " .
			"user_confirm_abbuchung='%s', " . "user_agb_ok='%s', " .
			"user_newsletter='%s' %s",
			$insup,
			$this->cms->tbname['papoo_user'],
			$this->db->escape($this->checked->neuusername),
			$this->db->escape($this->checked->user_gender),
			$this->db->escape($this->checked->email),
			$password,
			$this->db->escape($this->checked->antwortmail),
			$this->db->escape($this->checked->user_firma),
			$this->db->escape($this->checked->user_vorname),
			$this->db->escape($this->checked->user_nachname),
			$this->db->escape($this->checked->user_strasse),
			$this->db->escape($this->checked->user_ort),
			$this->db->escape($this->checked->user_plz),
			$this->db->escape($this->checked->user_telefon),
			$this->db->escape($this->checked->user_fax),
			$this->db->escape($this->checked->user_land),
			$active,
			$this->confirm_code,
			$this->db->escape($this->checked->user_kontonr),
			$this->db->escape($this->checked->user_blz),
			$this->db->escape($this->checked->user_bankname),
			$this->db->escape($this->checked->user_confirm_abbuchung),
			$this->db->escape($this->checked->user_agb_ok),
			$this->db->escape($this->checked->user_newsletter),
			$where
		);

		$this->db->query($sql);
		if ($modus != 'update') {
			//Eintragen in die lookup_ug
			if (empty($userid)) {
				$userid = $this->db->insert_id;
			}
			else {
				$sql = sprintf("DELETE FROM %s WHERE userid='%s'",
					$this->cms->papoo_lookup_ug,
					$userid
				);
				$this->db->query($sql);
			}
			//die gruppenid auf jeder setzen

			$sql = "INSERT INTO " . $this->cms->papoo_lookup_ug . " SET userid='$userid', gruppenid='10' ";
			$this->db->query($sql);

		}
		#$sql = "INSERT INTO " . $this->cms->papoo_lookup_ug . " SET userid='$userid', gruppenid='20' ";
		#$this->db->query($sql);

		IfNotSetNull($userid);
		IfNotSetNull($where2);

		//eintragen spezielle USerdaten
		$sql = sprintf("%s %s SET " . "partner_user_id='%s', " . "partner_name='%s', " .
			"partner_code='%s', " . "partner_url='%s', " . "partner_logo='%s' %s",
			$insup, $this->cms->tbname['papoo_partnerverwaltung_daten'],
			$userid,
			$this->db->escape($this->checked->user_firma),
			$this->db->escape("nobr:" . $this->checked->beschreibung),
			$this->db->escape($this->checked->partner_url),
			$this->checked->image_name,
			$where2
		);
		$this->db->query($sql);
	}

	/**
	 * Neuen Partner erstellen
	 */
	function new_partner()
	{
		//Wenn Daten kommen

		if (!empty($this->checked->formSubmit)) {
			//checken
			$this->checkdata();
			//Wenn ok, dann eintragen
			if (empty($this->fehler_eingabe)) {
				$this->insert_update('insert');
			}
			else {
				//Fehlermeldung ausgeben
				$this->content->template['fehler'] =
					"<h2>Überprüfen Sie die markierten Felder!</h2>";
				$this->doitagain();
			}
			//ansonsten nochmal anbieten mit Fehlermeldung
		}
	}

	/**
	 * Daten zur Kontrolle
	 */
	function doitagain()
	{
		foreach ($_POST as $key => $value) {
			$this->content->template['user']['0'][$key] = $value;
		}
	}

	/**
	 *  �berpr�fung
	 *
	 * @param string $modus
	 */
	function checkdata($modus = "")
	{

		//einige Variablen initialisieren
		$this->fehler_eingabe = "";
		$this->checked->neuusername = htmlentities($this->checked->neuusername);

		//checken ob Geschlecht stimmt
		if (empty($this->checked->user_gender)) {
			$this->content->template['genderfalsch'] = "<strong style=\"color:red;\">***</strong>";
			$this->fehler_eingabe .= "user_gender,";
		}

		/*User �berpr�fen*/

		// existiert dieser User schon ??
		if ($modus != "front_edit") {
			if (!empty($this->checked->neuusername)) {
				$selectuser = "SELECT COUNT(userid) FROM " . $this->cms->papoo_user . " " .
					"WHERE username = '" . $this->db->escape($this->checked->neuusername) . "' ";
				$result = $this->db->get_var($selectuser);
				$selectuser = "SELECT (userid) FROM " . $this->cms->papoo_user . " " .
					"WHERE username = '" . $this->db->escape($this->checked->neuusername) . "' ";
				$result_id = $this->db->get_var($selectuser);
				if ($result_id != $this->checked->visilex_user_id) {
					// Wenn ja...
					if ($result == 1) {
						// dann auf falsch setzen
						$this->content->template['neuusernamefalsch'] = "<strong style=\"color:red;\">***</strong>";
						$this->fehler_eingabe .= "neuusername,";
					}
				}
				// existiert schon
			}
			// kein neuusername eingegeben
			else {
				if (empty($this->session->neuusername)) {
					$this->content->template['neuusernamefalsch'] = "<strong style=\"color:red;\">***</strong>";
					$this->fehler_eingabe .= "user,";
				}
				$this->checked->neuusername = "";
				$this->content->template['neuusernamefalsch'] = "<strong style=\"color:red;\">***</strong>";
				$this->fehler_eingabe .= "user,";
			}
		}
		// Auch wenn der neuusername default ist
		if ($this->checked->neuusername == "neuusername") {

			$this->content->template['neuusernamefalsch'] = "<strong style=\"color:red;\">***</strong>";
			$this->fehler_eingabe .= "user,";
		}
		//Blacklist filter
		if ($this->blacklist->do_blacklist($this->checked->email) == "not_ok") {
			$this->fehler_eingabe .= "user,";
		}

		/*Passwort �berpr�fen*/
		if (empty($this->checked->formpassword) and empty($this->session->password)) {
			if (empty($this->checked->formpassword) or $this->checked->formpassword1 != $this->checked->formpassword2) {
				if ($this->checked->formpassword1 != $this->checked->formpassword2) {
					$this->content->template['nomatch'] = "<strong style=\"color:red;\">***</strong>";
					$this->fehler_eingabe .= "passnomatch,";
				}
			}
		}
		if (empty($this->checked->formpassword) and empty($this->checked->formpassword1)) {
			if (empty($this->checked->visilex_user_id)) {
				$this->content->template['pass1falsch'] = "<strong style=\"color:red;\">***</strong>";
			}
		}

		/*Email �berpr�fen */

		// keine Email angegeben
		if (empty($this->checked->email)) {
			#$this->content->template['emailfalsch'] = "<strong style=\"color:red;\">***</strong>";
			#$this->fehler_eingabe .= "email,";
		}
		else {
			// Validit�t der Mail checken
			if (!$this->validateEmail($this->checked->email)) {
				$this->emailfalsch = 1;
				$this->content->template['emailfalsch'] = "<strong style=\"color:red;\">***</strong>";
				$this->fehler_eingabe .= "email,";
			}
			else {
				$selectuser = "SELECT COUNT(userid) FROM " . $this->cms->papoo_user . " " .
					"WHERE email = '" . $this->db->escape($this->checked->email) . "' ";
				$result = $this->db->get_var($selectuser);
				// Wenn ja...
				if ($result > 0) {
					$selectuser = "SELECT userid FROM " . $this->cms->papoo_user . " " .
						"WHERE email = '" . $this->db->escape($this->checked->email) . "' ";
					$result_id = $this->db->get_var($selectuser);
					$isok = "";
					if ($result_id != $this->checked->visilex_user_id) {
						#$this->content->template['emailfalsch'] = "<strong style=\"color:red;\">***</strong>";
						#$this->fehler_eingabe .= "email";
					}
				}
				// existiert schon
			}
		}
		/*Vorname �berpr�fen*/
		if (empty($this->checked->user_vorname) or $this->checked->user_vorname == "Vorname") {
			$this->content->template['vornamefalsch'] = "<strong style=\"color:red;\">***</strong>";
			$this->fehler_eingabe .= "vorname,";
		}
		/*Nachname �berpr�fen*/
		if (empty($this->checked->user_nachname) or $this->checked->user_nachname == "Nachname") {
			$this->content->template['nachnamefalsch'] = "<strong style=\"color:red;\">***</strong>";
			$this->fehler_eingabe .= "nachname,";
		}
		/*Postleitzahl �berpr�fen*/
		if (empty($this->checked->user_plz) or $this->checked->user_plz == "Postleitzahl") {
			$this->content->template['plzfalsch'] = "<strong style=\"color:red;\">***</strong>";
			$this->fehler_eingabe .= "plz,";
		}
		/*Ort �berpr�fen*/
		if (empty($this->checked->user_ort) or $this->checked->user_ort == "Wohnort") {
			$this->content->template['ortfalsch'] = "<strong style=\"color:red;\">***</strong>";
			$this->fehler_eingabe .= "ort,";
		}
		/*Strasse und Hausnummer �berpr�fen*/
		if (empty($this->checked->user_strasse) or $this->checked->user_strasse == "Strasse und Hausnummer") {
			$this->content->template['strnrfalsch'] = "<strong style=\"color:red;\">***</strong>";
			$this->fehler_eingabe .= "strnr,";
		}
		/*Land �berpr�fen*/
		if (empty($this->checked->user_land) or $this->checked->user_land == "eingeben") {
			$this->content->template['landfalsch'] = "<strong style=\"color:red;\">***</strong>";
			$this->fehler_eingabe .= "land,";
		}
	}

	/**
	 * Creates a Backup of the needed tables
	 */
	function make_dump()
	{
		$this->diverse->extern_dump("partnerverwaltung_daten,partnerverwaltung_pref");
	}

	/**
	 * FUNCTION: validateEmail
	 *
	 * Validates an email address and return true or false.
	 *
	 * @param string $address email address
	 * @return bool true/false
	 * @access public
	 */
	function validateEmail($address)
	{
		return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9_-]+(\.[_a-z0-9-]+)+$/i', $address);
	}
}

$partnerverwaltung = new partnerverwaltung();
