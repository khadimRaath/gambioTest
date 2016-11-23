DROP TABLE IF EXISTS `brickfox_orders_lines`;
CREATE TABLE `brickfox_orders_lines` (
  `brickfox_orders_lines_id` int(11) NOT NULL DEFAULT '0',
  `orders_products_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`brickfox_orders_lines_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;