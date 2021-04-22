DROP TABLE IF EXISTS `XXX_plugin_cotent_client`; ##b_dump##
CREATE TABLE `XXX_plugin_cotent_client` (
  `plugin_cotent_client_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_cotent_client_url_der_zentrale` text,
  `plugin_cotent_client_token_key_kommt_aus_der_zentrale` text,
  `plugin_cotent_client_domain_key` text,
  PRIMARY KEY (`plugin_cotent_client_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_cotent_client` SET plugin_cotent_client_id='1', plugin_cotent_client_url_der_zentrale='', plugin_cotent_client_token_key_kommt_aus_der_zentrale='xy', plugin_cotent_client_domain_key=''  ; ##b_dump##
