DROP TABLE IF EXISTS `orders_paypal`;
CREATE TABLE `orders_paypal` (
  `orders_id` int(10) unsigned NOT NULL DEFAULT '0',
  `correlation_id` varchar(255) DEFAULT NULL,
  `payer_id` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `paymentaction` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`orders_id`),
  KEY `correlation_id` (`correlation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;