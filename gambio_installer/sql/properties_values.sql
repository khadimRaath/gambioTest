DROP TABLE IF EXISTS `properties_values`;
CREATE TABLE `properties_values` (
  `properties_values_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `properties_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `value_model` varchar(64) NOT NULL DEFAULT '',
  `value_price` decimal(9,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`properties_values_id`),
  KEY `properties_values_id` (`properties_values_id`,`properties_id`,`sort_order`),
  KEY `properties_id` (`properties_id`,`sort_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `properties_values` VALUES(1, 1, 1, 's', 0.0000);
INSERT INTO `properties_values` VALUES(2, 1, 2, 'm', 0.0000);
INSERT INTO `properties_values` VALUES(3, 1, 3, 'l', 5.0000);
INSERT INTO `properties_values` VALUES(4, 2, 1, 'gold', 0.0000);
INSERT INTO `properties_values` VALUES(5, 2, 2, 'red', 0.0000);
INSERT INTO `properties_values` VALUES(6, 2, 3, 'black', 2.0000);