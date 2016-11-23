<?php
/* --------------------------------------------------------------
   FeatureSetSource.inc.php 2016-05-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Description of FeatureSetSource
 */
class FeatureSetSource
{	
	/**
	 *	Constructor 
	 */
	public function __construct() { }
	
	/**
	 *	Returns all features sets by a provided products_id.
	 * 
	 *	@param int $p_products_id id of the product of which the feature sets should be fetched
	 *	@return mixed[][] A hash-array with feature_set_ids as keys and int-Arrays, representing the feature value ids associated to the set, as values
	 */
	public function get_all_sets( $p_products_id )
	{
		$c_products_id = (int)$p_products_id;
        if(empty($c_products_id))
		{
			trigger_error('get_all_sets: typeof($p_products_id) != integer', E_USER_ERROR);
		}

		$coo_data_object = MainFactory::create_object('GMDataObjectGroup', array('feature_set_to_products', array('products_id' => $c_products_id)));
		$t_data_objects = $coo_data_object->get_data_objects_array();
		
		$t_set_ids = array();
		foreach($t_data_objects as $t_data_object)
		{
			$t_set_ids[] = $t_data_object->get_data_value('feature_set_id');
		}
		
		$t_sets = array();
		if(count($t_set_ids) > 0)
		{
			$coo_data_object = MainFactory::create_object('GMDataObjectGroup', array('feature_index', array('feature_set_id' => $t_set_ids), array('feature_set_id DESC')));
			$t_data_objects = $coo_data_object->get_data_objects_array();

			foreach($t_data_objects as $t_data_object)
			{
				$t_feature_set_id = $t_data_object->get_data_value('feature_set_id');
				$tmp_set = $this->convert_to_feature_value_array($t_data_object->get_data_value('feature_value_index'));

				$t_sql = '
					SELECT fv.feature_id, fd.feature_name, fd.feature_admin_name, fv.feature_value_id, fvd.feature_value_text FROM feature_value AS fv
					LEFT JOIN feature_description AS fd ON fv.feature_id = fd.feature_id
					LEFT JOIN feature_value_description AS fvd ON fv.feature_value_id = fvd.feature_value_id
					WHERE fv.feature_value_id IN ('.implode(',', $tmp_set).')
						AND fd.language_id = '.$_SESSION['languages_id'].'
						AND fvd.language_id = '.$_SESSION['languages_id'].'
					ORDER BY fv.feature_id, fv.sort_order, fvd.feature_value_text
				';
				//echo $t_sql;

				if (empty($tmp_set) == false)
				{
					$t_result = xtc_db_query($t_sql);
					while($t_row = xtc_db_fetch_array($t_result))
					{
						$t_sets[$t_feature_set_id][$t_row['feature_id']]['feature_name'] = $t_row['feature_name'];
						$t_sets[$t_feature_set_id][$t_row['feature_id']]['feature_admin_name'] = $t_row['feature_admin_name'];
						$t_sets[$t_feature_set_id][$t_row['feature_id']]['feature_values'][$t_row['feature_value_id']] = $t_row['feature_value_text'];
					}
				}
			}
		}
		
		return $t_sets;
	}
	
	/**
	 *	Returns a feature set by its id
	 *
	 *	@param int $p_feature_set_id id of the feature set that should be fetched
	 *	@return int[] An array containing the selected feature ids
	 */
	public function get_feature_set( $p_feature_set_id )
	{
		$c_feature_set_id = (int)$p_feature_set_id;
		if (empty($c_feature_set_id))
		{
			trigger_error('get_set: typeof($p_feature_set_id) != integer', E_USER_ERROR);
		}

		$coo_feature_index = MainFactory::create_object('GMDataObject', array('feature_index'));
		$coo_feature_index->set_keys(array('feature_set_id' => $c_feature_set_id));
		$coo_feature_index->init();
		
		$t_feature_value_array = $this->convert_to_feature_value_array($coo_feature_index->get_data_value('feature_value_index'));

		$t_sql = '
			SELECT fv.feature_id, fd.feature_name, fd.feature_admin_name, fv.feature_value_id, fvd.feature_value_text FROM feature_value AS fv
			LEFT JOIN feature_description AS fd ON fv.feature_id = fd.feature_id
			LEFT JOIN feature_value_description AS fvd ON fv.feature_value_id = fvd.feature_value_id
			WHERE fv.feature_value_id IN ('.implode(',', $t_feature_value_array).')
				AND fd.language_id = '.$_SESSION['languages_id'].'
				AND fvd.language_id = '.$_SESSION['languages_id'].'
			ORDER BY fv.feature_id, fv.sort_order, fvd.feature_value_text
		';
		//echo $t_sql;

		$t_set = array();
		if(!empty($t_feature_value_array))
		{
			$t_result = xtc_db_query($t_sql);
			while($t_row = xtc_db_fetch_array($t_result))
			{
				$t_set[$t_row['feature_id']]['feature_name'] = $t_row['feature_name'];
				$t_set[$t_row['feature_id']]['feature_admin_name'] = $t_row['feature_admin_name'];
				$t_set[$t_row['feature_id']]['feature_values'][$t_row['feature_value_id']] = $t_row['feature_value_text'];
			}
		}
		
		return $t_set;
	}
	
