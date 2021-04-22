DROP TABLE IF EXISTS `XXX_plugin_gutschein`; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_gutschein_nummern`; ##b_dump##
CREATE TABLE `XXX_plugin_gutschein` (
  `gutschein_id` int(11) NOT NULL AUTO_INCREMENT,
  `gutschein_shop_id` int(11) NOT NULL,
  `gutschein_is_send` tinyint(1) NOT NULL DEFAULT 0,
  `gutschein_pdf_template_file` text,
  PRIMARY KEY (`gutschein_id`),
  UNIQUE INDEX (`gutschein_shop_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##

CREATE TABLE `XXX_plugin_gutschein_nummern` (
  `gutschein_shop_id` int(11) NOT NULL,
  `gutschein_product_id` int(11) NOT NULL,
  `gutschein_number` int(11) NOT NULL,
  PRIMARY KEY (`gutschein_shop_id`, `gutschein_product_id`)
) ENGINE=MyISAM ; ##b_dump##
