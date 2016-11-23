DROP TABLE IF EXISTS `orders_total`;
CREATE TABLE `orders_total` (
  `orders_total_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `text` varchar(255) NOT NULL DEFAULT '',
  `value` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `class` varchar(32) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`orders_total_id`),
  KEY `idx_orders_total_orders_id` (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;