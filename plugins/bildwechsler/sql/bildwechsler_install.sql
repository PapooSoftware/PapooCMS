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
  PRIMARY KEY (`bw_id`),
  KEY `bw_menuid` (`bw_menu_id`),
  KEY `bw_order_id` (`bw_order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_bildwechsler` SET bw_id='1', bw_menu_id='0', bw_order_id='10', bw_bild='src=\"http://lorempixel.com/510/250/animals/1\" alt=\"Ein Tierbild (1)\" title=\"Ein Tierbild (1)\"', bw_ueberschrift='Tierbild 1', bw_text='Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', bw_link='href=\"http://lorempixel.com\" target=\"_blank\"' ; ##b_dump##
INSERT INTO `XXX_bildwechsler` SET bw_id='2', bw_menu_id='0', bw_order_id='20', bw_bild='src=\"http://lorempixel.com/510/250/animals/2\" alt=\"Ein Tierbild (2)\" title=\"Ein Tierbild (2)\"', bw_ueberschrift='Tierbild 1', bw_text='Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', bw_link='href=\"http://lorempixel.com\" target=\"_blank\"' ; ##b_dump##
INSERT INTO `XXX_bildwechsler` SET bw_id='3', bw_menu_id='0', bw_order_id='30', bw_bild='src=\"http://lorempixel.com/510/250/animals/3\" alt=\"Ein Tierbild (3)\" title=\"Ein Tierbild (3)\"', bw_ueberschrift='Tierbild 1', bw_text='Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', bw_link='href=\"http://lorempixel.com\" target=\"_blank\"' ; ##b_dump##
DROP TABLE IF EXISTS `XXX_bildwechsler_config`; ##b_dump##
CREATE TABLE `XXX_bildwechsler_config` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `bw_template_name` VARCHAR(32),
  `use_ancestors_as_fallback` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1; ##b_dump##
INSERT INTO `XXX_bildwechsler_config` SET id = 1, bw_template_name='tm-slider', use_ancestors_as_fallback = 1; ##b_dump##
