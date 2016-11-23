DROP TABLE IF EXISTS `personal_offers_by_customers_status_`;
CREATE TABLE `personal_offers_by_customers_status_` (
  `price_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) NOT NULL DEFAULT '0',
  `quantity` decimal(15,4) DEFAULT NULL,
  `personal_offer` decimal(15,4) DEFAULT NULL,
  PRIMARY KEY (`price_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;