DROP TABLE IF EXISTS `gm_gmotion_products`;
CREATE TABLE `gm_gmotion_products` (
  `gm_gmotion_products_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `products_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`gm_gmotion_products_id`),
  UNIQUE KEY `products_id` (`products_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `gm_gmotion_products` (`gm_gmotion_products_id`, `products_id`) VALUES(1, 1);