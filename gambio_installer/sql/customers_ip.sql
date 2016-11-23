DROP TABLE IF EXISTS `customers_ip`;
CREATE TABLE `customers_ip` (
  `customers_ip_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `customers_ip` varchar(15) NOT NULL DEFAULT '',
  `customers_ip_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `customers_host` varchar(255) NOT NULL DEFAULT '',
  `customers_advertiser` varchar(30) DEFAULT NULL,
  `customers_referer_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`customers_ip_id`),
  KEY `customers_id` (`customers_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;