	/**
	 *	Gets feature_values by set id
	 * 
	 *	@param int $p_feature_set_id id of the feature set that should be fetched
	 *	@return int[] An array containing the selected feature_value ids
	 */
	public function get_feature_set_values( $p_feature_set_id )
	{
		$c_feature_set_id = (int)$p_feature_set_id;
        if(empty($c_feature_set_id)) trigger_error('get_set: typeof($p_feature_set_id) != integer', E_USER_ERROR);
		
		$coo_feature_set_source = MainFactory::create_object('GMDataObject', array('feature_index'));
		$coo_feature_set_source->set_keys(array('feature_set_id' => $c_feature_set_id));
		$coo_feature_set_source->init();
		
		$t_feature_value_array = $this->convert_to_feature_value_array($coo_feature_set_source->get_data_value('feature_value_index'));
		
		return $t_feature_value_array;
	}
	
	public function get_feature_by_feature_id( $p_feature_id )
	{
		$c_feature_id = (int)$p_feature_id;
		$t_sql = '
				SELECT fv.feature_id, fd.feature_name, fd.feature_admin_name, fv.feature_value_id, fvd.feature_value_text FROM feature_value AS fv
				LEFT JOIN feature_description AS fd ON fv.feature_id = fd.feature_id
				LEFT JOIN feature_value_description AS fvd ON fv.feature_value_id = fvd.feature_value_id
				WHERE fd.feature_id = ' . $c_feature_id . '
					AND fd.language_id = '.$_SESSION['languages_id'].'
					AND fvd.language_id = '.$_SESSION['languages_id'].'
				ORDER BY fv.feature_id, fv.sort_order, fvd.feature_value_text
				';
		
		$t_result = xtc_db_query($t_sql);
		$t_features_array = array();
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_features_array[$t_row['feature_id']]['feature_values'][$t_row['feature_value_id']] = $t_row['feature_value_text'];
			$t_features_array[$t_row['feature_id']]['feature_name'] = $t_row['feature_name'];
			$t_features_array[$t_row['feature_id']]['feature_admin_name'] = $t_row['feature_admin_name'];
		}
		
