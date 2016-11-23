DROP TABLE IF EXISTS `job_waiting_tickets`;
CREATE TABLE `job_waiting_tickets` (
  `waiting_ticket_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(45) NOT NULL DEFAULT '',
  `callback` varchar(45) NOT NULL DEFAULT '',
  `due_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `done_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`waiting_ticket_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;