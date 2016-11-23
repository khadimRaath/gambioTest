DROP TABLE IF EXISTS `products_hermesoptions`;
CREATE TABLE `products_hermesoptions` (
  `products_id` int(11) NOT NULL DEFAULT '0',
  `min_pclass` enum('XS','S','M','L','XL','XXL') NOT NULL,
  PRIMARY KEY (`products_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;