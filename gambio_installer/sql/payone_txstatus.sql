DROP TABLE IF EXISTS `payone_txstatus`;
CREATE TABLE `payone_txstatus` (
  `payone_txstatus_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `received` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`payone_txstatus_id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;