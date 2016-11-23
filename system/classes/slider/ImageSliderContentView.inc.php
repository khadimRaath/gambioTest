<?php

/* --------------------------------------------------------------
  ImageSliderContentView.inc.php 2014-08-29 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class ImageSliderContentView extends ContentView
{
	protected $slider_set_id;
	protected $language_id;

	/*
	 * constructor
	 */
	public function __construct() 
	{
		parent::__construct();
		
		$this->set_template('module/image_slider.html');
	}

	public function prepare_data()
	{
		$this->build_html = false;
		
		$t_uninitialized_array = $this->get_uninitialized_variables(array('slider_set_id', 'language_id'));
		
		if(empty($t_uninitialized_array))
		{
			# sliderControl object
			$t_coo_control = MainFactory::create_object('SliderControl');

			# get all sets for search param
			$t_param_array = array('slider_set_id' => $this->slider_set_id);
			$t_slider_set_array = $t_coo_control->get_slider_set_array($t_param_array);

			# if there are sets, parse them down
			$t_set_data_array = array();
			foreach ($t_slider_set_array as $t_coo_set)
			{
				$t_slider_set_id = (int) $t_coo_set->get_slider_set_id();
				$t_slider_set_speed = floor($t_coo_set->get_slider_speed() / 1000);
				$t_slider_set_width = $t_coo_set->get_slider_width();
				$t_slider_set_height = $t_coo_set->get_slider_height();

				$t_set_image_array = $t_coo_control->get_slider_image_array($t_param_array, array('sort_order'));
				$t_slider_image_amount = count($t_set_image_array);

				$t_slider_thumb_width = 60;
				if (!empty($t_slider_image_amount) && $t_slider_image_amount > 10)
				{
					$t_slider_thumb_width = floor($t_slider_set_width / $t_slider_image_amount) - 13;
				}

				foreach ($t_set_image_array as $t_coo_image)
				{
					$t_image_id = $t_coo_image->get_slider_image_id();
					$t_image_file = $t_coo_image->get_image_file();
					$t_image_preview = $t_coo_image->get_preview_file();
					$t_image_title = $t_coo_image->get_image_title($this->language_id);
					$t_image_alt_text = $t_coo_image->get_image_alt_text($this->language_id);
					$t_image_url = $t_coo_image->get_link_url();
					$t_image_target = $t_coo_image->get_link_window_target();
					$t_image_areas = $t_coo_control->get_slider_image_area_array(array('slider_image_id' => $t_image_id));

					$t_image_area_array = array();
					foreach ($t_image_areas as $t_image_area)
					{
						$t_image_area_array[] = array('image_area_id' => $t_image_area->get_slider_image_area_id(),
							'image_area_link_url' => $t_image_area->get_link_url(),
							'image_area_link_target' => $t_image_area->get_link_target(),
							'image_area_title' => htmlspecialchars_wrapper($t_image_area->get_title()),
							'image_area_coords' => $t_image_area->get_coords(),
							'image_area_flyover_content' => htmlspecialchars_wrapper($t_image_area->get_flyover_content()),
							'image_area_shape' => $t_image_area->get_shape()
						);
					}

					$t_set_data_array[] = array('image_id' => $t_image_id,
						'image_large' => $t_image_file,
						'image_preview' => $t_image_preview,
						'image_title_text' => htmlspecialchars_wrapper($t_image_title),
						'image_alt_text' => htmlspecialchars_wrapper($t_image_alt_text),
						'link_url' => $t_image_url,
						'link_target' => $t_image_target,
						'image_area_array' => $t_image_area_array
					);
				}
			}

			if (sizeof($t_set_data_array) > 0)
			{
				# contains dimensions
				$this->content_array['SET_WIDTH'] = $t_slider_set_width;
				$this->content_array['SET_HEIGHT'] = $t_slider_set_height;
				$this->content_array['SET_INTERVAL'] = $t_slider_set_speed;
				$this->content_array['THUMB_WIDTH'] = $t_slider_thumb_width;
				$this->content_array['SLIDER_ID'] = $t_slider_set_id;

				# contains all data for all needed images
				$this->content_array['IMG_DATA'] = $t_set_data_array;
				
				$this->build_html = true;
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or are null", E_USER_ERROR);
		}
	}

	public function set_template($p_template)
	{
		$this->set_content_template($p_template);
		return true;
	}

	protected function set_validation_rules()
	{
		// GENERAL VALIDATION RULES
		$this->validation_rules_array['slider_set_id']	= array('type' => 'int');
		$this->validation_rules_array['language_id']	= array('type' => 'int');
	}
}