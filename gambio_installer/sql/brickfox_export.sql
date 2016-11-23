DROP TABLE IF EXISTS `brickfox_export`;
CREATE TABLE `brickfox_export` (
  `brickfox_export_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL DEFAULT '',
  `number_exported` int(11) NOT NULL DEFAULT '0',
  `date_exported` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`brickfox_export_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;