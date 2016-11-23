<?php
/* --------------------------------------------------------------
  cart_dropdown.php 2014-07-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

// ShoppingCartDropdown
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

// build dropdown with default template
$t_html = $coo_content_view->get_html();
$this->set_content_data('SHOPPING_CART_DROPDOWN', $t_html);

// build head with another template
$coo_content_view->set_content_template('boxes/box_cart_head.html');
$t_html = $coo_content_view->get_html();
$this->set_content_data('SHOPPING_CART_HEAD', $t_html);