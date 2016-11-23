DROP TABLE IF EXISTS `slider_image_description`;
CREATE TABLE `slider_image_description` (
  `slider_image_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `image_title` varchar(255) DEFAULT NULL,
  `image_alt_text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`language_id`,`slider_image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;