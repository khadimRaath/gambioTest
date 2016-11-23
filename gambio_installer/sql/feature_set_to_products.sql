DROP TABLE IF EXISTS `feature_set_to_products`;
CREATE TABLE `feature_set_to_products` (
  `feature_set_id` int(11) unsigned NOT NULL DEFAULT '0',
  `products_id` int(11) unsigned NOT NULL DEFAULT '0',
  KEY `products_id` (`products_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;