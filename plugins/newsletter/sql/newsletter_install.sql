DROP TABLE IF EXISTS `XXX_papoo_news_imp_lang`; ##b_dump##
CREATE TABLE `XXX_papoo_news_imp_lang` (
  `news_imp_id` int(11) NOT NULL DEFAULT '0',
  `news_imp_lang_id` int(11) NOT NULL DEFAULT '0',
  `news_imp_imp` text NOT NULL,
  `news_imp_imp_html` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

INSERT INTO `XXX_papoo_news_imp_lang` SET news_imp_id='1', news_imp_lang_id='1', news_imp_imp='', news_imp_imp_html='x'  ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_news_impressum`; ##b_dump##
CREATE TABLE `XXX_papoo_news_impressum` (
  `news_imp_id` int(11) NOT NULL DEFAULT '0',
  `news_email` varchar(255) NOT NULL,
  `news_name` varchar(255) NOT NULL,
  `mails_per_step` int(11) NOT NULL DEFAULT '100',
  `news_erw` varchar(255) NOT NULL,
  `wyswig` varchar(255) NOT NULL,
  `news_html` varchar(255) NOT NULL,
  `news_allow_delete` TINYINT(1) NOT NULL DEFAULT '0',
  `news_anzeig_message` varchar(255) NOT NULL,
  `news_sprachwahl` varchar(255) NOT NULL,
  `news_impressum` text NOT NULL,
  `news_lang` text NOT NULL,
  `news_impressum_html` text NOT NULL,
  `pop3_server` varchar(255) NOT NULL,
  `pop3_username` varchar(255) NOT NULL,
  `pop3_password` varchar(255) NOT NULL,
  `pop3_port` int(5) NOT NULL DEFAULT '110',
  `check_hardbounce` int(1) NOT NULL DEFAULT '0',
  `max_hardbounce` int(2) NOT NULL DEFAULT '3',
  `max_hardbounce_time` int(3) NOT NULL DEFAULT '14'
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

INSERT INTO `XXX_papoo_news_impressum` SET news_imp_id='0', news_email='newsletter@ihre_seite.de', news_name='Ihr Absendername', news_erw='', wyswig='', news_html='', news_anzeig_message='', news_sprachwahl='', news_impressum='Inhalt', news_lang=';1', news_impressum_html=''  ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_news_user`; ##b_dump##
CREATE TABLE `XXX_papoo_news_user` (
  `news_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_active` int(11) NOT NULL DEFAULT '0',
  `deleted` TINYINT(1) NOT NULL DEFAULT '0',
  `news_key` text NOT NULL,
  `news_user_email` varchar(200) NOT NULL,
  `news_name_vor` varchar(255) NOT NULL,
  `news_name_nach` varchar(255) NOT NULL,
  `news_name_gender` varchar(255) NOT NULL,
  `news_name_str` varchar(255) NOT NULL,
  `news_name_plz` varchar(255) NOT NULL,
  `news_user_lang` varchar(255) NOT NULL DEFAULT '1',
  `news_name_ort` varchar(255) NOT NULL,
  `news_name_staat` varchar(255) NOT NULL,
  `news_phone` varchar(255) NOT NULL,
  `news_name_firma` varchar(255) NOT NULL,
  `news_name_mitglied` tinyint(1) NOT NULL DEFAULT '0',
  `news_name_abonnent` tinyint(1) NOT NULL DEFAULT '0',
  `news_signup_date` varchar(50) NOT NULL,
  `news_unsubscribe_date` varchar(50) NOT NULL,
  `news_gruppen` text,
  `news_hardbounce` int(2) NOT NULL DEFAULT '0',
  `news_hardbounce_time` date NOT NULL,
  PRIMARY KEY (`news_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; ##b_dump##

INSERT INTO `XXX_papoo_news_user` SET news_user_id='1', news_active='1', news_key='b7841872e8b138fc40a3b5f0a9db43e5', news_user_email='mail@ihre_seite.de', news_name_vor='', news_name_nach='', news_name_gender='', news_name_str='', news_name_plz='', news_user_lang='1', news_name_ort='', news_signup_date=''  ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_newsletter`; ##b_dump##
CREATE TABLE `XXX_papoo_newsletter` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_date` varchar(255) NOT NULL,
  `news_date_send` varchar(255) NOT NULL,
  `news_inhalt` text NOT NULL,
  `news_inhalt_lang` text NOT NULL,
  `news_inhalt_html` text NOT NULL,
  `news_inhalt_attach` text NOT NULL,
  `news_header` text NOT NULL,
  `news_send` int(11) NOT NULL DEFAULT '0',
  `news_abbonennten` int(11) NOT NULL DEFAULT '0',
  `news_gruppe` text NOT NULL,
  `news_nlgruppe` text NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_newsletter_messages`; ##b_dump##
CREATE TABLE `XXX_papoo_newsletter_messages` (
  `news_msg_id` int(11) NOT NULL,
  `news_msg_lang_id` int(11) NOT NULL,
  `news_keyword` varchar(255) NOT NULL,
  `news_header` text NOT NULL,
  `news_inhalt` text NOT NULL,
  PRIMARY KEY (`news_msg_id`,`news_msg_lang_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_news_gruppen`; ##b_dump##
CREATE TABLE `XXX_papoo_news_gruppen` (
  `news_gruppe_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_gruppe_name` varchar(255) NOT NULL,
  `news_gruppe_description` text NOT NULL,
  PRIMARY KEY (`news_gruppe_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; ##b_dump##

INSERT INTO `XXX_papoo_news_gruppen` (`news_gruppe_id`, `news_gruppe_name`, `news_gruppe_description`) VALUES (1, 'NL Verteilerliste Standard', 'Standard NL Verteilerliste'); ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_news_attachments`; ##b_dump##
CREATE TABLE `XXX_papoo_news_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `name_stored` text NOT NULL,
  `size` int(11) NOT NULL,
  PRIMARY KEY (`id`,`news_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_news_protocol_attachments`; ##b_dump##
CREATE TABLE `XXX_papoo_news_protocol_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `name_stored` text NOT NULL,
  `size` int(11) NOT NULL,
  PRIMARY KEY (`id`,`news_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_news_protocol_newsletter`; ##b_dump##
CREATE TABLE `XXX_papoo_news_protocol_newsletter` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_date` varchar(255) NOT NULL,
  `news_date_send` varchar(255) NOT NULL,
  `news_inhalt` text NOT NULL,
  `news_inhalt_lang` text NOT NULL,
  `news_inhalt_html` text NOT NULL,
  `news_inhalt_attach` text NOT NULL,
  `news_header` text NOT NULL,
  `news_send` int(11) NOT NULL DEFAULT '0',
  `news_abbonennten` int(11) NOT NULL DEFAULT '0',
  `news_gruppe` text NOT NULL,
  `news_nlgruppe` text NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_news_protocol_user`; ##b_dump##
CREATE TABLE `XXX_papoo_news_protocol_user` (
  `news_user_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `deleted` TINYINT(1) NOT NULL DEFAULT '0',
  `news_type` varchar(10) NOT NULL,
  `news_active` int(11) NOT NULL DEFAULT '0',
  `news_key` text NOT NULL,
  `news_user_email` varchar(200) NOT NULL,
  `news_name_vor` varchar(255) NOT NULL,
  `news_name_nach` varchar(255) NOT NULL,
  `news_name_gender` varchar(255) NOT NULL,
  `news_name_str` varchar(255) NOT NULL,
  `news_name_plz` varchar(255) NOT NULL,
  `news_user_lang` varchar(255) NOT NULL DEFAULT '1',
  `news_name_ort` varchar(255) NOT NULL,
  `news_name_staat` varchar(255) NOT NULL,
  `news_phone` varchar(255) NOT NULL,
  `news_name_firma` varchar(255) NOT NULL,
  `news_name_mitglied` tinyint(1) NOT NULL DEFAULT '0',
  `news_name_abonnent` tinyint(1) NOT NULL DEFAULT '0',
  `news_signup_date` varchar(50) NOT NULL,
  `news_unsubscribe_date` varchar(50) NOT NULL,
  `news_gruppen` text,
  `news_hardbounce` int(2) NOT NULL DEFAULT '0',
  `news_hardbounce_time` date NOT NULL,
  PRIMARY KEY (`news_user_id`,`news_id`,`news_type` )
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_news_error_report`; ##b_dump##
CREATE TABLE `XXX_papoo_news_error_report` (
  `report_id` int(11) NOT NULL auto_increment ,
  `import_time` timestamp NOT NULL ,
  `imported_by` text NOT NULL,
  `records_to_import` int(11) NOT NULL DEFAULT 0 ,
  `error_count` int(11) NOT NULL DEFAULT 0 ,
  `success_count` int(11) NOT NULL DEFAULT 0,
  `highest_cc` int(11) NOT NULL DEFAULT 0,
  `used_file` text NOT NULL,
  PRIMARY KEY (`report_id`) 
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_news_error_report_details`; ##b_dump##
CREATE TABLE `XXX_papoo_news_error_report_details` (
  `report_id` int(11) NOT NULL ,
  `import_file_record_no` int(11) NOT NULL DEFAULT 0 ,
  `import_file_field_position` smallint(11) NOT NULL DEFAULT 0 ,
  `import_file_field_name` text NOT NULL ,
  `completion_code` int(11) NOT NULL DEFAULT 0 ,
  `NAME` TEXT CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `VORNAME` TEXT CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `STRASSE` TEXT CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `PLZ` TEXT CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `ORT` TEXT CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `MAIL` TEXT CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  KEY `idx_records` (`report_id`) 
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_news_user_lookup_gruppen`; ##b_dump##
CREATE TABLE `XXX_papoo_news_user_lookup_gruppen` (
  `news_user_id_lu` int(11) NOT NULL DEFAULT '0',
  `news_gruppe_id_lu` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`news_user_id_lu`,`news_gruppe_id_lu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##

ALTER TABLE  `XXX_papoo_news_gruppen` ADD  `news_grpfront` INT( 11 ) NOT NULL DEFAULT '0' ; ##b_dump##
ALTER TABLE  `XXX_papoo_news_gruppen` ADD  `news_grpmoderated` INT( 11 ) NOT NULL DEFAULT '0' ; ##b_dump##
ALTER TABLE  `XXX_papoo_news_user_lookup_gruppen` ADD  `news_active` INT( 11 ) NOT NULL DEFAULT '1' ; ##b_dump##
ALTER TABLE  `XXX_papoo_news_protocol_user` MODIFY  `news_type` VARCHAR( 200 ) NOT NULL; ##b_dump##