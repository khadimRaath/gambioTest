DROP TABLE IF EXISTS `withdrawals`;
CREATE TABLE `withdrawals` (
  `withdrawal_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `customer_gender` varchar(16) NOT NULL DEFAULT '',
  `customer_firstname` varchar(255) NOT NULL DEFAULT '',
  `customer_lastname` varchar(255) NOT NULL DEFAULT '',
  `customer_street_address` varchar(255) NOT NULL DEFAULT '',
  `customer_postcode` varchar(255) NOT NULL DEFAULT '',
  `customer_city` varchar(255) NOT NULL DEFAULT '',
  `customer_country` varchar(255) NOT NULL DEFAULT '',
  `customer_email` varchar(255) NOT NULL DEFAULT '',
  `order_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `delivery_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `withdrawal_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `withdrawal_content` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `created_by_admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`withdrawal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;