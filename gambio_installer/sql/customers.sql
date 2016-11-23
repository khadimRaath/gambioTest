DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `customers_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_cid` varchar(32) DEFAULT NULL,
  `customers_vat_id` varchar(20) DEFAULT NULL,
  `customers_vat_id_status` int(2) NOT NULL DEFAULT '0',
  `customers_warning` varchar(32) DEFAULT NULL,
  `customers_status` int(5) NOT NULL DEFAULT '1',
  `customers_gender` char(1) NOT NULL DEFAULT '',
  `customers_firstname` varchar(64) NOT NULL DEFAULT '',
  `customers_lastname` varchar(64) NOT NULL DEFAULT '',
  `customers_dob` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `customers_email_address` varchar(96) NOT NULL DEFAULT '',
  `customers_default_address_id` int(11) NOT NULL DEFAULT '0',
  `customers_telephone` varchar(32) NOT NULL DEFAULT '',
  `customers_fax` varchar(32) DEFAULT NULL,
  `customers_password` varchar(40) NOT NULL DEFAULT '',
  `customers_newsletter` char(1) DEFAULT NULL,
  `customers_newsletter_mode` char(1) NOT NULL DEFAULT '0',
  `member_flag` char(1) NOT NULL DEFAULT '0',
  `delete_user` char(1) NOT NULL DEFAULT '1',
  `account_type` int(1) NOT NULL DEFAULT '0',
  `password_request_key` varchar(32) NOT NULL DEFAULT '',
  `payment_unallowed` varchar(255) NOT NULL DEFAULT '',
  `shipping_unallowed` varchar(255) NOT NULL DEFAULT '',
  `refferers_id` int(5) NOT NULL DEFAULT '0',
  `customers_date_added` datetime DEFAULT '1000-01-01 00:00:00',
  `customers_last_modified` datetime DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`customers_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;