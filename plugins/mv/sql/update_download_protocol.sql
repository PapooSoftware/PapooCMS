DROP TABLE IF EXISTS `XXX_papoo_mv_download_protocol`; ##b_dump##
CREATE TABLE IF NOT EXISTS `XXX_papoo_mv_download_protocol` (
  `dl_idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Index',
  `file_name` text NOT NULL COMMENT 'Dateiname',
  `mv_id` int(11) NOT NULL COMMENT 'Verw.-Id',
  `mv_content_id` int(11) NOT NULL COMMENT 'Datei-Id',
  `field_id` int(11) NOT NULL COMMENT 'Feld-Id',
  `dl_count` int(11) NOT NULL COMMENT 'Anzahl Downloads',
  PRIMARY KEY (`dl_idx`),
  UNIQUE KEY `dl_idx` (`dl_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Count Downloads' AUTO_INCREMENT=1 ; ##b_dump##