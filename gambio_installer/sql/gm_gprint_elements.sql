DROP TABLE IF EXISTS `gm_gprint_elements`;
CREATE TABLE `gm_gprint_elements` (
  `gm_gprint_elements_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gm_gprint_elements_groups_id` int(10) unsigned NOT NULL DEFAULT '0',
  `gm_gprint_surfaces_id` int(10) unsigned NOT NULL DEFAULT '0',
  `position_x` int(10) NOT NULL DEFAULT '0',
  `position_y` int(10) NOT NULL DEFAULT '0',
  `height` int(10) unsigned NOT NULL DEFAULT '0',
  `width` int(10) unsigned NOT NULL DEFAULT '0',
  `z_index` int(10) NOT NULL DEFAULT '0',
  `max_characters` int(10) NOT NULL DEFAULT '0',
  `allowed_extensions` varchar(255) NOT NULL DEFAULT '',
  `show_name` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `minimum_filesize` decimal(7,4) NOT NULL DEFAULT '0.0000',
  `maximum_filesize` decimal(7,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`gm_gprint_elements_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;