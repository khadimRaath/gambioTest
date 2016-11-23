DROP TABLE IF EXISTS `zones_to_geo_zones`;
CREATE TABLE `zones_to_geo_zones` (
  `association_id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_country_id` int(11) NOT NULL DEFAULT '0',
  `zone_id` int(11) NOT NULL DEFAULT '0',
  `geo_zone_id` int(11) NOT NULL DEFAULT '0',
  `last_modified` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `date_added` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`association_id`),
  KEY `zone_id` (`zone_id`),
  KEY `geo_zone_id` (`geo_zone_id`,`zone_country_id`),
  KEY `zone_country_id` (`zone_country_id`,`zone_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;