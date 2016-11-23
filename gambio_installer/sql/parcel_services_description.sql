DROP TABLE IF EXISTS `parcel_services_description`;
CREATE TABLE `parcel_services_description` (
  `parcel_service_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `url` varchar(1023) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  PRIMARY KEY (`parcel_service_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (1, 1, 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=en&idc={TRACKING_NUMBER}&rfn=&extendedSearch=true', 'You can access the shipment tracking for your order by visiting the link above.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (1, 2, 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc={TRACKING_NUMBER}&rf n=&extendedSearch=true', 'Die Sendungsverfolgung für Ihre Bestellung können Sie über den oben stehenden Link aufrufen.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (2, 1, 'https://tracking.dpd.de/parcelstatus?query={TRACKING_NUMBER}&locale=en_DE', 'You can access the shipment tracking for your order by visiting the link above.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (2, 2, 'https://tracking.dpd.de/parcelstatus?query={TRACKING_NUMBER}&locale=de_DE', 'Die Sendungsverfolgung für Ihre Bestellung können Sie über den oben stehenden Link aufrufen.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (3, 1, 'https://gls-group.eu/DE/en/parcel-tracking?match={TRACKING_NUMBER}', 'You can access the shipment tracking for your order by visiting the link above.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (3, 2, 'https://gls-group.eu/DE/de/paketverfolgung?match={TRACKING_NUMBER}', 'Die Sendungsverfolgung für Ihre Bestellung können Sie über den oben stehenden Link aufrufen.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (4, 1, 'https://tracking.hermesworld.com/?TrackID={TRACKING_NUMBER}', 'You can access the shipment tracking for your order by visiting the link above.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (4, 2, 'https://tracking.hermesworld.com/?TrackID={TRACKING_NUMBER}', 'Die Sendungsverfolgung für Ihre Bestellung können Sie über den oben stehenden Link aufrufen.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (5, 1, 'http://wwwapps.ups.com/ietracking/tracking.cgi?tracknum={TRACKING_NUMBER}&IATA=de&Lang=eng', 'You can access the shipment tracking for your order by visiting the link above.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (5, 2, 'http://wwwapps.ups.com/ietracking/tracking.cgi?tracknum={TRACKING_NUMBER}&IATA=de&Lang=ger', 'Die Sendungsverfolgung für Ihre Bestellung können Sie über den oben stehenden Link aufrufen.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (6, 1, 'https://shipcloud.io', 'You can access the shipment tracking for your order by visiting the link above.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (6, 2, 'https://shipcloud.io', 'Die Sendungsverfolgung für Ihre Bestellung können Sie über den oben stehenden Link aufrufen.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (7, 1, 'https://www.fedex.com/apps/fedextrack/?action=track&cntry_code=en&trackingnumber={TRACKING_NUMBER}', 'You can access the shipment tracking for your order by visiting the link above.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (7, 2, 'https://www.fedex.com/apps/fedextrack/?action=track&cntry_code=de&trackingnumber={TRACKING_NUMBER}', 'Die Sendungsverfolgung für Ihre Bestellung können Sie über den oben stehenden Link aufrufen.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (8, 1, 'https://www.myhermes.de/wps/portal/paket/SISYR?auftragsNummer={TRACKING_NUMBER}', 'Die Sendungsverfolgung für Ihre Bestellung können Sie über den oben stehenden Link aufrufen.');
INSERT INTO `parcel_services_description` (`parcel_service_id`, `language_id`, `url`, `comment`) VALUES (8, 2, 'https://www.myhermes.de/wps/portal/paket/SISYR?auftragsNummer={TRACKING_NUMBER}', 'You can access the shipment tracking for your order by visiting the link above.');
