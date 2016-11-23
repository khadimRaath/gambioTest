DROP TABLE IF EXISTS `newsletters_history`;
CREATE TABLE `newsletters_history` (
  `news_hist_id` int(11) NOT NULL DEFAULT '0',
  `news_hist_cs` int(11) NOT NULL DEFAULT '0',
  `news_hist_cs_date_sent` date DEFAULT NULL,
  PRIMARY KEY (`news_hist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
