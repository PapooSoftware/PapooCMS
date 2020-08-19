DROP TABLE IF EXISTS `XXX_papoo_google_maps_tabelle`; ##b_dump##
CREATE TABLE `XXX_papoo_google_maps_tabelle` (
  `gmap_id` int(11) NOT NULL AUTO_INCREMENT,
  `gmap_apikey` text,
  `gmap_apikey_2` text,
  `gmap_typ` varchar(255) DEFAULT NULL,
  `gmap_breite` varchar(255) DEFAULT NULL,
  `gmap_hoehe` varchar(255) DEFAULT NULL,
  `gmap_zoom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`gmap_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_papoo_google_maps_tabelle` SET gmap_id='1', gmap_apikey='', gmap_apikey_2='', gmap_typ='MAP', gmap_breite='500', gmap_hoehe='200', gmap_zoom='17'  ; ##b_dump##
