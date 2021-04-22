DROP TABLE IF EXISTS `XXX_plugin_adwords_kw_script`; ##b_dump##
CREATE TABLE `XXX_plugin_adwords_kw_script` (
  `script` VARCHAR(1000) NOT NULL,
  `replace_number` VARCHAR(16) NOT NULL,
  `id` VARCHAR(1) NOT NULL
) ; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_adwords_kw_list`; ##b_dump##
CREATE TABLE `XXX_plugin_adwords_kw_list` (
  `placeholder` VARCHAR(30) NOT NULL,
  `keyword` VARCHAR(100) NOT NULL,
  `parameter` VARCHAR(100) NOT NULL,
  UNIQUE (`placeholder`)
) ; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_adwords_tel_list`; ##b_dump##
CREATE TABLE `XXX_plugin_adwords_tel_list` (
  `tel_placeholder` VARCHAR(30) NOT NULL,
  `tel_org` VARCHAR(100) NOT NULL,
  `tel_adwords` VARCHAR(100) NOT NULL,
  UNIQUE (`tel_placeholder`)
) ; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_adwords_kw_log`; ##b_dump##
CREATE TABLE `XXX_plugin_adwords_kw_log` (
  `keyword` VARCHAR(30) NOT NULL,
  `url` VARCHAR(100) NOT NULL,
  `date_time` TIMESTAMP NOT NULL,
  `counter` INT UNSIGNED NOT NULL
) ; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_adwords_kw_ab`; ##b_dump##
CREATE TABLE `XXX_plugin_adwords_kw_ab` (
  `script` VARCHAR(1000) NOT NULL,
  `article_url` VARCHAR(160) NOT NULL,
  `id` VARCHAR(1) NOT NULL
) ; ##b_dump##

INSERT INTO `XXX_plugin_adwords_kw_script` (`script`, `replace_number`, `id`)
VALUES ('','', '1') ; ##b_dump##

INSERT INTO `XXX_plugin_adwords_kw_ab` (`script`, `article_url`, `id`)
VALUES ('','', '1') ; ##b_dump##