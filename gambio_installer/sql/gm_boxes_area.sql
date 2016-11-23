DROP TABLE IF EXISTS `gm_boxes_area`;
CREATE TABLE `gm_boxes_area` (
  `boxes_area_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `boxes_id` int(11) NOT NULL DEFAULT '0',
  `area` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`boxes_area_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;