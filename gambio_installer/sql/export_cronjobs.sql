DROP TABLE IF EXISTS `export_cronjobs`;
CREATE TABLE `export_cronjobs` (
  `cronjob_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `scheme_id` int(10) unsigned NOT NULL DEFAULT '0',
  `due_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`cronjob_id`),
  KEY `scheme_id` (`scheme_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;