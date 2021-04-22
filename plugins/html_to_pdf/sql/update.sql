CREATE TABLE `XXX_plugin_html2pdf` (`ext_search_id` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`ext_search_id`)) ENGINE=MyISAM; ##b_dump##

ALTER TABLE `XXX_plugin_html2pdf` ADD `ext_search_pdf_template` TEXT; ##b_dump##

ALTER TABLE `XXX_plugin_html2pdf` ADD `ext_search_css_daten` TEXT; ##b_dump##

ALTER TABLE `XXX_plugin_html2pdf` ADD `ext_search_pdf_template_file` TEXT; ##b_dump##

