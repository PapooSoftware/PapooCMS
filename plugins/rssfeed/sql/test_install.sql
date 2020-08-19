DROP TABLE IF EXISTS `XXX_rss_feed_config`; ##b_dump##
CREATE TABLE `XXX_rss_feed_config` (
  `rss_feed_id` int(11) NOT NULL  ,
  `rss_feedtitle` varchar(255) NULL ,
  `rss_feeddesc` varchar(255) NULL ,
  `rss_feedcount` varchar(255) NULL ,
  `rss_feedlang` varchar(255) NULL ,
  `rss_feedprefix` text NULL  
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_rss_feed_config` 
(rss_feed_id, rss_feedtitle, rss_feeddesc, rss_feedcount,rss_feedlang,rss_feedprefix) 
VALUES ('1', 'TSEnergie', 'Software TSEnergie', '15','1','http://') ; ##b_dump##


DROP TABLE IF EXISTS `XXX_rss_feed_url`; ##b_dump##
CREATE TABLE `XXX_rss_feed_url` (
  `rss_feed_id` int(11) NOT NULL auto_increment ,
  `rss_feed_url` varchar(255) NULL ,
  `rss_feed_short` varchar(10) NULL, 
  PRIMARY KEY (`rss_feed_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_rss_feed_url` 
(rss_feed_id,rss_feed_url, rss_feed_short) 
VALUES ('1','http://faq.tschoessow.org/feed/news/rss.php', 'FEED1'); ##b_dump##

INSERT INTO `XXX_rss_feed_url` 
(rss_feed_id,rss_feed_url, rss_feed_short) 
VALUES ('2','http://www.tschoessow.org/feed', 'FEED2'); ##b_dump##