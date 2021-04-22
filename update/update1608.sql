ALTER TABLE `XXX_papoo_user` CHANGE `password` `password` VARCHAR(255) NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_message` ADD `email` VARCHAR(78) NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_message` ADD `artikelurl` VARCHAR(255) NOT NULL; ##b_dump##
ALTER TABLE `XXX_bildwechsler` ADD `bw_ueberschrift` text default NULL; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_bildwechsler_config` ( bw_template_name text DEFAULT NULL ) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_bildwechsler_config` SET bw_template_name='jquerytools' ; ##b_dump##