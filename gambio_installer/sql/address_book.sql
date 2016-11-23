DROP TABLE IF EXISTS `address_book`;
CREATE TABLE `address_book` (
  `address_book_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `entry_gender` char(1) NOT NULL DEFAULT '',
  `entry_company` varchar(255) DEFAULT NULL,
  `entry_firstname` varchar(32) NOT NULL DEFAULT '',
  `entry_lastname` varchar(32) NOT NULL DEFAULT '',
  `entry_street_address` varchar(64) NOT NULL DEFAULT '',
  `entry_house_number` varchar(64) NOT NULL DEFAULT '',
  `entry_additional_info` varchar(255) NOT NULL DEFAULT '',
  `entry_suburb` varchar(32) DEFAULT NULL,
  `entry_postcode` varchar(10) NOT NULL DEFAULT '',
  `entry_city` varchar(32) NOT NULL DEFAULT '',
  `entry_state` varchar(32) DEFAULT NULL,
  `entry_country_id` int(11) NOT NULL DEFAULT '0',
  `entry_zone_id` int(11) NOT NULL DEFAULT '0',
  `address_date_added` datetime DEFAULT '1000-01-01 00:00:00',
  `address_last_modified` datetime DEFAULT '1000-01-01 00:00:00',
  `address_class` varchar(32) NOT NULL DEFAULT '',
  `customer_b2b_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`address_book_id`),
  KEY `idx_address_book_customers_id` (`customers_id`),
  KEY `entry_country_id` (`entry_country_id`),
  KEY `entry_zone_id` (`entry_zone_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;