DROP TABLE IF EXISTS `XXX_plugin_html2pdf`; ##b_dump##
CREATE TABLE `XXX_plugin_html2pdf` (
  `ext_search_id` int(11) NOT NULL AUTO_INCREMENT,
  `ext_search_pdf_template` text,
  `ext_search_css_daten` text,
  `ext_search_pdf_template_file` text,
  PRIMARY KEY (`ext_search_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_html2pdf` SET ext_search_id='1', ext_search_pdf_template='', ext_search_css_daten='div.pdf_content {margin-left:0.8cm;}\r\ndiv.pdf_content {padding-top:2cm;}', ext_search_pdf_template_file='0ebe7_vorlage_allgemein_pdf.pdf'  ; ##b_dump##
