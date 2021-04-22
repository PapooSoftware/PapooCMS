DROP TABLE IF EXISTS `XXX_papoo_faq_categories`; ##b_dump##
CREATE TABLE `XXX_papoo_faq_categories` (
  `id` int(11) NOT NULL  auto_increment ,
  `lang_id` int(11) NOT NULL  DEFAULT '1' ,
  `parent_id` int(11) NOT NULL  DEFAULT '0' ,
  `catname` varchar(255) NOT NULL ,
  `catdescript` varchar(255) NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT '10' ,
  PRIMARY KEY (`id`,`lang_id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_faq_content`; ##b_dump##
CREATE TABLE `XXX_papoo_faq_content` (
  `id` int(11) NOT NULL  auto_increment ,
  `version_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL  DEFAULT '1' ,
  `question` text NOT NULL ,
  `answer` longtext ,
  `active` enum('n','j') NOT NULL default 'n',
  `upload_count` smallint(2) NOT NULL DEFAULT '0' ,
  `created` varchar(15) NOT NULL,
  `createdby` varchar(255) NOT NULL,
  `changedd` varchar(15) NOT NULL,
  `changedby` varchar(255) NOT NULL,
  PRIMARY KEY (`id`,`lang_id`,`version_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_faq_versions`; ##b_dump##
CREATE TABLE `XXX_papoo_faq_versions` (
  `id` int(11) NOT NULL  auto_increment ,
  `version_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL  DEFAULT '1' ,
  `question` text NOT NULL ,
  `answer` longtext ,
  `active` enum('n','j') NOT NULL default 'n',
  `upload_count` smallint(2) NOT NULL DEFAULT '0' ,
  `created` varchar(15) NOT NULL,
  `createdby` varchar(255) NOT NULL,
  `changedd` varchar(15) NOT NULL,
  `changedby` varchar(255) NOT NULL,
  PRIMARY KEY (`id`,`lang_id`,`version_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_faq_content_frontend`; ##b_dump##
CREATE TABLE `XXX_papoo_faq_content_frontend` (
  `id` int(11) NOT NULL  auto_increment ,
  `version_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL  DEFAULT '1' ,
  `question` text NOT NULL ,
  `answer` longtext ,
  `active` enum('n','j') NOT NULL default 'n',
  `upload_count` smallint(2) NOT NULL DEFAULT '0' ,
  `created` varchar(15) NOT NULL,
  `createdby` varchar(255) NOT NULL,
  `changedd` varchar(15) NOT NULL,
  `changedby` varchar(255) NOT NULL,
  PRIMARY KEY (`id`,`lang_id`,`version_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_faq_new_question_frontend`; ##b_dump##
CREATE TABLE `XXX_papoo_faq_new_question_frontend` (
  `id` int(11) NOT NULL  auto_increment ,
  `version_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL  DEFAULT '1' ,
  `question` text NOT NULL ,
  `answer` longtext ,
  `active` enum('n','j') NOT NULL default 'n',
  `upload_count` smallint(2) NOT NULL DEFAULT '0' ,
  `created` varchar(15) NOT NULL,
  `createdby` varchar(255) NOT NULL,
  `changedd` varchar(15) NOT NULL,
  `changedby` varchar(255) NOT NULL,
  PRIMARY KEY (`id`,`lang_id`,`version_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_faq_cat_link`; ##b_dump##
CREATE TABLE `XXX_papoo_faq_cat_link` (
  `cat_id` int(11) NOT NULL ,
  `faq_id` int(11) NOT NULL ,
  `version_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT '10' ,
  PRIMARY KEY  (`cat_id`,`faq_id`,`version_id`),
  KEY `idx_records` (`cat_id`,`faq_id`,`version_id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_faq_cat_link_frontend`; ##b_dump##
CREATE TABLE `XXX_papoo_faq_cat_link_frontend` (
  `cat_id` int(11) NOT NULL ,
  `faq_id` int(11) NOT NULL ,
  `version_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT '10' ,
  PRIMARY KEY  (`cat_id`,`faq_id`,`version_id`),
  KEY `idx_records` (`cat_id`,`faq_id`,`version_id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_faq_cat_link_new_question_frontend`; ##b_dump##
CREATE TABLE `XXX_papoo_faq_cat_link_new_question_frontend` (
  `cat_id` int(11) NOT NULL ,
  `faq_id` int(11) NOT NULL ,
  `version_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT '10' ,
  PRIMARY KEY  (`cat_id`,`faq_id`,`version_id`),
  KEY `idx_records` (`cat_id`,`faq_id`,`version_id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_faq_attachments`; ##b_dump##
CREATE TABLE `XXX_papoo_faq_attachments` (
  `id` INT( 11 ) NOT NULL auto_increment ,
  `faq_id` INT( 11 ) NOT NULL ,
  `version_id` int(11) NOT NULL,
  `name` TEXT NOT NULL ,
  `name_stored` TEXT NOT NULL ,
  `size` INT( 11 ) NOT NULL ,
  PRIMARY KEY ( `id`,`faq_id`,`version_id` )
) ENGINE = MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_faq_config`; ##b_dump##
CREATE TABLE `XXX_papoo_faq_config` (
  `id` int(4) NOT NULL ,
  `lang_id` int(11) NOT NULL  DEFAULT '1' ,
  `title` text NOT NULL ,
  `descript` text NOT NULL ,
  `keywords` text NOT NULL ,
  `layout` enum('Kompakt','Linkliste','Linkliste 2','Linkliste 3','Linkliste 4','Linkliste 5','Extrapage') NOT NULL  DEFAULT 'Linkliste 3' ,
  `faq_order` enum('created','question','createdby','order_id') NOT NULL  DEFAULT 'created' ,
  `renum_step` smallint(2) NOT NULL  DEFAULT '10' ,
  `cats_per_page` smallint(2) NOT NULL  DEFAULT '20' ,
  `faq_header` text NOT NULL ,
  `faq_head_text` text NOT NULL ,
  `faq_footer` text NOT NULL ,
  `faqs_per_page` smallint(2) NOT NULL  DEFAULT '20' ,
  `attachshow` enum('j','n') NOT NULL  DEFAULT 'j' ,
  `attachsize` int(4) NOT NULL  DEFAULT '102400' ,
  `uploads_per_faq` smallint(2) NOT NULL  DEFAULT '5' ,
  `shownewfaq` enum('j','n') NOT NULL  DEFAULT 'n' ,
  `shownewf` enum('j','n') NOT NULL  DEFAULT 'n' ,
  `autodetect_lang` enum('j','n') NOT NULL  DEFAULT 'j' ,
  `sendMail` enum('j','n') NOT NULL  DEFAULT 'n' ,
  `adminmail` varchar(100) NOT NULL ,
  PRIMARY KEY (`lang_id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci; ##b_dump##
INSERT INTO `XXX_papoo_faq_config` SET id='0', lang_id='0', autodetect_lang='j' ; ##b_dump##
INSERT INTO `XXX_papoo_faq_config` SET id='1', lang_id='1', title='Papoo FAQ Plugin Deutsch', descript='Papoo FAQ Plugin', keywords='papoo, faq, plugin', layout='Extrapage', faq_order='order_id', renum_step='10', cats_per_page='20', faq_header='Papoo FAQ Plugin Deutsch', faqs_per_page='20', attachshow='j', attachsize='102400', shownewfaq='n'  ; ##b_dump##
INSERT INTO `XXX_papoo_faq_config` SET id='1', lang_id='2', title='Papoo FAQ English', descript='Papoo FAQ Plugin', keywords='papoo, faq, plugin', layout='Kompakt', faq_order='created', renum_step='10', cats_per_page='20', faq_header='Papoo FAQ Plugin English', faqs_per_page='20', attachshow='j', attachsize='102400', shownewfaq='n'  ; ##b_dump##
INSERT INTO `XXX_papoo_faq_config` SET id='1', lang_id='3', title='Papoo FAQ Italiano', descript='Papoo FAQ Plugin', keywords='papoo, faq, plugin', layout='Kompakt', faq_order='created', renum_step='10', cats_per_page='20', faq_header='Papoo FAQ Plugin Italiano', faqs_per_page='20', attachshow='j', attachsize='102400', shownewfaq='n'  ; ##b_dump##
INSERT INTO `XXX_papoo_faq_config` SET id='1', lang_id='4', title='Papoo FAQ Español', descript='Papoo FAQ Plugin', keywords='papoo, faq, plugin', layout='Kompakt', faq_order='created', renum_step='10', cats_per_page='20', faq_header='Papoo FAQ Plugin Español', faqs_per_page='20', attachshow='j', attachsize='102400', shownewfaq='n'  ; ##b_dump##
INSERT INTO `XXX_papoo_faq_config` SET id='1', lang_id='5', title='Papoo FAQ Français', descript='Papoo FAQ Plugin', keywords='papoo, faq, plugin', layout='Kompakt', faq_order='created', renum_step='10', cats_per_page='20', faq_header='Papoo FAQ Plugin Français', faqs_per_page='20', attachshow='j', attachsize='102400', shownewfaq='n'  ; ##b_dump##
INSERT INTO `XXX_papoo_faq_config` SET id='1', lang_id='6', title='Papoo FAQ Português', descript='Papoo FAQ Plugin', keywords='papoo, faq, plugin', layout='Kompakt', faq_order='created', renum_step='10', cats_per_page='20', faq_header='Papoo FAQ Plugin Português', faqs_per_page='20', attachshow='j', attachsize='102400', shownewfaq='n'  ; ##b_dump##
INSERT INTO `XXX_papoo_faq_config` SET id='1', lang_id='7', title='Papoo FAQ Nederlands', descript='Papoo FAQ Plugin', keywords='papoo, faq, plugin', layout='Kompakt', faq_order='created', renum_step='10', cats_per_page='20', faq_header='Papoo FAQ Plugin Nederlands', faqs_per_page='20', attachshow='j', attachsize='102400', shownewfaq='n'  ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_faq_read_privileges`; ##b_dump##
CREATE TABLE `XXX_papoo_faq_read_privileges` (
  `id` int(4) NOT NULL,
  `gruppeid` int(11) NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci; ##b_dump##
INSERT INTO `XXX_papoo_faq_read_privileges` (`id`, `gruppeid`) VALUES (1, 10); ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_faq_write_privileges`; ##b_dump##
CREATE TABLE `XXX_papoo_faq_write_privileges` (
  `id` int(4) NOT NULL,
  `gruppeid` int(11) NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci; ##b_dump##
INSERT INTO `XXX_papoo_faq_write_privileges` (`id`, `gruppeid`) VALUES (1, 10); ##b_dump##