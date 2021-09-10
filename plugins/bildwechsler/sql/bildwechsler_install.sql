DROP TABLE IF EXISTS `XXX_bildwechsler`; ##b_dump##
CREATE TABLE `XXX_bildwechsler` (
  `bw_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bw_menu_id` bigint(20) DEFAULT NULL,
  `bw_order_id` int(11) DEFAULT NULL,
  `bw_bild` text,
  `bw_link` text,
  `bw_text` text,
  `bw_ueberschrift` text,
  `bw_noch_mehr_text` text,
  `bw_extra_link` text,
  `bw_extra_link_text` text,
  `bw_lang_id` int(11) NOT NULL,
  PRIMARY KEY (`bw_id`, `bw_lang_id`),
  KEY `bw_menuid` (`bw_menu_id`),
  KEY `bw_order_id` (`bw_order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 ; ##b_dump##
DROP TABLE IF EXISTS `XXX_bildwechsler_config`; ##b_dump##
CREATE TABLE `XXX_bildwechsler_config` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `bw_template_name` VARCHAR(32),
  `use_ancestors_as_fallback` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1; ##b_dump##
INSERT INTO `XXX_bildwechsler_config` SET id = 1, bw_template_name='tm-slider', use_ancestors_as_fallback = 1; ##b_dump##
