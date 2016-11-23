<?php
/* --------------------------------------------------------------
   postfinance.php 2014-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

/* -----------------------------------------------------------------------------------------
   $Id: postfinance.php, v.2.1 swisswebXperts GmbH
   2014-07-18 swisswebXperts GmbH

	 Copyright (c) 2009 swisswebXperts GmbH www.swisswebxperts.ch
	 Released under the GNU General Public License (Version 2)
	 [http://www.gnu.org/licenses/gpl-2.0.html]
   ---------------------------------------------------------------------------------------*/
class postfinance_ORIGIN
{
    var $title, $description, $enabled, $orderid, $productive;

    public $code = 'postfinance';
    public $codeUpperCase = '';
    public $images = array();
    public $pspid = '';

    protected $paymentMethod = '';
    protected $paymentBrand = '';
    protected $paymentMethodList = array();

    protected $shaMode = 'sha512';

    public function __construct()
    {
        global $order;

        $this->codeUpperCase = strtoupper($this->code);

        $this->title = @constant('MODULE_PAYMENT_' . $this->codeUpperCase . '_TEXT_TITLE');
        $this->description = @constant('MODULE_PAYMENT_' . $this->codeUpperCase . '_TEXT_DESCRIPTION');
        $this->info = @constant('MODULE_PAYMENT_' . $this->codeUpperCase . '_TEXT_INFO');
        $this->sort_order = @constant('MODULE_PAYMENT_' . $this->codeUpperCase . '_SORT_ORDER');
        $this->enabled = @constant('MODULE_PAYMENT_' . $this->codeUpperCase . '_STATUS') == 'True';


        if ((int)MODULE_PAYMENT_POSTFINANCEAG_BASIC_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_POSTFINANCEAG_BASIC_ORDER_STATUS_ID;
        }

        if ((int)MODULE_PAYMENT_POSTFINANCEAG_BASIC_ERROR_ORDER_STATUS_ID > 0) {
            $this->order_status_error = MODULE_PAYMENT_POSTFINANCEAG_BASIC_ERROR_ORDER_STATUS_ID;
        }

        if (is_object($order)) {
            $this->update_status();
        }

        $this->productive = MODULE_PAYMENT_POSTFINANCEAG_BASIC_PRODUCTIVE;
        $this->charset = MODULE_PAYMENT_POSTFINANCEAG_BASIC_UTF8;

        if ($this->productive == 'True') {
            // PRODUCTIVE LINK
			if($this->charset == 'UTF8') {
				$this->form_action_url = 'https://e-payment.postfinance.ch/ncol/prod/orderstandard_utf8.asp'; //Link UTF8
			}else{
				$this->form_action_url = 'https://e-payment.postfinance.ch/ncol/prod/orderstandard.asp'; // Link ISO
			}
            $this->pspid = MODULE_PAYMENT_POSTFINANCEAG_BASIC_PSPID_PRODUCTIVE;
			
        } else {
            // TEST LINK
			if($this->charset == 'UTF8') {
				$this->form_action_url = 'https://e-payment.postfinance.ch/ncol/test/orderstandard_utf8.asp'; //Link UTF8
			}else{
			    $this->form_action_url = 'https://e-payment.postfinance.ch/ncol/test/orderstandard.asp'; // Link ISO
			}
			            
            $this->pspid = MODULE_PAYMENT_POSTFINANCEAG_BASIC_PSPID_TEST;
        }

        $this->tmpOrders = true;
        $this->tmpStatus = 0;
    }

