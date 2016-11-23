<?php
/* --------------------------------------------------------------
  DataStoreProviderSession.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class Raautil_DataStoreProviderSession implements Raautil_IDataStoreProvider {
    
    const SESSION_KEY = 'Raautil_DataStoreProviderSession';
    
    public function __construct() {
        
    } // End constructor
    
    /**
     * Validates the values.
     * 
     * @throws Raautil_TransactionException
     *    On error
     */
    public function validateDatastore() {
        if(!isset($_SESSION)) {
            throw new Raautil_TransactionException('No Session started');
        }
    } // End validateDatastore
    
    private function normalizeKey($key = '') {
        $key = str_replace(' ', '_', $key);
        return $key;
    } // End normalizeKey

    public function readDatastore($key = '') {
        $key = isset($key) ? (is_string($key) ? trim($key) : '') : '';
        if($key == '') {
            return NULL;
        }
        
        try {
            $this->validateDatastore();
        } catch(Raautil_TransactionException $ex) {
            return NULL;
        }
        
        $Raautil_DataStore = NULL;
        $key = $this->normalizeKey($key);
        if(isset($_SESSION[self::SESSION_KEY])) {
            if(is_array($_SESSION[self::SESSION_KEY])) {
                if(isset($_SESSION[self::SESSION_KEY][$key])) {
                    $Raautil_DataStore = Raautil_DataStore::deserializeDataStore($_SESSION[self::SESSION_KEY][$key]);
                }
            }
        }
        return $Raautil_DataStore;
    } // End readDatastore
    
    public function insertDatastore($key = '', $Raautil_DataStore = null) {
        $key = isset($key) ? (is_string($key) ? trim($key) : '') : '';
        if($key == '') {
            throw new Raautil_TransactionException("key is empty");
        }
        
        $this->validateDatastore();
        
        if(!isset($Raautil_DataStore)) {
            throw new Raautil_TransactionException("Raautil_DataStore is null");
        }
        else if(!is_object($Raautil_DataStore)) {
            throw new Raautil_TransactionException("Raautil_DataStore is no object");
        }
        else if(!($Raautil_DataStore instanceof Raautil_DataStore)) {
            throw new Raautil_TransactionException("Raautil_DataStore is no instance of Raautil_DataStore");
        }
        
        $key = $this->normalizeKey($key);
        $Raautil_DataStoreExisting = $this->readDatastore($key);
        if(isset($Raautil_DataStoreExisting)) {
            throw new Raautil_TransactionException("key is already existing");
        }
        
        $Raautil_DataStore->setTimestampCreated(time());
        $Raautil_DataStore->setTimestampChanged(time());
        $serializedString = $Raautil_DataStore->serializeDataStore();
        if(!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = array();
        }
        $bWritten = FALSE;
        if(isset($_SESSION[self::SESSION_KEY])) {
            if(is_array($_SESSION[self::SESSION_KEY])) {
                $_SESSION[self::SESSION_KEY][$key] = $serializedString;
                $bWritten = TRUE;
            }
        }
        if($bWritten == FALSE) {
            throw new Raautil_TransactionException("Error inserting");
        }
    } // End insertDatastore
    
    public function updateDatastore($key = '', $Raautil_DataStore = null) {
        $key = isset($key) ? (is_string($key) ? trim($key) : '') : '';
        if($key == '') {
            throw new Raautil_TransactionException("key is empty");
        }
        
        $this->validateDatastore();
        
        if(!isset($Raautil_DataStore)) {
            throw new Raautil_TransactionException("Raautil_DataStore is null");
        }
        else if(!is_object($Raautil_DataStore)) {
            throw new Raautil_TransactionException("Raautil_DataStore is no object");
        }
        else if(!($Raautil_DataStore instanceof Raautil_DataStore)) {
            throw new Raautil_TransactionException("Raautil_DataStore is no instance of Raautil_DataStore");
        }
        
        $key = $this->normalizeKey($key);
        $Raautil_DataStoreExisting = $this->readDatastore($key);
        if(!isset($Raautil_DataStoreExisting)) {
            throw new Raautil_TransactionException("key is not existing");
        }
        
        $Raautil_DataStore->setTimestampChanged(time());
        $serializedString = $Raautil_DataStore->serializeDataStore();
        if(!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = array();
        }
        $bWritten = FALSE;
        if(isset($_SESSION[self::SESSION_KEY])) {
            if(is_array($_SESSION[self::SESSION_KEY])) {
                $_SESSION[self::SESSION_KEY][$key] = $serializedString;
                $bWritten = TRUE;
            }
        }
        if($bWritten == FALSE) {
            throw new Raautil_TransactionException("Error updating");
        }
    } // End updateDatastore
    
    public function deleteDatastore($key = '') {
        $key = isset($key) ? (is_string($key) ? trim($key) : '') : '';
        if($key == '') {
            throw new Raautil_TransactionException("key is empty");
        }
        
        $this->validateDatastore();
        
        $key = $this->normalizeKey($key);
        $Raautil_DataStoreExisting = $this->readDatastore($key);
        if(!isset($Raautil_DataStoreExisting)) {
            throw new Raautil_TransactionException("key is not existing");
        }
        if(!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = array();
        }
        $bDeleted = FALSE;
        if(isset($_SESSION[self::SESSION_KEY])) {
            if(is_array($_SESSION[self::SESSION_KEY])) {
                unset($_SESSION[self::SESSION_KEY][$key]);
                $bDeleted = TRUE;
            }
        }
        if($bDeleted == FALSE) {
            throw new Raautil_TransactionException("Error deleting");
        }
    } // End deleteDatastore
    
    public function readAllDatastores() {
        $resultArray = array();
        try {
            $this->validateDatastore();
        } catch(Raautil_TransactionException $ex) {
            return $resultArray;
        }
        
        if(isset($_SESSION[self::SESSION_KEY])) {
            if(is_array($_SESSION[self::SESSION_KEY])) {
                foreach($_SESSION[self::SESSION_KEY] as $key => $serStr) {
                    $Raautil_DataStore = Raautil_DataStore::deserializeDataStore($serStr);
                    if(isset($Raautil_DataStore)) {
                        $entry = array(
                            'key'   => $key,
                            'data'  => $Raautil_DataStore,
                        );
                        $resultArray[] = $entry;
                    }
                }
            }
        }
        return $resultArray;
    } // End readAllDatastores
    
} // End class Raautil_DataStoreProviderSession

