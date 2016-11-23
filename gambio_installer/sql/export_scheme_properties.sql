DROP TABLE IF EXISTS `export_scheme_properties`;
CREATE TABLE `export_scheme_properties` (
  `scheme_id` int(11) unsigned NOT NULL DEFAULT '0',
  `properties_column` varchar(100) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`scheme_id`,`properties_column`),
  KEY `scheme_id` (`scheme_id`,`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;