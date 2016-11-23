<?php
/* --------------------------------------------------------------
   WhatsNewBoxContentView.inc.php 2015-03-13 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(whats_new.php,v 1.31 2003/02/10); www.oscommerce.com
   (c) 2003	 nextcommerce (whats_new.php,v 1.12 2003/08/21); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: whats_new.php 1292 2005-10-07 16:10:55Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Enable_Disable_Categories 1.3        	Autor: Mikel Williams | mikel@ladykatcostumes.com

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once(DIR_FS_INC . 'xtc_random_select.inc.php');
require_once(DIR_FS_INC . 'xtc_rand.inc.php');
require_once(DIR_FS_INC . 'xtc_get_products_name.inc.php');

class WhatsNewBoxContentView extends ContentView
{
	protected $coo_product;
	protected $products_id = 0;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_whatsnew.html');
		$this->set_caching_enabled(false);
	}
	
	protected function set_validation_rules()
	{
		// SET VALIDATION RULES
		$this->validation_rules_array['coo_product']	= array('type' => 'object',
																'object_type' => 'product');
		$this->validation_rules_array['products_id']	= array('type' => 'int');
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_product'));
		if(empty($t_uninitialized_array))
		{
			$this->build_html = false;
			//fsk18 lock
			$t_fsk_lock = '';
			if($_SESSION['customers_status']['customers_fsk18_display'] == '0')
			{
				$t_fsk_lock = ' AND p.products_fsk18 != 1';
			}

			if(GROUP_CHECK == 'true')
			{
				$t_group_check =
					" AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " = 1 ";
			}

			if(MAX_DISPLAY_NEW_PRODUCTS_DAYS != '0')
			{
				$t_date_new_products = date('Y.m.d', mktime(1, 1, 1, date(m), date(d) - MAX_DISPLAY_NEW_PRODUCTS_DAYS, date(Y)));
				$t_days              = " AND p.products_date_added > '" . $t_date_new_products . "' ";
			}

			$t_query = "SELECT DISTINCT
							p.products_id,
							pd.products_name,
							pd.gm_alt_text,
							pd.products_meta_description,
							p.products_image,
							p.products_tax_class_id,
							p.products_vpe,
							p.products_vpe_status,
							p.products_vpe_value,
							p.products_price
						FROM
							(   SELECT
									p.products_id,
									p.products_image,
									p.products_tax_class_id,
									p.products_vpe,
									p.products_vpe_status,
									p.products_vpe_value,
									p.products_price,
									p.products_status,
									p.products_date_added
								FROM " . TABLE_PRODUCTS . " p
								WHERE
									p.products_status = 1
									" . $t_days . "
									" . $t_group_check . "
									" . $t_fsk_lock . "
								ORDER BY p.products_date_added DESC
								LIMIT 1000
							) AS p,
							" . TABLE_PRODUCTS_DESCRIPTION . " pd
						WHERE
							p.products_id = pd.products_id
							AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
							AND p.products_id != '" . $this->products_id . "'
							AND TRIM(pd.products_name) != '' 
							AND pd.products_name is not null
						LIMIT " . MAX_RANDOM_SELECT_NEW;

			$t_random_product_array = xtc_random_select($t_query);

			if((isset($t_random_product_array['products_name']) && $t_random_product_array['products_name'] != '') ||
			   $_SESSION['style_edit_mode'] == 'edit'
			)
			{
				$t_box_content_array = $this->coo_product->buildDataArray($t_random_product_array);
				$this->content_array['box_content'] = $t_box_content_array;
				$this->content_array['LINK_NEW_PRODUCTS'] = xtc_href_link(FILENAME_PRODUCTS_NEW);
				$this->build_html = true;
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
}