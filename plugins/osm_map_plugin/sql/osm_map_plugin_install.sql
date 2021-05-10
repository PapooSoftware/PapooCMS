DROP TABLE IF EXISTS `XXX_papoo_osm_map_plugin_config`; ##b_dump##
CREATE TABLE `XXX_papoo_osm_map_plugin_config` (
  `id` tinyint default 1 not null,
  `map_breite` varchar(255) DEFAULT NULL,
  `map_hoehe` varchar(255) DEFAULT NULL,
  `map_zoom` int DEFAULT NULL,
  /* according to the nominatim usage guidelines the api results must be cached, default is set to 1 month */
  `nominatim_cache_lifetime` int DEFAULT 1,
  `nominatim_cache_lifetime_unit` ENUM('day', 'week', 'month', 'year') DEFAULT 'month',
  primary key(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_papoo_osm_map_plugin_config` SET map_breite='100%', map_hoehe='300px', map_zoom='17'  ; ##b_dump##
