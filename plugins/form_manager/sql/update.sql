ALTER TABLE `XXX_papoo_form_manager_lang` ADD `form_manager_text_html` TEXT NOT NULL; ##b_dump##

CREATE TABLE `XXX_papoo_plugin_cform_cats` (
	`formcat__id` INT(11) NOT NULL auto_increment,
	PRIMARY KEY (`formcat__id`)
) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_papoo_plugin_cform_cats` ADD `formcat__name_der_kategorie` TEXT; ##b_dump##
ALTER TABLE `XXX_papoo_plugin_cform_cats` ADD `formcat__beschreibung_der_kategorie` TEXT; ##b_dump##
ALTER TABLE `XXX_papoo_plugin_cform_cats` ADD `formcat__cat_lang_id` INT(11) NOT NULL; ##b_dump##

ALTER TABLE `XXX_papoo_form_manager` ADD `form_manager_modul_id` varchar(255) NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_form_manager` ADD `form_manager_kategorie` INT(11) NOT NULL; ##b_dump##
ALTER TABLE  `XXX_papoo_form_manager` ADD `form_manager_erstellt` INT(11) NOT NULL; ##b_dump##
ALTER TABLE  `XXX_papoo_form_manager` ADD `form_manager_geaendert` INT(11) NOT NULL; ##b_dump##

ALTER TABLE `XXX_papoo_form_manager` ADD `form_manager_sender_mail` varchar(255) NOT NULL DEFAULT ''; ##b_dump##
ALTER TABLE `XXX_papoo_form_manager` ADD `form_manager_smtp_settings_type` varchar(255) NOT NULL DEFAULT ''; ##b_dump##
ALTER TABLE `XXX_papoo_form_manager` ADD `form_manager_smtp_host` varchar(255) NOT NULL DEFAULT ''; ##b_dump##
ALTER TABLE `XXX_papoo_form_manager` ADD `form_manager_smtp_port` smallint UNSIGNED NOT NULL DEFAULT 0; ##b_dump##
ALTER TABLE `XXX_papoo_form_manager` ADD `form_manager_smtp_user` varchar(255) NOT NULL DEFAULT ''; ##b_dump##
ALTER TABLE `XXX_papoo_form_manager` ADD `form_manager_smtp_pass` varchar(255) NOT NULL DEFAULT ''; ##b_dump##

CREATE TABLE `XXX_plugin_cform` (
	`form_manager_id` int(11) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`form_manager_id`)
) ENGINE=MyISAM; ##b_dump##

ALTER TABLE `XXX_plugin_cform` ADD `form_manager_name_form` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_cform` ADD `form_manager_kategorie_form` VARCHAR(255) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_cform` ADD `form_manager_formsuche` VARCHAR(255) NOT NULL; ##b_dump##

ALTER TABLE `XXX_papoo_form_manager_lead_content` ADD PRIMARY KEY (`form_manager_content_lead_id`); ##b_dump##
ALTER TABLE `XXX_papoo_form_manager_lead_content` ADD INDEX `idx_pfmlc_form_manager_content_lead_id_id` (`form_manager_content_lead_id_id`); ##b_dump##
ALTER TABLE `XXX_papoo_form_manager_lead_content` ADD INDEX `idx_pfmlc_form_manager_content_lead_feld_name` (`form_manager_content_lead_feld_name`); ##b_dump##
ALTER TABLE `XXX_papoo_form_manager_lead_content` ADD FULLTEXT `idx_pfmlc_form_manager_content_lead_feld_content` (`form_manager_content_lead_feld_content`); ##b_dump##

ALTER TABLE `XXX_papoo_form_manager_leads` ADD INDEX `idx_pfml_form_manager_form_id` (`form_manager_form_id`); ##b_dump##

ALTER TABLE `XXX_papoo_plugin_cform_lang` ADD PRIMARY KEY (`plugin_cform_lang_id`, `plugin_cform_lang_lang`); ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_form_manager_menu_lookup`; ##b_dump##
CREATE TABLE `XXX_papoo_form_manager_menu_lookup` (
	`form_mid` INT(11) NOT NULL,
	`form_menu_id` INT(11) NOT NULL,
	PRIMARY KEY (`form_mid`, `form_menu_id`)
) ENGINE=InnoDB; ##b_dump##
