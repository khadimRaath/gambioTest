DROP TABLE IF EXISTS `whos_online`;
CREATE TABLE `whos_online` (
  `customer_id` int(11) DEFAULT NULL,
  `full_name` varchar(64) NOT NULL DEFAULT '',
  `session_id` varchar(128) NOT NULL DEFAULT '',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `time_entry` varchar(14) NOT NULL DEFAULT '',
  `time_last_click` varchar(14) NOT NULL DEFAULT '',
  `last_page_url` varchar(255) NOT NULL DEFAULT '',
  KEY `customer_id` (`customer_id`),
  KEY `session_id` (`session_id`),
  KEY `time_last_click` (`time_last_click`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;