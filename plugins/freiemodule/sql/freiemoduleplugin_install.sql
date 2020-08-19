DROP TABLE IF EXISTS `XXX_papoo_freiemodule_pref`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_freiemodule_pref` (
  `freiemodule_id` int(11) NOT NULL auto_increment,
  `freiemodule_rotation` varchar(255) NOT NULL default '',
  `freiemodule_export` varchar(255) NOT NULL default '',
  `freiemodule_import` varchar(255) NOT NULL default '',
  `freiemodule_url` text NOT NULL,
  `freiemodule_wieviel` text NOT NULL,
  PRIMARY KEY  (`freiemodule_id`)
) ; ##b_dump##
INSERT INTO `XXX_papoo_freiemodule_pref` SET freiemodule_id='1', freiemodule_rotation='', freiemodule_export='', freiemodule_import='', freiemodule_url='', freiemodule_wieviel='3'  ; ##b_dump##



DROP TABLE IF EXISTS `XXX_papoo_freiemodule_daten`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_freiemodule_daten` (
  `freiemodule_id` int(11) NOT NULL auto_increment,
  `freiemodule_name` text NOT NULL,
  `freiemodule_code` text NOT NULL,
  `freiemodule_menuid` varchar(255) NOT NULL default '',
  `freiemodule_artikelid` varchar(255) NOT NULL default '',
  `freiemodule_start` date NOT NULL,
  `freiemodule_stop` date NOT NULL,
  `freiemodule_modulid` varchar(255) NOT NULL default '',
  `freiemodule_lang` varchar(255) NOT NULL default '',
  `freiemodule_raw_output` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY  (`freiemodule_id`)
); ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_freiemodule_menu_lookup`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_freiemodule_menu_lookup` (
  `freiemodule_mid` int(11) NOT NULL ,
  `freiemodule_menu_id` text NOT NULL
); ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_freiemodule_menu_blacklist`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_freiemodule_menu_blacklist` (
	`blacklist_menu_id` INT(11) NOT NULL,
	`blacklist_module_id` INT(11) NOT NULL,
	PRIMARY KEY (`blacklist_menu_id`, `blacklist_module_id`)
); ##b_dump##
