CREATE TABLE `XXX_plugin_koobi_config` (`redirectkoobi_plugin_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`redirectkoobi_plugin_id`)) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_plugin_koobi_config` ADD `redirectkoobi_plugin_benutzername` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_koobi_config` ADD `redirectkoobi_plugin_passwort` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_koobi_config` ADD `redirectkoobi_plugin_servername` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_koobi_config` ADD `redirectkoobi_plugin_datenbankname` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_koobi_config` ADD `redirectkoobi_plugin_sprache_auswhlen` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_koobi_config` ADD `redirectkoobi_plugin_html_entfernen_beim_import` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_koobi_config` ADD `redirectkoobi_plugin_url_der_seite_data` TEXT; ##b_dump##
