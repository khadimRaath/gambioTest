DROP TABLE IF EXISTS `email_contacts`;
CREATE TABLE `email_contacts` (
  `email_id` int(11) NOT NULL DEFAULT '0',
  `email_address` varchar(128) NOT NULL DEFAULT '',
  `contact_type` varchar(32) NOT NULL DEFAULT '',
  `contact_name` varchar(128) DEFAULT '',
  KEY `email_id_index` (`email_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;