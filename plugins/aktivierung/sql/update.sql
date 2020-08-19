CREATE TABLE `XXX_plugin_aktivierung_config` (`aktiv_plugin_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`aktiv_plugin_id`)) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_plugin_aktivierung_config` ADD `aktiv_plugin_ihre_rechnungsnummer` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_aktivierung_config` ADD `aktiv_plugin_installationen_auf_localhost` TEXT; ##b_dump##
