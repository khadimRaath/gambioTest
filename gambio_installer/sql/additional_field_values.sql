DROP TABLE IF EXISTS `additional_field_values`;
CREATE TABLE `additional_field_values` (
  `additional_field_value_id` int(11) NOT NULL AUTO_INCREMENT,
  `additional_field_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`additional_field_value_id`),
  UNIQUE KEY `additional_field_id` (`additional_field_id`,`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;