<?php
/* --------------------------------------------------------------
   FeatureControl.inc.php 2014-01-13 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FeatureControl
{
  /*
   * constructor
   */
  function FeatureControl()
  {

  }


  /*
   * get data for filter on 'feature' table
   * @param array $p_filter_array  assoc array with filter data
   * @param array $p_sort_by  assoc array with sorting options
   * @return array $t_feature_result_array  result array with object data
   */
  function get_feature_array($p_filter_array = array(), $p_sort_by = array())
  {
    $t_feature_result_array = $this->get_data('Feature', 'feature', $p_filter_array, $p_sort_by);
    return $t_feature_result_array;
  }

  /*
   * get data for filter on 'feature_value' table
   * @param array $p_filter_array  assoc array with filter data
   * @param array $p_sort_by  assoc array with sorting options
   * @return array $t_feature_result_array  result array with object data
   */
  function get_feature_value_array($p_feature_id)
  {
	  $c_feature_id = (int)$p_feature_id;
	  
	  $t_sql = 'SELECT *,fv.feature_value_id AS feature_value_id, fv.sort_order AS feature_sort_order FROM feature_value fv 
				LEFT JOIN feature_value_description fvd ON ( fv.feature_value_id = fvd.feature_value_id AND fvd.language_id = "' . $_SESSION['languages_id'] . '" )
				WHERE fv.feature_id = "' . $c_feature_id . '"
				ORDER BY fv.sort_order, feature_value_text';
	  
	  $t_result = xtc_db_query( $t_sql );
	  
	  $t_feature_result_array = array();
	  
	  while( $t_row = xtc_db_fetch_array( $t_result ) )
	  {
		  $t_feature_result_array[ $t_row[ 'feature_value_id' ] ][ 'feature_value_id' ] = $t_row[ 'feature_value_id' ];
		  $t_feature_result_array[ $t_row[ 'feature_value_id' ] ][ 'feature_id' ] = $t_row[ 'feature_id' ];
		  $t_feature_result_array[ $t_row[ 'feature_value_id' ] ][ 'sort_order' ] = $t_row[ 'feature_sort_order' ];
	  }
    return $t_feature_result_array;
  }
  
  function get_feature_value_description($p_feature_value_array)
  {	  
	  if( count($p_feature_value_array) > 0 )
	  {
		  $t_sql = 'SELECT * FROM feature_value_description
					WHERE feature_value_id IN (' . implode( ",", array_keys( $p_feature_value_array ) ) . ')';

		  $t_result = xtc_db_query( $t_sql );

		  while( $t_row = xtc_db_fetch_array( $t_result ) )
		  {
			  $p_feature_value_array[ $t_row[ 'feature_value_id' ] ][ 'feature_value_text_array' ][ $t_row[ 'language_id' ] ] = $t_row[ 'feature_value_text' ];
		  }
	  }
	   
    return $p_feature_value_array;
  }

  /*
   * get data for filter on 'categories_filter' table
   * @param array $p_filter_array  assoc array with filter data
   * @param array $p_sort_by  assoc array with sorting options
   * @return array $t_feature_result_array  result array with object data
   */
  function get_categories_filter_array($p_filter_array = array(), $p_sort_by = array())
  {
    $t_feature_result_array = $this->get_data('CategoriesFilter', 'categories_filter', $p_filter_array, $p_sort_by);
    return $t_feature_result_array;
  }

  /*
   * get data from table using class and return object data array
   * @param string $p_class  class to be used
   * @param string $p_table  table matching the class
   * @param array $p_filter_array  assoc array with filter data
   * @return array $t_result_array  result array with object data
   */
  function get_data($p_class, $p_table, $p_filter_array, $p_sort_by)
  {
    $t_result_array = array();
    $coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array($p_table, $p_filter_array, $p_sort_by));
    $t_data_object_array = $coo_data_object_group->get_data_objects_array();

    foreach ($t_data_object_array as $t_data_object_item) {
      $coo_class = MainFactory::create_object($p_class);
      $coo_class->load_data_object($t_data_object_item);
      $t_result_array[] = $coo_class;
      $coo_class = NULL;
    }

    return $t_result_array;
  }

  /*
   * create a 'Feature' object and return this object
   * @return object $coo_feature  feature object
   */
  function create_feature()
  {
    $coo_feature = MainFactory::create_object('Feature');
    return $coo_feature;
  }

  /*
   * create a 'FeatureValue' object and return this object
   * @return object $coo_feature_value  feature object
   */
  function create_feature_value()
  {
    $coo_feature_value = MainFactory::create_object('FeatureValue');
    return $coo_feature_value;
  }

  /*
   * create a 'CategoriesFilter' object and return this object
   * @return object $coo_categories_filter  feature object
   */
  function create_categories_filter()
  {
    $coo_categories_filter = MainFactory::create_object('CategoriesFilter');
    return $coo_categories_filter;
  }
  
  
  function is_category_filter_enabled($p_categories_id)
  {
	  $c_categories_id = (int)$p_categories_id;
	  
	  if($c_categories_id === 0)
	  {
		  return false;
	  }
	  
	  $coo_category_data_object = MainFactory::create_object('GMDataObject', array('categories', array('categories_id' => $c_categories_id) ));
	  $t_show_cat_filter = $coo_category_data_object->get_data_value('show_category_filter');
	  
	  if($t_show_cat_filter == '1')
	  {
		  return true;
	  }
	  
	  return false;
	  
  }
}