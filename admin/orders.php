<?php
/* --------------------------------------------------------------
   orders.php 2016-09-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE.
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(orders.php,v 1.109 2003/05/28); www.oscommerce.com
   (c) 2003	 nextcommerce (orders.php,v 1.19 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: orders.php 1189 2005-08-28 15:27:00Z hhgag $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   OSC German Banktransfer v0.85a       	Autor:	Dominik Guder <osc@guder.org>
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   credit card encryption functions for the catalog module
   BMC 2003 for the CC CVV Module

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require ('includes/application_top.php');

ob_start();

if(!isset($jsEngineLanguage))
{
	$jsEngineLanguage = array();
}

$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
$languageTextManager->init_from_lang_file('parcel_services');
$languageTextManager->init_from_lang_file('configuration');
$jsEngineLanguage['orders'] = $languageTextManager->get_section_array('orders');
$jsEngineLanguage['parcel_services'] = $languageTextManager->get_section_array('parcel_services');
$jsEngineLanguage['shipcloud'] = $languageTextManager->get_section_array('shipcloud');
$jsEngineLanguage['iloxx'] = $languageTextManager->get_section_array('iloxx');

if (count($languageTextManager->get_section_array('shipcloud')) > 0)
{
	$jsEngineLanguage['shipcloud'] = $languageTextManager->get_section_array('shipcloud');
}

$t_page_token = $_SESSION['coo_page_token']->generate_token();

$adminOrderOverviewExtender = MainFactory::create_object('AdminOrderOverviewExtenderComponent');
$adminOrderOverviewExtender->set_data('GET', $_GET);
$adminOrderOverviewExtender->set_data('POST', $_POST);
$adminOrderOverviewExtender->proceed();

require_once (DIR_FS_INC.'xtc_php_mail.inc.php');
require_once (DIR_FS_INC.'xtc_add_tax.inc.php');
require_once (DIR_FS_INC.'changedataout.inc.php');
require_once (DIR_FS_INC.'xtc_validate_vatid_status.inc.php');
require_once (DIR_FS_INC.'xtc_get_attributes_model.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_prepare_number.inc.php');

ob_start();

if(!isset($_SESSION['messages'][basename(__FILE__)]))
{
	$_SESSION['messages'][basename(__FILE__)] = array();
}

$orderStatusColorsQuery = '
	SELECT
		`orders_status_id`, `color`
	FROM
		`orders_status`
	GROUP BY
		`orders_status_id`
';
$orderStatusColorsResult = xtc_db_query($orderStatusColorsQuery);

// Compatibility Function: Get the correct badge class depending the order status.
function getBadgeClass($orderStatus) {
	return 'badge badge-' . $orderStatus;
}

$withdrawalsDataArray = array();
$ordersStatusDataArray = array();
if(array_key_exists('oID', $_GET) && array_key_exists('action', $_GET) && $_GET['action'] === 'edit')
{
	$withdrawalQuery  = 'SELECT withdrawal_id, date_created FROM withdrawals WHERE order_id = \''
	                    . xtc_db_input($_GET['oID']) . '\' ORDER BY withdrawal_id';
	$withdrawalResult = xtc_db_query($withdrawalQuery);
	if(xtc_db_num_rows($withdrawalResult))
	{
		while($withdrawalRow = xtc_db_fetch_array($withdrawalResult))
		{
			$withdrawalsDataArray[] = $withdrawalRow;
		}
	}

	$orderStatusQuery = 'SELECT
							orders_status_id,
							date_added,
							customer_notified,
							comments
						FROM
							orders_status_history
						WHERE
							orders_id = "' . xtc_db_input($_GET['oID']) . '"
						ORDER BY
							date_added'
	;
	$orderStatusResult = xtc_db_query($orderStatusQuery);
	if(xtc_db_num_rows($orderStatusResult))
	{
		while($orderStatusRow = xtc_db_fetch_array($orderStatusResult))
		{
			$ordersStatusDataArray[] = $orderStatusRow;
		}
	}
}

/* magnalister v1.0.1 */
if (function_exists('magnaExecute')) magnaExecute('magnaSubmitOrderStatus', array(), array('order_details.php'));
/* END magnalister */

require_once (DIR_FS_CATALOG.'callback/sofort/ressources/scripts/sofortOrders.php');

// BEGIN Hermes
require_once DIR_FS_CATALOG .'includes/classes/hermes.php';
$hermes = new Hermes();
// END Hermes

// save number of orders per page
if(isset($_POST['number_of_orders_per_page']) && is_numeric($_POST['number_of_orders_per_page']) && $_POST['number_of_orders_per_page'] > 0)
{
	gm_set_conf('NUMBER_OF_ORDERS_PER_PAGE', $_POST['number_of_orders_per_page']);
}

// initiate template engine for mail
$smarty = new Smarty;
// bof gm
$gm_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
if($gm_logo_mail->logo_use == '1') {
	$smarty->assign('gm_logo_mail', $gm_logo_mail->get_logo());
}
require (DIR_WS_CLASSES.'currencies.php');
$currencies = new currencies();

$messageStack->add_additional_class('breakpoint-large');

if ((($_GET['action'] == 'edit') || ($_GET['action'] == 'update_order')) && ($_GET['oID'])) {
	$oID = xtc_db_prepare_input($_GET['oID']);

	$orders_query = xtc_db_query("select orders_id from ".TABLE_ORDERS." where orders_id like '".xtc_db_input($oID)."'");
	$order_exists = true;
	if (!xtc_db_num_rows($orders_query)) {
		$orders_query = xtc_db_query("select orders_id from ".TABLE_ORDERS." where gm_orders_code = '".xtc_db_input($oID)."' LIMIT 1");
		if (!xtc_db_num_rows($orders_query)) {
		$order_exists = false;
		$messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'warning');
	}
		else
		{
			$t_result_array = xtc_db_fetch_array($orders_query);
			xtc_redirect(xtc_href_link(FILENAME_ORDERS, 'action=edit&oID=' . (int)$t_result_array['orders_id']));
}
	}

	// BOF eKomi
	if(gm_get_conf('EKOMI_STATUS') == '1')
	{
		$coo_ekomi_manager = MainFactory::create_object('EkomiManager', array(gm_get_conf('EKOMI_API_ID'), gm_get_conf('EKOMI_API_PASSWORD')));

		if(isset($_GET['ekomi']) && $_GET['ekomi'] == 'send_mail')
		{
			$t_success = $coo_ekomi_manager->send_mails($_GET['oID'], true);
			if($t_success)
			{
				$messageStack->add(EKOMI_SEND_MAIL_SUCCESS, 'success');
			}
			elseif($coo_ekomi_manager->mail_already_sent($_GET['oID']))
			{
				$messageStack->add(EKOMI_ALREADY_SEND_MAIL_ERROR, 'error');
			}
			else
			{
				$messageStack->add(EKOMI_SEND_MAIL_ERROR, 'error');
			}
		}
	}
	// EOF eKomi
}

