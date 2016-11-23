DROP TABLE IF EXISTS `addon_values_storage`;
CREATE TABLE `addon_values_storage` (
  `addon_value_id` int(11) NOT NULL AUTO_INCREMENT,
  `container_type` varchar(64) DEFAULT NULL,
  `container_id` int(11) DEFAULT NULL,
  `addon_key` varchar(255) DEFAULT NULL,
  `addon_value` text,
  PRIMARY KEY (`addon_value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;