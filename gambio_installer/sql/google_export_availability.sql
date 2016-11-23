DROP TABLE IF EXISTS `google_export_availability`;
CREATE TABLE `google_export_availability` (
  `google_export_availability_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `availability` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`google_export_availability_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `google_export_availability` (`google_export_availability_id`, `availability`) VALUES(1, 'auf lager');
INSERT INTO `google_export_availability` (`google_export_availability_id`, `availability`) VALUES(3, 'nicht auf lager');
INSERT INTO `google_export_availability` (`google_export_availability_id`, `availability`) VALUES(4, 'vorbestellt');