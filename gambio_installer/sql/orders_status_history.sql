DROP TABLE IF EXISTS `orders_status_history`;
CREATE TABLE `orders_status_history` (
  `orders_status_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `orders_status_id` int(5) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `customer_notified` int(1) DEFAULT '0',
  `comments` text,
  PRIMARY KEY (`orders_status_history_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;