DROP TABLE IF EXISTS `shared_shopping_carts`;
CREATE TABLE `shared_shopping_carts` (
  `hash` VARCHAR(32) COLLATE utf8_general_ci NOT NULL,
  `json_shopping_cart` TEXT COLLATE utf8_general_ci NOT NULL,
  `creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_id` INT NOT NULL,
  PRIMARY KEY (`hash`(32))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;