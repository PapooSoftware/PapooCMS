ALTER TABLE `XXX_papoo_daten` ADD `stamm_benach_artikelfreigabe_email` int(4) NOT NULL  DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_daten` ADD `stamm_documents_change_backup` int(4) NOT NULL  DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_user` ADD `user_content_tree_show_all` int(4) NOT NULL DEFAULT '0'; ##b_dump## 

DROP TABLE IF EXISTS `XXX_papoo_download_versionen`; ##b_dump##
CREATE TABLE `XXX_papoo_download_versionen` (
  `dv_id` bigint(20) NOT NULL auto_increment ,
  `dv_downloadid` bigint(20) NULL ,
  `dv_downloadlink` varchar(255) NULL ,
  `dv_downloaduserid` int(11) NULL ,
  `dv_zeitpunkt` varchar(50) NULL ,
  `dv_org_filename` varchar(255) NULL ,
  PRIMARY KEY (`dv_id`),
  KEY `dv_downloadid` (`dv_downloadid`),
  KEY `dv_zeitpunkt` (`dv_zeitpunkt`) 
) ENGINE=MyISAM ; ##b_dump##

ALTER TABLE `XXX_papoo_form_manager_lang` ADD `mail_an_betreiber_inhalt` text NOT NULL ; ##b_dump## 