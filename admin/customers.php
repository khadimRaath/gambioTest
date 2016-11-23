<?php
/* --------------------------------------------------------------
   customers.php 2016-10-19
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
   (c) 2002-2003 osCommerce(customers.php,v 1.76 2003/05/04); www.oscommerce.com
   (c) 2003	 nextcommerce (customers.php,v 1.22 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: customers.php 1296 2005-10-08 17:52:26Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require ('includes/application_top.php');

// Include JS Language Vars
if(!isset($jsEngineLanguage))
{
	$jsEngineLanguage = array();
}
$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
$jsEngineLanguage['admin_buttons'] = $languageTextManager->get_section_array('admin_buttons');
$jsEngineLanguage['gm_general'] = $languageTextManager->get_section_array('gm_general');
$jsEngineLanguage['admin_customers'] = $languageTextManager->get_section_array('admin_customers');


// filter search-parameter for security reasons
if(isset($_GET['search']))
{
	$_GET['search'] = htmlspecialchars_wrapper($_GET['search']);
}

$t_page_token = $_SESSION['coo_page_token']->generate_token();

require_once (DIR_FS_INC.'xtc_validate_vatid_status.inc.php');
require_once (DIR_FS_INC.'xtc_get_geo_zone_code.inc.php');
require_once (DIR_FS_INC.'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC.'xtc_js_lang.php');

// BOF GM_MOD
require_once(DIR_FS_INC.'xtc_random_charcode.inc.php');
require_once(DIR_WS_CLASSES . 'currencies.php');
// EOF GM_MOD

/**
 * Get Caret
 *
 * Returns information about the provided element.
 *
 * @param string $elementName Has to be equal to the $_GET['sorting'] value.
 *                            E.g. 'price'
 *
 * @return array Information about the element. E.g. Is the page
 *               sorted after the current element? Which is the current
 *               sorting direction? (ascending or descending).
 */
function _getCaret($elementName)
{
	$caretInformation = array();
	$caretInformation['activeCaret'] = 'false';
	$caretInformation['sortingDirection'] = 'asc';

	// By default the table is sorted by the creation date (desc)
	if($elementName === 'date_account_created' && !isset($_GET['sorting']))
	{
		$caretInformation['sortingDirection'] = 'desc';
		$caretInformation['activeCaret'] = 'true';
	}
	else if($_GET['sorting'] === $elementName . '-desc')
	{
		$caretInformation['sortingDirection'] = 'desc';
		$caretInformation['activeCaret'] = 'true';
	}
	else if($_GET['sorting'] === $elementName)
	{
		$caretInformation['activeCaret'] = 'true';
	}

	return $caretInformation;
}

// save number of customers per page
if(isset($_POST['number_of_customers_per_page']) && is_numeric($_POST['number_of_customers_per_page']) && $_POST['number_of_customers_per_page'] > 0)
{
    gm_set_conf('NUMBER_OF_CUSTOMERS_PER_PAGE', $_POST['number_of_customers_per_page']);
}

$customers_statuses_array = xtc_get_customers_statuses();

if ($_GET['special'] == 'remove_memo') {
	$mID = xtc_db_prepare_input($_GET['mID']);
	xtc_db_query("DELETE FROM ".TABLE_CUSTOMERS_MEMO." WHERE memo_id = '".$mID."'");
	xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, 'cID='.(int) $_GET['cID'].'&action=edit'));
}

if ($_GET['action'] == 'edit' || $_GET['action'] == 'update') {
	if ($_GET['cID'] == 1 && $_SESSION['customer_id'] == 1) {
	} else {
		if ($_GET['cID'] != 1) {
		} else {
			xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, ''));
		}
	}
}

