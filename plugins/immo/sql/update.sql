CREATE TABLE `XXX_plugin_zentrale_inhalte` (`plugin_zentrale_inhalte_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`plugin_zentrale_inhalte_id`)) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_plugin_zentrale_inhalte` ADD `plugin_zentrale_inhalte_name_der_domain` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_zentrale_inhalte` ADD `plugin_zentrale_inhalte_domainkey` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_zentrale_inhalte` ADD `plugin_zentrale_inhalte_platzhalter` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_flex2sitemap` ADD `plugin_flex2sitemap_menupunkte` VARCHAR( 255 ) NOT NULL; ##b_dump##
CREATE TABLE `XXX_plugin_immo` (`plugin_immo_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`plugin_immo_id`)) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_plugin_immo` ADD `plugin_immo_objekte` TEXT; ##b_dump##
CREATE TABLE `XXX_plugin_immo_ausgabe` (`plugin_immo_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`plugin_immo_id`)) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_plugin_immo_ausgabe` ADD `plugin_immo_uebersicht` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_immo_ausgabe` ADD `plugin_immo_einzelansicht` TEXT; ##b_dump##
CREATE TABLE `XXX_plugin_immo_config` (`plugin_immo_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`plugin_immo_id`)) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_plugin_immo_config` ADD `plugin_immo_userid` TEXT; ##b_dump##
CREATE TABLE `XXX_plugin_immo_lookup` (`plugin_immo_lookup_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`plugin_immo_lookup_id`)) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_plugin_immo_lookup` ADD `plugin_immo_lookup_toplisting` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_immo_lookup` ADD `plugin_immo_lookup_sperren1` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_immo_lookup` ADD `plugin_immo_lookup_ref` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_immo_lookup` ADD `plugin_immo_lookup_typ_immosuche` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_immo_lookup` ADD `plugin_immo_lookup_suche` TEXT; ##b_dump##
