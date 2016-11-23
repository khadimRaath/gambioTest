DROP TABLE IF EXISTS `gm_bookmarks`;
CREATE TABLE `gm_bookmarks` (
  `gm_bookmarks_id` int(11) NOT NULL AUTO_INCREMENT,
  `gm_bookmarks_name` varchar(255) DEFAULT NULL,
  `gm_bookmarks_link` varchar(255) DEFAULT NULL,
  `gm_bookmarks_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`gm_bookmarks_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `gm_bookmarks` (`gm_bookmarks_id`, `gm_bookmarks_name`, `gm_bookmarks_link`, `gm_bookmarks_image`) VALUES(14, 'digg.com', 'http://digg.com/submit?url=', 'diggs.gif');
INSERT INTO `gm_bookmarks` (`gm_bookmarks_id`, `gm_bookmarks_name`, `gm_bookmarks_link`, `gm_bookmarks_image`) VALUES(16, 'folkd', 'http://www.folkd.com/page/submit.html?addtofolkd=1&step1_sent=1&url=', 'folkd.gif');
INSERT INTO `gm_bookmarks` (`gm_bookmarks_id`, `gm_bookmarks_name`, `gm_bookmarks_link`, `gm_bookmarks_image`) VALUES(17, 'favoriten.de', 'http://www.favoriten.de/url-hinzufuegen.html?bm_url=', 'favoriten.gif');
INSERT INTO `gm_bookmarks` (`gm_bookmarks_id`, `gm_bookmarks_name`, `gm_bookmarks_link`, `gm_bookmarks_image`) VALUES(19, 'google', 'http://www.google.com/bookmarks/mark?op=add&hl=de&bkmk=', 'google.gif');
INSERT INTO `gm_bookmarks` (`gm_bookmarks_id`, `gm_bookmarks_name`, `gm_bookmarks_link`, `gm_bookmarks_image`) VALUES(20, 'del.icio.us', 'http://del.icio.us/post?url=', 'delicio.gif');
INSERT INTO `gm_bookmarks` (`gm_bookmarks_id`, `gm_bookmarks_name`, `gm_bookmarks_link`, `gm_bookmarks_image`) VALUES(24, 'edelight', 'http://www.edelight.de/geschenk/neu?purl=', 'edelight.gif');