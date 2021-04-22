DROP TABLE IF EXISTS `XXX_newsgrabber`; ##b_dump##
CREATE TABLE `XXX_newsgrabber` (
  `newsgrabber_id` int(11) NOT NULL,
  `newsgrabber_lang` int(11) NOT NULL,
  `newsfile` text NULL ,
  `enable_caching` text NULL ,
  `cache_file` longtext NULL ,
  `cache_file_time` text NULL ,
  `cache_refresh_time` text NULL ,
  `max_news` text NULL ,
  `kommentieren` text NULL ,
  `schlagzeile` text NULL ,
  `datumanzeigen` text NULL ,
  `newsfilequelle` text NULL ,
  `fehleranzeige` text NULL ,
  `allowable_tags` text NULL ,
  `menuid` int(11) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`newsgrabber_id`, `newsgrabber_lang`)
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_newsgrabber` (newsgrabber_id, newsfile, enable_caching, cache_file, cache_file_time, cache_refresh_time, max_news, kommentieren, schlagzeile, datumanzeigen, newsfilequelle, fehleranzeige, allowable_tags,newsgrabber_lang) 
VALUES ('1', 'http://www.golem.de/rss.php?feed=RSS2.0', 'true', '', '0', '20', '15', 'false', 'true', 'false', 'true', 'true', '<a><abbr><acronym><b><big><br><bdo><center><cite><code><del><dfn><div><em><h3><h4><h5><h6><i><ins><kbd><li><ol><samp><small><span><strike><strong><sub><sup><table><td><th><tr><tt><u><ul><var>','1') ; ##b_dump##