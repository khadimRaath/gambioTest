DROP TABLE IF EXISTS `products_properties_admin_select`;
CREATE TABLE `products_properties_admin_select` (
  `products_properties_admin_select_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) NOT NULL DEFAULT '0',
  `properties_id` int(11) NOT NULL DEFAULT '0',
  `properties_values_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`products_properties_admin_select_id`),
  UNIQUE KEY `unique_value_assignment` (`products_id`,`properties_values_id`),
  KEY `products_id` (`products_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `products_properties_admin_select` VALUES(1, 1, 1, 1);
INSERT INTO `products_properties_admin_select` VALUES(2, 1, 1, 2);
INSERT INTO `products_properties_admin_select` VALUES(3, 1, 1, 3);
INSERT INTO `products_properties_admin_select` VALUES(4, 1, 2, 4);
INSERT INTO `products_properties_admin_select` VALUES(5, 1, 2, 5);
INSERT INTO `products_properties_admin_select` VALUES(6, 1, 2, 6);