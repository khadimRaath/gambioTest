<?php

/* --------------------------------------------------------------
  CheckoutShippingContentControl.inc.php 2014-01-14 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_shipping.php,v 1.15 2003/04/08); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_shipping.php,v 1.20 2003/08/20); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_shipping.php 1037 2005-07-17 15:25:32Z gwinger $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once (DIR_FS_INC.'xtc_count_shipping_modules.inc.php');

// include classes
require_once (DIR_WS_CLASSES . 'shipping.php');
MainFactory::load_class('CheckoutControl');

class CheckoutShippingContentControl extends CheckoutControl
{

	public function proceed()
	{
		$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
		$coo_xtc_price = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
		$t_shipping_free_over = 0;
		
		if($this->check_stock() == false)
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_SHOPPING_CART));
		}

		// if no shipping destination address was selected, use the customers own address as default
		if(!isset($_SESSION['sendto']))
		{
			$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
		}
		else
		{
			// verify the selected shipping address
			$check_address_query = xtc_db_query("SELECT COUNT(*) AS total 
													FROM " . TABLE_ADDRESS_BOOK . " 
													WHERE
														customers_id = '" . (int) $_SESSION['customer_id'] . "' AND
														address_book_id = '" . (int) $_SESSION['sendto'] . "'", 'db_link', false);
			$check_address = xtc_db_fetch_array($check_address_query);

			if($check_address['total'] != '1')
			{
				$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
				
				if(isset($_SESSION['shipping']))
				{
					unset($_SESSION['shipping']);
				}
			}
		}

		$order = new order();

		// register a random ID in the session to check throughout the checkout procedure
		// against alterations in the shopping cart contents
		$_SESSION['cartID'] = $_SESSION['cart']->cartID;

		// if the order contains only virtual products, forward the customer to the billing page as
		// a shipping address is not needed
		if($order->content_type == 'virtual' || ($order->content_type == 'virtual_weight') || ($_SESSION['cart']->count_contents_non_virtual() == 0))
		{ // GV Code added
			$_SESSION['shipping'] = false;
			$_SESSION['sendto'] = false;
			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
			return true;
		}

		// used in shipping class
		$GLOBALS['total_weight'] = $_SESSION['cart']->show_weight();
		$GLOBALS['total_count'] = $_SESSION['cart']->count_contents();

		if($order->delivery['country']['iso_code_2'] != '')
		{
			$_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];
		}
		
		// load all enabled shipping modules
		$shipping_modules = new shipping();

		if(defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true'))
		{
			switch(MODULE_ORDER_TOTAL_SHIPPING_DESTINATION)
			{
				case 'national' :
					if($order->delivery['country_id'] == STORE_COUNTRY)
						$pass = true;
					break;
				case 'international' :
					if($order->delivery['country_id'] != STORE_COUNTRY)
						$pass = true;
					break;
				case 'both' :
					$pass = true;
					break;
				default :
					$pass = false;
					break;
			}
			
			$free_shipping = false;

			$t_shipping_free_over = (double)MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER;
			if($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && (int)MODULE_ORDER_TOTAL_SHIPPING_TAX_CLASS > 0)
			{
				$t_shipping_free_over = $t_shipping_free_over / (1 + $coo_xtc_price->TAX[MODULE_ORDER_TOTAL_SHIPPING_TAX_CLASS] / 100);
			}

			if($pass == true && ($order->info['total'] - $order->info['shipping_cost'] >= $coo_xtc_price->xtcFormat($t_shipping_free_over, false, 0, true)))
			{
				$free_shipping = true;
				$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/order_total/ot_shipping.php');
			}
		}
		else
		{
			$free_shipping = false;
		}
		
		// process the selected shipping method
		if(isset($this->v_data_array['POST']['action']) && ($this->v_data_array['POST']['action'] == 'process'))
		{
			if((xtc_count_shipping_modules() > 0) || ($free_shipping == true))
			{
				if((isset($this->v_data_array['POST']['shipping'])) && (strpos($this->v_data_array['POST']['shipping'], '_')))
				{
					$_SESSION['shipping'] = $this->v_data_array['POST']['shipping'];

					list ($module, $method) = explode('_', $_SESSION['shipping']);

					if(!is_object($$module) && isset($GLOBALS[$module]) && is_object($GLOBALS[$module]))
					{
						$$module = $GLOBALS[$module];
					}

					if(is_object($$module) || $free_shipping == true)
					{
						if($_SESSION['shipping'] == 'free_free')
						{
							$quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
							$quote[0]['methods'][0]['cost'] = '0';
						}
						elseif(is_object($$module))
						{
							$quote = $shipping_modules->quote($method, $module);
						}
						else
						{
							$quote['error'] = 'error';
						}
						if(isset($quote['error']))
						{
							unset($_SESSION['shipping']);
						}
						else
						{
							if((isset($quote[0]['methods'][0]['title'])) && (isset($quote[0]['methods'][0]['cost'])))
							{
								$_SESSION['shipping'] = array('id' => $_SESSION['shipping'], 'title' => (($free_shipping == true) ? $quote[0]['methods'][0]['title'] : $quote[0]['module'] . ' (' . $quote[0]['methods'][0]['title'] . ')'), 'cost' => $quote[0]['methods'][0]['cost']);

								$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
								return true;
							}
						}
					}
					else
					{
						unset($_SESSION['shipping']);
					}
				}
			}
			else
			{
				$_SESSION['shipping'] = false;

				$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
				return true;
			}
		}
		
		// get all available shipping quotes
		$t_quotes_array = $shipping_modules->quote();
		
		// if no shipping method has been selected, automatically select the cheapest method.
		// if the modules status was changed when none were available, to save on implementing
		// a javascript force-selection method, also automatically select the cheapest shipping
		// method if more than one module is now enabled
		if(!isset($_SESSION['shipping']) || (isset($_SESSION['shipping']) && $_SESSION['shipping'] == false && xtc_count_shipping_modules() > 1))
		{
			$_SESSION['shipping'] = $shipping_modules->cheapest();
		}

		$coo_checkout_shipping_view = MainFactory::create_object('CheckoutShippingContentView');
		$coo_checkout_shipping_view->set_('free_shipping', $free_shipping);
		$coo_checkout_shipping_view->set_('quotes_array', $t_quotes_array);
		$coo_checkout_shipping_view->set_('shipping_free_over', $t_shipping_free_over);
		$coo_checkout_shipping_view->set_('coo_xtc_price', $coo_xtc_price);
		$coo_checkout_shipping_view->set_('address_book_id', $_SESSION['sendto']);
		$coo_checkout_shipping_view->set_('customer_id', $_SESSION['customer_id']);
		$coo_checkout_shipping_view->set_('language', $_SESSION['language']);
		
		if(isset($_SESSION['shipping']['id']))
		{
			$coo_checkout_shipping_view->set_('selected_shipping_method', $_SESSION['shipping']['id']);
		}		
		
		$t_style_edit_active = false;
		if($_SESSION['style_edit_mode'] == 'edit')
		{
			$t_style_edit_active = true;
		}
		$coo_checkout_shipping_view->set_('style_edit_active', $t_style_edit_active);
		
		$this->v_output_buffer = $coo_checkout_shipping_view->get_html();

		return true;
	}
}
