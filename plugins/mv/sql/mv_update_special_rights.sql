DROP TABLE IF EXISTS `xxx_papoo_mv_special_rights1`; ##b_dump##
CREATE TABLE IF NOT EXISTS `xxx_papoo_mv_special_rights1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group` (`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1; ##b_dump##

DROP TABLE IF EXISTS `xxx_papoo_mv_special_rights2`; ##b_dump##
CREATE TABLE IF NOT EXISTS `xxx_papoo_mv_special_rights2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group` (`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1; ##b_dump##