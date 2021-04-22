DROP TABLE IF EXISTS `XXX_plugin_cotent_client`; ##b_dump##
CREATE TABLE `XXX_plugin_cotent_client` (
  `plugin_cotent_client_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_cotent_client_url_der_zentrale` text,
  `plugin_cotent_client_token_key_kommt_aus_der_zentrale` text,
  `plugin_cotent_client_domain_key` text,
  PRIMARY KEY (`plugin_cotent_client_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_cotent_client` SET plugin_cotent_client_id='1', plugin_cotent_client_url_der_zentrale='http://localhost/papoo_trunk/plugins/zentrale_inhalte/show_content.php', plugin_cotent_client_token_key_kommt_aus_der_zentrale='fdgw455etzhe5hwreznjw352z6wzrjezrhbw54zh', plugin_cotent_client_domain_key='f46f8fa4c74cab411cdf96c3cf99c9317e5735ed'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_cotent_client_lookup`; ##b_dump##
CREATE TABLE `XXX_plugin_cotent_client_lookup` (
  `lookup_id` int(11) NOT NULL,
  `lookup_tab_name` varchar(255) NOT NULL,
  `lookup_feld_serial` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; ##b_dump##
