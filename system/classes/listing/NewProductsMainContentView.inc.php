<?php
/* --------------------------------------------------------------
   NewProductsMainContentView.inc.php 2015-10-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(products_new.php,v 1.25 2003/05/27); www.oscommerce.com
   (c) 2003	 nextcommerce (products_new.php,v 1.16 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: products_new.php 1292 2005-10-07 16:10:55Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Enable_Disable_Categories 1.3        	Autor: Mikel Williams | mikel@ladykatcostumes.com

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed function
require_once(DIR_FS_INC . 'xtc_date_long.inc.php');
require_once(DIR_FS_INC . 'xtc_get_vpe_name.inc.php');

class NewProductsMainContentView extends ContentView
{
	protected $customers_status_id;
	protected $customers_fsk18_display = 0;
	protected $languages_id;
	protected $new_products_count;


	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/products_new_main.html');
		$this->set_flat_assigns(true);
		
		$this->new_products_count = (int)gm_get_conf('GM_NEW_PRODUCTS_STARTPAGE');
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['customers_status_id']		= array('type' => 'int');
		$this->validation_rules_array['customers_fsk18_display']	= array('type' => 'int');
		$this->validation_rules_array['languages_id']				= array('type' => 'int');
		$this->validation_rules_array['new_products_count']			= array('type' => 'int');
	}
	
	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('customers_status_id',
																		  'languages_id')
		);

		if(empty($t_uninitialized_array))
		{
			if($this->new_products_count > 0)
			{
				$t_sql = $this->build_sql_query();
				$t_result = xtc_db_query($t_sql);

				if(xtc_db_num_rows($t_result) > 0)
				{
					while($t_products_new = xtc_db_fetch_array($t_result))
					{
						$coo_product = MainFactory::create_object('product', array($t_products_new['products_id']));
						$this->content_array['module_content'][] = $coo_product->buildDataArray($coo_product->data);
					}

					$this->content_array['showManufacturerImages'] = gm_get_conf('SHOW_MANUFACTURER_IMAGE_LISTING');
					$this->content_array['showProductRibbons'] = gm_get_conf('SHOW_PRODUCT_RIBBONS');
					
					$showRating = false;
					if(gm_get_conf('ENABLE_RATING') === 'true' && gm_get_conf('SHOW_RATING_IN_GRID_AND_LISTING') === 'true')
					{
						$showRating = true;
					}
					$this->content_array['showRating'] = $showRating;
					
					$this->content_array['TRUNCATE_PRODUCTS_NAME'] = gm_get_conf('TRUNCATE_PRODUCTS_NAME');
				}
			}
			else
			{
				$this->build_html = false;
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	protected function build_sql_query()
	{
		$t_fsk_lock = '';
		if($this->customers_fsk18_display == 0)
		{
			$t_fsk_lock = ' AND p.products_fsk18 != 1 ';
		}

		$t_group_check = '';
		if(GROUP_CHECK == 'true')
		{
			$t_group_check = ' AND p.group_permission_' . $this->customers_status_id . ' = 1 ';
		}

		if(MAX_DISPLAY_NEW_PRODUCTS_DAYS != '0')
		{
			$t_date_new_products = date('Y-m-d', mktime(1, 1, 1, date(m), date(d) - (int)MAX_DISPLAY_NEW_PRODUCTS_DAYS, date(Y)));
			$t_days = ' AND p.products_date_added > "' . $t_date_new_products . '" ';
		}
		$t_products_new_query = "SELECT
									p.products_id
								FROM
									(SELECT
										p.products_id
									FROM
										" . TABLE_PRODUCTS . " p
									WHERE
										p.products_status = '1'
										" . $t_group_check . "
										" . $t_fsk_lock . "
										" . $t_days . "
									LIMIT " . (int)MAX_RANDOM_SELECT_NEW . ") AS p
								ORDER BY 
									RAND()
								LIMIT " . $this->new_products_count
		;

		return $t_products_new_query;
	}
}