    function update_status()
    {
        global $order;

        if (($this->enabled == true) && ((int)@constant('MODULE_PAYMENT_' . $this->codeUpperCase .'_ZONE') > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
                . @constant('MODULE_PAYMENT_' . $this->codeUpperCase .'_ZONE') . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
            while ($check = xtc_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }

            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    function javascript_validation()
    {
        return false;
    }

    function selection()
    {
		global $order;
        $this->info = '';
        $images = '';

		if (MODULE_PAYMENT_POSTFINANCEAG_BASIC_CURRENCY != 'Selected Currency') {
			
			if ($order instanceof order && MODULE_PAYMENT_POSTFINANCEAG_BASIC_CURRENCY != $order->info['currency']) {
				return false;
			}
		}
        foreach ($this->images as $image) {
            $images .= '<img src="includes/modules/payment/postfinanceag/images/' . $image . '.png" />&nbsp;';
        }

        if ($images != '') {
            $this->info .= '<br />' . trim($images);
        }

        // SWIX return array ('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
		//SWIX Modul Zahlungsgebühren
        $selection = array ('id' => $this->code, 'module' => $this->title, 'description' => $this->info);

        if(defined(MODULE_OT_PAYMENTFEE_STATUS) && MODULE_OT_PAYMENTFEE_STATUS == 'True') {
            include_once(DIR_FS_CATALOG . '/includes/modules/order_total/ot_paymentfee.php');

            $arrCosts = ot_paymentfee::getPaymentCosts($this->code);
            $selection['module_cost'] = $arrCosts['text'];
        }

        return $selection;
		// swix end
    }

    function pre_confirmation_check()
    {
        return false;
    }

    function confirmation()
    {
        return false;
    }

    function process_button()
    {
        return false;
    }

    function payment_action()
    {
        global $order, $xtPrice, $insert_id;

        if (@constant('MODULE_PAYMENT_' . $this->codeUpperCase . '_CURRENCY') == 'Selected Currency') {
            $currency = $_SESSION['currency'];
        } else {
            $currency = @constant('MODULE_PAYMENT_' . $this->codeUpperCase . '_CURRENCY');
        }

        if (MODULE_PAYMENT_POSTFINANCEAG_BASIC_LANGUAGE == 'Selected language') {
            $language = $_SESSION['language_code'];

            switch ($language) {
                case 'en':
                    $language = 'en_US';
                    break;
                case 'fr':
                    $language = 'fr_FR';
                    break;
                case 'nl':
                    $language = 'nl_NL';
                    break;
                case 'be':
                    $language = 'nl_BE';
                    break;
                case 'it':
                    $language = 'it_IT';
                    break;
                case 'de':
                    $language = 'de_DE';
                    break;
                case 'es':
                    $language = 'es_ES';
                    break;
                case 'no':
                    $language = 'no_NO';
                    break;
                case 'tr':
                    $language = 'tr_TR';
                    break;
                default :
                    $language = 'en_US';
                    break;
            }
        } else {
            $language = MODULE_PAYMENT_POSTFINANCEAG_BASIC_LANGUAGE;
        }

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $amount = round($order->info['total'] + $order->info['tax'], $xtPrice->get_decimal_places($currency));
        } else {
            $amount = round($order->info['total'], $xtPrice->get_decimal_places($currency));
        }
        $amount = $amount * 100;

        if (ENABLE_SSL == true) {
            $homeurl = HTTPS_SERVER;
        } else {
            $homeurl = HTTP_SERVER;
        }

        $catalogurl = $homeurl . DIR_WS_CATALOG;

        // Alphabetisch sortiert
        $arrParams = array(
            'amount' => $amount,
            'currency' => $currency,
            'language' => $language,
            'homeurl' => 'none',
            'orderID' => $insert_id,
            'PSPID' => $this->pspid,
            'CN' => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
            'EMAIL' => $order->customer['email_address'],
            'owneraddress' => $order->customer['street_address'],
            'ownerZIP' => $order->customer['postcode'],
            'ownertown' => $order->customer['city'],
            'ownercty' => $order->customer['country']['iso_code_2'],
            'TITLE' => STORE_NAME,
            'accepturl' => $catalogurl . 'checkout_process.php',
            'declineurl' => $catalogurl . 'checkout_payment.php',
            'exceptionurl' => $catalogurl . 'checkout_payment.php',
            'cancelurl' => $catalogurl . 'checkout_payment.php',
            'backurl' => $catalogurl . 'checkout_payment.php',
            'COMPLUS' => $catalogurl,
        );

        if (strlen($this->paymentMethod) > 0) {
            $arrParams['PM'] = $this->paymentMethod;
        }

        if (strlen($this->paymentBrand) > 0) {
            $arrParams['BRAND'] = $this->paymentBrand;
        }

        if (count($this->paymentMethodList) > 0) {
            $arrParams['PMLIST'] = implode(';', $this->paymentMethodList);
        }

        if (strlen($order->customer['telephone']) > 0) {
            $arrParams['ownertelno'] = $order->customer['telephone'];
        }

        // Alphabetisch sortieren
        function my_sort($a, $b)
        {
            $a = strtolower($a);
            $b = strtolower($b);

            if ($a == $b) return 0;
            return ($a < $b) ? -1 : 1;
        }

        uksort($arrParams, "my_sort");

        $query = '';
        $shaStr = '';

        foreach ($arrParams as $key => $value) {
            $query .= $key . '=' . urlencode($value) . '&';
            $shaStr .= strtoupper($key) . '=' . $value . MODULE_PAYMENT_POSTFINANCEAG_BASIC_SHA_SIGNATURE;
        }

        $shasign = hash("sha512", $shaStr);
        $query .= 'SHASign=' . strtoupper($shasign);

        xtc_redirect($this->form_action_url . '?' . $query);
        exit;
    }

    function before_process()
    {
        return false;
    }

    function after_process()
    {
	    if ($this->checkResponse($_GET)) {
            $this->setPaymentInfo($_GET['orderID'], $_GET);
            $this->setOrderStatus($_GET['orderID'], $this->order_status);
        }
    }

    function output_error()
    {
        $error = array('title' => MODULE_PAYMENT_POSTFINANCEAG_BASIC_TEXT_ERROR,
            'error' => MODULE_PAYMENT_POSTFINANCEAG_BASIC_ERROR);
    }

    function check()
    {
        if (!isset ($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_".  strtoupper($this->code) ."_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    public function install()
    {
        $configSQL = "INSERT INTO " . TABLE_CONFIGURATION . "
            (
                configuration_key,
                configuration_value,
                configuration_group_id, sort_order,
                use_function,
                set_function,
                date_added
            ) VALUES
            ('MODULE_PAYMENT_POSTFINANCEAG_BASIC_STATUS',
                'True',
                6, 10,
                null,
                'xtc_cfg_select_option(array(\'True\', \'False\'), ',
                now()
            ),
            ('MODULE_PAYMENT_POSTFINANCEAG_BASIC_SORT_ORDER',
                '0',
                6, 20,
                null,
                null,
                now()
            ),
            ('MODULE_PAYMENT_POSTFINANCEAG_BASIC_UTF8',
                'UTF8',
                6, 25,
                null,
                'xtc_cfg_select_option(array(\'UTF8\', \'ISO\'), ',
                now()
            ),			
            ('MODULE_PAYMENT_POSTFINANCEAG_BASIC_PRODUCTIVE',
                'False',
                6, 30,
                null,
                'xtc_cfg_select_option(array(\'True\', \'False\'), ',
                now()
            ),
            ('MODULE_PAYMENT_POSTFINANCEAG_BASIC_PSPID_TEST',
                '',
                6, 40,
                null,
                null,
                now()
            ),
            ('MODULE_PAYMENT_POSTFINANCEAG_BASIC_PSPID_PRODUCTIVE',
                '',
                6, 50,
                null,
                null,
                now()
            ),
            ('MODULE_PAYMENT_POSTFINANCEAG_BASIC_SHA_SIGNATURE',
                '',
                6, 60,
                null,
                null,
                now()
            ),
            ('MODULE_PAYMENT_POSTFINANCEAG_BASIC_LANGUAGE',
                'Selected language',
                6, 70,
                null,
                'xtc_cfg_select_option(array(\'Selected language\', \'de_DE\', \'fr_FR\', \'it_IT\', \'en_US\', \'es_ES\', \'nl_NL\', \'nl_BE\', \'no_NO\', \'tr_TR\'), ',
                now()
            ),
            ('MODULE_PAYMENT_POSTFINANCEAG_BASIC_ALLOWED',
                'CH,LI',
                6, 80,
                null,
                null,
                now()
            ),
            ('MODULE_PAYMENT_POSTFINANCEAG_BASIC_CURRENCY',
                'CHF',
                6, 90,
                null,
                'xtc_cfg_select_option(array(\'Selected Currency\',\'CHF\',\'EUR\',\'USD\'), ',
                now()
            ),
            ('MODULE_PAYMENT_POSTFINANCEAG_BASIC_ZONE',
                '0',
                6, 100,
                'xtc_get_zone_class_title',
                'xtc_cfg_pull_down_zone_classes(',
                now()
            ),
            ('MODULE_PAYMENT_POSTFINANCEAG_BASIC_ORDER_STATUS_ID',
                '0',
                6, 110,
                'xtc_get_order_status_name',
                'xtc_cfg_pull_down_order_statuses(',
                now()
            ),
            ('MODULE_PAYMENT_POSTFINANCEAG_BASIC_ERROR_ORDER_STATUS_ID',
                '0',
                6, 120,
                'xtc_get_order_status_name',
                'xtc_cfg_pull_down_order_statuses(',
                now()
            )
        ";
        xtc_db_query($configSQL);
    }

    function remove()
    {
        xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys()
    {
        $resultSet = xtc_db_query("SELECT * FROM configuration WHERE configuration_key LIKE 'MODULE_PAYMENT_" . strtoupper($this->code) . "%'
            ORDER BY configuration_group_id, sort_order");

        $keys = array();
        while($config = xtc_db_fetch_array($resultSet)) {
            $keys[] = $config['configuration_key'];
        }

        return $keys;
    }

    function processCallback()
    {
        include_once(DIR_FS_CATALOG . 'gm/inc/gm_get_conf.inc.php');

        //$this->setDebug();

        if ($this->checkResponse($_POST)) {

            if (isset($_POST['COMPLUS']) && $_POST['COMPLUS'] != HTTP_SERVER . DIR_WS_CATALOG && $_POST['COMPLUS'] != HTTPS_SERVER . DIR_WS_CATALOG) {
                $this->redirectCallback($_POST);
            }

            $orderId = (int)$_POST['orderID'];

            $order = new order($orderId);
		
            if ($this->isAccepted($_POST['STATUS'])) {

                if (!$this->isOrderSent($orderId)) {
			
                    $this->setPaymentInfo($orderId, $_POST);

                    $language = $this->getOrderLanguage($orderId);

                    $_SESSION['language'] = $language;

                    if(!defined('DATE_FORMAT_LONG')) {
                        define('DATE_FORMAT_LONG', '%d.%m.%Y');
                    }
					
					$coo_recreate_order = MainFactory::create_object('RecreateOrder', array($orderId));
                    $coo_recreate_order->getHtml();

                    $this->setOrderStatus($orderId, $this->order_status);

                    // create subject
                    include_once(DIR_FS_CATALOG . 'gm/inc/gm_get_content.inc.php');

                    $t_subject = gm_get_content('EMAIL_BILLING_SUBJECT_ORDER', $_SESSION['languages_id']);
                    if(empty($t_subject)) $t_subject = EMAIL_BILLING_SUBJECT_ORDER;
                    $order_subject = str_replace('{$nr}', $orderId, $t_subject);
                    $order_subject = str_replace('{$date}', strftime(DATE_FORMAT_LONG), $order_subject);

                    $order_query_check = xtc_db_query("
										SELECT
											customers_email_address,
											customers_firstname,
											customers_lastname,
											gm_order_html,
											gm_order_txt
										FROM " .
                        TABLE_ORDERS . "
										WHERE
											orders_id='".(int)$orderId."'
									");

                    $order_check = xtc_db_fetch_array($order_query_check);

                    $html_mail = $order_check['gm_order_html'];
                    $txt_mail = $order_check['gm_order_txt'];

                    // send mail to admin
                    // BOF GM_MOD:

                    if(SEND_EMAILS == 'true') {
                        // get the sender mail adress. e.g. Host Europe has problems with the customer mail adress.
                        $from_email_address = $order->customer['email_address'];
                        if(SEND_EMAIL_BY_BILLING_ADRESS == 'SHOP_OWNER') {
                            $from_email_address = EMAIL_BILLING_ADDRESS;
                        }
                        xtc_php_mail(
                            $from_email_address,
                            $order->customer['firstname'].' '.$order->customer['lastname'],
                            EMAIL_BILLING_ADDRESS,
                            STORE_NAME,
                            EMAIL_BILLING_FORWARDING_STRING,
                            $order->customer['email_address'],
                            $order->customer['firstname'].' '.$order->customer['lastname'],
                            '',
                            '',
                            $order_subject,
                            $html_mail,
                            $txt_mail
                        );
                    }
                    // send mail to customer
                    // BOF GM_MOD:
                    if(SEND_EMAILS == 'true') {
                        $gm_mail_status = xtc_php_mail(
                            EMAIL_BILLING_ADDRESS,
                            EMAIL_BILLING_NAME,
                            $order->customer['email_address'],
                            $order->customer['firstname'].' '.$order->customer['lastname'],
                            '',
                            EMAIL_BILLING_REPLY_ADDRESS,
                            EMAIL_BILLING_REPLY_ADDRESS_NAME,
                            '',
                            '',
                            $order_subject,
                            $html_mail,
                            $txt_mail
                        );

                        if($gm_mail_status) {
                            xtc_db_query("
											UPDATE
												" . TABLE_ORDERS . "
											SET
												gm_send_order_status		= '1',
												gm_order_send_date			= NOW()
											WHERE
												orders_id = '" . (int)$orderId . "'
										");
                        }
                    }

                    //Clear Cart
                    $customer_id = $order->customer['ID'];
                    xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "'");
                    xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "'");
                }
            } else {
                $this->setOrderStatus($orderId, $this->order_status_error);
            }
        } else {
            throw new Exception('Ungültiger SHAIN');
        }
    }

    function redirectCallback($postData)
    {
        $requestURI = $postData['COMPLUS'] . 'callback/postfinance/callback.php';

        $request = curl_init($requestURI);

        curl_setopt($request, CURLOPT_POST, 1);
        curl_setopt($request, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($request);
        curl_close($request);

        echo $response;
        exit;
    }

    function isAccepted($status)
    {
        return in_array($status, array(5,9));
    }

    function getOrderLanguage($orderId)
    {
        $resultSet = xtc_db_query("SELECT language FROM " . TABLE_ORDERS  . " WHERE orders_id =" . (int)$orderId);
        $record = xtc_db_fetch_array($resultSet);

        return $record['language'];
    }

    function getOrderStatus($orderId)
    {
        $resultSet = xtc_db_query("SELECT orders_status FROM " . TABLE_ORDERS  . " WHERE orders_id =" . (int)$orderId);
        $record = xtc_db_fetch_array($resultSet);

        return $record['orders_status'];
    }

    function isOrderSent($orderId)
    {
        $resultSet = xtc_db_query("SELECT * FROM " . TABLE_ORDERS  . " WHERE orders_id =" . (int)$orderId . " AND gm_send_order_status = 1");
        return xtc_db_num_rows($resultSet) >= 1;
    }

    function checkResponse($params)
    {
        if (isset($params['SHASIGN'])) {
            $shasign = $params['SHASIGN'];
            unset($params['SHASIGN']);

            if (isset($params['tpl'])) {
                unset($params['tpl']);
            }

            return strtoupper($this->getSHAFromData($params)) == strtoupper($shasign);
        }
        return false;
    }

    function getSHAFromData($data)
    {
        $shaStr = '';
        uksort($data, array($this, 'shaSort'));

        foreach($data as $key => $value) {
            if ($value == '') {
                continue;
            }
			
			$value = stripslashes($value);
			
            $shaStr .= strtoupper($key) . '=' . $value . MODULE_PAYMENT_POSTFINANCEAG_BASIC_SHA_SIGNATURE;
        }

        return hash($this->shaMode, $shaStr);
    }

    function shaSort($a, $b)
    {
        $a = strtolower($a);
        $b = strtolower($b);

        if ($a == $b) return 0;
        return ($a < $b) ? -1 : 1;
    }

    function setOrderStatus($orders_id, $orders_status)
    {
        xtc_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status='" . xtc_db_input($orders_status) . "' WHERE orders_id='" . (int)$orders_id . "'");

        $order_history_data = array('orders_id' => (int)$orders_id,
            'orders_status_id' => xtc_db_input($orders_status),
            'date_added' => 'now()',
            'customer_notified' => '0',
            'comments' => '');

        xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $order_history_data);
    }

    function setPaymentInfo($orders_id, $params) {
        $payment_transaction_no = '';
        if (isset($params['PAYID'])) {
            $payment_transaction_no = $params['PAYID'];
        }
        $cc_type = '';
        if (isset($params['BRAND'])) {
            $cc_type = $params['BRAND'];
        }

        if ($payment_transaction_no != '' || $cc_type != '') {
			xtc_db_query("UPDATE ".TABLE_ORDERS."
                SET orders_ident_key = '" . xtc_db_input($payment_transaction_no) . "',
                cc_type = '" . xtc_db_input($cc_type) . "' WHERE orders_id='" . (int)$orders_id . "'");
        }
    }

    private function setDebug()
    {
        $_POST = array (
            "orderID" => '400382',
            "currency" => 'CHF',
            "amount" => '31.08',
            "PM" => 'PostFinance Card',
            "ACCEPTANCE" => 'TEST',
            "STATUS" => '9',
            "CARDNO" => '',
            "ED" => '',
            "CN" => 'Sabine N�f',
            "TRXDATE" => '02/18/14',
            "PAYID" => '28182456',
            "NCERROR" => '0',
            "BRAND" => 'PostFinance Card',
            "CREDITDEBIT" => '',
            "IPCTY" => 'CH',
            "CCCTY" => 'CH',
            "ECI" => '5',
            "CVCCheck" => '',
            "AAVCheck" => '',
            "VC" => '',
            "AAVZIP" => 'NO',
            "AAVADDRESS" => 'NO',
            "COMPLUS" => 'http://swixtest.ch/gambio-dev/',
            "IP" => '46.14.156.123',
            "SHASIGN" => 'EFE533ACE6C32FF1FCE2891D3B60F57C248385E77FF08443786E14D91311613DD24ED0D07B126A1926E18FFFBF86F5557D0676CE9684E98351B6B2A439150FCB',
        );
    }
}
MainFactory::load_origin_class('postfinance');