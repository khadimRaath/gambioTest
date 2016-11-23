<?php
/* --------------------------------------------------------------
   ProductListingContentControl.inc.php 2016-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_listing.php,v 1.42 2003/05/27); www.oscommerce.com
   (c) 2002-2003 osCommerce(advanced_search_result.php,v 1.68 2003/05/14); www.oscommerce.com
   (c) 2002-2003 osCommerce(default.php,v 1.84 2003/05/07); www.oscommerce.com
   (c) 2003	 nextcommerce (product_listing.php,v 1.19 2003/08/1); www.nextcommerce.org
   (c) 2003	 nextcommerce (advanced_search_result.php,v 1.17 2003/08/21); www.nextcommerce.org
   (c) 2003  nextcommerce (default.php,v 1.11 2003/08/22); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_listing.php 1286 2005-10-07 10:10:18Z mz $)
   (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: advanced_search_result.php 1141 2005-08-10 11:31:36Z novalis $)
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: default.php 1292 2005-10-07 16:10:55Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Enable_Disable_Categories 1.3        Autor: Mikel Williams | mikel@ladykatcostumes.com
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs...by=date#dirlist

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once(DIR_FS_INC . 'xtc_check_categories_status.inc.php');
require_once(DIR_FS_INC . 'xtc_get_all_get_params.inc.php');
require_once(DIR_FS_INC . 'xtc_get_currencies_values.inc.php');
require_once(DIR_FS_INC . 'xtc_get_path.inc.php');
require_once(DIR_FS_INC . 'xtc_get_subcategories.inc.php');
require_once(DIR_FS_INC . 'xtc_get_vpe_name.inc.php');
require_once(DIR_FS_INC . 'xtc_parse_search_string.inc.php');

/**
 * Class ProductListingContentControl
 */
class ProductListingContentControl extends DataProcessing
{
	protected $c_path;
	protected $cache_id_parameter_array;
	protected $cat;
	protected $categories_id;
	protected $coo_filter_manager;
	protected $coo_product;
	protected $currency_code;
	protected $current_category_id;
	protected $current_page;
	protected $customer_country_id;
	protected $customer_zone_id;
	protected $customers_fsk18_display;
	protected $customers_status_id;
	protected $feature_categories_id;
	protected $filter_fv_id;
	protected $filter_id;
	protected $filter_price_max;
	protected $filter_price_min;
	protected $filter_selection_html;
	protected $include_subcategories_for_search;
	protected $languages_id;
	protected $last_listing_sql;
	protected $listing_count;
	protected $listing_page_image;
	protected $listing_sort;
	protected $manufacturers_data_array;
	protected $manufacturers_dropdown;
	protected $manufacturers_id;
	protected $page_number;
	protected $price_from;
	protected $price_to;
	protected $search_keywords;
	protected $show_graduated_prices;
	protected $show_price_tax;
	protected $sort;
	protected $sql_query;
	protected $value_conjunction;
	protected $view_mode;
	protected $product_ids;
	
	protected $productListingTemplatePath = null;

	public function __construct()
	{
		parent::__construct();
	}

	protected function set_filter_fv_id($p_filter_fv_id)
	{
		if(is_array($p_filter_fv_id))
		{
			$this->filter_fv_id = $p_filter_fv_id;
		}
		elseif(check_data_type($p_filter_fv_id, 'int'))
		{
			$this->filter_fv_id = $p_filter_fv_id;
		}
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['c_path']                           = array('type' => 'string');
		$this->validation_rules_array['cache_id_parameter_array']         = array('type' => 'array');
		$this->validation_rules_array['cat']                              = array('type' => 'string');
		$this->validation_rules_array['categories_id']                    = array('type' => 'int');
		$this->validation_rules_array['coo_filter_manager']               = array('type'        => 'object',
																				  'object_type' => 'FilterManager'
		);
		$this->validation_rules_array['coo_product']                      = array('type'        => 'object',
																				  'object_type' => 'product'
		);
		$this->validation_rules_array['currency_code']                    = array('type' => 'string');
		$this->validation_rules_array['current_category_id']              = array('type' => 'int');
		$this->validation_rules_array['current_page']                     = array('type' => 'string');
		$this->validation_rules_array['customer_country_id']              = array('type' => 'int');
		$this->validation_rules_array['customer_zone_id']                 = array('type' => 'int');
		$this->validation_rules_array['customers_fsk18_display']          = array('type' => 'int');
		$this->validation_rules_array['customers_status_id']              = array('type' => 'int');
		$this->validation_rules_array['feature_categories_id']            = array('type' => 'int');
		$this->validation_rules_array['filter_id']                        = array('type' => 'int');
		$this->validation_rules_array['filter_price_max']                 = array('type' => 'string');
		$this->validation_rules_array['filter_price_min']                 = array('type' => 'string');
		$this->validation_rules_array['filter_selection_html']            = array('type' => 'string');
		$this->validation_rules_array['include_subcategories_for_search'] = array('type' => 'int');
		$this->validation_rules_array['languages_id']                     = array('type' => 'int');
		$this->validation_rules_array['last_listing_sql']                 = array('type' => 'string');
		$this->validation_rules_array['listing_count']                    = array('type' => 'int');
		$this->validation_rules_array['listing_sort']                     = array('type' => 'string');
		$this->validation_rules_array['manufacturers_data_array']         = array('type' => 'array');
		$this->validation_rules_array['manufacturers_dropdown']           = array('type' => 'string');
		$this->validation_rules_array['manufacturers_id']                 = array('type' => 'int');
		$this->validation_rules_array['page_number']                      = array('type' => 'int');
		$this->validation_rules_array['price_from']                       = array('type' => 'string');
		$this->validation_rules_array['price_to']                         = array('type' => 'string');
		$this->validation_rules_array['sort']                             = array('type' => 'string');
		$this->validation_rules_array['search_keywords']                  = array('type' => 'string');
		$this->validation_rules_array['show_graduated_prices']            = array('type' => 'bool');
		$this->validation_rules_array['show_price_tax']                   = array('type' => 'int');
		$this->validation_rules_array['sql_query']                        = array('type' => 'string');
		$this->validation_rules_array['value_conjunction']                = array('type' => 'array');
		$this->validation_rules_array['view_mode']                        = array('type' => 'string');
		$this->validation_rules_array['product_ids']                      = array('type' => 'array');
	}

