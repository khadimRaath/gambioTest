DROP TABLE IF EXISTS `mf_errors`;
CREATE TABLE `mf_errors` (
  `errorId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `errorText` text NOT NULL,
  `customerId` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`errorId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;