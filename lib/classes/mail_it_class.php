<?php
/**
######################################
# Papoo CMS                          #
# (c) Dr. Carsten Euwens 2008        #
# Authors: Carsten Euwens            #
# http://www.papoo.de                #
# Internet                           #
######################################
# PHP Version 4.2                    #
######################################
 */

/**
 * Diese Klasse initialisert einen Benutzer, mit do_einlogg werden die Daten mit der Datenbank abgeglichen.
 *
 * Class mail_it
 */
class mail_it
{
	/** @var string Adresse to */
	var $to = "";
	/** @var string Sender Adresse */
	var $from = "";
	/** @var string Sender Adresse Name */
	var $from_text = "";
	/** @var string CC Adresse */
	var $cc = "";
	/** @var string Subject */
	var $subject = "";
	/** @var string Inhalt = body */
	var $body = "";
	/** @var string Priorität */
	var $priority = "3";
	/** @var string|array Attachement */
	var $attach = "";
	/** @var string Antwort an = replyto */
	var $replyto = "";
	/** @var string Formular anzeigen für Kontakt */
	var $formular = "";
	/** @var string geschickt oder nicht */
	var $geschickt = "";
	/** @var string Encoding (bei Newsletter quoted-printable, sonst 8bit) */
	var $Encoding = "8bit";

	/**
	 * mail_it constructor.
	 */
	function __construct()
	{
		/**
		Klassen und Variablen globalisieren
		 */
		// cms Klasse einbinden
		global $cms;
		$this->cms = & $cms;

		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = & $db;

		global $content;
		$this->content = & $content;

		global $checked;
		$this->checked = & $checked;

		// Diverse Klasse einbinden
		global $diverse;
		$this->diverse = & $diverse;

		// Blacklist Klasse einbinden
		global $blacklist;
		$this->blacklist = & $blacklist;
	}

	/**
	 * Hiermit wird aus dem Forum eine Mail an den Benutzer gesendet,
	 * auf dessen Nachricht geantwortet wurde, so der User es denn will
	 *
	 * @param $elterid
	 * @param $rootid
	 * @param $mesid
	 * @param $forumid
	 * @param $menuid
	 * @param $title
	 * @return void
	 *
	 * @throws phpmailerException
	 */
	function send_answer_forum($elterid, $rootid, $mesid, $forumid, $menuid, $title)
	{
		// Userid und Thema der Nachricht raussuchen, auf die geantwortet wurde
		$elterid=$this->db->escape($elterid);
		$resultu = $this->db->get_results("SELECT userid, thema FROM ".$this->cms->papoo_message." WHERE msgid=$elterid LIMIT 1");
		// Für den Eintrag durchgehen
		foreach ($resultu as $row) {
			$mailuserid = $this->db->escape($row->userid);
			$mailthema = $this->db->escape($row->thema);
		}
		IfNotSetNull($mailuserid);
		// Zur Userid den Usernamen mit Email raussuchen
		$resultb = $this->db->get_results("SELECT username, email, antwortmail FROM ".$this->cms->papoo_user." WHERE userid='$mailuserid' LIMIT 1");
		// Daten zuweisen
		foreach ($resultb as $row) {
			// $mailusername = $row->username;
			$mailemail = $row->email;
			$mailja_nein = $row->antwortmail;
		}
		# Wenn eine Antwortmail erwünscht ist ... eine senden ... ###
		IfNotSetNull($mailja_nein);
		IfNotSetNull($mailemail);
		IfNotSetNull($mailthema);
		if ($mailja_nein == 1) {
			$link = "http://".str_replace("//", "/", $title."/forumthread.php?rootid=".$rootid."&msgid=".$mesid."&forumid=".$forumid."&menuid=".$menuid);
			$this->content->template['message_2279']=str_ireplace('#thema#',$mailthema,$this->content->template['message_2279']);
			$this->content->template['message_2279']=str_ireplace('#link#',$link,$this->content->template['message_2279']);

			$body =$this->content->template['message_2279'];

			$this->to = $mailemail;
			$this->from = $this->cms->admin_email;
			$this->from_text = "Forum ".$title;
			$this->subject = $this->content->template['message_2280'].$title;
			$this->body = $body;
			// niedrigste Priorität
			$this->priority = 3;
			$this->do_mail();
		}
	}

