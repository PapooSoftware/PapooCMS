CREATE TABLE `XXX_plugin_admin_message` (`plugin_admin_message_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`plugin_admin_message_id`)) ENGINE=MyISAM; ##b_dump##
ALTER TABLE `XXX_plugin_admin_message` ADD `plugin_admin_message_email_adressen` TEXT; ##b_dump##
