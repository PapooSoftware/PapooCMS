DROP TABLE IF EXISTS `XXX_papoo_flexint_flexinttabelle`; ##b_dump##
CREATE TABLE `XXX_papoo_flexint_flexinttabelle` (
  `flexint_id` int(11) NOT NULL auto_increment ,
  `flexint_zahl` int(11) NULL ,
  `flexint_name` text NULL ,
  `flexint_unterverzeichnis` text NULL ,
  `flexint_url` text NULL ,
  `flexint_url_sans` text NULL ,
  `flexint_trenner1` text NULL ,
  `flexint_trenner2` text NULL ,
  PRIMARY KEY (`flexint_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_flexint_flexinttabelle` SET flexint_id='1', flexint_zahl='123', flexint_url='http://www.building-services.de/plugin.php?ProjektHeadline_4=&APFirma_22=&Branche_31=&search_mv=&mv_submit=Finden&mv_submit=Finden&mv_id=1&menuid=44&onemv=1&template=mv%2Ftemplates%2Fmv_search_front_onemv.html', flexint_url_sans='http://www.building-services.de', flexint_trenner1='', flexint_trenner2=''  ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_flexint_texte_lang`; ##b_dump##
CREATE TABLE `XXX_papoo_flexint_texte_lang` (
	`flexint_id` int(11) NOT NULL ,
  `flexint_langid` int(11) NOT NULL ,
  `flexint_text_oben` text NOT NULL ,
  `flexint_text_unten` text NOT NULL
) ENGINE=MyISAM ; ##b_dump##