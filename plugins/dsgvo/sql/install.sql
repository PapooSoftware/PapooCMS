DROP TABLE IF EXISTS `XXX_plugin_dsgvo_settings`; ##b_dump##
CREATE TABLE `XXX_plugin_dsgvo_settings` (
	id TINYINT(1) NOT NULL DEFAULT 1,
	logfile_autoremove TINYINT(1) NOT NULL DEFAULT 0,
	logfile_autoremove_interval INT UNSIGNED NOT NULL DEFAULT 30,
	youtube_nocookie TINYINT(1) NOT NULL DEFAULT 1,
	cookie_lifetime INT UNSIGNED NOT NULL DEFAULT 7,
	formdata_autoremove TINYINT(1) NOT NULL DEFAULT 0,
	formdata_autoremove_interval INT UNSIGNED NOT NULL DEFAULT 365,
	PRIMARY KEY (id)
) ENGINE=MyISAM CHARSET 'utf8' COLLATE 'utf8_unicode_ci'; ##b_dump##
INSERT INTO `XXX_plugin_dsgvo_settings` (id) VALUES (1); ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_dsgvo_external_resources`; ##b_dump##
CREATE TABLE `XXX_plugin_dsgvo_external_resources` (
	id INT AUTO_INCREMENT NOT NULL,
	url VARCHAR(1024) NOT NULL,
	url_hash VARCHAR(32) NOT NULL COLLATE 'ascii_general_ci',
	last_encounter DATETIME NOT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY (url_hash)
) ENGINE=MyISAM CHARSET 'utf8' COLLATE 'utf8_unicode_ci'; ##b_dump##
