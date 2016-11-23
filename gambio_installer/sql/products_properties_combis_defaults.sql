DROP TABLE IF EXISTS `products_properties_combis_defaults`;
CREATE TABLE `products_properties_combis_defaults` (
  `products_properties_combis_defaults_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `products_id` int(10) unsigned NOT NULL DEFAULT '0',
  `combi_ean` varchar(20) NOT NULL DEFAULT '',
  `combi_quantity` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `combi_shipping_status_id` int(11) NOT NULL DEFAULT '0',
  `combi_weight` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `combi_price_type` enum('calc','fix') NOT NULL,
  `combi_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `products_vpe_id` int(11) NOT NULL DEFAULT '0',
  `vpe_value` decimal(15,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`products_properties_combis_defaults_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `products_properties_combis_defaults` VALUES(1, 1, '123456789', 100.0000, 2, 2.0000, 'calc', 0.0000, 0, 0.0000);