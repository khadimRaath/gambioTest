<?php
/* --------------------------------------------------------------
   paypal_checkout.php 2014-07-15 misc
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

*
 * Project:   	xt:Commerce - eCommerce Engine
 * @version $Id
 *
 * xt:Commerce - Shopsoftware
 * (c) 2003-2007 xt:Commerce (Winger/Zanier), http://www.xt-commerce.com
 *
 * xt:Commerce ist eine gesch?tzte Handelsmarke und wird vertreten durch die xt:Commerce GmbH (Austria)
 * xt:Commerce is a protected trademark and represented by the xt:Commerce GmbH (Austria)
 *
 * @copyright Copyright 2003-2007 xt:Commerce (Winger/Zanier), www.xt-commerce.com
 * @copyright based on Copyright 2002-2003 osCommerce; www.oscommerce.com
 * @copyright Porttions Copyright 2003-2007 Zen Cart Development Team
 * @copyright Porttions Copyright 2004 DevosC.com
 * @license http://www.xt-commerce.com.com/license/2_0.txt GNU Public License V2.0
 *
 * For questions, help, comments, discussion, etc., please join the
 * xt:Commerce Support Forums at www.xt-commerce.com
 *
 * ab 15.08.2008 Teile vom Hamburger-Internetdienst geändert
 * Hamburger-Internetdienst Support Forums at www.forum.hamburger-internetdienst.de
 * Stand: 09.01.2011
 */
require_once(DIR_FS_INC . 'xtc_write_user_info.inc.php');

if(!function_exists('xtc_get_zone_code')) {
	include(DIR_FS_INC . 'xtc_get_zone_code.inc.php');
}

define('PROXY_HOST', '127.0.0.1');
define('PROXY_PORT', '808');
define('VERSION', '71.0');

class paypal_checkout_ORIGIN {

	var $API_UserName,
		$API_Password,
	    $API_Signature,
	    $API_Endpoint,
	    $version,
	    $location_error,
	    $NOTIFY_URL,
	    $EXPRESS_CANCEL_URL,
	    $EXPRESS_RETURN_URL,
	    $CANCEL_URL,
	    $RETURN_URL,
	    $EXPRESS_URL,
	    $IPN_URL,
	    $ppAPIec,
	    $ppAPIdp,
	    $payPalURL,
		$pp_module,
		$v_pp_log_token,
		$real_products_amount = 0,
		$module_version = '1.0.6',
		$api_tool_link = 'https://www.paypal.com/de/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true';

	public function __construct() {
		if(PAYPAL_MODE == 'sandbox') {
		$this->API_UserName 	= PAYPAL_API_SANDBOX_USER;
		$this->API_Password 	= PAYPAL_API_SANDBOX_PWD;
		$this->API_Signature	= PAYPAL_API_SANDBOX_SIGNATURE;
		$this->API_Endpoint 	= 'https://api-3t.sandbox.paypal.com/nvp';
		$this->EXPRESS_URL		= 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
		$this->IPN_URL			= 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		} elseif(PAYPAL_MODE == 'live') {
		$this->API_UserName 	= PAYPAL_API_USER;
		$this->API_Password 	= PAYPAL_API_PWD;
		$this->API_Signature	= PAYPAL_API_SIGNATURE;
		$this->API_Endpoint 	= 'https://api-3t.paypal.com/nvp';
		$this->EXPRESS_URL		= 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
		$this->IPN_URL			= 'https://www.paypal.com/cgi-bin/webscr';
		}

		$this->NOTIFY_URL 			= GM_HTTP_SERVER.DIR_WS_CATALOG.'callback/paypal/ipn.php';
		$this->EXPRESS_CANCEL_URL 	= GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_SHOPPING_CART		.'?XTCsid='.xtc_session_id().'&error=true&gm_paypal_error=2';
		$this->EXPRESS_RETURN_URL 	= GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_PAYPAL_CHECKOUT	.'?XTCsid='.xtc_session_id();
		$this->PRE_CANCEL_URL		= GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT	.'?XTCsid='.xtc_session_id();
		$this->CANCEL_URL 			= GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT	.'?XTCsid='.xtc_session_id().'&error=true&gm_paypal_error=1&error_message=PAYPAL_ERROR';
		$this->RETURN_URL 			= GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PROCESS	.'?XTCsid='.xtc_session_id();
		$this->GM_SUCCESS_URL 		= GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_SUCCESS	.'?XTCsid='.xtc_session_id();

		$this->version		= VERSION;
		$this->USE_PROXY	= FALSE;
		$this->payPalURL	= '';

		$this->ppAPIec = $this->buildAPIKey(PAYPAL_API_KEY, 'ec');
		$this->ppAPIdp = $this->buildAPIKey(PAYPAL_API_KEY, 'dp');
		$this->v_pp_log_token = PAYPAL_MODE;
	}

	/**
	 * returns the PayPal-Modul version
	 *
	 * @return string Modulversion
	 * @access public
	 */
	function get_version() {
		return $this->module_version;
	}

	/**
	 * returns the PayPal API-Tool link
	 *
	 * @return string PayPal API-Tool link
	 * @access public
	 */
	function get_api_link() {
		return $this->api_tool_link;
	}

	function build_express_checkout_button($total, $currency) {
		global $PHP_SELF;

		$button = '';
		if (!isset ($_SESSION['customer_id'])
				&& ($_SESSION['cart']->content_type == 'virtual'
					|| ($_SESSION['cart']->content_type == 'virtual_weight')
					|| ($_SESSION['cart']->content_type == 'mixed'))) {
			return $button;
		}

		if(MODULE_PAYMENT_PAYPALEXPRESS_STATUS=='True'){
			if ($_SESSION['languages_id']=='2') { // de
				$source = 'https://www.paypal.com/de_DE/i/btn/btn_xpressCheckout.gif';
			} else {
				$source = 'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif';
			}
			$button .= '<a class="paypal_checkout" href="'.xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array ('action')).'action=paypal_express_checkout').'"><img src="'.$source.'" border="0"></a>';

