DROP TABLE IF EXISTS `feature_set_values`;
CREATE TABLE `feature_set_values` (
  `feature_set_id` int(11) unsigned NOT NULL DEFAULT '0',
  `feature_value_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`feature_set_id`,`feature_value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;