<?php
/* --------------------------------------------------------------
   CartDropdownAjaxHandler.inc.php 2014-07-17 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CartDropdownAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		switch($this->v_data_array['GET']['part'])
		{
			case 'header':
				$coo_content_view = MainFactory::create_object('ShoppingCartDropdownBoxContentView');
				$coo_content_view->set_('coo_cart', $_SESSION['cart']);
				$coo_content_view->set_('language_id', $_SESSION['languages_id']);
				$coo_content_view->set_('language_code', $_SESSION['language_code']);
				$coo_content_view->set_('customers_status_ot_discount_flag', $_SESSION['customers_status']['customers_status_ot_discount_flag']);
				$coo_content_view->set_('customers_status_ot_discount', $_SESSION['customers_status']['customers_status_ot_discount']);
				$coo_content_view->set_('customers_status_show_price_tax', $_SESSION['customers_status']['customers_status_show_price_tax']);
				$coo_content_view->set_('customers_status_add_tax_ot', $_SESSION['customers_status']['customers_status_add_tax_ot']);
				$coo_content_view->set_('customers_status_show_price', $_SESSION['customers_status']['customers_status_show_price']);
				$coo_content_view->set_('customers_status_payment_unallowed', $_SESSION['customers_status']['customers_status_payment_unallowed']);
				$coo_content_view->set_content_template('boxes/box_cart_head.html');
				$this->v_output_buffer = $coo_content_view->get_html();
				break;

			case 'dropdown':
				$coo_content_view = MainFactory::create_object('ShoppingCartDropdownBoxContentView');
				$coo_content_view->set_('coo_cart', $_SESSION['cart']);
				$coo_content_view->set_('language_id', $_SESSION['languages_id']);
				$coo_content_view->set_('language_code', $_SESSION['language_code']);
				$coo_content_view->set_('customers_status_ot_discount_flag', $_SESSION['customers_status']['customers_status_ot_discount_flag']);
				$coo_content_view->set_('customers_status_ot_discount', $_SESSION['customers_status']['customers_status_ot_discount']);
				$coo_content_view->set_('customers_status_show_price_tax', $_SESSION['customers_status']['customers_status_show_price_tax']);
				$coo_content_view->set_('customers_status_add_tax_ot', $_SESSION['customers_status']['customers_status_add_tax_ot']);
				$coo_content_view->set_('customers_status_show_price', $_SESSION['customers_status']['customers_status_show_price']);
				$coo_content_view->set_('customers_status_payment_unallowed', $_SESSION['customers_status']['customers_status_payment_unallowed']);
				$this->v_output_buffer = $coo_content_view->get_html();
				break;

			case 'fixed':
				$coo_content_view = MainFactory::create_object('ShoppingCartDropdownBoxContentView');
				$coo_content_view->set_('coo_cart', $_SESSION['cart']);
				$coo_content_view->set_('language_id', $_SESSION['languages_id']);
				$coo_content_view->set_('language_code', $_SESSION['language_code']);
				$coo_content_view->set_('customers_status_ot_discount_flag', $_SESSION['customers_status']['customers_status_ot_discount_flag']);
				$coo_content_view->set_('customers_status_ot_discount', $_SESSION['customers_status']['customers_status_ot_discount']);
				$coo_content_view->set_('customers_status_show_price_tax', $_SESSION['customers_status']['customers_status_show_price_tax']);
				$coo_content_view->set_('customers_status_add_tax_ot', $_SESSION['customers_status']['customers_status_add_tax_ot']);
				$coo_content_view->set_('customers_status_show_price', $_SESSION['customers_status']['customers_status_show_price']);
				$coo_content_view->set_('customers_status_payment_unallowed', $_SESSION['customers_status']['customers_status_payment_unallowed']);
				$coo_content_view->set_content_template('boxes/box_cart_dropdown_fixed.html');
				$this->v_output_buffer = $coo_content_view->get_html();
				break;

		}

		return true;
	}
}