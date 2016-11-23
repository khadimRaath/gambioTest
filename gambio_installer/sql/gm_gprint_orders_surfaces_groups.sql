DROP TABLE IF EXISTS `gm_gprint_orders_surfaces_groups`;
CREATE TABLE `gm_gprint_orders_surfaces_groups` (
  `gm_gprint_orders_surfaces_groups_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `orders_products_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`gm_gprint_orders_surfaces_groups_id`),
  UNIQUE KEY `orders_products_id` (`orders_products_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;