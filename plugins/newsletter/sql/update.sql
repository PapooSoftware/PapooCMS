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