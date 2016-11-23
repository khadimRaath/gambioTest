DROP TABLE IF EXISTS `feature_value`;
CREATE TABLE `feature_value` (
  `feature_value_id` int(11) NOT NULL AUTO_INCREMENT,
  `feature_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`feature_value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;