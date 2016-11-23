DROP TABLE IF EXISTS `gm_counter_info`;
CREATE TABLE `gm_counter_info` (
  `gm_counter_info_id` int(10) NOT NULL AUTO_INCREMENT,
  `gm_counter_info_type_id` int(10) NOT NULL DEFAULT '0',
  `gm_counter_info_hits` int(10) NOT NULL DEFAULT '0',
  `gm_counter_info_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`gm_counter_info_id`),
  KEY `gm_counter_info_type_id` (`gm_counter_info_type_id`,`gm_counter_info_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `gm_counter_info` (`gm_counter_info_id`, `gm_counter_info_type_id`, `gm_counter_info_hits`, `gm_counter_info_name`) VALUES(126, 1, 1, 'Mozilla Firefox 3.0.1');
INSERT INTO `gm_counter_info` (`gm_counter_info_id`, `gm_counter_info_type_id`, `gm_counter_info_hits`, `gm_counter_info_name`) VALUES(127, 2, 1, 'Windows XP');
INSERT INTO `gm_counter_info` (`gm_counter_info_id`, `gm_counter_info_type_id`, `gm_counter_info_hits`, `gm_counter_info_name`) VALUES(128, 5, 1, 'de');
INSERT INTO `gm_counter_info` (`gm_counter_info_id`, `gm_counter_info_type_id`, `gm_counter_info_hits`, `gm_counter_info_name`) VALUES(129, 3, 1, '1680x1050');
INSERT INTO `gm_counter_info` (`gm_counter_info_id`, `gm_counter_info_type_id`, `gm_counter_info_hits`, `gm_counter_info_name`) VALUES(130, 4, 1, '32');