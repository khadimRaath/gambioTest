DROP TABLE IF EXISTS `orders_shopgate_order`;
CREATE TABLE `orders_shopgate_order` (
  `shopgate_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `shopgate_order_number` bigint(20) NOT NULL DEFAULT '0',
  `last_response` text NOT NULL,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`shopgate_order_id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;