DROP TABLE IF EXISTS `XXX_content_manipulator_tabelle`; ##b_dump##
CREATE TABLE `XXX_content_manipulator_tabelle` (
  `cm_id` int(11) NOT NULL auto_increment ,
  `cm_zahl` int(11) NULL ,
  `cm_string` varchar(255) NULL ,
  `cm_text` text NULL ,
  PRIMARY KEY (`cm_id`) 
) ENGINE=MyISAM ; ##b_dump##