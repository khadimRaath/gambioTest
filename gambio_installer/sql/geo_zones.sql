DROP TABLE IF EXISTS `geo_zones`;
CREATE TABLE `geo_zones` (
  `geo_zone_id` int(11) NOT NULL AUTO_INCREMENT,
  `geo_zone_name` varchar(32) NOT NULL DEFAULT '',
  `geo_zone_description` varchar(255) NOT NULL DEFAULT '',
  `last_modified` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`geo_zone_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;