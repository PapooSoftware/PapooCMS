<?php

/**
 * Class gutschein
 */
class gutschein
{
	/** @var int */
	const MAIL_TEMPLATE_ID = 11;
	/** @var string */
	const LIEFER_EMAIL_COLUMN = 'kunden_order_liefer_email';
	/** @var string */
	const USE_LIEFER_EMAIL_COLUMN = 'kunden_order_liefer_an_lieferadresse';
	/** @var array Ignoriert, da Postversand nicht aktiv */
	public $EMAIL_VERSANDMETHODEN = array(4);

	function __construct()
	{
		global $cms, $db, $user, $diverse, $content, $weiter, $checked;
		$this->cms = & $cms;
		$this->db = & $db;
		$this->user = & $user;
		$this->diverse = & $diverse;
		$this->content = & $content;
		$this->weiter = & $weiter;
		$this->checked = & $checked;
		$this->do_action();
	}

	/**
	 * gutschein::do_action()
	 *
	 * Hauptfunktion der Klasse. Verzweigt je nach Template und Aktion.
	 *
	 * @return void
	 */
	function do_action()
	{
		global $template;

		if (strpos('XXX'.$template, 'gutschein_back.html')) {
			// Nicht vergessen  - intern immer checken ob der Zugriff erlaubt ist
			if ($this->user->check_intern()) {
				if ($this->checked->to_order_info) {
					$order_id = (int)$this->checked->to_order_info;
					$this->set_gutschein_info($order_id, true);
					$this->redirect("template=papoo_shop/templates/papoo_shop_order.html&order_id=$order_id&faktura_act=show");
				}
				// Gutschein anzeigen wenn gewünscht
				$this->do_show_gutschein();
				// Gutscheine versenden wenn gewünscht
				$this->do_send_gutscheine();
				// Backend füllen
				$this->make_gutschein_backend();
			}
		}
	}

	/**
	 * gutschein::make_gutschein_backend()
	 *
	 * Füllt das Backend-Template und versendet Gutscheine
	 *
	 * @return void
	 */
	function make_gutschein_backend()
	{
		//Die Liste rausholen
		$liste=$this->get_liste_gutscheine();

		//gesendet
		if (($this->checked->send=="send")) {
			$this->content->template['is_send_gutschein']="ok";
		}
		if (($this->checked->send=="error")) {
			$this->content->template['is_no_send_gutschein']="ok";
		}
	}

	/**
	 * gutschein::do_send_gutscheine()
	 *
	 * Versendet Gutscheine, wenn diese Aktion im Backend ausgewählt wurde
	 * und lädt dann die Seite mit neuem GET-Parameter neu.
	 *
	 * @return void
	 */
	function do_send_gutscheine()
	{
		if (!empty($this->checked->send_gutschein) and is_numeric($this->checked->send_gutschein)) {
			if ($this->real_send_gutscheine($this->checked->send_gutschein)) {
				$this->set_gutschein_info($this->checked->send_gutschein, true);
				//Reload - gesendet
				$this->reload("send");
			}
			else {
				//Reload - Fehler beim Senden
				$this->reload("error");
			}
		}
	}

	/**
	 * gutschein::real_send_gutscheine(...)
	 * Führt das Erstellen und Versenden der Gutscheine aus,
	 * aber nur wenn Empfänger Versand per Mail ausgewählt hat
	 *
	 * @param int $order_id ID der Bestellung
	 * @return bool Erfolg des Versands
	 */
	private function real_send_gutscheine($order_id) {
		// Den Beleg rausholen
		$beleg = $this->get_order($order_id);
		// Und die bestellten Produkte
		$produkte = $this->get_order_products($order_id);
		$beleg['produkte_anzahl'] = count($produkte);

		// Will der Kunde den Gutschein überhaupt per Mail?
		// Ja will er, da Postversand zur Zeit nicht aktiv.
		/*
		if (!in_array($beleg['kunden_order_versand'], $this->EMAIL_VERSANDMETHODEN)) {
			// Sonst jetzt abbrechen.
			return false;
		}
		*/

		// Pro Produkt ein PDF erstellen
		foreach ($produkte as &$produkt) {
			// Dateiname des PDFs im Produkte-Array speichern
			$produkt['filename'] = $this->create_pdf($beleg, $produkt);
		}
		unset($produkt);

		// Ist eine Liefer-E-Mail-Adresse in der Datenbank vorgesehen?
		if (isset($beleg[gutschein::LIEFER_EMAIL_COLUMN])) {
			// Will der Kunde an diese Adresse senden?
			if ($beleg[gutschein::USE_LIEFER_EMAIL_COLUMN] == 1) {
				$should_use_liefer_email = true;
			}
		}
		else {
			$should_use_liefer_email = false;
		}

		// Den Gutschein an Versender und Herausgeber senden
		$herausgeber_map = [];
		// Bestimmen welche Gutscheine an welchen Herausgeber gehen sollen
		foreach ($produkte as $produktid => $produkt) {
			$mail = $this->get_herausgeber_mail($produkt);
			if (!isset($herausgeber_map[$mail])) {
				$herausgeber_map[$mail] = [];
			}
			$herausgeber_map[$mail][$produktid] = $produkt;
		}
		$adminmail = $this->cms->admin_email;
		foreach ($herausgeber_map as $mail => $h_produkte) {
			$mails = ($mail !== $adminmail) ? [$mail, $adminmail] : [$mail];
			$this->mail_gutschein($beleg, $h_produkte, true, $this->get_versender_mail(), '', $mails);
		}

		// Den/die Gutschein(e) an den Empfänger und im CC an den Besteller mailen
		if ($should_use_liefer_email) {
			$name = $beleg['kunden_order_liefer_vorname'].' '.$beleg['kunden_order_strasse_liefer_adresse'];
			return $this->mail_gutschein($beleg, $produkte, true, $beleg[gutschein::LIEFER_EMAIL_COLUMN], $name, array($this->get_kunden_email($beleg)));
		}
		else {
			// den/die Gutschein(e) an den Besteller mailen
			$name = $beleg['kunden_order_vorname'].' '.$beleg['kunden_order_name'];
			return $this->mail_gutschein($beleg, $produkte, false, $this->get_kunden_email($beleg), $name);
		}
	}

