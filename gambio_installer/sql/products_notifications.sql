DROP TABLE IF EXISTS `products_notifications`;
CREATE TABLE `products_notifications` (
  `products_id` int(11) NOT NULL DEFAULT '0',
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`products_id`,`customers_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;