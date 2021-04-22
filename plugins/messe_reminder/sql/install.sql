DROP TABLE IF EXISTS `XXX_papoo_messe_reminder`; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_messe_reminder`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_messe_reminder` (
  `messe_id` int(11) NOT NULL,
  `enabled` boolean NOT NULL DEFAULT TRUE,
  `email` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `interval` int(11) NOT NULL,
  `last_timestamp` int(11) DEFAULT NULL,
  PRIMARY KEY  (`messe_id`)
) ; ##b_dump##

INSERT INTO `XXX_papoo_messe_reminder` (`messe_id`, `email`, `subject`, `text`, `interval`)
	VALUES (0, NULL, 'Ihr Messeeintrag: #messe#', '', 3);