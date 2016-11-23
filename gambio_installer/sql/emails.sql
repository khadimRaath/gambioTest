DROP TABLE IF EXISTS `emails`;
CREATE TABLE `emails` (
  `email_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(256) NOT NULL DEFAULT '',
  `content_plain` text,
  `content_html` longtext,
  `is_pending` tinyint(4) DEFAULT '1',
  `creation_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `sent_date` datetime DEFAULT NULL,
  PRIMARY KEY (`email_id`),
  KEY `email_id_index` (`email_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;