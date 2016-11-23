DROP TABLE IF EXISTS `gm_search_history`;
CREATE TABLE `gm_search_history` (
  `gm_search_history_id` int(10) NOT NULL AUTO_INCREMENT,
  `gm_search_ip` varchar(15) NOT NULL DEFAULT '',
  `gm_search_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`gm_search_history_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;