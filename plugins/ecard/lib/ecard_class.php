<?php

/**
 * Class ecard_class
 */
#[AllowDynamicProperties]
class ecard_class
{
	/** @var array diverse Einstellungen */
	var $einstellungen = array();
	/** @var array Liste aller Baby-Galerien */
	var $ecardn_liste = array();

	/**
	 * ecard_class constructor.
	 */
	function __construct()
	{
		global $cms, $user, $db, $db_praefix, $checked, $content, $weiter, $image_core;
		$this->cms = & $cms;
		$this->user = & $user;
		$this->db = & $db;
		$this->db_praefix = $db_praefix;
		$this->checked = & $checked;
		$this->content = & $content;
		$this->weiter = & $weiter;
		$this->image_core = & $image_core;

		$this->einstellungen =
			array("bild_breite" => 540, "bild_hoehe" => 405, "thumb_breite" => 288, "thumb_hoehe" => 216, "streifenbild_offset" => 200, "streifenbild_count" => 5);

		//$_SESSION['plugins']['ecard'] = array();

		$this->make_ecard();
	}

	/**
	 * Global Aktions-Weiche der Klasse
	 */
	function make_ecard()
	{
		global $template;

		if (!defined("admin")) {
			$this->ecard_init("FRONT");

			// Frontend
			if (strpos("XXX".$template, "/ecard_front.html")) {
				if (@$this->checked->action) $this->switch_front_actions($this->checked->action);
				$this->switch_front();

				$this->content->template['front_link_ecard']='./plugin.php?menuid='.$this->checked->menuid.'&template=ecard/templates/ecard_front.html';
			}
		}
		else {
			// Backend
			if (strpos("XXX".$template, "/ecard_back.html")) {
				$this->user->check_intern();
				$this->ecard_init("BACK");
				$this->switch_back(@$this->checked->action);
			}

			if (strpos("XXX".$template, "/ecard_back_cards.html")) {
				$this->user->check_intern();
				$this->ecard_init("BACK");
				$this->switch_back_babies(@$this->checked->action);
			}
			if (strpos("XXX".$template, "/ecard_back_galerien.html")) {
				$this->user->check_intern();
				$this->ecard_init("BACK");
				$this->switch_back_galerien(@$this->checked->action);
			}
			if (strpos("XXX".$template, "/ecard_back_einbindung.html")) {
				$this->user->check_intern();
				$this->ecard_init("BACK");
			}
			if (strpos("XXX".$template, "/ecard_back_einstellungen.html")) {
				$this->user->check_intern();
				$this->ecard_init("BACK");
				$this->switch_back_einstellungen(@$this->checked->action);
			}
		}
	}

	/**
	 * Initialisieren der Klasse. Es werden alle "grundlegenden" Funktionen ausgef�hrt.
	 *
	 * @param string $modus
	 */
	function ecard_init($modus = "FRONT")
	{
		$this->ecardn_liste = $this->galerien_liste();
		$this->content->template['plugin']['ecard']['galerien_liste'] = & $this->ecardn_liste;

		$this->einstellungen_laden();
		$this->content->template['plugin']['ecard']['einstellungen'] = & $this->einstellungen;
	}

	/**
	 * Aktionen Weiche Frontend
	 *
	 * @param $action
	 */
	function switch_front_actions($action)
	{
		switch ($action)
		{
		case "email_senden":
			$this->helper_email_senden($this->checked->bgalb_id);
			break;
		}
	}

