DROP TABLE IF EXISTS `XXX_plugin_2_klick_video`; ##b_dump##
CREATE TABLE `XXX_plugin_2_klick_video` (
	id TINYINT(1) NOT NULL DEFAULT 1,
	installation_key BINARY(16) DEFAULT NULL,
	active BOOLEAN NOT NULL DEFAULT TRUE,
	two_click BOOLEAN NOT NULL DEFAULT FALSE,
	use_thumbnails BOOLEAN NOT NULL DEFAULT TRUE,
	use_flex_video BOOLEAN NOT NULL DEFAULT TRUE,
	thumbnail_sizes VARCHAR(255) NOT NULL DEFAULT '(max-width: 40.063em) 99vw, 70vw',
	cache_lifetime TIME(0) NOT NULL DEFAULT '24:00:00',
	PRIMARY KEY (id)
) CHARSET 'utf8' COLLATE 'utf8_unicode_ci'; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_2_klick_video_lang`; ##b_dump##
CREATE TABLE `XXX_plugin_2_klick_video_lang` (
	lang_id INT(11) NOT NULL,
	title_text VARCHAR(128) NOT NULL,
	info_text VARCHAR(1024) NOT NULL,
	link_text VARCHAR(64) NOT NULL,
	dismiss_text VARCHAR(64) NOT NULL,
	text_color VARCHAR(9) COLLATE 'ascii_general_ci' NOT NULL DEFAULT '#555555',
	background_color VARCHAR(9) COLLATE 'ascii_general_ci' NOT NULL DEFAULT '#FFFFFF',
	confirm_button_color VARCHAR(9) COLLATE 'ascii_general_ci' NOT NULL DEFAULT '#A44F21',
	dismiss_button_color VARCHAR(9) COLLATE 'ascii_general_ci' NOT NULL DEFAULT '#FFFFFF',
	confirm_text_color VARCHAR(9) COLLATE 'ascii_general_ci' NOT NULL DEFAULT '#FFFFFF',
	dismiss_text_color VARCHAR(9) COLLATE 'ascii_general_ci' NOT NULL DEFAULT '#1B93B9',
	PRIMARY KEY (lang_id)
) CHARSET 'utf8' COLLATE 'utf8_unicode_ci'; ##b_dump##

ALTER TABLE `XXX_plugin_2_klick_video_lang`
	ADD FOREIGN KEY `lang_id` REFERENCES `XXX_papoo_name_language`(`lang_id`) ON UPDATE CASCADE ON DELETE CASCADE
; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_2_klick_video_cache`; ##b_dump##
CREATE TABLE `XXX_plugin_2_klick_video_cache` (
	cache_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	video_provider VARCHAR(8) COLLATE 'ascii_general_ci' NOT NULL,
	video_id VARCHAR(16) COLLATE 'ascii_bin' NOT NULL,
	video_title VARCHAR(128) NOT NULL DEFAULT '',
	cache_date DATETIME NOT NULL,
	PRIMARY KEY (cache_id),
	UNIQUE KEY (video_provider, video_id)
) CHARSET 'utf8' COLLATE 'utf8_bin'; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_2_klick_video_cache_images`; ##b_dump##
CREATE TABLE `XXX_plugin_2_klick_video_cache_images` (
	cache_id INT(11) UNSIGNED NOT NULL,
	image_width SMALLINT UNSIGNED NOT NULL,
	image_height SMALLINT UNSIGNED NOT NULL,
	file_name VARCHAR(64) NOT NULL,
	etag VARCHAR(16) NULL,
	PRIMARY KEY (cache_id, image_width)
) CHARSET 'ascii' COLLATE 'ascii_bin'; ##b_dump##

ALTER TABLE `XXX_plugin_2_klick_video_cache_images`
	ADD FOREIGN KEY `cache_id` REFERENCES `XXX_plugin_2_klick_video_cache`(`cache_id`) ON UPDATE CASCADE ON DELETE CASCADE
; ##b_dump##

INSERT INTO `XXX_plugin_2_klick_video` (id) VALUES (1); ##b_dump##

INSERT INTO `XXX_plugin_2_klick_video_lang` (lang_id, title_text, info_text, link_text, dismiss_text) VALUES
(1, 'Video aktivieren', 'Zum Aktivieren des Videos klicke bitte unten auf Bestätigen. Wir möchten dich darauf hinweisen, dass nach der Aktivierung Daten an den jeweiligen Anbieter übermittelt werden.', 'Bestätigen', 'Abbrechen'),
(2, 'Activate video', 'To activate the video, click on Confirm below. We want to inform you that data will be transmitted to the video provider after activation.', 'Confirm', 'Dismiss')
; ##b_dump##

UPDATE `XXX_plugin_2_klick_video` SET installation_key=RANDOM_BYTES(16) WHERE id=1; ##b_dump##