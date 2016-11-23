DROP TABLE IF EXISTS `banners_history`;
CREATE TABLE `banners_history` (
  `banners_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `banners_id` int(11) NOT NULL DEFAULT '0',
  `banners_shown` int(5) NOT NULL DEFAULT '0',
  `banners_clicked` int(5) NOT NULL DEFAULT '0',
  `banners_history_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`banners_history_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;