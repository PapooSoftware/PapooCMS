DROP TABLE IF EXISTS `XXX_plugin_piwik`; ##b_dump##
CREATE TABLE `XXX_plugin_piwik` (
  `piwik_id` int(11) NOT NULL auto_increment ,
  `piwik_tracking_code` text NULL ,
  PRIMARY KEY (`piwik_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_plugin_piwik` 
(piwik_id) 
VALUES ('1') ; ##b_dump##