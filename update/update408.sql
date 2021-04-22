ALTER TABLE `XXX_papoo_me_nu` ADD `artikel_sort` int(11)  NULL  DEFAULT '0' ; ##b_dump## 
ALTER TABLE `XXX_papoo_news_impressum` ADD `news_allow_delete` TINYINT(1) NOT NULL DEFAULT '0' ; ##b_dump## 
ALTER TABLE `XXX_papoo_config` ADD `config_html_tidy_funktionaktivieren` varchar(255) NOT NULL; ##b_dump## 

ALTER TABLE `XXX_papoo_news_user` ADD `news_unsubscribe_date` varchar(50) NOT NULL ; ##b_dump## 

ALTER TABLE `XXX_papoo_news_user` ADD `deleted` TINYINT(1) NOT NULL DEFAULT '0' ; ##b_dump## 
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
  PRIMARY KEY (`news_user_id`,`news_id`)
) ENGINE=MyISAM ; ##b_dump##
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
) ENGINE=MyISAM ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_news_protocol_attachments`; ##b_dump##
CREATE TABLE `XXX_papoo_news_protocol_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `name_stored` text NOT NULL,
  `size` int(11) NOT NULL,
  PRIMARY KEY (`id`,`news_id`)
) ENGINE=MyISAM  ; ##b_dump##
