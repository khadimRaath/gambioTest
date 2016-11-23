<?php
/* --------------------------------------------------------------
   GoogleTaxonomyViewAjaxHandler.inc.php 2014-08-08 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GoogleTaxonomyViewAjaxHandler extends AjaxHandler
{

	function proceed()
	{
		$t_action_request = $this->v_data_array['action'];

		switch($t_action_request)
		{
			case 'add_products_google_category':
				$c_product_id = $this->v_data_array['POST']['products_id'];
				$c_add_array = $this->v_data_array['POST']['category_list'];
				$this->add_products_google_category($c_product_id, $c_add_array);
				break;
			case 'delete_products_google_category':
				$c_delete_array = $this->v_data_array['POST']['delete_list'];
				$this->delete_products_google_category($c_delete_array);
				break;
		}

		return true;
	}

	/**
	 * adds a google category to a product
	 *
	 * @param int $p_product_id Product ID
	 * @param string $p_products_google_categories_array Google category
	 * @return bool true
	 */
	function add_products_google_category($p_product_id, $p_products_google_categories_array)
	{
		foreach($p_products_google_categories_array as $t_google_categorie) {
			foreach($t_google_categorie as $t_key => $t_value) {
				$coo_taxonomy_control = MainFactory::create_object('GoogleTaxonomyControl');
				$coo_taxonomy_control->create_product_google_category();

				$coo_taxonomy_control->coo_product_google_category->set_products_google_categories_id($t_key);
				$coo_taxonomy_control->coo_product_google_category->set_products_id($p_product_id);
				$coo_taxonomy_control->coo_product_google_category->set_google_category($t_value);
				$coo_taxonomy_control->coo_product_google_category->save();
			}
		}

		return true;
	}

	/**
	 * delete a google category from a product
	 *
	 * @param array $p_products_google_categories_array Array with product IDs
	 * @return bool true
	 */
	function delete_products_google_category($p_products_google_categories_array)
	{
		foreach($p_products_google_categories_array as $t_products_google_categories_id) {
			$coo_taxonomy_control = MainFactory::create_object('GoogleTaxonomyControl');
			$coo_taxonomy_control->create_product_google_category();

			$coo_taxonomy_control->coo_product_google_category->set_products_google_categories_id($t_products_google_categories_id);
			$coo_taxonomy_control->coo_product_google_category->delete();
		}

		return true;
	}
}