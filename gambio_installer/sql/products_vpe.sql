DROP TABLE IF EXISTS `products_vpe`;
CREATE TABLE `products_vpe` (
  `products_vpe_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `products_vpe_name` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`products_vpe_id`,`language_id`),
  KEY `language_id` (`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;