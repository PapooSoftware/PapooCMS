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
ALTER TABLE `XXX_papoo_freiemodule_daten` ADD COLUMN `freiemodule_lang_id` INT(11) NOT NULL AFTER `freiemodule_id`; ##b_dump##
UPDATE `XXX_papoo_freiemodule_daten` _module
LEFT JOIN `XXX_papoo_name_language` _lang ON _lang.lang_short LIKE _module.freiemodule_lang
SET _module.freiemodule_lang_id = COALESCE(_lang.lang_id, 1); ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` MODIFY `freiemodule_id` INT NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` DROP PRIMARY KEY; ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` ADD PRIMARY KEY (`freiemodule_id`, `freiemodule_lang_id`); ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` MODIFY `freiemodule_id` INT NOT NULL AUTO_INCREMENT; ##b_dump##
