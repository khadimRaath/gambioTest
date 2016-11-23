DROP TABLE IF EXISTS `customers_logs_history`;
CREATE TABLE `customers_logs_history` (
  `customers_logs_history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customers_id` int(10) unsigned NOT NULL DEFAULT '0',
  `confirmation_date` varchar(32) NOT NULL DEFAULT '',
  `logfile` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`customers_logs_history_id`),
  UNIQUE KEY `customers_id` (`customers_id`,`logfile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;