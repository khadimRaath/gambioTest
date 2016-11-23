<?php
/* --------------------------------------------------------------
  PriceOfferContentView.inc.php 2016-08-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

// include needed functions
require_once(DIR_FS_CATALOG . 'gm/inc/gm_prepare_number.inc.php');
require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');
require_once(DIR_FS_INC . 'xtc_random_charcode.inc.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMAttributesCalculator.php');

class PriceOfferContentView extends ContentView
{
	protected $product_id;
	protected $language_id;
	protected $propertie_value_ids_array;
	protected $attributes_ids_array;
	protected $coo_xtc_price;
	protected $coo_main;
	protected $coo_seo_boost;
	protected $coo_captcha;
	protected $propertie_combis_id;
	protected $attributes_data_array = array();
	protected $product_data_array = array();
	protected $product_price;
	protected $customer_data_array;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/gm_price_offer.html');
		$this->set_flat_assigns(true);
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['product_id']					= array('type' => 'int');
		$this->validation_rules_array['language_id']				= array('type' => 'int');
		$this->validation_rules_array['propertie_value_ids_array']	= array('type' => 'array');
		$this->validation_rules_array['attributes_ids_array']		= array('type' => 'array');
		$this->validation_rules_array['coo_xtc_price']				= array('type' => 'object',
																			'object_type' => 'xtcPrice');
		$this->validation_rules_array['coo_main']					= array('type' => 'object',
																			'object_type' => 'main');
		$this->validation_rules_array['coo_seo_boost']				= array('type' => 'object',
																			'object_type' => 'GMSEOBoost');
		$this->validation_rules_array['coo_captcha']				= array('type' => 'object',
																			'object_type' => 'Captcha');
		$this->validation_rules_array['propertie_combis_id']		= array('type' => 'int');
		$this->validation_rules_array['attributes_data_array']		= array('type' => 'array');
		$this->validation_rules_array['propertie_data']		        = array('type' => 'array');
		$this->validation_rules_array['product_data_array']			= array('type' => 'array');
		$this->validation_rules_array['product_price']				= array('type' => 'double');
		$this->validation_rules_array['customer_data_array']		= array('type' => 'array');
	}

	public function prepare_data()
	{
		$this->coo_xtc_price = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
		$this->coo_xtc_price->showFrom_Attributes = false;
		$this->coo_main = new main();
		$this->coo_seo_boost = MainFactory::create_object('GMSEOBoost');
		$this->coo_captcha = MainFactory::create_object('Captcha');
		
		$this->build_html = false;
		
		$t_uninitialized_array = $this->get_uninitialized_variables(array('product_id', 'language_id'));
		if(empty($t_uninitialized_array))
		{
			$this->get_data();
			$this->add_data();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}


	/**
	 * @param array $propertyValueIds
	 */
	public function set_propertie_value_ids_array(array $propertyValueIds)
	{
		$ids = array();
		
		foreach($propertyValueIds as $id)
		{
			$ids[] = (int)$id;
		}
		
		$this->propertie_value_ids_array = $ids;
	}


	/**
	 * @param array $attributeIds
	 */
	public function set_attributes_ids_array(array $attributeIds)
	{
		$ids = array();

		foreach($attributeIds as $optionId => $valueId)
		{
			$ids[$optionId] = (int)$valueId;
		}

		$this->attributes_ids_array = $ids;
	}
	
	
	protected function get_data()
	{
		$this->get_product_data();
		if(empty($this->product_data_array) == false)
		{
			$this->build_html = true;
			$this->get_propertie_combi();
			$this->get_attributes();
			$this->get_price();
			$this->get_customer_data();
		}
	}
	
	protected function get_product_data()
	{
		$t_query = 'SELECT 
						pd.products_name,
						pd.products_short_description,
						p.products_image,
						p.products_price,
						p.products_tax_class_id
					FROM
						products_description pd,
						products p
					WHERE
						p.products_id = "' . $this->product_id . '"
						AND pd.products_id = "' . $this->product_id . '"
						AND pd.language_id = "' . $this->language_id . '"';

		$t_result = xtc_db_query($t_query);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$this->product_data_array = xtc_db_fetch_array($t_result);
		}
	}
	
	protected function get_propertie_combi()
	{
		if(isset($this->propertie_value_ids_array))
		{
			$coo_properties_control = MainFactory::create_object('PropertiesControl');
			$this->propertie_combis_id = $coo_properties_control->get_combis_id_by_value_ids_array($this->product_id, $this->propertie_value_ids_array);

			if(empty($this->propertie_combis_id))
			{
				$t_cheapest_combi_array = $coo_properties_control->get_cheapest_combi($this->product_id, $this->language_id);
				$this->propertie_combis_id = $t_cheapest_combi_array['products_properties_combis_id'];
			}

			$t_properties_name = array();
			foreach($coo_properties_control->get_properties_combis_details($this->propertie_combis_id, $this->language_id) as $property)
			{
				$t_properties_name[] = array(
					"name"  => $property['properties_name'],
					"value" => $property['values_name']
				);
			}
			$this->content_array['PROPERTIES'] = $t_properties_name;
		}
	}
	
	protected function get_attributes()
	{
		if(isset($this->attributes_ids_array) && empty($this->attributes_ids_array) == false)
		{
			foreach($this->attributes_ids_array as $key => $unit)
			{
				$this->attributes_data_array[] = array('option' => (int)$key, 'value' => (int)$unit);
			}
		}
	}
	
	protected function get_price()
	{
		$propertiesControl = MainFactory::create_object('PropertiesControl');
		$combiId           = $propertiesControl->get_combis_id_by_value_ids_array($this->product_id,
		                                                                          $this->propertie_value_ids_array);
		
		$showFrom_Attributes = $GLOBALS['xtPrice']->showFrom_Attributes;
		$GLOBALS['xtPrice']->showFrom_Attributes = false;
		
		$attributes = array();
		if(isset($this->attributes_ids_array))
		{
			foreach($this->attributes_ids_array as $option => $value)
			{
				$attributes[] = array('option' => $option, 'value' => $value);
			}
		}
		
		$gmAttrCalc = new GMAttributesCalculator($this->product_id, $attributes, $this->product_data_array['products_tax_class_id'], $combiId);
		$this->product_price = $gmAttrCalc->calculate(1, true);
		$GLOBALS['xtPrice']->showFrom_Attributes = $showFrom_Attributes;
	}
	
	protected function get_customer_data()
	{
		if(isset($this->v_env_post_array['name']))
		{
			$this->customer_data_array['name'] = trim($this->v_env_post_array['name']);
		}
		if(isset($this->v_env_post_array['email']))
		{
			$this->customer_data_array['email'] = trim($this->v_env_post_array['email']);
		}
		if(isset($this->v_env_post_array['telephone']))
		{
			$this->customer_data_array['telephone'] = trim($this->v_env_post_array['telephone']);
		}
		if(isset($this->v_env_post_array['price']))
		{
			$this->customer_data_array['price'] = trim($this->v_env_post_array['price']);
		}
		if(isset($this->v_env_post_array['offerer']))
		{
			$this->customer_data_array['offerer'] = trim($this->v_env_post_array['offerer']);
		}
		if(isset($this->v_env_post_array['link']))
		{
			$this->customer_data_array['link'] = trim($this->v_env_post_array['link']);
		}
		if(isset($this->v_env_post_array['message']))
		{
			$this->customer_data_array['message'] = trim($this->v_env_post_array['message']);
		}
	}
	
	protected function add_data()
	{
		$this->add_product_data();
		$this->add_attributes_data();
		$this->add_form_data();
		$this->add_customer_data();
		if(isset($this->content_array['VVCODE_ERROR']) == false && isset($this->content_array['ERROR']) == false && empty($_POST) == false)
		{
			$this->send_mail();
		}
	}
	
	protected function add_product_data()
	{
		$this->content_array['PRODUCT_NAME'] = $this->product_data_array['products_name'];
		$t_products_short_description = str_replace('<br />', " ", $this->product_data_array['products_short_description']);
		$t_products_short_description = str_replace('<br>', " ", $t_products_short_description);
		$t_products_short_description = strip_tags($t_products_short_description);
		$this->content_array['PRODUCT_SHORT_DESCRIPTION'] = trim($t_products_short_description);

		$t_image = '';
		if($this->product_data_array['products_image'] != '')
		{
			$t_image = DIR_WS_THUMBNAIL_IMAGES . $this->product_data_array['products_image'];
		}
		$this->content_array['PRODUCT_IMAGE'] = $t_image;
		$this->content_array['PRODUCT_POPUP_LINK'] = 'javascript:popupWindow(\'' . xtc_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $this->product_id . '&imgID=0') . '\')';

		$tax_rate = $this->coo_xtc_price->TAX[$this->product_data_array['products_tax_class_id']];
		$tax_info = $this->coo_main->getTaxInfo($tax_rate);
		$this->content_array['PRODUCTS_TAX_INFO'] = $tax_info;
		$this->content_array['PRODUCTS_SHIPPING_LINK'] = $this->coo_main->getShippingLink(true, $this->product_id);
		$this->content_array['PRODUCT_PRICE'] = $this->product_price;
	}
	
	protected function add_attributes_data()
	{
		if(isset($this->attributes_data_array) && empty($this->attributes_data_array) == false)
		{
			$t_attributes_name = array();
			foreach($this->attributes_data_array as $t_attribute)
			{
				$t_query = 'SELECT
								po.products_options_name,
								pov.products_options_values_name 
							FROM 
								products_attributes pa,
								products_options po,
								products_options_values pov
							WHERE 
								pa.products_id = "' . $this->product_id . '"
								AND pa.options_id = "' . $t_attribute['option'] . '"
								AND pa.options_values_id = "' . $t_attribute['value'] . '"
								AND pa.options_id = po.products_options_id
								AND pov.products_options_values_id = pa.options_values_id
								AND pov.language_id = "' . $this->language_id . '"
								AND po.language_id = "' . $this->language_id . '"';
				
				$t_result = xtc_db_query($t_query);
				while($row = xtc_db_fetch_array($t_result))
				{
					$t_attributes_name[] = array(
						"name"  => $row['products_options_name'],
						"value" => $row['products_options_values_name']
					);
				}
			}
			$this->content_array['ATTRIBUTES'] = $t_attributes_name;
		}
	}
	
	protected function add_form_data()
	{
		$this->content_array['FORM_ID'] = 'gm_price_offer';
		$t_get_params_string = $this->get_get_params_string();
		$this->content_array['FORM_ACTION_URL'] = xtc_href_link('gm_price_offer.php', $t_get_params_string, 'NONSSL', true, true, true);
		$this->content_array['FORM_METHOD'] = 'post';
		if($this->coo_seo_boost->boost_products)
		{
			$t_product_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url($this->product_id, $this->product_data_array['products_name']));
		}
		else
		{
			$t_product_link = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($this->product_id, $this->product_data_array['products_name']));
		}
		
		$this->content_array['INPUT_NAME'] = $_SESSION['customer_first_name'] . ' ' . $_SESSION['customer_last_name'];
		$this->content_array['GM_CAPTCHA'] = $this->coo_captcha->get_html();
		$this->content_array['VALIDATION_ACTIVE'] = gm_get_conf('GM_PRICE_OFFER_VVCODE');
		
		$this->content_array['BUTTON_BACK_LINK'] = $t_product_link;
		$this->content_array['GM_PRIVACY_LINK'] = gm_get_privacy_link('GM_CHECK_PRIVACY_FOUND_CHEAPER');
		
		$this->content_array['show_privacy_checkbox'] = gm_get_conf('PRIVACY_CHECKBOX_FOUND_CHEAPER');
		$this->content_array['privacy_accepted'] = isset($this->v_env_post_array['privacy_accepted']) ? $this->v_env_post_array['privacy_accepted'] : '0';
	}
	
	protected function get_get_params_string()
	{
		$t_get_params_array = array();
		foreach($this->v_env_get_array as $t_key => $t_unit)
		{
			if(is_array($t_unit))
			{
				foreach($t_unit as $t_key2 => $t_unit2)
				{
					$t_get_params_array[] = htmlspecialchars_wrapper($t_key) . rawurlencode('[') . htmlspecialchars_wrapper($t_key2) . rawurlencode(']') . '=' . htmlspecialchars_wrapper($t_unit2);
				}
			}
			else
			{
				$t_get_params_array[] = htmlspecialchars_wrapper($t_key) . '=' . htmlspecialchars_wrapper($t_unit);
			}
		}
		return implode('&', $t_get_params_array);
	}
	
	protected function add_customer_data()
	{
		if(empty($this->customer_data_array) == false)
		{
			$this->add_error();
			$this->content_array['INPUT_NAME'] = $this->customer_data_array['name'];
			$this->content_array['INPUT_EMAIL'] = $this->customer_data_array['email'];
			$this->content_array['INPUT_TELEPHONE'] = $this->customer_data_array['telephone'];
			$this->content_array['INPUT_PRICE'] = $this->customer_data_array['price'];
			$this->content_array['INPUT_OFFERER'] = $this->customer_data_array['offerer'];
			$this->content_array['INPUT_LINK'] = $this->customer_data_array['link'];
			$this->content_array['INPUT_MESSAGE'] = $this->customer_data_array['message'];
		}
	}
	
	protected function add_error()
	{
		$t_captcha_is_valid = $this->coo_captcha->is_valid($this->v_env_post_array, 'GM_PRICE_OFFER_VVCODE');

		if($t_captcha_is_valid == false)
		{
			$this->content_array['VVCODE_ERROR'] = GM_PRICE_OFFER_WRONG_CODE;
		}
		
		if(empty($this->customer_data_array['name']) || empty($this->customer_data_array['email']) || empty($this->customer_data_array['link']))
		{
			$this->content_array['ERROR'] = GM_PRICE_OFFER_ERROR;
		}
		
		if(gm_get_conf('GM_CHECK_PRIVACY_FOUND_CHEAPER') === '1'
		   && gm_get_conf('PRIVACY_CHECKBOX_FOUND_CHEAPER') === '1'
		   && (!isset($this->v_env_post_array['privacy_accepted'])
		       || $this->v_env_post_array['privacy_accepted'] !== '1')
		)
		{
			$this->content_array['ERROR'] .= ' ' . ENTRY_PRIVACY_ERROR;
		}
	}
	
	protected function send_mail()
	{
		if($this->coo_seo_boost->boost_products)
		{
			$t_product_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url($this->product_id, $this->product_data_array['products_name']));
		}
		else
		{
			$t_product_link = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($this->product_id, $this->product_data_array['products_name']));
		}
		
		$productDetails = '';
		
		$this->add_attributes_data();
		if(isset($this->content_array['ATTRIBUTES']))
		{
			foreach($this->content_array['ATTRIBUTES'] as $attribute)
			{
				$productDetails .= $attribute['name'] . ': ' . $attribute['value'] . "\n";
			}
		}
		
		$this->get_propertie_combi();
		if(isset($this->content_array['PROPERTIES']))
		{
			foreach($this->content_array['PROPERTIES'] as $property)
			{
				$productDetails .= $property['name'] . ': ' . $property['value'] . "\n";
			}
		}
		
		$t_mail_content = GM_PRICE_OFFER_MAIL_CUSTOMER . $this->customer_data_array['name']
				. "\n" . GM_PRICE_OFFER_MAIL_EMAIL . $this->customer_data_array['email']
				. "\n" . GM_PRICE_OFFER_MAIL_TELEPHONE . $this->customer_data_array['telephone']
				. "\n\n" . $this->product_data_array['products_name'] . " (" . trim(strip_tags($this->product_price)) . "):\n" . $t_product_link
				. "\n" . $productDetails
				. "\n\n" . GM_PRICE_OFFER_MAIL_LINK . ' ' . $this->customer_data_array['link']
				. "\n" . GM_PRICE_OFFER_MAIL_PRICE . ' ' . $this->customer_data_array['price']
				. "\n" . GM_PRICE_OFFER_MAIL_OFFERER . ' ' . $this->customer_data_array['offerer']
				. "\n\n" . GM_PRICE_OFFER_MAIL_MESSAGE . "\n" . gm_prepare_string($this->customer_data_array['message']);

		// send mail
		xtc_php_mail($this->customer_data_array['email'], $this->customer_data_array['name'], STORE_OWNER_EMAIL_ADDRESS, STORE_NAME, '', $this->customer_data_array['email'], $this->customer_data_array['name'], '', '', GM_PRICE_OFFER_MAIL_SUBJECT . $this->product_data_array['products_name'], nl2br(htmlentities_wrapper($t_mail_content)), $t_mail_content);
		$this->content_array['MAIL_OUT'] = GM_PRICE_OFFER_MAIL_OUT;
	}
}