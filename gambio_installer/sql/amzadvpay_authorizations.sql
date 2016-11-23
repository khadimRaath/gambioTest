DROP TABLE IF EXISTS `amzadvpay_authorizations`;
CREATE TABLE `amzadvpay_authorizations` (
  `amzadvpay_authorizations_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `order_reference_id` varchar(64) NOT NULL DEFAULT '',
  `authorization_reference_id` varchar(64) NOT NULL DEFAULT '',
  `state` varchar(20) NOT NULL DEFAULT '',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_details` mediumtext NOT NULL,
  PRIMARY KEY (`amzadvpay_authorizations_id`),
  UNIQUE KEY `authorization_reference_id` (`authorization_reference_id`),
  KEY `orders_id` (`orders_id`),
  KEY `order_reference_id` (`order_reference_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;