DROP TABLE IF EXISTS `gm_gprint_surfaces`;
CREATE TABLE `gm_gprint_surfaces` (
  `gm_gprint_surfaces_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gm_gprint_surfaces_groups_id` int(10) unsigned NOT NULL DEFAULT '0',
  `width` int(10) unsigned NOT NULL DEFAULT '0',
  `height` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`gm_gprint_surfaces_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;