DROP TABLE IF EXISTS `banktransfer`;
CREATE TABLE `banktransfer` (
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `banktransfer_owner` varchar(64) DEFAULT NULL,
  `banktransfer_number` varchar(24) DEFAULT NULL,
  `banktransfer_bankname` varchar(255) DEFAULT NULL,
  `banktransfer_blz` varchar(8) DEFAULT NULL,
  `banktransfer_status` int(11) DEFAULT NULL,
  `banktransfer_prz` char(2) DEFAULT NULL,
  `banktransfer_fax` char(2) DEFAULT NULL,
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;