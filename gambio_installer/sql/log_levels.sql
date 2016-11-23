DROP TABLE IF EXISTS `log_levels`;
CREATE TABLE `log_levels` (
  `log_level_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`log_level_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `log_levels` (`log_level_id`, `name`) VALUES(1, 'error');
INSERT INTO `log_levels` (`log_level_id`, `name`) VALUES(2, 'warning');
INSERT INTO `log_levels` (`log_level_id`, `name`) VALUES(3, 'notice');