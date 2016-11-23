DROP TABLE IF EXISTS `payone_txstatus_data`;
CREATE TABLE `payone_txstatus_data` (
  `payone_txstatus_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `payone_txstatus_id` int(11) NOT NULL DEFAULT '0',
  `key` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`payone_txstatus_data_id`),
  KEY `payone_txstatus_id` (`payone_txstatus_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;