DROP TABLE IF EXISTS `XXX_plugin_shop_upload`; ##b_dump##
CREATE TABLE `XXX_plugin_shop_upload` (
  `shop_upload_plugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_upload_plugin_bestellnr` int(11) NULL,
  `shop_upload_plugin_text_` text,
  `shop_upload_plugin_image` text,
  PRIMARY KEY (`shop_upload_plugin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 ; ##b_dump##

