DROP TABLE IF EXISTS `XXX_papoo_messe_pref`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_messe_pref` (
  `messe_id` int(11) NOT NULL default '0',
  `messe_liste` varchar(255) NOT NULL default '',
  `messe_daten` text NOT NULL,
  `messe_xml_yn` varchar(255) NOT NULL default '',
  `messe_xml_link` text NOT NULL,
  `messe_lang` text NOT NULL,
  `messe_show` varchar(255) NOT NULL default ''
) ; ##b_dump##

INSERT INTO `XXX_papoo_messe_pref` 
(messe_id, messe_liste, messe_daten) 
VALUES ('1', '1', '0') ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_messe_lang_pref`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_messe_lang_pref` (
  `messe_id_id` varchar(255) NOT NULL default '',
  `messe_lang_id` int(11) NOT NULL,
  `messe_lang` text NOT NULL
); ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_messe_daten`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_messe_daten` (
  `messe_id` int(11) NOT NULL auto_increment,
  `messe_von` varchar(255) NOT NULL default '',
  `messe_bis` varchar(255) NOT NULL default '',
  `messe_logo` TEXT NOT NULL ,
  `messe_name` TEXT NOT NULL ,
  PRIMARY KEY  (`messe_id`)
); ##b_dump##


DROP TABLE IF EXISTS `XXX_papoo_messe_daten_lang`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_messe_daten_lang` (
  `messe_id_id` int(11) NOT NULL ,
  `messe_lang_id` int(11) NOT NULL ,
  `messe_descrip` text NOT NULL
); ##b_dump##
