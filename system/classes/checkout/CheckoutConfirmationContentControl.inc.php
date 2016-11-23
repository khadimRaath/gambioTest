<?php

/* --------------------------------------------------------------
  CheckoutConfirmationContentControl.inc.php 2016-06-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
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

// include needed classes
require_once(DIR_WS_CLASSES . 'order_total.php');
require_once(DIR_WS_CLASSES . 'payment.php');
MainFactory::load_class('CheckoutControl');

class CheckoutConfirmationContentControl extends CheckoutControl
{
	public function proceed()
	{
		if($this->check_stock() == false)
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_SHOPPING_CART));
			return true;
		}

		if($this->check_cart_id() == false || $this->check_shipping() == false)
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
			return true;
		}

		if($this->check_payment() == false)
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
			return true;
		}

		if($this->check_billing_address_for_packstation() == false)
		{
			$_SESSION['gm_error_message'] = urlencode(ERROR_BILLING_ADDRESS_IS_PACKSTATION);
			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
			return true;
		}

		// moneybookers
		if(isset($_SESSION['tmp_oID']))
		{
			unset($_SESSION['tmp_oID']);
		}

		// mediafinanz
		if(gm_get_conf('MODULE_CENTER_MEDIAFINANZ_INSTALLED') == true)
		{
			include_once(DIR_FS_CATALOG . 'includes/modules/mediafinanz/include_checkout_confirmation.php');
		}

		//check if display conditions on checkout page is true

		if(isset($this->v_data_array['POST']['payment']))
		{
			$_SESSION['payment'] = xtc_db_prepare_input($this->v_data_array['POST']['payment']);
		}

		if($this->v_data_array['POST']['comments_added'] != '')
		{
			$_SESSION['comments'] = xtc_db_prepare_input($this->v_data_array['POST']['comments']);
		}

		//-- TheMedia Begin check if display conditions on checkout page is true
		if(isset($this->v_data_array['POST']['cot_gv']))
		{
			$_SESSION['cot_gv'] = true;
		}

		// if conditions are not accepted, redirect the customer to the payment method selection page
		if(gm_get_conf('GM_CHECK_CONDITIONS') == 1 && $_REQUEST['conditions'] == false)
		{
			$error = str_replace('\n', '<br />', ERROR_CONDITIONS_NOT_ACCEPTED_AGB);
		}

		if(gm_get_conf('GM_CHECK_WITHDRAWAL') == 1 && $this->v_data_array['POST']['withdrawal'] == false)
		{
			$error = str_replace('\n', '<br />', ERROR_CONDITIONS_NOT_ACCEPTED_WITHDRAWAL);
		}
		
		if((gm_get_conf('GM_CHECK_WITHDRAWAL') == 1 && $this->v_data_array['POST']['withdrawal'] == false)
		   && (gm_get_conf('GM_CHECK_CONDITIONS') == 1 && $_REQUEST['conditions'] == false)
		)
		{
			$error = str_replace('\n', '<br />', ERROR_CONDITIONS_NOT_ACCEPTED);
		}

		if(!isset($_SESSION['conditions']) || !isset($_SESSION['withdrawal']))
		{
			if(($this->v_data_array['POST']['conditions'] == false && gm_get_conf('GM_CHECK_CONDITIONS') == 1)
				|| ($this->v_data_array['POST']['withdrawal'] == false && gm_get_conf('GM_CHECK_WITHDRAWAL') == 1)
			)
			{
				$_SESSION['gm_error_message'] = urlencode($error);
				$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
				return true;
			}
			else
			{
				if($this->v_data_array['POST']['conditions'] == true)
				{
					$_SESSION['conditions'] = 'true';
				}

				if($this->v_data_array['POST']['withdrawal'] == true)
				{
					$_SESSION['withdrawal'] = 'true';
				}
			}
		}

		// GV Code ICW ADDED FOR CREDIT CLASS SYSTEM
		$GLOBALS['order'] = new order();

		// GV Code Start
		$order_total_modules = new order_total();
		$order_total_modules->collect_posts();
		$order_total_modules->pre_confirmation_check();
		// GV Code End

		// load the selected payment module
		if(isset($_SESSION['credit_covers']))
		{
			$_SESSION['payment'] = 'no_payment'; // GV Code Start/End ICW added for CREDIT CLASS
		}
		unset($GLOBALS['order']);

		$payment_modules = new payment($_SESSION['payment']);

		$GLOBALS['order'] = new order();

		$t_error = '';
		$t_check_abandonment_download = false;
		$t_check_abandonment_service = false;

		foreach($GLOBALS['order']->products as $t_product)
		{
			if($t_product['product_type'] == '2')
			{
				$t_check_abandonment_download = true;
			}
			if($t_product['product_type'] == '3')
			{
				$t_check_abandonment_service = true;
			}
		}

		if($t_check_abandonment_download)
		{
			if(isset($_SESSION['abandonment_download']) == false
				&& isset($_POST['abandonment_download']) == false
				&& gm_get_conf('CHECK_ABANDONMENT_OF_WITHDRAWL_DOWNLOAD') == 1)
			{
				$t_error .= ERROR_ABANDONMENT_DOWNLOAD_NOT_ACCEPTED;
			}
			elseif(isset($_POST['abandonment_download']))
			{
				$_SESSION['abandonment_download'] = 'true';
			}
			else
			{
				$_SESSION['abandonment_download'] = 'false';
			}
		}

		if($t_check_abandonment_service)
		{
			if(isset($_SESSION['abandonment_service']) == false
				&& isset($_POST['abandonment_service']) == false
				&& gm_get_conf('CHECK_ABANDONMENT_OF_WITHDRAWL_SERVICE') == 1)
			{
				$t_error .= ERROR_ABANDONMENT_SERVICE_NOT_ACCEPTED;
			}
			elseif(isset($_POST['abandonment_service']))
			{
				$_SESSION['abandonment_service'] = 'true';
			}
			else
			{
				$_SESSION['abandonment_service'] = 'false';
			}
		}

		if($t_error != '')
		{
			$_SESSION['gm_error_message'] .= urlencode($t_error);
			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
			return true;
		}

		// GV Code line changed

		$t_no_payment_selected = false;

		if(trim($_SESSION['payment']) == '')
		{
			// NO PAYMENT SELECTED
			$t_no_payment_selected = true;
		}
		else if((is_array($payment_modules->modules)
					&& sizeof($payment_modules->selection()) > 1
					&& is_object($GLOBALS[$_SESSION['payment']]) === false
					&& isset($_SESSION['credit_covers']) === false)
				|| (is_object($GLOBALS[$_SESSION['payment']])
					&& $GLOBALS[$_SESSION['payment']]->enabled == false))
		{
			// NO PAYMENT SELECTED
			$t_no_payment_selected = true;
		}

		if($t_no_payment_selected)
		{
			$_SESSION['gm_error_message'] = urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED);
			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
			return true;
		}

		if(is_array($payment_modules->modules) && strpos($_SESSION['payment'], 'saferpaygw') === false)
		{
			$payment_modules->pre_confirmation_check();
		}

		// saferpay
		if(is_array($payment_modules->modules) && strpos($_SESSION['payment'], 'saferpaygw') !== false)
		{
			if(MODULE_ORDER_TOTAL_INSTALLED)
			{
				$order_total_modules->process();
			}

			$payment_modules->pre_confirmation_check();
		}

		// START Heidelpay
		$_SESSION['gm_heidelpay'] = $GLOBALS['order']->info['total'];
		$_SESSION['gm_heidelpay_currency'] = $GLOBALS['order']->info['currency'];
		$_SESSION['gm_heidelpay_firstname'] = $GLOBALS['order']->billing['firstname'];
		$_SESSION['gm_heidelpay_lastname'] = $GLOBALS['order']->billing['lastname'];
		$_SESSION['gm_heidelpay_gender'] = $GLOBALS['order']->customer['gender'];
		$_SESSION['gm_heidelpay_street_address'] = $GLOBALS['order']->billing['street_address'];
		$_SESSION['gm_heidelpay_postcode'] = $GLOBALS['order']->billing['postcode'];
		$_SESSION['gm_heidelpay_city'] = $GLOBALS['order']->billing['city'];
		$_SESSION['gm_heidelpay_state'] = $GLOBALS['order']->billing['state'];
		$_SESSION['gm_heidelpay_city'] = $GLOBALS['order']->billing['city'];
		$_SESSION['gm_heidelpay_state'] = $GLOBALS['order']->billing['state'];
		$_SESSION['gm_heidelpay_iso_code_2'] = $GLOBALS['order']->billing['country']['iso_code_2'];
		$_SESSION['gm_heidelpay_email_address'] = $GLOBALS['order']->customer['email_address'];
		// END Heidelpay

		$coo_checkout_confirmation_view = MainFactory::create_object('CheckoutConfirmationContentView');
		$coo_checkout_confirmation_view->set_('coo_payment', $payment_modules);
		$coo_checkout_confirmation_view->set_('coo_order', $GLOBALS['order']);
		$coo_checkout_confirmation_view->set_('coo_order_total', $order_total_modules);
		$coo_checkout_confirmation_view->set_('coo_xtc_price', $GLOBALS['xtPrice']);
		$coo_checkout_confirmation_view->set_('language', $_SESSION['language']);
		$coo_checkout_confirmation_view->set_('languages_id', (int)$_SESSION['languages_id']);
		$coo_checkout_confirmation_view->set_('payment', $_SESSION['payment']);
		$coo_checkout_confirmation_view->set_('shipping_address_book_id', (int)$_SESSION['sendto']);

		$t_credit_covers = false;
		if($_SESSION['credit_covers'] == '1')
		{
			$t_credit_covers = true;
		}
		$coo_checkout_confirmation_view->set_('credit_covers', $t_credit_covers);

		$customers_ip = $_SERVER["REMOTE_ADDR"];
		if($_SERVER["HTTP_X_FORWARDED_FOR"])
		{
			$customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		$coo_checkout_confirmation_view->set_('customers_ip', $customers_ip);

		$t_customers_status_add_tax_ot = false;
		if($_SESSION['customers_status']['customers_status_add_tax_ot'] == '1')
		{
			$t_customers_status_add_tax_ot = true;
		}
		$coo_checkout_confirmation_view->set_('customers_status_add_tax_ot', $t_customers_status_add_tax_ot);

		$t_customers_status_show_price_tax = false;
		if($_SESSION['customers_status']['customers_status_show_price_tax'] == '1')
		{
			$t_customers_status_show_price_tax = true;
		}
		$coo_checkout_confirmation_view->set_('customers_status_show_price_tax', $t_customers_status_show_price_tax);

		$t_error_message = '';
		if(isset($this->v_data_array['GET']['payment_error']))
		{
			$t_error_message = htmlentities_wrapper($this->v_data_array['GET']['ret_errormsg']);
		}
		$coo_checkout_confirmation_view->set_('error_message', $t_error_message);

		$this->v_output_buffer = $coo_checkout_confirmation_view->get_html();

		return true;
	}
}
