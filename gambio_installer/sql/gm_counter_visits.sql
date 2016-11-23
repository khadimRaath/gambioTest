DROP TABLE IF EXISTS `gm_counter_visits`;
CREATE TABLE `gm_counter_visits` (
  `gm_counter_id` int(10) NOT NULL AUTO_INCREMENT,
  `gm_counter_visits_total` int(10) NOT NULL DEFAULT '0',
  `gm_counter_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`gm_counter_id`),
  KEY `gm_counter_date` (`gm_counter_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `gm_counter_visits` (`gm_counter_id`, `gm_counter_visits_total`, `gm_counter_date`) VALUES(1, 1, '2008-08-25 00:00:00');