require_once DIR_WS_CLASSES . 'order.php';
if(($_GET['action'] === 'edit' || $_GET['action'] === 'update_order') && $order_exists) 
{
	$order = new order($oID);
	$customerId = $order->customer['ID'];
	
	if(empty($order->customer['ID']))
	{
		$customerId = $order->customer['csID'];
	}

	/**
	 * Fetch data of first order
	 */
	$query          = xtc_db_query('SELECT `date_purchased` 
									FROM `orders` 
									WHERE 
										`customers_id` = "' . xtc_db_input($customerId) . '" OR 
										`customers_cid` = "' . xtc_db_input($customerId) . '" 
									ORDER BY `date_purchased` ASC 
									LIMIT 1');
	$result         = xtc_db_fetch_array($query);
	$firstOrderDate = '-';
	if(count($result) > 0)
	{
		$firstOrderDate = date('d.m.Y', strtotime($result['date_purchased']));
	}

	/**
	 * Fetch data of last order
	 */
	$query         = xtc_db_query('SELECT `date_purchased` 
									FROM `orders` 
									WHERE 
										`customers_id` = "' . xtc_db_input($customerId) . '" OR 
										`customers_cid` = "' . xtc_db_input($customerId) . '" 
									ORDER BY `date_purchased` DESC 
									LIMIT 1');
	$result        = xtc_db_fetch_array($query);
	$lastOrderDate = '-';
	if(count($result) > 0)
	{
		$lastOrderDate = date('d.m.Y', strtotime($result['date_purchased']));
	}

	/**
	 * Fetch amount of orders
	 */
	$fieldName = empty($order->customer['ID']) ? 'customers_cid' : 'customers_id';
	
	$query          = xtc_db_query('SELECT COUNT(*) as `count` 
									FROM  `orders` 
									WHERE `' . $fieldName . '` = "' . xtc_db_input($customerId) . '"'); 
	$result         = xtc_db_fetch_array($query);
	$amountOfOrders = '0';
	if(count($result) > 0)
	{
		$amountOfOrders = $result['count'];
	}

	/**
	 * Set text of sum of this order
	 */
	$query         = xtc_db_query('SELECT `text`
									FROM `orders_total` 
									WHERE 
										`orders_id` = ' . (int)$oID . ' AND
										`class` = "ot_total"');
	$orderSumText = '';
	if(xtc_db_num_rows($query))
	{
		$result = xtc_db_fetch_array($query);
		$orderSumText = $result['text'];
	}
	
	/**
	 * Fetch total sum of customer orders
	 */
	$query         = xtc_db_query('SELECT `value` 
									FROM `orders_total` 
									LEFT JOIN `orders` USING (`orders_id`) 
									WHERE `sort_order` = "99" 
										AND `' . $fieldName . '` = "' . xtc_db_input($customerId) . '"');
	$sumOrderTotal = 0;
	while($result = xtc_db_fetch_array($query))
	{
		$sumOrderTotal += (double)$result['value'];
	}
	$sumOrderTotal = number_format($sumOrderTotal, 2);
}

  $lang_query = xtc_db_query("select languages_id from " . TABLE_LANGUAGES . " where directory = '" . $order->info['language'] . "'");
  $lang = xtc_db_fetch_array($lang_query);
  $lang=$lang['languages_id'];

if (!isset($lang)) $lang=$_SESSION['languages_id'];
$orders_statuses = array ();
$orders_status_array = array ();
$change_orders_status = array();
$orders_status_query = xtc_db_query("select orders_status_id, orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".$lang."'");
while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
	$orders_statuses[] = array ('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
	// this array is needed for the change status selectbox
	// set status "storno" only with the storno-button!
	if($orders_status['orders_status_id'] != gm_get_conf('GM_ORDER_STATUS_CANCEL_ID')) {
		$change_orders_status[] = array ('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
	}
	$orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
}

/**
 * Order Status Array
 *
 * Holds all order statuses
 * depending on the sessions laguage id
 */
$orderStatusArray = array ();

/**
 * Order Status Display Name
 *
 * Represents the display name for the front end
 */
$orderStatusValuesArray = array ();

/**
 * Change Order Status Display Names
 *
 * Holds the statuses as display names for the
 * select field / modal layer to change the current order status
 */
$changeOrderStatusValuesArray = array();

/**
 * Order Status Query
 *
 * Fetches all order status texts
 * in a specific language (depending on the sessions language id)
 */
$orderStatusQuery = xtc_db_query("
								SELECT
									orders_status_id, orders_status_name
								FROM ".
                                    TABLE_ORDERS_STATUS."
                                WHERE
                                    language_id = '". (int)$_SESSION['languages_id'] ."'"
);

while ($orderStatus = xtc_db_fetch_array($orderStatusQuery))
{
	$orderStatusArray[] = array (
		'id'   => $orderStatus['orders_status_id'],
		'text' => $orderStatus['orders_status_name']
	);

	// this array is needed for the change status selectbox
	// set status "storno" only with the storno-button!
	if($orderStatus['orders_status_id'] !== gm_get_conf('GM_ORDER_STATUS_CANCEL_ID'))
	{
		$changeOrderStatusValuesArray[] = array (
			'id' => $orderStatus['orders_status_id'],
			'text' => $orderStatus['orders_status_name']
		);
	}
	$orderStatusValuesArray[$orderStatus['orders_status_id']] = $orderStatus['orders_status_name'];
}

$coo_order_action_extender_component = MainFactory::create_object('AdminOrderActionExtenderComponent');
$coo_order_action_extender_component->set_data('GET', $_GET);
$coo_order_action_extender_component->set_data('POST', $_POST);

// (xycons.de - Additional Extenders) (START)
$coo_order_status_mail_extender_component = MainFactory::create_object('AdminOrderStatusMailExtenderComponent');
$coo_order_status_mail_extender_component->set_data('GET', $_GET);
$coo_order_status_mail_extender_component->set_data('POST', $_POST);
// (xycons.de - Additional Extenders) (END)

switch ($_GET['action']) {

	// bof gm
	case 'gm_multi_status':

			$order_updated = false;
			$gm_status = xtc_db_prepare_input($_POST['gm_status']);
			$gm_comments = xtc_db_prepare_input($_POST['gm_comments']);

			for($i = 0; $i < count($_POST['gm_multi_status']); $i++) {
				$oID = xtc_db_prepare_input($_POST['gm_multi_status'][$i]);

				$check_status_query = xtc_db_query("
													SELECT
														o.customers_name,
														o.customers_gender,
														o.customers_email_address,
														o.orders_status,
														o.language,
														o.date_purchased,
														l.languages_id
													FROM " .
														TABLE_ORDERS . " o
													LEFT JOIN languages AS l ON (o.language = l.directory)
													WHERE
														orders_id = '" . xtc_db_input($oID) . "'
													");

				$check_status = xtc_db_fetch_array($check_status_query);

				if ($check_status['orders_status'] != $gm_status || $comments != '') {

					if($gm_status == gm_get_conf('GM_ORDER_STATUS_CANCEL_ID')) {
						$gm_update = "gm_cancel_date = now(),";
					}

					if(is_numeric($gm_status)){
						xtc_db_query("
									UPDATE " .
										TABLE_ORDERS . "
									SET
										" . $gm_update . "
										orders_status = '" . xtc_db_input($gm_status)."',
										last_modified = now()
									WHERE
										orders_id = '" . xtc_db_input($oID) . "'
									");
					}
					else{
						$gm_status = $check_status['orders_status'];
					}

					// cancel order
					if(xtc_db_input($gm_status) == gm_get_conf('GM_ORDER_STATUS_CANCEL_ID')) {
						xtc_remove_order(xtc_db_input($oID), true, true);
					}

					$customer_notified = '0';
					if($_POST['gm_notify'] == 'on') {
						$notify_comments = '';
						if ($_POST['gm_notify_comments'] == 'on') {
							$notify_comments = $gm_comments;
						} else {
							$notify_comments = '';
						}

						// assign language to template for caching
						$smarty->assign('language', $_SESSION['language']);
						$smarty->caching = false;

						// set dirs manual
						$smarty->template_dir = DIR_FS_CATALOG.'templates';
						$smarty->compile_dir = DIR_FS_CATALOG.'templates_c';
						$smarty->config_dir = DIR_FS_CATALOG.'lang';

						$smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
						$smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');

						$smarty->assign('NAME', $check_status['customers_name']);
						$smarty->assign('GENDER', $check_status['customers_gender']);
						$smarty->assign('ORDER_NR', $oID);
						$smarty->assign('ORDER_LINK', xtc_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id='.$oID, 'SSL'));
						$smarty->assign('ORDER_DATE', xtc_date_long($check_status['date_purchased']));
						$smarty->assign('ORDER_STATUS', $orders_status_array[$gm_status]);

						if(defined('EMAIL_SIGNATURE')) {
							$smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
							$smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
						}

						// START Parcel Tracking Code
						/** @var ParcelTrackingCode $coo_parcel_tracking_code_item */
						$coo_parcel_tracking_code_item = MainFactory::create_object('ParcelTrackingCode');
						/** @var ParcelTrackingCodeReader $coo_parcel_tracking_code_reader */
						$coo_parcel_tracking_code_reader = MainFactory::create_object('ParcelTrackingCodeReader');
						$t_parcel_tracking_codes_array = $coo_parcel_tracking_code_reader->getTackingCodeItemsByOrderId($coo_parcel_tracking_code_item,
																												  $oID);
						$smarty->assign('PARCEL_TRACKING_CODES_ARRAY', $t_parcel_tracking_codes_array);
						$send_parcel_tracking_codes = '';
						if($_POST['send_parcel_tracking_codes'] == 'on') {
							$send_parcel_tracking_codes = 'true';
						}
						$smarty->assign('PARCEL_TRACKING_CODES', $send_parcel_tracking_codes);
						// END Parcel Tracking Code

						// (xycons.de - Additional Extenders) (START)
						$coo_order_status_mail_extender_component->set_data('action', $_GET['action']);
						$coo_order_status_mail_extender_component->proceed();

						if(is_array($coo_order_status_mail_extender_component->v_output_buffer))
						{
							foreach($coo_order_status_mail_extender_component->v_output_buffer as $t_key => $t_value)
							{
								$smarty->assign($t_key, $t_value);
							}
						}
						// (xycons.de - Additional Extenders) (END)

						$smarty->assign('NOTIFY_COMMENTS', nl2br($notify_comments));
						$html_mail = fetch_email_template($smarty, 'change_order_mail', 'html');
						$smarty->assign('NOTIFY_COMMENTS', $notify_comments);
						$txt_mail = fetch_email_template($smarty, 'change_order_mail', 'txt');

						// BOF GM_MOD
						if($_SESSION['language'] == 'german') xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'], $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', UPDATE_ORDER_EMAIL_SUBJECT_TEXT . ' ' .$oID.', '.xtc_date_long($check_status['date_purchased']).', '.$check_status['customers_name'], $html_mail, $txt_mail);

						else xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'], $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', UPDATE_ORDER_EMAIL_SUBJECT_TEXT . ' ' .$oID. ', '.xtc_date_long($check_status['date_purchased']).', '.$check_status['customers_name'], $html_mail, $txt_mail);
						// EOF GM_MOD
						$customer_notified = '1';
					}

					xtc_db_query("INSERT INTO " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('".xtc_db_input($oID)."', '".xtc_db_input($gm_status)."', now(), '".$customer_notified."', '".xtc_db_input($gm_comments)."')");

					$order_updated = true;
				}
			}

			if ($order_updated) {
				$messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
			} else {
				$messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
			}

			$coo_order_action_extender_component->set_data('order_updated', $order_updated);

			xtc_redirect(xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('action')).'action=edit'));

	break;

	case 'update_order':
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			$oID = xtc_db_prepare_input($_GET['oID']);
			$status = xtc_db_prepare_input($_POST['status']);
			$comments = xtc_db_prepare_input($_POST['comments']);

			//Interkurier begin \
			require('interkurierConnect.php');
			//Interkurier end\

			//	$order = new order($oID);
			$order_updated = false;
			$check_status_query = xtc_db_query("select customers_name, customers_email_address, orders_status, date_purchased from ".TABLE_ORDERS." where orders_id = '".xtc_db_input($oID)."'");
			$check_status = xtc_db_fetch_array($check_status_query);

			if ($check_status['orders_status'] != $status || $comments != '') {

				if(xtc_db_input($status) == gm_get_conf('GM_ORDER_STATUS_CANCEL_ID')) {
					$gm_update = "gm_cancel_date = now(),";
				}

				xtc_db_query("
								UPDATE " .
									TABLE_ORDERS . "
								SET
									" . $gm_update . "
									orders_status = '".xtc_db_input($status)."',
									last_modified = now()
								WHERE
									orders_id = '".xtc_db_input($oID)."'
								");

				$customer_notified = '0';
				if($_POST['notify'] == 'on') {
					$notify_comments = '';
					if ($_POST['notify_comments'] == 'on') {
						//$notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments)."\n\n";
						$notify_comments = $comments;
					} else {
						$notify_comments = '';
					}

					// assign language to template for caching
					$smarty->assign('language', $_SESSION['language']);
					$smarty->caching = false;

					// set dirs manual
					$smarty->template_dir = DIR_FS_CATALOG.'templates';
					$smarty->compile_dir = DIR_FS_CATALOG.'templates_c';
					$smarty->config_dir = DIR_FS_CATALOG.'lang';

					$smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
					$smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');

					$smarty->assign('NAME', $check_status['customers_name']);
					$smarty->assign('GENDER', $order->customer['gender']);
					$smarty->assign('ORDER_NR', $oID);
					$smarty->assign('ORDER_LINK', xtc_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id='.$oID, 'SSL'));
					$smarty->assign('ORDER_DATE', xtc_date_long($check_status['date_purchased']));

					$smarty->assign('ORDER_STATUS', $orders_status_array[$status]);

					if(defined('EMAIL_SIGNATURE')) {
						$smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
						$smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
					}

					// START Parcel Tracking Code
					/** @var ParcelTrackingCode $coo_parcel_tracking_code_item */
					$coo_parcel_tracking_code_item = MainFactory::create_object('ParcelTrackingCode');
					/** @var ParcelTrackingCodeReader $coo_parcel_tracking_code_reader */
					$coo_parcel_tracking_code_reader = MainFactory::create_object('ParcelTrackingCodeReader');
					$t_parcel_tracking_codes_array = $coo_parcel_tracking_code_reader->getTackingCodeItemsByOrderId($coo_parcel_tracking_code_item,
																												$oID);
					$smarty->assign('PARCEL_TRACKING_CODES_ARRAY', $t_parcel_tracking_codes_array);
					$send_parcel_tracking_codes = '';
					if($_POST['send_parcel_tracking_codes'] == 'on') {
						$send_parcel_tracking_codes = 'true';
					}
					$smarty->assign('PARCEL_TRACKING_CODES', $send_parcel_tracking_codes);
					// END Parcel Tracking Code

					// (xycons.de - Additional Extenders) (START)
					$coo_order_status_mail_extender_component->set_data('action', $_GET['action']);
					$coo_order_status_mail_extender_component->proceed();
					if(is_array($coo_order_status_mail_extender_component->v_output_buffer))
					{
						foreach($coo_order_status_mail_extender_component->v_output_buffer as $t_key => $t_value)
						{
							$smarty->assign($t_key, $t_value);
						}
					}
					// (xycons.de - Additional Extenders) (END)

					$smarty->assign('NOTIFY_COMMENTS', nl2br($notify_comments));
					$html_mail = fetch_email_template($smarty, 'change_order_mail', 'html');
					$smarty->assign('NOTIFY_COMMENTS', $notify_comments);
					$txt_mail = fetch_email_template($smarty, 'change_order_mail', 'txt');

					// BOF GM_MOD

					if($_SESSION['language'] == 'german') xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'], $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', 'Ihre Bestellung '.$oID.', '.xtc_date_long($check_status['date_purchased']).', '.$check_status['customers_name'], $html_mail, $txt_mail);



					else xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'], $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', 'Your Order '.$oID.', '.xtc_date_long($check_status['date_purchased']).', '.$check_status['customers_name'], $html_mail, $txt_mail);

					// EOF GM_MOD



					$customer_notified = '1';
				}

				xtc_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('".xtc_db_input($oID)."', '".xtc_db_input($status)."', now(), '".$customer_notified."', '".xtc_db_input($comments)."')");

				$order_updated = true;
			}

			if ($order_updated) {
				$messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
			} else {
				$messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
			}

			$coo_order_action_extender_component->set_data('order_updated', $order_updated);

			xtc_redirect(xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('action')).'action=edit'));
		}
		break;
	case 'resendordermail':
		break;
	case 'deleteconfirm':
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			if(isset($_POST['gm_multi_status']) && is_array($_POST['gm_multi_status']))
			{
				foreach($_POST['gm_multi_status'] as $orderID)
				{
					$orderID = (int)$orderID;
					xtc_remove_order($orderID, $_POST['restock'], false, $_POST['reshipp']);
				}
			}
			else
			{
				$oID = xtc_db_prepare_input($_GET['oID']);
				xtc_remove_order($oID, $_POST['restock'], false, $_POST['reshipp']);
			}

			xtc_redirect(xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action'))));
		}
		break;
		// BMC Delete CC info Start
		// Remove CVV Number
	case 'deleteccinfo':
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			$oID = xtc_db_prepare_input($_GET['oID']);

			xtc_db_query("update ".TABLE_ORDERS." set cc_cvv = null where orders_id = '".xtc_db_input($oID)."'");
			xtc_db_query("update ".TABLE_ORDERS." set cc_number = '0000000000000000' where orders_id = '".xtc_db_input($oID)."'");
			xtc_db_query("update ".TABLE_ORDERS." set cc_expires = null where orders_id = '".xtc_db_input($oID)."'");
			xtc_db_query("update ".TABLE_ORDERS." set cc_start = null where orders_id = '".xtc_db_input($oID)."'");
			xtc_db_query("update ".TABLE_ORDERS." set cc_issue = null where orders_id = '".xtc_db_input($oID)."'");

			xtc_redirect(xtc_href_link(FILENAME_ORDERS, 'oID='.$_GET['oID'].'&action=edit'));
		}
		break;

	case 'afterbuy_send' :
		$oID = xtc_db_prepare_input($_GET['oID']);
		require_once (DIR_FS_CATALOG.'includes/classes/afterbuy.php');
		$aBUY = new xtc_afterbuy_functions($oID);
		if ($aBUY->order_send())
			$aBUY->process_order();

		break;

		// BMC Delete CC Info End
}

// Pay Pal deprecated check
PayPalDeprecatedCheck::ppDeprecatedCheck($messageStack);

//var_dump($t_orders_id_string);

// (xycons.de - Additional Extenders) (START)
if(isset($_GET['action']) && !empty($_GET['action']))
{
	$coo_order_action_extender_component->set_data('action', $_GET['action']);
	$coo_order_action_extender_component->proceed();
}
// (xycons.de - Additional Extenders) (END)
?>

<?php
// BOF GM_MOD GX-Customizer
if($_GET['action'] == 'edit')
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo HTML_PARAMS; ?>>
<?php
}
else
{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<?php
}
// EOF GM_MOD GX-Customizer
?>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<script type="text/javascript">
			var oID = "<?php echo (int)$_GET['oID']; ?>";
		</script>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_ADMIN; ?>html/assets/styles/legacy/lightbox.css">
		<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_ADMIN; ?>html/assets/styles/legacy/buttons.css">
		<?php
		// BOF GM_MOD GX-Customizer:
		include_once('../gm/modules/gm_gprint_admin_orders_css.php');
		?>

		<style>
			<?php
				while($orderStatusColorsRow = xtc_db_fetch_array($orderStatusColorsResult))
				{
					echo '.badge-' . $orderStatusColorsRow['orders_status_id'] . ' {';
					echo '  color: #' . (ColorHelper::getLuminance(new StringType($orderStatusColorsRow['color'])) > 143 ? '000000' : 'FFFFFF') . ' !important;';
					echo '  background-color: #' . $orderStatusColorsRow['color'] . ' !important;';
					echo '  background-image: none !important;';
					echo ' }';
					echo "\n";
				}
			?>
		</style>
	</head>
	<body marginwidth="0"
	      marginheight="0"
	      topmargin="0"
	      bottommargin="0"
	      leftmargin="0"
	      rightmargin="0"
	      bgcolor="#FFFFFF"
	      data-gx-widget="button_dropdown"
	      data-button_dropdown-user_id="<?php echo (int)$_SESSION['customer_id']; ?>"
	      data-button_dropdown-config_keys="orderOverviewDropdownBtn orderMultiDropdownBtn"
	>
		<?php
			if(!isset($_GET['oID']) || !isset($_GET['action']))
			{
				include DIR_FS_ADMIN . 'html/content/orders_delete_form.php';
				include DIR_FS_ADMIN . 'html/content/orders_multi_delete_form.php';
				include DIR_FS_ADMIN . 'html/content/orders_add_tracking_code_form.php';
				include DIR_FS_ADMIN . 'html/content/orders_update_orders_status.php';
				include DIR_FS_ADMIN . 'html/content/orders_multi_cancel.php';
			}
		?>
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<?php
		// BOF GM_MOD GX-Customizer:
		include_once('../gm/modules/gm_gprint_admin_orders_js.php');
		?>
		<table border="0" style="width:100%; height:100%;" cellspacing="0" cellpadding="0">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
					<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="0" class="columnLeft">
						<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					</table>
				</td>
				<!-- body_text //-->

			<?php if (($_GET['action'] == 'edit') && ($order_exists)) { ?>

				<!-- COMPATIBILITY ORDER DETAILS VIEW -->
				<td class="boxCenter" width="100%" valign="top" style="display:table-cell">

					<table class="hidden" border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/kunden.png)">&nbsp;<?php echo ORDER_HEADING_TITLE .' '. $oID; ?></div>
							</td>
						</tr>
					</table>

					<?php
						require DIR_FS_ADMIN . 'html/compatibility/order_details.php';
						include DIR_FS_ADMIN . 'html/content/orders_delete_tracking_code_form.php';
						include DIR_FS_ADMIN . 'html/content/orders_update_orders_status.php';
					?>
				</td>

				<td class="boxCenter hidden" width="100%" valign="top">
					<?php /* BEGIN INTRASHIP */
						if(file_exists(DIR_FS_CATALOG.'gm/classes/GMIntraship.php'))
						{
							$intraship = new GMIntraship();
							if(isset($_SESSION['intraship_warning_not_codeable']) && $_SESSION['intraship_warning_not_codeable'] == true): ?>
								<p style="margin: 1em auto; width: 80%; background: #ffe; border: 1px solid #f00; padding: 1ex 1em; font-family: sans-serif;">
									<strong><?php echo $intraship->get_text('warning'); ?>:</strong> <?php echo $intraship->get_text('label_not_codeable'); ?>
								</p>
								<?php
									unset($_SESSION['intraship_warning_not_codeable']);
							endif;
						}
					/* END INTRASHIP */ ?>
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<div class="pageHeading hidden" style="background-image:url(html/assets/images/legacy/gm_icons/kunden.png)">
								<div style="float:left">
									 <?php /* BOF GM_MOD */ echo GM_ORDERS_NUMBER . $oID . ' - ' . xtc_date_short($order->info['date_purchased']) . ' ' . date("H:i", strtotime($order->info['date_purchased'])) . GM_ORDERS_EDIT_CLOCK; /* EOF GM_MOD */?>
								</div>
								<div>
									<?php echo '<a class="button float_right" href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('action'))) . '">' . BUTTON_BACK . '</a>'; ?>
									<a class="button float_right" href="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'oID='.$_GET['oID'].'&cID=' . $order->customer['ID']);?>"><?php echo BUTTON_EDIT ?></a>

									<?php // BEGIN HERMES
									if($hermes->getUsername() != '' || $hermes->getService() == 'PriPS')
									{
										echo '<a class="button float_right" href="' . xtc_href_link('hermes_order.php', 'orders_id=' . $_GET['oID']) . '">'.$hermes->get_text('hermes_shipping').'</a>';
									}
									// END HERMES ?>

									<?php // Intraship
									if(isset($intraship) && $intraship->active == true):
										$label_url = $intraship->getLabelURL((int)$_GET['oID']);
										?>
										<?php if(!empty($label_url)): ?>
											<a class="button float_right" href="<?php echo $label_url ?>"><?php echo $intraship->get_text('dhl_label_show') ?></a>
										<?php else: ?>
											<a class="button float_right" href="<?php echo xtc_href_link('print_intraship_label.php','oID='.(int)$_GET['oID']) ?>"><?php echo $intraship->get_text('dhl_label_get') ?></a>
										<?php endif; ?>
									<?php endif; // END Intraship ?>
								</div>
							</td>
						</tr>
					</table>
					<br />
<!-- ORDERS - OVERVIEW -->
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="pdf_menu">
						<tr>
							<td width="120" class="" style="border-right: 0px;">
								<?php echo HEADING_TITLE; ?>
							</td>
						</tr>
					</table>
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
						<tr>
							<td width="80" class="main gm_strong" valign="top">
								<?php echo ENTRY_CUSTOMER; ?>
							</td>
							<td class="main" valign="top">
								<?php
									// BOF GM_MOD
									$gm_get_gender = xtc_db_query("SELECT customers_gender
																	FROM orders
																	WHERE customers_id = '" . $order->customer['ID'] . "' AND orders_id = '" . (int)$_GET['oID'] . "'");
									if(xtc_db_num_rows($gm_get_gender) == 1){
										$row = xtc_db_fetch_array($gm_get_gender);
										if($row['customers_gender'] == 'm') echo $coo_lang_file_master->get_text('gender_male', 'account_edit', $_SESSION['languages_id']) . '<br />';
										elseif($row['customers_gender'] == 'f') echo $coo_lang_file_master->get_text('gender_female', 'account_edit', $_SESSION['languages_id']) . '<br />';
									}
									// EOF GM_MOD
								?>
								<?php echo xtc_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'); ?>
							</td>
							<td width="80" class="main gm_strong" valign="top">
								<?php echo ENTRY_SHIPPING_ADDRESS; ?>
							</td>
							<td class="main" valign="top">
								<?php
									// BOF GM_MOD
									$gm_get_gender = xtc_db_query("SELECT delivery_gender
																	FROM orders
																	WHERE customers_id = '" . $order->customer['ID'] . "' AND orders_id = '" . (int)$_GET['oID'] . "'");
									if(xtc_db_num_rows($gm_get_gender) == 1){
										$row = xtc_db_fetch_array($gm_get_gender);
										if($row['delivery_gender'] == 'm') echo $coo_lang_file_master->get_text('gender_male', 'account_edit', $_SESSION['languages_id']) . '<br />';
										elseif($row['delivery_gender'] == 'f') echo $coo_lang_file_master->get_text('gender_female', 'account_edit', $_SESSION['languages_id']) . '<br />';
									}
									// EOF GM_MOD
								?>
								<?php echo xtc_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'); ?>
							</td>
							<td width="80" class="main gm_strong" valign="top">
								<?php echo ENTRY_BILLING_ADDRESS; ?>
							</td>
							<td class="main" valign="top">
								<?php
									// BOF GM_MOD
									$gm_get_gender = xtc_db_query("SELECT billing_gender
																	FROM orders
																	WHERE customers_id = '" . $order->customer['ID'] . "' AND orders_id = '" . (int)$_GET['oID'] . "'");
									if(xtc_db_num_rows($gm_get_gender) == 1){
										$row = xtc_db_fetch_array($gm_get_gender);
										if($row['billing_gender'] == 'm') echo $coo_lang_file_master->get_text('gender_male', 'account_edit', $_SESSION['languages_id']) . '<br />';
										elseif($row['billing_gender'] == 'f') echo $coo_lang_file_master->get_text('gender_female', 'account_edit', $_SESSION['languages_id']) . '<br />';
									}
									// EOF GM_MOD
								?>
								<?php echo xtc_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'); ?>
							</td>
						</tr>

						<tr><td colspan="6" class="main" valign="top">&nbsp;</td></tr>

						<?php if ($order->customer['csID']!='') { ?>
						<tr>
							<td width="80" class="main gm_strong" valign="top">
								<?php echo TITLE_CUSTOMER_ID; ?>
							</td>
							<td colspan="5" class="main" valign="top">
								<?php echo $order->customer['csID']; ?>
							</td>
						</tr>
						<?php } ?>

						<?php if ($order->customer['telephone']!='') { ?>
						<tr>
							<td width="80" class="main gm_strong" valign="top">
								<?php echo ENTRY_TELEPHONE; ?>
							</td>
							<td colspan="5" class="main" valign="top">
								<?php echo $order->customer['telephone']; ?>
							</td>
						</tr>
						<?php } ?>

						<tr>
							<td width="80" class="main gm_strong" valign="top">
								<?php echo GM_MAIL; ?>
							</td>
							<td colspan="5" class="main" valign="top">
								<?php echo '<a href="mailto:' . $order->customer['email_address'] . '"><u>' . $order->customer['email_address'] . '</u></a>'; ?>
							</td>
						</tr>

						<?php if ($order->customer['vat_id']!='') { ?>
						<tr>
							<td width="80" class="main gm_strong" valign="top">
								<?php echo ENTRY_CUSTOMERS_VAT_ID; ?>
							</td>
							<td colspan="5" class="main" valign="top">
								<?php echo $order->customer['vat_id']; ?>
							</td>
						</tr>
						<?php } ?>

						<?php if ( $order->customer['cIP']!='') { ?>
						<tr>
							<td width="80" class="main gm_strong" valign="top">
								<?php echo IP; ?>
							</td>
							<td colspan="5" class="main" valign="top">
								<?php echo $order->customer['cIP']; ?>
							</td>
						</tr>
						<?php } ?>

						<tr>
							<td width="80" class="main gm_strong" valign="top">
								<?php echo ENTRY_LANGUAGE; ?>
							</td>
							<td colspan="5" class="main" valign="top">
								<?php echo $order->info['language']; ?>
							</td>
						</tr>

						<tr>
							<td width="80" class="main gm_strong" valign="top">
								<?php echo ENTRY_PAYMENT_METHOD; ?>
							</td>
							<td colspan="5" class="main" valign="top">
								<?php echo $order->info['payment_method']; ?>
							</td>
						</tr>

						<?php
						$memo_query = xtc_db_query("SELECT count(*) as count FROM ".TABLE_CUSTOMERS_MEMO." where customers_id='".$order->customer['ID']."'");
						$memo_count = xtc_db_fetch_array($memo_query);
						?>

						<tr>
							<td width="80" class="main gm_strong" valign="top">
								<?php echo CUSTOMERS_MEMO; ?>
							</td>
							<td colspan="5" class="main" valign="top">
								<?php echo $memo_count['count'].'</b>'; ?>  <span style="cursor:pointer" onClick="javascript:window.open('<?php echo xtc_href_link(FILENAME_POPUP_MEMO,'ID='.$order->customer['ID']); ?>', 'popup', 'scrollbars=yes, width=500, height=500')">(<?php echo DISPLAY_MEMOS; ?>)</span>
							</td>
						</tr>
					</table>

					<!-- EXTENSIONS -->
					<?php
					$extensions = glob(DIR_FS_ADMIN . 'includes/modules/orders/*.php');
					if(is_array($extensions))
					{
						foreach($extensions as $extension_file)
						{
							include $extension_file;
						}
					}

					//$coo_order_extender_component = MainFactory::create_object('OrderExtenderComponent');
					//$coo_order_extender_component->set_data('GET', $_GET);
					//$coo_order_extender_component->set_data('POST', $_POST);
					//$coo_order_extender_component->proceed();
					?>


<!-- ORDERS - PAYPAL -->
				<?php
					if(strstr($order->info['payment_method'], 'paypal')
						/* magnalister v1.0.0 */
						&& (
							(function_exists('magnaExecute')) ?
							magnaExecute('magnaRenderOrderDetails', array('oID' => $oID),
							array('order_details.php')) == '': true
						)
						/* END magnalister */
						/* PayPalNG */
						&& strpos($order->info['payment_method'], 'paypalng') === false
						/* END PayPalNG */
					) {
				?>
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td width="30%" class=""><?php echo TABLE_HEADING_PAYPAL; ?></td>
						</tr>
					</table>
					<?php
					if ($order->info['payment_method']=='paypal_ipn' or $order->info['payment_method']=='paypal_directpayment' or $order->info['payment_method']=='paypal' or $order->info['payment_method']=='paypalexpress') {
						?>
						<script type="text/javascript" src="html/assets/javascript/legacy/modules/LoadPayPalAdminNotification.js"></script>
						<script type="text/javascript">
							$(document).ready(function(){
								var coo_pay_pal_admin_notification = new LoadPayPalAdminNotification();
								coo_pay_pal_admin_notification.load_admin_notification('<?php echo $_GET['oID']; ?>', '<?php echo $_GET['action']; ?>');
								$('#reload_paypal_admin_notifikation').bind("click", function(){
									$('#paypal_admin_notification_text').hide();
									$('#paypal_admin_notification_error').hide();
									$('#paypal_admin_notification_loader').show();
									coo_pay_pal_admin_notification.load_admin_notification('<?php echo $_GET['oID']; ?>', '<?php echo $_GET['action']; ?>');
									return false;
								});
							});
						</script>
						<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
							<tr><td class="main">
								<div id="paypal_admin_notification">
									<div id="paypal_admin_notification_loader">
										<div style="padding-left: 30px; background: url('../images/loading.gif') left center no-repeat;"><?php echo TEXT_PPNOTIFICATION_LOADING; ?></div>
									</div>
									<div id="paypal_admin_notification_text"></div>
									<div id="paypal_admin_notification_error" style="display: none;">
										<?php echo TEXT_PPNOTIFICATION_ERROR; ?><br />
										<a id="reload_paypal_admin_notifikation" class="button" href="#"><?php echo BUTTON_PP_RELOAD; ?></a>
									</div>
								</div>
							</td></tr>
						</table>
						<?php
					}
				}
				?>

<!-- ORDERS - WITHDRAWALS -->
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td width="15%" class="">
							<?php echo TABLE_HEADING_WITHDRAWAL_ID; ?>
							</td>
							<td class="" style="border-right: 0px;">
								<?php echo TABLE_HEADING_DATE_ADDED; ?>
							</td>
						</tr>
					</table>

					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
						<?php
						$t_withdrawal_query = 'SELECT withdrawal_id, date_created FROM withdrawals WHERE order_id = \'' . xtc_db_input($oID) . '\' ORDER BY withdrawal_id';
						$t_withdrawal_result = xtc_db_query($t_withdrawal_query);
						if(xtc_db_num_rows($t_withdrawal_result))
						{
							while($t_whithdrawal_row = xtc_db_fetch_array($t_withdrawal_result))
							{
								echo '<tr>';
								echo '<td class="smallText" width="15%" align="left">';
								echo $t_whithdrawal_row['withdrawal_id'] . ' <a href="' . xtc_href_link('withdrawals.php', 'id=' . $t_whithdrawal_row['withdrawal_id'] . '&action=edit') . '">(' . TEXT_SHOW_WITHDRAWAL . ')</a>';
								echo '</td>';
								echo '<td class="smallText" align="left">';
								echo xtc_datetime_short($t_whithdrawal_row['date_created']);
								echo '</td>';
								echo '</tr>';
							}
						}
						else
						{
							echo '<tr>';
							echo '<td class="smallText" align="left" colspan="2">' . TEXT_NO_WITHDRAWALS . '</td>';
							echo '</tr>';
						}

						$t_orders_hash_string = '';
						$t_orders_id_string = '';
						if(isset($order->info['orders_hash']) && empty($order->info['orders_hash']) == false)
						{
							$t_orders_hash_string = 'order=' . $order->info['orders_hash'];
						}
						else
						{
							$t_orders_id_string = 'order_id=' . xtc_db_input($oID);
						}

						echo '<tr>';
						echo '<td class="smallText" align="left" colspan="2">';

						echo '<a style="width:120px; margin-top: 20px;" class="button" href="' . xtc_catalog_href_link('withdrawal.php', $t_orders_hash_string . $t_orders_id_string, 'SSL') . '" target="_blank">' . TEXT_CREATE_WITHDRAWAL . '</a>';
						echo '</td>';
						echo '</tr>';
						?>
					</table>
<!-- ORDERS - WITHDRAWALS - END -->

					<?php
					// (xycons.de - Additional Extenders) (START)
					//echo $coo_order_extender_component->get_output('below_withdrawal');
					// (xycons.de - Additional Extenders) (END)
					?>

<!-- ORDERS - ABANDONMENT OF WITHDRAWAL -->
					<?php
					$t_has_download_products = false;
					$t_has_service_products = false;

					foreach($order->products as $t_actual_product)
					{
						$t_has_download_products = $t_has_download_products || $t_actual_product['product_type'] == 2;
						$t_has_service_products = $t_has_service_products || $t_actual_product['product_type'] == 3;
					}

					if($t_has_download_products || $t_has_service_products)
					{
					?>
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td class="">
							<?php echo TABLE_HEADING_ABANDONMENT_WITHDRAWAL; ?>
							</td>
						</tr>
					</table>

					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
						<tr>
							<td class="smallText">
								<?php
								if($t_has_download_products)
								{
									echo TEXT_ABANDONMENT_DOWNLOAD . ' <b>';
									if($order->info['abandonment_download'] == 1)
									{
										echo strtoupper(YES);
									}
									else
									{
										echo strtoupper(NO);
									}
									echo '</b><br />';
								}

								if($t_has_service_products)
								{
									echo TEXT_ABANDONMENT_SERVICE . ' <b>';
									if($order->info['abandonment_service'] == 1)
									{
										echo strtoupper(YES);
									}
									else
									{
										echo strtoupper(NO);
									}
									echo '</b><br />';
								}
								?>
							</td>
						</tr>
					</table>
					<?php
					}
					?>
<!-- ORDERS - ABANDONMENT OF WITHDRAWAL - END -->

<!-- ORDERS - DATA -->
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td width="30%" class=""><?php echo TABLE_HEADING_PRODUCTS; ?></td>
							<td width="10%" class=""><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
							<td width="20%" class="" align="right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></td>
							<?php if ($order->products[0]['allow_tax'] == 1) { ?>
							<td width="10%" class="" align="right"><?php echo TABLE_HEADING_TAX; ?></td>
							<td width="15%" class="" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
							<?php } ?>
							<td width="15%" class="" align="right" style="border-right: 0px;"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX;
							if ($order->products[$i]['allow_tax'] == 1) {
								echo ' (excl.)';
							}
							?>
							</td>
						</tr>
					</table>
					<table style="background-color:#d6e6f3" border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
					<?php
						for ($i = 0, $n = sizeof($order->products); $i < $n; $i ++)
						{
						echo '<tr style="background-color:#d6e6f3" class="dataTableRow">'."\n".'
							<td style="border-right: 0px;" width="30%" class="dataTableContent" valign="top">' . gm_prepare_number($order->products[$i]['qty']).'&nbsp;' . ((!empty($order->products[$i]['quantity_unit_id'])) ? $order->products[$i]['unit_name'] : 'x') . '&nbsp;' . $order->products[$i]['name'];
							# attributes BOF
							if (sizeof($order->products[$i]['attributes']) > 0)
							{
								for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j ++)
								{
									// BOF GM_MOD GX-Customizer
									if(!empty($order->products[$i]['attributes'][$j]['option']) || !empty($order->products[$i]['attributes'][$j]['value']))
									{
										echo '<br /><nobr><small>&nbsp;<i> - '.$order->products[$i]['attributes'][$j]['option'].': '.$order->products[$i]['attributes'][$j]['value'].'</i></small></nobr>';
									}
									// EOF GM_MOD GX-Customizer
								}

								// BOF GM_MOD GX-Customizer:
								//include(DIR_FS_CATALOG . 'gm/modules/gm_gprint_admin_orders.php');
							}
							# attributes EOF

							# properties BOF
							if (sizeof($order->products[$i]['properties']) > 0)
							{
								for ($j = 0, $k = sizeof($order->products[$i]['properties']); $j < $k; $j ++)
								{
									if(!empty($order->products[$i]['properties'][$j]['properties_name']) || !empty($order->products[$i]['properties'][$j]['values_name']))
									{
										echo '<br /><nobr><small>&nbsp;<i> - '.$order->products[$i]['properties'][$j]['properties_name'].': '.$order->products[$i]['properties'][$j]['values_name'].'</i></small></nobr>';
									}
								}
							}
							# properties EOF

							// BOF GM_MOD GX-Customizer:
							echo '</td>'."\n".'<td class="dataTableContent" valign="top" style="border-right: 0px; vertical-align: top" width="10%" >';
							if ($order->products[$i]['model'] != '') {
								echo $order->products[$i]['model'];
							} else {
								echo '<br />';
							}

							// attribute models
							if(sizeof($order->products[$i]['attributes']) > 0)
							{
								$t_languages_id = $_SESSION['languages_id'];
								$t_languages_id_sql = "SELECT l.languages_id
														FROM
															orders o,
															languages l
														WHERE
															o.orders_id = '" . (int)$_GET['oID'] . "' AND
															o.language = l.directory
														LIMIT 1";
								$t_languages_id_result = xtc_db_query($t_languages_id_sql);
								if(xtc_db_num_rows($t_languages_id_result) == 1)
								{
									$t_languages_id_result_array = xtc_db_fetch_array($t_languages_id_result);
									$t_languages_id = $t_languages_id_result_array['languages_id'];
								}

								for($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j ++)
								{
									$model = xtc_get_attributes_model($order->products[$i]['id'], $order->products[$i]['attributes'][$j]['value'],$order->products[$i]['attributes'][$j]['option'], $t_languages_id);
									if ($model != '') {
										echo $model.'<br />';
									} else {
										echo '<br />';
									}
								}
							}

							echo '&nbsp;</td>'."\n".'<td style="border-right: 0px;" width="20%" class="dataTableContent" align="right" valign="top">';
							if($order->products[$i]['qty'] == 0)
							{
								echo format_price(0.0, 1, $order->info['currency'], $order->products[$i]['allow_tax'], $order->products[$i]['tax']).'</td>'."\n";
							}
							else
							{
								echo format_price($order->products[$i]['final_price'] / $order->products[$i]['qty'], 1, $order->info['currency'], $order->products[$i]['allow_tax'], $order->products[$i]['tax']).'</td>'."\n";
							}

							if($order->products[$i]['allow_tax'] == 1)
							{
									echo '<td style="border-right: 0px;" width="10%" class="dataTableContent" align="right" valign="top">';
									echo xtc_display_tax_value($order->products[$i]['tax']).'%';
									echo '</td>'."\n";
									echo '<td style="border-right: 0px;" width="15%"  class="dataTableContent" align="right" valign="top"><b>';
								if($order->products[$i]['qty'] == 0)
								{
									echo format_price(0.0, 1, $order->info['currency'], 0, 0);
								}
								else
								{
									echo format_price($order->products[$i]['final_price'] / $order->products[$i]['qty'], 1, $order->info['currency'], 0, 0);
								}
									echo '</b></td>'."\n";
								}
									echo '<td style="border-right: 0px;" width="15%" class="dataTableContent" align="right" valign="top"><b>'.format_price(($order->products[$i]['final_price']), 1, $order->info['currency'], 0, 0).'</b></td>'."\n";
									echo '</tr>'."\n";
							}
						?>
							<?php
						for($i = 0, $n = sizeof($order->totals); $i < $n; $i ++)
						{
							if($order->products[0]['allow_tax'] == 1)
							{
										echo '<tr>'."\n".'<td colspan="5" align="right" class="smallText">'.$order->totals[$i]['title'].'</td>'."\n".'
										<td align="right" class="smallText">'.$order->totals[$i]['text'].'</td>'."\n".'</tr>'."\n";
							}
							else
							{
										echo '<tr>'."\n".'<td colspan="3" align="right" class="smallText">'.$order->totals[$i]['title'].'</td>'."\n".'
										<td align="right" class="smallText">'.$order->totals[$i]['text'].'</td>'."\n".'</tr>'."\n";
									}
								}
							?>
					</table>
<?php
/* magnalister v1.0.0 */
if (function_exists('magnaExecute')) echo magnaExecute('magnaRenderOrderDetails', array('oID' => $oID), array('order_details.php'));
/* END magnalister */
?>
<!-- ORDERS - STATUS -->
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="pdf_menu">
						<tr>
							<td width="25%" class="">
								<?php echo TABLE_HEADING_DATE_ADDED; ?>
							</td>
							<td width="25%" class="">
								<?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?>
							</td>
							<td width="25%"class="">
								<?php echo TABLE_HEADING_STATUS; ?>
							</td>
							<td width="25%" class="" style="border-right: 0px;">
								<?php echo TABLE_HEADING_COMMENTS; ?>
							</td>
						</tr>
					</table>

					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
					<?php

					$orders_history_query = xtc_db_query("select orders_status_id, date_added, customer_notified, comments from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id = '".xtc_db_input($oID)."' order by date_added");
					if (xtc_db_num_rows($orders_history_query)) {
						while ($orders_history = xtc_db_fetch_array($orders_history_query)) {
							echo '<tr>'."\n".'
							<td width="25%" class="smallText" align="left">'.xtc_datetime_short($orders_history['date_added']).'</td>'."\n".'
							<td width="25%" class="smallText" align="left">';
							if ($orders_history['customer_notified'] == '1') {
								echo xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/tick.gif', ICON_TICK)."</td>\n";
							} else {
								echo xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/cross.gif', ICON_CROSS)."</td>\n";
							}

							echo '<td width="25%" class="smallText">';
							if($orders_history['orders_status_id'] !== null) {
								echo $orders_status_array[$orders_history['orders_status_id']];
							}
							echo '</td>'."\n".'<td width="25%" align="left" class="smallText">'.nl2br(xtc_db_output($orders_history['comments'])).'&nbsp;</td>'."\n".'</tr>'."\n";
							}
						} else {
							echo '<tr>'."\n".'<td class="smallText" colspan="4">'.TEXT_NO_ORDER_HISTORY.'</td>'."\n".'</tr>'."\n";
					}
					?>
					</table>

					<?php
						// (xycons.de - Additional Extenders) (START)
						//echo $coo_order_extender_component->get_output('below_history');
						// (xycons.de - Additional Extenders) (END)
					?>

<!-- ORDERS - STATUS SEND -->
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="pdf_menu">
						<tr>
							<td class="" style="border-right: 0px;">
								<?php echo TABLE_HEADING_STATUS; ?>
							</td>
						</tr>
					</table>
					<?php echo xtc_draw_form('status', FILENAME_ORDERS, xtc_get_all_get_params(array('action')) . 'action=update_order'); ?>
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
						<tr>
							<td width="160" class="main" valign="top">
								<?php echo ENTRY_STATUS; ?>
							</td>
							<td class="main" valign="top">
								<?php echo xtc_draw_pull_down_menu('status', $change_orders_status, $order->info['orders_status']); ?>
							</td>
						</tr><?php
						/* magnalister v2.0.0 */
						if (function_exists('magnaExecute')) magnaExecute('magnaRenderOrderStatusSync', array(), array('order_details.php'));
						/* END magnalister */
						?><tr>
							<td width="160" class="main" valign="top">
								<?php echo ENTRY_NOTIFY_CUSTOMER; ?>
							</td>
							<td class="main" valign="top">
								<?php echo xtc_draw_checkbox_field('notify', 'on', true); ?>
							</td>
						</tr>
						<tr>
							<td width="160" class="main" valign="top">
								<?php echo ENTRY_SEND_PARCEL_TRACKING_CODES; ?>
							</td>
							<td class="main" valign="top">
								<?php echo xtc_draw_checkbox_field('send_parcel_tracking_codes', 'on', true); ?>
							</td>
						</tr>
						<tr>
							<td width="160" class="main" valign="top">
								<?php echo ENTRY_NOTIFY_COMMENTS; ?>
							</td>
							<td class="main" valign="top">
								<?php echo xtc_draw_checkbox_field('notify_comments', 'on', true); ?>
							</td>
						</tr>
						<tr>
							<td width="160" class="main" valign="top">
								<?php echo TABLE_HEADING_COMMENTS; ?>
							</td>
							<td class="main" valign="top">
								<?php echo xtc_draw_textarea_field('comments', 'soft', '60', '3', $order->info['comments']); ?>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="main" valign="top">
								&nbsp;
							</td>
						</tr>

						<?php
							// (xycons.de - Additional Extenders) (START)
							//echo $coo_order_extender_component->get_output('order_status');
							// (xycons.de - Additional Extenders) (END)
						?>

						<tr>
							<td colspan="2" class="main" valign="top">
								<?php echo xtc_draw_hidden_field('page_token', $t_page_token); ?>
								<input type="submit" class="button" value="<?php echo BUTTON_UPDATE; ?>">
							</td>
						</tr>
					</table>
				</form>
<!-- ORDERS - BUTTONS -->
					<a style="width:170px;" class="button float_right" href=<?php echo '"' . xtc_href_link(FILENAME_ORDERS, 'oID='.$_GET['oID'].'&action=deleteccinfo&page_token=' . $t_page_token).'">'.BUTTON_REMOVE_CC_INFO;?></a>
					<?php
						echo '<input type="hidden" value="' . $_GET['oID'] .'" id="gm_order_id">';

						// BOF eKomi
						if(gm_get_conf('EKOMI_STATUS') == '1' && $coo_ekomi_manager->mail_already_sent($_GET['oID']) == false)
						{
							echo '<a style="width:140px;float:right;" class="button" href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('ekomi')) . '&ekomi=send_mail') . '">' . BUTTON_EKOMI_SEND_MAIL . '</a>';
						}
						// EOF eKomi

						echo '<span style="width:170px;float:right;" class="GM_SEND_ORDER button" href="' . xtc_href_link('gm_send_order.php', 'oID=' . $_GET['oID'] . '&type=send_order') . '" target="_blank">' . TITLE_SEND_ORDER . '</span>';
						// BEGIN Klarna2
						$is_klarna2 = $klarna instanceof GMKlarna;
						if($is_klarna2) {
							$okdata = $klarna->getOrdersKlarnaData($_GET['oID']);
							$has_klarna2_invoice = !empty($okdata['inv_rno']);
						}
						if(gm_pdf_is_installed()) {
							echo '<a style="width:220px;" class="button float_right" href="' . xtc_href_link('gm_send_order.php', 'oID=' . $_GET['oID'] . '&type=recreate_order') . '" target="_blank">' . TITLE_RECREATE_ORDER . '</a>';
							echo '<a style="width:85px;" class="button float_right" href="' . xtc_href_link('gm_pdf_order.php', 'oID=' . $_GET['oID'] . '&type=packingslip') . '" target="_blank">' . TITLE_PACKINGSLIP	. '</a> ';
							if(!$is_klarna2) {
								echo '<span style="width:110px;float:right" class="GM_INVOICE_MAIL button">' . TITLE_INVOICE_MAIL  . '</span> ';
							}
							if(!$is_klarna2 || $has_klarna2_invoice) {
								echo '<a style="width:85px;" class="button float_right" href="' . xtc_href_link('gm_pdf_order.php', 'oID=' . $_GET['oID'] . '&type=invoice') . '" target="_blank">' . TITLE_INVOICE	. '</a> ';
							}
						}
						// END Klarna2
							//echo '<a class="button float_right" href="' . xtc_href_link('gm_send_order.php', 'oID=' . $_GET['oID'] . '&type=order') . '" target="_blank">' . TITLE_ORDER . '</a>';

						// (xycons.de - Additional Extenders) (START)
						//echo $coo_order_extender_component->get_output('buttons');
						// (xycons.de - Additional Extenders) (END)
						?>

					<?php
						if (ACTIVATE_GIFT_SYSTEM == 'true') {
							echo '<a style="width:110px;" class="button float_right" href="'.xtc_href_link(FILENAME_GV_MAIL, xtc_get_all_get_params(array ('cID', 'action')).'cID='.$order->customer['ID']).'">'.TITLE_GIFT_MAIL.'</a>';
						}
					?>

					<?php
					// mediafinanz
					include_once(DIR_FS_CATALOG . 'includes/modules/mediafinanz/include_orders.php');
					?>
					<br style="clear:right" />
					<br />
					<a style="float:right" class="button" href=<?php echo '"' . xtc_href_link(FILENAME_ORDERS, 'page='.(int)$_GET['page'].'&oID='.$_GET['oID']).'">'.BUTTON_BACK;?></a>
				</td>
			</tr>
		</table>
<?php

} elseif ($_GET['action'] == 'custom_action') {
	echo '<td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">';
	include ('orders_actions.php');
} else {
?>
	<td  class="boxCenter" width="100%" valign="top"><table class="breakpoint-large" border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%">
			<div class="pageHeading" style="float:left; background-image:url(html/assets/images/legacy/gm_icons/kunden.png)">
				<?php echo HEADING_TITLE; ?>
			</div>
			<div class="pageHeading orders_form">
				<?php echo xtc_draw_form('orders', FILENAME_ORDERS, '', 'get'); ?>
				<?php echo HEADING_TITLE_SEARCH . ' ' . xtc_draw_input_field('oID', '', 'size="12"') . xtc_draw_hidden_field('action', 'edit').xtc_draw_hidden_field(xtc_session_name(), xtc_session_id()); ?>
				</form>
				<?php echo xtc_draw_form('status', FILENAME_ORDERS, '', 'get'); ?>
				<?php $GLOBALS['status'] = $_GET['status']; ?>
				<?php echo HEADING_TITLE_STATUS . ' ' . xtc_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $orders_statuses), '', 'onChange="this.form.submit();"').xtc_draw_hidden_field(xtc_session_name(), xtc_session_id()); ?>
				</form>
			</div>
			<br>

			<!-- bof gm -->
			<?php
				if($_GET['action'] != "delete") {
					echo xtc_draw_form('gm_multi_status', FILENAME_ORDERS, xtc_get_all_get_params(array('action')) . 'action=gm_multi_status', 'post');
				}
			?>
			<!-- eof gm -->
        </td>
      </tr>
      <tr>
        <td class="main">

        	<?php if(!empty($_SESSION['messages'][basename(__FILE__)])): ?>
        		<style>
        		div.messages { border: 2px solid red; background: #ffa; padding: 1ex 1em; margin-bottom: 1em; }
        		div.messages p.message { margin: 0; }
        		</style>
        		<div class="messages">
        			<?php
        			foreach($_SESSION['messages'][basename(__FILE__)] as $message)
        			{
        				echo '<p class="message">'.$message.'</p>';
        			}
        			?>
        		</div>
        		<?php $_SESSION['messages'][basename(__FILE__)] = array(); ?>
        	<?php endif ?>

		<!-- bof gm send_order status -->
		<span class="gm_strong">
		<?php
			$gm_send_order_status = array();
			$gm_query = xtc_db_query("
									SELECT
										orders_id
									FROM
										orders
									WHERE
										gm_send_order_status = '0'
									");
			while($row = xtc_db_fetch_array($gm_query)) {
				$gm_send_order_status[] = $row['orders_id'];
			}

			//if(count($gm_send_order_status) == 1) {
			//	echo GM_SEND_ORDER_STATUS_MONO . "<br /><br />";
			//} elseif(count($gm_send_order_status) > 1) {
			//	echo GM_SEND_ORDER_STATUS_STEREO . "<br /><br />";
			//}

		?>
		</span>
		<!-- eof gm send_order status -->
		<?php
		$shipcloud_js = gm_get_conf('MODULE_CENTER_SHIPCLOUD_INSTALLED') == true ? 'orders/orders_shipcloud' : '';
		?>
		<table border="0" width="100%" cellspacing="0" cellpadding="0" data-gx-compatibility="row_selection" data-gx-extension="visibility_switcher" data-visibility_switcher-selections="div.action-list">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0"
                                    class="gx-orders-table gx-compatibility-table" data-gx-compatibility="orders/orders_table_controller <?php echo $shipcloud_js ?>">
              <tr class="dataTableHeadingRow">
                <td style="width: 13px"><input type="checkbox" id="gm_check"></td>
	            <td style="width: 60px" align="left"><?php echo 'Nr'; ?></td>
	            <td style="width: 190px"><?php echo TABLE_HEADING_CUSTOMERS; ?></td>
                <td style="width: 64px" align="left"><?php echo TABLE_HEADING_ORDER_TOTAL; ?></td>
	            <td style="width: 200px" align="left"><?php echo TABLE_HEADING_PAYMENT_METHOD; ?></td>
                <td style="width: 115px" align="left"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></td>
                <td style="width: 120px" align="left"><?php echo TABLE_HEADING_STATUS; ?></td>
	              <?php
	                $orderTableExtender = MainFactory::create('AdminOrderOverviewTableExtenderComponent');
	                echo $orderTableExtender->getRenderedHeadCells();
	              ?>
				<td class="hidden" align="left"><?php echo TABLE_HEADING_WITHDRAWAL; ?></td>
                <?php if (AFTERBUY_ACTIVATED=='true') { ?>
                <td style="width: 120px" align="left"><?php echo TABLE_HEADING_AFTERBUY; ?></td>
                <?php } ?>
                <td style="min-width: 250px" align="right">&nbsp;</td>
              </tr>
<?php

// bof gm
	// prepare GET-data
	if(isset($_GET['gm_status'])) {

		$oID = xtc_db_prepare_input($_GET['oID']);
		$status = xtc_db_prepare_input($_GET['gm_status']);
		$order_updated = false;

		// check status
		$check_status_query = xtc_db_query("select orders_status from ".TABLE_ORDERS." where orders_id = '".xtc_db_input($oID)."'");
		$check_status = xtc_db_fetch_array($check_status_query);

		// proceed
		if ($check_status['orders_status'] != $status || $comments != '') {
			xtc_db_query("update ".TABLE_ORDERS." set orders_status = '".xtc_db_input($status)."', last_modified = now() where orders_id = '".xtc_db_input($oID)."'");
			xtc_db_query("INSERT INTO " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('".xtc_db_input($oID)."', '".xtc_db_input($gm_status)."', now(), '".$customer_notified."', '".xtc_db_input($gm_comments)."')");
			$GLOBALS['order']->info['orders_status'] = $status;
		}
		unset($_GET['gm_status']);
	}
// eof gm

	if ($_GET['cID']) {
		$cID = xtc_db_prepare_input($_GET['cID']);
		$orders_query_raw = "select o.customers_id, customers_email_address, o.orders_id, o.afterbuy_success, o.afterbuy_id, o.customers_name, o.customers_company, o.customers_id, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, o.orders_status, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s, " . TABLE_CUSTOMERS_INFO . " ci where o.customers_id = '".xtc_db_input($cID)."' and (o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and ot.class = 'ot_total') and o.customers_id = ci.customers_info_id and o.date_purchased >= ci.customers_info_date_account_created group by o.orders_id order by date_purchased DESC";
	}
	elseif ($_GET['status'] !== null && $_GET['status'] !== '') {
		$status = xtc_db_prepare_input($_GET['status']);
		$orders_query_raw = "select o.customers_id, customers_email_address, o.orders_id, o.afterbuy_success, o.afterbuy_id, o.customers_name, o.customers_company, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, o.orders_status, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and s.orders_status_id = '".xtc_db_input($status)."' and ot.class = 'ot_total' order by o.date_purchased DESC";
	} else {
		$orders_query_raw = "select o.customers_id, customers_email_address, o.orders_id, o.orders_status, o.afterbuy_success, o.afterbuy_id, o.customers_name, o.customers_company, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and ot.class = 'ot_total' order by o.date_purchased DESC";
	}
	$orders_split = new splitPageResults($_GET['page'], gm_get_conf('NUMBER_OF_ORDERS_PER_PAGE', 'ASSOC', true), $orders_query_raw, $orders_query_numrows);
$orders_query = xtc_db_query($orders_query_raw);

	if(xtc_db_num_rows($orders_query) == 0)
	{
		$gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
		echo '
				<tr class="gx-container no-hover">
					<td colspan="8" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
				</tr>
			';
	}

	//bof gm
	while ($orders = xtc_db_fetch_array($orders_query)) {
		$t_orders_hash_query = 'SELECT orders_hash FROM ' . TABLE_ORDERS . ' WHERE orders_id = ' . $orders['orders_id'];
		$t_orders_hash_result = xtc_db_query($t_orders_hash_query);
		if(xtc_db_num_rows($t_orders_hash_result) == 1)
		{
			$t_row = xtc_db_fetch_array($t_orders_hash_result);
			$orders['orders_hash'] = $t_row['orders_hash'];
		}
		$t_withdrawal_query = 'SELECT withdrawal_id FROM withdrawals WHERE order_id = ' . $orders['orders_id'] . ' ORDER BY withdrawal_id';
		$t_withdrawal_result = xtc_db_query($t_withdrawal_query);

		if (((!$_GET['oID']) || ($_GET['oID'] == $orders['orders_id'])) && (!$oInfo)) {
			$oInfo = new objectInfo($orders);
		}

		// row is selected
		if ((is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id)) {
			$gm_tr_class	= "dataTableRowSelected";
			$gm_td_action	= 'onclick="document.location.href=\''.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=edit').'\'"';

		// row is not selected
		} else {
			$gm_tr_class	= "dataTableRow";
			$gm_td_action	= 'onclick="document.location.href=\''.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID')).'oID='.$orders['orders_id']).'\'"';
		}
/*
			echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\''.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=edit').'\'">'."\n";
		} else {
			echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\''.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID')).'oID='.$orders['orders_id']).'\'">'."\n";
		}

*/

		include_once(DIR_FS_INC.'get_payment_title.inc.php');
?>
		<tr class="visibility_switcher row_selection <?php echo $gm_tr_class; ?>" data-row-id="<?php echo $orders['orders_id']; ?>"
				<?php if(in_array($orders['orders_id'], $gm_send_order_status)) {echo ' '; }?>>
			<td class="dataTableContent"><input type="checkbox" class="checkbox" value="<?php echo $orders['orders_id']; ?>" name="gm_multi_status[]"></td>
			<td class="dataTableContent numeric_cell" <?php echo $gm_td_action; ?> align="left"><?php echo '<a data-gx-controller="orders/orders_tooltip" data-order_tooltip-url="admin.php?do=OrderTooltip&orderId=' . $orders['orders_id'] . '" href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit') . '" ' . (in_array($orders['orders_id'], $gm_send_order_status) ? ' ' : '' ) . '>' . $orders['orders_id'] . '</a>'; ?></td>
			<td class="dataTableContent" <?php echo $gm_td_action;
			?>><?php
				$customerName = trim($orders['customers_name']);

				if($customerName === '') {
					$customerName = $orders['customers_company'];
				}

				if(!empty($orders['customers_id']))
				{
					echo '<a data-customer-id=' . $orders['customers_id'] . ' href="' . xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params(array ('cID', 'action')).'cID='. $orders['customers_id'] .'&action=edit')  . '" ' . (in_array($orders['orders_id'], $gm_send_order_status) ? '  ' : '' ) . '>' . $customerName . '</a>&nbsp;';
				}
				else
				{
					echo '<span data-customer-id=' . $orders['customers_id'] . '>' . $customerName . '</span>&nbsp;';
				}
				?>
			</td>
			<td class="dataTableContent numeric_cell" <?php echo $gm_td_action; ?> align="left"><?php echo strip_tags($orders['order_total']); ?></td>
			<td class="dataTableContent" <?php echo $gm_td_action; ?> align="left" title="<?php echo $orders['payment_method']; ?>"><?php if(!empty($orders['payment_method'])){ echo get_payment_title($orders['payment_method']); } ?></td>
			<td class="dataTableContent" <?php echo $gm_td_action; ?> align="left"><?php echo xtc_datetime_short($orders['date_purchased']); ?></td>
			<td class="dataTableContent nowrap" <?php echo $gm_td_action; ?> align="left">
				<?php
				echo '<span class="'. getBadgeClass($orders['orders_status']) .'">' . $orders['orders_status_name'] . '</span>';
				if(xtc_db_num_rows($t_withdrawal_result) > 0)
				{
					$t_withdrawal_id_array = array();
					while($t_withdrawal_row = xtc_db_fetch_array($t_withdrawal_result))
					{
						$t_withdrawal_id_array[] = '<a class="order-status-icon" title="' . TABLE_HEADING_WITHDRAWAL_ID . ' ' . $t_withdrawal_row['withdrawal_id'] . '" href="' . xtc_href_link('withdrawals.php', 'id=' . $t_withdrawal_row['withdrawal_id'] . '&action=edit') . '"><img src="html/assets/images/legacy/icons/withdrawal-on.png" border="0" /></a>';
					}
					echo implode(' ', $t_withdrawal_id_array);
				}

				if(in_array($orders['orders_id'], $gm_send_order_status))
				{
					echo '<i class="fa fa-envelope-o order-status-icon" title="' . TEXT_CONFIRMATION_NOT_SENT . '"></i>';
				}
				?>
			</td>

			<?php
				$orderId = MainFactory::create('IdType', (int)$orders['orders_id']);
				$orderTableExtender->setOrderId($orderId);
				$orderTableExtender->proceed();
				echo $orderTableExtender->getRenderedContentsCells($orderId);
			?>

			<?php
				/*
					-> afterbuy
				*/
				if (AFTERBUY_ACTIVATED=='true') {
			?>
				<td class="dataTableContent" align="right">
					<?php
						if ($orders['afterbuy_success'] == 1) {
							echo $orders['afterbuy_id'];
						} else {
							echo 'TRANSMISSION_ERROR';
						}
					?>
				</td>
				<?php } ?>

				<td class="dataTableContent gx-container" align="left">
					<div class="action-list pull-right" data-gx-extension="toolbar_icons">
						<a class="action-icon btn-view" title="<?php echo BUTTON_DETAILS ?>"
						   href="<?php echo xtc_href_link('orders.php?oID=' . $orders['orders_id'] . '&action=edit'); ?>"></a>
						<a class="action-icon btn-delete"
						   href="#"
						   title="<?php echo BUTTON_DELETE ?>"
						   data-gx-compatibility="orders/orders_modal_layer"
						   data-orders_modal_layer-action="delete"
						   data-order_id="<?php echo $orders['orders_id'] ?>"></a>
						<!-- ROW ACTIONS - BUTTON DROPDOWN WIDGET -->
						<div data-use-button_dropdown="true"
						     data-config_key="orderOverviewDropdownBtn"
							 class="single-order-dropdown">
							<button></button>
							<ul></ul>
						</div>
					</div>
				</td>
			</tr>
<?php

	} // -> close while

//eof gm
?>
            </table>
		</td>
<?php

	$heading = array ();
	$contents = array ();
	switch ($_GET['action']) {
		case 'delete' :
			$heading[] = array ('text' => '<b>'.TEXT_INFO_HEADING_DELETE_ORDER.'</b>');

			$contents = array ('form' => xtc_draw_form('orders', FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=deleteconfirm'));
			$contents[] = array ('text' => TEXT_INFO_DELETE_INTRO.'<br /><br /><b>'.$cInfo->customers_firstname.' '.$cInfo->customers_lastname.'</b>');


			if($oInfo->orders_status != gm_get_conf('GM_ORDER_STATUS_CANCEL_ID')) {
				// BOF GM_MOD
				$t_gm_restock_checked = true;
				if(STOCK_LIMITED == 'false')
				{
					$t_gm_restock_checked = false;
				}
				$contents[] = array ('text' => '<br />'.xtc_draw_checkbox_field('restock', '', $t_gm_restock_checked).' '.TEXT_INFO_RESTOCK_PRODUCT_QUANTITY);
				// BOF GM_MOD products_shippingtime:
				$auto_shipping_status = gm_get_conf('GM_AUTO_SHIPPING_STATUS');
		        if($auto_shipping_status == 'true' && ACTIVATE_SHIPPING_STATUS == 'true' && STOCK_LIMITED == 'true') {
					$contents[] = array ('text' => xtc_draw_checkbox_field('reshipp', '', true).' '.TEXT_INFO_RESHIPP);
				}
                $contents[] = array ('text' => xtc_draw_checkbox_field('reactivateArticle', '', false).' '.TEXT_INFO_REACTIVATEARTICLE);
				// BOF GM_MOD products_shippingtime:
				// EOF GM_MOD
			}

			$contents[] = array ('text' => xtc_draw_hidden_field('page_token', $t_page_token));
			$contents[] = array ('align' => 'center', 'text' => '<div align="center"><input type="submit" class="button" value="'. BUTTON_DELETE .'"></div>');
			$contents[] = array ('align' => 'center', 'text' => '<div align="center"><a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id).'">' . BUTTON_CANCEL . '</a></div>');
			$contents[] = array ('text' => '</form><br />');
			break;

		default:
			if (is_object($oInfo)) {

				$heading[] = array ('text' => '<b>['.$oInfo->orders_id.']&nbsp;&nbsp;'.xtc_datetime_short($oInfo->date_purchased).'</b>');
				$contents[] = array ('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold; ">' . TEXT_MARKED_ELEMENTS . '</div><br />');
				$contents[] = array ('align' => 'left', 'text' => '<div align="center"><a class="button btn-details" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=edit').'">'.BUTTON_DETAILS.'</a></div>');
				$contents[] = array ('align' => 'left', 'text' => '<div align="center"><a class="button btn-delete"  data-gx-compatibility="orders/orders_modal_layer" data-orders_modal_layer-action="delete" data-order_id="' . $oInfo->orders_id . '" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=delete').'">'.BUTTON_DELETE.'</a></div>');
				$contents[] = array ('align' => 'left', 'text' => '<div align="center"><a class="button btn-multi_delete"  data-gx-compatibility="orders/orders_modal_layer" data-orders_modal_layer-action="multi_delete" data-order_id="' . $oInfo->orders_id . '" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=delete').'">'.BUTTON_DELETE.'</a></div>');
				$contents[] = array ('align' => 'left', 'text' => '<div align="center"><a class="button btn-multi_cancel"  data-gx-compatibility="orders/orders_modal_layer" data-orders_modal_layer-action="multi_cancel" data-order_id="' . $oInfo->orders_id . '" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=delete').'">'.BUTTON_GM_CANCEL.'</a></div>');

				// bof
				$contents[] = array ('align' => 'left', 'text' => '<div align="center"><input type="hidden" value="' . $oInfo->orders_id .'" id="gm_order_id"><span class="GM_CANCEL button">'.BUTTON_GM_CANCEL.'</span></div>');
				// eof gm

				require_once(DIR_FS_CATALOG.'callback/sofort/ressources/scripts/adminOrdersMenu.php');

				// bof gm
				if(gm_pdf_is_installed()) {
					// BEGIN Klarna2
					$is_klarna2 = $oInfo->payment_method == 'klarna2_invoice' || $oInfo->payment_method == 'klarna2_partpay';
					if($is_klarna2) {
						$klarna = new GMKlarna();
						$okdata = $klarna->getOrdersKlarnaData($_GET['oID']);
						$has_klarna2_invoice = !empty($okdata['inv_rno']);
					}
					else {
						$has_klarna2_invoice = false;
					}
					if(!$is_klarna2) {
						$contents[] = array ('align' => 'left', 'text' => '<div align="center"><input type="hidden" value="' . $oInfo->orders_id .'" id="gm_order_id"><a class="button btn-invoice" href="' . xtc_href_link('gm_pdf_order.php', 'oID=' . $oInfo->orders_id . '&type=invoice') . '" target="_blank">' . TITLE_INVOICE . '</a></div>');
					}
					else {
						if($has_klarna2_invoice)
						{
							$contents[] = array ('align' => 'left', 'text' => '<div align="center"><input type="hidden" value="' . $oInfo->orders_id .'" id="gm_order_id"><a class="button btn-invoice" href="'.$klarna->getInvoicePDFURL($okdata['inv_rno']).'" target="_blank">Klarna '. TITLE_INVOICE .'</a></div>');
						}
						else
						{
							$contents[] = array ('align' => 'left', 'text' => '<div align="center"><input type="hidden" value="' . $oInfo->orders_id .'" id="gm_order_id"></div>');
						}
					}
					if(!$is_klarna2) {
						$contents[] = array ('align' => 'left', 'text' => '<div align="center"><span class="GM_INVOICE_MAIL button">' . TITLE_INVOICE_MAIL  . '</div></span>');
					}
					// END Klarna2
					$contents[] = array ('align' => 'left', 'text' => '<div align="center"><a class="button btn-packing_slip" href="' . xtc_href_link('gm_pdf_order.php', 'oID=' . $oInfo->orders_id . '&type=packingslip')	. '" target="_blank">' . TITLE_PACKINGSLIP . '</a></div>');
				}
				// eof gm
				$contents[] = array ('align' => 'left', 'text' => '<div align="center"><a class="button btn-order_confirmation" href="' . xtc_href_link('gm_send_order.php', 'oID=' . $oInfo->orders_id . '&type=order') . '" target="_blank">' . TITLE_ORDER . '</a></div>');

				//BOF GM ORDER RECREATE
				$contents[] = array ('align' => 'left', 'text' => '<div align="center"><a class="button btn-recreate_order_confirmation" href="' . xtc_href_link('gm_send_order.php', 'oID=' . $oInfo->orders_id . '&type=recreate_order') . '" target="_blank">' . TITLE_RECREATE_ORDER . '</a></div>');
				//EOF GM ORDER RECREATE

				$contents[] = array ('align' => 'left', 'text' => '<div align="center"><span class="GM_SEND_ORDER button" href="' . xtc_href_link('gm_send_order.php', 'oID=' . $oInfo->orders_id . '&type=send_order') . '" target="_blank">' . TITLE_SEND_ORDER . '</span></div>');

				$t_orders_hash_string = '';
				$t_orders_id_string = '';
				if(isset($oInfo->orders_hash) && empty($oInfo->orders_hash) == false)
				{
					$t_orders_hash_string = 'order=' . $oInfo->orders_hash;
				}
				else
				{
					$t_orders_id_string = 'order_id=' . xtc_db_input($oInfo->orders_id);
				}

				$contents[] = array ('align' => 'left', 'text' => '<div align="center"><a class="button btn-create_withdrawal" href="' . xtc_catalog_href_link('withdrawal.php', $t_orders_hash_string . $t_orders_id_string, 'SSL') . '" target="_blank">' . TEXT_CREATE_WITHDRAWAL . '</a></div>');
				//$gm_quick_status = '<form method="get" action="'.FILENAME_ORDERS.'" ' . xtc_draw_pull_down_menu('gm_status', array_merge(array(array('id' => '', 'text' => TEXT_GM_STATUS)),array(array('id' => '0', 'text' => TEXT_VALIDATING)), $orders_statuses), '', 'onChange="this.form.submit();"').xtc_draw_hidden_field('oID', $oInfo->orders_id) . xtc_draw_hidden_field(xtc_session_name(), xtc_session_id()) . '</form>';

				$contents[] = array('align' => 'left', 'text' => '<div align="center"><a class="button btn-add_tracking_code" data-gx-compatibility="orders/orders_modal_layer" data-orders_modal_layer-action="add_tracking_code" data-order_id="' . $oInfo->orders_id . '" href="#">' . TXT_PARCEL_TRACKING_SENDBUTTON_TITLE . '</a>');
				$contents[] = array('align' => 'left', 'text' => '<div align="center"><a class="button btn-update_order_status" data-gx-compatibility="orders/orders_modal_layer" data-orders_modal_layer-action="update_orders_status" data-order_id="' . $oInfo->orders_id . '"href="#">Bestellstatus ändern</a>');

				// BEGIN Hermes
				if($hermes->getUsername() != '' || $hermes->getService() == 'PriPS')
				{
					$contents[] = array ('align' => 'left', 'text' => '<div align="center"><a class="button btn-hermes" href="' . xtc_href_link('hermes_order.php', 'orders_id=' . $oInfo->orders_id) . '">Hermes Versand</a></div>');
				}
				// END Hermes

				// begin intraship
				if(file_exists(DIR_FS_CATALOG.'gm/classes/GMIntraship.php'))
				{
					$intraship = new GMIntraship();
					if($intraship->active == true)
					{
						$contents[] = array ('align' => 'left', 'text' => '<div align="center"><a class="btton btn-dhl_label" href="'.xtc_href_link('print_intraship_label.php','oID='.$oInfo->orders_id).'">DHL Label</a></div>');
					}
				}
				// end intraship

				$contents[] = array ('text' => '<div class="extended_single_actions">' . $adminOrderOverviewExtender->get_output('single_action') . '</div>');
				$contents[] = array ('text' => '<div class="extended_multi_actions">' . $adminOrderOverviewExtender->get_output('multi_action') . '</div>');

				$contents[] = array ('text' => '<br />'.TEXT_DATE_ORDER_CREATED.' '.xtc_date_short($oInfo->date_purchased));
				if (xtc_not_null($oInfo->last_modified))
					$contents[] = array ('text' => TEXT_DATE_ORDER_LAST_MODIFIED.' '.xtc_date_short($oInfo->last_modified));
				$contents[] = array ('text' => '<br />'.TEXT_INFO_PAYMENT_METHOD.' '.$oInfo->payment_method);

				// elari added to display product list for selected order
				$order = new order($oInfo->orders_id);
				$contents[] = array ('text' => '<br /><br />'.sizeof($order->products).' '.GM_PRODUCTS); // BOF GM_MOD EOF
				for ($i = 0; $i < sizeof($order->products); $i ++) {
					$contents[] = array ('text' => gm_prepare_number($order->products[$i]['qty']).'&nbsp;' . ((!empty($order->products[$i]['quantity_unit_id'])) ? $order->products[$i]['unit_name'] : 'x') . '&nbsp;'.$order->products[$i]['name']); // BOF GM_MOD EOF

					if (sizeof($order->products[$i]['attributes']) > 0) {
						for ($j = 0; $j < sizeof($order->products[$i]['attributes']); $j ++) {
							$contents[] = array ('text' => '<small>&nbsp;<i> - '.$order->products[$i]['attributes'][$j]['option'].': '.$order->products[$i]['attributes'][$j]['value'].'</i></small></nobr>');
						}
						// BOF GM_MOD GX-Customizer:
						include(DIR_FS_CATALOG . 'gm/modules/gm_gprint_admin_orders_2.php');
					}

					# properties BOF
					if (sizeof($order->products[$i]['properties']) > 0) {
						for ($j = 0, $k = sizeof($order->products[$i]['properties']); $j < $k; $j ++) {
							if(!empty($order->products[$i]['properties'][$j]['properties_name']) || !empty($order->products[$i]['properties'][$j]['values_name']))
							{
								$contents[] = array ('text' => '<small>&nbsp;<i> - '.$order->products[$i]['properties'][$j]['properties_name'].': '.$order->products[$i]['properties'][$j]['values_name'].'</i></small></nobr>');
							}
						}
					}
					# properties EOF

				}
				// elari End add display products
				$contents[] = array ('text' => '<br />'); // BOF GM_MOD EOF
			}

			// bof gm
			$gm_heading_multi_status[]		= array ('text' => '<b>'.HEADING_GM_STATUS.'</b>');
			$content_multi_order_status[]	= array ('text' => xtc_draw_hidden_field(xtc_session_name(), xtc_session_id()));
			$content_multi_order_status[]	= array ('text' => xtc_draw_hidden_field('action', 'gm_multi_status').xtc_draw_hidden_field('page', (int)$_GET['page']));
			$content_multi_order_status[]	= array ('text' => xtc_draw_pull_down_menu('gm_status', array_merge(array(array('id' => '', 'text' => TEXT_GM_STATUS)),array(array('id' => '0', 'text' => TEXT_VALIDATING)), $change_orders_status)));
			/* magnalister v2.0.0 */
			if (function_exists('magnaExecute')) magnaExecute('magnaRenderOrderStatusSync', array('multi' => true), array('order_details.php'));
			/* END magnalister */
			$content_multi_order_status[]	= array ('text' => xtc_draw_checkbox_field('gm_notify', 'on')			. ENTRY_NOTIFY_CUSTOMER);
			$content_multi_order_status[]	= array ('text' => xtc_draw_checkbox_field('send_parcel_tracking_codes', 'on') . ENTRY_SEND_PARCEL_TRACKING_CODES);
			$content_multi_order_status[]	= array ('text' => xtc_draw_checkbox_field('gm_notify_comments', 'on')	. ENTRY_NOTIFY_COMMENTS);
			$content_multi_order_status[]	= array ('text' => TABLE_HEADING_COMMENTS.'<br>'.xtc_draw_textarea_field('gm_comments', '', 24, 5, $_GET['comments'],'',false).'<br>');
			$content_multi_order_status[]	= array ('align' => 'left', 'text' => '<div align="center"><input type="submit" class="button" value="'. BUTTON_CONFIRM .'"></form></div>');
			$content_multi_order_status[]	= array ('align' => 'left', 'text' => '<br />');
			// eof gm
			break;
	}

	if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
		echo '            <td width="25%" valign="top" id="gm_orders">'."\n";

		$box = new box;
		echo $box->infoBox($heading, $contents, array(), array('action_buttons'));

		/* START Parcel Tracking Module  */

		echo '<br /><div class="parcel_tracking_code_sidebox" data-gx-widget="lightbox">';

		/** @var LanguageTextManager $langFileMaster */
		$langFileMaster = MainFactory::create_object('LanguageTextManager',
													 array('parcel_services',
														   $lang
													 ), true);

		$box = new box;
		$t_heading_parcel_array[] = array('text' =>
											  '<b>' . sprintf($langFileMaster->get_text('TXT_PARCEL_TRACKING_HEADING'), $oInfo->orders_id) .
											  '</b>'
		);

		/** @var ParcelTrackingCode $coo_parcel_tracking_code_item */
		$coo_parcel_tracking_code_item = MainFactory::create_object('ParcelTrackingCode');


		/** @var ParcelTrackingCodeReader $coo_parcel_tracking_code_reader */
		$coo_parcel_tracking_code_reader = MainFactory::create_object('ParcelTrackingCodeReader');

		$t_parcel_tracking_codes_array = $coo_parcel_tracking_code_reader->getTackingCodeItemsByOrderId($coo_parcel_tracking_code_item,
																								$oInfo->orders_id);

		$t_parcel_traking_element = '<tr class="">
										<td class="infoBoxContent"><a href="%s" target="_blank">%s</a> (%s)</td>
										<td>
											<img src="images/buttons/button_cancel_red_cross.png" alt="' . htmlspecialchars_wrapper($langFileMaster->get_text('TXT_PARCEL_TRACKING_DELETE_BUTTON')) . '" title="' . htmlspecialchars_wrapper($langFileMaster->get_text('TXT_PARCEL_TRACKING_DELETE_BUTTON')) . '" data-lightbox-href="lightbox_confirm.html?buttons=cancel-delete&amp;section=parcel_services&amp;message=TXT_PARCEL_TRACKING_DELETE_CONFIRM" data-lightbox-param_order_id="%s" data-lightbox-param_tracking_code_id="%s" data-lightbox-param_page_token="%s" data-lightbox-setting_lightbox_width="460px" data-lightbox-controller="tracking/delete_tracking_code" style="cursor: pointer" class="parcel_tracking_delete_button open_lightbox" data-lightbox-param_reload="true"/>
										</td>
									  </tr>';

		$t_parcel_traking_element_output = '';


		/** @var ParcelTrackingCode $parcel_tracking_code */
		foreach($t_parcel_tracking_codes_array as $parcel_tracking_code)
		{
			$t_parcel_traking_element_output .= sprintf(
				$t_parcel_traking_element,
				htmlspecialchars_wrapper($parcel_tracking_code->getServiceUrl()),
				htmlspecialchars_wrapper($parcel_tracking_code->getTrackingCode()),
				htmlspecialchars_wrapper($parcel_tracking_code->getServiceName()),
				htmlspecialchars_wrapper($parcel_tracking_code->getTrackingCode()),
				htmlspecialchars_wrapper($parcel_tracking_code->getTrackingCode()),
				$parcel_tracking_code->getOrderId(),
				$parcel_tracking_code->getTrackingCodeId(),
				$t_page_token
				);
		}

		$t_parcel_traking_element_output =
			'<table class="contentTable" border="0" width="100%" cellspacing="0" cellpadding="0">' .
			$t_parcel_traking_element_output . '</table>';


		/* parcel tracking add new tracking code */


		$t_tracking_form_html_template = '

			<p><b>%s</b></p>

			<div align="center">
			<input type="text" name="parcel_service_tracking_code"/>
				%s
				<input type="button" data-order_id=' . $oInfo->orders_id . ' class="button add_tracking_code" value="%s" title="%s"/>
			</div>
		<br />

';


		/* Build Service-Dropdown */
		$t_options_html_element = '';

		/** @var ParcelServiceReader $parcelServiceReadService */
		$parcelServiceReadService = MainFactory::create_object('ParcelServiceReader');
		$t_all_parcel_services_array = $parcelServiceReadService->getAllParcelServices();

		$t_options_html_element .= '
	<select name="parcel_service">
		';

		$t_parcel_options = '';
		/** @var ParcelTrackingCode $parcel_service */
		foreach($t_all_parcel_services_array as $parcel_service)
		{
			$t_parcel_service_selected = '';

			if($parcel_service->getDefault())
			{
				$t_parcel_service_selected = ' selected="selected"';
			}

			$t_parcel_options .= sprintf('<option value="%s"%s>%s</option>', $parcel_service->getId(), $t_parcel_service_selected, htmlspecialchars_wrapper($parcel_service->getName()));
		}

		$t_options_html_element .= $t_parcel_options;
		$t_options_html_element .= '</select>';

		$t_parcel_traking_element_output .= sprintf($t_tracking_form_html_template, $langFileMaster->get_text('TXT_PARCEL_TRACKING_FORMHEADING'),
													$t_options_html_element, $langFileMaster->get_text('TXT_PARCEL_TRACKING_SENDBUTTON'),
													$langFileMaster->get_text('TXT_PARCEL_TRACKING_SENDBUTTON_TITLE'));


		$t_contents_parcel_array[] = array('text' => $t_parcel_traking_element_output);
		echo $box->infoBox($t_heading_parcel_array, $t_contents_parcel_array);

		echo '<input class="page_token" type="hidden" name="page_token" value="' . $t_page_token . '" />';

		echo '</div><br />';

		/* END Parcel Tracking Module  */

		$box = new box;
		echo $box->infoBox($gm_heading_multi_status, $content_multi_order_status);

		$multi_action_block = '';
		//$multi_action_block .= FOR_SELECTED_ORDERS.'<br>';

		// BEGIN ILOXX
		$iloxx = new GMIloxx();
		if(!empty($iloxx->userid) && gm_get_conf('MODULE_CENTER_ILOXX_INSTALLED') === '1')
		{
			$iloxx_trackpopurl = $iloxx->getTrackPopUrl((int)$_GET['oID']);
			$iloxx_block .= '<div align="center" class="multi_action_block">';
			$iloxx_block .= '<strong>MyDPD Business/iloxx</strong><br>';
			$iloxx_block .= '<form class="multi_action" action="orders_iloxx.php" method="post" id="iloxx_form">';
			$iloxx_block .= '<input type="hidden" name="return_uri" value="'.$_SERVER['REQUEST_URI'] .'">';
			$iloxx_block .= '<input type="hidden" name="cmd" value="select_orders">';
			$iloxx_block .= '<input type="submit" style="width: auto;" class="button" id="iloxx_orders" value="'.$iloxx->get_text('get_labels').'">';
			$iloxx_block .= '</form>';
			if($iloxx_trackpopurl !== false) {
				$iloxx_block .= '<br>'.$iloxx->get_text('for_selected_order').':<br><a target="_new" href="'.$iloxx_trackpopurl.'" class="button">'.$iloxx->get_text('tracking').'</a>';
			}
			$iloxx_block .= '</div>';
			$multi_action_block .= $iloxx_block;
		}
		// END ILOXX

		// BEGIN KLARNA
		$klarna = new GMKlarna();
		if($klarna->isConfigured())
		{
			$klarna_block = '<div align="center" class="multi_action_block">';
			$klarna_block .= '<strong>Klarna</strong>';
			$klarna_block .= '<form class="multi_action" action="'.xtc_href_link('request_port.php?module=KlarnaMultiAction').'" method="post" id="klarna_form">';
			$klarna_block .= '<input type="hidden" name="multi_action_module" value="klarna">';
			$klarna_block .= '<input type="hidden" name="orders_params" value="'.base64_encode(xtc_get_all_get_params()).'">';
			$klarna_block .= '<input type="submit" style="width: auto;" class="button" name="klarna_activate_reservation" value="'.$klarna->get_text('multi_action_activate_reservation').'">';
			$klarna_block .= '</form>';
			$klarna_block .= '</div>';
			$multi_action_block .= $klarna_block;
		}
		// END KLARNA

		if(!empty($multi_action_block))
		{
			$multi_action_block = '<style> div.multi_action_block { margin: 1ex 10px 1ex 0; padding: 1ex 0; background: rgba(0, 0, 0, 0.1); } </style>'.$multi_action_block;
			$multi_action_heading[] = array('text' => ACTIONS_FOR_SELECTED_ORDERS);
			$multi_action_content[] = array(
				array('text' => $multi_action_block),
			);
			$box = new box();
			echo '<br>'.$box->infoBox($multi_action_heading, $multi_action_content);
			?>
			<script>
				$(function() {
					$('form.multi_action').submit(function(e) {
						if($('input[name="gm_multi_status[]"]:checked').length == 0)
						{
							alert('<?php echo NO_ORDER_SELECTED; ?>');
							return false;
						}
						var checked_ids = '';
						var the_form = $(this);
						$('input[name="gm_multi_status[]"]:checked').each(function() {
							checked_ids += $(this).val() + '_';
							the_form.append($('<input type="hidden" name="checked_orders_ids[]" value="'+$(this).val()+'">'));
						});
						$(this).append($('<input type="hidden" name="checked_ids" value="'+checked_ids+'">'));
					});
				});
			</script>
			<?php
		}

		echo '            </td>'."\n";
	}
