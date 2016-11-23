<?php
/* --------------------------------------------------------------
  filter.php 2014-11-11 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

# shop language id
$t_shop_language_id = (int)$_SESSION['languages_id'];
$t_global_filter = gm_get_conf('GLOBAL_FILTER');
$t_show_filter = false;

$c_filter_categories_id = false;
if(isset($_GET['feature_categories_id']))
{
	$c_filter_categories_id = $_GET['feature_categories_id'];
}
else if(($this->category_id == 0 && strpos(strtolower(gm_get_env_info("PHP_SELF")), FILENAME_DEFAULT) !== false) || $this->category_id > 0)
{
	// startpage, category listing or product details
	$c_filter_categories_id = $this->category_id;
}

if($c_filter_categories_id !== false)
{
	$t_coo_control = MainFactory::create_object('FeatureControl');
	$t_show_filter = $t_coo_control->is_category_filter_enabled($c_filter_categories_id);
	
	if($c_filter_categories_id == 0 && gm_get_conf('STARTPAGE_FILTER_ACTIVE') == "1")
	{
		$t_show_filter = true;
	}
}

if($t_global_filter == true && gm_get_conf('STARTPAGE_FILTER_ACTIVE') == "1")
{
	if($t_show_filter == false)
	{
		$c_filter_categories_id = 0;
	}
	// global filter
	$t_show_filter = true;
}

if(isset($_GET['manufacturers_id']))
{
	$t_show_filter = false;
}

if(($t_show_filter == true || $_SESSION['style_edit_mode'] == 'edit') && $actual_products_id == '')
{
	$t_selected_feature_value_id_array = array();

	$t_feature_value_group_array = $_SESSION['coo_filter_manager']->get_feature_value_group_array();
	for($i = 0; $i < sizeof($t_feature_value_group_array); $i++)
	{
		$t_selected_feature_value_id_array = array_merge($t_selected_feature_value_id_array, $t_feature_value_group_array[$i]['FEATURE_VALUE_ID_ARRAY']);
	}

	if(isset($_GET['filter_price_min']))
		$t_price_start = $_GET['filter_price_min'];
	else
		$t_price_start = '';
	if(isset($_GET['filter_price_max']))
		$t_price_end = $_GET['filter_price_max'];
	else
		$t_price_end = '';

	$_SESSION['coo_filter_manager']->set_categories_id($c_filter_categories_id);


	$coo_content_view = MainFactory::create_object('FilterBoxContentView');
	$coo_content_view->setCategoryId($c_filter_categories_id);
	$coo_content_view->setLanguageId($t_shop_language_id);
	$coo_content_view->setSelectedValuesArray($t_selected_feature_value_id_array);
	$coo_content_view->setPriceStart($t_price_start);
	$coo_content_view->setPriceEnd($t_price_end);
	$t_html = $coo_content_view->get_html();
	$this->set_content_data($GLOBALS['coo_template_control']->get_menubox_position('filter'), $t_html);
}