	/**
	 * Diese Methode übernimmt Daten aus einer anderen Klasse und wandelt diese in Varibalen zum senden um.
	 * Anschließend wird das mailen ausgelöst.
	 * Gibt zurück ob es geklappt hat oder nicht.
	 *
	 * @param $menuid
	 * @param $reporeid
	 * @param $title
	 * @param $commentemail
	 * @param $mailempfang
	 * @param $artikel
	 * @return string|void Mail senden auslösen ja/nein
	 *
	 * @throws phpmailerException
	 */
	function send_mail($menuid, $reporeid, $title, $commentemail, $mailempfang, $artikel)
	{
		// den Titel richtig Übergeben
		$title = $this->cms->title;
		// HTML rausnehmen.
		//$artikel = strip_tags($artikel);
		// Daten zuweisen
		//$this->from = $commentemail;
		$this->from = $this->cms->admin_email;

		//$this->from_text = $commentemail;
		$this->to = $mailempfang;
		$this->cc = "";
		$this->subject = "Empfehlung eines Artikels.";

		$link = "http://".str_replace("//", "/", $title."".PAPOO_WEB_PFAD."/index.php?menuid=".$menuid."&reporeid=".$reporeid);
		$this->body = "Ihnen wurde ein Artikel von ".$commentemail." empfohlen. \n\nUeber diesen Link gelangen sie dorthin: ".$link."\n\n\n".$artikel;
		// Übermitteltes mail Ergebniss zurückgeben ja/nein geklappt
		return $this->do_mail();
	}

	/**
	 * Diese Methode sendet die Mail, und gibt zurück ob es geklappt hat oder nicht.
	 * @param array $smtpSettings Optionale eigene SMTP-Einstellungen. Folgende Schlüssel müssen im Array vorhanden sein:
	 *  settingsType: Welcher Mail-Type verwendet werden soll: system (Systemeinstellungen), smtp (Eigene SMTP-Einstellungen), sendmail (Kein SMTP)
	 *  smtp_host: Der SMTP Host
	 *  smtp_port: Der SMTP Port
	 *  smtp_user: Der SMTP Benutzer
	 *  smtp_pass: Das SMTP Passwort
	 * @return string|void
	 *
	 * @throws phpmailerException
	 */
	function do_mail(array $smtpSettings = [])
	{
		if (!empty($this->from_text)) {
			$this->from_text="";
		}
		// Email Adressen Überprüfen
		if (!empty ($this->from)) {
			$true1 = $this->validateEmail($this->from);
		}
		if (!empty ($this->to)) {
			$true2 = $this->validateEmail($this->to);
		}
		// Email Adressen ok, dann senden...
		IfNotSetNull($true1);
		IfNotSetNull($true2);
		if ($true1 == true and $true2 == true) {
			// Neue mail Klasse initialisieren
			require_once(PAPOO_ABS_PFAD."/lib/classes/class.phpmailer.php");
			require_once(PAPOO_ABS_PFAD."/lib/classes/class.smtp.php");
			$mail = new PHPMailer();

			if($smtpSettings['settingsType'] == 'system' || empty($smtpSettings)) {
				$smtpSettings = $this->cms->getSmtpConfig();
			}

			if($smtpSettings['settingsType'] == 'sendmail') {
				$mail->SMTPAuth   = false;
			}
			elseif ($smtpSettings['settingsType'] == 'smtp') {
				$mail->IsSMTP();
				$mail->Host       = $smtpSettings['host'];
				$mail->SMTPAuth   = true;
				$mail->Username   = $smtpSettings['user'];
				$mail->Password   = $smtpSettings['password'];
				$mail->SMTPSecure = "tls";
				$mail->Port       = $smtpSettings['port'];
			}

			$mail->CharSet = "utf-8";
			//$mail->IsMail(); // ???
			// Daten Übergeben
			$mail->AddAddress($this->to, $this->to);
			#$mail->cc = $this->cc;
			//Wenn ein Attachment angegeben wird
			if (!empty($this->attach)) {
				foreach ($this->attach as $attach) {
					$mail->AddAttachment($attach);
				}
			}

			$mail->From = $this->from;
			if (!empty($this->from_text)) {
				$mail->Sender = $this->from_text;
			}
			else {
				$mail->Sender = $this->from;
			}

			if (!empty($this->from_textx)) {
				$mail->FromName = $this->from_textx;
			}
			else {
				$mail->FromName = $this->from_text;
			}
			//$mail->language=0;

			$mail->SetLanguage($this->cms->lang_short, PAPOO_ABS_PFAD."/lib/classes/language/");

			$mail->IsHTML(false);
			$mail->Subject = "=?UTF-8?Q?" . quoted_printable_encode($this->subject) . "?=";
			//Wenn HTML gesendet wird
			if (!empty($this->body_html)) {
				$mail->Encoding = $this->Encoding;
				$mail->Body = $this->body_html;
				$mail->IsHTML(true);
				$mail->AltBody=$this->body;
			}
			//Nur Text
			else {
				$mail->Encoding = '8bit';
				$mail->Body = $this->body;
			}

			IfNotSetNull($this->ReplyTo);
			#$mail->ReplyTo = $this->ReplyTo;
			if (is_array($this->ReplyTo)) {
				foreach ($this->ReplyTo as $key=>$value) {
					$mail->AddReplyTo($key,$value);
				}
			}

			$mail->Priority = $this->priority;

			// Email senden
			if (!$mail->Send()) {
				$this->error= "Fehler, Mail wurde nicht verschickt. Error: ".$mail->ErrorInfo;
				$this->mail_log();
			}
			else {
				//ok oder nicht?
				$this->error="gesendet";
				$this->ok = "ok";
				$this->mail_log();
				// Alles ok, zurückgeben
				return $this->ok;
			}
		}
		else {
			//ok oder nicht?
			$this->error="Nicht gesendet";
			$this->ok = "not_ok";
			$this->mail_log();
			// Nicht ok, zurückgeben
			return $this->ok;
		}
	}

