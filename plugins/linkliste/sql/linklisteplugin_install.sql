DROP TABLE IF EXISTS `XXX_papoo_linkliste_cat`; ##b_dump##
CREATE TABLE `XXX_papoo_linkliste_cat` (
  `cat_id` int(11) NOT NULL auto_increment ,
  `cat_order_id` int(11) NOT NULL ,
  `cat_sub` int(11) NOT NULL  DEFAULT '0' ,
  `cat_level` int(11) NOT NULL  DEFAULT '0' ,
  PRIMARY KEY (`cat_id`) 
) ENGINE=MyISAM ; ##b_dump##



DROP TABLE IF EXISTS `XXX_papoo_linkliste_daten`; ##b_dump##
CREATE TABLE `XXX_papoo_linkliste_daten` (
  `linkliste_id` int(11) NOT NULL auto_increment ,
  `linkliste_order_id` int(11) NOT NULL ,
  `cat_linkliste_id` int(11) NOT NULL ,
  `paket_logo` text NOT NULL ,
  `linkliste_link` text NOT NULL ,
  `linkliste_link_art` text NOT NULL ,
  `linkliste_descrip` text NOT NULL ,
  `linkliste_meta_title` text NOT NULL ,
  `linkliste_meta_descrip` text NOT NULL ,
  `linkliste_meta_key` text NOT NULL ,
  `linkliste_real` text NOT NULL ,
  PRIMARY KEY (`linkliste_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_linkliste_daten` SET linkliste_id='1', linkliste_order_id='1', cat_linkliste_id='0', paket_logo='', linkliste_link='http://www.papoo.de', linkliste_link_art='', linkliste_descrip='', linkliste_meta_title='', linkliste_meta_descrip='', linkliste_meta_key='', linkliste_real=''  ; ##b_dump##
INSERT INTO `XXX_papoo_linkliste_daten` SET linkliste_id='2', linkliste_order_id='1', cat_linkliste_id='0', paket_logo='', linkliste_link='http://www.google.de', linkliste_link_art='', linkliste_descrip='', linkliste_meta_title='', linkliste_meta_descrip='', linkliste_meta_key='', linkliste_real=''  ; ##b_dump##
INSERT INTO `XXX_papoo_linkliste_daten` SET linkliste_id='3', linkliste_order_id='1', cat_linkliste_id='0', paket_logo='', linkliste_link='http://www.spiegel.de', linkliste_link_art='', linkliste_descrip='', linkliste_meta_title='', linkliste_meta_descrip='', linkliste_meta_key='', linkliste_real=''  ; ##b_dump##


DROP TABLE IF EXISTS `XXX_papoo_linkliste_daten_lang`; ##b_dump##
CREATE TABLE `XXX_papoo_linkliste_daten_lang` (
  `linkliste_lang_id` int(11) NOT NULL ,
  `linkliste_lang_lang` int(11) NOT NULL ,
  `linkliste_Wort` text NOT NULL ,
  `linkliste_link_lang` text NOT NULL ,
  `linkliste_descrip` text NOT NULL ,
  `linkliste_lang_header` text NOT NULL  
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_linkliste_daten_lang` SET linkliste_lang_id='1', linkliste_lang_lang='1', linkliste_Wort='http://www.papoo.de', linkliste_link_lang='', linkliste_descrip='', linkliste_lang_header=''  ; ##b_dump##
INSERT INTO `XXX_papoo_linkliste_daten_lang` SET linkliste_lang_id='2', linkliste_lang_lang='1', linkliste_Wort='http://www.google.de', linkliste_link_lang='', linkliste_descrip='', linkliste_lang_header=''  ; ##b_dump##
INSERT INTO `XXX_papoo_linkliste_daten_lang` SET linkliste_lang_id='3', linkliste_lang_lang='1', linkliste_Wort='http://www.spiegel.de', linkliste_link_lang='', linkliste_descrip='', linkliste_lang_header=''  ; ##b_dump##


DROP TABLE IF EXISTS `XXX_papoo_linkliste_lang_cat`; ##b_dump##
CREATE TABLE `XXX_papoo_linkliste_lang_cat` (
  `cat_id_idx` int(11) NOT NULL auto_increment ,
  `cat_id_id` int(11) NOT NULL  DEFAULT '0' ,
  `cat_lang_id` int(11) NOT NULL  DEFAULT '0' ,
  `cat_name` text NOT NULL ,
  `cat_title` text NOT NULL ,
  `cat_description` text NOT NULL ,
  `cat_keywords` text NOT NULL ,
  PRIMARY KEY (`cat_id_idx`) 
) ENGINE=MyISAM ; ##b_dump##



DROP TABLE IF EXISTS `XXX_papoo_linkliste_lang_pref`; ##b_dump##
CREATE TABLE `XXX_papoo_linkliste_lang_pref` (
  `linkliste_id_id` varchar(255) NOT NULL ,
  `linkliste_lang_id` int(11) NOT NULL ,
  `linkliste_lang` text NOT NULL ,
  `linkliste_title` text NOT NULL ,
  `linkliste_description` text NOT NULL ,
  `linkliste_keywords` text NOT NULL  
) ENGINE=MyISAM ; ##b_dump##



DROP TABLE IF EXISTS `XXX_papoo_linkliste_pref`; ##b_dump##
CREATE TABLE `XXX_papoo_linkliste_pref` (
  `linkliste_id` int(11) NOT NULL  DEFAULT '0' ,
  `linkliste_liste` varchar(255) NOT NULL ,
  `linkliste_daten` text NOT NULL ,
  `linkliste_xml_yn` varchar(255) NOT NULL ,
  `linkliste_xml_link` text NOT NULL ,
  `linkliste_lang` text NOT NULL ,
  `linkliste_show` varchar(255) NOT NULL  
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_linkliste_pref` SET linkliste_id='1', linkliste_liste='', linkliste_daten='0', linkliste_xml_yn='', linkliste_xml_link='', linkliste_lang='0;1', linkliste_show='1'  ; ##b_dump##
