DROP TABLE IF EXISTS `orders_payment_instruction`;
CREATE TABLE `orders_payment_instruction` (
  `orders_payment_instruction_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `reference` varchar(255) NOT NULL DEFAULT '',
  `bank_name` varchar(255) NOT NULL DEFAULT '',
  `account_holder` varchar(255) NOT NULL DEFAULT '',
  `iban` varchar(34) NOT NULL DEFAULT '',
  `bic` varchar(11) NOT NULL DEFAULT '',
  `value` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `currency` varchar(3) NOT NULL DEFAULT '',
  `due_date` date NOT NULL DEFAULT '1000-01-01',
  PRIMARY KEY (`orders_payment_instruction_id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;