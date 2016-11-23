<?php
/* --------------------------------------------------------------
   SliderImage.inc.php 2012-12-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SliderImage
{
  /*
   * params
   */
	var $v_slider_image_id = 0;
	var $v_slider_set_id = 0;
	var $v_sort_order = 0;
	var $v_image_file = '';
	var $v_image_preview_file = '';
	var $v_image_title_array = array();
	var $v_image_alt_text_array = array();
	var $v_link_url = '';
	var $v_link_window_target = '';
  var $v_link_target_default = '_blank';
  var $v_allowed_targets = array();


  /*
   * constructor
   */
  function SliderImages()
	{
		
	}


  /*
   * set_allowed_targets
   * @return bool
   */
  function set_allowed_targets()
  {
    $this->v_allowed_targets = array('_top', '_tab', '_blank', '_self');
    return true;
  }

  /*
   * get_allowed_targets
   * @return array  all allowed targets
   */
  function get_allowed_targets()
  {
    return $this->v_allowed_targets;
  }
  /*
   * set_slider_image_id
   * @param int $p_slider_image_id  the slider image id
   * @return bool
   */
  function set_slider_image_id($p_slider_image_id)
  {
    $this->v_slider_image_id = (int) $p_slider_image_id;
    return true;
  }

  /*
   * get_slider_image_id
   * @return int  sliderset id
   */
  function get_slider_image_id()
	{
		return (int) $this->v_slider_image_id;
	}

  /*
   * set_slider_set_id
   * @param int $p_slider_set_id  the sliderset id
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
   * @return int  sort order
   */
  function get_sort_order()
	{
		return (int) $this->v_sort_order;
	}

  /*
   * set_image_file
   * @param int $p_image_file  the name of the image file (e.g. 'picture.jpg')
   * @return bool
   */
  function set_image_file($p_image_file)
	{
		$this->v_image_file = $p_image_file;
    return true;
	}

  /*
   * get_image_file
   * @return string  string with image name
   */
  function get_image_file()
	{
		return (string) $this->v_image_file;
	}

  /*
   * set_preview_file
   * @param int $p_image_preview_file  the name of the thumb image file (e.g. 'picture_tn.jpg')
   * @return bool
   */
  function set_preview_file($p_image_preview_file)
  {
    $this->v_image_preview_file = $p_image_preview_file;
    return true;
  }

  /*
   * get_preview_file
   * @return string  string with thumb image name
   */
  function get_preview_file()
  {
    return (string) $this->v_image_preview_file;
  }

  /*
   * set_image_title
   * @param int $p_language_id  the language_id
   * @param string $p_image_title  the image title string
   * @return bool
   */
  function set_image_title($p_language_id, $p_image_title)
	{
    $c_language_id = (int) $p_language_id;
		$this->v_image_title_array[ $c_language_id ] = $p_image_title;
    return true;
	}

  /*
   * get_image_title
   * @return int  string with image title
   */
  function get_image_title($p_language_id)
	{
    $c_language_id = (int) $p_language_id;
    if (!isset($this->v_image_title_array[ $c_language_id ])) {
      return false;
    }
		return (string) $this->v_image_title_array[ $c_language_id ];
	}

  /*
   * set_image_alt_text
   * @param int $p_language_id  the language_id
   * @param string $p_image_alt_text  the ALT text for this image
   * @return bool
   */
  function set_image_alt_text($p_language_id, $p_image_alt_text)
	{
    $c_language_id = (int) $p_language_id;
		$this->v_image_alt_text_array[ $c_language_id ] = $p_image_alt_text;
    return true;
	}

  /*
   * get_image_alt_text
   * @return int  the image ALT text
   */
  function get_image_alt_text($p_language_id)
	{
    $c_language_id = (int) $p_language_id;
    if (!isset($this->v_image_alt_text_array[ $c_language_id ])) {
      return false;
    }
		return (string) $this->v_image_alt_text_array[ $c_language_id ];
	}

  /*
   * set_link_url
   * @param string $p_link_url  the url for this image
   * @return bool
   */
  function set_link_url($p_link_url)
	{
		$this->v_link_url = (string) $p_link_url;
    return true;
	}

  /*
   * get_link_url
   * @return int  product url for this image
   */
  function get_link_url()
	{
		return (string) $this->v_link_url;
	}

  /*
   * set_link_window_target
   * @param string $p_link_window_target  the link target for this image
   * @return bool
   */
  function set_link_window_target($p_link_window_target)
	{
    $this->set_allowed_targets();

    # target not allowed -> use default
    $t_link_target = (string) $p_link_window_target;
    if (!in_array($t_link_target, $this->v_allowed_targets)) {
      $t_link_target = (string) $this->v_link_target_default;
    };

    $this->v_link_window_target = $t_link_target;
    return true;
	}

  /*
   * get_link_window_target
   * @return int  image target for link
   */
  function get_link_window_target()
	{
		return (string) $this->v_link_window_target;
	}

  /*
   * save
   * @return int  latest id after saving (0:error)
   */
  function save()
	{
    # insert mode?
    $t_insert_mode = true;
    if (!empty($this->v_slider_image_id)) $t_insert_mode = false;

    $coo_slider = MainFactory::create_object('GMDataObject', array('slider_image'));

    # insert or update?
    if($t_insert_mode) {
			$coo_slider->set_keys(array('slider_image_id' => false));
    } else {
			$coo_slider->set_keys(array('slider_image_id' => $this->v_slider_image_id));
		}

    # save basic IMAGE data
    $coo_slider->set_data_value('slider_image_id', $this->v_slider_image_id);
    $coo_slider->set_data_value('slider_set_id', $this->v_slider_set_id);
    $coo_slider->set_data_value('sort_order', $this->v_sort_order);
    $coo_slider->set_data_value('image_file', $this->v_image_file);
    $coo_slider->set_data_value('image_preview_file', $this->v_image_preview_file);
    $coo_slider->set_data_value('link_url', $this->v_link_url);
    $coo_slider->set_data_value('link_window_target', $this->v_link_window_target);

    $t_slider_image_id = (int) $coo_slider->save_body_data();

    # get new id
    if (empty($t_slider_image_id) && !empty($this->v_slider_image_id)) {
      $t_slider_image_id = $this->v_slider_image_id;
    }

    $coo_slider = NULL;

    # save IMAGE description
    foreach ($this->v_image_title_array as $t_language_id => $t_image_title) {
      $c_language_id = (int) $t_language_id;

      $coo_slider_desc = MainFactory::create_object('GMDataObject', array('slider_image_description'));

      # insert or update?
      $t_insert_mode = $this->has_description($t_language_id);

      if($t_insert_mode) {
        $coo_slider_desc->set_keys(array('slider_image_id' => false,
                                         'language_id'     => false));
      } else {
        $coo_slider_desc->set_keys(array('slider_image_id' => $t_slider_image_id,
                                         'language_id'     => $c_language_id));
      }

      $t_image_title = '';
      if (!empty($this->v_image_title_array[$c_language_id])) {
        $t_image_title = $this->v_image_title_array[$c_language_id];
      }
      $t_image_alt_text = '';
      if (!empty($this->v_image_alt_text_array[$c_language_id])) {
        $t_image_alt_text = $this->v_image_alt_text_array[$c_language_id];
      }

      # save description data
      $coo_slider_desc->set_data_value('slider_image_id', $t_slider_image_id);
      $coo_slider_desc->set_data_value('language_id', $c_language_id);
      $coo_slider_desc->set_data_value('image_title', $t_image_title);
      $coo_slider_desc->set_data_value('image_alt_text', $t_image_alt_text);

      $coo_slider_desc->save_body_data();

      $coo_slider_desc = NULL;
    }

    # set and return new id
    if ($t_slider_image_id != $this->v_slider_image_id) {
      $this->set_slider_image_id($t_slider_image_id);
    }

    return $t_slider_image_id;
  }

  /*
   * has_description
   * @param int $p_language_id  language_id for the searched entry
   * @return bool false:UPDATE | true:INSERT
   */
  function has_description($p_language_id)
  {
    $c_language_id = (int) $p_language_id;

    $t_data_array = array('slider_image_id'=>$this->v_slider_image_id, 'language_id'=>$c_language_id);

    $coo_data_object = MainFactory::create_object('GMDataObject', array('slider_image_description', $t_data_array));

    if (is_array($coo_data_object->v_table_content)) return false;
    return true;
  }

  /*
   * load
   * @return bool true:ok | false:error
   */
  function load($p_slider_image_id)
	{
    $this->reset();

    $c_slider_image_id = (int) $p_slider_image_id;

    $t_param_array = array('slider_image_id' => $c_slider_image_id);
    $coo_data_object = MainFactory::create_object('GMDataObject', array('slider_image', $t_param_array));
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
    # basic data
    $this->set_slider_image_id( $p_coo_data_object->get_data_value('slider_image_id') );
    $this->set_slider_set_id( $p_coo_data_object->get_data_value('slider_set_id') );
    $this->set_sort_order( $p_coo_data_object->get_data_value('sort_order') );
    $this->set_image_file( $p_coo_data_object->get_data_value('image_file') );
    $this->set_preview_file( $p_coo_data_object->get_data_value('image_preview_file') );
    $this->set_link_url( $p_coo_data_object->get_data_value('link_url') );
    $this->set_link_window_target( $p_coo_data_object->get_data_value('link_window_target') );

    # descriptions (title, alt)
    $t_param_array = array('slider_image_id' => $this->v_slider_image_id);
    $coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('slider_image_description', $t_param_array));
    $t_data_object_array = $coo_data_object_group->get_data_objects_array();
    $coo_data_object_group = NULL;

    foreach($t_data_object_array as $t_data_object_item) {
      $t_language_id = (int) $t_data_object_item->get_data_value('language_id');

      $t_image_title = $t_data_object_item->get_data_value('image_title');
      $this->set_image_title($t_language_id, $t_image_title);

      $t_image_alt_text = $t_data_object_item->get_data_value('image_alt_text');
      $this->set_image_alt_text($t_language_id, $t_image_alt_text);
    }

    return true;
  }

  /*
   * delete
   * delete image, all description and all image maps
   * @return bool
   */
  function delete()
	{
	  // get image_file_name && image_preview_file_name
	  
    $coo_slider = MainFactory::create_object('GMDataObject', array('slider_image', array( 'slider_image_id' => $this->v_slider_image_id )));
	$t_slider_image_file_name = $coo_slider->get_data_value( 'image_file' );
	$t_slider_image_preview_file_name = $coo_slider->get_data_value( 'image_preview_file' );
    $coo_slider->delete();
    $coo_slider = NULL;

    $coo_slider = MainFactory::create_object('GMDataObject', array('slider_image_description'));
    $coo_slider->set_keys( array( 'slider_image_id' => $this->v_slider_image_id ) );
    $coo_slider->delete();
    $coo_slider = NULL;
		
	// delete slider_image_area
	$coo_slider = MainFactory::create_object( 'GMDataObject', array( 'slider_image_area' ) );
	$coo_slider->set_keys( array( 'slider_image_id' => $this->v_slider_image_id ) );
	$coo_slider->delete();
	
	// delete physical image
	if( !empty( $t_slider_image_file_name ) ) 
	{
		$t_file = DIR_FS_CATALOG.'images/slider_images/' . $t_slider_image_file_name;
		@unlink( $t_file );
	}
	if( !empty( $t_slider_image_preview_file_name ) ) 
	{
		$t_file = DIR_FS_CATALOG.'images/slider_images/thumbnails/' . $t_slider_image_preview_file_name;
		@unlink( $t_file );
	}

    return true;
	}

  /*
   * reset
   * @return bool
   */
  function reset()
  {
    # clear all
    $this->v_slider_image_id = 0;
    $this->v_slider_set_id = 0;
    $this->v_sort_order = 0;
    $this->v_image_file = '';
    $this->v_image_preview_file = '';
    $this->v_image_title_array = array();
    $this->v_image_alt_text_array = array();
    $this->v_link_url = '';
    $this->v_link_window_target = $this->v_link_target_default;
    $this->set_allowed_targets();
    # done
    return true;
  }
}
?>