DROP TABLE IF EXISTS `email_templates_edited`;
CREATE TABLE `email_templates_edited` (
  `name` varchar(32) NOT NULL DEFAULT '',
  `language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` enum('txt','html') NOT NULL DEFAULT 'txt',
  `content` mediumtext NOT NULL,
  `backup` mediumtext NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`name`,`language_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;