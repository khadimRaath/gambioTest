DROP TABLE IF EXISTS `ts_certs`;
CREATE TABLE `ts_certs` (
  `tsid` varchar(33) NOT NULL DEFAULT '',
  `language` varchar(2) NOT NULL DEFAULT '',
  `state` enum('PRODUCTION','CANCELLED','DISABLED','INTEGRATION','TEST','INVALID_TS_ID') NOT NULL,
  `type` enum('CLASSIC','EXCELLENCE','MIGRATION') NOT NULL,
  `url` text NOT NULL,
  `user` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `login_ok` tinyint(1) NOT NULL DEFAULT '0',
  `rating_ok` tinyint(1) NOT NULL DEFAULT '0',
  `date_checked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`tsid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;