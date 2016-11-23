DROP TABLE IF EXISTS `orders_tax_sum_items`;
CREATE TABLE `orders_tax_sum_items` (
  `orders_tax_sum_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_class` varchar(100) NOT NULL DEFAULT '',
  `tax_zone` varchar(100) NOT NULL DEFAULT '',
  `tax_rate` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `gross` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `net` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `tax` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `currency` varchar(100) NOT NULL DEFAULT '',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `insert_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `last_change_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tax_description` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`orders_tax_sum_item_id`),
  KEY `order_id` (`order_id`),
  KEY `insert_date` (`insert_date`,`tax_zone`,`tax_class`,`tax_rate`,`currency`),
  KEY `insert_date_2` (`insert_date`,`order_id`,`tax_zone`,`tax_class`,`tax_rate`,`currency`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;