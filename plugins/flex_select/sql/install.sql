DROP TABLE IF EXISTS `XXX_papoo_mv_select_linking`; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_mv_select_linking_items`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_mv_select_linking` (
  `link_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `field1_id` int(11) NOT NULL,
  `field2_id` int(11) NOT NULL,
  PRIMARY KEY  (`link_id`),
  UNIQUE INDEX (`form_id`, `field2_id`)
) ; ##b_dump##

CREATE TABLE IF NOT EXISTS `XXX_papoo_mv_select_linking_items` (
  `link_id` int(11) NOT NULL,
  `lookup1_id` int(11) NOT NULL,
  `lookup2_id` int(11) NOT NULL,
  PRIMARY KEY  (`link_id`, `lookup1_id`, `lookup2_id`)
) ; ##b_dump##

ALTER TABLE `XXX_papoo_mv_select_linking_items` 
	ADD FOREIGN KEY (`link_id`) REFERENCES
	`XXX_papoo_mv_select_linking` (`link_id`)
    ON DELETE CASCADE ON UPDATE CASCADE; ##b_dump##