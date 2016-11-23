DROP TABLE IF EXISTS `coupon_gv_queue`;
CREATE TABLE `coupon_gv_queue` (
  `unique_id` int(5) NOT NULL AUTO_INCREMENT,
  `customer_id` int(5) NOT NULL DEFAULT '0',
  `order_id` int(5) NOT NULL DEFAULT '0',
  `amount` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `date_created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `ipaddr` varchar(32) NOT NULL DEFAULT '',
  `release_flag` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`unique_id`),
  KEY `uid` (`unique_id`,`customer_id`,`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;