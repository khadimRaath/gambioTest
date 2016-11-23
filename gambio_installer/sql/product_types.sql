DROP TABLE IF EXISTS `product_types`;
CREATE TABLE `product_types` (
  `product_type_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`product_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `product_types` (`product_type_id`) VALUES(1);
INSERT INTO `product_types` (`product_type_id`) VALUES(2);
INSERT INTO `product_types` (`product_type_id`) VALUES(3);