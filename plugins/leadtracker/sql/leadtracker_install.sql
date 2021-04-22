DROP TABLE IF EXISTS `XXX_plugin_leadtracker`; ##b_dump##
CREATE TABLE `XXX_plugin_leadtracker` (
  `leadtracker_id` int(11) NOT NULL AUTO_INCREMENT,
  `leadtracker_die_downloaddatei` varchar(255) NOT NULL,
  `leadtracker_verknpfen_mit` varchar(255) NOT NULL,
  `leadtracker_kann_muss` varchar(255) NOT NULL,
  `leadtracker__die_downloaddatei` varchar(255) NOT NULL,
  `leadtracker__verknpfen_mit` varchar(255) NOT NULL,
  `leadtracker_follow_up_doi_nutzen` varchar(255) NOT NULL,
  `leadtracker_follow_up_mails_betreff_fum` text,
  `leadtracker_form_id` int(11) NOT NULL,
  `leadtracker_formular_fr_neuauswahl_form` varchar(255) NOT NULL,
  `leadtracker_autofill` BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (`leadtracker_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1 ; ##b_dump##
ALTER TABLE `XXX_plugin_leadtracker` ADD `leadtracker_formular_direkt_neu_laden` VARCHAR( 255 ) NOT NULL; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_leadtracker_bfp`; ##b_dump##
CREATE TABLE `XXX_plugin_leadtracker_bfp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(60) NOT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(39) DEFAULT NULL,
  `port` smallint(16) unsigned DEFAULT NULL,
  `http_referrer` varchar(255) DEFAULT NULL,
  `http_user_agent` varchar(1023) DEFAULT NULL,
  `http_accept` varchar(1023) DEFAULT NULL,
  `http_accept_charset` varchar(63) DEFAULT NULL,
  `http_accept_encoding` varchar(63) DEFAULT NULL,
  `http_accept_language` varchar(63) DEFAULT NULL,
  `png_id` int(10) unsigned DEFAULT NULL,
  `document_referrer` varchar(255) DEFAULT NULL,
  `navigator_appCodeName` varchar(63) DEFAULT NULL,
  `navigator_appName` varchar(63) DEFAULT NULL,
  `navigator_appVersion` varchar(127) DEFAULT NULL,
  `navigator_cookieEnabled` tinyint(1) DEFAULT NULL,
  `navigator_language` varchar(15) DEFAULT NULL,
  `navigator_platform` varchar(15) DEFAULT NULL,
  `navigator_userAgent` varchar(1023) DEFAULT NULL,
  `screen_width` smallint(5) unsigned DEFAULT NULL,
  `screen_height` smallint(5) unsigned DEFAULT NULL,
  `screen_avail_width` smallint(5) unsigned DEFAULT NULL,
  `screen_avail_height` smallint(5) unsigned DEFAULT NULL,
  `color_depth` tinyint(3) unsigned DEFAULT NULL,
  `dpi_x` smallint(5) unsigned DEFAULT NULL,
  `dpi_y` smallint(5) unsigned DEFAULT NULL,
  `devicePixelRatio` smallint(5) unsigned DEFAULT NULL,
  `timezone_offset` smallint(6) DEFAULT NULL,
  `java_enabled` tinyint(1) unsigned DEFAULT NULL,
  `color_activeborder` int(24) unsigned DEFAULT NULL,
  `color_activecaption` int(24) unsigned DEFAULT NULL,
  `color_appworkspace` int(24) unsigned DEFAULT NULL,
  `color_background` int(24) unsigned DEFAULT NULL,
  `color_buttonface` int(24) unsigned DEFAULT NULL,
  `color_buttonhighlight` int(24) unsigned DEFAULT NULL,
  `color_buttonshadow` int(24) unsigned DEFAULT NULL,
  `color_buttontext` int(24) unsigned DEFAULT NULL,
  `color_captiontext` int(24) unsigned DEFAULT NULL,
  `color_graytext` int(24) unsigned DEFAULT NULL,
  `color_highlight` int(24) unsigned DEFAULT NULL,
  `color_highlighttext` int(24) unsigned DEFAULT NULL,
  `color_inactiveborder` int(24) unsigned DEFAULT NULL,
  `color_inactivecaption` int(24) unsigned DEFAULT NULL,
  `color_inactivecaptiontext` int(24) unsigned DEFAULT NULL,
  `color_infobackground` int(24) unsigned DEFAULT NULL,
  `color_infotext` int(24) unsigned DEFAULT NULL,
  `color_menu` int(24) unsigned DEFAULT NULL,
  `color_menutext` int(24) unsigned DEFAULT NULL,
  `color_scrollbar` int(24) unsigned DEFAULT NULL,
  `color_threeddarkshadow` int(24) unsigned DEFAULT NULL,
  `color_threedface` int(24) unsigned DEFAULT NULL,
  `color_threedhighlight` int(24) unsigned DEFAULT NULL,
  `color_threedlightshadow` int(24) unsigned DEFAULT NULL,
  `color_threedshadow` int(24) unsigned DEFAULT NULL,
  `color_window` int(24) unsigned DEFAULT NULL,
  `color_windowframe` int(24) unsigned DEFAULT NULL,
  `color_windowtext` int(24) unsigned DEFAULT NULL,
  `plugin_flash` varchar(15) DEFAULT NULL,
  `plugin_adobe_acrobat` varchar(15) DEFAULT NULL,
  `plugin_silverlight` varchar(15) DEFAULT NULL,
  `plugins` text,
  `plugins_hash` binary(16) DEFAULT NULL,
  `mimetypes` text,
  `mimetypes_hash` binary(16) DEFAULT NULL,
  `fonts` text,
  `fonts_hash` binary(16) DEFAULT NULL,
  `ignorelevel` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `fonts_hash` (`fonts_hash`),
  KEY `mimetypes_hash` (`mimetypes_hash`),
  KEY `plugins_hash` (`plugins_hash`),
  KEY `port` (`port`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_leadtracker_follow_up_mails`; ##b_dump##
CREATE TABLE `XXX_plugin_leadtracker_follow_up_mails` (
  `leadtracker_fum_id` int(11) NOT NULL AUTO_INCREMENT,
  `leadtracker_betreff_fum` text,
  `leadtracker_mail_inhalt_text` text,
  `leadtracker_mail_inhalt_html` text,
  `leadtracker_id_von_follow_element` varchar(255) NOT NULL,
  `leadtracker_type_von_follow_element` varchar(255) NOT NULL,
  `leadtracker_versand_nach` text,
  `leadtracker_fum_form_id` int(11) NOT NULL,
  `leadtracker_checkreplace` int(11) DEFAULT NULL,
  PRIMARY KEY (`leadtracker_fum_id`),
  KEY `leadtracker_fum_form_id` (`leadtracker_fum_form_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_leadtracker_lookup_form_uid_leadid`; ##b_dump##
CREATE TABLE `XXX_plugin_leadtracker_lookup_form_uid_leadid` (
  `leadtracker_uid_id` int(11) NOT NULL AUTO_INCREMENT,
  `leadtracker_uid` varchar(250) NOT NULL,
  `leadtracker_form_id` int(11) DEFAULT NULL,
  `leadtracker_lead_id` int(11) DEFAULT NULL,
  `leadtracker_time` TIMESTAMP,
  PRIMARY KEY (`leadtracker_uid_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 ; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_leadtracker_tracking`; ##b_dump##
CREATE TABLE `XXX_plugin_leadtracker_tracking` (
  `leadtracker_track_id` int(11) NOT NULL AUTO_INCREMENT,
  `leadtracker_track_url` text NOT NULL,
  `leadtracker_track_direct_referrer` text NOT NULL,
  `leadtracker_track_first_referrer` text NOT NULL,
  `leadtracker_track_timestamp` int(11) NOT NULL,
  `leadtracker_track_cookie` varchar(250) NOT NULL,
  `leadtracker_track_download_id` int(11) NOT NULL,
  `leadtracker_track_form_data` text NOT NULL,
  `leadtracker_track_user_id` int(11) NOT NULL,
  `leadtracker_track_follow_up_mail` text NOT NULL,
  `leadtracker_track_follow_up_mail_click_link` text NOT NULL,
  PRIMARY KEY (`leadtracker_track_id`),
  KEY `leadtracker_track_timestamp` (`leadtracker_track_timestamp`),
  KEY `leadtracker_track_cookie` (`leadtracker_track_cookie`),
  KEY `leadtracker_track_download_id` (`leadtracker_track_download_id`),
  FULLTEXT KEY `leadtracker_track_url` (`leadtracker_track_url`),
  FULLTEXT KEY `leadtracker_track_direct_referrer` (`leadtracker_track_direct_referrer`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=latin1 ; ##b_dump##

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

ALTER TABLE `XXX_plugin_leadtracker_tracking` ADD `leadtracker_track_campaignid` INT( 11 ) NULL DEFAULT '0'; ##b_dump##
ALTER TABLE `XXX_plugin_leadtracker_tracking` ADD `leadtracker_track_adgroupid` INT( 11 ) NULL DEFAULT '0'; ##b_dump##
ALTER TABLE `XXX_plugin_leadtracker_tracking` ADD `leadtracker_track_keyword` VARCHAR( 45 ) NULL DEFAULT NULL; ##b_dump##