if ($_GET['action']) {
	switch ($_GET['action']) {
		case 'newMemo':
			$memo_title = xtc_db_prepare_input($_POST['memo_title']);
			$memo_text  = xtc_db_prepare_input($_POST['memo_text']);
			if($memo_text !== '' && $memo_title !== '')
			{
				$sql_data_array = array(
					'customers_id' => xtc_db_input($_GET['cID']),
					'memo_date'    => date("Y-m-d"),
					'memo_title'   => $memo_title,
					'memo_text'    => $memo_text,
					'poster_id'    => $_SESSION['customer_id']
				);
				xtc_db_perform(TABLE_CUSTOMERS_MEMO, $sql_data_array);
			}
			xtc_redirect(xtc_href_link('customers.php', 'cID=' . (int)$_GET['cID'] . '&action=edit'));
			breaK;
		case 'new_order' :

			$customers1_query = xtc_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".xtc_db_input($_GET['cID'])."'");
			$customers1 = xtc_db_fetch_array($customers1_query);

			$customers_query = xtc_db_query("select * from ".TABLE_ADDRESS_BOOK." where customers_id = '".xtc_db_input($_GET['cID'])."'");
			$customers = xtc_db_fetch_array($customers_query);

			// BOF GM_MOD:
			$country_query = xtc_db_query("select countries_name, countries_iso_code_2 from ".TABLE_COUNTRIES." where countries_id = '".$customers['entry_country_id']."'");
			$country = xtc_db_fetch_array($country_query);

			$stat_query = xtc_db_query("select * from ".TABLE_CUSTOMERS_STATUS." where customers_status_id = '".$customers1[customers_status]."' and language_id = " . (int)$_SESSION['languages_id']);
			$stat = xtc_db_fetch_array($stat_query);

            if(ACCOUNT_STATE=='false') $customers['entry_suburb']='';
			// BOF GM_MOD
			$coo_gm_currencies = new currencies();
			$sql_data_array = array ('customers_id' => xtc_db_prepare_input($customers['customers_id']),
									'customers_cid' => xtc_db_prepare_input($customers1['customers_cid']),
									'customers_vat_id' => xtc_db_prepare_input($customers1['customers_vat_id']),
									'customers_status' => xtc_db_prepare_input($customers1['customers_status']),
									'customers_status_name' => xtc_db_prepare_input($stat['customers_status_name']),
									'customers_status_image' => xtc_db_prepare_input($stat['customers_status_image']),
									'customers_status_discount' => xtc_db_prepare_input($stat['customers_status_discount']),
									'customers_gender' => xtc_db_prepare_input($customers['entry_gender']),
									'customers_name' => xtc_db_prepare_input($customers['entry_firstname'].' '.$customers['entry_lastname']),
									'customers_firstname' => xtc_db_prepare_input($customers['entry_firstname']),
									'customers_lastname' => xtc_db_prepare_input($customers['entry_lastname']),
									'customers_company' => xtc_db_prepare_input($customers['entry_company']),
									'customers_street_address' => xtc_db_prepare_input($customers['entry_street_address']),
									'customers_suburb' => xtc_db_prepare_input($customers['entry_suburb']),
									'customers_city' => xtc_db_prepare_input($customers['entry_city']),
									'customers_postcode' => xtc_db_prepare_input($customers['entry_postcode']),
									'customers_state' => xtc_db_prepare_input($customers['entry_state']),
									'customers_country' => xtc_db_prepare_input($country['countries_name']),
									'customers_telephone' => xtc_db_prepare_input($customers1['customers_telephone']),
									'customers_email_address' => xtc_db_prepare_input($customers1['customers_email_address']),
									'customers_address_format_id' => '5',
									'customers_ip' => '0',
									'delivery_gender' => xtc_db_prepare_input($customers['entry_gender']),
									'delivery_name' => xtc_db_prepare_input($customers['entry_firstname'].' '.$customers['entry_lastname']),
									'delivery_firstname' => xtc_db_prepare_input($customers['entry_firstname']),
									'delivery_lastname' => xtc_db_prepare_input($customers['entry_lastname']),
									'delivery_company' => xtc_db_prepare_input($customers['entry_company']),
									'delivery_street_address' => xtc_db_prepare_input($customers['entry_street_address']),
									'delivery_suburb' => xtc_db_prepare_input($customers['entry_suburb']),
									'delivery_city' => xtc_db_prepare_input($customers['entry_city']),
									'delivery_postcode' => xtc_db_prepare_input($customers['entry_postcode']),
									'delivery_state' => xtc_db_prepare_input($customers['entry_state']),
									'delivery_country' => xtc_db_prepare_input($country['countries_name']),
									'delivery_address_format_id' => '5',
									'billing_gender' => xtc_db_prepare_input($customers['entry_gender']),
									'billing_name' => xtc_db_prepare_input($customers['entry_firstname'].' '.$customers['entry_lastname']),
									'billing_firstname' => xtc_db_prepare_input($customers['entry_firstname']),
									'billing_lastname' => xtc_db_prepare_input($customers['entry_lastname']),
									'billing_company' => xtc_db_prepare_input($customers['entry_company']),
									'billing_street_address' => xtc_db_prepare_input($customers['entry_street_address']),
									'billing_suburb' => xtc_db_prepare_input($customers['entry_suburb']),
									'billing_city' => xtc_db_prepare_input($customers['entry_city']),
									'billing_postcode' => xtc_db_prepare_input($customers['entry_postcode']),
									'billing_state' => xtc_db_prepare_input($customers['entry_state']),
									'billing_country' => xtc_db_prepare_input($country['countries_name']),
									'billing_address_format_id' => '5',
									'payment_method' => 'cod',
									'cc_type' => '',
									'cc_owner' => '',
									'cc_number' => '',
									'cc_expires' => '',
									'cc_start' => '',
									'cc_issue' => '',
									'cc_cvv' => '',
									'comments' => '',
									'last_modified' => 'now()',
									'date_purchased' => 'now()',
									'orders_status' => '1',
									'orders_date_finished' => '',
									'currency' => DEFAULT_CURRENCY,
									'currency_value' => $coo_gm_currencies->currencies[DEFAULT_CURRENCY]['value'],
									'account_type' => '0',
									'payment_class' => 'cod',
									'shipping_method' => 'Pauschale Versandkosten',
									'shipping_class' => 'flat_flat',
									'customers_ip' => '',
									'language' => 'german',
									'delivery_country_iso_code_2' => $country['countries_iso_code_2'],
									'billing_country_iso_code_2' => $country['countries_iso_code_2']);
			// EOF GM_MOD

			$sql_data_array['orders_hash'] = md5(time() + mt_rand());

			$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
			//xtc_db_perform(TABLE_ORDERS, $sql_data_array);
			$orders_id = xtc_db_insert_id();

			$sql_data_array = array ('orders_id' => $orders_id, 'title' => '<b>Summe</b>:', 'text' => '0', 'value' => '0', 'class' => 'ot_total');

			$insert_sql_data = array ('sort_order' => MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER);
			$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
			//xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);

			$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/order_total/ot_total_netto.php');
			require_once(DIR_FS_CATALOG . 'includes/modules/order_total/ot_total_netto.php');
			$coo_ot_total_netto = new ot_total_netto();
			if($coo_ot_total_netto->check())
			{
				$sql_data_array = array('orders_id' => $orders_id,
										'title' => $coo_ot_total_netto->title . ':',
										'text' => '0', 'value' => '0',
										'class' => $coo_ot_total_netto->code,
										'sort_order' => $coo_ot_total_netto->sort_order);
				//xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
			}

			$sql_data_array = array ('orders_id' => $orders_id, 'title' => '<b>Zwischensumme</b>:', 'text' => '0', 'value' => '0', 'class' => 'ot_subtotal');

			$insert_sql_data = array ('sort_order' => MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER);
			$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
			//xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);

			/**
			 * BEGIN NEW ORDER SERVICE
			 */

			/** @var OrderWriteService $orderWriteService */
			$orderWriteService = StaticGXCoreLoader::getService('OrderWrite');

			$isGuest = new BoolType($customers1['customers_status'] == DEFAULT_CUSTOMERS_STATUS_ID_GUEST);

			$customerStatusInfo = MainFactory::create('CustomerStatusInformation',
			                                          new IdType((int)$customers1['customers_status']),
			                                          new StringType((string)$stat['customers_status_name']),
			                                          new StringType((string)$stat['customers_status_image']),
			                                          new DecimalType((double)$stat['customers_status_discount']), $isGuest);

			/** @var CustomerReadService $customerReadService */
			$customerReadService = StaticGXCoreLoader::getService('CustomerRead');

			/** @var Customer $customer */
			$customer = $customerReadService->getCustomerById(new IdType($customers['customers_id']));

			$customerAddress = $customer->getDefaultAddress();

			/** @var AddressBookService $addressBookService */
			$addressBookService = StaticGXCoreLoader::getService('AddressBook');

			$addressBookId  = new IdType($customers['address_book_id']);
			$address = $addressBookService->findAddressById($addressBookId);

			$orderTotalObjects = array();

			/** @var OrderObjectService $orderObjectService */
			$orderObjectService = StaticGXCoreLoader::getService('OrderObject');

			$orderTotalObjects[] = $orderObjectService->createOrderTotalObject(new StringType('<b>Zwischensumme:</b>'),
			                                                                   new DecimalType(0),
			                                                                   new StringType('0'),
			                                                                   new StringType('ot_subtotal'),
			                                                                   MainFactory::create('IntType',
			                                                                                       (int)MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER));

			if($coo_ot_total_netto->check())
			{
				$orderTotalObjects[] = $orderObjectService->createOrderTotalObject(new StringType($coo_ot_total_netto->title),
				                                                                   new DecimalType(0),
				                                                                   new StringType('0'),
				                                                                   new StringType($coo_ot_total_netto->code),
				                                                                   MainFactory::create('IntType',
				                                                                                       (int)$coo_ot_total_netto->sort_order));
			}

			$orderTotalObjects[] = $orderObjectService->createOrderTotalObject(new StringType('<b>Summe:</b>'),
			                                                                   new DecimalType(0),
			                                                                   new StringType('0'),
			                                                                   new StringType('ot_total'),
			                                                                   MainFactory::create('IntType',
				                                                                   (int)MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER));

			$orderTotals = MainFactory::create('OrderTotalCollection', $orderTotalObjects);

			$orderId = $orderWriteService->createNewCustomerOrder(new IdType($customers['customers_id']),
			                                                      $customerStatusInfo,
			                                                      new StringType((string)$customers1['customers_cid']),
			                                                      new EmailStringType((string)$customers1['customers_email_address']),
			                                                      new StringType((string)$customers1['customers_telephone']),
			                                                      new StringType((string)$customers1['customers_vat_id']),
			                                                      $customerAddress, $address, $address,
			                                                      MainFactory::create('OrderItemCollection', array()),
			                                                      $orderTotals, MainFactory::create('OrderShippingType',
			                                                                                        new StringType('Pauschale Versandkosten'),
			                                                                                        new StringType('flat_flat')),
			                                                      MainFactory::create('OrderPaymentType',
			                                                                          new StringType('cod'),
			                                                                          new StringType('cod')),
			                                                      MainFactory::create('CurrencyCode',
			                                                                          new NonEmptyStringType(DEFAULT_CURRENCY)),
			                                                      new LanguageCode(new NonEmptyStringType($_SESSION['language_code'])),
			                                                      new DecimalType(0.0), new StringType(''));

			xtc_redirect(xtc_href_link(FILENAME_ORDERS, 'oID='.$orderId.'&action=edit'));

			break;
		case 'statusconfirm' :
			if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				$new_status = $_POST['status'];
				$customers_id = xtc_db_prepare_input($_GET['cID']);
				$customer_updated = false;
				$t_account_type = 0;
				if( $new_status == 1 ) $t_account_type = 1;
				$check_status_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address , customers_status, member_flag from ".TABLE_CUSTOMERS." where customers_id = '".xtc_db_input($_GET['cID'])."'");
				$check_status = xtc_db_fetch_array($check_status_query);
				if ($check_status['customers_status'] != $new_status) {
					xtc_db_query("update ".TABLE_CUSTOMERS." set customers_status = '".xtc_db_input($new_status)."', account_type = '" . $t_account_type . "' where customers_id = '".xtc_db_input($_GET['cID'])."'");
					// BOF GM_MOD:
					xtc_db_query("update ".TABLE_NEWSLETTER_RECIPIENTS." set customers_status = '".xtc_db_input($new_status)."' where customers_id = '".xtc_db_input($_GET['cID'])."'");

					// create insert for admin access table if customers status is set to 0
					if ($new_status == 0) {
						xtc_db_query("INSERT into ".TABLE_ADMIN_ACCESS." (customers_id,start) VALUES ('".xtc_db_input($_GET['cID'])."','1')");
						$messageStack->add_session(TEXT_INFO_ADMIN_HAS_NO_RIGHTS . '<a href="' . xtc_href_link(FILENAME_ACCOUNTING, xtc_get_all_get_params(array ('cID', 'action')).'cID='.xtc_db_input($_GET['cID'])) . '">' . xtc_href_link(FILENAME_ACCOUNTING, xtc_get_all_get_params(array ('cID', 'action')).'cID='.xtc_db_input($_GET['cID'])) . '</a>', 'warning');
					} else {
						xtc_db_query("DELETE FROM ".TABLE_ADMIN_ACCESS." WHERE customers_id = '".xtc_db_input($_GET['cID'])."'");

					}
					//Temporarily set due to above commented lines
					$customer_notified = '0';
					xtc_db_query("insert into ".TABLE_CUSTOMERS_STATUS_HISTORY." (customers_id, new_value, old_value, date_added, customer_notified) values ('".xtc_db_input($_GET['cID'])."', '".xtc_db_input($new_status)."', '".$check_status['customers_status']."', now(), '".$customer_notified."')");
					$customer_updated = true;
				}
				xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, 'page='.$_GET['page'].'&cID='.$_GET['cID']));
			}
			break;

		case 'update' :
			if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				$customers_id = xtc_db_prepare_input($_GET['cID']);
				$customers_cid = xtc_db_prepare_input($_POST['csID']);
				$customers_vat_id = xtc_db_prepare_input($_POST['customers_vat_id']);
				$customers_vat_id_status = xtc_db_prepare_input($_POST['customers_vat_id_status']);
				$customers_firstname = xtc_db_prepare_input($_POST['customers_firstname']);
				$customers_lastname = xtc_db_prepare_input($_POST['customers_lastname']);
				$customers_email_address = xtc_db_prepare_input($_POST['customers_email_address']);
				$customers_telephone = xtc_db_prepare_input($_POST['customers_telephone']);
				$customers_fax = xtc_db_prepare_input($_POST['customers_fax']);
				$customers_newsletter = xtc_db_prepare_input($_POST['customers_newsletter']);

				$customers_gender = xtc_db_prepare_input($_POST['customers_gender']);
				$customers_dob = xtc_db_prepare_input($_POST['customers_dob']);

				$default_address_id = xtc_db_prepare_input($_POST['default_address_id']);
				$entry_street_address = xtc_db_prepare_input($_POST['entry_street_address']);
				$entry_house_number = xtc_db_prepare_input($_POST['entry_house_number']);
				$entry_additional_info = xtc_db_prepare_input($_POST['customers_additional_info']);
				$entry_suburb = xtc_db_prepare_input($_POST['entry_suburb']);
				$entry_postcode = xtc_db_prepare_input($_POST['entry_postcode']);
				$entry_city = xtc_db_prepare_input($_POST['entry_city']);
				$entry_country_id = xtc_db_prepare_input($_POST['entry_country_id']);

				$entry_company = xtc_db_prepare_input($_POST['entry_company']);
				$entry_state = xtc_db_prepare_input($_POST['entry_state']);
				$entry_zone_id = xtc_db_prepare_input($_POST['entry_zone_id']);

				$memo_title = xtc_db_prepare_input($_POST['memo_title']);
				$memo_text = xtc_db_prepare_input($_POST['memo_text']);

				$payment_unallowed = xtc_db_prepare_input($_POST['payment_unallowed']);
				$shipping_unallowed = xtc_db_prepare_input($_POST['shipping_unallowed']);
				$password = xtc_db_prepare_input($_POST['entry_password']);

				$t_credit_sql_mode = 'update';
				$sql_data_array = array('amount' => (double)$_POST['credit_balance']);
				$t_sql = 'SELECT * FROM ' . TABLE_COUPON_GV_CUSTOMER . ' WHERE customer_id = "' . (int)$customers_id . '"';
				$t_result = xtc_db_query($t_sql);
				if(xtc_db_num_rows($t_result) == 0)
				{
					$t_credit_sql_mode = 'insert';
					$sql_data_array['customer_id'] = (int)$customers_id;
				}

				xtc_db_perform(TABLE_COUPON_GV_CUSTOMER, $sql_data_array, $t_credit_sql_mode, 'customer_id = "' . (int)$customers_id . '"');

				$t_sql = 'SELECT
								coupon_id,
								coupon_amount
							FROM ' . TABLE_COUPONS . '
							WHERE
								coupon_code = "' . xtc_db_input($_POST['voucher_code']) . '" AND
								coupon_active = "Y" AND
								coupon_type = "G"';
				$t_result = xtc_db_query($t_sql);
				if(xtc_db_num_rows($t_result) > 0)
				{
					$t_result_array = xtc_db_fetch_array($t_result);
					$t_voucher_value = (double)$t_result_array['coupon_amount'];
					$t_coupon_id = $t_result_array['coupon_id'];
					$t_current_credit_balance = 0;

					$t_sql = 'SELECT amount FROM ' . TABLE_COUPON_GV_CUSTOMER . ' WHERE customer_id = "' . (int)$customers_id . '"';
					$t_result = xtc_db_query($t_sql);
					$t_coupon_gv_customer_exists = xtc_db_num_rows($t_result) > 0;

					if($t_coupon_gv_customer_exists)
					{
						$t_result_array = xtc_db_fetch_array($t_result);
						$t_current_credit_balance = (double)$t_result_array['amount'];
					}

					$t_new_credit_balance = $t_current_credit_balance + $t_voucher_value;
					xtc_db_query("UPDATE " . TABLE_COUPONS . " SET coupon_active = 'N' WHERE coupon_id = '" . $t_coupon_id . "'");
					xtc_db_query("INSERT INTO " . TABLE_COUPON_REDEEM_TRACK . " (coupon_id, customer_id, redeem_date, redeem_ip)
									VALUES ('" . $t_coupon_id . "', '" . (int)$customers_id . "', NOW(),'" .  xtc_db_input(xtc_get_ip_address()) . "')");

					if($t_coupon_gv_customer_exists)
					{
						xtc_db_query("UPDATE " . TABLE_COUPON_GV_CUSTOMER . " SET amount = '" . $t_new_credit_balance . "' WHERE customer_id = '" . (int)$customers_id . "'");
					}
					else
					{
						xtc_db_query("INSERT INTO " . TABLE_COUPON_GV_CUSTOMER . " (customer_id, amount) VALUES ('" .(int)$customers_id . "', '" . $t_new_credit_balance . "')");
					}
				}

				if ($memo_text != '' && $memo_title != '') {
					$sql_data_array = array ('customers_id' => xtc_db_input($_GET['cID']), 'memo_date' => date("Y-m-d"), 'memo_title' => $memo_title, 'memo_text' => $memo_text, 'poster_id' => $_SESSION['customer_id']);
					xtc_db_perform(TABLE_CUSTOMERS_MEMO, $sql_data_array);
				}
				$error = false; // reset error flag

				$namesOptional = ACCOUNT_NAMES_OPTIONAL === 'true' && $entry_company !== '';

				if(!$namesOptional && strlen_wrapper($customers_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH)
				{
					$error                 = true;
					$entry_firstname_error = true;
				}
				else
				{
					$entry_firstname_error = false;
				}

				if(!$namesOptional && strlen_wrapper($customers_lastname) < ENTRY_LAST_NAME_MIN_LENGTH)
				{
					$error                = true;
					$entry_lastname_error = true;
				}
				else
				{
					$entry_lastname_error = false;
				}

				if (ACCOUNT_DOB == 'true') {
					if (checkdate(substr(xtc_date_raw($customers_dob), 4, 2), substr(xtc_date_raw($customers_dob), 6, 2), substr(xtc_date_raw($customers_dob), 0, 4))) {
						$entry_date_of_birth_error = false;
					} else {
						$error = true;
						$entry_date_of_birth_error = true;
					}
				}

				// GET ZONE_ID
				if(ACCOUNT_STATE == 'true')
				{
					$check_query = xtc_db_query("select count(*) as total from ".TABLE_ZONES." where zone_country_id = '".(int) $entry_country_id."'");
					$check = xtc_db_fetch_array($check_query);
					$entry_state_has_zones = ($check['total'] > 0);
					if($entry_state_has_zones == true)
					{
						$zone_query = xtc_db_query("select distinct zone_id from ".TABLE_ZONES." where zone_country_id = '".(int) $entry_country_id."' and (zone_name like '".xtc_db_input($entry_state)."%' or zone_code like '%".xtc_db_input($entry_state)."%')");
						if(xtc_db_num_rows($zone_query) > 1)
						{
							$zone_query = xtc_db_query("select distinct zone_id from ".TABLE_ZONES." where zone_country_id = '".(int) $entry_country_id."' and zone_name = '".xtc_db_input($entry_state)."'");
						}
						if(xtc_db_num_rows($zone_query) >= 1)
						{
							$zone = xtc_db_fetch_array($zone_query);
							$entry_zone_id = $zone['zone_id'];
						}
						else
						{
							$entry_state_error = true;
							$error = true;
						}
					}
				}

				// New VAT Check
					if (xtc_get_geo_zone_code($entry_country_id) != '6') {
					require_once(DIR_FS_CATALOG.DIR_WS_CLASSES.'vat_validation.php');
					$vatID = new vat_validation($customers_vat_id, $customers_id, '', $entry_country_id);

					$customers_vat_id_status = $vatID->vat_info['vat_id_status'];
					$vat_error = $vatID->vat_info['error'];

					if($vat_error==1){
					$entry_vat_error = true;
					$error = true;
				  }

				  }
				// New VAT CHECK END

				if (strlen_wrapper($customers_email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
					$error = true;
					$entry_email_address_error = true;
				} else {
					$entry_email_address_error = false;
				}

				if (!xtc_validate_email($customers_email_address)) {
					$error = true;
					$entry_email_address_check_error = true;
				} else {
					$entry_email_address_check_error = false;
				}

				// BOF GM_MOD:
				if (strlen_wrapper($password) < ENTRY_PASSWORD_MIN_LENGTH && $password != '') {
					$error = true;
					$entry_password_error = true;
				} else {
					$entry_password_error = false;
				}

				$check_email = xtc_db_query("select customers_email_address from ".TABLE_CUSTOMERS." where customers_email_address = '".xtc_db_input($customers_email_address)."' and customers_id <> '".xtc_db_input($customers_id)."'");
				if (xtc_db_num_rows($check_email)) {
					$error = true;
					$entry_email_address_exists = true;
				} else {
					$entry_email_address_exists = false;
				}

				if ($error == false) {
					// BOF GM_MOD
					$gm_check_newsletter = xtc_db_query("SELECT customers_newsletter FROM customers WHERE customers_id = '".xtc_db_input($customers_id)."'");
					if(xtc_db_num_rows($gm_check_newsletter) == 1){
						$check_newsletter = xtc_db_fetch_array($gm_check_newsletter);
						if($check_newsletter['customers_newsletter'] != $customers_newsletter){
							if($customers_newsletter == 0){
								xtc_db_query("DELETE FROM newsletter_recipients WHERE customers_id = '".xtc_db_input($customers_id)."'");
							}
							else{
								xtc_db_query("DELETE FROM newsletter_recipients WHERE customers_id = '".xtc_db_input($customers_id)."'");

								$gm_get_customers_status = xtc_db_query("SELECT customers_status FROM customers WHERE customers_id = '".xtc_db_input($customers_id)."'");
								$gm_customers_status = xtc_db_fetch_array($gm_get_customers_status);

								xtc_db_query("INSERT INTO newsletter_recipients
												SET
													customers_email_address = '" . $customers_email_address . "',
													customers_id = '" . xtc_db_input($customers_id) . "',
													customers_status = '" . $gm_customers_status['customers_status'] . "',
													customers_firstname = '" . xtc_db_input($customers_firstname) . "',
													customers_lastname = '" . xtc_db_input($customers_lastname) . "',
													mail_status = '1',
													mail_key = '" . xtc_random_charcode(32) . "',
													date_added = NOW()");

								unset($gm_get_customers_status);
								unset($gm_customers_status);
							}
						}
						unset($check_newsletter);
					}
					unset($gm_check_newsletter);
					// EOF GM_MOD

					$sql_data_array = array ('payment_unallowed' => $payment_unallowed,
					                         'shipping_unallowed' => $shipping_unallowed,
					                         'customers_newsletter' => $customers_newsletter,
					                         'customers_last_modified' => 'now()');

					xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update',
					               "customers_id = '" . xtc_db_input($customers_id) . "'");

					xtc_db_query("update " . TABLE_CUSTOMERS_INFO
					             . " set customers_info_date_account_last_modified = now() where customers_info_id = '"
					             . xtc_db_input($customers_id) . "'");

					/** @var CustomerReadService $customerReadService */
					$customerReadService = StaticGXCoreLoader::getService('CustomerRead');

					/** @var AddressBooKService $addressBookService */
					$addressBookService = StaticGXCoreLoader::getService('AddressBook');

					$customer = $customerReadService->getCustomerById(new IdType($customers_id));
					$customerDefaultAddress = $customer->getDefaultAddress();

					$customer->setCustomerNumber(MainFactory::create('CustomerNumber', $customers_cid));
					$customer->setFirstname(MainFactory::create('CustomerFirstname', $customers_firstname));
					$customer->setLastname(MainFactory::create('CustomerLastname', $customers_lastname));
					$customer->setEmail(MainFactory::create('CustomerEmail', $customers_email_address));
					$customer->setVatNumber(MainFactory::create('CustomerVatNumber', (string)$customers_vat_id));
					$customer->setVatNumberStatus((int)$customers_vat_id_status);
					$customer->setTelephoneNumber(MainFactory::create('CustomerCallNumber', $customers_telephone));
					$customer->setFaxNumber(MainFactory::create('CustomerCallNumber', $customers_fax));

					// if new password is set
					if($password !== '')
					{
						$customer->setPassword(MainFactory::create('CustomerPassword', $password));
					}

					if(ACCOUNT_GENDER == 'true')
					{
						$customer->setGender(MainFactory::create('CustomerGender', $customers_gender));
						$customerDefaultAddress->setGender(MainFactory::create('CustomerGender', $customers_gender));
					}

					if(ACCOUNT_DOB == 'true')
					{
						$customer->setDateOfBirth(MainFactory::create('CustomerDateOfBirth', xtc_date_raw($customers_dob)));
					}

					$customerDefaultAddress->setFirstname(MainFactory::create('CustomerFirstname', $customers_firstname));
					$customerDefaultAddress->setLastname(MainFactory::create('CustomerLastname', $customers_lastname));
					$customerDefaultAddress->setStreet(MainFactory::create('CustomerStreet', $entry_street_address));
					$customerDefaultAddress->setHouseNumber(MainFactory::create('CustomerHouseNumber', (string)$entry_house_number));
					$customerDefaultAddress->setAdditionalAddressInfo(MainFactory::create('CustomerAdditionalAddressInfo', (string)$entry_additional_info));
					$customerDefaultAddress->setPostcode(MainFactory::create('CustomerPostcode', $entry_postcode));
					$customerDefaultAddress->setCity(MainFactory::create('CustomerCity', $entry_city));

					/** @var CountryService $countryService */
					$countryService = StaticGXCoreLoader::getService('Country');
					$country = $countryService->getCountryById(new IdType($entry_country_id));

					$customerDefaultAddress->setCountry($country);

					if(ACCOUNT_COMPANY == 'true')
					{
						$customerDefaultAddress->setCompany(MainFactory::create('CustomerCompany', $entry_company));
					}

					if(ACCOUNT_SUBURB == 'true')
					{
						$customerDefaultAddress->setSuburb(MainFactory::create('CustomerSuburb', $entry_suburb));
					}

					if(ACCOUNT_STATE == 'true')
					{
						if($entry_zone_id > 0)
						{
							$countryZone = $countryService->getCountryZoneById(new IdType($entry_zone_id));
						}
						else
						{
							$countryZone = $countryService->getUnknownCountryZoneByName($entry_state);
						}

						$customerDefaultAddress->setCountryZone($countryZone);
					}

					if(ACCOUNT_COMPANY == 'true')
					{
						$customerDefaultAddress->setB2BStatus(MainFactory::create('CustomerB2BStatus', (boolean)(int)$_POST['customer_b2b_status']));
					}

					/** @var CustomerWriteService $customerWriteService */
					$customerWriteService = StaticGXCoreLoader::getService('CustomerWrite');

					$customerWriteService->updateCustomer($customer);
					$addressBookService->updateCustomerAddress($customerDefaultAddress);

					xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params(array ('cID', 'action')).'cID='.$customers_id.'&action=edit'));
				}
				elseif ($error == true) {
					$cInfo = new objectInfo($_POST);
					$processed = true;
				}
			}
			break;

		case 'deleteconfirm':
			if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				$customers_id = xtc_db_prepare_input($_GET['cID']);

				/** @var CustomerWriteService $customerWriteService */
				$customerWriteService = StaticGXCoreLoader::getService('CustomerWrite');
				$customerWriteService->deleteCustomerById(new IdType($customers_id));

				if ($_POST['delete_reviews'] == 'on') {
					$reviews_query = xtc_db_query("select reviews_id from ".TABLE_REVIEWS." where customers_id = '".xtc_db_input($customers_id)."'");
					while ($reviews = xtc_db_fetch_array($reviews_query)) {
						xtc_db_query("delete from ".TABLE_REVIEWS_DESCRIPTION." where reviews_id = '".$reviews['reviews_id']."'");
					}
					xtc_db_query("delete from ".TABLE_REVIEWS." where customers_id = '".xtc_db_input($customers_id)."'");
				} else {
					xtc_db_query("update ".TABLE_REVIEWS." set customers_id = null where customers_id = '".xtc_db_input($customers_id)."'");
				}

				xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params(array ('cID', 'action'))));
			}
			break;

		default :
			break;
	}
}

