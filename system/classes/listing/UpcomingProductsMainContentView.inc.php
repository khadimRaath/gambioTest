<?php
/* --------------------------------------------------------------
   UpcomingProductsMainContentView.inc.php 2016-01-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(upcoming_products.php,v 1.23 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (upcoming_products.php,v 1.7 2003/08/22); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: upcoming_products.php 1243 2005-09-25 09:33:02Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once(DIR_FS_INC . 'xtc_date_short.inc.php');

class UpcomingProductsMainContentView extends ContentView
{
	protected $customers_status_id;
	protected $customers_fsk18_display = 0;
	protected $languages_id;
	protected $upcoming_products_count = 0;
	
	function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/upcoming_products.html');
		$this->set_flat_assigns(true);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['customers_status_id']		= array('type' => 'int');
		$this->validation_rules_array['customers_fsk18_display']	= array('type' => 'int');
		$this->validation_rules_array['languages_id']				= array('type' => 'int');
		$this->validation_rules_array['upcoming_products_count']	= array('type' => 'int');
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('customers_status_id',
																		  'languages_id')
		);

		if(empty($t_uninitialized_array))
		{
			if($this->upcoming_products_count > 0)
			{
				$t_upcoming_products_query = $this->build_sql_query();
				$t_result = xtc_db_query($t_upcoming_products_query);
				
				if(xtc_db_num_rows($t_result) > 0)
				{
					while($t_upcoming = xtc_db_fetch_array($t_result))
					{
						$coo_seo_boost = MainFactory::create_object('GMSEOBoost');

						if($coo_seo_boost->boost_products)
						{
							$gm_product_link = xtc_href_link($coo_seo_boost->get_boosted_product_url($t_upcoming['products_id'], $t_upcoming['products_name']) );
						}
						else
						{
							$gm_product_link = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($t_upcoming['products_id'], $t_upcoming['products_name']));
						}

						/*
						$this->content_array['module_content'][] = array(
							'PRODUCTS_LINK' => $gm_product_link,
							'PRODUCTS_NAME' => $t_upcoming['products_name'],
							'PRODUCTS_DATE' => xtc_date_short($t_upcoming['date_expected'])
						);
						*/

						$coo_product = MainFactory::create_object('product', array($t_upcoming['products_id']));
						$this->content_array['module_content'][] = $coo_product->buildDataArray($coo_product->data);
						$this->content_array['module_content'][sizeof($this->content_array['module_content']) - 1]['PRODUCTS_TAX_INFO'] = '';
						$this->content_array['module_content'][sizeof($this->content_array['module_content']) - 1]['PRODUCTS_SHIPPING_LINK'] = '';
						$this->content_array['module_content'][sizeof($this->content_array['module_content']) - 1]['PRODUCTS_DATE'] = xtc_date_short($t_upcoming['date_expected']);
						$this->content_array['module_content'][sizeof($this->content_array['module_content']) - 1]['PRODUCTS_NAME'] = $t_upcoming['products_name'];
						
						$this->content_array['showManufacturerImages'] = gm_get_conf('SHOW_MANUFACTURER_IMAGE_LISTING');
						$this->content_array['showProductRibbons']     = gm_get_conf('SHOW_PRODUCT_RIBBONS');

						$showRating = false;
						if(gm_get_conf('ENABLE_RATING') === 'true' && gm_get_conf('SHOW_RATING_IN_GRID_AND_LISTING') === 'true')
						{
							$showRating = true;
						}
						$this->content_array['showRating'] = $showRating;
						
						$this->content_array['TRUNCATE_PRODUCTS_NAME'] = gm_get_conf('TRUNCATE_PRODUCTS_NAME');
					}
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

		if(GROUP_CHECK == 'true')
		{
			$t_group_check = " AND p.group_permission_" . $this->customers_status_id . " = 1 ";
		}

		$t_upcoming_products_query = "SELECT
										p.products_id,
										pd.products_name,
										products_date_available as date_expected
									FROM
										" . TABLE_PRODUCTS . " p,
										" . TABLE_PRODUCTS_DESCRIPTION . " pd
									WHERE
										p.products_status = 1
										AND
										to_days(products_date_available) >= to_days(now()) 
										AND p.products_id = pd.products_id
										" . $t_group_check . "
										" . $t_fsk_lock . " 
										AND pd.language_id = '" . $this->languages_id . "'
									ORDER BY 
										" . EXPECTED_PRODUCTS_FIELD . " " . EXPECTED_PRODUCTS_SORT . "
									LIMIT " . $this->upcoming_products_count
		;
		
		return $t_upcoming_products_query;
	}
}