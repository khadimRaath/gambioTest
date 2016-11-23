<?php
/* --------------------------------------------------------------
  RsmartsepaTransactionWrapper.php 2015-04-27 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */
class RsmartsepaTransactionWrapper implements Raautil_ITerminalDataProvider, Raautil_IDataStoreProvider {
    
    private $applicationId = 'OPM';
    private $simulation = FALSE;
    private $terminalDataArray = array();
    private $simulationData = array();
    
    /**
     * An optional instane of Raautil_TerminalData
     * @var Raautil_TerminalData 
     */
    private $Raautil_TerminalData = NULL;
    
    /**
     * The TransactionHandler
     * @var Raautil_TransactionHandler
     */
    private $Raautil_TransactionHandler = NULL;
    
    /**
     * An instance of Raautil_IDataStoreProvider
     * @var Raautil_IDataStoreProvider 
     */
    private $Raautil_IDataStoreProvider = NULL;

    /**
     * Instance of Raautil_DataStore
     * @var Raautil_DataStore 
     */
    private $Raautil_DataStore = NULL;
    
    private $lastMatchOrRemoveResult = '';
    private $Raautil_AccountDisclosure = NULL;

    /**
     * Constructs a new TransactionWrapper.
     * 
     * @param string $applicationId
     *    The application id.
     *    Must be either Raautil_TerminalData::APPLICATION_ID_OPM or Raautil_TerminalData::APPLICATION_ID_ICG
     * 
     * @param boolean $simulation
     *    Determines the simulation mode.
     *    
     * @param type $terminalDataArray 
     */
    public function __construct($applicationId = 'OPM', $simulation = FALSE, $terminalDataArray = array()) {
        $this->applicationId = isset($applicationId) ? (is_string($applicationId) ? trim($applicationId) : Raautil_TerminalData::APPLICATION_ID_OPM) : Raautil_TerminalData::APPLICATION_ID_OPM;
        if($this->applicationId != Raautil_TerminalData::APPLICATION_ID_OPM &&
           $this->applicationId != Raautil_TerminalData::APPLICATION_ID_ICG) {
            $this->applicationId = Raautil_TerminalData::APPLICATION_ID_OPM;
        }
        $this->simulation = isset($simulation) ? ($simulation == TRUE ? TRUE : FALSE) : FALSE;
        $this->terminalDataArray = isset($terminalDataArray) ? (is_array($terminalDataArray) ? $terminalDataArray : array()) : array();
        $this->createDataStoreProvider();
    } // End constructor

    private function createDataStoreProvider() {
        $this->Raautil_IDataStoreProvider = new RsmartsepaDataStoreProvider();        
    } // End createDataStoreProvider
    
    public function setTransactionConstants() {
        if(!defined("RAA_DEBUG")) {
            define("RAA_DEBUG", "True");
        }
        if(!defined("RAA_TESTING_AVOID_CERT_ERROR")) {
            define("RAA_TESTING_AVOID_CERT_ERROR", "True");
        }
        if(!defined("RAAUTIL_CREATE_QRCODE_OUTPUTBUFFERING")) {
            define("RAAUTIL_CREATE_QRCODE_OUTPUTBUFFERING", "True");
        }
        
    } // End setTransactionConstants
    
    public function getDataStoreDirectory() {
        $dir = RsmartsepaHelper::getDatastoreDirectory();
        return $dir; 
    } // End getDataStoreDirectory
    
    public function getTerminalDataDirectory() {
        $helperdir = RsmartsepaHelper::getHelperDirectory();
        return $helperdir;
    } // End getTerminalDataDirectory
    
    public function isSimulation() {
        return $this->simulation;
    } // End isSimulation
    
    public function getApplicationId() {
        return $this->applicationId;
    } // End getApplicationId
    
    public function setSimulationData($simulationData = array()) {
        $this->simulationData = isset($simulationData) ? (is_array($simulationData) ? $simulationData : array()) : array();
        return $this;
    } // End setSimulationData
    
    public function getSimulationData() {
        return $this->simulationData;
    } // End getSimulationData

    public function getTerminalDataArray() {
        return $this->terminalDataArray;
    } // End getTerminalDataArray

    public function setTerminalData($Raautil_TerminalData = NULL) {
        if(isset($Raautil_TerminalData)) {
            if($Raautil_TerminalData instanceof Raautil_TerminalData) {
                $this->Raautil_TerminalData = $Raautil_TerminalData;
            }
        }
        return $this;
    } // End setTerminalData
    
