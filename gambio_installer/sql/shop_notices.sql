DROP TABLE IF EXISTS `shop_notices`;
CREATE TABLE `shop_notices` (
  `shop_notice_id` int(11) NOT NULL DEFAULT '0',
  `notice_type` enum('topbar','popup') DEFAULT NULL,
  PRIMARY KEY (`shop_notice_id`),
  UNIQUE KEY `notice_type_UNIQUE` (`notice_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `shop_notices` (`shop_notice_id`, `notice_type`) VALUES(1, 'topbar');
INSERT INTO `shop_notices` (`shop_notice_id`, `notice_type`) VALUES(2, 'popup');