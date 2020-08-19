DROP TABLE IF EXISTS `XXX_plugin_blogger_shortcut_config`; ##b_dump##
CREATE TABLE `XXX_plugin_blogger_shortcut_config` (
  `blogger_shortcut_config_id` int(11) NOT NULL AUTO_INCREMENT,
  `blogger_shortcut_config_name` varchar(255) NOT NULL,
  `blogger_shortcut_config_value` varchar(255) NOT NULL,
  `blogger__feldtypen_fuer_schlagworte` varchar(255) NOT NULL,
  PRIMARY KEY (`blogger_shortcut_config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_blogger_shortcut_config` SET blogger_shortcut_config_id='1', blogger_shortcut_config_name='wordcloud_count', blogger_shortcut_config_value='10'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_blogger_shortcuts`; ##b_dump##
CREATE TABLE `XXX_plugin_blogger_shortcuts` (
  `blogger_shortcuts_id` int(11) NOT NULL AUTO_INCREMENT,
  `blogger_shortcuts_menuid` int(11) NOT NULL,
  `blogger_shortcuts_calendar` int(1) NOT NULL,
  `blogger_shortcuts_wordcloud` int(1) NOT NULL,
  `blogger_shortcuts_month` int(11) NOT NULL,
  PRIMARY KEY (`blogger_shortcuts_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_blogger_trackback_config`; ##b_dump##
CREATE TABLE `XXX_plugin_blogger_trackback_config` (
  `blogger_plugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `blogger_plugin_ping_service_aktivieren` varchar(255) NOT NULL,
  `blogger_plugin_trackback_aktivieren` varchar(255) NOT NULL,
  `blogger_plugin_mail_an_admin_wenn_neuer_tb_erstellt_wurde` varchar(255) NOT NULL,
  PRIMARY KEY (`blogger_plugin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_blogger_trackback_config` SET blogger_plugin_id='1', blogger_plugin_ping_service_aktivieren='0', blogger_plugin_trackback_aktivieren='0', blogger_plugin_mail_an_admin_wenn_neuer_tb_erstellt_wurde='0'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_blogger_trackbacks`; ##b_dump##
CREATE TABLE `XXX_plugin_blogger_trackbacks` (
  `blogger_plugin_tb_id` int(11) NOT NULL AUTO_INCREMENT,
  `blogger_plugin_tb_my_url` text NOT NULL,
  `blogger_plugin_tb_remote_url` text NOT NULL,
  `blogger_plugin_tb_count` int(11) NOT NULL,
  PRIMARY KEY (`blogger_plugin_tb_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_blogger_word_article_lookup`; ##b_dump##
CREATE TABLE `XXX_plugin_blogger_word_article_lookup` (
  `blogger_word_article_lookup_id` int(11) NOT NULL AUTO_INCREMENT,
  `blogger_word_article_lookup_word_id` int(11) NOT NULL,
  `blogger_word_article_lookup_lan_repore_id` int(11) NOT NULL,
  PRIMARY KEY (`blogger_word_article_lookup_id`),
  KEY `blogger_word_article_lookup_word_id` (`blogger_word_article_lookup_word_id`),
  KEY `blogger_word_article_lookup_lan_repore_id` (`blogger_word_article_lookup_lan_repore_id`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=latin1 ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_blogger_wordlist`; ##b_dump##
CREATE TABLE `XXX_plugin_blogger_wordlist` (
  `blogger_wordlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `blogger_wordlist_word` varchar(255) NOT NULL,
  `blogger_wordlist_count` int(11) NOT NULL,
  PRIMARY KEY (`blogger_wordlist_id`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=latin1 ; ##b_dump##