DROP TABLE IF EXISTS `products_properties_combis`;
CREATE TABLE `products_properties_combis` (
  `products_properties_combis_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `products_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `combi_model` varchar(64) NOT NULL DEFAULT '',
  `combi_ean` varchar(20) NOT NULL DEFAULT '',
  `combi_quantity` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `combi_shipping_status_id` int(11) NOT NULL DEFAULT '0',
  `combi_weight` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `combi_price_type` enum('calc','fix') NOT NULL,
  `combi_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `combi_image` varchar(255) NOT NULL DEFAULT '',
  `products_vpe_id` int(11) NOT NULL DEFAULT '0',
  `vpe_value` decimal(15,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`products_properties_combis_id`),
  KEY `products_properties_combis_id` (`products_properties_combis_id`,`products_id`,`sort_order`),
  KEY `products_id` (`products_id`,`sort_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `products_properties_combis` VALUES(1, 1, 1, 's-gold', '123456789', 100.0000, 2, 2.0000, 'calc', 0.0000, '', 0, 0.0000);
INSERT INTO `products_properties_combis` VALUES(2, 1, 2, 's-red', '123456789', 100.0000, 2, 2.0000, 'calc', 0.0000, '', 0, 0.0000);
INSERT INTO `products_properties_combis` VALUES(3, 1, 3, 's-black', '123456789', 100.0000, 2, 2.0000, 'calc', 1.6807, '', 0, 0.0000);
INSERT INTO `products_properties_combis` VALUES(4, 1, 4, 'm-gold', '123456789', 100.0000, 2, 2.0000, 'calc', 0.0000, '', 0, 0.0000);
INSERT INTO `products_properties_combis` VALUES(5, 1, 5, 'm-red', '123456789', 100.0000, 2, 2.0000, 'calc', 0.0000, '', 0, 0.0000);
INSERT INTO `products_properties_combis` VALUES(6, 1, 6, 'm-black', '123456789', 100.0000, 2, 2.0000, 'calc', 1.6807, '', 0, 0.0000);
INSERT INTO `products_properties_combis` VALUES(7, 1, 7, 'l-gold', '123456789', 100.0000, 2, 2.0000, 'calc', 4.2017, '', 0, 0.0000);
INSERT INTO `products_properties_combis` VALUES(8, 1, 8, 'l-red', '123456789', 100.0000, 2, 2.0000, 'calc', 4.2017, '', 0, 0.0000);
INSERT INTO `products_properties_combis` VALUES(9, 1, 9, 'l-black', '123456789', 100.0000, 2, 2.0000, 'calc', 5.8824, '', 0, 0.0000);