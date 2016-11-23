DROP TABLE IF EXISTS `gm_gprint_surfaces_groups_to_products`;
CREATE TABLE `gm_gprint_surfaces_groups_to_products` (
  `gm_gprint_surfaces_groups_id` int(10) unsigned NOT NULL DEFAULT '0',
  `products_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`gm_gprint_surfaces_groups_id`,`products_id`),
  UNIQUE KEY `products_id` (`products_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;