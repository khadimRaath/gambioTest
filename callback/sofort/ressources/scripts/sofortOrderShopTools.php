<?php
/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2012-09-06 13:49:09 +0200 (Thu, 06 Sep 2012) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: sofortOrderShopTools.php 5326 2012-09-06 11:49:09Z boehm $
 */


/**
 * get language file
 * @param  string $lng
 * @param  string $paymentMethod
 * @return string
 */
function shopGetLanguageFile($lng, $paymentMethod){
	switch($paymentMethod){
		case 'LS':		$fileName = 'lastschrift';		  break;
		case 'SL':		$fileName = 'sofortlastschrift';  break;
		case 'SR':		$fileName = 'sofortrechnung';	  break;
		case 'SU':		$fileName = 'sofortueberweisung'; break;
		case 'SV':		$fileName = 'sofortvorkasse';	  break;
		case 'general': $fileName = 'general';			  break;
	}

	return DIR_FS_CATALOG.'lang/'.$lng.'/modules/payment/sofort_'.strtolower($fileName).'.php';
}


/**
 * get link to packing slip
 * @return string
 */
function shopGetPackingslipLink(){
	return FILENAME_PRINT_PACKINGSLIP;
}


/**
 * get link to invoice
 * @return string
 */
function shopGetInvoiceLink(){
	return FILENAME_PRINT_ORDER;
}


/**
 * get packing slip text
 * @return string
 */
function shopGetPackingslipText(){
	return BUTTON_PACKINGSLIP;
}


/**
 * get "back" text
 * @return string
 */
function shopGetBackText(){
	return BUTTON_BACK;
}


/**
 * get "invoice" text
 * @return string
 */
function shopGetInvoiceText(){
	return BUTTON_INVOICE;
}


/**
 * get logo for specified sofort-paymentMethod
 * @param  string $paymentMethodShort
 * @return string
 */
function shopGetLogo($paymentMethodShort){
	return constant('MODULE_PAYMENT_SOFORT_'.$paymentMethodShort.'_LOGO_HTML');
}


/**
 * get link to top part of orders.php
 * @return string
 */
function shopGetTop(){
	return DIR_FS_CATALOG.'callback/sofort/ressources/scripts/'.HelperFunctions::getIniValue('shopsystemVersion').'_ordersTop.php';
}


/**
 * get link to bottom part of orders.php
 * @return string
 */
function shopGetBottom(){
	return DIR_FS_CATALOG.'callback/sofort/ressources/scripts/'.HelperFunctions::getIniValue('shopsystemVersion').'_ordersBottom.php';
}


/**
 * get tooltip image
 * @return string
 */
function shopGetTooltipImage(){
	return '../callback/sofort/ressources/images/ilink.gif';
}


/**
 * get icon path
 * @return string
 */
function shopGetIconPath(){
	return '../callback/sofort/ressources/images/';
}


/**
 * get config key
 * @return string
 */
function shopGetConfigKey(){
	return MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY;
}


/**
 * only return result if it contains exactly 1 row
 * @param  string	$query
 * @return boolean
 */
function shopDbCheckAndFetchOne($query){
	$result = xtc_db_query($query);

	if (xtc_db_num_rows($result)==0 || xtc_db_num_rows($result)>=2) {
		return false;
	} else {
		return xtc_db_fetch_array($result);
	}
}


/**
 * handle and submit status / comment change via orderdetailpage
 * @param int	 $oID
 * @param object $order
 * @param string $status
 * @param string $comments
 * @param string $notifyCustomer
 * @param string $notifyWithComments
 */
