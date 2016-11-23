DROP TABLE IF EXISTS `paypal_checkoutdetails`;
CREATE TABLE `paypal_checkoutdetails` (
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `retrievaltime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checkoutdetails` text NOT NULL,
  PRIMARY KEY (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;