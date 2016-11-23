DROP TABLE IF EXISTS `amzadvpay_orders`;
CREATE TABLE `amzadvpay_orders` (
  `amzadvpay_orders_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `order_reference_id` varchar(64) NOT NULL DEFAULT '',
  `state` varchar(20) NOT NULL DEFAULT '',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_details` mediumtext NOT NULL,
  PRIMARY KEY (`amzadvpay_orders_id`),
  UNIQUE KEY `orders_id_2` (`orders_id`),
  KEY `orders_id` (`orders_id`,`order_reference_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;