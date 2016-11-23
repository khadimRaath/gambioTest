DROP TABLE IF EXISTS `coupons_description`;
CREATE TABLE `coupons_description` (
  `coupon_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `coupon_name` varchar(32) NOT NULL DEFAULT '',
  `coupon_description` text,
  PRIMARY KEY (`coupon_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;