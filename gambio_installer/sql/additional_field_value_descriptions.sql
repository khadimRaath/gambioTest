DROP TABLE IF EXISTS `additional_field_value_descriptions`;
CREATE TABLE `additional_field_value_descriptions` (
  `additional_field_value_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  PRIMARY KEY (`additional_field_value_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;