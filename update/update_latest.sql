ALTER TABLE `XXX_bildwechsler` CHANGE `bw_id` `bw_id` BIGINT NOT NULL; ##b_dump##
ALTER TABLE `XXX_bildwechsler` DROP PRIMARY KEY; ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` ADD COLUMN `freiemodule_lang_id` INT(11) NOT NULL AFTER `freiemodule_id`; ##b_dump##
UPDATE `XXX_papoo_freiemodule_daten` _module
LEFT JOIN `XXX_papoo_name_language` _lang ON _lang.lang_short LIKE _module.freiemodule_lang
SET _module.freiemodule_lang_id = COALESCE(_lang.lang_id, 1); ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` MODIFY `freiemodule_id` INT NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` DROP PRIMARY KEY; ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` ADD PRIMARY KEY (`freiemodule_id`, `freiemodule_lang_id`); ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` MODIFY `freiemodule_id` INT NOT NULL AUTO_INCREMENT; ##b_dump##
ALTER TABLE `XXX_plugin_kalender` CHANGE `kalender_id` `kalender_id` INT NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_kalender` DROP PRIMARY KEY; ##b_dump##
ALTER TABLE `XXX_plugin_kalender_date` CHANGE `pkal_date_id` `pkal_date_id` INT NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_kalender_date` DROP PRIMARY KEY; ##b_dump##
ALTER TABLE `XXX_glossar_daten` CHANGE `glossar_id` `glossar_id` INT NOT NULL; ##b_dump##
ALTER TABLE `XXX_glossar_daten` DROP PRIMARY KEY; ##b_dump##
ALTER TABLE `XXX_glossar_daten` DROP PRIMARY KEY; ##b_dump##
DROP TABLE IF EXISTS `XXX_glossar_pref_html`; ##b_dump##
CREATE TABLE `XXX_glossar_pref_html` (
                                         `glosspref_id_id` int NOT NULL DEFAULT '1',
                                         `glosspref_lang_id` int NOT NULL DEFAULT '1',
                                         `glosspref_introtext_de` longtext
) ; ##b_dump##
INSERT INTO `XXX_glossar_pref_html` SET `glosspref_id_id`='1', `glosspref_lang_id`='1', `glosspref_introtext_de`='<h2>Glossar-Überschrift</h2><p>.. Text</p>'  ; ##b_dump##
ALTER TABLE `XXX_papoo_bannerverwaltung_daten` ADD `banner_lang_id` INT(11) NOT NULL DEFAULT '1' AFTER `banner_id`; ##b_dump##
ALTER TABLE `XXX_papoo_bannerverwaltung_daten` CHANGE `banner_id` `banner_id` INT NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_bannerverwaltung_daten` DROP PRIMARY KEY; ##b_dump##
ALTER TABLE `XXX_papoo_faq_categories` CHANGE `id` `id` INT NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_faq_categories` DROP PRIMARY KEY; ##b_dump##
ALTER TABLE `XXX_papoo_faq_content` CHANGE `id` `id` INT NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_faq_content` DROP PRIMARY KEY; ##b_dump##
ALTER TABLE `XXX__plugin_shop_produkte_lang` CHANGE `produkte_lang_meta_title` `produkte_lang_meta_title` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL; ##b_dump##
ALTER TABLE `XXX__plugin_shop_produkte_lang` CHANGE `produkte_lang_meta_beschreibung` `produkte_lang_meta_beschreibung` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL; ##b_dump##
ALTER TABLE `XXX__plugin_shop_produkte_lang` CHANGE `produkte_lang_met_keywords` `produkte_lang_met_keywords` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL; ##b_dump##
ALTER TABLE `XXX__plugin_shop_produkte_lang` CHANGE `produkte_lang_produkt_beschreibung_lang` `produkte_lang_produkt_beschreibung_lang` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL; ##b_dump##
ALTER TABLE `XXX__plugin_shop_produkte_lang` CHANGE `produkte_lang_produkt_surl` `produkte_lang_produkt_surl` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL; ##b_dump##
ALTER TABLE `XXX__plugin_shop_produkte_lang` CHANGE `produkte_lang_produktename` `produkte_lang_produktename` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL; ##b_dump##
ALTER TABLE `XXX__plugin_shop_produkte_lang` CHANGE `produkte_lang_produkt_beschreibung` `produkte_lang_produkt_beschreibung` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_user` ADD `user_titel` varchar(255) NULL; ##b_dump##
ALTER TABLE `XXX_papoo_user` ADD `user_country` varchar(255) NULL; ##b_dump##
ALTER TABLE `XXX_papoo_user` ADD `user_tel_abends` varchar(255) NULL; ##b_dump##
ALTER TABLE `XXX_papoo_user` ADD `user_tel_tags` varchar(255) NULL; ##b_dump##
ALTER TABLE `XXX_papoo_user` ADD `user_tel_kunden_nr` varchar(255) NULL; ##b_dump##
ALTER TABLE `XXX_papoo_user` ADD `user_merkzettel` LONGTEXT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `send_reply_mail` tinyint(1) NOT NULL DEFAULT 1; ##b_dump##
ALTER TABLE `XXX__plugin_shop_daten` CHANGE `einstellungen_id` `einstellungen_id` INT NOT NULL; ##b_dump##
