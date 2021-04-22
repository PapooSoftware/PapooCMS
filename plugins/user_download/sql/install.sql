DROP TABLE IF EXISTS `XXX_plugin_user_download_files`; ##b_dump##

CREATE TABLE IF NOT EXISTS `XXX_plugin_user_download_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `size` int(30) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uploaded_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_user_download_lookup_uf`; ##b_dump##

CREATE TABLE IF NOT EXISTS `XXX_plugin_user_download_lookup_uf` (
  `userid` int(11) NOT NULL,
  `fileid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1; ##b_dump##


