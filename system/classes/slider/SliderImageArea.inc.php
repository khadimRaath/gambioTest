<?php
/* --------------------------------------------------------------
   SliderImageArea.inc.php 2011-07-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SliderImageArea
{
	/**
	 * @access private
	 */
	var $v_slider_image_area_id = 0;
	/**
	 * @access private
	 */
	var $v_slider_image_id = 0;
	/**
	 * @access private
	 */
	var $v_shape = '';
	/**
	 * @access private
	 */
	var $v_coords = '';
	/**
	 * @access private
	 */
	var $v_title = '';
	/**
	 * @access private
	 */
	var $v_link_url = '';
	/**
	 * @access private
	 */
	var $v_link_target = '';
  /**
	 * @access private
	 */
	var $v_flyover_content = '';

	/**
	 * @return int
	 * @access public
	 */
	function get_slider_image_area_id()
	{
		return (int)$this->v_slider_image_area_id;
	}

	/**
	 * @param int p_slider_image_area_id
	 * @return
	 * @access public
	 */
	function set_slider_image_area_id($p_slider_image_area_id)
	{
		if(check_data_type($p_slider_image_area_id, 'int'))
		{
			$this->v_slider_image_area_id = (int)$p_slider_image_area_id;
		}
	}

	/**
	 * @return int
	 * @access public
	 */
	function get_slider_image_id()
	{
		return (int)$this->v_slider_image_id;
	}

	/**
	 * @param int p_slider_image_id
	 * @return
	 * @access public
	 */
	function set_slider_image_id($p_slider_image_id)
	{
		if(check_data_type($p_slider_image_id, 'int'))
		{
			$this->v_slider_image_id = (int)$p_slider_image_id;
		}
	}

	/**
	 * @return string
	 * @access public
	 */
	function get_shape()
	{
		return (string)$this->v_shape;
	}

	/**
	 * @param string p_shape
	 * @return
	 * @access public
	 */
	function set_shape($p_shape)
	{
		if(check_data_type($p_shape, 'string'))
		{
			$this->v_shape = $p_shape;
		}
	}

	/**
	 * @return
	 * @access public
	 */
	function save()
	{
		// insert mode?
		$t_insert_mode = true;
		if(!empty($this->v_slider_image_area_id))
		{
			$t_insert_mode = false;
		}

		$coo_slider_image_area = MainFactory::create_object('GMDataObject', array('slider_image_area'));

		if($t_insert_mode)
		{
			$coo_slider_image_area->set_keys(array('slider_image_area_id' => false));
		}
		else
		{
			if($this->get_slider_image_area_id() > 0)
			{
				$coo_slider_image_area->set_keys(array('slider_image_area_id' => $this->get_slider_image_area_id()));
			}
			else
			{
				trigger_error('saving aborted, because slider_image_area_id is 0', E_USER_ERROR);
				return false;
			}
		}

		$coo_slider_image_area->set_data_value('slider_image_id', $this->get_slider_image_id());
		$coo_slider_image_area->set_data_value('shape', $this->get_shape());
		$coo_slider_image_area->set_data_value('coords', $this->get_coords());
		$coo_slider_image_area->set_data_value('title', $this->get_title());
		$coo_slider_image_area->set_data_value('link_url', $this->get_link_url());
		$coo_slider_image_area->set_data_value('link_target', $this->get_link_target());
    $coo_slider_image_area->set_data_value('flyover_content', $this->get_flyover_content());

		$c_slider_image_area_id = (int)$coo_slider_image_area->save_body_data();

		return $c_slider_image_area_id;
	}

	/**
	 * @param int p_slider_image_area_id
	 * @return bool
	 * @access public
	 */
	function load($p_slider_image_area_id)
	{
		$c_slider_image_area_id = (int)$p_slider_image_area_id;
		$coo_data_object = MainFactory::create_object('GMDataObject', array('slider_image_area', array('slider_image_area_id' => $c_slider_image_area_id)));
		$this->load_data_object($coo_data_object);
	}

	/**
	 * @param GMDataObject p_coo_data_object
	 * @return bool
	 * @access public
	 */
	function load_data_object($p_coo_data_object)
	{
		if(!is_object($p_coo_data_object) || !is_callable(array($p_coo_data_object, 'get_data_value')))
		{
			trigger_error('load_data_object param is no GMDataObject', E_USER_WARNING);

			return false;
		}

		// set data
		$t_slider_image_area_id = $p_coo_data_object->get_data_value('slider_image_area_id');
		$this->set_slider_image_area_id($t_slider_image_area_id);

		$t_slider_image_id = $p_coo_data_object->get_data_value('slider_image_id');
		$this->set_slider_image_id($t_slider_image_id);

		$t_shape = $p_coo_data_object->get_data_value('shape');
		$this->set_shape($t_shape);

		$t_coords = $p_coo_data_object->get_data_value('coords');
		$this->set_coords($t_coords);

		$t_title = $p_coo_data_object->get_data_value('title');
		$this->set_title($t_title);

		$t_title = $p_coo_data_object->get_data_value('title');
		$this->set_title($t_title);

		$t_link_url = $p_coo_data_object->get_data_value('link_url');
		$this->set_link_url($t_link_url);

		$t_link_target = $p_coo_data_object->get_data_value('link_target');
		$this->set_link_target($t_link_target);
    
    $t_flyover_content = $p_coo_data_object->get_data_value('flyover_content');
		$this->set_flyover_content($t_flyover_content);

		return true;
	}

	/**
	 * @return
	 * @access public
	 */
	function delete()
	{
		if($this->get_slider_image_area_id() === 0)
		{
			trigger_error('cannot delete because slider_image_area_id is missing', E_USER_ERROR);

			return false;
		}

		$coo_slider_image_area = MainFactory::create_object('GMDataObject', array('slider_image_area'));
		$coo_slider_image_area->set_keys(array('slider_image_area_id' => $this->get_slider_image_area_id()));
		$coo_slider_image_area->delete();

		return true;
	}

	/**
	 * @return string
	 * @access public
	 */
	function get_coords()
	{
		return (string)$this->v_coords;
	}

	/**
	 * @param string p_coords
	 * @return
	 * @access public
	 */
	function set_coords($p_coords)
	{
		if(check_data_type($p_coords, 'string'))
		{
			$this->v_coords = $p_coords;
		}
	}

	/**
	 * @return string
	 * @access public
	 */
	function get_title()
	{
		return (string)$this->v_title;
	}

	/**
	 * @param string p_title
	 * @return
	 * @access public
	 */
	function set_title($p_title)
	{
		if(check_data_type($p_title, 'string'))
		{
			$this->v_title = $p_title;
		}
	}

	/**
	 * @return string
	 * @access public
	 */
	function get_link_url()
	{
		return (string)$this->v_link_url;
	}

	/**
	 * @param string p_link_url
	 * @return
	 * @access public
	 */
	function set_link_url($p_link_url)
	{
		if(check_data_type($p_link_url, 'string'))
		{
			$this->v_link_url = $p_link_url;
		}
	}

	/**
	 * @return string
	 * @access public
	 */
	function get_link_target()
	{
		return (string)$this->v_link_target;
	}

	/**
	 * @param string p_link_target
	 * @return
	 * @access public
	 */
	function set_link_target($p_link_target)
	{
		if(check_data_type($p_link_target, 'string'))
		{
			$this->v_link_target = $p_link_target;
		}
	}
  
  /**
	 * @return string
	 * @access public
	 */
	function get_flyover_content()
	{
		return (string)$this->v_flyover_content;
	}

	/**
	 * @param string $p_flyover_content
	 * @return
	 * @access public
	 */
	function set_flyover_content($p_flyover_content)
	{
		if(check_data_type($p_flyover_content, 'string'))
		{
			$t_flyover_content = xtc_db_prepare_input($p_flyover_content);
			$this->v_flyover_content = $t_flyover_content;
		}
	}
}
?>