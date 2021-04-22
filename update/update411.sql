ALTER TABLE `XXX_papoo_daten` ADD `mod_free` INT NOT NULL AFTER `mod_mime`; ##b_dump## 
ALTER TABLE `XXX_plugin_fb_like_button` ADD `fb_plugin_likebutton_likeme_button_aktivieren` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_fb_like_button` ADD `fb_plugin_likebutton_button_unterhalb_der_berschrift_` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_fb_like_button` ADD `fb_plugin_likebutton_button_unterhalb_des_artikels_anzeigen` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_fb_like_button` ADD `fb_plugin_likebutton_button_im_forum_anzeigen` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_fb_like_button` ADD `fb_plugin_likebutton_liketext` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_kalender` ADD `kalender_xml_google` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_galerie_galerien` ADD `parent_id` INT( 11 ) NOT NULL DEFAULT '999999999' AFTER `gal_id`; ##b_dump##
ALTER TABLE `XXX_plugin_ext_search_page` ADD `ext_search_seite_mv_id` INT( 11 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_plugin_ext_search_page` ADD `ext_search_seite_mv_content_id` INT( 11 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_google_maps_tabelle` ADD `gmap_apikey_2` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_config` ADD `config_html_tidy_funktionaktivieren` varchar(255) NOT NULL; ##b_dump## 

