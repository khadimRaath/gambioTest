<?php
/* --------------------------------------------------------------
   ProductGoogleCategory.inc.php 2011-09-13 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ProductGoogleCategory
{

	/**
	 * relation ID
	 * @var int
	 */
	var $v_products_google_categories_id;
	/**
	 * product ID
	 * @var int
	 */
	var $v_products_id;
	/**
	 * google category
	 * @var string
	 */
	var $v_google_category;

	/**
	 * sets the products google cat ID
	 *
	 * @param int $p_products_google_categories_id relation ID
	 * @return bool true
	 */
	function set_products_google_categories_id($p_products_google_categories_id)
	{
		//products_google_categories_id aus DB
		$this->v_products_google_categories_id = (int)$p_products_google_categories_id;
		return true;
	}

	/**
	 * gets the products google cat ID
	 *
	 * @return int $products_google_cat_id Products google cat ID
	 */
	function get_products_google_categories_id()
	{
		return (int)$this->v_products_google_categories_id;
	}

	/**
	 * set the products ID
	 * 
	 * @param int $p_products_id Product ID
	 * @return bool true
	 */
	function set_products_id($p_products_id)
	{
		$this->v_products_id = (int)$p_products_id;
		return true;
	}

	/**
	 * get the Product ID
	 * 
	 * @return int $this->v_products_id Products ID
	 */
	function get_products_id()
	{
		return (int)$this->v_products_id;
	}

	/**
	 * set the google category
	 * 
	 * @param string $p_google_category google category
	 * @return bool true
	 */
	function set_google_category($p_google_category)
	{
		$this->v_google_category = $p_google_category;
	}

	/**
	 * get google category
	 *
	 * @return string $t_google_category Google category
	 */
	function get_google_category()
	{
		return $this->v_google_category;
	}

	/*
	 * save the google category for an product
	 * 
	 * return mixed relation ID:ok | false:error
	 */
	function save()
	{
		// if no IDs set, error
		if(empty($this->v_products_id) || empty($this->v_google_category)) {
			return false;
		}

		// check if product has this google category
		$t_param = array('products_id' => $this->v_products_id, 'google_category' => $this->v_google_category);
		$coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('products_google_categories', $t_param));
		$t_data_object_array = $coo_data_object_group->get_data_objects_array();
		$t_number_objects = count($t_data_object_array);
		if($t_number_objects == 1) {
			$this->load_data_object($t_data_object_array[0]);
			return $this->v_products_google_categories_id;
		} elseif($t_number_objects > 1) {
			$t_result_array = array();
			foreach ($t_data_object_array as $t_data_object_item) {
				$this->load_data_object($t_data_object_item);
				$t_result_array[] = $this->v_products_google_categories_id;
			}
			trigger_error('Save Google Category: pID: '.$this->v_products_id.' - gK: '.$this->v_google_category.' - ids: '.implode(',', $t_result_array), E_USER_WARNING);
			return false;
		}

		// insert mode?
		$t_insert_mode = true;
		if (!empty($this->v_products_google_categories_id)) {
			$t_insert_mode = false;
		}

		$coo_products_google_categories = MainFactory::create_object('GMDataObject', array('products_google_categories'));

		// insert or update?
		if($t_insert_mode) {
			$coo_products_google_categories->set_keys(array('products_google_categories_id' => false));
		} else {
			$coo_products_google_categories->set_keys(array('products_google_categories_id' => $this->v_products_google_categories_id));
		}

		// save data
		$coo_products_google_categories->set_data_value('products_id', $this->v_products_id);
		$coo_products_google_categories->set_data_value('google_category', $this->v_google_category);

		$t_products_google_categories_id = (int)$coo_products_google_categories->save_body_data();

		// get, set and return new id
		if (empty($t_products_google_categories_id) && !empty($this->v_products_google_categories_id)) {
			$t_products_google_categories_id = $this->v_products_google_categories_id;
		}

		if ($t_products_google_categories_id != $this->v_products_id) {
			$this->set_products_google_categories_id($t_products_google_categories_id);
		}

		return $t_products_google_categories_id;
	}

	/**
	 * load a data object
	 *
	 * @param object $p_coo_data_object  GmDataObject
	 * @return bool true
	 * 
	 */
	function load_data_object($p_coo_data_object)
	{
		// set all datas
		$this->set_products_google_categories_id( $p_coo_data_object->get_data_value('products_google_categories_id') );
		$this->set_products_id( $p_coo_data_object->get_data_value('products_id') );
		$this->set_google_category( $p_coo_data_object->get_data_value('google_category') );

		return true;
	}

	/**
	 * delete a products google category entety
	 *
	 * @return bool true
	 */
	function delete()
	{
		// delete a products google category
		$coo_products_google_categories = MainFactory::create_object('GMDataObject', array('products_google_categories'));
		$coo_products_google_categories->set_keys(array('products_google_categories_id' => $this->v_products_google_categories_id));
		$coo_products_google_categories->delete();
		$coo_products_google_categories = NULL;

		return true;
	}

}
?>