CREATE TABLE `XXX_plugin_cotent_client` (`plugin_cotent_client_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`plugin_cotent_client_id`)) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_plugin_cotent_client` ADD `plugin_cotent_client_url_der_zentrale` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_cotent_client` ADD `plugin_cotent_client_token_key_kommt_aus_der_zentrale` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_cotent_client` ADD `plugin_cotent_client_domain_key` TEXT; ##b_dump##
