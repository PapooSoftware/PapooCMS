DROP TABLE IF EXISTS `XXX_papoo_mv_click_count`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_mv_click_count` (
  `mv_id` int(11) NOT NULL,
  `mv_content_id` int(11) NOT NULL,
  `counter` int(11) NOT NULL,
  UNIQUE KEY `mv_id` (`mv_id`,`mv_content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1; ##b_dump##