DROP TABLE IF EXISTS `properties_values_description`;
CREATE TABLE `properties_values_description` (
  `properties_values_description_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `properties_values_id` int(10) unsigned NOT NULL DEFAULT '0',
  `language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `values_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`properties_values_description_id`),
  UNIQUE KEY `unique_description` (`properties_values_id`,`language_id`),
  KEY `properties_values_description_id` (`properties_values_description_id`,`properties_values_id`,`language_id`),
  KEY `properties_values_id` (`properties_values_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `properties_values_description` VALUES(1, 1, 2, 'S');
INSERT INTO `properties_values_description` VALUES(2, 1, 1, 'S');
INSERT INTO `properties_values_description` VALUES(3, 2, 2, 'M');
INSERT INTO `properties_values_description` VALUES(4, 2, 1, 'M');
INSERT INTO `properties_values_description` VALUES(5, 3, 2, 'L');
INSERT INTO `properties_values_description` VALUES(6, 3, 1, 'L');
INSERT INTO `properties_values_description` VALUES(7, 4, 2, 'Gold');
INSERT INTO `properties_values_description` VALUES(8, 4, 1, 'Gold');
INSERT INTO `properties_values_description` VALUES(9, 5, 2, 'Rot');
INSERT INTO `properties_values_description` VALUES(10, 5, 1, 'Red');
INSERT INTO `properties_values_description` VALUES(11, 6, 2, 'Schwarz');
INSERT INTO `properties_values_description` VALUES(12, 6, 1, 'Black');