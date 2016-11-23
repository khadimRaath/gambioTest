DROP TABLE IF EXISTS `gm_boxes`;
CREATE TABLE `gm_boxes` (
  `boxes_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(32) NOT NULL DEFAULT '',
  `box_name` varchar(64) NOT NULL DEFAULT '',
  `position` varchar(32) NOT NULL DEFAULT '',
  `box_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`boxes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Gambio StyleEdit INTERFACE TABLE';

INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'admin', 'gm_box_pos_2', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'categories', 'gm_box_pos_4', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'filter', 'gm_box_pos_6', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'login', 'gm_box_pos_8', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'bestsellers', 'gm_box_pos_10', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'reviews', 'gm_box_pos_12', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'gm_trusted_shops_widget', 'gm_box_pos_14', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'specials', 'gm_box_pos_16', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'add_quickie', 'gm_box_pos_18', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'search', 'gm_box_pos_20', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'manufacturers_info', 'gm_box_pos_22', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'languages', 'gm_box_pos_24', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'currencies', 'gm_box_pos_26', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'infobox', 'gm_box_pos_28', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'newsletter', 'gm_box_pos_30', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'gm_counter', 'gm_box_pos_32', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'last_viewed', 'gm_box_pos_34', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'whatsnew', 'gm_box_pos_36', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'order_history', 'gm_box_pos_38', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'manufacturers', 'gm_box_pos_40', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'trusted', 'gm_box_pos_42', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'gm_scroller', 'gm_box_pos_44', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'information', 'gm_box_pos_46', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'content', 'gm_box_pos_48', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'gm_bookmarks', 'gm_box_pos_50', 1);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'paypal', 'gm_box_pos_52', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'extrabox1', 'gm_box_pos_54', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'extrabox2', 'gm_box_pos_56', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'extrabox3', 'gm_box_pos_58', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'extrabox4', 'gm_box_pos_60', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'extrabox5', 'gm_box_pos_62', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'extrabox6', 'gm_box_pos_64', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'extrabox7', 'gm_box_pos_66', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'extrabox8', 'gm_box_pos_70', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'extrabox9', 'gm_box_pos_74', 0);
INSERT INTO `gm_boxes` (`template_name`, `box_name`, `position`, `box_status`) VALUES('EyeCandy', 'ekomi', 'gm_box_pos_76', 0);