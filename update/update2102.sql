ALTER TABLE `XXX_plugin_ext_search_config` ADD `ext_search_teaser_max_length` smallint UNSIGNED NOT NULL DEFAULT 400 AFTER `ext_search_nzahl_der_uchergebnisse`; ##b_dump##
UPDATE `XXX_papoo_menuint` SET menulink = 'plugin:faq/templates/faq_cat_back_main.html' WHERE menulink LIKE 'plugin:faq/templates/cat_back_main.html'; ##b_dump##