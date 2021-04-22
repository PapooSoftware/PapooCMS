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