	// Aktions-Weiche f�r Frontend
	function switch_front()
	{
		if($this->checked->bgalb_id && $this->checked->bgalg_id) {
			//echo "Baby_ID: ".$this->checked->bgalb_id."; Galerie_ID: ".$this->checked->bgalg_id."<br />\n";
			$this->content->template['plugin']['ecard']['template_weiche'] = "ecard_DETAIL";
			$this->content->template['plugin']['ecard']['galerie_data'] = $this->galerien_data($this->checked->bgalg_id);
			$this->content->template['plugin']['ecard']['baby_data'] = $this->babies_data($this->checked->bgalb_id);

			if ($_SESSION['plugins']['ecard']['glueckwuensche'] &&
				in_array($this->checked->bgalb_id, $_SESSION['plugins']['ecard']['glueckwuensche'])) {
				$this->content->template['plugin']['ecard']['template_switch_glueckwuensche_formular'] = false;
			}
			else {
				$this->content->template['plugin']['ecard']['template_switch_glueckwuensche_formular'] = true;
			}
			$this->content->template['site_title'] = $this->content->template['plugin']['ecard']['baby_data']['bgalb_name'];
			if ($this->checked->send=="ok") {
				$this->content->template['ecard_is_send'] = $this->content->template['message']['plugin']['ecard']['is_send'];
			}

		}
		elseif ($this->checked->bgalg_id) {
			$this->helper_weiter_init($this->checked->bgalg_id);
			$this->content->template['plugin']['ecard']['template_weiche'] = "ecard_LISTE";
			$this->content->template['plugin']['ecard']['galerie_data'] = $this->galerien_data($this->checked->bgalg_id);
			$this->content->template['plugin']['ecard']['babies_liste'] = $this->babies_liste($this->checked->bgalg_id);
			$this->content->template['site_title'] = $this->content->template['plugin']['ecard']['galerie_data']['bgalg_name'];
		}
		elseif (count($this->ecardn_liste) == 1) {
			$this->checked->bgalg_id = $this->ecardn_liste[0]['bgalg_id'];
			$this->switch_front();
		}
		else {
			$this->content->template['plugin']['ecard']['template_weiche'] = "GALERIEN_LISTE";
		}
	}

	/**
	 * Aktions-Weiche f�r Backend
	 *
	 * @param string $modus
	 */
	function switch_back($modus = "")
	{
		switch ($modus)
		{
		case "":
		default:

			break;
		}
	}

	/**
	 * Aktions-Weiche f�r Backend "Babies"
	 *
	 * @param string $modus
	 */
	function switch_back_babies($modus = "")
	{
		switch ($modus) {
		case "bgalb_loeschen_do":
			$this->ecards_loeschen($this->checked->bgalb_id);
			$this->helper_galerien_make_streifenbilder();
			$this->switch_back_babies();
			break;

		case "bgalb_loeschen":
			$this->content->template['plugin']['ecard']['baby_data'] = $this->babies_data($this->checked->bgalb_id);
			$this->content->template['plugin']['ecard']['template_weiche'] = "LOESCHEN";
			break;

		case "bgalb_save":
			$this->babies_save();
			$this->helper_galerien_make_streifenbilder();
			$this->switch_back_babies();
			break;

		case "bgalb_neu":
			$temp_data = array();
			$temp_data = $this->babies_data_extra($temp_data);
			$this->content->template['plugin']['ecard']['baby_data'] = $temp_data;
			$this->content->template['plugin']['ecard']['modus'] = $modus;
			$this->helper_set_babies_neu_edit_ranges();
			$this->content->template['plugin']['ecard']['template_weiche'] = "ecard_NEU_EDIT";
			break;

		case "bgalb_edit":
			$temp_data = $this->babies_data($this->checked->bgalb_id);
			$temp_data = $this->babies_data_extra($temp_data);
			$temp_data['bgalb_name'] = "nodecode:".$temp_data['bgalb_name'];
			$temp_data['bgalb_gewicht'] = "nodecode:".$temp_data['bgalb_gewicht'];
			$temp_data['bgalb_groesse'] = "nodecode:".$temp_data['bgalb_groesse'];
			$temp_data['bgalb_text'] = "nodecode:".$temp_data['bgalb_text'];
			$this->content->template['plugin']['ecard']['baby_data'] = $temp_data;
			$this->content->template['plugin']['ecard']['modus'] = $modus;
			$this->helper_set_babies_neu_edit_ranges();
			$this->content->template['plugin']['ecard']['template_weiche'] = "ecard_NEU_EDIT";
			break;

		case "":
		default:
			$this->content->template['plugin']['ecard']['babies_liste'] = $this->babies_liste(1, "ALL_ALL");
			$this->content->template['plugin']['ecard']['template_weiche'] = "ecard_LISTE";
			break;
		}
	}

