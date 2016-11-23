DROP TABLE IF EXISTS `feature_index`;
CREATE TABLE `feature_index` (
  `feature_set_id` int(11) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  `feature_value_index` text,
  PRIMARY KEY (`feature_set_id`),
  FULLTEXT KEY `feature_value_index` (`feature_value_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;