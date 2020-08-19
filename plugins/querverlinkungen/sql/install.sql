DROP TABLE IF EXISTS `XXX_querverlinkungen_menuepunkte_menuepunkte`; ##b_dump##
CREATE TABLE `XXX_querverlinkungen_menuepunkte_menuepunkte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `origin_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM; ##b_dump##

DROP TABLE IF EXISTS `XXX_querverlinkungen_artikel_artikel`; ##b_dump##
CREATE TABLE `XXX_querverlinkungen_artikel_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `origin_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM; ##b_dump##