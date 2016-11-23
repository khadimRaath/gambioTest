DROP TABLE IF EXISTS `categories_description`;
CREATE TABLE `categories_description` (
  `categories_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '1',
  `categories_name` varchar(255) NOT NULL DEFAULT '',
  `categories_heading_title` varchar(255) NOT NULL DEFAULT '',
  `categories_description` text NOT NULL,
  `categories_meta_title` text NOT NULL,
  `categories_meta_description` text NOT NULL,
  `categories_meta_keywords` text NOT NULL,
  `gm_alt_text` varchar(255) NOT NULL DEFAULT '',
  `gm_url_keywords` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL DEFAULT '',
  PRIMARY KEY (`categories_id`,`language_id`),
  KEY `idx_categories_name` (`categories_name`),
  KEY `seo_boost_index` (`gm_url_keywords`,`categories_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `categories_description` (`categories_id`, `language_id`, `categories_name`, `categories_heading_title`, `categories_description`, `categories_meta_title`, `categories_meta_description`, `categories_meta_keywords`, `gm_alt_text`, `gm_url_keywords`) VALUES(1, 1, 'test category', 'test category', '<p>test category description</p>', '', '', '', '', 'test-category');
INSERT INTO `categories_description` (`categories_id`, `language_id`, `categories_name`, `categories_heading_title`, `categories_description`, `categories_meta_title`, `categories_meta_description`, `categories_meta_keywords`, `gm_alt_text`, `gm_url_keywords`) VALUES(1, 2, 'Testkategorie', 'Testkategorie', '<p>Testkategorie Beschreibung</p>', '', '', '', '', 'Testkategorie');