DROP TABLE IF EXISTS `XXX_plugin_flex2sitemap`; ##b_dump##
CREATE TABLE `XXX_plugin_flex2sitemap` (
  `plugin_flex2sitemap_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_flex2sitemap_in_sitemap_erscheinen` varchar(255) NOT NULL,
  `plugin_flex2sitemap_feld_auswhlen` varchar(255) NOT NULL,
  `plugin_flex2sitemap_menuid` int(11) NOT NULL,
  `plugin_flex2sitemap_mvid` int(11) NOT NULL,
  PRIMARY KEY (`plugin_flex2sitemap_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=latin1 ; ##b_dump##
 