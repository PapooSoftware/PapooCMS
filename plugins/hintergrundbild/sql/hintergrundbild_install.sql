DROP TABLE IF EXISTS `XXX_plugin_background_lookup_bilder_menuepunkte`; ##b_dump##
CREATE TABLE `XXX_plugin_background_lookup_bilder_menuepunkte` (
  `menu_id` INT(11) NOT NULL,
  `bilddatei` TEXT NOT NULL,
  PRIMARY KEY (menu_id)
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_background_config`; ##b_dump##
CREATE TABLE `XXX_plugin_background_config` (
  `vererbung` BOOL NOT NULL,
  `css_path` TEXT
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_plugin_background_config` (vererbung, css_path)
VALUES (TRUE, 'body') ; ##b_dump##