	/**
	 * Aktions-Weiche f�r Backend "Galerien"
	 *
	 * @param string $modus
	 */
	function switch_back_galerien($modus = "")
	{
		switch ($modus) {
		case "bgalg_save":
			$this->galerien_save();
			$this->ecard_init("BACK");
			$this->switch_back_galerien();
			break;

		case "bgalg_edit":
			$temp_data = $this->galerien_data($this->checked->bgalg_id);
			$temp_data['bgalg_name'] = "nodecode:".$temp_data['bgalg_name'];
			$temp_data['bgalg_text'] = "nodecode:".$temp_data['bgalg_text'];
			$this->content->template['plugin']['ecard']['galerie_data'] = $temp_data;
			$this->content->template['plugin']['ecard']['template_weiche'] = "GALERIEN_EDIT";
			break;

		case "bgalg_new":
			$temp_data = $this->galerien_data($this->checked->bgalg_id);
			$temp_data['bgalg_name'] = "nodecode:".$temp_data['bgalg_name'];
			$temp_data['bgalg_text'] = "nodecode:".$temp_data['bgalg_text'];
			$this->content->template['plugin']['ecard']['galerie_data'] = $temp_data;
			$this->content->template['plugin']['ecard']['template_weiche'] = "GALERIEN_EDIT";
			break;

		case "":
		default:
			$this->content->template['plugin']['ecard']['template_weiche'] = "GALERIEN_LISTE";
			break;
		}
	}

	/**
	 * Aktions-Weiche f�r Backend "Einstellungen"
	 *
	 * @param string $modus
	 */
	function switch_back_einstellungen($modus = "")
	{
		switch ($modus) {
		case "bgalset_save":
			$this->einstellungen_save();
			$this->ecard_init();
			break;

		case "":
		default:
			break;
		}
	}

	// ===========================================================
	//
	// Galerien Funktionen
	//
	// ===========================================================

	/**
	 * Erstellt eine Liste aller Baby-Galerien.
	 *
	 * @return array|void
	 */
	function galerien_liste()
	{
		$sql = sprintf("SELECT * FROM %s
						ORDER BY bgalg_order_id",
			$this->db_praefix."ecard_galerien"
		);
		$temp_return = $this->db->get_results($sql, ARRAY_A);
		return $temp_return;
	}

	/**
	 * Gibt die Daten der Baby-Galerie $bgalg_id zurueck
	 *
	 * @param int $bgalg_id
	 * @return array|void
	 */
	function galerien_data($bgalg_id = 0)
	{
		$sql = sprintf("SELECT * FROM %s
						WHERE bgalg_id='%d'",
			$this->db_praefix."ecard_galerien",
			$bgalg_id
		);
		$temp_return = $this->db->get_row($sql, ARRAY_A);
		return $temp_return;
	}

	function galerien_save()
	{
		if (empty($this->checked->bgalg_id)) {
			$sql = sprintf("INSERT INTO %s
						SET bgalg_name='%s', bgalg_text='%s'",
				$this->db_praefix."ecard_galerien",

				$this->db->escape(strip_tags($this->checked->bgalg_name)),
				$this->db->escape(strip_tags($this->checked->bgalg_text))
			);
			$this->db->query($sql);
		}
		else {
			$sql = sprintf("UPDATE %s
						SET bgalg_name='%s', bgalg_text='%s'
						WHERE bgalg_id='%d'",
				$this->db_praefix."ecard_galerien",

				$this->db->escape(strip_tags($this->checked->bgalg_name)),
				$this->db->escape(strip_tags($this->checked->bgalg_text)),

				$this->checked->bgalg_id
			);
			$this->db->query($sql);
		}
	}



	// ===========================================================
	// 
	// Baby Funktionen
	//
	// ===========================================================

