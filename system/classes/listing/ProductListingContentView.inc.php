<?php
/* --------------------------------------------------------------
   ProductListingContentView.inc.php 2016-05-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_listing.php,v 1.42 2003/05/27); www.oscommerce.com
   (c) 2003	 nextcommerce (product_listing.php,v 1.19 2003/08/1); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_listing.php 1286 2005-10-07 10:10:18Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class ProductListingContentView extends ContentView
{
	protected $cache_id_parameter_array;
	protected $category_description;
	protected $category_heading_title;
	protected $category_image;
	protected $category_image_alt_text;
	protected $category_name;
	protected $filter_selection_html;
	protected $get_params_hidden_data_array;
	protected $listing_count;
	protected $listing_sort;
	protected $manufacturers_data_array;
	protected $manufacturers_id;
	protected $manufacturers_dropdown;
	protected $navigation_html;
	protected $navigation_info_html;
	protected $page_number;
	protected $products_array;
	protected $products_per_page;
	protected $search_keywords;
	protected $show_quantity;
	protected $sorting_form_action_url;
	protected $thumbnail_width;
	protected $view_mode;
	protected $view_mode_url_default;
	protected $view_mode_url_tiled;
	protected $showRating;
	protected $showManufacturerImages;
	protected $showProductRibbons;


	public function __construct($p_template = 'default')
	{
		parent::__construct();
		$t_filepath = DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_listing/';

		// get default template
		$c_template = $this->get_default_template($t_filepath, $p_template);

		$this->set_content_template('module/product_listing/' . $c_template);
		$this->set_flat_assigns(true);
	}


	protected function set_validation_rules()
	{
		$this->validation_rules_array['cache_id_parameter_array']     = array('type' => 'array');
		$this->validation_rules_array['category_description']         = array('type' => 'string');
		$this->validation_rules_array['category_heading_title']       = array('type' => 'string');
		$this->validation_rules_array['category_image']               = array('type' => 'string');
		$this->validation_rules_array['category_image_alt_text']      = array('type' => 'string');
		$this->validation_rules_array['category_name']                = array('type' => 'string');
		$this->validation_rules_array['filter_selection_html']        = array('type' => 'string');
		$this->validation_rules_array['get_params_hidden_data_array'] = array('type' => 'array');
		$this->validation_rules_array['listing_count']                = array('type' => 'int');
		$this->validation_rules_array['listing_sort']                 = array('type' => 'string');
		$this->validation_rules_array['manufacturers_data_array']     = array('type' => 'array');
		$this->validation_rules_array['manufacturers_id']             = array('type' => 'int');
		$this->validation_rules_array['manufacturers_dropdown']       = array('type' => 'string');
		$this->validation_rules_array['navigation_html']              = array('type' => 'string');
		$this->validation_rules_array['navigation_info_html']         = array('type' => 'string');
		$this->validation_rules_array['page_number']                  = array('type' => 'int');
		$this->validation_rules_array['products_array']               = array('type' => 'array');
		$this->validation_rules_array['products_per_page']            = array('type' => 'int');
		$this->validation_rules_array['search_keywords']              = array('type' => 'string');
		$this->validation_rules_array['show_quantity']                = array('type' => 'bool');
		$this->validation_rules_array['sorting_form_action_url']      = array('type' => 'string');
		$this->validation_rules_array['thumbnail_width']              = array('type' => 'int');
		$this->validation_rules_array['view_mode']                    = array('type' => 'string');
		$this->validation_rules_array['view_mode_url_default']        = array('type' => 'string');
		$this->validation_rules_array['view_mode_url_tiled']          = array('type' => 'string');
		$this->validation_rules_array['showRating']                   = array('type' => 'bool');
		$this->validation_rules_array['showManufacturerImages']       = array('type' => 'string');
		$this->validation_rules_array['showProductRibbons']           = array('type' => 'string');
	}
	
	
	public function prepare_data()
	{
		parent::prepare_data();

		$t_uninitialized_array = $this->get_uninitialized_variables(array('cache_id_parameter_array',
																		  'get_params_hidden_data_array',
																		  'navigation_html', 
																		  'navigation_info_html',
																		  'products_array', 
																		  'products_per_page',
																		  'show_quantity', 
																		  'sorting_form_action_url',
																		  'view_mode', 
																		  'view_mode_url_default',
																		  'view_mode_url_tiled',
																	));

		if(empty($t_uninitialized_array))
		{
			$this->set_content_data('CATEGORIES_DESCRIPTION', $this->category_description);
			$this->set_content_data('CATEGORIES_GM_ALT_TEXT', htmlspecialchars_wrapper($this->category_image_alt_text));
			$this->set_content_data('CATEGORIES_HEADING_TITLE',	htmlspecialchars_wrapper($this->category_heading_title));
			$this->set_content_data('CATEGORIES_IMAGE', $this->category_image);
			$this->set_content_data('CATEGORIES_NAME', htmlspecialchars_wrapper($this->category_name));

			$t_start_count_value = $this->products_per_page;
			$t_count_value_2     = $t_start_count_value * 2;
			$t_count_value_3     = $t_start_count_value + $t_count_value_2;
			$t_count_value_4     = $t_count_value_3 * 2;
			$t_count_value_5     = $t_count_value_4 * 2;
			$this->set_content_data('COUNT_VALUE_1', $t_start_count_value);
			$this->set_content_data('COUNT_VALUE_2', $t_count_value_2);
			$this->set_content_data('COUNT_VALUE_3', $t_count_value_3);
			$this->set_content_data('COUNT_VALUE_4', $t_count_value_4);
			$this->set_content_data('COUNT_VALUE_5', $t_count_value_5);

			$this->set_content_data('FILTER_SELECTION', $this->filter_selection_html);
			$this->set_content_data('get_params_hidden_data', $this->get_params_hidden_data_array);

			if($this->show_quantity === true)
			{
				$this->set_content_data('GM_SHOW_QTY', '1');
			}
			else
			{
				$this->set_content_data('GM_SHOW_QTY', '0');
			}

			$this->set_content_data('gm_manufacturers_id', $this->manufacturers_id);
			$this->set_content_data('HIDDEN_QTY_NAME', 'products_qty');
			$this->set_content_data('HIDDEN_QTY_VALUE', '1');

			if($this->listing_count !== null)
			{
				$this->set_content_data('ITEM_COUNT', htmlspecialchars_wrapper($this->listing_count));
			}

			$this->set_content_data('manufacturers_data', $this->manufacturers_data_array);
			$this->set_content_data('MANUFACTURER_DROPDOWN', $this->manufacturers_dropdown);
			$this->set_content_data('module_content', $this->products_array);
			$this->set_content_data('NAVIGATION', $this->navigation_html);
			$this->set_content_data('bar', $this->navigation_html);
			$this->set_content_data('NAVIGATION_INFO', $this->navigation_info_html);
			$this->set_content_data('info', $this->navigation_info_html);

			if(isset($this->search_keywords))
			{
				$this->set_content_data('SEARCH_RESULT_PAGE', 1);
				$this->set_content_data('KEYWORDS', gm_prepare_string($this->search_keywords, true));
			}

			if($this->listing_sort !== null)
			{
				$this->set_content_data('SORT', htmlspecialchars_wrapper($this->listing_sort));
			}

			$this->set_content_data('SORTING_FORM_ACTION_URL', htmlspecialchars_wrapper($this->sorting_form_action_url));
			$this->set_content_data('VIEW_MODE', $this->view_mode);
			$this->set_content_data('VIEW_MODE_URL_DEFAULT', $this->view_mode_url_default);
			$this->set_content_data('VIEW_MODE_URL_TILED', $this->view_mode_url_tiled);
			$this->set_content_data('showManufacturerImages', $this->showManufacturerImages);
			$this->set_content_data('showProductRibbons', $this->showProductRibbons);
			$this->set_content_data('showRating', $this->showRating);
			
			# BOF YOOCHOOSE
			$t_view_html = '';
			
			if(defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE)
			{
				$yoo_object = MainFactory::create_object('YoochooseProductListingContentView');
				$t_view_html = $yoo_object->get_html();
			}
			
			$this->set_content_data('MODULE_yoochoose_category_topsellers', $t_view_html);
			# EOF YOOCHOOSE
			
			$this->add_cache_id_elements($this->cache_id_parameter_array);
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
}