	/**
	 * gutschein::do_show_gutschein()
	 *
	 * Zeigt Gutschein-PDF an, wenn diese Aktion im Backend ausgewählt wurde
	 * und beendet dann das Script.
	 *
	 * @return void
	 */
	function do_show_gutschein()
	{
		if (!empty($this->checked->show_pdf_gutschein)) {
			// Warnungen während PDF-Ausgabe vermeiden
			ini_set('display_errors', false);
			// Den Beleg rausholen
			$beleg = $this->get_order($this->checked->show_pdf_gutschein);
			// Und die bestellten Produkte
			$produkte = $this->get_order_products($this->checked->show_pdf_gutschein);

			if (!empty($this->checked->product_id)) {
				// Suche gewünschtes Produkt
				foreach ($produkte as $produkt) {
					if ($produkt['produkte_order_produkt_id'] == $this->checked->product_id) break;
				}
				if ($produkt['produkte_order_produkt_id'] != $this->checked->product_id) {
					header('HTTP/1.1 404 Not Found');
					header('Content-Type: text/plain');
					exit();
				}
			}
			else {
				$produkt = $produkte[0];
			}

			// Für das Produkt ein PDF erstellen
			$filename = $this->create_pdf($beleg, $produkt);
			unset($produkt);

			// Ausgabe
			session_write_close();
			header('Content-Type: application/pdf');
			header('Content-Disposition: inline; filename='.basename($filename));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Content-Length: ' . filesize($filename));
			ob_clean();
			flush();
			readfile($filename);
			exit();
		}
	}

	/**
	 * gutschein::get_liste_gutscheine()
	 *
	 * @return void
	 */
	function get_liste_gutscheine()
	{
		//Zuerst die Anzahl der Bestellungen
		$sql=sprintf("SELECT COUNT(order_id) FROM %s
							LEFT JOIN %s ON order_id = kunden_order_id
							WHERE `order_typ` BETWEEN 5 AND 7",
			$this->cms->tbname['plugin_shop_order'],
			$this->cms->tbname['plugin_shop_order_lookup_kunde']
		);

		$this->weiter->result_anzahl=$this->db->get_var($sql);
		$this->weiter->weiter_link='/interna/plugin.php?menuid='.$this->checked->menuid.'&template=gutschein/templates/gutschein_back.html';
		$this->weiter->do_weiter("teaser");
		$this->weiter->make_limit($this->cms->pagesize);

		//Dann mit Limit die nächsten
		$sql=sprintf("SELECT * FROM %s
								LEFT JOIN %s ON order_id = kunden_order_id
								LEFT JOIN %s ON order_id = gutschein_shop_id
								WHERE `order_typ` BETWEEN 5 AND 7
								ORDER BY order_id  DESC
								%s",
			$this->cms->tbname['plugin_shop_order'],
			$this->cms->tbname['plugin_shop_order_lookup_kunde'],
			$this->cms->tbname['plugin_gutschein'],
			$this->weiter->sqllimit
		);

		$result=$this->db->get_results($sql,ARRAY_A);

		// Produkte holen und Versandmethode bestimmen
		if ($result) {
			foreach ($result as &$item) {
				$item['versand_per_mail'] = true; //(in_array($item['kunden_order_versand'], $this->EMAIL_VERSANDMETHODEN));
				$item['order_last_change'] = date('d.m.Y',$item['order_last_change']);

				$sql=sprintf("SELECT * FROM %s
								WHERE produkte_order_id = %d
								ORDER BY produkte_order_produkt_id ASC",
					$this->cms->tbname['plugin_shop_order_lookup_produkte'],
					(int)$item['order_id']
				);
				$item['products']=$this->db->get_results($sql,ARRAY_A);
			}
		}

		$this->content->template['order_datan']=$result;

		$this->content->template['menuid_aktuell']=$this->checked->menuid;
	}

