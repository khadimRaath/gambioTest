DROP TABLE IF EXISTS `field_replace_jobs`;
CREATE TABLE `field_replace_jobs` (
  `field_replace_job_id` int(11) NOT NULL AUTO_INCREMENT,
  `waiting_ticket_id` int(11) NOT NULL DEFAULT '0',
  `table_name` varchar(255) NOT NULL DEFAULT '',
  `field_name` varchar(255) NOT NULL DEFAULT '',
  `old_value` text NOT NULL,
  `new_value` text NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`field_replace_job_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;