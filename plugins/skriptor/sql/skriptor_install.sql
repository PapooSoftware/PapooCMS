DROP TABLE IF EXISTS `XXX_plugin_skriptor`; ##b_dump##

CREATE TABLE `XXX_plugin_skriptor` (
      `id` int(11) NOT NULL auto_increment ,
      `js_active` TINYINT NULL,
      `js_compress` TINYINT NULL,
      `js_remove_comments` TINYINT NULL,
      `css_active` TINYINT NULL,
      `css_compress` TINYINT NULL,
      `css_remove_comments` TINYINT NULL,
      `site_speed_plugin_leerzeichen_tabs` VARCHAR( 255 ) NOT NULL,
      `site_speed_plugin_komplet_html` VARCHAR( 255 ) NOT NULL,
      `site_speed_plugin_bilder_komprimieren` VARCHAR( 255 ) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_plugin_skriptor` 
(id,js_active,js_compress,js_remove_comments,css_active,css_compress,css_remove_comments) 
VALUES ('1','1','1','1','1','1','1') ; ##b_dump##