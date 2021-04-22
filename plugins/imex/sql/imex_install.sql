DROP TABLE IF EXISTS `XXX_papoo_imex_daten`; ##b_dump##
CREATE TABLE `XXX_papoo_imex_daten` (
  `imex_id` int(11) NOT NULL auto_increment ,
  `imex_name` text NOT NULL ,
  `imex_csv` text NOT NULL ,
  `imex_felder` text NOT NULL ,
  `imex_csv_update` text NOT NULL ,
  `imex_felder_update` text NOT NULL ,
  `imex_meta_key` text NOT NULL ,
  PRIMARY KEY (`imex_id`) 
) ENGINE=MyISAM ; ##b_dump##
