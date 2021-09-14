DROP TABLE IF EXISTS `XXX_papoo_form_manager`; ##b_dump##
CREATE TABLE `XXX_papoo_form_manager` (
  `form_manager_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_manager_email` varchar(255) NOT NULL,
  `form_manager_name` varchar(255) NOT NULL,
  `form_manager_anzeig_select_email` varchar(255) NOT NULL,
  `form_manager_lang` varchar(255) NOT NULL,
  `form_manager_antwort_html` text NOT NULL,
  `form_manager_antwort_email` text NOT NULL,
  `form_manager_antwort_yn` varchar(255) NOT NULL,
  `form_manager_saleforce_yn` varchar(255) NOT NULL,
  `form_manager_saleforce_oid` varchar(255) NOT NULL,
  `form_manager_saleforce_returl` varchar(255) NOT NULL,
  `form_manager_saleforce_action` text NOT NULL,
  `form_manager_saleforce_debug` varchar(255) NOT NULL,
  `form_manager_saleforce_debug_email` varchar(255) NOT NULL,
  `form_manager_loesch_dat1` varchar(255) NOT NULL,
  `form_manager_loesch_dat2` varchar(255) NOT NULL,
  `form_manager_modul_id` varchar(255) NOT NULL,
  `form_manager_kategorie` int(11) NOT NULL,
  `form_manager_erstellt` int(11) NOT NULL,
  `form_manager_geaendert` int(11) NOT NULL,
  `form_manager_sender_mail` varchar(255) NOT NULL DEFAULT '',
  `form_manager_mail_settings_type` varchar(255) NOT NULL DEFAULT 'system',
  `form_manager_smtp_host` varchar(255) NOT NULL DEFAULT '',
  `form_manager_smtp_port` smallint UNSIGNED NOT NULL DEFAULT 0,
  `form_manager_smtp_user` varchar(255) NOT NULL DEFAULT '',
  `form_manager_smtp_pass` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`form_manager_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager` SET `form_manager_id`='2', `form_manager_email`='', `form_manager_name`='Kontakt', `form_manager_anzeig_select_email`='', `form_manager_lang`='', `form_manager_antwort_html`='', `form_manager_antwort_email`='', `form_manager_antwort_yn`='0', `form_manager_saleforce_yn`='', `form_manager_saleforce_oid`='', `form_manager_saleforce_returl`='', `form_manager_saleforce_action`='', `form_manager_saleforce_debug`='', `form_manager_saleforce_debug_email`='', `form_manager_loesch_dat1`='999', `form_manager_loesch_dat2`='999', `form_manager_modul_id`='93', `form_manager_kategorie`='0', `form_manager_erstellt`='0', `form_manager_geaendert`='1430743789', `form_manager_sender_mail`='test@kobos.de', `form_manager_mail_settings_type`='system', `form_manager_smtp_host`='', `form_manager_smtp_port`=0, `form_manager_smtp_user`='', `form_manager_smtp_pass`=''  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_form_manager_email`; ##b_dump##
CREATE TABLE `XXX_papoo_form_manager_email` (
  `form_manager_email_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_manager_email_form_id` int(11) NOT NULL,
  `form_manager_email_email` varchar(255) NOT NULL,
  `form_manager_email_name` varchar(255) NOT NULL,
  PRIMARY KEY (`form_manager_email_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_email` SET `form_manager_email_id`='3', `form_manager_email_form_id`='2', `form_manager_email_email`='test@kobos.de\r', `form_manager_email_name`='test'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_form_manager_lang`; ##b_dump##
CREATE TABLE `XXX_papoo_form_manager_lang` (
  `form_manager_id_id` int(11) NOT NULL DEFAULT '0',
  `form_manager_lang_id` varchar(255) NOT NULL,
  `form_manager_antwort_html` text NOT NULL,
  `form_manager_toptext_html` text NOT NULL,
  `form_manager_bottomtext_html` text NOT NULL,
  `form_manager_antwort_email_betreff` text NOT NULL,
  `form_manager_antwort_email` text NOT NULL,
  `form_manager_antwort_email_html` text NOT NULL,
  `form_manager_antwort_yn` varchar(255) NOT NULL,
  `form_manager_lang_button` varchar(255) NOT NULL,
  `mail_an_betreiber_betreff` text NOT NULL,
  `mail_an_betreiber_inhalt` text NOT NULL,
  `form_manager_text_html` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lang` SET `form_manager_id_id`='1', `form_manager_lang_id`='1', `form_manager_antwort_html`='', `form_manager_toptext_html`='', `form_manager_bottomtext_html`='', `form_manager_antwort_email_betreff`='', `form_manager_antwort_email`='*Vielen Dank für Ihre Anmeldung*\r\n\r\nVielen Dank für Ihre Anmeldung, wir werden uns in Kürze bei Ihnen melden.', `form_manager_antwort_email_html`='', `form_manager_antwort_yn`='', `form_manager_lang_button`='Absenden', `mail_an_betreiber_betreff`='', `mail_an_betreiber_inhalt`='', `form_manager_text_html`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lang` SET `form_manager_id_id`='3', `form_manager_lang_id`='1', `form_manager_antwort_html`='', `form_manager_toptext_html`='', `form_manager_bottomtext_html`='', `form_manager_antwort_email_betreff`='', `form_manager_antwort_email`='', `form_manager_antwort_email_html`='', `form_manager_antwort_yn`='', `form_manager_lang_button`='Absenden', `mail_an_betreiber_betreff`='', `mail_an_betreiber_inhalt`='', `form_manager_text_html`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lang` SET `form_manager_id_id`='2', `form_manager_lang_id`='1', `form_manager_antwort_html`='<p>Vielen Dank für Ihre Anfrage</p>', `form_manager_toptext_html`='<p>Bitte füllen Sie alle mit einem Stern (*) markierten Felder aus. Wir freuen uns auf Ihre Nachricht.</p>', `form_manager_bottomtext_html`='', `form_manager_antwort_email_betreff`='', `form_manager_antwort_email`='', `form_manager_antwort_email_html`='', `form_manager_antwort_yn`='', `form_manager_lang_button`='senden', `mail_an_betreiber_betreff`='test', `mail_an_betreiber_inhalt`='test', `form_manager_text_html`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lang` SET `form_manager_id_id`='4', `form_manager_lang_id`='1', `form_manager_antwort_html`='', `form_manager_toptext_html`='', `form_manager_bottomtext_html`='', `form_manager_antwort_email_betreff`='', `form_manager_antwort_email`='', `form_manager_antwort_email_html`='', `form_manager_antwort_yn`='', `form_manager_lang_button`='Absenden', `mail_an_betreiber_betreff`='', `mail_an_betreiber_inhalt`='', `form_manager_text_html`=''  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_form_manager_lead_content`; ##b_dump##
CREATE TABLE `XXX_papoo_form_manager_lead_content` (
  `form_manager_content_lead_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_manager_content_lead_id_id` int(11) NOT NULL,
  `form_manager_content_lead_feld_name` varchar(255) NOT NULL,
  `form_manager_content_lead_feld_content` text NOT NULL,
  PRIMARY KEY (`form_manager_content_lead_id`),
  KEY `idx_pfmlc_form_manager_content_lead_id_id` (`form_manager_content_lead_id_id`, `form_manager_content_lead_feld_name`),
  KEY `idx_pfmlc_form_manager_content_lead_feld_name` (`form_manager_content_lead_feld_name`),
  FULLTEXT `idx_pfmlc_form_manager_content_lead_feld_content` (`form_manager_content_lead_feld_content`)
) ENGINE=MyISAM AUTO_INCREMENT=171 DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='1', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='anrede', `form_manager_content_lead_feld_content`='Herr'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='2', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='3', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='4', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='5', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='6', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='titel', `form_manager_content_lead_feld_content`='Dr.'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='7', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='8', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='9', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='10', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='11', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='vorname', `form_manager_content_lead_feld_content`='asdasd'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='12', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='13', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='14', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='15', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='16', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='nachname', `form_manager_content_lead_feld_content`='asdasd'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='17', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='18', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='19', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='20', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='21', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='firma', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='22', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='23', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='24', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='25', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='26', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='ihre_position', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='27', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='28', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='29', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='30', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='31', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='strasse', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='32', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='33', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='34', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='35', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='36', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='plz', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='37', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='38', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='39', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='40', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='41', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='ort', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='42', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='43', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='44', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='45', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='46', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='telefon', `form_manager_content_lead_feld_content`='adasd'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='47', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='48', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='49', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='50', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='51', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='telefax', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='52', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='53', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='54', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='55', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='56', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='e_mail', `form_manager_content_lead_feld_content`='asd@kobos.de'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='57', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='58', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='59', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='60', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='61', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='ihre_nachricht', `form_manager_content_lead_feld_content`='adsads'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='62', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='63', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='64', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='65', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='66', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='name', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='67', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='68', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='69', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='70', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='71', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='spamcode', `form_manager_content_lead_feld_content`='kqj'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='72', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='73', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='74', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='75', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='76', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='form_manager_id', `form_manager_content_lead_feld_content`='2'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='77', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='78', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='79', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='80', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='81', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='form_manager_submit', `form_manager_content_lead_feld_content`='senden'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='82', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='83', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='84', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='85', `form_manager_content_lead_id_id`='1', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='86', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='anrede', `form_manager_content_lead_feld_content`='Herr'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='87', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='88', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='89', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='90', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='91', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='titel', `form_manager_content_lead_feld_content`='Dr.'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='92', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='93', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='94', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='95', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='96', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='vorname', `form_manager_content_lead_feld_content`='asdasd'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='97', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='98', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='99', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='100', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='101', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='nachname', `form_manager_content_lead_feld_content`='asdasd'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='102', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='103', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='104', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='105', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='106', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='firma', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='107', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='108', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='109', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='110', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='111', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='ihre_position', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='112', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='113', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='114', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='115', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='116', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='strasse', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='117', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='118', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='119', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='120', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='121', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='plz', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='122', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='123', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='124', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='125', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='126', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='ort', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='127', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='128', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='129', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='130', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='131', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='telefon', `form_manager_content_lead_feld_content`='adasd'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='132', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='133', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='134', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='135', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='136', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='telefax', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='137', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='138', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='139', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='140', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='141', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='e_mail', `form_manager_content_lead_feld_content`='asd@kobos.de'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='142', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='143', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='144', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='145', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='146', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='ihre_nachricht', `form_manager_content_lead_feld_content`='adsads'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='147', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='148', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='149', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='150', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='151', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='name', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='152', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='153', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='154', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='155', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='156', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='spamcode', `form_manager_content_lead_feld_content`='dc3'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='157', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='158', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='159', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='160', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='161', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='form_manager_id', `form_manager_content_lead_feld_content`='2'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='162', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='163', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='164', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='165', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='166', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='form_manager_submit', `form_manager_content_lead_feld_content`='senden'  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='167', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Referrer Google', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='168', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='gclid', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='169', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Letzter direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_content` SET `form_manager_content_lead_id`='170', `form_manager_content_lead_id_id`='2', `form_manager_content_lead_feld_name`='Primaerer direkter Referrer', `form_manager_content_lead_feld_content`=''  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_form_manager_lead_show_felder`; ##b_dump##
CREATE TABLE `XXX_papoo_form_manager_lead_show_felder` (
  `form_manager_content_form_id` int(11) NOT NULL DEFAULT '0',
  `form_manager_content_form_id_single` int(11) NOT NULL DEFAULT '0',
  `form_manager_content_lead_feld_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_lead_show_felder` SET `form_manager_content_form_id`='2', `form_manager_content_form_id_single`='0', `form_manager_content_lead_feld_name`='datum'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_form_manager_leads`; ##b_dump##
CREATE TABLE `XXX_papoo_form_manager_leads` (
  `form_manager_lead_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_manager_form_id` int(11) NOT NULL,
  `form_manager_form_datum` varchar(255) NOT NULL,
  `form_manager_form_ip_sender` varchar(255) NOT NULL,
  `form_manager_form_loesch_datum1` varchar(255) NOT NULL,
  `form_manager_form_loesch_datum2` varchar(255) NOT NULL,
  `form_manager_form_loesch_yn` varchar(255) NOT NULL,
  `form_manager_form_mail1_send` varchar(255) NOT NULL,
  `form_manager_lang_mail2_send` varchar(255) NOT NULL,
  PRIMARY KEY (`form_manager_lead_id`),
  KEY `idx_pfml_form_manager_form_id` (`form_manager_form_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_papoo_form_manager_leads` SET `form_manager_lead_id`='2', `form_manager_form_id`='2', `form_manager_form_datum`='1379929061', `form_manager_form_ip_sender`='127.0.0.1', `form_manager_form_loesch_datum1`='1466242661', `form_manager_form_loesch_datum2`='1466242661', `form_manager_form_loesch_yn`='', `form_manager_form_mail1_send`='', `form_manager_lang_mail2_send`=''  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_form_manager_menu_lookup`; ##b_dump##
CREATE TABLE `XXX_papoo_form_manager_menu_lookup` (
  `form_mid` int(11) NOT NULL,
  `form_menu_id` int(11) NOT NULL,
  PRIMARY KEY (`form_mid`, `form_menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_plugin_cform`; ##b_dump##
CREATE TABLE `XXX_papoo_plugin_cform` (
  `plugin_cform_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_cform_group_id` int(11) NOT NULL DEFAULT '0',
  `plugin_cform_no_group` varchar(255) NOT NULL,
  `plugin_cform_form_id` int(11) NOT NULL DEFAULT '0',
  `plugin_cform_order_id` int(11) NOT NULL DEFAULT '0',
  `plugin_cform_name` varchar(255) NOT NULL,
  `plugin_cform_label_img` text NOT NULL,
  `plugin_cform_size` varchar(255) NOT NULL,
  `plugin_cform_must` int(11) NOT NULL DEFAULT '0',
  `plugin_cform_type` varchar(255) NOT NULL DEFAULT 'text',
  `plugin_cform_content_type` varchar(255) NOT NULL DEFAULT 'alpha',
  `plugin_cform_minlaeng` varchar(255) NOT NULL DEFAULT '1',
  PRIMARY KEY (`plugin_cform_id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='25', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='40', `plugin_cform_name`='nachname', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='1', `plugin_cform_type`='text', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='24', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='50', `plugin_cform_name`='firma', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='0', `plugin_cform_type`='text', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='23', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='60', `plugin_cform_name`='firma', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='0', `plugin_cform_type`='text', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='22', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='70', `plugin_cform_name`='ihre_position', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='0', `plugin_cform_type`='text', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='21', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='80', `plugin_cform_name`='strasse', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='0', `plugin_cform_type`='text', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='20', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='90', `plugin_cform_name`='plz', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='0', `plugin_cform_type`='text', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='19', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='100', `plugin_cform_name`='ort', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='0', `plugin_cform_type`='text', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='18', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='110', `plugin_cform_name`='telefon', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='1', `plugin_cform_type`='text', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='17', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='120', `plugin_cform_name`='telefax', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='0', `plugin_cform_type`='text', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='16', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='130', `plugin_cform_name`='e_mail', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='0', `plugin_cform_type`='email', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='15', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='140', `plugin_cform_name`='ihre_nachricht', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='1', `plugin_cform_type`='textarea', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='26', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='30', `plugin_cform_name`='vorname', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='1', `plugin_cform_type`='text', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='27', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='20', `plugin_cform_name`='titel', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='1', `plugin_cform_type`='select', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform` SET `plugin_cform_id`='28', `plugin_cform_group_id`='4', `plugin_cform_no_group`='0', `plugin_cform_form_id`='2', `plugin_cform_order_id`='10', `plugin_cform_name`='anrede', `plugin_cform_label_img`='', `plugin_cform_size`='0', `plugin_cform_must`='1', `plugin_cform_type`='select', `plugin_cform_content_type`='alpha', `plugin_cform_minlaeng`='0'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_plugin_cform_cats`; ##b_dump##
CREATE TABLE `XXX_papoo_plugin_cform_cats` (
  `formcat__id` int(11) NOT NULL AUTO_INCREMENT,
  `formcat__name_der_kategorie` text,
  `formcat__beschreibung_der_kategorie` text,
  `formcat__cat_lang_id` int(11) NOT NULL,
  PRIMARY KEY (`formcat__id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_plugin_cform_group`; ##b_dump##
CREATE TABLE `XXX_papoo_plugin_cform_group` (
  `plugin_cform_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_cform_group_form_id` int(11) NOT NULL DEFAULT '0',
  `plugin_cform_group_order_id` int(11) NOT NULL DEFAULT '0',
  `plugin_cform_group_name` varchar(255) NOT NULL,
  PRIMARY KEY (`plugin_cform_group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_group` SET `plugin_cform_group_id`='4', `plugin_cform_group_form_id`='2', `plugin_cform_group_order_id`='1', `plugin_cform_group_name`='Kontaktformular'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_plugin_cform_group_lang`; ##b_dump##
CREATE TABLE `XXX_papoo_plugin_cform_group_lang` (
  `plugin_cform_group_lang_id` int(11) NOT NULL DEFAULT '0',
  `plugin_cform_group_lang_lang` int(11) NOT NULL DEFAULT '0',
  `plugin_cform_group_text` text NOT NULL,
  `plugin_cform_group_text_intern` text NOT NULL,
  PRIMARY KEY (`plugin_cform_group_lang_id`, `plugin_cform_group_lang_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_group_lang` SET `plugin_cform_group_lang_id`='1', `plugin_cform_group_lang_lang`='1', `plugin_cform_group_text`='Gewünschter Seminartermin auswählen', `plugin_cform_group_text_intern`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_group_lang` SET `plugin_cform_group_lang_id`='2', `plugin_cform_group_lang_lang`='1', `plugin_cform_group_text`='Die Teilnehmerzahl ist begrenzt ', `plugin_cform_group_text_intern`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_group_lang` SET `plugin_cform_group_lang_id`='3', `plugin_cform_group_lang_lang`='1', `plugin_cform_group_text`='Bestätigung', `plugin_cform_group_text_intern`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_group_lang` SET `plugin_cform_group_lang_id`='2', `plugin_cform_group_lang_lang`='2', `plugin_cform_group_text`='Die Teilnehmerzahl ist begrenzt ', `plugin_cform_group_text_intern`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_group_lang` SET `plugin_cform_group_lang_id`='4', `plugin_cform_group_lang_lang`='1', `plugin_cform_group_text`='Kontakt', `plugin_cform_group_text_intern`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_group_lang` SET `plugin_cform_group_lang_id`='5', `plugin_cform_group_lang_lang`='1', `plugin_cform_group_text`='Kontaktformular', `plugin_cform_group_text_intern`=''  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_plugin_cform_lang`; ##b_dump##
CREATE TABLE `XXX_papoo_plugin_cform_lang` (
  `plugin_cform_lang_id` int(11) NOT NULL DEFAULT '0',
  `plugin_cform_lang_lang` int(11) NOT NULL DEFAULT '0',
  `plugin_cform_label` text NOT NULL,
  `plugin_cform_label_img` text NOT NULL,
  `plugin_cform_content_list` text NOT NULL,
  `plugin_cform_descrip` text NOT NULL,
  `plugin_cform_tooltip` text NOT NULL,
  `plugin_cform_lang_header` text NOT NULL,
  PRIMARY KEY (`plugin_cform_lang_id`, `plugin_cform_lang_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='1', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Firma/Institution:', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='2', `plugin_cform_lang_lang`='1', `plugin_cform_label`='24.05.2007 - Termin', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='3', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Ihr Name', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='4', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Ihr Vorname', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='6', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Straße u. HausNr.:', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='5', `plugin_cform_lang_lang`='1', `plugin_cform_label`='26.04.2007 - Termin', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='7', `plugin_cform_lang_lang`='1', `plugin_cform_label`='PLZ u. Ort:', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='8', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Telefon (ohne / nur die Nummer)', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='9', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Telefax', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='10', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Ihre E-Mail', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='11', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Ich nehme mit wieviel Personen teil?', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='12', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Die  Seminarbedingungen habe ich gesehen und bin damit einverstanden.', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='12', `plugin_cform_lang_lang`='2', `plugin_cform_label`='abe ich gesehen und bin damit einverstanden.', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='13', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Ort?', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='14', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Datum', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='2', `plugin_cform_lang_lang`='2', `plugin_cform_label`='24.05.2007 - Termin', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='5', `plugin_cform_lang_lang`='2', `plugin_cform_label`='26.04.2007 - Termin', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='3', `plugin_cform_lang_lang`='2', `plugin_cform_label`='Your Name', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='4', `plugin_cform_lang_lang`='2', `plugin_cform_label`='Your vorname', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='15', `plugin_cform_lang_lang`='1', `plugin_cform_label`=' Ihre Nachricht', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='16', `plugin_cform_lang_lang`='1', `plugin_cform_label`=' E-Mail', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='17', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Telefax', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='18', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Telefon', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='19', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Ort', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='20', `plugin_cform_lang_lang`='1', `plugin_cform_label`='PLZ', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='21', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Strasse', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='22', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Ihre Position', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='23', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Firma', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='24', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Firma', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='25', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Nachname', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='26', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Vorname', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='27', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Titel', `plugin_cform_label_img`='', `plugin_cform_content_list`='Prof.\r\nDr.', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='28', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Anrede', `plugin_cform_label_img`='', `plugin_cform_content_list`='Herr\r\nFrau', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='29', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Nachname', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='30', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Firma', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='31', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Firma', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='32', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Ihre Position', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='33', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Strasse', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='34', `plugin_cform_lang_lang`='1', `plugin_cform_label`='PLZ', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='35', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Ort', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='36', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Telefon', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='37', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Telefax', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='38', `plugin_cform_lang_lang`='1', `plugin_cform_label`=' E-Mail', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='39', `plugin_cform_lang_lang`='1', `plugin_cform_label`=' Ihre Nachricht', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='40', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Vorname', `plugin_cform_label_img`='', `plugin_cform_content_list`='', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='41', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Titel', `plugin_cform_label_img`='', `plugin_cform_content_list`='Prof.\r\nDr.', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##
INSERT INTO `XXX_papoo_plugin_cform_lang` SET `plugin_cform_lang_id`='42', `plugin_cform_lang_lang`='1', `plugin_cform_label`='Anrede', `plugin_cform_label_img`='', `plugin_cform_content_list`='Herr\r\nFrau', `plugin_cform_descrip`='', `plugin_cform_tooltip`='', `plugin_cform_lang_header`=''  ; ##b_dump##