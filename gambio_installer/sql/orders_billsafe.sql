DROP TABLE IF EXISTS `orders_billsafe`;
CREATE TABLE `orders_billsafe` (
  `orders_id` int(10) unsigned NOT NULL DEFAULT '0',
  `transaction_id` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`orders_id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;