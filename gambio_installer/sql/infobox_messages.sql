DROP TABLE IF EXISTS `infobox_messages`;
CREATE TABLE `infobox_messages` (
  `infobox_messages_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source` varchar(255) NOT NULL DEFAULT '',
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `status` enum('new','read','hidden','deleted') NOT NULL DEFAULT 'new',
  `type` enum('info','warning','success') NOT NULL DEFAULT 'info',
  `visibility` enum('alwayson','hideable','removable') NOT NULL DEFAULT 'hideable',
  `button_link` varchar(255) NOT NULL DEFAULT '',
  `customers_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`infobox_messages_id`),
  UNIQUE KEY `identifier` (`identifier`,`customers_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;