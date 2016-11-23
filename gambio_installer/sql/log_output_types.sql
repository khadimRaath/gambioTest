DROP TABLE IF EXISTS `log_output_types`;
CREATE TABLE `log_output_types` (
  `log_output_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`log_output_type_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `log_output_types` (`log_output_type_id`, `name`) VALUES(1, 'file');
INSERT INTO `log_output_types` (`log_output_type_id`, `name`) VALUES(2, 'screen');
INSERT INTO `log_output_types` (`log_output_type_id`, `name`) VALUES(3, 'mail');
INSERT INTO `log_output_types` (`log_output_type_id`, `name`) VALUES(4, 'html_file');