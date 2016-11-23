DROP TABLE IF EXISTS `coupon_email_track`;
CREATE TABLE `coupon_email_track` (
  `unique_id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) NOT NULL DEFAULT '0',
  `customer_id_sent` int(11) NOT NULL DEFAULT '0',
  `sent_firstname` varchar(32) DEFAULT NULL,
  `sent_lastname` varchar(32) DEFAULT NULL,
  `emailed_to` varchar(32) DEFAULT NULL,
  `date_sent` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`unique_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;