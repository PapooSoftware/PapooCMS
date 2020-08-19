DROP TABLE IF EXISTS `XXX_cookieplugin_settings`; ##b_dump##
CREATE TABLE `XXX_cookieplugin_settings` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ; ##b_dump##

INSERT INTO `XXX_cookieplugin_settings` SET `name`='message', `value`='Diese Webseite benutzt Cookies um Ihnen das beste Erlebnis beim benutzen der Webseite bieten zu können.'; ##b_dump##
INSERT INTO `XXX_cookieplugin_settings` SET `name`='buttontext', `value`='OK'; ##b_dump##
INSERT INTO `XXX_cookieplugin_settings` SET `name`='learnmore', `value`='Mehr Informationen'; ##b_dump##
INSERT INTO `XXX_cookieplugin_settings` SET `name`='link', `value`='http://www.cookiechoices.org/'; ##b_dump##
INSERT INTO `XXX_cookieplugin_settings` SET `name`='theme', `value`='dark-bottom'; ##b_dump##
