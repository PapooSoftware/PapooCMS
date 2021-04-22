DROP TABLE IF EXISTS `XXX_umfrage`; ##b_dump##
CREATE TABLE `XXX_umfrage` (
  `umf_id` bigint(20) NOT NULL auto_increment ,
  `umf_aktiv_janein` varchar(10) NOT NULL  DEFAULT 'nein' ,
  `umf_count` bigint(20) NOT NULL  DEFAULT '0' ,
  `umf_datum_start` varchar(50) NOT NULL ,
  `umf_datum_letzter` varchar(50) NOT NULL ,
  `umf_datum_ende` varchar(50) NOT NULL ,
  `umf_menu` varchar(255) NOT NULL  DEFAULT '0' ,
  PRIMARY KEY (`umf_id`),
  KEY `umf_aktiv_janein` (`umf_aktiv_janein`) 
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_umfrage_user`; ##b_dump##
CREATE TABLE `XXX_umfrage_user` (
  `umf_id` bigint(20) NOT NULL auto_increment ,
  `usr_id` bigint(20) NOT NULL ,
  PRIMARY KEY (`umf_id`)
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_umfrage` SET umf_id='1', umf_aktiv_janein='ja', umf_count='24', umf_datum_start='2007-07-01 16:56:21', umf_datum_letzter='2007-07-10 15:44:41', umf_datum_ende='00000000000000', umf_menu='0'  ; ##b_dump##
INSERT INTO `XXX_umfrage` SET umf_id='15', umf_aktiv_janein='nein', umf_count='2', umf_datum_start='2007-06-01 16:56:21', umf_datum_letzter='2007-07-01 16:56:21', umf_datum_ende='00000000000000', umf_menu='0'  ; ##b_dump##
INSERT INTO `XXX_umfrage` SET umf_id='17', umf_aktiv_janein='nein', umf_count='8', umf_datum_start='2007-06-01 16:56:21', umf_datum_letzter='2007-07-01 16:56:21', umf_datum_ende='00000000000000', umf_menu='0'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_umfrage_antworten`; ##b_dump##
CREATE TABLE `XXX_umfrage_antworten` (
  `umfant_id` bigint(20) NOT NULL auto_increment ,
  `umfant_umf_id` bigint(20) NOT NULL ,
  `umfant_count` bigint(20) NOT NULL  DEFAULT '0' ,
  PRIMARY KEY (`umfant_id`),
  KEY `umfant_umf_id` (`umfant_umf_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_umfrage_antworten` SET umfant_id='1', umfant_umf_id='1', umfant_count='13'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten` SET umfant_id='2', umfant_umf_id='1', umfant_count='4'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten` SET umfant_id='3', umfant_umf_id='1', umfant_count='7'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten` SET umfant_id='4', umfant_umf_id='15', umfant_count='0'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten` SET umfant_id='5', umfant_umf_id='15', umfant_count='1'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten` SET umfant_id='6', umfant_umf_id='15', umfant_count='1'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten` SET umfant_id='15', umfant_umf_id='17', umfant_count='3'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten` SET umfant_id='8', umfant_umf_id='17', umfant_count='3'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten` SET umfant_id='9', umfant_umf_id='17', umfant_count='2'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_umfrage_antworten_language`; ##b_dump##
CREATE TABLE `XXX_umfrage_antworten_language` (
  `umfantlan_umfant_id` bigint(20) NOT NULL ,
  `umfantlan_lan_id` bigint(20) NOT NULL ,
  `umfantlan_text` text NOT NULL ,
  KEY `umfantlan_umfant_id` (`umfantlan_umfant_id`),
  KEY `umfantlan_lan_id` (`umfantlan_lan_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='1', umfantlan_lan_id='1', umfantlan_text='Stephan Bergmann'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='1', umfantlan_lan_id='2', umfantlan_text='Stephan Bergmann'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='2', umfantlan_lan_id='1', umfantlan_text='Irgend so ein Wicht den keiner kennt'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='2', umfantlan_lan_id='2', umfantlan_text='Unknown guy'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='3', umfantlan_lan_id='1', umfantlan_text='Wen interessiert das schon?'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='3', umfantlan_lan_id='2', umfantlan_text='Who cares?'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='4', umfantlan_lan_id='1', umfantlan_text='Jeder'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='4', umfantlan_lan_id='2', umfantlan_text='Everybody'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='5', umfantlan_lan_id='1', umfantlan_text='Niemand'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='5', umfantlan_lan_id='2', umfantlan_text='Nobody'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='6', umfantlan_lan_id='1', umfantlan_text='Keine Ahnung'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='6', umfantlan_lan_id='2', umfantlan_text='Don\'t know'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='15', umfantlan_lan_id='2', umfantlan_text='Don\'t know'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='15', umfantlan_lan_id='1', umfantlan_text='Ich versteh die Frage nicht'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='8', umfantlan_lan_id='1', umfantlan_text='Reine Zeitverschwendung'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='8', umfantlan_lan_id='2', umfantlan_text='Wast of time'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='9', umfantlan_lan_id='1', umfantlan_text='Eine tolle Sache'  ; ##b_dump##
INSERT INTO `XXX_umfrage_antworten_language` SET umfantlan_umfant_id='9', umfantlan_lan_id='2', umfantlan_text='Great thing'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_umfrage_language`; ##b_dump##
CREATE TABLE `XXX_umfrage_language` (
  `umflan_umf_id` bigint(20) NOT NULL ,
  `umflan_lan_id` bigint(20) NOT NULL ,
  `umflan_text` text NOT NULL ,
  KEY `umflan_umf_id` (`umflan_umf_id`),
  KEY `umflan_lan_id` (`umflan_lan_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_umfrage_language` SET umflan_umf_id='1', umflan_lan_id='1', umflan_text='Wer hat das Umfrage-Plugin programmiert?'  ; ##b_dump##
INSERT INTO `XXX_umfrage_language` SET umflan_umf_id='15', umflan_lan_id='1', umflan_text='Wer hat Angst vor dem schwarzen Mann?'  ; ##b_dump##
INSERT INTO `XXX_umfrage_language` SET umflan_umf_id='17', umflan_lan_id='2', umflan_text='What\'s a poll?'  ; ##b_dump##
INSERT INTO `XXX_umfrage_language` SET umflan_umf_id='15', umflan_lan_id='2', umflan_text='Who\'s affraid of the big, black man?'  ; ##b_dump##
INSERT INTO `XXX_umfrage_language` SET umflan_umf_id='17', umflan_lan_id='1', umflan_text='Was ist eine Umfrage?'  ; ##b_dump##
INSERT INTO `XXX_umfrage_language` SET umflan_umf_id='1', umflan_lan_id='2', umflan_text='Who coded the poll-Plugin?'  ; ##b_dump##