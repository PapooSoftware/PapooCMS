 DROP TABLE IF EXISTS `XXX_papoo_rating_plugin`; ##b_dump##
 CREATE TABLE IF NOT EXISTS `XXX_papoo_rating_plugin` (
  `rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `uri` text NOT NULL,
  `page_title` text NOT NULL,
  `rating_count` int(11) NOT NULL,
  `one_star` int(111) NOT NULL,
  `two_stars` int(11) NOT NULL,
  `three_stars` int(11) NOT NULL,
  `four_stars` int(11) NOT NULL,
  `five_stars` int(11) NOT NULL,
  `dont_show` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rating_id`),
  UNIQUE KEY `uri` (`uri`(333))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; ##b_dump##

 DROP TABLE IF EXISTS `XXX_papoo_rating_time_locks`; ##b_dump##
 CREATE TABLE IF NOT EXISTS `XXX_papoo_rating_time_locks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `uri` text NOT NULL,
  `ts` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_uri` (`ip`,`uri`(318))
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

 DROP TABLE IF EXISTS `XXX_papoo_rating_config`; ##b_dump##
 CREATE TABLE IF NOT EXISTS `XXX_papoo_rating_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time_lock` int(4) NOT NULL,
  `rating_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ##b_dump##

INSERT INTO `XXX_papoo_rating_config` (`id`, `time_lock`, `rating_active`) VALUES (1, 120, 1); ##b_dump##