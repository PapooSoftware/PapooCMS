ALTER TABLE `XXX_glossar_daten` ADD `glossar_colorbox_fading` TEXT; ##b_dump##
ALTER TABLE `XXX_glossar_daten` ADD `glossar_mit_alphabet` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_glossar_pref` ADD `glossar_tabs` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_glossar_daten` CHANGE `glossar_id` `glossar_id` INT NOT NULL; ##b_dump##
ALTER TABLE `XXX_glossar_daten` DROP PRIMARY KEY; ##b_dump##
DROP TABLE IF EXISTS `XXX_glossar_pref_html`; ##b_dump##
CREATE TABLE `XXX_glossar_pref_html` (
                                         `glosspref_id_id` int NOT NULL DEFAULT '1',
                                         `glosspref_lang_id` int NOT NULL DEFAULT '1',
                                         `glosspref_introtext_de` longtext
) ENGINE=MyISAM ; ##b_dump##
INSERT INTO `XXX_glossar_pref_html` SET `glosspref_id_id`='1', `glosspref_lang_id`='1', `glosspref_introtext_de`='<h2>Glossar-Überschrift</h2><p>.. Text</p>'  ; ##b_dump##
