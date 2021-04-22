ALTER TABLE `XXX_plugin_gutschein` MODIFY COLUMN `gutschein_is_send` tinyint(1) NOT NULL DEFAULT 0 ; ##b_dump##
ALTER IGNORE TABLE `XXX_plugin_gutschein` ADD UNIQUE INDEX (`gutschein_shop_id`); ##b_dump##

CREATE TABLE `XXX_plugin_gutschein_nummern` (
  `gutschein_shop_id` int(11) NOT NULL,
  `gutschein_product_id` int(11) NOT NULL,
  `gutschein_number` int(11) NOT NULL,
  PRIMARY KEY (`gutschein_shop_id`, `gutschein_product_id`)
) ENGINE=MyISAM ; ##b_dump##
