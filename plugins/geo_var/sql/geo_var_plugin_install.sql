DROP TABLE IF EXISTS `XXX_plugin_geo_var`; ##b_dump##
CREATE TABLE `XXX_plugin_geo_var` (
  `geo_var_id` int(11) NOT NULL AUTO_INCREMENT,
  `geo_var_variablen` text,
  PRIMARY KEY (`geo_var_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_geo_var` SET geo_var_id='5', geo_var_variablen='eu;#var_1#;wert 1\r\neu;#var_2#;wert 2\r\nna;#var_1#;wert 2'  ; ##b_dump##