$GLOBALS['messageStack']->add_additional_class('breakpoint-large');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>
</head>
<body
	marginwidth="0"
	marginheight="0"
	topmargin="0"
	bottommargin="0"
	leftmargin="0"
	rightmargin="0"
	bgcolor="#FFFFFF"
	onload="SetFocus();"
	data-gx-widget="button_dropdown"
    data-button_dropdown-user_id="<?php echo (int)$_SESSION['customer_id']; ?>"
>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

<script type="text/javascript">
	$(document).ready(function()
	{
		$('#delete_guest_accounts').click(function()
		{
			$.ajax({
				url: $('#delete_guest_accounts').attr('href'),
				type: 'GET',
				dataType: 'json',
				data: '',
				async: false,
				success: function(p_result_json)
						{
							var t_url = window.location.href;
							if(window.location.search.search('cID=') != -1)
							{
								t_url = window.location.href.replace(/[&]?cID=[\d]+/g, '');
							}

							window.location.href = t_url;

							return false;
						}
			});

			return false;
		});
	});
</script>

<?php include DIR_FS_ADMIN . 'html/content/customer_memo_form.php'; ?>
<table border="0" style="width: 100%; height: 100%;" cellspacing="2" cellpadding="0">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="0" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0" class="breakpoint-large">
<?php

