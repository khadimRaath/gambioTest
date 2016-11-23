DROP TABLE IF EXISTS `content_slider_set`;
CREATE TABLE `content_slider_set` (
  `content_slider_set_id` int(11) NOT NULL AUTO_INCREMENT,
  `slider_set_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`content_slider_set_id`),
  KEY `fk_content_slider_set_slider_set1` (`slider_set_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;