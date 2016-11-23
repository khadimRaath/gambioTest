DROP TABLE IF EXISTS `newsletter_recipients`;
CREATE TABLE `newsletter_recipients` (
  `mail_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_email_address` varchar(96) NOT NULL DEFAULT '',
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `customers_status` int(5) NOT NULL DEFAULT '0',
  `customers_firstname` varchar(32) NOT NULL DEFAULT '',
  `customers_lastname` varchar(32) NOT NULL DEFAULT '',
  `mail_status` int(1) NOT NULL DEFAULT '0',
  `mail_key` varchar(32) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`mail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;