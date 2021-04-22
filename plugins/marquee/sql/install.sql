DROP TABLE IF EXISTS `XXX_papoo_marquee`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_marquee` (
  `marquee_lang` char(3) NOT NULL,
  `marquee_content` varchar(2000) NOT NULL,
  `marquee_class` varchar(63) NOT NULL default 'marquee',
  `marquee_behavior` ENUM('scroll', 'alternate', 'slide') NOT NULL default 'scroll',
  `marquee_direction` ENUM('right', 'left', 'up', 'down') NOT NULL default 'right',
  `marquee_scrollamount` smallint(5) NOT NULL default 1,
  PRIMARY KEY  (`marquee_lang`)
) ; ##b_dump##

INSERT INTO `XXX_papoo_marquee` (marquee_lang, marquee_content) VALUES
	("de", "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat."),
	("en", "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.")
	; ##b_dump##