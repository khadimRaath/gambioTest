DROP TABLE IF EXISTS `shipping_and_payment_matrix`;
CREATE TABLE `shipping_and_payment_matrix` (
  `country_code` varchar(255) NOT NULL DEFAULT '',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `shipping_info` text NOT NULL,
  `payment_info` text NOT NULL,
  `shipping_time` text NOT NULL,
  PRIMARY KEY (`country_code`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;