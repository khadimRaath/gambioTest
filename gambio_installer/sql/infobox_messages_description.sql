DROP TABLE IF EXISTS `infobox_messages_description`;
CREATE TABLE `infobox_messages_description` (
  `infobox_messages_id` int(10) unsigned NOT NULL DEFAULT '0',
  `languages_id` int(10) unsigned NOT NULL DEFAULT '0',
  `headline` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `button_label` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`infobox_messages_id`,`languages_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;