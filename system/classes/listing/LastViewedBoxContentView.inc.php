<?php
/* --------------------------------------------------------------
   LastViewedBoxContentView.inc.php 2014-07-17 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: last_viewed.php 1292 2005-10-07 16:10:55Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once(DIR_FS_INC . 'xtc_rand.inc.php');
require_once(DIR_FS_INC . 'xtc_get_path.inc.php');
require_once(DIR_FS_INC . 'xtc_get_products_name.inc.php');

class LastViewedBoxContentView extends ContentView
{
	protected $coo_product;
	protected $coo_xtc_price;
	protected $product_data_array = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_last_viewed.html');
		$this->set_caching_enabled(false);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['coo_product']		= array('type' => 'object',
																	'object_type' => 'product');
		$this->validation_rules_array['coo_xtc_price']		= array('type' => 'object',
																	'object_type' => 'xtcPrice');
		$this->validation_rules_array['product_data_array']	= array('type' => 'array');
	}

	public function prepare_data()
	{
		$this->build_html = false;

		if(isset ($_SESSION[tracking][products_history][0]) || $_SESSION['style_edit_mode'] == 'edit')
		{
			$t_random_last_viewed = $this->get_last_viewed();

			//fsk18 lock
			$t_fsk_lock = '';
			if($_SESSION['customers_status']['customers_fsk18_display'] == '0')
			{
				$t_fsk_lock = ' AND p.products_fsk18!=1';
			}
			if(GROUP_CHECK == 'true')
			{
				$t_group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " = 1 ";
			}

			$t_query = "SELECT p.products_id,
							pd.products_name,
							pd.gm_alt_text,
							pd.products_meta_description,
							p.products_price,
							p.products_tax_class_id,
							p.products_image,
							p.products_vpe,
							p.products_vpe_status,
							p.products_vpe_value
						FROM
							" . TABLE_PRODUCTS . " p,
							" . TABLE_PRODUCTS_DESCRIPTION . " pd
						WHERE
							p.products_status = '1'
							AND p.products_id = '" . (int)$_SESSION[tracking][products_history][$t_random_last_viewed] . "'
							AND pd.products_id = '" . (int)$_SESSION[tracking][products_history][$t_random_last_viewed] . "'
							AND pd.language_id = '" . $_SESSION['languages_id'] . "'
							" . $t_group_check . "
							" . $t_fsk_lock . "";

			$t_result               = xtc_db_query($t_query);
			$this->product_data_array = xtc_db_fetch_array($t_result);

			if($this->product_data_array['products_name'] != '' || $_SESSION['style_edit_mode'] == 'edit')
			{
				$t_box_content_array = $this->coo_product->buildDataArray($this->product_data_array);
				$this->content_array['box_content'] = $t_box_content_array;

				$this->build_html = true;
			}
		}
	}
	
	protected function get_last_viewed()
	{
		$t_max = count($_SESSION[tracking][products_history]);
		$t_max--;
		return xtc_rand(0, $t_max);
	}
}