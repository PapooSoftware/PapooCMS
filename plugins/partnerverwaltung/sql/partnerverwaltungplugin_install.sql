DROP TABLE IF EXISTS `XXX_papoo_partnerverwaltung_daten`; ##b_dump##
CREATE TABLE `XXX_papoo_partnerverwaltung_daten` (
  `partner_id` int(11) NOT NULL auto_increment ,
  `partner_user_id` varchar(255) NOT NULL ,
  `partner_name` text NOT NULL ,
  `partner_url` text NOT NULL ,
  `partner_code` text NOT NULL ,
  `partner_logo` varchar(255) NOT NULL ,
  `partner_lang_id` varchar(255) NOT NULL ,
  `partner_artikelid` varchar(255) NOT NULL ,
  PRIMARY KEY (`partner_id`) 
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_partnerverwaltung_pref`; ##b_dump##
CREATE TABLE `XXX_papoo_partnerverwaltung_pref` (
  `partnerverwaltung_id` int(11) NOT NULL auto_increment ,
  `partnerverwaltung_text_top` text NOT NULL ,
  `partnerverwaltung_wieviel` text NOT NULL ,
  `partner_pref_lang_id` varchar(255) NOT NULL ,
  PRIMARY KEY (`partnerverwaltung_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_partnerverwaltung_pref` SET partnerverwaltung_id='1', partnerverwaltung_text_top='', partnerverwaltung_wieviel='', partner_pref_lang_id=''  ; ##b_dump##