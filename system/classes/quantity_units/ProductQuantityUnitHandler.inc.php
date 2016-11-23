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

class ProductQuantityUnitHandler
{

  /*
   * constructor
   */
	function ProductQuantityUnitHandler()
	{

	}


  /*
   * set_quantity_unit_id
   * to set the selected quantity unit for the actual product
   * @param int $p_products_id  product id
   * @param int $p_quantity_unit_id  id for quantity unit
   * @return bool
   */
  function set_quantity_unit($p_products_id, $p_quantity_unit_id)
  {
		$coo_data_object = MainFactory::create_object('GMDataObject', array('products_quantity_unit'));
		$coo_data_object->set_keys(array(
									'quantity_unit_id' => false,
									'products_id' => false
								));

		$coo_data_object->set_data_value('quantity_unit_id', (int) $p_quantity_unit_id);
		$coo_data_object->set_data_value('products_id', (int) $p_products_id);

		$coo_data_object->save_body_data();
		return true;
  }

  /*
   * get_quantity_unit_id
   * to get the quantity unit id for the actual product
   * @param int $p_products_id  product id
   * @return int $t_quantity_unit_id  the unit id (0:none | >0:id)
   */
  function get_quantity_unit_id($p_products_id)
  {
    $t_search_array = array('products_id' => (int) $p_products_id);

		$coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('products_quantity_unit', $t_search_array));
		$t_data_object_array = $coo_data_object_group->get_data_objects_array();

    $t_quantity_unit_id = 0;
    foreach($t_data_object_array as $t_data_object_item) {
      $t_quantity_unit_id = $t_data_object_item->get_data_value('quantity_unit_id');
    }

    return (int) $t_quantity_unit_id;
  }

  /*
   * remove_quantity_unit
   * to remove a saved quantity unit for the actual product
   * @param int $p_products_id  product id
   * @return bool
   */
  function remove_quantity_unit($p_products_id)
  {
		$coo_data_object = MainFactory::create_object('GMDataObject', array('products_quantity_unit'));
		$coo_data_object->set_keys(array(
									'products_id' => (int) $p_products_id
								));
		$coo_data_object->delete();
		return true;
  }
}
?>