    /**
     * Implements interface Raautil_ITerminalDataProvider.
     * Returns an instance of Raautil_TerminalData.
     * 
     * - If $this->Raautil_TerminalData != null, $this->Raautil_TerminalData is returned.
     * - If $this->terminalDataArray is not empty, Raautil_TerminalData is constructed from
     *   these values.
     * - If $this->terminalDataArray is empty then
     *      - if $this->applicationId == Raautil_TerminalData::APPLICATION_ID_OPM
     *        then Raautil_TerminalData::createDefaultOPM() is called
     *      - if $this->applicationId == Raautil_TerminalData::APPLICATION_ID_ICG
     *        then Raautil_TerminalData::createDefaultICG() is called
     *      - otherwise an Exception is thrown
     * 
     * @return Raautil_TerminalData
     *    An instance of Raautil_TerminalData
     * 
     * @throws Exception
     *    In an error occured
     */
    public function getTerminalData() {
        if($this->Raautil_TerminalData != null) {
            $this->Raautil_TerminalData->setSimulation($this->simulation);
            return $this->Raautil_TerminalData;
        }
        else {
            if(count($this->terminalDataArray) == 0) {
                if($this->applicationId == Raautil_TerminalData::APPLICATION_ID_OPM) {
                    $this->Raautil_TerminalData = $this->createDefaultOPM();
                    if(isset($this->Raautil_TerminalData)) {
                        $this->Raautil_TerminalData->setSimulation($this->simulation);
                    }
                    return $this->Raautil_TerminalData;
                }
                else if($this->applicationId == Raautil_TerminalData::APPLICATION_ID_ICG) {
                    $this->Raautil_TerminalData = $this->createDefaultICG();
                    if(isset($this->Raautil_TerminalData)) {
                        $this->Raautil_TerminalData->setSimulation($this->simulation);
                    }
                    return $this->Raautil_TerminalData;
                }
                else {
                    throw new Exception("Invalid applicationId");
                }
            }
            else {
                $this->Raautil_TerminalData = new Raautil_TerminalData($this->terminalDataArray);
                if(isset($this->Raautil_TerminalData)) {
                    $this->Raautil_TerminalData->setSimulation($this->simulation);
                }
                return $this->Raautil_TerminalData;
            }
        }
    } // End getTerminalData

    /**
     * Validates the DataStoreProvider
     * 
     * @throws Exception
     *    On error
     */
    public function validateDatastore() {
        $this->Raautil_IDataStoreProvider->validateDatastore();
    } // End validateDatastore

    /**
     * Inserts an instance of Raautil_DataStore with a given key.
     * 
     * @param string $key
     *    A unique key for storing
     * 
     * @param Raautil_DataStore $Raautil_DataStore
     *    An instance of Raautil_DataStore to insert
     * 
     * @return void
     *    Does not return any value
     * 
     * @throws Exception
     *    If the insert failed 
     */
    public function insertDatastore($key = '', $Raautil_DataStore = null) {
        $this->Raautil_IDataStoreProvider->insertDatastore($key, $Raautil_DataStore);
    } // End insertDatastore

    /**
     * Updates an instance of Raautil_DataStore with a given key.
     * 
     * @param string $key
     *    A unique key for storing
     * 
     * @param Raautil_DataStore $Raautil_DataStore
     *    An instance of Raautil_DataStore to update
     * 
     * @return void
     *    Does not return any value
     * 
     * @throws Exception
     *    If the update failed
     */
    public function updateDatastore($key = '', $Raautil_DataStore = null) {
        $this->Raautil_IDataStoreProvider->updateDatastore($key, $Raautil_DataStore);
    } // End updateDatastore

    /**
     * Deletes a stored Raautil_DataStore by its key.
     * 
     * @param string $key
     *    A unique key to delete
     * 
     * @return void
     *    Does not return any value
     * 
     * @throws Exception
     *    If the update failed
     * 
     */
    public function deleteDatastore($key = '') {
        $this->Raautil_IDataStoreProvider->deleteDatastore($key);
    } // End deleteDatastore

    /**
     * Reads a stored Raautil_DataStore by its key.
     * 
     * @param string $key
     *    A unique key to delete
     * 
     * @return Raautil_DataStore
     *    An instance of Raautil_DataStore or NULL
     * 
     */
    public function readDatastore($key = '') {
        return $this->Raautil_IDataStoreProvider->readDatastore($key);
    } // End readDatastore

    /**
     * Reads all stored Raautil_DataStore instances.
     * 
     * @return array
     *    An array with structured subarrays or an empty array
     * 
     *    array(
     *       array(
     *          'key'       => (string) The key,
     *          'data'      => (Raautil_DataStore) an instance of Raautil_DataStore
     *       ),
     *       ...
     *    )
     */
    public function readAllDatastores() {
        return $this->Raautil_IDataStoreProvider->readAllDatastores();
    } // End readAllDatastores

    
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
     * @throws Exception
     *    On error
     */
    public function createAmountTransaction($amount = 0.00, 
                                            $currency = 'EUR', 
                                            $traid = '', 
                                            $tradesc = '',
                                            $SellerAccountInfo = NULL) {
        $this->Raautil_TransactionHandler = new Raautil_TransactionHandler($this, $this, $this->getDataStoreDirectory());
        $this->Raautil_DataStore = $this->Raautil_TransactionHandler->createAmountTransaction($amount, $currency, $traid, $tradesc, $SellerAccountInfo);
        return $this->Raautil_DataStore;
    } // End createAmountTransaction

