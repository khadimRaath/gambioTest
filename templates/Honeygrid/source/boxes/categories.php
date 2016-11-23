<?php
/* --------------------------------------------------------------
   categories.php 2016-03-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$categoryId = $this->category_id;
if($this->category_id === null && is_string($this->c_path))
{
	$categoryId = array_pop(explode('_', $this->c_path));
}

if(gm_get_conf('SHOW_SUBCATEGORIES') === 'true')
{
	/** @var CategoriesMenuBoxContentView $categoriesBox */
	$categoriesBox = MainFactory::create_object('CategoriesMenuBoxContentView');
	$categoriesBox->set_content_template('boxes/box_categories_left.html');
	$categoriesBox->set_tree_depth(0);
	$categoriesBox->setCategoryId($categoryId);
	$categoriesBox->setCurrentCategoryId($categoryId);
	$categoriesBox->setCPath($this->c_path);
	$categoriesBoxHtml = $categoriesBox->get_html();

	$boxPosition = $GLOBALS['coo_template_control']->get_menubox_position('categories');
	$this->set_content_data($boxPosition, $categoriesBoxHtml);
}
elseif(gm_get_conf('CAT_MENU_LEFT') === 'true')
{
	/** @var CategoriesMenuBoxContentView $categoriesBox */
	$categoriesBox = MainFactory::create_object('CategoriesMenuBoxContentView');
	$categoriesBox->set_content_template('boxes/box_categories.html');
	$categoriesBox->set_tree_depth(100);
	$categoriesBox->setCategoryId($categoryId);
	$categoriesBox->setCurrentCategoryId(0);
	$categoriesBox->setCPath($this->c_path);
	$categoriesBox->setUnfoldLevel(gm_get_conf('CATEGORY_UNFOLD_LEVEL'));
	
	if(gm_get_conf('CATEGORY_ACCORDION_EFFECT') === 'true')
	{
		$categoriesBox->activateAccordionEffect();
	}
	else
	{
		$categoriesBox->deactivateAccordionEffect();
	}

	if(gm_get_conf('CATEGORY_DISPLAY_SHOW_ALL_LINK') === 'true')
	{
		$categoriesBox->activateDisplayShowAllLink();
	}
	else
	{
		$categoriesBox->deactivateDisplayShowAllLink();
	}
	
	$categoriesBoxHtml = $categoriesBox->get_html();

	$boxPosition = $GLOBALS['coo_template_control']->get_menubox_position('categories');
	$this->set_content_data($boxPosition, $categoriesBoxHtml);
}