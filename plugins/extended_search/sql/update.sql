ALTER TABLE `XXX_plugin_ext_search_page` ADD `ext_search_document_path` varchar(255) DEFAULT NULL;
ALTER TABLE `XXX_plugin_ext_search_page` ADD `ext_search_seite_mv_id` INT( 11 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_ext_search_page` ADD `ext_search_seite_mv_content_id` INT( 11 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_ext_search_config` ADD `ext_search_teaser_max_length` smallint UNSIGNED NOT NULL DEFAULT 400 AFTER `ext_search_nzahl_der_uchergebnisse`; ##b_dump##
ALTER TABLE `XXX_plugin_ext_search_config` ADD `ext_search_schwelle` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_ext_search_config` ADD `ext_search_zuweisung` TEXT; ##b_dump##
ALTER TABLE `XXX_plugin_ext_search_config` ADD `ext_search_menus` text COLLATE utf8_bin NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_ext_search_config` ADD `ext_search_ur_doc` varchar(255) NOT NULL DEFAULT '',

