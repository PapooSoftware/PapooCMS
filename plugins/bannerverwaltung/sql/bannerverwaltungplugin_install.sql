DROP TABLE IF EXISTS `XXX_papoo_bannerverwaltung_pref`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_bannerverwaltung_pref` (
  `bannerverwaltung_id` int(11) NOT NULL auto_increment,
  `bannerverwaltung_rotation` varchar(255) NOT NULL default '',
  `bannerverwaltung_export` varchar(255) NOT NULL default '',
  `bannerverwaltung_import` varchar(255) NOT NULL default '',
  `bannerverwaltung_url` text NOT NULL,
  `bannerverwaltung_wieviel` text NOT NULL,
  PRIMARY KEY  (`bannerverwaltung_id`)
) ; ##b_dump##
INSERT INTO `XXX_papoo_bannerverwaltung_pref` SET bannerverwaltung_id='1', bannerverwaltung_rotation='', bannerverwaltung_export='', bannerverwaltung_import='', bannerverwaltung_url='', bannerverwaltung_wieviel='3'  ; ##b_dump##



DROP TABLE IF EXISTS `XXX_papoo_bannerverwaltung_daten`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_bannerverwaltung_daten` (
  `banner_id` int(11) NOT NULL auto_increment,
  `banner_name` text NOT NULL,
  `banner_code` text NOT NULL,
  `banner_menuid` varchar(255) NOT NULL default '',
  `banner_artikelid` varchar(255) NOT NULL default '',
  `banner_start` date NOT NULL,
  `banner_stop` date NOT NULL,
  `banner_modulid` varchar(255) NOT NULL default '',
  `banner_lang` varchar(255) NOT NULL default '',
  `banner_count_yn` int(11) NOT NULL,
  `banner_views` int(11) NOT NULL,
  `banner_clicks` int(11) NOT NULL,
  PRIMARY KEY  (`banner_id`)
); ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_banner_menu_lookup`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_banner_menu_lookup` (
  `banner_mid` int(11) NOT NULL ,
  `banner_menu_id` text NOT NULL
); ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_banner_artikel_lookup`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_banner_artikel_lookup` (
  `banner_aid` int(11) NOT NULL ,
  `banner_art_id` text NOT NULL
); ##b_dump##