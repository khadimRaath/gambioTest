DROP TABLE IF EXISTS `slider_image`;
CREATE TABLE `slider_image` (
  `slider_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `slider_set_id` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(11) DEFAULT NULL,
  `image_file` varchar(45) DEFAULT NULL,
  `image_preview_file` varchar(45) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `link_window_target` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`slider_image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;