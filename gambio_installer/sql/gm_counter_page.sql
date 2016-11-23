DROP TABLE IF EXISTS `gm_counter_page`;
CREATE TABLE `gm_counter_page` (
  `gm_counter_page_id` int(10) NOT NULL AUTO_INCREMENT,
  `gm_counter_page_name` varchar(255) DEFAULT NULL,
  `gm_counter_page_type` varchar(255) DEFAULT NULL,
  `gm_counter_page_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`gm_counter_page_id`),
  KEY `gm_counter_page_date` (`gm_counter_page_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;