DROP TABLE IF EXISTS `campaigns`;
CREATE TABLE `campaigns` (
  `campaigns_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaigns_name` varchar(32) NOT NULL DEFAULT '',
  `campaigns_refID` varchar(64) DEFAULT NULL,
  `campaigns_leads` int(11) NOT NULL DEFAULT '0',
  `date_added` datetime DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`campaigns_id`),
  KEY `IDX_CAMPAIGNS_NAME` (`campaigns_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;