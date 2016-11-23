<?php
/* --------------------------------------------------------------
  DataStore.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class Raautil_DataStore {
    
    const HMAC_HASH_FUNCTION = 'sha256';

    private $Raa_TransactionDataAmount = NULL;
    private $Raa_TransactionDataEmpty = NULL;
    private $Raa_TransactionResult = NULL;
    private $lastAction = '';
    private $lastStatus = '';
    private $simulationMode = FALSE;
    private $simulatedStatus = '';
    private $qrCode = '';
    private $qrCodeB64 = '';
    private $nextHash = '';
    private $smartIDSimulationCallCount = 0;
    private $customGeneratedQRCodeUrl = '';
    private $matchCounter = 0;
    private $tsCreated = 0;
    private $tsChanged = 0;
    
    
    public function __construct() {
        
    } // End constructor
    
    /**
     * Sets the Raa_TransactionDataAmount instance.
     * 
     * @param Raa_TransactionDataAmount $Raa_TransactionDataAmount
     *   An instance of Raa_TransactionDataAmount
     * 
     * @return Raautil_DataStore 
     */
    public function setTransactionDataAmount($Raa_TransactionDataAmount = NULL) {
        if(isset($Raa_TransactionDataAmount)) {
            if($Raa_TransactionDataAmount instanceof Raa_TransactionDataAmount) {
                $this->Raa_TransactionDataAmount = $Raa_TransactionDataAmount;
            }
        }
        return $this;
    } // End setTransactionDataAmount

    /**
     * Returns an instance of Raa_TransactionDataAmount or NULL.
     * 
     * @return Raa_TransactionDataAmount|NULL
     *     An instance of Raa_TransactionDataAmount or NULL
     */
    public function getTransactionDataAmount() {
        return $this->Raa_TransactionDataAmount;
    } // End getTransactionDataAmount

    public function getAmount() {
        $result = 0.00;
        if(isset($this->Raa_TransactionDataAmount)) {
            if($this->Raa_TransactionDataAmount instanceof Raa_TransactionDataAmount) {
                $result = $this->Raa_TransactionDataAmount->getAmount();
            }
        }        
        return $result;
    } // End getAmount

    public function getCurrency() {
        $result = 'EUR';
        if(isset($this->Raa_TransactionDataAmount)) {
            if($this->Raa_TransactionDataAmount instanceof Raa_TransactionDataAmount) {
                $result = $this->Raa_TransactionDataAmount->getCurrencyCode();
            }
        }        
        return $result;
    } // End getCurrency

    public function getTransactionId() {
        $result = '';
        if(isset($this->Raa_TransactionDataAmount)) {
            if($this->Raa_TransactionDataAmount instanceof Raa_TransactionDataAmount) {
                $result = $this->Raa_TransactionDataAmount->getLocalTransactionId();
            }
        }
        return $result;
    } // End getTransactionId

    public function getTransactionDescription() {
        $result = '';
        if(isset($this->Raa_TransactionDataAmount)) {
            if($this->Raa_TransactionDataAmount instanceof Raa_TransactionDataAmount) {
                $result = $this->Raa_TransactionDataAmount->getDescription();
            }
        }
        return $result;
    } // End getTransactionDescription
    
    
    
    /**
     * Sets the Raa_TransactionData instance.
     * 
     * @param Raa_TransactionData $Raa_TransactionDataEmpty
     *   An instance of Raa_TransactionData
     * 
     * @return Raautil_DataStore 
     */
    public function setTransactionDataEmpty($Raa_TransactionDataEmpty = NULL) {
        if(isset($Raa_TransactionDataEmpty)) {
            if($Raa_TransactionDataEmpty instanceof Raa_TransactionData) {
                $this->Raa_TransactionDataEmpty = $Raa_TransactionDataEmpty;
            }
        }
        return $this;
    } // End setTransactionDataEmpty

    /**
     * Returns an instance of Raa_TransactionData or NULL.
     * 
     * @return Raa_TransactionData|NULL
     *     An instance of Raa_TransactionData or NULL
     */
    public function getTransactionDataEmpty() {
        return $this->Raa_TransactionDataEmpty;
    } // End getTransactionDataEmpty
    
    
    
    /**
     * Sets in instance of Raa_TransactionResult.
     * 
     * @param Raa_TransactionResult $Raa_TransactionResult
     *    An instance of Raa_TransactionResult
     * 
     * @return Raautil_DataStore 
     */
    public function setTransactionResult($Raa_TransactionResult = NULL) {
        if(isset($Raa_TransactionResult)) {
            if($Raa_TransactionResult instanceof Raa_TransactionResult) {
                $this->Raa_TransactionResult = $Raa_TransactionResult;
            }
        }
        return $this;
    } // End setTransactionResult
    
    /**
     * Returns an instance of Raa_TransactionResult or NULL.
     * 
     * @return Raa_TransactionResult|NULL
     *     an instance of Raa_TransactionResult or NULL
     */
    public function getTransactionResult() {
        return $this->Raa_TransactionResult;
    } // End getTransactionResult

    public function getTransactionResultTID() {
        $tid = '';
        if(isset($this->Raa_TransactionResult)) {
            if($this->Raa_TransactionResult instanceof Raa_TransactionResult) {
                $tid = $this->Raa_TransactionResult->tid;
            }
        }        
        return $tid;
    } // End getTransactionResultTID

    public function getTransactionResultServerInfo() {
        $Raa_ServerInfo = NULL;
        if(isset($this->Raa_TransactionResult)) {
            if($this->Raa_TransactionResult instanceof Raa_TransactionResult) {
                $Raa_ServerInfo = $this->Raa_TransactionResult->serverInfo;
            }
        }
        return $Raa_ServerInfo;
    } // End getTransactionResultServerInfo

    
    public function setCustomGeneratedQRCodeUrl($url = '') {
        $this->customGeneratedQRCodeUrl = isset($url) ? (is_string($url) ? trim($url) : '') : '';
        return $this;
    } // End setCustomGeneratedQRCodeUrl
    
    public function getCustomGeneratedQRCodeUrl() {
        return $this->customGeneratedQRCodeUrl;
    } // End getCustomGeneratedQRCodeUrl
    
    
    /**
     * Sets the last action.
     * 
     * @param string $action
     *    The last action like 'CREATE', 'MATCH', 'MATCHANDGET' or 'REMOVE'
     * 
     * @return Raautil_DataStore 
     */
    public function setLastAction($action = '') {
        $action = isset($action) ? (is_string($action) ? trim($action) : ''): '';
        $this->lastAction = $action;
        return $this;
    } // End setLastAction
    
    /**
     * Returns the last action.
     * 
     * @return string
     *     The last action like 'CREATE', 'MATCH', 'MATCHANDGET' or 'REMOVE' or an empty string
     */
    public function getLastAction() {
        return $this->lastAction;
    } // End getLastAction

    
    
    /**
     * Sets the last status.
     * 
     * @param string $status
     *    The last status like 'MATCH', 'FAILURE' or 'ERROR'
     * 
     * @return Raautil_DataStore 
     */
    public function setLastStatus($status = '') {
        $status = isset($status) ? (is_string($status) ? trim($status) : ''): '';
        $this->lastStatus = $status;
        return $this;
    } // End setLastStatus
    
    /**
     * Returns the last status.
     * 
     * @return string
     *    The last status like 'PENDING', 'MATCH', 'FAILURE' or 'ERROR' or an empty string
     * 
     * @return string
     *    The last status
     */    
    public function getLastStatus() {
        return $this->lastStatus;
    } // End getLastStatus

    
    /**
     * Sets the simulated status.
     * 
     * @param string $status
     *    The simulated status like 'MATCH' or 'FAILURE'
     * 
     * @return Raautil_DataStore 
     */
    public function setSimulatedStatus($status = '') {
        $status = isset($status) ? (is_string($status) ? trim($status) : ''): '';
        $this->simulatedStatus = $status;
        return $this;
    } // End setSimulatedStatus
    
    /**
     * Returns the simulated status.
     * 
     * @return string
     *     The simulated status like 'MATCH' or 'FAILURE'
     */
    public function getSimulatedStatus() {
        return $this->simulatedStatus;
    } // End getSimulatedStatus
    
    
    public function setSimulationMode($mode = FALSE) {
        $mode = isset($mode) ? ($mode == TRUE ? TRUE : FALSE) : FALSE;
        $this->simulationMode = $mode;
        return $this;
    } // End setSimulationMode
    
    public function isSimulationMode() {
        return $this->simulationMode;
    } // End isSimulationMode
    
    
    public function setQRCode($qrcode = '') {
        if(isset($qrcode)) {
            $this->qrCode = $qrcode;
        }
        return $this;
    } // End setQRCode
    
    public function getQRCode() {
        return $this->qrCode;
    } // End getQRCode

    
    public function setQRCodeB64($b64 = '') {
        if(isset($b64)) {
            $this->qrCodeB64 = $b64;
        }
        return $this;
    } // End setQRCodeB64
    
    public function getQRCodeB64() {
        return $this->qrCodeB64;
    } // End getQRCodeB64

    
    public function setTimestampCreated($value = 0) {
        $created = 0;
        if(isset($value)) {
            if(is_int($value)) {
                $created = $value;
            }
            else if(is_string($value)) {
                if(ctype_digit($value)) {
                    $created = intval($value);
                }
            }
        }
        $this->tsCreated = $created;        
        return $this;
    } // End setTimestampCreated
    
    /**
     * Returns the creation unix timestamp.
     * 
     * @return int
     *     The creation unix timestamp
     */
    public function getTimestampCreated() {
        return $this->tsCreated;
    } // End getTimestampCreated
    
    
    public function setTimestampChanged($value = 0) {
        $changed = 0;
        if(isset($value)) {
            if(is_int($value)) {
                $changed = $value;
            }
            else if(is_string($value)) {
                if(ctype_digit($value)) {
                    $changed = intval($value);
                }
            }
        }
        $this->tsChanged = $changed;     
        return $this;
    } // End setTimestampChanged
    
    /**
     * Returns the last change unix timestamp.
     * 
     * @return int
     *     The last change unix timestamp
     */
    public function getTimestampChanged() {
        return $this->tsChanged;
    } // End getTimestampChanged
    
    public function incrementMatchCounter() {
        $this->matchCounter++;
    } // End incrementMatchCounter
    
    public function getMatchCounter() {
        return $this->matchCounter;
    } // End getMatchCounter
    
    /**
     * Calculates a hashcode.
     * 
     * @param array $sellerInfo
     *    The seller info array
     *    
     *    array(
     *      'id'    => (string) the seller id,
     *      'key'   => (string) the seller key,
     *    )
     * @return type 
     */
    public function calculateHashCode($sellerInfo = array()) {
        $result = '';
        $sellerInfo = isset($sellerInfo) ? (is_array($sellerInfo) ? $sellerInfo : array()) : array();
        $sellerId = isset($sellerInfo['id']) ? $sellerInfo['id'] : 'sellerId';
        if(trim($sellerId) == '') {
            $sellerId = 'sellerId';
        }
        $key = isset($sellerInfo['key']) ? $sellerInfo['key'] : 'sellerKey';
        if(trim($key) == '') {
            $key = 'sellerKey';
        }
        
        $data = $sellerId;
        $data = $data . strval($this->matchCounter);
        
        $tmp = self::createHmac($data, $key);
        $allowedChars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 
                              'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
                              'u', 'v', 'w', 'x', 'y', 'z',
                              'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 
                              'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
                              'U', 'V', 'W', 'X', 'Y', 'Z',
                              '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $result = '';
        $len = strlen($tmp);
        for($i = 0; $i < $len; $i++) {
            $c = substr($tmp, $i, 1);
            if(in_array($c, $allowedChars)) {
                $result .= $c;
            }
        }
        
        return $result;
    } // End calculateHashCode
    
    /**
     * Returns the hashcode created with createNextHashCode().
     * 
     * @return string
     *     The next hash code
     */
    public function getHashCode() {
        return $this->nextHash;
    } // End getHashCode
    
    public function setHashCode($hash = '') {
        $this->nextHash = isset($hash) ? (is_string($hash) ? $hash : '') : '';
        return $this;
    } // End setHashCode
    
    public function getSmartIDSimulationCallCount() {
        return $this->smartIDSimulationCallCount;
    } // End getSmartIDSimulationCallCount
    
    public function incrementSmartIDSimulationCallCount() {
        $this->smartIDSimulationCallCount += 1;
        if($this->smartIDSimulationCallCount > 5) {
            $this->setSimulatedStatus('MATCH');
        }
    } // End incrementSmartIDSimulationCallCount
    
    /**
     * Serializes this object.
     * 
     * @return string
     *     The serialized string  
     */
    public function serializeDataStore() {
        $resultString = @serialize($this);
        if(!isset($resultString)) {
            $resultString = '';
        }
        else if(!is_string($resultString)) {
            $resultString = '';
        }
        return $resultString;
    } // End serializeDataStore
    
    /**
     * Deserializes this object.
     * 
     * @param string $serializedString
     *    The serialized string
     * 
     * @return Raautil_DataStore 
     *    An instance of Raautil_DataStore or NULL
     */
    public static function deserializeDataStore($serializedString = '') {
        $serializedString = isset($serializedString) ? (is_string($serializedString) ? $serializedString : '') : '';
        if($serializedString == '') {
            return NULL;
        }
        
        $Raautil_DataStore = NULL;
        $obj = @unserialize($serializedString);
        if(isset($obj)) {
            if(is_object($obj)) {
                if($obj instanceof Raautil_DataStore) {
                    $Raautil_DataStore = $obj;
                }
            }
        }
        return $Raautil_DataStore;
    } // End deserializeDataStore
    
    /**
     * Create a HMAC.
     *
     * @param string $data The data from which the HMAC should be created.
     * @param $key The key for the HMAC calculation.
     * @return string The calculated base64 encoded HMAC.
     */
    public static function createHmac($data, $key) {
        return base64_encode(pack("H*", hash_hmac(self::HMAC_HASH_FUNCTION, $data, $key)));
    } // End createHmac
    
} // End class Raautil_DataStore

