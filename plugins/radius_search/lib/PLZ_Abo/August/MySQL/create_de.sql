USE myDBname

CREATE TABLE de
(
  land		varchar(2),
  plz		varchar(5),
  ort           varchar(48),
  ortsname           varchar(48),
  ortsnamezusatz           varchar(48),
  ortsteil          varchar(48),
  kreis		varchar(48),
  landratsamt	varchar(48),	
  bundesland    varchar(48),
  postfach	int,
  latitude      decimal(13, 9),
  longitude     decimal(13, 9)
)


CREATE TABLE IF NOT EXISTS `tappx07_papoo_radius_search_data_de` (
  `land` varchar(2) DEFAULT NULL,
  `plz` varchar(5) DEFAULT NULL,
  `ort` varchar(48) DEFAULT NULL,
  `ortsname` varchar(48) DEFAULT NULL,
  `ortsnamezusatz` varchar(48) DEFAULT NULL,
  `ortsteil` varchar(48) DEFAULT NULL,
  `kreis` varchar(48) DEFAULT NULL,
  `landratsamt` varchar(48) DEFAULT NULL,
  `bundesland` varchar(48) DEFAULT NULL,
  `postfach` int(11) DEFAULT NULL,
  `latitude` decimal(13,9) DEFAULT NULL,
  `longitude` decimal(13,9) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;