DROP TABLE IF EXISTS `XXX_plugin_flexslider`; ##b_dump##
CREATE TABLE `XXX_plugin_flexslider` (
  `fs_id` bigint(20) NOT NULL auto_increment,
  `fs_menu_id` bigint(20) default NULL,
  `fs_order_id` int(11) default NULL,
  `fs_bild` text default NULL,
  `fs_link` text default NULL,
  `fs_text` text default NULL,
  `fs_ueberschrift_1` text default NULL,
  `fs_ueberschrift_2` text default NULL,
  PRIMARY KEY  (`fs_id`),
  KEY `fs_menuid` (`fs_menu_id`),
  KEY `fs_order_id` (`fs_order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_flexslider` SET fs_id='1', fs_menu_id='0', fs_order_id='10', fs_bild='src=\"http://lorempixel.com/510/250/animals/1\" alt=\"Ein Tierbild (1)\" title=\"Ein Tierbild (1)\"', fs_ueberschrift='Tierbild 1', fs_text='Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', fs_link='href=\"http://lorempixel.com\" target=\"_blank\"'  ; ##b_dump##
INSERT INTO `XXX_plugin_flexslider` SET fs_id='2', fs_menu_id='0', fs_order_id='20', fs_bild='src=\"http://lorempixel.com/510/250/animals/2\" alt=\"Ein Tierbild (2)\" title=\"Ein Tierbild (2)\"', fs_ueberschrift='Tierbild 1', fs_text='Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', fs_link='href=\"http://lorempixel.com\" target=\"_blank\"'  ; ##b_dump##
INSERT INTO `XXX_plugin_flexslider` SET fs_id='3', fs_menu_id='0', fs_order_id='30', fs_bild='src=\"http://lorempixel.com/510/250/animals/3\" alt=\"Ein Tierbild (3)\" title=\"Ein Tierbild (3)\"', fs_ueberschrift='Tierbild 1', fs_text='Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', fs_link='href=\"http://lorempixel.com\" target=\"_blank\"'  ; ##b_dump##
