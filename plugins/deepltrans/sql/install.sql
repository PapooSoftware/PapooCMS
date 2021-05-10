DROP TABLE IF EXISTS `XXX_deepltrans_data`; ##b_dump##
CREATE TABLE `XXX_deepltrans_data` (

) ENGINE=MyISAM; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_deepltrans_einstellun_form`; ##b_dump##
CREATE TABLE `XXX_plugin_deepltrans_einstellun_form` (
                                                         `deepltrans_id` int NOT NULL AUTO_INCREMENT,
                                                         `deepltrans_deepl_key_input` text,
                                                         PRIMARY KEY (`deepltrans_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_plugin_deepltrans_einstellun_form` SET `deepltrans_id`='1', `deepltrans_deepl_key_input`='xxx'  ; ##b_dump##




