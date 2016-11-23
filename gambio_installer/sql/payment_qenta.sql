DROP TABLE IF EXISTS `payment_qenta`;
CREATE TABLE `payment_qenta` (
  `q_TRID` varchar(255) NOT NULL DEFAULT '',
  `q_DATE` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `q_QTID` bigint(18) unsigned NOT NULL DEFAULT '0',
  `q_ORDERDESC` varchar(255) NOT NULL DEFAULT '',
  `q_STATUS` tinyint(1) NOT NULL DEFAULT '0',
  `q_ORDERID` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`q_TRID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;