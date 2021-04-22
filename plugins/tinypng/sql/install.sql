DROP TABLE IF EXISTS `XXX_plugin_tinypng_config`; ##b_dump##
CREATE TABLE `XXX_plugin_tinypng_config` (
  `tinypng_config_id` int(11) NOT NULL AUTO_INCREMENT,
  `tinypng_config_apikey` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`tinypng_config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_tinypng_config` SET tinypng_config_id='1', tinypng_config_apikey=NULL ; ##b_dump##