DROP TABLE IF EXISTS `gm_gprint_elements_values`;
CREATE TABLE `gm_gprint_elements_values` (
  `gm_gprint_elements_values_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languages_id` int(10) unsigned NOT NULL DEFAULT '0',
  `gm_gprint_elements_groups_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `elements_value` text,
  PRIMARY KEY (`gm_gprint_elements_values_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;