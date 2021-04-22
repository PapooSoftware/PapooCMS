CREATE TABLE `XXX_plugin_bulk_import` (`akquise_bulk_import_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`akquise_bulk_import_id`)) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_plugin_bulk_import` ADD `akquise_bulk_import_falls_das_verzeichnis` TEXT; ##b_dump##
CREATE TABLE `XXX_plugin_flex2sitemap` (`plugin_flex2sitemap_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`plugin_flex2sitemap_id`)) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_plugin_flex2sitemap` ADD `plugin_flex2sitemap_in_sitemap_erscheinen` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_flex2sitemap` ADD `plugin_flex2sitemap_feld_auswhlen` VARCHAR( 255 ) NOT NULL; ##b_dump##
