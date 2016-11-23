DROP TABLE IF EXISTS `orders_klarna_returnamount`;
CREATE TABLE `orders_klarna_returnamount` (
  `ok_returnamount_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `vat` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `description` varchar(255) NOT NULL DEFAULT '',
  `sent_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ok_returnamount_id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;