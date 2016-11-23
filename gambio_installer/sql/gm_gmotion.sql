DROP TABLE IF EXISTS `gm_gmotion`;
CREATE TABLE `gm_gmotion` (
  `gm_gmotion_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `products_id` int(10) unsigned NOT NULL DEFAULT '0',
  `image_nr` smallint(6) NOT NULL DEFAULT '0',
  `position_from` varchar(32) NOT NULL DEFAULT '',
  `position_to` varchar(32) NOT NULL DEFAULT '',
  `zoom_from` decimal(3,1) NOT NULL DEFAULT '0.0',
  `zoom_to` decimal(3,1) NOT NULL DEFAULT '0.0',
  `duration` int(10) unsigned NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gm_gmotion_id`),
  UNIQUE KEY `products_id` (`products_id`,`image_nr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `gm_gmotion` (`gm_gmotion_id`, `products_id`, `image_nr`, `position_from`, `position_to`, `zoom_from`, `zoom_to`, `duration`, `sort_order`) VALUES(1, 1, 0, '4% 98%', '93% 11%', 1.0, 1.0, 10, 1);
INSERT INTO `gm_gmotion` (`gm_gmotion_id`, `products_id`, `image_nr`, `position_from`, `position_to`, `zoom_from`, `zoom_to`, `duration`, `sort_order`) VALUES(2, 1, 1, '21% 30%', '63% 67%', 1.0, 1.0, 10, 2);
INSERT INTO `gm_gmotion` (`gm_gmotion_id`, `products_id`, `image_nr`, `position_from`, `position_to`, `zoom_from`, `zoom_to`, `duration`, `sort_order`) VALUES(3, 1, 2, '73% 12%', '34% 92%', 1.0, 1.0, 10, 3);
INSERT INTO `gm_gmotion` (`gm_gmotion_id`, `products_id`, `image_nr`, `position_from`, `position_to`, `zoom_from`, `zoom_to`, `duration`, `sort_order`) VALUES(4, 1, 3, '6% 73%', '92% 34%', 1.0, 1.0, 10, 4);