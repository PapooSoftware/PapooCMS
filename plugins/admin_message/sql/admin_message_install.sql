DROP TABLE IF EXISTS `XXX_plugin_admin_message`; ##b_dump##
CREATE TABLE `XXX_plugin_admin_message` (
  `plugin_admin_message_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_admin_message_email_adressen` text,
  PRIMARY KEY (`plugin_admin_message_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_admin_message` SET plugin_admin_message_id='1', plugin_admin_message_email_adressen='info@papoo.de\r\ntest@kobos.de'  ; ##b_dump##

