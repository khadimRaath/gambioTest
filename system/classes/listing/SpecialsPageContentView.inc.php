<?php
/* --------------------------------------------------------------
   SpecialsPageContentView.inc.php 2014-11-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(specials.php,v 1.47 2003/05/27); www.oscommerce.com 
   (c) 2003	 nextcommerce (specials.php,v 1.12 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: specials.php 1292 2005-10-07 16:10:55Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

require_once(DIR_FS_INC . 'xtc_get_short_description.inc.php');

class SpecialsPageContentView extends ContentView
{
	protected $redirect = false;
	protected $coo_product;
	protected $language_id;
	protected $currency;
	protected $customer_status_id;
	protected $page;
	protected $coo_cache;
	protected $coo_split_page_result;

	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/specials.html');
		$this->set_flat_assigns(true);
	}
	
	protected function set_validation_rules()
	{
		// SET VALIDATION RULES
		$this->validation_rules_array['redirect']				= array('type' => 'bool');
		$this->validation_rules_array['coo_product']			= array('type' => 'object',
																		'object_type' => 'product');
		$this->validation_rules_array['language_id']			= array('type' => 'int');
		$this->validation_rules_array['currency']				= array('type' => 'string');
		$this->validation_rules_array['customer_status_id']		= array('type' => 'int');
		$this->validation_rules_array['page']					= array('type' => 'int');
		$this->validation_rules_array['coo_cache']				= array('type' => 'object',
																		'object_type' => 'DataCache');
		$this->validation_rules_array['coo_split_page_result']	= array('type' => 'object',
																		'object_type' => 'splitPageResults');
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
				'specials-' . (int)$this->page . '-' . $this->language_id . '-' . $this->currency . '-' . $this->customer_status_id .
				'-' . $t_use_sid;

			$this->coo_cache = DataCache::get_instance();
			$t_cache_key = $this->coo_cache->build_key($t_cache_key_source);

			$this->get_data($t_cache_key);

			if($this->coo_split_page_result->number_of_rows == 0)
			{
				$this->redirect = true;
				return true;
			}

			$this->add_data_to_content_view();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}

		$this->content_array['showManufacturerImages'] = gm_get_conf('SHOW_MANUFACTURER_IMAGE_LISTING');
		$this->content_array['showProductRibbons']     = gm_get_conf('SHOW_PRODUCT_RIBBONS');

		$showRating = false;
		if(gm_get_conf('ENABLE_RATING') === 'true' && gm_get_conf('SHOW_RATING_IN_GRID_AND_LISTING') === 'true')
		{
			$showRating = true;
		}
		$this->content_array['showRating'] = $showRating;
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
		$this->coo_split_page_result = $t_cache_data_array['specials_split'];
	}
	
	protected function generate_data()
	{
		//fsk18 lock
		$fsk_lock = '';
		if($_SESSION['customers_status']['customers_fsk18_display'] == '0')
		{
			$fsk_lock = ' and p.products_fsk18!=1';
		}
		if(GROUP_CHECK == 'true')
		{
			$group_check = " and p.group_permission_" . $this->customer_status_id . "=1 ";
		}

		$t_query = "SELECT
						p.products_id
					FROM
						(	SELECT
								s.products_id
							FROM
								" . TABLE_SPECIALS . " s
							WHERE
								s.status = '1'
							ORDER BY s.specials_date_added DESC) AS s,
						" . TABLE_PRODUCTS . " p,
						" . TABLE_PRODUCTS_DESCRIPTION . " pd
					WHERE
						p.products_id = s.products_id
						and p.products_status = '1'
						and p.products_id = pd.products_id
						" . $group_check . "
						" . $fsk_lock . "
						and pd.language_id = '" . $this->language_id . "'";

		$this->coo_split_page_result = new splitPageResults($t_query, $this->page, MAX_DISPLAY_SPECIAL_PRODUCTS);

		$specials_query = xtc_db_query($this->coo_split_page_result->sql_query);

		$this->content_array['module_content'] = array();

		while($specials = xtc_db_fetch_array($specials_query))
		{
			$this->coo_product = MainFactory::create_object('product', array($specials['products_id']));
			$this->content_array['module_content'][] = $this->coo_product->buildDataArray($this->coo_product->data);
		}
	}
	
	protected function save_data_to_cache($p_cache_key)
	{
		$t_cache_data_array = array();
		$t_cache_data_array['module_content'] = $this->content_array['module_content'];
		$t_cache_data_array['specials_split'] = $this->coo_split_page_result;

		$this->coo_cache->set_data($p_cache_key, $t_cache_data_array, true, array('TEMPLATE', 'CHECKOUT'));
	}
	
	protected function add_data_to_content_view()
	{
		$coo_products_new_details = MainFactory::create_object('SplitNavigationContentView');
		$coo_products_new_details->set_('coo_split_page_results', $this->coo_split_page_result);
		$t_view_html = $coo_products_new_details->get_html();

		$this->content_array['NAVBAR'] = $t_view_html;
		$this->content_array['NAVIGATION_INFO'] = $this->coo_split_page_result->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS);
		$this->content_array['GM_THUMBNAIL_WIDTH'] = PRODUCT_IMAGE_THUMBNAIL_WIDTH + 10;
	}
}