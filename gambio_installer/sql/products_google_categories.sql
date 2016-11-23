DROP TABLE IF EXISTS `products_google_categories`;
CREATE TABLE `products_google_categories` (
  `products_google_categories_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) DEFAULT NULL,
  `google_category` text,
  PRIMARY KEY (`products_google_categories_id`),
  KEY `products_id` (`products_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;