function shopSofortComment($oID, $order, $status, $comments, $notifyCustomer, $notifyWithComments){
	global $messageStack;

	$order_updated = false;
	$check_status_query = shopDbQuery("SELECT customers_name, customers_email_address, orders_status, date_purchased FROM ".TABLE_ORDERS." WHERE orders_id = '".shopDbInput($oID)."'");
	$check_status = shopDbFetchArray($check_status_query);

	if ($check_status['orders_status'] != $status || $comments != '') {
		shopDbQuery("UPDATE ".TABLE_ORDERS." SET orders_status = '".shopDbInput($status)."', last_modified = now() WHERE orders_id = '".shopDbInput($oID)."'");
		$customer_notified = '0';

		if ($notifyCustomer == 'on') {
			$notify_comments = '';

			if ($notifyWithComments == 'on') {
				$notify_comments = shopDbPrepareInput($comments);
			}

			if (!isset($lang)) $lang = $_SESSION['languages_id'];

			$orders_statuses = array ();
			$orders_status_array = array ();
			$orders_status_query = shopDbQuery("select orders_status_id, orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".$lang."'");
			while ($orders_status = shopDbFetchArray($orders_status_query)) {
				$orders_statuses[] = array ('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
				$orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
			}

			$smarty = new Smarty;

			if (HelperFunctions::isGambio()) {
				$gm_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
				if ($gm_logo_mail->logo_use == '1') {
					$smarty->assign('gm_logo_mail', $gm_logo_mail->get_logo());
				}
			}

			$smarty->assign('language', $_SESSION['language']);
			$smarty->caching = false;
			$smarty->template_dir = DIR_FS_CATALOG.'templates';
			$smarty->compile_dir = DIR_FS_CATALOG.'templates_c';
			$smarty->config_dir = DIR_FS_CATALOG.'lang';
			$smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
			$smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');
			$smarty->assign('NAME', $check_status['customers_name']);
			$smarty->assign('ORDER_NR', $oID);
			$smarty->assign('ORDER_LINK', shopCatalogHrefLink(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id='.$oID, 'SSL'));
			$smarty->assign('ORDER_DATE', shopDateLong($check_status['date_purchased']));
			$smarty->assign('ORDER_STATUS', $orders_status_array[$status]);

			if (defined('EMAIL_SIGNATURE')) {
				$smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
				$smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
			}
			$language_id = 0;
			$language_id_query = shopDbQuery("SELECT languages_id FROM ".TABLE_LANGUAGES." WHERE directory = '".$order->info['language']."' LIMIT 1");
			while ($language_id_array = shopDbFetchArray($language_id_query)) {
				$language_id = $language_id_array['languages_id'];
			}

			$smarty->assign('NOTIFY_COMMENTS', nl2br($notify_comments));
			$html_mail = fetch_email_template($smarty, 'change_order_mail', 'html', 'admin/', $language_id, $order->info['language']);
			$txt_mail = fetch_email_template($smarty, 'change_order_mail', 'txt', 'admin/', $language_id, $order->info['language']);

			if (!HelperFunctions::isGambio()) {
				shopDbMail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'], $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_BILLING_SUBJECT, $html_mail, $txt_mail);
			} else {
				if ($_SESSION['language'] == 'german') {
					shopDbMail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'], $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', 'Ihre Bestellung '.$oID.', '.shopDateLong($check_status['date_purchased']).', '.$check_status['customers_name'], $html_mail, $txt_mail);
				} else {
					shopDbMail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'], $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', 'Your Order '.$oID.', '.shopDateLong($check_status['date_purchased']).', '.$check_status['customers_name'], $html_mail, $txt_mail);
				}
			}

			$customer_notified = '1';
		}

		//if gambio: $customer_notified must be "1" to make information visibible for customer
		if (HelperFunctions::isGambio())
		{
			if ($status != '-1')
			{
				$customerNotified = '1';
			}
			else // $status == '-1'
			{
				$status = DEFAULT_ORDERS_STATUS_ID;
				$last_status_query = sprintf('SELECT orders_status_id FROM orders_status_history WHERE orders_id = %d ORDER BY date_added DESC LIMIT 1', $orderId);
				$result = xtc_db_query($last_status_query);
				while($row = xtc_db_fetch_array($result))
				{
					$status = $row['orders_status_id'];
				}
			}
		}

		shopDbQuery("INSERT INTO ".TABLE_ORDERS_STATUS_HISTORY." (orders_id, orders_status_id, date_added, customer_notified, comments) VALUES ('".shopDbInput($oID)."', '".shopDbInput($status)."', now(), '".$customer_notified."', '".shopDbInput($comments)."')");
		$order_updated = true;
	}

	if ($order_updated) {
		$messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
	} else {
		$messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
	}

	return;
}


///////////////////////////////////////////////////////
// Following functions only wrap shop-core-functions //
///////////////////////////////////////////////////////

function shopGetTaxRate($class_id){
	return xtc_get_tax_rate($class_id);
}


function shopDbQuery($query){
	return xtc_db_query($query);
}


function shopDbPerform($table, $data, $action = 'insert', $parameters = '', $link = 'db_link'){
	return xtc_db_perform($table, $data, $action, $parameters, $link);
}


function shopDbFetchArray($query){
	return xtc_db_fetch_array($query);
}


function shopDbPrepareInput($string){
	return xtc_db_prepare_input($string);
}


function shopDbInput($string){
	return xtc_db_input($string);
}


function shopDbNumRows($result){
	return xtc_db_num_rows($result);
}


function shopDbOutput($string) {
	return xtc_db_output($string);
}


function shopAdressFormat($address_format_id, $address, $html, $boln, $eoln){
	return xtc_address_format($address_format_id, $address, $html, $boln, $eoln);
}


function shopDisplayTaxValue($value){
	return xtc_display_tax_value($value);
}


function shopAddTax($price, $tax){
	return xtc_add_tax($price, $tax);
}


function shopDatetimeShort($raw_datetime){
	return xtc_datetime_short($raw_datetime);
}


function shopImage($src,$alt){
	return xtc_image($src,$alt);
}


function shopDrawForm($name, $action, $parameters){
	return xtc_draw_form($name, $action, $parameters);
}


function shopGetAllGetParams($exclude_array){
	return xtc_get_all_get_params($exclude_array);
}


function shopDrawTextAreaField($name, $wrap, $width, $height, $text){
	return xtc_draw_textarea_field($name, $wrap, $width, $height, $text);
}


function shopDrawCheckboxField($name ,$value, $checked){
	return xtc_draw_checkbox_field($name ,$value, $checked);
}


function shopDrawPulldownMenu($name, $values,$default){
	return xtc_draw_pull_down_menu($name, $values,$default);
}


function shopDrawRadioField($name, $value, $checked, $parameters = ''){
	return xtc_draw_radio_field($name,$value,$checked,$parameters);
}


function shopHrefLink($page, $parameters){
	return xtc_href_link($page,$parameters);
}


function shopDbMail($from_email_address, $from_email_name, $to_email_address, $to_name, $forwarding_to, $reply_address, $reply_address_name, $path_to_attachement, $path_to_more_attachements, $email_subject, $message_body_html, $message_body_plain){
	xtc_php_mail($from_email_address, $from_email_name, $to_email_address, $to_name, $forwarding_to, $reply_address, $reply_address_name, $path_to_attachement, $path_to_more_attachements, $email_subject, $message_body_html, $message_body_plain);

	return;
}


function shopCatalogHrefLink($page, $parameters){
	return xtc_catalog_href_link($page, $parameters);
}


function shopDateLong($raw_date){
	return xtc_date_long($raw_date);
}
