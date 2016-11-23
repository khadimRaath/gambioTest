DROP TABLE IF EXISTS `counter_history`;
CREATE TABLE `counter_history` (
  `month` char(8) DEFAULT NULL,
  `counter` int(12) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;