	/**
	 * Gibt eine Liste aller Babies der Galerie $bgalg_id zurueck
	 *
	 * @param int $bgalg_id
	 * @param string $modus
	 *  "": Babies der Galerie $bgalg_id (max. Anzahl LIMIT)
	 *  "ALL": alle Babies der Galerie $bgalg_id (ohne LIMIT)
	 *  $modus "ALL_ALL": alle Babies aller Galerien ohne LIMIT
	 * @return array|void
	 */
	function babies_liste ($bgalg_id = 0, $modus = "")
	{
		$temp_return = array();

		if ($bgalg_id) {
			$temp_sql_add_join = "";
			$temp_sql_add_galerie = sprintf("WHERE t1.bgalb_bgalg_id='%d'", $bgalg_id);
			$temp_sql_add_limit = $this->db->escape($this->weiter->sqllimit);

			switch ($modus) {
			case "ALL":
				$temp_sql_add_limit = "";
				break;

			case "ALL_ALL":
				$temp_sql_add_join = sprintf("LEFT JOIN %s AS t2 ON (t1.bgalb_bgalg_id = t2.bgalg_id)", $this->db_praefix."ecard_galerien");
				$temp_sql_add_galerie = "";
				$temp_sql_add_limit = "";
				break;
			}

			$sql = sprintf("SELECT * FROM %s AS t1
							%s
							%s
							ORDER BY t1.bgalb_datum DESC %s",
				$this->db_praefix."ecard_data",
				$temp_sql_add_join,
				$temp_sql_add_galerie,
				$temp_sql_add_limit
			);
			$temp_return = $this->db->get_results($sql, ARRAY_A);
		}
		return $temp_return;
	}

	/**
	 * Gibt die Daten des Babies $bgalb_id zurueck
	 *
	 * @param int $bgalb_id
	 * @return array|void
	 */
	function babies_data ($bgalb_id = 0)
	{
		$temp_return = array();

		if ($bgalb_id) {
			$sql = sprintf("SELECT * FROM %s 
							WHERE bgalb_id='%d'",
				$this->db_praefix."ecard_data",
				$bgalb_id
			);
			$temp_return = $this->db->get_row($sql, ARRAY_A);
		}

		return $temp_return;
	}

	/**
	 * @param array $data
	 * @return array|mixed
	 */
	function babies_data_extra($data = array())
	{
		$temp_return = $data;

		if ($data['bgalb_datum']) {
			$temp_datum = strtotime($data['bgalb_datum']);
		}
		else {
			$temp_datum = time();
		}


		$temp_return['extra_datum'] = getdate($temp_datum);

		return $temp_return;
	}

	function babies_save()
	{
		if ($this->checked->modus == "bgalb_neu") {
			$sql = sprintf("INSERT INTO %s SET bgalb_text='.. dummie-text ..'", $this->db_praefix."ecard_data");
			$this->db->query($sql);
			$temp_bgalb_id = $this->db->insert_id;
			$temp_bgalb_bild = "";
		}
		else {
			$temp_data = $this->babies_data($this->checked->bgalb_id);
			$temp_bgalb_id = $temp_data['bgalb_id'];
			$temp_bgalb_bild = $temp_data['bgalb_bild'];
		}

		if ($_FILES['bgalb_bild_upload']['name']) {
			$temp_bgalb_bild = $this->helper_babies_bild_save($temp_bgalb_id);
		}

		$temp_datum_string = sprintf("%d-%s-%s %s:%s:00",
			$this->checked->extra_datum_jahr,
			str_pad($this->checked->extra_datum_monat, 2, "0", STR_PAD_LEFT),
			str_pad($this->checked->extra_datum_tag, 2, "0", STR_PAD_LEFT),

			str_pad($this->checked->extra_datum_stunde, 2, "0", STR_PAD_LEFT),
			str_pad($this->checked->extra_datum_minute, 2, "0", STR_PAD_LEFT)
		);

		$sql = sprintf("UPDATE %s
						SET bgalb_bgalg_id='%d', bgalb_datum='%s', bgalb_name='%s',
						bgalb_geschlecht='%d', bgalb_gewicht='%s', bgalb_groesse='%s', 
						bgalb_bild='%s', bgalb_text='%s'
						WHERE bgalb_id='%d'",
			$this->db_praefix."ecard_data",

			$this->checked->bgalb_bgalg_id,
			$this->db->escape($temp_datum_string),
			$this->db->escape($this->checked->bgalb_name),

			$this->checked->bgalb_geschlecht,
			$this->db->escape($this->checked->bgalb_gewicht),
			$this->db->escape($this->checked->bgalb_groesse),

			$this->db->escape($temp_bgalb_bild),
			$this->db->escape(mb_substr($this->checked->bgalb_text, 0, 300, "UTF-8")),

			$temp_bgalb_id
		);
		$this->db->query($sql);
	}

