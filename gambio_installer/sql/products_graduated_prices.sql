DROP TABLE IF EXISTS `products_graduated_prices`;
CREATE TABLE `products_graduated_prices` (
  `products_id` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `unitprice` decimal(15,4) NOT NULL DEFAULT '0.0000',
  KEY `products_id` (`products_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;