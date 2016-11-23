<?php
/* --------------------------------------------------------------
  callback.php 2015-05-04 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

function rsmartsepa_callback_exists_order($order_id = 0) {
    $order_id = (int)$order_id;
    $order_query = xtc_db_query('select * from ' . TABLE_ORDERS . " where orders_id = '" . $order_id . "'");
    if (xtc_db_num_rows($order_query) < 1) {
        // Order not found
        return FALSE;
    }
    else {
        return TRUE;
    }
} // End rsmartsepa_callback_exists_order

function rsmartsepa_callback_update_order($order_id = 0, $paymentSuccess = FALSE, $historyMessage = '') {
    $order_id = (int)$order_id;
    $paymentSuccess = isset($paymentSuccess) ?($paymentSuccess == TRUE ? TRUE : FALSE) : FALSE;
    if(!rsmartsepa_callback_exists_order($order_id)) {
        return FALSE;
    }
    
    $historyMessage = isset($historyMessage) ? (is_string($historyMessage) ? trim($historyMessage) : '') : '';
    if($paymentSuccess == TRUE) {
        $newStatus = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_OK', '1');
        $sql = "update " . TABLE_ORDERS . " set orders_status = '" . $newStatus . 
               "', last_modified = now() where orders_id = '" . $order_id . "'";
        if(xtc_db_query($sql)) {
            // Status history
            $sql_data_array = array(
                'orders_id'         => $order_id,
                'orders_status_id'  => $newStatus,
                'date_added'        => 'now()',
                'customer_notified' => 1, // TRUE
                'comments'          => $historyMessage,
            );
            xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
            
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    else {
        // Payment failed
        $newStatus = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_ERROR', '99');
        $sql = "update " . TABLE_ORDERS . " set orders_status = '" . $newStatus . 
               "', last_modified = now() where orders_id = '" . $order_id . "'";
        if(xtc_db_query($sql)) {
            // Status history
            $sql_data_array = array(
                'orders_id'         => $order_id,
                'orders_status_id'  => $newStatus,
                'date_added'        => 'now()',
                'customer_notified' => 0, // TRUE
                'comments'          => $historyMessage,
            );
            xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
            
            return TRUE;
        }
        else {
            return FALSE;
        }            
    }
} // End rsmartsepa_callback_update_order


function rsmartsepa_callback_deliver_resource() {
    // Action for delivering a resource and exit
    RsmartsepaHelper::deliverResource();
    exit;
} // End rsmartsepa_callback_deliver_resource

function rsmartsepa_callback_ajax() {
    $transactionArray = array();
    if(isset($_SESSION['rsmartsepa'])) {
        if(is_array($_SESSION['rsmartsepa'])) {
            $transactionArray = $_SESSION['rsmartsepa'];
        }
    }
    // DEBUG
    RsmartsepaHelper::debug('log', 'callback:rsmartsepaajax', $transactionArray);

    $rsmartsepatrSimulationMode = RsmartsepaHelper::getConstantValueAsBoolean('MODULE_PAYMENT_RSMARTSEPA_SIMULATIONMODE', 'false');
    
    $postData = trim(RsmartsepaHelper::getRequestValue('json', '', FALSE));
    $jsonObj = NULL;
    if($postData != '') {
        $jsonObj = json_decode($postData);
    }
    if(!isset($jsonObj) || !is_object($jsonObj)) {
        RsmartsepaHelper::ajaxReturn('9', 'No jsonObj received', '');
    }
    
    if($jsonObj->action == 'MATCH') {
        $tid = $jsonObj->tid;
        $hash = $jsonObj->hash;
        try {
            RsmartsepaHelper::startLibrary();
            $RsmartsepaTransactionLogger = new RsmartsepaTransactionLogger();
            TerminalSdkLibrary::setLogger($RsmartsepaTransactionLogger);
            $RsmartsepaTransactionWrapper = new RsmartsepaTransactionWrapper('OPM', $rsmartsepatrSimulationMode, array());
            $RsmartsepaTransactionWrapper->setTransactionConstants();
            $resultArray = $RsmartsepaTransactionWrapper->matchTransaction($tid, $hash, TRUE);
            $Raautil_DataStore = $resultArray['Raautil_DataStore'];
            $status = $Raautil_DataStore->getLastStatus();
            $nextHash = $Raautil_DataStore->getHashCode();
            $order_id = $Raautil_DataStore->getTransactionId();
            if($status == 'MATCH') {
                $historyMessage = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTSUCCESS', 'Bezahlung mit rSm@rt war erfolgreich');
                try {
                    rsmartsepa_callback_update_order($order_id, TRUE, $historyMessage);
                } catch (Exception $ex) {}
                
                // 2015.05.04: The following statements are commented out because in the redirect function we will
                //             redirect to CHECKOUT_PROCESS that will send the email for us. The necessary member variable tmpOrders
                //             is set in includes/modules/payment/rsmartsepa.php in the constructor.                
//                $emailError = '';
//                try {
//                    rsmartsepa_callback_send_email($order_id);
//                } catch (Exception $ex) {
//                    $emailError = $ex->getMessage();
//                }
//                if($emailError != '') {
//                    try {
//                        rsmartsepa_callback_update_order($order_id, TRUE, $emailError);
//                    } catch (Exception $ex) {}
//                }
            }
            else if($status == 'FAILURE' || $status == 'ERROR') {
                $historyMessage = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTFAILED', 'Bezahlung mit rSm@rt ist fehlgeschlagen!');
                try {
                    rsmartsepa_callback_update_order($order_id, FALSE, $historyMessage);
                } catch (Exception $ex) {}
            }
            
            RsmartsepaHelper::ajaxReturn('0', $status, $nextHash);
        } catch(Exception $ex) {
            $unauthorizedCall = FALSE;
            if($ex instanceof Raautil_TransactionException) {
                $unauthorizedCall = $ex->isUnauthorizedCall();
            }
            if($unauthorizedCall == TRUE) {
                RsmartsepaHelper::ajaxReturn('8', $ex->getMessage(), '');
            }
            else {
                RsmartsepaHelper::ajaxReturn('9', $ex->getMessage(), '');
            }
        }
    } // end: MATCH
    else if($jsonObj->action == 'REMOVE') {
        $tid = $jsonObj->tid;
        $hash = $jsonObj->hash;
        try {
            RsmartsepaHelper::startLibrary();
            $RsmartsepaTransactionLogger = new RsmartsepaTransactionLogger();
            TerminalSdkLibrary::setLogger($RsmartsepaTransactionLogger);
            $RsmartsepaTransactionWrapper = new RsmartsepaTransactionWrapper('OPM', $rsmartsepatrSimulationMode, array());
            $RsmartsepaTransactionWrapper->setTransactionConstants();
            $resultArray = $RsmartsepaTransactionWrapper->removeTransaction($tid, $hash, TRUE);
            $Raautil_DataStore = $resultArray['Raautil_DataStore'];
            $status = $Raautil_DataStore->getLastStatus();
            $nextHash = $Raautil_DataStore->getHashCode();
            $order_id = $Raautil_DataStore->getTransactionId();
            if($status == 'MATCH') {
                $historyMessage = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTCANCELLED', 'Bezahlung mit rSm@rt wurde abgebrochen!');
                try {
                    rsmartsepa_callback_update_order($order_id, FALSE, $historyMessage);
                } catch (Exception $ex) {}
            }
            RsmartsepaHelper::ajaxReturn('0', $status, $nextHash);
        } catch(Exception $ex) {
            $unauthorizedCall = FALSE;
            if($ex instanceof Raautil_TransactionException) {
                $unauthorizedCall = $ex->isUnauthorizedCall();
            }
            if($unauthorizedCall == TRUE) {
                RsmartsepaHelper::ajaxReturn('8', $ex->getMessage(), '');
            }
            else {
                RsmartsepaHelper::ajaxReturn('9', $ex->getMessage(), '');
            }
        }
    } // end: REMOVE
    else if($jsonObj->action == 'SIMULATEMATCH' || $jsonObj->action == 'SIMULATEFAILURE') {
        $tid = $jsonObj->tid;
        try {
            $status = 'MATCH';
            if($jsonObj->action == 'SIMULATEFAILURE') {
                $status = 'FAILURE';
            }
            RsmartsepaHelper::startLibrary();
            $RsmartsepaTransactionLogger = new RsmartsepaTransactionLogger();
            TerminalSdkLibrary::setLogger($RsmartsepaTransactionLogger);
            $RsmartsepaTransactionWrapper = new RsmartsepaTransactionWrapper('OPM', $rsmartsepatrSimulationMode, array());
            $Raautil_DataStore = $RsmartsepaTransactionWrapper->readDatastore($tid);
            if(isset($Raautil_DataStore)) {
                $Raautil_DataStore->setSimulatedStatus($status);
                $RsmartsepaTransactionWrapper->updateDatastore($tid, $Raautil_DataStore);
                RsmartsepaHelper::ajaxReturn('0', 'Raautil_DataStore updated', '');
            }
            else {
                RsmartsepaHelper::ajaxReturn('0', 'Raautil_DataStore not found', '');
            }
        } catch(Exception $ex) {
            RsmartsepaHelper::ajaxReturn('0', $ex->getMessage(), '');
        }
    } // end: SIMULATEMATCH, SIMULATEFAILURE
    else if($jsonObj->action == 'CHECKMATCHSERVER') {
        // This constant prevents the class RsmartsepaDataStoreProvider from
        // persisting the datastore to the database table.
        // Instead of this, the datastore will be stored temporary
        // in an array within the class RsmartsepaDataStoreProvider
        if(!defined('RAAUTIL_USE_TEMPORARY_DATASTORE')) {
            define('RAAUTIL_USE_TEMPORARY_DATASTORE', 'true');
        }
        $infoArray = array(
            'simulation'        => $rsmartsepatrSimulationMode,
            'terminaldata'      => array(),
        );
        
        try {
            RsmartsepaHelper::startLibrary();
            $RsmartsepaTransactionLogger = new RsmartsepaTransactionLogger();
            TerminalSdkLibrary::setLogger($RsmartsepaTransactionLogger);
            $RsmartsepaTransactionWrapper = new RsmartsepaTransactionWrapper('OPM', $rsmartsepatrSimulationMode, array());
            $RsmartsepaTransactionWrapper->setTransactionConstants();
            
            $infoArray = $RsmartsepaTransactionWrapper->getCheckMatchServerInfos();
            
            $SellerAccountInfo = NULL;
            $Raautil_DataStore = NULL;
            ($Raautil_DataStore instanceof Raautil_DataStore);
            $amount = 0.05;
            $currency = 'EUR';
            $traId = 'test001';
            $tradesc = 'Check';
            $Raautil_DataStore = $RsmartsepaTransactionWrapper->createAmountTransaction($amount, 
                                                                                        $currency, 
                                                                                        $traId, 
                                                                                        $tradesc, 
                                                                                        $SellerAccountInfo);
            
            $successMessage = "Success";
            $tid = $Raautil_DataStore->getTransactionResultTID();
            $hash = $Raautil_DataStore->getHashCode();
            try {
                $resultArray = $RsmartsepaTransactionWrapper->removeTransaction($tid, $hash, FALSE);
                $Raautil_DataStore = $resultArray['Raautil_DataStore'];
                $status = $Raautil_DataStore->getLastStatus();
                $successMessage = $successMessage . ' (Testtransaction was removed with Status: ' . $status . ')';
            } catch (Exception $ex2) {
                $successMessage = $successMessage . ' (Testtransaction could not be removed: ' . $ex2->getMessage() . ')';
            }
            
            // Collect Infos
            $infoStr = $successMessage . "\r\n" .
                       "- Simulation: " . ($infoArray['simulation'] == TRUE ? 'true' : 'false') . "\r\n";
            if(count($infoArray['terminaldata'] > 0)) {
                $infoStr = $infoStr . "- URI: " . $infoArray['terminaldata']['URI'] . "\r\n" .
                                      "- matchServiceResolver: " . $infoArray['terminaldata']['matchServiceResolver'] . "\r\n" .
                                      "- applicationId: " . $infoArray['terminaldata']['applicationId'] . "\r\n" .
                                      "- operationPath: " . $infoArray['terminaldata']['operationPath'] . "\r\n" .
                                      "- sellerId: " . $infoArray['terminaldata']['sellerId'] . "\r\n" .
                                      "- sellerKey: " . $infoArray['terminaldata']['key'] . "\r\n" .
                                      "- providerId: " . $infoArray['terminaldata']['providerId'] . "\r\n" .
                                      "- salesPointId: " . $infoArray['terminaldata']['salesPointId'] . "\r\n" .
                                      "- countryId: " . $infoArray['terminaldata']['countryId'] . "\r\n" .
                                      "- description: " . $infoArray['terminaldata']['description'] . "\r\n" .
                                      "- secure: " . $infoArray['terminaldata']['secure'] . "\r\n";
            }
            
            
            $result = array(
                'status'        => 'ok',
                'result'        => $infoStr, // 'Success',
                'hash'          => '',
            );
            RsmartsepaHelper::jsonOutput($result, TRUE);                        
        } catch(Exception $ex) {
            $infoStr = 'Error: ' . $ex->getMessage() . ' (may be that terminaldata are incorrect) !' . "\r\n" .
                       "- Simulation: " . ($infoArray['simulation'] == TRUE ? 'true' : 'false') . "\r\n";
            if(count($infoArray['terminaldata'] > 0)) {
                $infoStr = $infoStr . "- URI: " . $infoArray['terminaldata']['URI'] . "\r\n" .
                                      "- matchServiceResolver: " . $infoArray['terminaldata']['matchServiceResolver'] . "\r\n" .
                                      "- applicationId: " . $infoArray['terminaldata']['applicationId'] . "\r\n" .
                                      "- operationPath: " . $infoArray['terminaldata']['operationPath'] . "\r\n" .
                                      "- sellerId: " . $infoArray['terminaldata']['sellerId'] . "\r\n" .
                                      "- sellerKey: " . $infoArray['terminaldata']['key'] . "\r\n" .
                                      "- providerId: " . $infoArray['terminaldata']['providerId'] . "\r\n" .
                                      "- salesPointId: " . $infoArray['terminaldata']['salesPointId'] . "\r\n" .
                                      "- countryId: " . $infoArray['terminaldata']['countryId'] . "\r\n" .
                                      "- description: " . $infoArray['terminaldata']['description'] . "\r\n" .
                                      "- secure: " . $infoArray['terminaldata']['secure'] . "\r\n";
            }
            
            $result = array(
                'status'        => 'ok',
                'result'        => $infoStr, // 'Error: ' . $ex->getMessage() . ' (may be that terminaldata are incorrect) !',
                'hash'          => '',
            );
            RsmartsepaHelper::jsonOutput($result, TRUE);            
        }
    } // end CHECKMATCHSERVER
    else {
        $result = array(
            'status'        => 'error',
            'result'        => 'error',
            'hash'          => '',
        );
        RsmartsepaHelper::jsonOutput($result, TRUE);
    }
} // End rsmartsepa_callback_ajax

function rsmartsepa_callback_cron() {
    // MODULE_PAYMENT_RSMARTSEPA_STATUS
    $moduleEnabled = RsmartsepaHelper::getConstantValueAsBoolean('MODULE_PAYMENT_RSMARTSEPA_STATUS', FALSE);
    if($moduleEnabled == FALSE) {
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Cron not allowed. Module is disabled');
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): F I N I S H E D');
        RsmartsepaHelper::textOutput(RsmartsepaHelper::cronGetMessagesAsString(), TRUE);
        exit;
    }
    
    $rsmartsepa_cronkey = trim(RsmartsepaHelper::getRequestValue('rsmartsepacronkey', ''));
    if($rsmartsepa_cronkey == '') {
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Cron not allowed. Wrong action or key');
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): F I N I S H E D');
        RsmartsepaHelper::textOutput(RsmartsepaHelper::cronGetMessagesAsString(), TRUE);
        exit;
    }
    $rsmartsepa_inicronkey = RsmartsepaHelper::getCronKey();
    if($rsmartsepa_inicronkey == '' || $rsmartsepa_inicronkey == 'undefined') {
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Cron not allowed. Invalid key');
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): F I N I S H E D');
        RsmartsepaHelper::textOutput(RsmartsepaHelper::cronGetMessagesAsString(), TRUE);
        exit;
    }
    if($rsmartsepa_cronkey != $rsmartsepa_inicronkey) {
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Cron not allowed. Invalid key');
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): F I N I S H E D');
        RsmartsepaHelper::textOutput(RsmartsepaHelper::cronGetMessagesAsString(), TRUE);
        exit;        
    }
    
    // Read all TIDs from the database where ACTION=MATCH, STATUS=PENDING and the last
    // update timestamp is 15 minutes ago
    RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Search PENDING TIDs where change time is 15 minutes ago');
    $durationSeconds = 15 * 60;
    $action = 'MATCH';
    $status = 'PENDING';
    $sortOrder = 'desc';
    $checkTidArray = RsmartsepaHelper::tableRsmartSepaReadAllTIDs($durationSeconds, $action, $status, $sortOrder);
    if(count($checkTidArray) == 0) {
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): No PENDING TIDs found');
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): F I N I S H E D');
        RsmartsepaHelper::textOutput(RsmartsepaHelper::cronGetMessagesAsString(), TRUE);
        exit; 
    }
    
    $rsmartsepatrSimulationMode = RsmartsepaHelper::getConstantValueAsBoolean('MODULE_PAYMENT_RSMARTSEPA_SIMULATIONMODE', 'false');
    RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): SimulationMode=' . ($rsmartsepatrSimulationMode == TRUE ? 'true' : 'false'));
    
    try {
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Start Library');
        RsmartsepaHelper::startLibrary();
        $RsmartsepaTransactionLogger = new RsmartsepaTransactionLogger();
        TerminalSdkLibrary::setLogger($RsmartsepaTransactionLogger);
        $RsmartsepaTransactionWrapper = new RsmartsepaTransactionWrapper('OPM', $rsmartsepatrSimulationMode, array());
        $RsmartsepaTransactionWrapper->setTransactionConstants();
        
        foreach($checkTidArray as $tid) {
            $Raautil_DataStore = $RsmartsepaTransactionWrapper->readDatastore($tid);
            if(!isset($Raautil_DataStore)) {
                // DEBUG
                RsmartsepaHelper::debug('log', 'callback:rsmartsepa_callback_cron()', '$Raautil_DataStore for TID=' . $tid . ' is NULL');
                continue;
            }
            
            RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Checkig TID ' . $tid);
            
            $status = $Raautil_DataStore->getLastStatus();
            $order_id = $Raautil_DataStore->getTransactionId();
            
            $paymentSuccess = $RsmartsepaTransactionWrapper->isAmountTransactionHistorySuccess($tid);
            
            if($paymentSuccess == TRUE) {
                RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): History Payment Successful');
                $historyMessage = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTSUCCESS', 'Bezahlung mit rSm@rt war erfolgreich');
                RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Update Order ' . strval($order_id));
                try {
                    rsmartsepa_callback_update_order($order_id, TRUE, $historyMessage);
                } catch (Exception $ex) {}
                RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Send Mail');
                try {
                    rsmartsepa_callback_send_email($order_id);
                    RsmartsepaHelper::tableRsmartSepaDeleteByTID($tid);
                    RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Database record deleted');
                } catch (Exception $ex) {}
            }
            else {
                $historyMessage = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTFAILED', 'Bezahlung mit rSm@rt ist fehlgeschlagen!');
                try {
                    rsmartsepa_callback_update_order($order_id, FALSE, $historyMessage);
                } catch (Exception $ex) {}
                
                RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): History Payment Failed');                
                RsmartsepaHelper::tableRsmartSepaDeleteByTID($tid);
                RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Database record deleted');
            }
        } // End foreach
        
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): F I N I S H E D');
        RsmartsepaHelper::textOutput(RsmartsepaHelper::cronGetMessagesAsString(), TRUE);
        exit; 
    } catch (Exception $ex) {
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): Exception: ' . $ex->getMessage());
        RsmartsepaHelper::cronAddMessage('CRON(rsmartsepa): F I N I S H E D');
        RsmartsepaHelper::textOutput(RsmartsepaHelper::cronGetMessagesAsString(), TRUE);
        exit;
    }
} // End rsmartsepa_callback_cron


function rsmartsepa_callback_redirect() {
    try {
        RsmartsepaHelper::setSessionValue('rsmartsepa', NULL);
        $tid = trim(RsmartsepaHelper::getRequestValue('tid', ''));
        $hash = trim(RsmartsepaHelper::getRequestValue('hash', ''));
        // DEBUG
        RsmartsepaHelper::debug('log', 'callback:rsmartsepa_callback_redirect', 'TID=' . $tid . ', HASH=' . $hash);
        
        $rsmartsepatrSimulationMode = RsmartsepaHelper::getConstantValueAsBoolean('MODULE_PAYMENT_RSMARTSEPA_SIMULATIONMODE', 'false');
        RsmartsepaHelper::startLibrary();
        $RsmartsepaTransactionLogger = new RsmartsepaTransactionLogger();
        TerminalSdkLibrary::setLogger($RsmartsepaTransactionLogger);
        $RsmartsepaTransactionWrapper = new RsmartsepaTransactionWrapper('OPM', $rsmartsepatrSimulationMode, array());
        $Raautil_DataStore = $RsmartsepaTransactionWrapper->readDatastore($tid);
        if(isset($Raautil_DataStore)) {
            // DEBUG
            RsmartsepaHelper::debug('log', 'callback:rsmartsepa_callback_redirect', '$Raautil_DataStore found');
            
            if($Raautil_DataStore->getHashCode() != $hash) {
                RsmartsepaHelper::debug('log', 'callback:rsmartsepa_callback_redirect', 'Invalid hash: Required=' . $Raautil_DataStore->getHashCode() . ', hash=' . $hash);
                $errorMsg = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTINVALIDHASH', "Bezahlung mit rSm@rt wurde wegen eines Fehlers abgebrochen!");
                try {
                    RsmartsepaHelper::tableRsmartSepaDeleteByTID($tid);
                    $order_id = $Raautil_DataStore->getTransactionId();
                    try {
                        rsmartsepa_callback_update_order($order_id, FALSE, $errorMsg);
                    } catch (Exception $ex) {}
                } catch (Exception $ex) {
                    
                }
                $parameters = array(
                    'payment_error'     => 'rsmartsepa',
                    'error'             => $errorMsg,
                );
                $url = RsmartsepaHelper::createUrl(FILENAME_CHECKOUT_PAYMENT, $parameters, 'SSL');
                xtc_redirect($url);
                return false; 
            }
            
            $lastAction = $Raautil_DataStore->getLastAction();
            $lastStatus = $Raautil_DataStore->getLastStatus();
            // DEBUG
            RsmartsepaHelper::debug('log', 'callback:rsmartsepa_callback_redirect', 'lastAction=' . $lastAction . ', lastStatus=' . $lastStatus);
            
            if($lastAction == 'MATCH' && $lastStatus == 'MATCH') {
                // 2015.05.04: We leave the session values as is because we redirect to CHECKOUT_PROCESS,
                //             that will handle the email functionality for us.                
//                $cart = RsmartsepaHelper::getSessionValue('cart', NULL);
//                if(isset($cart)) {
//                    $cart->reset(true);
//                }
//                RsmartsepaHelper::setSessionValue('sendto', NULL);
//                RsmartsepaHelper::setSessionValue('billto', NULL);
//                RsmartsepaHelper::setSessionValue('shipping', NULL);
//                RsmartsepaHelper::setSessionValue('payment', NULL);
//                RsmartsepaHelper::setSessionValue('comments', NULL);
//                RsmartsepaHelper::setSessionValue('last_order', NULL);
//                RsmartsepaHelper::setSessionValue('credit_covers', NULL);
                
                RsmartsepaHelper::tableRsmartSepaDeleteByTID($tid);
                
                $successMsg = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTSUCCESS', "Bezahlung mit rSm@rt war erfolgreich");
                $parameters = array(
                    'order_success_text'     => $successMsg,
                );
                //$url = RsmartsepaHelper::createUrl(FILENAME_CHECKOUT_SUCCESS, $parameters, 'SSL');
                $url = RsmartsepaHelper::createUrl(FILENAME_CHECKOUT_PROCESS, $parameters, 'SSL');
                xtc_redirect($url);
                return true;  
            }
            else if($lastAction == 'MATCH' && ($lastStatus == 'FAILURE' || $lastStatus == 'ERROR')) {
                RsmartsepaHelper::tableRsmartSepaDeleteByTID($tid);
                
                $errorMsg = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTFAILED', "Bezahlung mit rSm@rt ist fehlgeschlagen!");
                $parameters = array(
                    'payment_error'     => 'rsmartsepa',
                    'error'             => $errorMsg,
                );
                $url = RsmartsepaHelper::createUrl(FILENAME_CHECKOUT_PAYMENT, $parameters, 'SSL');
                xtc_redirect($url);
                return false;                
            }
            else if($lastAction == 'REMOVE' && $lastStatus == 'MATCH') {
                RsmartsepaHelper::tableRsmartSepaDeleteByTID($tid);
                
                $errorMsg = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTCANCELLED', "Bezahlung mit rSm@rt wurde abgebrochen!");
                $parameters = array(
                    'payment_error'     => 'rsmartsepa',
                    'error'             => $errorMsg,
                );
                $url = RsmartsepaHelper::createUrl(FILENAME_CHECKOUT_PAYMENT, $parameters, 'SSL');
                xtc_redirect($url);
                return false;                
            }
            else if($lastAction == 'REMOVE' && ($lastStatus == 'FAILURE' || $lastStatus == 'ERROR')) {
                RsmartsepaHelper::tableRsmartSepaDeleteByTID($tid);
                
                $errorMsg = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTCANCELFAILED', "Bezahlvorgang konnte auf dem Server nicht mehr erfolgreich abgebrochen werden!");
                $parameters = array(
                    'payment_error'     => 'rsmartsepa',
                    'error'             => $errorMsg,
                );
                $url = RsmartsepaHelper::createUrl(FILENAME_CHECKOUT_PAYMENT, $parameters, 'SSL');
                xtc_redirect($url);
                return false;                                
            }
            else {
                $errorMsg = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTFAILED', "Bezahlung mit rSm@rt ist fehlgeschlagen!");
                $parameters = array(
                    'payment_error'     => 'rsmartsepa',
                    'error'             => $errorMsg,
                );
                $url = RsmartsepaHelper::createUrl(FILENAME_CHECKOUT_PAYMENT, $parameters, 'SSL');
                xtc_redirect($url);
                return false;                
            }
        }
        else {
            // DEBUG
            RsmartsepaHelper::debug('log', 'callback:rsmartsepa_callback_redirect', '$Raautil_DataStore NOT found');
            
            $errorMsg = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTFAILED', "Bezahlung mit rSm@rt ist fehlgeschlagen!");
            $parameters = array(
                'payment_error'     => 'rsmartsepa',
                'error'             => $errorMsg,
            );
            $url = RsmartsepaHelper::createUrl(FILENAME_CHECKOUT_PAYMENT, $parameters, 'SSL');
            xtc_redirect($url);
            return false;
        }
    } catch(Exception $ex) {
        // DEBUG
        RsmartsepaHelper::debug('log', 'callback:rsmartsepa_callback_redirect', 'Exception=' . $ex->getMessage());
        $errorMsg = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYMENTFAILED', "Bezahlung mit rSm@rt ist fehlgeschlagen!") .
                    ' ' .
                    RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_REASON', "Grund") . 
                    ': ' . $ex->getMessage();
        $parameters = array(
            'payment_error'     => 'rsmartsepa',
            'error'             => $errorMsg,
        );
        $url = RsmartsepaHelper::createUrl(FILENAME_CHECKOUT_PAYMENT, $parameters, 'SSL');
        xtc_redirect($url);
        return false;
    }
} // End rsmartsepa_callback_redirect

function rsmartsepa_callback_send_email($order_id = 0) {
    require_once (DIR_FS_INC . 'xtc_get_order_data.inc.php');
    require_once (DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
    require_once (DIR_FS_INC . 'xtc_create_password.inc.php');
    require_once (DIR_FS_INC . 'xtc_encrypt_password.inc.php');
    require_once (DIR_FS_INC . 'html_entity_decode_wrapper.inc.php');

    // bof gm
    require_once (DIR_FS_CATALOG . 'gm/inc/gm_save_order.inc.php');
    // eof gm
    
    $order_id = (int)$order_id;
    
		// GENERATE ORDER
		$order = new order($order_id);

		// GET WITHDRAWAL
		$coo_shop_content_control = MainFactory::create_object('ShopContentContentControl');
		$t_mail_attachment_array = array();

		if (gm_get_conf('ATTACH_CONDITIONS_OF_USE_IN_ORDER_CONFIRMATION') == 1)
		{
			$coo_shop_content_control->set_content_group('3');
			$t_mail_attachment_array[] = $coo_shop_content_control->get_file();
		}
    
		if(gm_get_conf('ATTACH_WITHDRAWAL_INFO_IN_ORDER_CONFIRMATION') == '1')
		{
			$coo_shop_content_control->set_content_group(gm_get_conf('GM_WITHDRAWAL_CONTENT_ID'));
			$t_mail_attachment_array[] = $coo_shop_content_control->get_file();
		}

		if(gm_get_conf('ATTACH_WITHDRAWAL_FORM_IN_ORDER_CONFIRMATION') == '1')
		{
			$coo_shop_content_control->set_content_group(gm_get_conf('GM_WITHDRAWAL_CONTENT_ID'));
			$coo_shop_content_control->set_withdrawal_form('1');
			$t_mail_attachment_array[] = $coo_shop_content_control->get_file();
		}

		$t_shop_content_query = xtc_db_query("SELECT
											content_title,
											content_heading,
											content_text,
											content_file
											FROM " . TABLE_CONTENT_MANAGER . "
											WHERE content_group='" . (int)gm_get_conf('GM_WITHDRAWAL_CONTENT_ID') . "' " . $group_check . "
											AND languages_id='" . $_SESSION['languages_id'] . "'");
		$t_shop_content_data = xtc_db_fetch_array($t_shop_content_query);
		$t_withdrawal = html_entity_decode_wrapper(trim(strip_tags($t_shop_content_data['content_text'])));

		// GET AGB
		$t_shop_content_query = xtc_db_query("SELECT
											content_title,
											content_heading,
											content_text,
											content_file
											FROM " . TABLE_CONTENT_MANAGER . "
											WHERE content_group='3' " . $group_check . "
											AND languages_id='" . $_SESSION['languages_id'] . "'");
		$t_shop_content_data = xtc_db_fetch_array($t_shop_content_query);
		$t_agb = html_entity_decode_wrapper(trim(strip_tags($t_shop_content_data['content_text'])));

		/* BOF TRUSTED SHOPS RATING */
		$t_ts_rating_link = '';
		if((bool)gm_get_conf('GM_TS_RATING_ENABLED') === true && (bool)gm_get_conf('GM_TS_RATING_EMAIL') === true)
		{
			$t_service = new GMTSService();
			$t_ts_rating_link = $t_service->getRatingLink($order_id, $order->customer['email_address']);
		}

		// PAYMENT MODUL TEXTS
		$t_payment_info_html = '';
		$t_payment_info_text = '';

		// GET E-MAIL LOGO
		$t_mail_logo = '';
		$t_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
		if($t_logo_mail->logo_use == '1')
		{
			$t_mail_logo = $t_logo_mail->get_logo();
		}

		# JANOLAW START
		require_once(DIR_FS_CATALOG . 'gm/classes/GMJanolaw.php');
		$coo_janolaw = new GMJanolaw();
		$t_janolaw_info_html = '';
		$t_janolaw_info_text = '';
		if($coo_janolaw->get_status() == true)
		{
            $t_janolaw_info_html  = $coo_janolaw->get_page_content('revocation', true, true);
			$t_janolaw_info_html .= '<br/><br/>AGB<br/><br/>';
            $t_janolaw_info_html .= $coo_janolaw->get_page_content('terms', true, true);

            $t_janolaw_info_text  = $coo_janolaw->get_page_content('revocation', false, false);
			$t_janolaw_info_text .= "\n\nAGB\n\n";
            $t_janolaw_info_text .= $coo_janolaw->get_page_content('terms', false, false);
		}
		# JANOLAW END

		// CREATE CONTENTVIEW
		$coo_send_order_content_view = MainFactory::create_object('SendOrderContentView');

		// ASSIGN VARIABLES
		$coo_send_order_content_view->set_('order', $order);
		$coo_send_order_content_view->set_('order_id', $order_id);
		$coo_send_order_content_view->set_('credit_covers', $_SESSION['credit_covers']);
		$coo_send_order_content_view->set_('language', $_SESSION['language']);
		$coo_send_order_content_view->set_('language_id', $_SESSION['languages_id']);
		$coo_send_order_content_view->set_('language_code', $_SESSION['language_code']);
		$coo_send_order_content_view->set_('withdrawal', $t_withdrawal);
		$coo_send_order_content_view->set_('agb', $t_agb);
		//$coo_send_order_content_view->set_('ts_rating_link', $t_ts_rating_link); //
		$coo_send_order_content_view->set_('payment_info_html', $t_payment_info_html);
		$coo_send_order_content_view->set_('payment_info_text', $t_payment_info_text);
		$coo_send_order_content_view->set_('mail_logo', $t_mail_logo);
		$coo_send_order_content_view->set_('janolaw_info_html', $t_janolaw_info_html);
		$coo_send_order_content_view->set_('janolaw_info_text', $t_janolaw_info_text);

		// GET MAIL CONTENTS ARRAY
		$t_mail_content_array = $coo_send_order_content_view->get_mail_content_array();

		// GET HTML MAIL CONTENT
		$t_content_mail = $t_mail_content_array['html'];

		// GET TXT MAIL CONTENT
		$t_txt_mail = $t_mail_content_array['txt'];

		// CREATE SUBJECT
		$t_subject = gm_get_content('EMAIL_BILLING_SUBJECT_ORDER', $_SESSION['languages_id']);
		if (empty($t_subject))
		{
			$t_subject = EMAIL_BILLING_SUBJECT_ORDER;
		}
		$order_subject = str_replace('{$nr}', $order_id, $t_subject);
		$order_subject = str_replace('{$date}', utf8_encode_wrapper(strftime(DATE_FORMAT_LONG)), $order_subject);
		$order_subject = str_replace('{$lastname}', $order->customer['lastname'], $order_subject);
		$order_subject = str_replace('{$firstname}', $order->customer['firstname'], $order_subject);

		// send mail to admin
		// BOF GM_MOD:
		if(SEND_EMAILS == 'true')
		{
			// get the sender mail adress. e.g. Host Europe has problems with the customer mail adress.
			$from_email_address = $order->customer['email_address'];
			if(SEND_EMAIL_BY_BILLING_ADRESS == 'SHOP_OWNER') {
				$from_email_address = EMAIL_BILLING_ADDRESS;
			}
			xtc_php_mail($from_email_address,
						$order->customer['firstname'].' '.$order->customer['lastname'],
						EMAIL_BILLING_ADDRESS,
						STORE_NAME,
						EMAIL_BILLING_FORWARDING_STRING,
						$order->customer['email_address'],
						$order->customer['firstname'].' '.$order->customer['lastname'],
						$t_mail_attachment_array,
						'',
						$order_subject,
						$t_content_mail,
						$t_txt_mail
		   );
		}

		// send mail to customer
		// BOF GM_MOD:
		if (SEND_EMAILS == 'true')
		{
			$gm_mail_status = xtc_php_mail(EMAIL_BILLING_ADDRESS,
											EMAIL_BILLING_NAME,
											$order->customer['email_address'],
											$order->customer['firstname'].' '.$order->customer['lastname'],
											'',
											EMAIL_BILLING_REPLY_ADDRESS,
											EMAIL_BILLING_REPLY_ADDRESS_NAME,
											$t_mail_attachment_array,
											'',
											$order_subject,
											$t_content_mail,
											$t_txt_mail
			);
		}

		if($gm_mail_status == false) {
			$gm_send_order_status = 0;
		} else {
			$gm_send_order_status = 1;
		}

		gm_save_order($order_id, $t_content_mail, $t_txt_mail, $gm_send_order_status);
		// eof gm
                
		if (AFTERBUY_ACTIVATED == 'true') {
			require_once (DIR_WS_CLASSES.'afterbuy.php');
			$aBUY = new xtc_afterbuy_functions($order_id);
			if ($aBUY->order_send())
			{
				$aBUY->process_order();
			}
		}

		return true;                
} // End rsmartsepa_callback_send_email


