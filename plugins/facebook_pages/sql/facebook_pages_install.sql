DROP TABLE IF EXISTS `XXX_plugin_facebook_page`; ##b_dump##
CREATE TABLE `XXX_plugin_facebook_page` (
  `facebook_page_id` int(11) NOT NULL AUTO_INCREMENT,
  `facebook_page_der_name_der_kategorie_` text,
  PRIMARY KEY (`facebook_page_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_facebook_page` SET facebook_page_id='1', facebook_page_der_name_der_kategorie_='Kategorie 1'  ; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_facebook_page_sites`; ##b_dump##
CREATE TABLE `XXX_plugin_facebook_page_sites` (
  `kalender_id` int(11) NOT NULL AUTO_INCREMENT,
  `kalender_interne_bezeichnung` text,
  `kalender_domain` text,
  `kalender_titel_der_seite` text,
  `kalender_meta_description` text,
  `kalender_meta_keywords` text,
  `kalender_design` varchar(255) NOT NULL,
  `kalender_content` text,
  `kalender_kategori_der_sseite` varchar(255) NOT NULL,
  `kalender_gacode` text,
  PRIMARY KEY (`kalender_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 ; ##b_dump##
