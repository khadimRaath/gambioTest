DROP TABLE IF EXISTS `products_xsell_grp_name`;
CREATE TABLE `products_xsell_grp_name` (
  `products_xsell_grp_name_id` int(10) NOT NULL DEFAULT '0',
  `xsell_sort_order` int(10) NOT NULL DEFAULT '0',
  `language_id` smallint(6) NOT NULL DEFAULT '0',
  `groupname` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;