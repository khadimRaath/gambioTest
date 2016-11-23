DROP TABLE IF EXISTS `properties_description`;
CREATE TABLE `properties_description` (
  `properties_description_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `properties_id` int(10) unsigned NOT NULL DEFAULT '0',
  `language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `properties_name` varchar(255) NOT NULL DEFAULT '',
  `properties_admin_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`properties_description_id`),
  KEY `properties_id` (`properties_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `properties_description` VALUES(1, 1, 2, 'Größe', '');
INSERT INTO `properties_description` VALUES(2, 1, 1, 'Size', '');
INSERT INTO `properties_description` VALUES(3, 2, 2, 'Farbe', '');
INSERT INTO `properties_description` VALUES(4, 2, 1, 'Color', '');