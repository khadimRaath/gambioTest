<?php
/* --------------------------------------------------------------
  address_book_process.php 2014-02-21 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(address_book_process.php,v 1.77 2003/05/27); www.oscommerce.com
  (c) 2003	 nextcommerce (address_book_process.php,v 1.13 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: address_book_process.php 1218 2005-09-16 11:38:37Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_1_ADDRESS_BOOK_PROCESS, xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_2_ADDRESS_BOOK_PROCESS, xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));

if(isset($_GET['edit']) && is_numeric($_GET['edit']))
{
	$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_MODIFY_ENTRY_ADDRESS_BOOK_PROCESS, xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'edit=' . htmlentities_wrapper($_GET['edit']), 'SSL'));
}
elseif(isset($_GET['delete']) && is_numeric($_GET['delete']))
{
	$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_DELETE_ENTRY_ADDRESS_BOOK_PROCESS, xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'delete=' . htmlentities_wrapper($_GET['delete']), 'SSL'));
}
else
{
	$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_ADD_ENTRY_ADDRESS_BOOK_PROCESS, xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, '', 'SSL'));
}

$coo_address_book_process_control = MainFactory::create_object('AddressBookProcessContentControl');
$coo_address_book_process_control->set_data('GET', $_GET);
$coo_address_book_process_control->set_data('POST', $_POST);
$coo_address_book_process_control->set_customers_id($_SESSION['customer_id']);
$coo_address_book_process_control->set_customer_default_address_id($_SESSION['customer_default_address_id']);
$coo_address_book_process_control->set_customer_country_id($_SESSION['customer_country_id']);
$coo_address_book_process_control->proceed();

$t_redirect_url = $coo_address_book_process_control->get_redirect_url();
if(!empty($t_redirect_url))
{
	xtc_redirect($t_redirect_url);
}
else
{
	$t_main_content = $coo_address_book_process_control->get_response();
}

$coo_layout_control = MainFactory::create_object('LayoutContentControl');
$coo_layout_control->set_data('GET', $_GET);
$coo_layout_control->set_data('POST', $_POST);
$coo_layout_control->set_('coo_breadcrumb', $GLOBALS['breadcrumb']);
$coo_layout_control->set_('coo_product', $GLOBALS['product']);
$coo_layout_control->set_('coo_xtc_price', $GLOBALS['xtPrice']);
$coo_layout_control->set_('c_path', $GLOBALS['cPath']);
$coo_layout_control->set_('main_content', $t_main_content);
$coo_layout_control->set_('request_type', $GLOBALS['request_type']);
$coo_layout_control->proceed();

$t_redirect_url = $coo_layout_control->get_redirect_url();
if(empty($t_redirect_url) === false)
{
	xtc_redirect($t_redirect_url);
}
else
{
	echo $coo_layout_control->get_response();
}