DROP TABLE IF EXISTS `XXX_plugin_ext_search_config`; ##b_dump##
CREATE TABLE `XXX_plugin_ext_search_config` (
  `ext_search_id` int(11) NOT NULL AUTO_INCREMENT,
  `ext_search_lemente_mit_ewichtung` text COLLATE utf8_bin,
  `ext_search_orschkge_in_der_uchemaske_aktivieren` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ext_search_nzahl_der_uchergebnisse` text COLLATE utf8_bin,
  `ext_search_teaser_max_length` smallint UNSIGNED NOT NULL DEFAULT 400,
  `ext_search_blaufzeit` text COLLATE utf8_bin,
  `ext_search_as_soll_als_berschriftink_verwendet_werden` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ext_search_top_rter` text COLLATE utf8_bin,
  `ext_search_ur_rtikel` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ext_search_ur_rodukte` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ext_search_ur_orum` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ext_search_ur_lex` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ext_search_ur_doc` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ext_search__fr_igh` text COLLATE utf8_bin,
  `ext_search_ighlighting_auch_bei_oogle_reffern_aktivieren` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ext_search_ighlighting_rt` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ext_search_inschrnkung_auf_nur_` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ext_search_match_oder_listen_suche` varchar(255) COLLATE utf8_bin NOT NULL,
  `ext_search_highlighting_aktivieren` varchar(255) COLLATE utf8_bin NOT NULL,
  `ext_search_vorschlge_im_modul_suchmaske` varchar(255) COLLATE utf8_bin NOT NULL,
  `ext_search_schwelle` text COLLATE utf8_bin,
  `ext_search_zuweisung` text COLLATE utf8_bin,
  `ext_search_menus` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ext_search_id`)
) ENGINE=MyISAM AUTO_INCREMENT=81 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ; ##b_dump##
INSERT INTO `XXX_plugin_ext_search_config` SET ext_search_id='80', ext_search_lemente_mit_ewichtung='h1|6\r\nh2|5\r\nh3|4\r\nh4|3\r\na|4\r\nstrong|2', ext_search_orschkge_in_der_uchemaske_aktivieren='1', ext_search_nzahl_der_uchergebnisse='20', ext_search_blaufzeit='691200', ext_search_as_soll_als_berschriftink_verwendet_werden='', ext_search_top_rter='und\r\noder\r\nder\r\ndie\r\ndas\r\nden\r\ndem\r\nwieso\r\nweshalb\r\nwarum\r\nnicht\r\ndort\r\neiner\r\neins\r\nzwei\r\ndrei\r\nvier\r\nnbsp\r\nist\r\ndies\r\nauf\r\nein\r\nsie\r\nhier\r\nvon\r\nwww\r\nkann', ext_search_ur_rtikel='1', ext_search_ur_rodukte='1', ext_search_ur_orum='1', ext_search_ur_lex='', ext_search_ur_doc='', ext_search__fr_igh='background:#F5F694;\r\ncolor:#000;', ext_search_ighlighting_auch_bei_oogle_reffern_aktivieren='1', ext_search_ighlighting_rt='3', ext_search_inschrnkung_auf_nur_='1', ext_search_match_oder_listen_suche='', ext_search_highlighting_aktivieren='', ext_search_vorschlge_im_modul_suchmaske='', ext_search_schwelle='', ext_search_zuweisung='', ext_search_menus=''  ; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_ext_search_gruppen`; ##b_dump##
CREATE TABLE `XXX_plugin_ext_search_gruppen` (
  `ext_search_id_rid` int(111) NOT NULL DEFAULT '0',
  `ext_search_gruppen_id` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`ext_search_id_rid`,`ext_search_gruppen_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_ext_search_page`; ##b_dump##
CREATE TABLE `XXX_plugin_ext_search_page` (
  `ext_search_id` int(11) NOT NULL AUTO_INCREMENT,
  `ext_search_lang_id` int(11) NOT NULL DEFAULT '0',
  `ext_search_url` text,
  `ext_search_title` varchar(255) NOT NULL DEFAULT '',
  `ext_search_description` text,
  `ext_search_header` varchar(255) NOT NULL DEFAULT '',
  `ext_search_teaser` text,
  `ext_search_teaser_img` text,
  `ext_search_completet_text` text NOT NULL,
  `ext_search_seite_menu_id` int(11) NOT NULL DEFAULT '0',
  `ext_search_seite_reporeid` int(11) NOT NULL DEFAULT '0',
  `ext_search_seite_produkt_id` int(11) NOT NULL DEFAULT '0',
  `ext_search_seite_forum_id` int(11) NOT NULL DEFAULT '0',
  `ext_search_seite_message_id` int(11) NOT NULL DEFAULT '0',
  `ext_search_seite_faq_id` int(11) NOT NULL DEFAULT '0',
  `ext_search_seite_mv_id` int(11) NOT NULL DEFAULT '0',
  `ext_search_seite_mv_content_id` int(11) NOT NULL DEFAULT '0',
  `ext_search_document_path` varchar(255) DEFAULT NULL,
  `ext_search_url_time` int(11) NOT NULL DEFAULT '0',
  `ext_search_metaphone` text NOT NULL,
  PRIMARY KEY (`ext_search_id`),
  FULLTEXT (
    `ext_search_title`,
    `ext_search_description`,
    `ext_search_header`,
    `ext_search_teaser`,
    `ext_search_completet_text`
  )
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
CREATE TABLE `XXX_plugin_ext_search_statistik` (
  `ext_search_stat_id` int(11) NOT NULL AUTO_INCREMENT,
  `ext_search_stat_search` varchar(255) NOT NULL DEFAULT '',
  `ext_search_stat_time` int(11) NOT NULL DEFAULT '0',
  `ext_search_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ext_search_stat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=153 DEFAULT CHARSET=utf8 ; ##b_dump##
CREATE TABLE `XXX_plugin_ext_search_thesaurus` (
  `ext_search_thesaurus_id` int(11) NOT NULL AUTO_INCREMENT,
  `ext_search_thesaurus_lang_id` int(11) NOT NULL DEFAULT '0',
  `ext_search_thesaurus_einten_ie_vielleicht_egriff` text,
  `ext_search_thesaurus_uchbegriffe` text NOT NULL,
  PRIMARY KEY (`ext_search_thesaurus_id`),
  FULLTEXT (`ext_search_thesaurus_uchbegriffe`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_ext_search_thesaurus` SET ext_search_thesaurus_id='1', ext_search_thesaurus_lang_id='1', ext_search_thesaurus_einten_ie_vielleicht_egriff='Papoo', ext_search_thesaurus_uchbegriffe='ppoo\r\npappo\r\npap\r\nppoo'  ; ##b_dump##
INSERT INTO `XXX_plugin_ext_search_thesaurus` SET ext_search_thesaurus_id='3', ext_search_thesaurus_lang_id='1', ext_search_thesaurus_einten_ie_vielleicht_egriff='asfd', ext_search_thesaurus_uchbegriffe='safd'  ; ##b_dump##
CREATE TABLE `XXX_plugin_ext_search_thesaurus_search` (
  `ext_search_thesaurus_id` int(11) NOT NULL AUTO_INCREMENT,
  `ext_search_thesaurus_lang_id` int(11) NOT NULL DEFAULT '0',
  `ext_search_thesaurus_org_id` int(11) NOT NULL DEFAULT '0',
  `ext_search_thesaurus_einten_ie_vielleicht_egriff` text,
  `ext_search_thesaurus_uchbegriffe` text NOT NULL,
  PRIMARY KEY (`ext_search_thesaurus_id`),
  FULLTEXT (`ext_search_thesaurus_uchbegriffe`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_ext_search_thesaurus_search` SET ext_search_thesaurus_id='5', ext_search_thesaurus_lang_id='1', ext_search_thesaurus_org_id='1', ext_search_thesaurus_einten_ie_vielleicht_egriff='Papoo', ext_search_thesaurus_uchbegriffe='pappo'  ; ##b_dump##
INSERT INTO `XXX_plugin_ext_search_thesaurus_search` SET ext_search_thesaurus_id='4', ext_search_thesaurus_lang_id='1', ext_search_thesaurus_org_id='1', ext_search_thesaurus_einten_ie_vielleicht_egriff='Papoo', ext_search_thesaurus_uchbegriffe='ppoo'  ; ##b_dump##
INSERT INTO `XXX_plugin_ext_search_thesaurus_search` SET ext_search_thesaurus_id='6', ext_search_thesaurus_lang_id='1', ext_search_thesaurus_org_id='1', ext_search_thesaurus_einten_ie_vielleicht_egriff='Papoo', ext_search_thesaurus_uchbegriffe='pap'  ; ##b_dump##
INSERT INTO `XXX_plugin_ext_search_thesaurus_search` SET ext_search_thesaurus_id='7', ext_search_thesaurus_lang_id='1', ext_search_thesaurus_org_id='1', ext_search_thesaurus_einten_ie_vielleicht_egriff='Papoo', ext_search_thesaurus_uchbegriffe='ppoo'  ; ##b_dump##
INSERT INTO `XXX_plugin_ext_search_thesaurus_search` SET ext_search_thesaurus_id='12', ext_search_thesaurus_lang_id='1', ext_search_thesaurus_org_id='3', ext_search_thesaurus_einten_ie_vielleicht_egriff='asfd', ext_search_thesaurus_uchbegriffe='safd'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_ext_search_vorkommen`; ##b_dump##
CREATE TABLE `XXX_plugin_ext_search_vorkommen` (
  `ext_search_seite_id_id` int(22) NOT NULL AUTO_INCREMENT,
  `ext_search_seite_id` int(11) NOT NULL DEFAULT '0',
  `ext_search_wort_id` varchar(255) NOT NULL DEFAULT '',
  `ext_search_score_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ext_search_seite_id_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ; ##b_dump##
ALTER TABLE  `XXX_plugin_ext_search_vorkommen` ADD INDEX (  `ext_search_seite_id` ) ; ##b_dump##
ALTER TABLE  `XXX_plugin_ext_search_vorkommen` ADD INDEX (  `ext_search_wort_id` ) ; ##b_dump##