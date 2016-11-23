<?php
/* --------------------------------------------------------------
  categories.php 2014-11-11 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(categories.php,v 1.23 2002/11/12); www.oscommerce.com
  (c) 2003	 nextcommerce (categories.php,v 1.10 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: categories.php 1302 2005-10-12 16:21:29Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  Enable_Disable_Categories 1.3        	Autor: Mikel Williams | mikel@ladykatcostumes.com

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */
// reset var

if(gm_get_conf('CAT_MENU_LEFT') == 'true')
{
	#menu left
	/** @var $coo_categories $coo_categories */
	$coo_categories = MainFactory::create_object('CategoriesMenuBoxContentView');
	$coo_categories->set_content_template('boxes/box_categories_left.html');
	$coo_categories->set_tree_depth(1);
	$coo_categories->setCategoryId($this->category_id);
	$coo_categories->setCurrentCategoryId(0);
	$t_box_html = $coo_categories->get_html();

	$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('categories');
	$this->set_content_data($gm_box_pos, $t_box_html);

	/** @var CategoriesSubmenusBoxContentView $coo_categories_submenus_content_view */
	$coo_categories_submenus_content_view = MainFactory::create_object('CategoriesSubmenusBoxContentView');
	$coo_categories_submenus_content_view->setCustomerStatusId($_SESSION['customers_status']['customers_status_id']);
	$coo_categories_submenus_content_view->setLanguage($_SESSION['language']);
	$coo_categories_submenus_content_view->setLanguageId($_SESSION['languages_id']);
	$coo_categories_submenus_content_view->setCurrency($_SESSION['currency']);
	$coo_categories_submenus_content_view->setCPath($this->c_path);
	$t_html = $coo_categories_submenus_content_view->get_html();
	$this->set_content_data('CATEGORIES_SUBMENUS', $t_html);
}
elseif(gm_get_conf('CAT_MENU_CLASSIC') == 'true')
{
	#classic menu left
	/** @var CategoriesBoxContentView $coo_categories */
	$coo_categories = MainFactory::create_object('CategoriesBoxContentView');
	$coo_categories->set_('c_path', $this->c_path);
	$t_box_html = $coo_categories->get_html($this->c_path);

	$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('categories');
	$this->set_content_data($gm_box_pos, $t_box_html);
}