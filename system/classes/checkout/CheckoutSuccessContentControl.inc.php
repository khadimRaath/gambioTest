<?php
/* --------------------------------------------------------------
   CheckoutSuccessControl.inc.php 2014-07-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(checkout_success.php,v 1.48 2003/02/17); www.oscommerce.com 
   (c) 2003	 nextcommerce (checkout_success.php,v 1.14 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_success.php 896 2005-04-27 19:22:59Z mz $)

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
   ---------------------------------------------------------------------------------------*/

class CheckoutSuccessContentControl extends DataProcessing
{
	public function proceed()
	{
		if (isset($this->v_data_array['GET']['action']) && ($this->v_data_array['GET']['action'] == 'update'))
		{
			if ($_SESSION['account_type'] != 1) {
				$this->set_redirect_url(xtc_href_link(FILENAME_DEFAULT));
				return;
			}
			else
			{
				$this->set_redirect_url(xtc_href_link(FILENAME_LOGOFF));
				return;
			}
		}
		
		$coo_checkout_success_content_view = MainFactory::create_object('CheckoutSuccessContentView');
		
		$orders_query = xtc_db_query("select orders_id, orders_status, payment_method from ".TABLE_ORDERS." where customers_id = '".$_SESSION['customer_id']."' order by orders_id desc limit 1");
		$orders = xtc_db_fetch_array($orders_query);
		$last_order = $orders['orders_id'];
		$order_status = $orders['orders_status'];

		$coo_checkout_success_extender_component = MainFactory::create_object('CheckoutSuccessExtenderComponent');
		$coo_checkout_success_extender_component->set_data('orders_id', $last_order);
		$coo_checkout_success_extender_component->proceed();
		$t_dispatcher_result_array = $coo_checkout_success_extender_component->get_response();

		if(is_array($t_dispatcher_result_array))
		{
			foreach($t_dispatcher_result_array as $t_key => $t_value)
			{
				$coo_checkout_success_content_view->set_content_data($t_key, $t_value);
			}
		}
		
		$coo_checkout_success_content_view->set_content_data('extender_html_array', $coo_checkout_success_extender_component->get_html_output_array());

		// BOF GM_MOD HEIDELPAY E-MAIL
		if(strpos($orders['payment_method'], 'hp') === 0)
		{
			require_once(DIR_WS_CLASSES.'order.php');
			$order = new order($last_order);
			$insert_id = $last_order;
			if (empty($_SESSION['checkout_no_order_mail']))
			{
				$coo_send_order_process = MainFactory::create_object('SendOrderProcess');
				$coo_send_order_process->set_('order_id', $insert_id);
				$coo_send_order_process->proceed();
			}
		}
		// EOF GM_MOD HEIDELPAY E-MAIL
		
		$coo_checkout_success_content_view->set_('language', $_SESSION['language']);
		$coo_checkout_success_content_view->set_('order_id', $orders['orders_id']);
		$coo_checkout_success_content_view->set_('customer_id', $_SESSION['customer_id']);
		$coo_checkout_success_content_view->set_('nc_checkout_success_info', $_SESSION['nc_checkout_success_info']);
		$this->v_output_buffer = $coo_checkout_success_content_view->get_html();
		
		if($_SESSION['nc_checkout_success_info']) {
			unset ($_SESSION['nc_paypal_amount']);
			unset ($_SESSION['nc_checkout_success_info']);
		}
		
		return true;
	}
}
