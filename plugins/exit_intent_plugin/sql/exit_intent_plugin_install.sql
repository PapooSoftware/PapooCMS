DROP TABLE IF EXISTS `XXX_plugin_exit_intent_plugin`; ##b_dump##
CREATE TABLE `XXX_plugin_exit_intent_plugin` (
  `ei_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ei_link` text,
  `ei_hoehe` text,
  `ei_breite` text,
  `ei_delay` text,
  `ei_auslaufdatum` text,
  `ei_css_datei_link` text,
  `ei_cookie_name` text,
  `ei_name` text,
  PRIMARY KEY (`ei_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_exit_intent_plugin` SET `ei_id`='10',`ei_name`='Beispiel Popup',`ei_link`='<h1>Eine Überschrift im Popup</h1>\r\nGefolgt von einem Bild : <br />\r\n<a href=\"#link\" title=\"Papoo Software\"><img src=\"/images/crop_10_logo_green.png\" /></a><br/>\r\nund einer Liste : <br/>\r\n<ul>\r\n<li>1. eins</li>\r\n<li>2. zwei</li>\r\n<li>3. drei</li>\r\n<li>4. vier</li>\r\n<li>5. fünf</li>\r\n</ul>', `ei_hoehe`='593', `ei_breite`='1046', `ei_delay`='2', `ei_auslaufdatum`='0', `ei_css_datei_link`='', `ei_cookie_name`='bioep_shown_4f5343d8e69c44462f58e7b224cca7cb'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_exit_intent_plugin_lookup`; ##b_dump##
CREATE TABLE `XXX_plugin_exit_intent_plugin_lookup` (
  `ei_id` bigint(20) NOT NULL,
  `menu_id` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_exit_intent_plugin_lookup` SET `ei_id`='10', `menu_id`='1'  ; ##b_dump##