<?php
/* --------------------------------------------------------------
   QuantityUnitControl 2011-02-21 ih
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class QuantityUnitControl
{
  /*
   * constructor
   */
	function QuantityUnitControl()
	{
		
	}


  /*
   * get data for filter on 'quantity_unit' table
   * @param array $p_filter_array  assoc array with filter data
   * @param array $p_sort_by  assoc array with sorting options
   * @return array $t_quantity_unit_result_array  result array with object data
   */
	function get_quantity_unit_array($p_filter_array = array(), $p_sort_by = array())
	{
    $t_quantity_unit_result_array = $this->get_data('QuantityUnit', 'quantity_unit', $p_filter_array, $p_sort_by);
    return $t_quantity_unit_result_array;
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
   * create a 'QuantityUnit' object and return this object
   * @return object $coo_quantity_unit  quantity unit object
   */
	function create_quantity_unit()
	{
    $coo_quantity_unit = MainFactory::create_object('QuantityUnit');
    return $coo_quantity_unit;
	}
}
?>