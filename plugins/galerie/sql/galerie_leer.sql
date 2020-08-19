DROP TABLE IF EXISTS `XXX_galerie_bilder`; ##b_dump##
CREATE TABLE `XXX_galerie_bilder` (
  `bild_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bild_datei` varchar(255) NOT NULL,
  `bild_gal_id` bigint(20) NOT NULL,
  `bild_nummer` int(11) NOT NULL,
  `bild_format` varchar(50) NOT NULL,
  `bild_breite` int(11) NOT NULL,
  `bild_breite_thumb` int(11) NOT NULL,
  `bild_hoehe` int(11) NOT NULL,
  `bild_hoehe_thumb` int(11) NOT NULL,
  `bild_diashow_timeout` int(11) NOT NULL DEFAULT '5',
  PRIMARY KEY (`bild_id`),
  KEY `bild_gal_id` (`bild_gal_id`),
  KEY `bild_nummer` (`bild_nummer`)
) ENGINE=MyISAM AUTO_INCREMENT=416 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='1', bild_datei='../../../bilder/kein_bild.gif', bild_gal_id='0', bild_nummer='0', bild_format='GIF', bild_breite='100', bild_breite_thumb='100', bild_hoehe='30', bild_hoehe_thumb='30', bild_diashow_timeout='5'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_galerie_bilder_language`; ##b_dump##
CREATE TABLE `XXX_galerie_bilder_language` (
  `bildlang_bild_id` bigint(20) NOT NULL,
  `bildlang_lang_id` int(11) NOT NULL,
  `bildlang_name` varchar(255) NOT NULL,
  `bildlang_beschreibung` longtext NOT NULL,
  KEY `bildlang_bild_id` (`bildlang_bild_id`),
  KEY `bildlang_lang_id` (`bildlang_lang_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='1', bildlang_lang_id='1', bildlang_name='kein Bild', bildlang_beschreibung='Kein Bild'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='1', bildlang_lang_id='2', bildlang_name='no picture', bildlang_beschreibung='no picture'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_galerie_einstellungen`; ##b_dump##
CREATE TABLE `XXX_galerie_einstellungen` (
  `galset_thumb_breite` int(11) NOT NULL DEFAULT '150',
  `galset_thumb_hoehe` int(11) NOT NULL DEFAULT '150',
  `galset_diashow_id` int(11) NOT NULL DEFAULT '1',
  `galset_diashow_timeout` int(11) NOT NULL DEFAULT '5',
  `galset_lightbox` int(11) NOT NULL DEFAULT '1',
  `galset_diashow` int(11) NOT NULL DEFAULT '1',
  `galset_diashow_window` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_galerie_einstellungen` SET galset_thumb_breite='120', galset_thumb_hoehe='120', galset_diashow_id='1', galset_diashow_timeout='5', galset_lightbox='1', galset_diashow='1', galset_diashow_window='1'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_galerie_galerien`; ##b_dump##
CREATE TABLE `XXX_galerie_galerien` (
  `gal_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '999999999',
  `gal_bilderanzahl` int(11) NOT NULL DEFAULT '0',
  `gal_verzeichnis` varchar(255) NOT NULL,
  `gal_aktiv_janein` int(1) NOT NULL DEFAULT '0',
  `gal_bild_id` bigint(20) NOT NULL DEFAULT '1',
  `gal_order_id` int(11) NOT NULL,
  PRIMARY KEY (`gal_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_galerie_galerien` SET gal_id='13', parent_id='0', gal_bilderanzahl='0', gal_verzeichnis='', gal_aktiv_janein='0', gal_bild_id='1', gal_order_id='10'  ; ##b_dump##
INSERT INTO `XXX_galerie_galerien` SET gal_id='14', parent_id='0', gal_bilderanzahl='0', gal_verzeichnis='', gal_aktiv_janein='0', gal_bild_id='1', gal_order_id='20'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_galerie_galerien_language`; ##b_dump##
CREATE TABLE `XXX_galerie_galerien_language` (
  `gallang_gal_id` bigint(20) NOT NULL,
  `gallang_lang_id` bigint(20) NOT NULL,
  `gallang_name` varchar(255) NOT NULL,
  `gallang_beschreibung` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_galerie_galerien_language` SET gallang_gal_id='13', gallang_lang_id='1', gallang_name='Urlaub', gallang_beschreibung=''  ; ##b_dump##
INSERT INTO `XXX_galerie_galerien_language` SET gallang_gal_id='14', gallang_lang_id='1', gallang_name='Freizeit', gallang_beschreibung=''  ; ##b_dump##
