DROP TABLE IF EXISTS `payone_transactions`;
CREATE TABLE `payone_transactions` (
  `payone_transactions_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT '',
  `txid` varchar(100) NOT NULL DEFAULT '',
  `userid` varchar(100) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`payone_transactions_id`),
  KEY `orders_id` (`orders_id`,`txid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;