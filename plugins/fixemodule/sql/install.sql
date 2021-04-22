DROP TABLE IF EXISTS `XXX_plugin_fixemodule_felder`; ##b_dump##
CREATE TABLE `XXX_plugin_fixemodule_felder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `modul_id` int(10) unsigned NOT NULL,
  `feldtyp_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_fixemodule_feldinhalte`; ##b_dump##
CREATE TABLE `XXX_plugin_fixemodule_feldinhalte` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inhalt` text NOT NULL,
  `feld_id` int(10) unsigned NOT NULL,
  `sprache` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_fixemodule_feldtypen`; ##b_dump##
CREATE TABLE `XXX_plugin_fixemodule_feldtypen` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_plugin_fixemodule_feldtypen` SET `name`='Text'; ##b_dump##
INSERT INTO `XXX_plugin_fixemodule_feldtypen` SET `name`='Bild'; ##b_dump##
INSERT INTO `XXX_plugin_fixemodule_feldtypen` SET `name`='HTML'; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_fixemodule_module`; ##b_dump##
CREATE TABLE `XXX_plugin_fixemodule_module` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `beschreibung` text NOT NULL,
  `html` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
