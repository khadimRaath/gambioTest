DROP TABLE IF EXISTS `module_newsletter_temp_1`;
CREATE TABLE `module_newsletter_temp_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `customers_status` int(11) NOT NULL DEFAULT '0',
  `customers_firstname` varchar(64) NOT NULL DEFAULT '',
  `customers_lastname` varchar(64) NOT NULL DEFAULT '',
  `customers_email_address` text NOT NULL,
  `mail_key` varchar(32) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `comment` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;