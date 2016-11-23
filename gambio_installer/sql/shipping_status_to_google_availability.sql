DROP TABLE IF EXISTS `shipping_status_to_google_availability`;
CREATE TABLE `shipping_status_to_google_availability` (
  `shipping_status_id` int(10) unsigned NOT NULL DEFAULT '0',
  `google_export_availability_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`shipping_status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `shipping_status_to_google_availability` (`shipping_status_id`, `google_export_availability_id`) VALUES(1, 1);
INSERT INTO `shipping_status_to_google_availability` (`shipping_status_id`, `google_export_availability_id`) VALUES(2, 1);
INSERT INTO `shipping_status_to_google_availability` (`shipping_status_id`, `google_export_availability_id`) VALUES(3, 3);