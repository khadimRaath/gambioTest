DROP TABLE IF EXISTS `ts_items`;
CREATE TABLE `ts_items` (
  `ts_items_id` int(11) NOT NULL AUTO_INCREMENT,
  `ts_id` varchar(33) NOT NULL DEFAULT '',
  `retrievaldate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `id` int(11) NOT NULL DEFAULT '0',
  `currency` varchar(3) NOT NULL DEFAULT '',
  `grossfee` decimal(15,5) NOT NULL DEFAULT '0.00000',
  `netfee` decimal(15,5) NOT NULL DEFAULT '0.00000',
  `protectedamount` decimal(15,5) NOT NULL DEFAULT '0.00000',
  `protectionduration` int(11) NOT NULL DEFAULT '0',
  `tsproductid` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`ts_items_id`),
  KEY `ts_id` (`ts_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;