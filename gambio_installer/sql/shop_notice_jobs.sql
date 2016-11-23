DROP TABLE IF EXISTS `shop_notice_jobs`;
CREATE TABLE `shop_notice_jobs` (
  `shop_notice_job_id` int(11) NOT NULL AUTO_INCREMENT,
  `waiting_ticket_id` int(11) NOT NULL DEFAULT '0',
  `shop_active` tinyint(4) NOT NULL DEFAULT '0',
  `shop_offline_content` text NOT NULL,
  `topbar_active` int(11) NOT NULL DEFAULT '0',
  `topbar_color` varchar(45) NOT NULL DEFAULT '',
  `topbar_mode` varchar(45) NOT NULL DEFAULT '',
  `popup_active` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_notice_job_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;