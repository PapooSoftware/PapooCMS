DROP TABLE IF EXISTS `XXX_papoo_news_error_report`; ##b_dump##
CREATE TABLE `XXX_papoo_news_error_report` (
  `report_id` int(11) NOT NULL auto_increment ,
  `import_time` timestamp NOT NULL ,
  `imported_by` text NOT NULL,
  `records_to_import` int(11) NOT NULL DEFAULT 0 ,
  `error_count` int(11) NOT NULL DEFAULT 0 ,
  `success_count` int(11) NOT NULL DEFAULT 0,
  `highest_cc` int(11) NOT NULL DEFAULT 0,
  `used_file` text NOT NULL,

  PRIMARY KEY (`report_id`) 
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_news_error_report_details`; ##b_dump##
CREATE TABLE `XXX_papoo_news_error_report_details` (
  `report_id` int(11) NOT NULL ,
  `import_file_record_no` int(11) NOT NULL DEFAULT 0 ,
  `import_file_field_position` smallint(11) NOT NULL DEFAULT 0 ,
  `import_file_field_name` text NOT NULL ,
  `completion_code` int(11) NOT NULL DEFAULT 0 ,
  KEY `idx_records` (`report_id`) 
) ENGINE=MyISAM ; ##b_dump##