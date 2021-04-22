ALTER TABLE `XXX_papoo_config` ADD `config_uplaod_hide_praefix` SMALLINT default '0'; ##b_dump##
ALTER TABLE `XXX_papoo_config` ADD `config_tiny_advimg_filelist` SMALLINT default '0'; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='35', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_menue_bootstrap.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='35', modlang_lang_id='1', modlang_name='Navigations-Menü Komplett Bootstrap', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett mit Bootstrap Dropdown'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='35', modlang_lang_id='2', modlang_name='Navigations-Menü Komplett Bootstrap', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett mit Bootstrap Dropdown'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='35', modlang_lang_id='3', modlang_name='Navigations-Menü Komplett Bootstrap', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett mit Bootstrap Dropdown'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='35', modlang_lang_id='4', modlang_name='Navigations-Menü Komplett Bootstrap', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett mit Bootstrap Dropdown'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='35', modlang_lang_id='5', modlang_name='Navigations-Menü Komplett Bootstrap', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett mit Bootstrap Dropdown'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='35', modlang_lang_id='6', modlang_name='Navigations-Menü Komplett Bootstrap', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett mit Bootstrap Dropdown'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='35', modlang_lang_id='7', modlang_name='Navigations-Menü Komplett Bootstrap', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett mit Bootstrap Dropdown'  ; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_bildwechsler_config` ( bw_template_name text DEFAULT NULL ) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_bildwechsler_config` SET bw_template_name='jquerytools' ; ##b_dump##
