DROP TABLE IF EXISTS `version_history`;
CREATE TABLE `version_history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` enum('master_update','service_pack','update') NOT NULL DEFAULT 'update',
  `revision` int(11) NOT NULL DEFAULT '0',
  `is_full_version` tinyint(1) NOT NULL DEFAULT '0',
  `installation_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `php_version` varchar(255) NOT NULL DEFAULT '',
  `mysql_version` varchar(255) NOT NULL DEFAULT '',
  `installed` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Signalisiert, ob ein Versionseintrag wirklich installiert wurde oder durch die Versionsauswahl angelegt wurde.',
  PRIMARY KEY (`history_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;