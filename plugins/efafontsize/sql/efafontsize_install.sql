DROP TABLE IF EXISTS `XXX_plugin_efa_fontsize`; ##b_dump##
CREATE TABLE `XXX_plugin_efa_fontsize` (
  `efa_fontsize_spez_id` int(11) NOT NULL AUTO_INCREMENT,
  `efa_fontsize_spez_schriftgre_default_wert` text,
  `efa_fontsize_spez_steigerung_pro_schritt_in_` text,
  `efa_fontsize_spez_maximale_schriftgre_in_` text,
  `efa_fontsize_spez_minimale_schriftgre_in_` text,
  PRIMARY KEY (`efa_fontsize_spez_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_plugin_efa_fontsize` SET efa_fontsize_spez_id='7', efa_fontsize_spez_schriftgre_default_wert='80', efa_fontsize_spez_steigerung_pro_schritt_in_='30', efa_fontsize_spez_maximale_schriftgre_in_='180', efa_fontsize_spez_minimale_schriftgre_in_='70'  ; ##b_dump##
