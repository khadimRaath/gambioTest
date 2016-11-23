DROP TABLE IF EXISTS `log_outputs`;
CREATE TABLE `log_outputs` (
  `log_output_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`log_output_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `log_outputs` (`log_output_id`, `name`) VALUES(1, 'output');
INSERT INTO `log_outputs` (`log_output_id`, `name`) VALUES(2, 'filepath');
INSERT INTO `log_outputs` (`log_output_id`, `name`) VALUES(3, 'backtrace');
INSERT INTO `log_outputs` (`log_output_id`, `name`) VALUES(4, 'request_data');
INSERT INTO `log_outputs` (`log_output_id`, `name`) VALUES(5, 'code_snippet');
INSERT INTO `log_outputs` (`log_output_id`, `name`) VALUES(6, 'class_data');
INSERT INTO `log_outputs` (`log_output_id`, `name`) VALUES(7, 'function_data');
INSERT INTO `log_outputs` (`log_output_id`, `name`) VALUES(8, 'session_data');