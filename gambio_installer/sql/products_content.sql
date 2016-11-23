DROP TABLE IF EXISTS `products_content`;
CREATE TABLE `products_content` (
  `content_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) NOT NULL DEFAULT '0',
  `group_ids` text,
  `content_name` varchar(32) NOT NULL DEFAULT '',
  `content_file` varchar(64) NOT NULL DEFAULT '',
  `content_link` text NOT NULL,
  `languages_id` int(11) NOT NULL DEFAULT '0',
  `content_read` int(11) NOT NULL DEFAULT '0',
  `file_comment` text NOT NULL,
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;