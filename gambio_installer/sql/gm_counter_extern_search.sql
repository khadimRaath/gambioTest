DROP TABLE IF EXISTS `gm_counter_extern_search`;
CREATE TABLE `gm_counter_extern_search` (
  `gm_counter_extern_search_id` int(10) NOT NULL AUTO_INCREMENT,
  `gm_counter_extern_search_name` varchar(255) DEFAULT NULL,
  `gm_counter_extern_search_engine` varchar(255) DEFAULT NULL,
  `gm_counter_extern_search_hits` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gm_counter_extern_search_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;