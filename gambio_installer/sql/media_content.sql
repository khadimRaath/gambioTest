DROP TABLE IF EXISTS `media_content`;
CREATE TABLE `media_content` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `old_filename` text NOT NULL,
  `new_filename` text NOT NULL,
  `file_comment` text NOT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;