if ($_GET['action'] == 'edit' || $_GET['action'] == 'update') {
	$t_sql = "SELECT
					c.payment_unallowed,
					c.shipping_unallowed,
					c.customers_gender,
					c.customers_vat_id,
					c.customers_status,
					c.member_flag,
					c.customers_firstname,
					c.customers_cid,
					c.customers_lastname,
					c.customers_dob,
					c.customers_email_address,
					a.entry_company,
					a.entry_street_address,
					a.entry_house_number,
					a.entry_additional_info,
					a.entry_suburb,
					a.entry_postcode,
					a.entry_city,
					a.entry_state,
					a.entry_zone_id,
					a.entry_country_id,
					a.customer_b2b_status,
					c.customers_telephone,
					c.customers_fax,
					c.customers_newsletter,
					c.customers_default_address_id,
					g.amount AS credit_balance
				FROM
					" . TABLE_CUSTOMERS . " c
					LEFT JOIN  " . TABLE_ADDRESS_BOOK . " a ON (c.customers_default_address_id = a.address_book_id)
					LEFT JOIN " . TABLE_COUPON_GV_CUSTOMER . " g ON (c.customers_id = g.customer_id)
				WHERE
					a.customers_id = c.customers_id AND
					c.customers_id = '" . (int)$_GET['cID'] . "'";
	$customers_query = xtc_db_query($t_sql);

	$customers = xtc_db_fetch_array($customers_query);
	$cInfo = new objectInfo((array)$customers);
	$newsletter_array = array (array ('id' => '1', 'text' => ENTRY_NEWSLETTER_YES), array ('id' => '0', 'text' => ENTRY_NEWSLETTER_NO));

include DIR_FS_ADMIN . 'html/compatibility/customer_details.php';

} else {
?>
      <tr>
        <td class="gx-customer-overview">
		<div class="pageHeading" style="float:left; background-image:url(html/assets/images/legacy/gm_icons/kunden.png)">
			<?php echo HEADING_TITLE; ?>

			<table>
				<tr>
					<td class="dataTableHeadingContent">
						<?php echo BOX_CUSTOMERS ?>
					</td>
					<td class="dataTableHeadingContent">
						<a href="configuration.php?gID=5">
							<?php echo BOX_CONFIGURATION_5; ?>
						</a>
					</td>
				</tr>
			</table>

		</div>
	        <div class="gx-container create-new-wrapper">
		        <div class="create-new-container pull-right">
			        <a href="<?php echo xtc_href_link('create_account.php') ?>"
			           class="btn btn-success"><i
					        class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create', 'buttons'); ?>
			        </a>
		        </div>
	        </div>
     </td>
      </tr>
      <tr>
        <td>

             <div class="customer-sort-links">
             <a style='border: 0px; color: #000; float: left; width: 25px;' href='customers.php'><?php echo ALL; ?></a>
          <?php
          $buchstabe='A';
          for($a=0;$a<26;$a++)
          {

             echo "<a style='border: 0px; color: #000; float: left; width: 30px;' href='customers.php?search=".$buchstabe."'>".$buchstabe."</a>";

             $buchstabe++;
          }
          ?>
		<br />
		</div>
        <table style="clear: both;" border="0" width="100%" cellspacing="0" cellpadding="0" data-gx-widget="table_sorting" data-gx-extension="visibility_switcher" data-visibility_switcher-selections="div.action-list">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0" class="gx-compatibility-table gx-customer-overview" data-gx-compatibility="customers/customers_table_controller">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" style="width: 36px"><?php echo TABLE_HEADING_ACCOUNT_TYPE; ?></td>

	              <?php
	              $customerLastNameCaret = _getCaret('customers_lastname');
	              ?>
	              <td class="dataTableHeadingContent cursor-pointer"
	                  data-use-table_sorting="true"
	                  data-section="customers"
	                  data-column="lastName"
	                  data-direction="<?php echo $customerLastNameCaret['sortingDirection']; ?>"
	                  data-active-caret="<?php echo $customerLastNameCaret['activeCaret']; ?>"
	                  style="width: 135px"><?php echo TABLE_HEADING_LASTNAME; ?>
	              </td>

	              <?php
	              $customerFirstNameCaret = _getCaret('customers_firstname');
	              ?>
	              <td class="dataTableHeadingContent cursor-pointer"
	                  data-use-table_sorting="true"
	                  data-section="customers"
	                  data-column="firstName"
	                  data-direction="<?php echo $customerFirstNameCaret['sortingDirection']; ?>"
	                  data-active-caret="<?php echo $customerFirstNameCaret['activeCaret']; ?>"
	                  style="width: 120px"><?php echo TABLE_HEADING_FIRSTNAME; ?>
	              </td>

	              <td class="dataTableHeadingContent" style="width: 120px" align="left"><?php echo HEADING_TITLE_STATUS; ?></td>
	              <?php if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {?>
		              <td class="dataTableHeadingContent" style="width: 104px" align="left"><?php echo HEADING_TITLE_VAT; ?></td>
	              <?php } ?>

	              <?php
	              $dateAccountCreatedCaret = _getCaret('date_account_created');
	              ?>
	              <td class="dataTableHeadingContent cursor-pointer"
	                  data-use-table_sorting="true"
	                  data-section="customers"
	                  data-column="dateAccountCreated"
	                  data-direction="<?php echo $dateAccountCreatedCaret['sortingDirection']; ?>"
	                  data-active-caret="<?php echo $dateAccountCreatedCaret['activeCaret']; ?>"
	                  style="width: 80px" align="right">
		              <?php echo TABLE_HEADING_ACCOUNT_CREATED; ?>
	              </td>

	              <?php
	              $dateLastLogonCaret = _getCaret('date_last_logon');
	              ?>
	              <td class="dataTableHeadingContent cursor-pointer"
	                  data-use-table_sorting="true"
	                  data-section="customers"
	                  data-column="dateLastLogon"
	                  data-direction="<?php echo $dateLastLogonCaret['sortingDirection']; ?>"
	                  data-active-caret="<?php echo $dateLastLogonCaret['activeCaret']; ?>"
	                  style="width: 80px" align="right">
		              <?php echo TABLE_HEADING_DATE_LAST_LOGON; ?>
	              </td>

                <td class="dataTableHeadingContent hidden" align="right">&nbsp;</td>
                <td class="dataTableHeadingContent" style="min-width: 200px" align="right">&nbsp;</td>
              </tr>
		      <tr class="dataTableHeadingRow_sortbar">
				<td class="dataTableHeadingContent_sortbar" align="center">&nbsp;</td>
				<td class="dataTableHeadingContent_sortbar"  align="center"><?php echo xtc_sorting(FILENAME_CUSTOMERS,'customers_lastname');	?></td>
				<td class="dataTableHeadingContent_sortbar" align="center"><?php echo xtc_sorting(FILENAME_CUSTOMERS,'customers_firstname'); ?></td>
				<td class="dataTableHeadingContent_sortbar" align="center">&nbsp;</td>
                <?php if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {?>
				<td class="dataTableHeadingContent_sortbar" align="center">&nbsp;</td>
				<?php } ?>
                <td class="dataTableHeadingContent_sortbar" align="right"><?php echo xtc_sorting(FILENAME_CUSTOMERS,'date_account_created'); ?></td>
                <td class="dataTableHeadingContent_sortbar" align="right"><?php echo xtc_sorting(FILENAME_CUSTOMERS,'date_last_logon'); ?></td>
				<td class="dataTableHeadingContent_sortbar hidden" align="center">&nbsp;</td>
				<td class="dataTableHeadingContent_sortbar" align="center">&nbsp;</td>
			  </tr>
	<?php

	$search = '';
	if (($_GET['search']) && (xtc_not_null($_GET['search']))) {
		$keywords = xtc_db_input(xtc_db_prepare_input($_GET['search']));
		if(strlen_wrapper($keywords)==1 AND !is_numeric($keywords)) $search = "and (c.customers_lastname like '".$keywords."%' or c.customers_firstname like '".$keywords."%')";
        else $search = "and (c.customers_lastname like '%".$keywords."%' or c.customers_firstname like '%".$keywords."%' or c.customers_email_address like '%".$keywords."%' or c.customers_id like '%".$keywords."%' or c.customers_cid like '%".$keywords."%')";
	}

	if (isset($_GET['status']) && $_GET['status'] !== '-1') {
		$status = $_GET['status'];
		//  echo $status;
		$search = "and c.customers_status = '".xtc_db_input($status)."'";
	}

		switch ($_GET['sorting']) {

			case 'customers_firstname' :
				$sort = 'order by c.customers_firstname';
				break;

			case 'customers_firstname-desc' :
				$sort = 'order by c.customers_firstname DESC';
				break;

			case 'customers_lastname' :
				$sort = 'order by c.customers_lastname';
				break;

			case 'customers_lastname-desc' :
				$sort = 'order by c.customers_lastname DESC';
				break;

			case 'date_last_logon' :
				$sort = 'order by ci.customers_info_date_of_last_logon';
				break;

			case 'date_last_logon-desc' :
				$sort = 'order by ci.customers_info_date_of_last_logon DESC';
				break;

			case 'date_account_created' :
				$sort = 'order by ci.customers_info_date_account_created';
				break;

			case 'date_account_created-desc' :
			default:
				$sort = 'order by ci.customers_info_date_account_created DESC';
				break;
		}


	$customers_query_raw = "select
	                                c.account_type,
	                                c.customers_id,
	                                c.customers_vat_id,
	                                c.customers_vat_id_status,
	                                c.customers_lastname,
	                                c.customers_firstname,
	                                c.customers_email_address,
	                                a.entry_country_id,
	                                a.entry_company,
	                                a.customer_b2b_status,
	                                c.customers_status,
	                                c.member_flag,
	                                ci.customers_info_date_account_created,
	                                ci.customers_info_date_of_last_logon
	                                from
	                                ".TABLE_CUSTOMERS." c ,
	                                ".TABLE_ADDRESS_BOOK." a,
	                                ".TABLE_CUSTOMERS_INFO." ci
	                                Where
	                                c.customers_id = a.customers_id
	                                and c.customers_default_address_id = a.address_book_id
	                                and ci.customers_info_id = c.customers_id
	                                ".$search."
	                                group by c.customers_id
	                                ".$sort;

	$customers_split = new splitPageResults($_GET['page'], gm_get_conf('NUMBER_OF_CUSTOMERS_PER_PAGE', 'ASSOC', true), $customers_query_raw, $customers_query_numrows);
	$customers_query = xtc_db_query($customers_query_raw);

	if(xtc_db_num_rows($customers_query) == 0)
	{
		$gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
		echo '
			<tr class="gx-container no-hover">
				<td colspan="8" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
			</tr>
		';
	}
	
	$userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration'); 
	$customerOverviewDropdownBtn = $userConfigurationService->getUserConfiguration(new IdType($_SESSION['customer_id']), 'customerOverviewDropdownBtn');

	while ($customers = xtc_db_fetch_array($customers_query)) {
		$info_query = xtc_db_query("select customers_info_date_account_created as date_account_created, customers_info_date_account_last_modified as date_account_last_modified, customers_info_date_of_last_logon as date_last_logon, customers_info_number_of_logons as number_of_logons from ".TABLE_CUSTOMERS_INFO." where customers_info_id = '".$customers['customers_id']."'");
		$info = xtc_db_fetch_array($info_query);

		if (((!$_GET['cID']) || (@ $_GET['cID'] == $customers['customers_id'])) && (!$cInfo)) {
			$country_query = xtc_db_query("select countries_name from ".TABLE_COUNTRIES." where countries_id = '".$customers['entry_country_id']."'");
			$country = xtc_db_fetch_array($country_query);

			$reviews_query = xtc_db_query("select count(*) as number_of_reviews from ".TABLE_REVIEWS." where customers_id = '".$customers['customers_id']."'");
			$reviews = xtc_db_fetch_array($reviews_query);

			$customer_info = xtc_array_merge($country, $info, $reviews);

			$cInfo_array = xtc_array_merge($customers, $customer_info);
			$cInfo = new objectInfo($cInfo_array);
		}

		if ((is_object($cInfo)) && ($customers['customers_id'] == $cInfo->customers_id)) {
			echo '<tr
				data-row-id="' . $customers['customers_id'] . '"
				data-cust-email="' . $cInfo->customers_email_address . '"
				class="dataTableRowSelected visibility_switcher">';
		} else {
			echo '<tr
				data-row-id="' . $customers['customers_id'] . '"
				data-cust-email="' . $customers['customers_email_address'] . '"
				class="dataTableRow visibility_switcher">';
		}

		if ($customers['account_type'] == 1) {

			echo '<td class="dataTableContent">';
			echo TEXT_GUEST;

		} else {
			echo '<td class="dataTableContent">';
			echo TEXT_ACCOUNT;
		}
?>
		<?php if($customers['customers_lastname'] !== '' && $customers['customers_firstname'] !== ''): ?>
			<td class="dataTableContent"><?php echo htmlspecialchars_wrapper($customers['customers_lastname']); ?></td>
                <td class="dataTableContent"><?php echo htmlspecialchars_wrapper($customers['customers_firstname']); ?></td>
		<?php else: ?>
			<td colspan="2"><?= htmlspecialchars_wrapper($customers['entry_company']); ?></td>
		<?php endif; ?>
                <td class="dataTableContent" data-cust-group="<?php echo $customers_statuses_array[$customers['customers_status']]['id'] ?>" data-cust-id="<?php echo $customers['customers_id'] ?>" align="left"><?php echo $customers_statuses_array[$customers['customers_status']]['text']; ?></td>
                <?php if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {?>
                <td class="dataTableContent" align="left">&nbsp;
                <?php

		if ($customers['customers_vat_id']) {
			echo $customers['customers_vat_id'].'<br /><span style="font-size:8pt"><nobr>('.xtc_validate_vatid_status($customers['customers_id']).')</nobr></span>';
		}
?>
                </td>
                <?php } ?>
                <td class="dataTableContent" align="right"><?php echo ($info['date_account_created'] !== '1000-01-01 00:00:00') ? xtc_date_short($info['date_account_created']) : '-'; ?>&nbsp;</td>
                <td class="dataTableContent" align="right"><?php echo ($info['date_last_logon'] !== '1000-01-01 00:00:00') ? xtc_date_short($info['date_last_logon']) : '-'; ?>&nbsp;</td>
                <td class="dataTableContent hidden" align="right"><div class="arrow-icon"><?php if ( (is_object>($cInfo)) && ($customers['customers_id'] == $cInfo->customers_id) ) { echo xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icon_arrow_right.gif', ''); } else { echo '<a href="' . xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']) . '">' . xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</div></td>

                <td class="dataTableContent gx-container" align="right">

					<div class="action-list pull-right" data-gx-extension="toolbar_icons">
						<!-- ACTION ICONS -->
						<a class="action-icon btn-edit"></a>
						<a class="action-icon btn-delete"></a>
						<a class="action-icon btn-order"></a>
						&nbsp;

						<!-- ROW ACTIONS - BUTTON DROPDOWN WIDGET -->
						<div data-use-button_dropdown="true"
						     data-config_key="customerOverviewDropdownBtn"
						     data-config_value="<?php echo $customerOverviewDropdownBtn; ?>">
							<button></button>
							<ul></ul>
						</div>
					</div>

				</td>
              </tr>
<?php

	}
?>
              <tr class="table-footer">
                <td colspan="8" >

                	<?php
                	if(DELETE_GUEST_ACCOUNT == 'true')
					{
						echo '<button class="button" style="width: 200px" id="delete_guest_accounts" href="' . xtc_href_link('request_port.php', 'module=DeleteGuestAccounts&token=' . md5(LogControl::get_secure_token()), 'NONSSL', false) . '" target="_blank">' . BUTTON_DELETE_GUEST_ACCOUNTS . '</button>';
					}

                	echo xtc_draw_form('status', FILENAME_CUSTOMERS, '', 'get');
						$select_data = array ();
						$select_data = array (array ('id' => '-1', 'text' => TEXT_ALL_CUSTOMERS));
						echo HEADING_TITLE_STATUS . ' ' . xtc_draw_pull_down_menu('status',xtc_array_merge($select_data, $customers_statuses_array), '-1', 'onChange="this.form.submit();"').xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
					?>
					</form>

                <table border="0" width="100%" class="gx-container paginator" id="customer-overview-bottom-container">
                  <tr>
	                  <td>
		                  <button class="btn"
		                          style="margin: 0"
		                          id="delete-guest-accounts"
		                          data-token="<?php echo md5(LogControl::get_secure_token()); ?>">
			                  <?php echo BUTTON_DELETE_GUEST_ACCOUNTS; ?>
		                  </button>
	                  </td>
	                  <td class="pagination-control">
                          <?php
                          echo xtc_draw_form('status', FILENAME_CUSTOMERS, '', 'get');
                          $select_data = array();
                          $select_data = array(
                              array('id' => '-1', 'text' => TEXT_ALL_CUSTOMERS)
                          );
                          echo HEADING_TITLE_STATUS . ' ' . xtc_draw_pull_down_menu('status',
                                                                                    xtc_array_merge($select_data,
                                                                                                    $customers_statuses_array),
                                                                                    '-1',
                                                                                    'onChange="this.form.submit();"')
                               . xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
                          ?>
                          </form>
                          <form class="control-element" name="customers_per_page_form" action="<?php echo xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params()); ?>" method="post">
                              <?php
                              $t_values_array = array();
                              $t_values_array[] = array('id' => 20, 'text' => '20 ' . PER_PAGE);
                              $t_values_array[] = array('id' => 30, 'text' => '30 ' . PER_PAGE);
                              $t_values_array[] = array('id' => 50, 'text' => '50 ' . PER_PAGE);
                              $t_values_array[] = array('id' => 100, 'text' => '100 ' . PER_PAGE);
                              echo xtc_draw_pull_down_menu('number_of_customers_per_page', $t_values_array, gm_get_conf('NUMBER_OF_CUSTOMERS_PER_PAGE'), 'onchange="document.customers_per_page_form.submit()"');
                              ?>
                          </form>
		                  <?php
		                  echo $customers_split->display_count($customers_query_numrows, gm_get_conf('NUMBER_OF_CUSTOMERS_PER_PAGE'), $_GET['page'],
		                                                       TEXT_DISPLAY_NUMBER_OF_CUSTOMERS);
		                  ?>
		                  <span class="page-number-information">
			                  <?php
			                  echo $customers_split->display_links($customers_query_numrows, gm_get_conf('NUMBER_OF_CUSTOMERS_PER_PAGE'),
			                                                       MAX_DISPLAY_PAGE_LINKS, $_GET['page'],
			                                                       xtc_get_all_get_params(array(
				                                                                              'page',
				                                                                              'info',
				                                                                              'x',
				                                                                              'y',
				                                                                              'cID'
			                                                                              )));
			                  ?>
		                  </span>
	                  </td>
				  </tr>
                </table></td>
              </tr>
            </table></td>
<?php

	$heading = array ();
	$contents = array ();
	switch ($_GET['action']) {
		case 'confirm' :
			$heading[] = array ('text' => '<b>'.TEXT_INFO_HEADING_DELETE_CUSTOMER.'</b>');

			$contents = array ('form' => xtc_draw_form('customers', FILENAME_CUSTOMERS, xtc_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=deleteconfirm', 'post', 'data-gx-compatibility="customers/customers_modal_layer" data-customers_modal_layer-action="delete"'));
			$contents[] = array ('text' => TEXT_DELETE_INTRO.'<br /><br /><b>'.htmlspecialchars_wrapper($cInfo->customers_firstname).' '.htmlspecialchars_wrapper($cInfo->customers_lastname).'</b>');
			if ($cInfo->number_of_reviews > 0)
				$contents[] = array ('text' => '<br />'.xtc_draw_checkbox_field('delete_reviews', 'on', true).' '.sprintf(TEXT_DELETE_REVIEWS, $cInfo->number_of_reviews));
			$contents[] = array ('align' => 'center', 'text' => '<br /><div align="center"><input type="submit" class="button" value="'.BUTTON_DELETE.'">' . xtc_draw_hidden_field('page_token', $t_page_token) . '<a class="button" href="'.xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id . '&page_token=' . $t_page_token) . '">'.BUTTON_CANCEL.'</a></div>');
			break;

		case 'editstatus' :
			if($_GET['cID'] != 1)
			{
				$customers_history_query = xtc_db_query("select new_value, old_value, date_added, customer_notified from "
				                                        . TABLE_CUSTOMERS_STATUS_HISTORY . " where customers_id = '"
				                                        . xtc_db_input($_GET['cID'])
				                                        . "' order by customers_status_history_id desc");
				$heading[]               = array('text' => '<b>' . TEXT_INFO_HEADING_STATUS_CUSTOMER . '</b>');
				$contents                = array(
					'form' => xtc_draw_form('customers', FILENAME_CUSTOMERS,
					                        xtc_get_all_get_params(array('cID', 'action')) . 'cID='
					                        . $cInfo->customers_id . '&action=statusconfirm', 'post',
					                        'data-gx-compatibility="customers/customers_modal_layer" data-customers_modal_layer-action="editstatus"')
				);
				//$contents[]              = array('text' => xtc_draw_hidden_field('page_token', $t_page_token));
				$contents[]              = array(
					'text' => xtc_draw_pull_down_menu('status', $customers_statuses_array, $cInfo->customers_status)
				);

				if(xtc_db_num_rows($customers_history_query))
				{
					$boxContent = '<div class="grid">';
					while($customers_history = xtc_db_fetch_array($customers_history_query))
					{
						$boxContent .= '<div class="span12 edit-customer-group-list">';

						$boxContent .= '<i class="fa fa-angle-right"></i>&nbsp; '
						               . $customers_statuses_array[$customers_history['new_value']]['text'] . ' ('
						               . date('d.m.Y H:i:s', strtotime($customers_history['date_added'])) . ')';
						$boxContent .= '</div>';
					}
					$boxContent .= '</div>';
				}
				$contents[] = array(
					'text' => $boxContent . xtc_draw_hidden_field('page_token', $t_page_token)
					          . '<div align="center"><input type="submit" class="button" value="' . BUTTON_UPDATE
					          . '"><a class="btn" href="' . xtc_href_link(FILENAME_CUSTOMERS,
					                                                      xtc_get_all_get_params(array(
						                                                                             'cID',
						                                                                             'action'
					                                                                             )) . 'cID='
					                                                      . $cInfo->customers_id) . '">' . BUTTON_CANCEL
					          . '</a></div>'
				);
				$status     = xtc_db_input($_POST['status']); // maybe this line not needed to recheck...
			}
			break;

		default :
			$customer_status = xtc_get_customer_status($_GET['cID']);
			$cs_id = $customer_status['customers_status'];
			$cs_member_flag = $customer_status['member_flag'];
			$cs_name = $customer_status['customers_status_name'];
			$cs_image = $customer_status['customers_status_image'];
			$cs_discount = $customer_status['customers_status_discount'];
			$cs_ot_discount_flag = $customer_status['customers_status_ot_discount_flag'];
			$cs_ot_discount = $customer_status['customers_status_ot_discount'];
			$cs_staffelpreise = $customer_status['customers_status_staffelpreise'];
			$cs_payment_unallowed = $customer_status['customers_status_payment_unallowed'];

			//      echo 'customer_status ' . $cID . 'variables = ' . $cs_id . $cs_member_flag . $cs_name .  $cs_discount .  $cs_image . $cs_ot_discount;

			if (is_object($cInfo)) {
				$heading[] = array ('text' => '<b>'.htmlspecialchars_wrapper($cInfo->customers_firstname).' '.htmlspecialchars_wrapper($cInfo->customers_lastname).'</b>');
				$contents[] = array ('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold; ">' . TEXT_MARKED_ELEMENTS . '</div><br />');
				if ($cInfo->customers_id != 1) {
					$contents[] = array ('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="'.xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=edit').'">'.BUTTON_EDIT.'</a></div>');
				}
				if ($cInfo->customers_id == 1 && $_SESSION['customer_id'] == 1) {
					$contents[] = array ('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="'.xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=edit').'">'.BUTTON_EDIT.'</a></div>');
				}
				if ($cInfo->customers_id != 1) {
					$contents[] = array ('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="'.xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=confirm').'">'.BUTTON_DELETE.'</a></div>');
				}
				if ($cInfo->customers_id != 1 /*&& $_SESSION['customer_id'] == 1*/
					) {
					$contents[] = array ('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="'.xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=editstatus').'">'.BUTTON_STATUS.'</a></div>');
				}
				// elari cs v3.x changed for added accounting module
				if ($cInfo->customers_id != 1 && $cInfo->customers_status === '0') {
					$contents[] = array ('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="'.xtc_href_link(FILENAME_ACCOUNTING, xtc_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id).'">'.BUTTON_ACCOUNTING.'</a></div>');
				}

				// mediafinanz
				if(gm_get_conf('MODULE_CENTER_MEDIAFINANZ_INSTALLED') == true)
				{
					include_once(DIR_FS_CATALOG . 'includes/modules/mediafinanz/include_customers.php');
				}

				// elari cs v3.x changed for added iplog module
				$contents[] = array ('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="'.xtc_href_link(FILENAME_ORDERS, 'cID='.$cInfo->customers_id).'">'.BUTTON_ORDERS.'</a></div>');

				$contents[] = array ('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="'.xtc_href_link(FILENAME_MAIL, 'selected_box=tools&customer='.$cInfo->customers_email_address).'">'.BUTTON_EMAIL.'</a></div>');

				$contents[] = array ('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="'.xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=iplog').'">'.BUTTON_IPLOG.'</a></div>');

				$contents[] = array ('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="'.xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=new_order').'" onClick="return confirm(\''.NEW_ORDER.'\')">'.BUTTON_NEW_ORDER.'</a></div>');
				$contents[] = array('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold; border-top: 1px solid Black; margin-top: 5px;">' . TEXT_INSERT_ELEMENT . '</div><br />');
				$contents[] = array ('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CREATE_ACCOUNT) . '">' . BUTTON_CREATE_ACCOUNT . '</a></div>');

				$contents[] = array('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold; border-top: 1px solid Black; margin-top: 5px;">' . TEXT_INFORMATIONS . '</div><br />');
				$contents[] = array ('text' => '<span data-iplog="true">' . TEXT_DATE_ACCOUNT_CREATED.' '.xtc_date_short($cInfo->date_account_created) . '</span>' );
				$contents[] = array ('text' => '<span data-iplog="true">' . TEXT_DATE_ACCOUNT_LAST_MODIFIED.' '.xtc_date_short($cInfo->date_account_last_modified) . '</span>');
				$contents[] = array ('text' => '<span data-iplog="true">' . TEXT_INFO_DATE_LAST_LOGON.' '.xtc_date_short($cInfo->date_last_logon) . '</span>');
				$contents[] = array ('text' => '<span data-iplog="true">' . TEXT_INFO_NUMBER_OF_LOGONS.' '.$cInfo->number_of_logons . '</span>');
				$contents[] = array ('text' => '<span data-iplog="true">' . TEXT_INFO_COUNTRY.' '.$cInfo->countries_name . '</span>');
				$contents[] = array ('text' => '<span data-iplog="true">' . TEXT_INFO_NUMBER_OF_REVIEWS.' '.$cInfo->number_of_reviews . '</span>');
			}

			if ($_GET['action'] == 'iplog') {
				if (isset ($_GET['cID'])) {
					$contents[] = array ('text' => '<br /><b data-gx-compatibility="customers/customers_modal_layer" data-customers_modal_layer-action="iplog">IP-Log:'); // BOF GM_MOD EOF//
					$customers_id = xtc_db_prepare_input($_GET['cID']);
					$customers_log_info_array = xtc_get_user_info($customers_id);
					$t_customers_ip_log = '';
					if (xtc_db_num_rows($customers_log_info_array)) {
						while ($customers_log_info = xtc_db_fetch_array($customers_log_info_array)) {
							//$contents[] = array ('text' => '<tr>'."\n".'<td class="smallText">'.$customers_log_info['customers_ip_date'].' '.$customers_log_info['customers_ip'].' '.$customers_log_info['customers_advertiser']);
							if(strlen(trim($customers_log_info['customers_ip_date'])) > 0)
							{
								$t_customers_ip_log .= '<span data-iplog="true">' . TEXT_IP_LOG_LAST_LOGIN . ':<br/>' . $customers_log_info['customers_ip_date'] . '</span><br/><br/>';
							}
							if(strlen(trim($customers_log_info['customers_ip'])) > 0)
							{
								$t_customers_ip_log .= '<span data-iplog="true">' . TEXT_IP_LOG_CUSTOMER_IP . ':<br/>' . $customers_log_info['customers_ip'] . '</span><br/><br/>';
							}
							if(strlen(trim($customers_log_info['customers_advertiser'])) > 0)
							{
								$t_customers_ip_log .= '<span data-iplog="true">' . TEXT_IP_LOG_CUSTOMER_ADVERTISER . 'Kunden-IP:<br/>' . $customers_log_info['customers_advertiser'] . '</span><br/><br/>';
							}
							if(strlen(trim($customers_log_info['customers_host'])) > 0)
							{
								$t_customers_ip_log .= '<span data-iplog="true">' . TEXT_IP_LOG_HOST . ':<br/>' . $customers_log_info['customers_host'] . '</span><br/><br/>';
							}
							if(strlen(trim($customers_log_info['customers_referer_url'])) > 0)
							{
								$t_customers_ip_log .= '<span data-iplog="true">' . TEXT_IP_REFERER_URL . ':<br/>' . $customers_log_info['customers_referer_url'] . '</span><br/><br/>';
							}

							$contents[] = array ('text' => '<tr>'."\n".'<td class="smallText" data-iplog="true">' . $t_customers_ip_log);
						}
					}
				}
				break;
			}
	}

	if ((xtc_not_null($heading)) && (xtc_not_null($contents)) || DELETE_GUEST_ACCOUNT == 'true') {
		echo '            <td width="25%" valign="top" class="info-box">'."\n";

		$box = new box;
		echo $box->infoBox($heading, $contents);

		echo '            </td>'."\n";
	}
?>
          </tr>
        </table></td>
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
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
