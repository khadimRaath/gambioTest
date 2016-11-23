DROP TABLE IF EXISTS `orders_paypal_payments`;
CREATE TABLE `orders_paypal_payments` (
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `payment_id` varchar(48) NOT NULL DEFAULT '',
  `mode` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`orders_id`,`payment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;