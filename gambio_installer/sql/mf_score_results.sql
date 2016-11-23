DROP TABLE IF EXISTS `mf_score_results`;
CREATE TABLE `mf_score_results` (
  `scoreId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `customerId` int(10) unsigned NOT NULL DEFAULT '0',
  `score` decimal(2,1) NOT NULL DEFAULT '0.0',
  `explanation` text NOT NULL,
  `lastCheck` int(10) unsigned NOT NULL DEFAULT '0',
  `negativeEntryList` text NOT NULL,
  PRIMARY KEY (`scoreId`),
  UNIQUE KEY `customerId` (`customerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;