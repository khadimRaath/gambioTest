DROP TABLE IF EXISTS `gm_admin_favorites`;
CREATE TABLE `gm_admin_favorites` (
  `favorites_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `link_key` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`favorites_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `gm_admin_favorites` (`favorites_id`, `customers_id`, `link_key`, `sort_order`) VALUES(1, 1, 'id_e6d18015c0e4f88f119c48f00142e6d5', 0);
INSERT INTO `gm_admin_favorites` (`favorites_id`, `customers_id`, `link_key`, `sort_order`) VALUES(2, 1, 'id_46a7254c38e4bfacf97ae97135da12d9', 0);
INSERT INTO `gm_admin_favorites` (`favorites_id`, `customers_id`, `link_key`, `sort_order`) VALUES(3, 1, 'id_87bad688f21b28cca9981cbf2b843249', 0);
INSERT INTO `gm_admin_favorites` (`favorites_id`, `customers_id`, `link_key`, `sort_order`) VALUES(4, 1, 'id_ae14fd556f1e86067621605e2dac56b4', 0);
INSERT INTO `gm_admin_favorites` (`favorites_id`, `customers_id`, `link_key`, `sort_order`) VALUES(5, 1, 'id_393bb7cb2161a3c52f4feb39f71f1f2a', 0);