DROP TABLE IF EXISTS `paypal_paylink`;
CREATE TABLE `paypal_paylink` (
  `orders_id` int(10) unsigned NOT NULL DEFAULT '0',
  `paycode` varchar(32) NOT NULL DEFAULT '',
  `amount` decimal(15,4) DEFAULT NULL,
  PRIMARY KEY (`orders_id`),
  KEY `paycode` (`paycode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;