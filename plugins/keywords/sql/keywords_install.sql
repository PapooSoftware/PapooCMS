DROP TABLE IF EXISTS `XXX_papoo_keywords_liste`; ##b_dump##
CREATE TABLE `XXX_papoo_keywords_liste` (
  `keywords_liste_id` int(11) NOT NULL AUTO_INCREMENT,
  `keywords_liste_wieoft` int(11) DEFAULT NULL,
  `keywords_liste_lang_id` int(11) DEFAULT NULL,
  `keywords_liste_keyword` text,
  PRIMARY KEY (`keywords_liste_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_keywords_tabelle`; ##b_dump##
CREATE TABLE `XXX_papoo_keywords_tabelle` (
  `keywords_id` int(11) NOT NULL AUTO_INCREMENT,
  `keywords_wieoft` int(11) DEFAULT NULL,
  `keywords_wieviel` int(11) DEFAULT NULL,
  `keywords_text` text,
  `keywords_stop_wrter` varchar(255) NOT NULL,
  `keywords_basis_der_tag_cloud` varchar(255) NOT NULL,
  PRIMARY KEY (`keywords_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_papoo_keywords_tabelle` SET keywords_id='1', keywords_wieoft='864000', keywords_wieviel='10', keywords_text='1272630033', keywords_stop_wrter='', keywords_basis_der_tag_cloud=''  ; ##b_dump##
