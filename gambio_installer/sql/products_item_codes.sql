DROP TABLE IF EXISTS `products_item_codes`;
CREATE TABLE `products_item_codes` (
  `products_id` int(11) NOT NULL DEFAULT '0',
  `code_isbn` varchar(128) DEFAULT NULL,
  `code_upc` varchar(128) DEFAULT NULL,
  `code_mpn` varchar(128) DEFAULT NULL,
  `code_jan` varchar(128) DEFAULT NULL,
  `google_export_condition` varchar(64) NOT NULL DEFAULT 'neu',
  `google_export_availability_id` int(10) unsigned NOT NULL DEFAULT '0',
  `brand_name` varchar(128) NOT NULL DEFAULT '',
  `identifier_exists` tinyint(1) NOT NULL DEFAULT '1',
  `gender` enum('','Herren','Damen','Unisex') NOT NULL DEFAULT '',
  `age_group` enum('','Erwachsene','Kinder') NOT NULL DEFAULT '',
  `expiration_date` date NOT NULL DEFAULT '1000-01-01',
  PRIMARY KEY (`products_id`),
  KEY `google_export_condition` (`google_export_condition`),
  KEY `brand_name` (`brand_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;