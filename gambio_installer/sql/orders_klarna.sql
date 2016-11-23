DROP TABLE IF EXISTS `orders_klarna`;
CREATE TABLE `orders_klarna` (
  `orders_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rno` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT '',
  `risk_status` varchar(255) NOT NULL DEFAULT '',
  `inv_rno` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`orders_id`),
  KEY `rno` (`rno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;