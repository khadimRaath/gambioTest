<?php
/* --------------------------------------------------------------
  CheckoutConfirmationContentView.inc.php 2015-04-13 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_confirmation.php,v 1.137 2003/05/07); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_confirmation.php,v 1.21 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_confirmation.php 1277 2005-10-01 17:02:59Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  agree_conditions_1.01        	Autor:	Thomas Ploenkers (webmaster@oscommerce.at)

  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_display_tax_value.inc.php');
require_once(DIR_FS_INC . 'get_products_vpe_array.inc.php');

class CheckoutConfirmationContentView extends ContentView
{
	protected $coo_payment;
	protected $coo_order;
	protected $coo_order_total;
	protected $coo_xtc_price;
	protected $credit_covers;
	protected $customers_ip;
	protected $customers_status_add_tax_ot;
	protected $customers_status_show_price_tax;
	protected $error_message;
	protected $language;
	protected $languages_id;
	protected $payment;
	protected $shipping_address_book_id;

	public function __construct()
	{
		parent::__construct();

		$this->set_content_template('module/checkout_confirmation.html');
		$this->set_flat_assigns(true);
		$this->set_caching_enabled(false);
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_order',
																			'coo_order_total',
																			'coo_payment',
																			'coo_xtc_price',
																			'credit_covers',
																			'customers_ip',
																			'customers_status_add_tax_ot',
																			'customers_status_show_price_tax',
																			'error_message',
																			'languages_id',
																			'language',
																			'payment',
																			'shipping_address_book_id'));
		if(empty($t_uninitialized_array))
		{
			$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
			$coo_order = $this->coo_order;
			$coo_xtc_price = $this->coo_xtc_price;
			$t_payment = $this->payment;

			$this->content_array['ERROR'] = $this->error_message;

			if(gm_get_conf("GM_LOG_IP") == '1')
			{
				$this->content_array['GM_LOG_IP'] = '1';

				$this->content_array['CUSTOMERS_IP'] = $this->customers_ip;

				if(gm_get_conf("GM_CONFIRM_IP") == '1')
				{
					$this->content_array['GM_CONFIRM_IP'] = '1';
				}
				elseif(gm_get_conf("GM_SHOW_IP") == '1')
				{
					$this->content_array['GM_SHOW_IP'] = '1';
				}
			}

			$this->content_array['DELIVERY_LABEL'] = xtc_address_format($coo_order->delivery['format_id'], $coo_order->delivery, 1, ' ', '<br />');
			$this->content_array['BILLING_LABEL'] = xtc_address_format($coo_order->billing['format_id'], $coo_order->billing, 1, ' ', '<br />');

			$this->content_array['PRODUCTS_EDIT'] = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
			$this->content_array['SHIPPING_ADDRESS_EDIT'] = xtc_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL');
			$this->content_array['BILLING_ADDRESS_EDIT'] = xtc_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL');

			if($this->shipping_address_book_id != false && $coo_order->info['shipping_method'])
			{
				$this->content_array['SHIPPING_METHOD'] = $coo_order->info['shipping_method'];
				$this->content_array['SHIPPING_EDIT'] = xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL');
			}

			$coo_properties_control = MainFactory::create_object('PropertiesControl');
			$coo_properties_view = MainFactory::create_object('PropertiesView');

			$t_products_array = array();

			for($i = 0, $n = sizeof($coo_order->products); $i < $n; $i++)
			{
				$coo_product_item = new product(xtc_get_prid($coo_order->products[$i]['id']));

				$t_options_values_array = array();
				$t_attr_weight = 0;
				$t_attr_model_array = array();

				if(isset($coo_order->products[$i]['attributes']) && is_array($coo_order->products[$i]['attributes']))
				{
					foreach($coo_order->products[$i]['attributes'] AS $t_attributes_data_array)
					{
						$t_options_values_array[$t_attributes_data_array['option_id']] = $t_attributes_data_array['value_id'];
					}

					// calculate attributes weight and get attributes model
					foreach($t_options_values_array AS $t_option_id => $t_value_id)
					{
						$t_attr_sql = "SELECT
											options_values_weight AS weight,
											weight_prefix AS prefix,
											attributes_model
										FROM
											products_attributes
										WHERE
											products_id				= '" . (int)xtc_get_prid($coo_order->products[$i]['id']) . "' AND
											options_id				= '" . (int)$t_option_id . "' AND
											options_values_id		= '" . (int)$t_value_id . "'
										LIMIT 1";
						$t_attr_result = xtc_db_query($t_attr_sql);

						if(xtc_db_num_rows($t_attr_result) == 1)
						{
							$t_attr_result_array = xtc_db_fetch_array($t_attr_result);

							if(trim($t_attr_result_array['attributes_model']) != '')
							{
								$t_attr_model_array[] = $t_attr_result_array['attributes_model'];
							}

							if($t_attr_result_array['prefix'] == '-')
							{
								$t_attr_weight -= (double) $t_attr_result_array['weight'];
							}
							else
							{
								$t_attr_weight += (double) $t_attr_result_array['weight'];
							}
						}
					}
				}

				$t_shipping_time = '';
				if(ACTIVATE_SHIPPING_STATUS == 'true')
				{
					$t_shipping_time = $coo_order->products[$i]['shipping_time'];
				}

				$t_products_weight = '';
				if(!empty($coo_product_item->data['gm_show_weight']))
				{
					// already contains products properties weight
					$t_products_weight = gm_prepare_number((double) $coo_order->products[$i]['weight'] + $t_attr_weight, $coo_xtc_price->currencies[$coo_xtc_price->actualCurr]['decimal_point']);
				}

				$t_products_model = $coo_order->products[$i]['model'];
				if($t_products_model != '' && isset($t_attr_model_array[0]))
				{
					$t_products_model .= '-' . implode('-', $t_attr_model_array);
				}
				else
				{
					$t_products_model .= implode('-', $t_attr_model_array);
				}

				#properties
				$t_properties = '';
				$t_combis_id = '';
				$t_properties_array = array();

				if(strpos($coo_order->products[$i]['id'], 'x') !== false)
				{
					$t_combis_id = (int) substr($coo_order->products[$i]['id'], strpos($coo_order->products[$i]['id'], 'x') + 1);
				}

				if($t_combis_id != '')
				{
					$t_properties = $coo_properties_view->get_order_details_by_combis_id($t_combis_id, 'cart');
					$t_properties_array = $coo_properties_view->v_coo_properties_control->get_properties_combis_details($t_combis_id, $this->languages_id);

					if(method_exists($coo_properties_control, 'get_properties_combis_model'))
					{
						$t_combi_model = $coo_properties_control->get_properties_combis_model($t_combis_id);

						if(APPEND_PROPERTIES_MODEL == "true")
						{
							// Artikelnummer (Kombi) an Artikelnummer (Artikel) anhaengen
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

						if($coo_product_item->data['use_properties_combis_shipping_time'] == 1 && ACTIVATE_SHIPPING_STATUS == 'true')
						{
							$t_shipping_time = $coo_properties_control->get_properties_combis_shipping_time($t_combis_id);
						}
					}
				}

				$t_products_item = array(
					'products_name' => '',
					'quantity' => '',
					'price' => $coo_xtc_price->xtcFormat($coo_order->products[$i]['price'], true),
					'final_price' => '',
					'shipping_status' => '',
					'attributes' => '',
					'flag_last_item' => false,
					'PROPERTIES' => $t_properties,
					'properties_array' => $t_properties_array,
					'products_image' => (!empty($coo_product_item->data['gm_show_image']) && !empty($coo_product_item->data['products_image'])) ? DIR_WS_THUMBNAIL_IMAGES . $coo_product_item->data['products_image'] : '',
					'products_vpe_array' => get_products_vpe_array($coo_order->products[$i]['id'], $coo_order->products[$i]['price'], $t_options_values_array),
					'products_alt' => (!empty($coo_product_item->data['gm_alt_text'])) ? $coo_product_item->data['gm_alt_text'] : $coo_order->products[$i]['name'],
					'checkout_information' => $coo_product_item->data['checkout_information'],
					'products_url' => xtc_href_link('request_port.php', 'module=ProductDetails&id=' . $coo_order->products[$i]['id'], 'SSL'),
					'products_model' => $t_products_model,
					'products_weight' => $t_products_weight,
					'shipping_time' => $t_shipping_time,
					'DATA_ARRAY' => $coo_product_item->data
				);
				$t_products_attributes = array();

				if(ACTIVATE_SHIPPING_STATUS == 'true')
				{
					$t_products_item['shipping_status'] = SHIPPING_TIME . $coo_order->products[$i]['shipping_time'];
				}

				$t_products_item['quantity'] = gm_convert_qty($coo_order->products[$i]['qty'], false);
				$t_products_item['products_name'] = $coo_order->products[$i]['name'];
				$t_products_item['final_price'] = $coo_xtc_price->xtcFormat($coo_order->products[$i]['final_price'], true);
				$t_products_item['unit'] = $coo_order->products[$i]['unit_name'];

				if((isset($coo_order->products[$i]['attributes'])) && (sizeof($coo_order->products[$i]['attributes']) > 0))
				{
					for($j = 0, $n2 = sizeof($coo_order->products[$i]['attributes']); $j < $n2; $j++)
					{
						$t_products_attributes_item = array(
							'option' => $coo_order->products[$i]['attributes'][$j]['option'],
							'value' => $coo_order->products[$i]['attributes'][$j]['value']
						);
						$t_products_attributes[] = $t_products_attributes_item;
					}
					// GX-Customizer:
					$this->add_customizer_data($t_products_attributes, $coo_order->products[$i]['id']);

					$t_products_item['attributes'] = $t_products_attributes;
				}

				$t_products_array[] = $t_products_item;
			}

			$t_products_array[sizeof($t_products_array) - 1]['flag_last_item'] = true;

			$coo_content_master = MainFactory::create_object('ContentMaster');
			$t_confirmation_info_array = $coo_content_master->get_content(198);
			$this->content_array['CONFIRMATION_INFO'] = $t_confirmation_info_array['content_text'];

			# products table part
			$coo_content_view = MainFactory::create_object('ContentView');
			$coo_content_view->set_content_template('module/checkout_confirmation_products.html');
			$coo_content_view->set_content_data('products_data', $t_products_array);
			$t_products_table_part = $coo_content_view->get_html();
			$this->content_array['PRODUCTS_TABLE_PART'] = $t_products_table_part;

			if($coo_order->info['payment_method'] != 'no_payment' && $coo_order->info['payment_method'] != '')
			{
				$coo_lang_file_master->init_from_lang_file('lang/' . $this->language . '/modules/payment/' . $coo_order->info['payment_method'] . '.php');
				$this->content_array['PAYMENT_METHOD'] = constant(MODULE_PAYMENT_ . strtoupper($coo_order->info['payment_method']) . _TEXT_TITLE);

				if(isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error()))
				{
					$this->content_array['error'] = $error['title'] . '<br />' . htmlspecialchars_wrapper($error['error']);
				}
			}

			$this->content_array['PAYMENT_EDIT'] = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL');

			if(MODULE_ORDER_TOTAL_INSTALLED)
			{
				$coo_payment = $this->coo_payment;
				$coo_order_total = $this->coo_order_total;

				if(is_array($coo_payment->modules) && strpos($t_payment, 'saferpaygw') === false)
				{
					$coo_order_total->process();
				}

				$t_total_block_array = $coo_order_total->output_array();
			}

			$this->content_array['total_block_data'] = $t_total_block_array;

			//GM_PATCH 0000318
			$coo_payment = $this->coo_payment;
			$coo_payment->update_status();

			if(is_array($coo_payment->modules))
			{
				$confirmation = $coo_payment->confirmation();

				if(empty($confirmation) == false)
				{
					$this->content_array['payment_information_data'] = $confirmation['fields'];
					$this->content_array['PAYMENT_TITLE'] = $confirmation['title'];
				}
			}

			if(xtc_not_null($coo_order->info['comments']))
			{
				$this->content_array['ORDER_COMMENTS'] = nl2br(htmlspecialchars_wrapper($coo_order->info['comments'])) . xtc_draw_hidden_field('comments', $coo_order->info['comments']);
			}

			// Call Refresh Hook
			$coo_payment->refresh();
			if(isset($GLOBALS[$t_payment]->form_action_url) && !$GLOBALS[$t_payment]->tmpOrders && $t_payment != 'no_payment')
			{
				$form_action_url = $GLOBALS[$t_payment]->form_action_url;
			}
			else
			{
				$form_action_url = xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
			}

			// START saferpay
			// we need a source for our js to be loaded befor form
			$sPreForm = '';

			if(method_exists($GLOBALS[$t_payment], 'confirm_pre_form'))
			{
				$sPreForm = $GLOBALS[$t_payment]->confirm_pre_form();
			}

			$this->content_array['CHECKOUT_FORM'] = $sPreForm . xtc_draw_form('checkout_confirmation', $form_action_url, 'post');
			$this->content_array['CHECKOUT_FORM_PREFORM'] = $sPreForm;
			$this->content_array['CHECKOUT_FORM_ACTION_URL'] = $form_action_url;
			// END saferpay

			$payment_button = '';
			if(is_array($coo_payment->modules))
			{
				$payment_button .= $coo_payment->process_button();
			}

			$this->content_array['MODULE_BUTTONS'] = $payment_button;

			$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('checkout_confirmation', $this->languages_id));

			// Heidelpay: ueberschreiben von CHECKOUT_FORM, MODULE_BUTTONS und CHECKOUT_BUTTON
			if(substr_wrapper($coo_payment->selected_module, 0, 9) == 'heidelpay' && $coo_payment->selected_module != 'heidelpaypp')
			{
				$HEIDELPAY_CALL_FORM = true;
				$this->content_array['CHECKOUT_FORM'] = '';
				$payment_button = $coo_payment->process_button();
				$this->content_array['MODULE_BUTTONS'] = $payment_button;
				$this->content_array['CHECKOUT_BUTTON'] = '';
			}

			if(gm_get_env_info('TEMPLATE_VERSION') < FIRST_GX2_TEMPLATE_VERSION)
			{
				if(gm_get_conf('GM_SHOW_PRIVACY_CONFIRMATION') == 1)
				{
					$this->content_array['PRIVACY_CONFIRMATION_TEXT'] = GM_CONFIRMATION_PRIVACY;
					$this->content_array['PRIVACY_CONFIRMATION_URL'] = xtc_href_link('shop_content.php', 'coID=2', 'SSL');
				}

				if(gm_get_conf('GM_SHOW_CONDITIONS_CONFIRMATION') == 1)
				{
					$this->content_array['CONDITIONS_CONFIRMATION_TEXT'] = GM_CONFIRMATION_CONDITIONS;
					$this->content_array['CONDITIONS_CONFIRMATION_URL'] = xtc_href_link('shop_content.php', 'coID=3', 'SSL');
				}

				if(gm_get_conf('GM_SHOW_WITHDRAWAL_CONFIRMATION') == 1)
				{
					$this->content_array['WITHDRAWAL_CONFIRMATION_TEXT'] = GM_CONFIRMATION_WITHDRAWAL;
					$this->content_array['WITHDRAWAL_CONFIRMATION_URL'] = xtc_href_link('shop_content.php', 'coID=' . gm_get_conf('GM_WITHDRAWAL_CONTENT_ID'), 'SSL');
				}
			}
			else
			{
				if(gm_get_conf('GM_SHOW_PRIVACY_CONFIRMATION') == 1)
				{
					$this->content_array['PRIVACY_CONFIRMATION_TEXT'] = GM_CONFIRMATION_PRIVACY;
					$this->content_array['PRIVACY_CONFIRMATION_URL'] = xtc_href_link('popup_content.php', 'coID=2&lightbox_mode=1', 'SSL');
				}

				if(gm_get_conf('GM_SHOW_CONDITIONS_CONFIRMATION') == 1)
				{
					$this->content_array['CONDITIONS_CONFIRMATION_TEXT'] = GM_CONFIRMATION_CONDITIONS;
					$this->content_array['CONDITIONS_CONFIRMATION_URL'] = xtc_href_link('popup_content.php', 'coID=3&lightbox_mode=1', 'SSL');
				}

				if(gm_get_conf('GM_SHOW_WITHDRAWAL_CONFIRMATION') == 1)
				{
					$this->content_array['WITHDRAWAL_CONFIRMATION_TEXT'] = GM_CONFIRMATION_WITHDRAWAL;
					$this->content_array['WITHDRAWAL_CONFIRMATION_URL'] = xtc_href_link('popup_content.php', 'coID=' . gm_get_conf('GM_WITHDRAWAL_CONTENT_ID') . '&lightbox_mode=1', 'SSL');
				}
			}

			$coo_ts_excellence = MainFactory::create('TrustedShopsExcellenceContentView', $coo_order);
			$t_view_html = $coo_ts_excellence->get_html();
			$this->content_array['MODULE_ts_excellence'] = $t_view_html;

			$this->content_array['LIGHTBOX'] = gm_get_conf('GM_LIGHTBOX_CHECKOUT');
			$this->content_array['LIGHTBOX_CLOSE'] = xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL');

			// BEGIN AmazonAdvancedPayment
			if(empty($_SESSION['amazonadvpay_order_ref_id']) !== true)
			{
				$coo_aap = MainFactory::create_object('AmazonAdvancedPayment');
				if($_SESSION['cart']->get_content_type() == 'virtual')
				{
					$this->content_array['amazon_checkout_address'] = $coo_aap->get_text('no_address_for_virtual_cart');
				}
				else
				{
					$this->content_array['amazon_checkout_address'] = '<div id="readOnlyAddressBookWidgetDiv"></div>';
				}
				$this->content_array['amazon_checkout_payment'] = '<div id="readOnlyWalletWidgetDiv"></div>';
			}
			// END AmazonAdvancedPayment
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	// replaces gm/modules/gm_gprint_checkout_confirmation.php
	protected function add_customizer_data(&$t_attributes_array, $p_product)
	{
		$coo_gprint_content_manager = new GMGPrintContentManager();

		$t_gm_gprint_data = $coo_gprint_content_manager->get_content($p_product, 'cart');

		for($j = 0; $j < count($t_gm_gprint_data); $j++)
		{
			$t_attributes_array[] = array('option' => $t_gm_gprint_data[$j]['NAME'],
											'value' => $t_gm_gprint_data[$j]['VALUE']);
		}
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['languages_id']						= array('type' 			=> 'int',
																					'strict'		=> 'false');
		$this->validation_rules_array['shipping_address_book_id']			= array('type' 			=> 'int',
																					'strict'		=> 'false');
		$this->validation_rules_array['credit_covers']						= array('type' 			=> 'bool',
																					'strict' 		=> 'true');
		$this->validation_rules_array['customers_status_add_tax_ot']		= array('type' 			=> 'bool',
																					'strict' 		=> 'true');
		$this->validation_rules_array['customers_status_show_price_tax']	= array('type' 			=> 'bool',
																					'strict' 		=> 'true');
		$this->validation_rules_array['customers_ip']						= array('type' 			=> 'string',
																					'strict' 		=> 'false');
		$this->validation_rules_array['error_message']						= array('type' 			=> 'string',
																					'strict' 		=> 'false');
		$this->validation_rules_array['language']							= array('type' 			=> 'string',
																					'strict' 		=> 'false');
		$this->validation_rules_array['payment']							= array('type' 			=> 'string',
																					'strict' 		=> 'false');
		$this->validation_rules_array['coo_payment']						= array('type' 			=> 'object',
																					'object_type'	=> 'payment');
		$this->validation_rules_array['coo_order']							= array('type' 			=> 'object',
																					'object_type'	=> 'order');
		$this->validation_rules_array['coo_order_total']					= array('type' 			=> 'object',
																					'object_type'	=> 'order_total');
		$this->validation_rules_array['coo_xtc_price']						= array('type' 			=> 'object',
																					'object_type'	=> 'xtcPrice');
	}
}
