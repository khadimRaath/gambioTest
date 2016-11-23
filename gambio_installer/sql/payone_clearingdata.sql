DROP TABLE IF EXISTS `payone_clearingdata`;
CREATE TABLE `payone_clearingdata` (
  `p1_clearingdata_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bankaccountholder` varchar(255) NOT NULL DEFAULT '',
  `bankcountry` varchar(2) NOT NULL DEFAULT '',
  `bankaccount` varchar(32) NOT NULL DEFAULT '',
  `bankcode` varchar(32) NOT NULL DEFAULT '',
  `bankiban` varchar(32) NOT NULL DEFAULT '',
  `bankbic` varchar(32) NOT NULL DEFAULT '',
  `bankcity` varchar(64) NOT NULL DEFAULT '',
  `bankname` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`p1_clearingdata_id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;