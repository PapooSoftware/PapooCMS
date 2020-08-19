CREATE TABLE IF NOT EXISTS `XXX_bildwechsler_config`
(
	`id` INT NOT NULL AUTO_INCREMENT,
	`bw_template_name` VARCHAR(32),
	`use_ancestors_as_fallback` TINYINT(1) NOT NULL DEFAULT 1,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_bildwechsler_config` SET id = 1, bw_template_name='tm-slider', use_ancestors_as_fallback = 1; ##b_dump##

ALTER  TABLE `XXX_bildwechsler`
ADD `bw_text` text default NULL,
ADD `bw_noch_mehr_text` text default NULL,
ADD `bw_lang_id` int(11) default NULL,
ADD `bw_extra_link` text default NULL,
ADD `bw_extra_link_text` text default NULL; ##b_dump##

ALTER TABLE `XXX_bildwechsler_config` ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST; ##b_dump##
ALTER TABLE `XXX_bildwechsler_config` ADD COLUMN `use_ancestors_as_fallback` TINYINT(1) NOT NULL DEFAULT 1; ##b_dump##
DELETE FROM `XXX_bildwechsler_config` WHERE `id` > 1; ##b_dump##
