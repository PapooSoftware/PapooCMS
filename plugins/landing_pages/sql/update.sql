ALTER TABLE `XXX_plugin_kalender` ADD `kalender_name_der_kategorie` TEXT; ##b_dump##

CREATE TABLE `XXX_plugin_landing_page` (`landing_page_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`landing_page_id`)) ENGINE=MyISAM; ##b_dump##

ALTER TABLE `XXX_plugin_landing_page` ADD `landing_page_der_name_der_kategorie_` TEXT; ##b_dump##

CREATE TABLE `XXX_plugin_landing_page_sites` (`kalender_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`kalender_id`)) ENGINE=MyISAM; ##b_dump##

ALTER TABLE `XXX_plugin_landing_page_sites` ADD `kalender_interne_bezeichnung` TEXT; ##b_dump##

ALTER TABLE `XXX_plugin_landing_page_sites` ADD `kalender_domain` TEXT; ##b_dump##

ALTER TABLE `XXX_plugin_landing_page_sites` ADD `kalender_titel_der_seite` TEXT; ##b_dump##

ALTER TABLE `XXX_plugin_landing_page_sites` ADD `kalender_meta_description` TEXT; ##b_dump##

ALTER TABLE `XXX_plugin_landing_page_sites` ADD `kalender_meta_keywords` TEXT; ##b_dump##

ALTER TABLE `XXX_plugin_landing_page_sites` ADD `kalender_design` VARCHAR( 255 ) NOT NULL; ##b_dump##

ALTER TABLE `XXX_plugin_landing_page_sites` ADD `kalender_content` TEXT; ##b_dump##

ALTER TABLE `XXX_plugin_landing_page_sites` ADD `kalender_kategori_der_sseite` VARCHAR( 255 ) NOT NULL; ##b_dump##

ALTER TABLE `XXX_plugin_landing_page_sites` ADD `kalender_gacode` TEXT; ##b_dump##
