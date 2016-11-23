DROP TABLE IF EXISTS `gm_counter_page_history`;
CREATE TABLE `gm_counter_page_history` (
  `gm_counter_page_history_id` int(10) NOT NULL AUTO_INCREMENT,
  `gm_counter_page_history_name` varchar(255) DEFAULT NULL,
  `gm_counter_page_history_type` varchar(255) DEFAULT NULL,
  `gm_counter_page_history_hits` int(10) DEFAULT NULL,
  `gm_counter_page_history_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`gm_counter_page_history_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;