	/**
	 * Erstellt eine LOg für eine versendete Mail
	 */
	function mail_log()
	{
		//Mailversuch loggen
		$logdetails= array ();

		IfNotSetNull($this->ok);
		IfNotSetNull($this->from_textx);
		IfNotSetNull($this->ReplyTo);

		array_push($logdetails, array (
			'typ' => "email",
			'mailto' => $this->to,
			'mailcc' => $this->cc,
			'mailfrom' => $this->from,
			'mailfromtext' => $this->from_text,
			'emailfromname' => $this->from_textx,
			'emailsubject' => $this->subject,
			'emailbody' => $this->body,
			'emailerror' => $this->error,
			'emailsend' => $this->ok,
		));
		$this->diverse->do_log("email", $logdetails);
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

	/**
	 * Hiermit wird das Kontaktformular übermittelt
	 *
	 * @return void
	 *
	 * @throws phpmailerException
	 */
	function kontakt()
	{
		$this->content->template['menuid_aktuell']=$this->checked->menuid;

		if ($this->blacklist->do_blacklist($this->checked->mitteilung)!="not_ok") {
			// Eingetragende Daten als eMail verschicken
			if (!empty($this->checked->kontakt_senden) and !empty($this->checked->email)and !empty($this->checked->nachname)and !empty($this->checked->mitteilung)) {
				$body = "Kontaktformular der Seite ".$this->cms->title.
					"\n\nNachricht von: ".$this->checked->nachname.
					"\nEmail: ".$this->checked->email.
					"\n\nInhalt der Nachricht: ".$this->checked->mitteilung;

				$this->to = $this->cms->admin_email;
				//$this->from = $this->checked->email;
				$this->from = $this->cms->admin_email;
				$true1="";

				if (!empty ($this->from)) {
					$true1 = $this->validateEmail($this->from)."w";
				}

				if ($true1!=1) {
					$this->from = $this->cms->admin_email;
				}

				$this->subject = "Nachricht aus dem Kontaktformular der Seite ".$this->cms->title;
				$this->body = $body;
				//$this->priority = 5;
				$this->do_mail();
				//submithome
				if (!empty($this->checked->submithome)) {
					$this->from = $this->cms->admin_email;
					$this->to=$this->checked->email;
					$this->do_mail();
				}
				//$_SESSION['geschickt'] = 1;
				//$this->geschickt = '1';
				$this->content->template['geschickt'] = '1';
			}
			// Formular anzeigen
			else {
				if (empty($this->checked->email) or empty($this->checked->nachname) or empty($this->checked->mitteilung)) {
					if  (!empty($this->checked->kontakt_senden)) {
						$this->content->template['fehler_kontakt'] = '1';
						// Eintragungen wieder an das Formular übergeben
						$this->content->template['kontaktform_name'] = $this->checked->nachname;
						$this->content->template['kontaktform_email'] = $this->checked->email;
						$this->content->template['kontaktform_text'] = $this->checked->mitteilung;
					}
				}
				$this->content->template['formular'] = '1';
			}
		}
		else {
			$this->content->template['geschickt'] = '1';
		}
	}
}

$mail_it = new mail_it();