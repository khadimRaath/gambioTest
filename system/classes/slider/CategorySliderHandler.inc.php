<?php
/* --------------------------------------------------------------
   ProductQuantityUnitHandler.inc.php 2010-11-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CategorySliderHandler
{

  /*
   * constructor
   */
	function CategorySliderHandler()
	{

	}


  /*
   * set_category_slider
   * to set the selected quantity unit for the actual product
   * @param int $p_category_id  category id
   * @param int $p_cat_slider_id  id for category slider set
   * @return bool
   */
  function set_category_slider($p_category_id, $p_cat_slider_id)
  {
		$coo_data_object = MainFactory::create_object('GMDataObject', array('category_slider_set'));
		$coo_data_object->set_keys(array(
									'category_slider_set_id' => false,
									'category_id' => false
								));

		$coo_data_object->set_data_value('category_slider_set_id', (int) $p_cat_slider_id);
		$coo_data_object->set_data_value('category_id', (int) $p_category_id);

		$coo_data_object->save_body_data();
		return true;
  }

  /*
   * get_category_slider_id
   * to get the quantity unit id for the actual product
   * @param int $p_category_id  category id
   * @return int $t_category_slider_set_id  the slider set id (0:none | >0:id)
   */
  function get_category_slider_id($p_category_id)
  {
    $t_search_array = array('category_id' => (int) $p_category_id);

		$coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('category_slider_set', $t_search_array));
		$t_data_object_array = $coo_data_object_group->get_data_objects_array();

    $t_category_slider_set_id = 0;
    foreach($t_data_object_array as $t_data_object_item) {
      $t_category_slider_set_id = $t_data_object_item->get_data_value('category_slider_set_id');
    }

    return (int) $t_category_slider_set_id;
  }

  /*
   * remove_quantity_unit
   * to remove a saved quantity unit for the actual product
   * @param int $p_category_id  category id
   * @return bool
   */
  function remove_category_slider($p_category_id)
  {
		$coo_data_object = MainFactory::create_object('GMDataObject', array('category_slider_set'));
		$coo_data_object->set_keys(array(
									'category_id' => (int) $p_category_id
								));
		$coo_data_object->delete();
		return true;
  }
}
?>