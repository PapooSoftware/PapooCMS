ALTER TABLE `XXX_bildwechsler` CHANGE `bw_id` `bw_id` BIGINT NOT NULL;
ALTER TABLE `XXX_bildwechsler` DROP PRIMARY KEY;
ALTER TABLE `XXX_papoo_freiemodule_daten` ADD COLUMN `freiemodule_lang_id` INT(11) NOT NULL DEFAULT 1; ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` MODIFY `freiemodule_id` INT NOT NULL;
ALTER TABLE `XXX_papoo_freiemodule_daten` DROP PRIMARY KEY;
ALTER TABLE `XXX_plugin_kalender` CHANGE `kalender_id` `kalender_id` INT NOT NULL;
ALTER TABLE `XXX_plugin_kalender` DROP PRIMARY KEY;
ALTER TABLE `XXX_plugin_kalender_date` CHANGE `pkal_date_id` `pkal_date_id` INT NOT NULL;
ALTER TABLE `XXX_plugin_kalender_date` DROP PRIMARY KEY;
