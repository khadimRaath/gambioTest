DROP TABLE IF EXISTS `slider_set`;
CREATE TABLE `slider_set` (
  `slider_set_id` int(11) NOT NULL AUTO_INCREMENT,
  `set_name` varchar(255) DEFAULT NULL,
  `slider_speed` int(11) DEFAULT NULL,
  `width` int(10) unsigned NOT NULL DEFAULT '760',
  `height` int(10) unsigned NOT NULL DEFAULT '300',
  PRIMARY KEY (`slider_set_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;