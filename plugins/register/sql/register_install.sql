DROP TABLE IF EXISTS `XXX_register`; ##b_dump##
CREATE TABLE `XXX_register` (
  `reg_id` bigint(20) NOT NULL auto_increment ,
  `reg_reg_id` int(11) NOT NULL  DEFAULT '0' ,
  `reg_buchstabe` varchar(1) NULL ,
  `reg_name` varchar(250) NULL ,
  `reg_name_order` varchar(250) NULL ,
  `reg_text` longtext NULL ,
  PRIMARY KEY (`reg_id`),
  KEY `reg_name_order` (`reg_name_order`),
  KEY `reg_reg_id` (`reg_reg_id`) 
) ENGINE=MyISAM ; ##b_dump##