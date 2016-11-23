DROP TABLE IF EXISTS `orders_products_quantity_units`;
CREATE TABLE `orders_products_quantity_units` (
  `orders_products_id` int(11) NOT NULL DEFAULT '0',
  `quantity_unit_id` int(11) NOT NULL DEFAULT '0',
  `unit_name` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`orders_products_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;