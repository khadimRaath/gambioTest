DROP TABLE IF EXISTS `products_slider_set`;
CREATE TABLE `products_slider_set` (
  `products_slider_set_id` int(11) NOT NULL AUTO_INCREMENT,
  `slider_set_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`products_slider_set_id`),
  KEY `fk_products_slider_set_slider_set1` (`slider_set_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;