DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `languages_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `code` char(2) NOT NULL DEFAULT '',
  `image` varchar(64) DEFAULT NULL,
  `directory` varchar(32) DEFAULT NULL,
  `sort_order` int(3) DEFAULT NULL,
  `language_charset` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`languages_id`),
  KEY `IDX_LANGUAGES_NAME` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `languages` (`languages_id`, `name`, `code`, `image`, `directory`, `sort_order`, `language_charset`, `status`) VALUES(1, 'English', 'en', 'icon.gif', 'english', 2, 'utf-8', 1);
INSERT INTO `languages` (`languages_id`, `name`, `code`, `image`, `directory`, `sort_order`, `language_charset`, `status`) VALUES(2, 'Deutsch', 'de', 'icon.gif', 'german', 1, 'utf-8', 1);