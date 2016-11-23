DROP TABLE IF EXISTS `newsletters`;
CREATE TABLE `newsletters` (
  `newsletters_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `module` varchar(255) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `date_sent` datetime DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `locked` int(1) DEFAULT '0',
  PRIMARY KEY (`newsletters_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;