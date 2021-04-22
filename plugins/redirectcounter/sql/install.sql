DROP TABLE IF EXISTS `XXX_redirectcounter_daten`; ##b_dump##
CREATE TABLE `XXX_redirectcounter_daten` (
  `redirectco_id` int(11) NOT NULL,
  `redirectco_gesetzte_redirects_dat` int(99) NOT NULL,
  `redirectco_getestete_unterseiten_dat` int(99) NOT NULL,
  `redirectco_html_seiten_dat` int(99) NOT NULL,
  `redirectco_html_seiten_count_dat` int(99) NOT NULL,
  `redirectco_dokumente_dat` int(99) NOT NULL,
  `redirectco_images_dat` int(99) NOT NULL,
  `redirectco_domains_dat` int(99) NOT NULL,
  `redirectco_anzahl_vergleichsoperationen_dat` int(99) NOT NULL,
  UNIQUE KEY `redirectco_id` (`redirectco_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_redirectcounter_daten` SET `redirectco_id`='1', `redirectco_gesetzte_redirects_dat`='1', `redirectco_getestete_unterseiten_dat`='1', `redirectco_html_seiten_dat`='1', `redirectco_html_seiten_count_dat`='1', `redirectco_dokumente_dat`='1', `redirectco_images_dat`='1', `redirectco_domains_dat`='6171', `redirectco_anzahl_vergleichsoperationen_dat`='121923841'  ; ##b_dump##