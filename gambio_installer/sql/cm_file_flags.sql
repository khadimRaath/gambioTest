DROP TABLE IF EXISTS `cm_file_flags`;
CREATE TABLE `cm_file_flags` (
  `file_flag` int(11) NOT NULL DEFAULT '0',
  `file_flag_name` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`file_flag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `cm_file_flags` (`file_flag`, `file_flag_name`) VALUES(0, 'information');
INSERT INTO `cm_file_flags` (`file_flag`, `file_flag_name`) VALUES(1, 'content');
INSERT INTO `cm_file_flags` (`file_flag`, `file_flag_name`) VALUES(2, 'topmenu_corner');
INSERT INTO `cm_file_flags` (`file_flag`, `file_flag_name`) VALUES(3, 'topmenu');
INSERT INTO `cm_file_flags` (`file_flag`, `file_flag_name`) VALUES(4, 'extraboxes');
INSERT INTO `cm_file_flags` (`file_flag`, `file_flag_name`) VALUES(5, 'withdrawal');