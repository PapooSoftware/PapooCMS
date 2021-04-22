DROP TABLE IF EXISTS `XXX_kraken_settings`; ##b_dump##
CREATE TABLE `XXX_kraken_settings` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ; ##b_dump##

DROP TABLE IF EXISTS `XXX_kraken_history`; ##b_dump##
CREATE TABLE `XXX_kraken_history` (
  `bildname` varchar(255) NOT NULL,
  `optimiert` BOOLEAN
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ; ##b_dump##