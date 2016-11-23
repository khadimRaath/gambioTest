<?php
/* --------------------------------------------------------------
   account.php 2014-02-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project (earlier name of osCommerce)
   (c) 2002-2003 osCommerce (account.php,v 1.59 2003/05/19); www.oscommerce.com
   (c) 2003      nextcommerce (account.php,v 1.12 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: account.php 1124 2005-07-28 08:50:04Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once('includes/application_top.php');

if(isset($_SESSION['customer_id']) === false)
{
	xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_ACCOUNT, xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));

$coo_account_view = MainFactory::create_object('AccountContentView');
$coo_account_view->set_('customer_id', $_SESSION['customer_id']);
$coo_account_view->set_('languages_id', $_SESSION['languages_id']);
$coo_account_view->set_('tracking_data_array', $_SESSION['tracking']);
$coo_account_view->set_('coo_product', $GLOBALS['product']);
$coo_account_view->set_('coo_message_stack', $GLOBALS['messageStack']);

if(isset($_POST['action']) && $_POST['action'] == 'gm_delete_account')
{
	$coo_account_view->set_('post_action', $_POST['action']);
	$coo_account_view->set_('post_content', $_POST['gm_content']);
}
if($_SESSION['customers_status']['customers_status_id'] == DEFAULT_CUSTOMERS_STATUS_ID_GUEST)
{
	$coo_account_view->set_('is_guest', true);
}

$t_main_content = $coo_account_view->get_html();

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
