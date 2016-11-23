DROP TABLE IF EXISTS `user_configuration`;
CREATE TABLE `user_configuration` (
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `configuration_key` varchar(255) NOT NULL DEFAULT '',
  `configuration_value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`customer_id`,`configuration_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;