DROP TABLE IF EXISTS `ekomi_order_information`;
CREATE TABLE `ekomi_order_information` (
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL DEFAULT '',
  `hash` varchar(255) NOT NULL DEFAULT '',
  `date_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;