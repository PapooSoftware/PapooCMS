ALTER TABLE `XXX_papoo_daten` ADD `anzeig_filter` int(11) DEFAULT '0'; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `twitter_handle` varchar(16) NOT NULL DEFAULT ''; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `captcha_sitekey` varchar(255) NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `captcha_secret` varchar(255) NOT NULL; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_freiemodule_menu_lookup` (
  `freiemodule_mid` int(11) NOT NULL ,
  `freiemodule_menu_id` text NOT NULL
); ##b_dump##

CREATE TABLE IF NOT EXISTS `XXX_papoo_freiemodule_menu_blacklist` (
	`blacklist_menu_id` INT(11) NOT NULL,
	`blacklist_module_id` INT(11) NOT NULL,
	PRIMARY KEY (`blacklist_menu_id`, `blacklist_module_id`)
); ##b_dump##
