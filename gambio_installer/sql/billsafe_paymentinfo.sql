DROP TABLE IF EXISTS `billsafe_paymentinfo`;
CREATE TABLE `billsafe_paymentinfo` (
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `received` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `transaction_id` varchar(255) NOT NULL DEFAULT '',
  `recipient` varchar(100) NOT NULL DEFAULT '',
  `bankCode` varchar(8) NOT NULL DEFAULT '',
  `accountNumber` varchar(10) NOT NULL DEFAULT '',
  `bankName` varchar(100) NOT NULL DEFAULT '',
  `bic` varchar(11) NOT NULL DEFAULT '',
  `iban` varchar(34) NOT NULL DEFAULT '',
  `reference` varchar(50) NOT NULL DEFAULT '',
  `amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `currencyCode` varchar(3) NOT NULL DEFAULT '',
  `paymentPeriod` int(11) NOT NULL DEFAULT '0',
  `note` varchar(200) NOT NULL DEFAULT '',
  `legalNote` text NOT NULL,
  PRIMARY KEY (`orders_id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;