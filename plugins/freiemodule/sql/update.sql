ALTER TABLE `XXX_papoo_freiemodule_daten` ADD COLUMN `freiemodule_lang_id` INT(11) NOT NULL DEFAULT 1; ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` MODIFY `freiemodule_id` INT NOT NULL;
ALTER TABLE `XXX_papoo_freiemodule_daten` DROP PRIMARY KEY;
