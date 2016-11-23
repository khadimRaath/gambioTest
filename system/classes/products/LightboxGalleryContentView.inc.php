<?php
/* --------------------------------------------------------------
   LightboxGalleryContentView.inc.php 2014-03-06 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');

class LightboxGalleryContentView extends ContentView
{
	protected $products_id;
	protected $image_nr = 0;
	protected $coo_product;
	protected $image_max_width;
	protected $image_max_height;
	protected $thumbnail_max_width;
	protected $thumbnail_max_height;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/lightbox_gallery.html');
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['products_id']			= array('type' => 'int');
		$this->validation_rules_array['image_nr']				= array('type' => 'int');
		$this->validation_rules_array['image_max_width']		= array('type' => 'int');
		$this->validation_rules_array['image_max_height']		= array('type' => 'int');
		$this->validation_rules_array['thumbnail_max_width']	= array('type' => 'int');
		$this->validation_rules_array['thumbnail_max_height']	= array('type' => 'int');
		$this->validation_rules_array['coo_product']			= array('type' => 'object',
																		'object_type' => 'product');
	}
	
	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('products_id'));

		if(empty($t_uninitialized_array))
		{
			$this->get_data();
			$this->add_data();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
	
	protected function get_data()
	{
		$this->coo_product = new product($this->products_id);
		
		$this->set_dimension();

		// PRODUCT IMAGE
		$t_image_data_array = $this->get_image_data($this->coo_product->data['products_image']);
		$this->add_image_data($t_image_data_array);
		
		
		// MO PICS
		$t_more_images = xtc_get_products_mo_images($this->coo_product->data['products_id']);
		if($t_more_images != false)
		{
			foreach($t_more_images as $t_image_array)
			{
				$t_image_data_array = $this->get_image_data($t_image_array['image_name']);
				$t_image_data_array['IMAGE_NR'] = (int)$t_image_array['image_nr'];
				$this->add_image_data($t_image_data_array);
			}
		}
	}
	
	protected function get_image_data($p_image_name)
	{
		// PRODUCT IMAGE
		$t_image_url = DIR_WS_POPUP_IMAGES . $p_image_name;
		$t_info_image_size_array = @getimagesize($t_image_url);
		
		$t_padding_left = 0;
		$t_padding_top	= 0;

		if(isset($t_info_image_size_array[0]) && $t_info_image_size_array[0] < $this->image_max_width)
		{
			$t_padding_left = round(($this->image_max_width - $t_info_image_size_array[0]) / 2);
		}

		if(isset($t_info_image_size_array[1]) && $t_info_image_size_array[1] < $this->image_max_height)
		{
			$t_padding_top = round(($this->image_max_height - $t_info_image_size_array[1]) / 2);
		}

		// THUMBNAILS
		$t_thumbnail_url = DIR_WS_IMAGES . 'product_images/gallery_images/' . $p_image_name;
		$t_info_thumbnail_size_array = @getimagesize($t_thumbnail_url);

		$t_thumbnail_padding_left = 0;
		$t_thumbnail_padding_top = 0;

		if(isset($t_info_thumbnail_size_array[0]) && $t_info_thumbnail_size_array[0] < $this->thumbnail_max_width)
		{
			$t_thumbnail_padding_left = round(($this->thumbnail_max_width - $t_info_thumbnail_size_array[0]) / 2);
		}

		if(isset($t_info_thumbnail_size_array[1]) && $t_info_thumbnail_size_array[1] < $this->thumbnail_max_height)
		{
			$t_thumbnail_padding_top = round(($this->thumbnail_max_height - $t_info_thumbnail_size_array[1]) / 2);
		}

		$t_image_data_array = array(
			'IMAGE' => $t_image_url,
			'THUMBNAIL' => $t_thumbnail_url,
			'IMAGE_NR' => 0,
			'PRODUCTS_NAME' => $p_image_name,
			'PADDING_LEFT' => $t_padding_left,
			'PADDING_TOP' => $t_padding_top,
			'THUMBNAIL_PADDING_LEFT' => $t_thumbnail_padding_left,
			'THUMBNAIL_PADDING_TOP' => $t_thumbnail_padding_top
		);
		
		return $t_image_data_array;
	}
	
	protected function add_image_data(array $p_image_data)
	{
		$this->content_array['images_data'][] = $p_image_data;
	}

	protected function add_data()
	{
		$this->content_array['IMAGE_MAX_WIDTH'] = PRODUCT_IMAGE_POPUP_WIDTH;
		$this->content_array['IMAGE_MAX_HEIGHT'] = PRODUCT_IMAGE_POPUP_HEIGHT;

		$t_gallery_width = (int)PRODUCT_IMAGE_POPUP_WIDTH + 200;
		$this->content_array['GALLERY_WIDTH'] = $t_gallery_width;
	}

	protected function set_dimension()
	{
		$this->image_max_width = PRODUCT_IMAGE_POPUP_WIDTH;
		$this->image_max_height = PRODUCT_IMAGE_POPUP_HEIGHT;
		$this->thumbnail_max_width = 86;
		$this->thumbnail_max_height = 86;
	}
}