DROP TABLE IF EXISTS `orders_parcel_tracking_codes`;
CREATE TABLE `orders_parcel_tracking_codes` (
  `orders_parcel_tracking_code_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `tracking_code` varchar(255) NOT NULL DEFAULT '',
  `parcel_service_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parcel_service_name` varchar(45) NOT NULL DEFAULT '',
  `language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `url` varchar(1023) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`orders_parcel_tracking_code_id`),
  KEY `order_id` (`order_id`),
  KEY `tracking_code` (`tracking_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;