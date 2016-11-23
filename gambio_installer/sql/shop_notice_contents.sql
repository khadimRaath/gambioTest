DROP TABLE IF EXISTS `shop_notice_contents`;
CREATE TABLE `shop_notice_contents` (
  `shop_notice_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  PRIMARY KEY (`shop_notice_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `shop_notice_contents` (`shop_notice_id`, `language_id`, `content`) VALUES(1, 1, '');
INSERT INTO `shop_notice_contents` (`shop_notice_id`, `language_id`, `content`) VALUES(1, 2, '');
INSERT INTO `shop_notice_contents` (`shop_notice_id`, `language_id`, `content`) VALUES(2, 1, '');
INSERT INTO `shop_notice_contents` (`shop_notice_id`, `language_id`, `content`) VALUES(2, 2, '');