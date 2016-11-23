DROP TABLE IF EXISTS `slider_image_area`;
CREATE TABLE `slider_image_area` (
  `slider_image_area_id` int(11) NOT NULL AUTO_INCREMENT,
  `slider_image_id` int(11) NOT NULL DEFAULT '0',
  `shape` varchar(16) DEFAULT NULL,
  `coords` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `link_target` varchar(16) DEFAULT NULL,
  `flyover_content` text,
  PRIMARY KEY (`slider_image_area_id`),
  KEY `fk_slider_image_maparea_slider_image1` (`slider_image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;