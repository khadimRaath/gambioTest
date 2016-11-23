<?php
/* --------------------------------------------------------------
   FilterControl.inc.php 2010-12-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FilterControl
{
  /*
   * constructor
   */
  function FilterControl()
  {

  }


  /*
   * get_categories_filter_array
   * all filter data for a set of given search params and sort param
   * @param array $p_filter_array  array with filter options
   * @param array $p_sort_by  assoc array with sorting options
   * @return array $t_feature_result_array  result array with object data
   */
  function get_categories_filter_array($p_filter_array = array(), $p_sort_by = array())
  {
    $t_result_array = array();

    $coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('categories_filter', $p_filter_array, $p_sort_by));
    $t_data_object_array = $coo_data_object_group->get_data_objects_array();

    foreach ($t_data_object_array as $t_data_object_item) {
      $coo_class = MainFactory::create_object('CategoriesFilter');
      $coo_class->load_data_object($t_data_object_item);
      $t_result_array[] = $coo_class;
      $coo_class = NULL;
    }

    return $t_result_array;
  }

  /*
   * get_products_array
   * all product ids as an array for given search params
   * @param int $p_categories_id  cat_id
   * @param array $p_feature_value_id_array  feature value ids
   * @param int $p_price_from  price start
   * @param int $p_price_to  price end
   * @param array $p_sort_by  assoc array with sorting options
   * @return array $XXXXX  result array
   */
  function get_products_array($p_categories_id, $p_feature_value_id_array = array(), $p_price_from = '0', $p_price_to = '0', $p_sort_by = array())
  {

  }
}
?>