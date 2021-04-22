ALTER TABLE `XXX_plugin_leadtracker` ADD `leadtracker_form_id` INT( 11 ) NOT NULL; ##b_dump##

CREATE TABLE IF NOT EXISTS `XXX_plugin_leadtracker_lookup_form_uid_leadid` (
  `leadtracker_uid_id` int(11) NOT NULL AUTO_INCREMENT,
  `leadtracker_uid` varchar(255) NOT NULL,
  `leadtracker_form_id` int(11) DEFAULT NULL,
  `leadtracker_lead_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`leadtracker_uid_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;ALTER TABLE `XXX_plugin_leadtracker` ADD `leadtracker_formular_fr_neuauswahl_form` VARCHAR( 255 ) NOT NULL; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker` ADD `leadtracker_formular_direkt_neu_laden` VARCHAR( 255 ) NOT NULL; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker`
	ADD COLUMN `leadtracker_autofill` BOOLEAN DEFAULT TRUE ; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker_lookup_form_uid_leadid`
	ADD COLUMN `leadtracker_time` TIMESTAMP ; ##b_dump##

CREATE TABLE IF NOT EXISTS `XXX_plugin_leadtracker_incremental` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`last_tracking_id` INT NOT NULL,
	`last_form_track_id` INT NOT NULL,
	PRIMARY KEY (`id`)
) ; ##b_dump##

CREATE TABLE IF NOT EXISTS `XXX_plugin_leadtracker_statistics` (
	`cookie_id` varchar(250) NOT NULL,
	`mail` varchar(255) NULL,
	`count_visits` INT UNSIGNED NOT NULL DEFAULT 0,
	`count_forms` INT UNSIGNED NOT NULL DEFAULT 0,
	`count_downloads` INT UNSIGNED  NOT NULL DEFAULT 0,
	`first_visit` INT UNSIGNED NOT NULL,
	`last_visit` INT UNSIGNED NOT NULL,
	`last_download` INT UNSIGNED NULL,
	PRIMARY KEY (`cookie_id`)
) ; ##b_dump##

CREATE TABLE IF NOT EXISTS `XXX_plugin_leadtracker` (`leadtracker__id` int(11) NOT NULL auto_increment,
				  							PRIMARY KEY (`leadtracker__id`)) ENGINE=MyISAM; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker` ADD `leadtracker__die_downloaddatei` VARCHAR( 255 ) NOT NULL; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker` ADD `leadtracker__verknpfen_mit` VARCHAR( 255 ) NOT NULL; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker` ADD `leadtracker_kann_muss` VARCHAR( 255 ) NOT NULL; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker` ADD `leadtracker_follow_up_doi_nutzen` VARCHAR( 255 ) NOT NULL; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker` ADD `leadtracker_follow_up_mails_betreff_fum` TEXT; ##b_dump##

CREATE TABLE IF NOT EXISTS `XXX_plugin_leadtracker_follow_up_mails` (`leadtracker_id` int(11) NOT NULL auto_increment,
				  							PRIMARY KEY (`leadtracker_id`)) ENGINE=MyISAM; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker_follow_up_mails` ADD `leadtracker_betreff_fum` TEXT; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker_follow_up_mails` ADD `leadtracker_mail_inhalt_text` TEXT; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker_follow_up_mails` ADD `leadtracker_mail_inhalt_html` TEXT; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker_follow_up_mails` ADD `leadtracker_id_von_follow_element` VARCHAR( 255 ) NOT NULL; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker_follow_up_mails` ADD `leadtracker_type_von_follow_element` VARCHAR( 255 ) NOT NULL; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker_follow_up_mails` ADD `leadtracker_versand_nach` TEXT; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker_follow_up_mails` ADD `leadtracker_fum_form_id` int(11) NULL; ##b_dump##

ALTER TABLE `XXX_plugin_leadtracker_follow_up_mails` ADD `leadtracker_checkreplace` int(11) NULL DEFAULT NULL; ##b_dump#

CREATE TABLE IF NOT EXISTS `XXX_plugin_leadtracker_incremental` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_tracking_id` INT NOT NULL,
  `last_form_track_id` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=latin1 ; ##b_dump##

CREATE TABLE IF NOT EXISTS `XXX_plugin_leadtracker_statistics` (
  `cookie_id` varchar(250) NOT NULL,
  `mail` varchar(255) NULL,
  `count_visits` INT UNSIGNED NOT NULL DEFAULT 0,
  `count_forms` INT UNSIGNED NOT NULL DEFAULT 0,
  `count_downloads` INT UNSIGNED  NOT NULL DEFAULT 0,
  `first_visit` INT UNSIGNED NOT NULL,
  `last_visit` INT UNSIGNED NOT NULL,
  `last_download` INT UNSIGNED NULL,
  PRIMARY KEY (`cookie_id`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=latin1 ; ##b_dump##

CREATE TABLE IF NOT EXISTS `XXX_plugin_leadtracker_lookup_lead_fum` (
  `lookup_lead_fum_id` int(11) NOT NULL AUTO_INCREMENT,
  `lookup_lead_fum_lead_id` int(11) NOT NULL,
  `lookup_lead_fum_fum_id` int(11) NOT NULL,
  `lookup_lead_fum_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lookup_lead_fum_sentstamp` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`lookup_lead_fum_id`),
  KEY `lookup_lead_fum_lead_id` (`lookup_lead_fum_lead_id`),
  KEY `lookup_lead_fum_fum_id` (`lookup_lead_fum_fum_id`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=latin1 ; ##b_dump##

