<?php
/* --------------------------------------------------------------
  TransactionHandler.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class Raautil_TransactionHandler {
    
    /**
     * The implementation of Raautil_ITerminalDataProvider
     * @var Raautil_ITerminalDataProvider 
     */
    protected $TerminalDataProvider = null;
    
    /**
     * The implementation of Raautil_IDataStoreProvider
     * @var Raautil_IDataStoreProvider 
     */
    protected $DataStoreProvider = null;
    
    /**
     * The Raautil_TerminalData
     * @var Raautil_TerminalData 
     */
    protected $Raautil_TerminalData = null;
    
    protected $qrCodeTmpFolder = '';
    
    protected $simulation = FALSE;
    
    protected $simulationACCOUNTID = '';
    protected $simulationFIRSTNAME = '';
    protected $simulationLASTNAME = '';
    protected $simulationADDRESS1 = '';
    protected $simulationADDRESS2 = '';
    protected $simulationADDRESS3 = '';
    protected $simulationZIPCODE = '';
    protected $simulationCITY = '';
    protected $simulationCOUNTRY = '';
    protected $simulationMSISDN = '';
    protected $simulationMALE = -1;
    protected $simulationDATEOFBIRTH = '';
    protected $simulationBANKACCOUNTNUMBER = '';
    protected $simulationBANKCODE = '';
    
    
    /**
     * Creates a new instance of Raautil_TransactionHandler.
     * 
     * @param Raautil_ITerminalDataProvider $TerminalDataProvider
     *    An instance implementing Raautil_ITerminalDataProvider
     * 
     * @param Raautil_IDataStoreProvider $DataStoreProvider 
     *    An instance implementing Raautil_IDataStoreProvider
     * 
     * @param string $qrCodeTmpFolder
     *    The absolute path for an existing folder where
     *    created qrcodes are stored temporary
     * 
     * @throws Raautil_TransactionException
     *    On error
     */
    public function __construct($TerminalDataProvider = null, $DataStoreProvider = null, $qrCodeTmpFolder = '') {
        if(!isset($TerminalDataProvider)) {
            throw new Raautil_TransactionException("TerminalDataProvider is null");
        }
        else if(!is_object($TerminalDataProvider)) {
            throw new Raautil_TransactionException("TerminalDataProvider is no object");
        }
        else if(!($TerminalDataProvider instanceof Raautil_ITerminalDataProvider)) {
            throw new Raautil_TransactionException("TerminalDataProvider is no instance of Raautil_ITerminalDataProvider");
        }
        
        if(!isset($DataStoreProvider)) {
            throw new Raautil_TransactionException("DataStoreProvider is null");
        }
        else if(!is_object($DataStoreProvider)) {
            throw new Raautil_TransactionException("DataStoreProvider is no object");
        }
        else if(!($DataStoreProvider instanceof Raautil_IDataStoreProvider)) {
            throw new Raautil_TransactionException("DataStoreProvider is no instance of Raautil_IDataStoreProvider");
        }
        
        $this->qrCodeTmpFolder = isset($qrCodeTmpFolder) ? (is_string($qrCodeTmpFolder) ? trim($qrCodeTmpFolder) : '') : '';
        if($this->qrCodeTmpFolder == '') {
            throw new Raautil_TransactionException("qrCodeTmpFolder is empty");
        }
        else if(!is_dir($this->qrCodeTmpFolder)) {
            throw new Raautil_TransactionException("qrCodeTmpFolder is not an existing folder");
        }
        else if(!is_writable($this->qrCodeTmpFolder)) {
            throw new Raautil_TransactionException("qrCodeTmpFolder is not writable");
        }
        
        if(!defined('RAA_TESTING_AVOID_CERT_ERROR')) {
            define('RAA_TESTING_AVOID_CERT_ERROR', 'true');
        }
        
        
        $this->TerminalDataProvider = $TerminalDataProvider;
        $this->DataStoreProvider = $DataStoreProvider;
        $this->Raautil_TerminalData = $this->TerminalDataProvider->getTerminalData();
        
        // This method can overwrite $this->simulation
        $this->checkTerminalDataSimulationMode();
    } // End constructor
    
    /**
     * This method can overwrite the simulation flag defined in this class.
     */
    protected function checkTerminalDataSimulationMode() {
        $this->simulation = $this->Raautil_TerminalData->isSimulation();
        $this->simulationACCOUNTID = $this->Raautil_TerminalData->getSimulationACCOUNTID();
        $this->simulationFIRSTNAME = $this->Raautil_TerminalData->getSimulationFIRSTNAME();
        $this->simulationLASTNAME = $this->Raautil_TerminalData->getSimulationLASTNAME();
        $this->simulationADDRESS1 = $this->Raautil_TerminalData->getSimulationADDRESS1();
        $this->simulationADDRESS2 = $this->Raautil_TerminalData->getSimulationADDRESS2();
        $this->simulationADDRESS3 = $this->Raautil_TerminalData->getSimulationADDRESS3();
        $this->simulationZIPCODE = $this->Raautil_TerminalData->getSimulationZIPCODE();
        $this->simulationCITY = $this->Raautil_TerminalData->getSimulationCITY();
        $this->simulationCOUNTRY = $this->Raautil_TerminalData->getSimulationCOUNTRY();
        $this->simulationMSISDN = $this->Raautil_TerminalData->getSimulationMSISDN();
        $this->simulationMALE = $this->Raautil_TerminalData->getSimulationMALE();
        $this->simulationDATEOFBIRTH = $this->Raautil_TerminalData->getSimulationDATEOFBIRTH();
        $this->simulationBANKACCOUNTNUMBER = $this->Raautil_TerminalData->getSimulationBANKACCOUNTNUMBER();
        $this->simulationBANKCODE = $this->Raautil_TerminalData->getSimulationBANKCODE();
    } // End checkTerminalDataSimulationMode
    
    /**
     * Validates the DataStoreProvider.
     * 
     * @throws Raautil_TransactionException 
     *    On error
     */
    public function validate() {
        $this->DataStoreProvider->validateDatastore();
    } // End validate
    
    /**
     * Creates a new terminal client with protocol version 1.
     * 
     * @return Raa_TerminalClientV1Default 
     *    An instance of Raa_TerminalClientV1Default
     * 
     * @throws Raautil_TransactionException
     *    If terminal data are invalid or something went wrong with terminal creation
     */
    protected function createTerminal() {
        $this->validate();
        
        $termProps = $this->Raautil_TerminalData->getTerminalProperties();
        $connProps = $this->Raautil_TerminalData->getConnectionProperties();
        
        // Only Protocol 1 is supported
        $Raa_TerminalClientV1Default = 
             new Raa_TerminalClientV1Default(
                $connProps,
                $termProps['key'],
                new Raa_TerminalInfo(
                        $termProps['providerId'],
                        $termProps['countryId'],
                        $termProps['sellerId'],
                        $termProps['salesPointId'],
                        $termProps['applicationId'],
                        $this->getArrayElement('description', $termProps),
                        $this->getArrayElement('sellerName', $termProps)
                )
        );
        
        return $Raa_TerminalClientV1Default;
    } // End createTerminal
    
    protected function getArrayElement($elem, array $array) {
        if ($elem == null || $array == null || !array_key_exists($elem, $array)) {
                return null;
        }
        return $array[$elem];
    } // End getArrayElement
    
    /**
     * Creates an amount transaction.
     * 
     * @param float $amount
     *    The amount (required)
     * 
     * @param string $currency
     *    The currency code like 'EUR' (required)
     * 
     * @param string $traid
     *    The magento transaction id (required)
     * 
     * @param string $tradesc
     *    The transaction description (required)
     * 
     * @param Raa_SellerAccountInfo $SellerAccountInfo 
     *    An optional instance of Raa_SellerAccountInfo
     * 
     * @return Raautil_DataStore
     *    An instance of an already saved DataStore
     * 
     * @throws Exception|Raautil_TransactionException
     *    On error
     */
    public function createAmountTransaction($amount = 0.00, 
                                               $currency = 'EUR', 
                                               $traid = '', 
                                               $tradesc = '',
                                               $SellerAccountInfo = NULL) {
        $Raa_SellerAccountInfo = NULL;
        if(isset($SellerAccountInfo)) {
            if($SellerAccountInfo instanceof Raa_SellerAccountInfo) {
                $Raa_SellerAccountInfo = $SellerAccountInfo;
            }
        }
        
        $terminalClient = $this->createTerminal();
        if(!isset($terminalClient)) {
            throw new Raautil_TransactionException("Error creating terminalClient");
        }
        
        $txData = $terminalClient->createTransactionDataAmount($amount, $currency, $traid, $tradesc);
        
        // Only Protocol 1 is supported
        // createTransaction(Raa_TransactionData $transactionData, 
        //                   Raa_TerminalInfo $terminalInfo = null,
        //                   $key = null, 
        //                   $timestamp = null,
        //                   Raa_SellerAccountInfo $sellerAccountInfo = null)
        if($this->simulation == TRUE) {
            
            if (defined("RAA_DEBUG")) {
                $logTitle = 'Raautil_TransactionHandler->createAmountTransaction';
                $logData = '';
                if(isset($txData)) {
                    $logData = $txData->__toString();
                }
	        TerminalSdkLibrary::log('debug', $logTitle, $logData);
            }
            
            $tid = $this->generateTID();
            $pid = 1;
            $srvid = 123;
            $Raa_TransactionResult = new Raa_TransactionResult($tid, new Raa_ServerInfo($pid, $srvid));
        }
        else {
            // 2015.09.14: Check for serverTimeOffset Exception
            // If the local clock is behind the server clock, serverTimeOffset in the Exception is positive
            // If the local clock is ahead the server clock, serverTimeOffset in the Exception negative
            $clientOffset = 0;
            while(TRUE) {
                $timestamp = time();
                try {
                    $timestamp = $timestamp + $clientOffset;
                    $Raa_TransactionResult = $terminalClient->createTransaction($txData, null, null, $timestamp, $Raa_SellerAccountInfo);
                    break;
                } catch (Exception $ex) {
                    if($ex instanceof Raa_ClientTimestampException) {
                        $Raa_ClientTimestampException = $ex;
                        $clientOffset = $clientOffset + $Raa_ClientTimestampException->getServerTimeOffset();
                        continue;
                    }
                    else {
                        throw $ex;
                    }
                }
            }
        }
        
        $tid = $Raa_TransactionResult->tid;
        $serverInfo = $Raa_TransactionResult->serverInfo;
        $pid = $Raa_TransactionResult->serverInfo->pid;
        $srvId = $Raa_TransactionResult->serverInfo->srvId;
        
        // Create QRCode: This may throw an Raautil_TransactionException
        $imageArray = $this->createQRCodeImage($terminalClient, $tid, $serverInfo, $txData);
        $Raautil_DataStore = new Raautil_DataStore();
        $Raautil_DataStore->setQRCode($imageArray['qrcode']);
        $Raautil_DataStore->setQRCodeB64($imageArray['qrcodeb64']);
        $Raautil_DataStore->setCustomGeneratedQRCodeUrl(isset($imageArray['customurl']) ? $imageArray['customurl'] : '');
        $Raautil_DataStore->setTransactionDataAmount($txData);
        $Raautil_DataStore->setTransactionResult($Raa_TransactionResult);
        $Raautil_DataStore->setLastAction('CREATE');
        $Raautil_DataStore->setLastStatus('MATCH');
        $Raautil_DataStore->setSimulationMode($this->simulation);
        $calculatedHash = $Raautil_DataStore->calculateHashCode($this->getSellerInfo());
        $Raautil_DataStore->setHashCode($calculatedHash);
        
        // This may throw an Raautil_TransactionException
        $this->DataStoreProvider->insertDatastore($tid, $Raautil_DataStore);
        return $Raautil_DataStore;
    } // End createAmountTransaction
    
    
    /**
     * Creates an empty transaction.
     * 
     * @return Raautil_DataStore
     *    An instance of an already saved DataStore
     * 
     * @throws Exception|Raautil_TransactionException
     *    On error
     */    
    public function createEmptyTransaction() {
        $terminalClient = $this->createTerminal();
        if(!isset($terminalClient)) {
            throw new Raautil_TransactionException("Error creating terminalClient");
        }
        
        $txData = $terminalClient->createTransactionDataEmpty();
        
        if($this->simulation == TRUE) {
            
            if (defined("RAA_DEBUG")) {
                $logTitle = 'Raautil_TransactionHandler->createEmptyTransaction';
                $logData = '';
                if(isset($txData)) {
                    $logData = $txData->__toString();
                }
	        TerminalSdkLibrary::log('debug', $logTitle, $logData);
            }
            
            $tid = $this->generateTID();
            $pid = 1;
            $srvid = 123;
            $Raa_TransactionResult = new Raa_TransactionResult($tid, new Raa_ServerInfo($pid, $srvid));
        }
        else {
            // 2015.09.14: Check for serverTimeOffset Exception
            $clientOffset = 0;
            while(TRUE) {
                $timestamp = time();
                try {
                    $timestamp = $timestamp + $clientOffset;
                    $Raa_TransactionResult = $terminalClient->createTransaction($txData, null, null, $timestamp);
                    break;
                } catch (Exception $ex) {
                    if($ex instanceof Raa_ClientTimestampException) {
                        $Raa_ClientTimestampException = $ex;
                        $clientOffset = $clientOffset + $Raa_ClientTimestampException->getServerTimeOffset();
                        continue;
                    }
                    else {
                        throw $ex;
                    }
                }
            }
        }
        
        $tid = $Raa_TransactionResult->tid;
        $serverInfo = $Raa_TransactionResult->serverInfo;
        $pid = $Raa_TransactionResult->serverInfo->pid;
        $srvId = $Raa_TransactionResult->serverInfo->srvId;
        
        // Create QRCode: This may throw an Raautil_TransactionException
        $imageArray = $this->createQRCodeImage($terminalClient, $tid, $serverInfo, $txData);
        $Raautil_DataStore = new Raautil_DataStore();
        $Raautil_DataStore->setQRCode($imageArray['qrcode']);
        $Raautil_DataStore->setQRCodeB64($imageArray['qrcodeb64']);
        $Raautil_DataStore->setCustomGeneratedQRCodeUrl(isset($imageArray['customurl']) ? $imageArray['customurl'] : '');
        $Raautil_DataStore->setTransactionDataEmpty($txData);
        $Raautil_DataStore->setTransactionResult($Raa_TransactionResult);
        $Raautil_DataStore->setLastAction('CREATE');
        $Raautil_DataStore->setLastStatus('MATCH');
        $Raautil_DataStore->setSimulationMode($this->simulation);
        $calculatedHash = $Raautil_DataStore->calculateHashCode($this->getSellerInfo());
        $Raautil_DataStore->setHashCode($calculatedHash);
        
        // This may throw an Raautil_TransactionException
        $this->DataStoreProvider->insertDatastore($tid, $Raautil_DataStore);
        return $Raautil_DataStore;
    } // End createEmptyTransaction
    
    
    /**
     * Creates the QRCode image.
     * 
     * @param Raa_TerminalClientV1Default $terminalClient
     *    The terminal client
     * 
     * @param string $tid
     *    The TID
     * 
     * @parem Raa_ServerInfo $serverInfo
     *    The ServerInfo
     * 
     * @param Raa_TransactionData $txData
     *    The transaction data
     * 
     * @return array
     *    A structured array in the format
     * 
     *    array(
     *      'tid'               => (string) The TID,
     *      'fileextension'     => (string) The fileextension 'png',
     *      'qrcode'            => (string) The QRCode image data,
     *      'qrcodeb64'         => (string) The QRCode image data as base64 string,
     *    )
     * 
     * @throws Raautil_TransactionException
     *    On error
     */
    protected function createQRCodeImage($terminalClient = NULL, $tid = '', $serverInfo = NULL, $txData = NULL) {
        $tid = isset($tid) ? trim($tid) : '';
        if($tid == '') {
            throw new Raautil_TransactionException("Empty TID");
        }
        
        $result = array(
            'tid'               => $tid,
            'fileextension'     => 'png',
            'qrcode'            => '',
            'qrcodeb64'         => '',
        );
        
        $foundSimulationQRCode = FALSE;
        if($this->simulation == TRUE) {
            if(defined('RAAUTIL_CREATE_CUSTOM_QRCODE') && defined('RAAUTIL_CUSTOM_QRCODE_URL')) {
                $customUrl = trim(RAAUTIL_CUSTOM_QRCODE_URL);
                if($customUrl != '') {
                    if(strpos($customUrl, '?') === FALSE) {
                        $customUrl = $customUrl . '?tid=' . $tid;
                    }
                    else {
                        $customUrl = $customUrl . '&tid=' . $tid;
                    }
                    $Raautil_QRCodeGenerator = new Raautil_QRCodeGenerator($tid, $customUrl, $this->qrCodeTmpFolder);
                    if($Raautil_QRCodeGenerator->isValid()) {
                        $Raautil_QRCodeGenerator->createPNG();
                        if($Raautil_QRCodeGenerator->isGenerated()) {
                            $result['qrcode'] = $Raautil_QRCodeGenerator->getQRCodeData();
                            $result['qrcodeb64'] = $Raautil_QRCodeGenerator->getBase64QRCodeData();
                            $result['customurl'] = $customUrl;
                            $foundSimulationQRCode = TRUE;
                        }
                    }
                }
            }
            else if(defined('RAAUTIL_CREATE_CUSTOM_QRCODE') && defined('RAAUTIL_CUSTOM_QRCODE_DATA')) {
                $customData = Raautil_QRCodeGenerator::createDataFromTransactionData($tid, $serverInfo, $txData);
                if($customData != '') {
                    $Raautil_QRCodeGenerator = new Raautil_QRCodeGenerator($tid, $customData, $this->qrCodeTmpFolder);
                    if($Raautil_QRCodeGenerator->isValid()) {
                        $Raautil_QRCodeGenerator->createPNG();
                        if($Raautil_QRCodeGenerator->isGenerated()) {
                            $result['qrcode'] = $Raautil_QRCodeGenerator->getQRCodeData();
                            $result['qrcodeb64'] = $Raautil_QRCodeGenerator->getBase64QRCodeData();
                            $foundSimulationQRCode = TRUE;
                        }
                    }
                }                
            }
            
            if($foundSimulationQRCode == FALSE) {
                $fname = dirname(__FILE__) . '/qrcodesimulation.png';
                if(is_file($fname)) {
                    $cont = @file_get_contents($fname);
                    if(isset($cont)) {
                        if($cont !== FALSE) {
                            $foundSimulationQRCode = TRUE;
                            $result['qrcode'] = $cont;
                            $b64 = @base64_encode($cont);
                            $result['qrcodeb64'] = $b64;
                        }
                    }
                }
            }
            
            
            if(defined('RAAUTIL_CREATE_CUSTOM_QRCODE') && !defined('RAAUTIL_CUSTOM_QRCODE_URL') && !defined('RAAUTIL_CUSTOM_QRCODE_DATA')) {
                $foundSimulationQRCode = FALSE;
            }
            
        } // end: if($this->simulation == TRUE)
        
        
        
        
        if($foundSimulationQRCode == FALSE) {
            if(defined('RAAUTIL_CREATE_QRCODE_OUTPUTBUFFERING')) {
                // Start Output buffering
                ob_start();
                $terminalClient->createQrCodeImage($tid, $serverInfo, $txData, null, null);
                $content = ob_get_contents();
                ob_end_clean();
                if(isset($content)) {
                    if($content !== FALSE) {
                        $result['qrcode'] = $content;
                        $b64 = @base64_encode($content);
                        $result['qrcodeb64'] = $b64;
                    }
                }
            }
            else {
                $pngFile = $this->qrCodeTmpFolder . '/' . $tid . '.png';
                $png = $terminalClient->createQrCodeImage($tid, $serverInfo, $txData, null, $pngFile);
                if(is_file($pngFile)) {
                    $content = @file_get_contents($pngFile);
                    if(isset($content)) {
                        if($content !== FALSE) {
                            $result['qrcode'] = $content;
                            $b64 = @base64_encode($content);
                            $result['qrcodeb64'] = $b64;
                            if(!defined('RAAUTIL_KEEP_DATASTORE_IMAGE')) {
                                @unlink($pngFile);
                            }
                        }
                    }
                }
            } 
        } // end: if($foundSimulationQRCode == FALSE)
        
        if($result['qrcode'] == '' || $result['qrcodeb64'] == '') {
            throw new Raautil_TransactionException("Error creating QRCode");
        }
        
        return $result;
    } // End createQRCodeImage
    
    /**
     * Performs a match operation for a given TID on the matching server or simulates the match.
     * 
     * @param string $tid
     *    The TID
     * 
     * @param string $hash
     *    The hashcode
     * 
     * @param boolean $checkHash
     *    TRUE if the hashcode should be checked, FALSE otherwise
     *    (Default is FALSE)
     * 
     * @return array
     *    A structured array in the format
     * 
     *    array(
     *       'Raa_MatchResponse'         => (Object) The Raa_MatchResponse,
     *       'Raautil_DataStore'         => (Object) The updated Raautil_DataStore,
     *       'hash'                      => (String) The next hashcode,
     *    )
     * 
     * @throws Exception|Raautil_TransactionException
     *    On error
     */
    public function matchTransaction($tid = '', $hash = '', $checkHash = FALSE) {
        $tid = isset($tid) ? trim($tid) : '';
        if($tid == '') {
            throw new Raautil_TransactionException("Empty TID");
        }
        $hash = isset($hash) ? trim($hash) : '';
        $checkHash = isset($checkHash) ? ($checkHash == TRUE ? TRUE : FALSE) : FALSE;
        
        
        $Raautil_DataStore = $this->DataStoreProvider->readDatastore($tid);
        if(!isset($Raautil_DataStore)) {
            throw new Raautil_TransactionException("Transaction data not found for tid: " . $tid);
        }
        $terminalClient = $this->createTerminal();
        if(!isset($terminalClient)) {
            throw new Raautil_TransactionException("Error creating terminalClient");
        }
        
        if($checkHash == TRUE) {
            if($Raautil_DataStore->getHashCode() != $hash) {
                throw new Raautil_TransactionException("Invalid hash for tid: " . $tid, 8, null, TRUE);
            }
        }
        
        $reqId = time();
        if($this->simulation == TRUE) {
            
            if (defined("RAA_DEBUG")) {
                $logTitle = 'Raautil_TransactionHandler->matchTransaction';
                $logData = 'TID=' . $tid . ', hash=' . $hash . ', checkHash=' . ($checkHash == TRUE ? 'true' : 'false');
	        TerminalSdkLibrary::log('debug', $logTitle, $logData);
            }
            
            $status = 'PENDING';
            $simulatedStatus = trim($Raautil_DataStore->getSimulatedStatus());
            if($simulatedStatus != '') {
                $status = $simulatedStatus;
            }
            $Raa_MatchResponse = new Raa_MatchResponse($status, 2001, 1000); 
            $Raautil_DataStore->setLastAction('MATCH');
            $Raautil_DataStore->setLastStatus(isset($Raa_MatchResponse) ? $Raa_MatchResponse->result : 'FAILURE');
            $Raautil_DataStore->incrementMatchCounter();
            $calculatedHash = $Raautil_DataStore->calculateHashCode($this->getSellerInfo());
            $Raautil_DataStore->setHashCode($calculatedHash);
            $this->DataStoreProvider->updateDatastore($tid, $Raautil_DataStore);
            $resultArray = array(
                'Raa_MatchResponse'         => $Raa_MatchResponse,
                'Raautil_DataStore'         => $Raautil_DataStore,
                'hash'                      => $calculatedHash,
            );
            return $resultArray;
        }
        else {
            $Raa_ServerInfo = $Raautil_DataStore->getTransactionResultServerInfo();
            // Only Protocol 1 is supported
            //$Raa_MatchResponse = $terminalClient->match($tid, $Raa_ServerInfo, $this->protocol == 0 ? null : $reqId);
            $Raa_MatchResponse = $terminalClient->match($tid, $Raa_ServerInfo, $reqId);
            if(isset($Raa_MatchResponse)) {
                if(isset($Raa_MatchResponse->auth_confirmation_data)) {
                    if(defined('RAAUTIL_CHECK_SIGNATURE')) {
                        $this->checkSignature($terminalClient, $Raa_MatchResponse);
                    }
                }
            }
            $Raautil_DataStore->setLastAction('MATCH');
            $Raautil_DataStore->setLastStatus(isset($Raa_MatchResponse) ? $Raa_MatchResponse->result : 'FAILURE');
            $Raautil_DataStore->incrementMatchCounter();
            $calculatedHash = $Raautil_DataStore->calculateHashCode($this->getSellerInfo());
            $Raautil_DataStore->setHashCode($calculatedHash);
            $this->DataStoreProvider->updateDatastore($tid, $Raautil_DataStore);
            $resultArray = array(
                'Raa_MatchResponse'         => $Raa_MatchResponse,
                'Raautil_DataStore'         => $Raautil_DataStore,
                'hash'                      => $calculatedHash,
            );
            return $resultArray;
        }
    } // End matchTransaction
    
    /**
     * Performs a matchAndGet operation for a given TID on the matching server or simulates the match.
     * 
     * @param string $tid
     *    The TID
     * 
     * @param string $hash
     *    The hashcode
     * 
     * @param boolean $checkHash
     *    TRUE if the hashcode should be checked, FALSE otherwise
     *    (Default is FALSE)
     * 
     * @return array
     *    A structured array in the format
     * 
     *    array(
     *       'Raa_MatchResponse'         => (Object) The Raa_MatchResponse,
     *       'Raautil_DataStore'         => (Object) The updated Raautil_DataStore,
     *       'Raautil_AccountDisclosure' => (Object|String) Either a Raautil_AccountDisclosure on 'MATCH' or an empty string 
     *       'hash'                      => (String) The next hashcode,
     *    )
     * 
     * @throws Exception|Raautil_TransactionException
     *    On error
     */
    public function matchAndGetTransaction($tid = '', $hash = '', $checkHash = FALSE) {
        $tid = isset($tid) ? trim($tid) : '';
        if($tid == '') {
            throw new Raautil_TransactionException("Empty TID");
        }
        $hash = isset($hash) ? trim($hash) : '';
        $checkHash = isset($checkHash) ? ($checkHash == TRUE ? TRUE : FALSE) : FALSE;

        $Raautil_DataStore = $this->DataStoreProvider->readDatastore($tid);
        if(!isset($Raautil_DataStore)) {
            throw new Raautil_TransactionException("Transaction data not found for tid: " . $tid);
        }
        $terminalClient = $this->createTerminal();
        if(!isset($terminalClient)) {
            throw new Raautil_TransactionException("Error creating terminalClient");
        }
        
        if($checkHash == TRUE) {
            if($Raautil_DataStore->getHashCode() != $hash) {
                throw new Raautil_TransactionException("Invalid hash for tid: " . $tid, 8, null, TRUE);
            }
        }
        
        
        $Raautil_AccountDisclosure = ''; // Default is an empty string
        
        $reqId = time();
        if($this->simulation == TRUE) {
            
            if (defined("RAA_DEBUG")) {
                $logTitle = 'Raautil_TransactionHandler->matchAndGetTransaction';
                $logData = 'TID=' . $tid . ', hash=' . $hash . ', checkHash=' . ($checkHash == TRUE ? 'true' : 'false');
	        TerminalSdkLibrary::log('debug', $logTitle, $logData);
            }
            
            $status = 'PENDING';
            $Raautil_DataStore->incrementSmartIDSimulationCallCount();
            $simulatedStatus = trim($Raautil_DataStore->getSimulatedStatus());
            if($simulatedStatus != '') {
                $status = $simulatedStatus;
            }
            $Raa_MatchResponse = new Raa_MatchResponse($status, 2001, 1000); 
            $Raautil_DataStore->setLastAction('MATCH');
            $Raautil_DataStore->setLastStatus(isset($Raa_MatchResponse) ? $Raa_MatchResponse->result : 'FAILURE');
            $Raautil_DataStore->incrementMatchCounter();
            $calculatedHash = $Raautil_DataStore->calculateHashCode($this->getSellerInfo());
            $Raautil_DataStore->setHashCode($calculatedHash);
            $this->DataStoreProvider->updateDatastore($tid, $Raautil_DataStore);
            if($status == 'MATCH') {
                $Raautil_AccountDisclosure = new Raautil_AccountDisclosure();
                $Raautil_AccountDisclosure->setAccountId($this->simulationACCOUNTID != '' ? $this->simulationACCOUNTID : 'simulation.rsmartsepa@rubean.com');
                $Raautil_AccountDisclosure->setFirstname($this->simulationFIRSTNAME != '' ? $this->simulationFIRSTNAME : 'Susi');
                $Raautil_AccountDisclosure->setLastname($this->simulationLASTNAME != '' ? $this->simulationLASTNAME : 'Simulation');
                $Raautil_AccountDisclosure->setAddress1($this->simulationADDRESS1 != '' ? $this->simulationADDRESS1 : 'Frau');
                $Raautil_AccountDisclosure->setAddress2($this->simulationADDRESS2 != '' ? $this->simulationADDRESS2 : 'Susi Simulation');
                $Raautil_AccountDisclosure->setAddress3($this->simulationADDRESS3 != '' ? $this->simulationADDRESS3 : 'Teststrasse 3');
                $Raautil_AccountDisclosure->setZipcode($this->simulationZIPCODE != '' ? $this->simulationZIPCODE : '81379');
                $Raautil_AccountDisclosure->setCity($this->simulationCITY != '' ? $this->simulationCITY : 'Muenchen');
                $Raautil_AccountDisclosure->setCountry($this->simulationCOUNTRY != '' ? $this->simulationCOUNTRY : 'DE');
                $Raautil_AccountDisclosure->setMsisdn($this->simulationMSISDN != '' ? $this->simulationMSISDN : '017112345678');
                $Raautil_AccountDisclosure->setMale($this->simulationMALE != -1 ? $this->simulationMALE : 0); // or 1
                $Raautil_AccountDisclosure->setDateOfBirth($this->simulationDATEOFBIRTH != '' ? $this->simulationDATEOFBIRTH : '15.05.1985');
                $Raautil_AccountDisclosure->setBankAccountNumber($this->simulationBANKACCOUNTNUMBER != '' ? $this->simulationBANKACCOUNTNUMBER : '12345678');
                $Raautil_AccountDisclosure->setBankCode($this->simulationBANKCODE != '' ? $this->simulationBANKCODE : '70150000');
            }
            $resultArray = array(
                'Raa_MatchResponse'         => $Raa_MatchResponse,
                'Raautil_DataStore'         => $Raautil_DataStore,
                'Raautil_AccountDisclosure' => $Raautil_AccountDisclosure,
                'hash'                      => $calculatedHash,
            );
            return $resultArray;
        }
        else {
            $Raa_ServerInfo = $Raautil_DataStore->getTransactionResultServerInfo();
            $Raa_MatchResponse = $terminalClient->matchAndGet($tid, $Raa_ServerInfo , $reqId);
            if(isset($Raa_MatchResponse)) {
                if(isset($Raa_MatchResponse->auth_confirmation_data)) {
                    if(defined('RAAUTIL_CHECK_SIGNATURE')) {
                        $this->checkSignature($terminalClient, $Raa_MatchResponse);
                    }
                }
                
                if($Raa_MatchResponse->result == 'MATCH') {
                    if($Raa_MatchResponse instanceof Raa_MatchResponseWithSecureConfirmationAndIdentity) {
                        $acctDisclosure = isset($Raa_MatchResponse->acctDisclosure) ? $Raa_MatchResponse->acctDisclosure : NULL;
                        $Raautil_AccountDisclosure = Raautil_AccountDisclosure::createFromAccountDisclosureString($acctDisclosure);
                    }
                }
            } // end: if(isset($Raa_MatchResponse))
            $Raautil_DataStore->setLastAction('MATCHANDGET');
            $Raautil_DataStore->setLastStatus(isset($Raa_MatchResponse) ? $Raa_MatchResponse->result : 'FAILURE');
            $Raautil_DataStore->incrementMatchCounter();
            $calculatedHash = $Raautil_DataStore->calculateHashCode($this->getSellerInfo());
            $Raautil_DataStore->setHashCode($calculatedHash);
            $this->DataStoreProvider->updateDatastore($tid, $Raautil_DataStore);
            $resultArray = array(
                'Raa_MatchResponse'         => $Raa_MatchResponse,
                'Raautil_DataStore'         => $Raautil_DataStore,
                'Raautil_AccountDisclosure' => $Raautil_AccountDisclosure,
                'hash'                      => $calculatedHash,
            );
            return $resultArray;
        }
    } // End matchAndGetTransaction
    
    
    
    /**
     * Removes the transaction for the given TID on the matching server or simulates the remove.
     * 
     * @param string $tid
     *    The TID
     * 
     * @param string $hash
     *    The hashcode
     * 
     * @param boolean $checkHash
     *    TRUE if the hashcode should be checked, FALSE otherwise
     *    (Default is FALSE)
     * 
     * @return array
     *    A structured array in the format
     * 
     *    array(
     *       'Raa_MatchResponse'         => (Object) The Raa_MatchResponse,
     *       'Raautil_DataStore'         => (Object) The updated Raautil_DataStore,
     *       'hash'                      => (String) The next hashcode,
     *    )
     * 
     * @throws Exception|Raautil_TransactionException
     *    On error
     */
    public function removeTransaction($tid = '', $hash = '', $checkHash = FALSE) {
        $tid = isset($tid) ? trim($tid) : '';
        if($tid == '') {
            throw new Raautil_TransactionException("Empty TID");
        }
        $hash = isset($hash) ? trim($hash) : '';
        $checkHash = isset($checkHash) ? ($checkHash == TRUE ? TRUE : FALSE) : FALSE;

        $Raautil_DataStore = $this->DataStoreProvider->readDatastore($tid);
        if(!isset($Raautil_DataStore)) {
            throw new Raautil_TransactionException("Transaction data not found for tid: " . $tid);
        }
        $terminalClient = $this->createTerminal();
        if(!isset($terminalClient)) {
            throw new Raautil_TransactionException("Error creating terminalClient");
        }
        
        if($checkHash == TRUE) {
            if($Raautil_DataStore->getHashCode() != $hash) {
                throw new Raautil_TransactionException("Invalid hash for tid: " . $tid, 8, null, TRUE);
            }
        }
        
        $reqId = time();
        if($this->simulation == TRUE) {
            
            if (defined("RAA_DEBUG")) {
                $logTitle = 'Raautil_TransactionHandler->removeTransaction';
                $logData = 'TID=' . $tid . ', hash=' . $hash . ', checkHash=' . ($checkHash == TRUE ? 'true' : 'false');
	        TerminalSdkLibrary::log('debug', $logTitle, $logData);
            }
            
            $Raa_MatchResponse = new Raa_MatchResponse('MATCH', 2001, 1000);
            $Raautil_DataStore->setLastAction('REMOVE');
            $Raautil_DataStore->setLastStatus(isset($Raa_MatchResponse) ? $Raa_MatchResponse->result : 'FAILURE');
            $Raautil_DataStore->incrementMatchCounter();
            $calculatedHash = $Raautil_DataStore->calculateHashCode($this->getSellerInfo());
            $Raautil_DataStore->setHashCode($calculatedHash);
            $this->DataStoreProvider->updateDatastore($tid, $Raautil_DataStore);
            $resultArray = array(
                'Raa_MatchResponse'         => $Raa_MatchResponse,
                'Raautil_DataStore'         => $Raautil_DataStore,
                'hash'                      => $calculatedHash,
            );
            return $resultArray;
        }
        else {
            $Raa_ServerInfo = $Raautil_DataStore->getTransactionResultServerInfo();
            // Only Protocol 1 is supported
            //$Raa_MatchResponse = $terminalClient->remove($tid, $Raa_ServerInfo, $this->protocol == 0 ? null : $reqId);
            $Raa_MatchResponse = $terminalClient->remove($tid, $Raa_ServerInfo, $reqId);
            $Raautil_DataStore->setLastAction('REMOVE');
            $Raautil_DataStore->setLastStatus(isset($Raa_MatchResponse) ? $Raa_MatchResponse->result : 'FAILURE');
            $Raautil_DataStore->incrementMatchCounter();
            $calculatedHash = $Raautil_DataStore->calculateHashCode($this->getSellerInfo());
            $Raautil_DataStore->setHashCode($calculatedHash);
            $this->DataStoreProvider->updateDatastore($tid, $Raautil_DataStore);
            $resultArray = array(
                'Raa_MatchResponse'         => $Raa_MatchResponse,
                'Raautil_DataStore'         => $Raautil_DataStore,
                'hash'                      => $calculatedHash,
            );
            return $resultArray;
        }
    } // End removeTransaction
    
    /**
     * Checks the transaction historey for an amount transaction for a given TID.
     * 
     * @param string $tid
     *    The TID to check for
     * 
     * @return array
     *    An array containing Raa_HistoryRecord objects
     * 
     * @throws Exception|Raautil_TransactionException
     *    On error 
     */
    public function getTransactionHistory($tid = '') {
        $tid = isset($tid) ? trim($tid) : '';
        if($tid == '') {
            throw new Raautil_TransactionException("Empty TID");
        }
        
        $Raautil_DataStore = $this->DataStoreProvider->readDatastore($tid);
        if(!isset($Raautil_DataStore)) {
            throw new Raautil_TransactionException("Transaction data not found for tid: " . $tid);
        }
        $terminalClient = $this->createTerminal();
        if(!isset($terminalClient)) {
            throw new Raautil_TransactionException("Error creating terminalClient");
        }
        
        
        $terminalProviderId = $this->Raautil_TerminalData->getProviderID();
        $sellerId = $this->Raautil_TerminalData->getSellerID();
        $salesPointId = $this->Raautil_TerminalData->getSalesPointID();
        $applicationId = $this->Raautil_TerminalData->getApplicationID();
        $terminalDesc = $this->Raautil_TerminalData->getDescription();
        $sellerName = $this->Raautil_TerminalData->getSellerName();
        
        $Raa_TransactionDataAmount = $Raautil_DataStore->getTransactionDataAmount();
        if(!isset($Raa_TransactionDataAmount)) {
            throw new Raautil_TransactionException("No Raa_TransactionDataAmount stored in datastore file for tid: " . $tid);
        }
        
        $txType = $Raa_TransactionDataAmount->getType();
        $txIdLocal = $Raa_TransactionDataAmount->getLocalTransactionId();
        $txAmount = $Raa_TransactionDataAmount->getAmount();
        $txCurrencyCode = $Raa_TransactionDataAmount->getCurrencyCode();
        
        $resultArray = array();
        $reqId = time();
        if($this->simulation == TRUE) {
            
            if (defined("RAA_DEBUG")) {
                $logTitle = 'Raautil_TransactionHandler->getTransactionHistory';
                $logData = 'TID=' . $tid;
	        TerminalSdkLibrary::log('debug', $logTitle, $logData);
            }
            
            $time = time();
            $txStat = 'OK'; // ?? What else is possible ??
            $confirmTime = time();
            $acctProviderId = $terminalProviderId;
            $authConfirmationData = array();
            $signatureCertChain = array();
            $signature = '';
            $signatureHashMethod = 'SHA256';
            
            $Raa_HistoryRecord = new Raa_HistoryRecord($tid, $time, $terminalProviderId, $sellerId, $salesPointId, $applicationId,
			$terminalDesc, $sellerName, $txType, $txIdLocal, $txAmount, $txCurrencyCode,
			$txStat, $confirmTime, $acctProviderId, $authConfirmationData, $signatureCertChain,
			$signature, $signatureHashMethod);
            $resultArray[] = $Raa_HistoryRecord;
        }
        else {
            $firstResult = null;
            $maxResults = null;
            $terminalInfo = null; // Raa_TerminalInfo. Is already stored in terminalClient
            $key = null;
            $timestamp = null;
            
            // 2015.09.14: Check for serverTimeOffset Exception
            $clientOffset = 0;
            while(TRUE) {
                $timestamp = time();
                try {
                    $timestamp = $timestamp + $clientOffset;
                    // This may throw an exception
                    $Raa_HistoryResult = $terminalClient->getHistory($tid, $firstResult, $maxResults, $terminalInfo, $key, $timestamp);
                    break;
                } catch (Exception $ex) {
                    if($ex instanceof Raa_ClientTimestampException) {
                        $Raa_ClientTimestampException = $ex;
                        $clientOffset = $clientOffset + $Raa_ClientTimestampException->getServerTimeOffset();
                        continue;
                    }
                    else {
                        throw $ex;
                    }
                }
            }
            //$Raa_HistoryResult = $terminalClient->getHistory($tid, $firstResult, $maxResults, $terminalInfo, $key, $timestamp);
            if(!isset($Raa_HistoryResult)) {
                throw new Raautil_TransactionException("No Raa_HistoryResult received for tid: " . $tid);
            }
            $Raa_HistoryRecordArray = isset($Raa_HistoryResult->records) ? (is_array($Raa_HistoryResult->records) ? $Raa_HistoryResult->records : null) : null;
            if(!isset($Raa_HistoryRecordArray)) {
                throw new Raautil_TransactionException("No Raa_HistoryRecords received for tid: " . $tid);
            }
            
            // Find corresponding history record
            foreach($Raa_HistoryRecordArray as $Raa_HistoryRecord) {
                if($Raa_HistoryRecord->tid == $tid &&
                   $Raa_HistoryRecord->terminalProviderId == $terminalProviderId &&
                   $Raa_HistoryRecord->sellerId == $sellerId &&
                   $Raa_HistoryRecord->salesPointId == $salesPointId &&
                   $Raa_HistoryRecord->applicationId == $applicationId &&
                   $Raa_HistoryRecord->terminalDesc == $terminalDesc &&
                   $Raa_HistoryRecord->txType == $txType &&
                   $Raa_HistoryRecord->txIdLocal == $txIdLocal &&
                   $Raa_HistoryRecord->txAmount == $txAmount &&
                   $Raa_HistoryRecord->txCurrencyCode == $txCurrencyCode) {
                    $resultArray[] = $Raa_HistoryRecord;
                }
            }
        }
        return $resultArray;
    } // End getTransactionHistory
    
    /**
     * Checks if the history of an amount transaction for a given TID reports OK.
     * 
     * @param string $tid
     *    The TID to check
     * 
     * @return boolean
     *    TRUE if the history reports OK, FALSE otherwise 
     */
    public function isAmountTransactionHistorySuccess($tid = '') {
        $result = FALSE;
        try {
            $resultArray = $this->getTransactionHistory($tid);
            foreach($resultArray as $Raa_HistoryRecord) {
                ($Raa_HistoryRecord instanceof Raa_HistoryRecord);
                if(isset($Raa_HistoryRecord->txStat)) {
                    $stat = strtolower(trim($Raa_HistoryRecord->txStat));
                    // MWMUC 02.02.2015: Changed to lowercase
                    if($stat == 'ok') {
                        $result = TRUE;
                        break;
                    }
                }
            }
        } catch(Exception $ex) {}
        return $result;
    } // End isAmountTransactionHistorySuccess
    
    /**
     * Sets the simulation status for a given TID.
     *
     * @param string $tid
     *    The TID
     * 
     * @param string $status
     *    The status. This must be 'MATCH', 'FAILURE' or 'ERROR'.
     *    If it is none of them, 'MATCH' is assumed.
     * 
     * @return Raautil_DataStore
     *    The updated Raautil_DataStore
     * 
     * @throws Raautil_TransactionException
     *    On error
     */
    public function setSimulationStatus($tid = '', $status = 'MATCH') {
        $tid = isset($tid) ? trim($tid) : '';
        if($tid == '') {
            throw new Raautil_TransactionException("Empty TID");
        }
        $status = isset($status) ? trim($status) : 'MATCH';
        if($status == '') {
            throw new Raautil_TransactionException("Empty STATUS");
        }
        if($status != 'MATCH' && $status != 'FAILURE' && $status != 'ERROR') {
            $status = 'MATCH';
        }
        
        // This may throw an Raautil_TransactionException
        $this->validate();
        
        $Raautil_DataStore = $this->DataStoreProvider->readDatastore($tid);
        if(!isset($Raautil_DataStore)) {
            throw new Raautil_TransactionException("Transaction data not found for tid: " . $tid);
        }
        
        $Raautil_DataStore->setSimulatedStatus($status);
        $this->DataStoreProvider->updateDatastore($tid, $Raautil_DataStore);
        return $Raautil_DataStore;
    } // End setSimulationStatus
    
    /**
     * Tries to delete the DataStore by its provider.
     * 
     * @param string $tid
     *    The TID to delete the datastore for
     * 
     * @return boolean 
     *    TRUE if no exception was thrown, FALSE otherwise
     */
    public function deleteDataStore($tid = '') {
        $result = FALSE;
        try {
            $tid = isset($tid) ? trim($tid) : '';
            if($tid != '') {
                $this->validate();
                $this->DataStoreProvider->deleteDatastore($tid);
                $result = TRUE;
            }
        } catch(Exception $ex) {}
        return $result;
    } // End deleteDataStore
    
    protected function checkSignature($Raa_TerminalClientV1Default = NULL, $Raa_MatchResponse = NULL) {
        if(!isset($Raa_TerminalClientV1Default)) {
            throw new Raautil_TransactionException("Raa_TerminalClientV1Default is null");
        }
        if(!isset($Raa_MatchResponse)) {
            throw new Raautil_TransactionException("Raa_MatchResponse is null");
        }
        if(!$this->isExtensionsExisting()) {
            return;
        }
        
        $sigResult = NULL;
        if(isset($Raa_MatchResponse->auth_confirmation_data)) {
            $sigResult = $terminalClient->checkSignature($Raa_MatchResponse->auth_confirmation_data, 
                                                         $Raa_MatchResponse->signature_cert_chain, 
                                                         $Raa_MatchResponse->signature, 
                                                         $Raa_MatchResponse->signature_hash_method);
        }
    } // End checkSignature
    
    public function getSellerInfo() {
        $sellerId = 'sellerId';
        $key = 'sellerKey';
        if(isset($this->Raautil_TerminalData)) {
            $sellerId = $this->Raautil_TerminalData->getSellerID();
            $key = $this->Raautil_TerminalData->getKey();
        }
        $result = array(
            'id'        => $sellerId,
            'key'       => $key,
        );
        return $result;
    } // End getSellerInfo
    
    
    public function isExtensionsExisting() {
        if(function_exists('openssl_x509_read') &&
           function_exists('openssl_x509_free') &&
           function_exists('openssl_verify') &&
           function_exists('cert_check')) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    } // End isExtensionsExisting
    
    
    /**
     * Generates a random TID.
     * 
     * @return string 
     *    A random TID in the format 'xxxxxxxxxxxxxxxx-xxxxxxxxxxxxxxxx-xxxxxxxxxxxxxxxx-xxxxxxxxxxxxxxxx'
     *    For example: '2dc3a1548bd40921-bc9822ab0fec0e9f-281161ba152bd2ba-592271371640a75b'
     */
    protected function generateTID() {
        // '2dc3a1548bd40921-bc9822ab0fec0e9f-281161ba152bd2ba-592271371640a75b'
        $characters = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
                            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
                            'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
                            'u', 'v', 'w', 'x', 'y', 'z',
                      );
        
        $maxlen = (16 * 4) + 3;
        $upper = (16 * 4) - 1;
        $output = '';
        $part = '';
        while(strlen($output) < $maxlen) {
            $ind = rand(0, $upper);
            if(isset($characters[$ind])) {
               $part = $part . $characters[$ind];
               if(strlen($part) == 16) {
                   if($output == '')
                       $output = $part;
                   else
                       $output = $output . '-' . $part;
                   $part = '';
               }
            }
        }
        return $output;
    } // End generateTID
    

} // End class Raautil_TransactionHandler

