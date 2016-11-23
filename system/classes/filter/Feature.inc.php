<?php
/* --------------------------------------------------------------
   Feature.inc.php 2010-12-09 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class Feature
{
  /*
   * needed class vars
   */
  var $v_feature_id = 0;
  var $v_feature_name_array = array();
  var $v_feature_admin_name_array = array();


  /*
   * constructor
   */
  function Feature()
  {

  }


  /*
   * load_data_object
   * @param object $p_coo_data_object  GmDataObject
   * @return true:ok | false:error
   */
  function load_data_object($p_coo_data_object)
  {
    $t_feature_id = $p_coo_data_object->get_data_value('feature_id');
    $this->set_feature_id($t_feature_id);

    $coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('feature_description', array('feature_id' => $t_feature_id)));
    $t_data_object_array = $coo_data_object_group->get_data_objects_array();

    foreach($t_data_object_array as $t_data_object_item) {
      $t_language_id = $t_data_object_item->get_data_value('language_id');
      $t_feature_name = $t_data_object_item->get_data_value('feature_name');
      $this->set_name($t_language_id, $t_feature_name);
      $t_feature_admin_name = $t_data_object_item->get_data_value('feature_admin_name');
      $this->set_admin_name($t_language_id, $t_feature_admin_name);
    }
    $coo_data_object_group = NULL;

    return true;
  }

  /*
   * set_feature_id
   * @param int $p_feature_id  feature_id to be set
   * @return bool
   */
  function set_feature_id($p_feature_id)
  {
    $this->v_feature_id = (int) $p_feature_id;
    return true;
  }

  /*
   * get_feature_id
   * @return int  feature id
   */
  function get_feature_id()
  {
    return (int) $this->v_feature_id;
  }

  /*
   * set_name
   * set feature name for given language id in feature name array.
   * @param int $p_language_id  language id (1:english, 2:german)
   * @param string $p_feature_name  name (like attribute name)
   * @return bool true:ok | false:error
   */
  function set_name($p_language_id, $p_feature_name)
  {
    $c_language_id = (int) $p_language_id;
    $this->v_feature_name_array[$c_language_id] = $p_feature_name;
    return true;
  }

  /*
   * get_name
   * get feature name for a given language id from array.
   * @param int $p_language_id  language id (1:english, 2:german)
   * @return string  feature name (empty or not)
   */
  function get_name($p_language_id)
  {
    $c_language_id = (int) $p_language_id;
    if (!isset($this->v_feature_name_array[$c_language_id])) {
      return false;
    }
    return $this->v_feature_name_array[$c_language_id];
  }

  /*
   * set_admin_name
   * set feature admin name for given language id in feature name array.
   * @param int $p_language_id  language id (1:english, 2:german)
   * @param string $p_feature_admin_name  name (like attribute name)
   * @return bool true:ok | false:error
   */
  function set_admin_name($p_language_id, $p_feature_admin_name)
  {
    $c_language_id = (int) $p_language_id;
    $this->v_feature_admin_name_array[$c_language_id] = $p_feature_admin_name;
    return true;
  }

  /*
   * get_admin_name
   * get feature admin name for a given language id from array.
   * @param int $p_language_id  language id (1:english, 2:german)
   * @return string  feature name (empty or not)
   */
  function get_admin_name($p_language_id)
  {
    $c_language_id = (int) $p_language_id;
    if (!isset($this->v_feature_admin_name_array[$c_language_id])) {
      return false;
    }
    return $this->v_feature_admin_name_array[$c_language_id];
  }

  /*
   * save
   * @return int  latest id after saving (0:error)
   */
  function save()
  {
    # insert mode?
    $t_insert_mode = true;
    if (!empty($this->v_feature_id)) $t_insert_mode = false;

    $coo_feature = MainFactory::create_object('GMDataObject', array('feature'));

    if($t_insert_mode) {
			$coo_feature->set_keys(array('feature_id' => false));
    } else {
			$coo_feature->set_keys(array('feature_id' => $this->v_feature_id));
		}

    $coo_feature->set_data_value('feature_id', $this->v_feature_id);

    $t_feature_id = (int) $coo_feature->save_body_data();

    # set new id
    if (empty($t_feature_id) && !empty($this->v_feature_id)) {
      $t_feature_id = $this->v_feature_id;
    }

    $coo_feature = NULL;

    # save descriptions
    foreach ($this->v_feature_name_array as $t_language_id => $t_feature_name) {
      $coo_feature_desc = MainFactory::create_object('GMDataObject', array('feature_description'));

      $t_insert_mode = $this->has_feature_entry($t_language_id);

      if($t_insert_mode) {
        $coo_feature_desc->set_keys(array('feature_id' => false,
                                          'language_id' => false));
      } else {
        $coo_feature_desc->set_keys(array('feature_id' => $t_feature_id,
                                          'language_id' => $t_language_id));
      }

      $t_feature_admin_name = '';
      if (!empty($this->v_feature_admin_name_array[$t_language_id])) {
        $t_feature_admin_name = $this->v_feature_admin_name_array[$t_language_id];
      }

      $coo_feature_desc->set_data_value('feature_id', $t_feature_id);
      $coo_feature_desc->set_data_value('language_id', $t_language_id);
      $coo_feature_desc->set_data_value('feature_name', $t_feature_name);
      $coo_feature_desc->set_data_value('feature_admin_name', $t_feature_admin_name);
	  
      $coo_feature_desc->save_body_data();

      $coo_feature_desc = NULL;
    }

    # set and return new id
    if ($t_feature_id != $this->v_feature_id) {
      $this->set_feature_id($t_feature_id);
    }

    return $t_feature_id;
  }

  /*
   * has_feature_entry
   * @param int $p_language_id  language_id for the searched entry (feature_name)
   * @return bool false:UPDATE | true:INSERT
   */
  function has_feature_entry($p_language_id)
  {
    $c_language_id = (int) $p_language_id;

    $t_data_array = array('feature_id'=>$this->v_feature_id, 'language_id'=>$c_language_id);

    $coo_data_object = MainFactory::create_object('GMDataObject', array('feature_description', $t_data_array));

    if (is_array($coo_data_object->v_table_content)) return false;
    return true;
  }

  /*
   * load
   * @return bool true:ok | false:error
   */
  function load($p_feature_id)
  {
    $this->reset();

    $c_feature_id = (int) $p_feature_id;
    $coo_data_object = MainFactory::create_object('GMDataObject', array('feature_description', array('feature_id' => $c_feature_id)));
    $this->load_data_object($coo_data_object);

    $coo_data_object = NULL;

    return true;
  }

  /*
   * delete
   * delete Feature with all Feature Values for given feature_id
   * @return bool
   */
  function delete()
  {
	$coo_feature_set_source = MainFactory::create_object('FeatureSetSource');
	$coo_feature_set_source->delete_features($this->v_feature_id);
	$coo_feature_set_source = NULL;
	
	return true;
  }

  /*
   * reset
   * @return bool
   */
  function reset()
  {
    # clear all
    $this->v_feature_id = 0;
    $this->v_feature_name_array = array();
    # done
    return true;
  }
}
?>