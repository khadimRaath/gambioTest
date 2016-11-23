DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `banners_id` int(11) NOT NULL AUTO_INCREMENT,
  `banners_title` varchar(64) NOT NULL DEFAULT '',
  `banners_url` varchar(255) NOT NULL DEFAULT '',
  `banners_image` varchar(64) NOT NULL DEFAULT '',
  `banners_group` varchar(10) NOT NULL DEFAULT '',
  `banners_html_text` text,
  `expires_impressions` int(7) DEFAULT '0',
  `expires_date` datetime DEFAULT NULL,
  `date_scheduled` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `date_status_change` datetime DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`banners_id`),
  KEY `status` (`status`,`banners_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;