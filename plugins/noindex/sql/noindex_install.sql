DROP TABLE IF EXISTS `XXX_noindex_article_ids`; ##b_dump##
CREATE TABLE `XXX_noindex_article_ids` (
  `article_id` int(11) NOT NULL,
  PRIMARY KEY (`article_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ; ##b_dump##

DROP TABLE IF EXISTS `XXX_noindex_article_pages`; ##b_dump##
CREATE TABLE `XXX_noindex_article_pages` (
  `page` int(11) NOT NULL,
  PRIMARY KEY (`page`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ; ##b_dump##

DROP TABLE IF EXISTS `XXX_noindex_forum_msgs`; ##b_dump##
CREATE TABLE `XXX_noindex_forum_msgs` (
  `msgid` int(11) NOT NULL,
  PRIMARY KEY (`msgid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ; ##b_dump##

DROP TABLE IF EXISTS `XXX_noindex_forum_pages`; ##b_dump##
CREATE TABLE `XXX_noindex_forum_pages` (
  `forumid` int(11) NULL,
  `page` int(11) NULL
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ; ##b_dump##

DROP TABLE IF EXISTS `XXX_noindex_menu`; ##b_dump##
CREATE TABLE `XXX_noindex_menu` (
  `menuid_id` varchar(255) NOT NULL,
  PRIMARY KEY(`menuid_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ; ##b_dump##

DROP TABLE IF EXISTS `XXX_noindex_dateinamen`; ##b_dump##
CREATE TABLE `XXX_noindex_dateinamen` (
  `filename` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`filename`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ; ##b_dump##

DROP TABLE IF EXISTS `XXX_noindex_templates`; ##b_dump##
CREATE TABLE `XXX_noindex_templates` (
  `template` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`template`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ; ##b_dump##

DROP TABLE IF EXISTS `XXX_noindex_urls`; ##b_dump##
CREATE TABLE `XXX_noindex_urls` (
  `url` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`url`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ; ##b_dump##