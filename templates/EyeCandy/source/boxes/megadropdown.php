<?php
/* --------------------------------------------------------------
  megadropdown.php 2014-10-24 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

// Categories Top
/** @var CategoriesMenuBoxContentView $coo_content_view */
$coo_content_view = MainFactory::create_object('CategoriesMenuBoxContentView');
$coo_content_view->set_content_template('boxes/box_categories_top.html');
$coo_content_view->set_tree_depth(1);
$coo_content_view->setCategoryId($this->category_id);
$coo_content_view->setCurrentCategoryId(0);
$t_html = $coo_content_view->get_html();
$this->set_content_data('CATEGORIES_TOP', $t_html);

// Megadropdowns
$coo_content_view = MainFactory::create_object('CategoriesMenuBoxContentView');
$coo_content_view->set_content_template('module/megadropdown.html');
$coo_content_view->set_tree_depth(1);
$coo_content_view->setCategoryId($this->category_id);

/** @var CategoriesAgent $coo_categories_agent */
$coo_categories_agent = MainFactory::create_object('CategoriesAgent', array(), true);
$t_categories_info_array = $coo_categories_agent->get_categories_info_tree(0, $_SESSION['languages_id'], 0);

$t_html = '';

for($i = 0; $i < sizeof($t_categories_info_array); $i++)
{
	$t_categories_id = $t_categories_info_array[$i]['data']['id'];
	$coo_content_view->setCurrentCategoryId($t_categories_id);
	$t_html .= $coo_content_view->get_html();
}

$this->set_content_data('CATEGORIES_DROPDOWN', $t_html);