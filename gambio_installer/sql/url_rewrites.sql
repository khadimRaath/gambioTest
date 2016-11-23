CREATE TABLE IF NOT EXISTS `url_rewrites` (
  `content_id` INT NOT NULL DEFAULT '0',
  `content_type` ENUM('product','category','content','search') NOT NULL DEFAULT 'product',
  `language_id` int(11) NOT NULL DEFAULT '1',
  `rewrite_url` VARCHAR(255) NOT NULL DEFAULT '',
  `target_url` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`content_type`,`content_id`,`language_id`)
);