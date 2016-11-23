<?php
/* -----------------------------------------------------------------------------------------
   $Id: saferpaygw.php,v 1.0 2005/12/19 14:23:54 fb Exp $

   for XT-Commerce
   http://www.xt-commerce.com

   Copyright (c) 2006 Alexander Federau
   -----------------------------------------------------------------------------------------

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

//error_reporting(E_ALL ^ E_NOTICE);
//ini_set('display_errors', 'on');

define('MODULE_PAYMENT_SAFERPAYGW_TEST_ACCOUNT', '99867-94913159');
if ( !defined('MODULE_PAYMENT_SAFERPAYGW_PASSWORD') ) {
	define('MODULE_PAYMENT_SAFERPAYGW_PASSWORD', 'XAjc3Kna');
}
define('TABLE_SAFERPAY_TRANSACTIONS', 'saferpay_transactions');

class saferpaygw_ORIGIN {
    var $code, $title, $description, $enabled;
    var $payinit_url, $xml_name;
	var $saferpay_languages;
	var $terminal_lang_code = 'en';

// class constructor
	public function __construct() {
		global $order;

  	    $this->code = 'saferpaygw';
		$this->title = MODULE_PAYMENT_SAFERPAYGW_TEXT_TITLE;
		$t_show_transaction = '';
		if(defined('MODULE_PAYMENT_SAFERPAYGW_STATUS') && MODULE_PAYMENT_SAFERPAYGW_STATUS == 'true' && $admin_access['saferpay'] == '1')
		{
			$t_show_transaction = '(<a style="color:red" href="' . xtc_href_link('saferpay.php') . '">' . MODULE_PAYMENT_SAFERPAYGW_TEXT_SHOW_TRANSACTION . '</a>)<br />';
		}
		$this->description = sprintf(MODULE_PAYMENT_SAFERPAYGW_TEXT_DESCRIPTION, $t_show_transaction);
		$this->sort_order = MODULE_PAYMENT_SAFERPAYGW_SORT_ORDER;
		$this->enabled = ((strtolower(MODULE_PAYMENT_SAFERPAYGW_STATUS) == 'true') ? true : false);

		if ((int)MODULE_PAYMENT_SAFERPAYGW_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_SAFERPAYGW_ORDER_STATUS_ID;
		}

		// set array of languages
		$this->saferpay_languages = array('en', 'de', 'fr', 'it');
		if ( in_array($_SESSION['language_code'], $this->saferpay_languages) ) {
			$this->terminal_lang_code = $_SESSION['language_code'];
		}
		elseif ( in_array(DEFAULT_LANGUAGE, $this->saferpay_languages) ) {
			$this->terminal_lang_code = DEFAULT_LANGUAGE;
		}

		if (is_object($order)) $this->update_status();

		$this->form_action_url = '';
	}

	// class methods
    function update_status() {
		global $order;

  	    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_SAFERPAYGW_ZONE > 0) ) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_SAFERPAYGW_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
			while ($check = xtc_db_fetch_array($check_query)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				}
				elseif ($check['zone_id'] == $order->billing['zone_id']) {
					$check_flag = true;
					break;
				}
			}

			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
    }

    function javascript_validation() {
		return false;
    }

    function selection() {
		global $order;
		$selection = array('id' => $this->code,
                         'module' => $this->title);

		return $selection;
	}

	function pre_confirmation_check() {
		global $order, $xtPrice;

		if (PHP_VERSION < 4.1) {
			global $_POST;
		}

		if ( defined('MODULE_PAYMENT_SAFERPAYGW_CURRENCY') && xtc_not_null(MODULE_PAYMENT_SAFERPAYGW_CURRENCY) ) {
			$trx_currency = MODULE_PAYMENT_SAFERPAYGW_CURRENCY;
		}
		else {
			$trx_currency = $_SESSION['currency'];
		}

		$query  = xtc_db_query("SELECT MAX(orders_id)+1 as new_id FROM " . TABLE_ORDERS);
		$this->orderid = '1';

		if ( xtc_db_num_rows($query) > 0)
		{
			$orders = xtc_db_fetch_array($query);
			if ( isset($orders['new_id']) && (int)$orders['new_id'] > 0 )
				$this->orderid = $orders['new_id'];
		} else {
			xtc_db_query("alter table " . TABLE_ORDERS . " auto_increment=1");
		}
		// order_id + Time  XXX_HHMMSS
		$this->orderid .= '_' . date("YmdHis");
		//the checking for a posibility to send a request
		//
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$total=$order->info['total']+$order->info['tax'];
		}
		else {
			$total=$order->info['total'];
		}

  	    if ($_SESSION['currency']==$trx_currency) {
			$amount=round($total, $xtPrice->get_decimal_places($trx_currency));
		}
		else {
			$amount=round($xtPrice->xtcCalculateCurrEx($total,$trx_currency) , $xtPrice->get_decimal_places($trx_currency));
		}

		$strAttributes = 'ACCOUNTID=' . MODULE_PAYMENT_SAFERPAYGW_ACCOUNT_ID .
					   '&LANGID=' . $this->terminal_lang_code .
					   '&AMOUNT=' . $amount*100 .
					   '&CURRENCY=' . $trx_currency .
					   '&ORDERID='. $this->orderid .
					   //'&USERNOTIFY=' . $customer_values['customers_email_address'] .
					   '&DESCRIPTION=' . urlencode(htmlentities(STORE_NAME)) .
					   '&SUCCESSLINK='.xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL').
					 '&CCCVC='. (MODULE_PAYMENT_SAFERPAYGW_CCCVC=='true'?'yes':'no').
					 '&CCNAME='. (MODULE_PAYMENT_SAFERPAYGW_CCNAME=='true'?'yes':'no').
					 '&FAILLINK='.xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'SSL', true).
					 '&BACKLINK='.xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL').
					 '&DELIVERY=no' .
					   '&ALLOWCOLLECT=no';

		if ( defined('MODULE_PAYMENT_SAFERPAYGW_MENUCOLOR') && xtc_not_null(MODULE_PAYMENT_SAFERPAYGW_MENUCOLOR) ) {
			$strAttributes .= '&MENUCOLOR='.MODULE_PAYMENT_SAFERPAYGW_MENUCOLOR;
		}
		if ( defined('MODULE_PAYMENT_SAFERPAYGW_MENUFONTCOLOR') && xtc_not_null(MODULE_PAYMENT_SAFERPAYGW_MENUFONTCOLOR) ) {
			$strAttributes .= '&MENUFONTCOLOR='.MODULE_PAYMENT_SAFERPAYGW_MENUFONTCOLOR;
		}
		if ( defined('MODULE_PAYMENT_SAFERPAYGW_BODYFONTCOLOR') && xtc_not_null(MODULE_PAYMENT_SAFERPAYGW_BODYFONTCOLOR) ) {
			$strAttributes .= '&BODYFONTCOLOR='.MODULE_PAYMENT_SAFERPAYGW_BODYFONTCOLOR;
		}
		if ( defined('MODULE_PAYMENT_SAFERPAYGW_BODYCOLOR') && xtc_not_null(MODULE_PAYMENT_SAFERPAYGW_BODYCOLOR) ) {
			$strAttributes .= '&BODYCOLOR='.MODULE_PAYMENT_SAFERPAYGW_BODYCOLOR;
		}
		if ( defined('MODULE_PAYMENT_SAFERPAYGW_HEADFONTCOLOR') && xtc_not_null(MODULE_PAYMENT_SAFERPAYGW_HEADFONTCOLOR) ) {
			$strAttributes .= '&HEADFONTCOLOR='.MODULE_PAYMENT_SAFERPAYGW_HEADFONTCOLOR;
		}
		if ( defined('MODULE_PAYMENT_SAFERPAYGW_HEADCOLOR') && xtc_not_null(MODULE_PAYMENT_SAFERPAYGW_HEADCOLOR) ) {
			$strAttributes .= '&HEADCOLOR='.MODULE_PAYMENT_SAFERPAYGW_HEADCOLOR;
		}
		if ( defined('MODULE_PAYMENT_SAFERPAYGW_HEADLINECOLOR') && xtc_not_null(MODULE_PAYMENT_SAFERPAYGW_HEADLINECOLOR) ) {
			$strAttributes .= '&HEADLINECOLOR='.MODULE_PAYMENT_SAFERPAYGW_HEADLINECOLOR;
		}
		if ( defined('MODULE_PAYMENT_SAFERPAYGW_LINKCOLOR') && xtc_not_null(MODULE_PAYMENT_SAFERPAYGW_LINKCOLOR) ) {
			$strAttributes .= '&LINKCOLOR='.MODULE_PAYMENT_SAFERPAYGW_LINKCOLOR;
		}

		$url = MODULE_PAYMENT_SAFERPAYGW_PAYINIT_URL.'?'.$strAttributes;
		// debug
		//error_log(var_export($url, true)."\n", 3, DIR_FS_CATALOG.'tmp/saferpay_'.date('Ymd').'.log');
		$payinit_url = $this->process_url($url);

		// debug
		//error_log("PayInit: ". var_export($payinit_url, true)."\n", 3, DIR_FS_CATALOG.'tmp/saferpay_'.date('Ymd').'.log');

  	    if(strlen($payinit_url) >0) {
			$this->payinit_url = rawurlencode($payinit_url);
			if ( strpos('\\', $this->payinit_url) !== false ) {
				$this->payinit_url = stripslashes($this->payinit_url);
			}
		//	$this->form_action_url = "JavaScript: OpenSaferpayTerminal('" . $this->payinit_url . "', this, 'BUTTON');";
			$this->form_action_url = rawurldecode($this->payinit_url);
		}
		else{
			$payment_error_return = 'payment_error=' . $this->code . '&error=' . TEXT_SAFERPAYGW_SETUP_ERROR;
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
		}
		return false;
    }

	function process_url($sURL) {
		switch ( MODULE_PAYMENT_SAFERPAYGW_URLREADER ) {
			case 'curl':
				//Die Session initialisieren
				$ch = curl_init($sURL);
				curl_setopt($ch, CURLOPT_PORT, 443);

				// Prüfung des SSL-Zertifikats abschalten (SSL ist dennoch sicher)
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				//Session Optionen setzen
				// kein Header in der Ausgabe
				curl_setopt($ch, CURLOPT_HEADER, 0);
				// Rückgabe schalten
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
				//Ausführen der Aktionen
				$sReturn = curl_exec($ch);
				//Session beenden
				curl_close($ch);
				break;

			default:
				$sReturn = implode("", file($sURL));
				break;
		}
		return $sReturn;
	}

	function confirmation() {
		return false;
    }

    function process_button() {
		//the preperation for a payment here
//		$process_button_string = '<script src="https://support.saferpay.de/OpenSaferpayScript.js" type="text/javascript"></script>';
//		$process_button_string = '<script src="https://www.saferpay.com/OpenSaferpayScript.js" type="text/javascript"></script>';
		//end of the preperation for a payment here
		$form_action_url = "JavaScript: OpenSaferpayTerminal('" . $this->payinit_url . "', this, 'BUTTON');";
		$process_button_string = '<script type="text/javascript">var f=document.getElementById(\'checkout_confirmation\'); if(f) f.action="'.$form_action_url.'";</script>';

  	    return $process_button_string;
    }

    function confirm_pre_form() {
		//the preperation for a payment here
		//$process_button_string = '<script src="https://support.saferpay.de/OpenSaferpayScript.js" type="text/javascript"></script>';
		//$process_button_string = '<script src="https://www.saferpay.com/OpenSaferpayScript.js" type="text/javascript"></script>';
		//end of the preperation for a payment here

		$script_name = 'includes/modules/payment/saferpaygw.js';
		if (file_exists(DIR_FS_CATALOG . $script_name)) {
			if ( ENABLE_SSL ) {
				return '<script src="'.HTTPS_SERVER . DIR_WS_CATALOG . $script_name.'" type="text/javascript"></script>';
			}
			else {
				return '<script src="'.HTTP_SERVER . DIR_WS_CATALOG . $script_name.'" type="text/javascript"></script>';
			}
		}

  	  //return '<script src="https://support.saferpay.de/OpenSaferpayScript.js" type="text/javascript"></script>';
  	  return '<script src="https://www.saferpay.com/OpenSaferpayScript.js" type="text/javascript"></script>';
    }

    function before_process() {
		//global $QUERY_STRING;
		parse_str($_SERVER['QUERY_STRING']);
		$DATA = rawurldecode($DATA);
		//if ( strpos('\\', $DATA) !== false ) {
			//$DATA = stripslashes($DATA);
		//}
		while (substr($DATA,0,14)=="<IDP MSGTYPE=\\") {$DATA = stripslashes($DATA);} 
		$SIGNATURE = rawurldecode($SIGNATURE);
		// debug
		//error_log("Responce: ". var_export($_SERVER['QUERY_STRING'], true)."\n", 3, DIR_FS_CATALOG.'tmp/saferpay_'.date('Ymd').'.log');
		//error_log("Responce DATA: ". var_export($DATA, true)."\n", 3, DIR_FS_CATALOG.'tmp/saferpay_'.date('Ymd').'.log');

		//extract amount and currency
		$trx_amount = 0;
		if ( preg_match('/^<IDP\s.*AMOUNT="([0-9]+)".*>$/i', $DATA, $matches) ) {
			$trx_amount = floatval($matches[1]);
		}
		$trx_currency = $_SESSION['currency'];
		if ( preg_match('/^<IDP\s.*CURRENCY="([A-Z]{3})".*>$/i', $DATA, $matches) ) {
			$trx_currency = $matches[1];
		}
		$payment_provider_id = 0;
		if ( preg_match('/^<IDP\s.*PROVIDERID="([0-9]+)".*>$/i', $DATA, $matches) ) {
			$payment_provider_id = intval($matches[1]);
		}
		$payment_provider_name = '';
		if ( preg_match('/^<IDP\s.*PROVIDERNAME="([^"]+)".*>$/i', $DATA, $matches) ) {
			$payment_provider_name = $matches[1];
		}


		/* put it all together */
		$url = MODULE_PAYMENT_SAFERPAYGW_CONFIRM_URL ."?DATA=".urlencode($DATA)."&SIGNATURE=".urlencode($SIGNATURE);
		/* verify pay confirm message at hosting server */
		$result = $this->process_url($url);
		// debug
		//error_log("PayConfirm: ". var_export($result, true)."\n", 3, DIR_FS_CATALOG.'tmp/saferpay_'.date('Ymd').'.log');

  	    if (substr($result, 0, 3) == "OK:" ) {
			parse_str(substr($result, 3));
			/* $ID = saferpay transaction identifier, store in DBMS */
			/* $TOKEN = token of transaction, store in DBMS */
			$this->ID = $ID;
			$sql_data_array = array( 'customers_id' => $_SESSION['customer_id'],
									 'saferpay_ID' => $this->ID,
									 'saferpay_amount' => $trx_amount/100,
									 'saferpay_currency' => $trx_currency,
									 'saferpay_provider_id' => $payment_provider_id,
									 'saferpay_provider_name' => xtc_db_prepare_input($payment_provider_name),
									 'date_added' => 'now()');
			xtc_db_perform(TABLE_SAFERPAY_TRANSACTIONS, $sql_data_array);

			if ( defined('MODULE_PAYMENT_SAFERPAYGW_COMPLETE') && MODULE_PAYMENT_SAFERPAYGW_COMPLETE == 'true' ) {
				/***** Optional: Finalize payment by capture of transaction *****/
				// if test account than use Password
				$spPassword = '';
				if ( defined('MODULE_PAYMENT_SAFERPAYGW_ACCOUNT_ID') && MODULE_PAYMENT_SAFERPAYGW_ACCOUNT_ID == MODULE_PAYMENT_SAFERPAYGW_TEST_ACCOUNT ) {
					$spPassword = '&spPassword='.MODULE_PAYMENT_SAFERPAYGW_PASSWORD;
				}

				/* put it all together */
				$url = MODULE_PAYMENT_SAFERPAYGW_COMPLETE_URL."?ACCOUNTID=".MODULE_PAYMENT_SAFERPAYGW_ACCOUNT_ID."&ID=".urlencode($ID)."&TOKEN=".urlencode($TOKEN).$spPassword;
				// debug
				//error_log("PayComplete URL:". var_export($url, true)."\n", 3, DIR_FS_CATALOG.'tmp/saferpay_'.date('Ymd').'.log');
				/* complete payment by hosting server */
				$result = $this->process_url($url);
				// debug
				//error_log("PayComplete:". var_export($result, true)."\n", 3, DIR_FS_CATALOG.'tmp/saferpay_'.date('Ymd').'.log');
				if (substr($result, 0, 2) == "OK") {
					$sql_data_array = array( 'saferpay_complete' => '1',
											 'saferpay_complete_result' => $result);
				}
				else {
					// payment could not be completed
					$sql_data_array = array( 'saferpay_complete_result' => $result);
				}
				xtc_db_perform(TABLE_SAFERPAY_TRANSACTIONS, $sql_data_array, 'update', "customers_id='". $_SESSION['customer_id'] ."' AND saferpay_ID='". $this->ID ."'");
			}
		}
		else {
			$payment_error_return = 'payment_error=' . $this->code . '&error=' . TEXT_SAFERPAYGW_CONFIRMATION_ERROR;
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
		}

 	     return false;
	}

    function after_process() {
	    global $insert_id;
        if ($this->order_status) xtc_db_query("UPDATE ". TABLE_ORDERS ." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");
		if ( isset($this->ID) && strlen($this->ID) > 0 ) {
			$sql_data_array = array( 'orders_id' => $insert_id);
			xtc_db_perform(TABLE_SAFERPAY_TRANSACTIONS, $sql_data_array, 'update', "customers_id='". $_SESSION['customer_id'] ."' AND saferpay_ID='". $this->ID ."'");
		}
    }

    function get_error() {
		if (PHP_VERSION < 4.1) {
			global $_GET;
		}

  	    $error = array('title' => SAFERPAYGW_ERROR_HEADING,
                     'error' => ((isset($_GET['error'])) ? stripslashes(urldecode($_GET['error'])) : SAFERPAYGW_ERROR_MESSAGE));

 	     return $error;
	}

    function check() {
		if (!isset($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_SAFERPAYGW_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
    }

    function install() {
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_STATUS', 'true', '6', '1', 'xtc_cfg_pull_down_truefalse(', 'xtc_get_cfg_truefalse', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_ALLOWED', '', '6', '0', now())");
		//xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_LOGIN', 'e99867001', '6', '2', now())");
		//xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_PASSWORD', 'XAjc3Kna', '6', '4', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_ACCOUNT_ID', '99867-94913159', '6', '5', now())");
		//xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_PATH', '', '6', '3', now())");

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_URLREADER', 'file', '6', '1', 'cfg_pull_down_urlreader(', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_PAYINIT_URL', 'https://www.saferpay.com/hosting/CreatePayInit.asp', '6', '6', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_CONFIRM_URL', 'https://www.saferpay.com/hosting/VerifyPayConfirm.asp', '6', '7', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_COMPLETE_URL', 'https://www.saferpay.com/hosting/PayComplete.asp', '6', '8', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_COMPLETE', 'false', '6', '9', 'xtc_cfg_pull_down_truefalse(', 'xtc_get_cfg_truefalse', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_CCCVC', 'true', '6', '10', 'xtc_cfg_pull_down_truefalse(', 'xtc_get_cfg_truefalse', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_CCNAME', 'true', '6', '11', 'xtc_cfg_pull_down_truefalse(', 'xtc_get_cfg_truefalse', now())");

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_CURRENCY', '".DEFAULT_CURRENCY."', '6', '9', 'xtc_cfg_pull_down_currencies(', 'xtc_get_currency_name', now())");

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_MENUCOLOR', '#93B1CF', '6', '10', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_MENUFONTCOLOR', '#000000', '6', '10', now())");
		//xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_FONT', 'Verdana', '6', '10', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_BODYFONTCOLOR', '#000000', '6', '10', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_BODYCOLOR', '#E5E7E8', '6', '10', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_HEADFONTCOLOR', '#000000', '6', '10', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_HEADCOLOR', '#134B83', '6', '10', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_HEADLINECOLOR', '#93B1CF', '6', '10', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_LINKCOLOR', '#134B83', '6', '10', now())");

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_SORT_ORDER', '0', '6', '10', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_ZONE', '0', '6', '11', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SAFERPAYGW_ORDER_STATUS_ID', '0', '6', '12', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

		// create DB table for store of saferpaytransactions
		$query_raw = "CREATE TABLE IF NOT EXISTS ". TABLE_SAFERPAY_TRANSACTIONS ." (
  trans_id int(11) NOT NULL auto_increment,
  customers_id int(11) NOT NULL default '0',
  orders_id int(11) NOT NULL default '0',
  saferpay_ID varchar(96) default NULL,
  saferpay_amount decimal(15,4) NOT NULL default '0.0000',
  saferpay_currency varchar(8) NOT NULL default '',
  saferpay_provider_id int(11) default '0',
  saferpay_provider_name varchar(255) default NULL,
  saferpay_complete int(1) NOT NULL default '0',
  saferpay_complete_result varchar(255) default NULL,
  date_added datetime default NULL,
  last_modified datetime default NULL,
  PRIMARY KEY  (trans_id),
  KEY IDX_CUSTOMERS (customers_id),
  KEY IDX_ORDER (orders_id),
  KEY IDX_SAFERPAY_ID (saferpay_ID)
);";
		xtc_db_query($query_raw);

		// set access-rights to saferpay transactions tool
		$query_res = xtc_db_query("SHOW COLUMNS FROM ". TABLE_ADMIN_ACCESS ." LIKE 'saferpay'");
		if ( xtc_db_num_rows($query_res) == 0 ) {
			xtc_db_query("ALTER TABLE ". TABLE_ADMIN_ACCESS ." ADD saferpay INT(1) NOT NULL default 0");
		}
		xtc_db_query("UPDATE ". TABLE_ADMIN_ACCESS ." SET saferpay = '1' WHERE customers_id = '1'");
		if ( $_SESSION['customer_id'] != '1') {
			xtc_db_query("UPDATE ". TABLE_ADMIN_ACCESS ." SET saferpay = '1' WHERE customers_id = '".$_SESSION['customer_id']."'");
		}
    }

    function remove() {
		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
		return array('MODULE_PAYMENT_SAFERPAYGW_STATUS',
					 'MODULE_PAYMENT_SAFERPAYGW_ALLOWED',
					 //'MODULE_PAYMENT_SAFERPAYGW_LOGIN',
					 //'MODULE_PAYMENT_SAFERPAYGW_PASSWORD',
					 'MODULE_PAYMENT_SAFERPAYGW_ACCOUNT_ID',
					 //'MODULE_PAYMENT_SAFERPAYGW_PATH',
					 'MODULE_PAYMENT_SAFERPAYGW_URLREADER',
					 'MODULE_PAYMENT_SAFERPAYGW_PAYINIT_URL',
					 'MODULE_PAYMENT_SAFERPAYGW_CONFIRM_URL',
					 'MODULE_PAYMENT_SAFERPAYGW_COMPLETE_URL',
					 'MODULE_PAYMENT_SAFERPAYGW_COMPLETE',
					 'MODULE_PAYMENT_SAFERPAYGW_CCCVC',
					 'MODULE_PAYMENT_SAFERPAYGW_CCNAME',
					 'MODULE_PAYMENT_SAFERPAYGW_CURRENCY',

					 'MODULE_PAYMENT_SAFERPAYGW_MENUCOLOR',
					 'MODULE_PAYMENT_SAFERPAYGW_MENUFONTCOLOR',
					 //'MODULE_PAYMENT_SAFERPAYGW_FONT',
					 'MODULE_PAYMENT_SAFERPAYGW_BODYFONTCOLOR',
					 'MODULE_PAYMENT_SAFERPAYGW_BODYCOLOR',
					 'MODULE_PAYMENT_SAFERPAYGW_HEADFONTCOLOR',
					 'MODULE_PAYMENT_SAFERPAYGW_HEADCOLOR',
					 'MODULE_PAYMENT_SAFERPAYGW_HEADLINECOLOR',
					 'MODULE_PAYMENT_SAFERPAYGW_LINKCOLOR',

					 'MODULE_PAYMENT_SAFERPAYGW_ZONE',
					 'MODULE_PAYMENT_SAFERPAYGW_ORDER_STATUS_ID',
					 'MODULE_PAYMENT_SAFERPAYGW_SORT_ORDER');
	}
}

if ( !function_exists('xtc_cfg_pull_down_truefalse') ) {
	function xtc_cfg_pull_down_truefalse($truefalse, $key = '') {

		$name = (($key) ? 'configuration['.$key.']' : 'configuration_value');

		$truefalse_array = array(array ('id' => 'true', 'text' => YES),
							     array('id' => 'false', 'text' => NO));

		return xtc_draw_pull_down_menu($name, $truefalse_array, $truefalse);
	}
}

if ( !function_exists('xtc_get_cfg_truefalse') ) {
	function xtc_get_cfg_truefalse($truefalse, $language_id = '') {

		if ( xtc_not_null($truefalse) ) {
			if ( $truefalse == 'true' ) {
				return YES;
			}
		}
		return NO;
	}
}

if ( !function_exists('xtc_cfg_pull_down_currencies') ) {
	function xtc_cfg_pull_down_currencies($currency_code, $key = '') {

		$name = (($key) ? 'configuration['.$key.']' : 'configuration_value');

		$query_res = xtc_db_query("select title, code from ".TABLE_CURRENCIES." order by title");
		if ( xtc_db_num_rows($query_res) > 1) {
			$currencies_array = array (array ('id' => '', 'text' => TEXT_USER_CURRENCY));
		}
		else {
			$currencies_array = array ();
		}

		while ($record = xtc_db_fetch_array($query_res)) {
			$currencies_array[] = array ('id' => $record['code'], 'text' => $record['title']);
		}

		return xtc_draw_pull_down_menu($name, $currencies_array, $currency_code);
	}
}

if ( !function_exists('xtc_get_currency_name') ) {
	function xtc_get_currency_name($currency_id, $language_id = '') {

		if ( xtc_not_null($currency_id) ) {
			return $currency_id;
		}
		return TEXT_USER_CURRENCY;
	}
}

if ( !function_exists('cfg_pull_down_urlreader') ) {
	function cfg_pull_down_urlreader($urlreader, $key = '') {

		$name = (($key) ? 'configuration['.$key.']' : 'configuration_value');

		$urlreader_ary = array(array ('id' => 'file', 'text' => 'file'),
							     array('id' => 'curl', 'text' => 'curl'));

		return xtc_draw_pull_down_menu($name, $urlreader_ary, $urlreader);
	}
}

MainFactory::load_origin_class('saferpaygw');