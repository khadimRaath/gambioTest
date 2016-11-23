DROP TABLE IF EXISTS `gm_gprint_elements_groups`;
CREATE TABLE `gm_gprint_elements_groups` (
  `gm_gprint_elements_groups_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_type` varchar(255) DEFAULT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`gm_gprint_elements_groups_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;