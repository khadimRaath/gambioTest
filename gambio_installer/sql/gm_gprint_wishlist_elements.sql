DROP TABLE IF EXISTS `gm_gprint_wishlist_elements`;
CREATE TABLE `gm_gprint_wishlist_elements` (
  `gm_gprint_wishlist_elements_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gm_gprint_elements_id` int(10) unsigned DEFAULT NULL,
  `products_id` tinytext,
  `customers_id` int(10) unsigned DEFAULT NULL,
  `elements_value` text,
  `gm_gprint_uploads_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`gm_gprint_wishlist_elements_id`),
  UNIQUE KEY `gm_gprint_uploads_id` (`gm_gprint_uploads_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;