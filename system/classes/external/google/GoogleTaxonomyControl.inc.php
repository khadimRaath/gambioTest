<?php
/* --------------------------------------------------------------
  GoogleTaxonomyControl.inc.php 2013-12-05 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2013 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class GoogleTaxonomyControl
{
	/**
	 * product google category object
	 * @var object
	 */
	var $coo_product_google_category = null;

	/**
	 * category google category object
	 * @var object
	 */
	var $coo_category_google_category = null;

	/**
	 * get array with google sub categories from given category
	 * 
	 * @param string $p_parent the parent category
	 * @return array $t_cat_array array with google categories
	 */
	function get_google_categories_array($p_parent)
	{
		$t_cat_array = array();
		$t_taxonomy_file_url = gm_get_conf('GOOGLE_TAXONOMY_FILE_PATH');

		$coo_taxonomy_source = MainFactory::create_object('GoogleTaxonomySource');
		$coo_taxonomy_source->set_taxonomy_file_url($t_taxonomy_file_url);
		$t_result = $coo_taxonomy_source->refresh_local_taxonomy_file();
		if(!$t_result)
		{
			return $t_cat_array;
		}

		$t_cat_array = $coo_taxonomy_source->get_categories_array($p_parent);

		return $t_cat_array;
	}

	/**
	 * get an object array of product to google categories
	 *
	 * @param array $p_filter_array array to filter the result
	 * @return array $t_result_array array with objects
	 */
	function get_product_google_category_array($p_filter_array)
	{
		$t_result_array = array();
		$coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('products_google_categories', $p_filter_array));
		$t_data_object_array = $coo_data_object_group->get_data_objects_array();

		foreach($t_data_object_array as $t_data_object_item)
		{
			$coo_class = MainFactory::create_object('ProductGoogleCategory');
			$coo_class->load_data_object($t_data_object_item);
			$t_result_array[] = $coo_class;
			$coo_class = NULL;
		}

		return $t_result_array;
	}

	function get_product_ids_by_category_id($p_category_id, $p_rekursion)
	{
		$coo_categories_agent = MainFactory::create_object('CategoriesAgent');
		$t_products_ids_array = $coo_categories_agent->get_products_ids_array($p_category_id, $p_rekursion);

		return $t_products_ids_array;
	}

	/**
	 * initialized an product to google categories object
	 *
	 * @return bool true
	 */
	function create_product_google_category()
	{
		$this->coo_product_google_category = MainFactory::create_object('ProductGoogleCategory');
		return true;
	}

	/**
	 * initialized an category to google categories object
	 *
	 * @return bool true
	 */
	function create_category_google_category()
	{
		$this->coo_category_google_category = MainFactory::create_object('CategoryGoogleCategory');
		return true;
	}
}