DROP TABLE IF EXISTS `sepa`;
CREATE TABLE `sepa` (
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `sepa_owner` varchar(64) DEFAULT NULL,
  `sepa_iban` varchar(35) DEFAULT NULL,
  `sepa_bic` varchar(15) DEFAULT NULL,
  `sepa_bankname` varchar(255) DEFAULT NULL,
  `sepa_status` int(11) DEFAULT NULL,
  `sepa_prz` char(2) DEFAULT NULL,
  `sepa_fax` char(2) DEFAULT NULL,
  PRIMARY KEY (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;