DROP TABLE IF EXISTS `XXX_papoo_easyedit_daten`; ##b_dump##
CREATE TABLE `XXX_papoo_easyedit_daten` (
  `easy_id` int(11) NOT NULL auto_increment ,
  `easy_men_id` int(11) NOT NULL ,
  `easy_order_id` int(11) NOT NULL DEFAULT '1' ,
  `easy_easy_ok` varchar(255) NOT NULL ,
  PRIMARY KEY (`easy_id`) 
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_easyedit_gruppe`; ##b_dump##
CREATE TABLE `XXX_papoo_easyedit_gruppe` (
  `easy_gid` int(11) NOT NULL auto_increment ,
  `easy_group_id` int(11) NOT NULL ,
  `easy_geasy_ok` varchar(255) NOT NULL ,
  PRIMARY KEY (`easy_gid`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_daten` SET easy_id='1', easy_men_id='1', easy_order_id='1', easy_easy_ok='1'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_daten` SET easy_id='2', easy_men_id='2', easy_order_id='0', easy_easy_ok='0'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_daten` SET easy_id='3', easy_men_id='5', easy_order_id='0', easy_easy_ok='0'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_daten` SET easy_id='4', easy_men_id='6', easy_order_id='1', easy_easy_ok='1'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_daten` SET easy_id='5', easy_men_id='15', easy_order_id='1', easy_easy_ok='1'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_daten` SET easy_id='6', easy_men_id='16', easy_order_id='1', easy_easy_ok='1'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_daten` SET easy_id='7', easy_men_id='12', easy_order_id='0', easy_easy_ok='0'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_daten` SET easy_id='8', easy_men_id='14', easy_order_id='0', easy_easy_ok='0'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_daten` SET easy_id='9', easy_men_id='17', easy_order_id='1', easy_easy_ok='1'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_daten` SET easy_id='10', easy_men_id='13', easy_order_id='0', easy_easy_ok='0'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_daten` SET easy_id='11', easy_men_id='3', easy_order_id='0', easy_easy_ok='0'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_daten` SET easy_id='12', easy_men_id='4', easy_order_id='0', easy_easy_ok='0'  ; ##b_dump##




INSERT INTO `XXX_papoo_easyedit_gruppe` SET easy_gid='1', easy_group_id='10', easy_geasy_ok='0'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_gruppe` SET easy_gid='2', easy_group_id='1', easy_geasy_ok='1'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_gruppe` SET easy_gid='3', easy_group_id='12', easy_geasy_ok='0'  ; ##b_dump##

INSERT INTO `XXX_papoo_easyedit_gruppe` SET easy_gid='4', easy_group_id='11', easy_geasy_ok='0'  ; ##b_dump##