			return $button;
		}
	}

	function paypal_auth_call($force_redirect = '') {
		global $order, $insert_id, $xtPrice;

		// Shipping:
		if(!isset ($_SESSION['sendto'])) {
			$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
		} else {
			// verify the selected shipping address
			$check_address_query = xtc_db_query("select count(*) as total from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $_SESSION['customer_id']."' and address_book_id = '".(int) $_SESSION['sendto']."'");
			$check_address = xtc_db_fetch_array($check_address_query);

			if ($check_address['total'] != '1') {
				$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
				if(isset($_SESSION['shipping'])) {
					unset($_SESSION['shipping']);
				}
			}
		}
		// Shipping END

		// cleanup session
		unset($_SESSION['reshash'])	;
		unset($_SESSION['nvpReqArray'])	;

		/* The returnURL is the location where buyers return when a
		payment has been succesfully authorized.
		The cancelURL is the location buyers are sent to when they hit the
		cancel button during authorization of payment during the PayPal flow
		*/
		$gm_success_url		= urlencode($this->GM_SUCCESS_URL);
		$returnURL			= urlencode($this->EXPRESS_RETURN_URL);
		$cancelURL			= urlencode($this->EXPRESS_CANCEL_URL);
		$paymentType		= urlencode(PAYPAL_EXPRESS_PAYMENTACTION);
		$paymentAmount		= round($_SESSION['cart']->show_total(), 2);
		$currencyCodeType	= $_SESSION['currency'];
		// PayPal
		$this->pp_module = 'paypal_express';
		if($force_redirect != '' && $force_redirect == 'checkout_process') {
			$this->pp_module = 'paypal';
			// BOF GM_MOD
			if($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
			{
				$paymentAmount = round($order->info['total'] + $order->info['tax'], 2);
			}
			else
			{
				$paymentAmount = round($order->info['total'], 2);
			}
			// EOF GM_MOD
			$returnURL			= urlencode($this->RETURN_URL);
			$cancelURL			= urlencode($this->CANCEL_URL);
			$paymentType		= urlencode(PAYPAL_EXPRESS_PAYMENTACTION);
			$currencyCodeType	= $order->info['currency'];
		}
		$_SESSION['payment_type'] = $paymentType;

		/* Construct the parameter string that describes the PayPal payment
		the varialbes were set in the web form, and the resulting string
		is stored in $nvpstr
		*/
		$sh_name		= urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $order->delivery['firstname'] . ' ' . $order->delivery['lastname']));
		$sh_street		= urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $order->delivery['street_address']));
		$sh_city		= urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $order->delivery['city']));
		//$sh_state		= urlencode(xtc_get_zone_code($order->delivery['country']['id'], $order->delivery['zone_id'], ''));
		$sh_country		= urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $order->delivery['country']['iso_code_2']));
		$sh_phonenum	= urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $order->customer['telephone']));
		$sh_zip			= urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $order->delivery['postcode']));

		// BOF GM_MOD
		# GM_MOD: zone_id-fix
		if(is_array($order->delivery['country'])) {
			$t_country_iso_code_2 = $order->delivery['country']['iso_code_2'];
		} else {
			$t_country_iso_code_2 = $order->delivery['country'];
		}

		$t_result		= xtc_db_query('SELECT countries_id FROM ' . TABLE_COUNTRIES . ' WHERE countries_iso_code_2 = "'.xtc_db_input($t_country_iso_code_2).'" OR countries_name = "'.xtc_db_input($t_country_iso_code_2).'"');
		$t_country_data = xtc_db_fetch_array($t_result);
		$t_result		= xtc_db_query("SELECT DISTINCT zone_id FROM " . TABLE_ZONES . " WHERE zone_country_id = '".xtc_db_input($t_country_data['countries_id'])."' AND zone_name = '".xtc_db_input($order->delivery['state'])."'");
		$t_zone_data	= xtc_db_fetch_array($t_result);
		$sh_state		= xtc_get_zone_code($t_country_data['countries_id'], $t_zone_data['zone_id'], '');
		$sh_state		= urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $sh_state));

		// change the locale of PayPal pages to match the language on customer
		$local_array = array(
			'AU','AT','BE','CA','CH','CN','DE',
			'ES','GB','FR','IT','NL','PL','US');

		// for paypalexpress, no adress set if user not loged in
		$t_iso_code_2 = strtoupper($_SESSION['language_code']);
		if($t_iso_code_2 == '') {
			$t_iso_code_2 = $t_country_iso_code_2;
		}

		// get the localcode
		$t_localcode = 'US';
		if(in_array($t_iso_code_2, $local_array)) {
			$t_localcode = $t_iso_code_2;
		}
		$t_localcode = urlencode($t_localcode);
		// EOF GM_MOD

		$address = '';
		if($sh_street != '') {
			$address  =
				'&PAYMENTREQUEST_0_SHIPTONAME='			.$sh_name.
				'&PAYMENTREQUEST_0_SHIPTOSTREET='		.$sh_street.
				'&PAYMENTREQUEST_0_SHIPTOCITY='			.$sh_city.
				'&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE='	.$sh_country.
				'&PAYMENTREQUEST_0_SHIPTOSTATE='		.$sh_state.
				'&PAYMENTREQUEST_0_SHIPTOZIP='			.$sh_zip.
				'&PAYMENTREQUEST_0_SHIPTOPHONENUM='		.$sh_phonenum.
				'&ADDROVERRIDE=1';
		}

		$order_details = '';
		// get the details of the order
		if(isset($_SESSION['cart'])) {
			$order_details = $this->get_order_details();
		}

		// discard paypal-cart, if the product's amount does not match payment amount
		if((string)round($this->real_products_amount, $xtPrice->get_decimal_places($_SESSION['currency'])) != (string)(double)$paymentAmount) {
			$order_details = "&L_PAYMENTREQUEST_0_NAME0=Summe";
			$order_details .= "&L_PAYMENTREQUEST_0_AMT0=".round($paymentAmount, 2);
			$order_details .= "&L_PAYMENTREQUEST_0_QTY0=1";
		}

		// BOF GM_MOD:
		$nvpstr =
			"&GIROPAYSUCCESSURL="				.$gm_success_url.
			"&GIROPAYCANCELURL="				.$cancelURL.
			"&BANKTXNPENDINGURL="				.$gm_success_url.
			"&RETURNURL="						.$returnURL.
			"&CANCELURL="						.$cancelURL.
			$address.
			"&LOCALECODE="						.$t_localcode.
			"&PAYMENTREQUEST_0_PAYMENTACTION="	.$paymentType.
			"&PAYMENTREQUEST_0_CURRENCYCODE="	.$currencyCodeType.
			"&PAYMENTREQUEST_0_AMT="			.round($paymentAmount, 2).
			"&PAYMENTREQUEST_0_DESC="			.$insert_id.
			$order_details;

		// if virtual order, NoShipping attribute must be "1"
		$t_shipping = '0';
		if ($order->content_type == 'virtual' || ($order->content_type == 'virtual_weight') || ($_SESSION['cart']->count_contents_virtual() == 0)) {
			$t_shipping = '2';
			if(isset($_SESSION['customer_id'])) {
				$t_shipping = '1';
			}
		}
		$nvpstr .= "&NOSHIPPING=".$t_shipping;

		// set the logo on the PayPal shopping cart
		if(PAYPAL_SHOP_LOGO != '') {
			$t_img_url = urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', PAYPAL_SHOP_LOGO));
			if(strlen_wrapper($t_img_url) > 120) {
				$t_img_url = '';
			}
			$nvpstr .= "&HDRIMG=".$t_img_url;
		}

		/* Make the call to PayPal to set the Express Checkout token
		If the API call succeded, then redirect the buyer to PayPal
		to begin to authorize payment.  If an error occured, show the
		resulting errors
		*/
		$resArray = $this->hash_call('SetExpressCheckout', $nvpstr);
		$_SESSION['reshash'] = $resArray;

		$ack = strtoupper($resArray['ACK']);

		 if($ack == 'SUCCESS'){
			// Redirect to paypal.com here
			$token = urldecode($resArray['TOKEN']);
			$this->payPalURL = $this->EXPRESS_URL.$token;
			return $this->payPalURL;
		} else  {
			$this->build_error_message($_SESSION['reshash']);
			$this->payPalURL = $this->EXPRESS_CANCEL_URL;
			return $this->payPalURL;
		}
	}

	/**
	 * Converts from ISO 8859-1 to UTF-8, and vice versa
	 *
	 * @param string $encoding1 Convert from
	 * @param string $encoding2 Convert to
	 * @param string $string String to convert
	 * @return string Converted string
	 */
	function mn_iconv($encoding1, $encoding2, $string){

		$t_is_utf8 = false;
		// search for UTF-8 characters
		if(preg_match('/(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+/xs', $string))
		{
			$t_is_utf8 = true;
		}

		if((strtolower_wrapper($encoding1) == strtolower_wrapper($encoding2)) || ($t_is_utf8 && strtolower_wrapper($encoding2) == 'UTF-8'))
		{
			return $string;
		} elseif(strtolower_wrapper($encoding2) == 'UTF-8') {
			// only as a replacement for iconv and only in one direction in 1251 to UTF8
			//ISO 8859-1 to UTF-8
			if(function_exists('utf8_encode')) {
				return utf8_encode($string);
			} else {
				$string = preg_replace_callback("/([\x80-\xFF]/)",function($matches){return "chr(0xC0|ord('".$matches[1]."')>>6).chr(0x80|ord('".$matches[1]."')&0x3F)";}, $string);
				return $string;
			}
		} elseif(strtolower_wrapper($encoding1) == 'UTF-8') {
			//UTF-8 to ISO 8859-1
			if(function_exists('utf8_decode')) {
				return utf8_decode($string);
			} else {
				$string = preg_replace_callback("/([\xC2\xC3])([\x80-\xBF])/",function($matches){return "chr(ord('".$matches[1]."')<<6&0xC0|ord('".$matches[2]."')&0x3F)";}, $string);
				return $string;
			}
		} elseif(function_exists('iconv')) {
			return iconv($encoding1, $encoding2, $string);
		} else {
			// no conversion is possible
			return $string;
		}
	}

	/**
	 * gets an array with order details
	 *
	 * this function returns an string with information from the order
	 * included the order total informations
	 *
	 * return string $t_order_total string with product infos from order
	 */
	function get_order_details() {
		global $insert_id, $xtPrice;

		// get the product from the cart
		$t_products = $_SESSION['cart']->get_products();

		$order_totals = array();

		// get order total from DB
		$t_sql = "
			SELECT
				class, title, value
			FROM
				" . TABLE_ORDERS_TOTAL . "
			WHERE
				orders_id = '" . (int)$insert_id . "'";
		$t_query = xtc_db_query($t_sql, 'db_link', false);
		while($t_result = xtc_db_fetch_array($t_query, false)) {
			$order_totals[] = $t_result;
		}

		$i = 0;
		$t_order_total = '';
		// format products
		foreach($t_products as $t_key => $t_value) {

			if(empty($t_value['unit_name'])) $t_unit = 'x';

			// paypal does not support decimal quantities -> do workaround and write quantity in name
			if((string)round((double)$t_value['quantity']) !== (string)(double)$t_value['quantity'])
			{
				$t_quantity = str_replace('.', $xtPrice->currencies[$_SESSION['currency']]['decimal_point'], (string)(double)$t_value['quantity']);
				$t_product_name = '('.$t_quantity.' '.$t_unit.') '.strip_tags($t_value['name']);
				$t_product_amount = $t_value['quantity'] * $t_value['final_price'];
				$t_product_qty = 1;
				$this->real_products_amount += $t_product_amount;
			}
			else
			{
				$t_product_qty = (int)$t_value['quantity'];
				$t_product_amount = $t_value['final_price'];
				$t_product_name = strip_tags($t_value['name']);
				$this->real_products_amount += ($t_product_amount * $t_value['quantity']);
			}

			$t_order_total .= "&L_PAYMENTREQUEST_0_NAME"	. $i . "=" . urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $t_product_name));
			$t_order_total .= "&L_PAYMENTREQUEST_0_AMT"		. $i . "=" . round(urlencode($t_product_amount), 2);
			$t_order_total .= "&L_PAYMENTREQUEST_0_QTY"		. $i . "=" . round(urlencode($t_product_qty), 2);

			$i++;
		}

		// format order total
		if(count($order_totals) > 0) {
			foreach($order_totals as $t_key => $t_value) {
				switch($t_value['class']) {
					case 'ot_shipping':
						$t_order_total .= "&L_PAYMENTREQUEST_0_NAME". $i . "="	. urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $t_value['title']));
						$t_order_total .= "&L_PAYMENTREQUEST_0_AMT"	. $i . "="	. round(urlencode($t_value['value']), 2);
						$t_order_total .= "&L_PAYMENTREQUEST_0_QTY"	. $i . "=1";
						$this->real_products_amount += $t_value['value'];
						$i++;
						break;
					case 'ot_tax':
						if($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
						{
							$t_order_total .= "&L_PAYMENTREQUEST_0_NAME". $i . "="	. urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $t_value['title']));
							$t_order_total .= "&L_PAYMENTREQUEST_0_AMT"	. $i . "="	. round(urlencode($t_value['value']), 2);
							$t_order_total .= "&L_PAYMENTREQUEST_0_QTY"	. $i . "=1";
							$this->real_products_amount += $t_value['value'];
							$i++;
						}
						break;
					case 'ot_discount':
						$t_order_total .= "&L_PAYMENTREQUEST_0_NAME". $i . "="	. urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $t_value['title']));
						$t_order_total .= "&L_PAYMENTREQUEST_0_AMT"	. $i . "="	. round(urlencode($t_value['value']), 2);
						$t_order_total .= "&L_PAYMENTREQUEST_0_QTY"	. $i . "=1";
						$this->real_products_amount += $t_value['value'];
						$i++;
						break;
					case 'ot_coupon':
						$ot_coupon_value = $t_value['value'] * -1;
						$t_order_total .= "&L_PAYMENTREQUEST_0_NAME". $i . "="	. urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $t_value['title']));
						$t_order_total .= "&L_PAYMENTREQUEST_0_AMT"	. $i . "="	. round(urlencode($ot_coupon_value), 2);
						$t_order_total .= "&L_PAYMENTREQUEST_0_QTY"	. $i . "=1";
						$this->real_products_amount += $ot_coupon_value;
						$i++;
						break;
					case 'ot_gv':
						$ot_gv_value = $t_value['value'] * -1;
						$t_order_total .= "&L_PAYMENTREQUEST_0_NAME". $i . "="	. urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $t_value['title']));
						$t_order_total .= "&L_PAYMENTREQUEST_0_AMT"	. $i . "="	. round(urlencode($ot_gv_value), 2);
						$t_order_total .= "&L_PAYMENTREQUEST_0_QTY"	. $i . "=1";
						$this->real_products_amount += $ot_gv_value;
						$i++;
						break;
					case 'ot_bonus_fee':
						$ot_bonus_fee_value = $t_value['value'] * -1;
						$t_order_total .= "&L_PAYMENTREQUEST_0_NAME". $i . "="	. urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $t_value['title']));
						$t_order_total .= "&L_PAYMENTREQUEST_0_AMT"	. $i . "="	. round(urlencode($ot_bonus_fee_value), 2);
						$t_order_total .= "&L_PAYMENTREQUEST_0_QTY"	. $i . "=1";
						$this->real_products_amount += $ot_bonus_fee_value;
						$i++;
						break;
					case 'ot_payment':
						$t_order_total .= "&L_PAYMENTREQUEST_0_NAME". $i . "="	. urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $t_value['title']));
						$t_order_total .= "&L_PAYMENTREQUEST_0_AMT"	. $i . "="	. round(urlencode($t_value['value']), 2);
						$t_order_total .= "&L_PAYMENTREQUEST_0_QTY"	. $i . "=1";
						$this->real_products_amount += $t_value['value'];
						$i++;
						break;
					case 'ot_cod_fee':
						$t_order_total .= "&L_PAYMENTREQUEST_0_NAME". $i . "="	. urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $t_value['title']));
						$t_order_total .= "&L_PAYMENTREQUEST_0_AMT"	. $i . "="	. round(urlencode($t_value['value']), 2);
						$t_order_total .= "&L_PAYMENTREQUEST_0_QTY"	. $i . "=1";
						$this->real_products_amount += $t_value['value'];
						$i++;
						break;
					case 'ot_ps_fee':
						$t_order_total .= "&L_PAYMENTREQUEST_0_NAME". $i . "="	. urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $t_value['title']));
						$t_order_total .= "&L_PAYMENTREQUEST_0_AMT"	. $i . "="	. round(urlencode($t_value['value']), 2);
						$t_order_total .= "&L_PAYMENTREQUEST_0_QTY"	. $i . "=1";
						$this->real_products_amount += $t_value['value'];
						$i++;
						break;
					case 'ot_loworderfee':
						$t_order_total .= "&L_PAYMENTREQUEST_0_NAME". $i . "="	. urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $t_value['title']));
						$t_order_total .= "&L_PAYMENTREQUEST_0_AMT"	. $i . "="	. round(urlencode($t_value['value']), 2);
						$t_order_total .= "&L_PAYMENTREQUEST_0_QTY"	. $i . "=1";
						$this->real_products_amount += $t_value['value'];
						$i++;
						break;
					case 'ot_gambioultra':
						$t_order_total .= "&L_PAYMENTREQUEST_0_NAME". $i . "="	. urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $t_value['title']));
						$t_order_total .= "&L_PAYMENTREQUEST_0_AMT"	. $i . "="	. round(urlencode($t_value['value']), 2);
						$t_order_total .= "&L_PAYMENTREQUEST_0_QTY"	. $i . "=1";
						$this->real_products_amount += $t_value['value'];
						$i++;
						break;
				}
			}
		}

		return $t_order_total;
	}

	function paypal_get_customer_data(){
		/* Make the API call and store the results in an array.  If the
		call was a success, show the authorization details, and provide
		an action to complete the payment.  If failed, show the error
		*/
		$nvpstr = "&TOKEN=".$_SESSION['reshash']['TOKEN'];
		$resArray = $this->hash_call("GetExpressCheckoutDetails", $nvpstr);
		$_SESSION['reshash'] = array_merge((array)$_SESSION['reshash'], $resArray) ;
		$ack = strtoupper($resArray["ACK"]);

		if($ack == "SUCCESS"){
			$_SESSION['paypal_express_checkout'] = true;
			$_SESSION['paypal_express_payment_modules'] = 'paypalexpress.php';

			$this->check_customer();
		} else  {
			$this->build_error_message($_SESSION['reshash']);
			$this->payPalURL = $this->EXPRESS_CANCEL_URL;
			return $this->payPalURL;
		}
	}

	function check_customer(){

		if (!isset ($_SESSION['customer_id'])) {
			$check_customer_query = xtc_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address = '".xtc_db_input($_SESSION['reshash']['EMAIL'])."' and account_type = '0'");
			if (!xtc_db_num_rows($check_customer_query)) {
				$this->create_account();
			}else{
				$check_customer_query = xtc_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address = '".xtc_db_input($_SESSION['reshash']['EMAIL'])."' and account_type = '0'");
				$check_customer = xtc_db_fetch_array($check_customer_query);
				$this->login_customer($check_customer);
				if(PAYPAL_EXPRESS_ADDRESS_OVERRIDE == 'true' && $_SESSION['pp_allow_address_change']!='true'){
					$this->create_shipping_address($check_customer);
				}
			}
		}else{
			if(PAYPAL_EXPRESS_ADDRESS_OVERRIDE == 'true' && $_SESSION['pp_allow_address_change']!='true'){
				$check_customer_query = xtc_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".xtc_db_input($_SESSION['customer_id'])."' and account_type = '0'");
				$check_customer = xtc_db_fetch_array($check_customer_query);
				$this->create_shipping_address($check_customer);
			}
		}
	}

	function create_account(){

		//$gender = xtc_db_prepare_input($_POST['gender']);

		$firstname = xtc_db_prepare_input($_SESSION['reshash']['FIRSTNAME']);
		$lastname = xtc_db_prepare_input($_SESSION['reshash']['LASTNAME']);
		$email_address = xtc_db_prepare_input($_SESSION['reshash']['EMAIL']);
		$company = xtc_db_prepare_input($_SESSION['reshash']['BUSINESS']);
		$street_address = xtc_db_prepare_input($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTOSTREET']);
		$postcode = xtc_db_prepare_input($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTOZIP']);
		$city = xtc_db_prepare_input($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTOCITY']);
		$state = xtc_db_prepare_input($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTOSTATE']);
		$telephone = xtc_db_prepare_input($_SESSION['reshash']['PHONENUM']);

		$country_query = xtc_db_query("select * from ".TABLE_COUNTRIES." where countries_iso_code_2 = '".xtc_db_input($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'])."' ");
		$tmp_country = xtc_db_fetch_array($country_query);

		$country = xtc_db_prepare_input($tmp_country['countries_id']);

		$customers_status = DEFAULT_CUSTOMERS_STATUS_ID;

		$sql_data_array = array (
			'customers_status' => $customers_status,
			'customers_firstname' => $firstname,
			'customers_lastname' => $lastname,
			'customers_email_address' => $email_address,
			'customers_telephone' => $telephone,
			'customers_date_added' => 'now()',
			'customers_last_modified' => 'now()');

		xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array);

		$_SESSION['paypal_express_new_customer'] = 'true';

		$_SESSION['customer_id'] = xtc_db_insert_id();
		$user_id = xtc_db_insert_id();
		xtc_write_user_info($user_id);
		$sql_data_array = array (
			'customers_id' => $_SESSION['customer_id'],
			'entry_firstname' => $firstname,
			'entry_lastname' => $lastname,
			'entry_street_address' => $street_address,
			'entry_postcode' => $postcode,
			'entry_city' => $city,
			'entry_country_id' => $country,
			'address_date_added' => 'now()',
			'address_last_modified' => 'now()'
		);

		if (ACCOUNT_COMPANY == 'true')
			$sql_data_array['entry_company'] = $company;
		if (ACCOUNT_SUBURB == 'true')
			$sql_data_array['entry_suburb'] = $suburb;
		if (ACCOUNT_STATE == 'true') {
				$t_zone_id = $this->getZoneId($country, $state);
				$sql_data_array['entry_zone_id'] = $t_zone_id;
				$sql_data_array['entry_state'] = $state;
		}

		xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

		$address_id = xtc_db_insert_id();

		xtc_db_query("update " . TABLE_CUSTOMERS . " set customers_default_address_id = '" . $address_id . "', customers_cid = '" . (int) $_SESSION['customer_id'] . "' where customers_id = '" . (int) $_SESSION['customer_id'] . "'");

		xtc_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . (int) $_SESSION['customer_id'] . "', '0', now())");

		if (isset ($_SESSION['tracking']['refID'])) {
			$campaign_check_query_raw = "SELECT *
						                            FROM " . TABLE_CAMPAIGNS . "
						                            WHERE campaigns_refID = '" . $_SESSION[tracking][refID] . "'";
			$campaign_check_query = xtc_db_query($campaign_check_query_raw);
			if (xtc_db_num_rows($campaign_check_query) > 0) {
				$campaign = xtc_db_fetch_array($campaign_check_query);
				$refID = $campaign['campaigns_id'];
			} else {
				$refID = 0;
			}

			xtc_db_query("update " . TABLE_CUSTOMERS . " set
			                                 refferers_id = '" . $refID . "'
			                                 where customers_id = '" . (int) $_SESSION['customer_id'] . "'");

			$leads = $campaign['campaigns_leads'] + 1;
			xtc_db_query("update " . TABLE_CAMPAIGNS . " set
					                         campaigns_leads = '" . $leads . "'
			                                 where campaigns_id = '" . $refID . "'");
		}

		if (ACTIVATE_GIFT_SYSTEM == 'true') {
			// GV Code Start
			// ICW - CREDIT CLASS CODE BLOCK ADDED  ******************************************************* BEGIN
			if (NEW_SIGNUP_GIFT_VOUCHER_AMOUNT > 0) {
				$coupon_code = create_coupon_code();
				$insert_query = xtc_db_query("insert into " . TABLE_COUPONS . " (coupon_code, coupon_type, coupon_amount, date_created) values ('" . $coupon_code . "', 'G', '" . NEW_SIGNUP_GIFT_VOUCHER_AMOUNT . "', now())");
				$insert_id = xtc_db_insert_id($insert_query);
				$insert_query = xtc_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $insert_id . "', '0', 'Admin', '" . $email_address . "', now() )");

				$_SESSION['reshash']['SEND_GIFT'] = 'true';
				$_SESSION['reshash']['GIFT_AMMOUNT'] = $xtPrice->xtcFormat(NEW_SIGNUP_GIFT_VOUCHER_AMOUNT, true);
				$_SESSION['reshash']['GIFT_CODE'] = $coupon_code;
				$_SESSION['reshash']['GIFT_LINK'] = xtc_href_link(FILENAME_GV_REDEEM, 'gv_no=' . $coupon_code, 'NONSSL', false);

			}
			if (NEW_SIGNUP_DISCOUNT_COUPON != '') {
				$coupon_code = NEW_SIGNUP_DISCOUNT_COUPON;
				$coupon_query = xtc_db_query("select * from " . TABLE_COUPONS . " where coupon_code = '" . $coupon_code . "'");
				$coupon = xtc_db_fetch_array($coupon_query);
				$coupon_id = $coupon['coupon_id'];
				$coupon_desc_query = xtc_db_query("select * from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $coupon_id . "' and language_id = '" . (int) $_SESSION['languages_id'] . "'");
				$coupon_desc = xtc_db_fetch_array($coupon_desc_query);
				$insert_query = xtc_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $coupon_id . "', '0', 'Admin', '" . $email_address . "', now() )");

				$_SESSION['reshash']['SEND_COUPON'] = 'true';
				$_SESSION['reshash']['COUPON_DESC'] = $coupon_desc['coupon_description'];
				$_SESSION['reshash']['COUPON_CODE'] = $coupon['coupon_code'];

			}
			// ICW - CREDIT CLASS CODE BLOCK ADDED  ******************************************************* END
			// GV Code End       // create templates
		}

		$_SESSION['ACCOUNT_PASSWORD'] = 'true';

		// Login Customer
		$check_customer_query = xtc_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address = '".xtc_db_input($email_address)."' and account_type = '0'", 'db_link', false);
		$check_customer = xtc_db_fetch_array($check_customer_query);
		$this->login_customer($check_customer);
		if(PAYPAL_EXPRESS_ADDRESS_OVERRIDE == 'true'){
			$this->create_shipping_address($check_customer);
		}

	}

	function login_customer($check_customer){

			if (SESSION_RECREATE == 'True') {
				xtc_session_recreate();
			}

			$check_country_query = xtc_db_query("select entry_country_id, entry_zone_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $check_customer['customers_id']."' and address_book_id = '".$check_customer['customers_default_address_id']."'");
			$check_country = xtc_db_fetch_array($check_country_query);

			$_SESSION['customer_gender'] = $check_customer['customers_gender'];
			$_SESSION['customer_first_name'] = $check_customer['customers_firstname'];
			$_SESSION['customer_last_name'] = $check_customer['customers_lastname'];
			$_SESSION['customer_id'] = $check_customer['customers_id'];
			$_SESSION['customer_vat_id'] = $check_customer['customers_vat_id'];
			$_SESSION['customer_default_address_id'] = $check_customer['customers_default_address_id'];
			$_SESSION['customer_country_id'] = $check_country['entry_country_id'];
			$_SESSION['customer_zone_id'] = $check_country['entry_zone_id'];
			$_SESSION['customer_email_address'] = $check_customer['customers_email_address'];

			$date_now = date('Ymd');

			xtc_db_query("update ".TABLE_CUSTOMERS_INFO." SET customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 WHERE customers_info_id = '".(int) $_SESSION['customer_id']."'");
			xtc_write_user_info((int) $_SESSION['customer_id']);
			// restore cart contents
			$_SESSION['cart']->restore_contents($check_customer['customers_status']);
			//$_SESSION['cart']->check_cart($check_customer['customers_status']);

	}

	function create_shipping_address($check_customer){

		//$gender = xtc_db_prepare_input($_POST['gender']);

		$pos = strrpos_wrapper($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTONAME'], ' ');
		$lenght = strlen_wrapper($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTONAME']);

		$firstname = substr_wrapper($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTONAME'], 0, $pos);
		$lastname = substr_wrapper($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTONAME'], ($pos+1), $lenght);

		$email_address = xtc_db_prepare_input($_SESSION['reshash']['EMAIL']);
		$company = xtc_db_prepare_input($_SESSION['reshash']['BUSINESS']);
		$street_address = xtc_db_prepare_input($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTOSTREET']);
		$postcode = xtc_db_prepare_input($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTOZIP']);
		$city = xtc_db_prepare_input($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTOCITY']);
		$state = xtc_db_prepare_input($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTOSTATE']);
		$telephone = xtc_db_prepare_input($_SESSION['reshash']['PHONENUM']);

		$country_query = xtc_db_query("select * from ".TABLE_COUNTRIES." where countries_iso_code_2 = '".xtc_db_input($_SESSION['reshash']['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'])."' ");
		$tmp_country = xtc_db_fetch_array($country_query);

		$country = xtc_db_prepare_input($tmp_country['countries_id']);

		$sql_data_array = array (
			'customers_id' => $_SESSION['customer_id'],
			'entry_firstname' => $firstname,
			'entry_lastname' => $lastname,
			'entry_street_address' => $street_address,
			'entry_postcode' => $postcode,
			'entry_city' => $city,
			'entry_country_id' => $country,
			'address_date_added' => 'now()',
			'address_last_modified' => 'now()',
			'address_class' => 'paypal'
		);

		if (ACCOUNT_COMPANY == 'true')
			$sql_data_array['entry_company'] = $company;
		if (ACCOUNT_STATE == 'true') {
				$t_zone_id = $this->getZoneId($country, $state);
				$sql_data_array['entry_zone_id'] = $t_zone_id;
				$sql_data_array['entry_state'] = $state;
		}

		$check_address_query = xtc_db_query("select address_book_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $_SESSION['customer_id']."' and address_class = 'paypal'");
		$check_address = xtc_db_fetch_array($check_address_query);

		if ($check_address['address_book_id']!='') {
			xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "address_book_id = '".(int) $check_address['address_book_id']."' and customers_id ='".(int) $_SESSION['customer_id']."'");
			$send_to = $check_address['address_book_id'];

		}else{
			xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
			$send_to = xtc_db_insert_id();
		}

		$_SESSION['sendto'] = $send_to;
	}

	function complete_express_ceckout($tmp_id, $data='', $check=false){
		global $xtPrice,  $order;

		if($check==true){
				$order = new order($tmp_id);
		}

		if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
			$customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else {
			$customers_ip = $_SERVER["REMOTE_ADDR"];
		}

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$total = $order->info['total'] + $order->info['tax'];
		} else {
			$total = $order->info['total'];
		}

		if($check==true){
			$total = round($order->info['pp_total'], $xtPrice->get_decimal_places($_SESSION['currency']));
		}


		$products_count = 0;
		for ($i = 0, $n = sizeof($order->products); $i < $n; $i ++) {

		$products_tax = 0;
		$products_tax = $xtPrice->xtcGetTax($order->products[$i]['price'], $order->products[$i]['tax']);

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$products_price = $order->products[$i]['price'];
		} else {
			$products_price = $order->products[$i]['price'] - products_tax;
		}

			$tmp_products .= '&L_NAME'.$i.'='.$order->products[$i]['name'].'&L_NUMBER'.$i.'='.$order->products[$i]['model'].'&L_QTY'.$i.'='.$order->products[$i]['qty'].'&L_TAXAMT'.$i.'='.$products_tax.'&L_AMT'.$i.'='. $products_price;
 			$products_count ++;
		}

		$amount = round($total, $xtPrice->get_decimal_places($_SESSION['currency']));
		if($check==true){
			$shipping = $order->info['pp_shipping'];
		}else{
			$shipping = $xtPrice->xtcFormat($order->info['shipping_cost'], false, 0, true);
		}
		$item_amt = $amount-$shipping;


		if($data['token']!=''){
			$tkn = $data['token'];
		}else{
			$tkn =  $_SESSION['nvpReqArray']['TOKEN'];
		}

		if($data['PayerID']!=''){
			$payer = $data['PayerID'];
		}else{
			$payer =  $_SESSION['reshash']['PAYERID'];
		}

		$token =urlencode($tkn);
		$paymentAmount =urlencode ($total);


		$paymentType=$_SESSION['payment_type'];

		$currCodeType = urlencode($_SESSION['currency']);
		$payerID = urlencode($payer);
		$serverName = urlencode($_SERVER['SERVER_NAME']);
		$notify_url  = urlencode($this->NOTIFY_URL);
		$inv_num = urlencode($tmp_id);
		$item_amt = urlencode($item_amt);
		$tax_amt = urlencode($order->info['tax']);
		$shipping_amt = urlencode($shipping);
		$button_source = urlencode($this->ppAPIec);

		$sh_name = urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $order->delivery['firstname'].' '.$order->delivery['lastname']));
		$sh_street = urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $order->delivery['street_address']));
		$sh_street_2 = '';
		$sh_city = urlencode($this->mn_iconv($_SESSION['language_charset'], 'UTF-8', $order->delivery['city']));


		# GM_MOD: zone_id-fix
		if(is_array($order->delivery['country'])) {
			$t_country_iso_code_2 = $order->delivery['country']['iso_code_2'];
		} else {
			$t_country_iso_code_2 = $order->delivery['country'];
		}

		$t_result = xtc_db_query('SELECT countries_id FROM ' . TABLE_COUNTRIES . ' WHERE countries_iso_code_2 = "'.xtc_db_input($t_country_iso_code_2).'" OR countries_name = "'.xtc_db_input($t_country_iso_code_2).'"');
		$t_country_data = xtc_db_fetch_array($t_result);
		$t_result = xtc_db_query("SELECT DISTINCT zone_id FROM " . TABLE_ZONES . " WHERE zone_country_id = '".xtc_db_input($t_country_data['countries_id'])."' AND zone_name = '".xtc_db_input($order->delivery['state'])."'");
		$t_zone_data = xtc_db_fetch_array($t_result);


		$sh_state = urlencode(xtc_get_zone_code($t_country_data['countries_id'], $t_zone_data['zone_id'], ''));
		if(empty($sh_state) && !empty($order->delivery['state']))
		{
			$sh_state = urlencode($order->delivery['state']);
		}

		if($check==true){
			$sh_country = urlencode($order->delivery['country_iso_2']);
		}else{
			$sh_country = urlencode($order->delivery['country']['iso_code_2']);
		}

		$sh_phonenum = urlencode($order->customer['telephone']);
		$sh_zip = urlencode($order->delivery['postcode']);

		$adress =
			'&PAYMENTREQUEST_0_SHIPTONAME='			.$sh_name.
			'&PAYMENTREQUEST_0_SHIPTOSTREET='		.$sh_street.
			'&PAYMENTREQUEST_0_SHIPTOCITY='			.$sh_city.
			'&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE='	.$sh_country.
			'&PAYMENTREQUEST_0_SHIPTOSTATE='		.$sh_state.
			'&PAYMENTREQUEST_0_SHIPTOZIP='			.$sh_zip.
			'&PAYMENTREQUEST_0_SHIPTOPHONENUM='		.$sh_phonenum;

		$nvpstr=
			'&Token='			.$token.
			'&PayerID='			.$payerID.
			'&PaymentAction='	.$paymentType.
			'&AMT='				.round($paymentAmount, 2).
			'&CURRENCYCODE='	.$currCodeType.
			'&IPADDRESS='		.$customers_ip.
			'&NotifyURL='		.$notify_url.
			'&INVNUM='			.$inv_num.$adress.
			'&ButtonSource='	.$button_source;

 		/* Make the call to PayPal to finalize payment
   		 If an error occured, show the resulting errors
    	*/
		$resArray = $this->hash_call('DoExpressCheckoutPayment',$nvpstr);
		$_SESSION['reshash'] = array_merge($_SESSION['reshash'], $resArray) ;
		$ack = strtoupper($resArray['ACK']);

		if($ack != 'SUCCESS'){
			$this->build_error_message($_SESSION['reshash']);
			$this->payPalURL = $this->EXPRESS_CANCEL_URL;
			return $this->payPalURL;
		}
	}

function doDirectPayment($data, $tmp_id){
global $xtPrice, $order;

	if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
		$customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} else {
		$customers_ip = $_SERVER["REMOTE_ADDR"];
	}

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$total = $order->info['total'] + $order->info['tax'];
		} else {
			$total = $order->info['total'];
		}

		$products_count = 0;
		for ($i = 0, $n = sizeof($order->products); $i < $n; $i ++) {

		$products_tax = 0;
		$products_tax = $xtPrice->xtcGetTax($order->products[$i]['price'], $order->products[$i]['tax']);

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$products_price = $order->products[$i]['price'];
		} else {
			$products_price = $order->products[$i]['price'] - products_tax;
		}

			$tmp_products .= '&L_NAME'.$i.'='.$order->products[$i]['name'].'&L_NUMBER'.$i.'='.$order->products[$i]['model'].'&L_QTY'.$i.'='.$order->products[$i]['qty'].'&L_TAXAMT'.$i.'='.$products_tax.'&L_AMT'.$i.'='. $products_price;
 			$products_count ++;
		}

		$amount = round($total, $xtPrice->get_decimal_places($_SESSION['currency']));
		$shipping = $xtPrice->xtcFormat($order->info['shipping_cost'], false, 0, true);
		$item_amt = $amount-$shipping;


$paymentType =urlencode('Sale');
$firstName =urlencode( $data['firstName']);
$lastName =urlencode( $data['lastName']);
$creditCardType =urlencode( $data['creditCardType']);
$creditCardNumber = urlencode($data['creditCardNumber']);
$expDateMonth =urlencode($data['expDateMonth']);
$ip_address = urlencode($customers_ip);
$notify_url  = urlencode($this->NOTIFY_URL);
$inv_num = urlencode($tmp_id);
$item_amt = urlencode($item_amt);
$shipping_amt = urlencode($shipping);
$tax_amt = urlencode($order->info['tax']);

// Month must be padded with leading zero
$padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);

$expDateYear =urlencode( $data['expDateYear']);
$cvv2Number = urlencode($data['cvv2Number']);
$address1 = urlencode($data['address1']);
$address2 = urlencode($data['address2']);
$city = urlencode($data['city']);
$state =urlencode( $data['state']);
$zip = urlencode($data['zip']);
$amount = urlencode($total);
$currencyCode=urlencode($_SESSION['currency']);
$paymentType=urlencode($paymentType);
$country_code = urlencode($data['country']);
$button_source = urlencode($this->ppAPIdp);
//////////

/* Construct the request string that will be sent to PayPal.
   The variable $nvpstr contains all the variables and is a
   name value pair string with & as a delimiter */

  $nvpstr=
	'&PAYMENTACTION='.$paymentType.
	'&AMT='.$amount.
	'&CREDITCARDTYPE='.$creditCardType.
	'&ACCT='.$creditCardNumber.
	'&EXPDATE='.$padDateMonth.$expDateYear.
	'&CVV2='.$cvv2Number.
	'&FIRSTNAME='.$firstName.
	'&LASTNAME='.$lastName.
	'&STREET='.$address1.
	'&CITY='.$city.
	'&STATE='.$state.
	'&ZIP='.$zip.
	'&COUNTRYCODE=US'.
	'&CURRENCYCODE='.$currencyCode.
	'&BUTTONSOURCE='.$button_source;


/* Make the API call to PayPal, using API signature.
   The API response is stored in an associative array called $resArray */
   $resArray=$this->hash_call("doDirectPayment",$nvpstr);
   //$_SESSION['reshash']=$resArray;

   $nvpstr_1='&TRANSACTIONID='.urlencode($resArray['TRANSACTIONID']);
   $resArray_1=$this->hash_call("getTransactionDetails",$nvpstr_1);

   $_SESSION['reshash'] = array_merge($resArray, $resArray_1) ;

/* Display the API response back to the browser.
   If the response from PayPal was a success, display the response parameters'
   If the response was an error, display the errors received using APIError.php.
   */
$ack = strtoupper($resArray["ACK"]);

		   if($ack!="SUCCESS"){
					$this->build_error_message($_SESSION['reshash']=$resArray);
				  	$this->payPalURL = $this->EXPRESS_CANCEL_URL;
				  	return $this->payPalURL;
			  }

}


	/**
	  * hash_call: Function to perform the API call to PayPal using API signature
	  * @methodName is name of API  method.
	  * @nvpStr is nvp string.
	  * returns an associtive array containing the response from the server.
	*/

	function hash_call($methodName, $nvpStr, $pp_token = '')
	{
		// BOF GM_MOD
		// if API check, no logging
		$t_check_api = false;
		if($nvpStr == 'CHECK_API') {
			$nvpStr = '';
			$t_check_api = true;
		}
		// EOF GM_MOD

		$this->_logAPICall('START_CALL '.$this->pp_module.' ' . $methodName);

		if(function_exists('curl_init')) {
			//setting the curl parameters.
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->API_Endpoint.$pp_token);
			curl_setopt($ch, CURLOPT_VERBOSE, 0);

			//turning off the server and peer verification(TrustManager Concept).
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_SSLVERSION, 3);
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			//if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
		   //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php
			if($this->USE_PROXY) {
				curl_setopt ($ch, CURLOPT_PROXY, PROXY_HOST.":".PROXY_PORT);
			}

			//NVPRequest for submitting to server
			$nvpreq =
				'METHOD='		.urlencode($methodName).
				'&VERSION='		.urlencode($this->version).
				'&PWD='			.urlencode($this->API_Password).
				'&USER='		.urlencode($this->API_UserName).
				'&SIGNATURE='	.urlencode($this->API_Signature).
				$nvpStr;

			//setting the nvpreq as POST FIELD to curl
			curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);

			//GM_MOD:
			if(!$t_check_api) {
				$this->_logAPICall($this->API_Endpoint.$pp_token .' + '. $nvpreq.'&CLIENT='.$_SERVER['HTTP_USER_AGENT']);
			}

			//getting response from server
			$response = curl_exec($ch);

			//GM_MOD:
			if(!$t_check_api) {
				$this->_logAPICall('RESPONSE: '.$response);
			}

			//convrting NVPResponse to an Associative Array
			$nvpResArray = $this->deformatNVP($response);
			$nvpReqArray = $this->deformatNVP($nvpreq);

			$_SESSION['nvpReqArray'] = $nvpReqArray;

			if(curl_errno($ch)) {
				// moving to display page to display curl errors
				$t_curl_error = curl_errno($ch);
				$_SESSION['curl_error_no'] = $t_curl_error;
				$_SESSION['curl_error_msg'] = $t_curl_error;
				$this->build_error_message($_SESSION['reshash']);
				// $this->payPalURL = $this->EXPRESS_CANCEL_URL;
				// return $this->payPalURL;
			}

			//closing the curl
			curl_close($ch);
		} else {
			// BOF GM_MOD
			$nvpreq =
				"METHOD="		.urlencode($methodName).
				"&VERSION="		.urlencode($this->version).
				"&PWD="			.urlencode($this->API_Password).
				"&USER="		.urlencode($this->API_UserName).
				"&SIGNATURE="	.urlencode($this->API_Signature).$nvpStr;

			$request_post = array(
				'http' => array(
					'method' => 'POST',
					'header' => "Content-type: application/x-www-form-urlencoded\r\n",
					'content' => $nvpreq));

			if(!$t_check_api) {
				$this->_logAPICall('STREAM '.$this->API_Endpoint.$pp_token .' + '. $nvpreq);
			}

			$request		= stream_context_create($request_post);
			$response		= file_get_contents($this->API_Endpoint.$pp_token, false, $request);
			$nvpResArray	= $this->deformatNVP($response);
			$nvpReqArray	= $this->deformatNVP($nvpreq);

			if(!$t_check_api) {
				$this->_logAPICall('STREAM RESPONSE: '.$response);
			}

			$_SESSION['nvpReqArray'] = $nvpReqArray;
			// EOF GM_MOD
		}
		$this->_logAPICall('END_CALL '.$this->pp_module.' ' . $methodName);

		return $nvpResArray;
	}

	/** This function will take NVPString and convert it to an Associative Array and it will decode the response.
	  * It is usefull to search for a particular key and displaying arrays.
	  * @nvpstr is NVPString.
	  * @nvpArray is Associative Array.
	  */
	function deformatNVP($nvpstr)
	{
		$intial = 0;
	 	$nvpArray = array();

		while(strlen_wrapper($nvpstr)) {
			//postion of Key
			$keypos = strpos_wrapper($nvpstr, '=');
			//position of value
			$valuepos = strpos_wrapper($nvpstr, '&') ? strpos_wrapper($nvpstr, '&') : strlen_wrapper($nvpstr);

			/* getting the Key and Value values and storing in a Associative Array */
			$keyval = substr_wrapper($nvpstr, $intial, $keypos);
			$valval = substr_wrapper($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
			//decoding the respose
			$nvpArray[urldecode($keyval)] = urldecode($valval);
			$nvpstr = substr_wrapper($nvpstr, $valuepos + 1, strlen_wrapper($nvpstr));
	     }
		return $nvpArray;
	}

	function build_error_message($resArray = '')
	{
		global $messageStack;

			if(isset($_SESSION['curl_error_no'])) {
			$errorCode = $_SESSION['curl_error_no'];
			$errorMessage = $_SESSION['curl_error_msg'];

			$error .= 'Error Number: '.$errorCode.'<br />';
			$error .= 'Error Message: '.$errorMessage.'<br />';
			} else {
			$error .= 'Ack: '.$resArray['ACK'].'<br />';
			$error .= 'Correlation ID: '.$resArray['CORRELATIONID'].'<br />';
			$error .= 'Version:'.$resArray['VERSION'].'<br />';

			$count = 0;
			while(isset($resArray['L_SHORTMESSAGE'.$count])) {
				$errorCode = $resArray['L_ERRORCODE'.$count];
				$shortMessage = $resArray['L_SHORTMESSAGE'.$count];
				$longMessage = $resArray['L_LONGMESSAGE'.$count];
				$count = $count + 1;

				$error .= 'Error Number:'.$errorCode.'<br />';
				$error .= 'Error Short Message: '.$shortMessage.'<br />';
				$error .= 'Error Long Message: '.$longMessage.'<br />';
 				}//end while
			}// end else

		$_SESSION['reshash']['FORMATED_ERRORS'] = $error;
	}

	function write_status_history($o_id)
	{
		if(empty($o_id)) {
			return false;
	}

		$ack = strtoupper($_SESSION['reshash']['ACK']);
		if($ack != 'SUCCESS') {
			$o_status = PAYPAL_ORDER_STATUS_REJECTED_ID;
	    } else {
	    	$o_status = PAYPAL_ORDER_STATUS_SUCCESS_ID;
	    }
		/*
		while (list ($key, $value) = each($_SESSION['reshash'])) {
			$comment .= $key.'='.$value;
		}
		*/
		$order_history_data = array('orders_id' => $o_id,
		 						    'orders_status_id' => $o_status,
		 						    'date_added' => 'now()',
		 						    'customer_notified' => '0',
		 						    'comments' => $comment);
		xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $order_history_data);
		xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status = '".$o_status."', last_modified = now() WHERE orders_id = '".xtc_db_prepare_input($o_id)."'");

		return true;
	}

	function callback_process($data) {
		global $_GET;
		$this->data = $data;

		//$this->_logTrans($data);

		if (EMAIL_TRANSPORT == 'smtp') {
			require_once (DIR_WS_CLASSES . 'class.smtp.php');
		}
		require_once (DIR_FS_INC . 'xtc_Security.inc.php');

		if (isset ($this->data['invoice']) && is_numeric($this->data['invoice']) && ($this->data['invoice'] > 0)) {
			$order_query = xtc_db_query("SELECT	currency, currency_value
										 FROM " . TABLE_ORDERS . "
										 WHERE orders_id = '" . xtc_db_input($this->data['invoice']) . "'");

			if (xtc_db_num_rows($order_query) > 0) {
				$order = xtc_db_fetch_array($order_query);
				$total_query = xtc_db_query("SELECT value
											 FROM " . TABLE_ORDERS_TOTAL . "
											 WHERE orders_id = '" . xtc_db_input($this->data['invoice']) . "'
											 AND class = 'ot_total' limit 1");

				$ipn_data = array();
				$ipn_data['reason_code'] = xtc_db_input($this->data['reason_code']);
				$ipn_data['xtc_order_id'] = xtc_db_input($this->data['invoice']);
				$ipn_data['payment_type'] = xtc_db_input($this->data['payment_type']);
				$ipn_data['payment_status'] = xtc_db_input($this->data['payment_status']);
				$ipn_data['pending_reason'] = xtc_db_input($this->data['pending_reason']);
				$ipn_data['invoice'] = xtc_db_input($this->data['invoice']);
				$ipn_data['mc_currency'] = xtc_db_input($this->data['mc_currency']);
				$ipn_data['first_name'] = xtc_db_input($this->mn_iconv('ISO-8859-1', 'UTF-8', $this->data['first_name']));
				$ipn_data['last_name'] = xtc_db_input($this->mn_iconv('ISO-8859-1', 'UTF-8', $this->data['last_name']));

				$ipn_data['address_name'] = xtc_db_input($this->mn_iconv('ISO-8859-1', 'UTF-8', $this->data['address_name']));
				$ipn_data['address_street'] = xtc_db_input($this->mn_iconv('ISO-8859-1', 'UTF-8', $this->data['address_street']));
				$ipn_data['address_city'] = xtc_db_input($this->mn_iconv('ISO-8859-1', 'UTF-8', $this->data['address_city']));
				$ipn_data['address_state'] = xtc_db_input($this->mn_iconv('ISO-8859-1', 'UTF-8', $this->data['address_state']));
				$ipn_data['address_zip'] = xtc_db_input($this->data['address_zip']);
				$ipn_data['address_country'] = xtc_db_input($this->mn_iconv('ISO-8859-1', 'UTF-8', $this->data['address_country']));
				$ipn_data['address_status'] = xtc_db_input($this->data['address_status']);

				$ipn_data['payer_email'] = xtc_db_input($this->mn_iconv('ISO-8859-1', 'UTF-8', $this->data['payer_email']));
				$ipn_data['payer_id'] = xtc_db_input($this->data['payer_id']);
				$ipn_data['payer_status'] = xtc_db_input($this->data['payer_status']);

				$ipn_data['payment_date'] = xtc_db_input($this->datetime_to_sql_format($this->data['payment_date']));
				$ipn_data['business'] = xtc_db_input($this->mn_iconv('ISO-8859-1', 'UTF-8', $this->data['business']));
				$ipn_data['receiver_email'] = xtc_db_input($this->mn_iconv('ISO-8859-1', 'UTF-8', $this->data['receiver_email']));
				$ipn_data['receiver_id'] = xtc_db_input($this->data['receiver_id']);

				$ipn_data['txn_id'] = xtc_db_input($this->data['txn_id']);
				$ipn_data['parent_txn_id'] = xtc_db_input($this->data['parent_txn_id']);

				$ipn_data['mc_gross'] = xtc_db_input($this->data['mc_gross']);
				$ipn_data['mc_fee'] = xtc_db_input($this->data['mc_fee']);

				$ipn_data['payment_gross'] = xtc_db_input($this->data['payment_gross']);
				$ipn_data['payment_fee'] = xtc_db_input($this->data['payment_fee']);


				$ipn_data['notify_version'] = xtc_db_input($this->data['notify_version']);
				$ipn_data['verify_sign'] = xtc_db_input($this->data['verify_sign']);
				$ipn_data['txn_type']= $this->ipn_determine_txn_type($this->data['txn_type']);

				$_transQuery = "SELECT paypal_ipn_id FROM ".TABLE_PAYPAL." WHERE txn_id = '".$ipn_data['txn_id']."'";
				$_transQuery = xtc_db_query($_transQuery);
				$_transQuery = xtc_db_fetch_array($_transQuery);
				if ($_transQuery['paypal_ipn_id']!='') {
					$insert_id = $_transQuery['paypal_ipn_id'];
					// do not insert data in main table
//					xtc_db_perform('paypal',$ipn_data,'update','paypal_ipn_id='.$insert_id);
					// only update status of main transaction

					xtc_db_query("update ".TABLE_PAYPAL." set
									payment_status	= '".$ipn_data['payment_status']."',
									pending_reason	= '".$ipn_data['pending_reason']."',
									payer_email		= '".$ipn_data['payer_email']."',
									num_cart_items	= '".$ipn_data['num_cart_items']."',
									mc_fee			= '".$ipn_data['mc_fee']."',
									mc_shipping		= '".$ipn_data['mc_shipping']."',
									first_name		= '".$ipn_data['first_name']."',
									last_name		= '".$ipn_data['last_name']."',
									address_name	= '".$ipn_data['address_name']."',
									address_street	= '".$ipn_data['address_street']."',
									address_city	= '".$ipn_data['address_city']."',
									address_state	= '".$ipn_data['address_state']."',
									address_zip		= '".$ipn_data['address_zip']."',
									address_country	= '".$ipn_data['address_country']."',
									address_status	= '".$ipn_data['address_status']."',
									payer_status	= '".$ipn_data['payer_status']."',
									receiver_email	= '".$ipn_data['receiver_email']."',
									last_modified	= now() where paypal_ipn_id = '".$insert_id."'");
					// GM origninal
					// xtc_db_query("update ".TABLE_PAYPAL." set payment_status = '".$ipn_data['payment_status']."',pending_reason='". $ipn_data['pending_reason']."', last_modified = now() where paypal_ipn_id = '".$insert_id."'");
				} else {
					$ipn_data['date_added']='now()';
					$ipn_data['last_modified']='now()';
					xtc_db_perform(TABLE_PAYPAL, $ipn_data);
					$insert_id = xtc_db_insert_id();
				}

				$paypal_order_history = array ('paypal_ipn_id' => $insert_id,
                                   'txn_id' => $ipn_data['txn_id'],
                                   'parent_txn_id' => $ipn_data['parent_txn_id'],
                                   'payment_status' => $ipn_data['payment_status'],
                                   'pending_reason' => $ipn_data['pending_reason'],
                                   'mc_amount' => $ipn_data['mc_gross'],
                                   'date_added' => 'now()'
                                  );
				xtc_db_perform('paypal_status_history', $paypal_order_history);


				$total = xtc_db_fetch_array($total_query);
				$crlf = "\n";
				$comment_status = xtc_db_input($this->data['payment_status']) . ' ' . xtc_db_input($this->data['mc_gross']) . xtc_db_input($this->data['mc_currency']) . $crlf;
				$comment_status .= ' ' . xtc_db_input($this->data['first_name']) . ' ' . xtc_db_input($this->data['last_name']) . ' ' . xtc_db_input($this->data['payer_email']);

				if (isset ($this->data['payer_status'])) {
					$comment_status .= ' is ' . xtc_db_input($this->data['payer_status']);
				}

				$comment_status .= '.' . $crlf . $crlf . ' [';

				if (isset ($this->data['test_ipn']) && is_numeric($this->data['test_ipn']) && ($_POST['test_ipn'] > 0)) {
					$debug = '(Sandbox-Test Mode) ';
				}

				$comment_status .= $crlf . 'Fee=' . xtc_db_input($this->data['mc_fee']) . xtc_db_input($this->data['mc_currency']);

				if (isset ($this->data['pending_reason'])) {
					$comment_status .= $crlf . ' Pending Reason=' . xtc_db_input($this->data['pending_reason']);
				}

				if (isset ($this->data['reason_code'])) {
					$comment_status .= $crlf . ' Reason Code=' . xtc_db_input($this->data['reason_code']);
				}

				$comment_status .= $crlf . ' Payment=' . xtc_db_input($this->data['payment_type']);
				$comment_status .= $crlf . ' Date=' . xtc_db_input($this->data['payment_date']);

				if (isset ($this->data['parent_txn_id'])) {
					$comment_status .= $crlf . ' ParentID=' . xtc_db_input($this->data['parent_txn_id']);
				}

				$comment_status .= $crlf . ' ID=' . xtc_db_input($_POST['txn_id']);

				//Set status for default (Pending)
				$order_status_id = PAYPAL_ORDER_STATUS_PENDING_ID;

				$parameters = 'cmd=_notify-validate';

				foreach ($this->data as $key => $value) {
					$parameters .= '&' . $key . '=' . urlencode(stripslashes($value));
				}

				$this->_logTransactions($parameters);

                $no_curl = true;
                if(function_exists('curl_init')) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $this->IPN_URL);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                    $result = curl_exec($ch);
                    if(!curl_errno($ch)) {
                        $no_curl = false;
                    }
                    curl_close($ch);
                }
                if($no_curl) {
					$request_post = array(
						'http'=>array(
							'method' => 'POST',
							'header' => "Content-type: application/x-www-form-urlencoded\r\n",
							'content' => $parameters));
					$request = stream_context_create($request_post);
					$result = file_get_contents($this->IPN_URL, false, $request);
				}

				if ($result == 'VERIFIED' || $result == '1') {
					if ($this->data['payment_status'] == 'Completed') {
						if (PAYPAL_ORDER_STATUS_SUCCESS_ID > 0) {
							$order_status_id = PAYPAL_ORDER_STATUS_SUCCESS_ID;
						}
					}
					//Set status for Denied, Failed, Refunded or Reversed
					elseif(($this->data['payment_status'] == 'Denied') OR ($this->data['payment_status'] == 'Failed') OR ($this->data['payment_status'] == 'Refunded') OR ($this->data['payment_status'] == 'Reversed')) {
						$order_status_id = PAYPAL_ORDER_STATUS_REJECTED_ID;
					}
				} else {
					$debug .= '[PayPal-Zahlung fehlgeschlagen] - ' . $result . "\n";
					$order_status_id = PAYPAL_ORDER_STATUS_REJECTED_ID;
					$error_reason = 'Die Sofortige Zahlungsbestätigung von PayPal meldet eine ungültige Zahlung, aber Rechnungs-und Kundennummer stimmen überein.';
				}

				$comment_status .= ']';

				// BOF GM_MOD
				if($this->data['payment_status'] != 'Refunded')
				{
					xtc_db_query("UPDATE " . TABLE_ORDERS . "
								  SET orders_status = '" . $order_status_id . "', last_modified = now()
								  WHERE orders_id = '" . xtc_db_input($this->data['invoice']) . "'");

					$sql_data_array = array (
						'orders_id' => xtc_db_input($this->data['invoice']
					), 'orders_status_id' => $order_status_id, 'date_added' => 'now()', 'customer_notified' => '0', 'comments' => 'PayPal IPN ' . $comment_status . '');

					xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
				}
				// EOF GM_MOD
			} else {
				$error_reason = 'Keine Bestellung mit der Bestellnummer "' . xtc_db_input($this->data['invoice']) . '" von Kunden "' . (int) $this->data['custom'] . '" vorhanden.';
			}
		} else {
			$error_reason = "Es wurde keine Bestellnummer zu den von der Sofortige Zahlungsbestätigung von PayPal in Ihrem Shop gefunden.\n";
			$error_reason .= "Ein Grund für den Erhalt dieser E-Mail erhalten ist, wenn Sie eine Bestellung gelöscht haben.";
		}

		if (xtc_not_null(EMAIL_SUPPORT_ADDRESS) && strlen_wrapper($error_reason)) {
			$t_parameter_mapping = $this->get_parameter_map();

			$email_body = $error_reason . "\n\n";
			$email_body.= "Bei der Prüfung der Zahlung hat PayPal eine Unstimmigkeit festgestellt.\nBitte melden Sie sich bei Ihrem PayPal Konto an und überprüfen Sie die Bestellung mit der Bestellnummer '".$this->data['invoice']."', ob die Bezahlung eingegangen ist und die Kundendaten übereinstimmen.\nStimmen die Daten überein, war die Zahlung erfolgreich. Andernfalls setzen Sie sich bitte mit dem Kunden in Verbindung.\n\n Nähere Informationen zu der Bestellung finden Sie weiter unten in dieser E-Mail.\n\n";
			$email_body.= "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
			$email_body.= "Bitte beachten Sie die Beschreibung am Anfang dieser Benachrichtigung!\n\nDiese Informationen helfen Ihnen bei der Überprüfung der Zahlung.\n\n";

			foreach ($this->data as $key => $value) {
				if(array_key_exists($key, $t_parameter_mapping) === false) {
					continue;
				}
				if(is_array($t_parameter_mapping[$key])) {
					$email_body .= $t_parameter_mapping[$key][$value] . "\n";
				} else {
					$email_body .= $t_parameter_mapping[$key] . ' = ' . $value . "\n";
				}
			}

			$email_body_html = nl2br($email_body);
			$email_subject = 'Ungültige PayPal-Sofortige Zahlungsbestätigung';

			xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_ADDRESS, '', EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, false, false, $email_subject, $email_body_html, $email_body);
		}
	}

	/**
	 * map the PayPal IPN variables for the INVALID mail
	 *
	 * @return array $t_parameter_map Map with variables
	 */
	function get_parameter_map() {
		$t_parameter_map = array(
			'payment_type' => array(
				'echeck' => 'Die Zahlungsmittel wurden per Überweisung bereitgestellt.',
				'instant' => 'Die Zahlung wurde aus dem PayPal-Guthaben, von einer Kreditkarte oder Geldtransfer beglichen.'
			),
			'payment_date' => 'Datum der Zahlung',
			'payment_status' => array(
				'Canceled-Reversal' => 'Eine bestehende Rückbuchung wurde storniert, z.B. wenn ein Konflikt zu Ihren Gunsten beigelegt wurde und Ihnen der rückgebuchte Betrag wieder gutgeschrieben wurde.',
				'Completed' => 'Die Zahlung wurde abgeschlossen und Ihrem Konto gutgeschrieben.',
				'Denied' => 'Sie haben die Zahlung abgelehnt.',
				'Failed' => 'Die Zahlung ist fehlgeschlagen.',
				'Partially-Refunded' => 'Zu der Transaktion wurde eine Teilrückzahlung durchgeführt.',
				'Pending' => 'Die Zahlung ist offen.',
				'Refunded' => 'Die Zahlung wurde rückerstattet.',
				'Reversed' => 'Die Zahlung wurde aus einem anderen Grund als einer Rückzahlung rückgebucht. Der Betrag wurde dem Käufer zurückübertragen.',
				'Processed' => 'Die Zahlung wurde abgeschlossen.'
			),
			'address_status' => array(
				'confirmed' => 'Der Käufer hat eine bestätigte Adresse angegeben.',
				'unconfirmed' => 'Der Käufer hat eine unbestätigte Adresse angegeben.'
			),
			'payer_status' => array(
				'verified' => 'Der Käufer hat ein verifiziertes PayPal-Konto.',
				'unverified' => 'Der Käufer hat ein nicht-verifiziertes PayPal-Konto.'
			),
			'first_name' => 'Vorname des Käufers',
			'last_name' => 'Nachname des Käufers',
			'payer_email' => 'E-Mail-Adresse des Käufers',
			'payer_id' => 'Eindeutige PayPal-Kundennummer',
			'address_name' => 'Empfängername',
			'address_country' => 'Land der Käuferadresse',
			'address_country_code' => 'Ländercode',
			'address_zip' => 'Postleitzahl der Käuferadresse',
			'address_state' => 'Bundesstaat der Käuferadresse',
			'address_city' => 'Stadt der Käuferadresse',
			'address_street' => 'Straße der Käuferadresse',
			'receiver_email' => 'E-Mail-Adresse oder die PayPal-Kundennummer des Zahlungsempfängers',
			'receiver_id' => 'PayPal-Kundennummer des Empfängers',
			'residence_country' => 'Ländercode des Zahlungsempfängers',
			'invoice' => 'Bestellnummer',
			'txn_id' => 'PayPal Transaktionsnummer'
		);

		return $t_parameter_map;
	}

	function datetime_to_sql_format($paypalDateTime) {
		//Copyright (c) 2004 DevosC.com
		$months = array (
			'Jan' => '01',
			'Feb' => '02',
			'Mar' => '03',
			'Apr' => '04',
			'May' => '05',
			'Jun' => '06',
			'Jul' => '07',
			'Aug' => '08',
			'Sep' => '09',
			'Oct' => '10',
			'Nov' => '11',
			'Dec' => '12'
		);
		$hour = substr_wrapper($paypalDateTime, 0, 2);
		$minute = substr_wrapper($paypalDateTime, 3, 2);
		$second = substr_wrapper($paypalDateTime, 6, 2);
		$month = $months[substr_wrapper($paypalDateTime, 9, 3)];
		$day = (strlen_wrapper($day = preg_replace("/,/", '', substr_wrapper($paypalDateTime, 13, 2))) < 2) ? '0' . $day : $day;
		$year = substr_wrapper($paypalDateTime, -8, 4);
		if (strlen_wrapper($day) < 2)
			$day = '0' . $day;
		return ($year . "-" . $month . "-" . $day . " " . $hour . ":" . $minute . ":" . $second);
	}


	function logging_status($o_id) {
		$data = array_merge((array)$_SESSION['reshash'], (array)$_SESSION['nvpReqArray']);

 		// if paypal error, set resaoncode to errorcode
		// BOF GM_MOD
		$t_reasoncode = $data['REASONCODE'];
		if($_SESSION['reshash']['ACK'] == 'Failure' && empty($data['REASONCODE'])) {
			$t_reasoncode = $_SESSION['reshash']['L_ERRORCODE0'];
		}
		// EOF GM_MOD

		$data_array = array (
 						   'xtc_order_id' => $o_id,
 						   'txn_type' => $data['TRANSACTIONTYPE'],
 						   'reason_code' => $t_reasoncode,
 						   'payment_type' => $data['PAYMENTTYPE'],
 						   'payment_status' => $data['PAYMENTSTATUS'],
 						   'pending_reason' => $data['PENDINGREASON'],
 						   'invoice' => $data['INVNUM'],
 						   'mc_currency' => $data['CURRENCYCODE'],
 						   'first_name' => $data['FIRSTNAME'],
 						   'last_name' => $data['LASTNAME'],
 						   'payer_business_name' => $data['BUSINESS'],
 						   'address_name' => $data['PAYMENTREQUEST_0_SHIPTONAME'],
 						   'address_street' => $data['PAYMENTREQUEST_0_SHIPTOSTREET'],
 						   'address_city' => $data['PAYMENTREQUEST_0_SHIPTOCITY'],
 						   'address_state' => $data['PAYMENTREQUEST_0_SHIPTOSTATE'],
 						   'address_zip' => $data['PAYMENTREQUEST_0_SHIPTOZIP'],
 						   'address_country' => $data['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'],
 						   'address_status' => $data['ADDRESSSTATUS'],
 						   'payer_email' => $data['EMAIL'],
 						   'payer_id' => $data['PAYERID'],
 						   'payer_status' => $data['PAYERSTATUS'],
 						   'payment_date' => $data['TIMESTAMP'],
 						   'business' => '',
 						   'receiver_email' => '',
 						   'receiver_id' => '',
 						   'txn_id' => $data['TRANSACTIONID'],
 						   'parent_txn_id' => '',
 						   'num_cart_items' => '',
 						   'mc_gross' => $data['AMT'],
 						   'mc_fee' => $data['FEEAMT'],
 						   'mc_authorization' => $data['AMT'],
 						   'payment_gross' => '',
 						   'payment_fee' => '',
 						   'settle_amount' => $data['SETTLEAMT'],
 						   'settle_currency' => '',
 						   'exchange_rate' => $data['EXCHANGERATE'],
 						   'notify_version' => $data['VERSION'],
 						   'verify_sign' => '',
 						   'last_modified' => '',
 						   'date_added' => 'now()',
 						   'memo' => $data['DESC']);
		xtc_db_perform(TABLE_PAYPAL,$data_array);
		return true;
	}

	function buildAPIKey($key, $pay){
		/*
		$key_arr=explode(',',$key);
		$k='';
		for ($i=0; $i<count($key_arr);$i++) $k.=chr($key_arr[$i]);
			if($pay=='ec'){
		    return $k.'EC_AT_31';
			}elseif($pay=='dp'){
			return $k.'DP_AT_31';
			}
			*/
		return 'Gambio_cart_ECS_DE';
	}

	  function ipn_determine_txn_type($txn_type = 'unknown') {

    if (substr_wrapper($txn_type,0,8) == 'cleared-') return $txn_type;
    if ($this->data['txn_type'] == 'send_money') return $this->data['txn_type'];
    if ($this->data['txn_type'] == 'express_checkout' || $this->data['txn_type'] == 'cart') $txn_type = $this->data['txn_type'];
// if it's not unique or linked to a parent, then:
// 1. could be an e-check denied / cleared
// 2. could be an express-checkout "pending" transaction which has been Accepted in the merchant's PayPal console and needs activation in Zen Cart
    if ($this->data['payment_status']=='Completed' && $txn_type=='express_checkout' && $this->data['payment_type']=='echeck') {
      $txn_type = 'express-checkout-cleared';
      return $txn_type;
    }
    if ($this->data['payment_status']=='Completed' && $this->data['payment_type']=='echeck') {
      $txn_type = 'echeck-cleared';
      return $txn_type;
    }
    if (($this->data['payment_status']=='Denied' || $this->data['payment_status']=='Failed') && $this->data['payment_type']=='echeck') {
      $txn_type = 'echeck-denied';
      return $txn_type;
    }
    if ($this->data['payment_status']=='Denied') {
      $txn_type = 'denied';
      return $txn_type;
    }
    if (($this->data['payment_status']=='Pending') && $this->data['pending_reason']=='echeck') {
      $txn_type = 'pending-echeck';
      return $txn_type;
    }
    if (($this->data['payment_status']=='Pending') && $this->data['pending_reason']=='address') {
      $txn_type = 'pending-address';
      return $txn_type;
    }
    if (($this->data['payment_status']=='Pending') && $this->data['pending_reason']=='intl') {
      $txn_type = 'pending-intl';
      return $txn_type;
    }
    if (($this->data['payment_status']=='Pending') && $this->data['pending_reason']=='multi-currency') {
      $txn_type = 'pending-multicurrency';
      return $txn_type;
    }
    if (($this->data['payment_status']=='Pending') && $this->data['pending_reason']=='multi-verify') {
      $txn_type = 'pending-verify';
      return $txn_type;
    }
    return $txn_type;
  }

  /**
   * check the PayPal API
   *
   * this methode checks the PayPal API
   * 1. check is cURL and file_get_contens installed
   * 2. is open SSL instaled
   * 3. if no error, check the API credential are valide
   * 4. check the server firewall for PayPal URLs
   * 5. check the shop delivers in a land, where the state is required
   *
   * @return string $result_string if an error, the error
   * @access public
   */
  function check_api()
  {
	$result_string	= '';
	$error			= '';
	$extensions		= get_loaded_extensions();
	if (!in_array("curl", $extensions) && !function_exists('file_get_contents')) {
		$error = GM_SETTINGS_PAYPAL_ERROR_CURL;
	} elseif(!in_array("openssl", $extensions)) {
		$error = GM_SETTINGS_PAYPAL_ERROR_OPENSSL;
	}
	if($error == '') {
		$result = $this->hash_call('SetExpressCheckout', 'CHECK_API');
		if((!empty($result) && (int) $result['L_ERRORCODE0'] == 10002)) {
			$error = GM_SETTINGS_PAYPAL_ERROR_API.'<br /><a style="font-size:14px;color:#cc0000;font-weight:bold;text-decoration:underline" href="'.$this->api_tool_link.'" target="_blank">'.BOX_API_TOOL.'</a>';
		} elseif(empty($result) || !is_array($result)) {
			$error = GM_SETTINGS_PAYPAL_ERROR_FIREWALL;
		}
	}
	// BOF GM_MOD
	// check if country of selected payment address is not allowed
	$t_check_countries_array = array('CA', 'US', 'GB');
	$t_show_state_error = false;
	// get the active countries from DB
	$coo_countries = new GMDataObjectGroup(TABLE_COUNTRIES, array('status' => '1'));
	$coo_countries_array = $coo_countries->get_data_objects_array();
	foreach($coo_countries_array as $coo_countrie) {
		$t_needle = $coo_countrie->get_data_value('countries_iso_code_2');
		$t_activate_state = in_array($t_needle, $t_check_countries_array);
		// if you deliver in a land of confusion, show message to change the state
		if($t_activate_state == true) {
			$t_show_state_error = true;
			break;
		}
	}

	// state must be activated if paypal is installed for a land of confusion
	if($t_show_state_error && (ACCOUNT_STATE === 'false' || (int)ENTRY_STATE_MIN_LENGTH <= 0)) {
      	if(!empty($error)) {
			$error .= '<br /><br />';
		}
		$error .= GM_SETTINGS_PAYPAL_ERROR_STATE;
	}
	// EOF GM_MOD

	if(!empty($error)) {
      $result_string = '
    	<span style="font-size:14px;color:#cc0000;"><b>'.$error.'</b /><br /><br /></span>
        ';
	}
    return $result_string;
  }



	function _logAPICall($call)
	{
		$coo_log = LogControl::get_instance();
		$coo_log->notice($call, 'payment', 'payment.paypal_api_calls-'.$this->v_pp_log_token);
	}

	function _logTransactions($parameters)
	{
		$coo_log = LogControl::get_instance();
		$line = 'PP TRANS|' . xtc_get_ip_address() . '|';
		foreach ($_POST as $key => $val)
		{
			$line .= $key . ':' . $val . '|';
		}
		$coo_log->notice($line, 'payment', 'payment.paypal_ipn-'.$this->v_pp_log_token);
	}

	function _logTrans($data)
	{
		while (list ($key, $value) = each($data)) {
			$line .= $key . ':' . $val . '|';
		}
		xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_ADDRESS, '', EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, false, false, 'PayPal IPN Invalid Process', $line, $line);
	}

	function getZoneId($country, $state) {
		$t_result = xtc_db_query("SELECT DISTINCT zone_id FROM " . TABLE_ZONES . " WHERE zone_country_id = '".xtc_db_input($country)."' AND zone_code = '".xtc_db_input($state)."'");
		$t_zone_data = xtc_db_fetch_array($t_result);
		return $t_zone_data['zone_id'];
	}
}
