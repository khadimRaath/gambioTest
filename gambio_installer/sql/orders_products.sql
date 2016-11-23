DROP TABLE IF EXISTS `orders_products`;
CREATE TABLE `orders_products` (
  `orders_products_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `products_id` int(11) NOT NULL DEFAULT '0',
  `products_model` varchar(64) DEFAULT NULL,
  `products_name` varchar(255) NOT NULL DEFAULT '',
  `products_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `products_discount_made` decimal(4,2) DEFAULT NULL,
  `products_shipping_time` varchar(255) DEFAULT NULL,
  `final_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `products_tax` decimal(7,4) NOT NULL DEFAULT '0.0000',
  `products_quantity` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `allow_tax` int(1) NOT NULL DEFAULT '0',
  `product_type` int(11) NOT NULL DEFAULT '1',
  `properties_combi_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `properties_combi_model` varchar(64) NOT NULL DEFAULT '',
  `checkout_information` text,
  PRIMARY KEY (`orders_products_id`),
  KEY `orders_id` (`orders_id`),
  KEY `products_id` (`products_id`,`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;