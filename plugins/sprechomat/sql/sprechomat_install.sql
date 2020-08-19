DROP TABLE IF EXISTS `XXX_sprechomat`; ##b_dump##
CREATE TABLE `XXX_sprechomat` (
  `sprechomat_id` int(11) NOT NULL auto_increment ,
  `sprechomat_aktiv_yn` int(11) NULL  DEFAULT '0' ,
  `sprechomat_serverurl` varchar(255) NULL ,
  `sprechomat_devloginid` varchar(50) NULL ,
  `sprechomat_domainid` varchar(50) NULL ,
  `sprechomat_registerlink` varchar(255) NULL ,
  `sprechomat_lokalurl` varchar(255) NULL ,
  `sprechomat_aktiv_starttext_yn` int(11) NOT NULL  DEFAULT '1' ,
  `sprechomat_aktiv_artikel_yn` int(11) NOT NULL  DEFAULT '1' ,
  `sprechomat_aktiv_kommentare_yn` int(11) NOT NULL  DEFAULT '1' ,
  `sprechomat_aktiv_gaestebuch_yn` int(11) NOT NULL  DEFAULT '1' ,
  `sprechomat_aktiv_forum_yn` int(11) NOT NULL  DEFAULT '1' ,
  PRIMARY KEY (`sprechomat_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_sprechomat` SET sprechomat_id='1', sprechomat_aktiv_yn='0', sprechomat_serverurl='http://maschine01.sprechomat.de/index.php', sprechomat_devloginid='', sprechomat_domainid='', sprechomat_registerlink='http://www.sprechomat.de/', sprechomat_lokalurl='', sprechomat_aktiv_starttext_yn='1', sprechomat_aktiv_artikel_yn='1', sprechomat_aktiv_kommentare_yn='1', sprechomat_aktiv_gaestebuch_yn='1', sprechomat_aktiv_forum_yn='1'  ; ##b_dump##


DROP TABLE IF EXISTS `XXX_sprechomat_artikelausschluss`; ##b_dump##
CREATE TABLE `XXX_sprechomat_artikelausschluss` (
  `sprechomat_artikelausschluss_id` bigint(20) NULL ,
  `sprechomat_artikelausschluss_lang_id` int(11) NULL  
) ENGINE=MyISAM ; ##b_dump##