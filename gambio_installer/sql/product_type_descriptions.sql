DROP TABLE IF EXISTS `product_type_descriptions`;
CREATE TABLE `product_type_descriptions` (
  `product_type_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`product_type_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `product_type_descriptions` (`product_type_id`, `language_id`, `name`) VALUES(1, 1, 'Default');
INSERT INTO `product_type_descriptions` (`product_type_id`, `language_id`, `name`) VALUES(1, 2, 'Standard');
INSERT INTO `product_type_descriptions` (`product_type_id`, `language_id`, `name`) VALUES(2, 1, 'Download');
INSERT INTO `product_type_descriptions` (`product_type_id`, `language_id`, `name`) VALUES(2, 2, 'Download');
INSERT INTO `product_type_descriptions` (`product_type_id`, `language_id`, `name`) VALUES(3, 1, 'Service');
INSERT INTO `product_type_descriptions` (`product_type_id`, `language_id`, `name`) VALUES(3, 2, 'Dienstleistung');