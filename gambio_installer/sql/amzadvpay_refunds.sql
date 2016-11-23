DROP TABLE IF EXISTS `amzadvpay_refunds`;
CREATE TABLE `amzadvpay_refunds` (
  `amzadvpay_refunds_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `order_reference_id` varchar(64) NOT NULL DEFAULT '',
  `authorization_reference_id` varchar(64) NOT NULL DEFAULT '',
  `capture_reference_id` varchar(64) NOT NULL DEFAULT '',
  `refund_reference_id` varchar(64) NOT NULL DEFAULT '',
  `state` varchar(20) NOT NULL DEFAULT '',
  `last_update` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `last_details` mediumtext,
  PRIMARY KEY (`amzadvpay_refunds_id`),
  UNIQUE KEY `refund_reference_id` (`refund_reference_id`),
  KEY `orders_id` (`orders_id`),
  KEY `authorization_reference_id` (`authorization_reference_id`),
  KEY `capture_reference_id` (`capture_reference_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;