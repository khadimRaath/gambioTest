DROP TABLE IF EXISTS `gm_admin_boxes`;
CREATE TABLE `gm_admin_boxes` (
  `boxes_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `box_key` varchar(64) NOT NULL DEFAULT '',
  `box_status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`boxes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(530, 4, 'BOX_HEADING_GAMBIO_SEO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(587, 4, 'BOX_HEADING_GAMBIO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(581, 4, 'BOX_HEADING_CUSTOMERS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(529, 4, 'BOX_HEADING_PRODUCTS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(532, 4, 'BOX_HEADING_STATISTICS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(589, 4, 'BOX_HEADING_MODULES', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(533, 4, 'BOX_HEADING_TOOLS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(583, 4, 'BOX_HEADING_GV_ADMIN', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(535, 4, 'BOX_HEADING_ZONE', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(536, 4, 'BOX_HEADING_CONFIGURATION', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(394, 7, 'BOX_HEADING_GAMBIO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(269, 7, 'BOX_HEADING_GAMBIO_SEO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(501, 8, 'BOX_HEADING_MODULES', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(502, 8, 'BOX_HEADING_STATISTICS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(223, 6, 'BOX_HEADING_GAMBIO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(225, 6, 'BOX_HEADING_GAMBIO_SEO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(320, 6, 'BOX_HEADING_CUSTOMERS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(514, 8, 'BOX_HEADING_GAMBIO_SEO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(372, 7, 'BOX_HEADING_PRODUCTS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(547, 8, 'BOX_HEADING_CUSTOMERS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(378, 7, 'BOX_HEADING_CUSTOMERS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(306, 7, 'BOX_HEADING_ZONE', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(505, 8, 'BOX_HEADING_ZONE', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(500, 8, 'BOX_HEADING_PRODUCTS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(366, 4, 'last', 0);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(557, 0, 'BOX_HEADING_GAMBIO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(508, 8, 'BOX_HEADING_CONFIGURATION', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(504, 8, 'BOX_HEADING_GV_ADMIN', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(503, 8, 'BOX_HEADING_TOOLS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(432, 24, 'BOX_HEADING_GAMBIO_SEO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(434, 24, 'BOX_HEADING_CUSTOMERS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(436, 24, 'BOX_HEADING_GV_ADMIN', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(438, 24, 'BOX_HEADING_GAMBIO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(456, 24, 'BOX_HEADING_ZONE', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(458, 24, 'BOX_HEADING_CONFIGURATION', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(450, 24, 'BOX_HEADING_STATISTICS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(454, 24, 'BOX_HEADING_TOOLS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(462, 24, 'BOX_HEADING_PRODUCTS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(464, 25, 'BOX_HEADING_GAMBIO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(510, 25, 'BOX_HEADING_CUSTOMERS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(516, 8, 'BOX_HEADING_GAMBIO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(541, 44, 'BOX_HEADING_GAMBIO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(543, 44, 'BOX_HEADING_GV_ADMIN', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(545, 44, 'BOX_HEADING_STATISTICS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(549, 44, 'BOX_HEADING_CUSTOMERS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(597, 1, 'BOX_HEADING_PRODUCTS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(601, 2, 'BOX_HEADING_GAMBIO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(561, 2, 'BOX_HEADING_PRODUCTS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(559, 2, 'BOX_HEADING_CUSTOMERS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(565, 2, 'BOX_HEADING_GAMBIO_SEO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(567, 2, 'BOX_HEADING_MODULES', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(569, 3, 'BOX_HEADING_GAMBIO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(575, 2, 'BOX_HEADING_TOOLS', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(577, 3, 'BOX_HEADING_GAMBIO_SEO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(593, 1, 'BOX_HEADING_GAMBIO_SEO', 1);
INSERT INTO `gm_admin_boxes` (`boxes_id`, `customers_id`, `box_key`, `box_status`) VALUES(603, 1, 'BOX_HEADING_GAMBIO', 1);