// ###########################################################################
// Change the directory to the root directory
chdir('../../');

// Include the includes/application_top.php
require_once('includes/application_top.php');

// Include the callback/rsmartsepa/RsmartsepaHelper.php
require_once(DIR_FS_CATALOG . 'callback/rsmartsepa/RsmartsepaHelper.php');

$language = RsmartsepaHelper::getLanguageName(( isset($_SESSION['language']) ? $_SESSION['language'] : NULL ));
$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
$coo_lang_file_master->init_from_lang_file('lang/' . $language . '/modules/payment/rsmartsepa.php');

// DEBUG
RsmartsepaHelper::debug('printDefinedConstants', 'user', 'callback.php');

// ############################################################################
// Check the action
// ############################################################################
$rsmartsepa_action = trim(RsmartsepaHelper::getRequestValue('rsmartsepaaction', ''));
if($rsmartsepa_action == 'rsmartsepagetres') {
    // Action for delivering a resource
    rsmartsepa_callback_deliver_resource();
} // end action: 'rsmartsepagetres'
else if($rsmartsepa_action == 'rsmartsepadisplay') {
    $transactionArray = RsmartsepaHelper::getSessionValue('rsmartsepa', array());
    if(isset($transactionArray) && is_array($transactionArray) && count($transactionArray) > 0) {
        $templateName = isset($transactionArray['rsmartsepatrTemplateName']) ? trim($transactionArray['rsmartsepatrTemplateName']) : '';
        if($templateName != '') {
            
            $configParams = array(
                'logging'               => $transactionArray['rsmartsepatrDebugMode'],
                'timeout'               => 5000,
                'urlAjax'               => $transactionArray['rsmartsepatrUrlAjax'],
                'urlRedirect'           => $transactionArray['rsmartsepatrUrlRedirect'],
                'tid'                   => $transactionArray['rsmartsepatrTID'],
                'hash'                  => $transactionArray['rsmartsepatrHash'],
                'changebuttonstyle'     => FALSE,
            );
            $rsmartsepaINLINECONFIGCODE = RsmartsepaHelper::createInlineConfigCode('spsrsmart.rsmartcore.AppConfig', $configParams);
            
            $spsrsmartCSS_DISPLAY_INLINE_CODE = RsmartsepaHelper::readResourceFile('css/rsmartsepapayment.css');
            $spsrsmartCSS_DISPLAY_INLINE_CODE = str_replace('@mob-app-banner2@', RsmartsepaHelper::createResourceUrl('resources/css/mob-app-banner2.gif'), $spsrsmartCSS_DISPLAY_INLINE_CODE);
            $spsrsmartCSS_DISPLAY_INLINE_CODE = str_replace('@header_color_3@', RsmartsepaHelper::createResourceUrl('resources/css/header_color_3.jpg'), $spsrsmartCSS_DISPLAY_INLINE_CODE);
            $spsrsmartCSS_DISPLAY_INLINE_CODE = str_replace('@mob-app-banner@', RsmartsepaHelper::createResourceUrl('resources/css/mob-app-banner.png'), $spsrsmartCSS_DISPLAY_INLINE_CODE);
            $spsrsmartCSS_LOGGING_WINDOW_INLINE_CODE = RsmartsepaHelper::readResourceFile('css/loggingwindow.css');
            $spsrsmartJS_MODERNIZR_INLINE_CODE = RsmartsepaHelper::readResourceFile('js/modernizr.custom.js');
			$jquery_path = get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/jquery.min.js');
			$spsrsmartJS_JQUERY_INLINE_CODE = is_file($jquery_path) ? file_get_contents($jquery_path) : '';
            
            $spsrsmartJS_CORE_INLINE_CODE = RsmartsepaHelper::readResourceFile('js/spsrsmart_core_1_0_0.js');
            $spsrsmartJS_VIEW_INLINE_CODE = RsmartsepaHelper::readResourceFile('js/spsrsmart_view_1_0_0.js');
            $spsrsmartJS_APP_INLINE_CODE  = RsmartsepaHelper::readResourceFile('js/spsrsmart_app_1_0_0.js');
            $spsrsmartPNG_RSMART_URL = RsmartsepaHelper::createResourceUrl('resources/images/rsmart.png');
            
            $templateVars = array(
                'spsrsmartAmount'                           => $transactionArray['rsmartsepatrAmount'],
                'spsrsmartCurrency'                         => $transactionArray['rsmartsepatrCurrency'],
                'spsrsmartTransactionId'                    => $transactionArray['rsmartsepatrTransactionId'],
                'spsrsmartTransactionDesc'                  => '',
                'spsrsmartSimulationMode'                   => $transactionArray['rsmartsepatrSimulationMode'],
                'spsrsmartDebugMode'                        => $transactionArray['rsmartsepatrDebugMode'],
                'spsrsmartShopName'                         => $transactionArray['rsmartsepatrShopName'],
                'spsrsmartShopUrl'                          => $transactionArray['rsmartsepatrShopUrl'],
                'spsrsmartTID'                              => $transactionArray['rsmartsepatrTID'],
                'spsrsmartHash'                             => $transactionArray['rsmartsepatrHash'],
                'spsrsmartQrCodeB64'                        => '',
                'spsrsmartQrCodeUrl'                        => $transactionArray['rsmartsepatrQrCodeUrl'],
                'spsrsmartRaaUrl'                           => $transactionArray['rsmartsepatrRaaUrl'],
                'spsrsmartTRANSLATOR'                       => new RsmartsepaHelper(),
                'spsrsmartCSS_DISPLAY_URL'                  => '',
                'spsrsmartCSS_DISPLAY_INLINE_CODE'          => $spsrsmartCSS_DISPLAY_INLINE_CODE,
                'spsrsmartCSS_LOGGING_WINDOW_URL'           => '',
                'spsrsmartCSS_LOGGING_WINDOW_INLINE_CODE'   => $spsrsmartCSS_LOGGING_WINDOW_INLINE_CODE,
                'spsrsmartJS_MODERNIZR_URL'                 => '',
                'spsrsmartJS_MODERNIZR_INLINE_CODE'         => $spsrsmartJS_MODERNIZR_INLINE_CODE,
                'spsrsmartJS_JQUERY_URL'                    => '',
                'spsrsmartJS_JQUERY_INLINE_CODE'            => $spsrsmartJS_JQUERY_INLINE_CODE,
                'spsrsmartJS_CORE_URL'                      => '',
                'spsrsmartJS_CORE_INLINE_CODE'              => $spsrsmartJS_CORE_INLINE_CODE,
                'spsrsmartJS_VIEW_URL'                      => '',
                'spsrsmartJS_VIEW_INLINE_CODE'              => $spsrsmartJS_VIEW_INLINE_CODE,
                'spsrsmartJS_APP_URL' => '',
                'spsrsmartJS_APP_INLINE_CODE'               => $spsrsmartJS_APP_INLINE_CODE,
                'spsrsmartJS_APPCONFIG_INLINE_CODE'         => $rsmartsepaINLINECONFIGCODE,
                'spsrsmartPNG_RSMART_URL'                   => $spsrsmartPNG_RSMART_URL,
            );
            
            $html = RsmartsepaHelper::renderTemplate('rsmartsepapayment', $templateVars);
            RsmartsepaHelper::htmlOutput($html);
        }
    }
    
    $url = RsmartsepaHelper::createUrl('', array(), 'SSL');
    xtc_redirect($url);
} // end action: 'rsmartsepadisplay'
else if($rsmartsepa_action == 'rsmartsepaajax') {
    rsmartsepa_callback_ajax();
} // end action: 'rsmartsepaajax'
else if($rsmartsepa_action == 'rsmartsepacron') {
    rsmartsepa_callback_cron();
} // end action: 
else if($rsmartsepa_action == 'rsmartseparedirect') {
    rsmartsepa_callback_redirect();
} // end action: 'rsmartseparedirect'

// End of all callbacks
if (ob_get_level()) {
    ob_end_clean();
}
$html = '<html>
           <head>
              <title>Test</title>
           </head>
           <body>
              <h1>Test</h1>
              <h3>Output</h3>
              <pre>Test</pre>
           </body>
         </html>';
print($html);
if(function_exists('xtc_db_close')) {
    xtc_db_close();
}
exit;
