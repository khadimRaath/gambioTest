DROP TABLE IF EXISTS `brickfox_orders`;
CREATE TABLE `brickfox_orders` (
  `brickfox_orders_id` int(11) NOT NULL DEFAULT '0',
  `extern_orders_id` varchar(255) NOT NULL DEFAULT '',
  `intern_orders_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`brickfox_orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;