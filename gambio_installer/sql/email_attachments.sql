DROP TABLE IF EXISTS `email_attachments`;
CREATE TABLE `email_attachments` (
  `email_id` int(11) NOT NULL DEFAULT '0',
  `path` text NOT NULL,
  `name` varchar(255) DEFAULT '',
  KEY `email_id_index` (`email_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;