	/**
	 * ecard_class::ecards_loeschen()
	 *
	 * @param integer $bgalb_id
	 * @return void
	 */
	function ecards_loeschen($bgalb_id = 0)
	{
		if ($bgalb_id) {
			$temp_baby_data = $this->babies_data($bgalb_id);

			// Bilder loeschen
			if (!empty($temp_baby_data['bgalb_bild'])) {
				$temp_pfad_images = $this->cms->pfadhier."/plugins/ecard/ecard_bilder/";
				$temp_pfad_thumbs = $temp_pfad_images."_thumbs/";

				if (file_exists($temp_pfad_images.$temp_baby_data['bgalb_bild'].".jpg")) {
					unlink($temp_pfad_images.$temp_baby_data['bgalb_bild'].".jpg");
				}
				if (file_exists($temp_pfad_thumbs.$temp_baby_data['bgalb_bild'].".jpg")) {
					unlink($temp_pfad_thumbs.$temp_baby_data['bgalb_bild'].".jpg");
				}
			}

			// Eintrag loeschen
			$sql = sprintf("DELETE FROM %s WHERE bgalb_id='%d'",
				$this->db_praefix."ecard_data",
				$temp_baby_data['bgalb_id']
			);
			$this->db->query($sql);
		}
	}

	// ===========================================================
	//
	// Einstellungen Funktionen
	//
	// ===========================================================
	/**
	 * Erg�nzt die fixen Einstellungen $this->einstellungen um die in der DB gespeicherten Einstellungen
	 */
	function einstellungen_laden()
	{
		$temp_einstellungen = $this->einstellungen_data();
		$this->einstellungen = array_merge($this->einstellungen, $temp_einstellungen);
	}

	/**
	 * Gibt die Daten des Babies $bgalb_id zurueck
	 *
	 * @return array|void
	 */
	function einstellungen_data ()
	{
		$sql = sprintf("SELECT * FROM %s WHERE bgalset_id='1'",
			$this->db_praefix."ecard_einstellungen"
		);
		$temp_return = $this->db->get_row($sql, ARRAY_A);
		return $temp_return;
	}

	/**
	 * ecard_class::einstellungen_save()
	 *
	 * @return string|void
	 */
	function einstellungen_save()
	{
		$temp_return = "save";

		$sql = sprintf("UPDATE %s
						SET bgalset_email_absender='%s' 
						WHERE bgalset_id='1' ",
			$this->db_praefix."ecard_einstellungen",

			$this->db->escape(trim(strip_tags($this->checked->bgalset_email_absender)))
		);
		$this->db->query($sql);
		return $temp_return;
	}

	// ===========================================================
	//
	// Hilfs Funktionen
	//
	// ===========================================================
	/**
	 * @param int $bgalg_id
	 */
	function helper_weiter_init($bgalg_id = 0)
	{
		$this->weiter->make_limit(12);
		$this->weiter->result_anzahl = count($this->babies_liste($bgalg_id, "ALL"));
		$this->weiter->weiter_link="plugin.php?menuid=".$this->checked->menuid."&template=ecard/templates/ecard_front.html";
		$this->weiter->modlink="no";
		$this->weiter->do_weiter("teaser");

	}

