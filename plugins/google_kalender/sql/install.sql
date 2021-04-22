DROP TABLE IF EXISTS `XXX_plugin_google_kalender`; ##b_dump##
CREATE TABLE `XXX_plugin_google_kalender` (
	`kalender_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`kalender_google_id` VARCHAR(255) COLLATE ascii_general_ci NOT NULL,
	`kalender_send_email` BOOLEAN NOT NULL DEFAULT FALSE,
	`kalender_info_email` VARCHAR(255),
	`kalender_timezone` VARCHAR(64) COLLATE ascii_general_ci NOT NULL,
	`kalender_access_token` BLOB,
	PRIMARY KEY (`kalender_id`)
) DEFAULT CHARSET=utf8 ; ##b_dump##

CREATE TABLE `XXX_plugin_google_kalender_lang` (
	`kalender_id` INT UNSIGNED NOT NULL,
	`kalender_lang_id` INT UNSIGNED NOT NULL,
	`kalender_name` VARCHAR(128) NOT NULL,
	`kalender_text_above` TEXT,
	PRIMARY KEY (`kalender_id`, `kalender_lang_id`)
) DEFAULT CHARSET=utf8 ; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_google_kalender_lookup_read`; ##b_dump##
CREATE TABLE `XXX_plugin_google_kalender_lookup_read` (
	`kalender_id` INT UNSIGNED NOT NULL,
	`gruppeid` INT NOT NULL,
	PRIMARY KEY (`kalender_id`, `gruppeid`)
) DEFAULT CHARSET=utf8 ; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_google_kalender_lookup_write`; ##b_dump##
CREATE TABLE `XXX_plugin_google_kalender_lookup_write` (
	`kalender_id` INT UNSIGNED NOT NULL,
	`gruppeid` INT NOT NULL,
	PRIMARY KEY (`kalender_id`, `gruppeid`)
) DEFAULT CHARSET=utf8 ; ##b_dump##

ALTER TABLE `XXX_plugin_google_kalender_lang`
	ADD FOREIGN KEY (`kalender_id`) REFERENCES `XXX_plugin_google_kalender`(`kalender_id`) ON UPDATE CASCADE ON DELETE CASCADE ; ##b_dump##

ALTER TABLE `XXX_plugin_google_kalender_lookup_read`
	ADD FOREIGN KEY (`kalender_id`) REFERENCES `XXX_plugin_google_kalender`(`kalender_id`) ON UPDATE CASCADE ON DELETE CASCADE ; ##b_dump##

ALTER TABLE `XXX_plugin_google_kalender_lookup_read`
	ADD FOREIGN KEY (`gruppeid`) REFERENCES `XXX_papoo_gruppe`(`gruppeid`) ON UPDATE CASCADE ON DELETE CASCADE ; ##b_dump##

ALTER TABLE `XXX_plugin_google_kalender_lookup_write`
	ADD FOREIGN KEY (`kalender_id`) REFERENCES `XXX_plugin_google_kalender`(`kalender_id`) ON UPDATE CASCADE ON DELETE CASCADE ; ##b_dump##

ALTER TABLE `XXX_plugin_google_kalender_lookup_write`
	ADD FOREIGN KEY (`gruppeid`) REFERENCES `XXX_papoo_gruppe`(`gruppeid`) ON UPDATE CASCADE ON DELETE CASCADE ; ##b_dump##
