DROP TABLE IF EXISTS `feature_description`;
CREATE TABLE `feature_description` (
  `feature_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `feature_name` varchar(45) DEFAULT NULL,
  `feature_admin_name` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`feature_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;