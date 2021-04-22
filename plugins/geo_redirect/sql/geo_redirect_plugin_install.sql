DROP TABLE IF EXISTS `XXX_plugin_geo_redirect_domain`; ##b_dump##
CREATE TABLE `XXX_plugin_geo_redirect_domain` (
  `geo_redirect_domain__id` int(11) NOT NULL AUTO_INCREMENT,
  `geo_redirect_domain__domainname` text,
  `geo_redirect_domain__sprache_die_erzwungen_werden_soll` varchar(255) NOT NULL,
  PRIMARY KEY (`geo_redirect_domain__id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_geo_redirect_domain` SET geo_redirect_domain__id='6', geo_redirect_domain__domainname='papoo.ch', geo_redirect_domain__sprache_die_erzwungen_werden_soll='2'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_geo_redirect_ip`; ##b_dump##
CREATE TABLE `XXX_plugin_geo_redirect_ip` (
  `geo_redirect_ip_id` int(11) NOT NULL AUTO_INCREMENT,
  `geo_redirect_ip_sprache_erzwingen` varchar(255) NOT NULL,
  `geo_redirect_ip_countrycodeip` varchar(255) NOT NULL,
  PRIMARY KEY (`geo_redirect_ip_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
