DROP TABLE IF EXISTS `payment_ipayment`;
CREATE TABLE `payment_ipayment` (
  `ip_BOOKNR` varchar(255) NOT NULL DEFAULT '',
  `ip_INFO` text NOT NULL,
  `ip_ORDERID` int(11) NOT NULL DEFAULT '0',
  `ip_IS_CAPTURED` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ip_BOOKNR`),
  KEY `ip_ORDERID` (`ip_ORDERID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;