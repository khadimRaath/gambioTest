DROP TABLE IF EXISTS `ts_protection`;
CREATE TABLE `ts_protection` (
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `application_number` int(11) NOT NULL DEFAULT '0',
  `tsid` varchar(33) NOT NULL DEFAULT '',
  `result` int(11) DEFAULT NULL,
  PRIMARY KEY (`orders_id`),
  KEY `application_number` (`application_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;