		return $t_features_array;
	}
	
	/*
	 * Returns all possible filter-features for a given category
	 * 
	 * @param int $p_category_id the id of the category of a product
	 * @return array an array containing the feature_ids, feature_names and the feature_admin_names
	 */
	public function get_categories_features( $p_category_path )
	{
		if (trim($p_category_path) == '' || preg_match("/[^\d_]/", $p_category_path))
		{
			trigger_error('get_features_by_category: typeof($p_category_id) contains unexpected characters', E_USER_ERROR);
		}

		$t_categories_array = explode('_', $p_category_path);
		$t_categories_array[] = 0;
		
		$t_sql = '
				SELECT * FROM categories_filter AS cf 
				LEFT JOIN feature_description AS fd ON cf.feature_id=fd.feature_id 
				WHERE cf.categories_id IN ('.implode(',', $t_categories_array).')
				AND language_id = ' . $_SESSION['languages_id'] . '
				ORDER BY fd.feature_admin_name, fd.feature_name;
				';

		$t_category_feature_list = array();
		$t_result = xtc_db_query($t_sql);
		while($t_row = xtc_db_fetch_array($t_result))
		{
				$t_category_feature_list[$t_row['feature_id']]['feature_name'] = $t_row['feature_name'];
				$t_category_feature_list[$t_row['feature_id']]['feature_admin_name'] = $t_row['feature_admin_name'];
		}
		
		return $t_category_feature_list;
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
		$c_feature_set_id = (int)$p_feature_set_id;

		$t_selected_features_list = array();
		if(!empty($c_feature_set_id))
		{
			$coo_feature_set_source = MainFactory::create_object('GMDataObject', array('feature_index'));
			$coo_feature_set_source->set_keys(array('feature_set_id' => $c_feature_set_id));
			$coo_feature_set_source->init();
			$t_feature_value_array = $this->convert_to_feature_value_array($coo_feature_set_source->get_data_value('feature_value_index'));
			$t_selected_features_list = $this->get_features_by_selected_values($t_feature_value_array);
		}
		
		return $t_selected_features_list;
	}
	
	/*
	 * Returns all filter-features for the given array of feature_values
	 * 
	 * @param array an array with the feature_ids, feature_names and the feature_admin_names
	 * @return array an array containing the feature_ids, feature_names and the feature_admin_names
	 */
	protected function get_features_by_selected_values( $p_values_array )
	{
		if(!is_array($p_values_array)) trigger_error ('get_features_by_selected_values: typeof($p_values_array != array', E_USER_ERROR);
		
		$c_values_array = array_map('intval', $p_values_array);
		
		$t_sql = '
				SELECT fv.feature_id, fd.feature_name, fd.feature_admin_name
				FROM feature_value AS fv
				LEFT JOIN feature_description AS fd ON fv.feature_id = fd.feature_id
				WHERE fv.feature_value_id IN ('.implode(',', $c_values_array).')
				ORDER BY fd.feature_admin_name, fd.feature_name;
				';
		
		$t_feature_array = array();
		$t_result = xtc_db_query($t_sql);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_feature_array[$t_row['feature_id']]['feature_name'] = $t_row['feature_name'];
			$t_feature_array[$t_row['feature_id']]['feature_admin_name'] = $t_row['feature_admin_name'];
		}
		
		return $t_feature_array;
	}
	
	public function get_unselected_features( $p_features_array )
	{
		if(count($p_features_array) == 0)
		{
			$p_features_array[] = 0;
		}
		
		$c_features_array = array_map('intval', $p_features_array);
		
		$t_sql = '
            SELECT f.feature_id, fd.feature_name, fd.feature_admin_name FROM feature AS f
				LEFT JOIN feature_description AS fd ON f.feature_id = fd.feature_id
				LEFT JOIN feature_value AS fv ON f.feature_id = fv.feature_id
				WHERE f.feature_id NOT IN ('.implode(',', $c_features_array).')
					AND fd.language_id = '.$_SESSION['languages_id'].'
            GROUP BY 
                f.feature_id
            HAVING 
                count(fv.feature_value_id) != 0
			ORDER BY fd.feature_admin_name, fd.feature_name
        ';
		
		$t_unselected_features = array();
		
		if (!empty($c_features_array)) {
			$t_result = xtc_db_query($t_sql);
			while($t_row = xtc_db_fetch_array($t_result))
			{
				$t_unselected_features[$t_row['feature_id']]['feature_name'] = $t_row['feature_name'];
				$t_unselected_features[$t_row['feature_id']]['feature_admin_name'] = $t_row['feature_admin_name'];
			}
		}
		
		return $t_unselected_features;
	}
	
	/*
	 * A function to get all feature-values of the given feature_ids
	 * 
	 * @param array an array containing the feature_ids, feature_names and the feature_admin_names
	 * @return array an array containing the feature_ids, feature_name, feature_admin_names and an array with its feature_value_ids and feature_value_text
	 */
	public function get_values_by_features( $p_features_array )
	{
		$c_features_array = array_map('intval', $p_features_array);
		
		$t_sql = '
				SELECT fv.feature_id, fvd.feature_value_id, fvd.feature_value_text
				FROM feature_value AS fv
				LEFT JOIN feature_value_description AS fvd ON fv.feature_value_id = fvd.feature_value_id
				WHERE fv.feature_id IN ('.implode(',', array_map('intval', array_keys($c_features_array))).')
				AND fvd.language_id = "' . $_SESSION['languages_id'] . '"
				ORDER BY fv.feature_id, fv.sort_order, fvd.feature_value_text;
				';
		
		$t_result = xtc_db_query($t_sql);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$p_features_array[$t_row['feature_id']]['feature_values'][$t_row['feature_value_id']] = $t_row['feature_value_text'];
		}
		
		return $p_features_array;
	}
		
	/**
	 *	Creates or modifies a feature set with given values.
	 * 
	 *	@param $p_feature_set_id id of the set that should be updated
	 *	@param $p_products_id id of the product for which the set is created
	 *	@param $p_feature_value_array
	 *	@return int id of the inserted or updated set
	 */
	public function save_feature_set($p_feature_set_id, $p_products_id, $p_feature_value_array)
	{
		$c_products_id = (int)$p_products_id;
		$c_feature_set_id = (int)$this->get_feature_set_id_if_exists($p_feature_value_array);
		$c_old_feature_set_id = (int)$p_feature_set_id;
        if(empty($c_products_id))
		{
			trigger_error('save_set: typeof($p_products_id) != integer', E_USER_ERROR);
		}
		
		if($this->feature_set_reference_exists($c_feature_set_id, $c_products_id))
		{
			return -1;
		}
		
		//Produkt-Referenz entfernen
		$coo_delete_set_reference = MainFactory::create_object('GMDataObject', array('feature_set_to_products'));
		$coo_delete_set_reference->set_keys(array('feature_set_id' => $c_old_feature_set_id, 'products_id' => $c_products_id));
		$coo_delete_set_reference->delete();
		
		//Kontroll-Flags setzen
		$t_is_update = $c_old_feature_set_id > 0;
		$t_feature_set_exists = empty($c_feature_set_id) == false;
		
		//Neues Set anlegen
		if($t_feature_set_exists == false)
		{
			$coo_set = MainFactory::create_object('GMDataObject', array('feature_set'));
			$coo_set->set_data_value('feature_set_id', '');
			$c_feature_set_id = (int)$coo_set->save_body_data();
			$this->save_feature_set_values($c_feature_set_id, $p_feature_value_array);
			$this->generate_feature_set_index($c_feature_set_id);
		}
		
		if($t_is_update && $this->feature_set_still_in_use($c_old_feature_set_id) == false)
		{
			$this->delete_feature_sets($c_old_feature_set_id);
		}
		
		//Produkt-Referenz erzeugen
		$coo_set_reference = MainFactory::create_object('GMDataObject', array('feature_set_to_products', array('feature_set_id' => $c_feature_set_id, 'products_id' => $c_products_id)));
		if($coo_set_reference->get_result_count() == 0)
		{
			$coo_set_reference->set_keys(array());
			$coo_set_reference->set_data_value('feature_set_id', $c_feature_set_id);
			$coo_set_reference->set_data_value('products_id', $c_products_id);
			$coo_set_reference->save_body_data();
		}
		
		return $c_feature_set_id;
	}
	
	protected function feature_set_reference_exists($p_feature_set_id, $p_products_id)
	{
		$c_feature_set_id = (int)$p_feature_set_id;
		$c_products_id = (int)$p_products_id;
		if(empty($c_feature_set_id) || empty($c_products_id))
		{
			return false;
		}
		$coo_data_object = MainFactory::create_object('GMDataObject', array('feature_set_to_products', array('feature_set_id' => $c_feature_set_id, 'products_id' => $c_products_id)));
		if($coo_data_object->get_result_count() == 0)
		{
			return false;
		}
		return true;
	}
	
	protected function get_feature_set_id_if_exists($p_feature_value_array)
	{
		if(empty($p_feature_value_array) || is_array($p_feature_value_array) == false)
		{
			return false;
		}
		sort($p_feature_value_array);
		$coo_data_object = MainFactory::create_object('GMDataObject', array('feature_index', array('feature_value_index' => '-' . implode('--', $p_feature_value_array) . '-')));
		if($coo_data_object->get_result_count() == 0)
		{
			return false;
		}
		$t_feature_set_id = $coo_data_object->get_data_value('feature_set_id');
		
		return $t_feature_set_id;
	}
	
	protected function save_feature_set_values($p_feature_set_id, $p_feature_value_array)
	{		
		$c_feature_set_id = (int)$p_feature_set_id;
		
		$coo_data_object = MainFactory::create_object('GMDataObject', array('feature_set_values'));
		$coo_data_object->set_data_value('feature_set_id', $c_feature_set_id);
		
		foreach($p_feature_value_array as $t_feature_value_id)
		{
			$coo_data_object->set_data_value('feature_value_id', $t_feature_value_id);
			$coo_data_object->save_body_data();
		}

		return true;
	}
		
	/**
	 *	Converts a feature value index string to an int-array containing feature value ids
	 * 
	 *	@param string $p_feature_value_index the string representing feature value ids
	 *	@return int[] a converted int-array containing feature value ids
	 */
	protected function convert_to_feature_value_array($p_feature_value_index)
	{
		if (!is_string($p_feature_value_index))
		{
			trigger_error('convert_to_feature_value_array: typeof($p_feature_value_index != string', E_USER_ERROR);
		}

		preg_match_all('/-(\d+)-/', $p_feature_value_index, $t_match);
		return $t_match[1];
	}
	
	function generate_feature_set_index($p_feature_set_id)
	{
		$c_feature_set_id = (int)$p_feature_set_id;
        if(empty($c_feature_set_id))
		{
			trigger_error('generate_feature_set_index: typeof($p_feature_set_id) != integer', E_USER_ERROR);
		}
		
		// get all feature_values from feature_set_id $t_feature_set_id
		$coo_feature_set_values = MainFactory::create_object('GMDataObjectGroup', array('feature_set_values', array('feature_set_id' => $c_feature_set_id), array('feature_value_id')));
		$t_feature_set_values_array = $coo_feature_set_values->get_data_objects_array();
		$t_feature_values_array = array();

		// iterate & temporary save feature_values
		foreach($t_feature_set_values_array as $coo_feature_set_value)
		{
			$t_feature_values_array[] = $coo_feature_set_value->get_data_value('feature_value_id');
		}
		
		if(empty($t_feature_values_array))
		{
			return true;
		}
		
		// convert feature_values to feature_value_string
		$t_feature_values_string = '-' . implode('--', $t_feature_values_array) . '-';
		
		// write new feature_index data set
		$coo_feature_index = MainFactory::create_object('GMDataObject', array('feature_index'));
		$coo_feature_index->set_data_value('feature_set_id', $c_feature_set_id);
		$coo_feature_index->set_data_value('feature_value_index', $t_feature_values_string);
		$t_new_feature_set_id = $coo_feature_index->save_body_data();
		
		$t_success = $c_feature_set_id == $t_new_feature_set_id;
		
		return $t_success;
	}
	
	function build_feature_set_index($p_feature_set_id)
	{		
		$c_feature_set_id = (int)$p_feature_set_id;
        if (empty($c_feature_set_id))
		{
			trigger_error('build_feature_set_index: typeof($p_feature_set_id) != integer', E_USER_ERROR);
		}

		// reset feature_value_index for product_id $c_products_id
		$t_sql = 'DELETE FROM feature_index WHERE feature_set_id = ' . $c_feature_set_id;
		xtc_db_query($t_sql);
		
		$t_success = $this->generate_feature_set_index($c_feature_set_id);
		
		return $t_success;
	}
	
	public function get_available_feature_values_by_feature_values($p_category_id, $p_feature_values)
	{
		$t_head_selection = array();
		
		if (!is_array($p_feature_values))
		{
			$t_feature_values_array = $this->split_feature_values_string($p_feature_values);
		}
		else
		{
			$t_feature_values_array = $p_feature_values;
		}
		
		$t_plain_feature_values = array();
		$t_max_recursion_for_selection_preservation = 1000;
		$t_features = array();
		$_SESSION['filter_history_category'] = $p_category_id;
		
		foreach ($t_feature_values_array as $t_feature => $t_values)
		{
			$t_plain_feature_values = array_merge($t_plain_feature_values, $t_values);
			foreach ($t_values as $t_value)
			{
				$t_features[(int)$t_value] = (int)$t_feature;
			}
		}
		$t_plain_feature_values = array_unique($t_plain_feature_values);
		
		if (!isset($_SESSION['filter_history']))
		{
			$_SESSION['filter_history'] = array();
		}
		
		$t_additional_selection = array_diff($t_plain_feature_values, $_SESSION['filter_history']);
		$t_reduced_selection = array_diff($_SESSION['filter_history'], $t_plain_feature_values);
		
		$t_change_index = 0;
		$t_deselection_flag = false;
		
		if (empty($t_reduced_selection))
		{
			if (!empty($t_additional_selection))
			{
				$_SESSION['filter_history'] = array_merge($_SESSION['filter_history'], $t_additional_selection);
			}
			$t_head_selection = $_SESSION['filter_history'];
			$t_tail_selection = array();
		}
		else
		{
			$t_deselection_flag = true;
			$t_temp = array_flip($_SESSION['filter_history']);
			$t_change_index = $t_temp[array_shift($t_reduced_selection)];
			
			$t_head_selection = array_slice($_SESSION['filter_history'], 0, $t_change_index);
			$t_tail_selection = array_slice($_SESSION['filter_history'], $t_change_index + 1);
			$t_tail_selection = array_diff($t_tail_selection, $t_reduced_selection);
			
			$_SESSION['filter_history'] = array_intersect($_SESSION['filter_history'], $t_plain_feature_values);
			
			if (!empty($t_additional_selection))
			{
				$t_head_selection = array_merge($t_head_selection, $t_additional_selection);
				$_SESSION['filter_history'] = array_merge($_SESSION['filter_history'], $t_additional_selection);
			}
		}
		
		$coo_data_object = MainFactory::create_object('GMDataObjectGroup', array('categories_filter', array('categories_id' => $p_category_id), array('sort_order', 'feature_id')));
		$t_value_conjunctions = $this->get_value_conjunctions($coo_data_object);
		
		$t_recursion_counter = 0;
		$t_intersect_empty = false;
		
		$t_at_least_once = true;
		
		while ($t_at_least_once)
		{
			if (empty($t_head_selection) && !empty($t_tail_selection))
			{
				$t_head_selection[] = array_shift($t_tail_selection);
			}
			
			if (empty($t_tail_selection) || $t_intersect_empty || $t_recursion_counter > $t_max_recursion_for_selection_preservation)
			{
				$t_at_least_once = false;
			}
			$t_feature_values_array = $this->build_feature_values_array($t_head_selection, $t_features);
			$t_recursion_counter++;
			
			$t_where_for_sets = $this->get_where_for_sets($t_feature_values_array, $t_value_conjunctions);
			$t_where_for_features = $this->get_where_for_features($t_feature_values_array, $t_value_conjunctions);
			
			$t_available_values_from_sets = $this->get_values_from_where_strings($p_category_id, $t_where_for_sets);
			$t_available_values_from_features = $this->get_values_from_where_strings($p_category_id, $t_where_for_features, array_keys($t_feature_values_array), true);

			$t_available_values = array_merge($t_available_values_from_sets, $t_available_values_from_features);
			$t_available_values = array_unique($t_available_values);
			sort($t_available_values);
			
			$t_intersection = array_intersect($t_available_values, $t_tail_selection);
			$t_intersect_empty = empty($t_intersection);
			
			if (!$t_intersect_empty)
			{
				$t_head_selection = array_merge($t_head_selection, $t_intersection);
				foreach ($t_intersection as $t_element)
				{
					unset($t_tail_selection[array_search($t_element, $t_tail_selection)]);
					// repair index
					$t_tail_selection = array_values($t_tail_selection);
				}
			}
		}
		
		if ($t_deselection_flag)
		{
			$t_history_sorted_values = array();
			
			foreach ($_SESSION['filter_history'] as $t_history_element)
			{
				if (in_array($t_history_element, $t_available_values))
				{
					$t_history_sorted_values[]  = $t_history_element;
				}
			}
			
			$_SESSION['filter_history'] = $t_history_sorted_values;
		}
		
		return $t_available_values;
	}
	
	public function build_features_array( $p_feature_values )
	{
		$t_features_array = array();
		$coo_feature_values = MainFactory::create_object('GMDataObjectGroup', array('feature_value', array('feature_value_id' => $p_feature_values)));
		$t_coo_feature_values_array = $coo_feature_values->get_data_objects_array();
		
		foreach ($t_coo_feature_values_array as $t_feature_value_object)
		{
			if (!isset($t_features_array[$t_feature_value_object->v_table_content['feature_value_id']]))
			{
				$t_features_array[$t_feature_value_object->v_table_content['feature_value_id']] = array();
			}
			
			$t_features_array[$t_feature_value_object->v_table_content['feature_value_id']] = $t_feature_value_object->v_table_content['feature_id'];
		}
		
		return $t_features_array;
	}
	
	public function build_feature_values_array( $p_feature_values, &$p_features )
	{
		$t_feature_values_array = array();
		if( is_array( $p_feature_values ) && count( $p_feature_values ) > 0 )
		{
			$p_feature_values = array_diff( $p_feature_values, array('') );

			foreach ( $p_feature_values as $t_value )
			{
				if (!isset($t_feature_values_array[(int)$p_features[$t_value]])) {
					$t_feature_values_array[(int)$p_features[$t_value]] = array();
				}
				$t_feature_values_array[(int)$p_features[$t_value]][] = (int)$t_value;
			}
		}
		return $t_feature_values_array;
	}
	
	protected function get_value_conjunctions( &$p_coo_data_object )
	{
		$t_categories_filter = $p_coo_data_object->get_data_objects_array();
		$t_value_conjunctions = array();
		
		foreach ($t_categories_filter as $t_filter)
		{
			$t_value_conjunctions[$t_filter->v_table_content['feature_id']] = $t_filter->v_table_content['value_conjunction'];
		}
		
		return $t_value_conjunctions;
	}
	
	public function split_feature_values_string( $p_feature_values_string )
	{
		$t_feature_values_array = array();
		
		$t_features = explode('&', $p_feature_values_string);
		foreach ($t_features as $t_feature_values)
		{
			$t_feature_id = substr_wrapper($t_feature_values, 0, strpos_wrapper($t_feature_values, ':'));
			$t_feature_values = substr_wrapper($t_feature_values, strpos_wrapper($t_feature_values, ':') + 1);
			$t_feature_values = explode('|', $t_feature_values);
			sort($t_feature_values);
			$t_feature_values_array[(int)$t_feature_id] = $t_feature_values;
		}
		
		return $t_feature_values_array;
	}
	
	public function extract_features_from_feature_values_string( $p_feature_values_string )
	{
		$t_feature_array = array();
		
		$t_features = explode('&', $p_feature_values_string);
		foreach ($t_features as $t_feature_values)
		{
			$t_feature_array[] = substr_wrapper($t_feature_values, 0, strpos_wrapper($t_feature_values, ':'));		
		}
		
		return $t_feature_array;
	}
	
	public function extract_values_from_feature_values_string( $p_feature_values_string, $p_allowed_features = false )
	{
		$t_feature_values_array = array();
		
		$t_features = explode('&', $p_feature_values_string);
		foreach ($t_features as $t_feature_values)
		{
			$t_feature_id = substr_wrapper($t_feature_values, 0, strpos_wrapper($t_feature_values, ':'));
			$t_feature_values = substr_wrapper($t_feature_values, strpos_wrapper($t_feature_values, ':') + 1);
			$t_feature_values = explode('|', $t_feature_values);
			
			if( is_array($p_allowed_features) && !in_array($t_feature_id, $p_allowed_features) )
			{
				break;
			}
			foreach ($t_feature_values as $t_feature_value)
			{
				$t_feature_values_array[] = $t_feature_value;
			}
		}
		
		return $t_feature_values_array;
	}
	
	public function convert_values_array_to_feature_values_array($p_values_array)
	{
		$t_feature_values_array = array();
		
		$c_values_array = array_map('intval', $p_values_array);
		
		$t_sql = 'SELECT * FROM feature_value WHERE feature_value_id IN ("' . implode( '","', $c_values_array ) . '") ORDER BY sort_order, feature_value_id';
		$t_result = xtc_db_query($t_sql);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_feature_id = $t_row['feature_id'];
			$t_feature_value_id = $t_row['feature_value_id'];
			if(array_key_exists($t_feature_id, $t_feature_values_array) == false)
			{
				$t_feature_values_array[$t_feature_id] = array();
			}
			$t_feature_values_array[$t_feature_id][] = $t_feature_value_id;
		}
		
		return $t_feature_values_array;
	}
	
	//horizontal
	protected function get_where_for_sets( $p_feature_values, &$p_value_conjunctions )
	{
		$t_where_strings = array();
		
		foreach ($p_feature_values as $t_feature_id => $t_feature_values)
		{
			$t_where_strings[] = $this->build_where_string($t_feature_values, $p_value_conjunctions[$t_feature_id]);
		}
		
		return $t_where_strings;
	}
	
	//vertical
	protected function get_where_for_features( $p_feature_values, &$p_value_conjunctions )
	{
		$t_where_strings = array();
		
		foreach ($p_feature_values as $t_skip_feature_id => $t_skip_feature_values)
		{
			$t_where_parts = array();
			
			foreach ($p_feature_values as $t_feature_id => $t_feature_values)
			{
				if($p_value_conjunctions[$t_feature_id] == 0 && $t_feature_id == $t_skip_feature_id)
				{
					continue;
				}
				$t_where_parts[] = $this->build_where_string($t_feature_values, $p_value_conjunctions[$t_feature_id]);
			}
			
			$t_where_string = "((";
			if (count($t_where_parts) > 0)
			{
				$t_where_string .= implode(" AND ", $t_where_parts) . " AND ";
			}
			$t_where_string .= "fv.feature_id = " . $t_skip_feature_id . ") OR fv.feature_value_id IN ( " . implode(",", $t_skip_feature_values) . " ))";
			
			$t_where_strings[] = $t_where_string;
		}
		
		return $t_where_strings;
	}
	
	protected function build_where_string( $p_feature_values, $p_value_conjunction )
	{
		sort($p_feature_values);
		$t_where = "fi.feature_value_index";
		
		if ($p_value_conjunction)
		{
			$t_where .= " LIKE '%-" . implode("-%-", $p_feature_values) . "-%'";
		}
		else
		{
			$t_where .= " REGEXP ('-" . implode("-|-", $p_feature_values) . "-')";
		}
		
		return $t_where;
	}
	
	//vertikal
	public function get_values_from_where_strings( $p_category_id, $p_where_strings, $p_features_ids=array(), $p_filter_by_feature=false )
	{
		$t_query = 'SELECT * FROM ';
		
		$c_category_id = (int)$p_category_id;
		$c_features_ids = array_map('intval', $p_features_ids);

		$t_include_sub_categories = true;
		$t_show_cat_sub_products        = xtc_db_query('SELECT `show_sub_products` FROM `categories` WHERE `categories_id` = '
		                                               . $c_category_id);
		if(xtc_db_num_rows($t_show_cat_sub_products))
		{
			$t_row = xtc_db_fetch_array($t_show_cat_sub_products);
			$t_show_cat_sub_products_result = (int)$t_row['show_sub_products'];

			$t_include_sub_categories = ($t_show_cat_sub_products_result === 1) ? true : false;
		}
		
		if($t_include_sub_categories)
		{
			$t_product_finder = MainFactory::create_object('IndexFeatureProductFinder');
			$t_categories_where_part = $t_product_finder->get_categories_index_search_string(array($c_category_id));
			
			$t_query = 'SELECT 
							p.products_id
						FROM 
							categories_index AS ci
							LEFT JOIN products AS p ON (ci.products_id = p.products_id)
						WHERE 
							p.products_status = "1" AND
							categories_index LIKE "' . $t_categories_where_part . '"';
		}
		else
		{
			$t_query = 'SELECT 
							p.products_id
						FROM 
							products_to_categories AS ptc
							LEFT JOIN products AS p ON (ptc.products_id = p.products_id)
						WHERE 
							p.products_status = "1" AND
							ptc.categories_id = ' . $c_category_id;
		}
		
		$t_result = xtc_db_query($t_query);
		
		$t_products_ids_array = array();
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_products_ids_array[] = $t_row['products_id'];
		}
		
		$t_feature_values = array();
		if(count($t_products_ids_array) > 0)
		{
			$t_products_ids_chunk_array = array_chunk($t_products_ids_array, 200);
			$t_where = implode($p_filter_by_feature ? " OR " : " AND ", $p_where_strings);
			
			foreach($t_products_ids_chunk_array as $t_products_ids_chunk)
			{
				$t_query = "SELECT DISTINCT
								fsv.feature_value_id
							FROM
								feature_set_values AS fsv
								LEFT JOIN feature_set AS fs ON (fsv.feature_set_id = fs.feature_set_id)
								LEFT JOIN feature_set_to_products AS fstp ON (fstp.feature_set_id = fs.feature_set_id)
								LEFT JOIN feature_index AS fi ON (fs.feature_set_id = fi.feature_set_id)
								LEFT JOIN feature_value AS fv ON (fv.feature_value_id = fsv.feature_value_id)     
							WHERE
								fstp.products_id IN ('" . implode("','", $t_products_ids_chunk) . "') " .
								(!empty($t_where) ? " AND (" . $t_where . ")" : "") . ($p_filter_by_feature && !empty($c_features_ids) ? " AND fv.feature_id IN (" . implode(",", $c_features_ids) . ")" : "") . " 
							ORDER BY
								fsv.feature_value_id";

				$t_result = xtc_db_query($t_query);

				while ($t_row = xtc_db_fetch_array($t_result))
				{
					$t_feature_values[] = $t_row['feature_value_id'];
				}
			}
		}
		
		return $t_feature_values;
	}
	
	protected function get_all_values_from_feature($p_feature_id)
	{
		$t_values_array = array();
		
		$t_sql = '
				SELECT feature_value_id
				FROM feature_value
				WHERE feature_id = ' . (int)$p_feature_id . ';
				';
		
		$t_result = xtc_db_query($t_sql);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_values_array[] = $t_row['feature_value_id'];
		}
		
		return $t_values_array;
	}
	
	protected function check_int_param($p_id)
	{
		if(is_array($p_id))
		{
			return $p_id;
		}
		else if(is_int($p_id))
		{
			return array($p_id);
		}
		else
		{
			trigger_error('check_int_param: typeof($p_id) != integer/array', E_USER_ERROR);
		}
	}
	
	public function delete_feature_sets($p_feature_set_ids_array, $p_product_id = 0)
	{
		$c_feature_set_ids_array = $this->check_int_param($p_feature_set_ids_array);
		$c_product_id = (int)$p_product_id;
		
		foreach($c_feature_set_ids_array as $t_feature_set_id)
		{
			$c_feature_set_id = (int)$t_feature_set_id;
			$coo_feature_set = MainFactory::create_object('GMDataObject', array('feature_set_to_products'));
			$t_keys_array = array('feature_set_id' => $c_feature_set_id);
			if($c_product_id != 0)
			{
				$t_keys_array['products_id'] = $c_product_id;
			}
			$coo_feature_set->set_keys($t_keys_array);
			$coo_feature_set->delete();

			if($c_product_id == 0)
			{
				$t_feature_set_still_in_use = false;
			}
			else
			{
				$t_feature_set_still_in_use = $this->feature_set_still_in_use($c_feature_set_id);
			}
			
			if($t_feature_set_still_in_use == false)
			{
				$coo_feature_set = MainFactory::create_object('GMDataObject', array('feature_set'));
				$coo_feature_set->set_keys(array('feature_set_id' => $c_feature_set_id));
				$coo_feature_set->delete();

				$coo_feature_set = MainFactory::create_object('GMDataObject', array('feature_set_values'));
				$coo_feature_set->set_keys(array('feature_set_id' => $c_feature_set_id));
				$coo_feature_set->delete();

				$coo_feature_set = MainFactory::create_object('GMDataObject', array('feature_index'));
				$coo_feature_set->set_keys(array('feature_set_id' => $c_feature_set_id));
				$coo_feature_set->delete();
			}
		}
		
		return true;
	}
	
	public function feature_set_still_in_use($p_feature_set_id)
	{
		$c_feature_set_id = (int)$p_feature_set_id;
		$coo_feature_set = MainFactory::create_object('GMDataObjectGroup', array('feature_set_to_products', array('feature_set_id' => $c_feature_set_id)));
		$t_still_in_use = count($coo_feature_set->coo_data_objects_array) > 0;
		return $t_still_in_use;
	}
	
	/**
	 *	Deletes all feature sets associated to the product with the given product id.
	 * 
	 *	@param int $p_products_id The id of the product which feature sets should be deleted
	 *	@return boolean Indicates if the deletion was successful
	 */
	public function delete_feature_sets_by_products_id($p_products_id)
	{
		$c_products_id = (int)$p_products_id;
        if (empty($c_products_id))
		{
			trigger_error('delete_sets_by_products_id: typeof($p_products_id) != integer', E_USER_ERROR);
		}

		$t_feature_set_ids_array = array();
		
		$coo_feature_set_to_products = MainFactory::create_object('GMDataObjectGroup', array('feature_set_to_products', array('products_id' => $c_products_id)));		
		$t_feature_set_to_products_array = $coo_feature_set_to_products->get_data_objects_array();
		
		// iterate over feature sets
		foreach($t_feature_set_to_products_array as $coo_feature_set_to_product)
		{
			$t_feature_set_id = $coo_feature_set_to_product->get_data_value('feature_set_id');
			if($this->feature_set_still_in_use($t_feature_set_id) == false)
			{
				$coo_feature_set_values = MainFactory::create_object('GMDataObject', array('feature_set_values'));
				$coo_feature_set_values->set_keys(array('feature_set_id' => $t_feature_set_id));
				$coo_feature_set_values->delete();
				
				$coo_feature_sets = MainFactory::create_object('GMDataObject', array('feature_set'));
				$coo_feature_sets->set_keys(array('feature_set_id' => $t_feature_set_id));
				$coo_feature_sets->delete();
				
				$coo_feature_index = MainFactory::create_object('GMDataObject', array('feature_index'));
				$coo_feature_index->set_keys(array('feature_set_id' => $t_feature_set_id));
				$coo_feature_index->delete();
			}
			$coo_feature_set_to_product->delete();
		}
		
		// delete all feature_set_values by feature_set_ids_array
		$coo_feature_set_values = MainFactory::create_object('GMDataObject', array('feature_set_values'));
		$coo_feature_set_values->set_keys(array('feature_set_id' => $t_feature_set_ids_array));
		$coo_feature_set_values->delete();
		
		// delete all feature_set_to_products by products_id
		$coo_feature_set_to_products = MainFactory::create_object('GMDataObject', array('feature_set_to_products'));
		$coo_feature_set_to_products->set_keys(array('products_id' => $c_products_id));
		$coo_feature_set_to_products->delete();
		
		return true;
	}
	
	/**
	 *	Deletes feature set values with the given set id.
	 * 
	 *	@param int $p_feature_set_id The id of the set that should be deleted
	 *	@return boolean Indicates if the deletion was successful
	 */
	public function delete_feature_set_values_by_feature_set_id( $p_feature_set_id )
	{
		$c_feature_set_id = (int)$p_feature_set_id;
        if (empty($c_feature_set_id))
		{
			trigger_error('delete_feature_set_values_by_feature_set_id: typeof($p_feature_set_id) != integer', E_USER_ERROR);
		}

		$coo_feature_set = MainFactory::create_object('GMDataObject', array('feature_set_values'));
		$coo_feature_set->set_keys(array('feature_set_id' => $c_feature_set_id));
		$coo_feature_set->delete();
		
		return true;
	}
	
	public function delete_features( $p_feature_ids_array )
	{
		$c_feature_ids_array = $this->check_int_param( $p_feature_ids_array );
		
		// delete from feature
		$coo_feature = MainFactory::create_object('GMDataObject', array('feature'));
		$coo_feature->set_keys(array('feature_id' => $c_feature_ids_array));
		$coo_feature->delete();
		$coo_feature = NULL;

		// delete from feature_description
		$coo_feature_desc = MainFactory::create_object('GMDataObject', array('feature_description'));
		$coo_feature_desc->set_keys(array('feature_id' => $c_feature_ids_array));
		$coo_feature_desc->delete();
		$coo_feature_desc = NULL;

		$coo_categories_filter = MainFactory::create_object('GMDataObject', array('categories_filter'));
		$coo_categories_filter->set_keys(array('feature_id' => $c_feature_ids_array));
		$coo_categories_filter->delete();
		$coo_categories_filter = NULL;

		$coo_feature_values = MainFactory::create_object('GMDataObjectGroup', array('feature_value'));
		$coo_feature_values->set_keys(array('feature_id' => $c_feature_ids_array));
		$coo_feature_values->init();
		$t_feature_values_data_objects = $coo_feature_values->get_data_objects_array();

		$t_feature_value_ids_array = array();

		foreach($t_feature_values_data_objects as $t_feature_values_data_object)
		{
			$t_feature_value_ids_array[] = $t_feature_values_data_object->get_data_value('feature_value_id');
		}
		
		$this->delete_feature_values($t_feature_value_ids_array);


		return true;
	}
	
	public function delete_feature_values($p_feature_value_ids_array)
	{
		$c_feature_value_ids_array = $this->check_int_param( $p_feature_value_ids_array );
		
		// delete from feature_value
		$coo_feature = MainFactory::create_object('GMDataObject', array('feature_value'));
		$coo_feature->set_keys(array('feature_value_id' => $c_feature_value_ids_array));
		$coo_feature->delete();
		$coo_feature = NULL;

		// delete from feature_value_description
		$coo_feature_desc = MainFactory::create_object('GMDataObject', array('feature_value_description'));
		$coo_feature_desc->set_keys(array('feature_value_id' => $c_feature_value_ids_array));
		$coo_feature_desc->delete();
		$coo_feature_desc = NULL;

		// delete feature_value in feature_set_values by id
		$coo_feature_set_value = MainFactory::create_object('GMDataObject', array('feature_set_values'));
		$coo_feature_set_value->set_keys(array('feature_value_id' => $c_feature_value_ids_array));
		$coo_feature_set_value->delete();
		$coo_feature_set_value = NULL;
		
		$this->delete_empty_feature_sets();
		
		foreach($c_feature_value_ids_array as $t_feature_value_id)
		{
			// rebuild feature_value_index
			$t_sql = "SELECT feature_set_id
						FROM feature_index
						WHERE feature_value_index like '%-" . $t_feature_value_id . "-%'";
			$t_result = xtc_db_query($t_sql);
			if(xtc_db_num_rows($t_result))
			{
				while($t_row = xtc_db_fetch_array($t_result))
				{
					$this->build_feature_set_index($t_row['feature_set_id']);
				}
			}
		}
		
		return true;
	}
	
	
	
	public function delete_empty_feature_sets()
	{
		$t_sql = 'SELECT fs.feature_set_id
					FROM feature_set fs
					LEFT JOIN feature_set_values fsv ON fs.feature_set_id = fsv.feature_set_id
					GROUP BY feature_set_id
					HAVING COUNT(fsv.feature_set_id) = 0';
		
		$t_result = xtc_db_query($t_sql);
		
		if(xtc_db_num_rows($t_result) > 0)
		{
			$coo_feature_set = MainFactory::create_object('GMDataObject', array('feature_set'));
			while($t_row = xtc_db_fetch_array($t_result))
			{
				$coo_feature_set->set_keys(array('feature_set_id' => $t_row['feature_set_id']));
				$coo_feature_set->delete();
			}
			
			$coo_feature_set = NULL;
		}
	}
}
