DROP TABLE IF EXISTS `properties`;
CREATE TABLE `properties` (
  `properties_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`properties_id`),
  KEY `properties_id` (`properties_id`,`sort_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `properties` VALUES(1, 1);
INSERT INTO `properties` VALUES(2, 2);