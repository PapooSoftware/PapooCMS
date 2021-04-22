DROP TABLE IF EXISTS `XXX_ecard_data`; ##b_dump##
CREATE TABLE `XXX_ecard_data` (
  `bgalb_id` bigint(20) NOT NULL auto_increment ,
  `bgalb_bgalg_id` bigint(20) NULL ,
  `bgalb_datum` varchar(50) NULL ,
  `bgalb_name` varchar(250) NULL ,
  `bgalb_geschlecht` tinyint(4) NULL ,
  `bgalb_gewicht` varchar(50) NULL ,
  `bgalb_groesse` varchar(50) NULL ,
  `bgalb_bild` varchar(50) NULL ,
  `bgalb_text` text NULL ,
  KEY `bgalb_id` (`bgalb_id`),
  KEY `bgalb_bgalg_id` (`bgalb_bgalg_id`),
  KEY `bgalb_datum` (`bgalb_datum`) 
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_ecard_einstellungen`; ##b_dump##
CREATE TABLE `XXX_ecard_einstellungen` (
  `bgalset_id` int(11) NOT NULL auto_increment ,
  `bgalset_email_absender` varchar(250) NULL ,
  PRIMARY KEY (`bgalset_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_ecard_einstellungen` SET bgalset_id='1', bgalset_email_absender='ecard@ihre_domains.de'  ; ##b_dump##

DROP TABLE IF EXISTS `XXX_ecard_galerien`; ##b_dump##
CREATE TABLE `XXX_ecard_galerien` (
  `bgalg_id` bigint(20) NOT NULL auto_increment ,
  `bgalg_order_id` bigint(20) NULL ,
  `bgalg_name` varchar(250) NULL ,
  `bgalg_text` text NULL ,
  `bgalg_bild` varchar(250) NULL ,
  PRIMARY KEY (`bgalg_id`),
  KEY `bgalg_order_id` (`bgalg_order_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_ecard_galerien` SET bgalg_id='1', bgalg_order_id='10', bgalg_name='Galerie 1', bgalg_text='Text zur Galerie', bgalg_bild=''  ; ##b_dump##

NE=MyISAM ; ##b_dump##