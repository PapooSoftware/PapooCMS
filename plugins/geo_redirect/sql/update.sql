CREATE TABLE `XXX_plugin_geo_redirect_domain` (`geo_redirect_domain__id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`geo_redirect_domain__id`)) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_plugin_geo_redirect_domain` ADD `geo_redirect_domain__domainname` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_geo_redirect_domain` ADD `geo_redirect_domain__sprache_die_erzwungen_werden_soll` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_geo_redirect_domain` ADD `geo_redirect_domain__countrycode` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_geo_redirect_domain` ADD `geo_redirect_domain__countrycodeip` VARCHAR( 255 ) NOT NULL; ##b_dump##
CREATE TABLE `XXX_plugin_geo_redirect_ip` (`geo_redirect_ip_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`geo_redirect_ip_id`)) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_plugin_geo_redirect_ip` ADD `geo_redirect_ip_sprache_erzwingen` VARCHAR( 255 ) NOT NULL; ##b_dump##
