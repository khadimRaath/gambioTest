DROP TABLE IF EXISTS `billsafe_products_shipped`;
CREATE TABLE `billsafe_products_shipped` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(10) unsigned NOT NULL DEFAULT '0',
  `transaction_id` varchar(255) NOT NULL DEFAULT '',
  `shipping_date` date NOT NULL DEFAULT '0000-00-00',
  `parcel_service` varchar(255) NOT NULL DEFAULT '',
  `parcel_company` varchar(255) NOT NULL DEFAULT '',
  `parcel_trackingid` varchar(255) NOT NULL DEFAULT '',
  `article_number` varchar(255) NOT NULL DEFAULT '',
  `article_name` varchar(255) NOT NULL DEFAULT '',
  `article_type` varchar(20) NOT NULL DEFAULT '',
  `article_quantity` int(5) NOT NULL DEFAULT '0',
  `article_grossprice` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `article_tax` decimal(4,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;