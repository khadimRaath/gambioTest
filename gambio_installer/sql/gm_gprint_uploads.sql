DROP TABLE IF EXISTS `gm_gprint_uploads`;
CREATE TABLE `gm_gprint_uploads` (
  `gm_gprint_uploads_id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `customers_id` int(10) unsigned DEFAULT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `encrypted_filename` varchar(255) NOT NULL DEFAULT '',
  `download_key` varchar(32) NOT NULL DEFAULT '',
  `ip_hash` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`gm_gprint_uploads_id`),
  UNIQUE KEY `encrypted_filename` (`encrypted_filename`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;