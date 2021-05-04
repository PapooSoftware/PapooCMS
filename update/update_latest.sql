ALTER TABLE `XXX_bildwechsler` CHANGE `bw_id` `bw_id` BIGINT NOT NULL; ##b_dump##
ALTER TABLE `XXX_bildwechsler` DROP PRIMARY KEY; ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` ADD COLUMN `freiemodule_lang_id` INT(11) NOT NULL DEFAULT 1; ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` MODIFY `freiemodule_id` INT NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_freiemodule_daten` DROP PRIMARY KEY; ##b_dump##
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_glossar_pref_html` SET `glosspref_id_id`='1', `glosspref_lang_id`='1', `glosspref_introtext_de`='<h2>Glossar-Überschrift</h2><p>.. Text</p>'  ; ##b_dump##

