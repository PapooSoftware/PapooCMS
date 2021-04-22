ALTER TABLE `XXX_papoo_quicknavi_daten` `quicknavi_lang_id` int(11) NOT NULL DEFAULT '1'; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_aktivierung_config`; ##b_dump##
CREATE TABLE `XXX_plugin_aktivierung_config` (
  `aktiv_plugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `aktiv_plugin_ihre_rechnungsnummer` text,
  `aktiv_plugin_installationen_auf_localhost` text,
  PRIMARY KEY (`aktiv_plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##

ALTER TABLE `XXX_plugin_kalender` MODIFY `kalender_xml_google` text; ##b_dump##

ALTER TABLE `XXX_bildwechsler` ADD `bw_ueberschrift` text default NULL; ##b_dump##

