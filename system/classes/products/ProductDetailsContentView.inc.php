<?php
/* --------------------------------------------------------------
  ProductDetailsContentView.inc.php 2014-11-18 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------- */

require_once(DIR_FS_INC . 'get_products_vpe_array.inc.php');
require_once(DIR_FS_INC . 'xtc_get_products_mo_images.inc.php');
require_once(DIR_WS_CLASSES . 'order.php'); // needed for old shop versions

class ProductDetailsContentView extends ContentView
{
	protected $product_id = 0;
	protected $coo_product;
	protected $coo_properties_control;
	protected $coo_properties_view;
	protected $coo_xtc_price;
	protected $coo_order;
	protected $coo_main;
	protected $coo_tab_tokenizer;
	protected $coo_gprint_content_manager;
	protected $product_index;
	protected $attributes_ids_data_array;
	protected $attributes_names_data_array;
	protected $attributes_weight = 0;
	protected $attributes_model_array = array();
	protected $properties_data_array;
	protected $properties_combi_id;

	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/product_details.html');
		$this->set_flat_assigns(true);
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['product_id']						= array('type' => 'string');
		$this->validation_rules_array['coo_product']					= array('type' => 'object',
																				'object_type' => 'product');
		$this->validation_rules_array['coo_properties_control']			= array('type' => 'object',
																				'object_type' => 'product');
		$this->validation_rules_array['coo_properties_view']			= array('type' => 'object',
																				'object_type' => 'product');
		$this->validation_rules_array['coo_xtc_price']					= array('type' => 'object',
																				'object_type' => 'product');
		$this->validation_rules_array['coo_order']						= array('type' => 'object',
																				'object_type' => 'product');
		$this->validation_rules_array['coo_main']						= array('type' => 'object',
																				'object_type' => 'product');
		$this->validation_rules_array['coo_tab_tokenizer']				= array('type' => 'object',
																				'object_type' => 'product');
		$this->validation_rules_array['coo_gprint_content_manager']		= array('type' => 'object',
																				'object_type' => 'product');
		$this->validation_rules_array['product_index']					= array('type' => 'int');
		$this->validation_rules_array['attributes_ids_data_array']		= array('type' => 'array');
		$this->validation_rules_array['attributes_names_data_array']	= array('type' => 'array');
		$this->validation_rules_array['attributes_weight']				= array('type' => 'int');
		$this->validation_rules_array['attributes_model_array']			= array('type' => 'array');
		$this->validation_rules_array['properties_data_array']			= array('type' => 'array');
		$this->validation_rules_array['properties_combi_id']			= array('type' => 'int');
	}

	public function prepare_data()
	{
		$this->coo_properties_control = MainFactory::create_object('PropertiesControl');
		$this->coo_properties_view = MainFactory::create_object('PropertiesView');
		$this->coo_xtc_price = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
		$this->coo_order = new order();
		$this->coo_main = new main();
		$this->coo_gprint_content_manager = new GMGPrintContentManager();

		$this->build_html = false;

		$this->get_product_index();

		if(isset($this->product_index))
		{
			$this->build_html = true;

			$this->coo_product = new product(xtc_get_prid($this->product_id));
			$this->coo_tab_tokenizer = MainFactory::create_object('GMTabTokenizer', array(stripslashes($this->coo_product->data['products_description'])));

			$this->get_data();

			$this->add_data();
		}
	}

	protected function get_product_index()
	{
		for($i = 0, $n = count($this->coo_order->products); $i < $n; $i++)
		{
			if($this->coo_order->products[$i]['id'] == $this->product_id)
			{
				$this->product_index = $i;
				break;
			}
		}
	}

	protected function get_data()
	{
		$this->get_attributes_data();
		$this->get_properties_data();
		$this->get_customizer_data();
	}

	protected function get_attributes_data()
	{
		if(isset($this->coo_order->products[$this->product_index]['attributes']) && is_array($this->coo_order->products[$this->product_index]['attributes']))
		{
			foreach($this->coo_order->products[$this->product_index]['attributes'] AS $t_attributes_data_array)
			{
				$this->attributes_ids_data_array[$t_attributes_data_array['option_id']] = $t_attributes_data_array['value_id'];
				$this->attributes_names_data_array[] = array(
					'option' => $t_attributes_data_array['option'],
					'value' => $t_attributes_data_array['value']
				);

				$t_query = 'SELECT
								options_values_weight AS weight,
								weight_prefix AS prefix,
								attributes_model
							FROM
								products_attributes
							WHERE
								products_id				= "' . (int)xtc_get_prid($this->coo_order->products[$this->product_index]['id']) . '"
								AND	options_id			= "' . (int)$t_attributes_data_array['option_id'] . '"
								AND	options_values_id	= "' . (int)$t_attributes_data_array['value_id'] . '"
							LIMIT 1';
				$t_attr_result = xtc_db_query($t_query);
				if(xtc_db_num_rows($t_attr_result) == 1)
				{
					$t_attr_result_array = xtc_db_fetch_array($t_attr_result);

					if(trim($t_attr_result_array['attributes_model']) != '')
					{
						$this->attributes_model_array[] = $t_attr_result_array['attributes_model'];
					}

					if($t_attr_result_array['prefix'] == '-')
					{
						$this->attributes_weight -= (double)$t_attr_result_array['weight'];
					}
					else
					{
						$this->attributes_weight += (double)$t_attr_result_array['weight'];
					}
				}
			}
		}
	}

	protected function get_properties_data()
	{
		$this->properties_combi_id = $this->coo_properties_control->extract_combis_id($this->coo_order->products[$this->product_index]['id']);
		if($this->properties_combi_id != '')
		{
			$this->properties_data_array = $this->coo_properties_view->v_coo_properties_control->get_properties_combis_details($this->properties_combi_id, $_SESSION['languages_id']);
		}
	}
	
	protected function get_customizer_data()
	{
		$t_gm_gprint_data = $this->coo_gprint_content_manager->get_content($this->product_id, 'cart');

		if(is_array($t_gm_gprint_data) && empty($t_gm_gprint_data) == false)
		{
			foreach($t_gm_gprint_data as $t_data)
			{
				$this->attributes_names_data_array[] = array(
					'option' => $t_data['NAME'],
					'value' => $t_data['VALUE']
				);
			}
		}
	}

	protected function add_data()
	{
		$this->add_shipping();
		$this->add_weight();
		$this->add_model();
		$this->add_quantity();
		$this->add_images();
		$this->add_attributes();
		$this->add_properties();
		
		$this->content_array['HTML_PARAMS'] = HTML_PARAMS;
		$this->content_array['CHARSET'] = $_SESSION['language_charset'];
		$this->content_array['BASE_URL'] = GM_HTTP_SERVER . DIR_WS_CATALOG;

		$this->content_array['DESCRIPTION'] = $this->coo_tab_tokenizer->get_prepared_output();
		$this->content_array['NAME'] = $this->coo_order->products[$this->product_index]['name'];

		$this->content_array['PRICE'] = $this->coo_xtc_price->xtcFormat($this->coo_order->products[$this->product_index]['price'], true);
		$this->content_array['PRODUCTS_TAX_INFO'] = $this->coo_main->getTaxInfo($this->coo_xtc_price->TAX[$this->coo_product->data['products_tax_class_id']]);
		$this->content_array['VPE_ARRAY'] = get_products_vpe_array($this->coo_order->products[$this->product_index]['id'], $this->coo_order->products[$this->product_index]['price'], $this->attributes_ids_data_array);
		$this->content_array['UNIT'] = $this->coo_order->products[$this->product_index]['unit_name'];
	}

	protected function add_shipping()
	{
		if(ACTIVATE_SHIPPING_STATUS == 'true')
		{
			$t_shipping_time = $this->coo_order->products[$this->product_index]['shipping_time'];

			if(isset($this->properties_combi_id) && $this->coo_product->data['use_properties_combis_shipping_time'] == 1 && ACTIVATE_SHIPPING_STATUS == 'true')
			{
				$t_shipping_time = $this->coo_properties_control->get_properties_combis_shipping_time($this->properties_combi_id);
			}

			$this->content_array['SHIPPING_TIME'] = $t_shipping_time;
		}
		$this->content_array['PRODUCTS_SHIPPING_LINK'] = str_replace(' target="_blank"', '', $this->coo_main->getShippingLink(true, $this->product_id));
	}

	protected function add_weight()
	{
		if(!empty($this->coo_product->data['gm_show_weight']))
		{
			$this->content_array['WEIGHT'] = gm_prepare_number($this->coo_order->products[$this->product_index]['weight'] + $this->attributes_weight, $this->coo_xtc_price->currencies[$this->coo_xtc_price->actualCurr]['decimal_point']);
		}
	}

	protected function add_model()
	{
		$t_products_model = $this->coo_order->products[$this->product_index]['model'];
		if($t_products_model != '' && isset($this->attributes_model_array[0]))
		{
			$t_products_model .= '-' . implode('-', $this->attributes_model_array);
		}
		else
		{
			$t_products_model .= implode('-', $this->attributes_model_array);
		}

		if(!empty($this->properties_combi_id))
		{
			$t_combi_model = $this->coo_properties_control->get_properties_combis_model($this->properties_combi_id);

			if(APPEND_PROPERTIES_MODEL == "true")
			{
				if($t_products_model != '' && $t_combi_model != '')
				{
					$t_products_model = $t_products_model . '-' . $t_combi_model;
				}
				else if($t_combi_model != '')
				{
					$t_products_model = $t_combi_model;
				}
			}
			else
			{
				// Artikelnummer (Artikel) durch Artikelnummer (Kombi) ersetzen
				if($t_combi_model != '')
				{
					$t_products_model = $t_combi_model;
				}
			}
		}
		$this->content_array['MODEL'] = $t_products_model;
	}

	protected function add_quantity()
	{
		$t_quantity = $this->coo_product->data['products_quantity'];

		if(!empty($this->properties_combi_id))
		{
			$t_use_combis_quantity = $this->coo_properties_control->get_use_properties_combis_quantity($this->product_id);

			if(($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_combis_quantity == 2)
			{
				$t_quantity = $this->coo_properties_control->get_properties_combis_quantity($this->properties_combi_id);
			}
		}
		$this->content_array['PRODUCTS_QUANTITY'] = gm_prepare_number($t_quantity, $this->coo_xtc_price->currencies[$this->coo_xtc_price->actualCurr]['decimal_point']);
		$this->content_array['SHOW_PRODUCTS_QUANTITY'] = $this->coo_product->data['gm_show_qty_info'];
	}

	protected function add_images()
	{
		if($this->coo_product->data['products_image'] != '' && $this->coo_product->data['gm_show_image'] == '1')
		{
			$this->content_array['IMAGES_ARRAY'][] = array('IMAGE' => DIR_WS_THUMBNAIL_IMAGES . $this->coo_product->data['products_image'],
				'IMAGE_ALT' => $this->coo_product->data['gm_alt_text']
			);
		}

		$t_mo_images_array = xtc_get_products_mo_images($this->coo_product->data['products_id']);

		if($t_mo_images_array != false)
		{
			$coo_gm_alt_form = MainFactory::create_object('GMAltText');

			foreach($t_mo_images_array as $t_image_array)
			{
				$this->content_array['IMAGES_ARRAY'][] = array('IMAGE' => DIR_WS_THUMBNAIL_IMAGES . $t_image_array['image_name'],
					'IMAGE_ALT' => $coo_gm_alt_form->get_alt($t_image_array["image_id"], $t_image_array['image_nr'], $this->coo_product->data['products_id'])
				);
			}
		}
	}
	
	protected function add_attributes()
	{
		$this->content_array['ATTRIBUTES_ARRAY'] = $this->attributes_names_data_array;
	}
	
	protected function add_properties()
	{
		if($this->properties_combi_id != '')
		{
			$this->content_array['PROPERTIES'] = $this->coo_properties_view->get_order_details_by_combis_id($this->properties_combi_id, 'cart');
			$this->content_array['PROPERTIES_ARRAY'] = $this->properties_data_array;
		}
	}
}