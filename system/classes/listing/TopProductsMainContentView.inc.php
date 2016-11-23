<?php
/* --------------------------------------------------------------
   TopProductsMainContentView.inc.php 2015-10-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(new_products.php,v 1.33 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (new_products.php,v 1.9 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: new_products.php 1292 2005-10-07 16:10:55Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Enable_Disable_Categories 1.3        	Autor: Mikel Williams | mikel@ladykatcostumes.com

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class TopProductsMainContentView extends ContentView
{
	protected $customers_fsk18_display;
	protected $customers_status_id;
	protected $languages_id;


	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/new_products_default.html');
		$this->set_flat_assigns(true);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['customers_fsk18_display']	= array('type' => 'int');
		$this->validation_rules_array['customers_status_id']		= array('type' => 'int');
		$this->validation_rules_array['languages_id']				= array('type' => 'int');
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('customers_status_id',
																		  'languages_id')
		);

		if(empty($t_uninitialized_array))
		{
			$coo_products = MainFactory::create_object('GMDataObject', array('products', array('products_startpage' => 1)));

			if($coo_products->get_result_count() > 0)
			{
				$t_top_products_query = $this->build_sql_query();
				$t_result = xtc_db_query($t_top_products_query);

				if(xtc_db_num_rows($t_result) > 0)
				{
					while($t_new_products = xtc_db_fetch_array($t_result))
					{
						$coo_product = MainFactory::create_object('product', array($t_new_products['products_id']));
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
			$t_fsk_lock = ' AND p.products_fsk18 != 1';
		}

		if(GROUP_CHECK == 'true')
		{
			$t_group_check = ' AND p.group_permission_' . $this->customers_status_id . ' = 1 ';
		}

		$t_new_products_query = "SELECT 
									products_id
								FROM
									" . TABLE_PRODUCTS . " p
								WHERE
									p.products_status = '1' AND
									p.products_startpage = '1'
									" . $t_group_check . "
									" . $t_fsk_lock . " 
								ORDER BY 
									p.products_startpage_sort ASC";

		return $t_new_products_query;
	}
}