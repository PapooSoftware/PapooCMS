DROP TABLE IF EXISTS `XXX_plugin_partner_unterseiten`; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_partner_unterseiten_menuedits`; ##b_dump##
CREATE TABLE `XXX_plugin_partner_unterseiten` (
	`unterseiten_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`unterseiten_name` VARCHAR(64) NOT NULL,
	`unterseiten_default_menuid` INT NULL DEFAULT NULL,
	`unterseiten_bildwechsler_menuid` INT NULL DEFAULT NULL,
	`unterseiten_shop_redirect` VARCHAR(255) COLLATE utf8_bin NULL DEFAULT NULL,
	`unterseiten_shop_redirect_time` SMALLINT NOT NULL DEFAULT -1,
	PRIMARY KEY (`unterseiten_id`),
	UNIQUE KEY (`unterseiten_name`)
) ; ##b_dump##

CREATE TABLE `XXX_plugin_partner_unterseiten_menuedits` (
	`unterseiten_id` SMALLINT UNSIGNED NOT NULL,
	`menuid` INT NOT NULL,
	`action` VARCHAR(16) COLLATE ascii_bin NOT NULL,
	`menuid2` INT NULL DEFAULT NULL,
	PRIMARY KEY (`unterseiten_id`, `menuid`)
) ; ##b_dump##

ALTER TABLE `XXX_plugin_partner_unterseiten_menuedits`
	ADD FOREIGN KEY (`unterseiten_id`)
	REFERENCES `XXX_plugin_partner_unterseiten` (`unterseiten_id`)
	ON UPDATE CASCADE ON DELETE CASCADE ; ##b_dump##

ALTER TABLE `XXX_plugin_partner_unterseiten_menuedits`
	ADD FOREIGN KEY (`menuid`)
	REFERENCES `XXX_papoo_menu_language` (`menuid_id`)
	ON UPDATE CASCADE ON DELETE CASCADE,
	ADD FOREIGN KEY (`menuid2`)
	REFERENCES `XXX_papoo_menu_language` (`menuid_id`)
	ON UPDATE CASCADE ON DELETE CASCADE; ##b_dump##

ALTER TABLE `XXX_plugin_partner_unterseiten`
	ADD FOREIGN KEY (`unterseiten_default_menuid`)
	REFERENCES `XXX_papoo_menu_language` (`menuid_id`)
	ON UPDATE CASCADE ON DELETE SET NULL,
	ADD FOREIGN KEY (`unterseiten_bildwechsler_menuid`)
	REFERENCES `XXX_papoo_menu_language` (`menuid_id`)
	ON UPDATE CASCADE ON DELETE SET NULL; ##b_dump##

INSERT INTO `XXX_plugin_partner_unterseiten` (`unterseiten_name`, `unterseiten_default_menuid`, `unterseiten_bildwechsler_menuid`)
	VALUES ('', 1, NULL) ;  ##b_dump##