	/**
	 * ecard_class::helper_email_senden()
	 *
	 * @param integer $bgalb_id
	 * @return void
	 */
	function helper_email_senden($bgalb_id = 0)
	{
		if ($bgalb_id) {
			$temp_baby_data = $this->babies_data($bgalb_id);

			global $spamschutz;

			if ($this->cms->stamm_kontakt_spamschutz && $spamschutz->is_spam) {
				$this->content->template['ecard_error'] = "Spammschutz nicht korrekt";
			}
			else {
				if (!empty($temp_baby_data)) {
					global $mail_it;

					$mail_it->from = $this->einstellungen['bgalset_email_absender'];
					$mail_it->to = $this->checked->email_empfaenger;

					$mail_it->subject = "Ecard von ".htmlentities($this->checked->email_name);

					$temp_text_html = "<html><head></head><body>";
					$temp_text_html .= "<h1>Ecard von ".htmlentities($this->checked->email_name)."</h1>";
					$temp_text_html .= "<p><img src='http://".$this->cms->title_send.$this->cms->webverzeichnis."/plugins/ecard/ecard_bilder/".$temp_baby_data['bgalb_bild'].".jpg' ></p>";
					$temp_text_html .= "<p>";

					$temp_text_html .= "</p>";

					$temp_text_html .= "<p>".$this->content->template['message']['plugin']['ecard']['email_von'].": ".htmlentities($this->checked->email_name)."</p>";
					$temp_text_html .= "<p>".nl2br(htmlentities($this->checked->email_text))."</p>";

					$temp_text_html .= "</body></html>";

					$mail_it->body_html = $temp_text_html;
					$temp_ergebnis = $mail_it->do_mail();

					$this->reload();
				}
			}
		}
	}


	/**
	 * ecard_class::reload()
	 *
	 * @return void
	 */
	function reload()
	{
		$location_url="plugin.php?menuid=".$this->checked->menuid."&template=ecard/templates/ecard_front.html&bgalg_id=".$this->checked->bgalg_id."&bgalb_id=".$this->checked->bgalb_id."&send=ok";
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['form_manager']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}

	/**
	 * ecard_class::helper_set_babies_neu_edit_ranges()
	 *
	 * @return void
	 */
	function helper_set_babies_neu_edit_ranges()
	{
		$temp_date = getdate();
		$this->content->template['plugin']['ecard']['range']['jahre'] = array($temp_date['year'], $temp_date['year']-1);
		$this->content->template['plugin']['ecard']['range']['monate'] = range(1, 12);
		$this->content->template['plugin']['ecard']['range']['tage'] = range(1, 31);

		$this->content->template['plugin']['ecard']['range']['stunden'] = range(1, 24);
		$this->content->template['plugin']['ecard']['range']['minuten'] = range(1, 59);
	}

	/**
	 * ecard_class::helper_babies_bild_save()
	 *
	 * @param integer $bgalb_id
	 * @return mixed|string
	 */
	function helper_babies_bild_save($bgalb_id = 0)
	{
		$temp_return = "";
		$temp_bildname_neu = $bgalb_id."_".rand(10000, 99999);
		$temp_baby_data = $this->babies_data($bgalb_id);
		$temp_return = $temp_baby_data['bgalb_bild'];

		$temp_pfad_images = $this->cms->pfadhier."/plugins/ecard/ecard_bilder/";
		$temp_pfad_thumbs = $temp_pfad_images."_thumbs/";

		$this->image_core->pfad_images = $temp_pfad_images;
		$this->image_core->pfad_thumbs = $temp_pfad_thumbs;
		$this->image_core->image_load($_FILES['bgalb_bild_upload']);
		$image_infos = $this->image_core->image_infos;
		// Test: Bild-Typ JPG
		if ($image_infos['type'] != "JPG") {
			unlink ($image_infos['bild_temp']);
			return $temp_return;
		}

		// Eigentliche Bilder erzeugen und sichern
		$image = $this->image_core->image_create($image_infos['bild_temp']);

		$bild_gross = $this->image_core->image_create(array($this->einstellungen['bild_breite'], $this->einstellungen['bild_hoehe']));
		imagecopyresampled($bild_gross, $image, 0, 0, 0, 0, $this->einstellungen['bild_breite'], $this->einstellungen['bild_hoehe'], $image_infos['breite'], $image_infos['hoehe']);
		$this->image_core->image_save($bild_gross, $temp_pfad_images.$temp_bildname_neu.".jpg");

		$bild_klein = $this->image_core->image_create(array($this->einstellungen['thumb_breite'], $this->einstellungen['thumb_hoehe']));
		imagecopyresampled($bild_klein, $image, 0, 0, 0, 0, $this->einstellungen['thumb_breite'], $this->einstellungen['thumb_hoehe'], $image_infos['breite'], $image_infos['hoehe']);
		$this->image_core->image_save($bild_klein, $temp_pfad_thumbs.$temp_bildname_neu.".jpg");

		// tempor�re Daten l�schen und image_core r�cksetzen
		ImageDestroy($image);
		ImageDestroy($bild_gross);
		ImageDestroy($bild_klein);
		if (file_exists($image_infos['bild_temp'])) {
			unlink ($image_infos['bild_temp']);
		}
		$this->image_core->init();


		// eventuelle alte Bilder loeschen
		if (!empty($temp_baby_data['bgalb_bild'])) {
			if (file_exists($temp_pfad_images.$temp_baby_data['bgalb_bild'].".jpg")) unlink($temp_pfad_images.$temp_baby_data['bgalb_bild'].".jpg");
			if (file_exists($temp_pfad_thumbs.$temp_baby_data['bgalb_bild'].".jpg")) unlink($temp_pfad_thumbs.$temp_baby_data['bgalb_bild'].".jpg");
		}

		$temp_return = $temp_bildname_neu;
		return $temp_return;
	}