    /**
     * Creates an empty transaction.
     * 
     * @return Raautil_DataStore
     *    An instance of an already saved DataStore
     * 
     * @throws Exception
     *    On error
     */    
    public function createEmptyTransaction() {
        $this->Raautil_TransactionHandler = new Raautil_TransactionHandler($this, $this, $this->getDataStoreDirectory());
        $this->Raautil_DataStore = $this->Raautil_TransactionHandler->createEmptyTransaction();
        return $this->Raautil_DataStore;
    } // End createEmptyTransaction

    public function getCreatedTID() {
        $result = '';
        if(isset($this->Raautil_DataStore)) {
            $result = $this->Raautil_DataStore->getTransactionResultTID();
        }
        return $result;
    } // End getCreatedTID

    
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
     * @throws Exception
     *    On error
     */
    public function matchTransaction($tid = '', $hash = '', $checkHash = FALSE) {
        //$this->Raautil_TerminalData = $this->createDefaultTerminalData();
        $this->Raautil_TransactionHandler = new Raautil_TransactionHandler($this, $this, $this->getDataStoreDirectory());
        $resultArray = $this->Raautil_TransactionHandler->matchTransaction($tid, $hash, $checkHash);
        $Raa_MatchResponse = $resultArray['Raa_MatchResponse'];
        if($Raa_MatchResponse instanceof Raa_MatchResponse) {
            $this->lastMatchOrRemoveResult = $Raa_MatchResponse->result;
        }
        return $resultArray;
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
     * @throws Exception
     *    On error
     */
    public function matchAndGetTransaction($tid = '', $hash = '', $checkHash = FALSE) {
        //$this->Raautil_TerminalData = $this->createDefaultTerminalData();
        $this->Raautil_TransactionHandler = new Raautil_TransactionHandler($this, $this, $this->getDataStoreDirectory());
        $resultArray = $this->Raautil_TransactionHandler->matchAndGetTransaction($tid, $hash, $checkHash);
        $Raa_MatchResponse = $resultArray['Raa_MatchResponse'];
        if($Raa_MatchResponse instanceof Raa_MatchResponse) {
            $this->lastMatchOrRemoveResult = $Raa_MatchResponse->result;
        }
        $Raautil_AccountDisclosure = $resultArray['Raautil_AccountDisclosure'];
        if(isset($Raautil_AccountDisclosure) && ($Raautil_AccountDisclosure instanceof Raautil_AccountDisclosure)) {
            $this->Raautil_AccountDisclosure = $Raautil_AccountDisclosure;
        }
        return $resultArray;
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
     * @throws Exception
     *    On error
     */
    public function removeTransaction($tid = '', $hash = '', $checkHash = FALSE) {
        //$this->Raautil_TerminalData = $this->createDefaultTerminalData();
        $this->Raautil_TransactionHandler = new Raautil_TransactionHandler($this, $this, $this->getDataStoreDirectory());
        $resultArray = $this->Raautil_TransactionHandler->removeTransaction($tid, $hash, $checkHash);
        $Raa_MatchResponse = $resultArray['Raa_MatchResponse'];
        if($Raa_MatchResponse instanceof Raa_MatchResponse) {
            $this->lastMatchOrRemoveResult = $Raa_MatchResponse->result;
        }        
        return $resultArray;
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
     * @throws Exception
     *    On error 
     */
    public function getTransactionHistory($tid = '') {
        //$this->Raautil_TerminalData = $this->createDefaultTerminalData();
        $this->Raautil_TransactionHandler = new Raautil_TransactionHandler($this, $this, $this->getDataStoreDirectory());
        return $this->Raautil_TransactionHandler->getTransactionHistory($tid);
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
        //$this->Raautil_TerminalData = $this->createDefaultTerminalData();
        $this->Raautil_TransactionHandler = new Raautil_TransactionHandler($this, $this, $this->getDataStoreDirectory());
        return $this->Raautil_TransactionHandler->isAmountTransactionHistorySuccess($tid);
    } // End isAmountTransactionHistorySuccess

    public function getLastMatchOrRemoveResult() {
        return $this->lastMatchOrRemoveResult;
    } // End getLastMatchOrRemoveResult
    
    public function getAccountDisclosure() {
        return $this->Raautil_AccountDisclosure;
    } // End getAccountDisclosure

    public function deliverQrCodeForTID($tid = '') {
        $data = '';
        try {
            $Raautil_DataStore = $this->readDatastore($tid);
            if(isset($Raautil_DataStore)) {
                $data = $Raautil_DataStore->getQRCode();
            }
        } catch(Exception $ex) {}
        
        // Close Database Connection
        if(function_exists('xtc_db_close')) {
            xtc_db_close();
        }
        
        Raautil_Utils::deliverResource($data, Raautil_Utils::CONTENT_TYPE_IMAGE_PNG, TRUE);
    } // End deliverQrCodeForTID

    public function createDefaultTerminalData() {
        return Raautil_TerminalData::createDefaultTerminalData(
                $this->applicationId, 
                $this->simulation, 
                isset($this->simulationData['simulationACCOUNTID']) ? $this->simulationData['simulationACCOUNTID'] : '', 
                isset($this->simulationData['simulationFIRSTNAME']) ? $this->simulationData['simulationFIRSTNAME'] : '', 
                isset($this->simulationData['simulationLASTNAME']) ? $this->simulationData['simulationLASTNAME'] : '', 
                isset($this->simulationData['simulationADDRESS1']) ? $this->simulationData['simulationADDRESS1'] : '', 
                isset($this->simulationData['simulationADDRESS2']) ? $this->simulationData['simulationADDRESS2'] : '', 
                isset($this->simulationData['simulationADDRESS3']) ? $this->simulationData['simulationADDRESS3'] : '', 
                isset($this->simulationData['simulationZIPCODE']) ? $this->simulationData['simulationZIPCODE'] : '', 
                isset($this->simulationData['simulationCITY']) ? $this->simulationData['simulationCITY'] : '', 
                isset($this->simulationData['simulationCOUNTRY']) ? $this->simulationData['simulationCOUNTRY'] : '', 
                isset($this->simulationData['simulationMSISDN']) ? $this->simulationData['simulationMSISDN'] : '', 
                isset($this->simulationData['simulationDATEOFBIRTH']) ? $this->simulationData['simulationDATEOFBIRTH'] : '', 
                isset($this->simulationData['simulationMALE']) ? $this->simulationData['simulationMALE'] : 0,
                isset($this->simulationData['simulationBANKACCOUNTNUMBER']) ? $this->simulationData['simulationBANKACCOUNTNUMBER'] : '', 
                isset($this->simulationData['simulationBANKCODE']) ? $this->simulationData['simulationBANKCODE'] : '');
    } // End createDefaultTerminalData
    
    
    
    /**
     * Returns a Raautil_TerminalData object usable for amount transactions 
     * created from an ini file named 'terminalopm.ini' that is located in this 
     * directory where this class is located.
     * 
     * @return Raautil_TerminalData
     *    The Raautil_TerminalData object
     * 
     * @throws Exception
     *    If the file does not exist or it exists but contains invalid values
     */
    public function createDefaultOPM() {
//        $folder = $this->getTerminalDataDirectory();
//        $fname = 'terminaldata';
//        $Raautil_TerminalDataProviderInifile = new Raautil_TerminalDataProviderInifile($folder, $fname);
//        return $Raautil_TerminalDataProviderInifile->getTerminalData();
        
        $RsmartsepaTerminalDataProviderConfig = new RsmartsepaTerminalDataProviderConfig();
        return $RsmartsepaTerminalDataProviderConfig->getTerminalData();
    } // End createDefaultOPM

    /**
     * Returns a Raautil_TerminalData object usable for empty (login) transactions 
     * created from an ini file named 'terminalicg.ini' that is located in this 
     * directory where this class is located.
     * 
     * @return Raautil_TerminalData
     *    The Raautil_TerminalData object
     * 
     * @throws Exception
     *    If the file does not exist or it exists but contains invalid values
     */
    public function createDefaultICG() {
        $folder = $this->getTerminalDataDirectory();
        $fname = 'terminaldata';
        $Raautil_TerminalDataProviderInifile = new Raautil_TerminalDataProviderInifile($folder, $fname);
        return $Raautil_TerminalDataProviderInifile->getTerminalData();
    } // End createDefaultICG
    
    
    public function getCheckMatchServerInfos() {
        $infoArray = array(
            'simulation'        => $this->simulation,
            'terminaldata'      => array(),
        );
        
        try {
            $Raautil_TerminalData = $this->getTerminalData();
            if(isset($Raautil_TerminalData)) {
                $infoArray['terminaldata'] = $Raautil_TerminalData->toArray();
            }
        } catch (Exception $ex) {

        }
        
        return $infoArray;
    } // End getCheckMatchServerInfos
    
} // End class RsmartsepaTransactionWrapper

