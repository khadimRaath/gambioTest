DROP TABLE IF EXISTS `payone_ac_cache`;
CREATE TABLE `payone_ac_cache` (
  `address_hash` varchar(32) NOT NULL DEFAULT '',
  `address_book_id` int(11) NOT NULL DEFAULT '0',
  `received` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `secstatus` int(11) NOT NULL DEFAULT '0',
  `status` varchar(7) NOT NULL DEFAULT '',
  `personstatus` varchar(4) NOT NULL DEFAULT '',
  `street` varchar(255) NOT NULL DEFAULT '',
  `streetname` varchar(255) NOT NULL DEFAULT '',
  `streetnumber` varchar(255) NOT NULL DEFAULT '',
  `zip` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `errorcode` varchar(255) NOT NULL DEFAULT '',
  `errormessage` text NOT NULL,
  `customermessage` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`address_hash`),
  KEY `address_book_id` (`address_book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;