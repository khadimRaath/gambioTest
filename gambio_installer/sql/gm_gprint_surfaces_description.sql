DROP TABLE IF EXISTS `gm_gprint_surfaces_description`;
CREATE TABLE `gm_gprint_surfaces_description` (
  `gm_gprint_surfaces_id` int(10) unsigned NOT NULL DEFAULT '0',
  `languages_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`gm_gprint_surfaces_id`,`languages_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;