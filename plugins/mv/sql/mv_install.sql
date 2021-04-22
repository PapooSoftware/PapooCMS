DROP TABLE IF EXISTS `XXX_papoo_mv`; ##b_dump##
CREATE TABLE `XXX_papoo_mv` (
  `mv_id` int(11) NOT NULL auto_increment ,
  `mv_name` text NOT NULL ,
  `mv_lang` text NOT NULL ,
  `mv_art` int(11) NOT NULL DEFAULT '1' ,
  `mv_set_group_id` int(11) NOT NULL  DEFAULT '0' ,
  `mv_set_group_name` text NOT NULL ,
  `mv_set_suchmaske`int(11) NOT NULL DEFAULT '0' ,
  `mv_menu_id` int(11) NOT NULL ,
  PRIMARY KEY (`mv_id`)
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mv_lang`; ##b_dump##
CREATE TABLE `XXX_papoo_mv_lang` (
  `mv_id_id` int(11) NOT NULL  DEFAULT '0' ,
  `mv_lang_id` text NOT NULL ,
  `mv_name_label` text NOT NULL ,  
  KEY `mv_id_id` (`mv_id_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mv_name_language`; ##b_dump##
CREATE TABLE `XXX_papoo_mv_name_language` (
  `mv_lang_id` int(11) NOT NULL auto_increment  ,
  `mv_lang_short` text NOT NULL ,
  `mv_lang_long` text NOT NULL ,
  `mv_aktive` int(11) NOT NULL  DEFAULT '0' ,
  `mv_lang_standard` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mv_lang_id`)
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mvcform`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_mvcform` (
  `mvcform_id` int(11) NOT NULL default '0',
  `mvcform_meta_id` int(11) NOT NULL default '0',
  `mvcform_group_id` int(11) NOT NULL default '0',
  `mvcform_no_group` text NOT NULL,
  `mvcform_form_id` int(11) NOT NULL default '0',
  `mvcform_order_id` int(11) NOT NULL default '0',
  `mvcform_name` text NOT NULL,
  `mvcform_size` text NOT NULL,
  `mvcform_must` tinyint(1) NOT NULL default '0',
  `mvcform_must_back` tinyint(1) NOT NULL default '0',
  `mvcform_type` text NOT NULL,
  `mvcform_content_type` text NOT NULL,
  `mvcform_minlaeng` text NOT NULL,
  `mvcform_maxlaeng` text NOT NULL,
  `mvcform_required_feld` text NOT NULL,
  `mvcform_default_wert` int(11) NOT NULL default '0',
  `mvcform_list` tinyint(1) NOT NULL default '0',
  `mvcform_list_front` tinyint(1) NOT NULL default '0',
  `mvcform_normaler_user` tinyint(1) NOT NULL default '0',
  `mvcform_protokoll` tinyint(1) NOT NULL default '0',
  `mvcform_search` tinyint(1) NOT NULL default '0',
  `mvcform_search_back` tinyint(1) NOT NULL default '0',
  `mvcform_kalender` tinyint(1) NOT NULL default '0',
  `mvcform_filter_front` tinyint(1) NOT NULL default '1',
  `mvcform_defaulttreeviewname` tinyint(1) NOT NULL default '0',
  `mvcform_aktiv` tinyint(1) NOT NULL default '0',
  `mvcform_admin_must` tinyint(1) NOT NULL default '0',
  `mvcform_sort` int(11) NOT NULL default '0',
  `mvcform_name_export` text NOT NULL,
  `mvcform_lang_dependence` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Sprachabhängigkeit',
  `mvcform_flex_id` int(11) DEFAULT NULL,
  `mvcform_flex_feld_id` int(11) DEFAULT NULL,
  UNIQUE KEY `mvcfom_index` (`mvcform_form_id`,`mvcform_meta_id`,`mvcform_group_id`,`mvcform_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mvcform_group`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_mvcform_group` (
  `mvcform_group_id` int(11) NOT NULL,
  `mvcform_group_form_id` int(11) NOT NULL DEFAULT '0' COMMENT 'MV-ID',
  `mvcform_group_order_id` int(11) NOT NULL DEFAULT '10',
  `mvcform_group_name` varchar(255) NOT NULL,
  `mvcform_group_form_meta_id` int(11) NOT NULL DEFAULT '1',
  UNIQUE KEY `unique_index` (`mvcform_group_form_id`,`mvcform_group_form_meta_id`,`mvcform_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mvcform_group_lang`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_mvcform_group_lang` (
  `mvcform_group_lang_id` int(11) NOT NULL DEFAULT '0',
  `mvcform_group_lang_lang` int(11) NOT NULL DEFAULT '0',
  `mvcform_group_lang_meta` int(11) NOT NULL DEFAULT '1',
  `mvcform_group_text` text NOT NULL,
  `mvcform_group_text_intern` text NOT NULL,
  UNIQUE KEY `unique_index` (`mvcform_group_lang_id`,`mvcform_group_lang_lang`,`mvcform_group_lang_meta`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mvcform_lang`; ##b_dump##
CREATE TABLE `XXX_papoo_mvcform_lang` (
  `mvcform_lang_id` int(11) NOT NULL  DEFAULT '0' ,
  `mvcform_lang_lang` int(11) NOT NULL  DEFAULT '0' ,
  `mvcform_lang_meta_id` int(11) NOT NULL  DEFAULT '0' ,
  `mvcform_label` text NOT NULL ,
  `mvcform_content_list` text NOT NULL ,
  `mvcform_descrip` text NOT NULL ,
  `mvcform_lang_header` text NOT NULL,
  `mvcform_lang_tooltip` TEXT NOT NULL,
  UNIQUE KEY `unique_index` (`mvcform_lang_id`,`mvcform_lang_lang`,`mvcform_lang_meta_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mv_protokoll`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_mv_protokoll` (
  `mv_pro_id` int(11) NOT NULL auto_increment,
  `mv_pro_date` datetime default NULL,
  `mv_pro_mv_id` int(11) NOT NULL default '0',
  `mv_pro_mv_content_id` int(11) NOT NULL default '0',
  `mv_pro_group_id` int(11) NOT NULL default '0',
  `mv_pro_feld_id` int(11) NOT NULL default '0',
  `mv_pro_old_content` text NOT NULL,
  `mv_pro_login_id` int(11) NOT NULL default '0',
  `mv_pro_ip` text NOT NULL,
  PRIMARY KEY  (`mv_pro_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mv_imex_daten`; ##b_dump##
CREATE TABLE `XXX_papoo_mv_imex_daten` (
  `imex_id` int(11) NOT NULL auto_increment ,
  `imex_name` text NOT NULL ,
  `imex_csv` text NOT NULL ,
  `imex_felder` text NOT NULL ,
  `imex_csv_update` text NOT NULL ,
  `imex_felder_update` text NOT NULL ,
  `imex_meta_key` text NOT NULL ,
  PRIMARY KEY (`imex_id`) 
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mv_imex_error_report`; ##b_dump##
CREATE TABLE `XXX_papoo_mv_imex_error_report` (
  `report_id` int(11) NOT NULL auto_increment ,
  `imported_to_mv` text NOT NULL,
  `imported_to_main_meta` text NOT NULL,
  `imported_to_other_meta` text NOT NULL,
  `selected_group_rights` text NOT NULL,
  `import_time` timestamp NOT NULL ,
  `imported_by` text NOT NULL,
  `records_to_import` int(11) NOT NULL DEFAULT 0 ,
  `error_count` int(11) NOT NULL DEFAULT 0 ,
  `success_count` int(11) NOT NULL DEFAULT 0,
  `highest_cc` int(11) NOT NULL DEFAULT 0,
  `used_file` text NOT NULL,
  `used_file_type` text NOT NULL,
  `used_template_id` int(11) NOT NULL DEFAULT 0,
  `used_template_name` text NOT NULL,
  `used_import_type` text NOT NULL,
  PRIMARY KEY (`report_id`) 
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mv_imex_error_report_details`; ##b_dump##
CREATE TABLE `XXX_papoo_mv_imex_error_report_details` (
  `report_id` int(11) NOT NULL ,
  `import_file_record_no` int(11) NOT NULL DEFAULT 0 ,
  `import_file_field_position` smallint(11) NOT NULL DEFAULT 0 ,
  `import_file_field_name` text NOT NULL ,
  `completion_code` int(11) NOT NULL DEFAULT 0 ,
  KEY `idx_records` (`report_id`) 
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mv_admin`; ##b_dump##
CREATE TABLE `XXX_papoo_mv_admin` (
  `id` int(11) NOT NULL,
  `admin_name` text NOT NULL ,
  `admin_id` int(11) NOT NULL ,
  PRIMARY KEY (`id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_mv_admin` SET id='1', admin_name='noch keiner ausgewählt', admin_id='0' ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mv_dzvhae`; ##b_dump##
CREATE TABLE `XXX_papoo_mv_dzvhae` (
  `mv_dzvhae_system_id` int(11) NOT NULL auto_increment ,
  `mv_content_id` int(11) NOT NULL,
  PRIMARY KEY (`mv_dzvhae_system_id`) 
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mv_meta_lp`; ##b_dump##
CREATE TABLE `XXX_papoo_mv_meta_lp` (
  `mv_meta_lp_user_id` int(11) NOT NULL ,
  `mv_meta_lp_meta_id` int(11) NOT NULL ,
  `mv_meta_lp_mv_id` int(11) NOT NULL,
  UNIQUE KEY `unique_index` (`mv_meta_lp_user_id`,`mv_meta_lp_meta_id`,`mv_meta_lp_mv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mv_meta_main_lp`; ##b_dump##
CREATE TABLE `XXX_papoo_mv_meta_main_lp` (
  `mv_meta_main_lp_user_id` int(11) NOT NULL ,
  `mv_meta_main_lp_meta_id` int(11) NOT NULL ,
  `mv_meta_main_lp_mv_id` int(11) NOT NULL,
  UNIQUE KEY `unique_index` (`mv_meta_main_lp_user_id`,`mv_meta_main_lp_meta_id`,`mv_meta_main_lp_mv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mvcform_pflicht_lp`; ##b_dump##
CREATE TABLE `XXX_papoo_mvcform_pflicht_lp` (
  `mv_id` int(11) NOT NULL ,
  `meta_id` int(1) NOT NULL ,
  `feld_id` int(11) NOT NULL,
  `pflicht_feld_id` int(11) NOT NULL,
  KEY `mv_id` (`mv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mv_feld_preisintervall`; ##b_dump##
CREATE TABLE `XXX_papoo_mv_feld_preisintervall` (
  `mv_id` int(11) NOT NULL ,
  `feld_id` int(11) NOT NULL,
  `intervalle` text NOT NULL ,
  `waehrung` text NOT NULL,
  KEY `mv_id` (`mv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mv_datum_verfallen`; ##b_dump##
CREATE TABLE `XXX_papoo_mv_datum_verfallen` (
  `verfall_mv_id` int(11) NOT NULL ,
  `verfall_meta_id` int(11) NOT NULL,
  `verfall_content_id` int(11) NOT NULL,
  `verfall_stufe_id` int(11) NOT NULL,
  KEY `verfall_mv_id` (`verfall_mv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

DROP TABLE IF EXISTS `xxx_papoo_mv_special_rights1`; ##b_dump##
CREATE TABLE IF NOT EXISTS `xxx_papoo_mv_special_rights1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group` (`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1; ##b_dump##

DROP TABLE IF EXISTS `xxx_papoo_mv_special_rights2`; ##b_dump##
CREATE TABLE IF NOT EXISTS `xxx_papoo_mv_special_rights2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group` (`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_mv_download_protocol`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_mv_download_protocol` (
  `dl_idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Index',
  `file_name` text NOT NULL COMMENT 'Dateiname',
  `mv_id` int(11) NOT NULL COMMENT 'Verw.-Id',
  `mv_content_id` int(11) NOT NULL COMMENT 'Datei-Id',
  `field_id` int(11) NOT NULL COMMENT 'Feld-Id',
  `dl_count` int(11) NOT NULL COMMENT 'Anzahl Downloads',
  PRIMARY KEY (`dl_idx`),
  UNIQUE KEY `dl_idx` (`dl_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Count Downloads' AUTO_INCREMENT=1 ; ##b_dump##
