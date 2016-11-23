DROP TABLE IF EXISTS `customers_wishlist_attributes`;
CREATE TABLE `customers_wishlist_attributes` (
  `customers_basket_attributes_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `products_id` tinytext NOT NULL,
  `products_options_id` int(11) NOT NULL DEFAULT '0',
  `products_options_value_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`customers_basket_attributes_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;