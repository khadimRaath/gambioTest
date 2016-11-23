DROP TABLE IF EXISTS `parcel_services`;
CREATE TABLE `parcel_services` (
  `parcel_service_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL DEFAULT '',
  `default` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`parcel_service_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (1, 'DHL', 1);
INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (2, 'DPD', 0);
INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (3, 'GLS', 0);
INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (4, 'Hermes', 0);
INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (5, 'UPS', 0);
INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (6, 'Shipcloud', 0);
INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (7, 'FedEx', 0);
INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (8, 'MyHermes', 0);
