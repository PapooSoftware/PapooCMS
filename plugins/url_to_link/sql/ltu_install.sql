DROP TABLE IF EXISTS `XXX_plugin_ltu`; ##b_dump##
CREATE TABLE `XXX_plugin_ltu` (
  `ltu__id` int(11) NOT NULL AUTO_INCREMENT,
  `ltu__link_to_url_1` text,
  `ltu__link_to_url_2` text,
  PRIMARY KEY (`ltu__id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_ltu` SET ltu__id='1', ltu__link_to_url_1='Standard;http://www.google.de', ltu__link_to_url_2='Standard;http://www.papoo.de'  ; ##b_dump##
ALTER TABLE `XXX_plugin_ltu` ADD `ltu__link_to_url_3` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_ltu` ADD `ltu__link_to_url_4` TEXT; ##b_dump##