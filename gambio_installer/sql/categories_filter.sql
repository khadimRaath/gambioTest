DROP TABLE IF EXISTS `categories_filter`;
CREATE TABLE `categories_filter` (
  `categories_id` int(11) NOT NULL DEFAULT '0',
  `feature_id` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(11) DEFAULT NULL,
  `selection_preview_mode` varchar(45) DEFAULT NULL,
  `selection_template` varchar(45) DEFAULT NULL,
  `value_conjunction` int(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`categories_id`,`feature_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;