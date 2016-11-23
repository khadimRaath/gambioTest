DROP TABLE IF EXISTS `products_xsell`;
CREATE TABLE `products_xsell` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `products_id` int(10) unsigned NOT NULL DEFAULT '1',
  `products_xsell_grp_name_id` int(10) unsigned NOT NULL DEFAULT '1',
  `xsell_id` int(10) unsigned NOT NULL DEFAULT '1',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `products_id` (`products_id`,`xsell_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;