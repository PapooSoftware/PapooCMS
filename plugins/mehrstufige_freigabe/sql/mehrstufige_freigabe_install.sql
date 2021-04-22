DROP TABLE IF EXISTS `XXX_mehrstufige_freigabe`; ##b_dump##
CREATE TABLE `XXX_mehrstufige_freigabe` (
  `mf_repore_id` bigint(20) NULL ,
  `mf_autor_name` varchar(250) NULL ,
  `mf_autor_id` int(11) NULL ,
  `mf_autor_date` varchar(50) NULL ,
  `mf_pruefer_name` varchar(250) NULL ,
  `mf_pruefer_id` int(11) NULL ,
  `mf_pruefer_date` varchar(50) NULL ,
  `mf_freigeber_name` varchar(250) NULL ,
  `mf_freigeber_id` int(11) NULL ,
  `mf_freigeber_date` varchar(50) NULL ,
  KEY `mf_reporeid` (`mf_repore_id`) 
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_mehrstufige_freigabe_einstellungen`; ##b_dump##
CREATE TABLE `XXX_mehrstufige_freigabe_einstellungen` (
  `mfe_id` int(11) NOT NULL auto_increment ,
  `mfe_gruppenid_autor` int(11) NOT NULL  DEFAULT '12' ,
  `mfe_gruppenid_pruefer` int(11) NOT NULL  DEFAULT '11' ,
  `mfe_gruppenid_freigeber` int(11) NOT NULL  DEFAULT '1' ,
  PRIMARY KEY (`mfe_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_mehrstufige_freigabe_einstellungen` SET mfe_id='1', mfe_gruppenid_autor='12', mfe_gruppenid_pruefer='11', mfe_gruppenid_freigeber='1'  ; ##b_dump##