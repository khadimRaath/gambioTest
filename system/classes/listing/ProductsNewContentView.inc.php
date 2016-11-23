<?php
/* --------------------------------------------------------------
   ProductsNewContentView.inc.php 2015-05-29 gm
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

class ProductsNewContentView extends ContentView
{
	protected $page = 1;
	protected $customer_fsk18_display;
	protected $customer_status_id;
	protected $language_id;
	protected $currency;
	protected $coo_cache;
	protected $coo_split_page_results;
	protected $coo_product;


	function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/new_products_overview.html');
		$this->set_flat_assigns(true);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['page']					= array('type' => 'int');
		$this->validation_rules_array['customer_fsk18_display']	= array('type' => 'int');
		$this->validation_rules_array['customer_status_id']		= array('type' => 'int');
		$this->validation_rules_array['language_id']			= array('type' => 'int');
		$this->validation_rules_array['currency']				= array('type' => 'string');
		$this->validation_rules_array['coo_cache']				= array('type' => 'object',
																		'object_type' => 'DataCache');
		$this->validation_rules_array['coo_split_page_results']	= array('type' => 'object',
																		'object_type' => 'splitPageResults');
		$this->validation_rules_array['coo_product']			= array('type' => 'object',
																		'object_type' => 'product');
	}
	
	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('language_id', 'currency', 'customer_status_id', 'page'));
		if(empty($t_uninitialized_array))
		{
			if(xtc_not_null(SID))
			{
				$t_use_sid = 'sid_TRUE';
			}
			else
			{
				$t_use_sid = 'sid_FALSE';
			}

			// parameter list for cache matching
			$t_cache_key_source =
				'products_new-' . $this->page . '-' . $this->language_id . '-' . $this->currency . '-' .
				$this->customer_status_id . '-' . $t_use_sid;

			$this->coo_cache = DataCache::get_instance();
			$t_cache_key = $this->coo_cache->build_key($t_cache_key_source);
			
			$this->get_data($t_cache_key);
			
			$this->add_data_to_content_view();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}		
	}
	
	protected function get_data($p_cache_key)
	{
		$t_data_cache_exists = $this->check_cache($p_cache_key);
		if($t_data_cache_exists)
		{
			$this->get_data_from_cache($p_cache_key);
		}
		else
		{
			$this->generate_data();
			$this->save_data_to_cache($p_cache_key);
		}
	}
	
	protected function check_cache($p_cache_key)
	{
		if($this->coo_cache->key_exists($p_cache_key, true))
		{
			return true;
		}
		return false;
	}
	
	protected function get_data_from_cache($p_cache_key)
	{
		// use cached result
		$t_cache_data_array = $this->coo_cache->get_data($p_cache_key);
		$this->content_array['module_content'] = $t_cache_data_array['module_content'];
		$this->coo_split_page_results = $t_cache_data_array['products_new_split'];
	}
	
	protected function generate_data()
	{
		$fsk_lock = '';
		if($this->customer_fsk18_display == '0')
		{
			$fsk_lock = ' and p.products_fsk18!=1';
		}
		if(GROUP_CHECK == 'true')
		{
			$group_check = " and p.group_permission_" . $this->customer_status_id . "=1 ";
		}
		if(MAX_DISPLAY_NEW_PRODUCTS_DAYS != '0')
		{
			$date_new_products = date('Y-m-d', mktime(1, 1, 1, date(m), date(d) - MAX_DISPLAY_NEW_PRODUCTS_DAYS, date(Y)));
			$days              = " and p.products_date_added > '" . $date_new_products . "' ";
		}

		$t_query = "SELECT DISTINCT
						p.products_id													
					FROM 
						( SELECT 
								p.products_id,
								p.products_date_added
							FROM
								" . TABLE_PRODUCTS . " p
							WHERE
								p.products_status = 1
								" . $group_check . "
								" . $fsk_lock . "
						) AS p,
						" . TABLE_PRODUCTS_DESCRIPTION . " pd
					WHERE 
						p.products_id = pd.products_id
						AND pd.language_id = '" . $this->language_id . "'
						" . $days . " 
					ORDER BY
						p.products_date_added DESC";
		
		$this->coo_split_page_results = new splitPageResults($t_query, $this->page, MAX_DISPLAY_PRODUCTS_NEW, 'p.products_id');

		$products_new_query = xtc_db_query($this->coo_split_page_results->sql_query);

		$this->content_array['module_content'] = array();

		while($products_new = xtc_db_fetch_array($products_new_query))
		{
			$this->coo_product = MainFactory::create_object('product', array($products_new['products_id']));
			$this->content_array['module_content'][] = $this->coo_product->buildDataArray($this->coo_product->data);
		}
	}
	
	protected function save_data_to_cache($p_cache_key)
	{
		$t_cache_data_array = array();
		$t_cache_data_array['module_content'] = $this->content_array['module_content'];
		$t_cache_data_array['products_new_split'] = $this->coo_split_page_results;

		$this->coo_cache->set_data($p_cache_key, $t_cache_data_array, true, array('TEMPLATE', 'CHECKOUT'));
	}
	
	protected function add_data_to_content_view()
	{
		$coo_products_new_details = MainFactory::create_object('SplitNavigationContentView');
		$coo_products_new_details->set_('coo_split_page_results', $this->coo_split_page_results);
		$t_view_html = $coo_products_new_details->get_html();

		$this->content_array['NAVIGATION_BAR'] = $t_view_html;
		$this->content_array['NAVIGATION_INFO'] = $this->coo_split_page_results->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS);
	}
}