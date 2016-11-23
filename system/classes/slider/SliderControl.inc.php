<?php
/* --------------------------------------------------------------
   SliderControl.inc.php 2011-01-25 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SliderControl
{
  /*
   * get data for filter on 'slider_set' table
   * @param array $p_filter_array  assoc array with filter data
   * @param array $p_sort_by  assoc array with sorting options
   * @return array $t_slider_result_array  result array with object data
   */
  function get_slider_set_array($p_filter_array = array(), $p_sort_by = array())
	{
    $t_slider_result_array = $this->get_data('SliderSet', 'slider_set', $p_filter_array, $p_sort_by);
    return $t_slider_result_array;
	}

  /*
   * get data for filter on 'slider_image' table
   * @param array $p_filter_array  assoc array with filter data
   * @param array $p_sort_by  assoc array with sorting options
   * @return array $t_slider_result_array  result array with object data
   */
	function get_slider_image_array($p_filter_array = array(), $p_sort_by = array())
	{
    $t_slider_result_array = $this->get_data('SliderImage', 'slider_image', $p_filter_array, $p_sort_by);
    return $t_slider_result_array;
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
   * create a 'SliderSet' object and return this object
   * @return object $coo_slider_set  SliderSet object
   */
	function create_slider_set()
	{
    $coo_slider_set = MainFactory::create_object('SliderSet');
    return $coo_slider_set;
	}

  /*
   * create a 'SliderImage' object and return this object
   * @return object $coo_slider_image  SliderImage object
   */
	function create_slider_image()
	{
    $coo_slider_image = MainFactory::create_object('SliderImage');
    return $coo_slider_image;
	}

	/**
	 * @param assoc_array p_filter_array
	 * @return
	 * @access public
	 */
	function get_slider_image_area_array($p_filter_array)
	{
		$coo_slider_image_area = $this->get_data('SliderImageArea', 'slider_image_area', $p_filter_array, array());
		return $coo_slider_image_area;
	}
	
	/**
	 * @param int p_slider_image_id 
	 * @return SliderImageArea
	 * @access public
	 */
	function create_slider_image_area($p_slider_image_id)
	{
		$coo_slider_image_area = MainFactory::create_object('SliderImageArea');
		$coo_slider_image_area->set_slider_image_id($p_slider_image_id);

		return $coo_slider_image_area;
	}
}
?>