DROP TABLE IF EXISTS `XXX_userlogin_page`; ##b_dump##
CREATE TABLE `XXX_userlogin_page` (
	`id` INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM; ##b_dump##
INSERT INTO `XXX_userlogin_page` SET id=1; ##b_dump##

DROP TABLE IF EXISTS `XXX_userlogin_page_group`; ##b_dump##
CREATE TABLE `XXX_userlogin_page_group` (
	`page_id` INT NOT NULL,
	`group_id` INT NOT NULL,
	PRIMARY KEY (`page_id`, `group_id`),
	FOREIGN KEY (`page_id`) REFERENCES `XXX_userlogin_page`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`group_id`) REFERENCES `XXX_papoo_gruppe`(`gruppeid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MyISAM; ##b_dump##
INSERT INTO `XXX_userlogin_page_group` SET page_id=1, group_id=10; ##b_dump##

DROP TABLE IF EXISTS `XXX_userlogin_page_language`; ##b_dump##
CREATE TABLE `XXX_userlogin_page_language` (
	`page_id` INT NOT NULL,
	`language_id` INT NOT NULL,
	`text_angemeldet` MEDIUMTEXT,
	PRIMARY KEY (`page_id`, `language_id`),
	FOREIGN KEY (`page_id`) REFERENCES `XXX_userlogin_page`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci; ##b_dump##
INSERT INTO `XXX_userlogin_page_language` SET page_id=1, language_id=1, text_angemeldet='<h1>Angemeldet</h1><h2>Unter-Überschrift zu erfolgreicher Anmeldung</h2><p></p><p><strong><img src=\"../images/10_login_of.jpg\" width=\"79\" height=\"79\" alt=\"Angemeldet im Login-Bereich\" title=\"Angemeldet im Login-Bereich\" class=\"bild_rechts\" />Beispielhafter \"Angemeldet-Text\" zur Ansicht im Frontend.</strong></p><p></p><p>Dieser Text kann geändert werden unter \"Plugins -&gt; User-Login\"</p>'  ; ##b_dump##
INSERT INTO `XXX_userlogin_page_language` SET page_id=1, language_id=2, text_angemeldet='<h1>You are logged in</h1><p>Sample text shown when user is logged in.</p>'  ; ##b_dump##
