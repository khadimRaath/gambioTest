<?php
/* --------------------------------------------------------------
   FeatureSetControl.inc.php 2014-07-14 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 *	Description of FeatureSetControl
 */
class FeatureSetControl
{
	protected $v_feature_set_source = null;
	
	/*
	* constructor
	*/
	public function __construct()
	{
		$this->v_feature_set_source = MainFactory::create_object('FeatureSetSource');
	}
	
	/**
	 *	Gets all features sets by a provided products_id.
	 * 
	 *	@param int $p_products_id id of the product of which the feature sets should be fetched
	 *	@return mixed[][] A hash-array with feature_set_ids as keys and int-Arrays, representing the feature value ids associated to the set, as values
	 */
	public function get_all_sets( $p_products_id )
	{
		return $this->v_feature_set_source->get_all_sets($p_products_id);
	}
	
	/**
	 *	Gets a feature set by its id
	 * 
	 *	@param int $p_feature_set_id id of the feature set that should be fetched
	 *	@return int[] An array containing the selected feature ids
	 */
	public function get_feature_set( $p_feature_set_id )
	{
		return $this->v_feature_set_source->get_feature_set($p_feature_set_id);
	}
	
	/**
	 *	Get values of a set by the set id
	 * 
	 *	@param int $p_feature_set_id id of the feature set that should be fetched
	 *	@return int[] An array containing the selected value ids
	 */
	public function get_feature_set_values( $p_feature_set_id )
	{
		return $this->v_feature_set_source->get_feature_set_values($p_feature_set_id);
	}
	
	public function get_feature_by_feature_id( $p_feature_id )
	{
		return $this->v_feature_set_source->get_feature_by_feature_id($p_feature_id);
	}
	
	/*
	 * Returns a list of features an their values which has to display when editing an existing set
	 * 
	 * @param int $p_feature_set_id id of the set which is being edited
	 * @param int $p_category_id id of the product-category which is associated with the active feature-set
	 * @return array an array containing the feature_ids, feature_name, feature_admin_names and an array with its feature_value_ids and feature_value_text
	 */
	public function get_categories_features( $p_category_id )
	{
		return $this->v_feature_set_source->get_categories_features($p_category_id);
	}
	
	/*
	 * Returns a list of features an their values which has to display when editing an existing set
	 * 
	 * @param int $p_feature_set_id id of the set which is being edited
	 * @param int $p_category_id id of the product-category which is associated with the active feature-set
	 * @return array an array containing the feature_ids, feature_name, feature_admin_names and an array with its feature_value_ids and feature_value_text
	 */
	public function get_selected_features( $p_feature_set_id )
	{
		return $this->v_feature_set_source->get_selected_features($p_feature_set_id);
	}
	
	public function get_unselected_features( $p_features_array )
	{
		return $this->v_feature_set_source->get_unselected_features($p_features_array);
	}
	
	public function get_values_by_features( $p_features_array )
	{
		return $this->v_feature_set_source->get_values_by_features($p_features_array);
	}
	
	/**
	 *	Creates or modifies a feature set with given values.
	 * 
	 *	@param $p_feature_set_id id of the set that should be updated
	 *	@param $p_products_id id of the product for which the set is created
	 *	@param $p_feature_value_array
	 *	@return int id of the inserted or updated set
	 */
	public function save_feature_set( $p_feature_set_id, $p_products_id, $p_feature_value_string )
	{
		if (preg_match("/[^\d&]/", $p_feature_value_string))
		{
			trigger_error('get_features_by_category: typeof($p_category_id) contains unexpected characters', E_USER_ERROR);
		}

		if(trim($p_feature_value_string) == '') 
		{
			$c_feature_value_array = array();
		}
		else if(strpos($p_feature_value_string, "&") === false)
		{
			$c_feature_value_array = array($p_feature_value_string);
		}
		else
		{
			$c_feature_value_array = explode("&", $p_feature_value_string);
		}
		
		$c_feature_set_id = (int)$p_feature_set_id;
				
		if(count($c_feature_value_array) == 0 && $c_feature_set_id != 0)
		{
			$this->v_feature_set_source->delete_feature_sets($c_feature_set_id);
			$t_feature_set_id = 0;
		}
		else if(count($c_feature_value_array) > 0)
		{
			$t_feature_set_id = $this->v_feature_set_source->save_feature_set($c_feature_set_id, $p_products_id, $c_feature_value_array);
		}
		else
		{
			$t_feature_set_id = 0;
		}
		
		return $t_feature_set_id;
	}
	
	/**
	 *	Copies all sets of a given product to another given product.
	 *	
	 *	@param int $p_source_products_id The ID of the owning product of the sets that should be copied.
	 *	@param int $p_target_products_id The ID of the product to which the feature sets should be copied to.
	 *	@return int[] An Array of the copied set-IDs.
	 */
	public function copy_feature_sets_by_products_id($p_source_products_id, $p_target_products_id)
	{
		$t_feature_sets = $this->v_feature_set_source->get_all_sets($p_source_products_id);
		$t_feature_set_ids = array();
		
		foreach ($t_feature_sets as $t_feature_set_id => $t_feature_set)
		{
			$t_feature_set_ids[] = $this->v_feature_set_source->save_feature_set($t_feature_set_id, $p_target_products_id, $t_feature_set);
		}
		
		return $t_feature_set_ids;
	}
	
	/**
	 *	Deletes all feature sets associated to the product with the given product id.
	 * 
	 *	@param int $p_product_id The id of the product which feature sets should be deleted
	 *	@return boolean Indicates if the deletion was successful
	 */
	public function delete_feature_sets_by_products_id($p_product_id)
	{
		$this->v_feature_set_source->delete_feature_sets_by_products_id($p_product_id);
	}
	
	/**
	 *	Deletes feature sets with the given set ids.
	 * 
	 *	@param int $p_feature_set_id The id of the set that should be deleted
	 *	@param int $p_product_id The id of the product of which the relation should be deleted
	 *	@return boolean Indicates if the deletion was successful
	 */
	public function delete_feature_sets($p_feature_set_id, $p_product_id = 0)
	{
		return $this->v_feature_set_source->delete_feature_sets($p_feature_set_id, $p_product_id);
	}
	
	/**
	 *	Finds all remaining feature values that are assigned to any products after filtering by a given list of feature value IDs as well as a given category ID.
	 *	
	 *	@param int $p_category_id A category ID that should be matched against.
	 *	@param int[] $p_feature_value_array An Array of set-IDs that should be matched against.
	 *	@return int[] An Array of all set-IDs that match the given Array of set-IDs.
	 */
	public function get_available_feature_values_by_feature_values( $p_category_id, $p_feature_value_array )
	{
		return $this->v_feature_set_source->get_available_feature_values_by_feature_values($p_category_id, $p_feature_value_array);
	}
}
