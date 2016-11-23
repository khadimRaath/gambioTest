<?php
/* --------------------------------------------------------------
   ProductSliderHandler.inc.php 2011-08-19 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ProductSliderHandler
{
	/*
	 * set_product_slider
	 * to set the selected quantity unit for the actual product
	 * @param int $p_id  object id
	 * @param int $p_slider_id  id for slider set
	 * @return bool
	*/
	function set_product_slider($p_id, $p_slider_id)
	{
		$coo_data_object = MainFactory::create_object('GMDataObject', array('products_slider_set'));
		$coo_data_object->set_keys(array(
									'products_slider_set_id' => false,
									'slider_set_id' => false
								));

		$coo_data_object->set_data_value('products_slider_set_id', (int) $p_id);
		$coo_data_object->set_data_value('slider_set_id', (int) $p_slider_id);

		$coo_data_object->save_body_data();
		return true;
	}

	/*
	 * get_product_slider_id
	 * to get the quantity unit id for the actual product
	 * @param int $p_id  object id
	 * @return int $t_slider_set_id  the slider set id (0:none | >0:id)
	*/
	function get_product_slider_id($p_id)
	{
		$t_search_array = array('products_slider_set_id' => (int) $p_id);

		$coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('products_slider_set', $t_search_array));
		$t_data_object_array = $coo_data_object_group->get_data_objects_array();

		$t_slider_set_id = 0;
		foreach($t_data_object_array as $t_data_object_item) {
			$t_slider_set_id = $t_data_object_item->get_data_value('slider_set_id');
		}

		return (int) $t_slider_set_id;
	}

	/*
	 * remove_product_slider
	 * to remove a saved quantity unit for the actual product
	 * @param int $p_id  object id
	 * @return bool
	*/
	function remove_product_slider($p_id)
	{
		$coo_data_object = MainFactory::create_object('GMDataObject', array('products_slider_set'));
		$coo_data_object->set_keys(array(
									'products_slider_set_id' => (int) $p_id
								));
		$coo_data_object->delete();
		return true;
	}
}
?>