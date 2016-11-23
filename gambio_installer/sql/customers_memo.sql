DROP TABLE IF EXISTS `customers_memo`;
CREATE TABLE `customers_memo` (
  `memo_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `memo_date` date NOT NULL DEFAULT '1000-01-01',
  `memo_title` text NOT NULL,
  `memo_text` text NOT NULL,
  `poster_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`memo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;