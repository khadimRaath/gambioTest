<?php
/* --------------------------------------------------------------
  rsmartsepa.php 2015-05-04 wem
  Payment Module 
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

require_once(DIR_FS_CATALOG . 'callback/rsmartsepa/class.rsmartsepamain.php');

class rsmartsepa_ORIGIN extends rsmartsepa_main {
    
    public function __construct() {
        global $order;
        
        parent::__construct();
        
        // DEBUG
        RsmartsepaHelper::debug('printExceptionStackTrace', new Exception("rsmartsepa_ORIGIN->__construct()"));
        RsmartsepaHelper::debug('printDefinedConstants', 'rsmartsepa_ORIGIN->__construct()');
        
        
        $this->code = 'rsmartsepa';
        $this->module = 'rsmartsepa';
        $this->title = RsmartsepaHelper::createRSmartLogoImageTag() . ' ' . MODULE_PAYMENT_RSMARTSEPA_TEXT_TITLE;
        $this->description = $this->_createDescription(); //MODULE_PAYMENT_RSMARTSEPA_TEXT_DESCRIPTION;
        $this->description_checkout = 'Checkout Beschreibung';
        
        if (defined('MODULE_PAYMENT_RSMARTSEPA_STATUS') === true) {
            $this->sort_order = 0;
            if(defined('MODULE_PAYMENT_RSMARTSEPA_SORT_ORDER')) {
                $this->sort_order = constant('MODULE_PAYMENT_RSMARTSEPA_SORT_ORDER');
            }
            
            $this->enabled = false;
            if(defined('MODULE_PAYMENT_RSMARTSEPA_STATUS')) {
                $this->enabled = ((constant('MODULE_PAYMENT_RSMARTSEPA_STATUS') == 'True') ? true : false);
            }
            
            $this->redirect   = true;
            $this->tmpOrders  = true;
            $this->tmpStatus = 0;
            if(defined('MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_TEMP')) {
                $this->tmpStatus = constant('MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_TEMP');
            }
                
        }
        
        if(isset($order)) {
            if(is_object($order)) {
                $this->update_status();
            }
        }
    } // End constructor
    
    function _createDescription() {
        $result = MODULE_PAYMENT_RSMARTSEPA_TEXT_DESCRIPTION;
        
        if(defined('DIR_WS_ADMIN') && strpos($_SERVER['REQUEST_URI'], constant('DIR_WS_ADMIN')) !== false) {
            $curlExisting = RsmartsepaHelper::isPHPExtensionInstalled('curl');
            $libgdExisting = RsmartsepaHelper::isPHPExtensionInstalled('libgd');
            $result = $result . '<br/><br/>' . MODULE_PAYMENT_RSMARTSEPA_STR_SYSTEMREQUIREMENTS . ':';
            $result = $result . '<br/><span>' . '- Version: ' . '</span>' .
                                '<span style="color: green">' . 
                                   RsmartsepaHelper::RSMARTSEPA_VERSION . 
                                '</span>';
            $result = $result . '<br/><span>' . '- cURL: ' . '</span>' .
                                '<span style="color: ' . ($curlExisting ? 'green' : 'red') . '">' . 
                                   ($curlExisting ? MODULE_PAYMENT_RSMARTSEPA_STR_EXISTING: MODULE_PAYMENT_RSMARTSEPA_STR_MISSING) . 
                                '</span>';
            $result = $result . '<br/><span>' . '- libgd: ' . '</span>' .
                                '<span style="color: ' . ($libgdExisting ? 'green' : 'red') . '">' . 
                                   ($libgdExisting ? MODULE_PAYMENT_RSMARTSEPA_STR_EXISTING: MODULE_PAYMENT_RSMARTSEPA_STR_MISSING) . 
                                '</span>';
            
            try {
                RsmartsepaHelper::startLibrary();
                $result = $result . '<br/><span>' . '- Library: ' . '</span>' .
                                    '<span style="color: green">OK</span>';
            } catch(Exception $ex) {
                $result = $result . '<br/><span>' . '- Library: ' . '</span>' .
                                    '<span style="color: red">' . $ex->getMessage() . '</span>';                
            }
            
//            $cronParams = array(
//                'rsmartsepaaction'      => 'rsmartsepacron',
//                'rsmartsepacronkey'     => RsmartsepaHelper::getCronKey(TRUE),
//            );
            $cronUrl = RsmartsepaHelper::getConstantValueAsString('HTTP_SERVER', '') .
                       RsmartsepaHelper::getConstantValueAsString('DIR_WS_CATALOG', '') .
                       'callback/rsmartsepa/cron.php?rsmartsepaaction=rsmartsepacron' .
                       '&rsmartsepacronkey=' . RsmartsepaHelper::getCronKey(TRUE);
//            $cronUrl = RsmartsepaHelper::createCronUrl($cronParams);
            $result = $result . '<br/><span>' . '- Cron URL: ' . '</span>' .
                                '<span style="color: green">' . $cronUrl . '</span>';
            
            $checkMatchServerMarkup = RsmartsepaHelper::createCheckMatchServerMarkup();
            if($checkMatchServerMarkup != '') {
                $result = $result . '<br/><br/>' . $checkMatchServerMarkup . '<br/><br/>';
            }
        }
        
        return $result;
    } // End _createDescription
    
    /**
     * Implementation of payment module method
     * This method is called by Gambio when the checkout process
     * starts to show the headline ('module') and the the description
     * ('description') among other activated payment methods.
     * 
     * 
     * @return type 
     */
    function selection() {
        $rsmartsepaImageUrl = RsmartsepaHelper::createResourceUrl('resources/images/rsmart.png');
        // DEBUG
        RsmartsepaHelper::debug('printExceptionStackTrace', new Exception("rsmartsepa_ORIGIN->selection()"));
        RsmartsepaHelper::debug('log', 'rsmartsepa_ORIGIN->selection():rsmartsepaImageUrl', $rsmartsepaImageUrl);
        
        $rsmartsepaImage = '<img style="border: none; padding: 2px;" src="' . $rsmartsepaImageUrl . '" alt="rSmart Logo" width="100" />';
        
        $desc = $rsmartsepaImage . '<br/>' . 
                MODULE_PAYMENT_RSMARTSEPA_STR_CHECKOUT_DESCRIPTION .
                '<br/><br/>' .
                '<a href="https://itunes.apple.com/de/app/id922443625" target="_blank" style="text-decoration: none;">' .
                ' <img src="' . RsmartsepaHelper::createResourceUrl('resources/images/btn_app_store.png') . '" alt="App Store" width="120" />' .
                '</a>' .
                '&nbsp;&nbsp;&nbsp;' .
                '<a href="https://play.google.com/store/apps/details?id=com.rubean.raalite.android.app&hl=de" target="_blank" style="text-decoration: none;">' .
                ' <img src="' . RsmartsepaHelper::createResourceUrl('resources/images/btn_google_play.png') . '" alt="Play Store" width="112" />' .
                '</a>';
        $result = array(
            'id'            => $this->code,
            'module'        => MODULE_PAYMENT_RSMARTSEPA_STR_CHECKOUT_MODULETITLE,
            'description'   => $desc,
        );
        return $result;
    } // End selection
    
    /**
     * Implementation of payment module method.
     * This method is called by Gambio when this payment method was selected
     * and the user clicked on the 'Weiter' button.
     * 
     * @return boolean
     *     
     */
    function pre_confirmation_check() {
        // DEBUG
        RsmartsepaHelper::debug('printExceptionStackTrace', new Exception("rsmartsepa_ORIGIN->pre_confirmation_check()"));
        
//        $testErrorMsg = '';        
//        if($testErrorMsg != '') {
//            $parameters = array(
//                'payment_error'     => strval($this->code),
//                'error'             => $testErrorMsg,
//            );
//            $url = RsmartsepaHelper::createUrl(FILENAME_CHECKOUT_PAYMENT, $parameters, 'SSL');
//            xtc_redirect($url);
//            return false;
//        }
        
        return true;
    } // End pre_confirmation_check
    
    /**
     * Implementation of payment module method.
     * This method is called by Gambio after pre_confirmation_check()
     * 
     * @return array
     *    An array 
     * 
     *    array(
     *        'title'       => (string) A title,
     *       [ 'fields' ]   =>  
     *    )
     */
    function confirmation() {
        // DEBUG
        RsmartsepaHelper::debug('printExceptionStackTrace', new Exception("rsmartsepa_ORIGIN->confirmation()"));
        
        $confirmationArray = array(
            'title'         => MODULE_PAYMENT_RSMARTSEPA_STR_CHECKOUT_CONFIRMATION,
        );
        return $confirmationArray;
    } // End confirmation
    
    /**
     * Implementation of payment module method.
     * This method is called by Gambio after confirmation()
     * 
     * @return string|boolean
     *    Either html (e.g. for hidden fields) or FALSE 
     */
    function process_button() {
        // DEBUG
        RsmartsepaHelper::debug('printExceptionStackTrace', new Exception("rsmartsepa_ORIGIN->process_button()"));
        
        return false;
    } // End process_button
    
    function javascript_validation() {
        // DEBUG
        RsmartsepaHelper::debug('printExceptionStackTrace', new Exception("rsmartsepa_ORIGIN->javascript_validation()"));
        RsmartsepaHelper::debug('printGlobalVariables', 'rsmartsepa_ORIGIN->javascript_validation()');
        RsmartsepaHelper::debug('printSessionVariables', 'rsmartsepa_ORIGIN->javascript_validation()');
        
        return '';
    } // End javascript_validation
    
    function before_process() {
        // DEBUG
        RsmartsepaHelper::debug('printExceptionStackTrace', new Exception("rsmartsepa_ORIGIN->before_process()"));
        RsmartsepaHelper::debug('printGlobalVariables', 'rsmartsepa_ORIGIN->before_process()');
        RsmartsepaHelper::debug('printSessionVariables', 'rsmartsepa_ORIGIN->before_process()');
        
        return false;
    } // End before_process
    
    function payment_action() {
        global $_POST, $order, $xtPrice, $insert_id;
        
        RsmartsepaHelper::setSessionValue('rsmartsepa', NULL);
        
        // DEBUG
        RsmartsepaHelper::debug('printExceptionStackTrace', new Exception("rsmartsepa_ORIGIN->payment_action()"));
        RsmartsepaHelper::debug('printGlobalVariables', 'rsmartsepa_ORIGIN->payment_action()');
        RsmartsepaHelper::debug('printSessionVariables', 'rsmartsepa_ORIGIN->payment_action()');
        
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $total = $order->info['total'] + $order->info['tax'];
        } else {
            $total = $order->info['total'];
        }
        
        $currency = $order->info['currency'];
        $transactionArray = array(
            'rsmartsepatrInsertId'              => $insert_id,
            'rsmartsepatrTransactionId'         => strval($insert_id),
            'rsmartsepatrAmount'                => round($xtPrice->xtcCalculateCurrEx($total, $currency), $xtPrice->get_decimal_places($currency)),
            'rsmartsepatrCurrency'              => $currency,
            'rsmartsepatrShopName'              => STORE_NAME,
            'rsmartsepatrTradesc'               => strval($insert_id), // 'Ihre Bestellung bei ' . STORE_NAME,
            'rsmartsepatrSimulationMode'        => RsmartsepaHelper::getConstantValueAsBoolean('MODULE_PAYMENT_RSMARTSEPA_SIMULATIONMODE', 'false'),
            'rsmartsepatrDebugMode'             => RsmartsepaHelper::getConstantValueAsBoolean('MODULE_PAYMENT_RSMARTSEPA_DEBUGMODE', 'false'),
            'rsmartsepatrQrCodeTemplateName'    => 'rsmartsepadefault',
            'rsmartsepatrUrlAjax'               => RsmartsepaHelper::createCallbackUrl(array('rsmartsepaaction' => 'rsmartsepaajax'), 'SSL'),
            'rsmartsepatrUrlRedirect'           => RsmartsepaHelper::createCallbackUrl(array('rsmartsepaaction' => 'rsmartseparedirect'), 'SSL'),
            'rsmartsepatrTID'                   => '',
            'rsmartsepatrHash'                  => '',
            'rsmartsepatrQrCodeB64'             => '',
            'rsmartsepatrQrCodeUrl'             => '',
            'rsmartsepatrRaaUrl'                => '',
            'rsmartsepatrTemplateName'          => 'rsmartsepapayment',
            'rsmartsepatrShopUrl'               => RsmartsepaHelper::getShopUrl(),
        );
        
        // Start the transaction
        try {
            
            RsmartsepaHelper::startLibrary();
            $RsmartsepaTransactionLogger = new RsmartsepaTransactionLogger();
            TerminalSdkLibrary::setLogger($RsmartsepaTransactionLogger);
            $RsmartsepaTransactionWrapper = new RsmartsepaTransactionWrapper('OPM', $transactionArray['rsmartsepatrSimulationMode'], array());
            $RsmartsepaTransactionWrapper->setTransactionConstants();
            $SellerAccountInfo = NULL;
            $Raautil_DataStore = NULL;
            ($Raautil_DataStore instanceof Raautil_DataStore);
            $Raautil_DataStore = $RsmartsepaTransactionWrapper->createAmountTransaction($transactionArray['rsmartsepatrAmount'], 
                                                                                        $transactionArray['rsmartsepatrCurrency'], 
                                                                                        $transactionArray['rsmartsepatrTransactionId'], 
                                                                                        $transactionArray['rsmartsepatrTradesc'], 
                                                                                        $SellerAccountInfo);
            $transactionArray['rsmartsepatrTID'] = $Raautil_DataStore->getTransactionResultTID();
            $transactionArray['rsmartsepatrHash'] = $Raautil_DataStore->getHashCode();
            $transactionArray['rsmartsepatrQrCodeB64'] = $Raautil_DataStore->getQRCodeB64();
            $parameters = array(
                'rsmartsepaaction'      => 'rsmartsepagetres',
                'rsmartseparestype'     => 'qrcode',
                'rsmartseparesname'     => $transactionArray['rsmartsepatrTID'],
            );
            $transactionArray['rsmartsepatrQrCodeUrl'] = RsmartsepaHelper::createUrl('callback/rsmartsepa/callback.php', $parameters, 'SSL', true, false);
            $transactionArray['rsmartsepatrRaaUrl'] = 'raa://?qrcode=' . $transactionArray['rsmartsepatrQrCodeB64'];
            
            $_SESSION['rsmartsepa'] = $transactionArray;
            // DEBUG
            RsmartsepaHelper::debug('log', 'payment_action', $transactionArray);
            
            $_SESSION['rsmartsepa'] = $transactionArray;
            
            //$html = RsmartsepaHelper::renderTemplate($templateName, $transactionArray);
            //RsmartsepaHelper::htmlOutput($html);
            
            // Redirect to display
            $displayUrl = RsmartsepaHelper::createCallbackUrl(array('rsmartsepaaction' => 'rsmartsepadisplay'), 'SSL');
            xtc_redirect($displayUrl);
            
            return TRUE;
            
        } catch(Exception $ex) {
            $errorMsg = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_ERROR_CREATETRANSACTION', "Bezahlung mit rSmartSEPA fehlgeschlagen!") . 
                        ' ' .
                        RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_REASON', "Grund") .
                        ': ' . $ex->getMessage();
            
            // Update Order
            RsmartsepaHelper::orderUpdate($insert_id, FALSE, $errorMsg);
                    
                    
            $parameters = array(
                'payment_error'     => strval($this->code),
                'error'             => $errorMsg,
            );
            $url = RsmartsepaHelper::createUrl(FILENAME_CHECKOUT_PAYMENT, $parameters, 'SSL');
            xtc_redirect($url);
            return false;
        }
        
        // Redirect to display
        //$displayUrl = RsmartsepaHelper::createCallbackUrl(array('rsmartsepaaction' => 'rsmartsepadisplay'), 'SSL');
        //xtc_redirect($displayUrl);
        
        return true;
    } // End payment_action
    
    function after_process() {
        // DEBUG
        RsmartsepaHelper::debug('printExceptionStackTrace', new Exception("rsmartsepa_ORIGIN->after_process()"));
        RsmartsepaHelper::debug('printGlobalVariables', 'rsmartsepa_ORIGIN->after_process()');
        RsmartsepaHelper::debug('printSessionVariables', 'rsmartsepa_ORIGIN->after_process()');
        
        return false;
    } // End after_process
    
    
} // End class rsmartsepa_ORIGIN

MainFactory::load_origin_class('rsmartsepa');

