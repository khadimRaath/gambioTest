DROP TABLE IF EXISTS `feature_value_description`;
CREATE TABLE `feature_value_description` (
  `feature_value_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `feature_value_text` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`feature_value_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;