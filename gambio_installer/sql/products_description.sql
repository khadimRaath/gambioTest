DROP TABLE IF EXISTS `products_description`;
CREATE TABLE `products_description` (
  `products_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL DEFAULT '1',
  `products_name` varchar(255) NOT NULL DEFAULT '',
  `products_description` text,
  `products_short_description` text,
  `products_keywords` varchar(255) DEFAULT NULL,
  `products_meta_title` text NOT NULL,
  `products_meta_description` text NOT NULL,
  `products_meta_keywords` text NOT NULL,
  `products_url` varchar(255) DEFAULT NULL,
  `products_viewed` int(5) DEFAULT '0',
  `gm_alt_text` varchar(255) DEFAULT NULL,
  `gm_url_keywords` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL DEFAULT '',
  `checkout_information` text NOT NULL,
  PRIMARY KEY (`products_id`,`language_id`),
  KEY `products_name` (`products_name`),
  KEY `language_id` (`language_id`,`products_keywords`),
  KEY `language_id_2` (`language_id`,`products_name`),
  KEY `seo_boost_index` (`gm_url_keywords`,`products_id`,`language_id`),
  FULLTEXT KEY `products_name_2` (`products_name`),
  FULLTEXT KEY `products_description` (`products_description`),
  FULLTEXT KEY `products_short_description` (`products_short_description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `products_description` (`products_id`, `language_id`, `products_name`, `products_description`, `products_short_description`, `products_keywords`, `products_meta_title`, `products_meta_description`, `products_meta_keywords`, `products_url`, `products_viewed`, `gm_alt_text`, `gm_url_keywords`) VALUES(1, 1, 'test article', '<p>test article description</p>', '<p>test article short description</p>', '', '', '', '', '', 0, 'product image', 'test-article');
INSERT INTO `products_description` (`products_id`, `language_id`, `products_name`, `products_description`, `products_short_description`, `products_keywords`, `products_meta_title`, `products_meta_description`, `products_meta_keywords`, `products_url`, `products_viewed`, `gm_alt_text`, `gm_url_keywords`) VALUES(1, 2, 'Testartikel', '[TAB:Seite 1] Testartikel Beschreibung Seite 1 [TAB:Seite 2] Testartikel Beschreibung Seite 2 [TAB:Seite 3] Testartikel Beschreibung Seite 3', '<p>Testartikel Kurzbeschreibung</p>', '', '', '', '', '', 32, 'Artikelbild', 'Testartikel');