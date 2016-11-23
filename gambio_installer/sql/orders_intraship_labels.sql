DROP TABLE IF EXISTS `orders_intraship_labels`;
CREATE TABLE `orders_intraship_labels` (
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `label_url` text NOT NULL,
  PRIMARY KEY (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;