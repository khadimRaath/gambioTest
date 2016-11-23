<?php
/* --------------------------------------------------------------
   QuantityUnit 2011-02-21 ih
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class QuantityUnit
{
  /*
   * needed class vars
   */
	var $v_quantity_unit_id = 0;
	var $v_unit_name_array  = array();


  /*
   * constructor
   */
	function QuantityUnit()
	{

	}


  /*
   * set_quantity_unit_id
   * @param int $p_quantity_unit_id  set unit id
   * @return bool
   */
  function set_quantity_unit_id($p_quantity_unit_id)
	{
    $this->v_quantity_unit_id = (int) $p_quantity_unit_id;
    return true;
	}

  /*
   * get_quantity_unit_id
   * @return int  return quantity unit id
   */
	function get_quantity_unit_id()
	{
		return (int) $this->v_quantity_unit_id;
	}

  /*
   * set_unit_name
   * @param int $p_language_id   language id
   * @param string $p_unit_name  unit name
   * @return bool
   */
	function set_unit_name($p_language_id, $p_unit_name)
	{
    $c_language_id = (int) $p_language_id;
    $this->v_unit_name_array[$c_language_id] = $p_unit_name;
    return true;
	}

  /*
   * get_unit_name
   * @param int $p_language_id  language id
   * @return string  unit name
   */
	function get_unit_name($p_language_id)
	{
    $c_language_id = (int) $p_language_id;
    if (!isset($this->v_unit_name_array[$c_language_id])) {
      return false;
    }
    return $this->v_unit_name_array[$c_language_id];
	}

  /*
   * save
   * @return int  latest id after saving (0:error)
   */
	function save()
	{
    # insert mode?
    $t_insert_mode = true;
    if (!empty($this->v_quantity_unit_id)) $t_insert_mode = false;

    $coo_quantity_unit = MainFactory::create_object('GMDataObject', array('quantity_unit'));

    if($t_insert_mode) {
			$coo_quantity_unit->set_keys(array('quantity_unit_id' => false));
    } else {
			$coo_quantity_unit->set_keys(array('quantity_unit_id' => $this->v_quantity_unit_id));
		}

    # save basic data
    $coo_quantity_unit->set_data_value('quantity_unit_id', $this->v_quantity_unit_id);

    $t_quantity_unit_id = (int) $coo_quantity_unit->save_body_data();

    # set new id
    if (empty($t_quantity_unit_id) && !empty($this->v_quantity_unit_id)) {
      $t_quantity_unit_id = $this->v_quantity_unit_id;
    }

    $coo_quantity_unit = NULL;

    # save description
    foreach ($this->v_unit_name_array as $t_language_id => $t_quantity_unit_name) {
      $coo_quantity_unit_desc = MainFactory::create_object('GMDataObject', array('quantity_unit_description'));

      $t_insert_mode = $this->has_quantity_unit_entry($t_language_id);

      if($t_insert_mode) {
        $coo_quantity_unit_desc->set_keys(array('quantity_unit_id' => false,
                                                'language_id' => false));
      } else {
        $coo_quantity_unit_desc->set_keys(array('quantity_unit_id' => $t_quantity_unit_id,
                                                'language_id' => $t_language_id));
      }

      $coo_quantity_unit_desc->set_data_value('quantity_unit_id', $t_quantity_unit_id);
      $coo_quantity_unit_desc->set_data_value('language_id', $t_language_id);
      $coo_quantity_unit_desc->set_data_value('unit_name', $t_quantity_unit_name);
      $coo_quantity_unit_desc->save_body_data();

      $coo_quantity_unit_desc = NULL;
    }

    # set and return new id
    if ($t_quantity_unit_id != $this->v_quantity_unit_id) {
      $this->set_quantity_unit_id($t_quantity_unit_id);
    }

    return $t_quantity_unit_id;
  }

  /*
   * has_unit_entry
   * @param int $p_language_id  language_id for the searched entry (unit_name)
   * @return bool false:UPDATE | true:INSERT
   */
  function has_quantity_unit_entry($p_language_id)
  {
    $c_language_id = (int) $p_language_id;

    $t_data_array = array('quantity_unit_id'=>$this->v_quantity_unit_id, 'language_id'=>$c_language_id);

    $coo_data_object = MainFactory::create_object('GMDataObject', array('quantity_unit_description', $t_data_array));

    if (is_array($coo_data_object->v_table_content)) return false;
    return true;
  }

  /*
   * load
   * @return bool true:ok | false:error
   */
  function load($p_quantity_unit_id)
	{
    $this->reset();

    $c_quantity_unit_id = (int) $p_quantity_unit_id;
    $coo_data_object = MainFactory::create_object('GMDataObject', array('quantity_unit', array('quantity_unit_id' => $c_quantity_unit_id)));
    $this->load_data_object($coo_data_object);

    $coo_data_object = NULL;

    return true;
	}

  /*
   * load_data_object
   * @param object $p_coo_data_object  GmDataObject
   * @return true:ok | false:error
   */
	function load_data_object($p_coo_data_object)
	{
    # set params
    $t_quantity_unit_id = $p_coo_data_object->get_data_value('quantity_unit_id');
    $this->set_quantity_unit_id($t_quantity_unit_id);

    # load descriptions
    $coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('quantity_unit_description', array('quantity_unit_id' => $t_quantity_unit_id)));
    $t_data_object_array = $coo_data_object_group->get_data_objects_array();
    foreach($t_data_object_array as $t_data_object_item) {
      $t_language_id = $t_data_object_item->get_data_value('language_id');
      $t_quantity_name = $t_data_object_item->get_data_value('unit_name');
      $this->set_unit_name($t_language_id, $t_quantity_name);
    }
    $coo_data_object_group = NULL;

    return true;
  }

  /*
   * delete
   * @return bool
   */
	function delete()
	{
    $coo_quantity_unit = MainFactory::create_object('GMDataObject', array('quantity_unit'));
    $coo_quantity_unit->set_keys(array('quantity_unit_id' => $this->v_quantity_unit_id));
    $coo_quantity_unit->delete();
    $coo_quantity_unit = NULL;

    $coo_quantity_unit_desc = MainFactory::create_object('GMDataObject', array('quantity_unit_description'));
    $coo_quantity_unit_desc->set_keys(array('quantity_unit_id' => $this->v_quantity_unit_id));
    $coo_quantity_unit_desc->delete();
    $coo_quantity_unit_desc = NULL;

    $coo_quantity_unit_desc = MainFactory::create_object('GMDataObject', array('products_quantity_unit'));
    $coo_quantity_unit_desc->set_keys(array('quantity_unit_id' => $this->v_quantity_unit_id));
    $coo_quantity_unit_desc->delete();
    $coo_quantity_unit_desc = NULL;

    return true;
	}

  /*
   * reset
   * @return bool
   */
  function reset()
  {
    # reset
    $this->v_quantity_unit_id = 0;
    $this->v_unit_name_array  = array();
    # done
    return true;
  }
  
  /*
   * TODO
   * get_quantity_unit_id_by_products_id
   */
  function get_quantity_unit_name_by_products_id($p_products_id, $p_language_id){
	$query = xtc_db_query("SELECT unit_name 
							FROM quantity_unit_description AS qud
							LEFT JOIN products_quantity_unit AS pqu ON qud.quantity_unit_id=pqu.quantity_unit_id
							WHERE qud.language_id = '".$p_language_id."'
							AND pqu.products_id = '".$p_products_id."'
					  ");
	$result = xtc_db_fetch_array($query);
	return $result['unit_name'];
  }
  
  
}
?>