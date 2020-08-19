DROP TABLE IF EXISTS `XXX_plugin_aktivierung_config`; ##b_dump##
CREATE TABLE `XXX_plugin_aktivierung_config` (
  `aktiv_plugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `aktiv_plugin_ihre_rechnungsnummer` text,
  `aktiv_plugin_installationen_auf_localhost` text,
  PRIMARY KEY (`aktiv_plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
