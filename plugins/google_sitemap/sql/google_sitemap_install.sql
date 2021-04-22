DROP TABLE IF EXISTS `XXX_papoo_google_sitemap`; ##b_dump##

CREATE TABLE IF NOT EXISTS `XXX_papoo_google_sitemap` (
  `google_sitemap_id` int(11) NOT NULL default '0',
  `datum` varchar(255) NOT NULL,
  `changefreq` varchar(255) NOT NULL,
  `priority` varchar(255) NOT NULL
) ; ##b_dump##

INSERT INTO `XXX_papoo_google_sitemap`
(google_sitemap_id, datum, changefreq, priority)
VALUES ('1','0','always','0.5') ; ##b_dump##
