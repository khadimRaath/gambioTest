DROP TABLE IF EXISTS `products_attributes`;
CREATE TABLE `products_attributes` (
  `products_attributes_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) NOT NULL DEFAULT '0',
  `options_id` int(11) NOT NULL DEFAULT '0',
  `options_values_id` int(11) NOT NULL DEFAULT '0',
  `options_values_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `price_prefix` char(1) NOT NULL DEFAULT '',
  `attributes_model` varchar(64) DEFAULT NULL,
  `attributes_stock` decimal(15,4) DEFAULT NULL,
  `options_values_weight` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `weight_prefix` char(1) NOT NULL DEFAULT '',
  `sortorder` int(11) DEFAULT NULL,
  `products_vpe_id` int(11) unsigned NOT NULL DEFAULT '0',
  `gm_vpe_value` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `gm_ean` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`products_attributes_id`),
  KEY `products_id` (`products_id`,`options_id`,`options_values_id`,`sortorder`),
  KEY `options_values_id` (`options_values_id`),
  KEY `sortorder` (`sortorder`),
  FULLTEXT KEY `attributes_model` (`attributes_model`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;