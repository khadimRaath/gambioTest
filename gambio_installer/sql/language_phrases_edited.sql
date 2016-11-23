DROP TABLE IF EXISTS `language_phrases_edited`;
CREATE TABLE `language_phrases_edited` (
  `language_id` int(11) NOT NULL DEFAULT '0',
  `section_name` varchar(100) NOT NULL DEFAULT '',
  `phrase_name` varchar(100) NOT NULL DEFAULT '',
  `phrase_text` text NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`language_id`,`section_name`,`phrase_name`),
  KEY `section` (`language_id`,`section_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;