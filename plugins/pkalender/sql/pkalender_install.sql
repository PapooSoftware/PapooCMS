DROP TABLE IF EXISTS `XXX_plugin_kalender`; ##b_dump##
CREATE TABLE `XXX_plugin_kalender` (
  `kalender_id` int(11) NOT NULL AUTO_INCREMENT,
  `kalender_lang_id` int(11) NOT NULL,
  `kalender_bezeichnung_des_kalenders` text,
  `kalender_eintrge_von_aussen` varchar(255) NOT NULL,
  `kalender_direkt_freischalten` varchar(255) NOT NULL,
  `kalender_kalender_benutzt_kategorien` varchar(255) NOT NULL,
  `kalender_text_oberhalb` text,
  `kalender_whlen_sie_hier_den_termin_aus` text,
  `kalender_kategorien_im_kalender_jede_zeile_eine_kategorie` text,
  `kalender_modul_id` int(11) NOT NULL,
  `kalender_email_versenden_bei_neuen_eintrag_von_auen` varchar(255) NOT NULL,
  `kalender_email_adresse_fr_den_versand_dieser_mail` text,
  `kalender_modul_liste_id` int(11) NOT NULL,
  `kalender_anzahl_der_eintrge_im_modul_aktuellste_eintrge` text,
  `kalender_xml_google` text,
  PRIMARY KEY (`kalender_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_plugin_kalender` SET kalender_id='25', kalender_lang_id='1', kalender_bezeichnung_des_kalenders='Kalender 1', kalender_eintrge_von_aussen='0', kalender_direkt_freischalten='0', kalender_kalender_benutzt_kategorien='', kalender_text_oberhalb='<p>Kalender 1</p>', kalender_whlen_sie_hier_den_termin_aus='', kalender_kategorien_im_kalender_jede_zeile_eine_kategorie='Kategorie 1\r\nKategorie 2', kalender_modul_id='106', kalender_email_versenden_bei_neuen_eintrag_von_auen='0', kalender_email_adresse_fr_den_versand_dieser_mail='', kalender_modul_liste_id='107', kalender_anzahl_der_eintrge_im_modul_aktuellste_eintrge='10', kalender_xml_google=''  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_kalender_date`; ##b_dump##
CREATE TABLE `XXX_plugin_kalender_date` (
  `pkal_date_id` int(11) NOT NULL AUTO_INCREMENT,
  `pkal_date_lang_id` int(11) NOT NULL,
  `pkal_date_kalender_id` int(11) NOT NULL,
  `pkal_date_titel_des_termins` text,
  `pkal_date_terminbeschreibung` text,
  `pkal_date_kategorie_im_kalender` varchar(255) NOT NULL,
  `pkal_date_start_datum` varchar(255) DEFAULT NULL,
  `pkal_date_end_datum` text,
  `pkal_date_uhrzeit_beginn` text,
  `pkal_date_uhrzeit_ende` text,
  `pkal_date_veranstaltung_wiederholt_sich_so_oft` text,
  `pkal_date_an_jedem` varchar(255) NOT NULL,
  `pkal_date_link_zu_terminfeld` text,
  `pkal_date_eintrag_im_frontend_freischalten` varchar(255) NOT NULL,
  PRIMARY KEY (`pkal_date_id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_plugin_kalender_date` SET pkal_date_id='36', pkal_date_lang_id='1', pkal_date_kalender_id='25', pkal_date_titel_des_termins='Papoo CMS Release', pkal_date_terminbeschreibung='<p>Papoo CMS Release</p>', pkal_date_kategorie_im_kalender='2', pkal_date_start_datum='1277935200', pkal_date_end_datum='1278021600', pkal_date_uhrzeit_beginn='', pkal_date_uhrzeit_ende='', pkal_date_veranstaltung_wiederholt_sich_so_oft='', pkal_date_an_jedem='', pkal_date_link_zu_terminfeld='', pkal_date_eintrag_im_frontend_freischalten='1'  ; ##b_dump##
INSERT INTO `XXX_plugin_kalender_date` SET pkal_date_id='37', pkal_date_lang_id='1', pkal_date_kalender_id='25', pkal_date_titel_des_termins='asdasd', pkal_date_terminbeschreibung='<p>asdasd</p>', pkal_date_kategorie_im_kalender='0', pkal_date_start_datum='1332284400', pkal_date_end_datum='1332885600', pkal_date_uhrzeit_beginn='', pkal_date_uhrzeit_ende='', pkal_date_veranstaltung_wiederholt_sich_so_oft='', pkal_date_an_jedem='', pkal_date_link_zu_terminfeld='', pkal_date_eintrag_im_frontend_freischalten='1'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_kalender_lookup_cats`; ##b_dump##
CREATE TABLE `XXX_plugin_kalender_lookup_cats` (
  `kalender_cid_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_plugin_kalender_lookup_cats` SET kalender_cid_id='25', cat_id='2', cat_name='Kategorie 2'  ; ##b_dump##
INSERT INTO `XXX_plugin_kalender_lookup_cats` SET kalender_cid_id='25', cat_id='1', cat_name='Kategorie 1'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_kalender_lookup_read`; ##b_dump##
CREATE TABLE `XXX_plugin_kalender_lookup_read` (
  `kalender_id_id` int(11) NOT NULL,
  `kalender_gruppe_lese_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_plugin_kalender_lookup_read` SET kalender_id_id='25', kalender_gruppe_lese_id='10'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_kalender_lookup_read_date`; ##b_dump##
CREATE TABLE `XXX_plugin_kalender_lookup_read_date` (
  `date_id_id` int(11) NOT NULL,
  `date_gruppe_lese_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_plugin_kalender_lookup_read_date` SET date_id_id='36', date_gruppe_lese_id='10'  ; ##b_dump##
INSERT INTO `XXX_plugin_kalender_lookup_read_date` SET date_id_id='37', date_gruppe_lese_id='10'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_kalender_lookup_write`; ##b_dump##
CREATE TABLE `XXX_plugin_kalender_lookup_write` (
  `kalender_wid_id` int(11) NOT NULL,
  `kalender_gruppe_write_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_plugin_kalender_lookup_write` SET kalender_wid_id='25', kalender_gruppe_write_id='1'  ; ##b_dump##