	public function proceed($p_action = 'default')
	{
		$t_html_output = '';

		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_product',
																		  'current_category_id',
																		  'current_page',
																		  'customers_status_id',
																		  'languages_id',
																	));

		if(empty($t_uninitialized_array))
		{
			switch($p_action)
			{
				case 'search_result':
					$this->build_search_result_sql();

					break;
				default:

					if(xtc_check_categories_status($this->current_category_id) >= 1)
					{
						$this->v_output_buffer = $this->get_error_html_output(CATEGORIE_NOT_FOUND);
						return true;
					}

					$this->init_feature_filter();
					$t_category_depth = $this->determine_category_depth();

					switch($t_category_depth)
					{
						case 'top':
							// start page
							$this->v_output_buffer = $this->get_start_page_html_output();
							return true;

							break;
						case 'nested':
							$t_html_output = $this->get_category_listing_html_output();

							// no break;
						default:
							$this->build_sql_query();
					}

			}

			$this->extend_proceed($p_action);

			if(empty($this->sql_query))
			{
				return true;
			}

			$t_max_display_search_results = $this->determine_max_display_search_results();

			// save last listing query for ProductNavigator ($_SESSION['last_listing_sql'])
			$this->last_listing_sql = $this->sql_query;

			$coo_listing_split = new splitPageResults($this->sql_query, $this->page_number, $t_max_display_search_results, 'p.products_id');
			$t_products_array = array();

			if($coo_listing_split->number_of_rows > 0)
			{
				$t_category_data_array = $this->get_category_data_array();

				$t_category_name = $t_category_data_array['name'];
				$t_category_heading_title = $t_category_data_array['heading_title'];
				$t_category_image_alt_text = $t_category_data_array['image_alt_text'];
				$t_category_image = $t_category_data_array['image'];
				$t_categories_description = $t_category_data_array['description'];
				$t_show_quantity = $t_category_data_array['show_quantity'];
				$t_category_show_quantity = $t_category_data_array['category_show_quantity'];

				$coo_navigation_view = MainFactory::create_object('SplitNavigationContentView');
				$coo_navigation_view->set_('coo_split_page_results', $coo_listing_split);
				$t_navigation_html = $coo_navigation_view->get_html();

				$t_rows_count = 0;
				$t_query = $coo_listing_split->sql_query;
				$t_result = xtc_db_query($t_query);

				while($t_product_array = xtc_db_fetch_array($t_result))
				{
					$t_rows_count++;

					// check if product has properties
					$t_query    = 'SELECT COUNT(*) AS count FROM products_properties_combis WHERE products_id = ' . $t_product_array['products_id'];
					$t_combis_result = xtc_db_query($t_query);
					$t_count_combis_array    = xtc_db_fetch_array($t_combis_result);

					if($t_count_combis_array['count'] > 0)
					{
						$t_product_has_properties = true;
					}
					else
					{
						$t_product_has_properties = false;
					}

					$GLOBALS['xtPrice']->showFrom_Attributes = true;
					if(((gm_get_conf('MAIN_SHOW_ATTRIBUTES') == 'true' && isset($t_category_data_array['gm_show_attributes']) == false)
					    ||
					    $t_category_data_array['gm_show_attributes'] == '1') && $t_product_has_properties == false
					)
					{
						$GLOBALS['xtPrice']->showFrom_Attributes = false;
					}

					$t_products_array[] = $this->coo_product->buildDataArray($t_product_array);
					$coo_product = new product($t_product_array['products_id']);

					$t_attributes_html = '';
					if(((gm_get_conf('MAIN_SHOW_ATTRIBUTES') == 'true' && isset($t_category_data_array['gm_show_attributes']) == false)
						||
						$t_category_data_array['gm_show_attributes'] == '1') && $t_product_has_properties == false
					)
					{
						// CREATE ProductAttributesContentView OBJECT
						$coo_product_attributes = MainFactory::create_object('ProductAttributesContentView');

						// SET TEMPLATE
						$t_filepath   = DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/gm_product_options/';
						$c_template = $coo_product_attributes->get_default_template($t_filepath, $coo_product->data['gm_options_template']);
						$coo_product_attributes->set_content_template('module/gm_product_options/' . $c_template);

						// SET DATA
						$coo_product_attributes->set_coo_product($coo_product);
						$coo_product_attributes->set_language_id($_SESSION['languages_id']);

						// GET HTML
						$t_attributes_html = $coo_product_attributes->get_html();
					}

					$t_graduated_prices_html = '';
					if($t_category_data_array['show_graduated_prices'])
					{
						$coo_graduated_prices = MainFactory::create_object('GraduatedPricesContentView');
						$coo_graduated_prices->set_coo_product($coo_product);
						$coo_graduated_prices->set_customers_status_graduated_prices($_SESSION['customers_status']['customers_status_graduated_prices']);
						$coo_graduated_prices->set_content_template('module/gm_graduated_price.html');
						$t_graduated_prices_html = $coo_graduated_prices->get_html();
					}

					if(xtc_has_product_attributes($t_product_array['products_id']))
					{
						$gm_has_attributes = 1;
					}
					else
					{
						$gm_has_attributes = 0;
					}

					$t_products_array[sizeof($t_products_array) - 1] = array_merge($t_products_array[sizeof($t_products_array) - 1],
																							   array('GM_ATTRIBUTES'       => $t_attributes_html,
																									 'GM_GRADUATED_PRICES' => $t_graduated_prices_html,
																									 'GM_HAS_ATTRIBUTES'   => $gm_has_attributes
																							   ));

					if(empty($coo_product->data['quantity_unit_id']) == false
					   && (!$t_product_has_properties || ($t_product_has_properties && $coo_product->data['use_properties_combis_quantity'] == '0'))
					)
					{
						$t_products_array[sizeof($t_products_array) - 1] = array_merge($t_products_array[sizeof($t_products_array) - 1],
																								   array('UNIT' => $coo_product->data['unit_name']));
					}

					if($t_category_show_quantity)
					{
						if(empty($coo_product->data['gm_show_qty_info']) == false
						   && (!$t_product_has_properties || ($t_product_has_properties && $coo_product->data['use_properties_combis_quantity'] == '0'))
						)
						{
							$t_products_array[sizeof($t_products_array) - 1] = array_merge($t_products_array[sizeof($t_products_array) - 1],
																									   array('PRODUCTS_QUANTITY' => $coo_product->data['products_quantity']));
						}
					}

					if($t_product_has_properties)
					{
						$t_products_array[sizeof($t_products_array) - 1]['SHOW_PRODUCTS_WEIGHT'] = 0;
					}

					$t_products_array[sizeof($t_products_array) - 1] = array_merge($t_products_array[sizeof($t_products_array) - 1],
																									array('ABROAD_SHIPPING_INFO_LINK' => main::get_abroad_shipping_info_link()));

					$this->add_product_data($t_products_array, $t_product_array, $coo_product);

					unset($products_options_data);
				}

				if(isset($t_category_data_array['view_mode_tiled']))
				{
					$t_view_mode = $this->determine_view_mode($t_category_data_array['view_mode_tiled']);
				}
				else
				{
					$t_view_mode = $this->determine_view_mode();
				}

				$this->build_cache_id_parameter_array(array($t_view_mode));

				$coo_product_listing_view = MainFactory::create_object('ProductListingContentView', array($t_category_data_array['listing_template']));

				$coo_product_listing_view->set_('cache_id_parameter_array', $this->cache_id_parameter_array);
				$coo_product_listing_view->set_('category_description', $t_categories_description);
				$coo_product_listing_view->set_('category_heading_title', $t_category_heading_title);
				$coo_product_listing_view->set_('category_image_alt_text', $t_category_image_alt_text);
				$coo_product_listing_view->set_('category_image', $t_category_image);
				$coo_product_listing_view->set_('category_name', $t_category_name);

				if(isset($this->filter_selection_html))
				{
					$coo_product_listing_view->set_('filter_selection_html', $this->filter_selection_html);
				}

				// De-duplicate multidimensional array (@link http://stackoverflow.com/a/946300)
				$t_hidden_get_params_array = array_map('unserialize', array_unique(array_map('serialize', $this->build_hidden_get_params_array())));
				$coo_product_listing_view->set_('get_params_hidden_data_array', array_values($t_hidden_get_params_array));

				if(isset($this->listing_count))
				{
					$coo_product_listing_view->set_('listing_count', $this->listing_count);
				}

				if(isset($this->listing_sort))
				{
					$coo_product_listing_view->set_('listing_sort', $this->listing_sort);
				}

				if(isset($this->manufacturers_id))
				{
					$coo_product_listing_view->set_('manufacturers_id', $this->manufacturers_id);
				}

				if(isset($this->manufacturers_data_array))
				{
					$coo_product_listing_view->set_('manufacturers_data_array', $this->manufacturers_data_array);
				}

				if(isset($this->manufacturers_dropdown))
				{
					$coo_product_listing_view->set_('manufacturers_dropdown', $this->manufacturers_dropdown);
				}

				$coo_product_listing_view->set_('navigation_html', $t_navigation_html);
				$coo_product_listing_view->set_('navigation_info_html', $coo_listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS));
				$coo_product_listing_view->set_('products_array', $t_products_array);
				$coo_product_listing_view->set_('products_per_page', (int)MAX_DISPLAY_SEARCH_RESULTS);

				if(isset($this->search_keywords))
				{
					$coo_product_listing_view->set_('search_keywords', $this->search_keywords);
				}

				$coo_product_listing_view->set_('show_quantity', $t_show_quantity);
				$coo_product_listing_view->set_('thumbnail_width', PRODUCT_IMAGE_THUMBNAIL_WIDTH + 10);

				$t_page_url_array = explode('?', gm_get_env_info('REQUEST_URI'));

				$coo_product_listing_view->set_('sorting_form_action_url', $t_page_url_array[0]);
				$coo_product_listing_view->set_('view_mode', $t_view_mode);

				$coo_product_listing_view->set_('view_mode_url_default', $this->build_view_mode_url('default'));
				$coo_product_listing_view->set_('view_mode_url_tiled', $this->build_view_mode_url('tiled'));

				$showRating = false;
				if(gm_get_conf('ENABLE_RATING') === 'true' && gm_get_conf('SHOW_RATING_IN_GRID_AND_LISTING') === 'true')
				{
					$showRating = true;
				}
				
				$coo_product_listing_view->set_('showRating', $showRating);
				$coo_product_listing_view->set_('showManufacturerImages', gm_get_conf('SHOW_MANUFACTURER_IMAGE_LISTING'));
				$coo_product_listing_view->set_('showProductRibbons', gm_get_conf('SHOW_PRODUCT_RIBBONS'));

				if($this->productListingTemplatePath !== null)
				{
					$t_html_output = '';
					$coo_product_listing_view->set_content_template($this->productListingTemplatePath);
				}
				
				$t_html_output .= $coo_product_listing_view->get_html();
			}
			elseif(GM_CAT_COUNT == 0) // GM_CAT_COUNT > 0: products FALSE, sub-categories TRUE
			{
				$t_html_output = $this->get_error_html_output(TEXT_PRODUCT_NOT_FOUND);
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}

		$this->v_output_buffer = $t_html_output;
	}

	public function build_search_result_sql()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('currency_code',
																		  'customer_country_id',
																		  'customer_zone_id',
																		  'customers_fsk18_display',
																		  'customers_status_id',
																		  'languages_id',
																		  'search_keywords',
																		  'show_price_tax',
																	));
		if(empty($t_uninitialized_array))
		{
			/*
			 * check search entry
			 */

			$t_error = 0; // reset error flag to false
			$t_error_count = 0;
			$t_keyerror = 0;

			if((isset($this->search_keywords) && empty($this->search_keywords)) && (isset($this->price_from) && empty($this->price_from)) && (isset($this->price_to) && empty($this->price_to)))
			{
				$t_keyerror = 1;
				$t_error_count += 1;
				$t_error = 1;
			}
			elseif(isset($this->search_keywords) && empty($this->search_keywords) && !(isset($this->price_from)) && !(isset($this->price_to)))
			{
				$t_keyerror = 1;
				$t_error_count += 1;
				$t_error = 1;
			}

			if(strlen_wrapper($this->search_keywords) < 3 && strlen_wrapper($this->search_keywords) > 0 && $t_error == 0 
			   && strlen($this->price_from) === 0 && strlen($this->price_to) === 0)
			{
				$t_error_count += 1;
				$t_error = 1;
				$t_keyerror = 1;
			}

			if(strlen_wrapper($this->price_from) > 0)
			{
				$t_price_from_to_check = xtc_db_input($this->price_from);
				if(!settype($t_price_from_to_check, "double"))
				{
					$t_error_count += 10000;
					$t_error = 1;
				}
			}

			if(strlen_wrapper($this->price_to) > 0)
			{
				$t_price_to_to_check = $this->price_to;
				if(!settype($t_price_to_to_check, "double"))
				{
					$t_error_count += 100000;
					$t_error = 1;
				}
			}

			if(strlen_wrapper($this->price_from) > 0 && !(($t_error_count & 10000) == 10000) && strlen_wrapper($this->price_to) > 0 && !(($t_error_count & 100000) == 100000))
			{
				if($t_price_from_to_check > $t_price_to_to_check)
				{
					$t_error_count += 1000000;
					$t_error = 1;
				}
			}

			if(strlen_wrapper($this->search_keywords) > 0)
			{
				if(!xtc_parse_search_string(stripslashes($this->search_keywords), $t_search_keywords_array))
				{
					$t_error_count += 10000000;
					$t_error = 1;
					$t_keyerror = 1;
				}
			}

			if($t_error == 1 && $t_keyerror != 1)
			{
				$this->set_redirect_url(xtc_href_link(FILENAME_ADVANCED_SEARCH, 'errorno=' . $t_error_count . '&amp;' . xtc_get_all_get_params(array('x', 'y'))));
				return true;
			}

			/*
			 *    search process starts here
			 */

			// define additional filters //
			//fsk18 lock
			$t_fsk_lock = '';
			if($this->customers_fsk18_display == '0')
			{
				$t_fsk_lock = " AND p.products_fsk18 != '1' ";
			}

			//group check
			$t_group_check = '';
			if(GROUP_CHECK == 'true')
			{
				$t_group_check = " AND p.group_permission_" . $this->customers_status_id . " = 1 ";
			}

			//manufacturers if set
			$t_manufacturer_check = '';
			if(isset($this->manufacturers_id) && xtc_not_null($this->manufacturers_id))
			{
				$t_manufacturer_check = " AND p.manufacturers_id = '" . $this->manufacturers_id . "' ";
			}

			//include subcategories if needed
			if(isset($this->categories_id) && xtc_not_null($this->categories_id))
			{
				if($this->include_subcategories_for_search == '1')
				{
					$t_subcategories_array = array();
					xtc_get_subcategories($t_subcategories_array, $this->categories_id);
					$t_subcat_join = " LEFT OUTER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS p2c ON (p.products_id = p2c.products_id) ";
					$t_subcat_where = " AND p2c.categories_id IN ('" . $this->categories_id . "' ";

					foreach($t_subcategories_array AS $t_category_id)
					{
						$t_subcat_where .= ", '" . $t_category_id . "'";
					}

					$t_subcat_where .= ") ";
				}
				else
				{
					$t_subcat_join = " LEFT OUTER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS p2c ON (p.products_id = p2c.products_id) ";
					$t_subcat_where = " AND p2c.categories_id = '" . $this->categories_id . "' ";
				}
			}

			if($this->price_from || $this->price_to)
			{
				$t_rate = xtc_get_currencies_values($this->currency_code);
				$t_rate = $t_rate['value'];

				if($t_rate && $this->price_from != '')
				{
					$t_price_from = $this->price_from / $t_rate;
				}

				if($t_rate && $this->price_to != '')
				{
					$t_price_to = $this->price_to / $t_rate;
				}
			}

			//price filters
			$t_price_from_check = '';
			$t_price_to_check = '';

			if($this->show_price_tax != 0)
			{
				if(($t_price_from != '') && (is_numeric($t_price_from)))
				{
					$t_price_from_check = " AND (IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) * (tax_rate/100+1) >= " . xtc_db_input($t_price_from) . ") ";
				}

				if(($t_price_to != '') && (is_numeric($t_price_to)))
				{
					$t_price_to_check = " AND (IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) * (tax_rate/100+1) <= " . xtc_db_input($t_price_to) . " ) ";
				}
			}
			else
			{
				if(($t_price_from != '') && (is_numeric($t_price_from)))
				{
					$t_price_from_check = " AND (IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) >= " . xtc_db_input($t_price_from) . ") ";
				}

				if(($t_price_to != '') && (is_numeric($t_price_to)))
				{
					$t_price_to_check = " AND (IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) <= " . xtc_db_input($t_price_to) . " ) ";
				}
			}
			
			if((!isset($this->search_keywords) || !xtc_not_null($this->search_keywords))
			                                     && (is_numeric($t_price_from) || is_numeric($t_price_to)))
			{
				$this->search_keywords = '%';
			}

			// sorting
			if(isset($this->listing_sort))
			{
				$coo_listing_manager = MainFactory::create_object('ListingManager');
				$t_order_by = $coo_listing_manager->get_sql_sort_part($this->listing_sort);
			}
			else if(empty($this->product_ids) !== true)
			{
				$t_order_by = 'ORDER BY FIELD(`p`.`products_id`, '.implode(',', $this->product_ids).')';
			}

			$t_select_part = '';
			if(strpos($t_order_by, 'p.products_price') !== false)
			{
				if($this->show_price_tax != 0)
				{
					$t_select_part = ", ROUND(IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) * (IF(p.products_tax_class_id = 0,0,tax_rate)/100+1), 2) AS final_price ";
				}
				else
				{
					$t_select_part = ", ROUND(IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price), 2) AS final_price ";
				}

				$t_order_by = str_replace('p.products_price', 'final_price', $t_order_by);
			}

			//build query
			$t_select = "SELECT distinct
					  p.products_id,
					  p.products_price,
					  p.products_model,
					  p.products_quantity,
					  p.products_shippingtime,
					  p.products_fsk18,
					  p.products_image,
					p.products_image_w, 
					p.products_image_h,
					  p.products_weight,
					  p.gm_show_weight,
					  p.products_tax_class_id,
					  p.products_vpe,
					  p.products_vpe_status,
					  p.products_vpe_value,
					  pd.products_name,
					  pd.products_short_description,
					  pd.products_description "
						  . $t_select_part;

			$t_from = "FROM " . TABLE_PRODUCTS . " AS p LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON (p.products_id = pd.products_id) ";
			$t_from .= $t_subcat_join;

			if(SEARCH_IN_ATTR == 'true')
			{
				$t_from .= " LEFT OUTER JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " AS pa ON (p.products_id = pa.products_id)
							LEFT OUTER JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " AS pov ON (pa.options_values_id = pov.products_options_values_id)
							LEFT OUTER JOIN products_properties_combis AS ppc ON (p.products_id = ppc.products_id)
							LEFT OUTER JOIN products_properties_index AS ppi ON (p.products_id = ppi.products_id) ";
			}

			$t_from .= "LEFT OUTER JOIN " . TABLE_SPECIALS . " AS s ON (p.products_id = s.products_id AND s.status = '1')";

			$tax_where = '';
			if(($this->show_price_tax != 0) && ((isset($this->price_from) && xtc_not_null($this->price_from)) || (isset($this->price_to) && xtc_not_null($this->price_to)) || $t_select_part != ''))
			{
				$t_from .= "LEFT JOIN " . TABLE_TAX_RATES . " AS tr ON (p.products_tax_class_id = tr.tax_class_id OR p.products_tax_class_id = 0)
							LEFT JOIN " . TABLE_ZONES_TO_GEO_ZONES . " AS gz ON (tr.tax_zone_id = gz.geo_zone_id AND gz.zone_country_id = '" . $this->customer_country_id . "') ";
				$tax_where = " AND (gz.zone_id = '0' OR gz.zone_id = '" . $this->customer_zone_id . "') ";
			}

			//where-string
			$t_where = " WHERE p.products_status = '1' " . " AND pd.language_id = '" . $this->languages_id . "'" . $t_subcat_where . $t_fsk_lock . $t_manufacturer_check . $t_group_check . $tax_where . $t_price_from_check . $t_price_to_check;

			if(empty($this->product_ids) !== true)
			{
				// show list of specific products (mostly for external search providers)
				$t_where .= ' AND p.products_id IN ('.implode(',', $this->product_ids).') ';
			}
			else if(isset($this->search_keywords) && xtc_not_null($this->search_keywords))
			{
				//go for keywords... this is the main search process
				if(xtc_parse_search_string(htmlspecialchars_decode(stripslashes($this->search_keywords)), $t_search_keywords_array))
				{
					$t_where .= " AND ( ";

					for($i = 0, $n = sizeof($t_search_keywords_array); $i < $n; $i ++)
					{
						switch($t_search_keywords_array[$i])
						{
							case '(' :
							case ')' :
							case 'and' :
							case 'or' :
								$t_where .= " " . $t_search_keywords_array[$i] . " ";
								break;
							default :
								$t_where .= " ( ";
								$t_where .= "pd.products_keywords LIKE ('%" . xtc_db_input($t_search_keywords_array[$i]) . "%') ";

								if(SEARCH_IN_DESC == 'true')
								{
									$t_where .= "OR pd.products_description LIKE ('%" . xtc_db_input(htmlentities_wrapper($t_search_keywords_array[$i])) . "%') ";
									$t_where .= "OR pd.products_short_description LIKE ('%" . xtc_db_input(htmlentities_wrapper($t_search_keywords_array[$i])) . "%') ";
								}

								$t_where .= "OR pd.products_name LIKE ('%" . xtc_db_input($t_search_keywords_array[$i]) . "%') ";
								$t_where .= "OR p.products_model LIKE ('%" . xtc_db_input($t_search_keywords_array[$i]) . "%') ";
								$t_where .= "OR p.products_ean LIKE ('%" . xtc_db_input($t_search_keywords_array[$i]) . "%') ";

								if(SEARCH_IN_ATTR == 'true')
								{
									$t_where .= "OR pa.attributes_model LIKE ('%" . xtc_db_input($t_search_keywords_array[$i]) . "%') ";
									$t_where .= "OR ppc.combi_model LIKE ('%" . xtc_db_input($t_search_keywords_array[$i]) . "%') ";
									$t_where .= "OR (ppi.properties_name LIKE ('%" . xtc_db_input($t_search_keywords_array[$i]) . "%') ";
									$t_where .= "AND ppi.language_id = '" . $this->languages_id . "')";
									$t_where .= "OR (ppi.values_name LIKE ('%" . xtc_db_input($t_search_keywords_array[$i]) . "%') ";
									$t_where .= "AND ppi.language_id = '" . $this->languages_id . "')";
									$t_where .= "OR (pov.products_options_values_name LIKE ('%" . xtc_db_input($t_search_keywords_array[$i]) . "%') ";
									$t_where .= "AND pov.language_id = '" . $this->languages_id . "')";
								}

								$t_where .= " ) ";

								break;
						}
					}

					$t_where .= " ) GROUP BY p.products_id ";

					$t_sql_string_ok = true;
				}
			}

			//glue together
			if(empty($this->product_ids) !== true || ($this->search_keywords != '' && trim($this->search_keywords) != '' && $t_sql_string_ok == true))
			{
				$t_sql_query = $t_select . $t_from . $t_where;
			}
			else
			{
				// dummy: keine Suchergebnisse, wenn kein Suchbegriff angegeben wurde
				$t_sql_query = 'SELECT products_id FROM products WHERE products_id = 0';
			}

			// sorting
			if(isset($this->listing_sort) && $t_order_by != '')
			{
				$t_sql_query .= $t_order_by;
			}
			else if(empty($this->product_ids) !== true && $t_order_by != '')
			{
				$t_sql_query .= $t_order_by;
			}

			$this->sql_query = $t_sql_query;
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	public function build_sql_query()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_filter_manager',
																		  'current_category_id',
																		  'customers_status_id',
																		  'languages_id',
																	));
		if(empty($t_uninitialized_array))
		{
			// show the products of a specified manufacturer
			if(isset($this->manufacturers_id))
			{
				$this->build_manufacturer_filter_sql();
			}
			else
			{
				// show the products in a given category
				if(isset($this->filter_id) &&
				   xtc_not_null($this->filter_id)
				)
				{
					$this->build_category_filter_sql();
				}
				elseif($this->coo_filter_manager->is_active() == true)
				{
					// product filter
					$this->build_feature_filter_sql();
				}
				else
				{
					/* DEFAULT PRODUCT LISTING PAGE */
					$this->build_default_sql();
				}
			}

			// optional Product List Filter
			if(PRODUCT_LIST_FILTER > 0)
			{
				if(isset($this->manufacturers_id))
				{
					$t_filterlist_sql = "SELECT DISTINCT
											c.categories_id AS id,
											cd.categories_name AS name
										FROM
											" .	TABLE_PRODUCTS . " p,
											" . TABLE_PRODUCTS_TO_CATEGORIES . " p2c,
											" . TABLE_CATEGORIES . " c,
											" . TABLE_CATEGORIES_DESCRIPTION . " cd
										WHERE
											p.products_status = '1' AND
											p.products_id = p2c.products_id AND
											p2c.categories_id = c.categories_id AND
											p2c.categories_id = cd.categories_id AND
											cd.language_id = '" . $this->languages_id . "' AND
											p.manufacturers_id = '" . $this->manufacturers_id . "'
										ORDER BY cd.categories_name";
				}
				else
				{
					$t_filterlist_sql =	"SELECT DISTINCT
												m.manufacturers_id AS id,
												m.manufacturers_name AS name
											FROM
												" . TABLE_PRODUCTS . " p,
												" . TABLE_PRODUCTS_TO_CATEGORIES . " p2c,
												" . TABLE_MANUFACTURERS ." m
											WHERE
												p.products_status = '1' AND
												p.manufacturers_id = m.manufacturers_id AND
												p.products_id = p2c.products_id AND
												p2c.categories_id = '" . $this->current_category_id . "'
											ORDER BY m.manufacturers_name";
				}

				$t_filterlist_query = xtc_db_query($t_filterlist_sql);

				if(xtc_db_num_rows($t_filterlist_query) > 1)
				{
					$t_manufacturers_data_array                   = array();
					$t_manufacturers_data_array['FORM']['ID']     = 'filter';
					$t_manufacturers_data_array['FORM']['ACTION'] = xtc_href_link(FILENAME_DEFAULT, '',
																				  'NONSSL', true, true, true);
					$t_manufacturers_data_array['FORM']['METHOD'] = 'get';


					$t_manufacturer_dropdown_html = xtc_draw_form('filter',
														   xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL', true,
																		 true, true), 'get',
														   'style="float:right"');
					if(isset($this->manufacturers_id))
					{
						$t_manufacturer_dropdown_html .= xtc_draw_hidden_field('manufacturers_id', $this->manufacturers_id);
						$options                                           = array(array('text' => TEXT_ALL_CATEGORIES));
						$t_manufacturers_data_array['HIDDEN'][0]['NAME']   = 'manufacturers_id';
						$t_manufacturers_data_array['HIDDEN'][0]['VALUE']  = $this->manufacturers_id;
						$t_manufacturers_data_array['OPTIONS'][0]['VALUE'] = 0;
						$t_manufacturers_data_array['OPTIONS'][0]['NAME']  = TEXT_ALL_CATEGORIES;
					}
					else
					{
						$t_manufacturer_dropdown_html .= xtc_draw_hidden_field('cat',
																		$this->cat);
						$options                                           = array(array('text' => TEXT_ALL_MANUFACTURERS));
						$t_manufacturers_data_array['HIDDEN'][0]['NAME']   = 'cat';
						$t_manufacturers_data_array['HIDDEN'][0]['VALUE']  = $this->cat;
						$t_manufacturers_data_array['OPTIONS'][0]['VALUE'] = 0;
						$t_manufacturers_data_array['OPTIONS'][0]['NAME']  = TEXT_ALL_MANUFACTURERS;
					}

					$t_manufacturer_dropdown_html .= xtc_draw_hidden_field('sort', $this->sort);
					$t_manufacturers_data_array['HIDDEN'][1]['NAME']  = 'sort';
					$t_manufacturers_data_array['HIDDEN'][1]['VALUE'] = $this->sort;
					$count_options                                    = 1;

					while($filterlist = xtc_db_fetch_array($t_filterlist_query))
					{
						$options[] = array('id'   => $filterlist['id'], 'text' => $filterlist['name']);
						$t_manufacturers_data_array['OPTIONS'][$count_options]['VALUE'] = $filterlist['id'];
						$t_manufacturers_data_array['OPTIONS'][$count_options]['NAME']  = $filterlist['name'];
						$count_options++;
					}

					$t_manufacturers_data_array['DEFAULT'] = $this->filter_id;
					$t_manufacturers_data_array['NAME']    = 'filter_id';
					$t_manufacturer_dropdown_html .= xtc_draw_pull_down_menu('filter_id', $options,
																	  $this->filter_id,
																	  'onchange="this.form.submit()"');
					$t_manufacturer_dropdown_html .= '</form>' . "\n";

					$this->manufacturers_data_array = $t_manufacturers_data_array;
					$this->manufacturers_dropdown   = $t_manufacturer_dropdown_html;
				}
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	public function determine_category_depth()
	{
		static $t_category_depth;

		if($t_category_depth !== null)
		{
			return $t_category_depth;
		}

		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_filter_manager',
																		  'c_path',
																		  'current_category_id',
																		  'customers_status_id',
																	));
		if(empty($t_uninitialized_array))
		{
			$t_category_depth = 'top';

			if(((strpos(strtolower(gm_get_env_info("PHP_SELF")), FILENAME_DEFAULT) !== false 
			     || strpos(strtolower(gm_get_env_info("PHP_SELF")), 'shop.php') !== false) &&
				$this->coo_filter_manager->is_active()) 
			   || (isset($this->c_path) && xtc_not_null($this->c_path) && $this->c_path !== '0')
			)
			{
				if(GROUP_CHECK == 'false')
				{
					$t_categories_products_query = "SELECT COUNT(*) AS total
														FROM
															" . TABLE_PRODUCTS_TO_CATEGORIES . " ptc,
															" . TABLE_PRODUCTS . " p
														WHERE
															ptc.categories_id = '" . $this->current_category_id . "'
															AND ptc.products_id = p.products_id
															AND p.products_status = '1'";
				}
				else
				{
					$t_categories_products_query = "SELECT COUNT(*) AS total
														FROM
															" . TABLE_PRODUCTS_TO_CATEGORIES . " ptc,
															" . TABLE_PRODUCTS . " p
														WHERE
															ptc.categories_id = '" . $this->current_category_id . "'
															AND ptc.products_id = p.products_id
															AND p.products_status = '1'
															AND p.group_permission_" . $this->customers_status_id . " = '1'";
				}

				$t_categories_products_query = xtc_db_query($t_categories_products_query);
				$t_cateqories_products_array = xtc_db_fetch_array($t_categories_products_query);

				if($t_cateqories_products_array['total'] > 0)
				{
					$t_category_depth = 'nested';

					return $t_category_depth; // navigate through the categories
				}
				else
				{
					$t_category_parent_query = "SELECT COUNT(*) AS total FROM " . TABLE_CATEGORIES . " WHERE parent_id = '" .
											   $this->current_category_id . "'";
					$t_category_parent_query = xtc_db_query($t_category_parent_query);
					$t_category_parent_array = xtc_db_fetch_array($t_category_parent_query);

					if($t_category_parent_array['total'] > 0 && $this->current_category_id != 0)
					{
						$t_category_depth = 'nested';

						return $t_category_depth; // navigate through the categories
					}
					else
					{
						$t_category_depth = 'products';

						return $t_category_depth; // category has no products, but display the 'no products' message
					}
				}
			}
			elseif(isset($this->manufacturers_id))
			{
				$t_category_depth = 'products';

				return $t_category_depth;
			}

			return $t_category_depth;
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	public function init_feature_filter()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_filter_manager'));
		if(empty($t_uninitialized_array))
		{
			$t_global_filter            = gm_get_conf('GLOBAL_FILTER');
			$t_persistent_global_filter = gm_get_conf('PERSISTENT_GLOBAL_FILTER');

			// FeatureFilter
			if(isset($this->filter_fv_id) || isset($this->filter_price_min) ||
			   isset($this->filter_price_max) ||
			   isset($this->value_conjunction) ||
			   ($t_persistent_global_filter == true && $this->coo_filter_manager->is_active())
			)
			{
				$c_filter_categories_id = false;

				if(isset($this->feature_categories_id))
				{
					$c_filter_categories_id = (int)$this->feature_categories_id;
				}
				else if(((int)$this->categories_id == 0 && strpos(strtolower(gm_get_env_info("PHP_SELF")), FILENAME_DEFAULT) !== false) ||
						(int)$this->categories_id > 0
				)
				{
					// startpage, category listing or product details
					$c_filter_categories_id = $this->categories_id;
				}

				if($c_filter_categories_id !== false)
				{
					$t_coo_control = MainFactory::create_object('FeatureControl');
					$t_show_filter = $t_coo_control->is_category_filter_enabled($c_filter_categories_id);
				}

				if($t_global_filter == true && $t_show_filter == false)
				{
					$c_filter_categories_id = 0;
				}

				if($c_filter_categories_id != $this->coo_filter_manager->get_categories_id())
				{
					$this->coo_filter_manager->reset();
				}

				if(isset($this->filter_fv_id) ||
				   isset($this->filter_price_min) ||
				   isset($this->filter_price_max) ||
				   isset($this->value_conjunction)
				)
				{
					// clear filter and deactivate
					$this->coo_filter_manager->reset();
				}

				// set price range
				if(isset($this->filter_price_min) &&
				   empty($this->filter_price_min) == false
				)
				{
					$this->coo_filter_manager->set_price_range_start($this->filter_price_min);
				}
				if(isset($this->filter_price_max) &&
				   empty($this->filter_price_max) == false
				)
				{
					$this->coo_filter_manager->set_price_range_end($this->filter_price_max);
				}

				if(is_array($this->filter_fv_id) == false)
				{
					// filter_fv_id is a single id
					$this->coo_filter_manager->add_feature_value_id($this->filter_fv_id);
				}
				else
				{
					// filter_fv_id is an array. add groups.
					foreach($this->filter_fv_id as $f_feature_id => $f_feature_value_id_array)
					{
						$c_feature_id  = (int)$f_feature_id;
						$f_conjunction = $this->value_conjunction[$c_feature_id];
						
						// skip empty group
						if(is_array($f_feature_value_id_array) && count($f_feature_value_id_array) == 1 && $f_feature_value_id_array[0] === '')
						{
							continue;
						}
						
						$this->coo_filter_manager->add_feature_value_group($f_feature_value_id_array, $f_conjunction);
					}
				}
				// activate filter
				$this->coo_filter_manager->set_active(true);

				// get filter data for check
				$t_id_array    = $this->coo_filter_manager->get_feature_value_id_array();
				$t_group_array = $this->coo_filter_manager->get_feature_value_group_array();
				$t_price_start = $this->coo_filter_manager->get_price_range_start();
				$t_price_end   = $this->coo_filter_manager->get_price_range_end();

				if(sizeof($t_id_array) == 0 && sizeof($t_group_array) == 0 && $t_price_start == 0 && $t_price_end == 0)
				{
					// filter manager is empty -> deactivate
					$this->coo_filter_manager->set_active(false);
				}
			}
			else
			{
				// no filter information given. deactivate filter
				$this->coo_filter_manager->set_active(false);
				$this->coo_filter_manager->reset();
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	public function build_manufacturer_filter_sql()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array(	  'customer_country_id',
																			  'customer_zone_id',
																			  'customers_fsk18_display',
																			  'customers_status_id',
																			  'languages_id',
																			  'manufacturers_id',
																			  'show_price_tax',
																	));
		if(empty($t_uninitialized_array))
		{
			$t_select_part = '';
			$t_where_part = '';
			$t_group_check = '';
			$t_orderby = '';

			//fsk18 lock
			$t_fsk_lock = '';
			if($this->customers_fsk18_display == '0')
			{
				$t_fsk_lock = ' AND p.products_fsk18 != 1 ';
			}

			if(isset($this->filter_id) &&
			   xtc_not_null($this->filter_id)
			)
			{
				// sorting query
				$t_sorting_query = xtc_db_query("SELECT
													products_sorting,
													products_sorting2
												FROM " . TABLE_CATEGORIES . "
												WHERE categories_id = '" . $this->filter_id . "'");
				$t_sorting_data_array  = xtc_db_fetch_array($t_sorting_query);

				if(!$t_sorting_data_array['products_sorting'])
				{
					$t_sorting_data_array['products_sorting'] = 'pd.products_name';
				}

				$t_sorting = ' ORDER BY ' . $t_sorting_data_array['products_sorting'] . ' ' . $t_sorting_data_array['products_sorting2'] . ' ';

				if(isset($this->listing_sort))
				{
					$coo_listing_manager = MainFactory::create_object('ListingManager');
					$t_orderby           = $coo_listing_manager->get_sql_sort_part($this->listing_sort);

					if($t_orderby != '')
					{
						$t_sorting = $t_orderby;
					}
				}

				// We are asked to show only a specific category
				if(GROUP_CHECK == 'true')
				{
					$t_group_check = " AND p.group_permission_" . $this->customers_status_id . " = 1 ";
				}

				// sort by price
				if(strpos($t_orderby, 'p.products_price') !== false)
				{
					if($this->show_price_tax != 0)
					{
						$t_select_part = ", ROUND(IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) * (IF(p.products_tax_class_id = 0,0,tax_rate)/100+1), 2) AS final_price ";
						$t_from_part   = "LEFT JOIN " . TABLE_TAX_RATES . " AS tr ON (p.products_tax_class_id = tr.tax_class_id OR p.products_tax_class_id = 0)
												LEFT JOIN " . TABLE_ZONES_TO_GEO_ZONES .
										 " AS gz ON (tr.tax_zone_id = gz.geo_zone_id AND gz.zone_country_id = '" .
										 $this->customer_country_id . "') ";
						$t_where_part  = " AND (gz.zone_id = '0' OR gz.zone_id = '" .
										 $this->customer_zone_id . "') ";
					}
					else
					{
						$t_select_part = ", ROUND(IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price), 2) AS final_price ";
					}

					$t_sorting = str_replace('p.products_price', 'final_price', $t_sorting);
				}

				$this->sql_query = "SELECT DISTINCT p.products_fsk18,
										p.products_shippingtime,
										p.products_model,
										p.products_ean,
										pd.products_name,
										m.manufacturers_name,
										p.products_quantity,
										p.products_image,
										p.products_image_w, 
										p.products_image_h,
										p.products_weight,
										p.gm_show_weight,
										pd.products_short_description,
										pd.products_description,
										pd.gm_alt_text,
										pd.products_meta_description,
										p.products_id,
										p.manufacturers_id,
										p.products_price,
										p.products_vpe,
										p.products_vpe_status,
										p.products_vpe_value,
										p.products_discount_allowed,
										p.products_tax_class_id
										" . $t_select_part . "
									FROM
										" . TABLE_PRODUCTS . " p
										LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON (pd.products_id = p.products_id)
										LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS ptc ON (ptc.products_id = p.products_id)
										LEFT JOIN " . TABLE_MANUFACTURERS . " AS m ON (m.manufacturers_id = p.manufacturers_id)
										LEFT JOIN " . TABLE_SPECIALS . " AS s ON (s.products_id = p.products_id)
										" . $t_from_part . "
									WHERE
										p.products_status = 1 AND
										pd.language_id = '" . $this->languages_id . "' AND
										ptc.categories_id = '" . (int)$this->filter_id . "' AND
										m.manufacturers_id =  '" . $this->manufacturers_id . "'
										" . $t_where_part . "
										" . $t_group_check . "
										" . $t_fsk_lock . "
										" . $t_sorting;
			}
			else
			{
				// We show them all
				if(GROUP_CHECK == 'true')
				{
					$t_group_check = " AND p.group_permission_" . $this->customers_status_id . " = 1 ";
				}

				$t_sorting = '';

				if(isset($this->listing_sort))
				{
					$coo_listing_manager = MainFactory::create_object('ListingManager');
					$t_orderby           = $coo_listing_manager->get_sql_sort_part($this->listing_sort);

					if($t_orderby != '')
					{
						$t_sorting = $t_orderby;
					}
				}

				// sort by price
				if(strpos($t_orderby, 'p.products_price') !== false)
				{
					if($this->show_price_tax != 0)
					{
						$t_select_part = ", ROUND(IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) * (IF(p.products_tax_class_id = 0,0,tax_rate)/100+1), 2) AS final_price ";
						$t_from_part   = "LEFT JOIN " . TABLE_TAX_RATES . " AS tr ON (p.products_tax_class_id = tr.tax_class_id OR p.products_tax_class_id = 0)
												LEFT JOIN " . TABLE_ZONES_TO_GEO_ZONES .
										 " AS gz ON (tr.tax_zone_id = gz.geo_zone_id AND gz.zone_country_id = '" .
										 $this->customer_country_id . "') ";
						$t_where_part  = " AND (gz.zone_id = '0' OR gz.zone_id = '" .
										 $this->customer_zone_id . "') ";
					}
					else
					{
						$t_select_part = ", ROUND(IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price), 2) AS final_price ";
					}

					$t_sorting = str_replace('p.products_price', 'final_price', $t_sorting);
				}

				$this->sql_query = "SELECT DISTINCT p.products_fsk18,
										p.products_shippingtime,
										p.products_model,
										p.products_ean,
										pd.products_name,
										m.manufacturers_name,
										p.products_quantity,
										p.products_image,
										p.products_image_w, 
										p.products_image_h,
										p.products_weight,
										p.gm_show_weight,
										pd.products_short_description,
										pd.products_description,
										pd.gm_alt_text,
										pd.products_meta_description,
										p.products_id,
										p.manufacturers_id,
										p.products_price,
										p.products_vpe,
										p.products_vpe_status,
										p.products_vpe_value,
										p.products_discount_allowed,
										p.products_tax_class_id
										" . $t_select_part . "
									FROM
										" . TABLE_PRODUCTS . " p
										LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON (pd.products_id = p.products_id)
										LEFT JOIN " . TABLE_MANUFACTURERS . " AS m ON (m.manufacturers_id = p.manufacturers_id)
										LEFT JOIN " . TABLE_SPECIALS . " AS s ON (s.products_id = p.products_id)
										" . $t_from_part . "
									WHERE
										p.products_status = 1 AND
										pd.language_id = '" . $this->languages_id . "' AND
										m.manufacturers_id =  '" . $this->manufacturers_id . "'
										" . $t_where_part . "
										" . $t_group_check . "
										" . $t_fsk_lock . "
										" . $t_sorting;
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	public function build_category_filter_sql()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('current_category_id',
																		  'customer_country_id',
																		  'customer_zone_id',
																		  'customers_fsk18_display',
																		  'customers_status_id',
																		  'languages_id',
																		  'show_price_tax',
																	));
		if(empty($t_uninitialized_array))
		{
			$t_select_part = '';
			$t_where_part = '';
			$t_group_check = '';
			$t_orderby = '';

			//fsk18 lock
			$t_fsk_lock = '';
			if($this->customers_fsk18_display == '0')
			{
				$t_fsk_lock = ' AND p.products_fsk18 != 1 ';
			}

			// sorting query
			$t_sorting_query = xtc_db_query("SELECT
													products_sorting,
													products_sorting2
												FROM " . TABLE_CATEGORIES . "
												WHERE categories_id = '" . $this->current_category_id . "'");
			$t_sorting_data_array  = xtc_db_fetch_array($t_sorting_query);

			if(!$t_sorting_data_array['products_sorting'])
			{
				$t_sorting_data_array['products_sorting'] = 'pd.products_name';
			}

			$t_sorting = ' ORDER BY ' . $t_sorting_data_array['products_sorting'] . ' ' .
						 $t_sorting_data_array['products_sorting2'] . ' ';

			// We are asked to show only specific catgeory
			if(GROUP_CHECK == 'true')
			{
				$t_group_check = " AND p.group_permission_" . $this->customers_status_id . " = 1 ";
			}

			if(isset($this->listing_sort))
			{
				$coo_listing_manager = MainFactory::create_object('ListingManager');
				$t_orderby = $coo_listing_manager->get_sql_sort_part($this->listing_sort);

				if($t_orderby != '')
				{
					$t_sorting = $t_orderby;
				}
			}

			// sort by price
			if(strpos($t_orderby, 'p.products_price') !== false)
			{
				if($this->show_price_tax != 0)
				{
					$t_select_part = ", ROUND(IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) * (IF(p.products_tax_class_id = 0,0,tax_rate)/100+1), 2) AS final_price ";
					$t_from_part   = "LEFT JOIN " . TABLE_TAX_RATES . " AS tr ON (p.products_tax_class_id = tr.tax_class_id OR p.products_tax_class_id = 0)
												LEFT JOIN " . TABLE_ZONES_TO_GEO_ZONES .
									 " AS gz ON (tr.tax_zone_id = gz.geo_zone_id AND gz.zone_country_id = '" .
									 $this->customer_country_id . "') ";
					$t_where_part  = " AND (gz.zone_id = '0' OR gz.zone_id = '" .
									 $this->customer_zone_id . "') ";
				}
				else
				{
					$t_select_part = ", ROUND(IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price), 2) AS final_price ";
				}

				$t_sorting = str_replace('p.products_price', 'final_price', $t_sorting);
			}

			$this->sql_query = "SELECT DISTINCT p.products_fsk18,
										p.products_shippingtime,
										p.products_model,
										p.products_ean,
										pd.products_name,
										m.manufacturers_name,
										p.products_quantity,
										p.products_image,
										p.products_image_w, 
										p.products_image_h,
										p.products_weight,
										p.gm_show_weight,
										pd.products_short_description,
										pd.products_description,
										pd.gm_alt_text,
										pd.products_meta_description,
										p.products_id,
										p.manufacturers_id,
										p.products_price,
										p.products_vpe,
										p.products_vpe_status,
										p.products_vpe_value,
										p.products_discount_allowed,
										p.products_tax_class_id
										" . $t_select_part . "
									FROM  " . TABLE_PRODUCTS . " p
										LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON (pd.products_id = p.products_id)
										LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS ptc ON (ptc.products_id = p.products_id)
										LEFT JOIN " . TABLE_MANUFACTURERS . " AS m ON (m.manufacturers_id = p.manufacturers_id)
										LEFT JOIN " . TABLE_SPECIALS . " AS s ON (s.products_id = p.products_id)
										" . $t_from_part . "
									WHERE
										p.products_status = 1 AND
										pd.language_id = '" . $this->languages_id . "' AND
										ptc.categories_id = '" . $this->current_category_id . "' AND
										m.manufacturers_id = '" . (int)$this->filter_id . "'
										" . $t_where_part . "
										" . $t_group_check . "
										" . $t_fsk_lock . "
										" . $t_sorting;
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	public function build_feature_filter_sql()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array(	'coo_filter_manager',
																			'current_category_id',
																		  	'customers_fsk18_display',
																		  	'customers_status_id',
																	));
		if(empty($t_uninitialized_array))
		{
			//fsk18 lock
			$t_fsk_lock = '';
			if($this->customers_fsk18_display == '0')
			{
				$t_fsk_lock = ' AND p.products_fsk18 != 1 ';
			}

			// sorting query
			$t_sorting_query = xtc_db_query("SELECT
													products_sorting,
													products_sorting2
												FROM " . TABLE_CATEGORIES . "
												WHERE categories_id = '" . $this->current_category_id . "'");
			$t_sorting_data_array  = xtc_db_fetch_array($t_sorting_query);

			if(!$t_sorting_data_array['products_sorting'])
			{
				$t_sorting_data_array['products_sorting'] = 'pd.products_name';
			}

			$t_sorting = ' ORDER BY ' . $t_sorting_data_array['products_sorting'] . ' ' .
						 $t_sorting_data_array['products_sorting2'] . ' ';

			// We show them all
			if(GROUP_CHECK == 'true')
			{
				$t_group_check = " AND p.group_permission_" . $this->customers_status_id . " = 1 ";
			}

			// sorting
			if(isset($this->listing_sort))
			{
				$coo_listing_manager = MainFactory::create_object('ListingManager');
				$t_orderby           = $coo_listing_manager->get_sql_sort_part($this->listing_sort);

				if($t_orderby != '')
				{
					$t_sorting = $t_orderby;
				}
			}

			// filter is active
			$coo_finder = MainFactory::create_object('IndexFeatureProductFinder');

			// set categories_id
			$t_categories_id = $this->coo_filter_manager->get_categories_id();
			if(isset($_GET['feature_categories_id']))
			{
			  $coo_finder->add_categories_id((int)$_GET['feature_categories_id']);
			}
			else
			{
			  $coo_finder->add_categories_id((int)$t_categories_id);
			}

			// set price range
			$t_price_start = $this->coo_filter_manager->get_price_range_start();
			$t_price_end   = $this->coo_filter_manager->get_price_range_end();

			if($t_price_start !== false)
			{
				$coo_finder->set_price_range_start($t_price_start);
			}

			if($t_price_end !== false)
			{
				$coo_finder->set_price_range_end($t_price_end);
			}

			// get feature_value_groups from FilterManager
			$t_feature_value_group_array = $this->coo_filter_manager->get_feature_value_group_array();

			// transfer feature_value_groups to product finder
			foreach($t_feature_value_group_array as $t_feature_value_group)
			{
				$coo_finder->add_feature_value_group($t_feature_value_group);
			}

			// get built sql query for product_listing
			$t_filter_sql = $coo_finder->get_products_listing_sql_string($t_group_check . $t_fsk_lock,
																		 $t_sorting);

			// set filter query for listing
			$this->sql_query = $t_filter_sql;

			if($this->determine_category_depth() != 'nested' || empty($this->current_category_id))
			{
				$coo_filter_selection_content_view = MainFactory::create_object('FilterSelectionContentView');
				$coo_filter_selection_content_view->set_('feature_value_group_array', $t_feature_value_group_array);
				$coo_filter_selection_content_view->set_('language_id', $_SESSION['languages_id']);
				$this->filter_selection_html = $coo_filter_selection_content_view->get_html();
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	public function build_default_sql()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array(	'current_category_id',
																			'customer_country_id',
																			'customer_zone_id',
																			'customers_fsk18_display',
																			'customers_status_id',
																			'languages_id',
																			'show_price_tax',
																	));
		if(empty($t_uninitialized_array))
		{
			//fsk18 lock
			$t_fsk_lock = '';
			if($this->customers_fsk18_display == '0')
			{
				$t_fsk_lock = ' AND p.products_fsk18 != 1 ';
			}

			$t_select_part = '';
			$t_from_part   = '';
			$t_where_part  = '';

			// sorting query
			$t_sorting_query = xtc_db_query("SELECT
													products_sorting,
													products_sorting2
												FROM " . TABLE_CATEGORIES . "
												WHERE categories_id = '" . $this->current_category_id . "'");
			$t_sorting_data_array  = xtc_db_fetch_array($t_sorting_query);

			if(!$t_sorting_data_array['products_sorting'])
			{
				$t_sorting_data_array['products_sorting'] = 'pd.products_name';
			}

			$t_sorting = ' ORDER BY ' . $t_sorting_data_array['products_sorting'] . ' ' .
						 $t_sorting_data_array['products_sorting2'] . ' ';

			// We show them all
			if(GROUP_CHECK == 'true')
			{
				$t_group_check = " AND p.group_permission_" . $this->customers_status_id . " = 1 ";
			}

			// sorting
			if(isset($this->listing_sort))
			{
				$coo_listing_manager = MainFactory::create_object('ListingManager');
				$t_orderby           = $coo_listing_manager->get_sql_sort_part($this->listing_sort);

				if($t_orderby != '')
				{
					$t_sorting = $t_orderby;
				}
			}

			// sort by price
			if(strpos($t_orderby, 'p.products_price') !== false)
			{
				if($this->show_price_tax != 0)
				{
					$t_select_part = ", ROUND(IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) * (IF(p.products_tax_class_id = 0,0,tax_rate)/100+1), 2) AS final_price ";
					$t_from_part   = "LEFT JOIN " . TABLE_TAX_RATES . " AS tr ON (p.products_tax_class_id = tr.tax_class_id OR p.products_tax_class_id = 0)
									LEFT JOIN " . TABLE_ZONES_TO_GEO_ZONES .
									 " AS gz ON (tr.tax_zone_id = gz.geo_zone_id AND gz.zone_country_id = '" .
									 $this->customer_country_id . "') ";
					$t_where_part  = " AND (gz.zone_id = '0' OR gz.zone_id = '" .
									 $this->customer_zone_id . "') ";
				}
				else
				{
					$t_select_part = ", ROUND(IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price), 2) AS final_price ";
				}

				$t_sorting = str_replace('p.products_price', 'final_price', $t_sorting);
			}

			$this->sql_query = "SELECT DISTINCT p.products_fsk18,
											p.products_shippingtime,
											p.products_model,
											p.products_ean,
											pd.products_name,
											m.manufacturers_name,
											p.products_quantity,
											p.products_image,
											p.products_image_w, 
											p.products_image_h,
											p.products_weight,
											p.gm_show_weight,
											pd.products_short_description,
											pd.products_description,
											pd.gm_alt_text,
											pd.products_meta_description,
											p.products_id,
											p.manufacturers_id,
											p.products_price,
											p.products_vpe,
											p.products_vpe_status,
											p.products_vpe_value,
											p.products_discount_allowed,
											p.products_tax_class_id
											" . $t_select_part . "
										FROM
											" . TABLE_PRODUCTS . " p
											LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " AS pd ON (pd.products_id = p.products_id)
											LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS ptc ON (ptc.products_id = p.products_id)
											LEFT JOIN " . TABLE_MANUFACTURERS . " AS m ON (m.manufacturers_id = p.manufacturers_id)
											LEFT JOIN " . TABLE_SPECIALS . " AS s ON (s.products_id = p.products_id)
											" . $t_from_part . "
										WHERE
											p.products_status = 1 AND
											pd.language_id = '" . $this->languages_id . "' AND
											ptc.categories_id = '" . $this->current_category_id . "'
											" . $t_where_part . "
											" . $t_group_check . "
											" . $t_fsk_lock . "
											" . $t_sorting;
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	public function build_cache_id_parameter_array($p_parameter_array)
	{
		$this->cache_id_parameter_array[] = $this->current_category_id;
		$this->cache_id_parameter_array[] = $this->manufacturers_id;
		$this->cache_id_parameter_array[] = $this->filter_id;
		$this->cache_id_parameter_array[] = $this->page_number;
		$this->cache_id_parameter_array[] = $this->search_keywords;
		$this->cache_id_parameter_array[] = $this->categories_id;
		$this->cache_id_parameter_array[] = $this->price_from;
		$this->cache_id_parameter_array[] = $this->price_to;
		$this->cache_id_parameter_array[] = md5(print_r($this->filter_fv_id, true));
		$this->cache_id_parameter_array[] = $this->filter_price_min;
		$this->cache_id_parameter_array[] = $this->filter_price_max;
		$this->cache_id_parameter_array[] = $this->listing_sort;
		$this->cache_id_parameter_array[] = $this->listing_count;

		foreach($p_parameter_array as $t_parameter)
		{
			$this->cache_id_parameter_array[] = $t_parameter;
		}
	}

	public function get_error_html_output($p_error)
	{
		$coo_error_message_content_view = MainFactory::create_object('ErrorMessageContentView');
		$coo_error_message_content_view->set_error($p_error);
		$t_html_output = $coo_error_message_content_view->get_html();

		return $t_html_output;
	}

	public function get_category_listing_html_output()
	{
		$t_html_output = '';

		$t_uninitialized_array = $this->get_uninitialized_variables(array('current_category_id'));

		if(empty($t_uninitialized_array))
		{
			$t_template_sql    = "SELECT categories_template
													FROM " . TABLE_CATEGORIES . " c
													WHERE c.categories_id = '" . $this->current_category_id . "'";
			$t_template_result = xtc_db_query($t_template_sql);

			if(xtc_db_num_rows($t_template_result) == 1)
			{
				$t_template_result_array = xtc_db_fetch_array($t_template_result);

				/* @var CategoryListingContentView $coo_category_listing_content_view */
				$coo_category_listing_content_view = MainFactory::create_object('CategoryListingContentView',
																				array($t_template_result_array['categories_template']));
				$coo_category_listing_content_view->setCurrentCategoryId($this->current_category_id);
				$coo_category_listing_content_view->setCustomerStatusId($_SESSION['customers_status']['customers_status_id']);
				$coo_category_listing_content_view->setLanguageId($_SESSION['languages_id']);
				$coo_category_listing_content_view->setFilterManager($_SESSION['coo_filter_manager']);
				$t_html_output = $coo_category_listing_content_view->get_html();
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}

		return $t_html_output;
	}
	
	public function get_filter_selection_html_output()
	{
		/* @var CategoryListingContentView $coo_category_listing_content_view */
		$coo_category_listing_content_view = MainFactory::create_object('CategoryListingContentView');
		$coo_category_listing_content_view->setCurrentCategoryId($this->current_category_id);
		$coo_category_listing_content_view->setCustomerStatusId($_SESSION['customers_status']['customers_status_id']);
		$coo_category_listing_content_view->setLanguageId($_SESSION['languages_id']);
		$coo_category_listing_content_view->setFilterManager($_SESSION['coo_filter_manager']);
		
		return $coo_category_listing_content_view->getFilterSelectionHtml();
	}

	public function get_start_page_html_output()
	{
		$coo_index_content_view = MainFactory::create_object('IndexContentView');
		$coo_index_content_view->set_('customers_status_id', $_SESSION['customers_status']['customers_status_id']);
		$coo_index_content_view->set_('languages_id', $_SESSION['languages_id']);
		$t_html_output = $coo_index_content_view->get_html();

		return $t_html_output;
	}

	public function build_view_mode_url($p_view_mode)
	{
		$t_view_mode_url = '';

		$t_uninitialized_array = $this->get_uninitialized_variables(array('current_category_id',
																		  'current_page',
																	));

		if(empty($t_uninitialized_array))
		{
			$coo_seo_boost = MainFactory::create_object('GMSEOBoost');

			if($coo_seo_boost->boost_categories == true && $this->current_category_id != 0)
			{
				// use boosted url
				$t_href_url = $coo_seo_boost->get_current_boost_url();

				if($t_href_url == '')
				{
					$t_exclude_parameters = array('view_mode');
				}
				else
				{
					$t_exclude_parameters = array('view_mode', 'gm_boosted_category', 'cat', 'cPath');
				}
			}
			else
			{
				// use default url for splitting urls
				$t_href_url           = $this->current_page;
				$t_exclude_parameters = array('view_mode');
			}

			if(isset($this->page_number) == false)
			{
				$t_page = 'page=0&';
			}
			else
			{
				$t_page = '';
			}

			$t_view_mode_url = xtc_href_link($t_href_url,
											  xtc_get_all_get_params($t_exclude_parameters) . $t_page . 'view_mode=' . (string)$p_view_mode,
											  'NONSSL');
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}

		return $t_view_mode_url;
	}

	public function build_hidden_get_params_array()
	{
		$t_page_url                = gm_get_env_info('REQUEST_URI');
		$t_page_url_array          = explode('?', $t_page_url);
		$t_get_params_hidden_array = array();

		if(count($t_page_url_array) == 2)
		{
			$t_page_url_get_params = $t_page_url_array[1];
			$t_page_url_get_params = str_replace('&amp;', '&', $t_page_url_get_params);
			$t_get_params_array    = explode('&', $t_page_url_get_params);

			for($i = 0; $i < count($t_get_params_array); $i++)
			{
				$t_get_data_array = explode('=', $t_get_params_array[$i]);

				if($t_get_data_array[0] != 'listing_sort' && $t_get_data_array[0] != 'listing_count')
				{
					if(isset($t_get_data_array[1]))
					{
						$t_get_params_hidden_array[] = array('NAME'  => htmlspecialchars_wrapper(urldecode($t_get_data_array[0])),
															 'VALUE' => htmlspecialchars_wrapper(urldecode($t_get_data_array[1]))
						);
					}
				}
			}
		}

		return $t_get_params_hidden_array;
	}

	public function determine_view_mode($p_view_mode_tiled = null)
	{
		$t_view_mode = 'default';

		if(isset($this->view_mode) && ($this->view_mode == 'tiled' || $this->view_mode == 'default'))
		{
			$t_view_mode = $this->view_mode;
			return $t_view_mode;
		}
		
		if(($p_view_mode_tiled === null && gm_get_conf('MAIN_VIEW_MODE_TILED') == 'true') || $p_view_mode_tiled == 1)
		{
			$t_view_mode = 'tiled';
		}

		return $t_view_mode;
	}

	public function get_category_data_array()
	{
		$t_category_data_array = array();
		$t_category_data_array['name'] = '';
		$t_category_data_array['heading_title'] = '';
		$t_category_data_array['image_alt_text'] = '';
		$t_category_data_array['image'] = '';
		$t_category_data_array['description'] = '';
		$t_category_data_array['show_quantity'] = false;
		$t_category_data_array['category_show_quantity'] = false;
		$t_category_data_array['show_graduated_prices'] = false;

		$t_uninitialized_array = $this->get_uninitialized_variables(array('current_category_id',
																		  'customers_status_id',
																		  'languages_id',
																		  'show_graduated_prices',
																	));

		if(empty($t_uninitialized_array))
		{
			if(GROUP_CHECK == 'true')
			{
				$t_group_check = ' AND c.group_permission_' . $this->customers_status_id . ' = 1 ';
			}

			$t_query = 'SELECT
									cd.categories_description,
									cd.categories_name,
									cd.categories_heading_title,
									cd.gm_alt_text,
									c.categories_image,
									c.listing_template,
									c.categories_image,
									c.view_mode_tiled,
									c.gm_show_attributes,
									c.gm_show_graduated_prices,
									c.gm_show_qty,
									c.gm_show_qty_info
								FROM
									' . TABLE_CATEGORIES . ' c,
									' . TABLE_CATEGORIES_DESCRIPTION . ' cd
								WHERE
									c.categories_id = "' . $this->current_category_id . '"
									AND cd.categories_id = "' . $this->current_category_id . '"
									AND cd.language_id = "' . $this->languages_id . '"
									' . $t_group_check;

			$t_result = xtc_db_query($t_query);

			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_result_array = xtc_db_fetch_array($t_result);

				if(GM_CAT_COUNT == 0)
				{
					$t_category_data_array['description'] = $t_result_array['categories_description'];

					if(trim($t_category_data_array['description']) == '<br />')
					{
						$t_category_data_array['description'] = '';
					}

					$t_category_data_array['name'] = $t_result_array['categories_name'];
					$t_category_data_array['heading_title'] = $t_result_array['categories_heading_title'];
					$t_category_data_array['image_alt_text'] = $t_result_array['gm_alt_text'];
					$t_category_data_array['image'] = '';

					if(trim($t_result_array['categories_image']) != '' && file_exists(DIR_WS_IMAGES . 'categories/' . basename($t_result_array['categories_image'])))
					{
						$t_category_data_array['image'] = DIR_WS_IMAGES . 'categories/' . basename($t_result_array['categories_image']);
					}
				}

				$t_category_data_array['gm_show_attributes'] = $t_result_array['gm_show_attributes'];
				$t_category_data_array['listing_template'] = $t_result_array['listing_template'];
				$t_category_data_array['view_mode_tiled'] = $t_result_array['view_mode_tiled'];
			}

			// Get the right image for the top-right
			if(isset($this->manufacturers_id))
			{
				$t_image = xtc_db_query("SELECT manufacturers_image FROM " . TABLE_MANUFACTURERS .
										" WHERE manufacturers_id = '" . $this->manufacturers_id . "'");
				$t_image = xtc_db_fetch_array($t_image);

				if(is_file(DIR_WS_IMAGES . 'manufacturers/' . basename($t_image['manufacturers_image'])))
				{
					$t_category_data_array['image'] = DIR_WS_IMAGES . 'manufacturers/' . basename($t_image['manufacturers_image']);
				}
			}

			if((isset($t_result_array['gm_show_qty']) && $t_result_array['gm_show_qty'] == '1')
			   ||
			   (gm_get_conf('MAIN_SHOW_QTY') == 'true' && isset($t_result_array['gm_show_qty']) === false)
			)
			{
				$t_category_data_array['show_quantity'] = true;
			}

			if((isset($t_result_array['gm_show_qty_info']) && $t_result_array['gm_show_qty_info'] == '1')
			   ||
			   (gm_get_conf('MAIN_SHOW_QTY_INFO') == 'true' && isset($t_result_array['gm_show_qty_info']) == false)
			)
			{
				$t_category_data_array['category_show_quantity'] = true;
			}

			if($this->show_graduated_prices &&
			   (gm_get_conf('MAIN_SHOW_GRADUATED_PRICES') == 'true'
				&& isset($t_result_array['gm_show_graduated_prices']) === false
				|| $t_result_array['gm_show_graduated_prices'] == '1')
			)
			{
				$t_category_data_array['show_graduated_prices'] = true;
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}

		return $t_category_data_array;
	}

	public function determine_max_display_search_results()
	{
		$t_max_display_search_results = MAX_DISPLAY_SEARCH_RESULTS;

		if(isset($this->listing_count))
		{
			$t_max_display_search_results = $this->listing_count;
		}

		if($t_max_display_search_results < 1)
		{
			$t_max_display_search_results = 1;
		}

		return $t_max_display_search_results;
	}

	public function extend_proceed($p_action)
	{
		// overload this method e.g. to manipulate the sql_query before it is executed
	}

	public function add_product_data(array &$p_products_array, array $p_product_array, product $p_coo_product)
	{
		// overload this method to add or manipulate data of the product array
	}
	
	
	public function setProductListingTemplatePath($path)
	{
		$this->productListingTemplatePath = $path;
	}
	
	
	/**
	 * @return FilterManager|null
	 */
	public function get_filter_manager()
	{
		return $this->coo_filter_manager;
	}
}