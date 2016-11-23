DROP TABLE IF EXISTS `orders_products_download`;
CREATE TABLE `orders_products_download` (
  `orders_products_download_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `orders_products_id` int(11) NOT NULL DEFAULT '0',
  `orders_products_filename` varchar(255) NOT NULL DEFAULT '',
  `download_maxdays` int(2) NOT NULL DEFAULT '0',
  `download_count` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`orders_products_download_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;