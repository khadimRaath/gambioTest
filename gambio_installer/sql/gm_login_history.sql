DROP TABLE IF EXISTS `gm_login_history`;
CREATE TABLE `gm_login_history` (
  `gm_login_history_id` int(10) NOT NULL AUTO_INCREMENT,
  `gm_login_ip` varchar(15) NOT NULL DEFAULT '',
  `gm_login_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`gm_login_history_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;