	/**
	 * gutschein::post_papoo()
	 *
	 * Wird am Ende jedes Seitenaufrufs ausgeführt
	 *
	 * @return void
	 */
	function post_papoo()
	{
		if (!defined('admin')) {
			//Frontend - wenn checkout fertg
			if ($this->checked->ps_act=="checkout_fertig") {
				//Dann den letzten Beleg rausholen
				$beleg = $this->get_last_order();
				if (!$beleg) {
					return;
				}

				// Nur bei Rechnungen/Lieferscheinen erstellen
				if ($beleg['order_typ'] >= 5 and $beleg['order_typ'] <= 7) {
					$this->after_checkout_action($beleg);
				}
			}
		}
	}

	/**
	 * gutschein::after_checkout_action()
	 *
	 * Wird von post_papoo() ausgeführt wenn ein Checkout durchgeführt wurde.
	 * Erstellt einen Eintrag in der Gutschein-Liste und versendet ggf.
	 *
	 * @param array $beleg Order-Array
	 * @return void
	 */
	function after_checkout_action($beleg) {
		if (is_string($beleg) or is_int($beleg)) {
			$beleg = $this->get_order($beleg);
		}

		$produkte = $this->get_order_products($beleg['order_id']);

		//Checken ob das schon existiert
		$existiert = $this->has_gutschein_info($beleg['order_id']);

		//Noch kein Eintrag
		if (!$existiert) {
			// Für jedes Produkt im Beleg die Nummer speichern
			foreach ($produkte as $produkt) {
				$this->set_gutschein_number(
					$beleg['order_id'],
					$produkt['produkte_order_produkt_id'],
					$produkt['produkte_lang_anzahl_der_produkte']+1
				);
			}

			//Den Eintrag schon mal in die eigene Tabelle eintragen
			$this->set_gutschein_info($beleg['order_id'], false);

			// Wenn bezahlt
			if ($beleg['order_is_payd']==1) {
				// Versand anstoßen (bricht automatisch ab, wenn Postversand gewünscht)
				if ($this->real_send_gutscheine($beleg['order_id'])) {
					// Wenn Erfolgreich als versandt markieren
					$this->set_gutschein_info($beleg['order_id'], true);
				}
			}
		}
	}

	/**
	 * @param $beleg
	 * @return array|null
	 */
	private function get_kunden_email($beleg) {
		$sql=sprintf("SELECT extended_user_email FROM %s
						WHERE extended_user_name LIKE '%s'
						AND extended_user_vorname LIKE '%s' ",
			$this->cms->tbname['plugin_shop_crm_extended_user'],
			$this->db->escape($beleg['kunden_order_name']),
			$this->db->escape($beleg['kunden_order_vorname'])
		);
		return $this->db->get_var($sql);
	}

	/**
	 * @return mixed
	 */
	private function get_versender_mail() {
		$template = $this->get_mail_template(gutschein::MAIL_TEMPLATE_ID);
		return $template['mail_ersand_ail_dresse'];
	}

	/**
	 * @param $produkte
	 * @return string[]
	 */
	private function get_herausgeber_mails($produkte) {
		$herausgeber_mails = array();
		foreach  ($produkte as $produktid => $produkt) {
			$herausgeber_mail = $produkt['produkte_lang_gutschein_herausgeber_email'];
			if ($herausgeber_mail) {
				$herausgeber_mails[] = $herausgeber_mail;
			}
		}
		// Admin-E-Mail-Adresse anhängen
		if (!in_array($this->cms->admin_email, $herausgeber_mails, true)) {
			$herausgeber_mails[] = $this->cms->admin_email;
		}
		return $herausgeber_mails;
	}

	/**
	 * @param array $produkt
	 * @return string
	 */
	private function get_herausgeber_mail($produkt) {
		return !empty($produkt['produkte_lang_gutschein_herausgeber_email']) ? $produkt['produkte_lang_gutschein_herausgeber_email'] : $this->cms->admin_email;
	}

