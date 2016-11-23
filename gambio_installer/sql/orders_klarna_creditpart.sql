DROP TABLE IF EXISTS `orders_klarna_creditpart`;
CREATE TABLE `orders_klarna_creditpart` (
  `ok_creditpart_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(10) unsigned NOT NULL DEFAULT '0',
  `products_model` varchar(255) NOT NULL DEFAULT '',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `sent_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ok_creditpart_id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;