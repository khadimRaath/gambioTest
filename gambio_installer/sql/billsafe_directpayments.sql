DROP TABLE IF EXISTS `billsafe_directpayments`;
CREATE TABLE `billsafe_directpayments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;