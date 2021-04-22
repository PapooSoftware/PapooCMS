DROP TABLE IF EXISTS `XXX_papoo_gruppen_sync_daten`; ##b_dump##
CREATE TABLE `XXX_papoo_gruppen_sync_daten` (
  `gruppen_sync_lang_id` int(11) NOT NULL auto_increment ,
  `gruppen_sync_lang_hlen_ie_die_erwendung_aus` varchar(255) NOT NULL ,
  `gruppen_sync_lang_ier_bitte_die_rl_eintragen_wo_die_aten` text NULL ,
  PRIMARY KEY (`gruppen_sync_lang_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_gruppen_sync_daten` SET gruppen_sync_lang_id='1', gruppen_sync_lang_hlen_ie_die_erwendung_aus='1', gruppen_sync_lang_ier_bitte_die_rl_eintragen_wo_die_aten='http://localhost/papoo_trunk/plugin.php?menuid=1&template=gruppensync/templates/sync_front.html&syncvar=0866fb38e0ea190f5429556c49d1c20a963018c6'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_gruppen_sync_lookup`; ##b_dump##
CREATE TABLE `XXX_papoo_gruppen_sync_lookup` (
  `gruppen_sync_lookup_id` int(11) NOT NULL auto_increment ,
  `gruppen_sync_lookup_lokale_gruppe` varchar(255) NOT NULL ,
  `gruppen_sync_lookup_externe_gruppe` varchar(255) NOT NULL ,
  PRIMARY KEY (`gruppen_sync_lookup_id`) 
) ENGINE=MyISAM ; ##b_dump##


