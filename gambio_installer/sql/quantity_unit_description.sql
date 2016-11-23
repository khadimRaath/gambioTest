DROP TABLE IF EXISTS `quantity_unit_description`;
CREATE TABLE `quantity_unit_description` (
  `quantity_unit_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `unit_name` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`quantity_unit_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;