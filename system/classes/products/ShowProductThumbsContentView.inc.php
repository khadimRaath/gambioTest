<?php
/* --------------------------------------------------------------
  ShowProductThumbsContentView.inc.php 2014-02-27 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(popup_image.php,v 1.12 2001/12/12); www.oscommerce.com
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: show_product_thumbs.php 831 2005-03-13 10:16:09Z mz $)

  Third Party contributions:
  Modified by BIA Solutions (www.biasolutions.com) to create a bordered look to the image

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once(DIR_FS_INC . 'xtc_get_products_mo_images.inc.php');

class ShowProductThumbsContentView extends ContentView
{
	protected $products_id;
	protected $image_id;
	protected $languages_id;

	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/show_product_thumbs.html');
		$this->set_flat_assigns(true);
	}

	protected function set_validation_rules()
	{
		// SET VALIDATION RULES
		$this->validation_rules_array['products_id']	= array('type' => 'int');
		$this->validation_rules_array['image_id']		= array('type' => 'int');
		$this->validation_rules_array['languages_id']	= array('type' => 'int');
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('products_id', 'image_id', 'languages_id'));

		if(empty($t_uninitialized_array))
		{
			$t_images_data_array = array();
			$t_style = '';

			if($this->image_id == 0)
			{
				$t_style = 'background-color: #FF0000;';
			}

			$products_query = xtc_db_query("SELECT 
												pd.products_name, p.products_image 
											FROM 
												" . TABLE_PRODUCTS . " p 
											LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id 
											WHERE 
												p.products_status = '1' AND 
												p.products_id = '" . $this->products_id . "' AND
												pd.language_id = '" . $this->languages_id . "'");
			$products_values = xtc_db_fetch_array($products_query);

			$t_images_data_array[] = array(	'img' => xtc_image(DIR_WS_THUMBNAIL_IMAGES . $products_values['products_image'], $products_values['products_name']),
											'style' => $t_style,
											'image_nr' => 0
									);

			$mo_images = xtc_get_products_mo_images($this->products_id);

			if($mo_images != false)
			{
				foreach($mo_images as $mo_img)
				{
					$t_style = '';
					if($mo_img['image_nr'] == $this->image_id)
					{
						$t_style = 'background-color: #FF0000;';
					}

					$t_images_data_array[] = array('img' => xtc_image(DIR_WS_THUMBNAIL_IMAGES . $mo_img['image_name'], $products_values['products_name']),
													'style' => $t_style,
													'image_nr' => $mo_img['image_nr']);
				}
			}

			$this->content_array['images_data_array'] = $t_images_data_array;
			$this->content_array['PRODUCTS_ID'] = $this->products_id;
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or are null", E_USER_ERROR);
		}
	}
}
