<?php
/* --------------------------------------------------------------
   FeatureValue.php 2012-10-17 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FeatureValue
{
  /*
   * needed class vars
   */
  var $v_feature_value_id = 0;
  var $v_feature_id = 0;
  var $v_sort_order = 0;
  var $v_feature_value_text_array = array();


  /*
   * constructor
   */
  function FeatureValue()
  {

  }


  /*
   * load_data_object
   * @param object $p_coo_data_object  GmDataObject
   * @return true:ok | false:error
   */
  function load_data_object($p_coo_data_object)
  {
    # set params
    $t_feature_id = $p_coo_data_object->get_data_value('feature_id');
    $this->set_feature_id($t_feature_id);

    $t_feature_value_id = $p_coo_data_object->get_data_value('feature_value_id');
    $this->set_feature_value_id($t_feature_value_id);

    $t_sort_order = $p_coo_data_object->get_data_value('sort_order');
    $this->set_sort_order($t_sort_order);

    # load descriptions
    $coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('feature_value_description', array('feature_value_id' => $t_feature_value_id)));
    $t_data_object_array = $coo_data_object_group->get_data_objects_array();
    foreach($t_data_object_array as $t_data_object_item) {
      $t_language_id = $t_data_object_item->get_data_value('language_id');
      $t_feature_name = $t_data_object_item->get_data_value('feature_value_text');
      $this->set_text($t_language_id, $t_feature_name);
    }
    $coo_data_object_group = NULL;

    return true;
  }

  /*
   * set_feature_value_id
   * @param int $p_feature_value_id  feature_value_id
   * @return bool
   */
  function set_feature_value_id($p_feature_value_id)
  {
    $this->v_feature_value_id = (int) $p_feature_value_id;
    return true;
  }

  /*
   * get_feature_value_id
   * @return int  feature value id
   */
  function get_feature_value_id()
  {
    return (int) $this->v_feature_value_id;
  }

  /*
   * get_feature_id
   * @return int  feature id
   */
  function set_feature_id($p_feature_id)
  {
    $this->v_feature_id = (int) $p_feature_id;
    return true;
  }

  /*
   * set_feature_id
   * @param int $p_feature_id  feature_id
   * @return bool
   */
  function get_feature_id()
  {
    return (int) $this->v_feature_id;
  }

  /*
   * set_sort_order
   * @param int $p_sort_order  sort order
   * @return bool
   */
  function set_sort_order($p_sort_order)
  {
    $this->v_sort_order = (int) $p_sort_order;
    return true;
  }

  /*
   * get_sort_order
   * @return int  sort order value
   */
  function get_sort_order()
  {
    return (int) $this->v_sort_order;
  }

  /*
   * set_text
   * set text for feature value using given language id.
   * @param int $p_language_id  language id
   * @parem string $p_text  text for feature value
   * @return bool true:ok | false:error
   */
  function set_text($p_language_id, $p_text)
  {
    $c_language_id = (int) $p_language_id;
    $this->v_feature_value_text_array[$c_language_id] = $p_text;
    return true;
  }

  /*
   * get_text
   * get feature value text for a given language id.
   * @param int $p_language_id  language id
   * @return string  feature value text from array
   */
  function get_text($p_language_id)
  {
    $c_language_id = (int) $p_language_id;
    if (!isset($this->v_feature_value_text_array[$c_language_id])) {
      return false;
    }
    return $this->v_feature_value_text_array[$c_language_id];
  }

  /*
   * save
   * @return int  latest id after saving (0:error)
   */
  function save()
  {
    # insert mode?
    $t_insert_mode = true;
    if (!empty($this->v_feature_value_id)) $t_insert_mode = false;

    $coo_feature_value = MainFactory::create_object('GMDataObject', array('feature_value'));

    if($t_insert_mode) {
			$coo_feature_value->set_keys(array('feature_value_id' => false));
    } else {
			$coo_feature_value->set_keys(array('feature_value_id' => $this->v_feature_value_id));
		}

    # save basic data
    $coo_feature_value->set_data_value('feature_value_id', $this->v_feature_value_id);
    $coo_feature_value->set_data_value('feature_id', $this->v_feature_id);
    $coo_feature_value->set_data_value('sort_order', $this->v_sort_order);

    $t_feature_value_id = (int) $coo_feature_value->save_body_data();

    # set new id
    if (empty($t_feature_value_id) && !empty($this->v_feature_value_id)) {
      $t_feature_value_id = $this->v_feature_value_id;
    }

    $coo_feature_value = NULL;

    # save description
    foreach ($this->v_feature_value_text_array as $t_language_id => $t_feature_value_name) {
      $coo_feature_value_desc = MainFactory::create_object('GMDataObject', array('feature_value_description'));

      $t_insert_mode = $this->has_feature_value_entry($t_language_id);

      if($t_insert_mode) {
        $coo_feature_value_desc->set_keys(array('feature_value_id' => false,
                                                'language_id' => false));
      } else {
        $coo_feature_value_desc->set_keys(array('feature_value_id' => $t_feature_value_id,
                                                'language_id' => $t_language_id));
      }

      $coo_feature_value_desc->set_data_value('feature_value_id', $t_feature_value_id);
      $coo_feature_value_desc->set_data_value('language_id', $t_language_id);
      $coo_feature_value_desc->set_data_value('feature_value_text', xtc_db_prepare_input($t_feature_value_name));
      $coo_feature_value_desc->save_body_data();

      $coo_feature_value_desc = NULL;
    }

    # set and return new id
    if ($t_feature_value_id != $this->v_feature_value_id) {
      $this->set_feature_value_id($t_feature_value_id);
    }

    return $t_feature_value_id;
  }

  /*
   * has_feature_entry
   * @param int $p_language_id  language_id for the searched entry (feature_name)
   * @return bool false:UPDATE | true:INSERT
   */
  function has_feature_value_entry($p_language_id)
  {
    $c_language_id = (int) $p_language_id;
    $t_data_array = array('feature_value_id'=>$this->v_feature_value_id, 'language_id'=>$c_language_id);

    $coo_data_object = MainFactory::create_object('GMDataObject', array('feature_value_description', $t_data_array));

    if (is_array($coo_data_object->v_table_content)) return false;
    return true;
  }

  /*
   * load
   * @return bool true:ok | false: error
   */
  function load($p_feature_value_id)
  {
    $this->reset();

    $c_feature_value_id = (int) $p_feature_value_id;
    $coo_data_object = MainFactory::create_object('GMDataObject', array('feature_value', array('feature_value_id' => $c_feature_value_id)));
    $this->load_data_object($coo_data_object);

    $coo_data_object = NULL;

    return true;
  }

  /*
   * delete
   * delete all FeatureValues for given feature_value_id
   * @return bool
   */
  function delete()
  {
	$coo_feature_set_source = MainFactory::create_object('FeatureSetSource');
	$coo_feature_set_source->delete_feature_values($this->v_feature_value_id);
	$coo_feature_set_source = NULL;

    return true;
  }
  
  /*
   * reset
   * @return bool
   */
  function reset()
  {
    $this->v_feature_value_id = 0;
    $this->v_feature_id = 0;
    $this->v_sort_order = 0;
    $this->v_feature_value_text_array = array();
    return true;
  }
}
?>