?>
          </tr>
        </table>
		<!-- bof gambio -->
		<table border="0" cellpadding="0">
			<tr>
				<td valign="middle" align="left">
					<div id="orders-table-dropdown"
					     data-use-button_dropdown="true"
					     data-config_key="orderMultiDropdownBtn"
					     data-icon="check-square-o fa-fw"
					     class="remove-margin js-bottom-dropdown">
						<button></button>
						<ul></ul>
					</div>
				</td>
				<td class="pagination-control">
                    <form name="broken-html-fix--do-not-remove" class="hidden"></form>
                    <form name="status_filter" action="<?php echo xtc_href_link(FILENAME_ORDERS); ?>" method="get">
                        <?php
                        foreach($_GET as $getKey => $getValue)
                        {
                            if(is_string($getValue) && $getKey !== 'status')
                            {
                                echo xtc_draw_hidden_field($getKey, $getValue);
                            }
                        }

                        echo HEADING_TITLE_STATUS . ' ' . xtc_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $orders_statuses), '', 'onchange="document.status_filter.submit();"');
                        ?>
                    </form>
                    <form class="control-element" name="number_of_orders_per_page_form" action="<?php echo xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params()); ?>" method="post">
                        <?php
                        $t_values_array = array();
                        $t_values_array[] = array('id' => 20, 'text' => '20 ' . PER_PAGE);
                        $t_values_array[] = array('id' => 30, 'text' => '30 ' . PER_PAGE);
                        $t_values_array[] = array('id' => 50, 'text' => '50 ' . PER_PAGE);
                        $t_values_array[] = array('id' => 100, 'text' => '100 ' . PER_PAGE);
                        echo xtc_draw_pull_down_menu('number_of_orders_per_page', $t_values_array, gm_get_conf('NUMBER_OF_ORDERS_PER_PAGE'), 'class="number_of_orders_per_page" onchange="document.number_of_orders_per_page_form.submit()"');
                        ?>
                    </form>
					<?php echo $orders_split->display_count($orders_query_numrows, gm_get_conf('NUMBER_OF_ORDERS_PER_PAGE'), (int)$_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?>
					<span class="page-number-information">
						<?php echo $orders_split->display_links($orders_query_numrows, gm_get_conf('NUMBER_OF_ORDERS_PER_PAGE'), MAX_DISPLAY_PAGE_LINKS, (int)$_GET['page'], xtc_get_all_get_params(array('page', 'oID', 'action'))); ?>
					</span>
				</td>
			</tr>
		</table>
		<!-- eof gambio -->
	</td>
</tr>
<?php

}
?>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php

require (DIR_WS_INCLUDES.'footer.php');
?>
<!-- footer_eof //-->
<br />
<div id="GM_CANCEL_BOX"></div>
<div id="GM_ORDERS_MAIL_BOX"></div>
<div id="GM_INVOICE_MAIL_BOX"></div>
<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/hoverIntent/hoverIntent.js"></script>
<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/gm_orders.js"></script>
<?php
$coo_js_options_control = MainFactory::create_object('JSOptionsControl', array(false));
$t_js_options_array =  $coo_js_options_control->get_options_array($_GET);
?>
<script type="text/javascript"> var js_options = <?php echo json_encode($t_js_options_array) ?>; </script>
<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/lightbox_plugin.js"></script>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
