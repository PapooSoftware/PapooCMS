DROP TABLE IF EXISTS `XXX_dev_messages`; ##b_dump##
CREATE TABLE `XXX_dev_messages` (
  `msg_id` bigint(20) NOT NULL auto_increment ,
  `msg_grp_id` bigint(20) NULL ,
  `msg_order_id` int(11) NULL ,
  `msg_name` varchar(250) NULL ,
  `msg_name_alt` varchar(250) NULL ,
  PRIMARY KEY (`msg_id`),
  KEY `msg_name` (`msg_name`),
  KEY `msg_group_id` (`msg_grp_id`),
  KEY `msg_order_id` (`msg_order_id`),
  KEY `msg_name_alt` (`msg_name_alt`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_dev_messages` SET msg_id='1', msg_grp_id='3', msg_order_id='10', msg_name='hello_world', msg_name_alt=''  ; ##b_dump##
INSERT INTO `XXX_dev_messages` SET msg_id='2', msg_grp_id='4', msg_order_id='10', msg_name='test1', msg_name_alt=''  ; ##b_dump##
INSERT INTO `XXX_dev_messages` SET msg_id='3', msg_grp_id='4', msg_order_id='20', msg_name='test2', msg_name_alt=''  ; ##b_dump##
INSERT INTO `XXX_dev_messages` SET msg_id='4', msg_grp_id='4', msg_order_id='30', msg_name='test3', msg_name_alt=''  ; ##b_dump##
INSERT INTO `XXX_dev_messages` SET msg_id='5', msg_grp_id='5', msg_order_id='10', msg_name='test1', msg_name_alt=''  ; ##b_dump##


DROP TABLE IF EXISTS `XXX_dev_messages_group`; ##b_dump##
CREATE TABLE `XXX_dev_messages_group` (
  `msggrp_id` bigint(20) NOT NULL auto_increment ,
  `msggrp_parent_id` bigint(20) NOT NULL  DEFAULT '0' ,
  `msggrp_order_id` int(11) NULL ,
  `msggrp_name` varchar(250) NULL ,
  `msggrp_frontback` varchar(50) NOT NULL  DEFAULT 'FRONT' ,
  `msggrp_plugin_name` varchar(250) NULL ,
  PRIMARY KEY (`msggrp_id`),
  KEY `msggrp_order_id` (`msggrp_order_id`),
  KEY `msggrp_name` (`msggrp_name`),
  KEY `msggrp_frontback` (`msggrp_frontback`),
  KEY `msggrp_plugin_name` (`msggrp_plugin_name`),
  KEY `msggrp_parent_id` (`msggrp_parent_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_dev_messages_group` SET msggrp_id='3', msggrp_parent_id='0', msggrp_order_id='10', msggrp_name='general', msggrp_frontback='FRONT', msggrp_plugin_name=''  ; ##b_dump##
INSERT INTO `XXX_dev_messages_group` SET msggrp_id='4', msggrp_parent_id='2', msggrp_order_id='10', msggrp_name='sub_01', msggrp_frontback='FRONT', msggrp_plugin_name=''  ; ##b_dump##
INSERT INTO `XXX_dev_messages_group` SET msggrp_id='5', msggrp_parent_id='2', msggrp_order_id='20', msggrp_name='sub_02', msggrp_frontback='FRONT', msggrp_plugin_name=''  ; ##b_dump##
INSERT INTO `XXX_dev_messages_group` SET msggrp_id='1', msggrp_parent_id='0', msggrp_order_id='10', msggrp_name='_alter_namensraum_front', msggrp_frontback='FRONT', msggrp_plugin_name=''  ; ##b_dump##
INSERT INTO `XXX_dev_messages_group` SET msggrp_id='2', msggrp_parent_id='0', msggrp_order_id='10', msggrp_name='_alter_namensraum_back', msggrp_frontback='BACK', msggrp_plugin_name=''  ; ##b_dump##


DROP TABLE IF EXISTS `XXX_dev_messages_language`; ##b_dump##
CREATE TABLE `XXX_dev_messages_language` (
  `msglang_msg_id` bigint(20) NOT NULL  DEFAULT '0' ,
  `msglang_lang_short` char(2) NULL ,
  `msglang_text` text NULL ,
  KEY `msglan_msg_id` (`msglang_msg_id`),
  KEY `msglang_lang_short` (`msglang_lang_short`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_dev_messages_language` SET msglang_msg_id='1', msglang_lang_short='de', msglang_text='Hallo Welt!'  ; ##b_dump##
INSERT INTO `XXX_dev_messages_language` SET msglang_msg_id='1', msglang_lang_short='en', msglang_text='Hello world!'  ; ##b_dump##
INSERT INTO `XXX_dev_messages_language` SET msglang_msg_id='2', msglang_lang_short='de', msglang_text='Eintrag \"test1\" in Sub-Gruppe \"sub_01\"'  ; ##b_dump##
INSERT INTO `XXX_dev_messages_language` SET msglang_msg_id='2', msglang_lang_short='en', msglang_text='entry \"test1\" in sub-group \"sub_01\"'  ; ##b_dump##
INSERT INTO `XXX_dev_messages_language` SET msglang_msg_id='3', msglang_lang_short='de', msglang_text='\"test2\" in \"sub_01\" (de)'  ; ##b_dump##
INSERT INTO `XXX_dev_messages_language` SET msglang_msg_id='3', msglang_lang_short='en', msglang_text='\"test2\" in \"sub_01\" (en)'  ; ##b_dump##
INSERT INTO `XXX_dev_messages_language` SET msglang_msg_id='5', msglang_lang_short='de', msglang_text='Eintrag \"test1\" in Sub-Gruppe \"sub_02\"'  ; ##b_dump##
INSERT INTO `XXX_dev_messages_language` SET msglang_msg_id='5', msglang_lang_short='en', msglang_text='entry \"test1\" in sub-group \"sub_02\"'  ; ##b_dump##

