CREATE TABLE IF NOT EXISTS `XXX_papoo_freiemodule_menu_lookup` (
  `freiemodule_mid` int(11) NOT NULL ,
  `freiemodule_menu_id` text NOT NULL
); ##b_dump##

CREATE TABLE IF NOT EXISTS `XXX_papoo_freiemodule_menu_blacklist` (
	`blacklist_menu_id` INT(11) NOT NULL,
	`blacklist_module_id` INT(11) NOT NULL,
	PRIMARY KEY (`blacklist_menu_id`, `blacklist_module_id`)
); ##b_dump##

ALTER TABLE `XXX_papoo_freiemodule_daten` ADD COLUMN `freiemodule_raw_output` TINYINT(1) NOT NULL DEFAULT 0; ##b_dump##
