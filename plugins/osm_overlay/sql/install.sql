CREATE TABLE `XXX_plugin_osm_overlay` (
  `id` int(1) NOT NULL auto_increment,
  `lon` double(12,9) NULL,
  `lat` double(12,9) NULL,
  `zoom` int(2) NULL,
  `search_zoom` int(2) NULL,
  `table_name` varchar(255) NULL,
  `json_path` varchar(255) NULL,
  `json_url` varchar(255) NULL,
  `link` varchar(255) NULL,
  `link_field` varchar(255) NULL,
  `label_field` varchar(255) NULL,
  `header` text NULL,
  `footer` text NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_plugin_osm_overlay` VALUES
    ('1', '9', '51', '6', '11', 'papoo_mv_content_1_search_1', 'templates_c', '', '', '', '', 'Header', 'Footer'); ##b_dump##

CREATE TABLE `XXX_plugin_osm_overlay_fields` (
  `id` int(1) NOT NULL auto_increment,
  `field_name` varchar(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ; ##b_dump##

CREATE TABLE `XXX_plugin_osm_overlay_loc_fields` (
  `id` int(1) NOT NULL auto_increment,
  `name` varchar(255) NULL,
  `type` varchar(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ; ##b_dump##