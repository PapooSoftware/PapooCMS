ALTER TABLE `XXX_plugin_partner_unterseiten` 
	ADD COLUMN `unterseiten_shop_redirect` VARCHAR(255) COLLATE utf8_bin NULL DEFAULT NULL,
	ADD COLUMN `unterseiten_shop_redirect_time` SMALLINT NOT NULL DEFAULT -1 ;  ##b_dump##