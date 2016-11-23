DROP TABLE IF EXISTS `orders_products_properties`;
CREATE TABLE `orders_products_properties` (
  `orders_products_properties_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_products_id` int(10) unsigned DEFAULT NULL,
  `products_properties_combis_id` int(10) unsigned DEFAULT NULL,
  `properties_name` varchar(255) NOT NULL DEFAULT '',
  `values_name` varchar(255) NOT NULL DEFAULT '',
  `properties_price_type` varchar(8) NOT NULL DEFAULT '',
  `properties_price` decimal(16,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`orders_products_properties_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;