DROP TABLE IF EXISTS `campaigns_ip`;
CREATE TABLE `campaigns_ip` (
  `user_ip` varchar(15) NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `campaign` varchar(32) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;