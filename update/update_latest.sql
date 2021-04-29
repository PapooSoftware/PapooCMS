ALTER TABLE `XXX_papoo_daten` ADD `smtp_active` tinyint(1) NOT NULL DEFAULT 0; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `smtp_host` varchar(255) NOT NULL DEFAULT ''; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `smtp_port` smallint UNSIGNED NOT NULL DEFAULT 0; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `smtp_user` varchar(255) NOT NULL DEFAULT ''; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `smtp_pass` varchar(255) NOT NULL DEFAULT ''; ##b_dump##