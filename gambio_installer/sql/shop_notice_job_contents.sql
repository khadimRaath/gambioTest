DROP TABLE IF EXISTS `shop_notice_job_contents`;
CREATE TABLE `shop_notice_job_contents` (
  `shop_notice_job_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `topbar_content` text NOT NULL,
  `popup_content` text NOT NULL,
  PRIMARY KEY (`shop_notice_job_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;