	/**
	 * gutschein::mail_gutschein(...)
	 *
	 * @param array $beleg Beleg-/Kundendaten
	 * @param array $produkte Array der Produktdaten
	 * @param bool $liefer_html_template Ob das Liefer-HTML-Template verwendet werden soll
	 * @param string $empfaenger Empfänger-E-Mail-Adresse
	 * @param string $empfaenger_name
	 * @param array $cc CC-Empfänger (optional)
	 * @return bool Erfolg des Versands
	 */
	function mail_gutschein($beleg, $produkte, $liefer_html_template, $empfaenger, $empfaenger_name='', $cc=array())
	{
		if (!$empfaenger) {
			return false;
		}

		if ($liefer_html_template) {
			if (!$beleg[gutschein::LIEFER_EMAIL_COLUMN]) {
				return false;
			}
		}

		// Die Daten des E-Mail-Templates rausholen
		$template = $this->get_mail_template(gutschein::MAIL_TEMPLATE_ID);
		if ($liefer_html_template) {
			$is_html=true;
			$text = $template['mail__nhalt_der_ail_'];
		}
		else {
			$is_html=false;
			$text = $template['mail_ext_nhalt_der_ail_'];
		}

		// Ersetzung durchführen
		$text = $this->fill_template($text, $beleg, $produkte, $is_html);

		// E-Mail erstellen
		require_once(PAPOO_ABS_PFAD."/lib/classes/class.phpmailer.php");
		$mail2 = new PHPMailer();
		$mail2->CharSet = 'UTF-8';
		$mail2->SetLanguage($this->cms->lang_short);
		$mail2->IsHTML($is_html);
		//$mail2->Priority = 5;
		$mail2->Subject = $template['mail_etreff_der_ail'];

		// Empfänger setzen
		$mail2->AddAddress($empfaenger, $empfaenger_name);
		foreach ($cc as $cc_addr) {
			$mail2->AddCC($cc_addr);
		}

		// Absender setzen
		$mail2->From = $template['mail_ersand_ail_dresse'];
		$mail2->FromName = $template['mail_ame_zur_ail'];
		$mail2->Sender = $template['mail_ersand_ail_dresse'];

		//Attachements anhängen
		foreach ($produkte as $produkt) {
			$mail2->AddAttachment($produkt['filename'], $this->ascii_filename($produkt['produkte_order_produktname']).'_'.$produkt['produkte_lang_externeroduktid'].$produkt['gutschein_number'].'.pdf', 'base64', 'application/pdf');
		}

		// Inhalt setzen
		$mail2->Encoding = '8bit';
		$mail2->Body = $text;

		// Email senden
		$ok=$mail2->Send();

		//Mailversuch loggen
		$this->diverse->do_log("email", array(
			array('typ' => "email", 'mailto' => $empfaenger, 'mailcc' => implode(', ',$cc), 'mailfrom' => $mail2->From, 'mailfromtext' => '', 'emailfromname' => $mail2->FromName, 'emailsubject' => $mail2->Subject, 'emailerror' => $mail2->ErrorInfo, 'emailsend' => ($ok)?'ok':'not ok')
		));

		if ($ok || empty($mail2->ErrorInfo)) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * gutschein::create_pdf()
	 *
	 * @param $beleg
	 * @param $produkt
	 * @param bool $renew
	 * @return void
	 */
	function create_pdf($beleg, $produkt, $renew=false)
	{
		$data = array_merge($beleg, $produkt);
		$filename=PAPOO_ABS_PFAD."/plugins/papoo_shop/files/secure/gutschein_".$beleg['order_order_number'].'_'.$data['produkte_order_produkt_id'].".pdf";
		if (!$renew and file_exists($filename)) {
			return $filename;
		}
		//Zuerst das HTML fertigstellen
		$html=$this->create_html($data);
		//Dann das PDF
		$filename=$this->create_pdf_now($data,$html);
		return $filename;

	}

	/**
	 * gutschein::create_html()
	 *
	 * @param $data
	 * @return mixed|string|null
	 */
	function create_html($data)
	{
		//Zuerst die Mail Daten rausholen
		$sql=sprintf("SELECT mail__okument_nhalt FROM %s
						WHERE mail_id=11
						AND mail_lang_id=1",
			$this->cms->tbname['plugin_shop_mailing']
		);
		$html=$this->db->get_var($sql);

		//$html=file_get_contents(PAPOO_ABS_PFAD."/plugins/gutschein/templates/vorlage.html");

		// Umbrüche in der Beschreibung
		$data["produkte_order_produktdescription"] = str_replace("\n", '<br />', $data["produkte_order_produktdescription"]);

		//Ersetzung durchführen
		$html = $this->fill_template_simple($html, $data, true, false);

		return $html;
	}

	/**
	 * gutschein::create_pdf_now()
	 *
	 * @param $data
	 * @param mixed $html
	 * @return string
	 */
	function create_pdf_now($data,$html)
	{
		//Die PDF Vorlage rausholen
		$sql=sprintf("SELECT produkte_lang_produktvideo FROM %s
									WHERE produkte_lang_id='%d'
									AND produkte_lang_lang_id='1'",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$data['produkte_order_produkt_id']
		);
		$file_dat=$this->db->get_var($sql);

		//Daten direkt ausgeben.
		$file=PAPOO_ABS_PFAD."/plugins/papoo_shop/files/normal/".$file_dat;

		$this->settings[0]['ext_search_pdf_template_file']=trim($this->settings[0]['ext_search_pdf_template_file']);
		//Damit diverse depreceated Meldungen nicht kommen.
		$errs = error_reporting();
		error_reporting($errs & ~E_DEPRECATED);
		if (file_exists($file) && is_file($file) && !is_dir($file)) {
			$display_errors = ini_get('display_errors');
			ini_set('display_errors', false);

			// Standard Font Directory von Mpdf
			$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
			$fontDirs = $defaultConfig['fontDir'];

			// Standard Fonts von Mpdf
			$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
			$fontData = $defaultFontConfig['fontdata'];

			// Extra Fonts für Mpdf hinzufügen
			$fonts = [
				'fontDir' => array_merge($fontDirs, [ // fontDir array zum setzen von Font Directories (Standard Directory wird behalten)
					PAPOO_ABS_PFAD . '/lib/fonts/mpdf_ttfonts_extra/gutschein', // Directory für extra Fonts
				]),
				'fontdata' => $fontData + [
						'anonymous' => [
							'R' => 'Anonymous-webfont.ttf',
						],
						'arial' => [
							'R' => 'arial.ttf',
							'B' => 'arialbd.ttf',
						],
						'bergamostd' => [
							'R' => 'BergamoStd-Regular-webfont.ttf',
						],
						'colabbol' => [
							'R' => 'ColabBol-webfont.ttf',
						],
						'colablig' => [
							'R' => 'ColabLig-webfont.ttf',
						],
						'colabmed' => [
							'R' => 'ColabMed-webfont.ttf',
						],
						'colabreg' => [
							'R' => 'ColabReg-webfont.ttf',
						],
						'colabthi' => [
							'R' => 'ColabThi-webfont.ttf',
						],
						'cour' => [
							'R' => 'cour.ttf',
						],
						'dejavusans-extralight' => [
							'R' => 'DejaVuSans-ExtraLight.ttf',
						],
						'genbaser' => [
							'R' => 'GenBasR-webfont.ttf',
						],
						'itcedscr' => [
							'R' => 'ITCEDSCR.ttf',
						],
						'journal' => [
							'R' => 'journal.ttf',
						],
						'kalocsai_flowers' => [
							'R' => 'Kalocsai_Flowers.ttf',
						],
						'learningcurve_ot' => [
							'R' => 'LearningCurve_OT-webfont.ttf',
						],
						'mangal' => [
							'R' => 'mangal.ttf',
						],
						'norasi' => [
							'R' => 'Norasi.ttf',
							'B' => 'Norasi-Bold.ttf',
							'I' => 'Norasi-Oblique.ttf',
							'BI' => 'Norasi-BoldOblique.ttf',
						],
						'note_this' => [
							'R' => 'Note_this-webfont.ttf',
						],
						'pecita' => [
							'R' => 'Pecita-webfont.ttf',
						],
						'sf_cartoonist_hand' => [
							'R' => 'SF_Cartoonist-webfont.ttf',
							'B' => 'SF_Cartoonist_Bold-webfont.ttf',
							'I' => 'SF_Cartoonist_Italic-webfont.ttf',
							'BI' => 'SF_Cartoonist_Bold_Italic-webfont.ttf',
						],
						'sf_cartoonist_hand_SC' => [
							'R' => 'SF_Cartoonist_SC-webfont.ttf',
							'B' => 'SF_Cartoonist_SC_Bold-webfont.ttf',
							'I' => 'SF_Cartoonist_SC_Italic-webfont.ttf',
							'BI' => 'SF_Cartoonist_SC_Bold_Italic-webfont.ttf',
						],
						'tangerine' => [
							'R' => 'Tangerine_Regular-webfont.ttf',
							'B' => 'Tangerine_Bold-webfont.ttf',
						],
						'times' => [
							'R' => 'times.ttf',
							'B' => 'timesbd.ttf',
						],
						'verdana' => [
							'R' => 'verdana.ttf',
						],
					],
			];

			// Config für die PDF Ausgabe
			$config = [
				'tempDir' => rtrim(PAPOO_ABS_PFAD, '/').'/cache/mpdf',
				"mode" => "utf-8",
				"format" => "A4",
				"ignore_invalid_utf8" => true,
				// Fonts verfügbar machen
				$fonts,
			];

			$mpdf = new \Mpdf\Mpdf($config);

			$mpdf->SetImportUse();
			$mpdf->SetSourceFile($file);

			$tplId = $mpdf->ImportPage(1);
			#exit();
			$mpdf->UseTemplate($tplId);

			$mpdf->SetPageTemplate($tplId);
			$mpdf->WriteHTML($html);

			#$mpdf->SetDisplayMode('fullpage');
			$filename=PAPOO_ABS_PFAD."/plugins/papoo_shop/files/secure/gutschein_".$data['order_order_number'].'_'.$data['produkte_order_produkt_id'].".pdf";
			//	$mpdf->Output($filename,"I");//Direktausgabe in Browser
			$mpdf->Output($filename,"F");//F
			ini_set('display_errors', $display_errors);

		}
		else {
			echo "Vorlage-PDF \"$file\" nicht gefunden! Vorgang wird abgebrochen.";
			die();
		}
		error_reporting($errs);
		return $filename;
	}

	/**
	 * gutschein::fill_template(...)
	 * Fülle ein Template mit den Daten einer Bestellung und denen der Produkte.
	 *
	 * @param string $template Zu füllendes Template
	 * @param array $data Bestellungsdaten
	 * @param array $produktdaten Array der Produkte
	 * @param bool $is_html
	 * @return string Gefülltes Template
	 */
	function fill_template($template, $data, $produktdaten=array(), $is_html=false)
	{
		// Ersetzung der Hauptdaten durchführen
		$template = $this->fill_template_simple($template, $data, true, false, $is_html);

		// Suche #shopping_cart_START# und _STOP
		$start = strpos($template, '#shopping_cart_START#');
		$stop = strpos($template, '#shopping_cart_STOP#', $start);
		// Ersetzung der Produktdaten
		if ($start !== FALSE and $stop !== FALSE) {
			$warenkorb = array();
			$subtemplate = substr($template, $start+strlen('#shopping_cart_START#'), $stop-$start-strlen('#shopping_cart_START#'));
			foreach ($produktdaten as $produkt) {
				$warenkorb[] = $this->fill_template_simple($subtemplate, $produkt, false, true, $is_html);
			}
			$template = substr_replace($template, implode('',$warenkorb), $start, $stop-$start+strlen('#shopping_cart_STOP#'));
		}

		// Rückwärtskompatibilität: Ersetze einige übrig gebliebene Tags mit denen vom 1. Produkt.
		if (!empty($produktdaten[0])) {
			$template = $this->fill_template_simple($template, $produktdaten[0], false, false, $is_html);
		}
		return $template;
	}

	/**
	 * gutschein::fill_template_simple(...)
	 * Fülle ein Template mit den Daten eines assoziativen Arrays
	 *
	 * @param string $template Zu füllendes Template
	 * @param array $data Daten
	 * @param bool $replace_kunden_order ob 'kunden_order' in Keys zu 'extended_user' umgeschrieben werden soll
	 * @param bool $replace_produkte_order ob 'produkte_order' in Keys zu 'produkte' umgeschrieben werden soll
	 * @param bool $is_html
	 * @return string Gefülltes Template
	 */
	protected function fill_template_simple($template, $data, $replace_kunden_order=false, $replace_produkte_order=false, $is_html=false)
	{
		foreach ($data as $key=>$value) {
			if (strpos($value, 'nobr:') === 0) {
				$value = substr($value, 5);
			}
			if ($key=="order_summe_brutto" or $key=="produkte_order_produkt_brutto_price") {
				$value=number_format($value, 2, ',', ' ');
			}
			else if ($key=="order_order_date") {
				$value=date("d.m.Y",$value);
			}

			if ($is_html) {
				$value = str_replace("\n", "<br />\n", htmlspecialchars($value));

			}

			if ($replace_kunden_order) {
				$key=str_replace('kunden_order','extended_user',$key);
			}
			if ($replace_produkte_order) {
				$key=str_replace("produkte_order","produkte",$key);
			}
			$template=str_replace("#".$key."#",$value,$template);
		}
		return $template;
	}

	/**
	 * gutschein::get_order(...)
	 *
	 * Holt die Bestellung mit der angegebenen ID aus der Datenbank
	 *
	 * @param int $id
	 * @return array Array mit Informationen zur Bestellung und zum Kunden oder NULL
	 */
	function get_order($id)
	{
		$sql=sprintf("SELECT * FROM %s
							LEFT JOIN %s ON order_id = kunden_order_id
							WHERE order_id=%d
							LIMIT 1",
			$this->cms->tbname['plugin_shop_order'],
			$this->cms->tbname['plugin_shop_order_lookup_kunde'],
			(int)$id
		);
		$result = $this->db->get_results($sql,ARRAY_A);
		if (!$result) {
			return NULL;
		}
		else {
			return $result[0];
		}
	}

	/**
	 * gutschein::get_last_order(...)
	 *
	 * Holt die letztgetätigte Bestellung aus der Datenbank
	 *
	 * @return array Array mit Informationen zur Bestellung und zum Kunden oder NULL
	 */
	function get_last_order()
	{
		//Dann den letzten Beleg rausholen
		$sql=sprintf("SELECT * FROM %s
								LEFT JOIN %s ON order_id = kunden_order_id
								ORDER BY order_id DESC
								LIMIT 1",
			$this->cms->tbname['plugin_shop_order'],
			$this->cms->tbname['plugin_shop_order_lookup_kunde']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!$result) {
			return NULL;
		}
		else {
			return $result[0];
		}
	}

	/**
	 * gutschein::get_order_products(...)
	 *
	 * Holt die Produktdaten zu der Bestellung mit der angegebenen ID aus der Datenbank
	 *
	 * @param int $id
	 * @return array Array der Produktdaten zu der angegebenen Order-ID. Leeres Array bei ungültiger ID.
	 */
	function get_order_products($id) {
		$sql=sprintf("SELECT * FROM %s LEFT JOIN %s ON produkte_order_produkt_id = produkte_lang_id
									LEFT JOIN %s ON gutschein_shop_id=produkte_order_id
									AND gutschein_product_id=produkte_order_produkt_id
								WHERE produkte_order_id=%d
								ORDER BY produkte_order_produkt_id ASC",
			$this->cms->tbname['plugin_shop_order_lookup_produkte'],
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->cms->tbname['plugin_gutschein_nummern'],
			(int)$id
		);
		$result = $this->db->get_results($sql,ARRAY_A);

		foreach ($result as &$item) {
			$sql = sprintf('SELECT COUNT(*) FROM %s WHERE produkte_order_id < %d AND produkte_order_produkt_id = %d',
				$this->cms->tbname['plugin_shop_order_lookup_produkte'],
				(int)$id,
				(int)$item['produkte_order_produkt_id']);
			$item['produkte_order_nummer'] = sprintf("%03d",(int)$this->db->get_var($sql));
		}
		if (!$result) {
			return array();
		}
		else {
			return $result;
		}
	}

	/**
	 * gutschein::get_mail_template(...)
	 * Holt eine E-Mail-Vorlage aus dem Shop
	 *
	 * @param int $id Sprache (Default: 1 (Deutsch))
	 * @param int $lang
	 * @return array Daten der Vorlage
	 */
	function get_mail_template($id, $lang=1)
	{
		$sql=sprintf("SELECT * FROM %s
							WHERE mail_id=%d
							AND mail_lang_id=%d",
			$this->cms->tbname['plugin_shop_mailing'],
			(int)$id,
			(int)$lang
		);
		$result = $this->db->get_results($sql,ARRAY_A);
		if (!$result) {
			return NULL;
		}
		else {
			return $result[0];
		}
	}

	/**
	 * @param $id
	 * @return bool
	 */
	function get_gutschein_versandt($id)
	{
		$sql = sprintf("SELECT gutschein_is_send FROM %s WHERE gutschein_shop_id = %d",
			$this->cms->tbname['plugin_gutschein'],
			$id);
		$result = $this->db->get_var($sql);
		return $result == 1;
	}

	/**
	 * @param $id
	 * @return bool
	 */
	function has_gutschein_info($id)
	{
		$sql = sprintf("SELECT COUNT(*) FROM %s WHERE gutschein_shop_id = %d",
			$this->cms->tbname['plugin_gutschein'],
			$id);
		return $this->db->get_var($sql) > 0;
	}

	/**
	 * @param $id
	 * @param $versandt
	 */
	function set_gutschein_info($id, $versandt)
	{
		$exists = $this->has_gutschein_info($id);
		$sql = sprintf(
			($exists)
				?("UPDATE %s SET gutschein_is_send=%d WHERE gutschein_shop_id = %d")
				:("INSERT INTO %s (gutschein_is_send, gutschein_shop_id) VALUES (%d, %d)"),
			$this->cms->tbname['plugin_gutschein'],
			(int)$versandt,
			(int)$id);
		$this->db->query($sql);

	}

	/**
	 * @param $order_id
	 * @param $product_id
	 * @param $number
	 */
	function set_gutschein_number($order_id, $product_id, $number)
	{
		$sql = sprintf(
			"INSERT INTO %s (gutschein_shop_id, gutschein_product_id, gutschein_number) VALUES (%d, %d, %d)",
			$this->cms->tbname['plugin_gutschein_nummern'],
			(int)$order_id,
			(int)$product_id,
			(int)$number);
		$this->db->query($sql);
	}

	/**
	 * @param $order_id
	 * @param $product_id
	 * @return array|null
	 */
	function get_gutschein_number($order_id, $product_id)
	{
		$sql = sprintf(
			"SELECT gutschein_number FROM %s WHERE gutschein_shop_id = %d AND gutschein_product_id = %d LIMIT 1",
			$this->cms->tbname['plugin_gutschein_nummern'],
			(int)$order_id,
			(int)$product_id);
		return $this->db->get_var($sql);
	}

	/**
	 * gutschein::reload()
	 *
	 * Lädt das Gutschein-Backend neu mit angehängter Aktion.
	 * Die Funktion kehrt nicht zurück.
	 *
	 * @param integer $action
	 * @return void
	 */
	function reload($action=0)
	{
		$self=$_SERVER['PHP_SELF'];

		$url="menuid=".$this->checked->menuid."&template=gutschein/templates/gutschein_back.html&send=".$action;

		$location_url = $self . "?" . $url;
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . htmlspecialchars($location_url) . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header('HTTP/1.1 303 See Other');
			header("Location: $location_url");
		}
		exit;

	}

