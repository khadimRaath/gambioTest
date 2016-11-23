<?php
/****************************************************** 
 * Masterpayment Modul for Gambio GX2 
 * Version 3.5
 * Copyright (c) 2010-2012 by K-30 | Florian Ressel 
 *
 * support@k-30.de | www.k-30.de
 * ----------------------------------------------------
 *
 * $Id: checkout_masterpayment.php 28.11.2012 22:57 $
 *	
 *	The Modul based on:
 *  XT-Commerce - community made shopping
 *  http://www.xt-commerce.com
 *
 *  Copyright (c) 2003 XT-Commerce
 *
 *	Released under the GNU General Public License
 *
 ******************************************************/

require_once('includes/application_top.php');

// create smarty elements
$smarty = new Smarty;

$action = $_GET['action'];

if($action == '' || !isset($action) || $action != 'response')
{	
	// if the customer is not logged on, redirect them to the login page
	if(!isset($_SESSION['customer_id']))
	{
		if(ACCOUNT_OPTIONS == 'guest')
		{
			xtc_redirect(xtc_href_link('shop.php', 'do=CreateGuest&checkout_started=1', 'SSL'));
		} 
		else
		{
			xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
		}
	}
	
	// if there is nothing in the customers cart, redirect them to the shopping cart page
	if($_SESSION['cart']->count_contents() < 1)
	{
		xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
	}
	
	// if no shipping method has been selected, redirect the customer to the shipping method selection page
	if(!isset($_SESSION['shipping']))
	{
		xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
	}
	
	// avoid hack attempts during the checkout procedure by checking the internal cartID
	if(isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID']))
	{
		if($_SESSION['cart']->cartID != $_SESSION['cartID'])
		{
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
		}
	}
	
	if((!isset($_SESSION['cart_Masterpayment_ID']) && empty($_SESSION['cart_Masterpayment_ID'])) || (substr($_SESSION['payment'], 0, strpos($_SESSION['payment'], '_')) != 'masterpayment'))
	{	
		xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'NONSSL'));		
	}
}


$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_PAYMENT, xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_PAYMENT, xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));

$smarty->assign('tpl_path', CURRENT_TEMPLATE);

if($action == 'response')
{
	include_once('includes/external/masterpayment/MasterpaymentResponse.class.php');  
	$MasterpaymentResponse = new MasterpaymentResponse($_GET);

	$languageTextManager = MainFactory::create_object('LanguageTextManager', array('masterpayment'), true);
	$_masterpaymentCallbackMessages = $languageTextManager->get_section_array();

	$languageTextManager->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/payment/masterpayment_' . basename($_GET['payment_method']) . '.php');
	if(defined('MODULE_PAYMENT_MASTERPAYMENT_'.strtoupper($_GET['payment_method']).'_CHECKOUT_TITLE'))
	{
		$smarty->assign('masterpayment_method_title', constant('MODULE_PAYMENT_MASTERPAYMENT_'.strtoupper($_GET['payment_method']).'_CHECKOUT_TITLE'));
	}

	$smarty->assign('masterpayment_message', $_masterpaymentCallbackMessages[strtoupper($_GET['response'])]);		
	
	$t_main_content = $smarty->fetch(CURRENT_TEMPLATE . '/module/masterpayment_response.html');
} 
elseif($action == 'request')
{	
	// load selected payment module
	require_once(DIR_WS_CLASSES . 'payment.php');
	$payment_modules = new payment($_SESSION['payment']);
	
	// load the selected shipping module
	require_once(DIR_WS_CLASSES . 'shipping.php');
	$shipping_modules = new shipping($_SESSION['shipping']);

	if(!class_exists('order'))
	{
		require_once(DIR_WS_CLASSES . 'order.php');
	}
	
	$order = new order();

  	if(!class_exists('order_total'))
	{  
  		require_once(DIR_WS_CLASSES . 'order_total.php');
 	}  
  	$order_total_modules = new order_total;
  	$order_total_modules->process();
	
	include_once('includes/external/masterpayment/MasterpaymentRequest.class.php');  
	$masterpayment = new MasterpaymentRequest();

	if($masterpayment->init())
	{
		$smarty->assign('masterpayment_url', $masterpayment->getMasterpaymentURL());
		$smarty->assign('request_parameters', $masterpayment->generateRequest());
	} 
	else
	{
		$smarty->assign('masterpayment_error', 1);
	}

	$smarty->assign('masterpayment_button_text', MODULE_PAYMENT_MASTERPAYMENT_FRAME_BUTTON_TEXT);
	$smarty->assign('masterpayment_error_message', MODULE_PAYMENT_MASTERPAYMENT_ERROR_MESSAGE);
	$smarty->assign('masterpayment_error_button_link', $masterpayment->getShopURL() . 'checkout_payment.php?' . session_name() . '=' . session_id());
	$smarty->assign('masterpayment_error_button_text', MODULE_PAYMENT_MASTERPAYMENT_FRAME_ERROR_BUTTON_TEXT);
	$smarty->display(CURRENT_TEMPLATE . '/module/masterpayment_request.html');
	
	xtc_db_close();
	exit;
} 
else
{
	include_once('includes/external/masterpayment/MasterpaymentActions.class.php');  
	$MasterpaymentActions = new MasterpaymentActions();
	
	$smarty->assign('language', $_SESSION['language']);
	$smarty->assign('masterpayment_request_url', $MasterpaymentActions->getRequestURL());
	
	$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/payment/masterpayment_config.php');
	$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/payment/' . $_SESSION['payment'] . '.php');
	
	if(defined('MODULE_PAYMENT_MASTERPAYMENT_' . strtoupper(str_replace('masterpayment_', '', $_SESSION['payment'])) . '_CHECKOUT_TITLE'))
	{
		$smarty->assign('masterpayment_button_text', MODULE_PAYMENT_MASTERPAYMENT_FRAME_BUTTON_TEXT); 
		$smarty->assign('masterpayment_payment_title', constant('MODULE_PAYMENT_MASTERPAYMENT_' . strtoupper(str_replace('masterpayment_', '', $_SESSION['payment'])) . '_CHECKOUT_TITLE'));
	}
	
	if(function_exists('gm_get_conf'))
	{
		$smarty->assign('LIGHTBOX', gm_get_conf('GM_LIGHTBOX_CHECKOUT'));
		$smarty->assign('LIGHTBOX_CLOSE', xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL'));
	}
		
	$t_main_content = $smarty->fetch(CURRENT_TEMPLATE . '/module/checkout_masterpayment.html');
}

$coo_layout_control = MainFactory::create_object('LayoutContentControl');
$coo_layout_control->set_data('GET', $_GET);
$coo_layout_control->set_data('POST', $_POST);
$t_category_id = 0;

if(isset($GLOBALS['cID']))
{
	$t_category_id = $GLOBALS['cID'];
}

$coo_layout_control->set_('category_id', $t_category_id);
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
