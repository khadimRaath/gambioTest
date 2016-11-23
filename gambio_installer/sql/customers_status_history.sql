DROP TABLE IF EXISTS `customers_status_history`;
CREATE TABLE `customers_status_history` (
  `customers_status_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `new_value` int(5) NOT NULL DEFAULT '0',
  `old_value` int(5) DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `customer_notified` int(1) DEFAULT '0',
  PRIMARY KEY (`customers_status_history_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;