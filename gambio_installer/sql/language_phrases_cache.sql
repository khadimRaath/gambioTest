DROP TABLE IF EXISTS `language_phrases_cache`;
CREATE TABLE `language_phrases_cache` (
  `language_id` int(11) NOT NULL DEFAULT '0',
  `section_name` varchar(100) NOT NULL DEFAULT '',
  `phrase_name` varchar(100) NOT NULL DEFAULT '',
  `phrase_text` text NOT NULL,
  `source` varchar(255) NOT NULL DEFAULT '',
  `date_modified` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`language_id`,`section_name`,`phrase_name`),
  KEY `search` (`source`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;