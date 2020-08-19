DROP TABLE IF EXISTS `XXX_plugin_social_media_buttons`; ##b_dump##

CREATE TABLE `XXX_plugin_social_media_buttons` (
  `id` tinyint(2) NOT NULL auto_increment ,
  `display` varchar(255) NULL ,
  `name` varchar(255) NULL ,
  `aktiv` tinyint(1) NULL ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons` (display, name, aktiv)
VALUES ('Facebook', 'facebook', '1') ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons` (display, name, aktiv)  
VALUES ('Twitter', 'twitter', '1') ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons` (display, name, aktiv)
VALUES ('LinkedIn', 'linkedin', '0') ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons` (display, name, aktiv)
VALUES ('Xing', 'xing', '0') ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons` (display, name, aktiv)
VALUES ('Whats App', 'whatsapp', '0') ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons` (display, name, aktiv)
VALUES ('Mail', 'mail', '1') ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons` (display, name, aktiv)
VALUES ('Info', 'info', '0') ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons` (display, name, aktiv)
VALUES ('AddThis', 'addthis', '0') ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons` (display, name, aktiv)
VALUES ('Tumblr', 'tumblr', '0') ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons` (display, name, aktiv)
VALUES ('Diaspora', 'diaspora', '0') ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons` (display, name, aktiv)
VALUES ('Reddit', 'reddit', '0') ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons` (display, name, aktiv)
VALUES ('StumbleUpon', 'stumbleupon', '0') ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons` (display, name, aktiv)
VALUES ('Threema', 'threema', '0') ; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_social_media_buttons_config`; ##b_dump##

CREATE TABLE `XXX_plugin_social_media_buttons_config` (
  `fontawesome` tinyint(1) NULL ,
  `theme` varchar(255) NULL ,
  `vertical` tinyint(1) NULL
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_plugin_social_media_buttons_config` (fontawesome, theme, vertical)
VALUES ('0', 'standard', '0') ; ##b_dump##