	/**
	 * @param $template
	 */
	function redirect($template)
	{
		$self=$_SERVER['PHP_SELF'];

		$url="menuid=".$this->checked->menuid.'&'.$template;

		$location_url = $self . "?" . $url;
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . htmlspecialchars($location_url) . '">' .
				$this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header('HTTP/1.1 303 See Other');
			header("Location: $location_url");
		}
		exit;
	}

	/**
	 * @param $text
	 * @return string|string[]|null
	 */
	function ascii_filename($text)
	{
		if (strpos($text, 'nobr:') === 0) {
			$text = substr($text, 5);
		}
		$text = str_replace('ä','ae',$text);
		$text = str_replace('ö','oe',$text);
		$text = str_replace('ü','ue',$text);
		$text = str_replace('Ä','Ae',$text);
		$text = str_replace('Ö','Oe',$text);
		$text = str_replace('Ü','Ue',$text);
		$text = str_replace('ß','ss',$text);
		$text = str_replace(' ','_',$text);
		return preg_replace('/[^A-Za-z0-9_.-]/', '', $text);
	}
}

$gutschein = new gutschein();

/** Daten in array data
 *
 *  [order_id] => 4
[order_lang] => 1
[order_order_number] => 20401383
[order_oid_org] => 0
[order_agb_ok] =>
[order_ip] => 127.0.0.1
[order_status] => 0
[order_admin_user] => 10
[order_last_change] => 1315483226
[order_summe_netto_versandkosten] =>
[order_summe_netto] => 16.55
[order_summe_steuer] => 19
[order_summe_brutto] => 19.69450000000000144951
[order_summe_rabatt] =>
[order_order_date] => 1315483226
[order_currency] =>
[order_origin] => FRONTEND
[order_origin_search_keyword] =>
[order_kommentar] =>
[order_typ] => 7
[order_is_payd] => 1
[order_abo_naechster_beleg_datum] => 0
[order_abo_letzter_beleg_datum] => 0
[order_hythmus_eleg] => 0
[order_rsteerstellung] =>
[order_next_mahn] => 0
[kunden_order_id] => 4
[kunden_order_payment] => 3
[kunden_order_versand] => 5
[kunden_order_user_id] => 1
[kunden_order_name] => Euwens
[kunden_order_vorname] => Carsten
[kunden_order_strasse_liefer_adresse] =>
[kunden_order_str_nr_re_adresse] => Burbacherstr. 231
[kunden_order_versandart_der_letzten_bestellung] => 5
[kunden_order_bezahl_art_letzt_bestellung] => 3
[kunden_order_postleitzahl] => 53129
[kunden_order_readr_ort] => Bonn
[kunden_order_read_firma] => Papoo Software
[kunden_order_liefer_vorname] =>
[kunden_order_liefer_str_nr] =>
[kunden_order_liefer_plz] =>
[kunden_order_liefer_ort] =>
[kunden_order_liefer_firma] =>
[kunden_order_telefon] =>
[kunden_order_fax] =>
[kunden_order_mobil] =>
[kunden_order_ohne_ust] =>
[kunden_order_benutzername] =>
[kunden_order_land] => 34
[kunden_order_email] => info@papoo.de
[kunden_order_re_gleich_liefer] => 1
[kunden_order_password] =>
[kunden_order_aktiv] => 1
[kunden_order_newsletter] =>
[kunden_order_antwortmail] =>
[kunden_order_kundengruppe] =>
[kunden_order_] =>
[kunden_order_testtest] =>
[kunden_order_dfasdfa] =>
[kunden_order_sdfsdf] =>
[kunden_order_sdfasdf] =>
[kunden_order_trewetr] =>
[kunden_order_retzertz] =>
[kunden_order_ewrtwert] =>
[kunden_order_dsfgsdfg] =>
[kunden_order_dsfasdfasdf] =>
[kunden_order_gdsfgsdfg] =>
[kunden_order_dsfgdsfg] =>
[kunden_order_order_id_export] =>
[kunden_order_exportname_feld] =>
[kunden_order_feld_exportieren] =>
[kunden_order_customers_vat_id] =>
[kunden_order_anrede] => 0
[kunden_order_geburtsdatum] =>
[kunden_order_kontonummer] => 104413521
[kunden_order_bankleitzahl_blz] => 37050198
[kunden_order_name_der_bank] => 12341234
[kunden_order_konto_inhaber] => 234
[kunden_order_konto_verifiziert] => 1
[produkte_order_id] => 4
[produkte_order_produkt_id] => 1
[produkte_order_produktname] => Gew�rzmischung 1
[produkte_order_produktdescription] => F�r orientalische Gerichte. Zutaten: Muskatnuss. schwarzer Pfeffer. Koriander. Gew�rznelken. Zimt. Kardamon. Paprika edels�ss. Chilli aus kontrolliert  biologischem Anbau.
[produkte_order_produkt_netto_price] => 16.55
[produkte_order_produkt_netto_price_org] => 16.55
[produkte_order_produkt_brutto_price] => 19.69450000000000144951
[produkte_order_produkt_anzahl] => 1
[produkte_order_produkt_summe_netto] => 16.55
[produkte_order_produkt_rabatt] => 0
[produkte_order_steuerklasse] => 1
[produkte_order_sortier_id] => 1
 * */