	/**
	 * ecard_class::helper_galerien_make_streifenbilder()
	 *
	 * @return void
	 */
	function helper_galerien_make_streifenbilder()
	{
		$temp_galerien = $this->galerien_liste();

		if (!empty($temp_galerien)) {
			foreach($temp_galerien as $temp_galerie) {
				$temp_bildname_neu = $temp_galerie['bgalg_id']."_".rand(10000, 99999);

				$temp_baby_liste = $this->babies_liste($temp_galerie['bgalg_id']);
				if (!empty($temp_baby_liste)) {
					$temp_bildanzahl = min($this->einstellungen['streifenbild_count'], count($temp_baby_liste));

					$temp_streifenbild = $this->image_core->image_create(array(($this->einstellungen['streifenbild_offset'] * $temp_bildanzahl), $this->einstellungen['thumb_hoehe']), "JPG");
					$this->image_core->image_infos['type'] = "JPG";
					$temp_farbe_weiss = imagecolorallocate ($temp_streifenbild, 255, 255, 255);
					imagefill($temp_streifenbild, 0, 0, $temp_farbe_weiss);

					$i = 1;
					$temp_offset = 0;

					foreach($temp_baby_liste as $baby_data) {
						if ($baby_data['bgalb_bild'] && file_exists ($this->cms->pfadhier."/plugins/ecard/ecard_bilder/_thumbs/".$baby_data['bgalb_bild'].".jpg")) {
							$temp_bild = $this->image_core->image_create($this->cms->pfadhier."/plugins/ecard/ecard_bilder/_thumbs/".$baby_data['bgalb_bild'].".jpg");
							$temp_result = ImageCopy($temp_streifenbild, $temp_bild, $temp_offset, 0, 0, 0, $this->einstellungen['thumb_breite'], $this->einstellungen['thumb_hoehe']);

							$i++;
							if ($i > $this->einstellungen['streifenbild_count']) break;
							$temp_offset += $this->einstellungen['streifenbild_offset'];
						}
					}
				}

				$this->image_core->image_save($temp_streifenbild, $this->cms->pfadhier."/plugins/ecard/bilder/".$temp_bildname_neu.".jpg");
				@ImageDestroy($temp_streifenbild);

				$sql = sprintf("UPDATE %s SET bgalg_bild='%s' WHERE bgalg_id='%d'",
					$this->db_praefix."ecard_galerien",
					$this->db->escape($temp_bildname_neu),
					$temp_galerie['bgalg_id']
				);
				$this->db->query($sql);

				// eventuelles altes Streifenbild loeschen
				if (file_exists($this->cms->pfadhier."/plugins/ecard/bilder/".$temp_galerie['bgalg_bild'].".jpg")) {
					unlink($this->cms->pfadhier."/plugins/ecard/bilder/".$temp_galerie['bgalg_bild'].".jpg");
				}
			}
		}
	}
}

$ecard = new ecard_class();
