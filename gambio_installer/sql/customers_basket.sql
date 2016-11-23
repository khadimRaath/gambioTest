DROP TABLE IF EXISTS `customers_basket`;
CREATE TABLE `customers_basket` (
  `customers_basket_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `products_id` tinytext NOT NULL,
  `customers_basket_quantity` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `final_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `customers_basket_date_added` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`customers_basket_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;