<?php
/* --------------------------------------------------------------
   CheckoutPaymentContentControl.inc.php 2016-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(checkout_payment.php,v 1.110 2003/03/14); www.oscommerce.com
   (c) 2003	 nextcommerce (checkout_payment.php,v 1.20 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_payment.php 1325 2005-10-30 10:23:32Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   agree_conditions_1.01        	Autor:	Thomas Plänkers (webmaster@oscommerce.at)

   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include classes
require_once(DIR_WS_CLASSES . 'payment.php');
require_once(DIR_WS_CLASSES . 'order_total.php');
MainFactory::load_class('CheckoutControl');

class CheckoutPaymentContentControl extends CheckoutControl
{
	protected $coo_payment;

	public function __construct()
	{
		parent::__construct();
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['coo_payment'] = array('type'			=> 'object',
															 'object_type'	=> 'payment');
	}

	public function proceed()
	{
		unset($_SESSION['tmp_oID']);

		// moneybookers
		unset($_SESSION['transaction_id']);

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

		if(isset($_SESSION['credit_covers']))
		{
			unset($_SESSION['credit_covers']); //ICW ADDED FOR CREDIT CLASS SYSTEM
		}

		// if no billing destination address was selected, use the customers own address as default
		if(!isset($_SESSION['billto']))
		{
			$_SESSION['billto'] = $_SESSION['customer_default_address_id'];
		}
		else
		{
			// verify the selected billing address
			$check_address_query = xtc_db_query("SELECT COUNT(*) AS total
													FROM " . TABLE_ADDRESS_BOOK . "
													WHERE
														customers_id = '" . (int)$_SESSION['customer_id'] . "' AND
														address_book_id = '" . (int)$_SESSION['billto'] . "'");
			$check_address = xtc_db_fetch_array($check_address_query);

			if($check_address['total'] != '1')
			{
				$_SESSION['billto'] = $_SESSION['customer_default_address_id'];

				if(isset($_SESSION['payment']))
				{
					unset($_SESSION['payment']);
				}
			}
		}

		if(!isset($_SESSION['sendto']) || $_SESSION['sendto'] == '')
		{
			$_SESSION['sendto'] = $_SESSION['billto'];
		}

		$GLOBALS['order'] = new order();
		$order = $GLOBALS['order'];
		$order_total_modules = new order_total(); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM

		$GLOBALS['total_weight'] = $_SESSION['cart']->show_weight();
		$GLOBALS['total_count'] = $_SESSION['cart']->count_contents_non_virtual(); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM

		if($order->billing['country']['iso_code_2'] != '')
		{
			$_SESSION['delivery_zone'] = $order->billing['country']['iso_code_2'];
		}

		// mediafinanz
		if(gm_get_conf('MODULE_CENTER_MEDIAFINANZ_INSTALLED') == true)
		{
			include_once(DIR_FS_CATALOG . 'includes/modules/mediafinanz/include_checkout_payment.php');
		}

		// load all enabled payment modules
		$this->coo_payment = new payment();

		// redirect if Coupon matches ammount
		$order_total_modules->process();

		if(gm_get_conf('GM_CHECK_WITHDRAWAL') == 1)
		{
			unset($_SESSION['withdrawal']);
		}

		if(gm_get_conf('GM_CHECK_CONDITIONS') == 1)
		{
			unset($_SESSION['conditions']);
		}

		$t_error_message = '';

		// check if country of selected shipping address is not allowed
		if($this->check_country_by_address_book_id($_SESSION['billto']) == false)
		{
			$t_error_message = ERROR_INVALID_PAYMENT_COUNTRY;
		}

		if($order->info['total'] > 0 && isset($this->v_data_array['GET']['payment_error']) && is_object($GLOBALS[$this->v_data_array['GET']['payment_error']]) && ($error = $GLOBALS[$this->v_data_array['GET']['payment_error']]->get_error()))
		{
			$t_error_message = htmlspecialchars_wrapper($error['error']);
		}

		if(isset($_SESSION['gm_error_message']) && xtc_not_null($_SESSION['gm_error_message']))
		{
			$t_error_message = htmlspecialchars_wrapper(urldecode($_SESSION['gm_error_message']));
			unset($_SESSION['gm_error_message']);
		}

		# phantom call for creating checkout cache-file
		MainFactory::create_object('GMJanolaw');

		$coo_checkout_payment_view = MainFactory::create_object('CheckoutPaymentContentView');

		$coo_checkout_payment_view->set_('address_book_id', $_SESSION['billto']);
		$coo_checkout_payment_view->set_('customer_id', $_SESSION['customer_id']);
		$coo_checkout_payment_view->set_('customers_status_id', $_SESSION['customers_status']['customers_status_id']);
		$coo_checkout_payment_view->set_('language', $_SESSION['language']);
		$coo_checkout_payment_view->set_('languages_id', $_SESSION['languages_id']);
		$coo_checkout_payment_view->set_('coo_payment', $this->coo_payment);
		$coo_checkout_payment_view->set_('coo_order', $order);
		$coo_checkout_payment_view->set_('coo_order_total', $order_total_modules);
		$coo_checkout_payment_view->set_('error_message', $t_error_message);
		$coo_checkout_payment_view->set_('cart_product_array', $_SESSION['cart']->get_products());

		if(isset($_SESSION['payment']))
		{
			$coo_checkout_payment_view->set_('selected_payment_method', $_SESSION['payment']);
		}

		$t_comments = '';
		if(isset($_SESSION['comments']))
		{
			$t_comments = $_SESSION['comments'];
		}
		$coo_checkout_payment_view->set_('comments', $t_comments);

		$t_style_edit_active = false;
		if($_SESSION['style_edit_mode'] == 'edit')
		{
			$t_style_edit_active = true;
		}
		$coo_checkout_payment_view->set_('style_edit_active', $t_style_edit_active);

		$this->v_output_buffer = $coo_checkout_payment_view->get_html();
		unset($_SESSION['abandonment_download']);
		unset($_SESSION['abandonment_service']);

		return true;
	}
}