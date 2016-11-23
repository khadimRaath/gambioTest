DROP TABLE IF EXISTS `products_properties_index`;
CREATE TABLE `products_properties_index` (
  `products_id` int(10) NOT NULL DEFAULT '0',
  `language_id` int(10) NOT NULL DEFAULT '0',
  `properties_id` int(10) NOT NULL DEFAULT '0',
  `products_properties_combis_id` int(10) NOT NULL DEFAULT '0',
  `properties_values_id` int(10) NOT NULL DEFAULT '0',
  `properties_name` varchar(255) DEFAULT NULL,
  `properties_admin_name` varchar(255) NOT NULL DEFAULT '',
  `properties_sort_order` int(10) NOT NULL DEFAULT '0',
  `values_name` varchar(255) DEFAULT NULL,
  `values_price` decimal(9,4) NOT NULL DEFAULT '0.0000',
  `value_sort_order` int(10) DEFAULT NULL,
  PRIMARY KEY (`products_properties_combis_id`,`language_id`,`properties_values_id`),
  KEY `products_id` (`products_id`,`language_id`,`properties_id`),
  KEY `products_id_2` (`products_id`,`language_id`,`properties_values_id`,`products_properties_combis_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

INSERT INTO `products_properties_index` VALUES(1, 1, 1, 1, 1, 'Size', '', 1, 'S', 0.0000, 1);
INSERT INTO `products_properties_index` VALUES(1, 1, 1, 2, 1, 'Size', '', 1, 'S', 0.0000, 1);
INSERT INTO `products_properties_index` VALUES(1, 1, 1, 3, 1, 'Size', '', 1, 'S', 0.0000, 1);
INSERT INTO `products_properties_index` VALUES(1, 1, 1, 4, 2, 'Size', '', 1, 'M', 0.0000, 2);
INSERT INTO `products_properties_index` VALUES(1, 1, 1, 5, 2, 'Size', '', 1, 'M', 0.0000, 2);
INSERT INTO `products_properties_index` VALUES(1, 1, 1, 6, 2, 'Size', '', 1, 'M', 0.0000, 2);
INSERT INTO `products_properties_index` VALUES(1, 1, 1, 7, 3, 'Size', '', 1, 'L', 5.0000, 3);
INSERT INTO `products_properties_index` VALUES(1, 1, 1, 8, 3, 'Size', '', 1, 'L', 5.0000, 3);
INSERT INTO `products_properties_index` VALUES(1, 1, 1, 9, 3, 'Size', '', 1, 'L', 5.0000, 3);
INSERT INTO `products_properties_index` VALUES(1, 1, 2, 1, 4, 'Color', '', 2, 'Gold', 0.0000, 1);
INSERT INTO `products_properties_index` VALUES(1, 1, 2, 4, 4, 'Color', '', 2, 'Gold', 0.0000, 1);
INSERT INTO `products_properties_index` VALUES(1, 1, 2, 7, 4, 'Color', '', 2, 'Gold', 0.0000, 1);
INSERT INTO `products_properties_index` VALUES(1, 1, 2, 2, 5, 'Color', '', 2, 'Red', 0.0000, 2);
INSERT INTO `products_properties_index` VALUES(1, 1, 2, 5, 5, 'Color', '', 2, 'Red', 0.0000, 2);
INSERT INTO `products_properties_index` VALUES(1, 1, 2, 8, 5, 'Color', '', 2, 'Red', 0.0000, 2);
INSERT INTO `products_properties_index` VALUES(1, 1, 2, 3, 6, 'Color', '', 2, 'Black', 2.0000, 3);
INSERT INTO `products_properties_index` VALUES(1, 1, 2, 6, 6, 'Color', '', 2, 'Black', 2.0000, 3);
INSERT INTO `products_properties_index` VALUES(1, 1, 2, 9, 6, 'Color', '', 2, 'Black', 2.0000, 3);
INSERT INTO `products_properties_index` VALUES(1, 2, 1, 1, 1, 'Größe', '', 1, 'S', 0.0000, 1);
INSERT INTO `products_properties_index` VALUES(1, 2, 1, 2, 1, 'Größe', '', 1, 'S', 0.0000, 1);
INSERT INTO `products_properties_index` VALUES(1, 2, 1, 3, 1, 'Größe', '', 1, 'S', 0.0000, 1);
INSERT INTO `products_properties_index` VALUES(1, 2, 1, 4, 2, 'Größe', '', 1, 'M', 0.0000, 2);
INSERT INTO `products_properties_index` VALUES(1, 2, 1, 5, 2, 'Größe', '', 1, 'M', 0.0000, 2);
INSERT INTO `products_properties_index` VALUES(1, 2, 1, 6, 2, 'Größe', '', 1, 'M', 0.0000, 2);
INSERT INTO `products_properties_index` VALUES(1, 2, 1, 7, 3, 'Größe', '', 1, 'L', 5.0000, 3);
INSERT INTO `products_properties_index` VALUES(1, 2, 1, 8, 3, 'Größe', '', 1, 'L', 5.0000, 3);
INSERT INTO `products_properties_index` VALUES(1, 2, 1, 9, 3, 'Größe', '', 1, 'L', 5.0000, 3);
INSERT INTO `products_properties_index` VALUES(1, 2, 2, 1, 4, 'Farbe', '', 2, 'Gold', 0.0000, 1);
INSERT INTO `products_properties_index` VALUES(1, 2, 2, 4, 4, 'Farbe', '', 2, 'Gold', 0.0000, 1);
INSERT INTO `products_properties_index` VALUES(1, 2, 2, 7, 4, 'Farbe', '', 2, 'Gold', 0.0000, 1);
INSERT INTO `products_properties_index` VALUES(1, 2, 2, 2, 5, 'Farbe', '', 2, 'Rot', 0.0000, 2);
INSERT INTO `products_properties_index` VALUES(1, 2, 2, 5, 5, 'Farbe', '', 2, 'Rot', 0.0000, 2);
INSERT INTO `products_properties_index` VALUES(1, 2, 2, 8, 5, 'Farbe', '', 2, 'Rot', 0.0000, 2);
INSERT INTO `products_properties_index` VALUES(1, 2, 2, 3, 6, 'Farbe', '', 2, 'Schwarz', 2.0000, 3);
INSERT INTO `products_properties_index` VALUES(1, 2, 2, 6, 6, 'Farbe', '', 2, 'Schwarz', 2.0000, 3);
INSERT INTO `products_properties_index` VALUES(1, 2, 2, 9, 6, 'Farbe', '', 2, 'Schwarz', 2.0000, 3);