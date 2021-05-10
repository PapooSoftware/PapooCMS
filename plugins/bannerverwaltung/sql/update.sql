ALTER TABLE `XXX_papoo_bannerverwaltung_daten` ADD `banner_lang_id` INT(11) NOT NULL DEFAULT '1' AFTER `banner_id`; ##b_dump##
ALTER TABLE `XXX_papoo_bannerverwaltung_daten` CHANGE `banner_id` `banner_id` INT NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_bannerverwaltung_daten` DROP PRIMARY KEY; ##b_dump##
