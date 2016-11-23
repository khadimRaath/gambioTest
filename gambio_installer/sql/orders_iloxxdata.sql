DROP TABLE IF EXISTS `orders_iloxxdata`;
CREATE TABLE `orders_iloxxdata` (
  `orders_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parcelnumber` varchar(255) DEFAULT NULL,
  `service` varchar(255) NOT NULL DEFAULT '',
  `weight` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `shipdate` date DEFAULT NULL,
  PRIMARY KEY (`orders_id`),
  KEY `parcelnumber` (`parcelnumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;