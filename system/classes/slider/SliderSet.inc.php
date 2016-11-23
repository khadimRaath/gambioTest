<?php
/* --------------------------------------------------------------
   SliderSet.inc.php 2011-01-25 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SliderSet
{
  /*
   * params
   */
	var $v_slider_set_id   = 0;
	var $v_slider_set_name = '';
	var $v_slider_speed    = 3000;
	var $v_slider_width    = 700;
	var $v_slider_height   = 500;


  /*
   * constructor
   */
  function SliderSet()
	{
		
	}


  /*
   * set_slider_set_id
   * @param int $p_slider_set_id  slider_set_id
   * @return bool
   */
  function set_slider_set_id($p_slider_set_id)
  {
    $this->v_slider_set_id = (int) $p_slider_set_id;
    return true;
  }

  /*
   * get_slider_set_id
   * @return int  sliderset id
   */
  function get_slider_set_id()
	{
		return (int) $this->v_slider_set_id;
	}

  /*
   * set_slider_set_name
   * @param string $p_slider_set_name  slider_set_name
   * @return bool
   */
	function set_slider_set_name($p_slider_set_name)
	{
    $this->v_slider_set_name = (string) $p_slider_set_name;
    return true;
	}

  /*
   * get_slider_set_name
   * @return int  sliderset name
   */
	function get_slider_set_name()
	{
		return (string) $this->v_slider_set_name;
	}

  /*
   * set_slider_speed
   * @param int $p_slider_speed  slider_speed
   * @return bool
   */
	function set_slider_speed($p_slider_speed)
	{
		$this->v_slider_speed = (int) $p_slider_speed;
    return true;
	}

  /*
   * get_slider_speed
   * @return int  slider speed
   */
	function get_slider_speed()
	{
		return (int) $this->v_slider_speed;
	}


  function set_slider_width($p_slider_width)
  {
		$this->v_slider_width = (int) $p_slider_width;
    return true;
  }

  function get_slider_width()
  {
    return (int) $this->v_slider_width;
  }

  function set_slider_height($p_slider_height)
  {
		$this->v_slider_height = (int) $p_slider_height;
    return true;
  }

  function get_slider_height()
  {
    return (int) $this->v_slider_height;
  }


  /*
   * save
   * @return int  latest id after saving (0:error)
   */
	function save()
	{
    # insert mode?
    $t_insert_mode = true;
    if (!empty($this->v_slider_set_id)) $t_insert_mode = false;

    $coo_slider = MainFactory::create_object('GMDataObject', array('slider_set'));

    # insert or update?
    if($t_insert_mode) {
			$coo_slider->set_keys(array('slider_set_id' => false));
    } else {
			$coo_slider->set_keys(array('slider_set_id' => $this->v_slider_set_id));
		}

    # save data
    $coo_slider->set_data_value('slider_set_id', $this->v_slider_set_id);
    $coo_slider->set_data_value('set_name', $this->v_slider_set_name);
    $coo_slider->set_data_value('slider_speed', $this->v_slider_speed);
    $coo_slider->set_data_value('width', $this->v_slider_width);
    $coo_slider->set_data_value('height', $this->v_slider_height);

    $t_slider_set_id = (int) $coo_slider->save_body_data();

    # get, set and return new id
    if (empty($t_slider_set_id) && !empty($this->v_slider_set_id)) {
      $t_slider_set_id = $this->v_slider_set_id;
    }

    if ($t_slider_set_id != $this->v_slider_set_id) {
      $this->set_slider_set_id($t_slider_set_id);
    }

    return $t_slider_set_id;
  }

  /*
   * load
   * @return bool true:ok | false:error
   */
	function load($p_slider_set_id)
	{
    $this->reset();

    $c_slider_set_id = (int) $p_slider_set_id;

    $t_param_array = array('slider_set_id' => $c_slider_set_id);
    $coo_data_object = MainFactory::create_object('GMDataObject', array('slider_set', $t_param_array));
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
    $this->set_slider_set_id( $p_coo_data_object->get_data_value('slider_set_id') );
    $this->set_slider_set_name( $p_coo_data_object->get_data_value('set_name') );
    $this->set_slider_speed( $p_coo_data_object->get_data_value('slider_speed') );
    $this->set_slider_width( $p_coo_data_object->get_data_value('width') );
    $this->set_slider_height( $p_coo_data_object->get_data_value('height') );

    return true;
	}

  /*
   * delete
   * delete a set (with all images and descriptions)
   * @return bool
   */
	function delete()
	{
    $coo_slider = MainFactory::create_object('GMDataObject', array('slider_set'));
    $coo_slider->set_keys(array('slider_set_id' => $this->v_slider_set_id));
    $coo_slider->delete();
    $coo_slider = NULL;
		
    $coo_slider = MainFactory::create_object('GMDataObject', array('category_slider_set'));
    $coo_slider->set_keys(array('category_slider_set_id' => $this->v_slider_set_id));
    $coo_slider->delete();
    $coo_slider = NULL;

	$coo_slider_image_object = MainFactory::create_object( 'SliderImage' );
	
    $coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('slider_image', array( 'slider_set_id' => $this->v_slider_set_id )));
	$coo_data_object_group_array = $coo_data_object_group->get_data_objects_array();
	foreach( $coo_data_object_group_array AS $coo_data_object )
	{
		$coo_slider_image_object->set_slider_image_id( (int) $coo_data_object->get_data_value( 'slider_image_id' ) );
		$coo_slider_image_object->delete();
	}
	$coo_slider_image_object = NULL;
	$coo_data_object_group = NULL;
	$coo_data_object = NULL;
	
	return true;
  }

  /*
   * reset
   * @return bool
   */
  function reset()
  {
    # clear all
    $this->v_slider_set_id   = 0;
    $this->v_slider_set_name = '';
    $this->v_slider_speed    = 3000;
    $this->v_slider_width    = 700;
    $this->v_slider_height   = 500;
    # done
    return true;
  }
}
?>