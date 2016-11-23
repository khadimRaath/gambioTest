DROP TABLE IF EXISTS `products_properties_combis_values`;
CREATE TABLE `products_properties_combis_values` (
  `products_properties_combis_values_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `products_properties_combis_id` int(10) unsigned NOT NULL DEFAULT '0',
  `properties_values_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`products_properties_combis_values_id`),
  UNIQUE KEY `unique_value_assignment` (`products_properties_combis_id`,`properties_values_id`),
  KEY `products_properties_combis_values_id` (`products_properties_combis_values_id`,`products_properties_combis_id`,`properties_values_id`),
  KEY `products_properties_combis_id` (`products_properties_combis_id`,`properties_values_id`),
  KEY `properties_values_id` (`properties_values_id`,`products_properties_combis_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `products_properties_combis_values` VALUES(1, 1, 1);
INSERT INTO `products_properties_combis_values` VALUES(2, 1, 4);
INSERT INTO `products_properties_combis_values` VALUES(3, 2, 1);
INSERT INTO `products_properties_combis_values` VALUES(4, 2, 5);
INSERT INTO `products_properties_combis_values` VALUES(5, 3, 1);
INSERT INTO `products_properties_combis_values` VALUES(6, 3, 6);
INSERT INTO `products_properties_combis_values` VALUES(7, 4, 2);
INSERT INTO `products_properties_combis_values` VALUES(8, 4, 4);
INSERT INTO `products_properties_combis_values` VALUES(9, 5, 2);
INSERT INTO `products_properties_combis_values` VALUES(10, 5, 5);
INSERT INTO `products_properties_combis_values` VALUES(11, 6, 2);
INSERT INTO `products_properties_combis_values` VALUES(12, 6, 6);
INSERT INTO `products_properties_combis_values` VALUES(13, 7, 3);
INSERT INTO `products_properties_combis_values` VALUES(14, 7, 4);
INSERT INTO `products_properties_combis_values` VALUES(15, 8, 3);
INSERT INTO `products_properties_combis_values` VALUES(16, 8, 5);
INSERT INTO `products_properties_combis_values` VALUES(17, 9, 3);
INSERT INTO `products_properties_combis_values` VALUES(18, 9, 6);