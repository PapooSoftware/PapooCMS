ALTER TABLE `XXX_plugin_kalender` CHANGE `kalender_id` `kalender_id` INT NOT NULL;
ALTER TABLE `XXX_plugin_kalender` DROP PRIMARY KEY;
ALTER TABLE `XXX_plugin_kalender_date` CHANGE `pkal_date_id` `pkal_date_id` INT NOT NULL;
ALTER TABLE `XXX_plugin_kalender_date` DROP PRIMARY KEY;
