DROP TABLE IF EXISTS `products_quantity_unit`;
CREATE TABLE `products_quantity_unit` (
  `products_id` int(11) NOT NULL DEFAULT '0',
  `quantity_unit_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`products_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;